<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Parser extends CI_Parser
{

    private $_ci;
    private $_smarty;
    private $_parser_compile_dir = '';
    private $_parser_cache_dir = '';
    private $_parser_cache_time = 0;
    private $_parser_assign_refs = array();

}