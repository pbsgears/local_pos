<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*

============================================================

-- File Name : Pos_dashboard_model.php
-- Project Name : SME POS retaturant
-- Module Name : POS Restaurant Dashboard model
-- Author : Mohamed Shafri
-- Create date : 21 - November 2016
-- Description : Dashboard for restaurant model.

--REVISION HISTORY
--Date: 21 - NOV 2016 By: Mohamed Shafri: comment started

============================================================
--Date: 21 - NOV 2016 By: Mohamed Shafri: comment started
--Date: 14 - AUG 2018 By: Mohamed Shafri: Bug fixes in the report - loaded source from menu sales payment, dashboard figures mismatched result resolved.

*/

class Pos_dashboard_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function getLastSevenDaySales()
    {
        $begin = new DateTime("6 days ago");
        $end = new DateTime(date('Y-m-d'));

        $startDate = $begin->format('Y-m-d 00:00:00');
        $endDate = $end->format('Y-m-d 23:59:59');

        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $w1 = '';
        if (!empty($wareHouseAutoID) && $wareHouseAutoID) {
            $w1 = ' AND sales.wareHouseAutoID = ' . $wareHouseAutoID;

        }
        $q = "SELECT
                sales.salesDay,
                sum( IFNULL(payment.tmpNetTotal ,0) ) AS totalSales 
            FROM
                srp_erp_pos_menusalesmaster AS sales 
                LEFT JOIN ( SELECT SUM( IFNULL( amount, 0 ) ) AS tmpNetTotal, menuSalesID FROM srp_erp_pos_menusalespayments WHERE createdDateTime BETWEEN '$startDate' AND '$endDate' GROUP BY menuSalesID ) AS payment ON sales.menuSalesID = payment.menuSalesID 
            WHERE
                sales.isHold = 0 
                AND sales.companyID = " . current_companyID() . " 
                AND sales.isVoid = 0 
                " . $w1 . "
            GROUP BY
                sales.salesDay";
        $result = $this->db->query($q)->result_array();


        return $result;
    }


    function getYTDSales()
    {
        $yearDate = date('Y-01-01');

        $this->db->select("salesDay, sum(netTotal) as totalSales");
        $this->db->from("srp_erp_pos_menusalesmaster");
        $this->db->where("DATE_FORMAT( menuSalesDate ,'%Y-%m-%d')  BETWEEN  '" . $yearDate . "' AND   NOW()  AND isHold=0 AND companyID = " . current_companyID() . " AND isVoid=0");
        $this->db->group_by('salesDay');

        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        if (!empty($wareHouseAutoID) && $wareHouseAutoID) {
            $this->db->where('wareHouseAutoID', $wareHouseAutoID);
        }
        //$result = $this->db->query($q)->result_array();
        $result = $this->db->get()->result_array();


        return $result;
    }


    function getYTDSales_dayCount()
    {
        $sqlChar = '\'' . date('Y-01-01') . '\'';

        $this->db->select("salesDay, count(salesDay) AS countSalesDay, menuSalesDate");
        $this->db->from("srp_erp_pos_menusalesmaster");
        $this->db->where("isHold = 0 AND salesDay IS NOT NULL AND companyID = " . current_companyID() . " AND DATE_FORMAT( menuSalesDate ,'%Y-%m-%d')  BETWEEN  " . $sqlChar . "  AND  DATE(NOW()) AND isVoid=0");
        $this->db->group_by('menuSalesDate');
        $this->db->order_by('salesDay');

        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        if (!empty($wareHouseAutoID) && $wareHouseAutoID) {
            $this->db->where('wareHouseAutoID', $wareHouseAutoID);
        }
        $result = $this->db->get()->result_array();
        //$result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_REVPASH($interval = 'yesterday')
    {
        switch ($interval) {
            case "WTD":
                $ObjDate = new DateTime('-1 week');
                $date = $ObjDate->format('Y-m-d');
                $sqlChar = "'" . $date . "' AND  DATE(NOW())";
                break;
            case "MTD":
                $sqlChar = '\'' . date('Y-m-01') . '\' AND  DATE(NOW())';
                break;
            case "YTD":
                $sqlChar = '\'' . date('Y-01-01') . '\' AND  DATE(NOW())';
                break;
            default :
                $ObjDate = new DateTime('-1 day');
                $date = $ObjDate->format('Y-m-d');
                $sqlChar = "'" . $date . "' AND  '" . $date . "'";
        }


        //$q = "SELECT sum(netTotal) AS totalRev, companyID FROM srp_erp_pos_menusalesmaster WHERE (DATE_FORMAT( menuSalesDate ,'%Y-%m-%d')  BETWEEN  " . $sqlChar . "   )  AND isHold = 0 AND salesDay IS NOT NULL AND companyID = " . current_companyID() . " AND isVoid=0";

        $this->db->select("sum(netTotal) AS totalRev, companyID");
        $this->db->from("srp_erp_pos_menusalesmaster");
        $this->db->where("(DATE_FORMAT( menuSalesDate ,'%Y-%m-%d')  BETWEEN  " . $sqlChar . "   )  AND isHold = 0 AND salesDay IS NOT NULL AND companyID = " . current_companyID() . " AND isVoid=0");

        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        if (!empty($wareHouseAutoID) && $wareHouseAutoID) {
            $this->db->where('wareHouseAutoID', $wareHouseAutoID);
        }
        $result = $this->db->get()->result_array();
        /*echo $this->db->last_query();
        exit;*/

        //$result = $this->db->query($q)->row_array();

        $totalRev = isset($result['totalRev']) ? $result['totalRev'] : 0;

        $seats = $this->getTotalSeats();
        $noOfSeats = $seats ? $seats : 0;
        if ($noOfSeats) {
            $hours = 12; // 12 hours per days
            $REVPASH = $totalRev / ($seats * $hours);
            return number_format($REVPASH, 1);
        } else {
            return 0;
        }


    }

    function getTotalSeats()
    {
        $this->db->select_sum("table.noOfSeats");
        $this->db->from("srp_erp_pos_diningtables table");
        $this->db->where('table.companyID', current_companyID());
        $result = $this->db->get()->row_array();
        return $result['noOfSeats'];

    }

    function get_sales_profit($interval = 'yesterday')
    {
        switch ($interval) {
            case "WTD":
                $ObjDate = new DateTime('-1 week');
                $date = $ObjDate->format('Y-m-d');
                //echo $date;
                $sqlChar = "'" . $date . "  00:00:00 ' AND  NOW()";
                break;
            case "MTD":
                $sqlChar = '\'' . date('Y-m-01 00:00:00') . '\' AND  NOW()';
                break;
            case "YTD":
                $sqlChar = '\'' . date('Y-01-01 00:00:00') . '\' AND  NOW()';
                break;
            default :
                $ObjDate = new DateTime('-1 day');
                $date = $ObjDate->format('Y-m-d');
                $sqlChar = "'" . $date . " 00:00:00' AND  '" . $date . " 23:59:59'";
        }


        //$q = "SELECT sum(netTotal) AS totalRev, sum(menuCost) as menuCost FROM srp_erp_pos_menusalesmaster WHERE (DATE_FORMAT(menuSalesDate,'%Y-%m-%d')  BETWEEN  " . $sqlChar . "   )   AND isHold = 0 AND salesDay IS NOT NULL AND companyID = " . current_companyID() . " AND isVoid=0";


        /*$this->db->select("sum( IFNULL( sales.netTotal, 0 ) ) AS totalRev, sum( IFNULL( sales.menuCost, 0 ) ) AS menuCost ");
        $this->db->from("srp_erp_pos_menusalesmaster sales");
        $this->db->join("srp_erp_pos_menusalespayments payment", "sales.menuSalesID = payment.menuSalesID", "left");
        $this->db->where("(DATE_FORMAT(sales.menuSalesDate,'%Y-%m-%d')  BETWEEN  " . $sqlChar . "   )   AND isHold = 0 AND companyID = " . current_companyID() . " AND isVoid=0");*/ /*AND salesDay IS NOT NULL */

        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $sqlChar2 = '';
        if (!empty($wareHouseAutoID) && $wareHouseAutoID) {
            //$this->db->where('sales.wareHouseAutoID', $wareHouseAutoID);
            $sqlChar2 = " AND sales.wareHouseAutoID = " . $wareHouseAutoID;
        }

        $q = "SELECT
                    sum( IFNULL( payment.tmpNetTotal, 0 ) ) AS totalRev,
                    sum( IFNULL( sales.menuCost, 0 ) ) AS menuCost 
                FROM
                    srp_erp_pos_menusalesmaster AS sales
                    LEFT JOIN (SELECT SUM( IFNULL(amount,0) ) as tmpNetTotal,  menuSalesID FROM srp_erp_pos_menusalespayments WHERE createdDateTime BETWEEN $sqlChar  GROUP BY menuSalesID ) AS payment ON sales.menuSalesID = payment.menuSalesID 
                WHERE isHold = 0 AND companyID = " . current_companyID() . $sqlChar2;

        $result = $this->db->query($q)->row_array();


        //$result = $this->db->query($q)->row_array();
        //$result = $this->db->get()->row_array();
        //echo $this->db->last_query();
        /*exit;*/
        if (!empty($result)) {
            $sales = $result['totalRev'] > 0 ? $result['totalRev'] : 0;
            $cost = $result['menuCost'] > 0 ? $result['menuCost'] : 0;
            $profit = $sales - $cost;

            return array('sales' => number_format($sales, 0, '.', ''), 'profit' => number_format($profit, 0, '.', ''));
        } else {
            return array('sales' => 0, 'profit' => 0);
        }
    }

    function get_sales_profit_generalPOS($interval = 'yesterday')
    {
        switch ($interval) {
            case "WTD":
                $ObjDate = new DateTime('-1 week');
                $date = $ObjDate->format('Y-m-d');
                $sqlChar = "'" . $date . "' AND  DATE(NOW())";
                break;
            case "MTD":
                $sqlChar = '\'' . date('Y-m-01') . '\' AND  DATE(NOW())';
                break;
            case "YTD":
                $sqlChar = '\'' . date('Y-01-01') . '\' AND  DATE(NOW())';
                break;
            default :
                $ObjDate = new DateTime('-1 day');
                $date = $ObjDate->format('Y-m-d');
                $sqlChar = "'" . $date . "' AND  '" . $date . "'";
        }

        $this->db->select("sum( IFNULL( id.transactionAmount, 0 ) ) AS totalRev, (sum( IFNULL( id.transactionAmount, 0 ) )) - (sum( IFNULL( id.wacAmount, 0 ) ) * sum( IFNULL( id.qty, 0 ) )) as profit ");
        $this->db->from("srp_erp_pos_invoicedetail id");
        $this->db->where("(DATE_FORMAT(id.createdDateTime,'%Y-%m-%d')  BETWEEN  " . $sqlChar . "   )   AND id.companyID = " . current_companyID());

        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        if (!empty($wareHouseAutoID) && $wareHouseAutoID) {
            $this->db->where('wareHouseAutoID', $wareHouseAutoID);
        }


        $result = $this->db->get()->row_array();

        if (!empty($result)) {
            $sales = $result['totalRev'];
            $profit = $result['profit'];

            return array('sales' => number_format($sales, 0, '.', ''), 'profit' => number_format($profit, 0, '.', ''));
        } else {
            return array('sales' => 0, 'profit' => 0);
        }
    }

    function get_paxCount($interval = 'yesterday')
    {
        switch ($interval) {
            case "WTD":
                $ObjDate = new DateTime('-1 week');
                $date = $ObjDate->format('Y-m-d');
                $sqlChar = "'" . $date . "' AND  DATE(NOW())";
                break;
            case "MTD":
                $sqlChar = '\'' . date('Y-m-01') . '\' AND  DATE(NOW())';
                break;
            case "YTD":
                $sqlChar = '\'' . date('Y-01-01') . '\' AND  DATE(NOW())';
                break;
            default :
                $ObjDate = new DateTime('-1 day');
                $date = $ObjDate->format('Y-m-d');
                $sqlChar = "'" . $date . "' AND  '" . $date . "'";
        }


        //$q = "SELECT count(menuSalesID) as countPax FROM srp_erp_pos_menusalesmaster WHERE DATE_FORMAT( menuSalesDate ,'%Y-%m-%d')  BETWEEN  " . $sqlChar . "   AND  isHold = 0 AND salesDay IS NOT NULL AND companyID = " . current_companyID() . " AND isVoid=0";

        //echo $q.'<hr/>';

        $this->db->select("count(menuSalesID) as countPax");
        $this->db->from("srp_erp_pos_menusalesmaster");
        $this->db->where("DATE_FORMAT( menuSalesDate ,'%Y-%m-%d')  BETWEEN  " . $sqlChar . "   AND  isHold = 0 AND salesDay IS NOT NULL AND companyID = " . current_companyID() . " AND isVoid=0");

        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        if (!empty($wareHouseAutoID) && $wareHouseAutoID) {
            $this->db->where('wareHouseAutoID', $wareHouseAutoID);
        }

        //$result = $this->db->query($q)->row_array();
        $result = $this->db->get()->row_array();


        if (!empty($result)) {
            return isset($result['countPax']) && $result['countPax'] ? $result['countPax'] : 0;

        } else {
            return 0;
        }
    }

    function getFastMovingItems($interval = 'YTD')
    {
        switch ($interval) {
            case "WTD":
                $sqlChar = "DATE(NOW()) -  INTERVAL 7 DAY ";
                break;
            case "MTD":
                $sqlChar = '\'' . date('Y-m-01') . '\' AND  DATE(NOW())';
                break;
            case "YTD":
                $sqlChar = '\'' . date('Y-01-01') . '\' AND  DATE(NOW())';
                break;
            default :
                /*YTD default */
                $sqlChar = '\'' . date('Y-01-01') . '\' AND  DATE(NOW()) ';
        }


        //$q = "SELECT menuItem.warehouseMenuID, sum(menuItem.qty) AS sumQty, warehouseMenuMaster.menuMasterID, mainMaster.menuMasterDescription FROM srp_erp_pos_menusalesmaster menuMaster LEFT JOIN srp_erp_pos_menusalesitems AS menuItem ON menuMaster.menuSalesID = menuItem.menuSalesID LEFT JOIN srp_erp_pos_warehousemenumaster warehouseMenuMaster ON warehouseMenuMaster.warehouseMenuID = menuItem.warehouseMenuID LEFT JOIN srp_erp_pos_menumaster mainMaster ON mainMaster.menuMasterID = warehouseMenuMaster.menuMasterID WHERE  menuMaster.isHold = 0 AND DATE_FORMAT( menuMaster.menuSalesDate ,'%Y-%m-%d')  BETWEEN  " . $sqlChar . "  AND menuMaster.salesDay IS NOT NULL AND menuMaster.companyID = " . current_companyID() . " AND menuMaster.isVoid=0 GROUP BY menuItem.warehouseMenuID ORDER BY sumQty DESC LIMIT 15";

        $this->db->select("menuItem.warehouseMenuID, sum(menuItem.qty) AS sumQty, warehouseMenuMaster.menuMasterID, mainMaster.menuMasterDescription");
        $this->db->from("srp_erp_pos_menusalesmaster menuMaster");
        $this->db->join("LEFT JOIN srp_erp_pos_menusalesitems AS menuItem ON menuMaster.menuSalesID = menuItem.menuSalesID LEFT JOIN srp_erp_pos_warehousemenumaster warehouseMenuMaster ON warehouseMenuMaster.warehouseMenuID = menuItem.warehouseMenuID LEFT JOIN srp_erp_pos_menumaster mainMaster ON mainMaster.menuMasterID = warehouseMenuMaster.menuMasterID");
        $this->db->where("menuMaster.isHold = 0 AND DATE_FORMAT( menuMaster.menuSalesDate ,'%Y-%m-%d')  BETWEEN  " . $sqlChar . "  AND menuMaster.salesDay IS NOT NULL AND menuMaster.companyID = " . current_companyID() . " AND menuMaster.isVoid=0");
        $this->db->group_by('menuItem.warehouseMenuID');
        $this->db->order_by('sumQty');
        $this->db->limit(15);


        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        if (!empty($wareHouseAutoID) && $wareHouseAutoID) {
            $this->db->where('wareHouseAutoID', $wareHouseAutoID);
        }


        //$result = $this->db->query($q)->result_array();
        $result = $this->db->get()->result_array();
        return $result;
    }

    function getLastSevenDaySalesGeneral()
    {
        $begin = new DateTime("6 days ago");
        $end = new DateTime(date('Y-m-d'));


        $startDate = $begin->format('Y-m-d');
        $endDate = $end->format('Y-m-d');


        $q = "SELECT invoiceDate, sum(netTotal) as totalSales , count(invoiceDate) AS countSalesDay from srp_erp_pos_invoice WHERE invoiceDate BETWEEN '" . $startDate . "' AND '" . $endDate . "' AND companyID = " . current_companyID() . "   GROUP BY invoiceDate";
        $result = $this->db->query($q)->result_array();

        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        if (!empty($wareHouseAutoID) && $wareHouseAutoID) {
            //$this->db->where('wareHouseAutoID', $wareHouseAutoID);
        }

        return $result;
    }

    function get_currentCompanyDetail()
    {
        $this->db->select("*");
        $this->db->from("srp_erp_company");
        $this->db->where('company_id', current_companyID());
        $result = $this->db->get()->row_array();

        return $result;
    }

    function get_report_generalCustomerTypeCount($date, $date2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }

        $q = "SELECT count(invoiceID) as billCount FROM srp_erp_pos_invoice  WHERE    companyID=" . current_companyID() . " AND DATE_FORMAT(invoiceDate,'%Y-%m-%d') BETWEEN '" . $date . "' AND '" . $date2 . "' " . $qString . " ";


        $result = $this->db->query($q)->row_array();
        return $result;
    }

    function get_report_generalPaymentMethod($date, $data2, $cashier = null)
    {
        /*$qString = '';
        if ($cashier != null) {
            $qString = " AND createdUserID IN(" . $cashier . ") ";
        }*/

        $q = "SELECT  SUM(cardAmount) as cardAmount , SUM(chequeAmount) as chequeAmount, SUM(cashAmount) as cashAmount, SUM(creditNoteAmount) as creditNoteAmount, SUM(netTotal) as netTotal FROM srp_erp_pos_invoice  WHERE  srp_erp_pos_invoice.companyID=" . current_companyID() . " AND DATE_FORMAT(invoiceDate,'%Y-%m-%d')  BETWEEN '" . $date . "' AND '" . $data2 . "'   ";
        $result = $this->db->query($q)->row_array();
        echo '<span class="hide">' . $this->db->last_query() . '</span>';
        return $result;
    }

    function get_itemizedSalesReport($dateFrom, $dateTo)
    {

        $this->db->select("srp_erp_pos_invoice.invoiceID, srp_erp_pos_invoice.invoiceDate, srp_erp_pos_invoicedetail.invoiceDetailsID, srp_erp_pos_invoicedetail.itemDescription, SUM(srp_erp_pos_invoicedetail.qty) as qty, SUM(srp_erp_pos_invoicedetail.transactionAmount) as price,SUM(IFNULL(srp_erp_pos_salesreturndetails.qty,0)) AS returnqty,SUM(IFNULL(srp_erp_pos_salesreturndetails.transactionAmount,0))AS returnprice");
        $this->db->from('srp_erp_pos_invoice');
        $this->db->join("srp_erp_pos_invoicedetail", "srp_erp_pos_invoice.invoiceID = srp_erp_pos_invoicedetail.invoiceID", "LEFT");
        $this->db->join("srp_erp_pos_salesreturndetails", "srp_erp_pos_invoice.invoiceID = srp_erp_pos_salesreturndetails.invoiceID", "LEFT");
        $this->db->where("srp_erp_pos_invoice.invoiceDate BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "' AND srp_erp_pos_invoice.companyID = " . current_companyID());
        $this->db->group_by("srp_erp_pos_invoicedetail.itemAutoID");


        if (isset($filter['wareHouseAutoID']) && $filter['wareHouseAutoID']) {
            $this->db->where('wareHouseAutoID', $filter['wareHouseAutoID']);
        }


        $result = $this->db->get()->result_array();

        return $result;
    }


    function productMix_menuItem($dateFrom, $dateTo)
    {
        $where = '';
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        if (!empty($wareHouseAutoID) && $wareHouseAutoID) {
            $where .= ' AND  salesMaster.wareHouseAutoID = ' . $wareHouseAutoID . ' ';
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
            AND menuMaster.menuMasterID IS NOT NULL " . $where . "
            GROUP BY
                menuMaster.menuMasterID
            ORDER BY
                menuMaster.menuMasterDescription LIMIT 15";

        $result = $this->db->query($q)->result_array();

        //echo '<span class="hide">' . $q . '</span>';

        return $result;
    }


    function productMix_menuItem_generalPOS($dateFrom, $dateTo)
    {
        $q = "SELECT id.itemDescription, sum( IFNULL( id.qty, 0 ) ) qtySum
              FROM
                srp_erp_pos_invoicedetail id
              LEFT JOIN srp_erp_pos_invoice invoice ON invoice.invoiceID = id.invoiceID
              WHERE
               id.companyID = " . current_companyID() . "
                AND DATE_FORMAT(
                    id.createdDateTime,
                    '%Y-%m-%d'
                ) BETWEEN '" . $dateFrom . "'
                AND '" . $dateTo . "'
              GROUP BY
                    id.itemSystemCode
              ORDER BY sum( IFNULL( id.qty, 0 ) ) DESC  LIMIT 15";

        $result = $this->db->query($q)->result_array();

        return $result;
    }

    function get_productMixPacks_sales($dateFrom, $dateTo)
    {
        $where = '';
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        if (!empty($wareHouseAutoID) && $wareHouseAutoID) {
            $where .= ' AND  salesMaster.wareHouseAutoID = ' . $wareHouseAutoID . ' ';
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
            AND menuMaster.menuMasterID IS NOT NULL " . $where . "
            AND salesMaster.companyID = " . current_companyID() . "
            GROUP BY
                valuePack.menuID
            ORDER BY
                menuMaster.menuMasterDescription LIMIT 15";
        $result = $this->db->query($q)->result_array();

        //echo '<div class="hide">'.$q.'</div>';

        return $result;
    }

    function get_report_generalRefundMethod($date, $data2, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND createdUserID IN(" . $cashier . ") ";
        }

        $q = "SELECT  SUM(refundAmount) as refundAmount  FROM srp_erp_pos_salesreturn  WHERE returnMode=2 AND  srp_erp_pos_salesreturn.companyID=" . current_companyID() . " AND DATE_FORMAT(salesReturnDate,'%Y-%m-%d')  BETWEEN '" . $date . "' AND '" . $data2 . "'  " . $qString . " ";
        $result = $this->db->query($q)->row_array();
        //echo '<span class="hide">' . $this->db->last_query() . '</span>';
        return $result;
    }
}