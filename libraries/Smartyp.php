<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."third_party/Smarty/Smarty.class.php";

class Smartyp extends Smarty
{
    public function __construct()
    {
        parent::__construct();
        
        $this->template_dir = APPPATH."views/";                     // Where your templates are to be loaded from
        $this->compile_dir  = BASEPATH."cache/smarty/compiled";     // Where templates are compiled
        $this->cache_dir    = BASEPATH."cache/smarty/cached";       // Where templates are cached
        $this->config_dir   = APPPATH."third_party/Smarty/configs"; // Where Smarty configs are located
    }

    public function _assign_variables($variables = array())
    {
        foreach ($variables as $name => $val) 
        {
            $this->assign($name, $val);
        }
    }

}