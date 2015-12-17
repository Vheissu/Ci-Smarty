<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CI Smarty
 *
 * Smarty templating for Codeigniter
 *
 * @package   CI Smarty
 * @author    Dwayne Charrington
 * @copyright 2015 Dwayne Charrington and Github contributors
 * @link      http://ilikekillnerds.com
 * @license   MIT
 * @version   3.0
 */

class Smartytest extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        // Ideally you would autoload the parser
        $this->load->library('parser');
    }

    public function index()
    {
        // Some example data
        $data['title'] = "The Smarty parser works!";
        $data['body']  = "This is body text to show that the Smarty Parser works!";

        // Load the template from the views directory
        $this->parser->parse("smartytest.tpl", $data);
    }

    /**
     * Showing off Smarty 3 template inheritance features
     *
     */
    public function inheritance()
    {
        // Some example data
        $data['title'] = "The Smarty parser works with template inheritance!";
        $data['body']  = "This is body text to show that Smarty 3 template inheritance works with Smarty Parser.";

        // Load the template from the views directory
        $this->parser->parse("inheritancetest.tpl", $data);

    }

    /**
     * 支付
     * @return [type] [description]
     */
    public function pay()
    {
        $orderNo = '123';
        $this->load->library('paypal_lib');
        // paypal生成的支付编码
        $this->paypal_lib->add_field('hosted_button_id', 'KM2DPM8XXXXXX');
        // 支付成功返回地址
        $this->paypal_lib->add_field('return', site_url('pay/success'));
        // 支付失败返回地址
        $this->paypal_lib->add_field('cancel_return', site_url('pay/cancel'));
        // ipn地址
        $this->paypal_lib->add_field('notify_url', site_url('pay/ipn')); // <-- IPN url
        // 自定义订单号
        $this->paypal_lib->add_field('custom', $orderNo);
        $this->paypal_lib->paypal_auto_form();
    }

    /**
     * 验证
     * @return [type] [description]
     */
    function ipn()
    {
        $this->load->library('paypal_lib');
        if ($this->paypal_lib->validate_ipn())
        {
            // 获取之前的订单号
            $orerNo = $this->paypal_lib->ipn_data['custom'];
            // 更改订单状态,记录日志
            // ...
        }
    }

    /**
     * Vimeo简单使用
     * @return [type] [description]
     */
    function vimeo()
    {
        $this->load->library('vimeo_lib');
        // 通过预置条件，使用API筛选视频
        $result = $this->vimeo_lib->request('/me/videos',array('per_page'=>$limit, 'page'=>$offset,'filter'=>'embeddable','filter_embeddable'=>'true', 'sort'=>'alphabetical', 'direction'=>'asc'));
        if($result)
        {
            //var_dump($result);
            $list = array();
            $list['total'] = $result['body']['total'];
            $list['data'] = $result['body']['data'];
            return $list;
        }
        else
        {
            return null;
        }
    }
}
