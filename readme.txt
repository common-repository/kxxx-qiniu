=== 远程抓取图片CDN加速插件 ===
Contributors: JerryZhang
Donate link: http://images.kelenews.com/jerry/tool/Wxpay.jpg-w2h2
Tags: CDN, 七牛, qiniu, 远程抓取图片
Requires at least: 4.5
Tested up to: 4.7
Stable tag: 1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

一款抓取远程图片并对接七牛cdn的扩展

== Description ==

七牛云存储的WordPress扩展，实现简单的链接转换功能，本地链接自动转为CDN链接。<br>
可选是否抓取远程图片到本地，当然了，如果不打算使用cdn功能，也可单独使用远程远程图片的功能。
1.编辑保存文字时将会把远程图片（非本网站域名下的图片）抓取保存到本地，并且把文章中的远程图片链接转换为本地链接
2.前台展示内容时会自动把本地图片地址转换为cdn链接地址

== Installation ==

1. 下载此仓库文件，上传到 `/wp-content/plugins/plugin-name` 下
2. 在WordPress后台激活插件
3. 把在七牛云平台申请的加速域名配置到选项
4. 可选择是否需要抓取远程图片到本地服务器
5. 可配置白名单，排除不需要抓取的远程图片

== Screenshots ==

1. 七牛云存储 扩展插件

== Changelog ==

= 1.1 =
1.修复抓取本地域名下的图片的bug
2.添加远程图片白名单功能，可添加排除不抓取的远程图片地址

= 1.0 =
* 第一版暂时只实现最简单的链接转换和抓取远程图片的功能，接下来逐步完善

== Arbitrary section ==

待实现功能：
1.如有需求请发邮件联系，收到后会第一时间回复

 == Upgrade Notice ==

刚开始学习写plugin，自测通过没有问题，不过不能保证完全没有bug，有问题或者新的需求请不吝赐教，email：zjk6700@mail.com
