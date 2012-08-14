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

require_once APPPATH."third_party/Smarty/Smarty.class.php";

class CI_Smarty extends Smarty {
    
    public $template_ext = '.php';

    public function __construct()
    {
        parent::__construct();

        // Store the Codeigniter super global instance... whatever
        $CI = get_instance();

        // Load the Smarty config file
        $CI->load->config('smarty');
        
        // Turn on/off debug
        $this->debugging = config_item('smarty_debug');

        // Set some pretty standard Smarty directories
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
        
        // Set the error reporting level
        $this->error_reporting   = config_item('template_error_reporting');
        
        // Should let us access Codeigniter stuff in views
        // This means we can go for example {$this->session->userdata('item')}
        // just like we normally would in standard CI views
        $this->assign("this", $CI);

    }

    /**
     * Enable Caching
     *
     * Allows you to enable caching on a page by page basis
     * @example $this->smarty->enable_caching(); then do your parse call
     */
    public function enable_caching()
    {
        $this->caching = 1;
    }
    
    /**
     * Disable Caching
     *
     * Allows you to disable caching on a page by page basis
     * @example $this->smarty->disable_caching(); then do your parse call
     */
    public function disable_caching()
    {
        $this->caching = 0; 
    }

}