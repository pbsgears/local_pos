<?php

class Payment_voucher_model extends ERP_Model
{

    function save_paymentvoucher_header()
    {
        $this->db->trans_start();

        $date_format_policy = date_format_policy();
        $PaymentVoucherdate = $this->input->post('PVdate');
        $PVdate = input_format_date($PaymentVoucherdate, $date_format_policy);
        $PVcheqDate = $this->input->post('PVchequeDate');
        $PVchequeDate = input_format_date($PVcheqDate, $date_format_policy);
        $accountPayeeOnly = 0;
        if (!empty($this->input->post('accountPayeeOnly'))) {
            $accountPayeeOnly = 1;
        }
        //$period = explode('|', trim($this->input->post('financeyear_period')));
        $supplierdetails = explode('|', trim($this->input->post('SupplierDetails')));
        $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear')));
        $segment = explode('|', trim($this->input->post('segment')));
        $bank = explode('|', trim($this->input->post('bank')));
        $currency_code = explode('|', trim($this->input->post('currency_code')));

        $FYBegin = input_format_date($financeyr[0], $date_format_policy);
        $FYEnd = input_format_date($financeyr[1], $date_format_policy);

        $data['PVbankCode'] = trim($this->input->post('PVbankCode'));
        $bank_detail = fetch_gl_account_desc($data['PVbankCode']);
        $data['documentID'] = 'PV';
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear'));
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear'));
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period'));
        /*$data['FYPeriodDateFrom'] = trim($period[0]);
        $data['FYPeriodDateTo'] = trim($period[1]);*/
        $data['PVdate'] = trim($PVdate);
        $data['PVNarration'] = trim_desc($this->input->post('narration'));
        $data['accountPayeeOnly'] = $accountPayeeOnly;
        $data['segmentID'] = trim($segment[0]);
        $data['segmentCode'] = trim($segment[1]);
        $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
        $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
        $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
        $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
        $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
        $data['PVbank'] = $bank_detail['bankName'];
        $data['PVbankBranch'] = $bank_detail['bankBranch'];
        $data['PVbankSwiftCode'] = $bank_detail['bankSwiftCode'];
        $data['PVbankAccount'] = $bank_detail['bankAccountNumber'];
        $data['PVbankType'] = $bank_detail['subCategory'];
        $data['paymentType'] = $this->input->post('paymentType');
        $data['supplierBankMasterID'] = $this->input->post('supplierBankMasterID');
        if ($bank_detail['isCash'] == 1) {
            $data['PVchequeNo'] = null;
            $data['PVchequeDate'] = null;
        } else {
            if($this->input->post('paymentType')==2 && $this->input->post('vouchertype')=='Supplier'){
                $data['PVchequeNo'] = null;
                $data['PVchequeDate'] = null;
            }else{
                $data['PVchequeNo'] = trim($this->input->post('PVchequeNo'));
                $data['PVchequeDate'] = trim($PVchequeDate);
            }
        }
        $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);
        $data['pvType'] = trim($this->input->post('vouchertype'));
        $data['referenceNo'] = trim_desc($this->input->post('referenceno'));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        // $data['transactionCurrency'] = trim($this->input->post('transactionCurrency'));
        // $data['transactionExchangeRate'] = 1;
        // $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal($data['transactionCurrency']);
        // $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        // $default_currency = currency_conversion($data['transactionCurrency'], $data['companyLocalCurrency']);
        // $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        // $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        // $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        // $reporting_currency = currency_conversion($data['transactionCurrency'], $data['companyReportingCurrency']);
        // $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        // $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

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
        $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
        $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
        $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
        if ($data['pvType'] == 'Direct') {
            $data['partyType'] = 'DIR';
            $data['partyName'] = trim($this->input->post('partyName'));
            $data['partyCurrencyID'] = $data['companyLocalCurrencyID'];
            $data['partyCurrency'] = $data['companyLocalCurrency'];
            $data['partyExchangeRate'] = $data['transactionExchangeRate'];
            $data['partyCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
        } elseif ($data['pvType'] == 'Employee') {
            $emp_arr = $this->fetch_empyoyee($this->input->post('partyID'));
            $data['partyType'] = 'EMP';
            $data['partyID'] = trim($this->input->post('partyID'));
            $data['partyCode'] = $emp_arr['ECode'];
            $data['partyName'] = $emp_arr['Ename2'];
            $data['partyAddress'] = $emp_arr['EcAddress1'] . ' ' . $emp_arr['EcAddress2'] . ' ' . $emp_arr['EcAddress3'];
            $data['partyTelephone'] = $emp_arr['EpTelephone'];
            $data['partyFax'] = $emp_arr['EpFax'];
            $data['partyEmail'] = $emp_arr['EEmail'];
            $data['partyGLAutoID'] = '';
            $data['partyGLCode'] = '';
            $data['partyCurrencyID'] = $data['companyLocalCurrencyID'];
            $data['partyCurrency'] = $data['companyLocalCurrency'];
            $data['partyExchangeRate'] = $data['transactionExchangeRate'];
            $data['partyCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
        } elseif ($data['pvType'] == 'Supplier') {
            $supplier_arr = $this->fetch_supplier_data($this->input->post('partyID'));
            $data['partyType'] = 'SUP';
            $data['partyID'] = $this->input->post('partyID');
            $data['partyCode'] = $supplier_arr['supplierSystemCode'];
            $data['partyName'] = $supplier_arr['supplierName'];
            $data['partyAddress'] = $supplier_arr['supplierAddress1'] . ' ' . $supplier_arr['supplierAddress2'];
            $data['partyTelephone'] = $supplier_arr['supplierTelephone'];
            $data['partyFax'] = $supplier_arr['supplierFax'];
            $data['partyEmail'] = $supplier_arr['supplierEmail'];
            $data['partyGLAutoID'] = $supplier_arr['liabilityAutoID'];
            $data['partyGLCode'] = $supplier_arr['liabilitySystemGLCode'];
            $data['partyCurrencyID'] = $supplier_arr['supplierCurrencyID'];
            $data['partyCurrency'] = $supplier_arr['supplierCurrency'];
            $data['partyExchangeRate'] = $data['transactionExchangeRate'];
            $data['partyCurrencyDecimalPlaces'] = $supplier_arr['supplierCurrencyDecimalPlaces'];
        } elseif ($data['pvType'] == 'SC') {
            $sales_rep = $this->fetch_sales_rep_data($this->input->post('partyID'));
            $data['partyType'] = 'SC';
            $data['partyID'] = $this->input->post('partyID');
            $data['partyCode'] = $sales_rep['SalesPersonCode'];
            $data['partyName'] = $sales_rep['SalesPersonName'];
            $data['partyAddress'] = $sales_rep['SalesPersonAddress'];
            $data['partyTelephone'] = $sales_rep['contactNumber'];
            $data['partyEmail'] = $sales_rep['SalesPersonEmail'];
            $data['partyGLAutoID'] = $sales_rep['receivableAutoID'];
            $data['partyGLCode'] = $sales_rep['receivableSystemGLCode'];
            $data['partyCurrencyID'] = $sales_rep['salesPersonCurrencyID'];
            $data['partyCurrency'] = $sales_rep['salesPersonCurrency'];
            $data['partyExchangeRate'] = 0;
            $data['partyCurrencyDecimalPlaces'] = $sales_rep['salesPersonCurrencyDecimalPlaces'];
        }
        $partyCurrency = currency_conversionID($data['transactionCurrencyID'], $data['partyCurrencyID']);
        $data['partyExchangeRate'] = $partyCurrency['conversion'];
        $data['partyCurrencyDecimalPlaces'] = $partyCurrency['DecimalPlaces'];

        if (trim($this->input->post('PayVoucherAutoId'))) {
            $this->db->where('payVoucherAutoId', trim($this->input->post('PayVoucherAutoId')));
            $this->db->update('srp_erp_paymentvouchermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Payment Voucher Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Payment Voucher Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('PayVoucherAutoId'));
            }
        } else {
            $this->db->where('GLAutoID', $data['bankGLAutoID']);
            $this->db->update('srp_erp_chartofaccounts', array('bankCheckNumber' => $data['PVchequeNo']));
            //$this->load->library('sequence');
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $type = substr($data['pvType'], 0, 3);
            $data['PVcode'] = 0;
            $this->db->insert('srp_erp_paymentvouchermaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Payment Voucher   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Payment Voucher Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_payment_match_header()
    {
        $this->db->trans_start();

        $date_format_policy = date_format_policy();
        $matchingDate = $this->input->post('matchDate');
        $matchDate = input_format_date($matchingDate, $date_format_policy);

        $supplier_arr = $this->fetch_supplier_data($this->input->post('supplierID'));
        $currency_code = explode('|', trim($this->input->post('currency_code')));
        $data['documentID'] = 'PVM';
        $data['matchDate'] = trim($matchDate);
        $data['Narration'] = trim($this->input->post('Narration'));
        $data['refNo'] = trim($this->input->post('refNo'));
        $data['supplierID'] = $this->input->post('supplierID');
        $data['supplierSystemCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
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
        $data['supplierCurrency'] = $supplier_arr['supplierCurrency'];
        $data['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $data['supplierCurrencyDecimalPlaces'] = $supplier_arr['supplierCurrencyDecimalPlaces'];
        $supplierCurrency = currency_conversionID($data['transactionCurrencyID'], $data['supplierCurrencyID']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('matchID'))) {
            $this->db->where('matchID', trim($this->input->post('matchID')));
            $this->db->update('srp_erp_pvadvancematch', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Payment Match Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Payment Match Updated Successfully.');
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

            $this->db->insert('srp_erp_pvadvancematch', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Payment Match   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Payment Match Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_supplier_data($supplierID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID);
        return $this->db->get()->row_array();
    }

    function fetch_sales_rep_data($salesPersonID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_salespersonmaster');
        $this->db->where('salesPersonID', $salesPersonID);
        return $this->db->get()->row_array();
    }

    function fetch_empyoyee($id)
    {
        $this->db->select('Ename1,Ename2,Ename3,Ename4,ECode,EIdNo,EcAddress1,EcAddress2,EcAddress3,EpTelephone,EpFax,EEmail');
        $this->db->where('EIdNo', $id);
        $this->db->from('srp_employeesdetails');
        return $this->db->get()->row_array();
    }

    function fetch_supplier_inv($supplierID, $currencyID, $PVdate)
    {
        $PVdate = format_date($PVdate);
        $output = $this->db->query("SELECT srp_erp_paysupplierinvoicemaster.InvoiceAutoID,bookingInvCode,paymentTotalAmount,DebitNoteTotalAmount,advanceMatchedTotal,RefNo,((sid.transactionAmount * (100+IFNULL(tax.taxPercentage,0))) / 100 ) as transactionAmount FROM srp_erp_paysupplierinvoicemaster LEFT JOIN (SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount FROM srp_erp_paysupplierinvoicedetail GROUP BY invoiceAutoID) sid ON srp_erp_paysupplierinvoicemaster.invoiceAutoID = sid.invoiceAutoID
 LEFT JOIN (SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY invoiceAutoID) tax ON tax.invoiceAutoID = srp_erp_paysupplierinvoicemaster.invoiceAutoID WHERE confirmedYN = 1 AND approvedYN = 1 AND paymentInvoiceYN = 0 AND `supplierID` = '{$supplierID}' AND `transactionCurrencyID` = '{$currencyID}' AND `bookingDate` <= '{$PVdate}'")->result_array();
        return $output;
    }

    function fetch_debit_note($supplierID, $currencyID, $documentDate)
    {
        $tmpDate = format_date($documentDate);
        $output = $this->db->query("SELECT
                                    masterTbl.debitNoteMasterAutoID AS debitNoteMasterAutoID,
                                    masterTbl.debitNoteCode AS debitNoteCode,
                                    detailTbl.transactionAmount,
                                    masterTbl.docRefNo AS RefNo,
                                    SUM(pvDetail.transactionAmount) AS PVTransactionAmount
                                    FROM
                                        srp_erp_debitnotemaster masterTbl
                                    LEFT JOIN (
                                        SELECT
                                            SUM(transactionAmount) AS transactionAmount,
                                            debitNoteMasterAutoID
                                        FROM
                                            srp_erp_debitnotedetail
                                        WHERE
                                            (ISNULL(InvoiceAutoID) OR InvoiceAutoID = 0)
                                        GROUP BY
                                            debitNoteMasterAutoID
                                    ) detailTbl ON detailTbl.debitNoteMasterAutoID = masterTbl.debitNoteMasterAutoID
                                    LEFT JOIN srp_erp_paymentvoucherdetail AS pvDetail ON pvDetail.debitNoteAutoID = masterTbl.debitNoteMasterAutoID
                                    WHERE
                                        masterTbl.confirmedYN = 1
                                    AND masterTbl.approvedYN = 1
                                    AND masterTbl.transactionCurrencyID = '" . $currencyID . "'
                                    AND masterTbl.debitNoteDate <= '" . $tmpDate . "'
                                    AND masterTbl.supplierID = '" . $supplierID . "'
                                    GROUP BY
                                        masterTbl.debitNoteMasterAutoID")->result_array();
        //echo $this->db->last_query();
        return $output;

    }

    function fetch_supplier_po($supplierID, $currencyID, $PVdate)
    {
        $date_format_policy = date_format_policy();
        $format_PVdate = input_format_date($PVdate, $date_format_policy);
        $output = $this->db->query("SELECT purchaseOrderID,purchaseOrderCode,transactionAmount,narration,referenceNumber,expectedDeliveryDate FROM srp_erp_purchaseordermaster WHERE confirmedYN = 1 AND approvedYN = 1 AND supplierID = $supplierID AND transactionCurrencyID = $currencyID AND documentDate <= '" . $format_PVdate . "'")->result_array();
        return $output;
    }

    function save_inv_base_items()
    {
        $this->db->trans_start();
        $InvoiceAutoID = $this->input->post('InvoiceAutoID');
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrency, supplierCurrencyExchangeRate,srp_erp_paysupplierinvoicemaster.InvoiceAutoID, DebitNoteTotalAmount,supplierliabilityAutoID, supplierliabilitySystemGLCode, supplierliabilityGLAccount,companyReportingCurrency, supplierliabilityDescription , supplierliabilityType,transactionCurrencyID , companyLocalCurrencyID, transactionCurrency,transactionExchangeRate, companyLocalCurrency, bookingInvCode,RefNo,bookingDate,comments,((sid.transactionAmount * (100+IFNULL(tax.taxPercentage,0))) / 100 ) as transactionAmount,paymentTotalAmount,DebitNoteTotalAmount,advanceMatchedTotal,companyReportingCurrencyID,supplierCurrencyID');
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $this->db->join('(SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount FROM srp_erp_paysupplierinvoicedetail GROUP BY invoiceAutoID) sid','srp_erp_paysupplierinvoicemaster.invoiceAutoID = sid.invoiceAutoID','left');
        $this->db->join('(SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY invoiceAutoID) tax','tax.invoiceAutoID = srp_erp_paysupplierinvoicemaster.invoiceAutoID','left');
        $this->db->where_in('srp_erp_paysupplierinvoicemaster.InvoiceAutoID', $this->input->post('InvoiceAutoID'));
        $master_recode = $this->db->get()->result_array();
        $amount = $this->input->post('amount');
        for ($i = 0; $i < count($master_recode); $i++) {
            $due_amount = ($master_recode[$i]['transactionAmount'] - ($master_recode[$i]['paymentTotalAmount'] + $master_recode[$i]['DebitNoteTotalAmount'] + $master_recode[$i]['advanceMatchedTotal']));
            $data[$i]['payVoucherAutoId'] = $this->input->post('payVoucherAutoId');
            $data[$i]['InvoiceAutoID'] = $master_recode[$i]['InvoiceAutoID'];
            $data[$i]['type'] = 'Invoice';
            $data[$i]['bookingInvCode'] = $master_recode[$i]['bookingInvCode'];
            $data[$i]['referenceNo'] = $master_recode[$i]['RefNo'];
            $data[$i]['bookingDate'] = $master_recode[$i]['bookingDate'];
            $data[$i]['GLAutoID'] = $master_recode[$i]['supplierliabilityAutoID'];
            $data[$i]['systemGLCode'] = $master_recode[$i]['supplierliabilitySystemGLCode'];
            $data[$i]['GLCode'] = $master_recode[$i]['supplierliabilityGLAccount'];
            $data[$i]['GLDescription'] = $master_recode[$i]['supplierliabilityDescription'];
            $data[$i]['GLType'] = $master_recode[$i]['supplierliabilityType'];
            $data[$i]['description'] = $master_recode[$i]['comments'];
            $data[$i]['Invoice_amount'] = $master_recode[$i]['transactionAmount'];
            $data[$i]['due_amount'] = $due_amount;
            $data[$i]['balance_amount'] = ($data[$i]['due_amount'] - (float)$amount[$i]);
            $data[$i]['transactionCurrencyID'] = $master_recode[$i]['transactionCurrencyID'];
            $data[$i]['transactionCurrency'] = $master_recode[$i]['transactionCurrency'];
            $data[$i]['transactionExchangeRate'] = $master_recode[$i]['transactionExchangeRate'];
            $data[$i]['transactionAmount'] = (float)$amount[$i];
            $data[$i]['companyLocalCurrencyID'] = $master_recode[$i]['companyLocalCurrencyID'];
            $data[$i]['companyLocalCurrency'] = $master_recode[$i]['companyLocalCurrency'];
            $data[$i]['companyLocalExchangeRate'] = $master_recode[$i]['companyLocalExchangeRate'];
            $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['companyLocalExchangeRate']);
            $data[$i]['companyReportingCurrencyID'] = $master_recode[$i]['companyReportingCurrencyID'];
            $data[$i]['companyReportingCurrency'] = $master_recode[$i]['companyReportingCurrency'];
            $data[$i]['companyReportingExchangeRate'] = $master_recode[$i]['companyReportingExchangeRate'];
            $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['companyReportingExchangeRate']);
            $data[$i]['partyCurrencyID'] = $master_recode[$i]['supplierCurrencyID'];
            $data[$i]['partyCurrency'] = $master_recode[$i]['supplierCurrency'];
            $data[$i]['partyExchangeRate'] = $master_recode[$i]['supplierCurrencyExchangeRate'];
            $data[$i]['partyAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['supplierCurrencyExchangeRate']);
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

            $grv_m[$i]['InvoiceAutoID'] = $InvoiceAutoID[$i];
            $grv_m[$i]['paymentTotalAmount'] = ($master_recode[$i]['paymentTotalAmount'] + $amount[$i]);
            $grv_m[$i]['paymentInvoiceYN'] = 0;
            if ($data[$i]['balance_amount'] <= 0) {
                $grv_m[$i]['paymentInvoiceYN'] = 1;
            }
        }

        if (!empty($data)) {
            $this->db->update_batch('srp_erp_paysupplierinvoicemaster', $grv_m, 'InvoiceAutoID');
            $this->db->insert_batch('srp_erp_paymentvoucherdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Supplier Invoice : Details Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Supplier Invoice : ' . count($master_recode) . ' Item Details Saved Successfully . ');
                $this->db->trans_commit();
                return array('status' => true);
            }
        } else {
            return array('status' => false);
        }
    }

    function get_debitNote_master($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_debitnotemaster');
        $this->db->where('debitNoteMasterAutoID', $id);
        $output = $this->db->get()->row_array();
        return $output;
    }

    function get_debitNote_paymentVoucher_transactionAmount($debitNoteAutoID)
    {
        $sumTransactionAmount = $this->db->query("SELECT SUM(transactionAmount)AS totalTransactionAmount FROM srp_erp_paymentvoucherdetail WHERE debitNoteAutoID = '" . $debitNoteAutoID . "'")->row('totalTransactionAmount');
        return $sumTransactionAmount;

    }

    function save_debitNote_base_items()
    {
        $this->db->trans_start();
        $payVoucherAutoId = $this->input->post('payVoucherAutoId');

        /** Array */
        $debitNoteMasterIDs = $this->input->post('debitNoteMasterID');
        $amount = $this->input->post('amount');
        $transactionAmount = $this->input->post('transactionAmount');


        if (!empty($debitNoteMasterIDs)) {
            $i = 0;
            foreach ($debitNoteMasterIDs as $debitNoteMasterID) {
                $master_recode = $this->get_debitNote_master($debitNoteMasterID);
                $alreadyPaidAmount = $this->get_debitNote_paymentVoucher_transactionAmount($debitNoteMasterID); // use this value to get due amount
                $due_amount = $transactionAmount[$i] - $alreadyPaidAmount;
                $balance_amount = $due_amount - $amount[$i];


                $data[$i]['debitNoteAutoID'] = $debitNoteMasterIDs[$i];
                $data[$i]['InvoiceAutoID'] = null;
                $data[$i]['type'] = 'debitnote';
                $data[$i]['payVoucherAutoId'] = $payVoucherAutoId;
                $data[$i]['bookingInvCode'] = $master_recode['debitNoteCode'];
                $data[$i]['referenceNo'] = $master_recode['docRefNo'];
                $data[$i]['bookingDate'] = $master_recode['debitNoteDate'];
                $data[$i]['GLAutoID'] = $master_recode['supplierliabilityAutoID'];
                $data[$i]['systemGLCode'] = $master_recode['supplierliabilitySystemGLCode'];
                $data[$i]['GLCode'] = $master_recode['supplierliabilityGLAccount'];
                $data[$i]['GLDescription'] = $master_recode['supplierliabilityDescription'];
                $data[$i]['GLType'] = $master_recode['supplierliabilityType'];
                $data[$i]['description'] = $master_recode['comments'];

                $data[$i]['Invoice_amount'] = $transactionAmount[$i];
                $data[$i]['due_amount'] = $due_amount;
                $data[$i]['balance_amount'] = $balance_amount;

                $data[$i]['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
                $data[$i]['transactionCurrency'] = $master_recode['transactionCurrency'];
                $data[$i]['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
                $data[$i]['transactionAmount'] = (float)$amount[$i];
                $data[$i]['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
                $data[$i]['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
                $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
                $data[$i]['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
                $data[$i]['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
                $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
                $data[$i]['partyCurrencyID'] = $master_recode['supplierCurrencyID'];
                $data[$i]['partyCurrency'] = $master_recode['supplierCurrency'];
                $data[$i]['partyExchangeRate'] = $master_recode['supplierCurrencyExchangeRate'];
                $data[$i]['partyAmount'] = ($data[$i]['transactionAmount'] / $master_recode['supplierCurrencyExchangeRate']);

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
            $this->db->insert_batch('srp_erp_paymentvoucherdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Supplier Invoice : Details Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Supplier Invoice : ' . count($master_recode) . ' Item Details Saved Successfully . ');
                $this->db->trans_commit();
                return array('status' => true);
            }
        } else {
            return array('status' => false);
        }
    }

    function delete_payment_voucher()
    {
        $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
        $data = $this->db->get('srp_erp_paymentvoucherdetail')->result_array();

        $this->db->select('PVcode');
        $this->db->from('srp_erp_paymentVouchermaster');
        $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId')));
        $master = $this->db->get()->row_array();

        if ($data) {
            $this->session->set_flashdata('e', 'Delete details first.');
            return true;
        } else {
            if($master['PVcode']=="0"){
                $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
                $results = $this->db->delete('srp_erp_paymentVouchermaster');
                if ($results) {
                    $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
                    $this->db->delete('srp_erp_paymentvoucherdetail');
                    $this->session->set_flashdata('s', 'Deleted Successfully');
                    return true;
                }
            }else{
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId')));
                $this->db->update('srp_erp_paymentVouchermaster', $data);
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            }

        }
        /*$this->db->delete('srp_erp_paymentVouchermaster', array('payVoucherAutoId' => trim($this->input->post('payVoucherAutoId'))));
        $this->db->delete('srp_erp_paymentvoucherdetail', array('payVoucherAutoId' => trim($this->input->post('payVoucherAutoId'))));*/

    }

    function delete_payment_match()
    {
        /*$this->db->where('matchID', $this->input->post('matchID'));
        $data = $this->db->get('srp_erp_pvadvancematchdetails')->result_array();
        foreach ($data as $val_as) {
            $id = $val_as['InvoiceAutoID'];
            $amo = $val_as['transactionAmount'];
            $this->db->query("UPDATE srp_erp_paysupplierinvoicemaster SET advanceMatchedTotal = (advanceMatchedTotal-{$amo}) WHERE InvoiceAutoID='{
        $id}'");
        }
        $this->db->delete('srp_erp_pvadvancematch', array('matchID' => trim($this->input->post('matchID'))));
        $this->db->delete('srp_erp_pvadvancematchdetails', array('matchID' => trim($this->input->post('matchID'))));
        return true;*/
        $this->db->select('*');
        $this->db->from('srp_erp_pvadvancematchdetails');
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
            $this->db->update('srp_erp_pvadvancematch', $data);
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;
        }

    }

    function delete_pv_match_detail()
    {
        $this->db->select('InvoiceAutoID,transactionAmount');
        $this->db->where('matchDetailID', $this->input->post('matchDetailID'));
        $data = $this->db->get('srp_erp_pvadvancematchdetails')->row_array();
        $id = $data['InvoiceAutoID'];
        $amo = $data['transactionAmount'];
        $this->db->query("UPDATE srp_erp_paysupplierinvoicemaster SET advanceMatchedTotal = (advanceMatchedTotal-{$amo}),paymentInvoiceYN =0 WHERE InvoiceAutoID=$id");

        $this->db->where('matchDetailID', $this->input->post('matchDetailID'));
        $results = $this->db->delete('srp_erp_pvadvancematchdetails');
        $this->session->set_flashdata('s', 'Payment Matching Detail Deleted Successfully');
        return true;
    }

    function delete_item_direct()
    {
        $this->db->select('srp_erp_paymentvoucherdetail.*,srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID', 'left');
        $this->db->where('srp_erp_paymentvoucherdetail.payVoucherDetailAutoID', trim($this->input->post('payVoucherDetailAutoID')));
        $detail_arr = $this->db->get()->row_array();

        /** delete sub item in PV*/
        if ($detail_arr['isSubitemExist'] == 1) {
            $this->db->where('receivedDocumentID', 'PV');
            $this->db->where('receivedDocumentAutoID', $detail_arr['payVoucherAutoId']);
            $this->db->where('receivedDocumentDetailID', $detail_arr['payVoucherDetailAutoID']);
            $this->db->delete('srp_erp_itemmaster_subtemp');


        }
        /**end  delete sub item in PV*/

        if ($detail_arr['type'] == 'Invoice') {
            $company_id = $this->common_data['company_data']['company_id'];
            $match_id = $detail_arr['InvoiceAutoID'];
            $number = $detail_arr['transactionAmount'];
            $status = 0;
            $this->db->query("UPDATE srp_erp_paysupplierinvoicemaster SET paymentTotalAmount = (paymentTotalAmount -{$number})  , paymentInvoiceYN = {$status}  WHERE InvoiceAutoID=
        $match_id and companyID=
        $company_id");
            //echo $this->db->last_query();
        }
        $this->db->where('payVoucherDetailAutoID', trim($this->input->post('payVoucherDetailAutoID')));
        $results = $this->db->delete('srp_erp_paymentvoucherdetail');


        if ($results) {
            $this->session->set_flashdata('s', 'Payment Voucher Detail Deleted Successfully');
            return true;
        }
    }

    function save_sales_rep_payment()
    {
        // if (!empty($this->input->post('payVoucherDetailAutoID'))) {
        //     $this->db->select('itemDescription,itemSystemCode');
        //     $this->db->from('srp_erp_paymentvoucherdetail');
        //     $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId')));
        //     $this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
        //     $this->db->where('payVoucherDetailAutoID != ', trim($this->input->post('payVoucherDetailAutoID')));
        //     $order_detail = $this->db->get()->row_array();
        //     if (!empty($order_detail)) {
        //         return array('w', 'Payment Voucher Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists . ');
        //     }
        // }

        $this->db->select(' * ');
        $this->db->where('salesPersonID', trim($this->input->post('salesPersonID')));
        $salesperson = $this->db->get('srp_erp_salespersonmaster')->row_array();

        $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate, companyLocalCurrency, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate ,partyCurrency,partyExchangeRate,companyReportingCurrencyID,partyCurrencyID,segmentCode,segmentID,companyLocalCurrencyID');
        $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId')));
        $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();
        $this->db->trans_start();
        $data['type'] = 'SC';
        $data['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId'));
        $data['description'] = trim($this->input->post('description'));
        $data['salesPersonID'] = trim($this->input->post('salesPersonID'));
        // $data['itemSystemCode'] = $item_arr['itemSystemCode'];
        // $data['itemDescription'] = $item_arr['itemDescription'];
        // $data['unitOfMeasure'] = trim($uom[0]);
        // $data['unitOfMeasureID'] = trim($this->input->post('UnitOfMeasureID'));
        // $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        // $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
        // $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        // $data['requestedQty'] = trim($this->input->post('quantityRequested'));
        // $data['unittransactionAmount'] = trim($this->input->post('estimatedAmount'));
        $data['segmentID'] = $salesperson['segmentID'];
        $data['segmentCode'] = $salesperson['segmentCode'];
        $data['transactionCurrencyID'] = $salesperson['salesPersonCurrencyID'];
        $data['transactionCurrency'] = $salesperson['salesPersonCurrency'];
        $data['transactionExchangeRate'] = 1;//$salesperson['transactionExchangeRate'];
        $data['transactionAmount'] = trim($this->input->post('transactionAmount'));
        $data['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
        $data['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
        $data['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
        $data['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
        $data['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
        $data['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
        $data['partyCurrency'] = $master_recode['partyCurrency'];
        $data['partyCurrencyID'] = $master_recode['partyCurrencyID'];
        $data['partyExchangeRate'] = $master_recode['partyExchangeRate'];
        $data['partyAmount'] = ($data['transactionAmount'] / $master_recode['partyExchangeRate']);
        // $data['comment'] = trim($this->input->post('comment'));
        // $data['remarks'] = trim($this->input->post('remarks'));
        $data['wareHouseAutoID'] = $salesperson['wareHouseAutoID'];
        $data['wareHouseCode'] = $salesperson['wareHouseCode'];
        $data['wareHouseLocation'] = $salesperson['wareHouseLocation'];
        $data['wareHouseDescription'] = $salesperson['wareHouseDescription'];
        $data['GLAutoID'] = $salesperson['receivableAutoID'];
        $data['systemGLCode'] = $salesperson['receivableSystemGLCode'];
        $data['GLCode'] = $salesperson['receivableGLAccount'];
        $data['GLDescription'] = $salesperson['receivableDescription'];
        $data['GLType'] = $salesperson['receivableType'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('payVoucherDetailAutoID'))) {
            $this->db->where('payVoucherDetailAutoID', trim($this->input->post('payVoucherDetailAutoID')));
            $this->db->update('srp_erp_paymentvoucherdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => 0, 'type' => 'e', 'data' => 'Payment Voucher Detail : ' . $salesperson['SalesPersonName'] . ' Update Failed ');
            } else {
                $this->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'data' => 'Payment Voucher Detail : ' . $salesperson['SalesPersonName'] . ' Updated Successfully . ');
            }
        } else {
            $this->db->delete('srp_erp_paymentvoucherdetail', array('payVoucherAutoId' => trim($this->input->post('payVoucherAutoId'))));
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_paymentvoucherdetail', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => 0, 'type' => 'e', 'data' => 'Payment Voucher Detail : ' . $salesperson['SalesPersonName'] . ' Update Failed ');
            } else {
                $this->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'data' => 'Payment Voucher Detail : ' . $salesperson['SalesPersonName'] . ' Saved Successfully . ');
            }
        }
    }

    function save_pv_item_detail()
    {
        $projectExist = project_is_exist();
        $payVoucherDetailAutoID = trim($this->input->post('payVoucherDetailAutoID'));
        if (!empty($payVoucherDetailAutoID)) {
            $this->db->select('itemDescription,itemSystemCode');
            $this->db->from('srp_erp_paymentvoucherdetail');
            $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId')));
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
            $this->db->where('payVoucherDetailAutoID != ', $payVoucherDetailAutoID);
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('w', 'Payment Voucher Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists . ');
            }
        }

        $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate, companyLocalCurrency, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate ,partyCurrency,partyExchangeRate,companyReportingCurrencyID,partyCurrencyID,segmentCode,segmentID,companyLocalCurrencyID');
        $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId')));
        $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();
        $this->db->trans_start();
        $wareHouse_location = explode(' | ', trim($this->input->post('wareHouse')));
        $uom = explode(' | ', $this->input->post('uom'));
        $item_arr = fetch_item_data(trim($this->input->post('itemAutoID')));
        $data['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId'));
        $data['itemAutoID'] = trim($this->input->post('itemAutoID'));
        $data['projectID'] = trim($this->input->post('projectID'));
        if ($projectExist == 1) {
            $projectID = trim($this->input->post('projectID'));
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master_recode['transactionCurrencyID'], $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['itemSystemCode'] = $item_arr['itemSystemCode'];
        $data['itemDescription'] = $item_arr['itemDescription'];
        $data['unitOfMeasure'] = trim($uom[0]);
        $data['unitOfMeasureID'] = trim($this->input->post('UnitOfMeasureID'));
        $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['requestedQty'] = trim($this->input->post('quantityRequested'));
        $data['unittransactionAmount'] = trim($this->input->post('estimatedAmount'));
        $data['segmentID'] = $master_recode['segmentID'];
        $data['segmentCode'] = $master_recode['segmentCode'];
        $data['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
        $data['transactionCurrency'] = $master_recode['transactionCurrency'];
        $data['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
        $data['transactionAmount'] = ($data['unittransactionAmount'] * $data['requestedQty']);
        $data['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
        $data['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
        $data['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
        $data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $master_recode['companyLocalExchangeRate']);
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
        $data['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
        $data['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
        $data['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
        $data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $master_recode['companyReportingExchangeRate']);
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
        $data['partyCurrency'] = $master_recode['partyCurrency'];
        $data['partyCurrencyID'] = $master_recode['partyCurrencyID'];
        $data['partyExchangeRate'] = $master_recode['partyExchangeRate'];
        $data['unitpartyAmount'] = ($data['unittransactionAmount'] / $master_recode['partyExchangeRate']);
        $data['partyAmount'] = ($data['transactionAmount'] / $master_recode['partyExchangeRate']);
        $data['comment'] = trim($this->input->post('comment'));
        $data['remarks'] = trim($this->input->post('remarks'));
        $data['type'] = 'Item';
        $data['wareHouseAutoID'] = trim($this->input->post('wareHouseAutoID'));
        $data['wareHouseCode'] = trim($wareHouse_location[0]);
        $data['wareHouseLocation'] = trim($wareHouse_location[1]);
        $data['wareHouseDescription'] = trim($wareHouse_location[2]);
        $item_data = fetch_item_data($data['itemAutoID']);
        if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Fixed Assets') {
            $data['GLAutoID'] = $item_data['assteGLAutoID'];
            $data['systemGLCode'] = $item_data['assteSystemGLCode'];
            $data['GLCode'] = $item_data['assteGLCode'];
            $data['GLDescription'] = $item_data['assteDescription'];
            $data['GLType'] = $item_data['assteType'];
        } else {
            $data['GLAutoID'] = $item_data['costGLAutoID'];
            $data['systemGLCode'] = $item_data['costSystemGLCode'];
            $data['GLCode'] = $item_data['costGLCode'];
            $data['GLDescription'] = $item_data['costDescription'];
            $data['GLType'] = $item_data['costType'];
        }
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];


        if ($payVoucherDetailAutoID) {
            /*echo 'payVoucherDetailAutoID: '.$payVoucherDetailAutoID;
            exit;*/

            /** update sub item master */
            $subData['uom'] = $data['unitOfMeasure'];
            $subData['uomID'] = $data['unitOfMeasureID'];
            $subData['payVoucherDetailAutoID'] = $payVoucherDetailAutoID;


            $this->edit_sub_itemMaster_tmpTbl($this->input->post('quantityRequested'), $item_data['itemAutoID'], $data['payVoucherAutoId'], $payVoucherDetailAutoID, 'PV', $data['itemSystemCode'], $subData);

            $this->db->where('payVoucherDetailAutoID', $payVoucherDetailAutoID);
            $this->db->update('srp_erp_paymentvoucherdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Payment Voucher Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Payment Voucher Detail : ' . $data['itemSystemCode'] . ' Updated Successfully . ');

                //return array('status' => true, 'last_id' => $this->input->post('purchaseOrderDetailsID'));
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_paymentvoucherdetail', $data);
            $last_id = $this->db->insert_id();

            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
            $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();
            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $data['wareHouseAutoID'],
                    'wareHouseLocation' => $data['wareHouseLocation'],
                    'wareHouseDescription' => $data['wareHouseDescription'],
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


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Payment Voucher Detail : ' . $data['itemSystemCode'] . ' Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Payment Voucher Detail : ' . $data['itemSystemCode'] . ' Saved Successfully . ');
            }
        }
    }

    function save_pv_item_detail_multiple()
    {
        $projectExist = project_is_exist();
        $payVoucherDetailAutoID = $this->input->post('payVoucherDetailAutoID');
        $payVoucherAutoId = $this->input->post('payVoucherAutoId');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $wareHouse = $this->input->post('wareHouse');
        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $comment = $this->input->post('comment');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $projectID = $this->input->post('projectID');

        $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate, companyLocalCurrency, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate ,partyCurrency,partyExchangeRate,companyReportingCurrencyID,partyCurrencyID,segmentCode,segmentID,companyLocalCurrencyID');
        $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId')));
        $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();

        $ACA_ID = $this->common_data['controlaccounts']['ACA'];
        $ACA = fetch_gl_account_desc($ACA_ID);

        $this->db->trans_start();
        foreach ($itemAutoIDs as $key => $itemAutoID) {
            if (!trim($this->input->post('payVoucherDetailAutoID'))) {
                $this->db->select('itemDescription,itemSystemCode');
                $this->db->from('srp_erp_paymentvoucherdetail');
                $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId')));
                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Payment Voucher Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists . ');
                }

                $wareHouse_location = explode(' | ', $wareHouse[$key]);
                $item_arr = fetch_item_data($itemAutoID);
                $uomEx = explode(' | ', $uom[$key]);

                $data['payVoucherAutoId'] = trim($payVoucherAutoId);
                $data['itemAutoID'] = $itemAutoID;
                $data['itemSystemCode'] = $item_arr['itemSystemCode'];
                $data['itemDescription'] = $item_arr['itemDescription'];
                if ($projectExist == 1) {
                    $projectCurrency = project_currency($projectID[$key]);
                    $projectCurrencyExchangerate = currency_conversionID($master_recode['transactionCurrencyID'], $projectCurrency);
                    $data['projectID'] = $projectID[$key];
                    $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                }
                $data['unitOfMeasure'] = trim($uomEx[0]);
                $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
                $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
                $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
                $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
                $data['requestedQty'] = $quantityRequested[$key];
                $data['unittransactionAmount'] = $estimatedAmount[$key];
                $data['segmentID'] = $master_recode['segmentID'];
                $data['segmentCode'] = $master_recode['segmentCode'];
                $data['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
                $data['transactionCurrency'] = $master_recode['transactionCurrency'];
                $data['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
                $data['transactionAmount'] = ($data['unittransactionAmount'] * $data['requestedQty']);
                $data['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
                $data['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
                $data['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
                $data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $master_recode['companyLocalExchangeRate']);
                $data['companyLocalAmount'] = ($data['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
                $data['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
                $data['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
                $data['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
                $data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $master_recode['companyReportingExchangeRate']);
                $data['companyReportingAmount'] = ($data['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
                $data['partyCurrency'] = $master_recode['partyCurrency'];
                $data['partyCurrencyID'] = $master_recode['partyCurrencyID'];
                $data['partyExchangeRate'] = $master_recode['partyExchangeRate'];
                $data['unitpartyAmount'] = ($data['unittransactionAmount'] / $master_recode['partyExchangeRate']);
                $data['partyAmount'] = ($data['transactionAmount'] / $master_recode['partyExchangeRate']);
                $data['comment'] = $comment[$key];
                $data['remarks'] = '';
                $data['type'] = 'Item';
                $data['wareHouseAutoID'] = $wareHouseAutoID[$key];
                $data['wareHouseCode'] = trim($wareHouse_location[0]);
                $data['wareHouseLocation'] = trim($wareHouse_location[1]);
                $data['wareHouseDescription'] = trim($wareHouse_location[2]);
                $item_data = fetch_item_data($data['itemAutoID']);
                if ($item_data['mainCategory'] == 'Inventory') {
                    $data['GLAutoID'] = $item_data['assteGLAutoID'];
                    $data['systemGLCode'] = $item_data['assteSystemGLCode'];
                    $data['GLCode'] = $item_data['assteGLCode'];
                    $data['GLDescription'] = $item_data['assteDescription'];
                    $data['GLType'] = $item_data['assteType'];
                } else if ($item_data['mainCategory'] == 'Fixed Assets') {
                    $data['GLAutoID'] = $ACA_ID;
                    $data['systemGLCode'] = $ACA['systemAccountCode'];
                    $data['GLCode'] = $ACA['GLSecondaryCode'];
                    $data['GLDescription'] = $ACA['GLDescription'];
                    $data['GLType'] = $ACA['subCategory'];
                } else {
                    $data['GLAutoID'] = $item_data['costGLAutoID'];
                    $data['systemGLCode'] = $item_data['costSystemGLCode'];
                    $data['GLCode'] = $item_data['costGLCode'];
                    $data['GLDescription'] = $item_data['costDescription'];
                    $data['GLType'] = $item_data['costType'];
                }
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];

                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_paymentvoucherdetail', $data);
                $last_id = $this->db->insert_id();

                /** add sub item config*/
                if ($item_data['isSubitemExist'] == 1) {

                    $qty = 0;
                    if (!empty($itemAutoIDs)) {
                        $x = 0;
                        foreach ($itemAutoIDs as $key => $itemAutoIDTmp) {
                            if ($itemAutoIDTmp == $itemAutoID) {
                                $qty = $quantityRequested[$key];
                                $warehouseID = $wareHouseAutoID[$x];
                            }
                            $x++;
                        }
                    }

                    $subData['uom'] = $data['unitOfMeasure'];
                    $subData['uomID'] = $data['unitOfMeasureID'];
                    $subData['pv_detailID'] = $last_id;
                    $this->add_sub_itemMaster_tmpTbl($qty, $itemAutoID, $payVoucherAutoId, $last_id, 'PV', $item_data['itemSystemCode'], $subData, $warehouseID);


                }

                /** End add sub item config*/

                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();
                if (empty($warehouseitems)) {
                    $data_arr = array(
                        'wareHouseAutoID' => $data['wareHouseAutoID'],
                        'wareHouseLocation' => $data['wareHouseLocation'],
                        'wareHouseDescription' => $data['wareHouseDescription'],
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
            }

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Payment Voucher Detail : Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('e', 'Payment Voucher Details :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Payment Voucher Details : Saved Successfully . ');
        }

    }

    function fetch_payment_voucher_template_data($payVoucherAutoId)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select(' *,DATE_FORMAT(PVdate, \'' . $convertFormat . '\') AS PVdate,DATE_FORMAT(PVchequeDate,\'' . $convertFormat . '\') AS PVchequeDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,srp_erp_suppliermaster.nameOnCheque as nameOnCheque,srp_erp_suppliermaster.supplierName as supname,srp_erp_suppliermaster.supplierSystemCode as supsyscode, srp_erp_suppliermaster.supplierAddress1 as supaddress1, srp_erp_suppliermaster.supplierTelephone as suptel,srp_erp_suppliermaster.supplierFax as supfax, case pvType when \'Direct\' then partyName when \'Employee\' then srp_employeesdetails.Ename2 when \'Supplier\' then srp_erp_suppliermaster.supplierName end as partyName,
        
        case pvType when \'Direct\' then " " when \'Employee\' then CONCAT_WS(\', \',
       IF(LENGTH(srp_employeesdetails.EpAddress1),srp_employeesdetails.EpAddress1,NULL),
       IF(LENGTH(srp_employeesdetails.EpAddress2),srp_employeesdetails.EpAddress2,NULL),
       IF(LENGTH(srp_employeesdetails.EpAddress3),srp_employeesdetails.EpAddress3,NULL)
    
	
) when \'Supplier\' then srp_erp_suppliermaster.supplierAddress1  end as partyAddresss,
        
        case pvType when \'Direct\' then " " when \'Employee\' then CONCAT_WS(" / ", srp_employeesdetails.EpTelephone ,srp_employeesdetails.EcFax) when \'Supplier\' then CONCAT_WS(" / ", srp_erp_suppliermaster.supplierTelephone ,srp_erp_suppliermaster.supplierFax) end as parttelfax
        ');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_paymentvouchermaster.partyID = srp_erp_suppliermaster.supplierAutoID', 'Left');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_paymentVouchermaster.partyID', 'Left');
        $this->db->from('srp_erp_paymentvouchermaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);
        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Item');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['item_detail'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'GL');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['gl_detail'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Invoice');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['invoice'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'debitnote');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['debitnote'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Advance');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['advance'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'SC');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['sales_commission'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchertaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();
        return $data;
    }

    function fetch_payment_voucher_match_template_data($matchID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(matchDate,\'' . $convertFormat . '\') AS matchDate, DATE_FORMAT(confirmedDate,\'' . $convertFormat . ' %h:%i:%s\') AS confirmedDate');
        $this->db->where('matchID', $matchID);
        $this->db->from('srp_erp_pvadvancematch');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);
        $this->db->select('*,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,DATE_FORMAT(bookingDate,\'' . $convertFormat . '\') AS bookingDate');
        $this->db->where('matchID', $matchID);
        $this->db->from('srp_erp_pvadvancematchdetails');
        $data['detail'] = $this->db->get()->result_array();
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

    function load_payment_voucher_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,DATE_FORMAT(PVchequeDate,\'' . $convertFormat . '\') AS PVchequeDate');
        $this->db->where('payVoucherAutoId', $this->input->post('PayVoucherAutoId'));
        return $this->db->get('srp_erp_paymentvouchermaster')->row_array();
    }

    function load_payment_match_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(matchDate,\'' . $convertFormat . '\') AS matchDate');
        $this->db->where('matchID', $this->input->post('matchID'));
        return $this->db->get('srp_erp_pvadvancematch')->row_array();
    }

    function fetch_match_detail()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(srp_erp_pvadvancematchdetails.PVdate,\'' . $convertFormat . '\') AS PVdate,DATE_FORMAT(srp_erp_pvadvancematchdetails.bookingDate,\'' . $convertFormat . '\') AS bookingDate');
        $this->db->where('matchID', $this->input->post('matchID'));
        return $this->db->get('srp_erp_pvadvancematchdetails')->result_array();
    }

    function fetch_pv_direct_details()
    {
        $payVoucherAutoId = trim($this->input->post('payVoucherAutoId'));
        $this->db->select('transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,partyCurrencyDecimalPlaces');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $data['currency'] = $this->db->get('srp_erp_paymentvouchermaster')->row_array();
        $this->db->select('srp_erp_paymentvoucherdetail.*,srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID', 'left');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $data['detail'] = $this->db->get()->result_array();
        //echo $this->db->last_query();
        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchertaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();
        return $data;
    }

    function fetch_detail()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->where('payVoucherAutoId', trim($this->input->post('PayVoucherAutoId')));
        return $this->db->get()->result_array();
    }

    function save_direct_pv_detail()
    {
        $projectExist = project_is_exist();
        $this->db->trans_start();
        $this->db->select('transactionCurrency, transactionExchangeRate, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate ,partyCurrency,partyExchangeRate,transactionCurrencyID');
        $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
        $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();
        $segment = explode('|', trim($this->input->post('segment_gl')));
        $gl_code = explode('|', trim($this->input->post('gl_code_des')));
        $data['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId'));
        $data['GLAutoID'] = trim($this->input->post('gl_code'));
        if ($projectExist == 1) {
            $projectID = trim($this->input->post('projectID'));
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($this->input->post('transactionCurrencyID'), $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['systemGLCode'] = trim($gl_code[0]);
        $data['GLCode'] = trim($gl_code[1]);
        $data['GLDescription'] = trim($gl_code[2]);
        $data['GLType'] = trim($gl_code[3]);
        $data['segmentID'] = trim($segment[0]);
        $data['segmentCode'] = trim($segment[1]);
        $data['transactionCurrency'] = $master_recode['transactionCurrency'];
        $data['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
        $data['transactionAmount'] = trim($this->input->post('amount'));
        $data['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
        $data['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
        $data['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
        $data['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
        $data['partyCurrency'] = $master_recode['partyCurrency'];
        $data['partyExchangeRate'] = $master_recode['partyExchangeRate'];
        $data['partyAmount'] = ($data['transactionAmount'] / $master_recode['partyExchangeRate']);
        $data['description'] = trim($this->input->post('description'));
        $data['type'] = 'GL';
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('payVoucherDetailAutoID'))) {
            $this->db->where('payVoucherDetailAutoID', trim($this->input->post('payVoucherDetailAutoID')));
            $this->db->update('srp_erp_paymentvoucherdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Payment Voucher Detail : ' . $data['GLDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Payment Voucher Detail : ' . $data['GLDescription'] . ' Updated Successfully.');
            }
        } else {
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_paymentvoucherdetail', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Payment Voucher Detail : ' . $data['GLDescription'] . '  Saved Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Payment Voucher Detail : ' . $data['GLDescription'] . ' Saved Successfully.');
            }
        }
    }

    function save_direct_pv_detail_multiple()
    {
        $this->db->trans_start();
        $projectExist = project_is_exist();

        $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate, companyLocalCurrencyID,companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrencyID,companyReportingCurrency ,companyReportingExchangeRate ,partyCurrency,partyExchangeRate');
        $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
        $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();

        $gl_codes = $this->input->post('gl_code');
        $gl_code_des = $this->input->post('gl_code_des');
        $amount = $this->input->post('amount');
        $descriptions = $this->input->post('description');
        $segment_gls = $this->input->post('segment_gl');
        $projectID = $this->input->post('projectID');

        foreach ($gl_codes as $key => $gl_code) {
            $segment = explode('|', $segment_gls[$key]);
            $gl_code = explode('|', $gl_code_des[$key]);

            $data[$key]['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId'));
            $data[$key]['GLAutoID'] = $gl_codes[$key];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master_recode['transactionCurrencyID'], $projectCurrency);
                $data[$key]['projectID'] = $projectID[$key];
                $data[$key]['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data[$key]['systemGLCode'] = trim($gl_code[0]);
            $data[$key]['GLCode'] = trim($gl_code[1]);
            $data[$key]['GLDescription'] = trim($gl_code[2]);
            $data[$key]['GLType'] = trim($gl_code[3]);
            $data[$key]['segmentID'] = trim($segment[0]);
            $data[$key]['segmentCode'] = trim($segment[1]);
            $data[$key]['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
            $data[$key]['transactionCurrency'] = $master_recode['transactionCurrency'];
            $data[$key]['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
            $data[$key]['transactionAmount'] = $amount[$key];
            $data[$key]['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
            $data[$key]['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
            $data[$key]['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
            $data[$key]['companyLocalAmount'] = ($data[$key]['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
            $data[$key]['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
            $data[$key]['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
            $data[$key]['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
            $data[$key]['companyReportingAmount'] = ($data[$key]['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
            $data[$key]['partyCurrency'] = $master_recode['partyCurrency'];
            $data[$key]['partyExchangeRate'] = $master_recode['partyExchangeRate'];
            $data[$key]['partyAmount'] = ($data[$key]['transactionAmount'] / $master_recode['partyExchangeRate']);
            $data[$key]['description'] = $descriptions[$key];
            $data[$key]['type'] = 'GL';
            $data[$key]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$key]['modifiedUserID'] = $this->common_data['current_userID'];
            $data[$key]['modifiedUserName'] = $this->common_data['current_user'];
            $data[$key]['modifiedDateTime'] = $this->common_data['current_date'];

            $data[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdUserName'] = $this->common_data['current_user'];
            $data[$key]['createdDateTime'] = $this->common_data['current_date'];
        }
        $this->db->insert_batch('srp_erp_paymentvoucherdetail', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Payment Voucher Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Payment Voucher Detail :  Saved Successfully.');
        }

    }

    function save_pv_po_detail()
    {
        $this->db->trans_start();
        $po = explode('|', trim($this->input->post('po_code')));
        $po_des = explode('|', trim($this->input->post('po_des')));
        if (!empty($this->input->post('po_code')) && $this->input->post('po_des') != 'Select PO') {
            $this->db->select('transactionCurrency, transactionExchangeRate, transactionCurrencyID,companyLocalCurrencyID, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency,companyReportingExchangeRate ,supplierCurrency,supplierCurrencyExchangeRate,supplierCurrencyID,companyReportingCurrencyID,segmentID,segmentCode');
            $this->db->where('purchaseOrderID', trim($po[0]));
            $master = $this->db->get('srp_erp_purchaseordermaster')->row_array();
            $data['purchaseOrderID'] = trim($po[0]);
            $data['PODate'] = trim($po[1]);
            $data['POCode'] = trim($po_des[0]);
            $data['PODescription'] = trim($po_des[1]);
            $data['partyCurrencyID'] = $master['supplierCurrencyID'];
            $data['partyCurrency'] = $master['supplierCurrency'];
            $data['partyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
        } else {
            $this->db->select('transactionCurrency, transactionExchangeRate, transactionCurrencyID,companyLocalCurrencyID, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate ,partyCurrency,partyExchangeRate,partyCurrencyID,companyReportingCurrencyID,segmentID,segmentCode');
            $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId')));
            $master = $this->db->get('srp_erp_paymentvouchermaster')->row_array();
            $data['purchaseOrderID'] = 0;
            $data['PODate'] = null;
            $data['POCode'] = null;
            $data['PODescription'] = trim($this->input->post('description'));
            $data['partyCurrencyID'] = $master['partyCurrencyID'];
            $data['partyCurrency'] = $master['partyCurrency'];
            $data['partyExchangeRate'] = $master['partyExchangeRate'];
        }
        $data['transactionAmount'] = trim($this->input->post('amount'));
        $data['segmentID'] = $master['segmentID'];
        $data['segmentCode'] = $master['segmentCode'];
        $data['transactionCurrencyID'] = $master['transactionCurrencyID'];
        $data['transactionCurrencyID'] = $master['transactionCurrencyID'];
        $data['transactionCurrency'] = $master['transactionCurrency'];
        $data['transactionExchangeRate'] = $master['transactionExchangeRate'];
        $data['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
        $data['companyLocalCurrency'] = $master['companyLocalCurrency'];
        $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $master['companyLocalExchangeRate']);
        $data['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
        $data['companyReportingCurrency'] = $master['companyReportingCurrency'];
        $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $master['companyReportingExchangeRate']);
        $data['partyAmount'] = ($data['transactionAmount'] / $data['partyExchangeRate']);
        $data['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId'));
        $data['description'] = trim($this->input->post('description'));
        $data['type'] = 'Advance';
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('payVoucherDetailAutoID'))) {
            $this->db->where('payVoucherDetailAutoID', trim($this->input->post('payVoucherDetailAutoID')));
            $this->db->update('srp_erp_paymentvoucherdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Payment Voucher Detail Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Payment Voucher Detail Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('payVoucherDetailAutoID'));
            }
        } else {
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_paymentvoucherdetail', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Payment Voucher Detail  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Payment Voucher Detail Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function payment_confirmation()
    {

        $this->db->select('payVoucherDetailAutoID');
        $this->db->where('payVoucherAutoId',trim($this->input->post('PayVoucherAutoId')));
        $this->db->from('srp_erp_paymentvoucherdetail');
        $results=$this->db->get()->result_array();
        if(empty($results))
        {
            return array('w','There are no records to confirm this document!');
        }
        else
        {
            $pvid=$this->input->post('PayVoucherAutoId');
            $taxamnt=0;
            $GL = $this->db->query("SELECT
	SUM(transactionAmount) as transactionAmount
FROM
	srp_erp_paymentvoucherdetail
WHERE
	payVoucherAutoId = $pvid
	AND type='GL'
GROUP BY payVoucherAutoId")->row_array();

            if(empty($GL)){
                $GL=0;
            }else{
                $GL=$GL['transactionAmount'];
            }
            $Item = $this->db->query("SELECT
	SUM(transactionAmount) as transactionAmount
FROM
	srp_erp_paymentvoucherdetail
WHERE
	payVoucherAutoId = $pvid
	AND type='Item'
GROUP BY payVoucherAutoId")->row_array();
            if(empty($Item)){
                $Item=0;
            }else{
                $Item=$Item['transactionAmount'];
            }
            $debitnote = $this->db->query("SELECT
	SUM(transactionAmount) as transactionAmount
FROM
	srp_erp_paymentvoucherdetail
WHERE
	payVoucherAutoId = $pvid
	AND type='debitnote'
GROUP BY payVoucherAutoId")->row_array();
            if(empty($debitnote)){
                $debitnote=0;
            }else{
                $debitnote=$debitnote['transactionAmount'];
            }
            $Advance = $this->db->query("SELECT
	SUM(transactionAmount) as transactionAmount
FROM
	srp_erp_paymentvoucherdetail
WHERE
	payVoucherAutoId = $pvid
	AND type='Advance'
GROUP BY payVoucherAutoId")->row_array();
            if(empty($Advance)){
                $Advance=0;
            }else{
                $Advance=$Advance['transactionAmount'];
            }
            $Invoice = $this->db->query("SELECT
	SUM(transactionAmount) as transactionAmount
FROM
	srp_erp_paymentvoucherdetail
WHERE
	payVoucherAutoId = $pvid
	AND type='Invoice'
GROUP BY payVoucherAutoId")->row_array();
            if(empty($Invoice)){
                $Invoice=0;
            }else{
                $Invoice=$Invoice['transactionAmount'];
            }
            $tax = $this->db->query("SELECT
	SUM(taxPercentage) as taxPercentage
FROM
	srp_erp_paymentvouchertaxdetails
WHERE
	payVoucherAutoId = $pvid
GROUP BY payVoucherAutoId")->row_array();
            if(empty($tax)){
                $tax=0;
            }else{
                $tax=$tax['taxPercentage'];
                $taxamnt=(($Item+$GL)/100)*$tax;
            }
            $totalamnt=($Item+$GL+$Invoice+$Advance+$taxamnt)-$debitnote;
            if($totalamnt<0){
                return array('w', 'Grand total should be greater than 0.');
            }else{
                $this->db->select('PayVoucherAutoId');
                $this->db->where('PayVoucherAutoId', trim($this->input->post('PayVoucherAutoId')));
                $this->db->where('confirmedYN', 1);
                $this->db->from('srp_erp_paymentvouchermaster');
                $Confirmed = $this->db->get()->row_array();
                if (!empty($Confirmed)) {
                    return array('w', 'Document already confirmed');
                } else {


                    $PayVoucherAutoId = trim($this->input->post('PayVoucherAutoId'));
                    //$subItemNullCount = $this->db->query("SELECT count(srp_erp_itemmaster_subtemp.subItemAutoID) as countAll FROM srp_erp_paymentvoucherdetail LEFT JOIN srp_erp_itemmaster_subtemp ON srp_erp_itemmaster_subtemp.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_itemmaster_subtemp.itemAutoID WHERE payVoucherAutoId = '" . $PayVoucherAutoId . "'  AND ( srp_erp_paymentvoucherdetail.itemAutoID <> NULL OR srp_erp_paymentvoucherdetail.itemAutoID != ''  ) AND (srp_erp_itemmaster_subtemp.productReferenceNo = NULL OR srp_erp_itemmaster_subtemp.productReferenceNo = '') AND srp_erp_itemmaster.isSubitemExist=1 ")->row_array();
                    $subItemNullCount = $this->db->query("SELECT
                                        count(im.isSubitemExist) AS countAll
                                    FROM
                                        srp_erp_paymentvouchermaster masterTbl
                                    LEFT JOIN srp_erp_paymentvoucherdetail detailTbl ON masterTbl.payVoucherAutoId = detailTbl.payVoucherAutoId
                                    LEFT JOIN srp_erp_itemmaster im ON im.itemAutoID = detailTbl.itemAutoID
                                    LEFT JOIN srp_erp_itemmaster_subtemp itemMaster ON itemMaster.receivedDocumentDetailID = detailTbl.payVoucherDetailAutoID
                                    WHERE
                                        masterTbl.payVoucherAutoId = '" . $PayVoucherAutoId . "'
                                    AND im.isSubitemExist = 1
                                    AND (
                                        ISNULL(itemMaster.productReferenceNo )
                                        OR itemMaster.productReferenceNo = ''
)")->row_array();
                    $isProductReference_completed = isMandatory_completed_document($PayVoucherAutoId,'PV');

                    if ($isProductReference_completed == 0) {
                        $this->db->select('documentID, PVcode,DATE_FORMAT(PVdate, "%Y") as invYear,DATE_FORMAT(PVdate, "%m") as invMonth,companyFinanceYearID');
                        $this->db->where('PayVoucherAutoId', $PayVoucherAutoId);
                        $this->db->from('srp_erp_paymentvouchermaster');
                        $master_dt = $this->db->get()->row_array();
                        $this->load->library('sequence');
                        if($master_dt['PVcode'] == "0") {
                            $pvCd = array(
                                'PVcode' => $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth'])
                            );
                            $this->db->where('PayVoucherAutoId', trim($this->input->post('PayVoucherAutoId')));
                            $this->db->update('srp_erp_paymentvouchermaster', $pvCd);
                        }
                        $this->load->library('approvals');
                        $this->db->select('documentID,PayVoucherAutoId, PVcode,DATE_FORMAT(PVdate, "%Y") as invYear,DATE_FORMAT(PVdate, "%m") as invMonth,companyFinanceYearID');
                        $this->db->where('PayVoucherAutoId', $PayVoucherAutoId);
                        $this->db->from('srp_erp_paymentvouchermaster');
                        $app_data = $this->db->get()->row_array();
                        $approvals_status = $this->approvals->CreateApproval('PV', $app_data['PayVoucherAutoId'], $app_data['PVcode'], 'Payment Voucher', 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId');
                        if ($approvals_status==1) {
                            $data = array(
                                'confirmedYN' => 1,
                                'confirmedDate' => $this->common_data['current_date'],
                                'confirmedByEmpID' => $this->common_data['current_userID'],
                                'confirmedByName' => $this->common_data['current_user']
                            );
                            $this->db->where('PayVoucherAutoId', trim($this->input->post('PayVoucherAutoId')));
                            $this->db->update('srp_erp_paymentvouchermaster', $data);
                            return array('s','Document confirmed successfully.');

                        }else if($approvals_status==3){
                            return array('w', 'There are no users exist to perform approval for this document.');
                        } else {
                            return array('e', 'oops, something went wrong!');
                        }
                    } else {
                        return array('e', 'Please complete you sub item configuration, fill all the mandatory fields!');
                    }
                }
            }




        }
    }


    function payment_match_confirmation()
    {
        $this->db->select('matchID');
        $this->db->where('matchID', trim($this->input->post('matchID')));
        $this->db->from('srp_erp_pvadvancematchdetails');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('w', 'There are no records to confirm this document!');
        }
        else {
            $this->db->select('matchID');
            $this->db->where('matchID', trim($this->input->post('matchID')));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_pvadvancematch');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                return array('w', 'Document already confirmed');
            } else {

                $data = array(
                    'confirmedYN' => 1,
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                    'confirmedByName' => $this->common_data['current_user']
                );

                $this->db->where('matchID', trim($this->input->post('matchID')));
                $this->db->update('srp_erp_pvadvancematch', $data);
                return array('s', 'Document confirmed Successfully');
            }

        }
    }

    function fetch_pv_advance_detail()
    {
        $data = array();
        $convertFormat = convert_date_format_sql();
        $this->db->select('supplierID,transactionCurrency,DATE_FORMAT(matchDate,"%Y-%m-%d") AS matchDate');
        $this->db->where('matchID', $this->input->post('matchID'));
        $master_arr = $this->db->get('srp_erp_pvadvancematch')->row_array();

        $this->db->select('purchaseOrderID,POCode,PODescription,srp_erp_paymentvoucherdetail.transactionAmount ,PODate ,DATE_FORMAT(srp_erp_paymentvouchermaster.PVdate,\'' . $convertFormat . '\') AS PVdate ,srp_erp_paymentvouchermaster.PVcode,sum(srp_erp_pvadvancematchdetails.transactionAmount) as paid,srp_erp_paymentvoucherdetail.payVoucherDetailAutoID');
        $this->db->from('srp_erp_paymentvouchermaster');
        $this->db->where('partyID', $master_arr['supplierID']);
        $this->db->where('srp_erp_paymentvoucherdetail.transactionCurrency', $master_arr['transactionCurrency']);
        $this->db->where('srp_erp_paymentvoucherdetail.companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('type', 'Advance');
        $this->db->group_by("payVoucherDetailAutoID");
        $this->db->where('srp_erp_paymentvouchermaster.approvedYN', 1);
        $this->db->join('srp_erp_paymentvoucherdetail', 'srp_erp_paymentvoucherdetail.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId');
        $this->db->join('srp_erp_pvadvancematchdetails', 'srp_erp_pvadvancematchdetails.payVoucherDetailAutoID = srp_erp_paymentvoucherdetail.payVoucherDetailAutoID', 'Left');
        $data['payment'] = $this->db->get()->result_array();

        $this->db->select('InvoiceAutoID,bookingInvCode,bookingDate,transactionAmount,paymentTotalAmount ,DebitNoteTotalAmount,advanceMatchedTotal');
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $this->db->where('paymentInvoiceYN', 0);
        $this->db->where('approvedYN', 1);
        $this->db->where('supplierID', $master_arr['supplierID']);
        $this->db->where('transactionCurrency', $master_arr['transactionCurrency']);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data['invoice'] = $this->db->get()->result_array();
        return $data;
    }

    function save_match_amount()
    {
        $this->db->trans_start();
        $payVoucherDetailAutoID = $this->input->post('payVoucherDetailAutoID');
        $invoice_id = $this->input->post('InvoiceAutoID');
        $amounts = $this->input->post('amounts');
        $matchID = $this->input->post('matchID');
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate');
        $this->db->where('matchID', $matchID);
        $master = $this->db->get('srp_erp_pvadvancematch')->row_array();

        $this->db->select('srp_erp_paymentvouchermaster.payVoucherAutoId,srp_erp_paymentvoucherdetail.transactionAmount,srp_erp_paymentvouchermaster.PVdate,srp_erp_paymentvouchermaster.PVcode,srp_erp_paymentvoucherdetail.payVoucherDetailAutoID');
        $this->db->group_by("payVoucherDetailAutoID");
        $this->db->from('srp_erp_paymentvouchermaster');
        $this->db->join('srp_erp_paymentvoucherdetail', 'srp_erp_paymentvoucherdetail.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId');
        $this->db->where_in('payVoucherDetailAutoID', $payVoucherDetailAutoID);
        $detail_arr = $this->db->get()->result_array();

        for ($i = 0; $i < count($detail_arr); $i++) {
            $invoice_data = $this->fetch_invoice($invoice_id[$i]);
            $data[$i]['matchID'] = $matchID;
            $data[$i]['payVoucherAutoId'] = $detail_arr[$i]['payVoucherAutoId'];
            $data[$i]['payVoucherDetailAutoID'] = $detail_arr[$i]['payVoucherDetailAutoID'];
            $data[$i]['pvCode'] = $detail_arr[$i]['PVcode'];
            $data[$i]['PVdate'] = $detail_arr[$i]['PVdate'];
            $data[$i]['InvoiceAutoID'] = trim($invoice_data['InvoiceAutoID']);
            $data[$i]['bookingInvCode'] = trim($invoice_data['bookingInvCode']);
            $data[$i]['bookingDate'] = trim($invoice_data['bookingDate']);
            $data[$i]['transactionAmount'] = $amounts[$i];
            $data[$i]['transactionExchangeRate'] = 1;
            $data[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data[$i]['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master['companyLocalExchangeRate']);
            $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master['companyReportingExchangeRate']);
            $data[$i]['supplierAmount'] = ($data[$i]['transactionAmount'] / $master['supplierCurrencyExchangeRate']);
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
            $this->db->query("UPDATE srp_erp_paysupplierinvoicemaster SET advanceMatchedTotal = (advanceMatchedTotal+{$amo}) WHERE InvoiceAutoID='{$id}'");
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_pvadvancematchdetails', $data);
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
        $this->db->select('InvoiceAutoID,bookingInvCode,bookingDate,transactionAmount,paymentTotalAmount ,DebitNoteTotalAmount,advanceMatchedTotal');
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $this->db->where('InvoiceAutoID', $id);
        return $this->db->get()->row_array();
    }

    function save_pv_approval()
    {
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_code = trim($this->input->post('payVoucherAutoId'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        $comments = trim($this->input->post('comments'));

        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'PV');
        if ($approvals_status == 1) {
            $this->db->select('*');
            $this->db->where('payVoucherAutoId', $system_code);
            $this->db->from('srp_erp_paymentvouchermaster');
            $master = $this->db->get()->row_array();
            $this->db->select('*');
            $this->db->where('payVoucherAutoId', $system_code);
            $this->db->from('srp_erp_paymentvoucherdetail');
            $payment_detail = $this->db->get()->result_array();
            for ($a = 0; $a < count($payment_detail); $a++) {
                if ($payment_detail[$a]['type'] == 'Item') {
                    $item = fetch_item_data($payment_detail[$a]['itemAutoID']);
                    $ACA_ID = $this->common_data['controlaccounts']['ACA'];
                    $ACA = fetch_gl_account_desc($ACA_ID);
                    if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory') {
                        $itemAutoID = $payment_detail[$a]['itemAutoID'];
                        $qty = $payment_detail[$a]['requestedQty'] / $payment_detail[$a]['conversionRateUOM'];
                        $wareHouseAutoID = $payment_detail[$a]['wareHouseAutoID'];
                        $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                        $item_arr[$a]['itemAutoID'] = $payment_detail[$a]['itemAutoID'];
                        $item_arr[$a]['currentStock'] = ($item['currentStock'] + $qty);
                        $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) + $payment_detail[$a]['transactionAmount']) / $item_arr[$a]['currentStock']), $master['companyLocalCurrencyDecimalPlaces']);
                        $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) + ($payment_detail[$a]['transactionAmount'] / $master['companyReportingExchangeRate'])) / $item_arr[$a]['currentStock']), $master['companyReportingCurrencyDecimalPlaces']);
                        if (!empty($item_arr)) {
                            $this->db->where('itemAutoID', trim($payment_detail[$a]['itemAutoID']));
                            $this->db->update('srp_erp_itemmaster', $item_arr[$a]);
                        }
                        $itemledger_arr[$a]['documentID'] = $master['documentID'];
                        $itemledger_arr[$a]['documentCode'] = $master['documentID'];
                        $itemledger_arr[$a]['documentAutoID'] = $master['payVoucherAutoId'];
                        $itemledger_arr[$a]['documentSystemCode'] = $master['PVcode'];
                        $itemledger_arr[$a]['documentDate'] = $master['PVdate'];
                        $itemledger_arr[$a]['referenceNumber'] = $master['referenceNo'];
                        $itemledger_arr[$a]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                        $itemledger_arr[$a]['companyFinanceYear'] = $master['companyFinanceYear'];
                        $itemledger_arr[$a]['FYBegin'] = $master['FYBegin'];
                        $itemledger_arr[$a]['FYEnd'] = $master['FYEnd'];
                        $itemledger_arr[$a]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                        $itemledger_arr[$a]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                        $itemledger_arr[$a]['wareHouseAutoID'] = $payment_detail[$a]['wareHouseAutoID'];
                        $itemledger_arr[$a]['wareHouseCode'] = $payment_detail[$a]['wareHouseCode'];
                        $itemledger_arr[$a]['wareHouseLocation'] = $payment_detail[$a]['wareHouseLocation'];
                        $itemledger_arr[$a]['wareHouseDescription'] = $payment_detail[$a]['wareHouseDescription'];
                        $itemledger_arr[$a]['itemAutoID'] = $payment_detail[$a]['itemAutoID'];
                        $itemledger_arr[$a]['itemSystemCode'] = $payment_detail[$a]['itemSystemCode'];
                        $itemledger_arr[$a]['itemDescription'] = $payment_detail[$a]['itemDescription'];
                        $itemledger_arr[$a]['defaultUOMID'] = $payment_detail[$a]['defaultUOMID'];
                        $itemledger_arr[$a]['defaultUOM'] = $payment_detail[$a]['defaultUOM'];
                        $itemledger_arr[$a]['transactionUOM'] = $payment_detail[$a]['unitOfMeasure'];
                        $itemledger_arr[$a]['transactionUOMID'] = $payment_detail[$a]['unitOfMeasureID'];
                        $itemledger_arr[$a]['transactionQTY'] = $payment_detail[$a]['requestedQty'];
                        $itemledger_arr[$a]['convertionRate'] = $payment_detail[$a]['conversionRateUOM'];
                        $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                        $itemledger_arr[$a]['PLGLAutoID'] = $item['costGLAutoID'];
                        $itemledger_arr[$a]['PLSystemGLCode'] = $item['costSystemGLCode'];
                        $itemledger_arr[$a]['PLGLCode'] = $item['costGLCode'];
                        $itemledger_arr[$a]['PLDescription'] = $item['costDescription'];
                        $itemledger_arr[$a]['PLType'] = $item['costType'];
                        $itemledger_arr[$a]['BLGLAutoID'] = $item['assteGLAutoID'];
                        $itemledger_arr[$a]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                        $itemledger_arr[$a]['BLGLCode'] = $item['assteGLCode'];
                        $itemledger_arr[$a]['BLDescription'] = $item['assteDescription'];
                        $itemledger_arr[$a]['BLType'] = $item['assteType'];
                        $itemledger_arr[$a]['transactionAmount'] = $payment_detail[$a]['transactionAmount'];
                        $itemledger_arr[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                        $itemledger_arr[$a]['transactionCurrency'] = $master['transactionCurrency'];
                        $itemledger_arr[$a]['transactionExchangeRate'] = $master['transactionExchangeRate'];
                        $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                        $itemledger_arr[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                        $itemledger_arr[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                        $itemledger_arr[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                        $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                        $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                        $itemledger_arr[$a]['companyLocalWacAmount'] = $item_arr[$a]['companyLocalWacAmount'];
                        $itemledger_arr[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                        $itemledger_arr[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                        $itemledger_arr[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                        $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                        $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                        $itemledger_arr[$a]['companyReportingWacAmount'] = $item_arr[$a]['companyReportingWacAmount'];
                        $itemledger_arr[$a]['partyCurrencyID'] = $master['partyCurrencyID'];
                        $itemledger_arr[$a]['partyCurrency'] = $master['partyCurrency'];
                        $itemledger_arr[$a]['partyCurrencyExchangeRate'] = $master['partyExchangeRate'];
                        $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $master['partyCurrencyDecimalPlaces'];
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

                    } elseif ($item['mainCategory'] == 'Fixed Assets') {
                        $this->load->library('sequence');
                        $assat_data = array();
                        $assat_amount = ($payment_detail[$a]['transactionAmount'] / ($payment_detail[$a]['requestedQty'] / $payment_detail[$a]['conversionRateUOM']));
                        for ($b = 0; $b < ($payment_detail[$a]['requestedQty'] / $payment_detail[$a]['conversionRateUOM']); $b++) {
                            $assat_data[$b]['documentID'] = 'FA';
                            $assat_data[$b]['docOriginSystemCode'] = $master['payVoucherAutoId'];
                            $assat_data[$b]['docOriginDetailID'] = $payment_detail[$a]['payVoucherDetailAutoID'];
                            $assat_data[$b]['docOrigin'] = 'PV';
                            $assat_data[$b]['dateAQ'] = $master['PVdate'];
                            $assat_data[$b]['grvAutoID'] = $master['payVoucherAutoId'];
                            $assat_data[$b]['isFromGRV'] = 1;
                            $assat_data[$b]['assetDescription'] = $item['itemDescription'];
                            $assat_data[$b]['comments'] = trim($this->input->post('comments'));
                            $assat_data[$b]['faCatID'] = $item['subcategoryID'];
                            $assat_data[$b]['faSubCatID'] = $item['subSubCategoryID'];
                            $assat_data[$b]['assetType'] = 1;
                            $assat_data[$b]['transactionAmount'] = $assat_amount;
                            $assat_data[$b]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                            $assat_data[$b]['transactionCurrency'] = $master['transactionCurrency'];
                            $assat_data[$b]['transactionCurrencyExchangeRate'] = $master['transactionExchangeRate'];
                            $assat_data[$b]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                            $assat_data[$b]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                            $assat_data[$b]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                            $assat_data[$b]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                            $assat_data[$b]['companyLocalAmount'] = round($assat_amount, $assat_data[$b]['transactionCurrencyDecimalPlaces']);
                            $assat_data[$b]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                            $assat_data[$b]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                            $assat_data[$b]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                            $assat_data[$b]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                            $assat_data[$b]['companyReportingAmount'] = round($assat_amount, $assat_data[$b]['companyLocalCurrencyDecimalPlaces']);
                            $assat_data[$b]['companyReportingDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                            $assat_data[$b]['supplierID'] = $master['partyID'];
                            $assat_data[$b]['segmentID'] = $master['segmentID'];
                            $assat_data[$b]['segmentCode'] = $master['segmentCode'];
                            $assat_data[$b]['companyID'] = $master['companyID'];
                            $assat_data[$b]['companyCode'] = $master['companyCode'];
                            $assat_data[$b]['createdUserGroup'] = $master['createdUserGroup'];
                            $assat_data[$b]['createdPCID'] = $master['createdPCID'];
                            $assat_data[$b]['createdUserID'] = $master['createdUserID'];
                            $assat_data[$b]['createdDateTime'] = $master['createdDateTime'];
                            $assat_data[$b]['createdUserName'] = $master['createdUserName'];
                            $assat_data[$b]['modifiedPCID'] = $master['modifiedPCID'];
                            $assat_data[$b]['modifiedUserID'] = $master['modifiedUserID'];
                            $assat_data[$b]['modifiedDateTime'] = $master['modifiedDateTime'];
                            $assat_data[$b]['modifiedUserName'] = $master['modifiedUserName'];
                            $assat_data[$b]['costGLAutoID'] = $item['faCostGLAutoID'];
                            $assat_data[$b]['ACCDEPGLAutoID'] = $item['faACCDEPGLAutoID'];
                            $assat_data[$b]['DEPGLAutoID'] = $item['faDEPGLAutoID'];
                            $assat_data[$b]['DISPOGLAutoID'] = $item['faDISPOGLAutoID'];
                            $assat_data[$b]['isPostToGL'] = 1;
                            $assat_data[$b]['postGLAutoID'] = $ACA_ID;
                            $assat_data[$b]['postGLCode'] = $ACA['systemAccountCode'];
                            $assat_data[$b]['postGLCodeDes'] = $ACA['GLDescription'];
                            $assat_data[$b]['faCode'] = $this->sequence->sequence_generator("FA");
                        }
                    }
                } elseif ($payment_detail[$a]['type'] == 'Advance') {
                    $this->load->library('sequence');
                    $advance_data = array();
                }

            }

            if (!empty($assat_data)) {
                $assat_data = array_values($assat_data);
                $this->db->insert_batch('srp_erp_fa_asset_master', $assat_data);
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
            $generalledger_arr = array();
            $double_entry = $this->Double_entry_model->fetch_double_entry_payment_voucher_data($system_code, 'PV');

            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['payVoucherAutoId'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['PVcode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['PVdate'];
                $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['pvType'];
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['PVdate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['PVdate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['PVNarration'];
                $generalledger_arr[$i]['chequeNumber'] = $double_entry['master_data']['PVchequeNo'];
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

            $amount = payment_voucher_total_value($double_entry['master_data']['payVoucherAutoId'], $double_entry['master_data']['transactionCurrencyDecimalPlaces'], 0);
            $bankledger_arr['documentMasterAutoID'] = $double_entry['master_data']['payVoucherAutoId'];
            $bankledger_arr['documentDate'] = $double_entry['master_data']['PVdate'];
            $bankledger_arr['transactionType'] = 2;
            $bankledger_arr['bankName'] = $double_entry['master_data']['PVbank'];
            $bankledger_arr['bankGLAutoID'] = $double_entry['master_data']['bankGLAutoID'];
            $bankledger_arr['bankSystemAccountCode'] = $double_entry['master_data']['bankSystemAccountCode'];
            $bankledger_arr['bankGLSecondaryCode'] = $double_entry['master_data']['bankGLSecondaryCode'];
            $bankledger_arr['documentType'] = 'PV';
            $bankledger_arr['documentSystemCode'] = $double_entry['master_data']['PVcode'];
            $bankledger_arr['modeofPayment'] = $double_entry['master_data']['modeOfPayment'];
            $bankledger_arr['chequeNo'] = $double_entry['master_data']['PVchequeNo'];
            $bankledger_arr['chequeDate'] = $double_entry['master_data']['PVchequeDate'];
            $bankledger_arr['memo'] = $double_entry['master_data']['PVNarration'];
            $bankledger_arr['partyType'] = $double_entry['master_data']['partyType'];
            $bankledger_arr['partyAutoID'] = $double_entry['master_data']['partyID'];
            $bankledger_arr['partyCode'] = $double_entry['master_data']['partyCode'];
            $bankledger_arr['partyName'] = $double_entry['master_data']['partyName'];
            $bankledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
            $bankledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
            $bankledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
            $bankledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
            $bankledger_arr['transactionAmount'] = $amount;
            $bankledger_arr['partyCurrencyID'] = $double_entry['master_data']['partyCurrencyID'];
            $bankledger_arr['partyCurrency'] = $double_entry['master_data']['partyCurrency'];
            $bankledger_arr['partyCurrencyExchangeRate'] = $double_entry['master_data']['partyExchangeRate'];
            $bankledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['partyCurrencyDecimalPlaces'];
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
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                $this->db->select('sum(transactionAmount) as transaction_total, sum(companyLocalAmount) as companyLocal_total, sum(companyReportingAmount) as companyReporting_total, sum(partyCurrencyAmount) as party_total');
                $this->db->where('documentCode', 'PV');
                $this->db->where('documentMasterAutoID', $system_code);
                $totals = $this->db->get('srp_erp_generalledger')->row_array();
                if ($totals['transaction_total'] != 0 or $totals['companyLocal_total'] != 0 or $totals['companyReporting_total'] != 0 or $totals['party_total'] != 0) {
                    $generalledger_arr = array();
                    $ERGL_ID = $this->common_data['controlaccounts']['ERGL'];
                    $ERGL = fetch_gl_account_desc($ERGL_ID);
                    $generalledger_arr['documentMasterAutoID'] = $double_entry['master_data']['payVoucherAutoId'];
                    $generalledger_arr['documentCode'] = $double_entry['code'];
                    $generalledger_arr['documentSystemCode'] = $double_entry['master_data']['PVcode'];
                    $generalledger_arr['documentDate'] = $double_entry['master_data']['PVdate'];
                    $generalledger_arr['documentType'] = $double_entry['master_data']['pvType'];
                    $generalledger_arr['documentYear'] = $double_entry['master_data']['PVdate'];
                    $generalledger_arr['documentMonth'] = date("m", strtotime($double_entry['master_data']['PVdate']));
                    $generalledger_arr['documentNarration'] = $double_entry['master_data']['PVNarration'];
                    $generalledger_arr['chequeNumber'] = $double_entry['master_data']['PVchequeNo'];
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
                    $generalledger_arr['partyType'] = $double_entry['master_data']['partyType'];
                    $generalledger_arr['partyAutoID'] = $double_entry['master_data']['partyID'];
                    $generalledger_arr['partySystemCode'] = $double_entry['master_data']['partyCode'];
                    $generalledger_arr['partyName'] = $double_entry['master_data']['partyName'];
                    $generalledger_arr['partyCurrencyID'] = $double_entry['master_data']['partyCurrencyID'];
                    $generalledger_arr['partyCurrency'] = $double_entry['master_data']['partyCurrency'];
                    $generalledger_arr['partyExchangeRate'] = $double_entry['master_data']['partyExchangeRate'];
                    $generalledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['partyCurrencyDecimalPlaces'];
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

            $this->session->set_flashdata('s', 'Payment Voucher Approval Successfully.');

            /** update sub item master : shafry */
            $maxLevel = $this->approvals->maxlevel('PV');
            $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? true : false;
            if ($isFinalLevel) {
                $masterID = $this->input->post('payVoucherAutoId');
                $result = $this->db->query("SELECT  * FROM srp_erp_itemmaster_subtemp WHERE receivedDocumentAutoID = '" . $masterID . "'")->result_array();
                if (!empty($result)) {
                    $i = 0;
                    foreach ($result as $item) {
                        unset($result[$i]['subItemAutoID']);
                        $i++;
                    }

                    $this->db->insert_batch('srp_erp_itemmaster_sub', $result);
                    $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentAutoID' => $masterID, 'receivedDocumentID' => 'PV'));

                }
            }

            $itemAutoIDarry = array();
            $wareHouseAutoIDDarry = array();
            foreach($payment_detail as $value){
                if($value['itemAutoID']){
                    array_push($itemAutoIDarry,$value['itemAutoID']);
                }
                if($value['wareHouseAutoID']){
                    array_push($wareHouseAutoIDDarry,$value['wareHouseAutoID']);
                }

            }
            if($itemAutoIDarry && $wareHouseAutoIDDarry){
                $companyID=current_companyID();
                $exceededitems = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID IN  (".join($itemAutoIDarry).") AND companyID= $companyID AND warehouseAutoID IN  (".join($wareHouseAutoIDDarry).") AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                $exceededMatchID=0;
                if(!empty($exceededitems)){
                    $this->load->library('sequence');
                    $exceededmatch['documentID'] = "EIM";
                    $exceededmatch['documentDate'] = $master ['PVdate'];
                    $exceededmatch['orginDocumentID'] = $master ['documentID'];
                    $exceededmatch['orginDocumentMasterID'] = $master ['payVoucherAutoId'];
                    $exceededmatch['orginDocumentSystemCode'] = $master ['PVcode'];
                    $exceededmatch['companyFinanceYearID'] = $master ['companyFinanceYearID'];
                    $exceededmatch['companyID'] = current_companyID();
                    $exceededmatch['transactionCurrencyID'] = $master ['transactionCurrencyID'];
                    $exceededmatch['transactionCurrency'] = $master ['transactionCurrency'];
                    $exceededmatch['transactionExchangeRate'] = $master ['transactionExchangeRate'];
                    $exceededmatch['transactionCurrencyDecimalPlaces'] = $master ['transactionCurrencyDecimalPlaces'];
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
                    $exceededMatchID=$this->db->insert_id();
                }

                foreach($payment_detail as $itemid){
                    if($itemid['type']=='Item'){
                        $receivedQty=$itemid['requestedQty'];
                        $receivedQtyConverted=$itemid['requestedQty']/$itemid['conversionRateUOM'];
                        $companyID=current_companyID();
                        $exceededitems = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID AND warehouseAutoID= '" . $itemid['wareHouseAutoID'] . "' AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                        $itemCost = $this->db->query("SELECT  companyLocalWacAmount FROM srp_erp_itemmaster WHERE itemAutoID = '" . $itemid['itemAutoID'] . "' AND companyID= $companyID")->row_array();
                        $sumqty=array_column($exceededitems,'balanceQty');
                        $sumqty=array_sum($sumqty);
                        if(!empty($exceededitems)){
                            foreach($exceededitems as $exceededItemAutoID){
                                if($receivedQtyConverted>0){
                                    $balanceQty=$exceededItemAutoID['balanceQty'];
                                    $updatedQty=$exceededItemAutoID['updatedQty'];
                                    $balanceQtyConverted=$exceededItemAutoID['balanceQty']/$exceededItemAutoID['conversionRateUOM'];
                                    $updatedQtyConverted=$exceededItemAutoID['updatedQty']/$exceededItemAutoID['conversionRateUOM'];
                                    if ($receivedQtyConverted > $balanceQtyConverted) {
                                        $qty = $receivedQty - $balanceQty;
                                        $qtyconverted = $receivedQtyConverted - $balanceQtyConverted;
                                        $receivedQty = $qty;
                                        $receivedQtyConverted = $qtyconverted;
                                        $exeed['balanceQty'] = 0;
                                        //$exeed['updatedQty'] = $updatedQty+$balanceQty;
                                        $exeed['updatedQty'] = ($updatedQtyConverted*$exceededItemAutoID['conversionRateUOM'])+($balanceQtyConverted*$exceededItemAutoID['conversionRateUOM']);
                                        $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                        $this->db->update('srp_erp_itemexceeded', $exeed);

                                        $exceededmatchdetail['exceededMatchID'] = $exceededMatchID;
                                        $exceededmatchdetail['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                        $exceededmatchdetail['warehouseAutoID'] = $itemid['wareHouseAutoID'];
                                        $exceededmatchdetail['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                        $exceededmatchdetail['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                        $exceededmatchdetail['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                        $exceededmatchdetail['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                        $exceededmatchdetail['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                        $exceededmatchdetail['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                        $exceededmatchdetail['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                        $exceededmatchdetail['matchedQty'] = $balanceQtyConverted;
                                        $exceededmatchdetail['itemCost'] = $itemCost['companyLocalWacAmount'];
                                        $exceededmatchdetail['totalValue'] = $balanceQtyConverted*$exceededmatchdetail['itemCost'];
                                        $exceededmatchdetail['segmentID'] = $exceededItemAutoID['segmentID'];
                                        $exceededmatchdetail['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                        $exceededmatchdetail['createdUserGroup'] = $this->common_data['user_group'];
                                        $exceededmatchdetail['createdPCID'] = $this->common_data['current_pc'];
                                        $exceededmatchdetail['createdUserID'] = $this->common_data['current_userID'];
                                        $exceededmatchdetail['createdUserName'] = $this->common_data['current_user'];
                                        $exceededmatchdetail['createdDateTime'] = $this->common_data['current_date'];

                                        $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetail);

                                    } else {
                                        $exeed['balanceQty'] = $balanceQtyConverted-$receivedQtyConverted;
                                        $exeed['updatedQty'] = $updatedQtyConverted+$receivedQtyConverted;
                                        $this->db->where('exceededItemAutoID', $exceededItemAutoID['exceededItemAutoID']);
                                        $this->db->update('srp_erp_itemexceeded', $exeed);

                                        $exceededmatchdetails['exceededMatchID'] = $exceededMatchID;
                                        $exceededmatchdetails['itemAutoID'] = $exceededItemAutoID['itemAutoID'];
                                        $exceededmatchdetails['warehouseAutoID'] = $itemid['wareHouseAutoID'];
                                        $exceededmatchdetails['assetGLAutoID'] = $exceededItemAutoID['assetGLAutoID'];
                                        $exceededmatchdetails['costGLAutoID'] = $exceededItemAutoID['costGLAutoID'];
                                        $exceededmatchdetails['defaultUOMID'] = $exceededItemAutoID['defaultUOMID'];
                                        $exceededmatchdetails['defaultUOM'] = $exceededItemAutoID['defaultUOM'];
                                        $exceededmatchdetails['unitOfMeasureID'] = $exceededItemAutoID['unitOfMeasureID'];
                                        $exceededmatchdetails['unitOfMeasure'] = $exceededItemAutoID['unitOfMeasure'];
                                        $exceededmatchdetails['conversionRateUOM'] = $exceededItemAutoID['conversionRateUOM'];
                                        $exceededmatchdetails['matchedQty'] = $receivedQtyConverted;
                                        $exceededmatchdetails['itemCost'] = $itemCost['companyLocalWacAmount'];
                                        $exceededmatchdetails['totalValue'] = $receivedQtyConverted*$exceededmatchdetails['itemCost'];
                                        $exceededmatchdetails['segmentID'] = $exceededItemAutoID['segmentID'];
                                        $exceededmatchdetails['segmentCode'] = $exceededItemAutoID['segmentCode'];
                                        $exceededmatchdetails['createdUserGroup'] = $this->common_data['user_group'];
                                        $exceededmatchdetails['createdPCID'] = $this->common_data['current_pc'];
                                        $exceededmatchdetails['createdUserID'] = $this->common_data['current_userID'];
                                        $exceededmatchdetails['createdUserName'] = $this->common_data['current_user'];
                                        $exceededmatchdetails['createdDateTime'] = $this->common_data['current_date'];
                                        $this->db->insert('srp_erp_itemexceededmatchdetails', $exceededmatchdetails);
                                        $receivedQty = $receivedQty - $exeed['updatedQty'];
                                        $receivedQtyConverted =$receivedQtyConverted- ($updatedQtyConverted+$receivedQtyConverted);
                                    }
                                }
                            }
                        }
                    }

                }
                if(!empty($exceededitems)){
                    exceed_double_entry($exceededMatchID);
                }
            }
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

    function fetch_payment_voucher_detail()
    {
        $this->db->select('*');
        $this->db->where('payVoucherDetailAutoID', trim($this->input->post('payVoucherDetailAutoID')));
        $this->db->from('srp_erp_paymentvoucherdetail');
        return $this->db->get()->row_array();
    }


    function delete_tax_detail()
    {
        $this->db->delete('srp_erp_paymentvouchertaxdetails', array('taxDetailAutoID' => trim($this->input->post('taxDetailAutoID'))));
        return true;
    }

    function save_inv_tax_detail()
    {
        $this->db->select('taxMasterAutoID');
        $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $tax_detail = $this->db->get('srp_erp_paymentvouchertaxdetails')->row_array();
        if (!empty($tax_detail)) {
            return array('status' => 1, 'type' => 'w', 'data' => ' Tax Detail added already ! ');
        }

        $this->db->trans_start();
        $this->db->select('*');
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $this->db->from('srp_erp_taxmaster');
        $master = $this->db->get()->row_array();

        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
        $inv_master = $this->db->get('srp_erp_paymentvouchermaster')->row_array();

        $data['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId'));
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
            $this->db->update('srp_erp_paymentvouchertaxdetails', $data);
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
            $this->db->insert('srp_erp_paymentvouchertaxdetails', $data);
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

    function fetch_sales_person($salesPersonID, $currency, $payVoucherAutoId)
    {
        $data = $this->db->query("SELECT srp_erp_salescommisionmaster.transactionCurrencyDecimalPlaces,sum(netCommision) as netCommision,IFNULL(sum(srp_erp_paymentvoucherdetail.transactionAmount),0) as transactionAmount,srp_erp_salescommisionmaster.salesCommisionCode,srp_erp_salescommisionmaster.referenceNo,(sum(netCommision)-IFNULL(sum(transactionAmount),0)) as balance,srp_erp_salescommisionmaster.salesCommisionID FROM srp_erp_salespersonmaster 
INNER JOIN srp_erp_salescommisionperson ON srp_erp_salespersonmaster.salesPersonID = srp_erp_salescommisionperson.salesPersonID 
INNER JOIN srp_erp_salescommisionmaster ON srp_erp_salescommisionmaster.salesCommisionID = srp_erp_salescommisionperson.salesCommisionID AND srp_erp_salescommisionmaster.approvedYN=1
LEFT JOIN (SELECT SUM(transactionAmount) as transactionAmount,salesPersonID FROM srp_erp_paymentvoucherdetail GROUP BY srp_erp_paymentvoucherdetail.salesPersonID) as srp_erp_paymentvoucherdetail ON srp_erp_paymentvoucherdetail.salesPersonID = srp_erp_salespersonmaster.salesPersonID
WHERE srp_erp_salespersonmaster.salesPersonID = {$salesPersonID} AND srp_erp_salescommisionmaster.transactionCurrencyID = $currency  GROUP BY srp_erp_salescommisionmaster.salesCommisionID HAVING balance > 0")->result_array();
        //echo $this->db->last_query();
        return $data;
    }

    function add_sub_itemMaster_tmpTbl($qty = 0, $itemAutoID, $masterID, $detailID, $code = 'PV', $itemCode = null, $data = array(), $warehouseID)
    {


        $uom = isset($data['uom']) && !empty($data['uom']) ? $data['uom'] : null;
        $uomID = isset($data['uomID']) && !empty($data['uomID']) ? $data['uomID'] : null;
        $pv_detailID = isset($data['pv_detailID']) && !empty($data['pv_detailID']) ? $data['pv_detailID'] : null;
        $data_subItemMaster = array();
        if ($qty > 0) {
            $x = 0;
            for ($i = 1; $i <= $qty; $i++) {
                $data_subItemMaster[$x]['itemAutoID'] = $itemAutoID;
                $data_subItemMaster[$x]['subItemSerialNo'] = $i;
                $data_subItemMaster[$x]['subItemCode'] = $itemCode . '/PV/' . $pv_detailID . '/' . $i;
                $data_subItemMaster[$x]['wareHouseAutoID'] = $warehouseID;
                $data_subItemMaster[$x]['uom'] = $uom;
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
            $this->db->insert_batch('srp_erp_itemmaster_subtemp', $data_subItemMaster);
        }
    }

    function edit_sub_itemMaster_tmpTbl($qty = 0, $itemAutoID, $masterID, $detailID, $code = 'PV', $itemCode = null, $data = array())
    {
        $this->db->select('isSubitemExist');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', $itemAutoID);
        $r = $this->db->get()->row_array();
        $isSubitemExist = $r['isSubitemExist'];

        $uom = isset($data['uom']) && !empty($data['uom']) ? $data['uom'] : null;
        $uomID = isset($data['uomID']) && !empty($data['uomID']) ? $data['uomID'] : null;
        $payVoucherDetailAutoID = isset($data['payVoucherDetailAutoID']) && !empty($data['payVoucherDetailAutoID']) ? $data['payVoucherDetailAutoID'] : null;
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');

        $result = $this->getQty_subItemMaster_tmpTbl($itemAutoID, $masterID, $detailID);
        //echo $this->db->last_query();

        /** delete existing set */
        $this->delete_sub_itemMaster_existing($itemAutoID, $masterID, $detailID, 'PV');

        if ($isSubitemExist == 1) {
            $count_subItemMaster = 0;
            if (!empty($result)) {
                $count_subItemMaster = count($result);
            }
            if ($count_subItemMaster != $qty || true) {


                /** Add new set */

                $data_subItemMaster = array();
                if ($qty > 0) {
                    $x = 0;
                    for ($i = 1; $i <= $qty; $i++) {
                        $data_subItemMaster[$x]['itemAutoID'] = $itemAutoID;
                        $data_subItemMaster[$x]['subItemSerialNo'] = $i;
                        $data_subItemMaster[$x]['subItemCode'] = $itemCode . '/PV/' . $payVoucherDetailAutoID . '/' . $i;
                        $data_subItemMaster[$x]['uom'] = $uom;
                        $data_subItemMaster[$x]['uomID'] = $uomID;
                        $data_subItemMaster[$x]['wareHouseAutoID'] = $wareHouseAutoID;
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
            } else if ($count_subItemMaster == 0) {
                $data_subItemMaster = array();
                if ($qty > 0) {
                    $x = 0;
                    for ($i = 1; $i <= $qty; $i++) {
                        $data_subItemMaster[$x]['itemAutoID'] = $itemAutoID;
                        $data_subItemMaster[$x]['subItemSerialNo'] = $i;
                        $data_subItemMaster[$x]['subItemCode'] = $itemCode . '/' . $i;
                        $data_subItemMaster[$x]['uom'] = $uom;
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
        }


    }

    function getQty_subItemMaster_tmpTbl($itemAutoID, $masterID, $detailID)
    {

        $this->db->select('*');
        $this->db->where('itemAutoID', $itemAutoID);
        $this->db->where('receivedDocumentAutoID', $masterID);
        $this->db->where('receivedDocumentDetailID', $detailID);
        $this->db->from('srp_erp_itemmaster_subtemp');
        $r = $this->db->get()->result_array();
        return $r;
    }

    function delete_sub_itemMaster_existing($itemAutoID, $masterID, $detailID, $documentID)
    {
        $this->db->where('receivedDocumentID', $documentID);
        //$this->db->where('itemAutoID', $itemAutoID);
        $this->db->where('receivedDocumentAutoID', $masterID);
        $this->db->where('receivedDocumentDetailID', $detailID);
        $result = $this->db->delete('srp_erp_itemmaster_subtemp');
        return $result;


    }

    function batch_insert_srp_erp_itemmaster_subtemp($data)
    {
        $this->db->insert_batch('srp_erp_itemmaster_subtemp', $data);
    }

    function save_commission_base_items()
    {
        $this->db->trans_start();
        $InvoiceAutoID = array_values(array_diff($this->input->post('InvoiceAutoID'), array("null", "")));
        $exist = $this->db->query("SELECT * FROM srp_erp_paymentvoucherdetail WHERE payVoucherAutoId=" . $this->input->post('payVoucherAutoId') . " AND InvoiceAutoID IN(" . join(',', $InvoiceAutoID) . ")")->result_array();
        if (empty($exist)) {
            $amount = array_values(array_diff($this->input->post('amount'), array("null", "")));
            $due_amount = array_values(array_diff($this->input->post('due_amount'), array("null", "")));
            $this->db->select('srp_erp_salescommisionmaster.*,srp_erp_salespersonmaster.*,srp_erp_salescommisionperson.netCommision,srp_erp_salescommisionperson.salesPersonCurrencyExchangeRate');
            $this->db->from('srp_erp_salescommisionmaster');
            $this->db->join('srp_erp_salescommisionperson', 'srp_erp_salescommisionperson.salesCommisionID = srp_erp_salescommisionmaster.salesCommisionID', 'inner');
            $this->db->join('srp_erp_salespersonmaster', 'srp_erp_salescommisionperson.salesPersonID = srp_erp_salespersonmaster.salesPersonID', 'inner');
            $this->db->where_in('srp_erp_salescommisionmaster.salesCommisionID', $InvoiceAutoID);
            $this->db->where('srp_erp_salescommisionperson.salesPersonID', $this->input->post('salesPersonID'));
            $master_recode = $this->db->get()->result_array();

            for ($i = 0; $i < count($master_recode); $i++) {
                $data[$i]['payVoucherAutoId'] = $this->input->post('payVoucherAutoId');
                $data[$i]['salesCommissionID'] = $master_recode[$i]['salesCommisionID'];
                $data[$i]['salesPersonID'] = $master_recode[$i]['salesPersonID'];
                $data[$i]['type'] = 'SC';
                $data[$i]['bookingInvCode'] = $master_recode[$i]['salesCommisionCode'];
                $data[$i]['referenceNo'] = $master_recode[$i]['referenceNo'];
                $data[$i]['bookingDate'] = $master_recode[$i]['asOfDate'];
                $data[$i]['GLAutoID'] = $master_recode[$i]['receivableAutoID'];
                $data[$i]['systemGLCode'] = $master_recode[$i]['receivableSystemGLCode'];
                $data[$i]['GLCode'] = $master_recode[$i]['receivableGLAccount'];
                $data[$i]['GLDescription'] = $master_recode[$i]['receivableDescription'];
                $data[$i]['GLType'] = $master_recode[$i]['receivableType'];
                $data[$i]['description'] = null;
                $data[$i]['Invoice_amount'] = $master_recode[$i]['netCommision'];
                $data[$i]['due_amount'] = $due_amount[$i];
                $data[$i]['balance_amount'] = ($due_amount[$i] - (float)$amount[$i]);
                $data[$i]['transactionCurrencyID'] = $master_recode[$i]['transactionCurrencyID'];
                $data[$i]['transactionCurrency'] = $master_recode[$i]['transactionCurrency'];
                $data[$i]['transactionExchangeRate'] = $master_recode[$i]['transactionExchangeRate'];
                $data[$i]['transactionAmount'] = (float)$amount[$i];
                $data[$i]['companyLocalCurrencyID'] = $master_recode[$i]['companyLocalCurrencyID'];
                $data[$i]['companyLocalCurrency'] = $master_recode[$i]['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $master_recode[$i]['companyLocalExchangeRate'];
                $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['companyLocalExchangeRate']);
                $data[$i]['companyReportingCurrencyID'] = $master_recode[$i]['companyReportingCurrencyID'];
                $data[$i]['companyReportingCurrency'] = $master_recode[$i]['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $master_recode[$i]['companyReportingExchangeRate'];
                $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['companyReportingExchangeRate']);
                $data[$i]['partyCurrencyID'] = $master_recode[$i]['salesPersonCurrencyID'];
                $data[$i]['partyCurrency'] = $master_recode[$i]['salesPersonCurrency'];
                $data[$i]['partyExchangeRate'] = $master_recode[$i]['salesPersonCurrencyExchangeRate'];
                $data[$i]['partyAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['salesPersonCurrencyExchangeRate']);
                $data[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                $data[$i]['modifiedPCID'] = null;
                $data[$i]['modifiedUserID'] = null;
                $data[$i]['modifiedUserName'] = null;
                $data[$i]['modifiedDateTime'] = null;
                $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $data[$i]['createdPCID'] = $this->common_data['current_pc'];
                $data[$i]['createdUserID'] = $this->common_data['current_userID'];
                $data[$i]['createdUserName'] = $this->common_data['current_user'];
                $data[$i]['createdDateTime'] = $this->common_data['current_date'];
            }

            if (!empty($data)) {
                $this->db->insert_batch('srp_erp_paymentvoucherdetail', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('status' => false, 'message' => 'Sales Commission : Details Save Failed ' . $this->db->_error_message(), 'type' => 'e');
                } else {
                    $this->db->trans_commit();
                    return array('status' => true, 'message' => 'Sales Commission : ' . count($master_recode) . ' Item Details Saved Successfully.', 'type' => 's');
                }
            } else {
                return array('status' => false);
            }
        } else {
            return array('status' => false, 'message' => 'Sales Commission : Item detail already pulled to this document', 'type' => 'e');
        }
    }

    function re_open_commisionpayment()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId')));
        $this->db->update('srp_erp_paymentVouchermaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function re_open_payment_voucher()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId')));
        $this->db->update('srp_erp_paymentvouchermaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function re_open_payment_match()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('matchID', trim($this->input->post('matchID')));
        $this->db->update('srp_erp_pvadvancematch', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function fetch_payment_voucher_cheque_data($payVoucherAutoId)
    {

        $this->db->select('srp_erp_paymentvouchermaster.pvType as pvType');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchermaster');
        $type = $this->db->get()->row_array();
        if ($type['pvType'] == 'Direct' || $type['pvType'] == 'Employee') {
            $this->db->select('srp_erp_paymentvouchermaster.PVchequeDate as PVchequeDate,srp_erp_paymentvouchermaster.partyName,srp_erp_paymentvouchermaster.transactionCurrency,srp_erp_paymentvouchermaster.pvType as pvType,srp_erp_paymentvouchermaster.accountPayeeOnly as accountPayeeOnly,PVcode,PVNarration,transactionCurrencyDecimalPlaces');
            $this->db->where('payVoucherAutoId', $payVoucherAutoId);
            $this->db->from('srp_erp_paymentvouchermaster');
            $data['master'] = $this->db->get()->row_array();
        } else {
            $this->db->select('srp_erp_paymentvouchermaster.PVchequeDate as PVchequeDate,srp_erp_paymentvouchermaster.partyName,srp_erp_suppliermaster.nameOnCheque,srp_erp_paymentvouchermaster.transactionCurrency,srp_erp_paymentvouchermaster.pvType as pvType,srp_erp_paymentvouchermaster.accountPayeeOnly as accountPayeeOnly,PVcode,PVNarration,transactionCurrencyDecimalPlaces');
            $this->db->where('payVoucherAutoId', $payVoucherAutoId);
            $this->db->join('srp_erp_suppliermaster', 'srp_erp_paymentvouchermaster.partyID = srp_erp_suppliermaster.supplierAutoID');
            $this->db->from('srp_erp_paymentvouchermaster');
            $data['master'] = $this->db->get()->row_array();
        }


        $this->db->select('transactionAmount');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('transactionAmount');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Invoice');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['invoice'] = $this->db->get()->result_array();

        $this->db->select('transactionAmount');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'debitnote');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['debitnote'] = $this->db->get()->result_array();

        $this->db->select('sum(taxPercentage) as taxPercentage');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchertaxdetails');
        $data['tax'] = $this->db->get()->row_array();

        /*$this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Invoice');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['invoices'] = $this->db->get()->result_array();*/

        $data['invoices'] = $this->db->query("SELECT
	payVoucherDetailAutoID,
	payVoucherAutoId,
	srp_erp_paysupplierinvoicemaster.invoiceDate,
	srp_erp_paymentvoucherdetail.bookingInvCode AS invoiceCode,
	supplierInvoiceNo,
	SUM(srp_erp_paymentvoucherdetail.transactionAmount) AS transactionAmount,
	srp_erp_paymentvoucherdetail.transactionCurrencyDecimalPlaces
FROM
	`srp_erp_paymentvoucherdetail`
LEFT JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_paymentvoucherdetail.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
WHERE
	payVoucherAutoId = $payVoucherAutoId
AND type = 'Invoice'
GROUP BY
	srp_erp_paymentvoucherdetail.InvoiceAutoID")->result_array();

        $this->db->select('payVoucherDetailAutoID,payVoucherAutoId,description,transactionAmount,transactionCurrencyDecimalPlaces');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'GL');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['GLs'] = $this->db->get()->result_array();

        $this->db->select('payVoucherDetailAutoID,payVoucherAutoId,comment as description,transactionAmount,transactionCurrencyDecimalPlaces');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Item');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['Items'] = $this->db->get()->result_array();

        $this->db->select('payVoucherDetailAutoID,payVoucherAutoId,description,transactionAmount,transactionCurrencyDecimalPlaces');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Advance');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['Advances'] = $this->db->get()->result_array();

        $this->db->select('payVoucherDetailAutoID,payVoucherAutoId,description,transactionAmount,transactionCurrencyDecimalPlaces');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'debitnote');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['debitnote'] = $this->db->get()->result_array();

        $this->db->select('authourizedSignatureLevel');
        $this->db->join('srp_erp_chartofaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_paymentvouchermaster.PVbankCode', 'left');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchermaster');
        $data['signature'] = $this->db->get()->row_array();

        return $data;
    }

    function load_Cheque_templates($payVoucherAutoId)
    {
        $this->db->select('bankGLAutoID');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchermaster');
        $glid = $this->db->get()->row_array();

        $this->db->select('srp_erp_chartofaccountchequetemplates.coaChequeTemplateID,srp_erp_chartofaccountchequetemplates.pageLink,srp_erp_systemchequetemplates.Description');
        $this->db->where('companyID', current_companyID());
        $this->db->where('GLAutoID', $glid['bankGLAutoID']);
        $this->db->join('srp_erp_systemchequetemplates', 'srp_erp_chartofaccountchequetemplates.systemChequeTemplateID = srp_erp_systemchequetemplates.chequeTemplateID', 'left');
        $this->db->from('srp_erp_chartofaccountchequetemplates');
        $data = $this->db->get()->result_array();
        return $data;
    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'PV');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }

    function get_po_amount(){
        $payVoucherAutoId=$this->input->post('payVoucherAutoId');
        $pocode=$this->input->post('pocode');
        $companyID=current_companyID();

        $this->db->select('payVoucherDetailAutoID');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('companyID', $companyID);
        $this->db->where('type', 'Advance');
        $this->db->where('purchaseOrderID', $pocode);
        $this->db->from('srp_erp_paymentvoucherdetail');
        $poadded= $this->db->get()->row_array();
        if(!empty($poadded)){
            return array('w','PO already added');
            exit;
        }

        $sumTransactionAmount = $this->db->query("SELECT SUM(transactionAmount)AS totalTransactionAmount FROM srp_erp_paymentvoucherdetail WHERE purchaseOrderID = '" . $pocode . "' AND companyID = $companyID")->row_array();
        $sumTransactionAmountPO = $this->db->query("SELECT SUM(totalAmount)AS totalTransactionAmount FROM srp_erp_purchaseorderdetails WHERE purchaseOrderID = '" . $pocode . "' AND companyID = $companyID")->row_array();
        $previoussum=0;
        if(!empty($poadded)){
            $previoussum=$sumTransactionAmount['totalTransactionAmount'];
        }

        $balanceamnt=$sumTransactionAmountPO['totalTransactionAmount']-$previoussum;

        return array('s',$balanceamnt);
    }

    function get_supplier_banks(){
        $companyID=current_companyID();
        $supplierAutoID=$this->input->post('supplierID');
        $this->db->select('supplierBankMasterID,bankName');
        $this->db->where('supplierAutoID', $supplierAutoID);
        $this->db->where('companyID', $companyID);
        $this->db->from('srp_erp_supplierbankmaster');
        return $this->db->get()->result_array();
    }

    function fetch_payment_voucher_transfer_data($payVoucherAutoId){
        $this->db->select('srp_erp_paymentvouchermaster.pvType as pvType,supplierBankMasterID,partyID');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchermaster');
        $type = $this->db->get()->row_array();
        if ($type['pvType'] == 'Direct' || $type['pvType'] == 'Employee') {
            $this->db->select('*,srp_erp_chartofaccounts.bankName as bankName,srp_erp_chartofaccounts.bankAccountNumber as bankAccountNumber');
            $this->db->where('payVoucherAutoId', $payVoucherAutoId);
            $this->db->join('srp_erp_chartofaccounts', 'srp_erp_paymentvouchermaster.bankGLAutoID = srp_erp_chartofaccounts.GLAutoID');
            $this->db->from('srp_erp_paymentvouchermaster');
            $data['master'] = $this->db->get()->row_array();
        } else {
            $this->db->select('srp_erp_paymentvouchermaster.PVchequeDate as PVchequeDate,srp_erp_paymentvouchermaster.partyName,srp_erp_suppliermaster.nameOnCheque,srp_erp_paymentvouchermaster.transactionCurrency,srp_erp_paymentvouchermaster.pvType as pvType,srp_erp_paymentvouchermaster.accountPayeeOnly as accountPayeeOnly,srp_erp_paymentvouchermaster.*,srp_erp_chartofaccounts.bankName as bankName,srp_erp_chartofaccounts.bankAccountNumber as bankAccountNumber');
            $this->db->where('payVoucherAutoId', $payVoucherAutoId);
            $this->db->join('srp_erp_suppliermaster', 'srp_erp_paymentvouchermaster.partyID = srp_erp_suppliermaster.supplierAutoID');
            $this->db->join('srp_erp_chartofaccounts', 'srp_erp_paymentvouchermaster.bankGLAutoID = srp_erp_chartofaccounts.GLAutoID');
            $this->db->from('srp_erp_paymentvouchermaster');
            $data['master'] = $this->db->get()->row_array();
        }


        $this->db->select('nameOnCheque');
        $this->db->where('supplierAutoID', $type['partyID']);
        $this->db->from('srp_erp_suppliermaster');
        $data['supplier'] = $this->db->get()->row_array();

        $this->db->select('accountNumber,swiftCode,IbanCode,bankName,accountName,bankAddress');
        $this->db->where('supplierBankMasterID', $type['supplierBankMasterID']);
        $this->db->from('srp_erp_supplierbankmaster');
        $data['bank'] = $this->db->get()->row_array();

        $this->db->select('transactionAmount');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('transactionAmount');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Invoice');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['invoice'] = $this->db->get()->result_array();

        $this->db->select('transactionAmount');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'debitnote');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['debitnote'] = $this->db->get()->result_array();

        $this->db->select('sum(taxPercentage) as taxPercentage');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchertaxdetails');
        $data['tax'] = $this->db->get()->row_array();

        return $data;
    }

}