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
    public function parse($template, $data = '', $return = false, $use_theme = false)
    {
        if ($template == '')
        {
            return FALSE;
        }
        
        if ($use_theme !== false)
        {
            $this->load->library('template');
            $template = "file:/".$this->template->get_theme_path().$template."";
        }
        
        if ( !stripos($template, '.') ) 
        {
            $template = $template.".tpl";
        }
        
        $data = array_merge($data, $this->load->_ci_cached_vars);
        
        if ($data)
        {
            foreach ($data as $key => $val)
            {
                $this->smarty->assign($key, $val);
            }
        }
        
        $template_string = $this->smarty->fetch($template);
        
        if ($return == FALSE)
        {
            $this->output->append_output($template_string);
        }
        
        return $template_string;
    }
    
    public function parse_string($template, $data = '', $return = false, $use_theme = FALSE)
    {
        return $this->parse($template, $data, $return, $use_theme);
    }

}