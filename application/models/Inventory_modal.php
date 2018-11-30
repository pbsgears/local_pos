<?php

class Inventory_modal extends ERP_Model
{
    function save_material_issue_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $isuDate = $this->input->post('issueDate');
        $issueDate = input_format_date($isuDate, $date_format_policy);
        $segment = explode('|', trim($this->input->post('segment')));
        $location = explode('|', trim($this->input->post('location_dec')));
        $requestedLocation = explode('|', trim($this->input->post('requested_location_dec')));
        //$period = explode('|', trim($this->input->post('financeyear_period')));
        $year = explode(' - ', trim($this->input->post('companyFinanceYear')));

        $FYBegin = input_format_date($year[0], $date_format_policy);
        $FYEnd = input_format_date($year[1], $date_format_policy);

        $data['documentID'] = 'MI';
        $data['issueType'] = trim($this->input->post('issueType'));
        $data['itemType'] = trim($this->input->post('itemType'));
        $data['issueDate'] = trim($issueDate);
        $data['issueRefNo'] = trim($this->input->post('issueRefNo'));
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear'));
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear'));
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period'));
        //$data['FYPeriodDateFrom'] = trim($period[0]);
        //$data['FYPeriodDateTo'] = trim($period[1]);
        $data['wareHouseAutoID'] = trim($this->input->post('location'));
        $data['wareHouseCode'] = trim($location[0]);
        $data['wareHouseLocation'] = trim($location[1]);
        $data['wareHouseDescription'] = trim($location[2]);
        if ($data['issueType'] == 'Material Request') {
            $data['requestedWareHouseAutoID'] = trim($this->input->post('requested_location'));
            $data['requestedWareHouseCode'] = trim($requestedLocation[0]);
            $data['requestedWareHouseLocation'] = trim($requestedLocation[1]);
            $data['requestedWareHouseDescription'] = trim($requestedLocation[2]);
        }
        $data['jobNo'] = trim($this->input->post('jobNo'));
        /* $data['employeeCode'] = trim($Requested[0]);
         $data['employeeName'] = trim($Requested[1]);*/
        /*$data['employeeCode'] = trim($Requested[0]);*/
        if ($data['issueType'] == 'Direct Issue') {
            if ($this->input->post('employeeID')) {
                $Requested = explode('|', trim($this->input->post('requested')));
                $data['employeeName'] = trim($Requested[1]);
                $data['employeeCode'] = trim($Requested[0]);
                $data['employeeID'] = trim($this->input->post('employeeID'));
            } else {
                $data['employeeName'] = trim($this->input->post('employeeName'));
                $data['employeeCode'] = NULL;
                $data['employeeID'] = NULL;
            }
        }
        $data['requestedDate'] = '';
        $data['comment'] = trim($this->input->post('narration'));
        if ($data['itemType'] == 'Material Request') {
            $data['segmentID'] = '';
            $data['segmentCode'] = '';
        } else {
            $data['segmentID'] = trim($segment[0]);
            $data['segmentCode'] = trim($segment[1]);
        }
        if (trim($this->input->post('itemIssueAutoID'))) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID')));
            $this->db->update('srp_erp_itemissuemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Material Issue :  Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Material Issue :  Updated Successfully.', $this->input->post('issueType'));
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('itemIssueAutoID'), 'issueType' => $this->input->post('issueType'));
            }
        } else {
            //$this->load->library('sequence');
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data['companyLocalExchangeRate'] = 1;
            $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $reporting_currency = currency_conversionID($data['companyLocalCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            //$data['itemIssueCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_itemissuemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Material Issue :   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Material Issue :  Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id, 'issueType' => $this->input->post('issueType'));
            }
        }
    }

    function save_stock_transfer_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $trfrDate = $this->input->post('tranferDate');
        $tranferDate = input_format_date($trfrDate, $date_format_policy);

        $segment = explode('|', trim($this->input->post('segment')));
        $form_location = explode('|', trim($this->input->post('form_location_dec')));
        $to_location = explode('|', trim($this->input->post('to_location_dec')));
        //$period = explode('|', trim($this->input->post('financeyear_period')));
        $year = explode(' - ', trim($this->input->post('companyFinanceYear')));
        $FYBegin = input_format_date($year[0], $date_format_policy);
        $FYEnd = input_format_date($year[1], $date_format_policy);

        $data['documentID'] = 'ST';
        $data['itemType'] = trim($this->input->post('itemType'));
        $data['tranferDate'] = trim($tranferDate);
        $data['referenceNo'] = trim($this->input->post('referenceNo'));
        $data['comment'] = trim($this->input->post('narration'));
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear'));
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear'));
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period'));
        /*$data['FYPeriodDateFrom'] = trim($period[0]);
        $data['FYPeriodDateTo'] = trim($period[1]);*/
        $data['from_wareHouseAutoID'] = trim($this->input->post('form_location'));
        $data['form_wareHouseCode'] = trim($form_location[0]);
        $data['form_wareHouseLocation'] = trim($form_location[1]);
        $data['form_wareHouseDescription'] = trim($form_location[2]);
        $data['to_wareHouseAutoID'] = trim($this->input->post('to_location'));
        $data['to_wareHouseCode'] = trim($to_location[0]);
        $data['to_wareHouseLocation'] = trim($to_location[1]);
        $data['to_wareHouseDescription'] = trim($to_location[2]);
        $data['segmentID'] = trim($segment[0]);
        $data['segmentCode'] = trim($segment[1]);
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('stockTransferAutoID'))) {
            $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID')));
            $this->db->update('srp_erp_stocktransfermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Stock Transfer : ' . $data['form_wareHouseDescription'] . ' - ' . $data['to_wareHouseDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Stock Transfer : ' . $data['form_wareHouseDescription'] . ' - ' . $data['to_wareHouseDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('stockTransferAutoID'));
            }
        } else {
            $this->load->library('sequence');
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data['companyLocalExchangeRate'] = 1;
            $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $reporting_currency = currency_conversionID($data['companyLocalCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['stockTransferCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_stocktransfermaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Stock Transfer : ' . $data['form_wareHouseDescription'] . ' - ' . $data['to_wareHouseDescription'] . '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Stock Transfer : ' . $data['form_wareHouseDescription'] . ' - ' . $data['to_wareHouseDescription'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_stock_adjustment_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $stkAdjmntDate = $this->input->post('stockAdjustmentDate');
        $stockAdjustmentDate = input_format_date($stkAdjmntDate, $date_format_policy);

        $segment = explode('|', trim($this->input->post('segment')));
        $location = explode('|', trim($this->input->post('location_dec')));
        //$period = explode('|', trim($this->input->post('financeyear_period')));
        $year = explode(' - ', trim($this->input->post('companyFinanceYear')));

        $FYBegin = input_format_date($year[0], $date_format_policy);
        $FYEnd = input_format_date($year[1], $date_format_policy);

        $data['documentID'] = 'SA';
        $data['stockAdjustmentDate'] = trim($stockAdjustmentDate);
        $data['referenceNo'] = trim($this->input->post('referenceNo'));
        $data['comment'] = trim($this->input->post('narration'));
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear'));
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear'));
        $data['stockAdjustmentType'] = trim($this->input->post('adjustmentType'));
        $data['adjustmentType'] = trim($this->input->post('adjsType'));
        $data['wareHouseAutoID'] = trim($this->input->post('location'));
        $data['wareHouseCode'] = trim($location[0]);
        $data['wareHouseLocation'] = trim($location[1]);
        $data['wareHouseDescription'] = trim($location[2]);
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period'));
        /*$data['FYPeriodDateFrom'] = trim($period[0]);
        $data['FYPeriodDateTo'] = trim($period[1]);*/
        $data['segmentID'] = trim($segment[0]);
        $data['segmentCode'] = trim($segment[1]);
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('stockAdjustmentAutoID'))) {
            $this->db->where('stockAdjustmentAutoID', trim($this->input->post('stockAdjustmentAutoID')));
            $this->db->update('srp_erp_stockadjustmentmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Stock Adjustment : ' . $data['wareHouseDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Stock Adjustment : ' . $data['wareHouseDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('stockAdjustmentAutoID'));
            }
        } else {
            $this->load->library('sequence');
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data['companyLocalExchangeRate'] = 1;
            $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $reporting_currency = currency_conversionID($data['companyLocalCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['stockAdjustmentCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_stockadjustmentmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Stock Adjustment : ' . $data['wareHouseDescription'] . '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Stock Adjustment : ' . $data['wareHouseDescription'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_stock_return_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $rtrnDate = $this->input->post('returnDate');
        $returnDate = input_format_date($rtrnDate, $date_format_policy);

        $location = explode('|', trim($this->input->post('location_dec')));
        //$period = explode('|', trim($this->input->post('financeyear_period')));
        $year = explode(' - ', trim($this->input->post('companyFinanceYear')));
        $FYBegin = input_format_date($year[0], $date_format_policy);
        $FYEnd = input_format_date($year[1], $date_format_policy);
        $supplier_arr = $this->fetch_supplier_data(trim($this->input->post('supplierID')));
        $currency_code = explode('|', trim($this->input->post('currency_code')));
        $data['documentID'] = 'SR';
        $data['returnDate'] = trim($returnDate);
        $data['referenceNo'] = trim($this->input->post('referenceNo'));
        $data['comment'] = trim($this->input->post('narration'));
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear'));
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear'));
        $data['supplierID'] = trim($this->input->post('supplierID'));
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period'));
        //$data['FYPeriodDateFrom'] = trim($period[0]);
        //$data['FYPeriodDateTo'] = trim($period[1]);
        $data['wareHouseAutoID'] = trim($this->input->post('location'));
        $data['wareHouseCode'] = trim($location[0]);
        $data['wareHouseLocation'] = trim($location[1]);
        $data['wareHouseDescription'] = trim($location[2]);
        $data['supplierSystemCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
        $data['supplierAddress'] = $supplier_arr['supplierAddress1'] . ' ' . $supplier_arr['supplierAddress2'];
        $data['supplierTelephone'] = $supplier_arr['supplierTelephone'];
        $data['supplierFax'] = $supplier_arr['supplierFax'];
        $data['supplierEmail'] = $supplier_arr['supplierEmail'];
        $data['supplierliabilityAutoID'] = $supplier_arr['liabilityAutoID'];
        $data['supplierliabilitySystemGLCode'] = $supplier_arr['liabilitySystemGLCode'];
        $data['supplierliabilityGLAccount'] = $supplier_arr['liabilityGLAccount'];
        $data['supplierliabilityDescription'] = $supplier_arr['liabilityDescription'];
        $data['supplierliabilityType'] = $supplier_arr['liabilityType'];

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

        $data['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $data['supplierCurrency'] = $supplier_arr['supplierCurrency'];
        $supplierCurrency = currency_conversionID($data['transactionCurrencyID'], $data['supplierCurrencyID']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('stockReturnAutoID'))) {
            $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID')));
            $this->db->update('srp_erp_stockreturnmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Purchase Return : ' . $data['wareHouseDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Purchase Return : ' . $data['wareHouseDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('stockReturnAutoID'));
            }
        } else {
            //$this->load->library('sequence');

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            //$data['stockReturnCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_stockreturnmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Purchase Return : ' . $data['wareHouseDescription'] . '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Purchase Return : ' . $data['wareHouseDescription'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function load_material_issue_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(issueDate,\'' . $convertFormat . '\') AS issueDate');
        $this->db->where('itemIssueAutoID', $this->input->post('itemIssueAutoID'));
        return $this->db->get('srp_erp_itemissuemaster')->row_array();
    }

    function laad_stock_transfer_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(tranferDate,\'' . $convertFormat . '\') AS tranferDate ');
        $this->db->where('stockTransferAutoID', $this->input->post('stockTransferAutoID'));
        return $this->db->get('srp_erp_stocktransfermaster')->row_array();
    }

    function fetch_supplier_data($supplierID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID);
        return $this->db->get()->row_array();
    }

    function laad_stock_adjustment_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(stockAdjustmentDate,\'' . $convertFormat . '\') AS stockAdjustmentDate');
        $this->db->where('stockAdjustmentAutoID', $this->input->post('stockAdjustmentAutoID'));
        return $this->db->get('srp_erp_stockadjustmentmaster')->row_array();
    }

    function load_stock_return_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(returnDate,\'' . $convertFormat . '\') AS returnDate');
        $this->db->where('stockReturnAutoID', $this->input->post('stockReturnAutoID'));
        return $this->db->get('srp_erp_stockreturnmaster')->row_array();
    }

    function fetch_stockTransfer_detail_table()
    {
        $this->db->select('srp_erp_stocktransferdetails.*,srp_erp_itemmaster.isSubitemExist,srp_erp_stocktransfermaster.from_wareHouseAutoID');
        $this->db->where('srp_erp_stocktransferdetails.stockTransferAutoID', $this->input->post('stockTransferAutoID'));
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_stocktransferdetails.itemAutoID', 'left');
        $this->db->join('srp_erp_stocktransfermaster', 'srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID', 'left');
        return $this->db->get('srp_erp_stocktransferdetails')->result_array();
    }

    function fetch_stock_adjustment_detail()
    {
        $this->db->select('PLGLAutoID,BLGLAutoID,srp_erp_stockadjustmentdetails.itemSystemCode, srp_erp_stockadjustmentdetails.itemDescription,srp_erp_stockadjustmentdetails.unitOfMeasure,srp_erp_stockadjustmentdetails.previousWareHouseStock,srp_erp_stockadjustmentdetails.previousWac,srp_erp_stockadjustmentmaster.companyLocalCurrencyDecimalPlaces,srp_erp_stockadjustmentdetails.currentWareHouseStock,srp_erp_stockadjustmentdetails.currentWac,srp_erp_stockadjustmentdetails.adjustmentWareHouseStock,srp_erp_stockadjustmentdetails.adjustmentWac,srp_erp_stockadjustmentdetails.totalValue,srp_erp_stockadjustmentdetails.stockAdjustmentDetailsAutoID,srp_erp_stockadjustmentdetails.previousStock, srp_erp_stockadjustmentdetails.currentStock, srp_erp_stockadjustmentmaster.wareHouseAutoID, srp_erp_itemmaster.isSubitemExist,srp_erp_itemmaster.currentStock as itemcurrentStock');
        $this->db->where('srp_erp_stockadjustmentdetails.stockAdjustmentAutoID', $this->input->post('stockAdjustmentAutoID'));
        $this->db->join('srp_erp_stockadjustmentmaster', 'srp_erp_stockadjustmentdetails.stockAdjustmentAutoID = srp_erp_stockadjustmentmaster.stockAdjustmentAutoID');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID= srp_erp_stockadjustmentdetails.itemAutoID', 'left');
        return $this->db->get('srp_erp_stockadjustmentdetails')->result_array();
    }

    function fetch_template_data($itemIssueAutoID)
    {
        /*$convertFormat = convert_date_format_sql();
        $this->db->select('*,,DATE_FORMAT(issueDate,\'' . $convertFormat . '\') AS issueDate,(DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\')) AS approvedDate');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $this->db->from('srp_erp_itemissuemaster');
        $data['master'] = $this->db->get()->row_array();
        $this->db->select('srp_erp_itemissuedetails.*,srp_erp_materialrequest.MRCode');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $this->db->join('srp_erp_materialrequest', 'srp_erp_materialrequest.mrAutoID = srp_erp_itemissuedetails.mrAutoID', 'left');
        $this->db->from('srp_erp_itemissuedetails');
        $data['detail'] = $this->db->get()->result_array();
        return $data;*/
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,,DATE_FORMAT(issueDate,\'' . $convertFormat . '\') AS issueDate,(DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\')) AS approvedDate,(DATE_FORMAT(FYBegin,\'' . $convertFormat . '\')) AS FYbegining,(DATE_FORMAT(FYEnd,\'' . $convertFormat . '\')) AS FYend');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $this->db->from('srp_erp_itemissuemaster');
        $data['master'] = $this->db->get()->row_array();
        $this->db->select('srp_erp_itemissuedetails.*,srp_erp_materialrequest.MRCode,srp_erp_chartofaccounts.systemAccountCode as systemAccountCode,srp_erp_chartofaccounts.GLDescription as GLDescription ');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $this->db->join('srp_erp_materialrequest', 'srp_erp_materialrequest.mrAutoID = srp_erp_itemissuedetails.mrAutoID', 'left');
        $this->db->join('srp_erp_chartofaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_itemissuedetails.PLGLAutoID', 'left');
        $this->db->from('srp_erp_itemissuedetails');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function fetch_template_stock_transfer($stockTransferAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(tranferDate,\'' . $convertFormat . '\') AS tranferDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate');
        $this->db->where('stockTransferAutoID', $stockTransferAutoID);
        $this->db->from('srp_erp_stocktransfermaster');
        $data['master'] = $this->db->get()->row_array();
        $this->db->select('*');
        $this->db->where('stockTransferAutoID', $stockTransferAutoID);
        $this->db->from('srp_erp_stocktransferdetails');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function fetch_template_stock_return_data($stockReturnAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(returnDate,\'' . $convertFormat . '\') AS returnDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate');
        $this->db->where('stockReturnAutoID', $stockReturnAutoID);
        $this->db->from('srp_erp_stockreturnmaster');
        $data['master'] = $this->db->get()->row_array();
        $this->db->select('stockReturnDetailsID, stockReturnAutoID, type, itemAutoID, concat(grvPrimaryCode," - ",srp_erp_stockreturndetails.itemSystemCode ) as itemSystemCode, itemDescription, itemFinanceCategory, itemFinanceCategorySub, financeCategory, itemCategory, unitOfMeasureID, unitOfMeasure, defaultUOMID, defaultUOM conversionRateUOM, return_Qty, received_Qty, currentStock, currentWareHouseStock, currentlWacAmount, totalValue, PLGLAutoID, PLSystemGLCode, PLGLCode, PLDescription, PLType, BLGLAutoID, BLSystemGLCode, BLGLCode, BLDescription, BLType, comments ');
        $this->db->where('stockReturnAutoID', $stockReturnAutoID);
        $this->db->from('srp_erp_stockreturndetails');

        $this->db->join('srp_erp_grvmaster', 'srp_erp_grvmaster.grvAutoID = srp_erp_stockreturndetails.grvAutoID', 'left');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function fetch_template_stock_adjustment($stockAdjustmentAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(stockAdjustmentDate,\'' . $convertFormat . '\') AS stockAdjustmentDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate');
        $this->db->where('stockAdjustmentAutoID', $stockAdjustmentAutoID);
        $this->db->from('srp_erp_stockadjustmentmaster');
        $data['master'] = $this->db->get()->row_array();
        $this->db->select('*');
        $this->db->where('stockAdjustmentAutoID', $stockAdjustmentAutoID);
        $this->db->from('srp_erp_stockadjustmentdetails');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function save_material_detail()
    {
        $projectExist = project_is_exist();
        $companyID = current_companyID();

        $this->db->select('cca.GLAutoID as GLAutoID,ca.systemAccountCode,ca.GLSecondaryCode,ca.GLDescription,ca.subCategory');
        $this->db->from('srp_erp_companycontrolaccounts cca');
        $this->db->join('srp_erp_chartofaccounts ca', 'cca.GLAutoID = ca.GLAutoID', 'LEFT');
        $this->db->where('controlAccountType', 'GIT');
        $this->db->where('cca.companyID', $this->common_data['company_data']['company_id']);
        $materialRequestGlDetail = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_itemissuemaster');
        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID')));
        $masterRecord = $this->db->get()->row_array();

        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        if (!empty($this->input->post('itemIssueDetailID'))) {
            $this->db->select('itemIssueAutoID,,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_itemissuedetails');
            $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID')));
            if ($masterRecord['issueType'] == 'Material Request') {
                $this->db->where('mrAutoID', trim($this->input->post('mrAutoID')));
            }
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
            $this->db->where('itemIssueDetailID !=', trim($this->input->post('itemIssueDetailID')));
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('w', 'Item Issue Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }

        if ($masterRecord['issueType'] == 'Material Request') {
            if (!empty($this->input->post('itemIssueDetailID'))) {
                $this->db->select('mrAutoID,,qtyRequested');
                $this->db->from('srp_erp_itemissuedetails');
                $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID')));
                $this->db->where('itemIssueDetailID', trim($this->input->post('itemIssueDetailID')));
                $req_QTY = $this->db->get()->row_array();

                if (!empty($req_QTY)) {
                    $mrid = $req_QTY['mrAutoID'];
                    $itmid = $this->input->post('itemAutoID');
                    $itmDid = $this->input->post('itemIssueDetailID');
                    $issuedQTY = $this->db->query("SELECT
	SUM(qtyIssued) as qtyIssued
FROM
	`srp_erp_itemissuedetails`

WHERE
	`srp_erp_itemissuedetails`.`mrAutoID` = $mrid
AND `srp_erp_itemissuedetails`.`itemAutoID` = $itmid
AND `srp_erp_itemissuedetails`.`companyID` = $companyID
AND `srp_erp_itemissuedetails`.`itemIssueDetailID`!= $itmDid
")->row_array();
                    $qtyrequested = $req_QTY['qtyRequested'] - $issuedQTY['qtyIssued'];
                    if (!empty($req_QTY['mrAutoID']) && $req_QTY['mrAutoID'] > 0 && $qtyrequested < $this->input->post('quantityRequested')) {
                        return array('w', 'Qty cannot be grater than balance qty');
                    }
                }

            }
        }

        $this->db->trans_start();
        $segment = explode('|', trim($this->input->post('a_segment')));
        $uom = explode('|', $this->input->post('uom'));
        $item_data = fetch_item_data(trim($this->input->post('itemAutoID')));
        $projectID = trim($this->input->post('projectID'));
        $data['itemIssueAutoID'] = trim($this->input->post('itemIssueAutoID'));
        $data['itemAutoID'] = trim($this->input->post('itemAutoID'));
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        $data['unitOfMeasure'] = trim($uom[0]);
        $data['unitOfMeasureID'] = trim($this->input->post('unitOfMeasureID'));
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['qtyIssued'] = trim($this->input->post('quantityRequested'));
        $data['comments'] = trim($this->input->post('comment'));
        $data['remarks'] = trim($this->input->post('remarks'));
        $data['currentWareHouseStock'] = trim($this->input->post('currentWareHouseStockQty'));
        if ($masterRecord['issueType'] == 'Material Request') {
            $data['segmentID'] = '';
            $data['segmentCode'] = '';
        } else {
            $data['segmentID'] = trim($segment[0]);
            $data['segmentCode'] = trim($segment[1]);
        }
        $data['itemFinanceCategory'] = $item_data['subcategoryID'];
        $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
        $data['financeCategory'] = $item_data['financeCategory'];
        $data['itemCategory'] = $item_data['mainCategory'];
        $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
        $data['currentStock'] = $item_data['currentStock'];
        if ($masterRecord['issueType'] == 'Material Request') {

            $data['PLGLAutoID'] = $materialRequestGlDetail['GLAutoID'];
            $data['PLSystemGLCode'] = $materialRequestGlDetail['systemAccountCode'];
            $data['PLGLCode'] = $materialRequestGlDetail['GLSecondaryCode'];
            $data['PLDescription'] = $materialRequestGlDetail['GLDescription'];
            $data['PLType'] = $materialRequestGlDetail['subCategory'];

            $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
            $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
            $data['BLGLCode'] = $item_data['assteGLCode'];
            $data['BLDescription'] = $item_data['assteDescription'];
            $data['BLType'] = $item_data['assteType'];
        } else {
            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }
        }
        $data['totalValue'] = ($data['currentlWacAmount'] * ($data['qtyIssued'] / $data['conversionRateUOM']));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('itemIssueDetailID'))) {
            $this->db->where('itemIssueDetailID', trim($this->input->post('itemIssueDetailID')));
            $this->db->update('srp_erp_itemissuedetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Item Issue Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Item Issue Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Updated Successfully.');
            }
        } else {
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_itemissuedetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Item Issue Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'Item Issue Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Saved Successfully.');
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_material_detail_multiple()
    {
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $itemIssueDetailID = $this->input->post('itemIssueDetailID');
        $itemIssueAutoID = $this->input->post('itemIssueAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $projectID = $this->input->post('projectID');
        $currentWareHouseStockQty = $this->input->post('currentWareHouseStockQty');
        $quantityRequested = $this->input->post('quantityRequested');
        $a_segment = $this->input->post('a_segment');
        $comment = $this->input->post('comment');

        $this->db->trans_start();

        foreach ($itemAutoIDs as $key => $itemAutoID) {

            if (!$itemIssueDetailID) {
                $this->db->select('itemIssueAutoID,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_itemissuedetails');
                $this->db->where('itemIssueAutoID', $itemIssueAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Item Issue Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);

            $data['itemIssueAutoID'] = trim($this->input->post('itemIssueAutoID'));
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasure'] = trim($uomEx[0]);
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['qtyIssued'] = $quantityRequested[$key];
            $data['comments'] = $comment[$key];
            $data['remarks'] = '';
            $data['currentWareHouseStock'] = $currentWareHouseStockQty[$key];
            $data['segmentID'] = trim($segment[0]);
            $data['segmentCode'] = trim($segment[1]);
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['currentStock'] = $item_data['currentStock'];

            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }

            $data['totalValue'] = ($data['currentlWacAmount'] * ($data['qtyIssued'] / $data['conversionRateUOM']));
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_itemissuedetails', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Item Issue Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Item Issue Detail :  Saved Successfully.');

        }

    }

    function save_return_item_detail()
    {
        if (!trim($this->input->post('stockReturnDetailsID'))) {
            $this->db->select('stockReturnAutoID');
            $this->db->from('srp_erp_stockreturndetails');
            $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID')));
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
            $order_detail = $this->db->get()->result_array();

            if (!empty($order_detail)) {
                $this->session->set_flashdata('w', 'Item Issue Detail : ' . trim($this->input->post('itemCode')) . '  already exists.');
                return array('status' => false);
            }
        }
        $this->db->trans_start();
        $segment = explode('|', trim($this->input->post('a_segment')));
        $data['stockReturnAutoID'] = trim($this->input->post('stockReturnAutoID'));
        $data['itemSystemCode'] = trim($this->input->post('itemSystemCode'));
        $data['itemAutoID'] = trim($this->input->post('itemAutoID'));
        $data['itemDescription'] = trim($this->input->post('itemDescription'));
        $data['unitOfMeasure'] = trim($this->input->post('unitOfMeasure'));
        $data['unitOfMeasureID'] = trim($this->input->post('unitOfMeasureID'));
        $data['defaultUOMID'] = trim($this->input->post('defaultUOMID'));
        $data['defaultUOM'] = trim($this->input->post('defaultUOM'));
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['return_Qty'] = trim($this->input->post('return_Qty'));
        $data['comments'] = trim($this->input->post('comment'));
        $data['type'] = 'Item';
        $data['currentStock'] = trim($this->input->post('currentStock'));
        $data['currentWareHouseStock'] = trim($this->input->post('currentWareHouseStockQty'));
        $data['segmentID'] = trim($segment[0]);
        $data['segmentCode'] = trim($segment[1]);

        if (trim($this->input->post('stockReturnDetailsID'))) {
            $this->db->where('stockReturnDetailsID', trim($this->input->post('stockReturnDetailsID')));
            $this->db->update('srp_erp_stockreturndetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Return Item Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'Return Item Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Updated Successfully.');
                return array('status' => true, 'last_id' => $this->input->post('itemIssueDetailID'));
            }
        } else {
            $item_data = fetch_item_data(trim($this->input->post('itemAutoID')));
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['currentStock'] = $item_data['currentStock'];
            //$data['currentWareHouseStock']  = $item_data['companyLocalWacAmount'];
            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }
            $data['totalValue'] = ($data['currentlWacAmount'] * $data['return_Qty']);

            $this->db->insert('srp_erp_stockreturndetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Return Item Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'Return Item Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Saved Successfully.');
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_stock_adjustment_detail()
    {
        if (!trim($this->input->post('stockAdjustmentDetailsAutoID'))) {
            $this->db->select('stockAdjustmentDetailsAutoID,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_stockadjustmentdetails');
            $this->db->where('stockAdjustmentDetailsAutoID', trim($this->input->post('stockAdjustmentDetailsAutoID')));
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
            $order_detail = $this->db->get()->result_array();
            if (!empty($order_detail)) {
                return array('w', 'Stock Adjustment Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }
        $this->db->trans_start();
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $projectID = trim($this->input->post('projectID'));

        $this->db->select('wareHouseAutoID');
        $this->db->where('stockAdjustmentAutoID', $this->input->post('stockAdjustmentAutoID'));
        $stockadjustmentMaster = $this->db->get('srp_erp_stockadjustmentmaster')->row_array();
        $segment = explode('|', trim($this->input->post('a_segment')));
        $uom = explode('|', $this->input->post('uom'));
        $item_data = fetch_item_data(trim($this->input->post('itemAutoID')));
        $this->db->select('currentStock');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        $prevItemMasterTotal = $this->db->get()->row_array();
        $previousWareHouseStock = $this->db->query("SELECT currentStock FROM srp_erp_warehouseitems WHERE wareHouseAutoID='{$stockadjustmentMaster['wareHouseAutoID']}' and itemAutoID='{$this->input->post('itemAutoID')}'")->row_array(); //get warehouse stock of the item by location
        $data['stockAdjustmentAutoID'] = trim($this->input->post('stockAdjustmentAutoID'));
        $data['itemSystemCode'] = trim($this->input->post('itemSystemCode'));
        $data['itemAutoID'] = trim($this->input->post('itemAutoID'));
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        $data['unitOfMeasure'] = trim($uom[0]);
        $data['unitOfMeasureID'] = trim($this->input->post('unitOfMeasureID'));
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['segmentID'] = trim($segment[0]);
        $data['segmentCode'] = trim($segment[1]);
        $data['itemFinanceCategory'] = $item_data['subcategoryID'];
        $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
        $data['financeCategory'] = $item_data['financeCategory'];
        $data['itemCategory'] = $item_data['mainCategory'];
        if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
            $data['PLGLAutoID'] = $item_data['stockAdjustmentGLAutoID'];
            $data['PLSystemGLCode'] = $item_data['stockAdjustmentSystemGLCode'];
            $data['PLGLCode'] = $item_data['stockAdjustmentGLCode'];
            $data['PLDescription'] = $item_data['stockAdjustmentDescription'];
            $data['PLType'] = $item_data['stockAdjustmentType'];
            $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
            $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
            $data['BLGLCode'] = $item_data['assteGLCode'];
            $data['BLDescription'] = $item_data['assteDescription'];
            $data['BLType'] = $item_data['assteType'];
        } elseif ($data['financeCategory'] == 2) {
            $data['PLGLAutoID'] = $item_data['costGLAutoID'];
            $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['PLGLCode'] = $item_data['costGLCode'];
            $data['PLDescription'] = $item_data['costDescription'];
            $data['PLType'] = $item_data['costType'];
            $data['BLGLAutoID'] = '';
            $data['BLSystemGLCode'] = '';
            $data['BLGLCode'] = '';
            $data['BLDescription'] = '';
            $data['BLType'] = '';
        }
        $data['previousStock'] = trim($prevItemMasterTotal['currentStock']);
        $data['previousWac'] = trim($this->input->post('currentWac'));
        $data['previousWareHouseStock'] = $previousWareHouseStock["currentStock"];
        $data['currentWac'] = trim($this->input->post('adjustment_wac'));
        $data['currentWareHouseStock'] = trim($this->input->post('adjustment_Stock'));
        $data['adjustmentWac'] = (trim($this->input->post('adjustment_wac')) - trim($this->input->post('currentWac')));
        $data['adjustmentWareHouseStock'] = (trim($this->input->post('adjustment_Stock')) - trim($this->input->post('currentWareHouseStock')));
        $data['adjustmentStock'] = (trim($this->input->post('adjustment_Stock')) - trim($this->input->post('currentWareHouseStock')));
        $data['currentStock'] = $data['adjustmentStock'] + $data['previousStock'];
        $previousTotal = ($data['previousStock'] * $data['previousWac']);
        $newTotal = ($data['currentStock'] * $data['currentWac']);
        $data['totalValue'] = ($data['currentStock'] * $data['currentWac']) - ($data['previousStock'] * $data['previousWac']);
        $data['comments'] = trim($this->input->post('comments'));

        if (trim($this->input->post('stockAdjustmentDetailsAutoID'))) {

            $this->db->where('stockAdjustmentDetailsAutoID', trim($this->input->post('stockAdjustmentDetailsAutoID')));
            $this->db->update('srp_erp_stockadjustmentdetails', $data);

            /** item master Sub codes*/
            $detailsAutoID = $this->input->post('stockAdjustmentDetailsAutoID');

            /* 1---- delete all entries in the update process - item master sub temp */
            $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentDetailID' => $detailsAutoID, 'receivedDocumentID' => 'SA'));
            /* 2----  update all selected sub item list */
            if ($item_data['isSubitemExist'] == 1) {

                if ($data['previousStock'] < $data['currentStock']) {
                    /* Add Stock */
                    $qty = $data['adjustmentStock'];
                    $last_id = $detailsAutoID;
                    $documentAutoID = $data['stockAdjustmentAutoID'];

                    $subData['uom'] = $data['unitOfMeasure'];
                    $subData['uomID'] = $data['unitOfMeasureID'];
                    $subData['grv_detailID'] = $last_id;
                    $subData['warehouseAutoID'] = $stockadjustmentMaster['wareHouseAutoID'];
                    $this->add_sub_itemMaster_tmpTbl($qty, $data['itemAutoID'], $documentAutoID, $last_id, 'SA', $item_data['itemSystemCode'], $subData);
                }


            }

            /* 3---- update all selected values */

            $setData['isSold'] = null;
            $setData['soldDocumentID'] = null;
            $setData['soldDocumentAutoID'] = null;
            $setData['soldDocumentDetailID'] = null;

            $ware['soldDocumentID'] = 'SA';
            $ware['soldDocumentDetailID'] = $detailsAutoID;

            $this->db->update('srp_erp_itemmaster_sub', $setData, $ware);


            /** end item master Sub codes*/


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Stock Adjustment Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Stock Adjustment Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Updated Successfully.');
            }
        } else {
            /* We are not using this method : there is a bulk insert method used to add the item..  */
            $this->db->insert('srp_erp_stockadjustmentdetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Stock Adjustment Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'Stock Adjustment Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Saved Successfully.');
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_stock_adjustment_detail_multiple()
    {
        $stockAdjustmentDetailsAutoID = $this->input->post('stockAdjustmentDetailsAutoID');
        $stockAdjustmentAutoID = $this->input->post('stockAdjustmentAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $currentWareHouseStock = $this->input->post('currentWareHouseStock');
        $currentWac = $this->input->post('currentWac');
        $projectID = $this->input->post('projectID');
        $adjustment_Stock = $this->input->post('adjustment_Stock');
        $adjustment_wac = $this->input->post('adjustment_wac');
        $a_segment = $this->input->post('a_segment');

        $projectExist = project_is_exist();
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];

        $this->db->select('wareHouseAutoID, wareHouseCode, wareHouseLocation, wareHouseDescription,adjustmentType');
        $this->db->where('stockAdjustmentAutoID', $this->input->post('stockAdjustmentAutoID'));
        $stockadjustmentMaster = $this->db->get('srp_erp_stockadjustmentmaster')->row_array();


        $this->db->trans_start();
        foreach ($itemAutoIDs as $key => $itemAutoID) {

            if (!$stockAdjustmentDetailsAutoID) {
                $this->db->select('stockAdjustmentDetailsAutoID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_stockadjustmentdetails');
                $this->db->where('stockAdjustmentAutoID', $stockAdjustmentAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('error' => 1, 'w', 'Stock Adjustment Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);

            $previousWareHouseStock = $this->db->query("SELECT currentStock FROM srp_erp_warehouseitems WHERE wareHouseAutoID='{$stockadjustmentMaster['wareHouseAutoID']}' and itemAutoID='{$itemAutoID}'")->row_array(); //get warehouse stock of the item by location

            $this->db->select('currentStock,companyLocalWacAmount,(currentStock * companyLocalWacAmount) as prevItemMasterTotal');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $prevItemMasterTotal = $this->db->get()->row_array();

            $data['stockAdjustmentAutoID'] = $stockAdjustmentAutoID;
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasure'] = trim($uomEx[0]);
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['segmentID'] = trim($segment[0]);
            $data['segmentCode'] = trim($segment[1]);
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];

            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {

                $data['PLGLAutoID'] = $item_data['stockAdjustmentGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['stockAdjustmentSystemGLCode'];
                $data['PLGLCode'] = $item_data['stockAdjustmentGLCode'];
                $data['PLDescription'] = $item_data['stockAdjustmentDescription'];
                $data['PLType'] = $item_data['stockAdjustmentType'];

                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];

            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }
            //$data['previousStock'] = isset($prevItemMasterTotal['currentStock']) && !empty($prevItemMasterTotal['currentStock']) ? $prevItemMasterTotal['currentStock'] : 0;
            $data['previousStock'] = $prevItemMasterTotal['currentStock'];
            $data['previousWac'] = $currentWac[$key];
            $data['previousWareHouseStock'] = isset($previousWareHouseStock["currentStock"]) && !empty($previousWareHouseStock["currentStock"]) ? $previousWareHouseStock["currentStock"] : 0;
            $data['currentWac'] = $adjustment_wac[$key];

            if($stockadjustmentMaster['adjustmentType']==1){
                $data['currentWareHouseStock'] = isset($previousWareHouseStock["currentStock"]) && !empty($previousWareHouseStock["currentStock"]) ? $previousWareHouseStock["currentStock"] : 0;
            }else{
                $data['currentWareHouseStock'] = $adjustment_Stock[$key];
            }
            $data['adjustmentWac'] = ($adjustment_wac[$key] - $currentWac[$key]);
            if($stockadjustmentMaster['adjustmentType']==1){
                $data['adjustmentWareHouseStock'] = 0;
            }else{
                $data['adjustmentWareHouseStock'] = ($adjustment_Stock[$key] - $currentWareHouseStock[$key]);
            }
            //$data['adjustmentWareHouseStock'] = ($adjustment_Stock[$key] - $currentWareHouseStock[$key]);
            if($stockadjustmentMaster['adjustmentType']==1){
                $data['adjustmentStock'] = 0;
            }else{
                $data['adjustmentStock'] = ($adjustment_Stock[$key] - $currentWareHouseStock[$key]);
            }
            //$data['adjustmentStock'] = ($adjustment_Stock[$key] - $currentWareHouseStock[$key]);
            $data['currentStock'] = $prevItemMasterTotal['currentStock']+$data['adjustmentStock'];

            //print_r($data);

            $previousTotal = ($data['previousStock'] * $data['previousWac']);
            $newTotal = ($data['currentStock'] * $data['currentWac']);


            /*$prevItemMasterStock = $prevItemMasterTotal["currentStock"];
            $prevItemMasterWac = $prevItemMasterTotal["companyLocalWacAmount"];
            $prevItemMasterTotal = $prevItemMasterTotal["prevItemMasterTotal"];
            $total = (($data['adjustmentStock'] + $prevItemMasterStock) * $data['currentWac']) - $prevItemMasterTotal;*/

            $prevItemMasterStock = $currentWareHouseStock[$key];
            $prevItemMasterWac = $currentWac[$key];
            $adjustmentStock = $adjustment_Stock[$key];
            $adjustmentWac = $adjustment_wac[$key];
            $total = (($adjustmentStock * $adjustmentWac) - ($prevItemMasterStock * $prevItemMasterWac));

            //$data['totalValue'] = ($newTotal - $previousTotal);
            $data['totalValue'] = ($data['currentStock'] * $data['currentWac']) - ($data['previousStock'] * $data['previousWac']);
            $data['comments'] = '';

            $this->db->insert('srp_erp_stockadjustmentdetails', $data);
            $last_id = $this->db->insert_id();

            if ($item_data['mainCategory'] == 'Inventory' || $item_data['mainCategory'] == 'Non Inventory') {
                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', $data['itemAutoID']);
                $this->db->where('wareHouseAutoID', $stockadjustmentMaster['wareHouseAutoID']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

                if (empty($warehouseitems)) {
                    if ($data['previousWareHouseStock'] == 0) {
                        $data_arr = array(
                            'wareHouseAutoID' => $stockadjustmentMaster['wareHouseAutoID'],
                            'wareHouseLocation' => $stockadjustmentMaster['wareHouseLocation'],
                            'wareHouseDescription' => $stockadjustmentMaster['wareHouseDescription'],
                            'itemAutoID' => $data['itemAutoID'],
                            'itemSystemCode' => $data['itemSystemCode'],
                            'itemDescription' => $data['itemDescription'],
                            'unitOfMeasureID' => $data['unitOfMeasureID'],
                            'unitOfMeasure' => $data['unitOfMeasure'],
                            'currentStock' => 0,
                            'companyID' => $this->common_data['company_data']['company_id'],
                            'companyCode' => $this->common_data['company_data']['company_code'],
                        );

                        $this->db->insert('srp_erp_warehouseitems', $data_arr);
                    }

                }
            }


            /*sub item master config : multiple add scanario */
            $adjustedStock = $data['adjustmentStock'];

            if ($item_data['isSubitemExist'] == 1) {

                if ($data['previousStock'] < $data['currentStock']) {
                    /* Add Stock */
                    $qty = $adjustedStock;

                    $subData['uom'] = $data['unitOfMeasure'];
                    $subData['uomID'] = $data['unitOfMeasureID'];
                    $subData['grv_detailID'] = $last_id;
                    $subData['warehouseAutoID'] = $stockadjustmentMaster['wareHouseAutoID'];
                    $this->add_sub_itemMaster_tmpTbl($qty, $itemAutoID, $stockAdjustmentAutoID, $last_id, 'SA', $item_data['itemSystemCode'], $subData);
                }

            }

            /*end of sub item master config */

        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('error' => 1, 'e', 'Stock Adjustment Details : Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('error' => 0, 's', 'Stock Adjustment Details : Saved Successfully.');
        }
    }

    function save_stock_transfer_detail()
    {
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        if (!empty($this->input->post('stockTransferDetailsID'))) {
            $this->db->select('stockTransferAutoID,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_stocktransferdetails');
            $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID')));
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
            $this->db->where('stockTransferDetailsID !=', trim($this->input->post('stockTransferDetailsID')));
            $order_detail = $this->db->get()->row_array();

            if (!empty($order_detail)) {
                return array('w', 'Stock Transfer Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }
        $this->db->trans_start();
        $segment = explode('|', trim($this->input->post('a_segment')));
        $uom = explode('|', $this->input->post('uom'));
        $item_data = fetch_item_data(trim($this->input->post('itemAutoID')));
        $projectID = trim($this->input->post('projectID'));
        $data['stockTransferAutoID'] = trim($this->input->post('stockTransferAutoID'));
        $data['itemAutoID'] = trim($this->input->post('itemAutoID'));
        $data['projectID'] = trim($this->input->post('projectID'));
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        $data['unitOfMeasure'] = trim($uom[0]);
        $data['unitOfMeasureID'] = trim($this->input->post('unitOfMeasureID'));
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['transfer_QTY'] = trim($this->input->post('transfer_QTY'));
        $data['segmentID'] = trim($segment[0]);
        $data['segmentCode'] = trim($segment[1]);
        $data['currentWareHouseStock'] = trim($this->input->post('currentWareHouseStockQty'));
        // $data['modifiedPCID']            = $this->common_data['current_pc'];
        // $data['modifiedUserID']          = $this->common_data['current_userID'];
        // $data['modifiedUserName']        = $this->common_data['current_user'];
        // $data['modifiedDateTime']        = $this->common_data['current_date'];

        $this->db->select('to_wareHouseAutoID,to_wareHouseLocation,to_wareHouseDescription');
        $this->db->from('srp_erp_stocktransfermaster');
        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID')));
        $master = $this->db->get()->row_array();

        $this->db->select('itemAutoID');
        $this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
        $this->db->where('wareHouseAutoID', $master['to_wareHouseAutoID']);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

        if (empty($warehouseitems)) {
            $data_arr = array(
                'wareHouseAutoID' => $master['to_wareHouseAutoID'],
                'wareHouseLocation' => $master['to_wareHouseLocation'],
                'wareHouseDescription' => $master['to_wareHouseDescription'],
                'itemAutoID' => $data['itemAutoID'],
                'itemSystemCode' => $data['itemSystemCode'],
                'itemDescription' => $data['itemDescription'],
                'unitOfMeasureID' => $data['defaultUOMID'],
                'unitOfMeasure' => $data['defaultUOM'],
                'currentStock' => 0,
                'companyID' => $this->common_data['company_data']['company_id'],
                'companyCode' => $this->common_data['company_data']['company_code'],
            );
            $this->db->insert('srp_erp_warehouseitems', $data_arr);
        }

        if (trim($this->input->post('stockTransferDetailsID'))) {
            $this->db->where('stockTransferDetailsID', trim($this->input->post('stockTransferDetailsID')));
            $this->db->update('srp_erp_stocktransferdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Stock Transfer Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Stock Transfer Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Updated Successfully.');
            }
        } else {
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }
            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['currentStock'] = $item_data['currentStock'];
            $data['totalValue'] = ($data['currentlWacAmount'] * $data['transfer_QTY']);

            $this->db->insert('srp_erp_stocktransferdetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Stock Transfer Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'Stock Transfer Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Saved Successfully.');
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_stock_transfer_detail_multiple()
    {
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $stockTransferDetailsID = $this->input->post('stockTransferDetailsID');
        $stockTransferAutoID = $this->input->post('stockTransferAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $currentWareHouseStockQty = $this->input->post('currentWareHouseStockQty');
        $transfer_QTY = $this->input->post('transfer_QTY');
        $projectID = $this->input->post('projectID');
        $a_segment = $this->input->post('a_segment');

        $this->db->trans_start();
        foreach ($itemAutoIDs as $key => $itemAutoID) {
            if (!$stockTransferDetailsID) {
                $this->db->select('stockTransferAutoID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_stocktransferdetails');
                $this->db->where('stockTransferAutoID', $stockTransferAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();

                if (!empty($order_detail)) {
                    return array('error' => 1, 'w', 'Stock Transfer Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);
            $data['stockTransferAutoID'] = $stockTransferAutoID;
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            $data['unitOfMeasure'] = trim($uomEx[0]);
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['transfer_QTY'] = $transfer_QTY[$key];
            $data['segmentID'] = trim($segment[0]);
            $data['segmentCode'] = trim($segment[1]);
            $data['currentWareHouseStock'] = $currentWareHouseStockQty[$key];

            $this->db->select('to_wareHouseAutoID,to_wareHouseLocation,to_wareHouseDescription,from_wareHouseAutoID');
            $this->db->from('srp_erp_stocktransfermaster');
            $this->db->where('stockTransferAutoID', $stockTransferAutoID);
            $master = $this->db->get()->row_array();

            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $master['to_wareHouseAutoID']);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

            $this->db->select('wareHouseAutoID,warehouseType,WIPGLAutoID');
            $this->db->from('srp_erp_warehousemaster');
            $this->db->where('wareHouseAutoID', $master['from_wareHouseAutoID']);
            $fromWarehouseGl = $this->db->get()->row_array();

            $this->db->select('wareHouseAutoID,warehouseType,WIPGLAutoID');
            $this->db->from('srp_erp_warehousemaster');
            $this->db->where('wareHouseAutoID', $master['to_wareHouseAutoID']);
            $toWarehouseGl = $this->db->get()->row_array();

            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $master['to_wareHouseAutoID'],
                    'wareHouseLocation' => $master['to_wareHouseLocation'],
                    'wareHouseDescription' => $master['to_wareHouseDescription'],
                    'itemAutoID' => $data['itemAutoID'],
                    'itemSystemCode' => $data['itemSystemCode'],
                    'itemDescription' => $data['itemDescription'],
                    'unitOfMeasureID' => $data['defaultUOMID'],
                    'unitOfMeasure' => $data['defaultUOM'],
                    'currentStock' => 0,
                    'companyID' => $this->common_data['company_data']['company_id'],
                    'companyCode' => $this->common_data['company_data']['company_code'],
                );
                $this->db->insert('srp_erp_warehouseitems', $data_arr);
            }

            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }

            if ($fromWarehouseGl['warehouseType'] == 2) {
                $data['fromWarehouseType'] = 2;
                $data['fromWarehouseWIPGLAutoID'] = $fromWarehouseGl['WIPGLAutoID'];
            }

            if ($toWarehouseGl['warehouseType'] == 2) {
                $data['toWarehouseType'] = 2;
                $data['toWarehouseWIPGLAutoID'] = $toWarehouseGl['WIPGLAutoID'];
            }

            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['currentStock'] = $item_data['currentStock'];
            $data['totalValue'] = ($data['currentlWacAmount'] * $data['transfer_QTY']);
            $this->db->insert('srp_erp_stocktransferdetails', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('error' => 1, 'e', 'Stock Transfer Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('error' => 0, 's', 'Stock Transfer Detail :  Saved Successfully.');
        }

    }

    function conversionRateUOM($umo, $default_umo)
    {
        $this->db->select('UnitID');
        $this->db->where('UnitShortCode', $default_umo);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $masterUnitID = $this->db->get('srp_erp_unit_of_measure')->row('UnitID');

        $this->db->select('UnitID');
        $this->db->where('UnitShortCode', $umo);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $subUnitID = $this->db->get('srp_erp_unit_of_measure')->row('UnitID');

        $this->db->select('conversion');
        $this->db->from('srp_erp_unitsconversion');
        $this->db->where('masterUnitID', $masterUnitID);
        $this->db->where('subUnitID', $subUnitID);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        return $this->db->get()->row('conversion');
    }

    function fetch_item_detail($id)
    {
        $this->db->select('*');
        $this->db->where('itemAutoID', $id);
        $this->db->from('srp_erp_itemmaster');
        return $this->db->get()->row_array();
    }

    function fetch_material_item_detail()
    {
        $itemIssueAutoID = $this->input->post('itemIssueAutoID');
        $data['detail'] = $this->db->query("SELECT
	`srp_erp_itemissuedetails`.*, `srp_erp_itemmaster`.`isSubitemExist`,
	`srp_erp_itemissuemaster`.`wareHouseAutoID`,
srp_erp_warehouseitems.currentStock as stock,srp_erp_materialrequest.MRCode as MRCode
FROM
	`srp_erp_itemissuedetails`
LEFT JOIN `srp_erp_itemmaster` ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_itemissuedetails`.`itemAutoID`
LEFT JOIN `srp_erp_itemissuemaster` ON `srp_erp_itemissuemaster`.`itemIssueAutoID` = `srp_erp_itemissuedetails`.`itemIssueAutoID`
LEFT JOIN `srp_erp_materialrequest` ON `srp_erp_materialrequest`.`mrAutoID` = `srp_erp_itemissuedetails`.`mrAutoID`
JOIN `srp_erp_warehouseitems` ON `srp_erp_warehouseitems`.`itemAutoID` = `srp_erp_itemissuedetails`.`itemAutoID`
AND srp_erp_itemissuemaster.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID
WHERE
	`srp_erp_itemissuedetails`.`itemIssueAutoID` = '$itemIssueAutoID' ")->result_array();
        return $data;
    }

    function fetch_return_direct_details()
    {

        $this->db->select('concat(grvPrimaryCode," - ",srp_erp_stockreturndetails.itemSystemCode ) as itemSystemCode,srp_erp_stockreturndetails.itemDescription,srp_erp_stockreturndetails.unitOfMeasure,srp_erp_stockreturnmaster.transactionCurrencyDecimalPlaces,srp_erp_stockreturndetails.currentlWacAmount,srp_erp_stockreturndetails.return_Qty,srp_erp_stockreturndetails.totalValue,srp_erp_stockreturndetails.stockReturnDetailsID,srp_erp_stockreturndetails.type, ,srp_erp_itemmaster.isSubitemExist,srp_erp_stockreturnmaster.wareHouseAutoID');
        $this->db->from('srp_erp_stockreturndetails');
        $this->db->where('srp_erp_stockreturndetails.stockReturnAutoID', trim($this->input->post('stockReturnAutoID')));
        $this->db->join('srp_erp_stockreturnmaster', 'srp_erp_stockreturndetails.stockReturnAutoID = srp_erp_stockreturnmaster.stockReturnAutoID');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_stockreturndetails.itemAutoID', 'left');
        $this->db->join('srp_erp_grvmaster', 'srp_erp_grvmaster.grvAutoID = srp_erp_stockreturndetails.grvAutoID', 'left');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function load_material_item_detail()
    {
        $itemIssueDetailID = $this->input->post('itemIssueDetailID');
        $result = $this->db->query("SELECT
	srp_erp_itemissuedetails.*, `srp_erp_warehouseitems`.`currentStock` AS Stock
FROM
	`srp_erp_itemissuedetails`
JOIN `srp_erp_itemissuemaster` ON `srp_erp_itemissuemaster`.`itemIssueAutoID` = `srp_erp_itemissuedetails`.`itemIssueAutoID`
JOIN `srp_erp_warehouseitems` ON `srp_erp_warehouseitems`.`itemAutoID` = `srp_erp_itemissuedetails`.`itemAutoID`
AND srp_erp_itemissuemaster.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID
WHERE
	`itemIssueDetailID` = '$itemIssueDetailID'")->row_array();
        return $result;
    }

    function load_stock_transfer_item_detail()
    {
        $this->db->select('st.stockTransferDetailsID,w.currentStock as wareHouseStock,st.itemAutoID,st.itemDescription,st.itemSystemCode, st.defaultUOMID,st.unitOfMeasureID,st.transfer_QTY,st.segmentID,st.segmentCode,st.projectID');
        $this->db->from('srp_erp_stocktransferdetails st');
        $this->db->join('srp_erp_warehouseitems w', 'w.itemAutoID = st.itemAutoID');
        $this->db->where('wareHouseAutoID', trim($this->input->post('location')));
        $this->db->where('stockTransferDetailsID', trim($this->input->post('stockTransferDetailsID')));
        return $this->db->get()->row_array();
    }

    function delete_material_item()
    {
        $id = $this->input->post('itemIssueDetailID');

        $this->db->select('*');
        $this->db->from('srp_erp_itemissuedetails');
        $this->db->where('itemIssueDetailID', $id);
        $detail_arr = $this->db->get()->row_array();

        /** update sub item master */

        $dataTmp['isSold'] = null;
        $dataTmp['soldDocumentAutoID'] = null;
        $dataTmp['soldDocumentDetailID'] = null;
        $dataTmp['soldDocumentID'] = null;
        $dataTmp['modifiedPCID'] = current_pc();
        $dataTmp['modifiedUserID'] = current_userID();
        $dataTmp['modifiedDatetime'] = format_date_mysql_datetime();

        $this->db->where('soldDocumentAutoID', $detail_arr['itemIssueAutoID']);
        $this->db->where('soldDocumentDetailID', $detail_arr['itemIssueDetailID']);
        $this->db->where('soldDocumentID', 'MI');
        $this->db->update('srp_erp_itemmaster_sub', $dataTmp);


        /** end update sub item master */
        $this->db->where('itemIssueDetailID', $id);
        $result = $this->db->delete('srp_erp_itemissuedetails');
        if ($result) {
            $this->session->set_flashdata('s', 'Records Deleted Successfully');
            return true;
        }
    }

    function load_adjustment_item_detail()
    {
        $this->db->select('sa.stockAdjustmentDetailsAutoID,sa.itemDescription,sa.itemSystemCode,w.currentStock as wareHouseStock,sa.currentWac as currentWacstock,im.companyLocalWacAmount as LocalWacAmount,sa.defaultUOMID,sa.segmentID,sa.segmentCode,sa.currentStock,sa.currentWac,sa.itemAutoID,sa.projectID,sa.adjustmentStock as adjustmentStock,sa.previousWareHouseStock as previousWareHouseStock,im.currentStock as itemcurrentStock');
        $this->db->from('srp_erp_stockadjustmentdetails sa');
        $this->db->join('srp_erp_warehouseitems w', 'w.itemAutoID = sa.itemAutoID');
        $this->db->join('srp_erp_itemmaster im', 'im.itemAutoID = sa.itemAutoID');
        $this->db->where('stockAdjustmentDetailsAutoID', trim($this->input->post('stockAdjustmentDetailsAutoID')));
        $this->db->where('wareHouseAutoID', trim($this->input->post('location')));
        return $this->db->get()->row_array();
    }

    function delete_adjustment_item()
    {
        $id = $this->input->post('stockAdjustmentDetailsAutoID');
        $this->db->where('stockAdjustmentDetailsAutoID', $id);
        $result = $this->db->delete('srp_erp_stockadjustmentdetails');
        $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentDetailID' => $id, 'receivedDocumentID' => 'SA'));

        if ($result) {
            $this->session->set_flashdata('s', 'Records Deleted Successfully');
            return true;
        }
    }

    function delete_material_issue_header()
    {
        /*$this->db->where('itemIssueAutoID', $this->input->post('itemIssueAutoID'));
        $result = $this->db->delete('srp_erp_itemissuemaster');
        $this->db->where('itemIssueAutoID', $this->input->post('itemIssueAutoID'));
        $result = $this->db->delete('srp_erp_itemissuedetails');
        if ($result) {
            $this->session->set_flashdata('s', 'Records Deleted Successfully');
            return true;
        }*/

        /*  $this->db->select('*');
          $this->db->from('srp_erp_itemissuedetails');
          $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID')));
          $datas = $this->db->get()->row_array();
          if ($datas) {
              $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
              return true;
          } else {
              $data = array(
                  'isDeleted' => 1,
                  'deletedEmpID' => current_userID(),
                  'deletedDate' => current_date(),
              );*/
        $data = array(
            'isDeleted' => 1,
            'deletedEmpID' => current_userID(),
            'deletedDate' => current_date(),
        );

        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID')));
        $this->db->update('srp_erp_itemissuemaster', $data);
        $this->session->set_flashdata('s', 'Deleted Successfully.');
        return true;


    }

    function material_item_confirmation()
    {
        $this->db->select('itemIssueAutoID');
        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID')));
        $this->db->from('srp_erp_itemissuedetails');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {
            $this->db->select('itemIssueAutoID');
            $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID')));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_itemissuemaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $this->db->select('itemIssueCode,documentID,DATE_FORMAT(issueDate, "%Y") as invYear,DATE_FORMAT(issueDate, "%m") as invMonth,companyFinanceYearID');
                $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID')));
                $this->db->from('srp_erp_itemissuemaster');
                $master_dt = $this->db->get()->row_array();

                $this->load->library('sequence');
                if($master_dt['itemIssueCode'] == "0" || empty($master_dt['itemIssueCode'])) {
                    $pvCd = array(
                        'itemIssueCode' => $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth'])
                    );
                    $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID')));
                    $this->db->update('srp_erp_itemissuemaster', $pvCd);
                }

                $this->load->library('approvals');
                $this->db->select('itemIssueAutoID, itemIssueCode');
                $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID')));
                $this->db->from('srp_erp_itemissuemaster');
                $app_data = $this->db->get()->row_array();

                $sql = "SELECT(srp_erp_itemissuedetails.qtyIssued / srp_erp_itemissuedetails.conversionRateUOM) AS qty,srp_erp_warehouseitems.itemAutoID,srp_erp_warehouseitems.currentStock,(srp_erp_warehouseitems.currentStock - (srp_erp_itemissuedetails.qtyIssued / srp_erp_itemissuedetails.conversionRateUOM)) AS stock FROM srp_erp_itemissuedetails INNER JOIN srp_erp_itemissuemaster  ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID INNER JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_itemissuedetails.itemAutoID AND srp_erp_itemissuemaster.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID where srp_erp_itemissuedetails.itemIssueAutoID = '{$this->input->post('itemIssueAutoID')}' Having stock < 0";
                $item_low_qty = $this->db->query($sql)->result_array();
                if (!empty($item_low_qty)) {
                    /*$this->session->set_flashdata('w', 'Some Item quantities are not sufficient to confirm this transaction');
                    return array('status' => false, 'data' => 'Some Item quantities are not sufficient to confirm this transaction');*/
                    return array('error' => 1, 'message' => 'Some Item quantities are not sufficient to confirm this transaction!', 'itemAutoID' => $item_low_qty);
                }

                /** item Master Sub check */


                $documentDetailID = trim($this->input->post('itemIssueAutoID'));
                $validate = $this->validate_itemMasterSub($documentDetailID, 'MI');

                /** end of item master sub */

                if ($validate) {
                    $approvals_status = $this->approvals->CreateApproval('MI', $app_data['itemIssueAutoID'], $app_data['itemIssueCode'], 'Material issue', 'srp_erp_itemissuemaster', 'itemIssueAutoID');
                    if ($approvals_status == 1) {
                        $data = array(
                            'confirmedYN' => 1,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user']
                        );

                        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID')));
                        $this->db->update('srp_erp_itemissuemaster', $data);
                    } else if ($approvals_status == 3) {
                        return array('error' => 2, 'message' => 'There are no users exist to perform approval for this document');
                    } else {
                        //return false;
                        return array('error' => 1, 'message' => 'Document confirmation failed!');
                    }
                    return array('error' => 0, 'message' => 'Document Confirmed Successfully.');
                    //return array('status' => true);
                } else {
                    return array('error' => 1, 'message' => 'Please complete sub item configurations<br/><br/> Please add sub item/s before confirm this document.');

                }

            }
        }

    }

    function stock_transfer_confirmation()
    {
        $this->db->select('stockTransferAutoID');
        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID')));
        $this->db->from('srp_erp_stocktransferdetails');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {
            $this->db->select('stockTransferAutoID');
            $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID')));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_stocktransfermaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $this->load->library('approvals');
                $this->db->select('stockTransferAutoID, stockTransferCode');
                $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID')));
                $this->db->from('srp_erp_stocktransfermaster');
                $app_data = $this->db->get()->row_array();


                $sql = "SELECT(srp_erp_stocktransferdetails.transfer_QTY / srp_erp_stocktransferdetails.conversionRateUOM) AS qty,srp_erp_warehouseitems.currentStock,srp_erp_warehouseitems.itemAutoID,(srp_erp_warehouseitems.currentStock - (srp_erp_stocktransferdetails.transfer_QTY / srp_erp_stocktransferdetails.conversionRateUOM)) AS stock FROM srp_erp_stocktransferdetails INNER JOIN srp_erp_stocktransfermaster  ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID INNER JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_stocktransferdetails.itemAutoID AND srp_erp_stocktransfermaster.from_wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID where srp_erp_stocktransferdetails.stockTransferAutoID = '{$this->input->post('stockTransferAutoID')}' Having stock < 0";

                $item_low_qty = $this->db->query($sql)->result_array();

                if (!empty($item_low_qty)) {
                    /*$this->session->set_flashdata('w', 'Some Item quantities are not sufficient to confirm this transaction');
                    return array('status' => false, 'data' => 'Some Item quantities are not sufficient to confirm this transaction');*/
                    return array('error' => 1, 'message' => 'Some Item quantities are not sufficient to confirm this transaction.', 'itemAutoID' => $item_low_qty);
                }

                /** item Master Sub check */


                $documentDetailID = trim($this->input->post('stockTransferAutoID'));
                $validate = $this->validate_itemMasterSub($documentDetailID, 'ST');

                /** end of item master sub */

                if ($validate) {
                    $approvals_status = $this->approvals->CreateApproval('ST', $app_data['stockTransferAutoID'], $app_data['stockTransferCode'], 'Stock Transfer', 'srp_erp_stocktransfermaster', 'stockTransferAutoID');
                    if ($approvals_status == 1) {
                        $data = array(
                            'confirmedYN' => 1,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user']
                        );

                        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID')));
                        $this->db->update('srp_erp_stocktransfermaster', $data);
                    } else if ($approvals_status == 3) {
                        return array('error' => 2, 'message' => 'There are no users exist to perform approval for this document');
                    } else {
                        return array('error' => 1, 'message' => 'Document confirmation failed');
                    }
                    //return array('status' => true);
                    return array('error' => 0, 'message' => 'Document Confirmed Successfully.');
                    //return array('status' => true);
                } else {
                    return array('error' => 1, 'message' => 'Please complete sub item configurations<br/><br/> Please add sub item/s before confirm this document.');

                }

            }

        }
    }

    function stock_adjustment_confirmation()
    {
        $this->db->select('stockAdjustmentAutoID');
        $this->db->where('stockAdjustmentAutoID', trim($this->input->post('stockAdjustmentAutoID')));
        $this->db->from('srp_erp_stockadjustmentdetails');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!.');
        } else {
            $this->db->select('stockAdjustmentAutoID');
            $this->db->where('stockAdjustmentAutoID', trim($this->input->post('stockAdjustmentAutoID')));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_stockadjustmentmaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $id = trim($this->input->post('stockAdjustmentAutoID'));
                //$isProductReference_completed = $this->isProductReference_completed_document_SA($id);
                $isProductReference_completed = isMandatory_completed_document($id, 'SA');
                if ($isProductReference_completed == 0) {


                    /** item Master Sub check : sub item already added items check box are ch */

                    $validate = $this->validate_itemMasterSub($id, 'SA');

                    /** validation skipped until they found this. we have to do the both side of check in the validate_itemMasterSub method and have to change the query */

                    if ($validate) {

                        $this->load->library('approvals');
                        $this->db->select('stockAdjustmentAutoID, stockAdjustmentCode');
                        $this->db->where('stockAdjustmentAutoID', $id);
                        $this->db->from('srp_erp_stockadjustmentmaster');
                        $app_data = $this->db->get()->row_array();
                        $approvals_status = $this->approvals->CreateApproval('SA', $app_data['stockAdjustmentAutoID'], $app_data['stockAdjustmentCode'], 'Stock Adjustment', 'srp_erp_stockadjustmentmaster', 'stockAdjustmentAutoID');
                        if ($approvals_status == 1) {
                            $data = array(
                                'confirmedYN' => 1,
                                'confirmedDate' => $this->common_data['current_date'],
                                'confirmedByEmpID' => $this->common_data['current_userID'],
                                'confirmedByName' => $this->common_data['current_user']
                            );

                            $this->db->where('stockAdjustmentAutoID', $id);
                            $this->db->update('srp_erp_stockadjustmentmaster', $data);
                        }else if($approvals_status == 3)
                        {
                            return array('error' => 2, 'message' => 'There are no users exist to perform approval for this document');

                        }else
                        {
                            return array('error' => 1, 'message' => 'Document confirmation failed');

                        }
                        //return array('status' => true);
                        return array('error' => 0, 'message' => 'Document confirmed successfully');
                    } else {
                        return array('error' => 1, 'message' => 'Please complete sub item configurations<br/> Please add sub item/s before confirm this document.');
                    }


                } else {
                    return array('error' => 1, 'message' => 'Please complete you sub item configuration, fill all the mandatory fields!.');
                }

            }

        }
    }


    function isProductReference_completed_document_SA($id)
    {
        $result = $this->db->query("SELECT
                        count(itemMaster.subItemAutoID) AS countTotal
                    FROM
                        srp_erp_stockadjustmentmaster stockMaster
                    LEFT JOIN srp_erp_stockadjustmentdetails stockAdjustment ON stockAdjustment.stockAdjustmentAutoID = stockMaster.stockAdjustmentAutoID
                    LEFT JOIN srp_erp_itemmaster_subtemp itemMaster ON itemMaster.receivedDocumentDetailID = stockAdjustment.stockAdjustmentDetailsAutoID
                    LEFT JOIN srp_erp_itemmaster im ON im.itemAutoID = itemMaster.itemAutoID
                    WHERE
                        stockMaster.stockAdjustmentAutoID = '" . $id . "'
                    AND ( ISNULL( itemMaster.productReferenceNo )
                        OR itemMaster.productReferenceNo = ''
                    )
                    AND im.isSubitemExist = 1")->row_array();

        return $result['countTotal'];

    }

    function stock_return_confirmation()
    {
        $this->db->select('stockReturnAutoID');
        $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID')));
        $this->db->from('srp_erp_stockreturndetails');
        $record = $this->db->get()->result_array();
        if (empty($record)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {
            $this->db->select('stockReturnAutoID');
            $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID')));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_stockreturnmaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $this->db->select('documentID,stockReturnCode,companyFinanceYearID,DATE_FORMAT(returnDate, "%Y") as invYear,DATE_FORMAT(returnDate, "%m") as invMonth');
                $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID')));
                $this->db->from('srp_erp_stockreturnmaster');
                $master_dt = $this->db->get()->row_array();

                $this->load->library('sequence');
                if($master_dt['stockReturnCode'] == "0" || empty($master_dt['stockReturnCode'])) {
                    $pvCd = array(
                        'stockReturnCode' => $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth'])
                    );
                    $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID')));
                    $this->db->update('srp_erp_stockreturnmaster', $pvCd);
                }

                $this->load->library('approvals');
                $this->db->select('stockReturnAutoID, stockReturnCode');
                $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID')));
                $this->db->from('srp_erp_stockreturnmaster');
                $app_data = $this->db->get()->row_array();

                /** item Master Sub check */


                $documentDetailID = trim($this->input->post('stockReturnAutoID'));
                $validate = $this->validate_itemMasterSub($documentDetailID, 'SR');

                /** end of item master sub */

                if ($validate) {
                    $sql = "SELECT(srp_erp_stockreturndetails.return_Qty / srp_erp_stockreturndetails.conversionRateUOM) AS qty,srp_erp_warehouseitems.currentStock,(srp_erp_warehouseitems.currentStock - (srp_erp_stockreturndetails.return_Qty / srp_erp_stockreturndetails.conversionRateUOM)) AS stock FROM srp_erp_stockreturndetails INNER JOIN srp_erp_stockreturnmaster  ON srp_erp_stockreturnmaster.stockReturnAutoID = srp_erp_stockreturndetails.stockReturnAutoID INNER JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_stockreturndetails.itemAutoID AND srp_erp_stockreturnmaster.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID where srp_erp_stockreturndetails.stockReturnAutoID = '{$this->input->post('stockReturnAutoID')}' Having stock < 0";

                    $item_low_qty = $this->db->query($sql)->result_array();


                    if (!empty($item_low_qty)) {
                        //$this->session->set_flashdata('w', 'Some Item quantities are not sufficient to confirm this transaction');
                        //return array('status' => false, 'data' => 'Some Item quantities are not sufficient to confirm this transaction');
                        return array('error' => 1, 'message' => 'Some Item quantities are not sufficient to confirm this transaction');
                    }
                } else {
                    return array('error' => 1, 'message' => 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');

                }


                $approvals_status = $this->approvals->CreateApproval('SR', $app_data['stockReturnAutoID'], $app_data['stockReturnCode'], 'Stock Return', 'srp_erp_stockreturnmaster', 'stockReturnAutoID');
                if ($approvals_status == 1) {
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user']
                    );

                    $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID')));
                    $this->db->update('srp_erp_stockreturnmaster', $data);
                } else if ($approvals_status == 3) {
                    return array('error' => 2, 'message' => 'There are no users exist to perform approval for this document');
                } else {
                    return array('error' => 1, 'message' => 'Document confirmation failed!');
                }
                return array('error' => 0, 'message' => 'document successfully confirmed');

            }

        }
        //return array('status' => true);
    }

    function fetch_warehouse_item()
    {
        $this->db->select('wareHouseAutoID,wareHouseCode, wareHouseLocation, wareHouseDescription');
        $this->db->from('srp_erp_itemissuemaster');
        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID')));
        $query = $this->db->get()->row_array();

        $this->db->select('srp_erp_warehouseitems.currentStock,companyLocalWacAmount,wareHouseDescription,wareHouseLocation');
        $this->db->from('srp_erp_warehouseitems');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_warehouseitems.itemAutoID');
        $this->db->where('wareHouseAutoID', $query['wareHouseAutoID']);
        $this->db->where('srp_erp_warehouseitems.itemAutoID', trim($this->input->post('itemAutoID')));
        $this->db->where('srp_erp_warehouseitems.companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get()->row_array();
        if (!empty($data)) {
            return array('status' => true, 'currentStock' => $data['currentStock'], 'WacAmount' => $data['companyLocalWacAmount']);
        } else {
            $this->session->set_flashdata('w', "Item doesn't exists in the selected warehouse " . $query["wareHouseDescription"] . " ( " . $query["wareHouseLocation"] . " )");
            return array('status' => false);
        }
    }

    function fetch_st_warehouse_item()
    {
        $this->db->select('from_wareHouseAutoID,form_wareHouseCode, form_wareHouseLocation, form_wareHouseDescription');
        $this->db->from('srp_erp_stocktransfermaster');
        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID')));
        $query = $this->db->get()->row_array();

        $this->db->select('srp_erp_warehouseitems.currentStock,companyLocalWacAmount,wareHouseDescription,wareHouseLocation');
        $this->db->from('srp_erp_warehouseitems');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_warehouseitems.itemAutoID');
        $this->db->where('wareHouseAutoID', $query['from_wareHouseAutoID']);
        $this->db->where('srp_erp_warehouseitems.itemAutoID', trim($this->input->post('itemAutoID')));
        $this->db->where('srp_erp_warehouseitems.companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get()->row_array();
        if (!empty($data)) {
            return array('error' => 0, 'message' => '', 'status' => true, 'currentStock' => $data['currentStock'], 'WacAmount' => $data['companyLocalWacAmount']);
        } else {
            $this->session->set_flashdata('w', "Item doesn't exists in the selected warehouse " . $query['form_wareHouseDescription'] . " ( " . $query['form_wareHouseLocation'] . " )");
            return array('status' => false, 'error' => 2, 'message' => "Item doesn't exists in the selected warehouse " . $query['form_wareHouseDescription'] . " ( " . $query['form_wareHouseLocation'] . " )");
        }
    }

    function fetch_warehouse_item_adjustment()
    {
        $this->db->select('wareHouseAutoID,wareHouseCode, wareHouseLocation, wareHouseDescription');
        $this->db->from('srp_erp_stockadjustmentmaster');
        $this->db->where('stockAdjustmentAutoID', trim($this->input->post('stockAdjustmentAutoID')));
        $query = $this->db->get()->row_array();

        $this->db->select('currentStock');
        $this->db->from('srp_erp_warehouseitems');
        $this->db->where('wareHouseAutoID', $query['wareHouseAutoID']);
        $this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $currentStock = $this->db->get()->row('currentStock');

        $this->db->select('companyLocalWacAmount');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $currentWac = $this->db->get()->row('companyLocalWacAmount');

        if (!empty($currentStock)) {
            return array('status' => true, 'currentStock' => $currentStock, 'currentWac' => $currentWac);
        } else {
            return array('status' => true, 'currentStock' => 0, 'currentWac' => $currentWac);
            //$this->session->set_flashdata('w', 'The item you entered is not exists in this warehouse ' . $query['wareHouseDescription'] . ' ( ' . $query['wareHouseLocation'] . ' ) . you can not issue this item from this warehouse.');
            return array('status' => false);
        }
    }


    function save_material_issue_approval()
    {
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_code = trim($this->input->post('itemIssueAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        $comments = trim($this->input->post('comments'));

        $this->db->select('wareHouseAutoID');
        $this->db->from('srp_erp_itemissuemaster');
        $this->db->where('itemIssueAutoID', $system_code);
        $frmWareHouse = $this->db->get()->row_array();

        $this->db->select('(srp_erp_warehouseitems.currentStock-srp_erp_itemissuedetails.qtyIssued) as stockDiff,srp_erp_itemmaster.itemSystemCode,srp_erp_itemmaster.itemDescription,srp_erp_warehouseitems.currentStock as availableStock');
        $this->db->from('srp_erp_itemissuedetails');
        $this->db->where('srp_erp_itemissuedetails.itemIssueAutoID', $system_code);
        $this->db->where('srp_erp_warehouseitems.companyID', current_companyID());
        $this->db->where('srp_erp_warehouseitems.wareHouseAutoID', $frmWareHouse['wareHouseAutoID']);
        $this->db->join('srp_erp_warehouseitems', 'srp_erp_itemissuedetails.itemAutoID = srp_erp_warehouseitems.itemAutoID');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemissuedetails.itemAutoID = srp_erp_itemmaster.itemAutoID');
        $this->db->having('stockDiff < 0');
        $items_arr = $this->db->get()->result_array();
        if ($status != 1) {
            $items_arr = '';
        }
        if (!$items_arr) {
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'MI');
            if ($approvals_status == 1) {
                $this->db->select('*,COALESCE(SUM(srp_erp_itemissuedetails.qtyIssued),0) AS qtyUpdatedIssued,COALESCE(SUM(srp_erp_itemissuedetails.totalValue),0) AS UpdatedTotalValue');
                $this->db->from('srp_erp_itemissuedetails');
                $this->db->where('srp_erp_itemissuedetails.itemIssueAutoID', $system_code);
                $this->db->join('srp_erp_itemissuemaster', 'srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID');
                $this->db->group_by('srp_erp_itemissuedetails.itemAutoID');
                $details_arr = $this->db->get()->result_array();

                $item_arr = array();
                $itemledger_arr = array();
                $transaction_loc_tot = 0;
                $company_rpt_tot = 0;
                $supplier_cr_tot = 0;
                $company_loc_tot = 0;
                for ($i = 0; $i < count($details_arr); $i++) {
                    if ($details_arr[$i]['itemCategory'] == 'Inventory' or $details_arr[$i]['itemCategory'] == 'Non Inventory') {
                        $item = fetch_item_data($details_arr[$i]['itemAutoID']);
                        $item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                        $item_arr[$i]['currentStock'] = ($item['currentStock'] - ($details_arr[$i]['qtyUpdatedIssued'] / $details_arr[$i]['conversionRateUOM']));
                        $item_arr[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                        $item_arr[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                        $qty = ($details_arr[$i]['qtyUpdatedIssued'] / $details_arr[$i]['conversionRateUOM']);
                        $itemSystemCode = $details_arr[$i]['itemAutoID'];
                        $location = $details_arr[$i]['wareHouseLocation'];
                        $wareHouseAutoID = $details_arr[$i]['wareHouseAutoID'];
                        $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock -{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemSystemCode}'");

                        $itemledger_arr[$i]['documentID'] = $details_arr[$i]['documentID'];
                        $itemledger_arr[$i]['documentCode'] = $details_arr[$i]['documentID'];
                        $itemledger_arr[$i]['documentAutoID'] = $details_arr[$i]['itemIssueAutoID'];
                        $itemledger_arr[$i]['documentSystemCode'] = $details_arr[$i]['itemIssueCode'];
                        $itemledger_arr[$i]['documentDate'] = $details_arr[$i]['issueDate'];
                        $itemledger_arr[$i]['referenceNumber'] = $details_arr[$i]['issueRefNo'];
                        $itemledger_arr[$i]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                        $itemledger_arr[$i]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                        $itemledger_arr[$i]['FYBegin'] = $details_arr[$i]['FYBegin'];
                        $itemledger_arr[$i]['FYEnd'] = $details_arr[$i]['FYEnd'];
                        $itemledger_arr[$i]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                        $itemledger_arr[$i]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                        $itemledger_arr[$i]['wareHouseAutoID'] = $details_arr[$i]['wareHouseAutoID'];
                        $itemledger_arr[$i]['wareHouseCode'] = $details_arr[$i]['wareHouseCode'];
                        $itemledger_arr[$i]['wareHouseLocation'] = $details_arr[$i]['wareHouseLocation'];
                        $itemledger_arr[$i]['wareHouseDescription'] = $details_arr[$i]['wareHouseDescription'];
                        $itemledger_arr[$i]['projectID'] = $details_arr[$i]['projectID'];
                        $itemledger_arr[$i]['projectExchangeRate'] = $details_arr[$i]['projectExchangeRate'];
                        $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                        $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                        $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                        $itemledger_arr[$i]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                        $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                        $itemledger_arr[$i]['transactionQTY'] = ($details_arr[$i]['qtyUpdatedIssued'] * -1);
                        $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                        $itemledger_arr[$i]['currentStock'] = $item_arr[$i]['currentStock'];
                        $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                        $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                        $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                        $itemledger_arr[$i]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                        $itemledger_arr[$i]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                        $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                        $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                        $itemledger_arr[$i]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                        $itemledger_arr[$i]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                        $itemledger_arr[$i]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                        $itemledger_arr[$i]['PLDescription'] = $details_arr[$i]['PLDescription'];
                        $itemledger_arr[$i]['PLType'] = $details_arr[$i]['PLType'];
                        $itemledger_arr[$i]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                        $itemledger_arr[$i]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                        $itemledger_arr[$i]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                        $itemledger_arr[$i]['BLDescription'] = $details_arr[$i]['BLDescription'];
                        $itemledger_arr[$i]['BLType'] = $details_arr[$i]['BLType'];
                        $itemledger_arr[$i]['transactionCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                        $itemledger_arr[$i]['transactionCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                        $itemledger_arr[$i]['transactionExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                        $itemledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                        $itemledger_arr[$i]['transactionAmount'] = (round($details_arr[$i]['UpdatedTotalValue'], $itemledger_arr[$i]['transactionCurrencyDecimalPlaces']) * -1);
                        $itemledger_arr[$i]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                        $itemledger_arr[$i]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                        $itemledger_arr[$i]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                        $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                        $itemledger_arr[$i]['companyLocalAmount'] = (round(($details_arr[$i]['UpdatedTotalValue'] / $details_arr[$i]['companyLocalExchangeRate']), $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']) * -1);
                        $itemledger_arr[$i]['companyLocalWacAmount'] = round($details_arr[$i]['currentlWacAmount'], $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                        $itemledger_arr[$i]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                        $itemledger_arr[$i]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                        $itemledger_arr[$i]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                        $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                        $itemledger_arr[$i]['companyReportingAmount'] = (round(($details_arr[$i]['UpdatedTotalValue'] / $details_arr[$i]['companyReportingExchangeRate']), $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']) * -1);
                        $itemledger_arr[$i]['companyReportingWacAmount'] = round(($itemledger_arr[$i]['companyLocalWacAmount'] / $itemledger_arr[$i]['companyReportingExchangeRate']), $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                        $itemledger_arr[$i]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                        $itemledger_arr[$i]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                        $itemledger_arr[$i]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                        $itemledger_arr[$i]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                        $itemledger_arr[$i]['approvedYN'] = $details_arr[$i]['approvedYN'];
                        $itemledger_arr[$i]['approvedDate'] = $details_arr[$i]['approvedDate'];
                        $itemledger_arr[$i]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                        $itemledger_arr[$i]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];
                        $itemledger_arr[$i]['segmentID'] = $details_arr[$i]['segmentID'];
                        $itemledger_arr[$i]['segmentCode'] = $details_arr[$i]['segmentCode'];
                        $itemledger_arr[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                        $itemledger_arr[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                        $itemledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                        $itemledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                        $itemledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                        $itemledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                        $itemledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                        $itemledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                        $itemledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                        $itemledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                        $itemledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                    }
                }

                if (!empty($item_arr)) {
                    $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
                }

                if (!empty($itemledger_arr)) {
                    $itemledger_arr = array_values($itemledger_arr);
                    $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                }

                $this->load->model('Double_entry_model');
                $double_entry = $this->Double_entry_model->fetch_double_entry_material_issue_data($system_code, 'MI');
                for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                    $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['itemIssueAutoID'];
                    $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                    $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['itemIssueCode'];
                    $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['issueDate'];
                    $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['issueType'];
                    $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['issueDate'];
                    $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['issueDate']));
                    $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comment'];
                    $generalledger_arr[$i]['chequeNumber'] = '';
                    $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['partyContractID'] = '';
                    $generalledger_arr[$i]['partyType'] = 'EMP';
                    $generalledger_arr[$i]['partyAutoID'] = $double_entry['master_data']['employeeID'];
                    $generalledger_arr[$i]['partySystemCode'] = $double_entry['master_data']['employeeCode'];
                    $generalledger_arr[$i]['partyName'] = $double_entry['master_data']['employeeName'];
                    $generalledger_arr[$i]['partyCurrencyID'] = '';//$double_entry['master_data']['supplierCurrency'];
                    $generalledger_arr[$i]['partyCurrency'] = '';//$double_entry['master_data']['supplierCurrency'];
                    $generalledger_arr[$i]['partyExchangeRate'] = '';//$double_entry['master_data']['supplierCurrencyExchangeRate'];
                    $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = '';//$double_entry['master_data']['supplierCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                    $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                    $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                    $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                    $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                    $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                    if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                        $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                    }
                    $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                    //$generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['partyExchangeRate']),$generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                    $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                    $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                    $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                    $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                    $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                    $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                    $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                    $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                    $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                    $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                    $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                    $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                    $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];

                    $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                    $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                    $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                }

                if (!empty($generalledger_arr)) {
                    $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                }
                //$this->session->set_flashdata('s', 'Material Issue Approval Successfully.');
            }
            /*else {
                $this->session->set_flashdata('s', 'Material Issue Approval : Level ' . $level_id . ' Successfully.');
            }*/

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Material Issue Approval Failed.', 1);
            } else {
                $this->db->trans_commit();
                return array('s', 'Material Issue Approved Successfully.', 1);
            }
        } else {
            return array('e', 'Item quantities are insufficient.', $items_arr);
        }
    }

    function save_stock_adjustment_approval()
    {
        $this->load->library('approvals');
        $system_code = trim($this->input->post('stockAdjustmentAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        $comments = trim($this->input->post('comments'));

        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'SA');
        if ($approvals_status == 1) {
            $this->db->select('*,srp_erp_stockadjustmentdetails.segmentID as segID,srp_erp_stockadjustmentdetails.segmentCode as segCode');
            $this->db->from('srp_erp_stockadjustmentdetails');
            $this->db->where('srp_erp_stockadjustmentdetails.stockAdjustmentAutoID', $system_code);
            $this->db->join('srp_erp_stockadjustmentmaster',
                'srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID');
            $details_arr = $this->db->get()->result_array();

            $item_arr = array();
            $itemledger_arr = array();
            $transaction_loc_tot = 0;
            $company_rpt_tot = 0;
            $supplier_cr_tot = 0;
            $company_loc_tot = 0;
            for ($i = 0; $i < count($details_arr); $i++) {
                $this->db->select('currentStock');
                $this->db->from('srp_erp_itemmaster');
                $this->db->where('itemAutoID', $details_arr[$i]['itemAutoID']);
                $prevItemMasterTotal = $this->db->get()->row_array();

                $item = fetch_item_data($details_arr[$i]['itemAutoID']);
                $qty = $details_arr[$i]['adjustmentStock'];
                $item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                if($details_arr[$i]['adjustmentType']==0) {
                    $item_arr[$i]['currentStock'] = ($item['currentStock'] + $qty);
                }else{
                    $item_arr[$i]['currentStock'] = $prevItemMasterTotal['currentStock'];
                }
                $item_arr[$i]['companyLocalWacAmount'] = round(($details_arr[$i]['currentWac'] / $details_arr[$i]['companyLocalExchangeRate']),
                    $details_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $item_arr[$i]['companyReportingWacAmount'] = round(($details_arr[$i]['currentWac'] / $details_arr[$i]['companyReportingExchangeRate']),
                    $details_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                $itemSystemCode = $details_arr[$i]['itemAutoID'];
                $location = $details_arr[$i]['wareHouseLocation'];
                $wareHouseAutoID = $details_arr[$i]['wareHouseAutoID'];
                if($details_arr[$i]['adjustmentType']==0){
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = currentStock + {$details_arr[$i]['adjustmentWareHouseStock']}  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemSystemCode}'");
                }
                $itemledger_arr[$i]['documentID'] = $details_arr[$i]['documentID'];
                $itemledger_arr[$i]['documentAutoID'] = $details_arr[$i]['stockAdjustmentAutoID'];
                $itemledger_arr[$i]['documentCode'] = $details_arr[$i]['documentID'];
                $itemledger_arr[$i]['documentSystemCode'] = $details_arr[$i]['stockAdjustmentCode'];
                $itemledger_arr[$i]['documentDate'] = $details_arr[$i]['stockAdjustmentDate'];
                $itemledger_arr[$i]['referenceNumber'] = $details_arr[$i]['referenceNo'];
                $itemledger_arr[$i]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                $itemledger_arr[$i]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                $itemledger_arr[$i]['FYBegin'] = $details_arr[$i]['FYBegin'];
                $itemledger_arr[$i]['FYEnd'] = $details_arr[$i]['FYEnd'];
                $itemledger_arr[$i]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                $itemledger_arr[$i]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                $itemledger_arr[$i]['wareHouseAutoID'] = $details_arr[$i]['wareHouseAutoID'];
                $itemledger_arr[$i]['wareHouseCode'] = $details_arr[$i]['wareHouseCode'];
                $itemledger_arr[$i]['wareHouseLocation'] = $details_arr[$i]['wareHouseLocation'];
                $itemledger_arr[$i]['wareHouseDescription'] = $details_arr[$i]['wareHouseDescription'];
                $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                $itemledger_arr[$i]['transactionQTY'] = $qty;
                $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                $itemledger_arr[$i]['currentStock'] = $item_arr[$i]['currentStock'];
                $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                $itemledger_arr[$i]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                $itemledger_arr[$i]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                $itemledger_arr[$i]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                $itemledger_arr[$i]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                $itemledger_arr[$i]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                $itemledger_arr[$i]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                $itemledger_arr[$i]['PLDescription'] = $details_arr[$i]['PLDescription'];
                $itemledger_arr[$i]['PLType'] = $details_arr[$i]['PLType'];
                $itemledger_arr[$i]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                $itemledger_arr[$i]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                $itemledger_arr[$i]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                $itemledger_arr[$i]['BLDescription'] = $details_arr[$i]['BLDescription'];
                $itemledger_arr[$i]['BLType'] = $details_arr[$i]['BLType'];
                $itemledger_arr[$i]['transactionCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                $itemledger_arr[$i]['transactionCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                $itemledger_arr[$i]['transactionExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                $itemledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                $itemledger_arr[$i]['transactionAmount'] = (round($details_arr[$i]['totalValue'],
                    $itemledger_arr[$i]['transactionCurrencyDecimalPlaces']));
                $itemledger_arr[$i]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                $itemledger_arr[$i]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                $itemledger_arr[$i]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                $itemledger_arr[$i]['companyLocalAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['companyLocalExchangeRate']),
                    $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']));
                $itemledger_arr[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                $itemledger_arr[$i]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                $itemledger_arr[$i]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                $itemledger_arr[$i]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                $itemledger_arr[$i]['companyReportingAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['companyReportingExchangeRate']),
                    $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']));
                $itemledger_arr[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];

                $itemledger_arr[$i]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                $itemledger_arr[$i]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                $itemledger_arr[$i]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                $itemledger_arr[$i]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                $itemledger_arr[$i]['approvedYN'] = $details_arr[$i]['approvedYN'];
                $itemledger_arr[$i]['approvedDate'] = $details_arr[$i]['approvedDate'];
                $itemledger_arr[$i]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                $itemledger_arr[$i]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];
                /*$itemledger_arr[$i]['segmentID'] = $details_arr[$i]['segmentID'];
                $itemledger_arr[$i]['segmentCode'] = $details_arr[$i]['segmentCode'];*/
                $itemledger_arr[$i]['segmentID'] = $details_arr[$i]['segID'];
                $itemledger_arr[$i]['segmentCode'] = $details_arr[$i]['segCode'];
                $itemledger_arr[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                $itemledger_arr[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                $itemledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $itemledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                $itemledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                $itemledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                $itemledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                $itemledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $itemledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $itemledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $itemledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
            }

            if (!empty($item_arr)) {
                $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
            }
            if (!empty($itemledger_arr)) {
                $itemledger_arr = array_values($itemledger_arr);
                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
            }

            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_sa_data($system_code, 'SA');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['stockAdjustmentAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['stockAdjustmentCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['stockAdjustmentDate'];
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['stockAdjustmentDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m",
                    strtotime($double_entry['master_data']['stockAdjustmentDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comment'];
                $generalledger_arr[$i]['chequeNumber'] = '';
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                // $generalledger_arr[$i]['partyType']                                 = 'SUP';
                // $generalledger_arr[$i]['partyAutoID']                               = $double_entry['master_data']['supplierID'];
                // $generalledger_arr[$i]['partySystemCode']                           = $double_entry['master_data']['supplierSystemCode'];
                // $generalledger_arr[$i]['partyName']                                 = $double_entry['master_data']['supplierName'];
                // $generalledger_arr[$i]['partyCurrency']                             = $double_entry['master_data']['supplierCurrency'];
                // $generalledger_arr[$i]['partyExchangeRate']                         = $double_entry['master_data']['supplierCurrencyExchangeRate'];
                // $generalledger_arr[$i]['partyCurrencyDecimalPlaces']                = $double_entry['master_data']['supplierCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                    $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                }
                $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']),
                    $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                //$generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']*$generalledger_arr[$i]['partyExchangeRate']),4);
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : NULL;
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];

                $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
            }

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            }

            $maxLevel = $this->approvals->maxlevel('SA');

            $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? TRUE : FALSE;
            /** update sub item master : shafry */
            if ($isFinalLevel) {
                $masterID = $this->input->post('stockAdjustmentAutoID');
                $result = $this->db->query("SELECT  * FROM srp_erp_itemmaster_subtemp WHERE receivedDocumentAutoID = '" . $masterID . "'")->result_array();
                if (!empty($result)) {
                    $i = 0;
                    foreach ($result as $item) {
                        unset($result[$i]['subItemAutoID']);
                        $i++;
                    }
                    $this->db->insert_batch('srp_erp_itemmaster_sub', $result);
                    $this->db->delete('srp_erp_itemmaster_subtemp',
                        array('receivedDocumentAutoID' => $masterID, 'receivedDocumentID' => 'SA'));

                }
            }
            $this->session->set_flashdata('s', 'Stock adjustment Approval Successfully.');
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return TRUE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }

    function save_stock_transfer_approval()
    {
        $this->load->library('approvals');
        $system_code = trim($this->input->post('stockTransferAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        $comments = trim($this->input->post('comments'));

        $maxLevel = $this->approvals->maxlevel('ST');

        $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? true : false;

        $this->db->select('from_wareHouseAutoID');
        $this->db->from('srp_erp_stocktransfermaster');
        $this->db->where('stockTransferAutoID', $system_code);
        $frmWareHouse = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_stocktransfermaster');
        $this->db->where('stockTransferAutoID', $system_code);
        $master = $this->db->get()->row_array();

        $this->db->select('(srp_erp_warehouseitems.currentStock-srp_erp_stocktransferdetails.transfer_QTY) as stockDiff,srp_erp_itemmaster.itemSystemCode,srp_erp_itemmaster.itemDescription,srp_erp_warehouseitems.currentStock as availableStock');
        $this->db->from('srp_erp_stocktransferdetails');
        $this->db->where('srp_erp_stocktransferdetails.stockTransferAutoID', $system_code);
        $this->db->where('srp_erp_warehouseitems.companyID', current_companyID());
        $this->db->where('srp_erp_warehouseitems.wareHouseAutoID', $frmWareHouse['from_wareHouseAutoID']);
        $this->db->join('srp_erp_warehouseitems', 'srp_erp_stocktransferdetails.itemAutoID = srp_erp_warehouseitems.itemAutoID');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_stocktransferdetails.itemAutoID = srp_erp_itemmaster.itemAutoID');
        $this->db->having('stockDiff < 0');
        $items_arr = $this->db->get()->result_array();
        if ($status != 1) {
            $items_arr = '';
        }
        if (!$items_arr) {
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'ST');
            if ($approvals_status == 1) {
                $this->db->select('*');
                $this->db->from('srp_erp_stocktransferdetails');
                $this->db->where('srp_erp_stocktransferdetails.stockTransferAutoID', $system_code);
                $this->db->join('srp_erp_stocktransfermaster', 'srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID');
                $details_arr = $this->db->get()->result_array();

                $item_arr = array();
                $itemledger_arr = array();
                $transaction_loc_tot = 0;
                $company_rpt_tot = 0;
                $supplier_cr_tot = 0;
                $company_loc_tot = 0;
                $x = 0;
                for ($i = 0; $i < count($details_arr); $i++) {
                    $item = fetch_item_data($details_arr[$i]['itemAutoID']);
                    $item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $item_arr[$i]['currentStock'] = $item['currentStock'];
                    $item_arr[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                    $item_arr[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                    $qty = ($details_arr[$i]['transfer_QTY'] / $details_arr[$i]['conversionRateUOM']);
                    $itemSystemCode = $details_arr[$i]['itemAutoID'];
                    $location = $details_arr[$i]['from_wareHouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock -{$qty}) WHERE wareHouseAutoID='{$location}' and itemAutoID='{$itemSystemCode}'");
                    $location = $details_arr[$i]['to_wareHouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$qty})  WHERE wareHouseAutoID='{$location}' and itemAutoID='{$itemSystemCode}'");

                    $itemledger_arr[$x]['documentID'] = $details_arr[$i]['documentID'];
                    $itemledger_arr[$x]['documentCode'] = $details_arr[$i]['documentID'];
                    $itemledger_arr[$x]['documentAutoID'] = $details_arr[$i]['stockTransferAutoID'];
                    $itemledger_arr[$x]['documentSystemCode'] = $details_arr[$i]['stockTransferCode'];
                    $itemledger_arr[$x]['documentDate'] = $details_arr[$i]['tranferDate'];
                    $itemledger_arr[$x]['referenceNumber'] = $details_arr[$i]['referenceNo'];
                    $itemledger_arr[$x]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                    $itemledger_arr[$x]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                    $itemledger_arr[$x]['FYBegin'] = $details_arr[$i]['FYBegin'];
                    $itemledger_arr[$x]['FYEnd'] = $details_arr[$i]['FYEnd'];
                    $itemledger_arr[$x]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                    $itemledger_arr[$x]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                    $itemledger_arr[$x]['wareHouseAutoID'] = $details_arr[$i]['from_wareHouseAutoID'];
                    $itemledger_arr[$x]['wareHouseCode'] = $details_arr[$i]['form_wareHouseCode'];
                    $itemledger_arr[$x]['wareHouseLocation'] = $details_arr[$i]['form_wareHouseLocation'];
                    $itemledger_arr[$x]['wareHouseDescription'] = $details_arr[$i]['form_wareHouseLocation'];
                    $itemledger_arr[$x]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$x]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$x]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$x]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr[$x]['transactionQTY'] = ($qty * -1);
                    $itemledger_arr[$x]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr[$x]['currentStock'] = $item_arr[$i]['currentStock'];
                    $itemledger_arr[$x]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$x]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$x]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$x]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                    $itemledger_arr[$x]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr[$x]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                    $itemledger_arr[$x]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                    $itemledger_arr[$x]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr[$x]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                    $itemledger_arr[$x]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                    $itemledger_arr[$x]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                    $itemledger_arr[$x]['PLDescription'] = $details_arr[$i]['PLDescription'];
                    $itemledger_arr[$x]['PLType'] = $details_arr[$i]['PLType'];
                    $itemledger_arr[$x]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                    $itemledger_arr[$x]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                    $itemledger_arr[$x]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                    $itemledger_arr[$x]['BLDescription'] = $details_arr[$i]['BLDescription'];
                    $itemledger_arr[$x]['BLType'] = $details_arr[$i]['BLType'];
                    $itemledger_arr[$x]['transactionCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr[$x]['transactionCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr[$x]['transactionExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr[$x]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$x]['transactionAmount'] = (round($details_arr[$i]['totalValue'], $itemledger_arr[$x]['transactionCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$x]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr[$x]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr[$x]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr[$x]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$x]['companyLocalAmount'] = (round(($details_arr[$i]['totalValue'] / $itemledger_arr[$x]['companyLocalExchangeRate']), $itemledger_arr[$x]['companyLocalCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$x]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                    $itemledger_arr[$x]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                    $itemledger_arr[$x]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                    $itemledger_arr[$x]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                    $itemledger_arr[$x]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr[$x]['companyReportingAmount'] = (round(($details_arr[$i]['totalValue'] / $itemledger_arr[$x]['companyReportingExchangeRate']), $itemledger_arr[$x]['companyReportingCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$x]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                    $itemledger_arr[$x]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                    $itemledger_arr[$x]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                    $itemledger_arr[$x]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                    $itemledger_arr[$x]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                    $itemledger_arr[$x]['approvedYN'] = $details_arr[$i]['approvedYN'];
                    $itemledger_arr[$x]['approvedDate'] = $details_arr[$i]['approvedDate'];
                    $itemledger_arr[$x]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                    $itemledger_arr[$x]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];
                    $itemledger_arr[$x]['segmentID'] = $details_arr[$i]['segmentID'];
                    $itemledger_arr[$x]['segmentCode'] = $details_arr[$i]['segmentCode'];
                    $itemledger_arr[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                    $itemledger_arr[$x]['companyCode'] = $this->common_data['company_data']['company_code'];
                    $itemledger_arr[$x]['createdUserGroup'] = $this->common_data['user_group'];
                    $itemledger_arr[$x]['createdPCID'] = $this->common_data['current_pc'];
                    $itemledger_arr[$x]['createdUserID'] = $this->common_data['current_userID'];
                    $itemledger_arr[$x]['createdDateTime'] = $this->common_data['current_date'];
                    $itemledger_arr[$x]['createdUserName'] = $this->common_data['current_user'];
                    $itemledger_arr[$x]['modifiedPCID'] = $this->common_data['current_pc'];
                    $itemledger_arr[$x]['modifiedUserID'] = $this->common_data['current_userID'];
                    $itemledger_arr[$x]['modifiedDateTime'] = $this->common_data['current_date'];
                    $itemledger_arr[$x]['modifiedUserName'] = $this->common_data['current_user'];
                    $x++;

                    $itemledger_arr[$x]['documentID'] = $details_arr[$i]['documentID'];
                    $itemledger_arr[$x]['documentCode'] = $details_arr[$i]['documentID'];
                    $itemledger_arr[$x]['documentAutoID'] = $details_arr[$i]['stockTransferAutoID'];
                    $itemledger_arr[$x]['documentSystemCode'] = $details_arr[$i]['stockTransferCode'];
                    $itemledger_arr[$x]['documentDate'] = $details_arr[$i]['tranferDate'];
                    $itemledger_arr[$x]['referenceNumber'] = $details_arr[$i]['referenceNo'];
                    $itemledger_arr[$x]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                    $itemledger_arr[$x]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                    $itemledger_arr[$x]['FYBegin'] = $details_arr[$i]['FYBegin'];
                    $itemledger_arr[$x]['FYEnd'] = $details_arr[$i]['FYEnd'];
                    $itemledger_arr[$x]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                    $itemledger_arr[$x]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                    $itemledger_arr[$x]['wareHouseAutoID'] = $details_arr[$i]['to_wareHouseAutoID'];
                    $itemledger_arr[$x]['wareHouseCode'] = $details_arr[$i]['to_wareHouseCode'];
                    $itemledger_arr[$x]['wareHouseLocation'] = $details_arr[$i]['to_wareHouseLocation'];
                    $itemledger_arr[$x]['wareHouseDescription'] = $details_arr[$i]['to_wareHouseLocation'];
                    $itemledger_arr[$x]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$x]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$x]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$x]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr[$x]['transactionQTY'] = $qty;
                    $itemledger_arr[$x]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr[$x]['currentStock'] = $item_arr[$i]['currentStock'];
                    $itemledger_arr[$x]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$x]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$x]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$x]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                    $itemledger_arr[$x]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr[$x]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                    $itemledger_arr[$x]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                    $itemledger_arr[$x]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr[$x]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                    $itemledger_arr[$x]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                    $itemledger_arr[$x]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                    $itemledger_arr[$x]['PLDescription'] = $details_arr[$i]['PLDescription'];
                    $itemledger_arr[$x]['PLType'] = $details_arr[$i]['PLType'];
                    $itemledger_arr[$x]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                    $itemledger_arr[$x]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                    $itemledger_arr[$x]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                    $itemledger_arr[$x]['BLDescription'] = $details_arr[$i]['BLDescription'];
                    $itemledger_arr[$x]['BLType'] = $details_arr[$i]['BLType'];
                    $itemledger_arr[$x]['transactionCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr[$x]['transactionCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr[$x]['transactionExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr[$x]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$x]['transactionAmount'] = round($details_arr[$i]['totalValue'], $itemledger_arr[$x]['transactionCurrencyDecimalPlaces']);
                    $itemledger_arr[$x]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr[$x]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr[$x]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr[$x]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$x]['companyLocalAmount'] = round(($details_arr[$i]['totalValue'] / $itemledger_arr[$x]['companyLocalExchangeRate']), $itemledger_arr[$x]['companyLocalCurrencyDecimalPlaces']);
                    $itemledger_arr[$x]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                    $itemledger_arr[$x]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                    $itemledger_arr[$x]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                    $itemledger_arr[$x]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                    $itemledger_arr[$x]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr[$x]['companyReportingAmount'] = round(($details_arr[$i]['totalValue'] / $itemledger_arr[$x]['companyReportingExchangeRate']), $itemledger_arr[$x]['companyReportingCurrencyDecimalPlaces']);
                    $itemledger_arr[$x]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                    $itemledger_arr[$x]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                    $itemledger_arr[$x]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                    $itemledger_arr[$x]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                    $itemledger_arr[$x]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                    $itemledger_arr[$x]['approvedYN'] = $details_arr[$i]['approvedYN'];
                    $itemledger_arr[$x]['approvedDate'] = $details_arr[$i]['approvedDate'];
                    $itemledger_arr[$x]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                    $itemledger_arr[$x]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];
                    $itemledger_arr[$x]['segmentID'] = $details_arr[$i]['segmentID'];
                    $itemledger_arr[$x]['segmentCode'] = $details_arr[$i]['segmentCode'];
                    $itemledger_arr[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                    $itemledger_arr[$x]['companyCode'] = $this->common_data['company_data']['company_code'];
                    $itemledger_arr[$x]['createdUserGroup'] = $this->common_data['user_group'];
                    $itemledger_arr[$x]['createdPCID'] = $this->common_data['current_pc'];
                    $itemledger_arr[$x]['createdUserID'] = $this->common_data['current_userID'];
                    $itemledger_arr[$x]['createdDateTime'] = $this->common_data['current_date'];
                    $itemledger_arr[$x]['createdUserName'] = $this->common_data['current_user'];
                    $itemledger_arr[$x]['modifiedPCID'] = $this->common_data['current_pc'];
                    $itemledger_arr[$x]['modifiedUserID'] = $this->common_data['current_userID'];
                    $itemledger_arr[$x]['modifiedDateTime'] = $this->common_data['current_date'];
                    $itemledger_arr[$x]['modifiedUserName'] = $this->common_data['current_user'];
                    $x++;
                }

                if (!empty($item_arr)) {
                    $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
                }
                if (!empty($itemledger_arr)) {
                    $itemledger_arr = array_values($itemledger_arr);
                    $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                }

                $this->load->model('Double_entry_model');
                $double_entry = $this->Double_entry_model->fetch_double_entry_stock_transfer_data($system_code, 'ST');
                for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                    $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['stockTransferAutoID'];
                    $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                    $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['stockTransferCode'];
                    $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['tranferDate'];
                    $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['itemType'];
                    $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['tranferDate'];
                    $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['tranferDate']));
                    $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comment'];
                    $generalledger_arr[$i]['chequeNumber'] = '';
                    $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['partyContractID'] = '';
                    $generalledger_arr[$i]['partyType'] = '';//'EMP';
                    $generalledger_arr[$i]['partyAutoID'] = '';//$double_entry['master_data']['employeeID'];
                    $generalledger_arr[$i]['partySystemCode'] = '';//$double_entry['master_data']['employeeCode'];
                    $generalledger_arr[$i]['partyName'] = '';//$double_entry['master_data']['employeeName'];
                    $generalledger_arr[$i]['partyCurrency'] = '';//$double_entry['master_data']['supplierCurrency'];
                    $generalledger_arr[$i]['partyExchangeRate'] = '';//$double_entry['master_data']['supplierCurrencyExchangeRate'];
                    $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = '';//$double_entry['master_data']['supplierCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                    $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                    $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                    $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                    $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                    $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                    if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                        $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                    }
                    $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                    //$generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['partyExchangeRate']),$generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                    $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                    $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                    $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                    $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                    $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                    $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                    $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                    $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                    $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                    $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                    $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                    $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];

                    $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                    $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                    $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                }

                if (!empty($generalledger_arr)) {
                    $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                }

                /** update sub item master sub : shafry */
                if ($isFinalLevel) {
                    $masterID = $this->input->post('stockTransferAutoID');


                    $masterData = $this->db->query("SELECT  * FROM srp_erp_stocktransfermaster WHERE stockTransferAutoID = '" . $masterID . "'")->row_array();

                    $result = $this->db->query("SELECT  * FROM srp_erp_itemmaster_sub WHERE soldDocumentID = 'ST' AND isSold='1' AND soldDocumentAutoID = '" . $masterID . "'")->result_array();


                    if (!empty($result)) {
                        $i = 0;
                        foreach ($result as $item) {
                            $result[$i]['receivedDocumentID'] = 'ST';
                            $result[$i]['receivedDocumentAutoID'] = $item['soldDocumentAutoID'];
                            $result[$i]['receivedDocumentDetailID'] = $item['soldDocumentDetailID'];
                            $result[$i]['isSold'] = null;
                            $result[$i]['soldDocumentID'] = null;
                            $result[$i]['soldDocumentDetailID'] = null;
                            $result[$i]['soldDocumentAutoID'] = null;

                            $result[$i]['wareHouseAutoID'] = $masterData['to_wareHouseAutoID'];

                            unset($result[$i]['subItemAutoID']);
                            $i++;
                        }


                        $this->db->insert_batch('srp_erp_itemmaster_sub', $result);

                    }
                }
                $itemAutoIDarry = array();
                foreach ($details_arr as $value) {
                    array_push($itemAutoIDarry, $value['itemAutoID']);
                }
                $companyID = current_companyID();
                $exceededitems = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID IN  (" . join($itemAutoIDarry) . ") AND companyID= $companyID AND warehouseAutoID= '" . $master ['to_wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                $exceededMatchID = 0;
                if (!empty($exceededitems)) {
                    $this->load->library('sequence');
                    $exceededmatch['documentID'] = "EIM";
                    $exceededmatch['documentDate'] = $master ['tranferDate'];
                    $exceededmatch['orginDocumentID'] = $master ['documentID'];
                    $exceededmatch['orginDocumentMasterID'] = $master ['stockTransferAutoID'];
                    $exceededmatch['orginDocumentSystemCode'] = $master ['stockTransferCode'];
                    $exceededmatch['companyFinanceYearID'] = $master ['companyFinanceYearID'];
                    $exceededmatch['companyID'] = current_companyID();
                    $exceededmatch['transactionCurrencyID'] = $master ['companyLocalCurrencyID'];
                    $exceededmatch['transactionCurrency'] = $master ['companyLocalCurrency'];
                    $exceededmatch['transactionExchangeRate'] = $master ['companyLocalExchangeRate'];
                    $exceededmatch['transactionCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                    $exceededmatch['companyLocalCurrencyID'] = $master ['companyLocalCurrencyID'];
                    $exceededmatch['companyLocalCurrency'] = $master ['companyLocalCurrency'];
                    $exceededmatch['companyLocalExchangeRate'] = $master ['companyLocalExchangeRate'];
                    $exceededmatch['companyLocalCurrencyDecimalPlaces'] = $master ['companyLocalCurrencyDecimalPlaces'];
                    $exceededmatch['companyReportingCurrencyID'] = $master ['companyReportingCurrencyID'];
                    $exceededmatch['companyReportingCurrency'] = $master ['companyReportingCurrency'];
                    $exceededmatch['companyReportingExchangeRate'] = $master ['companyReportingExchangeRate'];
                    $exceededmatch['companyReportingCurrencyDecimalPlaces'] = $master ['companyReportingCurrencyDecimalPlaces'];
                    $exceededmatch['companyFinanceYear'] = $master ['companyFinanceYear'];
                    $exceededmatch['FYBegin'] = $master ['FYBegin'];
                    $exceededmatch['FYEnd'] = $master ['FYEnd'];
                    $exceededmatch['FYPeriodDateFrom'] = $master ['FYPeriodDateFrom'];
                    $exceededmatch['FYPeriodDateTo'] = $master ['FYPeriodDateTo'];
                    $exceededmatch['companyFinancePeriodID'] = $master ['companyFinancePeriodID'];
                    $exceededmatch['createdUserGroup'] = $this->common_data['user_group'];
                    $exceededmatch['createdPCID'] = $this->common_data['current_pc'];
                    $exceededmatch['createdUserID'] = $this->common_data['current_userID'];
                    $exceededmatch['createdUserName'] = $this->common_data['current_user'];
                    $exceededmatch['createdDateTime'] = $this->common_data['current_date'];
                    $exceededmatch['documentSystemCode'] = $this->sequence->sequence_generator($exceededmatch['documentID']);
                    $this->db->insert('srp_erp_itemexceededmatch', $exceededmatch);
                    $exceededMatchID = $this->db->insert_id();
                }

                foreach ($details_arr as $itemid) {
                    $receivedQty = $itemid['transfer_QTY'];
                    $receivedQtyConverted = $itemid['transfer_QTY'] / $itemid['conversionRateUOM'];
                    $companyID = current_companyID();
                    $exceededitems = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID AND warehouseAutoID= '" . $master ['to_wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                    $itemCost = $this->db->query("SELECT  companyLocalWacAmount FROM srp_erp_itemmaster WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID")->row_array();
                    $sumqty = array_column($exceededitems, 'balanceQty');
                    $sumqty = array_sum($sumqty);
                    if (!empty($exceededitems)) {
                        foreach ($exceededitems as $exceededItemAutoID) {
                            if ($receivedQty > 0) {
                                $balanceQty = $exceededItemAutoID['balanceQty'];
                                $updatedQty = $exceededItemAutoID['updatedQty'];
                                $balanceQtyConverted = $exceededItemAutoID['balanceQty'] / $exceededItemAutoID['conversionRateUOM'];
                                $updatedQtyConverted = $exceededItemAutoID['updatedQty'] / $exceededItemAutoID['conversionRateUOM'];
                                if ($receivedQtyConverted > $balanceQtyConverted) {
                                    $qty = $receivedQty - $balanceQty;
                                    $qtyconverted = $receivedQtyConverted - $balanceQtyConverted;
                                    $receivedQty = $qty;
                                    $receivedQtyConverted = $qtyconverted;
                                    $exeed['balanceQty'] = 0;
                                    //$exeed['updatedQty'] = $updatedQty+$balanceQty;
                                    $exeed['updatedQty'] = ($updatedQtyConverted * $exceededItemAutoID['conversionRateUOM']) + ($balanceQtyConverted * $exceededItemAutoID['conversionRateUOM']);
                                    $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                    $this->db->update('srp_erp_itemexceeded', $exeed);

                                    $exceededmatchdetail['exceededMatchID'] = $exceededMatchID;
                                    $exceededmatchdetail['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                    $exceededmatchdetail['warehouseAutoID'] = $master['to_wareHouseAutoID'];
                                    $exceededmatchdetail['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                    $exceededmatchdetail['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                    $exceededmatchdetail['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                    $exceededmatchdetail['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                    $exceededmatchdetail['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                    $exceededmatchdetail['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                    $exceededmatchdetail['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                    $exceededmatchdetail['matchedQty'] = $balanceQtyConverted;
                                    $exceededmatchdetail['itemCost'] = $itemCost['companyLocalWacAmount'];
                                    $exceededmatchdetail['totalValue'] = $balanceQtyConverted * $exceededmatchdetail['itemCost'];
                                    $exceededmatchdetail['segmentID'] = $exceededItemAutoID['segmentID'];
                                    $exceededmatchdetail['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                    $exceededmatchdetail['createdUserGroup'] = $this->common_data['user_group'];
                                    $exceededmatchdetail['createdPCID'] = $this->common_data['current_pc'];
                                    $exceededmatchdetail['createdUserID'] = $this->common_data['current_userID'];
                                    $exceededmatchdetail['createdUserName'] = $this->common_data['current_user'];
                                    $exceededmatchdetail['createdDateTime'] = $this->common_data['current_date'];

                                    $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetail);

                                } else {
                                    $exeed['balanceQty'] = $balanceQtyConverted - $receivedQtyConverted;
                                    $exeed['updatedQty'] = $updatedQtyConverted + $receivedQtyConverted;
                                    $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                    $this->db->update('srp_erp_itemexceeded', $exeed);

                                    $exceededmatchdetails['exceededMatchID'] = $exceededMatchID;
                                    $exceededmatchdetails['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                    $exceededmatchdetails['warehouseAutoID'] = $master['to_wareHouseAutoID'];
                                    $exceededmatchdetails['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                    $exceededmatchdetails['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                    $exceededmatchdetails['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                    $exceededmatchdetails['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                    $exceededmatchdetails['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                    $exceededmatchdetails['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                    $exceededmatchdetails['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                    $exceededmatchdetails['matchedQty'] = $receivedQtyConverted;
                                    $exceededmatchdetails['itemCost'] = $itemCost['companyLocalWacAmount'];
                                    $exceededmatchdetails['totalValue'] = $receivedQtyConverted * $exceededmatchdetails['itemCost'];
                                    $exceededmatchdetails['segmentID'] = $exceededItemAutoID['segmentID'];
                                    $exceededmatchdetails['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                    $exceededmatchdetails['createdUserGroup'] = $this->common_data['user_group'];
                                    $exceededmatchdetails['createdPCID'] = $this->common_data['current_pc'];
                                    $exceededmatchdetails['createdUserID'] = $this->common_data['current_userID'];
                                    $exceededmatchdetails['createdUserName'] = $this->common_data['current_user'];
                                    $exceededmatchdetails['createdDateTime'] = $this->common_data['current_date'];
                                    $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetails);
                                    $receivedQty = $receivedQty - $exeed['updatedQty'];
                                    $receivedQtyConverted = $receivedQtyConverted - ($updatedQtyConverted + $receivedQtyConverted);
                                }
                            }
                        }
                    }
                }
                if (!empty($exceededitems)) {
                    exceed_double_entry($exceededMatchID);
                }
                //$this->session->set_flashdata('s', 'Stock Transfer Approval Successfully.');
            } /*else {
            $this->session->set_flashdata('s', 'Stock Transfer Approval : Level ' . $level_id . ' Successfully.');
        }*/

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Stock Transfer Approval Failed.', 1);
            } else {
                $this->db->trans_commit();
                return array('s', 'Stock Transfer Approval Successfully.', 1);
            }
        } else {
            return array('e', 'Item quantities are insufficient.', $items_arr);
        }
    }

    function save_stock_return_approval()
    {
        $this->load->library('approvals');
        $this->load->library('wac');
        $system_code = trim($this->input->post('stockReturnAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        $comments = trim($this->input->post('comments'));
        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'SR');
        if ($approvals_status == 1) {
            $this->db->select('*');
            $this->db->from('srp_erp_stockreturndetails');
            $this->db->where('srp_erp_stockreturndetails.stockReturnAutoID', $system_code);
            $this->db->join('srp_erp_stockreturnmaster', 'srp_erp_stockreturnmaster.stockReturnAutoID = srp_erp_stockreturndetails.stockReturnAutoID');
            $details_arr = $this->db->get()->result_array();

            $item_arr = array();
            $itemledger_arr = array();
            $transaction_loc_tot = 0;
            $company_rpt_tot = 0;
            $supplier_cr_tot = 0;
            $company_loc_tot = 0;
            for ($i = 0; $i < count($details_arr); $i++) {
                if ($details_arr[$i]['itemCategory'] == 'Inventory' or $details_arr[$i]['itemCategory'] == 'Non Inventory') {
                    $item = fetch_item_data($details_arr[$i]['itemAutoID']);
                    $qty = ($details_arr[$i]['return_Qty'] / $details_arr[$i]['conversionRateUOM']);
                    $wacAmount = $this->wac->wac_calculation_amounts($details_arr[$i]['itemAutoID'], $details_arr[$i]['unitOfMeasure'], ($details_arr[$i]['return_Qty'] * -1), $details_arr[$i]['transactionCurrency'], $details_arr[$i]['currentlWacAmount']); //get Local and reporitng Amount
                    /*$item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $item_arr[$i]['currentStock'] = ($item['currentStock'] - $qty);
                    $item_arr[$i]['companyLocalWacAmount'] = $wacAmount["companyLocalWacAmount"];
                    $item_arr[$i]['companyReportingWacAmount'] = $wacAmount["companyReportingWacAmount"];*/

                    $itemSystemCode = $details_arr[$i]['itemAutoID'];
                    $location = $details_arr[$i]['wareHouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$location}' and itemAutoID='{$itemSystemCode}'");
                    $itemledger_arr[$i]['documentID'] = $details_arr[$i]['documentID'];
                    $itemledger_arr[$i]['documentCode'] = $details_arr[$i]['documentID'];
                    $itemledger_arr[$i]['documentAutoID'] = $details_arr[$i]['stockReturnAutoID'];
                    $itemledger_arr[$i]['documentSystemCode'] = $details_arr[$i]['stockReturnCode'];
                    $itemledger_arr[$i]['documentDate'] = $details_arr[$i]['returnDate'];
                    $itemledger_arr[$i]['referenceNumber'] = $details_arr[$i]['referenceNo'];
                    $itemledger_arr[$i]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                    $itemledger_arr[$i]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                    $itemledger_arr[$i]['FYBegin'] = $details_arr[$i]['FYBegin'];
                    $itemledger_arr[$i]['FYEnd'] = $details_arr[$i]['FYEnd'];
                    $itemledger_arr[$i]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                    $itemledger_arr[$i]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                    $itemledger_arr[$i]['wareHouseAutoID'] = $details_arr[$i]['wareHouseAutoID'];
                    $itemledger_arr[$i]['wareHouseCode'] = $details_arr[$i]['wareHouseCode'];
                    $itemledger_arr[$i]['wareHouseLocation'] = $details_arr[$i]['wareHouseLocation'];
                    $itemledger_arr[$i]['wareHouseDescription'] = $details_arr[$i]['wareHouseDescription'];
                    $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr[$i]['transactionQTY'] = ($details_arr[$i]['return_Qty'] * -1);
                    $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr[$i]['currentStock'] = ($item['currentStock'] - $qty);
                    $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                    $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                    $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                    $itemledger_arr[$i]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                    $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                    $itemledger_arr[$i]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                    $itemledger_arr[$i]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                    $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                    $itemledger_arr[$i]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                    $itemledger_arr[$i]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                    $itemledger_arr[$i]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                    $itemledger_arr[$i]['PLDescription'] = $details_arr[$i]['PLDescription'];
                    $itemledger_arr[$i]['PLType'] = $details_arr[$i]['PLType'];
                    $itemledger_arr[$i]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                    $itemledger_arr[$i]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                    $itemledger_arr[$i]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                    $itemledger_arr[$i]['BLDescription'] = $details_arr[$i]['BLDescription'];
                    $itemledger_arr[$i]['BLType'] = $details_arr[$i]['BLType'];
                    $itemledger_arr[$i]['transactionCurrencyID'] = $details_arr[$i]['transactionCurrencyID'];
                    $itemledger_arr[$i]['transactionCurrency'] = $details_arr[$i]['transactionCurrency'];
                    $itemledger_arr[$i]['transactionExchangeRate'] = $details_arr[$i]['transactionExchangeRate'];
                    $itemledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['transactionCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['transactionAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['transactionExchangeRate']), $itemledger_arr[$i]['transactionCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$i]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                    $itemledger_arr[$i]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                    $itemledger_arr[$i]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                    $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['companyLocalAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['companyLocalExchangeRate']), $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                    $itemledger_arr[$i]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                    $itemledger_arr[$i]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                    $itemledger_arr[$i]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                    $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr[$i]['companyReportingAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['companyReportingExchangeRate']), $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                    $itemledger_arr[$i]['segmentID'] = $details_arr[$i]['segmentID'];
                    $itemledger_arr[$i]['segmentCode'] = $details_arr[$i]['segmentCode'];
                    $itemledger_arr[$i]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                    $itemledger_arr[$i]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                    $itemledger_arr[$i]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                    $itemledger_arr[$i]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                    $itemledger_arr[$i]['approvedYN'] = $details_arr[$i]['approvedYN'];
                    $itemledger_arr[$i]['approvedDate'] = $details_arr[$i]['approvedDate'];
                    $itemledger_arr[$i]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                    $itemledger_arr[$i]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];

                    $itemledger_arr[$i]['partyCurrencyID'] = $details_arr[$i]['supplierCurrencyID'];
                    $itemledger_arr[$i]['partyCurrency'] = $details_arr[$i]['supplierCurrency'];
                    $itemledger_arr[$i]['partyCurrencyExchangeRate'] = $details_arr[$i]['supplierCurrencyExchangeRate'];
                    $itemledger_arr[$i]['partyCurrencyAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['supplierCurrencyExchangeRate']), $details_arr[$i]['supplierCurrencyDecimalPlaces']) * -1);
                    $itemledger_arr[$i]['partyCurrencyDecimalPlaces'] = $details_arr[$i]['supplierCurrencyDecimalPlaces'];

                    $itemledger_arr[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                    $itemledger_arr[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                    $itemledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                    $itemledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                    $itemledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                    $itemledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                    $itemledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                    $itemledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                    $itemledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                    $itemledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                    $itemledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                }
            }

            /*if (!empty($item_arr)) {
                $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
            }*/

            if (!empty($itemledger_arr)) {
                $itemledger_arr = array_values($itemledger_arr);
                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
            }

            //$data['approvedYN']             = $status;
            //$data['approvedbyEmpID']        = $this->common_data['current_userID'];
            //$data['approvedbyEmpName']      = $this->common_data['current_user'];
            //$data['approvedDate']           = $this->common_data['current_date'];
            //$data['companyLocalAmount']     = $company_loc_tot;
            //$data['companyReportingAmount'] = $company_rpt_tot;
            //$data['supplierCurrencyAmount'] = $supplier_cr_tot;
            //$data['transactionAmount']      = $transaction_loc_tot;

            //$this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID')));
            //$this->db->update('srp_erp_stockreturnmaster', $data);
            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_stock_return_data($system_code, 'SR');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['stockReturnAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['stockReturnCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['returnDate'];
                $generalledger_arr[$i]['documentType'] = 'Return';
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['returnDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['returnDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comment'];
                $generalledger_arr[$i]['chequeNumber'] = '';
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                $generalledger_arr[$i]['partyType'] = 'SUP';
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['master_data']['supplierID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['master_data']['supplierSystemCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['master_data']['supplierName'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['master_data']['supplierCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['master_data']['supplierCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['master_data']['supplierCurrencyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['supplierCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                    $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                }
                $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];

                $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
            }

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            }
            $this->session->set_flashdata('s', 'Purchase Return Approved Successfully.');
        } /*else {
            $this->session->set_flashdata('s', 'Purchase Return Approval : Level ' . $level_id . ' Successfully.');
        }*/

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function delete_material_Issue_attachement()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            return false;
        } else {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        }
    }

    function fetch_item_for_grv()
    {
        //made changes to query - by mubashir (condition receivedQty > 0,grvDate less than stock returndate )

        $itemAutoID = trim($this->input->post('itemAutoID'));
        $this->db->select('transactionCurrency,returnDate');
        $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID')));
        $this->db->from('srp_erp_stockreturnmaster');
        $currency = $this->db->get()->row_array();

        $this->db->select('grvDetailsID,srp_erp_grvmaster.grvAutoID,grvPrimaryCode,grvDate,srp_erp_grvdetails.itemAutoID,srp_erp_grvdetails.itemSystemCode, srp_erp_grvdetails.itemDescription, (srp_erp_grvdetails.receivedQty-SUM(IFNULL(return_Qty,0))) as receivedQty, srp_erp_grvdetails.receivedAmount,transactionCurrencyDecimalPlaces,srp_erp_grvmaster.transactionCurrency,srp_erp_grvdetails.unitOfMeasure');
        $this->db->from('srp_erp_grvmaster');
        $this->db->join('srp_erp_grvdetails', 'srp_erp_grvdetails.grvAutoID = srp_erp_grvmaster.grvAutoID');
        $this->db->join('srp_erp_stockreturndetails', 'srp_erp_grvmaster.grvAutoID = srp_erp_stockreturndetails.grvAutoID AND srp_erp_stockreturndetails.itemAutoID = ' . $itemAutoID . '', "LEFT");
        $this->db->where('srp_erp_grvdetails.itemAutoID', trim($this->input->post('itemAutoID')));
        $this->db->where('srp_erp_grvmaster.supplierID', trim($this->input->post('supplierID')));
        $this->db->where('srp_erp_grvmaster.wareHouseLocation', trim($this->input->post('wareHouseLocation')));
        $this->db->where('srp_erp_grvmaster.transactionCurrency', $currency["transactionCurrency"]);
        $this->db->where('srp_erp_grvmaster.grvDate <=', $currency["returnDate"]);
        $this->db->where('srp_erp_grvmaster.approvedYN', 1);
        $this->db->group_by('srp_erp_grvmaster.grvAutoID');
        $this->db->having('receivedQty >', 0);
        return $this->db->get()->result_array();
        //echo  $this->db->last_query();
    }

    function save_grv_base_items()
    {
        $this->db->trans_start();
        $items_arr = array();
        $this->db->select('srp_erp_grvdetails.grvAutoID,itemAutoID,itemSystemCode,itemDescription,defaultUOM,unitOfMeasure,conversionRateUOM,receivedQty,itemFinanceCategory,itemFinanceCategorySub,PLGLAutoID,PLSystemGLCode,PLGLCode,PLDescription,PLType,BLGLAutoID,BLSystemGLCode,BLGLCode,BLDescription,BLType,segmentID,segmentCode,receivedAmount ,financeCategory,itemCategory,itemFinanceCategory,defaultUOMID,unitOfMeasureID');
        $this->db->from('srp_erp_grvdetails');
        $this->db->join('srp_erp_grvmaster', 'srp_erp_grvmaster.grvAutoID = srp_erp_grvdetails.grvAutoID');
        $this->db->where_in('grvDetailsID', $this->input->post('grvDetailsID'));
        $grv_data = $this->db->get()->result_array();
        $qty = $this->input->post('qty');
        for ($i = 0; $i < count($grv_data); $i++) {
            $data[$i]['type'] = 'GRV';
            $data[$i]['stockReturnAutoID'] = trim($this->input->post('stockReturnAutoID'));
            $data[$i]['grvAutoID'] = $grv_data[$i]['grvAutoID'];
            $data[$i]['itemAutoID'] = $grv_data[$i]['itemAutoID'];
            $data[$i]['itemSystemCode'] = $grv_data[$i]['itemSystemCode'];
            $data[$i]['itemDescription'] = $grv_data[$i]['itemDescription'];
            $data[$i]['defaultUOMID'] = $grv_data[$i]['defaultUOMID'];
            $data[$i]['defaultUOM'] = $grv_data[$i]['defaultUOM'];
            $data[$i]['unitOfMeasureID'] = $grv_data[$i]['unitOfMeasureID'];
            $data[$i]['unitOfMeasure'] = $grv_data[$i]['unitOfMeasure'];
            $data[$i]['conversionRateUOM'] = $grv_data[$i]['conversionRateUOM'];
            $data[$i]['return_Qty'] = $qty[$i];
            $data[$i]['received_Qty'] = $grv_data[$i]['receivedQty'];
            $data[$i]['itemFinanceCategory'] = $grv_data[$i]['itemFinanceCategory'];
            $data[$i]['itemFinanceCategorySub'] = $grv_data[$i]['itemFinanceCategorySub'];
            $data[$i]['PLGLAutoID'] = $grv_data[$i]['PLGLAutoID'];
            $data[$i]['PLSystemGLCode'] = $grv_data[$i]['PLSystemGLCode'];
            $data[$i]['PLGLCode'] = $grv_data[$i]['PLGLCode'];
            $data[$i]['PLDescription'] = $grv_data[$i]['PLDescription'];
            $data[$i]['PLType'] = $grv_data[$i]['PLType'];
            $data[$i]['BLGLAutoID'] = $grv_data[$i]['BLGLAutoID'];
            $data[$i]['BLSystemGLCode'] = $grv_data[$i]['BLSystemGLCode'];
            $data[$i]['BLGLCode'] = $grv_data[$i]['BLGLCode'];
            $data[$i]['BLDescription'] = $grv_data[$i]['BLDescription'];
            $data[$i]['BLType'] = $grv_data[$i]['BLType'];
            $data[$i]['segmentID'] = $grv_data[$i]['segmentID'];
            $data[$i]['segmentCode'] = $grv_data[$i]['segmentCode'];
            $data[$i]['currentlWacAmount'] = $grv_data[$i]['receivedAmount'];
            $data[$i]['financeCategory'] = $grv_data[$i]['financeCategory'];
            $data[$i]['itemCategory'] = $grv_data[$i]['itemCategory'];
            $data[$i]['itemFinanceCategory'] = $grv_data[$i]['itemFinanceCategory'];
            //$data[$i]['currentStock']           = $grv_data[$i]['currentStock'];
            //$data[$i]['currentWareHouseStock']  = $grv_data[$i]['currentStock'];
            $data[$i]['totalValue'] = ($data[$i]['currentlWacAmount'] * $data[$i]['return_Qty']);
        }

        $this->db->insert_batch('srp_erp_stockreturndetails', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Purchase Return : Details Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Purchase Return : ' . count($grv_data) . ' Item Details Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function delete_stockTransfer_attachement()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            return false;
        } else {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        }
    }

    function delete_return_detail()
    {

        /** update sub item master */
        $id = $this->input->post('stockReturnDetailsID');

        $this->db->select('*');
        $this->db->from('srp_erp_stockreturndetails');
        $this->db->where('stockReturnDetailsID', $id);
        $rTmp = $this->db->get()->row_array();


        $dataTmp['isSold'] = null;
        $dataTmp['soldDocumentAutoID'] = null;
        $dataTmp['soldDocumentDetailID'] = null;
        $dataTmp['soldDocumentID'] = null;
        $dataTmp['modifiedPCID'] = current_pc();
        $dataTmp['modifiedUserID'] = current_userID();
        $dataTmp['modifiedDatetime'] = format_date_mysql_datetime();

        $this->db->where('soldDocumentAutoID', $rTmp['stockReturnAutoID']);
        $this->db->where('soldDocumentDetailID', $rTmp['stockReturnDetailsID']);
        $this->db->where('soldDocumentID', 'SR');
        $this->db->update('srp_erp_itemmaster_sub', $dataTmp);

        /** end update sub item master */

        $this->db->delete('srp_erp_stockreturndetails', array('stockReturnDetailsID' => trim($this->input->post('stockReturnDetailsID'))));
        return true;
    }

    function fetch_inv_item_stock_adjustment()
    {
        $dataArr = array();
        $dataArr2 = array();
        $dataArr2['query'] = 'test';
        $companyCode = $this->common_data['company_data']['company_code'];
        $search_string = "%" . $_GET['query'] . "%";
        $type = (empty($_GET['t'])) ? 'Inventory' : $_GET['t'];
        $data = $this->db->query('SELECT * FROM (
SELECT mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT( IFNULL(itemDescription,"empty"), " - ", IFNULL(itemSystemCode,"empty"), " - ", IFNULL(partNo,"empty")  , " - ", IFNULL(seconeryItemCode,"empty")) AS "Match",CONCAT(itemDescription, " (" ,itemSystemCode,")") as itemDesc FROM srp_erp_itemmaster WHERE companyCode = "' . $companyCode . '" AND isActive="1" AND mainCategory = "' . $type . '") a WHERE (a.itemSystemCode LIKE "' . $search_string . '" OR a.itemDescription LIKE "' . $search_string . '" OR a.seconeryItemCode LIKE "' . $search_string . '" OR a.itemDesc LIKE "' . $search_string . '") LIMIT 20')->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['itemSystemCode'], 'itemAutoID' => $val['itemAutoID'], 'currentStock' => $val['currentStock'], 'defaultUnitOfMeasure' => $val['defaultUnitOfMeasure'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'companyLocalWacAmount' => $val['companyLocalWacAmount']);
            }

        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_inv_item()
    {
        $companyCode = $this->common_data['company_data']['company_code'];
        $search_string = "%" . $_GET['q'] . "%";
        $type = (empty($_GET['t'])) ? 'Inventory' : $_GET['t'];
        return $this->db->query('SELECT mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT(itemDescription, " (" ,itemSystemCode,")") AS "Match" FROM srp_erp_itemmaster WHERE (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR seconeryItemCode LIKE "' . $search_string . '") AND companyCode = "' . $companyCode . '" AND isActive="1" AND mainCategory = "' . $type . '"')->result_array();
    }

    function delete_purchase_return()
    {
        /*$this->db->delete('srp_erp_stockreturnmaster', array('stockReturnAutoID' => trim($this->input->post('stockReturnID'))));
        $this->db->delete('srp_erp_stockreturndetails', array('stockReturnAutoID' => trim($this->input->post('stockReturnID'))));*/
        $this->db->select('*');
        $this->db->from('srp_erp_stockreturndetails');
        $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnID')));
        $datas = $this->db->get()->row_array();
        if ($datas) {
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        } else {
            $data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnID')));
            $this->db->update('srp_erp_stockreturnmaster', $data);
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;
        }
    }

    function delete_stock_adjustment()
    {
        $id = trim($this->input->post('stock_auto_id'));

        /** Delete sub item list */
        /* 1---- delete all entries in the update process - item master sub temp */
        $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentAutoID' => $id, 'receivedDocumentID' => 'SA'));

        /*2-- reset all marked values */
        $setData['isSold'] = null;
        $setData['soldDocumentID'] = null;
        $setData['soldDocumentAutoID'] = null;
        $setData['soldDocumentDetailID'] = null;
        $ware['soldDocumentID'] = 'SA';
        $ware['soldDocumentAutoID'] = $id;
        $this->db->update('srp_erp_itemmaster_sub', $setData, $ware);
        /** End Delete sub item list */

        //$this->db->delete('srp_erp_stockadjustmentmaster', array('stockAdjustmentAutoID' => $id));
        $this->db->delete('srp_erp_stockadjustmentdetails', array('stockAdjustmentAutoID' => $id));


        $data = array(
            'isDeleted' => 1,
            'deletedEmpID' => current_userID(),
            'deletedDate' => current_date(),
        );
        $this->db->where('stockAdjustmentAutoID', trim($id));
        $this->db->update('srp_erp_stockadjustmentmaster', $data);
        $this->session->set_flashdata('s', 'Deleted Successfully.');
        return true;
    }


    function delete_purchaseReturn_attachement()
    {

        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            return false;
        } else {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        }
    }


    function delete_stockAdjustment_attachement()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            return false;
        } else {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        }
    }

    function delete_stockTransfer_details()
    {
        $this->db->delete('srp_erp_stocktransferdetails', array('stockTransferDetailsID' => trim($this->input->post('stockReturnDetailsID'))));
        return true;
    }

    function delete_stocktransfer_master()
    {
        /*$this->db->delete('srp_erp_stocktransfermaster', array('stockTransferAutoID' => trim($this->input->post('stockTransferAutoID'))));
        $this->db->delete('srp_erp_stocktransferdetails', array('stockTransferAutoID' => trim($this->input->post('stockTransferAutoID'))));*/
        /*    $this->db->select('*');
            $this->db->from('srp_erp_stocktransferdetails');
            $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID')));
            $datas = $this->db->get()->row_array();
            if ($datas) {
                $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
                return true;
            } else {
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );*/
        $data = array(
            'isDeleted' => 1,
            'deletedEmpID' => current_userID(),
            'deletedDate' => current_date(),
        );
        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID')));
        $this->db->update('srp_erp_stocktransfermaster', $data);
        $this->session->set_flashdata('s', 'Deleted Successfully.');
        return true;

    }

    function validate_itemMasterSub($itemAutoID, $documentID)
    {

        switch ($documentID) {
            case "SR":
                $query1 = "SELECT
                        count(*) AS countAll 
                    FROM
                        srp_erp_stockreturnmaster masterTbl
                    LEFT JOIN srp_erp_stockreturndetails detailTbl ON masterTbl.stockReturnAutoID = detailTbl.stockReturnAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = detailTbl.stockReturnDetailsID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockReturnAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 ";
                $query2 = "SELECT
                        SUM(detailTbl.return_Qty) AS totalQty
                    FROM
                        srp_erp_stockreturnmaster masterTbl
                    LEFT JOIN srp_erp_stockreturndetails detailTbl ON masterTbl.stockReturnAutoID = detailTbl.stockReturnAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockReturnAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1";
                break;

            case "MI":
                $query1 = "SELECT
                        count(*) AS countAll 
                    FROM
                        srp_erp_itemissuemaster masterTbl
                    LEFT JOIN srp_erp_itemissuedetails detailTbl ON masterTbl.itemIssueAutoID = detailTbl.itemIssueAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = detailTbl.itemIssueDetailID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.itemIssueAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 ";
                $query2 = "SELECT
                        SUM(detailTbl.qtyIssued) AS totalQty
                    FROM
                        srp_erp_itemissuemaster masterTbl
                    LEFT JOIN srp_erp_itemissuedetails detailTbl ON masterTbl.itemIssueAutoID = detailTbl.itemIssueAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.itemIssueAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1";
                break;

            case "ST":
                $query1 = "SELECT
                        count(*) AS countAll 
                    FROM
                        srp_erp_stocktransfermaster masterTbl
                    LEFT JOIN srp_erp_stocktransferdetails detailTbl ON masterTbl.stockTransferAutoID = detailTbl.stockTransferAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = detailTbl.stockTransferDetailsID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockTransferAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 ";
                $query2 = "SELECT
                        SUM(detailTbl.transfer_QTY) AS totalQty
                    FROM
                        srp_erp_stocktransfermaster masterTbl
                    LEFT JOIN srp_erp_stocktransferdetails detailTbl ON masterTbl.stockTransferAutoID = detailTbl.stockTransferAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockTransferAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1";
                break;

            case "SA":
                $query1 = "SELECT
                        count(*) AS countAll 
                    FROM
                        srp_erp_stockadjustmentmaster masterTbl
                    LEFT JOIN srp_erp_stockadjustmentdetails detailTbl ON masterTbl.stockAdjustmentAutoID = detailTbl.stockAdjustmentAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = detailTbl.stockAdjustmentDetailsAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockAdjustmentAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 AND detailTbl.previousStock > detailTbl.currentStock ";
                $query2 = "SELECT
                        SUM(abs(detailTbl.adjustmentStock)) AS totalQty
                    FROM
                        srp_erp_stockadjustmentmaster masterTbl
                    LEFT JOIN srp_erp_stockadjustmentdetails detailTbl ON masterTbl.stockAdjustmentAutoID = detailTbl.stockAdjustmentAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockAdjustmentAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 AND detailTbl.previousStock > detailTbl.currentStock";
                break;

            default:
                echo $documentID . ' Error: Code not configured!<br/>';
                echo 'File: ' . __FILE__ . '<br/>';
                echo 'Line No: ' . __LINE__ . '<br><br>';
                exit;
        }

        $r1 = $this->db->query($query1)->row_array();
        //echo $this->db->last_query();

        $r2 = $this->db->query($query2)->row_array();
        //echo $this->db->last_query();

        //exit;

        if (empty($r1) && empty($r2)) {
            $validate = true;
        } else if (empty($r1) || $r1['countAll'] == 0) {
            $validate = true;
        } else {
            if ($r1['countAll'] == $r2['totalQty']) {
                $validate = true;
            } else {
                $validate = false;
            }
        }
        return $validate;

    }


    function add_sub_itemMaster_tmpTbl($qty = 0, $itemAutoID, $masterID, $detailID, $code = 'GRV', $itemCode = null, $data = array())
    {

        $uom = isset($data['uom']) && !empty($data['uom']) ? $data['uom'] : null;
        $uomID = isset($data['uomID']) && !empty($data['uomID']) ? $data['uomID'] : null;
        $grv_detailID = isset($data['grv_detailID']) && !empty($data['grv_detailID']) ? $data['grv_detailID'] : null;
        $warehouseAutoID = isset($data['warehouseAutoID']) && !empty($data['warehouseAutoID']) ? $data['warehouseAutoID'] : null;
        $data_subItemMaster = array();
        if ($qty > 0) {
            $x = 0;
            for ($i = 1; $i <= $qty; $i++) {
                $data_subItemMaster[$x]['itemAutoID'] = $itemAutoID;
                $data_subItemMaster[$x]['subItemSerialNo'] = $i;
                $data_subItemMaster[$x]['subItemCode'] = $itemCode . '/SA/' . $grv_detailID . '/' . $i;
                $data_subItemMaster[$x]['uom'] = $uom;
                $data_subItemMaster[$x]['wareHouseAutoID'] = $warehouseAutoID;
                $data_subItemMaster[$x]['uomID'] = $uomID;
                $data_subItemMaster[$x]['receivedDocumentID'] = $code;
                $data_subItemMaster[$x]['receivedDocumentAutoID'] = $masterID;
                $data_subItemMaster[$x]['receivedDocumentDetailID'] = $detailID;
                $data_subItemMaster[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                $data_subItemMaster[$x]['createdUserGroup'] = $this->common_data['user_group'];
                $data_subItemMaster[$x]['createdPCID'] = $this->common_data['current_pc'];
                $data_subItemMaster[$x]['createdUserID'] = $this->common_data['current_userID'];
                $data_subItemMaster[$x]['createdDateTime'] = $this->common_data['current_date'];
                $x++;
            }
        }

        if (!empty($data_subItemMaster)) {
            /** bulk insert to item master sub */
            $this->batch_insert_srp_erp_itemmaster_subtemp($data_subItemMaster);
        }
    }

    function batch_insert_srp_erp_itemmaster_subtemp($data)
    {
        $this->db->insert_batch('srp_erp_itemmaster_subtemp', $data);
    }


    function save_sales_return_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $rtrnDate = $this->input->post('returnDate');
        $returnDate = input_format_date($rtrnDate, $date_format_policy);

        /*Finance Period */
        $financeyear_period = $this->input->post('financeyear_period');
        $this->db->select('*');
        $this->db->from('srp_erp_companyfinanceperiod');
        $this->db->where('companyFinancePeriodID', $financeyear_period);
        $companyFinancePeriod = $this->db->get()->row_array();


        $location = explode('|', trim($this->input->post('location_dec')));
        //$period = explode('|', trim($this->input->post('financeyear_period')));
        $year = explode(' - ', trim($this->input->post('companyFinanceYear')));
        $FYBegin = input_format_date($year[0], $date_format_policy);
        $FYEnd = input_format_date($year[1], $date_format_policy);
        $customer_arr = $this->fetch_customer_data(trim($this->input->post('customerID')));
        $currency_code = explode('|', trim($this->input->post('currency_code')));
        $data['documentID'] = 'SLR';
        $data['returnDate'] = trim($returnDate);
        $data['referenceNo'] = trim($this->input->post('referenceNo'));
        $data['comment'] = trim($this->input->post('narration'));
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear'));
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear'));
        $data['customerID'] = trim($this->input->post('customerID'));
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period'));
        $data['FYPeriodDateFrom'] = $companyFinancePeriod['dateFrom'];
        $data['FYPeriodDateTo'] = $companyFinancePeriod['dateTo'];
        $data['wareHouseAutoID'] = trim($this->input->post('location'));
        $data['wareHouseCode'] = trim($location[0]);
        $data['wareHouseLocation'] = trim($location[1]);
        $data['wareHouseDescription'] = trim($location[2]);
        $data['customerSystemCode'] = $customer_arr['customerSystemCode'];
        $data['customerName'] = $customer_arr['customerName'];
        $data['customerAddress'] = $customer_arr['customerAddress1'] . ' ' . $customer_arr['customerAddress2'];
        $data['customerTelephone'] = $customer_arr['customerTelephone'];
        $data['customerFax'] = $customer_arr['customerFax'];
        $data['customerEmail'] = $customer_arr['customerEmail'];
        $data['customerReceivableAutoID'] = $customer_arr['receivableAutoID'];
        $data['customerReceivableSystemGLCode'] = $customer_arr['receivableSystemGLCode'];
        $data['customerReceivableGLAccount'] = $customer_arr['receivableGLAccount'];
        $data['customerReceivableDescription'] = $customer_arr['receivableDescription'];
        $data['customerReceivableType'] = $customer_arr['receivableType'];

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

        $data['customerCurrencyID'] = $customer_arr['customerCurrencyID'];
        $data['customerCurrency'] = $customer_arr['customerCurrency'];
        $customerCurrency = currency_conversionID($data['transactionCurrencyID'], $data['customerCurrencyID']);
        $data['customerCurrencyExchangeRate'] = $customerCurrency['conversion'];
        $data['customerCurrencyDecimalPlaces'] = $customerCurrency['DecimalPlaces'];

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];


        if (trim($this->input->post('salesReturnAutoID'))) {
            $this->db->where('salesReturnAutoID', trim($this->input->post('salesReturnAutoID')));
            $this->db->update('srp_erp_salesreturnmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Sales Return : ' . $data['wareHouseDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Sales Return : ' . $data['wareHouseDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('salesReturnAutoID'));
            }
        } else {
            $this->load->library('sequence');

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['salesReturnCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_salesreturnmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Sales Return : ' . $data['wareHouseDescription'] . '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Sales Return : ' . $data['wareHouseDescription'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_customer_data($supplierID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $supplierID);
        return $this->db->get()->row_array();
    }

    function fetch_template_sales_return_data($salesReturnAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(returnDate,\'' . $convertFormat . '\') AS returnDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate');
        $this->db->where('salesReturnAutoID', $salesReturnAutoID);
        $this->db->from('srp_erp_salesreturnmaster');
        $data['master'] = $this->db->get()->row_array();
        $this->db->select('*');
        $this->db->where('salesReturnAutoID', $salesReturnAutoID);
        $this->db->from('srp_erp_salesreturndetails');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function load_sales_return_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(returnDate,\'' . $convertFormat . '\') AS returnDate');
        $this->db->where('salesReturnAutoID', $this->input->post('salesReturnAutoID'));
        return $this->db->get('srp_erp_salesreturnmaster')->row_array();
    }

    function fetch_sales_return_details()
    {
        $this->db->select('detailTbl.itemSystemCode,detailTbl.itemDescription,detailTbl.unitOfMeasure,srp_erp_salesreturnmaster.transactionCurrencyDecimalPlaces,detailTbl.currentWacAmount,detailTbl.return_Qty,detailTbl.totalValue,detailTbl.salesReturnDetailsID,srp_erp_itemmaster.isSubitemExist,srp_erp_salesreturnmaster.wareHouseAutoID, detailTbl.salesPrice');
        $this->db->from('srp_erp_salesreturndetails detailTbl');
        $this->db->where('detailTbl.salesReturnAutoID', trim($this->input->post('salesReturnAutoID')));
        $this->db->join('srp_erp_salesreturnmaster', 'detailTbl.salesReturnAutoID = srp_erp_salesreturnmaster.salesReturnAutoID');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = detailTbl.itemAutoID', 'left');
        $data['detail'] = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $data;
    }

    function fetch_item_for_sales_return()
    {
        //made changes to query - by mubashir (condition receivedQty > 0,grvDate less than stock returndate )
        $this->db->select('transactionCurrency,returnDate,wareHouseAutoID');
        $this->db->where('salesReturnAutoID', trim($this->input->post('salesReturnAutoID')));
        $this->db->from('srp_erp_salesreturnmaster');
        $currency = $this->db->get()->row_array();

        $this->db->select('invoiceDetailsAutoID,mainTable.invoiceAutoID,invoiceCode,invoiceDate,detailTbl.itemAutoID,detailTbl.itemSystemCode, detailTbl.itemDescription, (detailTbl.requestedQty-SUM(IFNULL(srp_erp_salesreturndetails.return_Qty,0))) as requestedQty, (detailTbl.unittransactionAmount-detailTbl.discountAmount) as transactionAmount,transactionCurrencyDecimalPlaces,mainTable.transactionCurrency,detailTbl.unitOfMeasure');
        $this->db->from('srp_erp_customerinvoicemaster mainTable');
        $this->db->join('srp_erp_customerinvoicedetails detailTbl', 'detailTbl.invoiceAutoID = mainTable.invoiceAutoID');
        $this->db->join('srp_erp_salesreturndetails', 'mainTable.invoiceAutoID = srp_erp_salesreturndetails.invoiceAutoID AND `detailTbl`.`itemAutoID` = `srp_erp_salesreturndetails`.`itemAutoID`', "LEFT");
        $this->db->where('detailTbl.itemAutoID', trim($this->input->post('itemAutoID')));
        $this->db->where('mainTable.customerID', trim($this->input->post('customerID')));
        //$this->db->where('mainTable.wareHouseLocation', trim($this->input->post('wareHouseLocation')));
        $this->db->where('mainTable.transactionCurrency', $currency["transactionCurrency"]);
        $this->db->where('mainTable.invoiceDate <=', $currency["returnDate"]);
        $this->db->where('mainTable.approvedYN', 1);
        $this->db->where('detailTbl.wareHouseAutoID', $currency['wareHouseAutoID']);
        $this->db->group_by('mainTable.invoiceAutoID');
        $this->db->having('requestedQty >', 0);
        $r = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $r;
    }

    function save_sales_return_detail_items()
    {
        $invoiceAutoID = $this->input->post('invoiceDetailsAutoID');
        $salesReturnAutoID = $this->input->post('salesReturnAutoID');
        $companyID = current_companyID();
        $currentTime = format_date_mysql_datetime();

        $invoiceIDs = join(',', $invoiceAutoID);
        $this->db->trans_start();
        $items_arr = array();
        $this->db->select('detailTbl.wareHouseAutoID,detailTbl.invoiceAutoID,itemAutoID,itemSystemCode,itemDescription,defaultUOM,unitOfMeasure,conversionRateUOM,requestedQty, expenseGLAutoID, expenseSystemGLCode, expenseGLCode, expenseGLDescription, expenseGLType, revenueGLAutoID, revenueGLCode, revenueSystemGLCode , revenueGLDescription , revenueGLType, assetGLAutoID,  assetGLCode,  assetSystemGLCode,  assetGLDescription, assetGLType, detailTbl.segmentID, detailTbl.segmentCode, detailTbl.transactionAmount, itemCategory, defaultUOMID, unitOfMeasureID, detailTbl.itemCategory, (detailTbl.unittransactionAmount-detailTbl.discountAmount) as unittransactionAmount, detailTbl.companyLocalWacAmount');
        $this->db->from('srp_erp_customerinvoicedetails as detailTbl');
        $this->db->join('srp_erp_customerinvoicemaster as masterTbl', 'masterTbl.invoiceAutoID = detailTbl.invoiceAutoID');
        $this->db->where('detailTbl.invoiceDetailsAutoID IN (' . $invoiceIDs . ')');
        $itemDetailList = $this->db->get()->result_array();

        //echo $this->db->last_query();


        $i = 0;
        $qty = $this->input->post('qty');


        foreach ($itemDetailList as $item) {

            $itemAutoID = $item['itemAutoID'];
            $wareHouseAutoID = $item['wareHouseAutoID'];
            $return_Qty = $qty[$i];

            /** item Master */
            $this->db->select('*');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $itemMaster = $this->db->get()->row_array();

            /** warehouse item Master */
            $this->db->select('*');
            $this->db->from('srp_erp_warehouseitems');
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $wareHouseAutoID);
            $itemWarehouseMaster = $this->db->get()->row_array();

            $data[$i]['salesReturnAutoID'] = $salesReturnAutoID;
            $data[$i]['invoiceAutoID'] = $item['invoiceAutoID'];
            $data[$i]['itemAutoID'] = $itemAutoID;
            $data[$i]['itemSystemCode'] = $item['itemSystemCode'];
            $data[$i]['itemDescription'] = $item['itemDescription'];
            $data[$i]['itemCategory'] = $item['itemCategory'];
            $data[$i]['unitOfMeasureID'] = $item['unitOfMeasureID'];
            $data[$i]['unitOfMeasure'] = $item['unitOfMeasure'];
            $data[$i]['defaultUOMID'] = $item['defaultUOMID'];
            $data[$i]['defaultUOM'] = $item['defaultUOM'];
            $data[$i]['conversionRateUOM'] = $item['conversionRateUOM'];
            $data[$i]['return_Qty'] = $return_Qty;
            $data[$i]['issued_Qty'] = $item['requestedQty'];
            $data[$i]['currentStock'] = $itemMaster['currentStock'];
            $data[$i]['currentWareHouseStock'] = $itemWarehouseMaster['currentStock'];
            $data[$i]['currentWacAmount'] = $item['companyLocalWacAmount'];
            $data[$i]['salesPrice'] = $item['unittransactionAmount'];
            $data[$i]['totalValue'] = $return_Qty * $item['unittransactionAmount'];
            $data[$i]['segmentID'] = $item['segmentID'];
            $data[$i]['segmentCode'] = $item['segmentCode'];
            $data[$i]['expenseGLAutoID'] = $item['expenseGLAutoID'];
            $data[$i]['expenseSystemGLCode'] = $item['expenseSystemGLCode'];
            $data[$i]['expenseGLCode'] = $item['expenseGLCode'];
            $data[$i]['expenseGLDescription'] = $item['expenseGLDescription'];
            $data[$i]['expenseGLType'] = $item['expenseGLType'];
            $data[$i]['revenueGLAutoID'] = $item['revenueGLAutoID'];
            $data[$i]['revenueGLCode'] = $item['revenueGLCode'];
            $data[$i]['revenueSystemGLCode'] = $item['revenueSystemGLCode'];
            $data[$i]['revenueGLDescription'] = $item['revenueGLDescription'];
            $data[$i]['revenueGLType'] = $item['revenueGLType'];
            $data[$i]['assetGLAutoID'] = $item['assetGLAutoID'];
            $data[$i]['assetGLCode'] = $item['assetGLCode'];
            $data[$i]['assetSystemGLCode'] = $item['assetSystemGLCode'];
            $data[$i]['assetGLDescription'] = $item['assetGLDescription'];
            $data[$i]['assetGLType'] = $item['assetGLType'];
            $data[$i]['comments'] = '';
            $data[$i]['companyID'] = $companyID;
            $data[$i]['timestamp'] = $currentTime;
            $i++;
        }


        $this->db->insert_batch('srp_erp_salesreturndetails', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Good Received note : Details Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Item Details Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function delete_sales_return_detail()
    {
        $id = $this->input->post('salesReturnDetailsID');
        /** update sub item master */
        /*$this->db->select('*');
        $this->db->from('srp_erp_stockreturndetails');
        $this->db->where('stockReturnDetailsID', $id);
        $rTmp = $this->db->get()->row_array();


        $dataTmp['isSold'] = null;
        $dataTmp['soldDocumentAutoID'] = null;
        $dataTmp['soldDocumentDetailID'] = null;
        $dataTmp['soldDocumentID'] = null;
        $dataTmp['modifiedPCID'] = current_pc();
        $dataTmp['modifiedUserID'] = current_userID();
        $dataTmp['modifiedDatetime'] = format_date_mysql_datetime();

        $this->db->where('soldDocumentAutoID', $rTmp['stockReturnAutoID']);
        $this->db->where('soldDocumentDetailID', $rTmp['stockReturnDetailsID']);
        $this->db->where('soldDocumentID', 'SR');
        $this->db->update('srp_erp_itemmaster_sub', $dataTmp);*/
        /** end update sub item master */

        $this->db->delete('srp_erp_salesreturndetails', array('salesReturnDetailsID' => $id));
        return true;
    }

    function sales_return_confirmation()
    {
        $this->db->select('salesReturnDetailsID');
        $this->db->where('salesReturnAutoID', trim($this->input->post('salesReturnAutoID')));
        $this->db->from('srp_erp_salesreturndetails');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {
            $this->db->select('salesReturnAutoID');
            $this->db->where('salesReturnAutoID', trim($this->input->post('salesReturnAutoID')));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_salesreturnmaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $masterID = trim($this->input->post('salesReturnAutoID'));
                $this->load->library('approvals');
                $this->db->select('salesReturnAutoID, salesReturnCode');
                $this->db->where('salesReturnAutoID', $masterID);
                $this->db->from('srp_erp_salesreturnmaster');
                $app_data = $this->db->get()->row_array();

                /** item Master Sub check */
                /*$documentDetailID = trim($this->input->post('stockReturnAutoID'));
                $validate = $this->validate_itemMasterSub($documentDetailID, 'SLR');*/
                /** end of item master sub */

                /*if ($validate) {*/


                /*} else {
                    return array('error' => 1, 'message' => 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');
                }*/


                $approvals_status = $this->approvals->CreateApproval('SLR', $app_data['salesReturnAutoID'], $app_data['salesReturnCode'], 'Sales Return', 'srp_erp_salesreturnmaster', 'salesReturnAutoID');


                if ($approvals_status == 1) {
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user']
                    );

                    $this->db->where('salesReturnAutoID', trim($this->input->post('stockReturnAutoID')));
                    $this->db->update('srp_erp_salesreturnmaster', $data);

                    return array('error' => 0, 'message' => 'document successfully confirmed');

                } else {
                    return array('error' => 1, 'message' => 'Approval setting are not configured!, please contact your system team.');
                }


            }
        }
        //return array('status' => true);
    }

    function delete_sales_return()
    {
        /* $this->db->delete('srp_erp_salesreturnmaster', array('salesReturnAutoID' => trim($this->input->post('salesReturnAutoID'))));
         $this->db->delete('srp_erp_salesreturndetails', array('salesReturnAutoID' => trim($this->input->post('salesReturnAutoID'))));*/
        $this->db->select('*');
        $this->db->from('srp_erp_salesreturndetails');
        $this->db->where('salesReturnAutoID', trim($this->input->post('salesReturnAutoID')));
        $datas = $this->db->get()->row_array();
        if ($datas) {
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        } else {
            $data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('salesReturnAutoID', trim($this->input->post('salesReturnAutoID')));
            $this->db->update('srp_erp_salesreturnmaster', $data);
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;
        }
    }


    /**
     * @param $oldStock : item master stock
     * @param $WACAmount : item master WAC  old
     * @param $qty : item master Qty
     * @param $cost : sales return unit cost
     * @param int $decimal : decimal point
     * @return float
     */
    function calculateNewWAC_salesReturn($oldStock, $WACAmount, $qty, $cost, $decimal = 2)
    {
        $newStock = $oldStock + $qty;
        $newWACAmount = round(((($oldStock * $WACAmount) + ($cost * $qty)) / $newStock), $decimal);
        return $newWACAmount;
    }

    function save_sales_return_approval()
    {
        $this->load->library('approvals');
        $system_id = trim($this->input->post('salesReturnAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        $comments = trim($this->input->post('comments'));

        $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'SLR');
        if ($approvals_status == 1) {
            $this->db->select('*');
            $this->db->where('salesReturnAutoID', $system_id);
            $this->db->from('srp_erp_salesreturnmaster');
            $master = $this->db->get()->row_array();

            /* $this->db->select('*');
             $this->db->where('salesReturnAutoID', $system_id);
             $this->db->from('srp_erp_salesreturndetails');*/
            $qry = "SELECT *,SUM(return_Qty) as return_Qty FROM srp_erp_salesreturndetails WHERE salesReturnAutoID = $system_id GROUP BY itemAutoID,unitOfMeasureID";
            $detailTbl = $this->db->query($qry)->result_array();


            $this->db->trans_start();
            /**setup data for item master & item ledger */
            $i = 0;
            foreach ($detailTbl as $invDetail) {

                $itemAutoID = $invDetail['itemAutoID'];
                $decimal = $master['companyLocalCurrencyDecimalPlaces'];
                $item = fetch_item_data($itemAutoID);

                $wareHouseAutoID = $master['wareHouseAutoID'];
                $qty = $invDetail['return_Qty'] / $invDetail['conversionRateUOM'];
                $newStock = $item['currentStock'] + $qty;

                $this->db->select('*');
                $this->db->from('srp_erp_warehouseitems');
                $this->db->where('wareHouseAutoID', $master['wareHouseAutoID']);
                $this->db->where('itemAutoID', $invDetail['itemAutoID']);
                $warehouseItem = $this->db->get()->row_array();
                $newStock_warehouse = $warehouseItem['currentStock'] + $qty;

                /** update warehouse stock */
                //$this->db->query("UPDATE srp_erp_warehouseitems SET currentStock =  '{$newStock}'  WHERE wareHouseAutoID='{$wareHouseAutoID}' AND itemAutoID='{$itemAutoID}'");

                /** WAC Calculation  */
                $companyLocalWacAmount = $this->calculateNewWAC_salesReturn($item['currentStock'], $item['companyLocalWacAmount'], $invDetail['return_Qty'], $invDetail['salesPrice'], $decimal);
                $companyReportingWacAmount = $this->calculateNewWAC_salesReturn($item['currentStock'], $item['companyReportingWacAmount'], $invDetail['return_Qty'], $invDetail['salesPrice'], $decimal);

                /** warehouse item update data */
                $warehouseItemData[$i]['warehouseItemsAutoID'] = $warehouseItem['warehouseItemsAutoID'];
                $warehouseItemData[$i]['currentStock'] = $newStock_warehouse;

                /** Item master update data */
                $itemMaster[$i]['itemAutoID'] = $itemAutoID;
                $itemMaster[$i]['currentStock'] = $newStock;
                $itemMaster[$i]['companyLocalWacAmount'] = $companyLocalWacAmount;
                $itemMaster[$i]['companyReportingWacAmount'] = $companyReportingWacAmount;

                /** setup Item Ledger Data  */
                $itemLedgerData[$i]['documentID'] = $master['documentID'];
                $itemLedgerData[$i]['documentCode'] = $master['documentID'];
                $itemLedgerData[$i]['documentAutoID'] = $master['salesReturnAutoID'];
                $itemLedgerData[$i]['documentSystemCode'] = $master['salesReturnCode'];
                $itemLedgerData[$i]['documentDate'] = $master['returnDate'];
                $itemLedgerData[$i]['referenceNumber'] = $master['referenceNo'];
                $itemLedgerData[$i]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                $itemLedgerData[$i]['companyFinanceYear'] = $master['companyFinanceYear'];
                $itemLedgerData[$i]['FYBegin'] = $master['FYBegin'];
                $itemLedgerData[$i]['FYEnd'] = $master['FYEnd'];
                $itemLedgerData[$i]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                $itemLedgerData[$i]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                $itemLedgerData[$i]['wareHouseAutoID'] = $master['wareHouseAutoID'];
                $itemLedgerData[$i]['wareHouseCode'] = $master['wareHouseCode'];
                $itemLedgerData[$i]['wareHouseLocation'] = $master['wareHouseLocation'];
                $itemLedgerData[$i]['wareHouseDescription'] = $master['wareHouseDescription'];
                $itemLedgerData[$i]['itemAutoID'] = $itemAutoID;
                $itemLedgerData[$i]['itemSystemCode'] = $invDetail['itemSystemCode'];
                $itemLedgerData[$i]['itemDescription'] = $invDetail['itemDescription'];
                $itemLedgerData[$i]['defaultUOMID'] = $invDetail['defaultUOMID'];
                $itemLedgerData[$i]['defaultUOM'] = $invDetail['defaultUOM'];
                $itemLedgerData[$i]['transactionUOMID'] = $invDetail['unitOfMeasureID'];
                $itemLedgerData[$i]['transactionUOM'] = $invDetail['unitOfMeasure'];
                $itemLedgerData[$i]['transactionQTY'] = $invDetail['return_Qty'];
                $itemLedgerData[$i]['convertionRate'] = $invDetail['conversionRateUOM'];
                $itemLedgerData[$i]['currentStock'] = $newStock;
                $itemLedgerData[$i]['PLGLAutoID'] = $item['costGLAutoID'];
                $itemLedgerData[$i]['PLSystemGLCode'] = $item['costSystemGLCode'];
                $itemLedgerData[$i]['PLGLCode'] = $item['costGLCode'];
                $itemLedgerData[$i]['PLDescription'] = $item['costDescription'];
                $itemLedgerData[$i]['PLType'] = $item['costType'];
                $itemLedgerData[$i]['BLGLAutoID'] = $item['assteGLAutoID'];
                $itemLedgerData[$i]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                $itemLedgerData[$i]['BLGLCode'] = $item['assteGLCode'];
                $itemLedgerData[$i]['BLDescription'] = $item['assteDescription'];
                $itemLedgerData[$i]['BLType'] = $item['assteType'];
                $itemLedgerData[$i]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                $ex_rate_wac = (1 / $master['companyLocalExchangeRate']);
                $itemLedgerData[$i]['transactionAmount'] = round((($invDetail['currentWacAmount'] / $ex_rate_wac) * ($itemLedgerData[$i]['transactionQTY'] / $invDetail['conversionRateUOM'])), $itemLedgerData[$i]['transactionCurrencyDecimalPlaces']);
                $itemLedgerData[$i]['salesPrice'] = $invDetail["salesPrice"];
                $itemLedgerData[$i]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                $itemLedgerData[$i]['transactionCurrency'] = $master['transactionCurrency'];
                $itemLedgerData[$i]['transactionExchangeRate'] = $master['transactionExchangeRate'];

                $itemLedgerData[$i]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                $itemLedgerData[$i]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                $itemLedgerData[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $itemLedgerData[$i]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                $itemLedgerData[$i]['companyLocalAmount'] = round(($itemLedgerData[$i]['transactionAmount'] / $itemLedgerData[$i]['companyLocalExchangeRate']), $itemLedgerData[$i]['companyLocalCurrencyDecimalPlaces']);
                $itemLedgerData[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                $itemLedgerData[$i]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                $itemLedgerData[$i]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                $itemLedgerData[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $itemLedgerData[$i]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                $itemLedgerData[$i]['companyReportingAmount'] = round(($itemLedgerData[$i]['transactionAmount'] / $itemLedgerData[$i]['companyReportingExchangeRate']), $itemLedgerData[$i]['companyReportingCurrencyDecimalPlaces']);
                $itemLedgerData[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                $itemLedgerData[$i]['partyCurrencyID'] = $master['customerCurrencyID'];
                $itemLedgerData[$i]['partyCurrency'] = $master['customerCurrency'];
                $itemLedgerData[$i]['partyCurrencyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $itemLedgerData[$i]['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $itemLedgerData[$i]['partyCurrencyAmount'] = round(($itemLedgerData[$i]['transactionAmount'] / $itemLedgerData[$i]['partyCurrencyExchangeRate']), $itemLedgerData[$i]['partyCurrencyDecimalPlaces']);
                $itemLedgerData[$i]['confirmedYN'] = $master['confirmedYN'];
                $itemLedgerData[$i]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                $itemLedgerData[$i]['confirmedByName'] = $master['confirmedByName'];
                $itemLedgerData[$i]['confirmedDate'] = $master['confirmedDate'];
                $itemLedgerData[$i]['approvedYN'] = $master['approvedYN'];
                $itemLedgerData[$i]['approvedDate'] = $master['approvedDate'];
                $itemLedgerData[$i]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                $itemLedgerData[$i]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                $itemLedgerData[$i]['segmentID'] = $invDetail['segmentID'];
                $itemLedgerData[$i]['segmentCode'] = $invDetail['segmentCode'];
                $itemLedgerData[$i]['companyID'] = $master['companyID'];
                $itemLedgerData[$i]['companyCode'] = $master['companyCode'];
                $itemLedgerData[$i]['createdUserGroup'] = $master['createdUserGroup'];
                $itemLedgerData[$i]['createdPCID'] = $master['createdPCID'];
                $itemLedgerData[$i]['createdUserID'] = $master['createdUserID'];
                $itemLedgerData[$i]['createdDateTime'] = $master['createdDateTime'];
                $itemLedgerData[$i]['createdUserName'] = $master['createdUserName'];
                $i++;
            }


            /** updating Item master new stock */
            if (!empty($itemMaster)) {
                $this->db->update_batch('srp_erp_itemmaster', $itemMaster, 'itemAutoID');
            }

            /** updating warehouse Item new stock */
            if (!empty($warehouseItemData)) {
                $this->db->update_batch('srp_erp_warehouseitems', $warehouseItemData, 'warehouseItemsAutoID');
            }

            /** updating Item Ledger */
            if (!empty($itemLedgerData)) {
                $this->db->insert_batch('srp_erp_itemledger', $itemLedgerData);
            }


            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_sales_return_data($system_id, 'SLR');


            /**setup data for general Ledger  */
            $i = 0;

            foreach ($double_entry['GLEntries'] as $doubleEntry) {
                $generalLedgerData[$i]['documentMasterAutoID'] = $master['salesReturnAutoID'];
                $generalLedgerData[$i]['documentCode'] = $master['documentID'];
                $generalLedgerData[$i]['documentSystemCode'] = $master['salesReturnCode'];
                $generalLedgerData[$i]['documentDate'] = $master['returnDate'];
                $generalLedgerData[$i]['documentType'] = '';
                $generalLedgerData[$i]['documentYear'] = date("Y", strtotime($master['returnDate']));;
                $generalLedgerData[$i]['documentMonth'] = date("m", strtotime($master['returnDate']));
                $generalLedgerData[$i]['documentNarration'] = $master['comment'];
                $generalLedgerData[$i]['chequeNumber'] = '';
                $generalLedgerData[$i]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                $generalLedgerData[$i]['transactionCurrency'] = $master['transactionCurrency'];
                $generalLedgerData[$i]['transactionExchangeRate'] = $master['transactionExchangeRate'];
                $generalLedgerData[$i]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                $generalLedgerData[$i]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                $generalLedgerData[$i]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                $generalLedgerData[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $generalLedgerData[$i]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                $generalLedgerData[$i]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                $generalLedgerData[$i]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                $generalLedgerData[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $generalLedgerData[$i]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                $generalLedgerData[$i]['partyContractID'] = '';
                $generalLedgerData[$i]['partyType'] = 'CUS';
                $generalLedgerData[$i]['partyAutoID'] = $master['customerID'];
                $generalLedgerData[$i]['partySystemCode'] = $master['customerSystemCode'];
                $generalLedgerData[$i]['partyName'] = $master['customerName'];
                $generalLedgerData[$i]['partyCurrencyID'] = $master['customerCurrencyID'];
                $generalLedgerData[$i]['partyCurrency'] = $master['customerCurrency'];
                $generalLedgerData[$i]['partyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $generalLedgerData[$i]['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                $generalLedgerData[$i]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                $generalLedgerData[$i]['confirmedByName'] = $master['confirmedByName'];
                $generalLedgerData[$i]['confirmedDate'] = $master['confirmedDate'];
                $generalLedgerData[$i]['approvedDate'] = $master['approvedDate'];
                $generalLedgerData[$i]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                $generalLedgerData[$i]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                $generalLedgerData[$i]['companyID'] = $master['companyID'];
                $generalLedgerData[$i]['companyCode'] = $master['companyCode'];
                $amount = $doubleEntry['debit'];
                if ($doubleEntry['amountType'] == 'cr') {
                    $amount = ($doubleEntry['credit'] * -1);
                }

                $transactionAmount = $doubleEntry['transactionAmount'];

                $generalLedgerData[$i]['transactionAmount'] = round($transactionAmount, $doubleEntry['transactionDecimal']);
                $generalLedgerData[$i]['companyLocalAmount'] = round(($transactionAmount / $generalLedgerData[$i]['companyLocalExchangeRate']), $generalLedgerData[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalLedgerData[$i]['companyReportingAmount'] = round(($transactionAmount / $generalLedgerData[$i]['companyReportingExchangeRate']), $generalLedgerData[$i]['companyReportingCurrencyDecimalPlaces']);
                $generalLedgerData[$i]['partyCurrencyAmount'] = round(($transactionAmount / $generalLedgerData[$i]['partyExchangeRate']), $generalLedgerData[$i]['partyCurrencyDecimalPlaces']);
                $generalLedgerData[$i]['amount_type'] = $doubleEntry['amountType'];
                $generalLedgerData[$i]['documentDetailAutoID'] = $doubleEntry['auto_id'];
                $generalLedgerData[$i]['GLAutoID'] = $doubleEntry['GLAutoID'];
                $generalLedgerData[$i]['systemGLCode'] = $doubleEntry['SystemGLCode'];
                $generalLedgerData[$i]['GLCode'] = $doubleEntry['GLSecondaryCode'];
                $generalLedgerData[$i]['GLDescription'] = $doubleEntry['GLDescription'];
                $generalLedgerData[$i]['GLType'] = $doubleEntry['GLType'];
                $generalLedgerData[$i]['segmentID'] = $doubleEntry['segmentID'];
                $generalLedgerData[$i]['segmentCode'] = $doubleEntry['segmentCode'];
                $generalLedgerData[$i]['subLedgerType'] = $doubleEntry['subLedgerType'];
                $generalLedgerData[$i]['subLedgerDesc'] = $doubleEntry['subLedgerDesc'];
                $generalLedgerData[$i]['isAddon'] = 0;
                $generalLedgerData[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $generalLedgerData[$i]['createdPCID'] = $this->common_data['current_pc'];
                $generalLedgerData[$i]['createdUserID'] = $this->common_data['current_userID'];
                $generalLedgerData[$i]['createdDateTime'] = $this->common_data['current_date'];
                $generalLedgerData[$i]['createdUserName'] = $this->common_data['current_user'];
                $i++;
            }


            if (!empty($generalLedgerData)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalLedgerData);
            }


            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];

            $this->db->where('salesReturnAutoID', $system_id);
            $this->db->update('srp_erp_salesreturnmaster', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            //return true;
            //$this->session->set_flashdata('s', 'Document approved successfully.');
            return array('error' => 1, 'An error has occurred!');
        } else {
            $this->db->trans_commit();
            return array('error' => 0, 'Document approved successfully.');
            //return true;
        }
    }

    function re_open_inventory()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('salesReturnAutoID', trim($this->input->post('salesReturnAutoID')));
        $this->db->update('srp_erp_salesreturnmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function re_open_stock_return()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('stockReturnAutoID', trim($this->input->post('stockReturnAutoID')));
        $this->db->update('srp_erp_stockreturnmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function re_open_material_issue()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('itemIssueAutoID', trim($this->input->post('itemIssueAutoID')));
        $this->db->update('srp_erp_itemissuemaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function re_open_stock_transfer()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID')));
        $this->db->update('srp_erp_stocktransfermaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function re_open_stock_adjestment()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('stockAdjustmentAutoID', trim($this->input->post('stockAdjustmentAutoID')));
        $this->db->update('srp_erp_stockadjustmentmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function stockadjustmentAccountUpdate()
    {

        $gl = fetch_gl_account_desc($this->input->post('PLGLAutoID'));

        $BLGLAutoID = $this->input->post('BLGLAutoID');

        $data = array(
            'PLGLAutoID' => $this->input->post('PLGLAutoID'),
            'PLSystemGLCode' => $gl['systemAccountCode'],
            'PLGLCode' => $gl['GLSecondaryCode'],
            'PLDescription' => $gl['GLDescription'],
            'PLType' => $gl['subCategory'],
        );
        if (isset($BLGLAutoID)) {
            $bl = fetch_gl_account_desc($this->input->post('BLGLAutoID'));
            $data = array_merge($data, array('BLGLAutoID' => $this->input->post('BLGLAutoID'),
                'BLSystemGLCode' => $bl['systemAccountCode'],
                'BLGLCode' => $bl['GLSecondaryCode'],
                'BLDescription' => $bl['GLDescription']));
        }
        if ($this->input->post('applyAll') == 1) {
            $this->db->where('stockAdjustmentAutoID', trim($this->input->post('masterID')));
        } else {
            $this->db->where('stockAdjustmentDetailsAutoID', trim($this->input->post('detailID')));
        }

        $this->db->update('srp_erp_stockadjustmentdetails', $data);
        return array('s', 'GL Account Successfully Changed');

    }

    function materialAccountUpdate()
    {
        $gl = fetch_gl_account_desc($this->input->post('PLGLAutoID'));

        $BLGLAutoID = $this->input->post('BLGLAutoID');

        $data = array(
            'PLGLAutoID' => $this->input->post('PLGLAutoID'),
            'PLSystemGLCode' => $gl['systemAccountCode'],
            'PLGLCode' => $gl['GLSecondaryCode'],
            'PLDescription' => $gl['GLDescription'],
            'PLType' => $gl['subCategory'],
        );
        if (isset($BLGLAutoID)) {
            $bl = fetch_gl_account_desc($this->input->post('BLGLAutoID'));
            $data = array_merge($data, array('BLGLAutoID' => $this->input->post('BLGLAutoID'),
                'BLSystemGLCode' => $bl['systemAccountCode'],
                'BLGLCode' => $bl['GLSecondaryCode'],
                'BLDescription' => $bl['GLDescription']));
        }


        if ($this->input->post('applyAll') == 1) {
            $this->db->where('itemIssueAutoID', trim($this->input->post('masterID')));
        } else {
            $this->db->where('itemIssueDetailID', trim($this->input->post('detailID')));
        }
        $this->db->update('srp_erp_itemissuedetails', $data);
        return array('s', 'GL Account Successfully Changed');
    }


    function fetch_stockTransfer_all_detail_edit()
    {
        $this->db->select('st.stockTransferDetailsID,w.currentStock as wareHouseStock,st.itemAutoID,st.itemDescription,st.itemSystemCode, st.defaultUOMID,st.unitOfMeasureID,st.transfer_QTY,st.segmentID,st.segmentCode,st.projectID');
        $this->db->from('srp_erp_stocktransferdetails st');
        $this->db->join('srp_erp_warehouseitems w', 'w.itemAutoID = st.itemAutoID');
        $this->db->where('wareHouseAutoID', trim($this->input->post('location')));
        $this->db->where('stockTransferAutoID', trim($this->input->post('stockTransferAutoID')));
        $data['details'] = $this->db->get()->result_array();

        $this->db->select('srp_erp_unit_of_measure.UnitID,UnitShortCode,UnitDes,conversion,masterUnitID');
        $this->db->from('srp_erp_unitsconversion');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_unitsconversion.subUnitID');
        $this->db->where('srp_erp_unitsconversion.companyID', $this->common_data['company_data']['company_id']);
        $data['alluom'] = $this->db->get()->result_array();

        return $data;

    }

    function save_stock_transfer_detail_edit_all_multiple()
    {
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $stockTransferDetailsID = $this->input->post('stockTransferDetailsID');
        $stockTransferAutoID = $this->input->post('stockTransferAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $currentWareHouseStockQty = $this->input->post('currentWareHouseStockQty');
        $transfer_QTY = $this->input->post('transfer_QTY');
        $projectID = $this->input->post('projectID');
        $a_segment = $this->input->post('a_segment');

        $this->db->trans_start();
        foreach ($itemAutoIDs as $key => $itemAutoID) {
            if (!$stockTransferDetailsID[$key]) {
                $this->db->select('stockTransferAutoID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_stocktransferdetails');
                $this->db->where('stockTransferAutoID', $stockTransferAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();

                if (!empty($order_detail)) {
                    return array('error' => 1, 'w', 'Stock Transfer Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            } else {
                $this->db->select('stockTransferAutoID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_stocktransferdetails');
                $this->db->where('stockTransferAutoID', $stockTransferAutoID);
                $this->db->where('stockTransferDetailsID !=', $stockTransferDetailsID[$key]);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();

                if (!empty($order_detail)) {
                    return array('error' => 1, 'w', 'Stock Transfer Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);
            $data['stockTransferAutoID'] = $stockTransferAutoID;
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            $data['unitOfMeasure'] = trim($uomEx[0]);
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['transfer_QTY'] = $transfer_QTY[$key];
            $data['segmentID'] = trim($segment[0]);
            $data['segmentCode'] = trim($segment[1]);
            $data['currentWareHouseStock'] = $currentWareHouseStockQty[$key];

            $this->db->select('to_wareHouseAutoID,to_wareHouseLocation,to_wareHouseDescription,from_wareHouseAutoID');
            $this->db->from('srp_erp_stocktransfermaster');
            $this->db->where('stockTransferAutoID', $stockTransferAutoID);
            $master = $this->db->get()->row_array();

            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $master['to_wareHouseAutoID']);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

            $this->db->select('wareHouseAutoID,warehouseType,WIPGLAutoID');
            $this->db->from('srp_erp_warehousemaster');
            $this->db->where('wareHouseAutoID', $master['from_wareHouseAutoID']);
            $fromWarehouseGl = $this->db->get()->row_array();

            $this->db->select('wareHouseAutoID,warehouseType,WIPGLAutoID');
            $this->db->from('srp_erp_warehousemaster');
            $this->db->where('wareHouseAutoID', $master['to_wareHouseAutoID']);
            $toWarehouseGl = $this->db->get()->row_array();

            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $master['to_wareHouseAutoID'],
                    'wareHouseLocation' => $master['to_wareHouseLocation'],
                    'wareHouseDescription' => $master['to_wareHouseDescription'],
                    'itemAutoID' => $data['itemAutoID'],
                    'itemSystemCode' => $data['itemSystemCode'],
                    'itemDescription' => $data['itemDescription'],
                    'unitOfMeasureID' => $data['defaultUOMID'],
                    'unitOfMeasure' => $data['defaultUOM'],
                    'currentStock' => 0,
                    'companyID' => $this->common_data['company_data']['company_id'],
                    'companyCode' => $this->common_data['company_data']['company_code'],
                );
                $this->db->insert('srp_erp_warehouseitems', $data_arr);
            }


            if ($fromWarehouseGl['warehouseType'] == 2) {
                $data['fromWarehouseType'] = 2;
                $data['fromWarehouseWIPGLAutoID'] = $fromWarehouseGl['WIPGLAutoID'];
            }

            if ($toWarehouseGl['warehouseType'] == 2) {
                $data['toWarehouseType'] = 2;
                $data['toWarehouseWIPGLAutoID'] = $toWarehouseGl['WIPGLAutoID'];
            }

            if (trim($stockTransferDetailsID[$key])) {
                $this->db->where('stockTransferDetailsID', trim($stockTransferDetailsID[$key]));
                $this->db->update('srp_erp_stocktransferdetails', $data);
                $this->db->trans_complete();
            } else {
                $data['itemFinanceCategory'] = $item_data['subcategoryID'];
                $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
                $data['financeCategory'] = $item_data['financeCategory'];
                $data['itemCategory'] = $item_data['mainCategory'];
                if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                    $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                    $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                    $data['PLGLCode'] = $item_data['costGLCode'];
                    $data['PLDescription'] = $item_data['costDescription'];
                    $data['PLType'] = $item_data['costType'];
                    $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                    $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                    $data['BLGLCode'] = $item_data['assteGLCode'];
                    $data['BLDescription'] = $item_data['assteDescription'];
                    $data['BLType'] = $item_data['assteType'];
                } elseif ($data['financeCategory'] == 2) {
                    $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                    $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                    $data['PLGLCode'] = $item_data['costGLCode'];
                    $data['PLDescription'] = $item_data['costDescription'];
                    $data['PLType'] = $item_data['costType'];
                    $data['BLGLAutoID'] = '';
                    $data['BLSystemGLCode'] = '';
                    $data['BLGLCode'] = '';
                    $data['BLDescription'] = '';
                    $data['BLType'] = '';
                }
                $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
                $data['currentStock'] = $item_data['currentStock'];
                $data['totalValue'] = ($data['currentlWacAmount'] * $data['transfer_QTY']);

                $this->db->insert('srp_erp_stocktransferdetails', $data);
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('error' => 1, 'e', 'Stock Transfer Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('error' => 0, 's', 'Stock Transfer Detail :  Saved Successfully.');
        }

    }


    function save_material_detail_multiple_edit()
    {
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $itemIssueDetailID = $this->input->post('itemIssueDetailID');
        $itemIssueAutoID = $this->input->post('itemIssueAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $projectID = $this->input->post('projectID');
        $currentWareHouseStockQty = $this->input->post('currentWareHouseStockQty');
        $quantityRequested = $this->input->post('quantityRequested');
        $a_segment = $this->input->post('a_segment');
        $comment = $this->input->post('comment');

        $this->db->trans_start();

        foreach ($itemAutoIDs as $key => $itemAutoID) {

            if (!$itemIssueDetailID[$key]) {
                $this->db->select('itemIssueAutoID,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_itemissuedetails');
                $this->db->where('itemIssueAutoID', $itemIssueAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Item Issue Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            } else {
                $this->db->select('itemIssueAutoID,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_itemissuedetails');
                $this->db->where('itemIssueAutoID', $itemIssueAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->where('itemIssueDetailID !=', $itemIssueDetailID[$key]);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Item Issue Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);

            $data['itemIssueAutoID'] = trim($this->input->post('itemIssueAutoID'));
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasure'] = trim($uomEx[0]);
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['qtyIssued'] = $quantityRequested[$key];
            $data['comments'] = $comment[$key];
            $data['remarks'] = '';
            $data['currentWareHouseStock'] = $currentWareHouseStockQty[$key];
            $data['segmentID'] = trim($segment[0]);
            $data['segmentCode'] = trim($segment[1]);
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['currentStock'] = $item_data['currentStock'];

            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }

            $data['totalValue'] = ($data['currentlWacAmount'] * ($data['qtyIssued'] / $data['conversionRateUOM']));
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            if (trim($itemIssueDetailID[$key])) {
                $this->db->where('itemIssueDetailID', trim($itemIssueDetailID[$key]));
                $this->db->update('srp_erp_itemissuedetails', $data);
            } else {
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_itemissuedetails', $data);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Item Issue Detail :  Update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Item Issue Detail :  Updated Successfully.');

        }

    }


    function save_material_request_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $isuDate = $this->input->post('requestedDate');
        $issueDate = input_format_date($isuDate, $date_format_policy);
        //$segment = explode('|', trim($this->input->post('segment')));
        $location = explode('|', trim($this->input->post('location_dec')));

        $data['documentID'] = 'MR';
        $data['itemType'] = trim($this->input->post('itemType'));
        $data['requestedDate'] = trim($issueDate);
        $data['referenceNo'] = trim($this->input->post('referenceNo'));
        $data['wareHouseAutoID'] = trim($this->input->post('location'));
        $data['wareHouseCode'] = trim($location[0]);
        $data['wareHouseLocation'] = trim($location[1]);
        $data['wareHouseDescription'] = trim($location[2]);
        $data['jobNo'] = trim($this->input->post('jobNo'));

        if ($this->input->post('employeeID')) {
            $Requested = explode('|', trim($this->input->post('requested')));
            $data['employeeName'] = trim($Requested[1]);
            $data['employeeCode'] = trim($Requested[0]);
            $data['employeeID'] = trim($this->input->post('employeeID'));
        } else {
            $data['employeeName'] = trim($this->input->post('employeeName'));
            $data['employeeCode'] = NULL;
            $data['employeeID'] = NULL;
        }
        $data['comment'] = trim($this->input->post('narration'));
        //$data['segmentID'] = trim($segment[0]);
        //$data['segmentCode'] = trim($segment[1]);
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('mrAutoID'))) {
            $this->db->where('mrAutoID', trim($this->input->post('mrAutoID')));
            $this->db->update('srp_erp_materialrequest', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Material Request : ' . $data['employeeName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Material Request : ' . $data['employeeName'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('mrAutoID'));
            }
        } else {
            //$this->load->library('sequence');
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data['companyLocalExchangeRate'] = 1;
            $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $reporting_currency = currency_conversionID($data['companyLocalCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            //$data['MRCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_materialrequest', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Material Request : ' . $data['employeeName'] . '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Material Request : ' . $data['employeeName'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function load_material_request_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(requestedDate,\'' . $convertFormat . '\') AS requestedDate');
        $this->db->where('mrAutoID', $this->input->post('mrAutoID'));
        return $this->db->get('srp_erp_materialrequest')->row_array();
    }

    function fetch_material_request_detail()
    {
        $mrAutoID = $this->input->post('mrAutoID');
        $data['detail'] = $this->db->query("SELECT
	`srp_erp_materialrequestdetails`.*, `srp_erp_itemmaster`.`isSubitemExist`,
	`srp_erp_materialrequest`.`wareHouseAutoID`,
srp_erp_warehouseitems.currentStock AS stock,srp_erp_materialrequestdetails.currentWareHouseStock as CurrentStockAddTime
FROM
	`srp_erp_materialrequestdetails`
LEFT JOIN `srp_erp_itemmaster` ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_materialrequestdetails`.`itemAutoID`
LEFT JOIN `srp_erp_materialrequest` ON `srp_erp_materialrequest`.`mrAutoID` = `srp_erp_materialrequestdetails`.`mrAutoID`
JOIN `srp_erp_warehouseitems` ON `srp_erp_warehouseitems`.`itemAutoID` = `srp_erp_materialrequestdetails`.`itemAutoID`
AND srp_erp_materialrequest.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID
WHERE
	`srp_erp_materialrequestdetails`.`mrAutoID` = '$mrAutoID' ")->result_array();
        return $data;
    }


    function save_material_request_detail_multiple()
    {
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $mrDetailID = $this->input->post('mrDetailID');
        $mrAutoID = $this->input->post('mrAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $projectID = $this->input->post('projectID');
        $currentWareHouseStockQty = $this->input->post('currentWareHouseStockQty');
        $quantityRequested = $this->input->post('quantityRequested');
        //$a_segment = $this->input->post('a_segment');
        $comment = $this->input->post('comment');

        $this->db->select('*');
        $this->db->from('srp_erp_materialrequest');
        $this->db->where('mrAutoID', $mrAutoID);
        $masterRecord = $this->db->get()->row_array();

        $this->db->trans_start();

        foreach ($itemAutoIDs as $key => $itemAutoID) {

            if (!$mrDetailID) {
                $this->db->select('mrAutoID,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_materialrequestdetails');
                $this->db->where('mrAutoID', $mrAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Item Request Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            //$segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);

            $data['mrAutoID'] = trim($this->input->post('mrAutoID'));
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasure'] = trim($uomEx[0]);
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['qtyRequested'] = $quantityRequested[$key];
            $data['comments'] = $comment[$key];
            $data['remarks'] = '';
            $data['currentWareHouseStock'] = $currentWareHouseStockQty[$key];
            //$data['segmentID'] = $masterRecord['segmentID'];
            //$data['segmentCode'] = $masterRecord['segmentCode'];
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['currentStock'] = $item_data['currentStock'];

            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }

            $data['totalValue'] = ($data['currentlWacAmount'] * ($data['qtyRequested'] / $data['conversionRateUOM']));
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_materialrequestdetails', $data);

            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $masterRecord['wareHouseAutoID']);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $masterRecord['wareHouseAutoID'],
                    'wareHouseLocation' => $masterRecord['wareHouseLocation'],
                    'wareHouseDescription' => $masterRecord['wareHouseDescription'],
                    'itemAutoID' => $itemAutoID,
                    'itemSystemCode' => $item_data['itemSystemCode'],
                    'itemDescription' => $item_data['itemDescription'],
                    'unitOfMeasureID' => $UnitOfMeasureID[$key],
                    'unitOfMeasure' => trim($uomEx[0]),
                    'currentStock' => 0,
                    'companyID' => $this->common_data['company_data']['company_id'],
                    'companyCode' => $this->common_data['company_data']['company_code'],
                );
                $this->db->insert('srp_erp_warehouseitems', $data_arr);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Item Request Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Item Request Detail :  Saved Successfully.');

        }

    }

    function fetch_warehouse_item_material_request()
    {
        $this->db->select('wareHouseAutoID,wareHouseCode, wareHouseLocation, wareHouseDescription');
        $this->db->from('srp_erp_materialrequest');
        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID')));
        $query = $this->db->get()->row_array();

        $this->db->select('srp_erp_warehouseitems.currentStock,companyLocalWacAmount,wareHouseDescription,wareHouseLocation');
        $this->db->from('srp_erp_warehouseitems');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_warehouseitems.itemAutoID');
        $this->db->where('wareHouseAutoID', $query['wareHouseAutoID']);
        $this->db->where('srp_erp_warehouseitems.itemAutoID', trim($this->input->post('itemAutoID')));
        $this->db->where('srp_erp_warehouseitems.companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get()->row_array();
        if (!empty($data)) {
            return array('status' => true, 'currentStock' => $data['currentStock'], 'WacAmount' => $data['companyLocalWacAmount']);
        } else {
            $this->session->set_flashdata('w', "Item doesn't exists in the selected warehouse " . $query["wareHouseDescription"] . " ( " . $query["wareHouseLocation"] . " )");
            return array('status' => false);
        }
    }

    function load_material_request_detail()
    {
        $mrDetailID = $this->input->post('mrDetailID');
        $result = $this->db->query("SELECT
	srp_erp_materialrequestdetails.*, `srp_erp_warehouseitems`.`currentStock` AS Stock
FROM
	`srp_erp_materialrequestdetails`
JOIN `srp_erp_materialrequest` ON `srp_erp_materialrequest`.`mrAutoID` = `srp_erp_materialrequestdetails`.`mrAutoID`
JOIN `srp_erp_warehouseitems` ON `srp_erp_warehouseitems`.`itemAutoID` = `srp_erp_materialrequestdetails`.`itemAutoID`
AND srp_erp_materialrequest.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID
WHERE
	`mrDetailID` = '$mrDetailID'")->row_array();
        return $result;
    }

    function save_material_request_detail()
    {
        $projectExist = project_is_exist();
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        if (!empty($this->input->post('mrDetailID'))) {
            $this->db->select('mrAutoID,,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_materialrequestdetails');
            $this->db->where('mrAutoID', trim($this->input->post('mrAutoID')));
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
            $this->db->where('mrDetailID !=', trim($this->input->post('mrDetailID')));
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('w', 'Item Issue Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }
        $this->db->select('*');
        $this->db->from('srp_erp_materialrequest');
        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID')));
        $masterRecord = $this->db->get()->row_array();
        $this->db->trans_start();
        //$segment = explode('|', trim($this->input->post('a_segment')));
        $uom = explode('|', $this->input->post('uom'));
        $item_data = fetch_item_data(trim($this->input->post('itemAutoID')));
        $projectID = trim($this->input->post('projectID'));
        $data['mrAutoID'] = trim($this->input->post('mrAutoID'));
        $data['itemAutoID'] = trim($this->input->post('itemAutoID'));
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        $data['unitOfMeasure'] = trim($uom[0]);
        $data['unitOfMeasureID'] = trim($this->input->post('unitOfMeasureID'));
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['qtyRequested'] = trim($this->input->post('quantityRequested'));
        $data['comments'] = trim($this->input->post('comment'));
        $data['remarks'] = trim($this->input->post('remarks'));
        $data['currentWareHouseStock'] = trim($this->input->post('currentWareHouseStockQty'));
        $data['segmentID'] = $masterRecord['segmentID'];
        $data['segmentCode'] = $masterRecord['segmentCode'];
        $data['itemFinanceCategory'] = $item_data['subcategoryID'];
        $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
        $data['financeCategory'] = $item_data['financeCategory'];
        $data['itemCategory'] = $item_data['mainCategory'];
        $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
        $data['currentStock'] = $item_data['currentStock'];
        if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
            $data['PLGLAutoID'] = $item_data['costGLAutoID'];
            $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['PLGLCode'] = $item_data['costGLCode'];
            $data['PLDescription'] = $item_data['costDescription'];
            $data['PLType'] = $item_data['costType'];

            $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
            $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
            $data['BLGLCode'] = $item_data['assteGLCode'];
            $data['BLDescription'] = $item_data['assteDescription'];
            $data['BLType'] = $item_data['assteType'];
        } elseif ($data['financeCategory'] == 2) {
            $data['PLGLAutoID'] = $item_data['costGLAutoID'];
            $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['PLGLCode'] = $item_data['costGLCode'];
            $data['PLDescription'] = $item_data['costDescription'];
            $data['PLType'] = $item_data['costType'];

            $data['BLGLAutoID'] = '';
            $data['BLSystemGLCode'] = '';
            $data['BLGLCode'] = '';
            $data['BLDescription'] = '';
            $data['BLType'] = '';
        }
        $data['totalValue'] = ($data['currentlWacAmount'] * ($data['qtyRequested'] / $data['conversionRateUOM']));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('mrDetailID'))) {
            $this->db->where('mrDetailID', trim($this->input->post('mrDetailID')));
            $this->db->update('srp_erp_materialrequestdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Item Request Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Item Request Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Updated Successfully.');
            }
        } else {
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_materialrequestdetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Item Request Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'Item Request Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Saved Successfully.');
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }


    function fetch_template_data_MR($mrAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,,DATE_FORMAT(requestedDate,\'' . $convertFormat . '\') AS requestedDate,(DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\')) AS approvedDate');
        $this->db->where('mrAutoID', $mrAutoID);
        $this->db->from('srp_erp_materialrequest');
        $data['master'] = $this->db->get()->row_array();
        $this->db->select('*');
        $this->db->where('mrAutoID', $mrAutoID);
        $this->db->from('srp_erp_materialrequestdetails');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function delete_material_request_item()
    {
        $id = $this->input->post('mrDetailID');

        $this->db->select('*');
        $this->db->from('srp_erp_materialrequestdetails');
        $this->db->where('mrDetailID', $id);
        $detail_arr = $this->db->get()->row_array();

        /** update sub item master */

        $dataTmp['isSold'] = null;
        $dataTmp['soldDocumentAutoID'] = null;
        $dataTmp['soldDocumentDetailID'] = null;
        $dataTmp['soldDocumentID'] = null;
        $dataTmp['modifiedPCID'] = current_pc();
        $dataTmp['modifiedUserID'] = current_userID();
        $dataTmp['modifiedDatetime'] = format_date_mysql_datetime();

        $this->db->where('soldDocumentAutoID', $detail_arr['mrAutoID']);
        $this->db->where('soldDocumentDetailID', $detail_arr['mrDetailID']);
        $this->db->where('soldDocumentID', 'MR');
        $this->db->update('srp_erp_itemmaster_sub', $dataTmp);


        /** end update sub item master */
        $this->db->where('mrDetailID', $id);
        $result = $this->db->delete('srp_erp_materialrequestdetails');
        if ($result) {
            $this->session->set_flashdata('s', 'Records Deleted Successfully');
            return true;
        }
    }


    function delete_material_request_header()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_materialrequestdetails');
        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID')));
        $datas = $this->db->get()->row_array();

        $data = array(
            'isDeleted' => 1,
            'deletedEmpID' => current_userID(),
            'deletedDate' => current_date(),
        );
        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID')));
        $this->db->update('srp_erp_materialrequest', $data);
        $this->session->set_flashdata('s', 'Deleted Successfully.');
        return true;


    }

    function re_open_material_request()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID')));
        $this->db->update('srp_erp_materialrequest', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }


    function material_request_item_confirmation()
    {
        $this->db->select('mrAutoID');
        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID')));
        $this->db->from('srp_erp_materialrequestdetails');
        $result = $this->db->get()->row_array();
        if (empty($result)) {
            $this->session->set_flashdata('w', 'There are no records to confirm this document!');
            return false;
        } else {
            $this->db->select('mrAutoID');
            $this->db->where('mrAutoID', trim($this->input->post('mrAutoID')));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_materialrequest');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $this->db->select('MRCode,documentID,DATE_FORMAT(requestedDate, "%Y") as invYear,DATE_FORMAT(requestedDate, "%m") as invMonth,requestedDate');
                $this->db->where('mrAutoID', trim($this->input->post('mrAutoID')));
                $this->db->from('srp_erp_materialrequest');
                $master_dt = $this->db->get()->row_array();

                $docDate=$master_dt['requestedDate'];
                $Comp=current_companyID();
                $companyFinanceYearID = $this->db->query("SELECT
	period.companyFinanceYearID as companyFinanceYearID
FROM
	srp_erp_companyfinanceperiod period
WHERE
	period.companyID = $Comp
AND '$docDate' BETWEEN period.dateFrom
AND period.dateTo
AND period.isActive = 1")->row_array();

                if(empty($companyFinanceYearID['companyFinanceYearID'])){
                    $companyFinanceYearID['companyFinanceYearID']=NULL;
                }

                $this->load->library('sequence');
                if($master_dt['MRCode'] == "0" || empty($master_dt['MRCode'])) {
                    $pvCd = array(
                        'MRCode' => $this->sequence->sequence_generator_fin($master_dt['documentID'], $companyFinanceYearID['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth'])
                    );
                    $this->db->where('mrAutoID', trim($this->input->post('mrAutoID')));
                    $this->db->update('srp_erp_materialrequest', $pvCd);
                }


                $this->load->library('approvals');
                $this->db->select('mrAutoID, MRCode');
                $this->db->where('mrAutoID', trim($this->input->post('mrAutoID')));
                $this->db->from('srp_erp_materialrequest');
                $app_data = $this->db->get()->row_array();

                $approvals_status = $this->approvals->CreateApproval('MR', $app_data['mrAutoID'], $app_data['MRCode'], 'Material Request', 'srp_erp_materialrequest', 'mrAutoID');
                if ($approvals_status == 1) {
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user']
                    );
                    $this->db->where('mrAutoID', trim($this->input->post('mrAutoID')));
                    $this->db->update('srp_erp_materialrequest', $data);
                    $this->session->set_flashdata('s', 'Approvals Created Successfully ');
                    return true;
                } else {
                    return false;
                }
            }
        }

    }


    function save_material_request_detail_multiple_edit()
    {
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $mrDetailID = $this->input->post('mrDetailID');
        $mrAutoID = $this->input->post('mrAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $projectID = $this->input->post('projectID');
        $currentWareHouseStockQty = $this->input->post('currentWareHouseStockQty');
        $quantityRequested = $this->input->post('quantityRequested');
        $a_segment = $this->input->post('a_segment');
        $comment = $this->input->post('comment');

        $this->db->select('*');
        $this->db->from('srp_erp_materialrequest');
        $this->db->where('mrAutoID', $mrAutoID);
        $masterRecord = $this->db->get()->row_array();

        $this->db->trans_start();

        foreach ($itemAutoIDs as $key => $itemAutoID) {

            if (!$mrDetailID[$key]) {
                $this->db->select('mrAutoID,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_materialrequestdetails');
                $this->db->where('mrAutoID', $mrAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Item Request Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            } else {
                $this->db->select('mrAutoID,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_materialrequestdetails');
                $this->db->where('mrAutoID', $mrAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->where('mrDetailID !=', $mrDetailID[$key]);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Item Request Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);

            $data['mrAutoID'] = trim($this->input->post('mrAutoID'));
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasure'] = trim($uomEx[0]);
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['qtyRequested'] = $quantityRequested[$key];
            $data['comments'] = $comment[$key];
            $data['remarks'] = '';
            $data['currentWareHouseStock'] = $currentWareHouseStockQty[$key];
            $data['segmentID'] = $masterRecord['segmentID'];
            $data['segmentCode'] = $masterRecord['segmentCode'];
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];
            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['currentStock'] = $item_data['currentStock'];

            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];

                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }

            $data['totalValue'] = ($data['currentlWacAmount'] * ($data['qtyRequested'] / $data['conversionRateUOM']));
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            if (trim($mrDetailID[$key])) {
                $this->db->where('mrDetailID', trim($mrDetailID[$key]));
                $this->db->update('srp_erp_materialrequestdetails', $data);
            } else {
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_materialrequestdetails', $data);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Item Request Detail :  Update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Item Request Detail :  Updated Successfully.');

        }

    }

    function save_material_request_approval()
    {
        //$this->db->trans_start();
        $this->load->library('approvals');
        $system_code = trim($this->input->post('mrAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        $comments = trim($this->input->post('comments'));

        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'MR');
        if ($approvals_status) {
            if ($status == 1) {
                return array('s', 'Approved Successfully.', 1);
            } else {
                return array('s', 'Rejected Successfully.', 1);
            }

        } else {
            return array('e', 'Approval Failed.', 1);
        }

    }


    function fetch_MR_code()
    {
        $itemIssueAutoID = $this->input->post('itemIssueAutoID');

        $this->db->select('issueDate,itemType,wareHouseAutoID,requestedWareHouseAutoID,segmentID');
        $this->db->from('srp_erp_itemissuemaster');
        $this->db->where('itemIssueAutoID', trim($itemIssueAutoID));
        $result = $this->db->get()->row_array();

        $issueDate=$result['issueDate'];
        $itemType=$result['itemType'];
        $requestedWareHouseAutoID=$result['requestedWareHouseAutoID'];
        $companyID=current_companyID();

        $data = $this->db->query("SELECT
	mrm.mrAutoID,
	MRCode,
	requestedDate,
	employeeName,
IFNULL(SUM(mrqdetail.qtyRequested),0) as qtyRequested,
IFNULL(SUM(mrqdetail.mrQty),0) as mrQty
FROM
	srp_erp_materialrequest mrm
LEFT JOIN (
	SELECT
		mrd.mrDetailID,
		mrd.mrAutoID,
		mrd.qtyRequested,
		sum(
			srp_erp_itemissuedetails.qtyIssued
		) AS mrQty
	FROM
	srp_erp_materialrequestdetails mrd
	LEFT JOIN srp_erp_itemissuedetails ON srp_erp_itemissuedetails.mrDetailID= mrd.mrDetailID
	GROUP BY
		mrDetailID
) mrqdetail ON mrqdetail.mrAutoID = mrm.mrAutoID
WHERE
	mrm.requestedDate <= '$issueDate'
AND mrm.itemType = '$itemType'
AND mrm.wareHouseAutoID = '$requestedWareHouseAutoID'
AND mrm.companyID = '$companyID'
AND mrm.approvedYN = 1
GROUP BY
		mrm.mrAutoID")->result_array();
        return $data;

        /*$this->db->select('mrAutoID,MRCode,requestedDate,employeeName');
        $this->db->from('srp_erp_materialrequest');
        $this->db->where('requestedDate <=', trim($result['issueDate']));
        $this->db->where('itemType', trim($result['itemType']));
        $this->db->where('wareHouseAutoID', trim($result['requestedWareHouseAutoID']));
        $this->db->where('companyID', trim(current_companyID()));
        $this->db->where('approvedYN', 1);
        return $this->db->get()->result_array();*/
    }

    function fetch_mr_detail_table()
    {
        $itemIssueAutoID = trim($this->input->post('itemIssueAutoID'));

        $this->db->select('*');
        $this->db->where('mrAutoID', trim($this->input->post('mrAutoID')));
        $this->db->where('companyID', trim(current_companyID()));
        $this->db->from('srp_erp_materialrequest');
        $master = $this->db->get()->row_array();

        $this->db->select('issueDate,itemType,wareHouseAutoID,requestedWareHouseAutoID');
        $this->db->from('srp_erp_itemissuemaster');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $issueMaster = $this->db->get()->row_array();

        $issueMasterWarehouseID = $issueMaster['wareHouseAutoID'];
        $warehouseID = $master['wareHouseAutoID'];
        $mrAutoID = $this->input->post('mrAutoID');
        $companyID = current_companyID();
        /*$this->db->select('srp_erp_materialrequestdetails.*,srp_erp_itemissuedetails.qtyIssued as qtyIssued,srp_erp_warehouseitems.currentStock as stock');
        $this->db->where('srp_erp_materialrequestdetails.mrAutoID', trim($this->input->post('mrAutoID')));
        $this->db->where('srp_erp_materialrequestdetails.companyID', trim(current_companyID()));
        $this->db->from('srp_erp_materialrequestdetails');
        $this->db->join('srp_erp_itemissuedetails', 'srp_erp_itemissuedetails.mrDetailID = srp_erp_materialrequestdetails.mrDetailID AND srp_erp_itemissuedetails.mrAutoID = srp_erp_materialrequestdetails.mrAutoID', 'left');
        $this->db->join('srp_erp_warehouseitems', 'srp_erp_warehouseitems.itemAutoID = srp_erp_materialrequestdetails.itemAutoID AND srp_erp_warehouseitems.wareHouseAutoID = '.$warehouseID.'', 'left');
        $data['detail'] = $this->db->get()->result_array();*/

        $data['detail'] = $this->db->query("SELECT
	`srp_erp_materialrequestdetails`.*, `det`.`qtyIssued` AS `qtyIssued`,
	`srp_erp_warehouseitems`.`currentStock` AS `stock`,detMaterialIssue.miQtyIssued AS miQtyIssued
FROM
	`srp_erp_materialrequestdetails`
LEFT JOIN (
    SELECT
       COALESCE(SUM(qtyIssued),0) as qtyIssued,mrDetailID,mrAutoID
    FROM
        srp_erp_itemissuedetails
    GROUP BY
        mrDetailID
) AS det ON det.`mrDetailID` = `srp_erp_materialrequestdetails`.`mrDetailID`
AND det.`mrAutoID` = `srp_erp_materialrequestdetails`.`mrAutoID` LEFT JOIN (
	SELECT
		COALESCE(SUM(qtyIssued),0) AS miQtyIssued,
		mrDetailID,
		mrAutoID,
		itemAutoID
	FROM
		srp_erp_itemissuedetails
	WHERE
		itemIssueAutoID = {$itemIssueAutoID}
	GROUP BY
		itemAutoID
) AS detMaterialIssue ON detMaterialIssue.`itemAutoID` = `srp_erp_materialrequestdetails`.`itemAutoID`
LEFT JOIN `srp_erp_warehouseitems` ON `srp_erp_warehouseitems`.`itemAutoID` = `srp_erp_materialrequestdetails`.`itemAutoID`
AND `srp_erp_warehouseitems`.`wareHouseAutoID` = $issueMasterWarehouseID
WHERE
	`srp_erp_materialrequestdetails`.`mrAutoID` = $mrAutoID
AND `srp_erp_materialrequestdetails`.`companyID` = $companyID
GROUP BY srp_erp_materialrequestdetails.mrDetailID")->result_array();
        //echo $this->db->last_query();
        return $data;
    }

    function save_mr_base_items()
    {
        $qty = $this->input->post('qty');
        $mrDetailID = $this->input->post('mrDetailID');
        $itemIssueAutoID = $this->input->post('itemIssueAutoID');
        $this->db->trans_start();

        $this->db->select('*');
        $this->db->from('srp_erp_itemissuemaster');
        $this->db->where('itemIssueAutoID', $itemIssueAutoID);
        $issueMaster = $this->db->get()->row_array();

        $this->db->select('cca.GLAutoID as GLAutoID,ca.systemAccountCode,ca.GLSecondaryCode,ca.GLDescription,ca.subCategory');
        $this->db->from('srp_erp_companycontrolaccounts cca');
        $this->db->join('srp_erp_chartofaccounts ca', 'cca.GLAutoID = ca.GLAutoID', 'LEFT');
        $this->db->where('controlAccountType', 'GIT');
        $this->db->where('cca.companyID', $this->common_data['company_data']['company_id']);
        $materialRequestGlDetail = $this->db->get()->row_array();

        foreach ($mrDetailID as $key => $mrDetailID) {

            if ($qty[$key] != 0) {
                $this->db->select('srp_erp_materialrequestdetails.*');
                $this->db->from('srp_erp_materialrequestdetails');
                $this->db->where('mrDetailID', $mrDetailID);
                $itemDetail = $this->db->get()->row_array();

                if ($itemIssueAutoID) {
                    $this->db->select('itemIssueAutoID,,itemDescription,itemSystemCode');
                    $this->db->from('srp_erp_itemissuedetails');
                    $this->db->where('itemIssueAutoID', $itemIssueAutoID);
                    $this->db->where('mrAutoID', $itemDetail['mrAutoID']);
                    $this->db->where('itemAutoID', $itemDetail['itemAutoID']);
                    $order_detail = $this->db->get()->row_array();
                    if (!empty($order_detail)) {
                        $this->session->set_flashdata('w', 'Item Issue Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                        return array('status' => false);
                        //return array('w', 'Item Issue Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                    }
                }

                $item_data = fetch_item_data($itemDetail['itemAutoID']);
                $data['itemIssueAutoID'] = trim($this->input->post('itemIssueAutoID'));
                $data['mrAutoID'] = trim($itemDetail['mrAutoID']);
                $data['mrDetailID'] = trim($mrDetailID);
                $data['itemAutoID'] = $itemDetail['itemAutoID'];
                $data['itemSystemCode'] = $item_data['itemSystemCode'];
                $data['itemDescription'] = $item_data['itemDescription'];
                $data['unitOfMeasure'] = trim($itemDetail['unitOfMeasure']);
                $data['unitOfMeasureID'] = $itemDetail['unitOfMeasureID'];
                $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
                $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
                $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
                $data['qtyRequested'] = $itemDetail['qtyRequested'];
                $data['qtyIssued'] = $qty[$key];
                $data['comments'] = $itemDetail['comments'];;
                $data['remarks'] = '';
                $data['currentWareHouseStock'] = $itemDetail['currentWareHouseStock'];
                if ($issueMaster['issueType'] != 'Material Request') {
                    $data['segmentID'] = trim($itemDetail['segmentID']);
                    $data['segmentCode'] = trim($itemDetail['segmentCode']);
                }
                $data['itemFinanceCategory'] = $item_data['subcategoryID'];
                $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
                $data['financeCategory'] = $item_data['financeCategory'];
                $data['itemCategory'] = $item_data['mainCategory'];
                $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
                $data['currentStock'] = $item_data['currentStock'];

                /*                if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                                    $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                                    $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                                    $data['PLGLCode'] = $item_data['costGLCode'];
                                    $data['PLDescription'] = $item_data['costDescription'];
                                    $data['PLType'] = $item_data['costType'];

                                    $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                                    $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                                    $data['BLGLCode'] = $item_data['assteGLCode'];
                                    $data['BLDescription'] = $item_data['assteDescription'];
                                    $data['BLType'] = $item_data['assteType'];
                                } elseif ($data['financeCategory'] == 2) {
                                    $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                                    $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                                    $data['PLGLCode'] = $item_data['costGLCode'];
                                    $data['PLDescription'] = $item_data['costDescription'];
                                    $data['PLType'] = $item_data['costType'];

                                    $data['BLGLAutoID'] = '';
                                    $data['BLSystemGLCode'] = '';
                                    $data['BLGLCode'] = '';
                                    $data['BLDescription'] = '';
                                    $data['BLType'] = '';
                                }*/

                $data['PLGLAutoID'] = $materialRequestGlDetail['GLAutoID'];
                $data['PLSystemGLCode'] = $materialRequestGlDetail['systemAccountCode'];
                $data['PLGLCode'] = $materialRequestGlDetail['GLSecondaryCode'];
                $data['PLDescription'] = $materialRequestGlDetail['GLDescription'];
                $data['PLType'] = $materialRequestGlDetail['subCategory'];

                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];

                $data['totalValue'] = ($data['currentlWacAmount'] * ($data['qtyIssued'] / $data['conversionRateUOM']));
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];

                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_itemissuedetails', $data);
            }

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Material Request : Details Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Material Requestt : Item Details Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }

    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'SLR');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }

    function fetch_signaturelevel_purchasereturn()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'SR');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }

    function fetch_signaturelevel_material_issue()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'MI');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }

    function fetch_signaturelevel_stock_transfer()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'ST');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }

    function fetch_signaturelevel_stock_adjustment()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'SA');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }

    function fetch_signaturelevel_material_request()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'MR');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }
}