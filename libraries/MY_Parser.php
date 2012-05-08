<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CI Smarty
 *
 * Smarty templating for Codeigniter
 *
 * @package   CI Smarty
 * @author    Dwayne Charrington
 * @copyright Copyright (c) 2012 Dwayne Charrington and Github contributors
 * @link      http://ilikekillnerds.com
 * @license   http://www.apache.org/licenses/LICENSE-2.0.html
 * @version   1.2
 */

class MY_Parser extends CI_Parser {

    protected $CI;
    
    protected $_module = '';
    protected $_controller = '';
    protected $_method = '';
    
    public function __construct()
    {
        parent::__construct();

        $this->CI =& get_instance();
        $this->CI->load->library('smarty');

        // Modular Separation / Modular Extensions has been detected
        if (method_exists( $this->CI->router, 'fetch_module' ))
        {
            $this->_module  = $this->CI->router->fetch_module();
        }
    }
    
    /**
    * Parse
    * Parses a template using Smarty 3 engine
    * 
    * @param string $template
    * @param array $data
    * @param boolean $return
    * @param mixed $caching
    * @return string
    */
    public function parse($template, $data = array(), $return = FALSE, $caching = TRUE)
    {
        // Make sure we have a template, yo.
        if (empty($template))
        {
            return FALSE;
        }
        
        // If we don't want caching, disable it
        if ($caching === FALSE)
        {
            $this->CI->smarty->disable_caching();
        }
        
        // If no file extension dot has been found default to defined extension for view extensions
        if ( ! stripos($template, '.') ) 
        {
            $template = $template.".".$this->CI->smarty->template_ext;
        }

        // Get the location of our view, where the hell is it?
        $template = $this->_find_view($template);

        // Merge in cached variables
        $data = array_merge($data, $this->CI->load->_ci_cached_vars);
        
        // If we have variables to assign, lets assign them
        if (!empty($data))
        {
            foreach ($data as $key => $val)
            {
                $this->CI->smarty->assign($key, $val);
            }
        }
        
        // Get our template data as a string
        $template_string = $this->CI->smarty->fetch($template);
        
        // If we're returning the templates contents, we're displaying the template
        if ($return == FALSE)
        {
            $this->CI->output->append_output($template_string);
        }
        
        // We're returning the contents, fo' shizzle
        return $template_string;
    }

    /**
    * Find View
    * Searches through module and view folders looking for your view, sir.
    *
    * @access protected
    * @param string $file - The view to search for
    * @return string The path and file found
    *
    */
    protected function _find_view($file)
    {
        // Ye ol' faithful views folder
        $view_folder = APPPATH.'views/';

        // The location of our HMVC modules
        $modules_folder = APPPATH.'modules/';

        // Final path
        $final_path = FALSE;

        // Make sure we have a module
        if ($this->_module !== '')
        {
            // The module we're currently in, look in the views folder
            $the_module = $modules_folder.$this->_module."/views/";

            // Is the module name in the path?
            $has_module_in_path = FALSE;

            // Has the file got the modulename/ in it? Means we could be looking for a module view
            if (stripos($file, $this->_module."/") !== FALSE)
            {
                // We could potentially have a module in the path name
                $has_module_in_path = TRUE;
            }

            // Better tell the file_exists function we need to kind of strip that out
            if ($has_module_in_path == TRUE)
            {
                // Our new file minus the module name if there is one
                $file = str_replace($this->_module."/", "", $file);
            }

            // Look in the modules folder first, if the file is found we use it
            if (file_exists($the_module.$file))
            {
                // Set our final path to be that of the module and view file
                $final_path = $the_module.$file;
            }
            // Look in the application/views folder for the file
            elseif (file_exists($view_folder.$file))
            {
                // Not found in a module views directory, maybe just in views?
                $final_path = $view_folder.$file;
            }
            // Fuck, the view hasn't been found anywhere!
            else
            {
                // There is no PATH!
                $final_path = FALSE;
            }

            // Return the final path
            return $final_path;
        }
        else
        {
            // No module, then just return the file in the normal views folder
            return $view_folder.$file;
        }
    }
    
    /**
    * String Parse
    * Parses a string using Smarty 3
    * 
    * @param string $template
    * @param array $data
    * @param boolean $return
    * @param mixed $is_include
    */
    public function string_parse($template, $data = array(), $return = FALSE, $is_include = FALSE)
    {
        return $this->CI->smarty->fetch('string:'.$template, $data);
    }
    
    /**
    * Parse String
    * Parses a string using Smarty 3. Never understood why there
    * was two identical functions in Codeigniter that did the same.
    * 
    * @param string $template
    * @param array $data
    * @param boolean $return
    * @param mixed $is_include
    */
    public function parse_string($template, $data = array(), $return = FALSE, $is_include = false)
    {
        return $this->string_parse($template, $data, $return, $is_include);
    }

}
