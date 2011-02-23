# CI Smarty

So you want to integrate Smarty into your Codeigniter applications because you're unhappy with how the in-built parser works? Meet CI Smarty. You don't really need to edit any files or do anything to use it.

Codeigniter's parser library is a bit limited and extremely basic, Smarty fills the gap with things like template inheritance and other cool shit to make your views look neater.

## It works with Phil Sturgeon's Template Library

If you've used Phil's template library before (http://philsturgeon.co.uk/code/codeigniter-template) you would know it gives you theming capabilities but it doesn't really work too well with view and parser extensions unless of course you're using Phil's Dwoo library, CI Smarty works with Phil's template library so you can have themes and view files using Smarty syntax which look a lot nicer.

## How to use it?

Drop the contents of the download zip into your application directory, then edit your autoload.php file in the config folder, and add parser to the list of autoloaded libraries. Instead of using $this->load->view() you now use $this->parser->parse() instead. That's it.