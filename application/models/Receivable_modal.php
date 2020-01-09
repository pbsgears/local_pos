<?php
class Receivable_modal extends ERP_Model
{
    function fetch_credit_note_template_data($creditNoteMasterAutoID)
    {
        $convertFormat=convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(srp_erp_creditnotemaster.creditNoteDate,\''.$convertFormat.'\') AS creditNoteDate,DATE_FORMAT(srp_erp_creditnotemaster.approvedDate,\''.$convertFormat.' %h:%i:%s\') AS approvedDate');
        $this->db->where('creditNoteMasterAutoID', $creditNoteMasterAutoID);
        $this->db->from('srp_erp_creditnotemaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('customerName,customerAddress1,customerTelephone,customerSystemCode,customerFax');
        $this->db->where('customerAutoID',$data['master']['customerID']);
        $this->db->from('srp_erp_customermaster');
        $data['customer'] = $this->db->get()->row_array();

        $this->db->select('GLCode,GLDescription,segmentCode,transactionAmount,companyLocalAmount,customerAmount,description,isFromInvoice');
        // $this->db->group_by("GLCode"); 
        // $this->db->group_by("segmentCode"); 
        $this->db->where('creditNoteMasterAutoID', $creditNoteMasterAutoID);
        $this->db->from('srp_erp_creditnotedetail');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function fetch_customer_data($customerID){
        $this->db->select('*');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $customerID); 
        return $this->db->get()->row_array();
    }

    function save_creditnote_header(){
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $cDate = $this->input->post('cnDate');
        $cnDate = input_format_date($cDate,$date_format_policy);

        //$period          = explode('|', trim($this->input->post('financeyear_period')));
        $currency_code   = explode('|', trim($this->input->post('currency_code')));
        $financeyr       = explode(' - ', trim($this->input->post('companyFinanceYear')));
        $FYBegin = input_format_date($financeyr[0],$date_format_policy);
        $FYEnd = input_format_date($financeyr[1],$date_format_policy);

        $customer_arr                               = $this->fetch_customer_data(trim($this->input->post('customer')));
        $data['documentID']                         = 'CN';
        $data['companyFinanceYearID']               = trim($this->input->post('financeyear'));
        $data['companyFinanceYear']                 = trim($this->input->post('companyFinanceYear'));
        $data['creditNoteDate']                     = trim($cnDate);
        $data['companyFinancePeriodID']                     = trim($this->input->post('financeyear_period'));
        /*$data['FYPeriodDateFrom']                   = trim($period[0]);
        $data['FYPeriodDateTo']                     = trim($period[1]);*/
        $data['customerID']                         = trim($this->input->post('customer'));
        $data['customerCode']                       = $customer_arr['customerSystemCode'];
        $data['customerName']                       = $customer_arr['customerName'];
        $data['customerAddress']                    = $customer_arr['customerAddress1'];
        $data['customerTelephone']                  = $customer_arr['customerTelephone'];
        $data['customerFax']                        = $customer_arr['customerFax'];
        $data['customerReceivableAutoID']           = $customer_arr['receivableAutoID'];
        $data['customerReceivableSystemGLCode']     = $customer_arr['receivableSystemGLCode'];
        $data['customerReceivableGLAccount']        = $customer_arr['receivableGLAccount'];
        $data['customerReceivableDescription']      = $customer_arr['receivableDescription'];
        $data['customerReceivableType']             = $customer_arr['receivableType'];
        $data['customerCurrencyID']                 = $customer_arr['customerCurrencyID'];
        $data['customerCurrency']                   = $customer_arr['customerCurrency'];
        $data['FYBegin']                            = trim($FYBegin);
        $data['FYEnd']                              = trim($FYEnd);
        $data['docRefNo']                           = trim($this->input->post('referenceno'));
        $data['comments']                           = trim($this->input->post('comments'));
        $data['transactionCurrencyID']                  = trim($this->input->post('customer_currencyID'));
        $data['transactionCurrency']                    = trim($currency_code[0]);
        $data['transactionExchangeRate']                = 1;
        $data['transactionCurrencyDecimalPlaces']       = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID']                 = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency']                   = $this->common_data['company_data']['company_default_currency'];
        $default_currency      = currency_conversionID($data['transactionCurrencyID'],$data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate']               = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces']      = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrency']               = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID']             = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency    = currency_conversionID($data['transactionCurrencyID'],$data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate']           = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces']  = $reporting_currency['DecimalPlaces'];
        $customer_currency    = currency_conversionID($data['transactionCurrencyID'],$data['customerCurrencyID']);
        $data['customerCurrencyExchangeRate']           = $customer_currency['conversion'];
        $data['customerCurrencyDecimalPlaces']          = $customer_currency['DecimalPlaces'];
        $data['modifiedPCID']                       = $this->common_data['current_pc'];
        $data['modifiedUserID']                     = $this->common_data['current_userID'];
        $data['modifiedUserName']                   = $this->common_data['current_user'];
        $data['modifiedDateTime']                   = $this->common_data['current_date'];

        if (trim($this->input->post('creditNoteMasterAutoID'))) {
            $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID')));
            $this->db->update('srp_erp_creditnotemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Credit Note Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Credit Note Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('creditNoteMasterAutoID'));
            }
        } else {
            //$this->load->library('sequence');
            $data['companyCode']        = $this->common_data['company_data']['company_code'];
            $data['companyID']          = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup']   = $this->common_data['user_group'];
            $data['createdPCID']        = $this->common_data['current_pc'];
            $data['createdUserID']      = $this->common_data['current_userID'];
            $data['createdUserName']    = $this->common_data['current_user'];
            $data['createdDateTime']    = $this->common_data['current_date'];
            //$data['creditNoteCode']      = $this->sequence->sequence_generator($data['documentID']);

            $this->db->insert('srp_erp_creditnotemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Credit Note   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Credit Note Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_inv_tax_detail(){
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $this->input->post('InvoiceAutoID'));
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $tax_detail = $this->db->get('srp_erp_customerinvoicetaxdetails')->row_array();
        if (!empty($tax_detail)) {
            $this->session->set_flashdata('w', 'Tax Detail added already ! ');
            return array('status' => true);
        }
        $this->db->select('*');
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $master = $this->db->get('srp_erp_taxmaster')->row_array();

        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID,companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces,companyReportingCurrency,companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('invoiceAutoID', $this->input->post('InvoiceAutoID'));
        $inv_master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

        $data['invoiceAutoID']                   = trim($this->input->post('InvoiceAutoID'));
        $data['taxMasterAutoID']                 = $master['taxMasterAutoID'];
        $data['taxDescription']                  = $master['taxDescription'];
        $data['taxShortCode']                    = $master['taxShortCode'];
        $data['supplierAutoID']                  = $master['supplierAutoID'];
        $data['supplierSystemCode']              = $master['supplierSystemCode'];
        $data['supplierName']                    = $master['supplierName'];
        $data['supplierCurrencyID']              = $master['supplierCurrencyID'];
        $data['supplierCurrency']                = $master['supplierCurrency'];
        $data['supplierCurrencyDecimalPlaces']   = $master['supplierCurrencyDecimalPlaces'];
        $data['GLAutoID']                        = $master['supplierGLAutoID'];
        $data['systemGLCode']                    = $master['supplierGLSystemGLCode'];
        $data['GLCode']                          = $master['supplierGLAccount'];
        $data['GLDescription']                   = $master['supplierGLDescription'];
        $data['GLType']                          = $master['supplierGLType'];
        $data['taxPercentage']                   = trim($this->input->post('percentage'));
        $data['transactionAmount']               = trim($this->input->post('amount'));
        $data['transactionCurrencyID']           = $inv_master['transactionCurrencyID'];
        $data['transactionCurrency']             = $inv_master['transactionCurrency'];
        $data['transactionExchangeRate']         = $inv_master['transactionExchangeRate'];
        $data['transactionCurrencyDecimalPlaces']= $inv_master['transactionCurrencyDecimalPlaces'];
        $data['companyLocalCurrencyID']          = $inv_master['companyLocalCurrencyID'];
        $data['companyLocalCurrency']            = $inv_master['companyLocalCurrency'];
        $data['companyLocalExchangeRate']        = $inv_master['companyLocalExchangeRate'];
        $data['companyReportingCurrencyID']      = $inv_master['companyReportingCurrencyID'];
        $data['companyReportingCurrency']        = $inv_master['companyReportingCurrency'];
        $data['companyReportingExchangeRate']    = $inv_master['companyReportingExchangeRate'];

        $supplierCurrency      = currency_conversion($data['transactionCurrency'],$data['supplierCurrency']);
        $data['supplierCurrencyExchangeRate']    = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces']   = $supplierCurrency['DecimalPlaces'];
        $data['modifiedPCID']                    = $this->common_data['current_pc'];
        $data['modifiedUserID']                  = $this->common_data['current_userID'];
        $data['modifiedUserName']                = $this->common_data['current_user'];
        $data['modifiedDateTime']                = $this->common_data['current_date'];

        if (trim($this->input->post('taxDetailAutoID'))) {
            $this->db->where('taxDetailAutoID', trim($this->input->post('taxDetailAutoID')));
            $this->db->update('srp_erp_customerinvoicetaxdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Tax Detail : ' . $data['GLDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Tax Detail : ' . $data['GLDescription']. ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('taxDetailAutoID'));
            }
        } else {
            $data['companyCode']        = $this->common_data['company_data']['company_code'];
            $data['companyID']          = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup']   = $this->common_data['user_group'];
            $data['createdPCID']        = $this->common_data['current_pc'];
            $data['createdUserID']      = $this->common_data['current_userID'];
            $data['createdUserName']    = $this->common_data['current_user'];
            $data['createdDateTime']    = $this->common_data['current_date'];
            $this->db->insert('srp_erp_customerinvoicetaxdetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Tax Detail : ' . $data['GLDescription']. '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Tax Detail : ' . $data['GLDescription']. ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function load_credit_note_header()
    {
        $convertFormat=convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(creditNoteDate,\''.$convertFormat.'\') AS creditNoteDate,DATE_FORMAT(FYPeriodDateFrom,"%Y-%m-%d") AS FYPeriodDateFrom,DATE_FORMAT(FYPeriodDateTo,"%Y-%m-%d") AS FYPeriodDateTo');
        $this->db->where('creditNoteMasterAutoID', $this->input->post('creditNoteMasterAutoID'));
        return $this->db->get('srp_erp_creditnotemaster')->row_array();
    }

    function fetch_cn_detail_table(){
        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces,companyLocalCurrency,companyLocalCurrencyDecimalPlaces,customerCurrency,customerCurrencyDecimalPlaces');
        $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID')));
        $this->db->from('srp_erp_creditnotemaster');
        $data['currency'] = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID')));
        $this->db->from('srp_erp_creditnotedetail');trim($this->input->post('creditNoteMasterAutoID'));
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function save_cn_detail(){
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate');
        $this->db->where('creditNoteMasterAutoID', $this->input->post('creditNoteMasterAutoID'));
        $master = $this->db->get('srp_erp_creditnotemaster')->row_array();
        $segment                                 = explode('|', trim($this->input->post('segment_gl')));
        $gl_code                                 = explode(' | ', trim($this->input->post('gl_code_des')));
        $data['creditNoteMasterAutoID']          = trim($this->input->post('creditNoteMasterAutoID'));
        $data['GLAutoID']                        = trim($this->input->post('gl_code'));
        $data['systemGLCode']                    = trim($gl_code[0]);
        $data['GLCode']                          = trim($gl_code[1]);
        $data['GLDescription']                   = trim($gl_code[2]);
        $data['GLType']                          = trim($gl_code[3]);
        $data['segmentID']                       = trim($segment[0]);
        $data['segmentCode']                     = trim($segment[1]);
        $data['transactionAmount']               = trim($this->input->post('amount'));
        $data['companyLocalAmount']              = ($data['transactionAmount']/$master['companyLocalExchangeRate']);
        $data['companyReportingAmount']          = ($data['transactionAmount']/$master['companyReportingExchangeRate']);
        $data['customerAmount']                  = ($data['transactionAmount']/$master['customerCurrencyExchangeRate']);
        $data['description']                     = trim($this->input->post('description'));
        $data['modifiedPCID']                    = $this->common_data['current_pc'];
        $data['modifiedUserID']                  = $this->common_data['current_userID'];
        $data['modifiedUserName']                = $this->common_data['current_user'];
        $data['modifiedDateTime']                = $this->common_data['current_date'];

        if (trim($this->input->post('creditNoteDetailsID'))) {
            $this->db->where('creditNoteDetailsID', trim($this->input->post('creditNoteDetailsID')));
            $this->db->update('srp_erp_creditnotedetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Credit Note Detail : ' . $data['GLDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Credit Note Detail : ' . $data['GLDescription']. ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('creditNoteDetailsID'));
            }
        } else {
            $data['companyCode']        = $this->common_data['company_data']['company_code'];
            $data['companyID']          = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup']   = $this->common_data['user_group'];
            $data['createdPCID']        = $this->common_data['current_pc'];
            $data['createdUserID']      = $this->common_data['current_userID'];
            $data['createdUserName']    = $this->common_data['current_user'];
            $data['createdDateTime']    = $this->common_data['current_date'];
            $this->db->insert('srp_erp_creditnotedetail', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Credit Note Detail : ' . $data['GLDescription']. '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Credit Note Detail : ' . $data['GLDescription']. ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function cn_confirmation(){


        $this->db->select('creditNoteMasterAutoID');
        $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID')));
        $this->db->from('srp_erp_creditnotedetail');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('w', 'There are no records to confirm this document!');
        }
        else{
        $this->db->select('creditNoteMasterAutoID');
        $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID')));
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_creditnotemaster');
        $Confirmed = $this->db->get()->row_array();
        if (!empty($Confirmed)) {
            return array('w', 'Document already confirmed');
        }else
        {
            $system_code = trim($this->input->post('creditNoteMasterAutoID'));

            $this->db->select('documentID, creditNoteCode,DATE_FORMAT(creditNoteDate, "%Y") as invYear,DATE_FORMAT(creditNoteDate, "%m") as invMonth,companyFinanceYearID');
            $this->db->where('creditNoteMasterAutoID', $system_code);
            $this->db->from('srp_erp_creditnotemaster');
            $master_dt = $this->db->get()->row_array();
            $this->load->library('sequence');
            if($master_dt['creditNoteCode'] == "0") {
                $pvCd = array(
                    'creditNoteCode' => $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth'])
                );
                $this->db->where('creditNoteMasterAutoID', $system_code);
                $this->db->update('srp_erp_creditnotemaster', $pvCd);
            }


            $this->load->library('approvals');
            $this->db->select('creditNoteMasterAutoID, creditNoteCode');
            $this->db->where('creditNoteMasterAutoID', $system_code);
            $this->db->from('srp_erp_creditnotemaster');
            $cn_data = $this->db->get()->row_array();
            $approvals_status = $this->approvals->CreateApproval('CN',$cn_data['creditNoteMasterAutoID'],$cn_data['creditNoteCode'],'Credit note','srp_erp_creditnotemaster','creditNoteMasterAutoID');
            if ($approvals_status==1) {

                $data = array(
                    'confirmedYN'        => 1,
                    'confirmedDate'      => $this->common_data['current_date'],
                    'confirmedByEmpID'   => $this->common_data['current_userID'],
                    'confirmedByName'    => $this->common_data['current_user']
                );

                $this->db->where('creditNoteMasterAutoID', $system_code);
               $result =  $this->db->update('srp_erp_creditnotemaster', $data);
                if($result)
                {
                    return array('s', 'Document confirmed Successfully');

                }

            }else if($approvals_status==3){
                return array('w', 'There are no users exist to perform approval for this document.');
            }else{
                return array('e', 'Document confirmation failed');
            }
        }
        }
    }

    function delete_tax_detail(){
        $this->db->delete('srp_erp_customerinvoicetaxdetails',array('taxDetailAutoID' => trim($this->input->post('taxDetailAutoID'))));
        return true;
    }

    function delete_cn_detail(){ 
        $this->db->select('invoiceAutoID,transactionAmount');
        $this->db->from('srp_erp_creditnotedetail');
        $this->db->where('creditNoteDetailsID', trim($this->input->post('creditNoteDetailsID'))); 
        $detail_arr = $this->db->get()->row_array();
        $company_id = $this->common_data['company_data']['company_id'];
        $match_id   = $detail_arr['invoiceAutoID'];
        $number     = $detail_arr['transactionAmount'];
        $status     = 0;
        $this->db->query("UPDATE srp_erp_customerinvoicemaster SET creditNoteTotalAmount = (creditNoteTotalAmount -{$number}) WHERE invoiceAutoID='{$match_id}' and companyID='{$company_id}'"); 
        $this->db->delete('srp_erp_creditnotedetail', array('creditNoteDetailsID' => trim($this->input->post('creditNoteDetailsID'))));
        $this->session->set_flashdata('s', 'Credit Note Detail Deleted Successfully');
        return true;
    }

    function save_cn_approval(){
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_code    = trim($this->input->post('creditNoteMasterAutoID'));
        $level_id       = trim($this->input->post('Level'));
        $status         = trim($this->input->post('status'));
        $comments       = trim($this->input->post('comments'));

        $approvals_status = $this->approvals->approve_document($system_code,$level_id,$status,$comments,'CN');
        if ($approvals_status==1) {
            $this->load->model('Double_entry_model');
            $double_entry  = $this->Double_entry_model->fetch_double_entry_credit_note_data($system_code,'CN');
            for ($i=0; $i < count($double_entry['gl_detail']); $i++) { 
                $generalledger_arr[$i]['documentMasterAutoID']                      = $double_entry['master_data']['creditNoteMasterAutoID'];
                $generalledger_arr[$i]['documentCode']                              = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode']                        = $double_entry['master_data']['creditNoteCode'];
                $generalledger_arr[$i]['documentDate']                              = $double_entry['master_data']['creditNoteDate'];
                $generalledger_arr[$i]['documentType']                              = '';
                $generalledger_arr[$i]['documentYear']                              = $double_entry['master_data']['creditNoteDate'];
                $generalledger_arr[$i]['documentMonth']                             = date("m",strtotime($double_entry['master_data']['creditNoteDate']));
                $generalledger_arr[$i]['documentNarration']                         = $double_entry['master_data']['comments'];
                $generalledger_arr[$i]['chequeNumber']                              = '';
                $generalledger_arr[$i]['transactionCurrency']                       = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionCurrencyID']                     = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionExchangeRate']                   =$double_entry['gl_detail'][$i]['transactionExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']          = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrency']                      = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalCurrencyID']                    = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalExchangeRate']                  = $double_entry['gl_detail'][$i]['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']         = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrency']                  = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingCurrencyID']                = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingExchangeRate']              = $double_entry['gl_detail'][$i]['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']     = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID']                           = '';
                $generalledger_arr[$i]['partyType']                                 = 'CUS';
                $generalledger_arr[$i]['partyAutoID']                               = $double_entry['master_data']['customerID'];
                $generalledger_arr[$i]['partySystemCode']                           = $double_entry['master_data']['customerCode'];
                $generalledger_arr[$i]['partyName']                                 = $double_entry['master_data']['customerName'];
                $generalledger_arr[$i]['partyCurrencyID']                           = $double_entry['master_data']['customerCurrencyID'];
                $generalledger_arr[$i]['partyCurrency']                             = $double_entry['master_data']['customerCurrency'];
                $generalledger_arr[$i]['partyExchangeRate']                         = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces']                = $double_entry['master_data']['customerCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID']                          = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName']                           = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate']                             = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate']                              = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID']                           = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName']                         = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID']                                 = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode']                               = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type']=='cr') {
                    $amount =($double_entry['gl_detail'][$i]['gl_cr']*-1);
                }
                $generalledger_arr[$i]['transactionAmount']                         = round($amount,$generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyLocalAmount']                        = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['companyLocalExchangeRate']),$generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount']                    = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['companyReportingExchangeRate']),$generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['partyExchangeRate']),$generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['amount_type']                               = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID']                      = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID']                                  = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode']                              = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode']                                    = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription']                             = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType']                                    = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID']                                 = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode']                               = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                $generalledger_arr[$i]['subLedgerType']                             = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc']                             = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon']                                   = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['createdUserGroup']                          = $this->common_data['user_group'];
                $generalledger_arr[$i]['createdPCID']                               = $this->common_data['current_pc'];
                $generalledger_arr[$i]['createdUserID']                             = $this->common_data['current_userID'];
                $generalledger_arr[$i]['createdDateTime']                           = $this->common_data['current_date'];
                $generalledger_arr[$i]['createdUserName']                           = $this->common_data['current_user'];
                $generalledger_arr[$i]['modifiedPCID']                              = $this->common_data['current_pc'];
                $generalledger_arr[$i]['modifiedUserID']                            = $this->common_data['current_userID'];
                $generalledger_arr[$i]['modifiedDateTime']                          = $this->common_data['current_date'];
                $generalledger_arr[$i]['modifiedUserName']                          = $this->common_data['current_user'];
            }

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                $this->db->select('sum(transactionAmount) as transaction_total, sum(companyLocalAmount) as companyLocal_total, sum(companyReportingAmount) as companyReporting_total, sum(partyCurrencyAmount) as party_total');
                $this->db->where('documentMasterAutoID',$system_code);
                $this->db->where('documentCode','CN');
                $totals = $this->db->get('srp_erp_generalledger')->row_array();
                if ($totals['transaction_total'] !=0 or $totals['companyLocal_total'] !=0 or $totals['companyReporting_total'] !=0 or $totals['party_total'] !=0) {
                    $generalledger_arr = array();
                    $ERGL_ID = $this->common_data['controlaccounts']['ERGL'];
                    $ERGL = fetch_gl_account_desc($ERGL_ID);
                    $generalledger_arr['documentMasterAutoID']= $double_entry['master_data']['creditNoteMasterAutoID'];
                    $generalledger_arr['documentCode']        = $double_entry['code'];
                    $generalledger_arr['documentSystemCode']  = $double_entry['master_data']['creditNoteCode'];
                    $generalledger_arr['documentDate']        = $double_entry['master_data']['creditNoteDate'];
                    $generalledger_arr['documentType']        = '';
                    $generalledger_arr['documentYear']        = $double_entry['master_data']['creditNoteDate'];
                    $generalledger_arr['documentMonth']=date("m",strtotime($double_entry['master_data']['creditNoteDate']));
                    $generalledger_arr['documentNarration']   = $double_entry['master_data']['docRefNo'];
                    $generalledger_arr['chequeNumber']        = '';
                    $generalledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $generalledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $generalledger_arr['transactionExchangeRate']=$double_entry['master_data']['transactionExchangeRate'];
                    $generalledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr['companyLocalExchangeRate']=$double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr['companyReportingCurrency']=$double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr['partyContractID'] = '';
                    $generalledger_arr['partyType'] = 'CUS';
                    $generalledger_arr['partyAutoID']               = $double_entry['master_data']['customerID'];
                    $generalledger_arr['partySystemCode']           = $double_entry['master_data']['customerCode'];
                    $generalledger_arr['partyName']                 = $double_entry['master_data']['customerName'];
                    $generalledger_arr['partyCurrencyID'] = $double_entry['master_data']['customerCurrencyID'];
                    $generalledger_arr['partyCurrency']             = $double_entry['master_data']['customerCurrency'];
                    $generalledger_arr['partyExchangeRate']  = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr['partyCurrencyDecimalPlaces']=$double_entry['master_data']['customerCurrencyDecimalPlaces'];
                    $generalledger_arr['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $generalledger_arr['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $generalledger_arr['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $generalledger_arr['approvedDate'] = $double_entry['master_data']['approvedDate'];
                    $generalledger_arr['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                    $generalledger_arr['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                    $generalledger_arr['companyID'] = $double_entry['master_data']['companyID'];
                    $generalledger_arr['companyCode'] = $double_entry['master_data']['companyCode'];
                    $generalledger_arr['transactionAmount'] = round(($totals['transaction_total']* -1), $generalledger_arr['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr['companyLocalAmount'] = round(($totals['companyLocal_total']* -1), $generalledger_arr['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr['companyReportingAmount'] = round(($totals['companyReporting_total']* -1), $generalledger_arr['companyReportingCurrencyDecimalPlaces']);
                    $generalledger_arr['partyCurrencyAmount'] = round(($totals['party_total']* -1), $generalledger_arr['partyCurrencyDecimalPlaces']);
                    $generalledger_arr['amount_type'] = null;
                    $generalledger_arr['documentDetailAutoID'] = 0;
                    $generalledger_arr['GLAutoID'] = $ERGL_ID;
                    $generalledger_arr['systemGLCode'] = $ERGL['systemAccountCode'];
                    $generalledger_arr['GLCode'] = $ERGL['GLSecondaryCode'];
                    $generalledger_arr['GLDescription'] = $ERGL['GLDescription'];
                    $generalledger_arr['GLType'] = $ERGL['subCategory'];
                    $seg = explode('|',$this->common_data['company_data']['default_segment']);
                    $generalledger_arr['segmentID'] = $seg[0];
                    $generalledger_arr['segmentCode'] = $seg[1];
                    $generalledger_arr['subLedgerType'] = 0;
                    $generalledger_arr['subLedgerDesc'] = null;
                    $generalledger_arr['isAddon'] = 0;
                    $generalledger_arr['createdUserGroup'] = $this->common_data['user_group'];
                    $generalledger_arr['createdPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr['createdUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr['createdDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr['createdUserName'] = $this->common_data['current_user'];
                    $generalledger_arr['modifiedPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr['modifiedUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr['modifiedDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr['modifiedUserName'] = $this->common_data['current_user'];
                    $this->db->insert('srp_erp_generalledger', $generalledger_arr);
                }
            }

            // $data['approvedYN']             = $status;
            // $data['approvedbyEmpID']        = $this->common_data['current_userID'];
            // $data['approvedbyEmpName']      = $this->common_data['current_user'];
            // $data['approvedDate']           = $this->common_data['current_date'];

            // $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID')));
            // $this->db->update('srp_erp_creditnotemaster', $data);

            $this->session->set_flashdata('s', 'Credit Note Approval Successfully.');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function fetch_custemer_data_invoice(){
        $this->db->select('creditNoteDate,creditNoteMasterAutoID,customerID,transactionCurrency, transactionCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID')));
        $this->db->from('srp_erp_creditnotemaster');
        $data['master'] = $this->db->get()->row_array();  

        /*$this->db->select('invoiceAutoID,invoiceCode,invoiceDate,receiptTotalAmount,transactionCurrency,transactionCurrencyID, creditNoteTotalAmount, transactionAmount');
        $this->db->where('invoiceDate <=', $data['master']['creditNoteDate']);
        $this->db->where('customerID', $data['master']['customerID']);
        $this->db->where('transactionCurrency', $data['master']['transactionCurrency']);
        $this->db->where('receiptInvoiceYN', 0);
        $this->db->where('approvedYN', 1);
        $this->db->from('srp_erp_customerinvoicemaster');*/

        $output = $this->db->query("SELECT srp_erp_customerinvoicemaster.invoiceAutoID,invoiceCode,receiptTotalAmount,advanceMatchedTotal,creditNoteTotalAmount,referenceNo ,( ( cid.transactionAmount - cid.totalAfterTax ) * ( IFNULL( tax.taxPercentage, 0 ) / 100 ) + IFNULL( cid.transactionAmount, 0 ) ) as transactionAmount,invoiceDate  FROM srp_erp_customerinvoicemaster LEFT JOIN (SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount,IFNULL(SUM(totalAfterTax ),0) as totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) cid ON srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID
 LEFT JOIN (SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID) tax ON tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID WHERE confirmedYN = 1 AND approvedYN = 1 AND receiptInvoiceYN = 0 AND `customerID` = '{$data['master']['customerID']}' AND `transactionCurrencyID` = '{$data['master']['transactionCurrencyID']}' AND invoiceDate <= '{$data['master']['creditNoteDate']}' ")->result_array();

        $data['detail'] = $output;
        return $data;      
    }

    function save_credit_base_items(){
        $projectExist = project_is_exist();
        $this->db->trans_start();
        $creditNoteMasterAutoID = trim($this->input->post('creditNoteMasterAutoID'));
        $invoice_id     = $this->input->post('invoiceAutoID');
        $segments       = $this->input->post('segment');
        $gl_code_d      = $this->input->post('gl_code_dec');
        $amounts        = $this->input->post('amounts');
        $gl_codes       = $this->input->post('gl_code');
        $code           = $this->input->post('invoiceCode');
        $projectID      = $this->input->post('project');
        for($i=0; $i < count($invoice_id); $i++) {
            $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyID');
            $this->db->where('invoiceAutoID', $invoice_id[$i]);
            $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

            $segment                                     = explode('|', $segments[$i]);
            $gl_code_des                                 = explode('|', $gl_code_d[$i]);
            $data[$i]['creditNoteMasterAutoID']          = $creditNoteMasterAutoID;
            $data[$i]['invoiceAutoID']                   = $invoice_id[$i];
            $data[$i]['invoiceSystemCode']               = $code[$i];
            $data[$i]['GLAutoID']                        = $gl_codes[$i];
            if($projectExist == 1){
                $projectCurrency = project_currency($projectID[$i]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'],$projectCurrency);
                $data[$i]['projectID'] = $projectID[$i];
                $data[$i]['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data[$i]['projectID']                       = $projectID[$i];
            $data[$i]['systemGLCode']                    = trim($gl_code_des[0]);
            $data[$i]['GLCode']                          = trim($gl_code_des[1]);
            $data[$i]['GLDescription']                   = trim($gl_code_des[2]);
            $data[$i]['GLType']                          = trim($gl_code_des[3]);
            $data[$i]['segmentID']                       = trim($segment[0]);
            $data[$i]['segmentCode']                     = trim($segment[1]);
            $data[$i]['transactionAmount']               = $amounts[$i];
            $data[$i]['companyLocalAmount']              = ($data[$i]['transactionAmount']/$master['companyLocalExchangeRate']);
            $data[$i]['companyLocalExchangeRate']        = $master['companyLocalExchangeRate'];
            $data[$i]['companyReportingAmount']          = ($data[$i]['transactionAmount']/$master['companyReportingExchangeRate']);
            $data[$i]['companyReportingExchangeRate']    = $master['companyReportingExchangeRate'];
            $data[$i]['customerAmount']                  = ($data[$i]['transactionAmount']/$master['customerCurrencyExchangeRate']);
            $data[$i]['customerCurrencyExchangeRate']    = $master['customerCurrencyExchangeRate'];
            $data[$i]['description']                     = trim($this->input->post('description'));
            $data[$i]['modifiedPCID']                    = $this->common_data['current_pc'];
            $data[$i]['modifiedUserID']                  = $this->common_data['current_userID'];
            $data[$i]['modifiedUserName']                = $this->common_data['current_user'];
            $data[$i]['modifiedDateTime']                = $this->common_data['current_date']; 
            $data[$i]['companyID']                       = $this->common_data['company_data']['company_id'];
            $data[$i]['companyCode']                     = $this->common_data['company_data']['company_code'];
            $data[$i]['createdUserGroup']                = $this->common_data['user_group'];
            $data[$i]['createdPCID']                     = $this->common_data['current_pc'];
            $data[$i]['createdUserID']                   = $this->common_data['current_userID'];
            $data[$i]['createdUserName']                 = $this->common_data['current_user'];
            $data[$i]['createdDateTime']                 = $this->common_data['current_date'];

            $id          = $data[$i]['invoiceAutoID'];
            $amo         = $data[$i]['transactionAmount'];
            $this->db->query("UPDATE srp_erp_customerinvoicemaster SET creditNoteTotalAmount = (creditNoteTotalAmount+{$amo}) WHERE invoiceAutoID='{$id}'");
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_creditnotedetail', $data); 
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function delete_creditNote_attachement(){
        $attachmentID=$this->input->post('attachmentID');
        $myFileName=$this->input->post('myFileName');
        $url= base_url("attachments");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH.$link))
        {
            return false;
        }
        else
        {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        }
    }

    function delete_creditNote_master(){
        /*$this->db->select('invoiceAutoID,transactionAmount');
        $this->db->from('srp_erp_creditnotedetail');
        $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID')));
        $detail_arr = $this->db->get()->result_array();
        $company_id = $this->common_data['company_data']['company_id'];
        foreach($detail_arr as $val_as){
            $match_id   = $val_as['invoiceAutoID'];
            $number     = $val_as['transactionAmount'];
            $this->db->query("UPDATE srp_erp_customerinvoicemaster SET creditNoteTotalAmount = (creditNoteTotalAmount - {$number}) WHERE invoiceAutoID='{$match_id}' and companyID='{$company_id}'");
        }
        $this->db->delete('srp_erp_creditnotemaster', array('creditNoteMasterAutoID' => trim($this->input->post('creditNoteMasterAutoID'))));
        $this->db->delete('srp_erp_creditnotedetail', array('creditNoteMasterAutoID' => trim($this->input->post('creditNoteMasterAutoID'))));
        return true;*/
        $this->db->select('*');
        $this->db->from('srp_erp_creditnotedetail');
        $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID')));
        $datas= $this->db->get()->row_array();
        if($datas){
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        }else{
            $data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID')));
            $this->db->update('srp_erp_creditnotemaster', $data);
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;
        }
    }

    function re_open_credit_note(){
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID')));
        $this->db->update('srp_erp_creditnotemaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }
    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'CN');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }
    function save_crditNote_detail_GLCode_multiple(){
        $this->db->trans_start();
        $projectExist = project_is_exist();
        $this->db->select('*');
        $this->db->where('creditNoteMasterAutoID', $this->input->post('creditNoteMasterAutoID'));
        $master = $this->db->get('srp_erp_creditnotemaster')->row_array();

        $gl_codes = $this->input->post('gl_code_array');
        $gl_code_des = $this->input->post('gl_code_des');
        $projectID = $this->input->post('projectID');
        $amount = $this->input->post('amount');
        $descriptions = $this->input->post('description');
        $segment_gls = $this->input->post('segment_gl');

        foreach ($gl_codes as $key => $gl_code) {
            $segment = explode('|', $segment_gls[$key]);
            $gl_code = explode('|', $gl_code_des[$key]);

            $data[$key]['creditNoteMasterAutoID'] = trim($this->input->post('creditNoteMasterAutoID'));
            $data[$key]['GLAutoID'] = $gl_codes[$key];
            if($projectExist == 1){
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'],$projectCurrency);
                $data[$key]['projectID'] = $projectID[$key];
                $data[$key]['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data[$key]['systemGLCode'] = trim($gl_code[0]);
            $data[$key]['GLCode'] = trim($gl_code[1]);
            $data[$key]['GLDescription'] = trim($gl_code[2]);
            $data[$key]['GLType'] = trim($gl_code[3]);
            $data[$key]['segmentID'] = trim($segment[0]);
            $data[$key]['segmentCode'] = trim($segment[1]);
            $data[$key]['description'] = $descriptions[$key];
            $data[$key]['transactionAmount'] = round($amount[$key], $master['transactionCurrencyDecimalPlaces']);
            $companyLocalAmount = $data[$key]['transactionAmount'] / $master['companyLocalExchangeRate'];
            $data[$key]['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $data[$key]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $companyReportingAmount = $data[$key]['transactionAmount'] / $master['companyReportingExchangeRate'];
            $data[$key]['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $data[$key]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $customerAmount = $data[$key]['transactionAmount'] / $master['customerCurrencyExchangeRate'];
            $data[$key]['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
            $data[$key]['customerCurrencyExchangeRate'] = $master['customerCurrencyExchangeRate'];

            $data[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdUserName'] = $this->common_data['current_user'];
            $data[$key]['createdDateTime'] = $this->common_data['current_date'];
            $data[$key]['isFromInvoice'] = 0;
        }
        $this->db->insert_batch('srp_erp_creditnotedetail', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            //$this->session->set_flashdata('e', 'Supplier Invoice Detail : Saved Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('e', 'Credit Note Detail : Saved Failed ');
        } else {
            //$this->session->set_flashdata('s', 'Supplier Invoice Detail : Saved Successfully.');
            $this->db->trans_commit();
            return array('s', 'Credit Note Detail : Saved Successfully.');
        }
    }

}