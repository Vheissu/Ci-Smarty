<?php

function smarty_function_js($params, $template)
{
    $CI =& get_instance();
    $CI->load->helper('parser');

    $file = $params['file'];
    unset($params['file']);

    js($file, $params);
}