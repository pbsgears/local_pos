<?php

class Pos_auth_process_model extends ERP_Model
{
    function check_pos_auth_process()
    {
        $type= $this->input->post('type');
        $processMasterID = $this->input->post('processMasterID');
        if($type == 1) {
            $this->db->select('*');
            $this->db->from('srp_employeesdetails');
            $this->db->where('username', $this->input->post('username'));
            $this->db->where('password', md5($this->input->post('password')));
            $result = $this->db->get()->row_array();

            if ($result) {
                $this->db->select('*');
                $this->db->from('srp_erp_pos_auth_usergroupdetail');
                $this->db->join('srp_erp_pos_auth_usergroupmaster','srp_erp_pos_auth_usergroupdetail.userGroupMasterID = srp_erp_pos_auth_usergroupmaster.userGroupMasterID','inner');
                $this->db->where('processMasterID', $processMasterID);
                $this->db->where('srp_erp_pos_auth_usergroupdetail.userGroupMasterID', $result["pos_userGroupMasterID"]);
                $this->db->where('wareHouseID', get_outletID());
                $this->db->where('srp_erp_pos_auth_usergroupdetail.companyID', current_companyID());
                $this->db->where('srp_erp_pos_auth_usergroupmaster.isActive',1);
                $output = $this->db->get()->row_array();
                if($output){
                    return array('s', 'Access granted');
                }else{
                    return array('e', 'You don`t have access to do this process');
                }
            } else {
                return array('e', 'Authentication failed');
            }
        }else{
            $this->db->select('*');
            $this->db->from('srp_employeesdetails');
            $this->db->where('pos_barCode', $this->input->post('pos_barCode'));
            $result = $this->db->get()->row_array();

            if ($result) {
                $this->db->select('*');
                $this->db->from('srp_erp_pos_auth_usergroupdetail');
                $this->db->join('srp_erp_pos_auth_usergroupmaster','srp_erp_pos_auth_usergroupdetail.userGroupMasterID = srp_erp_pos_auth_usergroupmaster.userGroupMasterID','inner');
                $this->db->where('processMasterID', $processMasterID);
                $this->db->where('srp_erp_pos_auth_usergroupdetail.userGroupMasterID', $result["pos_userGroupMasterID"]);
                $this->db->where('wareHouseID', get_outletID());
                $this->db->where('srp_erp_pos_auth_usergroupdetail.companyID', current_companyID());
                $this->db->where('srp_erp_pos_auth_usergroupmaster.isActive',1);
                $output = $this->db->get()->row_array();
                if($output){
                    return array('s', 'Access granted');
                }else{
                    return array('e', 'You don`t have access to do this process');
                }
            } else {
                return array('e', 'Authentication failed');
            }
        }
    }

    function check_has_pos_auth_process(){
        $processMasterID = $this->input->post('processMasterID');
        $this->db->select('*');
        $this->db->from('srp_erp_pos_auth_processassign');
        $this->db->where('processMasterID', $processMasterID);
        $this->db->where('isActive', 1);
        $this->db->where('companyID', current_companyID());
        $result = $this->db->get()->row_array();
        if($result){
            return true;
        }else{
            return false;
        }
    }
}
