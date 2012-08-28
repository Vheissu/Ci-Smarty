# CI Smarty

CI Smarty integrates Smarty into your Codeigniter applications extending the basic and very limited in-built parser library that comes with the framework. No configuration is really required (unless you want to change something), it works out of the box after dropping the files in and loading them. The in-built Codeigniter parsing library sucks, CI Smarty takes the power and ease of the Smarty templating language and adds easy drop-in support for Codeigniter and even supports HMVC.

## In-built theming and Asset Management

CI Smarty comes with complimentary functionality to add theming support in your Codeigniter applications. Simply create a themes directory in the root folder of your app and then inside of that folders of themes. If you're not using themes, then don't add anything and CI Smarty will work fine without them. It's a good idea if you're building a web app to have a default theme in application/themes/themename and then allow themes in a different directory to override your default theme files.

### Asset Management

When dealing with themes you want to include static content like images and stylesheets as simply as possible. By calling; {css('file.css')}, {js('file.js')}, {img('file.jpg')}, etc in your themes files will automatically be embedded. It is assumed your files are in the directories; themename/css, themename/js and themename/images. To get a web friendly URL to your themes directory simply use: {theme_url()} which also supports adding file and folders.

## How to use it?

Drop the contents of the download zip into your application directory, then edit your autoload.php file in the config folder, and add 'parser' to your list of autoloaded libraries. Instead of using $this->load->view() you now use $this->parser->parse() instead. That's it.

## HMVC Modular Extensions compatible

I've spent a good chunk of time making CI Smarty support HMVC. The library will check for a file inside of your modulename/views folder and if a template doesn't exist, it will just check your standard application/views folder instead. Module views always override application views. 

## View loading priority

When loading view files due to the way Smarty requires paths to themes set, themes are searched in a particular order which can be changed in MY_parser.php very easily, but should never be changed.

## Issues

If you run into any issues, especially the Codeigniter blank screen of death issue there is a simple fix. CI Smarty creates new cache folders in your application/cache directory and needs them to be writable. Issues I've encountered are generally the folders aren't owned by correct user and group doing a: chown www-data:www-data (at least on Ubuntu) on your caching folder will fix the issue.