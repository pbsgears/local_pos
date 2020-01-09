<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PaymentReversal extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Payment_reversale_model');
        $this->load->helpers('paymentReversal');
    }

    function fetch_payment_reversal()
    {
        // date inter change according to company policy
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND partyID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( documentDate >= '" . $datefromconvert . " 00:00:00' AND documentDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $where2=" AND srp_erp_paymentvouchermaster.approvedYN=1 AND srp_erp_paymentvouchermaster.rrvrID is NULL";
        //$where2=" AND NOT EXISTS (SELECT * FROM srp_erp_paymentreversalmaster WHERE srp_erp_paymentreversalmaster.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)";
        $where = "srp_erp_paymentvouchermaster.companyID = " . $companyid . $supplier_filter . $date . $status_filter .$where2 ;

        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_paymentvouchermaster.payVoucherAutoId as payVoucherAutoId,srp_erp_paymentvouchermaster.companyCode,PVcode,PVchequeNo,DATE_FORMAT(PVchequeDate,'$convertFormat') AS PVchequeDate,approvedYN ,DATE_FORMAT(PVdate,'$convertFormat') AS PVdate,srp_erp_paymentvouchermaster.transactionCurrency,det.transactionAmount as amount,det.currency as detailCurrency,dets.taxPercentage as taxPercentage,det.currencydecimal as decimal,(((IFNULL(dets.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)) as totamount");
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId,srp_erp_paymentvoucherdetail.transactionCurrency as currency,srp_erp_paymentvoucherdetail.transactionCurrencyDecimalPlaces as currencydecimal FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type!="debitnote" GROUP BY payVoucherAutoId) det', '(det.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="GL" OR srp_erp_paymentvoucherdetail.type="Item"  GROUP BY payVoucherAutoId) tyepdet', '(tyepdet.payVoucherAutoId = srp_erp_paymentVouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT IFNULL(SUM(taxPercentage),0) AS taxPercentage,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails GROUP BY payVoucherAutoId) dets', '(dets.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT documentMasterAutoID FROM srp_erp_bankledger WHERE documentType="PV" AND clearedYN = 0 GROUP BY documentMasterAutoID) bankleger', '(bankleger.documentMasterAutoID = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'inner');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="debitnote" GROUP BY payVoucherAutoId) debitnote', '(debitnote.payVoucherAutoId = srp_erp_paymentVouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->from('srp_erp_paymentvouchermaster');
        //$this->datatables->edit_column('total_value', '<div class="pull-right"><b>$1 : </b>  </div>', 'transactionCurrency');
        //$this->datatables->where($where);
        //$this->datatables->where($where);
        //$this->datatables->add_column('totamount', '$1', 'get_total_amount(amount,taxPercentage,detailCurrency,decimal)');
        $this->datatables->edit_column('totamount', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(totamount,decimal),detailCurrency');
        $this->datatables->add_column('edit', '$1', 'load_prvr_action(payVoucherAutoId,0)');
        $this->datatables->where('srp_erp_paymentvouchermaster.payVoucherAutoId NOT IN (SELECT srp_erp_paymentreversalmaster.payVoucherAutoId FROM srp_erp_paymentreversalmaster WHERE srp_erp_paymentreversalmaster.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)');
        $this->datatables->where($where);
        echo $this->datatables->generate();
    }

    function fetch_reversed_payment(){
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND partyID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( documentDate >= '" . $datefromconvert . " 00:00:00' AND documentDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $where2=" AND srp_erp_paymentvouchermaster.approvedYN=1";
        //$where2=" AND NOT EXISTS (SELECT * FROM srp_erp_paymentreversalmaster WHERE srp_erp_paymentreversalmaster.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)";
        $where = "srp_erp_paymentvouchermaster.companyID = " . $companyid . $supplier_filter . $date . $status_filter .$where2 ;

        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_paymentvouchermaster.payVoucherAutoId as payVoucherAutoId,srp_erp_paymentvouchermaster.companyCode,PVcode,PVchequeNo,DATE_FORMAT(PVchequeDate,'$convertFormat') AS PVchequeDate,approvedYN ,DATE_FORMAT(PVdate,'$convertFormat') AS PVdate,srp_erp_paymentvouchermaster.transactionCurrency,det.transactionAmount as amount,det.currency as detailCurrency,dets.taxPercentage as taxPercentage,det.currencydecimal as decimal,(((IFNULL(dets.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)) as totamount");
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId,srp_erp_paymentvoucherdetail.transactionCurrency as currency,srp_erp_paymentvoucherdetail.transactionCurrencyDecimalPlaces as currencydecimal FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type!="debitnote" GROUP BY payVoucherAutoId) det', '(det.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="GL" OR srp_erp_paymentvoucherdetail.type="Item"  GROUP BY payVoucherAutoId) tyepdet', '(tyepdet.payVoucherAutoId = srp_erp_paymentVouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT IFNULL(SUM(taxPercentage),0) AS taxPercentage,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails GROUP BY payVoucherAutoId) dets', '(dets.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT documentMasterAutoID FROM srp_erp_bankledger WHERE documentType="PV" GROUP BY documentMasterAutoID) bankleger', '(bankleger.documentMasterAutoID = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'inner');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="debitnote" GROUP BY payVoucherAutoId) debitnote', '(debitnote.payVoucherAutoId = srp_erp_paymentVouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->from('srp_erp_paymentvouchermaster');
        //$this->datatables->edit_column('total_value', '<div class="pull-right"><b>$1 : </b>  </div>', 'transactionCurrency');
        //$this->datatables->where($where);
        //$this->datatables->where($where);
        //$this->datatables->add_column('totamount', '$1', 'get_total_amount(amount,taxPercentage,detailCurrency,decimal)');
        $this->datatables->edit_column('totamount', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(totamount,decimal),detailCurrency');
        $this->datatables->add_column('edit', '$1', 'load_prvr_action(payVoucherAutoId,1)');
        $this->datatables->where('srp_erp_paymentvouchermaster.payVoucherAutoId IN (SELECT srp_erp_paymentreversalmaster.payVoucherAutoId FROM srp_erp_paymentreversalmaster WHERE srp_erp_paymentreversalmaster.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)');
        $this->datatables->where($where);
        echo $this->datatables->generate();
    }

    function load_html()
    {
        //onchange="fetch_supplier_currency(this.value)"
        $select_value = trim($this->input->post('select_value'));
        if (trim($this->input->post('value')) == 'Employee') {
            echo form_dropdown('partyID', all_employee_drop(), $select_value, 'class="form-control select2" id="partyID"');
        } elseif (trim($this->input->post('value')) == 'Sales Rep') {
            echo form_dropdown('partyID', all_srp_erp_sales_person_drop(), $select_value, 'class="form-control select2" id="partyID"');
        } else {
            echo form_dropdown('partyID', all_supplier_drop(), $select_value, 'class="form-control select2" id="partyID" required onchange="fetch_supplier_currency_by_id(this.value)"');
        }
    }

    function save_paymentreversal_header()
    {
        $date_format_policy = date_format_policy();
        $Pdte = $this->input->post('documentDate');
        $documentDate = input_format_date($Pdte, $date_format_policy);

        $voucherType = $this->input->post('Type');

        $this->form_validation->set_rules('Type', 'Type', 'trim|required');
        $this->form_validation->set_rules('documentDate', 'Document Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('referenceNo', 'Reference No', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('PVbankCode', 'Bank Code', 'trim|required');
        $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
        $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        $this->form_validation->set_rules('narration', 'Narration', 'trim|required');

        if ($voucherType == 'Supplier') {
            $this->form_validation->set_rules('partyID', 'Supplier', 'trim|required');
        } elseif ($voucherType == 'Direct') {
            $this->form_validation->set_rules('partyName', 'Payee Name', 'trim|required');
        } elseif ($voucherType == 'Employee') {
            $this->form_validation->set_rules('partyID', 'Employee Name', 'trim|required');
        } elseif ($voucherType == 'SC') {
            $this->form_validation->set_rules('partyID', 'Sales Person', 'trim|required');
        }
        $bank_detail = fetch_gl_account_desc($this->input->post('PVbankCode'));


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $financearray = $this->input->post('financeyear_period');
            $financePeriod = fetchFinancePeriod($financearray);
            if ($documentDate >= $financePeriod['dateFrom'] && $documentDate <= $financePeriod['dateTo']) {
                    echo json_encode($this->Payment_reversale_model->save_paymentreversal_header());

            } else {
                $this->session->set_flashdata('e', 'Document Date not between Financial period !');
                echo json_encode(FALSE);
            }
        }
    }

    function load_payment_reversal_header()
    {
        echo json_encode($this->Payment_reversale_model->load_payment_reversal_header());
    }

    function fetch_PRVR_detail_table()
    {
        echo json_encode($this->Payment_reversale_model->fetch_PRVR_detail_table());
    }

    function fetch_Pv_detail_table()
    {
        echo json_encode($this->Payment_reversale_model->fetch_Pv_detail_table());
    }

    function save_Payment_Reversale_detail()
    {
        $this->form_validation->set_rules('checkboxprvr[]', 'Select Payment Voucher', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e','Select an payment voucher'));
        } else {
            echo json_encode($this->Payment_reversale_model->save_Payment_Reversale_detail());
        }
    }

    function delete_payment_reversale_detail()
    {
        echo json_encode($this->Payment_reversale_model->delete_payment_reversale_detail());
    }

    function load_payment_reversal_conformation()
    {
        $paymentReversalAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('paymentReversalAutoID'));
        $data['extra'] = $this->Payment_reversale_model->fetch_template_data($paymentReversalAutoID);
        $data['approval'] = $this->input->post('approval');
        $html = $this->load->view('system/PaymentReversal/erp_payment_reversale_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function delete_payment_reversal()
    {
        echo json_encode($this->Payment_reversale_model->delete_payment_reversal());
    }
    function reverse_paymentVoucher()
    {
        $this->form_validation->set_rules('payVoucherAutoId', 'Payment voucher id', 'trim|required');
        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        $this->form_validation->set_rules('reversalDate', 'Date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e',validation_errors()));
        } else {
            echo json_encode($this->Payment_reversale_model->reverse_paymentVoucher());
        }

    }
    function payment_reversal_confirmation()
    {
        echo json_encode($this->Payment_reversale_model->payment_reversal_confirmation());
    }

    function referback_paymentReversal()
    {
        $paymentReversalAutoID = $this->input->post('paymentReversalAutoID');

        $this->load->library('approvals');
        $status = $this->approvals->approve_delete($paymentReversalAutoID, 'PRVR');
        if ($status == 1) {
            echo json_encode(array('s', ' Referred Back Successfully.', $status));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $status));
        }

    }


    function load_prvr_conformation()
    {
        $payVoucherAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('payVoucherAutoId'));
        $data['extra'] = $this->Payment_reversale_model->fetch_payment_voucher_template_data($payVoucherAutoId);
        $data['approval'] = $this->input->post('approval');
        $html = $this->load->view('system/PaymentReversal/erp_payment_voucher_prvr_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

}