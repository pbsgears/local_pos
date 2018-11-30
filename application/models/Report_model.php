<?php
/*
-- =============================================
-- File Name : Report_model.php
-- Project Name : SME ERP
-- Module Name : Report
-- Author : Mohamed Mubashir
-- Create date : 15 - September 2016
-- Description : This file contains all the report module queries.

-- REVISION HISTORY
-- Modified By : Shafri on 15-05-2017 sub item configuration in the reports
-- =============================================*/
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_model extends CI_Model
{
    function __contruct()
    {
        parent::__contruct();
    }

    function getColumnsByReport($reportCode)
    {
        $this->db->select("fieldName,caption,isDefault,textAlign,isMandatory,isCalculate");
        $this->db->from("srp_erp_reporttemplate rt");
        $this->db->join('srp_erp_reporttemplatefields rf', 'rt.reportID = rf.reportID', 'INNER');
        $this->db->where("rt.documentCode", $reportCode);
        $this->db->where("rf.isVisible", 1);
        $this->db->order_by("rf.sortOrder ASC");
        $result = $this->db->get()->result_array();
        return $result;
    }

    function getColumnsDetailByReport($reportCode, $columns)
    {
        if (!empty($columns)) {
            $this->db->select("fieldName,caption,isDefault,textAlign,isMandatory,isCalculate,isDecimalPlaceAllowed");
            $this->db->from("srp_erp_reporttemplate rt");
            $this->db->join('srp_erp_reporttemplatefields rf', 'rt.reportID = rf.reportID', 'INNER');
            $this->db->where_in("rf.fieldName", $columns);
            $this->db->where("rt.documentCode", $reportCode);
            $this->db->order_by("rf.sortOrder ASC");
            $result = $this->db->get()->result_array();
            return $result;
        } else {
            return false;
        }
    }

    function get_item_ledger_report()
    {
        $location = $this->input->post("location");
        $items = $this->input->post("itemTo");
        $i = 1;
        $itmesOR = '( ';
        if (!empty($items)) {
            foreach ($items as $item_val) {
                if ($i != 1) {
                    $itmesOR .= ' OR ';
                }
                $itmesOR .= " il.itemAutoID = '" . $item_val . "' "; /*generate the query according to selectd items*/
                $i++;
            }
        }
        $itmesOR .= ' ) ';
        $feilds = "";
        $feildsra = "";
        $feilds2 = "";
        $feilds3 = array();
        if (!empty($this->input->post("fieldNameChk"))) { /*generate the query according to selectd columns*/
            $fieldNameChk = $this->input->post("fieldNameChk");
            $key = array_search("transactionQTY", $fieldNameChk); // get key of transactionQTY in an array
            unset($fieldNameChk[$key]);
            foreach ($fieldNameChk as $val) {
                if ($val == "documentID") {
                    $feilds3[] = "'Opening Balance' as documentID";
                } else if ($val == "documentSystemCode") {
                    $feilds3[] = "'-' as documentSystemCode";
                } else if ($val == "documentDate") {
                    $feilds3[] = "'1970-01-01' as documentDate";
                } else if ($val == "segmentCode") {
                    $feilds3[] = "'-' as segmentCode";
                } else if ($val == "narration") {
                    $feilds3[] = "'-' as narration";
                } else if ($val == "referenceNumber") {
                    $feilds3[] = "'-' as referenceNumber";
                } else if ($val == "wareHouseLocation") {
                    $feilds3[] = "'-' as wareHouseLocation";
                } else {
                    $feilds3[] = 'il.' . $val;
                }
            }

            foreach ($fieldNameChk as $val) {
                if ($val == "wareHouseLocation") {
                    $feildsra[] = "srp_erp_warehousemaster.wareHouseLocation as wareHouseLocation";

                } else if ($val == "segmentCode") {
                    $feildsra[] = "srp_erp_segment.description as segmentCode";

                } else {
                    $feildsra[] = 'il.' . $val;
                }
            }
            $feilds3 = join(',', $feilds3);
            $feilds = join(',', $feildsra);
            $feilds .= ",SUM(il.transactionQTY/il.convertionRate) as transactionQTY";
            $feilds3 .= ",SUM(il.transactionQTY/il.convertionRate) as transactionQTY";
            $feilds2 = join(',a.', $fieldNameChk);
            $feilds2 = "a." . $feilds2;
            $feilds2 .= ",a.transactionQTY";
            if (in_array("companyLocalWacAmount", $fieldNameChk)) {
                $feilds .= ",SUM(il.companyLocalAmount) as localCostAsset";
                $feilds .= ",CL.DecimalPlaces as companyLocalWacAmountDecimalPlaces";
                $feilds .= ",(SUM(il.companyLocalAmount) / SUM(il.transactionQTY/il.convertionRate)) as avgCompanyLocalAmount";
                $feilds2 .= ",a.localCostAsset";
                $feilds2 .= ",a.companyLocalWacAmountDecimalPlaces";
                $feilds2 .= ",a.avgCompanyLocalAmount as avgCompanyLocalAmount";
                $feilds3 .= ",SUM(il.companyLocalAmount) as localCostAsset";
                $feilds3 .= ",CL.DecimalPlaces as companyLocalWacAmountDecimalPlaces";
                $feilds3 .= ",(SUM(il.companyLocalAmount) / SUM(il.transactionQTY/il.convertionRate)) as avgCompanyLocalAmount";
            }
            if (in_array("companyReportingWacAmount", $fieldNameChk)) {
                $feilds .= ",SUM(il.companyReportingAmount) as rptCostAsset";
                $feilds .= ",CR.DecimalPlaces as companyReportingWacAmountDecimalPlaces";
                $feilds .= ",(SUM(il.companyReportingAmount) / SUM(il.transactionQTY/il.convertionRate)) as avgCompanyReportingAmount";
                $feilds2 .= ",a.rptCostAsset";
                $feilds2 .= ",a.companyReportingWacAmountDecimalPlaces";
                $feilds2 .= ",a.avgCompanyReportingAmount";
                $feilds3 .= ",SUM(il.companyReportingAmount) as rptCostAsset";
                $feilds3 .= ",CR.DecimalPlaces as companyReportingWacAmountDecimalPlaces";
                $feilds3 .= ",(SUM(il.companyReportingAmount) / SUM(il.transactionQTY/il.convertionRate)) as avgCompanyReportingAmount";

            }
        }
        $result = $this->db->query("SELECT $feilds2, a.documentAutoID,a.salesPrice,a.itemDescription,a.itemSystemCode,a.transactionUOM,a.mainCategory,a.subCategory,a.companyLocalCurrencyDecimalPlaces,a.companyReportingCurrencyDecimalPlaces FROM ((SELECT $feilds,il.documentAutoID,il.salesPrice,il.itemDescription,il.itemSystemCode,il.transactionUOM,ic1.description as mainCategory,ic2.description as subCategory,il.companyLocalCurrencyDecimalPlaces,il.companyReportingCurrencyDecimalPlaces
        FROM srp_erp_itemledger il
        INNER JOIN
    `srp_erp_itemmaster` `im` ON `il`.`itemAutoID` = `im`.`itemAutoID` AND im.companyID = " . $this->common_data['company_data']['company_id'] . "
    LEFT JOIN srp_erp_warehousemaster ON srp_erp_warehousemaster.wareHouseAutoID = il.wareHouseAutoID
    LEFT JOIN srp_erp_segment ON srp_erp_segment.segmentID = il.segmentID
        INNER JOIN
    (SELECT
		description,
		itemCategoryID
	FROM
		srp_erp_itemcategory
	GROUP BY
		itemCategoryID) AS `ic1` ON (`ic1`.`itemCategoryID` = `im`.`mainCategoryID`)
        INNER JOIN
    (SELECT
		description,
		itemCategoryID
	FROM
		srp_erp_itemcategory
	GROUP BY
		itemCategoryID) AS `ic2` ON (`ic2`.`itemCategoryID` = `im`.`subcategoryID`) 
		 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = il.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = il.companyLocalCurrencyID) WHERE $itmesOR AND il.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "' AND il.companyID = " . $this->common_data['company_data']['company_id'] . " AND il.wareHouseAutoID IN (" . join(',', $location) . ") GROUP BY il.itemAutoID,il.documentID,il.documentSystemCode,il.warehouseAutoID ORDER BY il.documentDate DESC)
UNION ALL 
(SELECT $feilds3,il.documentAutoID,il.salesPrice,il.itemDescription,il.itemSystemCode,il.transactionUOM,ic1.description as mainCategory,ic2.description as subCategory,il.companyLocalCurrencyDecimalPlaces,il.companyReportingCurrencyDecimalPlaces
        FROM srp_erp_itemledger il
        INNER JOIN
    `srp_erp_itemmaster` `im` ON `il`.`itemAutoID` = `im`.`itemAutoID` AND im.companyID = " . $this->common_data['company_data']['company_id'] . "
        INNER JOIN
    (SELECT
		description,
		itemCategoryID
	FROM
		srp_erp_itemcategory
	GROUP BY
		itemCategoryID) AS `ic1` ON (`ic1`.`itemCategoryID` = `im`.`mainCategoryID`)
        INNER JOIN
    (SELECT
		description,
		itemCategoryID
	FROM
		srp_erp_itemcategory
	GROUP BY
		itemCategoryID) AS `ic2` ON (`ic2`.`itemCategoryID` = `im`.`subcategoryID`) 
		 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = il.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = il.companyLocalCurrencyID) WHERE $itmesOR AND il.documentDate < '" . format_date($this->input->post("from")) . "' AND il.companyID = " . $this->common_data['company_data']['company_id'] . " AND il.wareHouseAutoID IN (" . join(',', $location) . ") GROUP BY il.itemAutoID ORDER BY il.documentDate DESC)) AS a ORDER BY documentDate ASC
")->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    function get_item_ledger_group_report()
    {
        $location = $this->input->post("location");
        $company = $this->get_group_company();
        $items = $this->input->post("itemTo");
        $items = $this->get_group_items($items);
        $i = 1;
        $itmesOR = '( ';
        if (!empty($items)) {
            foreach ($items as $item_val) {
                if ($i != 1) {
                    $itmesOR .= ' OR ';
                }
                $itmesOR .= " il.itemAutoID = '" . $item_val["ItemAutoID"] . "' "; /*generate the query according to selectd items*/
                $i++;
            }
        }
        $itmesOR .= ' ) ';
        $feilds = "";
        $feilds2 = "";
        $feildsra = "";
        $feilds3 = array();
        if (!empty($this->input->post("fieldNameChk"))) { /*generate the query according to selectd columns*/
            $fieldNameChk = $this->input->post("fieldNameChk");
            $key = array_search("transactionQTY", $fieldNameChk); // get key of transactionQTY in an array
            unset($fieldNameChk[$key]);
            foreach ($fieldNameChk as $val) {
                if ($val == "documentID") {
                    $feilds3[] = "'Opening Balance' as documentID";
                } else if ($val == "documentSystemCode") {
                    $feilds3[] = "'-' as documentSystemCode";
                } else if ($val == "documentDate") {
                    $feilds3[] = "'1970-01-01' as documentDate";
                } else if ($val == "segmentCode") {
                    $feilds3[] = "'-' as segmentCode";
                } else if ($val == "narration") {
                    $feilds3[] = "'-' as narration";
                } else if ($val == "referenceNumber") {
                    $feilds3[] = "'-' as referenceNumber";
                } else if ($val == "wareHouseLocation") {
                    $feilds3[] = "'-' as wareHouseLocation";
                } else {
                    $feilds3[] = 'il.' . $val;
                }
            }

            foreach ($fieldNameChk as $val) {
                if ($val == "wareHouseLocation") {
                    $feildsra[] = "whm.wareHouseLocation as wareHouseLocation";

                } else if ($val == "segmentCode") {
                    $feildsra[] = "gseg.description as segmentCode";

                } else {
                    $feildsra[] = 'il.' . $val;
                }
            }


            $feilds3 = join(',', $feilds3);
            $feilds = join(',', $feildsra);
            $feilds .= ",SUM(il.transactionQTY/il.convertionRate) as transactionQTY";
            $feilds3 .= ",SUM(il.transactionQTY/il.convertionRate) as transactionQTY";
            $feilds2 = join(',a.', $fieldNameChk);
            $feilds2 = "a." . $feilds2;
            $feilds2 .= ",a.transactionQTY";
            if (in_array("companyLocalWacAmount", $fieldNameChk)) {
                $feilds .= ",SUM(il.companyLocalAmount) as localCostAsset";
                $feilds .= ",CL.DecimalPlaces as companyLocalWacAmountDecimalPlaces";
                $feilds .= ",(SUM(il.companyLocalAmount) / SUM(il.transactionQTY/il.convertionRate)) as avgCompanyLocalAmount";
                $feilds2 .= ",a.localCostAsset";
                $feilds2 .= ",a.companyLocalWacAmountDecimalPlaces";
                $feilds2 .= ",a.avgCompanyLocalAmount as avgCompanyLocalAmount";
                $feilds3 .= ",SUM(il.companyLocalAmount) as localCostAsset";
                $feilds3 .= ",CL.DecimalPlaces as companyLocalWacAmountDecimalPlaces";
                $feilds3 .= ",(SUM(il.companyLocalAmount) / SUM(il.transactionQTY/il.convertionRate)) as avgCompanyLocalAmount";
            }
            if (in_array("companyReportingWacAmount", $fieldNameChk)) {
                $feilds .= ",SUM(il.companyReportingAmount) as rptCostAsset";
                $feilds .= ",CR.DecimalPlaces as companyReportingWacAmountDecimalPlaces";
                $feilds .= ",(SUM(il.companyReportingAmount) / SUM(il.transactionQTY/il.convertionRate)) as avgCompanyReportingAmount";
                $feilds2 .= ",a.rptCostAsset";
                $feilds2 .= ",a.companyReportingWacAmountDecimalPlaces";
                $feilds2 .= ",a.avgCompanyReportingAmount";
                $feilds3 .= ",SUM(il.companyReportingAmount) as rptCostAsset";
                $feilds3 .= ",CR.DecimalPlaces as companyReportingWacAmountDecimalPlaces";
                $feilds3 .= ",(SUM(il.companyReportingAmount) / SUM(il.transactionQTY/il.convertionRate)) as avgCompanyReportingAmount";

            }
        }
        $result = $this->db->query("SELECT $feilds2, a.documentAutoID,a.salesPrice,a.itemDescription,a.itemSystemCode,a.transactionUOM,a.mainCategory,a.subCategory,a.companyLocalCurrencyDecimalPlaces,a.companyReportingCurrencyDecimalPlaces FROM 
((SELECT $feilds,il.documentAutoID,il.salesPrice,im.itemDescription,im.itemSystemCode,il.transactionUOM,ic1.description as mainCategory,ic2.description as subCategory,il.companyLocalCurrencyDecimalPlaces,il.companyReportingCurrencyDecimalPlaces
        FROM srp_erp_itemledger il
        INNER JOIN (SELECT srp_erp_groupitemmaster.*,srp_erp_groupitemmasterdetails.itemAutoID as itemID,srp_erp_itemmaster.mainCategoryID AS itemMainCategoryID  FROM srp_erp_groupitemmaster INNER JOIN srp_erp_groupitemmasterdetails ON srp_erp_groupitemmaster.itemAutoID = srp_erp_groupitemmasterdetails.groupItemMasterID INNER JOIN srp_erp_itemmaster
	ON srp_erp_itemmaster.itemAutoID = srp_erp_groupitemmasterdetails.itemAutoID  WHERE groupID = " . current_companyID() . ") im ON im.itemID = `il`.`itemAutoID`
    INNER JOIN (SELECT srp_erp_groupwarehousemaster.*,srp_erp_groupwarehousedetails.warehosueMasterID as warehouseID FROM srp_erp_groupwarehousemaster INNER JOIN srp_erp_groupwarehousedetails ON srp_erp_groupwarehousemaster.wareHouseAutoID = srp_erp_groupwarehousedetails.groupWarehouseMasterID WHERE groupID = " . current_companyID() . " AND wareHouseAutoID IN (" . join(',', $location) . ")) whm ON whm.warehouseID = il.wareHouseAutoID
    LEFT JOIN (SELECT srp_erp_groupsegment.*,srp_erp_groupsegmentdetails.segmentID as groupSegmentID FROM srp_erp_groupsegment INNER JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID WHERE groupID = " . current_companyID() . ") gseg ON gseg.groupSegmentID = il.segmentID
        INNER JOIN
    (SELECT srp_erp_groupitemcategory.*,srp_erp_groupitemcategorydetails.itemCategoryID as categoryID FROM srp_erp_groupitemcategory INNER JOIN srp_erp_groupitemcategorydetails ON srp_erp_groupitemcategory.itemCategoryID = srp_erp_groupitemcategorydetails.groupItemCategoryID WHERE groupID = " . current_companyID() . ") AS `ic1` ON (`ic1`.`categoryID` = `im`.`itemMainCategoryID`)
        INNER JOIN
    (SELECT
		description,
		itemCategoryID
	FROM
		srp_erp_groupitemcategory
	GROUP BY
		itemCategoryID) AS `ic2` ON (`ic2`.`itemCategoryID` = `im`.`subcategoryID`) 
		 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = il.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = il.companyLocalCurrencyID) WHERE $itmesOR AND il.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "' AND il.companyID in (" . join(',', $company) . ") GROUP BY im.itemAutoID,il.documentID,il.documentSystemCode ORDER BY il.documentDate DESC)
UNION ALL 
(SELECT $feilds3,il.documentAutoID,il.salesPrice,il.itemDescription,il.itemSystemCode,il.transactionUOM,ic1.description as mainCategory,ic2.description as subCategory,il.companyLocalCurrencyDecimalPlaces,il.companyReportingCurrencyDecimalPlaces
        FROM srp_erp_itemledger il
        INNER JOIN (SELECT srp_erp_groupitemmaster.*,srp_erp_groupitemmasterdetails.itemAutoID as itemID,srp_erp_itemmaster.mainCategoryID AS itemMainCategoryID  FROM srp_erp_groupitemmaster INNER JOIN srp_erp_groupitemmasterdetails ON srp_erp_groupitemmaster.itemAutoID = srp_erp_groupitemmasterdetails.groupItemMasterID INNER JOIN srp_erp_itemmaster
	ON srp_erp_itemmaster.itemAutoID = srp_erp_groupitemmasterdetails.itemAutoID  WHERE groupID = " . current_companyID() . ") im ON im.itemID = `il`.`itemAutoID`
    INNER JOIN (SELECT srp_erp_groupwarehousemaster.*,srp_erp_groupwarehousedetails.warehosueMasterID as warehouseID FROM srp_erp_groupwarehousemaster INNER JOIN srp_erp_groupwarehousedetails ON srp_erp_groupwarehousemaster.wareHouseAutoID = srp_erp_groupwarehousedetails.groupWarehouseMasterID WHERE groupID = " . current_companyID() . " AND wareHouseAutoID IN (" . join(',', $location) . ")) whm ON whm.warehouseID = il.wareHouseAutoID
    LEFT JOIN (SELECT srp_erp_groupsegment.*,srp_erp_groupsegmentdetails.segmentID as groupSegmentID FROM srp_erp_groupsegment INNER JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID WHERE groupID = " . current_companyID() . ") gseg ON gseg.groupSegmentID = il.segmentID
        INNER JOIN
    (SELECT srp_erp_groupitemcategory.*,srp_erp_groupitemcategorydetails.itemCategoryID as categoryID FROM srp_erp_groupitemcategory INNER JOIN srp_erp_groupitemcategorydetails ON srp_erp_groupitemcategory.itemCategoryID = srp_erp_groupitemcategorydetails.groupItemCategoryID WHERE groupID = " . current_companyID() . ") AS `ic1` ON (`ic1`.`categoryID` = `im`.`itemMainCategoryID`)
        INNER JOIN
    (SELECT
		description,
		itemCategoryID
	FROM
		srp_erp_groupitemcategory
	GROUP BY
		itemCategoryID) AS `ic2` ON (`ic2`.`itemCategoryID` = `im`.`subcategoryID`) 
		 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = il.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = il.companyLocalCurrencyID) WHERE $itmesOR AND il.documentDate < '" . format_date($this->input->post("from")) . "' AND il.companyID in (" . join(',', $company) . ") GROUP BY im.itemAutoID ORDER BY il.documentDate DESC)) AS a ORDER BY documentDate ASC
")->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    function get_item_valuation_summary_report()
    {
        $location = $this->input->post("location");
        $items = $this->input->post("itemTo");
        $i = 1;
        $itmesOR = '( ';
        if (!empty($items)) { /*generate the query according to selectd items*/
            foreach ($items as $item_val) {
                if ($i != 1) {
                    $itmesOR .= ' OR ';
                }
                $itmesOR .= " il.itemAutoID = '" . $item_val . "' ";
                $i++;
            }
        }
        $itmesOR .= ' ) ';

        $feilds = "";
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            $feilds = join(',il.', $fieldNameChk);
            $feilds = ",il." . $feilds;
            if (in_array("companyReportingWacAmount", $fieldNameChk)) {
                $feilds .= ",SUM(il.companyReportingAmount) as AssetValueRpt,(SUM(il.companyReportingAmount) / SUM(il.transactionQTY/il.convertionRate)) as companyReportingWacAmount,(im.companyReportingSellingPrice * SUM(il.transactionQTY/il.convertionRate)) as RetailValue,im.companyReportingSellingPrice as salesPrice";
                $feilds .= ",CR.DecimalPlaces as companyReportingWacAmountDecimalPlaces";
            }
            if (in_array("companyLocalWacAmount", $fieldNameChk)) {
                $feilds .= ",SUM(il.companyLocalAmount) as AssetValueLocal,(SUM(il.companyLocalAmount) / SUM(il.transactionQTY/il.convertionRate)) as companyLocalWacAmount,(im.companyLocalSellingPrice * SUM(il.transactionQTY/il.convertionRate)) as RetailValue,im.companyLocalSellingPrice as salesPrice";
            }
            $feilds .= ",CL.DecimalPlaces as companyLocalWacAmountDecimalPlaces";
        }
        $result = $this->db->query("SELECT il.itemDescription,SUM(il.transactionQTY/il.convertionRate) as transactionQTY,il.itemSystemCode,ic1.description as mainCategory,ic2.description as subCategory,il.itemAutoID $feilds
        FROM srp_erp_itemledger il
        INNER JOIN
    `srp_erp_itemmaster` `im` ON `il`.`itemAutoID` = `im`.`itemAutoID` AND im.companyID = " . $this->common_data['company_data']['company_id'] . "
        INNER JOIN
    (SELECT
		description,
		itemCategoryID
	FROM
		srp_erp_itemcategory
	GROUP BY
		itemCategoryID) AS `ic1` ON (`ic1`.`itemCategoryID` = `im`.`mainCategoryID`)
        INNER JOIN
    (SELECT
		description,
		itemCategoryID
	FROM
		srp_erp_itemcategory
	GROUP BY
		itemCategoryID) AS `ic2` ON (`ic2`.`itemCategoryID` = `im`.`subcategoryID`) 
		LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = il.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = il.companyLocalCurrencyID) 
WHERE $itmesOR AND il.documentDate <= '" . format_date($this->input->post("from")) . "' AND il.companyID = " . $this->common_data['company_data']['company_id'] . " AND wareHouseAutoID IN(" . join(',', $location) . ")  GROUP BY `il`.`itemAutoID` ORDER BY il.documentDate DESC")->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_item_valuation_summary_group_report()
    {
        $location = $this->input->post("location");
        $company = $this->get_group_company();
        $items = $this->input->post("itemTo");
        $items = $this->get_group_items($items);
        $i = 1;
        $itmesOR = '( ';
        if (!empty($items)) { /*generate the query according to selectd items*/
            foreach ($items as $item_val) {
                if ($i != 1) {
                    $itmesOR .= ' OR ';
                }
                $itmesOR .= " il.itemAutoID = '" . $item_val["ItemAutoID"] . "' ";
                $i++;
            }
        }
        $itmesOR .= ' ) ';

        $feilds = "";
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            $feilds = join(',il.', $fieldNameChk);
            $feilds = ",il." . $feilds;
            if (in_array("companyReportingWacAmount", $fieldNameChk)) {
                $feilds .= ",SUM(il.companyReportingAmount) as AssetValueRpt,(SUM(il.companyReportingAmount) / SUM(il.transactionQTY/il.convertionRate)) as companyReportingWacAmount,(im.companyReportingSellingPrice * SUM(il.transactionQTY/il.convertionRate)) as RetailValue,im.companyReportingSellingPrice as salesPrice";
                $feilds .= ",CR.DecimalPlaces as companyReportingWacAmountDecimalPlaces";
            }
            if (in_array("companyLocalWacAmount", $fieldNameChk)) {
                $feilds .= ",SUM(il.companyLocalAmount) as AssetValueLocal,(SUM(il.companyLocalAmount) / SUM(il.transactionQTY/il.convertionRate)) as companyLocalWacAmount,(im.companyLocalSellingPrice * SUM(il.transactionQTY/il.convertionRate)) as RetailValue,im.companyLocalSellingPrice as salesPrice";
            }
            $feilds .= ",CL.DecimalPlaces as companyLocalWacAmountDecimalPlaces";
        }
        $result = $this->db->query("SELECT im.itemDescription,SUM(il.transactionQTY/il.convertionRate) as transactionQTY,im.itemSystemCode,ic1.description as mainCategory,ic2.description as subCategory,il.itemAutoID $feilds
        FROM srp_erp_itemledger il
        INNER JOIN (SELECT srp_erp_groupitemmaster.*,srp_erp_groupitemmasterdetails.itemAutoID as itemID,srp_erp_itemmaster.mainCategoryID AS itemMainCategoryID  FROM srp_erp_groupitemmaster INNER JOIN srp_erp_groupitemmasterdetails ON srp_erp_groupitemmaster.itemAutoID = srp_erp_groupitemmasterdetails.groupItemMasterID INNER JOIN srp_erp_itemmaster
	ON srp_erp_itemmaster.itemAutoID = srp_erp_groupitemmasterdetails.itemAutoID  WHERE groupID = " . current_companyID() . ") im ON im.itemID = `il`.`itemAutoID`
	INNER JOIN (SELECT srp_erp_groupwarehousemaster.*,srp_erp_groupwarehousedetails.warehosueMasterID as warehouseID FROM srp_erp_groupwarehousemaster INNER JOIN srp_erp_groupwarehousedetails ON srp_erp_groupwarehousemaster.wareHouseAutoID = srp_erp_groupwarehousedetails.groupWarehouseMasterID WHERE groupID = " . current_companyID() . " AND wareHouseAutoID IN (" . join(',', $location) . ")) whm ON whm.warehouseID = il.wareHouseAutoID
        INNER JOIN
    (SELECT srp_erp_groupitemcategory.*,srp_erp_groupitemcategorydetails.itemCategoryID as categoryID FROM srp_erp_groupitemcategory INNER JOIN srp_erp_groupitemcategorydetails ON srp_erp_groupitemcategory.itemCategoryID = srp_erp_groupitemcategorydetails.groupItemCategoryID WHERE groupID = " . current_companyID() . ") AS `ic1` ON (`ic1`.`categoryID` = `im`.`itemMainCategoryID`)
        INNER JOIN
    (SELECT
		description,
		itemCategoryID
	FROM
		srp_erp_groupitemcategory
	GROUP BY
		itemCategoryID) AS `ic2` ON (`ic2`.`itemCategoryID` = `im`.`subcategoryID`) 
		LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = il.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = il.companyLocalCurrencyID) 
WHERE $itmesOR AND il.documentDate <= '" . format_date($this->input->post("from")) . "' AND il.companyID IN (" . join(',', $company) . ") GROUP BY `im`.`itemAutoID` ORDER BY il.documentDate DESC")->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_item_valuation_summary_total_asset()
    {
        $location = $this->input->post("location");
        $items = $this->input->post("itemTo");
        $i = 1;
        $itmesOR = '( ';
        if (!empty($items)) { /*generate the query according to selectd items*/
            foreach ($items as $item_val) {
                if ($i != 1) {
                    $itmesOR .= ' OR ';
                }
                $itmesOR .= " il.itemAutoID = '" . $item_val . "' ";
                $i++;
            }
        }
        $itmesOR .= ' ) ';
        $feilds = array();
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            if (in_array("companyReportingWacAmount", $fieldNameChk)) {
                $feilds[] = "SUM(il.companyReportingAmount) as TotalAssetValueRpt,SUM(im.companyReportingSellingPrice * (il.transactionQTY/il.convertionRate)) as TotalRetailValue";
            }
            if (in_array("companyLocalWacAmount", $fieldNameChk)) {
                $feilds[] = "SUM(il.companyLocalAmount) as TotalAssetValueLocal,SUM(im.companyLocalSellingPrice * (il.transactionQTY/il.convertionRate)) as TotalRetailValue";
            }
        }
        $result = $this->db->query(" SELECT " . join(',', $feilds) . "
        FROM srp_erp_itemledger il
        INNER JOIN
    `srp_erp_itemmaster` `im` ON `il`.`itemAutoID` = `im`.`itemAutoID` AND im.companyID = " . $this->common_data['company_data']['company_id'] . "
        INNER JOIN
    (SELECT
		description,
		itemCategoryID
	FROM
		srp_erp_itemcategory
	GROUP BY
		itemCategoryID) AS `ic1` ON (`ic1`.`itemCategoryID` = `im`.`mainCategoryID`)
        INNER JOIN
    (SELECT
		description,
		itemCategoryID
	FROM
		srp_erp_itemcategory
	GROUP BY
		itemCategoryID) AS `ic2` ON (`ic2`.`itemCategoryID` = `im`.`subcategoryID`) WHERE $itmesOR AND il.documentDate <= '" . format_date($this->input->post("from")) . "' AND wareHouseAutoID IN(" . join(',', $location) . ") AND il.companyID = " . $this->common_data['company_data']['company_id'])->row_array();
        //echo $this->db->last_query();
        return $result;
    }


    function get_item_valuation_summary_total_asset_group()
    {
        $location = $this->input->post("location");
        $company = $this->get_group_company();
        $items = $this->input->post("itemTo");
        $items = $this->get_group_items($items);
        $i = 1;
        $itmesOR = '( ';
        if (!empty($items)) { /*generate the query according to selectd items*/
            foreach ($items as $item_val) {
                if ($i != 1) {
                    $itmesOR .= ' OR ';
                }
                $itmesOR .= " il.itemAutoID = '" . $item_val["ItemAutoID"] . "' ";
                $i++;
            }
        }
        $itmesOR .= ' ) ';
        $feilds = array();
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            if (in_array("companyReportingWacAmount", $fieldNameChk)) {
                $feilds[] = "SUM(il.companyReportingAmount) as TotalAssetValueRpt,SUM(im.companyReportingSellingPrice * (il.transactionQTY/il.convertionRate)) as TotalRetailValue";
            }
            if (in_array("companyLocalWacAmount", $fieldNameChk)) {
                $feilds[] = "SUM(il.companyLocalAmount) as TotalAssetValueLocal,SUM(im.companyLocalSellingPrice * (il.transactionQTY/il.convertionRate)) as TotalRetailValue";
            }
        }
        $result = $this->db->query(" SELECT " . join(',', $feilds) . "
        FROM srp_erp_itemledger il
        INNER JOIN (SELECT srp_erp_groupitemmaster.*,srp_erp_groupitemmasterdetails.itemAutoID as itemID,srp_erp_itemmaster.mainCategoryID AS itemMainCategoryID  FROM srp_erp_groupitemmaster INNER JOIN srp_erp_groupitemmasterdetails ON srp_erp_groupitemmaster.itemAutoID = srp_erp_groupitemmasterdetails.groupItemMasterID INNER JOIN srp_erp_itemmaster
	ON srp_erp_itemmaster.itemAutoID = srp_erp_groupitemmasterdetails.itemAutoID  WHERE groupID = " . current_companyID() . ") im ON im.itemID = `il`.`itemAutoID`
	INNER JOIN (SELECT srp_erp_groupwarehousemaster.*,srp_erp_groupwarehousedetails.warehosueMasterID as warehouseID FROM srp_erp_groupwarehousemaster INNER JOIN srp_erp_groupwarehousedetails ON srp_erp_groupwarehousemaster.wareHouseAutoID = srp_erp_groupwarehousedetails.groupWarehouseMasterID WHERE groupID = " . current_companyID() . " AND wareHouseAutoID IN (" . join(',', $location) . ")) whm ON whm.warehouseID = il.wareHouseAutoID
        INNER JOIN
    (SELECT srp_erp_groupitemcategory.*,srp_erp_groupitemcategorydetails.itemCategoryID as categoryID FROM srp_erp_groupitemcategory INNER JOIN srp_erp_groupitemcategorydetails ON srp_erp_groupitemcategory.itemCategoryID = srp_erp_groupitemcategorydetails.groupItemCategoryID WHERE groupID = " . current_companyID() . ") AS `ic1` ON (`ic1`.`categoryID` = `im`.`itemMainCategoryID`)
        INNER JOIN
    (SELECT
		description,
		itemCategoryID
	FROM
		srp_erp_groupitemcategory
	GROUP BY
		itemCategoryID) AS `ic2` ON (`ic2`.`itemCategoryID` = `im`.`subcategoryID`) WHERE $itmesOR AND il.documentDate <= '" . format_date($this->input->post("from")) . "' AND il.companyID IN (" . join(',', $company) . ")")->row_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_item_counting_report()
    {
        $location = $this->input->post("location");
        $items = $this->input->post("itemTo");
        $i = 1;
        $itmesOR = '( ';
        if (!empty($items)) { /*generate the query according to selectd items*/
            foreach ($items as $item_val) {
                if ($i != 1) {
                    $itmesOR .= ' OR ';
                }
                $itmesOR .= " il.itemAutoID = '" . $item_val . "' ";
                $i++;
            }
        }
        $itmesOR .= ' ) ';
        $having = array();
        $feilds = "";
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            if (in_array("AssetValueLocal", $fieldNameChk)) {
                $feilds .= ",SUM(il.companyLocalAmount) as AssetValueLocal";
                $feilds .= ",(SUM(il.companyLocalAmount)/SUM(il.transactionQTY/il.convertionRate)) as companyLocalWacAmount";
                $feilds .= ",ROUND((SUM(il.companyLocalAmount)/SUM(il.transactionQTY/il.convertionRate))) as companyLocalWacAmountRound";
                $feilds .= ",CL.DecimalPlaces as companyLocalCurrencyDecimalPlaces";
                $having[] = 'companyLocalWacAmountRound != 0';
            }
            if (in_array("AssetValueRpt", $fieldNameChk)) {
                $feilds .= ",SUM(il.companyReportingAmount) as AssetValueRpt";
                $feilds .= ",(SUM(il.companyReportingAmount)/SUM(il.transactionQTY/il.convertionRate)) as companyReportingWacAmount";
                $feilds .= ",ROUND((SUM(il.companyReportingAmount)/SUM(il.transactionQTY/il.convertionRate))) as companyReportingWacAmountRound";
                $feilds .= ",CR.DecimalPlaces as companyReportingCurrencyDecimalPlaces";
                $having[] = 'companyReportingWacAmountRound != 0';
            }
        }
        $result = $this->db->query("SELECT il.transactionUOM,il.itemDescription,srp_erp_warehousemaster.wareHouseLocation, il.wareHouseAutoID,SUM(il.transactionQTY/il.convertionRate) as transactionQTY,il.salesPrice,il.itemSystemCode,ic1.description as mainCategory, im.isSubitemExist, im.itemAutoID, ic2.description as subCategory $feilds
        FROM srp_erp_itemledger il 
        INNER JOIN
    `srp_erp_itemmaster` `im` ON `il`.`itemAutoID` = `im`.`itemAutoID` AND im.companyID = " . $this->common_data['company_data']['company_id'] . "
    LEFT JOIN srp_erp_warehousemaster ON srp_erp_warehousemaster.wareHouseAutoID = il.wareHouseAutoID
        INNER JOIN
    (SELECT
		description,
		itemCategoryID
	FROM
		srp_erp_itemcategory
	GROUP BY
		itemCategoryID) AS `ic1` ON (`ic1`.`itemCategoryID` = `im`.`mainCategoryID`)
        INNER JOIN
    (SELECT
		description,
		itemCategoryID
	FROM
		srp_erp_itemcategory
	GROUP BY
		itemCategoryID) AS `ic2` ON (`ic2`.`itemCategoryID` = `im`.`subcategoryID`) 
		LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = il.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = il.companyLocalCurrencyID)  
WHERE $itmesOR AND il.documentDate <= '" . format_date($this->input->post("from")) . "' AND il.companyID = " . $this->common_data['company_data']['company_id'] . " AND il.wareHouseAutoID IN(" . join(',', $location) . ") GROUP BY `il`.`itemAutoID`,il.wareHouseAutoID  ORDER BY il.itemAutoID DESC")->result_array(); //HAVING (" . join(' OR ', $having) . ")
        //echo $this->db->last_query();
        return $result;
    }


    function get_item_counting_group_report()
    {
        $location = $this->input->post("location");
        $company = $this->get_group_company();
        $items = $this->input->post("itemTo");
        $items = $this->get_group_items($items);
        $i = 1;
        $itmesOR = '( ';
        if (!empty($items)) { /*generate the query according to selectd items*/
            foreach ($items as $item_val) {
                if ($i != 1) {
                    $itmesOR .= ' OR ';
                }
                $itmesOR .= " il.itemAutoID = '" . $item_val["ItemAutoID"] . "' ";
                $i++;
            }
        }
        $itmesOR .= ' ) ';
        $having = array();
        $feilds = "";
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            if (in_array("AssetValueLocal", $fieldNameChk)) {
                $feilds .= ",SUM(il.companyLocalAmount) as AssetValueLocal";
                $feilds .= ",(SUM(il.companyLocalAmount)/SUM(il.transactionQTY/il.convertionRate)) as companyLocalWacAmount";
                $feilds .= ",ROUND((SUM(il.companyLocalAmount)/SUM(il.transactionQTY/il.convertionRate))) as companyLocalWacAmountRound";
                $feilds .= ",CL.DecimalPlaces as companyLocalCurrencyDecimalPlaces";
                $having[] = 'companyLocalWacAmountRound != 0';
            }
            if (in_array("AssetValueRpt", $fieldNameChk)) {
                $feilds .= ",SUM(il.companyReportingAmount) as AssetValueRpt";
                $feilds .= ",(SUM(il.companyReportingAmount)/SUM(il.transactionQTY/il.convertionRate)) as companyReportingWacAmount";
                $feilds .= ",ROUND((SUM(il.companyReportingAmount)/SUM(il.transactionQTY/il.convertionRate))) as companyReportingWacAmountRound";
                $feilds .= ",CR.DecimalPlaces as companyReportingCurrencyDecimalPlaces";
                $having[] = 'companyReportingWacAmountRound != 0';
            }
        }
        $result = $this->db->query("SELECT il.transactionUOM,im.itemDescription,whm.wareHouseLocation, whm.wareHouseAutoID,SUM(il.transactionQTY/il.convertionRate) as transactionQTY,il.salesPrice,il.itemSystemCode,ic1.description as mainCategory, im.isSubitemExist, im.itemAutoID, ic2.description as subCategory $feilds
        FROM srp_erp_itemledger il 
         INNER JOIN (SELECT srp_erp_groupitemmaster.*,srp_erp_groupitemmasterdetails.itemAutoID as itemID,srp_erp_itemmaster.mainCategoryID AS itemMainCategoryID  FROM srp_erp_groupitemmaster INNER JOIN srp_erp_groupitemmasterdetails ON srp_erp_groupitemmaster.itemAutoID = srp_erp_groupitemmasterdetails.groupItemMasterID INNER JOIN srp_erp_itemmaster
	ON srp_erp_itemmaster.itemAutoID = srp_erp_groupitemmasterdetails.itemAutoID  WHERE groupID = " . current_companyID() . ") im ON im.itemID = `il`.`itemAutoID`
	INNER JOIN (SELECT srp_erp_groupwarehousemaster.*,srp_erp_groupwarehousedetails.warehosueMasterID as warehouseID FROM srp_erp_groupwarehousemaster INNER JOIN srp_erp_groupwarehousedetails ON srp_erp_groupwarehousemaster.wareHouseAutoID = srp_erp_groupwarehousedetails.groupWarehouseMasterID WHERE groupID = " . current_companyID() . " AND wareHouseAutoID IN (" . join(',', $location) . ")) whm ON whm.warehouseID = il.wareHouseAutoID
        INNER JOIN
    (SELECT srp_erp_groupitemcategory.*,srp_erp_groupitemcategorydetails.itemCategoryID as categoryID FROM srp_erp_groupitemcategory INNER JOIN srp_erp_groupitemcategorydetails ON srp_erp_groupitemcategory.itemCategoryID = srp_erp_groupitemcategorydetails.groupItemCategoryID WHERE groupID = " . current_companyID() . ") AS `ic1` ON (`ic1`.`categoryID` = `im`.`itemMainCategoryID`)
        INNER JOIN
    (SELECT
		description,
		itemCategoryID
	FROM
		srp_erp_groupitemcategory
	GROUP BY
		itemCategoryID) AS `ic2` ON (`ic2`.`itemCategoryID` = `im`.`subcategoryID`)
		LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = il.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = il.companyLocalCurrencyID)  
WHERE $itmesOR AND il.documentDate <= '" . format_date($this->input->post("from")) . "' AND il.companyID IN (" . join(',', $company) . ") GROUP BY `im`.`itemAutoID`,whm.wareHouseAutoID HAVING (" . join(' OR ', $having) . ") ORDER BY im.itemAutoID DESC")->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    function get_item_fast_moving_report()
    {
        $limit = ""; /*limit the record according to selected report type*/
        if ($this->input->post("rptType") == 2) {
            $limit = "limit 10";
        } else if ($this->input->post("rptType") == 3) {
            $limit = "limit 20";
        } else {
            $limit = "";
        }
        $feilds = "";
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            foreach ($fieldNameChk as $val) {
                if ($val == "companyLocalAmount") {
                    $feilds .= "SUM(((il.transactionQTY/convertionRate)*-1) * il.salesPrice/il.companyLocalExchangeRate) as " . $val . ",";
                    $feilds .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                }
                if ($val == "companyReportingAmount") {
                    $feilds .= "SUM(((il.transactionQTY/convertionRate)*-1) * il.salesPrice/il.companyReportingExchangeRate) as " . $val . ",";
                    $feilds .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                }

            }
        }
        $result = $this->db->query("SELECT $feilds im.defaultUnitOfMeasure as UOM,im.itemDescription,SUM(il.transactionQTY/convertionRate)*-1 as transactionQTY,im.itemSystemCode,im.currentStock,ic1.description as mainCategory,ic2.description as subCategory
        FROM srp_erp_itemledger il
        INNER JOIN
    `srp_erp_itemmaster` `im` ON `il`.`itemAutoID` = `im`.`itemAutoID` AND im.companyID = " . $this->common_data['company_data']['company_id'] . " AND im.mainCategory = 'Inventory'
     INNER JOIN
    (SELECT 
        description, itemCategoryID
    FROM
        srp_erp_itemcategory GROUP BY itemCategoryID) AS `ic1` ON (`ic1`.`itemCategoryID` = `im`.`financeCategory`)
        INNER JOIN
    (SELECT 
        description, masterID
    FROM
        srp_erp_itemcategory GROUP BY masterID) AS `ic2` ON (`ic2`.`masterID` = `im`.`mainCategoryID`)
        LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = il.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = il.companyLocalCurrencyID)  
         WHERE il.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "' AND il.companyID = " . $this->common_data['company_data']['company_id'] . " AND il.documentCode IN ('CINV','RV')  GROUP BY `il`.`itemAutoID` ORDER BY transactionQTY DESC $limit")->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_finance_tb_report()
    {
        switch ($this->input->post('rptType')) {
            case "1": /*Month Wise*/
                $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                $months = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month"); /*calculate months*/
                $feilds = "";
                $feilds2 = "";
                $having = array();
                $fieldNameChk = $this->input->post("fieldNameChk");
                if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
                    foreach ($fieldNameChk as $val) {
                        $feilds2 .= "SUM(IF(a.documentDate < '" . $financialBeginingDate["beginingDate"] . "',a." . $val . ",0)) AS `openingBalance`,";
                        if (!empty($months)) {
                            foreach ($months as $key => $val2) {
                                $feilds2 .= "SUM(if(b.masterCategory = 'BS',if(DATE_FORMAT(a.documentDate,'%Y-%m') <= '$key',a." . $val . ",0),if(DATE_FORMAT(a.documentDate,'%Y-%m') = '$key',a." . $val . ",0) )) as `" . $key . "`,";
                            }
                        }
                        $feilds .= "SUM(srp_erp_generalledger." . $val . ") as " . $val . ",";
                        if ($val == "companyLocalAmount") {
                            $feilds .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        if ($val == "companyReportingAmount") {
                            $feilds .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        $feilds2 .= $val . "DecimalPlaces,";
                        $having[] = $val . '!= -0 AND ' . $val . ' != 0';
                    }
                }

                $sql = "SELECT $feilds2
    b.GLDescription,b.masterCategory,b.GLSecondaryCode,b.systemAccountCode,b.GLAutoID FROM (
    (SELECT $feilds srp_erp_generalledger.GLAutoID,srp_erp_generalledger.documentDate FROM srp_erp_generalledger 
    INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND masterCategory = 'BS' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
    WHERE documentDate < '" . $financialBeginingDate["beginingDate"] . "' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY GLAutoID,documentDate HAVING (" . join(' AND ', $having) . ")) 
    UNION ALL
    (SELECT $feilds GLAutoID,documentDate FROM srp_erp_generalledger 
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
    WHERE documentDate BETWEEN '" . $financialBeginingDate["beginingDate"] . "' AND '" . format_date($this->input->post("from")) . "' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY GLAutoID,documentDate HAVING (" . join(' AND ', $having) . "))) AS a
        LEFT JOIN
    srp_erp_chartofaccounts b ON a.GLAutoID = b.GLAutoID GROUP BY a.GLAutoID ORDER BY b.GLSecondaryCode ASC";
                $result = $this->db->query($sql)->result_array();
                return $result;
                break;
            case "3": /*YTD*/
                $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                $feilds = "";
                $feilds2 = "";
                $having = array();
                $fieldNameChk = $this->input->post("fieldNameChk");
                if (isset($fieldNameChk)) { /*generate the query according to selected columns*/
                    foreach ($fieldNameChk as $val) {
                        $feilds .= "SUM(srp_erp_generalledger." . $val . ") as " . $val . ",";
                        $feilds2 .= "SUM(a." . $val . ") as " . $val . ",";
                        $having[] = '(' . $val . '!= -0 AND ' . $val . ' != 0)';
                        if ($val == "companyLocalAmount") {
                            $feilds .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        if ($val == "companyReportingAmount") {
                            $feilds .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        $feilds2 .= $val . "DecimalPlaces,";
                    }
                }
                $result = $this->db->query("SELECT $feilds2
    b.GLDescription,b.masterCategory,b.GLSecondaryCode,b.systemAccountCode,b.GLAutoID FROM 
    ((SELECT $feilds srp_erp_generalledger.GLAutoID,srp_erp_generalledger.documentDate FROM srp_erp_generalledger
    INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND masterCategory = 'BS' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . " 
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
    WHERE documentDate < '" . $financialBeginingDate["beginingDate"] . "' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY GLAutoID) 
    UNION ALL (SELECT $feilds GLAutoID,documentDate FROM srp_erp_generalledger
     LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
WHERE documentDate BETWEEN '" . $financialBeginingDate["beginingDate"] . "' AND '" . format_date($this->input->post("from")) . "' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY GLAutoID)) AS a
        LEFT JOIN
    srp_erp_chartofaccounts b ON a.GLAutoID = b.GLAutoID WHERE (" . join(' OR ', $having) . ") GROUP BY a.GLAutoID ORDER BY b.GLSecondaryCode ASC")->result_array();
                //echo $this->db->last_query();
                return $result;
                break;
        }
    }


    function get_finance_tb_group_report()
    {
        switch ($this->input->post('rptType')) {
            case "1": /*Month Wise*/
                $company = $this->get_group_company();
                $financialBeginingDate = get_group_financial_year(format_date($this->input->post("from")));
                $months = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month"); /*calculate months*/
                $feilds = "";
                $feilds2 = "";
                $having = array();
                $fieldNameChk = $this->input->post("fieldNameChk");
                if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
                    foreach ($fieldNameChk as $val) {
                        $feilds2 .= "SUM(IF(a.documentDate < '" . $financialBeginingDate["beginingDate"] . "',a." . $val . ",0)) AS `openingBalance`,";
                        if (!empty($months)) {
                            foreach ($months as $key => $val2) {
                                $feilds2 .= "SUM(if(b.masterCategory = 'BS',if(DATE_FORMAT(a.documentDate,'%Y-%m') <= '$key',a." . $val . ",0),if(DATE_FORMAT(a.documentDate,'%Y-%m') = '$key',a." . $val . ",0) )) as `" . $key . "`,";
                            }
                        }
                        $feilds .= "SUM(srp_erp_generalledger." . $val . ") as " . $val . ",";
                        if ($val == "companyLocalAmount") {
                            $feilds .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        if ($val == "companyReportingAmount") {
                            $feilds .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        $feilds2 .= $val . "DecimalPlaces,";
                        $having[] = $val . '!= -0 AND ' . $val . ' != 0';
                    }
                }

                $sql = "SELECT $feilds2
    b.GLDescription,b.masterCategory,b.GLSecondaryCode,b.systemAccountCode,b.GLAutoID FROM (
    (SELECT $feilds coa.GLAutoID as GLAutoID,srp_erp_generalledger.documentDate FROM srp_erp_generalledger 
    INNER JOIN (SELECT chartofAccountID,GLSecondaryCode,GLDescription,GLAutoID FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID AND groupID = " . current_companyID() . " AND masterCategory = 'BS') coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID 
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
    WHERE documentDate < '" . $financialBeginingDate["beginingDate"] . "' AND srp_erp_generalledger.companyID IN (" . join(',', $company) . ") GROUP BY GLAutoID,documentDate HAVING (" . join(' AND ', $having) . ")) 
    UNION ALL
    (SELECT $feilds coa.GLAutoID,documentDate FROM srp_erp_generalledger 
    INNER JOIN (SELECT chartofAccountID,GLSecondaryCode,GLDescription,GLAutoID FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID AND groupID = " . current_companyID() . ") coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID 
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
    WHERE documentDate BETWEEN '" . $financialBeginingDate["beginingDate"] . "' AND '" . format_date($this->input->post("from")) . "' AND srp_erp_generalledger.companyID IN(" . join(',', $company) . ") GROUP BY GLAutoID,documentDate HAVING (" . join(' AND ', $having) . "))) AS a
        LEFT JOIN
    ( SELECT GLSecondaryCode,GLDescription,GLAutoID,masterCategory,systemAccountCode FROM srp_erp_groupchartofaccounts WHERE groupID = " . current_companyID() . ") b ON a.GLAutoID = b.GLAutoID GROUP BY a.GLAutoID ORDER BY b.GLSecondaryCode ASC";
                $result = $this->db->query($sql)->result_array();
                return $result;
                break;
            case "3": /*YTD*/
                $company = $this->get_group_company();
                $financialBeginingDate = get_group_financial_year(format_date($this->input->post("from")));
                $feilds = "";
                $feilds2 = "";
                $having = array();
                $fieldNameChk = $this->input->post("fieldNameChk");
                if (isset($fieldNameChk)) { /*generate the query according to selected columns*/
                    foreach ($fieldNameChk as $val) {
                        $feilds .= "SUM(srp_erp_generalledger." . $val . ") as " . $val . ",";
                        $feilds2 .= "SUM(a." . $val . ") as " . $val . ",";
                        $having[] = '(' . $val . '!= -0 AND ' . $val . ' != 0)';
                        if ($val == "companyLocalAmount") {
                            $feilds .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        if ($val == "companyReportingAmount") {
                            $feilds .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        $feilds2 .= $val . "DecimalPlaces,";
                    }
                }
                $result = $this->db->query("SELECT $feilds2
    b.GLDescription,b.masterCategory,b.GLSecondaryCode,b.systemAccountCode,b.GLAutoID FROM 
    ((SELECT $feilds coa.GLAutoID,srp_erp_generalledger.documentDate FROM srp_erp_generalledger 
    INNER JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription,GLAutoID FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID AND groupID = " . current_companyID() . " AND masterCategory = 'BS') coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID 
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
    WHERE documentDate < '" . $financialBeginingDate["beginingDate"] . "' AND srp_erp_generalledger.companyID IN(" . join(',', $company) . ") GROUP BY coa.GLAutoID) 
    UNION ALL (SELECT $feilds coa.GLAutoID,documentDate FROM srp_erp_generalledger
     LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
     INNER JOIN (SELECT chartofAccountID,GLSecondaryCode,GLDescription,GLAutoID FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID AND groupID = " . current_companyID() . ") coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
WHERE documentDate BETWEEN '" . $financialBeginingDate["beginingDate"] . "' AND '" . format_date($this->input->post("from")) . "' AND srp_erp_generalledger.companyID IN(" . join(',', $company) . ") GROUP BY coa.GLAutoID)) AS a
        LEFT JOIN
        (SELECT GLSecondaryCode,GLDescription,GLAutoID,masterCategory,systemAccountCode FROM srp_erp_groupchartofaccounts WHERE groupID = " . current_companyID() . ") b ON a.GLAutoID = b.GLAutoID WHERE (" . join(' OR ', $having) . ") GROUP BY a.GLAutoID ORDER BY b.GLSecondaryCode ASC")->result_array();
                //echo $this->db->last_query();
                return $result;
                break;
        }
    }

    function get_finance_income_statement_report()
    {

        switch ($this->input->post('rptType')) {
            case "4":
            case "1": /*Month Wise*/
                $dmfrom = date('Y-m', strtotime($this->input->post("from")));
                $dmto = date('Y-m', strtotime($this->input->post("to")));
                $segment = $this->input->post("segment");
                $months = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month"); /*calculate months*/
                $feilds = "";
                $feilds2 = "";
                $feilds3 = "";
                $having = array();
                $fieldNameChk = $this->input->post("fieldNameChk");
                if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
                    foreach ($fieldNameChk as $val) {
                        if (!empty($months)) {
                            foreach ($months as $key => $val2) {
                                $feilds .= "SUM(if(DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') = '$key',srp_erp_generalledger." . $val . " * -1,0) ) as `" . $key . "`,SUM(bd.`" . $key . "`) as `budget$key`,";
                                $feilds2 .= "SUM(if(CONCAT(srp_erp_budgetdetail.budgetYear,'-',srp_erp_budgetdetail.budgetMonth) = '$key',srp_erp_budgetdetail." . $val . " * -1,0) ) as `" . $key . "`,";
                                $having[] = "(`" . $key . "` != 0 OR `" . $key . "` != - 0)";
                            }
                        }
                        if ($val == "companyLocalAmount") {
                            $feilds .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        if ($val == "companyReportingAmount") {
                            $feilds .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                    }
                }

                $result = $this->db->query("SELECT $feilds
	srp_erp_chartofaccounts.masterAutoID,
	srp_erp_chartofaccounts.GLAutoID,
	srp_erp_generalledger.documentDate,
	srp_erp_generalledger.segmentID,
	srp_erp_chartofaccounts.masterCategory,
	srp_erp_chartofaccounts.GLDescription,
	IF (
	srp_erp_chartofaccounts.subCategory = 'PLE',
	'EXPENSE',

IF (
	srp_erp_chartofaccounts.subCategory = 'PLI',
	'INCOME',
	'ND'
)
) AS mainCategory,
srp_erp_accountCategoryTypes.CategoryTypeDescription as subCategory,
ca2.GLDescription as subsubCategory
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
INNER JOIN srp_erp_accountCategoryTypes ON srp_erp_accountCategoryTypes.accountCategoryTypeID = srp_erp_chartofaccounts.accountCategoryTypeID
LEFT JOIN (
	SELECT
		GLDescription,
GLAutoID
	FROM
		srp_erp_chartofaccounts
	WHERE
		srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
) ca2 ON (
	ca2.GLAutoID = srp_erp_chartofaccounts.masterAutoID
)
LEFT JOIN (SELECT $feilds2 srp_erp_budgetdetail.GLAutoID FROM srp_erp_budgetdetail INNER JOIN srp_erp_budgetmaster ON srp_erp_budgetdetail.budgetAutoID = srp_erp_budgetmaster.budgetAutoID WHERE srp_erp_budgetdetail.companyID = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_budgetdetail.segmentID IN(" . join(',', $segment) . ") AND CONCAT(budgetYear,'-',LPAD(budgetMonth,2,0)) BETWEEN '" . $dmfrom . "' AND '" . $dmto . "' GROUP BY GLAutoID) bd ON (bd.GLAutoID = srp_erp_generalledger.GLAutoID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
WHERE
	srp_erp_generalledger.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_generalledger.segmentID IN(" . join(',', $segment) . ") 
GROUP BY
	srp_erp_chartofaccounts.masterAutoID,
	ca2.GLDescription,
	srp_erp_chartofaccounts.accountCategoryTypeID,
	srp_erp_chartofaccounts.GLAutoID,
	srp_erp_chartofaccounts.GLDescription,
	mainCategory HAVING (" . join(' OR ', $having) . ") ORDER BY sortOrder")->result_array();
                //echo $this->db->last_query();
                return $result;
                break;
            case "5":
            case "3": /*YTD*/
                $segment = $this->input->post("segment");
                $dmfrom = date('Y-m', strtotime($this->input->post("from")));
                $dmto = date('Y-m', strtotime($this->input->post("to")));
                $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                $feilds = "";
                $feilds2 = "";
                $having = array();
                $fieldNameChk = $this->input->post("fieldNameChk");
                if (isset($fieldNameChk)) {
                    foreach ($fieldNameChk as $val) { /*generate the query according to selectd columns*/
                        $feilds .= "SUM(srp_erp_generalledger." . $val . ") * -1 as " . $val . ", SUM(bd." . $val . ") * -1 as budget" . $val . ",";
                        $feilds2 .= "SUM(srp_erp_budgetdetail." . $val . ") * -1 as " . $val . ",";
                        $having[] = $val . ' != -0 OR ' . $val . ' != 0';
                        if ($val == "companyLocalAmount") {
                            $feilds .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        if ($val == "companyReportingAmount") {
                            $feilds .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                    }
                }
                $result = $this->db->query("SELECT $feilds 
	srp_erp_chartofaccounts.masterAutoID,
	srp_erp_chartofaccounts.GLAutoID,
	srp_erp_generalledger.documentDate,
	srp_erp_generalledger.segmentID,
	srp_erp_chartofaccounts.masterCategory,
	srp_erp_chartofaccounts.GLDescription,
	srp_erp_accountCategoryTypes.accountCategoryTypeID,
IF (
	srp_erp_chartofaccounts.subCategory = 'PLE',
	'EXPENSE',

IF (
	srp_erp_chartofaccounts.subCategory = 'PLI',
	'INCOME',
	'ND'
)
) AS mainCategory,
srp_erp_accountCategoryTypes.CategoryTypeDescription as subCategory,
ca2.GLDescription as subsubCategory
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
INNER JOIN srp_erp_accountCategoryTypes ON srp_erp_accountCategoryTypes.accountCategoryTypeID = srp_erp_chartofaccounts.accountCategoryTypeID
LEFT JOIN (
	SELECT
		GLDescription,
GLAutoID
	FROM
		srp_erp_chartofaccounts
	WHERE
		srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
) ca2 ON (
	ca2.GLAutoID = srp_erp_chartofaccounts.masterAutoID
)
LEFT JOIN (SELECT $feilds2 srp_erp_budgetdetail.GLAutoID FROM srp_erp_budgetdetail INNER JOIN srp_erp_budgetmaster ON srp_erp_budgetdetail.budgetAutoID = srp_erp_budgetmaster.budgetAutoID WHERE srp_erp_budgetdetail.companyID = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_budgetdetail.segmentID IN(" . join(',', $segment) . ") AND CONCAT(budgetYear,'-',budgetMonth) BETWEEN '" . $dmfrom . "' AND '" . $dmto . "' GROUP BY GLAutoID) bd ON (bd.GLAutoID = srp_erp_generalledger.GLAutoID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
WHERE
	srp_erp_generalledger.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_generalledger.segmentID IN(" . join(',', $segment) . ")
GROUP BY
	srp_erp_chartofaccounts.masterAutoID,
	ca2.GLDescription,
	srp_erp_chartofaccounts.accountCategoryTypeID,
	srp_erp_chartofaccounts.GLAutoID,
	srp_erp_chartofaccounts.GLDescription,
	mainCategory HAVING (" . join(' OR ', $having) . ") ORDER BY sortOrder;")->result_array();
                //echo $this->db->last_query();
                return $result;
                break;
        }
    }


    function get_finance_income_statement_group_report()
    {

        switch ($this->input->post('rptType')) {
            case "4":
            case "1": /*Month Wise*/
                $company = $this->get_group_company();
                $dmfrom = date('Y-m', strtotime($this->input->post("from")));
                $dmto = date('Y-m', strtotime($this->input->post("to")));
                $segment = $this->input->post("segment");
                $months = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month"); /*calculate months*/
                $feilds = "";
                $feilds2 = "";
                $feilds3 = "";
                $having = array();
                $fieldNameChk = $this->input->post("fieldNameChk");
                if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
                    foreach ($fieldNameChk as $val) {
                        if (!empty($months)) {
                            foreach ($months as $key => $val2) {
                                $feilds .= "SUM(if(DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') = '$key',srp_erp_generalledger." . $val . " * -1,0) ) as `" . $key . "`,SUM(bd.`" . $key . "`) as `budget$key`,";
                                $feilds2 .= "SUM(if(CONCAT(srp_erp_budgetdetail.budgetYear,'-',srp_erp_budgetdetail.budgetMonth) = '$key',srp_erp_budgetdetail." . $val . " * -1,0) ) as `" . $key . "`,";
                                $having[] = "(`" . $key . "` != 0 OR `" . $key . "` != - 0)";
                            }
                        }
                        if ($val == "companyLocalAmount") {
                            $feilds .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        if ($val == "companyReportingAmount") {
                            $feilds .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                    }
                }

                $result = $this->db->query("SELECT $feilds
	coa.masterAutoID,
	coa.GLAutoID,
	srp_erp_generalledger.documentDate,
	srp_erp_generalledger.segmentID,
	coa.masterCategory,
	coa.GLDescription,
	IF (
	coa.subCategory = 'PLE',
	'EXPENSE',

IF (
	coa.subCategory = 'PLI',
	'INCOME',
	'ND'
)
) AS mainCategory,
srp_erp_accountCategoryTypes.CategoryTypeDescription as subCategory,
ca2.GLDescription as subsubCategory
FROM
	srp_erp_generalledger
INNER JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription,accountCategoryTypeID,masterAutoID,GLAutoID,masterCategory,subCategory FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID WHERE groupID = " . current_companyID() . " AND masterCategory = 'PL') coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID
INNER JOIN srp_erp_accountCategoryTypes ON srp_erp_accountCategoryTypes.accountCategoryTypeID = coa.accountCategoryTypeID
LEFT JOIN (
	SELECT chartofAccountID,GLSecondaryCode,GLDescription,accountCategoryTypeID,GLAutoID FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID WHERE groupID = " . current_companyID() . " AND masterCategory = 'PL'
) ca2 ON (
	ca2.GLAutoID = coa.masterAutoID
)
INNER JOIN ( SELECT srp_erp_groupsegmentdetails.segmentID,description,segmentCode FROM srp_erp_groupsegment INNER JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID AND groupID = " . current_companyID() . " WHERE srp_erp_groupsegment.segmentID IN(".join(',',$segment).")) seg ON srp_erp_generalledger.segmentID = seg.segmentID
LEFT JOIN (SELECT $feilds2 srp_erp_budgetdetail.GLAutoID FROM srp_erp_budgetdetail INNER JOIN srp_erp_budgetmaster ON srp_erp_budgetdetail.budgetAutoID = srp_erp_budgetmaster.budgetAutoID 
 INNER JOIN ( SELECT srp_erp_groupsegmentdetails.segmentID,description,segmentCode FROM srp_erp_groupsegment INNER JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID AND groupID = " . current_companyID() . " WHERE srp_erp_groupsegment.segmentID IN(".join(',',$segment).")) seg ON srp_erp_budgetdetail.segmentID = seg.segmentID
 WHERE srp_erp_budgetdetail.companyID IN (".join(',',$company).") AND CONCAT(budgetYear,'-',LPAD(budgetMonth,2,0)) BETWEEN '" . $dmfrom . "' AND '" . $dmto . "' GROUP BY GLAutoID) bd ON (bd.GLAutoID = srp_erp_generalledger.GLAutoID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
WHERE
	srp_erp_generalledger.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "' AND srp_erp_generalledger.companyID IN (".join(',',$company).")
GROUP BY
	coa.masterAutoID,
	ca2.GLDescription,
	coa.accountCategoryTypeID,
	coa.GLAutoID,
	coa.GLDescription,
	mainCategory HAVING (" . join(' OR ', $having) . ") ORDER BY sortOrder")->result_array();
                //echo $this->db->last_query();
                return $result;
                break;
            case "5":
            case "3": /*YTD*/
                $company = $this->get_group_company();
                $segment = $this->input->post("segment");
                $dmfrom = date('Y-m', strtotime($this->input->post("from")));
                $dmto = date('Y-m', strtotime($this->input->post("to")));
                $financialBeginingDate = get_group_financial_year(format_date($this->input->post("from")));
                $feilds = "";
                $feilds2 = "";
                $having = array();
                $fieldNameChk = $this->input->post("fieldNameChk");
                if (isset($fieldNameChk)) {
                    foreach ($fieldNameChk as $val) { /*generate the query according to selectd columns*/
                        $feilds .= "SUM(srp_erp_generalledger." . $val . ") * -1 as " . $val . ", SUM(bd." . $val . ") * -1 as budget" . $val . ",";
                        $feilds2 .= "SUM(srp_erp_budgetdetail." . $val . ") * -1 as " . $val . ",";
                        $having[] = $val . ' != -0 OR ' . $val . ' != 0';
                        if ($val == "companyLocalAmount") {
                            $feilds .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        if ($val == "companyReportingAmount") {
                            $feilds .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                    }
                }
                $result = $this->db->query("SELECT $feilds 
	coa.masterAutoID,
	coa.GLAutoID,
	srp_erp_generalledger.documentDate,
	srp_erp_generalledger.segmentID,
	coa.masterCategory,
	coa.GLDescription,
	srp_erp_accountCategoryTypes.accountCategoryTypeID,
IF (
	coa.subCategory = 'PLE',
	'EXPENSE',

IF (
	coa.subCategory = 'PLI',
	'INCOME',
	'ND'
)
) AS mainCategory,
srp_erp_accountCategoryTypes.CategoryTypeDescription as subCategory,
ca2.GLDescription as subsubCategory
FROM
	srp_erp_generalledger
INNER JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription,accountCategoryTypeID,masterAutoID,GLAutoID,masterCategory,subCategory FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID WHERE groupID = " . current_companyID() . " AND masterCategory = 'PL') coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID
INNER JOIN srp_erp_accountCategoryTypes ON srp_erp_accountCategoryTypes.accountCategoryTypeID = coa.accountCategoryTypeID
LEFT JOIN (
	SELECT chartofAccountID,GLSecondaryCode,GLDescription,accountCategoryTypeID,GLAutoID FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID WHERE groupID = " . current_companyID() . " AND masterCategory = 'PL'
) ca2 ON (
	ca2.GLAutoID = coa.masterAutoID
)
INNER JOIN ( SELECT srp_erp_groupsegmentdetails.segmentID,description,segmentCode FROM srp_erp_groupsegment INNER JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID AND groupID = " . current_companyID() . " WHERE srp_erp_groupsegment.segmentID IN(".join(',',$segment).")) seg ON srp_erp_generalledger.segmentID = seg.segmentID
LEFT JOIN (SELECT $feilds2 srp_erp_budgetdetail.GLAutoID FROM srp_erp_budgetdetail INNER JOIN srp_erp_budgetmaster ON srp_erp_budgetdetail.budgetAutoID = srp_erp_budgetmaster.budgetAutoID  INNER JOIN ( SELECT srp_erp_groupsegmentdetails.segmentID,description,segmentCode FROM srp_erp_groupsegment INNER JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID AND groupID = " . current_companyID() . " WHERE srp_erp_groupsegment.segmentID IN(".join(',',$segment).")) seg ON srp_erp_budgetdetail.segmentID = seg.segmentID WHERE srp_erp_budgetdetail.companyID IN (".join(',',$company).")  AND CONCAT(budgetYear,'-',budgetMonth) BETWEEN '" . $dmfrom . "' AND '" . $dmto . "' GROUP BY GLAutoID) bd ON (bd.GLAutoID = srp_erp_generalledger.GLAutoID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
WHERE
	srp_erp_generalledger.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "' AND srp_erp_generalledger.companyID IN (".join(',',$company).")
GROUP BY
	coa.masterAutoID,
	ca2.GLDescription,
	coa.accountCategoryTypeID,
	coa.GLAutoID,
	coa.GLDescription,
	mainCategory HAVING (" . join(' OR ', $having) . ") ORDER BY sortOrder;")->result_array();
                //echo $this->db->last_query();
                return $result;
                break;
        }
    }

    function get_finance_balance_sheet_report()
    {
        switch ($this->input->post('rptType')) {
            case "1": /*Month Wise*/
                $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                $months = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month"); /*calculate months*/
                $feilds = "";
                $feilds2 = "";
                $feilds3 = "";
                $having = array();
                $fieldNameChk = $this->input->post("fieldNameChk");
                if (isset($fieldNameChk)) { /*generate the query according to selected columns*/
                    foreach ($fieldNameChk as $val) {
                        if (!empty($months)) {
                            foreach ($months as $key => $val2) {
                                $feilds .= "SUM(if(srp_erp_chartofaccounts.subCategory = 'BSA',if(DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') <= '$key',srp_erp_generalledger." . $val . ",0),if(DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') <= '$key',srp_erp_generalledger." . $val . " * -1,0)) ) as `" . $key . "`,";
                                $feilds2 .= "a.`" . $key . "` as `" . $key . "`,";
                                $feilds3 .= "SUM(if(DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') <= '$key',srp_erp_generalledger." . $val . ",0) ) as `" . $key . "`,";
                                $having[] = "`" . $key . "` != 0";
                            }
                        }
                        if ($val == "companyLocalAmount") {
                            $feilds .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                            $feilds3 .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        if ($val == "companyReportingAmount") {
                            $feilds .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                            $feilds3 .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        $feilds2 .= "a." . $val . "DecimalPlaces,";
                    }
                }

                $result = $this->db->query("SELECT $feilds2
a.sortOrder,
	a.GLDescription,
a.mainCategory,
a.subCategory,
a.subsubCategory,
 a.masterCategory,
a.GLAutoID
FROM ((SELECT $feilds
srp_erp_accountCategoryTypes.sortOrder,
	srp_erp_chartofaccounts.GLDescription,
IF (
srp_erp_chartofaccounts.subCategory = 'BSA',
	'ASSETS',
IF (
srp_erp_chartofaccounts.subCategory = 'BSL',
	'LIABILITIES',
	'ND'
)
) AS mainCategory,
srp_erp_accountCategoryTypes.CategoryTypeDescription as subCategory,
ca2.GLDescription as subsubCategory,
srp_erp_chartofaccounts.masterCategory,
srp_erp_generalledger.GLAutoID
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'BS' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
INNER JOIN srp_erp_accountCategoryTypes ON srp_erp_accountCategoryTypes.accountCategoryTypeID = srp_erp_chartofaccounts.accountCategoryTypeID
LEFT JOIN (
                SELECT
		GLDescription,
GLAutoID
	FROM
		srp_erp_chartofaccounts
	WHERE
		srp_erp_chartofaccounts.masterCategory = 'BS' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
) ca2 ON (
                ca2.GLAutoID = srp_erp_chartofaccounts.masterAutoID
            )
            LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
WHERE
	srp_erp_generalledger.documentDate <= '" . format_date($this->input->post("from")) . "' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . "
GROUP BY
	srp_erp_chartofaccounts.masterAutoID,
	ca2.GLDescription,
	srp_erp_chartofaccounts.accountCategoryTypeID,
	srp_erp_chartofaccounts.GLAutoID,
	srp_erp_chartofaccounts.GLDescription,
	mainCategory ORDER BY sortOrder,mainCategory) UNION ALL
            (SELECT $feilds3
	'9' as sortOrder,
'Retained Earnings' as GLDescription,
'LIABILITIES' AS mainCategory,
'Equity' as subCategory,
'Equity' as subsubCategory,
'-' as masterCategory,
'-' as GLAutoID
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'BS' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
WHERE
	srp_erp_generalledger.documentDate <= '" . format_date($this->input->post("from")) . "' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . ")) as a GROUP BY
	a.mainCategory,a.subCategory,a.subsubCategory,a.GLDescription HAVING (" . join(' OR ', $having) . ") ORDER BY a.sortOrder,a.mainCategory")->result_array();
                //echo $this->db->last_query();
                return $result;
                break;
            case "3": /*YTD*/
                $feilds = "";
                $feilds2 = "";
                $feilds3 = "";
                $having = array();
                $fieldNameChk = $this->input->post("fieldNameChk");
                if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
                    foreach ($fieldNameChk as $val) {
                        $feilds .= "if(srp_erp_chartofaccounts.subCategory = 'BSA',SUM(srp_erp_generalledger." . $val . "),SUM(srp_erp_generalledger." . $val . ")*-1) as " . $val . ",";
                        $feilds3 .= "SUM(srp_erp_generalledger." . $val . ") as " . $val . ",";
                        $feilds2 .= "SUM(a." . $val . ") as " . $val . ",";
                        if ($val == "companyLocalAmount") {
                            $feilds .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                            $feilds3 .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        if ($val == "companyReportingAmount") {
                            $feilds .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                            $feilds3 .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        $feilds2 .= "a." . $val . "DecimalPlaces,";
                        $having[] = $val . '!= -0 AND ' . $val . ' != 0';
                    }
                }
                $sql = "SELECT $feilds2
a.sortOrder,
	a.GLDescription,
a.mainCategory,
a.subCategory,
a.masterCategory,
a.GLAutoID,
a.subsubCategory
FROM ((SELECT $feilds
srp_erp_accountCategoryTypes.sortOrder,
	srp_erp_chartofaccounts.GLDescription,
IF (
	srp_erp_chartofaccounts.subCategory = 'BSA',
	'ASSETS',
IF (
	srp_erp_chartofaccounts.subCategory = 'BSL',
	'LIABILITIES',
	'ND'
)
) AS mainCategory,
srp_erp_chartofaccounts.masterCategory,
srp_erp_generalledger.GLAutoID,
srp_erp_accountCategoryTypes.CategoryTypeDescription as subCategory,
ca2.GLDescription as subsubCategory
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'BS' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
INNER JOIN srp_erp_accountCategoryTypes ON srp_erp_accountCategoryTypes.accountCategoryTypeID = srp_erp_chartofaccounts.accountCategoryTypeID
LEFT JOIN (
	SELECT
		GLDescription,
GLAutoID
	FROM
		srp_erp_chartofaccounts
	WHERE
		srp_erp_chartofaccounts.masterCategory = 'BS' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
) ca2 ON (
	ca2.GLAutoID = srp_erp_chartofaccounts.masterAutoID
)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
WHERE
	srp_erp_generalledger.documentDate <= '" . format_date($this->input->post("from")) . "' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . "
GROUP BY
	srp_erp_chartofaccounts.masterAutoID,
	ca2.GLDescription,
	srp_erp_chartofaccounts.accountCategoryTypeID,
	srp_erp_chartofaccounts.GLAutoID,
	srp_erp_chartofaccounts.GLDescription,
	mainCategory ORDER BY sortOrder,mainCategory) UNION ALL
	(SELECT $feilds3
	'9' as sortOrder,
'Retained Earnings' as GLDescription,
'LIABILITIES' AS mainCategory,
'Equity' as subCategory,
'Equity' as subsubCategory,
'-' as masterCategory,
'-' as GLAutoID
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'BS' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
WHERE
	srp_erp_generalledger.documentDate <= '" . format_date($this->input->post("from")) . "' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . ")) as a GROUP BY
	a.mainCategory,a.subCategory,a.subsubCategory,a.GLDescription HAVING (" . join(' AND ', $having) . ") ORDER BY a.mainCategory,a.sortOrder DESC;";
                $result = $this->db->query($sql)->result_array();
                //echo $this->db->last_query();
                return $result;
                break;
        }
    }


    function get_finance_balance_sheet_group_report()
    {
        switch ($this->input->post('rptType')) {
            case "1": /*Month Wise*/
                $company = $this->get_group_company();
                $financialBeginingDate = get_group_financial_year(format_date($this->input->post("from")));
                $months = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month"); /*calculate months*/
                $feilds = "";
                $feilds2 = "";
                $feilds3 = "";
                $having = array();
                $fieldNameChk = $this->input->post("fieldNameChk");
                if (isset($fieldNameChk)) { /*generate the query according to selected columns*/
                    foreach ($fieldNameChk as $val) {
                        if (!empty($months)) {
                            foreach ($months as $key => $val2) {
                                $feilds .= "SUM(if(coa.subCategory = 'BSA',if(DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') <= '$key',srp_erp_generalledger." . $val . ",0),if(DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') <= '$key',srp_erp_generalledger." . $val . " * -1,0)) ) as `" . $key . "`,";
                                $feilds2 .= "a.`" . $key . "` as `" . $key . "`,";
                                $feilds3 .= "SUM(if(DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') <= '$key',srp_erp_generalledger." . $val . ",0) ) as `" . $key . "`,";
                                $having[] = "`" . $key . "` != 0";
                            }
                        }
                        if ($val == "companyLocalAmount") {
                            $feilds .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                            $feilds3 .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        if ($val == "companyReportingAmount") {
                            $feilds .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                            $feilds3 .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        $feilds2 .= "a." . $val . "DecimalPlaces,";
                    }
                }

                $result = $this->db->query("SELECT $feilds2
a.sortOrder,
	a.GLDescription,
a.mainCategory,
a.subCategory,
a.subsubCategory,
 a.masterCategory,
a.GLAutoID
FROM ((SELECT $feilds
srp_erp_accountCategoryTypes.sortOrder,
	coa.GLDescription,
IF (
coa.subCategory = 'BSA',
	'ASSETS',
IF (
coa.subCategory = 'BSL',
	'LIABILITIES',
	'ND'
)
) AS mainCategory,
srp_erp_accountCategoryTypes.CategoryTypeDescription as subCategory,
ca2.GLDescription as subsubCategory,
coa.masterCategory,
coa.GLAutoID
FROM
	srp_erp_generalledger
INNER JOIN ( SELECT GLAutoID,chartofAccountID,GLSecondaryCode,GLDescription,accountCategoryTypeID,subCategory,masterCategory,masterAutoID FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID WHERE groupID = " . current_companyID() . " AND srp_erp_groupchartofaccounts.masterCategory = 'BS') coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID
INNER JOIN srp_erp_accountCategoryTypes ON srp_erp_accountCategoryTypes.accountCategoryTypeID = coa.accountCategoryTypeID
LEFT JOIN (
               SELECT chartofAccountID,GLSecondaryCode,GLDescription FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID WHERE groupID = " . current_companyID() . " AND srp_erp_groupchartofaccounts.masterCategory = 'BS'
) ca2 ON (
                ca2.chartofAccountID = coa.masterAutoID
            )
            LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
WHERE
	srp_erp_generalledger.documentDate <= '" . format_date($this->input->post("from")) . "' AND srp_erp_generalledger.companyID IN (" . join(',',$company) . ")
GROUP BY
	coa.masterAutoID,
	ca2.GLDescription,
	coa.accountCategoryTypeID,
	coa.GLAutoID,
	coa.GLDescription,
	mainCategory ORDER BY sortOrder,mainCategory) UNION ALL
            (SELECT $feilds3
	'9' as sortOrder,
'Retained Earnings' as GLDescription,
'LIABILITIES' AS mainCategory,
'Equity' as subCategory,
'Equity' as subsubCategory,
'-' as masterCategory,
'-' as GLAutoID
FROM
	srp_erp_generalledger
INNER JOIN ( SELECT GLAutoID,chartofAccountID,GLSecondaryCode,GLDescription,accountCategoryTypeID,subCategory,masterCategory FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID WHERE groupID = " . current_companyID() . " AND srp_erp_groupchartofaccounts.masterCategory = 'BS') coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
WHERE
	srp_erp_generalledger.documentDate <= '" . format_date($this->input->post("from")) . "' AND srp_erp_generalledger.companyID IN (" . join(',',$company) . "))) as a GROUP BY
	a.mainCategory,a.subCategory,a.subsubCategory,a.GLDescription HAVING (" . join(' OR ', $having) . ") ORDER BY a.sortOrder,a.mainCategory")->result_array();
                //echo $this->db->last_query();
                return $result;
                break;
            case "3": /*YTD*/
                $company = $this->get_group_company();
                $feilds = "";
                $feilds2 = "";
                $feilds3 = "";
                $having = array();
                $fieldNameChk = $this->input->post("fieldNameChk");
                if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
                    foreach ($fieldNameChk as $val) {
                        $feilds .= "if(coa.subCategory = 'BSA',SUM(srp_erp_generalledger." . $val . "),SUM(srp_erp_generalledger." . $val . ")*-1) as " . $val . ",";
                        $feilds3 .= "SUM(srp_erp_generalledger." . $val . ") as " . $val . ",";
                        $feilds2 .= "SUM(a." . $val . ") as " . $val . ",";
                        if ($val == "companyLocalAmount") {
                            $feilds .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                            $feilds3 .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        if ($val == "companyReportingAmount") {
                            $feilds .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                            $feilds3 .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                        }
                        $feilds2 .= "a." . $val . "DecimalPlaces,";
                        $having[] = $val . '!= -0 AND ' . $val . ' != 0';
                    }
                }
                $sql = "SELECT $feilds2
a.sortOrder,
	a.GLDescription,
a.mainCategory,
a.subCategory,
a.masterCategory,
a.GLAutoID,
a.subsubCategory
FROM ((SELECT $feilds
srp_erp_accountCategoryTypes.sortOrder,
	coa.GLDescription,
IF (
	coa.subCategory = 'BSA',
	'ASSETS',
IF (
	coa.subCategory = 'BSL',
	'LIABILITIES',
	'ND'
)
) AS mainCategory,
coa.masterCategory,
coa.GLAutoID,
srp_erp_accountCategoryTypes.CategoryTypeDescription as subCategory,
ca2.GLDescription as subsubCategory
FROM
	srp_erp_generalledger
INNER JOIN ( SELECT GLAutoID,chartofAccountID,GLSecondaryCode,GLDescription,accountCategoryTypeID,subCategory,masterCategory,masterAutoID FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID WHERE groupID = " . current_companyID() . " AND srp_erp_groupchartofaccounts.masterCategory = 'BS') as coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID
INNER JOIN srp_erp_accountCategoryTypes ON srp_erp_accountCategoryTypes.accountCategoryTypeID = coa.accountCategoryTypeID
LEFT JOIN (
               SELECT chartofAccountID,GLSecondaryCode,GLDescription FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID WHERE groupID = " . current_companyID() . " AND srp_erp_groupchartofaccounts.masterCategory = 'BS'
) ca2 ON (
                ca2.chartofAccountID = coa.masterAutoID
            )
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
WHERE
	srp_erp_generalledger.documentDate <= '" . format_date($this->input->post("from")) . "' AND srp_erp_generalledger.companyID IN (" . join(',',$company) . ")
GROUP BY
	coa.masterAutoID,
	ca2.GLDescription,
	coa.accountCategoryTypeID,
	coa.GLAutoID,
	coa.GLDescription,
	mainCategory ORDER BY sortOrder,mainCategory) UNION ALL
	(SELECT $feilds3
	'9' as sortOrder,
'Retained Earnings' as GLDescription,
'LIABILITIES' AS mainCategory,
'Equity' as subCategory,
'Equity' as subsubCategory,
'-' as masterCategory,
'-' as GLAutoID
FROM
	srp_erp_generalledger
INNER JOIN ( SELECT GLAutoID,chartofAccountID,GLSecondaryCode,GLDescription,accountCategoryTypeID,subCategory,masterCategory FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID WHERE groupID = " . current_companyID() . " AND srp_erp_groupchartofaccounts.masterCategory = 'BS') coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
WHERE
	srp_erp_generalledger.documentDate <= '" . format_date($this->input->post("from")) . "' AND srp_erp_generalledger.companyID IN (" . join(',',$company) . "))) as a GROUP BY
	a.mainCategory,a.subCategory,a.subsubCategory,a.GLDescription HAVING (" . join(' AND ', $having) . ") ORDER BY a.mainCategory,a.sortOrder DESC;";
                $result = $this->db->query($sql)->result_array();
                //echo $this->db->last_query();
                return $result;
                break;
        }
    }

    function get_finance_general_ledger_report()
    {
        $segment = $this->input->post("segment");
        $glcode = $this->input->post("glCodeTo");

        $segmentFilter = array_filter(fetch_segment_reports(true));
        unset($segmentFilter['']);
        $segmentFilterCount = count($segmentFilter);
        $segmentCount = count($segment);
        $segmentQry = "";
        if ($segmentFilterCount != $segmentCount) {
            $segmentQry = "AND srp_erp_generalledger.segmentID IN(" . join(',', $segment) . ")";
        }
        /*
        $i = 1;
        $segmentOR = '( ';
        if (!empty($segment)) { generate the query according to selectd segment
            foreach ($segment as $segment_val) {
                if ($i != 1) {
                    $segmentOR .= ' OR ';
                }
                $segmentOR .= " srp_erp_generalledger.segmentID = '" . $segment_val . "' ";
                $i++;
            }
        }
        $segmentOR .= ' ) ';*/

        $feilds = "";
        $feilds2 = "";
        $feilds3 = "";
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (!empty($fieldNameChk)) { /*generate the query according to selectd columns*/
            foreach ($fieldNameChk as $val) {
                if ($val == "segmentID") {
                    $feilds[] = "CONCAT(srp_erp_segment.segmentCode,'-',srp_erp_segment.description) as segmentID";
                } else if ($val == "partySystemCode") {
                    $feilds[] = "IF(cust.customerSystemCode IS NOT NULL,CONCAT(cust.customerSystemCode,'-',cust.customerName),IF (supp.supplierSystemCode IS NOT NULL,CONCAT(supp.supplierSystemCode,'-',supp.supplierName),'-')) AS partySystemCode";
                } else if ($val == "GLSecondaryCode") {
                    $feilds[] = "srp_erp_chartofaccounts." . $val;
                } else if ($val == "document") {
                    $feilds[] = "dc." . $val;
                } else {
                    $feilds[] = "srp_erp_generalledger." . $val;
                }

                $feilds2[] = "a." . $val;
                if ($val == "companyLocalAmount") {
                    $feilds3[] = "SUM(companyLocalAmount) as " . $val;
                    $feilds3[] = "CL.DecimalPlaces as " . $val . "DecimalPlaces";
                    $feilds[] = "CL.DecimalPlaces as " . $val . "DecimalPlaces";
                    $feilds2[] = "a." . $val . "DecimalPlaces";
                } elseif ($val == "companyReportingAmount") {
                    $feilds3[] = "SUM(companyReportingAmount) as " . $val;
                    $feilds3[] = "CR.DecimalPlaces as " . $val . "DecimalPlaces";
                    $feilds[] = "CR.DecimalPlaces as " . $val . "DecimalPlaces";
                    $feilds2[] = "a." . $val . "DecimalPlaces";
                } elseif ($val == "documentNarration") {
                    $feilds3[] = "'CF Balance' as " . $val;
                } elseif ($val == "documentDate") {
                    //$feilds3[] = "'1970-01-01' as documentDate";
                    $feilds3[] = "'' as documentDate";
                    $feilds2[] = "DATE_FORMAT(a.documentDate,'" . $this->format . "') as documentDate";
                    $feilds2[] = "a.documentDate as documentDate2";
                    $feilds2[] = "a.documentDate as documentDateSort";
                } else {
                    $feilds3[] = "'-' as " . $val;
                }

            }
        }
        $feilds = join(',', $feilds);
        $feilds2 = join(',', $feilds2);
        $feilds3 = join(',', $feilds3);
        $sql = "SELECT $feilds2,a.documentCode,a.documentMasterAutoID,a.GLDescription,a.masterCategory,a.GLAutoID FROM ((SELECT 
  $feilds,srp_erp_generalledger.documentCode,srp_erp_generalledger.documentMasterAutoID,srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.masterCategory,srp_erp_generalledger.GLAutoID
 FROM srp_erp_generalledger
 INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
 LEFT JOIN srp_erp_segment ON srp_erp_generalledger.segmentID = srp_erp_segment.segmentID AND srp_erp_segment.companyID = " . $this->common_data['company_data']['company_id'] . "
 LEFT JOIN (SELECT * FROM srp_erp_customermaster WHERE companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY customerAutoID) cust ON srp_erp_generalledger.partyAutoID = cust.customerAutoID AND srp_erp_generalledger.partyType = 'CUS'
 LEFT JOIN (SELECT * FROM srp_erp_suppliermaster WHERE companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY supplierAutoID) supp ON srp_erp_generalledger.partyAutoID = supp.supplierAutoID AND srp_erp_generalledger.partyType = 'SUP'
 LEFT JOIN (SELECT document,documentID FROM srp_erp_documentcodemaster WHERE companyID = " . $this->common_data['company_data']['company_id'] . ") dc ON (dc.documentID = srp_erp_generalledger.documentCode)
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
 WHERE srp_erp_generalledger.GLAutoID IN(" . join(',', $glcode) . ") $segmentQry AND srp_erp_generalledger.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . "
 ORDER BY srp_erp_generalledger.documentType,srp_erp_generalledger.documentDate ASC) UNION ALL
 (SELECT $feilds3,srp_erp_generalledger.documentCode,srp_erp_generalledger.documentMasterAutoID,srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.masterCategory,srp_erp_generalledger.GLAutoID FROM srp_erp_generalledger 
 INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . " 
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
 WHERE srp_erp_generalledger.GLAutoID IN(" . join(',', $glcode) . ")  $segmentQry AND srp_erp_chartofaccounts.masterCategory = 'BS' AND srp_erp_generalledger.documentDate < '" . format_date($this->input->post("from")) . "' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY srp_erp_generalledger.GLAutoID )) as a ORDER BY documentDate2 asc";
        $result = $this->db->query($sql)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_finance_general_ledger_group_report()
    {
        $company = $this->get_group_company();
        $segment = $this->input->post("segment");
        $glcode = $this->input->post("glCodeTo");

        $segmentFilter = array_filter(fetch_segment(true));
        unset($segmentFilter['']);
        $segmentFilterCount = count($segmentFilter);
        $segmentCount = count($segment);
        $segmentQry = "";

        if ($segmentFilterCount != $segmentCount) {
            $segmentQry = "srp_erp_generalledger.segmentID IN(" . join(',', $segment) . ")";
        }
        /*
        $i = 1;
        $segmentOR = '( ';
        if (!empty($segment)) { generate the query according to selectd segment
            foreach ($segment as $segment_val) {
                if ($i != 1) {
                    $segmentOR .= ' OR ';
                }
                $segmentOR .= " srp_erp_generalledger.segmentID = '" . $segment_val . "' ";
                $i++;
            }
        }
        $segmentOR .= ' ) ';*/

        $feilds = "";
        $feilds2 = "";
        $feilds3 = "";
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (!empty($fieldNameChk)) { /*generate the query according to selectd columns*/
            foreach ($fieldNameChk as $val) {
                if ($val == "segmentID") {
                    $feilds[] = "CONCAT(seg.segmentCode,'-',seg.description) as segmentID";
                } else if ($val == "partySystemCode") {
                    $feilds[] = "IF(cust.groupcustomerSystemCode IS NOT NULL,CONCAT(cust.groupcustomerSystemCode,'-',cust.groupCustomerName),IF (supp.groupSupplierSystemCode IS NOT NULL,CONCAT(supp.groupSupplierSystemCode,'-',supp.groupSupplierName),'-')) AS partySystemCode";
                } else if ($val == "GLSecondaryCode") {
                    $feilds[] = "coa." . $val;
                } else if ($val == "document") {
                    $feilds[] = "dc." . $val;
                } else {
                    $feilds[] = "srp_erp_generalledger." . $val;
                }

                $feilds2[] = "a." . $val;
                if ($val == "companyLocalAmount") {
                    $feilds3[] = "SUM(companyLocalAmount) as " . $val;
                    $feilds3[] = "CL.DecimalPlaces as " . $val . "DecimalPlaces";
                    $feilds[] = "CL.DecimalPlaces as " . $val . "DecimalPlaces";
                    $feilds2[] = "a." . $val . "DecimalPlaces";
                } elseif ($val == "companyReportingAmount") {
                    $feilds3[] = "SUM(companyReportingAmount) as " . $val;
                    $feilds3[] = "CR.DecimalPlaces as " . $val . "DecimalPlaces";
                    $feilds[] = "CR.DecimalPlaces as " . $val . "DecimalPlaces";
                    $feilds2[] = "a." . $val . "DecimalPlaces";
                } elseif ($val == "documentNarration") {
                    $feilds3[] = "'CF Balance' as " . $val;
                } elseif ($val == "documentDate") {
                    //$feilds3[] = "'1970-01-01' as documentDate";
                    $feilds3[] = "'' as documentDate";
                    $feilds2[] = "DATE_FORMAT(a.documentDate,'" . $this->format . "') as documentDate";
                    $feilds2[] = "a.documentDate as documentDateSort";
                } else {
                    $feilds3[] = "'-' as " . $val;
                }

            }
        }
        $feilds = join(',', $feilds);
        $feilds2 = join(',', $feilds2);
        $feilds3 = join(',', $feilds3);
        $sql = "SELECT $feilds2,a.documentCode,a.documentMasterAutoID,a.GLDescription,a.masterCategory,a.GLAutoID FROM ((SELECT 
  $feilds,srp_erp_generalledger.documentCode,srp_erp_generalledger.documentMasterAutoID,coa.GLDescription,coa.masterCategory,srp_erp_generalledger.GLAutoID
 FROM srp_erp_generalledger 
 INNER JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription,masterCategory FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID AND groupID = " . current_companyID() . " WHERE srp_erp_groupchartofaccounts.GLAutoID IN(" . join(',', $glcode) . ")) coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID
 INNER JOIN ( SELECT srp_erp_groupsegmentdetails.segmentID,description,segmentCode FROM srp_erp_groupsegment INNER JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID AND groupID = " . current_companyID() . " WHERE srp_erp_groupsegment.segmentID IN(".join(',',$segment).")) seg ON srp_erp_generalledger.segmentID = seg.segmentID
 LEFT JOIN ( SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . ") cust  ON srp_erp_generalledger.partyAutoID = cust.customerMasterID AND srp_erp_generalledger.partyType = 'CUS'
 LEFT JOIN (SELECT groupSupplierAutoID,groupSupplierName,SupplierMasterID,groupSupplierSystemCode FROM srp_erp_groupsuppliermaster INNER JOIN srp_erp_groupsupplierdetails ON srp_erp_groupsuppliermaster.groupSupplierAutoID = srp_erp_groupsupplierdetails.groupSupplierMasterID WHERE srp_erp_groupsupplierdetails.companygroupID = " . current_companyID() . ") supp ON srp_erp_generalledger.partyAutoID = supp.SupplierMasterID AND srp_erp_generalledger.partyType = 'SUP'
 LEFT JOIN (SELECT document,documentID FROM srp_erp_documentcodemaster WHERE companyID IN (".join(',',$company).") GROUP BY documentID) dc ON (dc.documentID = srp_erp_generalledger.documentCode)
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
 WHERE srp_erp_generalledger.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "' AND srp_erp_generalledger.companyID IN (".join(',',$company).")
 ORDER BY srp_erp_generalledger.documentType,srp_erp_generalledger.documentDate ASC) UNION ALL
 (SELECT $feilds3,srp_erp_generalledger.documentCode,srp_erp_generalledger.documentMasterAutoID,coa.GLDescription,coa.masterCategory,coa.GLAutoID FROM srp_erp_generalledger 
 INNER JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription,masterCategory,GLAutoID FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID AND groupID = " . current_companyID() . " WHERE srp_erp_groupchartofaccounts.GLAutoID IN(" . join(',', $glcode) . ")) coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID
 INNER JOIN ( SELECT srp_erp_groupsegmentdetails.segmentID,description,segmentCode FROM srp_erp_groupsegment INNER JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID AND groupID = " . current_companyID() . " WHERE srp_erp_groupsegment.segmentID IN(".join(',',$segment).")) seg ON srp_erp_generalledger.segmentID = seg.segmentID
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID)
 WHERE coa.masterCategory = 'BS' AND srp_erp_generalledger.documentDate < '" . format_date($this->input->post("from")) . "' AND srp_erp_generalledger.companyID IN (".join(',',$company).") GROUP BY coa.GLAutoID )) as a ORDER BY documentDateSort asc";
        $result = $this->db->query($sql)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_procurement_purchase_order_list_report()
    {
        /* $cache = $this->cache->is_supported('xcache');
         if ($cache) {
             return "test";
         } else {*/
        $status = "";
        if ($this->input->post("status") == 3) {
            $status = join(',', array(0, 1, 2));
        } else {
            $status = $this->input->post("status");
        }

        $vendor = $this->input->post("vendorTo");
        $i = 1;
        $vendorOR = '(';
        if (!empty($vendor)) { /*generate the query according to selectd vendor*/
            foreach ($vendor as $vendor_val) {
                if ($i != 1) {
                    $vendorOR .= ' OR ';
                }
                $vendorOR .= "srp_erp_purchaseordermaster.supplierPrimaryCode = '" . $vendor_val . "' ";
                $i++;
            }
        }
        $vendorOR .= ')';

        $segment = $this->input->post("segment");
        $i = 1;
        $segmentOR = '(';
        if (!empty($vendor)) { /*generate the query according to selectd vendor*/
            foreach ($segment as $segment_val) {
                if ($i != 1) {
                    $segmentOR .= ' OR ';
                }
                $segmentOR .= "srp_erp_purchaseordermaster.segmentID = '" . $segment_val . "' ";
                $i++;
            }
        }
        $segmentOR .= ')';

        $feilds = "";
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            foreach ($fieldNameChk as $val) {
                if ($val == "companyReportingAmount") {
                    $feilds .= " srp_erp_purchaseordermaster.companyReportingCurrency,";
                    $feilds .= " srp_erp_purchaseordermaster.companyReportingCurrencyID,";
                    $feilds .= "srp_erp_purchaseordermaster." . $val . ",";
                    $feilds .= "(srp_erp_purchaseordermaster." . $val . " - IFNULL(GRV.receivedTotalAmount/GRV.companyReportingExchangeRate,0)) as " . $val . "Balance,";
                    $feilds .= "(GRV.receivedTotalAmount/GRV.companyReportingExchangeRate) as GRV" . $val . ",";
                    $feilds .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                }
                if ($val == "supplierCurrencyAmount") {
                    $feilds .= " srp_erp_purchaseordermaster.supplierCurrency,";
                    $feilds .= " srp_erp_purchaseordermaster.supplierCurrencyID,";
                    $feilds .= " srp_erp_purchaseordermaster." . $val . ",";
                    $feilds .= "(srp_erp_purchaseordermaster." . $val . " - IFNULL(GRV.receivedTotalAmount/GRV.supplierCurrencyExchangeRate,0)) as " . $val . "Balance,";
                    $feilds .= "(GRV.receivedTotalAmount/GRV.supplierCurrencyExchangeRate) as GRV" . $val . ",";
                    $feilds .= "CS.DecimalPlaces as " . $val . "DecimalPlaces,";
                }
            }
        }
        $result = $this->db->query("SELECT $feilds IFNULL(GRV.receivedTotalAmount,0) as receivedTotalAmount,srp_erp_suppliermaster.supplierSystemCode, srp_erp_suppliermaster.supplierName, srp_erp_purchaseordermaster.purchaseOrderType, srp_erp_purchaseordermaster.purchaseOrderCode,DATE_FORMAT(srp_erp_purchaseordermaster.documentDate,'" . $this->format . "') as documentDate, srp_erp_purchaseordermaster.narration, DATE_FORMAT(srp_erp_purchaseordermaster.expectedDeliveryDate,'" . $this->format . "') as expectedDeliveryDate,srp_erp_purchaseordermaster.purchaseOrderID,srp_erp_purchaseordermaster.documentID
FROM srp_erp_purchaseordermaster
INNER JOIN srp_erp_suppliermaster ON srp_erp_purchaseordermaster.supplierPrimaryCode = srp_erp_suppliermaster.supplierAutoID AND srp_erp_suppliermaster.companyID = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_purchaseordermaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CS ON (CS.currencyID = srp_erp_purchaseordermaster.supplierCurrencyID)
LEFT JOIN (
	SELECT
		SUM(receivedTotalAmount) as receivedTotalAmount,
		purchaseOrderMastertID,
		companyReportingExchangeRate,
		supplierCurrencyExchangeRate
	FROM
		srp_erp_grvdetails
	INNER JOIN srp_erp_grvmaster ON srp_erp_grvdetails.grvAutoID = srp_erp_grvmaster.grvAutoID
	WHERE
		srp_erp_grvmaster.approvedYN = 1
		GROUP BY purchaseOrderMastertID
) GRV ON (
	GRV.purchaseOrderMastertID = srp_erp_purchaseordermaster.purchaseOrderID
)
WHERE $vendorOR AND $segmentOR AND isReceived IN ($status) AND srp_erp_purchaseordermaster.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "' AND srp_erp_purchaseordermaster.companyID = " . $this->common_data['company_data']['company_id'] . "  AND srp_erp_purchaseordermaster.approvedYN = 1
ORDER BY srp_erp_purchaseordermaster.documentDate DESC")->result_array();
        //echo $this->db->last_query();
        //$this->cache->save('purchase_order_list_report', array(0=>'data', 1=>'other data'));
        return $result;
        // }
    }


    function get_procurement_purchase_order_list_group_report()
    {
        /* $cache = $this->cache->is_supported('xcache');
         if ($cache) {
             return "test";
         } else {*/
        $status = "";
        if ($this->input->post("status") == 3) {
            $status = join(',', array(0, 1, 2));
        } else {
            $status = $this->input->post("status");
        }

        $vendor = $this->input->post("vendorTo");
        $vendor = $this->get_group_suppliers($vendor);
        $i = 1;
        $vendorOR = '(';
        if (!empty($vendor)) { /*generate the query according to selectd vendor*/
            foreach ($vendor as $vendor_val) {
                if ($i != 1) {
                    $vendorOR .= ' OR ';
                }
                $vendorOR .= "srp_erp_purchaseordermaster.supplierPrimaryCode = '" . $vendor_val["supplierMasterID"] . "' ";
                $i++;
            }
        }
        $vendorOR .= ')';

        $feilds = "";
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            foreach ($fieldNameChk as $val) {
                if ($val == "companyReportingAmount") {
                    $feilds .= " srp_erp_purchaseordermaster.companyReportingCurrency,";
                    $feilds .= " srp_erp_purchaseordermaster.companyReportingCurrencyID,";
                    $feilds .= "srp_erp_purchaseordermaster." . $val . ",";
                    $feilds .= "(srp_erp_purchaseordermaster." . $val . " - IFNULL(GRV.receivedTotalAmount/GRV.companyReportingExchangeRate,0)) as " . $val . "Balance,";
                    $feilds .= "(GRV.receivedTotalAmount/GRV.companyReportingExchangeRate) as GRV" . $val . ",";
                    $feilds .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                }
                if ($val == "supplierCurrencyAmount") {
                    $feilds .= " srp_erp_purchaseordermaster.supplierCurrency,";
                    $feilds .= " srp_erp_purchaseordermaster.supplierCurrencyID,";
                    $feilds .= " srp_erp_purchaseordermaster." . $val . ",";
                    $feilds .= "(srp_erp_purchaseordermaster." . $val . " - IFNULL(GRV.receivedTotalAmount/GRV.supplierCurrencyExchangeRate,0)) as " . $val . "Balance,";
                    $feilds .= "(GRV.receivedTotalAmount/GRV.supplierCurrencyExchangeRate) as GRV" . $val . ",";
                    $feilds .= "CS.DecimalPlaces as " . $val . "DecimalPlaces,";
                }
            }
        }
        $result = $this->db->query("SELECT $feilds IFNULL(GRV.receivedTotalAmount,0) as receivedTotalAmount,sup.groupSupplierSystemCode, sup.groupSupplierName as supplierName, srp_erp_purchaseordermaster.purchaseOrderType, srp_erp_purchaseordermaster.purchaseOrderCode,DATE_FORMAT(srp_erp_purchaseordermaster.documentDate,'" . $this->format . "') as documentDate, srp_erp_purchaseordermaster.narration, DATE_FORMAT(srp_erp_purchaseordermaster.expectedDeliveryDate,'" . $this->format . "') as expectedDeliveryDate,srp_erp_purchaseordermaster.purchaseOrderID,srp_erp_purchaseordermaster.documentID
FROM srp_erp_purchaseordermaster 
INNER JOIN (SELECT srp_erp_groupsuppliermaster.*,SupplierMasterID FROM srp_erp_groupsuppliermaster INNER JOIN srp_erp_groupsupplierdetails ON groupSupplierMasterID = srp_erp_groupsuppliermaster.groupSupplierAutoID) sup ON srp_erp_purchaseordermaster.supplierPrimaryCode = sup.SupplierMasterID
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_purchaseordermaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CS ON (CS.currencyID = srp_erp_purchaseordermaster.supplierCurrencyID)
LEFT JOIN (
	SELECT
		SUM(receivedTotalAmount) as receivedTotalAmount,
		purchaseOrderMastertID,
		companyReportingExchangeRate,
		supplierCurrencyExchangeRate
	FROM
		srp_erp_grvdetails
	INNER JOIN srp_erp_grvmaster ON srp_erp_grvdetails.grvAutoID = srp_erp_grvmaster.grvAutoID
	WHERE
		srp_erp_grvmaster.approvedYN = 1
		GROUP BY purchaseOrderMastertID
) GRV ON (
	GRV.purchaseOrderMastertID = srp_erp_purchaseordermaster.purchaseOrderID
)
WHERE $vendorOR AND isReceived IN ($status) AND srp_erp_purchaseordermaster.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "' AND srp_erp_purchaseordermaster.approvedYN = 1
ORDER BY srp_erp_purchaseordermaster.documentDate DESC")->result_array();
        //echo $this->db->last_query();
        //$this->cache->save('purchase_order_list_report', array(0=>'data', 1=>'other data'));
        return $result;
        // }
    }


    function get_inventory_unbilled_grv_report()
    {
        $vendor = $this->input->post("vendorTo");
        $i = 1;
        $vendorOR = '(';
        if (!empty($vendor)) { /*generate the query according to selectd vendor*/
            foreach ($vendor as $vendor_val) {
                if ($i != 1) {
                    $vendorOR .= ' OR ';
                }
                $vendorOR .= "srp_erp_suppliermaster.supplierAutoID = '" . $vendor_val . "' ";
                $i++;
            }
        }
        $vendorOR .= ')';

        $feilds = "";
        $feilds2 = "";
        $feilds3 = "";
        $feilds4 = "";
        $groupBy = array();
        $groupBy2 = array();
        $groupBy3 = array();
        $having = array();
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            foreach ($fieldNameChk as $val) {
                if ($val == "companyReportingAmount") {
                    $feilds .= " srp_erp_grvmaster.companyReportingCurrency as currency" . $val . ",";
                    $feilds .= " CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                    $feilds .= " IFNULL(SUM(gd.receivedTotalAmount)/companyReportingExchangeRate,0) as " . $val . ",";
                    $feilds2 .= " srp_erp_grv_addon.companyReportingCurrency as currency" . $val . ",";
                    $feilds2 .= " CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                    $feilds2 .= " IFNULL(SUM(srp_erp_grv_addon.bookingCurrencyAmount/srp_erp_grv_addon.companyReportingExchangeRate),0) as " . $val . ",";
                    $groupBy3[] = "srp_erp_grv_addon.bookingCurrencyID";
                }
                if ($val == "transactionAmount") {
                    $feilds .= " srp_erp_grvmaster.transactionCurrency AS currency" . $val . ",";
                    $feilds .= " TC.DecimalPlaces as " . $val . "DecimalPlaces,";
                    $feilds .= " IFNULL(SUM(gd.receivedTotalAmount),0) as " . $val . ",";
                    $feilds2 .= " srp_erp_grv_addon.bookingCurrency as currency" . $val . ",";
                    $feilds2 .= " CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                    $feilds2 .= " IFNULL(SUM(srp_erp_grv_addon.bookingCurrencyAmount),0) as " . $val . ",";
                    $groupBy3[] = "srp_erp_grv_addon.companyReportingCurrencyID";
                }
                $feilds3 .= "a." . $val . "DecimalPlaces,a.currency" . $val . ",IFNULL(SUM(a." . $val . "),0) as " . $val . ",";
                $feilds4 .= "b." . $val . "DecimalPlaces,b.currency" . $val . ",IFNULL(SUM(psi." . $val . "),0) AS sumOf" . $val . ",IFNULL(SUM(b." . $val . "),0) as " . $val . ",(IFNULL(SUM(b." . $val . "),0)-IFNULL(SUM(psi." . $val . "),0)) AS balance" . $val . ",";
                $groupBy[] = "a.currency" . $val;
                $groupBy2[] = "b.currency" . $val;
                $having[] = "ROUND(balance" . $val . ",0) != 0";
            }
        }

        $result = $this->db->query("SELECT $feilds4 b.supplierID,b.grvPrimaryCode,b.documentID,DATE_FORMAT(b.grvDate,'" . $this->format . "') as grvDate,b.supplierName,b.supplierSystemCode,b.grvAutoID FROM 
 (SELECT $feilds3 a.supplierID,a.grvPrimaryCode,a.documentID,a.grvDate,a.supplierName,a.supplierSystemCode,a.grvAutoID FROM
  ((SELECT
$feilds
	srp_erp_suppliermaster.supplierSystemCode,
	srp_erp_suppliermaster.supplierName,
	srp_erp_grvmaster.grvDate,
	srp_erp_grvmaster.grvPrimaryCode,
	srp_erp_grvmaster.grvAutoID,
	srp_erp_grvmaster.documentID,
	srp_erp_grvmaster.supplierID
FROM
	srp_erp_grvmaster
	LEFT JOIN (SELECT
		grvAutoID,
		SUM(receivedTotalAmount) as receivedTotalAmount
	FROM
		srp_erp_grvdetails
		WHERE companyID = " . $this->common_data['company_data']['company_id'] . "
	GROUP BY
		grvAutoID
) gd ON (
	gd.grvAutoID = srp_erp_grvmaster.grvAutoID
)
LEFT JOIN srp_erp_suppliermaster ON srp_erp_grvmaster.supplierID = srp_erp_suppliermaster.supplierAutoID AND srp_erp_suppliermaster.companyID = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_grvmaster.companyReportingCurrencyID)
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_grvmaster.transactionCurrencyID)
WHERE
	$vendorOR AND srp_erp_grvmaster.grvDate <= '" . format_date($this->input->post("from")) . "' AND srp_erp_grvmaster.approvedYN = 1  AND srp_erp_grvmaster.companyID = " . $this->common_data['company_data']['company_id'] . "
GROUP BY
	srp_erp_grvmaster.grvAutoID,
	srp_erp_grvmaster.supplierID) 
	UNION ALL 
	(SELECT $feilds2 srp_erp_suppliermaster.supplierSystemCode,
	srp_erp_suppliermaster.supplierName,
	srp_erp_grvmaster.grvDate,
	srp_erp_grvmaster.grvPrimaryCode,
	srp_erp_grvmaster.grvAutoID,
	srp_erp_grvmaster.documentID,
	srp_erp_grv_addon.supplierID
	FROM srp_erp_grv_addon 
	INNER JOIN srp_erp_grvmaster ON srp_erp_grvmaster.grvAutoID = srp_erp_grv_addon.grvAutoID AND srp_erp_grvmaster.grvDate <= '" . format_date($this->input->post("from")) . "' AND srp_erp_grvmaster.companyID = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_grvmaster.approvedYN =1
	LEFT JOIN srp_erp_suppliermaster ON srp_erp_grv_addon.supplierID = srp_erp_suppliermaster.supplierAutoID AND srp_erp_suppliermaster.companyID = " . $this->common_data['company_data']['company_id'] . "
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_grv_addon.companyReportingCurrencyID)
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_grv_addon.transactionCurrencyID)
	WHERE $vendorOR AND srp_erp_grv_addon.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY
	        srp_erp_grvmaster.grvAutoID,
			srp_erp_grv_addon.supplierID,
			" . join(",", $groupBy3) . ")) a GROUP BY a.grvAutoID,a.supplierID," . join(",", $groupBy) . ") b
			LEFT JOIN 
			(SELECT srp_erp_paysupplierinvoicedetail.grvAutoID,srp_erp_paysupplierinvoicedetail.InvoiceAutoID,match_supplierinvoiceAutoID,SUM(srp_erp_paysupplierinvoicedetail.companyReportingAmount) as companyReportingAmount,SUM(srp_erp_paysupplierinvoicedetail.companyLocalAmount) as companyLocalAmount,SUM(srp_erp_paysupplierinvoicedetail.transactionAmount) as transactionAmount,supplierID
FROM srp_erp_paysupplierinvoicedetail 
INNER JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paysupplierinvoicedetail.InvoiceAutoID 
WHERE srp_erp_paysupplierinvoicemaster.companyID = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_paysupplierinvoicemaster.invoiceType = 'GRV Base' AND srp_erp_paysupplierinvoicemaster.approvedYN = 1 
GROUP BY srp_erp_paysupplierinvoicedetail.grvAutoID) psi ON (b.grvAutoID = psi.grvAutoID AND b.supplierID = psi.supplierID) GROUP BY b.supplierID,b.grvAutoID," . join(",", $groupBy2) . " HAVING " . join("AND", $having) . ";")->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    function get_inventory_unbilled_grv_group_report()
    {
        $vendor = $this->input->post("vendorTo");
        $company = $this->get_group_company();
        $i = 1;
        $vendorOR = '(';
        if (!empty($vendor)) { /*generate the query according to selectd vendor*/
            foreach ($vendor as $vendor_val) {
                if ($i != 1) {
                    $vendorOR .= ' OR ';
                }
                $vendorOR .= "srp_erp_groupsuppliermaster.groupSupplierAutoID = '" . $vendor_val . "' ";
                $i++;
            }
        }
        $vendorOR .= ')';

        $feilds = "";
        $feilds2 = "";
        $feilds3 = "";
        $feilds4 = "";
        $groupBy = array();
        $groupBy2 = array();
        $groupBy3 = array();
        $having = array();
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            foreach ($fieldNameChk as $val) {
                if ($val == "companyReportingAmount") {
                    $feilds .= " srp_erp_grvmaster.companyReportingCurrency as currency" . $val . ",";
                    $feilds .= " CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                    $feilds .= " IFNULL(SUM(gd.receivedTotalAmount)/companyReportingExchangeRate,0) as " . $val . ",";
                    $feilds2 .= " srp_erp_grv_addon.companyReportingCurrency as currency" . $val . ",";
                    $feilds2 .= " CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                    $feilds2 .= " IFNULL(SUM(srp_erp_grv_addon.bookingCurrencyAmount/srp_erp_grv_addon.companyReportingExchangeRate),0) as " . $val . ",";
                    $groupBy3[] = "srp_erp_grv_addon.bookingCurrencyID";
                }
                if ($val == "transactionAmount") {
                    $feilds .= " srp_erp_grvmaster.transactionCurrency AS currency" . $val . ",";
                    $feilds .= " TC.DecimalPlaces as " . $val . "DecimalPlaces,";
                    $feilds .= " IFNULL(SUM(gd.receivedTotalAmount),0) as " . $val . ",";
                    $feilds2 .= " srp_erp_grv_addon.bookingCurrency as currency" . $val . ",";
                    $feilds2 .= " CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                    $feilds2 .= " IFNULL(SUM(srp_erp_grv_addon.bookingCurrencyAmount),0) as " . $val . ",";
                    $groupBy3[] = "srp_erp_grv_addon.companyReportingCurrencyID";
                }
                $feilds3 .= "a." . $val . "DecimalPlaces,a.currency" . $val . ",IFNULL(SUM(a." . $val . "),0) as " . $val . ",";
                $feilds4 .= "b." . $val . "DecimalPlaces,b.currency" . $val . ",IFNULL(SUM(psi." . $val . "),0) AS sumOf" . $val . ",IFNULL(SUM(b." . $val . "),0) as " . $val . ",(IFNULL(SUM(b." . $val . "),0)-IFNULL(SUM(psi." . $val . "),0)) AS balance" . $val . ",";
                $groupBy[] = "a.currency" . $val;
                $groupBy2[] = "b.currency" . $val;
                $having[] = "ROUND(balance" . $val . ",0) != 0";
            }
        }

        $result = $this->db->query("SELECT $feilds4 b.supplierID,b.grvPrimaryCode,b.documentID,DATE_FORMAT(b.grvDate,'" . $this->format . "') as grvDate,b.supplierName,b.supplierSystemCode,b.grvAutoID FROM 
 (SELECT $feilds3 a.supplierID,a.grvPrimaryCode,a.documentID,a.grvDate,a.supplierName,a.supplierSystemCode,a.grvAutoID FROM
  ((SELECT
$feilds
	supp.groupSupplierSystemCode as supplierSystemCode,
	supp.groupSupplierName as supplierName,
	srp_erp_grvmaster.grvDate,
	srp_erp_grvmaster.grvPrimaryCode,
	srp_erp_grvmaster.grvAutoID,
	srp_erp_grvmaster.documentID,
	srp_erp_grvmaster.supplierID
FROM
	srp_erp_grvmaster
	LEFT JOIN (SELECT
		grvAutoID,
		SUM(receivedTotalAmount) as receivedTotalAmount
	FROM
		srp_erp_grvdetails
		WHERE companyID IN(" . join(",", $company) . ")
	GROUP BY
		grvAutoID
) gd ON (
	gd.grvAutoID = srp_erp_grvmaster.grvAutoID
)
INNER JOIN (SELECT srp_erp_groupsuppliermaster.*,srp_erp_groupsupplierdetails.SupplierMasterID as SupplierMasterID FROM srp_erp_groupsuppliermaster INNER JOIN srp_erp_groupsupplierdetails ON srp_erp_groupsuppliermaster.groupSupplierAutoID = srp_erp_groupsupplierdetails.groupSupplierMasterID WHERE $vendorOR AND srp_erp_groupsuppliermaster.companygroupID = " . current_companyID() . ") supp ON supp.SupplierMasterID = srp_erp_grvmaster.supplierID
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_grvmaster.companyReportingCurrencyID)
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_grvmaster.transactionCurrencyID)
WHERE
	srp_erp_grvmaster.grvDate <= '" . format_date($this->input->post("from")) . "' AND srp_erp_grvmaster.approvedYN = 1  AND srp_erp_grvmaster.companyID IN(" . join(",", $company) . ")
GROUP BY
	srp_erp_grvmaster.grvAutoID,
	srp_erp_grvmaster.supplierID) 
	UNION ALL 
	(SELECT $feilds2 
	supp.groupSupplierSystemCode as supplierSystemCode,
	supp.groupSupplierName as supplierName,
	srp_erp_grvmaster.grvDate,
	srp_erp_grvmaster.grvPrimaryCode,
	srp_erp_grvmaster.grvAutoID,
	srp_erp_grvmaster.documentID,
	srp_erp_grv_addon.supplierID
	FROM srp_erp_grv_addon 
	INNER JOIN srp_erp_grvmaster ON srp_erp_grvmaster.grvAutoID = srp_erp_grv_addon.grvAutoID AND srp_erp_grvmaster.grvDate <= '" . format_date($this->input->post("from")) . "' AND srp_erp_grvmaster.companyID IN(" . join(",", $company) . ") AND srp_erp_grvmaster.approvedYN =1
	INNER JOIN (SELECT srp_erp_groupsuppliermaster.*,srp_erp_groupsupplierdetails.SupplierMasterID as SupplierMasterID FROM srp_erp_groupsuppliermaster INNER JOIN srp_erp_groupsupplierdetails ON srp_erp_groupsuppliermaster.groupSupplierAutoID = srp_erp_groupsupplierdetails.groupSupplierMasterID WHERE $vendorOR AND srp_erp_groupsuppliermaster.companygroupID = " . current_companyID() . ") supp ON supp.SupplierMasterID = srp_erp_grv_addon.supplierID
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_grv_addon.companyReportingCurrencyID)
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_grv_addon.transactionCurrencyID)
	WHERE srp_erp_grv_addon.companyID IN(" . join(",", $company) . ") GROUP BY
	        srp_erp_grvmaster.grvAutoID,
			srp_erp_grv_addon.supplierID,
			" . join(",", $groupBy3) . ")) a GROUP BY a.grvAutoID,a.supplierID," . join(",", $groupBy) . ") b
			LEFT JOIN 
			(SELECT srp_erp_paysupplierinvoicedetail.grvAutoID,srp_erp_paysupplierinvoicedetail.InvoiceAutoID,match_supplierinvoiceAutoID,SUM(srp_erp_paysupplierinvoicedetail.companyReportingAmount) as companyReportingAmount,SUM(srp_erp_paysupplierinvoicedetail.companyLocalAmount) as companyLocalAmount,SUM(srp_erp_paysupplierinvoicedetail.transactionAmount) as transactionAmount,supplierID
FROM srp_erp_paysupplierinvoicedetail 
INNER JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paysupplierinvoicedetail.InvoiceAutoID 
WHERE srp_erp_paysupplierinvoicemaster.companyID IN(" . join(",", $company) . ") AND srp_erp_paysupplierinvoicemaster.invoiceType = 'GRV Base' AND srp_erp_paysupplierinvoicemaster.approvedYN = 1 
GROUP BY srp_erp_paysupplierinvoicedetail.grvAutoID) psi ON (b.grvAutoID = psi.grvAutoID AND b.supplierID = psi.supplierID) GROUP BY b.supplierID,b.grvAutoID," . join(",", $groupBy2) . " HAVING " . join("AND", $having) . ";")->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_accounts_payable_vendor_ledger_report()
    {
        $vendor = $this->input->post("vendorTo");
        $i = 1;
        $vendorOR = '(';
        if (!empty($vendor)) { /*generate the query according to selectd vendor*/
            foreach ($vendor as $vendor_val) {
                if ($i != 1) {
                    $vendorOR .= ' OR ';
                }
                $vendorOR .= "srp_erp_generalledger.partyAutoID = '" . $vendor_val . "' ";
                $i++;
            }
        }
        $vendorOR .= ')';

        $feilds = "";
        $feilds2 = "";
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            foreach ($fieldNameChk as $val) {
                if ($val == "transactionAmount") {
                    $feilds .= " srp_erp_generalledger.transactionCurrency AS transactionCurrency,";
                    $feilds2 .= " a.transactionCurrency AS transactionCurrency,";
                    $feilds .= " TC.DecimalPlaces AS " . $val . "DecimalPlaces,";
                }
                if ($val == "companyLocalAmount") {
                    $feilds .= " CL.DecimalPlaces AS " . $val . "DecimalPlaces,";
                }
                if ($val == "companyReportingAmount") {
                    $feilds .= " CR.DecimalPlaces AS " . $val . "DecimalPlaces,";
                }
                $feilds .= " IF(documentCode = 'BSI',ABS(SUM(srp_erp_generalledger." . $val . ")),IF(documentCode = 'PV' OR documentCode = 'DN' OR documentCode= 'PRVR',SUM(srp_erp_generalledger." . $val . ")*-1,SUM(srp_erp_generalledger." . $val . "))) AS " . $val . ",";
                $feilds2 .= " a." . $val . "DecimalPlaces,";
                $feilds2 .= " a." . $val . " AS " . $val . ",";
            }
        }

        $result = $this->db->query("SELECT $feilds2
    a.documentMasterAutoID,DATE_FORMAT(a.documentDate,'" . $this->format . "') as documentDate,a.documentCode,a.documentSystemCode,a.documentNarration,a.supplierName,a.supplierSystemCode,a.GLSecondaryCode,a.GLDescription FROM 
    ((SELECT $feilds srp_erp_generalledger.documentMasterAutoID,srp_erp_generalledger.documentDate,srp_erp_generalledger.documentCode,srp_erp_generalledger.documentSystemCode,srp_erp_generalledger.documentNarration,srp_erp_suppliermaster.supplierName,srp_erp_suppliermaster.supplierSystemCode,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.GLDescription 
    FROM srp_erp_generalledger 
    INNER JOIN srp_erp_suppliermaster ON srp_erp_generalledger.partyAutoID = srp_erp_suppliermaster.supplierAutoID AND srp_erp_generalledger.subLedgerType = 2 AND srp_erp_suppliermaster.companyID = " . $this->common_data['company_data']['company_id'] . " 
    INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "  
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_generalledger.transactionCurrencyID) 
    WHERE $vendorOR AND srp_erp_generalledger.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " 
    GROUP BY srp_erp_generalledger.documentSystemCode,srp_erp_generalledger.partyAutoID) 
    UNION ALL 
    (SELECT $feilds srp_erp_generalledger.documentMasterAutoID,'1970-01-01' as documentDate,'' as documentCode,'' as documentSystemCode,'Opening Balance' as documentNarration,srp_erp_suppliermaster.supplierName,srp_erp_suppliermaster.supplierSystemCode,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.GLDescription  FROM srp_erp_generalledger 
    INNER JOIN srp_erp_suppliermaster ON srp_erp_generalledger.partyAutoID = srp_erp_suppliermaster.supplierAutoID AND srp_erp_generalledger.subLedgerType = 2 AND srp_erp_suppliermaster.companyID = " . $this->common_data['company_data']['company_id'] . " 
    INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "  
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_generalledger.transactionCurrencyID) 
    WHERE $vendorOR AND srp_erp_generalledger.documentDate < '" . format_date($this->input->post("from")) . "'  AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " 
    GROUP BY srp_erp_generalledger.GLAutoID,srp_erp_suppliermaster.supplierAutoID)) AS a
      ORDER BY  a.documentDate")->result_array();
        return $result;
    }


    function get_accounts_payable_vendor_ledger_group_report()
    {
        $company = $this->get_group_company();
        $vendor = $this->input->post("vendorTo");
        $i = 1;
        $vendorOR = '(';
        if (!empty($vendor)) { /*generate the query according to selectd vendor*/
            foreach ($vendor as $vendor_val) {
                if ($i != 1) {
                    $vendorOR .= ' OR ';
                }
                $vendorOR .= "supp.groupSupplierAutoID = '" . $vendor_val . "' ";
                $i++;
            }
        }
        $vendorOR .= ')';

        $feilds = "";
        $feilds2 = "";
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            foreach ($fieldNameChk as $val) {
                if ($val == "transactionAmount") {
                    $feilds .= " srp_erp_generalledger.transactionCurrency AS transactionCurrency,";
                    $feilds2 .= " a.transactionCurrency AS transactionCurrency,";
                    $feilds .= " TC.DecimalPlaces AS " . $val . "DecimalPlaces,";
                }
                if ($val == "companyLocalAmount") {
                    $feilds .= " CL.DecimalPlaces AS " . $val . "DecimalPlaces,";
                }
                if ($val == "companyReportingAmount") {
                    $feilds .= " CR.DecimalPlaces AS " . $val . "DecimalPlaces,";
                }
                $feilds .= " IF(documentCode = 'BSI',ABS(SUM(srp_erp_generalledger." . $val . ")),IF(documentCode = 'PV' OR documentCode = 'DN' OR documentCode= 'PRVR',SUM(srp_erp_generalledger." . $val . ")*-1,SUM(srp_erp_generalledger." . $val . "))) AS " . $val . ",";
                $feilds2 .= " a." . $val . "DecimalPlaces,";
                $feilds2 .= " a." . $val . " AS " . $val . ",";
            }
        }

        $result = $this->db->query("SELECT $feilds2
    a.documentMasterAutoID,DATE_FORMAT(a.documentDate,'" . $this->format . "') as documentDate,a.documentCode,a.documentSystemCode,a.documentNarration,a.supplierName,a.supplierSystemCode,a.GLSecondaryCode,a.GLDescription FROM 
    ((SELECT $feilds srp_erp_generalledger.documentMasterAutoID,srp_erp_generalledger.documentDate,srp_erp_generalledger.documentCode,srp_erp_generalledger.documentSystemCode,srp_erp_generalledger.documentNarration,supp.groupSupplierName as supplierName,supp.groupSupplierSystemCode as supplierSystemCode,coa.GLSecondaryCode,coa.GLDescription 
    FROM srp_erp_generalledger 
    INNER JOIN (SELECT groupSupplierAutoID,groupSupplierName,SupplierMasterID,groupSupplierSystemCode FROM srp_erp_groupsuppliermaster INNER JOIN srp_erp_groupsupplierdetails ON srp_erp_groupsuppliermaster.groupSupplierAutoID = srp_erp_groupsupplierdetails.groupSupplierMasterID WHERE srp_erp_groupsupplierdetails.companygroupID = " . current_companyID() . ") supp ON srp_erp_generalledger.partyAutoID = supp.SupplierMasterID
    INNER JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID AND groupID = " . current_companyID() . ") coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID 
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_generalledger.transactionCurrencyID) 
    WHERE $vendorOR AND srp_erp_generalledger.subLedgerType = 2 AND srp_erp_generalledger.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "' AND srp_erp_generalledger.companyID IN(" . join(',', $company) . ")
    GROUP BY srp_erp_generalledger.documentSystemCode,srp_erp_generalledger.partyAutoID) 
    UNION ALL 
    (SELECT $feilds srp_erp_generalledger.documentMasterAutoID,'1970-01-01' as documentDate,'' as documentCode,'' as documentSystemCode,'Opening Balance' as documentNarration,supp.groupSupplierName as supplierName,supp.groupSupplierSystemCode as supplierSystemCode,coa.GLSecondaryCode,coa.GLDescription  FROM srp_erp_generalledger 
    INNER JOIN (SELECT groupSupplierAutoID,groupSupplierName,SupplierMasterID,groupSupplierSystemCode FROM srp_erp_groupsuppliermaster INNER JOIN srp_erp_groupsupplierdetails ON srp_erp_groupsuppliermaster.groupSupplierAutoID = srp_erp_groupsupplierdetails.groupSupplierMasterID WHERE srp_erp_groupsupplierdetails.companygroupID = " . current_companyID() . ") supp ON srp_erp_generalledger.partyAutoID = supp.SupplierMasterID
     INNER JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID AND groupID = " . current_companyID() . ") coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID  
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_generalledger.transactionCurrencyID) 
    WHERE $vendorOR AND srp_erp_generalledger.subLedgerType = 2 AND srp_erp_generalledger.documentDate < '" . format_date($this->input->post("from")) . "'  AND srp_erp_generalledger.companyID IN(" . join(',', $company) . ") 
    GROUP BY srp_erp_generalledger.GLAutoID,supp.SupplierMasterID)) AS a
      ORDER BY  a.documentDate")->result_array();
        return $result;
    }


    function get_accounts_payable_vendor_statement_report($overdue = false)
    {
        $where = "";
        if (isset($_POST["currency"]) && $_POST["currency"] != "") {
            $where = " WHERE " . $this->input->post("fieldNameChk")[0] . "currency = '" . $_POST["currency"] . "'";
        }
        $columnCheck = "bookingDate";
        if ($overdue) {
            $columnCheck = "invoiceDueDate";
        }
        $vendor = $this->input->post("vendorTo");
        $i = 1;
        $vendorOR = '(';
        if (!empty($vendor)) { /*generate the query according to selectd vendor*/
            foreach ($vendor as $vendor_val) {
                if ($i != 1) {
                    $vendorOR .= ' OR ';
                }
                $vendorOR .= "srp_erp_suppliermaster.supplierAutoID = '" . $vendor_val . "' ";
                $i++;
            }
        }
        $vendorOR .= ')';

        $fields = "";
        $fields2 = "";
        $fields3 = "";
        $fields4 = "";
        $fields5 = "";
        $fields6 = "";
        $fields7 = "";
        $having = array();
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            foreach ($fieldNameChk as $val) {
                if ($val == 'transactionAmount') {
                    $fields .= 'srp_erp_paysupplierinvoicemaster.transactionCurrency as ' . $val . 'currency,';
                    $fields .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields2 .= 'srp_erp_paymentvoucherdetail.transactionCurrency as ' . $val . 'currency,';
                    $fields2 .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields7 .= 'srp_erp_debitnotemaster.transactionCurrency as ' . $val . 'currency,';
                    $fields7 .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                } else if ($val == 'companyReportingAmount') {
                    $fields .= 'srp_erp_paysupplierinvoicemaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields2 .= 'srp_erp_paymentvoucherdetail.companyReportingCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields7 .= 'srp_erp_debitnotemaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields7 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                } else if ($val == 'companyLocalAmount') {
                    $fields .= 'srp_erp_paysupplierinvoicemaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields2 .= 'srp_erp_paymentvoucherdetail.companyLocalCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields7 .= 'srp_erp_debitnotemaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields7 .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                }
                $fields .= '(SUM(srp_erp_paysupplierinvoicemaster.' . $val . ') - (IFNULL(SUM(pvd.' . $val . '),0)+IFNULL(SUM(dnd.' . $val . '),0)+IFNULL(SUM(pva.' . $val . '),0))) * -1  as ' . $val . ',';
                $fields2 .= '(SUM(srp_erp_paymentvoucherdetail.' . $val . ') - IFNULL(SUM(avd.' . $val . '),0)) as ' . $val . ',';
                $fields3 .= 'a.' . $val . 'currency as ' . $val . 'currency,';
                $fields3 .= 'a.' . $val . 'DecimalPlaces,';
                $fields3 .= 'a.' . $val . ' as ' . $val . ',';
                $fields4 .= 'SUM(srp_erp_paymentvoucherdetail.' . $val . ') as ' . $val . ',';
                $fields5 .= 'SUM(srp_erp_debitnotedetail.' . $val . ') as ' . $val . ',';
                $fields6 .= 'SUM(srp_erp_pvadvancematchdetails.' . $val . ') as ' . $val . ',';
                $fields7 .= '(SUM(srp_erp_debitnotedetail.' . $val . ') - IFNULL(SUM(pvd.' . $val . '),0)) as ' . $val . ',';
                $having[] = $val . '!= -0 AND ' . $val . ' != 0';
            }
        }

        $result = $this->db->query("SELECT $fields3  a.InvoiceAutoID,a.supplierName,a.supplierSystemCode,a.comments,a.documentID,DATE_FORMAT(a.bookingDate,'" . $this->format . "') as bookingDate,a.GLSecondaryCode,a.GLDescription,a.bookingInvCode,DATE_FORMAT(a.invoiceDueDate,'" . $this->format . "') as invoiceDueDate FROM 
((SELECT $fields srp_erp_paysupplierinvoicemaster.InvoiceAutoID,srp_erp_suppliermaster.supplierName,srp_erp_suppliermaster.supplierSystemCode,srp_erp_paysupplierinvoicemaster.comments,srp_erp_paysupplierinvoicemaster.documentID,srp_erp_paysupplierinvoicemaster.bookingDate,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,srp_erp_paysupplierinvoicemaster.bookingInvCode,srp_erp_paysupplierinvoicemaster.invoiceDueDate FROM `srp_erp_paysupplierinvoicemaster` 
LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_paysupplierinvoicemaster`.`supplierID` = `srp_erp_suppliermaster`.`supplierAutoID` AND `srp_erp_suppliermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN 
(
	SELECT
		$fields4 srp_erp_paymentvoucherdetail.InvoiceAutoID,srp_erp_paymentvoucherdetail.payVoucherAutoID
	FROM
		srp_erp_paymentvoucherdetail
		INNER JOIN `srp_erp_paymentvouchermaster` ON `srp_erp_paymentvouchermaster`.`payVoucherAutoID` = `srp_erp_paymentvoucherdetail`.`payVoucherAutoID` AND `srp_erp_paymentvouchermaster`.`approvedYN` = 1
	WHERE
		`srp_erp_paymentvoucherdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_paymentvouchermaster.PVDate <= '" . format_date($this->input->post("from")) . "'  GROUP BY srp_erp_paymentvoucherdetail.InvoiceAutoID
) pvd ON (
	pvd.`InvoiceAutoID` = `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN
(
	SELECT
		$fields5 srp_erp_debitnotedetail.InvoiceAutoID,srp_erp_debitnotedetail.debitNoteMasterAutoID
	FROM
		srp_erp_debitnotedetail 
		INNER JOIN `srp_erp_debitnotemaster` ON `srp_erp_debitnotemaster`.`debitNoteMasterAutoID` = `srp_erp_debitnotedetail`.`debitNoteMasterAutoID` AND `srp_erp_debitnotemaster`.`approvedYN` = 1
	WHERE
		`srp_erp_debitnotedetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_debitnotemaster.debitNoteDate <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_debitnotedetail.InvoiceAutoID
) dnd ON (
	dnd.`InvoiceAutoID` = `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN 
(
	SELECT
		$fields6 srp_erp_pvadvancematchdetails.InvoiceAutoID
	FROM
	srp_erp_pvadvancematchdetails
		INNER JOIN `srp_erp_pvadvancematch` ON `srp_erp_pvadvancematch`.`matchID` = `srp_erp_pvadvancematchdetails`.`matchID` AND `srp_erp_pvadvancematch`.`confirmedYN` = 1
		WHERE `srp_erp_pvadvancematch`.`matchDate` <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_pvadvancematchdetails.InvoiceAutoID
) pva ON (
	pva.`InvoiceAutoID` = `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_paysupplierinvoicemaster.supplierliabilityAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_paysupplierinvoicemaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_paysupplierinvoicemaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_paysupplierinvoicemaster.transactionCurrencyID) 
WHERE $vendorOR AND `srp_erp_paysupplierinvoicemaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_paysupplierinvoicemaster.$columnCheck <= '" . format_date($this->input->post("from")) . "'  AND `srp_erp_paysupplierinvoicemaster`.`approvedYN` = 1 
GROUP BY `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID` HAVING (" . join(' AND ', $having) . ")) 
UNION ALL
(SELECT $fields2 srp_erp_paymentvouchermaster.payVoucherAutoID as InvoiceAutoID,srp_erp_suppliermaster.supplierName,srp_erp_suppliermaster.supplierSystemCode,'Advance' as comments,srp_erp_paymentvouchermaster.documentID,srp_erp_paymentvouchermaster.PVDate as bookingDate,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,srp_erp_paymentvouchermaster.PVcode as bookingInvCode,'-' as invoiceDueDate  FROM srp_erp_paymentvouchermaster 
INNER JOIN `srp_erp_paymentvoucherdetail` ON `srp_erp_paymentvoucherdetail`.`payVoucherAutoID` = `srp_erp_paymentvouchermaster`.`payVoucherAutoID` AND `srp_erp_paymentvoucherdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND  srp_erp_paymentvoucherdetail.type='Advance'
LEFT JOIN (SELECT $fields6 srp_erp_pvadvancematchdetails.payVoucherAutoId 
		FROM srp_erp_pvadvancematchdetails 
		INNER JOIN `srp_erp_pvadvancematch` ON `srp_erp_pvadvancematch`.`matchID` = `srp_erp_pvadvancematchdetails`.`matchID` AND `srp_erp_pvadvancematch`.`confirmedYN` = 1
		WHERE `srp_erp_pvadvancematch`.`matchDate` <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_pvadvancematchdetails.payVoucherAutoID
		) avd ON (avd.payVoucherAutoID = `srp_erp_paymentvoucherdetail`.`payVoucherAutoID`)
LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_paymentvouchermaster`.`partyID` = `srp_erp_suppliermaster`.`supplierAutoID` AND `srp_erp_suppliermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_paymentvouchermaster.partyGLAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_paymentvouchermaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_paymentvouchermaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_paymentvouchermaster.transactionCurrencyID) 
WHERE $vendorOR AND `srp_erp_paymentvouchermaster`.`approvedYN` = 1 AND srp_erp_paymentvouchermaster.PVDate <= '" . format_date($this->input->post("from")) . "' AND srp_erp_paymentvoucherdetail.InvoiceAutoID IS NULL GROUP BY `srp_erp_paymentvouchermaster`.`payVoucherAutoID`)
UNION ALL
(SELECT $fields7 srp_erp_debitnotemaster.debitNoteMasterAutoID as InvoiceAutoID,srp_erp_suppliermaster.supplierName,srp_erp_suppliermaster.supplierSystemCode,srp_erp_debitnotemaster.comments,srp_erp_debitnotemaster.documentID,srp_erp_debitnotemaster.debitNoteDate as bookingDate,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,srp_erp_debitnotemaster.debitNoteCode as bookingInvCode,'-' as invoiceDueDate  FROM srp_erp_debitnotemaster 
INNER JOIN `srp_erp_debitnotedetail` ON `srp_erp_debitnotedetail`.`debitNoteMasterAutoID` = `srp_erp_debitnotemaster`.`debitNoteMasterAutoID` AND `srp_erp_debitnotedetail`.`InvoiceAutoID` IS NULL AND `srp_erp_debitnotedetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT $fields4 debitNoteAutoID FROM `srp_erp_paymentvoucherdetail` WHERE `srp_erp_paymentvoucherdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND type='debitnote' GROUP BY debitNoteAutoID) pvd ON pvd.`debitNoteAutoID` = `srp_erp_debitnotemaster`.`debitNoteMasterAutoID`
LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_debitnotemaster`.`supplierID` = `srp_erp_suppliermaster`.`supplierAutoID` AND `srp_erp_suppliermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_debitnotemaster.supplierliabilityAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_debitnotemaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_debitnotemaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_debitnotemaster.transactionCurrencyID) 
WHERE $vendorOR AND `srp_erp_debitnotemaster`.`approvedYN` = 1 AND srp_erp_debitnotemaster.debitNoteDate <= '" . format_date($this->input->post("from")) . "' AND `srp_erp_debitnotemaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " GROUP BY `srp_erp_debitnotemaster`.`debitNoteMasterAutoID`)) as a $where")->result_array();
        return $result;
    }

    function get_accounts_payable_vendor_statement_group_report($overdue = false)
    {
        $company = $this->get_group_company();
        $where = "";
        if (isset($_POST["currency"]) && $_POST["currency"] != "") {
            $where = " WHERE " . $this->input->post("fieldNameChk")[0] . "currency = '" . $_POST["currency"] . "'";
        }
        $columnCheck = "bookingDate";
        if ($overdue) {
            $columnCheck = "invoiceDueDate";
        }
        $vendor = $this->input->post("vendorTo");
        $i = 1;
        $vendorOR = '(';
        if (!empty($vendor)) { /*generate the query according to selectd vendor*/
            foreach ($vendor as $vendor_val) {
                if ($i != 1) {
                    $vendorOR .= ' OR ';
                }
                $vendorOR .= "supp.groupSupplierAutoID = '" . $vendor_val . "' ";
                $i++;
            }
        }
        $vendorOR .= ')';

        $fields = "";
        $fields2 = "";
        $fields3 = "";
        $fields4 = "";
        $fields5 = "";
        $fields6 = "";
        $fields7 = "";
        $having = array();
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            foreach ($fieldNameChk as $val) {
                if ($val == 'transactionAmount') {
                    $fields .= 'srp_erp_paysupplierinvoicemaster.transactionCurrency as ' . $val . 'currency,';
                    $fields .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields2 .= 'srp_erp_paymentvoucherdetail.transactionCurrency as ' . $val . 'currency,';
                    $fields2 .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields7 .= 'srp_erp_debitnotemaster.transactionCurrency as ' . $val . 'currency,';
                    $fields7 .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                } else if ($val == 'companyReportingAmount') {
                    $fields .= 'srp_erp_paysupplierinvoicemaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields2 .= 'srp_erp_paymentvoucherdetail.companyReportingCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields7 .= 'srp_erp_debitnotemaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields7 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                } else if ($val == 'companyLocalAmount') {
                    $fields .= 'srp_erp_paysupplierinvoicemaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields2 .= 'srp_erp_paymentvoucherdetail.companyLocalCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields7 .= 'srp_erp_debitnotemaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields7 .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                }
                $fields .= '(SUM(srp_erp_paysupplierinvoicemaster.' . $val . ') - (IFNULL(SUM(pvd.' . $val . '),0)+IFNULL(SUM(dnd.' . $val . '),0)+IFNULL(SUM(pva.' . $val . '),0))) * -1  as ' . $val . ',';
                $fields2 .= '(SUM(srp_erp_paymentvoucherdetail.' . $val . ') - IFNULL(SUM(avd.' . $val . '),0)) as ' . $val . ',';
                $fields3 .= 'a.' . $val . 'currency as ' . $val . 'currency,';
                $fields3 .= 'a.' . $val . 'DecimalPlaces,';
                $fields3 .= 'a.' . $val . ' as ' . $val . ',';
                $fields4 .= 'SUM(srp_erp_paymentvoucherdetail.' . $val . ') as ' . $val . ',';
                $fields5 .= 'SUM(srp_erp_debitnotedetail.' . $val . ') as ' . $val . ',';
                $fields6 .= 'SUM(srp_erp_pvadvancematchdetails.' . $val . ') as ' . $val . ',';
                $fields7 .= '(SUM(srp_erp_debitnotedetail.' . $val . ') - IFNULL(SUM(pvd.' . $val . '),0)) as ' . $val . ',';
                $having[] = $val . '!= -0 AND ' . $val . ' != 0';
            }
        }

        $result = $this->db->query("SELECT $fields3  a.InvoiceAutoID,a.supplierName,a.supplierSystemCode,a.comments,a.documentID,DATE_FORMAT(a.bookingDate,'" . $this->format . "') as bookingDate,a.GLSecondaryCode,a.GLDescription,a.bookingInvCode,DATE_FORMAT(a.invoiceDueDate,'" . $this->format . "') as invoiceDueDate FROM 
((SELECT $fields srp_erp_paysupplierinvoicemaster.InvoiceAutoID,supp.groupSupplierName as supplierName,supp.groupSupplierSystemCode as supplierSystemCode,srp_erp_paysupplierinvoicemaster.comments,srp_erp_paysupplierinvoicemaster.documentID,srp_erp_paysupplierinvoicemaster.bookingDate,coa.GLSecondaryCode,coa.GLDescription,srp_erp_paysupplierinvoicemaster.bookingInvCode,srp_erp_paysupplierinvoicemaster.invoiceDueDate FROM `srp_erp_paysupplierinvoicemaster` 
LEFT JOIN (SELECT groupSupplierAutoID,groupSupplierName,SupplierMasterID,groupSupplierSystemCode FROM srp_erp_groupsuppliermaster INNER JOIN srp_erp_groupsupplierdetails ON srp_erp_groupsuppliermaster.groupSupplierAutoID = srp_erp_groupsupplierdetails.groupSupplierMasterID WHERE srp_erp_groupsupplierdetails.companygroupID = " . current_companyID() . ") supp ON `srp_erp_paysupplierinvoicemaster`.`supplierID` = `supp`.`SupplierMasterID`
LEFT JOIN 
(
	SELECT
		$fields4 srp_erp_paymentvoucherdetail.InvoiceAutoID,srp_erp_paymentvoucherdetail.payVoucherAutoID
	FROM
		srp_erp_paymentvoucherdetail
		INNER JOIN `srp_erp_paymentvouchermaster` ON `srp_erp_paymentvouchermaster`.`payVoucherAutoID` = `srp_erp_paymentvoucherdetail`.`payVoucherAutoID` AND `srp_erp_paymentvouchermaster`.`approvedYN` = 1
	WHERE
		`srp_erp_paymentvoucherdetail`.`companyID` IN ( " . join(",", $company) . ") AND srp_erp_paymentvouchermaster.PVDate <= '" . format_date($this->input->post("from")) . "'  GROUP BY srp_erp_paymentvoucherdetail.InvoiceAutoID
) pvd ON (
	pvd.`InvoiceAutoID` = `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN
(
	SELECT
		$fields5 srp_erp_debitnotedetail.InvoiceAutoID,srp_erp_debitnotedetail.debitNoteMasterAutoID
	FROM
		srp_erp_debitnotedetail 
		INNER JOIN `srp_erp_debitnotemaster` ON `srp_erp_debitnotemaster`.`debitNoteMasterAutoID` = `srp_erp_debitnotedetail`.`debitNoteMasterAutoID` AND `srp_erp_debitnotemaster`.`approvedYN` = 1
	WHERE
		`srp_erp_debitnotedetail`.`companyID` IN ( " . join(",", $company) . ") AND srp_erp_debitnotemaster.debitNoteDate <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_debitnotedetail.InvoiceAutoID
) dnd ON (
	dnd.`InvoiceAutoID` = `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN 
(
	SELECT
		$fields6 srp_erp_pvadvancematchdetails.InvoiceAutoID
	FROM
	srp_erp_pvadvancematchdetails
		INNER JOIN `srp_erp_pvadvancematch` ON `srp_erp_pvadvancematch`.`matchID` = `srp_erp_pvadvancematchdetails`.`matchID` AND `srp_erp_pvadvancematch`.`confirmedYN` = 1
		WHERE `srp_erp_pvadvancematch`.`matchDate` <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_pvadvancematchdetails.InvoiceAutoID
) pva ON (
	pva.`InvoiceAutoID` = `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID AND groupID = " . current_companyID() . ") coa ON srp_erp_paysupplierinvoicemaster.supplierliabilityAutoID = coa.chartofAccountID
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_paysupplierinvoicemaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_paysupplierinvoicemaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_paysupplierinvoicemaster.transactionCurrencyID) 
WHERE $vendorOR AND `srp_erp_paysupplierinvoicemaster`.`companyID` IN ( " . join(",", $company) . ") AND srp_erp_paysupplierinvoicemaster.$columnCheck <= '" . format_date($this->input->post("from")) . "'  AND `srp_erp_paysupplierinvoicemaster`.`approvedYN` = 1 
GROUP BY `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID` HAVING (" . join(' AND ', $having) . ")) 
UNION ALL
(SELECT $fields2 srp_erp_paymentvouchermaster.payVoucherAutoID as InvoiceAutoID,supp.groupSupplierName as supplierName,supp.groupSupplierSystemCode as supplierSystemCode,'Advance' as comments,srp_erp_paymentvouchermaster.documentID,srp_erp_paymentvouchermaster.PVDate as bookingDate,coa.GLSecondaryCode,coa.GLDescription,srp_erp_paymentvouchermaster.PVcode as bookingInvCode,'-' as invoiceDueDate  FROM srp_erp_paymentvouchermaster 
INNER JOIN `srp_erp_paymentvoucherdetail` ON `srp_erp_paymentvoucherdetail`.`payVoucherAutoID` = `srp_erp_paymentvouchermaster`.`payVoucherAutoID` AND `srp_erp_paymentvoucherdetail`.`companyID` IN ( " . join(",", $company) . ") AND  srp_erp_paymentvoucherdetail.type='Advance'
LEFT JOIN (SELECT $fields6 srp_erp_pvadvancematchdetails.payVoucherAutoId 
		FROM srp_erp_pvadvancematchdetails 
		INNER JOIN `srp_erp_pvadvancematch` ON `srp_erp_pvadvancematch`.`matchID` = `srp_erp_pvadvancematchdetails`.`matchID` AND `srp_erp_pvadvancematch`.`confirmedYN` = 1
		WHERE `srp_erp_pvadvancematch`.`matchDate` <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_pvadvancematchdetails.payVoucherAutoID
		) avd ON (avd.payVoucherAutoID = `srp_erp_paymentvoucherdetail`.`payVoucherAutoID`)
LEFT JOIN (SELECT groupSupplierAutoID,groupSupplierName,SupplierMasterID,groupSupplierSystemCode FROM srp_erp_groupsuppliermaster INNER JOIN srp_erp_groupsupplierdetails ON srp_erp_groupsuppliermaster.groupSupplierAutoID = srp_erp_groupsupplierdetails.groupSupplierMasterID WHERE srp_erp_groupsupplierdetails.companygroupID = " . current_companyID() . ") supp ON `srp_erp_paymentvouchermaster`.`partyID` = `supp`.`SupplierMasterID`
LEFT JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID AND groupID = " . current_companyID() . ") coa ON srp_erp_paymentvouchermaster.partyGLAutoID = coa.chartofAccountID
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_paymentvouchermaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_paymentvouchermaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_paymentvouchermaster.transactionCurrencyID) 
WHERE $vendorOR AND `srp_erp_paymentvouchermaster`.`approvedYN` = 1 AND srp_erp_paymentvouchermaster.PVDate <= '" . format_date($this->input->post("from")) . "' AND srp_erp_paymentvoucherdetail.InvoiceAutoID IS NULL GROUP BY `srp_erp_paymentvouchermaster`.`payVoucherAutoID`)
UNION ALL
(SELECT $fields7 srp_erp_debitnotemaster.debitNoteMasterAutoID as InvoiceAutoID,supp.groupSupplierName as supplierName,supp.groupSupplierSystemCode as supplierSystemCode,srp_erp_debitnotemaster.comments,srp_erp_debitnotemaster.documentID,srp_erp_debitnotemaster.debitNoteDate as bookingDate,coa.GLSecondaryCode,coa.GLDescription,srp_erp_debitnotemaster.debitNoteCode as bookingInvCode,'-' as invoiceDueDate  FROM srp_erp_debitnotemaster 
INNER JOIN `srp_erp_debitnotedetail` ON `srp_erp_debitnotedetail`.`debitNoteMasterAutoID` = `srp_erp_debitnotemaster`.`debitNoteMasterAutoID` AND `srp_erp_debitnotedetail`.`InvoiceAutoID` IS NULL AND `srp_erp_debitnotedetail`.`companyID` IN ( " . join(",", $company) . ")
LEFT JOIN (SELECT $fields4 debitNoteAutoID FROM `srp_erp_paymentvoucherdetail` WHERE `srp_erp_paymentvoucherdetail`.`companyID` IN ( " . join(",", $company) . ") AND type='debitnote' GROUP BY debitNoteAutoID) pvd ON pvd.`debitNoteAutoID` = `srp_erp_debitnotemaster`.`debitNoteMasterAutoID`
LEFT JOIN (SELECT groupSupplierAutoID,groupSupplierName,SupplierMasterID,groupSupplierSystemCode FROM srp_erp_groupsuppliermaster INNER JOIN srp_erp_groupsupplierdetails ON srp_erp_groupsuppliermaster.groupSupplierAutoID = srp_erp_groupsupplierdetails.groupSupplierMasterID WHERE srp_erp_groupsupplierdetails.companygroupID = " . current_companyID() . ") supp  ON `srp_erp_debitnotemaster`.`supplierID` = `supp`.`SupplierMasterID`
LEFT JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID AND groupID = " . current_companyID() . ") coa ON srp_erp_debitnotemaster.supplierliabilityAutoID = coa.chartofAccountID
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_debitnotemaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_debitnotemaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_debitnotemaster.transactionCurrencyID) 
WHERE $vendorOR AND `srp_erp_debitnotemaster`.`approvedYN` = 1 AND srp_erp_debitnotemaster.debitNoteDate <= '" . format_date($this->input->post("from")) . "' AND `srp_erp_debitnotemaster`.`companyID` IN ( " . join(",", $company) . ") GROUP BY `srp_erp_debitnotemaster`.`debitNoteMasterAutoID`)) as a $where")->result_array();
        return $result;
    }

    function get_accounts_payable_vendor_aging_summary_report()
    {
        $vendor = $this->input->post("vendorTo");
        $i = 1;
        $vendorOR = '(';
        if (!empty($vendor)) { /*generate the query according to selectd vendor*/
            foreach ($vendor as $vendor_val) {
                if ($i != 1) {
                    $vendorOR .= ' OR ';
                }
                $vendorOR .= "srp_erp_suppliermaster.supplierAutoID = '" . $vendor_val . "' ";
                $i++;
            }
        }
        $vendorOR .= ')';


        $aging = array();
        $interval = $this->input->post("interval");
        $through = $this->input->post("through");
        $z = 1;
        for ($i = $interval; $i < $through; $z++) { /*calculate aging range*/
            if ($z == 1) {
                $aging[] = $z . "-" . $interval;
            } else {
                if (($i + $interval) > $through) {
                    $aging[] = ($i + 1) . "-" . ($through);
                    $i += $interval;
                } else {
                    $aging[] = ($i + 1) . "-" . ($i + $interval);
                    $i += $interval;
                }

            }
        }
        $aging[] = "> " . ($through);

        $fields = "";
        $fields2 = "";
        $fields3 = "";
        $fields4 = "";
        $fields5 = "";
        $fields6 = "";
        $fields8 = "";
        $fields9 = "";
        $having = array();
        $groupBy = array();
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            foreach ($fieldNameChk as $val) {
                if ($val == 'companyReportingAmount') {
                    $fields .= 'srp_erp_paysupplierinvoicemaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields .= '(SUM(srp_erp_paysupplierinvoicemaster.companyReportingAmount) - (IFNULL(SUM(pvd.companyReportingAmount),0)+IFNULL(SUM(dnd.companyReportingAmount),0)+IFNULL(SUM(pva.companyReportingAmount),0)))  as ' . $val . ',';
                    $fields2 .= 'srp_erp_paymentvoucherdetail.companyReportingCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields2 .= '(IFNULL(SUM(srp_erp_paymentvoucherdetail.' . $val . '),0) - IFNULL(SUM(avd.' . $val . '),0)) * -1 as ' . $val . ',';
                    $fields4 .= 'SUM(srp_erp_paymentvoucherdetail.' . $val . ') as ' . $val . ',';
                    $fields5 .= 'SUM(srp_erp_debitnotedetail.' . $val . ') as ' . $val . ',';
                    $fields6 .= 'SUM(srp_erp_pvadvancematchdetails.' . $val . ') as ' . $val . ',';
                    $fields8 .= 'srp_erp_debitnotemaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields8 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields8 .= '(SUM(srp_erp_debitnotedetail.' . $val . ') - IFNULL(SUM(pvd.' . $val . '),0)) * -1 as ' . $val . ',';
                } else if ($val == 'companyLocalAmount') {
                    $fields .= 'srp_erp_paysupplierinvoicemaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields .= '(SUM(srp_erp_paysupplierinvoicemaster.companyLocalAmount) - (IFNULL(SUM(pvd.companyLocalAmount),0)+IFNULL(SUM(dnd.companyLocalAmount),0)+IFNULL(SUM(pva.companyLocalAmount),0)))  as ' . $val . ',';
                    $fields2 .= 'srp_erp_paymentvoucherdetail.companyLocalCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields2 .= '(IFNULL(SUM(srp_erp_paymentvoucherdetail.' . $val . '),0) - IFNULL(SUM(avd.' . $val . '),0)) * -1 as ' . $val . ',';
                    $fields4 .= 'SUM(srp_erp_paymentvoucherdetail.' . $val . ') as ' . $val . ',';
                    $fields5 .= 'SUM(srp_erp_debitnotedetail.' . $val . ') as ' . $val . ',';
                    $fields6 .= 'SUM(srp_erp_pvadvancematchdetails.' . $val . ') as ' . $val . ',';
                    $fields8 .= 'srp_erp_debitnotemaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields8 .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields8 .= '(SUM(srp_erp_debitnotedetail.' . $val . ') - IFNULL(SUM(pvd.' . $val . '),0)) * -1 as ' . $val . ',';
                } else if ($val == 'transactionAmount') {
                    $fields .= 'srp_erp_paysupplierinvoicemaster.transactionCurrency as ' . $val . 'currency,';
                    $fields .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields .= '(SUM(srp_erp_paysupplierinvoicemaster.transactionAmount) - (IFNULL(SUM(pvd.transactionAmount),0)+IFNULL(SUM(dnd.transactionAmount),0)+IFNULL(SUM(pva.transactionAmount),0)))  as ' . $val . ',';
                    $fields2 .= 'srp_erp_paymentvoucherdetail.transactionCurrency as ' . $val . 'currency,';
                    $fields2 .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields2 .= '(IFNULL(SUM(srp_erp_paymentvoucherdetail.' . $val . '),0) - IFNULL(SUM(avd.' . $val . '),0)) * -1 as ' . $val . ',';
                    $fields4 .= 'SUM(srp_erp_paymentvoucherdetail.' . $val . ') as ' . $val . ',';
                    $fields5 .= 'SUM(srp_erp_debitnotedetail.' . $val . ') as ' . $val . ',';
                    $fields6 .= 'SUM(srp_erp_pvadvancematchdetails.' . $val . ') as ' . $val . ',';
                    $fields8 .= 'srp_erp_debitnotemaster.transactionCurrency as ' . $val . 'currency,';
                    $fields8 .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields8 .= '(SUM(srp_erp_debitnotedetail.' . $val . ') - IFNULL(SUM(pvd.' . $val . '),0)) * -1 as ' . $val . ',';
                    $groupBy[] = $val . 'currency';
                } else if ($val == 'supplierCurrencyAmount') {
                    $fields .= 'srp_erp_paysupplierinvoicemaster.supplierCurrency as ' . $val . 'currency,';
                    $fields .= 'srp_erp_paysupplierinvoicemaster.supplierCurrencyDecimalPlaces as ' . $val . 'decimalPlace,';
                    $fields .= '(SUM(srp_erp_paysupplierinvoicemaster.supplierCurrencyAmount) - (IFNULL(SUM(pvd.supplierCurrencyAmount),0)+IFNULL(SUM(dnd.supplierCurrencyAmount),0)+IFNULL(SUM(pva.supplierCurrencyAmount),0)))  as ' . $val . ',';
                    $fields2 .= 'srp_erp_paymentvoucherdetail.partyCurrency as ' . $val . 'currency,';
                    $fields2 .= 'SUM(srp_erp_paymentvoucherdetail.partyCurrencyDecimalPlaces) as ' . $val . 'decimalPlace,';
                    $fields2 .= '(IFNULL(SUM(srp_erp_paymentvoucherdetail.partyAmount),0) - IFNULL(SUM(avd.' . $val . '),0)) as ' . $val . ',';
                    $fields4 .= 'SUM(srp_erp_paymentvoucherdetail.partyAmount) as ' . $val . ',';
                    $fields5 .= 'SUM(srp_erp_debitnotedetail.supplierAmount) as ' . $val . ',';
                    $fields6 .= 'SUM(srp_erp_pvadvancematchdetails.' . $val . ') as ' . $val . ',';
                    $groupBy[] = $val . 'currency';
                }
                $fields9 .= 'SUM(srp_erp_paymentvoucherdetail.' . $val . ') as ' . $val . ',';
                $having[] = $val . '!= 0';
                $fields3 .= 'a.' . $val . 'currency as currency,';
                $fields3 .= 'a.' . $val . 'DecimalPlaces as DecimalPlaces,';
                $fields3 .= 'a.' . $val . ' as ' . $val . ',';
                $fields7 = $val . ' > 0';

                if (!empty($aging)) { /*calculate aging range in query*/
                    $count = count($aging);
                    $c = 1;
                    foreach ($aging as $val2) {
                        if ($count == $c) {
                            $fields3 .= "SUM(if(a.age > " . $through . ",a." . $val . ",0)) as `" . $val2 . "`,";
                        } else {
                            $list = explode("-", $val2);
                            $fields3 .= "SUM(if(a.age >= " . $list[0] . " AND a.age <= " . $list[1] . ",a." . $val . ",0)) as `" . $val2 . "`,";
                        }
                        $c++;
                    }
                }
                $fields3 .= "SUM(if(a.age <= 0,a." . $val . ",0)) as `current`,";

                $groupByExplode = "";
                if ($groupBy) {
                    $groupByExplode = "," . join(',', $groupBy);
                }
            }
        }

        $result = $this->db->query("SELECT $fields3 a.supplierName,a.supplierSystemCode,a.comments,a.documentID,a.bookingDate,a.GLSecondaryCode,a.GLDescription,a.bookingInvCode,a.supplierID FROM 
((SELECT $fields srp_erp_suppliermaster.supplierName,srp_erp_suppliermaster.supplierSystemCode,srp_erp_paysupplierinvoicemaster.comments,srp_erp_paysupplierinvoicemaster.documentID,srp_erp_paysupplierinvoicemaster.bookingDate,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,srp_erp_paysupplierinvoicemaster.bookingInvCode,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_paysupplierinvoicemaster.`invoiceDueDate`) as age,`srp_erp_paysupplierinvoicemaster`.`supplierID` as supplierID FROM `srp_erp_paysupplierinvoicemaster` 
LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_paysupplierinvoicemaster`.`supplierID` = `srp_erp_suppliermaster`.`supplierAutoID` AND `srp_erp_suppliermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN 
(
	SELECT
		$fields4 InvoiceAutoID,srp_erp_paymentvoucherdetail.payVoucherAutoID
	FROM
	srp_erp_paymentvoucherdetail
		INNER JOIN `srp_erp_paymentvouchermaster` ON `srp_erp_paymentvouchermaster`.`payVoucherAutoID` = `srp_erp_paymentvoucherdetail`.`payVoucherAutoID` AND `srp_erp_paymentvouchermaster`.`approvedYN` = 1 AND srp_erp_paymentvouchermaster.`PVDate` <= '" . format_date($this->input->post("from")) . "'
		WHERE `srp_erp_paymentvoucherdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . "  GROUP BY srp_erp_paymentvoucherdetail.InvoiceAutoID
) pvd ON (
	pvd.`InvoiceAutoID` = `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN
(
	SELECT
		$fields5 InvoiceAutoID,srp_erp_debitnotedetail.debitNoteMasterAutoID
	FROM
		srp_erp_debitnotedetail
		INNER JOIN `srp_erp_debitnotemaster` ON `srp_erp_debitnotemaster`.`debitNoteMasterAutoID` = `srp_erp_debitnotedetail`.`debitNoteMasterAutoID` AND `srp_erp_debitnotemaster`.`approvedYN` = 1
	WHERE
		`srp_erp_debitnotedetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_debitnotemaster.`debitNoteDate` <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_debitnotedetail.InvoiceAutoID
) dnd ON (
	dnd.`InvoiceAutoID` = `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN 
(
	SELECT
		$fields6 srp_erp_pvadvancematchdetails.InvoiceAutoID
	FROM
	srp_erp_pvadvancematchdetails
		INNER JOIN `srp_erp_pvadvancematch` ON `srp_erp_pvadvancematch`.`matchID` = `srp_erp_pvadvancematchdetails`.`matchID` AND `srp_erp_pvadvancematch`.`confirmedYN` = 1
		WHERE `srp_erp_pvadvancematch`.`matchDate` <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_pvadvancematchdetails.InvoiceAutoID
) pva ON (
	pva.`InvoiceAutoID` = `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_paysupplierinvoicemaster.supplierliabilityAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_paysupplierinvoicemaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_paysupplierinvoicemaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_paysupplierinvoicemaster.transactionCurrencyID) 
WHERE $vendorOR AND `srp_erp_paysupplierinvoicemaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_paysupplierinvoicemaster.`bookingDate` <= '" . format_date($this->input->post("from")) . "' AND `srp_erp_paysupplierinvoicemaster`.`approvedYN` = 1 GROUP BY `srp_erp_paysupplierinvoicemaster`.`supplierID`,srp_erp_paysupplierinvoicemaster.`invoiceDueDate` HAVING $fields7) 
UNION ALL
(SELECT $fields2 srp_erp_suppliermaster.supplierName,srp_erp_suppliermaster.supplierSystemCode,'Advance' as comments,srp_erp_paymentvouchermaster.documentID,srp_erp_paymentvouchermaster.PVDate as bookingDate,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,srp_erp_paymentvouchermaster.PVcode as bookingInvCode,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_paymentvouchermaster.`PVdate`) as age,`srp_erp_paymentvouchermaster`.`partyID` as supplierID
FROM srp_erp_paymentvouchermaster 
INNER JOIN `srp_erp_paymentvoucherdetail` ON `srp_erp_paymentvoucherdetail`.`payVoucherAutoID` = `srp_erp_paymentvouchermaster`.`payVoucherAutoID` AND `srp_erp_paymentvoucherdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_paymentvoucherdetail.type='Advance'
LEFT JOIN (SELECT $fields6 srp_erp_pvadvancematchdetails.payVoucherAutoId 
		FROM srp_erp_pvadvancematchdetails 
		INNER JOIN `srp_erp_pvadvancematch` ON `srp_erp_pvadvancematch`.`matchID` = `srp_erp_pvadvancematchdetails`.`matchID` AND `srp_erp_pvadvancematch`.`confirmedYN` = 1
		WHERE `srp_erp_pvadvancematch`.`matchDate` <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_pvadvancematchdetails.payVoucherAutoID
		) avd ON (avd.payVoucherAutoID = `srp_erp_paymentvoucherdetail`.`payVoucherAutoID`)
LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_paymentvouchermaster`.`partyID` = `srp_erp_suppliermaster`.`supplierAutoID` AND `srp_erp_suppliermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_paymentvouchermaster.partyGLAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_paymentvouchermaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_paymentvouchermaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_paymentvouchermaster.transactionCurrencyID) 
WHERE $vendorOR AND `srp_erp_paymentvouchermaster`.`approvedYN` = 1 AND srp_erp_paymentvouchermaster.PVDate <= '" . format_date($this->input->post("from")) . "' GROUP BY `srp_erp_paymentvouchermaster`.`partyID`,srp_erp_paymentvouchermaster.`PVdate` HAVING (" . join(' AND ', $having) . "))
  UNION ALL
(SELECT $fields8 srp_erp_suppliermaster.supplierName,srp_erp_suppliermaster.supplierSystemCode,srp_erp_debitnotemaster.comments,srp_erp_debitnotemaster.documentID,srp_erp_debitnotemaster.debitNoteDate as bookingDate,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,srp_erp_debitnotemaster.debitNoteCode as bookingInvCode,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_debitnotemaster.`debitNoteDate`) as age,`srp_erp_debitnotemaster`.`supplierID` as supplierID  FROM srp_erp_debitnotemaster 
INNER JOIN `srp_erp_debitnotedetail` ON `srp_erp_debitnotedetail`.`debitNoteMasterAutoID` = `srp_erp_debitnotemaster`.`debitNoteMasterAutoID` AND `srp_erp_debitnotedetail`.`InvoiceAutoID` IS NULL AND `srp_erp_debitnotedetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT $fields9 debitNoteAutoID FROM `srp_erp_paymentvoucherdetail` WHERE `srp_erp_paymentvoucherdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND type='debitnote' GROUP BY debitNoteAutoID) pvd ON pvd.`debitNoteAutoID` = `srp_erp_debitnotemaster`.`debitNoteMasterAutoID`
LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_debitnotemaster`.`supplierID` = `srp_erp_suppliermaster`.`supplierAutoID` AND `srp_erp_suppliermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_debitnotemaster.supplierliabilityAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_debitnotemaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_debitnotemaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_debitnotemaster.transactionCurrencyID) 
WHERE $vendorOR AND `srp_erp_debitnotemaster`.`approvedYN` = 1 AND srp_erp_debitnotemaster.debitNoteDate <= '" . format_date($this->input->post("from")) . "' AND `srp_erp_debitnotemaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " GROUP BY `srp_erp_debitnotemaster`.`supplierID`,`srp_erp_debitnotemaster`.`debitNoteDate` HAVING (" . join(' AND ', $having) . "))) as a GROUP BY a.supplierSystemCode $groupByExplode ")->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_accounts_payable_vendor_aging_detail_report()
    {
        $vendor = $this->input->post("vendorTo");
        $i = 1;
        $vendorOR = '(';
        if (!empty($vendor)) { /*generate the query according to selected vendor*/
            foreach ($vendor as $vendor_val) {
                if ($i != 1) {
                    $vendorOR .= ' OR ';
                }
                $vendorOR .= "srp_erp_suppliermaster.supplierAutoID = '" . $vendor_val . "' ";
                $i++;
            }
        }
        $vendorOR .= ')';


        $aging = array();
        $interval = $this->input->post("interval");
        $through = $this->input->post("through");
        $z = 1;
        for ($i = $interval; $i < $through; $z++) { /*calculate aging range*/
            if ($z == 1) {
                $aging[] = $z . "-" . $interval;
            } else {
                if (($i + $interval) > $through) {
                    $aging[] = ($i + 1) . "-" . ($through);
                    $i += $interval;
                } else {
                    $aging[] = ($i + 1) . "-" . ($i + $interval);
                    $i += $interval;
                }

            }
        }
        $aging[] = "> " . ($through);

        $fields = "";
        $fields2 = "";
        $fields3 = "";
        $fields4 = "";
        $fields5 = "";
        $fields6 = "";
        $fields8 = "";
        $fields9 = "";
        $having = array();
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            foreach ($fieldNameChk as $val) {
                if ($val == 'companyReportingAmount') {
                    $fields .= 'srp_erp_paysupplierinvoicemaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields2 .= 'srp_erp_paymentvoucherdetail.companyReportingCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields4 .= 'SUM(srp_erp_paymentvoucherdetail.' . $val . ') as ' . $val . ',';
                    $fields5 .= 'SUM(srp_erp_debitnotedetail.' . $val . ') as ' . $val . ',';
                    $fields2 .= '(IFNULL(SUM(srp_erp_paymentvoucherdetail.' . $val . '),0) - IFNULL(SUM(avd.' . $val . '),0))*-1 as ' . $val . ',';
                    $fields8 .= 'srp_erp_debitnotemaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields8 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $having[] = $val . '!= 0';
                } else if ($val == 'companyLocalAmount') {
                    $fields .= 'srp_erp_paysupplierinvoicemaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields2 .= 'srp_erp_paymentvoucherdetail.companyLocalCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields4 .= 'SUM(srp_erp_paymentvoucherdetail.' . $val . ') as ' . $val . ',';
                    $fields5 .= 'SUM(srp_erp_debitnotedetail.' . $val . ') as ' . $val . ',';
                    $fields2 .= '(IFNULL(SUM(srp_erp_paymentvoucherdetail.' . $val . '),0) - IFNULL(SUM(avd.' . $val . '),0))*-1 as ' . $val . ',';
                    $fields8 .= 'srp_erp_debitnotemaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields8 .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $having[] = $val . '!= 0';
                } else if ($val == 'transactionAmount') {
                    $fields .= 'srp_erp_paysupplierinvoicemaster.transactionCurrency as ' . $val . 'currency,';
                    $fields .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields2 .= 'srp_erp_paymentvoucherdetail.transactionCurrency as ' . $val . 'currency,';
                    $fields2 .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields4 .= 'SUM(srp_erp_paymentvoucherdetail.' . $val . ') as ' . $val . ',';
                    $fields5 .= 'SUM(srp_erp_debitnotedetail.' . $val . ') as ' . $val . ',';
                    $fields2 .= '(IFNULL(SUM(srp_erp_paymentvoucherdetail.' . $val . '),0) - IFNULL(SUM(avd.' . $val . '),0))*-1 as ' . $val . ',';
                    $fields8 .= 'srp_erp_debitnotemaster.transactionCurrency as ' . $val . 'currency,';
                    $fields8 .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $having[] = $val . '!= 0';
                } else if ($val == 'supplierCurrencyAmount') {
                    $fields .= 'srp_erp_paysupplierinvoicemaster.supplierCurrency as ' . $val . 'currency,';
                    $fields .= 'srp_erp_paysupplierinvoicemaster.supplierCurrencyDecimalPlaces as ' . $val . 'decimalPlace,';
                    $fields2 .= 'srp_erp_paymentvoucherdetail.partyCurrency as ' . $val . 'currency,';
                    $fields2 .= 'SUM(srp_erp_paymentvoucherdetail.partyCurrencyDecimalPlaces) as ' . $val . 'decimalPlace,';
                    $fields4 .= 'SUM(srp_erp_paymentvoucherdetail.partyAmount) as ' . $val . ',';
                    $fields5 .= 'SUM(srp_erp_debitnotedetail.supplierAmount) as ' . $val . ',';
                    $fields2 .= '(IFNULL(SUM(srp_erp_paymentvoucherdetail.partyAmount),0) - IFNULL(SUM(avd.' . $val . '),0))*-1 as ' . $val . ',';
                    $having[] = $val . '!= 0';
                }
                $fields8 .= '(SUM(srp_erp_debitnotedetail.' . $val . ') - IFNULL(SUM(pvd.' . $val . '),0)) * -1 as ' . $val . ',';
                $fields9 .= 'SUM(srp_erp_paymentvoucherdetail.' . $val . ') as ' . $val . ',';
                $fields .= '(SUM(srp_erp_paysupplierinvoicemaster.' . $val . ') - (IFNULL(SUM(pvd.' . $val . '),0)+IFNULL(SUM(dnd.' . $val . '),0)+IFNULL(SUM(pva.' . $val . '),0)))  as ' . $val . ',';
                $fields3 .= 'a.' . $val . 'currency as currency,';
                $fields3 .= 'a.' . $val . 'DecimalPlaces as DecimalPlaces,';
                $fields3 .= 'a.' . $val . ' as ' . $val . ',';
                $fields6 .= 'SUM(srp_erp_pvadvancematchdetails.' . $val . ') as ' . $val . ',';
                $fields7 = $val . ' > 0';

                if (!empty($aging)) { /*calculate aging range in query*/
                    $count = count($aging);
                    $c = 1;
                    foreach ($aging as $val2) {
                        if ($count == $c) {
                            $fields3 .= "SUM(if(a.age > " . $through . ",a." . $val . ",0)) as `" . $val2 . "`,";
                        } else {
                            $list = explode("-", $val2);
                            $fields3 .= "SUM(if(a.age >= " . $list[0] . " AND a.age <= " . $list[1] . ",a." . $val . ",0)) as `" . $val2 . "`,";
                        }
                        $c++;
                    }
                }
                $fields3 .= "SUM(if(a.age <= 0,a." . $val . ",0)) as `current`,";
            }
        }


        $result = $this->db->query("SELECT $fields3 a.invoiceAutoID,a.supplierInvoiceNo,DATE_FORMAT(a.documentDate,'" . $this->format . "') as documentDate,a.documentCode,a.documentID,a.supplierName,a.supplierSystemCode,a.comments,a.GLSecondaryCode,a.GLDescription FROM 
((SELECT $fields srp_erp_paysupplierinvoicemaster.invoiceAutoID,srp_erp_paysupplierinvoicemaster.supplierInvoiceNo as supplierInvoiceNo,srp_erp_paysupplierinvoicemaster.documentID as documentID,srp_erp_paysupplierinvoicemaster.bookingInvCode as documentCode,srp_erp_paysupplierinvoicemaster.invoiceDueDate as documentDate, srp_erp_suppliermaster.supplierName,srp_erp_suppliermaster.supplierSystemCode,srp_erp_paysupplierinvoicemaster.comments,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_paysupplierinvoicemaster.`invoiceDueDate`) as age FROM `srp_erp_paysupplierinvoicemaster` 
LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_paysupplierinvoicemaster`.`supplierID` = `srp_erp_suppliermaster`.`supplierAutoID` AND `srp_erp_suppliermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN 
(
	SELECT
		$fields4 InvoiceAutoID,srp_erp_paymentvoucherdetail.payVoucherAutoID
	FROM
	srp_erp_paymentvoucherdetail
		INNER JOIN `srp_erp_paymentvouchermaster` ON `srp_erp_paymentvouchermaster`.`payVoucherAutoID` = `srp_erp_paymentvoucherdetail`.`payVoucherAutoID` AND `srp_erp_paymentvouchermaster`.`approvedYN` = 1  AND srp_erp_paymentvouchermaster.`PVDate` <= '" . format_date($this->input->post("from")) . "'
		WHERE `srp_erp_paymentvoucherdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . "   GROUP BY srp_erp_paymentvoucherdetail.InvoiceAutoID
) pvd ON (
	pvd.`InvoiceAutoID` = `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN
(
	SELECT
		$fields5 InvoiceAutoID,srp_erp_debitnotedetail.debitNoteMasterAutoID
	FROM
		srp_erp_debitnotedetail
		INNER JOIN `srp_erp_debitnotemaster` ON `srp_erp_debitnotemaster`.`debitNoteMasterAutoID` = `srp_erp_debitnotedetail`.`debitNoteMasterAutoID` AND `srp_erp_debitnotemaster`.`approvedYN` = 1
	WHERE
		`srp_erp_debitnotedetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_debitnotemaster.`debitNoteDate` <= '" . format_date($this->input->post("from")) . "'  GROUP BY srp_erp_debitnotedetail.InvoiceAutoID
) dnd ON (
	dnd.`InvoiceAutoID` = `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN 
(
	SELECT
		$fields6 srp_erp_pvadvancematchdetails.InvoiceAutoID
	FROM
	srp_erp_pvadvancematchdetails
		INNER JOIN `srp_erp_pvadvancematch` ON `srp_erp_pvadvancematch`.`matchID` = `srp_erp_pvadvancematchdetails`.`matchID` AND `srp_erp_pvadvancematch`.`confirmedYN` = 1
		WHERE `srp_erp_pvadvancematch`.`matchDate` <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_pvadvancematchdetails.InvoiceAutoID
) pva ON (
	pva.`InvoiceAutoID` = `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_paysupplierinvoicemaster.supplierliabilityAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_paysupplierinvoicemaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_paysupplierinvoicemaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_paysupplierinvoicemaster.transactionCurrencyID) 
WHERE $vendorOR AND `srp_erp_paysupplierinvoicemaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_paysupplierinvoicemaster.`bookingDate` <= '" . format_date($this->input->post("from")) . "' AND `srp_erp_paysupplierinvoicemaster`.`approvedYN` = 1 GROUP BY `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID`,srp_erp_paysupplierinvoicemaster.`invoiceDueDate` HAVING $fields7) 
UNION ALL
(SELECT $fields2 srp_erp_paymentvouchermaster.payVoucherAutoID as invoiceAutoID,'-' as supplierInvoiceNo,srp_erp_paymentvouchermaster.documentID AS documentID,
srp_erp_paymentvouchermaster.PVcode AS documentCode,
					srp_erp_paymentvouchermaster.PVDate AS documentDate,srp_erp_suppliermaster.supplierName,srp_erp_suppliermaster.supplierSystemCode,'Advance' as comments,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_paymentvouchermaster.`PVdate`) as age
FROM srp_erp_paymentvouchermaster 
INNER JOIN `srp_erp_paymentvoucherdetail` ON `srp_erp_paymentvoucherdetail`.`payVoucherAutoID` = `srp_erp_paymentvouchermaster`.`payVoucherAutoID` AND `srp_erp_paymentvoucherdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_paymentvoucherdetail.type='Advance'
LEFT JOIN (SELECT $fields6 srp_erp_pvadvancematchdetails.payVoucherAutoId 
		FROM srp_erp_pvadvancematchdetails 
		INNER JOIN `srp_erp_pvadvancematch` ON `srp_erp_pvadvancematch`.`matchID` = `srp_erp_pvadvancematchdetails`.`matchID` AND `srp_erp_pvadvancematch`.`confirmedYN` = 1
		WHERE `srp_erp_pvadvancematch`.`matchDate` <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_pvadvancematchdetails.payVoucherAutoID
		) avd ON (avd.payVoucherAutoID = `srp_erp_paymentvoucherdetail`.`payVoucherAutoID`)
LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_paymentvouchermaster`.`partyID` = `srp_erp_suppliermaster`.`supplierAutoID` AND `srp_erp_suppliermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_paymentvouchermaster.partyGLAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_paymentvouchermaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_paymentvouchermaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_paymentvouchermaster.transactionCurrencyID) 
WHERE $vendorOR AND `srp_erp_paymentvouchermaster`.`approvedYN` = 1 AND srp_erp_paymentvouchermaster.PVDate <= '" . format_date($this->input->post("from")) . "' GROUP BY `srp_erp_paymentvouchermaster`.`payVoucherAutoID`,srp_erp_paymentvouchermaster.`PVdate` HAVING (" . join(' AND ', $having) . "))
UNION ALL
(
SELECT $fields8 
`srp_erp_debitnotemaster`.`debitNoteMasterAutoID` as invoiceAutoID,'-' as supplierInvoiceNo,srp_erp_debitnotemaster.documentID AS documentID,
srp_erp_debitnotemaster.debitNoteCode AS documentCode,
					srp_erp_debitnotemaster.debitNoteDate AS documentDate,srp_erp_suppliermaster.supplierName,srp_erp_suppliermaster.supplierSystemCode,srp_erp_debitnotemaster.comments,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_debitnotemaster.debitNoteDate) as age
  FROM srp_erp_debitnotemaster 
INNER JOIN `srp_erp_debitnotedetail` ON `srp_erp_debitnotedetail`.`debitNoteMasterAutoID` = `srp_erp_debitnotemaster`.`debitNoteMasterAutoID` AND `srp_erp_debitnotedetail`.`InvoiceAutoID` IS NULL AND `srp_erp_debitnotedetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT $fields9 debitNoteAutoID FROM `srp_erp_paymentvoucherdetail` WHERE `srp_erp_paymentvoucherdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND type='debitnote' GROUP BY debitNoteAutoID) pvd ON pvd.`debitNoteAutoID` = `srp_erp_debitnotemaster`.`debitNoteMasterAutoID`
LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_debitnotemaster`.`supplierID` = `srp_erp_suppliermaster`.`supplierAutoID` AND `srp_erp_suppliermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_debitnotemaster.supplierliabilityAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_debitnotemaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_debitnotemaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_debitnotemaster.transactionCurrencyID) 
WHERE $vendorOR AND `srp_erp_debitnotemaster`.`approvedYN` = 1 AND srp_erp_debitnotemaster.debitNoteDate <= '" . format_date($this->input->post("from")) . "' AND `srp_erp_debitnotemaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " GROUP BY `srp_erp_debitnotemaster`.`debitNoteMasterAutoID`,`srp_erp_debitnotemaster`.`debitNoteDate` HAVING (" . join(' AND ', $having) . "))
) as a GROUP BY a.documentCode ")->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    function get_accounts_receivable_customer_ledger_report()
    {
        $customer = $this->input->post("customerTo");
        $i = 1;
        $customerOR = '(';
        if (!empty($customer)) { /*generate the query according to selected vendor*/
            foreach ($customer as $customer_val) {
                if ($i != 1) {
                    $customerOR .= ' OR ';
                }
                $customerOR .= "srp_erp_generalledger.partyAutoID = '" . $customer_val . "' ";
                $i++;
            }
        }
        $customerOR .= ')';

        $feilds = "";
        $feilds2 = "";
        $feilds3 = " ORDER BY a.documentDate";
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) {
            foreach ($fieldNameChk as $val) { /*generate the query according to selectd columns*/
                if ($val == "transactionAmount") {
                    $feilds .= " srp_erp_generalledger.transactionCurrency AS transactionCurrency,";
                    $feilds2 .= " a.transactionCurrency AS transactionCurrency,";
                    $feilds .= " TC.DecimalPlaces AS " . $val . "DecimalPlaces,";
                    $feilds3 = " ORDER BY a.documentDate,a.transactionCurrency";
                }
                if ($val == "companyLocalAmount") {
                    $feilds .= " CL.DecimalPlaces AS " . $val . "DecimalPlaces,";
                }
                if ($val == "companyReportingAmount") {
                    $feilds .= " CR.DecimalPlaces AS " . $val . "DecimalPlaces,";
                }
                $feilds .= " SUM(srp_erp_generalledger." . $val . ") AS " . $val . ",";
                $feilds2 .= " a." . $val . " AS " . $val . ",";
                $feilds2 .= " a." . $val . "DecimalPlaces,";
            }
        }

        $result = $this->db->query("SELECT $feilds2 a.document,a.documentMasterAutoID,
    DATE_FORMAT(a.documentDate,'" . $this->format . "') as documentDate,a.documentCode,a.documentSystemCode,a.documentNarration,a.customerName,a.customerSystemCode,a.GLSecondaryCode,a.GLDescription FROM
     ((SELECT $feilds srp_erp_documentcodemaster.document,srp_erp_generalledger.documentMasterAutoID,srp_erp_generalledger.documentDate,srp_erp_generalledger.documentCode,srp_erp_generalledger.documentSystemCode,srp_erp_generalledger.documentNarration,srp_erp_customermaster.customerName,srp_erp_customermaster.customerSystemCode,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.GLDescription FROM srp_erp_generalledger
    INNER JOIN srp_erp_customermaster ON srp_erp_generalledger.partyAutoID = srp_erp_customermaster.customerAutoID AND srp_erp_generalledger.subLedgerType = 3 AND srp_erp_customermaster.companyID = " . $this->common_data['company_data']['company_id'] . " 
    INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "  
    LEFT JOIN srp_erp_documentcodemaster ON srp_erp_documentcodemaster.documentID = srp_erp_generalledger.documentCode  AND srp_erp_documentcodemaster.companyID = " . $this->common_data['company_data']['company_id'] . "
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_generalledger.transactionCurrencyID) 
    WHERE $customerOR AND srp_erp_generalledger.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY srp_erp_generalledger.documentSystemCode,srp_erp_generalledger.partyAutoID) 
    UNION ALL
    (SELECT $feilds '' as document,srp_erp_generalledger.documentMasterAutoID,'1970-01-01' as documentDate,'' as documentCode,'' as documentSystemCode,'Opening Balance' as documentNarration,srp_erp_customermaster.customerName,srp_erp_customermaster.customerSystemCode,srp_erp_chartofaccounts.GLSecondaryCode,srp_erp_chartofaccounts.GLDescription  FROM srp_erp_generalledger 
    INNER JOIN srp_erp_customermaster ON srp_erp_generalledger.partyAutoID = srp_erp_customermaster.customerAutoID AND srp_erp_generalledger.subLedgerType = 3 AND srp_erp_customermaster.companyID = " . $this->common_data['company_data']['company_id'] . " 
    INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "  
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_generalledger.transactionCurrencyID) 
    WHERE $customerOR AND srp_erp_generalledger.documentDate < '" . format_date($this->input->post("from")) . "'  AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " 
    GROUP BY srp_erp_generalledger.GLAutoID,srp_erp_customermaster.customerAutoID)) AS a
      $feilds3")->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    function get_accounts_receivable_customer_ledger_group_report()
    {
        $company = $this->get_group_company();
        $customer = $this->input->post("customerTo");
        $i = 1;
        $customerOR = '(';
        if (!empty($customer)) { /*generate the query according to selected vendor*/
            foreach ($customer as $customer_val) {
                if ($i != 1) {
                    $customerOR .= ' OR ';
                }
                $customerOR .= "cust.groupCustomerAutoID = '" . $customer_val . "' ";
                $i++;
            }
        }
        $customerOR .= ')';

        $feilds = "";
        $feilds2 = "";
        $feilds3 = " ORDER BY a.documentDate";
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) {
            foreach ($fieldNameChk as $val) { /*generate the query according to selectd columns*/
                if ($val == "transactionAmount") {
                    $feilds .= " srp_erp_generalledger.transactionCurrency AS transactionCurrency,";
                    $feilds2 .= " a.transactionCurrency AS transactionCurrency,";
                    $feilds .= " TC.DecimalPlaces AS " . $val . "DecimalPlaces,";
                    $feilds3 = " ORDER BY a.documentDate,a.transactionCurrency";
                }
                if ($val == "companyLocalAmount") {
                    $feilds .= " CL.DecimalPlaces AS " . $val . "DecimalPlaces,";
                }
                if ($val == "companyReportingAmount") {
                    $feilds .= " CR.DecimalPlaces AS " . $val . "DecimalPlaces,";
                }
                $feilds .= "SUM(srp_erp_generalledger." . $val . ") AS " . $val . ",";
                $feilds2 .= " a." . $val . " AS " . $val . ",";
                $feilds2 .= " a." . $val . "DecimalPlaces,";
            }
        }

        $result = $this->db->query("SELECT $feilds2 a.document,a.documentMasterAutoID,
    DATE_FORMAT(a.documentDate,'" . $this->format . "') as documentDate,a.documentCode,a.documentSystemCode,a.documentNarration,a.customerName,a.customerSystemCode,a.GLSecondaryCode,a.GLDescription FROM
     ((SELECT $feilds dc.document,srp_erp_generalledger.documentMasterAutoID,srp_erp_generalledger.documentDate,srp_erp_generalledger.documentCode,srp_erp_generalledger.documentSystemCode,srp_erp_generalledger.documentNarration,cust.groupCustomerName as customerName,cust.groupcustomerSystemCode as customerSystemCode,coa.GLSecondaryCode,coa.GLDescription FROM srp_erp_generalledger 
    INNER JOIN ( SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . ") cust ON srp_erp_generalledger.partyAutoID = cust.customerMasterID 
    INNER JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID AND groupID = " . current_companyID() . ") coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID  
    LEFT JOIN (SELECT document,documentID FROM srp_erp_documentcodemaster WHERE companyID IN ( " . join(",", $company) . ") GROUP BY documentID) dc ON (dc.documentID = srp_erp_generalledger.documentCode)
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_generalledger.transactionCurrencyID) 
    WHERE $customerOR AND srp_erp_generalledger.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "' AND srp_erp_generalledger.subLedgerType = 3 AND srp_erp_generalledger.companyID IN(" . join(',', $company) . ") GROUP BY srp_erp_generalledger.documentSystemCode,srp_erp_generalledger.partyAutoID) 
    UNION ALL
    (SELECT $feilds '' as document,srp_erp_generalledger.documentMasterAutoID,'1970-01-01' as documentDate,'' as documentCode,'' as documentSystemCode,'Opening Balance' as documentNarration,cust.groupCustomerName as customerName,cust.groupcustomerSystemCode as customerSystemCode,coa.GLSecondaryCode,coa.GLDescription FROM srp_erp_generalledger 
    INNER JOIN ( SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . ") cust ON srp_erp_generalledger.partyAutoID = cust.customerMasterID 
    INNER JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription,GLAutoID FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID WHERE groupID = " . current_companyID() . ") coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
    LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_generalledger.transactionCurrencyID) 
    WHERE $customerOR AND srp_erp_generalledger.documentDate < '" . format_date($this->input->post("from")) . "' AND srp_erp_generalledger.subLedgerType = 3 AND srp_erp_generalledger.companyID IN(" . join(',', $company) . ") 
    GROUP BY coa.GLAutoID,
	cust.groupCustomerAutoID)) AS a
      $feilds3")->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    function get_accounts_receivable_customer_statement_report($overdue = false)
    {
        $where = "";
        if (isset($_POST["currency"]) && $_POST["currency"] != "") {
            $where = " WHERE " . $this->input->post("fieldNameChk")[0] . "currency = '" . $_POST["currency"] . "'";
        }
        $columnCheck = "invoiceDate";
        if ($overdue) {
            $columnCheck = "invoiceDueDate";
        }
        $customer = $this->input->post("customerTo");
        $i = 1;
        $customerOR = '(';
        if (!empty($customer)) { /*generate the query according to selected customer*/
            foreach ($customer as $customer_val) {
                if ($i != 1) {
                    $customerOR .= ' OR ';
                }
                $customerOR .= "srp_erp_customermaster.customerAutoID = '" . $customer_val . "' ";
                $i++;
            }
        }
        $customerOR .= ')';

        $fields = "";
        $fields2 = "";
        $fields3 = "";
        $fields4 = "";
        $fields5 = "";
        $fields6 = "";
        $fields7 = "";
        $fields8 = "";
        $having = array();
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) {
            foreach ($fieldNameChk as $val) {
                if ($val == 'transactionAmount') {
                    $fields .= 'srp_erp_customerinvoicemaster.transactionCurrency as ' . $val . 'currency,';
                    $fields .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields2 .= 'srp_erp_customerreceiptmaster.transactionCurrency as ' . $val . 'currency,';
                    $fields2 .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields7 = "ORDER BY transactionAmountCurrency";
                    $fields8 .= 'srp_erp_creditnotemaster.transactionCurrency as ' . $val . 'currency,';
                    $fields8 .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';

                } else if ($val == 'companyReportingAmount') {
                    $fields .= 'srp_erp_customerinvoicemaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields2 .= 'srp_erp_customerreceiptmaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields8 .= 'srp_erp_creditnotemaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields8 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                } else if ($val == 'companyLocalAmount') {
                    $fields .= 'srp_erp_customerinvoicemaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields2 .= 'srp_erp_customerreceiptmaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields8 .= 'srp_erp_creditnotemaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields8 .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                }
                $fields .= '(SUM(srp_erp_customerinvoicemaster.' . $val . ') - (IFNULL(SUM(pvd.' . $val . '),0)+IFNULL(SUM(cnd.' . $val . '),0)+IFNULL(SUM(ca.' . $val . '),0))) as ' . $val . ',';
                $fields2 .= '(SUM(srp_erp_customerreceiptdetail.' . $val . ') - IFNULL(SUM(avd.' . $val . '),0)) * -1 as ' . $val . ',';
                $fields3 .= 'a.' . $val . 'currency as ' . $val . 'currency,';
                $fields3 .= 'a.' . $val . 'DecimalPlaces,';
                $fields3 .= 'a.' . $val . ' as ' . $val . ',';
                $fields4 .= 'IFNULL(SUM(srp_erp_customerreceiptdetail.' . $val . '),0) as ' . $val . ',';
                $fields5 .= 'SUM(srp_erp_creditnotedetail.' . $val . ') as ' . $val . ',';
                $fields6 .= 'SUM(srp_erp_rvadvancematchdetails.' . $val . ') as ' . $val . ',';
                $fields8 .= '(SUM(srp_erp_creditnotedetail.' . $val . ') - IFNULL(SUM(cvd.' . $val . '),0)) * -1 as ' . $val . ',';
                $having[] = $val . '!= -0 AND ' . $val . ' != 0';
            }
        }

        $result = $this->db->query("SELECT $fields3 a.invoiceAutoID,a.document,a.age,DATE_FORMAT(a.invoiceDueDate,'" . $this->format . "') as invoiceDueDate,a.customerAddress,a.customerName,a.customerSystemCode,a.comments,a.documentID,DATE_FORMAT(a.bookingDate,'" . $this->format . "') as bookingDate,a.GLSecondaryCode,a.GLDescription,a.bookingInvCode,a.customerID FROM
((SELECT $fields srp_erp_customerinvoicemaster.invoiceAutoID,srp_erp_documentcodemaster.document,srp_erp_customermaster.customerAddress1 as customerAddress,srp_erp_customermaster.customerName,srp_erp_customermaster.customerSystemCode,srp_erp_customerinvoicemaster.invoiceNarration as comments,srp_erp_customerinvoicemaster.documentID,srp_erp_customerinvoicemaster.invoiceDueDate as invoiceDueDate,srp_erp_customerinvoicemaster.invoiceDate as bookingDate,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,srp_erp_customerinvoicemaster.invoiceCode as bookingInvCode,`srp_erp_customerinvoicemaster`.`customerID` as customerID,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_customerinvoicemaster.`invoiceDueDate`) as age
FROM `srp_erp_customerinvoicemaster` 
LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customerinvoicemaster`.`customerID` = `srp_erp_customermaster`.`customerAutoID` AND `srp_erp_customermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN srp_erp_documentcodemaster ON srp_erp_documentcodemaster.documentID = srp_erp_customerinvoicemaster.documentID  AND srp_erp_documentcodemaster.companyID = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN 
(
	SELECT
		$fields4 srp_erp_customerreceiptdetail.invoiceAutoID,srp_erp_customerreceiptdetail.receiptVoucherAutoID
	FROM
		srp_erp_customerreceiptdetail
		INNER JOIN `srp_erp_customerreceiptmaster` ON `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId` = `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` AND `srp_erp_customerreceiptmaster`.`approvedYN` = 1
	WHERE
		`srp_erp_customerreceiptdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_customerreceiptmaster.RVDate <= '" . format_date($this->input->post("from")) . "'  GROUP BY srp_erp_customerreceiptdetail.invoiceAutoID
) pvd ON (
	pvd.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID`
)
LEFT JOIN
(
	SELECT
		$fields5 invoiceAutoID,srp_erp_creditnotedetail.creditNoteMasterAutoID
	FROM
		srp_erp_creditnotedetail
		INNER JOIN `srp_erp_creditnotemaster` ON `srp_erp_creditnotemaster`.`creditNoteMasterAutoID` = `srp_erp_creditnotedetail`.`creditNoteMasterAutoID` AND `srp_erp_creditnotemaster`.`approvedYN` = 1
	WHERE
		`srp_erp_creditnotedetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_creditnotemaster.creditNoteDate <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_creditnotedetail.invoiceAutoID
) cnd ON (
	cnd.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID`
)
LEFT JOIN( 
SELECT 
$fields6 srp_erp_rvadvancematchdetails.InvoiceAutoID,srp_erp_rvadvancematchdetails.receiptVoucherAutoID
	FROM srp_erp_rvadvancematchdetails 
	INNER JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematchdetails`.`matchID` = `srp_erp_rvadvancematch`.`matchID` AND `srp_erp_rvadvancematch`.`confirmedYN` = 1
	WHERE `srp_erp_rvadvancematchdetails`.`companyID` = " . $this->common_data['company_data']['company_id'] . " GROUP BY srp_erp_rvadvancematchdetails.InvoiceAutoID) 
	ca ON (
	ca.`InvoiceAutoID` = `srp_erp_customerinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_customerinvoicemaster.customerReceivableAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_customerinvoicemaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_customerinvoicemaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_customerinvoicemaster.transactionCurrencyID) 
WHERE $customerOR AND `srp_erp_customerinvoicemaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_customerinvoicemaster.$columnCheck <= '" . format_date($this->input->post("from")) . "' AND `srp_erp_customerinvoicemaster`.`approvedYN` = 1 GROUP BY `srp_erp_customerinvoicemaster`.`invoiceAutoID` HAVING (" . join(' AND ', $having) . "))
 UNION ALL (
 SELECT $fields2 srp_erp_customerreceiptmaster.receiptVoucherAutoID as invoiceAutoID,
 srp_erp_documentcodemaster.document,
 srp_erp_customermaster.customerAddress1 as customerAddress,
 srp_erp_customermaster.customerName,
 srp_erp_customermaster.customerSystemCode,
 'Advance' as comments,
 srp_erp_customerreceiptmaster.documentID,
 '-' as invoiceDueDate,
 srp_erp_customerreceiptmaster.RVDate as bookingDate,
 srp_erp_chartofAccounts.GLSecondaryCode,
 srp_erp_chartofAccounts.GLDescription,
 srp_erp_customerreceiptmaster.RVCode as bookingInvCode,
 `srp_erp_customerreceiptmaster`.`customerID` as customerID,
 DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_customerreceiptmaster.`RVDate`) as age  
 FROM srp_erp_customerreceiptmaster 
INNER JOIN `srp_erp_customerreceiptdetail` ON `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` = `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId` AND `srp_erp_customerreceiptdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND  srp_erp_customerreceiptdetail.type='Advance'
LEFT JOIN (SELECT $fields6 srp_erp_rvadvancematchdetails.receiptVoucherAutoID 
		FROM srp_erp_rvadvancematchdetails 
		INNER JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematch`.`matchID` = `srp_erp_rvadvancematchdetails`.`matchID` AND `srp_erp_rvadvancematch`.`confirmedYN` = 1
		WHERE `srp_erp_rvadvancematch`.`matchDate` <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_rvadvancematchdetails.receiptVoucherAutoID
		) avd ON (avd.receiptVoucherAutoID = `srp_erp_customerreceiptdetail`.`receiptVoucherAutoID`)
LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customerreceiptmaster`.`customerID` = `srp_erp_customermaster`.`customerAutoID` AND `srp_erp_customermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_customerreceiptmaster.customerreceivableAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN srp_erp_documentcodemaster ON srp_erp_documentcodemaster.documentID = srp_erp_customerreceiptmaster.documentID  AND srp_erp_documentcodemaster.companyID = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_customerreceiptmaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_customerreceiptmaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_customerreceiptmaster.transactionCurrencyID) 
WHERE $customerOR AND `srp_erp_customerreceiptmaster`.`approvedYN` = 1 AND srp_erp_customerreceiptmaster.RVDate <= '" . format_date($this->input->post("from")) . "' AND srp_erp_customerreceiptdetail.invoiceAutoID IS NULL GROUP BY `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId`
 ) UNION ALL 
 ( SELECT
$fields8 srp_erp_creditnotemaster.creditNoteMasterAutoID AS InvoiceAutoID,
srp_erp_documentcodemaster.document,
srp_erp_customermaster.customerAddress1 as customerAddress,
srp_erp_customermaster.customerName,
srp_erp_customermaster.customerSystemCode,
srp_erp_creditnotemaster.comments,
srp_erp_creditnotemaster.documentID,
'-' as invoiceDueDate,
srp_erp_creditnotemaster.creditNoteDate AS bookingDate,
srp_erp_chartofAccounts.GLSecondaryCode,
srp_erp_chartofAccounts.GLDescription,
srp_erp_creditnotemaster.creditNoteCode AS bookingInvCode,
`srp_erp_creditnotemaster`.customerID as customerID,
DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_creditnotemaster.creditNoteDate) as age
FROM
	srp_erp_creditnotemaster
	INNER JOIN `srp_erp_creditnotedetail` ON `srp_erp_creditnotedetail`.`creditNoteMasterAutoID` = `srp_erp_creditnotemaster`.`creditNoteMasterAutoID` 
	AND `srp_erp_creditnotedetail`.invoiceAutoID IS NULL 
	AND `srp_erp_creditnotedetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
	LEFT JOIN ( SELECT $fields4 creditNoteAutoID FROM `srp_erp_customerreceiptdetail` WHERE `srp_erp_customerreceiptdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND type = 'creditnote' GROUP BY creditNoteAutoID ) cvd ON cvd.`creditNoteAutoID` = `srp_erp_creditnotemaster`.`creditNoteMasterAutoID`
	LEFT JOIN `srp_erp_customermaster` ON `srp_erp_creditnotemaster`.`customerID` = `srp_erp_customermaster`.`customerAutoID` 
	AND `srp_erp_customermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
	LEFT JOIN srp_erp_chartofAccounts ON srp_erp_creditnotemaster.customerreceivableAutoID = srp_erp_chartofAccounts.GLAutoID 
	AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
	LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CR ON ( CR.currencyID = srp_erp_creditnotemaster.companyReportingCurrencyID )
	LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CL ON ( CL.currencyID = srp_erp_creditnotemaster.companyLocalCurrencyID )
	LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) TC ON ( TC.currencyID = srp_erp_creditnotemaster.transactionCurrencyID ) 
	LEFT JOIN srp_erp_documentcodemaster ON srp_erp_documentcodemaster.documentID = srp_erp_creditnotemaster.documentID  AND srp_erp_documentcodemaster.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	$customerOR 
	AND `srp_erp_creditnotemaster`.`approvedYN` = 1 
	AND srp_erp_creditnotemaster.creditNoteDate <= '" . format_date($this->input->post("from")) . "' 
	AND `srp_erp_creditnotemaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
GROUP BY
	`srp_erp_creditnotemaster`.`creditNoteMasterAutoID` 
	)$fields7) as a $where $fields7")->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_accounts_receivable_customer_statement_group_report($overdue = false)
    {
        $where = "";
        if (isset($_POST["currency"]) && $_POST["currency"] != "") {
            $where = " WHERE " . $this->input->post("fieldNameChk")[0] . "currency = '" . $_POST["currency"] . "'";
        }
        $columnCheck = "invoiceDate";
        if ($overdue) {
            $columnCheck = "invoiceDueDate";
        }
        $customer = $this->input->post("customerTo");
        $company = $this->get_group_company();
        $i = 1;
        $customerOR = '(';
        if (!empty($customer)) { /*generate the query according to selected customer*/
            foreach ($customer as $customer_val) {
                if ($i != 1) {
                    $customerOR .= ' OR ';
                }
                $customerOR .= "groupCustomerMasterID = '" . $customer_val . "' ";
                $i++;
            }
        }
        $customerOR .= ')';

        $fields = "";
        $fields2 = "";
        $fields3 = "";
        $fields4 = "";
        $fields5 = "";
        $fields6 = "";
        $fields7 = "";
        $having = array();
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) {
            foreach ($fieldNameChk as $val) {
                if ($val == 'transactionAmount') {
                    $fields .= 'srp_erp_customerinvoicemaster.transactionCurrency as ' . $val . 'currency,';
                    $fields .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields2 .= 'srp_erp_customerreceiptmaster.transactionCurrency as ' . $val . 'currency,';
                    $fields2 .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields7 = "ORDER BY transactionAmountCurrency";
                } else if ($val == 'companyReportingAmount') {
                    $fields .= 'srp_erp_customerinvoicemaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields2 .= 'srp_erp_customerreceiptmaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                } else if ($val == 'companyLocalAmount') {
                    $fields .= 'srp_erp_customerinvoicemaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields2 .= 'srp_erp_customerreceiptmaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                }
                $fields .= '(SUM(srp_erp_customerinvoicemaster.' . $val . ') - (IFNULL(SUM(pvd.' . $val . '),0)+IFNULL(SUM(cnd.' . $val . '),0)+IFNULL(SUM(ca.' . $val . '),0))) as ' . $val . ',';
                $fields2 .= '(SUM(srp_erp_customerreceiptdetail.' . $val . ') - IFNULL(SUM(avd.' . $val . '),0)) * -1 as ' . $val . ',';
                $fields3 .= 'a.' . $val . 'currency as ' . $val . 'currency,';
                $fields3 .= 'a.' . $val . 'DecimalPlaces,';
                $fields3 .= 'a.' . $val . ' as ' . $val . ',';
                $fields4 .= 'IFNULL(SUM(srp_erp_customerreceiptdetail.' . $val . '),0) as ' . $val . ',';
                $fields5 .= 'SUM(srp_erp_creditnotedetail.' . $val . ') as ' . $val . ',';
                $fields6 .= 'SUM(srp_erp_rvadvancematchdetails.' . $val . ') as ' . $val . ',';
                $having[] = $val . '!= -0 AND ' . $val . ' != 0';
            }
        }

        $result = $this->db->query("SELECT $fields3 a.invoiceAutoID,a.document,a.age,DATE_FORMAT(a.invoiceDueDate,'" . $this->format . "') as invoiceDueDate,a.customerAddress,a.customerName,a.customerSystemCode,a.comments,a.documentID,DATE_FORMAT(a.bookingDate,'" . $this->format . "') as bookingDate,a.GLSecondaryCode,a.GLDescription,a.bookingInvCode,a.customerID FROM 
((SELECT $fields srp_erp_customerinvoicemaster.invoiceAutoID,dc.document,cust.customerAddress1 as customerAddress,cust.groupCustomerName as customerName,cust.groupcustomerSystemCode as customerSystemCode,srp_erp_customerinvoicemaster.invoiceNarration as comments,srp_erp_customerinvoicemaster.documentID,srp_erp_customerinvoicemaster.invoiceDueDate as invoiceDueDate,srp_erp_customerinvoicemaster.invoiceDate as bookingDate,coa.GLSecondaryCode,coa.GLDescription,srp_erp_customerinvoicemaster.invoiceCode as bookingInvCode,`srp_erp_customerinvoicemaster`.`customerID` as customerID,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_customerinvoicemaster.`invoiceDueDate`) as age
FROM `srp_erp_customerinvoicemaster` 
 INNER JOIN ( SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode,customerAddress1 FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE $customerOR  AND srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . ") cust ON srp_erp_customerinvoicemaster.customerID = cust.customerMasterID  
 LEFT JOIN (SELECT document,documentID FROM srp_erp_documentcodemaster WHERE companyID IN ( " . join(",", $company) . ") GROUP BY documentID) dc ON (dc.documentID = srp_erp_customerinvoicemaster.documentID)
LEFT JOIN 
(
	SELECT
		$fields4 srp_erp_customerreceiptdetail.invoiceAutoID,srp_erp_customerreceiptdetail.receiptVoucherAutoID
	FROM
		srp_erp_customerreceiptdetail
		INNER JOIN `srp_erp_customerreceiptmaster` ON `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId` = `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` AND `srp_erp_customerreceiptmaster`.`approvedYN` = 1
	WHERE
		`srp_erp_customerreceiptdetail`.`companyID` IN ( " . join(",", $company) . ") AND srp_erp_customerreceiptmaster.RVDate <= '" . format_date($this->input->post("from")) . "'  GROUP BY srp_erp_customerreceiptdetail.invoiceAutoID
) pvd ON (
	pvd.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID`
)
LEFT JOIN
(
	SELECT
		$fields5 invoiceAutoID,srp_erp_creditnotedetail.creditNoteMasterAutoID
	FROM
		srp_erp_creditnotedetail
		INNER JOIN `srp_erp_creditnotemaster` ON `srp_erp_creditnotemaster`.`creditNoteMasterAutoID` = `srp_erp_creditnotedetail`.`creditNoteMasterAutoID` AND `srp_erp_creditnotemaster`.`approvedYN` = 1
	WHERE
		`srp_erp_creditnotedetail`.`companyID` IN ( " . join(",", $company) . ") AND srp_erp_creditnotemaster.creditNoteDate <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_creditnotedetail.invoiceAutoID
) cnd ON (
	cnd.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID`
)
LEFT JOIN( 
SELECT 
$fields6 srp_erp_rvadvancematchdetails.InvoiceAutoID,srp_erp_rvadvancematchdetails.receiptVoucherAutoID
	FROM srp_erp_rvadvancematchdetails 
	INNER JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematchdetails`.`matchID` = `srp_erp_rvadvancematch`.`matchID` AND `srp_erp_rvadvancematch`.`confirmedYN` = 1
	WHERE `srp_erp_rvadvancematchdetails`.`companyID` IN ( " . join(",", $company) . ") GROUP BY srp_erp_rvadvancematchdetails.InvoiceAutoID) 
	ca ON (
	ca.`InvoiceAutoID` = `srp_erp_customerinvoicemaster`.`InvoiceAutoID`
)
INNER JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID AND groupID = " . current_companyID() . ") coa ON srp_erp_customerinvoicemaster.customerReceivableAutoID = coa.chartofAccountID 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_customerinvoicemaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_customerinvoicemaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_customerinvoicemaster.transactionCurrencyID) 
WHERE `srp_erp_customerinvoicemaster`.`companyID` IN ( " . join(",", $company) . ") AND srp_erp_customerinvoicemaster.$columnCheck <= '" . format_date($this->input->post("from")) . "' AND `srp_erp_customerinvoicemaster`.`approvedYN` = 1 GROUP BY `srp_erp_customerinvoicemaster`.`invoiceAutoID` HAVING (" . join(' AND ', $having) . "))
 UNION ALL (
 SELECT $fields2 srp_erp_customerreceiptmaster.receiptVoucherAutoID as invoiceAutoID,dc.document,cust.customerAddress1 as customerAddress,cust.groupCustomerName as customerName,cust.groupcustomerSystemCode as customerSystemCode,'Advance' as comments,srp_erp_customerreceiptmaster.documentID,'-' as invoiceDueDate,srp_erp_customerreceiptmaster.RVDate as bookingDate,coa.GLSecondaryCode,coa.GLDescription,srp_erp_customerreceiptmaster.RVCode as bookingInvCode,`srp_erp_customerreceiptmaster`.`customerID` as customerID,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_customerreceiptmaster.`RVDate`) as age  
 FROM srp_erp_customerreceiptmaster 
INNER JOIN `srp_erp_customerreceiptdetail` ON `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` = `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId` AND `srp_erp_customerreceiptdetail`.`companyID`IN ( " . join(",", $company) . ") AND  srp_erp_customerreceiptdetail.type='Advance'
LEFT JOIN (SELECT $fields6 srp_erp_rvadvancematchdetails.receiptVoucherAutoID 
		FROM srp_erp_rvadvancematchdetails 
		INNER JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematch`.`matchID` = `srp_erp_rvadvancematchdetails`.`matchID` AND `srp_erp_rvadvancematch`.`confirmedYN` = 1
		WHERE `srp_erp_rvadvancematch`.`matchDate` <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_rvadvancematchdetails.receiptVoucherAutoID
		) avd ON (avd.receiptVoucherAutoID = `srp_erp_customerreceiptdetail`.`receiptVoucherAutoID`)
INNER JOIN ( SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode,customerAddress1 FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE $customerOR AND srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . ") cust ON srp_erp_customerreceiptmaster.customerID = cust.customerMasterID 
 INNER JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID AND groupID = " . current_companyID() . ") coa ON srp_erp_customerreceiptmaster.customerreceivableAutoID = coa.chartofAccountID 
LEFT JOIN (SELECT document,documentID FROM srp_erp_documentcodemaster WHERE companyID IN ( " . join(",", $company) . ") GROUP BY documentID) dc ON (dc.documentID = srp_erp_customerreceiptmaster.documentID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_customerreceiptmaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_customerreceiptmaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_customerreceiptmaster.transactionCurrencyID) 
WHERE `srp_erp_customerreceiptmaster`.`approvedYN` = 1 AND srp_erp_customerreceiptmaster.RVDate <= '" . format_date($this->input->post("from")) . "' AND srp_erp_customerreceiptdetail.invoiceAutoID IS NULL GROUP BY `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId`
 ) $fields7) as a $where $fields7")->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_accounts_receivable_customer_aging_summary_report()
    {
        $customer = $this->input->post("customerTo");
        $i = 1;
        $customerOR = '(';
        if (!empty($customer)) { /*generate the query according to selected customer*/
            foreach ($customer as $customer_val) {
                if ($i != 1) {
                    $customerOR .= ' OR ';
                }
                $customerOR .= "srp_erp_customermaster.customerAutoID = '" . $customer_val . "' ";
                $i++;
            }
        }
        $customerOR .= ')';
        $aging = array();
        $interval = $this->input->post("interval");
        $through = $this->input->post("through");
        $z = 1;
        for ($i = $interval; $i < $through; $z++) { /*calculate aging range*/
            if ($z == 1) {
                $aging[] = $z . "-" . $interval;
            } else {
                if (($i + $interval) > $through) {
                    $aging[] = ($i + 1) . "-" . ($through);
                    $i += $interval;
                } else {
                    $aging[] = ($i + 1) . "-" . ($i + $interval);
                    $i += $interval;
                }

            }
        }
        $aging[] = "> " . ($through);

        $fields = "";
        $fields3 = "";
        $fields4 = "";
        $fields5 = "";
        $fields6 = "";
        $fields2 = "";
        $fields8 = "";
        $fields9 = "";
        $having = array();
        $groupBy = array();
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            foreach ($fieldNameChk as $val) {
                if ($val == 'companyReportingAmount') {
                    $fields .= 'srp_erp_customerinvoicemaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields4 .= '(IFNULL(SUM(srp_erp_customerreceiptdetail.' . $val . '),0)) as ' . $val . ',';
                    $fields5 .= 'SUM(srp_erp_creditnotedetail.' . $val . ') as ' . $val . ',';
                    $fields2 .= 'srp_erp_customerreceiptmaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';

                    $fields8 .= 'srp_erp_creditnotemaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields8 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields8 .= '(SUM(srp_erp_creditnotedetail.' . $val . ') - IFNULL(SUM(rvd.' . $val . '),0)) * -1 as ' . $val . ',';
                } else if ($val == 'companyLocalAmount') {
                    $fields .= 'srp_erp_customerinvoicemaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields4 .= '(IFNULL(SUM(srp_erp_customerreceiptdetail.' . $val . '),0)) as ' . $val . ',';
                    $fields5 .= 'SUM(srp_erp_creditnotedetail.' . $val . ') as ' . $val . ',';
                    $fields2 .= 'srp_erp_customerreceiptmaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';

                    $fields8 .= 'srp_erp_creditnotemaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields8 .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields8 .= '(SUM(srp_erp_creditnotedetail.' . $val . ') - IFNULL(SUM(rvd.' . $val . '),0)) * -1 as ' . $val . ',';
                } else if ($val == 'customerCurrencyAmount') {
                    $fields .= 'srp_erp_customerinvoicemaster.customerCurrency as ' . $val . 'currency,';
                    $fields .= 'srp_erp_customerinvoicemaster.customerCurrencyDecimalPlaces as ' . $val . 'decimalPlace,';
                    $fields4 .= '(IFNULL(SUM(srp_erp_customerreceiptdetail.customerAmount),0)) as ' . $val . ',';
                    $fields5 .= 'SUM(srp_erp_creditnotedetail.customerAmount) as ' . $val . ',';
                    $fields2 .= 'srp_erp_customerreceiptmaster.customerCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $groupBy[] = $val . 'currency';
                } else if ($val == 'transactionAmount') {
                    $fields .= 'srp_erp_customerinvoicemaster.transactionCurrency as ' . $val . 'currency,';
                    $fields .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields4 .= '(IFNULL(SUM(srp_erp_customerreceiptdetail.' . $val . '),0)) as ' . $val . ',';
                    $fields5 .= 'SUM(srp_erp_creditnotedetail.' . $val . ') as ' . $val . ',';
                    $fields2 .= 'srp_erp_customerreceiptmaster.transactionCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';

                    $fields8 .= 'srp_erp_creditnotemaster.transactionCurrency as ' . $val . 'currency,';
                    $fields8 .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields8 .= '(SUM(srp_erp_creditnotedetail.' . $val . ') - IFNULL(SUM(rvd.' . $val . '),0)) * -1 as ' . $val . ',';
                    $groupBy[] = $val . 'currency';
                }
                $fields9 .= 'SUM(srp_erp_customerreceiptdetail.' . $val . ') as ' . $val . ',';
                $fields .= '(SUM(srp_erp_customerinvoicemaster.' . $val . ') - (IFNULL(SUM(pvd.' . $val . '),0)+IFNULL(SUM(cnd.' . $val . '),0)+IFNULL(SUM(ca.' . $val . '),0)))  as ' . $val . ',';
                $fields3 .= 'a.' . $val . 'currency as currency,';
                $fields3 .= 'a.' . $val . 'DecimalPlaces as DecimalPlaces,';
                $fields3 .= 'a.' . $val . ' as ' . $val . ',';
                $fields6 .= 'SUM(srp_erp_rvadvancematchdetails.' . $val . ') as ' . $val . ',';
                $fields2 .= '(IFNULL(SUM(srp_erp_customerreceiptdetail.' . $val . '),0) - IFNULL(SUM(avd.' . $val . '),0))*-1 as ' . $val . ',';
                $having[] = $val . '!= 0';
                $fields7 = $val . ' > 0';
                if (!empty($aging)) { /*calculate aging range in query*/
                    $count = count($aging);
                    $c = 1;
                    foreach ($aging as $val2) {
                        if ($count == $c) {
                            $fields3 .= "SUM(if(a.age > " . $through . ",a." . $val . ",0)) as `" . $val2 . "`,";
                        } else {
                            $list = explode("-", $val2);
                            $fields3 .= "SUM(if(a.age >= " . $list[0] . " AND a.age <= " . $list[1] . ",a." . $val . ",0)) as `" . $val2 . "`,";
                        }
                        $c++;
                    }
                }
                $fields3 .= "SUM(if(a.age <= 0,a." . $val . ",0)) as `current`,";
            }
        }
        $groupByExplode = "";
        if ($groupBy) {
            $groupByExplode = "," . join(',', $groupBy);
        }

        $result = $this->db->query("SELECT $fields3 a.customerName,a.customerSystemCode,a.comments,a.documentID,a.bookingDate,a.GLSecondaryCode,a.GLDescription,a.bookingInvCode,a.customerID FROM 
((SELECT $fields srp_erp_customermaster.customerName,srp_erp_customermaster.customerSystemCode,srp_erp_customerinvoicemaster.invoiceNarration as comments,srp_erp_customerinvoicemaster.documentID,srp_erp_customerinvoicemaster.invoiceDueDate as bookingDate,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,srp_erp_customerinvoicemaster.invoiceCode as bookingInvCode,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_customerinvoicemaster.`invoiceDueDate`) as age,`srp_erp_customerinvoicemaster`.`customerID` as customerID FROM `srp_erp_customerinvoicemaster` 
LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customerinvoicemaster`.`customerID` = `srp_erp_customermaster`.`customerAutoID` AND `srp_erp_customermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN 
(
	SELECT
		$fields4 srp_erp_customerreceiptdetail.invoiceAutoID,srp_erp_customerreceiptdetail.receiptVoucherAutoID
	FROM
	srp_erp_customerreceiptdetail
		INNER JOIN `srp_erp_customerreceiptmaster` ON `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId` = `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` AND `srp_erp_customerreceiptmaster`.`approvedYN` = 1 AND srp_erp_customerreceiptmaster.RVDate <= '" . format_date($this->input->post("from")) . "'
		WHERE `srp_erp_customerreceiptdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " GROUP BY srp_erp_customerreceiptdetail.invoiceAutoID
) pvd ON (
	pvd.`InvoiceAutoID` = `srp_erp_customerinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN
(
	SELECT
		$fields5 InvoiceAutoID,srp_erp_creditnotedetail.creditNoteMasterAutoID
	FROM
		srp_erp_creditnotedetail
		INNER JOIN `srp_erp_creditnotemaster` ON `srp_erp_creditnotemaster`.`creditNoteMasterAutoID` = `srp_erp_creditnotedetail`.`creditNoteMasterAutoID` AND `srp_erp_creditnotemaster`.`approvedYN` = 1
	WHERE
		`srp_erp_creditnotedetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_creditnotemaster.creditNoteDate <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_creditnotedetail.invoiceAutoID
) cnd ON (
	cnd.`InvoiceAutoID` = `srp_erp_customerinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN( 
SELECT 
$fields6 srp_erp_rvadvancematchdetails.InvoiceAutoID,srp_erp_rvadvancematchdetails.receiptVoucherAutoID
	FROM srp_erp_rvadvancematchdetails 
	INNER JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematchdetails`.`matchID` = `srp_erp_rvadvancematch`.`matchID` AND `srp_erp_rvadvancematch`.`confirmedYN` = 1
	WHERE `srp_erp_rvadvancematchdetails`.`companyID` = " . $this->common_data['company_data']['company_id'] . "  GROUP BY srp_erp_rvadvancematchdetails.InvoiceAutoID)
	ca ON (
	ca.`InvoiceAutoID` = `srp_erp_customerinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_customerinvoicemaster.customerReceivableAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_customerinvoicemaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_customerinvoicemaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_customerinvoicemaster.transactionCurrencyID) 
WHERE $customerOR AND `srp_erp_customerinvoicemaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_customerinvoicemaster.`invoiceDueDate` <= '" . format_date($this->input->post("from")) . "' AND `srp_erp_customerinvoicemaster`.`approvedYN` = 1 GROUP BY `srp_erp_customerinvoicemaster`.`customerID`,`srp_erp_customerinvoicemaster`.`invoiceDueDate` HAVING $fields7) 
UNION ALL (
 SELECT $fields2 srp_erp_customermaster.customerName,srp_erp_customermaster.customerSystemCode,'Advance' as comments,srp_erp_customerreceiptmaster.documentID,srp_erp_customerreceiptmaster.RVDate as bookingDate,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,srp_erp_customerreceiptmaster.RVCode as bookingInvCode,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_customerreceiptmaster.`RVDate`) as age,`srp_erp_customerreceiptmaster`.`customerID` as customerID 
 FROM srp_erp_customerreceiptmaster 
INNER JOIN `srp_erp_customerreceiptdetail` ON `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` = `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId` AND `srp_erp_customerreceiptdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND  srp_erp_customerreceiptdetail.type='Advance'
LEFT JOIN (SELECT $fields6 srp_erp_rvadvancematchdetails.receiptVoucherAutoID 
		FROM srp_erp_rvadvancematchdetails 
		INNER JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematch`.`matchID` = `srp_erp_rvadvancematchdetails`.`matchID` AND `srp_erp_rvadvancematch`.`confirmedYN` = 1
		WHERE `srp_erp_rvadvancematch`.`matchDate` <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_rvadvancematchdetails.receiptVoucherAutoID
		) avd ON (avd.receiptVoucherAutoID = `srp_erp_customerreceiptdetail`.`receiptVoucherAutoID`)
LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customerreceiptmaster`.`customerID` = `srp_erp_customermaster`.`customerAutoID` AND `srp_erp_customermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_customerreceiptmaster.customerreceivableAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN srp_erp_documentcodemaster ON srp_erp_documentcodemaster.documentID = srp_erp_customerreceiptmaster.documentID  AND srp_erp_documentcodemaster.companyID = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_customerreceiptmaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_customerreceiptmaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_customerreceiptmaster.transactionCurrencyID) 
WHERE $customerOR AND `srp_erp_customerreceiptmaster`.`approvedYN` = 1 AND srp_erp_customerreceiptmaster.RVDate <= '" . format_date($this->input->post("from")) . "' GROUP BY `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId`,srp_erp_customerreceiptmaster.`RVDate` HAVING (" . join(' AND ', $having) . "))

 UNION ALL
	(
		SELECT
			$fields8 srp_erp_customermaster.customerName,
			srp_erp_customermaster.customerSystemCode,
			srp_erp_creditnotemaster.comments,
			srp_erp_creditnotemaster.documentID,
			srp_erp_creditnotemaster.creditNoteDate AS bookingDate,
			srp_erp_chartofAccounts.GLSecondaryCode,
			srp_erp_chartofAccounts.GLDescription,
			srp_erp_creditnotemaster.creditNoteCode AS bookingInvCode,
			DATEDIFF(
				'" . format_date($this->input->post("from")) . "',
				srp_erp_creditnotemaster.`creditNoteDate`
			) AS age,
			`srp_erp_creditnotemaster`.`customerID` AS customerID
		FROM
			srp_erp_creditnotemaster
		INNER JOIN `srp_erp_creditnotedetail` ON `srp_erp_creditnotedetail`.`creditNoteMasterAutoID` = `srp_erp_creditnotemaster`.`creditNoteMasterAutoID`
		AND `srp_erp_creditnotedetail`.`InvoiceAutoID` IS NULL
		AND `srp_erp_creditnotedetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
		LEFT JOIN (
			SELECT
				$fields9 receiptVoucherAutoId
			FROM
				`srp_erp_customerreceiptdetail`
			WHERE
				`srp_erp_customerreceiptdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
			AND type = 'creditnote'
			GROUP BY
				receiptVoucherAutoId
		) rvd ON rvd.`receiptVoucherAutoId` = `srp_erp_creditnotemaster`.`creditNoteMasterAutoID`
		LEFT JOIN `srp_erp_customermaster` ON `srp_erp_creditnotemaster`.`customerID` = `srp_erp_customermaster`.`customerAutoID`
		AND `srp_erp_customermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
		LEFT JOIN srp_erp_chartofAccounts ON srp_erp_creditnotemaster.customerReceivableAutoID = srp_erp_chartofAccounts.GLAutoID
		AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
		LEFT JOIN (
			SELECT
				DecimalPlaces,
				currencyID
			FROM
				srp_erp_currencymaster
		) CR ON (
			CR.currencyID = srp_erp_creditnotemaster.companyReportingCurrencyID
		)
		LEFT JOIN (
			SELECT
				DecimalPlaces,
				currencyID
			FROM
				srp_erp_currencymaster
		) CL ON (
			CL.currencyID = srp_erp_creditnotemaster.companyLocalCurrencyID
		)
		LEFT JOIN (
			SELECT
				DecimalPlaces,
				currencyID
			FROM
				srp_erp_currencymaster
		) TC ON (
			TC.currencyID = srp_erp_creditnotemaster.transactionCurrencyID
		)
		WHERE
			$customerOR
		AND `srp_erp_creditnotemaster`.`approvedYN` = 1
		AND srp_erp_creditnotemaster.creditNoteDate <= '" . format_date($this->input->post("from")) . "'
		AND `srp_erp_creditnotemaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
		GROUP BY
			`srp_erp_creditnotemaster`.`customerID`,
			`srp_erp_creditnotemaster`.`creditNoteDate`
		HAVING
			(
				" . join(' AND ', $having) . "
			)
	)
) AS a
GROUP BY
	a.customerSystemCode $groupByExplode
 ")->result_array();
        //echo $this->db->last_query();
        return $result;

    }

    function get_accounts_receivable_customer_aging_detail_report()
    {
        $customer = $this->input->post("customerTo");
        $i = 1;
        $customerOR = '(';
        if (!empty($customer)) { /*generate the query according to selected customer*/
            foreach ($customer as $customer_val) {
                if ($i != 1) {
                    $customerOR .= ' OR ';
                }
                $customerOR .= "srp_erp_customermaster.customerAutoID = '" . $customer_val . "' ";
                $i++;
            }
        }
        $customerOR .= ')';
        $aging = array();
        $interval = $this->input->post("interval");
        $through = $this->input->post("through");
        $z = 1;
        for ($i = $interval; $i < $through; $z++) { /*calculate aging range*/
            if ($z == 1) {
                $aging[] = $z . "-" . $interval;
            } else {
                if (($i + $interval) > $through) {
                    $aging[] = ($i + 1) . "-" . ($through);
                    $i += $interval;
                } else {
                    $aging[] = ($i + 1) . "-" . ($i + $interval);
                    $i += $interval;
                }

            }
        }
        $aging[] = "> " . ($through);

        $fields = "";
        $fields3 = "";
        $fields4 = "";
        $fields5 = "";
        $fields6 = "";
        $fields2 = "";
        $fields8 = "";
        $fields9 = "";
        $having = array();
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) { /*generate the query according to selectd columns*/
            foreach ($fieldNameChk as $val) {
                if ($val == 'companyReportingAmount') {
                    $fields .= 'srp_erp_customerinvoicemaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields4 .= '(IFNULL(SUM(srp_erp_customerreceiptdetail.' . $val . '),0)) as ' . $val . ',';
                    $fields5 .= 'SUM(srp_erp_creditnotedetail.' . $val . ') as ' . $val . ',';
                    $fields2 .= 'srp_erp_customerreceiptmaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields8 .= 'srp_erp_creditnotemaster.companyReportingCurrency as ' . $val . 'currency,';
                    $fields8 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                } else if ($val == 'companyLocalAmount') {
                    $fields .= 'srp_erp_customerinvoicemaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields4 .= '(IFNULL(SUM(srp_erp_customerreceiptdetail.' . $val . '),0)) as ' . $val . ',';
                    $fields5 .= 'SUM(srp_erp_creditnotedetail.' . $val . ') as ' . $val . ',';
                    $fields2 .= 'srp_erp_customerreceiptmaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields8 .= 'srp_erp_creditnotemaster.companyLocalCurrency as ' . $val . 'currency,';
                    $fields8 .= 'CL.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                } else if ($val == 'customerCurrencyAmount') {
                    $fields .= 'srp_erp_customerinvoicemaster.customerCurrency as ' . $val . 'currency,';
                    $fields .= 'srp_erp_customerinvoicemaster.customerCurrencyDecimalPlaces as ' . $val . 'decimalPlace,';
                    $fields4 .= '(IFNULL(SUM(srp_erp_customerreceiptdetail.customerAmount),0)) as ' . $val . ',';
                    $fields5 .= 'SUM(srp_erp_creditnotedetail.customerAmount) as ' . $val . ',';
                    $fields2 .= 'srp_erp_customerreceiptmaster.customerCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                } else if ($val == 'transactionAmount') {
                    $fields .= 'srp_erp_customerinvoicemaster.transactionCurrency as ' . $val . 'currency,';
                    $fields .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields4 .= '(IFNULL(SUM(srp_erp_customerreceiptdetail.' . $val . '),0)) as ' . $val . ',';
                    $fields5 .= 'SUM(srp_erp_creditnotedetail.' . $val . ') as ' . $val . ',';
                    $fields2 .= 'srp_erp_customerreceiptmaster.transactionCurrency as ' . $val . 'currency,';
                    $fields2 .= 'CR.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                    $fields8 .= 'srp_erp_creditnotemaster.transactionCurrency as ' . $val . 'currency,';
                    $fields8 .= 'TC.DecimalPlaces as ' . $val . 'DecimalPlaces,';
                }
                $fields9 .= 'SUM(srp_erp_customerreceiptdetail.' . $val . ') as ' . $val . ',';
                $fields8 .= '(SUM(srp_erp_creditnotedetail.' . $val . ') - IFNULL(SUM(rvd.' . $val . '),0)) * -1 as ' . $val . ',';
                $fields .= '(SUM(srp_erp_customerinvoicemaster.' . $val . ') - (IFNULL(SUM(pvd.' . $val . '),0)+IFNULL(SUM(cnd.' . $val . '),0)+IFNULL(SUM(ca.' . $val . '),0)))  as ' . $val . ',';
                $fields3 .= 'a.' . $val . 'currency as currency,';
                $fields3 .= 'a.' . $val . 'DecimalPlaces as DecimalPlaces,';
                $fields3 .= 'a.' . $val . ' as ' . $val . ',';
                $fields6 .= 'SUM(srp_erp_rvadvancematchdetails.' . $val . ') as ' . $val . ',';
                $fields2 .= '(IFNULL(SUM(srp_erp_customerreceiptdetail.' . $val . '),0) - IFNULL(SUM(avd.' . $val . '),0))*-1 as ' . $val . ',';
                $having[] = $val . '!= 0';
                $fields7 = $val . ' > 0';
                if (!empty($aging)) { /*calculate aging range in query*/
                    $count = count($aging);
                    $c = 1;
                    foreach ($aging as $val2) {
                        if ($count == $c) {
                            $fields3 .= "SUM(if(a.age > " . $through . ",a." . $val . ",0)) as `" . $val2 . "`,";
                        } else {
                            $list = explode("-", $val2);
                            $fields3 .= "SUM(if(a.age >= " . $list[0] . " AND a.age <= " . $list[1] . ",a." . $val . ",0)) as `" . $val2 . "`,";
                        }
                        $c++;
                    }
                }
                $fields3 .= "SUM(if(a.age <= 0,a." . $val . ",0)) as `current`,";
            }
        }

        $result = $this->db->query("SELECT $fields3 a.invoiceAutoID,DATE_FORMAT(a.documentDate,'" . $this->format . "') as documentDate,a.documentCode,a.documentID,a.customerName,a.customerSystemCode,a.comments,a.GLSecondaryCode,a.GLDescription FROM 
((SELECT $fields srp_erp_customerinvoicemaster.invoiceAutoID,srp_erp_customerinvoicemaster.documentID as documentID,srp_erp_customerinvoicemaster.invoiceCode as documentCode,srp_erp_customerinvoicemaster.invoiceDueDate as documentDate,srp_erp_customermaster.customerName,srp_erp_customermaster.customerSystemCode,srp_erp_customerinvoicemaster.invoiceNarration as comments,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_customerinvoicemaster.`invoiceDueDate`) as age FROM `srp_erp_customerinvoicemaster` 
LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customerinvoicemaster`.`customerID` = `srp_erp_customermaster`.`customerAutoID` AND `srp_erp_customermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN 
(
	SELECT
		$fields4 srp_erp_customerreceiptdetail.invoiceAutoID,srp_erp_customerreceiptdetail.receiptVoucherAutoID
	FROM
	srp_erp_customerreceiptdetail
		INNER JOIN `srp_erp_customerreceiptmaster` ON `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId` = `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` 
		AND `srp_erp_customerreceiptmaster`.`approvedYN` = 1 AND srp_erp_customerreceiptmaster.RVDate <= '" . format_date($this->input->post("from")) . "'
		WHERE `srp_erp_customerreceiptdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " GROUP BY srp_erp_customerreceiptdetail.invoiceAutoID
) pvd ON (
	pvd.`InvoiceAutoID` = `srp_erp_customerinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN
(
	SELECT
		$fields5 InvoiceAutoID,srp_erp_creditnotedetail.creditNoteMasterAutoID
	FROM
		srp_erp_creditnotedetail
		INNER JOIN `srp_erp_creditnotemaster` ON `srp_erp_creditnotemaster`.`creditNoteMasterAutoID` = `srp_erp_creditnotedetail`.`creditNoteMasterAutoID` AND `srp_erp_creditnotemaster`.`approvedYN` = 1
	WHERE
		`srp_erp_creditnotedetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_creditnotemaster.creditNoteDate <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_creditnotedetail.invoiceAutoID 
) cnd ON (
	cnd.`InvoiceAutoID` = `srp_erp_customerinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN( 
SELECT 
$fields6 srp_erp_rvadvancematchdetails.InvoiceAutoID,srp_erp_rvadvancematchdetails.receiptVoucherAutoID
	FROM srp_erp_rvadvancematchdetails 
	INNER JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematchdetails`.`matchID` = `srp_erp_rvadvancematch`.`matchID` AND `srp_erp_rvadvancematch`.`confirmedYN` = 1
	WHERE `srp_erp_rvadvancematchdetails`.`companyID` = " . $this->common_data['company_data']['company_id'] . " GROUP BY srp_erp_rvadvancematchdetails.InvoiceAutoID)
	ca ON (
	ca.`InvoiceAutoID` = `srp_erp_customerinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_customerinvoicemaster.customerReceivableAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_customerinvoicemaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_customerinvoicemaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_customerinvoicemaster.transactionCurrencyID) 
WHERE $customerOR AND `srp_erp_customerinvoicemaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_customerinvoicemaster.`invoiceDueDate` <= '" . format_date($this->input->post("from")) . "' AND `srp_erp_customerinvoicemaster`.`approvedYN` = 1 GROUP BY `srp_erp_customerinvoicemaster`.`invoiceAutoID`,`srp_erp_customerinvoicemaster`.`invoiceDueDate` HAVING $fields7)
UNION ALL (
 SELECT $fields2 srp_erp_customerreceiptmaster.receiptVoucherAutoId as invoiceAutoID,srp_erp_customerreceiptmaster.documentID as documentID,srp_erp_customerreceiptmaster.RVCode as documentCode,srp_erp_customerreceiptmaster.RVDate as documentDate,srp_erp_customermaster.customerName,srp_erp_customermaster.customerSystemCode,'Advance' as comments,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_customerreceiptmaster.`RVDate`) as age
 FROM srp_erp_customerreceiptmaster 
INNER JOIN `srp_erp_customerreceiptdetail` ON `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` = `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId` AND `srp_erp_customerreceiptdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND  srp_erp_customerreceiptdetail.type='Advance'
LEFT JOIN (SELECT $fields6 srp_erp_rvadvancematchdetails.receiptVoucherAutoID 
		FROM srp_erp_rvadvancematchdetails 
		INNER JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematch`.`matchID` = `srp_erp_rvadvancematchdetails`.`matchID` AND `srp_erp_rvadvancematch`.`confirmedYN` = 1
		WHERE `srp_erp_rvadvancematch`.`matchDate` <= '" . format_date($this->input->post("from")) . "' GROUP BY srp_erp_rvadvancematchdetails.receiptVoucherAutoID
		) avd ON (avd.receiptVoucherAutoID = `srp_erp_customerreceiptdetail`.`receiptVoucherAutoID`)
LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customerreceiptmaster`.`customerID` = `srp_erp_customermaster`.`customerAutoID` AND `srp_erp_customermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_customerreceiptmaster.customerreceivableAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN srp_erp_documentcodemaster ON srp_erp_documentcodemaster.documentID = srp_erp_customerreceiptmaster.documentID  AND srp_erp_documentcodemaster.companyID = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_customerreceiptmaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_customerreceiptmaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_customerreceiptmaster.transactionCurrencyID) 
WHERE $customerOR AND `srp_erp_customerreceiptmaster`.`approvedYN` = 1 AND srp_erp_customerreceiptmaster.RVDate <= '" . format_date($this->input->post("from")) . "' GROUP BY `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId`,srp_erp_customerreceiptmaster.`RVDate` HAVING (" . join(' AND ', $having) . "))

UNION ALL (

SELECT $fields8
`srp_erp_creditnotemaster`.`creditNoteMasterAutoID` as invoiceAutoID,srp_erp_creditnotemaster.documentID AS documentID,
srp_erp_creditnotemaster.creditNoteCode AS documentCode,
					srp_erp_creditnotemaster.creditNoteDate AS documentDate,srp_erp_customermaster.customerName,srp_erp_customermaster.customerSystemCode,srp_erp_creditnotemaster.comments,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_creditnotemaster.creditNoteDate) as age
  FROM srp_erp_creditnotemaster
INNER JOIN `srp_erp_creditnotedetail` ON `srp_erp_creditnotedetail`.`creditNoteMasterAutoID` = `srp_erp_creditnotemaster`.`creditNoteMasterAutoID` AND `srp_erp_creditnotedetail`.`InvoiceAutoID` IS NULL AND `srp_erp_creditnotedetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT $fields9 receiptVoucherAutoId FROM `srp_erp_customerreceiptdetail` WHERE `srp_erp_customerreceiptdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND type='creditnote' GROUP BY receiptVoucherAutoId) rvd ON rvd.`receiptVoucherAutoId` = `srp_erp_creditnotemaster`.`creditNoteMasterAutoID`
LEFT JOIN `srp_erp_customermaster` ON `srp_erp_creditnotemaster`.`customerID` = `srp_erp_customermaster`.`customerAutoID` AND `srp_erp_customermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_creditnotemaster.customerReceivableAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_creditnotemaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_creditnotemaster.companyLocalCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_creditnotemaster.transactionCurrencyID)
WHERE $customerOR AND `srp_erp_creditnotemaster`.`approvedYN` = 1 AND srp_erp_creditnotemaster.creditNoteDate <= '" . format_date($this->input->post("from")) . "' AND `srp_erp_creditnotemaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " GROUP BY `srp_erp_creditnotemaster`.`creditNoteMasterAutoID`,`srp_erp_creditnotemaster`.`creditNoteDate` HAVING (" . join(' AND ', $having) . "))
) as a GROUP BY a.documentCode
")->result_array();
        //echo $this->db->last_query();
        return $result;

    }

    function get_finance_report_drilldown($fromTo = false, $segment = false, $financialBeginingDate)
    {
        $dateQuery = "";
        $dateQuery2 = "";
        $segmentQuery = "";
        if ($segment) {
            $segment = $this->input->post("segment");
            $segmentQuery = "AND srp_erp_generalledger.segmentID IN(" . join(',', $segment) . ")";
        }
        $glcode = $this->input->post("glCode");
        $feilds = "";
        $feilds2 = "";
        $feilds3 = "";
        $fieldNameChk = $this->input->post("currency");
        if (!empty($fieldNameChk)) { /*generate the query according to selectd columns*/
            $feilds[] = "srp_erp_generalledger." . $fieldNameChk . " as " . $fieldNameChk;
            $feilds2[] = "a." . $fieldNameChk;
            $feilds3[] = "SUM(" . $fieldNameChk . ") as " . $fieldNameChk;
            if ($fieldNameChk == "companyReportingAmount") {
                $feilds[] = "CR.DecimalPlaces as " . $fieldNameChk . "DecimalPlaces";
                $feilds3[] = "CR.DecimalPlaces as " . $fieldNameChk . "DecimalPlaces";
            }
            if ($fieldNameChk == "companyLocalAmount") {
                $feilds[] = "CL.DecimalPlaces as " . $fieldNameChk . "DecimalPlaces";
                $feilds3[] = "CL.DecimalPlaces as " . $fieldNameChk . "DecimalPlaces";
            }
            $feilds2[] = "a." . $fieldNameChk . "DecimalPlaces";
        }
        $feilds = join(',', $feilds);
        $feilds2 = join(',', $feilds2);
        $feilds3 = join(',', $feilds3);
        $sql = "";
        if ($this->input->post("masterCategory") == "BS") {
            if (is_null($this->input->post("month")) || empty($this->input->post("month"))) {
                if ($fromTo) {
                    $dateQuery = "srp_erp_generalledger.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "'";
                } else {
                    $dateQuery = "srp_erp_generalledger.documentDate BETWEEN '" . $financialBeginingDate["beginingDate"] . "' AND '" . format_date($this->input->post("from")) . "'";
                }
                $dateQuery2 = "srp_erp_generalledger.documentDate < '" . $financialBeginingDate["beginingDate"] . "'";
            } else {
                if ($fromTo) {
                    $dateQuery = "DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') BETWEEN '" . date('Y-m', strtotime($this->input->post("from"))) . "' AND '" . date('Y-m', strtotime($this->input->post("to"))) . "'";
                } else {
                    if (date('Y-m', strtotime($this->input->post("from"))) == $this->input->post("month")) {
                        $dateQuery = "srp_erp_generalledger.documentDate BETWEEN '" . $this->input->post("month") . "-01" . "' AND '" . $this->input->post("month") . "-" . date('d', strtotime($this->input->post("from"))) . "'";
                    } else {
                        $dateQuery = "DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') = '" . $this->input->post("month") . "'";
                    }
                }
                $dateQuery2 = "DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') < '" . $this->input->post("month") . "'";
            }

            $sql = "SELECT $feilds2,a.documentCode,a.documentMasterAutoID,a.approvedbyEmpName,a.documentSystemCode,DATE_FORMAT(a.documentDate,'" . $this->format . "') as documentDate,a.documentNarration,a.document,a.GLSecondaryCode,a.partySystemCode,a.segmentID,a.GLDescription,a.masterCategory,a.GLAutoID, a.documentDate as nonFormatDate FROM ((SELECT 
  $feilds,srp_erp_generalledger.documentCode,srp_erp_generalledger.documentMasterAutoID,srp_erp_generalledger.approvedbyEmpName,srp_erp_generalledger.documentSystemCode,srp_erp_generalledger.documentDate,srp_erp_generalledger.documentNarration,dc.document,srp_erp_chartofaccounts.GLSecondaryCode,IF(cust.customerSystemCode IS NOT NULL,cust.customerName,IF (supp.supplierSystemCode IS NOT NULL,supp.supplierName,'-')) AS partySystemCode,CONCAT(srp_erp_segment.segmentCode,'-',srp_erp_segment.description) as segmentID,srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.masterCategory,srp_erp_generalledger.GLAutoID
 FROM srp_erp_generalledger 
 INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
 LEFT JOIN srp_erp_segment ON srp_erp_generalledger.segmentID = srp_erp_segment.segmentID AND srp_erp_segment.companyID = " . $this->common_data['company_data']['company_id'] . "
 LEFT JOIN (SELECT * FROM srp_erp_customermaster WHERE companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY customerAutoID) cust ON srp_erp_generalledger.partyAutoID = cust.customerAutoID AND srp_erp_generalledger.partyType = 'CUS'
 LEFT JOIN (SELECT * FROM srp_erp_suppliermaster WHERE companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY supplierAutoID) supp ON srp_erp_generalledger.partyAutoID = supp.supplierAutoID AND srp_erp_generalledger.partyType = 'SUP'
 LEFT JOIN (SELECT document,documentID FROM srp_erp_documentcodemaster WHERE companyID = " . $this->common_data['company_data']['company_id'] . ") dc ON (dc.documentID = srp_erp_generalledger.documentCode)
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
 WHERE srp_erp_generalledger.GLAutoID = '$glcode' $segmentQuery AND $dateQuery AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . "
 ORDER BY srp_erp_generalledger.documentType,srp_erp_generalledger.documentDate ASC) 
 UNION ALL
 (SELECT $feilds3,srp_erp_generalledger.documentCode,srp_erp_generalledger.documentMasterAutoID,'-' as approvedbyEmpName,'-' as documentSystemCode,'-' as documentDate,'CF Balance' as documentNarration,'-' AS document,'-' AS GLSecondaryCode,'-' AS partySystemCode,'-' AS  segmentID,srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.masterCategory,srp_erp_generalledger.GLAutoID
 FROM srp_erp_generalledger 
 INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . " 
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
 WHERE srp_erp_generalledger.GLAutoID = '$glcode' $segmentQuery AND srp_erp_chartofaccounts.masterCategory = 'BS' AND $dateQuery2 AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY srp_erp_generalledger.GLAutoID )) as a ORDER BY nonFormatDate ASC";
        } elseif ($this->input->post("masterCategory") == "PL") {
            if (is_null($this->input->post("month")) || empty($this->input->post("month"))) {
                if ($fromTo) {
                    $dateQuery = "srp_erp_generalledger.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "'";
                } else {
                    $dateQuery = "srp_erp_generalledger.documentDate BETWEEN '" . $financialBeginingDate["beginingDate"] . "' AND '" . format_date($this->input->post("from")) . "'";
                }
            } else {
                if ($fromTo) {
                    $dateQuery = "DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') = '" . $this->input->post("month") . "'";
                } else {
                    $dateQuery = "DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') = '" . $this->input->post("month") . "'";
                }
            }
            $sql = "SELECT $feilds2,a.documentCode,a.documentMasterAutoID,a.approvedbyEmpName,a.documentSystemCode,a.documentDate,a.documentNarration,a.document,a.GLSecondaryCode,a.partySystemCode,a.segmentID,a.GLDescription,a.masterCategory,a.GLAutoID,a.documentDate as nonFormatDate FROM ((SELECT 
  $feilds,srp_erp_generalledger.documentCode,srp_erp_generalledger.documentMasterAutoID,srp_erp_generalledger.approvedbyEmpName,srp_erp_generalledger.documentSystemCode,srp_erp_generalledger.documentDate,srp_erp_generalledger.documentNarration,srp_erp_documentcodemaster.document,srp_erp_chartofaccounts.GLSecondaryCode,IF(srp_erp_customermaster.customerSystemCode IS NOT NULL,srp_erp_customermaster.customerName,IF (srp_erp_suppliermaster.supplierSystemCode IS NOT NULL,srp_erp_suppliermaster.supplierName,'-')) AS partySystemCode,CONCAT(srp_erp_segment.segmentCode,'-',srp_erp_segment.description) as segmentID,srp_erp_chartofaccounts.GLDescription,srp_erp_chartofaccounts.masterCategory,srp_erp_generalledger.GLAutoID
 FROM srp_erp_generalledger 
 INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
 LEFT JOIN srp_erp_segment ON srp_erp_generalledger.segmentID = srp_erp_segment.segmentID AND srp_erp_segment.companyID = " . $this->common_data['company_data']['company_id'] . "
 LEFT JOIN srp_erp_customermaster ON srp_erp_generalledger.partyAutoID = srp_erp_customermaster.customerAutoID AND srp_erp_customermaster.companyID = " . $this->common_data['company_data']['company_id'] . "  AND srp_erp_generalledger.partyType = 'CUS'
 LEFT JOIN srp_erp_suppliermaster ON srp_erp_generalledger.partyAutoID = srp_erp_suppliermaster.supplierAutoID AND srp_erp_suppliermaster.companyID = " . $this->common_data['company_data']['company_id'] . "  AND srp_erp_generalledger.partyType = 'SUP'
 LEFT JOIN srp_erp_documentcodemaster ON srp_erp_documentcodemaster.documentID = srp_erp_generalledger.documentCode AND srp_erp_documentcodemaster.companyID = " . $this->common_data['company_data']['company_id'] . "
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
 WHERE srp_erp_generalledger.GLAutoID = '$glcode' $segmentQuery AND $dateQuery AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " 
 ORDER BY srp_erp_generalledger.documentType,srp_erp_generalledger.documentDate ASC)) as a ORDER BY nonFormatDate ASC";
        }
        $result = $this->db->query($sql)->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    function get_finance_report_group_drilldown($fromTo = false, $segment = false, $financialBeginingDate)
    {
        $dateQuery = "";
        $dateQuery2 = "";
        $company = $this->get_group_company();
        $segmentQuery = "";
        if ($segment) {
            $segment = $this->input->post("segment");
            $segmentQuery = "AND srp_erp_groupsegment.segmentID IN(" . join(',', $segment) . ")";
        }
        //$glcode = $this->input->post("glCode");
        $glcode = $this->get_group_chartofaccount();
        $feilds = "";
        $feilds2 = "";
        $feilds3 = "";
        $fieldNameChk = $this->input->post("currency");
        if (!empty($fieldNameChk)) { /*generate the query according to selectd columns*/
            $feilds[] = "srp_erp_generalledger." . $fieldNameChk . " as " . $fieldNameChk;
            $feilds2[] = "a." . $fieldNameChk;
            $feilds3[] = "SUM(" . $fieldNameChk . ") as " . $fieldNameChk;
            if ($fieldNameChk == "companyReportingAmount") {
                $feilds[] = "CR.DecimalPlaces as " . $fieldNameChk . "DecimalPlaces";
                $feilds3[] = "CR.DecimalPlaces as " . $fieldNameChk . "DecimalPlaces";
            }
            if ($fieldNameChk == "companyLocalAmount") {
                $feilds[] = "CL.DecimalPlaces as " . $fieldNameChk . "DecimalPlaces";
                $feilds3[] = "CL.DecimalPlaces as " . $fieldNameChk . "DecimalPlaces";
            }
            $feilds2[] = "a." . $fieldNameChk . "DecimalPlaces";
        }
        $feilds = join(',', $feilds);
        $feilds2 = join(',', $feilds2);
        $feilds3 = join(',', $feilds3);
        $sql = "";
        if ($this->input->post("masterCategory") == "BS") {
            if (is_null($this->input->post("month")) || empty($this->input->post("month"))) {
                if ($fromTo) {
                    $dateQuery = "srp_erp_generalledger.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "'";
                } else {
                    $dateQuery = "srp_erp_generalledger.documentDate BETWEEN '" . $financialBeginingDate["beginingDate"] . "' AND '" . format_date($this->input->post("from")) . "'";
                }
                $dateQuery2 = "srp_erp_generalledger.documentDate < '" . $financialBeginingDate["beginingDate"] . "'";
            } else {
                if ($fromTo) {
                    $dateQuery = "DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') BETWEEN '" . date('Y-m', strtotime($this->input->post("from"))) . "' AND '" . date('Y-m', strtotime($this->input->post("to"))) . "'";
                } else {
                    if (date('Y-m', strtotime($this->input->post("from"))) == $this->input->post("month")) {
                        $dateQuery = "srp_erp_generalledger.documentDate BETWEEN '" . $this->input->post("month") . "-01" . "' AND '" . $this->input->post("month") . "-" . date('d', strtotime($this->input->post("from"))) . "'";
                    } else {
                        $dateQuery = "DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') = '" . $this->input->post("month") . "'";
                    }
                }
                $dateQuery2 = "DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') < '" . $this->input->post("month") . "'";
            }

            $sql = "SELECT $feilds2,a.documentCode,a.documentMasterAutoID,a.approvedbyEmpName,a.documentSystemCode,DATE_FORMAT(a.documentDate,'" . $this->format . "') as documentDate,a.documentNarration,a.document,a.GLSecondaryCode,a.partySystemCode,a.segmentID,a.GLDescription,a.masterCategory,a.GLAutoID FROM ((SELECT 
  $feilds,srp_erp_generalledger.documentCode,srp_erp_generalledger.documentMasterAutoID,srp_erp_generalledger.approvedbyEmpName,srp_erp_generalledger.documentSystemCode,srp_erp_generalledger.documentDate,srp_erp_generalledger.documentNarration,dc.document,coa.GLSecondaryCode,IF(cust.groupcustomerSystemCode IS NOT NULL,cust.groupCustomerName,IF (supp.groupSupplierSystemCode IS NOT NULL,supp.groupSupplierName,'-')) AS partySystemCode,CONCAT(seg.segmentCode,'-',seg.description) as segmentID,coa.GLDescription,coa.masterCategory,srp_erp_generalledger.GLAutoID
 FROM srp_erp_generalledger 
 INNER JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription,masterCategory FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID AND groupID = " . current_companyID() . ") coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID 
 LEFT JOIN (SELECT srp_erp_groupsegment.*,srp_erp_groupsegmentdetails.segmentID as groupSegmentID FROM srp_erp_groupsegment INNER JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID WHERE groupID = " . current_companyID() . " $segmentQuery) seg ON srp_erp_generalledger.segmentID = seg.groupSegmentID
 LEFT JOIN (SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . " GROUP BY customerMasterID) cust ON srp_erp_generalledger.partyAutoID = cust.customerMasterID AND srp_erp_generalledger.partyType = 'CUS'
 LEFT JOIN (SELECT groupSupplierAutoID,groupSupplierName,SupplierMasterID,groupSupplierSystemCode FROM srp_erp_groupsuppliermaster INNER JOIN srp_erp_groupsupplierdetails ON srp_erp_groupsuppliermaster.groupSupplierAutoID = srp_erp_groupsupplierdetails.groupSupplierMasterID WHERE srp_erp_groupsupplierdetails.companygroupID = " . current_companyID() . " GROUP BY SupplierMasterID) supp ON srp_erp_generalledger.partyAutoID = supp.SupplierMasterID AND srp_erp_generalledger.partyType = 'SUP'
 LEFT JOIN (SELECT document,documentID FROM srp_erp_documentcodemaster WHERE companyID IN (" .join(',',$company) . ") GROUP BY documentID) dc ON (dc.documentID = srp_erp_generalledger.documentCode)
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
 WHERE srp_erp_generalledger.GLAutoID IN(".join(',',$glcode).") AND $dateQuery AND srp_erp_generalledger.companyID IN (" .join(',',$company) . ")
 ORDER BY srp_erp_generalledger.documentType,srp_erp_generalledger.documentDate ASC) 
 UNION ALL
 (SELECT $feilds3,srp_erp_generalledger.documentCode,srp_erp_generalledger.documentMasterAutoID,'-' as approvedbyEmpName,'-' as documentSystemCode,'-' as documentDate,'CF Balance' as documentNarration,'-' AS document,'-' AS GLSecondaryCode,'-' AS partySystemCode,'-' AS  segmentID,coa.GLDescription,coa.masterCategory,srp_erp_generalledger.GLAutoID
 FROM srp_erp_generalledger 
INNER JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription,masterCategory,GLAutoID FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID WHERE groupID = " . current_companyID() . " AND masterCategory = 'BS') coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID
LEFT JOIN (SELECT srp_erp_groupsegment.*,srp_erp_groupsegmentdetails.segmentID as groupSegmentID FROM srp_erp_groupsegment INNER JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID WHERE groupID = " . current_companyID() . " $segmentQuery) seg ON srp_erp_generalledger.segmentID = seg.groupSegmentID
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
 WHERE srp_erp_generalledger.GLAutoID IN(".join(',',$glcode).") AND $dateQuery2 AND srp_erp_generalledger.companyID IN (" .join(',',$company) . ") GROUP BY coa.GLAutoID )) as a ORDER BY documentDate,GLDescription DESC";
        } elseif ($this->input->post("masterCategory") == "PL") {
            if (is_null($this->input->post("month")) || empty($this->input->post("month"))) {
                if ($fromTo) {
                    $dateQuery = "srp_erp_generalledger.documentDate BETWEEN '" . format_date($this->input->post("from")) . "' AND '" . format_date($this->input->post("to")) . "'";
                } else {
                    $dateQuery = "srp_erp_generalledger.documentDate BETWEEN '" . $financialBeginingDate["beginingDate"] . "' AND '" . format_date($this->input->post("from")) . "'";
                }
            } else {
                if ($fromTo) {
                    $dateQuery = "DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') = '" . $this->input->post("month") . "'";
                } else {
                    $dateQuery = "DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') = '" . $this->input->post("month") . "'";
                }
            }
            $sql = "SELECT $feilds2,a.documentCode,a.documentMasterAutoID,a.approvedbyEmpName,a.documentSystemCode,a.documentDate,a.documentNarration,a.document,a.GLSecondaryCode,a.partySystemCode,a.segmentID,a.GLDescription,a.masterCategory,a.GLAutoID FROM ((SELECT 
  $feilds,srp_erp_generalledger.documentCode,srp_erp_generalledger.documentMasterAutoID,srp_erp_generalledger.approvedbyEmpName,srp_erp_generalledger.documentSystemCode,srp_erp_generalledger.documentDate,srp_erp_generalledger.documentNarration,dc.document,coa.GLSecondaryCode,IF(cust.groupcustomerSystemCode IS NOT NULL,cust.groupCustomerName,IF (supp.groupSupplierSystemCode IS NOT NULL,supp.groupSupplierName,'-')) AS partySystemCode,CONCAT(seg.segmentCode,'-',seg.description) as segmentID,coa.GLDescription,coa.masterCategory,srp_erp_generalledger.GLAutoID
 FROM srp_erp_generalledger 
 INNER JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription,masterCategory FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID AND groupID = " . current_companyID() . ") coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID 
 LEFT JOIN (SELECT srp_erp_groupsegment.*,srp_erp_groupsegmentdetails.segmentID as groupSegmentID FROM srp_erp_groupsegment INNER JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID WHERE groupID = " . current_companyID() . " $segmentQuery) seg ON srp_erp_generalledger.segmentID = seg.groupSegmentID
 LEFT JOIN (SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . " GROUP BY customerMasterID) cust ON srp_erp_generalledger.partyAutoID = cust.customerMasterID AND srp_erp_generalledger.partyType = 'CUS'
 LEFT JOIN (SELECT groupSupplierAutoID,groupSupplierName,SupplierMasterID,groupSupplierSystemCode FROM srp_erp_groupsuppliermaster INNER JOIN srp_erp_groupsupplierdetails ON srp_erp_groupsuppliermaster.groupSupplierAutoID = srp_erp_groupsupplierdetails.groupSupplierMasterID WHERE srp_erp_groupsupplierdetails.companygroupID = " . current_companyID() . " GROUP BY SupplierMasterID) supp ON srp_erp_generalledger.partyAutoID = supp.SupplierMasterID AND srp_erp_generalledger.partyType = 'SUP'
 LEFT JOIN (SELECT document,documentID FROM srp_erp_documentcodemaster WHERE companyID IN (" .join(',',$company) . ") GROUP BY documentID) dc ON (dc.documentID = srp_erp_generalledger.documentCode)
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
 WHERE srp_erp_generalledger.GLAutoID IN(".join(',',$glcode).") AND $dateQuery AND srp_erp_generalledger.companyID IN (" .join(',',$company) . ")
 ORDER BY srp_erp_generalledger.documentType,srp_erp_generalledger.documentDate ASC)) as a ORDER BY documentDate,GLDescription";
        }
        $result = $this->db->query($sql)->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    function get_accounts_payable_report_drilldown($fromTo = false, $financialBeginingDate)
    {
        $vendor = $this->input->post("supplierID");
        $age = $this->input->post("age");
        $list = explode("-", $age);
        $finalCheck = "";
        if ($this->input->post('through') == $age) {
            $finalCheck = "a.age > " . $age;
        } else {
            $finalCheck = "a.age >= " . $list[0] . " AND a.age <= " . $list[1];
        }

        $fields = "";
        $fields2 = "";
        $fields3 = "";
        $fields4 = "";
        $fields5 = "";
        $fields7 = "";
        $having = array();
        $fieldNameChk = $this->input->post("currency");
        if (!empty($fieldNameChk)) { /*generate the query according to selectd columns*/
            if ($fieldNameChk == 'transactionAmount') {
                $fields .= 'srp_erp_paysupplierinvoicemaster.transactionCurrency as ' . $fieldNameChk . 'currency,';
                $fields .= 'TC.DecimalPlaces as ' . $fieldNameChk . 'DecimalPlaces,';
                $fields2 .= 'srp_erp_paymentvoucherdetail.transactionCurrency as ' . $fieldNameChk . 'currency,';
                $fields2 .= 'TC.DecimalPlaces as ' . $fieldNameChk . 'DecimalPlaces,';
                $fields2 .= 'SUM(srp_erp_paymentvoucherdetail.' . $fieldNameChk . ') as ' . $fieldNameChk . ',';
                $fields4 .= 'SUM(srp_erp_paymentvoucherdetail.' . $fieldNameChk . ') as ' . $fieldNameChk . ',';
                $fields5 .= 'SUM(srp_erp_debitnotedetail.' . $fieldNameChk . ') as ' . $fieldNameChk . ',';
                $fields7 .= 'srp_erp_debitnotemaster.transactionCurrency as ' . $fieldNameChk . 'currency,';
                $fields7 .= 'TC.DecimalPlaces as ' . $fieldNameChk . 'DecimalPlaces,';
            } else if ($fieldNameChk == 'companyReportingAmount') {
                $fields .= 'srp_erp_paysupplierinvoicemaster.companyReportingCurrency as ' . $fieldNameChk . 'currency,';
                $fields .= 'CR.DecimalPlaces as ' . $fieldNameChk . 'DecimalPlaces,';
                $fields2 .= 'srp_erp_paymentvoucherdetail.companyReportingCurrency as ' . $fieldNameChk . 'currency,';
                $fields2 .= 'CR.DecimalPlaces as ' . $fieldNameChk . 'DecimalPlaces,';
                $fields2 .= 'SUM(srp_erp_paymentvoucherdetail.' . $fieldNameChk . ') as ' . $fieldNameChk . ',';
                $fields4 .= 'SUM(srp_erp_paymentvoucherdetail.' . $fieldNameChk . ') as ' . $fieldNameChk . ',';
                $fields5 .= 'SUM(srp_erp_debitnotedetail.' . $fieldNameChk . ') as ' . $fieldNameChk . ',';
                $fields7 .= 'srp_erp_debitnotemaster.companyReportingCurrency as ' . $fieldNameChk . 'currency,';
                $fields7 .= 'CR.DecimalPlaces as ' . $fieldNameChk . 'DecimalPlaces,';
            } else if ($fieldNameChk == 'companyLocalAmount') {
                $fields .= 'srp_erp_paysupplierinvoicemaster.companyLocalCurrency as ' . $fieldNameChk . 'currency,';
                $fields .= 'CL.DecimalPlaces as ' . $fieldNameChk . 'DecimalPlaces,';
                $fields2 .= 'srp_erp_paymentvoucherdetail.companyLocalCurrency as ' . $fieldNameChk . 'currency,';
                $fields .= 'CL.DecimalPlaces as ' . $fieldNameChk . 'DecimalPlaces,';
                $fields2 .= 'SUM(srp_erp_paymentvoucherdetail.' . $fieldNameChk . ') as ' . $fieldNameChk . ',';
                $fields4 .= 'SUM(srp_erp_paymentvoucherdetail.' . $fieldNameChk . ') as ' . $fieldNameChk . ',';
                $fields5 .= 'SUM(srp_erp_debitnotedetail.' . $fieldNameChk . ') as ' . $fieldNameChk . ',';
                $fields7 .= 'srp_erp_debitnotemaster.companyLocalCurrency as ' . $fieldNameChk . 'currency,';
                $fields7 .= 'CL.DecimalPlaces as ' . $fieldNameChk . 'DecimalPlaces,';
            } else if ($fieldNameChk == 'supplierCurrencyAmount') {
                $fields .= 'srp_erp_paysupplierinvoicemaster.supplierCurrency as ' . $fieldNameChk . 'currency,';
                $fields .= 'srp_erp_paysupplierinvoicemaster.supplierCurrencyDecimalPlaces as ' . $fieldNameChk . 'decimalPlace,';
                $fields2 .= 'srp_erp_paymentvoucherdetail.partyAmount as ' . $fieldNameChk . 'currency,';
                $fields2 .= 'srp_erp_paymentvoucherdetail.partyCurrencyDecimalPlaces as ' . $fieldNameChk . 'decimalPlace,';
                $fields2 .= 'SUM(srp_erp_paymentvoucherdetail.partyAmount) as ' . $fieldNameChk . ',';
                $fields4 .= 'SUM(srp_erp_paymentvoucherdetail.partyAmount) as ' . $fieldNameChk . ',';
                $fields5 .= 'SUM(srp_erp_debitnotedetail.supplierAmount) as ' . $fieldNameChk . ',';
            }
            $fields .= '(SUM(srp_erp_paysupplierinvoicemaster.' . $fieldNameChk . ') - (IFNULL(SUM(pvd.' . $fieldNameChk . '),0)+IFNULL(SUM(dnd.' . $fieldNameChk . '),0))) * -1  as ' . $fieldNameChk . ',';
            $fields3 .= 'a.' . $fieldNameChk . 'currency as ' . $fieldNameChk . 'currency,';
            $fields3 .= 'a.' . $fieldNameChk . 'DecimalPlaces,';
            $fields3 .= 'a.' . $fieldNameChk . ' as ' . $fieldNameChk . ',';
            $fields7 .= '(SUM(srp_erp_debitnotedetail.' . $fieldNameChk . ') - IFNULL(SUM(pvd.' . $fieldNameChk . '),0)) as ' . $fieldNameChk . ',';
            $having[] = $fieldNameChk . '!= -0 AND ' . $fieldNameChk . ' != 0';
        }

        $result = $this->db->query("SELECT $fields3 a.InvoiceAutoID,a.supplierInvoiceNo,a.supplierName,a.supplierSystemCode,a.comments,a.documentID,DATE_FORMAT(a.bookingDate,'" . $this->format . "') as bookingDate,a.GLSecondaryCode,a.GLDescription,a.bookingInvCode,a.age FROM 
((SELECT $fields srp_erp_paysupplierinvoicemaster.InvoiceAutoID,srp_erp_paysupplierinvoicemaster.supplierInvoiceNo as supplierInvoiceNo,srp_erp_suppliermaster.supplierName,srp_erp_suppliermaster.supplierSystemCode,srp_erp_paysupplierinvoicemaster.comments,srp_erp_paysupplierinvoicemaster.documentID,srp_erp_paysupplierinvoicemaster.bookingDate,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,srp_erp_paysupplierinvoicemaster.bookingInvCode,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_paysupplierinvoicemaster.`invoiceDueDate`) as age FROM `srp_erp_paysupplierinvoicemaster` 
LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_paysupplierinvoicemaster`.`supplierID` = `srp_erp_suppliermaster`.`supplierAutoID` AND `srp_erp_suppliermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN 
(
	SELECT
		$fields4 srp_erp_paymentvoucherdetail.InvoiceAutoID,srp_erp_paymentvoucherdetail.payVoucherAutoID
	FROM
		srp_erp_paymentvoucherdetail
		INNER JOIN `srp_erp_paymentvouchermaster` ON `srp_erp_paymentvouchermaster`.`payVoucherAutoID` = `srp_erp_paymentvoucherdetail`.`payVoucherAutoID` AND `srp_erp_paymentvouchermaster`.`approvedYN` = 1 AND srp_erp_paymentvouchermaster.PVDate <= '" . format_date($this->input->post("from")) . "'
	WHERE
		`srp_erp_paymentvoucherdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . "  GROUP BY srp_erp_paymentvoucherdetail.InvoiceAutoID
) pvd ON (
	pvd.`InvoiceAutoID` = `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN
(
	SELECT
		$fields5 srp_erp_debitnotedetail.InvoiceAutoID,srp_erp_debitnotedetail.debitNoteMasterAutoID
	FROM
		srp_erp_debitnotedetail 
		INNER JOIN `srp_erp_debitnotemaster` ON `srp_erp_debitnotemaster`.`debitNoteMasterAutoID` = `srp_erp_debitnotedetail`.`debitNoteMasterAutoID` AND `srp_erp_debitnotemaster`.`approvedYN` = 1 AND srp_erp_debitnotemaster.debitNoteDate <= '" . format_date($this->input->post("from")) . "'
	WHERE
		`srp_erp_debitnotedetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " GROUP BY srp_erp_debitnotedetail.InvoiceAutoID
) dnd ON (
	dnd.`InvoiceAutoID` = `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_paysupplierinvoicemaster.supplierliabilityAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_paysupplierinvoicemaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_paysupplierinvoicemaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_paysupplierinvoicemaster.transactionCurrencyID) 
WHERE srp_erp_suppliermaster.supplierAutoID = $vendor AND `srp_erp_paysupplierinvoicemaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_paysupplierinvoicemaster.`bookingDate` <= '" . format_date($this->input->post("from")) . "'  AND `srp_erp_paysupplierinvoicemaster`.`approvedYN` = 1 GROUP BY `srp_erp_paysupplierinvoicemaster`.`InvoiceAutoID` HAVING (" . join(' AND ', $having) . ")) 
UNION ALL
(SELECT $fields2 srp_erp_paymentvouchermaster.payVoucherAutoID as InvoiceAutoID,'-' as supplierInvoiceNo,srp_erp_suppliermaster.supplierName,srp_erp_suppliermaster.supplierSystemCode,'Advance' as comments,srp_erp_paymentvouchermaster.documentID,srp_erp_paymentvouchermaster.PVDate as bookingDate,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,srp_erp_paymentvouchermaster.PVcode as bookingInvCode,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_paymentvouchermaster.`PVdate`) as age  FROM srp_erp_paymentvouchermaster 
INNER JOIN `srp_erp_paymentvoucherdetail` ON `srp_erp_paymentvoucherdetail`.`payVoucherAutoID` = `srp_erp_paymentvouchermaster`.`payVoucherAutoID` AND `srp_erp_paymentvoucherdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND  srp_erp_paymentvoucherdetail.type='Advance'
LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_paymentvouchermaster`.`partyID` = `srp_erp_suppliermaster`.`supplierAutoID` AND `srp_erp_suppliermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_paymentvouchermaster.partyGLAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_paymentvouchermaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_paymentvouchermaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_paymentvouchermaster.transactionCurrencyID) 
WHERE srp_erp_suppliermaster.supplierAutoID = $vendor AND `srp_erp_paymentvouchermaster`.`approvedYN` = 1 AND srp_erp_paymentvouchermaster.PVDate <= '" . format_date($this->input->post("from")) . "' AND srp_erp_paymentvoucherdetail.InvoiceAutoID IS NULL GROUP BY `srp_erp_paymentvouchermaster`.`payVoucherAutoID`)
UNION ALL
(SELECT $fields7 
srp_erp_debitnotemaster.debitNoteMasterAutoID as InvoiceAutoID,'-' as supplierInvoiceNo,srp_erp_suppliermaster.supplierName,srp_erp_suppliermaster.supplierSystemCode,srp_erp_debitnotemaster.comments,srp_erp_debitnotemaster.documentID,srp_erp_debitnotemaster.debitNoteDate as bookingDate,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,srp_erp_debitnotemaster.debitNoteCode as bookingInvCode,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_debitnotemaster.debitNoteDate) as age FROM srp_erp_debitnotemaster 
INNER JOIN `srp_erp_debitnotedetail` ON `srp_erp_debitnotedetail`.`debitNoteMasterAutoID` = `srp_erp_debitnotemaster`.`debitNoteMasterAutoID` AND `srp_erp_debitnotedetail`.`InvoiceAutoID` IS NULL AND `srp_erp_debitnotedetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT $fields4 debitNoteAutoID FROM `srp_erp_paymentvoucherdetail` WHERE `srp_erp_paymentvoucherdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND type='debitnote' GROUP BY debitNoteAutoID) pvd ON pvd.`debitNoteAutoID` = `srp_erp_debitnotemaster`.`debitNoteMasterAutoID`
LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_debitnotemaster`.`supplierID` = `srp_erp_suppliermaster`.`supplierAutoID` AND `srp_erp_suppliermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_debitnotemaster.supplierliabilityAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_debitnotemaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_debitnotemaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_debitnotemaster.transactionCurrencyID) 
WHERE srp_erp_suppliermaster.supplierAutoID = $vendor AND `srp_erp_debitnotemaster`.`approvedYN` = 1 AND srp_erp_debitnotemaster.debitNoteDate <= '" . format_date($this->input->post("from")) . "' AND `srp_erp_debitnotemaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " GROUP BY `srp_erp_debitnotemaster`.`debitNoteMasterAutoID`)) as a WHERE $finalCheck")->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_accounts_receivable_report_drilldown($fromTo = false, $financialBeginingDate)
    {
        $customer = $this->input->post("customerID");
        $age = $this->input->post("age");
        $list = explode("-", $age);
        $finalCheck = "";
        if ($this->input->post('through') == $age) {
            $finalCheck = "a.age > " . $age;
        } else {
            $finalCheck = "a.age >= " . $list[0] . " AND a.age <= " . $list[1];
        }

        $fields = "";
        $fields3 = "";
        $fields4 = "";
        $fields5 = "";
        $fields6 = "";
        $fields7 = "";
        $having = array();
        $fieldNameChk = $this->input->post("currency");
        if (isset($fieldNameChk)) {
            if ($fieldNameChk == 'transactionAmount') {
                $fields .= 'srp_erp_customerinvoicemaster.transactionCurrency as ' . $fieldNameChk . 'currency,';
                $fields .= " TC.DecimalPlaces AS " . $fieldNameChk . "DecimalPlaces,";
                $fields4 .= '(IFNULL(SUM(srp_erp_customerreceiptdetail.' . $fieldNameChk . '),0) -  IFNULL(SUM(rvd.' . $fieldNameChk . '),0)) as ' . $fieldNameChk . ',';
                $fields5 .= 'SUM(srp_erp_creditnotedetail.' . $fieldNameChk . ') as ' . $fieldNameChk . ',';
            } else if ($fieldNameChk == 'companyReportingAmount') {
                $fields .= 'srp_erp_customerinvoicemaster.companyReportingCurrency as ' . $fieldNameChk . 'currency,';
                $fields .= " CR.DecimalPlaces AS " . $fieldNameChk . "DecimalPlaces,";
                $fields4 .= '(IFNULL(SUM(srp_erp_customerreceiptdetail.' . $fieldNameChk . '),0) -  IFNULL(SUM(rvd.' . $fieldNameChk . '),0)) as ' . $fieldNameChk . ',';
                $fields5 .= 'SUM(srp_erp_creditnotedetail.' . $fieldNameChk . ') as ' . $fieldNameChk . ',';
            } else if ($fieldNameChk == 'companyLocalAmount') {
                $fields .= 'srp_erp_customerinvoicemaster.companyLocalCurrency as ' . $fieldNameChk . 'currency,';
                $fields .= " CL.DecimalPlaces AS " . $fieldNameChk . "DecimalPlaces,";
                $fields4 .= '(IFNULL(SUM(srp_erp_customerreceiptdetail.' . $fieldNameChk . '),0) -  IFNULL(SUM(rvd.' . $fieldNameChk . '),0)) as ' . $fieldNameChk . ',';
                $fields5 .= 'SUM(srp_erp_creditnotedetail.' . $fieldNameChk . ') as ' . $fieldNameChk . ',';
            } else if ($fieldNameChk == 'customerCurrencyAmount') {
                $fields .= 'srp_erp_customerinvoicemaster.customerCurrency as ' . $fieldNameChk . 'currency,';
                $fields .= 'srp_erp_customerinvoicemaster.customerCurrencyDecimalPlaces as ' . $fieldNameChk . 'decimalPlace,';
                $fields4 .= '(IFNULL(SUM(srp_erp_customerreceiptdetail.customerAmount),0) -  IFNULL(SUM(rvd.' . $fieldNameChk . '),0)) as ' . $fieldNameChk . ',';
                $fields5 .= 'SUM(srp_erp_creditnotedetail.customerAmount) as ' . $fieldNameChk . ',';
            }
            $fields .= '(SUM(srp_erp_customerinvoicemaster.' . $fieldNameChk . ') - (IFNULL(SUM(pvd.' . $fieldNameChk . '),0)+IFNULL(SUM(cnd.' . $fieldNameChk . '),0))+IFNULL(SUM(ca.' . $fieldNameChk . '),0)) as ' . $fieldNameChk . ',';
            $fields3 .= 'a.' . $fieldNameChk . 'currency as ' . $fieldNameChk . 'currency,';
            $fields3 .= " a." . $fieldNameChk . "DecimalPlaces,";
            $fields3 .= 'a.' . $fieldNameChk . ' as ' . $fieldNameChk . ',';
            $fields6 .= 'SUM(srp_erp_rvadvancematchdetails.' . $fieldNameChk . ') as ' . $fieldNameChk . ',';
            $having[] = $fieldNameChk . '!= -0 AND ' . $fieldNameChk . ' != 0';
        }

        $result = $this->db->query("SELECT $fields3 a.invoiceAutoID,a.customerName,a.customerSystemCode,a.comments,a.documentID,DATE_FORMAT(a.bookingDate,'" . $this->format . "') as bookingDate,a.GLSecondaryCode,a.GLDescription,a.bookingInvCode,a.customerID,a.age FROM 
((SELECT $fields srp_erp_customerinvoicemaster.invoiceAutoID,srp_erp_customermaster.customerName,srp_erp_customermaster.customerSystemCode,srp_erp_customerinvoicemaster.invoiceNarration as comments,srp_erp_customerinvoicemaster.documentID,srp_erp_customerinvoicemaster.invoiceDate as bookingDate,srp_erp_chartofAccounts.GLSecondaryCode,srp_erp_chartofAccounts.GLDescription,srp_erp_customerinvoicemaster.invoiceCode as bookingInvCode,`srp_erp_customerinvoicemaster`.`customerID` as customerID,DATEDIFF('" . format_date($this->input->post("from")) . "',srp_erp_customerinvoicemaster.`invoiceDueDate`) as age
FROM `srp_erp_customerinvoicemaster`
LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customerinvoicemaster`.`customerID` = `srp_erp_customermaster`.`customerAutoID` AND `srp_erp_customermaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
LEFT JOIN 
(
	SELECT
		$fields4 srp_erp_customerreceiptdetail.invoiceAutoID,srp_erp_customerreceiptdetail.receiptVoucherAutoID
	FROM
		srp_erp_customerreceiptdetail
		INNER JOIN `srp_erp_customerreceiptmaster` ON `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId` = `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` AND `srp_erp_customerreceiptmaster`.`approvedYN` = 1 AND srp_erp_customerreceiptmaster.RVDate <= '" . format_date($this->input->post("from")) . "'
		LEFT JOIN (SELECT $fields6 srp_erp_rvadvancematchdetails.receiptVoucherAutoId 
		FROM srp_erp_rvadvancematchdetails 
		INNER JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematchdetails`.`matchID` = `srp_erp_rvadvancematch`.`matchID`
        AND `srp_erp_rvadvancematch`.`confirmedYN` = 1) rvd ON (rvd.receiptVoucherAutoId = `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId`)
	WHERE
		`srp_erp_customerreceiptdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . "  GROUP BY srp_erp_customerreceiptdetail.invoiceAutoID
) pvd ON (
	pvd.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID`
)
LEFT JOIN
(
	SELECT
		$fields5 invoiceAutoID,srp_erp_creditnotedetail.creditNoteMasterAutoID
	FROM
		srp_erp_creditnotedetail
		INNER JOIN `srp_erp_creditnotemaster` ON `srp_erp_creditnotemaster`.`creditNoteMasterAutoID` = `srp_erp_creditnotedetail`.`creditNoteMasterAutoID` AND `srp_erp_creditnotemaster`.`approvedYN` = 1 AND srp_erp_creditnotemaster.creditNoteDate <= '" . format_date($this->input->post("from")) . "'
	WHERE
		`srp_erp_creditnotedetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " GROUP BY srp_erp_creditnotedetail.invoiceAutoID
) cnd ON (
	cnd.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID`
)
LEFT JOIN( 
SELECT 
$fields6 srp_erp_rvadvancematchdetails.InvoiceAutoID,srp_erp_rvadvancematchdetails.receiptVoucherAutoID
	FROM srp_erp_rvadvancematchdetails 
	INNER JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematchdetails`.`matchID` = `srp_erp_rvadvancematch`.`matchID` AND `srp_erp_rvadvancematch`.`confirmedYN` = 1
	WHERE `srp_erp_rvadvancematchdetails`.`companyID` = " . $this->common_data['company_data']['company_id'] . "  GROUP BY srp_erp_rvadvancematchdetails.InvoiceAutoID)
	ca ON (
	ca.`InvoiceAutoID` = `srp_erp_customerinvoicemaster`.`InvoiceAutoID`
)
LEFT JOIN srp_erp_chartofAccounts ON srp_erp_customerinvoicemaster.customerReceivableAutoID = srp_erp_chartofAccounts.GLAutoID AND `srp_erp_chartofAccounts`.`companyID` = " . $this->common_data['company_data']['company_id'] . "
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_customerinvoicemaster.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_customerinvoicemaster.companyLocalCurrencyID) 
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_customerinvoicemaster.transactionCurrencyID) 
WHERE `srp_erp_customerinvoicemaster`.`customerID` = $customer AND `srp_erp_customerinvoicemaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_customerinvoicemaster.`invoiceDate` <= '" . format_date($this->input->post("from")) . "' AND `srp_erp_customerinvoicemaster`.`approvedYN` = 1 GROUP BY `srp_erp_customerinvoicemaster`.`invoiceAutoID` HAVING (" . join(' AND ', $having) . "))) as a WHERE $finalCheck")->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_item_report_drilldown($fromTo = false, $financialBeginingDate)
    {
        $item = $this->input->post("itemID");
        $fieldNameChk = $this->input->post("currency");
        $feilds = "";
        if (!empty($fieldNameChk)) { /*generate the query according to selectd columns*/
            if ($fieldNameChk == "companyLocalWacAmount") {
                $feilds .= "il.companyLocalAmount as localCostAsset";
                $feilds .= ",CL.DecimalPlaces as companyLocalWacAmountDecimalPlaces";
                $feilds .= ",il.companyLocalAmount / (il.transactionQTY/il.convertionRate) as avgCompanyLocalAmount";
            }
            if ($fieldNameChk == ("companyReportingWacAmount")) {
                $feilds .= "il.companyReportingAmount as rptCostAsset";
                $feilds .= ",CR.DecimalPlaces as companyReportingWacAmountDecimalPlaces";
                $feilds .= ",il.companyReportingAmount / (il.transactionQTY/il.convertionRate) as avgCompanyReportingAmount";
            }
        }
        $result = $this->db->query("SELECT $feilds,il.documentAutoID,il.segmentCode,il.documentSystemCode,(il.transactionQTY/il.convertionRate) as transactionQTY,il.referenceNumber,il.referenceNumber,DATE_FORMAT(il.documentDate,'" . $this->format . "') as documentDate,il.documentID,il.salesPrice,il.itemDescription,il.itemSystemCode,il.transactionUOM,ic1.description as mainCategory,ic2.description as subCategory,il.documentID,il.companyLocalWacAmount,il.companyReportingWacAmount
        FROM srp_erp_itemledger il
        INNER JOIN
    `srp_erp_itemmaster` `im` ON `il`.`itemAutoID` = `im`.`itemAutoID` AND im.companyID = " . $this->common_data['company_data']['company_id'] . "
        INNER JOIN
    (SELECT
		description,
		itemCategoryID
	FROM
		srp_erp_itemcategory
	GROUP BY
		itemCategoryID) AS `ic1` ON (`ic1`.`itemCategoryID` = `im`.`mainCategoryID`)
        INNER JOIN
    (SELECT
		description,
		itemCategoryID
	FROM
		srp_erp_itemcategory
	GROUP BY
		itemCategoryID) AS `ic2` ON (`ic2`.`itemCategoryID` = `im`.`subcategoryID`)
		 LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = il.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = il.companyLocalCurrencyID)
WHERE il.itemAutoID = $item AND il.documentDate <= '" . format_date($this->input->post("from")) . "' AND il.companyID = " . $this->common_data['company_data']['company_id'] . " ORDER BY il.documentDate DESC")->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_finance_tb_retain()
    {
        $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
        $feilds = "";
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) {
            foreach ($fieldNameChk as $val) {
                $feilds .= "SUM(srp_erp_generalledger." . $val . ") as " . $val . ",";
                if ($val == "companyLocalAmount") {
                    $feilds .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                }
                if ($val == "companyReportingAmount") {
                    $feilds .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                }
            }
        }
        $result = $this->db->query("(SELECT $feilds SUM(srp_erp_generalledger.companyLocalAmount) as companyLocalAmount FROM srp_erp_generalledger 
        INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND masterCategory = 'BS' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . " 
        LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
        WHERE documentDate < '" . $financialBeginingDate["beginingDate"] . "' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . ")")->row_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_finance_tb_group_retain()
    {
        $company = $this->get_group_company();
        $financialBeginingDate = get_group_financial_year(format_date($this->input->post("from")));
        $feilds = "";
        $fieldNameChk = $this->input->post("fieldNameChk");
        if (isset($fieldNameChk)) {
            foreach ($fieldNameChk as $val) {
                $feilds .= "SUM(srp_erp_generalledger." . $val . ") as " . $val . ",";
                if ($val == "companyLocalAmount") {
                    $feilds .= "CL.DecimalPlaces as " . $val . "DecimalPlaces,";
                }
                if ($val == "companyReportingAmount") {
                    $feilds .= "CR.DecimalPlaces as " . $val . "DecimalPlaces,";
                }
            }
        }
        $result = $this->db->query("(SELECT $feilds SUM(srp_erp_generalledger.companyLocalAmount) as companyLocalAmount FROM srp_erp_generalledger 
        INNER JOIN ( SELECT chartofAccountID,GLSecondaryCode,GLDescription FROM srp_erp_groupchartofaccounts INNER JOIN srp_erp_groupchartofaccountdetails ON srp_erp_groupchartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID WHERE groupID = " . current_companyID() . " AND masterCategory = 'BS') coa ON srp_erp_generalledger.GLAutoID = coa.chartofAccountID
        LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID)
LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
        WHERE documentDate < '" . $financialBeginingDate["beginingDate"] . "' AND srp_erp_generalledger.companyID IN(" . join(',',$company). "))")->row_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_financial_year()
    {
        $currentDate = format_date($this->input->post('currentDate'));
        $this->db->SELECT("beginingDate,endingDate");
        $this->db->FROM('srp_erp_companyfinanceyear');
        $this->db->where("companyFinanceYearID", $this->input->post('financeYear'));
        $this->db->where("'{$currentDate}' BETWEEN beginingDate AND endingDate");
        $result = $this->db->get()->row_array();
        if ($result) {
            return array("error" => 0, "data" => $result);
        } else {
            return array("error" => 1, "data" => $result);
        }
    }

    function get_group_financial_year()
    {
        $currentDate = format_date($this->input->post('currentDate'));
        $this->db->SELECT("beginingDate,endingDate");
        $this->db->FROM('srp_erp_groupfinanceyear');
        $this->db->where("groupFinanceYearID", $this->input->post('financeYear'));
        $this->db->where("'{$currentDate}' BETWEEN beginingDate AND endingDate");
        $result = $this->db->get()->row_array();
        if ($result) {
            return array("error" => 0, "data" => $result);
        } else {
            return array("error" => 1, "data" => $result);
        }
    }

    function get_item_inquiry_report()
    {
        $feilds = array();
        $feilds2 = array();
        $feilds3 = array();
        $items = $this->input->post("itemTo");
        $i = 1;
        $itmesOR = '( ';
        if (!empty($items)) {
            foreach ($items as $item_val) {
                if ($i != 1) {
                    $itmesOR .= ' OR ';
                }
                $itmesOR .= " srp_erp_itemmaster.itemAutoID = '" . $item_val . "' "; /*generate the query according to selectd items*/
                $i++;
            }
        }
        $itmesOR .= ' ) ';
        $warehouse = load_location_drop();
        if (isset($warehouse)) {
            foreach ($warehouse as $val) {
                $feilds[] = "if(wareHouseAutoID = " . $val["wareHouseAutoID"] . ",IFNULL(SUM(transactionQTY/convertionRate),0),0) as `" . $val["wareHouseCode"] . "`";
                $feilds2[] = "IFNULL(SUM(il.`" . $val["wareHouseCode"] . "`),0) as `" . $val["wareHouseCode"] . "`";
                $feilds3[] = "IFNULL(SUM(il.`" . $val["wareHouseCode"] . "`),0)";
            }
        }
        $result = $this->db->query("SELECT itemSystemCode,itemDescription,defaultUnitOfMeasure," . join(',', $feilds2) . ",(" . join('+', $feilds3) . ") as total,IFNULL(poCurrentStock,0) as poCurrentStock,IFNULL(coCurrentStock,0) as coCurrentStock,(IFNULL(ugrvCurrentStock,0) + IFNULL(usrCurrentStock,0) + IFNULL(umiCurrentStock,0) + IFNULL(ustCurrentStock,0) + IFNULL(usaCurrentStock,0) + IFNULL(upvCurrentStock,0) + IFNULL(ucinvCurrentStock,0) + IFNULL(urvCurrentStock,0)) as unapprovedDoc,ic1.description as mainCategory,ic2.description as subCategory,srp_erp_itemmaster.itemAutoID,((" . join('+', $feilds3) . ")+IFNULL(poCurrentStock,0)+IFNULL(coCurrentStock,0)+(IFNULL(ugrvCurrentStock,0) + IFNULL(usrCurrentStock,0) + IFNULL(umiCurrentStock,0) + IFNULL(ustCurrentStock,0) + IFNULL(usaCurrentStock,0) + IFNULL(upvCurrentStock,0) + IFNULL(ucinvCurrentStock,0) + IFNULL(urvCurrentStock,0))) as netStock,minimumQty FROM srp_erp_itemmaster
        LEFT JOIN (SELECT wareHouseAutoID,itemAutoID," . join(',', $feilds) . " FROM srp_erp_itemledger WHERE companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY wareHouseAutoID,itemAutoID) il ON (il.itemAutoID = srp_erp_itemmaster.itemAutoID)
         LEFT JOIN (
         SELECT (SUM(requestedQty/conversionRateUOM)-IFNULL(grv.grvCurrentStock,0)) as poCurrentStock,srp_erp_purchaseorderdetails.itemAutoID FROM srp_erp_purchaseorderdetails 
         INNER JOIN srp_erp_purchaseordermaster ON srp_erp_purchaseorderdetails.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID AND srp_erp_purchaseordermaster.approvedYN = 1 AND srp_erp_purchaseordermaster.companyID = " . $this->common_data['company_data']['company_id'] . "  
         LEFT JOIN (SELECT SUM(receivedQty/conversionRateUOM) as grvCurrentStock,srp_erp_grvdetails.purchaseOrderDetailsID,itemAutoID FROM srp_erp_grvdetails         
            INNER JOIN srp_erp_grvmaster ON srp_erp_grvdetails.grvAutoID = srp_erp_grvmaster.grvAutoID AND srp_erp_grvmaster.approvedYN = 1 AND srp_erp_grvmaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            WHERE srp_erp_grvdetails.companyID = " . $this->common_data['company_data']['company_id'] . " AND purchaseOrderDetailsID != 0 GROUP BY  srp_erp_grvdetails.itemAutoID
            ) grv ON (srp_erp_purchaseorderdetails.itemAutoID = grv.itemAutoID) 
         WHERE srp_erp_purchaseorderdetails.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY  srp_erp_purchaseorderdetails.itemAutoID) po ON (po.itemAutoID = srp_erp_itemmaster.itemAutoID)
         LEFT JOIN (
         SELECT (SUM(requestedQty/conversionRateUOM) - IFNULL(cinv.cinvCurrentStock,0)) as coCurrentStock,srp_erp_contractdetails.itemAutoID FROM srp_erp_contractdetails 
         INNER JOIN srp_erp_contractmaster ON srp_erp_contractdetails.contractAutoID = srp_erp_contractmaster.contractAutoID AND srp_erp_contractmaster.approvedYN = 1 AND srp_erp_contractmaster.companyID = " . $this->common_data['company_data']['company_id'] . "  
         LEFT JOIN (SELECT SUM(requestedQty/conversionRateUOM) * -1 as cinvCurrentStock,srp_erp_customerinvoicedetails.contractDetailsAutoID,itemAutoID FROM srp_erp_customerinvoicedetails         
            INNER JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicedetails.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID AND srp_erp_customerinvoicemaster.approvedYN = 1 AND srp_erp_customerinvoicemaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            WHERE srp_erp_customerinvoicedetails.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY srp_erp_customerinvoicedetails.contractDetailsAutoID
            ) cinv ON (srp_erp_contractdetails.itemAutoID = cinv.itemAutoID) 
         WHERE srp_erp_contractdetails.companyID = " . $this->common_data['company_data']['company_id'] . " AND  srp_erp_contractmaster.documentID = 'SO' GROUP BY srp_erp_contractdetails.itemAutoID) co ON (co.itemAutoID = srp_erp_itemmaster.itemAutoID)
         
         LEFT JOIN (SELECT IFNULL(SUM(receivedQty/conversionRateUOM),0) as ugrvCurrentStock,srp_erp_grvdetails.itemAutoID FROM srp_erp_grvdetails         
            INNER JOIN srp_erp_grvmaster ON srp_erp_grvdetails.grvAutoID = srp_erp_grvmaster.grvAutoID AND srp_erp_grvmaster.approvedYN = 0 AND srp_erp_grvmaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            WHERE srp_erp_grvdetails.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY  srp_erp_grvdetails.itemAutoID) ugrv ON (ugrv.itemAutoID = srp_erp_itemmaster.itemAutoID)
           
         LEFT JOIN (SELECT IFNULL(SUM(return_Qty/conversionRateUOM),0) * -1 as usrCurrentStock,srp_erp_stockreturndetails.itemAutoID FROM srp_erp_stockreturndetails         
            INNER JOIN srp_erp_stockreturnmaster ON srp_erp_stockreturndetails.stockReturnAutoID = srp_erp_stockreturnmaster.stockReturnAutoID AND srp_erp_stockreturnmaster.approvedYN = 0 AND srp_erp_stockreturnmaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            GROUP BY  srp_erp_stockreturndetails.itemAutoID) usr ON (usr.itemAutoID = srp_erp_itemmaster.itemAutoID)
         
         LEFT JOIN (SELECT IFNULL(SUM(qtyissued/conversionRateUOM),0)*-1 as umiCurrentStock,srp_erp_itemissuedetails.itemAutoID FROM srp_erp_itemissuedetails         
            INNER JOIN srp_erp_itemissuemaster ON srp_erp_itemissuedetails.itemIssueAutoID = srp_erp_itemissuemaster.itemIssueAutoID AND srp_erp_itemissuemaster.approvedYN = 0 AND srp_erp_itemissuemaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            WHERE srp_erp_itemissuedetails.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY  srp_erp_itemissuedetails.itemAutoID) umi ON (umi.itemAutoID = srp_erp_itemmaster.itemAutoID)
            
         LEFT JOIN (SELECT IFNULL(SUM(transfer_QTY/conversionRateUOM),0) as ustCurrentStock,srp_erp_stocktransferdetails.itemAutoID FROM srp_erp_stocktransferdetails         
            INNER JOIN srp_erp_stocktransfermaster ON srp_erp_stocktransferdetails.stockTransferAutoID = srp_erp_stocktransfermaster.stockTransferAutoID AND srp_erp_stocktransfermaster.approvedYN = 0 AND srp_erp_stocktransfermaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            GROUP BY  srp_erp_stocktransferdetails.itemAutoID) ust ON (ust.itemAutoID = srp_erp_itemmaster.itemAutoID)
            
         LEFT JOIN (SELECT IFNULL(SUM(adjustmentWareHouseStock/conversionRateUOM),0) as usaCurrentStock,srp_erp_stockadjustmentdetails.itemAutoID FROM srp_erp_stockadjustmentdetails         
            INNER JOIN srp_erp_stockadjustmentmaster ON srp_erp_stockadjustmentdetails.stockAdjustmentAutoID = srp_erp_stockadjustmentmaster.stockAdjustmentAutoID AND srp_erp_stockadjustmentmaster.approvedYN = 0 AND srp_erp_stockadjustmentmaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            GROUP BY  srp_erp_stockadjustmentdetails.itemAutoID) usa ON (usa.itemAutoID = srp_erp_itemmaster.itemAutoID)
            
         LEFT JOIN (SELECT IFNULL(SUM(requestedQty/conversionRateUOM),0) as upvCurrentStock,srp_erp_paymentvoucherdetail.itemAutoID FROM srp_erp_paymentvoucherdetail         
            INNER JOIN srp_erp_paymentvouchermaster ON srp_erp_paymentvoucherdetail.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId AND srp_erp_paymentvouchermaster.approvedYN = 0 AND srp_erp_paymentvouchermaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            WHERE srp_erp_paymentvoucherdetail.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY  srp_erp_paymentvoucherdetail.itemAutoID) upv ON (upv.itemAutoID = srp_erp_itemmaster.itemAutoID)
            
         LEFT JOIN (SELECT IFNULL(SUM(requestedQty/conversionRateUOM),0)*-1 as ucinvCurrentStock,srp_erp_customerinvoicedetails.itemAutoID FROM srp_erp_customerinvoicedetails         
            INNER JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicedetails.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID AND srp_erp_customerinvoicemaster.approvedYN = 0 AND srp_erp_customerinvoicemaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            WHERE srp_erp_customerinvoicedetails.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY  srp_erp_customerinvoicedetails.itemAutoID) ucinv ON (ucinv.itemAutoID = srp_erp_itemmaster.itemAutoID)
            
         LEFT JOIN (SELECT IFNULL(SUM(requestedQty/conversionRateUOM),0) * -1 as urvCurrentStock,srp_erp_customerreceiptdetail.itemAutoID FROM srp_erp_customerreceiptdetail         
            INNER JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId AND srp_erp_customerreceiptmaster.approvedYN = 0 AND srp_erp_customerreceiptmaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            WHERE srp_erp_customerreceiptdetail.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY  srp_erp_customerreceiptdetail.itemAutoID) urv ON (urv.itemAutoID = srp_erp_itemmaster.itemAutoID)
            
         LEFT JOIN
    (SELECT 
        description, itemCategoryID
    FROM
        srp_erp_itemcategory GROUP BY itemCategoryID) AS `ic1` ON (`ic1`.`itemCategoryID` = `srp_erp_itemmaster`.`mainCategoryID`)
        LEFT JOIN
    (SELECT 
        description, masterID
    FROM
        srp_erp_itemcategory GROUP BY masterID) AS `ic2` ON (`ic2`.`masterID` = `srp_erp_itemmaster`.`subcategoryID`)
        WHERE srp_erp_itemmaster.maincategory =  'Inventory' AND srp_erp_itemmaster.companyID = " . $this->common_data['company_data']['company_id'] . " AND $itmesOR
        GROUP BY srp_erp_itemmaster.itemAutoID")->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    function get_item_inquiry_po_report_drilldown()
    {
        $item = $this->input->post("itemID");
        $result = $this->db->query("SELECT (SUM(requestedQty/conversionRateUOM)-IFNULL(grv.grvCurrentStock,0)) as poCurrentStock,srp_erp_purchaseorderdetails.itemAutoID,srp_erp_purchaseordermaster.purchaseOrderCode,srp_erp_purchaseorderdetails.comment,srp_erp_purchaseordermaster.referenceNumber,im.itemSystemCode,im.itemDescription,srp_erp_purchaseordermaster.documentDate,srp_erp_purchaseordermaster.expectedDeliveryDate,srp_erp_purchaseordermaster.documentID,srp_erp_purchaseordermaster.purchaseOrderID,im.defaultUnitOfMeasure FROM srp_erp_purchaseorderdetails 
         INNER JOIN srp_erp_purchaseordermaster ON srp_erp_purchaseorderdetails.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID AND srp_erp_purchaseordermaster.approvedYN = 1 AND srp_erp_purchaseordermaster.companyID = " . $this->common_data['company_data']['company_id'] . "
          INNER JOIN
    `srp_erp_itemmaster` `im` ON `srp_erp_purchaseorderdetails`.`itemAutoID` = `im`.`itemAutoID` AND im.companyID = " . $this->common_data['company_data']['company_id'] . "
     LEFT JOIN (SELECT SUM(receivedQty/conversionRateUOM) as grvCurrentStock,srp_erp_grvdetails.purchaseOrderDetailsID FROM srp_erp_grvdetails         
            INNER JOIN srp_erp_grvmaster ON srp_erp_grvdetails.grvAutoID = srp_erp_grvmaster.grvAutoID AND srp_erp_grvmaster.approvedYN = 1 AND srp_erp_grvmaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            WHERE srp_erp_grvdetails.companyID = " . $this->common_data['company_data']['company_id'] . " GROUP BY  srp_erp_grvdetails.purchaseOrderDetailsID
            ) grv ON (srp_erp_purchaseorderdetails.purchaseOrderDetailsID = grv.purchaseOrderDetailsID) 
            WHERE srp_erp_purchaseorderdetails.itemAutoID = " . $item . " GROUP BY srp_erp_purchaseorderdetails.purchaseOrderDetailsID HAVING poCurrentStock > 0")->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_item_inquiry_all_doc_report_drilldown()
    {
        $item = $this->input->post("itemID");
        $result = $this->db->query(" SELECT a.currentStock,a.itemAutoID,a.documentID,a.documentDate,a.documentCode,a.autoID FROM 
        ((SELECT IFNULL(SUM(receivedQty/conversionRateUOM),0) as currentStock,srp_erp_grvdetails.itemAutoID,documentID,grvDate as documentDate,grvPrimaryCode as documentCode,srp_erp_grvmaster.grvAutoID as autoID FROM srp_erp_grvdetails         
            INNER JOIN srp_erp_grvmaster ON srp_erp_grvdetails.grvAutoID = srp_erp_grvmaster.grvAutoID AND srp_erp_grvmaster.approvedYN = 0 AND srp_erp_grvmaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            WHERE srp_erp_grvdetails.companyID = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_grvdetails.itemAutoID = " . $item . "  GROUP BY  srp_erp_grvmaster.grvAutoID)
           
         UNION ALL (SELECT IFNULL(SUM(return_Qty/conversionRateUOM),0) * -1 as currentStock,srp_erp_stockreturndetails.itemAutoID,documentID,returnDate as documentDate,stockReturnCode as documentCode,srp_erp_stockreturnmaster.stockReturnAutoID as autoID FROM srp_erp_stockreturndetails         
            INNER JOIN srp_erp_stockreturnmaster ON srp_erp_stockreturndetails.stockReturnAutoID = srp_erp_stockreturnmaster.stockReturnAutoID AND srp_erp_stockreturnmaster.approvedYN = 0 AND srp_erp_stockreturnmaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            WHERE srp_erp_stockreturndetails.itemAutoID = " . $item . " GROUP BY srp_erp_stockreturnmaster.stockReturnAutoID)
         
         UNION ALL (SELECT IFNULL(SUM(qtyissued/conversionRateUOM),0)*-1 as currentStock,srp_erp_itemissuedetails.itemAutoID,documentID,issueDate as documentDate,itemIssueCode as documentCode,srp_erp_itemissuemaster.itemIssueAutoID as autoID FROM srp_erp_itemissuedetails         
            INNER JOIN srp_erp_itemissuemaster ON srp_erp_itemissuedetails.itemIssueAutoID = srp_erp_itemissuemaster.itemIssueAutoID AND srp_erp_itemissuemaster.approvedYN = 0 AND srp_erp_itemissuemaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            WHERE srp_erp_itemissuedetails.companyID = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_itemissuedetails.itemAutoID = " . $item . " GROUP BY srp_erp_itemissuemaster.itemIssueAutoID)
            
         UNION ALL (SELECT IFNULL(SUM(transfer_QTY/conversionRateUOM),0) as currentStock,srp_erp_stocktransferdetails.itemAutoID,documentID,tranferDate as documentDate,stockTransferCode as documentCode,srp_erp_stocktransfermaster.stockTransferAutoID as autoID FROM srp_erp_stocktransferdetails         
            INNER JOIN srp_erp_stocktransfermaster ON srp_erp_stocktransferdetails.stockTransferAutoID = srp_erp_stocktransfermaster.stockTransferAutoID AND srp_erp_stocktransfermaster.approvedYN = 0 AND srp_erp_stocktransfermaster.companyID = " . $this->common_data['company_data']['company_id'] . " 
            WHERE srp_erp_stocktransferdetails.itemAutoID = " . $item . " GROUP BY srp_erp_stocktransfermaster.stockTransferAutoID)
            
         UNION ALL (SELECT IFNULL(SUM(adjustmentWareHouseStock/conversionRateUOM),0) as currentStock,srp_erp_stockadjustmentdetails.itemAutoID,documentID,stockAdjustmentDate as documentDate,stockAdjustmentCode as documentCode,srp_erp_stockadjustmentmaster.stockAdjustmentAutoID as autoID FROM srp_erp_stockadjustmentdetails         
            INNER JOIN srp_erp_stockadjustmentmaster ON srp_erp_stockadjustmentdetails.stockAdjustmentAutoID = srp_erp_stockadjustmentmaster.stockAdjustmentAutoID AND srp_erp_stockadjustmentmaster.approvedYN = 0 AND srp_erp_stockadjustmentmaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            WHERE srp_erp_stockadjustmentdetails.itemAutoID = " . $item . " GROUP BY srp_erp_stockadjustmentmaster.stockAdjustmentAutoID)
            
         UNION ALL (SELECT IFNULL(SUM(requestedQty/conversionRateUOM),0) as currentStock,srp_erp_paymentvoucherdetail.itemAutoID,documentID,PVDate as documentDate,PVCode as documentCode,srp_erp_paymentvouchermaster.payVoucherAutoId as autoID FROM srp_erp_paymentvoucherdetail         
            INNER JOIN srp_erp_paymentvouchermaster ON srp_erp_paymentvoucherdetail.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId AND srp_erp_paymentvouchermaster.approvedYN = 0 AND srp_erp_paymentvouchermaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            WHERE srp_erp_paymentvoucherdetail.companyID = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_paymentvoucherdetail.itemAutoID = " . $item . " GROUP BY srp_erp_paymentvouchermaster.payVoucherAutoId)
            
         UNION ALL (SELECT IFNULL(SUM(requestedQty/conversionRateUOM),0) * -1 as currentStock,srp_erp_customerinvoicedetails.itemAutoID,documentID,invoiceDate as documentDate,invoiceCode as documentCode,srp_erp_customerinvoicemaster.invoiceAutoID as autoID FROM srp_erp_customerinvoicedetails         
            INNER JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicedetails.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID AND srp_erp_customerinvoicemaster.approvedYN = 0 AND srp_erp_customerinvoicemaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            WHERE srp_erp_customerinvoicedetails.companyID = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_customerinvoicedetails.itemAutoID = " . $item . " GROUP BY srp_erp_customerinvoicemaster.invoiceAutoID)
            
         UNION ALL (SELECT IFNULL(SUM(requestedQty/conversionRateUOM),0) * -1 as currentStock,srp_erp_customerreceiptdetail.itemAutoID,documentID,RVDate as documentDate,RVcode as documentCode,srp_erp_customerreceiptmaster.receiptVoucherAutoId as autoID FROM srp_erp_customerreceiptdetail         
            INNER JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId AND srp_erp_customerreceiptmaster.approvedYN = 0 AND srp_erp_customerreceiptmaster.companyID = " . $this->common_data['company_data']['company_id'] . "
            WHERE srp_erp_customerreceiptdetail.companyID = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_customerreceiptdetail.itemAutoID = " . $item . " GROUP BY srp_erp_customerreceiptmaster.receiptVoucherAutoId)) a")->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    /** NASIK **/
    function save_companyLevelReportDetails()
    {
        $fieldsID = $this->input->post('fieldsID');
        $columnName = $this->input->post('columnName');
        $masterID = $this->input->post('masterID');

        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $userGroup = current_user_group();
        $PC_ID = current_pc();
        $userID = current_userID();
        $time = $this->common_data['current_date'];
        $userName = current_employee();

        $whereIn = 'reportID IN (';

        $data = array();
        foreach ($fieldsID as $key => $row) {
            $whereIn .= ($key > 0) ? ', ' . $row : $row;
            $data[$key]['masterID'] = $masterID;
            $data[$key]['reportID'] = $row;
            $data[$key]['reportValue'] = $this->input->post($columnName[$key]);
            $data[$key]['companyID'] = $companyID;
            $data[$key]['companyCode'] = $companyCode;
            $data[$key]['createdPCID'] = $PC_ID;
            $data[$key]['createdUserID'] = $userID;
            $data[$key]['createdUserGroup'] = $userGroup;
            $data[$key]['createdUserName'] = $userName;
            $data[$key]['createdDateTime'] = $time;
        }
        $whereIn .= ')';

        /*echo '<pre>'; print_r($data); echo '</pre>';
        echo '<pre>'; print_r($whereIn); echo '</pre>';die();*/
        $this->db->trans_start();


        $this->db->query("DELETE FROM srp_erp_sso_reporttemplatedetails WHERE masterID={$masterID} AND companyID={$companyID} AND {$whereIn} ");
        $this->db->insert_batch('srp_erp_sso_reporttemplatedetails', $data);


        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Process failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'Record saved successfully');
        }

    }

    function save_employeeLevelReportDetails($fields_arr)
    {

        $empID_arr = $this->input->post('empID');
        $masterID = $this->input->post('masterID');

        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $userGroup = current_user_group();
        $PC_ID = current_pc();
        $userID = current_userID();
        $time = $this->common_data['current_date'];
        $userName = current_employee();

        $whereIn = 'reportID IN (';
        $data = array();
        $m = 0;
        foreach ($fields_arr as $key => $rowField) {
            $columnName = $this->input->post($rowField['inputName']);
            $reportID = $rowField['id'];

            $whereIn .= ($m > 0) ? ', ' . $reportID : $reportID;

            foreach ($empID_arr as $keyEmp => $empID) {
                $data[$m]['masterID'] = $masterID;
                $data[$m]['reportID'] = $reportID;
                $data[$m]['empID'] = $empID;
                $data[$m]['reportValue'] = $columnName[$keyEmp];
                $data[$m]['companyID'] = $companyID;
                $data[$m]['companyCode'] = $companyCode;
                $data[$m]['createdPCID'] = $PC_ID;
                $data[$m]['createdUserID'] = $userID;
                $data[$m]['createdUserGroup'] = $userGroup;
                $data[$m]['createdUserName'] = $userName;
                $data[$m]['createdDateTime'] = $time;
                $m++;
            }
        }

        $whereIn .= ')';
        //echo $whereIn; die();
        $this->db->trans_start();

        //$this->db->query("DELETE FROM srp_erp_sso_reporttemplatedetails WHERE masterID={$masterID} AND companyID={$companyID} AND {$whereIn} ");
        $this->db->query("DELETE FROM srp_erp_sso_reporttemplatedetails WHERE companyID={$companyID} AND masterID={$masterID} AND {$whereIn} ");

        $this->db->insert_batch('srp_erp_sso_reporttemplatedetails', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Process failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'Record saved successfully');
        }

    }


    function save_epfReportOtherConfig()
    {
        $masterID = $this->input->post('masterID');
        $shortOrder = $this->input->post('shortOrder');
        $strLength = $this->input->post('strLength');
        $reportID = $this->input->post('reportID');

        $companyID = current_companyID();
        $userGroup = current_user_group();
        $PC_ID = current_pc();
        $userID = current_userID();
        $time = $this->common_data['current_date'];
        $userName = current_employee();

        $data = array();
        foreach ($shortOrder as $key => $row) {
            $data[$key]['masterID'] = $masterID;
            $data[$key]['reportID'] = $reportID[$key];
            $data[$key]['shortOrder'] = $shortOrder[$key];
            $data[$key]['strLength'] = $strLength[$key];
            $data[$key]['companyID'] = $companyID;
            $data[$key]['createdPCID'] = $PC_ID;
            $data[$key]['createdUserID'] = $userID;
            $data[$key]['createdUserGroup'] = $userGroup;
            $data[$key]['createdUserName'] = $userName;
            $data[$key]['createdDateTime'] = $time;
        }

        $this->db->trans_start();

        $this->db->query("DELETE FROM srp_erp_sso_reporttemplateconfig WHERE masterID={$masterID} AND companyID={$companyID}");

        $this->db->insert_batch('srp_erp_sso_reporttemplateconfig', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Process failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'Record saved successfully');
        }

    }

    function save_epfReportMaster()
    {

        $payrollMonth = $this->input->post('payrollMonth');
        $submissionID = $this->input->post('submissionID');
        $comments = $this->input->post('comments');
        $companyID = current_companyID();
        $payrollYear = date('Y', strtotime($payrollMonth));
        $payrollMonth = date('m', strtotime($payrollMonth));

        $serialNo = $this->db->query("SELECT serialNo FROM srp_erp_sso_epfreportmaster WHERE companyID={$companyID} ORDER BY id DESC LIMIT 1")->row('serialNo');
        $serialNo = ($serialNo == null) ? 1 : $serialNo + 1;

        //Generate template Code
        $this->load->library('sequence');
        $docCode = $this->sequence->sequence_generator('EPF-R', $serialNo);

        $data = array(
            'documentCode' => $docCode,
            'serialNo' => $serialNo,
            'submissionID' => $submissionID,
            'payrollYear' => $payrollYear,
            'payrollMonth' => $payrollMonth,
            'comment' => $comments,
            'companyID' => $companyID,
            'createdPCID' => current_pc(),
            'createdUserGroup' => current_user_group(),
            'createdUserID' => current_userID(),
            'createdUserName' => current_employee(),
            'createdDateTime' => $this->common_data['current_date']
        );


        $this->db->insert('srp_erp_sso_epfreportmaster', $data);


        if ($this->db->affected_rows() > 0) {
            $insert_id = $this->db->insert_id();
            return array('s', '[ ' . $docCode . ' ]  saved successfully', $insert_id);
        } else {
            return array('e', 'Process failed');
        }
    }

    function epf_reportData($epfMasterID)
    {
        $companyID = current_companyID();
        $masterData = $this->db->query("SELECT id, master.documentCode, submissionID, comment, payrollMasterID, payrollYear, payrollMonth, confirmedYN,
                                        DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') ,'%Y-%m-%d') AS payrollPeriod
                                        FROM srp_erp_sso_epfreportmaster AS master
                                        WHERE master.companyID={$companyID} AND id={$epfMasterID}")->row_array();

        return $masterData;
    }

    function get_epfReportEmployee()
    {
        $companyID = current_companyID();
        $con = "IFNULL(Ename2, '')";
        $epfMasterID = $this->input->post('epfMasterID');

        $where = array(
            'Erp_companyID' => $companyID,
            'companyID' => $companyID,
            'epfReportID' => $epfMasterID
        );

        $this->db->select('reportTB.id, empID, ECode, CONCAT(' . $con . ') AS empName, ocGrade');
        $this->db->from('srp_employeesdetails AS empTB');
        $this->db->join('srp_erp_sso_epfreportdetails AS reportTB', 'empTB.EIdNo = reportTB.empID');
        $this->db->order_by('empTB.ECode', 'ASC');
        $result = $this->db->where($where)->get();

        return $result->result_array();

    }

    function save_empEmployeeAsTemporary()
    {
        $empDet = $this->input->post('empHiddenID');
        $last_ocGrade = $this->input->post('last_ocGrade');
        $epfReportID = $this->input->post('masterID');

        $companyID = current_companyID();
        $userGroup = current_user_group();
        $PC_ID = current_pc();
        $userID = current_userID();
        $time = $this->common_data['current_date'];
        $userName = current_employee();

        $data = $this->epf_reportData($epfReportID);
        $payrollYear = $data['payrollYear'];
        $payrollMonth = $data['payrollMonth'];

        if ($data['confirmedYN'] == 1) {
            return array('e', 'This report is already confirmed.<br>You can not delete this');
        } else {
            $payrollID = $data['payrollMasterID'];
            $whereIn = join(',', $empDet);

            /** Check the employee is added to a report on this payroll id**/
            $empList = $this->db->query("SELECT empID, Ename2 AS empName, ECode FROM srp_erp_sso_epfreportmaster AS masterTB
                                     JOIN srp_erp_sso_epfreportdetails AS detailTB ON detailTB.epfReportID=masterTB.id AND detailTB.companyID=$companyID
                                     JOIN srp_employeesdetails AS empTB ON empTB.EIdNo=detailTB.empID AND Erp_companyID=$companyID
                                     WHERE masterTB.companyID={$companyID} AND payrollYear={$payrollMonth} AND payrollMonth={$payrollMonth}
                                     AND empID IN ({$whereIn})
                                     GROUP BY empID ")->result_array();


            if (!empty($empList)) {
                $errorMsg = 'Following employees are already added to report of this payroll month</br>';
                foreach ($empList as $empRow) {
                    $errorMsg .= $empRow['ECode'] . ' - ' . $empRow['empName'] . '</br>';
                }

                return array('e', $errorMsg);

            } else {

                $data = array();

                foreach ($empDet as $key => $emp) {
                    $data[$key]['epfReportID'] = $epfReportID;
                    $data[$key]['empID'] = $emp;
                    $data[$key]['ocGrade'] = $last_ocGrade[$key];
                    $data[$key]['companyID'] = $companyID;
                    $data[$key]['createdPCID'] = $PC_ID;
                    $data[$key]['createdUserID'] = $userID;
                    $data[$key]['createdUserGroup'] = $userGroup;
                    $data[$key]['createdUserName'] = $userName;
                    $data[$key]['createdDateTime'] = $time;

                }

                $this->db->trans_start();
                $this->db->insert_batch('srp_erp_sso_epfreportdetails', $data);
                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Error in process');
                } else {
                    $this->db->trans_commit();
                    return array('s', '');
                }
            }
        }
    }

    function save_reportDetails()
    {

        $empDet = $this->input->post('empID');
        $comment = $this->input->post('comment');
        $ocGrade = $this->input->post('ocGrade');
        $epfReportID = $this->input->post('epfMasterID');
        $submissionID = $this->input->post('submissionID');
        $isConfirmed = $this->input->post('isConfirmed');

        $companyID = current_companyID();
        $userGroup = current_user_group();
        $PC_ID = current_pc();
        $userID = current_userID();
        $time = $this->common_data['current_date'];
        $userName = current_employee();

        $data = $this->epf_reportData($epfReportID);

        if ($data['confirmedYN'] == 1) {
            return array('e', 'This report is already confirmed.<br>You can not delete this');
        } else {
            $detailTB_where_arr = array(
                'companyID' => $companyID,
                'epfReportID' => $epfReportID
            );
            $data = array();

            foreach ($empDet as $key => $emp) {
                $data[$key]['epfReportID'] = $epfReportID;
                $data[$key]['empID'] = $emp;
                $data[$key]['ocGrade'] = $ocGrade[$key];
                $data[$key]['companyID'] = $companyID;
                $data[$key]['createdPCID'] = $PC_ID;
                $data[$key]['createdUserID'] = $userID;
                $data[$key]['createdUserGroup'] = $userGroup;
                $data[$key]['createdUserName'] = $userName;
                $data[$key]['createdDateTime'] = $time;
            }

            $masterData = array(
                'submissionID' => $submissionID,
                'comment' => $comment,
                'modifiedUserID' => $userID,
                'modifiedUserName' => $userName,
                'modifiedPCID' => $userID,
                'modifiedDateTime' => $time,
            );

            $where_arr = array(
                'id' => $epfReportID,
                'companyID' => $companyID
            );

            if ($isConfirmed == 1) {
                $masterData['confirmedYN'] = 1;
                $masterData['confirmedByEmpID'] = $userID;
                $masterData['confirmedByName'] = $userName;
                $masterData['confirmedDate'] = $time;
            }

            $this->db->trans_start();

            $this->db->where($where_arr)->update('srp_erp_sso_epfreportmaster', $masterData);
            $this->db->delete('srp_erp_sso_epfreportdetails', $detailTB_where_arr);
            $this->db->insert_batch('srp_erp_sso_epfreportdetails', $data);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Error in process');
            } else {
                $this->db->trans_commit();
                return array('s', 'Process successfully done');
            }
        }
    }

    function delete_epfReport()
    {
        $deleteID = $this->input->post('deleteID');
        $companyID = current_companyID();
        $data = $this->epf_reportData($deleteID);

        if ($data['confirmedYN'] == 1) {
            return array('e', 'This report is already confirmed.<br>You can not delete this');
        } else {

            $this->db->trans_start();

            $this->db->delete('srp_erp_sso_epfreportmaster', 'id=' . $deleteID . ' AND companyID=' . $companyID);
            $this->db->delete('srp_erp_sso_epfreportdetails', 'epfReportID=' . $deleteID . ' AND companyID=' . $companyID);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Error in process');
            } else {
                $this->db->trans_commit();
                return array('s', 'Process successfully done');
            }
        }
    }

    function delete_epfReportEmp()
    {
        $epfMasterID = $this->input->post('epfMasterID');
        $id = $this->input->post('id');
        $data = $this->epf_reportData($epfMasterID);
        $companyID = current_companyID();

        if ($data['confirmedYN'] == 1) {
            return array('e', 'This report is already confirmed.<br>You can not delete this');
        } else {

            $this->db->trans_start();

            $where_arr = array(
                'id' => $id,
                'epfReportID' => $epfMasterID,
                'companyID' => $companyID
            );

            $this->db->delete('srp_erp_sso_epfreportdetails', $where_arr);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Error in process');
            } else {
                $this->db->trans_commit();
                return array('s', 'Process successfully done');
            }
        }
    }

    function delete_epfReportAllEmp()
    {
        $epfMasterID = $this->input->post('epfMasterID');
        $data = $this->epf_reportData($epfMasterID);
        $companyID = current_companyID();

        if ($data['confirmedYN'] == 1) {
            return array('e', 'This report is already confirmed.<br>You can not delete this');
        } else {

            $this->db->trans_start();

            $where_arr = array(
                'epfReportID' => $epfMasterID,
                'companyID' => $companyID
            );

            $this->db->delete('srp_erp_sso_epfreportdetails', $where_arr);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Error in process');
            } else {
                $this->db->trans_commit();
                return array('s', 'Process successfully done');
            }
        }
    }


    function get_group_suppliers($vendor)
    {

        $this->db->select("supplierMasterID");
        $this->db->from('srp_erp_groupsupplierdetails');
        $this->db->where_in('groupSupplierMasterID', $vendor);
        $supplier = $this->db->get()->result_array();
        return $supplier;
    }

    function get_group_items($item)
    {

        $this->db->select("ItemAutoID");
        $this->db->from('srp_erp_groupitemmasterdetails');
        $this->db->where_in('groupItemMasterID', $item);
        $items = $this->db->get()->result_array();
        return $items;
    }

    function get_group_company()
    {

        $this->db->select("companyID");
        $this->db->from('srp_erp_companygroupdetails');
        $this->db->where('companyGroupID', current_companyID());
        $company = $this->db->get()->result_array();
        return array_column($company, 'companyID');
    }

    function load_subcat()
    {
        if ($this->input->post('type') == 1) {
            $this->db->select('itemCategoryID,description,masterID');
            $this->db->where_in('masterID', $this->input->post('mainCategoryID'));
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->from('srp_erp_itemcategory');
            return $subcat = $this->db->get()->result_array();
        } else {
            $this->db->select('itemCategoryID,description,masterID');
            $this->db->where_in('masterID', $this->input->post('mainCategoryID'));
            $this->db->where('groupID', $this->common_data['company_data']['company_id']);
            $this->db->from('srp_erp_groupitemcategory');
            return $subcat = $this->db->get()->result_array();
        }
    }

    function load_subsubcat()
    {
        if ($this->input->post('type') == 1) {
            $this->db->select('itemCategoryID,description,masterID');
            $this->db->where_in('masterID', $this->input->post('subCategoryID'));
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->from('srp_erp_itemcategory');
            return $subsubcat = $this->db->get()->result_array();
        } else {
            $this->db->select('itemCategoryID,description,masterID');
            $this->db->where_in('masterID', $this->input->post('subCategoryID'));
            $this->db->where('groupID', $this->common_data['company_data']['company_id']);
            $this->db->from('srp_erp_groupitemcategory');
            return $subsubcat = $this->db->get()->result_array();
        }
    }

    function loadItems()
    {
        if ($this->input->post('type') == 1) {
            $this->db->SELECT("*");
            $this->db->FROM('srp_erp_itemmaster');
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            if (!empty($this->input->post('mainCategoryID'))) {
                $this->db->where_in('mainCategoryID', $this->input->post('mainCategoryID'));
            }
            if (!empty($this->input->post('subCategoryID'))) {
                $this->db->where_in('subCategoryID', $this->input->post('subCategoryID'));
            }
            if (!empty($this->input->post('subSubCategoryID'))) {
                $this->db->where_in('subSubCategoryID', $this->input->post('subSubCategoryID'));
            }
            $result = $this->db->get()->result_array();
            /*echo $this->db->last_query();
            exit;*/
            return $result;
        } else {
            $this->db->SELECT("*");
            $this->db->FROM('srp_erp_groupitemmaster');
            $this->db->where('srp_erp_groupitemmaster.groupID', current_companyID());
            if (!empty($this->input->post('mainCategoryID'))) {
                $this->db->where_in('mainCategoryID', $this->input->post('mainCategoryID'));
            }
            if (!empty($this->input->post('subCategoryID'))) {
                $this->db->where_in('subCategoryID', $this->input->post('subCategoryID'));
            }
            if (!empty($this->input->post('subSubCategoryID'))) {
                $this->db->where_in('subSubCategoryID', $this->input->post('subSubCategoryID'));
            }
            $result = $this->db->get()->result_array();
            /*echo $this->db->last_query();
            exit;*/
            return $result;
        }
    }

    function loadGroupItems()
    {
        $this->db->SELECT("*");
        $this->db->FROM('srp_erp_groupitemmaster');
        $this->db->where('srp_erp_groupitemmaster.groupID', current_companyID());
        if (!empty($this->input->post('mainCategoryID'))) {
            $this->db->where_in('mainCategoryID', $this->input->post('mainCategoryID'));
        }
        if (!empty($this->input->post('subCategoryID'))) {
            $this->db->where_in('subCategoryID', $this->input->post('subCategoryID'));
        }
        if (!empty($this->input->post('subSubCategoryID'))) {
            $this->db->where_in('subSubCategoryID', $this->input->post('subSubCategoryID'));
        }
        $result = $this->db->get()->result_array();
        /*echo $this->db->last_query();
        exit;*/
        return $result;
    }


    function get_warehouse()
    {
        $this->db->SELECT("*");
        $this->db->FROM('srp_erp_warehousemaster');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where_in('wareHouseAutoID', $this->input->post('location'));
        $result = $this->db->get()->result_array();
        $result = array_column($result, 'wareHouseLocation');
        return $result;
    }

    function get_group_warehouse()
    {
        $this->db->SELECT("*");
        $this->db->FROM('srp_erp_groupwarehousemaster');
        $this->db->where('groupID', $this->common_data['company_data']['company_id']);
        $this->db->where_in('wareHouseAutoID', $this->input->post('location'));
        $result = $this->db->get()->result_array();
        $result = array_column($result, 'wareHouseLocation');
        return $result;
    }

    function get_group_chartofaccount()
    {
        $this->db->SELECT("*");
        $this->db->FROM('srp_erp_groupchartofaccountdetails');
        $this->db->where('companyGroupID', $this->common_data['company_data']['company_id']);
        $this->db->where_in('groupChartofAccountMasterID', $this->input->post('glCode'));
        $result = $this->db->get()->result_array();
        $result = array_column($result, 'chartofAccountID');
        return $result;
    }

    function get_collection_summery_report($datearr, $previousbegindate, $previousenddate, $beginingDate, $endingDate)
    {
        $customerID = $this->input->post('customerID');
        $segment = $this->input->post('segment');
        $currency = $this->input->post('currency');
        $sumamount = '';
        $previousamount = '';
        if ($currency == 2) {
            foreach ($datearr as $key => $val) {
                $sumamount .= " SUM(IF(RVdate='$key',transactionAmount/companyLocalExchangeRate,0)) as '$val' ,";
            }
            $previousamount = "total.previoustransactionAmount/companyLocalExchangeRate";
        } else {
            foreach ($datearr as $key => $val) {
                $sumamount .= " SUM(IF(RVdate='$key',transactionAmount/companyReportingExchangeRate,0)) as '$val' ,";
            }
            $previousamount = "total.previoustransactionAmount/companyReportingExchangeRate";
        }

        $qry = "SELECT
	b.*,

IFNULL(previousdet.previoustransactionAmount,0) as previoustransactionAmount
FROM
	(
		SELECT
			$sumamount
			customermastername,
			transactionCurrencyDecimalPlaces,
			transactionExchangeRate,
			companyLocalCurrencyDecimalPlaces,
			companyLocalExchangeRate,
			companyReportingCurrencyDecimalPlaces,
			companyReportingExchangeRate,
			customerID,
			segmentID
		FROM
			(
				SELECT
					det.transactionAmount,
					srp_erp_customerreceiptmaster.customerID,
					`srp_erp_customermaster`.`customerName` AS `customermastername`,
					`transactionCurrencyDecimalPlaces`,
					`transactionCurrency`,
					`transactionExchangeRate`,
					`companyLocalCurrency`,
					`companyLocalCurrencyDecimalPlaces`,
					companyLocalExchangeRate,
					`companyReportingCurrency`,
					`companyReportingExchangeRate`,
					`companyReportingCurrencyDecimalPlaces`,
					DATE_FORMAT(RVdate, '%Y-%m') AS RVdate,
					segmentID
				FROM
					srp_erp_customerreceiptmaster
				LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_customerreceiptmaster`.`customerID`
				INNER JOIN (
					SELECT
						SUM(
							srp_erp_customerreceiptdetail.transactionAmount
						) AS transactionAmount,
						srp_erp_customerreceiptdetail.receiptVoucherAutoId
					FROM
						srp_erp_customerreceiptdetail
					LEFT JOIN srp_erp_customerreceiptmaster ON `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId
					WHERE
						type = 'Invoice'
					AND approvedYN = 1
					GROUP BY
						srp_erp_customerreceiptdetail.receiptVoucherAutoId
				) det ON (
					`det`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId
				)
				WHERE
					srp_erp_customerreceiptmaster.companyID = " . current_companyID() . "
				AND approvedYN = 1
				AND srp_erp_customerreceiptmaster.RVdate BETWEEN '$previousbegindate'
				AND '$endingDate'
				AND RVType = 'Invoices'
				AND srp_erp_customerreceiptmaster.customerID IN (" . join(',', $customerID) . ")
                AND srp_erp_customerreceiptmaster.segmentID IN (" . join(',', $segment) . ")
			) a
		GROUP BY
			customerID
	) b
LEFT JOIN (
	SELECT
		IFNULL(
			SUM(
				$previousamount
			),
			0
		) AS previoustransactionAmount,
		total.receiptVoucherAutoId,
		srp_erp_customerreceiptmaster.customerID
	FROM
		srp_erp_customerreceiptmaster
	LEFT JOIN (
		SELECT
			SUM(transactionAmount) AS previoustransactionAmount,
			receiptVoucherAutoId,
			type
		FROM
			srp_erp_customerreceiptdetail
		WHERE
			type = 'Invoice'
		GROUP BY
			receiptVoucherAutoId
	) total ON total.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId
	WHERE
		srp_erp_customerreceiptmaster.RVdate BETWEEN '$previousbegindate'
	AND '$previousenddate' AND approvedYN=1
	GROUP BY
		customerID
) previousdet ON (
	`previousdet`.`customerID` = b.customerID
)";

        $output = $this->db->query($qry)->result_array();
        return $output;
    }


    /*function get_collection_summery_report($datearr,$previousbegindate,$previousenddate,$beginingDate,$endingDate){
        $customerID = $this->input->post('customerID');
        $segment = $this->input->post('segment');
        $currency = $this->input->post('currency');
        $sumamount='';
        $previousamount='';
        if($currency==2){
            foreach($datearr as $key => $val ){
                $sumamount .= " SUM(IF(RVdate='$key',transactionAmount/companyLocalExchangeRate,0)) as '$val' ,";
            }
            $previousamount="IFNULL(previoustransactionAmount/companyLocalExchangeRate,0) as previoustransactionAmount,";
        }else{
            foreach($datearr as $key => $val ){
                $sumamount .= " SUM(IF(RVdate='$key',transactionAmount/companyReportingExchangeRate,0)) as '$val' ,";
                $previousamount="IFNULL(previoustransactionAmount/companyReportingExchangeRate,0) as previoustransactionAmount,";
            }
        }
        $qry = "SELECT
    $sumamount
    $previousamount
    customermastername,
    transactionCurrencyDecimalPlaces,
    transactionExchangeRate,
    companyLocalCurrencyDecimalPlaces,
    companyLocalExchangeRate,
    companyReportingCurrencyDecimalPlaces,
    companyReportingExchangeRate,
    customerID,
    segmentID
FROM
    (
        SELECT
            det.transactionAmount,
            previousdet.previoustransactionAmount,
            srp_erp_customerreceiptmaster.customerID,
            `srp_erp_customermaster`.`customerName` AS `customermastername`,
            `transactionCurrencyDecimalPlaces`,
            `transactionCurrency`,
            `transactionExchangeRate`,
            `companyLocalCurrency`,
            `companyLocalCurrencyDecimalPlaces`,
            companyLocalExchangeRate,
            `companyReportingCurrency`,
            `companyReportingExchangeRate`,
            `companyReportingCurrencyDecimalPlaces`,
            DATE_FORMAT(RVdate, '%Y-%m') AS RVdate,
            segmentID
        FROM
            srp_erp_customerreceiptmaster
        LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_customerreceiptmaster`.`customerID`
        LEFT JOIN (
            SELECT
                SUM(srp_erp_customerreceiptdetail.transactionAmount) AS transactionAmount,
                srp_erp_customerreceiptdetail.receiptVoucherAutoId
            FROM
                srp_erp_customerreceiptdetail
                left JOIN srp_erp_customerreceiptmaster ON `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId
            WHERE
                type = 'Invoice'
                AND approvedYN = 1
            GROUP BY
                srp_erp_customerreceiptdetail.receiptVoucherAutoId
        ) det ON (
            `det`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId
        )
        LEFT JOIN (
            SELECT
                SUM(srp_erp_customerreceiptdetail.transactionAmount) AS previoustransactionAmount,
                srp_erp_customerreceiptdetail.receiptVoucherAutoId,
                srp_erp_customerreceiptmaster.customerID
            FROM
                srp_erp_customerreceiptdetail
            LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId
            WHERE
                type = 'Invoice'
                AND srp_erp_customerreceiptmaster.RVdate BETWEEN '$previousbegindate' AND  '$previousenddate'
            GROUP BY
                receiptVoucherAutoId
        ) previousdet ON (
            `previousdet`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId
        )
        WHERE
            srp_erp_customerreceiptmaster.companyID = ".current_companyID()."
        AND approvedYN = 1
        AND srp_erp_customerreceiptmaster.RVdate BETWEEN '$beginingDate'
			AND '$endingDate'
        AND RVType = 'Invoices'
        AND srp_erp_customerreceiptmaster.customerID IN (".join(',',$customerID).")
        AND srp_erp_customerreceiptmaster.segmentID IN (".join(',',$segment).")
    ) a
GROUP BY
    customerID";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }*/


    function get_revanue_details_drilldown_report()
    {
        $convertFormat = convert_date_format_sql();
        $customerID = $this->input->post('customerID');
        $segment = $this->input->post('segment');
        $companyID = current_companyID();
        $currency = $this->input->post('currency');
        $datefrm = $this->input->post('date');
        $datefromconvert = $datefrm . '-01';
        $datetoconvert = $datefrm . '-31';


        $date = "";
        //$date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 00:00:00')";
        $sumamount = '';
        if ($currency == 2) {
            $sumamount .= " SUM(srp_erp_customerreceiptdetail.transactionAmount/srp_erp_customerreceiptmaster.companyLocalExchangeRate) as transactionAmount";
        } else {
            $sumamount .= " SUM(srp_erp_customerreceiptdetail.transactionAmount/srp_erp_customerreceiptmaster.companyReportingExchangeRate) as transactionAmount";
        }

        $qry = "SELECT
	srp_erp_customerreceiptmaster.receiptVoucherAutoId,
	srp_erp_customerreceiptmaster.RVcode,
	srp_erp_customerreceiptmaster.RVNarration,
	srp_erp_customerreceiptmaster.documentID,
	srp_erp_chartofaccounts.bankName,
	srp_erp_chartofaccounts.bankAccountNumber,
	DATE_FORMAT(srp_erp_customerreceiptmaster.RVdate,'" . $convertFormat . "') AS RVdate,
	srp_erp_customerreceiptmaster.segmentCode,
	srp_erp_customerreceiptmaster.companyLocalCurrencyDecimalPlaces,
	srp_erp_customerreceiptmaster.companyReportingCurrencyDecimalPlaces,
	srp_erp_customerreceiptmaster.transactionCurrency,
    srp_erp_customerreceiptmaster.companyLocalCurrency,
    srp_erp_customerreceiptmaster.companyReportingCurrency,
	$sumamount
FROM
	srp_erp_customerreceiptdetail
LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId
LEFT JOIN srp_erp_chartofaccounts ON srp_erp_customerreceiptmaster.bankGLAutoID = srp_erp_chartofaccounts.GLAutoID
LEFT JOIN srp_erp_customermaster cus ON srp_erp_customerreceiptmaster.customerID = cus.customerAutoID
WHERE
	srp_erp_customerreceiptmaster.companyID = $companyID
AND customerID = $customerID

AND srp_erp_customerreceiptmaster.approvedYN = 1
AND srp_erp_customerreceiptdetail.type = 'Invoice'
AND srp_erp_customerreceiptmaster.RVdate BETWEEN '$datefromconvert'
AND '$datetoconvert'
GROUP BY
	srp_erp_customerreceiptmaster.receiptVoucherAutoId
	ORDER BY srp_erp_customerreceiptmaster.RVdate ASC";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }


    function get_revanue_previous_details_drilldown_report()
    {
        $convertFormat = convert_date_format_sql();
        $customerID = $this->input->post('customerID');
        $segment = $this->input->post('segment');
        $companyID = current_companyID();
        $currency = $this->input->post('currency');
        $datebegin = $this->input->post('datebegin');
        $dateend = $this->input->post('dateend');


        $date = "";
        //$date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 00:00:00')";
        $sumamount = '';
        if ($currency == 2) {
            $sumamount .= " SUM(srp_erp_customerreceiptdetail.transactionAmount/srp_erp_customerreceiptmaster.companyLocalExchangeRate) as transactionAmount";
        } else {
            $sumamount .= " SUM(srp_erp_customerreceiptdetail.transactionAmount/srp_erp_customerreceiptmaster.companyReportingExchangeRate) as transactionAmount";
        }

        $qry = "SELECT
	srp_erp_customerreceiptmaster.receiptVoucherAutoId,
	srp_erp_customerreceiptmaster.RVcode,
	cus.customerName,
	srp_erp_customerreceiptmaster.RVNarration,
	srp_erp_customerreceiptmaster.documentID,
	srp_erp_chartofaccounts.bankName,
	srp_erp_chartofaccounts.bankAccountNumber,
	DATE_FORMAT(srp_erp_customerreceiptmaster.RVdate,'" . $convertFormat . "') AS RVdate,
	srp_erp_customerreceiptmaster.segmentCode,
	srp_erp_customerreceiptmaster.companyLocalCurrencyDecimalPlaces,
	srp_erp_customerreceiptmaster.companyReportingCurrencyDecimalPlaces,
	srp_erp_customerreceiptmaster.transactionCurrency,
    srp_erp_customerreceiptmaster.companyLocalCurrency,
    srp_erp_customerreceiptmaster.companyReportingCurrency,
	$sumamount
FROM
	srp_erp_customerreceiptdetail
LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId
LEFT JOIN srp_erp_chartofaccounts ON srp_erp_customerreceiptmaster.bankGLAutoID = srp_erp_chartofaccounts.GLAutoID
LEFT JOIN srp_erp_customermaster cus ON srp_erp_customerreceiptmaster.customerID = cus.customerAutoID
WHERE
	srp_erp_customerreceiptmaster.companyID = $companyID
AND customerID = $customerID
AND srp_erp_customerreceiptmaster.approvedYN = 1
AND srp_erp_customerreceiptdetail.type = 'Invoice'
AND srp_erp_customerreceiptmaster.RVdate BETWEEN '$datebegin'
AND '$dateend'
GROUP BY
	srp_erp_customerreceiptdetail.receiptVoucherAutoId
	ORDER BY srp_erp_customerreceiptmaster.RVdate ASC";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function get_collection_detail_reports($currency, $customer, $segment)
    {
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $date = "";

        $sumamount = '';
        if ($currency == 1) {
            $sumamount .= " SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount";
        } else if ($currency == 2) {
            $sumamount .= " SUM(srp_erp_customerreceiptdetail.transactionAmount/srp_erp_customerreceiptmaster.companyLocalExchangeRate) as transactionAmount";
        } else {
            $sumamount .= " SUM(srp_erp_customerreceiptdetail.transactionAmount/srp_erp_customerreceiptmaster.companyReportingExchangeRate) as transactionAmount";
        }

        $qry = "SELECT
	srp_erp_customerreceiptmaster.receiptVoucherAutoId,
	cus.customerName,
	srp_erp_customerreceiptmaster.RVcode,
	srp_erp_customerreceiptmaster.RVNarration,
	srp_erp_customerreceiptmaster.documentID,
	srp_erp_chartofaccounts.bankName,
	srp_erp_chartofaccounts.bankAccountNumber,
	DATE_FORMAT( srp_erp_customerreceiptmaster.RVdate, '%d-%m-%Y' ) AS RVdate,
	srp_erp_customerreceiptmaster.segmentCode,
	srp_erp_customerreceiptmaster.companyLocalCurrencyDecimalPlaces,
	srp_erp_customerreceiptmaster.companyReportingCurrencyDecimalPlaces,
	srp_erp_customerreceiptmaster.customerCurrencyDecimalPlaces,
	srp_erp_customerreceiptmaster.transactionCurrencyDecimalPlaces,
	srp_erp_customerreceiptmaster.transactionCurrency,
	srp_erp_customerreceiptmaster.companyLocalCurrency,
	srp_erp_customerreceiptmaster.companyReportingCurrency,
	$sumamount 
FROM
	srp_erp_customerreceiptdetail
	LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId
	LEFT JOIN srp_erp_chartofaccounts ON srp_erp_customerreceiptmaster.bankGLAutoID = srp_erp_chartofaccounts.GLAutoID 
LEFT JOIN srp_erp_customermaster cus ON srp_erp_customerreceiptmaster.customerID = cus.customerAutoID 
WHERE
	srp_erp_customerreceiptmaster.companyID = $companyID 
	AND srp_erp_customerreceiptmaster.customerID IN (" . join(',', $customer) . ")
    AND srp_erp_customerreceiptmaster.segmentID IN (" . join(',', $segment) . ")
	AND srp_erp_customerreceiptmaster.approvedYN = 1 
	AND srp_erp_customerreceiptdetail.type = 'Invoice' 
	AND srp_erp_customerreceiptmaster.RVdate BETWEEN '$datefromconvert'
	AND '$datetoconvert'
GROUP BY
	srp_erp_customerreceiptmaster.receiptVoucherAutoId 
ORDER BY
	srp_erp_customerreceiptmaster.RVdate ASC";
        $output = $this->db->query($qry)->result_array();
        //$this->db->query($qry)->result_array();
        return $output;

    }
    function customer_name()
    {
        $customer =$this->input->post('customerID');
        $this->db->select('customerName');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $customer);
        $data = $this->db->get()->row_array();
        return $data;
    }

    function group_customer_linked(){
        $company = $this->get_group_company();
        $qry = "SELECT
	CONCAT(customerSystemCode,' - ',customerName) as description
FROM
	srp_erp_customermaster
WHERE NOT EXISTS (SELECT * FROM srp_erp_groupcustomerdetails WHERE srp_erp_groupcustomerdetails.customerMasterID = srp_erp_customermaster.customerAutoID AND companyGroupID = ".current_companyID().")
	AND srp_erp_customermaster.companyID IN (" . join(',', $company) . ")";
        $output = $this->db->query($qry)->result_array();
        return array_column($output, 'description');
    }

    function group_supplier_linked(){
        $company = $this->get_group_company();
        $qry = "SELECT
	CONCAT(supplierSystemCode,' - ',supplierName) as description
FROM
	srp_erp_suppliermaster
WHERE NOT EXISTS (SELECT * FROM srp_erp_groupsupplierdetails WHERE srp_erp_groupsupplierdetails.SupplierMasterID = srp_erp_suppliermaster.supplierAutoID AND companyGroupID = ".current_companyID().")
	AND srp_erp_suppliermaster.companyID IN (" . join(',', $company) . ")";
        $output = $this->db->query($qry)->result_array();
        return array_column($output, 'description');
    }

    function group_chartofaccount_linked(){
        $company = $this->get_group_company();
        $qry = "SELECT
	CONCAT(systemAccountCode,' - ',GLDescription) as description
FROM
	srp_erp_chartofaccounts
WHERE NOT EXISTS (SELECT * FROM srp_erp_groupchartofaccountdetails WHERE srp_erp_groupchartofaccountdetails.chartofAccountID = srp_erp_chartofaccounts.GLAutoID AND companyGroupID = ".current_companyID()." AND masterAccountYN = 0)
	AND masterAccountYN = 0 AND srp_erp_chartofaccounts.companyID IN (" . join(',', $company) . ")";
        $output = $this->db->query($qry)->result_array();
        return array_column($output, 'description');
    }

    function group_segment_linked(){
        $company = $this->get_group_company();
        $qry = "SELECT
	CONCAT(segmentCode,' - ',description) as description
FROM
	srp_erp_segment
WHERE NOT EXISTS (SELECT * FROM srp_erp_groupsegmentdetails WHERE srp_erp_groupsegmentdetails.segmentID = srp_erp_segment.segmentID AND companyGroupID = ".current_companyID().")
	AND srp_erp_segment.companyID IN (" . join(',', $company) . ")";
        $output = $this->db->query($qry)->result_array();
        return array_column($output, 'description');
    }

    function group_item_linked(){
        $company = $this->get_group_company();
        $qry = "SELECT
	CONCAT(itemSystemCode,' - ',itemDescription) as description
FROM
	srp_erp_itemmaster
WHERE NOT EXISTS (SELECT * FROM srp_erp_groupitemmasterdetails WHERE srp_erp_groupitemmasterdetails.ItemAutoID = srp_erp_itemmaster.itemAutoID AND companyGroupID = ".current_companyID().")
	AND srp_erp_itemmaster.companyID IN (" . join(',', $company) . ")";
        $output = $this->db->query($qry)->result_array();
        return array_column($output, 'description');
    }

    function group_warehouse_linked(){
        $company = $this->get_group_company();
        $qry = "SELECT
	CONCAT(wareHouseCode,' - ',wareHouseDescription) as description
FROM
	srp_erp_warehousemaster
WHERE NOT EXISTS (SELECT * FROM srp_erp_groupwarehousedetails WHERE srp_erp_groupwarehousedetails.warehosueMasterID = srp_erp_warehousemaster.wareHouseAutoID AND companyGroupID = ".current_companyID().")
	AND srp_erp_warehousemaster.companyID IN (" . join(',', $company) . ")";
        $output = $this->db->query($qry)->result_array();
        return array_column($output, 'description');
    }

    function get_customer_balance_report($datearr)
    {
        $customerID = $this->input->post('customerID');
        $currency = $this->input->post('currency');
        $companyID = current_companyID();

        $currencygroup='';
        if($currency==2){
            $currencygroup='a.transactionCurrency';
        }

if($currency==2){
    $qry = "SELECT
  srp_erp_chartofaccounts.systemAccountCode as systemGLCode,
  srp_erp_chartofaccounts.GLDescription,
  srp_erp_customermaster.customerName,
	srp_erp_customermaster.customerSystemCode,
	srp_erp_customermaster.secondaryCode,
  companyLocalCurrency,
  transactionCurrency,
  companyReportingCurrency,
	transactionCurrencyDecimalPlaces,
	companyLocalCurrencyDecimalPlaces,
	companyReportingCurrencyDecimalPlaces,
  sum(companyLocalAmount) as companyLocalAmount,
  sum(transactionAmount) as transactionAmount,
  sum(companyReportingAmount) as  companyReportingAmount
FROM
    `srp_erp_generalledger`
INNER JOIN srp_erp_customermaster ON srp_erp_generalledger.partyAutoID = srp_erp_customermaster.customerAutoID
LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID=srp_erp_generalledger.GLAutoID
WHERE
srp_erp_generalledger.partyAutoID IN (" . join(',', $customerID) . ")
    AND srp_erp_generalledger.companyID = $companyID
AND `subLedgerType` = '3'
and documentDate<='$datearr'
group by srp_erp_customermaster.customerAutoID,transactionCurrencyID,srp_erp_chartofaccounts.GLAutoID";
}else{
    $qry = "SELECT
  srp_erp_chartofaccounts.systemAccountCode as systemGLCode,
  srp_erp_chartofaccounts.GLDescription,
  srp_erp_customermaster.customerName,
	srp_erp_customermaster.customerSystemCode,
	srp_erp_customermaster.secondaryCode,
  companyLocalCurrency,
  transactionCurrency,
  companyReportingCurrency,
	transactionCurrencyDecimalPlaces,
	companyLocalCurrencyDecimalPlaces,
	companyReportingCurrencyDecimalPlaces,
  sum(companyLocalAmount) as companyLocalAmount,
  sum(transactionAmount) as transactionAmount,
  sum(companyReportingAmount) as  companyReportingAmount
FROM
    `srp_erp_generalledger`
INNER JOIN srp_erp_customermaster ON srp_erp_generalledger.partyAutoID = srp_erp_customermaster.customerAutoID
LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID=srp_erp_generalledger.GLAutoID
WHERE
srp_erp_generalledger.partyAutoID IN (" . join(',', $customerID) . ")
    AND srp_erp_generalledger.companyID = $companyID
AND `subLedgerType` = '3'
and documentDate<='$datearr'
group by srp_erp_customermaster.customerAutoID,srp_erp_chartofaccounts.GLAutoID";
}


        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function get_vendor_balance_report($datearr)
    {
        $supplierID = $this->input->post('supplierID');
        $currency = $this->input->post('currency');
        $companyID = current_companyID();

        $currencygroup='';
        if($currency==2){
            $currencygroup='a.transactionCurrency';
        }

        if($currency==2){
            $qry = "SELECT
  srp_erp_chartofaccounts.systemAccountCode as systemGLCode,
  srp_erp_chartofaccounts.GLDescription,
  srp_erp_suppliermaster.supplierName,
	srp_erp_suppliermaster.supplierSystemCode,
	srp_erp_suppliermaster.secondaryCode,
  companyLocalCurrency,
  transactionCurrency,
  companyReportingCurrency,
	transactionCurrencyDecimalPlaces,
	companyLocalCurrencyDecimalPlaces,
	companyReportingCurrencyDecimalPlaces,
  sum(companyLocalAmount) as companyLocalAmount,
  sum(transactionAmount) as transactionAmount,
  sum(companyReportingAmount) as  companyReportingAmount
FROM
    `srp_erp_generalledger`
INNER JOIN srp_erp_suppliermaster ON srp_erp_generalledger.partyAutoID = srp_erp_suppliermaster.supplierAutoID
LEFT JOIN srp_erp_chartofaccounts on srp_erp_chartofaccounts.GLAutoID=srp_erp_generalledger.GLAutoID
WHERE
srp_erp_generalledger.partyAutoID IN (" . join(',', $supplierID) . ")
    AND srp_erp_generalledger.companyID = $companyID
AND `subLedgerType` = '2'
and documentDate<='$datearr'
group by partyAutoID,transactionCurrencyID,srp_erp_chartofaccounts.GLAutoID";
        }else{
            $qry = "SELECT
  srp_erp_chartofaccounts.systemAccountCode as systemGLCode,
  srp_erp_chartofaccounts.GLDescription,
  srp_erp_suppliermaster.supplierName,
	srp_erp_suppliermaster.supplierSystemCode,
	srp_erp_suppliermaster.secondaryCode,
  companyLocalCurrency,
  transactionCurrency,
  companyReportingCurrency,
	transactionCurrencyDecimalPlaces,
	companyLocalCurrencyDecimalPlaces,
	companyReportingCurrencyDecimalPlaces,
  sum(companyLocalAmount) as companyLocalAmount,
  sum(transactionAmount) as transactionAmount,
  sum(companyReportingAmount) as  companyReportingAmount
FROM
    `srp_erp_generalledger`
INNER JOIN srp_erp_suppliermaster ON srp_erp_generalledger.partyAutoID = srp_erp_suppliermaster.supplierAutoID
LEFT JOIN srp_erp_chartofaccounts on srp_erp_chartofaccounts.GLAutoID=srp_erp_generalledger.GLAutoID
WHERE
srp_erp_generalledger.partyAutoID IN (" . join(',', $supplierID) . ")
    AND srp_erp_generalledger.companyID = $companyID
AND `subLedgerType` = '2'
and documentDate<='$datearr'
group by partyAutoID,srp_erp_chartofaccounts.GLAutoID";
        }


        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function get_segment()
    {
        $this->db->SELECT("*");
        $this->db->FROM('srp_erp_segment');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where_in('segmentID', $this->input->post('segment'));
        $result = $this->db->get()->result_array();
        $result = array_column($result, 'description');
        return $result;
    }
}