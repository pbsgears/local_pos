<?php

class Suppliermaster_model extends ERP_Model
{

    function save_supplier_master()
    {
        $this->db->trans_start();
        $isactive = 0;
        if (!empty($this->input->post('isActive'))) {
            $isactive = 1;
        }
        $currency_code = explode('|', trim($this->input->post('currency_code')));
        $liability = fetch_gl_account_desc(trim($this->input->post('liabilityAccount')));
        $data['isActive'] = $isactive;
        $data['secondaryCode'] = trim($this->input->post('suppliercode'));
        $data['supplierName'] = trim($this->input->post('supplierName'));
        $data['supplierCountry'] = trim($this->input->post('suppliercountry'));
        $data['supplierTelephone'] = trim($this->input->post('supplierTelephone'));
        $data['supplierEmail'] = trim($this->input->post('supplierEmail'));
        $data['supplierUrl'] = trim($this->input->post('supplierUrl'));
        $data['supplierFax'] = trim($this->input->post('supplierFax'));
        $data['taxGroupID'] = trim($this->input->post('suppliertaxgroup'));
        $data['vatIdNo'] = trim($this->input->post('vatIdNo'));
        $data['supplierAddress1'] = trim($this->input->post('supplierAddress1'));
        $data['supplierAddress2'] = trim($this->input->post('supplierAddress2'));
        $data['partyCategoryID'] = trim($this->input->post('partyCategoryID'));
        $data['nameOnCheque'] = trim($this->input->post('nameOnCheque'));

        $data['liabilityAutoID'] = $liability['GLAutoID'];
        $data['liabilitySystemGLCode'] = $liability['systemAccountCode'];
        $data['liabilityGLAccount'] = $liability['GLSecondaryCode'];
        $data['liabilityDescription'] = $liability['GLDescription'];
        $data['liabilityType'] = $liability['subCategory'];

        $data['supplierCreditPeriod'] = trim($this->input->post('supplierCreditPeriod'));
        $data['supplierCreditLimit'] = trim($this->input->post('supplierCreditLimit'));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('supplierAutoID'))) {
            $this->db->where('supplierAutoID', trim($this->input->post('supplierAutoID')));
            $this->db->update('srp_erp_suppliermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Supplier : ' . $data['supplierName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Supplier : ' . $data['supplierName'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('supplierAutoID'));
            }
        } else {
            $this->load->library('sequence');
            $data['supplierCurrencyID'] = trim($this->input->post('supplierCurrency'));
            $data['supplierCurrency'] = $currency_code[0];
            $data['supplierCurrencyDecimalPlaces'] = fetch_currency_desimal($data['supplierCurrency']);
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['supplierSystemCode'] = $this->sequence->sequence_generator('SUP');
            $this->db->insert('srp_erp_suppliermaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Supplier : ' . $data['supplierName'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Supplier : ' . $data['supplierName'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function load_supplier_header()
    {
        $this->db->select('supplierFax,partyCategoryID,supplierTelephone,supplierAutoID,supplierSystemCode,supplierName,supplierAddress1,supplierAddress2,supplierEmail,supplierUrl,liabilityAutoID,secondaryCode,supplierCurrency,supplierCreditPeriod,supplierCreditLimit,isActive,liabilityGLAccount,supplierCountry,supplierCurrencyID,taxGroupID,vatIdNo,nameOnCheque');
        $this->db->where('supplierAutoID', $this->input->post('supplierAutoID'));
        return $this->db->get('srp_erp_suppliermaster')->row_array();
    }

    function get_supplier()
    {
        $this->db->select('*');
        $this->db->where('supplierAutoID', $this->input->post('id'));
        return $this->db->get('srp_erp_suppliermaster')->row_array();
    }


    function delete_supplier()
    {
        $this->db->where('supplierAutoID', $this->input->post('supplierAutoID'));
        $result = $this->db->delete('srp_erp_suppliermaster');
        $this->session->set_flashdata('s', 'Record Deleted Successfully');
        return true;
    }

    function saveCategory()
    {
        if (empty($this->input->post('partyCategoryID'))) {

            $this->db->select('partyCategoryID');
            $this->db->where('categoryDescription', $this->input->post('categoryDescription'));
            $this->db->where('partyType', 2);
            $this->db->where('companyID', current_companyID());
            $category = $this->db->get('srp_erp_partycategories')->row_array();
            if (empty($category)) {
                $this->db->set('categoryDescription', $this->input->post('categoryDescription'));
                $this->db->set('partyType', 2);
                $this->db->set('companyID', current_companyID());
                $this->db->set('companyCode', current_companyCode());
                $this->db->set('createdUserGroup', current_user_group());
                $this->db->set('createdPCID', current_pc());
                $this->db->set('createdUserID', current_userID());
                $this->db->set('createdUserID', current_userID());
                $this->db->set('createdUserName', current_user());
                $this->db->set('createdDateTime', $this->common_data['current_date']);
                $result = $this->db->insert('srp_erp_partycategories');

                if ($result) {
                    return array('s', 'Record added successfully');
                } else {
                    return array('e', 'Error in adding Record');
                }
            } else {
                return array('e', 'Category Already Exist');
            }
        } else {
            $this->db->select('partyCategoryID');
            $this->db->where('categoryDescription', $this->input->post('categoryDescription'));
            $this->db->where('partyType', 2);
            $category = $this->db->get('srp_erp_partycategories')->row_array();
            if (empty($category)) {
                $data['categoryDescription'] = $this->input->post('categoryDescription');
                $data['modifiedPCID'] = current_pc();
                $data['modifiedUserID'] = current_userID();
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['modifiedUserName'] = current_user();

                $this->db->where('partyCategoryID', $this->input->post('partyCategoryID'));
                $result = $this->db->update('srp_erp_partycategories', $data);


                if ($result) {
                    return array('s', 'Record Updated successfully');
                } else {
                    return array('e', 'Error in Updating Record');
                }
            } else {
                return array('e', 'Category Already Exist');
            }
        }

    }

    function getCategory()
    {
        $this->db->select('*');
        $this->db->where('partyCategoryID', $this->input->post('partyCategoryID'));
        return $this->db->get('srp_erp_partycategories')->row_array();
    }

    function delete_category()
    {
        $this->db->where('partyCategoryID', $this->input->post('partyCategoryID'));
        $result = $this->db->delete('srp_erp_partycategories');
        if ($result) {
            return array('s', 'Record Deleted successfully');
        }
    }

    function save_bank_detail()
    {
        $this->db->trans_start();
        $supplierBankMasterID= $this->input->post('supplierBankMasterID');
        $data['bankName'] = $this->input->post('bankName');
        $data['currencyID'] = $this->input->post('currencyID');
        $data['accountName'] = $this->input->post('accountName');
        $data['accountNumber'] = $this->input->post('accountNumber');
        $data['swiftCode'] = $this->input->post('swiftCode');
        $data['ibanCode'] = $this->input->post('ibanCode');
        $data['bankAddress'] = $this->input->post('address');
        $data['supplierAutoID'] = $this->input->post('supplierAutoID');
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        if(empty($supplierBankMasterID)) {
            $this->db->insert('srp_erp_supplierBankMaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }else{
            $this->db->where('supplierBankMasterID', $supplierBankMasterID);
            $this->db->update('srp_erp_supplierBankMaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $last_id = $this->db->insert_id();
                $this->session->set_flashdata('s', 'Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }



    }

    function delete_supplierbank()
    {
        $this->db->where('supplierBankMasterID', $this->input->post('supplierBankMasterID'));
        $result = $this->db->delete('srp_erp_supplierBankMaster');
        $this->session->set_flashdata('s', 'Record Deleted Successfully');
        return true;
    }

    function edit_Bank_Details(){
        $this->db->select('*');
        $this->db->where('supplierBankMasterID', $this->input->post('supplierBankMasterID'));
        return $this->db->get('srp_erp_supplierBankMaster')->row_array();
    }

}