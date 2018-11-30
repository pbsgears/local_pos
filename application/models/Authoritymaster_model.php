<?php

class Authoritymaster_model extends ERP_Model
{

    function save_supplier_master()
    {
        $this->db->trans_start();
        $liability = fetch_gl_account_desc(trim($this->input->post('taxPayableGLAutoID')));

        $data['authoritySecondaryCode'] = trim($this->input->post('authoritySecondaryCode'));
        $data['AuthorityName'] = trim($this->input->post('AuthorityName'));
        $data['telephone'] = trim($this->input->post('telephone'));
        $data['email'] = trim($this->input->post('email'));
        $data['fax'] = trim($this->input->post('fax'));
        $data['address'] = trim($this->input->post('address'));
        $data['taxPayableGLAutoID'] = $liability['GLAutoID'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('taxAuthourityMasterID'))) {
            $this->db->where('taxAuthourityMasterID', trim($this->input->post('taxAuthourityMasterID')));
            $this->db->update('srp_erp_taxauthorithymaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Authority : ' . $data['AuthorityName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Authority : ' . $data['AuthorityName'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('taxAuthourityMasterID'));
            }
        } else {
            $this->load->library('sequence');
            $data['currencyID'] = trim($this->input->post('currencyID'));
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['authoritySystemCode'] = $this->sequence->sequence_generator('AUT');
            $this->db->insert('srp_erp_taxauthorithymaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Authority : ' . $data['AuthorityName'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Authority : ' . $data['AuthorityName'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function load_authority_header()
    {
        $this->db->select('*');
        $this->db->where('taxAuthourityMasterID', $this->input->post('taxAuthourityMasterID'));
        return $this->db->get('srp_erp_taxauthorithymaster')->row_array();
    }

    function delete_authority()
    {
        $this->db->where('taxAuthourityMasterID', $this->input->post('taxAuthourityMasterID'));
        $result = $this->db->delete('srp_erp_taxauthorithymaster');
        if($result){
            return array('s','Deleted Successfully');
        }else{
            return array('e','Deletion Failed');
        }


    }

}