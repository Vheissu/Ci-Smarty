# CI Smarty

CI Smarty integrates Smarty into your Codeigniter applications extending the basic and very limited in-built parser library that comes with the framework. No configuration is really required (unless you want to change something), it works out of the box after dropping the files in and loading them. The in-built Codeigniter parsing library sucks, CI Smarty takes the power and ease of the Smarty templating language and adds easy drop-in support for Codeigniter and even supports HMVC.

## How to use it?

Drop the contents of the download zip into your application directory, then edit your autoload.php file in the config folder, and add 'parser' to your list of autoloaded libraries. Instead of using $this->load->view() you now use $this->parser->parse() instead. That's it.

## HMVC Modular Extensions compatible

I've spent a good chunk of time making CI Smarty support HMVC. The library will check for a file inside of your modulename/views folder and if a template doesn't exist, it will just check your standard application/views folder instead. Module views always override application views. 

### Special note for HMVC Modular Extensions Users

Modular Extensions comes with a application/core/MY_Loader which is vital to the operation of the library. CI Smarty comes with a MY_Loader of it's own, so do not copy the loader from this package into your app, instead copy the public $_ci_cached_vars declaration and paste it into your current MY_Loader. 

## Issues

If you run into any issues, especially the Codeigniter blank screen of death issue there is a simple fix. CI Smarty creates new cache folders in your application/cache directory and needs them to be writable. Issues I've encountered are generally the folders aren't owned by correct user and group doing a: chown www-data:www-data (at least on Ubuntu) on your caching folder will fix the issue.