<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CI Smarty
 *
 * Smarty templating for Codeigniter
 *
 * Responsible for sending debug Smarty final output to browser
 *
 * @package    CI Smarty
 * @subpackage Core
 * @author     Dwayne Charrington
 * @copyright  2017 Dwayne Charrington and Github contributors
 * @link       http://ilikekillnerds.com
 * @license    MIT
 * @version    3.0
 */


class MY_Output extends CI_Output {

	public function _display($output = '') {
	    parent::_display($output);

  		$CI =& get_instance();
		$CI->smarty->_debug->display_debug($CI->smarty, TRUE);
	}
}
