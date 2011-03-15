<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Parser extends CI_Parser {

    protected $CI;
    protected $theme_location;
    
    public function __construct()
    {
        $this->CI = get_instance();
        $this->load->library('smarty');   
    }
    
    /**
    * This function lets us access Codeigniter instance objects like;
    * helpers, libraries and core functions without having to prefix
    * our faux Codeigniter instance variable 'CI' we can load Codeigniter
    * libraries and other goodness like we would normally within controllers
    * and other things.
    * 
    * @param mixed $bleh
    */
    public function __get($bleh)
    {
        return $this->CI->$bleh;
    }
    
    /**
    * Parse a template using Smarty. Hows this for a Codeigniter
    * core extension? Nice and simple.
    * 
    * @param mixed $template
    * @param array $data
    * @param mixed $return
    */
    public function parse($template, $data = '', $return = FALSE, $use_theme = FALSE)
    {
        // Make sure we have a template, yo.
        if ($template == '')
        {
            return FALSE;
        }
        
        // If we want to get a certain template from another location
        if ($use_theme != FALSE)
        {
            $this->load->library('template');
            $template = "file:/".$this->template->get_theme_path().$template."";
        }
        
        // If no file extension dot has been found default to .tpl for view extensions
        if ( !stripos($template, '.') ) 
        {
            $template = $template.".tpl";
        }
        
        // Merge in any cached variables with our supplied variables
        $data = array_merge($data, $this->load->_ci_cached_vars);
        
        // If we have variables to assign, lets assign them
        if ($data)
        {
            foreach ($data as $key => $val)
            {
                $this->smarty->assign($key, $val);
            }
        }
        
        // Get our template data as a string
        $template_string = $this->smarty->fetch($template);
        
        // If we're returning the templates contents, we're displaying the template
        if ($return == FALSE)
        {
            $this->output->append_output($template_string);
        }
        
        // We're returning the contents, fo'' shizzle
        return $template_string;
    }
    
    public function parse_string($template, $data = '', $return = FALSE, $use_theme = FALSE)
    {
        return $this->parse($template, $data, $return, $use_theme);
    }

}