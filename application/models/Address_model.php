<?php
class Address_model extends ERP_Model{

    function save_address()
    {
        $data['companyID']              = $this->common_data['company_data']['company_id'];
        $data['companyCode']            = $this->common_data['company_data']['company_code'];
        $data['contactPerson']          = $this->input->post('addresstypeid');
        $data['addressTypeID']          = $this->input->post('addresstypeid');
        $data['addressDescription']      = $this->input->post('addressdescription');
        $data['contactPerson']          = $this->input->post('contactpersonid');
        $data['contactPersonTelephone'] = $this->input->post('contactpersontelephone');
        $data['contactPersonFaxNo']     = $this->input->post('contactpersonfaxno');
        $data['contactPersonEmail']     = $this->input->post('contactpersonemail');
        $data['addressType']            = $this->input->post('addressType');
        if (!$this->input->post('addressedit')) {         
            $result = $this->db->insert('srp_erp_address',$data);
            $this->session->set_flashdata('s', 'Address Added Successfully');
            return true;
        } else {
            $this->db->where('addressID', $this->input->post('addressedit'));
            $result = $this->db->update('srp_erp_address',$data);
            if ($result) {
                $this->session->set_flashdata('s', 'Records Updated Successfully');
                return true;
            }
        }
    }

    function edit_address()
    {
        $this->db->select('*');
        $this->db->where('addressID', $this->input->post('id'));
        return $this->db->get('srp_erp_address')->row_array();
    }

    function delete_address()
    {
        $this->db->where('addressID', $this->input->post('id'));
        $result= $this->db->delete('srp_erp_address');
        if ($result) {
            $this->session->set_flashdata('s', 'Records Deleted Successfully');
            return true;
        }
    }
}