## Kxxx-qiniu Wordpress Plugin

### Description

七牛云存储的WordPress扩展，实现简单的链接转换功能，本地链接自动转为CDN链接。<br>
可选是否抓取远程图片到本地，当然了，如果不打算使用cdn功能，也可单独使用远程远程图片的功能。

### Installation

1. 下载此仓库文件，上传到 `/wp-content/plugins/plugin-name` 下
2. 在WordPress后台激活插件
3. 把在七牛云平台申请的加速域名配置到选项

### ChangeLog

==1.1-dev

1.修复抓取本地域名下的图片的bug
2.添加远程图片白名单功能，可添加排除不抓取的远程图片地址

==1.0

第一版暂时只实现图片url替换功能，本地图片直接转为云存储图片，非本地图片链接，在编辑文章保存时会自动抓取远程图片到本地，
并且转换远程链接地址为本地图片链接地址，当前台页面展示时自动转为云存储链接


Donate : 

![donate](http://images.kelenews.com/jerry/tool/Wxpay.png-w2h2 "Jerry-Wxpay")  ![donate](http://images.kelenews.com/jerry/tool/Alipay.png-w2h2 "Jerry-Alipay")



