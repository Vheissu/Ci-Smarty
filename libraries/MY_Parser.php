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
 * @version   2.0
 */

class MY_Parser extends CI_Parser {

    protected $CI;
    
    protected $_module = '';
    protected $_template_locations = array();

    // The name of the theme in use
    protected $_theme_name = '';
    
    public function __construct()
    {
        // Codeigniter instance and other required libraries/files
        $this->CI =& get_instance();
        $this->CI->load->library('smarty');
        
        // Detect if we have a current module
        $this->_module = $this->current_module();

        // What controllers or methods are in use
        $this->_controller  = $this->CI->router->fetch_class();
        $this->_method      = $this->CI->router->fetch_method();

        // If we don't have a theme name stored
        if ($this->_theme_name == '')
        {
            $this->set_theme(config_item('theme_name'));
        }

        // Store a whole heap of template locations
        $this->_template_locations = array( 
            config_item('theme_path') . $this->_theme_name . '/views/modules/' . $this->_module .'/layouts/',
            config_item('theme_path') . $this->_theme_name . '/views/layouts/',
            config_item('theme_path') . $this->_theme_name . '/views/modules/' . $this->_module .'/',
            config_item('theme_path') . $this->_theme_name . '/views/',
            APPPATH . 'modules/' . $this->_module . '/views/layouts/',
            APPPATH . 'modules/' . $this->_module . '/views/',
            APPPATH . 'views/layouts/',
            APPPATH . 'views/'
        );

        // Will add paths into Smarty for "smarter" inheritance and inclusion
        $this->_add_paths();
    }

    /**
     * Set Theme
     *
     * Set the theme to use
     * 
     * @return string
     */
    public function set_theme($name)
    {
        // Store the theme name
        $this->_theme_name = trim($name);

        // Our themes can have a functions.php file just like Wordpress
        $functions_file = config_item('theme_path') . $this->_theme_name . '/functions.php';

        // If we have a functions file, include it
        if (file_exists($functions_file))
        {
            include_once($functions_file);
        }

    }

    /**
     * Get Theme
     *
     * Does what the function name implies: gets the name of
     * the currently in use theme.
     *
     * @return string
     */
    public function get_theme()
    {
        return (isset($this->_theme_name)) ? $this->_theme_name : '';
    }

    /**
    * Current Module
    *
    * Just a fancier way of getting the current module
    * if we have support for modules
    *
    * @return string
    */
    public function current_module()
    {
        // Modular Separation / Modular Extensions has been detected
        if (method_exists( $this->CI->router, 'fetch_module' ))
        {
            $module = $this->CI->router->fetch_module(); 
            return (!empty($module)) ? $module : '';
        }
        else
        {
            return '';
        }
    }
    
    /**
    * Parse
    *
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
        // If we don't want caching, disable it
        if ($caching === FALSE)
        {
            $this->CI->smarty->disable_caching();
        }
        
        // If no file extension dot has been found default to defined extension for view extensions
        if ( ! stripos($template, '.')) 
        {
            $template = $template.".".$this->CI->smarty->template_ext;
        }

        // Get the location of our view, where the hell is it?
        $template = $this->_find_view($template);
        
        // If we have variables to assign, lets assign them
        if ( ! empty($data))
        {
            foreach ($data AS $key => $val)
            {
                $this->CI->smarty->assign($key, $val);
            }
        }
        
        // Load our template into our string for judgement
        $template_string = $this->CI->smarty->fetch($template);
        
        // If we're returning the templates contents, we're displaying the template
        if ($return === FALSE)
        {
            $this->CI->output->append_output($template_string);
        }
        
        // We're returning the contents, fo' shizzle
        return $template_string;
    }

    /**
    * Find View
    *
    * Searches through module and view folders looking for your view, sir.
    *
    * @access protected
    * @param string $file - The view to search for
    * @return string The path and file found
    */
    protected function _find_view($file)
    {
        // We have no path by default
        $path = NULL;

        // Iterate over our saved locations and find the file
        foreach($this->_template_locations AS $location)
        {
            if (file_exists($location.$file) && $path == NULL)
            {
                // Store the file to load
                $path = $location.$file;

                // Stop the loop, we found our file
                break;
            }
        }

        // Return the path
        return $path;
    }

    /**
    * Add Paths
    *
    * Traverses all added template locations and adds them
    * to Smarty so we can extend and include view files
    * correctly from a slew of different locations including
    * modules if we support them.
    *
    * @access protected
    */
    protected function _add_paths()
    {
        // Iterate over our saved locations and find the file
        foreach($this->_template_locations AS $location)
        {
            $this->CI->smarty->addTemplateDir($location);
        }    
    }
    
    /**
    * String Parse
    *
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
    *
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
