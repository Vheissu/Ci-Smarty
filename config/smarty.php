<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// Your views directory with a trailing slash
$config['template_directory']   = APPPATH."views/";

// Where templates are compiled
$config['compile_directory']    = APPPATH."cache/smarty/compiled";

// Where templates are cached
$config['cache_directory']      = APPPATH."cache/smarty/cached";

// Where Smarty configs are located
$config['config_directory']     = APPPATH."third_party/Smarty/configs";

// Default extension of templates if one isn't supplied
$config['template_ext']         = 'php';

// PHP error reporting level (can be any valid error reporting level)
$config['error_reporting'] = "E_ERROR";