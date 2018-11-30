<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Procurement_modal extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function save_purchase_order_header()
    {
        $this->db->trans_start();
        $projectExist = project_is_exist();
        $date_format_policy = date_format_policy();
        $expectedDeliveryDate = trim($this->input->post('expectedDeliveryDate'));
        $POdate = trim($this->input->post('POdate'));
        $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate, $date_format_policy);
        $format_POdate = input_format_date($POdate, $date_format_policy);

        $segment = explode('|', trim($this->input->post('segment')));
        $currency_code = explode('|', trim($this->input->post('currency_code')));
        $supplier_arr = $this->fetch_supplier_data(trim($this->input->post('supplierPrimaryCode')));
        $ship_data = fetch_address_po(trim($this->input->post('shippingAddressID')));
        $sold_data = fetch_address_po(trim($this->input->post('soldToAddressID')));
        $invoice_data = fetch_address_po(trim($this->input->post('invoiceToAddressID')));
        $data['documentID'] = 'PO';
        $data['narration'] = trim_desc($this->input->post('narration'));
        $data['transactionCurrency'] = trim($this->input->post('transactionCurrency'));
        $data['supplierPrimaryCode'] = trim($this->input->post('supplierPrimaryCode'));
        $data['purchaseOrderType'] = trim($this->input->post('purchaseOrderType'));
        if ($projectExist == 1) {
            $projectCurrency = project_currency($this->input->post('projectID'));
            $projectCurrencyExchangerate = currency_conversionID($this->input->post('transactionCurrencyID'), $projectCurrency);
            $data['projectID'] = trim($this->input->post('projectID'));
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['referenceNumber'] = trim($this->input->post('referenceNumber'));
        $data['creditPeriod'] = trim($this->input->post('creditPeriod'));
        $data['soldToAddressID'] = trim($this->input->post('soldToAddressID'));
        $data['shippingAddressID'] = trim($this->input->post('shippingAddressID'));
        $data['invoiceToAddressID'] = trim($this->input->post('invoiceToAddressID'));
        $data['supplierID'] = $supplier_arr['supplierAutoID'];
        $data['supplierCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
        $data['supplierAddress'] = $supplier_arr['supplierAddress1'] . ' ' . $supplier_arr['supplierAddress2'];
        $data['supplierTelephone'] = $supplier_arr['supplierTelephone'];
        $data['supplierFax'] = $supplier_arr['supplierFax'];
        $data['supplierEmail'] = $supplier_arr['supplierEmail'];
        $data['expectedDeliveryDate'] = $format_expectedDeliveryDate;
        $data['documentDate'] = $format_POdate;
        $data['paymentTerms'] = trim_desc($this->input->post('paymentTerms'));
        $data['penaltyTerms'] = trim_desc($this->input->post('penaltyTerms'));
        $data['deliveryTerms'] = trim_desc($this->input->post('deliveryTerms'));
        $data['shippingAddressID'] = $ship_data['addressID'];
        $data['shippingAddressDescription'] = trim($this->input->post('shippingAddressDescription'));
        $data['shipTocontactPersonID'] = $ship_data['contactPerson'];
        $data['shipTocontactPersonTelephone'] = $ship_data['contactPersonTelephone'];
        $data['shipTocontactPersonFaxNo'] = $ship_data['contactPersonFaxNo'];
        $data['shipTocontactPersonEmail'] = $ship_data['contactPersonEmail'];
        $data['invoiceToAddressID'] = $invoice_data['addressID'];
        $data['invoiceToAddressDescription'] = $invoice_data['addressDescription'];
        $data['invoiceTocontactPersonID'] = $invoice_data['contactPerson'];
        $data['invoiceTocontactPersonTelephone'] = $invoice_data['contactPersonTelephone'];
        $data['invoiceTocontactPersonFaxNo'] = $invoice_data['contactPersonFaxNo'];
        $data['invoiceTocontactPersonEmail'] = $invoice_data['contactPersonEmail'];
        $data['soldToAddressID'] = $sold_data['addressID'];
        $data['soldToAddressDescription'] = $sold_data['addressDescription'];
        $data['soldTocontactPersonID'] = $sold_data['contactPerson'];
        $data['soldTocontactPersonTelephone'] = $sold_data['contactPersonTelephone'];
        $data['soldTocontactPersonFaxNo'] = $sold_data['contactPersonFaxNo'];
        $data['soldTocontactPersonEmail'] = $sold_data['contactPersonEmail'];
        $data['segmentID'] = trim($segment[0]);
        $data['segmentCode'] = trim($segment[1]);
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['contactPersonName'] = trim($this->input->post('contactperson'));
        $data['contactPersonNumber'] = trim($this->input->post('contactnumber'));
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

        $data['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $data['supplierCurrency'] = $supplier_arr['supplierCurrency'];
        $supplierCurrency = currency_conversionID($data['transactionCurrencyID'], $data['supplierCurrencyID']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];

        if (trim($this->input->post('purchaseOrderID'))) {
            $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
            $this->db->update('srp_erp_purchaseordermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Purchase Order For : ( ' . $data['supplierCode'] . ' ) ' . $data['supplierName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                //$this->lib_log->log_event('Purchase Order','Error','Purchase Order For : ( '.$data['supplierCode'].' ) '.$data['supplierName']. ' Update Failed '.$this->db->_error_message(),'Purchase Order');
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Purchase Order For : ( ' . $data['supplierCode'] . ' ) ' . $data['supplierName'] . ' Updated Successfully.');
                $this->db->trans_commit();
                //$this->lib_log->log_event('Purchase Order','Success','Purchase Order For : ( '.$data['supplierCode'].' ) '.$data['supplierName'].' Update Successfully. Affected Rows - ' . $this->db->affected_rows(),'Purchase Order');
                return array('status' => true, 'last_id' => $this->input->post('purchaseOrderID'), 'purchaseOrderType' => $this->input->post('purchaseOrderType'));
            }
        } else {
            //$this->load->library('sequence');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            //$data['purchaseOrderCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_purchaseordermaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Purchase Order For : ( ' . $data['supplierCode'] . ' ) ' . $data['supplierName'] . ' Save Failed ' . $this->db->_error_message());
                //$this->lib_log->log_event('Purchase Order','Error','Purchase Order For : ( '.$data['supplierCode'].' ) '.$this->input->post('desc') . ' Save Failed '.$this->db->_error_message(),'Purchase Order');
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Purchase Order For : ( ' . $data['supplierCode'] . ' ) ' . $data['supplierName'] . ' Saved Successfully.');
                //$this->lib_log->log_event('Purchase Order','Success','Purchase Order For : ( '.$data['supplierCode'].' ) '.$this->input->post('desc') . ' Save Successfully. Affected Rows - ' . $this->db->affected_rows(),'Purchase Order');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id, 'purchaseOrderType' => $this->input->post('purchaseOrderType'));
            }
        }
    }

    function save_uom()
    {
        $this->db->trans_start();
        $data['UnitShortCode'] = trim($this->input->post('UnitShortCode'));
        $data['UnitDes'] = trim($this->input->post('UnitDes'));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        if (trim($this->input->post('UnitID'))) {
            $this->db->where('UnitID', trim($this->input->post('UnitID')));
            $this->db->update('srp_erp_unit_of_measure', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Unit of measure Update Failed ');
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Unit of measure Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('UnitID'));
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_unit_of_measure', $data);
            $last_id = $this->db->insert_id();
            $this->db->insert('srp_erp_unitsconversion', array('masterUnitID' => $last_id, 'subUnitID' => $last_id, 'conversion' => 1, 'timestamp' => date('Y-m-d'), 'companyID' => $this->common_data['company_data']['company_id']));

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Unit of measure Save Failed ');
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Unit of measure Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_uom_conversion()
    {
        //$this->db->trans_start();
        $data['masterUnitID'] = trim($this->input->post('masterUnitID'));
        $data['subUnitID'] = trim($this->input->post('subUnitID'));
        $data['conversion'] = round($this->input->post('conversion'), 20);
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $this->db->insert('srp_erp_unitsconversion', $data);
        $last_id = $this->db->insert_id();

        $data['subUnitID'] = trim($this->input->post('masterUnitID'));
        $data['masterUnitID'] = trim($this->input->post('subUnitID'));
        $data['conversion'] = round((1 / $this->input->post('conversion')), 20);
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $this->db->insert('srp_erp_unitsconversion', $data);
        //$this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Unit of measure conversion Save Failed ');
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Unit of measure conversion Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }
    }

    function save_inv_tax_detail()
    {
        $this->db->select('taxMasterAutoID');
        $this->db->where('purchaseOrderAutoID', $this->input->post('purchaseOrderID'));
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $tax_detail = $this->db->get('srp_erp_purchaseordertaxdetails')->row_array();
        if (!empty($tax_detail)) {
            return array('status' => 1, 'type' => 'w', 'data' => ' Tax Detail added already ! ');
        }

        $this->db->trans_start();
        $this->db->select('*');
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $master = $this->db->get('srp_erp_taxmaster')->row_array();

        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('purchaseOrderID', $this->input->post('purchaseOrderID'));
        $inv_master = $this->db->get('srp_erp_purchaseordermaster')->row_array();

        $data['purchaseOrderAutoID'] = trim($this->input->post('purchaseOrderID'));
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
            $this->db->update('srp_erp_purchaseordertaxdetails', $data);
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
            $this->db->insert('srp_erp_purchaseordertaxdetails', $data);
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

    function change_conversion()
    {
        $this->db->trans_start();
        $data['masterUnitID'] = trim($this->input->post('masterUnitID'));
        $data['subUnitID'] = trim($this->input->post('subUnitID'));
        $data['conversion'] = round($this->input->post('conversion'), 20);
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $this->db->where('masterUnitID', $data['masterUnitID']);
        $this->db->where('subUnitID', $data['subUnitID']);
        $this->db->update('srp_erp_unitsconversion', $data);

        $data['subUnitID'] = trim($this->input->post('masterUnitID'));
        $data['masterUnitID'] = trim($this->input->post('subUnitID'));
        $data['conversion'] = round((1 / $this->input->post('conversion')), 20);
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $this->db->where('masterUnitID', $data['masterUnitID']);
        $this->db->where('subUnitID', $data['subUnitID']);
        $this->db->update('srp_erp_unitsconversion', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Unit of measure conversion Update Failed ');
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Unit of measure conversion Updated Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function save_purchase_order_detail()
    {
        $purchaseOrderDetailsID = $this->input->post('purchaseOrderDetailsID');
        $purchaseOrderID = $this->input->post('purchaseOrderID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $quantityRequested = $this->input->post('quantityRequested');
        $discount = $this->input->post('discount');
        $comment = $this->input->post('comment');

        $this->db->trans_start();

        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $item_arr = fetch_item_data($itemAutoID);
            $uomEx = explode('|', $uom[$key]);
            if (!$purchaseOrderDetailsID) {
                $this->db->select('purchaseOrderID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_purchaseorderdetails');
                $this->db->where('itemType', 'Inventory');
                $this->db->where('purchaseOrderID', $purchaseOrderID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Purchase Order Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $data['purchaseOrderID'] = $purchaseOrderID;
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_arr['itemSystemCode'];
            $data['itemType'] = $item_arr['mainCategory'];
            $data['itemDescription'] = $item_arr['itemDescription'];
            $data['unitOfMeasure'] = trim($uomEx[0]);
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['discountPercentage'] = $discount[$key];
            $data['discountAmount'] = ($estimatedAmount[$key] / 100) * $discount[$key];
            $data['requestedQty'] = $quantityRequested[$key];
            $data['unitAmount'] = ($estimatedAmount[$key] - $data['discountAmount']);
            $data['totalAmount'] = ($data['unitAmount'] * $quantityRequested[$key]);
            $data['comment'] = $comment[$key];
            $data['remarks'] = '';

            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['GRVSelectedYN'] = 0;
            $data['goodsRecievedYN'] = 0;

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_purchaseorderdetails', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Purchase Order Details :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Purchase Order Details :  Saved Successfully.');
        }

    }

    function update_purchase_order_detail()
    {
        if (!empty($this->input->post('purchaseOrderDetailsID'))) {
            $this->db->select('purchaseOrderID,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_purchaseorderdetails');
            $this->db->where('itemType', 'Inventory');
            $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
            $this->db->where('purchaseOrderDetailsID !=', trim($this->input->post('purchaseOrderDetailsID')));
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('w', 'Purchase Order Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }
        $this->db->trans_start();
        $item_arr = fetch_item_data(trim($this->input->post('itemAutoID')));
        $uom = explode('|', $this->input->post('uom'));
        $data['purchaseOrderID'] = trim($this->input->post('purchaseOrderID'));
        $data['itemAutoID'] = trim($this->input->post('itemAutoID'));
        $data['itemType'] = $item_arr['mainCategory'];
        $data['itemSystemCode'] = $item_arr['itemSystemCode'];
        $data['itemDescription'] = $item_arr['itemDescription'];
        $data['unitOfMeasure'] = trim($uom[0]);
        $data['unitOfMeasureID'] = trim($this->input->post('UnitOfMeasureID'));
        $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['discountPercentage'] = trim($this->input->post('discount'));
        $data['discountAmount'] = (trim($this->input->post('estimatedAmount')) / 100) * trim($this->input->post('discount'));
        $data['requestedQty'] = trim($this->input->post('quantityRequested'));
        $data['unitAmount'] = (trim($this->input->post('estimatedAmount')) - $data['discountAmount']);
        $data['totalAmount'] = ($data['unitAmount'] * trim($this->input->post('quantityRequested')));
        $data['comment'] = trim($this->input->post('comment'));
        $data['remarks'] = trim($this->input->post('remarks'));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('purchaseOrderDetailsID'))) {
            $this->db->where('purchaseOrderDetailsID', trim($this->input->post('purchaseOrderDetailsID')));
            $this->db->update('srp_erp_purchaseorderdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Purchase Order Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Purchase Order Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');

            }
        }
    }

    function conversionRateUOM($umo, $default_umo)
    {
        $this->db->select('UnitID');
        $this->db->where('UnitShortCode', $default_umo);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $masterUnitID = $this->db->get('srp_erp_unit_of_measure')->row('UnitID');

        $this->db->select('UnitID');
        $this->db->where('UnitShortCode', $umo);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $subUnitID = $this->db->get('srp_erp_unit_of_measure')->row('UnitID');

        $this->db->select('conversion');
        $this->db->from('srp_erp_unitsconversion');
        $this->db->where('masterUnitID', $masterUnitID);
        $this->db->where('subUnitID', $subUnitID);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        return $this->db->get()->row('conversion');
    }

    function fetch_supplier_data($supplierID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID);
        return $this->db->get()->row_array();
    }

    function load_purchase_order_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate');
        $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
        $this->db->from('srp_erp_purchaseordermaster');
        return $this->db->get()->row_array();
    }

    function fetch_itemrecode_po()
    {
        $dataArr = array();
        $dataArr2 = array();
        $dataArr2['query'] = 'test';
        $companyCode = $this->common_data['company_data']['company_code'];
        $search_string = "%" . $_GET['query'] . "%";
        $data = $this->db->query('SELECT
                                mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,
                                CONCAT( IFNULL(itemDescription,"empty"), " - ", IFNULL(itemSystemCode,"empty"), " - ", IFNULL(partNo,"empty")  , " - ", IFNULL(seconeryItemCode,"empty")) AS "Match",
                                isSubitemExist 
                            FROM
                                srp_erp_itemmaster 
                            WHERE
                                ( itemSystemCode LIKE "' . $search_string . '" OR 
                                itemDescription LIKE "' . $search_string . '" OR 
                                seconeryItemCode LIKE "' . $search_string . '" OR 
                                partNo LIKE "' . $search_string . '" OR 
                                itemName LIKE "' . $search_string . '" ) 
                                AND companyCode = "' . $companyCode . '" 
                                AND isActive = "1"')->result_array();

        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['itemSystemCode'], 'itemAutoID' => $val['itemAutoID'], 'currentStock' => $val['currentStock'], 'defaultUnitOfMeasure' => $val['defaultUnitOfMeasure'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'companyLocalSellingPrice' => $val['companyLocalSellingPrice'], 'companyLocalWacAmount' => $val['companyLocalWacAmount'], 'isSubitemExist' => $val['isSubitemExist'], 'revanueGLCode' => $val['revanueGLCode']);
            }

        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_itemrecode()
    {
        $companyCode = $this->common_data['company_data']['company_code'];
        $search_string = "%" . $_GET['q'] . "%";
        return $this->db->query('SELECT mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT(itemDescription, " (" ,itemSystemCode,")") AS "Match" FROM srp_erp_itemmaster WHERE (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR seconeryItemCode LIKE "' . $search_string . '") AND companyCode = "' . $companyCode . '" AND isActive="1"')->result_array();
    }

    function fetch_po_detail_table()
    {
        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces');
        $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
        $this->db->from('srp_erp_purchaseordermaster');
        $data['currency'] = $this->db->get()->row_array();
        $this->db->select('*');
        $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
        $this->db->from('srp_erp_purchaseorderdetails');
        $data['detail'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('purchaseOrderAutoID', trim($this->input->post('purchaseOrderID')));
        $this->db->from('srp_erp_purchaseordertaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();
        return $data;
    }

    function delete_purchase_order_detail()
    {
        $this->db->delete('srp_erp_purchaseorderdetails', array('purchaseOrderDetailsID' => trim($this->input->post('purchaseOrderDetailsID'))));
        return true;
    }

    function delete_tax_detail()
    {
        $this->db->delete('srp_erp_purchaseordertaxdetails', array('taxDetailAutoID' => trim($this->input->post('taxDetailAutoID'))));
        return true;
    }

    function delete_purchase_order()
    {
        /*$this->db->delete('srp_erp_purchaseordermaster', array('purchaseOrderID' => trim($this->input->post('purchaseOrderID'))));
        $this->db->delete('srp_erp_purchaseorderdetails', array('purchaseOrderID' => trim($this->input->post('purchaseOrderID'))));
        return true;*/
        $this->db->select('*');
        $this->db->from('srp_erp_purchaseorderdetails');
        $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
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
            $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
            $this->db->update('srp_erp_purchaseordermaster', $data);
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;

        }
    }

    function fetch_purchase_order_detail()
    {
        $this->db->select('*');
        $this->db->where('purchaseOrderDetailsID', trim($this->input->post('purchaseOrderDetailsID')));
        $this->db->from('srp_erp_purchaseorderdetails');
        return $this->db->get()->row_array();
    }

    function fetch_template_data($purchaseOrderID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('purchaseOrderID,supplierID,transactionCurrency,transactionCurrencyDecimalPlaces,purchaseOrderCode,shippingAddressDescription, DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,referenceNumber,soldToAddressDescription,soldTocontactPersonID,soldTocontactPersonTelephone,supplierName,supplierTelephone,supplierEmail,soldTocontactPersonEmail,supplierFax,soldTocontactPersonFaxNo,supplierAddress,invoiceToAddressDescription,shipTocontactPersonID,invoiceTocontactPersonID,shipTocontactPersonTelephone,invoiceTocontactPersonTelephone,shipTocontactPersonFaxNo,invoiceTocontactPersonFaxNo,shipTocontactPersonEmail,invoiceTocontactPersonEmail,narration,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,paymentTerms,deliveryTerms,confirmedByName,confirmedYN,DATE_FORMAT(confirmedDate,\'' . $convertFormat . '\') AS confirmedDate,approvedbyEmpID,approvedbyEmpName,approvedYN,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,supplierCode,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate,segmentCode,penaltyTerms');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $this->db->from('srp_erp_purchaseordermaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('supplierSystemCode,supplierName,supplierAddress1,supplierTelephone,supplierFax,supplierEmail');
        $this->db->where('supplierAutoID', $data['master']['supplierID']);
        $this->db->from('srp_erp_suppliermaster');
        $data['supplier'] = $this->db->get()->row_array();

        $this->db->select('itemSystemCode,itemDescription,unitOfMeasure,requestedQty,unitAmount,discountAmount,comment, totalAmount, discountPercentage');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $this->db->from('srp_erp_purchaseorderdetails');
        $data['detail'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('purchaseOrderAutoID', $purchaseOrderID);
        $data['tax'] = $this->db->get('srp_erp_purchaseordertaxdetails')->result_array();
        $this->db->select('approvedYN, approvedDate, approvalLevelID,Ename1,Ename2,Ename3,Ename4');
        $this->db->where('documentSystemCode', $purchaseOrderID);
        $this->db->where('documentID', 'PO');
        $this->db->from('srp_erp_documentapproved');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.ECode = srp_erp_documentapproved.approvedEmpID');
        $data['approval'] = $this->db->get()->result_array();
        return $data;
    }

    function purchase_order_confirmation()
    {
        $this->db->select('purchaseOrderID');
        $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
        $this->db->from('srp_erp_purchaseorderdetails');
        $record = $this->db->get()->result_array();
        if (empty($record)) {
            $this->session->set_flashdata('w', 'There are no records to confirm this document!');
            return false;
        } else {

            $this->db->select('purchaseOrderID');
            $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_purchaseordermaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $this->load->library('approvals');
                $this->db->select('purchaseOrderCode,supplierCurrencyExchangeRate,companyReportingExchangeRate,companyLocalExchangeRate ,purchaseOrderID,transactionCurrencyDecimalPlaces,documentDate,DATE_FORMAT(documentDate, "%Y") as invYear,DATE_FORMAT(documentDate, "%m") as invMonth,documentID');
                $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
                $this->db->from('srp_erp_purchaseordermaster');
                $po_data = $this->db->get()->row_array();
                $docDate = $po_data['documentDate'];

                $Comp = current_companyID();
                $companyFinanceYearID = $this->db->query("SELECT
	period.companyFinanceYearID as companyFinanceYearID
FROM
	srp_erp_companyfinanceperiod period
WHERE
	period.companyID = $Comp
AND '$docDate' BETWEEN period.dateFrom
AND period.dateTo
AND period.isActive = 1")->row_array();

                if (empty($companyFinanceYearID['companyFinanceYearID'])) {
                    $companyFinanceYearID['companyFinanceYearID'] = NULL;
                }

                $this->load->library('sequence');
                if ($po_data['purchaseOrderCode'] == "0" || empty($po_data['purchaseOrderCode'])) {
                    $pvCd = array(
                        'purchaseOrderCode' => $this->sequence->sequence_generator_fin($po_data['documentID'], $companyFinanceYearID['companyFinanceYearID'], $po_data['invYear'], $po_data['invMonth'])
                    );
                    $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
                    $this->db->update('srp_erp_purchaseordermaster', $pvCd);
                }

                $this->load->library('approvals');
                $this->db->select('purchaseOrderCode,supplierCurrencyExchangeRate,companyReportingExchangeRate,companyLocalExchangeRate ,purchaseOrderID,transactionCurrencyDecimalPlaces,documentDate');
                $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
                $this->db->from('srp_erp_purchaseordermaster');
                $po_master = $this->db->get()->row_array();

                $approvals_status = $this->approvals->CreateApproval('PO', $po_master['purchaseOrderID'], $po_master['purchaseOrderCode'], 'Purchase Order', 'srp_erp_purchaseordermaster', 'purchaseOrderID');
                if ($approvals_status == 1) {
                    $this->db->select_sum('totalAmount');
                    $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
                    $po_total = $this->db->get('srp_erp_purchaseorderdetails')->row('totalAmount');
                    $data = array(
                        'confirmedYN' => 1,
                        'approvedYN' => 0,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user'],
                        'transactionAmount' => round($po_total, $po_data['transactionCurrencyDecimalPlaces']),
                        'companyLocalAmount' => ($po_total / $po_data['companyLocalExchangeRate']),
                        'companyReportingAmount' => ($po_total / $po_data['companyReportingExchangeRate']),
                        'supplierCurrencyAmount' => ($po_total / $po_data['supplierCurrencyExchangeRate']),
                        'isReceived' => 0,
                    );
                    $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
                    $this->db->update('srp_erp_purchaseordermaster', $data);
                    $this->session->set_flashdata('s', 'Approvals Created Successfully ');
                    return true;
                } else {
                    return false;
                }
            }

        }

    }

    function save_purchase_order_approval()
    {
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_code = trim($this->input->post('purchaseOrderID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('po_status'));
        $comments = trim($this->input->post('comments'));

        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'PO');
        if ($approvals_status == 1) {
            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];
            //$data['companyLocalAmount']     = $company_loc_tot;
            //$data['companyReportingAmount'] = $company_rpt_tot;
            //$data['supplierCurrencyAmount'] = $supplier_cr_tot;
            //$data['transactionAmount']      = $transaction_loc_tot;

            $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
            $this->db->update('srp_erp_purchaseordermaster', $data);

            $this->session->set_flashdata('s', 'Document Approved Successfully.');
        } else {
            $this->session->set_flashdata('s', 'Approval Rejected Successfully.');
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

    function save_purchase_order_close()
    {
        $this->db->trans_start();
        $system_code = trim($this->input->post('purchaseOrderID'));

        $data['closedYN'] = 1;
        $data['closedDate'] = $this->input->post('closedDate');
        $data['closedReason'] = trim($this->input->post('comments'));
        $data['approvedYN'] = 5;
        $data['approvedbyEmpID'] = $this->common_data['current_userID'];
        $data['approvedbyEmpName'] = $this->common_data['current_user'];
        $data['approvedDate'] = $this->common_data['current_date'];

        $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
        $this->db->update('srp_erp_purchaseordermaster', $data);
        $this->session->set_flashdata('s', 'Purchase Order Closed Successfully.');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function fetch_convertion_detail_table()
    {
        $this->db->select('subUnitID,conversion,s.UnitShortCode as sub_code,s.UnitDes as sub_dese,m.UnitShortCode as m_code,m.UnitDes as m_dese');
        $this->db->where('masterUnitID', trim($this->input->post('masterUnitID')));
        $this->db->where('srp_erp_unitsconversion.companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_unitsconversion');
        $this->db->join('srp_erp_unit_of_measure s', 's.UnitID = srp_erp_unitsconversion.subUnitID');
        $this->db->join('srp_erp_unit_of_measure m', 'm.UnitID = srp_erp_unitsconversion.masterUnitID');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('UnitID,UnitShortCode,UnitDes');
        $this->db->where('UnitID !=', trim($this->input->post('masterUnitID')));
        $this->db->where('srp_erp_unit_of_measure.companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_unit_of_measure');
        //$this->db->join('srp_erp_unitsconversion', 'srp_erp_unitsconversion.subUnitID != srp_erp_unit_of_measure.UnitID','inner');
        $data['drop'] = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $data;
    }

    function fetch_supplier_currency()
    {
        $this->db->select('supplierCurrency');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', trim($this->input->post('supplierAutoID')));
        return $this->db->get()->row_array();
    }

    function fetch_supplier_currency_by_id()
    {
        $this->db->select('supplierCurrencyID,supplierCreditPeriod');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', trim($this->input->post('supplierAutoID')));
        return $this->db->get()->row_array();
    }

    function fetch_customer_currency()
    {
        $this->db->select('customerCurrency');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', trim($this->input->post('customerAutoID')));
        return $this->db->get()->row_array();
    }


    function delete_purchaseOrder_attachement()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            return false;
        } else {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        }
    }

    function re_open_procurement()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
        $this->db->update('srp_erp_purchaseordermaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function fetch_prq_code()
    {
        $purchaseOrderID = $this->input->post('purchaseOrderID');

        $this->db->select('documentDate,transactionCurrencyID');
        $this->db->from('srp_erp_purchaseordermaster');
        $this->db->where('purchaseOrderID', trim($purchaseOrderID));
        $result = $this->db->get()->row_array();

        $documentDate=$result['documentDate'];
        $transactionCurrencyID=$result['transactionCurrencyID'];
        $companyID=current_companyID();

        $data = $this->db->query("SELECT
	prqm.purchaseRequestID,
	purchaseRequestCode,
	documentDate,
	requestedByName,
	SUM(prqdetail.prQty) as prQty,
	SUM(prqdetail.requestedQty) as requestedQty
FROM
	srp_erp_purchaserequestmaster prqm
LEFT JOIN (
	SELECT
		prqd.purchaseRequestDetailsID,
		prqd.purchaseRequestID,
		prqd.requestedQty,
		sum(
			srp_erp_purchaseorderdetails.requestedQty
		) AS prQty
	FROM
	srp_erp_purchaserequestdetails prqd
	LEFT JOIN srp_erp_purchaseorderdetails ON srp_erp_purchaseorderdetails.prDetailID = prqd.purchaseRequestDetailsID
	GROUP BY
		purchaseRequestDetailsID
) prqdetail ON prqdetail.purchaseRequestID = prqm.purchaseRequestID
WHERE
	documentDate <= '$documentDate'
AND prqm.transactionCurrencyID = '$transactionCurrencyID'
AND prqm.companyID = '$companyID'
AND prqm.approvedYN = 1
GROUP BY
		prqm.purchaseRequestID")->result_array();
return $data;
        /*$this->db->select('purchaseRequestID,purchaseRequestCode,documentDate,requestedByName');
        $this->db->from('srp_erp_purchaserequestmaster');
        $this->db->where('documentDate <=', trim($result['documentDate']));
        $this->db->where('transactionCurrencyID', trim($result['transactionCurrencyID']));
        $this->db->where('companyID', trim(current_companyID()));
        $this->db->where('approvedYN', 1);
        return $this->db->get()->result_array();*/
    }

    function fetch_prq_detail_table()
    {
        $this->db->select('srp_erp_purchaserequestdetails.*,sum(srp_erp_purchaseorderdetails.requestedQty) AS prQty');
        $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID')));
        $this->db->from('srp_erp_purchaserequestdetails');
        $this->db->join('srp_erp_purchaseorderdetails', 'srp_erp_purchaseorderdetails.prDetailID = srp_erp_purchaserequestdetails.purchaseRequestDetailsID', 'left');
        $this->db->group_by("purchaseRequestDetailsID");
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }


    function save_prq_base_items()
    {
        //$post = $this->input->post();

        $this->db->trans_start();
        $items_arr = array();
        $this->db->select('srp_erp_purchaserequestdetails.*,sum(srp_erp_purchaseorderdetails.prQty) AS prQty,srp_erp_purchaserequestmaster.purchaseRequestCode');
        $this->db->from('srp_erp_purchaserequestdetails');
        $this->db->where_in('srp_erp_purchaserequestdetails.purchaseRequestDetailsID', $this->input->post('DetailsID'));
        $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_purchaserequestdetails.purchaseRequestID');
        $this->db->join('srp_erp_purchaseorderdetails', 'srp_erp_purchaseorderdetails.prDetailID = srp_erp_purchaserequestdetails.purchaseRequestDetailsID', 'left');
        $this->db->group_by("purchaseRequestDetailsID");
        $query = $this->db->get()->result_array();

        $qty = $this->input->post('qty');
        $amount = $this->input->post('amount');
        $discountPercentage = $this->input->post('discount');
        $discountAmount = $this->input->post('discountamt');
        for ($i = 0; $i < count($query); $i++) {
            $this->db->select('prMasterID');
            $this->db->from('srp_erp_purchaseorderdetails');
            $this->db->where('prMasterID', $query[$i]['purchaseRequestID']);
            $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID')));
            $this->db->where('itemAutoID', $query[$i]['itemAutoID']);
            $order_detail = $this->db->get()->result_array();


            if (!empty($order_detail)) {
                $this->session->set_flashdata('w', 'Purchase Request Details added already.');
            } else {

                $data[$i]['prMasterID'] = $query[$i]['purchaseRequestID'];
                // $data[$i]['purchaseRequestCode'] = $query[$i]['purchaseRequestCode'];
                $data[$i]['prDetailID'] = $query[$i]['purchaseRequestDetailsID'];
                $data[$i]['purchaseOrderID'] = trim($this->input->post('purchaseOrderID'));
                $data[$i]['itemAutoID'] = $query[$i]['itemAutoID'];
                $data[$i]['itemSystemCode'] = $query[$i]['itemSystemCode'];
                $data[$i]['itemDescription'] = $query[$i]['itemDescription'];
                $data[$i]['defaultUOM'] = $query[$i]['defaultUOM'];
                $data[$i]['defaultUOMID'] = $query[$i]['defaultUOMID'];
                $data[$i]['unitOfMeasure'] = $query[$i]['unitOfMeasure'];
                $data[$i]['unitOfMeasureID'] = $query[$i]['unitOfMeasureID'];
                $data[$i]['conversionRateUOM'] = $query[$i]['conversionRateUOM'];
                $data[$i]['requestedQty'] = $qty[$i];
                $data[$i]['prQty'] = $query[$i]['requestedQty'];
                $data[$i]['discountPercentage'] = $discountPercentage[$i];
                $data[$i]['discountAmount'] = $discountAmount[$i];
                $data[$i]['unitAmount'] = $amount[$i] - $discountAmount[$i];
                $data[$i]['comment'] = $query[$i]['comment'];
                $data[$i]['totalAmount'] = ($amount[$i] - $discountAmount[$i]) * $qty[$i];

                $data[$i]['comment'] = $query[$i]['comment'];
                $data[$i]['remarks'] = $query[$i]['remarks'];
                $data[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                $data[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $data[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $data[$i]['modifiedUserName'] = $this->common_data['current_user'];
                $data[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $data[$i]['createdPCID'] = $this->common_data['current_pc'];
                $data[$i]['createdUserID'] = $this->common_data['current_userID'];
                $data[$i]['createdUserName'] = $this->common_data['current_user'];
                $data[$i]['createdDateTime'] = $this->common_data['current_date'];

            }
        }

        if (!empty($data)) {

            //print_r($data);
            $this->db->insert_batch('srp_erp_purchaseorderdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Purchase Request : Details Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Purchase Request : Item Details Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true);
            }
        } else {
            return array('status' => false, 'data' => 'Purchase Request Details added already.');
        }
    }


    function fetch_last_grn_amount()
    {
        $itemAutoId = $this->input->post('itemAutoId');
        $currencyID = $this->input->post('currencyID');
        $supplierPrimaryCode = $this->input->post('supplierPrimaryCode');
        $data = $this->db->query('SELECT
	grvdetails.receivedAmount
FROM
	srp_erp_grvdetails grvdetails
JOIN srp_erp_grvmaster grvmaster ON grvdetails.grvAutoID=grvmaster.grvAutoID

where grvmaster.approvedYN=1 and  grvmaster.transactionCurrencyID=' . $currencyID . ' and itemAutoID=' . $itemAutoId . '
and grvmaster.supplierID=' . $supplierPrimaryCode . '
and grvDate=(SELECT
	max(grvmaster.grvDate) as maxdate
FROM
	srp_erp_grvdetails grvdetails
JOIN srp_erp_grvmaster grvmaster ON grvdetails.grvAutoID=grvmaster.grvAutoID

where grvmaster.approvedYN=1 and  grvmaster.transactionCurrencyID=' . $currencyID . ' and itemAutoID=' . $itemAutoId . ' and grvmaster.supplierID=' . $supplierPrimaryCode . '
)')->row_array();
        return $data;
    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'PO');
        $this->db->from('srp_erp_documentcodemaster ');
        return $this->db->get()->row_array();


    }

    function loademail()
    {
        $poid = $this->input->post('purchaseOrderID');
        $this->db->select('srp_erp_purchaseordermaster.*,srp_erp_suppliermaster.supplierEmail as supplierEmail');
        $this->db->where('purchaseOrderID', $poid);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster.supplierID', 'left');
        $this->db->from('srp_erp_purchaseordermaster ');
        return $this->db->get()->row_array();

    }

    function send_po_email()
    {
        $poid = trim($this->input->post('purchaseOrderID'));
        $supplierEmail = trim($this->input->post('email'));
        $this->db->select('srp_erp_purchaseordermaster.*,srp_erp_suppliermaster.supplierEmail as supplierEmail,srp_erp_suppliermaster.supplierName as supplierName');
        $this->db->where('purchaseOrderID', $poid);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster.supplierID', 'left');
        $this->db->from('srp_erp_purchaseordermaster ');
        $results = $this->db->get()->row_array();

        if (!empty($results)) {
            if ($results['supplierEmail'] == '') {
                $data_master['supplierEmail'] = $supplierEmail;
                $this->db->where('supplierAutoID', $results['supplierID']);
                $this->db->update('srp_erp_suppliermaster', $data_master);
            }
        }

        $data['approval'] = $this->input->post('approval');
        $data['extra'] = $this->Procurement_modal->fetch_template_data($poid);
        $data['signature'] = $this->Procurement_modal->fetch_signaturelevel();
        $data['printHeaderFooterYN'] = 1;
        $this->load->library('NumberToWords');
        $html = $this->load->view('system/procurement/erp_purchase_order_print', $data, true);
        $this->load->library('pdf');
        $path = UPLOAD_PATH . "/uploads/po/" . $poid . "-PO-" . current_userID() . ".pdf";
        $this->pdf->save_pdf($html, 'A4', 1, $path);

        $this->db->select('supplierEmail,supplierName');
        $this->db->where('supplierAutoID', $results['supplierID']);
        $this->db->from('srp_erp_suppliermaster ');
        $supplierMaster = $this->db->get()->row_array();

        if (!empty($supplierMaster)) {
            if ($supplierMaster['supplierEmail'] != '') {
                $param = array();
                $param["empName"] = 'Sir/Madam';
                $param["body"] = 'we are pleased to submit our purchase order as follows.<br/>
                                          <table border="0px">
                                          </table>';
                $mailData = [
                    'approvalEmpID' => '',
                    'documentCode' => '',
                    'toEmail' => $supplierEmail,
                    'subject' => ' Purchase Order of ' . $supplierMaster['supplierName'],
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
