<?php

class Suppliermastergroup_model extends ERP_Model
{

    function save_supplier_master()
    {
        $this->db->trans_start();
        $companyid = $this->common_data['company_data']['company_id'];
       /* $this->db->select('companyGroupID');
        $this->db->where('companyID', $companyid);
        $grp = $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid = $companyid;

        $currency_code = explode('|', trim($this->input->post('currency_code')));
        $liability = fetch_gl_account_desc(trim($this->input->post('liabilityAccount')));
        $data['secondaryCode'] = trim($this->input->post('suppliercode'));
        $data['groupSupplierName'] = trim($this->input->post('supplierName'));
        //$data['supplierCountry'] = trim($this->input->post('suppliercountry'));
        // $data['supplierTelephone'] = trim($this->input->post('supplierTelephone'));
        //$data['supplierEmail'] = trim($this->input->post('supplierEmail'));
        //$data['supplierUrl'] = trim($this->input->post('supplierUrl'));
        //$data['supplierFax'] = trim($this->input->post('supplierFax'));
        //$data['taxGroupID'] = trim($this->input->post('suppliertaxgroup'));
        //$data['supplierAddress1'] = trim($this->input->post('supplierAddress1'));
        //$data['supplierAddress2'] = trim($this->input->post('supplierAddress2'));
        $data['partyCategoryID'] = trim($this->input->post('partyCategoryID'));

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

        if (trim($this->input->post('groupSupplierAutoID'))) {
            $this->db->where('groupSupplierAutoID', trim($this->input->post('groupSupplierAutoID')));
            $this->db->update('srp_erp_groupsuppliermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e', 'Supplier Updating  Failed');
            } else {
                return array('s', 'Supplier Updated Successfully');
            }
        } else {
            $this->load->library('sequence');
            $data['isActive'] = 1;
            $data['supplierCurrencyID'] = trim($this->input->post('supplierCurrency'));
            $data['supplierCurrency'] = $currency_code[0];
            $data['supplierCurrencyDecimalPlaces'] = fetch_currency_desimal($data['supplierCurrency']);
            $data['companygroupID'] = $grpid;
            // $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $number = $this->db->query("SELECT IFNULL(MAX(serialNo),0) as serialNo FROM srp_erp_groupsuppliermaster")->row_array();
            $code = (current_companyCode() . '/SUP'. str_pad($number["serialNo"]+1, 6, '0', STR_PAD_LEFT));
            $data['groupSupplierSystemCode'] = $code;
            $data['serialNo'] = $number["serialNo"]+1;
            $this->db->insert('srp_erp_groupsuppliermaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e', 'Supplier Save Failed');
            } else {
                return array('s', 'Supplier Saved Successfully');
            }
        }
    }

    function load_supplier_header()
    {
        $this->db->select('supplierFax,partyCategoryID,supplierTelephone,groupSupplierAutoID,groupSupplierSystemCode,groupSupplierName,supplierAddress1,supplierAddress2,supplierEmail,supplierUrl,liabilityAutoID,secondaryCode,supplierCurrency,supplierCreditPeriod,supplierCreditLimit,isActive,liabilityGLAccount,supplierCountry,supplierCurrencyID,taxGroupID');
        $this->db->where('groupSupplierAutoID', $this->input->post('groupSupplierAutoID'));
        return $this->db->get('srp_erp_groupsuppliermaster')->row_array();
    }

    function get_supplier()
    {
        $this->db->select('*');
        $this->db->where('supplierAutoID', $this->input->post('id'));
        return $this->db->get('srp_erp_suppliermaster')->row_array();
    }


    function delete_supplier()
    {
        $this->db->where('groupSupplierAutoID', $this->input->post('groupSupplierAutoID'));
        $result = $this->db->delete('srp_erp_groupsuppliermaster');
        $this->session->set_flashdata('s', 'Record Deleted Successfully');
        return true;
    }


    function save_supplier_link()
    {
        $companyid = $this->input->post('companyIDgrp');
        $SupplierMasterID = $this->input->post('SupplierMasterID');

        $com = current_companyID();
        /*$this->db->select('companyGroupID');
        $this->db->where('companyID', $com);
        $grp = $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid = $com;

        $this->db->delete('srp_erp_groupsupplierdetails', array('companyGroupID' => $grpid, 'groupSupplierMasterID' => $this->input->post('groupSupplierMasterID')));

        foreach ($companyid as $key => $val) {
            if(!empty($SupplierMasterID[$key])){
                $data['groupSupplierMasterID'] = trim($this->input->post('groupSupplierMasterID'));
                $data['SupplierMasterID'] = trim($SupplierMasterID[$key]);
                $data['companyID'] = trim($val);

                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];

                $data['companyGroupID'] = $grpid;
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];

                $results = $this->db->insert('srp_erp_groupsupplierdetails', $data);
            }
            //$last_id = $this->db->insert_id();
        }
        if ($results) {
            return array('s', 'Supplier Link Saved Successfully');

        } else {
            return array('e', 'Supplier Link Save Failed');
        }
    }

    function delete_supplier_link()
    {
        $this->db->where('groupSupplierDetailID', $this->input->post('groupSupplierDetailID'));
        $result = $this->db->delete('srp_erp_groupsupplierdetails');
        return array('s', 'Record Deleted Successfully');
    }

    function load_supplier_heading()
    {
        $this->db->select('*');
        $this->db->where('groupSupplierAutoID', $this->input->post('groupSupplierMasterID'));
        return $this->db->get('srp_erp_groupsuppliermaster')->row_array();
    }

    function save_supplier_duplicate(){
        $companyid = $this->input->post('checkedCompanies');
        $com = current_companyID();
        $grpid = $com;
        $results='';
        $comparr=array();
        foreach($companyid as $key => $val){
            $i=0;
            $this->db->select('groupSupplierDetailID');
            $this->db->where('groupSupplierMasterID', $this->input->post('supplierAutoIDDuplicatehn'));
            $this->db->where('companyID', $val);
            $this->db->where('companyGroupID', $grpid);
            $linkexsist = $this->db->get('srp_erp_groupsupplierdetails')->row_array();

            $this->db->select('*');
            $this->db->where('groupSupplierAutoID', $this->input->post('supplierAutoIDDuplicatehn'));
            $CurrentCus = $this->db->get('srp_erp_groupsuppliermaster')->row_array();

            $this->db->select('groupPartyCategoryDetailID');
            $this->db->where('partyCategoryID', $CurrentCus['partyCategoryID']);
            $this->db->where('companyID', $val);
            $this->db->where('companyGroupID', $grpid);
            $categorylinkexsist = $this->db->get('srp_erp_grouppartycategorydetails')->row_array();
            if(empty($categorylinkexsist)){
                $i++;
                $companyName = get_companyData($val);
                $this->db->select('categoryDescription');
                $this->db->where('partyCategoryID', $CurrentCus['partyCategoryID']);
                $partyDesc = $this->db->get('srp_erp_grouppartycategories')->row_array();
                array_push($comparr, array("companyname" => $companyName['company_name'], "message" => "Category not linked" ." (".$partyDesc['categoryDescription'].")" ));
            }
            $this->db->select('groupChartofAccountDetailID');
            $this->db->where('chartofAccountID', $CurrentCus['liabilityAutoID']);
            $this->db->where('companyID', $val);
            $this->db->where('companyGroupID', $grpid);
            $categoryCOAexsist = $this->db->get('srp_erp_groupchartofaccountdetails')->row_array();

            if(empty($categoryCOAexsist)){
                $i++;
                $companyName = get_companyData($val);
                $this->db->select('GLSecondaryCode');
                $this->db->where('GLAutoID', $CurrentCus['liabilityAutoID']);
                $glDesc = $this->db->get('srp_erp_groupchartofaccounts')->row_array();
                array_push($comparr, array("companyname" => $companyName['company_name'], "message" => "Chart of Account not linked" ." (".$glDesc['GLSecondaryCode'].")" ));
            }




            $this->db->select('supplierAutoID');
            $this->db->where('supplierName', $CurrentCus['groupSupplierName']);
            $this->db->where('companyID', $val);
            $CurrentCOAexsist = $this->db->get('srp_erp_suppliermaster')->row_array();
            if($i==0){
                if(empty($linkexsist) && empty($CurrentCOAexsist)){
                    $data['isActive'] = 1;
                    $data['secondaryCode'] = $CurrentCus['secondaryCode'];
                    $data['supplierName'] = $CurrentCus['supplierName'];
                    $data['supplierCountry'] = $CurrentCus['supplierCountry'];
                    $data['supplierTelephone'] = $CurrentCus['supplierTelephone'];
                    $data['supplierEmail'] = $CurrentCus['supplierEmail'];
                    $data['supplierUrl'] = $CurrentCus['supplierUrl'];
                    $data['supplierFax'] = $CurrentCus['supplierFax'];
                    $data['supplierAddress1'] = $CurrentCus['supplierAddress1'];
                    $data['supplierAddress2'] = $CurrentCus['supplierAddress2'];
                    $data['taxGroupID'] = $CurrentCus['taxGroupID'];
                    $data['vatIdNo'] = $CurrentCus['vatIdNo'];
                    $data['partyCategoryID'] = $CurrentCus['partyCategoryID'];
                    $data['liabilityAutoID'] = $CurrentCus['liabilityAutoID'];
                    $data['liabilitySystemGLCode'] = $CurrentCus['liabilitySystemGLCode'];
                    $data['liabilityGLAccount'] = $CurrentCus['liabilityGLAccount'];
                    $data['liabilityDescription'] = $CurrentCus['liabilityDescription'];
                    $data['liabilityType'] = $CurrentCus['liabilityType'];
                    $data['supplierCreditPeriod'] = $CurrentCus['supplierCreditPeriod'];
                    $data['supplierCreditLimit'] = $CurrentCus['supplierCreditLimit'];
                    $data['modifiedPCID'] = $this->common_data['current_pc'];
                    $data['modifiedUserID'] = $this->common_data['current_userID'];
                    $data['modifiedUserName'] = $this->common_data['current_user'];
                    $data['modifiedDateTime'] = $this->common_data['current_date'];
                    $this->load->library('sequence');
                    $data['supplierCurrencyID'] = $CurrentCus['supplierCurrencyID'];
                    $data['supplierCurrency'] = $CurrentCus['supplierCurrency'];
                    $data['supplierCurrencyDecimalPlaces'] = $CurrentCus['supplierCurrencyDecimalPlaces'];
                    $data['companyID'] = $val;
                    $companyCode = get_companyData($val);
                    $data['companyCode'] = $companyCode['company_code'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $data['supplierSystemCode'] = $this->sequence->sequence_generator('CUS');
                    $this->db->insert('srp_erp_suppliermaster', $data);
                    $last_id = $this->db->insert_id();


                    $dataLink['groupSupplierMasterID'] = trim($this->input->post('supplierAutoIDDuplicatehn'));
                    $dataLink['SupplierMasterID'] = trim($last_id);
                    $dataLink['companyID'] = trim($val);
                    $dataLink['companyGroupID'] = $grpid;

                    $dataLink['createdPCID'] = $this->common_data['current_pc'];
                    $dataLink['createdUserID'] = $this->common_data['current_userID'];
                    $dataLink['createdUserName'] = $this->common_data['current_user'];
                    $dataLink['createdDateTime'] = $this->common_data['current_date'];

                    $results = $this->db->insert('srp_erp_groupsupplierdetails', $dataLink);

                }
            }else{
                continue;
            }

        }

        if ($results) {
            return array('s', 'Supplier Replicated Successfully',$comparr);
        } else {
            return array('e', 'Supplier Replication not successful',$comparr);
        }

    }


}