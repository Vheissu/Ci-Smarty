<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'third_party/PHPMailer/class.phpmailer.php';
require_once APPPATH.'third_party/PHPMailer/class.smtp.php';
/**
 * Send Email
 * @param  String $to    UserEmail
 * @param  String $title EmailTitle
 * @param  String $body  EmailMessage
 * @return Boolean       TrueOrFalse
 */
function send_mail($to,$title,$body)
{
    static $EMAILNAME = 'CF_EmailName';
    static $EMAILPWD  = 'CF_EmailPwd';

    $ci =& get_instance();
    $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
    $mail->IsSMTP(); // telling the class to use SMTP
    try {
        // For 163
        // $mail->Host       = 'smtp.163.com';
        // $mail->SMTPDebug  = false;
        // $mail->SMTPAuth   = true;
        // $mail->Port       = 25;

        // For Gmail
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPDebug  = false;
        $mail->SMTPAuth   = true;
        $mail->Port       = 465;
        // ↓ Very important
        $mail->SMTPSecure = "ssl";

        // Please change your username and password to your own !
        $mail->Username   = "your account";
        $mail->Password   = "your password";

        $mail->AddAddress($to, 'Kings Of Capital User');
        $mail->SetFrom($emailName, 'Kings Of Capital');

        $mail->Subject = $title;
        $mail->MsgHTML($body);

        $mail->Send();
        return true;
    } catch (phpmailerException $e) {
        log_message('error', 'mail_exception:'.$e->errorMessage());
        echo $e->errorMessage(); //Pretty error messages from PHPMailer
        return false;
    } catch (Exception $e) {
        log_message('error', 'mail_exception:'.$e->errorMessage());
        echo $e->getMessage(); //Boring error messages from anything else!
        return false;
    }
}
