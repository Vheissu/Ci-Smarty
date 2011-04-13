<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."third_party/Smarty/Smarty.class.php";

class CI_Smarty extends Smarty {

    // Codeigniter instance
    protected $CI;

    public function __construct()
    {
        parent::__construct();

        // Store the Codeigniter super global instance... whatever
        $this->CI = get_instance();

        $this->CI->load->config('smarty');

        $this->template_dir      = $this->CI->config->item('template_directory');
        $this->compile_dir       = $this->CI->config->item('compile_directory');
        $this->cache_dir         = $this->CI->config->item('cache_directory');
        $this->config_dir        = $this->CI->config->item('config_directory');
        $this->template_ext      = $this->CI->config->item('template_ext');
        $this->exception_handler = null;
        
        // Only show serious errors. Without this if you try and use variables that
        // do not exist, Smarty will throw variable does not exist errors
        $this->error_reporting   = "E_ERROR";

        // Add all helpers to plugins_dir
        $helpers = glob(APPPATH . 'helpers/', GLOB_ONLYDIR | GLOB_MARK);

        foreach ($helpers as $helper)
        {
            $this->plugins_dir[] = $helper;
        }
        
        // Should let us access Codeigniter stuff in views
        $this->assign("this", $this->CI);

    }

}