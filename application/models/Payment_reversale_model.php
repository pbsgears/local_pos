<?php

class Payment_reversale_model extends ERP_Model
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

    function reverse_paymentVoucher()
    {
        $pvautoid = $this->input->post('payVoucherAutoId');
        $this->db->select('*');
        $this->db->from('srp_erp_paymentvouchermaster');
        $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId')));
        $pvmaster = $this->db->get()->row_array();

        $this->db->select('sum(transactionAmount) as transactionAmount,sum(companyLocalAmount) as companyLocalAmount,sum(companyReportingAmount) as companyReportingAmount');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId')));
        $pvdetail = $this->db->get()->row_array();

        /*$this->db->select('sum(transactionAmount) as transactionAmount,sum(companyLocalAmount) as companyLocalAmount,sum(companyReportingAmount) as companyReportingAmount');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId')));
        $this->db->where('type', trim('Invoice'));
        $this->db->where('type', trim('Advance'));
        $pvdetailSupp = $this->db->get()->row_array();*/


        $pvdetailSupp = $this->db->query('SELECT
	sum(transactionAmount) as transactionAmount,sum(companyLocalAmount) as companyLocalAmount,sum(companyReportingAmount) as companyReportingAmount
FROM srp_erp_paymentvoucherdetail
WHERE srp_erp_paymentvoucherdetail.payVoucherAutoId = '.$pvautoid.'
AND (type = "Invoice" OR type = "Advance" OR type = "debitnote")')->row_array();

       /* $this->db->select('companyFinanceYearID');
        $this->db->from('srp_erp_companyfinanceperiod');
        $this->db->where('companyID', trim(current_companyID()));
        $this->db->where('isCurrent', 1);
        $financeYearID = $this->db->get()->row_array();*/
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
	SUM(srp_erp_paymentvoucherdetail.transactionAmount) as Amount,SUM(srp_erp_paymentvoucherdetail.companyLocalAmount) as compLocalAmount,SUM(srp_erp_paymentvoucherdetail.companyReportingAmount) as compReportingAmount,srp_erp_suppliermaster.liabilityAutoID,srp_erp_suppliermaster.liabilitySystemGLCode,srp_erp_suppliermaster.liabilityGLAccount,srp_erp_suppliermaster.liabilityDescription,srp_erp_suppliermaster.liabilityType,srp_erp_chartofaccounts.GLAutoID,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.systemAccountCode,srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.subCategory
FROM srp_erp_paymentvouchermaster
LEFT JOIN srp_erp_paymentvoucherdetail on srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentvoucherdetail.payVoucherAutoId
LEFT JOIN srp_erp_suppliermaster on srp_erp_paymentvouchermaster.partyID = srp_erp_suppliermaster.supplierAutoID
LEFT JOIN srp_erp_chartofaccounts on srp_erp_suppliermaster.liabilityAutoID = srp_erp_chartofaccounts.GLAutoID
WHERE srp_erp_paymentvouchermaster.payVoucherAutoId = '.$pvautoid.'
AND (type = "Invoice" OR type = "Advance" OR type = "debitnote")')->row_array();


        $ItemGlDetails = $this->db->query('SELECT
	SUM(srp_erp_paymentvoucherdetail.transactionAmount) as Amount,SUM(srp_erp_paymentvoucherdetail.companyLocalAmount) as compLocalAmount,SUM(srp_erp_paymentvoucherdetail.companyReportingAmount) as compReportingAmount,srp_erp_chartofaccounts.GLAutoID,addondet.taxPercentage as taxPercentage,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.systemAccountCode,srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.subCategory
FROM srp_erp_paymentvouchermaster
LEFT JOIN srp_erp_paymentvoucherdetail on srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentvoucherdetail.payVoucherAutoId
LEFT JOIN srp_erp_suppliermaster on srp_erp_paymentvouchermaster.partyID = srp_erp_suppliermaster.supplierAutoID
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		payVoucherAutoId
	FROM
		srp_erp_paymentvouchertaxdetails
	GROUP BY
		payVoucherAutoId
) addondet ON (
	`addondet`.`payVoucherAutoId` = srp_erp_paymentVouchermaster.payVoucherAutoId
)
LEFT JOIN srp_erp_companycontrolaccounts on srp_erp_companycontrolaccounts.controlAccountType = "PRVR"
LEFT JOIN srp_erp_chartofaccounts on srp_erp_companycontrolaccounts.GLAutoID = srp_erp_chartofaccounts.GLAutoID
WHERE srp_erp_paymentvouchermaster.payVoucherAutoId = '.$pvautoid.'
AND srp_erp_companycontrolaccounts.companyID='.$companyid.'
AND (type = "GL" OR type = "Item")')->row_array();

        $bankGlBankLeager = $this->db->query('SELECT
	SUM(srp_erp_paymentvoucherdetail.transactionAmount) as Amount,SUM(srp_erp_paymentvoucherdetail.companyLocalAmount) as compLocalAmount,SUM(srp_erp_paymentvoucherdetail.companyReportingAmount) as compReportingAmount,addondet.taxPercentage as taxPercentage,srp_erp_chartofaccounts.GLAutoID,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.systemAccountCode,srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.subCategory
FROM srp_erp_paymentvouchermaster
LEFT JOIN srp_erp_paymentvoucherdetail on srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentvoucherdetail.payVoucherAutoId
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		payVoucherAutoId
	FROM
		srp_erp_paymentvouchertaxdetails
	GROUP BY
		payVoucherAutoId
) addondet ON (
	`addondet`.`payVoucherAutoId` = srp_erp_paymentVouchermaster.payVoucherAutoId
)
LEFT JOIN srp_erp_chartofaccounts on srp_erp_paymentvouchermaster.bankGLAutoID = srp_erp_chartofaccounts.GLAutoID
WHERE srp_erp_paymentvouchermaster.payVoucherAutoId = '.$pvautoid.'')->row_array();

        if (!empty($companyFinanceYearID)) {

            $this->load->library('sequence');
            if ($pvmaster) {
                $data['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId'));
                $data['Type'] = $pvmaster['pvType'];
                $data['documentID'] = 'PRVR';
                $data['documentDate'] = $reversalDate;
                $data['referenceNo'] = $pvmaster['referenceNo'];
                $data['narration'] = $this->input->post('comments');
                $data['companyFinanceYearID'] = $companyFinanceYearID['companyFinanceYearID'];

                $data['PVbankCode'] = $pvmaster['PVbankCode'];
                $data['bankGLAutoID'] = $pvmaster['bankGLAutoID'];
                $data['partyType'] = $pvmaster['partyType'];
                $data['partyID'] = $pvmaster['partyID'];
                $data['partyName'] = $pvmaster['partyName'];
                $data['partyGLAutoID'] = $pvmaster['partyGLAutoID'];
                $data['companyID'] = current_companyID();
                $data['companyCode'] = $this->common_data['company_data']['company_code'];

                $data['documentSystemCode'] = $this->sequence->sequence_generator('PRVR');
                $payReversalMaster = $this->db->insert('srp_erp_paymentreversalmaster', $data);
                $paymentReversalAutoID = $this->db->insert_id();
                if ($payReversalMaster) {
                    if ($pvmaster['pvType'] == 'Supplier') {

                        $RVdata['documentID'] = 'RV';
                        $RVdata['prvrID'] = $paymentReversalAutoID;
                        $RVdata['RVdate'] = $reversalDate;
                        $RVdata['RVType'] = 'Direct';
                        $RVdata['companyFinanceYearID'] = $companyFinanceYearID['companyFinanceYearID'];
                        $RVdata['FYPeriodDateFrom'] = $companyFinanceYearID['dateFrom'];
                        $RVdata['FYPeriodDateTo'] = $companyFinanceYearID['dateTo'];
                        $RVdata['companyFinancePeriodID'] = $companyFinanceYearID['companyFinancePeriodID'];
                        $RVdata['modeOfPayment'] = $pvmaster['modeOfPayment'];
                        $RVdata['RVbankCode'] = $pvmaster['PVbankCode'];
                        $RVdata['RVbankSwiftCode'] = $pvmaster['PVbankSwiftCode'];
                        $RVdata['bankGLAutoID'] = $pvmaster['bankGLAutoID'];
                        $RVdata['bankSystemAccountCode'] = $pvmaster['bankSystemAccountCode'];
                        $RVdata['bankGLSecondaryCode'] = $pvmaster['bankGLSecondaryCode'];
                        $RVdata['RVbank'] = $pvmaster['PVbank'];
                        $RVdata['RVbankAccount'] = $pvmaster['PVbankAccount'];
                        $RVdata['RVbankBranch'] = $pvmaster['PVbankBranch'];
                        $RVdata['RVbankType'] = $pvmaster['PVbankType'];
                        $RVdata['RVchequeNo'] = $pvmaster['PVchequeNo'];
                        $RVdata['bankCurrencyID'] = $pvmaster['bankCurrencyID'];
                        $RVdata['bankCurrency'] = $pvmaster['bankCurrency'];
                        $RVdata['bankCurrencyExchangeRate'] = $pvmaster['bankCurrencyExchangeRate'];
                        $RVdata['bankCurrencyAmount'] = ($pvdetail['transactionAmount'] / $pvmaster['bankCurrencyExchangeRate']);
                        $RVdata['bankCurrencyDecimalPlaces'] = $pvmaster['bankCurrencyDecimalPlaces'];
                        $RVdata['RVchequeDate'] = $pvmaster['PVchequeDate'];
                        $RVdata['RVNarration'] = 'Payment reversal of payment voucher' . $pvmaster['PVcode'];
                        $RVdata['transactionCurrencyID'] = $pvmaster['transactionCurrencyID'];
                        $RVdata['transactionCurrency'] = $pvmaster['transactionCurrency'];
                        $RVdata['transactionAmount'] = $pvdetail['transactionAmount'];
                        $RVdata['transactionCurrencyDecimalPlaces'] = $pvmaster['transactionCurrencyDecimalPlaces'];
                        $RVdata['companyLocalCurrencyID'] = $pvmaster['companyLocalCurrencyID'];
                        $RVdata['companyLocalCurrency'] = $pvmaster['companyLocalCurrency'];
                        $RVdata['companyLocalExchangeRate'] = $pvmaster['companyLocalExchangeRate'];
                        $RVdata['companyLocalAmount'] = $pvdetail['companyLocalAmount'];
                        $RVdata['companyLocalCurrencyDecimalPlaces'] = $pvmaster['companyLocalCurrencyDecimalPlaces'];
                        $RVdata['companyReportingCurrencyID'] = $pvmaster['companyReportingCurrencyID'];
                        $RVdata['companyReportingCurrency'] = $pvmaster['companyReportingCurrency'];
                        $RVdata['companyReportingExchangeRate'] = $pvmaster['companyReportingExchangeRate'];
                        $RVdata['companyReportingAmount'] = $pvdetail['companyReportingAmount'];
                        $RVdata['companyReportingCurrencyDecimalPlaces'] = $pvmaster['companyReportingCurrencyDecimalPlaces'];
                        $RVdata['approvedYN'] = 1;
                        $RVdata['approvedDate'] = current_date();
                        $RVdata['confirmedYN'] = 1;
                        $RVdata['approvedbyEmpID'] = $this->common_data['current_userID'];
                        $RVdata['approvedbyEmpName'] = $this->common_data['current_user'];
                        $RVdata['companyCode'] = $this->common_data['company_data']['company_code'];
                        $RVdata['companyID'] = $this->common_data['company_data']['company_id'];
                        $RVdata['createdUserGroup'] = $this->common_data['user_group'];
                        $RVdata['createdPCID'] = $this->common_data['current_pc'];
                        $RVdata['createdUserID'] = $this->common_data['current_userID'];
                        $RVdata['createdUserName'] = $this->common_data['current_user'];
                        $RVdata['createdDateTime'] = $this->common_data['current_date'];

                        $RVdata['RVcode'] = $this->sequence->sequence_generator('RV');
                        $payCustomerReceiptM = $this->db->insert('srp_erp_customerreceiptmaster', $RVdata);
                        $receiptVoucherAutoId = $this->db->insert_id();
                        if ($payCustomerReceiptM) {
                            $RVDdata['receiptVoucherAutoId'] = $receiptVoucherAutoId;
                            $RVDdata['type'] = 'PRVR';
                            $RVDdata['transactionAmount'] = $pvdetail['transactionAmount'];
                            $RVDdata['companyLocalAmount'] = $pvdetail['companyLocalAmount'];
                            $RVDdata['companyReportingAmount'] = $pvdetail['companyReportingAmount'];
                            $RVDdata['companyCode'] = $this->common_data['company_data']['company_code'];
                            $RVDdata['companyID'] = $this->common_data['company_data']['company_id'];
                            $RVDdata['createdUserGroup'] = $this->common_data['user_group'];
                            $RVDdata['createdPCID'] = $this->common_data['current_pc'];
                            $RVDdata['createdUserID'] = $this->common_data['current_userID'];
                            $RVDdata['createdUserName'] = $this->common_data['current_user'];
                            $RVDdata['createdDateTime'] = $this->common_data['current_date'];

                            $payCustomerReceiptD = $this->db->insert('srp_erp_customerreceiptdetail', $RVDdata);
                            if ($payCustomerReceiptD) {
                                $BSIdata['documentID'] = 'BSI';
                                $BSIdata['invoiceType'] = 'Standard';
                                $BSIdata['companyFinanceYearID'] = $companyFinanceYearID['companyFinanceYearID'];
                                $BSIdata['FYPeriodDateFrom'] = $companyFinanceYearID['dateFrom'];
                                $BSIdata['FYPeriodDateTo'] = $companyFinanceYearID['dateTo'];
                                $BSIdata['companyFinancePeriodID'] = $companyFinanceYearID['companyFinancePeriodID'];
                                $BSIdata['prvrID'] = $paymentReversalAutoID;
                                $BSIdata['bookingDate'] = $reversalDate;
                                $BSIdata['invoiceDate'] = $reversalDate;
                                $BSIdata['invoiceDueDate'] = $reversalDate;
                                $BSIdata['comments'] = 'Payment reversal of payment voucher' . $pvmaster['PVcode'];
                                $BSIdata['supplierID'] = $pvmaster['partyID'];
                                $BSIdata['supplierCode'] = $pvmaster['partyCode'];
                                $BSIdata['supplierName'] = $pvmaster['partyName'];
                                $BSIdata['supplierAddress'] = $pvmaster['partyAddress'];
                                $BSIdata['supplierTelephone'] = $pvmaster['partyTelephone'];
                                $BSIdata['supplierFax'] = $pvmaster['partyFax'];
                                $BSIdata['transactionCurrencyID'] = $pvmaster['transactionCurrencyID'];
                                $BSIdata['transactionCurrency'] = $pvmaster['transactionCurrency'];
                                $BSIdata['transactionExchangeRate'] = $pvmaster['transactionExchangeRate'];
                                $BSIdata['transactionAmount'] = $pvdetailSupp['transactionAmount'];
                                $BSIdata['transactionCurrencyDecimalPlaces'] = $pvmaster['transactionCurrencyDecimalPlaces'];
                                $BSIdata['companyLocalCurrencyID'] = $pvmaster['companyLocalCurrencyID'];
                                $BSIdata['companyLocalCurrency'] = $pvmaster['companyLocalCurrency'];
                                $BSIdata['companyLocalExchangeRate'] = $pvmaster['companyLocalExchangeRate'];
                                $BSIdata['companyLocalAmount'] = $pvdetailSupp['companyLocalAmount'];
                                $BSIdata['companyLocalCurrencyDecimalPlaces'] = $pvmaster['companyLocalCurrencyDecimalPlaces'];
                                $BSIdata['companyReportingCurrencyID'] = $pvmaster['companyReportingCurrencyID'];
                                $BSIdata['companyReportingCurrency'] = $pvmaster['companyReportingCurrency'];
                                $BSIdata['companyReportingExchangeRate'] = $pvmaster['companyReportingExchangeRate'];
                                $BSIdata['companyReportingAmount'] = $pvdetailSupp['companyReportingAmount'];
                                $BSIdata['companyReportingCurrencyDecimalPlaces'] = $pvmaster['companyReportingCurrencyDecimalPlaces'];
                                $BSIdata['supplierCurrencyID'] = $pvmaster['partyCurrencyID'];
                                $BSIdata['supplierCurrency'] = $pvmaster['partyCurrency'];
                                $BSIdata['supplierCurrencyExchangeRate'] = $pvmaster['partyExchangeRate'];
                                $BSIdata['supplierCurrencyAmount'] = $pvmaster['partyCurrencyAmount'];
                                $BSIdata['supplierCurrencyDecimalPlaces'] = $pvmaster['partyCurrencyDecimalPlaces'];
                                $BSIdata['supplierliabilityAutoID'] = $invoiceGlDetails['liabilityAutoID'];
                                $BSIdata['supplierliabilitySystemGLCode'] = $invoiceGlDetails['liabilitySystemGLCode'];
                                $BSIdata['supplierliabilityGLAccount'] = $invoiceGlDetails['liabilityGLAccount'];
                                $BSIdata['supplierliabilityDescription'] = $invoiceGlDetails['liabilityDescription'];
                                $BSIdata['supplierliabilityType'] = $invoiceGlDetails['liabilityType'];
                                $BSIdata['approvedYN'] = 1;
                                $BSIdata['approvedDate'] = current_date();
                                $BSIdata['confirmedYN'] = 1;
                                $BSIdata['approvedbyEmpID'] = $this->common_data['current_userID'];
                                $BSIdata['approvedbyEmpName'] = $this->common_data['current_user'];
                                $BSIdata['companyCode'] = $this->common_data['company_data']['company_code'];
                                $BSIdata['companyID'] = $this->common_data['company_data']['company_id'];
                                $BSIdata['createdUserGroup'] = $this->common_data['user_group'];
                                $BSIdata['createdPCID'] = $this->common_data['current_pc'];
                                $BSIdata['createdUserID'] = $this->common_data['current_userID'];
                                $BSIdata['createdUserName'] = $this->common_data['current_user'];
                                $BSIdata['createdDateTime'] = $this->common_data['current_date'];

                                $BSIdata['bookingInvCode'] = $this->sequence->sequence_generator('BSI');
                                $paySupplierM = $this->db->insert('srp_erp_paysupplierinvoicemaster', $BSIdata);
                                $InvoiceAutoID = $this->db->insert_id();
                                if ($paySupplierM) {
                                    $BSIDdata['InvoiceAutoID'] = $InvoiceAutoID;
                                    $BSIDdata['grvType'] = 'Standard';
                                    $BSIDdata['description'] = 'Payment reversal of payment voucher' . $pvmaster['PVcode'];
                                    $BSIDdata['transactionAmount'] = $pvdetailSupp['transactionAmount'];
                                    $BSIDdata['companyLocalAmount'] = $pvdetailSupp['companyLocalAmount'];
                                    $BSIDdata['companyReportingAmount'] = $pvdetailSupp['companyReportingAmount'];
                                    $BSIDdata['companyCode'] = $this->common_data['company_data']['company_code'];
                                    $BSIDdata['companyID'] = $this->common_data['company_data']['company_id'];
                                    $BSIDdata['createdUserGroup'] = $this->common_data['user_group'];
                                    $BSIDdata['createdPCID'] = $this->common_data['current_pc'];
                                    $BSIDdata['createdUserID'] = $this->common_data['current_userID'];
                                    $BSIDdata['createdUserName'] = $this->common_data['current_user'];
                                    $BSIDdata['createdDateTime'] = $this->common_data['current_date'];

                                    $result = $this->db->insert('srp_erp_paysupplierinvoicedetail', $BSIDdata);
                                    if ($result) {

                                        if($invoiceGlDetails){
                                            $invGLLia['documentCode'] = 'PRVR';
                                            $invGLLia['documentMasterAutoID'] = $paymentReversalAutoID;
                                            $invGLLia['documentSystemCode'] = $data['documentSystemCode'];
                                            $invGLLia['documentType'] = $pvmaster['pvType'];
                                            $invGLLia['documentDate'] = $reversalDate;
                                            $invGLLia['documentYear'] = $year;
                                            $invGLLia['documentMonth'] = $month;
                                            $invGLLia['documentNarration'] = 'Payment reversal of payment voucher' . $pvmaster['PVcode'];
                                            $invGLLia['GLAutoID'] = $invoiceGlDetails['GLAutoID'];
                                            $invGLLia['systemGLCode'] = $invoiceGlDetails['systemAccountCode'];
                                            $invGLLia['GLCode'] = $invoiceGlDetails['GLSecondaryCode'];
                                            $invGLLia['GLDescription'] = $invoiceGlDetails['GLDescription'];
                                            $invGLLia['GLType'] = $invoiceGlDetails['subCategory'];
                                            $invGLLia['amount_type'] = 'cr';

                                            $invGLLia['transactionCurrencyID'] = $pvmaster['transactionCurrencyID'];
                                            $invGLLia['transactionCurrency'] = $pvmaster['transactionCurrency'];
                                            $invGLLia['transactionExchangeRate'] = $pvmaster['transactionExchangeRate'];
                                            $invGLLia['transactionAmount'] = $invoiceGlDetails['Amount']*-1;
                                            $invGLLia['transactionCurrencyDecimalPlaces'] = $pvmaster['transactionCurrencyDecimalPlaces'];

                                            $invGLLia['companyLocalCurrencyID'] = $pvmaster['companyLocalCurrencyID'];
                                            $invGLLia['companyLocalCurrency'] = $pvmaster['companyLocalCurrency'];
                                            $invGLLia['companyLocalExchangeRate'] = $pvmaster['companyLocalExchangeRate'];
                                            $invGLLia['companyLocalAmount'] = $invoiceGlDetails['compLocalAmount']*-1;
                                            $invGLLia['companyLocalCurrencyDecimalPlaces'] = $pvmaster['companyLocalCurrencyDecimalPlaces'];

                                            $invGLLia['companyReportingCurrencyID'] = $pvmaster['companyReportingCurrencyID'];
                                            $invGLLia['companyReportingCurrency'] = $pvmaster['companyReportingCurrency'];
                                            $invGLLia['companyReportingExchangeRate'] = $pvmaster['companyReportingExchangeRate'];
                                            $invGLLia['companyReportingAmount'] = $invoiceGlDetails['compReportingAmount']*-1;
                                            $invGLLia['companyReportingCurrencyDecimalPlaces'] = $pvmaster['companyReportingCurrencyDecimalPlaces'];
                                            $invGLLia['partyAutoID'] = $pvmaster['partyID'];
                                            $invGLLia['partyName'] = $pvmaster['partyName'];
                                            $invGLLia['subLedgerType'] = 2;
                                            $invGLLia['subLedgerDesc'] = 'AP';
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
                                            $invGL['documentCode'] = 'PRVR';
                                            $invGL['documentMasterAutoID'] = $paymentReversalAutoID;
                                            $invGL['documentSystemCode'] = $data['documentSystemCode'];
                                            $invGL['documentType'] = $pvmaster['pvType'];
                                            $invGL['documentDate'] = $reversalDate;
                                            $invGL['documentYear'] = $year;
                                            $invGL['documentMonth'] = $month;
                                            $invGL['documentNarration'] = 'Payment reversal of payment voucher' . $pvmaster['PVcode'];
                                            $invGL['GLAutoID'] = $ItemGlDetails['GLAutoID'];
                                            $invGL['systemGLCode'] = $ItemGlDetails['systemAccountCode'];
                                            $invGL['GLCode'] = $ItemGlDetails['GLSecondaryCode'];
                                            $invGL['GLDescription'] = $ItemGlDetails['GLDescription'];
                                            $invGL['GLType'] = $ItemGlDetails['subCategory'];
                                            $invGL['amount_type'] = 'cr';

                                            $invGL['transactionCurrencyID'] = $pvmaster['transactionCurrencyID'];
                                            $invGL['transactionCurrency'] = $pvmaster['transactionCurrency'];
                                            $invGL['transactionExchangeRate'] = $pvmaster['transactionExchangeRate'];
                                            if($ItemGlDetails['taxPercentage']== null){
                                                $taxPercentage=0;
                                            }else{
                                                $taxPercentage=$ItemGlDetails['taxPercentage'];
                                            }
                                            $itemTaxamount=($ItemGlDetails['Amount']/100)*$taxPercentage;
                                            $invGL['transactionAmount'] = ($ItemGlDetails['Amount']+$itemTaxamount)*-1;
                                            $invGL['transactionCurrencyDecimalPlaces'] = $pvmaster['transactionCurrencyDecimalPlaces'];

                                            $invGL['companyLocalCurrencyID'] = $pvmaster['companyLocalCurrencyID'];
                                            $invGL['companyLocalCurrency'] = $pvmaster['companyLocalCurrency'];
                                            $invGL['companyLocalExchangeRate'] = $pvmaster['companyLocalExchangeRate'];
                                            $invGL['companyLocalAmount'] = ($ItemGlDetails['compLocalAmount']+$itemTaxamount)*-1;
                                            $invGL['companyLocalCurrencyDecimalPlaces'] = $pvmaster['companyLocalCurrencyDecimalPlaces'];

                                            $invGL['companyReportingCurrencyID'] = $pvmaster['companyReportingCurrencyID'];
                                            $invGL['companyReportingCurrency'] = $pvmaster['companyReportingCurrency'];
                                            $invGL['companyReportingExchangeRate'] = $pvmaster['companyReportingExchangeRate'];
                                            $invGL['companyReportingAmount'] = ($ItemGlDetails['compReportingAmount']+$itemTaxamount)*-1;
                                            $invGL['companyReportingCurrencyDecimalPlaces'] = $pvmaster['companyReportingCurrencyDecimalPlaces'];
                                            $invGL['partyAutoID'] = $pvmaster['partyID'];
                                            $invGL['partyName'] = $pvmaster['partyName'];
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
                                            $invGLbnk['documentCode'] = 'PRVR';
                                            $invGLbnk['documentMasterAutoID'] = $paymentReversalAutoID;
                                            $invGLbnk['documentSystemCode'] = $data['documentSystemCode'];
                                            $invGLbnk['documentType'] = $pvmaster['pvType'];
                                            $invGLbnk['documentDate'] = $reversalDate;
                                            $invGLbnk['documentYear'] = $year;
                                            $invGLbnk['documentMonth'] = $month;
                                            $invGLbnk['documentNarration'] = 'Payment reversal of payment voucher' . $pvmaster['PVcode'];
                                            $invGLbnk['GLAutoID'] = $bankGlBankLeager['GLAutoID'];
                                            $invGLbnk['systemGLCode'] = $bankGlBankLeager['systemAccountCode'];
                                            $invGLbnk['GLCode'] = $bankGlBankLeager['GLSecondaryCode'];
                                            $invGLbnk['GLDescription'] = $bankGlBankLeager['GLDescription'];
                                            $invGLbnk['GLType'] = $bankGlBankLeager['subCategory'];
                                            $invGLbnk['amount_type'] = 'dr';

                                            $invGLbnk['transactionCurrencyID'] = $pvmaster['transactionCurrencyID'];
                                            $invGLbnk['transactionCurrency'] = $pvmaster['transactionCurrency'];
                                            $invGLbnk['transactionExchangeRate'] = $pvmaster['transactionExchangeRate'];
                                            if($bankGlBankLeager['taxPercentage']== null){
                                                $taxPercentageb=0;
                                            }else{
                                                $taxPercentageb=$bankGlBankLeager['taxPercentage'];
                                            }
                                            $bankTaxamount=($bankGlBankLeager['Amount']/100)*$taxPercentageb;
                                            $invGLbnk['transactionAmount'] = $bankGlBankLeager['Amount']+$bankTaxamount;
                                            $invGLbnk['transactionCurrencyDecimalPlaces'] = $pvmaster['transactionCurrencyDecimalPlaces'];

                                            $invGLbnk['companyLocalCurrencyID'] = $pvmaster['companyLocalCurrencyID'];
                                            $invGLbnk['companyLocalCurrency'] = $pvmaster['companyLocalCurrency'];
                                            $invGLbnk['companyLocalExchangeRate'] = $pvmaster['companyLocalExchangeRate'];
                                            $invGLbnk['companyLocalAmount'] = $bankGlBankLeager['compLocalAmount']+$bankTaxamount;
                                            $invGLbnk['companyLocalCurrencyDecimalPlaces'] = $pvmaster['companyLocalCurrencyDecimalPlaces'];

                                            $invGLbnk['companyReportingCurrencyID'] = $pvmaster['companyReportingCurrencyID'];
                                            $invGLbnk['companyReportingCurrency'] = $pvmaster['companyReportingCurrency'];
                                            $invGLbnk['companyReportingExchangeRate'] = $pvmaster['companyReportingExchangeRate'];
                                            $invGLbnk['companyReportingAmount'] = $bankGlBankLeager['compReportingAmount']+$bankTaxamount;
                                            $invGLbnk['companyReportingCurrencyDecimalPlaces'] = $pvmaster['companyReportingCurrencyDecimalPlaces'];
                                            $invGLbnk['partyAutoID'] = $pvmaster['partyID'];
                                            $invGLbnk['partyName'] = $pvmaster['partyName'];
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
                                        $BankLedger['transactionType'] = 1;
                                        $BankLedger['partyType'] = 'SUP';
                                        $BankLedger['partyAutoID'] = $pvmaster['partyID'];
                                        $BankLedger['partyCode'] = $pvmaster['partyCode'];
                                        $BankLedger['partyName'] = $pvmaster['partyName'];
                                        $BankLedger['partyCurrencyID'] = $pvmaster['partyCurrencyID'];
                                        $BankLedger['partyCurrency'] = $pvmaster['partyCurrency'];
                                        $BankLedger['partyCurrencyExchangeRate'] = $pvmaster['partyExchangeRate'];
                                        $BankLedger['partyCurrencyDecimalPlaces'] = $pvmaster['partyCurrencyDecimalPlaces'];
                                        $BankLedger['bankCurrencyID'] = $pvmaster['bankCurrencyID'];
                                        $BankLedger['bankCurrency'] = $pvmaster['bankCurrency'];
                                        $BankLedger['bankCurrencyExchangeRate'] = $pvmaster['bankCurrencyExchangeRate'];
                                        $BankLedger['bankCurrencyAmount'] = ($pvdetail['transactionAmount'] / $pvmaster['bankCurrencyExchangeRate']);
                                        $BankLedger['bankCurrencyDecimalPlaces'] = $pvmaster['bankCurrencyDecimalPlaces'];
                                        $BankLedger['transactionCurrencyID'] = $pvmaster['transactionCurrencyID'];
                                        $BankLedger['transactionCurrency'] = $pvmaster['transactionCurrency'];
                                        $BankLedger['transactionAmount'] = $pvdetail['transactionAmount'];
                                        $BankLedger['transactionCurrencyDecimalPlaces'] = $pvmaster['transactionCurrencyDecimalPlaces'];
                                        $BankLedger['chequeNo'] = $pvmaster['PVchequeNo'];
                                        $BankLedger['chequeDate'] = $pvmaster['PVchequeDate'];
                                        $BankLedger['bankName'] = $pvmaster['PVbank'];
                                        $BankLedger['bankGLAutoID'] = $pvmaster['bankGLAutoID'];
                                        $BankLedger['bankSystemAccountCode'] = $pvmaster['bankSystemAccountCode'];
                                        $BankLedger['bankGLSecondaryCode'] = $pvmaster['bankGLSecondaryCode'];
                                        $BankLedger['documentMasterAutoID'] = $receiptVoucherAutoId;
                                        $BankLedger['documentType'] = 'RV';
                                        $BankLedger['documentSystemCode'] = $RVdata['RVcode'];

                                        $BankLedger['companyCode'] = $this->common_data['company_data']['company_code'];
                                        $BankLedger['companyID'] = $this->common_data['company_data']['company_id'];
                                        $BankLedger['createdPCID'] = $this->common_data['current_pc'];
                                        $BankLedger['createdUserID'] = $this->common_data['current_userID'];
                                        $BankLedger['createdUserName'] = $this->common_data['current_user'];
                                        $BankLedger['createdDateTime'] = $this->common_data['current_date'];
                                        $this->db->insert('srp_erp_bankledger', $BankLedger);

                                        return array('s', 'Payment voucher reversed successfully');
                                    }
                                } else {
                                    return array('e', 'Payment voucher reversal failed 3.1');
                                }
                            } else {
                                return array('e', 'Payment voucher reversal failed 2.1');
                            }
                        } else {
                            return array('e', 'Payment voucher reversal failed 2');
                        }
                    } else {
                        $RVdata['documentID'] = 'RV';
                        $RVdata['prvrID'] = $paymentReversalAutoID;
                        $RVdata['RVdate'] = $reversalDate;
                        $RVdata['RVType'] = 'Direct';
                        $RVdata['companyFinanceYearID'] = $companyFinanceYearID['companyFinanceYearID'];
                        $RVdata['FYPeriodDateFrom'] = $companyFinanceYearID['dateFrom'];
                        $RVdata['FYPeriodDateTo'] = $companyFinanceYearID['dateTo'];
                        $RVdata['companyFinancePeriodID'] = $companyFinanceYearID['companyFinancePeriodID'];
                        $RVdata['modeOfPayment'] = $pvmaster['modeOfPayment'];
                        $RVdata['RVbankCode'] = $pvmaster['PVbankCode'];
                        $RVdata['RVbankSwiftCode'] = $pvmaster['PVbankSwiftCode'];
                        $RVdata['bankGLAutoID'] = $pvmaster['bankGLAutoID'];
                        $RVdata['bankSystemAccountCode'] = $pvmaster['bankSystemAccountCode'];
                        $RVdata['bankGLSecondaryCode'] = $pvmaster['bankGLSecondaryCode'];
                        $RVdata['RVbank'] = $pvmaster['PVbank'];
                        $RVdata['RVbankAccount'] = $pvmaster['PVbankAccount'];
                        $RVdata['RVbankBranch'] = $pvmaster['PVbankBranch'];
                        $RVdata['RVbankType'] = $pvmaster['PVbankType'];
                        $RVdata['RVchequeNo'] = $pvmaster['PVchequeNo'];
                        $RVdata['bankCurrencyID'] = $pvmaster['bankCurrencyID'];
                        $RVdata['bankCurrency'] = $pvmaster['bankCurrency'];
                        $RVdata['bankCurrencyExchangeRate'] = $pvmaster['bankCurrencyExchangeRate'];
                        $RVdata['bankCurrencyAmount'] = ($pvdetail['transactionAmount'] / $pvmaster['bankCurrencyExchangeRate']);
                        $RVdata['bankCurrencyDecimalPlaces'] = $pvmaster['bankCurrencyDecimalPlaces'];
                        $RVdata['RVchequeDate'] = $pvmaster['PVchequeDate'];
                        $RVdata['RVNarration'] = 'Payment reversal of payment voucher' . $pvmaster['PVcode'];
                        $RVdata['transactionCurrencyID'] = $pvmaster['transactionCurrencyID'];
                        $RVdata['transactionCurrency'] = $pvmaster['transactionCurrency'];
                        $RVdata['transactionAmount'] = $pvdetail['transactionAmount'];
                        $RVdata['transactionCurrencyDecimalPlaces'] = $pvmaster['transactionCurrencyDecimalPlaces'];
                        $RVdata['companyLocalCurrencyID'] = $pvmaster['companyLocalCurrencyID'];
                        $RVdata['companyLocalCurrency'] = $pvmaster['companyLocalCurrency'];
                        $RVdata['companyLocalExchangeRate'] = $pvmaster['companyLocalExchangeRate'];
                        $RVdata['companyLocalAmount'] = $pvdetail['companyLocalAmount'];
                        $RVdata['companyLocalCurrencyDecimalPlaces'] = $pvmaster['companyLocalCurrencyDecimalPlaces'];
                        $RVdata['companyReportingCurrencyID'] = $pvmaster['companyReportingCurrencyID'];
                        $RVdata['companyReportingCurrency'] = $pvmaster['companyReportingCurrency'];
                        $RVdata['companyReportingExchangeRate'] = $pvmaster['companyReportingExchangeRate'];
                        $RVdata['companyReportingAmount'] = $pvdetail['companyReportingAmount'];
                        $RVdata['companyReportingCurrencyDecimalPlaces'] = $pvmaster['companyReportingCurrencyDecimalPlaces'];
                        $RVdata['approvedYN'] = 1;
                        $RVdata['approvedDate'] = current_date();
                        $RVdata['confirmedYN'] = 1;
                        $RVdata['approvedbyEmpID'] = $this->common_data['current_userID'];
                        $RVdata['approvedbyEmpName'] = $this->common_data['current_user'];
                        $RVdata['companyCode'] = $this->common_data['company_data']['company_code'];
                        $RVdata['companyID'] = $this->common_data['company_data']['company_id'];
                        $RVdata['createdUserGroup'] = $this->common_data['user_group'];
                        $RVdata['createdPCID'] = $this->common_data['current_pc'];
                        $RVdata['createdUserID'] = $this->common_data['current_userID'];
                        $RVdata['createdUserName'] = $this->common_data['current_user'];
                        $RVdata['createdDateTime'] = $this->common_data['current_date'];

                        $RVdata['RVcode'] = $this->sequence->sequence_generator('RV');
                        $payCustomerReceiptM = $this->db->insert('srp_erp_customerreceiptmaster', $RVdata);
                        $receiptVoucherAutoId = $this->db->insert_id();
                        if ($payCustomerReceiptM) {
                            $RVDdata['receiptVoucherAutoId'] = $receiptVoucherAutoId;
                            $RVDdata['type'] = 'PRVR';
                            $RVDdata['transactionAmount'] = $pvdetail['transactionAmount'];
                            $RVDdata['companyLocalAmount'] = $pvdetail['companyLocalAmount'];
                            $RVDdata['companyReportingAmount'] = $pvdetail['companyReportingAmount'];
                            $RVDdata['companyCode'] = $this->common_data['company_data']['company_code'];
                            $RVDdata['companyID'] = $this->common_data['company_data']['company_id'];
                            $RVDdata['createdUserGroup'] = $this->common_data['user_group'];
                            $RVDdata['createdPCID'] = $this->common_data['current_pc'];
                            $RVDdata['createdUserID'] = $this->common_data['current_userID'];
                            $RVDdata['createdUserName'] = $this->common_data['current_user'];
                            $RVDdata['createdDateTime'] = $this->common_data['current_date'];

                            $result = $this->db->insert('srp_erp_customerreceiptdetail', $RVDdata);
                            if ($result) {

                                if($ItemGlDetails){
                                    $invGL['documentCode'] = 'PRVR';
                                    $invGL['documentMasterAutoID'] = $paymentReversalAutoID;
                                    $invGL['documentSystemCode'] = $data['documentSystemCode'];
                                    $invGL['documentType'] = $pvmaster['pvType'];
                                    $invGL['documentDate'] = $reversalDate;
                                    $invGL['documentYear'] = $year;
                                    $invGL['documentMonth'] = $month;
                                    $invGL['documentNarration'] = 'Payment reversal of payment voucher' . $pvmaster['PVcode'];
                                    $invGL['GLAutoID'] = $ItemGlDetails['GLAutoID'];
                                    $invGL['systemGLCode'] = $ItemGlDetails['systemAccountCode'];
                                    $invGL['GLCode'] = $ItemGlDetails['GLSecondaryCode'];
                                    $invGL['GLDescription'] = $ItemGlDetails['GLDescription'];
                                    $invGL['GLType'] = $ItemGlDetails['subCategory'];
                                    $invGL['amount_type'] = 'cr';

                                    $invGL['transactionCurrencyID'] = $pvmaster['transactionCurrencyID'];
                                    $invGL['transactionCurrency'] = $pvmaster['transactionCurrency'];
                                    $invGL['transactionExchangeRate'] = $pvmaster['transactionExchangeRate'];
                                    if(empty($ItemGlDetails['taxPercentage'])){
                                        $ItemGlDetails['taxPercentage']=0;
                                    }
                                    if($ItemGlDetails['taxPercentage']== null){
                                        $taxPercentage=0;
                                    }else{
                                        $taxPercentage=$ItemGlDetails['taxPercentage'];
                                    }
                                    $itemTaxamount=($ItemGlDetails['Amount']/100)*$taxPercentage;
                                    $invGL['transactionAmount'] = ($ItemGlDetails['Amount']+$itemTaxamount)*-1;
                                    $invGL['transactionCurrencyDecimalPlaces'] = $pvmaster['transactionCurrencyDecimalPlaces'];

                                    $invGL['companyLocalCurrencyID'] = $pvmaster['companyLocalCurrencyID'];
                                    $invGL['companyLocalCurrency'] = $pvmaster['companyLocalCurrency'];
                                    $invGL['companyLocalExchangeRate'] = $pvmaster['companyLocalExchangeRate'];
                                    $invGL['companyLocalAmount'] = ($ItemGlDetails['compLocalAmount']+$itemTaxamount)*-1;
                                    $invGL['companyLocalCurrencyDecimalPlaces'] = $pvmaster['companyLocalCurrencyDecimalPlaces'];

                                    $invGL['companyReportingCurrencyID'] = $pvmaster['companyReportingCurrencyID'];
                                    $invGL['companyReportingCurrency'] = $pvmaster['companyReportingCurrency'];
                                    $invGL['companyReportingExchangeRate'] = $pvmaster['companyReportingExchangeRate'];
                                    $invGL['companyReportingAmount'] = ($ItemGlDetails['compReportingAmount']+$itemTaxamount)*-1;
                                    $invGL['companyReportingCurrencyDecimalPlaces'] = $pvmaster['companyReportingCurrencyDecimalPlaces'];
                                    $invGL['partyAutoID'] = $pvmaster['partyID'];
                                    $invGL['partyName'] = $pvmaster['partyName'];
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
                                    $invGLbnk['documentCode'] = 'PRVR';
                                    $invGLbnk['documentMasterAutoID'] = $paymentReversalAutoID;
                                    $invGLbnk['documentSystemCode'] = $data['documentSystemCode'];
                                    $invGLbnk['documentType'] = $pvmaster['pvType'];
                                    $invGLbnk['documentDate'] = $reversalDate;
                                    $invGLbnk['documentYear'] = $year;
                                    $invGLbnk['documentMonth'] = $month;
                                    $invGLbnk['documentNarration'] = 'Payment reversal of payment voucher' . $pvmaster['PVcode'];
                                    $invGLbnk['GLAutoID'] = $bankGlBankLeager['GLAutoID'];
                                    $invGLbnk['systemGLCode'] = $bankGlBankLeager['systemAccountCode'];
                                    $invGLbnk['GLCode'] = $bankGlBankLeager['GLSecondaryCode'];
                                    $invGLbnk['GLDescription'] = $bankGlBankLeager['GLDescription'];
                                    $invGLbnk['GLType'] = $bankGlBankLeager['subCategory'];
                                    $invGLbnk['amount_type'] = 'dr';

                                    $invGLbnk['transactionCurrencyID'] = $pvmaster['transactionCurrencyID'];
                                    $invGLbnk['transactionCurrency'] = $pvmaster['transactionCurrency'];
                                    $invGLbnk['transactionExchangeRate'] = $pvmaster['transactionExchangeRate'];
                                    if($bankGlBankLeager['taxPercentage']== null){
                                        $taxPercentageb=0;
                                    }else{
                                        $taxPercentageb=$bankGlBankLeager['taxPercentage'];
                                    }
                                    $bankTaxamount=($bankGlBankLeager['Amount']/100)*$taxPercentageb;
                                    $invGLbnk['transactionAmount'] = $bankGlBankLeager['Amount']+$bankTaxamount;
                                    $invGLbnk['transactionCurrencyDecimalPlaces'] = $pvmaster['transactionCurrencyDecimalPlaces'];

                                    $invGLbnk['companyLocalCurrencyID'] = $pvmaster['companyLocalCurrencyID'];
                                    $invGLbnk['companyLocalCurrency'] = $pvmaster['companyLocalCurrency'];
                                    $invGLbnk['companyLocalExchangeRate'] = $pvmaster['companyLocalExchangeRate'];
                                    $invGLbnk['companyLocalAmount'] = $bankGlBankLeager['compLocalAmount']+$bankTaxamount;
                                    $invGLbnk['companyLocalCurrencyDecimalPlaces'] = $pvmaster['companyLocalCurrencyDecimalPlaces'];

                                    $invGLbnk['companyReportingCurrencyID'] = $pvmaster['companyReportingCurrencyID'];
                                    $invGLbnk['companyReportingCurrency'] = $pvmaster['companyReportingCurrency'];
                                    $invGLbnk['companyReportingExchangeRate'] = $pvmaster['companyReportingExchangeRate'];
                                    $invGLbnk['companyReportingAmount'] = $bankGlBankLeager['compReportingAmount']+$bankTaxamount;
                                    $invGLbnk['companyReportingCurrencyDecimalPlaces'] = $pvmaster['companyReportingCurrencyDecimalPlaces'];
                                    $invGLbnk['partyAutoID'] = $pvmaster['partyID'];
                                    $invGLbnk['partyName'] = $pvmaster['partyName'];
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
                                $BankLedger['transactionType'] = 1;
                                $BankLedger['partyType'] = 'SUP';
                                $BankLedger['partyAutoID'] = $pvmaster['partyID'];
                                $BankLedger['partyCode'] = $pvmaster['partyCode'];
                                $BankLedger['partyName'] = $pvmaster['partyName'];
                                $BankLedger['partyCurrencyID'] = $pvmaster['partyCurrencyID'];
                                $BankLedger['partyCurrency'] = $pvmaster['partyCurrency'];
                                $BankLedger['partyCurrencyExchangeRate'] = $pvmaster['partyExchangeRate'];
                                $BankLedger['partyCurrencyDecimalPlaces'] = $pvmaster['partyCurrencyDecimalPlaces'];
                                $BankLedger['bankCurrencyID'] = $pvmaster['bankCurrencyID'];
                                $BankLedger['bankCurrency'] = $pvmaster['bankCurrency'];
                                $BankLedger['bankCurrencyExchangeRate'] = $pvmaster['bankCurrencyExchangeRate'];
                                $BankLedger['bankCurrencyAmount'] = $pvmaster['bankCurrencyAmount'];
                                $BankLedger['bankCurrencyDecimalPlaces'] = $pvmaster['bankCurrencyDecimalPlaces'];
                                $BankLedger['transactionCurrencyID'] = $pvmaster['transactionCurrencyID'];
                                $BankLedger['transactionCurrency'] = $pvmaster['transactionCurrency'];
                                $BankLedger['transactionAmount'] = $pvdetail['transactionAmount'];
                                $BankLedger['transactionCurrencyDecimalPlaces'] = $pvmaster['transactionCurrencyDecimalPlaces'];
                                $BankLedger['chequeNo'] = $pvmaster['PVchequeNo'];
                                $BankLedger['chequeDate'] = $pvmaster['PVchequeDate'];
                                $BankLedger['bankName'] = $pvmaster['PVbank'];
                                $BankLedger['bankGLAutoID'] = $pvmaster['bankGLAutoID'];
                                $BankLedger['bankSystemAccountCode'] = $pvmaster['bankSystemAccountCode'];
                                $BankLedger['bankGLSecondaryCode'] = $pvmaster['bankGLSecondaryCode'];
                                $BankLedger['documentMasterAutoID'] = $receiptVoucherAutoId;
                                $BankLedger['documentType'] = 'RV';
                                $BankLedger['documentSystemCode'] = $RVdata['RVcode'];

                                $BankLedger['companyCode'] = $this->common_data['company_data']['company_code'];
                                $BankLedger['companyID'] = $this->common_data['company_data']['company_id'];
                                $BankLedger['createdPCID'] = $this->common_data['current_pc'];
                                $BankLedger['createdUserID'] = $this->common_data['current_userID'];
                                $BankLedger['createdUserName'] = $this->common_data['current_user'];
                                $BankLedger['createdDateTime'] = $this->common_data['current_date'];
                                $this->db->insert('srp_erp_bankledger', $BankLedger);

                                return array('s', 'Payment voucher reversed successfully');
                            }

                        } else {
                            return array('e', 'Payment voucher reversal failed');
                        }
                    }
                } else {
                    return array('e', 'Payment voucher reversal failed');
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

    function fetch_payment_voucher_template_data($payVoucherAutoId)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select(' *,DATE_FORMAT(PVdate, \'' . $convertFormat . '\') AS PVdate,DATE_FORMAT(PVchequeDate,\'' . $convertFormat . '\') AS PVchequeDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
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

}