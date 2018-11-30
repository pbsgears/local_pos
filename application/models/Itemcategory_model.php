<?php
class Itemcategory_model extends ERP_Model{

    function save_item_category(){
        $data['codePrefix']          = $this->input->post('codeprefix');
        $data['StartSerial']         = $this->input->post('startserial');
        $data['codeLength']          = $this->input->post('codelength');
        $data['description']         = $this->input->post('description');
        $data['itemType']            = $this->input->post('itemtype');
        $data['categoryTypeID']      = $this->input->post('categoryTypeID');
        $data['modifiedPCID']        = $this->common_data['current_pc'];
        $data['modifiedUserID']      = $this->common_data['current_userID'];
        $data['modifiedUserName']    = $this->common_data['current_user'];
        $data['modifiedDateTime']    = $this->common_data['current_date'];

        if($this->input->post('itemcategoryedit')){
            $this->db->where('itemCategoryID', $this->input->post('itemcategoryedit'));
            $this->db->update('srp_erp_itemcategory', $data);
            $this->session->set_flashdata('s', 'Item Category Updated Successfully');
            return true;
        }
        else{
            $data['companyID']                    = $this->common_data['company_data']['company_id'];
            $data['companyCode']                  = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup']             = $this->common_data['user_group'];
            $data['createdPCID']                  = $this->common_data['current_pc'];
            $data['createdUserID']                = $this->common_data['current_userID'];
            $data['createdUserName']              = $this->common_data['current_user'];
            $data['createdDateTime']              = $this->common_data['current_date'];
            $this->db->insert('srp_erp_itemcategory',$data);
            $this->session->set_flashdata('s', 'Item Category Created Successfully');
            return true;
        }
    }

    function edit_itemcategory()
    {
        $this->db->select('*');
        $this->db->where('itemCategoryID', $this->input->post('id'));
        return $this->db->get('srp_erp_itemcategory')->row_array();
    }
}