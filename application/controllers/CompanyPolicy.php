<?php

class CompanyPolicy extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('CompanyPolicy_model');
        $this->load->helper('CompanyPolicy_helper');
    }

    function fetch_company_policy()
    {
        $companyId = current_companyID();

        $data['detail'] = $this->db->query("SELECT `policymaster`.`companypolicymasterID` AS `companypolicymasterID`, `policymaster`.`code` AS `code`, `policymaster`.`companyPolicyDescription` AS `companyPolicyDescription`, `policymaster`.`fieldType` AS `fieldType`, `policydetails`.`value` AS `companyValue`, `policymaster`.`documentID` AS `documentID`,0 AS isCompanyLevel FROM `srp_erp_companypolicymaster` `policymaster` LEFT JOIN ( SELECT * FROM `srp_erp_companypolicy` WHERE companyID = '{$companyId}' ) `policydetails` ON `policymaster`.`companypolicymasterID` = `policydetails`.`companypolicymasterID` WHERE `policymaster`.`isCompanyLevel` = 0 GROUP BY companypolicymasterID UNION SELECT `policymaster`.`companypolicymasterID` AS `companypolicymasterID`, `policymaster`.`companyPolicyDescription` AS `companyPolicyDescription`,`policymaster`.`code` AS `code`, `policymaster`.`fieldType` AS `fieldType`, `policydetails`.`value` AS `companyValue`, `policymaster`.`documentID` AS `documentID`,1 AS isCompanyLevel FROM `srp_erp_companypolicymaster` `policymaster` LEFT JOIN ( SELECT srp_erp_companypolicy.companyPolicyAutoID, srp_erp_companypolicy.companypolicymasterID, srp_erp_companypolicy.companyID, srp_erp_companypolicy.documentID, srp_erp_companypolicy.`value` FROM `srp_erp_companypolicy` INNER JOIN srp_erp_companypolicymaster_value ON srp_erp_companypolicy.companypolicymasterID = srp_erp_companypolicymaster_value.companypolicymasterID WHERE srp_erp_companypolicy.companyID = '{$companyId}' ) `policydetails` ON `policymaster`.`companypolicymasterID` = `policydetails`.`companypolicymasterID` WHERE `policymaster`.`isCompanyLevel` = 1 AND `policydetails`.`companyID` = '{$companyId}' GROUP BY companypolicymasterID")->result_array();
        echo $this->load->view('system/erp_company_policy_table', $data, true);

    }

    function master_policy_update()
    {
        $this->form_validation->set_rules('autoID', 'ID Is Missing', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CompanyPolicy_model->master_policy_update());
        }
    }

    function policy_detail_update()
    {
        $this->form_validation->set_rules('id', 'Id is missing.', 'trim|required');
        $this->form_validation->set_rules('value', 'Value is required.', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->CompanyPolicy_model->policy_detail_update());
        }
        exit;
        /*$fields = $this->db->query("SELECT * FROM srp_erp_companypolicymaster")->result_array();

        foreach ($fields as $field) {
            $this->form_validation->set_rules("{$field['companypolicymasterID']}", "{$field['companyPolicyDescription']} is missing", 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CompanyPolicy_model->policy_detail_update());
        }*/
    }

    function get_document_policy()
    {
        $documentID = $this->input->post('documentID');
        $companyID = current_companyID();
        $getPolicy = $this->db->query("SELECT companypolicymasterID,value FROM srp_erp_companypolicy WHERE `companyID` = '{$companyID}' AND `documentID` = '{$documentID}'")->result_array();
        echo json_encode($getPolicy);
    }

    function policy()
    {
        $this->load->view('system/company/feed-policy');
    }

    function get_password_policy(){
        echo json_encode($this->CompanyPolicy_model->get_password_policy());
    }

    function save_password_complexity(){
        echo json_encode($this->CompanyPolicy_model->save_password_complexity());
    }
}