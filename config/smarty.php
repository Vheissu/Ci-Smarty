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

// Smarty caching enabled by default unless explicitly set to 0
$config['cache_status']         = 1;

// The path to the themes
$config['theme_path'] = FCPATH . '/themes/';

// The default name of the theme to use (this can be overridden)
$config['theme_name'] = "default";

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
