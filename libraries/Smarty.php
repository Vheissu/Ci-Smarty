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

require_once APPPATH."third_party/Smarty/Smarty.class.php";

class CI_Smarty extends Smarty {
    
    public $template_ext = '.php';

    public function __construct()
    {
        parent::__construct();

        // Store the Codeigniter super global instance... whatever
        $CI = get_instance();

        $CI->load->config('smarty');

        $this->addTemplateDir(config_item('template_directory'));
        $this->setCompileDir(config_item('compile_directory'));
        $this->setCacheDir(config_item('cache_directory'));
        $this->setConfigDir(config_item('config_directory'));

        // Default template extension
        $this->template_ext = config_item('template_ext');
        
        // How long to cache templates for
        $this->cache_lifetime = config_item('cache_lifetime');
        
        // Disable Smarty security policy
        $this->disableSecurity();
        
        // If caching is enabled, then disable force compile and enable cache
        if (config_item('cache_status') === TRUE)
        {
            $this->enable_caching();
        }
        else
        {
            $this->disable_caching();
        }
        
        $this->error_reporting   = config_item('template_error_reporting');

        // Looking for view sub folders?
        if (config_item('traverse_view_directories') == TRUE)
        {
            $this->add_extends_locations();
        }
        
        // Should let us access Codeigniter stuff in views
        // This means we can go for example {$this->session->userdata('item')}
        // just like we normally would in standard CI views
        $this->assign("this", $CI);
    }

    /**
     * If Smarty doesn't know every location of a file being extended
     * it will throw an error. It is presumed that master files being
     * extended are most likely in your application/views folder, but
     * you might have an instance when your main layout file is in
     * application/views/layouts for example.
     * 
     * If the thought of a function traversing your views folder scares
     * you from a performance point of view, you can turn it off in the
     * config file and add in your view locations manually (probably ideal).     
     *
     *
     */
    protected function add_extends_locations()
    {
        $CI = get_instance();
        $CI->load->helper('directory');

        // The views path
        $base_path = APPPATH."views/";

        $map = directory_map(APPPATH."views", 1);

        // Blank array for our found folders
        $folders = array();

        if ($map !== false)
        {        
            foreach ($map AS $key => $name)
            {                 
                $name = strtolower(trim($name));
                      
                // Ignore files
                if ( !stripos($name, ".") )
                {              
                    $folders[] = $base_path.$name;
                }
            }

            // Yarr, we got a map lets find the treasure
            if (!empty($folders))
            {
                $this->addTemplateDir($folders);
            }

        }
    }

    /**
     * Allows you to enable caching on a page by page basis
     * @example $this->smarty->enable_caching(); then do your parse call
     */
    public function enable_caching()
    {
        $this->caching = 1;
    }
    
    /**
     * Allows you to disable caching on a page by page basis
     * @example $this->smarty->disable_caching(); then do your parse call
     */
    public function disable_caching()
    {
        $this->caching = 0; 
    }

}