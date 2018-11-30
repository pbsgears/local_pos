<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** ================================
 * -- Project Name : POS
 * -- Module Name : Point of sale
 * -- Author : Mohamed Shafri
 * -- Create date : 30 - March 2018
 * -- Description : To manage policy of POS
 *
 */
class Pos_policy_controller extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        if (!isset($this->common_data['status']) || empty(trim($this->common_data['status']))) {
            header('Location: ' . site_url('Login/logout'));
            exit;
        } else {
            $this->load->library('Pos_policy');
            $this->load->helper('pos');
        }
    }

    function loadPolicyValues_view()
    {

        $data['masters'] = $this->pos_policy->get_policy();
        $this->load->view('system/pos/settings/policy/ajax/get_policy', $data);
    }

    function change_policy()
    {
        $outletID = $this->input->post('outletID');
        $status = $this->input->post('status');
        $posPolicyMasterID = $this->input->post('posPolicyMasterID');
        $companyID = current_companyID();


        if ($status) {
            $message = 'add';

            $data['posPolicyMasterID'] = $posPolicyMasterID;
            $data['outletID'] = $outletID;
            $data['companyID'] = $companyID;
            $data['companyCode'] = current_company_code();
            $data['createdPCID'] = current_pc();
            $data['createdUserID'] = current_userID();
            $data['createdDateTime'] = format_date_mysql_datetime();
            $data['createdUserName'] = current_user();
            $data['timestamp'] = format_date_mysql_datetime();

            $this->db->insert('srp_erp_pos_policydetail', $data);

        } else {
            $this->db->select('*');
            $this->db->from('srp_erp_pos_policydetail');
            $this->db->where('companyID', $companyID);
            $this->db->where('outletID', $outletID);
            $this->db->where('posPolicyMasterID', $posPolicyMasterID);
            $result = $this->db->get()->row_array();

            if (!empty($result)) {
                $this->db->delete('srp_erp_pos_policydetail', array('posPolicyID' => $result['posPolicyID']));
            }
        }


        $result = array('status' => 'e', 'message' => 'Policy updated successfully', 'q' => $this->db->last_query());
        echo json_encode($result);
    }


}