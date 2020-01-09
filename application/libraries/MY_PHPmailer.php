<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once (APPPATH.'/third_party/phpmailer/class.phpmailer.php');
require_once (APPPATH.'/third_party/phpmailer/class.smtp.php');
class MY_PHPmailer extends PHPMailer
{
    function __construct()
    {
        parent::__construct();
    }
}