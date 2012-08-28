<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Theme URL
 *
 * A helper function for getting the current theme URL
 * in a web friendly format.
 *
 * @param string $location
 * @return mixed
 */
function theme_url($location = '')
{
    $CI =& get_instance();

    return $CI->parser->theme_url($location);
}

/**
 * CSS
 *
 * A helper function for getting the current theme CSS embed code
 * in a web friendly format
 *
 * @param $file
 */
function css($file)
{
    $CI =& get_instance();

    echo $CI->parser->css($file);
}

/**
 * JS
 *
 * A helper function for getting the current theme JS embed code
 * in a web friendly format
 *
 * @param $file
 */
function js($file)
{
    $CI =& get_instance();

    echo $CI->parser->js($file);
}

/**
 * IMG
 *
 * A helper function for getting the current theme IMG embed code
 * in a web friendly format
 *
 * @param $file
 */
function img($file)
{
    $CI =& get_instance();

    echo $CI->parser->img($file);
}