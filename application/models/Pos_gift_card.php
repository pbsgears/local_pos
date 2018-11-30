<?php
/**
 *
 * -- =============================================
 * -- File Name : Pos_config.php
 * -- Project Name : POS
 * -- Module Name : POS Config model
 * -- Author : Mohamed Shafri
 * -- Create date : 13 October 2016
 * -- Description : database script related to pos config.
 *
 * --REVISION HISTORY
 * --Date: 13-Oct 2016 By: Mohamed Shafri: file created
 * -- =============================================
 **/
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pos_gift_card extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function save_giftCardMaster()
    {
        $cardMasterID = $this->input->post("cardMasterID");
        $barcode = $this->input->post('barcode');
        if ($cardMasterID) {
            /** UPDATE */
            $validate = $this->validateCardBarCodeExist($barcode, $cardMasterID);
            if ($validate) {
                $data['outletID'] = $this->input->post('outletID');
                $data['barcode'] = $barcode;
                $data['cardExpiryInMonths'] = $this->input->post('cardExpiryInMonths');
                $data['modifiedPCID'] = current_pc();
                $data['modifiedUserID'] = current_userID();
                $data['modifiedDateTime'] = format_date_mysql_datetime();
                $data['modifiedUserName'] = current_user();

                $this->db->where('cardMasterID', $cardMasterID);
                $result = $this->db->update('srp_erp_pos_giftcardmaster', $data);
                //echo $this->db->last_query();

                if ($result) {
                    return array('error' => 0, 'message' => 'Card Successfully Updated.');
                } else {
                    return array('error' => 1, 'message' => "Error while updating" . $this->db->_error_message());
                }
            } else {
                return array('error' => 1, 'message' => "This barcode (" . $barcode . ') already added in the system');
            }

        } else {
            /** INSERT */
            $validate = $this->validateCardBarCodeExist($barcode, $cardMasterID);
            if ($validate) {

                $data['outletID'] = $this->input->post('outletID');
                $data['barcode'] = $barcode;
                $data['cardExpiryInMonths'] = $this->input->post('cardExpiryInMonths');
                $data['companyID'] = current_companyID();
                $data['companyCode'] = current_companyCode();
                $data['createdPCID'] = current_pc();
                $data['createdUserID'] = current_userID();
                $data['createdDateTime'] = format_date_mysql_datetime();
                $data['createdUserName'] = current_user();
                $data['createdUserGroup'] = user_group();
                $data['timestamp'] = format_date_mysql_datetime();

                $result = $this->db->insert('srp_erp_pos_giftcardmaster', $data);
                if ($result) {
                    return array('error' => 0, 'message' => 'Card Successfully saved.');
                } else {
                    return array('error' => 1, 'message' => "Error while inserting" . $this->db->_error_message());
                }
            } else {
                return array('error' => 1, 'message' => "This barcode (" . $barcode . ') is already in the system');
            }
        }
    }

    function validateCardBarCodeExist($barcode, $cardMasterID)
    {
        $this->db->select('barcode');
        $this->db->from('srp_erp_pos_giftcardmaster');
        $this->db->where('barcode', $barcode);
        $this->db->where('companyID', current_companyID());
        $this->db->where_not_in('cardMasterID', $cardMasterID);
        $result = $this->db->get()->row('barcode');
        //echo $this->db->last_query();
        if ($result) {
            return false;
        } else {
            return true;
        }
    }

    function loadCardIssueData($barCode)
    {
        $q = "SELECT cardIssueID, cardMasterID, expiryDate, srp_erp_pos_customermaster.* FROM srp_erp_pos_cardissue
            LEFT JOIN srp_erp_pos_customermaster ON srp_erp_pos_customermaster.posCustomerAutoID = srp_erp_pos_cardissue.posCustomerAutoID
            WHERE barCode = '" . $barCode . "'";
        $customerInfo = $this->db->query($q)->row_array();
        return $customerInfo;
    }

    function get_giftCardBalanceAmount($barCode)
    {
        $q = "SELECT SUM(topUpAmount) AS topUpAmount FROM srp_erp_pos_cardtopup WHERE barCode =  '" . $barCode . "'";
        $topUpAmountArray = $this->db->query($q)->row_array();
        $topUpAmount = !empty($topUpAmountArray) && $topUpAmountArray['topUpAmount'] > 0 ? $topUpAmountArray['topUpAmount'] : 0;
        return $topUpAmount;
    }

    function get_giftCardDatetime($dataTime, $format = 'd')
    {
        $timestamp = strtotime($dataTime);
        if ($format == 'd') {
            return date('d/m/Y', $timestamp);
        } else if ($format == 't') {
            return date('h:i A', $timestamp);
        }
    }


    function insert_double_entries_giftCard($id)
    {

        $cardGLAutoID = $this->get_cardGlAutoID();
        if ($cardGLAutoID > 0) {


            $this->db->select('*');
            $this->db->from('srp_erp_pos_cardtopup');
            $this->db->where('cardTopUpID', $id);
            $isCreditSale = $this->db->get()->row('isCreditSale');
            if ($isCreditSale) {
                /** Credit sales entry */
                $invoiceCode = $this->sequence->sequence_generator('CINV');

                /** CINV  create customer invoice master */
                $q = "INSERT INTO srp_erp_customerinvoicemaster (
                wareHouseAutoID,
                invoiceType,
                documentID,
                invoiceDate,
                invoiceDueDate,
                customerInvoiceDate,
                invoiceCode,
                referenceNo,
                invoiceNarration,
                companyFinanceYearID,
                companyFinanceYear,
                FYBegin,
                FYEnd,
                companyFinancePeriodID,
                customerID,
                customerSystemCode,
                customerName,
                customerReceivableAutoID,
                customerReceivableSystemGLCode,
                customerReceivableGLAccount,
                customerReceivableDescription,
                customerReceivableType,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                customerCurrencyID,
                customerCurrency,
                customerCurrencyExchangeRate,
                customerCurrencyAmount,
                customerCurrencyDecimalPlaces,
                confirmedYN,
                confirmedByEmpID,
                confirmedByName,
                confirmedDate,
                approvedYN,
                approvedDate,
                approvedbyEmpID,
                approvedbyEmpName,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp` 
                ) (
                SELECT
                    srp_erp_pos_cardtopup.wareHouseAutoID as wareHouseAutoID,
                    'Direct' AS invoiceType,
                    'CINV' AS documentID,
                    DATE_FORMAT( srp_erp_pos_cardtopup.createdDateTime, \"%Y-%m-%d\" ) AS invoiceDate,
                    DATE_FORMAT( srp_erp_pos_cardtopup.createdDateTime, \"%Y-%m-%d\" ) AS invoiceDueDate,
                    DATE_FORMAT( srp_erp_pos_cardtopup.createdDateTime, \"%Y-%m-%d\" ) AS customerInvoiceDate,
                    '" . $invoiceCode . "' AS invoiceCode,
                    srp_erp_pos_cardtopup.cardTopUpID AS referenceNo,
                    concat( 'Gift Card Credit Sales - ', srp_erp_pos_cardtopup.cardTopUpID ) AS invoiceNarration,
                    getCompanyFinanceYearID ( srp_erp_pos_cardtopup.companyID ) AS companyFinanceYearID,
                    concat( FY.beginingDate, ' - ', FY.endingDate ) AS companyFinanceYear,
                    FY.beginingDate AS FYBegin,
                    FY.endingDate AS FYEnd,
                    getCompanyFinancePeriodID ( srp_erp_pos_cardtopup.companyID ) AS companyFinancePeriodID,
                    srp_erp_pos_cardtopup.creditSalesCustomerID AS customerID,
                    srp_erp_customermaster.customerSystemCode AS customerSystemCode,
                    srp_erp_customermaster.customerName AS customerName,
                    srp_erp_customermaster.receivableAutoID AS customerReceivableAutoID,
                    srp_erp_customermaster.receivableSystemGLCode AS customerReceivableSystemGLCode,
                    srp_erp_customermaster.receivableGLAccount AS customerReceivableGLAccount,
                    srp_erp_customermaster.receivableDescription AS customerReceivableDescription,
                    srp_erp_customermaster.receivableType AS customerReceivableType,
                    srp_erp_company.company_default_currencyID AS transactionCurrencyID,
                    srp_erp_company.company_default_currency AS transactionCurrency,
                    1 AS transactionExchangeRate,
                    srp_erp_pos_cardtopup.topUpAmount AS transactionAmount,
                    srp_erp_company.company_default_decimal AS transactionCurrencyDecimalPlaces,
                    srp_erp_company.company_default_currencyID AS companyLocalCurrencyID,
                    srp_erp_company.company_default_currency AS companyLocalCurrency,
                    1 AS companyLocalExchangeRate,
                    srp_erp_pos_cardtopup.topUpAmount AS companyLocalAmount,
                    srp_erp_company.company_default_decimal AS companyLocalCurrencyDecimalPlaces,
                    srp_erp_company.company_reporting_currencyID AS companyReportingCurrencyID,
                    srp_erp_company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate ( srp_erp_company.company_default_currencyID, srp_erp_company.company_reporting_currencyID, srp_erp_company.company_id ) AS companyReportingExchangeRate,
                    srp_erp_pos_cardtopup.topUpAmount * getExchangeRate ( srp_erp_company.company_default_currencyID, srp_erp_company.company_reporting_currencyID, srp_erp_company.company_id ) AS companyReportingAmount,
                    srp_erp_company.company_reporting_decimal AS companyReportingCurrencyDecimalPlaces,
                    srp_erp_company.company_default_currencyID AS customerCurrencyID,
                    srp_erp_company.company_default_currency AS customerCurrency,
                    1 AS customerCurrencyExchangeRate,
                    srp_erp_pos_cardtopup.topUpAmount AS customerCurrencyAmount,
                    srp_erp_company.company_default_decimal AS customerCurrencyDecimalPlaces,
                    1 AS confirmedYN,
                    srp_erp_pos_cardtopup.createdUserID AS confirmedByEmpID,
                    srp_erp_pos_cardtopup.createdUserName AS confirmedByName,
                    srp_erp_pos_cardtopup.createdDateTime AS confirmedDate,
                    1 AS approvedYN,
                    srp_erp_pos_cardtopup.createdDateTime AS approvedDate,
                    srp_erp_pos_cardtopup.createdUserID AS approvedbyEmpID,
                    srp_erp_pos_cardtopup.createdUserName AS approvedbyEmpName,
                    SUBSTRING_INDEX( srp_erp_company.default_segment, '|', 1 ) AS segmentID,
                    SUBSTRING_INDEX( srp_erp_company.default_segment, '|', - 1 ) AS segmentCode,
                    srp_erp_pos_cardtopup.companyID AS companyID,
                    srp_erp_pos_cardtopup.companyCode AS companyCode,
                    srp_erp_pos_cardtopup.createdUserGroup AS createdUserGroup,
                    srp_erp_pos_cardtopup.createdPCID AS createdPCID,
                    srp_erp_pos_cardtopup.createdUserID AS createdUserID,
                    srp_erp_pos_cardtopup.createdDateTime AS createdDateTime,
                    srp_erp_pos_cardtopup.createdUserName AS createdUserName,
                    srp_erp_pos_cardtopup.`timestamp` AS `timestamp` 
                FROM
                    srp_erp_pos_cardtopup
                    LEFT JOIN srp_erp_companyfinanceyear FY ON FY.beginingDate < CURDATE( ) AND FY.endingDate > CURDATE( ) 
                    AND FY.companyID = srp_erp_pos_cardtopup.companyID
                    LEFT JOIN srp_erp_customermaster ON srp_erp_pos_cardtopup.creditSalesCustomerID = srp_erp_customermaster.customerAutoID
                    LEFT JOIN srp_erp_company ON srp_erp_pos_cardtopup.companyID = srp_erp_company.company_id 
                WHERE
                    cardTopUpID = '" . $id . "'  AND  isCreditSale = 1
                    )";
                $this->db->query($q);
                $insert_id = $this->db->insert_id();

                /** CINV  create customer invoice detail */
                $q = "INSERT INTO srp_erp_customerinvoicedetails (
                        invoiceAutoID,
                        `type`,
                        description,
                        transactionAmount,
                        companyLocalAmount,
                        companyReportingAmount,
                        customerAmount,
                        revenueGLAutoID,
                        revenueGLCode,
                        revenueSystemGLCode,
                        revenueGLDescription,
                        revenueGLType,
                        segmentID,
                        segmentCode,
                        companyID,
                        companyCode,
                        createdUserGroup,
                        createdPCID,
                        createdUserID,
                        createdDateTime,
                        createdUserName,
                        `timestamp` 
                        ) (
                        SELECT
                            srp_erp_customerinvoicemaster.invoiceAutoID,
                            'GL' AS `type`,
                            concat( 'Gift Card Credit Sales - ', srp_erp_customerinvoicemaster.referenceNo ) AS description,
                            srp_erp_customerinvoicemaster.transactionAmount,
                            srp_erp_customerinvoicemaster.companyLocalAmount,
                            srp_erp_customerinvoicemaster.companyReportingAmount,
                            srp_erp_customerinvoicemaster.customerCurrencyAmount,
                            chartOfAccount.GLAutoID AS revenueGLAutoID,
                            chartOfAccount.GLSecondaryCode AS revenueGLCode,
                            chartOfAccount.systemAccountCode AS revenueSystemGLCode,
                            chartOfAccount.GLDescription AS revenueGLDescription,
                            chartOfAccount.subCategory AS revenueGLType,
                            srp_erp_customerinvoicemaster.segmentID,
                            srp_erp_customerinvoicemaster.segmentCode,
                            srp_erp_customerinvoicemaster.companyID,
                            srp_erp_customerinvoicemaster.companyCode,
                            srp_erp_customerinvoicemaster.createdUserGroup,
                            srp_erp_customerinvoicemaster.createdPCID,
                            srp_erp_customerinvoicemaster.createdUserID,
                            srp_erp_customerinvoicemaster.createdDateTime,
                            srp_erp_customerinvoicemaster.createdUserName,
                            srp_erp_customerinvoicemaster.`timestamp` 
                        FROM
                            srp_erp_customerinvoicemaster
                            LEFT JOIN srp_erp_companycontrolaccounts AS controlAccount ON controlAccount.controlAccountType = 'GC' 
                            AND controlAccount.companyID = srp_erp_customerinvoicemaster.companyID
                            LEFT JOIN srp_erp_chartofaccounts AS chartOfAccount ON controlAccount.GLAutoID = chartOfAccount.GLAutoID 
                        WHERE
                            srp_erp_customerinvoicemaster.invoiceAutoID =  '" . $insert_id . "' 
                            )";

                $this->db->query($q);

                /** CINV  create customer documented approved record for created invoice */
                $q2 = "INSERT INTO srp_erp_documentapproved (
                    `departmentID`,
                    `documentID`,
                    `documentSystemCode`,
                    `documentCode`,
                    `documentDate`,
                    `approvalLevelID`,
                    `roleID`,
                    `approvalGroupID`,
                    `roleLevelOrder`,
                    `docConfirmedDate`,
                    `docConfirmedByEmpID`,
                    `table_name`,
                    `table_unique_field_name`,
                    `approvedEmpID`,
                    `approvedYN`,
                    `approvedDate`,
                    `approvedComments`,
                    `approvedPC`,
                    `companyID`,
                    `companyCode`,
                    `timeStamp` 
                    ) (
                    SELECT
                        'CINV' as `departmentID`,
                        srp_erp_customerinvoicemaster.documentID as `documentID`,
                        srp_erp_customerinvoicemaster.invoiceAutoID as `documentSystemCode`,
                        srp_erp_customerinvoicemaster.invoiceCode as `documentCode`,
                        srp_erp_customerinvoicemaster.invoiceDate as `documentDate`,
                        1 as `approvalLevelID `,
                        1 as `roleID`,
                        0 as `approvalGroupID`,
                        1 as `roleLevelOrder`,
                        srp_erp_customerinvoicemaster.invoiceDate as `docConfirmedDate`,
                        srp_erp_customerinvoicemaster.createdUserID as `docConfirmedByEmpID`,
                        'srp_erp_customerinvoicemaster' as `table_name`,
                        'invoiceAutoID' as `table_unique_field_name`,
                        srp_erp_customerinvoicemaster.createdUserID as `approvedEmpID`,
                        1 as `approvedYN`,
                        srp_erp_customerinvoicemaster.createdDateTime as `approvedDate`,
                        'Approved from POS' as `approvedComments`,
                        srp_erp_customerinvoicemaster.createdPCID as `approvedPC`,
                        srp_erp_customerinvoicemaster.companyID as `companyID`,
                        srp_erp_customerinvoicemaster.companyCode as `companyCode`,
                        srp_erp_customerinvoicemaster.createdDateTime as `timeStamp`
                    FROM
                        srp_erp_customerinvoicemaster 
                        WHERE 
                    srp_erp_customerinvoicemaster.invoiceAutoID = '" . $insert_id . "')";
                $this->db->query($q2);


                /** GL Entries */
                $query_db = "INSERT INTO srp_erp_generalledger (
                        documentCode,
                        documentMasterAutoID,
                        documentSystemCode,
                        documentDate,
                        documentYear,
                        documentMonth,
                        documentNarration,
                        chequeNumber,
                        GLAutoID,
                        systemGLCode,
                        GLCode,
                        GLDescription,
                        GLType,
                        amount_type,
                        isFromItem,
                        transactionCurrencyID,
                        transactionCurrency,
                        transactionExchangeRate,
                        transactionAmount,
                        transactionCurrencyDecimalPlaces,
                        companyLocalCurrencyID,
                        companyLocalCurrency,
                        companyLocalExchangeRate,
                        companyLocalAmount,
                        companyLocalCurrencyDecimalPlaces,
                        companyReportingCurrencyID,
                        companyReportingCurrency,
                        companyReportingExchangeRate,
                        companyReportingAmount,
                        companyReportingCurrencyDecimalPlaces,
                        partyType,
                        partyAutoID,
                        partySystemCode,
                        partyName,
                        partyCurrencyID,
                        partyCurrency,
                        partyExchangeRate,
                        partyCurrencyAmount,
                        partyCurrencyDecimalPlaces,
                        subLedgerType,
                        subLedgerDesc,
                        segmentID,
                        segmentCode,
                        companyID,
                        companyCode,
                        createdUserGroup,
                        createdPCID,
                        createdUserID,
                        createdDateTime,
                        createdUserName,
                        modifiedPCID,
                        modifiedUserID,
                        modifiedDateTime,
                        modifiedUserName,
                        `timestamp` 
                        ) (
                        SELECT
                            'CINV' AS documentCode,
                            '" . $insert_id . "' AS documentMasterAutoID,
                            '" . $invoiceCode . "' AS documentSystemCode,
                            DATE_FORMAT( srp_erp_pos_cardtopup.createdDateTime, '%Y-%m-%d' ) AS documentdate,
                            YEAR ( srp_erp_pos_cardtopup.createdDateTime ) AS documentYear,
                            MONTH ( srp_erp_pos_cardtopup.createdDateTime ) AS documentMonth,
                            concat( 'Gift Card Credit Sales', ' - ', srp_erp_pos_cardtopup.cardTopUpID ) AS documentNarration,
                            '' AS chequeNumber,
                            chartOfAccount.GLAutoID AS GLAutoID,
                            chartOfAccount.systemAccountCode AS systemGLCode,
                            chartOfAccount.GLSecondaryCode AS GLCode,
                            chartOfAccount.GLDescription AS GLDescription,
                            chartOfAccount.subCategory AS GLType,
                            'dr' AS amount_type,
                            '0' AS isFromItem,
                            srp_erp_company.company_default_currencyID AS transactionCurrencyID,
                            srp_erp_company.company_default_currency AS transactionCurrency,
                            '1' AS transactionExchangeRate,
                            srp_erp_pos_cardtopup.topUpAmount AS transactionAmount,
                            srp_erp_company.company_default_decimal AS transactionCurrencyDecimalPlaces,
                            srp_erp_company.company_default_currencyID AS companyLocalCurrencyID,
                            srp_erp_company.company_default_currency AS companyLocalCurrency,
                            1 AS companyLocalExchangeRate,
                            srp_erp_pos_cardtopup.topUpAmount AS companyLocalAmount,
                            srp_erp_company.company_default_decimal AS companyLocalCurrencyDecimalPlaces,
                            srp_erp_company.company_reporting_currencyID AS companyReportingCurrencyID,
                            srp_erp_company.company_reporting_currency AS companyReportingCurrency,
                            getExchangeRate ( srp_erp_company.company_default_currencyID, srp_erp_company.company_reporting_currencyID, srp_erp_company.company_id ) AS companyReportingExchangeRate,
                            srp_erp_pos_cardtopup.topUpAmount / getExchangeRate ( srp_erp_company.company_default_currencyID, srp_erp_company.company_reporting_currencyID, srp_erp_company.company_id ) AS companyReportingAmount,
                            srp_erp_company.company_reporting_decimal AS companyReportingCurrencyDecimalPlaces,
                            'CUS' AS partyType,
                            srp_erp_pos_cardtopup.creditSalesCustomerID AS partyAutoID,
                            srp_erp_customermaster.customerSystemCode AS partySystemCode,
                            srp_erp_customermaster.customerName AS partyName,
                            srp_erp_customermaster.customerCurrencyID AS partyCurrencyID,
                            srp_erp_customermaster.customerCurrency AS partyCurrency,
                            getExchangeRate ( srp_erp_customermaster.customerCurrencyID, srp_erp_company.company_default_currencyID, srp_erp_pos_cardtopup.companyID ) AS partyExchangeRate,
                            SUM( srp_erp_pos_cardtopup.topUpAmount ) / ( getExchangeRate ( srp_erp_customermaster.customerCurrencyID, srp_erp_company.company_reporting_currencyID, srp_erp_pos_cardtopup.companyID ) ) AS partyCurrencyAmount,
                            srp_erp_customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                            3 AS subLedgerType,
                            'AR' AS subLedgerDesc,
                            SUBSTRING_INDEX( srp_erp_company.default_segment, '|', 1 ) AS segmentID,
                            SUBSTRING_INDEX( srp_erp_company.default_segment, '|', - 1 ) AS segmentCode,
                            srp_erp_pos_cardtopup.companyID AS companyID,
                            srp_erp_pos_cardtopup.companyCode AS companyCode,
                            srp_erp_pos_cardtopup.createdUserGroup AS createdUserGroup,
                            srp_erp_pos_cardtopup.createdPCID AS createdPCID,
                            srp_erp_pos_cardtopup.createdUserID AS createdUserID,
                            srp_erp_pos_cardtopup.createdDateTime AS createdDateTime,
                            srp_erp_pos_cardtopup.createdUserName AS createdUserName,
                            NULL AS modifiedPCID,
                            NULL AS modifiedUserID,
                            NULL AS modifiedDateTime,
                            NULL AS modifiedUserName,
                            CURRENT_TIMESTAMP ( ) `timestamp` 
                        FROM
                            srp_erp_pos_cardtopup
                            LEFT JOIN srp_erp_companyfinanceyear FY ON FY.beginingDate < CURDATE( ) AND FY.endingDate > CURDATE( ) 
                            AND FY.companyID = srp_erp_pos_cardtopup.companyID
                            LEFT JOIN srp_erp_customermaster ON srp_erp_pos_cardtopup.creditSalesCustomerID = srp_erp_customermaster.customerAutoID
                            LEFT JOIN srp_erp_company ON srp_erp_pos_cardtopup.companyID = srp_erp_company.company_id
                            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = srp_erp_customermaster.receivableAutoID 
                            LEFT JOIN srp_erp_customerinvoicemaster CINV ON CINV.invoiceAutoID = '" . $insert_id . "'
                        WHERE
                            cardTopUpID = '" . $id . "' AND isCreditSale = 1
                            )";
                $this->db->query($query_db);

                $query_cr = "INSERT INTO srp_erp_generalledger (
                        documentCode,
                        documentMasterAutoID,
                        documentSystemCode,
                        documentDate,
                        documentYear,
                        documentMonth,
                        documentNarration,
                        chequeNumber,
                        GLAutoID,
                        systemGLCode,
                        GLCode,
                        GLDescription,
                        GLType,
                        amount_type,
                        isFromItem,
                        transactionCurrencyID,
                        transactionCurrency,
                        transactionExchangeRate,
                        transactionAmount,
                        transactionCurrencyDecimalPlaces,
                        companyLocalCurrencyID,
                        companyLocalCurrency,
                        companyLocalExchangeRate,
                        companyLocalAmount,
                        companyLocalCurrencyDecimalPlaces,
                        companyReportingCurrencyID,
                        companyReportingCurrency,
                        companyReportingExchangeRate,
                        companyReportingAmount,
                        companyReportingCurrencyDecimalPlaces,
                        segmentID,
                        segmentCode,
                        companyID,
                        companyCode,
                        createdUserGroup,
                        createdPCID,
                        createdUserID,
                        createdDateTime,
                        createdUserName,
                        modifiedPCID,
                        modifiedUserID,
                        modifiedDateTime,
                        modifiedUserName,
                        `timestamp`
                    )(
                        SELECT
                        'CINV' AS documentCode,
                        '" . $insert_id . "' AS documentMasterAutoID,
                        '" . $invoiceCode . "' AS documentSystemCode,
                        CURDATE( ) AS documentdate,
                        YEAR ( curdate( ) ) AS documentYear,
                        MONTH ( curdate( ) ) AS documentMonth,
                        concat( 'Gift Card Credit Sales', ' - ', cardTopUp.cardTopUpID ) AS documentNarration,
                        '' AS chequeNumber,
                        cardTopUp.giftCardGLAutoID AS GLAutoID,
                        chartOfAccount.systemAccountCode AS systemGLCode,
                        chartOfAccount.GLSecondaryCode AS GLCode,
                        chartOfAccount.GLDescription AS GLDescription,
                        chartOfAccount.subCategory AS GLType,
                        'cr' AS amount_type,
                        '0' AS isFromItem,
                        company.company_default_currencyID AS transactionCurrencyID,
                        company.company_default_currency AS transactionCurrency,
                        '1' AS transactionExchangeRate,
                        abs( SUM( cardTopUp.topUpAmount ) ) *- 1 AS transactionAmount,
                        company.company_default_decimal AS transactionCurrencyDecimalPlaces,
                        company.company_default_currencyID AS companyLocalCurrencyID,
                        company.company_default_currency AS companyLocalCurrency,
                        1 AS companyLocalExchangeRate,
                        abs( SUM( cardTopUp.topUpAmount ) ) *- 1 AS companyLocalAmount,
                        getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
                        company.company_reporting_currencyID AS companyReportingCurrencyID,
                        company.company_reporting_currency AS companyReportingCurrency,
                        getExchangeRate ( company.company_default_currencyID, company.company_reporting_currencyID, cardTopUp.companyID ) AS companyReportingExchangeRate,
                        ( abs( SUM( cardTopUp.topUpAmount ) ) * - 1 ) / ( getExchangeRate ( company.company_default_currencyID, company.company_reporting_currencyID, cardTopUp.companyID ) ) AS companyReportingAmount,
                        getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                        SUBSTRING_INDEX( company.default_segment, '|', 1 ) AS segmentID,
                        SUBSTRING_INDEX( company.default_segment, '|', - 1 ) AS segmentCode,
                        cardTopUp.companyID AS companyID,
                        cardTopUp.companyCode AS companyCode,
                        cardTopUp.createdUserGroup AS createdUserGroup,
                        cardTopUp.createdPCID AS createdPCID,
                        cardTopUp.createdUserID createdUserID,
                        CURRENT_TIMESTAMP ( ) createdDateTime,
                        cardTopUp.createdUserName createdUserName,
                        NULL AS modifiedPCID,
                        NULL AS modifiedUserID,
                        NULL AS modifiedDateTime,
                        NULL AS modifiedUserName,
                        CURRENT_TIMESTAMP ( ) `timestamp` 
                    FROM
                        srp_erp_pos_cardtopup AS cardTopUp
                        LEFT JOIN srp_erp_companyfinanceyear FY ON FY.beginingDate < CURDATE( ) AND FY.endingDate > CURDATE( ) 
                        AND FY.companyID = cardTopUp.companyID
                        LEFT JOIN srp_erp_customermaster ON cardTopUp.creditSalesCustomerID = srp_erp_customermaster.customerAutoID
                        LEFT JOIN srp_erp_company AS company ON cardTopUp.companyID = company.company_id
                        -- LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = srp_erp_customermaster.receivableAutoID 
                        LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = cardTopUp.giftCardGLAutoID
                    WHERE
                       cardTopUp.cardTopUpID = '" . $id . "' 
                       AND cardTopUp.isCreditSale = 1
                    )";
                $this->db->query($query_cr);
            } else {
                $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`
            )(
                SELECT
                    'POSR_GC' AS documentCode,
                    cardTopUp.cardTopUpID AS documentMasterAutoID,
                    concat(
                        'POSR_GC/',
                        cardTopUp.cardTopUpID
                    ) AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS GiftCard issued' AS documentNarration,
                    '' AS chequeNumber,
                    glConfigDetail.GLCode AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    company.company_default_currencyID AS transactionCurrencyID,
                    company.company_default_currency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    SUM(cardTopUp.topUpAmount) AS transactionAmount,
                    company.company_default_decimal AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    1 AS companyLocalExchangeRate,
                    SUM(cardTopUp.topUpAmount) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        company.company_default_currencyID,
                        company.company_reporting_currencyID,
                        cardTopUp.companyID
                    ) AS companyReportingExchangeRate,
                    SUM(cardTopUp.topUpAmount) / (
                        getExchangeRate (
                            company.company_default_currencyID,
                            company.company_reporting_currencyID,
                            cardTopUp.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    segmentConfig.segmentID AS segmentID,
                    segmentConfig.segmentCode AS segmentCode,
                    cardTopUp.companyID AS companyID,
                    cardTopUp.companyCode AS companyCode,
                    cardTopUp.createdUserGroup AS createdUserGroup,
                    cardTopUp.createdPCID AS createdPCID,
                    cardTopUp.createdUserID createdUserID,
                    CURRENT_TIMESTAMP () createdDateTime,
                    cardTopUp.createdUserName createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () `timestamp`
                FROM
                    srp_erp_pos_cardtopup cardTopUp
                LEFT JOIN srp_erp_pos_paymentglconfigdetail glConfigDetail ON glConfigDetail.ID = cardTopUp.glConfigDetailID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = glConfigDetail.GLCode
                LEFT JOIN srp_erp_company company ON company.company_id = cardTopUp.companyID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = company.company_default_currencyID
                LEFT JOIN srp_erp_pos_segmentconfig segmentConfig ON segmentConfig.wareHouseAutoID = cardTopUp.outletID AND segmentConfig.isActive = -1
                WHERE
                    cardTopUp.cardTopUpID = '" . $id . "' AND cardTopUp.isCreditSale = 0
                GROUP BY
                    glConfigDetail.GLCode
            )";
                $this->db->query($q);

                $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`
            )(
                SELECT
                    'POSR_GC' AS documentCode,
                    cardTopUp.cardTopUpID AS documentMasterAutoID,
                    concat(
                        'POSR_GC/',
                        cardTopUp.cardTopUpID
                    ) AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS GiftCard issued' AS documentNarration,
                    '' AS chequeNumber,
                    cardTopUp.giftCardGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    company.company_default_currencyID AS transactionCurrencyID,
                    company.company_default_currency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(SUM(cardTopUp.topUpAmount)) *- 1 AS transactionAmount,
                    company.company_default_decimal AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    1 AS companyLocalExchangeRate,
                    abs(SUM(cardTopUp.topUpAmount)) *- 1 AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        company.company_default_currencyID,
                        company.company_reporting_currencyID,
                        cardTopUp.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        abs(SUM(cardTopUp.topUpAmount)) * - 1
                    ) / (
                        getExchangeRate (
                            company.company_default_currencyID,
                            company.company_reporting_currencyID,
                            cardTopUp.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    segmentConfig.segmentID AS segmentID,
                    segmentConfig.segmentCode AS segmentCode,
                    cardTopUp.companyID AS companyID,
                    cardTopUp.companyCode AS companyCode,
                    cardTopUp.createdUserGroup AS createdUserGroup,
                    cardTopUp.createdPCID AS createdPCID,
                    cardTopUp.createdUserID createdUserID,
                    CURRENT_TIMESTAMP () createdDateTime,
                    cardTopUp.createdUserName createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () `timestamp`
                FROM
                    srp_erp_pos_cardtopup cardTopUp
                LEFT JOIN srp_erp_pos_paymentglconfigdetail glConfigDetail ON glConfigDetail.ID = cardTopUp.glConfigDetailID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = cardTopUp.giftCardGLAutoID
                LEFT JOIN srp_erp_company company ON company.company_id = cardTopUp.companyID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = company.company_default_currencyID
                LEFT JOIN srp_erp_pos_segmentconfig segmentConfig ON segmentConfig.wareHouseAutoID = cardTopUp.outletID AND segmentConfig.isActive = -1
                WHERE
                    cardTopUp.cardTopUpID = '" . $id . "' AND cardTopUp.isCreditSale = 0
                GROUP BY
                    cardTopUp.giftCardGLAutoID
            )";
                $this->db->query($q);

                /**  Bank Ledger Entry */
                $q = "INSERT INTO srp_erp_bankledger (
                    documentDate,
                    transactionType,
                    transactionCurrencyID,
                    transactionCurrency,
                    transactionExchangeRate,
                    transactionAmount,
                    transactionCurrencyDecimalPlaces,
                    bankCurrencyID,
                    bankCurrency,
                    bankCurrencyExchangeRate,
                    bankCurrencyAmount,
                    bankCurrencyDecimalPlaces,
                    modeofPayment,
                    memo,
                    bankName,
                    bankGLAutoID,
                    bankSystemAccountCode,
                    bankGLSecondaryCode,
                    documentMasterAutoID,
                    documentType,
                    documentSystemCode,
                    createdPCID,
                    companyID,
                    companyCode,
                    createdUserID,
                    createdDateTime,
                    createdUserName,
                    `timeStamp`
                )(
                    SELECT
                        CURDATE() AS documentDate,
                        '2' AS transactionType,
                        company.company_default_currencyID AS transactionCurrencyID,
                        company.company_default_currency AS transactionCurrency,
                        '1' AS transactionExchangeRate,
                        SUM(cardTopUp.topUpAmount) AS transactionAmount,
                        company.company_default_decimal AS transactionCurrencyDecimalPlaces,
                        chartOfAccount.bankCurrencyID AS bankCurrencyID,
                        chartOfAccount.bankCurrencyCode AS bankCurrency,
                        getExchangeRate (
                            company.company_default_currencyID,
                            chartOfAccount.bankCurrencyID,
                            cardTopUp.companyID
                        ) AS bankCurrencyExchangeRate,
                        SUM(cardTopUp.topUpAmount) / (
                            getExchangeRate (
                                company.company_default_currencyID,
                                chartOfAccount.bankCurrencyID,
                                cardTopUp.companyID
                            )
                        ) AS bankCurrencyAmount,
                        chartOfAccount.bankCurrencyDecimalPlaces AS bankCurrencyDecimalPlaces,
                        '1' AS modeofPayment,
                        'POS Gift Card Amount Received' AS memo,
                        chartOfAccount.bankName AS bankName,
                        chartOfAccount.GLAutoID AS bankGLAutoID,
                        chartOfAccount.systemAccountCode AS bankSystemAccountCode,
                        chartOfAccount.GLSecondaryCode AS bankGLSecondaryCode,
                        cardTopUp.cardTopUpID AS documentMasterAutoID,
                        'RV' AS documentType,
                        concat(
                            'POSR_GC/',
                            cardTopUp.cardTopUpID
                        ) AS documentSystemCode,
                        cardTopUp.createdPCID AS createdPCID,
                        cardTopUp.companyID AS companyID,
                        cardTopUp.companyCode AS companyCode,
                        cardTopUp.createdUserID AS createdUserID,
                        CURRENT_TIMESTAMP () createdDateTime,
                        cardTopUp.createdUserName createdUserName,
                        CURRENT_TIMESTAMP () `timestamp`
                    FROM
                        srp_erp_pos_cardtopup cardTopUp
                    LEFT JOIN srp_erp_pos_paymentglconfigdetail glConfigDetail ON glConfigDetail.ID = cardTopUp.glConfigDetailID
                    LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = glConfigDetail.GLCode
                    LEFT JOIN srp_erp_company company ON company.company_id = cardTopUp.companyID
                    LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = company.company_default_currencyID
                    WHERE
                        cardTopUp.cardTopUpID = '" . $id . "' AND cardTopUp.isCreditSale = 0
                    GROUP BY
                        glConfigDetail.GLCode
                )";
                $this->db->query($q);
            }

        }

    }

    function get_cardGlAutoID()
    {
        $this->db->select('GLCode');
        $this->db->from('srp_erp_pos_paymentglconfigdetail');
        $this->db->where('companyID', current_companyID());
        $this->db->where('warehouseID', get_outletID());
        $this->db->where('paymentConfigMasterID', 5);
        $GLCode = $this->db->get()->row('GLCode');
        return $GLCode;
    }

    function get_giftCard_cardInformation($barcode)
    {
        $q = "SELECT
            srp_erp_pos_giftcardmaster.barcode,
            srp_erp_pos_customermaster.CustomerName,
            srp_erp_pos_customermaster.customerTelephone
            FROM
            srp_erp_pos_giftcardmaster
            INNER JOIN srp_erp_pos_cardissue ON srp_erp_pos_giftcardmaster.cardMasterID = srp_erp_pos_cardissue.cardMasterID
            INNER JOIN srp_erp_pos_customermaster ON srp_erp_pos_cardissue.posCustomerAutoID = srp_erp_pos_customermaster.posCustomerAutoID
            WHERE srp_erp_pos_cardissue.barCode = '" . $barcode . "'
            ";
        $r = $this->db->query($q)->row_array();
        return $r;
    }

    function get_giftCard_paymentInformation($receiptID)
    {
        $q = "SELECT
                srp_erp_pos_cardtopup.giftCardReceiptID,
                srp_erp_pos_cardtopup.cardTopUpID,
                srp_erp_pos_cardtopup.topUpAmount,
                srp_erp_pos_paymentglconfigmaster.description,
                srp_erp_pos_cardtopup.createdDateTime
                FROM
                srp_erp_pos_cardtopup
                INNER JOIN srp_erp_pos_paymentglconfigmaster ON srp_erp_pos_cardtopup.glConfigMasterID = srp_erp_pos_paymentglconfigmaster.autoID
                WHERE
                srp_erp_pos_cardtopup.giftCardReceiptID = '" . $receiptID . "'";
        $r = $this->db->query($q)->result_array();
        return $r;
    }

}