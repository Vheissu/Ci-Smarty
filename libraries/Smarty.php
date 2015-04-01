<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CI Smarty
 *
 * Smarty templating for Codeigniter
 *
 * @package   CI Smarty
 * @author    Dwayne Charrington
 * @copyright 2015 Dwayne Charrington and Github contributors
 * @link      http://ilikekillnerds.com
 * @license   MIT
 * @version   3.0
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
        $this->debugging = $CI->config->item('smarty.smarty_debug');

        // Set some pretty standard Smarty directories
        $this->setCompileDir($CI->config->item('smarty.compile_directory'));
        $this->setCacheDir($CI->config->item('smarty.cache_directory'));
        $this->setConfigDir($CI->config->item('smarty.config_directory'));

        // Default template extension
        $this->template_ext = $CI->config->item('smarty.template_ext');

        // How long to cache templates for
        $this->cache_lifetime = $CI->config->item('smarty.cache_lifetime');

        // Disable Smarty security policy
        $this->disableSecurity();

        // If caching is enabled, then disable force compile and enable cache
        if ( $CI->config->item('smarty.cache_status') === TRUE )
        {
            $this->enable_caching();
        }
        else
        {
            $this->disable_caching();
        }

        // Set the error reporting level
        $this->error_reporting   = $CI->config->item('smarty.template_error_reporting');

        // This will fix various issues like filemtime errors that some people experience
        // The cause of this is most likely setting the error_reporting value above
        // This is a static function in the main Smarty class
        Smarty::muteExpectedErrors();

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
