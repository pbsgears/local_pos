<?php
/** ================================
 * -- File Name : Pos_model.php
 * -- Project Name : SME
 * -- Module Name : Point of sale General
 * -- Author : Nasik Ahamed
 * -- Create date : 19-09-2016
 * -- Description : model for POS general
 *
 * --REVISION HISTORY
 * Date: 25-05-2017 By: Mohamed Shafri: worked on the bank ledger entry .
 *
 *
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');


class Pos_model extends ERP_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function item_search($barcode = false)
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $wareHouseID = $this->common_data['ware_houseID'];

        if ($barcode) {
            $search_string = $_GET['q'];
            $result = $this->db->query("SELECT t1.itemAutoID, t1.itemSystemCode as itemSystemCode, t1.itemDescription, t1.currentStock,
                                 t2.companyLocalSellingPrice, defaultUnitOfMeasure, itemImage, barcode
                                 FROM srp_erp_warehouseitems t1
                                 JOIN srp_erp_itemmaster t2 ON t1.itemAutoID = t2.itemAutoID
                                 WHERE (t2.barcode =  '" . $search_string . "' )
                                 AND t2.companyID={$companyID} AND wareHouseAutoID ={$wareHouseID} AND isActive=1")->row_array();


        } else {
            $search_string = "%" . $_GET['q'] . "%";
            $result = $this->db->query("SELECT t1.itemAutoID, t1.itemSystemCode, t1.itemDescription, t1.currentStock,
                                 t2.companyLocalSellingPrice, defaultUnitOfMeasure, barcode, itemImage
                                 FROM srp_erp_warehouseitems t1
                                 JOIN srp_erp_itemmaster t2 ON t1.itemAutoID = t2.itemAutoID
                                 WHERE (t1.itemSystemCode LIKE '" . $search_string . "' OR t1.itemDescription LIKE '" . $search_string . "' OR t2.barcode LIKE '" . $search_string . "')
                                 AND t2.companyID={$companyID} AND wareHouseAutoID ={$wareHouseID} AND isActive=1 limit 10")->result_array();
        }


        return $result;

    }


    function shift_create()
    {
        $employeeID = current_userID();
        $wareHouseID = current_warehouseID();
        $counterID = $this->input->post('counterID');
        $startingBalance = $this->input->post('startingBalance');
        $startingBalance = str_replace(',', '', $startingBalance);

        $isAvailableSession = $this->db->select('counterID')->from('srp_erp_pos_shiftdetails')
            ->where('wareHouseID', $wareHouseID)->where('empID', $employeeID)
            ->where('isClosed', 0)->get()->row('counterID');

        if (empty($isAvailableSession)) {

            $this->db->select('srp_erp_pos_shiftdetails.*, srp_employeesdetails.Ename2')->from('srp_erp_pos_shiftdetails')
                ->where('wareHouseID', $wareHouseID)->where('counterID', $counterID);
            $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_pos_shiftdetails.empID', 'LEFT');
            $this->db->where('isClosed', 0);

            $isExist = $this->db->get()->row_array();
            if (empty($isExist)) {
                $com_currency = $this->common_data['company_data']['company_default_currency'];
                $tr_currency = $this->common_data['company_data']['company_default_currency']; /*Transaction currency is Company currency */
                $rep_currency = $this->common_data['company_data']['company_reporting_currency'];

                $localConversion = currency_conversion($tr_currency, $com_currency, $startingBalance);
                $com_currDPlace = $localConversion['DecimalPlaces'];
                $localConversionRate = $localConversion['conversion'];

                $transConversion = currency_conversion($tr_currency, $tr_currency, $startingBalance);
                $tr_currDPlace = $transConversion['DecimalPlaces'];
                $transConversionRate = $transConversion['conversion'];

                $reportConversion = currency_conversion($tr_currency, $rep_currency, $startingBalance);
                $rep_currDPlace = $reportConversion['DecimalPlaces'];
                $reportConversionRate = $reportConversion['conversion'];

                $data = array(
                    'wareHouseID' => $wareHouseID,
                    'empID' => $employeeID,
                    'counterID' => $counterID,
                    'startTime' => current_date(),

                    'startingBalance_transaction' => $startingBalance,
                    'startingBalance_local' => round(($startingBalance / $localConversionRate), $com_currDPlace),
                    'startingBalance_reporting' => round(($startingBalance / $reportConversionRate), $rep_currDPlace),


                    'transactionCurrency' => $tr_currency,
                    'transactionCurrencyDecimalPlaces' => $tr_currDPlace,
                    'transactionExchangeRate' => $transConversionRate,

                    'companyLocalCurrency' => $com_currency,
                    'companyLocalCurrencyDecimalPlaces' => $com_currDPlace,
                    'companyLocalExchangeRate' => $localConversionRate,

                    'companyReportingCurrency' => $rep_currency,
                    'companyReportingCurrencyDecimalPlaces' => $rep_currDPlace,
                    'companyReportingExchangeRate' => $reportConversionRate,

                    'companyID' => $this->common_data['company_data']['company_id'],
                    'companyCode' => $this->common_data['company_data']['company_code'],
                    'createdPCID' => $this->common_data['current_pc'],
                    'createdUserID' => $this->common_data['current_userID'],
                    'createdUserName' => $this->common_data['current_user'],
                    'createdUserGroup' => $this->common_data['user_group'],
                    'createdDateTime' => current_date(),
                    'id_store' => current_warehouseID()
                );

                $this->db->insert('srp_erp_pos_shiftdetails', $data);
                if ($this->db->affected_rows() > 0) {
                    return array('s', 'Shift Created with counter'); /*, $this->promotionDetail() : removed by Shafri */
                } else {
                    return array('e', 'Error In Shift Creation');
                }
            } else {
                $counterCode = $this->db->select('counterCode')->from('srp_erp_pos_counters')
                    ->where('counterID', $counterID)->get()->row('counterCode');
                return array('e', 'Already a shift is going on with counter [ ' . $counterCode . ' ] ' . $isExist['Ename2']);
            }
        } else {
            $counterCode = $this->db->select('counterCode')->from('srp_erp_pos_counters')
                ->where('counterID', $isAvailableSession)->get()->row('counterCode');
            return array('e', 'You have a unclosed session in counter [ ' . $counterCode . ' ]');
        }
    }

    function shift_close($shiftID = 0)
    {

        $endBalance = $this->input->post('startingBalance');
        $endBalance = str_replace(',', '', $endBalance);

        $com_currency = $this->common_data['company_data']['company_default_currency'];
        $rep_currency = $this->common_data['company_data']['company_reporting_currency'];

        $localConversion = currency_conversion($com_currency, $com_currency, $endBalance);
        $localConversionRate = $localConversion['conversion'];

        $reportConversion = currency_conversion($com_currency, $rep_currency, $endBalance);
        $reportConversionRate = $reportConversion['conversion'];
        $cashSales = $this->input->post('cashSales');
        $cardCollection = $this->input->post('cardCollection');
        $closingCashBalance = $this->input->post('closingCashBalance');
        $different_transaction = $endBalance - $closingCashBalance;

        $data = array(
            'endTime' => current_date(),
            'isClosed' => 1,

            'endingBalance_transaction' => $endBalance,
            'endingBalance_local' => round(($endBalance / $localConversionRate), $localConversion['DecimalPlaces']),
            'endingBalance_reporting' => round(($endBalance / $reportConversionRate), $reportConversion['DecimalPlaces']),

            'cashSales' => $cashSales,
            'cashSales_local' => round(($cashSales / $localConversionRate), $localConversion['DecimalPlaces']),
            'cashSales_reporting' => round(($cashSales / $reportConversionRate), $reportConversion['DecimalPlaces']),

            'giftCardTopUp' => $cardCollection,
            'giftCardTopUp_local' => round(($cardCollection / $localConversionRate), $localConversion['DecimalPlaces']),
            'giftCardTopUp_reporting' => round(($cardCollection / $reportConversionRate), $reportConversion['DecimalPlaces']),

            'closingCashBalance_transaction' => $closingCashBalance,
            'closingCashBalance_local' => round(($closingCashBalance / $localConversionRate), $localConversion['DecimalPlaces']),
            'closingCashBalance_reporting' => round(($closingCashBalance / $reportConversionRate), $reportConversion['DecimalPlaces']),

            'different_transaction' => $different_transaction,
            'different_local' => round(($different_transaction / $localConversionRate), $localConversion['DecimalPlaces']),
            'different_local_reporting' => round(($different_transaction / $reportConversionRate), $reportConversion['DecimalPlaces']),


            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => current_date(),
            'id_store' => current_warehouseID(),
            'is_sync' => 0
        );

        $this->db->where('shiftID', $shiftID)->where('wareHouseID', current_warehouseID())->update('srp_erp_pos_shiftdetails', $data);
        $result = $this->db->affected_rows();
        /*echo $this->db->last_query();
        echo 'result: '.$result;*/
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

        $result = $this->db->select('shiftID, counterID')->from('srp_erp_pos_shiftdetails')->where($where)->get()->row_array();
        return $result;

    }

    function getInvoiceCode()
    {
        $query = $this->db->select('serialNo')->from('srp_erp_pos_invoice')->where('companyID', $this->common_data['company_data']['company_id'])
            ->order_by('invoiceID', 'desc')->get();
        $lastRefArray = $query->row_array();
        $lastRefNo = $lastRefArray['serialNo'];
        $lastRefNo = ($lastRefNo == null) ? 1 : $lastRefArray['serialNo'] + 1;

        $this->load->library('sequence');
        $refCode = $this->sequence->sequence_generator('POS', $lastRefNo);

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
    }

    function invoice_create()
    {
        $totVal = $this->input->post('totVal');
        $payConData = posPaymentConfig_data();

        if (empty($payConData)) {
            return array('e', 'Payment GL configuration is not configured');
            exit;
        }

        $currentShiftData = $this->isHaveNotClosedSession();

        $financeYear = $this->db->select('companyFinanceYearID, beginingDate, endingDate')->from('srp_erp_companyfinanceyear')
            ->where(
                array(
                    'isActive' => 1,
                    'isCurrent' => 1,
                    'companyID' => current_companyID()
                )
            )->get()->row_array();


        $financePeriod = $this->db->select('companyFinancePeriodID, dateFrom, dateTo')->from('srp_erp_companyfinanceperiod')
            ->where(
                array(
                    'isActive' => 1,
                    'isCurrent' => 1,
                    'companyID' => current_companyID()
                )
            )->get()->row_array();

        if (empty($financeYear)) {
            return array('e', 'Please setup the current financial year');
            exit;
        }

        if (empty($financePeriod)) {
            return array('e', 'Please setup the current financial period');
            exit;
        }

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
            $creditSalesAmount = $this->input->post('_creditSalesAmount');
            $chequeAmount = $this->input->post('_chequeAmount');
            $cardAmount = $this->input->post('_cardAmount');
            $referenceNO = $this->input->post('_referenceNO');
            $cardNumber = $this->input->post('_cardNumber');
            $bank = $this->input->post('_bank');
            $creditNoteAmount = str_replace(',', '', $this->input->post('_creditNoteAmount'));
            $creditNote_invID = $this->input->post('creditNote-invID');
            $total_discVal = $this->input->post('discVal');
            $paidAmount = ($cashAmount + $chequeAmount + $cardAmount + $creditNoteAmount);
            $netTotVal = $this->input->post('netTotVal');
            $totVal = $this->input->post('totVal');
            $balanceAmount = ($netTotVal - $paidAmount);

            $chequeNO = $this->input->post('_chequeNO');
            $chequeCashDate = $this->input->post('_chequeCashDate');


            if ($netTotVal < $paidAmount) {
                $cashAmount = $netTotVal - ($chequeAmount + $cardAmount + $creditNoteAmount);
                $balanceAmount = 0;
            }
            $isCreditSales = 0;
            if ($creditSalesAmount > 0) {
                $isCreditSales = 1;
                $balanceAmount = 0;
            }

            /*Payment Details Calculation End*/

            //Get last reference no
            $invCodeDet = $this->getInvoiceCode();
            $lastRefNo = $invCodeDet['lastRefNo'];
            $refCode = $invCodeDet['refCode'];

            $WarehouseID = current_warehouseID();

            $querys = $this->db->select('wareHouseCode')->from('srp_erp_warehousemaster')->where('wareHouseAutoID', $WarehouseID)->get();
            $WarehouseCode = $querys->row_array();

            $invSequenceCodeDet = $this->getInvoiceSequenceCode();
            $lastINVNo = $invSequenceCodeDet['lastINVNo'];
            $sequenceCode = $invSequenceCodeDet['sequenceCode'];
            /*********************************************************************************************
             * Always transaction is going with transaction currency [ Transaction Currency => OMR ]
             * If we want to know the reporting amount [ Reporting Currency => USD ]
             * So the currency_conversion functions 1st parameter will be the USD [what we looking for ]
             * And the 2nd parameter will be the OMR [what we already got]
             *
             * Ex :
             *    Transaction currency => OMR   => $trCurrency
             *    Transaction Amount => 1000/-  => $trAmount
             *    Reporting Currency => USD     => $reCurrency
             *
             *    $conversionData  = currency_conversion($trCurrency, $reCurrency, $trAmount);
             *    $conversionRate  = $conversionData['conversion'];
             *    $decimalPlace    = $conversionData['DecimalPlaces'];
             *    $reportingAmount = round( ($trAmount / $conversionRate) , $decimalPlace );
             **********************************************************************************************/

            $localConversion = currency_conversion($tr_currency, $com_currency, $netTotVal);
            $localConversionRate = $localConversion['conversion'];
            $transConversion = currency_conversion($tr_currency, $tr_currency, $netTotVal);
            $tr_currDPlace = $transConversion['DecimalPlaces'];
            $transConversionRate = $transConversion['conversion'];
            $reportConversion = currency_conversion($tr_currency, $rep_currency, $netTotVal);
            $reportConversionRate = $reportConversion['conversion'];

            $isStockCheck = 0;

            $invArray = array(
                'documentSystemCode' => $refCode,
                'documentCode' => 'POS',
                'serialNo' => $lastRefNo,
                'invoiceSequenceNo' => $lastINVNo,
                'invoiceCode' => $sequenceCode,
                'financialYearID' => $financeYear['companyFinanceYearID'],
                'financialPeriodID' => $financePeriod['companyFinancePeriodID'],
                'FYBegin' => $financeYear['beginingDate'],
                'FYEnd' => $financeYear['endingDate'],
                'FYPeriodDateFrom' => $financePeriod['dateFrom'],
                'FYPeriodDateTo' => $financePeriod['dateTo'],
                'customerID' => $customerID,
                'customerCode' => $customerCode,
                'invoiceDate' => $invoiceDate,
                'counterID' => $currentShiftData['counterID'],
                'shiftID' => $currentShiftData['shiftID'],
                'subTotal' => $totVal,
                'netTotal' => $netTotVal,
                'paidAmount' => $paidAmount,
                'balanceAmount' => $balanceAmount,
                'cashAmount' => $cashAmount,
                'chequeAmount' => $chequeAmount,
                'cardAmount' => $cardAmount,
                'discountAmount' => $total_discVal,
                'creditNoteID' => $creditNote_invID,
                'creditNoteAmount' => $creditNoteAmount,
                'creditSalesAmount' => $creditSalesAmount,
                'isCreditSales' => $isCreditSales,
                'chequeNo' => $chequeNO,
                'chequeDate' => $chequeCashDate,
                'companyLocalCurrencyID' => $localConversion['currencyID'],
                'companyLocalCurrency' => $com_currency,
                'companyLocalCurrencyDecimalPlaces' => $com_currDPlace,
                'companyLocalExchangeRate' => $localConversionRate,
                'transactionCurrencyID' => $localConversion['trCurrencyID'],
                'transactionCurrency' => $tr_currency,
                'transactionCurrencyDecimalPlaces' => $tr_currDPlace,
                'transactionExchangeRate' => $transConversionRate,
                'companyReportingCurrencyID' => $reportConversion['currencyID'],
                'companyReportingCurrency' => $rep_currency,
                'companyReportingCurrencyDecimalPlaces' => $rep_currDPlace,
                'companyReportingExchangeRate' => $reportConversionRate,
                'wareHouseAutoID' => $wareHouseData['wareHouseAutoID'],
                'wareHouseCode' => $wareHouseData['wareHouseCode'],
                'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
                'wareHouseDescription' => $wareHouseData['wareHouseDescription'],
                'segmentID' => $wareHouseData['segmentID'],
                'segmentCode' => $wareHouseData['segmentCode'],
                'companyID' => $this->common_data['company_data']['company_id'],
                'companyCode' => $this->common_data['company_data']['company_code'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdDateTime' => current_date(),
            );

            if (isset($bank)) {
                $invArray['cardRefNo'] = $referenceNO;
                $invArray['cardBank'] = $bank;
                $invArray['cardNumber'] = $cardNumber;
            }

            if ($customerID == 0) {
                $bankData = $this->db->query("SELECT receivableAutoID, receivableSystemGLCode, receivableGLAccount,
                                          receivableDescription, receivableType
                                          FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();
                $invArray['bankGLAutoID'] = $bankData['receivableAutoID'];
                $invArray['bankSystemGLCode'] = $bankData['receivableSystemGLCode'];
                $invArray['bankGLAccount'] = $bankData['receivableGLAccount'];
                $invArray['bankGLDescription'] = $bankData['receivableDescription'];
                $invArray['bankGLType'] = $bankData['receivableType'];

                /*************** item ledger party currency ***********/
                $partyData = array(
                    'cusID' => 0,
                    'sysCode' => 'CASH',
                    'cusName' => 'CASH',
                    'partyCurID' => $localConversion['trCurrencyID'],
                    'partyCurrency' => $tr_currency,
                    'partyDPlaces' => $tr_currDPlace,
                    'partyER' => $transConversionRate,
                );

            } else {

                $cusData = $this->db->query("SELECT customerAutoID, customerSystemCode, customerName, receivableAutoID,
                                             receivableSystemGLCode, receivableGLAccount, receivableDescription, receivableType,
                                             customerCurrencyID, customerCurrency, customerCurrencyDecimalPlaces,customerAddress1,customerTelephone
                                             FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();

                $partyData = currency_conversion($tr_currency, $cusData['customerCurrency']);

                $invArray['customerCurrencyID'] = $cusData['customerCurrencyID'];
                $invArray['customerCurrency'] = $cusData['customerCurrency'];
                $invArray['customerCurrencyExchangeRate'] = $partyData['conversion'];
                $invArray['customerCurrencyDecimalPlaces'] = $cusData['customerCurrencyDecimalPlaces'];

                $invArray['customerReceivableAutoID'] = $cusData['receivableAutoID'];
                $invArray['customerReceivableSystemGLCode'] = $cusData['receivableSystemGLCode'];
                $invArray['customerReceivableGLAccount'] = $cusData['receivableGLAccount'];
                $invArray['customerReceivableDescription'] = $cusData['receivableDescription'];
                $invArray['customerReceivableType'] = $cusData['receivableType'];

                /*************** item ledger party currency ***********/

                $partyData = array(
                    'cusID' => $cusData['customerAutoID'],
                    'sysCode' => $cusData['customerSystemCode'],
                    'cusName' => $cusData['customerName'],
                    'partyCurID' => $cusData['customerCurrencyID'],
                    'partyCurrency' => $cusData['customerCurrency'],
                    'partyDPlaces' => $cusData['customerCurrencyDecimalPlaces'],
                    'partyER' => $partyData['conversion'],
                    'partyGL' => $cusData,
                );
            }

            /*Load wac library*/
            $this->load->library('Wac');
            $this->load->library('sequence');

            $this->db->trans_start();
            $this->db->insert('srp_erp_pos_invoice', $invArray);
            $invID = $this->db->insert_id();

            if ($creditSalesAmount != 0) {
                $data_customer_invoice['invoiceType'] = 'Direct';
                $data_customer_invoice['documentID'] = 'CINV';
                $data_customer_invoice['posTypeID'] = 1;
                $data_customer_invoice['referenceNo'] = $sequenceCode;
                $data_customer_invoice['invoiceNarration'] = 'POS Credit Sales - ' . $sequenceCode;
                $data_customer_invoice['posMasterAutoID'] = $invID;
                $data_customer_invoice['invoiceDate'] = current_date();
                $data_customer_invoice['invoiceDueDate'] = current_date();
                $data_customer_invoice['customerInvoiceDate'] = current_date();
                $data_customer_invoice['invoiceCode'] = $this->sequence->sequence_generator($data_customer_invoice['documentID']);
                $customerInvoiceCode = $data_customer_invoice['invoiceCode'];
                $data_customer_invoice['companyFinanceYearID'] = $this->common_data['company_data']['companyFinanceYearID'];
                $financialYear = get_financial_from_to($this->common_data['company_data']['companyFinanceYearID']);
                $data_customer_invoice['companyFinanceYear'] = trim($financialYear['beginingDate']) . ' - ' . trim($financialYear['endingDate']);
                $data_customer_invoice['FYBegin'] = trim($financialYear['beginingDate']);
                $data_customer_invoice['FYEnd'] = trim($financialYear['endingDate']);
                $data_customer_invoice['FYPeriodDateFrom'] = trim($this->common_data['company_data']['FYPeriodDateFrom']);
                $data_customer_invoice['FYPeriodDateTo'] = trim($this->common_data['company_data']['FYPeriodDateTo']);
                $data_customer_invoice['companyFinancePeriodID'] = $this->common_data['company_data']['companyFinancePeriodID'];
                $data_customer_invoice['customerID'] = $customerID;
                $data_customer_invoice['customerSystemCode'] = $cusData['customerSystemCode'];
                $data_customer_invoice['customerName'] = $cusData['customerName'];
                $data_customer_invoice['customerAddress'] = $cusData['customerAddress1'];
                $data_customer_invoice['customerTelephone'] = $cusData['customerTelephone'];
                $data_customer_invoice['customerFax'] = $cusData['customerTelephone'];
                $data_customer_invoice['customerEmail'] = $cusData['customerTelephone'];
                $data_customer_invoice['customerReceivableAutoID'] = $cusData['receivableAutoID'];
                $data_customer_invoice['customerReceivableSystemGLCode'] = $cusData['receivableSystemGLCode'];
                $data_customer_invoice['customerReceivableGLAccount'] = $cusData['receivableGLAccount'];
                $data_customer_invoice['customerReceivableDescription'] = $cusData['receivableDescription'];
                $data_customer_invoice['customerReceivableType'] = $cusData['receivableType'];
                $data_customer_invoice['customerCurrency'] = $cusData['customerCurrency'];
                $data_customer_invoice['customerCurrencyID'] = $cusData['customerCurrencyID'];
                $data_customer_invoice['customerCurrencyDecimalPlaces'] = $cusData['customerCurrencyDecimalPlaces'];

                $data_customer_invoice['confirmedYN'] = 1;
                $data_customer_invoice['confirmedByEmpID'] = current_userID();
                $data_customer_invoice['confirmedByName'] = current_user();
                $data_customer_invoice['confirmedDate'] = current_date();
                $data_customer_invoice['approvedYN'] = 1;
                $data_customer_invoice['approvedDate'] = current_date();
                $data_customer_invoice['currentLevelNo'] = 1;
                $data_customer_invoice['approvedbyEmpID'] = current_userID();
                $data_customer_invoice['approvedbyEmpName'] = current_user();

                $data_customer_invoice['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $data_customer_invoice['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
                $data_customer_invoice['transactionExchangeRate'] = 1;
                $data_customer_invoice['transactionAmount'] = $totVal;
                $data_customer_invoice['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data_customer_invoice['transactionCurrencyID']);
                $data_customer_invoice['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $data_customer_invoice['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                $default_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['companyLocalCurrencyID']);
                $data_customer_invoice['companyLocalExchangeRate'] = $default_currency['conversion'];
                $data_customer_invoice['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                $data_customer_invoice['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                $data_customer_invoice['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                $reporting_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['companyReportingCurrencyID']);
                $data_customer_invoice['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                $data_customer_invoice['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                $customer_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['customerCurrencyID']);
                $data_customer_invoice['customerCurrencyExchangeRate'] = $customer_currency['conversion'];
                $data_customer_invoice['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
                $data_customer_invoice['companyCode'] = $this->common_data['company_data']['company_code'];
                $data_customer_invoice['companyID'] = $this->common_data['company_data']['company_id'];
                $data_customer_invoice['createdUserGroup'] = $this->common_data['user_group'];
                $data_customer_invoice['createdPCID'] = $this->common_data['current_pc'];
                $data_customer_invoice['createdUserID'] = $this->common_data['current_userID'];
                $data_customer_invoice['createdUserName'] = $this->common_data['current_user'];
                $data_customer_invoice['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_customerinvoicemaster', $data_customer_invoice);
                $customerInvoiceMasterID = $this->db->insert_id();

                if ($customerInvoiceMasterID) {
                    $doc_approved['departmentID'] = "CINV";
                    $doc_approved['documentID'] = "CINV";
                    $doc_approved['documentCode'] = $data_customer_invoice['invoiceCode'];
                    $doc_approved['documentSystemCode'] = $customerInvoiceMasterID;
                    $doc_approved['documentDate'] = current_date();
                    $doc_approved['approvalLevelID'] = 1;
                    $doc_approved['docConfirmedDate'] = current_date();
                    $doc_approved['docConfirmedByEmpID'] = current_userID();
                    $doc_approved['table_name'] = 'srp_erp_customerinvoicemaster';
                    $doc_approved['table_unique_field_name'] = 'invoiceAutoID';
                    $doc_approved['approvedEmpID'] = current_userID();
                    $doc_approved['approvedYN'] = 1;
                    $doc_approved['approvedComments'] = 'Approved from POS';
                    $doc_approved['approvedPC'] = current_pc();
                    $doc_approved['approvedDate'] = current_date();
                    $doc_approved['companyID'] = current_companyID();
                    $doc_approved['companyCode'] = current_company_code();
                    $this->db->insert('srp_erp_documentapproved', $doc_approved);

                    $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,segmentID,segmentCode,transactionCurrency,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces');
                    $this->db->from('srp_erp_customerinvoicemaster');
                    $this->db->where('invoiceAutoID', $customerInvoiceMasterID);
                    $master = $this->db->get()->row_array();

                    $data_customer_invoice_detail['invoiceAutoID'] = $customerInvoiceMasterID;
                    $data_customer_invoice_detail['type'] = 'GL';
                    $data_customer_invoice_detail['description'] = 'POS Sales - ' . $sequenceCode;
                    $data_customer_invoice_detail['transactionAmount'] = round($totVal, $master['transactionCurrencyDecimalPlaces']);
                    $companyLocalAmount = $data_customer_invoice_detail['transactionAmount'] / $master['companyLocalExchangeRate'];
                    $data_customer_invoice_detail['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                    $companyReportingAmount = $data_customer_invoice_detail['transactionAmount'] / $master['companyReportingExchangeRate'];
                    $data_customer_invoice_detail['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                    $customerAmount = $data_customer_invoice_detail['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                    $data_customer_invoice_detail['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

                    $data_customer_invoice_detail['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data_customer_invoice_detail['companyID'] = $this->common_data['company_data']['company_id'];
                    $data_customer_invoice_detail['createdUserGroup'] = $this->common_data['user_group'];
                    $data_customer_invoice_detail['createdPCID'] = $this->common_data['current_pc'];
                    $data_customer_invoice_detail['createdUserID'] = $this->common_data['current_userID'];
                    $data_customer_invoice_detail['createdUserName'] = $this->common_data['current_user'];
                    $data_customer_invoice_detail['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_customerinvoicedetails', $data_customer_invoice_detail);

                }


            }

            $i = 0;
            $item_ledger_arr = array();
            $dataInt = array();
            foreach ($item as $itemID) {
                $itemData = fetch_ware_house_item_data($itemID);
                $conversion = conversionRateUOM($itemUOM[$i], $itemData['defaultUnitOfMeasure']);
                $conversion = ($conversion == 0) ? 1 : $conversion;
                $conversionRate = 1 / $conversion;
                $availableQTY = $itemData['wareHouseQty'];
                $qty = $itemQty[$i] * $conversionRate;

                if ($availableQTY < $qty && $isStockCheck == 1) {
                    $this->db->trans_rollback();
                    return array('e', '[ ' . $itemData['itemSystemCode'] . ' - ' . $itemData['itemDescription'] . ' ]<p> is available only ' . $availableQTY . ' qty');
                    break;
                }

                $price = str_replace(',', '', $itemPrice[$i]);
                $itemTotal = $itemQty[$i] * $price;
                $itemTotal = ($itemDis[$i] > 0) ? ($itemTotal - ($itemTotal * 0.01 * $itemDis[$i])) : $itemTotal;
                $itemTotal = round($itemTotal, $tr_currDPlace);

                $dataInt[$i]['invoiceID'] = $invID;
                $dataInt[$i]['itemAutoID'] = $itemID;
                $dataInt[$i]['itemSystemCode'] = $itemData['itemSystemCode'];
                $dataInt[$i]['itemDescription'] = $itemData['itemDescription'];
                $dataInt[$i]['defaultUOM'] = $itemData['defaultUnitOfMeasure'];
                $dataInt[$i]['unitOfMeasure'] = $itemUOM[$i];
                $dataInt[$i]['conversionRateUOM'] = $conversion;
                $dataInt[$i]['qty'] = $itemQty[$i];
                $dataInt[$i]['price'] = $price;
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


                $dataInt[$i]['transactionAmount'] = $itemTotal;
                $dataInt[$i]['transactionExchangeRate'] = $transConversionRate;
                $dataInt[$i]['transactionCurrency'] = $tr_currency;
                $dataInt[$i]['transactionCurrencyID'] = $localConversion['trCurrencyID'];
                $dataInt[$i]['transactionCurrencyDecimalPlaces'] = $tr_currDPlace;
                $dataInt[$i]['companyLocalAmount'] = round(($itemTotal / $localConversionRate), $com_currDPlace);

                $dataInt[$i]['companyLocalExchangeRate'] = $localConversionRate;
                $dataInt[$i]['companyLocalCurrency'] = $com_currency;
                $dataInt[$i]['companyLocalCurrencyID'] = $localConversion['currencyID'];
                $dataInt[$i]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

                $dataInt[$i]['companyReportingAmount'] = round(($itemTotal / $reportConversionRate), $rep_currDPlace);
                $dataInt[$i]['companyReportingExchangeRate'] = $reportConversionRate;
                $dataInt[$i]['companyReportingCurrency'] = $rep_currency;
                $dataInt[$i]['companyReportingCurrencyID'] = $reportConversion['currencyID'];
                $dataInt[$i]['companyReportingCurrencyDecimalPlaces'] = $rep_currDPlace;

                $dataInt[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                $dataInt[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                $dataInt[$i]['createdPCID'] = $this->common_data['current_pc'];
                $dataInt[$i]['createdUserID'] = $this->common_data['current_userID'];
                $dataInt[$i]['createdUserName'] = $this->common_data['current_user'];
                $dataInt[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $dataInt[$i]['createdDateTime'] = current_date();

                /*$balanceQty = $availableQTY - $qty;
                $itemUpdateWhere = array('itemAutoID' => $itemID, 'wareHouseAutoID' => $this->common_data['ware_houseID']);
                $itemUpdateQty = array('currentStock' => $balanceQty);
                $this->db->where($itemUpdateWhere)->update('srp_erp_warehouseitems', $itemUpdateQty);*/

                $wacData = $this->wac->wac_calculation(1, $itemID, $qty, '', $this->common_data['ware_houseID']);

                if ($creditSalesAmount > 0) {
                    $item_ledger_arr[$i] = $this->item_ledger_customerInvoice($financeYear, $financePeriod, $customerInvoiceCode, $dataInt[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData);
                } else {
                    $item_ledger_arr[$i] = $this->item_ledger($financeYear, $financePeriod, $refCode, $dataInt[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData);
                }

                $i++;
            }

            //echo '<pre>';print_r($dataInt);echo '</pre>'; exit;

            $this->db->insert_batch('srp_erp_pos_invoicedetail', $dataInt);
            $isInvoiced = $this->input->post('isInvoiced');
            if (!empty($isInvoiced)) {
                $holdinv['isInvoiced'] = 1;
                $this->db->where('invoiceID', $isInvoiced);
                $this->db->update('srp_erp_pos_invoicehold', $holdinv);
            }
            $this->db->insert_batch('srp_erp_itemledger', $item_ledger_arr);

            $this->double_entry($invID, $partyData, $creditSalesAmount);

            $this->db->trans_complete();
            if ($this->db->trans_status() == false) {
                $this->db->trans_rollback();
                return array('e', 'Error in Invoice Create');
            } else {
                $this->db->trans_commit();
                return array('s', 'Invoice Code : ' . $sequenceCode . ' ', $invID, $refCode);
            }
        } else {
            return array('e', 'You have not a valid session.<p>Please login and try again.</p>');
        }
    }

    function item_ledger($financeYear, $financePeriod, $refCode, $itemData, $repoWac, $wacData, $wareHouseData, $partyData, $isReturn = null)
    {
        //$tranQty = $itemData['qty'] * ( 1 / $itemData['conversionRateUOM'] );
        $tranQty = $itemData['qty'];
        $ledger_arr = array();
        $ledger_arr['documentID'] = ($isReturn == null) ? 'POS' : 'RET';
        $ledger_arr['documentCode'] = ($isReturn == null) ? 'POS' : 'RET';
        $ledger_arr['documentAutoID'] = $itemData['invoiceID'];
        $ledger_arr['documentSystemCode'] = $refCode;
        $ledger_arr['documentDate'] = date('Y-m-d');
        $ledger_arr['referenceNumber'] = $refCode;
        $ledger_arr['companyFinanceYearID'] = $financeYear['companyFinanceYearID'];
        $ledger_arr['companyFinanceYear'] = $financeYear['beginingDate'] . ' - ' . $financeYear['endingDate'];
        $ledger_arr['FYBegin'] = $financeYear['beginingDate'];
        $ledger_arr['FYEnd'] = $financeYear['endingDate'];
        $ledger_arr['FYPeriodDateFrom'] = $financePeriod['dateFrom'];
        $ledger_arr['FYPeriodDateTo'] = $financePeriod['dateTo'];
        $ledger_arr['wareHouseAutoID'] = $wareHouseData['wareHouseAutoID'];
        $ledger_arr['wareHouseCode'] = $wareHouseData['wareHouseCode'];
        $ledger_arr['wareHouseLocation'] = $wareHouseData['wareHouseLocation'];
        $ledger_arr['wareHouseDescription'] = $wareHouseData['wareHouseDescription'];
        $ledger_arr['itemAutoID'] = $itemData['itemAutoID'];
        $ledger_arr['itemSystemCode'] = $itemData['itemSystemCode'];
        $ledger_arr['itemDescription'] = $itemData['itemDescription'];
        $ledger_arr['defaultUOM'] = $itemData['defaultUOM'];
        $ledger_arr['transactionUOM'] = $itemData['unitOfMeasure'];

        if ($isReturn == null) {
            $ledger_arr['transactionQTY'] = ($tranQty * -1);
        } else {
            $ledger_arr['transactionQTY'] = $tranQty;
        }

        $ledger_arr['convertionRate'] = $itemData['conversionRateUOM'];
        $ledger_arr['currentStock'] = $wacData[2];
        $ledger_arr['companyLocalWacAmount'] = $itemData['wacAmount'];
        $ledger_arr['companyReportingWacAmount'] = $repoWac;


        $ledger_arr['PLGLAutoID'] = $itemData['expenseGLAutoID'];
        $ledger_arr['PLSystemGLCode'] = $itemData['expenseSystemGLCode'];
        $ledger_arr['PLGLCode'] = $itemData['expenseGLCode'];
        $ledger_arr['PLDescription'] = $itemData['expenseGLDescription'];
        $ledger_arr['PLType'] = $itemData['expenseGLType'];


        $ledger_arr['BLGLAutoID'] = $itemData['assetGLAutoID'];
        $ledger_arr['BLSystemGLCode'] = $itemData['assetSystemGLCode'];
        $ledger_arr['BLGLCode'] = $itemData['assetGLCode'];
        $ledger_arr['BLDescription'] = $itemData['assetGLDescription'];
        $ledger_arr['BLType'] = $itemData['assetGLType'];


        $ledger_arr['transactionCurrencyDecimalPlaces'] = $itemData['transactionCurrencyDecimalPlaces'];
        if ($isReturn == null) {
            $wacMin = $itemData['wacAmount'] * -1;
            $ledger_arr['transactionAmount'] = round(($wacMin * $tranQty), $itemData['transactionCurrencyDecimalPlaces']);
        } else {
            $ledger_arr['transactionAmount'] = round(($itemData['wacAmount'] * $tranQty), $itemData['transactionCurrencyDecimalPlaces']);
        }

        $itemDiscount = $itemData['discountPer'];
        $ledger_arr['salesPrice'] = ($itemDiscount > 0) ? ($itemData['price'] - ($itemData['price'] * $itemDiscount * 0.01)) : $itemData['price'];
        $ledger_arr['transactionCurrencyID'] = $itemData['transactionCurrencyID'];
        $ledger_arr['transactionCurrency'] = $itemData['transactionCurrency'];
        $ledger_arr['transactionExchangeRate'] = $itemData['transactionExchangeRate'];

        $ledger_arr['companyLocalCurrencyID'] = $itemData['companyLocalCurrencyID'];
        $ledger_arr['companyLocalCurrency'] = $itemData['companyLocalCurrency'];
        $ledger_arr['companyLocalExchangeRate'] = $itemData['companyLocalExchangeRate'];
        $ledger_arr['companyLocalCurrencyDecimalPlaces'] = $itemData['companyLocalCurrencyDecimalPlaces'];
        $ledger_arr['companyLocalAmount'] = round(($ledger_arr['transactionAmount'] / $itemData['companyLocalExchangeRate']), $itemData['companyLocalCurrencyDecimalPlaces']);

        $ledger_arr['companyReportingCurrencyID'] = $itemData['companyReportingCurrencyID'];
        $ledger_arr['companyReportingCurrency'] = $itemData['companyReportingCurrency'];
        $ledger_arr['companyReportingExchangeRate'] = $itemData['companyReportingExchangeRate'];
        $ledger_arr['companyReportingCurrencyDecimalPlaces'] = $itemData['companyReportingCurrencyDecimalPlaces'];
        $ledger_arr['companyReportingAmount'] = round(($ledger_arr['transactionAmount'] / $itemData['companyReportingExchangeRate']),
            $itemData['companyReportingCurrencyDecimalPlaces']);


        $ledger_arr['partyCurrency'] = $partyData['partyCurrency'];
        $ledger_arr['partyCurrencyExchangeRate'] = $partyData['partyER'];
        $ledger_arr['partyCurrencyDecimalPlaces'] = $partyData['partyDPlaces'];
        $ledger_arr['partyCurrencyAmount'] = round(($ledger_arr['transactionAmount'] / $partyData['partyER']), $partyData['partyDPlaces']);


        $ledger_arr['confirmedYN'] = 1;
        $ledger_arr['confirmedByEmpID'] = $itemData['createdUserID'];
        $ledger_arr['confirmedByName'] = $itemData['createdUserName'];
        $ledger_arr['confirmedDate'] = $itemData['createdDateTime'];
        $ledger_arr['approvedYN'] = 1;
        $ledger_arr['approvedbyEmpID'] = $itemData['createdUserID'];
        $ledger_arr['approvedbyEmpName'] = $itemData['createdUserName'];
        $ledger_arr['approvedDate'] = $itemData['createdDateTime'];
        $ledger_arr['segmentID'] = $wareHouseData['segmentID'];
        $ledger_arr['segmentCode'] = $wareHouseData['segmentCode'];
        $ledger_arr['companyID'] = $itemData['companyID'];
        $ledger_arr['companyCode'] = $itemData['companyCode'];
        $ledger_arr['createdUserGroup'] = $itemData['createdUserGroup'];
        $ledger_arr['createdPCID'] = $itemData['createdPCID'];
        $ledger_arr['createdUserID'] = $itemData['createdUserID'];
        $ledger_arr['createdDateTime'] = $itemData['createdDateTime'];
        $ledger_arr['createdUserName'] = $itemData['createdUserName'];

        return $ledger_arr;
    }

    function item_ledger_customerInvoice($financeYear, $financePeriod, $refCode, $itemData, $repoWac, $wacData, $wareHouseData, $partyData, $isReturn = null)
    {
        //$tranQty = $itemData['qty'] * ( 1 / $itemData['conversionRateUOM'] );
        $tranQty = $itemData['qty'];
        $ledger_arr = array();
        $ledger_arr['documentID'] = ($isReturn == null) ? 'CINV' : 'RET';
        $ledger_arr['documentCode'] = ($isReturn == null) ? 'CINV' : 'RET';
        $ledger_arr['documentAutoID'] = $itemData['invoiceID'];
        $ledger_arr['documentSystemCode'] = $refCode;
        $ledger_arr['documentDate'] = date('Y-m-d');
        $ledger_arr['referenceNumber'] = $refCode;
        $ledger_arr['companyFinanceYearID'] = $financeYear['companyFinanceYearID'];
        $ledger_arr['companyFinanceYear'] = $financeYear['beginingDate'] . ' - ' . $financeYear['endingDate'];
        $ledger_arr['FYBegin'] = $financeYear['beginingDate'];
        $ledger_arr['FYEnd'] = $financeYear['endingDate'];
        $ledger_arr['FYPeriodDateFrom'] = $financePeriod['dateFrom'];
        $ledger_arr['FYPeriodDateTo'] = $financePeriod['dateTo'];
        $ledger_arr['wareHouseAutoID'] = $wareHouseData['wareHouseAutoID'];
        $ledger_arr['wareHouseCode'] = $wareHouseData['wareHouseCode'];
        $ledger_arr['wareHouseLocation'] = $wareHouseData['wareHouseLocation'];
        $ledger_arr['wareHouseDescription'] = $wareHouseData['wareHouseDescription'];
        $ledger_arr['itemAutoID'] = $itemData['itemAutoID'];
        $ledger_arr['itemSystemCode'] = $itemData['itemSystemCode'];
        $ledger_arr['itemDescription'] = $itemData['itemDescription'];
        $ledger_arr['defaultUOM'] = $itemData['defaultUOM'];
        $ledger_arr['transactionUOM'] = $itemData['unitOfMeasure'];

        if ($isReturn == null) {
            $ledger_arr['transactionQTY'] = ($tranQty * -1);
        } else {
            $ledger_arr['transactionQTY'] = $tranQty;
        }

        $ledger_arr['convertionRate'] = $itemData['conversionRateUOM'];
        $ledger_arr['currentStock'] = $wacData[2];
        $ledger_arr['companyLocalWacAmount'] = $itemData['wacAmount'];
        $ledger_arr['companyReportingWacAmount'] = $repoWac;


        $ledger_arr['PLGLAutoID'] = $itemData['expenseGLAutoID'];
        $ledger_arr['PLSystemGLCode'] = $itemData['expenseSystemGLCode'];
        $ledger_arr['PLGLCode'] = $itemData['expenseGLCode'];
        $ledger_arr['PLDescription'] = $itemData['expenseGLDescription'];
        $ledger_arr['PLType'] = $itemData['expenseGLType'];


        $ledger_arr['BLGLAutoID'] = $itemData['assetGLAutoID'];
        $ledger_arr['BLSystemGLCode'] = $itemData['assetSystemGLCode'];
        $ledger_arr['BLGLCode'] = $itemData['assetGLCode'];
        $ledger_arr['BLDescription'] = $itemData['assetGLDescription'];
        $ledger_arr['BLType'] = $itemData['assetGLType'];


        $ledger_arr['transactionCurrencyDecimalPlaces'] = $itemData['transactionCurrencyDecimalPlaces'];
        if ($isReturn == null) {
            $wacMin = $itemData['wacAmount'] * -1;
            $ledger_arr['transactionAmount'] = round(($wacMin * $tranQty), $itemData['transactionCurrencyDecimalPlaces']);
        } else {
            $ledger_arr['transactionAmount'] = round(($itemData['wacAmount'] * $tranQty), $itemData['transactionCurrencyDecimalPlaces']);
        }

        $itemDiscount = $itemData['discountPer'];
        $ledger_arr['salesPrice'] = ($itemDiscount > 0) ? ($itemData['price'] - ($itemData['price'] * $itemDiscount * 0.01)) : $itemData['price'];
        $ledger_arr['transactionCurrencyID'] = $itemData['transactionCurrencyID'];
        $ledger_arr['transactionCurrency'] = $itemData['transactionCurrency'];
        $ledger_arr['transactionExchangeRate'] = $itemData['transactionExchangeRate'];

        $ledger_arr['companyLocalCurrencyID'] = $itemData['companyLocalCurrencyID'];
        $ledger_arr['companyLocalCurrency'] = $itemData['companyLocalCurrency'];
        $ledger_arr['companyLocalExchangeRate'] = $itemData['companyLocalExchangeRate'];
        $ledger_arr['companyLocalCurrencyDecimalPlaces'] = $itemData['companyLocalCurrencyDecimalPlaces'];
        $ledger_arr['companyLocalAmount'] = round(($ledger_arr['transactionAmount'] / $itemData['companyLocalExchangeRate']), $itemData['companyLocalCurrencyDecimalPlaces']);

        $ledger_arr['companyReportingCurrencyID'] = $itemData['companyReportingCurrencyID'];
        $ledger_arr['companyReportingCurrency'] = $itemData['companyReportingCurrency'];
        $ledger_arr['companyReportingExchangeRate'] = $itemData['companyReportingExchangeRate'];
        $ledger_arr['companyReportingCurrencyDecimalPlaces'] = $itemData['companyReportingCurrencyDecimalPlaces'];
        $ledger_arr['companyReportingAmount'] = round(($ledger_arr['transactionAmount'] / $itemData['companyReportingExchangeRate']),
            $itemData['companyReportingCurrencyDecimalPlaces']);


        $ledger_arr['partyCurrency'] = $partyData['partyCurrency'];
        $ledger_arr['partyCurrencyExchangeRate'] = $partyData['partyER'];
        $ledger_arr['partyCurrencyDecimalPlaces'] = $partyData['partyDPlaces'];
        $ledger_arr['partyCurrencyAmount'] = round(($ledger_arr['transactionAmount'] / $partyData['partyER']), $partyData['partyDPlaces']);


        $ledger_arr['confirmedYN'] = 1;
        $ledger_arr['confirmedByEmpID'] = $itemData['createdUserID'];
        $ledger_arr['confirmedByName'] = $itemData['createdUserName'];
        $ledger_arr['confirmedDate'] = $itemData['createdDateTime'];
        $ledger_arr['approvedYN'] = 1;
        $ledger_arr['approvedbyEmpID'] = $itemData['createdUserID'];
        $ledger_arr['approvedbyEmpName'] = $itemData['createdUserName'];
        $ledger_arr['approvedDate'] = $itemData['createdDateTime'];
        $ledger_arr['segmentID'] = $wareHouseData['segmentID'];
        $ledger_arr['segmentCode'] = $wareHouseData['segmentCode'];
        $ledger_arr['companyID'] = $itemData['companyID'];
        $ledger_arr['companyCode'] = $itemData['companyCode'];
        $ledger_arr['createdUserGroup'] = $itemData['createdUserGroup'];
        $ledger_arr['createdPCID'] = $itemData['createdPCID'];
        $ledger_arr['createdUserID'] = $itemData['createdUserID'];
        $ledger_arr['createdDateTime'] = $itemData['createdDateTime'];
        $ledger_arr['createdUserName'] = $itemData['createdUserName'];

        return $ledger_arr;
    }


    function double_entry($invID, $partyData, $creditSalesAmount)
    {

        $partyID = $partyData['cusID'];
        $partyName = $partyData['cusName'];
        $partySysCode = $partyData['sysCode'];
        $partyCurrencyID = 0;
        $partyCurrency = $partyData['partyCurrency'];
        $partyER = $partyData['partyER'];
        $partyDP = $partyData['partyDPlaces'];
        if ($creditSalesAmount > 0) {
            $documentid = 'CINV';
        } else {
            $documentid = 'POS';
        }

        /************** EXPENSE GL DEBIT *************/
        $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID, documentSystemCode, documentDate, documentYear, documentMonth, GLAutoID,
                         systemGLCode, GLCode, GLDescription,
                         GLType, amount_type, transactionAmount, transactionCurrencyID, transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces,
                         companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency,  companyLocalExchangeRate, companyLocalCurrencyDecimalPlaces,
                         companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,
                         partyAutoID, partySystemCode, partyName, partyCurrencyID, partyCurrency, partyExchangeRate, partyCurrencyAmount, partyCurrencyDecimalPlaces,
                         confirmedByEmpID, confirmedByName, confirmedDate, approvedDate, approvedbyEmpID, approvedbyEmpName, segmentID, segmentCode, companyID, companyCode,
                         createdUserGroup, createdPCID, createdUserID, createdDateTime, createdUserName)
                         SELECT '{$documentid}', inv.invoiceID, documentSystemCode, invoiceDate, DATE_FORMAT(invoiceDate,'%Y'), DATE_FORMAT(invoiceDate,'%m'),
                         det.expenseGLAutoID, det.expenseSystemGLCode, det.expenseGLCode, det.expenseGLDescription, det.expenseGLType, 'dr',
                         ROUND( sum(wacAmount * qty), det.transactionCurrencyDecimalPlaces ), det.transactionCurrencyID, det.transactionCurrency, det.transactionExchangeRate,
                         det.transactionCurrencyDecimalPlaces, ROUND(sum( (wacAmount * qty) / det.companyLocalExchangeRate), det.companyLocalCurrencyDecimalPlaces),
                         det.companyLocalCurrencyID, det.companyLocalCurrency, det.companyLocalExchangeRate, det.companyLocalCurrencyDecimalPlaces,
                         ROUND( sum( (wacAmount * qty) / det.companyReportingExchangeRate), det.companyReportingCurrencyDecimalPlaces), det.companyReportingCurrencyID,
                         det.companyReportingCurrency, det.companyReportingExchangeRate, det.companyReportingCurrencyDecimalPlaces,
                         {$partyID}, '{$partySysCode}', '{$partyName}', {$partyCurrencyID}, '{$partyCurrency}',  {$partyER} , ROUND( sum( (wacAmount * qty) / {$partyER}),
                         {$partyDP}),  {$partyDP}, inv.createdUserID, inv.createdUserName, inv.createdDateTime, inv.createdDateTime, inv.createdUserID, inv.createdUserName,
                         inv.segmentID, inv.segmentCode, inv.companyID, inv.companyCode, inv.createdUserGroup, inv.createdPCID, inv.createdUserID, inv.createdDateTime,
                         inv.createdUserName
                         FROM srp_erp_pos_invoicedetail det JOIN srp_erp_pos_invoice inv ON inv.invoiceID = det.invoiceID
                         WHERE det.invoiceID ={$invID} AND financeCategory=1 GROUP BY expenseGLAutoID");

        /************** ASSET GL CREDIT *************/
        $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID, documentSystemCode, documentDate, documentYear, documentMonth, GLAutoID,
                         systemGLCode, GLCode, GLDescription, GLType, amount_type, transactionAmount, transactionCurrencyID, transactionCurrency, transactionExchangeRate,
                         transactionCurrencyDecimalPlaces, companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency, companyLocalExchangeRate,
                         companyLocalCurrencyDecimalPlaces,  companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingExchangeRate,
                         companyReportingCurrencyDecimalPlaces, partyAutoID, partySystemCode, partyName, partyCurrencyID, partyCurrency, partyExchangeRate,
                         partyCurrencyAmount, partyCurrencyDecimalPlaces, confirmedByEmpID, confirmedByName, confirmedDate, approvedDate, approvedbyEmpID, approvedbyEmpName,
                         segmentID, segmentCode, companyID, companyCode, createdUserGroup, createdPCID, createdUserID, createdDateTime, createdUserName)
                         SELECT '{$documentid}', inv.invoiceID, documentSystemCode, invoiceDate, DATE_FORMAT(invoiceDate,'%Y'), DATE_FORMAT(invoiceDate,'%m'),
                         det.assetGLAutoID, det.assetSystemGLCode, det.assetGLCode, det.assetGLDescription, det.assetGLType, 'cr',
                         ROUND( sum( (wacAmount * qty *-1)), det.transactionCurrencyDecimalPlaces ), det.transactionCurrencyID, det.transactionCurrency,
                         det.transactionExchangeRate, det.transactionCurrencyDecimalPlaces, ROUND(sum( (wacAmount * qty *-1) / det.companyLocalExchangeRate),
                         det.companyLocalCurrencyDecimalPlaces), det.companyLocalCurrencyID, det.companyLocalCurrency, det.companyLocalExchangeRate,
                         det.companyLocalCurrencyDecimalPlaces, ROUND( sum( (wacAmount * qty *-1) / det.companyReportingExchangeRate),
                         det.companyReportingCurrencyDecimalPlaces), det.companyReportingCurrencyID, det.companyReportingCurrency, det.companyReportingExchangeRate,
                         det.companyReportingCurrencyDecimalPlaces, {$partyID}, '{$partySysCode}', '{$partyName}', {$partyCurrencyID}, '{$partyCurrency}',  {$partyER} ,
                         ROUND( sum( (wacAmount * qty *-1) / {$partyER}), {$partyDP}),  {$partyDP},
                         inv.createdUserID, inv.createdUserName, inv.createdDateTime, inv.createdDateTime, inv.createdUserID, inv.createdUserName, inv.segmentID,
                         inv.segmentCode, inv.companyID, inv.companyCode, inv.createdUserGroup, inv.createdPCID, inv.createdUserID, inv.createdDateTime, inv.createdUserName
                         FROM srp_erp_pos_invoicedetail det JOIN srp_erp_pos_invoice inv ON inv.invoiceID = det.invoiceID
                         WHERE det.invoiceID ={$invID} AND financeCategory=1 GROUP BY assetGLAutoID");

        /************** Revenue GL CREDIT *************/
        $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID, documentSystemCode, documentDate, documentYear, documentMonth, GLAutoID,
                          systemGLCode, GLCode, GLDescription, GLType, amount_type, transactionAmount, transactionCurrencyID, transactionCurrency, transactionExchangeRate,
                          transactionCurrencyDecimalPlaces, companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency, companyLocalExchangeRate,
                          companyLocalCurrencyDecimalPlaces,  companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingExchangeRate,
                          companyReportingCurrencyDecimalPlaces, partyAutoID, partySystemCode, partyName, partyCurrencyID, partyCurrency, partyExchangeRate,
                          partyCurrencyAmount, partyCurrencyDecimalPlaces, confirmedByEmpID, confirmedByName, confirmedDate, approvedDate, approvedbyEmpID, approvedbyEmpName,
                          segmentID, segmentCode, companyID, companyCode, createdUserGroup, createdPCID, createdUserID, createdDateTime, createdUserName)
                         SELECT '{$documentid}', inv.invoiceID, documentSystemCode, invoiceDate, DATE_FORMAT(invoiceDate,'%Y'), DATE_FORMAT(invoiceDate,'%m'),
                         det.revenueGLAutoID, det.revenueSystemGLCode, det.revenueGLCode, det.revenueGLDescription, det.revenueGLType, 'cr',
                         ROUND( sum(det.transactionAmount *-1), det.transactionCurrencyDecimalPlaces ), det.transactionCurrencyID, det.transactionCurrency,
                         det.transactionExchangeRate, det.transactionCurrencyDecimalPlaces, ROUND( sum(det.companyLocalAmount *-1), det.companyLocalCurrencyDecimalPlaces),
                         det.companyLocalCurrencyID, det.companyLocalCurrency, det.companyLocalExchangeRate, det.companyLocalCurrencyDecimalPlaces,
                         ROUND( sum(det.companyReportingAmount *-1), det.companyReportingCurrencyDecimalPlaces), det.companyReportingCurrencyID, det.companyReportingCurrency,
                         det.companyReportingExchangeRate, det.companyReportingCurrencyDecimalPlaces, {$partyID}, '{$partySysCode}', '{$partyName}', {$partyCurrencyID},
                         '{$partyCurrency}',  {$partyER} , ROUND( sum(det.companyLocalAmount /-1) / {$partyER}, {$partyDP}),  {$partyDP},
                         inv.createdUserID, inv.createdUserName, inv.createdDateTime, inv.createdDateTime, inv.createdUserID, inv.createdUserName, inv.segmentID,
                         inv.segmentCode, inv.companyID, inv.companyCode, inv.createdUserGroup, inv.createdPCID, inv.createdUserID, inv.createdDateTime, inv.createdUserName
                         FROM srp_erp_pos_invoicedetail det JOIN srp_erp_pos_invoice inv ON inv.invoiceID = det.invoiceID
                         WHERE det.invoiceID ={$invID} GROUP BY revenueGLAutoID");


        /************** BANK / CUSTOMER GL DEBIT *************/
        $data = $this->db->query("SELECT documentCode, invoiceID, documentSystemCode,invoiceCode, invoiceDate, customerID, DATE_FORMAT(invoiceDate,'%Y') e_year,
                                  DATE_FORMAT(invoiceDate,'%m') e_month, 'cr' amountType, cashAmount, chequeAmount, cardAmount, creditNoteAmount, cardBank, creditSalesAmount,
                                  chequeNo, netTotal, transactionCurrencyID, transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces,
                                  ROUND((netTotal / companyLocalExchangeRate), companyLocalCurrencyDecimalPlaces) localAmount, companyLocalCurrencyID, companyLocalCurrency,
                                  companyLocalExchangeRate, companyLocalCurrencyDecimalPlaces,
                                  ROUND((netTotal / companyReportingExchangeRate), companyReportingCurrencyDecimalPlaces) reportAmount, companyReportingCurrencyID,
                                  companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces, createdUserGroup, createdPCID,
                                  createdUserID, createdUserName, createdDateTime, segmentID, segmentCode, companyID, companyCode
                                  FROM srp_erp_pos_invoice inv
                                  WHERE invoiceID ={$invID}")->row();
        $cusinvoice = $this->db->query("SELECT invoiceCode
                                  FROM srp_erp_customerinvoicemaster
                                  WHERE posMasterAutoID ={$invID} AND posTypeID=1")->row_array();
        $cash = $data->cashAmount;
        $cheque = $data->chequeAmount;
        $card = $data->cardAmount;
        $creditNote = $data->creditNoteAmount;

        $baninvoicePayments = $this->db->query("SELECT
	PaymentID,
	invoiceID,
	paymentConfigMasterID,
	paymentConfigDetailID,
	glAccountType,
	GLCode,
	SUM(amount) as amount,
	reference,
	customerAutoID
FROM
	srp_erp_pos_invoicepayments
WHERE
	invoiceID = $invID
GROUP BY GLCode")->result_array();
        if (!empty($cusinvoice)) {
            $docsyscode = $cusinvoice['invoiceCode'];
        } else {
            $docsyscode = $data->documentSystemCode;
        }

        $GL_Data = array(
            'documentMasterAutoID' => $data->invoiceID,
            'documentCode' => $documentid,
            'documentSystemCode' => $docsyscode,
            'documentDate' => $data->invoiceDate,
            'documentYear' => $data->e_year,
            'documentMonth' => $data->e_month,
            'amount_type' => 'dr',

            'transactionCurrencyID' => $data->transactionCurrencyID,
            'transactionCurrency' => $data->transactionCurrency,
            'transactionCurrencyDecimalPlaces' => $data->transactionCurrencyDecimalPlaces,
            'transactionExchangeRate' => $data->transactionExchangeRate,

            'companyLocalCurrencyID' => $data->companyLocalCurrencyID,
            'companyLocalCurrency' => $data->companyLocalCurrency,
            'companyLocalCurrencyDecimalPlaces' => $data->companyLocalCurrencyDecimalPlaces,
            'companyLocalExchangeRate' => $data->companyLocalExchangeRate,

            'companyReportingCurrencyID' => $data->companyReportingCurrencyID,
            'companyReportingCurrency' => $data->companyReportingCurrency,
            'companyReportingCurrencyDecimalPlaces' => $data->companyReportingCurrencyDecimalPlaces,
            'companyReportingExchangeRate' => $data->companyReportingExchangeRate,

            'confirmedDate' => $data->createdDateTime,
            'confirmedByEmpID' => $data->createdUserID,
            'confirmedByName' => $data->createdUserName,

            'approvedDate' => $data->createdDateTime,
            'approvedbyEmpID' => $data->createdUserID,
            'approvedbyEmpName' => $data->createdUserName,

            'partyAutoID' => $partyID,
            'partySystemCode' => $partySysCode,
            'partyName' => $partyName,
            'partyCurrencyID' => $partyCurrencyID,
            'partyCurrency' => $partyCurrency,
            'partyExchangeRate' => $partyER,
            'partyCurrencyDecimalPlaces' => $partyDP,


            'segmentID' => $data->segmentID,
            'segmentCode' => $data->segmentCode,
            'companyID' => $data->companyID,
            'companyCode' => $data->companyCode,
            'createdPCID' => $data->createdPCID,
            'createdUserID' => $data->createdUserID,
            'createdUserName' => $data->createdUserName,
            'createdUserGroup' => $data->createdUserGroup,
            'createdDateTime' => $data->createdDateTime,
        );


        /**
         * # Auther :sahfri
         * # created on 25-05-2017
         * # Bank Ledger impact
         */


        $payConData = posPaymentConfig_data();


        $bankLedger_Data = array(
            'documentMasterAutoID' => $data->invoiceID,
            'documentSystemCode' => $docsyscode,
            'documentDate' => $data->invoiceDate,

            'transactionType' => 1,
            'documentType' => 'RV',
            'remainIn' => null,
            'memo' => null,
            'clearedYN' => null,
            'clearedDate' => null,
            'clearedAmount' => null,
            'clearedBy' => null,
            'bankRecMonthID' => null,
            'thirdPartyName' => null,
            'thirdPartyInfo' => null,

            'transactionCurrencyID' => $data->transactionCurrencyID,
            'transactionCurrency' => $data->transactionCurrency,
            'transactionCurrencyDecimalPlaces' => $data->transactionCurrencyDecimalPlaces,
            'transactionExchangeRate' => $data->transactionExchangeRate,

            'partyType' => 'CUS',
            'partyAutoID' => $partyID,
            'partyCode' => $partySysCode,
            'partyName' => $partyName,
            'partyCurrencyID' => $partyData['partyCurID'],
            'partyCurrency' => $partyData['partyCurrency'],
            'partyCurrencyExchangeRate' => $partyData['partyER'],
            'partyCurrencyDecimalPlaces' => $partyData['partyDPlaces'],


            'segmentID' => $data->segmentID,
            'segmentCode' => $data->segmentCode,
            'companyID' => $data->companyID,
            'companyCode' => $data->companyCode,
            'createdPCID' => $data->createdPCID,
            'createdUserID' => $data->createdUserID,
            'createdUserName' => $data->createdUserName,
            'createdDateTime' => $data->createdDateTime,
            'timeStamp' => $data->createdDateTime
        );


        $this->load->model('Pos_config_model');
        $localER = $data->companyLocalExchangeRate;
        $localDP = $data->companyLocalCurrencyDecimalPlaces;
        $repoER = $data->companyReportingExchangeRate;
        $repoDP = $data->companyReportingCurrencyDecimalPlaces;

        /*if ($cash != 0 && $cash != null) {
            $cashAmount = $data->cashAmount;
            $cashGLID = $this->Pos_config_model->load_posGL(1); //srp_erp_pos_paymentglconfigmaster => unDepositFund autoID is (1)

            $cashGL = $this->db->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory')
                ->from('srp_erp_chartofaccounts')->where('GLAutoID', $cashGLID)->get()->row();

            $cashData = $GL_Data;
            $cashData['GLAutoID'] = $cashGL->GLAutoID;
            $cashData['systemGLCode'] = $cashGL->systemAccountCode;
            $cashData['GLCode'] = $cashGL->GLSecondaryCode;
            $cashData['GLDescription'] = $cashGL->GLDescription;
            $cashData['GLType'] = $cashGL->subCategory;
            $cashData['transactionAmount'] = $cashAmount;
            $cashData['companyLocalAmount'] = round(($cashAmount / $localER), $localDP);
            $cashData['companyReportingAmount'] = round(($cashAmount / $repoER), $repoDP);
            $cashData['partyCurrencyAmount'] = round(($cashAmount / $partyER), $partyDP);

            $this->db->insert('srp_erp_generalledger', $cashData);


            //BANK LEDGER IMPACT - CASH   created by shafri on 25-05-2017
            $ledgerInfo = loadPOS_BankLedgerInfo('cash');
            $cashData_bankLedger = $bankLedger_Data;
            $cashData_bankLedger['partyCurrencyAmount'] = round(($cashAmount / $partyER), $partyDP);;
            $cashData_bankLedger['transactionAmount'] = $cashAmount;

            $cashData_bankLedger['modeofPayment'] = 1;
            $cashData_bankLedger['chequeNo'] = null;
            $cashData_bankLedger['chequeDate'] = null;
            $cashData_bankLedger['bankName'] = $ledgerInfo['bankName'];
            $cashData_bankLedger['bankGLAutoID'] = $ledgerInfo['GLAutoID'];
            $cashData_bankLedger['bankSystemAccountCode'] = $ledgerInfo['systemAccountCode']; // systemAccountCode.chartofaccount
            $cashData_bankLedger['bankGLSecondaryCode'] = $ledgerInfo['GLSecondaryCode']; // GLSecondaryCode.chartofaccount
            $cashData_bankLedger['bankCurrencyID'] = $ledgerInfo['bankCurrencyID'];
            $cashData_bankLedger['bankCurrencyDecimalPlaces'] = $ledgerInfo['bankCurrencyDecimalPlaces'];

            $conversion = currency_conversionID($data->transactionCurrencyID, $ledgerInfo['bankCurrencyID']);

            $cashData_bankLedger['bankCurrencyID'] = $conversion['currencyID'];
            $cashData_bankLedger['bankCurrency'] = $conversion['CurrencyCode'];
            $cashData_bankLedger['bankCurrencyExchangeRate'] = $conversion['conversion'];
            $cashData_bankLedger['bankCurrencyAmount'] = $cashAmount / $conversion['conversion'];

            $this->db->insert('srp_erp_bankledger', $cashData_bankLedger);

        }*/

        /*if ($cheque != 0 && $cheque != null) {
            $chequeAmount = $data->chequeAmount;
            $chequeGLID = $this->Pos_config_model->load_posGL(1); //srp_erp_pos_paymentglconfigmaster => unDepositFund autoID is (1)

            $chequeBnkGL = $this->db->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory')
                ->from('srp_erp_chartofaccounts')->where('GLAutoID', $chequeGLID)->get()->row();

            $chequeData = $GL_Data;
            $chequeData['chequeNumber'] = $data->chequeNo;
            $chequeData['GLAutoID'] = $chequeBnkGL->GLAutoID;
            $chequeData['systemGLCode'] = $chequeBnkGL->systemAccountCode;
            $chequeData['GLCode'] = $chequeBnkGL->GLSecondaryCode;
            $chequeData['GLDescription'] = $chequeBnkGL->GLDescription;
            $chequeData['GLType'] = $chequeBnkGL->subCategory;
            $chequeData['transactionAmount'] = $chequeAmount;
            $chequeData['companyLocalAmount'] = round(($chequeAmount / $localER), $localDP);
            $chequeData['companyReportingAmount'] = round(($chequeAmount / $repoER), $repoDP);
            $chequeData['partyCurrencyAmount'] = round(($chequeAmount / $partyER), $partyDP);
            $this->db->insert('srp_erp_generalledger', $chequeData);



            $ledgerInfo = loadPOS_BankLedgerInfo('cash'); // un-deposited fund
            $cashData_bankLedger = $bankLedger_Data;
            $cashData_bankLedger['partyCurrencyAmount'] = round(($chequeAmount / $partyER), $partyDP);
            $cashData_bankLedger['transactionAmount'] = $chequeAmount;
            $cashData_bankLedger['modeofPayment'] = 2;

            $cashData_bankLedger['chequeNo'] = $this->input->post('_chequeNO');
            $tmpDate = $this->input->post('_chequeCashDate');
            $cashData_bankLedger['chequeDate'] = format_date_mysql_datetime($tmpDate);
            $cashData_bankLedger['bankName'] = $ledgerInfo['bankName'];
            $cashData_bankLedger['bankGLAutoID'] = $ledgerInfo['GLAutoID'];
            $cashData_bankLedger['bankSystemAccountCode'] = $ledgerInfo['systemAccountCode']; // systemAccountCode.chartofaccount
            $cashData_bankLedger['bankGLSecondaryCode'] = $ledgerInfo['GLSecondaryCode']; // GLSecondaryCode.chartofaccount
            $cashData_bankLedger['bankCurrencyID'] = $ledgerInfo['bankCurrencyID'];
            $cashData_bankLedger['bankCurrencyDecimalPlaces'] = $ledgerInfo['bankCurrencyDecimalPlaces'];

            $conversion = currency_conversionID($data->transactionCurrencyID, $ledgerInfo['bankCurrencyID']);

            $cashData_bankLedger['bankCurrencyID'] = $conversion['currencyID'];
            $cashData_bankLedger['bankCurrency'] = $conversion['CurrencyCode'];
            $cashData_bankLedger['bankCurrencyExchangeRate'] = $conversion['conversion'];
            $cashData_bankLedger['bankCurrencyAmount'] = $chequeAmount / $conversion['conversion'];

            $this->db->insert('srp_erp_bankledger', $cashData_bankLedger);
        }*/

        foreach ($baninvoicePayments as $bankDE) {
            if (($bankDE['paymentConfigMasterID'] != 2) && ($bankDE['paymentConfigMasterID'] != 7) && ($bankDE['paymentConfigMasterID'] != 25) && ($bankDE['paymentConfigMasterID'] != 26)) {

                /** To Nasik : please change this to vias & master cards because it has different GL codes */
                $cardAmount = $data->cardAmount;
                $cardBankGLID = $data->cardBank;
                $bankGL = $this->db->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory')
                    ->from('srp_erp_chartofaccounts')->where('GLAutoID', $bankDE['GLCode'])->get()->row();


                $cardData = $GL_Data;
                $cardData['GLAutoID'] = $bankGL->GLAutoID;
                $cardData['systemGLCode'] = $bankGL->systemAccountCode;
                $cardData['GLCode'] = $bankGL->GLSecondaryCode;
                $cardData['GLDescription'] = $bankGL->GLDescription;
                $cardData['GLType'] = $bankGL->subCategory;
                $cardData['transactionAmount'] = $bankDE['amount'];
                $cardData['companyLocalAmount'] = round(($bankDE['amount'] / $localER), $localDP);
                $cardData['companyReportingAmount'] = round(($bankDE['amount'] / $repoER), $repoDP);
                $cardData['partyCurrencyAmount'] = round(($bankDE['amount'] / $partyER), $partyDP);

                $this->db->insert('srp_erp_generalledger', $cardData);


                /** BANK LEDGER IMPACT - Master OR Visa   created by shafri on 25-05-2017 */
                $GLAutoID = $this->input->post('_bank');
                $this->db->select("*");
                $this->db->from("srp_erp_chartofaccounts");
                $this->db->where("GLAutoID", $bankDE['GLCode']);
                $chartOfAccountTmp = $this->db->get()->row();

                $cashData_bankLedger = $bankLedger_Data;
                $cashData_bankLedger['partyCurrencyAmount'] = round(($bankDE['amount'] / $partyER), $partyDP);
                $cashData_bankLedger['transactionAmount'] = $bankDE['amount'];
                $cashData_bankLedger['modeofPayment'] = 1;
                $cashData_bankLedger['chequeNo'] = null;
                $cashData_bankLedger['chequeDate'] = null;
                $cashData_bankLedger['bankName'] = $chartOfAccountTmp->bankName;
                $cashData_bankLedger['bankGLAutoID'] = $chartOfAccountTmp->GLAutoID;
                $cashData_bankLedger['bankSystemAccountCode'] = $chartOfAccountTmp->systemAccountCode;
                $cashData_bankLedger['bankGLSecondaryCode'] = $chartOfAccountTmp->GLSecondaryCode;
                $cashData_bankLedger['bankCurrencyID'] = $chartOfAccountTmp->bankCurrencyID;
                $cashData_bankLedger['bankCurrencyDecimalPlaces'] = $chartOfAccountTmp->bankCurrencyDecimalPlaces;

                $conversion = currency_conversionID($data->transactionCurrencyID, $chartOfAccountTmp->bankCurrencyID);

                $cashData_bankLedger['bankCurrencyID'] = $conversion['currencyID'];
                $cashData_bankLedger['bankCurrency'] = $conversion['CurrencyCode'];
                $cashData_bankLedger['bankCurrencyExchangeRate'] = $conversion['conversion'];
                $cashData_bankLedger['bankCurrencyAmount'] = $bankDE['amount'] / $conversion['conversion'];

                $this->db->insert('srp_erp_bankledger', $cashData_bankLedger);
            }
        }


        if ($creditNote != 0 && $creditNote != null) {

            $creditAmount = $data->creditNoteAmount;
            $creditNoteGLID = $this->Pos_config_model->load_posGL(2); //srp_erp_pos_paymentglconfigmaster => creditNote autoID is (2)
            $creditNoteGL = $this->db->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory')
                ->from('srp_erp_chartofaccounts')
                ->join('srp_erp_pos_paymentglconfigdetail', 'srp_erp_pos_paymentglconfigdetail.GLCode=srp_erp_chartofaccounts.GLAutoID')
                ->where('GLAutoID', $creditNoteGLID)->get()->row();

            $creditNoteData = $GL_Data;
            $creditNoteData['GLAutoID'] = $creditNoteGL->GLAutoID;
            $creditNoteData['systemGLCode'] = $creditNoteGL->systemAccountCode;
            $creditNoteData['GLCode'] = $creditNoteGL->GLSecondaryCode;
            $creditNoteData['GLDescription'] = $creditNoteGL->GLDescription;
            $creditNoteData['GLType'] = $creditNoteGL->subCategory;
            $creditNoteData['transactionAmount'] = $creditAmount;
            $creditNoteData['companyLocalAmount'] = round(($creditAmount / $localER), $localDP);
            $creditNoteData['companyReportingAmount'] = round(($creditAmount / $repoER), $repoDP);
            $creditNoteData['partyCurrencyAmount'] = round(($creditAmount / $partyER), $partyDP);


            $this->db->insert('srp_erp_generalledger', $creditNoteData);


            /** BANK LEDGER IMPACT - Credit Note created by shafri on 25-05-2017 */
            $ledgerInfo = loadPOS_BankLedgerInfo('creditNote');
            $cashData_bankLedger = $bankLedger_Data;
            $cashData_bankLedger['partyCurrencyAmount'] = round(($creditAmount / $partyER), $partyDP);
            $cashData_bankLedger['transactionAmount'] = $creditAmount;
            $cashData_bankLedger['modeofPayment'] = null;
            $cashData_bankLedger['chequeNo'] = null;
            $cashData_bankLedger['chequeDate'] = null;
            $cashData_bankLedger['bankName'] = $ledgerInfo['bankName'];
            $cashData_bankLedger['bankGLAutoID'] = $ledgerInfo['GLAutoID'];
            $cashData_bankLedger['bankSystemAccountCode'] = $ledgerInfo['systemAccountCode']; // systemAccountCode.chartofaccount
            $cashData_bankLedger['bankGLSecondaryCode'] = $ledgerInfo['GLSecondaryCode']; // GLSecondaryCode.chartofaccount
            $cashData_bankLedger['bankCurrencyID'] = $ledgerInfo['bankCurrencyID'];
            $cashData_bankLedger['bankCurrencyDecimalPlaces'] = $ledgerInfo['bankCurrencyDecimalPlaces'];

            $conversion = currency_conversionID($data->transactionCurrencyID, $ledgerInfo['bankCurrencyID']);

            $cashData_bankLedger['bankCurrencyID'] = $conversion['currencyID'];
            $cashData_bankLedger['bankCurrency'] = $conversion['CurrencyCode'];
            $cashData_bankLedger['bankCurrencyExchangeRate'] = $conversion['conversion'];
            $cashData_bankLedger['bankCurrencyAmount'] = $creditAmount / $conversion['conversion'];

            $this->db->insert('srp_erp_bankledger', $cashData_bankLedger);
        }


        if ($data->creditSalesAmount > 0 && $data->customerID != 0) {
            $partyGL = $partyData['partyGL'];
            $customerData = $GL_Data;
            $customerData['GLAutoID'] = $partyGL['receivableAutoID'];
            $customerData['systemGLCode'] = $partyGL['receivableSystemGLCode'];
            $customerData['GLCode'] = $partyGL['receivableGLAccount'];
            $customerData['GLDescription'] = $partyGL['receivableDescription'];
            $customerData['GLType'] = $partyGL['receivableType'];
            $customerData['transactionAmount'] = $data->creditSalesAmount;
            $customerData['companyLocalAmount'] = round(($data->creditSalesAmount / $localER), $localDP);
            $customerData['companyReportingAmount'] = round(($data->creditSalesAmount / $repoER), $repoDP);
            $customerData['partyCurrencyAmount'] = round(($data->creditSalesAmount / $partyER), $partyDP);
            $customerData['subLedgerType'] = 3;
            $customerData['subLedgerDesc'] = 'AR';

            $this->db->insert('srp_erp_generalledger', $customerData);
        }

        return $GL_Data;

    }

    function invoice_hold()
    {

        $com_currency = $this->common_data['company_data']['company_default_currency'];
        $com_currency_id = $this->common_data['company_data']['company_default_currencyID'];
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

        $invArray = array(
            'documentSystemCode' => $refCode,
            'serialNo' => $lastRefNo,
            'customerID' => $customerID,
            'customerCode' => $customerCode,
            'invoiceDate' => $invoiceDate,

            'netTotal' => number_format($netTotVal, $com_currDPlace),
            'localNetTotal' => ($netTotVal / $localConversionRate),
            'reportingNetTotal' => ($netTotVal / $reportConversionRate),

            'paidAmount' => $paidAmount,
            'localPaidAmount' => ($paidAmount / $localConversionRate),
            'reportingPaidAmount' => ($paidAmount / $reportConversionRate),

            'balanceAmount' => $balanceAmount,
            'localBalanceAmount' => ($balanceAmount / $localConversionRate),
            'reportingBalanceAmount' => ($balanceAmount / $reportConversionRate),

            'cashAmount' => $cashAmount,
            'chequeAmount' => $chequeAmount,
            'cardAmount' => $cardAmount,

            'discountAmount' => $total_discVal,
            'localDiscountAmount' => ($total_discVal / $localConversionRate),
            'reportingDiscountAmount' => ($total_discVal / $reportConversionRate),


            'companyLocalExchangeRate' => $localConversionRate,
            'companyLocalCurrency' => $com_currency,
            'companyLocalCurrencyDecimalPlaces' => $com_currDPlace,
            'companyLocalCurrencyID' => $com_currency_id,

            'transactionExchangeRate' => $localConversionRate,
            'transactionCurrencyID' => $com_currency_id,
            'transactionCurrency' => $com_currency,
            'transactionCurrencyDecimalPlaces' => $com_currDPlace,

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
            $price = str_replace(',', '', $itemPrice[$i]);
            $itemTotal = $itemQty[$i] * $price;
            $itemTotal = ($itemDis[$i] > 0) ? ($itemTotal - ($itemTotal * 0.01 * $itemDis[$i])) : $itemTotal;

            $dataInt[$i]['invoiceID'] = $invID;
            $dataInt[$i]['itemAutoID'] = $itemID;
            $dataInt[$i]['itemSystemCode'] = $itemData['itemSystemCode'];
            $dataInt[$i]['itemDescription'] = $itemData['itemDescription'];
            $dataInt[$i]['defaultUOM'] = $itemData['defaultUnitOfMeasure'];
            $dataInt[$i]['unitOfMeasure'] = $itemUOM[$i];
            $dataInt[$i]['conversionRateUOM'] = conversionRateUOM($itemUOM[$i], $itemData['defaultUnitOfMeasure']);
            $dataInt[$i]['qty'] = $itemQty[$i];
            $dataInt[$i]['price'] = $price;
            $dataInt[$i]['discountPer'] = $itemDis[$i];

            $dataInt[$i]['itemFinanceCategory'] = $itemData['subcategoryID'];
            $dataInt[$i]['itemFinanceCategorySub'] = $itemData['subSubCategoryID'];
            $dataInt[$i]['financeCategory'] = $itemData['financeCategory'];
            $dataInt[$i]['itemCategory'] = $itemData['mainCategory'];

            $dataInt[$i]['transactionAmount'] = $itemTotal;
            $dataInt[$i]['transactionExchangeRate'] = '';
            $dataInt[$i]['transactionCurrency'] = $com_currency;
            $dataInt[$i]['transactionCurrencyDecimalPlaces'] = '';

            $dataInt[$i]['companyLocalAmount'] = round(($itemTotal / $localConversionRate), $com_currDPlace);
            $dataInt[$i]['companyLocalExchangeRate'] = $localConversionRate;
            $dataInt[$i]['companyLocalCurrency'] = $com_currency;
            $dataInt[$i]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

            $dataInt[$i]['companyReportingAmount'] = round(($itemTotal / $reportConversionRate), $rep_currDPlace);
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

        return $this->db->query("SELECT customerAutoID, customerSystemCode,secondaryCode, customerName, customerCurrency, customerTelephone
                                 FROM srp_erp_customermaster WHERE companyID={$companyID} AND
                                 (customerName LIKE '%$key%' OR customerTelephone LIKE '%$key%' OR secondaryCode LIKE '%$key%')
                                 UNION SELECT 0, 'CASH', 'Cash', '','',''")->result_array();
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
            return array('s', 'Card Details Updated', $invID);
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
                                 FROM srp_erp_pos_invoice t1 WHERE companyID={$companyID} AND wareHouseAutoID={$wareHouse}")->result_array();
    }

    function creditNote_search()
    {
        $key = $this->input->post('key');
        $companyID = current_companyID();
        return $this->db->select('salesReturnID, documentSystemCode, salesReturnDate, netTotal')
            ->from('srp_erp_pos_salesreturn t1')
            ->where("(documentSystemCode LIKE '%" . $key . "%'  OR  netTotal LIKE '%" . $key . "%')
                        AND companyID={$companyID}
                        AND NOT EXISTS (
                            SELECT * FROM srp_erp_pos_invoice WHERE creditNoteID = t1.salesReturnID
                        )
                    ")->get()->result_array();
    }

    function get_returnCode($creditNoteID)
    {
        return $this->db->select('documentSystemCode')->from('srp_erp_pos_salesreturn')
            ->where('salesReturnID', $creditNoteID)->get()->row('documentSystemCode');
    }

    function invoice_search($invoiceID = null)
    {
        $companyID = current_companyID();
        $wareHouse = $this->common_data['ware_houseID'];

        /** when we open it from receipt, we do not need outlet filter */
        $receipt = $this->input->post('receipt');
        if ($receipt == 1) {
            $where = array('companyID' => $companyID);
        } else {

            $where = array('companyID' => $companyID, 'wareHouseAutoID' => $wareHouse);
        }

        if ($invoiceID != null) {
            $where['t1.invoiceID'] = $invoiceID;
        } else {
            $invoiceCode = $this->input->post('invoiceCode');
            $where['t1.invoiceCode'] = $invoiceCode;
        }


        $isExistInv = $this->db->select("t1.*, if(customerID=0 , 'Cash',
                                  (SELECT customerName FROM srp_erp_customermaster WHERE  customerAutoID=t1.customerID)) AS cusName,
                                  (SELECT sum(balanceAmount) FROM srp_erp_pos_invoice WHERE customerID = t1.customerID) AS cusBalance,
                                  (SELECT EmpShortCode FROM srp_employeesdetails WHERE EIdNo = t1.createdUserID) AS repName")
            ->from("srp_erp_pos_invoice t1")
            ->where($where)->get()->row_array();

        if ($isExistInv != null) {
            $invoiceID = $isExistInv['invoiceID'];


            $invItems = $this->db->select('*, (SELECT itemImage FROM srp_erp_itemmaster WHERE itemAutoID=invoiceDetail.itemAutoID) itemImage')
                ->select(" (invoiceDetail.qty - (SELECT  IFNULL(SUM(qty),0) FROM srp_erp_pos_salesreturndetails WHERE itemAutoID = invoiceDetail.itemAutoID
                                        AND invoiceID = {$invoiceID} AND invoiceDetailID = invoiceDetail.invoiceDetailsID ) ) balanceQty")
                ->from('srp_erp_pos_invoicedetail invoiceDetail')->where('invoiceID', $invoiceID)->get()->result_array();

            $invCodeDet = $this->getReturnCode();
            return array(
                0 => 's',
                1 => $isExistInv,
                2 => $invItems,
                3 => $invCodeDet['refCode']
            );
        } else {
            return array('w', 'There is not a invoice in this number');
        }
    }

    function invoice_return()
    {

        $financeYear = $this->db->select('companyFinanceYearID, beginingDate, endingDate')->from('srp_erp_companyfinanceyear')
            ->where(
                array(
                    'isActive' => 1,
                    'isCurrent' => 1,
                    'companyID' => current_companyID()
                )
            )->get()->row_array();


        $financePeriod = $this->db->select('companyFinancePeriodID, dateFrom, dateTo')->from('srp_erp_companyfinanceperiod')
            ->where(
                array(
                    'isActive' => 1,
                    'isCurrent' => 1,
                    'companyID' => current_companyID()
                )
            )->get()->row_array();

        if (empty($financeYear)) {
            return array('e', 'Please setup the current financial year');
            exit;
        }

        if (empty($financePeriod)) {
            return array('e', 'Please setup the current financial period');
            exit;
        }

        $currentShiftData = $this->isHaveNotClosedSession();

        if (!empty($currentShiftData)) {

            $com_currency = $this->common_data['company_data']['company_default_currency'];
            $com_currDPlace = $this->common_data['company_data']['company_default_decimal'];
            $rep_currency = $this->common_data['company_data']['company_reporting_currency'];
            $rep_currDPlace = $this->common_data['company_data']['company_reporting_decimal'];
            $wareHouseData = $this->get_wareHouse();

            $returnType = 0;
            $returnMode = $this->input->post('returnMode');
            /***************************************************
             *  $returnMode  =>  exchange/ creditNote =>  1    *
             *  $returnMode  =>  Refund               =>  2    *
             *  $returnMode  =>  credit-to-customer   =>  3    *
             ***************************************************/

            switch ($returnMode) {
                case 'exchange':
                    $returnType = 1;
                    break;

                case 'Refund':
                    $returnType = 2;
                    break;

                case 'credit-to-customer':
                    $returnType = 3;
                    break;
            }

            $return_invID = $this->input->post('return-invoiceID');
            $customerID = $this->input->post('return-customerID');
            $customerCode = $this->input->post('return-cusCode');
            $tr_currency = $this->common_data['company_data']['company_default_currency'];
            $invoiceDetailsID = $this->input->post('invoiceDetailsID[]');
            $item = $this->input->post('itemID[]');
            $itemUOM = $this->input->post('itemUOM[]');
            $return_QTY = $this->input->post('return_QTY[]');
            $itemPrice = $this->input->post('itemPrice[]');
            $itemDis = $this->input->post('itemDis[]');
            $invoiceDate = format_date($this->input->post('return-date'));

            /*Payment Details Calculation Start*/
            $refundable_hidden = $this->input->post('return-refundable-hidden');
            $refund = str_replace(',', '', $this->input->post('return-refund'));
            $total_discVal = str_replace(',', '', $this->input->post('return-discTotal'));
            $netTotVal = str_replace(',', '', $this->input->post('return-credit-total'));
            $subTotalAmount = str_replace(',', '', $this->input->post('return-subTotalAmount'));


            //Get last reference no
            $invCodeDet = $this->getReturnCode();

            $lastRefNo = $invCodeDet['lastRefNo'];
            $refCode = $invCodeDet['refCode'];

            $localConversion = currency_conversion($tr_currency, $com_currency, $netTotVal);
            $localConversionRate = $localConversion['conversion'];
            $transConversion = currency_conversion($tr_currency, $tr_currency, $netTotVal);
            $tr_currDPlace = $transConversion['DecimalPlaces'];
            $transConversionRate = $transConversion['conversion'];
            $reportConversion = currency_conversion($tr_currency, $rep_currency, $netTotVal);
            $reportConversionRate = $reportConversion['conversion'];


            $returnArray = array(
                'invoiceID' => $return_invID,
                'documentSystemCode' => $refCode,
                'documentCode' => 'RET',
                'serialNo' => $lastRefNo,
                'financialYearID' => $financeYear['companyFinanceYearID'],
                'financialPeriodID' => $financePeriod['companyFinancePeriodID'],
                'FYBegin' => $financeYear['beginingDate'],
                'FYEnd' => $financeYear['endingDate'],
                'FYPeriodDateFrom' => $financePeriod['dateFrom'],
                'FYPeriodDateTo' => $financePeriod['dateTo'],
                'customerID' => $customerID,
                'customerCode' => $customerCode,
                'salesReturnDate' => $invoiceDate,
                'counterID' => $currentShiftData['counterID'],
                'shiftID' => $currentShiftData['shiftID'],

                'subTotal' => $subTotalAmount,
                'netTotal' => $netTotVal,
                'refundAmount' => $refund,
                'discountAmount' => $total_discVal,
                'returnMode' => $returnType,

                'companyLocalCurrencyID' => $localConversion['currencyID'],
                'companyLocalCurrency' => $com_currency,
                'companyLocalCurrencyDecimalPlaces' => $com_currDPlace,
                'companyLocalExchangeRate' => $localConversionRate,

                'transactionCurrencyID' => $localConversion['trCurrencyID'],
                'transactionCurrency' => $tr_currency,
                'transactionCurrencyDecimalPlaces' => $tr_currDPlace,
                'transactionExchangeRate' => $transConversionRate,

                'companyReportingCurrencyID' => $reportConversion['currencyID'],
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
                $returnArray['bankGLAutoID'] = $bankData['receivableAutoID'];
                $returnArray['bankSystemGLCode'] = $bankData['receivableSystemGLCode'];
                $returnArray['bankGLAccount'] = $bankData['receivableGLAccount'];
                $returnArray['bankGLDescription'] = $bankData['receivableDescription'];
                $returnArray['bankGLType'] = $bankData['receivableType'];

                /*************** item ledger party currency ***********/
                $partyData = array(
                    'cusID' => 0,
                    'sysCode' => 'CASH',
                    'cusName' => 'CASH',
                    'partyCurID' => '',
                    'partyCurrency' => $tr_currency,
                    'partyDPlaces' => $tr_currDPlace,
                    'partyER' => $transConversionRate,
                );
            } else {
                $cusData = $this->db->query("SELECT receivableAutoID, receivableSystemGLCode, receivableGLAccount,
                                             receivableDescription, receivableType,customerCurrency,customerSystemCode,customerAutoID,customerName,customerCurrencyID,customerCurrency,customerCurrencyDecimalPlaces
                                             FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();

                $partyData = currency_conversion($tr_currency, $cusData['customerCurrency']);

                $returnArray['customerReceivableAutoID'] = $cusData['receivableAutoID'];
                $returnArray['customerReceivableSystemGLCode'] = $cusData['receivableSystemGLCode'];
                $returnArray['customerReceivableGLAccount'] = $cusData['receivableGLAccount'];
                $returnArray['customerReceivableDescription'] = $cusData['receivableDescription'];
                $returnArray['customerReceivableType'] = $cusData['receivableType'];

                /*************** item ledger party currency ***********/

                $partyData = array(
                    'cusID' => $cusData['customerAutoID'],
                    'sysCode' => $cusData['customerSystemCode'],
                    'cusName' => $cusData['customerName'],
                    'partyCurID' => $cusData['customerCurrencyID'],
                    'partyCurrency' => $cusData['customerCurrency'],
                    'partyDPlaces' => $cusData['customerCurrencyDecimalPlaces'],
                    'partyER' => $partyData['conversion'],
                    'partyGL' => $cusData,
                );
            }

            $this->db->trans_start();
            $this->db->insert('srp_erp_pos_salesreturn', $returnArray);
            $salesReturnID = $this->db->insert_id();

            /*Load wac library*/
            $this->load->library('Wac');

            $itemReturn_ledger_arr = array();
            $i = 0;
            $dataInt = array();
            foreach ($return_QTY as $r_qty) {
                $itemID = $item[$i];
                $itemData = fetch_ware_house_item_data($itemID);
                $conversion = conversionRateUOM($itemUOM[$i], $itemData['defaultUnitOfMeasure']);
                $conversionRate = 1 / $conversion;
                $qty = $r_qty * $conversionRate;
                $returnData = $this->get_invReturnBalanceQty($return_invID, $invoiceDetailsID[$i], $itemID);
                $balanceQty = $returnData->balanceQty;
                $invWac = $returnData->wacAmount;

                if ($qty > 0) {
                    if ($balanceQty >= $qty) {

                        $price = str_replace(',', '', $itemPrice[$i]);
                        $itemTotal = $r_qty * $price;
                        $itemTotal = ($itemDis[$i] > 0) ? ($itemTotal - ($itemTotal * 0.01 * $itemDis[$i])) : $itemTotal;


                        $dataInt[$i]['salesReturnID'] = $salesReturnID;
                        $dataInt[$i]['invoiceID'] = $return_invID;
                        $dataInt[$i]['invoiceDetailID'] = $invoiceDetailsID[$i];
                        $dataInt[$i]['itemAutoID'] = $itemID;
                        $dataInt[$i]['itemSystemCode'] = $itemData['itemSystemCode'];
                        $dataInt[$i]['itemDescription'] = $itemData['itemDescription'];
                        $dataInt[$i]['defaultUOM'] = $itemData['defaultUnitOfMeasure'];
                        $dataInt[$i]['unitOfMeasure'] = $itemUOM[$i];
                        $dataInt[$i]['conversionRateUOM'] = $conversion;
                        $dataInt[$i]['qty'] = $qty;
                        $dataInt[$i]['price'] = $price;
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


                        $dataInt[$i]['transactionAmount'] = $itemTotal;
                        $dataInt[$i]['transactionExchangeRate'] = $transConversionRate;
                        $dataInt[$i]['transactionCurrency'] = $tr_currency;
                        $dataInt[$i]['transactionCurrencyID'] = $localConversion['trCurrencyID'];
                        $dataInt[$i]['transactionCurrencyDecimalPlaces'] = $tr_currDPlace;

                        $dataInt[$i]['companyLocalAmount'] = round(($itemTotal / $localConversionRate), $com_currDPlace);
                        $dataInt[$i]['companyLocalExchangeRate'] = $localConversionRate;
                        $dataInt[$i]['companyLocalCurrency'] = $com_currency;
                        $dataInt[$i]['companyLocalCurrencyID'] = $localConversion['currencyID'];
                        $dataInt[$i]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

                        $dataInt[$i]['companyReportingAmount'] = round(($itemTotal / $reportConversionRate), $rep_currDPlace);
                        $dataInt[$i]['companyReportingExchangeRate'] = $reportConversionRate;
                        $dataInt[$i]['companyReportingCurrency'] = $rep_currency;
                        $dataInt[$i]['companyReportingCurrencyID'] = $reportConversion['currencyID'];
                        $dataInt[$i]['companyReportingCurrencyDecimalPlaces'] = $rep_currDPlace;

                        $dataInt[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                        $dataInt[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                        $dataInt[$i]['createdPCID'] = $this->common_data['current_pc'];
                        $dataInt[$i]['createdUserID'] = $this->common_data['current_userID'];
                        $dataInt[$i]['createdUserName'] = $this->common_data['current_user'];
                        $dataInt[$i]['createdUserGroup'] = $this->common_data['user_group'];
                        $dataInt[$i]['createdDateTime'] = current_date();


                        /*$newQty = $availableQTY + $qty;

                        $itemUpdateWhere = array('itemAutoID' => $itemID, 'wareHouseAutoID' => $this->common_data['ware_houseID']);
                        $itemUpdateQty = array('currentStock' => $newQty);
                        $this->db->where($itemUpdateWhere)->update('srp_erp_warehouseitems', $itemUpdateQty);*/


                        $wacData = $this->wac->wac_calculation(0, $itemID, $qty, $invWac, $this->common_data['ware_houseID']);
                        $itemReturn_ledger_arr[$i] = $this->item_ledger($financeYear, $financePeriod, $refCode, $dataInt[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData, 1);

                        $i++;
                    } else {
                        $this->db->trans_rollback();
                        return array('e', '[ ' . $itemData['itemSystemCode'] . ' - ' . $itemData['itemDescription'] . ' ]<p> maximum return quantity is : ' . $balanceQty);
                        break;
                    }
                }

            }


            $this->db->insert_batch('srp_erp_pos_salesreturndetails', $dataInt);
            $this->db->insert_batch('srp_erp_itemledger', $itemReturn_ledger_arr);
            $this->double_entry_itemReturn($salesReturnID, $partyData);


            $this->db->trans_complete();
            if ($this->db->trans_status() == false) {
                $this->db->trans_rollback();
                return array('e', 'Error in return process');
            } else {
                $this->db->trans_commit();
                /*$invoiceCode = $this->db->query("SELECT invoiceCode FROM srp_erp_pos_invoice WHERE invoiceID={$return_invID}")->row_array();*/

                return array('s', 'Return Note : ' . $refCode . ' ', $salesReturnID, $refCode);
            }
        } else {
            return array('e', 'You have not a valid session.<p>Please login and try again.</p>');
        }
    }

    function double_entry_itemReturn($salesReturnID, $partyData)
    {

        /*" . $partyData['cusID'] . " partyID, '" . $partyData['cusName'] . "' partyName, '" . $partyData['sysCode'] . "' partyCode, '" . $partyData['partyCurrency'] . "' partyCur, " . $partyData['partyDPlaces'] . " partyDPlace, " . $partyData['partyER'] . " partyER,*/
        $partyID = $partyData['cusID'];
        $partyName = $partyData['cusName'];
        $partySysCode = $partyData['sysCode'];
        $partyCurrencyID = 0;
        $partyCurrency = $partyData['partyCurrency'];
        $partyER = $partyData['partyER'];
        $partyDP = $partyData['partyDPlaces'];

        /************** EXPENSE GL DEBIT *************/
        $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID, documentSystemCode, documentDate, documentYear, documentMonth, GLAutoID, systemGLCode, GLCode, GLDescription,
                     GLType, amount_type, transactionAmount, transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces, companyLocalAmount, companyLocalCurrency,  companyLocalExchangeRate,
                     companyLocalCurrencyDecimalPlaces,  companyReportingAmount, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,
                     partyAutoID, partySystemCode, partyName, partyCurrencyID, partyCurrency, partyExchangeRate, partyCurrencyAmount, partyCurrencyDecimalPlaces,
                     confirmedByEmpID, confirmedByName, confirmedDate, approvedDate, approvedbyEmpID, approvedbyEmpName, segmentID, segmentCode, companyID, companyCode, createdUserGroup, createdPCID, createdUserID,
                     createdDateTime, createdUserName)
                     SELECT documentCode, ret.invoiceID, documentSystemCode, salesReturnDate, DATE_FORMAT(salesReturnDate,'%Y'), DATE_FORMAT(salesReturnDate,'%m'),
                     det.expenseGLAutoID, det.expenseSystemGLCode, det.expenseGLCode, det.expenseGLDescription, det.expenseGLType, 'cr',
                     ROUND( sum(wacAmount * qty *-1), det.transactionCurrencyDecimalPlaces ), det.transactionCurrency , det.transactionExchangeRate, det.transactionCurrencyDecimalPlaces,
                     ROUND( sum( (wacAmount * qty *-1) / det.companyLocalExchangeRate), det.companyLocalCurrencyDecimalPlaces), det.companyLocalCurrency, det.companyLocalExchangeRate, det.companyLocalCurrencyDecimalPlaces,
                     ROUND( sum( (wacAmount * qty *-1) / det.companyReportingExchangeRate), det.companyReportingCurrencyDecimalPlaces), det.companyReportingCurrency, det.companyReportingExchangeRate, det.companyReportingCurrencyDecimalPlaces,
                     {$partyID}, '" . $partySysCode . "', '" . $partyName . "', {$partyCurrencyID}, '" . $partyCurrency . "',  {$partyER} , ROUND( sum( (wacAmount * qty *-1) / {$partyER}), {$partyDP}),  {$partyDP},
                     ret.createdUserID, ret.createdUserName, ret.createdDateTime, ret.createdDateTime, ret.createdUserID, ret.createdUserName, ret.segmentID, ret.segmentCode, ret.companyID, ret.companyCode, ret.createdUserGroup, ret.createdPCID,
                     ret.createdUserID, ret.createdDateTime, ret.createdUserName
                     FROM srp_erp_pos_salesreturndetails det JOIN srp_erp_pos_salesreturn ret ON ret.salesReturnID = det.salesReturnID
                     WHERE det.salesReturnID ={$salesReturnID} GROUP BY expenseGLAutoID");


        /************** ASSET GL CREDIT *************/
        $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID, documentSystemCode, documentDate, documentYear, documentMonth, GLAutoID, systemGLCode, GLCode, GLDescription,
                     GLType, amount_type, transactionAmount, transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces, companyLocalAmount, companyLocalCurrency,  companyLocalExchangeRate,
                     companyLocalCurrencyDecimalPlaces,  companyReportingAmount, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,
                     partyAutoID, partySystemCode, partyName, partyCurrencyID, partyCurrency, partyExchangeRate, partyCurrencyAmount, partyCurrencyDecimalPlaces,
                     confirmedByEmpID, confirmedByName, confirmedDate, approvedDate, approvedbyEmpID, approvedbyEmpName, segmentID, segmentCode, companyID, companyCode, createdUserGroup, createdPCID,
                     createdUserID, createdDateTime, createdUserName)
                     SELECT documentCode, ret.invoiceID, documentSystemCode, salesReturnDate, DATE_FORMAT(salesReturnDate,'%Y'), DATE_FORMAT(salesReturnDate,'%m'),
                     det.assetGLAutoID, det.assetSystemGLCode, det.assetGLCode, det.assetGLDescription, det.assetGLType, 'dr',
                     ROUND( sum(wacAmount * qty), det.transactionCurrencyDecimalPlaces ), det.transactionCurrency, det.transactionExchangeRate, det.transactionCurrencyDecimalPlaces,
                     ROUND( sum( (wacAmount * qty) / det.companyLocalExchangeRate), det.companyLocalCurrencyDecimalPlaces), det.companyLocalCurrency, det.companyLocalExchangeRate, det.companyLocalCurrencyDecimalPlaces,
                     ROUND( sum( (wacAmount * qty) / det.companyReportingExchangeRate), det.companyReportingCurrencyDecimalPlaces), det.companyReportingCurrency, det.companyReportingExchangeRate, det.companyReportingCurrencyDecimalPlaces,
                     {$partyID}, '" . $partySysCode . "', '" . $partyName . "', {$partyCurrencyID}, '" . $partyCurrency . "',  {$partyER} , ROUND( sum( (wacAmount * qty) / {$partyER}), {$partyDP}),  {$partyDP},
                     ret.createdUserID, ret.createdUserName, ret.createdDateTime, ret.createdDateTime, ret.createdUserID, ret.createdUserName, ret.segmentID, ret.segmentCode, ret.companyID, ret.companyCode, ret.createdUserGroup, ret.createdPCID,
                     ret.createdUserID, ret.createdDateTime, ret.createdUserName
                     FROM srp_erp_pos_salesreturndetails det JOIN srp_erp_pos_salesreturn ret ON ret.salesReturnID = det.salesReturnID
                     WHERE det.salesReturnID ={$salesReturnID} GROUP BY assetGLAutoID");


        /************** Revenue GL CREDIT *************/
        $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID, documentSystemCode, documentDate, documentYear, documentMonth, GLAutoID, systemGLCode, GLCode, GLDescription,
                     GLType, amount_type, transactionAmount, transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces, companyLocalAmount, companyLocalCurrency,  companyLocalExchangeRate,
                     companyLocalCurrencyDecimalPlaces,  companyReportingAmount, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,
                     partyAutoID, partySystemCode, partyName, partyCurrencyID, partyCurrency, partyExchangeRate, partyCurrencyAmount, partyCurrencyDecimalPlaces,
                     confirmedByEmpID, confirmedByName, confirmedDate, approvedDate, approvedbyEmpID, approvedbyEmpName, segmentID, segmentCode, companyID, companyCode, createdUserGroup, createdPCID,
                     createdUserID, createdDateTime, createdUserName)
                     SELECT documentCode, ret.invoiceID, documentSystemCode, salesReturnDate, DATE_FORMAT(salesReturnDate,'%Y'), DATE_FORMAT(salesReturnDate,'%m'),
                     det.revenueGLAutoID, det.revenueSystemGLCode, det.revenueGLCode, det.revenueGLDescription, det.revenueGLType, 'dr',
                     ROUND( sum(det.transactionAmount), det.transactionCurrencyDecimalPlaces ), det.transactionCurrency , det.transactionExchangeRate, det.transactionCurrencyDecimalPlaces,
                     ROUND( sum(det.companyLocalAmount), det.companyLocalCurrencyDecimalPlaces), det.companyLocalCurrency, det.companyLocalExchangeRate, det.companyLocalCurrencyDecimalPlaces,
                     ROUND( sum(det.companyReportingAmount), det.companyReportingCurrencyDecimalPlaces), det.companyReportingCurrency, det.companyReportingExchangeRate, det.companyReportingCurrencyDecimalPlaces,
                     {$partyID}, '" . $partySysCode . "', '" . $partyName . "', {$partyCurrencyID}, '" . $partyCurrency . "',  {$partyER} , ROUND( sum(det.companyLocalAmount) / {$partyER}, {$partyDP}),  {$partyDP},
                     ret.createdUserID, ret.createdUserName, ret.createdDateTime, ret.createdDateTime, ret.createdUserID, ret.createdUserName, ret.segmentID, ret.segmentCode, ret.companyID, ret.companyCode, ret.createdUserGroup, ret.createdPCID,
                     ret.createdUserID, ret.createdDateTime, ret.createdUserName
                     FROM srp_erp_pos_salesreturndetails det JOIN srp_erp_pos_salesreturn ret ON ret.salesReturnID = det.salesReturnID
                     WHERE det.salesReturnID ={$salesReturnID} GROUP BY revenueGLAutoID");


        /************** BANK / CUSTOMER GL DEBIT *************/
        $data = $this->db->query("SELECT documentCode, invoiceID, documentSystemCode, salesReturnDate, customerID, DATE_FORMAT(salesReturnDate,'%Y') e_year, DATE_FORMAT(salesReturnDate,'%m') e_month, 'cr' amountType,
                             returnMode, refundAmount, netTotal, transactionCurrency , transactionExchangeRate, transactionCurrencyDecimalPlaces,
                             ROUND(sum(netTotal / companyLocalExchangeRate), companyLocalCurrencyDecimalPlaces) localAmount, companyLocalCurrency, companyLocalExchangeRate, companyLocalCurrencyDecimalPlaces,
                             ROUND(sum(netTotal / companyReportingExchangeRate), companyReportingCurrencyDecimalPlaces) reportAmount, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,
                             createdUserGroup, createdPCID, createdUserID, createdUserName, createdDateTime, segmentID, segmentCode, companyID, companyCode
                             FROM srp_erp_pos_salesreturn
                             WHERE salesReturnID ={$salesReturnID}")->row();

        $returnMode = $data->returnMode;


        $insertArray = array(
            'documentMasterAutoID' => $data->invoiceID,
            'documentCode' => 'RET',
            'documentSystemCode' => $data->documentSystemCode,
            'documentDate' => $data->salesReturnDate,
            'documentYear' => $data->e_year,
            'documentMonth' => $data->e_month,


            'transactionCurrencyID' => '',
            'transactionCurrency' => $data->transactionCurrency,
            'transactionCurrencyDecimalPlaces' => $data->transactionCurrencyDecimalPlaces,
            'transactionExchangeRate' => $data->transactionExchangeRate,


            'companyLocalCurrencyID' => '',
            'companyLocalCurrency' => $data->companyLocalCurrency,
            'companyLocalCurrencyDecimalPlaces' => $data->companyLocalCurrencyDecimalPlaces,
            'companyLocalExchangeRate' => $data->companyLocalExchangeRate,


            'companyReportingCurrencyID' => '',
            'companyReportingCurrency' => $data->companyReportingCurrency,
            'companyReportingCurrencyDecimalPlaces' => $data->companyReportingCurrencyDecimalPlaces,
            'companyReportingExchangeRate' => $data->companyReportingExchangeRate,


            'confirmedDate' => $data->createdDateTime,
            'confirmedByEmpID' => $data->createdUserID,
            'confirmedByName' => $data->createdUserName,

            'approvedDate' => $data->createdDateTime,
            'approvedbyEmpID' => $data->createdUserID,
            'approvedbyEmpName' => $data->createdUserName,

            'partyAutoID' => $partyID,
            'partySystemCode' => $partySysCode,
            'partyName' => $partyName,
            'partyCurrencyID' => $partyCurrencyID,
            'partyCurrency' => $partyCurrency,
            'partyExchangeRate' => $partyER,
            'partyCurrencyDecimalPlaces' => $partyDP,


            'segmentID' => $data->segmentID,
            'segmentCode' => $data->segmentCode,
            'companyID' => $data->companyID,
            'companyCode' => $data->companyCode,
            'createdPCID' => $data->createdPCID,
            'createdUserID' => $data->createdUserID,
            'createdUserName' => $data->createdUserName,
            'createdUserGroup' => $data->createdUserGroup,
            'createdDateTime' => $data->createdDateTime,
        );

        $this->load->model('Pos_config_model');
        $localER = $data->companyLocalExchangeRate;
        $localDP = $data->companyLocalCurrencyDecimalPlaces;
        $repoER = $data->companyReportingExchangeRate;
        $repoDP = $data->companyReportingCurrencyDecimalPlaces;


        if ($returnMode == 1) { //exchange => return note
            $creditAmount = $data->netTotal;
            $creditNoteGLID = $this->Pos_config_model->load_posGL(2); //srp_erp_pos_paymentglconfigmaster => creditNote autoID is (2)
            $creditNoteGL = $this->db->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory')
                ->from('srp_erp_chartofaccounts')
                ->join('srp_erp_pos_paymentglconfigdetail', 'srp_erp_pos_paymentglconfigdetail.GLCode=srp_erp_chartofaccounts.GLAutoID')
                ->where('GLAutoID', $creditNoteGLID)->get()->row();

            $creditNoteData = $insertArray;
            $creditNoteData['amount_type'] = 'cr';
            $creditNoteData['GLAutoID'] = $creditNoteGL->GLAutoID;
            $creditNoteData['systemGLCode'] = $creditNoteGL->systemAccountCode;
            $creditNoteData['GLCode'] = $creditNoteGL->GLSecondaryCode;
            $creditNoteData['GLDescription'] = $creditNoteGL->GLDescription;
            $creditNoteData['GLType'] = $creditNoteGL->subCategory;
            $creditNoteData['transactionAmount'] = $creditAmount * -1;
            $creditNoteData['companyLocalAmount'] = round(($creditAmount / $localER) * -1, $localDP);
            $creditNoteData['companyReportingAmount'] = round(($creditAmount / $repoER) * -1, $repoDP);
            $creditNoteData['partyCurrencyAmount'] = round(($creditAmount / $partyER) * -1, $partyDP);

            $this->db->insert('srp_erp_generalledger', $creditNoteData);
        }

        if ($returnMode == 2) { //Refund
            $refundAmount = $data->refundAmount;
            $cashGLID = $this->Pos_config_model->load_posGL(1); //srp_erp_pos_paymentglconfigmaster => unDepositFund autoID is (1)

            $cashGL = $this->db->select('GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory')
                ->from('srp_erp_chartofaccounts')->where('GLAutoID', $cashGLID)->get()->row();

            $cashData = $insertArray;
            $cashData['amount_type'] = 'cr';
            $cashData['GLAutoID'] = $cashGL->GLAutoID;
            $cashData['systemGLCode'] = $cashGL->systemAccountCode;
            $cashData['GLCode'] = $cashGL->GLSecondaryCode;
            $cashData['GLDescription'] = $cashGL->GLDescription;
            $cashData['GLType'] = $cashGL->subCategory;
            $cashData['transactionAmount'] = $refundAmount * -1;
            $cashData['companyLocalAmount'] = round(($refundAmount / $localER) * -1, $localDP);
            $cashData['companyReportingAmount'] = round(($refundAmount / $repoER) * -1, $repoDP);
            $cashData['partyCurrencyAmount'] = round(($refundAmount / $partyER) * -1, $partyDP);

            $this->db->insert('srp_erp_generalledger', $cashData);
        }

        if ($returnMode == 3) { //credit-to-customer

            $partyGL = $partyData['partyGL'];
            $customerData = $insertArray;
            $customerData['amount_type'] = 'cr';
            $customerData['GLAutoID'] = $partyGL['receivableAutoID'];
            $customerData['systemGLCode'] = $partyGL['receivableSystemGLCode'];
            $customerData['GLCode'] = $partyGL['receivableGLAccount'];
            $customerData['GLDescription'] = $partyGL['receivableDescription'];
            $customerData['GLType'] = $partyGL['receivableType'];
            $customerData['transactionAmount'] = $data->netTotal * -1;
            $customerData['companyLocalAmount'] = round(($data->netTotal / $localER) * -1, $localDP);
            $customerData['companyReportingAmount'] = round(($data->netTotal / $repoER) * -1, $repoDP);
            $customerData['partyCurrencyAmount'] = round(($data->netTotal / $partyER) * -1, $partyDP);
            $customerData['subLedgerType'] = 3;
            $customerData['subLedgerDesc'] = 'AR';

            $this->db->insert('srp_erp_generalledger', $customerData);
        }

    }

    function get_invReturnBalanceQty($invNo, $invDetID, $itemID)
    {
        return $this->db->query("SELECT wacAmount, ( t1.qty -
                                  (SELECT  IFNULL(SUM(qty),0) FROM srp_erp_pos_salesreturndetails WHERE itemAutoID={$itemID} AND invoiceID={$invNo} AND
                                  invoiceDetailID={$invDetID} )
                                 ) balanceQty
                                 FROM srp_erp_pos_invoicedetail t1 WHERE  invoiceDetailsID={$invDetID}")->row();
    }

    function getReturnCode()
    {
        $query = $this->db->select('serialNo')->from('srp_erp_pos_salesreturn')->where('companyID', $this->common_data['company_data']['company_id'])
            ->order_by('salesReturnID', 'desc')->get();
        $lastRefArray = $query->row_array();
        $lastRefNo = $lastRefArray['serialNo'];
        $lastRefNo = ($lastRefNo == null) ? 1 : $lastRefArray['serialNo'] + 1;

        $this->load->library('sequence');
        $refCode = $this->sequence->sequence_generator('RET', $lastRefNo);

        return array('refCode' => $refCode, 'lastRefNo' => $lastRefNo);
    }

    function recall_hold_invoice()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $wareHouse = $this->common_data['ware_houseID'];
        $recall_search = $this->input->post('recall_search');
        if ($recall_search) {
            $where = 'AND documentSystemCode="' . $recall_search . '"';
        } else {
            $where = '';
        }
        $result = $this->db->query("SELECT t1.*, if(customerID=0 , 'Cash',
                                  (SELECT customerName FROM srp_erp_customermaster WHERE  customerAutoID=t1.customerID)) AS cusName
                                 FROM srp_erp_pos_invoicehold t1 WHERE companyID={$companyID} AND wareHouseAutoID={$wareHouse} AND isInvoiced = 0 $where ")->result_array();
        return $result;
        //echo $this->db->last_query();
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

    function invReturn_details($returnID)
    {
        $companyID = current_companyID();
        $wareHouse = $this->common_data['ware_houseID'];

        $where = array('companyID' => $companyID, 'wareHouseAutoID' => $wareHouse, 't1.salesReturnID' => $returnID);


        $isExistInv = $this->db->select("t1.*, if(customerID=0 , 'Cash',
                                  (SELECT customerName FROM srp_erp_customermaster WHERE  customerAutoID=t1.customerID)) AS cusName,
                                  (SELECT documentSystemCode FROM srp_erp_pos_invoice WHERE  invoiceID=t1.invoiceID) AS invCode,
                                  (SELECT EmpShortCode FROM srp_employeesdetails WHERE EIdNo = t1.createdUserID) AS repName")
            ->from("srp_erp_pos_salesreturn t1")
            ->where($where)->get()->row_array();

        if ($isExistInv != null) {
            $invItems = $this->db->select('*')
                ->from('srp_erp_pos_salesreturndetails t1')->where('salesReturnID', $returnID)
                ->get()->result_array();


            return array(
                0 => 's',
                1 => $isExistInv,
                2 => $invItems
            );
        } else {
            return array('w', 'There is not a return in this number');
        }
    }

    /*Counter*/
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

    function get_counterData($counterID)
    {
        return $this->db->select('counterID, counterCode, counterName')->from('srp_erp_pos_counters')
            ->where('counterID', $counterID)->where('isActive', 1)
            ->get()->row_array();
    }

    function load_wareHouseUsers($wareHouse)
    {

        // CONCAT(IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '')) eName
        return $this->db->select("autoID, userID, Ecode, Ename2 AS eName ")
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
        $companyID = $this->common_data['company_data']['company_id'];
        $where = "(Ename1 LIKE '%$keyword%' OR Ename2 LIKE '%$keyword%' OR Ename3 LIKE '%$keyword%' OR Ename4 LIKE '%$keyword%' OR ECode LIKE '%$keyword%')  ";
        $where .= "AND t1.Erp_companyID='$companyID'";
        $where .= " AND EIdNo NOT IN(
                      SELECT userID FROM srp_erp_warehouse_users AS userTB
                      JOIN srp_employeesdetails AS empTB ON empTB.EIdNo=userTB.userID AND Erp_companyID='$companyID'
                      WHERE userTB.isActive = 1 GROUP BY userID AND companyID='$companyID'
                   )";

        //CONCAT(IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '')) empName
        $this->db->select("EIdNo, ECode, Ename2 AS empName");
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
                   (SELECT wareHouseLocation FROM srp_erp_warehousemaster WHERE wareHouseAutoID=t1.wareHouseID) AS wareHouse")
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

    function get_pos_shift()
    {
        $shiftID = $this->db->select('shiftID')->from('srp_erp_pos_shiftdetails')
            ->where('wareHouseID', current_warehouseID())->where('empID', current_userID())
            ->where('isClosed', 0)->get()->row('shiftID');


        return $shiftID;
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
                    item.menuSalesID = " . $menusSalesID . "
                GROUP BY
                    revenueGLAutoID";
        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_posr_sales($shiftID)
    {
        $q = "SELECT
                'POSR' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                CURDATE() AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                'POS Sales' AS documentNarration,
                '' AS chequeNumber,
                item.revenueGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                sum(item.menuSalesPrice) AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                -- AS companyLocalExchangeRate,
                -- AS companyLocalAmount,
                -- AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                -- AS companyReportingExchangeRate,
                -- AS companyReportingAmount,
                -- AS companyReportingCurrencyDecimalPlaces,
                -- AS confirmedByEmpID,
                -- AS confirmedByName,
                -- AS confirmedDate,
                -- AS approvedDate,
                -- AS approvedbyEmpID,
                -- AS approvedbyEmpName,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                SUM(item.salesPriceNetTotal) AS totalGL,
                chartOfAccount.systemAccountCode,
                chartOfAccount.GLSecondaryCode,
                chartOfAccount.GLDescription,
                chartOfAccount.subCategory,
                menusalesmaster.shiftID
            FROM
                srp_erp_pos_menusalesitems item
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.revenueGLAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "'
            GROUP BY
                revenueGLAutoID";
        $q = "SELECT
                'POSR' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                CURDATE() AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                'POS Sales' AS documentNarration,
                '' AS chequeNumber,
                item.revenueGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                sum(item.menuSalesPrice) AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate(menusalesmaster.transactionCurrencyID , company.company_default_currencyID ,menusalesmaster.companyID ) AS companyLocalExchangeRate,
                sum(item.menuSalesPrice)/(getExchangeRate(menusalesmaster.transactionCurrencyID , company.company_default_currencyID ,menusalesmaster.companyID ))  AS companyLocalAmount,
                getDecimalPlaces(company.company_default_currencyID) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate(menusalesmaster.transactionCurrencyID , company.company_reporting_currencyID ,menusalesmaster.companyID) AS companyReportingExchangeRate,
                sum(item.menuSalesPrice)/(getExchangeRate(menusalesmaster.transactionCurrencyID , company.company_reporting_currencyID ,menusalesmaster.companyID )) AS companyReportingAmount,
                getDecimalPlaces(company.company_reporting_currencyID) AS companyReportingCurrencyDecimalPlaces,
                 
                
                -- AS confirmedByEmpID,
                -- AS confirmedByName,
                -- AS confirmedDate,
                -- AS approvedDate,
                -- AS approvedbyEmpID,
                -- AS approvedbyEmpName,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                --'' AS createdUserGroup,
                --'' AS createdPCID,
                --'' AS createdUserID,
                --NOW() AS createdDateTime,
                --'' AS createdUserName,
                --'' AS modifiedPCID,
                --'' AS modifiedUserID,
                --NULL AS modifiedDateTime,
                --'' AS modifiedUserName,
                --NOW() AS `timestamp`,
                SUM(item.salesPriceNetTotal) AS totalGL,
                chartOfAccount.systemAccountCode,
                chartOfAccount.GLSecondaryCode,
                chartOfAccount.GLDescription,
                chartOfAccount.subCategory,
                menusalesmaster.shiftID
            FROM
                srp_erp_pos_menusalesitems item
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.revenueGLAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            WHERE
                menusalesmaster.shiftID =  '" . $shiftID . "'
            GROUP BY
                revenueGLAutoID";
        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function insert_batch_srp_erp_generalledger($data)
    {
        $result = $this->db->insert_batch('srp_erp_generalledger', $data);
        return $result;
    }

    function update_usergroup_isactive()
    {

        $data['isActive'] = ($this->input->post('chkedvalue'));

        $this->db->where('userGroupMasterID', $this->input->post('userGroupMasterID'));
        $result = $this->db->update('srp_erp_pos_auth_usergroupmaster', $data);
        if ($result) {
            return array('error' => 0, 'message' => 'successfully updated', 'result' => $result);
        }
    }

    function save_userGroup()
    {
        $description = $this->input->post('description');
        $companyID = current_companyID();
        $q = "SELECT
                    description
                FROM
                    srp_erp_pos_auth_usergroupmaster
                WHERE
                   description = '" . $description . "' AND companyID = $companyID ";
        $result = $this->db->query($q)->row_array();


        if ($result) {
            return array('e', 'User Group Exist');
        } else {
            $data = array(
                'description' => $description,

                'companyID' => $this->common_data['company_data']['company_id'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdDateTime' => current_date()
            );

            $this->db->insert('srp_erp_pos_auth_usergroupmaster', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'User Group Created Successfully.');
            } else {
                return array('e', 'Error In User Group Creation');
            }
        }

    }

    function update_userGroup()
    {
        $userGroupMasterID = $this->input->post('userGroupMasterID');
        $description = $this->input->post('description');
        $companyID = current_companyID();
        $q = "SELECT
                    description
                FROM
                    srp_erp_pos_auth_usergroupmaster
                WHERE
                   description = '" . $description . "' AND userGroupMasterID != $userGroupMasterID AND companyID = $companyID ";
        $result = $this->db->query($q)->row_array();


        if ($result) {
            return array('e', 'User Group Exist');
        } else {
            $data = array(
                'description' => $description,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $this->common_data['current_userID'],
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => $this->common_data['current_date']
            );
            $this->db->where('userGroupMasterID', $userGroupMasterID)->update('srp_erp_pos_auth_usergroupmaster', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'User Group Updated Successfully.');
            } else {
                return array('e', 'Error In Updating User Group');
            }
        }

    }

    function save_usergroup_users()
    {
        $empID = $this->input->post('empID');
        $userGroupMasterID = $this->input->post('userGroupMasterID');
        $companyID = current_companyID();

        $data['pos_userGroupMasterID'] = null;
        $this->db->where('Erp_companyID', $companyID);
        $this->db->where('pos_userGroupMasterID', $userGroupMasterID);
        $this->db->update('srp_employeesdetails', $data);
        $result = true;
        if ($empID) {
            foreach ($empID as $val) {
                $datas['pos_userGroupMasterID'] = $userGroupMasterID;
                $this->db->where('EIdNo', $val);
                $result = $this->db->update('srp_employeesdetails', $datas);
            }
        }

        if ($result) {
            return array('s', 'Employees successfully added to User Group.');
        } else {
            return array('e', 'Error In adding Employees to User Group.');
        }

    }

    function fetch_assigned_users()
    {
        $result = $this->db->select('*')->from('srp_employeesdetails')->where("pos_userGroupMasterID", $this->input->post("userGroupMasterID"))->where("Erp_companyID", current_companyID())->get()->result_array();
        return $result;
    }

    function getInvoiceSequenceCode()
    {
        /*$WarehouseID = current_warehouseID();

        $querys = $this->db->select('wareHouseCode')->from('srp_erp_warehousemaster')->where('wareHouseAutoID', $WarehouseID)->get();
        $WarehouseCode = $querys->row_array();

        $query = $this->db->select('invoiceSequenceNo')->from('srp_erp_pos_invoice')->where('companyID', $this->common_data['company_data']['company_id'])->where('wareHouseAutoID', $WarehouseID)
            ->order_by('invoiceID', 'desc')->get();
        $lastRefArray = $query->row_array();
        $lastINVNo = $lastRefArray['invoiceSequenceNo'];
        $lastINVNo = ($lastINVNo == null) ? 1 : $lastRefArray['invoiceSequenceNo'] + 1;
        $companyID=current_companyID();


        $this->load->library('sequence');
        $sequenceCode = $this->sequence->sequence_generator($WarehouseCode['wareHouseCode'], $lastINVNo);
        return array('sequenceCode' => $sequenceCode, 'lastINVNo' => $lastINVNo);*/

        $WarehouseID = current_warehouseID();

        $querys = $this->db->select('wareHouseCode')->from('srp_erp_warehousemaster')->where('wareHouseAutoID', $WarehouseID)->get();
        $WarehouseCode = $querys->row_array();
        $code = $WarehouseCode['wareHouseCode'];

        $query = $this->db->select('invoiceSequenceNo')->from('srp_erp_pos_invoice')->where('companyID', $this->common_data['company_data']['company_id'])->where('wareHouseAutoID', $WarehouseID)
            ->order_by('invoiceID', 'desc')->get();
        $lastRefArray = $query->row_array();
        $lastINVNo = $lastRefArray['invoiceSequenceNo'];
        $lastINVNo = ($lastINVNo == null) ? 1 : $lastRefArray['invoiceSequenceNo'] + 1;
        $companyID = current_companyID();
        $queryscomp = $this->db->select('company_code')->from('srp_erp_company')->where('company_id', $companyID)->get();
        $compCode = $queryscomp->row_array();
        $company_code = $compCode['company_code'];

        $sequenceCode['sequenceCode'] = ($company_code . '/' . $code . str_pad($lastINVNo, 6, '0', STR_PAD_LEFT));
        $sequenceCode['lastINVNo'] = $lastINVNo;
        return $sequenceCode;
    }


    function submit_pos_payments()
    {

        $totalPayment = $this->input->post('paid');
        $totVal = $this->input->post('totVal');
        $discVal = $this->input->post('discVal');
        $netTotalAmount = $this->input->post('total_payable_amt');
        $customerID = $this->input->post('customerID');
        $cardTotalAmount = $this->input->post('cardTotalAmount');
        $CreditSalesAmnt = $this->input->post('CreditSalesAmnt');
        if (!empty($this->input->post('paymentTypes[26]'))) {
            $CreditSalesAmnt = $this->input->post('paymentTypes[26]');
        }
        $CreditNoteAmnt = $this->input->post('paymentTypes[2]');
        $creditNote_invID = $this->input->post('creditNote-invID');
        $cash[1] = $this->input->post('paymentTypes[1]');
        $MasterCard[3] = $this->input->post('paymentTypes[3]');
        $VisaCard[4] = $this->input->post('paymentTypes[4]');
        $AMEX[6] = $this->input->post('paymentTypes[6]');
        $CreditSales[7] = $this->input->post('paymentTypes[7]');
        $CreditNote[2] = $this->input->post('paymentTypes[2]');

        $item = $this->input->post('itemID[]');
        $itemUOM = $this->input->post('itemUOM[]');
        $itemQty = $this->input->post('itemQty[]');
        $itemPrice = $this->input->post('itemPrice[]');
        $itemDis = $this->input->post('itemDis[]');

        $invCodeDet = $this->getInvoiceCode();
        $lastRefNo = $invCodeDet['lastRefNo'];
        $refCode = $invCodeDet['refCode'];

        $invSequenceCodeDet = $this->getInvoiceSequenceCode();
        $lastINVNo = $invSequenceCodeDet['lastINVNo'];
        $sequenceCode = $invSequenceCodeDet['sequenceCode'];

        $financeYear = $this->db->select('companyFinanceYearID, beginingDate, endingDate')->from('srp_erp_companyfinanceyear')
            ->where(
                array(
                    'isActive' => 1,
                    'isCurrent' => 1,
                    'companyID' => current_companyID()
                )
            )->get()->row_array();


        $financePeriod = $this->db->select('companyFinancePeriodID, dateFrom, dateTo')->from('srp_erp_companyfinanceperiod')
            ->where(
                array(
                    'isActive' => 1,
                    'isCurrent' => 1,
                    'companyID' => current_companyID()
                )
            )->get()->row_array();
        $invoiceDate = format_date(date('Y-m-d'));
        $currentShiftData = $this->isHaveNotClosedSession();
        $com_currency = $this->common_data['company_data']['company_default_currency'];
        $tr_currency = $this->common_data['company_data']['company_default_currency'];
        $localConversion = currency_conversion($tr_currency, $com_currency, $netTotalAmount);
        $localConversionRate = $localConversion['conversion'];
        $com_currDPlace = $this->common_data['company_data']['company_default_decimal'];
        $rep_currency = $this->common_data['company_data']['company_reporting_currency'];
        $rep_currDPlace = $this->common_data['company_data']['company_reporting_decimal'];

        $transConversion = currency_conversion($tr_currency, $tr_currency, $netTotalAmount);
        $tr_currDPlace = $transConversion['DecimalPlaces'];
        $transConversionRate = $transConversion['conversion'];
        $reportConversion = currency_conversion($tr_currency, $rep_currency, $netTotalAmount);
        $reportConversionRate = $reportConversion['conversion'];
        $wareHouseData = $this->get_wareHouse();
        $isStockCheck = 0;
        $invArray = array(
            'documentSystemCode' => $refCode,
            'documentCode' => 'POS',
            'serialNo' => $lastRefNo,
            'invoiceSequenceNo' => $lastINVNo,
            'invoiceCode' => $sequenceCode,
            'financialYearID' => $financeYear['companyFinanceYearID'],
            'financialPeriodID' => $financePeriod['companyFinancePeriodID'],
            'FYBegin' => $financeYear['beginingDate'],
            'FYEnd' => $financeYear['endingDate'],
            'FYPeriodDateFrom' => $financePeriod['dateFrom'],
            'FYPeriodDateTo' => $financePeriod['dateTo'],
            'customerID' => $customerID,
            /*'customerCode' => $customerCode,*/
            'invoiceDate' => $invoiceDate,
            'counterID' => $currentShiftData['counterID'],
            'shiftID' => $currentShiftData['shiftID'],
            'generalDiscountPercentage' => $this->input->post('gen_disc_percentage'),
            'generalDiscountAmount' => $this->input->post('gen_disc_amount_hide'),
            'subTotal' => $totVal,
            'netTotal' => $netTotalAmount,
            'paidAmount' => $totalPayment,
            'balanceAmount' => ($netTotalAmount - $totalPayment),
            'cashAmount' => $totalPayment - $cardTotalAmount,
            /*'chequeAmount' => $chequeAmount,*/
            'cardAmount' => $cardTotalAmount,
            'discountAmount' => $discVal,
            'creditNoteID' => $creditNote_invID,
            'creditNoteAmount' => $CreditNoteAmnt,
            'creditSalesAmount' => $CreditSalesAmnt,
            'isCreditSales' => $this->input->post('isCreditSale'),


            'memberID' => $this->input->post('memberidhn'),
            'memberName' => $this->input->post('membernamehn'),
            'memberContactNo' => $this->input->post('contactnumberhn'),
            'memberEmail' => $this->input->post('mailaddresshn'),
            /*'chequeNo' => $chequeNO,
            'chequeDate' => $chequeCashDate,*/
            'companyLocalCurrencyID' => $localConversion['currencyID'],
            'companyLocalCurrency' => $com_currency,
            'companyLocalCurrencyDecimalPlaces' => $com_currDPlace,
            'companyLocalExchangeRate' => $localConversionRate,
            'transactionCurrencyID' => $localConversion['trCurrencyID'],
            'transactionCurrency' => $tr_currency,
            'transactionCurrencyDecimalPlaces' => $tr_currDPlace,
            'transactionExchangeRate' => $transConversionRate,
            'companyReportingCurrencyID' => $reportConversion['currencyID'],
            'companyReportingCurrency' => $rep_currency,
            'companyReportingCurrencyDecimalPlaces' => $rep_currDPlace,
            'companyReportingExchangeRate' => $reportConversionRate,
            'wareHouseAutoID' => $wareHouseData['wareHouseAutoID'],
            'wareHouseCode' => $wareHouseData['wareHouseCode'],
            'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
            'wareHouseDescription' => $wareHouseData['wareHouseDescription'],
            'segmentID' => $wareHouseData['segmentID'],
            'segmentCode' => $wareHouseData['segmentCode'],
            'companyID' => $this->common_data['company_data']['company_id'],
            'companyCode' => $this->common_data['company_data']['company_code'],
            'createdPCID' => $this->common_data['current_pc'],
            'createdUserID' => $this->common_data['current_userID'],
            'createdUserName' => $this->common_data['current_user'],
            'createdUserGroup' => $this->common_data['user_group'],
            'createdDateTime' => current_date(),
        );
        if ($CreditNoteAmnt > 0 || $CreditSalesAmnt > 0) {
            $invArray['cardAmount'] = 0;
        }
        $invArray['cardRefNo'] = 0;
        $invArray['cardBank'] = 0;
        $invArray['cardNumber'] = 0;

        if (!empty($this->input->post('memberidhn'))) {
            $invArray['creditSalesAmount'] = $totalPayment - $cardTotalAmount;
            $invArray['isCreditSales'] = 1;
            $invArray['cashAmount'] = 0;
        }

        if ($customerID == 0) {
            $bankData = $this->db->query("SELECT receivableAutoID, receivableSystemGLCode, receivableGLAccount,
                                          receivableDescription, receivableType
                                          FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();
            $invArray['bankGLAutoID'] = $bankData['receivableAutoID'];
            $invArray['bankSystemGLCode'] = $bankData['receivableSystemGLCode'];
            $invArray['bankGLAccount'] = $bankData['receivableGLAccount'];
            $invArray['bankGLDescription'] = $bankData['receivableDescription'];
            $invArray['bankGLType'] = $bankData['receivableType'];

            /*************** item ledger party currency ***********/
            $partyData = array(
                'cusID' => 0,
                'sysCode' => 'CASH',
                'cusName' => 'CASH',
                'partyCurID' => $localConversion['trCurrencyID'],
                'partyCurrency' => $tr_currency,
                'partyDPlaces' => $tr_currDPlace,
                'partyER' => $transConversionRate,
            );

        } else {

            $cusData = $this->db->query("SELECT customerAutoID, customerSystemCode, customerName, receivableAutoID,
                                             receivableSystemGLCode, receivableGLAccount, receivableDescription, receivableType,
                                             customerCurrencyID, customerCurrency, customerCurrencyDecimalPlaces,customerAddress1,customerTelephone
                                             FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();

            $partyData = currency_conversion($tr_currency, $cusData['customerCurrency']);

            $invArray['customerCurrencyID'] = $cusData['customerCurrencyID'];
            $invArray['customerCurrency'] = $cusData['customerCurrency'];
            $invArray['customerCurrencyExchangeRate'] = $partyData['conversion'];
            $invArray['customerCurrencyDecimalPlaces'] = $cusData['customerCurrencyDecimalPlaces'];

            $invArray['customerReceivableAutoID'] = $cusData['receivableAutoID'];
            $invArray['customerReceivableSystemGLCode'] = $cusData['receivableSystemGLCode'];
            $invArray['customerReceivableGLAccount'] = $cusData['receivableGLAccount'];
            $invArray['customerReceivableDescription'] = $cusData['receivableDescription'];
            $invArray['customerReceivableType'] = $cusData['receivableType'];

            /*************** item ledger party currency ***********/

            $partyData = array(
                'cusID' => $cusData['customerAutoID'],
                'sysCode' => $cusData['customerSystemCode'],
                'cusName' => $cusData['customerName'],
                'partyCurID' => $cusData['customerCurrencyID'],
                'partyCurrency' => $cusData['customerCurrency'],
                'partyDPlaces' => $cusData['customerCurrencyDecimalPlaces'],
                'partyER' => $partyData['conversion'],
                'partyGL' => $cusData,
            );
        }

        $this->db->trans_start();
        $this->db->insert('srp_erp_pos_invoice', $invArray);
        $invID = $this->db->insert_id();
        $paymentTypes = $this->input->post('paymentTypes');
        foreach ($paymentTypes as $key => $paymentType) {
            if ($paymentType > 0) {
                $wareHouseID = $this->common_data['ware_houseID'];
                $paymentglconfigmaster = $this->db->query("SELECT * FROM srp_erp_pos_paymentglconfigmaster WHERE autoID={$key}")->row_array();
                $paymentglconfigdetail = $this->db->query("SELECT * FROM srp_erp_pos_paymentglconfigdetail WHERE paymentConfigMasterID={$key} AND warehouseID={$wareHouseID}")->row_array();
                if ($key == 7 || $key == 26) {
                    $cusid = $customerID;
                } else {
                    $cusid = 0;
                }

                if ($key == 1) {
                    $paymentType = $netTotalAmount - ($totalPayment - $invArray['cashAmount']);
                }

                if ($paymentType > 0) {

                    $invPaymentARR = array(
                        'invoiceID' => $invID,
                        'paymentConfigMasterID' => $key,
                        'paymentConfigDetailID' => $paymentglconfigdetail['ID'],
                        'glAccountType' => $paymentglconfigmaster['glAccountType'],
                        'GLCode' => $paymentglconfigdetail['GLCode'],
                        'amount' => $paymentType,
                        'reference' => $this->input->post('reference[' . $key . ']'),
                        'customerAutoID' => $cusid,
                        'createdPCID' => $this->common_data['current_pc'],
                        'createdUserID' => $this->common_data['current_userID'],
                        'createdUserName' => $this->common_data['current_user'],
                        'createdUserGroup' => $this->common_data['user_group'],
                        'createdDateTime' => current_date(),
                    );
                    $this->db->insert('srp_erp_pos_invoicepayments', $invPaymentARR);
                }
            }
        }

        /*Load wac library*/
        $this->load->library('Wac');
        $this->load->library('sequence');


        if ($CreditSalesAmnt != 0) {
            $cusData = $this->db->query("SELECT customerAutoID, customerSystemCode, customerName, receivableAutoID,
                                             receivableSystemGLCode, receivableGLAccount, receivableDescription, receivableType,
                                             customerCurrencyID, customerCurrency, customerCurrencyDecimalPlaces,customerAddress1,customerTelephone
                                             FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();

            $data_customer_invoice['invoiceType'] = 'Direct';
            $data_customer_invoice['documentID'] = 'CINV';
            $data_customer_invoice['posTypeID'] = 1;
            $data_customer_invoice['referenceNo'] = $sequenceCode;
            if (!empty($this->input->post('memberidhn'))) {
                $memberid = $this->input->post('memberidhn');
                $membername = $this->input->post('membernamehn');
                $data_customer_invoice['invoiceNarration'] = 'POS Credit Sales - ' . $sequenceCode . '-' . $memberid . '-' . $membername;
            } else {
                $data_customer_invoice['invoiceNarration'] = 'POS Credit Sales - ' . $sequenceCode;
            }
            $data_customer_invoice['posMasterAutoID'] = $invID;
            $data_customer_invoice['invoiceDate'] = current_date();
            $data_customer_invoice['invoiceDueDate'] = current_date();
            $data_customer_invoice['customerInvoiceDate'] = current_date();
            $data_customer_invoice['invoiceCode'] = $this->sequence->sequence_generator($data_customer_invoice['documentID']);
            $customerInvoiceCode = $data_customer_invoice['invoiceCode'];
            $data_customer_invoice['companyFinanceYearID'] = $this->common_data['company_data']['companyFinanceYearID'];
            $financialYear = get_financial_from_to($this->common_data['company_data']['companyFinanceYearID']);
            $data_customer_invoice['companyFinanceYear'] = trim($financialYear['beginingDate']) . ' - ' . trim($financialYear['endingDate']);
            $data_customer_invoice['FYBegin'] = trim($financialYear['beginingDate']);
            $data_customer_invoice['FYEnd'] = trim($financialYear['endingDate']);
            $data_customer_invoice['FYPeriodDateFrom'] = trim($this->common_data['company_data']['FYPeriodDateFrom']);
            $data_customer_invoice['FYPeriodDateTo'] = trim($this->common_data['company_data']['FYPeriodDateTo']);
            $data_customer_invoice['companyFinancePeriodID'] = $this->common_data['company_data']['companyFinancePeriodID'];
            $data_customer_invoice['customerID'] = $customerID;
            $data_customer_invoice['customerSystemCode'] = $cusData['customerSystemCode'];
            $data_customer_invoice['customerName'] = $cusData['customerName'];
            $data_customer_invoice['customerAddress'] = $cusData['customerAddress1'];
            $data_customer_invoice['customerTelephone'] = $cusData['customerTelephone'];
            $data_customer_invoice['customerFax'] = $cusData['customerTelephone'];
            $data_customer_invoice['customerEmail'] = $cusData['customerTelephone'];
            $data_customer_invoice['customerReceivableAutoID'] = $cusData['receivableAutoID'];
            $data_customer_invoice['customerReceivableSystemGLCode'] = $cusData['receivableSystemGLCode'];
            $data_customer_invoice['customerReceivableGLAccount'] = $cusData['receivableGLAccount'];
            $data_customer_invoice['customerReceivableDescription'] = $cusData['receivableDescription'];
            $data_customer_invoice['customerReceivableType'] = $cusData['receivableType'];
            $data_customer_invoice['customerCurrency'] = $cusData['customerCurrency'];
            $data_customer_invoice['customerCurrencyID'] = $cusData['customerCurrencyID'];
            $data_customer_invoice['customerCurrencyDecimalPlaces'] = $cusData['customerCurrencyDecimalPlaces'];

            $data_customer_invoice['confirmedYN'] = 1;
            $data_customer_invoice['confirmedByEmpID'] = current_userID();
            $data_customer_invoice['confirmedByName'] = current_user();
            $data_customer_invoice['confirmedDate'] = current_date();
            $data_customer_invoice['approvedYN'] = 1;
            $data_customer_invoice['approvedDate'] = current_date();
            $data_customer_invoice['currentLevelNo'] = 1;
            $data_customer_invoice['approvedbyEmpID'] = current_userID();
            $data_customer_invoice['approvedbyEmpName'] = current_user();

            $data_customer_invoice['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data_customer_invoice['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data_customer_invoice['transactionExchangeRate'] = 1;
            $data_customer_invoice['transactionAmount'] = $netTotalAmount;
            $data_customer_invoice['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data_customer_invoice['transactionCurrencyID']);
            $data_customer_invoice['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data_customer_invoice['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $default_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['companyLocalCurrencyID']);
            $data_customer_invoice['companyLocalExchangeRate'] = $default_currency['conversion'];
            $data_customer_invoice['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $data_customer_invoice['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $data_customer_invoice['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $reporting_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['companyReportingCurrencyID']);
            $data_customer_invoice['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data_customer_invoice['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $customer_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['customerCurrencyID']);
            $data_customer_invoice['customerCurrencyExchangeRate'] = $customer_currency['conversion'];
            $data_customer_invoice['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
            $data_customer_invoice['companyCode'] = $this->common_data['company_data']['company_code'];
            $data_customer_invoice['companyID'] = $this->common_data['company_data']['company_id'];
            $data_customer_invoice['createdUserGroup'] = $this->common_data['user_group'];
            $data_customer_invoice['createdPCID'] = $this->common_data['current_pc'];
            $data_customer_invoice['createdUserID'] = $this->common_data['current_userID'];
            $data_customer_invoice['createdUserName'] = $this->common_data['current_user'];
            $data_customer_invoice['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_customerinvoicemaster', $data_customer_invoice);
            $customerInvoiceMasterID = $this->db->insert_id();

            if ($customerInvoiceMasterID) {
                $data_cusinv['documentSystemCode'] = $data_customer_invoice['invoiceCode'];
                $this->db->where('invoiceID', $invID);
                $this->db->update('srp_erp_pos_invoice', $data_cusinv);

                $doc_approved['departmentID'] = "CINV";
                $doc_approved['documentID'] = "CINV";
                $doc_approved['documentCode'] = $data_customer_invoice['invoiceCode'];
                $doc_approved['documentSystemCode'] = $customerInvoiceMasterID;
                $doc_approved['documentDate'] = current_date();
                $doc_approved['approvalLevelID'] = 1;
                $doc_approved['docConfirmedDate'] = current_date();
                $doc_approved['docConfirmedByEmpID'] = current_userID();
                $doc_approved['table_name'] = 'srp_erp_customerinvoicemaster';
                $doc_approved['table_unique_field_name'] = 'invoiceAutoID';
                $doc_approved['approvedEmpID'] = current_userID();
                $doc_approved['approvedYN'] = 1;
                $doc_approved['approvedComments'] = 'Approved from POS';
                $doc_approved['approvedPC'] = current_pc();
                $doc_approved['approvedDate'] = current_date();
                $doc_approved['companyID'] = current_companyID();
                $doc_approved['companyCode'] = current_company_code();
                $this->db->insert('srp_erp_documentapproved', $doc_approved);

                $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,segmentID,segmentCode,transactionCurrency,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces');
                $this->db->from('srp_erp_customerinvoicemaster');
                $this->db->where('invoiceAutoID', $customerInvoiceMasterID);
                $master = $this->db->get()->row_array();

                $data_customer_invoice_detail['invoiceAutoID'] = $customerInvoiceMasterID;
                $data_customer_invoice_detail['type'] = 'GL';
                if (!empty($this->input->post('memberidhn'))) {
                    $memberid = $this->input->post('memberidhn');
                    $membername = $this->input->post('membernamehn');
                    $data_customer_invoice_detail['description'] = 'POS Sales - ' . $sequenceCode . '-' . $memberid . '-' . $membername;
                } else {
                    $data_customer_invoice_detail['description'] = 'POS Sales - ' . $sequenceCode;
                }
                $data_customer_invoice_detail['transactionAmount'] = round($netTotalAmount, $master['transactionCurrencyDecimalPlaces']);
                $companyLocalAmount = $data_customer_invoice_detail['transactionAmount'] / $master['companyLocalExchangeRate'];
                $data_customer_invoice_detail['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                $companyReportingAmount = $data_customer_invoice_detail['transactionAmount'] / $master['companyReportingExchangeRate'];
                $data_customer_invoice_detail['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                $customerAmount = $data_customer_invoice_detail['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                $data_customer_invoice_detail['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

                $data_customer_invoice_detail['companyCode'] = $this->common_data['company_data']['company_code'];
                $data_customer_invoice_detail['companyID'] = $this->common_data['company_data']['company_id'];
                $data_customer_invoice_detail['createdUserGroup'] = $this->common_data['user_group'];
                $data_customer_invoice_detail['createdPCID'] = $this->common_data['current_pc'];
                $data_customer_invoice_detail['createdUserID'] = $this->common_data['current_userID'];
                $data_customer_invoice_detail['createdUserName'] = $this->common_data['current_user'];
                $data_customer_invoice_detail['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_customerinvoicedetails', $data_customer_invoice_detail);

            }
        }
        $i = 0;
        $item_ledger_arr = array();
        $dataInt = array();
        foreach ($item as $itemID) {
            $itemData = fetch_ware_house_item_data($itemID);
            $conversion = conversionRateUOM($itemUOM[$i], $itemData['defaultUnitOfMeasure']);
            $conversion = ($conversion == 0) ? 1 : $conversion;
            $conversionRate = 1 / $conversion;
            $availableQTY = $itemData['wareHouseQty'];
            $qty = $itemQty[$i] * $conversionRate;

            if ($availableQTY < $qty && $isStockCheck == 1) {
                $this->db->trans_rollback();
                return array('e', '[ ' . $itemData['itemSystemCode'] . ' - ' . $itemData['itemDescription'] . ' ]<p> is available only ' . $availableQTY . ' qty');
                break;
            }

            $price = str_replace(',', '', $itemPrice[$i]);
            $itemTotal = $itemQty[$i] * $price;
            $itemTotal = ($itemDis[$i] > 0) ? ($itemTotal - ($itemTotal * 0.01 * $itemDis[$i])) : $itemTotal;
            $itemTotal = round($itemTotal, $tr_currDPlace);

            $dataInt[$i]['invoiceID'] = $invID;
            $dataInt[$i]['itemAutoID'] = $itemID;
            $dataInt[$i]['itemSystemCode'] = $itemData['itemSystemCode'];
            $dataInt[$i]['itemDescription'] = $itemData['itemDescription'];
            $dataInt[$i]['defaultUOM'] = $itemData['defaultUnitOfMeasure'];
            $dataInt[$i]['unitOfMeasure'] = $itemUOM[$i];
            $dataInt[$i]['conversionRateUOM'] = $conversion;
            $dataInt[$i]['qty'] = $itemQty[$i];
            $dataInt[$i]['price'] = $price;
            $dataInt[$i]['discountPer'] = $itemDis[$i];
            if ($itemDis[$i] > 0) {
                $discountAmount = ($price * 0.01 * $itemDis[$i]);
            } else {
                $discountAmount = 0;
            }
            $dataInt[$i]['discountAmount'] = $discountAmount;


            $gen_disc_percentage = $this->input->post('gen_disc_percentage');
            if ($gen_disc_percentage > 0) {
                $gen_discountAmount = ($price - $discountAmount) * 0.01 * $gen_disc_percentage * $itemQty[$i];
            } else {
                $gen_discountAmount = 0;
            }

            $dataInt[$i]['generalDiscountPercentage'] = $gen_disc_percentage;
            $dataInt[$i]['generalDiscountAmount'] = $gen_discountAmount;

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


            $dataInt[$i]['transactionAmountBeforeDiscount'] = $itemTotal;
            $itemTotal = $itemTotal - $gen_discountAmount;
            $dataInt[$i]['transactionAmount'] = $itemTotal;
            $dataInt[$i]['transactionExchangeRate'] = $transConversionRate;
            $dataInt[$i]['transactionCurrency'] = $tr_currency;
            $dataInt[$i]['transactionCurrencyID'] = $localConversion['trCurrencyID'];
            $dataInt[$i]['transactionCurrencyDecimalPlaces'] = $tr_currDPlace;
            $dataInt[$i]['companyLocalAmount'] = round(($itemTotal / $localConversionRate), $com_currDPlace);

            $dataInt[$i]['companyLocalExchangeRate'] = $localConversionRate;
            $dataInt[$i]['companyLocalCurrency'] = $com_currency;
            $dataInt[$i]['companyLocalCurrencyID'] = $localConversion['currencyID'];
            $dataInt[$i]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

            $dataInt[$i]['companyReportingAmount'] = round(($itemTotal / $reportConversionRate), $rep_currDPlace);
            $dataInt[$i]['companyReportingExchangeRate'] = $reportConversionRate;
            $dataInt[$i]['companyReportingCurrency'] = $rep_currency;
            $dataInt[$i]['companyReportingCurrencyID'] = $reportConversion['currencyID'];
            $dataInt[$i]['companyReportingCurrencyDecimalPlaces'] = $rep_currDPlace;

            $dataInt[$i]['companyID'] = $this->common_data['company_data']['company_id'];
            $dataInt[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
            $dataInt[$i]['createdPCID'] = $this->common_data['current_pc'];
            $dataInt[$i]['createdUserID'] = $this->common_data['current_userID'];
            $dataInt[$i]['createdUserName'] = $this->common_data['current_user'];
            $dataInt[$i]['createdUserGroup'] = $this->common_data['user_group'];
            $dataInt[$i]['createdDateTime'] = current_date();

            /*$balanceQty = $availableQTY - $qty;
            $itemUpdateWhere = array('itemAutoID' => $itemID, 'wareHouseAutoID' => $this->common_data['ware_houseID']);
            $itemUpdateQty = array('currentStock' => $balanceQty);
            $this->db->where($itemUpdateWhere)->update('srp_erp_warehouseitems', $itemUpdateQty);*/

            $wacData = $this->wac->wac_calculation(1, $itemID, $qty, '', $this->common_data['ware_houseID']);

            if ($CreditSalesAmnt > 0) {
                $item_ledger_arr[$i] = $this->item_ledger_customerInvoice($financeYear, $financePeriod, $customerInvoiceCode, $dataInt[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData);
            } else {
                $item_ledger_arr[$i] = $this->item_ledger($financeYear, $financePeriod, $refCode, $dataInt[$i], $itemData['companyReportingWacAmount'], $wacData, $wareHouseData, $partyData);
            }

            $i++;
        }
        //echo '<pre>';print_r($dataInt);echo '</pre>'; exit;
        $this->db->insert_batch('srp_erp_pos_invoicedetail', $dataInt);
        $isInvoiced = $this->input->post('isInvoiced');
        if (!empty($isInvoiced)) {
            $holdinv['isInvoiced'] = 1;
            $this->db->where('invoiceID', $isInvoiced);
            $this->db->update('srp_erp_pos_invoicehold', $holdinv);
        }
        $this->db->insert_batch('srp_erp_itemledger', $item_ledger_arr);
        $this->double_entry($invID, $partyData, $CreditSalesAmnt);

        $this->db->trans_complete();
        if ($this->db->trans_status() == false) {
            $this->db->trans_rollback();
            return array('e', 'Error in Invoice Create');
        } else {
            $this->db->trans_commit();
            return array('s', 'Invoice Code : ' . $sequenceCode . ' ', $invID, $refCode, $sequenceCode);
        }

    }

    function creditNote_load()
    {
        $key = $this->input->post('key');
        $companyID = current_companyID();
        return $this->db->select('salesReturnID, documentSystemCode, salesReturnDate, netTotal')
            ->from('srp_erp_pos_salesreturn t1')
            ->where(" companyID={$companyID}
                        AND NOT EXISTS (
                            SELECT * FROM srp_erp_pos_invoice WHERE creditNoteID = t1.salesReturnID
                        )
                    ")->get()->result_array();
    }

    function save_customer()
    {
        $this->db->trans_start();
        $isactive = 1;

        $companyData = get_companyInfo();
        $controlAccount = get_companyControlAccounts('ARA'); /*Account Receivable Control Account*/

        //$liability = fetch_gl_account_desc(trim($this->input->post('receivableAccount')));
        $currency_code = explode('|', trim($this->input->post('currency_code')));
        $country = explode('|', trim($this->input->post('country')));
        $data['isActive'] = $isactive;
        $data['secondaryCode'] = trim($this->input->post('customercode'));
        $data['customerName'] = trim($this->input->post('customerName'));
        $data['customerCountry'] = $companyData['company_country']; //$country[0];
        $data['customerTelephone'] = trim($this->input->post('customerTelephone'));
        $data['customerEmail'] = trim($this->input->post('customerEmail'));
        $data['customerUrl'] = ''; //trim($this->input->post('customerUrl'));
        $data['customerFax'] = ''; //trim($this->input->post('customerFax'));
        $data['customerAddress1'] = ''; //trim($this->input->post('customerAddress1'));
        $data['customerAddress2'] = ''; //trim($this->input->post('customerAddress2'));
        $data['taxGroupID'] = ''; //trim($this->input->post('customertaxgroup'));
        $data['partyCategoryID'] = ''; // trim($this->input->post('partyCategoryID'));
        $data['receivableAutoID'] = $controlAccount['GLAutoID']; // $liability['GLAutoID'];
        $data['receivableSystemGLCode'] = $controlAccount['systemAccountCode'];
        $data['receivableGLAccount'] = $controlAccount['GLSecondaryCode'];
        $data['receivableDescription'] = $controlAccount['GLDescription'];
        $data['receivableType'] = $controlAccount['subCategory'];
        $data['customerCreditPeriod'] = ''; //trim($this->input->post('customerCreditPeriod'));
        $data['customerCreditLimit'] = ''; //trim($this->input->post('customerCreditLimit'));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $this->load->library('sequence');
        $data['customerCurrencyID'] = trim($this->input->post('customerCurrency'));
        $data['customerCurrency'] = $currency_code[0];
        $data['customerCurrencyDecimalPlaces'] = fetch_currency_desimal($data['customerCurrency']);
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['customerSystemCode'] = $this->sequence->sequence_generator('CUS');
        $this->db->insert('srp_erp_customermaster', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Customer : ' . $data['customerName'] . ' Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Customer : ' . $data['customerName'] . ' Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }
    }


}

