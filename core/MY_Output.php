<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Turn Smarty debug compatible whith Ci-Smarty parse fetch method
 *
 * Responsible for sending debug Smarty final output to browser (Smarty_Internal_Debug::display_debug) using debug console (pop-window)
 *(tks for Redn0x - http://www.smarty.net/docs/en/chapter.debugging.console.tpl)
 *
 * @package       CodeIgniter
 * @subpackage    Core
 * @hacked-by     octaaugusto	  
*/


class MY_Output extends CI_Output {
    	
	public function _display($output = '')
	{
		parent::_display($output);
		// Grab the super object if we can.
		if (class_exists('CI_Controller') && (config_item('smarty_debug') || $this->enable_profiler))
		{
			$CI =& get_instance();
			Smarty_Internal_Debug::display_debug( $CI->smarty);
		}
	}
}
// END MY_Output Class

/* End of file MY_Output.php */
/* Location: ./application/core/MY_Output.php */