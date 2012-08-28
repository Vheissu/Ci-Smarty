<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function theme_url($location = '')
{
    $CI =& get_instance();

    return $CI->parser->theme_url($location);
}

function css($file)
{
    $CI =& get_instance();

    echo $CI->parser->css($file);
}

function js($file)
{
    $CI =& get_instance();

    echo $CI->parser->js($file);
}

function img($file)
{
    $CI =& get_instance();

    echo $CI->parser->img($file);
}