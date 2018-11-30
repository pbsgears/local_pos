<?php

class Payment_voucher extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helpers('payable');
        $this->load->model('Payment_voucher_model');
        $this->load->helpers('exceedmatch');
    }

    function fetch_payment_voucher()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND partyID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( PVdate >= '" . $datefromconvert . " 00:00:00' AND PVdate <= '" . $datetoconvert . " 23:59:00')";
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
        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( PVcode Like '%$search%' ESCAPE '!') OR ( pvType Like '%$sSearch%') OR (transactionCurrency Like '%$sSearch%') OR (det.transactionAmount Like '%$sSearch%') OR (PVNarration Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (PVdate Like '%$sSearch%')) ";
        }


        $where = "srp_erp_paymentVouchermaster.companyID = " . $companyid . $supplier_filter . $date . $status_filter . $searches . "";
        $this->datatables->select('srp_erp_paymentVouchermaster.payVoucherAutoId as payVoucherAutoId,PVNarration,PVcode,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,confirmedYN,approvedYN,srp_erp_paymentVouchermaster.createdUserID as createdUser,transactionCurrency,transactionCurrencyDecimalPlaces,pvType,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)) as total_value_search,srp_erp_paymentVouchermaster.isDeleted as isDeleted,bankGLAutoID,case pvType when \'Direct\' then partyName when \'Employee\' then srp_employeesdetails.Ename2 when \'Supplier\' then srp_erp_suppliermaster.supplierName end as partyName,paymentType');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type!="debitnote" GROUP BY payVoucherAutoId) det', '(det.payVoucherAutoId = srp_erp_paymentVouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="GL" OR srp_erp_paymentvoucherdetail.type="Item"  GROUP BY payVoucherAutoId) tyepdet', '(tyepdet.payVoucherAutoId = srp_erp_paymentVouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="debitnote" GROUP BY payVoucherAutoId) debitnote', '(debitnote.payVoucherAutoId = srp_erp_paymentVouchermaster.payVoucherAutoId)', 'left');
        //$this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentVouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentVouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->where($where);
        $this->datatables->where('pvType <>', 'SC');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paymentVouchermaster.partyID', 'left');
        $this->datatables->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_paymentVouchermaster.partyID', 'left');
        $this->datatables->from('srp_erp_paymentVouchermaster');
        $this->datatables->add_column('pv_detail', '<b>Supplier Name : </b> $2 <br> <b>Voucher Date : </b> $3 <b>&nbsp;&nbsp; Type : </b> $5 <br> <b>Comments : </b> $1 ', 'PVNarration,partyName,PVdate,transactionCurrency,pvType');
        //$this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'payment_voucher_total_value(payVoucherAutoId,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PV",payVoucherAutoId)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"PV",payVoucherAutoId)');
        $this->datatables->add_column('edit', '$1', 'load_pv_action(payVoucherAutoId,confirmedYN,approvedYN,createdUser,PV,isDeleted,bankGLAutoID,paymentType,pvType)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_commission_payment()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND partyID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( PVdate >= '" . $datefromconvert . " 00:00:00' AND PVdate <= '" . $datetoconvert . " 23:59:00')";
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
        $where = "companyID = " . $companyid . $supplier_filter . $date . $status_filter . "";
        $this->datatables->select('srp_erp_paymentVouchermaster.payVoucherAutoId as payVoucherAutoId,PVNarration,PVcode,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,confirmedYN,approvedYN,createdUserID,partyName,transactionCurrency,transactionCurrencyDecimalPlaces,pvType,(IFNULL(addondet.transactionAmount,0)+IFNULL(det.transactionAmount,0)) as total_value,(IFNULL(addondet.transactionAmount,0)+IFNULL(det.transactionAmount,0)) as total_value_search,isDeleted');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail GROUP BY payVoucherAutoId) det', '(det.payVoucherAutoId = srp_erp_paymentVouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentVouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->where($where);
        $this->datatables->where('pvType', 'SC');
        $this->datatables->from('srp_erp_paymentVouchermaster');
        $this->datatables->add_column('pv_detail', '<b>Sales person Name : </b> $2 <br><b>Voucher Date : </b> $3 <b>&nbsp;&nbsp; Type : </b> $5 <br> <b>Comments : </b> $1  ', 'PVNarration,partyName,PVdate,transactionCurrency,pvType');
        //$this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'payment_voucher_total_value(payVoucherAutoId,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PV",payVoucherAutoId)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"PV",payVoucherAutoId)');
        $this->datatables->add_column('edit', '$1', 'load_pv_action(payVoucherAutoId,confirmedYN,approvedYN,createdUserID,"SC",isDeleted,bankGLAutoID)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }


    function fetch_payment_match()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( matchDate >= '" . $datefromconvert . " 00:00:00' AND matchDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1)";
            }
        }
        $where = "srp_erp_pvadvancematch.companyID = " . $companyid . $supplier_filter . $date . $status_filter . "";
        $this->datatables->select('srp_erp_pvadvancematch.matchID as matchID,DATE_FORMAT(matchDate,\'' . $convertFormat . '\') AS matchDate ,matchSystemCode,refNo,Narration,srp_erp_suppliermaster.supplierName as supliermastername,transactionCurrency ,transactionCurrencyDecimalPlaces,confirmedYN,det.transactionAmount as total_value,det.transactionAmount as detTransactionAmount,isDeleted');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,matchID FROM srp_erp_pvadvancematchdetails GROUP BY matchID) det', '(det.matchID = srp_erp_pvadvancematch.matchID)', 'left');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_pvadvancematch');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_pvadvancematch.supplierID');
        $this->datatables->add_column('detail', '<b>Supplier Name : </b> $2 <br> <b>Voucher Date : </b> $3  <b>  <br>  <b>Comments : </b> $1', 'Narration,supliermastername,matchDate,transactionCurrency');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_approval(confirmedYN)');
        $this->datatables->add_column('edit', '$1', 'load_pvm_action(matchID,confirmedYN,isDeleted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function save_paymentvoucher_header()
    {
        $date_format_policy = date_format_policy();
        $Pdte = $this->input->post('PVdate');
        $PVdate = input_format_date($Pdte, $date_format_policy);

        $PVchqDte = $this->input->post('PVchequeDate');
        $voucherType = $this->input->post('vouchertype');
        $PVchequeDate = input_format_date($PVchqDte, $date_format_policy);

        $this->form_validation->set_rules('vouchertype', 'Voucher Type', 'trim|required');
        $this->form_validation->set_rules('segment', 'Segment', 'trim|required');
        $this->form_validation->set_rules('PVdate', 'Payment Voucher Date', 'trim|required|validate_date');
        if ($voucherType == 'Direct') {
            $this->form_validation->set_rules('referenceno', 'Reference No', 'trim|required');
            $this->form_validation->set_rules('narration', 'Narration', 'trim|required');
        }

        //$this->form_validation->set_rules('supplier', 'Supplier', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('PVbankCode', 'Bank Code', 'trim|required');
        $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
        $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');


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

        if ($bank_detail['isCash'] == 0) {
            //$this->form_validation->set_rules('PVchequeNo', 'Cheque Number', 'trim|required');
            if ($voucherType == 'Supplier') {
                $this->form_validation->set_rules('paymentType', 'Payment Type', 'trim|required');
            }
            if($this->input->post('paymentType')==2 && $voucherType == 'Supplier'){
                $this->form_validation->set_rules('supplierBankMasterID', 'Supplier Bank', 'trim|required');
            }else{
                $this->form_validation->set_rules('PVchequeDate', 'Cheque Date', 'trim|required');
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $financearray = $this->input->post('financeyear_period');
            $financePeriod = fetchFinancePeriod($financearray);
            if ($PVdate >= $financePeriod['dateFrom'] && $PVdate <= $financePeriod['dateTo']) {

                if ($PVchequeDate < $PVdate && $bank_detail['isCash'] == 0 && $this->input->post('paymentType')==1) {
                    $this->session->set_flashdata('e', 'Cheque Date Cannot be less than Payment Voucher Date  !');
                    echo json_encode(FALSE);

                } else {
                    echo json_encode($this->Payment_voucher_model->save_paymentvoucher_header());
                }

            } else {
                $this->session->set_flashdata('e', 'Payment Voucher Date not between Financial period !');
                echo json_encode(FALSE);
            }
        }
    }

    function save_payment_match_header()
    {
        // $this->form_validation->set_rules('PVdate', 'Payment Voucher Date', 'trim|required');
        // $this->form_validation->set_rules('referenceno', 'Reference No', 'trim|required');
        $this->form_validation->set_rules('supplierID', 'Supplier', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Payment Currency', 'trim|required');
        // $this->form_validation->set_rules('narration', 'Narration', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Payment_voucher_model->save_payment_match_header());
        }
    }

    function save_direct_pv_detail()
    {
        $projectExist = project_is_exist();
        $this->form_validation->set_rules('gl_code', 'GL Code', 'trim|required');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        //$this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('segment_gl', 'Segment', 'trim|required');
        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Payment_voucher_model->save_direct_pv_detail());
        }
    }

    function save_direct_pv_detail_multiple()
    {
        $gl_codes = $this->input->post('gl_code');
        $segment_gls = $this->input->post('segment_gl');
        $descriptions = $this->input->post('description');
        $amount = $this->input->post('amount');
        $projectExist = project_is_exist();

        foreach ($gl_codes as $key => $gl_code) {
            $this->form_validation->set_rules("gl_code[{$key}]", 'GL Code', 'required|trim');
            $this->form_validation->set_rules("segment_gl[{$key}]", 'Segment', 'required|trim');
            $this->form_validation->set_rules("amount[{$key}]", 'Amount', 'trim|required');
            $this->form_validation->set_rules("description[{$key}]", 'Description', 'trim|required');
            if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            }
        }
        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Payment_voucher_model->save_direct_pv_detail_multiple());
        }
    }

    function load_payment_match_header()
    {
        echo json_encode($this->Payment_voucher_model->load_payment_match_header());
    }

    function delete_pv()
    {
        echo json_encode($this->Payment_voucher_model->delete_pv());
    }

    function delete_pv_match_detail()
    {
        echo json_encode($this->Payment_voucher_model->delete_pv_match_detail());
    }

    function fetch_pv_advance_detail()
    {
        echo json_encode($this->Payment_voucher_model->fetch_pv_advance_detail());
    }

    function fetch_payment_voucher_detail()
    {
        echo json_encode($this->Payment_voucher_model->fetch_payment_voucher_detail());
    }

    function delete_item_direct()
    {
        echo json_encode($this->Payment_voucher_model->delete_item_direct());
    }

    function fetch_pv_direct_details()
    {
        echo json_encode($this->Payment_voucher_model->fetch_pv_direct_details());
    }

    function load_payment_voucher_header()
    {
        echo json_encode($this->Payment_voucher_model->load_payment_voucher_header());
    }

    function fetch_match_detail()
    {
        echo json_encode($this->Payment_voucher_model->fetch_match_detail());
    }

    function load_pv_conformation()
    {
        $payVoucherAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('payVoucherAutoId'));
        $data['extra'] = $this->Payment_voucher_model->fetch_payment_voucher_template_data($payVoucherAutoId);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Payment_voucher_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }

        $html = $this->load->view('system/payment_voucher/erp_payment_voucher_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {

            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function load_pv_match_conformation()
    {
        $matchID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('matchID'));
        $data['extra'] = $this->Payment_voucher_model->fetch_payment_voucher_match_template_data($matchID);
        $html = $this->load->view('system/payment_voucher/erp_payment_voucher_match_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }

    function fetch_detail()
    {
        $data['master'] = $this->Payment_voucher_model->load_payment_voucher_header();
        if ($data['master']['pvType'] == 'Supplier') {
            $data['supplier_po'] = $this->Payment_voucher_model->fetch_supplier_po($data['master']['partyID'], $data['master']['transactionCurrencyID'], $data['master']['PVdate']);
            $data['supplier_inv'] = $this->Payment_voucher_model->fetch_supplier_inv($data['master']['partyID'], $data['master']['transactionCurrencyID'], $data['master']['PVdate']);
            $data['debit_note'] = $this->Payment_voucher_model->fetch_debit_note($data['master']['partyID'], $data['master']['transactionCurrencyID'], $data['master']['PVdate']);
        }
        if ($data['master']['pvType'] == 'SC') {
            $data['sales_commission'] = $this->Payment_voucher_model->fetch_sales_person($data['master']['partyID'], $data['master']['transactionCurrencyID'], $data['master']['payVoucherAutoId']);
        }

        $data['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId'));
        $data['pvType'] = $data['master']['pvType'];
        $data['partyID'] = $data['master']['partyID'];
        $data['gl_code_arr'] = dropdown_all_revenue_gl();
        $data['gl_code_arr_income'] = dropdown_all_revenue_gl('PLI');
        $data['segment_arr'] = fetch_segment();
        $data['tab'] = $this->input->post('tab');
        $data['detail'] = $this->Payment_voucher_model->fetch_detail();
        $this->load->view('system/payment_voucher/payment_voucher_detail', $data);
    }

    function save_inv_tax_detail()
    {
        $this->form_validation->set_rules('text_type', 'Tax Type', 'trim|required');
        $this->form_validation->set_rules('percentage', 'Percentage', 'trim|required');
        $this->form_validation->set_rules('payVoucherAutoId', 'Payment Voucher ID', 'trim|required');
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'data' => validation_errors()));
        } else {
            echo json_encode($this->Payment_voucher_model->save_inv_tax_detail());
        }
    }

    function save_sales_rep_payment()
    {
        $this->form_validation->set_rules('salesPersonID', 'Sales Person', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('transactionAmount', 'Payment Amount', 'trim|required');
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'data' => validation_errors()));
        } else {
            echo json_encode($this->Payment_voucher_model->save_sales_rep_payment());
        }
    }

    function delete_tax_detail()
    {
        echo json_encode($this->Payment_voucher_model->delete_tax_detail());
    }

    function fetch_detail_header_lock()
    {

        echo json_encode($this->Payment_voucher_model->fetch_detail());
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

    function fetch_payment_voucher_approval()
    {
        /*                 * rejected = 1
                 * not rejected = 0
                 * */
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $this->datatables->select('srp_erp_paymentVouchermaster.payVoucherAutoId as PayVoucherAutoId,PVcode,PVNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,transactionCurrency,transactionCurrencyDecimalPlaces,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)) as total_value_search,case pvType when \'Direct\' then partyName when \'Employee\' then srp_employeesdetails.Ename2 when \'Supplier\' then srp_erp_suppliermaster.supplierName end as partyName', false);
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type!="debitnote" GROUP BY payVoucherAutoId) det', '(det.payVoucherAutoId = srp_erp_paymentVouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="GL" OR srp_erp_paymentvoucherdetail.type="Item"  GROUP BY payVoucherAutoId) tyepdet', '(tyepdet.payVoucherAutoId = srp_erp_paymentVouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="debitnote" GROUP BY payVoucherAutoId) debitnote', '(debitnote.payVoucherAutoId = srp_erp_paymentVouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,SUM(taxPercentage) as taxPercentage,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentVouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paymentVouchermaster.partyID', 'left');
        $this->datatables->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_paymentVouchermaster.partyID', 'left');
        $this->datatables->from('srp_erp_paymentvouchermaster');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_paymentvouchermaster.PayVoucherAutoId AND srp_erp_documentapproved.approvalLevelID = srp_erp_paymentvouchermaster.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_paymentvouchermaster.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'PV');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'PV');
        $this->datatables->where('pvType <>', 'SC');
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN')));
        $this->datatables->where('srp_erp_paymentvouchermaster.companyID', $companyID);
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('PVcode', '$1', 'approval_change_modal(PVcode,PayVoucherAutoId,documentApprovedID,approvalLevelID,approvedYN,PV,0)');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"PV",PayVoucherAutoId)');
        $this->datatables->add_column('edit', '$1', 'pv_action_approval(PayVoucherAutoId,approvalLevelID,approvedYN,documentApprovedID,0)');

        echo $this->datatables->generate();
    }

    function fetch_commission_payment_approval()
    {
        /*                 * rejected = 1
                 * not rejected = 0
                 * */
        $approvedYN = trim($this->input->post('approvedYN'));
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $this->datatables->select('srp_erp_paymentVouchermaster.payVoucherAutoId as PayVoucherAutoId,PVcode,PVNarration,partyName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,transactionCurrency,transactionCurrencyDecimalPlaces,(IFNULL(addondet.transactionAmount,0)+IFNULL(det.transactionAmount,0)) as total_value,(IFNULL(addondet.transactionAmount,0)+IFNULL(det.transactionAmount,0)) as total_value_search', false);
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail GROUP BY payVoucherAutoId) det', '(det.payVoucherAutoId = srp_erp_paymentVouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentVouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->from('srp_erp_paymentvouchermaster');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_paymentvouchermaster.PayVoucherAutoId AND srp_erp_documentapproved.approvalLevelID = srp_erp_paymentvouchermaster.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_paymentvouchermaster.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'PV');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'PV');
        $this->datatables->where('pvType', 'SC');
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN')));
        $this->datatables->where('srp_erp_paymentvouchermaster.companyID', $companyID);
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('PVcode', '$1', 'approval_change_modal(PVcode,PayVoucherAutoId,documentApprovedID,approvalLevelID,approvedYN,PV,0)');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"PV",PayVoucherAutoId)');
        $this->datatables->add_column('edit', '$1', 'pv_action_approval(PayVoucherAutoId,approvalLevelID,approvedYN,documentApprovedID,0)');

        echo $this->datatables->generate();
    }


    function save_pv_item_detail()
    {
        $projectExist = project_is_exist();
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        $this->form_validation->set_rules("wareHouseAutoID", 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('UnitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('quantityRequested', 'Quantity Requested', 'trim|required');
        $this->form_validation->set_rules('estimatedAmount', 'Estimated Amount', 'trim|required');
        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Payment_voucher_model->save_pv_item_detail());
        }
    }

    function save_pv_item_detail_multiple()
    {
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item 1', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("wareHouseAutoID[{$key}]", 'Warehouse', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Amount', 'trim|required');
            if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Payment_voucher_model->save_pv_item_detail_multiple());
        }

    }

    function payment_confirmation()
    {
        echo json_encode($this->Payment_voucher_model->payment_confirmation());
    }

    function payment_match_confirmation()
    {
        echo json_encode($this->Payment_voucher_model->payment_match_confirmation());
    }

    function delete_payment_match()
    {
        echo json_encode($this->Payment_voucher_model->delete_payment_match());
    }

    function save_inv_base_items()
    {
        echo json_encode($this->Payment_voucher_model->save_inv_base_items());
    }

    function save_debitNote_base_items()
    {
        echo json_encode($this->Payment_voucher_model->save_debitNote_base_items());
    }

    function delete_payment_voucher()
    {
        echo json_encode($this->Payment_voucher_model->delete_payment_voucher());
    }


    function save_pv_approval()
    {
        $system_code = trim($this->input->post('payVoucherAutoId'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'PV', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('payVoucherAutoId');
                $this->db->where('payVoucherAutoId', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_paymentvouchermaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('payVoucherAutoId', 'Payment Voucher ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Payment_voucher_model->save_pv_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('payVoucherAutoId');
            $this->db->where('payVoucherAutoId', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_paymentvouchermaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'PV', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('payVoucherAutoId', 'Payment Voucher ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Payment_voucher_model->save_pv_approval());
                    }
                }
            }
        }
    }

    function save_pv_po_detail()
    {
        /*$this->form_validation->set_rules('po_code', 'PO Code', 'trim|required');*/
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Payment_voucher_model->save_pv_po_detail());
        }
    }

    function referback_payment_voucher()
    {
        $payVoucherAutoId = $this->input->post('payVoucherAutoId');

        $this->db->select('approvedYN,PVcode');
        $this->db->where('payVoucherAutoId', trim($payVoucherAutoId));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_paymentvouchermaster');
        $approved_commision_payment = $this->db->get()->row_array();
        if (!empty($approved_commision_payment)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_commision_payment['PVcode']));
        } else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($payVoucherAutoId, 'PV');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }


    }

    function save_match_amount()
    {
        $this->form_validation->set_rules('matchID', 'Match ID', 'trim|required');
        $amounts = $this->input->post('amounts');
        foreach ($amounts as $key => $amount) {
            $this->form_validation->set_rules("amounts[{$key}]", 'Amount', 'trim|required');
            $this->form_validation->set_rules("InvoiceAutoID[{$key}]", 'Invoice', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'messsage' => validation_errors()));
        } else {
            echo json_encode($this->Payment_voucher_model->save_match_amount());
        }
    }

    function referback_payment_match()
    {
        $matchID = $this->input->post('matchID');

        $data['confirmedYN'] = 3;
        $data['confirmedByEmpID'] = NULL;
        $data['confirmedByName'] = NULL;
        $data['confirmedDate'] = NULL;
        $this->db->where('matchID', $matchID);
        $result = $this->db->update('srp_erp_pvadvancematch', $data);
        $this->db->delete('srp_erp_documentapproved', array('documentSystemCode' => $matchID, 'documentID' => 'PVM', 'companyID' => $this->common_data['company_data']['company_id']));

        if ($result) {
            echo json_encode(array('s', ' Referred Back Successfully.', $result));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $result));
        }
    }

    function save_commission_base_items()
    {
        echo json_encode($this->Payment_voucher_model->save_commission_base_items());
    }

    function re_open_commisionpayment()
    {
        echo json_encode($this->Payment_voucher_model->re_open_commisionpayment());
    }

    function re_open_payment_voucher()
    {
        echo json_encode($this->Payment_voucher_model->re_open_payment_voucher());
    }

    function re_open_payment_match()
    {
        echo json_encode($this->Payment_voucher_model->re_open_payment_match());
    }

    function cheque_print()
    {

        $payVoucherAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('payVoucherAutoId'));
        $coaChequeTemplateID = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('coaChequeTemplateID'));
        $data['extra'] = $this->Payment_voucher_model->fetch_payment_voucher_cheque_data($payVoucherAutoId);

        $this->db->select('pageLink');
        $this->db->where('coaChequeTemplateID', $coaChequeTemplateID);
        $this->db->from('srp_erp_chartofaccountchequetemplates');
        $pagelink = $this->db->get()->row_array();

        $this->load->library('NumberToWords');
        $html = $this->load->view('system/payment_voucher/' . $pagelink['pageLink'] . '', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4', '1',null,0);

    }

    function load_Cheque_templates()
    {
        $payVoucherAutoId = $this->input->post('payVoucherAutoId');

        $data['extra'] = $this->Payment_voucher_model->load_Cheque_templates($payVoucherAutoId);
        $html = $this->load->view('system/payment_voucher/ajax-erp_load_Cheque_templates', $data, true);
        echo $html;
    }

    function get_po_amount(){
        echo json_encode($this->Payment_voucher_model->get_po_amount());
    }

    function get_supplier_banks(){
        echo json_encode($this->Payment_voucher_model->get_supplier_banks());
    }

    function load_pv_bank_transfer()
    {
        $payVoucherAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('payVoucherAutoId'));
        $data['extra'] = $this->Payment_voucher_model->fetch_payment_voucher_transfer_data($payVoucherAutoId);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Payment_voucher_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $this->load->library('NumberToWords');
        $html = $this->load->view('system/payment_voucher/erp_payment_voucher_transfer_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {

            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'],null,0);
        }
    }

}
