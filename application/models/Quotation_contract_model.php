<?php

class Quotation_contract_model extends ERP_Model
{

    function save_quotation_contract_header()
    {
        $date_format_policy = date_format_policy();
        $cntrctDate = $this->input->post('contractDate');
        $contractDate = input_format_date($cntrctDate, $date_format_policy);

        $cntrctEpDate = $this->input->post('contractExpDate');
        $contractExpDate = input_format_date($cntrctEpDate, $date_format_policy);

        $this->db->trans_start();
        $customer_arr = $this->fetch_customer_data(trim($this->input->post('customerID')));
        $currency_code = explode('|', trim($this->input->post('currency_code')));

        $data['contractType'] = trim($this->input->post('contractType'));
        $d_code = 'CNT';
        if ($data['contractType'] == 'Quotation') {
            $d_code = 'QUT';
        } elseif ($data['contractType'] == 'Sales Order') {
            $d_code = 'SO';
        }
        $data['documentID'] = $d_code;
        $data['contactPersonName'] = trim($this->input->post('contactPersonName'));
        $data['contactPersonNumber'] = trim($this->input->post('contactPersonNumber'));
        $data['contractDate'] = trim($contractDate);
        $data['contractExpDate'] = trim($contractExpDate);
        $data['contractNarration'] = trim_desc($this->input->post('contractNarration'));
        $data['referenceNo'] = trim($this->input->post('referenceNo'));
        $data['customerID'] = $customer_arr['customerAutoID'];
        $data['customerSystemCode'] = $customer_arr['customerSystemCode'];
        $data['customerName'] = $customer_arr['customerName'];
        $data['customerAddress'] = $customer_arr['customerAddress1'] . ' ' . $customer_arr['customerAddress2'];
        $data['customerTelephone'] = $customer_arr['customerTelephone'];
        $data['customerFax'] = $customer_arr['customerFax'];
        $data['customerEmail'] = $customer_arr['customerEmail'];
        $data['customerReceivableAutoID'] = $customer_arr['receivableAutoID'];
        $data['customerReceivableSystemGLCode'] = $customer_arr['receivableSystemGLCode'];
        $data['customerReceivableGLAccount'] = $customer_arr['receivableGLAccount'];
        $data['customerReceivableDescription'] = $customer_arr['receivableDescription'];
        $data['customerReceivableType'] = $customer_arr['receivableType'];
        $data['customerCurrency'] = $customer_arr['customerCurrency'];
        $data['customerCurrencyID'] = $customer_arr['customerCurrencyID'];
        $data['customerCurrencyDecimalPlaces'] = $customer_arr['customerCurrencyDecimalPlaces'];
        $data['Note'] = trim($this->input->post('Note'));
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID'));
        $data['transactionCurrency'] = trim($currency_code[0]);
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $customer_currency = currency_conversionID($data['transactionCurrencyID'], $data['customerCurrencyID']);
        $data['customerCurrencyExchangeRate'] = $customer_currency['conversion'];
        $data['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('contractAutoID'))) {
            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID')));
            $this->db->update('srp_erp_contractmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Contract Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Contract Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('contractAutoID'));
            }
        } else {
            $this->load->library('sequence');

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['contractCode'] = $this->sequence->sequence_generator($data['documentID']);

            $this->db->insert('srp_erp_contractmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Contract Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Contract Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_customer_data($customerID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $customerID);
        return $this->db->get()->row_array();
    }

    function fetch_contract_template_data($contractAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(contractDate,\'' . $convertFormat . '\') AS contractDate,DATE_FORMAT(contractExpDate,\'' . $convertFormat . '\') AS contractExpDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate');
        $this->db->where('contractAutoID', $contractAutoID);
        $this->db->from('srp_erp_contractmaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('customerName,customerAddress1,customerTelephone,customerSystemCode,customerFax');
        $this->db->where('customerAutoID', $data['master']['customerID']);
        $this->db->from('srp_erp_customermaster');
        $data['customer'] = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('contractAutoID', $contractAutoID);
        //$this->db->where('type','Item');
        $this->db->from('srp_erp_contractdetails');
        $data['detail'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('contractAutoID', $contractAutoID);
        $data['tax'] = $this->db->get('srp_erp_contracttaxdetails')->result_array();
        return $data;
    }

    function fetch_item_detail_table()
    {
        $contractAutoID = trim($this->input->post('contractAutoID'));
        $data = array();
        $this->db->select('*');
        $this->db->from('srp_erp_contractdetails');
        $this->db->where('contractAutoID', $contractAutoID);
        $data['detail'] = $this->db->get()->result_array();
        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('contractAutoID', $contractAutoID);
        $data['currency'] = $this->db->get('srp_erp_contractmaster')->row_array();
        $this->db->select('*');
        $this->db->where('contractAutoID', $contractAutoID);
        $this->db->from('srp_erp_contracttaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();
        return $data;
    }

    function delete_tax_detail()
    {
        $this->db->delete('srp_erp_contracttaxdetails', array('taxDetailAutoID' => trim($this->input->post('taxDetailAutoID'))));
        return true;
    }

    function load_contract_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(contractDate,\'' . $convertFormat . '\') AS contractDate,DATE_FORMAT(contractExpDate,\'' . $convertFormat . '\') AS contractExpDate');
        $this->db->where('contractAutoID', $this->input->post('contractAutoID'));
        return $this->db->get('srp_erp_contractmaster')->row_array();
    }

    function save_item_order_detail()
    {
        $itemAutoIDs = $this->input->post('itemAutoID');
        $uoms = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $itemReferenceNo = $this->input->post('itemReferenceNo');
        $discount = $this->input->post('discount');
        $discount_amount = $this->input->post('discount_amount');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $quantityRequested = $this->input->post('quantityRequested');

        $itemAutoIDJoin = join(',', $itemAutoIDs);

        if (!trim($this->input->post('contractDetailsAutoID'))) {
            $this->db->select('contractAutoID,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_contractdetails');
            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID')));
            $this->db->where('itemAutoID IN (' . $itemAutoIDJoin . ')');
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('w', 'Order Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }
        $this->db->trans_start();
        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $item_arr = fetch_item_data(trim($itemAutoID));
            $uom = explode('|', $uoms[$key]);
            $data[$key]['contractAutoID'] = trim($this->input->post('contractAutoID'));
            $data[$key]['itemAutoID'] = trim($itemAutoID);
            $data[$key]['itemSystemCode'] = $item_arr['itemSystemCode'];
            $data[$key]['itemDescription'] = $item_arr['itemDescription'];
            $data[$key]['itemCategory'] = $item_arr['mainCategory'];
            $data[$key]['unitOfMeasure'] = trim($uom[0]);
            $data[$key]['unitOfMeasureID'] = trim($UnitOfMeasureID[$key]);
            $data[$key]['itemReferenceNo'] = trim($itemReferenceNo[$key]);
            $data[$key]['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
            $data[$key]['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
            $data[$key]['conversionRateUOM'] = conversionRateUOM_id($data[$key]['unitOfMeasureID'], $data[$key]['defaultUOMID']);
            $data[$key]['discountPercentage'] = $discount[$key];
            $data[$key]['discountAmount'] = $discount_amount[$key];
            $data[$key]['requestedQty'] = trim($quantityRequested[$key]);
            $data[$key]['unittransactionAmount'] = (trim($estimatedAmount[$key]) - $data[$key]['discountAmount']);
            $data[$key]['transactionAmount'] = ($data[$key]['unittransactionAmount'] * $data[$key]['requestedQty']);
            $data[$key]['discountTotal'] = ($data[$key]['discountAmount'] * $data[$key]['requestedQty']);
            $data[$key]['comment'] = trim($comment[$key]);
            $data[$key]['remarks'] = trim($remarks[$key]);
            $data[$key]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$key]['modifiedUserID'] = $this->common_data['current_userID'];
            $data[$key]['modifiedUserName'] = $this->common_data['current_user'];
            $data[$key]['modifiedDateTime'] = $this->common_data['current_date'];

            $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdUserName'] = $this->common_data['current_user'];
            $data[$key]['createdDateTime'] = $this->common_data['current_date'];
        }

        if (!empty($this->input->post('contractDetailsAutoID'))) {
            $data[$key]['contractDetailsAutoID'] = trim($this->input->post('contractDetailsAutoID'));
            $this->db->update_batch('srp_erp_contractdetails', $data, 'contractDetailsAutoID');
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Order Detail : Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Order Detail :  Updated Successfully.');
            }
        } else {

            $this->db->insert_batch('srp_erp_contractdetails', $data);
            $last_id = 0;
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Order Detail : Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Order Detail : Saved Successfully.');
            }
        }
    }

    function update_item_order_detail()
    {
        $itemAutoID = $this->input->post('itemAutoID');
        $uoms = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $itemReferenceNo = $this->input->post('itemReferenceNo');
        $discount = $this->input->post('discount');
        $discount_amount = $this->input->post('discount_amount');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $quantityRequested = $this->input->post('quantityRequested');

        if (!empty($this->input->post('contractDetailsAutoID'))) {
            $this->db->select('contractAutoID,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_contractdetails');
            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID')));
            $this->db->where('itemAutoID IN (' . $itemAutoID . ')');
            $this->db->where('contractDetailsAutoID !=', trim($this->input->post('contractDetailsAutoID')));
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('w', 'Order Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }

        $this->db->trans_start();

        $item_arr = fetch_item_data(trim($itemAutoID));
        $uom = explode('|', $uoms);
        $data['contractAutoID'] = trim($this->input->post('contractAutoID'));
        $data['itemAutoID'] = trim($itemAutoID);
        $data['itemSystemCode'] = $item_arr['itemSystemCode'];
        $data['itemDescription'] = $item_arr['itemDescription'];
        $data['itemCategory'] = $item_arr['mainCategory'];
        $data['unitOfMeasure'] = trim($uom[0]);
        $data['unitOfMeasureID'] = trim($UnitOfMeasureID);
        $data['itemReferenceNo'] = trim($itemReferenceNo);
        $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['discountPercentage'] = trim($discount);
        $data['discountAmount'] = $discount_amount;
        $data['requestedQty'] = trim($quantityRequested);
        $data['unittransactionAmount'] = (trim($estimatedAmount) - $data['discountAmount']);
        $data['transactionAmount'] = ($data['unittransactionAmount'] * $data['requestedQty']);
        $data['discountTotal'] = ($data['discountAmount'] * $data['requestedQty']);
        $data['comment'] = trim($comment);
        $data['remarks'] = trim($remarks);
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        /*        $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];*/

        if (!empty($this->input->post('contractDetailsAutoID'))) {
            $this->db->where('contractDetailsAutoID', trim($this->input->post('contractDetailsAutoID')));
            $this->db->update('srp_erp_contractdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Order Detail : Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Order Detail :  Updated Successfully.');
            }
        }
    }

    function fetch_item_detail()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_contractdetails');
        $this->db->where('contractDetailsAutoID', trim($this->input->post('contractDetailsAutoID')));
        return $this->db->get()->row_array();
    }

    function delete_item_detail()
    {
        $this->db->delete('srp_erp_contractdetails', array('contractDetailsAutoID' => trim($this->input->post('contractDetailsAutoID'))));
        return true;
    }

    function contract_confirmation()
    {
        $this->db->select('contractDetailsAutoID');
        $this->db->where('contractAutoID', trim($this->input->post('contractAutoID')));
        $this->db->from('srp_erp_contractdetails');
        $record = $this->db->get()->result_array();
        if (empty($record)) {
            return array('w', 'There are no records to confirm this document!');
        } else {


            $this->db->select('contractAutoID');
            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID')));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_contractmaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                return array('w', 'Document already confirmed');
            } else {

                $this->load->library('approvals');
                $this->db->select('documentID,contractType,contractCode,customerCurrencyExchangeRate,companyReportingExchangeRate, companyLocalExchangeRate ,contractAutoID,transactionCurrencyDecimalPlaces');
                $this->db->where('contractAutoID', trim($this->input->post('contractAutoID')));
                $this->db->from('srp_erp_contractmaster');
                $c_data = $this->db->get()->row_array();
                $approvals_status = $this->approvals->CreateApproval($c_data['documentID'], $c_data['contractAutoID'], $c_data['contractCode'], $c_data['contractType'], 'srp_erp_contractmaster', 'contractAutoID', 1);
                if ($approvals_status) {
                    $this->db->select_sum('transactionAmount');
                    $this->db->where('contractAutoID', trim($this->input->post('contractAutoID')));
                    $total = $this->db->get('srp_erp_contractdetails')->row('transactionAmount');
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user'],
                        'transactionAmount' => round($total, $c_data['transactionCurrencyDecimalPlaces']),
                        'companyLocalAmount' => ($total / $c_data['companyLocalExchangeRate']),
                        'companyReportingAmount' => ($total / $c_data['companyReportingExchangeRate']),
                        'customerCurrencyAmount' => ($total / $c_data['customerCurrencyExchangeRate']),
                    );
                    $this->db->where('contractAutoID', trim($this->input->post('contractAutoID')));
                    $this->db->update('srp_erp_contractmaster', $data);
                    /*$this->session->set_flashdata('s', 'Create Approval : ' . $c_data['contractCode'] . ' Approvals Created Successfully ');
                    return true;*/
                    return array('s', 'Approvals Created Successfully.');
                } else {
                    /*return false;*/
                    return array('e', 'oops, something went wrong!.');
                }

            }


        }
    }

    function save_quotation_contract_approval()
    {
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_code = trim($this->input->post('contractAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        $comments = trim($this->input->post('comments'));
        $code = trim($this->input->post('code'));

        $this->db->select('documentID');
        $this->db->from('srp_erp_contractmaster');
        $this->db->where('contractAutoID', $system_code);
        $code = $this->db->get()->row('documentID');

        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, $code);
        if ($approvals_status == 1) {
            // $data['approvedYN']             = $status;
            // $data['approvedbyEmpID']        = $this->common_data['current_userID'];
            // $data['approvedbyEmpName']      = $this->common_data['current_user'];
            // $data['approvedDate']           = $this->common_data['current_date'];

            // $this->db->where('contractAutoID', trim($this->input->post('contractAutoID')));
            // $this->db->update('srp_erp_creditnotemaster', $data);
            $this->session->set_flashdata('s', 'Approval Successfully.');
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

    function delete_con_master()
    {
        //$this->db->delete('srp_erp_contractmaster', array('isDeleted' => 1,'deletedEmpID' => current_userID(),'deletedDate' => current_date()));
        //$this->db->delete('srp_erp_contractdetails', array('contractAutoID' => trim($this->input->post('contractAutoID'))));
        $this->db->select('*');
        $this->db->from('srp_erp_contractdetails');
        $this->db->where('contractAutoID', trim($this->input->post('contractAutoID')));
        $datas = $this->db->get()->row_array();
        if ($datas) {
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        } else {
            $data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('contractAutoID', trim($this->input->post('contractAutoID')));
            $this->db->update('srp_erp_contractmaster', $data);
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;
        }
    }

    function quotation_version()
    {
        $contractAutoID = trim($this->input->post('contractAutoID'));
        $this->db->select('invoiceAutoID');
        $this->db->where('contractAutoID', $contractAutoID);
        $inv_data = $this->db->get('srp_erp_customerinvoicedetails')->row_array();
        if (!empty($inv_data)) {
            return array('status' => 0, 'type' => 'w', 'message' => 'You cannot create versions for this quotation. Invoice has been created already.');
        }
        $this->db->select('*');
        $this->db->where('contractAutoID', $contractAutoID);
        $srp_erp_contractmaster = $this->db->get('srp_erp_contractmaster')->row_array();
        $this->db->select('*');
        $this->db->where('contractAutoID', $contractAutoID);
        $srp_erp_contractdetails = $this->db->get('srp_erp_contractdetails')->result_array();
        $this->db->insert('srp_erp_contractversion', $srp_erp_contractmaster);
        $this->db->insert_batch('srp_erp_contractversiondetails', $srp_erp_contractdetails);
        $this->db->query("UPDATE srp_erp_contractmaster SET versionNo = (versionNo +1) , confirmedYN=0 , approvedYN=0 WHERE contractAutoID='{$contractAutoID}'");
        $this->db->where('documentID', 'QUT');
        $this->db->where('documentSystemCode', $contractAutoID);
        $this->db->delete('srp_erp_documentapproved');
        return array('status' => 1, 'type' => 's', 'message' => 'New Version of Quotation Created Successfully.');
    }

    function document_drill_down_View_modal()
    {
        $this->db->select('srp_erp_customerinvoicedetails.invoiceAutoID,invoiceType,invoiceDate,invoiceCode,invoiceDueDate,customerName ,(contractAmount*(requestedQty/conversionRateUOM)) as contractAmount,transactionCurrencyDecimalPlaces');
        $this->db->from('srp_erp_customerinvoicedetails');
        $this->db->join('srp_erp_customerinvoicemaster', 'srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID');
        $this->db->where('contractAutoID', $this->input->post('contractAutoID'));
        $this->db->where('srp_erp_customerinvoicedetails.companyID', $this->common_data['company_data']['company_id']);
        return $this->db->get()->result_array();
    }

    function save_inv_tax_detail()
    {
        $this->db->select('taxMasterAutoID');
        $this->db->where('contractAutoID', $this->input->post('contractAutoID'));
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $tax_detail = $this->db->get('srp_erp_contracttaxdetails')->row_array();
        if (!empty($tax_detail)) {
            return array('status' => 1, 'type' => 'w', 'data' => ' Tax Detail added already ! ');
        }

        $this->db->trans_start();
        $this->db->select('*');
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $master = $this->db->get('srp_erp_taxmaster')->row_array();

        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('contractAutoID', $this->input->post('contractAutoID'));
        $inv_master = $this->db->get('srp_erp_contractmaster')->row_array();

        $data['contractAutoID'] = trim($this->input->post('contractAutoID'));
        $data['taxMasterAutoID'] = $master['taxMasterAutoID'];
        $data['taxDescription'] = $master['taxDescription'];
        $data['taxShortCode'] = $master['taxShortCode'];
        $data['supplierAutoID'] = $master['supplierAutoID'];
        $data['supplierSystemCode'] = $master['supplierSystemCode'];
        $data['supplierName'] = $master['supplierName'];
        $data['supplierCurrencyID'] = $master['supplierCurrencyID'];
        $data['supplierCurrency'] = $master['supplierCurrency'];
        $data['supplierCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
        $data['GLAutoID'] = $master['supplierGLAutoID'];
        $data['systemGLCode'] = $master['supplierGLSystemGLCode'];
        $data['GLCode'] = $master['supplierGLAccount'];
        $data['GLDescription'] = $master['supplierGLDescription'];
        $data['GLType'] = $master['supplierGLType'];
        $data['taxPercentage'] = trim($this->input->post('percentage'));
        $data['transactionAmount'] = trim($this->input->post('amount'));
        $data['transactionCurrencyID'] = $inv_master['transactionCurrencyID'];
        $data['transactionCurrency'] = $inv_master['transactionCurrency'];
        $data['transactionExchangeRate'] = $inv_master['transactionExchangeRate'];
        $data['transactionCurrencyDecimalPlaces'] = $inv_master['transactionCurrencyDecimalPlaces'];
        $data['companyLocalCurrencyID'] = $inv_master['companyLocalCurrencyID'];
        $data['companyLocalCurrency'] = $inv_master['companyLocalCurrency'];
        $data['companyLocalExchangeRate'] = $inv_master['companyLocalExchangeRate'];
        $data['companyReportingCurrencyID'] = $inv_master['companyReportingCurrencyID'];
        $data['companyReportingCurrency'] = $inv_master['companyReportingCurrency'];
        $data['companyReportingExchangeRate'] = $inv_master['companyReportingExchangeRate'];

        $supplierCurrency = currency_conversion($data['transactionCurrency'], $data['supplierCurrency']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('taxDetailAutoID'))) {
            $this->db->where('taxDetailAutoID', trim($this->input->post('taxDetailAutoID')));
            $this->db->update('srp_erp_contracttaxdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === 0) {
                $this->db->trans_rollback();
                return array('status' => 0, 'type' => 'e', 'data' => 'Tax Detail : ' . $data['GLDescription'] . ' Update Failed ');
            } else {
                $this->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'data' => 'Tax Detail : ' . $data['GLDescription'] . ' Updated Successfully.', 'last_id' => $this->input->post('taxDetailAutoID'));
            }
        } else {
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_contracttaxdetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => 0, 'type' => 'e', 'data' => 'Tax Detail : ' . $data['GLDescription'] . ' Save Failed ');
            } else {
                $this->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'data' => 'Tax Detail : ' . $data['GLDescription'] . ' Saved Successfully.', 'last_id' => $last_id);
            }
        }
    }

    function load_unitprice_exchangerate()
    { //get localwac amount into exchange rate

        $localwacAmount = trim($this->input->post('LocalWacAmount'));
        $this->db->select('transactionCurrencyID,transactionExchangeRate,transactionCurrency,companyLocalCurrency,transactionCurrencyDecimalPlaces');
        $this->db->where('contractAutoID', $this->input->post('contractAutoID'));
        $result = $this->db->get('srp_erp_contractmaster')->row_array();
        $localCurrency = currency_conversion($result['companyLocalCurrency'], $result['transactionCurrency']);
        $unitprice = round(($localwacAmount / $localCurrency['conversion']), $result['transactionCurrencyDecimalPlaces']);

        return array('status' => true, 'amount' => $unitprice);
    }

    function delete_quotationContract_attachement()
    {

        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments");
        $link = "$url/$myFileName";

        if (!unlink(UPLOAD_PATH . $link)) {
            echo json_encode(false);
        } else {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        }
    }

    function re_open_contract()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('contractAutoID', trim($this->input->post('contractAutoID')));
        $this->db->update('srp_erp_contractmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'Qut');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }

    function loademail()
    {
        $contractautoid = $this->input->post('contractAutoID');
        $this->db->select('srp_erp_contractmaster.*,srp_erp_customermaster.customerEmail as customerEmail');
        $this->db->where('contractAutoID', $contractautoid);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID', 'left');
        $this->db->from('srp_erp_contractmaster ');
        return $this->db->get()->row_array();
    }

    function send_quatation_email()
    {
        $contractid = trim($this->input->post('contractid'));
        $contractemail = trim($this->input->post('email'));
        $this->db->select('srp_erp_contractmaster.*,srp_erp_customermaster.customerEmail as customerEmail,srp_erp_customermaster.customerName as customerName');
        $this->db->where('contractAutoID', $contractid);
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID', 'left');
        $this->db->from('srp_erp_contractmaster ');
        $results = $this->db->get()->row_array();

        if (!empty($results)) {
            if ($results['customerEmail'] == '') {
                $data_master['customerEmail'] = $contractemail;
                $this->db->where('customerAutoID', $results['customerID']);
                $this->db->update('srp_erp_customermaster', $data_master);
            }
        }
        $this->db->select('customerEmail,customerName');
        $this->db->where('customerAutoID', $results['customerID']);
        $this->db->from('srp_erp_customermaster ');
        $customerMaster = $this->db->get()->row_array();

        $data['approval'] = $this->input->post('approval');
        $data['extra'] = $this->Quotation_contract_model->fetch_contract_template_data($contractid);
        $data['signature'] = $this->Quotation_contract_model->fetch_signaturelevel();

        $this->load->library('NumberToWords');
        $html = $this->load->view('system/quotation_contract/erp_contract_print', $data, true);
        $this->load->library('pdf');
        $path = UPLOAD_PATH.'/uploads/qu/'. $contractid .$results["documentID"] . current_userID() . ".pdf";
        $this->pdf->save_pdf($html, 'A4', 1, $path);


        if (!empty($customerMaster)) {
            if ($customerMaster['customerEmail'] != '') {
                $param = array();
                $param["empName"] = 'Sir/Madam';
                $param["body"] = 'we are pleased to submit our '.$results["contractType"].' as follows.<br/>
                                          <table border="0px">
                                          </table>';
                $mailData = [
                    'approvalEmpID' => '',
                    'documentCode' => '',
                    'toEmail' => $contractemail,
                    'subject' => $results["contractType"]. ' for ' .$customerMaster['customerName'],
                    'param' => $param
                ];
                send_approvalEmail($mailData, 1, $path);
                return array('s', 'Email Send Successfully.');
            } else {
                return array('e', 'Please enter an Email ID.');
            }
        }
    }
}