<?php
class Chart_of_acconts_group_model extends ERP_Model
{

    function save_chart_of_accont()
    {
        $this->db->trans_start();
        $controlAccountUpdate = $this->input->post('controlAccountUpdate');
        $companyID = $this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $Grpid = $companyID;
        if ($controlAccountUpdate == 0) {  //if not control account update
            $isActive = 0;
            if (!empty($this->input->post('isActive'))) {
                $isActive = 1;
            }
            $account_type = explode('|', trim($this->input->post('account_type')));
            $data['accountCategoryTypeID'] = trim($this->input->post('accountCategoryTypeID'));
            $data['masterCategory'] = trim($account_type[0]);
            $data['subCategory'] = trim($account_type[1]);
            $data['CategoryTypeDescription'] = trim($account_type[2]);

            $data['masterAutoID'] = trim($this->input->post('masterAccount'));
            $data['isBank'] = trim($this->input->post('isBank'));
            $data['isCard'] = trim($this->input->post('isCard'));
            $data['isCash'] = trim($this->input->post('isCash'));
            if ($data['isCash'] == 1) {
                $data['bankAccountNumber'] = 'N/A';
                $data['bankName'] = trim($this->input->post('GLDescription'));
                $data['bankBranch'] = '-';
            } else {
                $data['bankAccountNumber'] = trim($this->input->post('bankAccountNumber'));
                $data['bankName'] = trim($this->input->post('bankName'));
                $data['bankBranch'] = trim($this->input->post('bank_branch'));
            }


            $data['bankSwiftCode'] = trim($this->input->post('bank_swift_code'));
            $data['bankCheckNumber'] = trim($this->input->post('bankCheckNumber'));
            $data['masterAccountYN'] = trim($this->input->post('masterAccountYN'));
            $data['bankCurrencyCode'] = trim($this->input->post('bankCurrencyCode'));
            /*if currencyCode set get currencyID*/
            if ($data['bankCurrencyCode'] != '') {
                $data['bankCurrencyID'] = fetch_currency_ID($data['bankCurrencyCode']);
            } else {
                $data['bankCurrencyID'] = '';
            }

            if ($data['isCash']) {
                $data['bankCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            }

            if ($data['masterAccountYN'] == 1) {
                $data['masterAccount'] = '';
                $data['masterAccountDescription'] = '';
            } else {
                $master_account = explode('|', trim($this->input->post('masterAccount_dec')));
                $data['masterAccount'] = trim($master_account[0]);
                $data['masterAccountDescription'] = trim($master_account[2]);
            }
            $data['approvedYN'] = 1;
            $data['isActive'] = $isActive;
        }
        $data['GLSecondaryCode'] = trim($this->input->post('GLSecondaryCode'));
        $data['GLDescription'] = trim($this->input->post('GLDescription'));

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        if (trim($this->input->post('GLAutoID'))) {
            $this->db->where('GLAutoID', trim($this->input->post('GLAutoID')));
            $this->db->update('srp_erp_groupchartofaccounts', $data);
            if ($controlAccountUpdate == 1) { /*conreol account = 1 update srp_erp_companycontrolaccounts */
                $this->db->update('srp_erp_companycontrolaccounts', array(
                    'GLSecondaryCode' => $data['GLSecondaryCode'],
                    'GLDescription' => $data['GLDescription']
                ), array('GLAutoID' => trim($this->input->post('GLAutoID'))));
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Ledger : ' . $data['GLDescription'] . ' Update Failed ');
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Ledger : ' . $data['GLDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('GLAutoID'));
            }
        } else {
            $this->load->library('sequence');
            $this->load->library('approvals');
            $data['isActive'] = 1;
            $data['groupID'] = $Grpid;
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $number = $this->db->query("SELECT IFNULL(MAX(serialNo),0) as serialNo FROM srp_erp_groupchartofaccounts")->row_array();
            $code = (current_companyCode() . '/' . $data['subCategory'] . str_pad($number["serialNo"] + 1, 6, '0', STR_PAD_LEFT));
            $data['systemAccountCode'] = $code;
            $data['serialNo'] = $number["serialNo"] + 1;
            $data['approvedYN'] = 1;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];
            $data['approvedComment'] = 'Auto approved';
            $data['confirmedYN'] = 1;
            $data['confirmedDate'] = $this->common_data['current_date'];
            $data['confirmedbyEmpID'] = $this->common_data['current_userID'];
            $data['confirmedbyName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_groupchartofaccounts', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Ledger  : ' . $data['GLDescription'] . ' Save Failed ');
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('w', '');
                $this->session->set_flashdata('s', 'Ledger : ' . $data['GLDescription'] . ' Added Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function load_chart_of_accont_header()
    {
        $this->db->select('*');
        $this->db->where('GLAutoID', $this->input->post('GLAutoID'));
        return $this->db->get('srp_erp_groupchartofaccounts')->row_array();
    }

    function fetch_master_account()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $Grpid = $companyID;
        $this->db->select('GLSecondaryCode,GLDescription,systemAccountCode,GLSecondaryCode,GLAutoID');
        $this->db->where('accountCategoryTypeID', trim($this->input->post('accountCategoryTypeID')));
        $this->db->where('subCategory', trim($this->input->post('subCategory')));
        $this->db->where('masterAccountYN', 1);
        $this->db->where('GLAutoID<>', trim($this->input->post('GLAutoID')));
        //$this->db->where('(masterAccountYN = 1 or  controllAccountYN = 1)');
        $this->db->where('groupID', $Grpid);
        return $this->db->get('srp_erp_groupchartofaccounts')->result_array();
    }

    function delete_chart_of_accont()
    {
        $this->db->where('GLAutoID', $this->input->post('GLAutoID'));
        $result = $this->db->delete('srp_erp_chartofaccounts');
        $this->session->set_flashdata('s', 'Record Deleted Successfully');
        return true;
    }

    function fetch_cheque_number()
    {
        $this->db->select('bankCheckNumber,isCash');
        $this->db->where('GLAutoID', $this->input->post('GLAutoID'));
        return $this->db->get('srp_erp_chartofaccounts')->row_array();
    }

    function save_chart_link()
    {
        $companyid = $this->input->post('companyIDgrp');
        $chartofAccountID = $this->input->post('chartofAccountID');
        $com = current_companyID();
        $masterGroupID=getParentgroupMasterID();
        /*$this->db->select('companyGroupID');
        $this->db->where('companyID', $com);
        $grp = $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid = $com;
        $results = true;
        $this->db->delete('srp_erp_groupchartofaccountdetails', array('companyGroupID' => $grpid, 'groupChartofAccountMasterID' => $this->input->post('GLAutoIDhn')));
        if (!empty($companyid)) {
            foreach ($companyid as $key => $val) {
                if (!empty($chartofAccountID[$key])) {
                    $data['groupChartofAccountMasterID'] = trim($this->input->post('GLAutoIDhn'));
                    $data['chartofAccountID'] = trim($chartofAccountID[$key]);
                    $data['companyID'] = trim($val);
                    $data['companyGroupID'] = $masterGroupID;

                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];

                    $results = $this->db->insert('srp_erp_groupchartofaccountdetails', $data);
                }
                //$last_id = $this->db->insert_id();
                //echo $this->db->last_query();
            }
        }


        if ($results) {
            return array('s', 'Chart of account Link Saved Successfully');
        } else {
            return array('e', 'Chart of account Link Save Failed');
        }
    }

    function delete_chart_link()
    {
        $this->db->where('groupChartofAccountDetailID', $this->input->post('groupChartofAccountDetailID'));
        $result = $this->db->delete('srp_erp_groupchartofaccountdetails');
        return array('s', 'Record Deleted Successfully');
    }

    function save_chart_duplicate()
    {
        $companyid = $this->input->post('checkedCompanies');
        //$chartofAccountID = $this->input->post('chartofAccountID');
        $masterAccountYN = $this->input->post('masterAccountYNhn');
        $com = current_companyID();
        $grpid = $com;
        $masterGroupID=getParentgroupMasterID();
        $comparr=array();
        if ($masterAccountYN == 1) {
            $results = '';
            foreach ($companyid as $key => $val) {
                $i=0;
                $this->db->select('groupChartofAccountDetailID');
                $this->db->where('groupChartofAccountMasterID', $this->input->post('GLAutoIDDuplicatehn'));
                $this->db->where('companyID', $val);
                $this->db->where('companyGroupID', $masterGroupID);
                $linkexsist = $this->db->get('srp_erp_groupchartofaccountdetails')->row_array();

                $this->db->select('*');
                $this->db->where('GLAutoID', $this->input->post('GLAutoIDDuplicatehn'));
                $CurrentCOA = $this->db->get('srp_erp_groupchartofaccounts')->row_array();

                $this->db->select('GLAutoID');
                $this->db->where('GLSecondaryCode', $CurrentCOA['GLSecondaryCode']);
                $this->db->where('companyID', $val);
                $CurrentCOAexsist = $this->db->get('srp_erp_chartofaccounts')->row_array();

                if (!empty($CurrentCOAexsist)) {
                    $i++;
                    $companyName = get_companyData($val);
                    array_push($comparr, array("companyname" => $companyName['company_name'], "message" => "Chart of account name already exist" . " (" . $CurrentCOA['GLSecondaryCode'] . ")"));
                }
                if($i==0){
                if (empty($linkexsist) && empty($CurrentCOAexsist)) {
                    $data['accountCategoryTypeID'] = $CurrentCOA['accountCategoryTypeID'];
                    $data['masterCategory'] = $CurrentCOA['masterCategory'];
                    $data['subCategory'] = $CurrentCOA['subCategory'];
                    $data['CategoryTypeDescription'] = $CurrentCOA['CategoryTypeDescription'];
                    $data['masterAutoID'] = $CurrentCOA['masterAutoID'];
                    $data['isBank'] = $CurrentCOA['isBank'];
                    $data['isCard'] = $CurrentCOA['isCard'];
                    $data['isCash'] = $CurrentCOA['isCash'];
                    $data['bankAccountNumber'] = $CurrentCOA['bankAccountNumber'];
                    $data['bankName'] = $CurrentCOA['bankName'];
                    $data['bankBranch'] = $CurrentCOA['bankBranch'];
                    $data['bankSwiftCode'] = $CurrentCOA['bankSwiftCode'];
                    $data['bankCheckNumber'] = $CurrentCOA['bankCheckNumber'];
                    $data['masterAccountYN'] = $CurrentCOA['masterAccountYN'];
                    $data['bankCurrencyCode'] = $CurrentCOA['bankCurrencyCode'];
                    $data['bankCurrencyID'] = $CurrentCOA['bankCurrencyID'];
                    $data['masterAccount'] = $CurrentCOA['masterAccount'];
                    $data['masterAccountDescription'] = $CurrentCOA['masterAccountDescription'];
                    $data['approvedYN'] = 1;
                    $data['GLSecondaryCode'] = $CurrentCOA['GLSecondaryCode'];
                    $data['GLDescription'] = $CurrentCOA['GLDescription'];
                    $data['modifiedPCID'] = $this->common_data['current_pc'];
                    $data['modifiedUserID'] = $this->common_data['current_userID'];
                    $data['modifiedUserName'] = $this->common_data['current_user'];
                    $data['modifiedDateTime'] = $this->common_data['current_date'];

                    $this->load->library('sequence');
                    $this->load->library('approvals');
                    $data['isActive'] = 1;
                    $data['companyID'] = $val;
                    $companyCode = get_companyData($val);
                    $data['companyCode'] = $companyCode['company_code'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $data['systemAccountCode'] = $this->sequence->sequence_generator_group($data['subCategory'], 0, $val, $companyCode['company_code']);
                    $data['approvedYN'] = 1;
                    $data['approvedbyEmpID'] = $this->common_data['current_userID'];
                    $data['approvedbyEmpName'] = $this->common_data['current_user'];
                    $data['approvedDate'] = $this->common_data['current_date'];
                    $data['approvedComment'] = 'Auto approved';
                    $data['confirmedYN'] = 1;
                    $data['confirmedDate'] = $this->common_data['current_date'];
                    $data['confirmedbyEmpID'] = $this->common_data['current_userID'];
                    $data['confirmedbyName'] = $this->common_data['current_user'];
                    $this->db->insert('srp_erp_chartofaccounts', $data);
                    $last_id = $this->db->insert_id();

                    $dataLink['groupChartofAccountMasterID'] = trim($this->input->post('GLAutoIDDuplicatehn'));
                    $dataLink['chartofAccountID'] = trim($last_id);
                    $dataLink['companyID'] = trim($val);
                    $dataLink['companyGroupID'] = $masterGroupID;

                    $dataLink['createdPCID'] = $this->common_data['current_pc'];
                    $dataLink['createdUserID'] = $this->common_data['current_userID'];
                    $dataLink['createdUserName'] = $this->common_data['current_user'];
                    $dataLink['createdDateTime'] = $this->common_data['current_date'];

                    $results = $this->db->insert('srp_erp_groupchartofaccountdetails', $dataLink);

                }
            }else{
                continue;
            }
            }

            if ($results) {
                return array('s', 'Chart of account Saved Successfully');
            } else {
                return array('e', 'Chart of account exist already');
            }

        } else {
            $results = '';
            foreach ($companyid as $key => $val) {
                $x=0;
                $this->db->select('masterAutoID');
                $this->db->where('GLAutoID', $this->input->post('GLAutoIDDuplicatehn'));
                $groupmasterautoID = $this->db->get('srp_erp_groupchartofaccounts')->row_array();

                $this->db->select('groupChartofAccountDetailID');
                $this->db->where('groupChartofAccountMasterID', $groupmasterautoID['masterAutoID']);
                $this->db->where('companyID', $val);
                $this->db->where('companyGroupID', $masterGroupID);
                $masterlinkexsist = $this->db->get('srp_erp_groupchartofaccountdetails')->row_array();

                $this->db->select('*');
                $this->db->where('GLAutoID', $groupmasterautoID['masterAutoID']);
                $CurrentCOAMaster = $this->db->get('srp_erp_groupchartofaccounts')->row_array();

                $this->db->select('groupChartofAccountDetailID');
                $this->db->where('groupChartofAccountMasterID', $this->input->post('GLAutoIDDuplicatehn'));
                $this->db->where('companyID', $val);
                $this->db->where('companyGroupID', $masterGroupID);
                $linkexsist = $this->db->get('srp_erp_groupchartofaccountdetails')->row_array();

                $this->db->select('*');
                $this->db->where('GLAutoID', $this->input->post('GLAutoIDDuplicatehn'));
                $CurrentCOA = $this->db->get('srp_erp_groupchartofaccounts')->row_array();

                $this->db->select('GLAutoID');
                $this->db->where('GLSecondaryCode', $CurrentCOA['GLSecondaryCode']);
                $this->db->where('companyID', $val);
                $CurrentCOAexsist = $this->db->get('srp_erp_chartofaccounts')->row_array();

                if (!empty($CurrentCOAexsist)) {
                    $x++;
                    $companyName = get_companyData($val);
                    array_push($comparr, array("companyname" => $companyName['company_name'], "message" => "Chart of account name already exist" . " (" . $CurrentCOA['GLSecondaryCode'] . ")"));
                }

                if($x==0){
                if (empty($masterlinkexsist)) {
                    if (empty($CurrentCOAexsist)){
                        $dataM['accountCategoryTypeID'] = $CurrentCOAMaster['accountCategoryTypeID'];
                        $dataM['masterCategory'] = $CurrentCOAMaster['masterCategory'];
                        $dataM['subCategory'] = $CurrentCOAMaster['subCategory'];
                        $dataM['CategoryTypeDescription'] = $CurrentCOAMaster['CategoryTypeDescription'];
                        $dataM['masterAutoID'] = $CurrentCOAMaster['masterAutoID'];
                        $dataM['isBank'] = $CurrentCOAMaster['isBank'];
                        $dataM['isCard'] = $CurrentCOAMaster['isCard'];
                        $dataM['isCash'] = $CurrentCOAMaster['isCash'];
                        $dataM['bankAccountNumber'] = $CurrentCOAMaster['bankAccountNumber'];
                        $dataM['bankName'] = $CurrentCOAMaster['bankName'];
                        $dataM['bankBranch'] = $CurrentCOAMaster['bankBranch'];
                        $dataM['bankSwiftCode'] = $CurrentCOAMaster['bankSwiftCode'];
                        $dataM['bankCheckNumber'] = $CurrentCOAMaster['bankCheckNumber'];
                        $dataM['masterAccountYN'] = $CurrentCOAMaster['masterAccountYN'];
                        $dataM['bankCurrencyCode'] = $CurrentCOAMaster['bankCurrencyCode'];
                        $dataM['bankCurrencyID'] = $CurrentCOAMaster['bankCurrencyID'];
                        $dataM['masterAccount'] = $CurrentCOAMaster['masterAccount'];
                        $dataM['masterAccountDescription'] = $CurrentCOAMaster['masterAccountDescription'];
                        $dataM['approvedYN'] = 1;
                        $dataM['GLSecondaryCode'] = $CurrentCOAMaster['GLSecondaryCode'];
                        $dataM['GLDescription'] = $CurrentCOAMaster['GLDescription'];
                        $dataM['modifiedPCID'] = $this->common_data['current_pc'];
                        $dataM['modifiedUserID'] = $this->common_data['current_userID'];
                        $dataM['modifiedUserName'] = $this->common_data['current_user'];
                        $dataM['modifiedDateTime'] = $this->common_data['current_date'];

                        $this->load->library('sequence');
                        $this->load->library('approvals');
                        $dataM['isActive'] = 1;
                        $dataM['companyID'] = $val;
                        $companyCode = get_companyData($val);
                        $dataM['companyCode'] = $companyCode['company_code'];
                        $dataM['createdUserGroup'] = $this->common_data['user_group'];
                        $dataM['createdPCID'] = $this->common_data['current_pc'];
                        $dataM['createdUserID'] = $this->common_data['current_userID'];
                        $dataM['createdUserName'] = $this->common_data['current_user'];
                        $dataM['createdDateTime'] = $this->common_data['current_date'];
                        $dataM['systemAccountCode'] = $this->sequence->sequence_generator_group($dataM['subCategory'], 0, $val, $companyCode['company_code']);
                        $dataM['approvedYN'] = 1;
                        $dataM['approvedbyEmpID'] = $this->common_data['current_userID'];
                        $dataM['approvedbyEmpName'] = $this->common_data['current_user'];
                        $dataM['approvedDate'] = $this->common_data['current_date'];
                        $dataM['approvedComment'] = 'Auto approved';
                        $dataM['confirmedYN'] = 1;
                        $dataM['confirmedDate'] = $this->common_data['current_date'];
                        $dataM['confirmedbyEmpID'] = $this->common_data['current_userID'];
                        $dataM['confirmedbyName'] = $this->common_data['current_user'];
                        $this->db->insert('srp_erp_chartofaccounts', $dataM);
                        $last_idM = $this->db->insert_id();
                    }else{
                        $last_idM =  $CurrentCOAexsist['GLAutoID'];
                    }


                    $dataLinkM['groupChartofAccountMasterID'] = trim($groupmasterautoID['masterAutoID']);
                    $dataLinkM['chartofAccountID'] = trim($last_idM);
                    $dataLinkM['companyID'] = trim($val);
                    $dataLinkM['companyGroupID'] = $masterGroupID;

                    $dataLinkM['createdPCID'] = $this->common_data['current_pc'];
                    $dataLinkM['createdUserID'] = $this->common_data['current_userID'];
                    $dataLinkM['createdUserName'] = $this->common_data['current_user'];
                    $dataLinkM['createdDateTime'] = $this->common_data['current_date'];

                    $this->db->insert('srp_erp_groupchartofaccountdetails', $dataLinkM);
                }

                $this->db->select('chartofAccountID');
                $this->db->where('groupChartofAccountMasterID', $groupmasterautoID['masterAutoID']);
                $this->db->where('companyID', $val);
                $this->db->where('companyGroupID', $masterGroupID);
                $masterAutoID = $this->db->get('srp_erp_groupchartofaccountdetails')->row_array();

                $this->db->select('systemAccountCode,GLDescription');
                $this->db->where('GLAutoID', $masterAutoID['chartofAccountID']);
                $masterAccount = $this->db->get('srp_erp_chartofaccounts')->row_array();


                if (empty($linkexsist) && empty($CurrentCOAexsist)) {
                    $data['accountCategoryTypeID'] = $CurrentCOA['accountCategoryTypeID'];
                    $data['masterCategory'] = $CurrentCOA['masterCategory'];
                    $data['subCategory'] = $CurrentCOA['subCategory'];
                    $data['CategoryTypeDescription'] = $CurrentCOA['CategoryTypeDescription'];
                    $data['masterAutoID'] = $masterAutoID['chartofAccountID'];
                    $data['isBank'] = $CurrentCOA['isBank'];
                    $data['isCard'] = $CurrentCOA['isCard'];
                    $data['isCash'] = $CurrentCOA['isCash'];
                    $data['bankAccountNumber'] = $CurrentCOA['bankAccountNumber'];
                    $data['bankName'] = $CurrentCOA['bankName'];
                    $data['bankBranch'] = $CurrentCOA['bankBranch'];
                    $data['bankSwiftCode'] = $CurrentCOA['bankSwiftCode'];
                    $data['bankCheckNumber'] = $CurrentCOA['bankCheckNumber'];
                    $data['masterAccountYN'] = $CurrentCOA['masterAccountYN'];
                    $data['bankCurrencyCode'] = $CurrentCOA['bankCurrencyCode'];
                    $data['bankCurrencyID'] = $CurrentCOA['bankCurrencyID'];
                    $data['masterAccount'] = $masterAccount['systemAccountCode'];
                    $data['masterAccountDescription'] = $masterAccount['GLDescription'];
                    $data['approvedYN'] = 1;
                    $data['GLSecondaryCode'] = $CurrentCOA['GLSecondaryCode'];
                    $data['GLDescription'] = $CurrentCOA['GLDescription'];
                    $data['modifiedPCID'] = $this->common_data['current_pc'];
                    $data['modifiedUserID'] = $this->common_data['current_userID'];
                    $data['modifiedUserName'] = $this->common_data['current_user'];
                    $data['modifiedDateTime'] = $this->common_data['current_date'];

                    $this->load->library('sequence');
                    $this->load->library('approvals');
                    $data['isActive'] = 1;
                    $data['companyID'] = $val;
                    $companyCode = get_companyData($val);
                    $data['companyCode'] = $companyCode['company_code'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $data['systemAccountCode'] = $this->sequence->sequence_generator_group($data['subCategory'], 0, $val, $companyCode['company_code']);
                    $data['approvedYN'] = 1;
                    $data['approvedbyEmpID'] = $this->common_data['current_userID'];
                    $data['approvedbyEmpName'] = $this->common_data['current_user'];
                    $data['approvedDate'] = $this->common_data['current_date'];
                    $data['approvedComment'] = 'Auto approved';
                    $data['confirmedYN'] = 1;
                    $data['confirmedDate'] = $this->common_data['current_date'];
                    $data['confirmedbyEmpID'] = $this->common_data['current_userID'];
                    $data['confirmedbyName'] = $this->common_data['current_user'];
                    $this->db->insert('srp_erp_chartofaccounts', $data);
                    $last_id = $this->db->insert_id();

                    $dataLink['groupChartofAccountMasterID'] = trim($this->input->post('GLAutoIDDuplicatehn'));
                    $dataLink['chartofAccountID'] = trim($last_id);
                    $dataLink['companyID'] = trim($val);
                    $dataLink['companyGroupID'] = $masterGroupID;

                    $dataLink['createdPCID'] = $this->common_data['current_pc'];
                    $dataLink['createdUserID'] = $this->common_data['current_userID'];
                    $dataLink['createdUserName'] = $this->common_data['current_user'];
                    $dataLink['createdDateTime'] = $this->common_data['current_date'];

                    $results = $this->db->insert('srp_erp_groupchartofaccountdetails', $dataLink);

                }
            }else{
                continue;
            }
            }

            if ($results) {
                return array('s', 'Chart of account Saved Successfully');
            } else {
                return array('e', 'Chart of account exist already');
            }

        }


    }

    function fetch_chartofaccount_details()
    {
       $GlAutoid =  trim($this->input->post('GLAutoid'));
        $data['chartofacccountdetails'] = $this->db->query("SELECT 
*
FROM 
srp_erp_groupchartofaccounts 

where 
GLAutoID = $GlAutoid ")->row_array();
        return $data;
    }

}