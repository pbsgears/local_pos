<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MaterialReceiptNote extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Material_receipt_note_modal');
        $this->load->helpers('MaterialReceiptNote');
        $this->load->helpers('exceedmatch');
    }

    function fetch_material_receipt()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);


        $companyid = $this->common_data['company_data']['company_id'];
        $location = $this->input->post('location');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $location_filter = '';
        if (!empty($location)) {
            $supplier = array($this->input->post('location'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $location_filter = " AND wareHouseAutoID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( receivedDate >= '" . $datefromconvert . " 00:00:00' AND receivedDate <= '" . $datetoconvert . " 23:59:00')";
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
        $where = "companyID = " . $companyid . $location_filter . $date . $status_filter . "";
        $this->datatables->select("srp_erp_materialreceiptmaster.mrnAutoID as mrnAutoID,companyCode,mrnCode,comment,employeeName,DATE_FORMAT(confirmedDate,'%Y-%m-%d') AS confirmedDate,confirmedYN,approvedYN,createdUserID,wareHouseDescription,DATE_FORMAT(receivedDate,'.$convertFormat.') AS receivedDate,receiptType,det.totalValue as tot_value,isDeleted,companyLocalCurrency,companyLocalCurrencyDecimalPlaces");
        $this->datatables->join('(SELECT SUM(totalValue) as totalValue,mrnAutoID FROM srp_erp_materialreceiptdetails GROUP BY mrnAutoID) det', '(det.mrnAutoID = srp_erp_materialreceiptmaster.mrnAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_materialreceiptmaster');
        $this->datatables->add_column('MRN_detail', '<b>Received By : </b> $2 <br> <b>Warehouse : </b> $1 <br> <b>Received Date : </b> $3 <b>&nbsp;&nbsp; Type : </b> $4', 'wareHouseDescription,employeeName,receivedDate,receiptType');
        /*$this->datatables->add_column('total_value', '<div class="pull-right"><b>Received : </b> $1 </div>', 'tot_value');*/
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(tot_value,companyLocalCurrencyDecimalPlaces),companyLocalCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"MRN",mrnAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"MRN",mrnAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_material_receipt_action(mrnAutoID,confirmedYN,approvedYN,createdUserID,isDeleted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_stock_transfer()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( tranferDate >= '" . $datefromconvert . " 00:00:00' AND tranferDate <= '" . $datetoconvert . " 23:59:00')";
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
        $where = "companyID = " . $companyid . $date . $status_filter . "";
        $this->datatables->select("stockTransferAutoID,confirmedYN,DATE_FORMAT(tranferDate,'$convertFormat') AS tranferDate,approvedYN,createdUserID,receivedYN,stockTransferCode, form_wareHouseCode , form_wareHouseLocation , form_wareHouseDescription,to_wareHouseCode , to_wareHouseLocation,to_wareHouseDescription,isDeleted");
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_stocktransfermaster');
        $this->datatables->add_column('st_detail', '<b>Form : </b> $1 - $2 - $3 | <b> To : </b> $4 - $5 - $6', 'form_wareHouseCode, form_wareHouseLocation, form_wareHouseDescription, to_wareHouseCode ,to_wareHouseLocation ,to_wareHouseDescription');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"ST",stockTransferAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"ST",stockTransferAutoID)');
        $this->datatables->add_column('received', '$1', 'confirm(receivedYN)');
        $this->datatables->add_column('edit', '$1', 'load_stock_transfer_action(stockTransferAutoID,confirmedYN,approvedYN,createdUserID,isDeleted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_stock_adjustment_table()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $location = $this->input->post('location');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $location_filter = '';
        if (!empty($location)) {
            $supplier = array($this->input->post('location'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $location_filter = " AND wareHouseAutoID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( stockAdjustmentDate >= '" . $datefromconvert . " 00:00:00' AND stockAdjustmentDate <= '" . $datetoconvert . " 23:59:00')";
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
        $where = "companyID = " . $companyid . $location_filter . $date . $status_filter . "";
        $this->datatables->select("stockAdjustmentAutoID,confirmedYN,approvedYN,createdUserID,stockAdjustmentCode,comment,DATE_FORMAT(stockAdjustmentDate,'$convertFormat') AS stockAdjustmentDate ,wareHouseCode,wareHouseLocation,wareHouseDescription,isDeleted");
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_stockadjustmentmaster');
        $this->datatables->add_column('st_detail', '$1 - $2 - $3 ', 'wareHouseCode, wareHouseLocation, wareHouseDescription');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"SA",stockAdjustmentAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"SA",stockAdjustmentAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_stock_adjustment_action(stockAdjustmentAutoID,confirmedYN,approvedYN,createdUserID,isDeleted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_stock_return_table()
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
            $date .= " AND ( returnDate >= '" . $datefromconvert . " 00:00:00' AND returnDate <= '" . $datetoconvert . " 23:59:00')";
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
        $this->datatables->select("stockReturnAutoID,confirmedYN,approvedYN,createdUserID,stockReturnCode,comment,DATE_FORMAT(returnDate,'$convertFormat') as returnDate,wareHouseCode,wareHouseLocation,transactionCurrency,wareHouseDescription,supplierName,isDeleted");
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_stockreturnmaster');
        $this->datatables->add_column('sr_detail', '<b>From : </b> $1', 'wareHouseLocation');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"SR",stockReturnAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"SR",stockReturnAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_stock_return_action(stockReturnAutoID,confirmedYN,approvedYN,createdUserID,isDeleted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function save_material_receipt_header()
    {
        $date_format_policy = date_format_policy();
        $isuDt = $this->input->post('receivedDate');
        $receiptType = trim($this->input->post('receiptType'));
        $issueDate = input_format_date($isuDt, $date_format_policy);

        $this->form_validation->set_rules('receiptType', 'Type', 'trim|required');
        /* $this->form_validation->set_rules('employeeID', 'Employee', 'trim|required');*/
        $this->form_validation->set_rules('employeeName', 'Employee', 'trim|required');
        $this->form_validation->set_rules('receivedDate', 'Date Received', 'trim|required|validate_date');
        $this->form_validation->set_rules('financeyear', 'Financial year', 'trim|required');
        $this->form_validation->set_rules('financeyear_period', 'Financial period', 'trim|required');
        $this->form_validation->set_rules('narration', 'Narration', 'trim|required');

        if($receiptType != 'Material Request'){
            $this->form_validation->set_rules('segment', 'Segment', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $financearray = $this->input->post('financeyear_period');
            $financePeriod = fetchFinancePeriod($financearray);
            if ($issueDate >= $financePeriod['dateFrom'] && $issueDate <= $financePeriod['dateTo']) {
                echo json_encode($this->Material_receipt_note_modal->save_material_receipt_header());
            } else {
                $this->session->set_flashdata('e', 'Date Received not between Financial period !');
                echo json_encode(FALSE);
            }
        }
    }

    function save_stock_return_header()
    {
        //$this->form_validation->set_rules('issueType', 'Issue Type', 'trim|required');
        //$this->form_validation->set_rules('segment', 'Segment', 'trim|required');
        $date_format_policy = date_format_policy();
        $rtndt = $this->input->post('returnDate');
        $returnDate = input_format_date($rtndt, $date_format_policy);

        $this->form_validation->set_rules('supplierID', 'Supplier ID', 'trim|required');
        $this->form_validation->set_rules('returnDate', 'Return Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('financeyear', 'Financial year', 'trim|required');
        $this->form_validation->set_rules('financeyear_period', 'Financial period', 'trim|required');
        $this->form_validation->set_rules('narration', 'Narration', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $financearray = $this->input->post('financeyear_period');
            $financePeriod = fetchFinancePeriod($financearray);
            if ($returnDate >= $financePeriod['dateFrom'] && $returnDate <= $financePeriod['dateTo']) {
                echo json_encode($this->Inventory_modal->save_stock_return_header());
            } else {
                $this->session->set_flashdata('e', 'Purchase Return Date not between Financial period !');
                echo json_encode(FALSE);
            }
        }
    }

    function load_stock_return_conformation()
    {
        $stockReturnAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('stockReturnAutoID'));
        $data['extra'] = $this->Inventory_modal->fetch_template_stock_return_data($stockReturnAutoID);
        $data['approval'] = $this->input->post('approval');
        $html = $this->load->view('system/inventory/erp_stock_return_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function load_material_receipt_conformation()
    {
        $mrnAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('mrnAutoID'));
        $data['extra'] = $this->Material_receipt_note_modal->fetch_template_data($mrnAutoID);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
           $data['signature']=$this->Material_receipt_note_modal->fetch_signaturelevel();
        } else {
            $data['signature']='';
        }


        $html = $this->load->view('system/inventory/MaterialReceipt/erp_material_receipt_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function load_stock_transfer_conformation()
    {
        $stockTransferAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('stockTransferAutoID'));
        $data['extra'] = $this->Inventory_modal->fetch_template_stock_transfer($stockTransferAutoID);
        $data['approval'] = $this->input->post('approval');
        $html = $this->load->view('system/inventory/erp_stock_transfer_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function load_stock_adjustment_conformation()
    {
        $stockAdjustmentAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('stockAdjustmentAutoID'));
        $data['extra'] = $this->Inventory_modal->fetch_template_stock_adjustment($stockAdjustmentAutoID);
        $data['approval'] = $this->input->post('approval');
        $html = $this->load->view('system/inventory/erp_stock_adjustment_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function load_material_receipt_header()
    {
        echo json_encode($this->Material_receipt_note_modal->load_material_receipt_header());
    }

    function laad_stock_return_header()
    {
        echo json_encode($this->Inventory_modal->load_stock_return_header());
    }

    function laad_stock_transfer_header()
    {
        echo json_encode($this->Inventory_modal->laad_stock_transfer_header());
    }

    function referback_stock_return()
    {
        $stockReturnAutoID = $this->input->post('stockReturnAutoID');

        $this->load->library('approvals');
        $status = $this->approvals->approve_delete($stockReturnAutoID, 'SR');
        if ($status == 1) {
            echo json_encode(array('s', ' Referred Back Successfully.', $status));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $status));
        }
    }

    function laad_stock_adjustment_header()
    {
        echo json_encode($this->Inventory_modal->laad_stock_adjustment_header());
    }

    function fetch_stockTransfer_detail_table()
    {
        echo json_encode($this->Inventory_modal->fetch_stockTransfer_detail_table());
    }

    function fetch_stock_adjustment_detail()
    {
        echo json_encode($this->Inventory_modal->fetch_stock_adjustment_detail());
    }

    function fetch_item_for_grv()
    {
        echo json_encode($this->Inventory_modal->fetch_item_for_grv());
    }

    function fetch_inv_item()
    {
        echo json_encode($this->Inventory_modal->fetch_inv_item());
    }

    function fetch_inv_item_stock_adjustment()
    {
        echo json_encode($this->Material_receipt_note_modal->fetch_inv_item_stock_adjustment());
    }

    function delete_return_detail()
    {
        echo json_encode($this->Inventory_modal->delete_return_detail());
    }

    function save_stock_transfer_header()
    {
        $date_format_policy = date_format_policy();
        $trfrDt = $this->input->post('tranferDate');
        $tranferDate = input_format_date($trfrDt, $date_format_policy);

        $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
        $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        $this->form_validation->set_rules('tranferDate', 'Tranfer Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('form_location', 'Form location', 'trim|required');
        $this->form_validation->set_rules('to_location', 'To location', 'trim|required');
        $this->form_validation->set_rules('narration', 'Narration', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $financearray = $this->input->post('financeyear_period');
            $financePeriod = fetchFinancePeriod($financearray);
            if ($tranferDate >= $financePeriod['dateFrom'] && $tranferDate <= $financePeriod['dateTo']) {
                if (trim($this->input->post('form_location')) != trim($this->input->post('to_location'))) {
                    echo json_encode($this->Inventory_modal->save_stock_transfer_header());
                } else {
                    $this->session->set_flashdata('e', 'From location and to location cannot be same !');
                    echo json_encode(FALSE);
                }

            } else {
                $this->session->set_flashdata('e', 'Transfer Date not between Financial period !');
                echo json_encode(FALSE);
            }
        }
    }

    function save_stock_adjustment_header()
    {
        $date_format_policy = date_format_policy();
        $stkAdntDte = $this->input->post('stockAdjustmentDate');
        $stockAdjustmentDate = input_format_date($stkAdntDte, $date_format_policy);

        $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
        $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        $this->form_validation->set_rules('stockAdjustmentDate', 'Adjustment Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('location', 'location', 'trim|required');
        $this->form_validation->set_rules('narration', 'Narration', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $financearray = $this->input->post('financeyear_period');
            $financePeriod = fetchFinancePeriod($financearray);
            if ($stockAdjustmentDate >= $financePeriod['dateFrom'] && $stockAdjustmentDate <= $financePeriod['dateTo']) {
                echo json_encode($this->Inventory_modal->save_stock_adjustment_header());
            } else {
                $this->session->set_flashdata('e', 'Adjustment Date not between Financial period !');
                echo json_encode(FALSE);
            }
        }
    }

    function save_stock_transfer_detail()
    {
        $projectExist = project_is_exist();
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item ID', 'trim|required');
        $this->form_validation->set_rules('unitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('transfer_QTY', 'Transfer Quantity', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('a_segment', 'Segment', 'trim|required');
        $this->form_validation->set_rules('currentWareHouseStockQty', 'Current Stock', 'trim|required|greater_than[0]');
        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Inventory_modal->save_stock_transfer_detail());
        }
    }

    function save_stock_transfer_detail_multiple()
    {
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $transfer_QTY = $this->input->post('transfer_QTY');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item ', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("a_segment[{$key}]", 'Segment', 'trim|required');
            $this->form_validation->set_rules("transfer_QTY[{$key}]", 'Transfer Quantity', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("currentWareHouseStockQty[{$key}]", 'Current Stock', 'trim|required|greater_than[0]');
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
            echo json_encode($this->Inventory_modal->save_stock_transfer_detail_multiple());
        }

    }

    function save_stock_return_detail()
    {
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemSystemCode', 'item System Code', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item ID', 'trim|required');
        $this->form_validation->set_rules('itemDescription', 'Item', 'trim|required');
        $this->form_validation->set_rules('unitOfMeasure', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('return_Qty', 'Return Quantity', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Inventory_modal->save_stock_return_detail());
        }
    }

    function save_return_item_detail()
    {
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemSystemCode', 'item System Code', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item ID', 'trim|required');
        $this->form_validation->set_rules('itemDescription', 'Item', 'trim|required');
        $this->form_validation->set_rules('UnitOfMeasure', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('return_Qty', 'Return Quantity', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Inventory_modal->save_return_item_detail());
        }
    }

    function save_stock_adjustment_detail()
    {
        $projectExist = project_is_exist();
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item ID', 'trim|required');
        $this->form_validation->set_rules('unitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('currentWareHouseStock', 'Current Stock', 'trim|required');
        $this->form_validation->set_rules('currentWac', 'Current Wac', 'trim|required');
        $this->form_validation->set_rules('adjustment_Stock', 'Adjustment Stock', 'trim|required');
        $this->form_validation->set_rules('adjustment_wac', 'Adjustment Wac', 'trim|required');
        $this->form_validation->set_rules('a_segment', 'Segment ', 'trim|required');
        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Inventory_modal->save_stock_adjustment_detail());
        }
    }

    function save_stock_adjustment_detail_multiple()
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
            $this->form_validation->set_rules("currentWareHouseStock[{$key}]", 'Current Stock', 'trim|required');
            $this->form_validation->set_rules("currentWac[{$key}]", 'Current Wac', 'trim|required');
            $this->form_validation->set_rules("adjustment_Stock[{$key}]", 'Adjustment Stock', 'trim|required');
            $this->form_validation->set_rules("adjustment_wac[{$key}]", 'Adjustment Wac', 'trim|required');
            $this->form_validation->set_rules("a_segment[{$key}]", 'Segment', 'trim|required');
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
            echo json_encode($this->Inventory_modal->save_stock_adjustment_detail_multiple());
        }
    }

    function save_material_detail()
    {
        $projectExist = project_is_exist();
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item ID', 'trim|required');
        $this->form_validation->set_rules('unitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('quantityRequested', 'Issued Qty', 'trim|required|greater_than[0]');
        if(!$this->input->post('materialReceiptNoteType')== 'Material Request'){
            $this->form_validation->set_rules('unitCost', 'Unit Cost', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules('a_segment', 'Segment', 'trim|required');
        }

        //$this->form_validation->set_rules('currentWareHouseStockQty', 'Current Stock', 'trim|required|greater_than[0]');
        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Material_receipt_note_modal->save_material_detail());
        }
    }

    function save_material_detail_multiple()
    {
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $unitCost = $this->input->post('unitCost');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item ', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("a_segment[{$key}]", 'Segment', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Received Qty', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("unitCost[{$key}]", 'Unit Cost', 'trim|required');
            //$this->form_validation->set_rules("currentWareHouseStockQty[{$key}]", 'Current Stock', 'trim|required');
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
            echo json_encode($this->Material_receipt_note_modal->save_material_detail_multiple());
        }
    }

    function save_grv_base_items()
    {
        $this->form_validation->set_rules('grvDetailsID[]', 'GRV', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Inventory_modal->save_grv_base_items());
        }
    }

    function fetch_stock_return_detail()
    {
        $data['master'] = $this->Inventory_modal->load_stock_return_header();
        $data['stockReturnAutoID'] = trim($this->input->post('stockReturnAutoID'));
        $data['supplierID'] = $data['master']['supplierID'];
        $this->load->view('system/inventory/stock_return_detail', $data);
    }

    function fetch_material_receipt_detail()
    {
        echo json_encode($this->Material_receipt_note_modal->fetch_material_receipt_detail());
    }

    function fetch_return_direct_details()
    {
        echo json_encode($this->Inventory_modal->fetch_return_direct_details());
    }

    function delete_material_item()
    {
        echo json_encode($this->Material_receipt_note_modal->delete_material_item());
    }

    function delete_adjustment_item()
    {
        echo json_encode($this->Inventory_modal->delete_adjustment_item());
    }

    function delete_material_receipt_header()
    {
        echo json_encode($this->Material_receipt_note_modal->delete_material_receipt_header());
    }

    function load_material_receipt_detail()
    {
        echo json_encode($this->Material_receipt_note_modal->load_material_receipt_detail());
    }

    function load_stock_transfer_item_detail()
    {
        echo json_encode($this->Inventory_modal->load_stock_transfer_item_detail());
    }

    function material_item_confirmation()
    {
        echo json_encode($this->Material_receipt_note_modal->material_item_confirmation());
    }

    function stock_transfer_confirmation()
    {
        echo json_encode($this->Inventory_modal->stock_transfer_confirmation());
    }

    function delete_stock_adjustment()
    {
        echo json_encode($this->Inventory_modal->delete_stock_adjustment());
    }

    function stock_return_confirmation()
    {
        echo json_encode($this->Inventory_modal->stock_return_confirmation());
    }

    function stock_adjustment_confirmation()
    {
        echo json_encode($this->Inventory_modal->stock_adjustment_confirmation());
    }

    function load_adjustment_item_detail()
    {
        echo json_encode($this->Inventory_modal->load_adjustment_item_detail());
    }

    function fetch_warehouse_item()
    {
        echo json_encode($this->Material_receipt_note_modal->fetch_warehouse_item());
    }

    function fetch_st_warehouse_item()
    {
        echo json_encode($this->Inventory_modal->fetch_st_warehouse_item());
    }

    function fetch_warehouse_item_adjustment()
    {
        echo json_encode($this->Inventory_modal->fetch_warehouse_item_adjustment());
    }

    function referback_materialissue()
    {
        $itemIssueAutoID = $this->input->post('mrnAutoID');

        $this->db->select('approvedYN,mrnCode');
        $this->db->where('mrnAutoID', trim($itemIssueAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_materialreceiptmaster');
        $approved_inventory_grv_mrn = $this->db->get()->row_array();
        if (!empty($approved_inventory_grv_mrn)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_inventory_grv_mrn['mrnCode']));
        }else
        {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($itemIssueAutoID, 'MRN');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }


    }

    function delete_purchase_return()
    {
        echo json_encode($this->Inventory_modal->delete_purchase_return());
    }

    function fetch_material_issue_approval()
    {

        /*
         * rejected = 1
         * not rejected = 0
         * */
        $convertFormat = convert_date_format_sql();
        $approvedYN = trim($this->input->post('approvedYN'));
        $companyID = $this->common_data['company_data']['company_id'];
        $this->datatables->select('srp_erp_materialreceiptmaster.mrnAutoID as mrnAutoID,mrnCode,wareHouseCode,wareHouseLocation,wareHouseDescription,srp_erp_materialreceiptmaster.employeeName as employeeName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,DATE_FORMAT(receivedDate,\'' . $convertFormat . '\') AS receivedDate,det.qtyReceived as tot_value', false);
        $this->datatables->join('(SELECT SUM(qtyReceived) as qtyReceived,mrnAutoID FROM srp_erp_materialreceiptdetails GROUP BY mrnAutoID) det', '(det.mrnAutoID = srp_erp_materialreceiptmaster.mrnAutoID)', 'left');
        $this->datatables->from('srp_erp_materialreceiptmaster');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_materialreceiptmaster.mrnAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_materialreceiptmaster.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_materialreceiptmaster.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'MRN');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'MRN');
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_materialreceiptmaster.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN')));
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>Received : </b> $1 </div>', 'tot_value');
        $this->datatables->add_column('mrnCode', '$1', 'approval_change_modal(mrnCode,mrnAutoID,documentApprovedID,approvalLevelID,approvedYN,MRN,0)');
        $this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "MRN", mrnAutoID)');
        $this->datatables->add_column('edit', '$1', 'material_issue_action_approval(mrnAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');

        echo $this->datatables->generate();
    }

    function fetch_stock_adjustment_approval()
    {
        /*
        * rejected = 1
        * not rejected = 0
        * */
        $convertFormat = convert_date_format_sql();
        $approvedYN = trim($this->input->post('approvedYN'));
        $companyID = $this->common_data['company_data']['company_id'];
        $this->datatables->select('stockAdjustmentAutoID,stockAdjustmentCode,wareHouseCode,wareHouseLocation,wareHouseDescription,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,confirmedByName,DATE_FORMAT(stockAdjustmentDate,\'' . $convertFormat . '\') AS stockAdjustmentDate', false);
        $this->datatables->from('srp_erp_stockadjustmentmaster');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_stockadjustmentmaster.stockAdjustmentAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_stockadjustmentmaster.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_stockadjustmentmaster.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'SA');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'SA');
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN')));
        $this->datatables->where('srp_erp_stockadjustmentmaster.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->add_column('stockAdjustmentCode', '$1', 'approval_change_modal(stockAdjustmentCode,stockAdjustmentAutoID,documentApprovedID,approvalLevelID,approvedYN,SA,0)');
        $this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "SA", stockAdjustmentAutoID)');
        $this->datatables->add_column('edit', '$1', 'stock_adjustment_action_approval(stockAdjustmentAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');

        echo $this->datatables->generate();
    }

    function fetch_stock_return_approval()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $convertFormat = convert_date_format_sql();
        $approvedYN = trim($this->input->post('approvedYN'));
        $companyID = $this->common_data['company_data']['company_id'];
        $this->datatables->select('stockReturnAutoID,stockReturnCode,wareHouseCode,wareHouseLocation,wareHouseDescription,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,confirmedByName,DATE_FORMAT(returnDate,\'' . $convertFormat . '\') as returnDate', false);
        $this->datatables->from('srp_erp_stockreturnmaster');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_stockreturnmaster.stockReturnAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_stockreturnmaster.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_stockreturnmaster.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'SR');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'SR');
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN')));
        $this->datatables->where('srp_erp_stockreturnmaster.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->add_column('stockReturnCode', '$1', 'approval_change_modal(stockReturnCode,stockReturnAutoID,documentApprovedID,approvalLevelID,approvedYN,SR,0)');
        $this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "SR",stockReturnAutoID)');
        $this->datatables->add_column('edit', '$1', 'stock_return_action_approval(stockReturnAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
        echo $this->datatables->generate();
    }

    function fetch_stock_transfer_approval()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN'));
        $this->datatables->select('stockTransferAutoID,stockTransferCode,form_wareHouseCode ,form_wareHouseLocation , form_wareHouseDescription ,  to_wareHouseCode , to_wareHouseLocation,to_wareHouseDescription,confirmedYN ,srp_erp_documentapproved.approvedYN as approvedYN, approvalLevelID,documentApprovedID,confirmedByName,DATE_FORMAT(tranferDate,\'' . $convertFormat . '\') AS tranferDate', false);
        $this->datatables->from('srp_erp_stocktransfermaster');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_stocktransfermaster.stockTransferAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_stocktransfermaster.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_stocktransfermaster.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'ST');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'ST');
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN')));
        $this->datatables->where('srp_erp_stocktransfermaster.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->add_column('stockTransferCode', '$1', 'approval_change_modal(stockTransferCode,stockTransferAutoID,documentApprovedID,approvalLevelID,approvedYN,ST,0)');
        $this->datatables->add_column('detail', '<b>Form : </b> $1 - $2 - $3 | <b> To : </b> $4 - $5 - $6', 'form_wareHouseCode, form_wareHouseLocation, form_wareHouseDescription, to_wareHouseCode ,to_wareHouseLocation ,to_wareHouseDescription');
        //$this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "ST", stockTransferAutoID)');
        $this->datatables->add_column('edit', '$1', 'stock_transfer_action_approval(stockTransferAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
        echo $this->datatables->generate();
    }

    function save_material_receipt_approval()
    {
        $system_code = trim($this->input->post('mrnAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'MRN', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('mrnAutoID');
                $this->db->where('mrnAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_materialreceiptmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', ' Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('mrnAutoID', 'Material Receipt Note ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Material_receipt_note_modal->save_material_receipt_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('mrnAutoID');
            $this->db->where('mrnAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_materialreceiptmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'MRN', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('mrnAutoID', 'Material Receipt Note ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Material_receipt_note_modal->save_material_receipt_approval());
                    }
                }
            }
        }
    }

    function save_stock_adjustment_approval()
    {
        $system_code = trim($this->input->post('stockAdjustmentAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'SA', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('stockAdjustmentAutoID');
                $this->db->where('stockAdjustmentAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_stockadjustmentmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('stockAdjustmentAutoID', 'Stock Adjustment ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Inventory_modal->save_stock_adjustment_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('stockAdjustmentAutoID');
            $this->db->where('stockAdjustmentAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_stockadjustmentmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'SA', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('stockAdjustmentAutoID', 'Stock Adjustment ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Inventory_modal->save_stock_adjustment_approval());
                    }
                }
            }
        }
    }

    function save_stock_transfer_approval()
    {
        $system_code = trim($this->input->post('stockTransferAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'ST', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('stockTransferAutoID');
                $this->db->where('stockTransferAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_stocktransfermaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('stockTransferAutoID', 'Stock Transfer ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Inventory_modal->save_stock_transfer_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('stockTransferAutoID');
            $this->db->where('stockTransferAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_stocktransfermaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'ST', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('stockTransferAutoID', 'Stock Transfer ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Inventory_modal->save_stock_transfer_approval());
                    }
                }
            }
        }
    }


    function save_stock_return_approval()
    {
        $system_code = trim($this->input->post('stockReturnAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'SR', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('stockReturnAutoID');
                $this->db->where('stockReturnAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_stockreturnmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('stockReturnAutoID', 'Purchase Return ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Inventory_modal->save_stock_return_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('stockReturnAutoID');
            $this->db->where('stockReturnAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_stockreturnmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'SR', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('stockReturnAutoID', 'Purchase Return ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Inventory_modal->save_stock_return_approval());
                    }
                }
            }
        }
    }

    function delete_purchaseReturn_attachement()
    {
        echo json_encode($this->Inventory_modal->delete_purchaseReturn_attachement());
    }

    function delete_material_Issue_attachement()
    {
        echo json_encode($this->Inventory_modal->delete_material_Issue_attachement());
    }

    function delete_stockTransfer_attachement()
    {
        echo json_encode($this->Inventory_modal->delete_stockTransfer_attachement());
    }

    function delete_stockAdjustment_attachement()
    {
        echo json_encode($this->Inventory_modal->delete_stockAdjustment_attachement());
    }

    function referback_stock_transfer()
    {
        $stockTransferAutoID = $this->input->post('stockTransferAutoID');

        $this->load->library('approvals');
        $status = $this->approvals->approve_delete($stockTransferAutoID, 'ST');
        if ($status == 1) {
            echo json_encode(array('s', ' Referred Back Successfully.', $status));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $status));
        }
    }

    function referback_stock_adjustment()
    {
        $stockAdjustmentAutoID = $this->input->post('stockAdjustmentAutoID');

        $this->load->library('approvals');
        $status = $this->approvals->approve_delete($stockAdjustmentAutoID, 'SA');
        if ($status == 1) {
            echo json_encode(array('s', ' Referred Back Successfully.', $status));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $status));
        }
    }

    function delete_stockTransfer_details()
    {
        echo json_encode($this->Inventory_modal->delete_stockTransfer_details());
    }

    function delete_stocktransfer_master()
    {
        echo json_encode($this->Inventory_modal->delete_stocktransfer_master());
    }

    /** Created by shafri on 16-05-2017 */
    function fetch_sales_return_table()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerPrimaryCode');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $customer_filter = '';
        if (!empty($customer)) {
            $customer = array($this->input->post('customerPrimaryCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( returnDate >= '" . $datefromconvert . " 00:00:00' AND returnDate <= '" . $datetoconvert . " 23:59:00')";
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
        $where = "companyID = " . $companyid . $customer_filter . $date . $status_filter . "";
        $this->datatables->select("salesReturnAutoID,confirmedYN,approvedYN,createdUserID,salesReturnCode,comment,DATE_FORMAT(returnDate,'$convertFormat') as returnDate,wareHouseCode,wareHouseLocation,transactionCurrency,wareHouseDescription,customerName,isDeleted");
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_salesreturnmaster');
        $this->datatables->add_column('sr_detail', '<b>From : </b> $1', 'wareHouseLocation');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"SLR",salesReturnAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"SLR",salesReturnAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_sales_return_action(salesReturnAutoID,confirmedYN,approvedYN,createdUserID,isDeleted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function createNewSalesReturn()
    {
        $data['id'] = '';
        $this->load->view('system/inventory/erp_sales_return', $data);
    }

    function save_sales_return_header()
    {
        $date_format_policy = date_format_policy();
        $rtndt = $this->input->post('returnDate');
        $returnDate = input_format_date($rtndt, $date_format_policy);

        $this->form_validation->set_rules('customerID', 'Supplier ID', 'trim|required');
        $this->form_validation->set_rules('returnDate', 'Return Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('financeyear', 'Financial year', 'trim|required');
        $this->form_validation->set_rules('financeyear_period', 'Financial period', 'trim|required');
        $this->form_validation->set_rules('narration', 'Narration', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $financearray = $this->input->post('financeyear_period');
            $financePeriod = fetchFinancePeriod($financearray);
            if ($returnDate >= $financePeriod['dateFrom'] && $returnDate <= $financePeriod['dateTo']) {
                echo json_encode($this->Inventory_modal->save_sales_return_header());
            } else {
                $this->session->set_flashdata('e', 'Purchase Return Date not between Financial period !');
                echo json_encode(FALSE);
            }
        }
    }

    function load_sales_return_conformation()
    {
        $salesReturnAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('salesReturnAutoID'));
        $data['extra'] = $this->Inventory_modal->fetch_template_sales_return_data($salesReturnAutoID);
        $data['approval'] = $this->input->post('approval');
        $html = $this->load->view('system/inventory/erp_sales_return_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function load_sales_return_header()
    {
        echo json_encode($this->Inventory_modal->load_sales_return_header());
    }

    function fetch_sales_return_detail()
    {
        $data['master'] = $this->Inventory_modal->load_sales_return_header();
        $data['stockReturnAutoID'] = trim($this->input->post('salesReturnAutoID'));
        $data['customerID'] = $data['master']['customerID'];
        $this->load->view('system/inventory/sales_return_detail', $data);
    }

    function fetch_sales_return_details()
    {
        echo json_encode($this->Inventory_modal->fetch_sales_return_details());
    }

    function fetch_item_for_sales_return()
    {
        echo json_encode($this->Inventory_modal->fetch_item_for_sales_return());
    }

    function save_sales_return_detail_items()
    {
        $this->form_validation->set_rules('invoiceDetailsAutoID[]', 'CINV', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Inventory_modal->save_sales_return_detail_items());
        }
    }

    function delete_sales_return_detail()
    {
        echo json_encode($this->Inventory_modal->delete_sales_return_detail());
    }

    function sales_return_confirmation()
    {
        echo json_encode($this->Inventory_modal->sales_return_confirmation());
    }

    function delete_sales_return()
    {
        echo json_encode($this->Inventory_modal->delete_sales_return());
    }

    function fetch_sales_return_approval()
    {
        /*
        * rejected = 1
        * not rejected = 0
        * */

        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN'));
        $this->datatables->select('masterTbl.salesReturnAutoID as masterAutoID, salesReturnCode as documentCode, `comment` as narration, customerName, confirmedYN, srp_erp_documentapproved.approvedYN as approvedYN, documentApprovedID, approvalLevelID, DATE_FORMAT(returnDate,\'' . $convertFormat . '\') AS documentDate, det.totalValue as total_value,transactionCurrencyDecimalPlaces, transactionCurrency', false);
        $this->datatables->join('(SELECT SUM(totalValue) as totalValue,salesReturnAutoID FROM srp_erp_salesreturndetails detailTbl GROUP BY salesReturnAutoID) det', '(det.salesReturnAutoID = masterTbl.salesReturnAutoID)', 'left');
        $this->datatables->from('srp_erp_salesreturnmaster masterTbl');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = masterTbl.salesReturnAutoID AND srp_erp_documentapproved.approvalLevelID = masterTbl.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = masterTbl.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'SLR');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'SLR');
        $this->datatables->where('srp_erp_documentapproved.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN')));
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('invoiceCode', '$1', 'approval_change_modal(invoiceCode,salesReturnAutoID,documentApprovedID,approvalLevelID,approvedYN,SLR,0)');
        $this->datatables->add_column('confirmed', "<div style='text-align: center'>Level $1</div>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"SLR",masterAutoID)');
        $this->datatables->add_column('edit', '$1', 'inv_action_approval(masterAutoID,approvalLevelID,approvedYN,documentApprovedID,SLR)');
        echo $this->datatables->generate();
    }

    function save_sales_return_approval()
    {
        $system_code = trim($this->input->post('salesReturnAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'SLR', $level_id);
            if ($approvedYN) {
                echo json_encode(array('error' => 1, 'message' => 'Document already approved'));
            } else {
                $this->db->select('salesReturnAutoID');
                $this->db->where('salesReturnAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_salesreturnmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    echo json_encode(array('error' => 1, 'message' => 'Document already rejected'));
                } else {
                    $this->form_validation->set_rules('status', 'Approval Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('salesReturnAutoID', 'Sales Return ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        //$this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(array('error' => 1, 'message' => validation_errors()));

                    } else {
                        echo json_encode($this->Inventory_modal->save_sales_return_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('salesReturnAutoID');
            $this->db->where('salesReturnAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_salesreturnmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                echo json_encode(array('error' => 1, 'message' => 'Document already rejected'));
            } else {
                $rejectYN = checkApproved($system_code, 'SLR', $level_id);
                if (!empty($rejectYN)) {
                    echo json_encode(array('error' => 1, 'message' => 'Document already approved'));
                } else {
                    $this->form_validation->set_rules('status', 'Approval Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('salesReturnAutoID', 'Sales Return ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        //$this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(array('error' => 1, 'message' => validation_errors()));

                    } else {
                        echo json_encode($this->Inventory_modal->save_sales_return_approval());
                    }
                }
            }
        }
    }

    function referback_sales_return()
    {
        $salesReturnAutoID = $this->input->post('salesReturnAutoID');

        $this->load->library('approvals');
        $status = $this->approvals->approve_delete($salesReturnAutoID, 'SLR');
        if ($status == 1) {
            echo json_encode(array('s', ' Referred Back Successfully.', $status));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $status));
        }
    }

    function re_open_inventory()
    {
        echo json_encode($this->Inventory_modal->re_open_inventory());
    }

    function re_open_stock_return()
    {
        echo json_encode($this->Inventory_modal->re_open_stock_return());
    }

    function re_open_material_receipt()
    {
        echo json_encode($this->Material_receipt_note_modal->re_open_material_receipt());
    }

    function re_open_stock_transfer()
    {
        echo json_encode($this->Inventory_modal->re_open_stock_transfer());
    }

    function re_open_stock_adjestment()
    {
        echo json_encode($this->Inventory_modal->re_open_stock_adjestment());
    }

    function stockadjustmentAccountUpdate()
    {
        $this->form_validation->set_rules('PLGLAutoID', 'Cost GL Account', 'trim|required');
        if ($this->input->post('BLGLAutoID')) {
            $this->form_validation->set_rules('BLGLAutoID', 'Asset GL Account', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Inventory_modal->stockadjustmentAccountUpdate());
        }


    }

    function stockAdjustment_load_gldropdown()
    {
        $companyID = current_companyID();
        $data['PLGLAutoID'] = $this->input->post('PLGLAutoID');
        $data['BLGLAutoID'] = $this->input->post('BLGLAutoID');
        $master = $this->db->query("select masterAccountYN from srp_erp_chartofaccounts WHERE GLAutoID={$data['BLGLAutoID']}")->row_array();
        $data['masterAccountYN'] = $master['masterAccountYN'];
        $costGL = $this->db->query("SELECT systemAccountCode, GLAutoID, GLDescription FROM srp_erp_chartofaccounts WHERE controllAccountYN=0 and isBank=0 and accountCategoryTypeID!=4 AND isActive = 1 AND masterAccountYN = 0 AND companyID = $companyID")->result_array();

        $data_arr = array('' => 'Select GL Code');
        if (isset($costGL)) {
            foreach ($costGL as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLDescription']);
            }

        }
        $data['costGL'] = $data_arr;

        echo $html = $this->load->view('system/inventory/stock_adjustment-account-change', $data, TRUE);
    }

    function materialAccountUpdate()
    {
        $this->form_validation->set_rules('PLGLAutoID', 'Cost GL Account', 'trim|required');
        if ($this->input->post('BLGLAutoID')) {
            $this->form_validation->set_rules('BLGLAutoID', 'Asset GL Account', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Inventory_modal->materialAccountUpdate());
        }
    }

    function fetch_material_issue_code()
    {
        echo json_encode($this->Material_receipt_note_modal->fetch_material_issue_code());
    }

    function fetch_material_issue_detail_table()
    {
        echo json_encode($this->Material_receipt_note_modal->fetch_material_issue_detail_table());
    }

    function save_material_issue_note_base_items()
    {
        echo json_encode($this->Material_receipt_note_modal->save_material_issue_note_base_items());
    }


}