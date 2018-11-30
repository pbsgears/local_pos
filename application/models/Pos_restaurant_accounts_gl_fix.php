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
--Date: 08 - Sep 2017 By: Mohamed Shafri: Accounts Double entries corrected  (BANK LEDGER INSERT, ITEM LEDGER INSERT , ITEM MASTER update , item ledger update)

============================================================

*/

class Pos_restaurant_accounts_gl_fix extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /******* New GL Entries ****/
    /** 1. REVENUE */
    function update_revenue_generalLedger($shiftID)
    {
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
                timestamp
            ) (SELECT
                    'POSR' AS documentCode,
                 menusalesmaster.shiftID AS documentMasterAutoID,
                concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                'POS Sales - Revenue' AS documentNarration,
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
                abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                (( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID )  )) AS companyLocalAmount, getDecimalPlaces ( company.company_default_currencyID  ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID as companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                (( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID )  )) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                shiftDetail.createdUserGroup AS createdUserGroup,
            shiftDetail.createdPCID as  createdPCID,
            shiftDetail.createdUserID as  createdUserID,
            shiftDetail.startTime as createdDateTime,
            shiftDetail.createdUserName as createdUserName,
            NULL AS modifiedPCID,
            NULL AS modifiedUserID,
            NULL AS modifiedDateTime,
            null AS modifiedUserName,
            CURRENT_TIMESTAMP() as `timestamp`
            FROM
                srp_erp_pos_menusalesitems item
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.revenueGLAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
            
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "' and menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 0
            GROUP BY
                revenueGLAutoID);";

        $result = $this->db->query($q);
        //echo $this->db->last_query();
        return $result;
    }

    /** 2. BANK OR CASH */
    function update_bank_cash_generalLedger($shiftID)
    {
        $q = "INSERT INTO srp_erp_generalledger ( documentCode,
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
                )
                (SELECT
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Bank' AS documentNarration,
                    '' AS chequeNumber,
                    payments.GLCode AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    SUM(payments.amount) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID createdUserID,
                    shiftDetail.startTime createdDateTime,
                    menusalesmaster.createdUserName createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () `timestamp`
                FROM
                    srp_erp_pos_menusalespayments payments 
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON payments.menuSalesID = menusalesmaster.menuSalesID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = payments.GLCode
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID  
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND payments.paymentConfigMasterID!=7 AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    payments.GLCode);";

        $result = $this->db->query($q);
        return $result;
    }

    /** 3. COGS */
    function update_cogs_generalLedger($shiftID, $isCreditSales = true)
    {
        echo '<strong>update_cogs_generalLedger</strong><br/>/>';
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
            )
            
            (SELECT
                'POSR' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                'POS Sales - COGS' AS documentNarration,
                '' AS chequeNumber,
                item.costGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'dr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                  ( sum( IFNULL(item.cost,0) * item.menuSalesQty    ) )  / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                  ( sum( IFNULL(item.cost,0) *  item.menuSalesQty    ) )  / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                menusalesmaster.createdUserGroup AS createdUserGroup,
                menusalesmaster.createdPCID AS createdPCID,
                menusalesmaster.createdUserID AS createdUserID,
                shiftDetail.startTime AS createdDateTime,
                menusalesmaster.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP() AS  `timestamp`
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.costGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM AND uom.companyID = menusalesmaster.companyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "'
            AND menusalesmaster.isHold = 0   AND menusalesmaster.isVoid = 0 ";

        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 0";
        }
        $q .= "     GROUP BY
                item.costGLAutoID)";

        echo $q.'<br/><br/>';

        $result = $this->db->query($q);
        return $result;
    }

    /** 4. INVENTORY */
    function update_inventory_generalLedger($shiftID, $isCreditSales = true)
    {
        echo '<strong>update_inventory_generalLedger</strong><br/>';
        $q = "INSERT INTO srp_erp_generalledger(
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
                timestamp
            )
            (SELECT
                'POSR' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                'POS Sales - Inventory' AS documentNarration,
                '' AS chequeNumber,
                item.assetGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                abs(( sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) )) *- 1 AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                ( abs(  sum( IFNULL(item.cost,0) * item.menuSalesQty    )  ) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                ( abs( sum( IFNULL(item.cost,0) *  item.menuSalesQty    ) ) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                menusalesmaster.createdUserGroup AS createdUserGroup,
                menusalesmaster.createdPCID AS createdPCID,
                menusalesmaster.createdUserID AS createdUserID,
                shiftDetail.startTime AS createdDateTime,
                menusalesmaster.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP () `timestamp`
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.assetGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM AND uom.companyID = menusalesmaster.companyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "'
            AND menusalesmaster.isHold = 0   AND menusalesmaster.isVoid = 0 ";
        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 0 ";
        }

        $q .= "   GROUP BY
                item.assetGLAutoID)";

        $result = $this->db->query($q);
        echo $q.'<br/><br/>';
        return $result;
    }

    /** 5. TAX */
    function update_tax_generalLedger($shiftID)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
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
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - TAX' AS documentNarration,
                    menusalesTax.GLCode AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(
                        sum(
                            ifnull(menusalesTax.taxAmount, 0)
                        )
                    ) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_reporting_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalestaxes menusalesTax
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = menusalesTax.menuSalesID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = menusalesTax.GLCode
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    chartOfAccount.GLAutoID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 6. COMMISSION EXPENSE  */
    function update_commissionExpense_generalLedger($shiftID)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
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
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Sales Commission' AS documentNarration,
                    customers.expenseGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        menusalesmaster.deliveryCommissionAmount
                    ) AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        sum(
                            menusalesmaster.deliveryCommissionAmount
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            IFNULL(menusalesmaster.deliveryCommissionAmount,0)
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = menusalesmaster.deliveryPersonID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customers.expenseGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
                AND (
                    menusalesmaster.deliveryCommission IS NOT NULL
                    AND menusalesmaster.deliveryCommission <> 0
                )
                AND menusalesmaster.isDelivery = 1 AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    customers.expenseGLAutoID)";

        $result = $this->db->query($q);
        return $result;
    }

    /** 7. COMMISSION PAYABLE */
    function update_commissionPayable_generalLedger($shiftID)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                    documentCode,
                    documentMasterAutoID,
                    documentSystemCode,
                    documentDate,
                    documentYear,
                    documentMonth,
                    documentNarration,
                    GLAutoID,
                    systemGLCode,
                    GLCode,
                    GLDescription,
                    GLType,
                    amount_type,
                    isFromItem,
                    transactionCurrency,
                    transactionExchangeRate,
                    transactionAmount,
                    transactionCurrencyID,
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
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Sales Commission Payable' AS documentNarration,
                    customers.liabilityGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    ABS(
                        sum(
                            menusalesmaster.deliveryCommissionAmount
                        )
                    ) * - 1 AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        ABS(
                            sum(
                                menusalesmaster.deliveryCommissionAmount
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        ABS(
                            sum(
                               IFNULL( menusalesmaster.deliveryCommissionAmount,0)
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = menusalesmaster.deliveryPersonID
                
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customers.liabilityGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
                AND (
                    menusalesmaster.deliveryCommission IS NOT NULL
                    AND menusalesmaster.deliveryCommission <> 0
                )
                AND menusalesmaster.isDelivery = 1
                AND menusalesmaster.isOnTimeCommision = 0 AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    customers.liabilityGLAutoID
                );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 8. ROYALTY PAYABLE */
    function update_royaltyPayable_generalLedger($shiftID)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
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
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Royalty Payable' AS documentNarration,
                    franchisemaster.royaltyLiabilityGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    ABS(
                        sum(
                            (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) * - 1 AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        ABS(
                            sum(
                                (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                    franchisemaster.royaltyPercentage / 100
                                )
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        ABS(
                            sum(
                                (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                    franchisemaster.royaltyPercentage / 100
                                )
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                INNER JOIN srp_erp_pos_franchisemaster franchisemaster ON franchisemaster.warehouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = franchisemaster.royaltyLiabilityGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    franchisemaster.royaltyLiabilityGLAutoID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 9. ROYALTY EXPENSES */
    function update_royaltyExpenses_generalLedger($shiftID)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
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
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Royalty Expenses' AS documentNarration,
                    franchisemaster.royaltyExpenseGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        ( IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0) ) * (
                            franchisemaster.royaltyPercentage / 100
                        )
                    ) AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        sum(
                            ( menusalesmaster.netTotal + menusalesmaster.serviceCharge ) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            ( menusalesmaster.netTotal + menusalesmaster.serviceCharge ) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                INNER JOIN srp_erp_pos_franchisemaster franchisemaster ON franchisemaster.warehouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = franchisemaster.royaltyExpenseGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID

                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    franchisemaster.royaltyExpenseGLAutoID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 10. SERVICE CHARGE */
    function update_serviceCharge_generalLedger($shiftID)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
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
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Service Charge' AS documentNarration,
                    servicecharge.GLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(
                        sum(
                            ifnull(
                                servicecharge.serviceChargeAmount,
                                0
                            )
                        )
                    ) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(
                                        servicecharge.serviceChargeAmount,
                                        0
                                    )
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(
                                        servicecharge.serviceChargeAmount,
                                        0
                                    )
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_reporting_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesservicecharge servicecharge
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = servicecharge.menuSalesID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = servicecharge.GLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    chartOfAccount.GLAutoID
            )";

        $result = $this->db->query($q);
        return $result;
    }

    /** 11. CREDIT CUSTOMER PAYMENTS */
    function update_creditSales_generalLedger($shiftID)
    {
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
            )(
                SELECT
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Credit Sales' AS documentNarration,
                    '' AS chequeNumber,
                    chartOfAccount.GLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    SUM(payments.amount) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
                    payments.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate (
                        customermaster.customerCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS partyExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            customermaster.customerCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    3 AS subLedgerType,
                    'AR' AS subLedgerDesc,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID createdUserID,
                    shiftDetail.startTime createdDateTime,
                    menusalesmaster.createdUserName createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP() `timestamp`
                FROM
                    srp_erp_pos_menusalespayments payments
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON payments.menuSalesID = menusalesmaster.menuSalesID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = payments.customerAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customermaster.receivableAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0
                AND menusalesmaster.isVoid = 0
                AND payments.paymentConfigMasterID = 7
                GROUP BY
                    chartOfAccount.GLAutoID,
                    payments.customerAutoID
            )";

        $result = $this->db->query($q);
        return $result;
    }

    /** BANK LEDGER  */
    function update_bankLedger($shiftID)
    {
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
                segmentID,
                segmentCode,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timeStamp`
                )
                (SELECT
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d')  AS documentDate,
                    '2' AS transactionType,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    SUM(payments.amount) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    chartOfAccount.bankCurrencyID AS bankCurrencyID,
                    chartOfAccount.bankCurrencyCode AS bankCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        chartOfAccount.bankCurrencyID,
                        menusalesmaster.companyID
                    ) AS bankCurrencyExchangeRate,
                    (SUM(payments.amount)) /(
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            chartOfAccount.bankCurrencyID,
                            menusalesmaster.companyID
                        )
                    )  AS bankCurrencyAmount,
                    chartOfAccount.bankCurrencyDecimalPlaces AS bankCurrencyDecimalPlaces,
                    '1' AS modeofPayment,
                    'payment collection from POSR' AS memo,
                    chartOfAccount.bankName AS bankName,
                    chartOfAccount.GLAutoID AS bankGLAutoID,
                    chartOfAccount.systemAccountCode AS bankSystemAccountCode,
                    chartOfAccount.GLSecondaryCode AS bankGLSecondaryCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    'RV' AS documentType,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime createdDateTime,
                    menusalesmaster.createdUserName createdUserName,
                    CURRENT_TIMESTAMP () `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                INNER JOIN srp_erp_pos_menusalespayments payments ON payments.menuSalesID = menusalesmaster.menuSalesID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = payments.GLCode
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND payments.paymentConfigMasterID!=7 AND payments.paymentConfigMasterID!=25
                GROUP BY
                    payments.GLCode);";

        $result = $this->db->query($q);
        return $result;
    }

    /** ITEM MASTER STOCK UPDATE */
    function update_itemMasterNewStock($shiftID)
    {
        $q = "SELECT
                    itemmaster.itemAutoID,
                    -- menusalesmaster.wareHouseAutoID,
                    -- itemmaster.currentStock,
                    -- sum(itemdetail.qty*itemdetail.menuSalesQty) as qty,
                    -- itemdetail.UOMID,
                    -- itemmaster.defaultUnitOfMeasure,
                    -- itemdetail.UOM,
                    -- getUoMConvertion (
                    -- 	itemdetail.UOMID,
                    -- 	itemmaster.defaultUnitOfMeasureID,
                    -- 	menusalesmaster.companyID
                    -- ) AS convertionRate,
                    -- SUM(
                    -- 	(itemdetail.qty * item.qty) / (
                    -- 		getUoMConvertion (
                    -- 			itemdetail.UOMID,
                    -- 			itemmaster.defaultUnitOfMeasureID,
                    -- 			menusalesmaster.companyID
                    -- 		)
                    -- 	)
                    -- ) AS usedStock, 
                 
                
                itemmaster.currentStock - SUM(
                        (
                            (
                                itemdetail.qty * itemdetail.menuSalesQty
                            ) / (
                                getUoMConvertion (
                                    itemdetail.UOMID,
                                    itemmaster.defaultUnitOfMeasureID,
                                    menusalesmaster.companyID
                                )
                            )
                        )
                    ) as currentStock
                FROM
                    srp_erp_pos_menusalesitemdetails itemdetail
                INNER JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID #LEFT JOIN srp_erp_pos_menusalesitems item ON item.menuSalesItemID = itemdetail.menuSalesItemID
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "' AND menusalesmaster.isCreditSales = 0
                    AND menusalesmaster.isHold = 0    
                    AND menusalesmaster.isVoid = 0
                GROUP BY
                    itemmaster.itemAutoID, itemdetail.UOMID";
        $result = $this->db->query($q)->result_array();

        /** New stock information */
        if (!empty($result)) {
            /**update new stock */
            $this->db->update_batch('srp_erp_itemmaster', $result, 'itemAutoID');
        }

        return $result;
    }

    /** WAREHOUSE ITEM MASTER STOCK UPDATE */
    function update_warehouseItemMasterNewStock($shiftID)
    {
        $q = "SELECT
                warehouseitem.warehouseItemsAutoID,
                -- itemdetail.itemAutoID,
             
            warehouseitem.currentStock - SUM(
                    (
                        (itemdetail.qty * itemdetail.menuSalesQty) / (
                            getUoMConvertion (
                                itemdetail.UOMID,
                                itemmaster.defaultUnitOfMeasureID,
                                menusalesmaster.companyID
                            )
                        )
                    )
                ) as currentStock 
            FROM
                srp_erp_pos_menusalesitemdetails itemdetail
            INNER JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID
            LEFT JOIN srp_erp_warehouseitems warehouseitem ON warehouseitem.itemAutoID = itemmaster.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "'
            AND menusalesmaster.wareHouseAutoID = warehouseitem.wareHouseAutoID AND menusalesmaster.isCreditSales = 0
            AND menusalesmaster.isHold = 0    
            AND menusalesmaster.isVoid = 0
            GROUP BY
                itemmaster.itemAutoID,
                menusalesmaster.wareHouseAutoID, itemdetail.UOMID;";
        $result = $this->db->query($q)->result_array();

        /** New stock information */
        if (!empty($result)) {
            /**update new stock */
            $this->db->update_batch('srp_erp_warehouseitems', $result, 'warehouseItemsAutoID');
        }

        return $result;
    }

    /** ITEM LEDGER  */
    function update_itemLedger($shiftID)
    {

        $curDate = date('Y-m-d');
        $q = "SELECT
                    financeyear.companyFinanceYearID AS companyFinanceYearID,
                CONCAT(financeyear.beginingDate , \" - \" , financeyear.endingDate ) AS companyFinanceYear,
                financeyear.beginingDate AS FYBegin,
                financeyear.endingDate  AS FYEnd,
                financeperiod.dateFrom AS FYPeriodDateFrom,
                financeperiod.dateTo  AS FYPeriodDateTo
                
                FROM
                    srp_erp_companyfinanceyear financeyear
                INNER JOIN  srp_erp_companyfinanceperiod financeperiod  ON financeperiod.companyFinanceYearID = financeyear.companyFinanceYearID
                WHERE
                    financeyear.companyID = '" . current_companyID() . "'
                AND financeyear.isActive = 1
                AND financeyear.beginingDate < '" . $curDate . "'
                AND financeyear.endingDate > '" . $curDate . "'
                AND financeperiod.isActive =1
                AND financeperiod.dateFrom < '" . $curDate . "'
                AND financeperiod.dateTo > '" . $curDate . "'";
        $financeYear = $this->db->query($q)->row_array();

        $companyFinanceYearID = isset($financeYear['companyFinanceYearID']) ? $financeYear['companyFinanceYearID'] : null;
        $companyFinanceYear = isset($financeYear['companyFinanceYear']) ? $financeYear['companyFinanceYear'] : null;
        $FYBegin = isset($financeYear['FYBegin']) ? $financeYear['FYBegin'] : null;
        $FYEnd = isset($financeYear['FYEnd']) ? $financeYear['FYEnd'] : null;
        $FYPeriodDateFrom = isset($financeYear['FYPeriodDateFrom']) ? $financeYear['FYPeriodDateFrom'] : null;
        $FYPeriodDateTo = isset($financeYear['FYPeriodDateTo']) ? $financeYear['FYPeriodDateTo'] : null;


        $q = "INSERT INTO srp_erp_itemledger (
                documentID,
                documentAutoID,
                documentCode,
                documentSystemCode,
                documentDate,
                companyFinanceYearID,
                companyFinanceYear,
                FYBegin,
                FYEnd,
                FYPeriodDateFrom,
                FYPeriodDateTo,
                wareHouseAutoID,
                wareHouseCode,
                wareHouseLocation,
                wareHouseDescription,
                itemAutoID,
                itemSystemCode,
                ItemSecondaryCode,
                itemDescription,
                defaultUOMID,
                defaultUOM,
                transactionUOMID,
                transactionUOM,
                transactionQTY,
                convertionRate,
                currentStock,
                PLGLAutoID,
                PLSystemGLCode,
                PLGLCode,
                PLDescription,
                PLType,
                BLGLAutoID,
                BLSystemGLCode,
                BLGLCode,
                BLDescription,
                BLType,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalWacAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingWacAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                narration,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp`
            )(
                SELECT
                    'POSR' AS documentID,
                    menusalesmaster.shiftID AS documentAutoID,
                    'POSR' AS documentCode,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d')  AS documentDate,
                    '" . $companyFinanceYearID . "' AS companyFinanceYearID,
                    '" . $companyFinanceYear . "' AS companyFinanceYear,
                    '" . $FYBegin . "' AS FYBegin,
                    '" . $FYEnd . "' AS FYEnd,
                    '" . $FYPeriodDateFrom . "' AS FYPeriodDateFrom,
                    '" . $FYPeriodDateTo . "' AS FYPeriodDateTo,
                    warehousemaster.wareHouseAutoID AS wareHouseAutoID,
                    warehousemaster.wareHouseCode AS wareHouseCode,
                    warehousemaster.wareHouseLocation AS wareHouseLocation,
                    warehousemaster.wareHouseDescription AS wareHouseDescription,
                    itemdetail.itemAutoID AS itemAutoID,
                    itemmaster.itemSystemCode AS itemSystemCode,
                    itemmaster.seconeryItemCode AS seconeryItemCode,
                    itemmaster.itemDescription AS itemDescription,
                    itemmaster.defaultUnitOfMeasureID AS defaultUOMID,
                    itemmaster.defaultUnitOfMeasure AS defaultUOM,
                    itemdetail.UOMID AS transactionUOMID,
                    itemdetail.UOM AS transactionUOM,
                    sum(itemdetail.qty * itemdetail.menuSalesQty) *- 1 AS transactionQTY,
                    getUoMConvertion (
                        itemdetail.UOMID,
                        itemmaster.defaultUnitOfMeasureID,
                        menusalesmaster.companyID
                    ) AS convertionRate,
                    itemmaster.currentStock AS currentStock,
                    itemmaster.costGLAutoID AS PLGLAutoID,
                    itemmaster.costSystemGLCode AS PLSystemGLCode,
                    itemmaster.costGLCode AS PLGLCode,
                    itemmaster.costDescription AS PLDescription,
                    itemmaster.costType AS PLType,
                    itemmaster.assteGLAutoID AS BLGLAutoID,
                    itemmaster.assteSystemGLCode AS BLSystemGLCode,
                    itemmaster.assteGLCode AS BLGLCode,
                    itemmaster.assteDescription AS BLDescription,
                    itemmaster.assteType AS BLType,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        ifnull(
                            itemdetail.cost * (
                                itemdetail.menuSalesQty
                            ),
                            0
                        ) *- 1
                    ) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            sum(
                                ifnull(
                                    itemdetail.cost * (
                                         itemdetail.menuSalesQty
                                    ),
                                    0
                                )
                            )
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        ) *- 1
                    ) AS companyLocalAmount,
                    itemmaster.companyLocalWacAmount AS companyLocalWacAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            ifnull(
                                itemdetail.cost * (
                                    itemdetail.menuSalesQty
                                ),
                                0
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        ) *- 1
                    ) AS companyReportingAmount,
                    itemmaster.companyLocalWacAmount / getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingWacAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    'POS Sales' AS narration,
                    shiftDetail.createdUserGroup AS createdUserGroup,
                    shiftDetail.createdPCID AS createdPCID,
                    shiftDetail.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    shiftDetail.createdUserName AS createdUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesitemdetails AS itemdetail
                LEFT JOIN srp_erp_pos_menusalesitems item ON itemdetail.menuSalesItemID = item.menuSalesItemID
                LEFT JOIN srp_erp_itemmaster AS itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0  AND menusalesmaster.isCreditSales = 0
                AND menusalesmaster.isVoid = 0  
                GROUP BY
                    itemdetail.itemAutoID
            )";

        $result = $this->db->query($q);
        return $result;
    }


    /** 0. CUSTOMER INVOICE - Credit Sales Entries  */
    function pos_generate_invoices_bill($shiftID, $billNo)
    {
        /** Create Invoice Header */
        $this->load->library('sequence');

        $q = "INSERT INTO srp_erp_customerinvoicemaster (
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
                    'Direct' AS invoiceType,
                    'CINV' AS documentID,
                    DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" ) AS invoiceDate,
                    DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" ) AS invoiceDueDate,
                    DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" ) AS customerInvoiceDate,
                    0 AS invoiceCode,
                    srp_erp_pos_menusalesmaster.invoiceCode AS referenceNo,
                    concat( 'POS Credit Sales - ', srp_erp_pos_menusalesmaster.invoiceCode ) AS invoiceNarration,
                    getCompanyFinanceYearID ( srp_erp_pos_shiftdetails.companyID ) AS companyFinanceYearID,
                    concat( FY.beginingDate, ' - ', FY.endingDate ) AS companyFinanceYear,
                    FY.beginingDate AS FYBegin,
                    FY.endingDate AS FYEnd,
                    getCompanyFinancePeriodID ( srp_erp_pos_shiftdetails.companyID ) AS companyFinancePeriodID,
                    srp_erp_pos_menusalespayments.customerAutoID AS customerID,
                    srp_erp_customermaster.customerSystemCode AS customerSystemCode,
                    srp_erp_customermaster.customerName AS customerName,
                    srp_erp_customermaster.receivableAutoID AS customerReceivableAutoID,
                    srp_erp_customermaster.receivableSystemGLCode AS customerReceivableSystemGLCode,
                    srp_erp_customermaster.receivableGLAccount AS customerReceivableGLAccount,
                    srp_erp_customermaster.receivableDescription AS customerReceivableDescription,
                    srp_erp_customermaster.receivableType AS customerReceivableType,
                    srp_erp_pos_menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    srp_erp_pos_menusalesmaster.transactionCurrency AS transactionCurrency,
                    srp_erp_pos_menusalesmaster.transactionExchangeRate AS transactionExchangeRate,
                    Sum( srp_erp_pos_menusalespayments.amount ) AS transactionAmount,
                    srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.companyLocalCurrencyID,
                    srp_erp_pos_menusalesmaster.companyLocalCurrency,
                    srp_erp_pos_menusalesmaster.companyLocalExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.companyLocalExchangeRate ) AS companyLocalAmount,
                    srp_erp_pos_menusalesmaster.companyLocalCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.companyReportingCurrencyID,
                    srp_erp_pos_menusalesmaster.companyReportingCurrency,
                    srp_erp_pos_menusalesmaster.companyReportingExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.companyReportingExchangeRate ) AS companyReportingAmount,
                    srp_erp_pos_menusalesmaster.companyReportingCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.customerCurrencyID,
                    srp_erp_pos_menusalesmaster.customerCurrency,
                    srp_erp_pos_menusalesmaster.customerCurrencyExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.customerCurrencyExchangeRate ) AS customerCurrencyAmount,
                    srp_erp_pos_menusalesmaster.customerCurrencyDecimalPlaces,
                    1 AS confirmedYN,
                    srp_erp_pos_shiftdetails.createdUserID AS confirmedByEmpID,
                    srp_erp_pos_shiftdetails.createdUserName AS confirmedByName,
                    srp_erp_pos_shiftdetails.startTime AS confirmedDate,
                    1 AS approvedYN,
                    srp_erp_pos_shiftdetails.startTime AS approvedDate,
                    srp_erp_pos_shiftdetails.createdUserID AS approvedbyEmpID,
                    srp_erp_pos_shiftdetails.createdUserName AS approvedbyEmpName,
                    srp_erp_pos_menusalesmaster.segmentID AS segmentID,
                    srp_erp_pos_menusalesmaster.segmentCode AS segmentCode,
                    srp_erp_pos_menusalesmaster.companyID,
                    srp_erp_pos_menusalesmaster.companyCode,
                    srp_erp_pos_shiftdetails.createdUserGroup,
                    srp_erp_pos_shiftdetails.createdPCID,
                    srp_erp_pos_shiftdetails.createdUserID,
                    srp_erp_pos_shiftdetails.startTime AS createdDateTime,
                    srp_erp_pos_shiftdetails.createdUserName,
                    srp_erp_pos_shiftdetails.`timestamp` AS `timestamp` 
                FROM
                    srp_erp_pos_menusalesmaster
                    LEFT JOIN srp_erp_pos_menusalespayments ON srp_erp_pos_menusalespayments.menuSalesID = srp_erp_pos_menusalesmaster.menuSalesID
                    LEFT JOIN srp_erp_pos_shiftdetails ON srp_erp_pos_menusalesmaster.shiftID = srp_erp_pos_shiftdetails.shiftID
                    LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID
                    LEFT JOIN srp_erp_companyfinanceyear FY ON FY.companyFinanceYearID = getCompanyFinanceYearID ( srp_erp_pos_shiftdetails.companyID ) 
                WHERE
                    srp_erp_pos_menusalesmaster.shiftID = '" . $shiftID . "' 
                    AND srp_erp_pos_menusalesmaster.isCreditSales = 1 
                    AND srp_erp_pos_menusalespayments.paymentConfigMasterID = 7 
                    AND srp_erp_pos_menusalesmaster.isVoid = 0
                    AND srp_erp_pos_menusalesmaster.isHold = 0
                    AND srp_erp_pos_menusalesmaster.menuSalesID = '" . $billNo . "' 
                    
                GROUP BY
                    srp_erp_pos_menusalesmaster.menuSalesID 
                ORDER BY
                    srp_erp_pos_menusalesmaster.menuSalesID DESC 
                    )";


        $this->db->query($q);
        $insert_id = $this->db->insert_id();
        $row_count = $this->db->affected_rows();
        $result = array();
        $i = 0;
        while (true) {
            if ($row_count == $i) {
                break;

            } else if ($i > 99) {
                break;
            }
            $result[$i] = $insert_id;
            $insert_id++;
            $i++;
        }
        if (!empty($result)) {
            $tmpData = array();
            $i2 = 0;
            $where = ' WHERE (';
            foreach ($result as $id) {
                $tmpData[$i2]['invoiceAutoID'] = $id;
                $tmpData[$i2]['invoiceCode'] = $this->sequence->sequence_generator('CINV');
                $where .= ' srp_erp_customerinvoicemaster.invoiceAutoID = ' . $id . ' OR';
                $i2++;
            }
            $where = trim($where, ' OR');
            $where .= ')';
            //var_dump($tmpData);
            $this->db->update_batch('srp_erp_customerinvoicemaster', $tmpData, 'invoiceAutoID');

            if ($row_count > 0) {
                /** Create Invoice Detail */
                $q = "INSERT INTO srp_erp_customerinvoicedetails (
                    invoiceAutoID,
                    `type`,
                    description,
                    transactionAmount,
                    companyLocalAmount,
                    companyReportingAmount,
                    customerAmount,
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
                        srp_erp_customerinvoicemaster.referenceNo AS description,
                        srp_erp_customerinvoicemaster.transactionAmount,
                        srp_erp_customerinvoicemaster.companyLocalAmount,
                        srp_erp_customerinvoicemaster.companyReportingAmount,
                        srp_erp_customerinvoicemaster.customerCurrencyAmount,
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
                    " . $where . " LIMIT " . $row_count . " )";
                //echo $q;
                $this->db->query($q);


                /** Document Approved Table Entries */
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
                    " . $where . " LIMIT " . $row_count . " )";

                $this->db->query($q2);


                /** update menusales master  */
                $q3 = "UPDATE srp_erp_pos_menusalesmaster AS t1,
                        (
                        SELECT
                            srp_erp_pos_menusalesmaster.menuSalesID AS menuSalesID,
                            srp_erp_customerinvoicemaster.invoiceCode AS invoiceCode, 
                            srp_erp_customerinvoicemaster.invoiceAutoID AS invoiceAutoID
                        FROM
                            srp_erp_pos_menusalesmaster
                            INNER JOIN srp_erp_customerinvoicemaster ON srp_erp_pos_menusalesmaster.invoiceCode = srp_erp_customerinvoicemaster.referenceNo 
                        WHERE
                            srp_erp_pos_menusalesmaster.shiftID =  '" . $shiftID . "'  AND srp_erp_pos_menusalesmaster.menuSalesID =  '" . $billNo . "' 
                            ) AS t2 
                            SET t1.documentSystemCode = t2.invoiceCode  ,   t1.documentMasterAutoID= t2.invoiceAutoID
                        WHERE
                            t1.menuSalesID = t2.menuSalesID 
                            AND t1.shiftID =  '" . $shiftID . "' AND  t1.menuSalesID =  '" . $billNo . "'   ";
                $this->db->query($q3);
            }

        }
        //var_dump($result);
        return $result;
    }

    /**Credit Sales Double Entry */
    /** 1. CREDIT SALES  - REVENUE */
    function update_revenue_generalLedger_credit_sales($shiftID)
    {
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
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
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
                timestamp
            ) (SELECT
                    'CINV' AS documentCode,
                menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                menusalesmaster.documentSystemCode  AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                concat('POS Credit Sales - Revenue',' - ',menusalesmaster.invoiceCode) AS documentNarration,
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
                abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                (( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID )  )) AS companyLocalAmount, getDecimalPlaces ( company.company_default_currencyID  ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID as companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                (( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID )  )) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                'CUS' AS partyType,
                customermaster.customerAutoID AS partyAutoID,
                customermaster.customerSystemCode AS partySystemCode,
                customermaster.customerName AS partyName,
                customermaster.customerCurrencyID AS partyCurrencyID,
                customermaster.customerCurrency AS partyCurrency,
                getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                shiftDetail.createdUserGroup AS createdUserGroup,
            shiftDetail.createdPCID as  createdPCID,
            shiftDetail.createdUserID as  createdUserID,
            shiftDetail.startTime as createdDateTime,
            shiftDetail.createdUserName as createdUserName,
            NULL AS modifiedPCID,
            NULL AS modifiedUserID,
            NULL AS modifiedDateTime,
            null AS modifiedUserName,
            CURRENT_TIMESTAMP() as `timestamp`
            FROM
                srp_erp_pos_menusalesitems item
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.revenueGLAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
            LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "' and menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 1
            GROUP BY
                revenueGLAutoID, menusalesmaster.menuSalesID);";

        $result = $this->db->query($q);
        //echo $this->db->last_query();
        return $result;
    }

    /** 2. CREDIT SALES  - COGS */
    function update_cogs_generalLedger_credit_sales($shiftID)
    {
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
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
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
            )
            
            (SELECT
                'CINV' AS documentCode,
                menusalesmaster.documentMasterAutoID  AS documentMasterAutoID,
                menusalesmaster.documentSystemCode AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                concat('POS Credit Sales - COGS',' - ',menusalesmaster.invoiceCode) AS documentNarration,
                '' AS chequeNumber,
                item.costGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'dr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                  ( sum( IFNULL(item.cost,0) * item.menuSalesQty    ) )  / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                  ( sum( IFNULL(item.cost,0) *  item.menuSalesQty    ) )  / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                'CUS' AS partyType,
                customermaster.customerAutoID AS partyAutoID,
                customermaster.customerSystemCode AS partySystemCode,
                customermaster.customerName AS partyName,
                customermaster.customerCurrencyID AS partyCurrencyID,
                customermaster.customerCurrency AS partyCurrency,
                getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                menusalesmaster.createdUserGroup AS createdUserGroup,
                menusalesmaster.createdPCID AS createdPCID,
                menusalesmaster.createdUserID AS createdUserID,
                shiftDetail.startTime AS createdDateTime,
                menusalesmaster.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP() AS  `timestamp`
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.costGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM AND uom.companyID = menusalesmaster.companyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
            LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "'
            AND menusalesmaster.isHold = 0   AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 1
            GROUP BY
                item.costGLAutoID, menusalesmaster.menuSalesID)";

        $result = $this->db->query($q);
        return $result;
    }

    /** 3. CREDIT SALES  - INVENTORY */
    function update_inventory_generalLedger_credit_sales($shiftID)
    {
        $q = "INSERT INTO srp_erp_generalledger(
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
                timestamp
            )
            (SELECT
                'CINV' AS documentCode,
                menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                menusalesmaster.documentSystemCode AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                concat('POS Credit Sales - Inventory', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                '' AS chequeNumber,
                item.assetGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                abs(( sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) )) *- 1 AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                ( abs( sum( IFNULL(item.cost,0) * item.menuSalesQty    ) ) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                ( abs( sum( IFNULL(item.cost,0) *  item.menuSalesQty    ) ) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                'CUS' AS partyType,
                customermaster.customerAutoID AS partyAutoID,
                customermaster.customerSystemCode AS partySystemCode,
                customermaster.customerName AS partyName,
                customermaster.customerCurrencyID AS partyCurrencyID,
                customermaster.customerCurrency AS partyCurrency,
                getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                abs(( sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) )) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                menusalesmaster.createdUserGroup AS createdUserGroup,
                menusalesmaster.createdPCID AS createdPCID,
                menusalesmaster.createdUserID AS createdUserID,
                shiftDetail.startTime AS createdDateTime,
                menusalesmaster.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP () `timestamp`
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.assetGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM AND uom.companyID = menusalesmaster.companyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
            LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "'
            AND menusalesmaster.isHold = 0   AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 1
            GROUP BY
                item.assetGLAutoID, menusalesmaster.menuSalesID)";

        $result = $this->db->query($q);
        return $result;
    }

    /** 4.  CREDIT SALES - TAX */
    function update_tax_generalLedger_credit_sales($shiftID)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
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
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - TAX', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    menusalesTax.GLCode AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(
                        sum(
                            ifnull(menusalesTax.taxAmount, 0)
                        )
                    ) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_reporting_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
                    customermaster.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                    abs(
                        sum(
                            ifnull(menusalesTax.taxAmount, 0)
                        )
                    ) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalestaxes menusalesTax
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = menusalesTax.menuSalesID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = menusalesTax.GLCode
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    chartOfAccount.GLAutoID, menusalesmaster.menuSalesID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 5.  CREDIT SALES - COMMISSION EXPENSE  */
    function update_commissionExpense_generalLedger_credit_sales($shiftID)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
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
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Sales Commission', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    customers.expenseGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        menusalesmaster.deliveryCommissionAmount
                    ) AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        sum(
                            menusalesmaster.deliveryCommissionAmount
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            IFNULL(menusalesmaster.deliveryCommissionAmount,0)
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
                    customermaster.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                    sum( menusalesmaster.deliveryCommissionAmount ) / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = menusalesmaster.deliveryPersonID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customers.expenseGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
                AND (
                    menusalesmaster.deliveryCommission IS NOT NULL
                    AND menusalesmaster.deliveryCommission <> 0
                )
                AND menusalesmaster.isDelivery = 1 AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    customers.expenseGLAutoID, menusalesmaster.menuSalesID)";

        $result = $this->db->query($q);
        return $result;
    }

    /** 6.  CREDIT SALES - COMMISSION PAYABLE */
    function update_commissionPayable_generalLedger_credit_sales($shiftID)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                    documentCode,
                    documentMasterAutoID,
                    documentSystemCode,
                    documentDate,
                    documentYear,
                    documentMonth,
                    documentNarration,
                    GLAutoID,
                    systemGLCode,
                    GLCode,
                    GLDescription,
                    GLType,
                    amount_type,
                    isFromItem,
                    transactionCurrency,
                    transactionExchangeRate,
                    transactionAmount,
                    transactionCurrencyID,
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
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Sales Commission Payable', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    customers.liabilityGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    ABS(
                        sum(
                            menusalesmaster.deliveryCommissionAmount
                        )
                    ) * - 1 AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        ABS(
                            sum(
                                menusalesmaster.deliveryCommissionAmount
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        ABS(
                            sum(
                               IFNULL( menusalesmaster.deliveryCommissionAmount,0)
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
					customermaster.customerAutoID AS partyAutoID,
					customermaster.customerSystemCode AS partySystemCode,
					customermaster.customerName AS partyName,
					customermaster.customerCurrencyID AS partyCurrencyID,
					customermaster.customerCurrency AS partyCurrency,
					getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
					ABS(
                        sum(
                            menusalesmaster.deliveryCommissionAmount
                        )
                    ) * - 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
					customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = menusalesmaster.deliveryPersonID
                
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customers.liabilityGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
                AND (
                    menusalesmaster.deliveryCommission IS NOT NULL
                    AND menusalesmaster.deliveryCommission <> 0
                )
                AND menusalesmaster.isDelivery = 1
                AND menusalesmaster.isOnTimeCommision = 0 AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    customers.liabilityGLAutoID, menusalesmaster.menuSalesID
                );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 7.  CREDIT SALES - ROYALTY PAYABLE */
    function update_royaltyPayable_generalLedger_credit_sales($shiftID)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
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
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Royalty Payable', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    franchisemaster.royaltyLiabilityGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    ABS(
                        sum(
                            (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) * - 1 AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        ABS(
                            sum(
                                (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                    franchisemaster.royaltyPercentage / 100
                                )
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        ABS(
                            sum(
                                (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                    franchisemaster.royaltyPercentage / 100
                                )
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
					customermaster.customerAutoID AS partyAutoID,
					customermaster.customerSystemCode AS partySystemCode,
					customermaster.customerName AS partyName,
					customermaster.customerCurrencyID AS partyCurrencyID,
					customermaster.customerCurrency AS partyCurrency,
					getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
					ABS(
                        sum(
                            (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) * - 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
					customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                INNER JOIN srp_erp_pos_franchisemaster franchisemaster ON franchisemaster.warehouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = franchisemaster.royaltyLiabilityGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    franchisemaster.royaltyLiabilityGLAutoID, menusalesmaster.menuSalesID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 8.  CREDIT SALES - ROYALTY EXPENSES */
    function update_royaltyExpenses_generalLedger_credit_sales($shiftID)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
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
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Royalty Expenses', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    franchisemaster.royaltyExpenseGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        ( IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0) ) * (
                            franchisemaster.royaltyPercentage / 100
                        )
                    ) AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        sum(
                            ( menusalesmaster.netTotal + menusalesmaster.serviceCharge ) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            ( menusalesmaster.netTotal + menusalesmaster.serviceCharge ) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
					customermaster.customerAutoID AS partyAutoID,
					customermaster.customerSystemCode AS partySystemCode,
					customermaster.customerName AS partyName,
					customermaster.customerCurrencyID AS partyCurrencyID,
					customermaster.customerCurrency AS partyCurrency,
					getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
					sum(
                        ( IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0) ) * (
                            franchisemaster.royaltyPercentage / 100
                        )
                    ) / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
					customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                INNER JOIN srp_erp_pos_franchisemaster franchisemaster ON franchisemaster.warehouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = franchisemaster.royaltyExpenseGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    franchisemaster.royaltyExpenseGLAutoID, menusalesmaster.menuSalesID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 9. CREDIT SALES -  SERVICE CHARGE */
    function update_serviceCharge_generalLedger_credit_sales($shiftID)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
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
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Service Charge', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    servicecharge.GLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(
                        sum(
                            ifnull(
                                servicecharge.serviceChargeAmount,
                                0
                            )
                        )
                    ) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(
                                        servicecharge.serviceChargeAmount,
                                        0
                                    )
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(
                                        servicecharge.serviceChargeAmount,
                                        0
                                    )
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_reporting_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    
                    'CUS' AS partyType,
                    customermaster.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                    abs(
                        sum(
                            ifnull(
                                servicecharge.serviceChargeAmount,
                                0
                            )
                        )
                    ) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesservicecharge servicecharge
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = servicecharge.menuSalesID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = servicecharge.GLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    chartOfAccount.GLAutoID, menusalesmaster.menuSalesID
            )";

        $result = $this->db->query($q);
        return $result;
    }

    /** 10. CREDIT SALES -  CREDIT CUSTOMER PAYMENTS */
    function update_creditSales_generalLedger_credit_sales($shiftID)
    {
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
            )(
                SELECT
                    'CINV' AS documentCode,
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    '' AS chequeNumber,
                    chartOfAccount.GLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    SUM(payments.amount) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
                    payments.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate (
                        customermaster.customerCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS partyExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            customermaster.customerCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    3 AS subLedgerType,
                    'AR' AS subLedgerDesc,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID createdUserID,
                    shiftDetail.startTime createdDateTime,
                    menusalesmaster.createdUserName createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP() `timestamp`
                FROM
                    srp_erp_pos_menusalespayments payments
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON payments.menuSalesID = menusalesmaster.menuSalesID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = payments.customerAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customermaster.receivableAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0
                AND menusalesmaster.isVoid = 0
                AND payments.paymentConfigMasterID = 7 AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    chartOfAccount.GLAutoID,
                    payments.customerAutoID, menusalesmaster.menuSalesID
            )";

        $result = $this->db->query($q);
        return $result;
    }


    /** CREDIT SALES - ITEM LEDGER  */
    function update_itemLedger_credit_sales($shiftID)
    {

        $curDate = date('Y-m-d');
        $q = "SELECT
                    financeyear.companyFinanceYearID AS companyFinanceYearID,
                CONCAT(financeyear.beginingDate , \" - \" , financeyear.endingDate ) AS companyFinanceYear,
                financeyear.beginingDate AS FYBegin,
                financeyear.endingDate  AS FYEnd,
                financeperiod.dateFrom AS FYPeriodDateFrom,
                financeperiod.dateTo  AS FYPeriodDateTo
                
                FROM
                    srp_erp_companyfinanceyear financeyear
                INNER JOIN  srp_erp_companyfinanceperiod financeperiod  ON financeperiod.companyFinanceYearID = financeyear.companyFinanceYearID
                WHERE
                    financeyear.companyID = '" . current_companyID() . "'
                AND financeyear.isActive = 1
                AND financeyear.beginingDate < '" . $curDate . "'
                AND financeyear.endingDate > '" . $curDate . "'
                AND financeperiod.isActive =1
                AND financeperiod.dateFrom < '" . $curDate . "'
                AND financeperiod.dateTo > '" . $curDate . "'";
        $financeYear = $this->db->query($q)->row_array();

        $companyFinanceYearID = isset($financeYear['companyFinanceYearID']) ? $financeYear['companyFinanceYearID'] : null;
        $companyFinanceYear = isset($financeYear['companyFinanceYear']) ? $financeYear['companyFinanceYear'] : null;
        $FYBegin = isset($financeYear['FYBegin']) ? $financeYear['FYBegin'] : null;
        $FYEnd = isset($financeYear['FYEnd']) ? $financeYear['FYEnd'] : null;
        $FYPeriodDateFrom = isset($financeYear['FYPeriodDateFrom']) ? $financeYear['FYPeriodDateFrom'] : null;
        $FYPeriodDateTo = isset($financeYear['FYPeriodDateTo']) ? $financeYear['FYPeriodDateTo'] : null;


        $q = "INSERT INTO srp_erp_itemledger (
                documentID,
                documentAutoID,
                documentCode,
                documentSystemCode,
                documentDate,
                companyFinanceYearID,
                companyFinanceYear,
                FYBegin,
                FYEnd,
                FYPeriodDateFrom,
                FYPeriodDateTo,
                wareHouseAutoID,
                wareHouseCode,
                wareHouseLocation,
                wareHouseDescription,
                itemAutoID,
                itemSystemCode,
                ItemSecondaryCode,
                itemDescription,
                defaultUOMID,
                defaultUOM,
                transactionUOMID,
                transactionUOM,
                transactionQTY,
                convertionRate,
                currentStock,
                PLGLAutoID,
                PLSystemGLCode,
                PLGLCode,
                PLDescription,
                PLType,
                BLGLAutoID,
                BLSystemGLCode,
                BLGLCode,
                BLDescription,
                BLType,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalWacAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingWacAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                narration,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp`
            )(
                SELECT
                    'CINV' AS documentID,
                    menusalesmaster.shiftID AS documentAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentSystemCode AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d')  AS documentDate,
                    '" . $companyFinanceYearID . "' AS companyFinanceYearID,
                    '" . $companyFinanceYear . "' AS companyFinanceYear,
                    '" . $FYBegin . "' AS FYBegin,
                    '" . $FYEnd . "' AS FYEnd,
                    '" . $FYPeriodDateFrom . "' AS FYPeriodDateFrom,
                    '" . $FYPeriodDateTo . "' AS FYPeriodDateTo,
                    warehousemaster.wareHouseAutoID AS wareHouseAutoID,
                    warehousemaster.wareHouseCode AS wareHouseCode,
                    warehousemaster.wareHouseLocation AS wareHouseLocation,
                    warehousemaster.wareHouseDescription AS wareHouseDescription,
                    itemdetail.itemAutoID AS itemAutoID,
                    itemmaster.itemSystemCode AS itemSystemCode,
                    itemmaster.seconeryItemCode AS seconeryItemCode,
                    itemmaster.itemDescription AS itemDescription,
                    itemmaster.defaultUnitOfMeasureID AS defaultUOMID,
                    itemmaster.defaultUnitOfMeasure AS defaultUOM,
                    itemdetail.UOMID AS transactionUOMID,
                    itemdetail.UOM AS transactionUOM,
                    sum(itemdetail.qty * item.qty) *- 1 AS transactionQTY,
                    getUoMConvertion (
                        itemdetail.UOMID,
                        itemmaster.defaultUnitOfMeasureID,
                        menusalesmaster.companyID
                    ) AS convertionRate,
                    itemmaster.currentStock AS currentStock,
                    itemmaster.costGLAutoID AS PLGLAutoID,
                    itemmaster.costSystemGLCode AS PLSystemGLCode,
                    itemmaster.costGLCode AS PLGLCode,
                    itemmaster.costDescription AS PLDescription,
                    itemmaster.costType AS PLType,
                    itemmaster.assteGLAutoID AS BLGLAutoID,
                    itemmaster.assteSystemGLCode AS BLSystemGLCode,
                    itemmaster.assteGLCode AS BLGLCode,
                    itemmaster.assteDescription AS BLDescription,
                    itemmaster.assteType AS BLType,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        ifnull(
                            itemdetail.cost * (
                                itemdetail.menuSalesQty
                            ),
                            0
                        ) *- 1
                    ) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            sum(
                                ifnull(
                                    itemdetail.cost * (
                                         itemdetail.menuSalesQty
                                    ),
                                    0
                                )
                            )
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        ) *- 1
                    ) AS companyLocalAmount,
                    itemmaster.companyLocalWacAmount AS companyLocalWacAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            ifnull(
                                itemdetail.cost * (
                                    itemdetail.menuSalesQty
                                ),
                                0
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        ) *- 1
                    ) AS companyReportingAmount,
                    itemmaster.companyLocalWacAmount / getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingWacAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    'POS Sales' AS narration,
                    shiftDetail.createdUserGroup AS createdUserGroup,
                    shiftDetail.createdPCID AS createdPCID,
                    shiftDetail.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    shiftDetail.createdUserName AS createdUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesitemdetails AS itemdetail
                INNER JOIN srp_erp_pos_menusalesitems item ON itemdetail.menuSalesItemID = item.menuSalesItemID
                INNER JOIN srp_erp_itemmaster AS itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0   AND menusalesmaster.isCreditSales = 1
                AND menusalesmaster.isVoid = 0
                GROUP BY
                    itemdetail.itemAutoID
            )";

        $result = $this->db->query($q);
        return $result;
    }


    /** for manual Entries  : requested by Hisham to update the old entries  */
    function pos_credit_sales_entries_manual($shiftID)
    {

        $this->db->select('*');
        $this->db->from('srp_erp_generalledger');
        $this->db->where('documentCode', 'POSR');
        $this->db->where('documentMasterAutoID', $shiftID);
        $result = $this->db->get()->result_array();
        if (empty($result)) {
            /** 0. CUSTOMER INVOICE - Credit Sales Entries  */
            $this->pos_generate_invoices($shiftID);
            /** 1. CREDIT SALES  - REVENUE */
            $this->update_revenue_generalLedger_credit_sales($shiftID);
            /** 2. CREDIT SALES  - COGS */
            $this->update_cogs_generalLedger_credit_sales($shiftID);
            /** 3. CREDIT SALES  - INVENTORY */
            $this->update_inventory_generalLedger_credit_sales($shiftID);
            /** 4.  CREDIT SALES - TAX */
            $this->update_tax_generalLedger_credit_sales($shiftID);
            /** 5.  CREDIT SALES - COMMISSION EXPENSE  */
            $this->update_commissionExpense_generalLedger_credit_sales($shiftID);
            /** 6.  CREDIT SALES - COMMISSION PAYABLE */
            $this->update_commissionPayable_generalLedger_credit_sales($shiftID);
            /** 7.  CREDIT SALES - ROYALTY PAYABLE */
            $this->update_royaltyPayable_generalLedger_credit_sales($shiftID);
            /** 8.  CREDIT SALES - ROYALTY EXPENSES */
            $this->update_royaltyExpenses_generalLedger_credit_sales($shiftID);
            /** 9. CREDIT SALES -  SERVICE CHARGE */
            $this->update_serviceCharge_generalLedger_credit_sales($shiftID);
            /** 10. CREDIT SALES -  CREDIT CUSTOMER PAYMENTS */

            /** CREDIT SALES - ITEM LEDGER  */
            $this->update_itemLedger_credit_sales($shiftID);

            echo 'Run Successfully!<br/>' . date('Y-m-d H:i:s');
        } else {

            echo 'Record already exist!<br/>' . date('Y-m-d H:i:s');
        }


    }


    /** ************************************************************ FIXES ********************************************   */
    /** BATCH Credit Sales Entries  */
    function batch_pos_generate_invoices($shiftID, $billNo)
    {
        /** Create Invoice Header */
        $this->load->library('sequence');

        $q = "INSERT INTO srp_erp_customerinvoicemaster (
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
                    'Direct' AS invoiceType,
                    'CINV' AS documentID,
                    DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" ) AS invoiceDate,
                    DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" ) AS invoiceDueDate,
                    DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" ) AS customerInvoiceDate,
                    0 AS invoiceCode,
                    srp_erp_pos_menusalesmaster.invoiceCode AS referenceNo,
                    concat( 'POS Credit Sales - ', srp_erp_pos_menusalesmaster.invoiceCode ) AS invoiceNarration,
                    getCompanyFinanceYearID ( srp_erp_pos_shiftdetails.companyID ) AS companyFinanceYearID,
                    concat( FY.beginingDate, ' - ', FY.endingDate ) AS companyFinanceYear,
                    FY.beginingDate AS FYBegin,
                    FY.endingDate AS FYEnd,
                    getCompanyFinancePeriodID ( srp_erp_pos_shiftdetails.companyID ) AS companyFinancePeriodID,
                    srp_erp_pos_menusalespayments.customerAutoID AS customerID,
                    srp_erp_customermaster.customerSystemCode AS customerSystemCode,
                    srp_erp_customermaster.customerName AS customerName,
                    srp_erp_customermaster.receivableAutoID AS customerReceivableAutoID,
                    srp_erp_customermaster.receivableSystemGLCode AS customerReceivableSystemGLCode,
                    srp_erp_customermaster.receivableGLAccount AS customerReceivableGLAccount,
                    srp_erp_customermaster.receivableDescription AS customerReceivableDescription,
                    srp_erp_customermaster.receivableType AS customerReceivableType,
                    srp_erp_pos_menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    srp_erp_pos_menusalesmaster.transactionCurrency AS transactionCurrency,
                    srp_erp_pos_menusalesmaster.transactionExchangeRate AS transactionExchangeRate,
                    Sum( srp_erp_pos_menusalespayments.amount ) AS transactionAmount,
                    srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.companyLocalCurrencyID,
                    srp_erp_pos_menusalesmaster.companyLocalCurrency,
                    srp_erp_pos_menusalesmaster.companyLocalExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.companyLocalExchangeRate ) AS companyLocalAmount,
                    srp_erp_pos_menusalesmaster.companyLocalCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.companyReportingCurrencyID,
                    srp_erp_pos_menusalesmaster.companyReportingCurrency,
                    srp_erp_pos_menusalesmaster.companyReportingExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.companyReportingExchangeRate ) AS companyReportingAmount,
                    srp_erp_pos_menusalesmaster.companyReportingCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.customerCurrencyID,
                    srp_erp_pos_menusalesmaster.customerCurrency,
                    srp_erp_pos_menusalesmaster.customerCurrencyExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.customerCurrencyExchangeRate ) AS customerCurrencyAmount,
                    srp_erp_pos_menusalesmaster.customerCurrencyDecimalPlaces,
                    1 AS confirmedYN,
                    srp_erp_pos_shiftdetails.createdUserID AS confirmedByEmpID,
                    srp_erp_pos_shiftdetails.createdUserName AS confirmedByName,
                    srp_erp_pos_shiftdetails.startTime AS confirmedDate,
                    1 AS approvedYN,
                    srp_erp_pos_shiftdetails.startTime AS approvedDate,
                    srp_erp_pos_shiftdetails.createdUserID AS approvedbyEmpID,
                    srp_erp_pos_shiftdetails.createdUserName AS approvedbyEmpName,
                    srp_erp_pos_menusalesmaster.segmentID AS segmentID,
                    srp_erp_pos_menusalesmaster.segmentCode AS segmentCode,
                    srp_erp_pos_menusalesmaster.companyID,
                    srp_erp_pos_menusalesmaster.companyCode,
                    srp_erp_pos_shiftdetails.createdUserGroup,
                    srp_erp_pos_shiftdetails.createdPCID,
                    srp_erp_pos_shiftdetails.createdUserID,
                    srp_erp_pos_shiftdetails.startTime AS createdDateTime,
                    srp_erp_pos_shiftdetails.createdUserName,
                    srp_erp_pos_shiftdetails.`timestamp` AS `timestamp` 
                FROM
                    srp_erp_pos_menusalesmaster
                    LEFT JOIN srp_erp_pos_menusalespayments ON srp_erp_pos_menusalespayments.menuSalesID = srp_erp_pos_menusalesmaster.menuSalesID
                    LEFT JOIN srp_erp_pos_shiftdetails ON srp_erp_pos_menusalesmaster.shiftID = srp_erp_pos_shiftdetails.shiftID
                    LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID
                    LEFT JOIN srp_erp_companyfinanceyear FY ON FY.companyFinanceYearID = getCompanyFinanceYearID ( srp_erp_pos_shiftdetails.companyID ) 
                WHERE
                    srp_erp_pos_menusalesmaster.shiftID = '" . $shiftID . "' 
                    AND srp_erp_pos_menusalesmaster.isCreditSales = 1 
                    AND srp_erp_pos_menusalespayments.paymentConfigMasterID = 7 
                    AND srp_erp_pos_menusalesmaster.isVoid = 0
                    AND srp_erp_pos_menusalesmaster.isHold = 0
                    AND srp_erp_pos_menusalesmaster.isHold = '" . $billNo . "'                
                GROUP BY
                    srp_erp_pos_menusalesmaster.menuSalesID 
                ORDER BY
                    srp_erp_pos_menusalesmaster.menuSalesID DESC 
                    )";


        $this->db->query($q);
        $insert_id = $this->db->insert_id();
        $row_count = $this->db->affected_rows();
        $result = array();
        $i = 0;
        while (true) {
            if ($row_count == $i) {
                break;

            } else if ($i > 99) {
                break;
            }
            $result[$i] = $insert_id;
            $insert_id++;
            $i++;
        }
        if (!empty($result)) {
            $tmpData = array();
            $i2 = 0;
            $where = ' WHERE (';
            foreach ($result as $id) {
                $tmpData[$i2]['invoiceAutoID'] = $id;
                $tmpData[$i2]['invoiceCode'] = $this->sequence->sequence_generator('CINV');
                $where .= ' srp_erp_customerinvoicemaster.invoiceAutoID = ' . $id . ' OR';
                $i2++;
            }
            $where = trim($where, ' OR');
            $where .= ')';
            //var_dump($tmpData);
            $this->db->update_batch('srp_erp_customerinvoicemaster', $tmpData, 'invoiceAutoID');

            if ($row_count > 0) {
                /** Create Invoice Detail */
                $q = "INSERT INTO srp_erp_customerinvoicedetails (
                    invoiceAutoID,
                    `type`,
                    description,
                    transactionAmount,
                    companyLocalAmount,
                    companyReportingAmount,
                    customerAmount,
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
                        srp_erp_customerinvoicemaster.referenceNo AS description,
                        srp_erp_customerinvoicemaster.transactionAmount,
                        srp_erp_customerinvoicemaster.companyLocalAmount,
                        srp_erp_customerinvoicemaster.companyReportingAmount,
                        srp_erp_customerinvoicemaster.customerCurrencyAmount,
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
                    " . $where . " LIMIT " . $row_count . " )";
                //echo $q;
                $this->db->query($q);

                /** update menusales master  */
                $q2 = "UPDATE srp_erp_pos_menusalesmaster AS t1,
                        (
                        SELECT
                            srp_erp_pos_menusalesmaster.menuSalesID AS menuSalesID,
                            srp_erp_customerinvoicemaster.invoiceCode AS invoiceCode,
                            srp_erp_customerinvoicemaster.invoiceAutoID AS  invoiceAutoID
                        FROM
                            srp_erp_pos_menusalesmaster
                            INNER JOIN srp_erp_customerinvoicemaster ON srp_erp_pos_menusalesmaster.invoiceCode = srp_erp_customerinvoicemaster.referenceNo 
                        WHERE
                            srp_erp_pos_menusalesmaster.shiftID =  '" . $shiftID . "'  AND  srp_erp_pos_menusalesmaster.shiftID =  '" . $billNo . "'
                            ) AS t2 
                            SET t1.documentSystemCode = t2.invoiceCode ,   t1.documentMasterAutoID= t2.invoiceAutoID 
                        WHERE
                            t1.menuSalesID = t2.menuSalesID 
                            AND t1.shiftID =  '" . $shiftID . "' ";
                $this->db->query($q2);
            }

        }
        //var_dump($result);
        return $result;
    }


    /** BATCH ITEM LEDGER  */
    function batch_update_itemLedger($shiftID, $isCreditSales = true)
    {
        $this->db->select("*");
        $this->db->from("srp_erp_pos_shiftdetails");
        $this->db->where("shiftID", $shiftID);

        $startTime = date('Y-m-d', strtotime($this->db->get()->row('startTime')));
        $curDate = $startTime; //date('Y-m-d');
        $q = "SELECT
                    financeyear.companyFinanceYearID AS companyFinanceYearID,
                CONCAT(financeyear.beginingDate , \" - \" , financeyear.endingDate ) AS companyFinanceYear,
                financeyear.beginingDate AS FYBegin,
                financeyear.endingDate  AS FYEnd,
                financeperiod.dateFrom AS FYPeriodDateFrom,
                financeperiod.dateTo  AS FYPeriodDateTo
                
                FROM
                    srp_erp_companyfinanceyear financeyear
                INNER JOIN  srp_erp_companyfinanceperiod financeperiod  ON financeperiod.companyFinanceYearID = financeyear.companyFinanceYearID
                WHERE
                    financeyear.companyID = '" . current_companyID() . "'
                AND financeyear.isActive = 1
                AND financeyear.beginingDate < '" . $curDate . "'
                AND financeyear.endingDate > '" . $curDate . "'
                AND financeperiod.isActive =1
                AND financeperiod.dateFrom < '" . $curDate . "'
                AND financeperiod.dateTo > '" . $curDate . "'";
        $financeYear = $this->db->query($q)->row_array();

        $companyFinanceYearID = isset($financeYear['companyFinanceYearID']) ? $financeYear['companyFinanceYearID'] : null;
        $companyFinanceYear = isset($financeYear['companyFinanceYear']) ? $financeYear['companyFinanceYear'] : null;
        $FYBegin = isset($financeYear['FYBegin']) ? $financeYear['FYBegin'] : null;
        $FYEnd = isset($financeYear['FYEnd']) ? $financeYear['FYEnd'] : null;
        $FYPeriodDateFrom = isset($financeYear['FYPeriodDateFrom']) ? $financeYear['FYPeriodDateFrom'] : null;
        $FYPeriodDateTo = isset($financeYear['FYPeriodDateTo']) ? $financeYear['FYPeriodDateTo'] : null;


        $q = "INSERT INTO srp_erp_itemledger (
                documentID,
                documentAutoID,
                documentCode,
                documentSystemCode,
                documentDate,
                companyFinanceYearID,
                companyFinanceYear,
                FYBegin,
                FYEnd,
                FYPeriodDateFrom,
                FYPeriodDateTo,
                wareHouseAutoID,
                wareHouseCode,
                wareHouseLocation,
                wareHouseDescription,
                itemAutoID,
                itemSystemCode,
                ItemSecondaryCode,
                itemDescription,
                defaultUOMID,
                defaultUOM,
                transactionUOMID,
                transactionUOM,
                transactionQTY,
                convertionRate,
                currentStock,
                PLGLAutoID,
                PLSystemGLCode,
                PLGLCode,
                PLDescription,
                PLType,
                BLGLAutoID,
                BLSystemGLCode,
                BLGLCode,
                BLDescription,
                BLType,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalWacAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingWacAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                narration,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp`
            )(
                SELECT
                    'POSR' AS documentID,
                    menusalesmaster.shiftID AS documentAutoID,
                    'POSR' AS documentCode,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d')  AS documentDate,
                    '" . $companyFinanceYearID . "' AS companyFinanceYearID,
                    '" . $companyFinanceYear . "' AS companyFinanceYear,
                    '" . $FYBegin . "' AS FYBegin,
                    '" . $FYEnd . "' AS FYEnd,
                    '" . $FYPeriodDateFrom . "' AS FYPeriodDateFrom,
                    '" . $FYPeriodDateTo . "' AS FYPeriodDateTo,
                    warehousemaster.wareHouseAutoID AS wareHouseAutoID,
                    warehousemaster.wareHouseCode AS wareHouseCode,
                    warehousemaster.wareHouseLocation AS wareHouseLocation,
                    warehousemaster.wareHouseDescription AS wareHouseDescription,
                    itemdetail.itemAutoID AS itemAutoID,
                    itemmaster.itemSystemCode AS itemSystemCode,
                    itemmaster.seconeryItemCode AS seconeryItemCode,
                    itemmaster.itemDescription AS itemDescription,
                    itemmaster.defaultUnitOfMeasureID AS defaultUOMID,
                    itemmaster.defaultUnitOfMeasure AS defaultUOM,
                    itemdetail.UOMID AS transactionUOMID,
                    itemdetail.UOM AS transactionUOM,
                    sum(itemdetail.menuSalesQty * itemdetail.qty) *- 1 AS transactionQTY,
                    getUoMConvertion (
                        itemdetail.UOMID,
                        itemmaster.defaultUnitOfMeasureID,
                        menusalesmaster.companyID
                    ) AS convertionRate,
                    itemmaster.currentStock AS currentStock,
                    itemmaster.costGLAutoID AS PLGLAutoID,
                    itemmaster.costSystemGLCode AS PLSystemGLCode,
                    itemmaster.costGLCode AS PLGLCode,
                    itemmaster.costDescription AS PLDescription,
                    itemmaster.costType AS PLType,
                    itemmaster.assteGLAutoID AS BLGLAutoID,
                    itemmaster.assteSystemGLCode AS BLSystemGLCode,
                    itemmaster.assteGLCode AS BLGLCode,
                    itemmaster.assteDescription AS BLDescription,
                    itemmaster.assteType AS BLType,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        ifnull(
                            itemdetail.cost * (
                                itemdetail.menuSalesQty
                            ),
                            0
                        ) *- 1
                    ) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            sum(
                                ifnull(
                                    itemdetail.cost * (
                                         itemdetail.menuSalesQty
                                    ),
                                    0
                                )
                            )
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        ) *- 1
                    ) AS companyLocalAmount,
                    itemmaster.companyLocalWacAmount AS companyLocalWacAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            ifnull(
                                itemdetail.cost * (
                                    itemdetail.menuSalesQty
                                ),
                                0
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        ) *- 1
                    ) AS companyReportingAmount,
                    itemmaster.companyLocalWacAmount / getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingWacAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    'POS Sales' AS narration,
                    shiftDetail.createdUserGroup AS createdUserGroup,
                    shiftDetail.createdPCID AS createdPCID,
                    shiftDetail.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    shiftDetail.createdUserName AS createdUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesitemdetails AS itemdetail
                
                LEFT JOIN srp_erp_itemmaster AS itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 ";

        if($isCreditSales){
            $q .= " AND menusalesmaster.isCreditSales = 0";
        }

        $q .= " AND menusalesmaster.isVoid = 0  
                GROUP BY
                    itemdetail.itemAutoID
            )";

        $result = $this->db->query($q);
        return $result;
    }

    /** BATCH ITEM LEDGER  */
    function batch_update_itemLedger_creditSales($shiftID, $billNo)
    {
        $this->db->select("*");
        $this->db->from("srp_erp_pos_shiftdetails");
        $this->db->where("shiftID", $shiftID);

        $startTime = date('Y-m-d', strtotime($this->db->get()->row('startTime')));
        $curDate = $startTime; //date('Y-m-d');

        $q = "SELECT
                    financeyear.companyFinanceYearID AS companyFinanceYearID,
                CONCAT(financeyear.beginingDate , \" - \" , financeyear.endingDate ) AS companyFinanceYear,
                financeyear.beginingDate AS FYBegin,
                financeyear.endingDate  AS FYEnd,
                financeperiod.dateFrom AS FYPeriodDateFrom,
                financeperiod.dateTo  AS FYPeriodDateTo
                
                FROM
                    srp_erp_companyfinanceyear financeyear
                INNER JOIN  srp_erp_companyfinanceperiod financeperiod  ON financeperiod.companyFinanceYearID = financeyear.companyFinanceYearID
                WHERE
                    financeyear.companyID = '" . current_companyID() . "'
                AND financeyear.isActive = 1
                AND financeyear.beginingDate < '" . $curDate . "'
                AND financeyear.endingDate > '" . $curDate . "'
                AND financeperiod.isActive =1
                AND financeperiod.dateFrom < '" . $curDate . "'
                AND financeperiod.dateTo > '" . $curDate . "'";
        $financeYear = $this->db->query($q)->row_array();

        $companyFinanceYearID = isset($financeYear['companyFinanceYearID']) ? $financeYear['companyFinanceYearID'] : null;
        $companyFinanceYear = isset($financeYear['companyFinanceYear']) ? $financeYear['companyFinanceYear'] : null;
        $FYBegin = isset($financeYear['FYBegin']) ? $financeYear['FYBegin'] : null;
        $FYEnd = isset($financeYear['FYEnd']) ? $financeYear['FYEnd'] : null;
        $FYPeriodDateFrom = isset($financeYear['FYPeriodDateFrom']) ? $financeYear['FYPeriodDateFrom'] : null;
        $FYPeriodDateTo = isset($financeYear['FYPeriodDateTo']) ? $financeYear['FYPeriodDateTo'] : null;


        $q = "INSERT INTO srp_erp_itemledger (
                documentID,
                documentAutoID,
                documentCode,
                documentSystemCode,
                documentDate,
                companyFinanceYearID,
                companyFinanceYear,
                FYBegin,
                FYEnd,
                FYPeriodDateFrom,
                FYPeriodDateTo,
                wareHouseAutoID,
                wareHouseCode,
                wareHouseLocation,
                wareHouseDescription,
                itemAutoID,
                itemSystemCode,
                ItemSecondaryCode,
                itemDescription,
                defaultUOMID,
                defaultUOM,
                transactionUOMID,
                transactionUOM,
                transactionQTY,
                convertionRate,
                currentStock,
                PLGLAutoID,
                PLSystemGLCode,
                PLGLCode,
                PLDescription,
                PLType,
                BLGLAutoID,
                BLSystemGLCode,
                BLGLCode,
                BLDescription,
                BLType,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalWacAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingWacAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                narration,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp`
            )(
                SELECT
                    'CINV' AS documentID,
                    menusalesmaster.shiftID AS documentAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentSystemCode AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d')  AS documentDate,
                    '" . $companyFinanceYearID . "' AS companyFinanceYearID,
                    '" . $companyFinanceYear . "' AS companyFinanceYear,
                    '" . $FYBegin . "' AS FYBegin,
                    '" . $FYEnd . "' AS FYEnd,
                    '" . $FYPeriodDateFrom . "' AS FYPeriodDateFrom,
                    '" . $FYPeriodDateTo . "' AS FYPeriodDateTo,
                    warehousemaster.wareHouseAutoID AS wareHouseAutoID,
                    warehousemaster.wareHouseCode AS wareHouseCode,
                    warehousemaster.wareHouseLocation AS wareHouseLocation,
                    warehousemaster.wareHouseDescription AS wareHouseDescription,
                    itemdetail.itemAutoID AS itemAutoID,
                    itemmaster.itemSystemCode AS itemSystemCode,
                    itemmaster.seconeryItemCode AS seconeryItemCode,
                    itemmaster.itemDescription AS itemDescription,
                    itemmaster.defaultUnitOfMeasureID AS defaultUOMID,
                    itemmaster.defaultUnitOfMeasure AS defaultUOM,
                    itemdetail.UOMID AS transactionUOMID,
                    itemdetail.UOM AS transactionUOM,
                    sum(itemdetail.qty * itemdetail.menuSalesQty) *- 1 AS transactionQTY,
                    getUoMConvertion (
                        itemdetail.UOMID,
                        itemmaster.defaultUnitOfMeasureID,
                        menusalesmaster.companyID
                    ) AS convertionRate,
                    itemmaster.currentStock AS currentStock,
                    itemmaster.costGLAutoID AS PLGLAutoID,
                    itemmaster.costSystemGLCode AS PLSystemGLCode,
                    itemmaster.costGLCode AS PLGLCode,
                    itemmaster.costDescription AS PLDescription,
                    itemmaster.costType AS PLType,
                    itemmaster.assteGLAutoID AS BLGLAutoID,
                    itemmaster.assteSystemGLCode AS BLSystemGLCode,
                    itemmaster.assteGLCode AS BLGLCode,
                    itemmaster.assteDescription AS BLDescription,
                    itemmaster.assteType AS BLType,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        ifnull(
                            itemdetail.cost * (
                                itemdetail.menuSalesQty
                            ),
                            0
                        ) *- 1
                    ) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            sum(
                                ifnull(
                                    itemdetail.cost * (
                                         itemdetail.menuSalesQty
                                    ),
                                    0
                                )
                            )
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        ) *- 1
                    ) AS companyLocalAmount,
                    itemmaster.companyLocalWacAmount AS companyLocalWacAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            ifnull(
                                itemdetail.cost * (
                                    itemdetail.menuSalesQty
                                ),
                                0
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        ) *- 1
                    ) AS companyReportingAmount,
                    itemmaster.companyLocalWacAmount / getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingWacAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    'POS Sales' AS narration,
                    shiftDetail.createdUserGroup AS createdUserGroup,
                    shiftDetail.createdPCID AS createdPCID,
                    shiftDetail.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    shiftDetail.createdUserName AS createdUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesitemdetails AS itemdetail
                LEFT JOIN srp_erp_itemmaster AS itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0   AND menusalesmaster.isCreditSales = 1
                AND menusalesmaster.isVoid = 0
                AND menusalesmaster.menuSalesID = '" . $billNo . "' 
                GROUP BY
                    itemdetail.itemAutoID
            )";

        $result = $this->db->query($q);
        return $result;
    }

    /** for manual Entries  : requested by Hisham to update the old entries  */
    function pos_batch_general_ledger($shiftID)
    {

        $this->db->select('*');
        $this->db->from('srp_erp_generalledger');
        $this->db->where('documentCode', 'POSR');
        $this->db->where('documentMasterAutoID', $shiftID);
        $result = $this->db->get()->result_array();
        if (empty($result)) {
            /** 0. CUSTOMER INVOICE - Credit Sales Entries  */
            //$this->pos_generate_invoices($shiftID);
            /** 1. CREDIT SALES  - REVENUE */
            $this->update_revenue_generalLedger_credit_sales($shiftID);
            /** 2. CREDIT SALES  - COGS */
            $this->update_cogs_generalLedger_credit_sales($shiftID);
            /** 3. CREDIT SALES  - INVENTORY */
            $this->update_inventory_generalLedger_credit_sales($shiftID);
            /** 4.  CREDIT SALES - TAX */
            $this->update_tax_generalLedger_credit_sales($shiftID);
            /** 5.  CREDIT SALES - COMMISSION EXPENSE  */
            $this->update_commissionExpense_generalLedger_credit_sales($shiftID);
            /** 6.  CREDIT SALES - COMMISSION PAYABLE */
            $this->update_commissionPayable_generalLedger_credit_sales($shiftID);
            /** 7.  CREDIT SALES - ROYALTY PAYABLE */
            $this->update_royaltyPayable_generalLedger_credit_sales($shiftID);
            /** 8.  CREDIT SALES - ROYALTY EXPENSES */
            $this->update_royaltyExpenses_generalLedger_credit_sales($shiftID);
            /** 9. CREDIT SALES -  SERVICE CHARGE */
            $this->update_serviceCharge_generalLedger_credit_sales($shiftID);
            /** 10. CREDIT SALES -  CREDIT CUSTOMER PAYMENTS */

            /** CREDIT SALES - ITEM LEDGER  */
            //$this->update_itemLedger_credit_sales($shiftID);

            echo 'Run Successfully!<br/>' . date('Y-m-d H:i:s');
        } else {

            echo 'Record already exist!<br/>' . date('Y-m-d H:i:s');
        }


    }


    /**Credit Sales Double Entry - Bill wise  */
    /** 1. CREDIT SALES  - REVENUE */
    function update_revenue_generalLedger_credit_sales_bill($shiftID, $billNo)
    {
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
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
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
                timestamp
            ) (SELECT
                    'CINV' AS documentCode,
                menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                menusalesmaster.documentSystemCode  AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                concat('POS Credit Sales - Revenue',' - ',menusalesmaster.invoiceCode) AS documentNarration,
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
                abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                (( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID )  )) AS companyLocalAmount, getDecimalPlaces ( company.company_default_currencyID  ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID as companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                (( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID )  )) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                'CUS' AS partyType,
                customermaster.customerAutoID AS partyAutoID,
                customermaster.customerSystemCode AS partySystemCode,
                customermaster.customerName AS partyName,
                customermaster.customerCurrencyID AS partyCurrencyID,
                customermaster.customerCurrency AS partyCurrency,
                getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                shiftDetail.createdUserGroup AS createdUserGroup,
            shiftDetail.createdPCID as  createdPCID,
            shiftDetail.createdUserID as  createdUserID,
            shiftDetail.startTime as createdDateTime,
            shiftDetail.createdUserName as createdUserName,
            NULL AS modifiedPCID,
            NULL AS modifiedUserID,
            NULL AS modifiedDateTime,
            null AS modifiedUserName,
            CURRENT_TIMESTAMP() as `timestamp`
            FROM
                srp_erp_pos_menusalesitems item
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.revenueGLAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
            LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "' and menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 1 AND menusalesmaster.menuSalesID = '" . $billNo . "'
            GROUP BY
                revenueGLAutoID, menusalesmaster.menuSalesID);";

        $result = $this->db->query($q);
        //echo $this->db->last_query();
        return $result;
    }

    /** 2. CREDIT SALES  - COGS */
    function update_cogs_generalLedger_credit_sales_bill($shiftID, $billNo)
    {
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
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
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
            )
            
            (SELECT
                'CINV' AS documentCode,
                menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                menusalesmaster.documentSystemCode AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                concat('POS Credit Sales - COGS',' - ',menusalesmaster.invoiceCode) AS documentNarration,
                '' AS chequeNumber,
                item.costGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'dr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                  ( sum( IFNULL(item.cost,0) * item.menuSalesQty    ) )  / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                  ( sum( IFNULL(item.cost,0) *  item.menuSalesQty    ) )  / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                'CUS' AS partyType,
                customermaster.customerAutoID AS partyAutoID,
                customermaster.customerSystemCode AS partySystemCode,
                customermaster.customerName AS partyName,
                customermaster.customerCurrencyID AS partyCurrencyID,
                customermaster.customerCurrency AS partyCurrency,
                getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                menusalesmaster.createdUserGroup AS createdUserGroup,
                menusalesmaster.createdPCID AS createdPCID,
                menusalesmaster.createdUserID AS createdUserID,
                shiftDetail.startTime AS createdDateTime,
                menusalesmaster.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP() AS  `timestamp`
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.costGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM AND uom.companyID = menusalesmaster.companyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
            LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "'
            AND menusalesmaster.isHold = 0   AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 1
             AND menusalesmaster.menuSalesID = '" . $billNo . "'
            GROUP BY
                item.costGLAutoID, menusalesmaster.menuSalesID)";

        $result = $this->db->query($q);
        return $result;
    }

    /** 3. CREDIT SALES  - INVENTORY */
    function update_inventory_generalLedger_credit_sales_bill($shiftID, $billNo)
    {
        $q = "INSERT INTO srp_erp_generalledger(
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
                timestamp
            )
            (SELECT
                'CINV' AS documentCode,
                menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                menusalesmaster.documentSystemCode AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                concat('POS Credit Sales - Inventory', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                '' AS chequeNumber,
                item.assetGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                abs(( sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) )) *- 1 AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                ( abs( sum( IFNULL(item.cost,0) * item.menuSalesQty    ) ) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                ( abs( sum( IFNULL(item.cost,0) *  item.menuSalesQty    ) ) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                'CUS' AS partyType,
                customermaster.customerAutoID AS partyAutoID,
                customermaster.customerSystemCode AS partySystemCode,
                customermaster.customerName AS partyName,
                customermaster.customerCurrencyID AS partyCurrencyID,
                customermaster.customerCurrency AS partyCurrency,
                getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                abs(( sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) )) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                menusalesmaster.createdUserGroup AS createdUserGroup,
                menusalesmaster.createdPCID AS createdPCID,
                menusalesmaster.createdUserID AS createdUserID,
                shiftDetail.startTime AS createdDateTime,
                menusalesmaster.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP () `timestamp`
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.assetGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM AND uom.companyID = menusalesmaster.companyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
            LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "'
            AND menusalesmaster.isHold = 0   AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 1
            AND menusalesmaster.menuSalesID = '" . $billNo . "'
            GROUP BY
                item.assetGLAutoID, menusalesmaster.menuSalesID)";

        $result = $this->db->query($q);
        return $result;
    }

    /** 4.  CREDIT SALES - TAX */
    function update_tax_generalLedger_credit_sales_bill($shiftID, $billNo)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
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
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - TAX', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    menusalesTax.GLCode AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(
                        sum(
                            ifnull(menusalesTax.taxAmount, 0)
                        )
                    ) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_reporting_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
                    customermaster.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                    abs(
                        sum(
                            ifnull(menusalesTax.taxAmount, 0)
                        )
                    ) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalestaxes menusalesTax
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = menusalesTax.menuSalesID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = menusalesTax.GLCode
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 1
                AND menusalesmaster.menuSalesID = '" . $billNo . "'
                GROUP BY
                    chartOfAccount.GLAutoID, menusalesmaster.menuSalesID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 5.  CREDIT SALES - COMMISSION EXPENSE  */
    function update_commissionExpense_generalLedger_credit_sales_bill($shiftID, $billNo)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
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
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Sales Commission', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    customers.expenseGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        menusalesmaster.deliveryCommissionAmount
                    ) AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        sum(
                            menusalesmaster.deliveryCommissionAmount
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            IFNULL(menusalesmaster.deliveryCommissionAmount,0)
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
                    customermaster.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                    sum( menusalesmaster.deliveryCommissionAmount ) / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = menusalesmaster.deliveryPersonID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customers.expenseGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
                AND (
                    menusalesmaster.deliveryCommission IS NOT NULL
                    AND menusalesmaster.deliveryCommission <> 0
                )
                AND menusalesmaster.isDelivery = 1 AND menusalesmaster.isCreditSales = 1
                AND menusalesmaster.menuSalesID = '" . $billNo . "'
                GROUP BY
                    customers.expenseGLAutoID, menusalesmaster.menuSalesID)";

        $result = $this->db->query($q);
        return $result;
    }

    /** 6.  CREDIT SALES - COMMISSION PAYABLE */
    function update_commissionPayable_generalLedger_credit_sales_bill($shiftID, $billNo)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                    documentCode,
                    documentMasterAutoID,
                    documentSystemCode,
                    documentDate,
                    documentYear,
                    documentMonth,
                    documentNarration,
                    GLAutoID,
                    systemGLCode,
                    GLCode,
                    GLDescription,
                    GLType,
                    amount_type,
                    isFromItem,
                    transactionCurrency,
                    transactionExchangeRate,
                    transactionAmount,
                    transactionCurrencyID,
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
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Sales Commission Payable', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    customers.liabilityGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    ABS(
                        sum(
                            menusalesmaster.deliveryCommissionAmount
                        )
                    ) * - 1 AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        ABS(
                            sum(
                                menusalesmaster.deliveryCommissionAmount
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        ABS(
                            sum(
                               IFNULL( menusalesmaster.deliveryCommissionAmount,0)
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
					customermaster.customerAutoID AS partyAutoID,
					customermaster.customerSystemCode AS partySystemCode,
					customermaster.customerName AS partyName,
					customermaster.customerCurrencyID AS partyCurrencyID,
					customermaster.customerCurrency AS partyCurrency,
					getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
					ABS(
                        sum(
                            menusalesmaster.deliveryCommissionAmount
                        )
                    ) * - 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
					customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = menusalesmaster.deliveryPersonID
                
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customers.liabilityGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
                AND (
                    menusalesmaster.deliveryCommission IS NOT NULL
                    AND menusalesmaster.deliveryCommission <> 0
                )
                AND menusalesmaster.isDelivery = 1
                AND menusalesmaster.isOnTimeCommision = 0 AND menusalesmaster.isCreditSales = 1
                AND menusalesmaster.menuSalesID = '" . $billNo . "'
                GROUP BY
                    customers.liabilityGLAutoID, menusalesmaster.menuSalesID
                );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 7.  CREDIT SALES - ROYALTY PAYABLE */
    function update_royaltyPayable_generalLedger_credit_sales_bill($shiftID, $billNo)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
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
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Royalty Payable', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    franchisemaster.royaltyLiabilityGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    ABS(
                        sum(
                            (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) * - 1 AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        ABS(
                            sum(
                                (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                    franchisemaster.royaltyPercentage / 100
                                )
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        ABS(
                            sum(
                                (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                    franchisemaster.royaltyPercentage / 100
                                )
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
					customermaster.customerAutoID AS partyAutoID,
					customermaster.customerSystemCode AS partySystemCode,
					customermaster.customerName AS partyName,
					customermaster.customerCurrencyID AS partyCurrencyID,
					customermaster.customerCurrency AS partyCurrency,
					getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
					ABS(
                        sum(
                            (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) * - 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
					customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                INNER JOIN srp_erp_pos_franchisemaster franchisemaster ON franchisemaster.warehouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = franchisemaster.royaltyLiabilityGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 1
                AND menusalesmaster.menuSalesID = '" . $billNo . "'
                GROUP BY
                    franchisemaster.royaltyLiabilityGLAutoID, menusalesmaster.menuSalesID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 8.  CREDIT SALES - ROYALTY EXPENSES */
    function update_royaltyExpenses_generalLedger_credit_sales_bill($shiftID, $billNo)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
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
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Royalty Expenses', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    franchisemaster.royaltyExpenseGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        ( IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0) ) * (
                            franchisemaster.royaltyPercentage / 100
                        )
                    ) AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        sum(
                            ( menusalesmaster.netTotal + menusalesmaster.serviceCharge ) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            ( menusalesmaster.netTotal + menusalesmaster.serviceCharge ) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
					customermaster.customerAutoID AS partyAutoID,
					customermaster.customerSystemCode AS partySystemCode,
					customermaster.customerName AS partyName,
					customermaster.customerCurrencyID AS partyCurrencyID,
					customermaster.customerCurrency AS partyCurrency,
					getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
					sum(
                        ( IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0) ) * (
                            franchisemaster.royaltyPercentage / 100
                        )
                    ) / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
					customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                INNER JOIN srp_erp_pos_franchisemaster franchisemaster ON franchisemaster.warehouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = franchisemaster.royaltyExpenseGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 1
                AND menusalesmaster.menuSalesID = '" . $billNo . "'
                GROUP BY
                    franchisemaster.royaltyExpenseGLAutoID, menusalesmaster.menuSalesID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 9. CREDIT SALES -  SERVICE CHARGE */
    function update_serviceCharge_generalLedger_credit_sales_bill($shiftID, $billNo)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
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
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Service Charge', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    servicecharge.GLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(
                        sum(
                            ifnull(
                                servicecharge.serviceChargeAmount,
                                0
                            )
                        )
                    ) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(
                                        servicecharge.serviceChargeAmount,
                                        0
                                    )
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(
                                        servicecharge.serviceChargeAmount,
                                        0
                                    )
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_reporting_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    
                    'CUS' AS partyType,
                    customermaster.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                    abs(
                        sum(
                            ifnull(
                                servicecharge.serviceChargeAmount,
                                0
                            )
                        )
                    ) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesservicecharge servicecharge
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = servicecharge.menuSalesID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = servicecharge.GLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID)
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 1
                AND menusalesmaster.menuSalesID = '" . $billNo . "'
                GROUP BY
                    chartOfAccount.GLAutoID, menusalesmaster.menuSalesID
            )";

        $result = $this->db->query($q);
        return $result;
    }

    /** 10. CREDIT SALES -  CREDIT CUSTOMER PAYMENTS */
    function update_creditSales_generalLedger_credit_sales_bill($shiftID, $billNo)
    {
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
            )(
                SELECT
                    'CINV' AS documentCode,
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    '' AS chequeNumber,
                    chartOfAccount.GLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    SUM(payments.amount) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
                    payments.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate (
                        customermaster.customerCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS partyExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            customermaster.customerCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    3 AS subLedgerType,
                    'AR' AS subLedgerDesc,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID createdUserID,
                    shiftDetail.startTime createdDateTime,
                    menusalesmaster.createdUserName createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP() `timestamp`
                FROM
                    srp_erp_pos_menusalespayments payments
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON payments.menuSalesID = menusalesmaster.menuSalesID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = payments.customerAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customermaster.receivableAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0
                AND menusalesmaster.isVoid = 0
                AND payments.paymentConfigMasterID = 7 AND menusalesmaster.isCreditSales = 1
                AND menusalesmaster.menuSalesID = '" . $billNo . "'
                GROUP BY
                    chartOfAccount.GLAutoID,
                    payments.customerAutoID, menusalesmaster.menuSalesID
            )";

        $result = $this->db->query($q);
        return $result;
    }

}