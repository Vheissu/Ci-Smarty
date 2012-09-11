<?php

function smarty_function_css($params, $template)
{
    $CI =& get_instance();
    $CI->load->helper('parser');

    $file = $params['file'];
    unset($params['file']);

    css($file, $params);
}