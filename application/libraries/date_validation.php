<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class MY_Form_validation extends CI_Form_validation {

    public function __construct($rules = array())
    {
        parent::__construct($rules);
        $this->ci =& get_instance();
        $this->ci->load->library('session');
        $this->ci->load->database();
    }


    public function valid_date($date)
    {
        $DateFormate = convert_date_format();
        $date_format_policy = date_format_policy();
        $convertedDate = input_format_date($date, $date_format_policy);
        $d = DateTime::createFromFormat($DateFormate, $convertedDate);
        if ($convertedDate == "1970-01-01") {
            return false;
        } else {
            return true;
        }
    }
}

