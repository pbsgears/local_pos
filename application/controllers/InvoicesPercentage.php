<?php

class InvoicesPercentage extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helpers('buyback_helper');
        $this->load->model('InvoicesPercentage_model');
    }

    function fetch_invoices()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $customer_filter = '';
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 23:59:00')";
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
        $where = "srp_erp_customerinvoicemaster_temp.companyID = " . $companyid . $customer_filter . $date . $status_filter . "";
        $this->datatables->select('srp_erp_customerinvoicemaster_temp.invoiceAutoID as invoiceAutoID,invoiceCode,invoiceNarration,srp_erp_customermaster.customerName as customermastername,transactionCurrencyDecimalPlaces,transactionCurrency, confirmedYN,approvedYN,srp_erp_customerinvoicemaster_temp.createdUserID as createdUser,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,invoiceType,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value_search,isDeleted');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails_temp GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster_temp.invoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails_temp  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster_temp.InvoiceAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster_temp.customerID', 'left');
        $this->datatables->from('srp_erp_customerinvoicemaster_temp');
        $this->datatables->add_column('invoice_detail', '<b>Customer Name : </b> $2 <br> <b>Document Date : </b> $3 <b style="text-indent: 1%;">&nbsp | &nbsp Due Date : </b> $4 <br> <b>Type : </b> $5 <br> <b>Comments : </b> $1 ', 'trim_desc(invoiceNarration),customermastername,invoiceDate,invoiceDueDate,invoiceType');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"CINV",invoiceAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"CINV",invoiceAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_invoice_percentage_action(invoiceAutoID,confirmedYN,approvedYN,createdUser,confirmedYN,isDeleted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_invoices_buyback()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $customer_filter = '';
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 23:59:00')";
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
        $where = "srp_erp_customerinvoicemaster_temp.companyID = " . $companyid . $customer_filter . $date . $status_filter . "";
        $this->datatables->select('srp_erp_customerinvoicemaster_temp.invoiceAutoID as invoiceAutoID,invoiceCode,invoiceNarration,srp_erp_customermaster.customerName as customermastername,transactionCurrencyDecimalPlaces,transactionCurrency, confirmedYN,approvedYN,srp_erp_customerinvoicemaster_temp.createdUserID as createdUserID,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,invoiceType,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value_search,isDeleted');
        $this->datatables->join('(SELECT SUM(tranasactionDividedAmount) as transactionAmount,sum(dividedTotalAfterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails_temp GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster_temp.invoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails_temp  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster_temp.InvoiceAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster_temp.customerID', 'left');
        $this->datatables->from('srp_erp_customerinvoicemaster_temp');
        $this->datatables->add_column('invoice_detail', '<b>Customer Name : </b> $2 <br> <b>Document Date : </b> $3 <b style="text-indent: 1%;">&nbsp | &nbsp Due Date : </b> $4 <br> <b>Type : </b> $5 <br> <b>Comments : </b> $1 ', 'trim_desc(invoiceNarration),customermastername,invoiceDate,invoiceDueDate,invoiceType');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"HCINV",invoiceAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"HCINV",invoiceAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_invoice_action_buyback(invoiceAutoID,confirmedYN,approvedYN,createdUserID,confirmedYN,isDeleted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }


    function fetch_invoices_buyback_dayClose()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $customer_filter = '';
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 23:59:00')";
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
        $where = "srp_erp_customerinvoicemaster_temp.companyID = " . $companyid . " AND isDayClosed = 0 ".  $customer_filter . $date . $status_filter . "";
        $this->datatables->select('srp_erp_customerinvoicemaster_temp.invoiceAutoID as invoiceAutoID,invoiceCode,invoiceNarration,srp_erp_customermaster.customerName as customermastername,transactionCurrencyDecimalPlaces,transactionCurrency, confirmedYN,approvedYN,srp_erp_customerinvoicemaster_temp.createdUserID,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,invoiceType,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value_search,isDeleted');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails_temp GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster_temp.invoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails_temp  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster_temp.InvoiceAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster_temp.customerID', 'left');
        $this->datatables->from('srp_erp_customerinvoicemaster_temp');
        $this->datatables->add_column('invoice_detail', '<b>Customer Name : </b> $2 <br> <b>Document Date : </b> $3 <b style="text-indent: 1%;">&nbsp | &nbsp Due Date : </b> $4 <br> <b>Type : </b> $5 <br> <b>Comments : </b> $1 ', 'trim_desc(invoiceNarration),customermastername,invoiceDate,invoiceDueDate,invoiceType');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"HCINV",invoiceAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"HCINV",invoiceAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_invoice_action_buyback_dayclose(invoiceAutoID,confirmedYN,approvedYN,createdUserID,confirmedYN,isDeleted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }


    function fetch_invoices_buyback_dayClose_modelView()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerCode');
        $status = $this->input->post('status');
        $customer_filter = '';
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $where = "srp_erp_customerinvoicemaster_temp.companyID = " . $companyid . " AND isDayClosed = 0 AND confirmedYN = 1 AND approvedYN = 1".  $customer_filter . $date  . "";
        $this->datatables->select('srp_erp_customerinvoicemaster_temp.invoiceAutoID as invoiceAutoID,invoiceCode,invoiceNarration,srp_erp_customermaster.customerName as customermastername,transactionCurrencyDecimalPlaces,transactionCurrency, confirmedYN,approvedYN,srp_erp_customerinvoicemaster_temp.createdUserID,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,invoiceType,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value_search,isDeleted,eliminateYN');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails_temp GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster_temp.invoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails_temp  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster_temp.InvoiceAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster_temp.customerID', 'left');
        $this->datatables->from('srp_erp_customerinvoicemaster_temp');
        $this->datatables->add_column('invoice_detail', '<b>Customer Name : </b> $2 <br> <b>Document Date : </b> $3 <b style="text-indent: 1%;">&nbsp | &nbsp Due Date : </b> $4 <br> <b>Type : </b> $5 <br> <b>Comments : </b> $1 ', 'trim_desc(invoiceNarration),customermastername,invoiceDate,invoiceDueDate,invoiceType');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"HCINV",invoiceAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"HCINV",invoiceAutoID)');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="ItemsSelectedSync(this)" name="checkedInvoice[]" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'invoiceAutoID');
        //$this->datatables->add_column('eliminate', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="eliminateYN_$1" onclick="InvoiceEliminate(this)" name="eliminateYN[]" type="checkbox" class="columnSelectedEliminate"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'invoiceAutoID,eliminateYN');
        $this->datatables->add_column('eliminate', '$1', 'eliminate_check_box(invoiceAutoID,eliminateYN)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function save_invoice_header()
    {
        $date_format_policy = date_format_policy();
        $invDueDate = $this->input->post('invoiceDueDate');
        $invoiceDueDate = input_format_date($invDueDate, $date_format_policy);
        $invDate = $this->input->post('customerInvoiceDate');
        $invoiceDate = input_format_date($invDate, $date_format_policy);
        $docDate = $this->input->post('invoiceDate');
        $documentDate = input_format_date($docDate, $date_format_policy);

        $this->form_validation->set_rules('invoiceType', 'Invoice Type', 'trim|required');
        $this->form_validation->set_rules('segment', 'Segment', 'trim|required');
        $this->form_validation->set_rules('invoiceDate', 'Invoice Date', 'trim|required');
        $this->form_validation->set_rules('invoiceDueDate', 'Invoice Due Date', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('customerID', 'Customer', 'trim|required');
        $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
        $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        $this->form_validation->set_rules('warehouseAutoIDtemp', 'WareHouse', 'trim|required');
        if ($this->input->post('invoiceType') == 'Direct') {
            $this->form_validation->set_rules('referenceNo', 'Reference No', 'trim|required');
            $this->form_validation->set_rules('invoiceNarration', 'Narration', 'trim|required');
        }


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $financearray = $this->input->post('financeyear_period');
            $financePeriod = fetchFinancePeriod($financearray);
            if (($invoiceDate) > ($invoiceDueDate)) {
                $this->session->set_flashdata('e', ' Invoice Due Date cannot be less than Invoice Date!');
                echo json_encode(FALSE);
            } else {
                if ($documentDate >= $financePeriod['dateFrom'] && $documentDate <= $financePeriod['dateTo']) {
                    echo json_encode($this->InvoicesPercentage_model->save_invoice_header());
                } else {
                    $this->session->set_flashdata('e', 'Document Date not between Financial period !');
                    echo json_encode(FALSE);
                }
            }
        }
    }

    function save_direct_invoice_detail()
    {
        $projectExist = project_is_exist();
        $gl_codes = $this->input->post('gl_code');
        $amount = $this->input->post('amount');
        $segment_gl = $this->input->post('segment_gl');

        foreach ($gl_codes as $key => $gl_code) {
            $this->form_validation->set_rules("gl_code[{$key}]", 'GL Code', 'trim|required');
            $this->form_validation->set_rules("amount[{$key}]", 'Amount', 'trim|required');
            $this->form_validation->set_rules("segment_gl[{$key}]", 'Segment', 'trim|required');
            if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));

            $this->session->set_flashdata($msgtype = 'e', join('', $validateMsg));
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->InvoicesPercentage_model->save_direct_invoice_detail());
        }
    }

    function update_income_invoice_detail()
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
            echo json_encode($this->InvoicesPercentage_model->update_income_invoice_detail());
        }
    }

    function save_con_base_items()
    {
        $ids = $this->input->post('DetailsID');
        foreach ($ids as $key => $id) {
            $num = ($key + 1);
            $this->form_validation->set_rules("DetailsID[{$key}]", "Line {$num} ID", 'trim|required');
            $this->form_validation->set_rules("amount[{$key}]", "Line {$num} Amount", 'trim|required');
            $this->form_validation->set_rules("wareHouseAutoID[{$key}]", "Line {$num} WareHouse", 'trim|required');
            $this->form_validation->set_rules("qty[{$key}]", "Line {$num} QTY", 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            //$this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->InvoicesPercentage_model->save_con_base_items());
        }
    }

    function fetch_con_detail_table()
    {
        echo json_encode($this->InvoicesPercentage_model->fetch_con_detail_table());
    }

    function delete_item_direct()
    {
        echo json_encode($this->InvoicesPercentage_model->delete_item_direct());
    }

    function referback_customer_invoice()
    {
        $invoiceAutoID = $this->input->post('invoiceAutoID');

        $this->load->library('approvals');
        $status = $this->approvals->approve_delete($invoiceAutoID, 'HCINV');
        if ($status == 1) {
            echo json_encode(array('s', ' Referred Back Successfully.', $status));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $status));
        }

    }

    function fetch_invoice_direct_details()
    {
        echo json_encode($this->InvoicesPercentage_model->fetch_invoice_direct_details());
    }

    function load_invoice_header()
    {
        echo json_encode($this->InvoicesPercentage_model->load_invoice_header());
    }

    function fetch_customer_invoice_detail()
    {
        echo json_encode($this->InvoicesPercentage_model->fetch_customer_invoice_detail());
    }

    function load_invoices_conformation()
    {
        $invoiceAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('invoiceAutoID'));
        $data['extra'] = $this->InvoicesPercentage_model->fetch_invoice_template_data($invoiceAutoID);
        $data['html'] = $this->input->post('html');
        $data['approval'] = $this->input->post('approval');

        if (!$this->input->post('html')) {
            $data['signature'] = $this->InvoicesPercentage_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $html = $this->load->view('system/invoices/erp_invoice_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function load_invoices_conformation_buyback()
    {
        $invoiceAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('invoiceAutoID'));
        $data['extra'] = $this->InvoicesPercentage_model->fetch_invoice_template_data($invoiceAutoID);
        $data['html'] = $this->input->post('html');
        $data['approval'] = $this->input->post('approval');

        if (!$this->input->post('html')) {
            $data['signature'] = $this->InvoicesPercentage_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $html = $this->load->view('system/invoices/erp_invoice_print_buyback', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function fetch_detail()
    {
        $data['master'] = $this->InvoicesPercentage_model->load_invoice_header();
        $data['invoiceAutoID'] = trim($this->input->post('invoiceAutoID'));
        $data['invoiceType'] = $data['master']['invoiceType'];
        $data['customerID'] = $data['master']['customerID'];
        $data['gl_code_arr'] = fetch_all_gl_codes();
        $data['gl_code_arr_income'] = fetch_all_gl_codes('PLI');
        $data['segment_arr'] = fetch_segment();
        $data['detail'] = $this->InvoicesPercentage_model->fetch_detail();
        $data['customer_con'] = $this->InvoicesPercentage_model->fetch_customer_con($data['master']);
        $data['tabID'] = $this->input->post('tab');
        $this->load->view('system/invoices/invoices_percentage_detail', $data);
    }

    function fetch_detail_buyback()
    {
        $data['master'] = $this->InvoicesPercentage_model->load_invoice_header();
        $data['invoiceAutoID'] = trim($this->input->post('invoiceAutoID'));
        $data['invoiceType'] = $data['master']['invoiceType'];
        $data['customerID'] = $data['master']['customerID'];
        $data['gl_code_arr'] = fetch_all_gl_codes();
        $data['segment_arr'] = fetch_segment();
        $data['detail'] = $this->InvoicesPercentage_model->fetch_detail();
        $data['customer_con'] = $this->InvoicesPercentage_model->fetch_customer_con($data['master']);
        $data['tabID'] = $this->input->post('tab');
        $this->load->view('system/invoices/invoices_detail_buyback', $data);
    }

    function fetch_detail_header_lock()
    {
        echo json_encode($this->InvoicesPercentage_model->fetch_detail());
    }

    function fetch_invoices_approval()
    {
        /*
        * rejected = 1
        * not rejected = 0
        * */

        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN'));
        $this->datatables->select('srp_erp_customerinvoicemaster_temp.invoiceAutoID as invoiceAutoID,invoiceCode,invoiceNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN, documentApprovedID, approvalLevelID,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL(det.transactionAmount,0)-(IFNULL(det.detailtaxamount,0)))))+IFNULL(det.transactionAmount,0)) as total_value,transactionCurrencyDecimalPlaces,transactionCurrency,srp_erp_customermaster.customerName as customerName', false);
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,sum(totalafterTax) as detailtaxamount,invoiceAutoID FROM srp_erp_customerinvoicedetails_temp GROUP BY invoiceAutoID) det', '(det.invoiceAutoID = srp_erp_customerinvoicemaster_temp.invoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails_temp  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_customerinvoicemaster_temp.InvoiceAutoID)', 'left');
        $this->datatables->from('srp_erp_customerinvoicemaster_temp');
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster_temp.customerID', 'left');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_customerinvoicemaster_temp.invoiceAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_customerinvoicemaster_temp.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_customerinvoicemaster_temp.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'HCINV');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'HCINV');
        $this->datatables->where('srp_erp_documentapproved.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN')));
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('invoiceCode', '$1', 'approval_change_modal(invoiceCode,invoiceAutoID,documentApprovedID,approvalLevelID,approvedYN,HCINV,0)');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"HCINV",invoiceAutoID)');
        $this->datatables->add_column('edit', '$1', 'inv_action_approval_buyback(invoiceAutoID,approvalLevelID,approvedYN,documentApprovedID,HCINV)');
        echo $this->datatables->generate();
    }

    function frm_test(){
        $formulaDetail = $this->db->query(" SELECT formulaDetailID,formula AS formulaString,taxMasters AS  payGroupCategories,taxCalculationformulaID,sortOrder
 FROM srp_erp_taxcalculationformuladetails WHERE taxCalculationformulaID = '1' ORDER BY sortOrder ASC  ")->result_array();
        if(!empty($formulaDetail) && $formulaDetail != null){
            foreach($formulaDetail as $formul){
                if(!empty($formul['formulaString'])){
                    $taxCalculationformulaID=$formul['taxCalculationformulaID'];
                    $companyID=current_companyID();
                    $sortOrder=$formul['sortOrder'];
                    echo '<pre>';print_r($formul); echo '</pre>';
                    $tax_categories = $this->db->query("SELECT
	srp_erp_taxcalculationformuladetails.*,srp_erp_taxmaster.taxDescription,srp_erp_taxmaster.taxPercentage
FROM
	srp_erp_taxcalculationformuladetails
LEFT JOIN srp_erp_taxmaster on srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
WHERE
	taxCalculationformulaID = $taxCalculationformulaID
AND srp_erp_taxcalculationformuladetails.companyID = $companyID AND sortOrder < $sortOrder  ")->result_array();

                    $formulaBuilder = tax_formulaBuilder_to_sql($formul, $tax_categories,85);

                    echo '<pre>';print_r($formulaBuilder); echo '</pre>';
                }
            }
        }
    }

    function save_invoice_item_detail()
    {
        $projectExist = project_is_exist();
        $isBuyBackCompany = isBuyBack_company();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Amount', 'trim|required');
            $this->form_validation->set_rules("wareHouseAutoID[{$key}]", 'Warehouse', 'trim|required');
            $this->form_validation->set_rules("noOfItems[{$key}]", 'No Item', 'trim|required');
            $this->form_validation->set_rules("grossQty[{$key}]", 'Gross Qty', 'trim|required');
            $this->form_validation->set_rules("noOfUnits[{$key}]", 'Units', 'trim|required');
            $this->form_validation->set_rules("deduction[{$key}]", 'Deduction', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->InvoicesPercentage_model->save_invoice_item_detail());
        }
    }

    function update_invoice_item_detail()
    {
        $projectExist = project_is_exist();
        $isBuyBackCompany = isBuyBack_company();
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        $this->form_validation->set_rules("wareHouseAutoID", 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('quantityRequested', 'Quantity Requested', 'trim|required');
        $this->form_validation->set_rules('estimatedAmount', 'Estimated Amount', 'trim|required');
        $this->form_validation->set_rules("noOfItems", 'No Item', 'trim|required');
        $this->form_validation->set_rules("grossQty", 'Gross Qty', 'trim|required');
        $this->form_validation->set_rules("noOfUnits", 'Units', 'trim|required');
        $this->form_validation->set_rules("deduction", 'Deduction', 'trim|required');
        //$this->form_validation->set_rules('UnitOfMeasureID', 'Unit Of Measure', 'trim|required');
        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->InvoicesPercentage_model->update_invoice_item_detail());
        }
    }

    function invoice_confirmation()
    {
        echo json_encode($this->InvoicesPercentage_model->invoice_confirmation());
    }

    // function save_inv_base_items(){
    //     echo json_encode($this->InvoicesPercentage_model->save_inv_base_items());
    // }

    function save_invoice_approval()
    {
        $system_code = trim($this->input->post('invoiceAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'HCINV', $level_id);
            if ($approvedYN) {
                //$this->session->set_flashdata('w', 'Document already approved');
                echo json_encode(array('w', 'Document already approved', 1));
            } else {
                $this->db->select('invoiceAutoID');
                $this->db->where('invoiceAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_customerinvoicemaster_temp');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    //$this->session->set_flashdata('w', 'Document already rejected');
                    echo json_encode(array('w', 'Document already rejected', 1));
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('invoiceAutoID', 'Payment Voucher ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        //$this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(array('e', validation_errors(), 1));
                    } else {
                        echo json_encode($this->InvoicesPercentage_model->save_invoice_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('invoiceAutoID');
            $this->db->where('invoiceAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_customerinvoicemaster_temp');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                //$this->session->set_flashdata('w', 'Document already rejected');
                echo json_encode(array('w', 'Document already rejected', 1));
            } else {
                $rejectYN = checkApproved($system_code, 'HCINV', $level_id);
                if (!empty($rejectYN)) {
                    //$this->session->set_flashdata('w', 'Document already approved');
                    echo json_encode(array('w', 'Document already approved', 1));
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('invoiceAutoID', 'Payment Voucher ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        //$this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(array('e', validation_errors(), 1));
                    } else {
                        echo json_encode($this->InvoicesPercentage_model->save_invoice_approval());
                    }
                }
            }
        }
    }

    function delete_customerInvoice_attachement()
    {
        echo json_encode($this->InvoicesPercentage_model->delete_customerInvoice_attachement());
    }

    function delete_invoice_master()
    {
        echo json_encode($this->InvoicesPercentage_model->delete_invoice_master());
    }


    function load_subItemList()
    {

        $detailID = $this->input->post('detailID');
        $documentID = $this->input->post('documentID');
        $warehouseID = $this->input->post('warehouseID');
        $data['subItems'] = $this->InvoicesPercentage_model->load_subItem_notSold($detailID, $documentID, $warehouseID);


        switch ($documentID) {
            case "CINV":
                $data['detail'] = $this->InvoicesPercentage_model->get_invoiceDetail($detailID);
                $data['attributes'] = fetch_company_assigned_attributes();
                break;

            case "RV":
                $data['detail'] = $this->InvoicesPercentage_model->get_receiptVoucherDetail($detailID);
                $data['attributes'] = fetch_company_assigned_attributes();
                break;

            case "SR":
                $data['detail'] = $this->InvoicesPercentage_model->get_stockReturnDetail($detailID);
                $data['attributes'] = fetch_company_assigned_attributes();
                break;

            case "MI":
                $data['detail'] = $this->InvoicesPercentage_model->get_materialIssueDetail($detailID);
                $data['attributes'] = fetch_company_assigned_attributes();
                break;

            case "ST":
                $data['detail'] = $this->InvoicesPercentage_model->get_stockTransferDetail($detailID);
                $data['attributes'] = fetch_company_assigned_attributes();
                break;

            case "SA":
                $data['detail'] = $this->InvoicesPercentage_model->get_stockAdjustmentDetail($detailID);
                $data['attributes'] = fetch_company_assigned_attributes();
                break;

            default:
                echo $documentID . ' Code not configured <br/>';
                echo 'File: ' . __FILE__ . '<br/>';
                echo 'Line No: ' . __LINE__ . '<br><br>';

        }

        $data['documentID'] = $documentID;
        $this->load->view('system/item/itemmastersub/load-sub-item-list', $data);
    }

    function save_subItemList()
    {
        $subItemCode = $this->input->post('subItemCode[]');
        $qty = $this->input->post('qty');

        if ($qty == count($subItemCode)) {
            $output = $this->InvoicesPercentage_model->save_subItemList();
            echo json_encode($output);

        } else {
            echo json_encode(array('error' => 1, 'message' => 'Please select ' . $qty . ' item/s.'));
        }


    }

    function re_open_invoice()
    {
        echo json_encode($this->InvoicesPercentage_model->re_open_invoice());
    }

    function customerinvoiceGLUpdate()
    {
        $this->form_validation->set_rules('PLGLAutoID', 'Cost GL Account', 'trim|required');
        if ($this->input->post('BLGLAutoID')) {
            $this->form_validation->set_rules('BLGLAutoID', 'Asset GL Account', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->InvoicesPercentage_model->customerinvoiceGLUpdate());
        }
    }

    function fetch_customer_invoice_all_detail_edit()
    {
        echo json_encode($this->InvoicesPercentage_model->fetch_customer_invoice_all_detail_edit());
    }


    function updateCustomerInvoice_edit_all_Item()
    {
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Amount', 'trim|required');
            $this->form_validation->set_rules("wareHouseAutoID[{$key}]", 'Warehouse', 'trim|required');
            if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->InvoicesPercentage_model->updateCustomerInvoice_edit_all_Item());
        }
    }

    function invoiceloademail()
    {

        echo json_encode($this->InvoicesPercentage_model->invoiceloademail());

    }

    function send_invoice_email()
    {
        $this->form_validation->set_rules('email', 'email', 'trim|valid_email');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->InvoicesPercentage_model->send_invoice_email());
        }
    }

    function day_close_invoice(){
        echo json_encode($this->InvoicesPercentage_model->day_close_invoice());
    }

    function save_inv_tax_detail()
    {
        $this->form_validation->set_rules('text_type', 'Tax Type', 'trim|required');
        $this->form_validation->set_rules('percentage', 'Percentage', 'trim|required');
        $this->form_validation->set_rules('InvoiceAutoID', 'InvoiceAutoID', 'trim|required');
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->InvoicesPercentage_model->save_inv_tax_detail());
        }
    }

    function save_inv_discount_detail()
    {
        $this->form_validation->set_rules('discountExtraChargeID', 'Discount Type', 'trim|required');
        $this->form_validation->set_rules('discountPercentage', 'Discount Percentage', 'trim|required');
        $this->form_validation->set_rules('InvoiceAutoID', 'InvoiceAutoID', 'trim|required');
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->InvoicesPercentage_model->save_inv_discount_detail());
        }
    }

    function delete_tax_detail()
    {
        echo json_encode($this->InvoicesPercentage_model->delete_tax_detail());
    }

    function delete_discount_gen()
    {
        echo json_encode($this->InvoicesPercentage_model->delete_discount_gen());
    }


    function save_inv_extra_detail()
    {
        $this->form_validation->set_rules('discountExtraChargeIDExtra', 'Extra Type', 'trim|required');
        $this->form_validation->set_rules('InvoiceAutoID', 'InvoiceAutoID', 'trim|required');
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->InvoicesPercentage_model->save_inv_extra_detail());
        }
    }

    function delete_extra_gen()
    {
        echo json_encode($this->InvoicesPercentage_model->delete_extra_gen());
    }

    function get_tax_drop(){
        echo json_encode($this->InvoicesPercentage_model->get_tax_drop());
    }

    function update_isEliminated(){
        echo json_encode($this->InvoicesPercentage_model->update_isEliminated());
    }
}
