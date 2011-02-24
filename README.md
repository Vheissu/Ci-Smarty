# CI Smarty

So you want to integrate Smarty into your Codeigniter applications because you're unhappy with how the in-built parser works? Meet CI Smarty. You don't really need to edit any files or do anything to use it.

Codeigniter's parser library is a bit limited and extremely basic, Smarty fills the gap with things like template inheritance and other cool shit to make your views look neater.

## How to use it?

Drop the contents of the download zip into your application directory, then edit your autoload.php file in the config folder, and add parser to the list of autoloaded libraries. Instead of using $this->load->view() you now use $this->parser->parse() instead. That's it.