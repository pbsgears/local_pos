<?php
class Authentication{
    protected $CI;
    public function __construct() {
        $this->CI = & get_instance();
    }
    public function check_user_login(){
        if(!$this->CI->session->has_userdata('status')){
            $data['title'] = 'Login';
            $data['extra'] = NULL;
            $this->load->view('login_page', $data);
        }
    }
}