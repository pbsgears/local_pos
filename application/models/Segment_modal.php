<?php

class Segment_modal extends ERP_Model
{

    function save_segment()
    {

        $this->db->trans_start();

        $data['description'] = trim($this->input->post('description'));
        $data['segmentCode'] = trim($this->input->post('segmentcode'));
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        if (trim($this->input->post('segmentID')) !='') {
            $this->db->where('segmentID', trim($this->input->post('segmentID')));
            $this->db->update('srp_erp_segment', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Segment Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Segment Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('segmentID'));
            }
        } else {
            $checkExist = $this->db->query("select * from srp_erp_segment where companyID =  " . $this->common_data['company_data']['company_id'] . " AND segmentCode = '" . $this->input->post('segmentcode') . "'")->row_array();
            if (!empty($checkExist)) {
                $this->session->set_flashdata('e', 'Segment Code already exists');
                return array('status' => false);
            } else {

                $this->load->library('sequence');
                $this->db->insert('srp_erp_segment', $data);
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Segment Save Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Segment Saved Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $last_id);
                }
            }
        }


    }

    function edit_segment()
    {
        $this->db->select('*');
        $this->db->where('segmentID', $this->input->post('segmentID'));
        return $this->db->get('srp_erp_segment')->row_array();
    }

    function update_segmentstatus()
    {

        $data['status'] = ($this->input->post('chkedvalue'));
        $this->db->where('segmentID', $this->input->post('segmentID'));
        $result = $this->db->update('srp_erp_segment', $data);
        if ($result) {
            $this->session->set_flashdata('s', 'Records Updated Successfully');
            return true;
        }
    }


}