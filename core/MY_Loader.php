<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Loader extends CI_Loader
{
    
    /**
    * Loads a view file and uses Smarty to parse its contents
    * This function overrides the view function from the parent
    * class CI_Loader.
    * 
    * @param mixed $view
    * @param mixed $vars
    * @param mixed $return
    */
    function view($view, $vars = array(), $return = FALSE)
    {
        $CI = & get_instance();                 // Get instance of the Codeigniter object
        $CI->load->library('smarty');          // Load our Smarty parser library
        $CI->smarty->_assign_variables($vars); // Assign variables to Smarty. 

        // If no file extension dot has been found default to .php for view extensions
        if ( !stripos($view, '.') ) 
        {
            $view = $view.".php";
        }

        if($return === TRUE)
        {
            return $CI->smarty->fetch($view);
        }
        else
        {
            return $CI->smarty->display($view);
        }
    }
    
}