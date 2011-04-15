# CI Smarty

CI Smarty integrates Smarty into your Codeigniter applications extending the basic and very limited in-built parser library that comes with the framework. No configuration is really required (unless you want to change something), it works out of the box after dropping the files in and loading them.

## How to use it?

Drop the contents of the download zip into your application directory, then edit your autoload.php file in the config folder, and add 'parser' to your list of autoloaded libraries. Instead of using $this->load->view() you now use $this->parser->parse() instead. That's it.

CI Smarty is compatible with most third party Codeigniter template libraries and has been tested with Phil Sturgeon's template library and works. Any library should work that uses the Smarty parser as it simply extends the stock-standard parser library.