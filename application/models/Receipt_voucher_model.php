<?php

class Receipt_voucher_model extends ERP_Model
{

    function save_receiptvoucher_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $RVdates = $this->input->post('RVdate');
        $RVdate = input_format_date($RVdates, $date_format_policy);
        $RVcheqDate = $this->input->post('RVchequeDate');
        $RVchequeDate = input_format_date($RVcheqDate, $date_format_policy);
        //$period = explode('|', trim($this->input->post('financeyear_period')));
        $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear')));
        $FYBegin = input_format_date($financeyr[0], $date_format_policy);
        $FYEnd = input_format_date($financeyr[1], $date_format_policy);
        $segment = explode('|', trim($this->input->post('segment')));
        $bank = explode('|', trim($this->input->post('bank')));
        $currency_code = explode('|', trim($this->input->post('currency_code')));
        $bank_detail = fetch_gl_account_desc(trim($this->input->post('RVbankCode')));
        $data['documentID'] = 'RV';
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear'));
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear'));
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period'));
        /*$data['FYPeriodDateFrom'] = trim($period[0]);
        $data['FYPeriodDateTo'] = trim($period[1]);*/
        $data['RVdate'] = trim($RVdate);
        $data['RVNarration'] = trim_desc($this->input->post('RVNarration'));
        $data['segmentID'] = trim($segment[0]);
        $data['segmentCode'] = trim($segment[1]);
        $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
        $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
        $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
        $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
        $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
        $data['RVbank'] = $bank_detail['bankName'];
        $data['RVbankBranch'] = $bank_detail['bankBranch'];
        $data['RVbankSwiftCode'] = $bank_detail['bankSwiftCode'];
        $data['RVbankAccount'] = $bank_detail['bankAccountNumber'];
        $data['RVbankType'] = $bank_detail['subCategory'];
        $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);
        $data['RVchequeNo'] = trim($this->input->post('RVchequeNo'));
        if ($bank_detail['isCash'] == 0) {
            $data['RVchequeDate'] = trim($RVchequeDate);
        } else {
            $data['RVchequeDate'] = null;
        }
        $data['RvType'] = trim($this->input->post('vouchertype'));
        $data['referanceNo'] = trim_desc($this->input->post('referenceno'));
        $data['RVbankCode'] = trim($this->input->post('RVbankCode'));

        if ($data['RvType'] == 'Direct') {
            $data['customerName'] = trim($this->input->post('customer_name'));
            $data['customerAddress'] = '';
            $data['customerTelephone'] = '';
            $data['customerFax'] = '';
            $data['customerEmail'] = '';
            $data['customerCurrency'] = trim($currency_code[0]);
            $data['customerCurrencyID'] = trim($this->input->post('transactionCurrencyID'));
            $data['customerCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['customerCurrencyID']);
        } else {
            $customer_arr = $this->fetch_customer_data(trim($this->input->post('customerID')));
            $data['customerID'] = $customer_arr['customerAutoID'];
            $data['customerSystemCode'] = $customer_arr['customerSystemCode'];
            $data['customerName'] = $customer_arr['customerName'];
            $data['customerAddress'] = $customer_arr['customerAddress1'] . ' ' . $customer_arr['customerAddress2'];
            $data['customerTelephone'] = $customer_arr['customerTelephone'];
            $data['customerFax'] = $customer_arr['customerFax'];
            $data['customerEmail'] = $customer_arr['customerEmail'];
            $data['customerreceivableAutoID'] = $customer_arr['receivableAutoID'];
            $data['customerreceivableSystemGLCode'] = $customer_arr['receivableSystemGLCode'];
            $data['customerreceivableGLAccount'] = $customer_arr['receivableGLAccount'];
            $data['customerreceivableDescription'] = $customer_arr['receivableDescription'];
            $data['customerreceivableType'] = $customer_arr['receivableType'];
            $data['customerCurrency'] = $customer_arr['customerCurrency'];
            $data['customerCurrencyID'] = $customer_arr['customerCurrencyID'];
            $data['customerCurrencyDecimalPlaces'] = $customer_arr['customerCurrencyDecimalPlaces'];
        }
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
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
        $customer_currency = currency_conversionID($data['transactionCurrencyID'], $data['customerCurrencyID']);
        $data['customerExchangeRate'] = $customer_currency['conversion'];
        $data['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
        $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
        $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
        $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
        if (trim($this->input->post('receiptVoucherAutoId'))) {
            $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId')));
            $this->db->update('srp_erp_customerreceiptmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Receipt Voucher Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Receipt Voucher Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('receiptVoucherAutoId'));
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
            $data['RVcode'] = 0;

            $this->db->insert('srp_erp_customerreceiptmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Receipt Voucher   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Receipt Voucher Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_customer_data($customerID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $customerID);
        return $this->db->get()->row_array();
    }

    function save_receipt_match_header()
    {
        $date_format_policy = date_format_policy();
        $matDate = $this->input->post('matchDate');
        $matchDate = input_format_date($matDate, $date_format_policy);

        $this->db->trans_start();
        $customer_arr = $this->fetch_customer_data(trim($this->input->post('customerID')));
        $currency_code = explode('|', trim($this->input->post('currency_code')));
        $data['documentID'] = 'RVM';
        $data['matchDate'] = trim($matchDate);
        $data['Narration'] = trim($this->input->post('Narration'));
        $data['refNo'] = trim($this->input->post('refNo'));
        $data['customerID'] = $customer_arr['customerAutoID'];
        $data['customerSystemCode'] = $customer_arr['customerSystemCode'];
        $data['customerName'] = $customer_arr['customerName'];
        $data['customerCurrency'] = $customer_arr['customerCurrency'];
        $data['customerCurrencyID'] = $customer_arr['customerCurrencyID'];
        $data['customerCurrencyDecimalPlaces'] = $customer_arr['customerCurrencyDecimalPlaces'];

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
        $customer_currency = currency_conversionID($data['transactionCurrencyID'], $data['customerCurrencyID']);
        $data['customerCurrencyExchangeRate'] = $customer_currency['conversion'];
        $data['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('matchID'))) {
            $this->db->where('matchID', trim($this->input->post('matchID')));
            $this->db->update('srp_erp_rvadvancematch', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Receipt Matching Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Receipt Matching Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('matchID'));
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
            $data['matchSystemCode'] = $this->sequence->sequence_generator($data['documentID']);

            $this->db->insert('srp_erp_rvadvancematch', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Receipt Matching Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Receipt Matching Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function load_receipt_match_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(matchDate,\'' . $convertFormat . '\') AS matchDate');
        $this->db->from('srp_erp_rvadvancematch');
        $this->db->where('matchID', $this->input->post('matchID'));
        return $this->db->get()->row_array();
    }

    function fetch_match_detail()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(RVdate,\'' . $convertFormat . '\') AS RVdate,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate');
        $this->db->where('matchID', $this->input->post('matchID'));
        return $this->db->get('srp_erp_rvadvancematchdetails')->result_array();
    }

    function save_inv_tax_detail()
    {
        $this->db->select('taxMasterAutoID');
        $this->db->where('receiptVoucherAutoId', $this->input->post('receiptVoucherAutoId'));
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $tax_detail = $this->db->get('srp_erp_customerreceipttaxdetails')->row_array();
        if (!empty($tax_detail)) {
            return array('status' => 1, 'type' => 'w', 'data' => ' Tax Detail added already ! ');
        }

        $this->db->trans_start();
        $this->db->select('*');
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $this->db->from('srp_erp_taxmaster');
        $master = $this->db->get()->row_array();

        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('receiptVoucherAutoId', $this->input->post('receiptVoucherAutoId'));
        $inv_master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();

        $data['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId'));
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
            $this->db->update('srp_erp_customerreceipttaxdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === 0) {
                $this->db->trans_rollback();
                return array('status' => 0, 'type' => 'e', 'data' => 'Tax Detail : ' . $data['GLDescription'] . ' Update Failed ');
            } else {
                $this->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'data' => 'Tax Detail : ' . $data['GLDescription'] . ' Updated Successfully.', 'last_id' => $this->input->post('taxDetailAutoID'));
            }
        } else {
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_customerreceipttaxdetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => 0, 'type' => 'e', 'data' => 'Tax Detail : ' . $data['GLDescription'] . ' Save Failed ');
            } else {
                $this->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'data' => 'Tax Detail : ' . $data['GLDescription'] . ' Saved Successfully.', 'last_id' => $last_id);
            }
        }
    }

    function fetch_rv_advance_detail()
    {
        $data = array();
        $convertFormat = convert_date_format_sql();
        $this->db->select('customerID,transactionCurrency,DATE_FORMAT(matchDate,"%Y-%m-%d") AS matchDate');
        $this->db->where('matchID', $this->input->post('matchID'));
        $master_arr = $this->db->get('srp_erp_rvadvancematch')->row_array();

        $this->db->select('srp_erp_customerreceiptdetail.transactionAmount ,DATE_FORMAT(srp_erp_customerreceiptmaster.RVdate,\'' . $convertFormat . '\') AS RVdate , srp_erp_customerreceiptmaster.RVcode,sum(srp_erp_rvadvancematchdetails.transactionAmount) as paid,srp_erp_customerreceiptdetail.receiptVoucherDetailAutoID');
        $this->db->from('srp_erp_customerreceiptmaster');
        $this->db->where('customerID', $master_arr['customerID']);
        //$this->db->where('srp_erp_customerreceiptdetail.transactionCurrency', $master_arr['transactionCurrency']);
        $this->db->where('srp_erp_customerreceiptdetail.companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('type', 'Advance');
        $this->db->group_by("receiptVoucherDetailAutoID");
        $this->db->where('srp_erp_customerreceiptmaster.approvedYN', 1);
        $this->db->join('srp_erp_customerreceiptdetail', 'srp_erp_customerreceiptdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId');
        $this->db->join('srp_erp_rvadvancematchdetails', 'srp_erp_rvadvancematchdetails.receiptVoucherDetailAutoID = srp_erp_customerreceiptdetail.receiptVoucherDetailAutoID', 'Left');
        $data['receipt'] = $this->db->get()->result_array();

        $this->db->select('invoiceAutoID,invoiceCode,invoiceDate,transactionAmount,receiptTotalAmount ,creditNoteTotalAmount,advanceMatchedTotal');
        $this->db->from('srp_erp_customerinvoicemaster');
        $this->db->where('receiptInvoiceYN', 0);
        $this->db->where('approvedYN', 1);
        $this->db->where('customerID', $master_arr['customerID']);
        $this->db->where('transactionCurrency', $master_arr['transactionCurrency']);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data['invoice'] = $this->db->get()->result_array();
        return $data;
    }

    function save_match_amount()
    {
        $this->db->trans_start();
        $receiptVoucherDetailAutoID = $this->input->post('receiptVoucherDetailAutoID');
        $invoice_id = $this->input->post('invoiceAutoID');
        $amounts = $this->input->post('amounts');
        $matchID = $this->input->post('matchID');
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate');
        $this->db->where('matchID', $matchID);
        $master = $this->db->get('srp_erp_rvadvancematch')->row_array();

        $this->db->select('srp_erp_customerreceiptmaster.receiptVoucherAutoId,srp_erp_customerreceiptdetail.transactionAmount,srp_erp_customerreceiptmaster.RVdate,srp_erp_customerreceiptmaster.RVcode,srp_erp_customerreceiptdetail.receiptVoucherDetailAutoID');
        $this->db->group_by("receiptVoucherDetailAutoID");
        $this->db->from('srp_erp_customerreceiptmaster');
        $this->db->join('srp_erp_customerreceiptdetail', 'srp_erp_customerreceiptdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId');
        $this->db->where_in('receiptVoucherDetailAutoID', $receiptVoucherDetailAutoID);
        $detail_arr = $this->db->get()->result_array();

        for ($i = 0; $i < count($detail_arr); $i++) {
            $invoice_data = $this->fetch_invoice($invoice_id[$i]);
            $data[$i]['matchID'] = $matchID;
            $data[$i]['receiptVoucherAutoId'] = $detail_arr[$i]['receiptVoucherAutoId'];
            $data[$i]['receiptVoucherDetailAutoID'] = $detail_arr[$i]['receiptVoucherDetailAutoID'];
            $data[$i]['RVcode'] = $detail_arr[$i]['RVcode'];
            $data[$i]['RVdate'] = $detail_arr[$i]['RVdate'];
            $data[$i]['invoiceAutoID'] = trim($invoice_data['invoiceAutoID']);
            $data[$i]['invoiceCode'] = trim($invoice_data['invoiceCode']);
            $data[$i]['invoiceDate'] = trim($invoice_data['invoiceDate']);
            $data[$i]['transactionAmount'] = $amounts[$i];
            $data[$i]['transactionExchangeRate'] = 1;
            $data[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data[$i]['customerCurrencyExchangeRate'] = $master['customerCurrencyExchangeRate'];
            $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master['companyLocalExchangeRate']);
            $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master['companyReportingExchangeRate']);
            $data[$i]['customerCurrencyAmount'] = ($data[$i]['transactionAmount'] / $master['customerCurrencyExchangeRate']);
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

            $id = $data[$i]['invoiceAutoID'];
            $amo = $data[$i]['transactionAmount'];
            $this->db->query("UPDATE srp_erp_customerinvoicemaster SET advanceMatchedTotal = (advanceMatchedTotal+$amo) WHERE invoiceAutoID='$id'");
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_rvadvancematchdetails', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('status' => 0, 'type' => 'e', 'messsage' => 'Records Inserted error');
        } else {
            $this->db->trans_commit();
            return array('status' => 1, 'type' => 's', 'messsage' => 'Records Inserted successfully');
        }
    }

    function fetch_invoice($id)
    {
        $this->db->select('invoiceAutoID,invoiceCode,invoiceDate,transactionAmount');
        $this->db->from('srp_erp_customerinvoicemaster');
        $this->db->where('invoiceAutoID', $id);
        //,receiptTotalAmount ,creditNoteTotalAmount,advanceMatchedTotal
        return $this->db->get()->row_array();
    }

    function delete_rv_match()
    {
        /*$this->db->select('invoiceAutoID,transactionAmount');
        $this->db->where('matchID', $this->input->post('matchID'));
        $data = $this->db->get('srp_erp_rvadvancematchdetails')->result_array();
        for ($i = 0; $i < count($data); $i++) {
            $id = $data[$i]['invoiceAutoID'];
            $amo = $data[$i]['transactionAmount'];
            $this->db->query("UPDATE srp_erp_customerinvoicemaster SET advanceMatchedTotal = (advanceMatchedTotal-{$amo}) and receiptInvoiceYN = 0 WHERE invoiceAutoID='{$id}'");
        }

        $this->db->where('matchID', $this->input->post('matchID'));
        $results = $this->db->delete('srp_erp_rvadvancematch');
        $this->db->where('matchID', $this->input->post('matchID'));
        $results = $this->db->delete('srp_erp_rvadvancematchdetails');
        $this->session->set_flashdata('s', 'Receipt Matching Deleted Successfully');
        return true;*/
        $this->db->select('*');
        $this->db->from('srp_erp_rvadvancematchdetails');
        $this->db->where('matchID', trim($this->input->post('matchID')));
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
            $this->db->where('matchID', trim($this->input->post('matchID')));
            $this->db->update('srp_erp_rvadvancematch', $data);
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;
        }
    }

    function delete_rv_match_detail()
    {
        $this->db->select('invoiceAutoID,transactionAmount');
        $this->db->where('matchDetailID', $this->input->post('matchDetailID'));
        $data = $this->db->get('srp_erp_rvadvancematchdetails')->row_array();
        $id = $data['invoiceAutoID'];
        $amo = $data['transactionAmount'];
        $this->db->query("UPDATE srp_erp_customerinvoicemaster SET advanceMatchedTotal = (advanceMatchedTotal-$amo),receiptInvoiceYN = 0 WHERE invoiceAutoID=$id");

        $this->db->where('matchDetailID', $this->input->post('matchDetailID'));
        $results = $this->db->delete('srp_erp_rvadvancematchdetails');
        $this->session->set_flashdata('s', 'Receipt Matching Deleted Successfully');
        return true;
    }

    function fetch_receipt_voucher_match_template_data($matchID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(matchDate,\'' . $convertFormat . '\') AS matchDate,DATE_FORMAT(confirmedDate,\'' . $convertFormat . ' %h:%i:%s\') AS confirmedDate');
        $this->db->where('matchID', $matchID);
        $this->db->from('srp_erp_rvadvancematch');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);
        $this->db->select('*');
        $this->db->where('matchID', $matchID);
        $this->db->from('srp_erp_rvadvancematchdetails');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function customer_inv($customerID, $currencyID, $RVdate)
    {
        $date_format_policy = date_format_policy();
        $RVdate = input_format_date($RVdate, $date_format_policy);
        //$RVdate = convert_date_format($RVdate);
        $data = $this->db->query("SELECT srp_erp_customerinvoicemaster.invoiceAutoID,slr.returnsalesvalue as salesreturnvalue,invoiceCode,receiptTotalAmount,advanceMatchedTotal,creditNoteTotalAmount,referenceNo ,( ( cid.transactionAmount - cid.totalAfterTax ) * ( IFNULL( tax.taxPercentage, 0 ) / 100 ) + IFNULL( cid.transactionAmount, 0 )) as transactionAmount  FROM srp_erp_customerinvoicemaster LEFT JOIN (SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount,IFNULL(SUM(totalAfterTax ),0) as totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) cid ON srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID
 LEFT JOIN (SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID) tax ON tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN (
	SELECT 
	invoiceAutoID,
	IFNULL( SUM(slaesdetail.totalValue), 0 ) AS returnsalesvalue
	from 
	srp_erp_salesreturndetails slaesdetail
	GROUP BY invoiceAutoID
	) slr on slr.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID  WHERE confirmedYN = 1 AND approvedYN = 1 AND receiptInvoiceYN = 0 AND `customerID` = '{$customerID}' AND `transactionCurrency` = '{$currencyID}' AND invoiceDate <= '{$RVdate}' ")->result_array();
        //echo $this->db->last_query();
        return $data;
    }

    function save_inv_base_items()
    {
        $this->db->trans_start();
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $this->db->select('customerReceivableAutoID,slr.returnsalesvalue as returnsalesvalue,companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,srp_erp_customerinvoicemaster.invoiceAutoID,invoiceCode,referenceNo,invoiceDate,invoiceNarration,( ( cid.transactionAmount - cid.totalAfterTax ) * ( IFNULL( tax.taxPercentage, 0 ) / 100 ) + IFNULL( cid.transactionAmount, 0 ) ) as transactionAmount,receiptTotalAmount,advanceMatchedTotal,creditNoteTotalAmount,customerReceivableSystemGLCode,customerReceivableGLAccount,customerReceivableDescription,customerReceivableType,segmentID,segmentCode,transactionCurrencyDecimalPlaces');
        $this->db->from('srp_erp_customerinvoicemaster');
        $this->db->join('(SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount,IFNULL(SUM(totalAfterTax ),0) as totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) cid', 'srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID', 'left');

        $this->db->join('(SELECT 
	invoiceAutoID,
	IFNULL( SUM(slaesdetail.totalValue), 0 ) AS returnsalesvalue
	from 
	srp_erp_salesreturndetails slaesdetail
	GROUP BY invoiceAutoID) slr', 'srp_erp_customerinvoicemaster.invoiceAutoID = slr.invoiceAutoID', 'left');



        $this->db->join('(SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID) tax', 'tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID', 'left');
        $this->db->where_in('srp_erp_customerinvoicemaster.invoiceAutoID', $this->input->post('invoiceAutoID'));
        $master_recode = $this->db->get()->result_array();
        $amount = $this->input->post('amount');
        for ($i = 0; $i < count($master_recode); $i++) {
            $data[$i]['receiptVoucherAutoId'] = $this->input->post('receiptVoucherAutoId');
            $data[$i]['invoiceAutoID'] = $master_recode[$i]['invoiceAutoID'];
            $data[$i]['type'] = 'Invoice';
            $data[$i]['invoiceCode'] = $master_recode[$i]['invoiceCode'];
            $data[$i]['referenceNo'] = $master_recode[$i]['referenceNo'];
            $data[$i]['invoiceDate'] = $master_recode[$i]['invoiceDate'];
            $data[$i]['GLAutoID'] = $master_recode[$i]['customerReceivableAutoID'];
            $data[$i]['systemGLCode'] = $master_recode[$i]['customerReceivableSystemGLCode'];
            $data[$i]['GLCode'] = $master_recode[$i]['customerReceivableGLAccount'];
            $data[$i]['GLDescription'] = $master_recode[$i]['customerReceivableDescription'];
            $data[$i]['GLType'] = $master_recode[$i]['customerReceivableType'];
            $data[$i]['description'] = $master_recode[$i]['invoiceNarration'];
            $data[$i]['Invoice_amount'] = $master_recode[$i]['transactionAmount'];
            $data[$i]['segmentID'] = $master_recode[$i]['segmentID'];
            $data[$i]['segmentCode'] = $master_recode[$i]['segmentCode'];
            $data[$i]['due_amount'] = ($master_recode[$i]['transactionAmount'] - ($master_recode[$i]['receiptTotalAmount'] + $master_recode[$i]['advanceMatchedTotal'] + $master_recode[$i]['creditNoteTotalAmount'] + $master_recode[$i]['returnsalesvalue']));
            $data[$i]['balance_amount'] = ($data[$i]['due_amount'] - round($amount[$i],$master_recode[$i]['transactionCurrencyDecimalPlaces']));
            $data[$i]['transactionAmount'] = round($amount[$i],$master_recode[$i]['transactionCurrencyDecimalPlaces']);
            $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['companyLocalExchangeRate']);
            $data[$i]['companyLocalExchangeRate'] = $master_recode[$i]['companyLocalExchangeRate'];
            $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['companyReportingExchangeRate']);
            $data[$i]['companyReportingExchangeRate'] = $master_recode[$i]['companyReportingExchangeRate'];
            $data[$i]['customerAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['customerCurrencyExchangeRate']);
            $data[$i]['customerCurrencyExchangeRate'] = $master_recode[$i]['customerCurrencyExchangeRate'];
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

            $grv_m[$i]['invoiceAutoID'] = $invoiceAutoID[$i];
            $grv_m[$i]['receiptTotalAmount'] = ($master_recode[$i]['receiptTotalAmount'] + $amount[$i]);
            $grv_m[$i]['receiptInvoiceYN'] = 0;
            if ($data[$i]['balance_amount'] <= 0) {
                $grv_m[$i]['receiptInvoiceYN'] = 1;
            }
        }

        if (!empty($data)) {
            $this->db->update_batch('srp_erp_customerinvoicemaster', $grv_m, 'invoiceAutoID');
            $this->db->insert_batch('srp_erp_customerreceiptdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', ' Invoice : Details Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', ' Invoice : ' . count($master_recode) . ' Item Details Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true);
            }
        } else {
            return array('status' => false);
        }
    }

    function save_rv_item_detail()
    {
        $itemAutoIDs = $this->input->post('itemAutoID');
        $wareHouseAutoIDs = array_filter($this->input->post('wareHouseAutoID'));
        $itemAutoIDJoin = join(',', $itemAutoIDs);
        $wareHouseAutoIDJoin = join(',', $wareHouseAutoIDs);


        if (!trim($this->input->post('receiptVoucherDetailAutoID')) && !empty($wareHouseAutoIDJoin)) {
            $this->db->select('receiptVoucherAutoId,,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_customerreceiptdetail');
            $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId')));
            $this->db->where('itemAutoID IN (' . $itemAutoIDJoin . ')');
            $this->db->where('wareHouseAutoID IN (' . $wareHouseAutoIDJoin . ')');
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('w', 'Receipt Voucher Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }

        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $wareHouse = $this->input->post('wareHouse');
        $projectExist = project_is_exist();

        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $projectID = $this->input->post('projectID');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');

        $this->db->select('transactionCurrency, transactionExchangeRate, transactionCurrencyID,companyLocalCurrencyID, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate, customerCurrency,customerExchangeRate,customerCurrencyID,companyReportingCurrencyID,segmentID,segmentCode');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId')));
        $this->db->from('srp_erp_customerreceiptmaster');
        $master = $this->db->get()->row_array();

        $this->db->trans_start();

        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $serviceitm= $this->db->get()->row_array();

            $wareHouse_location = explode('|', trim($wareHouse[$key]));
            $item_data = fetch_item_data(trim($itemAutoID));
            $uomDesc = explode('|', $uom[$key]);
            $data['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId'));
            $data['itemAutoID'] = trim($itemAutoID);
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasure'] = trim($uomDesc[0]);
            $data['unitOfMeasureID'] = trim($UnitOfMeasureID[$key]);
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['requestedQty'] = trim($quantityRequested[$key]);
            $data['unittransactionAmount'] = trim($estimatedAmount[$key]);
            $data['transactionAmount'] = ($data['unittransactionAmount'] * trim($quantityRequested[$key]));
            $data['comment'] = trim($comment[$key]);
            $data['remarks'] = trim($remarks[$key]);
            $data['type'] = 'Item';
            if($serviceitm['mainCategory']!='Service') {
                $data['wareHouseAutoID'] = trim($wareHouseAutoID[$key]);
                $data['wareHouseCode'] = trim($wareHouse_location[0]);
                $data['wareHouseLocation'] = trim($wareHouse_location[1]);
                $data['wareHouseDescription'] = trim($wareHouse_location[2]);
            }else{
                $data['wareHouseAutoID'] = null;
                $data['wareHouseCode'] = null;
                $data['wareHouseLocation'] = null;
                $data['wareHouseDescription'] = null;
            }
            $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data['customerCurrencyExchangeRate'] = $master['customerExchangeRate'];
            $data['companyLocalAmount'] = ($data['transactionAmount'] / $data['companyLocalExchangeRate']);
            $data['companyReportingAmount'] = ($data['transactionAmount'] / $data['companyReportingExchangeRate']);

            $data['customerAmount'] = 0;
            if ($data['customerCurrencyExchangeRate']) {
                $data['customerAmount'] = ($data['transactionAmount'] / $data['customerCurrencyExchangeRate']);
            }


            $data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $data['companyLocalExchangeRate']);
            $data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $data['companyReportingExchangeRate']);

            $data['unitpartyAmount'] = 0;
            if ($data['customerCurrencyExchangeRate']) {
                $data['unitpartyAmount'] = ($data['unittransactionAmount'] / $data['customerCurrencyExchangeRate']);
            }


            $data['segmentID'] = $master['segmentID'];
            $data['segmentCode'] = $master['segmentCode'];
            $data['GLAutoID'] = $item_data['revanueGLAutoID'];
            $data['systemGLCode'] = $item_data['revanueSystemGLCode'];
            $data['GLCode'] = $item_data['revanueGLCode'];
            $data['GLDescription'] = $item_data['revanueDescription'];
            $data['GLType'] = $item_data['revanueType'];
            $data['expenseGLAutoID'] = $item_data['costGLAutoID'];
            $data['expenseGLCode'] = $item_data['costGLCode'];
            $data['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['expenseGLDescription'] = $item_data['costDescription'];
            $data['expenseGLType'] = $item_data['costType'];
            $data['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
            $data['revenueGLCode'] = $item_data['revanueGLCode'];
            $data['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
            $data['revenueGLDescription'] = $item_data['revanueDescription'];
            $data['revenueGLType'] = $item_data['revanueType'];
            $data['assetGLAutoID'] = $item_data['assteGLAutoID'];
            $data['assetGLCode'] = $item_data['assteGLCode'];
            $data['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
            $data['assetGLDescription'] = $item_data['assteDescription'];
            $data['assetGLType'] = $item_data['assteType'];
            $data['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['itemCategory'] = $item_data['mainCategory'];
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            if (trim($this->input->post('receiptVoucherDetailAutoID'))) {
                /*$this->db->where('receiptVoucherDetailAutoID', trim($this->input->post('receiptVoucherDetailAutoID')));
                $this->db->update('srp_erp_customerreceiptdetail', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Receipt Voucher Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Receipt Voucher Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $this->input->post('purchaseOrderDetailsID'));
                }*/
            } else {
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_customerreceiptdetail', $data);
                $last_id = $this->db->insert_id();

                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', trim($itemAutoID));
                $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();


                /*nusky*/
                /*if (empty($warehouseitems)) {
                    $data_arr = array(
                        'wareHouseAutoID' => $data['wareHouseAutoID'],
                        'wareHouseLocation' => $data['wareHouseLocation'],
                        'wareHouseDescription' => $data['wareHouseDescription'],
                        'itemAutoID' => trim($itemAutoID),
                        'itemSystemCode' => trim($this->input->post('itemSystemCode')),
                        'itemDescription' => trim($this->input->post('itemDescription')),
                        'unitOfMeasure' => trim($this->input->post('defaultUOM')),
                        'currentStock' => 0,
                        'companyID' => $this->common_data['company_data']['company_id'],
                        'companyCode' => $this->common_data['company_data']['company_code'],
                    );
                    $this->db->insert('srp_erp_warehouseitems', $data_arr);
                }*/
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Records Inserted error');
        } else {
            $this->db->trans_commit();
            return array('s', 'Records Inserted successfully');
        }

    }

    function update_rv_item_detail()
    {
        $itemAutoID = $this->input->post('itemAutoID');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $this->db->select('mainCategory');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', $itemAutoID);
        $serviceitm= $this->db->get()->row_array();

        if (!empty($this->input->post('receiptVoucherDetailAutoID')) && $serviceitm['mainCategory']!='Service') {
            $this->db->select('receiptVoucherAutoId,,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_customerreceiptdetail');
            $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId')));
            $this->db->where('itemAutoID IN (' . $itemAutoID . ')');
            $this->db->where('wareHouseAutoID IN (' . $wareHouseAutoID . ')');
            $this->db->where('receiptVoucherDetailAutoID !=', trim($this->input->post('receiptVoucherDetailAutoID')));
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('w', 'Receipt Voucher Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }


        $wareHouse = $this->input->post('wareHouse');
        $projectExist = project_is_exist();
        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $comment = $this->input->post('comment');
        $projectID = $this->input->post('projectID');
        $remarks = $this->input->post('remarks');

        $this->db->select('transactionCurrency, transactionExchangeRate, transactionCurrencyID,companyLocalCurrencyID, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate, customerCurrency,customerExchangeRate,customerCurrencyID,companyReportingCurrencyID,segmentID,segmentCode');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId')));
        $this->db->from('srp_erp_customerreceiptmaster');
        $master = $this->db->get()->row_array();

        $this->db->trans_start();

        $wareHouse_location = explode('|', trim($wareHouse));
        $item_data = fetch_item_data(trim($itemAutoID));
        $uom = explode('|', $uom);
        $data['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId'));
        $data['itemAutoID'] = trim($itemAutoID);
        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($this->input->post('transactionCurrencyID'), $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['unitOfMeasure'] = trim($uom[0]);
        $data['unitOfMeasureID'] = trim($UnitOfMeasureID);
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['requestedQty'] = trim($quantityRequested);
        $data['unittransactionAmount'] = trim($estimatedAmount);
        $data['transactionAmount'] = ($data['unittransactionAmount'] * trim($quantityRequested));
        $data['comment'] = trim($comment);
        $data['remarks'] = trim($remarks);
        $data['type'] = 'Item';
        if($serviceitm['mainCategory']!='Service') {
            $data['wareHouseAutoID'] = trim($wareHouseAutoID);
            $data['wareHouseCode'] = trim($wareHouse_location[0]);
            $data['wareHouseLocation'] = trim($wareHouse_location[1]);
            $data['wareHouseDescription'] = trim($wareHouse_location[2]);
        }else{
            $data['wareHouseAutoID'] = null;
            $data['wareHouseCode'] = null;
            $data['wareHouseLocation'] = null;
            $data['wareHouseDescription'] = null;
        }
        $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
        $data['customerCurrencyExchangeRate'] = $master['customerExchangeRate'];
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $data['companyLocalExchangeRate']);
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $data['companyReportingExchangeRate']);
        $data['customerAmount'] = 0;
        if ($data['customerCurrencyExchangeRate']) {
            $data['customerAmount'] = ($data['transactionAmount'] / $data['customerCurrencyExchangeRate']);
        }

        $data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $data['companyLocalExchangeRate']);
        $data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $data['companyReportingExchangeRate']);
        $data['unitpartyAmount'] = 0;
        if ($data['customerCurrencyExchangeRate']) {
            $data['unitpartyAmount'] = ($data['unittransactionAmount'] / $data['customerCurrencyExchangeRate']);
        }

        $data['segmentID'] = $master['segmentID'];
        $data['segmentCode'] = $master['segmentCode'];
        $data['GLAutoID'] = $item_data['revanueGLAutoID'];
        $data['systemGLCode'] = $item_data['revanueSystemGLCode'];
        $data['GLCode'] = $item_data['revanueGLCode'];
        $data['GLDescription'] = $item_data['revanueDescription'];
        $data['GLType'] = $item_data['revanueType'];
        $data['expenseGLAutoID'] = $item_data['costGLAutoID'];
        $data['expenseGLCode'] = $item_data['costGLCode'];
        $data['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
        $data['expenseGLDescription'] = $item_data['costDescription'];
        $data['expenseGLType'] = $item_data['costType'];
        $data['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
        $data['revenueGLCode'] = $item_data['revanueGLCode'];
        $data['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
        $data['revenueGLDescription'] = $item_data['revanueDescription'];
        $data['revenueGLType'] = $item_data['revanueType'];
        $data['assetGLAutoID'] = $item_data['assteGLAutoID'];
        $data['assetGLCode'] = $item_data['assteGLCode'];
        $data['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
        $data['assetGLDescription'] = $item_data['assteDescription'];
        $data['assetGLType'] = $item_data['assteType'];
        $data['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
        $data['itemCategory'] = $item_data['mainCategory'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        $this->db->where('receiptVoucherDetailAutoID', trim($this->input->post('receiptVoucherDetailAutoID')));
        $this->db->update('srp_erp_customerreceiptdetail', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Receipt Voucher Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Receipt Voucher Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');
        }

    }

    function fetch_receipt_voucher_template_data($receiptVoucherAutoId)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(RVdate,\'' . $convertFormat . '\') AS RVdate,DATE_FORMAT(RVchequeDate,\'' . $convertFormat . '\') AS RVchequeDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,if(customerID IS NULL OR customerID = 0,srp_erp_customerreceiptmaster.customerName,srp_erp_customermaster.customerName) as customerName,srp_erp_customermaster.customerAddress1 as customeradd,srp_erp_customermaster.customerTelephone as customertel,srp_erp_customermaster.customerSystemCode as customersys,srp_erp_customermaster.customerFax as customerfax');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customerreceiptmaster.customerID = srp_erp_customermaster.customerAutoID', 'Left');
        $this->db->from('srp_erp_customerreceiptmaster');

        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        /*     $this->db->select('customerName,customerAddress1,customerTelephone,customerSystemCode,customerFax');
             $this->db->where('customerAutoID', $data['master']['customerID']);
             $this->db->from('srp_erp_customermaster');
             $data['customer'] = $this->db->get()->row_array();*/


        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'Item');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['item_detail'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'GL');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['gl_detail'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'Invoice');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['invoice'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'Advance');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['advance'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'creditnote');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['creditnote'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'PRVR');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['prvr_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->from('srp_erp_customerreceipttaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();
        return $data;
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

    function load_receipt_voucher_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(RVdate,\'' . $convertFormat . '\') AS RVdate,DATE_FORMAT(RVchequeDate,\'' . $convertFormat . '\') AS RVchequeDate');
        $this->db->where('receiptVoucherAutoId', $this->input->post('receiptVoucherAutoId'));
        return $this->db->get('srp_erp_customerreceiptmaster')->row_array();
    }

    function fetch_rv_details()
    {
        $receiptVoucherAutoId = trim($this->input->post('receiptVoucherAutoId'));
        $this->db->select('srp_erp_customerreceiptdetail.*,srp_erp_itemmaster.isSubitemExist,srp_erp_itemmaster.mainCategory');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID', 'left');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);

        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->from('srp_erp_customerreceipttaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();
        return $data;
    }

    function delete_tax_detail()
    {
        $this->db->delete('srp_erp_customerreceipttaxdetails', array('taxDetailAutoID' => trim($this->input->post('taxDetailAutoID'))));
        return true;
    }

    function save_direct_rv_detail()
    {
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerExchangeRate,transactionCurrencyID');
        $this->db->where('receiptVoucherAutoId', $this->input->post('receiptVoucherAutoId'));
        $master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();
        $projectExist = project_is_exist();
        $segment_gl = $this->input->post('segment_gl');
        $gl_code_des = $this->input->post('gl_code_des');
        $gl_auto_ids = $this->input->post('gl_code');
        $amount = $this->input->post('amount');
        $description = $this->input->post('description');
        $projectID = $this->input->post('projectID');

        foreach ($gl_auto_ids as $key => $gl_auto_id) {
            $segment = explode('|', trim($segment_gl[$key]));
            $gl_code = explode('|', trim($gl_code_des[$key]));

            $data[$key]['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId'));
            $data[$key]['GLAutoID'] = trim($gl_auto_id);
            $data[$key]['systemGLCode'] = trim($gl_code[0]);
            $data[$key]['GLCode'] = trim($gl_code[1]);
            $data[$key]['GLDescription'] = trim($gl_code[2]);
            $data[$key]['GLType'] = trim($gl_code[3]);
            $data[$key]['segmentID'] = trim($segment[0]);
            $data[$key]['segmentCode'] = trim($segment[1]);
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data[$key]['projectID'] = $projectID[$key];
                $data[$key]['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data[$key]['projectID'] = $projectID[$key];
            $data[$key]['transactionAmount'] = trim($amount[$key]);
            $data[$key]['companyLocalAmount'] = ($data[$key]['transactionAmount'] / $master['companyLocalExchangeRate']);
            $data[$key]['companyReportingAmount'] = ($data[$key]['transactionAmount'] / $master['companyReportingExchangeRate']);

            $data[$key]['customerAmount'] = 0;
            if ($master['customerExchangeRate']) {
                $data[$key]['customerAmount'] = ($data[$key]['transactionAmount'] / $master['customerExchangeRate']);
            }


            $data[$key]['description'] = trim($description[$key]);
            $data[$key]['type'] = 'GL';
            $data[$key]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$key]['modifiedUserID'] = $this->common_data['current_userID'];
            $data[$key]['modifiedUserName'] = $this->common_data['current_user'];
            $data[$key]['modifiedDateTime'] = $this->common_data['current_date'];

            //if (trim($this->input->post('receiptVoucherDetailAutoID'))) {
            /*$this->db->where('receiptVoucherDetailAutoID', trim($this->input->post('receiptVoucherDetailAutoID')));
            $this->db->update('srp_erp_customerreceiptdetail', $data[$key]);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Receipt Voucher Detail : ' . $data[$key]['GLDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Receipt Voucher Detail : ' . $data[$key]['GLDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('receiptVoucherDetailAutoID'));
            }*/
            //} else {
            $data[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdUserName'] = $this->common_data['current_user'];
            $data[$key]['createdDateTime'] = $this->common_data['current_date'];

            //}
        }

        $this->db->insert_batch('srp_erp_customerreceiptdetail', $data);
        $last_id = 0;//$this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Receipt Voucher Detail : Saved Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Receipt Voucher Detail : Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }

    }

    function update_direct_rv_detail()
    {
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerExchangeRate,transactionCurrencyID');
        $this->db->where('receiptVoucherAutoId', $this->input->post('receiptVoucherAutoId'));
        $master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();

        $projectExist = project_is_exist();
        $segment_gl = $this->input->post('segment_gl');
        $gl_code_des = $this->input->post('gl_code_des');
        $gl_auto_id = $this->input->post('gl_code');
        $projectID = $this->input->post('projectID');
        $amount = $this->input->post('amount');
        $description = $this->input->post('description');

        $segment = explode('|', trim($segment_gl));
        $gl_code = explode('|', trim($gl_code_des));

        $data['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId'));
        $data['GLAutoID'] = trim($gl_auto_id);

        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['systemGLCode'] = trim($gl_code[0]);
        $data['GLCode'] = trim($gl_code[1]);
        $data['GLDescription'] = trim($gl_code[2]);
        $data['GLType'] = trim($gl_code[3]);
        $data['segmentID'] = trim($segment[0]);
        $data['segmentCode'] = trim($segment[1]);
        $data['transactionAmount'] = trim($amount);
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $master['companyLocalExchangeRate']);
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $master['companyReportingExchangeRate']);
        $data['customerAmount'] = ($data['transactionAmount'] / $master['customerExchangeRate']);
        $data['description'] = trim($description);
        $data['type'] = 'GL';
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

        $this->db->where('receiptVoucherDetailAutoID', trim($this->input->post('receiptVoucherDetailAutoID')));
        $this->db->update('srp_erp_customerreceiptdetail', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Receipt Voucher Detail : Update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Receipt Voucher Detail : Updated Successfully.');
        }

    }

    function receipt_confirmation()
    {
        $this->load->library('approvals');

        $this->db->select('receiptVoucherAutoId');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId')));
        $this->db->from('srp_erp_customerreceiptdetail');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {
            $rvid=$this->input->post('receiptVoucherAutoId');
            $taxamnt=0;
            $GL = $this->db->query("SELECT
	SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount
FROM
	srp_erp_customerreceiptdetail
WHERE
	srp_erp_customerreceiptdetail.receiptVoucherAutoId = $rvid
	AND srp_erp_customerreceiptdetail.type='GL'
GROUP BY receiptVoucherAutoId")->row_array();

            if(empty($GL)){
                $GL=0;
            }else{
                $GL=$GL['transactionAmount'];
            }
            $Item = $this->db->query("SELECT
SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount
FROM
	srp_erp_customerreceiptdetail
WHERE
	srp_erp_customerreceiptdetail.receiptVoucherAutoId = $rvid
	AND srp_erp_customerreceiptdetail.type='Item'
GROUP BY receiptVoucherAutoId")->row_array();
            if(empty($Item)){
                $Item=0;
            }else{
                $Item=$Item['transactionAmount'];
            }
            $creditnote = $this->db->query("SELECT
	SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount
FROM
	srp_erp_customerreceiptdetail
WHERE
	srp_erp_customerreceiptdetail.receiptVoucherAutoId = $rvid
	AND srp_erp_customerreceiptdetail.type='creditnote'
GROUP BY receiptVoucherAutoId")->row_array();
            if(empty($creditnote)){
                $creditnote=0;
            }else{
                $creditnote=$creditnote['transactionAmount'];
            }
            $Advance = $this->db->query("SELECT
	SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount
FROM
	srp_erp_customerreceiptdetail
WHERE
	srp_erp_customerreceiptdetail.receiptVoucherAutoId = $rvid
	AND srp_erp_customerreceiptdetail.type='Advance'
GROUP BY receiptVoucherAutoId")->row_array();
            if(empty($Advance)){
                $Advance=0;
            }else{
                $Advance=$Advance['transactionAmount'];
            }
            $Invoice = $this->db->query("SELECT
	SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount
FROM
	srp_erp_customerreceiptdetail
WHERE
	srp_erp_customerreceiptdetail.receiptVoucherAutoId = $rvid
	AND srp_erp_customerreceiptdetail.type='Invoice'
GROUP BY receiptVoucherAutoId")->row_array();
            if(empty($Invoice)){
                $Invoice=0;
            }else{
                $Invoice=$Invoice['transactionAmount'];
            }
            $tax = $this->db->query("SELECT
	SUM(srp_erp_customerreceipttaxdetails.taxPercentage) as taxPercentage
FROM
	srp_erp_customerreceipttaxdetails
WHERE
	srp_erp_customerreceipttaxdetails.receiptVoucherAutoId = $rvid

GROUP BY receiptVoucherAutoId")->row_array();
            if(empty($tax)){
                $tax=0;
            }else{
                $tax=$tax['taxPercentage'];
                $taxamnt=(($Item+$GL)/100)*$tax;
            }
            $totalamnt=($Item+$GL+$Invoice+$Advance+$taxamnt)-$creditnote;

            if($totalamnt<0){
                return array('error' => 1, 'message' => 'Grand total should be greater than 0');
            }else{
                $this->db->select('documentID, RVcode,DATE_FORMAT(RVdate, "%Y") as invYear,DATE_FORMAT(RVdate, "%m") as invMonth,companyFinanceYearID');
                $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId')));
                $this->db->from('srp_erp_customerreceiptmaster');
                $master_dt = $this->db->get()->row_array();
                $this->load->library('sequence');
                if($master_dt['RVcode'] == "0"){
                    $rvcd = array(
                        'RVcode' => $this->sequence->sequence_generator_fin($master_dt['documentID'],$master_dt['companyFinanceYearID'],$master_dt['invYear'],$master_dt['invMonth'])
                    );
                    $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId')));
                    $this->db->update('srp_erp_customerreceiptmaster', $rvcd);
                }

                $this->db->select('documentID,receiptVoucherAutoId, RVcode,DATE_FORMAT(RVdate, "%Y") as invYear,DATE_FORMAT(RVdate, "%m") as invMonth,companyFinanceYearID');
                $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId')));
                $this->db->from('srp_erp_customerreceiptmaster');
                $app_data = $this->db->get()->row_array();

                $sql = "SELECT (srp_erp_customerreceiptdetail.requestedQty / srp_erp_customerreceiptdetail.conversionRateUOM) AS qty,srp_erp_warehouseitems.currentStock,(srp_erp_warehouseitems.currentStock-(srp_erp_customerreceiptdetail.requestedQty / srp_erp_customerreceiptdetail.conversionRateUOM)) as stock ,srp_erp_warehouseitems.itemAutoID as itemAutoID ,srp_erp_customerreceiptdetail.wareHouseAutoID FROM srp_erp_customerreceiptdetail INNER JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID AND srp_erp_customerreceiptdetail.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID where receiptVoucherAutoId = '{$this->input->post('receiptVoucherAutoId')}' AND itemCategory != 'Service' Having stock < 0";
                $item_low_qty = $this->db->query($sql)->result_array();
                if (!empty($item_low_qty)) {
                    //$this->session->set_flashdata('w', 'Some Item quantities are not sufficient to confirm this transaction');
                    return array('error' => 1, 'message' => 'Some Item quantities are not sufficient to confirm this transaction', 'itemAutoID' => $item_low_qty);
                }

                $approvals_status = $this->approvals->CreateApproval('RV', $app_data['receiptVoucherAutoId'], $app_data['RVcode'], 'Receipt Voucher', 'srp_erp_customerreceiptmaster', 'receiptVoucherAutoId');
                if ($approvals_status==1) {

                    /** item Master Sub check */
                    $documentID = trim($this->input->post('receiptVoucherAutoId'));
                    $validate = $this->validate_itemMasterSub($documentID);

                    /** end of item master sub */
                    if ($validate) {
                        $data = array(
                            'confirmedYN' => 1,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user']
                        );
                        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId')));
                        $this->db->update('srp_erp_customerreceiptmaster', $data);
                        //return array('status' => true, 'data' => 'Document Confirmed Successfully!');
                        return array('error' => 0, 'message' => 'Document Confirmed Successfully!');
                    } else {
                        return array('error' => 1, 'message' => 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');
                    }
                }else if($approvals_status==3){
                    return array('error' => 1, 'message' => 'There are no users exist to perform approval for this document');
                } else {
                    return array('error' => 1, 'message' => 'Confirm this transaction');
                    //return array('status' => false, 'data' => 'Confirm this transaction');
                }
            }


        }
    }

    function Receipt_match_confirmation()
    {
        $this->db->select('matchID');
        $this->db->where('matchID', trim($this->input->post('matchID')));
        $this->db->from('srp_erp_rvadvancematchdetails');
        $results = $this->db->get()->result_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {
            $data = array(
                'confirmedYN' => 1,
                'confirmedDate' => $this->common_data['current_date'],
                'confirmedByEmpID' => $this->common_data['current_userID'],
                'confirmedByName' => $this->common_data['current_user']
            );

            $this->db->where('matchID', trim($this->input->post('matchID')));
            $confirmation = $this->db->update('srp_erp_rvadvancematch', $data);
            if ($confirmation) {
                return array('error' => 0, 'message' => 'Document Confirmed Successfully !');
            } else {
                return array('error' => 1, 'message' => 'Document Confirmation failed !');
            }
        }
    }

    function delete_item_direct()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->where('receiptVoucherDetailAutoID', trim($this->input->post('receiptVoucherDetailAutoID')));
        $detail_arr = $this->db->get()->row_array();

        if ($detail_arr['type'] == 'Invoice') {
            $company_id = $this->common_data['company_data']['company_id'];
            $match_id = $detail_arr['invoiceAutoID'];
            $number = $detail_arr['transactionAmount'];
            $status = 0;
            $this->db->query("UPDATE srp_erp_customerinvoicemaster SET receiptTotalAmount = (receiptTotalAmount -{$number})  , receiptInvoiceYN = {$status}  WHERE invoiceAutoID='{$match_id}' and companyID='{$company_id}'");
        }


        /** update sub item master */

        $dataTmp['isSold'] = null;
        $dataTmp['soldDocumentAutoID'] = null;
        $dataTmp['soldDocumentDetailID'] = null;
        $dataTmp['soldDocumentID'] = null;
        $dataTmp['modifiedPCID'] = current_pc();
        $dataTmp['modifiedUserID'] = current_userID();
        $dataTmp['modifiedDatetime'] = format_date_mysql_datetime();

        $this->db->where('soldDocumentAutoID', $detail_arr['receiptVoucherAutoId']);
        $this->db->where('soldDocumentDetailID', $detail_arr['receiptVoucherDetailAutoID']);
        $this->db->where('soldDocumentID', 'RV');
        $this->db->update('srp_erp_itemmaster_sub', $dataTmp);

        /** end update sub item master */

        $this->db->where('receiptVoucherDetailAutoID', trim($this->input->post('receiptVoucherDetailAutoID')));
        $results = $this->db->delete('srp_erp_customerreceiptdetail');


        if ($results) {
            $this->session->set_flashdata('s', 'Receipt Voucher Detail Deleted Successfully');
            return true;
        }
    }

    function save_rv_advance_detail()
    {

        $amounts = $this->input->post('amount');
        $description = $this->input->post('description');
        $this->db->trans_start();
        //$po = explode('|', trim($this->input->post('po_code')));
        //$po_des = explode('|', trim($this->input->post('po_des')));
        // if (!empty($this->input->post('po_code')) && $this->input->post('po_des') != 'Select PO') {
        //     // $this->db->select('transactionCurrency, transactionExchangeRate, transactionCurrencyID,companyLocalCurrencyID, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency,companyReportingExchangeRate ,supplierCurrency,supplierCurrencyExchangeRate,supplierCurrencyID,companyReportingCurrencyID,segmentID,segmentCode');
        //     // $this->db->where('purchaseOrderID', trim($po[0]));
        //     // $master = $this->db->get('srp_erp_purchaseordermaster')->row_array();
        //     // $data['purchaseOrderID'] = trim($po[0]);
        //     // $data['PODate'] = trim($po[1]);
        //     // $data['POCode'] = trim($po_des[0]);
        //     // $data['PODescription'] = trim($po_des[1]);
        //     $data['customerCurrencyID'] = $master['supplierCurrencyID'];
        //     $data['customerCurrency'] = $master['supplierCurrency'];
        //     $data['customerExchangeRate'] = $master['supplierCurrencyExchangeRate'];
        // }else{
        $this->db->select('transactionCurrency, transactionExchangeRate, transactionCurrencyID,companyLocalCurrencyID, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate, customerCurrency,customerExchangeRate,customerCurrencyID,companyReportingCurrencyID,segmentID,segmentCode,customerreceivableAutoID,customerreceivableSystemGLCode,customerreceivableGLAccount,customerreceivableDescription,customerreceivableType');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId')));
        $master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();
        // $data['purchaseOrderID']    = 0;
        // $data['PODate']             =  null;
        // $data['POCode']             = null;
        //$data['PODescription'] = trim($this->input->post('description'));
        // $data['customerCurrencyID'] = $master['customerCurrencyID'];
        // $data['customerCurrency'] = $master['customerCurrency'];
        // $data['customerExchangeRate'] = $master['customerExchangeRate'];
        //}
        foreach ($amounts as $key => $amount) {
            $data[$key]['transactionAmount'] = trim($amount);
            $data[$key]['segmentID'] = $master['segmentID'];
            $data[$key]['segmentCode'] = $master['segmentCode'];
            $data[$key]['GLAutoID'] = $master['customerreceivableAutoID'];
            $data[$key]['SystemGLCode'] = $master['customerreceivableSystemGLCode'];
            $data[$key]['GLCode'] = $master['customerreceivableGLAccount'];
            $data[$key]['GLDescription'] = $master['customerreceivableDescription'];
            $data[$key]['GLType'] = $master['customerreceivableType'];
            $data[$key]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data[$key]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data[$key]['customerCurrencyExchangeRate'] = $master['customerExchangeRate'];
            $data[$key]['companyLocalAmount'] = ($data[$key]['transactionAmount'] / $master['companyLocalExchangeRate']);
            $data[$key]['companyReportingAmount'] = ($data[$key]['transactionAmount'] / $master['companyReportingExchangeRate']);
            $data[$key]['customerAmount'] = ($data[$key]['transactionAmount'] / $master['customerExchangeRate']);
            $data[$key]['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId'));
            $data[$key]['comment'] = trim($description[$key]);
            $data[$key]['type'] = 'Advance';
            $data[$key]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$key]['modifiedUserID'] = $this->common_data['current_userID'];
            $data[$key]['modifiedUserName'] = $this->common_data['current_user'];
            $data[$key]['modifiedDateTime'] = $this->common_data['current_date'];

            if (trim($this->input->post('receiptVoucherDetailAutoID'))) {
                /* $this->db->where('receiptVoucherDetailAutoID', trim($this->input->post('receiptVoucherDetailAutoID')));
                 $this->db->update('srp_erp_customerreceiptdetail', $data[$key]);
                 $this->db->trans_complete();
                 if ($this->db->trans_status() === FALSE) {
                     $this->session->set_flashdata('e', 'Receipt Voucher Detail Update Failed ' . $this->db->_error_message());
                     $this->db->trans_rollback();
                     return array('status' => false);
                 } else {
                     $this->session->set_flashdata('s', 'Receipt Voucher Detail Updated Successfully.');
                     $this->db->trans_commit();
                     return array('status' => true, 'last_id' => $this->input->post('receiptVoucherDetailAutoID'));
                 }*/
            } else {
                $data[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
                $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
                $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
                $data[$key]['createdPCID'] = $this->common_data['current_pc'];
                $data[$key]['createdUserID'] = $this->common_data['current_userID'];
                $data[$key]['createdUserName'] = $this->common_data['current_user'];
                $data[$key]['createdDateTime'] = $this->common_data['current_date'];

            }
        }

        $this->db->insert_batch('srp_erp_customerreceiptdetail', $data);
        $last_id = 0;//$this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Receipt Voucher Detail  Saved Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Receipt Voucher Detail Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }


    }

    function save_rv_approval()
    {
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_id = trim($this->input->post('receiptVoucherAutoId'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        $comments = trim($this->input->post('comments'));

        $sql = "SELECT
	(
		srp_erp_warehouseitems.currentStock - srp_erp_customerreceiptdetail.requestedQty
	) AS stockDiff,
	srp_erp_itemmaster.itemSystemCode,
	srp_erp_itemmaster.itemDescription,
	srp_erp_warehouseitems.currentStock as availableStock
FROM
	`srp_erp_customerreceiptdetail`
JOIN `srp_erp_warehouseitems` ON `srp_erp_customerreceiptdetail`.`itemAutoID` = `srp_erp_warehouseitems`.`itemAutoID`
AND `srp_erp_customerreceiptdetail`.`wareHouseAutoID` = `srp_erp_warehouseitems`.`wareHouseAutoID`
JOIN `srp_erp_itemmaster` ON `srp_erp_customerreceiptdetail`.`itemAutoID` = `srp_erp_itemmaster`.`itemAutoID`

WHERE
	`srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` = '$system_id'
AND `srp_erp_warehouseitems`.`companyID` = " . current_companyID() . "
HAVING
	`stockDiff` < 0";
        $items_arr = $this->db->query($sql)->result_array();
        if ($status != 1) {
            $items_arr = '';
        }
        if (!$items_arr) {
            $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'RV');
            if ($approvals_status == 1) {
                $this->db->select('*');
                $this->db->where('receiptVoucherAutoId', $system_id);
                $this->db->from('srp_erp_customerreceiptmaster');
                $master = $this->db->get()->row_array();
                $this->db->select('*');
                $this->db->where('receiptVoucherAutoId', $system_id);
                $this->db->from('srp_erp_customerreceiptdetail');
                $receipt_detail = $this->db->get()->result_array();
                for ($a = 0; $a < count($receipt_detail); $a++) {
                    if ($receipt_detail[$a]['type'] == 'Item') {
                        $item = fetch_item_data($receipt_detail[$a]['itemAutoID']);
                        if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory') {
                            $itemAutoID = $receipt_detail[$a]['itemAutoID'];
                            $qty = $receipt_detail[$a]['requestedQty'] / $receipt_detail[$a]['conversionRateUOM'];
                            $wareHouseAutoID = $receipt_detail[$a]['wareHouseAutoID'];
                            $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock -{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                            $item_arr[$a]['itemAutoID'] = $receipt_detail[$a]['itemAutoID'];
                            $item_arr[$a]['currentStock'] = ($item['currentStock'] - $qty);
                            $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) - ($item['companyLocalWacAmount'] * $qty)) / $item_arr[$a]['currentStock']), $master['companyLocalCurrencyDecimalPlaces']);
                            $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) - ($receipt_detail[$a]['transactionAmount'] / $master['companyReportingExchangeRate'])) / $item_arr[$a]['currentStock']), $master['companyReportingCurrencyDecimalPlaces']);
                            if (!empty($item_arr)) {
                                $this->db->where('itemAutoID', trim($receipt_detail[$a]['itemAutoID']));
                                $this->db->update('srp_erp_itemmaster', $item_arr[$a]);
                            }
                            $itemledger_arr[$a]['documentID'] = $master['documentID'];
                            $itemledger_arr[$a]['documentCode'] = $master['documentID'];
                            $itemledger_arr[$a]['documentAutoID'] = $master['receiptVoucherAutoId'];
                            $itemledger_arr[$a]['documentSystemCode'] = $master['RVcode'];
                            $itemledger_arr[$a]['documentDate'] = $master['RVdate'];
                            $itemledger_arr[$a]['referenceNumber'] = $master['referanceNo'];
                            $itemledger_arr[$a]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                            $itemledger_arr[$a]['companyFinanceYear'] = $master['companyFinanceYear'];
                            $itemledger_arr[$a]['FYBegin'] = $master['FYBegin'];
                            $itemledger_arr[$a]['FYEnd'] = $master['FYEnd'];
                            $itemledger_arr[$a]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                            $itemledger_arr[$a]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                            $itemledger_arr[$a]['wareHouseAutoID'] = $receipt_detail[$a]['wareHouseAutoID'];
                            $itemledger_arr[$a]['wareHouseCode'] = $receipt_detail[$a]['wareHouseCode'];
                            $itemledger_arr[$a]['wareHouseLocation'] = $receipt_detail[$a]['wareHouseLocation'];
                            $itemledger_arr[$a]['wareHouseDescription'] = $receipt_detail[$a]['wareHouseDescription'];
                            $itemledger_arr[$a]['itemAutoID'] = $receipt_detail[$a]['itemAutoID'];
                            $itemledger_arr[$a]['itemSystemCode'] = $receipt_detail[$a]['itemSystemCode'];
                            $itemledger_arr[$a]['itemDescription'] = $receipt_detail[$a]['itemDescription'];
                            $itemledger_arr[$a]['defaultUOMID'] = $receipt_detail[$a]['defaultUOMID'];
                            $itemledger_arr[$a]['defaultUOM'] = $receipt_detail[$a]['defaultUOM'];
                            $itemledger_arr[$a]['transactionUOMID'] = $receipt_detail[$a]['unitOfMeasureID'];
                            $itemledger_arr[$a]['transactionUOM'] = $receipt_detail[$a]['unitOfMeasure'];
                            $itemledger_arr[$a]['transactionQTY'] = ($receipt_detail[$a]['requestedQty'] * -1);
                            $itemledger_arr[$a]['convertionRate'] = $receipt_detail[$a]['conversionRateUOM'];
                            $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                            $itemledger_arr[$a]['PLGLAutoID'] = $item['revanueGLAutoID'];
                            $itemledger_arr[$a]['PLSystemGLCode'] = $item['revanueSystemGLCode'];
                            $itemledger_arr[$a]['PLGLCode'] = $item['revanueGLCode'];
                            $itemledger_arr[$a]['PLDescription'] = $item['revanueDescription'];
                            $itemledger_arr[$a]['PLType'] = $item['revanueType'];
                            $itemledger_arr[$a]['BLGLAutoID'] = $item['assteGLAutoID'];
                            $itemledger_arr[$a]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                            $itemledger_arr[$a]['BLGLCode'] = $item['assteGLCode'];
                            $itemledger_arr[$a]['BLDescription'] = $item['assteDescription'];
                            $itemledger_arr[$a]['BLType'] = $item['assteType'];
                            $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                            $ex_rate_wac = (1 / $master['companyLocalExchangeRate']);
                            $itemledger_arr[$a]['transactionAmount'] = round((($receipt_detail[$a]['companyLocalWacAmount'] / $ex_rate_wac) * ($itemledger_arr[$a]['transactionQTY'] / $itemledger_arr[$a]['convertionRate'])), $itemledger_arr[$a]['transactionCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['salesPrice'] = (($receipt_detail[$a]['transactionAmount'] / ($itemledger_arr[$a]['transactionQTY'] / $itemledger_arr[$a]['convertionRate'])) * -1);
                            $itemledger_arr[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                            $itemledger_arr[$a]['transactionCurrency'] = $master['transactionCurrency'];
                            $itemledger_arr[$a]['transactionExchangeRate'] = $master['transactionExchangeRate'];

                            $itemledger_arr[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                            $itemledger_arr[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                            $itemledger_arr[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                            $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                            $itemledger_arr[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                            $itemledger_arr[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                            $itemledger_arr[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                            $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                            $itemledger_arr[$a]['partyCurrencyID'] = $master['customerCurrencyID'];
                            $itemledger_arr[$a]['partyCurrency'] = $master['customerCurrency'];
                            $itemledger_arr[$a]['partyCurrencyExchangeRate'] = $master['customerExchangeRate'];
                            $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $master['customerCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['partyCurrencyAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['partyCurrencyExchangeRate']), $itemledger_arr[$a]['partyCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['confirmedYN'] = $master['confirmedYN'];
                            $itemledger_arr[$a]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                            $itemledger_arr[$a]['confirmedByName'] = $master['confirmedByName'];
                            $itemledger_arr[$a]['confirmedDate'] = $master['confirmedDate'];
                            $itemledger_arr[$a]['approvedYN'] = $master['approvedYN'];
                            $itemledger_arr[$a]['approvedDate'] = $master['approvedDate'];
                            $itemledger_arr[$a]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                            $itemledger_arr[$a]['approvedbyEmpName'] = $master['approvedbyEmpName'];
                            $itemledger_arr[$a]['segmentID'] = $master['segmentID'];
                            $itemledger_arr[$a]['segmentCode'] = $master['segmentCode'];
                            $itemledger_arr[$a]['companyID'] = $master['companyID'];
                            $itemledger_arr[$a]['companyCode'] = $master['companyCode'];
                            $itemledger_arr[$a]['createdUserGroup'] = $master['createdUserGroup'];
                            $itemledger_arr[$a]['createdPCID'] = $master['createdPCID'];
                            $itemledger_arr[$a]['createdUserID'] = $master['createdUserID'];
                            $itemledger_arr[$a]['createdDateTime'] = $master['createdDateTime'];
                            $itemledger_arr[$a]['createdUserName'] = $master['createdUserName'];
                            $itemledger_arr[$a]['modifiedPCID'] = $master['modifiedPCID'];
                            $itemledger_arr[$a]['modifiedUserID'] = $master['modifiedUserID'];
                            $itemledger_arr[$a]['modifiedDateTime'] = $master['modifiedDateTime'];
                            $itemledger_arr[$a]['modifiedUserName'] = $master['modifiedUserName'];
                        }
                    }
                }

                /*if (!empty($item_arr)) {
                    $item_arr = array_values($item_arr);
                    $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
                }*/

                if (!empty($itemledger_arr)) {
                    $itemledger_arr = array_values($itemledger_arr);
                    $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                }

                $this->load->model('Double_entry_model');
                $double_entry = $this->Double_entry_model->fetch_double_entry_receipt_voucher_data($system_id, 'RV');
                for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                    $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['receiptVoucherAutoId'];
                    $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                    $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['RVcode'];
                    $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['RVdate'];
                    $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['RVType'];
                    $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['RVdate'];
                    $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['RVdate']));
                    $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['RVNarration'];
                    $generalledger_arr[$i]['chequeNumber'] = $double_entry['master_data']['RVchequeNo'];
                    $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['gl_detail'][$i]['transactionExchangeRate'];
                    $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['gl_detail'][$i]['companyLocalExchangeRate'];
                    $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['gl_detail'][$i]['companyReportingExchangeRate'];
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
                $amount = receipt_voucher_total_value($double_entry['master_data']['receiptVoucherAutoId'], $double_entry['master_data']['transactionCurrencyDecimalPlaces'], 0);
                $bankledger_arr['documentMasterAutoID'] = $double_entry['master_data']['receiptVoucherAutoId'];
                $bankledger_arr['documentDate'] = $double_entry['master_data']['RVdate'];
                $bankledger_arr['transactionType'] = 1;
                $bankledger_arr['bankName'] = $double_entry['master_data']['RVbank'];
                $bankledger_arr['bankGLAutoID'] = $double_entry['master_data']['bankGLAutoID'];
                $bankledger_arr['bankSystemAccountCode'] = $double_entry['master_data']['bankSystemAccountCode'];
                $bankledger_arr['bankGLSecondaryCode'] = $double_entry['master_data']['bankGLSecondaryCode'];
                $bankledger_arr['documentType'] = 'RV';
                $bankledger_arr['documentSystemCode'] = $double_entry['master_data']['RVcode'];
                $bankledger_arr['modeofpayment'] = $double_entry['master_data']['modeOfPayment'];
                $bankledger_arr['chequeNo'] = $double_entry['master_data']['RVchequeNo'];
                $bankledger_arr['chequeDate'] = $double_entry['master_data']['RVchequeDate'];
                $bankledger_arr['memo'] = $double_entry['master_data']['RVNarration'];
                $bankledger_arr['partyType'] = 'CUS';
                $bankledger_arr['partyAutoID'] = $double_entry['master_data']['customerID'];
                $bankledger_arr['partyCode'] = $double_entry['master_data']['customerSystemCode'];
                $bankledger_arr['partyName'] = $double_entry['master_data']['customerName'];
                $bankledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $bankledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $bankledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                $bankledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                $bankledger_arr['transactionAmount'] = $amount;
                $bankledger_arr['partyCurrencyID'] = $double_entry['master_data']['customerCurrencyID'];
                $bankledger_arr['partyCurrency'] = $double_entry['master_data']['customerCurrency'];
                $bankledger_arr['partyCurrencyExchangeRate'] = $double_entry['master_data']['customerExchangeRate'];
                $bankledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['customerCurrencyDecimalPlaces'];
                $bankledger_arr['partyCurrencyAmount'] = ($bankledger_arr['transactionAmount'] / $bankledger_arr['partyCurrencyExchangeRate']);
                $bankledger_arr['bankCurrencyID'] = $double_entry['master_data']['bankCurrencyID'];
                $bankledger_arr['bankCurrency'] = $double_entry['master_data']['bankCurrency'];
                $bankledger_arr['bankCurrencyExchangeRate'] = $double_entry['master_data']['bankCurrencyExchangeRate'];
                $bankledger_arr['bankCurrencyAmount'] = ($bankledger_arr['transactionAmount'] / $bankledger_arr['bankCurrencyExchangeRate']);
                $bankledger_arr['bankCurrencyDecimalPlaces'] = $double_entry['master_data']['bankCurrencyDecimalPlaces'];
                $bankledger_arr['companyID'] = $double_entry['master_data']['companyID'];
                $bankledger_arr['companyCode'] = $double_entry['master_data']['companyCode'];
                $bankledger_arr['segmentID'] = $double_entry['master_data']['segmentID'];
                $bankledger_arr['segmentCode'] = $double_entry['master_data']['segmentCode'];
                $bankledger_arr['createdPCID'] = $this->common_data['current_pc'];
                $bankledger_arr['createdUserID'] = $this->common_data['current_userID'];
                $bankledger_arr['createdDateTime'] = $this->common_data['current_date'];
                $bankledger_arr['createdUserName'] = $this->common_data['current_user'];
                $bankledger_arr['modifiedPCID'] = $this->common_data['current_pc'];
                $bankledger_arr['modifiedUserID'] = $this->common_data['current_userID'];
                $bankledger_arr['modifiedDateTime'] = $this->common_data['current_date'];
                $bankledger_arr['modifiedUserName'] = $this->common_data['current_user'];

                $this->db->insert('srp_erp_bankledger', $bankledger_arr);
                if (!empty($generalledger_arr)) {
                    $generalledger_arr = array_values($generalledger_arr);
                    $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                    $this->db->select('sum(transactionAmount) as transaction_total, sum(companyLocalAmount) as companyLocal_total, sum(companyReportingAmount) as companyReporting_total, sum(partyCurrencyAmount) as party_total');
                    $this->db->where('documentCode', 'RV');
                    $this->db->where('documentMasterAutoID', $system_id);
                    $totals = $this->db->get('srp_erp_generalledger')->row_array();
                    if ($totals['transaction_total'] != 0 or $totals['companyLocal_total'] != 0 or $totals['companyReporting_total'] != 0 or $totals['party_total'] != 0) {
                        $generalledger_arr = array();
                        $ERGL_ID = $this->common_data['controlaccounts']['ERGL'];
                        $ERGL = fetch_gl_account_desc($ERGL_ID);
                        $generalledger_arr['documentMasterAutoID'] = $double_entry['master_data']['receiptVoucherAutoId'];
                        $generalledger_arr['documentCode'] = $double_entry['code'];
                        $generalledger_arr['documentSystemCode'] = $double_entry['master_data']['RVcode'];
                        $generalledger_arr['documentDate'] = $double_entry['master_data']['RVdate'];
                        $generalledger_arr['documentType'] = $double_entry['master_data']['RVType'];
                        $generalledger_arr['documentYear'] = $double_entry['master_data']['RVdate'];
                        $generalledger_arr['documentMonth'] = date("m", strtotime($double_entry['master_data']['RVdate']));
                        $generalledger_arr['documentNarration'] = $double_entry['master_data']['RVNarration'];
                        $generalledger_arr['chequeNumber'] = $double_entry['master_data']['RVchequeNo'];
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
                        $generalledger_arr['partyType'] = 'CUS';
                        $generalledger_arr['partyAutoID'] = $double_entry['master_data']['customerID'];
                        $generalledger_arr['partySystemCode'] = $double_entry['master_data']['customerSystemCode'];
                        $generalledger_arr['partyName'] = $double_entry['master_data']['customerName'];
                        $generalledger_arr['partyCurrencyID'] = $double_entry['master_data']['customerCurrencyID'];
                        $generalledger_arr['partyCurrency'] = $double_entry['master_data']['customerCurrency'];
                        $generalledger_arr['partyExchangeRate'] = $double_entry['master_data']['customerExchangeRate'];
                        $generalledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['customerCurrencyDecimalPlaces'];
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
                        $generalledger_arr['segmentID'] = $double_entry['master_data']['segmentID'];
                        $generalledger_arr['segmentCode'] = $double_entry['master_data']['segmentCode'];
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
                        $this->db->insert('srp_erp_generalledger', $generalledger_arr);
                    }
                }
                $this->db->select_sum('transactionAmount');
                $this->db->where('receiptVoucherAutoId', $system_id);
                $total = $this->db->get('srp_erp_customerreceiptdetail')->row('transactionAmount');

                $data['approvedYN'] = $status;
                $data['approvedbyEmpID'] = $this->common_data['current_userID'];
                $data['approvedbyEmpName'] = $this->common_data['current_user'];
                $data['approvedDate'] = $this->common_data['current_date'];
                $data['transactionAmount'] = $total;
                $this->db->where('receiptVoucherAutoId', $system_id);
                $this->db->update('srp_erp_customerreceiptmaster', $data);
                //$this->session->set_flashdata('s', 'Receipt Voucher Approval Successfully.');
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Receipt Voucher Approval Failed.', 1);
            } else {
                $this->db->trans_commit();
                return array('s', 'Receipt Voucher Approval Successfully.', 1);
            }
        } else {
            return array('e', 'Item quantities are insufficient.', $items_arr);
        }
    }

    function delete_receipt_voucher()
    {
        /*$this->db->select('type,invoiceAutoID,transactionAmount');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherId')));
        $detail_arr = $this->db->get()->result_array();
        $company_id = $this->common_data['company_data']['company_id'];
        foreach ($detail_arr as $val_as) {
            if ($val_as['type'] == 'Invoice') {
                $match_id = $val_as['invoiceAutoID'];
                $number = $val_as['transactionAmount'];
                $status = 0;
                $this->db->query("UPDATE srp_erp_customerinvoicemaster SET receiptTotalAmount = (receiptTotalAmount -{$number})  , receiptInvoiceYN = {$status}  WHERE invoiceAutoID='{$match_id}' and companyID='{$company_id}'");
            }
        }
        $this->db->delete('srp_erp_customerreceiptmaster', array('receiptVoucherAutoId' => trim($this->input->post('receiptVoucherId'))));
        $this->db->delete('srp_erp_customerreceiptdetail', array('receiptVoucherAutoId' => trim($this->input->post('receiptVoucherId'))));
        return true;*/
        $this->db->select('*');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherId')));
        $datas = $this->db->get()->row_array();

        $this->db->select('RVcode');
        $this->db->from('srp_erp_customerreceiptmaster');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherId')));
        $master = $this->db->get()->row_array();

        if ($datas) {
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        } else {
            if($master['RVcode']=="0"){
                $this->db->where('receiptVoucherAutoId', $this->input->post('receiptVoucherId'));
                $results = $this->db->delete('srp_erp_customerreceiptmaster');
                if ($results) {
                    $this->db->where('receiptVoucherAutoId', $this->input->post('receiptVoucherId'));
                    $this->db->delete('srp_erp_customerreceiptdetail');
                    $this->session->set_flashdata('s', 'Deleted Successfully');
                    return true;
                }
            }else{
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherId')));
                $this->db->update('srp_erp_customerreceiptmaster', $data);
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            }

        }
    }

    function delete_receipt_voucher_attachement()
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

    function fetch_income_all_detail()
    {
        $this->db->select('*');
        $this->db->where('receiptVoucherDetailAutoID', $this->input->post('receiptVoucherDetailAutoID'));
        return $this->db->get('srp_erp_customerreceiptdetail')->row_array();
    }

    function validate_itemMasterSub($itemAutoID)
    {
        $query1 = "SELECT
                        count(*) AS countAll 
                    FROM
                        srp_erp_customerreceiptmaster masterTbl
                    LEFT JOIN srp_erp_customerreceiptdetail detailTbl ON masterTbl.receiptVoucherAutoId = detailTbl.receiptVoucherAutoId
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = detailTbl.receiptVoucherDetailAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.receiptVoucherAutoId = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 ";
        $r1 = $this->db->query($query1)->row_array();
        //echo $this->db->last_query();

        $query2 = "SELECT
                        SUM(detailTbl.requestedQty) AS totalQty
                    FROM
                        srp_erp_customerreceiptmaster masterTbl
                    LEFT JOIN srp_erp_customerreceiptdetail detailTbl ON masterTbl.receiptVoucherAutoId = detailTbl.receiptVoucherAutoId
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.receiptVoucherAutoId = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1";


        $r2 = $this->db->query($query2)->row_array();

        /*print_r($r1);
        print_r($r2);
        exit;*/

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

    function re_open_receipt_voucher()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherId')));
        $this->db->update('srp_erp_customerreceiptmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function re_open_receipt_match()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('matchID', trim($this->input->post('matchID')));
        $this->db->update('srp_erp_rvadvancematch', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function fetch_itemrecode_po()
    {
        $dataArr = array();
        $dataArr2 = array();
        $dataArr2['query'] = 'test';
        $companyCode = $this->common_data['company_data']['company_code'];
        $search_string = "%" . $_GET['query'] . "%";
        $data = $this->db->query('SELECT mainCategory,mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT( IFNULL(itemDescription,"empty"), " - ", IFNULL(itemSystemCode,"empty"), " - ", IFNULL(partNo,"empty")  , " - ", IFNULL(seconeryItemCode,"empty")) AS "Match" , isSubitemExist FROM srp_erp_itemmaster WHERE (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR seconeryItemCode LIKE "' . $search_string . '") AND financeCategory != 3 AND companyCode = "' . $companyCode . '" AND isActive="1"')->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['itemSystemCode'], 'itemAutoID' => $val['itemAutoID'], 'currentStock' => $val['currentStock'], 'defaultUnitOfMeasure' => $val['defaultUnitOfMeasure'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'companyLocalSellingPrice' => $val['companyLocalSellingPrice'], 'companyLocalWacAmount' => $val['companyLocalWacAmount'], 'isSubitemExist' => $val['isSubitemExist'], 'revanueGLCode' => $val['revanueGLCode'], 'mainCategory' => $val['mainCategory']);
            }

        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_rv_warehouse_item()
    {

        $this->db->select('srp_erp_warehouseitems.currentStock,companyLocalWacAmount,wareHouseDescription,wareHouseLocation');
        $this->db->from('srp_erp_warehouseitems');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_warehouseitems.itemAutoID');
        $this->db->where('wareHouseAutoID', trim($this->input->post('wareHouseAutoID')));
        $this->db->where('srp_erp_warehouseitems.itemAutoID', trim($this->input->post('itemAutoID')));
        $this->db->where('srp_erp_warehouseitems.companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get()->row_array();

        if (!empty($data)) {
            return array('error' => 0, 'message' => '', 'status' => true, 'currentStock' => $data['currentStock'], 'WacAmount' => $data['companyLocalWacAmount']);
        } else {
            $this->session->set_flashdata('w', "Item doesn't exists in the selected warehouse ");
            return array('status' => false, 'error' => 2, 'message' => "Item doesn't exists in the selected warehouse");
        }
    }


    function updateReceiptVoucher_edit_all_Item()
    {
        $itemAutoIDs = $this->input->post('itemAutoID');
        $receiptVoucherDetailAutoID = $this->input->post('receiptVoucherDetailAutoID');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $wareHouse = $this->input->post('wareHouse');
        $projectExist = project_is_exist();

        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $projectID = $this->input->post('projectID');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');

        $this->db->select('transactionCurrency, transactionExchangeRate, transactionCurrencyID,companyLocalCurrencyID, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate, customerCurrency,customerExchangeRate,customerCurrencyID,companyReportingCurrencyID,segmentID,segmentCode');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId')));
        $this->db->from('srp_erp_customerreceiptmaster');
        $master = $this->db->get()->row_array();

        $this->db->trans_start();

        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $serviceitm= $this->db->get()->row_array();

            if (!trim($receiptVoucherDetailAutoID[$key]) && $serviceitm['mainCategory']!='Service') {
                $this->db->select('receiptVoucherAutoId,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_customerreceiptdetail');
                $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId')));
                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Receipt Voucher Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            } else {
                $this->db->select('receiptVoucherAutoId,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_customerreceiptdetail');
                $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId')));
                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
                $this->db->where('receiptVoucherDetailAutoID !=', $receiptVoucherDetailAutoID[$key]);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Receipt Voucher Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $wareHouse_location = explode('|', trim($wareHouse[$key]));
            $item_data = fetch_item_data(trim($itemAutoID));
            $uomDesc = explode('|', $uom[$key]);
            $data['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId'));
            $data['itemAutoID'] = trim($itemAutoID);
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasure'] = trim($uomDesc[0]);
            $data['unitOfMeasureID'] = trim($UnitOfMeasureID[$key]);
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['requestedQty'] = trim($quantityRequested[$key]);
            $data['unittransactionAmount'] = trim($estimatedAmount[$key]);
            $data['transactionAmount'] = ($data['unittransactionAmount'] * trim($quantityRequested[$key]));
            $data['comment'] = trim($comment[$key]);
            $data['remarks'] = trim($remarks[$key]);
            $data['type'] = 'Item';
            if($serviceitm['mainCategory']!='Service') {
                $data['wareHouseAutoID'] = trim($wareHouseAutoID[$key]);
                $data['wareHouseCode'] = trim($wareHouse_location[0]);
                $data['wareHouseLocation'] = trim($wareHouse_location[1]);
                $data['wareHouseDescription'] = trim($wareHouse_location[2]);
            }else{
                $data['wareHouseAutoID'] = null;
                $data['wareHouseCode'] = null;
                $data['wareHouseLocation'] = null;
                $data['wareHouseDescription'] = null;
            }
            $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data['customerCurrencyExchangeRate'] = $master['customerExchangeRate'];
            $data['companyLocalAmount'] = ($data['transactionAmount'] / $data['companyLocalExchangeRate']);
            $data['companyReportingAmount'] = ($data['transactionAmount'] / $data['companyReportingExchangeRate']);

            $data['customerAmount'] = 0;
            if ($data['customerCurrencyExchangeRate']) {
                $data['customerAmount'] = ($data['transactionAmount'] / $data['customerCurrencyExchangeRate']);
            }


            $data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $data['companyLocalExchangeRate']);
            $data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $data['companyReportingExchangeRate']);

            $data['unitpartyAmount'] = 0;
            if ($data['customerCurrencyExchangeRate']) {
                $data['unitpartyAmount'] = ($data['unittransactionAmount'] / $data['customerCurrencyExchangeRate']);
            }


            $data['segmentID'] = $master['segmentID'];
            $data['segmentCode'] = $master['segmentCode'];
            $data['GLAutoID'] = $item_data['revanueGLAutoID'];
            $data['systemGLCode'] = $item_data['revanueSystemGLCode'];
            $data['GLCode'] = $item_data['revanueGLCode'];
            $data['GLDescription'] = $item_data['revanueDescription'];
            $data['GLType'] = $item_data['revanueType'];
            $data['expenseGLAutoID'] = $item_data['costGLAutoID'];
            $data['expenseGLCode'] = $item_data['costGLCode'];
            $data['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['expenseGLDescription'] = $item_data['costDescription'];
            $data['expenseGLType'] = $item_data['costType'];
            $data['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
            $data['revenueGLCode'] = $item_data['revanueGLCode'];
            $data['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
            $data['revenueGLDescription'] = $item_data['revanueDescription'];
            $data['revenueGLType'] = $item_data['revanueType'];
            $data['assetGLAutoID'] = $item_data['assteGLAutoID'];
            $data['assetGLCode'] = $item_data['assteGLCode'];
            $data['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
            $data['assetGLDescription'] = $item_data['assteDescription'];
            $data['assetGLType'] = $item_data['assteType'];
            $data['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['itemCategory'] = $item_data['mainCategory'];
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            if (trim($receiptVoucherDetailAutoID[$key])) {
                $this->db->where('receiptVoucherDetailAutoID', trim($receiptVoucherDetailAutoID[$key]));
                $this->db->update('srp_erp_customerreceiptdetail', $data);
                $this->db->trans_complete();
            } else {
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_customerreceiptdetail', $data);

                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', trim($itemAutoID));
                $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Records Insertion error');
        } else {
            $this->db->trans_commit();
            return array('s', 'Records Inserted successfully');
        }
    }


    function fetch_rv_details_all()
    {
        $receiptVoucherAutoId = trim($this->input->post('receiptVoucherAutoId'));
        $this->db->select('srp_erp_customerreceiptdetail.*,srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID', 'left');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'Item');

        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->from('srp_erp_customerreceipttaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();
        return $data;
    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'RV');
        $this->db->from('srp_erp_documentcodemaster ');
        return $this->db->get()->row_array();
    }

    function fetch_credit_note($customerID, $currencyID, $documentDate)
    {
        $tmpDate = format_date($documentDate);
        $output = $this->db->query("SELECT
	masterTbl.creditNoteMasterAutoID AS creditNoteMasterAutoID,
	masterTbl.creditNoteCode AS creditNoteCode,
	detailTbl.transactionAmount,
	masterTbl.docRefNo AS RefNo,
	SUM(crDetail.transactionAmount) AS RVTransactionAmount
FROM
	srp_erp_creditnotemaster masterTbl
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		creditNoteMasterAutoID
	FROM
		srp_erp_creditnotedetail
	WHERE
		(
			ISNULL(InvoiceAutoID)
			OR InvoiceAutoID = 0
		)
	GROUP BY
		creditNoteMasterAutoID
) detailTbl ON detailTbl.creditNoteMasterAutoID = masterTbl.creditNoteMasterAutoID
LEFT JOIN srp_erp_customerreceiptdetail AS crDetail ON crDetail.creditNoteAutoID = masterTbl.creditNoteMasterAutoID
WHERE
	masterTbl.confirmedYN = 1
AND masterTbl.approvedYN = 1
AND masterTbl.transactionCurrencyID = '$currencyID'
AND masterTbl.creditNoteDate <= '$tmpDate'
AND masterTbl.customerID = '$customerID'
GROUP BY
	masterTbl.creditNoteMasterAutoID")->result_array();
        //echo $this->db->last_query();
        return $output;

    }

    function save_creditNote_base_items()
    {
        $this->db->trans_start();
        $receiptVoucherAutoId = $this->input->post('receiptVoucherAutoId');

        /** Array */
        $creditNoteMasterAutoIDs = $this->input->post('creditNoteMasterAutoID');
        $amount = $this->input->post('amount');
        $transactionAmount = $this->input->post('transactionAmount');


        if (!empty($creditNoteMasterAutoIDs)) {
            $i = 0;
            foreach ($creditNoteMasterAutoIDs as $creditNoteMasterAutoID) {
                $master_recode = $this->get_creditNote_master($creditNoteMasterAutoID);
                $alreadyPaidAmount = $this->get_debitNote_paymentVoucher_transactionAmount($creditNoteMasterAutoID); // use this value to get due amount
                $due_amount = $transactionAmount[$i] - $alreadyPaidAmount;
                $balance_amount = $due_amount - $amount[$i];


                $data[$i]['creditNoteAutoID'] = $creditNoteMasterAutoIDs[$i];
                $data[$i]['invoiceAutoID'] = null;
                $data[$i]['type'] = 'creditnote';
                $data[$i]['receiptVoucherAutoId'] = $receiptVoucherAutoId;
                $data[$i]['invoiceCode'] = $master_recode['creditNoteCode'];
                $data[$i]['referenceNo'] = $master_recode['docRefNo'];
                $data[$i]['invoiceDate'] = $master_recode['creditNoteDate'];
                $data[$i]['GLAutoID'] = $master_recode['customerReceivableAutoID'];
                $data[$i]['systemGLCode'] = $master_recode['customerReceivableSystemGLCode'];
                $data[$i]['GLCode'] = $master_recode['customerReceivableGLAccount'];
                $data[$i]['GLDescription'] = $master_recode['customerReceivableDescription'];
                $data[$i]['GLType'] = $master_recode['customerReceivableType'];
                $data[$i]['description'] = $master_recode['comments'];

                $data[$i]['Invoice_amount'] = $transactionAmount[$i];
                $data[$i]['due_amount'] = $due_amount;
                $data[$i]['balance_amount'] = $balance_amount;

                //$data[$i]['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
                //$data[$i]['transactionCurrency'] = $master_recode['transactionCurrency'];
                //$data[$i]['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
                $data[$i]['transactionAmount'] = round($amount[$i],$master_recode['transactionCurrencyDecimalPlaces']);
                //$data[$i]['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
                //$data[$i]['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
                $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
                //$data[$i]['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
                //$data[$i]['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
                $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master_recode['companyReportingExchangeRate']);

                /*$data[$i]['partyCurrencyID'] = $master_recode['supplierCurrencyID'];
                $data[$i]['partyCurrency'] = $master_recode['supplierCurrency'];*/
                $data[$i]['customerCurrencyExchangeRate'] = $master_recode['customerCurrencyExchangeRate'];
                $data[$i]['customerAmount'] = ($data[$i]['transactionAmount'] / $master_recode['customerCurrencyExchangeRate']);

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
                $i++;
            }
        }


        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_customerreceiptdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Customer Invoice : Details Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Customer Invoice : ' . count($master_recode) . ' Item Details Saved Successfully . ');
                $this->db->trans_commit();
                return array('status' => true);
            }
        } else {
            return array('status' => false);
        }
    }

    function get_creditNote_master($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_creditnotemaster');
        $this->db->where('creditNoteMasterAutoID', $id);
        $output = $this->db->get()->row_array();
        return $output;
    }

    function get_debitNote_paymentVoucher_transactionAmount($creditNoteAutoID)
    {
        $sumTransactionAmount = $this->db->query("SELECT SUM(transactionAmount)AS totalTransactionAmount FROM srp_erp_customerreceiptdetail WHERE creditNoteAutoID = '" . $creditNoteAutoID . "'")->row('totalTransactionAmount');
        return $sumTransactionAmount;

    }

}