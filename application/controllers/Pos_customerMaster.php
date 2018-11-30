<?php
/**
 *
 * -- =============================================
 * -- File Name : Pos_customerMaster.php
 * -- Project Name : POS
 * -- Module Name : POS Customer Master
 * -- Author : Mohamed Shafri
 * -- Create date : 19 October 2017
 * -- Description : Customer Master Controller
 *
 * --REVISION HISTORY
 * --Date: 19-Oct 2017 By: Mohamed Shafri: file created
 *
 * -- =============================================
 **/
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos_customerMaster extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Pos_customer_master');
        $this->load->helper('pos');
    }


    function fetch_customer()
    {
        $this->datatables->select('posCustomerAutoID, IF(rowTbl.CustomerAutoID IS NULL,masterTbl.CustomerName, rowTbl.CustomerName)  AS  CustomerName, masterTbl.posCustomerAutoID as posCustomerAutoID, masterTbl.CustomerAutoID as CustomerAutoID, masterTbl.serialNo as  serialNo, IF(rowTbl.CustomerAutoID IS NULL,masterTbl.customerCountry, rowTbl.customerCountry) AS  customerCountry, IF(rowTbl.CustomerAutoID IS NULL,masterTbl.customerTelephone, rowTbl.customerTelephone) AS customerTelephone, IF(rowTbl.CustomerAutoID IS NULL,masterTbl.customerEmail, rowTbl.customerEmail) AS customerEmail, masterTbl.isFromERP as isFromERP, IF(rowTbl.CustomerAutoID IS NULL,masterTbl.CustomerAddress1, rowTbl.CustomerAddress1) AS CustomerAddress1', false)
            ->from('srp_erp_pos_customermaster as masterTbl')
            ->join('srp_erp_customermaster rowTbl','rowTbl.CustomerAutoID = masterTbl.CustomerAutoID', 'left')
            ->where('masterTbl.companyID', current_companyID());
        $this->datatables->add_column('countryDiv', '$1', 'countryDiv_pos(customerCountry)');
        $this->datatables->add_column('edit', '$1', 'edit_pos_customer(posCustomerAutoID, isFromERP)');

        $result = $this->datatables->generate();
        echo $result;
    }

    function fetch_sync_customer()
    {
        $this->datatables->select('masterTbl.customerAutoID as customerAutoID, masterTbl.customerSystemCode as customerSystemCode, masterTbl.customerName as customerName, masterTbl.customerTelephone as customerTelephone, masterTbl.customerAddress1 as customerAddress1, masterTbl.customerEmail as customerEmail, masterTbl.customerCountry as customerCountry', false)
            ->from('srp_erp_customermaster as masterTbl')
            ->where('companyID', current_companyID());
        $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_pos_customermaster WHERE srp_erp_pos_customermaster.CustomerAutoID = masterTbl.CustomerAutoID AND companyID =' . current_companyID() . ' )');

        $this->datatables->add_column('countryDiv', '$1', 'countryDiv(customerCountry)');
        $this->datatables->add_column('edit', '$1', 'edit(primaryKey,isActive)');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="ItemsSelectedSync(this)" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'customerAutoID');

        $result = $this->datatables->generate();
        echo $result;
    }

    function add_customers()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Item', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Pos_customer_master->add_customer());
        }
    }

    function add_edit_customer()
    {
        $posCustomerAutoID = $this->input->post('posCustomerAutoID');

        $this->form_validation->set_rules('CustomerName', 'Customer Name', 'trim|required');
        $this->form_validation->set_rules('customerCountry', 'Country', 'trim|required');
        $this->form_validation->set_rules('customerEmail', 'Email', 'trim|required');
        $this->form_validation->set_rules('CustomerAddress1', 'Address', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error' => 1, 'message' => validation_errors()));
        } else {
            if ($posCustomerAutoID) {
                /** Update */
                echo json_encode($this->Pos_customer_master->update_customer());
            } else {
                /** Insert */
                echo json_encode($this->Pos_customer_master->insert_customer());
            }
        }
    }

    function loadCustomerDetail()
    {
        $result = $this->Pos_customer_master->get_srp_erp_mfq_customers();
        if (!empty($result)) {
            echo json_encode(array_merge(array('error' => 0, 'message' => 'Loading customer detail'), $result));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'record not found!'));
        }
    }


}