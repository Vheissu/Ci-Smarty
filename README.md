# CI Smarty
CodeIgniter 3 & Smarty 3整合框架

## 怎么使用?
查看Demo中的Controller和View可以快速入门，加载`parser`至Controller中，然后用`$this->parser->parse()`替代`$this->load->view()`即可使用Smarty. Then Enjoy it ~

Drop the contents of the download zip into your application directory, then edit your autoload.php file in the config folder, and add 'parser' to your list of autoloaded libraries. Instead of using $this->load->view() you now use $this->parser->parse() instead. That's it.

## Codeigniter支持的版本
CI Smarty已经过测试并且以构建与最新的3.0版本。它极有可能兼容Codeigniter 2.x，不过你最好使用3.0。

CI Smarty has been tested and working on the latest version of Codeigniter (3.0). It will most likely work with older 2.x versions as well, but these aren't supported anymore and you should be using 3.0.

## 支持主题
CI Smarty支持主题功能，你可以创建一个主题文件`theme`到项目中,然后再进入`config`,修改`smarty.php`中的`$config['smarty.theme_name']`.在`view`中使用`<{theme_url()}>`即可载入主题.

CI Smarty comes with complimentary functionality to add theming support in your Codeigniter applications. Simply create a themes directory in the root folder of your app and then inside of that folders of themes. If you're not using themes, then don't add anything and CI Smarty will work fine without them. It's a good idea if you're building a web app to have a default theme in application/themes/themename and then allow themes in a different directory to override your default theme files.

### Asset management
在`view`中引入静态文件最快捷的方式如下:
* `{css('file.css')}`
* `{js('file.js')}`
* `{image('file.jpg')}`

我发现`image`并不足以满足项目需求，然后我对`image`方法进行了增强，使用方法如下：

`<{image('logo.png',['class' => 'img-circle img-responsive', 'style' => 'height:25px;'])}>`

When dealing with themes you want to include static content like images and stylesheets. By calling; {css('file.css')}, {js('file.js')}, {image('file.jpg')}, etc in your themes files will automatically be embedded. It is assumed your files are in the directories; themename/css, themename/js and themename/img. To get a web friendly URL to your themes directory simply use: {theme_url()} which also supports adding file and folders. !! The contents of the above the original wrong, now has been modified!!


## HMVC/Modules
这个库可以与各种HMVC解决工作（包括模块化扩展）。然而，并不保证CI Smarty支持所有第三方库。

This library should work with various HMVC solutions (including Modular Extensions). However, no guarantee is given that CI Smarty will work with other third party libraries.

## Issues
如果项目搭建遇到什么问题，请先检查是否是缓存的原因（如果服务器是linux内核，请注意目录权限），若有使用问题，欢迎来我的博客留言`blog.66tools.com`.

If you run into any issues, especially the Codeigniter blank screen of death issue there is a simple fix. CI Smarty creates new cache folders in your application/cache directory and needs them to be writable. Issues I've encountered are generally the folders aren't owned by correct user and group doing a: chown www-data:www-data (at least on Ubuntu) on your caching folder will fix the issue.

## 更新记录
* 2015/11/29  : 整合Vimeo API至框架中，并提供Demo
* 2015/11/27  : 整合Paypal支付至框架中，并提供Demo
* 2015/11/26  : 整合PHPMailer至框架中(Ci自带邮件组件没有PHPMailer给力)
* 更早以前    : 框架已整合PHPMailer、Paypal、Vimeo、阿里云OSS等,后续将陆续整理上传
