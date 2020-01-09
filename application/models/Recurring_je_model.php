<?php

class Recurring_je_model extends ERP_Model
{

    function save_recurring_journal_entry_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $Jdates = $this->input->post('RJVStartDate');
        $RJVStartDate = input_format_date($Jdates,$date_format_policy);

        $EJdates = $this->input->post('RJVEndDate');
        $RJVEndDate = input_format_date($EJdates,$date_format_policy);

        //$period = explode('|', trim($this->input->post('financeyear_period')));
        $currency_code = explode('|', trim($this->input->post('currency_code')));


        $data['documentID'] = 'RJV';
        $data['RJVStartDate'] = trim($RJVStartDate);
        $data['RJVEndDate'] = trim($RJVEndDate);
        $data['RJVNarration'] = trim_desc($this->input->post('RJVNarration'));
        $data['referenceNo'] = trim($this->input->post('referenceNo'));

        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID'));
        $data['transactionCurrency'] = trim($currency_code[0]);
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];

        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('RJVMasterAutoId'))) {
            $this->db->where('RJVMasterAutoId', trim($this->input->post('RJVMasterAutoId')));
            $this->db->update('srp_erp_recurringjvmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Recurring Journal Entry' . $data['RJVNarration'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Recurring Journal Entry ' . $data['RJVNarration'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('RJVMasterAutoId'));
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
            $data['RJVcode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_recurringjvmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Recurring Journal Entry ' . $data['RJVNarration'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Recurring Journal Entry' . $data['RJVNarration'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_gl_detail()
    {
        $projectExist = project_is_exist();
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('RJVMasterAutoId', $this->input->post('RJVMasterAutoId'));
        $master = $this->db->get('srp_erp_recurringjvmaster')->row_array();

        $gl_codes = $this->input->post('gl_code');
        $gl_code_des = $this->input->post('gl_code_des');
        /*$gl_types = $this->input->post('gl_type');*/
        $debitAmount = $this->input->post('debitAmount');
        $creditAmount = $this->input->post('creditAmount');
        $descriptions = $this->input->post('description');
        $segment_gls = $this->input->post('segment_gl');
        $projectID = $this->input->post('projectID');

        foreach ($gl_codes as $key => $gl_code) {
            $segment = explode('|', $segment_gls[$key]);
            $gldata = fetch_gl_account_desc($gl_codes[$key]);

            if ($gldata['masterCategory'] == 'PL') {
                $data[$key]['segmentID'] = trim($segment[0]);
                $data[$key]['segmentCode'] = trim($segment[1]);
            }else{
             /*   $data[$key]['segmentID'] = trim($segment[0]);
                $data[$key]['segmentCode'] = trim($segment[1]);*/
                $data[$key]['segmentID'] = null;
                $data[$key]['segmentCode'] = null;
            }

            if($projectExist == 1){
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'],$projectCurrency);
                $data[$key]['projectID'] = $projectID[$key];
                $data[$key]['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }

            $gl_des = explode('|', $gl_code_des[$key]);
            $data[$key]['RJVMasterAutoId'] = trim($this->input->post('RJVMasterAutoId'));
            $data[$key]['GLAutoID'] = $gl_codes[$key];
            $data[$key]['systemGLCode'] = trim($gl_des[0]);
            $data[$key]['GLCode'] = trim($gl_des[1]);
            $data[$key]['GLDescription'] = trim($gl_des[2]);
            $data[$key]['GLType'] = trim($gl_des[3]);
            $data[$key]['projectID'] = $projectID[$key];

            if ($creditAmount[$key] > 0) {
                $data[$key]['gl_type'] = 'Cr';
            } else {
                $data[$key]['gl_type'] = 'Dr';
            }

            if ($data[$key]['gl_type'] == 'Cr') {
                $data[$key]['creditAmount'] = round( $creditAmount[$key], $master['transactionCurrencyDecimalPlaces']);
                $creditCompanyLocalAmount=$data[$key]['creditAmount'] / $master['companyLocalExchangeRate'];
                $data[$key]['creditCompanyLocalAmount'] = round($creditCompanyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $creditCompanyReportingAmount=$data[$key]['creditAmount'] / $master['companyReportingExchangeRate'];
                $data[$key]['creditCompanyReportingAmount'] = round($creditCompanyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);

                //updating the value as 0
                $data[$key]['debitAmount'] = 0;
                $data[$key]['debitCompanyLocalAmount'] = 0;
                $data[$key]['debitCompanyReportingAmount'] = 0;
            } else {


                $data[$key]['debitAmount'] = round( $debitAmount[$key], $master['transactionCurrencyDecimalPlaces']);
                $debitCompanyLocalAmount=$data[$key]['debitAmount'] / $master['companyLocalExchangeRate'];
                $data[$key]['debitCompanyLocalAmount'] = round($debitCompanyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $debitCompanyReportingAmount=$data[$key]['debitAmount'] / $master['companyReportingExchangeRate'];
                $data[$key]['debitCompanyReportingAmount'] = round($debitCompanyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);

                //updating the value as 0
                $data[$key]['creditAmount'] = 0;
                $data[$key]['creditCompanyLocalAmount'] = 0;
                $data[$key]['creditCompanyReportingAmount'] = 0;
            }
            $data[$key]['description'] = $descriptions[$key];
            $data[$key]['type'] = 'GL';

            $data[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];

        }

        $this->db->insert_batch('srp_erp_recurringjvdetail', $data);
        /*$last_id = $this->db->insert_id();*/
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'GL Description : Saved Failed ');
        } else {
            $this->db->trans_commit();
            return array('s', 'GL Description :  Saved Successfully.');
        }
    }


    function update_gl_detail()
    {
        $projectExist = project_is_exist();
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('RJVMasterAutoId', $this->input->post('RJVMasterAutoId'));
        $master = $this->db->get('srp_erp_recurringjvmaster')->row_array();
        $segment = explode('|', trim($this->input->post('edit_segment_gl')));
        $gl = $this->input->post('gl_code_des');
        $creditAmount = $this->input->post('editcreditAmount');
        $projectID = $this->input->post('projectID');
        $debitAmount = $this->input->post('editdebitAmount');

        $gldata = fetch_gl_account_desc($this->input->post('edit_gl_code'));
        if ($gldata['masterCategory'] == 'PL') {
            $data['segmentID'] = trim($segment[0]);
            $data['segmentCode'] = trim($segment[1]);

        }

        $gl_code = explode('|', trim($gl));
        $data['RJVMasterAutoId'] = trim($this->input->post('RJVMasterAutoId'));
        $data['GLAutoID'] = trim($this->input->post('edit_gl_code'));
        $data['systemGLCode'] = trim($gl_code[0]);
        $data['GLCode'] = trim($gl_code[1]);
        $data['GLDescription'] = trim($gl_code[2]);
        $data['GLType'] = trim($gl_code[3]);

        if($projectExist == 1){
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'],$projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        if ($creditAmount > 0) {
            $data['gl_type'] = 'Cr';
        } else {
            $data['gl_type'] = 'Dr';
        }

        if ($data['gl_type'] == 'Cr') {
            $data['creditAmount'] = round( trim($this->input->post('editcreditAmount')), $master['transactionCurrencyDecimalPlaces']);
            $creditCompanyLocalAmount=$data['creditAmount'] / $master['companyLocalExchangeRate'];
            $data['creditCompanyLocalAmount'] = round($creditCompanyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $creditCompanyReportingAmount=$data['creditAmount'] / $master['companyReportingExchangeRate'];
            $data['creditCompanyReportingAmount'] = round($creditCompanyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);

            //updating the value as 0
            $data['debitAmount'] = 0;
            $data['debitCompanyLocalAmount'] = 0;
            $data['debitCompanyReportingAmount'] = 0;
        } else {
            $data['debitAmount'] = round( trim($this->input->post('editdebitAmount')), $master['transactionCurrencyDecimalPlaces']);
            $debitCompanyLocalAmount=$data['debitAmount'] / $master['companyLocalExchangeRate'];
            $data['debitCompanyLocalAmount'] = round($debitCompanyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $debitCompanyReportingAmount=$data['debitAmount'] / $master['companyReportingExchangeRate'];
            $data['debitCompanyReportingAmount'] = round($debitCompanyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);

            //updating the value as 0
            $data['creditAmount'] = 0;
            $data['creditCompanyLocalAmount'] = 0;
            $data['creditCompanyReportingAmount'] = 0;
        }
        $data['description'] = trim($this->input->post('editdescription'));
        $data['type'] = 'GL';

        if (trim($this->input->post('RJVDetailAutoID'))) {
            $this->db->where('RJVDetailAutoID', trim($this->input->post('RJVDetailAutoID')));
            $this->db->update('srp_erp_recurringjvdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'GL Description : ' . $data['GLDescription'] . ' Update Failed ');
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'GL Description : ' . $data['GLDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('RJVDetailAutoID'));
            }
        } else {
        }
    }

    function fetch_Journal_entry_template_data($RJVMasterAutoId)
    {
        $convertFormat=convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(RJVStartDate,\''.$convertFormat.'\') AS RJVStartDate,DATE_FORMAT(RJVEndDate,\''.$convertFormat.'\') AS RJVEndDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate');
        $this->db->where('RJVMasterAutoId', $RJVMasterAutoId);
        $this->db->from('srp_erp_recurringjvmaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);
        $this->db->select('*');
        $this->db->where('RJVMasterAutoId', $RJVMasterAutoId);
        $this->db->from('srp_erp_recurringjvdetail');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function fetch_journal_entry_detail()
    {
        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces');
        $this->db->where('RJVMasterAutoId', $this->input->post('RJVMasterAutoId'));
        $this->db->from('srp_erp_recurringjvmaster');
        $data['currency'] = $this->db->get()->row_array();
        $this->db->select('*');
        $this->db->where('RJVMasterAutoId', $this->input->post('RJVMasterAutoId'));
        $this->db->from('srp_erp_recurringjvdetail');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function load_journal_entry_header()
    {
        $convertFormat=convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(RJVStartDate,\''.$convertFormat.'\') AS RJVStartDate,DATE_FORMAT(RJVEndDate,\''.$convertFormat.'\') AS RJVEndDate');
        $this->db->where('RJVMasterAutoId', $this->input->post('RJVMasterAutoId'));
        $this->db->from('srp_erp_recurringjvmaster');
        return $this->db->get()->row_array();
    }

    function delete_Journal_entry_detail()
    {
        $this->db->where('RJVDetailAutoID', $this->input->post('RJVDetailAutoID'));
        $this->db->delete('srp_erp_recurringjvdetail');
        $this->session->set_flashdata('s', 'Recurring journal entry : deleted Successfully.');
        return true;
    }

    function delete_recurring_journal_entry()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_recurringjvdetail');
        $this->db->where('RJVMasterAutoId', trim($this->input->post('RJVMasterAutoId')));
        $datas= $this->db->get()->row_array();
        if($datas){
            $this->session->set_flashdata('e', 'please delete all detail records before deleting this document.');
            return true;
        }else{
            $data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('RJVMasterAutoId', trim($this->input->post('RJVMasterAutoId')));
            $this->db->update('srp_erp_recurringjvmaster', $data);
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;
        }
    }

    function recurring_journal_entry_confirmation()
    {
        $this->load->library('approvals');
        $this->db->select('RJVMasterAutoId, RJVcode');
        $this->db->where('RJVMasterAutoId', trim($this->input->post('RJVMasterAutoId')));
        $this->db->from('srp_erp_recurringjvmaster');
        $app_data = $this->db->get()->row_array();
        $approvals_status = $this->approvals->CreateApproval('RJV', $app_data['RJVMasterAutoId'], $app_data['RJVcode'], 'Recurring Journal Voucher', 'srp_erp_recurringjvmaster', 'RJVMasterAutoId');

        $this->db->select_sum('debitAmount');
        $this->db->where('RJVMasterAutoId', trim($this->input->post('RJVMasterAutoId')));
        $amount = $this->db->get('srp_erp_recurringjvdetail')->row_array();
        if ($approvals_status==1) {
            $data = array(
                'confirmedYN' => 1,
                'confirmedDate' => $this->common_data['current_date'],
                'confirmedByEmpID' => $this->common_data['current_userID'],
                'confirmedByName' => $this->common_data['current_user'],
                'transactionAmount' => $amount['debitAmount']
            );

            $this->db->where('RJVMasterAutoId', trim($this->input->post('RJVMasterAutoId')));
            $this->db->update('srp_erp_recurringjvmaster', $data);
            $this->session->set_flashdata('s', 'Approvals Created Successfully.');
            return true;
        } else if($approvals_status==3){
            $this->session->set_flashdata('w', 'There are no users exist to perform approval for this document.');
            return true;
        } else {
            $this->session->set_flashdata('e', 'Document confirmation failed.');
            return false;
        }
    }

    function save_rjv_approval()
    {
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_code = trim($this->input->post('RJVMasterAutoId'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        $comments = trim($this->input->post('comments'));

        $this->db->select('documentID');
        $this->db->from('srp_erp_recurringjvmaster');
        $this->db->where('RJVMasterAutoId', $system_code);
        $code = $this->db->get()->row('documentID');

        $approvals_status = $this->approvals->approve_document($system_code,$level_id, $status, $comments, $code);
        if ($approvals_status == 1) {
            // $data['approvedYN']             = $status;
            // $data['approvedbyEmpID']        = $this->common_data['current_userID'];
            // $data['approvedbyEmpName']      = $this->common_data['current_user'];
            // $data['approvedDate']           = $this->common_data['current_date'];

            // $this->db->where('contractAutoID', trim($this->input->post('contractAutoID')));
            // $this->db->update('srp_erp_creditnotemaster', $data);
            $this->session->set_flashdata('s', 'Approval Successfully.');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function re_open_journal_entry(){
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('RJVMasterAutoId', trim($this->input->post('RJVMasterAutoId')));
        $this->db->update('srp_erp_recurringjvmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }
    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'RJV');
        $this->db->from('srp_erp_documentcodemaster ');
        return $this->db->get()->row_array();


    }
}