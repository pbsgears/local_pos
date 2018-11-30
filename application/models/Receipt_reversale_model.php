<?php

class Receipt_reversale_model extends ERP_Model
{

    function save_paymentreversal_header()
    {
        $this->db->trans_start();

        $date_format_policy = date_format_policy();
        $PaymentVoucherdate = $this->input->post('documentDate');
        $PVdate = input_format_date($PaymentVoucherdate, $date_format_policy);

        $supplierdetails = explode('|', trim($this->input->post('SupplierDetails')));
        $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear')));
        $bank = explode('|', trim($this->input->post('bank')));
        $currency_code = explode('|', trim($this->input->post('currency_code')));

        $FYBegin = input_format_date($financeyr[0], $date_format_policy);
        $FYEnd = input_format_date($financeyr[1], $date_format_policy);

        $data['PVbankCode'] = trim($this->input->post('PVbankCode'));
        $bank_detail = fetch_gl_account_desc($data['PVbankCode']);
        $data['documentID'] = 'PRVR';
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear'));
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear'));
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period'));
        $data['documentDate'] = trim($PVdate);
        $data['narration'] = trim_desc($this->input->post('narration'));
        $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
        $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
        $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
        $data['Type'] = trim($this->input->post('Type'));
        $data['referenceNo'] = trim_desc($this->input->post('referenceNo'));
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
        $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
        $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
        $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
        if ($data['Type'] == 'Direct') {
            $data['partyType'] = 'DIR';
            $data['partyName'] = trim($this->input->post('partyName'));
            $data['partyCurrencyID'] = $data['companyLocalCurrencyID'];
            $data['partyCurrency'] = $data['companyLocalCurrency'];
            $data['partyExchangeRate'] = $data['transactionExchangeRate'];
            $data['partyCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
        } elseif ($data['Type'] == 'Employee') {
            $emp_arr = $this->fetch_empyoyee($this->input->post('partyID'));
            $data['partyType'] = 'EMP';
            $data['partyID'] = trim($this->input->post('partyID'));
            $data['partyCode'] = $emp_arr['ECode'];
            $data['partyName'] = $emp_arr['Ename1'] . ' ' . $emp_arr['Ename2'] . ' ' . $emp_arr['Ename3'];
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
        } elseif ($data['Type'] == 'Supplier') {
            $supplier_arr = $this->fetch_supplier_data($this->input->post('partyID'));
            $data['partyType'] = 'SUP';
            $data['partyID'] = $this->input->post('partyID');
            //$data['partyCode'] = $supplier_arr['supplierSystemCode'];
            $data['partyName'] = $supplier_arr['supplierName'];
            //$data['partyAddress'] = $supplier_arr['supplierAddress1'] . ' ' . $supplier_arr['supplierAddress2'];
            //$data['partyTelephone'] = $supplier_arr['supplierTelephone'];
            //$data['partyFax'] = $supplier_arr['supplierFax'];
            // $data['partyEmail'] = $supplier_arr['supplierEmail'];
            $data['partyGLAutoID'] = $supplier_arr['liabilityAutoID'];
            // $data['partyGLCode'] = $supplier_arr['liabilitySystemGLCode'];
            $data['partyCurrencyID'] = $supplier_arr['supplierCurrencyID'];
            $data['partyCurrency'] = $supplier_arr['supplierCurrency'];
            $data['partyExchangeRate'] = $data['transactionExchangeRate'];
            $data['partyCurrencyDecimalPlaces'] = $supplier_arr['supplierCurrencyDecimalPlaces'];
        } elseif ($data['Type'] == 'SC') {
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

        if (trim($this->input->post('paymentReversalAutoID'))) {
            $this->db->where('paymentReversalAutoID', trim($this->input->post('paymentReversalAutoID')));
            $this->db->update('srp_erp_paymentreversalmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Payment Reversal Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Payment Reversal Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('paymentReversalAutoID'));
            }
        } else {
            /*$this->db->where('GLAutoID', $data['bankGLAutoID']);
            $this->db->update('srp_erp_chartofaccounts', array('bankCheckNumber' => $data['PVchequeNo']));*/
            $this->load->library('sequence');
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $type = substr($data['Type'], 0, 3);
            $data['documentSystemCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_paymentreversalmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Payment Reversal Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Payment Reversal Saved Successfully.');
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

    function load_payment_reversal_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate');
        $this->db->where('paymentReversalAutoID', $this->input->post('paymentReversalAutoID'));
        return $this->db->get('srp_erp_paymentreversalmaster')->row_array();
    }

    function fetch_PRVR_detail_table()
    {
        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces');
        $this->db->where('paymentReversalAutoID', trim($this->input->post('paymentReversalAutoID')));
        $this->db->from('srp_erp_paymentreversalmaster');
        $data['currency'] = $this->db->get()->row_array();

        $this->db->select('srp_erp_paymentreversaldetail.*,srp_erp_paymentvouchermaster.PVcode as PVcode,srp_erp_paymentvouchermaster.PVchequeNo as PVchequeNo,srp_erp_paymentvouchermaster.PVchequeDate as PVchequeDate');
        $this->db->where('paymentReversalAutoID', trim($this->input->post('paymentReversalAutoID')));
        $this->db->from('srp_erp_paymentreversaldetail');
        $this->db->join('srp_erp_paymentvouchermaster', 'srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentreversaldetail.pvAutoID');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function fetch_Pv_detail_table()
    {
        $this->db->select('*');
        $this->db->where('paymentReversalAutoID', trim($this->input->post('paymentReversalAutoID')));
        $this->db->from('srp_erp_paymentreversalmaster');
        $result = $this->db->get()->row_array();


        /*$this->db->select('srp_erp_paymentvouchermaster.*,sum(srp_erp_paymentvoucherdetail.transactionAmount) as Amount,ifnull(sum(srp_erp_paymentvouchertaxdetails.taxPercentage),0) as taxPercentage');
        $this->db->where('srp_erp_paymentvouchermaster.companyID ', current_companyID());

        $this->db->where('srp_erp_paymentvouchermaster.transactionCurrencyID ', $result['transactionCurrencyID']);
        $this->db->where('srp_erp_paymentvouchermaster.approvedYN ', 1);
        if($result['Type']=='Supplier'){
            $this->db->where('srp_erp_paymentvouchermaster.partyID ', $result['partyID']);
        }
        $this->db->where('srp_erp_paymentvouchermaster.bankGLAutoID ', $result['bankGLAutoID']);
        $this->db->where('srp_erp_paymentvouchermaster.PVdate  <=', $result['documentDate']);
        $this->db->where('srp_erp_bankledger.clearedYN', 0);
        $this->db->from('srp_erp_paymentvouchermaste');
        $this->db->join('srp_erp_paymentvoucherdetail', 'srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentvoucherdetail.payVoucherAutoId','left');
        $this->db->join('srp_erp_paymentvouchertaxdetails', 'srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentvouchertaxdetails.payVoucherAutoId','left');
        $this->db->join('srp_erp_bankledger', 'srp_erp_bankledger.documentMasterAutoID=srp_erp_paymentvouchermaster.payVoucherAutoId and srp_erp_bankledger.documentType=\'PV\'','left');
        $data['detail'] = $this->db->get()->result_array();*/
        $companyid = current_companyID();
        $transactionCurrencyID = $result['transactionCurrencyID'];
        $partyID = $result['partyID'];
        $bankGLAutoID = $result['bankGLAutoID'];
        $documentDate = $result['documentDate'];
        $Type = $result['Type'];
        $where = '';
        if ($Type == "Supplier") {
            $where = 'AND `srp_erp_paymentvouchermaster`.`partyID` = "' . $partyID . '"';
        }

        $data['detail'] = $this->db->query('SELECT
	`srp_erp_paymentvouchermaster`.*, pvd.amount,
	pvt.taxPercentage
FROM
	`srp_erp_paymentvouchermaster`
LEFT JOIN (
	SELECT
		sum(transactionAmount) AS amount,
		payVoucherAutoId
	FROM
		srp_erp_paymentvoucherdetail
	GROUP BY
		payVoucherAutoId
) AS pvd ON `srp_erp_paymentvouchermaster`.`payVoucherAutoId` = `pvd`.`payVoucherAutoId`
LEFT JOIN (
	SELECT
		ifnull(
			sum(
				srp_erp_paymentvouchertaxdetails.taxPercentage
			),
			0
		) AS taxPercentage,
		payVoucherAutoId
	FROM
		srp_erp_paymentvouchertaxdetails
	GROUP BY
		payVoucherAutoId
) AS pvt ON `srp_erp_paymentvouchermaster`.`payVoucherAutoId` = `pvt`.`payVoucherAutoId`
LEFT JOIN (
	SELECT
		*
	FROM
		srp_erp_bankledger
	WHERE
		documentType = "PV"
	AND clearedYN = 0
) AS bl ON `bl`.`documentMasterAutoID` = `srp_erp_paymentvouchermaster`.`payVoucherAutoId`
WHERE
	`srp_erp_paymentvouchermaster`.`companyID` = "' . $companyid . '"
AND `srp_erp_paymentvouchermaster`.`transactionCurrencyID` = "' . $transactionCurrencyID . '"
AND `srp_erp_paymentvouchermaster`.`approvedYN` = 1
' . $where . '
AND `srp_erp_paymentvouchermaster`.`bankGLAutoID` = "' . $bankGLAutoID . '"
AND `srp_erp_paymentvouchermaster`.`PVdate` <= "' . $documentDate . '"
AND srp_erp_paymentvouchermaster.payVoucherAutoId NOT IN (
    SELECT
        pvAutoID
    FROM
        srp_erp_paymentreversaldetail
    WHERE
        companyID = "' . $companyid . '"
)')->result_array();

        return $data;
    }

    function save_Payment_Reversale_detail()
    {
        $payVoucherAutoId = $this->input->post('checkboxprvr');
        $paymentReversalAutoID = $this->input->post('paymentReversalAutoID');
        foreach ($payVoucherAutoId as $val) {

            $master = $this->db->query('SELECT
	`srp_erp_paymentvouchermaster`.*, pvd.amount,
	pvt.taxPercentage
FROM
	`srp_erp_paymentvouchermaster`
LEFT JOIN (
	SELECT
		sum(transactionAmount) AS amount,
		payVoucherAutoId
	FROM
		srp_erp_paymentvoucherdetail
	GROUP BY
		payVoucherAutoId
) AS pvd ON `srp_erp_paymentvouchermaster`.`payVoucherAutoId` = `pvd`.`payVoucherAutoId`
LEFT JOIN (
	SELECT
		ifnull(
			sum(
				srp_erp_paymentvouchertaxdetails.taxPercentage
			),
			0
		) AS taxPercentage,
		payVoucherAutoId
	FROM
		srp_erp_paymentvouchertaxdetails
	GROUP BY
		payVoucherAutoId
) AS pvt ON `srp_erp_paymentvouchermaster`.`payVoucherAutoId` = `pvt`.`payVoucherAutoId`

WHERE
	`srp_erp_paymentvouchermaster`.`payVoucherAutoId` = "' . $val . '"')->row_array();

            $tax = ($master['amount'] / 100) * $master['taxPercentage'];
            $total = $master['amount'] + $tax;

            $data['paymentReversalAutoID'] = $paymentReversalAutoID;
            $data['pvAutoID'] = $val;
            $data['referenceNo'] = $master['referenceNo'];
            $data['pvDate'] = $master['PVdate'];
            $data['pvAmount'] = $total;
            $data['remarks'] = $master['PVNarration'];
            $data['transactionCurrencyID'] = $master['transactionCurrencyID'];
            $data['transactionCurrency'] = $master['transactionCurrency'];
            $data['transactionAmount'] = $master['amount'];
            $data['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
            $data['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
            $data['companyLocalCurrency'] = $master['companyLocalCurrency'];
            $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data['companyLocalAmount'] = $master['companyLocalAmount'];
            $data['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
            $data['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
            $data['companyReportingCurrency'] = $master['companyReportingCurrency'];
            $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data['companyReportingAmount'] = $master['companyReportingAmount'];
            $data['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
            $data['partyCurrencyID'] = $master['partyCurrencyID'];
            $data['partyCurrency'] = $master['partyCurrency'];
            $data['partyExchangeRate'] = $master['partyExchangeRate'];
            $data['partyAmount'] = $master['amount'];
            $data['partyCurrencyDecimalPlaces'] = $master['partyCurrencyDecimalPlaces'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $results = $this->db->insert('srp_erp_paymentreversaldetail', $data);
        }
        if ($results) {
            return array('s', 'Records successfully added');
        } else {
            return array('e', 'Records adding failed');
        }
    }

    function delete_payment_reversale_detail()
    {
        $this->db->delete('srp_erp_paymentreversaldetail', array('paymentReversalDetailID' => trim($this->input->post('paymentReversalDetailID'))));
        return array('s', 'Records successfully deleted');
    }

    function fetch_template_data($paymentReversalAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('paymentReversalAutoID,transactionCurrency,transactionCurrencyDecimalPlaces,documentSystemCode, DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate,referenceNo,partyName,narration,confirmedByName,confirmedYN,DATE_FORMAT(confirmedDate,\'' . $convertFormat . '\') AS confirmedDate,approvedbyEmpID,approvedbyEmpName,approvedYN,DATE_FORMAT(approvedDate,\'' . $convertFormat . '\') AS approvedDate');
        $this->db->where('paymentReversalAutoID', $paymentReversalAutoID);
        $this->db->from('srp_erp_paymentreversalmaster');
        $data['master'] = $this->db->get()->row_array();

        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('pvAutoID,srp_erp_paymentreversaldetail.referenceNo,srp_erp_paymentreversaldetail.pvDate,pvAmount,remarks,srp_erp_paymentreversaldetail.transactionCurrencyDecimalPlaces,srp_erp_paymentreversaldetail.transactionCurrency,srp_erp_paymentvouchermaster.PVcode as PVcode,srp_erp_paymentvouchermaster.PVchequeNo as PVchequeNo,DATE_FORMAT(srp_erp_paymentvouchermaster.PVchequeDate,\'' . $convertFormat . '\') AS PVchequeDate');
        $this->db->where('paymentReversalAutoID', $paymentReversalAutoID);
        $this->db->join('srp_erp_paymentvouchermaster', 'srp_erp_paymentreversaldetail.pvAutoID = srp_erp_paymentvouchermaster.payVoucherAutoId');
        $this->db->from('srp_erp_paymentreversaldetail');
        $data['detail'] = $this->db->get()->result_array();


        $this->db->select('approvedYN, approvedDate, approvalLevelID,Ename1,Ename2,Ename3,Ename4');
        $this->db->where('documentSystemCode', $paymentReversalAutoID);
        $this->db->where('documentID', 'PRVR');
        $this->db->from('srp_erp_documentapproved');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.ECode = srp_erp_documentapproved.approvedEmpID');
        $data['approval'] = $this->db->get()->result_array();
        return $data;
    }

    function delete_payment_reversal()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_paymentreversaldetail');
        $this->db->where('paymentReversalAutoID', trim($this->input->post('paymentReversalAutoID')));
        $datas = $this->db->get()->row_array();
        if ($datas) {
            $this->session->set_flashdata('e', 'please delete all detail records before deleting this document.');
            return true;
        } else {
            $data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('paymentReversalAutoID', trim($this->input->post('paymentReversalAutoID')));
            $this->db->update('srp_erp_paymentreversalmaster', $data);
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;

        }
    }

    function reverse_receiptVoucher()
    {
        $rvautoid = $this->input->post('receiptVoucherAutoId');
        $this->db->select('*');
        $this->db->from('srp_erp_customerreceiptmaster');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId')));
        $rvmaster = $this->db->get()->row_array();

        $this->db->select('sum(transactionAmount) as transactionAmount,sum(companyLocalAmount) as companyLocalAmount,sum(companyReportingAmount) as companyReportingAmount');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId')));
        $rvdetail = $this->db->get()->row_array();

        $rvdetailSupp = $this->db->query('SELECT
	sum(transactionAmount) as transactionAmount,sum(companyLocalAmount) as companyLocalAmount,sum(companyReportingAmount) as companyReportingAmount,sum(unittransactionAmount) as unittransactionAmount,sum(customerAmount) as customerAmount
FROM srp_erp_customerreceiptdetail
WHERE srp_erp_customerreceiptdetail.receiptVoucherAutoId = '.$rvautoid.'
AND (type = "Invoice" OR type = "Advance" OR type = "debitnote")')->row_array();

        $companyid = current_companyID();


        $date_format_policy = date_format_policy();
        $date = $this->input->post('reversalDate');
        $reversalDate = input_format_date($date, $date_format_policy);

        $str = $reversalDate;
        $dat=explode("-",$str);
        $year=$dat[0];
        $month=$dat[1];

        $companyFinanceYearID = $this->db->query('SELECT
    companyFinanceYearID,companyFinancePeriodID,dateFrom,dateTo
FROM
    srp_erp_companyfinanceperiod
WHERE
    companyID = ' . $companyid . '
AND isActive = 1
AND (
    "' . $reversalDate . '" BETWEEN dateFrom
    AND dateTo
)')->row_array();
        $invoiceGlDetails = $this->db->query('SELECT
	SUM(srp_erp_customerreceiptdetail.transactionAmount) as Amount,SUM(srp_erp_customerreceiptdetail.companyLocalAmount) as compLocalAmount,SUM(srp_erp_customerreceiptdetail.companyReportingAmount) as compReportingAmount,srp_erp_customermaster.receivableAutoID,srp_erp_customermaster.receivableSystemGLCode,srp_erp_customermaster.receivableGLAccount,srp_erp_customermaster.receivableDescription,srp_erp_customermaster.receivableType,srp_erp_chartofaccounts.GLAutoID,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.systemAccountCode,srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.subCategory
FROM srp_erp_customerreceiptmaster
LEFT JOIN srp_erp_customerreceiptdetail on srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
LEFT JOIN srp_erp_customermaster on srp_erp_customerreceiptmaster.customerID = srp_erp_customermaster.customerAutoID
LEFT JOIN srp_erp_chartofaccounts on srp_erp_customermaster.receivableAutoID = srp_erp_chartofaccounts.GLAutoID
WHERE srp_erp_customerreceiptmaster.receiptVoucherAutoId = '.$rvautoid.'
AND (type = "Invoice" OR type = "Advance" OR type = "debitnote")')->row_array();


        $ItemGlDetails = $this->db->query('SELECT
	SUM(srp_erp_customerreceiptdetail.transactionAmount) as Amount,SUM(srp_erp_customerreceiptdetail.companyLocalAmount) as compLocalAmount,SUM(srp_erp_customerreceiptdetail.companyReportingAmount) as compReportingAmount,srp_erp_chartofaccounts.GLAutoID,addondet.taxPercentage as taxPercentage,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.systemAccountCode,srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.subCategory
FROM srp_erp_customerreceiptmaster
LEFT JOIN srp_erp_customerreceiptdetail on srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
LEFT JOIN srp_erp_customermaster on srp_erp_customerreceiptmaster.customerID = srp_erp_customermaster.customerAutoID
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		receiptVoucherAutoId
	FROM
		srp_erp_customerreceipttaxdetails
	GROUP BY
		receiptVoucherAutoId
) addondet ON (
	`addondet`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId
)
LEFT JOIN srp_erp_companycontrolaccounts on srp_erp_companycontrolaccounts.controlAccountType = "RRVR"
LEFT JOIN srp_erp_chartofaccounts on srp_erp_companycontrolaccounts.GLAutoID = srp_erp_chartofaccounts.GLAutoID
WHERE srp_erp_customerreceiptmaster.receiptVoucherAutoId = '.$rvautoid.'
AND srp_erp_companycontrolaccounts.companyID='.$companyid.'
AND (type = "GL" OR type = "Item")')->row_array();

        $bankGlBankLeager = $this->db->query('SELECT
	SUM(srp_erp_customerreceiptdetail.transactionAmount) as Amount,SUM(srp_erp_customerreceiptdetail.companyLocalAmount) as compLocalAmount,SUM(srp_erp_customerreceiptdetail.companyReportingAmount) as compReportingAmount,addondet.taxPercentage as taxPercentage,srp_erp_chartofaccounts.GLAutoID,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.systemAccountCode,srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.subCategory
FROM srp_erp_customerreceiptmaster
LEFT JOIN srp_erp_customerreceiptdetail on srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		receiptVoucherAutoId
	FROM
		srp_erp_customerreceipttaxdetails
	GROUP BY
		receiptVoucherAutoId
) addondet ON (
	`addondet`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId
)
LEFT JOIN srp_erp_chartofaccounts on srp_erp_customerreceiptmaster.bankGLAutoID = srp_erp_chartofaccounts.GLAutoID
WHERE srp_erp_customerreceiptmaster.receiptVoucherAutoId = '.$rvautoid.'')->row_array();

        if (!empty($companyFinanceYearID)) {

            $this->load->library('sequence');
            if ($rvmaster) {
                $data['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId'));
                $data['Type'] = $rvmaster['RVType'];
                $data['documentID'] = 'RRVR';
                $data['documentDate'] = $reversalDate;
                $data['referenceNo'] = $rvmaster['referanceNo'];
                $data['narration'] = $this->input->post('comments');
                $data['companyFinanceYearID'] = $companyFinanceYearID['companyFinanceYearID'];

                $data['RVbankCode'] = $rvmaster['RVbankCode'];
                $data['bankGLAutoID'] = $rvmaster['bankGLAutoID'];
                $data['partyType'] = "CUS";
                $data['partyID'] = $rvmaster['customerID'];
                $data['partyName'] = $rvmaster['customerName'];
                $data['partyGLAutoID'] = $rvmaster['customerreceivableAutoID'];
                $data['companyID'] = current_companyID();
                $data['companyCode'] = $this->common_data['company_data']['company_code'];

                $data['documentSystemCode'] = $this->sequence->sequence_generator('RRVR');
                $payReversalMaster = $this->db->insert('srp_erp_receiptreversalmaster', $data);
                $receiptReversalAutoID = $this->db->insert_id();
                if ($payReversalMaster) {
                    if ($rvmaster['RVType'] == 'Invoices') {
                        $PVdata['documentID'] = 'PV';
                        $PVdata['rrvrID'] = $receiptReversalAutoID;
                        $PVdata['PVdate'] = $reversalDate;
                        $PVdata['pvType'] = 'Direct';
                        $PVdata['companyFinanceYearID'] = $companyFinanceYearID['companyFinanceYearID'];
                        $PVdata['FYPeriodDateFrom'] = $companyFinanceYearID['dateFrom'];
                        $PVdata['FYPeriodDateTo'] = $companyFinanceYearID['dateTo'];
                        $PVdata['companyFinancePeriodID'] = $companyFinanceYearID['companyFinancePeriodID'];
                        $PVdata['modeOfPayment'] = $rvmaster['modeOfPayment'];
                        $PVdata['PVbankCode'] = $rvmaster['RVbankCode'];
                        $PVdata['PVbankSwiftCode'] = $rvmaster['RVbankSwiftCode'];
                        $PVdata['bankGLAutoID'] = $rvmaster['bankGLAutoID'];
                        $PVdata['bankSystemAccountCode'] = $rvmaster['bankSystemAccountCode'];
                        $PVdata['bankGLSecondaryCode'] = $rvmaster['bankGLSecondaryCode'];
                        $PVdata['PVbank'] = $rvmaster['RVbank'];
                        $PVdata['PVbankAccount'] = $rvmaster['RVbankAccount'];
                        $PVdata['PVbankBranch'] = $rvmaster['RVbankBranch'];
                        $PVdata['PVbankType'] = $rvmaster['RVbankType'];
                        $PVdata['PVchequeNo'] = $rvmaster['RVchequeNo'];
                        $PVdata['bankCurrencyID'] = $rvmaster['bankCurrencyID'];
                        $PVdata['bankCurrency'] = $rvmaster['bankCurrency'];
                        $PVdata['bankCurrencyExchangeRate'] = $rvmaster['bankCurrencyExchangeRate'];
                        $PVdata['bankCurrencyAmount'] = ($rvdetail['transactionAmount'] / $rvmaster['bankCurrencyExchangeRate']);
                        $PVdata['bankCurrencyDecimalPlaces'] = $rvmaster['bankCurrencyDecimalPlaces'];
                        $PVdata['PVchequeDate'] = $rvmaster['RVchequeDate'];
                        $PVdata['PVNarration'] = 'Receipt reversal of receipt voucher' . $rvmaster['RVcode'];
                        $PVdata['partyType'] = 'DIR';
                        $PVdata['partyID'] = $rvmaster['customerID'];
                        $PVdata['partyCode'] = $rvmaster['customerSystemCode'];
                        $PVdata['partyName'] = $rvmaster['customerName'];
                        $PVdata['partyAddress'] = $rvmaster['customerAddress'];
                        $PVdata['partyTelephone'] = $rvmaster['customerTelephone'];
                        $PVdata['partyFax'] = $rvmaster['customerFax'];
                        $PVdata['partyEmail'] = $rvmaster['customerEmail'];
                        $PVdata['transactionCurrencyID'] = $rvmaster['transactionCurrencyID'];
                        $PVdata['transactionCurrency'] = $rvmaster['transactionCurrency'];
                        $PVdata['transactionAmount'] = $rvdetail['transactionAmount'];
                        $PVdata['transactionCurrencyDecimalPlaces'] = $rvmaster['transactionCurrencyDecimalPlaces'];
                        $PVdata['companyLocalCurrencyID'] = $rvmaster['companyLocalCurrencyID'];
                        $PVdata['companyLocalCurrency'] = $rvmaster['companyLocalCurrency'];
                        $PVdata['companyLocalExchangeRate'] = $rvmaster['companyLocalExchangeRate'];
                        $PVdata['companyLocalAmount'] = $rvdetail['companyLocalAmount'];
                        $PVdata['companyLocalCurrencyDecimalPlaces'] = $rvmaster['companyLocalCurrencyDecimalPlaces'];
                        $PVdata['companyReportingCurrencyID'] = $rvmaster['companyReportingCurrencyID'];
                        $PVdata['companyReportingCurrency'] = $rvmaster['companyReportingCurrency'];
                        $PVdata['companyReportingExchangeRate'] = $rvmaster['companyReportingExchangeRate'];
                        $PVdata['companyReportingAmount'] = $rvdetail['companyReportingAmount'];
                        $PVdata['companyReportingCurrencyDecimalPlaces'] = $rvmaster['companyReportingCurrencyDecimalPlaces'];
                        $PVdata['approvedYN'] = 1;
                        $PVdata['approvedDate'] = current_date();
                        $PVdata['confirmedYN'] = 1;
                        $PVdata['approvedbyEmpID'] = $this->common_data['current_userID'];
                        $PVdata['approvedbyEmpName'] = $this->common_data['current_user'];
                        $PVdata['companyCode'] = $this->common_data['company_data']['company_code'];
                        $PVdata['companyID'] = $this->common_data['company_data']['company_id'];
                        $PVdata['createdUserGroup'] = $this->common_data['user_group'];
                        $PVdata['createdPCID'] = $this->common_data['current_pc'];
                        $PVdata['createdUserID'] = $this->common_data['current_userID'];
                        $PVdata['createdUserName'] = $this->common_data['current_user'];
                        $PVdata['createdDateTime'] = $this->common_data['current_date'];

                        $PVdata['PVcode'] = $this->sequence->sequence_generator('PV');
                        $payPaymentVoucherM = $this->db->insert('srp_erp_paymentvouchermaster', $PVdata);
                        $payVoucherAutoId = $this->db->insert_id();
                        if ($payPaymentVoucherM) {
                            $PVDdata['payVoucherAutoId'] = $payVoucherAutoId;
                            $PVDdata['type'] = 'RRVR';
                            $PVDdata['transactionAmount'] = $rvdetail['transactionAmount'];
                            $PVDdata['companyLocalAmount'] = $rvdetail['companyLocalAmount'];
                            $PVDdata['companyReportingAmount'] = $rvdetail['companyReportingAmount'];
                            $PVDdata['companyCode'] = $this->common_data['company_data']['company_code'];
                            $PVDdata['companyID'] = $this->common_data['company_data']['company_id'];
                            $PVDdata['createdUserGroup'] = $this->common_data['user_group'];
                            $PVDdata['createdPCID'] = $this->common_data['current_pc'];
                            $PVDdata['createdUserID'] = $this->common_data['current_userID'];
                            $PVDdata['createdUserName'] = $this->common_data['current_user'];
                            $PVDdata['createdDateTime'] = $this->common_data['current_date'];

                            $payVouvherD = $this->db->insert('srp_erp_paymentvoucherdetail', $PVDdata);
                            if ($payVouvherD) {
                                $CINVdata['documentID'] = 'CINV';
                                $CINVdata['invoiceType'] = 'Direct';
                                $CINVdata['rrvrID'] = $receiptReversalAutoID;
                                $CINVdata['customerInvoiceDate'] = $reversalDate;
                                $CINVdata['invoiceDate'] = $reversalDate;
                                $CINVdata['invoiceDueDate'] = $reversalDate;
                                $CINVdata['invoiceNarration'] = 'Receipt reversal of receipt voucher' . $rvmaster['RVcode'];
                                $CINVdata['bankGLAutoID'] = $rvmaster['bankGLAutoID'];
                                $CINVdata['bankSystemAccountCode'] = $rvmaster['bankSystemAccountCode'];
                                $CINVdata['bankGLSecondaryCode'] = $rvmaster['bankGLSecondaryCode'];
                                $CINVdata['bankCurrencyID'] = $rvmaster['bankCurrencyID'];
                                $CINVdata['bankCurrency'] = $rvmaster['bankCurrency'];
                                $CINVdata['companyFinanceYearID'] = $companyFinanceYearID['companyFinanceYearID'];
                                $CINVdata['FYPeriodDateFrom'] = $companyFinanceYearID['dateFrom'];
                                $CINVdata['FYPeriodDateTo'] = $companyFinanceYearID['dateTo'];
                                $CINVdata['companyFinancePeriodID'] = $companyFinanceYearID['companyFinancePeriodID'];
                                $CINVdata['customerID'] = $rvmaster['customerID'];
                                $CINVdata['customerSystemCode'] = $rvmaster['customerSystemCode'];
                                $CINVdata['customerName'] = $rvmaster['customerName'];
                                $CINVdata['customerAddress'] = $rvmaster['customerAddress'];
                                $CINVdata['customerTelephone'] = $rvmaster['customerTelephone'];
                                $CINVdata['customerFax'] = $rvmaster['customerFax'];
                                $CINVdata['customerEmail'] = $rvmaster['customerEmail'];
                                $CINVdata['customerReceivableAutoID'] = $rvmaster['customerreceivableAutoID'];
                                $CINVdata['customerReceivableSystemGLCode'] = $rvmaster['customerreceivableSystemGLCode'];
                                $CINVdata['customerReceivableGLAccount'] = $rvmaster['customerreceivableGLAccount'];
                                $CINVdata['customerReceivableDescription'] = $rvmaster['customerreceivableDescription'];
                                $CINVdata['customerReceivableType'] = $rvmaster['customerreceivableType'];
                                $CINVdata['transactionCurrencyID'] = $rvmaster['transactionCurrencyID'];
                                $CINVdata['transactionCurrency'] = $rvmaster['transactionCurrency'];
                                $CINVdata['transactionExchangeRate'] = $rvmaster['transactionExchangeRate'];
                                $CINVdata['transactionAmount'] = $rvdetailSupp['transactionAmount'];
                                $CINVdata['transactionCurrencyDecimalPlaces'] = $rvmaster['transactionCurrencyDecimalPlaces'];
                                $CINVdata['companyLocalCurrencyID'] = $rvmaster['companyLocalCurrencyID'];
                                $CINVdata['companyLocalCurrency'] = $rvmaster['companyLocalCurrency'];
                                $CINVdata['companyLocalExchangeRate'] = $rvmaster['companyLocalExchangeRate'];
                                $CINVdata['companyLocalAmount'] = $rvdetailSupp['companyLocalAmount'];
                                $CINVdata['companyLocalCurrencyDecimalPlaces'] = $rvmaster['companyLocalCurrencyDecimalPlaces'];
                                $CINVdata['companyReportingCurrencyID'] = $rvmaster['companyReportingCurrencyID'];
                                $CINVdata['companyReportingCurrency'] = $rvmaster['companyReportingCurrency'];
                                $CINVdata['companyReportingExchangeRate'] = $rvmaster['companyReportingExchangeRate'];
                                $CINVdata['companyReportingAmount'] = $rvdetailSupp['companyReportingAmount'];
                                $CINVdata['companyReportingCurrencyDecimalPlaces'] = $rvmaster['companyReportingCurrencyDecimalPlaces'];
                                $CINVdata['customerCurrencyID'] = $rvmaster['customerCurrencyID'];
                                $CINVdata['customerCurrency'] = $rvmaster['customerCurrency'];
                                $CINVdata['customerCurrencyExchangeRate'] = $rvmaster['customerExchangeRate'];
                                $CINVdata['customerCurrencyAmount'] = $rvmaster['customerCurrencyAmount'];
                                $CINVdata['customerCurrencyDecimalPlaces'] = $rvmaster['customerCurrencyDecimalPlaces'];
                                $CINVdata['approvedYN'] = 1;
                                $CINVdata['approvedbyEmpID'] = $this->common_data['current_userID'];
                                $CINVdata['approvedbyEmpName'] = $this->common_data['current_user'];
                                $CINVdata['approvedDate'] = current_date();
                                $CINVdata['confirmedYN'] = 1;
                                $CINVdata['confirmedByEmpID'] = $this->common_data['current_userID'];
                                $CINVdata['confirmedByName'] = $this->common_data['current_user'];
                                $CINVdata['confirmedDate'] = current_date();
                                $CINVdata['companyCode'] = $this->common_data['company_data']['company_code'];
                                $CINVdata['companyID'] = $this->common_data['company_data']['company_id'];
                                $CINVdata['createdUserGroup'] = $this->common_data['user_group'];
                                $CINVdata['createdPCID'] = $this->common_data['current_pc'];
                                $CINVdata['createdUserID'] = $this->common_data['current_userID'];
                                $CINVdata['createdUserName'] = $this->common_data['current_user'];
                                $CINVdata['createdDateTime'] = $this->common_data['current_date'];

                                $CINVdata['invoiceCode'] = $this->sequence->sequence_generator('CINV');
                                $payCustomerInvM = $this->db->insert('srp_erp_customerinvoicemaster', $CINVdata);
                                $InvoiceAutoID = $this->db->insert_id();
                                if ($payCustomerInvM) {
                                    $CINVDdata['invoiceAutoID'] = $InvoiceAutoID;
                                    $CINVDdata['type'] = 'GL';
                                    $CINVDdata['description'] = 'Receipt reversal of receipt voucher' . $rvmaster['RVcode'];
                                    $CINVDdata['transactionAmount'] = $rvdetailSupp['transactionAmount'];
                                    $CINVDdata['companyLocalAmount'] = $rvdetailSupp['companyLocalAmount'];
                                    $CINVDdata['companyReportingAmount'] = $rvdetailSupp['companyReportingAmount'];
                                    $CINVDdata['unittransactionAmount'] = $rvdetailSupp['unittransactionAmount'];
                                    $CINVDdata['customerAmount'] = $rvdetailSupp['customerAmount'];
                                    $CINVDdata['companyCode'] = $this->common_data['company_data']['company_code'];
                                    $CINVDdata['companyID'] = $this->common_data['company_data']['company_id'];
                                    $CINVDdata['createdUserGroup'] = $this->common_data['user_group'];
                                    $CINVDdata['createdPCID'] = $this->common_data['current_pc'];
                                    $CINVDdata['createdUserID'] = $this->common_data['current_userID'];
                                    $CINVDdata['createdUserName'] = $this->common_data['current_user'];
                                    $CINVDdata['createdDateTime'] = $this->common_data['current_date'];

                                    $result = $this->db->insert('srp_erp_customerinvoicedetails', $CINVDdata);
                                    if ($result) {

                                        if($invoiceGlDetails){
                                            $invGLLia['documentCode'] = 'RRVR';
                                            $invGLLia['documentMasterAutoID'] = $receiptReversalAutoID;
                                            $invGLLia['documentSystemCode'] = $data['documentSystemCode'];
                                            $invGLLia['documentType'] = $rvmaster['RVType'];
                                            $invGLLia['documentDate'] = $reversalDate;
                                            $invGLLia['documentYear'] = $year;
                                            $invGLLia['documentMonth'] = $month;
                                            $invGLLia['documentNarration'] = 'Receipt reversal of receipt voucher' . $rvmaster['RVcode'];
                                            $invGLLia['GLAutoID'] = $invoiceGlDetails['GLAutoID'];
                                            $invGLLia['systemGLCode'] = $invoiceGlDetails['systemAccountCode'];
                                            $invGLLia['GLCode'] = $invoiceGlDetails['GLSecondaryCode'];
                                            $invGLLia['GLDescription'] = $invoiceGlDetails['GLDescription'];
                                            $invGLLia['GLType'] = $invoiceGlDetails['subCategory'];
                                            $invGLLia['amount_type'] = 'dr';

                                            $invGLLia['transactionCurrencyID'] = $rvmaster['transactionCurrencyID'];
                                            $invGLLia['transactionCurrency'] = $rvmaster['transactionCurrency'];
                                            $invGLLia['transactionExchangeRate'] = $rvmaster['transactionExchangeRate'];
                                            $invGLLia['transactionAmount'] = $invoiceGlDetails['Amount'];
                                            $invGLLia['transactionCurrencyDecimalPlaces'] = $rvmaster['transactionCurrencyDecimalPlaces'];

                                            $invGLLia['companyLocalCurrencyID'] = $rvmaster['companyLocalCurrencyID'];
                                            $invGLLia['companyLocalCurrency'] = $rvmaster['companyLocalCurrency'];
                                            $invGLLia['companyLocalExchangeRate'] = $rvmaster['companyLocalExchangeRate'];
                                            $invGLLia['companyLocalAmount'] = $invoiceGlDetails['compLocalAmount'];
                                            $invGLLia['companyLocalCurrencyDecimalPlaces'] = $rvmaster['companyLocalCurrencyDecimalPlaces'];

                                            $invGLLia['companyReportingCurrencyID'] = $rvmaster['companyReportingCurrencyID'];
                                            $invGLLia['companyReportingCurrency'] = $rvmaster['companyReportingCurrency'];
                                            $invGLLia['companyReportingExchangeRate'] = $rvmaster['companyReportingExchangeRate'];
                                            $invGLLia['companyReportingAmount'] = $invoiceGlDetails['compReportingAmount'];
                                            $invGLLia['companyReportingCurrencyDecimalPlaces'] = $rvmaster['companyReportingCurrencyDecimalPlaces'];
                                            $invGLLia['partyAutoID'] = $rvmaster['customerID'];
                                            $invGLLia['partyName'] = $rvmaster['customerName'];
                                            $invGLLia['subLedgerType'] = 3;
                                            $invGLLia['subLedgerDesc'] = 'AR';
                                            $invGLLia['confirmedByEmpID'] = $this->common_data['current_userID'];
                                            $invGLLia['confirmedByName'] = $this->common_data['current_user'];
                                            $invGLLia['confirmedDate'] = $this->common_data['current_date'];
                                            $invGLLia['approvedDate'] = $this->common_data['current_date'];
                                            $invGLLia['approvedbyEmpID'] = $this->common_data['current_userID'];
                                            $invGLLia['approvedbyEmpName'] = $this->common_data['current_user'];
                                            $invGLLia['companyCode'] = $this->common_data['company_data']['company_code'];
                                            $invGLLia['companyID'] = $this->common_data['company_data']['company_id'];
                                            $invGLLia['createdUserGroup'] = $this->common_data['user_group'];
                                            $invGLLia['createdPCID'] = $this->common_data['current_pc'];
                                            $invGLLia['createdUserID'] = $this->common_data['current_userID'];
                                            $invGLLia['createdUserName'] = $this->common_data['current_user'];
                                            $invGLLia['createdDateTime'] = $this->common_data['current_date'];
                                            $this->db->insert('srp_erp_generalledger', $invGLLia);
                                        }



                                        if($ItemGlDetails['GLAutoID']){
                                            $invGL['documentCode'] = 'RRVR';
                                            $invGL['documentMasterAutoID'] = $receiptReversalAutoID;
                                            $invGL['documentSystemCode'] = $data['documentSystemCode'];
                                            $invGL['documentType'] = $rvmaster['RVType'];
                                            $invGL['documentDate'] = $reversalDate;
                                            $invGL['documentYear'] = $year;
                                            $invGL['documentMonth'] = $month;
                                            $invGL['documentNarration'] = 'Receipt reversal of receipt voucher' . $rvmaster['RVcode'];
                                            $invGL['GLAutoID'] = $ItemGlDetails['GLAutoID'];
                                            $invGL['systemGLCode'] = $ItemGlDetails['systemAccountCode'];
                                            $invGL['GLCode'] = $ItemGlDetails['GLSecondaryCode'];
                                            $invGL['GLDescription'] = $ItemGlDetails['GLDescription'];
                                            $invGL['GLType'] = $ItemGlDetails['subCategory'];
                                            $invGL['amount_type'] = 'dr';

                                            $invGL['transactionCurrencyID'] = $rvmaster['transactionCurrencyID'];
                                            $invGL['transactionCurrency'] = $rvmaster['transactionCurrency'];
                                            $invGL['transactionExchangeRate'] = $rvmaster['transactionExchangeRate'];
                                            if($ItemGlDetails['taxPercentage']== null){
                                                $taxPercentage=0;
                                            }else{
                                                $taxPercentage=$ItemGlDetails['taxPercentage'];
                                            }
                                            $itemTaxamount=($ItemGlDetails['Amount']/100)*$taxPercentage;
                                            $invGL['transactionAmount'] = $ItemGlDetails['Amount']+$itemTaxamount;
                                            $invGL['transactionCurrencyDecimalPlaces'] = $rvmaster['transactionCurrencyDecimalPlaces'];

                                            $invGL['companyLocalCurrencyID'] = $rvmaster['companyLocalCurrencyID'];
                                            $invGL['companyLocalCurrency'] = $rvmaster['companyLocalCurrency'];
                                            $invGL['companyLocalExchangeRate'] = $rvmaster['companyLocalExchangeRate'];
                                            $invGL['companyLocalAmount'] = $ItemGlDetails['compLocalAmount']+$itemTaxamount;
                                            $invGL['companyLocalCurrencyDecimalPlaces'] = $rvmaster['companyLocalCurrencyDecimalPlaces'];

                                            $invGL['companyReportingCurrencyID'] = $rvmaster['companyReportingCurrencyID'];
                                            $invGL['companyReportingCurrency'] = $rvmaster['companyReportingCurrency'];
                                            $invGL['companyReportingExchangeRate'] = $rvmaster['companyReportingExchangeRate'];
                                            $invGL['companyReportingAmount'] = $ItemGlDetails['compReportingAmount']+$itemTaxamount;
                                            $invGL['companyReportingCurrencyDecimalPlaces'] = $rvmaster['companyReportingCurrencyDecimalPlaces'];
                                            $invGL['partyAutoID'] = $rvmaster['customerID'];
                                            $invGL['partyName'] = $rvmaster['customerName'];
                                            /*$invGL['subLedgerType'] = 2;
                                            $invGL['subLedgerDesc'] = 'AP';*/
                                            $invGL['confirmedByEmpID'] = $this->common_data['current_userID'];
                                            $invGL['confirmedByName'] = $this->common_data['current_user'];
                                            $invGL['confirmedDate'] = $this->common_data['current_date'];
                                            $invGL['approvedDate'] = $this->common_data['current_date'];
                                            $invGL['approvedbyEmpID'] = $this->common_data['current_userID'];
                                            $invGL['approvedbyEmpName'] = $this->common_data['current_user'];
                                            $invGL['companyCode'] = $this->common_data['company_data']['company_code'];
                                            $invGL['companyID'] = $this->common_data['company_data']['company_id'];
                                            $invGL['createdUserGroup'] = $this->common_data['user_group'];
                                            $invGL['createdPCID'] = $this->common_data['current_pc'];
                                            $invGL['createdUserID'] = $this->common_data['current_userID'];
                                            $invGL['createdUserName'] = $this->common_data['current_user'];
                                            $invGL['createdDateTime'] = $this->common_data['current_date'];
                                            $this->db->insert('srp_erp_generalledger', $invGL);
                                        }



                                        if($bankGlBankLeager['GLAutoID']){
                                            $invGLbnk['documentCode'] = 'RRVR';
                                            $invGLbnk['documentMasterAutoID'] = $receiptReversalAutoID;
                                            $invGLbnk['documentSystemCode'] = $data['documentSystemCode'];
                                            $invGLbnk['documentType'] = $rvmaster['RVType'];
                                            $invGLbnk['documentDate'] = $reversalDate;
                                            $invGLbnk['documentYear'] = $year;
                                            $invGLbnk['documentMonth'] = $month;
                                            $invGLbnk['documentNarration'] = 'Receipt reversal of receipt voucher' . $rvmaster['RVcode'];
                                            $invGLbnk['GLAutoID'] = $bankGlBankLeager['GLAutoID'];
                                            $invGLbnk['systemGLCode'] = $bankGlBankLeager['systemAccountCode'];
                                            $invGLbnk['GLCode'] = $bankGlBankLeager['GLSecondaryCode'];
                                            $invGLbnk['GLDescription'] = $bankGlBankLeager['GLDescription'];
                                            $invGLbnk['GLType'] = $bankGlBankLeager['subCategory'];
                                            $invGLbnk['amount_type'] = 'cr';

                                            $invGLbnk['transactionCurrencyID'] = $rvmaster['transactionCurrencyID'];
                                            $invGLbnk['transactionCurrency'] = $rvmaster['transactionCurrency'];
                                            $invGLbnk['transactionExchangeRate'] = $rvmaster['transactionExchangeRate'];
                                            if($ItemGlDetails['taxPercentage']== null){
                                                $taxPercentageb=0;
                                            }else{
                                                $taxPercentageb=$bankGlBankLeager['taxPercentage'];
                                            }
                                            $bankTaxamount=($ItemGlDetails['Amount']/100)*$taxPercentageb;
                                            $invGLbnk['transactionAmount'] = ($bankGlBankLeager['Amount']+$bankTaxamount)*-1;
                                            $invGLbnk['transactionCurrencyDecimalPlaces'] = $rvmaster['transactionCurrencyDecimalPlaces'];

                                            $invGLbnk['companyLocalCurrencyID'] = $rvmaster['companyLocalCurrencyID'];
                                            $invGLbnk['companyLocalCurrency'] = $rvmaster['companyLocalCurrency'];
                                            $invGLbnk['companyLocalExchangeRate'] = $rvmaster['companyLocalExchangeRate'];
                                            $invGLbnk['companyLocalAmount'] = ($bankGlBankLeager['compLocalAmount']+$bankTaxamount)*-1;
                                            $invGLbnk['companyLocalCurrencyDecimalPlaces'] = $rvmaster['companyLocalCurrencyDecimalPlaces'];

                                            $invGLbnk['companyReportingCurrencyID'] = $rvmaster['companyReportingCurrencyID'];
                                            $invGLbnk['companyReportingCurrency'] = $rvmaster['companyReportingCurrency'];
                                            $invGLbnk['companyReportingExchangeRate'] = $rvmaster['companyReportingExchangeRate'];
                                            $invGLbnk['companyReportingAmount'] = ($bankGlBankLeager['compReportingAmount']+$bankTaxamount)*-1;
                                            $invGLbnk['companyReportingCurrencyDecimalPlaces'] = $rvmaster['companyReportingCurrencyDecimalPlaces'];
                                            $invGLbnk['partyAutoID'] = $rvmaster['customerID'];
                                            $invGLbnk['partyName'] = $rvmaster['customerName'];
                                            /*$invGLbnk['subLedgerType'] = 2;
                                            $invGLbnk['subLedgerDesc'] = 'AP';*/
                                            $invGLbnk['confirmedByEmpID'] = $this->common_data['current_userID'];
                                            $invGLbnk['confirmedByName'] = $this->common_data['current_user'];
                                            $invGLbnk['confirmedDate'] = $this->common_data['current_date'];
                                            $invGLbnk['approvedDate'] = $this->common_data['current_date'];
                                            $invGLbnk['approvedbyEmpID'] = $this->common_data['current_userID'];
                                            $invGLbnk['approvedbyEmpName'] = $this->common_data['current_user'];
                                            $invGLbnk['companyCode'] = $this->common_data['company_data']['company_code'];
                                            $invGLbnk['companyID'] = $this->common_data['company_data']['company_id'];
                                            $invGLbnk['createdUserGroup'] = $this->common_data['user_group'];
                                            $invGLbnk['createdPCID'] = $this->common_data['current_pc'];
                                            $invGLbnk['createdUserID'] = $this->common_data['current_userID'];
                                            $invGLbnk['createdUserName'] = $this->common_data['current_user'];
                                            $invGLbnk['createdDateTime'] = $this->common_data['current_date'];
                                            $this->db->insert('srp_erp_generalledger', $invGLbnk);
                                        }

                                        $BankLedger['documentDate'] = $reversalDate;
                                        $BankLedger['transactionType'] = 2;
                                        $BankLedger['partyType'] = 'CUS';
                                        $BankLedger['partyAutoID'] = $rvmaster['customerID'];
                                        $BankLedger['partyCode'] = $rvmaster['customerSystemCode'];
                                        $BankLedger['partyName'] = $rvmaster['customerName'];
                                        $BankLedger['partyCurrencyID'] = $rvmaster['customerCurrencyID'];
                                        $BankLedger['partyCurrency'] = $rvmaster['customerCurrency'];
                                        $BankLedger['partyCurrencyExchangeRate'] = $rvmaster['customerExchangeRate'];
                                        $BankLedger['partyCurrencyDecimalPlaces'] = $rvmaster['customerCurrencyDecimalPlaces'];
                                        $BankLedger['bankCurrencyID'] = $rvmaster['bankCurrencyID'];
                                        $BankLedger['bankCurrency'] = $rvmaster['bankCurrency'];
                                        $BankLedger['bankCurrencyExchangeRate'] = $rvmaster['bankCurrencyExchangeRate'];
                                        $BankLedger['bankCurrencyAmount'] = ($rvdetail['transactionAmount'] / $rvmaster['bankCurrencyExchangeRate']);
                                        $BankLedger['bankCurrencyDecimalPlaces'] = $rvmaster['bankCurrencyDecimalPlaces'];
                                        $BankLedger['transactionCurrencyID'] = $rvmaster['transactionCurrencyID'];
                                        $BankLedger['transactionCurrency'] = $rvmaster['transactionCurrency'];
                                        $BankLedger['transactionAmount'] = $rvdetail['transactionAmount'];
                                        $BankLedger['transactionCurrencyDecimalPlaces'] = $rvmaster['transactionCurrencyDecimalPlaces'];
                                        $BankLedger['chequeNo'] = $rvmaster['RVchequeNo'];
                                        $BankLedger['chequeDate'] = $rvmaster['RVchequeDate'];
                                        $BankLedger['bankName'] = $rvmaster['RVbank'];
                                        $BankLedger['bankGLAutoID'] = $rvmaster['bankGLAutoID'];
                                        $BankLedger['bankSystemAccountCode'] = $rvmaster['bankSystemAccountCode'];
                                        $BankLedger['bankGLSecondaryCode'] = $rvmaster['bankGLSecondaryCode'];
                                        $BankLedger['documentMasterAutoID'] = $payVoucherAutoId;
                                        $BankLedger['documentType'] = 'PV';
                                        $BankLedger['documentSystemCode'] = $PVdata['PVcode'];

                                        $BankLedger['companyCode'] = $this->common_data['company_data']['company_code'];
                                        $BankLedger['companyID'] = $this->common_data['company_data']['company_id'];
                                        $BankLedger['createdPCID'] = $this->common_data['current_pc'];
                                        $BankLedger['createdUserID'] = $this->common_data['current_userID'];
                                        $BankLedger['createdUserName'] = $this->common_data['current_user'];
                                        $BankLedger['createdDateTime'] = $this->common_data['current_date'];
                                        $this->db->insert('srp_erp_bankledger', $BankLedger);

                                        return array('s', 'Receipt voucher reversed successfully');
                                    }
                                } else {
                                    return array('e', 'Receipt voucher reversal failed');
                                }
                            } else {
                                return array('e', 'Receipt voucher reversal failed');
                            }
                        } else {
                            return array('e', 'Receipt voucher reversal failed');
                        }
                    } else {
                        $PVdata['documentID'] = 'PV';
                        $PVdata['rrvrID'] = $receiptReversalAutoID;
                        $PVdata['PVdate'] = $reversalDate;
                        $PVdata['pvType'] = 'Direct';
                        $PVdata['companyFinanceYearID'] = $companyFinanceYearID['companyFinanceYearID'];
                        $PVdata['FYPeriodDateFrom'] = $companyFinanceYearID['dateFrom'];
                        $PVdata['FYPeriodDateTo'] = $companyFinanceYearID['dateTo'];
                        $PVdata['companyFinancePeriodID'] = $companyFinanceYearID['companyFinancePeriodID'];
                        $PVdata['modeOfPayment'] = $rvmaster['modeOfPayment'];
                        $PVdata['PVbankCode'] = $rvmaster['RVbankCode'];
                        $PVdata['PVbankSwiftCode'] = $rvmaster['RVbankSwiftCode'];
                        $PVdata['bankGLAutoID'] = $rvmaster['bankGLAutoID'];
                        $PVdata['bankSystemAccountCode'] = $rvmaster['bankSystemAccountCode'];
                        $PVdata['bankGLSecondaryCode'] = $rvmaster['bankGLSecondaryCode'];
                        $PVdata['PVbank'] = $rvmaster['RVbank'];
                        $PVdata['PVbankAccount'] = $rvmaster['RVbankAccount'];
                        $PVdata['PVbankBranch'] = $rvmaster['RVbankBranch'];
                        $PVdata['PVbankType'] = $rvmaster['RVbankType'];
                        $PVdata['PVchequeNo'] = $rvmaster['RVchequeNo'];
                        $PVdata['bankCurrencyID'] = $rvmaster['bankCurrencyID'];
                        $PVdata['bankCurrency'] = $rvmaster['bankCurrency'];
                        $PVdata['bankCurrencyExchangeRate'] = $rvmaster['bankCurrencyExchangeRate'];
                        $PVdata['bankCurrencyAmount'] = ($rvdetail['transactionAmount'] / $rvmaster['bankCurrencyExchangeRate']);
                        $PVdata['bankCurrencyDecimalPlaces'] = $rvmaster['bankCurrencyDecimalPlaces'];
                        $PVdata['PVchequeDate'] = $rvmaster['RVchequeDate'];
                        $PVdata['PVNarration'] = 'Receipt reversal of receipt voucher' . $rvmaster['RVcode'];
                        $PVdata['partyType'] = 'DIR';
                        $PVdata['partyID'] = $rvmaster['customerID'];
                        $PVdata['partyCode'] = $rvmaster['customerSystemCode'];
                        $PVdata['partyName'] = $rvmaster['customerName'];
                        $PVdata['partyAddress'] = $rvmaster['customerAddress'];
                        $PVdata['partyTelephone'] = $rvmaster['customerTelephone'];
                        $PVdata['partyFax'] = $rvmaster['customerFax'];
                        $PVdata['partyEmail'] = $rvmaster['customerEmail'];
                        $PVdata['transactionCurrencyID'] = $rvmaster['transactionCurrencyID'];
                        $PVdata['transactionCurrency'] = $rvmaster['transactionCurrency'];
                        $PVdata['transactionAmount'] = $rvdetail['transactionAmount'];
                        $PVdata['transactionCurrencyDecimalPlaces'] = $rvmaster['transactionCurrencyDecimalPlaces'];
                        $PVdata['companyLocalCurrencyID'] = $rvmaster['companyLocalCurrencyID'];
                        $PVdata['companyLocalCurrency'] = $rvmaster['companyLocalCurrency'];
                        $PVdata['companyLocalExchangeRate'] = $rvmaster['companyLocalExchangeRate'];
                        $PVdata['companyLocalAmount'] = $rvdetail['companyLocalAmount'];
                        $PVdata['companyLocalCurrencyDecimalPlaces'] = $rvmaster['companyLocalCurrencyDecimalPlaces'];
                        $PVdata['companyReportingCurrencyID'] = $rvmaster['companyReportingCurrencyID'];
                        $PVdata['companyReportingCurrency'] = $rvmaster['companyReportingCurrency'];
                        $PVdata['companyReportingExchangeRate'] = $rvmaster['companyReportingExchangeRate'];
                        $PVdata['companyReportingAmount'] = $rvdetail['companyReportingAmount'];
                        $PVdata['companyReportingCurrencyDecimalPlaces'] = $rvmaster['companyReportingCurrencyDecimalPlaces'];
                        $PVdata['approvedYN'] = 1;
                        $PVdata['approvedDate'] = current_date();
                        $PVdata['confirmedYN'] = 1;
                        $PVdata['approvedbyEmpID'] = $this->common_data['current_userID'];
                        $PVdata['approvedbyEmpName'] = $this->common_data['current_user'];
                        $PVdata['companyCode'] = $this->common_data['company_data']['company_code'];
                        $PVdata['companyID'] = $this->common_data['company_data']['company_id'];
                        $PVdata['createdUserGroup'] = $this->common_data['user_group'];
                        $PVdata['createdPCID'] = $this->common_data['current_pc'];
                        $PVdata['createdUserID'] = $this->common_data['current_userID'];
                        $PVdata['createdUserName'] = $this->common_data['current_user'];
                        $PVdata['createdDateTime'] = $this->common_data['current_date'];

                        $PVdata['PVcode'] = $this->sequence->sequence_generator('PV');
                        $payPaymentVoucherM = $this->db->insert('srp_erp_paymentvouchermaster', $PVdata);
                        $payVoucherAutoId = $this->db->insert_id();
                        if ($payPaymentVoucherM) {
                            $PVDdata['payVoucherAutoId'] = $payVoucherAutoId;
                            $PVDdata['type'] = 'RRVR';
                            $PVDdata['transactionAmount'] = $rvdetail['transactionAmount'];
                            $PVDdata['companyLocalAmount'] = $rvdetail['companyLocalAmount'];
                            $PVDdata['companyReportingAmount'] = $rvdetail['companyReportingAmount'];
                            $PVDdata['companyCode'] = $this->common_data['company_data']['company_code'];
                            $PVDdata['companyID'] = $this->common_data['company_data']['company_id'];
                            $PVDdata['createdUserGroup'] = $this->common_data['user_group'];
                            $PVDdata['createdPCID'] = $this->common_data['current_pc'];
                            $PVDdata['createdUserID'] = $this->common_data['current_userID'];
                            $PVDdata['createdUserName'] = $this->common_data['current_user'];
                            $PVDdata['createdDateTime'] = $this->common_data['current_date'];

                            $result = $this->db->insert('srp_erp_paymentvoucherdetail', $PVDdata);
                            if ($result) {

                                if($ItemGlDetails){
                                    $invGL['documentCode'] = 'RRVR';
                                    $invGL['documentMasterAutoID'] = $receiptReversalAutoID;
                                    $invGL['documentSystemCode'] = $data['documentSystemCode'];
                                    $invGL['documentType'] = $rvmaster['RVType'];
                                    $invGL['documentDate'] = $reversalDate;
                                    $invGL['documentYear'] = $year;
                                    $invGL['documentMonth'] = $month;
                                    $invGL['documentNarration'] = 'Receipt reversal of receipt voucher' . $rvmaster['RVcode'];
                                    $invGL['GLAutoID'] = $ItemGlDetails['GLAutoID'];
                                    $invGL['systemGLCode'] = $ItemGlDetails['systemAccountCode'];
                                    $invGL['GLCode'] = $ItemGlDetails['GLSecondaryCode'];
                                    $invGL['GLDescription'] = $ItemGlDetails['GLDescription'];
                                    $invGL['GLType'] = $ItemGlDetails['subCategory'];
                                    $invGL['amount_type'] = 'dr';

                                    $invGL['transactionCurrencyID'] = $rvmaster['transactionCurrencyID'];
                                    $invGL['transactionCurrency'] = $rvmaster['transactionCurrency'];
                                    $invGL['transactionExchangeRate'] = $rvmaster['transactionExchangeRate'];
                                    if($ItemGlDetails['taxPercentage']== null){
                                        $taxPercentage=0;
                                    }else{
                                        $taxPercentage=$ItemGlDetails['taxPercentage'];
                                    }
                                    $itemTaxamount=($ItemGlDetails['Amount']/100)*$taxPercentage;
                                    $invGL['transactionAmount'] = $ItemGlDetails['Amount']+$itemTaxamount;
                                    $invGL['transactionCurrencyDecimalPlaces'] = $rvmaster['transactionCurrencyDecimalPlaces'];

                                    $invGL['companyLocalCurrencyID'] = $rvmaster['companyLocalCurrencyID'];
                                    $invGL['companyLocalCurrency'] = $rvmaster['companyLocalCurrency'];
                                    $invGL['companyLocalExchangeRate'] = $rvmaster['companyLocalExchangeRate'];
                                    $invGL['companyLocalAmount'] = $ItemGlDetails['compLocalAmount']+$itemTaxamount;
                                    $invGL['companyLocalCurrencyDecimalPlaces'] = $rvmaster['companyLocalCurrencyDecimalPlaces'];

                                    $invGL['companyReportingCurrencyID'] = $rvmaster['companyReportingCurrencyID'];
                                    $invGL['companyReportingCurrency'] = $rvmaster['companyReportingCurrency'];
                                    $invGL['companyReportingExchangeRate'] = $rvmaster['companyReportingExchangeRate'];
                                    $invGL['companyReportingAmount'] = $ItemGlDetails['compReportingAmount']+$itemTaxamount;
                                    $invGL['companyReportingCurrencyDecimalPlaces'] = $rvmaster['companyReportingCurrencyDecimalPlaces'];
                                    $invGL['partyAutoID'] = $rvmaster['customerID'];
                                    $invGL['partyName'] = $rvmaster['customerName'];
                                    /*$invGL['subLedgerType'] = 2;
                                    $invGL['subLedgerDesc'] = 'AP';*/
                                    $invGL['confirmedByEmpID'] = $this->common_data['current_userID'];
                                    $invGL['confirmedByName'] = $this->common_data['current_user'];
                                    $invGL['confirmedDate'] = $this->common_data['current_date'];
                                    $invGL['approvedDate'] = $this->common_data['current_date'];
                                    $invGL['approvedbyEmpID'] = $this->common_data['current_userID'];
                                    $invGL['approvedbyEmpName'] = $this->common_data['current_user'];
                                    $invGL['companyCode'] = $this->common_data['company_data']['company_code'];
                                    $invGL['companyID'] = $this->common_data['company_data']['company_id'];
                                    $invGL['createdUserGroup'] = $this->common_data['user_group'];
                                    $invGL['createdPCID'] = $this->common_data['current_pc'];
                                    $invGL['createdUserID'] = $this->common_data['current_userID'];
                                    $invGL['createdUserName'] = $this->common_data['current_user'];
                                    $invGL['createdDateTime'] = $this->common_data['current_date'];
                                    $this->db->insert('srp_erp_generalledger', $invGL);
                                }



                                if($bankGlBankLeager){
                                    $invGLbnk['documentCode'] = 'RRVR';
                                    $invGLbnk['documentMasterAutoID'] = $receiptReversalAutoID;
                                    $invGLbnk['documentSystemCode'] = $data['documentSystemCode'];
                                    $invGLbnk['documentType'] = $rvmaster['RVType'];
                                    $invGLbnk['documentDate'] = $reversalDate;
                                    $invGLbnk['documentYear'] = $year;
                                    $invGLbnk['documentMonth'] = $month;
                                    $invGLbnk['documentNarration'] = 'Receipt reversal of receipt voucher' . $rvmaster['RVcode'];
                                    $invGLbnk['GLAutoID'] = $bankGlBankLeager['GLAutoID'];
                                    $invGLbnk['systemGLCode'] = $bankGlBankLeager['systemAccountCode'];
                                    $invGLbnk['GLCode'] = $bankGlBankLeager['GLSecondaryCode'];
                                    $invGLbnk['GLDescription'] = $bankGlBankLeager['GLDescription'];
                                    $invGLbnk['GLType'] = $bankGlBankLeager['subCategory'];
                                    $invGLbnk['amount_type'] = 'cr';

                                    $invGLbnk['transactionCurrencyID'] = $rvmaster['transactionCurrencyID'];
                                    $invGLbnk['transactionCurrency'] = $rvmaster['transactionCurrency'];
                                    $invGLbnk['transactionExchangeRate'] = $rvmaster['transactionExchangeRate'];
                                    if($ItemGlDetails['taxPercentage']== null){
                                        $taxPercentageb=0;
                                    }else{
                                        $taxPercentageb=$bankGlBankLeager['taxPercentage'];
                                    }
                                    $bankTaxamount=($ItemGlDetails['Amount']/100)*$taxPercentageb;
                                    $invGLbnk['transactionAmount'] = ($bankGlBankLeager['Amount']+$bankTaxamount)*-1;
                                    $invGLbnk['transactionCurrencyDecimalPlaces'] = $rvmaster['transactionCurrencyDecimalPlaces'];

                                    $invGLbnk['companyLocalCurrencyID'] = $rvmaster['companyLocalCurrencyID'];
                                    $invGLbnk['companyLocalCurrency'] = $rvmaster['companyLocalCurrency'];
                                    $invGLbnk['companyLocalExchangeRate'] = $rvmaster['companyLocalExchangeRate'];
                                    $invGLbnk['companyLocalAmount'] = ($bankGlBankLeager['compLocalAmount']+$bankTaxamount)*-1;
                                    $invGLbnk['companyLocalCurrencyDecimalPlaces'] = $rvmaster['companyLocalCurrencyDecimalPlaces'];

                                    $invGLbnk['companyReportingCurrencyID'] = $rvmaster['companyReportingCurrencyID'];
                                    $invGLbnk['companyReportingCurrency'] = $rvmaster['companyReportingCurrency'];
                                    $invGLbnk['companyReportingExchangeRate'] = $rvmaster['companyReportingExchangeRate'];
                                    $invGLbnk['companyReportingAmount'] = ($bankGlBankLeager['compReportingAmount']+$bankTaxamount)*-1;
                                    $invGLbnk['companyReportingCurrencyDecimalPlaces'] = $rvmaster['companyReportingCurrencyDecimalPlaces'];
                                    $invGLbnk['partyAutoID'] = $rvmaster['customerID'];
                                    $invGLbnk['partyName'] = $rvmaster['customerName'];
                                    /*$invGLbnk['subLedgerType'] = 2;
                                    $invGLbnk['subLedgerDesc'] = 'AP';*/
                                    $invGLbnk['confirmedByEmpID'] = $this->common_data['current_userID'];
                                    $invGLbnk['confirmedByName'] = $this->common_data['current_user'];
                                    $invGLbnk['confirmedDate'] = $this->common_data['current_date'];
                                    $invGLbnk['approvedDate'] = $this->common_data['current_date'];
                                    $invGLbnk['approvedbyEmpID'] = $this->common_data['current_userID'];
                                    $invGLbnk['approvedbyEmpName'] = $this->common_data['current_user'];
                                    $invGLbnk['companyCode'] = $this->common_data['company_data']['company_code'];
                                    $invGLbnk['companyID'] = $this->common_data['company_data']['company_id'];
                                    $invGLbnk['createdUserGroup'] = $this->common_data['user_group'];
                                    $invGLbnk['createdPCID'] = $this->common_data['current_pc'];
                                    $invGLbnk['createdUserID'] = $this->common_data['current_userID'];
                                    $invGLbnk['createdUserName'] = $this->common_data['current_user'];
                                    $invGLbnk['createdDateTime'] = $this->common_data['current_date'];
                                    $this->db->insert('srp_erp_generalledger', $invGLbnk);
                                }

                                $BankLedger['documentDate'] = $reversalDate;
                                $BankLedger['transactionType'] = 2;
                                $BankLedger['partyType'] = 'CUS';
                                $BankLedger['partyAutoID'] = $rvmaster['customerID'];
                                $BankLedger['partyCode'] = $rvmaster['customerSystemCode'];
                                $BankLedger['partyName'] = $rvmaster['customerName'];
                                $BankLedger['partyCurrencyID'] = $rvmaster['customerCurrencyID'];
                                $BankLedger['partyCurrency'] = $rvmaster['customerCurrency'];
                                $BankLedger['partyCurrencyExchangeRate'] = $rvmaster['customerExchangeRate'];
                                $BankLedger['partyCurrencyDecimalPlaces'] = $rvmaster['customerCurrencyDecimalPlaces'];
                                $BankLedger['bankCurrencyID'] = $rvmaster['bankCurrencyID'];
                                $BankLedger['bankCurrency'] = $rvmaster['bankCurrency'];
                                $BankLedger['bankCurrencyExchangeRate'] = $rvmaster['bankCurrencyExchangeRate'];
                                $BankLedger['bankCurrencyAmount'] = ($rvdetail['transactionAmount'] / $rvmaster['bankCurrencyExchangeRate']);
                                $BankLedger['bankCurrencyDecimalPlaces'] = $rvmaster['bankCurrencyDecimalPlaces'];
                                $BankLedger['transactionCurrencyID'] = $rvmaster['transactionCurrencyID'];
                                $BankLedger['transactionCurrency'] = $rvmaster['transactionCurrency'];
                                $BankLedger['transactionAmount'] = $rvdetail['transactionAmount'];
                                $BankLedger['transactionCurrencyDecimalPlaces'] = $rvmaster['transactionCurrencyDecimalPlaces'];
                                $BankLedger['chequeNo'] = $rvmaster['RVchequeNo'];
                                $BankLedger['chequeDate'] = $rvmaster['RVchequeDate'];
                                $BankLedger['bankName'] = $rvmaster['RVbank'];
                                $BankLedger['bankGLAutoID'] = $rvmaster['bankGLAutoID'];
                                $BankLedger['bankSystemAccountCode'] = $rvmaster['bankSystemAccountCode'];
                                $BankLedger['bankGLSecondaryCode'] = $rvmaster['bankGLSecondaryCode'];
                                $BankLedger['documentMasterAutoID'] = $payVoucherAutoId;
                                $BankLedger['documentType'] = 'PV';
                                $BankLedger['documentSystemCode'] = $PVdata['PVcode'];

                                $BankLedger['companyCode'] = $this->common_data['company_data']['company_code'];
                                $BankLedger['companyID'] = $this->common_data['company_data']['company_id'];
                                $BankLedger['createdPCID'] = $this->common_data['current_pc'];
                                $BankLedger['createdUserID'] = $this->common_data['current_userID'];
                                $BankLedger['createdUserName'] = $this->common_data['current_user'];
                                $BankLedger['createdDateTime'] = $this->common_data['current_date'];
                                $this->db->insert('srp_erp_bankledger', $BankLedger);

                                return array('s', 'Receipt voucher reversed successfully');
                            }

                        } else {
                            return array('e', 'Receipt voucher reversal failed');
                        }
                    }
                } else {
                    return array('e', 'Receipt voucher reversal failed');
                }
            }
        }else{
            return array('e', 'Selected Date is not between any active finance periods ');
        }
    }

    function payment_reversal_confirmation()
    {
        $this->db->select('paymentReversalAutoID');
        $this->db->where('paymentReversalAutoID', trim($this->input->post('paymentReversalAutoID')));
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_paymentreversalmaster');
        $Confirmed = $this->db->get()->row_array();
        if (!empty($Confirmed)) {
            $this->session->set_flashdata('w', 'Document already confirmed ');
            return false;
        } else {
            $this->load->library('approvals');
            $this->db->select('documentSystemCode,partyExchangeRate,companyReportingExchangeRate,companyLocalExchangeRate ,paymentReversalAutoID,transactionCurrencyDecimalPlaces');
            $this->db->where('paymentReversalAutoID', trim($this->input->post('paymentReversalAutoID')));
            $this->db->from('srp_erp_paymentreversalmaster');
            $po_data = $this->db->get()->row_array();
            $approvals_status = $this->approvals->CreateApproval('PRVR', $po_data['paymentReversalAutoID'], $po_data['documentSystemCode'], 'Payment Reversal', 'srp_erp_paymentreversalmaster', 'paymentReversalAutoID');
            if ($approvals_status == 1) {
                $this->db->select_sum('pvAmount');
                $this->db->where('paymentReversalAutoID', trim($this->input->post('paymentReversalAutoID')));
                $prvr_total = $this->db->get('srp_erp_paymentreversaldetail')->row('pvAmount');
                $data = array(
                    'confirmedYN' => 1,
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                    'confirmedByName' => $this->common_data['current_user'],
                    'transactionAmount' => round($prvr_total, $po_data['transactionCurrencyDecimalPlaces']),
                    'companyLocalAmount' => ($prvr_total / $po_data['companyLocalExchangeRate']),
                    'companyReportingAmount' => ($prvr_total / $po_data['companyReportingExchangeRate']),
                    'partyCurrencyAmount' => ($prvr_total / $po_data['partyExchangeRate']),
                );
                $this->db->where('paymentReversalAutoID', trim($this->input->post('paymentReversalAutoID')));
                $this->db->update('srp_erp_paymentreversalmaster', $data);
                $this->session->set_flashdata('s', 'Approvals Created Successfully ');
                return true;
            } else {
                return false;
            }
        }
    }

    function fetch_payment_voucher_template_data($receiptVoucherAutoId)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(RVdate,\'' . $convertFormat . '\') AS RVdate,DATE_FORMAT(RVchequeDate,\'' . $convertFormat . '\') AS RVchequeDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->from('srp_erp_customerreceiptmaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);
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
        $this->db->where('type', 'PRVR');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['prvr_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->from('srp_erp_customerreceipttaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();
        return $data;
    }

}