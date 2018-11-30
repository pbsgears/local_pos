<?php
/*
-- =============================================
-- File Name : Reversing_modal.php
-- Project Name : SME ERP
-- Module Name : Email
-- Author : Nuski Mohamed
-- Create date : 6 - Feb 2017
-- Description : 

-- REVISION HISTORY
-- =============================================*/
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Reversing_modal extends ERP_Model{

    function __contruct()
    {
        parent::__contruct();
    }

    function reversing_approval_document(){
        $auto_id        = trim($this->input->post('auto_id'));
        $date           = trim('Y-m-d');
        $document_id    = trim($this->input->post('document_id'));
        $document_code  = trim($this->input->post('document_code'));
        $comments       = trim($this->input->post('comments'));
        $company_id     = $this->common_data['company_data']['company_id'];

        if($document_code=='PV'){
            $companyID=current_companyID();
            $rrvrID = $this->db->query("SELECT
	payVoucherAutoId
FROM
	`srp_erp_paymentvouchermaster`
WHERE
	`companyID` = '$companyID'
	AND `payVoucherAutoId` = '$document_id'
	AND rrvrID !='' ")->row_array();

            $bankTransferID = $this->db->query("SELECT
	payVoucherAutoId
FROM
	`srp_erp_paymentvouchermaster`
WHERE
	`companyID` = '$companyID'
	AND `payVoucherAutoId` = '$document_id'
	AND bankTransferID !='' ")->row_array();

            if(!empty($rrvrID)){
                $this->session->set_flashdata('w', 'This document can not be reversed it has been generated from receipt reversal  ');
                return array('status' => false);
                exit;
            }

            if(!empty($bankTransferID)){
                $this->session->set_flashdata('w', 'This document can not be reversed it has been generated from bank transfer');
                return array('status' => false);
                exit;
            }


        }

        if($document_code=='SO' || $document_code=='QUT' || $document_code=='CNT'){
            $this->db->select('contractAutoID,invoiceAutoID');
            $this->db->from('srp_erp_customerinvoicedetails');
            $this->db->where('contractAutoID', $document_id);
            $this->db->where('companyID', current_companyID());
            $result=$this->db->get()->row_array();
            if($result){
                $this->db->select('invoiceCode');
                $this->db->from('srp_erp_customerinvoicemaster');
                $this->db->where('invoiceAutoID', $result['invoiceAutoID']);
                $invoiceCode=$this->db->get()->row_array();
                $this->session->set_flashdata('w', 'Document has been used in following invoice'.$invoiceCode['invoiceCode']);
                return array('status' => false);
                exit;
            }
        }

        $this->db->select('documentID, table_name, table_unique_field_name');
        $this->db->from('srp_erp_documentapproved');
        $this->db->where('documentApprovedID', $auto_id);
        $approved_data = $this->db->get()->row_array();
        if (empty($approved_data)) {
            $this->session->set_flashdata('w', ' Update Failed ');
            return array('status' => false);
        }
        $document_status = $this->current_document_status($document_code,$document_id);
        if ($document_status['status']=='A') {
            //$this->session->set_flashdata('w', ' Update Failed ');
            return array('status' => false,'data' => $document_status);
        }
        $this->db->trans_start();
        $ledger_qty = 0;
        $this->db->select('itemAutoID,transactionQTY,convertionRate,wareHouseAutoID,companyLocalAmount,documentID');
        $this->db->from('srp_erp_itemledger');
        $this->db->where('documentAutoID', $document_id);
        $this->db->where('documentID', $document_code);
        $this->db->where('companyID', $company_id);
        $item_ledger_data = $this->db->get()->result_array();

        if (!empty($item_ledger_data)) {
            foreach ($item_ledger_data as $value) {
                $ledger_qty = ($value['transactionQTY']/$value['convertionRate']); 
                //if (trim($value['documentID'])=='CINV' or trim($value['documentID'])=='RV' or trim($value['documentID'])=='MI') {
                    //$this->reversing_wac_calculation($value['itemAutoID'],$ledger_qty,$value['companyLocalAmount'],$value['wareHouseAutoID'],0);
                //}else{
                    $this->reversing_wac_calculation($value['itemAutoID'],$ledger_qty,$value['companyLocalAmount'],$value['wareHouseAutoID'],1);
                //}  
            }
        }

        $this->db->delete('srp_erp_itemledger', array('documentAutoID' => $document_id,'documentID' => $document_code,'companyID'=>$company_id));
        $this->db->delete('srp_erp_generalledger', array('documentMasterAutoID' => $document_id,'documentCode' => $document_code,'companyID'=>$company_id));
        $this->db->where($approved_data['table_unique_field_name'], $document_id);
        $this->db->update($approved_data['table_name'], array('confirmedYN' => 0,'approvedYN' => 0,'confirmedByEmpID' => null,'approvedbyEmpID' => null,'confirmedByName' => null,'approvedbyEmpName' => null,'confirmedDate' => null,'approvedDate' => null,'currentLevelNo' => 1));
        $this->db->delete('srp_erp_documentapproved', array('documentSystemCode' => $document_id,'documentID' => $document_code,'companyID'=>$company_id));

        $this->db->delete('srp_erp_bankledger', array('documentMasterAutoID' => $document_id,'documentType' => $document_code,'companyID'=>$company_id));
        if ($document_code=='GRV') {
            $this->db->delete('srp_erp_match_supplierinvoice',array('grvAutoID'=>$document_id,'companyID'=>$company_id)); 
        }      

        $data_reversing['documentMasterAutoID'] = $document_id;
        $data_reversing['documentID']           = $document_code;
        $data_reversing['reversedDate']         = date('Y-m-d');
        $data_reversing['reversedEmpID']        = $this->common_data['current_userID'];
        $data_reversing['reversedEmployee']     = $this->common_data['current_user'];
        $data_reversing['comments']             = $comments;
        $data_reversing['companyID']            = $company_id;
        $this->db->insert('srp_erp_documentapprovedReversing', $data_reversing);   

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', ' Update Failed ');
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s',' Updated Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }


    }

    function reversing_wac_calculation($itemAutoID,$defoult_qty,$total_value=0,$wareHouseID=0,$is_minimum=0){
        $CI =& get_instance();
        $com_currency   = $CI->common_data['company_data']['company_default_currency'];
        $com_currDPlace = $CI->common_data['company_data']['company_default_decimal'];
        $rep_currency   = $CI->common_data['company_data']['company_reporting_currency'];
        $rep_currDPlace = $CI->common_data['company_data']['company_reporting_decimal'];

        $item_current_data = $CI->db->select('itemSystemCode,currentStock,defaultUnitOfMeasure,companyLocalWacAmount as current_wac')->from('srp_erp_itemmaster')->where('itemAutoID', $itemAutoID)->get()->row();

        if ($is_minimum == 1) {
            $defoult_qty    *= -1;
            $total_value    *= -1;
            $document_total = $total_value;// * $defoult_qty;
        } else {
            $document_total = $total_value; //* $defoult_qty;//$item_current_data->current_wac * $defoult_qty;
        }

        $newQty = $item_current_data->currentStock + $defoult_qty;
        $currentTot = $item_current_data->current_wac * $item_current_data->currentStock;
        $newTot = $currentTot + $document_total;
        $newWac = round(($newTot / $newQty), $com_currDPlace);
        $reportConversion = currency_conversion($com_currency,$rep_currency,$newWac);
        $reportConversionRate = $reportConversion['conversion'];
        $repWac = round(($newWac / $reportConversionRate), $rep_currDPlace);

        $data = array('currentStock'=>$newQty,'companyLocalWacAmount'=>$newWac,'companyReportingWacAmount'=>$repWac);
        $where = array('itemAutoID' => $itemAutoID,'companyID' => current_companyID());
        $CI->db->where($where)->update('srp_erp_itemmaster', $data);

        if (isset($wareHouseID)) {
            $CI->db->query("UPDATE srp_erp_warehouseitems SET currentStock=(currentStock+{$defoult_qty}) WHERE itemAutoID={$itemAutoID} AND wareHouseAutoID={$wareHouseID}");
        }
        return true;
    }

    function current_document_status($document_code,$document_id){
        $document = array();
        if ($document_code=='PO') {
            $this->db->select('srp_erp_grvmaster.grvAutoID as auto_id, srp_erp_grvmaster.grvPrimaryCode as system_code');
            $this->db->group_by("srp_erp_grvmaster.grvAutoID"); 
            $this->db->from('srp_erp_grvdetails');
            $this->db->join('srp_erp_grvmaster', 'srp_erp_grvmaster.grvAutoID = srp_erp_grvdetails.grvAutoID');
            $this->db->where('purchaseOrderMastertID', $document_id); 
            $rev_data_arr = $this->db->get()->result_array();
        }elseif ($document_code=='GRV') {
            $this->db->select('srp_erp_paysupplierinvoicemaster.InvoiceAutoID as auto_id,bookingInvCode as system_code');
            $this->db->group_by("srp_erp_paysupplierinvoicemaster.InvoiceAutoID"); 
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->join('srp_erp_paysupplierinvoicemaster','srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paysupplierinvoicedetail.InvoiceAutoID');
            $this->db->where('grvAutoID', $document_id); 
            $rev_data_arr = $this->db->get()->result_array();

            $this->db->select('srp_erp_stockreturnmaster.stockReturnAutoID as auto_id,stockReturnCode as system_code');
            $this->db->group_by("srp_erp_stockreturnmaster.stockReturnAutoID"); 
            $this->db->from('srp_erp_stockreturndetails');
            $this->db->join('srp_erp_stockreturnmaster','srp_erp_stockreturnmaster.stockReturnAutoID = srp_erp_stockreturndetails.stockReturnAutoID');
            $this->db->where('type','GRV'); 
            $this->db->where('grvAutoID', $document_id); 
            $rev_data_arr += $this->db->get()->result_array();

        }elseif ($document_code=='BSI') {
            $this->db->select('srp_erp_paymentvouchermaster.payVoucherAutoId as auto_id, srp_erp_paymentvouchermaster.PVcode as system_code');
            $this->db->group_by("srp_erp_paymentvouchermaster.payVoucherAutoId"); 
            $this->db->from('srp_erp_paymentvoucherdetail');
            $this->db->join('srp_erp_paymentvouchermaster', 'srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentvoucherdetail.payVoucherAutoId');
            $this->db->where('InvoiceAutoID', $document_id); 
            $rev_data_arr = $this->db->get()->result_array();
            $this->db->select('srp_erp_debitnotemaster.debitNoteMasterAutoID as auto_id, srp_erp_debitnotemaster.debitNoteCode as system_code');
            $this->db->group_by("srp_erp_debitnotemaster.debitNoteMasterAutoID");
            $this->db->from('srp_erp_debitnotedetail');
            $this->db->join('srp_erp_debitnotemaster', 'srp_erp_debitnotemaster.debitNoteMasterAutoID = srp_erp_debitnotedetail.debitNoteMasterAutoID');
            $this->db->where('InvoiceAutoID', $document_id); 
            $rev_data_arr += $this->db->get()->result_array();
        }elseif ($document_code=='PV') {
            $this->db->select('bankLedgerAutoID as auto_id, srp_erp_bankrecmaster.bankRecPrimaryCode as system_code');
            $this->db->group_by("bankLedgerAutoID");
            $this->db->from('srp_erp_bankledger');
            $this->db->where('documentType','PV'); 
            $this->db->where('clearedYN',1); 
            $this->db->join('srp_erp_bankrecmaster','srp_erp_bankrecmaster.bankRecAutoID=srp_erp_bankledger.bankRecMonthID');
            $this->db->where('documentMasterAutoID', $document_id); 
            $rev_data_arr = $this->db->get()->result_array();
        }elseif ($document_code=='RV') {
            $this->db->select('bankLedgerAutoID as auto_id, srp_erp_bankrecmaster.bankRecPrimaryCode as system_code');
            $this->db->group_by("bankLedgerAutoID");
            $this->db->from('srp_erp_bankledger');
            $this->db->where('documentType','RV');
            $this->db->where('clearedYN',1);  
            $this->db->join('srp_erp_bankrecmaster','srp_erp_bankrecmaster.bankRecAutoID =srp_erp_bankledger.bankRecMonthID');
            $this->db->where('documentMasterAutoID', $document_id); 
            $rev_data_arr = $this->db->get()->result_array();
        }elseif ($document_code=='CINV') {
            $this->db->select('srp_erp_customerreceiptmaster.receiptVoucherAutoId as auto_id, srp_erp_customerreceiptmaster.RVcode as system_code');
            $this->db->group_by("srp_erp_customerreceiptmaster.receiptVoucherAutoId");
            $this->db->from('srp_erp_customerreceiptdetail');
            $this->db->join('srp_erp_customerreceiptmaster','srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId');
            $this->db->where('invoiceAutoID', $document_id); 
            $rev_data_arr = $this->db->get()->result_array();

            $this->db->select('srp_erp_creditnotemaster.creditNoteMasterAutoID as auto_id, srp_erp_creditnotemaster.creditNoteCode as system_code');
            $this->db->group_by("srp_erp_creditnotemaster.creditNoteMasterAutoID");
            $this->db->from('srp_erp_creditnotedetail');
            $this->db->join('srp_erp_creditnotemaster', 'srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID');
            $this->db->where('invoiceAutoID', $document_id); 
            $rev_data_arr += $this->db->get()->result_array();
        }
        if (empty($rev_data_arr)) {
            return array('status' => 'B');
        }
        return array('status' => 'A','data' =>$rev_data_arr);
    }

    function reversing_approval_HRDocument(){
        $auto_id        = trim($this->input->post('auto_id'));
        $document_id    = trim($this->input->post('document_id'));
        $document_code  = trim($this->input->post('document_code'));
        $comments       = trim($this->input->post('comments'));
        $companyID      = current_companyID();

        $document_code = $this->input->post('document_code');
        //$HR_documentCodes = array('SP', 'SD', 'NSP', 'BTL');
        $HR_documentCodes = array('SP', 'SD', 'SPN');
        if(in_array($document_code, $HR_documentCodes)) {
            $this->db->select('documentID, table_name, table_unique_field_name');
            $this->db->from('srp_erp_documentapproved');
            $this->db->where('documentApprovedID', $auto_id);
            $this->db->where('companyID', $companyID);
            $masterData = $this->db->get()->row_array();
            if (empty($masterData)) {
                $this->session->set_flashdata('w', ' Update Failed');
                return array('status' => false);
            }

            if ($document_code == 'SP' OR $document_code == 'SPN') {
                $this->db->trans_complete();

                $transferMaster = ($document_code == 'SPN') ? 'srp_erp_pay_non_banktransfermaster' : 'srp_erp_pay_banktransfermaster';
                $transferDetail = ($document_code == 'SPN') ? 'srp_erp_pay_non_banktransfer' : 'srp_erp_pay_banktransfer';
                $payrollMaster = ($document_code == 'SPN') ? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';

                $where = ['companyID'=> $companyID, 'payrollMasterID' => $document_id];
                $this->db->delete($transferMaster, $where);
                $this->db->delete($transferDetail, $where);
                $this->db->delete('srp_erp_generalledger', ['documentMasterAutoID' => $document_id, 'documentCode' => $document_code, 'companyID' => $companyID]);

                $data = array(
                    'isBankTransferProcessed' => 0,
                    'confirmedYN' => 0,
                    'approvedYN' => 0,
                    'confirmedByEmpID' => null,
                    'approvedbyEmpID' => null,
                    'confirmedByName' => null,
                    'approvedbyEmpName' => null,
                    'confirmedDate' => null,
                    'approvedDate' => null,
                    'currentLevelNo' => 1
                );

                $this->db->where($where)->update($payrollMaster, $data);

                $this->processComplete();

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', ' Update Failed ');
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', ' Updated Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true);
                }
            }
            elseif ($document_code == 'SD') {
                /** verify salary declaration for payroll / Non Payroll**/
                $salaryDecType = $this->db->query("SELECT isPayrollCategory FROM srp_erp_salarydeclarationmaster
                                                   WHERE salarydeclarationMasterID={$document_id} AND companyID={$companyID}")->row('isPayrollCategory');

                $payrollMaster = ($salaryDecType == 2)? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';
                $payrollDet = ($salaryDecType == 2)? 'srp_erp_non_payrolldetail' : 'srp_erp_payrolldetail';
                $salaryDeclaration = ($salaryDecType == 2)? 'srp_erp_non_pay_salarydeclartion' : 'srp_erp_pay_salarydeclartion';


                /** Check there are payroll processed with this salary declaration **/
                $processedPayroll = $this->db->query("SELECT payrollMaster.documentCode AS system_code
                                                      FROM {$salaryDeclaration} AS declarationDet
                                                      JOIN {$payrollDet} AS payrollDet ON payrollDet.detailTBID = declarationDet.id
                                                      AND payrollDet.companyID={$companyID} AND sdMasterID={$document_id} AND fromTB='SD'
                                                      JOIN {$payrollMaster} AS payrollMaster ON payrollMaster.payrollMasterID = payrollDet.payrollMasterID
                                                      WHERE declarationDet.companyID={$companyID}
                                                      GROUP BY payrollMaster.payrollMasterID")->result_array();


                if( empty($processedPayroll) ){
                    $this->db->trans_start();

                    $this->db->delete($salaryDeclaration, ['companyID'=>$companyID, 'sdMasterID'=>$document_id] );

                    $data = array(
                        'confirmedYN' => 0,
                        'approvedYN' => 0,
                        'confirmedByEmpID' => null,
                        'approvedbyEmpID' => null,
                        'confirmedByName' => null,
                        'approvedbyEmpName' => null,
                        'confirmedDate' => null,
                        'approvedDate' => null,
                        'currentLevelNo' => 1
                    );

                    $this->db->where(['companyID'=>$companyID, 'salarydeclarationMasterID'=>$document_id])->update('srp_erp_salarydeclarationmaster', $data);

                    $this->processComplete();

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->session->set_flashdata('e', ' Update Failed ');
                        $this->db->trans_rollback();
                        return array('status' => false);
                    } else {
                        $this->session->set_flashdata('s', ' Updated Successfully.');
                        $this->db->trans_commit();
                        return array('status' => true);
                    }
                }
                else{
                    $msg = 'Following payrolls are processed for this salary declaration<br/>';
                    $msg .= implode('<br/>', array_column($processedPayroll, 'system_code'));
                    $this->session->set_flashdata('e', $msg);
                    return array('status' => false);
                }

            }

        }
        else{
            $this->session->set_flashdata('w', ' In valid document code.');
            return array('status' => false);
        }


    }

    function processComplete(){

        $document_id    = trim($this->input->post('document_id'));
        $document_code  = trim($this->input->post('document_code'));
        $comments       = trim($this->input->post('comments'));
        $companyID      = current_companyID();

        $this->db->delete('srp_erp_documentapproved', array('documentSystemCode' => $document_id, 'documentID' => $document_code, 'companyID' => $companyID));

        $data_reversing['documentMasterAutoID'] = $document_id;
        $data_reversing['documentID']           = $document_code;
        $data_reversing['reversedDate']         = date('Y-m-d');
        $data_reversing['reversedEmpID']        = current_userID();
        $data_reversing['reversedEmployee']     = current_employee();
        $data_reversing['comments']             = $comments;
        $data_reversing['companyID']            = $companyID;

        $this->db->insert('srp_erp_documentapprovedReversing', $data_reversing);

    }


}