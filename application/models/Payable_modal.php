<?php

class Payable_modal extends ERP_Model
{
    function save_supplier_invoice_header()
    {
        $this->db->trans_start();

        $date_format_policy = date_format_policy();
        $bookDate = $this->input->post('bookingDate');
        $bookingDate = input_format_date($bookDate, $date_format_policy);
        $supplirInvDuDate = $this->input->post('supplierInvoiceDueDate');
        $supplierInvoiceDueDate = input_format_date($supplirInvDuDate, $date_format_policy);
        $supplierid = $this->input->post('supplierID');
       $supplierinvoice = $this->input->post('supplier_invoice_no');

        $supplirinvoiceDate = $this->input->post('invoiceDate');
        $supplierinvoiceDate_new = input_format_date($supplirinvoiceDate, $date_format_policy);
        $invoiceautoid = $this->input->post('InvoiceAutoID');
        $currency_code = explode('|', trim($this->input->post('currency_code')));
        $location = explode('|', trim($this->input->post('location')));
        //$period = explode('|', trim($this->input->post('financeyear_period')));
        $year = explode(' - ', trim($this->input->post('companyFinanceYear')));
        $FYBegin = input_format_date($year[0], $date_format_policy);
        $FYEnd = input_format_date($year[1], $date_format_policy);

        $supplier_arr = $this->fetch_supplier_data(trim($this->input->post('supplierID')));
        $data['invoiceType'] = trim($this->input->post('invoiceType'));
        $data['bookingDate'] = trim($bookingDate);
        $data['invoiceDueDate'] = trim($supplierInvoiceDueDate);
        $data['invoiceDate'] = trim($supplierinvoiceDate_new);
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear'));
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear'));
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period'));
        //$data['FYPeriodDateFrom'] = trim($period[0]);
        //$data['FYPeriodDateTo'] = trim($period[1]);
        $data['documentID'] = 'BSI';
        $data['supplierID'] = trim($supplierid);
        $data['supplierCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
        $data['supplierAddress'] = $supplier_arr['supplierAddress1'];
        $data['supplierTelephone'] = $supplier_arr['supplierTelephone'];
        $data['supplierFax'] = $supplier_arr['supplierFax'];
        $data['supplierliabilityAutoID'] = $supplier_arr['liabilityAutoID'];
        $data['supplierliabilitySystemGLCode'] = $supplier_arr['liabilitySystemGLCode'];
        $data['supplierliabilityGLAccount'] = $supplier_arr['liabilityGLAccount'];
        $data['supplierliabilityDescription'] = $supplier_arr['liabilityDescription'];
        $data['supplierliabilityType'] = $supplier_arr['liabilityType'];
        $data['supplierInvoiceNo'] = trim($supplierinvoice);
        $data['supplierInvoiceDate'] = trim($this->input->post('supplierInvoiceDueDate'));
        $data['transactionCurrency'] = trim($this->input->post('transactionCurrency'));
        $data['RefNo'] = trim($this->input->post('referenceno'));
        $data['comments'] = trim($this->input->post('comments'));
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


        if (trim($this->input->post('InvoiceAutoID'))) {
            $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID')));
            $this->db->update('srp_erp_paysupplierinvoicemaster', $data);

            if(!empty($supplierinvoice) || $supplierinvoice!='') {
                $q = "SELECT
                      supplierInvoiceNo,InvoiceAutoID
                FROM
                    srp_erp_paysupplierinvoicemaster
                WHERE
               InvoiceAutoID!='" . $invoiceautoid . "' AND supplierID = '". $supplierid ."' AND  supplierInvoiceNo = '" . $supplierinvoice . "' ";
                $result = $this->db->query($q)->row_array();
                if ($result) {
                    $this->session->set_flashdata('e', 'Supplier Invoice Number already exist for the selected supplier');
                    $this->db->trans_rollback();
                    return array('status' => false);
                }
                else
                {
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->session->set_flashdata('e', 'Supplier Invoice : ' . $data['documentID'] . ' Update Failed ' . $this->db->_error_message());
                        $this->db->trans_rollback();
                        return array('status' => false);
                    } else {
                        $this->session->set_flashdata('s', 'Supplier Invoice : ' . $data['documentID'] . ' Updated Successfully.');
                        $this->db->trans_commit();
                        return array('status' => true, 'last_id' => $this->input->post('InvoiceAutoID'));
                    }
                }
            }
            else {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Supplier Invoice : ' . $data['documentID'] . ' Update Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Supplier Invoice : ' . $data['documentID'] . ' Updated Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $this->input->post('InvoiceAutoID'));
                }
            }
        } else {
            if (!empty($supplierinvoice) || $supplierinvoice != '') {
                $q = "SELECT
                    supplierInvoiceNo,supplierID
                FROM
                    srp_erp_paysupplierinvoicemaster
                WHERE
                 supplierID = '". $supplierid ."'  AND supplierInvoiceNo = '". $supplierinvoice ."'";
                $result = $this->db->query($q)->row_array();
                if ($result) {
                    $this->session->set_flashdata('e', ' Supplier Invoice Number already exist for the selected supplier');
                    $this->db->trans_rollback();
                    return array('status' => false);
                }
                }
                //$this->load->library('sequence');
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['bookingInvCode'] = 0;
                $this->db->insert('srp_erp_paysupplierinvoicemaster', $data);
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Supplier Invoice   Saved Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Supplier Invoice Saved Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $last_id);
                }


        }
    }

    function laad_supplier_invoice_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(bookingDate,\'' . $convertFormat . '\') AS bookingDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate');
        $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
        return $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();
    }

    function fetch_supplier_invoice_template_data($InvoiceAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(bookingDate,\'' . $convertFormat . '\') AS bookingDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,(DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\')) AS approvedDate');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        //$this->db->where('approvedYN', 1);
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('supplierSystemCode,supplierName,supplierAddress1,supplierTelephone,supplierFax,supplierEmail');
        $this->db->where('supplierAutoID', $data['master']['supplierID']);
        $this->db->from('srp_erp_suppliermaster');
        $data['supplier'] = $this->db->get()->row_array();

        $this->db->select('GLCode,GLDescription,segmentCode,transactionAmount,companyLocalAmount,supplierAmount,description');
        // $this->db->group_by("GLCode");
        // $this->db->group_by("segmentCode");
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $this->db->from('srp_erp_paysupplierinvoicedetail');
        $data['detail'] = $this->db->get()->result_array();
        $this->db->select('segmentCode,transactionAmount,companyLocalAmount,supplierAmount,grvPrimaryCode ,grvDocRefNo,grvDate,description');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $this->db->from('srp_erp_paysupplierinvoicedetail');
        $data['grv_detail'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $data['tax'] = $this->db->get('srp_erp_paysupplierinvoicetaxdetails')->result_array();
        return $data;
    }

    function fetch_debit_note_template_data($debitNoteMasterAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(debitNoteDate,\'' . $convertFormat . '\') AS debitNoteDate,(DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\')) AS approvedDate');
        $this->db->where('debitNoteMasterAutoID', $debitNoteMasterAutoID);
        $this->db->from('srp_erp_debitnotemaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('supplierSystemCode,supplierName,supplierAddress1,supplierTelephone,supplierFax,supplierEmail');
        $this->db->where('supplierAutoID', $data['master']['supplierID']);
        $this->db->from('srp_erp_suppliermaster');
        $data['supplier'] = $this->db->get()->row_array();


        $this->db->select('GLCode,GLDescription,segmentCode,transactionAmount,companyLocalAmount,supplierAmount ,bookingInvCode,description, isFromInvoice');
        // $this->db->group_by("GLCode");
        // $this->db->group_by("segmentCode");
        $this->db->where('debitNoteMasterAutoID', $debitNoteMasterAutoID);
        $this->db->where('isFromInvoice', 1);
        $this->db->from('srp_erp_debitnotedetail');
        $data['detail'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('debitNoteMasterAutoID', $debitNoteMasterAutoID);
        $this->db->where('isFromInvoice', 0);
        $this->db->from('srp_erp_debitnotedetail');
        $data['detail_glCode'] = $this->db->get()->result_array();
        return $data;
    }

    function fetch_supplier_invoice_detail()
    {
        $data = array();
        $this->db->select('*');
        $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
        $data['detail'] = $this->db->get('srp_erp_paysupplierinvoicedetail')->result_array();
        $this->db->select('*');
        $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
        $data['tax'] = $this->db->get('srp_erp_paysupplierinvoicetaxdetails')->result_array();
        return $data;
    }

    function fetch_supplier_data($supplierID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID);
        return $this->db->get()->row_array();
    }

    function fetch_supplier_invoice_grv($segmentID, $bookingDate)
    {
        $convertFormat = convert_date_format_sql();
        $date_format_policy = date_format_policy();
        $bookingDate = input_format_date($bookingDate, $date_format_policy);
        $supplierID = trim($this->input->post('supplierID'));
        $currencyID = trim($this->input->post('currencyID'));
        return $this->db->query("SELECT DATE_FORMAT(srp_erp_grvmaster.grvDate,'$convertFormat') AS grvDate, srp_erp_match_supplierinvoice.isAddon,srp_erp_match_supplierinvoice.match_supplierinvoiceAutoID,srp_erp_grvmaster.grvAutoID,srp_erp_grvmaster.grvPrimaryCode,srp_erp_grvmaster.grvDocRefNo,srp_erp_match_supplierinvoice.bookingAmount,srp_erp_match_supplierinvoice.invoicedTotalAmount,srp_erp_match_supplierinvoice.isAddon,srp_erp_stockreturndetails.totalValue FROM srp_erp_grvmaster INNER JOIN srp_erp_match_supplierinvoice ON srp_erp_grvmaster.grvAutoID = srp_erp_match_supplierinvoice.grvAutoID LEFT JOIN srp_erp_stockreturndetails ON srp_erp_grvmaster.grvAutoID = srp_erp_stockreturndetails.grvAutoID WHERE srp_erp_grvmaster.confirmedYN = 1 AND srp_erp_grvmaster.approvedYN = 1 AND srp_erp_match_supplierinvoice.supplierInvoiceYN = 0 AND srp_erp_match_supplierinvoice.supplierID = '{$supplierID}' AND `bookingCurrencyID` = '{$currencyID}' AND `grvDate` <= '{$bookingDate}'")->result_array();
    }

    function save_bsi_detail()
    {
        $this->db->trans_start();
        $projectExist = project_is_exist();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate,transactionExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
        $master = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

        $segment = explode('|', trim($this->input->post('segment_gl')));
        $gl_code = explode(' | ', trim($this->input->post('gl_code_des')));
        $data['InvoiceAutoID'] = trim($this->input->post('InvoiceAutoID'));
        $data['GLAutoID'] = trim($this->input->post('gl_code'));
        if($projectExist == 1){
            $projectID = trim($this->input->post('projectID'));
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'],$projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['systemGLCode'] = trim($gl_code[0]);
        $data['GLCode'] = trim($gl_code[1]);
        $data['GLDescription'] = trim($gl_code[2]);
        $data['GLType'] = trim($gl_code[3]);
        $data['segmentID'] = trim($segment[0]);
        $data['segmentCode'] = trim($segment[1]);
        $data['description'] = trim($this->input->post('description'));
        $data['transactionAmount'] = round($this->input->post('amount'), $master['transactionCurrencyDecimalPlaces']);
        $data['transactionExchangeRate'] = $master['transactionExchangeRate'];

        $companyLocalAmount=0;
        if($master['companyLocalExchangeRate'])
        {
            $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
        }

        $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
        $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $companyReportingAmount=0;
        if($master['companyReportingExchangeRate'])
        {
            $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
        }
        $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
        $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];

        $supplierAmount =0;
        if($master['supplierCurrencyExchangeRate'])
        {
            $supplierAmount = $data['transactionAmount'] / $master['supplierCurrencyExchangeRate'];
        }

        $data['supplierAmount'] = round($supplierAmount, $master['supplierCurrencyDecimalPlaces']);
        $data['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('InvoiceDetailAutoID'))) {
            $this->db->where('InvoiceDetailAutoID', trim($this->input->post('InvoiceDetailAutoID')));
            $this->db->update('srp_erp_paysupplierinvoicedetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Supplier Invoice Detail : ' . $data['GLDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Supplier Invoice Detail : ' . $data['GLDescription'] . ' Updated Successfully.');
            }
        } else {
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_paysupplierinvoicedetail', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Supplier Invoice Detail : ' . $data['GLDescription'] . '  Saved Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Supplier Invoice Detail : ' . $data['GLDescription'] . ' Saved Successfully.');

            }
        }
    }

    function save_bsi_detail_multiple()
    {
        $this->db->trans_start();

        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate,transactionExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
        $master = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

        $gl_codes = $this->input->post('gl_code');
        $gl_code_des = $this->input->post('gl_code_des');
        $amount = $this->input->post('amount');
        $descriptions = $this->input->post('description');
        $segment_gls = $this->input->post('segment_gl');
        $projectExist = project_is_exist();
        $projectID = $this->input->post('projectID');

        foreach ($gl_codes as $key => $gl_code) {
            $segment = explode('|', $segment_gls[$key]);
            $gl_code = explode('|', $gl_code_des[$key]);

            $data[$key]['InvoiceAutoID'] = trim($this->input->post('InvoiceAutoID'));
            $data[$key]['GLAutoID'] = $gl_codes[$key];
            $data[$key]['projectID'] = $projectID[$key];
            if($projectExist == 1){
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'],$projectCurrency);
                $data[$key]['projectID'] = $projectID[$key];
                $data[$key]['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data[$key]['systemGLCode'] = trim($gl_code[0]);
            $data[$key]['GLCode'] = trim($gl_code[1]);
            $data[$key]['GLDescription'] = trim($gl_code[2]);
            $data[$key]['GLType'] = trim($gl_code[3]);
            $data[$key]['segmentID'] = trim($segment[0]);
            $data[$key]['segmentCode'] = trim($segment[1]);
            $data[$key]['description'] = $descriptions[$key];
            $data[$key]['transactionAmount'] = round($amount[$key], $master['transactionCurrencyDecimalPlaces']);
            $data[$key]['transactionExchangeRate'] = $master['transactionExchangeRate'];

            $companyLocalAmount =0;
            if($master['companyLocalExchangeRate'])
            {
                $companyLocalAmount = $data[$key]['transactionAmount'] / $master['companyLocalExchangeRate'];
            }

            $data[$key]['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $data[$key]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $companyReportingAmount = 0;

            if($master['companyReportingExchangeRate'])
            {
                $companyReportingAmount = $data[$key]['transactionAmount'] / $master['companyReportingExchangeRate'];
            }

            $data[$key]['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $data[$key]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $supplierAmount = 0;
            if($master['supplierCurrencyExchangeRate']){
                $supplierAmount = $data[$key]['transactionAmount'] / $master['supplierCurrencyExchangeRate'];
            }

            $data[$key]['supplierAmount'] = round($supplierAmount, $master['supplierCurrencyDecimalPlaces']);
            $data[$key]['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $data[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdUserName'] = $this->common_data['current_user'];
            $data[$key]['createdDateTime'] = $this->common_data['current_date'];
        }
        $this->db->insert_batch('srp_erp_paysupplierinvoicedetail', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            //$this->session->set_flashdata('e', 'Supplier Invoice Detail : Saved Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('e', 'Supplier Invoice Detail : Saved Failed ');
        } else {
            //$this->session->set_flashdata('s', 'Supplier Invoice Detail : Saved Successfully.');
            $this->db->trans_commit();
            return array('s', 'Supplier Invoice Detail : Saved Successfully.');
        }

    }

    function supplier_invoice_confirmation()
    {
        $this->db->select('InvoiceAutoID');
        $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID')));
        $this->db->from('srp_erp_paysupplierinvoicedetail');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            $this->session->set_flashdata('w', 'There are no records to confirm this document!');
            return false;
        }
        else {
            $this->db->select('InvoiceAutoID');
            $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID')));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_paysupplierinvoicemaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return array('status' => true);
            } else {
                $this->db->trans_start();
                $system_id = trim($this->input->post('InvoiceAutoID'));
                $this->db->select('bookingInvCode,companyFinanceYearID,DATE_FORMAT(bookingDate, "%Y") as invYear,DATE_FORMAT(bookingDate, "%m") as invMonth');
                $this->db->where('InvoiceAutoID', $system_id);
                $this->db->from('srp_erp_paysupplierinvoicemaster');
                $master_dt = $this->db->get()->row_array();
                $this->load->library('sequence');
                $lenth=strlen($master_dt['bookingInvCode']);
                if($lenth == 1){
                    $invcod = array(
                        'bookingInvCode' => $this->sequence->sequence_generator_fin('BSI',$master_dt['companyFinanceYearID'],$master_dt['invYear'],$master_dt['invMonth']),
                    );
                    $this->db->where('InvoiceAutoID', $system_id);
                    $this->db->update('srp_erp_paysupplierinvoicemaster', $invcod);
                }

                $this->load->library('approvals');
                $this->db->select('InvoiceAutoID, bookingInvCode,transactionCurrency,transactionExchangeRate,companyFinanceYearID,DATE_FORMAT(bookingDate, "%Y") as invYear,DATE_FORMAT(bookingDate, "%m") as invMonth');
                $this->db->where('InvoiceAutoID', $system_id);
                $this->db->from('srp_erp_paysupplierinvoicemaster');
                $master_data = $this->db->get()->row_array();

                $approvals_status = $this->approvals->CreateApproval('BSI', $master_data['InvoiceAutoID'], $master_data['bookingInvCode'], 'Supplier Invoice', 'srp_erp_paysupplierinvoicemaster', 'InvoiceAutoID');
                if ($approvals_status == 1) {
                    $transa_total_amount = 0;
                    $loca_total_amount = 0;
                    $rpt_total_amount = 0;
                    $supplier_total_amount = 0;
                    $t_arr = array();
                    $tra_tax_total = 0;
                    $loca_tax_total = 0;
                    $rpt_tax_total = 0;
                    $sup_tax_total = 0;
                    $this->db->select('sum(transactionAmount) as transactionAmount,sum(companyLocalAmount) as companyLocalAmount,sum(companyReportingAmount) as companyReportingAmount,sum(supplierAmount) as supplierAmount');
                    $this->db->where('InvoiceAutoID', $system_id);
                    $data_arr = $this->db->get('srp_erp_paysupplierinvoicedetail')->row_array();

                    $transa_total_amount += $data_arr['transactionAmount'];
                    $loca_total_amount += $data_arr['companyLocalAmount'];
                    $rpt_total_amount += $data_arr['companyReportingAmount'];
                    $supplier_total_amount += $data_arr['supplierAmount'];

                    $this->db->select('taxDetailAutoID,supplierCurrencyExchangeRate,companyReportingExchangeRate,companyLocalExchangeRate ,taxPercentage');
                    $this->db->where('InvoiceAutoID', $system_id);
                    $tax_arr = $this->db->get('srp_erp_paysupplierinvoicetaxdetails')->result_array();
                    for ($x = 0; $x < count($tax_arr); $x++) {
                        $tax_total_amount = (($tax_arr[$x]['taxPercentage'] / 100) * $transa_total_amount);
                        $t_arr[$x]['taxDetailAutoID'] = $tax_arr[$x]['taxDetailAutoID'];
                        $t_arr[$x]['transactionAmount'] = $tax_total_amount;
                        $t_arr[$x]['supplierCurrencyAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['supplierCurrencyExchangeRate']);
                        $t_arr[$x]['companyLocalAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyLocalExchangeRate']);
                        $t_arr[$x]['companyReportingAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyReportingExchangeRate']);
                        $tra_tax_total = $t_arr[$x]['transactionAmount'];
                        $sup_tax_total = $t_arr[$x]['supplierCurrencyAmount'];
                        $loca_tax_total = $t_arr[$x]['companyLocalAmount'];
                        $rpt_tax_total = $t_arr[$x]['companyReportingAmount'];
                    }
                    /*updating transaction amount using the query used in the master data table  done by mushtaq*/
                    $companyID = current_companyID();
                    $r1 = "SELECT
srp_erp_paysupplierinvoicemaster.InvoiceAutoID,
	`srp_erp_paysupplierinvoicemaster`.`companyLocalExchangeRate` AS `companyLocalExchangeRate`,
	`srp_erp_paysupplierinvoicemaster`.`companyLocalCurrencyDecimalPlaces` AS `companyLocalCurrencyDecimalPlaces`,
	`srp_erp_paysupplierinvoicemaster`.`companyReportingExchangeRate` AS `companyReportingExchangeRate`,
	`srp_erp_paysupplierinvoicemaster`.`companyReportingCurrencyDecimalPlaces` AS `companyReportingCurrencyDecimalPlaces`,
	`srp_erp_paysupplierinvoicemaster`.`supplierCurrencyExchangeRate` AS `supplierCurrencyExchangeRate`,
	`srp_erp_paysupplierinvoicemaster`.`supplierCurrencyDecimalPlaces` AS `supplierCurrencyDecimalPlaces`,
	`srp_erp_paysupplierinvoicemaster`.`transactionCurrencyDecimalPlaces` AS `transactionCurrencyDecimalPlaces`,
	(
		(
			(
				IFNULL(addondet.taxPercentage, 0) / 100
			) * IFNULL(det.transactionAmount, 0)
		) + IFNULL(det.transactionAmount, 0)
	) AS total_value
FROM
	`srp_erp_paysupplierinvoicemaster`
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		InvoiceAutoID
	FROM
		srp_erp_paysupplierinvoicedetail
	GROUP BY
		InvoiceAutoID
) det ON (
	`det`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		InvoiceAutoID
	FROM
		srp_erp_paysupplierinvoicetaxdetails
	GROUP BY
		InvoiceAutoID
) addondet ON (
	`addondet`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
)
WHERE
	`companyID` = $companyID
	AND srp_erp_paysupplierinvoicemaster.InvoiceAutoID = $system_id ";
                    $totalValue = $this->db->query($r1)->row_array();
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user'],
                        'companyLocalAmount' => (round($totalValue['total_value'] / $totalValue['companyLocalExchangeRate'], $totalValue['companyLocalCurrencyDecimalPlaces'])),
                        'companyReportingAmount' => (round($totalValue['total_value'] / $totalValue['companyReportingExchangeRate'], $totalValue['companyReportingCurrencyDecimalPlaces'])),
                        'supplierCurrencyAmount' => (round($totalValue['total_value'] / $totalValue['supplierCurrencyExchangeRate'], $totalValue['supplierCurrencyDecimalPlaces'])),
                        'transactionAmount' => (round($totalValue['total_value'], $totalValue['transactionCurrencyDecimalPlaces'])),
                    );
                    $this->db->where('InvoiceAutoID', $system_id);
                    $this->db->update('srp_erp_paysupplierinvoicemaster', $data);
                    if (!empty($t_arr)) {
                        $this->db->update_batch('srp_erp_paysupplierinvoicetaxdetails', $t_arr, 'taxDetailAutoID');
                    }
                }else if($approvals_status==3){
                    $this->session->set_flashdata('w', 'There are no users exist to perform approval for this document.');
                    return array('status' => true);
                }else{
                    $this->session->set_flashdata('e', 'Confirmation failed.');
                    return array('status' => false);
                }

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Supplier Invoice Confirmed failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Supplier Invoice Confirmed Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true);
                }
            }
        }

    }

    function delete_bsi_detail()
    {
        $this->db->select('match_supplierinvoiceAutoID,transactionAmount');
        $this->db->from('srp_erp_paysupplierinvoicedetail');
        $this->db->where('InvoiceDetailAutoID', trim($this->input->post('InvoiceDetailAutoID')));
        $detail_arr = $this->db->get()->row_array();
        $company_id = $this->common_data['company_data']['company_id'];
        $match_id = $detail_arr['match_supplierinvoiceAutoID'];
        $number = $detail_arr['transactionAmount'];
        $status = 0;
        $this->db->query("UPDATE srp_erp_match_supplierinvoice SET invoicedTotalAmount = (invoicedTotalAmount -{$number})  , supplierInvoiceYN = {$status}  WHERE match_supplierinvoiceAutoID='{$match_id}' and companyID='{$company_id}'");
        $this->db->delete('srp_erp_paysupplierinvoicedetail', array('InvoiceDetailAutoID' => trim($this->input->post('InvoiceDetailAutoID'))));
        return true;
    }

    function delete_tax_detail()
    {
        $this->db->delete('srp_erp_paysupplierinvoicetaxdetails', array('taxDetailAutoID' => trim($this->input->post('taxDetailAutoID'))));
        return true;
    }

    function fetch_bsi_detail()
    {
        $this->db->select('*');
        $this->db->where('InvoiceDetailAutoID', $this->input->post('InvoiceDetailAutoID'));
        return $this->db->get('srp_erp_paysupplierinvoicedetail')->row_array();
    }

    /*function save_grv_base_items()
    {
        $this->db->trans_start();
        $this->db->select('grvAutoID,grvType,companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate, grvPrimaryCode ,grvDocRefNo,supplierliabilityAutoID,supplierliabilitySystemGLCode,supplierliabilityGLAccount,supplierliabilityType,supplierliabilityDescription,grvDate,grvNarration,segmentID,segmentCode,invoicedTotalAmount,transactionAmount,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces');
        $this->db->from('srp_erp_grvmaster');
        $this->db->where_in('srp_erp_grvmaster.grvAutoID', $this->input->post('grvAutoID'));
        $master_recode = $this->db->get()->result_array();
        $amount = $this->input->post('amounts');
        $match = $this->input->post('match');
        for ($i = 0; $i < count($master_recode); $i++) {
            $this->db->select('bookingCurrencyExchangeRate, supplierCurrencyExchangeRate, companyLocalExchangeRate, companyReportingExchangeRate');
            $this->db->where('match_supplierinvoiceAutoID', $match[$i]);
            $this->db->from('srp_erp_match_supplierinvoice');
            $match_data = $this->db->get()->row_array();
            $data[$i]['InvoiceAutoID'] = $this->input->post('InvoiceAutoID');
            $data[$i]['grvAutoID'] = $master_recode[$i]['grvAutoID'];
            $data[$i]['grvType'] = 'GRV Base';
            $data[$i]['match_supplierinvoiceAutoID'] = $match[$i];
            $data[$i]['grvPrimaryCode'] = $master_recode[$i]['grvPrimaryCode'];
            $data[$i]['grvDocRefNo'] = $master_recode[$i]['grvDocRefNo'];
            $data[$i]['grvDate'] = $master_recode[$i]['grvDate'];
            $data[$i]['segmentID'] = $master_recode[$i]['segmentID'];
            $data[$i]['segmentCode'] = $master_recode[$i]['segmentCode'];
            $data[$i]['GLAutoID'] = $master_recode[$i]['supplierliabilityAutoID'];
            $data[$i]['systemGLCode'] = $master_recode[$i]['supplierliabilitySystemGLCode'];
            $data[$i]['GLCode'] = $master_recode[$i]['supplierliabilityGLAccount'];
            $data[$i]['GLDescription'] = $master_recode[$i]['supplierliabilityDescription'];
            $data[$i]['GLType'] = $master_recode[$i]['supplierliabilityType'];
            $data[$i]['description'] = $master_recode[$i]['grvNarration'];
            $transactionAmount = $amount[$i] / $match_data['bookingCurrencyExchangeRate'];
            $data[$i]['transactionAmount'] = round($transactionAmount, $master_recode[$i]['transactionCurrencyDecimalPlaces']);
            $data[$i]['transactionExchangeRate'] = $match_data['bookingCurrencyExchangeRate'];
            $companyLocalAmount = $data[$i]['transactionAmount'] / $match_data['companyLocalExchangeRate'];
            $data[$i]['companyLocalAmount'] = round($companyLocalAmount, $master_recode[$i]['companyLocalCurrencyDecimalPlaces']);
            $data[$i]['companyLocalExchangeRate'] = $match_data['companyLocalExchangeRate'];
            $companyReportingAmount = $data[$i]['transactionAmount'] / $match_data['companyReportingExchangeRate'];
            $data[$i]['companyReportingAmount'] = round($companyReportingAmount, $master_recode[$i]['companyReportingCurrencyDecimalPlaces']);
            $data[$i]['companyReportingExchangeRate'] = $match_data['companyReportingExchangeRate'];
            $supplierAmount = $data[$i]['transactionAmount'] / $match_data['supplierCurrencyExchangeRate'];
            $data[$i]['supplierAmount'] = round($supplierAmount, $master_recode[$i]['supplierCurrencyDecimalPlaces']);
            $data[$i]['supplierCurrencyExchangeRate'] = $match_data['supplierCurrencyExchangeRate'];
            $data[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$i]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$i]['modifiedUserID'] = $this->common_data['current_userID'];
            $data[$i]['modifiedUserName'] = $this->common_data['current_user'];
            $data[$i]['modifiedDateTime'] = $this->common_data['current_date'];
            $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$i]['createdPCID'] = $this->common_data['current_pc'];
            $data[$i]['createdUserID'] = $this->common_data['current_userID'];
            $data[$i]['createdUserName'] = $this->common_data['current_user'];
            $data[$i]['createdDateTime'] = $this->common_data['current_date'];
            $company_id = $this->common_data['company_data']['company_id'];
            $match_id = $data[$i]['match_supplierinvoiceAutoID'];
            $number = $transactionAmount;
            $status = 0;

            $this->db->select('invoicedTotalAmount, bookingAmount');
            $this->db->from('srp_erp_match_supplierinvoice');
            $this->db->where('match_supplierinvoiceAutoID', $match_id);
            $inv_data = $this->db->get()->row_array();
            if ($inv_data['bookingAmount'] <= ($number + $inv_data['invoicedTotalAmount'])) {
                $status = 1;
            }

            $this->db->query("UPDATE srp_erp_match_supplierinvoice SET invoicedTotalAmount = (invoicedTotalAmount +{$number}) , supplierInvoiceYN = '{$status}'  WHERE match_supplierinvoiceAutoID='{$match_id}' and companyID='{$company_id}'");
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_paysupplierinvoicedetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Good Received note : Details Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Good Received note : ' . count($master_recode) . ' Item Details Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true);
            }
        } else {
            return array('status' => false);
        }
    }*/


    function save_grv_base_items()
    {
        $this->db->trans_start();

        $amount = $this->input->post('amounts');
        $match = $this->input->post('match');
        $grvAutoID = $this->input->post('grvAutoID');



        $this->db->select('bookingCurrencyExchangeRate, supplierCurrencyExchangeRate, companyLocalExchangeRate, companyReportingExchangeRate');
        $this->db->where_in('match_supplierinvoiceAutoID', $match);
        $this->db->from('srp_erp_match_supplierinvoice');
        $match_data = $this->db->get()->result_array();

        for ($i = 0; $i < count($match_data); $i++) {
            $this->db->select('grvAutoID,grvType,companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate, grvPrimaryCode ,grvDocRefNo,supplierliabilityAutoID,supplierliabilitySystemGLCode,supplierliabilityGLAccount,supplierliabilityType,supplierliabilityDescription,grvDate,grvNarration,segmentID,segmentCode,invoicedTotalAmount,transactionAmount,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces');
            $this->db->from('srp_erp_grvmaster');
            $this->db->where('srp_erp_grvmaster.grvAutoID', $grvAutoID[$i]);
            $master_recode = $this->db->get()->row_array();

            $data[$i]['InvoiceAutoID'] = $this->input->post('InvoiceAutoID');
            $data[$i]['grvAutoID'] = $master_recode['grvAutoID'];
            $data[$i]['grvType'] = 'GRV Base';
            $data[$i]['match_supplierinvoiceAutoID'] = $match[$i];
            $data[$i]['grvPrimaryCode'] = $master_recode['grvPrimaryCode'];
            $data[$i]['grvDocRefNo'] = $master_recode['grvDocRefNo'];
            $data[$i]['grvDate'] = $master_recode['grvDate'];
            $data[$i]['segmentID'] = $master_recode['segmentID'];
            $data[$i]['segmentCode'] = $master_recode['segmentCode'];
            $data[$i]['GLAutoID'] = $master_recode['supplierliabilityAutoID'];
            $data[$i]['systemGLCode'] = $master_recode['supplierliabilitySystemGLCode'];
            $data[$i]['GLCode'] = $master_recode['supplierliabilityGLAccount'];
            $data[$i]['GLDescription'] = $master_recode['supplierliabilityDescription'];
            $data[$i]['GLType'] = $master_recode['supplierliabilityType'];
            $data[$i]['description'] = $master_recode['grvNarration'];
            $transactionAmount = $amount[$i] / $match_data[$i]['bookingCurrencyExchangeRate'];
            $data[$i]['transactionAmount'] = round($transactionAmount, $master_recode['transactionCurrencyDecimalPlaces']);
            $data[$i]['transactionExchangeRate'] = $match_data[$i]['bookingCurrencyExchangeRate'];
            $companyLocalAmount = $data[$i]['transactionAmount'] / $match_data[$i]['companyLocalExchangeRate'];
            $data[$i]['companyLocalAmount'] = round($companyLocalAmount, $master_recode['companyLocalCurrencyDecimalPlaces']);
            $data[$i]['companyLocalExchangeRate'] = $match_data[$i]['companyLocalExchangeRate'];
            $companyReportingAmount = $data[$i]['transactionAmount'] / $match_data[$i]['companyReportingExchangeRate'];
            $data[$i]['companyReportingAmount'] = round($companyReportingAmount, $master_recode['companyReportingCurrencyDecimalPlaces']);
            $data[$i]['companyReportingExchangeRate'] = $match_data[$i]['companyReportingExchangeRate'];
            $supplierAmount = $data[$i]['transactionAmount'] / $match_data[$i]['supplierCurrencyExchangeRate'];
            $data[$i]['supplierAmount'] = round($supplierAmount, $master_recode['supplierCurrencyDecimalPlaces']);
            $data[$i]['supplierCurrencyExchangeRate'] = $match_data[$i]['supplierCurrencyExchangeRate'];
            $data[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$i]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$i]['modifiedUserID'] = $this->common_data['current_userID'];
            $data[$i]['modifiedUserName'] = $this->common_data['current_user'];
            $data[$i]['modifiedDateTime'] = $this->common_data['current_date'];
            $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$i]['createdPCID'] = $this->common_data['current_pc'];
            $data[$i]['createdUserID'] = $this->common_data['current_userID'];
            $data[$i]['createdUserName'] = $this->common_data['current_user'];
            $data[$i]['createdDateTime'] = $this->common_data['current_date'];
            $company_id = $this->common_data['company_data']['company_id'];
            $match_id = $data[$i]['match_supplierinvoiceAutoID'];
            $number = $transactionAmount;
            $status = 0;

            $this->db->select('invoicedTotalAmount, bookingAmount');
            $this->db->from('srp_erp_match_supplierinvoice');
            $this->db->where('match_supplierinvoiceAutoID', $match_id);
            $inv_data = $this->db->get()->row_array();
            if ($inv_data['bookingAmount'] <= ($number + $inv_data['invoicedTotalAmount'])) {
                $status = 1;
            }

            $this->db->query("UPDATE srp_erp_match_supplierinvoice SET invoicedTotalAmount = (invoicedTotalAmount +{$number}) , supplierInvoiceYN = '{$status}'  WHERE match_supplierinvoiceAutoID='{$match_id}' and companyID='{$company_id}'");
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_paysupplierinvoicedetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Good Received note : Details Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Good Received note : ' . count($master_recode) . ' Item Details Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true);
            }
        } else {
            return array('status' => false);
        }
    }

    function delete_supplier_invoice()
    {
        /*$this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
        $results = $this->db->delete('srp_erp_paysupplierinvoicemaster');
        if ($results) {
            $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
            $this->db->delete('srp_erp_paysupplierinvoicedetail');
            $this->session->set_flashdata('s', 'Item Deleted Successfully');
            return true;
        }*/
        $this->db->select('*');
        $this->db->from('srp_erp_paysupplierinvoicedetail');
        $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID')));
        $datas = $this->db->get()->row_array();

        $this->db->select('bookingInvCode');
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID')));
        $master = $this->db->get()->row_array();

        if ($datas) {
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        } else {
            if($master['bookingInvCode']=="0"){
                $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
                $results = $this->db->delete('srp_erp_paysupplierinvoicemaster');
                if ($results) {
                    $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
                    $this->db->delete('srp_erp_paysupplierinvoicedetail');
                    $this->session->set_flashdata('s', 'Deleted Successfully');
                    return true;
                }
            }else{
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID')));
                $this->db->update('srp_erp_paysupplierinvoicemaster', $data);
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            }

        }

    }

    function save_supplier_invoice_approval()
    {
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_id = trim($this->input->post('InvoiceAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        $comments = trim($this->input->post('comments'));

        $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'BSI');
        if ($approvals_status == 1) {
            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_supplier_invoices_data($system_id, 'BSI');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['InvoiceAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['bookingInvCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['bookingDate'];
                $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['invoiceType'];
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['bookingDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['bookingDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comments'];
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
                $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
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
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['taxMasterAutoID'] = $double_entry['gl_detail'][$i]['taxMasterAutoID'];
                $generalledger_arr[$i]['partyVatIdNo'] = $double_entry['gl_detail'][$i]['partyVatIdNo'];
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

            $this->db->select('sum(srp_erp_paysupplierinvoicedetail.transactionAmount) AS transactionAmount ,srp_erp_paysupplierinvoicedetail.companyLocalExchangeRate ,srp_erp_paysupplierinvoicedetail.companyReportingExchangeRate, srp_erp_paysupplierinvoicedetail.supplierCurrencyExchangeRate');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->where('srp_erp_paysupplierinvoicedetail.InvoiceAutoID', $system_id);
            $this->db->join('srp_erp_paysupplierinvoicemaster', 'srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paysupplierinvoicedetail.InvoiceAutoID');
            $transactionAmount = $this->db->get()->row_array();

            $company_loc = ($transactionAmount['transactionAmount'] / $transactionAmount['companyLocalExchangeRate']);
            $company_rpt = ($transactionAmount['transactionAmount'] / $transactionAmount['companyReportingExchangeRate']);
            $supplier_cr = ($transactionAmount['transactionAmount'] / $transactionAmount['supplierCurrencyExchangeRate']);

            // $data['approvedYN']             = $status;
            // $data['approvedbyEmpID']        = $this->common_data['current_userID'];
            // $data['approvedbyEmpName']      = $this->common_data['current_user'];
            // $data['approvedDate']           = $this->common_data['current_date'];
            // $data['companyLocalAmount']     = $company_loc;
            // $data['companyReportingAmount'] = $company_rpt;
            // $data['supplierCurrencyAmount'] = $supplier_cr;
            // $data['transactionAmount']      = $transactionAmount['transactionAmount'];

            // $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID')));
            // $this->db->update('srp_erp_paysupplierinvoicemaster', $data);

            $this->session->set_flashdata('s', 'Supplier Invoices Approved Successfully.');
        }

        // else{
        //     $this->session->set_flashdata('s', 'Supplier Invoices Approval : Level '.$level_id.' Successfully.');
        // }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function save_debitnote_header()
    {
        $this->db->trans_start();

        $date_format_policy = date_format_policy();
        $debitnoteDate = $this->input->post('dnDate');
        $dnDate = input_format_date($debitnoteDate, $date_format_policy);

        $currency_code = explode('|', trim($this->input->post('currency_code')));
        //$period = explode('|', trim($this->input->post('financeyear_period')));
        $supplierdetails = explode('|', trim($this->input->post('SupplierDetails')));
        $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear')));

        $FYBegin = input_format_date($financeyr[0], $date_format_policy);
        $FYEnd = input_format_date($financeyr[1], $date_format_policy);

        $supplier_arr = $this->fetch_supplier_data(trim($this->input->post('supplier')));
        $data['documentID'] = 'DN';
        $data['debitNoteDate'] = trim($dnDate);
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear'));
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear'));
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period'));
        /*$data['FYPeriodDateFrom'] = trim($period[0]);
        $data['FYPeriodDateTo'] = trim($period[1]);*/
        $data['supplierID'] = trim($this->input->post('supplier'));
        $data['supplierCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
        $data['supplierAddress'] = $supplier_arr['supplierAddress1'];
        $data['supplierTelephone'] = $supplier_arr['supplierTelephone'];
        $data['supplierFax'] = $supplier_arr['supplierFax'];
        $data['supplierliabilityAutoID'] = $supplier_arr['liabilityAutoID'];
        $data['supplierliabilitySystemGLCode'] = $supplier_arr['liabilitySystemGLCode'];
        $data['supplierliabilityGLAccount'] = $supplier_arr['liabilityGLAccount'];
        $data['supplierliabilityDescription'] = $supplier_arr['liabilityDescription'];
        $data['supplierliabilityType'] = $supplier_arr['liabilityType'];
        $data['docRefNo'] = trim($this->input->post('referenceno'));
        $data['comments'] = trim($this->input->post('comments'));
        // $data['transactionExchangeRate']            = trim($this->input->post('exchangerate'));
        // $data['transactionCurrency']                = trim($this->input->post('supplier_currency'));
        // $data['transactionCurrencyDecimalPlaces']   = fetch_currency_desimal($data['transactionCurrency']);
        // $data['companyLocalCurrency']               = $this->common_data['company_data']['company_default_currency'];
        // $default_currency      = currency_conversion($data['transactionCurrency'],$data['companyLocalCurrency']);
        // $data['companyLocalExchangeRate']           = $default_currency['conversion'];
        // $data['companyLocalCurrencyDecimalPlaces']  = $default_currency['DecimalPlaces'];

        // $data['companyReportingCurrency']           = $this->common_data['company_data']['company_reporting_currency'];
        // $reporting_currency    = currency_conversion($data['transactionCurrency'],$data['companyReportingCurrency']);
        // $data['companyReportingExchangeRate']       = $reporting_currency['conversion'];
        // $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

        // $data['supplierCurrency']                   = $supplier_arr['supplierCurrency'];
        // $supplierCurrency      = currency_conversion($data['transactionCurrency'],$data['supplierCurrency']);
        // $data['supplierCurrencyExchangeRate']       = $supplierCurrency['conversion'];
        // $data['supplierCurrencyDecimalPlaces']      = $supplierCurrency['DecimalPlaces'];
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

        if (trim($this->input->post('debitNoteMasterAutoID'))) {
            $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID')));
            $this->db->update('srp_erp_debitnotemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Debit Note Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Debit Note Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('debitNoteMasterAutoID'));
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
            //$data['debitNoteCode'] = $this->sequence->sequence_generator($data['documentID']);

            $this->db->insert('srp_erp_debitnotemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Debit Note Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Debit Note Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_segment()
    {
        $this->db->select('segmentCode,description,segmentID');
        $this->db->from('srp_erp_segment');
        $this->db->where('srp_erp_segment.companyCode', $this->common_data['company_data']['company_code']);
        $data = $this->db->get()->result_array();
        $data_arr = array('' => 'Select Segment');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['segmentID']) . '|' . trim($row['segmentCode'])] = trim($row['segmentCode']) . ' | ' . trim($row['description']);
            }
        }
        return $data_arr;
    }

    function load_debit_note_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(debitNoteDate,\'' . $convertFormat . '\') AS debitNoteDate ,DATE_FORMAT(FYPeriodDateFrom,"%Y-%m-%d") AS FYPeriodDateFrom,DATE_FORMAT(FYPeriodDateTo,"%Y-%m-%d") AS FYPeriodDateTo');
        $this->db->where('debitNoteMasterAutoID', $this->input->post('debitNoteMasterAutoID'));
        return $this->db->get('srp_erp_debitnotemaster')->row_array();
    }

    function delete_dn()
    {
        /*$this->db->select('debitNoteMasterAutoID,transactionAmount,invoiceAutoID');
        $this->db->from('srp_erp_debitnotedetail');
        $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID')));
        $detail_arr = $this->db->get()->result_array();
        $company_id = $this->common_data['company_data']['company_id'];
        foreach ($detail_arr as $val_as) {
            $match_id = $val_as['invoiceAutoID'];
            $number = $val_as['transactionAmount'];
            $this->db->query("UPDATE srp_erp_paysupplierinvoicemaster SET DebitNoteTotalAmount = (DebitNoteTotalAmount - {$number}) WHERE invoiceAutoID='{$match_id}' and companyID='{$company_id}'");
        }
        $this->db->delete('srp_erp_debitnotemaster', array('debitNoteMasterAutoID' => trim($this->input->post('debitNoteMasterAutoID'))));
        $this->db->delete('srp_erp_debitnotedetail', array('debitNoteMasterAutoID' => trim($this->input->post('debitNoteMasterAutoID'))));

        return array('error' => 0, 'message' => 'Deleted Successfully.');*/

        $this->db->select('*');
        $this->db->from('srp_erp_debitnotedetail');
        $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID')));
        $datas = $this->db->get()->row_array();
        if ($datas) {
            //$this->session->set_flashdata('e', 'Delete detail first.');
            return array('error' => 1, 'message' => 'please delete all detail records before delete this document.');
        } else {
            $data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID')));
            $this->db->update('srp_erp_debitnotemaster', $data);
            return array('error' => 0, 'message' => 'Deleted Successfully.');
        }


    }

    function fetch_dn_detail_table()
    {
        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces,companyLocalCurrency,companyLocalCurrencyDecimalPlaces,supplierCurrency,supplierCurrencyDecimalPlaces');
        $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID')));
        $this->db->from('srp_erp_debitnotemaster');
        $data['currency'] = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID')));
        $this->db->from('srp_erp_debitnotedetail');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function save_bsi_tax_detail()
    {
        $this->db->trans_start();
        $this->db->select('*');
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $master = $this->db->get('srp_erp_taxmaster')->row_array();

        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,companyLocalCurrency,companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces,companyReportingCurrency,companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,transactionCurrencyID,companyLocalCurrencyID,companyReportingCurrencyID');
        $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
        $inv_master = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

        $supplier_arr = fetch_supplier_data($master['supplierAutoID']);
        $data['invoiceAutoID'] = trim($this->input->post('InvoiceAutoID'));
        $data['taxMasterAutoID'] = $master['taxMasterAutoID'];
        $data['taxDescription'] = $master['taxDescription'];
        $data['taxShortCode'] = $master['taxShortCode'];
        $data['supplierAutoID'] = $master['supplierAutoID'];
        $data['supplierSystemCode'] = $master['supplierSystemCode'];
        $data['supplierName'] = $master['supplierName'];
        $data['supplierCurrencyID'] = $master['supplierCurrencyID'];
        $data['supplierCurrency'] = $master['supplierCurrency'];
        $data['supplierCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
        $data['GLAutoID'] = $master['supplierGLAutoID'];
        $data['systemGLCode'] = $master['supplierGLSystemGLCode'];
        $data['GLCode'] = $master['supplierGLAccount'];
        $data['GLDescription'] = $master['supplierGLDescription'];
        $data['GLType'] = $master['supplierGLType'];
        $data['taxPercentage'] = trim($this->input->post('percentage'));
        $data['transactionAmount'] = trim($this->input->post('amount'));
        $data['transactionCurrencyID'] = $inv_master['transactionCurrencyID'];
        $data['transactionCurrency'] = $inv_master['transactionCurrency'];
        $data['transactionExchangeRate'] = $inv_master['transactionExchangeRate'];
        $data['transactionCurrencyDecimalPlaces'] = $inv_master['transactionCurrencyDecimalPlaces'];
        $data['companyLocalCurrencyID'] = $inv_master['companyLocalCurrencyID'];
        $data['companyLocalCurrency'] = $inv_master['companyLocalCurrency'];
        $data['companyLocalExchangeRate'] = $inv_master['companyLocalExchangeRate'];
        $data['companyReportingCurrencyID'] = $inv_master['companyReportingCurrencyID'];
        $data['companyReportingCurrency'] = $inv_master['companyReportingCurrency'];
        $data['companyReportingExchangeRate'] = $inv_master['companyReportingExchangeRate'];

        $supplierCurrency = currency_conversion($data['transactionCurrency'], $data['supplierCurrency']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('taxDetailAutoID'))) {
            $this->db->where('taxDetailAutoID', trim($this->input->post('taxDetailAutoID')));
            $this->db->update('srp_erp_paysupplierinvoicetaxdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Tax Detail : ' . $data['GLDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Tax Detail : ' . $data['GLDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('taxDetailAutoID'));
            }
        } else {
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_paysupplierinvoicetaxdetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Tax Detail : ' . $data['GLDescription'] . '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Tax Detail : ' . $data['GLDescription'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_dn_detail()
    {
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate');
        $this->db->where('debitNoteMasterAutoID', $this->input->post('debitNoteMasterAutoID'));
        $master = $this->db->get('srp_erp_debitnotemaster')->row_array();
        $segment = explode('|', trim($this->input->post('segment_gl')));
        $gl_code = explode('|', trim($this->input->post('gl_code_des')));
        $data['debitNoteMasterAutoID'] = trim($this->input->post('debitNoteMasterAutoID'));
        $data['GLAutoID'] = trim($this->input->post('gl_code'));
        $data['systemGLCode'] = trim($gl_code[0]);
        $data['GLCode'] = trim($gl_code[1]);
        $data['GLDescription'] = trim($gl_code[2]);
        $data['GLType'] = trim($gl_code[3]);
        $data['segmentID'] = trim($segment[0]);
        $data['segmentCode'] = trim($segment[1]);
        $data['transactionAmount'] = trim($this->input->post('amount'));
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $master['companyLocalExchangeRate']);
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $master['companyReportingExchangeRate']);
        $data['supplierAmount'] = ($data['transactionAmount'] / $master['supplierCurrencyExchangeRate']);
        $data['description'] = trim($this->input->post('description'));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('debitNoteDetailsID'))) {
            $this->db->where('debitNoteDetailsID', trim($this->input->post('debitNoteDetailsID')));
            $this->db->update('srp_erp_debitnotedetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Debit Note Detail : ' . $data['GLDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Debit Note Detail : ' . $data['GLDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('debitNoteDetailsID'));
            }
        } else {
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_debitnotedetail', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Debit Note Detail : ' . $data['GLDescription'] . '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Debit Note Detail : ' . $data['GLDescription'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_dn_detail()
    {
        $this->db->select('*');
        $this->db->where('debitNoteDetailsID', $this->input->post('debitNoteDetailsID'));
        return $this->db->get('srp_erp_debitnotedetail')->row_array();
    }

    function delete_dn_detail()
    {
        $this->db->select('InvoiceAutoID,transactionAmount');
        $this->db->from('srp_erp_debitnotedetail');
        $this->db->where('debitNoteDetailsID', trim($this->input->post('debitNoteDetailsID')));
        $detail_arr = $this->db->get()->row_array();
        $company_id = $this->common_data['company_data']['company_id'];
        $match_id = $detail_arr['InvoiceAutoID'];
        $number = $detail_arr['transactionAmount'];
        $status = 0;
        $this->db->query("UPDATE srp_erp_paysupplierinvoicemaster SET DebitNoteTotalAmount = (DebitNoteTotalAmount -{$number}) WHERE InvoiceAutoID='{$match_id}' and companyID='{$company_id}'");
        $this->db->delete('srp_erp_debitnotedetail', array('debitNoteDetailsID' => trim($this->input->post('debitNoteDetailsID'))));
        $this->session->set_flashdata('s', 'Debit Note Detail Deleted Successfully');
        return true;
    }

    function dn_confirmation()
    {
        $this->db->select('debitNoteMasterAutoID');
        $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID')));
        $this->db->from('srp_erp_debitnotedetail');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('w', 'There are no records to confirm this document!');
        }
        else
        {
        $this->db->select('debitNoteMasterAutoID');
        $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID')));
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_debitnotemaster');
        $Confirmed = $this->db->get()->row_array();
        if (!empty($Confirmed)) {
            return array('w', 'Document already confirmed');
        } else {

            $system_id = trim($this->input->post('debitNoteMasterAutoID'));

            $this->db->select('documentID, debitNoteCode,DATE_FORMAT(debitNoteDate, "%Y") as invYear,DATE_FORMAT(debitNoteDate, "%m") as invMonth,companyFinanceYearID');
            $this->db->where('debitNoteMasterAutoID', $system_id);
            $this->db->from('srp_erp_debitnotemaster');
            $master_dt = $this->db->get()->row_array();
            $this->load->library('sequence');
            if($master_dt['debitNoteCode'] == "0") {
                $pvCd = array(
                    'debitNoteCode' => $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth'])
                );
                $this->db->where('debitNoteMasterAutoID', $system_id);
                $this->db->update('srp_erp_debitnotemaster', $pvCd);
            }
            $this->load->library('approvals');
            $this->db->select('debitNoteMasterAutoID, debitNoteCode');
            $this->db->where('debitNoteMasterAutoID', $system_id);
            $this->db->from('srp_erp_debitnotemaster');
            $grv_data = $this->db->get()->row_array();
            $approvals_status = $this->approvals->CreateApproval('DN', $grv_data['debitNoteMasterAutoID'], $grv_data['debitNoteCode'], 'Debit note', 'srp_erp_debitnotemaster', 'debitNoteMasterAutoID');
            if ($approvals_status==1) {

                $data = array(
                    'confirmedYN' => 1,
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                    'confirmedByName' => $this->common_data['current_user']
                );

                $this->db->where('debitNoteMasterAutoID', $system_id);
                $result = $this->db->update('srp_erp_debitnotemaster', $data);
                if ($result) {
                    return array('s', 'Document confirmed Successfully');
                }
            }else if($approvals_status==3){
                return array('w', 'There are no users exist to perform approval for this document.');
            } else {
                return array('e', 'Document confirmation failed');
            }

        }
        }

    }

    function fetch_supplier_invoice()
    {
        $this->db->select('debitNoteDate,debitNoteMasterAutoID,supplierID,transactionCurrency, transactionCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID')));
        $this->db->from('srp_erp_debitnotemaster');
        $data['master'] = $this->db->get()->row_array();

        /*$this->db->select('InvoiceAutoID,bookingInvCode,bookingDate,paymentTotalAmount,transactionCurrency,DebitNoteTotalAmount, transactionAmount, InvoiceAutoID');
        $this->db->where('bookingDate <=', $data['master']['debitNoteDate']);
        $this->db->where('supplierID', $data['master']['supplierID']);
        $this->db->where('transactionCurrency', $data['master']['transactionCurrency']);
        $this->db->where('paymentInvoiceYN', 0);
        $this->db->where('approvedYN', 1);
        $this->db->from('srp_erp_paysupplierinvoicemaster');*/

        $output = $this->db->query("SELECT srp_erp_paysupplierinvoicemaster.InvoiceAutoID,bookingInvCode,paymentTotalAmount,DebitNoteTotalAmount,advanceMatchedTotal,RefNo,((sid.transactionAmount * (100+IFNULL(tax.taxPercentage,0))) / 100 ) as transactionAmount,bookingDate,transactionCurrency FROM srp_erp_paysupplierinvoicemaster LEFT JOIN (SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount FROM srp_erp_paysupplierinvoicedetail GROUP BY invoiceAutoID) sid ON srp_erp_paysupplierinvoicemaster.invoiceAutoID = sid.invoiceAutoID
 LEFT JOIN (SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY invoiceAutoID) tax ON tax.invoiceAutoID = srp_erp_paysupplierinvoicemaster.invoiceAutoID WHERE confirmedYN = 1 AND approvedYN = 1 AND paymentInvoiceYN = 0 AND `supplierID` = '{$data['master']['supplierID']}' AND `transactionCurrencyID` = '{$data['master']['transactionCurrencyID']}' AND `bookingDate` <= '{$data['master']['debitNoteDate']}'")->result_array();

        $data['detail'] = $output;
        return $data;
    }

    function save_debit_base_items()
    {
        $this->db->trans_start();
        $projectExist = project_is_exist();
        $debitNoteMasterAutoID = trim($this->input->post('debitNoteMasterAutoID'));
        // $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate');
        // $this->db->where('debitNoteMasterAutoID', $debitNoteMasterAutoID);
        // $master = $this->db->get('srp_erp_debitnotemaster')->row_array();

        $invoice_id = $this->input->post('InvoiceAutoID');
        $segments = $this->input->post('segment');
        $gl_code_d = $this->input->post('gl_code_dec');
        $amounts = $this->input->post('amounts');
        $gl_codes = $this->input->post('gl_code');
        $code = $this->input->post('bookingInvCode');
        $project = $this->input->post('project');
        for ($i = 0; $i < count($invoice_id); $i++) {
            $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate,transactionCurrencyID');
            $this->db->where('InvoiceAutoID', $invoice_id[$i]);
            $master = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();
            $segment = explode('|', $segments[$i]);
            $gl_code_des = explode('|', $gl_code_d[$i]);
            $data[$i]['debitNoteMasterAutoID'] = $debitNoteMasterAutoID;
            $data[$i]['InvoiceAutoID'] = $invoice_id[$i];
            $data[$i]['bookingInvCode'] = $code[$i];
            $data[$i]['projectID'] = $project[$i];
            if($projectExist == 1){
                $projectCurrency = project_currency($project[$i]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'],$projectCurrency);
                $data[$i]['projectID'] = $project[$i];
                $data[$i]['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data[$i]['GLAutoID'] = $gl_codes[$i];
            $data[$i]['systemGLCode'] = trim($gl_code_des[0]);
            $data[$i]['GLCode'] = trim($gl_code_des[1]);
            $data[$i]['GLDescription'] = trim($gl_code_des[2]);
            $data[$i]['GLType'] = trim($gl_code_des[3]);
            $data[$i]['segmentID'] = trim($segment[0]);
            $data[$i]['segmentCode'] = trim($segment[1]);
            $data[$i]['transactionAmount'] = $amounts[$i];
            $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master['companyLocalExchangeRate']);
            $data[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master['companyReportingExchangeRate']);
            $data[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data[$i]['supplierAmount'] = ($data[$i]['transactionAmount'] / $master['supplierCurrencyExchangeRate']);
            $data[$i]['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $data[$i]['description'] = trim($this->input->post('description'));
            $data[$i]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$i]['modifiedUserID'] = $this->common_data['current_userID'];
            $data[$i]['modifiedUserName'] = $this->common_data['current_user'];
            $data[$i]['modifiedDateTime'] = $this->common_data['current_date'];
            $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$i]['createdPCID'] = $this->common_data['current_pc'];
            $data[$i]['createdUserID'] = $this->common_data['current_userID'];
            $data[$i]['createdUserName'] = $this->common_data['current_user'];
            $data[$i]['createdDateTime'] = $this->common_data['current_date'];

            $id = $data[$i]['InvoiceAutoID'];
            $amo = $data[$i]['transactionAmount'];
            $this->db->query("UPDATE srp_erp_paysupplierinvoicemaster SET DebitNoteTotalAmount = (DebitNoteTotalAmount+{$amo}) WHERE InvoiceAutoID='{$id}'");
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_debitnotedetail', $data);
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

    function save_dn_approval()
    {
        $this->load->library('approvals');
        $system_id = trim($this->input->post('debitNoteMasterAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        $comments = trim($this->input->post('comments'));

        $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'DN');
        if ($approvals_status == 1) {
            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_debit_note_data($system_id, 'DN');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['debitNoteMasterAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['debitNoteCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['debitNoteDate'];
                $generalledger_arr[$i]['documentType'] = '';
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['debitNoteDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['debitNoteDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comments'];
                $generalledger_arr[$i]['chequeNumber'] = '';
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['gl_detail'][$i]['transactionExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['gl_detail'][$i]['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['gl_detail'][$i]['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                $generalledger_arr[$i]['partyType'] = 'SUP';
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['master_data']['supplierID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['master_data']['supplierCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['master_data']['supplierName'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['master_data']['supplierCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['master_data']['supplierCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
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
                // To get actual amount from debit note detail table
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
                $this->db->select('sum(transactionAmount) as transaction_total, sum(companyLocalAmount) as companyLocal_total, sum(companyReportingAmount) as companyReporting_total, sum(partyCurrencyAmount) as party_total');
                $this->db->where('documentCode', 'DN');
                $this->db->where('documentMasterAutoID', $system_id);
                $totals = $this->db->get('srp_erp_generalledger')->row_array();
                if ($totals['transaction_total'] != 0 or $totals['companyLocal_total'] != 0 or $totals['companyReporting_total'] != 0 or $totals['party_total'] != 0) {
                    $generalledger_arr = array();
                    $ERGL_ID = $this->common_data['controlaccounts']['ERGL'];
                    //echo 'xx<hr/>';
                    $ERGL = fetch_gl_account_desc($ERGL_ID);
                    //print_r($ERGL);
                    $generalledger_arr['documentMasterAutoID'] = $double_entry['master_data']['debitNoteMasterAutoID'];
                    $generalledger_arr['documentCode'] = $double_entry['code'];
                    $generalledger_arr['documentSystemCode'] = $double_entry['master_data']['debitNoteCode'];
                    $generalledger_arr['documentDate'] = $double_entry['master_data']['debitNoteDate'];
                    $generalledger_arr['documentType'] = '';
                    $generalledger_arr['documentYear'] = $double_entry['master_data']['debitNoteDate'];
                    $generalledger_arr['documentMonth'] = date("m", strtotime($double_entry['master_data']['debitNoteDate']));
                    $generalledger_arr['documentNarration'] = $double_entry['master_data']['comments'];
                    $generalledger_arr['chequeNumber'] = '';
                    $generalledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $generalledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $generalledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                    $generalledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr['partyContractID'] = '';
                    $generalledger_arr['partyType'] = 'SUP';
                    $generalledger_arr['partyAutoID'] = $double_entry['master_data']['supplierID'];
                    $generalledger_arr['partySystemCode'] = $double_entry['master_data']['supplierCode'];
                    $generalledger_arr['partyName'] = $double_entry['master_data']['supplierName'];
                    $generalledger_arr['partyCurrencyID'] = $double_entry['master_data']['supplierCurrencyID'];
                    $generalledger_arr['partyCurrency'] = $double_entry['master_data']['supplierCurrency'];
                    $generalledger_arr['partyExchangeRate'] = $double_entry['master_data']['supplierCurrencyExchangeRate'];
                    $generalledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['supplierCurrencyDecimalPlaces'];
                    $generalledger_arr['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $generalledger_arr['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $generalledger_arr['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $generalledger_arr['approvedDate'] = $double_entry['master_data']['approvedDate'];
                    $generalledger_arr['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                    $generalledger_arr['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                    $generalledger_arr['companyID'] = $double_entry['master_data']['companyID'];
                    $generalledger_arr['companyCode'] = $double_entry['master_data']['companyCode'];
                    $generalledger_arr['transactionAmount'] = round(($totals['transaction_total'] * -1), $generalledger_arr['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr['companyLocalAmount'] = round(($totals['companyLocal_total'] * -1), $generalledger_arr['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr['companyReportingAmount'] = round(($totals['companyReporting_total'] * -1), $generalledger_arr['companyReportingCurrencyDecimalPlaces']);
                    $generalledger_arr['partyCurrencyAmount'] = round(($totals['party_total'] * -1), $generalledger_arr['partyCurrencyDecimalPlaces']);
                    $generalledger_arr['amount_type'] = null;
                    $generalledger_arr['documentDetailAutoID'] = 0;
                    $generalledger_arr['GLAutoID'] = $ERGL_ID;
                    $generalledger_arr['systemGLCode'] = $ERGL['systemAccountCode'];
                    $generalledger_arr['GLCode'] = $ERGL['GLSecondaryCode'];
                    $generalledger_arr['GLDescription'] = $ERGL['GLDescription'];
                    $generalledger_arr['GLType'] = $ERGL['subCategory'];
                    $seg = explode('|', $this->common_data['company_data']['default_segment']);
                    $generalledger_arr['segmentID'] = $seg[0];
                    $generalledger_arr['segmentCode'] = $seg[1];
                    $generalledger_arr['subLedgerType'] = 0;
                    $generalledger_arr['subLedgerDesc'] = null;
                    $generalledger_arr['isAddon'] = 0;
                    $generalledger_arr['createdUserGroup'] = $this->common_data['user_group'];
                    $generalledger_arr['createdPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr['createdUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr['createdDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr['createdUserName'] = $this->common_data['current_user'];
                    $generalledger_arr['modifiedPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr['modifiedUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr['modifiedDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr['modifiedUserName'] = $this->common_data['current_user'];
                    //print_r($generalledger_arr);
                    $this->db->insert('srp_erp_generalledger', $generalledger_arr);
                }
            }
            $this->session->set_flashdata('s', 'Debit Note Approval Successfully.');
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

    function delete_supplierInvoices_attachement()
    {

        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments");
        $link = "$url/$myFileName";

        if (!unlink(UPLOAD_PATH . $link)) {
            echo json_encode(false);
        } else {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        }
    }

    function delete_debitNote_attachement()
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

    function delete_paymentVoucher_attachement()
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


    function fetch_customer_currency_by_id()
    {
        $this->db->select('customerCurrencyID,customerCreditPeriod');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', trim($this->input->post('customerAutoID')));
        return $this->db->get()->row_array();
    }

    function save_debitNote_detail_GLCode_multiple()
    {

        $this->db->trans_start();
        $projectExist = project_is_exist();
        $this->db->select('*');
        $this->db->where('debitNoteMasterAutoID', $this->input->post('debitNoteMasterAutoID'));
        $master = $this->db->get('srp_erp_debitnotemaster')->row_array();

        $gl_codes = $this->input->post('gl_code_array');
        $gl_code_des = $this->input->post('gl_code_des');
        $projectID = $this->input->post('projectID');
        $amount = $this->input->post('amount');
        $descriptions = $this->input->post('description');
        $segment_gls = $this->input->post('segment_gl');

        foreach ($gl_codes as $key => $gl_code) {
            $segment = explode('|', $segment_gls[$key]);
            $gl_code = explode('|', $gl_code_des[$key]);

            $data[$key]['debitNoteMasterAutoID'] = trim($this->input->post('debitNoteMasterAutoID'));
            $data[$key]['GLAutoID'] = $gl_codes[$key];
            if($projectExist == 1){
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'],$projectCurrency);
                $data[$key]['projectID'] = $projectID[$key];
                $data[$key]['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data[$key]['systemGLCode'] = trim($gl_code[0]);
            $data[$key]['GLCode'] = trim($gl_code[1]);
            $data[$key]['GLDescription'] = trim($gl_code[2]);
            $data[$key]['GLType'] = trim($gl_code[3]);
            $data[$key]['segmentID'] = trim($segment[0]);
            $data[$key]['segmentCode'] = trim($segment[1]);
            $data[$key]['description'] = $descriptions[$key];
            $data[$key]['transactionAmount'] = round($amount[$key], $master['transactionCurrencyDecimalPlaces']);
            $companyLocalAmount = $data[$key]['transactionAmount'] / $master['companyLocalExchangeRate'];
            $data[$key]['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $data[$key]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $companyReportingAmount = $data[$key]['transactionAmount'] / $master['companyReportingExchangeRate'];
            $data[$key]['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $data[$key]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $supplierAmount = $data[$key]['transactionAmount'] / $master['supplierCurrencyExchangeRate'];
            $data[$key]['supplierAmount'] = round($supplierAmount, $master['supplierCurrencyDecimalPlaces']);
            $data[$key]['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $data[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdUserName'] = $this->common_data['current_user'];
            $data[$key]['createdDateTime'] = $this->common_data['current_date'];
            $data[$key]['isFromInvoice'] = 0;
        }
        $this->db->insert_batch('srp_erp_debitnotedetail', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            //$this->session->set_flashdata('e', 'Supplier Invoice Detail : Saved Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('e', 'Debit Note Detail : Saved Failed ');
        } else {
            //$this->session->set_flashdata('s', 'Supplier Invoice Detail : Saved Successfully.');
            $this->db->trans_commit();
            return array('s', 'Debit Note Detail : Saved Successfully.');
        }

    }


    function re_open_supplier_invoice()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID')));
        $this->db->update('srp_erp_paysupplierinvoicemaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function re_open_dn()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID')));
        $this->db->update('srp_erp_debitnotemaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }
    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'PO');
        $this->db->from('srp_erp_documentcodemaster ');
        return $this->db->get()->row_array();

    }
    function fetch_signaturelevel_debit_note()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'DN');
        $this->db->from('srp_erp_documentcodemaster ');
        return $this->db->get()->row_array();

    }
}