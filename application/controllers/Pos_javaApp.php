<?php
/**
 *
 * -- =============================================
 * -- File Name : giftCard.php
 * -- Project Name : POS
 * -- Module Name : POS Gift Card
 * -- Author : Mohamed Shafri
 * -- Create date : 19 October 2017
 * -- Description : Gift Card masters and Gift Card Process .
 *
 * --REVISION HISTORY
 * --Date: 19-Oct 2017 By: Mohamed Shafri: file created
 * -- =============================================
 **/
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos_javaApp extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Pos_javaAppModel');
        $this->load->helper('pos');
    }


    function checkJavaApp()
    {
        $this->form_validation->set_rules('appPIN', 'PIN Code', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            $result = $this->Pos_javaAppModel->check_pinExist();
            echo json_encode($result);
        }
    }


}