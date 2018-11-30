<?php
error_reporting(0);
class upload_modal extends CI_Model
{
    function load_attachment(){
        $this->db->select('attachmentID,attachmentDescription, myFileName');
        $this->db->where('documentSystemCode', trim($this->input->post('RequestID')));
        $this->db->where('companyCode', current_companyID());
        $this->db->where('documentID',trim($this->input->post('code')));
        $data['attachments'] = $this->db->get('srp_erp_documentattachments')->result_array();
        $data['uploads_url'] = document_uploads_url();
        return $data;
    }

    function delete_attachment(){
        $this->db->select('attachmentID,attachmentDescription, myFileName');
        $this->db->where('attachmentID', trim($this->input->post('attachmentID')));
        $fileName = $this->db->get('srp_erp_documentattachments')->row_array();
        $this->db->where('attachmentID', trim($this->input->post('attachmentID')));
        $this->db->delete('srp_erp_documentattachments');
        if (trim($this->input->post('attachmentID'))) {
            unlink('uploads/'.$fileName['myFileName']);
            $this->session->set_flashdata('s', 'Purchase Request Attachment Deleted Successfully.'.document_uploads_url().$fileName['myFileName']);
            return true;
        } else {
            $this->session->set_flashdata('e', 'Purchase Request Attachment canot Delete.');
            return false;
        }
    }





}
