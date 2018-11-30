<?php
/**
 *
 * -- =============================================
 * -- File Name : Pos_config.php
 * -- Project Name : POS
 * -- Module Name : POS Config model
 * -- Author : Mohamed Shafri
 * -- Create date : 13 October 2016
 * -- Description : database script related to pos config.
 *
 * --REVISION HISTORY
 * --Date: 13-Oct 2016 By: Mohamed Shafri: file created
 * -- =============================================
 **/
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pos_customer_master extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function add_customer()
    {
        $result = $this->db->query('INSERT INTO srp_erp_pos_customermaster ( CustomerAutoID, CustomerSystemCode, CustomerName, partyCategoryID, CustomerAddress1, customerAddress2, customerCountry, customerTelephone, customerEmail, customerUrl, customerFax, secondaryCode, customerCurrencyID, customerCurrency, customerCurrencyDecimalPlaces, isActive, companyID, companyCode, createdUserGroup, createdPCID, createdUserID, createdUserName, createdDateTime, modifiedPCID, modifiedUserID, modifiedUserName, modifiedDateTime, `timestamp` , isCardHolder) 
                                SELECT customerAutoID,  customerSystemCode, customerName, partyCategoryID, customerAddress1, customerAddress2, customerCountry, customerTelephone, customerEmail, customerUrl, customerFax, secondaryCode, customerCurrencyID, customerCurrency, customerCurrencyDecimalPlaces, isActive, companyID, companyCode, createdUserGroup, createdPCID, createdUserID, createdUserName, createdDateTime, modifiedPCID, modifiedUserID, modifiedUserName, modifiedDateTime, `timestamp` , 0
 FROM srp_erp_customermaster 
                                WHERE companyID = ' . current_companyID() . '  AND customerAutoID IN(' . join(",", $this->input->post('selectedItemsSync')) . ')');

        if ($result) {
            $this->session->set_flashdata('s', 'Records added Successfully');
            return array('status' => true);
        }
    }

    function insert_customer()
    {
        $post = $this->input->post();
        unset($post['posCustomerAutoID']);

        $datetime = format_date_mysql_datetime();
        $post['companyID'] = current_companyID();
        $post['createdUserID'] = current_userID();
        $post['createdPCID'] = current_pc();
        $post['createdDateTime'] = $datetime;
        $post['timestamp'] = $datetime;
        $post['isFromERP'] = 0;

        $result = $this->db->insert('srp_erp_pos_customermaster', $post);
        if ($result) {
            return array('error' => 0, 'message' => 'Customer successfully Added', 'code' => 1);
        } else {
            return array('error' => 1, 'message' => 'Code: ' . $this->db->_error_number() . ' <br/>Message: ' . $this->db->_error_message());
        }

    }

    function get_srp_erp_mfq_customers()
    {
        $posCustomerAutoID = $this->input->post('posCustomerAutoID');
        $this->db->select('*');
        $this->db->from('srp_erp_pos_customermaster');
        $this->db->where('posCustomerAutoID', $posCustomerAutoID);
        $result = $this->db->get()->row_array();
        return $result;
    }


    function update_customer()
    {
        $posCustomerAutoID = $this->input->post('posCustomerAutoID');
        $post = $this->input->post();
        unset($post['posCustomerAutoID']);

        $post['modifiedUserID'] = current_userID();
        $post['modifiedPCID'] = current_pc();
        $post['modifiedDateTime'] = format_date_mysql_datetime();


        $this->db->where('posCustomerAutoID', $posCustomerAutoID);
        $result = $this->db->update('srp_erp_pos_customermaster', $post);
        if ($result) {
            return array('error' => 0, 'message' => 'Customer updated successfully', 'code' => 2);
        } else {
            return array('error' => 1, 'message' => 'Code: ' . $this->db->_error_number() . ' <br/>Message: ' . $this->db->_error_message());
        }

    }

}