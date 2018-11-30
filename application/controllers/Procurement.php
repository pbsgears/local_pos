<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Procurement extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Procurement_modal');
        $this->load->helpers('procurement');
    }

    function fetch_purchase_order()
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
        $isReceived = $this->input->post('isReceived');
        $segmentID = $this->input->post('segmentID');
        $supplier_filter = '';
        $segment_filter = '';
        $isReceived_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }
        if (!empty($segmentID)) {
            $segmentID = array($this->input->post('segmentID'));
            $whereIN = "( " . join("' , '", $segmentID) . " )";
            $segment_filter = " AND segmentID IN " . $whereIN;
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

        if ($isReceived != 'all') {
            if ($isReceived == 0) {
                $isReceived_filter = " AND (isReceived = 0 AND approvedYN = 5)";
            } else if ($isReceived == 1) {
                $isReceived_filter = " AND (isReceived = 1)";
            }else if ($isReceived == 2) {
                $isReceived_filter = " AND (isReceived = 2 )";
            }else{
                $isReceived_filter = " AND (closedYN = 1)";
            }
        }
        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( purchaseOrderCode Like '%$search%' ESCAPE '!') OR ( purchaseOrderType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%') OR (narration Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (documentDate Like '%$sSearch%') OR (expectedDeliveryDate Like '%$sSearch%') OR (transactionCurrency Like '%$sSearch%')) ";
        }


        $where = "srp_erp_purchaseordermaster.companyID = " . $companyid . $supplier_filter . $segment_filter . $date . $status_filter . $searches. $isReceived_filter. "";
        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_purchaseordermaster.purchaseOrderID as purchaseOrderID,srp_erp_purchaseordermaster.companyCode,purchaseOrderCode,narration,srp_erp_suppliermaster.supplierName as supliermastername,confirmedYN,approvedYN ,DATE_FORMAT(expectedDeliveryDate,'.$convertFormat.') AS expectedDeliveryDate,transactionCurrency,purchaseOrderType ,srp_erp_purchaseordermaster.createdUserID as createdUser,srp_erp_purchaseordermaster.transactionAmount,transactionCurrencyDecimalPlaces,(det.transactionAmount-generalDiscountAmount) as total_value,(det.transactionAmount-generalDiscountAmount) as detTransactionAmount,isDeleted,DATE_FORMAT(documentDate,'$convertFormat') AS documentDate,documentDate AS documentDatepofilter,isReceived,closedYN");
        $this->datatables->join('(SELECT SUM(totalAmount) as transactionAmount,purchaseOrderID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID) det', '(det.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID)', 'left');
        $this->datatables->from('srp_erp_purchaseordermaster');
        $this->datatables->join('srp_erp_suppliermaster','srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster.supplierID','left');
        $this->datatables->add_column('po_detail', '<b>Supplier Name : </b> $2 <br> <b>PO Date : </b> $6 <br> <b>Exp Delivery Date : </b> $3  <b>&nbsp;&nbsp; Type : </b> $5<br><b>Narration : </b> $1', 'narration,supliermastername,expectedDeliveryDate,transactionCurrency,purchaseOrderType,documentDate');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->where($where);
        //$this->datatables->or_where('createdUserID', $this->common_data['current_userID']);
        //$this->datatables->or_where('confirmedYN', 1);
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PO",purchaseOrderID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"PO",purchaseOrderID)');
        $this->datatables->add_column('edit', '$1', 'load_po_action(purchaseOrderID,confirmedYN,approvedYN,createdUser,isDeleted)');
        $this->datatables->add_column('isReceivedlbl', '$1', 'po_Recived(isReceived,closedYN,'.$isReceived.')');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_purchase_order_approval()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $searche = str_replace("\\", "\\\\", $sSearch);
            $searches = " AND (( purchaseOrderCode Like '%$searche%' ESCAPE '!')  OR (narration Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (expectedDeliveryDate Like '%$sSearch%'))";
        }
        $companyID = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_purchaseordermaster.companyID = " . $companyID . $searches . "";
        $approvedYN = trim($this->input->post('approvedYN'));
        $convertFormat = convert_date_format_sql();
        $this->datatables->select('srp_erp_purchaseordermaster.purchaseOrderID as purchaseOrderID,srp_erp_purchaseordermaster.companyCode,purchaseOrderCode,narration,srp_erp_suppliermaster.supplierName as supplierName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,transactionCurrency,transactionCurrencyDecimalPlaces,(det.transactionAmount-generalDiscountAmount) as total_value,(det.transactionAmount-generalDiscountAmount) as detTransactionAmount', false);
        $this->datatables->join('(SELECT SUM(totalAmount) as transactionAmount,purchaseOrderID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID) det', '(det.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID)', 'left');
        $this->datatables->from('srp_erp_purchaseordermaster');
        $this->datatables->join('srp_erp_suppliermaster','srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster.supplierID','left');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_purchaseordermaster.purchaseOrderID AND srp_erp_documentapproved.approvalLevelID = srp_erp_purchaseordermaster.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_purchaseordermaster.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'PO');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'PO');
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', $approvedYN);
        $this->datatables->where($where);
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('purchaseOrderCode', '$1', 'approval_change_modal(purchaseOrderCode,purchaseOrderID,documentApprovedID,approvalLevelID,approvedYN,PO,0)');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"PO",purchaseOrderID)');
        $this->datatables->add_column('edit', '$1', 'po_action_approval(purchaseOrderID,approvalLevelID,approvedYN,documentApprovedID,PO)');
        echo $this->datatables->generate();
    }

    function fetch_umo_data()
    {
        $this->datatables->select("UnitID,UnitShortCode,UnitDes,modifiedUserName");
        $this->datatables->from('srp_erp_unit_of_measure');
        $this->datatables->where('companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('edit', '$1', 'load_uom_action(UnitID,UnitDes,UnitShortCode)');
        echo $this->datatables->generate();
    }

    function save_uom()
    {
        $this->form_validation->set_rules('UnitShortCode', 'Code', 'trim|required');
        $this->form_validation->set_rules('UnitDes', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Procurement_modal->save_uom());
        }
    }

    function save_uom_conversion()
    {
        $this->form_validation->set_rules('masterUnitID', 'Master Unit ID', 'trim|required');
        $this->form_validation->set_rules('subUnitID', 'Sub Unit ID', 'trim|required');
        $this->form_validation->set_rules('conversion', 'Conversion', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Procurement_modal->save_uom_conversion());
        }
    }

    function save_inv_tax_detail()
    {
        $this->form_validation->set_rules('text_type', 'Tax Type', 'trim|required');
        $this->form_validation->set_rules('percentage', 'Percentage', 'trim|required');
        $this->form_validation->set_rules('purchaseOrderID', 'Purchase Order ID', 'trim|required');
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'data' => validation_errors()));
        } else {
            echo json_encode($this->Procurement_modal->save_inv_tax_detail());
        }
    }

    function delete_tax_detail()
    {
        echo json_encode($this->Procurement_modal->delete_tax_detail());
    }

    function change_conversion()
    {
        $this->form_validation->set_rules('masterUnitID', 'Master Unit ID', 'trim|required');
        $this->form_validation->set_rules('subUnitID', 'Sub Unit ID', 'trim|required');
        $this->form_validation->set_rules('conversion', 'Conversion', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Procurement_modal->change_conversion());
        }
    }

    function fetch_convertion_detail_table()
    {
        echo json_encode($this->Procurement_modal->fetch_convertion_detail_table());
    }

    function save_purchase_order_header()
    {
        $projectExist = project_is_exist();
        $this->form_validation->set_rules('supplierPrimaryCode', 'Supplier', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('expectedDeliveryDate', 'Delivery Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('POdate', 'PO Date ', 'trim|required|validate_date');
        $this->form_validation->set_rules('narration', 'Narration', 'trim|required');
        if($projectExist == 1){
            $this->form_validation->set_rules('projectID', 'Project', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $date_format_policy = date_format_policy();
            $format_expectedDeliveryDate = input_format_date($this->input->post('expectedDeliveryDate'), $date_format_policy);
            $format_POdate = input_format_date($this->input->post('POdate'), $date_format_policy);
            if ($format_expectedDeliveryDate >= $format_POdate) {
                echo json_encode($this->Procurement_modal->save_purchase_order_header());
            } else {
                $this->session->set_flashdata('e', 'Expected Delivery Date should be greater than PO Date');
                echo json_encode(FALSE);
            }
        }
    }

    function save_purchase_order_approval()
    {
        $system_code = trim($this->input->post('purchaseOrderID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('po_status'));
        if($status==1){
            $approvedYN=checkApproved($system_code,'PO',$level_id);
            if($approvedYN){
                $this->session->set_flashdata('w', 'Document already approved');
                echo json_encode(FALSE);
            }else{
                $this->db->select('purchaseOrderID');
                $this->db->where('purchaseOrderID', trim($system_code));
                $this->db->where('approvedYN', 2);
                $this->db->from('srp_erp_purchaseordermaster');
                $po_approved = $this->db->get()->row_array();
                if(!empty($po_approved)){
                    $this->session->set_flashdata('w', 'Document already rejected');
                    echo json_encode(FALSE);
                }else{
                    $this->form_validation->set_rules('po_status', 'Purchase Order Status', 'trim|required');
                    if($this->input->post('po_status') ==2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('purchaseOrderID', 'Purchase Order ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Procurement_modal->save_purchase_order_approval());
                    }
                }
            }
        }else if($status==2){
            $this->db->select('purchaseOrderID');
            $this->db->where('purchaseOrderID', trim($system_code));
            $this->db->where('approvedYN', 2);
            $this->db->where('confirmedYN !=', 1);
            $this->db->from('srp_erp_purchaseordermaster');
            $po_approved = $this->db->get()->row_array();
            if(!empty($po_approved)){
                $this->session->set_flashdata('w', 'Document already rejected');
                echo json_encode(FALSE);
            }else{
                $rejectYN=checkApproved($system_code,'PO',$level_id);
                if(!empty($rejectYN)){
                    $this->session->set_flashdata('w', 'Document already approved');
                    echo json_encode(FALSE);
                }else{
                    $this->form_validation->set_rules('po_status', 'Purchase Order Status', 'trim|required');
                    if($this->input->post('po_status') ==2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('purchaseOrderID', 'Purchase Order ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Procurement_modal->save_purchase_order_approval());
                    }
                }
            }
        }

    }


    function save_purchase_order_close()
    {
        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        $this->form_validation->set_rules('purchaseOrderID', 'Purchase Order ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Procurement_modal->save_purchase_order_close());
        }
    }

    function save_purchase_order_detail()
    {

        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item ID', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Unit Cost', 'trim|required|greater_than[0]');
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
            echo json_encode($this->Procurement_modal->save_purchase_order_detail());
        }
    }

    function update_purchase_order_detail()
    {
        $quantityRequested = trim($this->input->post('quantityRequested'));
        $estimatedAmount = trim($this->input->post('estimatedAmount'));

        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        $this->form_validation->set_rules('UnitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('quantityRequested', 'Quantity Requested', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('estimatedAmount', 'Estimated Amount', 'trim|required|greater_than[0]');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Procurement_modal->update_purchase_order_detail());
        }
    }

    function load_purchase_order_header()
    {
        echo json_encode($this->Procurement_modal->load_purchase_order_header());
    }

    function fetch_supplier_currency()
    {
        echo json_encode($this->Procurement_modal->fetch_supplier_currency());
    }

    function fetch_supplier_currency_by_id()
    {
        echo json_encode($this->Procurement_modal->fetch_supplier_currency_by_id());
    }

    function fetch_customer_currency()
    {
        echo json_encode($this->Procurement_modal->fetch_customer_currency());
    }

    function fetch_itemrecode()
    {
        echo json_encode($this->Procurement_modal->fetch_itemrecode());
    }
    function fetch_itemrecode_po()
    {
        echo json_encode($this->Procurement_modal->fetch_itemrecode_po());
    }

    function fetch_po_detail_table()
    {
        echo json_encode($this->Procurement_modal->fetch_po_detail_table());
    }

    function delete_purchase_order_detail()
    {
        echo json_encode($this->Procurement_modal->delete_purchase_order_detail());
    }

    function delete_purchase_order()
    {
        echo json_encode($this->Procurement_modal->delete_purchase_order());
    }

    function fetch_purchase_order_detail()
    {
        echo json_encode($this->Procurement_modal->fetch_purchase_order_detail());
    }

    function purchase_order_confirmation()
    {
        echo json_encode($this->Procurement_modal->purchase_order_confirmation());
    }

    function load_purchase_order_conformation()
    {
        $purchaseOrderID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('purchaseOrderID'));
        $data['extra'] = $this->Procurement_modal->fetch_template_data($purchaseOrderID);
        $data['approval'] = $this->input->post('approval');
        $printHeaderFooterYN=1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $this->db->select('printHeaderFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'PO');
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();
        if(!empty($result)){
            $printHeaderFooterYN =$result['printHeaderFooterYN'];
            $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        }
        if (!$this->input->post('html')) {
            $data['signature']=$this->Procurement_modal->fetch_signaturelevel();
        } else {
           $data['signature']='';
        }


        $html = $this->load->view('system/procurement/erp_purchase_order_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'],null,$printHeaderFooterYN);
        }
    }

    function load_approvel()
    {
        $this->datatables->select('documentApprovedID,documentSystemCode,approvalLevelID,srp_employeesdetails.Ename1 as docConfirmedByEmpID,documentDate,CONCAT(srp_employeesdetails.Ename1,\' \',srp_employeesdetails.Ename2) as empname', false)
            ->where('srp_erp_documentapproved.documentSystemCode', $this->input->post('porderid'))
            ->where('srp_erp_documentapproved.documentID', "PO")
            ->where('srp_erp_documentapproved.companyCode', $this->common_data['company_data']['company_code'])
            ->from('srp_erp_documentapproved');
        //$this->datatables->join('srp_schoolmaster', 'srp_erp_documentapproved.companyCode = srp_schoolmaster.SchMasterID', 'left');
        $this->datatables->join('srp_employeesdetails', 'srp_erp_documentapproved.docConfirmedByEmpID = srp_employeesdetails.ECode', 'left');
        echo $this->datatables->generate();

    }

    function referback_procurement()
    {
        $purchaseOrderID = $this->input->post('purchaseOrderID');

        $this->load->library('approvals');

        $this->db->select('approvedYN,purchaseOrderCode');
        $this->db->where('purchaseOrderID', trim($purchaseOrderID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_purchaseordermaster');
        $approved_purchase_order = $this->db->get()->row_array();
        if (!empty($approved_purchase_order)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_purchase_order['purchaseOrderCode']));
        }else
        {
            $status = $this->approvals->approve_delete($purchaseOrderID, 'PO');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }



    }

    function delete_purchaseOrder_attachement()
    {
        echo json_encode($this->Procurement_modal->delete_purchaseOrder_attachement());
    }

    function re_open_procurement()
    {
        echo json_encode($this->Procurement_modal->re_open_procurement());
    }

    function load_project_segmentBase()
    {
        $data_arr = '';
        $companyID = $this->common_data['company_data']['company_id'];
        $segment = trim($this->input->post('segment'));
        $type = trim($this->input->post('type'));
        $ex_segment = explode(" | " , $segment);
        $this->db->select('projectID, projectName');
        $this->db->from('srp_erp_projects');
        $this->db->where('companyID', $companyID);
        $this->db->where('segmentID', $ex_segment[0]);
        $result = $this->db->get()->result_array();
        $data_arr = array('' => 'Select Project');
        if (isset($result)) {
            foreach ($result as $row) {
                $data_arr[trim($row['projectID'])] = trim($row['projectName']);
            }
        }
        echo form_dropdown('projectID', $data_arr, '', 'class="form-control select2" id="projectID_'.$type.'"');
    }

    function load_project_segmentBase_multiple()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $segment = trim($this->input->post('segment'));
        $ex_segment = explode(" | " , $segment);
        $this->db->select('projectID, projectName');
        $this->db->from('srp_erp_projects');
        $this->db->where('companyID', $companyID);
        $this->db->where('segmentID', $ex_segment[0]);
        $result = $this->db->get()->result_array();
        $data_arr = array('' => 'Select Project');
        if (isset($result)) {
            foreach ($result as $row) {
                $data_arr[trim($row['projectID'])] = trim($row['projectName']);
            }
        }
        echo form_dropdown('projectID[]', $data_arr, '', 'class="form-control select2" id="projectID"');
    }

    function load_project_segmentBase_multiple_noclass()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $segment = trim($this->input->post('segment'));
        $detailID = trim($this->input->post('detailID'));
        $ex_segment = explode(" | " , $segment);
        $this->db->select('projectID, projectName');
        $this->db->from('srp_erp_projects');
        $this->db->where('companyID', $companyID);
        $this->db->where('segmentID', $ex_segment[0]);
        $result = $this->db->get()->result_array();
        $data_arr = array('' => 'Select Project');
        if (isset($result)) {
            foreach ($result as $row) {
                $data_arr[trim($row['projectID'])] = trim($row['projectName']);
            }
        }
        echo form_dropdown('projectID[]', $data_arr, '', ' id="projectID_'.$detailID.'"');
    }

    function fetch_prq_code(){
        echo json_encode($this->Procurement_modal->fetch_prq_code());
    }

    function fetch_prq_detail_table(){
        echo json_encode($this->Procurement_modal->fetch_prq_detail_table());
    }

    function save_prq_base_items(){
        echo json_encode($this->Procurement_modal->save_prq_base_items());
    }

    function fetch_last_grn_amount(){
        echo json_encode($this->Procurement_modal->fetch_last_grn_amount());
    }

    function loademail()
    {
        echo json_encode($this->Procurement_modal->loademail());
    }
    function send_po_email()
    {
        $this->form_validation->set_rules('email', 'email', 'trim|valid_email');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        }
        else{
            echo json_encode($this->Procurement_modal->send_po_email());
        }

    }

    function save_inv_disc_detail()
    {
        $this->form_validation->set_rules('discpercentage', 'Percentage', 'trim|required');
        $this->form_validation->set_rules('purchaseOrderID', 'Purchase Order ID', 'trim|required');
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'data' => validation_errors()));
        } else {
            echo json_encode($this->Procurement_modal->save_inv_disc_detail());
        }
    }

    function delete_purchase_order_discount()
    {
        echo json_encode($this->Procurement_modal->delete_purchase_order_discount());
    }

    function edit_discount(){
        echo json_encode($this->Procurement_modal->edit_discount());
    }

}