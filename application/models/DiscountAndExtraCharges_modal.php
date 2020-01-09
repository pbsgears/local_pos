<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class DiscountAndExtraCharges_modal extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function saveDiscountCategory(){
        $this->db->trans_start();
        $type=$this->input->post('type');
        $isChargeToExpenseval=$this->input->post('isChargeToExpenseval');
        $isTaxApplicableval=$this->input->post('isTaxApplicableval');
        $Description=$this->input->post('Description');
        $glCode=$this->input->post('glCode');

        $data['type'] = $type;
        $data['Description'] = $Description;
        $data['isChargeToExpense'] = $isChargeToExpenseval;
        $data['isTaxApplicable'] = $isTaxApplicableval;
        if($isChargeToExpenseval==0 && $type==1){
            $data['glCode'] = null;
        }else{
            $data['glCode'] = $glCode;
        }
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        if (trim($this->input->post('discountExtraChargeID'))) {
            $this->db->where('discountExtraChargeID', trim($this->input->post('discountExtraChargeID')));
            $this->db->update('srp_erp_discountextracharges', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Discount Updating Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Discount Updated Successfully.');
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_discountextracharges', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Discount Save  Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Discount Saved Successfully.');
            }
        }
    }

    function delete_discount_category(){
        $this->db->delete('srp_erp_discountextracharges', array('discountExtraChargeID' => trim($this->input->post('discountExtraChargeID'))));
        return array('s', 'Deleted Successfully.');
    }

    function getDiscount(){
        $this->db->select('*');
        $this->db->where('discountExtraChargeID', trim($this->input->post('discountExtraChargeID')));
        $this->db->from('srp_erp_discountextracharges');
        return $this->db->get()->row_array();
    }

}
