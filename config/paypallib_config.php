<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
    // ------------------------------------------------------------------------
    // Ppal (Paypal IPN Class)
    // ------------------------------------------------------------------------
    // If (and where) to log ipn to file
    // Paypal 支付接口地址
    $config['paypal_url'] = 'https://www.paypal.com/cgi-bin/webscr';
    // Paypal 测试接口地址
    //$config['paypal_url'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    $config['paypal_lib_ipn_log_file'] = '/var/log/kingsLog/paypal_ipn.log';
    $config['paypal_lib_ipn_log'] = TRUE;
    // Where are the buttons located at
    $config['paypal_lib_button_path'] = 'public/img/buttons';
    // What is the default currency?
    $config['paypal_lib_currency_code'] = 'USD';

