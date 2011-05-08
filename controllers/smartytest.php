<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* @name CI Smarty
* @copyright Dwayne Charrington, 2011.
* @author Dwayne Charrington and other Github contributors
* @license (DWYWALAYAM) 
           Do What You Want As Long As You Attribute Me Licence
* @version 1.2
* @link http://ilikekillnerds.com
*/

class Smartytest extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Ideally you would autoload the parser
        $this->load->library('parser');
    }

    public function index()
    {    
        // Some example data
        $data['title'] = "The Smarty parser works!";
        $data['body']  = "This is body text to show that the Smarty Parser works!";
        
        // Load the template from the views directory
        $this->parser->parse("smartytest.tpl", $data);
    }
    
    /**
     * Showing off Smarty 3 template inheritance features
     *
     */
    public function inheritance()
    {
        // Some example data
        $data['title'] = "The Smarty parser works with template inheritance!";
        $data['body']  = "This is body text to show that Smarty 3 template inheritance works with Smarty Parser.";
        
        // Load the template from the views directory
        $this->parser->parse("inheritancetest.tpl", $data);
        
    }

}