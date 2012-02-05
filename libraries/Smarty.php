<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* @name CI Smarty
* @copyright Dwayne Charrington, 2012.
* @author Dwayne Charrington and other Github contributors
* @license (DWYWALAYAM) 
           Do What You Want As Long As You Attribute Me Licence
* @version 1.3
* @link http://ilikekillnerds.com
*/

require_once APPPATH."third_party/Smarty/Smarty.class.php";

class CI_Smarty extends Smarty {
    
    public $template_ext = '.php';

    public function __construct()
    {
        parent::__construct();

        // Store the Codeigniter super global instance... whatever
        $CI = get_instance();

        $CI->load->config('smarty');

        $this->template_dir      = config_item('template_directory');
        $this->compile_dir       = config_item('compile_directory');
        $this->cache_dir         = config_item('cache_directory');
        $this->config_dir        = config_item('config_directory');
        $this->template_ext      = config_item('template_ext');
        
        $this->cache_lifetime    = config_item('cache_lifetime');
        
        $this->disableSecurity();
        
        // If caching is enabled, then disable force compile and enable cache
        if (config_item('cache_status') === TRUE)
        {
            $this->caching       = 1;
        }
        else
        {
            $this->disable_caching();
        }
        
        $this->error_reporting   = config_item('template_error_reporting');
        
        // Should let us access Codeigniter stuff in views
        $this->assign("this", $CI);
    }
    
    /**
    * Disable Caching
    * Allows you to disable caching on a page by page basis
    * @example $this->smarty->disable_caching(); then do your parse call
    * 
    */
    public function disable_caching()
    {
        $this->caching       = 0; 
    }

}