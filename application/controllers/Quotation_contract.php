<?php

class Quotation_contract extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Quotation_contract_model');
    }

    function fetch_Quotation_contract()
    {


        $sSearch=$this->input->post('sSearch');
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
        $contractType = $this->input->post('contractType');
        $customer_filter = '';

        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $contractType_filter = '';
        if (!empty($contractType)) {
            $contractType = explode(',', $this->input->post('contractType'));
            $whereIN = "( '" . join("' , '", $contractType) . "' )";
            $contractType_filter = " AND contractType IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' AND contractDate <= '" . $datetoconvert . " 23:59:00')";
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
        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( contractCode Like '%$search%' ESCAPE '!') OR ( contractType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%') OR (contractNarration Like '%$sSearch%') OR (srp_erp_customermaster.customerName Like '%$sSearch%') OR (contractDate Like '%$sSearch%') OR (contractExpDate Like '%$sSearch%')) ";
        }
        $where = "srp_erp_contractmaster.companyID = " . $companyid . $customer_filter . $date . $contractType_filter . $status_filter . $searches."";
        $this->datatables->select('srp_erp_contractmaster.contractAutoID as contractAutoID,contractCode,contractNarration,srp_erp_customermaster.customerName as customerMasterName,,documentID, transactionCurrencyDecimalPlaces ,transactionCurrency,confirmedYN,approvedYN, contractType, srp_erp_contractmaster.createdUserID as createdUser,DATE_FORMAT(contractDate,\'' . $convertFormat . '\') AS contractDate ,DATE_FORMAT(contractExpDate,\'' . $convertFormat . '\') AS contractExpDate,det.transactionAmount as total_value,det.transactionAmount as detTransactionAmount,srp_erp_contractmaster.isDeleted as isDeleted');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,contractAutoID FROM srp_erp_contractdetails GROUP BY contractAutoID) det', '(det.contractAutoID = srp_erp_contractmaster.contractAutoID)', 'left');
        $this->datatables->where($where);

        $this->datatables->from('srp_erp_contractmaster');
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID', 'left');
        $this->datatables->add_column('detail', '<b>Customer Name : </b> $2 <br> <b> Type  : </b> $5<br> <b>Document Date : </b> $3 <b style="text-indent: 1%;">&nbsp | &nbsp Document Exp Date : </b> $4 <br> <b>Comments : </b> $1  ', 'contractNarration,customerMasterName,contractDate,contractExpDate,contractType');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,documentID,contractAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,documentID,contractAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_contract_action(contractAutoID,confirmedYN,approvedYN,createdUser,documentID,confirmedYN,isDeleted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function save_quotation_contract_header()
    {
        $this->form_validation->set_rules('contractType', 'Contract Type', 'trim|required');
        $this->form_validation->set_rules('contractDate', 'Contract Date', 'trim|required');
        $this->form_validation->set_rules('contractExpDate', 'Contract Exp Date', 'trim|required');
        $this->form_validation->set_rules('referenceNo', 'Reference No', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('customerID', 'Customer', 'trim|required');
        $this->form_validation->set_rules('contractNarration', 'Narration', 'trim|required');
        $financearray = explode("|", $this->input->post('financeyear_period'));
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Quotation_contract_model->save_quotation_contract_header());
        }
    }

    function fetch_quotation_contract_approval()
    {
        /*
        * rejected = 1
        * not rejected = 0
        * */
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN'));
        $this->datatables->select('contractType,srp_erp_contractmaster.documentID as document,srp_erp_contractmaster.contractAutoID as contractAutoID,srp_erp_contractmaster.companyCode,contractCode,contractNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(contractDate,\'' . $convertFormat . '\') AS contractDate,transactionCurrencyDecimalPlaces ,transactionCurrency,det.transactionAmount as total_value,det.transactionAmount as detTransactionAmount,srp_erp_customermaster.customerName as customerName');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,contractAutoID FROM srp_erp_contractdetails GROUP BY contractAutoID) det', '(det.contractAutoID = srp_erp_contractmaster.contractAutoID)', 'left');
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID', 'left');
        $this->datatables->from('srp_erp_contractmaster');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_contractmaster.contractAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_contractmaster.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_contractmaster.currentLevelNo');
        $this->datatables->where_in('srp_erp_documentapproved.documentID', array('QUT', 'CNT', 'SO'));
        $this->datatables->where_in('srp_erp_approvalusers.documentID', array('QUT', 'CNT', 'SO'));
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN')));
        $this->datatables->where('srp_erp_contractmaster.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->group_by('srp_erp_documentapproved.documentSystemCode');
        $this->datatables->add_column('contractCode', '$1', 'approval_change_modal(contractCode,contractAutoID,documentApprovedID,approvalLevelID,approvedYN,document)');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,document,contractAutoID)');
        $this->datatables->add_column('edit', '$1', 'con_action_approval(contractAutoID,approvalLevelID,approvedYN,documentApprovedID,document,0)');
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        echo $this->datatables->generate();
    }

    function load_contract_conformation()
    {
        $contractAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('contractAutoID'));
        $data['extra'] = $this->Quotation_contract_model->fetch_contract_template_data($contractAutoID);
        $data['approval'] = $this->input->post('approval');

        $this->db->select('documentID');
        $this->db->where('contractAutoID', trim($contractAutoID));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_contractmaster');
        $documentid = $this->db->get()->row_array();

        $printHeaderFooterYN=1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $this->db->select('printHeaderFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', $documentid['documentID']);
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();
        if(!empty($result)){
            $printHeaderFooterYN =$result['printHeaderFooterYN'];
            $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        }

        if (!$this->input->post('html')) {
            $data['signature'] = $this->Quotation_contract_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }

        $html = $this->load->view('system/quotation_contract/erp_contract_print', $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'],null,$printHeaderFooterYN);
        }
    }

    function save_quotation_contract_approval()
    {
        $system_code = trim($this->input->post('contractAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        $code = trim($this->input->post('code'));

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, $code, $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata('w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('contractAutoID');
                $this->db->where('contractAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_contractmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata('w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Quotation_contract_model->save_quotation_contract_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('contractAutoID');
            $this->db->where('contractAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            //$this->db->where('confirmedYN !=', 1);
            $this->db->from('srp_erp_contractmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata('w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, $code, $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata('w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Quotation_contract_model->save_quotation_contract_approval());
                    }
                }
            }
        }
    }

    function quotation_version()
    {
        echo json_encode($this->Quotation_contract_model->quotation_version());
    }

    function load_contract_header()
    {
        echo json_encode($this->Quotation_contract_model->load_contract_header());
    }

    function fetch_item_detail_table()
    {
        echo json_encode($this->Quotation_contract_model->fetch_item_detail_table());
    }

    function fetch_item_detail()
    {
        echo json_encode($this->Quotation_contract_model->fetch_item_detail());
    }

    function delete_item_detail()
    {
        echo json_encode($this->Quotation_contract_model->delete_item_detail());
    }

    function contract_confirmation()
    {
        echo json_encode($this->Quotation_contract_model->contract_confirmation());
    }

    function referback_Quotation_contract()
    {
        $contractAutoID = trim($this->input->post('contractAutoID'));
        $documentID = trim($this->input->post('code'));

        $this->db->select('approvedYN,contractCode');
        $this->db->where('contractAutoID', trim($contractAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_contractmaster');
        $approved = $this->db->get()->row_array();
        if (!empty($approved)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved['contractCode']));
        } else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($contractAutoID, $documentID);
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }
    }

    function delete_con_master()
    {
        echo json_encode($this->Quotation_contract_model->delete_con_master());
    }

    function document_drill_down_View_modal()
    {
        echo json_encode($this->Quotation_contract_model->document_drill_down_View_modal());
    }

    function delete_tax_detail()
    {
        echo json_encode($this->Quotation_contract_model->delete_tax_detail());
    }

    function save_inv_tax_detail()
    {
        $this->form_validation->set_rules('text_type', 'Tax Type', 'trim|required');
        $this->form_validation->set_rules('percentage', 'Percentage', 'trim|required');
        $this->form_validation->set_rules('contractAutoID', 'Document ID', 'trim|required');
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'data' => validation_errors()));
        } else {
            echo json_encode($this->Quotation_contract_model->save_inv_tax_detail());
        }
    }

    function save_item_order_detail()
    {
        $searchs = $this->input->post('search');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        foreach ($searchs as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity Requested', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Sales Price', 'trim|required|greater_than[0]');
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
            echo json_encode($this->Quotation_contract_model->save_item_order_detail());
        }
    }

    function update_item_order_detail()
    {
        $quantityRequested = trim($this->input->post('quantityRequested'));
        $estimatedAmount = trim($this->input->post('estimatedAmount'));

        $this->form_validation->set_rules("itemAutoID", 'Item', 'trim|required');
        $this->form_validation->set_rules("UnitOfMeasureID", 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules("quantityRequested", 'Quantity Requested', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules("estimatedAmount", 'Sales Price', 'trim|required|greater_than[0]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            if (($quantityRequested == 0) || ($estimatedAmount == 0)) {
                echo json_encode(array('e', ' Qty, Sales Price cannot be 0.'));
            } else {
                echo json_encode($this->Quotation_contract_model->update_item_order_detail());
            }

        }
    }

    function load_unitprice_exchangerate() //get localwac amount into exchange rate
    {
        echo json_encode($this->Quotation_contract_model->load_unitprice_exchangerate());
    }

    function delete_quotationContract_attachement()
    {
        echo json_encode($this->Quotation_contract_model->delete_quotationContract_attachement());
    }

    function re_open_contract()
    {
        echo json_encode($this->Quotation_contract_model->re_open_contract());
    }

    function loademail()
    {
        echo json_encode($this->Quotation_contract_model->loademail());
    }

    function send_quatation_email()
    {
        $this->form_validation->set_rules('email', 'email', 'trim|valid_email');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Quotation_contract_model->send_quatation_email());
        }
    }

    function fetch_documentID(){
        $documentSystemCode=$this->input->post('documentSystemCode');
        $this->db->select('documentID');
        $this->db->where('contractAutoID', trim($documentSystemCode));
        $this->db->from('srp_erp_contractmaster');
        $documentID = $this->db->get()->row_array();
        echo json_encode($documentID['documentID']);
    }

}