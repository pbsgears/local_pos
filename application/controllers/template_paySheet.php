<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/** ================================
 * -- File Name : template_paySheet.php
 * -- Project Name : Gs_SME
 * -- Module Name : Payroll
 * -- Author : Nasik Ahamed
 * -- Create date : 16 - July 2016
 * -- Description :
 */
class template_paySheet extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        ini_set('max_execution_time', 500);
        ini_set('memory_limit', '2048M');
        $this->load->model('template_paySheet_model');
        $this->load->helper('template_paySheet');
        $this->load->helper('employee');
        $this->load->helpers('payable');
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('common', $primaryLanguage);
        $this->lang->load('hrms_approvals', $primaryLanguage);
        $this->lang->load('profile', $primaryLanguage);
    }

    public function fetch_templates()
    {

        $isNonPayroll = $this->input->post('isNonPayroll');
        $this->datatables->select('templateID, templateDescription, documentCode, confirmedYN, isDefault, isNonPayroll', false)
            ->from('srp_erp_pay_template')
            ->add_column('defaultTemplate', '$1', 'confirm(confirmedYN)')
            ->add_column('status', '$1', 'template_status(templateID, isDefault, confirmedYN, isNonPayroll)')
            ->add_column('edit', '$1', 'editDelFunction(templateID, confirmedYN, templateDescription)')
            ->where('isNonPayroll', $isNonPayroll)
            ->where('companyID', current_companyID());
        echo $this->datatables->generate();

    }

    public function createTemplate()
    {
        echo json_encode($this->template_paySheet_model->createTemplate());
    }

    public function cloneTemplate()
    {
        echo json_encode($this->template_paySheet_model->cloneTemplate());
    }

    public function templateHeaderDetails()
    {
        echo json_encode($this->template_paySheet_model->templateHeaderDetails());
    }

    public function templateDetails()
    {
        echo json_encode($this->template_paySheet_model->templateDetails());
    }

    public function templateDetails_view()
    {
        $payrollMasterID = $this->input->post('hidden_payrollID');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $segmentID = $this->input->post('segmentID');
        $hideZeroColumn = $this->input->post('hideZeroColumn');

        $data['masterData'] = $this->template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll);

        $lastMonth = array(
            date('Y', strtotime('-1 months', strtotime($data['masterData']['payrollYear'] . '-' . $data['masterData']['payrollMonth'] . '-01'))),
            date('m', strtotime('-1 months', strtotime($data['masterData']['payrollYear'] . '-' . $data['masterData']['payrollMonth'] . '-01')))
        );

        if ($this->input->post('from_approval') == 'Y') {
            $def = ($isNonPayroll == 'Y') ? 'Y' : 'N';
            $templateID = ($data['masterData']['templateID'] != null) ? ($data['masterData']['templateID']) : getDefault_template($def);
            $data['isForReverse'] = ($this->input->post('isForReverse') == 'Y') ? 'Y' : 'N';
            $data['isFromPrint'] = 'Y';
        } else {
            $templateID = $this->input->post('templateId');
            $data['isFromPrint'] = 'N';
        }

        $data['header_det'] = $this->template_paySheet_model->templateDetails($templateID);
        $data['currency_groups'] = $this->template_paySheet_model->currencyWiseSum($payrollMasterID, $isNonPayroll, $segmentID);
        $data['isForReverse'] = null;
        if ($hideZeroColumn == 'Y') {
            $data['paysheetData'] = $this->template_paySheet_model->fetchPaySheetData($payrollMasterID, $isNonPayroll, $lastMonth, $segmentID, $templateID);
            echo $this->load->view('system\hrm\print\paySheet_print', $data, true);
        } else {
            $data['paysheetData'] = $this->template_paySheet_model->fetchPaySheetData($payrollMasterID, $isNonPayroll, $lastMonth, $segmentID);
            /*echo '<pre>'; print_r($data['paysheetData']); echo '</pre>';*/
            echo $this->load->view('system\hrm\print\paySheet_print_withZero', $data, true);
        }
    }

    public function statusChangePaysheetTemplate()
    {
        echo json_encode($this->template_paySheet_model->statusChangePaysheetTemplate());
    }

    public function referBack()
    {
        $id = $this->input->post('referID');
        $companyID = current_companyID();
        $details = $this->template_paySheet_model->templateStatus($id);
        $table = ($details->isNonPayroll != 'Y') ? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';
        /*$isInUse = $this->db->query("SELECT documentCode FROM {$table} WHERE companyID={$companyID}
                                     AND templateID={$id} AND confirmedYN=1")->row('documentCode');*/
        $isInUse = null;
        if (!empty($isInUse)) {
            echo json_encode(['e', 'You can not refer back this.<br/>This template is used for the payroll', $isInUse]);
        } else {
            echo json_encode($this->template_paySheet_model->referBack());
        }
    }

    public function deleteTemplate()
    {
        echo json_encode($this->template_paySheet_model->deleteTemplate());
    }

    public function templateFields()
    {
        echo json_encode($this->template_paySheet_model->templateFields());
    }

    public function templateDetailsSave()
    {
        echo json_encode($this->template_paySheet_model->templateDetailsSave());
    }

    public function templateCaptionUpdate()
    {
        echo json_encode($this->template_paySheet_model->templateCaptionUpdate());
    }

    public function templateSortOrderUpdate()
    {
        echo json_encode($this->template_paySheet_model->templateSortOrderUpdate());
    }

    public function loadTemplate()
    {
        $tempHeaderDet = $this->template_paySheet_model->templateHeaderDetails();
        $tempDet = $this->template_paySheet_model->templateDetails();

        echo json_encode(array('error' => 0, 'header' => $tempHeaderDet, 'details' => $tempDet));
    }

    public function loadPaySheetData()
    {

        $this->form_validation->set_rules('payYear', 'Year ', 'trim|required|numeric');
        $this->form_validation->set_rules('payMonth', 'Month', 'trim|required|numeric');
        $this->form_validation->set_rules('payNarration', 'Narration', 'trim|required');
        $this->form_validation->set_rules('processingDate', 'Processing Date', 'trim|required|date');
        $this->form_validation->set_rules('visibleDate', 'Visible Date', 'trim|required|date');
        $this->form_validation->set_rules('selectedEmployees', 'Employee', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $payYear = $this->input->post('payYear');
            $payMonth = $this->input->post('payMonth');
            $visibleDate = $this->input->post('visibleDate');
            $date_format_policy = date_format_policy();
            $visibleDate = input_format_date($visibleDate, $date_format_policy);
            $payrollFirstDate = date('Y-m-d', strtotime($payYear . '-' . $payMonth . '-01'));

            if ($payrollFirstDate > $visibleDate) {
                die(json_encode(['e', 'Payslip date can not be leaser than ' . convert_date_format($payrollFirstDate)]));
            }


            $isNonPayroll = $this->input->post('isNonPayroll'); // Payroll or Non payroll
            $isAlreadyProcessed = null; //$this->template_paySheet_model->loadPaySheetData($isNonPayroll);

            if ($isAlreadyProcessed == null) {
                $payYear = $this->input->post('payYear');
                $payMonth = $this->input->post('payMonth');
                $sendWithExp = $this->input->post('sendWithExp');
                $status = $this->template_paySheet_model->currentMonthPaysheetData_status($payYear, $payMonth, $isNonPayroll);

                //die();
                if ($status[0] == 's') {
                    if ($sendWithExp != 1) {
                        $expenseClaims = $this->pendingExpensesClaims();

                        //echo '<pre>'; print_r($expenseClaims); echo '</pre>';die();
                        if ($expenseClaims[1] !== 0) {
                            die(json_encode($expenseClaims));
                        }
                    }
                    echo json_encode($this->template_paySheet_model->insertPaySheetDataBasedOnEmployee());

                } else {
                    echo json_encode($status);
                }

            } else {
                echo json_encode(array('w', 'Already Payroll processed on given month.'));
            }
        }
    }

    function getVisibleDate()
    {
        $year = $this->input->post('year');
        $month = $this->input->post('month');
        $lastDate = date('Y-m-t', strtotime($year . '-' . $month . '-01'));
        $lastDate = convert_date_format($lastDate);

        echo json_encode($lastDate);
    }

    public function fetchPaySheetData()
    {
        $payrollMasterID = $this->input->post('hidden_payrollID');
        $this->form_validation->set_rules('hidden_payrollID', 'Payroll ID', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $payrollDet = $this->template_paySheet_model->fetchPaySheetData($payrollMasterID);
            $currencyWiseSum = $this->template_paySheet_model->currencyWiseSum($payrollMasterID);
            echo json_encode(array('s', $payrollDet, $currencyWiseSum));
        }
    }

    public function fetch_paySheets()
    {
        $companyID = current_companyID();
        $isGroupAccess = getPolicyValues('PAC', 'All');

        $this->datatables->select('payrollMasterID, documentCode, payrollYear, payrollMonth, narration, confirmedYN, approvedYN, templateID', false)
            ->from('srp_erp_payrollmaster')
            ->edit_column('payrollMonth', '$1', 'payrollMonthInName(payrollYear, payrollMonth)')
            ->add_column('confirm', '$1', 'confirm(confirmedYN)')
            ->add_column('approve', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"SP",payrollMasterID)')
            ->add_column('action', '$1', 'paySheetAction(payrollMasterID, confirmedYN, approvedYN, payrollYear, payrollMonth, N, templateID)')
            ->where('companyID', $companyID);

        if ($isGroupAccess == 1) {
            $currentEmp = current_userID();
            $this->datatables->join("(SELECT payID FROM srp_erp_payrollgroupincharge AS inCharge
                                      JOIN(
                                            SELECT payrollMasterID AS payID, accessGroupID
                                            FROM srp_erp_payrollheaderdetails WHERE companyID={$companyID}
                                            AND accessGroupID IS NOT NULL
                                            GROUP BY payrollMasterID, accessGroupID
                                      ) AS headerDet ON inCharge.groupID=headerDet.accessGroupID
                                      WHERE companyID={$companyID} AND empID={$currentEmp}
                                      GROUP BY payID) AS accTB", 'srp_erp_payrollmaster.payrollMasterID = accTB.payID');

        }
        echo $this->datatables->generate();
    }

    public function fetch_paySheets_conformation()
    {
        $userID = $this->common_data["current_userID"];
        $status = trim($this->input->post('approvedYN'));

        /*
        * rejected = 1
        * not rejected = 0
        * */
        $where = array(
            'approve.documentID' => 'SP',
            'ap.documentID' => 'SP',
            'ap.employeeID' => $userID,
            'approve.approvedYN' => $status,
        );

        $this->datatables->select('payrollMasterID, t1.documentCode AS documentCode, payrollYear, payrollMonth, narration, approve.approvedYN as approvedYN,
        documentApprovedID, approvalLevelID', true)
            ->from('srp_erp_payrollmaster AS t1')
            ->join('srp_erp_documentapproved AS approve', 'approve.documentSystemCode = t1.payrollMasterID AND approve.approvalLevelID = t1.currentLevelNo')
            ->join('srp_erp_approvalusers AS ap', 'ap.levelNo = t1.currentLevelNo')
            ->where($where)
            ->where('t1.companyID', current_companyID())
            ->where('ap.companyID', current_companyID())
            ->add_column('payrollMonth', '$1', 'payrollMonthInName(payrollYear, payrollMonth)')
            ->add_column('level', "<center>Level $1</center>", 'approvalLevelID')
            ->add_column('documentCode_str', '$1', 'paysheet_action_approval(payrollMasterID, approvalLevelID, payrollMonth, documentCode, approvedYN, \'code\')')
            ->add_column('edit', '$1', 'paysheet_action_approval(payrollMasterID, approvalLevelID, payrollMonth, documentCode, approvedYN, \'edit\')')
            ->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"SP", payrollMasterID)');


        echo $this->datatables->generate('json', 'ISO-8859-1');
    }

    public function fetch_non_paySheets_conformation()
    {
        $userID = $this->common_data["current_userID"];
        $status = trim($this->input->post('approvedYN'));

        /*
        * rejected = 1
        * not rejected = 0
        * */
        $where = array(
            'approve.documentID' => 'SPN',
            'ap.documentID' => 'SPN',
            'ap.employeeID' => $userID,
            'approve.approvedYN' => $status,
        );

        $this->datatables->select('payrollMasterID, t1.documentCode AS documentCode, payrollYear, payrollMonth, narration, approve.approvedYN as approvedYN,
        documentApprovedID, approvalLevelID', true)
            ->from('srp_erp_non_payrollmaster AS t1')
            ->join('srp_erp_documentapproved AS approve', 'approve.documentSystemCode = t1.payrollMasterID AND approve.approvalLevelID = t1.currentLevelNo')
            ->join('srp_erp_approvalusers AS ap', 'ap.levelNo = t1.currentLevelNo')
            ->where($where)
            ->where('ap.companyID', current_companyID())
            ->where('t1.companyID', current_companyID())
            ->add_column('payrollMonth', '$1', 'payrollMonthInName(payrollYear, payrollMonth)')
            ->add_column('level', "<center>Level $1</center>", 'approvalLevelID')
            ->add_column('documentCode_str', '$1', 'paysheet_action_approval(payrollMasterID, approvalLevelID, payrollMonth, documentCode, approvedYN, \'code\')')
            ->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "SPN", payrollMasterID)')
            ->add_column('edit', '$1', 'paysheet_action_approval(payrollMasterID, approvalLevelID, payrollMonth, documentCode, approvedYN, \'edit\')');

        echo $this->datatables->generate('json', 'ISO-8859-1');
    }

    public function getPayrollDetails()
    {
        $payrollID = $this->input->post('payrollID');
        $isNonPayroll = $this->input->post('isNonPayroll');

        echo json_encode($this->template_paySheet_model->getPayrollDetails($payrollID, $isNonPayroll));
    }

    public function getPayrollApproveLevel()
    {
        $payrollID = $this->input->post('payrollID');
        echo json_encode($this->template_paySheet_model->getPayrollApproveLevel($payrollID));
    }

    public function update_PaySheet()
    {
        $this->form_validation->set_rules('hidden_payrollID', 'Payroll ID', 'trim|required');
        $this->form_validation->set_rules('payNarration', 'Narration', 'trim|required');
        $this->form_validation->set_rules('templateId', 'Template', 'trim|required');
        $this->form_validation->set_rules('processingDate', 'Processing Date', 'trim|required|date');
        $this->form_validation->set_rules('visibleDate', 'Visible Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $payrollID = trim($this->input->post('hidden_payrollID'));
            $isConfirm = $this->input->post('isConfirm');
            $isNonPayroll = $this->input->post('isNonPayroll');
            $visibleDate = $this->input->post('visibleDate');
            $date_format_policy = date_format_policy();
            $visibleDate = input_format_date($visibleDate, $date_format_policy);

            $payrollDet = $this->template_paySheet_model->getPayrollDetails($payrollID, $isNonPayroll);

            $payrollMonth = date('Y - F', strtotime($payrollDet['payrollYear'] . '-' . $payrollDet['payrollMonth'] . '-01'));

            $payrollFirstDate = date('Y-m-d', strtotime($payrollDet['payrollYear'] . '-' . $payrollDet['payrollMonth'] . '-01'));


            if ($payrollFirstDate > $visibleDate) {
                die(json_encode(['e', 'Payslip date can not be leaser than ' . convert_date_format($payrollFirstDate)]));
            }

            if ($payrollDet['confirmedYN'] != 1) {

                $isFinanceYearMatch = array();
                if ($isConfirm == 1) {
                    $year = $payrollDet['payrollYear'];
                    $month = $payrollDet['payrollMonth'];
                    $isFinanceYearMatch = $this->template_paySheet_model->check_financeYear($year, $month);

                    if ($isFinanceYearMatch[0] == 's') {
                        echo json_encode($this->template_paySheet_model->update_PaySheet($payrollDet, $isFinanceYearMatch, $isNonPayroll));
                    } else {
                        echo json_encode($isFinanceYearMatch);
                    }
                } else {
                    $isFinanceYearMatch[0] = null;
                    $isFinanceYearMatch[1] = null;
                    $isFinanceYearMatch[2] = null;
                    echo json_encode($this->template_paySheet_model->update_PaySheet($payrollDet, $isFinanceYearMatch, $isNonPayroll));
                }


            } else {
                echo json_encode(array('e', $payrollMonth . ' payroll is already confirmed you can not update this'));
            }
        }
    }

    public function referBackPayroll()
    {
        $this->form_validation->set_rules('referID', 'Payroll ID', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $payrollID = $this->input->post('referID');
            $isNonPayroll = $this->input->post('isNonPayroll');
            $payrollDet = $this->template_paySheet_model->getPayrollDetails($payrollID, $isNonPayroll);
            $payrollMonth = date('Y - F', strtotime($payrollDet['payrollYear'] . '-' . $payrollDet['payrollMonth'] . '-01'));

            if ($payrollDet['approvedYN'] == 1) {
                echo json_encode(array('e', '[ ' . $payrollMonth . ' ] is already approved, you can not refer back this'));
            } else {

                $this->load->library('approvals');
                $docCode = ($isNonPayroll != 'Y') ? 'SP' : 'SPN';
                $status = $this->approvals->approve_delete($payrollID, $docCode);
                if ($status == 1) {
                    echo json_encode(array('s', $payrollMonth . ' Referred Back Successfully.', $status));
                } else {
                    echo json_encode(array('e', $payrollMonth . ' Error in refer back.', $status));
                }
            }
        }
    }

    public function payroll_delete()
    {
        echo json_encode($this->template_paySheet_model->payroll_delete());
    }

    public function payroll_refresh()
    {
        echo json_encode($this->template_paySheet_model->payroll_refresh());
    }

    public function payroll_bankTransfer()
    {
        $this->form_validation->set_rules('payrollID', 'Payroll ID', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $payrollMasterID = $this->input->post('payrollID');
            $isNonPayroll = $this->input->post('isNonPayroll');
            $getPayrollDetails = $this->template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll);


            if ($getPayrollDetails['confirmedYN'] != 1) {
                echo json_encode(array('e', 'This Payroll is not confirmed yet.'));
            } else if ($getPayrollDetails['isBankTransferProcessed'] == 1) {
                echo json_encode(array('e', 'Bank Transfer process already done.<P> Please refresh the page and load again '));
            } else {
                $isSuccess = $this->template_paySheet_model->payroll_bankTransfer($payrollMasterID, $isNonPayroll);
                if ($isSuccess[0] == 's') {
                    echo json_encode(array('s', 'Processing'));
                    /*$data['bankTransferDet'] = $this->template_paySheet_model->payroll_bankTransferData($payrollMasterID);
                    $this->load->view('system\hrm\pay_sheetSalaryBankTransfer', $data);*/
                } else {
                    echo json_encode($isSuccess);
                }
            }

        }
    }

    public function load_bankTransferPage()
    {
        $payrollMasterID = $this->input->post('payrollID');
        $isNonPayroll = $this->input->post('isNonPayroll');

        $data['bankTransferDet'] = $this->template_paySheet_model->payroll_bankTransferPendingData($payrollMasterID, $isNonPayroll);
        $data['currencySum'] = $this->template_paySheet_model->payroll_bankTransferPendingData_currencyWiseSum($payrollMasterID, $isNonPayroll);
        $data['payrollID'] = $payrollMasterID;
        $data['isPending'] = (!empty($data['bankTransferDet'])) ? 'Y' : 'N';

        $this->load->view('system\hrm\pay_sheetSalaryBankTransfer', $data);
    }

    public function load_empWithoutBankPage()
    {
        $payrollMasterID = $this->input->post('payrollID');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $data['empWithoutBank'] = $this->template_paySheet_model->payroll_empWithoutBank($payrollMasterID, $isNonPayroll, 0);
        $data['empWithoutBank_paid'] = $this->template_paySheet_model->payroll_empWithoutBank($payrollMasterID, $isNonPayroll, 1);
        $data['payrollID'] = $payrollMasterID;
        $data['isPending'] = (count($data['empWithoutBank']) == 0) ? 'N' : 'Y';

        /*echo '<pre>'; print_r($data['empWithoutBank']); echo '</pre>'; die();*/

        $this->load->view('system\hrm\pay_sheetSalaryEmpWithoutBank', $data);
    }

    public function new_bankTransfer()
    {
        $this->form_validation->set_rules('bnkPayrollID', 'Payroll ID', 'trim|required|numeric');
        $this->form_validation->set_rules('accountID', 'Bank', 'trim|required|numeric');
        $this->form_validation->set_rules('transDate', 'Transfer Date', 'trim|required|date');
        $this->form_validation->set_rules('transCheck[]', 'Employee', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->template_paySheet_model->new_bankTransfer());
        }
    }

    public function fetch_processedBankTransfer()
    {
        $payrollID = $this->input->get('id');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $tableName = ($isNonPayroll != 'Y') ? 'srp_erp_pay_banktransfermaster' : 'srp_erp_pay_non_banktransfermaster';

        $this->datatables->select('bankTransferID, payrollMasterID, documentCode, bankName, branchName, swiftCode, accountNo, confirmedYN', false)
            ->from($tableName)
            ->add_column('amountDetails', '$1', 'processed_bankTransferData_currencyWiseSum(payrollMasterID, bankTransferID,' . $isNonPayroll . ')')
            ->add_column('action', '$1', 'actionBankProcess(bankTransferID, confirmedYN, documentCode,' . $isNonPayroll . ')')
            ->where('payrollMasterID', $payrollID)
            ->where('companyID', current_companyID());
        echo $this->datatables->generate();
    }

    public function pay_sheetBankTransferDet_load()
    {
        $bankTransID = $this->input->post('bankTransID');
        $payrollMasterID = $this->input->post('payrollMasterID');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $payrollDet = $this->template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll);
        $bTransOtherDet = $this->template_paySheet_model->getTotalBankTransferAmount($bankTransID, $isNonPayroll);

        $data['bTransOtherDet'] = $bTransOtherDet['lo_currency'] . ' ' . number_format($bTransOtherDet['lo_amount'], $bTransOtherDet['lo_dPlace']);
        $data['masterData'] = $this->template_paySheet_model->get_bankTransferMasterDet($bankTransID, $isNonPayroll);
        $data['payDate'] = date('Y - F', strtotime($payrollDet['payrollYear'] . '-' . $payrollDet['payrollMonth'] . '-01'));
        $data['bankTransferDet'] = $this->template_paySheet_model->processed_bankTransferData($payrollMasterID, $bankTransID, $isNonPayroll);
        $data['currencySum'] = processed_bankTransferData_currencyWiseSum($payrollMasterID, $bankTransID, $isNonPayroll, 1);

        $data['bankTransID'] = $bankTransID;

        $this->load->view('system\hrm\pay_sheetBankTransferDet_load', $data);
    }

    public function pay_sheetBankTransferDet_delete()
    {
        echo json_encode($this->template_paySheet_model->pay_sheetBankTransferDet_delete());
    }

    public function confirm_bankTransfer()
    {
        echo json_encode($this->template_paySheet_model->confirm_bankTransfer());
    }

    public function bankTransfer_print()
    {
        $bankTransID = $this->uri->segment(3);
        $isNonPayroll = $this->uri->segment(4);
        $data['masterData'] = $this->template_paySheet_model->get_bankTransferMasterDet($bankTransID, $isNonPayroll);
        $payrollMasterID = $data['masterData']['payrollMasterID'];
        $data['bankTransferDet'] = $this->template_paySheet_model->processed_bankTransferData($payrollMasterID, $bankTransID, $isNonPayroll);
        $data['bankTransID'] = $bankTransID;
        $data['currencySum'] = processed_bankTransferData_currencyWiseSum($payrollMasterID, $bankTransID, $isNonPayroll, 1);

        /*echo $this->load->view('system\hrm\print\bankTransferPrint', $data,true); die();*/
        $html = $this->load->view('system\hrm\print\bankTransferPrint', $data, true);
        $this->load->library('pdf');
        //$this->pdf->printed($html, 'A4', 1);
        $this->pdf->printed_bank_letter($html, 'A4', 1);

    }

    public function bankTransfer_excel_tab()
    {
        $this->load->library('excel');
        $bankTransID = $this->uri->segment(3);
        $isNonPayroll = $this->uri->segment(4);

        $masterData = $this->template_paySheet_model->get_bankTransferMasterDet($bankTransID, $isNonPayroll);
        $payrollMasterID = $masterData['payrollMasterID'];
        $bankTransferDet = $this->template_paySheet_model->processed_bankTransferData($payrollMasterID, $bankTransID, $isNonPayroll);
        $currencySum = processed_bankTransferData_currencyWiseSum($payrollMasterID, $bankTransID, $isNonPayroll, 1);

        $wizard = new PHPExcel_Helper_HTML;

        $lastBank = null;
        $lastCurrency = null;
        $lastGroup = null;

        $x = 0;
        $temArray = array_group_by($bankTransferDet, 'bankName', 'transactionCurrency');
        foreach ($temArray as $key => $transfers) {

            $this->excel->createSheet();
            $this->excel->setActiveSheetIndex($x);
            $tabName = substr($key, 0, 30);
            $this->excel->getActiveSheet()->setTitle($tabName);

            $width = 5;
            foreach (range('A', 'G') as $columnID) {
                $this->excel->getActiveSheet()->getStyle($columnID . '2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DEDEDE');
                $this->excel->getActiveSheet()->getStyle($columnID . '2')->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
                $this->excel->getActiveSheet()->getColumnDimension($columnID)->setWidth($width);
            }

            $html = '<p style="margin-left: 2%;">' . $key . '</p>';
            $richText = $wizard->toRichTextObject($html);
            $this->excel->getActiveSheet()->setCellValue('A1', $richText);
            $this->excel->getActiveSheet()->setCellValue('A2', '#');
            $this->excel->getActiveSheet()->setCellValue('B2', 'EMP ID');
            $this->excel->getActiveSheet()->setCellValue('C2', 'Name');
            $this->excel->getActiveSheet()->setCellValue('D2', 'Swift Code');
            $this->excel->getActiveSheet()->setCellValue('E2', 'Account No');
            $this->excel->getActiveSheet()->setCellValue('F2', 'Currency');
            $this->excel->getActiveSheet()->setCellValue('G2', 'Amount');

            $z = 3;
            foreach ($transfers as $key => $transfer) {
                if ($z > 3) {
                    $this->excel->getActiveSheet()->setCellValue('A' . ($z + 1), $key);
                    $this->excel->getActiveSheet()->mergeCells('A' . ($z + 1) . ':G' . ($z + 1));
                } else {
                    $this->excel->getActiveSheet()->setCellValue('A' . $z, $key);
                    $this->excel->getActiveSheet()->mergeCells('A' . $z . ':G' . $z);
                }
                $y = 0;
                if ($z > 3) {
                    $y = $z + 2;
                } else {
                    $y = $z + 1;
                }
                $tot = 0;
                $i = 1;
                $decimal = "";
                foreach ($transfer as $data) {
                    $trCurrency = trim($data['transactionCurrency']);
                    $this->excel->getActiveSheet()->setCellValue('A' . $y, $i++);
                    $this->excel->getActiveSheet()->setCellValue('B' . $y, $data['ECode']);
                    $this->excel->getActiveSheet()->setCellValue('C' . $y, $data['acc_holderName']);
                    $this->excel->getActiveSheet()->setCellValue('D' . $y, $data['swiftCode']);
                    $this->excel->getActiveSheet()->setCellValue('E' . $y, $data['accountNo']);
                    $this->excel->getActiveSheet()->setCellValue('F' . $y, $trCurrency);
                    $this->excel->getActiveSheet()->setCellValue('G' . $y, round($data['transactionAmount'], $data['transactionCurrencyDecimalPlaces']));

                    $this->excel->getActiveSheet()->getStyle('G' . $y)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                    $totThis = number_format($data['transactionAmount'], $data['transactionCurrencyDecimalPlaces'], '.', '');
                    $decimal = $data['transactionCurrencyDecimalPlaces'];
                    $tot += $totThis;

                    $y++;
                    $z = $y;
                }

                $this->excel->getActiveSheet()->setCellValue('A' . ($z), 'Total');
                $this->excel->getActiveSheet()->setCellValue('G' . ($z), round($tot, $decimal));
                $this->excel->getActiveSheet()->mergeCells('A' . ($z) . ':F' . ($z));
                $this->excel->getActiveSheet()->getStyle('G' . $z)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DEDEDE');
                $this->excel->getActiveSheet()->getStyle('A' . ($z))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $this->excel->getActiveSheet()->getStyle('G' . ($z))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            }
            $x++;
        }

        $this->excel->removeSheetByIndex($x);
        ob_clean();
        ob_start(); # added
        $filename = 'BankTransferTab.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        ob_clean(); # remove this
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }

    public function bankTransfer_excel_single()
    {
        $this->load->library('excel');
        $bankTransID = $this->uri->segment(3);
        $isNonPayroll = $this->uri->segment(4);
        $masterData = $this->template_paySheet_model->get_bankTransferMasterDet($bankTransID, $isNonPayroll);
        $payrollMasterID = $masterData['payrollMasterID'];
        $bankTransferDet = $this->template_paySheet_model->processed_bankTransferData($payrollMasterID, $bankTransID, $isNonPayroll);
        $currencySum = processed_bankTransferData_currencyWiseSum($payrollMasterID, $bankTransID, $isNonPayroll, 1);

        $wizard = new PHPExcel_Helper_HTML;
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle("Bank Transfer");
        $lastBank = null;
        $lastCurrency = null;
        $lastGroup = null;

        $temArray = array_group_by($bankTransferDet, 'bankName', 'transactionCurrency');
        $x = 2;
        foreach ($temArray as $key => $transfers) {
            foreach (range('A', 'G') as $columnID) {
                $this->excel->getActiveSheet()->getStyle($columnID . $x)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DEDEDE');
                $this->excel->getActiveSheet()->getStyle($columnID . $x)->getFont()->setBold(true);
            }

            $this->excel->getActiveSheet()->setCellValue('A' . ($x - 1), $key);
            $this->excel->getActiveSheet()->setCellValue('A' . $x, '#');
            $this->excel->getActiveSheet()->setCellValue('B' . $x, 'EMP ID');
            $this->excel->getActiveSheet()->setCellValue('C' . $x, 'Name');
            $this->excel->getActiveSheet()->setCellValue('D' . $x, 'Swift Code');
            $this->excel->getActiveSheet()->setCellValue('E' . $x, 'Account No');
            $this->excel->getActiveSheet()->setCellValue('F' . $x, 'Currency');
            $this->excel->getActiveSheet()->setCellValue('G' . $x, 'Amount');

            $z = $x + 1;
            $w = 0;
            $multiple = 0;
            foreach ($transfers as $key => $transfer) {
                if (count($transfers) > 1) {
                    if ($w > 0) {
                        $this->excel->getActiveSheet()->setCellValue('A' . ($z + 1), $key);
                        $this->excel->getActiveSheet()->mergeCells('A' . ($z + 1) . ':G' . ($z + 1));
                    } else {
                        $this->excel->getActiveSheet()->setCellValue('A' . $z, $key);
                        $this->excel->getActiveSheet()->mergeCells('A' . $z . ':G' . $z);
                    }

                } else {
                    $this->excel->getActiveSheet()->setCellValue('A' . $z, $key);
                    $this->excel->getActiveSheet()->mergeCells('A' . $z . ':G' . $z);
                }
                $y = 0;
                if (count($transfers) > 1) {
                    if ($w > 0) {
                        $y = $z + 2;
                    } else {
                        $y = $z + 1;
                    }
                } else {
                    $y = $z + 1;
                }
                $tot = 0;
                $i = 1;
                $decimal = "";
                foreach ($transfer as $data) {
                    $trCurrency = trim($data['transactionCurrency']);
                    $this->excel->getActiveSheet()->setCellValue('A' . $y, $i++);
                    $this->excel->getActiveSheet()->setCellValue('B' . $y, $data['ECode']);
                    $this->excel->getActiveSheet()->setCellValue('C' . $y, $data['acc_holderName']);
                    $this->excel->getActiveSheet()->setCellValue('D' . $y, $data['swiftCode']);
                    $this->excel->getActiveSheet()->setCellValue('E' . $y, $data['accountNo']);
                    $this->excel->getActiveSheet()->setCellValue('F' . $y, $trCurrency);
                    $this->excel->getActiveSheet()->setCellValue('G' . $y, round($data['transactionAmount'], $data['transactionCurrencyDecimalPlaces']));

                    $this->excel->getActiveSheet()->getStyle('G' . $y)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                    $totThis = round($data['transactionAmount'], $data['transactionCurrencyDecimalPlaces']);
                    $decimal = $data['transactionCurrencyDecimalPlaces'];
                    $tot += $totThis;

                    $y++;
                    $z = $y;
                }

                $this->excel->getActiveSheet()->setCellValue('A' . ($z), 'Total');
                $this->excel->getActiveSheet()->setCellValue('G' . ($z), round($tot, $decimal));
                $this->excel->getActiveSheet()->mergeCells('A' . ($z) . ':F' . ($z));
                $this->excel->getActiveSheet()->getStyle('G' . $z)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DEDEDE');
                $this->excel->getActiveSheet()->getStyle('A' . ($z))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $this->excel->getActiveSheet()->getStyle('G' . ($z))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                $x = $z + 3;
                $w++;
            }
        }
        ob_clean();
        ob_start(); # added
        $filename = 'BankTransferSingle.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        ob_clean(); # remove this
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }

    public function bankTransferCoverLetter_print()
    {

        $bankTransID = $this->uri->segment(3);
        $isNonPayroll = $this->uri->segment(4);
        $masterData = $this->template_paySheet_model->get_bankTransferMasterDet($bankTransID, $isNonPayroll);
        $data['masterData'] = $masterData;

        $payrollMasterID = $masterData['payrollMasterID'];
        $payrollDet = $this->template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll);
        $bTransOtherDet = $this->template_paySheet_model->getTotalBankTransferAmount($bankTransID, $isNonPayroll);

        $currencySum = processed_bankTransferData_currencyWiseSum($payrollMasterID, $bankTransID, $isNonPayroll, 1);
        $currencySumStr = '';
        foreach ($currencySum as $sumRow) {
            $currencySumStr .= $sumRow['transactionCurrency'] . ' ' . $sumRow['trAmount'] . '</br>';
        }
        $data['bTransOtherDet'] = $currencySumStr;


        $data['payDate'] = date('M, Y', strtotime($payrollDet['payrollYear'] . '-' . $payrollDet['payrollMonth'] . '-01'));
        $data['bankTransferDet'] = $this->template_paySheet_model->processed_bankTransferData($payrollMasterID, $bankTransID, $isNonPayroll);


        //echo '<pre>'; print_r($data['bTransOtherDet']); echo '</pre>';die();
        //echo $this->load->view('system\hrm\print\bankTransferCoverLetterPrint', $data,true); die();

        $html = $this->load->view('system\hrm\print\bankTransferCoverLetterPrint', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4', 1);

    }

    public function pay_slip()
    {
        $payrollID = $this->uri->segment(3);
        $empID = $this->uri->segment(4);
        $isNonPayroll = $this->uri->segment(5);

        $data['masterData'] = $this->template_paySheet_model->getPayrollDetails($payrollID, $isNonPayroll);

        $documentCode = ($isNonPayroll != 'Y') ? 'SP' : 'SPN';
        $code = ($isNonPayroll != 'Y') ? 'PT' : 'NPT';

        $template = getPolicyValues($code, $documentCode); //$this->template_paySheet_model->getDefault_paySlipTemplate($isNonPayroll);

        if ($template == 'Envoy') {
            $data['details'] = $this->template_paySheet_model->get_empPaySlipDet($empID, $payrollID, $isNonPayroll);
            $pageSize = 'A5';
            if (!empty($data['details'])) {
                $this->load->model('Employee_model');
                $html = $this->load->view('system\hrm\print\pay_slip_print_envoy', $data, true);
            } else {
                $html = 'No data';
            }

        } else if ($template == 0) {
            $data['details'] = $this->template_paySheet_model->get_empPaySlipDet($empID, $payrollID, $isNonPayroll);
            $pageSize = 'A4';
            if (!empty($data['details'])) {
                $this->load->model('Employee_model');
                $data['leaveDet'] = false; // $this->Employee_model->get_emp_leaveDet_paySheetPrint($empID, $data['masterData']);
                $html = $this->load->view('system\hrm\print\paySlipPrint', $data, true);
            } else {
                $html = 'No data';
            }

        } else {

            $data['details'] = $this->template_paySheet_model->fetchPaySheetData_employee($payrollID, $empID, $isNonPayroll);
            $pageSize = 'A5';

            if (!empty($data['details'])) {
                $data['header_det'] = $this->template_paySheet_model->templateDetails($template);
                /*echo '$template:'.$template;
                echo '<pre>'; print_r($data['header_det']); echo '</pre>';die();*/
                $html = $this->load->view('system\hrm\print\paySlipPrint_template', $data, true);
            } else {
                $html = 'No data';
            }

        }
        //echo $html; die();
        $this->load->library('pdf');
        $this->pdf->printed($html, $pageSize, $data['masterData']['approvedYN']);

    }

    public function pay_slip_selected_employee()
    {
        $payrollMonth = trim($this->input->post('payrollMonth'));
        $segment = $this->input->post('segmentID');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $empID = $this->input->post('empID');
        $companyID = current_companyID();

        $pYear = date('Y', strtotime($payrollMonth));
        $pMonth = date('m', strtotime($payrollMonth));

        $filter_payroll = $filter = "";

        if ($segment != '') {
            $segmentID = explode('|', $segment);
            $filter_payroll .= " AND segmentID={$segmentID[0]}";
        }

        $masterTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';
        $headerTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollheaderdetails' : 'srp_erp_non_payrollheaderdetails';


        $payroll_data = $this->db->query("SELECT payrollMaster.* FROM {$headerTB} AS t1
                                           JOIN (
                                              SELECT payrollMasterID, payrollYear, payrollMonth, narration
                                              FROM {$masterTB} WHERE companyID={$companyID}
                                              AND payrollYear={$pYear} AND payrollMonth={$pMonth} AND approvedYN=1
                                           ) AS payrollMaster ON payrollMaster.payrollMasterID = t1.payrollMasterID
                                           WHERE companyID={$companyID} {$filter_payroll} GROUP BY payrollMasterID")->result_array();

        $payrollID_arr = implode(',', array_column($payroll_data, 'payrollMasterID'));

        $documentCode = ($isNonPayroll != 'Y') ? 'SP' : 'SPN';
        $code = ($isNonPayroll != 'Y') ? 'PT' : 'NPT';

        $template = getPolicyValues($code, $documentCode); //$template = $this->template_paySheet_model->getDefault_paySlipTemplate($isNonPayroll);

        $data['payroll_data'] = $payroll_data;

        if ($template == 'Envoy') {
            $data['details'] = $this->get_empPaySlipDetSelectedEmp($payrollID_arr, $isNonPayroll, $empID);
            $data['empIDs'] = $empID;
            $data['leaveDet'] = false;

            $this->load->view('system\hrm\print\pay_slip_print_envoy_selected_employee', $data);
        } else if ($template == 0) {
            $data['details'] = $this->get_empPaySlipDetSelectedEmp($payrollID_arr, $isNonPayroll, $empID);
            $data['empIDs'] = $empID;
            $data['leaveDet'] = false;

            $this->load->view('system\hrm\print\pay_slip_selected_employee', $data);
        } else {
            $data['details'] = $this->get_empPaySlipDetSelectedEmp_template($payrollID_arr, $isNonPayroll, $empID);

            if (!empty($data['details'])) {
                $data['header_det'] = $this->template_paySheet_model->templateDetails($template);
                echo $this->load->view('system\hrm\print\paySlipPrint_template_all', $data, true);
            } else {
                echo 'No data';
            }
        }
    }

    public function get_empPaySlipDetSelectedEmp($payrollID_list, $isNonPayroll, $empID)
    {

        if ($isNonPayroll != 'Y') {
            $headerDetailTableName = 'srp_erp_payrollheaderdetails';
            $detailTableName = 'srp_erp_payrolldetail';
        } else {
            $headerDetailTableName = 'srp_erp_non_payrollheaderdetails';
            $detailTableName = 'srp_erp_non_payrolldetail';
        }

        $empList = implode(', ', $empID);
        $companyID = current_companyID();

        $headerDet = $this->db->query("SELECT Ename2 AS empName, Designation,EmpID, payrollMasterID, secondaryCode,
                                       IF(transactionCurrency = null , transactionCurrency, payCurrency) AS transactionCurrency,
                                       IF(transactionCurrencyDecimalPlaces = null, transactionCurrencyDecimalPlaces,
                                       (SELECT DecimalPlaces FROM srp_erp_currencymaster WHERE CurrencyCode = payCurrency )) AS dPlace
                                       FROM {$headerDetailTableName} WHERE payrollMasterID IN ({$payrollID_list}) AND
                                       EmpID IN({$empList}) AND  {$headerDetailTableName}.companyID={$companyID}
                                       GROUP BY {$headerDetailTableName}.EmpID")->result_array();

        //salary Declarations
        $salaryDec_A = $this->db->query("SELECT salaryDescription, detailType, sum(pay.transactionAmount) AS transactionAmount,
                                        pay.transactionCurrencyDecimalPlaces AS dPlace,pay.EmpID, fromTB,pay.salCatID
                                        FROM  {$detailTableName} AS pay
                                        JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = pay.salCatID
                                        WHERE payrollMasterID IN ({$payrollID_list})
                                        AND (fromTB = 'SD' OR  fromTB = 'VP' OR fromTB = 'BP' OR fromTB = 'OT') AND detailType = 'A' AND transactionAmount != 0 AND pay.companyID={$companyID}
                                        AND pay.EmpID IN({$empList}) GROUP BY pay.salCatID,pay.EmpID ")->result_array();

        $salaryDec_D = $this->db->query("SELECT salaryDescription, detailType, sum(pay.transactionAmount) AS transactionAmount,
                                        pay.transactionCurrencyDecimalPlaces AS dPlace,pay.EmpID
                                        FROM  {$detailTableName} AS pay
                                        JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = pay.salCatID
                                        WHERE payrollMasterID IN ({$payrollID_list})
                                        AND (fromTB = 'SD' OR  fromTB = 'NO-PAY')  AND detailType = 'D'  AND transactionAmount != 0 AND pay.companyID={$companyID}
                                        AND pay.EmpID IN({$empList}) GROUP BY pay.salCatID,pay.EmpID ")->result_array();

        //Monthly Addition
        $monthAdd = $this->db->query("SELECT *, SUM(amnt) AS transactionAmount FROM (
                                        SELECT monthlyDeclaration AS description, detailType, pay.transactionAmount AS amnt, pay.EmpID, 
                                        CONCAT(IFNULL(salCatID,0),'_',monthlyDeclarationID) AS grField, pay.transactionCurrencyDecimalPlaces AS dPlace
                                        FROM  {$detailTableName} AS pay
                                        JOIN srp_erp_pay_monthlyadditiondetail AS mAdd ON mAdd.monthlyAdditionDetailID = pay.detailTBID
                                        JOIN srp_erp_pay_monthlydeclarationstypes AS monDec ON monDec.monthlyDeclarationID = mAdd.declarationID
                                        WHERE payrollMasterID IN ({$payrollID_list}) AND pay.companyID={$companyID}
                                        AND fromTB = 'MA' AND pay.EmpID IN({$empList})
                                     ) t1 GROUP BY EmpID, grField")->result_array();



        //Monthly Deduction
        $monthDec = $this->db->query("SELECT *, SUM(amnt) AS transactionAmount FROM (
                                        SELECT monthlyDeclaration AS description, detailType, pay.transactionAmount AS amnt, pay.EmpID, 
                                        CONCAT(IFNULL(salCatID,0),'_',monthlyDeclarationID) AS grField, pay.transactionCurrencyDecimalPlaces AS dPlace                                    
                                        FROM  {$detailTableName} AS pay
                                        JOIN srp_erp_pay_monthlydeductiondetail AS mDed ON mDed.monthlyDeductionDetailID = pay.detailTBID
                                        JOIN srp_erp_pay_monthlydeclarationstypes AS monDec ON monDec.monthlyDeclarationID = mDed.declarationID
                                        WHERE payrollMasterID IN ({$payrollID_list}) AND pay.companyID={$companyID}
                                        AND fromTB = 'MD' AND pay.EmpID IN({$empList})
                                      ) t1 GROUP BY EmpID, grField")->result_array();

        //SSO Payee
        $sso_payee = $this->db->query("SELECT grMaster.description, detailType, pay.transactionAmount, pay.transactionCurrencyDecimalPlaces AS dPlace,pay.EmpID
                                        FROM  {$detailTableName} AS pay
                                        JOIN srp_erp_paygroupmaster AS grMaster ON grMaster.payGroupID = pay.detailTBID
                                        LEFT JOIN (
                                            SELECT * FROM srp_erp_socialinsurancemaster WHERE companyID={$companyID}
                                        ) AS ssoMaster ON ssoMaster.socialInsuranceID = grMaster.socialInsuranceID
                                        WHERE payrollMasterID IN ({$payrollID_list}) AND (employerContribution = 0  OR employerContribution is null)
                                        AND fromTB = 'PAY_GROUP' AND pay.EmpID IN({$empList}) GROUP BY detailTBID,pay.EmpID ")->result_array();


        $employerContributions = [];
        $OT_data = [];
        $isNonPayroll = $this->input->post('isNonPayroll');
        /*Get only for Envoy template (Not for Non payroll) */
        if($isNonPayroll != 'Y'){
            $template = getPolicyValues('PT', 'SP');
            if ($template == 'Envoy') {
                $tempData = $this->db->query("SELECT template_tb.id, transactionAmount, pay.empID FROM srp_erp_sso_reporttemplatefields AS template_tb
                                        LEFT JOIN srp_erp_sso_reporttemplatedetails AS setup_tb ON setup_tb.reportID = template_tb.id  
                                        AND setup_tb.companyID={$companyID}
                                        LEFT JOIN srp_erp_payrolldetail AS pay ON setup_tb.reportValue=pay.detailTBID 
                                        AND pay.payrollMasterID IN ({$payrollID_list}) AND pay.empID IN ({$empList}) 
                                        WHERE template_tb.id IN (6, 7, 18) ")->result_array();

                $tempData = array_group_by($tempData, 'empID');
                foreach ($tempData as $tKey=>$tRow){
                    foreach($tRow as $fnRow){
                        $employerContributions[$tKey][$fnRow['id']] = $fnRow['transactionAmount'];
                    }
                }


                /**** Get Over time hours and minutes */
                $OT_temp = $this->db->query("SELECT payTb.empID, CONCAT(FLOOR(hourorDays/60),'h ',MOD(hourorDays,60),'m') AS otHour, salCatID
                                FROM srp_erp_payrolldetail payTb
                                JOIN (
                                    SELECT ID AS attRVID, hourorDays, otDet.empID 
                                    FROM srp_erp_pay_empattendancereview attTB
                                    JOIN srp_erp_generalotdetail otDet ON attTB.generalOTID = otDet.generalOTMasterID
	                                AND attTB.empID = otDet.empID AND otDet.salaryCategoryID = attTB.salaryCategoryID
                                    WHERE paymentOT != 0 AND attTB.companyID={$companyID} AND attTB.empID IN ({$empList}) AND hourorDays != 0
                                    GROUP BY ID
                                ) AS otTB ON otTB.empID=payTb.empID AND payTb.detailTBID=otTB.attRVID
                                WHERE payrollMasterID IN ({$payrollID_list}) AND fromTB='OT' GROUP BY
	                                                            payTb.salCatID,otTB.empID")->result_array();

                $OT_data = array_group_by($OT_temp, 'empID');
                /*if(!empty($OT_temp)){
                    foreach ($OT_temp as $oRow){
                        $OT_data[$oRow['empID']] = $oRow['otHour'];
                    }
                }*/

            }
        }

        //Loan Deduction
        $loanDed = $this->db->query("SELECT installmentNo, loan.loanCode, loanDescription, detailType, pay.transactionAmount,
                                        pay.transactionCurrencyDecimalPlaces AS dPlace,pay.EmpID
                                        FROM  {$detailTableName} AS pay
                                        JOIN srp_erp_pay_emploan_schedule AS loan_sch ON loan_sch.ID = pay.detailTBID
                                        JOIN srp_erp_pay_emploan AS loan ON loan.ID = loan_sch.loanID
                                        WHERE payrollMasterID IN ({$payrollID_list}) AND pay.companyID={$companyID}
                                        AND fromTB = 'LO' AND pay.EmpID IN({$empList}) GROUP BY pay.EmpID")->result_array();


        $loanIntPending = $this->db->query("SELECT loan.loanCode, loanDescription, count(l_sched.ID) AS pending_Int,
                                            sum(l_sched.transactionAmount) as trAmount,loan.empID
                                            FROM srp_erp_pay_emploan AS loan
                                            JOIN srp_erp_pay_emploan_schedule AS l_sched ON loan.ID = l_sched.loanID
                                            WHERE approvedYN = 1 AND isClosed != 1 AND isSetteled = 0 AND skipedInstallmentID = 0
                                            AND loan.EmpID IN({$empList}) GROUP BY loan.loanCode,loan.empID")->result_array();


        //Bank transfer
        $bankTransferDed = $this->db->query("SELECT bankName, accountNo, transactionCurrency, transactionAmount, salaryTransferPer,
                                             transactionCurrencyDecimalPlaces AS dPlace, swiftCode, empID
                                             FROM srp_erp_pay_banktransfer
                                             WHERE payrollMasterID IN ({$payrollID_list}) AND companyID={$companyID} AND empID IN({$empList})
                                             GROUP BY empID")->result_array();

        //Salary Paid by cash / cheque
        $salaryNonBankTransfer = $this->db->query("SELECT * FROM srp_erp_payroll_salarypayment_without_bank WHERE payrollMasterID IN ({$payrollID_list})
                                                   AND empID IN({$empList}) AND companyID={$companyID} GROUP BY empID")->result_array();;

        return array(
            'headerDet' => array_group_by($headerDet, 'EmpID'),
            'salaryDec_A' => array_group_by($salaryDec_A, 'EmpID'),
            'salaryDec_D' => array_group_by($salaryDec_D, 'EmpID'),
            'monthAdd' => array_group_by($monthAdd, 'EmpID'),
            'monthDec' => array_group_by($monthDec, 'EmpID'),
            'sso_payee' => array_group_by($sso_payee, 'EmpID'),
            'employerContributions' => $employerContributions,
            'loanDed' => array_group_by($loanDed, 'EmpID'),
            'loanIntPending' => array_group_by($loanIntPending, 'empID'),
            'bankTransferDed' => array_group_by($bankTransferDed, 'empID'),
            'salaryNonBankTransfer' => array_group_by($salaryNonBankTransfer, 'empID'),
            'OT_data' => $OT_data
        );

    }

    public function get_empPaySlipDetSelectedEmp_template($payrollID_list, $isNonPayroll, $empID)
    {

        if ($isNonPayroll != 'Y') {
            $headerDetailTableName = 'srp_erp_payrollheaderdetails';
            $detailTableName = 'srp_erp_payrolldetail';
            $payGroupDetailTableName = 'srp_erp_payrolldetailpaygroup';
        } else {
            $headerDetailTableName = 'srp_erp_non_payrollheaderdetails';
            $detailTableName = 'srp_erp_non_payrolldetail';
            $payGroupDetailTableName = 'srp_erp_non_payrolldetailpaygroup';
        }

        $empList = implode(', ', $empID);
        $companyID = current_companyID();

        $info = $this->db->query("SELECT empTB.*, empTB.transactionAmount AS netTrans , fromTB, calculationTB, detailType, salCatID, pay.detailTBID,
                                  sum(pay.transactionAmount) AS transactionAmount, pay.transactionCurrencyDecimalPlaces, seg.segmentCode AS emp_segmentCode
                                  FROM {$headerDetailTableName} AS empTB
                                  JOIN {$detailTableName} AS pay ON empTB.EmpID=pay.empID
                                  LEFT JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = pay.salCatID
                                  LEFT JOIN srp_erp_segment AS seg ON seg.segmentID = empTB.segmentID
                                  WHERE pay.payrollMasterID IN ({$payrollID_list}) AND empTB.payrollMasterID IN ({$payrollID_list}) AND pay.companyID = {$companyID}
                                  AND fromTB != 'PAY_GROUP' AND pay.empID IN ($empList)
                                  GROUP BY pay.empID, pay.salCatID, pay.calculationTB
                                  UNION
                                        SELECT empTB.*, empTB.transactionAmount AS netTrans, fromTB, calculationTB, detailType, salCatID, pay.detailTBID,
                                        pay.transactionAmount AS transactionAmount, pay.transactionCurrencyDecimalPlaces, seg.segmentCode AS emp_segmentCode
                                        FROM {$headerDetailTableName} AS empTB
                                        JOIN {$detailTableName} AS pay ON empTB.EmpID=pay.empID
                                        LEFT JOIN srp_erp_segment AS seg ON seg.segmentID = empTB.segmentID
                                        WHERE fromTB = 'PAY_GROUP' AND pay.payrollMasterID IN ({$payrollID_list}) AND empTB.payrollMasterID IN ({$payrollID_list})
                                        AND pay.empID IN ($empList)
                                  UNION
                                        SELECT pay2.*, pay2.transactionAmount AS netTrans, fromTB, fromTB AS calculationTB, detailType, '' AS salCatID,
                                        detailTBID, payGroup.transactionAmount, payGroup.transactionCurrencyDecimalPlaces, '' AS emp_segmentCode
                                        FROM {$payGroupDetailTableName}  AS payGroup
                                        JOIN {$headerDetailTableName} AS pay2 ON payGroup.empID=pay2.empID AND pay2.companyID={$companyID} AND
                                        pay2.payrollMasterID IN ({$payrollID_list})
                                        WHERE payGroup.companyID={$companyID} AND payGroup.payrollMasterID IN ({$payrollID_list})
                                        AND payGroup.empID IN ($empList)
                                  ORDER BY empID DESC")->result_array();

        if (isset($info)) {

            $dataArray = array();
            $i = 0;
            $j = 0;
            $ECode = '';

            foreach ($info as $row) {
                $tmpECode = $row['ECode'];

                if ($ECode != $tmpECode) {
                    $j = 0;
                    $i++;

                    switch ($row['Gender']) {
                        case '1':
                            $gender = 'Male';
                            break;

                        case '2':
                            $gender = 'Female';
                            break;

                        default :
                            $gender = '-';
                    }

                    //$dataArray[$i]['empDet'] = $row;
                    $dataArray[$i]['empDet'] = array(
                        'masterID' => $row['payrollMasterID'],
                        'E_ID' => $row['EmpID'],
                        'ECode' => $row['ECode'],
                        'Ename1' => $row['Ename1'],
                        'Ename2' => $row['Ename2'],
                        'Ename3' => $row['Ename3'],
                        'Ename4' => $row['Ename4'],
                        'EmpShortCode' => $row['EmpShortCode'],
                        'Designation' => $row['Designation'],
                        'Gender' => $gender,
                        'EcTel' => $row['Tel'],
                        'EcMobile' => $row['Mobile'],
                        'EDOJ' => $row['DOJ'],
                        'payCurrency' => $row['payCurrency'],
                        'nationality' => $row['nationality'],
                        'dPlaces' => $row['transactionCurrencyDecimalPlaces'],
                        'segmentID' => $row['emp_segmentCode']
                    );

                    $ECode = $row['ECode'];
                }


                if ($row['calculationTB'] == 'SD') {
                    $cat = $row['salCatID'];
                } else if ($row['fromTB'] == 'PAY_GROUP') {
                    $cat = 'G_' . $row['detailTBID'];
                } else {
                    $cat = $row['fromTB'];
                }

                $dataArray[$i]['empSalDec'][$j] = array(
                    'catID' => $cat,
                    'catType' => $row['detailType'],
                    'amount' => $row['transactionAmount'],
                );
                $j++;

            }
            return $dataArray;

        } else {
            return 'There is no record.';
        }

    }

    public function paySheet_print()
    {
        $payrollMasterID = $this->uri->segment(3);
        $templateID = $this->uri->segment(4);
        $isNonPayroll = $this->uri->segment(5);

        $data['masterData'] = $this->template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll);

        $lastMonth = array(
            date('Y', strtotime('-1 months', strtotime($data['masterData']['payrollYear'] . '-' . $data['masterData']['payrollMonth'] . '-01'))),
            date('m', strtotime('-1 months', strtotime($data['masterData']['payrollYear'] . '-' . $data['masterData']['payrollMonth'] . '-01')))
        );

        $data['header_det'] = $this->template_paySheet_model->templateDetails($templateID);
        $data['currency_groups'] = $this->template_paySheet_model->currencyWiseSum($payrollMasterID, $isNonPayroll);
        $data['paysheetData'] = $this->template_paySheet_model->fetchPaySheetData($payrollMasterID, $isNonPayroll, $lastMonth, '');
        $data['isFromPrint'] = 'Y';
        $data['isForReverse'] = null;

        $html = $this->load->view('system\hrm\print\paySheet_print_withZero', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4-L', $data['masterData']['confirmedYN']);
    }

    public function paySheetPrint_segmentWise()
    {
        $payrollMasterID = $this->uri->segment(3);
        $templateID = $this->uri->segment(4);
        $isNonPayroll = $this->uri->segment(5);
        $segmentID = $this->input->post('segmentID');
        $hideZeroColumn = $this->input->post('hideZeroColumn');

        $data['masterData'] = $this->template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll, $segmentID);
        $lastMonth = array(
            date('Y', strtotime('-1 months', strtotime($data['masterData']['payrollYear'] . '-' . $data['masterData']['payrollMonth'] . '-01'))),
            date('m', strtotime('-1 months', strtotime($data['masterData']['payrollYear'] . '-' . $data['masterData']['payrollMonth'] . '-01')))
        );
        $data['header_det'] = $this->template_paySheet_model->templateDetails($templateID);
        $data['currency_groups'] = $this->template_paySheet_model->currencyWiseSum($payrollMasterID, $isNonPayroll, $segmentID);
        $data['isFromPrint'] = 'Y';
        $data['isForReverse'] = null;

        if ($hideZeroColumn == 'Y') {
            $data['paysheetData'] = $this->template_paySheet_model->fetchPaySheetData($payrollMasterID, $isNonPayroll, $lastMonth, $segmentID, $templateID);
            $html = $this->load->view('system\hrm\print\paySheet_print', $data, true);
        } else {
            $data['paysheetData'] = $this->template_paySheet_model->fetchPaySheetData($payrollMasterID, $isNonPayroll, $lastMonth, $segmentID);
            $html = $this->load->view('system\hrm\print\paySheet_print_withZero', $data, true);
        }

        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4-L', $data['masterData']['approvedYN']);
    }

    public function save_empNonBankPay()
    {
        $empID = $this->input->post('hidden_empID');
        $payrollMasterID = $this->input->post('hidden_payrollID');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $payType = $this->input->post('payType');
        $empPayBank = $this->input->post('empPayBank');
        $chequeNo = $this->input->post('chequeNo');

        $this->form_validation->set_rules('hidden_payrollID', 'Payroll ID', 'trim|required|numeric');
        $this->form_validation->set_rules('hidden_empID', 'Employee ID', 'trim|required|numeric');
        $this->form_validation->set_rules('payType', 'Payment Type', 'trim|required');
        $this->form_validation->set_rules('paymentDate', 'Payment Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $isAlreadyPaid = $this->template_paySheet_model->get_EmpNonBankTransferDet($empID, $payrollMasterID, $isNonPayroll);

            if ($isAlreadyPaid['isPaid'] != 1) {
                $byChq_errorMsg = '';
                if ($payType == 'By Cheque') {
                    if (trim($empPayBank) == '') {
                        $byChq_errorMsg = '<p>Bank field is required</p>';
                    }
                    if (trim($chequeNo) == '') {
                        $byChq_errorMsg .= '<p>Cheque No field is required</p>';
                    }
                }

                if ($byChq_errorMsg != '') {
                    echo json_encode(array('e', $byChq_errorMsg));
                } else {
                    echo json_encode($this->template_paySheet_model->save_empNonBankPay());
                }
            } else {
                echo json_encode(array('e', 'Already salary paid for this employee'));
            }

        }
    }

    public function print_empNonBankPay()
    {
        $payrollMasterID = $this->uri->segment(3);
        $isNonPayroll = $this->uri->segment(4);
        $data['masterData'] = $this->template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll);
        $data['printData'] = $this->template_paySheet_model->payroll_empWithoutBank($payrollMasterID, $isNonPayroll, 1);

        /*echo '<pre>';print_r( $data['printData']);echo '</pre>';die();*/
        /*echo $this->load->view('system\hrm\print\empNonBankPay', $data,true);die();*/

        $html = $this->load->view('system\hrm\print\empNonBankPayPrint', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4', $data['masterData']['confirmedYN']);
    }

    function getNotPayrollProcessedMonths()
    {
        $year = $this->input->post('year');
        $type = (!empty($this->input->post('payrollType'))) ? 'Y' : 'N';
        echo json_encode(payrollCalender($year, 2, $type));
    }

    function paysheetApproval()
    {
        $paysheetID = $this->input->post('hiddenPaysheetID');
        $paysheetCode = $this->input->post('hiddenPaysheetCode');
        $level_id = $this->input->post('level');
        $status = $this->input->post('status');
        $comments = $this->input->post('comments');
        $isNonPayroll = $this->input->post('isNonPayroll');

        $this->form_validation->set_rules('hiddenPaysheetID', 'Paysheet ID', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        $this->form_validation->set_rules('level', 'Level', 'trim|required');
        if ($this->input->post('status') == 2) {
            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $isConfirmed = $this->template_paySheet_model->getPayrollDetails($paysheetID, $isNonPayroll);

            if ($isConfirmed['confirmedYN'] == 1) {
                $this->load->library('approvals');
                $docCode = ($isNonPayroll != 'Y') ? 'SP' : 'SPN';
                $approvals_status = $this->approvals->approve_document($paysheetID, $level_id, $status, $comments, $docCode);

                if ($approvals_status == 2) {
                    //  echo json_encode(array('s', 'Pay sheet [ ' . $paysheetCode . ' ] Approved', $approvals_status));
                    $msg = $this->lang->line('common_paysheet') . ' [ ' . $paysheetCode . ' ] ' . strtolower($this->lang->line('common_approved')) . '.';
                    echo json_encode(array('s', $msg, $approvals_status));
                } else if ($approvals_status == 1) {
                    echo json_encode($this->template_paySheet_model->double_entries($paysheetID, $isNonPayroll));
                    //echo json_encode(array('s', 'Paysheet [ ' . $paysheetCode . ' ] Approved', $approvals_status));

                } else if ($approvals_status == 3) {
                    $Rejectprocess = $this->lang->line('hrms_payroll_approvals_reject_process_successfully_done');
                    echo json_encode(array('s', '[ ' . $paysheetCode . ' ]' . $Rejectprocess . ' .'));/*Approvals  Reject Process Successfully done*/
                } else if ($approvals_status == 5) {
                    $previouslevel = $this->lang->line('hrms_payroll_previous_level_approval_not_finished');

                    echo json_encode(array('w', '[ ' . $paysheetCode . ' ] ' . $previouslevel . '.'));/*Previous Level Approval Not Finished*/
                } else {
                    $errorinpaysheet = $this->lang->line('hrms_payroll_error_in_paysheet_approvals_of');

                    echo json_encode(array('e', ' [ ' . $paysheetCode . ' ]' . $errorinpaysheet . ' '));/*Error in Paysheet Approvals Of */
                }
            } else {
                $paysheet = $this->lang->line('common_paysheet');
                $notconfirmed = $this->lang->line('common_not_confirmed_yet');
                $refresh = $this->lang->line('common_please_refresh_and_try_again');
                echo json_encode(array('e', '' . $paysheet . ' [ ' . $paysheetCode . ' ] ' . $notconfirmed . '.</br>' . $refresh . '.'));/*Pay sheet*//*not confirmed yet*//*Please refresh and try again*/
            }
        }
    }

    function payrollAccountReview()
    {
        $payrollMasterID = $this->uri->segment(3);
        $isNonPayroll = $this->uri->segment(4);
        $data['masterData'] = $this->template_paySheet_model->getPayrollDetails($payrollMasterID, $isNonPayroll);
        $accReviewData_arr = $this->template_paySheet_model->payrollAccountReview($payrollMasterID, $isNonPayroll);

        $data['accReviewData_arr'] = $accReviewData_arr[1];

        $html = $this->load->view('system\hrm\print\payroll-account-review', $data, true);

        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4', 1);

    }

    function update_template_sortOrder()
    {
        $tempFieldID = $this->input->post('tempFieldID');
        $sortOrder = $this->input->post('sortOrder');

        $update = $this->db->update('srp_erp_pay_templatedetail', array('sortOrder' => $sortOrder), array('tempFieldID' => $tempFieldID));
        if ($update) {
            echo json_encode(array('error' => 0, 'message' => 'success'));
        } else {
            echo json_encode(array('error' => 0, 'message' => 'error'));
        }

    }

    function double_entries()
    {
        /*echo json_encode( $this->template_paySheet_model->double_entries($fb) );*/
    }

    function get_paySlip_report()
    {
        $payrollMonth = trim($this->input->post('payrollMonth'));
        $isNonPayroll = $this->input->post('isNonPayroll');
        $segment = $this->input->post('segmentID');
        $empID = $this->input->post('empID');
        $filter_payroll = $filter = "";
        $Pleasesselectatleastoneemployeetoproceed = $this->lang->line('common_please_select_at_least_one_employee_to_proceed');
        if ($empID == '') {
            echo '<div class="col-md-12 bg-border" style="">
                   <div class="row">
                        <div class="col-md-12 xxcol-md-offset-2">
                            <div class="alert alert-warning" role="alert">
                                <p>' . $Pleasesselectatleastoneemployeetoproceed . '<!--Please select at least one employee to proceed--></p>
                            </div>
                        </div>
                    </div>
                   </div>';
            die();
        } else {
            $commaList = implode(', ', $empID);
            $filter .= " AND empID IN({$commaList})";
        }


        $companyID = current_companyID();
        $pYear = date('Y', strtotime($payrollMonth));
        $pMonth = date('m', strtotime($payrollMonth));

        if ($segment != '') {
            $segmentID = explode('|', $segment);
            $filter_payroll .= " AND segmentID={$segmentID[0]}";
        }

        $masterTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';
        $headerTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollheaderdetails' : 'srp_erp_non_payrollheaderdetails';
        $detailTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrolldetail' : 'srp_erp_non_payrolldetail';


        $payrollID_arr = $this->db->query("SELECT payrollMasterID FROM {$headerTB} AS t1
                                           JOIN (
                                              SELECT payrollMasterID AS payID FROM {$masterTB} WHERE companyID={$companyID}
                                              AND payrollYear={$pYear} AND payrollMonth={$pMonth} AND approvedYN=1
                                           ) AS payrollMaster ON payrollMaster.payID = t1.payrollMasterID
                                           WHERE companyID={$companyID} {$filter_payroll} GROUP BY payrollMasterID")->result_array();

        if (empty($payrollID_arr)) {
            echo '<div class="col-md-12 bg-border" style="">
                       <div class="row">
                            <div class="col-md-12 xxcol-md-offset-2">
                                <div class="alert alert-warning" role="alert">
                                    <p>Please select at least one employee to proceed</p>
                                </div>
                            </div>
                       </div>
                  </div>';
            die();
        }

        $payrollID_arr = implode(',', array_column($payrollID_arr, 'payrollMasterID'));

        $sql = "SELECT {$masterTB}.payrollMasterID, EIdNo, ECode, EmpDesignationId, Ename1, Ename2, Ename3, Ename4, EmpShortCode,
              srp_employeesdetails.segmentID, transactionCurrency, transactionCurrencyDecimalPlaces, SUM(IF(detailType = 'A', transactionAmount, 0)) AS addition,
              SUM(IF(detailType = 'D' || detailType = 'G', transactionAmount  , 0))  AS deduction , SUM(IF(detailType = 'A', transactionAmount, 0)) +
              SUM(IF(detailType = 'D' || detailType = 'G', transactionAmount, 0))  AS total, detailType, salCatID
              FROM {$masterTB}
              LEFT JOIN {$detailTB} ON {$masterTB}.payrollMasterID = {$detailTB}.payrollMasterID
              LEFT JOIN srp_employeesdetails ON empID = EidNo
              WHERE approvedYN = 1 AND {$masterTB}.payrollMasterID IN ({$payrollID_arr}) {$filter} AND {$masterTB}.companyID = '{$companyID}'
              AND NOT EXISTS (
                  SELECT payGroupID FROM srp_erp_paygroupmaster AS groupMaster
                  JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID=groupMaster.socialInsuranceID AND SSO_TB.companyID='{$companyID}'
                  WHERE {$detailTB}.detailTBID = groupMaster.payGroupID AND {$detailTB}.fromTB='PAY_GROUP'
                  AND groupMaster.companyID='{$companyID}' AND SSO_TB.employerContribution > 0
              ) and {$detailTB}.payrollMasterID IN ({$payrollID_arr}) AND
              {$detailTB}.empID=srp_employeesdetails.EIdNo
              GROUP BY EIdNo , transactionCurrency";


        $sql2 = "SELECT transactionCurrency as currency, transactionCurrencyDecimalPlaces,
               SUM(IF(detailType = 'A', transactionAmount, 0)) AS totaladdition,
               SUM(IF(detailType = 'D' || detailType = 'G', transactionAmount  , 0))  AS totaldeduction ,
               SUM(IF(detailType = 'A', transactionAmount, 0)) + SUM(IF(detailType = 'D' || detailType = 'G', transactionAmount, 0))  AS totalamount
               FROM {$masterTB}
               LEFT JOIN {$detailTB} ON {$masterTB}.payrollMasterID = {$detailTB}.payrollMasterID
               LEFT JOIN srp_employeesdetails ON empID = EidNo
               WHERE approvedYN = 1 AND {$masterTB}.payrollMasterID IN ({$payrollID_arr}) {$filter} AND {$masterTB}.companyID = '{$companyID}'
               AND NOT EXISTS (
                   SELECT payGroupID FROM srp_erp_paygroupmaster AS groupMaster
                   JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID=groupMaster.socialInsuranceID AND SSO_TB.companyID='{$companyID}'
                   WHERE {$detailTB}.detailTBID = groupMaster.payGroupID AND {$detailTB}.fromTB='PAY_GROUP' AND
                   groupMaster.companyID='{$companyID}' AND SSO_TB.employerContribution > 0
               ) and {$detailTB}.payrollMasterID IN ({$payrollID_arr})
               AND {$detailTB}.empID=srp_employeesdetails.EIdNo
              GROUP BY  transactionCurrency";

        $data['detail'] = $this->db->query($sql)->result_array();
        $data['currency'] = $this->db->query($sql2)->result_array();

        /*  $this->load->view('system\hrm\pay_sheetTemplateDetails_view', $data);*/
        echo $this->load->view('system/hrm/ajax/load-employee-pays-slip', $data, true);
    }

    function dropdown_payslipemployees()
    {

        $this->form_validation->set_rules('payrollMonth', 'Payroll Month', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $html = '<div class="col-md-12 bg-border" style="">
                    <div class="row">
                        <div class="col-md-12 xxcol-md-offset-2">
                            <div class="alert alert-warning" role="alert">
                                ' . validation_errors() . '
                            </div>
                        </div>
                    </div>
                   </div>';

            echo json_encode(['e', $html]);

        } else {
            $payrollMonth = trim($this->input->post('payrollMonth'));
            $segment = $this->input->post('segmentID');
            $isNonPayroll = $this->input->post('isNonPayroll');
            $companyID = current_companyID();

            $pYear = date('Y', strtotime($payrollMonth));
            $pMonth = date('m', strtotime($payrollMonth));

            if (empty($segment)) {
                $segmentFilter = '';
            } else {
                $seg = explode('|', $segment);
                $segmentFilter = 'AND segmentID=' . $seg[0];
            }

            $masterTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';
            $headerTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollheaderdetails' : 'srp_erp_non_payrollheaderdetails';

            $str = '';
            $isGroupAccess = getPolicyValues('PAC', 'All');
            $data['isGroupAccess'] = $isGroupAccess;
            if ($isGroupAccess == 1) {
                $currentEmp = current_userID();
                $str = "JOIN (
                            SELECT groupID FROM srp_erp_payrollgroupincharge
                            WHERE companyID={$companyID} AND empID={$currentEmp}
                        ) AS accTb ON accTb.groupID = t1.accessGroupID";
            }

            $empArr = $this->db->query("SELECT EmpID, ECode, Ename2 FROM {$headerTB} AS t1
                                        JOIN (
                                           SELECT payrollMasterID AS payID FROM {$masterTB} WHERE companyID={$companyID}
                                           AND payrollYear={$pYear} AND payrollMonth={$pMonth} AND approvedYN=1
                                        ) AS payrollMaster ON payrollMaster.payID = t1.payrollMasterID
                                        {$str}
                                        WHERE companyID={$companyID} {$segmentFilter}")->result_array();

            $html = '<select name="empID[]" id="empID" class="form-control" multiple="multiple"  required>';

            if ($empArr) {
                foreach ($empArr as $empID) {
                    $html .= '<option value="' . $empID['EmpID'] . '">' . $empID['ECode'] . '|' . $empID['Ename2'] . '</option>';
                }
            }
            $html .= '</select>';

            echo json_encode(['s', $html]);
        }
    }


    public function fetch_paySheets_nonPayroll()
    {
        $companyID = current_companyID();

        $this->datatables->select('payrollMasterID, documentCode, payrollYear, payrollMonth, narration, confirmedYN, approvedYN, templateID', false)
            ->from('srp_erp_non_payrollmaster')
            ->edit_column('payrollMonth', '$1', 'payrollMonthInName(payrollYear, payrollMonth)')
            ->add_column('confirm', '$1', 'confirm_approval(confirmedYN)')
            ->add_column('approve', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"SPN",payrollMasterID)')
            ->add_column('action', '$1', 'paySheetAction(payrollMasterID, confirmedYN, approvedYN, payrollYear, payrollMonth, Y, templateID)')
            ->where('companyID', $companyID);
        echo $this->datatables->generate();
    }

    function get_paySlip_profile()
    {
        $companyID = current_companyID();
        $payrollMonth = trim($this->input->post('payrollMonth'));
        $isNonPayroll = trim($this->input->post('isNonPayroll'));
        $empID = trim($this->input->post('empID'));
        $pYear = date('Y', strtotime($payrollMonth));
        $pMonth = date('m', strtotime($payrollMonth));

        $headerTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollheaderdetails' : 'srp_erp_non_payrollheaderdetails';
        $masterTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';


        $payrollData = $this->db->query("SELECT payrollMasterID, visibleDate FROM {$headerTB} AS t1
                                       JOIN (
                                          SELECT payrollMasterID AS payID, visibleDate FROM {$masterTB} WHERE companyID={$companyID}
                                          AND payrollYear={$pYear} AND payrollMonth={$pMonth} AND approvedYN=1
                                       ) AS payrollMaster ON payrollMaster.payID = t1.payrollMasterID
                                       WHERE companyID={$companyID} AND empID={$empID}  ")->row_array();

        $warning_msg = $this->lang->line('common_warning');
        $payroll_not_run = $this->lang->line('profile_payroll_nor_run_on_selected_month_for_you');
        if (empty($payrollData)) {
            $returnData = '<div class="col-sm-12"><div class="alert alert-warning">
                             <strong>' . $warning_msg . '<!--Warning-->!</strong> <br/>' . $payroll_not_run . '<!--Payroll Not run on selected month for you-->.
                           </div></div>';
            die($returnData);
        }

        if ($payrollData['visibleDate'] > date('Y-m-d')) {
            $returnData = '<div class="col-sm-12"><div class="alert alert-warning">
                             <strong>' . $warning_msg . '<!--Warning-->!</strong> <br/>' . $payroll_not_run . '<!--Payroll Not run on selected month for you-->.
                           </div></div>';
            die($returnData);
        }

        $this->get_paySlip_reports_pdf($payrollData['payrollMasterID']);
    }

    function get_paySlip_reports_pdf($payrollID = 0)
    {

        if ($payrollID == 0) {
            $payrollID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('payrollMasterID'));
        }

        $empID = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('empID'));
        $isNonPayroll = ($this->uri->segment(5)) ? $this->uri->segment(5) : trim($this->input->post('isNonPayroll'));
        $ishtml = $this->input->post('html');
        $data['payrollMasterID'] = $payrollID;
        $data['empID'] = $empID;
        $data['isNonPayroll'] = $isNonPayroll;

        $data['masterData'] = $this->template_paySheet_model->getPayrollDetails($payrollID, $isNonPayroll);

        $documentCode = ($isNonPayroll != 'Y') ? 'SP' : 'SPN';
        $code = ($isNonPayroll != 'Y') ? 'PT' : 'NPT';

        $template = getPolicyValues($code, $documentCode); //$this->template_paySheet_model->getDefault_paySlipTemplate($isNonPayroll);


        if ($template == 'Envoy') {
            $data['details'] = $this->template_paySheet_model->get_empPaySlipDet($empID, $payrollID, $isNonPayroll);
            $pageSize = 'A5';
            if (!empty($data['details'])) {
                $this->load->model('Employee_model');
                $html = $this->load->view('system\hrm\print\pay_slip_print_envoy', $data, true);
            } else {
                $html = 'No data';
            }

        } else if ($template == 0) {
            $data['details'] = $this->template_paySheet_model->get_empPaySlipDet($empID, $payrollID, $isNonPayroll);
            $pageSize = 'A4';
            if (!empty($data['details'])) {
                $this->load->model('Employee_model');
                $data['leaveDet'] = false; // $this->Employee_model->get_emp_leaveDet_paySheetPrint($empID, $data['masterData']);
                $html = $this->load->view('system\hrm\print\paySlipPrint', $data, true);
            } else {
                $html = 'No data';
            }

        } else {

            $data['details'] = $this->template_paySheet_model->fetchPaySheetData_employee($payrollID, $empID, $isNonPayroll);
            $pageSize = 'A5';

            if (!empty($data['details'])) {
                $data['header_det'] = $this->template_paySheet_model->templateDetails($template);
                /*echo '$template:'.$template;
                echo '<pre>'; print_r($data['header_det']); echo '</pre>';die();*/
                $html = $this->load->view('system\hrm\print\paySlipPrint_template', $data, true);
            } else {
                $html = 'No data';
            }

        }
       // echo $html; die();
        if ($ishtml == 1) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $this->pdf->printed($html, $pageSize, $data['masterData']['approvedYN']);
        }

    }

    function payroll_dropDown()
    {
        $isNonPayroll = $this->input->post('isNonPayroll');

        echo json_encode(payrollMonth_dropDown($isNonPayroll));
    }

    function payroll_dropDown_with_visible_date()
    {
        $isNonPayroll = $this->input->post('isNonPayroll');

        echo json_encode(payrollMonth_dropDown_with_visible_date($isNonPayroll));
    }

    function getEmployeesDataTable()
    {
        $companyID = current_companyID();
        $segment = $this->input->post('segment');
        $currency = $this->input->post('currency');
        $payYear = $this->input->post('payYear');
        $payMonth = $this->input->post('payMonth');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $effectiveDate = date('Y-m-t', strtotime($payYear . '-' . $payMonth . '-01'));
        $segmentFilter = '';
        $currencyFilter = '';

        $salaryDeclarationTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_pay_salarydeclartion' : 'srp_erp_pay_salarydeclartion';
        $headerDetailTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollheaderdetails' : 'srp_erp_payrollheaderdetails';
        $payrollMaster = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';

        if (!empty($segment)) {
            $segmentFilter = ' AND srp_erp_segment.segmentID IN (' . $segment . ')';
        }
        if (!empty($currency)) {
            $currencyFilter = ' AND srp_erp_currencymaster.currencyID IN (' . $currency . ')';
        }

        $where = 'srp_employeesdetails.isPayrollEmployee = 1 AND isDischargedStatus != 1 AND  EIdNo NOT IN (
                        SELECT  empID FROM ' . $payrollMaster . ' AS payMaster
                        JOIN ' . $headerDetailTB . ' AS payDet ON payDet.payrollMasterID = payMaster.payrollMasterID AND payDet.companyID=' . $companyID . '
                        WHERE payMaster.companyID = ' . $companyID . ' AND payrollYear=' . $payYear . ' AND payrollMonth=' . $payMonth . '
                  ) ' . $segmentFilter . ' ' . $currencyFilter;

        $this->datatables->select('EIdNo, ECode, Ename2 AS empName, DesDescription, CurrencyCode, segmentCode');
        $this->datatables->from('srp_employeesdetails');
        $this->datatables->join(' (SELECT employeeNo FROM ' . $salaryDeclarationTB . ' WHERE companyID=' . $companyID . '
                                    AND payDate<="' . $effectiveDate . '" GROUP BY employeeNo) AS declarationTB',
            'declarationTB.employeeNo=srp_employeesdetails.EIdNo');
        $this->datatables->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID');
        $this->datatables->join('srp_erp_currencymaster', 'srp_employeesdetails.payCurrencyID = srp_erp_currencymaster.currencyID');
        $this->datatables->join('srp_erp_segment', 'srp_employeesdetails.segmentID = srp_erp_segment.segmentID AND companyID=' . $companyID);
        $this->datatables->join('(
                                    SELECT EIdNo AS empID, dischargedDate,
                                        IF( isDischarged != 1, 0,
                                        CASE
                                            WHEN \'' . $effectiveDate . '\' <= DATE_FORMAT(dischargedDate, \'%Y-%m-01\') THEN 0
                                            WHEN \'' . $effectiveDate . '\' > DATE_FORMAT(dischargedDate, \'%Y-%m-01\') THEN 1
                                        END
                                    )AS isDischargedStatus
                                    FROM srp_employeesdetails WHERE Erp_companyID =' . $companyID . '
                                ) AS dischargedStatusTB', ' ON dischargedStatusTB.empID = srp_employeesdetails.EIdNo', 'left');

        $isGroupAccess = getPolicyValues('PAC', 'All');
        if ($isGroupAccess == 1) {
            $currentEmp = current_userID();
            $this->datatables->join("(
                                        SELECT empTB.groupID, employeeID FROM srp_erp_payrollgroupemployees AS empTB
                                        JOIN srp_erp_payrollgroupincharge AS inCharge ON inCharge.groupID=empTB.groupID
                                        WHERE empTB.companyID={$companyID} AND inCharge.companyID={$companyID} AND empID={$currentEmp}
                                    ) AS accTb", 'accTb.employeeID=EIdNo');
        }
        $this->datatables->add_column('addBtn', '$1', 'addBtn()');
        $this->datatables->where('srp_employeesdetails.Erp_companyID', $companyID);
        $this->datatables->where('srp_employeesdetails.empConfirmedYN', 1);
        $this->datatables->where($where);

        echo $this->datatables->generate();
    }

    function delete_PayrollEmp()
    {
        $this->form_validation->set_rules('payrollID', 'Payroll ID', 'trim|required');
        $this->form_validation->set_rules('empID', 'Employee ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->template_paySheet_model->delete_PayrollEmp());
        }
    }

    function update_payrollEmpComment()
    {
        echo json_encode($this->template_paySheet_model->update_payrollEmpComment());
    }

    function payroll_reversing()
    {

    }

    function get_payScale_report()
    {
        $requestType = $this->uri->segment(3);
        $companyID = current_companyID();
        $segment = $this->input->post('segmentID');

        $category = $this->db->query("SELECT salaryCategoryType,salaryCategoryID,salaryDescription,deductionPercntage,companyContributionPercentage FROM srp_erp_pay_salarycategories WHERE companyID='{$companyID}' AND isPayrollCategory=1 order by salaryCategoryType ASC")->result_array();
        $query = '';
        $asofDate = $this->input->post('asofDate');
        if ($category) {
            foreach ($category as $cat) {
                $salaryDescription = str_replace(' ', '', $cat['salaryDescription']);
                $salaryDescription = preg_replace("/[^a-zA-Z 0-9]+/", "", $salaryDescription);

                $query .= "SUM(IF(catTB.salaryCategoryID = " . $cat['salaryCategoryID'] . " , transactionAmount, 0)) as " . $salaryDescription . ",";

                /*if ($cat['salaryCategoryType'] == 'D' || $cat['salaryCategoryType'] == 'DC') {
                    $query .= "SUM(IF(srp_erp_pay_salarydeclartion.salaryCategoryID = " . $cat['salaryCategoryID'] . ", transactionAmount * -1, 0))   as " . $salaryDescription . ",";
                }*/
            }
            $query .= "salDec.companyID";
        }

        if ($query == '') {
            $data['details'] = false;
            $data['currency'] = false;
        } else {
            $filter = '';
            if (!empty($segment)) {
                $commaList = implode(', ', $segment);
                $filter .= "AND srp_employeesdetails.segmentID IN($commaList) ";

                $str = '';
                $isGroupAccess = getPolicyValues('PAC', 'All');
                if ($isGroupAccess == 1) {
                    $currentEmp = current_userID();
                    $str = "JOIN (
                        SELECT groupID FROM srp_erp_payrollgroupincharge
                        WHERE companyID={$companyID} AND empID={$currentEmp}
                    ) AS accTb ON accTb.groupID = salDec.accessGroupID";
                }

                $data['details'] = $this->db->query("SELECT srp_erp_segment.description as segment,ECode, Ename1, Ename2, Ename3, Ename4, employeeNo,
                                                     catTB.salaryCategoryType, DesDescription, transactionCurrency, $query
                                                     FROM srp_erp_pay_salarydeclartion AS salDec
                                                     LEFT JOIN srp_erp_pay_salarycategories AS catTB ON salDec.salaryCategoryID = catTB.salaryCategoryID
                                                     AND catTB.companyID = {$companyID}
                                                     LEFT JOIN srp_employeesdetails ON employeeNo = EidNo
                                                     LEFT JOIN srp_designation ON DesignationID = EmpDesignationId
                                                     LEFT JOIN srp_erp_segment on srp_erp_segment.segmentID=srp_employeesdetails.segmentID
                                                     AND srp_erp_segment.companyID=salDec.companyID
                                                     {$str}
                                                     WHERE salDec.companyID='{$companyID}' AND effectiveDate < '{$asofDate}' AND isPayrollEmployee=1
                                                     $filter GROUP BY employeeNo, transactionCurrency")->result_array();

                $data['currency'] = $this->db->query("SELECT transactionCurrency as currency, $query
                                                      FROM srp_erp_pay_salarydeclartion AS salDec
                                                      LEFT JOIN srp_erp_pay_salarycategories AS catTB ON salDec.salaryCategoryID = catTB.salaryCategoryID
                                                      AND catTB.companyID={$companyID}
                                                      LEFT JOIN srp_employeesdetails ON employeeNo = EidNo
                                                      LEFT JOIN srp_designation ON DesignationID = EmpDesignationId
                                                      LEFT JOIN srp_erp_segment on srp_erp_segment.segmentID=srp_employeesdetails.segmentID
                                                      AND srp_erp_segment.companyID=salDec.companyID
                                                      {$str}
                                                      WHERE salDec.companyID = '{$companyID}' AND effectiveDate < '{$asofDate}' AND isPayrollEmployee=1
                                                      $filter GROUP BY transactionCurrency ")->result_array();

            } else {
                $data['details'] = false;
                $data['currency'] = false;
            }


        }
        $data['asofDate'] = $asofDate;
        $data['category'] = $category;
        $data['segment'] = $segment;

        if ($requestType == 'pdf') {
            $html = $this->load->view('system/hrm/ajax/load-employee-payscale-report_pdf.php', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        } else {
            echo $html = $this->load->view('system/hrm/ajax/load-employee-payscale-report.php', $data, true);
        }

    }

    function get_payScale_report_pdf()
    {
        $companyID = current_companyID();
        $segment = $this->input->post('segmentID');
        $category = $this->db->query("SELECT salaryCategoryType,salaryCategoryID,salaryDescription,deductionPercntage,companyContributionPercentage FROM srp_erp_pay_salarycategories WHERE companyID='{$companyID}' AND isPayrollCategory=1  order by salaryCategoryType ASC")->result_array();
        $query = '';
        $asofDate = $this->input->post('asofDate');
        if ($category) {
            foreach ($category as $cat) {
                $salaryDescription = str_replace(' ', '', $cat['salaryDescription']);
                $salaryDescription = preg_replace("/[^a-zA-Z 0-9]+/", "", $salaryDescription);
                /*   if ($cat['salaryCategoryType'] == 'A') {*/
                $query .= "SUM(IF(srp_erp_pay_salarydeclartion.salaryCategoryID = " . $cat['salaryCategoryID'] . " , transactionAmount, 0)) as " . $salaryDescription . ",";
                /*  }*/
                /*  if ($cat['salaryCategoryType'] == 'D' || $cat['salaryCategoryType'] == 'DC') {
                      $query .= "SUM(IF(srp_erp_pay_salarydeclartion.salaryCategoryID = " . $cat['salaryCategoryID'] . ", transactionAmount * -1, 0))   as " . $salaryDescription . ",";
                  }*/
            }
            $query .= "srp_erp_pay_salarydeclartion.companyID";
        }

        if ($query == '') {
            $data['details'] = false;
            $data['currency'] = false;
        } else {
            $filter = '';
            if (!empty($segment)) {
                $commaList = implode(', ', $segment);
                $filter .= "AND srp_employeesdetails.segmentID IN($commaList)";

                $data['details'] = $this->db->query("SELECT srp_erp_segment.description as segment,ECode, Ename1, Ename2, Ename3, Ename4, employeeNo, srp_erp_pay_salarycategories.salaryCategoryType, DesDescription, transactionCurrency, $query  FROM srp_erp_pay_salarydeclartion LEFT JOIN srp_erp_pay_salarycategories ON srp_erp_pay_salarydeclartion.salaryCategoryID = srp_erp_pay_salarycategories.salaryCategoryID AND srp_erp_pay_salarydeclartion.companyID = srp_erp_pay_salarycategories.companyID LEFT JOIN srp_employeesdetails ON employeeNo = EidNo LEFT JOIN srp_designation ON DesignationID = EmpDesignationId LEFT JOIN srp_erp_segment on srp_erp_segment.segmentID=srp_employeesdetails.segmentID AND srp_erp_segment.companyID=srp_erp_pay_salarydeclartion.companyID WHERE srp_erp_pay_salarydeclartion.companyID = '{$companyID}' AND effectiveDate < '{$asofDate}' $filter GROUP BY employeeNo , transactionCurrency ")->result_array();
                $data['currency'] = $this->db->query("SELECT transactionCurrency as currency,$query  FROM srp_erp_pay_salarydeclartion LEFT JOIN srp_erp_pay_salarycategories ON srp_erp_pay_salarydeclartion.salaryCategoryID = srp_erp_pay_salarycategories.salaryCategoryID AND srp_erp_pay_salarydeclartion.companyID = srp_erp_pay_salarycategories.companyID LEFT JOIN srp_employeesdetails ON employeeNo = EidNo LEFT JOIN srp_designation ON DesignationID = EmpDesignationId LEFT JOIN srp_erp_segment on srp_erp_segment.segmentID=srp_employeesdetails.segmentID AND srp_erp_segment.companyID=srp_erp_pay_salarydeclartion.companyID WHERE srp_erp_pay_salarydeclartion.companyID = '{$companyID}' AND effectiveDate < '{$asofDate}' $filter GROUP BY transactionCurrency ")->result_array();
            } else {
                $data['details'] = false;
                $data['currency'] = false;
            }

        }
        $data['category'] = $category;
        $data['asofDate'] = $asofDate;
        $html = $this->load->view('system/hrm/ajax/load-employee-payscale-report_pdf.php', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }

    function pendingExpensesClaims()
    {
        $companyID = current_companyID();
        $payYear = $this->input->post('payYear');
        $payMonth = $this->input->post('payMonth');
        $empList = $this->input->post('selectedEmployees');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $payDateMax = date('Y-m-t', strtotime($payYear . '-' . $payMonth . '-01'));
        $str = '';
        $count = 0;
        if ($isNonPayroll != 'Y') {
            $_pending = $this->db->query("SELECT * FROM (
                                          SELECT claimDet.expenseClaimMasterAutoID AS masterID, expenseClaimCode, empName, empCurrency,
                                          FORMAT(SUM(empCurrencyAmount), empCurrencyDecimalPlaces) AS empAmnt, DATE_FORMAT(expenseClaimDate,'%Y-%m-01') AS firstDate
                                          FROM  srp_erp_expenseclaimmaster AS claimMaster
                                          JOIN srp_erp_expenseclaimdetails AS claimDet ON claimDet.expenseClaimMasterAutoID=claimMaster.expenseClaimMasterAutoID
                                          JOIN srp_erp_expenseclaimcategories AS expCat ON expCat.expenseClaimCategoriesAutoID = claimDet.expenseClaimCategoriesAutoID
                                          AND expCat.companyID = {$companyID}
                                          JOIN (
                                              SELECT EIdNo AS EmpID, CONCAT(Ecode,'  -  ',Ename2) AS empName
                                              FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                          )AS payHead ON payHead.EmpID = claimMaster.claimedByEmpID
                                          WHERE approvedYN = 1  AND addedToSalary = 0 AND addedForPayment = 0
                                          AND claimMaster.claimedByEmpID IN ({$empList})
                                          GROUP BY claimDet.expenseClaimMasterAutoID
                                      ) AS dataMaster WHERE firstDate <= '{$payDateMax}' ")->result_array();


            if (!empty($_pending)) {
                foreach ($_pending as $key => $row) {
                    $r_ID = $row['masterID'];
                    $str .= '<tr><td>' . ($key + 1) . '</td><td>' . $row['expenseClaimCode'] . '</td><td>' . $row['empName'] . '</td><td>' . $row['empCurrency'] . '</td>';
                    $str .= '<td><div align="right">' . $row['empAmnt'] . '</div></td>';
                    $str .= '<td style="text-align: center;">';
                    $str .= '<input type="checkbox" name="selectedExpenseClaim[]" class="expCls" value="' . $r_ID . '" onclick="checkTotalChecked(\'.expCls\', \'#allCheckBox\')">';
                    $str .= '</td>';
                    $str .= '</tr>';
                }
            }

            $count = count($_pending);
        }

        $updateColumn = ($isNonPayroll == 'Y') ? 'nonPayrollID' : 'payrollID';
        $amountColumn = ($isNonPayroll == 'Y') ? 'noPaynonPayrollAmount' : 'noPayAmount';

        $_pending = $this->db->query("SELECT noPayData.*, CONCAT(Ecode,'  -  ',Ename2) AS empName, CurrencyCode,
                                      FORMAT(amountColumn, DecimalPlaces) AS empAmnt
                                      FROM (
                                          SELECT ID, reviewTB.empID, $amountColumn AS amountColumn, documentCode,
                                          DATE_FORMAT(attendanceDate, '%Y-%m-01') AS firstAttDate
                                          FROM srp_erp_pay_empattendancereview AS reviewTB
                                          JOIN srp_erp_leavemaster AS lMaster ON lMaster.leaveMasterID=reviewTB.leaveMasterID
                                          WHERE reviewTB.companyID='{$companyID}' AND lMaster.companyID='{$companyID}'
                                          AND {$updateColumn} = 0 AND reviewTB.empID IN ({$empList}) AND $amountColumn != 0
                                          AND $amountColumn IS NOT NULL
                                      ) AS noPayData
                                      JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = empID AND Erp_companyID='{$companyID}'
                                      JOIN srp_erp_currencymaster AS curMaster ON curMaster.currencyID=payCurrencyID
                                      WHERE firstAttDate <= '{$payDateMax}' ")->result_array();
        $str_no = '';
        if (!empty($_pending)) {
            foreach ($_pending as $key => $row) {
                $r_ID = $row['ID'];
                $str_no .= '<tr><td>' . ($key + 1) . '</td><td>' . $row['documentCode'] . '</td><td>' . $row['empName'] . '</td><td>' . $row['CurrencyCode'] . '</td>';
                $str_no .= '<td><div align="right">' . $row['empAmnt'] . '</div></td>';
                $str_no .= '<td style="text-align: center;">';
                $str_no .= '<input type="checkbox" name="selectedNoPay[]" class="noPayCls" value="' . $r_ID . '" onclick="checkTotalChecked(\'.noPayCls\', \'#allCheckBox1\')">';
                $str_no .= '</td>';
                $str_no .= '</tr>';
            }
        }

        return ['e', ($count + count($_pending)), 'pendingExpenseClaims', 'expenseClaim' => $str, 'noPay' => $str_no];
    }

    function ssoCal($payrollMasterID = 512, $payDateMin = '2017-09-01')
    {

        /*$am =  $this->db->query("");

          echo '<pre>'; print_r($am); echo '</pre>';die();
          echo $this->db->last_query();
          die();*/

        //|(|#43|+|#44|+|#45|+|#46|+|#49|+|#52|+|#57|+|!0|)||*|_0.07_
        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $createdUserGroup = current_user_group();
        $createdUserName = current_employee();
        $createdDateTime = current_date();
        $salary_categories_arr = salary_categories(array('A', 'D'));

        $ssoData = $this->db->query("SELECT ssoTB.socialInsuranceID, formulaString, expenseGlAutoID, liabilityGlAutoID, masterTB.payGroupID, isSlabApplicable, SlabID
                                     FROM srp_erp_socialinsurancemaster AS ssoTB
                                     JOIN srp_erp_paygroupmaster AS masterTB ON masterTB.socialInsuranceID=ssoTB.socialInsuranceID AND masterTB.companyID={$companyID}
                                     JOIN srp_erp_paygroupformula AS formulaTB ON formulaTB.payGroupID=masterTB.payGroupID AND formulaTB.companyID={$companyID}
                                     JOIN (
                                        SELECT socialInsuranceMasterID AS ssoID FROM srp_erp_socialinsurancedetails WHERE companyID={$companyID}
                                        GROUP BY socialInsuranceMasterID
                                     ) AS ssoDetail ON ssoDetail.ssoID = ssoTB.socialInsuranceID
                                     WHERE ssoTB.companyID={$companyID} AND masterTB.payGroupID=173")->result_array();
        echo $this->db->last_query();
        echo '<pre>';
        print_r($ssoData);
        echo '</pre>';
        foreach ($ssoData as $key => $ssoRow) {

            $isSlabApplicable = trim($ssoRow['isSlabApplicable']);
            $slabID = trim($ssoRow['SlabID']);
            $SSO_ID = trim($ssoRow['socialInsuranceID']);
            $payGroupID = trim($ssoRow['payGroupID']);
            $formula = trim($ssoRow['formulaString']);
            $expenseGL = trim($ssoRow['expenseGlAutoID']);
            $liabilityGL = trim($ssoRow['liabilityGlAutoID']);

            if (!empty($formula) && $formula != null) {
                $getBalancePay = ($isSlabApplicable == 1) ? 'N' : 'Y';
                $formulaBuilder = formulaBuilder_to_sql($ssoRow, $salary_categories_arr, $payDateMin, $payGroupID, $getBalancePay);

                $formulaDecode = $formulaBuilder['formulaDecode'];
                $select_str2 = $formulaBuilder['select_str2'];
                $whereInClause = $formulaBuilder['whereInClause'];

                $select_str2 = (trim($select_str2) == '') ? '' : $select_str2 . ',';


                if ($isSlabApplicable == 1) {
                    $slabData = $this->db->query("SELECT startRangeAmount strAmount, endRangeAmount endAmount, formulaString
                                                  FROM srp_erp_ssoslabmaster AS slabMaster
                                                  JOIN srp_erp_ssoslabdetails AS slabDet ON slabMaster.ssoSlabMasterID = slabDet.ssoSlabMasterID
                                                  AND slabDet.companyID={$companyID}
                                                  WHERE slabMaster.companyID={$companyID} AND slabMaster.ssoSlabMasterID={$slabID}")->result_array();


                    if (!empty($slabData)) {
                        foreach ($slabData as $keySlab => $slabRow) {
                            $formulaBuilder_slab = formulaBuilder_to_sql($slabRow, $salary_categories_arr, $payDateMin, $payGroupID);
                            $formulaDecode_slab = $formulaBuilder_slab['formulaDecode'];
                            $select_str_slab = $formulaBuilder_slab['select_str2'];
                            $whereInClause_slab = $formulaBuilder_slab['whereInClause'];

                            $strAmount = $slabRow['strAmount'];
                            $endAmount = $slabRow['endAmount'];

                            $this->db->query("INSERT INTO srp_erp_payrolldetail2 ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, detailType, GLCode, liabilityGL,
                                              transactionAmount, transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces,
                                              companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces,
                                              companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingER,
                                              companyReportingCurrencyDecimalPlaces, companyID, companyCode, createdPCID, createdUserID, createdUserGroup, createdUserName,
                                              createdDateTime, segmentID, segmentCode)

                                              SELECT {$payrollMasterID}, {$payGroupID}, 'PAY_GROUP', 'PAY_GROUP', calculationTB.empID, 'G', '{$expenseGL}', $liabilityGL,
                                              round( (({$formulaDecode_slab}) * -1 ), trDPlace ) AS trAmount, trCuID, trCu, 1, trDPlace,
                                              round( (({$formulaDecode_slab}) / locCuER) * -1 , locCuDPlace ) AS localAmount, locCuID, locCu, locCuER, locCuDPlace,
                                              round( (({$formulaDecode_slab}) / repCuER) * -1 , repCuDPlace ) AS reportingAmount, repCuID, repCu, repCuER, repCuDPlace,
                                              {$companyID}, '{$companyCode}', '{$createdPCID}', '{$createdUserID}', '{$createdUserGroup}', '{$createdUserName}',
                                              '{$createdDateTime}', seg.segmentID, seg.segmentCode
                                              FROM (
                                                    SELECT payDet.empID, {$select_str_slab},
                                                    transactionCurrencyID AS trCuID, transactionCurrency AS trCu, transactionER AS trER, transactionCurrencyDecimalPlaces
                                                    AS trDPlace, companyLocalCurrencyID AS locCuID , companyLocalCurrency AS locCu, companyLocalER AS locCuER,
                                                    companyLocalCurrencyDecimalPlaces AS locCuDPlace, companyReportingCurrencyID AS repCuID, companyReportingCurrency AS repCu,
                                                    companyReportingER AS repCuER, companyReportingCurrencyDecimalPlaces AS repCuDPlace
                                                    FROM srp_erp_payrolldetail AS payDet
                                                    JOIN srp_erp_socialinsurancedetails AS ssoDet ON ssoDet.empID = payDet.empID AND ssoDet.companyID={$companyID}
                                                    AND socialInsuranceMasterID={$SSO_ID}
                                                    WHERE payDet.companyID = {$companyID} AND payrollMasterID = {$payrollMasterID}
                                                    {$whereInClause_slab}  GROUP BY payDet.empID, salCatID, detailType
                                              ) calculationTB
                                              JOIN (
                                                    SELECT EmpID, segmentID FROM srp_erp_payrollheaderdetails WHERE companyID={$companyID} AND payrollMasterID={$payrollMasterID}
                                              ) AS empTB ON empTB.EmpID=calculationTB.empID
                                              JOIN srp_erp_segment seg ON seg.segmentID = empTB.segmentID AND seg.companyID = {$companyID}
                                              WHERE calculationTB.empID IN (
                                                   SELECT empID FROM (
                                                       SELECT calculationTB.empID, round( ({$formulaDecode}), trDPlace) AS trAmount FROM (
                                                            SELECT payDet.empID, {$select_str2} transactionCurrencyDecimalPlaces AS trDPlace
                                                            FROM srp_erp_payrolldetail AS payDet
                                                            JOIN srp_erp_socialinsurancedetails AS ssoDet ON ssoDet.empID = payDet.empID AND ssoDet.companyID={$companyID}
                                                            AND socialInsuranceMasterID={$SSO_ID}
                                                            WHERE payDet.companyID = {$companyID} AND payrollMasterID = {$payrollMasterID}
                                                            {$whereInClause}  GROUP BY payDet.empID, salCatID, detailType
                                                       ) calculationTB GROUP BY empID
                                                   ) AS currentMonthAmountTB WHERE trAmount > {$strAmount} and trAmount <= {$endAmount}
                                              ) GROUP BY calculationTB.empID");

                        }
                    }

                } else {

                    $this->db->query("INSERT INTO srp_erp_payrolldetail2 ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, detailType, GLCode, liabilityGL,
                                   transactionAmount, transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces,
                                   companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces,
                                   companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces,
                                   companyID, companyCode, createdPCID, createdUserID, createdUserGroup, createdUserName, createdDateTime, segmentID, segmentCode)

                                   SELECT {$payrollMasterID}, {$payGroupID}, 'PAY_GROUP', 'PAY_GROUP', calculationTB.empID, 'G', '{$expenseGL}', $liabilityGL,
                                   round((({$formulaDecode}) * -1 ), transactionCurrencyDecimalPlaces)AS transactionAmount, transactionCurrencyID, transactionCurrency,
                                   transactionER, transactionCurrencyDecimalPlaces,
                                   round( (({$formulaDecode}) / companyLocalER) * -1 , companyLocalCurrencyDecimalPlaces  )AS localAmount,
                                   companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces,
                                   round( (({$formulaDecode}) / companyReportingER) * -1 , companyReportingCurrencyDecimalPlaces  )AS reportingAmount,
                                   companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces,
                                   {$companyID}, '{$companyCode}', '{$createdPCID}', '{$createdUserID}', '{$createdUserGroup}', '{$createdUserName}', '{$createdDateTime}',
                                   seg.segmentID, seg.segmentCode
                                   FROM (
                                        SELECT payDet.empID, fromTB, detailType, salCatID, {$select_str2}
                                        transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces,
                                        companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces,
                                        companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces
                                        FROM srp_erp_payrolldetail AS payDet
                                        JOIN srp_erp_socialinsurancedetails AS ssoDet ON ssoDet.empID = payDet.empID AND ssoDet.companyID={$companyID}
                                        AND socialInsuranceMasterID={$SSO_ID}
                                        WHERE payDet.companyID = {$companyID} AND payrollMasterID = {$payrollMasterID}
                                        {$whereInClause}  GROUP BY payDet.empID, salCatID, detailType
                                   ) calculationTB
                                   JOIN (
                                        SELECT EmpID, segmentID FROM srp_erp_payrollheaderdetails WHERE companyID={$companyID} AND payrollMasterID={$payrollMasterID}
                                   ) AS empTB ON empTB.EmpID=calculationTB.empID
                                   JOIN srp_erp_segment seg ON seg.segmentID = empTB.segmentID AND seg.companyID = {$companyID}
                                   GROUP BY empID");
                }


            }
        }

    }

    function payeeCal()
    {
        //return $this->template_paySheet_model->payeeCal324(4934342, 'N');
        //return $this->template_paySheet_model->payGroup_temporary_calculation(492, 'N');
        $payrollMasterID = 512;
        $isNonPayroll = 'N';
        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $createdUserID = current_userID();
        $salary_categories_arr = salary_categories(array('A', 'D'));
        $payGroup_arr = get_payGroup(1);


        $payGroups = $this->db->query("SELECT temp.payGroupID, formulaString, payGroupCategories
                                       FROM srp_erp_pay_templatefields AS temp
                                       JOIN srp_erp_paygroupformula AS formulaTB ON formulaTB.payGroupID=temp.payGroupID AND formulaTB.companyID={$companyID}
                                       WHERE temp.fieldType = 'G' AND temp.companyID = {$companyID} AND isCalculate =1
                                       and temp.payGroupID=172")->result_array();


        foreach ($payGroups as $key => $payRow) {

            $payGroupID = trim($payRow['payGroupID']);
            $formula = trim($payRow['formulaString']);

            if (!empty($formula) && $formula != null) {
                $formulaBuilder = payGroup_formulaBuilder_to_sql('decode', $payRow, $salary_categories_arr, $payGroup_arr, $payGroupID, null);

                $formulaDecode = $formulaBuilder['formulaDecode'];
                $select_monthlyAD_str = trim($formulaBuilder['select_monthlyAD_str']);
                $select_salCat_str = trim($formulaBuilder['select_salaryCat_str']);
                $select_group_str = trim($formulaBuilder['select_group_str']);
                $whereInClause = trim($formulaBuilder['whereInClause']);
                $where_MA_MD_Clause = $formulaBuilder['where_MA_MD_Clause'];
                $whereInClause_group = trim($formulaBuilder['whereInClause_group']);


                $where_MA_MD_Clause_str = '';
                if (!empty($where_MA_MD_Clause)) {
                    if (count($where_MA_MD_Clause) > 1) {
                        $where_MA_MD_Clause_str = ' calculationTB = \'' . $where_MA_MD_Clause[0] . '\' OR calculationTB = \'' . $where_MA_MD_Clause[1] . '\'';
                    } else {
                        $where_MA_MD_Clause_str = ' calculationTB = \'' . $where_MA_MD_Clause[0] . '\'';
                    }
                }


                if ($select_monthlyAD_str != '') {
                    $select_monthlyAD_str .= ',';
                }

                if ($whereInClause != '' && $select_salCat_str != '') {
                    $select_salCat_str .= ',';
                    $whereInClause = 'salCatID IN (' . $whereInClause . ') AND calculationTB = \'SD\'';

                }

                if ($whereInClause_group != '' && $select_group_str != '') {
                    $select_group_str .= ',';
                    $whereInClause_group = 'detailTBID IN (' . $whereInClause_group . ') AND fromTB = \'PAY_GROUP\'';
                }


                if ($whereInClause != '' && $whereInClause_group != '') {
                    $whereIN = $whereInClause . ' OR ' . $whereInClause_group;
                } else {
                    $whereIN = $whereInClause . ' ' . $whereInClause_group;
                }

                if (trim($whereIN) == '') {
                    $whereIN = (trim($where_MA_MD_Clause_str) == '') ? '' : 'AND (' . $where_MA_MD_Clause_str . ' )';
                } else {
                    $MA_MD_Clause_str_join = (trim($where_MA_MD_Clause_str) == '') ? '' : ' OR ' . $where_MA_MD_Clause_str;
                    $whereIN = 'AND (' . $whereIN . ' ' . $MA_MD_Clause_str_join . ')';
                }


                $detailTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrolldetail' : 'srp_erp_payrolldetail';


                $this->db->query("SELECT {$payrollMasterID}, {$payGroupID}, 'PAY_GROUP', calculationTB.empID, ' ',transactionCurrencyID, transactionCurrency,
                                  transactionCurrencyDecimalPlaces, round((" . $formulaDecode . "), transactionCurrencyDecimalPlaces) AS transactionAmount,
                                  segmentID, segmentCode, {$companyID}, '{$companyCode}', '{$createdUserID}'
                                  FROM (
                                        SELECT payDet.empID, fromTB, detailType, salCatID, " . $select_salCat_str . " " . $select_group_str . " " . $select_monthlyAD_str . "
                                        transactionCurrencyID, transactionCurrency, transactionCurrencyDecimalPlaces, srp_erp_segment.segmentID, srp_erp_segment.segmentCode
                                        FROM {$detailTB} AS payDet
                                        JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = payDet.empID AND empTB.Erp_companyID={$companyID}
                                        JOIN srp_erp_segment ON srp_erp_segment.segmentID = empTB.segmentID AND srp_erp_segment.companyID = {$companyID}
                                        WHERE payDet.companyID = {$companyID} AND payrollMasterID = {$payrollMasterID} {$whereIN}
                                        GROUP BY payDet.empID, salCatID, payDet.fromTB, detailTBID
                                  ) calculationTB
                                  GROUP BY empID ");

                echo $this->db->last_query();
            }
        }
    }

    function decode_formula_categories()
    {

        $payGroups = $this->db->query("SELECT formulaString, formulaID FROM srp_erp_paygroupformula AS formulaTB ")->result_array();


        foreach ($payGroups as $key => $payRow) {

            $formulaID = trim($payRow['formulaID']);
            $formula = trim($payRow['formulaString']);

            if (!empty($formula) && $formula != null) {

                $salaryCategories = '';
                $ssoCategories = '';
                $payGroupCategories = '';

                $formula = (is_array($payRow)) ? trim($payRow['formulaString']) : $payRow;
                $operand_arr = operand_arr();


                $formula_arr = explode('|', $formula); // break the formula


                foreach ($formula_arr as $formula_row) {

                    if (trim($formula_row) != '') {
                        if (in_array($formula_row, $operand_arr)) { //validate is a operand

                        } else {

                            $elementType = $formula_row[0];

                            if ($elementType == '@') {
                                /*** SSO ***/
                                $SSO_Arr = explode('@', $formula_row);
                                $ssoCategories .= ($ssoCategories == '') ? $SSO_Arr[1] : ',' . $SSO_Arr[1];

                            } else if ($elementType == '#') {
                                /*** Salary category ***/
                                $catArr = explode('#', $formula_row);
                                $salaryCategories .= ($salaryCategories == '') ? $catArr[1] : ',' . $catArr[1];

                            } else if ($elementType == '~') {
                                /*** Pay Group ***/
                                $SSO_Arr = explode('~', $formula_row);
                                $payGroupCategories .= ($payGroupCategories == '') ? $SSO_Arr[1] : ',' . $SSO_Arr[1];

                            }

                        }
                    }

                }


                echo "<br/>formulaID: " . trim($formulaID);
                echo "<br/>salaryCategories: $salaryCategories";
                echo "<br/>ssoCategories: $ssoCategories";
                echo "<br/>payGroupCategories: $payGroupCategories";

                echo "<br/><br/><br/><br/><br/>";

                $dataUp = [
                    'salaryCategories' => (trim($salaryCategories) == '') ? null : $salaryCategories,
                    'ssoCategories' => (trim($ssoCategories) == '') ? null : $ssoCategories,
                    'payGroupCategories' => (trim($payGroupCategories) == '') ? null : $payGroupCategories
                ];

                $this->db->where('formulaID =' . $formulaID);
                $this->db->update('srp_erp_paygroupformula', $dataUp);

            }
        }
    }

    function get_localization_report()
    {
        $this->form_validation->set_rules('segmentID[]', 'Segment', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $data["details"] = $this->template_paySheet_model->get_localization();
            $data["type"] = "html";
            echo $html = $this->load->view('system/hrm/ajax/load-localization-report', $data, true);
        }
    }

    function get_localization_report_pdf()
    {
        $data["details"] = $this->template_paySheet_model->get_localization();
        $data["type"] = "pdf";
        $html = $this->load->view('system/hrm/ajax/load-localization-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }

    function get_salary_trend_report()
    {
        $this->form_validation->set_rules('year[]', 'Year', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $data["details"] = $this->template_paySheet_model->get_salary_trend();
            $data["type"] = "html";
            $data["months"] = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dece');
            echo $html = $this->load->view('system/hrm/ajax/load-salary-trend-report', $data, true);
        }
    }

    function get_salary_trend_report_pdf()
    {
        $data["details"] = $this->template_paySheet_model->get_salary_trend();
        $data["type"] = "pdf";
        $data["months"] = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dece');
        $html = $this->load->view('system/hrm/ajax/load-salary-trend-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }

    function payGroup_temporary_calculation()
    {
        $id = trim($this->uri->segment(3));

        if (empty($id)) {
            die('Payroll id is not valid');
        }

        $this->db->trans_start();

        $where = [
            'companyID' => current_companyID(),
            'payrollMasterID' => $id
        ];

        $this->db->delete('srp_erp_payrolldetailpaygroup', $where);

        $this->template_paySheet_model->payGroup_temporary_calculation($id, 'N', '');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            echo 'Updated successfully.';
        } else {
            $this->db->trans_rollback();
            echo 'Error in process.';
        }
    }

    function dropdown_payslipemployees_his_report()
    {
        $segment = $this->input->post('segmentID');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $companyID = current_companyID();
        //$seg = explode('|', $segment);
        if(empty($segment)){
            $html = '<select name="empID[]" id="empID" class="form-control" multiple="multiple"  required>';

            $html .= '</select>';

            echo json_encode(['s', $html]);
        }else{
            $empArr = $this->db->query("SELECT
	EIdNo,
	ECode,
	Ename2
FROM
	srp_employeesdetails
WHERE
	 segmentID IN (".join(',',$segment).") AND Erp_companyID=$companyID AND isSystemAdmin=0")->result_array();

            $html = '<select name="empID[]" id="empID" class="form-control" multiple="multiple"  required>';

            if ($empArr) {
                foreach ($empArr as $empID) {
                    $html .= '<option value="' . $empID['EIdNo'] . '">' . $empID['ECode'] . '|' . $empID['Ename2'] . '</option>';
                }
            }
            $html .= '</select>';

            echo json_encode(['s', $html]);
        }

    }


    function get_leave_history_report()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date To is required
            </div>';
        }  else {
            $this->form_validation->set_rules('empID[]', 'Employee', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
            } else {
                $data["details"] = $this->template_paySheet_model->get_leave_history_report();
                $data["type"] = "html";
                echo $html = $this->load->view('system/hrm/report/load-employee-leave-history-report', $data, true);
            }
        }
    }

    function get_leave_history_report_pdf()
    {
        $data["details"] = $this->template_paySheet_model->get_leave_history_report();
        $data["type"] = "pdf";
        $html = $this->load->view('system/hrm/report/load-employee-leave-history-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }

    function load_payment_voucher(){
        $bankTransferID=$this->input->post('bankTransferID');

        $data['extra'] = $this->template_paySheet_model->load_payment_voucher($bankTransferID);
        $html = $this->load->view('system/hrm/ajax/ajax-erp_load_payment_vouchers', $data, true);
        echo $html;
    }
}


