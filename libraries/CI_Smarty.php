<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."third_party/Smarty/Smarty.class.php";

class CI_Smarty extends Smarty
{
    public function __construct()
    {
        parent::__construct();

        $this->template_dir = APPPATH."views/";                     // Where your templates are to be loaded from
        $this->compile_dir  = BASEPATH."cache/smarty/compiled";     // Where templates are compiled
        $this->cache_dir    = APPPATH."cache/smarty/cached";        // Where templates are cached
        $this->config_dir   = APPPATH."third_party/Smarty/configs"; // Where Smarty configs are located

        // Add all helpers to plugins_dir
        $helpers = glob(APPPATH . 'helpers/*', GLOB_ONLYDIR | GLOB_MARK);

        foreach ($helpers as $helper)
        {
            $this->plugins_dir[] = $helper;
        }

        if ( method_exists( $this, 'assignByRef') )
        {
            $ci =& get_instance();
            $this->assignByRef("ci", $ci);
        }

    }

    public function _assign_variables($variables = array())
    {
        foreach ($variables as $name => $val)
        {
            $this->assign($name, $val);
        }
    }

    public function set_template_dir($directory)
    {
        $this->template_dir = $directory;
    }

}