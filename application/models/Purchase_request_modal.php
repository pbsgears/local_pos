<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Purchase_request_modal extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function save_purchase_request_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $expectedDeliveryDate = trim($this->input->post('expectedDeliveryDate'));
        $Pqrdate = trim($this->input->post('documentDate'));
        $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate, $date_format_policy);
        $format_POdate = input_format_date($Pqrdate, $date_format_policy);

        $segment = explode('|', trim($this->input->post('segment')));
        $currency_code = explode('|', trim($this->input->post('currency_code')));


        $data['documentID'] = 'PRQ';
        $data['projectID'] = trim($this->input->post('projectID'));
        $data['requestedEmpID'] = trim($this->input->post('requestedEmpID'));
        $data['requestedByName'] = trim($this->input->post('requestedByName'));
        $data['narration'] = trim_desc($this->input->post('narration'));
        $data['transactionCurrency'] = trim($this->input->post('transactionCurrency'));
        $data['referenceNumber'] = trim($this->input->post('referenceNumber'));
        $data['expectedDeliveryDate'] = $format_expectedDeliveryDate;
        $data['documentDate'] = $format_POdate;
        $data['segmentID'] = trim($segment[0]);
        $data['segmentCode'] = trim($segment[1]);

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
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


        if (trim($this->input->post('purchaseRequestID'))) {
            $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID')));
            $this->db->update('srp_erp_purchaserequestmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Purchase Request Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Purchase Request Updated Successfully.');
                $this->db->trans_commit();
                //$this->lib_log->log_event('Purchase Order','Success','Purchase Order For : ( '.$data['supplierCode'].' ) '.$data['supplierName'].' Update Successfully. Affected Rows - ' . $this->db->affected_rows(),'Purchase Order');
                return array('status' => true, 'last_id' => $this->input->post('purchaseRequestID'));
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
            //$data['purchaseRequestCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_purchaserequestmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Purchase Request Save Failed ' . $this->db->_error_message());
                //$this->lib_log->log_event('Purchase Order','Error','Purchase Order For : ( '.$data['supplierCode'].' ) '.$this->input->post('desc') . ' Save Failed '.$this->db->_error_message(),'Purchase Order');
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Purchase Request Saved Successfully.');
                //$this->lib_log->log_event('Purchase Order','Success','Purchase Order For : ( '.$data['supplierCode'].' ) '.$this->input->post('desc') . ' Save Successfully. Affected Rows - ' . $this->db->affected_rows(),'Purchase Order');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
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

    function save_purchase_request_detail()
    {
        $purchaseRequestDetailsID = $this->input->post('purchaseRequestDetailsID');
        $purchaseRequestID = $this->input->post('purchaseRequestID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $quantityRequested = $this->input->post('quantityRequested');
        $discount = $this->input->post('discount');
        $comment = $this->input->post('comment');
        $expectedDelDate = $this->input->post('expectedDeliveryDateDetail');

        $this->db->trans_start();

        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $item_arr = fetch_item_data($itemAutoID);
            $uomEx = explode('|', $uom[$key]);
            if (!$purchaseRequestDetailsID) {
                $this->db->select('purchaseRequestID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_purchaserequestdetails');
                $this->db->where('itemType', 'Inventory');
                $this->db->where('purchaseRequestID', $purchaseRequestID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Purchase Request Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $date_format_policy = date_format_policy();
            $expectedDeliveryDate = $expectedDelDate[$key];
            $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate, $date_format_policy);

            $data['purchaseRequestID'] = $purchaseRequestID;
            $data['expectedDeliveryDate'] = $format_expectedDeliveryDate;
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
            // $data['GRVSelectedYN'] = 0;
            //$data['goodsRecievedYN'] = 0;

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_purchaserequestdetails', $data);
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

    function update_purchase_request_detail()
    {
        if (!empty($this->input->post('purchaseRequestDetailsID'))) {
            $this->db->select('purchaseRequestID,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_purchaserequestdetails');
            $this->db->where('itemType', 'Inventory');
            $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID')));
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
            $this->db->where('purchaseRequestDetailsID !=', trim($this->input->post('purchaseRequestDetailsID')));
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('w', 'Purchase Request Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }
        $this->db->trans_start();
        $item_arr = fetch_item_data(trim($this->input->post('itemAutoID')));
        $uom = explode('|', $this->input->post('uom'));
        $date_format_policy = date_format_policy();
        $expectedDeliveryDate = trim($this->input->post('expectedDeliveryDateDetailEdit'));
        $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate, $date_format_policy);
        $data['purchaseRequestID'] = trim($this->input->post('purchaseRequestID'));
        $data['itemAutoID'] = trim($this->input->post('itemAutoID'));
        $data['expectedDeliveryDate'] = $format_expectedDeliveryDate;
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

        if (trim($this->input->post('purchaseRequestDetailsID'))) {
            $this->db->where('purchaseRequestDetailsID', trim($this->input->post('purchaseRequestDetailsID')));
            $this->db->update('srp_erp_purchaserequestdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Purchase Request Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Purchase Request Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');

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

    function load_purchase_request_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate');
        $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID')));
        $this->db->from('srp_erp_purchaserequestmaster');
        return $this->db->get()->row_array();
    }

    function fetch_itemrecode_pqr()
    {
        $dataArr = array();
        $dataArr2 = array();
        $dataArr2['query'] = 'test';
        $companyCode = $this->common_data['company_data']['company_code'];
        $search_string = "%" . $_GET['query'] . "%";
        $data = $this->db->query('SELECT mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT( IFNULL(itemDescription,"empty"), " - ", IFNULL(itemSystemCode,"empty"), " - ", IFNULL(partNo,"empty")  , " - ", IFNULL(seconeryItemCode,"empty")) AS "Match" , isSubitemExist FROM srp_erp_itemmaster WHERE (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR seconeryItemCode LIKE "' . $search_string . '") AND companyCode = "' . $companyCode . '" AND isActive="1"')->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['itemSystemCode'], 'itemAutoID' => $val['itemAutoID'], 'currentStock' => $val['currentStock'], 'defaultUnitOfMeasure' => $val['defaultUnitOfMeasure'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'companyLocalSellingPrice' => $val['companyLocalSellingPrice'], 'companyLocalWacAmount' => $val['companyLocalWacAmount'], 'isSubitemExist' => $val['isSubitemExist']);
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

    function fetch_pqr_detail_table()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces');
        $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID')));
        $this->db->from('srp_erp_purchaserequestmaster');
        $data['currency'] = $this->db->get()->row_array();
        $this->db->select('*,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate');
        $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID')));
        $this->db->from('srp_erp_purchaserequestdetails');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function delete_purchase_request_detail()
    {
        $this->db->delete('srp_erp_purchaserequestdetails', array('purchaseRequestDetailsID' => trim($this->input->post('purchaseRequestDetailsID'))));
        return true;
    }

    function delete_tax_detail()
    {
        $this->db->delete('srp_erp_purchaseordertaxdetails', array('taxDetailAutoID' => trim($this->input->post('taxDetailAutoID'))));
        return true;
    }

    function delete_purchase_request()
    {
        /*$this->db->delete('srp_erp_purchaseordermaster', array('purchaseOrderID' => trim($this->input->post('purchaseOrderID'))));
        $this->db->delete('srp_erp_purchaseorderdetails', array('purchaseOrderID' => trim($this->input->post('purchaseOrderID'))));
        return true;*/
        $this->db->select('*');
        $this->db->from('srp_erp_purchaserequestdetails');
        $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID')));
        $datas = $this->db->get()->row_array();
        if ($datas) {
            $this->session->set_flashdata('e', 'please delete all detail records before deleting this document.');
            return true;
        } else {
            $data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID')));
            $this->db->update('srp_erp_purchaserequestmaster', $data);
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;

        }
    }

    function fetch_purchase_request_detail()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate');
        $this->db->where('purchaseRequestDetailsID', trim($this->input->post('purchaseRequestDetailsID')));
        $this->db->from('srp_erp_purchaserequestdetails');
        return $this->db->get()->row_array();
    }

    function fetch_template_data($purchaseRequestID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('purchaseRequestID,transactionCurrency,transactionCurrencyDecimalPlaces,purchaseRequestCode, DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,referenceNumber,requestedByName,narration,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,confirmedByName,confirmedYN,DATE_FORMAT(confirmedDate,\'' . $convertFormat . '\') AS confirmedDate,approvedbyEmpID,approvedbyEmpName,approvedYN,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate,segmentCode');
        $this->db->where('purchaseRequestID', $purchaseRequestID);
        $this->db->from('srp_erp_purchaserequestmaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);
        $this->db->select('itemSystemCode,itemDescription,unitOfMeasure,requestedQty,unitAmount,discountAmount,comment, totalAmount, discountPercentage,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate');
        $this->db->where('purchaseRequestID', $purchaseRequestID);
        $this->db->from('srp_erp_purchaserequestdetails');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('approvedYN, approvedDate, approvalLevelID,Ename1,Ename2,Ename3,Ename4');
        $this->db->where('documentSystemCode', $purchaseRequestID);
        $this->db->where('documentID', 'PRQ');
        $this->db->from('srp_erp_documentapproved');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.ECode = srp_erp_documentapproved.approvedEmpID');
        $data['approval'] = $this->db->get()->result_array();
        return $data;
    }

    function purchase_request_confirmation()
    {

        $this->db->select('purchaseRequestID');
        $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID')));
        $this->db->from('srp_erp_purchaserequestdetails');
        $record = $this->db->get()->result_array();
        if (empty($record)) {
            $this->session->set_flashdata('w', 'There are no records to confirm this document!');
            return false;
        }else {
            $this->db->select('purchaseRequestID');
            $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID')));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_purchaserequestmaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $this->db->select('purchaseRequestCode,documentID,DATE_FORMAT(documentDate, "%Y") as invYear,DATE_FORMAT(documentDate, "%m") as invMonth,documentDate');
                $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID')));
                $this->db->from('srp_erp_purchaserequestmaster');
                $master_dt = $this->db->get()->row_array();

                $docDate=$master_dt['documentDate'];
                $Comp=current_companyID();
                $companyFinanceYearID = $this->db->query("SELECT
	period.companyFinanceYearID as companyFinanceYearID
FROM
	srp_erp_companyfinanceperiod period
WHERE
	period.companyID = $Comp
AND '$docDate' BETWEEN period.dateFrom
AND period.dateTo
AND period.isActive = 1")->row_array();

                if(empty($companyFinanceYearID['companyFinanceYearID'])){
                    $companyFinanceYearID['companyFinanceYearID']=NULL;
                }

                $this->load->library('sequence');
                if($master_dt['purchaseRequestCode'] == "0" || empty($master_dt['purchaseRequestCode'])) {
                    $pvCd = array(
                        'purchaseRequestCode' => $this->sequence->sequence_generator_fin($master_dt['documentID'], $companyFinanceYearID['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth'])
                    );
                    $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID')));
                    $this->db->update('srp_erp_purchaserequestmaster', $pvCd);
                }

                $this->load->library('approvals');
                $this->db->select('purchaseRequestCode,companyReportingExchangeRate,companyLocalExchangeRate ,purchaseRequestID,transactionCurrencyDecimalPlaces');
                $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID')));
                $this->db->from('srp_erp_purchaserequestmaster');
                $po_data = $this->db->get()->row_array();
                $approvals_status = $this->approvals->CreateApproval('PRQ', $po_data['purchaseRequestID'], $po_data['purchaseRequestCode'], 'Purchase Request', 'srp_erp_purchaserequestmaster', 'purchaseRequestID');
                if ($approvals_status == 1) {
                    $this->db->select_sum('totalAmount');
                    $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID')));
                    $po_total = $this->db->get('srp_erp_purchaserequestdetails')->row('totalAmount');
                    $data = array(
                        'confirmedYN' => 1,
                        'approvedYN' => null,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user'],
                        'transactionAmount' => round($po_total, $po_data['transactionCurrencyDecimalPlaces']),
                        'companyLocalAmount' => ($po_total / $po_data['companyLocalExchangeRate']),
                        'companyReportingAmount' => ($po_total / $po_data['companyReportingExchangeRate']),
                        'isReceived' => 0,
                    );
                    $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID')));
                    $this->db->update('srp_erp_purchaserequestmaster', $data);
                    $this->session->set_flashdata('s', 'Approvals Created Successfully ');
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    function save_purchase_request_approval()
    {
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_code = trim($this->input->post('purchaseRequestID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('po_status'));
        $comments = trim($this->input->post('comments'));

        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'PRQ');
        if ($approvals_status) {
            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];
            //$data['companyLocalAmount']     = $company_loc_tot;
            //$data['companyReportingAmount'] = $company_rpt_tot;
            //$data['supplierCurrencyAmount'] = $supplier_cr_tot;
            //$data['transactionAmount']      = $transaction_loc_tot;

            $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID')));
            $result = $this->db->update('srp_erp_purchaserequestmaster', $data);
        }
        if ($result) {
            if ($status == 1) {
                $this->session->set_flashdata('s', 'Approved Successfully.');
            } else {
                $this->session->set_flashdata('s', 'Rejected Successfully.');
            }

        } else {
            $this->session->set_flashdata('e', 'Approval Failed.');
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
        $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID')));
        $this->db->update('srp_erp_purchaserequestmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function fetch_last_grn_amount()
    {
        $itemAutoId = $this->input->post('itemAutoId');
        $currencyID = $this->input->post('currencyID');
        $data = $this->db->query('SELECT
	grvdetails.receivedAmount
FROM
	srp_erp_grvdetails grvdetails
JOIN srp_erp_grvmaster grvmaster ON grvdetails.grvAutoID=grvmaster.grvAutoID

where grvmaster.approvedYN=1 and  grvmaster.transactionCurrencyID=' . $currencyID . ' and itemAutoID=' . $itemAutoId . '
and grvDate=(SELECT
	max(grvmaster.grvDate) as maxdate
FROM
	srp_erp_grvdetails grvdetails
JOIN srp_erp_grvmaster grvmaster ON grvdetails.grvAutoID=grvmaster.grvAutoID

where grvmaster.approvedYN=1 and  grvmaster.transactionCurrencyID=' . $currencyID . ' and itemAutoID=' . $itemAutoId . ')')->row_array();
        return $data;
    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'PRQ');
        $this->db->from('srp_erp_documentcodemaster ');
        return $this->db->get()->row_array();


    }
}
