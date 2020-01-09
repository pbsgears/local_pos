<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Over_time_slab_modal extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function save_over_time_slab_header()
    {
        $this->db->trans_start();
        $data['Description'] = $this->input->post('Description');
        $data['transactionCurrencyID'] = $this->input->post('transactionCurrencyID');

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        if (!empty($this->input->post('otSlabsMasterID'))) {
            $this->db->where('otSlabsMasterID', trim($this->input->post('otSlabsMasterID')));
            $this->db->update('srp_erp_ot_slabsmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Over Time Slab Updating  Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Over Time Slab Updated Successfully.');
                $this->db->trans_commit();
                $this->db->select('CurrencyCode');
                $this->db->where('currencyID', $this->input->post('transactionCurrencyID'));
                $CurrencyCode = $this->db->get('srp_erp_currencymaster')->row('CurrencyCode');
                return array('status' => true, 'last_id' => $this->input->post('otSlabsMasterID'), 'CurrencyCode' => $CurrencyCode);
            }
        } else {
            $this->load->library('sequence');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_ot_slabsmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Over Time Slab Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Over Time Slab Saved Successfully.');
                $this->db->trans_commit();
                $this->db->select('CurrencyCode');
                $this->db->where('currencyID', $this->input->post('transactionCurrencyID'));
                $CurrencyCode = $this->db->get('srp_erp_currencymaster')->row('CurrencyCode');
                return array('status' => true, 'last_id' => $last_id,'CurrencyCode' => $CurrencyCode);
            }
        }
    }

    function fetch_over_time_slab_details()
    {
        $data = array();
        $this->db->select('*');
        $this->db->where('otSlabsMasterID', $this->input->post('otSlabsMasterID'));
        $data['detail'] = $this->db->get('srp_erp_ot_slabdetail')->result_array();
        return $data;
    }

    function laad_over_time_slab_header()
    {
        $this->db->select('otSlabsMasterID,Description,transactionCurrencyID,srp_erp_currencymaster.CurrencyCode as CurrencyCode');
        $this->db->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_ot_slabsmaster.transactionCurrencyID');
        $this->db->where('otSlabsMasterID', $this->input->post('otSlabsMasterID'));
        return $this->db->get('srp_erp_ot_slabsmaster')->row_array();
    }

    function load_sover_time_slab_endhour()
    {
        $this->db->select_max('EndHour');
        $this->db->where('otSlabsMasterID', $this->input->get('otSlabsMasterID'));
        return $this->db->get('srp_erp_ot_slabdetail')->row_array();
    }


    function save_over_time_slab_detail()
    {
        $this->db->trans_start();
        if($this->input->post('EndHour') < $this->input->post('startHour')){
            return array('type' => 'e', 'message' => 'End Hour Should be grater than Start Hour');
        }else{
            $data['startHour'] = $this->input->post('startHour');
            $data['EndHour'] = $this->input->post('EndHour');
            $data['hourlyRate'] = $this->input->post('hourlyRate');
            $data['otSlabsMasterID'] = $this->input->post('otSlabsMasterID');

            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_ot_slabdetail', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('type' => 'e', 'message' => 'Over Time Slab Detail Save Failed');
            } else {
                return array('type' => 's', 'message' => 'Over Time Slab Detail Save Successfully');
            }
        }

    }

    function delete_over_time_slab_detail()
    {
        $this->db->where('otSlabsDetailID', $this->input->post('otSlabsDetailID'));
        $result = $this->db->delete('srp_erp_ot_slabdetail');
        return array('status'=>1,'type'=>'s', 'message'=>'Record Deleted successfully');
    }


}
