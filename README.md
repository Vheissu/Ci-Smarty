# CI Smarty
Integrate Smarty into your Codeigniter applications. 

## How to use it?
Drop the contents of the download zip into your application directory, then edit your autoload.php file in the config folder, and add 'parser' to your list of autoloaded libraries. Instead of using $this->load->view() you now use $this->parser->parse() instead. That's it.

## Supported versions of Codeigniter
CI Smarty has been tested and working on the latest version of Codeigniter (3.0). It will most likely work with older 2.x versions as well, but these aren't supported anymore and you should be using 3.0.

## Theming support
CI Smarty comes with complimentary functionality to add theming support in your Codeigniter applications. Simply create a themes directory in the root folder of your app and then inside of that folders of themes. If you're not using themes, then don't add anything and CI Smarty will work fine without them. It's a good idea if you're building a web app to have a default theme in application/themes/themename and then allow themes in a different directory to override your default theme files.

### Asset management
When dealing with themes you want to include static content like images and stylesheets. By calling; {css('file.css')}, {js('file.js')}, {img('file.jpg')}, etc in your themes files will automatically be embedded. It is assumed your files are in the directories; themename/css, themename/js and themename/images. To get a web friendly URL to your themes directory simply use: {theme_url()} which also supports adding file and folders.

## HMVC/Modules
This library should work with various HMVC solutions (including Modular Extensions). However, no guarantee is given that CI Smarty will work with other third party libraries.

## Issues
If you run into any issues, especially the Codeigniter blank screen of death issue there is a simple fix. CI Smarty creates new cache folders in your application/cache directory and needs them to be writable. Issues I've encountered are generally the folders aren't owned by correct user and group doing a: chown www-data:www-data (at least on Ubuntu) on your caching folder will fix the issue.