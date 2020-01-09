<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Payable extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Payable_modal');
        $this->load->helpers('payable');
    }

    function fetch_debit_note()
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
            $date .= " AND ( debitNoteDate >= '" . $datefromconvert . " 00:00:00' AND debitNoteDate <= '" . $datetoconvert . " 23:59:00')";
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
            $searches = " AND (( debitNoteCode Like '%$search%' ESCAPE '!') OR (det.transactionAmount Like '%$sSearch%') OR (comments Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (debitNoteDate Like '%$sSearch%') OR (transactionCurrency Like '%$sSearch%')) ";
        }
        $where = "srp_erp_debitnotemaster.companyID = " . $companyid . $supplier_filter . $date . $status_filter . $searches. "";
        $this->datatables->select('srp_erp_debitnotemaster.debitNoteMasterAutoID as debitNoteMasterAutoID,documentID,debitNoteCode,DATE_FORMAT(debitNoteDate,\'' . $convertFormat . '\') AS debitNoteDate,comments,srp_erp_suppliermaster.supplierName as suppliername,confirmedYN,approvedYN,srp_erp_debitnotemaster.createdUserID as createdUser,transactionCurrency,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value,det.transactionAmount as detTransactionAmount,isDeleted');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,debitNoteMasterAutoID FROM srp_erp_debitnotedetail GROUP BY debitNoteMasterAutoID) det', '(det.debitNoteMasterAutoID = srp_erp_debitnotemaster.debitNoteMasterAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_debitnotemaster');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_debitnotemaster.supplierID');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('dn_detail', '<b>Supplier Name : </b> $2 <br> <b>Debit Note Date : </b> $3  <br><b>Comments : </b> $1 ', 'comments,suppliername,debitNoteDate,transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"DN",debitNoteMasterAutoID)');
        $this->datatables->add_column('approve', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"DN",debitNoteMasterAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_Debit_note_action(debitNoteMasterAutoID,confirmedYN,approvedYN,createdUser,isDeleted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_supplier_invoices()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( bookingDate >= '" . $datefromconvert . " 00:00:00' AND bookingDate <= '" . $datetoconvert . " 23:59:00')";
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
            $searches = " AND ((bookingInvCode Like '%$search%' ESCAPE '!') OR (invoiceType Like '%$sSearch%' ESCAPE '!') OR (transactionCurrency Like '%$sSearch%') OR (det.transactionAmount Like '%$sSearch%') OR (comments Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (bookingDate Like '%$sSearch%') OR (invoiceDueDate Like '%$sSearch%'))";
        }


        $where = "srp_erp_paysupplierinvoicemaster.companyID=" . $companyid . $supplier_filter . $date . $status_filter . $searches .  "";
        $this->datatables->select("bookingInvCode,comments,transactionCurrency,srp_erp_paysupplierinvoicemaster.transactionAmount,DATE_FORMAT(bookingDate,'.$convertFormat.') AS bookingDate,DATE_FORMAT(invoiceDueDate,'.$convertFormat.') AS invoiceDueDate,srp_erp_paysupplierinvoicemaster.InvoiceAutoID as InvoiceAutoID,confirmedYN,approvedYN,srp_erp_paysupplierinvoicemaster.createdUserID as createdUser,invoiceType,srp_erp_suppliermaster.supplierName as suppliermastername,transactionCurrencyDecimalPlaces,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(det.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(det.transactionAmount,0)) as total_value_search,isDeleted");
        //$this->datatables->select("bookingInvCode,comments,transactionCurrency,srp_erp_paysupplierinvoicemaster.transactionAmount,DATE_FORMAT(bookingDate,'.$convertFormat.') AS bookingDate,DATE_FORMAT(invoiceDueDate,'.$convertFormat.') AS invoiceDueDate,InvoiceAutoID,confirmedYN,approvedYN,createdUserID,invoiceType,supplierName,transactionCurrencyDecimalPlaces");
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,InvoiceAutoID FROM srp_erp_paysupplierinvoicedetail GROUP BY InvoiceAutoID) det', '(det.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,InvoiceAutoID FROM srp_erp_paysupplierinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paysupplierinvoicemaster.supplierID', 'left');
        $this->datatables->from('srp_erp_paysupplierinvoicemaster');
        $this->datatables->add_column('detail', '<b>Supplier Name : </b> $2 <br> <b>Document Date : </b> $3 &nbsp;&nbsp; | &nbsp;&nbsp;<b>Invoice Due Date : </b> $6 <br><b> Type : </b> $5 <br> <b>Narration : </b> $1 ', 'comments,suppliermastername,bookingDate,transactionCurrency,invoiceType,invoiceDueDate');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        //$this->datatables->add_column('total_value', '<div class="pull-right"><b>$1 : </b> $2 </div>', 'transactionCurrency,supplier_invoice_total_value(InvoiceAutoID,transactionCurrencyDecimalPlaces)');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"BSI",InvoiceAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"BSI",InvoiceAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_supplier_invoice_action(InvoiceAutoID,confirmedYN,approvedYN,createdUser,isDeleted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function load_supplier_invoice_conformation()
    {
        $InvoiceAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('InvoiceAutoID'));
        $data['extra'] = $this->Payable_modal->fetch_supplier_invoice_template_data($InvoiceAutoID);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Payable_modal->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $html = $this->load->view('system/accounts_payable/erp_supplier_invoice_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function load_dn_conformation()
    {
        $debitNoteMasterAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('debitNoteMasterAutoID'));
        $data['extra'] = $this->Payable_modal->fetch_debit_note_template_data($debitNoteMasterAutoID);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Payable_modal->fetch_signaturelevel_debit_note();
        } else {
            $data['signature'] = '';
        }
        $html = $this->load->view('system/accounts_payable/erp_debit_note_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function save_supplier_invoice_header()
    {
        $date_format_policy = date_format_policy();
        $bokngDt = $this->input->post('bookingDate');
        $bookingDate = input_format_date($bokngDt, $date_format_policy);

        $invduedt = $this->input->post('supplierInvoiceDueDate');
        $supplierInvoiceDueDate = input_format_date($invduedt, $date_format_policy);

        $invdt = $this->input->post('invoiceDate');
        $invoiceDate = input_format_date($invdt, $date_format_policy);

        $this->form_validation->set_rules('invoiceType', 'Invoice Type', 'trim|required');
        //$this->form_validation->set_rules('segment', 'Segment', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Currency', 'trim|required');
        $this->form_validation->set_rules('bookingDate', 'Invoice Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('supplierInvoiceDueDate', 'Invoice Due Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('financeyear', 'Financial year', 'trim|required');
        $this->form_validation->set_rules('financeyear_period', 'Financial period', 'trim|required');
        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $financearray = $this->input->post('financeyear_period');
            $financePeriod = fetchFinancePeriod($financearray);
            if ($bookingDate >= $financePeriod['dateFrom'] && $bookingDate <= $financePeriod['dateTo']) {
                if (($invoiceDate) > ($supplierInvoiceDueDate)) {
                    $this->session->set_flashdata('e', ' Invoice Due Date cannot be lesser than invoice Date!');
                    echo json_encode(FALSE);
                } else {
                    echo json_encode($this->Payable_modal->save_supplier_invoice_header());
                }
            } else {
                $this->session->set_flashdata('e', 'Invoice Date not between Financial Period !');
                echo json_encode(FALSE);
            }
        }
    }

    function laad_supplier_invoice_header()
    {
        echo json_encode($this->Payable_modal->laad_supplier_invoice_header());
    }

    function fetch_supplier_invoice()
    {
        $data = $this->Payable_modal->fetch_supplier_invoice();
        $html = $this->load->view('system/accounts_payable/erp_debit_note_detail', $data, true);
        echo $html;
    }

    function save_debit_base_items()
    {
        $projectExist = project_is_exist();
        $this->form_validation->set_rules('gl_code[]', 'GL Code', 'trim|required');
        $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
        $this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        $this->form_validation->set_rules('InvoiceAutoID[]', 'InvoiceAutoID', 'trim|required');
        if ($projectExist == 1) {
            $this->form_validation->set_rules("project[]", 'Project', 'trim|required');
        }
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Payable_modal->save_debit_base_items());
        }
    }

    function save_bsi_tax_detail()
    {
        $this->form_validation->set_rules('text_type', 'Tax Type', 'trim|required');
        $this->form_validation->set_rules('percentage', 'Percentage', 'trim|required');
        $this->form_validation->set_rules('InvoiceAutoID', 'InvoiceAutoID', 'trim|required');
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Payable_modal->save_bsi_tax_detail());
        }
    }

    function fetch_bsi_detail()
    {
        echo json_encode($this->Payable_modal->fetch_bsi_detail());
    }

    function fetch_supplier_inv_currency()
    {
        echo json_encode($this->Payable_modal->fetch_supplier_inv_currency());
    }

    function supplier_invoice_confirmation()
    {
        echo json_encode($this->Payable_modal->supplier_invoice_confirmation());
    }

    function fetch_supplier_invoice_detail()
    {
        $data['master'] = $this->Payable_modal->laad_supplier_invoice_header();
        if ($this->input->post('invoiceType') == 'GRV Base') {
            $data['supplier_grv'] = $this->Payable_modal->fetch_supplier_invoice_grv($data['master']['segmentID'], $data['master']['bookingDate']);
        }
        $data['segment_arr'] = $this->Payable_modal->fetch_segment();
        $data['InvoiceAutoID'] = trim($this->input->post('InvoiceAutoID'));
        $data['invoiceType'] = trim($this->input->post('invoiceType'));
        $data['supplierID'] = trim($this->input->post('supplierID'));
        $data['detail'] = $this->Payable_modal->fetch_supplier_invoice_detail();

        $this->load->view('system/accounts_payable/fetch_supplier_invoice_detail', $data);
    }

    function fetch_detail_header_lock()
    {

        echo json_encode($this->Payable_modal->fetch_supplier_invoice_detail());
    }

    function save_bsi_detail()
    {
        $projectExist = project_is_exist();
        $this->form_validation->set_rules("gl_code", 'GL Code', 'required|trim');
        $this->form_validation->set_rules("segment_gl", 'Segment', 'required|trim');
        $this->form_validation->set_rules("amount", 'Amount', 'trim|required');
        $this->form_validation->set_rules("description", 'Description', 'trim|required');
        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Payable_modal->save_bsi_detail());
        }
    }

    function save_bsi_detail_multiple()
    {
        $projectExist = project_is_exist();
        $gl_codes = $this->input->post('gl_code');
        $segment_gls = $this->input->post('segment_gl');
        $descriptions = $this->input->post('description');

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
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Payable_modal->save_bsi_detail_multiple());
        }
    }

    function delete_bsi_detail()
    {
        echo json_encode($this->Payable_modal->delete_bsi_detail());
    }

    function delete_tax_detail()
    {
        echo json_encode($this->Payable_modal->delete_tax_detail());
    }

    function referback_supplierinvoice()
    {

        $InvoiceAutoID = $this->input->post('InvoiceAutoID');

        $this->db->select('approvedYN,bookingInvCode');
        $this->db->where('InvoiceAutoID', trim($InvoiceAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $approved_inventory_payable_supplierinvoice = $this->db->get()->row_array();
        if (!empty($approved_inventory_payable_supplierinvoice)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_inventory_payable_supplierinvoice['bookingInvCode']));
        } else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($InvoiceAutoID, 'BSI');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }

    }

    function save_grv_base_items()
    {
        echo json_encode($this->Payable_modal->save_grv_base_items());
    }

    function delete_supplier_invoice()
    {
        echo json_encode($this->Payable_modal->delete_supplier_invoice());
    }

    function fetch_supplier_invoice_approval()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN'));
        $this->datatables->select('srp_erp_paysupplierinvoicemaster.InvoiceAutoID as InvoiceAutoID,bookingInvCode,comments,srp_erp_suppliermaster.supplierName as supplierName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,DATE_FORMAT(bookingDate,\'' . $convertFormat . '\') AS bookingDate,transactionCurrencyDecimalPlaces,transactionCurrency,(IFNULL(addondet.transactionAmount,0)+IFNULL(det.transactionAmount,0)) as total_value,(IFNULL(addondet.transactionAmount,0)+IFNULL(det.transactionAmount,0)) as total_value_search');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,InvoiceAutoID FROM srp_erp_paysupplierinvoicedetail GROUP BY InvoiceAutoID) det', '(det.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,InvoiceAutoID FROM srp_erp_paysupplierinvoicetaxdetails  GROUP BY InvoiceAutoID) addondet', '(addondet.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID)', 'left');
        $this->datatables->from('srp_erp_paysupplierinvoicemaster');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paysupplierinvoicemaster.supplierID');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_paysupplierinvoicemaster.InvoiceAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_paysupplierinvoicemaster.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_paysupplierinvoicemaster.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'BSI');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'BSI');
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN')));
        $this->datatables->where('srp_erp_paysupplierinvoicemaster.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('bookingInvCode', '$1', 'approval_change_modal(bookingInvCode,InvoiceAutoID,documentApprovedID,approvalLevelID,approvedYN,BSI,0)');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "BSI", InvoiceAutoID)');
        $this->datatables->add_column('edit', '$1', 'supplier_invoice_action_approval(InvoiceAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
        echo $this->datatables->generate();
    }

    function save_supplier_invoice_approval()
    {
        $system_code = trim($this->input->post('InvoiceAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'BSI', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('InvoiceAutoID');
                $this->db->where('InvoiceAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_paysupplierinvoicemaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('InvoiceAutoID', 'Invoice Auto ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Payable_modal->save_supplier_invoice_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('InvoiceAutoID');
            $this->db->where('InvoiceAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_paysupplierinvoicemaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'BSI', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('InvoiceAutoID', 'Invoice Auto ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Payable_modal->save_supplier_invoice_approval());
                    }
                }
            }
        }
    }


    function save_debitnote_header()
    {
        $date_format_policy = date_format_policy();
        $dDt = $this->input->post('dnDate');
        $dnDate = input_format_date($dDt, $date_format_policy);

        $this->form_validation->set_rules('supplier', 'Supplier', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Supplier Currency', 'trim|required');
        //$this->form_validation->set_rules('exchangerate', 'Exchange Rate', 'trim|required');
        $this->form_validation->set_rules('dnDate', 'Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('financeyear', 'Financial year', 'trim|required');
        $this->form_validation->set_rules('financeyear_period', 'Financial period', 'trim|required');
        /*$this->form_validation->set_rules('referenceno', 'Reference No', 'trim|required');*/
        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $financearray = $this->input->post('financeyear_period');
            $financePeriod = fetchFinancePeriod($financearray);
            if ($dnDate >= $financePeriod['dateFrom'] && $dnDate <= $financePeriod['dateTo']) {
                echo json_encode($this->Payable_modal->save_debitnote_header());
            } else {
                $this->session->set_flashdata('e', 'Date not between Financial period !');
                echo json_encode(FALSE);
            }
        }
    }

    function load_debit_note_header()
    {
        echo json_encode($this->Payable_modal->load_debit_note_header());
    }

    function delete_dn()
    {
        echo json_encode($this->Payable_modal->delete_dn());
    }

    function fetch_dn_detail_table()
    {
        echo json_encode($this->Payable_modal->fetch_dn_detail_table());
    }

    function save_dn_detail()
    {
        $this->form_validation->set_rules('gl_code', 'GL Code', 'trim|required');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('segment_gl', 'Segment', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Payable_modal->save_dn_detail());
        }
    }

    function fetch_dn_detail()
    {
        echo json_encode($this->Payable_modal->fetch_dn_detail());
    }

    function delete_dn_detail()
    {
        echo json_encode($this->Payable_modal->delete_dn_detail());
    }

    function dn_confirmation()
    {
        echo json_encode($this->Payable_modal->dn_confirmation());
    }

    function referback_dn()
    {
        $debitNoteMasterAutoID = $this->input->post('debitNoteMasterAutoID');

        $this->db->select('approvedYN,debitNoteCode');
        $this->db->where('debitNoteMasterAutoID', trim($debitNoteMasterAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_debitnotemaster');
        $approved_inventory_debit_note = $this->db->get()->row_array();
        if (!empty($approved_inventory_debit_note)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_inventory_debit_note['debitNoteCode']));
        } else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($debitNoteMasterAutoID, 'DN');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }

    }

    function fetch_debit_note_approval()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $convertFormat = convert_date_format_sql();
        $approvedYN = trim($this->input->post('approvedYN'));
        $companyID = $this->common_data['company_data']['company_id'];

        $this->datatables->select('srp_erp_debitnotemaster.debitNoteMasterAutoID as debitNoteMasterAutoID,debitNoteCode,comments,supplierID,supplierCode,srp_erp_suppliermaster.supplierName as supplierName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(debitNoteDate,\'' . $convertFormat . '\') AS debitNoteDate,,transactionCurrency,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value,det.transactionAmount as detTransactionAmount');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,debitNoteMasterAutoID FROM srp_erp_debitnotedetail GROUP BY debitNoteMasterAutoID) det', '(det.debitNoteMasterAutoID = srp_erp_debitnotemaster.debitNoteMasterAutoID)', 'left');
        $this->datatables->from('srp_erp_debitnotemaster');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_debitnotemaster.supplierID');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_debitnotemaster.debitNoteMasterAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_debitnotemaster.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_debitnotemaster.currentLevelNo');
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_debitnotemaster.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->where('srp_erp_documentapproved.documentID', 'DN');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'DN');
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN')));
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('debitNoteCode', '$1', 'approval_change_modal(debitNoteCode,debitNoteMasterAutoID,documentApprovedID,approvalLevelID,approvedYN,DN,0)');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"DN",debitNoteMasterAutoID)');
        $this->datatables->add_column('edit', '$1', 'dn_action_approval(debitNoteMasterAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');

        echo $this->datatables->generate();
    }

    function save_dn_approval()
    {
        $system_code = trim($this->input->post('debitNoteMasterAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'DN', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('debitNoteMasterAutoID');
                $this->db->where('debitNoteMasterAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_debitnotemaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('debitNoteMasterAutoID', 'Debit Note ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Payable_modal->save_dn_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('debitNoteMasterAutoID');
            $this->db->where('debitNoteMasterAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_debitnotemaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'DN', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('debitNoteMasterAutoID', 'Debit Note ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Payable_modal->save_dn_approval());
                    }
                }
            }
        }
    }

    function delete_supplierInvoices_attachement()
    {
        echo json_encode($this->Payable_modal->delete_supplierInvoices_attachement());
    }

    function delete_debitNote_attachement()
    {
        echo json_encode($this->Payable_modal->delete_debitNote_attachement());
    }

    function delete_paymentVoucher_attachement()
    {
        echo json_encode($this->Payable_modal->delete_paymentVoucher_attachement());
    }

    function fetch_customer_currency_by_id()
    {
        echo json_encode($this->Payable_modal->fetch_customer_currency_by_id());
    }

    function save_debitNote_detail_GLCode_multiple()
    {
        $projectExist = project_is_exist();
        $gl_codes = $this->input->post('gl_code_array');
        $segment_gls = $this->input->post('segment_gl');
        $descriptions = $this->input->post('description');

        foreach ($gl_codes as $key => $gl_code) {
            $this->form_validation->set_rules("gl_code_array[{$key}]", 'GL Code', 'required|trim');
            $this->form_validation->set_rules("segment_gl[{$key}]", 'Segment', 'required|trim');
            $this->form_validation->set_rules("amount[{$key}]", 'Amount', 'trim|required');
            $this->form_validation->set_rules("description[{$key}]", 'Description', 'trim|required');
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
            echo json_encode($this->Payable_modal->save_debitNote_detail_GLCode_multiple());
        }
    }

    function re_open_supplier_invoice()
    {
        echo json_encode($this->Payable_modal->re_open_supplier_invoice());
    }

    function re_open_dn()
    {
        echo json_encode($this->Payable_modal->re_open_dn());
    }
}