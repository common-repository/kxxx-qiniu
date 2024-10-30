<?php

namespace Kxxx\Admin;

class KxxxUtils
{
    private $kxxxOptions = array();

    private $mac_chrome_user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36';

    public function getKxxxOptions(){
        if(empty($this->kxxxOptions)){
            $this->kxxxOptions = get_option('kxxx_qiniu_option');
        }
        return $this->kxxxOptions;
    }

    /**
     * @param $sourceStr   图片链接
     * @param $cdnDomain    cdn域名
     * @return mixed|string
     */
    public function kxxx_convert_url_to_cdn($sourceStr, $cdnDomain)
    {
        if (!$sourceStr) {
            return '';
        }

        return str_replace(get_home_url(), $cdnDomain, $sourceStr);
    }

    public function kxxx_get_cdn_content($content)
    {
        $cdn_content = preg_replace_callback('|<img.*?src=[\'"](.*?)[\'"].*?>|i', array($this, 'kxxx_local_images_handler'), $content);
        return $cdn_content;
    }

    public function kxxx_local_images_handler($matches)
    {

        $img_url = trim($matches[1]);
        if (empty($img_url)) return;
        $options = $this->getKxxxOptions();

        if (!$this->compareWhiteListUrls($matches[1]) && $options && $options['host']) {
            return $this->kxxx_convert_url_to_cdn($matches[0], $options['host']);
        } else {
            return $matches[0];
        }

    }


    /**
     *
     * 后台更新文章时，处理远程图片（非本站图片）<br/>
     * @param $postId
     * @param $post
     * @return mixed|string
     */
    public function kxxx_save_post_action($post, $arr)
    {

        $options = $this->getKxxxOptions();
        if (!$options || empty($options['fetch'])) {
            return $post;
        }
        $content = $post['post_content'];
        if (!$content) {
            return $post;
        }

        $post['post_content'] = preg_replace_callback('|<img.*?src=\\\[\'"](.*?)\\\[\'"].*?>|i', array($this, 'kxxx_remote_images_handler'), $content);

        return $post;
    }

    /**
     * 将远程图片下载到本地，替换远程图片链接为cdn链接，回源至本地，防止远程图片失效。
     * @param $matches
     * @return mixed|string|void
     */
    public function kxxx_remote_images_handler($matches)
    {

        $img_url = trim($matches[1]);

        if (empty($img_url)) return '';

        if (stripos($img_url, get_home_url()) !== false) {
            return $matches[0];
        }
        if($this->compareWhiteListUrls($img_url)){
            return $matches[0];
        }

        $result = $this->kxxx_fetch_remote_image($img_url, array('method' => 'FILE'));
        if ($result && !empty($result['headers']['content-type'])) {

            $img_type = strtolower($result['headers']['content-type']);

            switch (true) {
                case stripos($img_type, 'image/png') !== false:
                    $ext = 'png';
                    break;
                case stripos($img_type, 'image/gif') !== false:
                    $ext = 'gif';
                    break;
                default:
                    $ext = 'jpg';
            }

            $upload_dir = wp_upload_dir();
            $file_name = md5($img_url) . '.' . $ext;
            $file_path = $upload_dir['path'] . DIRECTORY_SEPARATOR . $file_name;
            $file_url = $upload_dir['url'] . DIRECTORY_SEPARATOR . $file_name;
            $this->kxxx_save_file($result['body'], $file_path);
            return str_replace($img_url, $file_url, $matches[0]);
        } else {
            return '';
        }
    }

    public function kxxx_fetch_remote_image($url, $args = array(), $err_args = array())
    {

        if (!$url) {
            return 0;
        }
        $url = html_entity_decode($url);
        $args = wp_parse_args($args, array(
            'timeout' => 100,
            'method' => '',
            'body' => array(),
            'sslverify' => false,
            'blocking' => true,    // 如果不需要立刻知道结果，可以设置为 false
            'stream' => false,    // 如果是保存远程的文件，这里需要设置为 true
            'filename' => null,    // 设置保存下来文件的路径和名字
            'need_json_decode' => true,
            // 'headers'		=> array('Accept-Encoding'=>'gzip;'),	//使用压缩传输数据
            // 'compress'		=> false,
            'decompress' => true,
        ));

        $method = ($args['method']) ? strtoupper($args['method']) : ($args['body'] ? 'POST' : 'GET');

        unset($args['need_json_decode']);
        unset($args['method']);

        if ($method == 'GET') {
            $response = wp_remote_get($url, $args);
        } elseif ($method == 'POST') {
            $response = wp_remote_post($url, $args);
        } elseif ($method == 'FILE') {    // 上传文件
            $args['method'] = ($args['body']) ? 'POST' : 'GET';
            $args['sslcertificates'] = isset($args['sslcertificates']) ? $args['sslcertificates'] : ABSPATH . WPINC . '/certificates/ca-bundle.crt';
            $args['user-agent'] = isset($args['user-agent']) ? $args['user-agent'] : $this->mac_chrome_user_agent;
            $wp_http_curl = new \WP_Http_Curl();
            $response = $wp_http_curl->request($url, $args);
        }

        if (is_wp_error($response)) {
            trigger_error($url . "\n" . $response->get_error_code() . ' : ' . $response->get_error_message() . "\n" . var_export($args['body'], true));
            return $response;
        }
        return $response;

    }

    public function kxxx_save_file($file_content, $path)
    {
        $file = @fopen($path, 'a');
        fwrite($file, $file_content);
        fclose($file);
    }

    /**
     * 检查远程图片与白名单中的图片地址对比
     * @param $remoteImgUrl 远程图片地址
     */
    public function compareWhiteListUrls($remoteImgUrl){
        $options = $this->getKxxxOptions();
        if (!$remoteImgUrl || !$options || empty($options['whitelist'])) {
            return 0;
        }
        $params = parse_url($remoteImgUrl);
        $remoteHost = $params['host'];

        $whitelist = explode('|', $options['whitelist']);
        foreach ($whitelist as $domain){
            $domain = str_ireplace('http://', '', $domain);
            $domain = str_ireplace('https://', '', $domain);
            if(strripos($remoteHost, $domain) !== false){
                return 1;
            }
        }
        return 0;
    }
}