<?php

class Pos_auth_process extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Pos_auth_process_model');
        $this->load->helper('pos');
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('pos_restaurent', $primaryLanguage);
        $this->lang->load('common', $primaryLanguage);
        $this->lang->load('calendar', $primaryLanguage);
    }

    function check_pos_auth_process(){
        if($this->input->post('type') == 1){
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');
        }else{
            $this->form_validation->set_rules('pos_barCode', $this->lang->line('posr_access_card'), 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_auth_process_model->check_pos_auth_process());
        }
    }

    function check_has_pos_auth_process(){
        echo json_encode($this->Pos_auth_process_model->check_has_pos_auth_process());
    }


}
