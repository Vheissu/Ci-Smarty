<?php

function smarty_function_img($params, $template)
{
    $CI =& get_instance();
    $CI->load->helper('parser');

    $file = $params['file'];
    unset($params['file']);

    img($file, $params);
}