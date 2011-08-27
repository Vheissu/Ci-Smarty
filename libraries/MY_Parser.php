<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* @name CI Smarty
* @copyright Dwayne Charrington, 2011.
* @author Dwayne Charrington and other Github contributors
* @license (DWYWALAYAM) 
           Do What You Want As Long As You Attribute Me Licence
* @version 1.2
* @link http://ilikekillnerds.com
*/

class MY_Parser extends CI_Loader {

    protected $CI;
    protected $theme_location;
    
    public function __construct()
    {
        $this->CI = get_instance();
        $this->CI->load->library('smarty');   
    }
    
    /**
    * Parse
    * Parses a template using Smarty 3 engine
    * 
    * @param string $template
    * @param array $data
    * @param boolean $return
    * @param mixed $caching
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
        if ( !stripos($template, '.') ) 
        {
            $template = $template.".".$this->CI->smarty->template_ext;
        }
        
        //merge the data array with global cached vars
        
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
    * String Parse
    * Parses a string using Smarty 3
    * 
    * @param string $template
    * @param array $data
    * @param boolean $return
    * @param mixed $is_include
    */
    function string_parse($template, $data = array(), $return = FALSE, $is_include = FALSE)
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
    function parse_string($template, $data = array(), $return = FALSE, $is_include = false)
    {
        return $this->string_parse($template, $data, $return, $is_include);
    }

}
