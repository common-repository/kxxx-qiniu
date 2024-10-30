<?php
/*
Plugin Name: kxxx-qiniu
Plugin URI: https://github.com/bugZhang/kxxx-qiniu
Description: 体积最小的cdn插件，实现最简单的功能。远程抓取和CDN可单独使用。『 1.抓取远程图片到本地。 2.转换本地图片链接为cdn链接，实现cdn加速 』如有问题，联系：zjk6700@gmail.com
Version: 1.1
Author: Jerry Zhang
Author URI: https://github.com/bugZhang
*/
?>
<?php
require_once 'admin-options.php';
require_once 'kxxx-utils.php';

add_action('wp_loaded', 'kxxx_qiniu_start');

function kxxx_qiniu_start()
{
    $utils = new \Kxxx\Admin\KxxxUtils();
    add_filter('the_content', array($utils, 'kxxx_get_cdn_content'), 10);
    add_filter('wp_insert_post_data', array($utils, 'kxxx_save_post_action'), 10, 2);
}


