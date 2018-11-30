<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class CompanyPolicy_model extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function master_policy_update()
    {
        $autoID = $this->input->post('autoID');
        $isChecked = $this->input->post('isChecked');

        $data = array('is_active' => $isChecked);
        $this->db->where('companypolicymasterID', $autoID)->update('srp_erp_companypolicymaster', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }
    }

    function policy_detail_update()
    {
        $companyID = current_companyID();
        $id = $_POST['id'];
        $value = $_POST['value'];
        $type = $_POST['type'];


        $this->db->where('companyID', $companyID)
            ->where('companypolicymasterID', $id)
            ->delete('srp_erp_companypolicy');

        $request['companypolicymasterID'] = $id;
        $request['companyID'] = $companyID;
        $request['documentID'] = $type;
        $request['isYN'] = 1;
        $request['value'] = $value;
        $request['createdUserGroup'] = current_user_group();
        $request['createdPCID'] = current_pc();
        $request['createdUserID'] = current_userID();
        $request['createdDateTime'] = current_date(true);
        $request['timestamp'] = current_date(true);
        $this->db->insert('srp_erp_companypolicy', $request);
        return array('s', 'Policy Updated.');
        exit;
        $fields = $this->db->query("SELECT * FROM srp_erp_companypolicymaster")->result_array();
        $documentId = $_POST['documentId'];


        $request = array();

        unset($_POST['documentId']);

        foreach ($_POST as $key => $post) {
            $request[$key]['companypolicymasterID'] = $key;
            $request[$key]['companyID'] = $companyID;
            $request[$key]['documentID'] = $documentId;
            $request[$key]['isYN'] = 1;
            $request[$key]['value'] = $post;
            $request[$key]['createdUserGroup'] = current_user_group();
            $request[$key]['createdPCID'] = current_pc();
            $request[$key]['createdUserID'] = current_userID();
            $request[$key]['createdDateTime'] = current_date(true);
            $request[$key]['timestamp'] = current_date(true);
        }

        $this->db->where('companyID', $companyID)
            ->where('documentID', $documentId)
            ->delete('srp_erp_companypolicy');
        $this->db->insert_batch('srp_erp_companypolicy', $request);

        return array('s', 'Policy Updated.');
    }

    function get_password_policy(){
        $this->db->select('*');
        $this->db->where('companyID', trim(current_companyID()));
        $this->db->from('srp_erp_passwordcomplexcity');
        return $this->db->get()->row_array();
    }

    function save_password_complexity(){
        $status=$this->db->delete('srp_erp_passwordcomplexcity', array('companyID' => trim(current_companyID())));
        if($status){
            $this->db->trans_start();
            $companyid = $this->common_data['company_data']['company_id'];

            $data['minimumLength'] = trim($this->input->post('minimumLength'));
            $data['maximumLength'] = trim($this->input->post('maximumLength'));
            $data['isCapitalLettersMandatory'] = trim($this->input->post('isCapitalLettersMandatory'));
            $data['isSpecialCharactersMandatory'] = trim($this->input->post('isSpecialCharactersMandatory'));
            $data['companyID'] = trim($companyid);
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_passwordcomplexcity', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e', 'Save Failed');
            } else {
                return array('s', 'Saved Successfully');
            }
        }else{
            return array('e', 'Deletion Failed');
        }

    }
}