<?php
/*
-- =============================================
-- File Name : Email_model.php
-- Project Name : SME ERP
-- Module Name : Email
-- Author : Mohamed Mubashir
-- Create date : 20 - December 2016
-- Description : This file contains sending email to reciepient.

-- REVISION HISTORY
-- =============================================*/
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Email_model extends CI_Model
{
    function __contruct()
    {
        parent::__contruct();
    }


    function fetch_email_details()
    {
        $this->db->trans_start();
        $this->db->select("*");
        $this->db->from("srp_erp_alert");
        $this->db->where("isEmailSend", 0);
        $this->db->order_by("timestamp");
        $result = $this->db->get()->result_array();
        $this->db->trans_complete();
        return $result;
    }

    function update_email_sent($alertID, $emailAddress)
    {
        $this->db->trans_start();
        $this->db->set('isEmailSend', -1);
        $this->db->set('timestamp', date('Y-m-d H:i:s', time()));
        $this->db->where('alertID', $alertID);
        $result = $this->db->update('srp_erp_alert');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            log_message('error', 'Mail not sent to :' . $alertID . '-' . $emailAddress);
        } else {
            log_message('info', 'Mail sent to :' . $alertID . '-' . $emailAddress);
        }
    }

}