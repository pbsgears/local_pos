<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*

============================================================

-- File Name : Pos_restaurant.php
-- Project Name : POS
-- Module Name : POS Restaurant model
-- Author : Mohamed Shafri
-- Create date : 25 - October 2016
-- Description : SME POS System.

--REVISION HISTORY
--Date: 25 - Oct 2016 By: Mohamed Shafri: comment started

============================================================

*/

class Pos_restaurant_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function item_search()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $wareHouseID = $this->common_data['ware_houseID'];
        $search_string = "%" . $_GET['q'] . "%";
        return $this->db->query("SELECT t1.itemAutoID, t1.itemSystemCode, t1.itemDescription, t1.currentStock,
                                 t2.companyLocalSellingPrice, defaultUnitOfMeasure, itemImage
                                 FROM srp_erp_warehouseitems t1
                                 JOIN srp_erp_itemmaster t2 ON t1.itemAutoID = t2.itemAutoID
                                 WHERE (t1.itemSystemCode LIKE '" . $search_string . "' OR t1.itemDescription LIKE '" . $search_string . "')
                                 AND t2.companyID={$companyID} AND t1.wareHouseAutoID ={$wareHouseID} AND isActive=1")->result_array();

        /*return $this->db->query('SELECT mainCategoryID, subcategoryID, subSubCategoryID, revanueGLCode, itemSystemCode,
                                 costGLCode , assteGLCode, defaultUnitOfMeasure, itemDescription, itemAutoID, currentStock,
                                 companyLocalWacAmount, companyLocalSellingPrice
                                 FROM srp_erp_itemmaster WHERE (itemSystemCode LIKE "'.$search_string.'"
                                 OR itemDescription LIKE "'.$search_string.'") AND companyCode = "'.$companyCode.'"
                                 AND isActive="1"')->result_array();*/
    }

    function isHaveNotClosedSession_tabUsers()
    {
        $q = "SELECT `shiftID`, `counterID` FROM `srp_erp_pos_shiftdetails` WHERE `companyID` = '" . current_companyID() . "' AND `wareHouseID` = '" . current_warehouseID() . "' AND `isClosed` =0 AND `counterID`>0";
        $result = $this->db->query($q)->row_array();
        return $result;
    }


    function isHaveNotClosedSession()
    {
        $where = array(
            'empID' => current_userID(),
            'companyID' => current_companyID(),
            'wareHouseID' => current_warehouseID(),
            'isClosed' => 0,
        );

        return $this->db->select('shiftID, counterID')->from('srp_erp_pos_shiftdetails')->where($where)->get()->row_array();
        // echo $this->db->last_query();
    }


    function create_tmp_session($shiftID)
    {
        $this->db->select("*");
        $this->db->from("srp_erp_pos_shiftdetails");
        $this->db->where("shiftID", $shiftID);
        $result = $this->db->get()->row_array();
        unset($result['shiftID']);
        $result['empID'] = current_userID();
        $result['counterID'] = null;
        $result['startingBalance_transaction'] = 0;
        $result['startingBalance_local'] = 0;
        $result['startingBalance_reporting'] = 0;
        $result['createdDateTime'] = format_date_mysql_datetime();
        $result['createdUserName'] = current_user();
        $result['createdPCID'] = current_pc();

        return $this->db->insert('srp_erp_pos_shiftdetails', $result);
    }

    function getInvoiceCode()
    {
        $query = $this->db->select('serialNo')->from('srp_erp_pos_invoice')->where('companyID', $this->common_data['company_data']['company_id'])
            ->order_by('invoiceID', 'desc')->get();
        $lastRefArray = $query->row_array();
        $lastRefNo = $lastRefArray['serialNo'];
        $lastRefNo = ($lastRefNo == null) ? 1 : $lastRefArray['serialNo'] + 1;

        $this->load->library('sequence');
        $refCode = $this->sequence->sequence_generator('REF', $lastRefNo);

        return array('refCode' => $refCode, 'lastRefNo' => $lastRefNo);
    }

    function getInvoiceHoldCode()
    {
        $query = $this->db->select('serialNo')->from('srp_erp_pos_invoicehold')->where('companyID', $this->common_data['company_data']['company_id'])
            ->order_by('invoiceID', 'desc')->get();
        $lastRefArray = $query->row_array();
        $lastRefNo = $lastRefArray['serialNo'];
        $lastRefNo = ($lastRefNo == null) ? 1 : $lastRefArray['serialNo'] + 1;

        $this->load->library('sequence');
        $refCode = $this->sequence->sequence_generator('REF-H', $lastRefNo);

        return array('refCode' => $refCode, 'lastRefNo' => $lastRefNo);
    }

    function get_wareHouse()
    {
        $this->db->select('wHouse.wareHouseAutoID, wareHouseCode, wareHouseDescription, wareHouseLocation, segmentID, segmentCode')
            ->from('srp_erp_warehousemaster wHouse')
            ->join('srp_erp_pos_segmentconfig conf', 'conf.wareHouseAutoID=wHouse.wareHouseAutoID', 'left')
            ->where('wHouse.wareHouseAutoID', $this->common_data['ware_houseID']);
        return $this->db->get()->row_array();

        /*$this->db->select('wareHouseAutoID, wareHouseCode, wareHouseDescription, wareHouseLocation')
            ->from('srp_erp_warehousemaster')
            ->where('wareHouseAutoID', $this->common_data['ware_houseID']);
        return $this->db->get()->row_array();*/
    }

    function invoice_create()
    {
        $currentShiftData = $this->isHaveNotClosedSession();

        if (!empty($currentShiftData)) {

            $com_currency = $this->common_data['company_data']['company_default_currency'];
            $com_currDPlace = $this->common_data['company_data']['company_default_decimal'];
            $rep_currency = $this->common_data['company_data']['company_reporting_currency'];
            $rep_currDPlace = $this->common_data['company_data']['company_reporting_decimal'];
            $wareHouseData = $this->get_wareHouse();
            $customerID = $this->input->post('customerID');
            $customerCode = $this->input->post('customerCode');
            $tr_currency = $this->input->post('_trCurrency');
            $item = $this->input->post('itemID[]');
            $itemUOM = $this->input->post('itemUOM[]');
            $itemQty = $this->input->post('itemQty[]');
            $itemPrice = $this->input->post('itemPrice[]');
            $itemDis = $this->input->post('itemDis[]');
            $invoiceDate = format_date(date('Y-m-d'));

            /*Payment Details Calculation Start*/
            $cashAmount = $this->input->post('_cashAmount');
            $chequeAmount = $this->input->post('_chequeAmount');
            $cardAmount = $this->input->post('_cardAmount');
            $total_discVal = $this->input->post('discVal');
            $paidAmount = ($cashAmount + $chequeAmount + $cardAmount);
            $netTotVal = $this->input->post('netTotVal');
            $balanceAmount = ($netTotVal - $paidAmount);

            if ($netTotVal < $paidAmount) {
                $cashAmount = $netTotVal - ($chequeAmount + $cardAmount);
                $balanceAmount = 0;
            }

            /*Payment Details Calculation End*/

            //Get last reference no
            $invCodeDet = $this->getInvoiceCode();
            $lastRefNo = $invCodeDet['lastRefNo'];
            $refCode = $invCodeDet['refCode'];

            $localConversion = currency_conversion($com_currency, $com_currency, $netTotVal);
            $localConversionRate = $localConversion['conversion'];
            $transConversion = currency_conversion($tr_currency, $com_currency, $netTotVal);
            $tr_currDPlace = $transConversion['DecimalPlaces'];
            $transConversionRate = $transConversion['conversion'];
            $reportConversion = currency_conversion($rep_currency, $com_currency, $netTotVal);
            $reportConversionRate = $reportConversion['conversion'];

            /*echo '<pre>';print_r($tr_currency);echo '</pre>';
            die();*/

            $invArray = array(
                'documentSystemCode' => $refCode,
                'documentCode' => 'POS',
                'serialNo' => $lastRefNo,
                'customerID' => $customerID,
                'customerCode' => $customerCode,
                'invoiceDate' => $invoiceDate,
                'counterID' => $currentShiftData['counterID'],
                'shiftID' => $currentShiftData['shiftID'],


                'netTotal' => $netTotVal,
                'localNetTotal' => ($netTotVal * $localConversionRate),
                'reportingNetTotal' => ($netTotVal * $reportConversionRate),

                'paidAmount' => $paidAmount,
                'localPaidAmount' => ($paidAmount * $localConversionRate),
                'reportingPaidAmount' => ($paidAmount * $reportConversionRate),

                'balanceAmount' => $balanceAmount,
                'localBalanceAmount' => ($balanceAmount * $localConversionRate),
                'reportingBalanceAmount' => ($balanceAmount * $reportConversionRate),

                'cashAmount' => $cashAmount,
                'chequeAmount' => $chequeAmount,
                'cardAmount' => $cardAmount,

                'discountAmount' => $total_discVal,
                'localDiscountAmount' => ($total_discVal * $localConversionRate),
                'reportingDiscountAmount' => ($total_discVal * $reportConversionRate),


                'companyLocalCurrencyID' => '',
                'companyLocalCurrency' => $com_currency,
                'companyLocalCurrencyDecimalPlaces' => $com_currDPlace,
                'companyLocalExchangeRate' => $localConversionRate,

                'transactionCurrencyID' => '',
                'transactionCurrency' => $tr_currency,
                'transactionCurrencyDecimalPlaces' => $tr_currDPlace,
                'transactionExchangeRate' => $transConversionRate,

                'companyReportingCurrencyID' => '',
                'companyReportingCurrency' => $rep_currency,
                'companyReportingCurrencyDecimalPlaces' => $rep_currDPlace,
                'companyReportingExchangeRate' => $reportConversionRate,


                'wareHouseAutoID' => $wareHouseData['wareHouseAutoID'],
                'wareHouseCode' => $wareHouseData['wareHouseCode'],
                'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
                'wareHouseDescription' => $wareHouseData['wareHouseDescription'],

                'companyID' => $this->common_data['company_data']['company_id'],
                'companyCode' => $this->common_data['company_data']['company_code'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdDateTime' => current_date(),

            );

            if ($customerID == 0) {
                $bankData = $this->db->query("SELECT receivableAutoID, receivableSystemGLCode, receivableGLAccount,
                                          receivableDescription, receivableType
                                          FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();
                $invArray['bankGLAutoID'] = $bankData['receivableAutoID'];
                $invArray['bankSystemGLCode'] = $bankData['receivableSystemGLCode'];
                $invArray['bankGLAccount'] = $bankData['receivableGLAccount'];
                $invArray['bankGLDescription'] = $bankData['receivableDescription'];
                $invArray['bankGLType'] = $bankData['receivableType'];
            } else {
                $cusData = $this->db->query("SELECT receivableAutoID, receivableSystemGLCode, receivableGLAccount,
                                         receivableDescription, receivableType
                                         FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();

                $invArray['customerReceivableAutoID'] = $cusData['receivableAutoID'];
                $invArray['customerReceivableSystemGLCode'] = $cusData['receivableSystemGLCode'];
                $invArray['customerReceivableGLAccount'] = $cusData['receivableGLAccount'];
                $invArray['customerReceivableDescription'] = $cusData['receivableDescription'];
                $invArray['customerReceivableType'] = $cusData['receivableType'];
            }

            /*echo '<pre>';print_r($cusData);echo '</pre>';
            die();*/

            $this->db->trans_start();
            $this->db->insert('srp_erp_pos_invoice', $invArray);
            $invID = $this->db->insert_id();

            $i = 0;
            $dataInt = array();
            foreach ($item as $itemID) {
                $itemData = fetch_ware_house_item_data($itemID);
                $conversion = conversionRateUOM($itemUOM[$i], $itemData['defaultUnitOfMeasure']);
                $conversionRate = 1 / $conversion;
                $availableQTY = $itemData['wareHouseQty'];
                $qty = $itemQty[$i] * $conversionRate;

                /*echo 'conversion: '.$conversion;
                echo '<p>$itemQty[$i]: '.$itemQty[$i];
                echo '<p>conversionRate: '.$conversionRate;
                echo '<p>availableQTY: '.$availableQTY;
                echo '<p>QTY: '.$qty; die();*/

                /*if ($availableQTY >= $qty) {*/

                $itemTotal = $itemQty[$i] * $itemPrice[$i];
                $itemTotal = ($itemDis[$i] > 0) ? ($itemPrice[$i] * 0.01 * $itemDis[$i]) : $itemTotal;

                $dataInt[$i]['invoiceID'] = $invID;
                $dataInt[$i]['itemAutoID'] = $itemID;
                $dataInt[$i]['itemSystemCode'] = $itemData['itemSystemCode'];
                $dataInt[$i]['itemDescription'] = $itemData['itemDescription'];
                $dataInt[$i]['defaultUOM'] = $itemData['defaultUnitOfMeasure'];
                $dataInt[$i]['unitOfMeasure'] = $itemUOM[$i];
                $dataInt[$i]['conversionRateUOM'] = $conversion;
                $dataInt[$i]['qty'] = $itemQty[$i];
                $dataInt[$i]['price'] = $itemPrice[$i];
                $dataInt[$i]['discountPer'] = $itemDis[$i];
                $dataInt[$i]['wacAmount'] = $itemData['companyLocalWacAmount'];

                $dataInt[$i]['itemFinanceCategory'] = $itemData['subcategoryID'];
                $dataInt[$i]['itemFinanceCategorySub'] = $itemData['subSubCategoryID'];
                $dataInt[$i]['financeCategory'] = $itemData['financeCategory'];
                $dataInt[$i]['itemCategory'] = $itemData['mainCategory'];

                $dataInt[$i]['expenseGLAutoID'] = $itemData['costGLAutoID'];
                $dataInt[$i]['expenseGLCode'] = $itemData['costGLCode'];
                $dataInt[$i]['expenseSystemGLCode'] = $itemData['costSystemGLCode'];
                $dataInt[$i]['expenseGLDescription'] = $itemData['costDescription'];
                $dataInt[$i]['expenseGLType'] = $itemData['costType'];

                $dataInt[$i]['revenueGLAutoID'] = $itemData['revanueGLAutoID'];
                $dataInt[$i]['revenueGLCode'] = $itemData['revanueGLCode'];
                $dataInt[$i]['revenueSystemGLCode'] = $itemData['revanueSystemGLCode'];
                $dataInt[$i]['revenueGLDescription'] = $itemData['revanueDescription'];
                $dataInt[$i]['revenueGLType'] = $itemData['revanueType'];

                $dataInt[$i]['assetGLAutoID'] = $itemData['assteGLAutoID'];
                $dataInt[$i]['assetGLCode'] = $itemData['assteGLCode'];
                $dataInt[$i]['assetSystemGLCode'] = $itemData['assteSystemGLCode'];
                $dataInt[$i]['assetGLDescription'] = $itemData['assteDescription'];
                $dataInt[$i]['assetGLType'] = $itemData['assteType'];


                $dataInt[$i]['transactionAmount'] = ($itemTotal * $tr_currDPlace);;
                $dataInt[$i]['transactionExchangeRate'] = $transConversionRate;
                $dataInt[$i]['transactionCurrency'] = $tr_currency;
                $dataInt[$i]['transactionCurrencyDecimalPlaces'] = $tr_currDPlace;

                $dataInt[$i]['companyLocalAmount'] = ($itemTotal * $localConversionRate);
                $dataInt[$i]['companyLocalExchangeRate'] = $localConversionRate;
                $dataInt[$i]['companyLocalCurrency'] = $com_currency;
                $dataInt[$i]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

                $dataInt[$i]['companyReportingAmount'] = ($itemTotal * $reportConversionRate);
                $dataInt[$i]['companyReportingExchangeRate'] = $reportConversionRate;
                $dataInt[$i]['companyReportingCurrency'] = $rep_currency;
                $dataInt[$i]['companyReportingCurrencyDecimalPlaces'] = $rep_currDPlace;

                $dataInt[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                $dataInt[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                $dataInt[$i]['createdPCID'] = $this->common_data['current_pc'];
                $dataInt[$i]['createdUserID'] = $this->common_data['current_userID'];
                $dataInt[$i]['createdUserName'] = $this->common_data['current_user'];
                $dataInt[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $dataInt[$i]['createdDateTime'] = current_date();


                $balanceQty = $availableQTY - $qty;

                /* echo '<p>conversion:'.$conversion.'</p><p>qty:'.$qty.'</p><p>itemQty:'.$itemQty[$i].'</p>';
                 echo '<p>AVlQty:'.$availableQTY.'</p>';
                 echo '<p>balanceQty:'.$balanceQty.'</p>';
                 die();*/
                $itemUpdateWhere = array('itemAutoID' => $itemID, 'wareHouseAutoID' => $this->common_data['ware_houseID']);
                $itemUpdateQty = array('currentStock' => $balanceQty);
                $this->db->where($itemUpdateWhere)->update('srp_erp_warehouseitems', $itemUpdateQty);


                $i++;
                //}
                /*else {
                    $this->db->trans_rollback();
                    return array('e', '[ '.$itemData['itemSystemCode'].' - '.$itemData['itemDescription'].' ]<p> is available only '.$availableQTY.' qty');
                    break;
                }*/
            }

            $this->db->insert_batch('srp_erp_pos_invoicedetail', $dataInt);


            $this->db->trans_complete();
            if ($this->db->trans_status() == false) {
                $this->db->trans_rollback();
                return array('e', 'Error in Invoice Create');
            } else {
                $this->db->trans_commit();
                return array('s', 'Invoice Code : ' . $refCode . ' ', $invID, $refCode);
            }
        } else {
            return array('e', 'You have not a valid session.<p>Please login and try again.</p>');
        }
    }

    function invoice_hold()
    {

        $com_currency = $this->common_data['company_data']['company_default_currency'];
        $com_currDPlace = $this->common_data['company_data']['company_default_decimal'];
        $rep_currency = $this->common_data['company_data']['company_reporting_currency'];
        $rep_currDPlace = $this->common_data['company_data']['company_reporting_decimal'];
        $wareHouseData = $this->get_wareHouse();
        $customerID = $this->input->post('customerID');
        $customerCode = $this->input->post('customerCode');
        $tr_currency = $this->input->post('_trCurrency');
        $item = $this->input->post('itemID[]');
        $itemUOM = $this->input->post('itemUOM[]');
        $itemQty = $this->input->post('itemQty[]');
        $itemPrice = $this->input->post('itemPrice[]');
        $itemDis = $this->input->post('itemDis[]');
        $invoiceDate = format_date(date('Y-m-d'));

        /*Payment Details Calculation Start*/
        $cashAmount = $this->input->post('_cashAmount');
        $chequeAmount = $this->input->post('_chequeAmount');
        $cardAmount = $this->input->post('_cardAmount');
        $total_discVal = $this->input->post('discVal');
        $paidAmount = ($cashAmount + $chequeAmount + $cardAmount);
        $netTotVal = $this->input->post('netTotVal');
        $balanceAmount = ($netTotVal - $paidAmount);

        if ($netTotVal < $paidAmount) {
            $cashAmount = $netTotVal - ($chequeAmount + $cardAmount);
            $balanceAmount = 0;
        }

        /*Payment Details Calculation End*/

        //Get last reference no
        $invCodeDet = $this->getInvoiceHoldCode();
        $lastRefNo = $invCodeDet['lastRefNo'];
        $refCode = $invCodeDet['refCode'];

        $localConversion = currency_conversion($tr_currency, $com_currency, $netTotVal);
        $localConversionRate = $localConversion['conversion'];
        $reportConversion = currency_conversion($tr_currency, $rep_currency, $netTotVal);
        $reportConversionRate = $reportConversion['conversion'];

        /*echo $tr_currency.' // '.$com_repCurrency.' // '.$netTotVal;*/


        $invArray = array(
            'documentSystemCode' => $refCode,
            'serialNo' => $lastRefNo,
            'customerID' => $customerID,
            'customerCode' => $customerCode,
            'invoiceDate' => $invoiceDate,

            'netTotal' => $netTotVal,
            'localNetTotal' => ($netTotVal * $localConversionRate),
            'reportingNetTotal' => ($netTotVal * $reportConversionRate),

            'paidAmount' => $paidAmount,
            'localPaidAmount' => ($paidAmount * $localConversionRate),
            'reportingPaidAmount' => ($paidAmount * $reportConversionRate),

            'balanceAmount' => $balanceAmount,
            'localBalanceAmount' => ($balanceAmount * $localConversionRate),
            'reportingBalanceAmount' => ($balanceAmount * $reportConversionRate),

            'cashAmount' => $cashAmount,
            'chequeAmount' => $chequeAmount,
            'cardAmount' => $cardAmount,

            'discountAmount' => $total_discVal,
            'localDiscountAmount' => ($total_discVal * $localConversionRate),
            'reportingDiscountAmount' => ($total_discVal * $reportConversionRate),


            'companyLocalExchangeRate' => $localConversionRate,
            'companyLocalCurrency' => $com_currency,
            'companyLocalCurrencyDecimalPlaces' => $com_currDPlace,

            'companyReportingExchangeRate' => $reportConversionRate,
            'companyReportingCurrency' => $rep_currency,
            'companyReportingCurrencyDecimalPlaces' => $rep_currDPlace,

            'wareHouseAutoID' => $wareHouseData['wareHouseAutoID'],
            'wareHouseCode' => $wareHouseData['wareHouseCode'],
            'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
            'wareHouseDescription' => $wareHouseData['wareHouseDescription'],

            'companyID' => $this->common_data['company_data']['company_id'],
            'companyCode' => $this->common_data['company_data']['company_code'],
            'createdPCID' => $this->common_data['current_pc'],
            'createdUserID' => $this->common_data['current_userID'],
            'createdUserName' => $this->common_data['current_user'],
            'createdUserGroup' => $this->common_data['user_group'],
            'createdDateTime' => current_date()
        );
        //echo '<pre>'.print_r($invArray).'</pre>';die();


        $this->db->trans_start();
        $this->db->insert('srp_erp_pos_invoicehold', $invArray);
        $invID = $this->db->insert_id();

        $i = 0;
        $dataInt = array();
        foreach ($item as $itemID) {
            $itemData = fetch_ware_house_item_data($itemID);

            /*echo '<pre>'.print_r($itemData).'</pre>';*/
            $itemTotal = $itemQty[$i] * $itemPrice[$i];
            $itemTotal = ($itemDis[$i] > 0) ? ($itemTotal * 0.01 * $itemDis[$i]) : $itemTotal;

            $dataInt[$i]['invoiceID'] = $invID;
            $dataInt[$i]['itemAutoID'] = $itemID;
            $dataInt[$i]['itemSystemCode'] = $itemData['itemSystemCode'];
            $dataInt[$i]['itemDescription'] = $itemData['itemDescription'];
            $dataInt[$i]['defaultUOM'] = $itemData['defaultUnitOfMeasure'];
            $dataInt[$i]['unitOfMeasure'] = $itemUOM[$i];
            $dataInt[$i]['conversionRateUOM'] = conversionRateUOM($itemUOM[$i], $itemData['defaultUnitOfMeasure']);
            $dataInt[$i]['qty'] = $itemQty[$i];
            $dataInt[$i]['price'] = $itemPrice[$i];
            $dataInt[$i]['discountPer'] = $itemDis[$i];

            $dataInt[$i]['itemFinanceCategory'] = $itemData['subcategoryID'];
            $dataInt[$i]['itemFinanceCategorySub'] = $itemData['subSubCategoryID'];
            $dataInt[$i]['financeCategory'] = $itemData['financeCategory'];
            $dataInt[$i]['itemCategory'] = $itemData['mainCategory'];

            $dataInt[$i]['transactionAmount'] = $itemTotal;
            $dataInt[$i]['transactionExchangeRate'] = '';
            $dataInt[$i]['transactionCurrency'] = $com_currency;
            $dataInt[$i]['transactionCurrencyDecimalPlaces'] = '';

            $dataInt[$i]['companyLocalAmount'] = ($itemTotal * $localConversionRate);
            $dataInt[$i]['companyLocalExchangeRate'] = $localConversionRate;
            $dataInt[$i]['companyLocalCurrency'] = $com_currency;
            $dataInt[$i]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

            $dataInt[$i]['companyReportingAmount'] = ($itemTotal * $reportConversionRate);
            $dataInt[$i]['companyReportingExchangeRate'] = $reportConversionRate;
            $dataInt[$i]['companyReportingCurrency'] = $rep_currency;
            $dataInt[$i]['companyReportingCurrencyDecimalPlaces'] = $rep_currDPlace;

            $dataInt[$i]['companyID'] = $this->common_data['company_data']['company_id'];
            $dataInt[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
            $dataInt[$i]['createdPCID'] = $this->common_data['current_pc'];
            $dataInt[$i]['createdUserID'] = $this->common_data['current_userID'];
            $dataInt[$i]['createdUserName'] = $this->common_data['current_user'];
            $dataInt[$i]['createdUserGroup'] = $this->common_data['user_group'];
            $dataInt[$i]['createdDateTime'] = current_date();
            $i++;


        }

        $this->db->insert_batch('srp_erp_pos_invoiceholddetail', $dataInt);


        $this->db->trans_complete();
        if ($this->db->trans_status() == false) {
            $this->db->trans_rollback();
            return array('e', 'Error in Hold Invoice');
        } else {
            $this->db->trans_commit();
            return array('s', 'Hold Invoice Code : ' . $refCode . ' ', $invID, $refCode);
        }
    }

    function customer_search()
    {
        $key = $this->input->post('key');
        $companyID = $this->common_data['company_data']['company_id'];

        return $this->db->query("SELECT customerAutoID, customerSystemCode, customerName, customerCurrency, customerAddress1
                                 FROM srp_erp_customermaster WHERE companyID={$companyID} AND
                                 (customerName LIKE '%$key%' OR customerName LIKE '%$key%')
                                 UNION SELECT 1, 'CASH', 'Cash', '', ''")->result_array();
    }

    function invoice_cardDetail()
    {
        $invID = $this->input->post('invID');
        $referenceNO = $this->input->post('referenceNO');
        $cardNumber = $this->input->post('cardNumber');
        $bank = $this->input->post('bank');

        $upData = array(
            'cardNumber' => $cardNumber,
            'cardRefNo' => $referenceNO,
            'cardBank' => $bank
        );

        $this->db->where('invoiceID', $invID)->update('srp_erp_pos_invoice', $upData);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Card Details Updated');
        } else {
            return array('e', 'Error In Card Details Updated');
        }
    }

    function recall_invoice()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $wareHouse = $this->common_data['ware_houseID'];
        return $this->db->query("SELECT t1.*, if(customerID=0 , 'Cash',
                                  (SELECT customerName FROM srp_erp_customermaster WHERE  customerAutoID=t1.customerID)) AS cusName
                                 FROM srp_erp_pos_invoice t1 WHERE companyID={$companyID} AND t1.wareHouseAutoID={$wareHouse}")->result_array();
    }

    function invoice_search()
    {
        $companyID = current_companyID();
        $wareHouse = $this->common_data['ware_houseID'];
        $invoiceCode = $this->input->post('invoiceCode');

        $isExistInv = $this->db->query("SELECT t1.*, if(customerID=0 , 'Cash',
                                  (SELECT customerName FROM srp_erp_customermaster WHERE  customerAutoID=t1.customerID)) AS cusName,
                                  (SELECT sum(balanceAmount) FROM srp_erp_pos_invoice WHERE customerID = t1.customerID) AS cusBalance
                                  FROM srp_erp_pos_invoice t1 WHERE companyID={$companyID} AND t1.wareHouseAutoID={$wareHouse} AND
                                  t1.documentSystemCode='$invoiceCode'")->row_array();

        if ($isExistInv != null) {
            $invItems = $this->db->select('*')->from('srp_erp_pos_invoicedetail')->where('invoiceID', $isExistInv['invoiceID'])
                ->get()->result_array();
            return array(
                0 => 's',
                1 => $isExistInv,
                2 => $invItems
            );
        } else {
            return array('w', 'There is not a invoice in this number');
        }
    }

    function recall_hold_invoice()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $wareHouse = $this->common_data['ware_houseID'];
        return $this->db->query("SELECT t1.*, if(customerID=0 , 'Cash',
                                  (SELECT customerName FROM srp_erp_customermaster WHERE  customerAutoID=t1.customerID)) AS cusName
                                 FROM srp_erp_pos_invoicehold t1 WHERE companyID={$companyID} AND t1.wareHouseAutoID={$wareHouse}")->result_array();
    }

    function load_holdInv()
    {
        $wareHouse = $this->common_data['ware_houseID'];
        $holdID = $this->input->post('holdID');
        $masterDet = $this->db->query("SELECT t1.*, if(customerID=0 , 'Cash',
                                       (SELECT customerName FROM srp_erp_customermaster WHERE  customerAutoID=t1.customerID)) AS cusName
                                       FROM srp_erp_pos_invoicehold t1 WHERE invoiceID={$holdID}")->row_array();

        $itemDet = $this->db->query("SELECT t1.*, (SELECT currentStock FROM srp_erp_warehouseitems WHERE  wareHouseAutoID={$wareHouse}
                                     AND itemAutoID=t1.itemAutoID) AS currentStk,
                                     (SELECT itemImage FROM srp_erp_itemmaster WHERE itemAutoID=t1.itemAutoID) AS itemImage
                                     FROM srp_erp_pos_invoiceholddetail t1 WHERE  invoiceID={$holdID}")->result_array();

        return array($masterDet, $itemDet);
    }

    function new_counter()
    {
        $wareHouseID = $this->input->post('wareHouseID');
        $counterCode = $this->input->post('counterCode');
        $counterName = $this->input->post('counterName');

        $data = array(
            'counterCode' => $counterCode,
            'counterName' => $counterName,
            'wareHouseID' => $wareHouseID,
            'companyID' => $this->common_data['company_data']['company_id'],
            'companyCode' => $this->common_data['company_data']['company_code'],
            'createdPCID' => $this->common_data['current_pc'],
            'createdUserID' => $this->common_data['current_userID'],
            'createdUserName' => $this->common_data['current_user'],
            'createdUserGroup' => $this->common_data['user_group'],
            'createdDateTime' => current_date()
        );

        $this->db->insert('srp_erp_pos_counters', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Counter Created Successfully.');
        } else {
            return array('e', 'Error In Counter Created');
        }
    }

    function update_counterDetails()
    {
        $counterID = $this->input->post('updateID');
        $wareHouseID = $this->input->post('wareHouseID');
        $counterCode = $this->input->post('counterCode');
        $counterName = $this->input->post('counterName');

        $upData = array(
            'counterCode' => $counterCode,
            'counterName' => $counterName,
            'wareHouseID' => $wareHouseID,
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => $this->common_data['current_date']
        );
        $this->db->where('counterID', $counterID)->update('srp_erp_pos_counters', $upData);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Counter Updated Successfully.');
        } else {
            return array('e', 'Error In Counter Updated');
        }
    }

    function delete_counterDetails()
    {
        $counterID = $this->input->post('counterID');
        $upData = array(
            'isActive' => 0,
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => $this->common_data['current_date']
        );
        $this->db->where('counterID', $counterID)->update('srp_erp_pos_counters', $upData);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Counter Delete Successfully.');
        } else {
            return array('e', 'Error In Counter Delete');
        }
    }

    function load_wareHouseCounters($wareHouse)
    {
        $result = $this->db->select('counterID, counterCode, counterName')->from('srp_erp_pos_counters')
            ->where('wareHouseID', $wareHouse)->where('isActive', 1)
            ->get()->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_giftCardTopUpCashCollection()
    {
        $counterInfo = get_counterData();
        $shiftID = $counterInfo['shiftID'];
        $q = "SELECT
                srp_erp_pos_cardtopup.glConfigMasterID,
                sum( srp_erp_pos_cardtopup.topUpAmount ) AS totalAmount,
                srp_erp_pos_cardtopup.shiftID 
            FROM
                srp_erp_pos_cardtopup 
            WHERE
                srp_erp_pos_cardtopup.shiftID = '" . $shiftID . "' 
                AND srp_erp_pos_cardtopup.glConfigMasterID =1";

        $totalAmount = $this->db->query($q)->row('totalAmount');
        $totalAmount = !empty($totalAmount) ? $totalAmount : 0;
        return $totalAmount;


    }

    function get_counterData($counterID)
    {
        return $this->db->select('counterID, counterCode, counterName')->from('srp_erp_pos_counters')
            ->where('counterID', $counterID)->where('isActive', 1)
            ->get()->row_array();
    }

    function load_wareHouseUsers($wareHouse)
    {
        return $this->db->select("autoID, userID, Ecode, CONCAT(IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '')) eName ")
            ->from('srp_erp_warehouse_users t1')
            ->join('srp_employeesdetails t2', 't1.userID=t2.EidNo')
            ->where('t1.wareHouseID', $wareHouse)
            ->where('t1.isActive', 1)
            ->where('NOT EXISTS( SELECT userID FROM srp_erp_warehouse_users WHERE userID=t2.EIdNo
                         AND (counterID IS NOT NULL OR counterID != 0))')->get()->result_array();

    }

    function emp_search()
    {
        $keyword = $this->input->get('q');
        $com = $this->common_data['company_data']['company_id'];
        $where = "(Ename1 LIKE '%$keyword%' OR Ename2 LIKE '%$keyword%' OR Ename3 LIKE '%$keyword%' OR Ename4 LIKE '%$keyword%' OR ECode LIKE '%$keyword%') ";
        $where .= "AND t1.Erp_companyID='$com'";
        $where .= "AND NOT EXISTS( SELECT userID FROM srp_erp_warehouse_users WHERE userID=t1.EIdNo )";

        $this->db->select("EIdNo, ECode, CONCAT(IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '')) empName");
        $this->db->from('srp_employeesdetails t1');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result();
    }

    function add_ware_house_user()
    {
        $employeeID = $this->input->post('employeeID');
        $employeeCode = $this->input->post('employeeCode');
        $wareHouseID = $this->input->post('wareHouseID');

        $isExist = $this->db->select("autoID,
                   (SELECT wareHouseLocation FROM srp_erp_warehousemaster WHERE wareHouseAutoID=t1.wareHouseID) AS wareHouse")
            ->from('srp_erp_warehouse_users t1')
            ->where('userID', $employeeID)->where('isActive', 1)->get()->row_array();

        if (empty($isExist)) {
            $data = array(
                'userID' => $employeeID,
                'wareHouseID' => $wareHouseID,
                'companyID' => $this->common_data['company_data']['company_id'],
                'companyCode' => $this->common_data['company_data']['company_code'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdDateTime' => current_date(),
            );

            $this->db->insert('srp_erp_warehouse_users', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Employee [ ' . $employeeCode . ' ] successfully added to ware house');
            } else {
                return array('e', 'Error In Counter Created');
            }
        } else {
            return array('e', '[ ' . $employeeCode . ' ] is already added to ' . $isExist['wareHouse'] . ' location');
        }
    }

    function update_ware_house_user()
    {
        $autoID = $this->input->post('updateID');
        $employeeID = $this->input->post('employeeID');
        $employeeCode = $this->input->post('employeeCode');
        $wareHouseID = $this->input->post('wareHouseID');

        $isExist = $this->db->select("autoID,
                   (SELECT wareHouseLocation FROM srp_erp_warehousemaster WHERE srp_erp_warehousemaster.wareHouseAutoID=t1.wareHouseID) AS wareHouse")
            ->from('srp_erp_warehouse_users t1')
            ->where('userID=' . $employeeID . ' AND autoID!=' . $autoID . ' AND isActive=1')->get()->row_array();

        if (empty($isExist)) {
            $upData = array(
                'userID' => $employeeID,
                'wareHouseID' => $wareHouseID,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $this->common_data['current_userID'],
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => $this->common_data['current_date']
            );

            $this->db->where('autoID', $autoID)->update('srp_erp_warehouse_users', $upData);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Updated Successfully.');
            } else {
                return array('e', 'Error In Update Process');
            }
        } else {
            return array('e', '[ ' . $employeeCode . ' ] is already added to ' . $isExist['wareHouse'] . ' location');
        }
    }

    function delete_ware_house_user()
    {
        $autoID = $this->input->post('autoID');
        $upData = array(
            'isActive' => 0,
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => $this->common_data['current_date']
        );

        $this->db->where('autoID', $autoID)->update('srp_erp_warehouse_users', $upData);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Deleted Successfully.');
        } else {
            return array('e', 'Error In Deleted Process.');
        }
    }

    function currencyDenominations($currencyCode)
    {
        return $this->db->select('amount, value, caption, isNote')->from('srp_erp_currencydenomination')
            ->where('currencyCode', $currencyCode)->order_by('amount', 'DESC')->get()->result_array();
    }

    /*Promotion setups*/
    function new_promotion()
    {
        $promoType = $this->input->post('promoType');
        $warehouses = $this->input->post('warehouses[]');
        $range = $this->input->post('range[]');
        $discountPer = $this->input->post('discountPer[]');
        $couponAmount = $this->input->post('couponAmount[]');
        $getFreeQty = $this->input->post('getFreeQty[]');
        $buyQty = $this->input->post('buyQty[]');
        $isApplicableForAllItem = $this->input->post('isApplicableForAllItem');
        $isApplicableForAllWarehouse = $this->input->post('isApplicableForAllWarehouse');
        $promotionDescr = $this->input->post('promotionDescr');
        $fromDate = $this->input->post('fromDate');
        $endDate = $this->input->post('endDate');

        $data = array(
            'promotionTypeID' => $promoType,
            'description' => $promotionDescr,
            'dateFrom' => $fromDate,
            'dateTo' => $endDate,
            'isActive' => 0,
            'isApplicableForAllItem' => $isApplicableForAllItem,
            'companyID' => $this->common_data['company_data']['company_id'],
            'companyCode' => $this->common_data['company_data']['company_code'],
            'createdPCID' => $this->common_data['current_pc'],
            'createdUserID' => $this->common_data['current_userID'],
            'createdUserName' => $this->common_data['current_user'],
            //'createdUserGroup' => $this->common_data['user_group'],
            'createdDateTime' => current_date()
        );

        $this->db->trans_start();
        $this->db->insert('srp_erp_pos_promotionsetupmaster', $data);
        $promotionID = $this->db->insert_id();


        /*$proWarehouses = array();
        foreach( $warehouses as $key => $house ){
            $proWarehouses[$key]['promotionID'] = $promotionID;
            $proWarehouses[$key]['wareHouseID'] = $house;
            $proWarehouses[$key]['companyID'] = $this->common_data['company_data']['company_id'];
            $proWarehouses[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
            $proWarehouses[$key]['createdPCID'] = $this->common_data['current_pc'];
            $proWarehouses[$key]['createdUserID'] = $this->common_data['current_userID'];
            $proWarehouses[$key]['createdUserName'] = $this->common_data['current_user'];
            $proWarehouses[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $proWarehouses[$key]['createdDateTime'] = current_date();
        }
        $this->db->insert_batch('srp_erp_pos_promotionwarehouses', $proWarehouses);


        $promoDet = array();
        foreach($range as $key => $row){

            if($promoType == 1){
                $promoDet[$key]['startRangeAmount'] = $row;
                $promoDet[$key]['discountPrc'] = $discountPer[$key];
            }
            elseif($promoType == 2){
                $promoDet[$key]['startRangeAmount'] = $row;
                $promoDet[$key]['coupenAmount'] = $couponAmount[$key];
            }
            elseif($promoType == 3){
                $promoDet[$key]['buyQty'] = $buyQty[$key];
                $promoDet[$key]['getFreeQty'] = $getFreeQty[$key];
            }

            $promoDet[$key]['promotionID'] = $promotionID;
            $promoDet[$key]['companyID'] = $this->common_data['company_data']['company_id'];
            $promoDet[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
            $promoDet[$key]['createdPCID'] = $this->common_data['current_pc'];
            $promoDet[$key]['createdUserID'] = $this->common_data['current_userID'];
            $promoDet[$key]['createdUserName'] = $this->common_data['current_user'];
            $promoDet[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $promoDet[$key]['createdDateTime'] = current_date();
        }
        $this->db->insert_batch('srp_erp_pos_promotionsetupdetail', $promoDet);*/

        $this->insert_promotion_warehouses($promotionID);
        $this->insert_promotion_details($promotionID);

        $this->db->trans_complete();
        if ($this->db->trans_status() == false) {
            $this->db->trans_rollback();
            return array('e', 'Error In Promotion Create Process.');
        } else {
            $this->db->trans_commit();
            return array('s', 'Successfully.', $promotionID);
        }

    }

    function update_promotion()
    {
        $updateID = $this->input->post('updateID');
        $promoType = $this->input->post('promoType');
        $isApplicableForAllItem = $this->input->post('isApplicableForAllItem');
        $promotionDescr = $this->input->post('promotionDescr');
        $fromDate = $this->input->post('fromDate');
        $endDate = $this->input->post('endDate');

        $data = array(
            'promotionTypeID' => $promoType,
            'description' => $promotionDescr,
            'dateFrom' => $fromDate,
            'dateTo' => $endDate,
            'isActive' => 0,
            'isApplicableForAllItem' => $isApplicableForAllItem,

            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_employee(),
            'modifiedDateTime' => current_date()
        );

        $this->db->trans_start();
        $this->db->where('promotionID', $updateID)->update('srp_erp_pos_promotionsetupmaster', $data);

        $this->db->where('promotionID', $updateID)->delete('srp_erp_pos_promotionwarehouses');
        $this->db->where('promotionID', $updateID)->delete('srp_erp_pos_promotionsetupdetail');


        $this->insert_promotion_warehouses($updateID);
        $this->insert_promotion_details($updateID);

        $this->db->trans_complete();
        if ($this->db->trans_status() == false) {
            $this->db->trans_rollback();
            return array('e', 'Error is Promotion update');
        } else {
            $this->db->trans_commit();
            return array('s', 'Successfully Updated', $updateID);
        }

    }

    function insert_promotion_warehouses($promotionID)
    {
        $warehouses = $this->input->post('warehouses[]');
        $proWarehouses = array();

        foreach ($warehouses as $key => $house) {
            $proWarehouses[$key]['promotionID'] = $promotionID;
            $proWarehouses[$key]['wareHouseID'] = $house;
            $proWarehouses[$key]['companyID'] = current_companyID();
            $proWarehouses[$key]['companyCode'] = current_companyCode();
            $proWarehouses[$key]['createdPCID'] = current_pc();
            $proWarehouses[$key]['createdUserID'] = current_userID();
            $proWarehouses[$key]['createdUserName'] = current_employee();
            $proWarehouses[$key]['createdUserGroup'] = current_user_group();
            $proWarehouses[$key]['createdDateTime'] = current_date();
        }
        $this->db->insert_batch('srp_erp_pos_promotionwarehouses', $proWarehouses);
    }

    function insert_promotion_details($promotionID)
    {
        $promoType = $this->input->post('promoType');
        $range = $this->input->post('range[]');
        $discountPer = $this->input->post('discountPer[]');
        $couponAmount = $this->input->post('couponAmount[]');
        $getFreeQty = $this->input->post('getFreeQty[]');
        $buyQty = $this->input->post('buyQty[]');
        $promoDet = array();

        foreach ($range as $key => $row) {

            if ($promoType == 1) {
                $promoDet[$key]['startRangeAmount'] = $row;
                $promoDet[$key]['discountPrc'] = $discountPer[$key];
            } elseif ($promoType == 2) {
                $promoDet[$key]['startRangeAmount'] = $row;
                $promoDet[$key]['coupenAmount'] = $couponAmount[$key];
            } elseif ($promoType == 3) {
                $promoDet[$key]['buyQty'] = $buyQty[$key];
                $promoDet[$key]['getFreeQty'] = $getFreeQty[$key];
            }

            $promoDet[$key]['promotionID'] = $promotionID;
            $promoDet[$key]['companyID'] = current_companyID();
            $promoDet[$key]['companyCode'] = current_companyCode();
            $promoDet[$key]['createdPCID'] = current_pc();
            $promoDet[$key]['createdUserID'] = current_userID();
            $promoDet[$key]['createdUserName'] = current_employee();
            $promoDet[$key]['createdUserGroup'] = current_user_group();
            $promoDet[$key]['createdDateTime'] = current_date();

        }

        $this->db->insert_batch('srp_erp_pos_promotionsetupdetail', $promoDet);
        return $promoDet;
    }

    function delete_promotion()
    {
        $promoID = $this->input->post('promoID');

        $this->db->trans_start();

        $this->db->where('promotionID', $promoID)->delete('srp_erp_pos_promotionsetupmaster');
        $this->db->where('promotionID', $promoID)->delete('srp_erp_pos_promotionwarehouses');
        $this->db->where('promotionID', $promoID)->delete('srp_erp_pos_promotionsetupdetail');

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('e', 'Error in delete process');
        } else {
            $this->db->trans_commit();
            return array('s', 'Successfully deleted');
        }
    }

    function get_promotionMasterDet($promo_ID)
    {
        $master = $this->db->select('promotionTypeID, description, dateFrom, dateTo, isApplicableForAllItem')
            ->from('srp_erp_pos_promotionsetupmaster')->where('promotionID', $promo_ID)->get()->row_array();

        $warehouses = $this->db->select('house.wareHouseAutoID AS wareHID')
            ->from('srp_erp_pos_promotionwarehouses proWare')
            ->join('srp_erp_warehousemaster house', 'house.wareHouseAutoID=proWare.wareHouseID')
            ->where('promotionID', $promo_ID)->get()->result_array();
        return array(
            'master' => $master,
            'warehouses' => $warehouses
        );
    }

    function get_promotionDet($promo_ID)
    {
        return $this->db->select('startRangeAmount, discountPrc, coupenAmount, buyQty, getFreeQty')
            ->from('srp_erp_pos_promotionsetupdetail')->where('promotionID', $promo_ID)
            ->get()->result_array();

    }

    function load_applicableItems($promo_ID)
    {
        $companyID = current_companyID();
        return $this->db->query("SELECT t1.itemAutoID, itemSystemCode, itemDescription, t1.promotionID
                                 FROM srp_erp_pos_promotionapplicableitems t1
                                 JOIN srp_erp_itemmaster t2 ON t1.itemAutoID = t2.itemAutoID
                                 WHERE t2.companyID={$companyID} AND isActive=1 AND t1.promotionID={$promo_ID}")->result_array();


    }

    function save_promotionItems()
    {
        $promotionID = $this->input->post('promoID');
        $selectedItems = $this->input->post('selectedItems[]');
        $data = array();

        $this->db->trans_start();
        $this->db->where('promotionID', $promotionID)->delete('srp_erp_pos_promotionapplicableitems');

        foreach ($selectedItems as $key => $item) {
            $data[$key]['promotionID'] = $promotionID;
            $data[$key]['itemAutoID'] = $item;
            $data[$key]['companyID'] = current_companyID();
            $data[$key]['companyCode'] = current_companyCode();
            $data[$key]['createdPCID'] = current_pc();
            $data[$key]['createdUserID'] = current_userID();
            $data[$key]['createdUserName'] = current_employee();
            $data[$key]['createdUserGroup'] = current_user_group();
            $data[$key]['createdDateTime'] = current_date();
        }

        $this->db->insert_batch('srp_erp_pos_promotionapplicableitems', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('e', 'Error in applicable items saving');
        } else {
            $this->db->trans_commit();
            return array('s', 'Applicable items saved successfully');
        }

    }

    function promotionDetail()
    {
        $currentDate = date_format(date_create(current_date()), 'Y-m-d');
        $companyID = current_companyID();
        $wareHouse = $this->common_data['ware_houseID'];
        $couponArray = array();
        $freeIssueArray = array();

        $coupon = $this->db->query("SELECT promo.promotionID, description, isApplicableForAllItem
                                    FROM srp_erp_pos_promotionsetupmaster promo
                                    JOIN srp_erp_pos_promotionwarehouses house ON promo.promotionID = house.promotionID
                                    WHERE promo.dateFrom <= '$currentDate' AND promo.dateTo >= '$currentDate'
                                    AND promotionTypeID = 2 AND house.wareHouseID = {$wareHouse}
                                    AND promo.companyID = {$companyID}")->result_array();

        $freeIssue = $this->db->query("SELECT promo.promotionID, description, isApplicableForAllItem
                                    FROM srp_erp_pos_promotionsetupmaster promo
                                    JOIN srp_erp_pos_promotionwarehouses house ON promo.promotionID = house.promotionID
                                    WHERE promo.dateFrom <= '$currentDate' AND promo.dateTo >= '$currentDate'
                                    AND promotionTypeID = 3 AND house.wareHouseID = {$wareHouse}
                                    AND promo.companyID = {$companyID}")->result_array();

        foreach ($coupon as $key => $pro) {
            $promoID = $pro['promotionID'];
            $couponArray[$key] = array(
                'master' => $pro,
                'promoSetup' => $this->get_promotionDet($promoID),
                'promoItems' => $this->load_applicableItems($promoID),
            );

        }

        foreach ($freeIssue as $key => $pro) {
            $promoID = $pro['promotionID'];
            $isApplicableForAllItems = $pro['isApplicableForAllItem'];

            $freeIssueArray[$key] = array(
                'master' => $pro,
                'promoSetup' => $this->get_promotionDet($promoID),
                'promoItems' => (($isApplicableForAllItems == 0) ? $this->load_applicableItems($promoID) : null),
            );

        }

        return array(
            'coupon' => $couponArray,
            'freeIssue' => $freeIssueArray
        );
    }

    /*End of Promotion setups*/

    function get_warehouseMenues($warehouseID)
    {
        $path = base_url();
        $this->db->select("category.autoID as autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice , menuMaster.isPack ");
        $this->db->from("srp_erp_pos_warehousemenumaster menu");
        $this->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $this->db->where('menu.isActive', 1);
        $this->db->where('menu.isDeleted', 0);
        $this->db->where('category.isDeleted', 0);
        $this->db->where('category.isActive', 1);
        $this->db->where('menuMaster.isDeleted', 0);
        $this->db->where('menu.warehouseID', $warehouseID);
        $this->db->order_by('menuMaster.sortOrder', 'asc');
        $this->db->order_by('menuMaster.menuMasterDescription', 'asc');
        $result = $this->db->get()->result_array();
        /*echo $this->db->last_query();
        exit;*/
        return $result;
    }

    function get_warehouseCategory($warehouseID)
    {
        $path = base_url();
        $this->db->select("warehouseCategory.autoID, category.menuCategoryID, category.menuCategoryDescription as description, concat('" . $path . "',category.image) as image, category.bgColor, category.masterLevelID, category.levelNo, category.showImageYN as showImageYN");
        $this->db->from("srp_erp_pos_warehousemenucategory warehouseCategory");
        $this->db->join("srp_erp_pos_menucategory category", "warehouseCategory.menuCategoryID = category.menuCategoryID", "INNER");
        $this->db->where('category.isActive', 1);
        $this->db->where('warehouseCategory.isDeleted', 0);
        $this->db->where('warehouseCategory.isActive', 1);
        $this->db->where('category.companyID', current_companyID());
        $this->db->where('category.isDeleted', 0);
        $this->db->where('warehouseCategory.warehouseID', $warehouseID);
        $this->db->order_by('category.sortOrder', 'asc');
        $this->db->order_by('category.menuCategoryDescription', 'asc');
        $result = $this->db->get()->result_array();

        /*echo $this->db->last_query();
        exit;*/

        return $result;
    }


    function get_warehouseSubCategory($warehouseID)
    {
        $path = base_url();
        $q = "SELECT
                  whmc2.autoID,
                    srp_erp_pos_menucategory.menuCategoryID,
                    srp_erp_pos_menucategory.menuCategoryDescription AS description,
                    concat('" . $path . "',srp_erp_pos_menucategory.image) as image,
                    srp_erp_pos_menucategory.bgColor,
                    srp_erp_pos_menucategory.masterLevelID,
                    srp_erp_pos_menucategory.levelNo,
                    srp_erp_pos_menucategory.showImageYN
                FROM
                    srp_erp_pos_menucategory
                    INNER JOIN srp_erp_pos_warehousemenucategory whmc2 ON whmc2.menuCategoryID = srp_erp_pos_menucategory.menuCategoryID
                WHERE
                    srp_erp_pos_menucategory.menuCategoryID IN (
                        SELECT
                            masterLevelID
                        FROM
                            srp_erp_pos_menucategory
                        INNER JOIN srp_erp_pos_warehousemenucategory whmc ON whmc.menuCategoryID = srp_erp_pos_menucategory.menuCategoryID
                        WHERE
                            srp_erp_pos_menucategory.masterLevelID IS NOT NULL  AND whmc.warehouseID = '" . $warehouseID . "' AND srp_erp_pos_menucategory.isDeleted = 0
                        GROUP BY
                            masterLevelID
                    ) AND srp_erp_pos_menucategory.companyID = '" . current_companyID() . "' AND whmc2.isDeleted = 0
                    ORDER BY `srp_erp_pos_menucategory`.`sortOrder` ASC, `srp_erp_pos_menucategory`.`menuCategoryDescription` ASC";
        /*echo $q;
        exit;*/
        $result = $this->db->query($q)->result_array();
        return $result;

    }


    function get_warehouseMenu_specific($warehouseMenuID)
    {
        $path = base_url();
        $this->db->select("category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice, menu.warehouseMenuCategoryID, menuMaster.menuCost, menuMaster.isPack , categoryMaster.revenueGLAutoID,menu.menuMasterID, category.menuCategoryID, menuMaster.TAXpercentage, menuMaster.taxMasterID, menuMaster.pricewithoutTax, menuMaster.totalServiceCharge,menuMaster.totalTaxAmount,menu.isTaxEnabled");
        $this->db->from("srp_erp_pos_warehousemenumaster menu");
        $this->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $this->db->join("srp_erp_pos_menucategory categoryMaster", "categoryMaster.menuCategoryID = menuMaster.menuCategoryID", "INNER");
        $this->db->where('menu.isActive', 1);
        $this->db->where('menu.isDeleted', 0);
        $this->db->where('menuMaster.isDeleted', 0);
        $this->db->where('menu.warehouseMenuID', $warehouseMenuID);
        $result = $this->db->get()->row_array();
        /*echo $this->db->last_query();
        exit;*/
        return $result;
    }

    function get_srp_erp_pos_shiftdetails_employee()
    {
        $this->db->select("*");
        $this->db->from("srp_erp_pos_shiftdetails");
        $this->db->where('empID', current_userID());
        $this->db->where('companyID', current_companyID());
        $this->db->where('wareHouseID', current_warehouseID());
        $this->db->where('isClosed', 0);
        $result = $this->db->get()->row_array();
        return $result;
    }

    function insert_srp_erp_pos_menusalesmaster($data)
    {
        $data['wareHouseAutoID'] = current_warehouseID();
        $data['id_store'] = current_warehouseID();
        $result = $this->db->insert('srp_erp_pos_menusalesmaster', $data);
        if ($result) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return false;
        }
    }

    function insert_srp_erp_pos_menusalesitems($data)
    {
        $data['id_store'] = current_warehouseID();
        $result = $this->db->insert('srp_erp_pos_menusalesitems', $data);
        if ($result) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return false;
        }
    }

    function get_srp_erp_pos_menusalesitems_invoiceID_forHoldBill($invoiceID, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }
        $path = base_url();
        $this->db->select("sales.menuSalesID, sales.menuSalesItemID, category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice, sales.qty , sales.discountPer, sales.discountAmount, menuMaster.menuMasterID,sales.remarkes, menuMaster.pricewithoutTax, menuMaster.totalTaxAmount, menuMaster.totalServiceCharge, menu.isTaxEnabled , size.code as sizeCode, size.description as sizeDescription,sales.isSamplePrinted");
        $this->db->from("srp_erp_pos_menusalesitems sales");
        $this->db->join("srp_erp_pos_warehousemenumaster menu", "menu.warehouseMenuID = sales.warehouseMenuID");
        $this->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $this->db->join("srp_erp_pos_menusize size", "size.menuSizeID = menuMaster.menuSizeID", "left");
        $this->db->where('menu.isActive', 1);
        $this->db->where('menu.isDeleted', 0);
        $this->db->where('menuMaster.isDeleted', 0);
        $this->db->where('sales.menuSalesID', $invoiceID);
        $this->db->where('sales.id_store', $outletID);
        $result = $this->db->get()->result_array();

        return $result;
    }

    function get_srp_erp_pos_menusalesitems_invoiceID($invoiceID, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }
        $path = base_url();
        $this->db->select("sales.menuSalesID, sales.menuSalesItemID, category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice, sales.qty , sales.discountPer, sales.discountAmount, menuMaster.menuMasterID,sales.remarkes, menuMaster.pricewithoutTax, menuMaster.totalTaxAmount, menuMaster.totalServiceCharge, menu.isTaxEnabled , size.code as sizeCode, size.description as sizeDescription,sales.isSamplePrinted");
        $this->db->from("srp_erp_pos_menusalesitems sales");
        $this->db->join("srp_erp_pos_warehousemenumaster menu", "menu.warehouseMenuID = sales.warehouseMenuID");
        $this->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $this->db->join("srp_erp_pos_menusize size", "size.menuSizeID = menuMaster.menuSizeID", "left");
        $this->db->where('menu.isActive', 1);
        $this->db->where('menu.isDeleted', 0);
        $this->db->where('menuMaster.isDeleted', 0);
        $this->db->where('sales.menuSalesID', $invoiceID);
        $this->db->where('sales.id_store', $outletID);
        $result = $this->db->get()->result_array();

        return $result;
    }

    function get_srp_erp_pos_menusalesitems_specific($menuSalesItemID, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }

        $this->db->select("master.*");
        $this->db->from("srp_erp_pos_menusalesitems sales");
        $this->db->join("srp_erp_pos_warehousemenumaster warehouse", 'warehouse.warehouseMenuID = sales.warehouseMenuID', 'left');
        $this->db->join("srp_erp_pos_menumaster master", 'warehouse.menuMasterID = master.menuMasterID', 'left');
        $this->db->where('sales.menuSalesItemID', $menuSalesItemID);
        $this->db->where('sales.id_store', $outletID);
        $result = $this->db->get()->row_array();
        return $result;
    }


    function delete_menuSalesItem($id, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }
        $this->db->where('menuSalesItemID', $id);
        $this->db->where('id_store', $outletID);
        return $this->db->delete('srp_erp_pos_menusalesitems');

    }

    function delete_srp_erp_pos_valuepackdetail_by_ItemID($menuSalesItemID)
    {
        $this->db->where('menuSalesItemID', $menuSalesItemID);
        return $this->db->delete('srp_erp_pos_valuepackdetail');
    }


    /** Delete Menu Sales */
    function delete_srp_erp_pos_menusalesitems_byMenuSalesID($menuSalesID)
    {
        $this->db->where('menuSalesID', $menuSalesID);
        $this->db->where('id_store', current_warehouseID());
        $result = $this->db->delete('srp_erp_pos_menusalesitems');
        if ($result) {
            return array('error' => 0, 'message' => 'done');
        } else {
            return array('error' => 1, 'message' => 'Error deleting!, Please contact system support team');
        }
    }

    /** Delete Menu Sales */
    function delete_srp_erp_pos_menusalesmaster($menuSalesID)
    {
        $this->db->where('menuSalesID', $menuSalesID);
        $this->db->where('wareHouseAutoID', current_warehouseID());

        $result = $this->db->delete('srp_erp_pos_menusalesmaster');
        if ($result) {
            return array('error' => 0, 'message' => 'done');
        } else {
            return array('error' => 1, 'message' => 'Error deleting!, Please contact system support team');
        }
    }

    /** Delete Menu Sales */
    function delete_srp_erp_pos_menusalesitemdetails_byMenuSalesID($menuSalesID)
    {
        $this->db->where('menuSalesID', $menuSalesID);
        $this->db->where('id_store', current_warehouseID());
        $result = $this->db->delete('srp_erp_pos_menusalesitemdetails');
        if ($result) {
            return array('error' => 0, 'message' => 'done');
        } else {
            return array('error' => 1, 'message' => 'Error deleting!, Please contact system support team');
        }
    }

    function get_srp_erp_warehouse_users_WarehouseID()
    {
        $companyID = current_companyID();
        $userID = current_userID();

        $this->db->select("wareHouseID");
        $this->db->from("srp_erp_warehouse_users");
        $this->db->where('companyID', $companyID);
        $this->db->where('userID', $userID);
        $this->db->where('isActive', 1);
        $wareHouseID = $this->db->get()->row('wareHouseID');
        return $wareHouseID;
    }

    function update_srp_erp_pos_menusalesmaster($data, $id)
    {
        $this->db->where('menuSalesID', $id);
        $this->db->where('wareHouseAutoID', get_outletID());
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);
        return $result;
    }

    function get_srp_erp_pos_menusalesmaster($id, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }
        $this->db->select("menuSales.*, cType.customerDescription,promo.customerName as promotn");
        $this->db->from("srp_erp_pos_menusalesmaster menuSales");
        $this->db->join('srp_erp_customertypemaster cType', 'cType.customerTypeID = menuSales.customerTypeID', 'left'); /*customerTypeID*/
        $this->db->join('srp_erp_pos_customers promo', 'promo.customerID = menuSales.promotionID', 'left');
        $this->db->where('menuSales.menuSalesID', $id);
        $this->db->where('menuSales.wareHouseAutoID', $outletID);
        //$this->db->where('menuSales.wareHouseAutoID', current_warehouseID());
        $result = $this->db->get()->row_array();
        return $result;
    }

    function get_srp_erp_pos_menusalesmaster_specific($id, $select = '*', $row = false)
    {
        $this->db->select($select);
        $this->db->from('srp_erp_pos_menusalesmaster');
        $this->db->where('menuSalesID', $id);
        if ($row) {
            return $this->db->get()->row($select);
        } else {
            return $this->db->get()->row_array();
        }
    }

    function load_posHoldReceipt()
    {
        $this->db->select("*");
        $this->db->from("srp_erp_pos_menusalesmaster");
        $this->db->where('isHold', 0);
        $result = $this->db->get()->result_array();
        return $result;

    }

    function updateTotalCost($invoiceID)
    {
        $q = "UPDATE srp_erp_pos_menusalesmaster AS salesMaster, ( SELECT sum(detailTbl.menuCost) AS menuCostTmp FROM srp_erp_pos_menusalesitems AS detailTbl WHERE detailTbl.menuSalesID = " . $invoiceID . " AND detailTbl.id_store = " . current_warehouseID() . " ) tmp SET salesMaster.menuCost = tmp.menuCostTmp, salesMaster.is_sync=0 WHERE salesMaster.menuSalesID = '" . $invoiceID . "' AND salesMaster.wareHouseAutoID = '" . current_warehouseID() . "'";
        $result = $this->db->query($q);
        //echo $q.'<br/>';
        return $result;

    }

    function get_warehouseMenuItem($warehouseMenuID)
    {
        $path = base_url();
        $this->db->select("category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice , menuMaster.isPack , menuMaster.menuMasterID");
        $this->db->from("srp_erp_pos_warehousemenumaster menu");
        $this->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $this->db->where('menu.isActive', 1);
        $this->db->where('menu.isDeleted', 0);
        $this->db->where('category.isDeleted', 0);
        $this->db->where('category.isActive', 1);
        $this->db->where('menuMaster.isDeleted', 0);
        $this->db->where('menu.warehouseMenuID', $warehouseMenuID);
        $result = $this->db->get()->row_array();
        return $result;
    }

    function get_pack_menuItem($packMenuID)
    {
        $path = base_url();
        $this->db->select("packItem.menuPackItemID as id, packItem.menuID as menuID, packItem.isRequired as isRequired, menuMaster.menuMasterDescription, packItem.menuCategoryID ,  concat('" . $path . "',menuMaster.menuImage) as menuImage, menuCategory.menuCategoryDescription, packItem.PackMenuID");
        $this->db->from("srp_erp_pos_menupackitem packItem");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = packItem.menuID", "left");
        $this->db->join("srp_erp_pos_menucategory menuCategory", "menuCategory.menuCategoryID = packItem.menuCategoryID", "left");
        $this->db->where('menuMaster.isDeleted', 0);
        $this->db->where('packItem.PackMenuID', $packMenuID);
        $this->db->order_by('packItem.isRequired', 'DESC');
        $this->db->order_by('menuMaster.menuCategoryID ', 'ASC');
        $this->db->order_by('menuMaster.menuMasterDescription ', 'ASC');

        $result = $this->db->get()->result_array();
        return $result;
    }

    function get_srp_erp_pos_menupackitem_requiredItems($menuMasterID)
    {
        $this->db->select("menuPack.*,packGroup.packgroupdetailID");
        $this->db->from("srp_erp_pos_menupackitem menuPack");
        $this->db->join("srp_erp_pos_packgroupdetail packGroup", "packGroup.menuPackItemID = menuPack.menuPackItemID ", "LEFT");
        $this->db->where('menuPack.isRequired', 1);
        $this->db->where('packGroup.isActive', 1);
        $this->db->where('menuPack.PackMenuID', $menuMasterID);


        $result = $this->db->get()->result_array();
        return $result;
    }

    function get_srp_erp_pos_menupackitem_optionalItems($menuMasterID)
    {
        $this->db->select("menuPack.*,packGroup.packgroupdetailID");
        $this->db->from("srp_erp_pos_menupackitem menuPack");
        $this->db->join("srp_erp_pos_packgroupdetail packGroup", "packGroup.menuPackItemID = menuPack.menuPackItemID ", "LEFT");
        $this->db->where('menuPack.isRequired', 0);
        $this->db->where('menuPack.PackMenuID', $menuMasterID);

        $result = $this->db->get()->result_array();
        return $result;
    }

    function bulk_insert_srp_erp_pos_valuepackdetail($data)
    {
        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_pos_valuepackdetail', $data);
        }
    }

    function get_currentCompanyDetail()
    {
        $this->db->select("*");
        $this->db->from("srp_erp_company");
        $this->db->where('company_id', current_companyID());
        $result = $this->db->get()->row_array();
        /*echo $this->db->last_query();
        echo '<br/>';
        echo '<br/>';*/
        return $result;
    }

    function get_report_customerTypeCount($date, $date2, $cashier = null, $Outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }


        $q = "SELECT customertype.customerDescription,count(salesMaster.netTotal) as countTotal FROM srp_erp_pos_menusalesmaster AS salesMaster LEFT JOIN srp_erp_customertypemaster customertype ON customertype.customerTypeID = salesMaster.customerTypeID WHERE salesMaster.isVoid = 0 AND  salesMaster.isHold = 0 AND salesMaster.companyID=" . current_companyID() . " AND DATE_FORMAT(salesMaster.menuSalesDate,'%Y-%m-%d')  BETWEEN '" . $date . "' AND '" . $date2 . "' " . $qString . $outletFilter . $outletsFilter . " GROUP BY customertype.customerDescription";

        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_customerTypeCount2($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $q = "SELECT
                    customertype.customerDescription,
                    count(salesMaster.netTotal) AS countTotal,
                    sum(subTotal) as subTotal
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                LEFT JOIN srp_erp_customertypemaster customertype ON customertype.customerTypeID = salesMaster.customerTypeID
                WHERE
                    salesMaster.isVoid = 0 
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " .
            $qString . $outletFilter .
            "GROUP BY 
                    customertype.customerDescription ORDER BY customertype.customerTypeID ";
        // echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_customerTypeCount2_new($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $q = "SELECT						
                customertype.customerDescription,
                sum(payments.amount) AS subTotal,					
                count( salesMaster.menuSalesID ) AS countTotal
            FROM						
                srp_erp_pos_menusalesmaster AS salesMaster 
                LEFT JOIN  (
                        SELECT SUM( IFNULL(amount,0) ) as amount, menuSalesID, paymentConfigMasterID , wareHouseAutoID 
                        FROM srp_erp_pos_menusalespayments 
                        GROUP BY menuSalesID, wareHouseAutoID
                    ) as payments ON payments.menuSalesID = salesMaster.menuSalesID AND payments.wareHouseAutoID = salesMaster.wareHouseAutoID				
                LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = payments.paymentConfigMasterID 
                LEFT JOIN srp_erp_customertypemaster customertype ON customertype.customerTypeID = salesMaster.customerTypeID  
                WHERE
                    salesMaster.isVoid = 0 
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " .
            $qString . $outletFilter .
            "GROUP BY 
                customertype.customerDescription 
            ORDER BY 
                customertype.customerTypeID, salesMaster.wareHouseAutoID ";
        // echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_report_startingBillNo($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $q = "SELECT
                    customertype.customerDescription,
                    count(salesMaster.netTotal) AS countTotal,
                    sum(subTotal) as subTotal
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                LEFT JOIN srp_erp_customertypemaster customertype ON customertype.customerTypeID = salesMaster.customerTypeID
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " .
            $qString . $outletFilter .
            "GROUP BY 
                    customertype.customerDescription ORDER BY customertype.customerTypeID ";


        // echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_totalSalse($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }


        $q = "SELECT
                'Total Sales' AS Description,
                SUM(paidAmount) AS amount
            
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' "
            . $qString . $outletFilter .
            "AND createdUserID IN (1106, 1138, 12548)";


        // echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_javaAppDiscount($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $q = "SELECT
                'Java App' AS customerName,
            SUM(netTotal) AS netTotal,
            SUM(payments.amount) AS lessAmount
            FROM
                srp_erp_pos_menusalespayments AS payments
                JOIN srp_erp_pos_menusalesmaster  AS salesMaster ON  salesMaster.menuSalesID = payments.menuSalesID
            WHERE
                payments.paymentConfigMasterID = 25 
            AND  salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID =  '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
             " . $qString . $outletFilter;


        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_report_salesReport_discount($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $q = "SELECT
                'Discounts' AS customerName,
            SUM(netTotal) AS netTotal,
            SUM(salesMaster.discountAmount) AS lessAmount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID =  '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
            AND salesMaster.discountAmount>0 " . $qString . $outletFilter;


        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_lessAmount($date, $date2, $cashier = null, $Outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }


        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }

        $q = "SELECT
                    salesMaster.deliveryCommission,
                    customers.customerName,
                    SUM(netTotal) AS netTotal,
                    SUM( ( netTotal * ( salesMaster.deliveryCommission/100 ) ) ) AS lessAmount
                
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                JOIN srp_erp_pos_customers customers ON customers.customerID = salesMaster.deliveryPersonID
                JOIN srp_erp_pos_paymentglconfigmaster payments ON  payments.autoID = salesMaster.paymentMethod
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND DATE_FORMAT(
                    salesMaster.menuSalesDate,
                    '%Y-%m-%d'
                ) 
                BETWEEN '" . $date . "'
                AND '" . $date2 . "'
                AND NOT ISNULL(
                    salesMaster.deliveryPersonID
                )
                AND salesMaster.deliveryPersonID <> 0 
                AND payments.autoID = 1
                " . $qString . "
                " . $outletFilter . "
                " . $outletsFilter . "
                GROUP BY
                    customers.customerName
                ORDER BY
                    salesMaster.isPromotion";

        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_lessAmount2($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }


        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $q = "SELECT
                    salesMaster.deliveryCommission,
                    customers.customerName,
                    SUM(netTotal) AS netTotal,
                    SUM( deliveryCommissionAmount ) AS lessAmount
                
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = salesMaster.deliveryPersonID
                LEFT JOIN srp_erp_pos_paymentglconfigmaster paymentsConfig ON  paymentsConfig.autoID = salesMaster.paymentMethod
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
                AND NOT ISNULL(
                    salesMaster.deliveryPersonID
                )
                AND salesMaster.deliveryPersonID <> 0 
                AND paymentsConfig.autoID = 1
                " . $qString . "
                " . $outletFilter . "
                GROUP BY
                    customers.customerName
                ORDER BY
                    salesMaster.isPromotion";

        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_report_lessAmount_promotion($date, $date2, $cashier = null, $Outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }

        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }

        $q = "SELECT
                    salesMaster.promotionDiscount as deliveryCommission,
                    customers.customerName as customerName,
                    SUM(netTotal) AS netTotal,
                    SUM( ( netTotal * ( salesMaster.promotionDiscount/100 ) ) ) AS lessAmount
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                JOIN srp_erp_pos_customers customers ON customers.customerID = salesMaster.promotionID
                JOIN srp_erp_pos_paymentglconfigmaster paymentsConfig ON  paymentsConfig.autoID = salesMaster.paymentMethod
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND DATE_FORMAT(
                    salesMaster.menuSalesDate,
                    '%Y-%m-%d'
                ) 
                BETWEEN '" . $date . "'
                AND '" . $date2 . "'
                AND NOT ISNULL(
                    salesMaster.promotionID
                )
                AND salesMaster.promotionID <> 0 
                AND paymentsConfig.autoID = 1
                " . $qString . "
                " . $outletFilter . "
                " . $outletsFilter . "
                GROUP BY
                    customers.customerName
                ORDER BY
                    salesMaster.isPromotion";

        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_lessAmount_promotion2($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }

        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }


        $q = "SELECT
                    salesMaster.promotionDiscount AS deliveryCommission,
                    customers.customerName AS customerName,
                    SUM(IFNULL(grossTotal,0)) AS netTotal,
                    SUM(IFNULL(promotionDiscountAmount,0) ) as lessAmount
                
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = salesMaster.promotionID
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
                AND NOT ISNULL(salesMaster.promotionID)
                AND salesMaster.promotionID <> 0
                " . $qString . "
                " . $outletFilter . "
                GROUP BY
                    customers.customerName
                ORDER BY
                    salesMaster.isPromotion";

        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_report_paymentMethod($date, $data2, $cashier = null, $Outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND createdUserID IN(" . $cashier . ") ";
        }

        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }

        $q = "SELECT payment.paymentDescription, SUM(salesMaster.netTotal) as NetTotal FROM srp_erp_pos_menusalesmaster AS salesMaster LEFT JOIN srp_erp_pos_paymentmethods as payment ON payment.paymentMethodsID= salesMaster.paymentMethod WHERE salesMaster.isVoid = 0 AND salesMaster.isHold = 0 AND salesMaster.companyID=" . current_companyID() . " AND DATE_FORMAT(salesMaster.menuSalesDate,'%Y-%m-%d')  BETWEEN '" . $date . "' AND '" . $data2 . "'  " . $qString . $outletFilter . $outletsFilter . "  GROUP BY salesMaster.paymentMethod";
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_report_paymentMethod2($date, $data2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND payments.wareHouseAutoID = '" . $outletID . "' ";
        }

        $q = "SELECT
                    configMaster.description as paymentDescription,
                    SUM(payments.amount) AS NetTotal,
                     count(payments.menuSalesPaymentID) as countTransaction
                FROM
                    srp_erp_pos_menusalespayments AS payments 
                 
                LEFT JOIN srp_erp_pos_menusalesmaster AS salesMaster ON payments.menuSalesID = salesMaster.menuSalesID AND payments.wareHouseAutoID = salesMaster.wareHouseAutoID
                LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = payments.paymentConfigMasterID
                WHERE
                 salesMaster.isVoid = 0 AND 
                salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . "
                GROUP BY
                    payments.paymentConfigMasterID, payments.wareHouseAutoID
                ORDER BY payments.paymentConfigMasterID;";
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_report_voidBills($date, $data2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $q = "SELECT
                   'Voided Bills'  AS paymentDescription,
                    SUM(salesMaster.subTotal) AS NetTotal,
                     count(	salesMaster.menuSalesID) as countTransaction
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster 
                 
                WHERE
                   salesMaster.isVoid = 1 AND 
                salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter;
        $result = $this->db->query($q)->row_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_report_giftCardTopUp($date, $data2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND giftCard.createdUserID IN(" . $cashier . ") ";
        }

        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND giftCard.outletID = '" . $outletID . "' ";
        }

        $q = "SELECT
                    configMaster.description as paymentDescription,
                    SUM(giftCard.topUpAmount) AS topUpTotal,
                     count(giftCard.cardTopUpID) as countTopUp
                FROM
                    srp_erp_pos_cardtopup AS giftCard
                 
                LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = giftCard.glConfigMasterID
                WHERE
                  giftCard.companyID = '" . current_companyID() . "'
                AND giftCard.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . " AND giftCard.topUpAmount>0
                GROUP BY
                    configMaster.autoID";
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    function get_itemizedSalesReport($dateFrom, $dateTo, $Outlets = null, $cashier = null)
    {
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }

        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $q = "SELECT
                    menuMaster.menuMasterID,
                    menuMaster.menuMasterDescription,
                    menuCategory.menuCategoryID,
                    menuCategory.menuCategoryDescription,
                    sum( salesItem.salesPriceAfterDiscount ) AS itemPriceTotal,
                    sum( salesItem.qty ) AS qty,
                    size.description AS menuSize 
                FROM
                    srp_erp_pos_menusalesmaster salesMaster
                    LEFT JOIN srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID AND salesMaster.wareHouseAutoID = salesItem.id_store
                    LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID
                    LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = warehouse.menuMasterID
                    LEFT JOIN srp_erp_pos_menucategory menuCategory ON menuCategory.menuCategoryID = menuMaster.menuCategoryID
                    LEFT JOIN srp_erp_pos_menusize size ON size.menuSizeID = menuMaster.menuSizeID 
                WHERE
                    salesMaster.isVoid = 0 
                    AND salesMaster.isHold = 0 
                    AND salesMaster.companyID = " . current_companyID() . " 
                    AND salesMaster.createdDateTime BETWEEN '" . $dateFrom . "' 
                    AND '" . $dateTo . "' 
                    AND menuMaster.menuMasterID IS NOT NULL " . $outletFilter . " $qString$outletsFilter 
                GROUP BY
                    menuMaster.menuMasterID, salesItem.id_store
                ORDER BY
                    menuCategory.menuCategoryID";


        $result = $this->db->query($q)->result_array();
        // echo $this->db->last_query();


        return $result;
    }


    function get_srp_erp_pos_menudetails_by_menuMasterID($menuMasterID)
    {
        $this->db->select("menu.*,item.costGLAutoID, assteGLAutoID as assetGLAutoID");
        $this->db->from("srp_erp_pos_menudetails menu");
        $this->db->join("srp_erp_itemmaster item", "item.itemAutoID = menu.itemAutoID", "LEFT");
        $this->db->where('menuMasterID', $menuMasterID);
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function batch_insert_srp_erp_pos_menusalesitemdetails($data)
    {
        $this->db->insert_batch('srp_erp_pos_menusalesitemdetails', $data);
    }

    function get_GL_Entries_items($menusSalesID)
    {

        $q = "SELECT
                    SUM(item.salesPriceNetTotal) AS totalGL,
                    chartOfAccount.systemAccountCode,
                    chartOfAccount.GLSecondaryCode,
                    chartOfAccount.GLDescription,
                    chartOfAccount.subCategory,
                    item.*
                FROM
                    srp_erp_pos_menusalesitems item
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.revenueGLAutoID
                WHERE
                    item.menuSalesID = " . $menusSalesID . " AND item.menuSalesID = " . current_warehouseID() . " 
                GROUP BY
                    revenueGLAutoID";
        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_packGroup_menuItem($packMenuID)
    {
        $path = base_url();
        $q = "SELECT
                    pd.packgroupdetailID AS id,
                    pd.menuID AS menuID,
                    mgm.IsRequired AS isRequired,
                    menuMaster.menuMasterDescription,
                    mgm.groupMasterID as menuCategoryID,
                    concat(
                        '" . $path . "',
                        menuMaster.menuImage
                    ) AS menuImage,
                    mgm.description as menuCategoryDescription,
                    pd.PackMenuID
                FROM
                    srp_erp_pos_packgroupdetail pd
                LEFT JOIN srp_erp_pos_menupackgroupmaster mgm ON mgm.groupMasterID = pd.groupMasterID
                LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = pd.menuID
                LEFT JOIN srp_erp_pos_menucategory menuCategory ON menuCategory.menuCategoryID = menuMaster.menuCategoryID
                LEFT JOIN srp_erp_pos_menupackitem mpi ON mpi.menuPackItemID = pd.menuPackItemID
                WHERE
                    pd.packMenuID = '" . $packMenuID . "' AND pd.isActive=1 ORDER BY pd.groupMasterID ";
        /*  $this->db->select("packItem.menuPackItemID as id, packItem.menuID as menuID, packItem.isRequired as isRequired, menuMaster.menuMasterDescription, packItem.menuCategoryID ,  concat('".$path."',menuMaster.menuImage) as menuImage, menuCategory.menuCategoryDescription, packItem.PackMenuID");
          $this->db->from("srp_erp_pos_menupackitem packItem");
          $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = packItem.menuID", "left");
          $this->db->join("srp_erp_pos_menucategory menuCategory", "menuCategory.menuCategoryID = packItem.menuCategoryID", "left");
          $this->db->where('menuMaster.isDeleted', 0);
          $this->db->where('packItem.PackMenuID', $packMenuID);
          $this->db->order_by('packItem.isRequired', 'DESC');
          $this->db->order_by('menuMaster.menuCategoryID ', 'ASC');
          $this->db->order_by('menuMaster.menuMasterDescription ', 'ASC');*/

        //$result = $this->db->get()->result_array();
        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function update_srp_erp_pos_updaterestaurantTable($data, $id)
    {
        $data['is_sync'] = 0;
        $this->db->where('menuSalesID', $id);
        $this->db->where('wareHouseAutoID', current_warehouseID());
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);
        return $result;
    }

    function saveMenuSalesItemRemarkes($data, $menuSalesItemID, $menuSalesID)
    {
        $this->db->where('menuSalesID', $menuSalesID);
        $this->db->where('menuSalesItemID', $menuSalesItemID);
        $result = $this->db->update('srp_erp_pos_menusalesitems', $data);
        return $result;
    }

    function get_add_on_list($menuSalesItemID)
    {
        $cmpid = current_companyID();
        $result = $this->db->query("SELECT * from srp_erp_pos_menumaster WHERE companyID = '$cmpid' AND isAddOn= 1 AND isDeleted=0 AND menuStatus=1 ;")->result_array();
        return $result;
    }

    function saveAddon()
    {
        $menuSalesID = $this->input->post('invoiceIDMenusales');
        if (!empty($this->input->post('menuItemRemarkes'))) {
            $menuSalesItemID = $this->input->post('itmID');
            $menuItemRemarkes = $this->input->post('menuItemRemarkes');

            if (!empty($menuItemRemarkes)) {
                $data['remarkes'] = $menuItemRemarkes;

                $this->db->where('menuSalesID', $menuSalesID);
                $this->db->where('menuSalesItemID', $menuSalesItemID);
                $this->db->where('id_store', current_warehouseID());
                $result = $this->db->update('srp_erp_pos_menusalesitems', $data);

            }
        }

        $menuSalesItemIDaddon = $this->input->post('menuSalesItemIDaddon');
        $results = $this->db->delete('srp_erp_pos_addon', array('menuSalesItemID' => $menuSalesItemIDaddon));
        if (!empty($this->input->post('addonCheck'))) {
            foreach ($this->input->post('addonCheck') as $val) {
                $this->db->set('menuSalesItemID', $this->input->post('menuSalesItemIDaddon'));
                $this->db->set('menuMasterID', $val);

                $result = $this->db->insert('srp_erp_pos_addon');
            }
        }
        if ($result) {
            return array('s', 'Add On Added Successfully', $menuSalesID);
        } else {
            return array('e', 'Error In Adding Add On');
        }
    }

    function updateQty()
    {
        $menuSalesItemID = $this->input->post('menuSalesItemID');
        $qty = $this->input->post('qty');
        $outletID = $this->input->post('outletID');
        if ($outletID > 0) {
            $outletID = get_outletID();
        }

        $data['qty'] = $qty;

        $this->db->where('menuSalesItemID', $menuSalesItemID);
        $this->db->where('id_store', get_outletID());
        $result = $this->db->update('srp_erp_pos_menusalesitems', $data);
        if ($result) {
            return array('s', 'QTY Updated Successfully');
        } else {
            return array('e', 'Error In Updating QTY');
        }


    }


    function get_customerInfo($customerID)
    {
        $this->db->select("*");
        $this->db->from("srp_erp_pos_customers");
        $this->db->where('customerID', $customerID);
        $result = $this->db->get()->row_array();
        return $result;
    }

    function save_send_pos_email()
    {
        /**/
        $sen = 0;
        $comapnyemail = $this->pos_policy->isCompanyEmail();
        $companyid = current_companyID();

        $this->db->select("company_email");
        $this->db->from("srp_erp_company");
        $this->db->where('company_id', $companyid);
        $company_email = $this->db->get()->row_array();
        $compmail = $company_email['company_email'];

        $invoiceID = isPos_invoiceSessionExist();
        $invoiceID = $invoiceID ? $invoiceID : $this->input->post('invoiceID');
        $invoice = $this->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID);
        $masters = $this->get_srp_erp_pos_menusalesmaster($invoiceID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $msg = $this->load->view('system/pos/email/restaurant-pos-dotmatric-email', $data, true);

        /**/


        $emal = $this->input->post('emailAddress');
        $this->load->library('MY_PHPMailer');
        $mail = new MY_PHPMailer(); // create a new object
        $mail->AddEmbeddedImage(' ../lib/dist/img/VotexsupportDesk.png', 'logo_2u');
        //$mail->AddEmbeddedImage(' ../lib/dist/img/VotexLogo.png', 'logo');

        $mail->IsSMTP(); // enable SMTP
        $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
        $mail->Host = "smtpout.secureserver.net";
        $mail->Port = 465; // or 587
        $mail->IsHTML(true);
        $mail->Username = "support_admin@xupportcloud.com";
        $mail->Password = "P@ssw0rd240!";
        $mail->SetFrom("support_admin@xupportcloud.com");
        $mail->Subject = "Receipt";
        $mail->Body = $msg;
        $mail->AddAddress("$emal");
        if (isset($comapnyemail) && $comapnyemail) {
            $mail->AddCC("$compmail");
        }

        if ($sen == 0) {
            $sen = $sen++;
            $mailsend = $mail->Send();
        }
        if (!$mailsend) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo json_encode(array("error" => 0, "message" => "Mail sent"));
        }

    }

    function get_srp_erp_pos_menusalesitems_all()
    {
        $this->db->select("salesItem.*,warehouseMenu.menuMasterID,warehouseCategory.menuCategoryID");
        $this->db->from("srp_erp_pos_menusalesitems salesItem");
        $this->db->join("srp_erp_pos_warehousemenumaster warehouseMenu", "warehouseMenu.warehouseMenuID = salesItem.warehouseMenuID", "INNER");
        $this->db->join("srp_erp_pos_warehousemenucategory warehouseCategory", "warehouseCategory.autoID = salesItem.warehouseMenuCategoryID", "inner");

        $result = $this->db->get()->result_array();
        return $result;
    }

    function get_srp_erp_pos_menusalesmaster_all()
    {
        $this->db->select('menuSales .*, cType.customerDescription');
        $this->db->from('srp_erp_pos_menusalesmaster menuSales');
        $this->db->join('srp_erp_customertypemaster cType', 'cType.customerTypeID = menuSales.customerTypeID', 'left'); /*customerTypeID*/
        $result = $this->db->get()->result_array();
        return $result;
    }

    function void_bill()
    {
        $outletID = get_outletID();
        $data['isVoid'] = 1;
        $data['voidBy'] = current_user();
        $data['voidDatetime'] = current_date();
        $data['is_sync'] = 0;
        $this->db->where('menuSalesID', $this->input->post('menuSalesID'));
        $this->db->where('wareHouseAutoID', $outletID);
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);
        if ($result) {
            return array('s', 'Record Updated successfully');
        }
    }

    function un_void_bill()
    {
        $outletID = get_outletID();
        $data['isVoid'] = 0;
        $data['voidBy'] = null;
        $data['voidDatetime'] = null;
        $this->db->where('menuSalesID', $this->input->post('menuSalesID'));
        $this->db->where('wareHouseAutoID', $outletID);
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);
        if ($result) {
            return array('s', 'Record Updated successfully');
        }
    }

    function get_deliveryPersonReport($dateFrom, $dateTo, $customerID, $Outlets = null, $cashier = null)
    {
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND srp_erp_pos_menusalesmaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND srp_erp_pos_menusalesmaster.wareHouseAutoID IN(" . $Outlets . ")";
        }


        $qString = '';
        if ($cashier != null) {
            $qString = " AND createdUserID IN(" . $cashier . ") ";
        }

        if ($customerID) {
            $customerID = join(',', $customerID);
            $companyID = current_companyID();
            $q = "SELECT
                        menuSalesID,
                        DATE_FORMAT(createdDateTime, ' %d-%m-%Y') AS billdate,
                        DATE_FORMAT(createdDateTime, ' %h-%i %p') AS billtime,
                        netTotal,
                        deliveryCommission,
                        subTotal,
                        deliveryCommissionAmount,
                        netTotal * (deliveryCommission/100) AS CommissionAmount,
                    srp_erp_pos_customers.customerName
                    FROM
                        srp_erp_pos_menusalesmaster
                    JOIN srp_erp_pos_customers on srp_erp_pos_menusalesmaster.deliveryPersonID = srp_erp_pos_customers.customerID
                    WHERE
                        deliveryPersonID IN($customerID)
                        AND srp_erp_pos_menusalesmaster.companyID = $companyID
                    AND isHold = 0
                    AND isVoid = 0
                    AND DATE_FORMAT(createdDateTime, '%Y-%m-%d') BETWEEN '$dateFrom'   AND '$dateTo' " . $outletFilter . $qString . $outletsFilter;
            $result = $this->db->query($q)->result_array();

            // echo $this->db->last_query();

            return $result;
        } else {

        }

    }

    function get_discountReport($dateFrom, $dateTo, $customerID, $Outlets = null, $cashier = null)
    {
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND srp_erp_pos_menusalesmaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND srp_erp_pos_menusalesmaster.wareHouseAutoID IN(" . $Outlets . ")";
        }


        $qString = '';
        if ($cashier != null) {
            $qString = " AND createdUserID IN(" . $cashier . ") ";
        }


        $generalDiscount = false;
        if (!empty($customerID)) {
            foreach ($customerID as $key => $discountID) {
                if ($discountID == -1) {
                    unset($customerID[$key]);
                    $generalDiscount = true;

                }

            }
        }


        $whereSQL = '';

        if (!empty($customerID)) {
            $customerID = join(',', $customerID);
            $whereSQL .= 'AND (';
            $whereSQL .= ' promotionID IN (' . $customerID . ') ';
            if ($generalDiscount) {
                $whereSQL .= ' OR discountPer>0 ';
            }
            $whereSQL .= ')';
        } else {
            if ($generalDiscount) {
                $whereSQL .= ' AND discountPer>0 ';
            } else {
                $whereSQL .= ' AND discountPer=-1 ';
            }
        }

        $companyID = current_companyID();
        $q = "SELECT
                    menuSalesID,
                    invoiceCode,
                    DATE_FORMAT( createdDateTime, '%d-%m-%Y' ) AS billdate,
                    DATE_FORMAT( createdDateTime, '%h:%i %p' ) AS billtime,
                    subTotal + discountAmount + promotionDiscountAmount AS grossTotal,
                    subTotal AS netTotal,
                    discountPer AS generalDiscount,
                    discountAmount AS generalDiscountAmount,
                    promotionDiscount,
                    promotionDiscountAmount,
                    srp_erp_pos_customers.customerName  AS discountTypes,
                    srp_erp_pos_customers.customerName 
                FROM
                    srp_erp_pos_menusalesmaster
                    LEFT JOIN srp_erp_pos_customers ON srp_erp_pos_menusalesmaster.promotionID = srp_erp_pos_customers.customerID 
                WHERE
                    srp_erp_pos_menusalesmaster.companyID = $companyID 
                    AND isHold = 0 
                    AND isVoid = 0 
                    AND createdDateTime BETWEEN " . "'" . $dateFrom . "'   AND '" . $dateTo . "' " . $outletFilter . $qString . $outletsFilter . $whereSQL . "
                    ORDER BY
	                    menuSalesID ASC";

        $result = $this->db->query($q)->result_array();

        //echo $this->db->last_query();

        return $result;

    }

    function get_paymentCollection($dateFrom, $dateTo, $Outlets = null, $cashier = null)
    {
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND msm.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND msm.wareHouseAutoID IN(" . $Outlets . ")";
        }


        $qString = '';
        if ($cashier != null) {
            $qString = " AND msm.createdUserID IN(" . $cashier . ") ";
        }


        $companyID = current_companyID();
        $q = "SELECT
                    msm.menuSalesID,
                    msm.invoiceCode,
                    DATE_FORMAT( msm.createdDateTime, '%d-%m-%Y' ) AS billdate,
                    delivery.deliveryDate AS deliveryDate,
                    msp_tmp.paymentDate AS paymentDate,
                    msm.subTotal AS billAmount,
                    msm.isHold AS isHold,
                    IF ( msm.isHold = 0, 1, 0 ) AS DispatchedYN,
                    msp_tmp.amountPaid,
                    otype.customerDescription,
                    pcm_description AS paidType,
                    concat( cm.customerName, \" - \", delivery.phoneNo ) AS customerInfo,
                    ( msm.subTotal - IFNULL( msp_tmp.amountPaid, 0 ) ) AS balance 
                FROM
                    srp_erp_pos_menusalesmaster msm
                    INNER JOIN (
                SELECT
                    sum( IFNULL( amount, 0 ) ) AS amountPaid,
                    menuSalesID,
                    GROUP_CONCAT( pcm.description ) AS pcm_description,
                    DATE_FORMAT( createdDateTime, '%d-%m-%Y' ) AS paymentDate 
                FROM
                    srp_erp_pos_menusalespayments AS msp
                    LEFT JOIN srp_erp_pos_paymentglconfigmaster pcm ON pcm.autoID = msp.paymentConfigMasterID 
                WHERE
                    createdDateTime BETWEEN '" . $dateFrom . "' 
                    AND '" . $dateTo . "' 
                GROUP BY
                    menuSalesID 
                    ) AS msp_tmp ON msp_tmp.menuSalesID = msm.menuSalesID
                    LEFT JOIN srp_erp_pos_deliveryorders delivery ON delivery.menuSalesMasterID = msm.menuSalesID
                    LEFT JOIN srp_erp_pos_customermaster cm ON cm.posCustomerAutoID = delivery.posCustomerAutoID
                    LEFT JOIN srp_erp_customertypemaster otype ON otype.customerTypeID = msm.customerTypeID 
                WHERE
                    msm.companyID = '" . $companyID . "' 
                    AND ( ( delivery.deliveryDate IS NOT NULL ) OR ( msm.isHold = 0 AND delivery.deliveryDate IS NULL ) ) 
                    AND msm.isVoid = 0 
                    " . $qString . "
                    " . $outletsFilter . "
                ORDER BY
                    msm.menuSalesID DESC";
        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_deliveryPerson($customerID)
    {
        $q = "SELECT
                    customerName
                FROM
                    srp_erp_pos_customers
                WHERE
                    customerID = $customerID
                     ";
        $result = $this->db->query($q)->row_array();

        //echo $this->db->last_query();

        return $result;
    }

    function batch_get_srp_erp_pos_menusalesmaster_all($limit = 10)
    {
        $this->db->select(' * ');
        $this->db->from('srp_erp_pos_menusalesmaster menuSales');
        $this->db->where('isHold', 0);
        $this->db->limit($limit);
        $result = $this->db->get()->result_array();

        return $result;
    }

    function batch_update_srp_erp_pos_menusalesmaster($data)
    {
        $this->db->update_batch('srp_erp_pos_menusalesmaster', $data, 'menuSalesID');
        $row = $this->db->affected_rows();
        return $row;

    }


    function get_srp_erp_pos_menusalesitems_byMenusalesID($menuSalesID)
    {
        $this->db->select(' * ');
        $this->db->from('srp_erp_pos_menusalesitems');
        $this->db->where('menuSalesID', $menuSalesID);
        $result = $this->db->get()->result_array();

        return $result;
    }


    function get_productMixPacks_sales($dateFrom, $dateTo, $Outlets = null, $cashier = null)
    {
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }


        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $q = "SELECT valuePack.menuID,  menuMaster.menuMasterDescription, sum(valuePack.qty) AS qty, mSize.description AS menuSize   
            FROM srp_erp_pos_menusalesmaster salesMaster 
            LEFT JOIN  srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID
            LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID
            LEFT JOIN srp_erp_pos_valuepackdetail valuePack ON valuePack.menuSalesItemID = salesItem.menuSalesItemID
            LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = valuePack.menuID
            LEFT JOIN srp_erp_pos_menusize mSize ON mSize.menuSizeID = menuMaster.menuSizeID
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND DATE_FORMAT(
                salesMaster.menuSalesDate,
                '%Y-%m-%d'
            ) BETWEEN '" . $dateFrom . "'
            AND '" . $dateTo . "'
            AND menuMaster.menuMasterID IS NOT NULL
            AND salesMaster.companyID = " . current_companyID() . "
            " . $outletFilter . "
            " . $outletsFilter . "
            " . $qString . "
            GROUP BY
                valuePack.menuID
            ORDER BY
                menuMaster.menuMasterDescription";
        $result = $this->db->query($q)->result_array();

        //echo '<div class="hide"> '.$q.'</div> ';

        return $result;
    }

    function productMix_menuItem($dateFrom, $dateTo, $Outlets = null, $cashier = null)
    {

        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }


        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $q = "SELECT menuMaster.menuMasterID, menuMaster.menuMasterDescription,  sum( salesItem.salesPriceNetTotal ) AS itemPriceTotal, sum(salesItem.qty) AS qty, mSize.description AS menuSize
            FROM
                srp_erp_pos_menusalesmaster salesMaster
            LEFT JOIN srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID
            LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID
            LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = warehouse.menuMasterID
            LEFT JOIN srp_erp_pos_menusize mSize ON mSize.menuSizeID = menuMaster.menuSizeID
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = " . current_companyID() . "
            AND menuMaster.isPack = 0
            AND DATE_FORMAT(
                salesMaster.menuSalesDate,
                '%Y-%m-%d'
            ) BETWEEN '" . $dateFrom . "'
            AND '" . $dateTo . "'
            AND menuMaster.menuMasterID IS NOT NULL
            " . $outletFilter . "
            " . $outletsFilter . "
            " . $qString . "
            GROUP BY
                menuMaster.menuMasterID
            ORDER BY
                menuMaster.menuMasterDescription";

        $result = $this->db->query($q)->result_array();
        //echo '<span class="hide"> '.$q.'</span> ';

        return $result;
    }

    function get_franchiseReport($dateFrom, $dateTo, $Outlets = null, $cashier = null)
    {
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }

        $outletsFilter = '';
        if ($Outlets != null) {
            $outletsFilter = "AND salesMaster.wareHouseAutoID IN(" . $Outlets . ")";
        }


        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $companyID = current_companyID();
        $q = "SELECT
            salesMaster.salesDay,
            salesMaster.menuSalesDate,
            Customer.customerDescription,
            SUM(
                IF (
                    (Customer.customerDescription = 'Eat-in' OR Customer.customerDescription = 'dine-in' ),
                    (salesMaster.subTotal ),
                    0
                )
            ) AS EatInTotal,
            SUM(
        
                IF (
                    (Customer.customerDescription = 'Eat-in' OR Customer.customerDescription = 'dine-in' ),
                    1,
                    0
                )
            ) AS EatInQty,
        
            SUM(
                IF (
                    (Customer.customerDescription = 'Take-away' || Customer.customerDescription = 'Direct sale'),
                    (salesMaster.subTotal ),
                    0
                )
            ) AS TakeAwayTotal,
            SUM(
        
                IF (
                    (Customer.customerDescription = 'Take-away' || Customer.customerDescription = 'Direct sale'),
                    1,
                    0
                )
            ) AS TakeAwayQty,
            SUM(
                IF (
                    Customer.customerDescription = 'Delivery Orders',
                    (salesMaster.subTotal ),
                    0
                )
            ) AS DeliveryOrdersTotal,
            SUM(
        
                IF (
                    Customer.customerDescription = 'Delivery Orders',
                    1,
                    0
                )
            ) AS DeliveryOrdersQty,
            sum(salesMaster.subTotal) AS NetTotal,
            sum(salesMaster.totalTaxAmount) AS totalTax,
            count(salesMaster.menuSalesID) AS netQty
        FROM
            srp_erp_pos_menusalesmaster AS salesMaster
        LEFT JOIN srp_erp_customertypemaster AS Customer ON Customer.customerTypeID = salesMaster.customerTypeID
        WHERE
            salesMaster.isVoid = 0
        AND salesMaster.isHold = 0
        AND salesMaster.companyID = '$companyID'
        AND DATE_FORMAT(
            salesMaster.menuSalesDate,
            '%Y-%m-%d'
        ) BETWEEN '$dateFrom'
        AND '$dateTo'
        " . $outletFilter . "
        " . $outletsFilter . "
        " . $qString . "
        GROUP BY
            salesMaster.menuSalesDate
        ORDER BY
            salesMaster.menuSalesDate ASC ";
        $result = $this->db->query($q)->result_array();

        //echo $this->db->last_query();

        return $result;

    }

    function updateCurrentMenuWAC()
    {
        $companyID = current_companyID();
        $outletID = get_outletID();
        $this->db->select("*");
        $this->db->from("srp_erp_company");
        $this->db->where("company_id", $companyID);
        $company = $this->db->get()->row_array();
        if (!empty($company) && $company['pos_isFinanceEnables'] == 1 && false) { // WAC UPDATE stopped by Hisham
            $this->db->select('*');
            $this->db->from('srp_erp_pos_wac_updatehistory');
            $this->db->where('companyID', $companyID);
            $this->db->where('updatedDate', current_date(false));
            $result = $this->db->get()->row_array();

            if (empty($result)) {
                /** insert to history */
                $data['companyID'] = $companyID;
                $data['updatedDate'] = current_date();
                $data['timestamp'] = format_date_mysql_datetime();
                $this->db->insert('srp_erp_pos_wac_updatehistory', $data);

                /** update WAC in menusales detail */
                $q = "UPDATE srp_erp_pos_menudetails AS tmpMenuDetails,
                     (
                        SELECT
                            menudetails.menuDetailID,
                            menudetails.menuDetailDescription,
                            menudetails.cost,
                            menudetails.actualInventoryCost,
                            menudetails.UOM AS uom_pos,
                            unitOfMeasure.UnitID AS uomID_pos,
                            itemmaster.defaultUnitOfMeasure AS uom,
                            itemmaster.defaultUnitOfMeasureID AS uomID,
                            ABS(
                                itemmaster.companyLocalWacAmount
                            ) AS companyLocalWacAmount,
                    
                        IF ( unitOfMeasure.UnitID = itemmaster.defaultUnitOfMeasureID,  1, ( SELECT conversion FROM srp_erp_unitsconversion WHERE masterUnitID = unitOfMeasure.UnitID AND subUnitID = itemmaster.defaultUnitOfMeasureID ) ) AS conversion,
                        menudetails.qty AS Qty,
                    
                    IF ( unitOfMeasure.UnitID = itemmaster.defaultUnitOfMeasureID,
                            menudetails.qty * ( ABS( itemmaster.companyLocalWacAmount ) ),
                         ( SELECT conversion FROM srp_erp_unitsconversion WHERE masterUnitID = unitOfMeasure.UnitID
                            AND subUnitID = itemmaster.defaultUnitOfMeasureID ) * menudetails.qty * ( ABS( itemmaster.companyLocalWacAmount ) ) 
                    ) 
                    AS newWAC
                    FROM
                        srp_erp_pos_menudetails menudetails
                    LEFT JOIN srp_erp_pos_menumaster menumaster ON menudetails.menuMasterID = menumaster.menuMasterID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = menudetails.itemAutoID
                    LEFT JOIN srp_erp_unit_of_measure unitOfMeasure ON unitOfMeasure.UnitShortCode = menudetails.UOM
                    WHERE
                        menumaster.companyID = '" . $companyID . "'
                    AND unitOfMeasure.companyID = '" . $companyID . "'
                    AND menudetails.isYield = 0
                    )
                     tmpOutput
                    SET tmpMenuDetails.actualInventoryCost = tmpMenuDetails.cost,
                     tmpMenuDetails.cost = tmpOutput.newWAC
                    WHERE tmpOutput.menuDetailID = tmpMenuDetails.menuDetailID  AND tmpOutput.newWAC != tmpMenuDetails.cost";
                $this->db->query($q);

                /** update the menu cost */
                $q2 = "UPDATE srp_erp_pos_menumaster AS menumasterTmp,
                         (
                            SELECT
                                menumaster.menuMasterID,
                                menumaster.menuCost AS menuCost,
                                SUM(menudetails.cost) AS menuCostSum
                            FROM
                                srp_erp_pos_menumaster AS menumaster
                            LEFT JOIN srp_erp_pos_menudetails AS menudetails ON menumaster.menuMasterID = menudetails.menuMasterID
                            WHERE
                                menudetails.cost IS NOT NULL
                            AND menumaster.companyID = '" . $companyID . "'
                            GROUP BY
                                menumaster.menuMasterID
                            HAVING
                                menuCost <> menuCostSum
                        ) subList
                        SET menumasterTmp.menuCost = subList.menuCostSum
                        WHERE
                            menumasterTmp.menuMasterID = subList.menuMasterID
                        AND menumasterTmp.companyID = '" . $companyID . "'
                        AND menumasterTmp.menuCost <> subList.menuCostSum";
                $this->db->query($q2);

                outlet_generateMissingItemsToOutlets($companyID, $outletID);

                return array('error' => 0, 'message' => 'WAC & Item Master for outlet  updated successfully', 'output' => $result);
            } else {
                return array('error' => 1, 'message' => 'WAC & Item Master already updated for this day');
            }
        } else {
            return array('error' => 1, 'message' => 'Finance not enabled for this company.');
        }
    }

    function update_pos_payments()
    {
        $invoiceID = isPos_invoiceSessionExist();
        $outletID = get_outletID();


        if ($invoiceID) {

            $totalPaid = 0;

            $isConfirmedDeliveryOrder = pos_isConfirmedDeliveryOrder($invoiceID);

            $createdUserGroup = user_group();
            $createdPCID = current_pc();
            $createdUserID = current_userID();
            $createdUserName = current_user();
            $createdDateTime = format_date_mysql_datetime();
            $timestamp = format_date_mysql_datetime();
            $companyID = current_companyID();
            $companyCode = current_company_code();

            $masterData = get_pos_invoice_id($invoiceID);
            $counterInfo = get_counterData();
            $shiftID = $counterInfo['shiftID'];

            $reference = $this->input->post('reference');
            $customerAutoIDs = $this->input->post('customerAutoID');
            $paymentTypes = $this->input->post('paymentTypes');
            $cardTotalAmount = $this->input->post('cardTotalAmount');
            $netTotalAmount = $this->input->post('netTotalAmount');
            $isDelivery = $this->input->post('isDelivery');
            $isOnTimePayment = $this->input->post('isOnTimePayment');
            $payableDeliveryAmount = $this->input->post('totalPayableAmountDelivery_id');
            $returnChange = $this->input->post('returned_change');
            $grossTotal = $this->input->post('total_payable_amt');
            $promotional_discount = $this->input->post('promotional_discount');

            if (!empty($paymentTypes)) {
                $i = 0;
                foreach ($paymentTypes as $key => $amount) {
                    if ($amount > 0) {

                        $totalPaid += $amount;
                        $this->db->select('configDetail.GLCode,configMaster.autoID, configMaster.glAccountType');
                        $this->db->from('srp_erp_pos_paymentglconfigdetail configDetail');
                        $this->db->join('srp_erp_pos_paymentglconfigmaster configMaster', 'configDetail.paymentConfigMasterID = configMaster.autoID', 'left');
                        $this->db->where('configDetail.ID', $key);
                        $r = $this->db->get()->row_array();

                        if ($r['glAccountType'] == 1) {
                            /** Cash Payment */
                            if ($isDelivery == 1 && $isOnTimePayment == 1) {
                                $cashPaidAmount = $payableDeliveryAmount - $cardTotalAmount;
                            } else {
                                $cashPaidAmount = $netTotalAmount - $cardTotalAmount;

                                if ($isConfirmedDeliveryOrder) {
                                    $advancePayment = get_paidAmount($invoiceID);

                                    //$payable = $netTotalAmount - ($advancePayment + $cardTotalAmount); bug because of this.
                                    $payable = $grossTotal - ($advancePayment + $cardTotalAmount);

                                    if ($amount == $payable) {
                                        $cashPaidAmount = $amount;
                                        $returnChange = 0;
                                    } else if ($amount > $payable) {
                                        $cashPaidAmount = $payable;
                                        $returnChange = $amount - $payable;
                                    } else {

                                        /** Advance payment */
                                        $cashPaidAmount = $amount;
                                        $returnChange = 0;
                                    }
                                }
                            }
                            $amount = $cashPaidAmount;
                        }

                        /** Credit Customer's GL Code should be picked from Customer */
                        $GLCode = null;
                        if ($r['autoID'] == 7) {
                            if (isset($customerAutoIDs[$key]) && $customerAutoIDs[$key]) {
                                $receivableAutoID = $this->db->select('receivableAutoID')
                                    ->from('srp_erp_customermaster')
                                    ->where('customerAutoID', $customerAutoIDs[$key])
                                    ->get()->row('receivableAutoID');
                                $GLCode = $receivableAutoID;
                            }

                        }

                        $paymentData[$i]['menuSalesID'] = $invoiceID;
                        $paymentData[$i]['wareHouseAutoID'] = $outletID;
                        $paymentData[$i]['paymentConfigMasterID'] = $r['autoID'];
                        $paymentData[$i]['paymentConfigDetailID'] = $key;
                        $paymentData[$i]['GLCode'] = $r['autoID'] == 7 ? $GLCode : $r['GLCode'];
                        $paymentData[$i]['glAccountType'] = $r['glAccountType'];
                        $paymentData[$i]['amount'] = $amount;
                        $paymentData[$i]['reference'] = isset($reference[$key]) ? $reference[$key] : null;
                        $paymentData[$i]['customerAutoID'] = isset($customerAutoIDs[$key]) ? $customerAutoIDs[$key] : null;

                        /** Common Data */
                        $paymentData[$i]['createdUserGroup'] = $createdUserGroup;
                        $paymentData[$i]['createdPCID'] = $createdPCID;
                        $paymentData[$i]['createdUserID'] = $createdUserID;
                        $paymentData[$i]['createdUserName'] = $createdUserName;
                        $paymentData[$i]['createdDateTime'] = $createdDateTime;
                        $paymentData[$i]['timestamp'] = $timestamp;

                        /** Java App Redeem */
                        if ($r['autoID'] == 25) {
                            $data_JA['menuSalesID'] = $invoiceID;
                            $data_JA['outletID'] = $outletID;
                            $data_JA['appPIN'] = isset($reference[$key]) ? $reference[$key] : null;;
                            $data_JA['amount'] = $amount;
                            $data_JA['companyID'] = $companyID;
                            $data_JA['companyCode'] = $companyCode;
                            $data_JA['createdUserGroup'] = $createdUserGroup;
                            $data_JA['createdPCID'] = $createdPCID;
                            $data_JA['createdUserID'] = $createdUserID;
                            $data_JA['createdDateTime'] = $createdDateTime;
                            $data_JA['createdUserName'] = $createdUserName;
                            $data_JA['timestamp'] = $createdDateTime;

                            $this->db->insert('srp_erp_pos_javaappredeemhistory', $data_JA);
                        }

                        /** Gift Card Top Up*/
                        if ($r['autoID'] == 5) {
                            $barCode = isset($reference[$key]) ? $reference[$key] : null;
                            $cardInfo = get_giftCardInfo($barCode);


                            $dta_GC['wareHouseAutoID'] = $outletID;
                            $dta_GC['cardMasterID'] = !empty($cardInfo) ? $cardInfo['cardMasterID'] : null;
                            $dta_GC['barCode'] = isset($reference[$key]) ? $reference[$key] : null;
                            $dta_GC['posCustomerAutoID'] = !empty($cardInfo) ? $cardInfo['posCustomerAutoID'] : null;
                            $dta_GC['topUpAmount'] = abs($amount) * -1;
                            $dta_GC['points'] = 0;
                            $dta_GC['glConfigMasterID'] = $r['autoID'];
                            $dta_GC['glConfigDetailID'] = $key;
                            $dta_GC['menuSalesID'] = $invoiceID;
                            $dta_GC['giftCardGLAutoID'] = $r['autoID'] == 7 ? null : $r['GLCode'];
                            $dta_GC['outletID'] = $outletID;
                            $dta_GC['reference'] = 'redeem barcode ' . $barCode;
                            $dta_GC['companyID'] = $companyID;
                            $dta_GC['companyCode'] = $companyCode;
                            $dta_GC['createdPCID'] = $createdPCID;
                            $dta_GC['createdUserID'] = $createdUserID;
                            $dta_GC['createdDateTime'] = $createdDateTime;
                            $dta_GC['createdUserName'] = $createdUserName;
                            $dta_GC['createdUserGroup'] = $createdUserGroup;
                            $dta_GC['timestamp'] = $createdDateTime;
                            $dta_GC['shiftID'] = $shiftID;
                            $dta_GC['id_store'] = $outletID;

                            $this->db->insert('srp_erp_pos_cardtopup', $dta_GC);


                        }

                        $i++;
                    } // end if
                } //end foreach
                //print_r($paymentData);
                if (isset($paymentData) && !empty($paymentData)) {
                    $this->db->insert_batch('srp_erp_pos_menusalespayments', $paymentData);

                }
                if ($totalPaid > 0) {

                    $payable = $this->input->post('total_payable_amt');
                    //$balancePayable = $totalPaid - ($payable > 0 ? $payable : 0);
                    $this->db->update('srp_erp_pos_menusalesmaster', array('cashReceivedAmount' => $totalPaid, 'balanceAmount' => $returnChange, 'is_sync' => 0), array('menuSalesID' => $invoiceID));
                }
            }

            $get_outletID = get_outletID();
            $current_companyID = current_companyID();
            $isOutletTaxEnabled =isOutletTaxEnabled($get_outletID, $current_companyID);

            if($isOutletTaxEnabled==true){
                $this->insert_outlet_tax($outletID, $grossTotal, $promotional_discount, $invoiceID);
            }

            return true;
        } else {
            return false;
        }
    }

    function insert_outlet_tax($outletID, $grossTotal, $promotional_discount, $invoiceID)
    {
        //insert outlet tax table
        $outlet_tax_list = $this->outlet_tax_list($outletID);
        $amount_with_discount = $grossTotal - $promotional_discount;
        foreach ($outlet_tax_list as $outlet_tax) {
            $taxPercentage = $outlet_tax->taxPercentage;
            $tax_amount = ($amount_with_discount / 100) * $taxPercentage;
            //print_r($amount_with_discount);
            //print_r($outlet_tax);
            //print_r($tax_amount);
            $amount_with_tax = $amount_with_discount + $tax_amount;
            $menusalesoutlettaxes = array(
                "wareHouseAutoID" => $outletID,
                "menuSalesID" => $invoiceID,
                "outletTaxID" => $outlet_tax->outletTaxID,
                "taxmasterID" => $outlet_tax->taxMasterID,
                "GLCode" => $outlet_tax->supplierGLAutoID,
                "taxPercentage" => $taxPercentage,
                "taxAmount" => $tax_amount
            );
            $this->db->insert('srp_erp_pos_menusalesoutlettaxes', $menusalesoutlettaxes);
        }
    }

    function outlet_tax_list($outletID)
    {
        $query = $this->db->query("SELECT 
 srp_erp_pos_outlettaxmaster.taxPercentage,
 srp_erp_pos_outlettaxmaster.outletTaxID,
 srp_erp_pos_outlettaxmaster.taxMasterID,
 srp_erp_taxmaster.supplierGLAutoID
 FROM `srp_erp_pos_outlettaxmaster` 
JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID=srp_erp_pos_outlettaxmaster.taxMasterID
where srp_erp_pos_outlettaxmaster.warehouseAutoID=$outletID AND srp_erp_pos_outlettaxmaster.isDeleted=0");
        return $query->result();
    }

    function get_report_fullyDiscountBills_admin($date, $data2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                  count( salesMaster.menuSalesID ) AS fullyDiscountBills 
                FROM
                      srp_erp_pos_menusalesmaster AS salesMaster
                      LEFT JOIN srp_erp_pos_menusalespayments msp ON msp.menuSalesID = salesMaster.menuSalesID
                      AND msp.wareHouseAutoID = salesMaster.wareHouseAutoID
                WHERE
                  msp.menuSalesID IS NULL 
                  AND salesMaster.isVoid = 0 
                  AND salesMaster.isHold = 0
                  AND salesMaster.companyID = '" . current_companyID() . "'
                  AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter;
        //echo $q;
        $result = $this->db->query($q)->row_array();
        return $result;
    }

    function update_menuSalesTax($menuSalesID)
    {
        $q = "INSERT INTO srp_erp_pos_menusalestaxes (
                wareHouseAutoID, menuSalesID,
                menuSalesItemID, menuID, menutaxID,
                taxmasterID, GLCode, taxPercentage, taxAmount,
                beforeDiscountTotalTaxAmount, menusalesDiscount,
                menusalesPromotionalDiscount, unitMenuTaxAmount,
                menusalesItemQty, companyID, companyCode,
                createdUserGroup, createdPCID, createdUserID,
                createdDateTime, createdUserName, modifiedPCID,
                modifiedUserID, modifiedDateTime, modifiedUserName,
                `timestamp`
                ) (SELECT
                    msm.wareHouseAutoID as wareHouseAutoID,
                    msm.menuSalesID as menuSalesID,
                    msi.menuSalesItemID as menuSalesItemID,
                    msi.menuID as menuID,
                    mt.menuTaxID as menutaxID,
                    mt.taxmasterID as taxmasterID,
                    tm.supplierGLAutoID as GLAutoID,
                    mt.taxPercentage as taxPercentage,
                    IF(msi.discountPer>0,IF(msm.promotionDiscount>0, (msi.qty * (mt.taxAmount - (mt.taxAmount*msi.discountPer/100)) * ((100-msm.discountPer)/100)) * ((100-msm.promotionDiscount)/100), msi.qty * (mt.taxAmount - (mt.taxAmount*msi.discountPer/100)) * ((100-msm.discountPer)/100)),IF(msm.promotionDiscount>0, (msi.qty * (mt.taxAmount - (mt.taxAmount*msi.discountPer/100)) * ((100-msm.discountPer)/100)) * ((100-msm.promotionDiscount)/100), msi.qty * (mt.taxAmount - (mt.taxAmount*msi.discountPer/100)) * ((100-msm.discountPer)/100))) as taxAmount,	
                    msi.qty * mt.taxAmount as beforeDiscountTaxAmount,
                    msm.discountPer as menusalesDiscount,
                    msm.promotionDiscount as menusalesPromotionalDiscount,
                    mt.taxAmount as unitMenuTaxAmount,
                    msi.qty as menusalesItemQty,
                    msm.companyID as companyID,
                    msm.companyCode as companyCode,
                    msm.createdUserGroup as createdUserGroup,
                    msm.createdPCID as createdPCID,
                    msm.createdUserID as createdUserID,
                    CURRENT_TIMESTAMP() as createdDateTime,
                    msm.createdUserName as createdUserName,
                    null as modifiedPCID,
                    null as modifiedUserID,
                    null as modifiedDateTime,
                    null as modifiedUserName,
                    CURRENT_TIMESTAMP() as `timestamp`
                
                FROM
                    srp_erp_pos_menusalesmaster msm
                INNER JOIN srp_erp_pos_menusalesitems msi ON msi.menuSalesID = msm.menuSalesID
                INNER JOIN srp_erp_pos_warehousemenumaster whm ON whm.menuMasterID = msi.menuID
                INNER JOIN srp_erp_pos_menutaxes mt ON mt.menuMasterID = msi.menuID
                INNER JOIN srp_erp_taxmaster	tm ON mt.taxmasterID = tm.taxMasterAutoID
                WHERE
                    msm.menuSalesID = '" . $menuSalesID . "' 
                    AND whm.warehouseID = msm.wareHouseAutoID 
                    AND whm.isDeleted = 0 AND whm.isActive = 1 
                    AND whm.isTaxEnabled=1 )";
        $r = $this->db->query($q);

        /** update total Tax */
        if($r==true){
            $q2 = "UPDATE srp_erp_pos_menusalesmaster SET totalTaxAmount = ( SELECT sum(taxAmount) FROM srp_erp_pos_menusalestaxes WHERE menuSalesID = '" . $menuSalesID . "' ) WHERE menuSalesID = '" . $menuSalesID . "'";
            $r2 = $this->db->query($q2);

            if ($r2) {
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }
    }


    function update_menuSalesServiceCharge($menuSalesID)
    {
        $q = "INSERT INTO srp_erp_pos_menusalesservicecharge (
                wareHouseAutoID, menuSalesID, menuSalesItemID, menuServiceChargeID, menuMasterID, serviceChargePercentage, serviceChargeAmount,
                GLAutoID, beforeDiscountTotalServiceCharge, menusalesDiscount,
                menusalesPromotionalDiscount, unitMenuServiceCharge, menusalesItemQty,
                companyID, companyCode, createdUserGroup, createdPCID, createdUserID,
                createdDateTime, createdUserName, modifiedPCID, modifiedUserID,
                modifiedDateTime, modifiedUserName, `timestamp`)
                (SELECT
                msm.wareHouseAutoID as wareHouseAutoID,
                msi.menuSalesID as menuSalesID,
                msi.menuSalesItemID as menuSalesItemID,
                msc.menuServiceChargeID AS menuServiceChargeID,
                msc.menuMasterID AS menuMasterID,
                msc.serviceChargePercentage AS serviceChargePercentage,
                IF(msi.discountPer > 0 , IF(msm.promotionDiscount>0, (msi.qty * (msc.serviceChargeAmount - (msc.serviceChargeAmount * msi.discountPer/100)) * ((100-msm.discountPer)/100)) * ((100-msm.promotionDiscount)/100), msi.qty * (msc.serviceChargeAmount - (msc.serviceChargeAmount * msi.discountPer/100)) * ((100-msm.discountPer)/100)) ,IF(msm.promotionDiscount>0, (msi.qty * msc.serviceChargeAmount * ((100-msm.discountPer)/100)) * ((100-msm.promotionDiscount)/100), msi.qty * msc.serviceChargeAmount * ((100-msm.discountPer)/100)))  as serviceChargeAmount,
                msc.GLAutoID AS GLAutoID,
                msi.qty * msc.serviceChargeAmount AS beforeDiscountTotalServiceCharge,
                msm.discountPer AS menusalesDiscount,
                msm.promotionDiscount AS menusalesPromotionalDiscount,
                msc.serviceChargeAmount AS unitMenuServiceCharge,
                msi.qty AS menusalesItemQty,
                msm.companyID AS companyID,
                msm.companyCode AS companyCode,
                msm.createdUserGroup AS createdUserGroup,
                msm.createdPCID AS createdPCID,
                msm.createdUserID AS createdUserID,
                CURRENT_TIMESTAMP () AS createdDateTime,
                msm.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP () AS `timestamp`
            FROM
                srp_erp_pos_menusalesmaster msm
            INNER JOIN srp_erp_pos_menusalesitems msi ON msi.menuSalesID = msm.menuSalesID
            INNER JOIN srp_erp_pos_menuservicecharge msc ON msc.menuMasterID = msi.menuID
            WHERE
                msm.menuSalesID = '" . $menuSalesID . "')";

        $r = $this->db->query($q);
        if($r==true){
            /**update total Service charge */
            $r2=$this->db->query("UPDATE srp_erp_pos_menusalesmaster SET serviceCharge = ( SELECT sum(serviceChargeAmount) FROM srp_erp_pos_menusalesservicecharge WHERE menuSalesID = '" . $menuSalesID . "' )  , is_sync = 0 WHERE menuSalesID = '" . $menuSalesID . "'");
            if ($r2) {
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }
    }


    function update_deliveryCommission($menuSalesID)
    {
        $q1 = "SELECT IF (netTotal IS NULL, 0, netTotal) + IF (totalTaxAmount IS NULL, 0, totalTaxAmount) + IF (serviceCharge IS NULL, 0, serviceCharge) AS totalTmp, deliveryCommission FROM srp_erp_pos_menusalesmaster WHERE menuSalesID = '" . $menuSalesID . "' AND isDelivery =1";
        $result = $this->db->query($q1)->row_array();
        if (!empty($result) && $result['totalTmp'] > 0) {
            $calculatedCommission = $result['totalTmp'] * ($result['deliveryCommission'] / 100);
            $q2 = "UPDATE srp_erp_pos_menusalesmaster SET deliveryCommissionAmount =  '" . $calculatedCommission . "' , is_sync = 0   WHERE menuSalesID = '" . $menuSalesID . "'  ";
            $this->db->query($q2);
            $r2=$this->db->query($q2);
            if($r2==true){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }


    function get_report_salesReport_totalSales($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }


        $q = "SELECT
                'Total Sales' AS Description,
                SUM(paidAmount) AS amount
            
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' "
            . $qString . $outletFilter;


        // echo $q;

        $result = $this->db->query($q)->row_array();
        return $result;
    }


    function get_report_salesReport_totalTaxes($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }


        $q = "SELECT
                taxMaster.taxDescription AS Description,
                SUM(tax.taxAmount) AS amount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            LEFT JOIN srp_erp_pos_menusalestaxes tax ON tax.menuSalesID = salesMaster.menuSalesID
            INNER JOIN srp_erp_taxmaster taxMaster ON taxMaster.taxMasterAutoID = tax.taxmasterID
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' 
            " . $qString . $outletFilter . "
            GROUP BY tax.taxmasterID";
        //echo $q;


        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_report_salesReport_ServiceCharge($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletID = $this->input->post('outletID');
        $outletFilter = '';
        if ($outletID > 0) {
            $outletFilter = " AND salesMaster.wareHouseAutoID = '" . $outletID . "' ";
        }


        $q = "SELECT
                'Service Charge' AS Description,
                SUM(sc.serviceChargeAmount) AS amount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            LEFT JOIN srp_erp_pos_menusalesservicecharge sc ON sc.menuSalesID = salesMaster.menuSalesID
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " . $qString . $outletFilter;
        //echo $q;


        $result = $this->db->query($q)->row_array();
        return $result;
    }

    function updateSendToKitchen()
    {
        $invoiceID = $this->input->post('menuSalesID');
        $data['isOrderPending'] = 1;
        $this->db->where('menuSalesID', $invoiceID);
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);

        if ($result) {

            $q = "UPDATE `srp_erp_pos_menusalesitems` SET `KOTAlarm` = 0, `isOrderPending` = 1 WHERE `menuSalesID` = '" . $invoiceID . "' AND ( `KOTAlarm` = -1 OR `isOrderPending` = -1)";
            $result = $this->db->query($q);


            $printSession = $this->session->userdata('accessToken');

            if (!empty($printSession)) {
                $auth = 1;
            } else {
                $auth = 0;
            }
            return array('error' => 0, 'code' => $invoiceID, 'message' => 'done', 'auth' => $auth);
        } else {
            return array('error' => 1, 'code' => 0, 'message' => 'KOT not updated', 'auth' => 0);
        }
    }


    function get_report_lessAmount_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                    salesMaster.deliveryCommission,
                    customers.customerName,
                    SUM(netTotal) AS netTotal,
                    SUM(deliveryCommissionAmount) AS lessAmount

                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                JOIN srp_erp_pos_customers customers ON customers.customerID = salesMaster.deliveryPersonID
                JOIN srp_erp_pos_paymentglconfigmaster configMaster ON  configMaster.autoID = salesMaster.paymentMethod
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
                AND NOT ISNULL(
                    salesMaster.deliveryPersonID
                )
                AND salesMaster.deliveryPersonID <> 0
                AND configMaster.autoID = 1 
                " . $qString . "
                " . $outletFilter . "
                GROUP BY
                    customers.customerName
                ORDER BY
                    salesMaster.isPromotion";

        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_report_lessAmount_promotion_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }


        $q = "SELECT
                    salesMaster.promotionDiscount AS deliveryCommission,
                    customers.customerName AS customerName,
                    SUM(grossTotal) AS netTotal,
                    SUM(IFNULL(promotionDiscountAmount,0) ) as lessAmount
              
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = salesMaster.promotionID
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
                AND NOT ISNULL(salesMaster.promotionID)
                AND salesMaster.promotionID <> 0
                " . $qString . "
                " . $outletFilter . "
                GROUP BY
                    customers.customerName
                ORDER BY
                    salesMaster.isPromotion";


        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_discount_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                'Java App' AS customerName,
            SUM(netTotal) AS netTotal,
            SUM(payments.amount) AS lessAmount
            FROM
                srp_erp_pos_menusalespayments AS payments
                JOIN srp_erp_pos_menusalesmaster  AS salesMaster ON  salesMaster.menuSalesID = payments.menuSalesID
            WHERE
                payments.paymentConfigMasterID = 25
            AND  salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID =  '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
             " . $qString . $outletFilter;


        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_discount_item_wise_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                    salesMaster.promotionDiscount AS deliveryCommission,
                    'Item Wise Discount' AS customerName,
                    SUM( grossTotal ) AS netTotal,
                    SUM( IFNULL( salesitem.discountAmount, 0 ) ) AS lessAmount 
              
                FROM
                    srp_erp_pos_menusalesitems AS salesitem
	LEFT JOIN srp_erp_pos_menusalesmaster salesMaster ON salesMaster.menuSalesID = salesitem.menuSalesID
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
                " . $qString . "
                " . $outletFilter . " ";


        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_javaAppDiscount_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                'Discounts' AS customerName,
            SUM(netTotal) AS netTotal,
            SUM(salesMaster.discountAmount) AS lessAmount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID =  '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
            AND salesMaster.discountAmount>0 " . $qString . $outletFilter;


        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_report_paymentMethod_admin($date, $data2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND payments.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                    configMaster.description as paymentDescription,
                    SUM(payments.amount) AS NetTotal,
                    count(payments.menuSalesPaymentID) as countTransaction
                FROM
                    srp_erp_pos_menusalespayments AS payments
                LEFT JOIN srp_erp_pos_menusalesmaster AS salesMaster ON payments.menuSalesID = salesMaster.menuSalesID AND payments.wareHouseAutoID = salesMaster.wareHouseAutoID
                LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = payments.paymentConfigMasterID
                WHERE
                  salesMaster.isVoid = 0 AND
                  salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . "
                GROUP BY
                    payments.paymentConfigMasterID, payments.wareHouseAutoID
                    ORDER BY payments.paymentConfigMasterID;";
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_report_customerTypeCount_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                    customertype.customerDescription,
                    count(salesMaster.netTotal) AS countTotal,
                    sum(subTotal) as subTotal
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                LEFT JOIN srp_erp_customertypemaster customertype ON customertype.customerTypeID = salesMaster.customerTypeID
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " .
            $qString . $outletFilter .
            "GROUP BY
                    customertype.customerDescription ORDER BY customertype.customerTypeID ";


        // echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_customerTypeCount_2_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT						
                customertype.customerDescription,
                sum(payments.amount) AS subTotal,					
                count( salesMaster.menuSalesID ) AS countTotal
            FROM						
                srp_erp_pos_menusalesmaster AS salesMaster 
                LEFT JOIN  (
                    SELECT SUM( IFNULL(amount,0) ) as amount, menuSalesID, paymentConfigMasterID, wareHouseAutoID 
                    FROM srp_erp_pos_menusalespayments 
                    GROUP BY menuSalesID, wareHouseAutoID
                ) as payments ON payments.menuSalesID = salesMaster.menuSalesID AND payments.wareHouseAutoID = salesMaster.wareHouseAutoID
                LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = payments.paymentConfigMasterID 
                LEFT JOIN srp_erp_customertypemaster customertype ON customertype.customerTypeID = salesMaster.customerTypeID  
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " .
            $qString . $outletFilter .
            "GROUP BY
                    customertype.customerDescription 
                    ORDER BY customertype.customerDescription, salesMaster.wareHouseAutoID ";


        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_report_salesReport_totalSales_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                'Total Sales' AS Description,
                SUM(paidAmount) AS amount

            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' "
            . $qString . $outletFilter;


        // echo $q;

        $result = $this->db->query($q)->row_array();
        return $result;
    }


    function get_report_salesReport_totalTaxes_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                taxMaster.taxDescription AS Description,
                SUM(tax.taxAmount) AS amount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            LEFT JOIN srp_erp_pos_menusalestaxes tax ON tax.menuSalesID = salesMaster.menuSalesID
            INNER JOIN srp_erp_taxmaster taxMaster ON taxMaster.taxMasterAutoID = tax.taxmasterID
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
            " . $qString . $outletFilter . "
            GROUP BY tax.taxmasterID";
        //echo $q;


        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_ServiceCharge_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }


        $q = "SELECT
                'Service Charge' AS Description,
                SUM(sc.serviceChargeAmount) AS amount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            LEFT JOIN srp_erp_pos_menusalesservicecharge sc ON sc.menuSalesID = salesMaster.menuSalesID
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " . $qString . $outletFilter;
        //echo $q;


        $result = $this->db->query($q)->row_array();
        return $result;
    }

    function get_report_giftCardTopUp_admin($date, $data2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND giftCard.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND giftCard.outletID IN(" . $outlets . ")";
        }

        $q = "SELECT
                    configMaster.description as paymentDescription,
                    SUM(giftCard.topUpAmount) AS topUpTotal,
                     count(giftCard.cardTopUpID) as countTopUp
                FROM
                    srp_erp_pos_cardtopup AS giftCard

                LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = giftCard.glConfigMasterID
                WHERE
                  giftCard.companyID = '" . current_companyID() . "'
                AND giftCard.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . " AND giftCard.topUpAmount>0
                GROUP BY
                    configMaster.autoID";
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_report_creditSales($date, $data2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND srp_erp_pos_menusalesmaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND srp_erp_pos_menusalesmaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                Sum( srp_erp_pos_menusalespayments.amount ) AS salesAmount,
                count(srp_erp_pos_menusalespayments.menuSalesPaymentID) as countCreditSales,
                srp_erp_pos_customermaster.CustomerName,
                srp_erp_pos_customermaster.CustomerAutoID 
            FROM
                srp_erp_pos_menusalespayments
                INNER JOIN srp_erp_pos_paymentglconfigmaster ON srp_erp_pos_paymentglconfigmaster.autoID = srp_erp_pos_menusalespayments.paymentConfigMasterID
                INNER JOIN srp_erp_pos_menusalesmaster ON srp_erp_pos_menusalespayments.menuSalesID = srp_erp_pos_menusalesmaster.menuSalesID AND srp_erp_pos_menusalespayments.wareHouseAutoID = srp_erp_pos_menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_pos_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_pos_customermaster.CustomerAutoID 
                WHERE
                  srp_erp_pos_menusalesmaster.companyID = '" . current_companyID() . "'
                AND srp_erp_pos_menusalesmaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . "  
                AND srp_erp_pos_paymentglconfigmaster.autoID = 7 
                AND  srp_erp_pos_menusalesmaster.isVoid = 0 
                AND srp_erp_pos_menusalesmaster.isHold = 0
                GROUP BY
                    srp_erp_pos_customermaster.CustomerAutoID, srp_erp_pos_menusalespayments.wareHouseAutoID
                ORDER BY srp_erp_pos_menusalespayments.paymentConfigMasterID;";
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    function get_report_voidBills_admin($date, $data2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                   'Voided Bills'  AS paymentDescription,
                    SUM(salesMaster.subTotal) AS NetTotal,
                     count(	salesMaster.menuSalesID) as countTransaction
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster

                WHERE
                   salesMaster.isVoid = 1 AND
                salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter;
        $result = $this->db->query($q)->row_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_outlet_cashier()
    {
        if ($this->input->post("warehouseAutoID")) {
            $warehouse = join(',', $this->input->post("warehouseAutoID"));
            $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " and warehouseAutoID IN($warehouse) GROUP BY salesMaster.createdUserID ";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "cashier[]" id = "cashier2" class="form-control input-sm" multiple = "multiple"  required >';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['createdUserID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        } else {
            $html = '<select name = "cashier[]" id = "cashier2" class="form-control input-sm" multiple = "multiple"  required > ';
            $html .= '</select > ';
            return $html;
        }

    }


    function get_outlet_cashier_itemized()
    {
        if ($this->input->post("warehouseAutoID")) {
            $warehouse = join(',', $this->input->post("warehouseAutoID"));
            $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " and warehouseAutoID IN($warehouse) GROUP BY salesMaster.createdUserID ";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "cashier[]" id = "cashieritemized" class="form-control input-sm" multiple = "multiple"  required > ';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['createdUserID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        } else {
            $html = '<select name = "cashier[]" id = "cashieritemized" class="form-control input-sm" multiple = "multiple"  required > ';
            $html .= '</select > ';
            return $html;
        }

    }

    function get_outlet_cashier_Promotions()
    {
        if ($this->input->post("warehouseAutoID")) {
            $warehouse = join(',', $this->input->post("warehouseAutoID"));
            $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " and warehouseAutoID IN($warehouse) GROUP BY salesMaster.createdUserID ";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "cashier[]" id = "cashierpromotions" class="form-control input-sm" multiple = "multiple"  required > ';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['createdUserID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        } else {
            $html = '<select name = "cashier[]" id = "cashierpromotions" class="form-control input-sm" multiple = "multiple"  required > ';
            $html .= '</select > ';
            return $html;
        }

    }


    function get_outlet_cashier_productmix()
    {
        if ($this->input->post("warehouseAutoID")) {
            $warehouse = join(',', $this->input->post("warehouseAutoID"));
            $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " and warehouseAutoID IN($warehouse) GROUP BY salesMaster.createdUserID ";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "cashier[]" id = "cashierproductmix" class="form-control input-sm" multiple = "multiple"  required > ';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['createdUserID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        } else {
            $html = '<select name = "cashier[]" id = "cashierproductmix" class="form-control input-sm" multiple = "multiple"  required > ';
            $html .= '</select > ';
            return $html;
        }

    }

    function get_outlet_cashier_franchise()
    {
        if ($this->input->post("warehouseAutoID")) {
            $warehouse = join(',', $this->input->post("warehouseAutoID"));
            $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " and warehouseAutoID IN($warehouse) GROUP BY salesMaster.createdUserID ";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "cashier[]" id = "cashierfranchise" class="form-control input-sm" multiple = "multiple"  required > ';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['createdUserID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        } else {
            $html = '<select name = "cashier[]" id = "cashierfranchise" class="form-control input-sm" multiple = "multiple"  required > ';
            $html .= '</select > ';
            return $html;
        }

    }

    function get_srp_erp_pos_paymentglconfigmaster()
    {
        $this->db->select("autoID,description,sortOrder");
        $this->db->from("srp_erp_pos_paymentglconfigmaster");
        $this->db->order_by("sortOrder ASC");
        $result = $this->db->get()->result_array();
        return $result;
    }

    function get_report_salesDetailReport($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $querySalesDetail = "SELECT
                            salesMaster.menuSalesID AS salesMasterMenuSalesID,
                            DATE_FORMAT( salesMaster.createdDateTime, '%d-%m-%Y' ) AS salesMasterCreatedDate,
                            DATE_FORMAT( salesMaster.createdDateTime, '%h-%i %p' ) AS salesMasterCreatedTime,
                            wmaster.wareHouseDescription AS whouseName,
                            employee.EmpShortCode AS menuCreatedUser,
                            salesMaster.grossTotal,
                            salesMaster.grossAmount,
                            salesMaster.companyLocalCurrencyDecimalPlaces AS companyLocalDecimal,
                            invoiceCode,
                            salesMaster.discountPer,
                            salesMaster.discountAmount,
                            salesMaster.promotionDiscount,
                            salesMaster.deliveryCommission,
                            salesMaster.deliveryCommissionAmount,
                            salesMaster.subTotal AS billNetTotal,
                            salesMaster.promotionDiscount,
                            salesMaster.wareHouseAutoID as wareHouseAutoID,
                            payment.*,
                            promotionTypeP.customerName AS PromotionalDiscountType,
                            promotionTypeD.customerName AS DeliveryCommissionType 
                        FROM
                            srp_erp_pos_menusalesmaster AS salesMaster
                            LEFT JOIN srp_erp_warehousemaster wmaster ON salesMaster.wareHouseAutoID = wmaster.wareHouseAutoID
                            LEFT JOIN srp_employeesdetails employee ON employee.EIdNo = salesMaster.createdUserID
                            LEFT JOIN srp_erp_pos_customers promotionTypeP ON promotionTypeP.customerID = salesMaster.promotionID
                            LEFT JOIN srp_erp_pos_customers promotionTypeD ON promotionTypeD.customerID = salesMaster.deliveryPersonID
                            LEFT JOIN (
                                    SELECT
                                        paymentConfigMasterID,
                                        amount,
                                        menuSalesID,
                                        srp_erp_pos_menusalespayments.customerAutoID,
                                        srp_erp_customermaster.customerName,
                                        sum( CASE WHEN paymentConfigMasterID = '26' THEN amount ELSE 0 END ) RCGC,
                                        sum( CASE WHEN paymentConfigMasterID = '1' THEN amount ELSE 0 END ) Cash,
                                        sum( CASE WHEN paymentConfigMasterID = '2' THEN amount ELSE 0 END ) CreditNote,
                                        sum( CASE WHEN paymentConfigMasterID = '3' THEN amount ELSE 0 END ) MasterCard,
                                        sum( CASE WHEN paymentConfigMasterID = '4' THEN amount ELSE 0 END ) VisaCard,
                                        sum( CASE WHEN paymentConfigMasterID = '5' THEN amount ELSE 0 END ) GiftCard,
                                        sum( CASE WHEN paymentConfigMasterID = '6' THEN amount ELSE 0 END ) AMEX,
                                        sum( CASE WHEN paymentConfigMasterID = '7' THEN amount ELSE 0 END ) CreditSales,
                                        sum( CASE WHEN paymentConfigMasterID = '27' THEN amount ELSE 0 END ) FriMi,
                                        sum( CASE WHEN paymentConfigMasterID = '25' THEN amount ELSE 0 END ) JavaApp,
                                        sum( CASE WHEN paymentConfigMasterID = '28' THEN amount ELSE 0 END ) AliPay,
                                        wareHouseAutoID	 
                                    FROM
                                        srp_erp_pos_menusalespayments
                                       
                                        LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID 
                                    GROUP BY
                                        menuSalesID, wareHouseAutoID  
                            ) payment ON salesMaster.menuSalesID = payment.menuSalesID  AND payment.wareHouseAutoID = salesMaster.wareHouseAutoID
                        WHERE
                            salesMaster.isVoid = 0 
                            AND salesMaster.isHold = 0 
                            AND salesMaster.companyID = " . current_companyID() . " 
                            AND salesMaster.createdDateTime BETWEEN '" . $date . "' 
                            AND '" . $date2 . "' " . $qString . $outletFilter . " 
                        GROUP BY
                            salesMaster.menuSalesID, salesMaster.wareHouseAutoID ";

        return $this->db->query($querySalesDetail)->result_array();
    }

    function get_srp_erp_pos_menusalesitems_invoiceID_salesDetailReport($invoiceID, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }
        return $this->get_srp_erp_pos_menusalesitems_drillDown($invoiceID, $outletID);
    }

    function get_srp_erp_pos_menusalesmaster_salesDetailReport($id, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }
        $this->db->select("menuSales.*, cType.customerDescription, wmaster.wareHouseDescription,wmaster.warehouseAddress,wmaster.warehouseTel");
        $this->db->from("srp_erp_pos_menusalesmaster menuSales");
        $this->db->join('srp_erp_customertypemaster cType', 'cType.customerTypeID = menuSales.customerTypeID', 'left'); /*customerTypeID*/
        $this->db->join('srp_erp_warehousemaster wmaster', 'wmaster.wareHouseAutoID = menuSales.wareHouseAutoID', 'left'); /*customerTypeID*/
        $this->db->where('menuSales.menuSalesID', $id);
        $this->db->where('menuSales.wareHouseAutoID', $outletID);
        //$this->db->where('menuSales.wareHouseAutoID', current_warehouseID());
        $result = $this->db->get()->row_array();
        return $result;
    }

    function get_tableList($status = array())
    {

        $this->db->select('diningTableAutoID, diningTableDescription, noOfSeats, diningRoomMasterID');
        $this->db->from('srp_erp_pos_diningtables');
        if (!empty($status)) {
            foreach ($status as $val) {
                $this->db->or_where('status', $val);
            }
        }
        $this->db->where('companyID', current_companyID());
        $this->db->where('segmentID', get_outletID());
        $result = $this->db->get()->result_array();
        return $result;
    }

    function validate_tableOrder()
    {
        $menuSalesID = $this->input->post('menuSalesID');
        $tableID = $this->input->post('id');
        $this->db->select("*");
        $this->db->from("srp_erp_pos_diningtables");
        $this->db->where('diningTableAutoID', $tableID);
        $this->db->where('status', 1);
        $diningTableAutoID = $this->db->get()->row('diningTableAutoID');
        if ($diningTableAutoID) {
            return false;
        } else {
            return true;
        }

        /*        } else {
                    return false;
                }*/
    }

    function update_menuSalesMasterTableID()
    {
        $menuSalesID = $this->input->post('menuSalesID');
        $tableID = $this->input->post('id');
        $this->db->where('menuSalesID', $menuSalesID);
        $result = $this->db->update('srp_erp_pos_menusalesmaster', array('tableID' => $tableID));
        return $result;

    }

    function update_diningTableStatus()
    {
        /*
        $this->db->select("tableID");
        $this->db->from("srp_erp_pos_menusalesmaster");
        $this->db->where('isHold', 1);
        $this->db->where('isVoid', 0);
        $this->db->where('menuSalesID', $menuSalesID);
        $tableID = $this->db->get()->row('tableID');


        $result = false;
        if (!$tableID) {*/
        $tableID = $this->input->post('id');
        $this->db->where('diningTableAutoID', $tableID);
        $data['status'] = 1;
        $data['tmp_menuSalesID'] = $this->input->post('menuSalesID');
        $result = $this->db->update('srp_erp_pos_diningtables', $data);

        /* }*/

        return $result;
    }

    function get_diningTableUsed()
    {
        $this->db->select('msm.menuSalesID, dt.diningTableAutoID, concat(msm.invoiceCode,"<br/>",crew.crewLastName) as invoiceCode, dt.diningTableDescription as tableName, dt.status, dt.tmp_crewID');
        $this->db->from('srp_erp_pos_diningtables dt');
        $this->db->join('srp_erp_pos_menusalesmaster msm', 'msm.menuSalesID=dt.tmp_menuSalesID', 'left');
        $this->db->join('srp_erp_pos_crewmembers crew', 'crew.crewMemberID = dt.tmp_crewID', 'left');
        $this->db->where('dt.status', 1);
        $this->db->where('dt.companyID', current_companyID());
        $this->db->where('dt.segmentID', get_outletID());
        $result = $this->db->get()->result_array();
        return $result;

    }

    function update_diningTableReset($tableID)
    {
        $this->db->where('diningTableAutoID', $tableID);
        $result = $this->db->update('srp_erp_pos_diningtables', array('status' => 0, 'tmp_menuSalesID' => null, 'tmp_crewID' => null, 'tmp_numberOfPacks' => 0));
        return $result;
    }

    function get_srp_erp_pos_paymentglconfigmaster2()
    {
        $this->db->select("autoID,description,sortOrder");
        $this->db->from("srp_erp_pos_paymentglconfigmaster");
        $this->db->order_by("sortOrder ASC");
        $result = $this->db->get()->result_array();
        return $result;
    }

    function get_report_salesDetailReport2($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        //$querySalesDetail = "SELECT salesMaster.menuSalesID as salesMasterMenuSalesID,salesMaster.isDelivery,salesMaster.isHold,deliveryorders.isDispatched as isDispatched,DATE_FORMAT(salesMaster.createdDateTime, '%d-%m-%Y') AS salesMasterCreatedDate,DATE_FORMAT(salesMaster.createdDateTime, '%h-%i %p') AS salesMasterCreatedTime,wmaster.wareHouseDescription as whouseName,wmaster.wareHouseCode as wareHouseCode,employee.EmpShortCode as menuCreatedUser,salesMaster.grossTotal,salesMaster.grossAmount,salesMaster.companyLocalCurrencyDecimalPlaces as companyLocalDecimal,invoiceCode,salesMaster.discountPer,salesMaster.discountAmount,salesMaster.promotionDiscount,salesMaster.deliveryCommission,salesMaster.deliveryCommissionAmount,salesMaster.subTotal as billNetTotal,salesMaster.promotionDiscount,payment.*,promotionTypeP.customerName as PromotionalDiscountType,promotionTypeD.customerName as DeliveryCommissionType,salesMaster.isDelivery, salesMaster.isHold,COUNT(deliveryorders.menuSalesMasterID) AS isDelivery1,pos_cmaster.CustomerName AS DeliveryCustomerName,deliveryorders.posCustomerAutoID AS DeliveryCustomerID,CASE deliveryorders.isDispatched WHEN 0 THEN 'No' WHEN deliveryorders.isDispatched IS NULL THEN 'Yes' WHEN deliveryorders.isDispatched = '' THEN 'Yes' WHEN 1 THEN 'Yes' END AS deliveryordersDispatched FROM srp_erp_pos_menusalesmaster AS salesMaster LEFT JOIN srp_erp_warehousemaster wmaster ON salesMaster.wareHouseAutoID = wmaster.wareHouseAutoID LEFT JOIN srp_employeesdetails employee ON employee.EIdNo = salesMaster.createdUserID LEFT JOIN srp_erp_pos_deliveryorders deliveryorders ON deliveryorders.menuSalesMasterID = salesMaster.menuSalesID LEFT JOIN srp_erp_pos_customers promotionTypeP ON promotionTypeP.customerID = salesMaster.promotionID LEFT JOIN srp_erp_pos_customers promotionTypeD ON promotionTypeD.customerID = salesMaster.deliveryPersonID LEFT JOIN srp_erp_pos_customermaster pos_cmaster ON pos_cmaster.posCustomerAutoID = deliveryorders.posCustomerAutoID LEFT JOIN (Select paymentConfigMasterID,amount,menuSalesID,srp_erp_pos_menusalespayments.customerAutoID,srp_erp_customermaster.customerName, sum(CASE WHEN paymentConfigMasterID = '1' THEN amount ELSE 0 END) Cash,sum(CASE WHEN paymentConfigMasterID = '2' THEN amount ELSE 0 END) CreditNote,sum(CASE WHEN paymentConfigMasterID = '3' THEN amount ELSE 0 END) MasterCard,sum(CASE WHEN paymentConfigMasterID = '4' THEN amount ELSE 0 END) VisaCard,sum(CASE WHEN paymentConfigMasterID = '5' THEN amount ELSE 0 END) GiftCard,sum(CASE WHEN paymentConfigMasterID = '6' THEN amount ELSE 0 END) AMEX,sum(CASE WHEN paymentConfigMasterID = '7' THEN amount ELSE 0 END) CreditSales,sum(CASE WHEN paymentConfigMasterID = '25' THEN amount ELSE 0 END) JavaApp FROM srp_erp_pos_menusalespayments LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID GROUP BY menuSalesID) payment ON salesMaster.menuSalesID = payment.menuSalesID WHERE salesMaster.isVoid = 0   AND salesMaster.companyID = " . current_companyID() . " AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'" . $qString . $outletFilter . " GROUP BY salesMaster.menuSalesID HAVING (isHold = 0 OR isDelivery1=1) ";
        $querySalesDetail = "SELECT deliveryorders.deliveryOrderID,salesMaster.menuSalesID as salesMasterMenuSalesID,salesMaster.isDelivery,salesMaster.isHold,deliveryorders.isDispatched as isDispatched,DATE_FORMAT(salesMaster.createdDateTime, '%d-%m-%Y') AS salesMasterCreatedDate,DATE_FORMAT(salesMaster.createdDateTime, '%h-%i %p') AS salesMasterCreatedTime,deliveryorders.deliveryDate as ddate,deliveryorders.deliveryTime as dtime, salesMaster.createdDateTime k, if (deliveryorders.deliveryOrderID is not null , DATE_FORMAT(concat( DATE_FORMAT(deliveryorders.deliveryDate, '%Y-%m-%d'), ' ', DATE_FORMAT(deliveryorders.deliveryTime, '%H-%i-%s')), '%Y-%m-%d %H-%i-%s') , DATE_FORMAT(salesMaster.createdDateTime,'%Y-%m-%d %H-%i-%S')) as rptDate,wmaster.wareHouseDescription as whouseName,wmaster.wareHouseCode as wareHouseCode,employee.EmpShortCode as menuCreatedUser,salesMaster.grossTotal,salesMaster.grossAmount,salesMaster.companyLocalCurrencyDecimalPlaces as companyLocalDecimal,invoiceCode,salesMaster.discountPer,salesMaster.discountAmount,salesMaster.promotionDiscount,salesMaster.deliveryCommission,salesMaster.deliveryCommissionAmount,salesMaster.subTotal as billNetTotal,salesMaster.promotionDiscount,payment.*,promotionTypeP.customerName as PromotionalDiscountType,promotionTypeD.customerName as DeliveryCommissionType,salesMaster.isDelivery, salesMaster.isHold,COUNT(deliveryorders.menuSalesMasterID) AS isDelivery1,pos_cmaster.CustomerName AS DeliveryCustomerName,deliveryorders.posCustomerAutoID AS DeliveryCustomerID,CASE deliveryorders.isDispatched WHEN 0 THEN 'No' WHEN deliveryorders.isDispatched IS NULL THEN 'Yes' WHEN deliveryorders.isDispatched = '' THEN 'Yes' WHEN 1 THEN 'Yes' END AS deliveryordersDispatched FROM srp_erp_pos_menusalesmaster AS salesMaster LEFT JOIN srp_erp_warehousemaster wmaster ON salesMaster.wareHouseAutoID = wmaster.wareHouseAutoID LEFT JOIN srp_employeesdetails employee ON employee.EIdNo = salesMaster.createdUserID LEFT JOIN srp_erp_pos_deliveryorders deliveryorders ON deliveryorders.menuSalesMasterID = salesMaster.menuSalesID LEFT JOIN srp_erp_pos_customers promotionTypeP ON promotionTypeP.customerID = salesMaster.promotionID LEFT JOIN srp_erp_pos_customers promotionTypeD ON promotionTypeD.customerID = salesMaster.deliveryPersonID LEFT JOIN srp_erp_pos_customermaster pos_cmaster ON pos_cmaster.posCustomerAutoID = deliveryorders.posCustomerAutoID LEFT JOIN (Select paymentConfigMasterID,amount,menuSalesID,srp_erp_pos_menusalespayments.customerAutoID,srp_erp_customermaster.customerName, sum(CASE WHEN paymentConfigMasterID = '1' THEN amount ELSE 0 END) Cash,sum(CASE WHEN paymentConfigMasterID = '2' THEN amount ELSE 0 END) CreditNote,sum(CASE WHEN paymentConfigMasterID = '3' THEN amount ELSE 0 END) MasterCard,sum(CASE WHEN paymentConfigMasterID = '4' THEN amount ELSE 0 END) VisaCard,sum(CASE WHEN paymentConfigMasterID = '5' THEN amount ELSE 0 END) GiftCard,sum(CASE WHEN paymentConfigMasterID = '6' THEN amount ELSE 0 END) AMEX,sum(CASE WHEN paymentConfigMasterID = '7' THEN amount ELSE 0 END) CreditSales,sum(CASE WHEN paymentConfigMasterID = '25' THEN amount ELSE 0 END) JavaApp, sum( CASE WHEN paymentConfigMasterID = '27' THEN amount ELSE 0 END ) FriMi FROM srp_erp_pos_menusalespayments LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID GROUP BY menuSalesID) payment ON salesMaster.menuSalesID = payment.menuSalesID WHERE salesMaster.isVoid = 0   AND salesMaster.companyID = " . current_companyID() . " " . $qString . $outletFilter . " GROUP BY salesMaster.menuSalesID HAVING (isHold = 0 OR deliveryOrderID is not null) AND (rptDate BETWEEN '$date' AND '$date2') ";

        //echo $querySalesDetail;

        return $this->db->query($querySalesDetail)->result_array();
    }

    function get_srp_erp_pos_menusalesitems_drillDown($invoiceID, $outletID = 0)
    {
        $path = base_url();
        $this->db->select("sales.menuSalesID, sales.menuSalesItemID, category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, sales.menuSalesPrice as sellingPrice, sales.qty , sales.discountPer, sales.discountAmount, menuMaster.menuMasterID,sales.remarkes, sales.menuSalesPrice as pricewithoutTax, sales.totalMenuTaxAmount as totalTaxAmount, sales.totalMenuServiceCharge as totalServiceCharge,menu.isTaxEnabled , size.code as sizeCode, size.description as sizeDescription");
        $this->db->from("srp_erp_pos_menusalesitems sales");
        $this->db->join("srp_erp_pos_warehousemenumaster menu", "menu.warehouseMenuID = sales.warehouseMenuID");
        $this->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $this->db->join("srp_erp_pos_menusize size", "size.menuSizeID = menuMaster.menuSizeID", "left");
        /*$this->db->where('menu.isActive', 1);
        $this->db->where('menu.isDeleted', 0);
        $this->db->where('menuMaster.isDeleted', 0);*/
        $this->db->where('sales.menuSalesID', $invoiceID);
        $this->db->where('sales.id_store', $outletID);
        //$this->db->where('sales.id_store', current_warehouseID());
        $result = $this->db->get()->result_array();

        return $result;
    }

    function update_isSampleBillPrintFlag($invoiceID, $outletID)
    {
        if (!empty($invoiceID)) {
            $this->db->where('menuSalesID', $invoiceID);
            $this->db->where('id_store', $outletID);
            return $this->db->update('srp_erp_pos_menusalesitems', array('isSamplePrinted' => 1));
        } else {
            return false;
        }

    }

    function load_hold_refno()
    {
        $menuSalesID = $this->input->post('menuSalesID');
        $this->db->select("holdRemarks");
        $this->db->from("srp_erp_pos_menusalesmaster");
        $this->db->where('menuSalesID', $menuSalesID);
        $result = $this->db->get()->row_array();

        return $result;
    }

    function submitBOT()
    {
        $invoiceID = $this->input->post('id');
        $data['BOT'] = 1;
        $data['BOTCreatedUser'] = current_userID();
        $data['BOTCreatedDatetime'] = format_date_mysql_datetime();
        $this->db->where('menuSalesID', $invoiceID);
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);
        if ($result) {
            return array('error' => 0, 'e_type' => 's', 'message' => 'Successfully submitted to BOT.');
        } else {
            return array('error' => 1, 'e_type' => 'e', 'message' => 'error while submitting to BOT.');
        }
    }

    function update_pos_submitted_payments()
    {
        $invoiceID = isPos_invoiceSessionExist();
        $outletID = get_outletID();
//var_dump($invoiceID);exit;
//var_dump($this->input->post());exit;
        if ($invoiceID) {
            $totalPaid = 0;
            $isConfirmedDeliveryOrder = pos_isConfirmedDeliveryOrder($invoiceID);
            $createdUserGroup = user_group();
            $createdPCID = current_pc();
            $createdUserID = current_userID();
            $createdUserName = current_user();
            $createdDateTime = format_date_mysql_datetime();
            $timestamp = format_date_mysql_datetime();
            $companyID = current_companyID();
            $companyCode = current_company_code();
            $masterData = get_pos_invoice_id($invoiceID);
            $reference = $this->input->post('referenceUpdate');
            $customerAutoIDs = $this->input->post('customerAutoIDUpdate');
            $paymentTypes = $this->input->post('paymentTypesUpdate');
            $cardTotalAmount = $this->input->post('cardTotalAmountUpdate');
            $netTotalAmount = $this->input->post('netTotalAmountUpdate');
            $isDelivery = $this->input->post('isDeliveryUpdate');
            $isOnTimePayment = $this->input->post('isOnTimePaymentUpdate');
            $payableDeliveryAmount = $this->input->post('totalPayableAmountDelivery_idUpdate');
            $returnChange = $this->input->post('returned_changeUpdate');
            $grossTotal = $this->input->post('total_payable_amtUpdate');
            if (!empty($paymentTypes)) {
                $i = 0;
                // print_r($paymentTypes);exit;
                foreach ($paymentTypes as $key => $amount) {

                    if ($amount > 0) {



                        $totalPaid += $amount;
                        $this->db->select('configDetail.GLCode,configMaster.autoID, configMaster.glAccountType');
                        $this->db->from('srp_erp_pos_paymentglconfigdetail configDetail');
                        $this->db->join('srp_erp_pos_paymentglconfigmaster configMaster', 'configDetail.paymentConfigMasterID = configMaster.autoID', 'left');
                        $this->db->where('configDetail.ID', $key);
                        $r = $this->db->get()->row_array();
                        if ($r['glAccountType'] == 1) {

                            /** Cash Payment */
                            if ($isDelivery == 1 && $isOnTimePayment == 1) {
                                $cashPaidAmount = $payableDeliveryAmount - $cardTotalAmount;
                            } else {
                                $cashPaidAmount = $netTotalAmount - $cardTotalAmount;
                                if ($isConfirmedDeliveryOrder) {
                                    $advancePayment = get_paidAmount($invoiceID);
                                    //$payable = $netTotalAmount - ($advancePayment + $cardTotalAmount); bug because of this.
                                    $payable = $grossTotal - ($advancePayment + $cardTotalAmount);
                                    if ($amount == $payable) {
                                        $cashPaidAmount = $amount;
                                        $returnChange = 0;
                                    } else if ($amount > $payable) {
                                        $cashPaidAmount = $payable;
                                        $returnChange = $amount - $payable;
                                    } else {
                                        /** Advance payment */
                                        $cashPaidAmount = $amount;
                                        $returnChange = 0;
                                    }
                                }
                            }

                            $amount = $cashPaidAmount;


                        }

                        /** Credit Customer's GL Code should be picked from Customer */
                        $GLCode = null;
                        if ($r['autoID'] == 7) {
                            if (isset($customerAutoIDs[$key]) && $customerAutoIDs[$key]) {
                                $receivableAutoID = $this->db->select('receivableAutoID')
                                    ->from('srp_erp_customermaster')
                                    ->where('customerAutoID', $customerAutoIDs[$key])
                                    ->get()->row('receivableAutoID');
                                $GLCode = $receivableAutoID;
                            }
                        }

                        $paymentData[$i]['menuSalesID'] = $invoiceID;
                        $paymentData[$i]['wareHouseAutoID'] = $outletID;
                        $paymentData[$i]['paymentConfigMasterID'] = $r['autoID'];
                        $paymentData[$i]['paymentConfigDetailID'] = $key;
                        $paymentData[$i]['GLCode'] = $r['autoID'] == 7 ? $GLCode : $r['GLCode'];
                        $paymentData[$i]['glAccountType'] = $r['glAccountType'];
                        $paymentData[$i]['amount'] = $amount;
                        $paymentData[$i]['reference'] = isset($reference[$key]) ? $reference[$key] : null;
                        $paymentData[$i]['customerAutoID'] = isset($customerAutoIDs[$key]) ? $customerAutoIDs[$key] : null;
                        /*Common Data*/
                        $paymentData[$i]['createdUserGroup'] = $createdUserGroup;
                        $paymentData[$i]['createdPCID'] = $createdPCID;
                        $paymentData[$i]['createdUserID'] = $createdUserID;
                        $paymentData[$i]['createdUserName'] = $createdUserName;
                        $paymentData[$i]['createdDateTime'] = $createdDateTime;
                        $paymentData[$i]['timestamp'] = $timestamp;
                        if ($r['autoID'] == 25) {
                            $data_JA['menuSalesID'] = $invoiceID;
                            $data_JA['outletID'] = $outletID;
                            $data_JA['appPIN'] = isset($reference[$key]) ? $reference[$key] : null;;
                            $data_JA['amount'] = $amount;
                            $data_JA['companyID'] = $companyID;
                            $data_JA['companyCode'] = $companyCode;
                            $data_JA['createdUserGroup'] = $createdUserGroup;
                            $data_JA['createdPCID'] = $createdPCID;
                            $data_JA['createdUserID'] = $createdUserID;
                            $data_JA['createdDateTime'] = $createdDateTime;
                            $data_JA['createdUserName'] = $createdUserName;
                            $data_JA['timestamp'] = $createdDateTime;
                            $this->db->insert('srp_erp_pos_javaappredeemhistory', $data_JA);
                        }
                        if ($r['autoID'] == 5) {
                            $barCode = isset($reference[$key]) ? $reference[$key] : null;
                            $cardInfo = get_giftCardInfo($barCode);
                            $dta_GC['wareHouseAutoID'] = $outletID;
                            $dta_GC['cardMasterID'] = !empty($cardInfo) ? $cardInfo['cardMasterID'] : null;
                            $dta_GC['barCode'] = isset($reference[$key]) ? $reference[$key] : null;
                            $dta_GC['posCustomerAutoID'] = !empty($cardInfo) ? $cardInfo['posCustomerAutoID'] : null;
                            $dta_GC['topUpAmount'] = abs($amount) * -1;
                            $dta_GC['points'] = 0;
                            $dta_GC['glConfigMasterID'] = $r['autoID'];
                            $dta_GC['glConfigDetailID'] = $key;
                            $dta_GC['menuSalesID'] = $invoiceID;
                            $dta_GC['giftCardGLAutoID'] = $r['autoID'] == 7 ? null : $r['GLCode'];
                            $dta_GC['outletID'] = $outletID;
                            $dta_GC['reference'] = 'redeem barcode ' . $barCode;
                            $dta_GC['companyID'] = $companyID;
                            $dta_GC['companyCode'] = $companyCode;
                            $dta_GC['createdPCID'] = $createdPCID;
                            $dta_GC['createdUserID'] = $createdUserID;
                            $dta_GC['createdDateTime'] = $createdDateTime;
                            $dta_GC['createdUserName'] = $createdUserName;
                            $dta_GC['createdUserGroup'] = $createdUserGroup;
                            $dta_GC['timestamp'] = $createdDateTime;
                            $this->db->insert('srp_erp_pos_cardtopup', $dta_GC);
                        }
                        $i++;
                    }else{
                        if ($amount!=null && $amount==0) {
                            //echo $key;
                            $this->db->select('configDetail.GLCode,configMaster.autoID, configMaster.glAccountType');
                            $this->db->from('srp_erp_pos_paymentglconfigdetail configDetail');
                            $this->db->join('srp_erp_pos_paymentglconfigmaster configMaster', 'configDetail.paymentConfigMasterID = configMaster.autoID', 'left');
                            $this->db->where('configDetail.ID', $key);
                            $rh = $this->db->get()->row_array();
                            $GLCode = null;
                            if ($rh['autoID'] == 7) {
                                if (isset($customerAutoIDs[$key]) && $customerAutoIDs[$key]) {
                                    $receivableAutoID = $this->db->select('receivableAutoID')
                                        ->from('srp_erp_customermaster')
                                        ->where('customerAutoID', $customerAutoIDs[$key])
                                        ->get()->row('receivableAutoID');
                                    $GLCode = $receivableAutoID;
                                }
                            }
                            //echo $amount;exit;
                            $paymentData[$i]['menuSalesID'] = $invoiceID;
                            $paymentData[$i]['wareHouseAutoID'] = $outletID;
                            $paymentData[$i]['paymentConfigMasterID'] = $rh['autoID'];
                            $paymentData[$i]['paymentConfigDetailID'] = $key;
                            $paymentData[$i]['GLCode'] = $rh['autoID'] == 7 ? $GLCode : $rh['GLCode'];
                            $paymentData[$i]['glAccountType'] = $rh['glAccountType'];
                            $paymentData[$i]['amount'] = 0;
                            $paymentData[$i]['reference'] = isset($reference[$key]) ? $reference[$key] : null;
                            $paymentData[$i]['customerAutoID'] = isset($customerAutoIDs[$key]) ? $customerAutoIDs[$key] : null;
                            /*Common Data*/
                            $paymentData[$i]['createdUserGroup'] = $createdUserGroup;
                            $paymentData[$i]['createdPCID'] = $createdPCID;
                            $paymentData[$i]['createdUserID'] = $createdUserID;
                            $paymentData[$i]['createdUserName'] = $createdUserName;
                            $paymentData[$i]['createdDateTime'] = $createdDateTime;
                            $paymentData[$i]['timestamp'] = $timestamp;
                        }
                    } // end if
                } //end foreach

                if (isset($paymentData) && !empty($paymentData)) {
                    $this->db->delete('srp_erp_pos_menusalespayments', array('menuSalesID' => $invoiceID));
                    $this->db->insert_batch('srp_erp_pos_menusalespayments', $paymentData);
                }
                if ($totalPaid > 0) {
                    $payable = $this->input->post('total_payable_amt');
                    //$balancePayable = $totalPaid - ($payable > 0 ? $payable : 0);
                    $this->db->update('srp_erp_pos_menusalesmaster', array('cashReceivedAmount' => $totalPaid, 'balanceAmount' => $returnChange, 'is_sync' => 0), array('menuSalesID' => $invoiceID));
                }
            }
            $data['status']=true;
            $data['invoice_id']=$invoiceID;
            return $data;
        } else {
            $data['status']=false;
            $data['invoice_id']=$invoiceID;
            return $data;
        }
    }

}