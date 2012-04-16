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

// Your views directory with a trailing slash
$config['template_directory']   = array(APPPATH."views/");

// Smarty caching enabled by default unless explicitly set to 0
$config['cache_status']         = 1;

// Cache lifetime. Default value is 3600 seconds (1 hour) Smarty's default value
$config['cache_lifetime']       = 3600;

// Where templates are compiled
$config['compile_directory']    = APPPATH."cache/smarty/compiled/";

// Where templates are cached
$config['cache_directory']      = APPPATH."cache/smarty/cached/";

// Where Smarty configs are located
$config['config_directory']     = APPPATH."third_party/Smarty/configs/";

// Default extension of templates if one isn't supplied
$config['template_ext']         = 'tpl';

// Error reporting level to use while processing templates
$config['template_error_reporting'] = E_ALL & ~E_NOTICE;
