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

class Pos_kitchen_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }


    function get_pendingOrders()
    {
        $q = "SELECT
                    salesMaster.menuSalesID,
                    menuMaster.menuMasterID,
                    menuMaster.menuMasterDescription,
                    menuCategory.menuCategoryID,
                    menuCategory.menuCategoryDescription,
                    salesItem.qty,
                    menuMaster.menuImage,
                    salesMaster.invoiceCode
                FROM
                    srp_erp_pos_menusalesmaster salesMaster
                LEFT JOIN srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID
                LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID
                LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = warehouse.menuMasterID
                LEFT JOIN srp_erp_pos_menucategory menuCategory ON menuCategory.menuCategoryID = menuMaster.menuCategoryID
                WHERE
                    salesMaster.isOrderPending = 1
                AND salesMaster.isOrderInProgress = 0
                AND salesMaster.isOrderCompleted = 0
                AND DATE(salesMaster.createdDateTime ) = DATE(NOW()) AND salesMaster.companyID='" . current_companyID() . "' AND salesMaster.wareHouseAutoID='" . current_warehouseID() . "'  
                ORDER BY
                    salesMaster.menuSalesID";
        /*salesMaster.isHold = 1
                        AND */
        $result = $this->db->query($q)->result_array();


        return $result;
    }

    function get_pendingOrders_kitchen2($kitchenLocationID = null)
    {
        $counterInfo = get_counterData();
        //$shiftID = $counterInfo['shiftID'];
        $warehouseID = get_outletID(); //$counterInfo['wareHouseID'];
        $companyID = current_companyID(); //$counterInfo['companyID'];

        $q = "SELECT
                    salesMaster.menuSalesID,
                    salesItem.menuSalesItemID,
                    menuMaster.menuMasterID,
                    menuMaster.menuMasterDescription,
                    menuCategory.menuCategoryID,
                    menuCategory.menuCategoryDescription,
                    salesItem.qty,
                    menuMaster.menuImage,
                    salesItem.kitchenNote,
                    salesMaster.invoiceCode as invoiceCode,
                    salesItem.kotID as kotID,
                    salesMaster.KOTAlarm as KOTAlarm,
                    salesMaster.createdDateTime,
                    ms.description as menuDescription	
                FROM
                    srp_erp_pos_menusalesmaster salesMaster
                LEFT JOIN srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID
                LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID
                LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = warehouse.menuMasterID
                LEFT JOIN srp_erp_pos_menucategory menuCategory ON menuCategory.menuCategoryID = menuMaster.menuCategoryID
                LEFT JOIN srp_erp_pos_kitchenlocation kitchen ON kitchen.kitchenLocationID =  warehouse.kotID
                LEFT JOIN srp_erp_pos_menusize ms ON ms.menuSizeID = menuMaster.menuSizeID
                WHERE
                 -- salesItem.KOTAlarm = 1 AND 
                  -- salesMaster.isOrderPending = 1
                  -- AND salesMaster.isOrderInProgress = 0
                  -- AND salesMaster.isOrderCompleted = 0 AND 
                salesItem.isOrderPending = 1
                AND salesItem.isOrderInProgress = 0
                -- AND salesItem.isOrderCompleted = 0
                AND salesMaster.companyID='$companyID' AND salesMaster.wareHouseAutoID='$warehouseID'  
                AND kitchen.kitchenLocationID = '" . $kitchenLocationID . "' AND salesMaster.createdDateTime >= DATE_SUB(NOW(),INTERVAL 3 HOUR)
                ORDER BY
                    salesMaster.menuSalesID";
        /*AND salesMaster.shiftID  = '$shiftID' */

        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();


        return $result;
    }

    function get_invoiceIDs_pendingOrders_autoPrint($kitchenLocationID = null)
    {
        $warehouseID = get_outletID();
        $companyID = current_companyID();

        $q = "SELECT
                    salesMaster.menuSalesID,
                    salesItem.KOTAlarm as KOTAlarm
                FROM
                    srp_erp_pos_menusalesmaster salesMaster
                LEFT JOIN srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID
                LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID
                LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = warehouse.menuMasterID
                LEFT JOIN srp_erp_pos_menucategory menuCategory ON menuCategory.menuCategoryID = menuMaster.menuCategoryID
                LEFT JOIN srp_erp_pos_kitchenlocation kitchen ON kitchen.kitchenLocationID =  warehouse.kotID
                WHERE
                    salesMaster.isOrderPending = 1
                AND salesMaster.isOrderInProgress = 0
                AND salesMaster.isOrderCompleted = 0
                AND salesItem.isOrderPending = 1
                AND salesItem.isOrderInProgress = 0
                AND salesItem.isOrderCompleted = 0
                AND salesMaster.companyID='$companyID' AND salesMaster.wareHouseAutoID='$warehouseID'  
                AND salesItem.kotID = '" . $kitchenLocationID . "' AND salesMaster.createdDateTime >= DATE_SUB(NOW(),INTERVAL 3 HOUR)
                -- AND salesMaster.KOTAlarm   = 0
                AND salesItem.KOTAlarm   = 0";
        if (!empty($kitchenLocationID) && $kitchenLocationID > 0) {
            //$q .= " AND salesItem.kotID = " . $kitchenLocationID;
        }
        $q .= "
                GROUP BY salesMaster.menuSalesID
                ORDER BY
                    salesMaster.menuSalesID";
        /*AND salesMaster.shiftID  = '$shiftID' */

        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();


        return $result;
    }


    function get_pendingOrders_kitchenJava($kitchenLocationID = null)
    {
        $counterInfo = get_counterData();
        //$shiftID = $counterInfo['shiftID'];
        $warehouseID = get_outletID();
        $companyID = current_companyID();

        $q = "SELECT
                    salesMaster.menuSalesID,
                    menuMaster.menuMasterID,
                    menuMaster.menuMasterDescription,
                    menuCategory.menuCategoryID,
                    menuCategory.menuCategoryDescription,
                    salesItem.qty,
                    menuMaster.menuImage,
                    salesItem.kitchenNote,
                    salesMaster.invoiceCode as invoiceCode,
                    salesItem.kotID as kotID
                FROM
                    srp_erp_pos_menusalesmaster salesMaster
                LEFT JOIN srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID
                LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID
                LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = warehouse.menuMasterID
                LEFT JOIN srp_erp_pos_menucategory menuCategory ON menuCategory.menuCategoryID = menuMaster.menuCategoryID
                LEFT JOIN srp_erp_pos_kitchenlocation kitchen ON kitchen.kitchenLocationID =  warehouse.kotID
                WHERE
                salesMaster.isOrderPending = 1 AND 
                    salesMaster.companyID='$companyID' AND salesMaster.wareHouseAutoID='$warehouseID'  
                AND kitchen.kitchenLocationID = '" . $kitchenLocationID . "'
                AND salesMaster.createdDateTime >= DATE_SUB(NOW(),INTERVAL 6 HOUR)
                ORDER BY
                    salesMaster.menuSalesID DESC LIMIT " . get_kitchenLimit();
        /*salesMaster.shiftID  = '$shiftID' AND */
        //echo $q;

        $result = $this->db->query($q)->result_array();

        return $result;
    }

    function get_kot_nTh_record($kitchenLocationID = null, $n = 10)
    {
        $warehouseID = get_outletID();
        $companyID = current_companyID();
        $q = "SELECT
                    salesMaster.menuSalesID
                FROM
                    srp_erp_pos_menusalesmaster salesMaster
                    LEFT JOIN srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID
                    LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID
                    LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = warehouse.menuMasterID
                    LEFT JOIN srp_erp_pos_menucategory menuCategory ON menuCategory.menuCategoryID = menuMaster.menuCategoryID
                    LEFT JOIN srp_erp_pos_kitchenlocation kitchen ON kitchen.kitchenLocationID =  warehouse.kotID
                WHERE
                salesMaster.isOrderPending = 1 AND 
                    salesMaster.companyID='$companyID' AND salesMaster.wareHouseAutoID='$warehouseID'  
                AND salesMaster.createdDateTime >= DATE_SUB(NOW(),INTERVAL 6 HOUR)
                AND kitchen.kitchenLocationID = '" . $kitchenLocationID . "'
                ORDER BY
                   salesMaster.menuSalesID DESC 
	            LIMIT " . $n;

        $result = $this->db->query($q)->result_array();

        return $result;
    }

    function get_kot_betweenValues($kitchenLocationID)
    {
        $array[0]['start'] = 0;
        $array[0]['end'] = 0;
        $array[1]['start'] = 0;
        $array[1]['end'] = 0;

        $result = $this->get_kot_nTh_record($kitchenLocationID, 20);

        if (!empty($result)) {
            if (count($result) > 10) {
                $i = 0;
                foreach ($result as $tmp) {
                    if ($i == 0) {
                        $array[1]['end'] = $tmp['menuSalesID'];
                    } else if ($i == 4) {
                        $array[1]['start'] = $tmp['menuSalesID'];
                    } else if ($i == 5) {
                        $array[0]['end'] = $tmp['menuSalesID'];
                    } else if ($i == 9) {
                        $array[0]['start'] = $tmp['menuSalesID'];
                    }
                    $i++;
                }

            }
        }
        return $array;
    }

    function get_nTh_set($kitchenLocationID = null, $n = 0)
    {
        $result = $this->get_kot_betweenValues($kitchenLocationID);
        $limit = '';
        $q_string = '';
        switch ($n) {
            case 0:
                $betweenFrom = $result[0];
                $start = $betweenFrom['start'];
                $end = $betweenFrom['end'];
                if ($start != 0 && $end != 0) {
                    $q_string = " AND salesMaster.menuSalesID BETWEEN '" . $start . "' AND '" . $end . "' ";
                } else {
                    $limit = " LIMIT 10 ";
                }
                break;
            case 1:
                $betweenFrom = $result[1];
                $start = $betweenFrom['start'];
                $end = $betweenFrom['end'];
                if ($start != 0 && $end != 0) {
                    $q_string = " AND salesMaster.menuSalesID BETWEEN '" . $start . "' AND '" . $end . "' ";
                } else {
                    $limit = " LIMIT 0 ";
                }
                break;
            default:
                echo "error";
                exit;
        }

        $warehouseID = get_outletID();
        $companyID = current_companyID();

        $q = "SELECT
                    salesMaster.menuSalesID,
                    menuMaster.menuMasterID,
                    menuMaster.menuMasterDescription,
                    menuCategory.menuCategoryID,
                    menuCategory.menuCategoryDescription,
                    salesItem.qty,
                    menuMaster.menuImage,
                    salesItem.kitchenNote,
                    salesMaster.invoiceCode as invoiceCode,
                    salesItem.kotID as kotID,
                    salesMaster.KOTAlarm as KOTAlarm
                FROM
                    srp_erp_pos_menusalesmaster salesMaster
                LEFT JOIN srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID
                LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID
                LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = warehouse.menuMasterID
                LEFT JOIN srp_erp_pos_menucategory menuCategory ON menuCategory.menuCategoryID = menuMaster.menuCategoryID
                LEFT JOIN srp_erp_pos_kitchenlocation kitchen ON kitchen.kitchenLocationID =  warehouse.kotID
                WHERE
                salesMaster.isOrderPending = 1 AND 
                    salesMaster.companyID='$companyID' AND salesMaster.wareHouseAutoID='$warehouseID'  
                AND kitchen.kitchenLocationID = '" . $kitchenLocationID . "'
                AND salesMaster.createdDateTime >= DATE_SUB(NOW(),INTERVAL 6 HOUR)"
            . $q_string . "
                ORDER BY
                    salesMaster.menuSalesID " . $limit;

        //echo $q;

        $result = $this->db->query($q)->result_array();

        return $result;
    }


    function get_pendingOrdersCount()
    {
        $q = "SELECT
                    count(salesMaster.menuSalesID) as countMenuSales
                FROM
                    srp_erp_pos_menusalesmaster salesMaster
                WHERE
                    salesMaster.isOrderPending = 1
                AND salesMaster.isOrderInProgress = 0
                AND salesMaster.isOrderCompleted = 0
                AND DATE(salesMaster.createdDateTime ) = DATE(NOW()) ";
        /*salesMaster.isHold = 1
                        AND */
        $result = $this->db->query($q)->row_array();

        return $result;
    }


    function get_currentOrders($kitchenLocationID = null)
    {
        $counterInfo = get_counterData();
        $shiftID = $counterInfo['shiftID'];
        $warehouseID = $counterInfo['wareHouseID'];
        $companyID = $counterInfo['companyID'];

        $q = "SELECT
                    salesMaster.menuSalesID,
                    menuMaster.menuMasterID,
                    menuMaster.menuMasterDescription,
                    menuCategory.menuCategoryID,
                    menuCategory.menuCategoryDescription,
                    salesItem.qty,
                    menuMaster.menuImage
                FROM
                    srp_erp_pos_menusalesmaster salesMaster
                LEFT JOIN srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID
                LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID
                LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = warehouse.menuMasterID
                LEFT JOIN srp_erp_pos_menucategory menuCategory ON menuCategory.menuCategoryID = menuMaster.menuCategoryID
                LEFT JOIN srp_erp_pos_kitchenlocation kitchen ON kitchen.kitchenLocationID = warehouse.kotID 
                WHERE
                salesItem.isOrderPending = 0
                AND salesItem.isOrderInProgress = 1 
                AND salesItem.isOrderCompleted = 0 
                AND salesMaster.shiftID = '$shiftID' 
                AND salesMaster.companyID = '$companyID' 
                AND salesMaster.wareHouseAutoID = '$warehouseID' 
                AND kitchen.kitchenLocationID = '" . $kitchenLocationID . "'
                
                    
                ORDER BY
                    salesMaster.menuSalesID";
        /*salesMaster.isHold = 1
                        AND */
        //echo $q;
        $result = $this->db->query($q)->result_array();

        return $result;
    }

    function get_currentOrders_kitchen2($kitchenLocationID = null)
    {
        $counterInfo = get_counterData();
        //$shiftID = $counterInfo['shiftID'];
        $warehouseID = get_outletID();//  $counterInfo['wareHouseID'];
        $companyID = current_companyID(); // $counterInfo['companyID'];

        $q = "SELECT
                    salesMaster.menuSalesID,
                    salesItem.menuSalesItemID,
                    menuMaster.menuMasterID,
                    menuMaster.menuMasterDescription,
                    menuCategory.menuCategoryID,
                    menuCategory.menuCategoryDescription,
                    salesItem.qty,
                    menuMaster.menuImage,
                    salesItem.kitchenNote,
                    salesMaster.invoiceCode as invoiceCode,
                    salesItem.kotID as kotID,
                    salesMaster.createdDateTime,
                    ms.description as menuDescription
                FROM
                    srp_erp_pos_menusalesmaster salesMaster
                LEFT JOIN srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID
                LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID
                LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = warehouse.menuMasterID
                LEFT JOIN srp_erp_pos_menucategory menuCategory ON menuCategory.menuCategoryID = menuMaster.menuCategoryID
                LEFT JOIN srp_erp_pos_kitchenlocation kitchen ON kitchen.kitchenLocationID =  warehouse.kotID
                LEFT JOIN srp_erp_pos_menusize ms ON ms.menuSizeID = menuMaster.menuSizeID
                
                WHERE
                -- salesMaster.KOTAlarm = 1  AND 
                salesItem.isOrderPending = 1 
                AND salesItem.isOrderInProgress = 1 
                AND salesItem.isOrderCompleted = 0 
                
                AND salesMaster.companyID = '$companyID' 
                AND salesMaster.wareHouseAutoID = '$warehouseID' 
                AND kitchen.kitchenLocationID = '$kitchenLocationID' 
                AND salesMaster.createdDateTime >= DATE_SUB(NOW(),INTERVAL 3 HOUR) 
                ORDER BY
                    salesMaster.menuSalesID";
        /*salesMaster.isHold = 1
                        AND */
        /*AND salesMaster.shiftID = '$shiftID' */
        $result = $this->db->query($q)->result_array();
        //echo $q;

        return $result;
    }

    function get_currentOrdersCount()
    {
        $q = "SELECT
                    count(salesMaster.menuSalesID) as currentCount
                FROM
                    srp_erp_pos_menusalesmaster salesMaster
                WHERE
                    salesMaster.isOrderInProgress = 1
                AND salesMaster.isOrderCompleted = 0
                AND DATE(salesMaster.createdDateTime ) = DATE(NOW())";
        /*salesMaster.isHold = 1
                        AND */
        $result = $this->db->query($q)->row_array();

        return $result;
    }


    function update_srp_erp_pos_menusalesmaster($data, $id)
    {
        $data['is_sync'] = 0;
        $data['id_store'] = current_warehouseID();
        $this->db->where('menuSalesID', $id);
        $this->db->where('wareHouseAutoID', current_warehouseID());
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);
        return $result;
    }

    function completeOrder_master($id)
    {
        $data['isOrderPending'] = 1;
        $data['isOrderInProgress'] = 1;
        $data['isOrderCompleted'] = 1;
        $this->db->where('menuSalesID', $id);
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);
        return $result;
    }

    function progressOrder_master($id)
    {
        $data['isOrderPending'] = 1;
        $data['isOrderInProgress'] = 1;
        $data['isOrderCompleted'] = 0;
        $this->db->where('menuSalesID', $id);
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);
        return $result;
    }

    /*Old Code Reference */

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
                                 AND t2.companyID={$companyID} AND wareHouseAutoID ={$wareHouseID} AND isActive=1")->result_array();

    }


    function updateSendToKitchen()
    {
        $invoiceID = $this->input->post('menuSalesID');
        $data['isOrderPending'] = 1;
        $this->db->where('menuSalesID', $invoiceID);
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);

        if ($result) {

            $q = "UPDATE `srp_erp_pos_menusalesitems` SET `KOTAlarm` = 0, `isOrderPending` = 1, isSamplePrinted = 1   WHERE `menuSalesID` = '" . $invoiceID . "' AND ( `KOTAlarm` = -1 OR `isOrderPending` = -1)";
            $result = $this->db->query($q);

            if ($result) {
                $tmpData['isOrderPending'] = 1;

                $this->db->select('menuSalesID');
                $this->db->from('srp_erp_pos_menusalesitems');
                $this->db->where('isOrderInProgress', 1);
                $this->db->where('menuSalesID', $invoiceID);
                $result = $this->db->get()->row_array();

                if ($result) {
                    $tmpData['isOrderInProgress'] = 1;
                } else {
                    $tmpData['isOrderInProgress'] = 0;
                }

                $tmpData['isOrderCompleted'] = 0;
                $this->db->where('menuSalesID', $invoiceID);
                $this->db->update('srp_erp_pos_menusalesmaster', $tmpData);
            }

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
        $refCode = $this->sequence->sequence_generator('REF - H', $lastRefNo);

        return array('refCode' => $refCode, 'lastRefNo' => $lastRefNo);
    }

    function get_wareHouse()
    {
        $this->db->select('wHouse . wareHouseAutoID, wareHouseCode, wareHouseDescription, wareHouseLocation, segmentID, segmentCode')
            ->from('srp_erp_warehousemaster wHouse')
            ->join('srp_erp_pos_segmentconfig conf', 'conf . wareHouseAutoID = wHouse . wareHouseAutoID', 'left')
            ->where('wHouse . wareHouseAutoID', $this->common_data['ware_houseID']);
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
            $invoiceDate = format_date(date('Y - m - d'));

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

            /*echo ' < pre>';print_r($tr_currency);echo ' </pre > ';
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

            /*echo ' < pre>';print_r($cusData);echo ' </pre > ';
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
                echo ' < p>$itemQty[$i]: '.$itemQty[$i];
                echo ' < p>conversionRate: '.$conversionRate;
                echo ' < p>availableQTY: '.$availableQTY;
                echo ' < p>QTY: '.$qty; die();*/

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

                /* echo ' < p>conversion:'.$conversion.' </p ><p > qty:'.$qty.' </p ><p > itemQty:'.$itemQty[$i].' </p > ';
                 echo '<p > AVlQty:'.$availableQTY.' </p > ';
                 echo '<p > balanceQty:'.$balanceQty.' </p > ';
                 die();*/
                $itemUpdateWhere = array('itemAutoID' => $itemID, 'wareHouseAutoID' => $this->common_data['ware_houseID']);
                $itemUpdateQty = array('currentStock' => $balanceQty);
                $this->db->where($itemUpdateWhere)->update('srp_erp_warehouseitems', $itemUpdateQty);


                $i++;
                //}
                /*else {
                    $this->db->trans_rollback();
                    return array('e', '[' . $itemData['itemSystemCode'] . ' - ' . $itemData['itemDescription'] . ' ]<p > is available only ' . $availableQTY . ' qty');
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
            return array('e', 'You have not a valid session .<p > Please login and try again .</p > ');
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
        $invoiceDate = format_date(date('Y - m - d'));

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
                                 FROM srp_erp_pos_invoice t1 WHERE companyID={$companyID} AND wareHouseAutoID={$wareHouse}")->result_array();
    }

    function invoice_search()
    {
        $companyID = current_companyID();
        $wareHouse = $this->common_data['ware_houseID'];
        $invoiceCode = $this->input->post('invoiceCode');

        $isExistInv = $this->db->query("SELECT t1.*, if(customerID=0 , 'Cash',
                                  (SELECT customerName FROM srp_erp_customermaster WHERE  customerAutoID=t1.customerID)) AS cusName,
                                  (SELECT sum(balanceAmount) FROM srp_erp_pos_invoice WHERE customerID = t1.customerID) AS cusBalance
                                  FROM srp_erp_pos_invoice t1 WHERE companyID={$companyID} AND wareHouseAutoID={$wareHouse} AND
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
                                 FROM srp_erp_pos_invoicehold t1 WHERE companyID={$companyID} AND wareHouseAutoID={$wareHouse}")->result_array();
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
        return $this->db->select('counterID, counterCode, counterName')->from('srp_erp_pos_counters')
            ->where('wareHouseID', $wareHouse)->where('isActive', 1)
            ->get()->result_array();
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
        $result = $this->db->get()->result_array();

        /*echo $this->db->last_query();*/
        return $result;
    }

    function get_warehouseCategory($warehouseID)
    {
        $path = base_url();
        $this->db->select("warehouseCategory.autoID, category.menuCategoryID, category.menuCategoryDescription as description, concat('" . $path . "',category.image) as image");
        $this->db->from("srp_erp_pos_warehousemenucategory warehouseCategory");
        $this->db->join("srp_erp_pos_menucategory category", "warehouseCategory.menuCategoryID = category.menuCategoryID", "INNER");
        $this->db->where('category.isActive', 1);
        $this->db->where('warehouseCategory.isDeleted', 0);
        $this->db->where('warehouseCategory.isActive', 1);
        $this->db->where('category.companyID', current_companyID());
        $this->db->where('category.isDeleted', 0);
        $this->db->where('warehouseCategory.warehouseID', $warehouseID);
        $result = $this->db->get()->result_array();
        return $result;

    }

    function get_warehouseMenu_specific($warehouseMenuID)
    {
        $path = base_url();
        $this->db->select("category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice, menu.warehouseMenuCategoryID, menuMaster.menuCost, menuMaster.isPack , categoryMaster.revenueGLAutoID");
        $this->db->from("srp_erp_pos_warehousemenumaster menu");
        $this->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $this->db->join("srp_erp_pos_menucategory categoryMaster", "categoryMaster.menuCategoryID = menuMaster.menuCategoryID", "INNER");
        $this->db->where('menu.isActive', 1);
        $this->db->where('menu.isDeleted', 0);
        $this->db->where('menuMaster.isDeleted', 0);
        $this->db->where('menu.warehouseMenuID', $warehouseMenuID);
        $result = $this->db->get()->row_array();
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

    function get_srp_erp_pos_menusalesitems_invoiceID($invoiceID)
    {
        $outletID = get_outletID();
        $path = base_url();
        $this->db->select("sales.menuSalesID, sales.menuSalesItemID, category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice, sales.qty , sales.discountPer, sales.discountAmount, menuMaster.menuMasterID, sales.isOrderPending,sales.isOrderInProgress, sales.isOrderCompleted, sales.kotID, KOTLocation.description  as KOT_description");
        $this->db->from("srp_erp_pos_menusalesitems sales");
        $this->db->join("srp_erp_pos_warehousemenumaster menu", "menu.warehouseMenuID = sales.warehouseMenuID");
        $this->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $this->db->join("srp_erp_pos_warehousemenumaster wMenu", "wMenu.menuMasterID = menu.menuMasterID", "inner");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $this->db->join("srp_erp_pos_kitchenlocation KOTLocation", "KOTLocation.kitchenLocationID = sales.kotID", "left");
        $this->db->where('menu.isActive', 1);
        $this->db->where('menu.isDeleted', 0);
        $this->db->where('menuMaster.isDeleted', 0);
        $this->db->where('sales.menuSalesID', $invoiceID);
        $this->db->where('sales.id_store', $outletID);
        $this->db->group_by('sales.menuSalesItemID');
        $result = $this->db->get()->result_array();
        return $result;
    }


    function get_srp_erp_pos_menusalesitems_specific($menuSalesItemID)
    {

        $this->db->select("master.*");
        $this->db->from("srp_erp_pos_menusalesitems sales");
        $this->db->join("srp_erp_pos_warehousemenumaster warehouse", 'warehouse.warehouseMenuID = sales.warehouseMenuID', 'left');
        $this->db->join("srp_erp_pos_menumaster master", 'warehouse.menuMasterID = master.menuMasterID', 'left');
        $this->db->where('sales.menuSalesItemID', $menuSalesItemID);
        $this->db->where('sales.id_store', current_warehouseID());
        $result = $this->db->get()->row_array();
        return $result;
    }

    function delete_menuSalesItem($id)
    {
        $this->db->where('menuSalesItemID', $id);
        $this->db->where('id_store', current_warehouseID());
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
        $result = $this->db->get()->row_array();
        return $result['wareHouseID'];
    }


    function get_srp_erp_pos_menusalesmaster($id)
    {
        $this->db->select("*");
        $this->db->from("srp_erp_pos_menusalesmaster");
        $this->db->where('menuSalesID', $id);
        $this->db->where('wareHouseAutoID', current_warehouseID());
        $result = $this->db->get()->row_array();
        return $result;
    }

    function load_posHoldReceipt()
    {
        $this->db->select("*");
        $this->db->from("srp_erp_pos_menusalesmaster");
        $this->db->where('isHold', 0);
        $this->db->where('wareHouseAutoID', current_warehouseID());
        $result = $this->db->get()->result_array();
        return $result;

    }

    function updateTotalCost($invoiceID)
    {
        $q = "UPDATE srp_erp_pos_menusalesmaster AS salesMaster, ( SELECT sum(detailTbl.menuCost) AS menuCostTmp FROM srp_erp_pos_menusalesitems AS detailTbl WHERE detailTbl.menuSalesID = " . $invoiceID . " AND detailTbl.id_store = " . current_warehouseID() . " ) tmp SET salesMaster.menuCost = tmp.menuCostTmp , salesMaster.is_sync=0 WHERE salesMaster.menuSalesID = '" . $invoiceID . "'  AND salesMaster.wareHouseAutoID='" . current_warehouseID() . "'";
        $result = $this->db->query($q);
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
        $this->db->select("*");
        $this->db->from("srp_erp_pos_menupackitem");
        $this->db->where('isRequired', 1);
        $this->db->where('PackMenuID', $menuMasterID);
        $result = $this->db->get()->result_array();
        return $result;
    }

    function bulk_insert_srp_erp_pos_valuepackdetail($data)
    {
        $this->db->insert_batch('srp_erp_pos_valuepackdetail', $data);
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

    function get_report_customerTypeCount($date, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }

        $q = "SELECT customertype.customerDescription,count(salesMaster.netTotal) as countTotal FROM srp_erp_pos_menusalesmaster AS salesMaster JOIN srp_erp_customertypemaster customertype ON customertype.customerTypeID = salesMaster.customerTypeID WHERE salesMaster.isHold = 0 AND salesMaster.companyID=" . current_companyID() . " AND salesMaster.menuSalesDate BETWEEN '" . $date . "' AND NOW() " . $qString . " GROUP BY salesMaster.customerTypeID";

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_paymentMethod($date, $cashier = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND createdUserID IN(" . $cashier . ") ";
        }

        $q = "SELECT payment.paymentDescription, SUM(salesMaster.netTotal) as NetTotal FROM srp_erp_pos_menusalesmaster AS salesMaster JOIN srp_erp_pos_paymentmethods as payment ON payment.paymentMethodsID= salesMaster.paymentMethod WHERE salesMaster.isHold = 0 AND salesMaster.companyID=" . current_companyID() . " AND salesMaster.menuSalesDate BETWEEN '" . $date . "' AND NOW()  " . $qString . "  GROUP BY salesMaster.paymentMethod";
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    function get_itemizedSalesReport($dateFrom, $dateTo)
    {
        $q = "SELECT menuMaster.menuMasterID, menuMaster.menuMasterDescription, menuCategory.menuCategoryID, menuCategory.menuCategoryDescription, sum( salesItem.salesPriceNetTotal ) AS itemPriceTotal, sum(salesItem.qty) AS qty FROM srp_erp_pos_menusalesmaster salesMaster LEFT JOIN srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = warehouse.menuMasterID LEFT JOIN srp_erp_pos_menucategory menuCategory ON menuCategory.menuCategoryID = menuMaster.menuCategoryID WHERE salesMaster.isHold = 0 AND salesMaster.companyID = " . current_companyID() . " AND salesMaster.menuSalesDate BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "' AND menuMaster.menuMasterID IS NOT NULL GROUP BY menuMaster.menuMasterID ORDER BY menuCategory.menuCategoryID";
        $result = $this->db->query($q)->result_array();

        /*echo $q;
        echo '<br/>';
        echo '<br/>';*/

        return $result;
    }

    function get_srp_erp_pos_menudetails_by_menuMasterID($menuMasterID)
    {
        $this->db->select("menu.*,item.costGLAutoID, assteGLAutoID as assetGLAutoID");
        $this->db->from("srp_erp_pos_menudetails menu");
        $this->db->join("srp_erp_itemmaster item", "item.itemAutoID = menu.itemAutoID", "LEFT");
        $this->db->where('menuMasterID', $menuMasterID);
        $result = $this->db->get()->result_array();
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
                    item.menuSalesID = " . $menusSalesID . " AND item.id_store = " . current_warehouseID() . " 
                GROUP BY
                    revenueGLAutoID";
        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_srp_erp_pos_menusalesmaster_kitchenStatus($id)
    {
        $this->db->select("menuSales.*, cType.customerDescription, delivery.deliveryOrderID, delivery.deliveryDate, delivery.deliveryTime, table.diningTableDescription, crew.crewLastName as crewLastName");
        $this->db->from("srp_erp_pos_menusalesmaster menuSales");
        $this->db->join('srp_erp_customertypemaster cType', 'cType.customerTypeID = menuSales.customerTypeID', 'left'); /*customerTypeID*/
        $this->db->join('srp_erp_pos_deliveryorders delivery', 'delivery.menuSalesMasterID = menuSales.menuSalesID', 'left');
        $this->db->join('srp_erp_pos_diningtables table', 'table.diningTableAutoID = menuSales.tableID', 'left');
        $this->db->join('srp_erp_pos_crewmembers crew', 'crew.crewMemberID = menuSales.waiterID', 'left');
        $this->db->where('menuSales.menuSalesID', $id);
        $this->db->where('menuSales.wareHouseAutoID', current_warehouseID());
        $result = $this->db->get()->row_array();
        return $result;
    }


    function get_srp_erp_pos_menusalesitems_invoiceID_kotAlarm($invoiceID, $kotID = null)
    {
        $outletID = get_outletID();
        $path = base_url();
        $this->db->select("sales.menuSalesID, sales.menuSalesItemID, category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice, sales.qty , sales.discountPer, sales.discountAmount, menuMaster.menuMasterID, sales.isOrderPending,sales.isOrderInProgress, sales.isOrderCompleted, sales.kotID, KOTLocation.description  as KOT_description, sales.kitchenNote");
        $this->db->from("srp_erp_pos_menusalesitems sales");
        $this->db->join("srp_erp_pos_warehousemenumaster menu", "menu.warehouseMenuID = sales.warehouseMenuID");
        $this->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $this->db->join("srp_erp_pos_warehousemenumaster wMenu", "wMenu.menuMasterID = menu.menuMasterID", "inner");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $this->db->join("srp_erp_pos_kitchenlocation KOTLocation", "KOTLocation.kitchenLocationID = sales.kotID", "inner");
        $this->db->where('menu.isActive', 1);
        $this->db->where('menu.isDeleted', 0);
        $this->db->where('menuMaster.isDeleted', 0);
        $this->db->where('sales.menuSalesID', $invoiceID);
        $this->db->where('sales.id_store', $outletID);
        $this->db->where('sales.KOTAlarm', 0);
        if (!empty($kotID) && $kotID > 0) {
            $this->db->where('sales.kotID', $kotID);
        }
        $this->db->group_by('sales.menuSalesItemID');
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_srp_erp_pos_menusalesitems_invoiceID_kotPrint($invoiceID)
    {
        $outletID = get_outletID();
        $path = base_url();
        $this->db->select("sales.menuSalesID, sales.menuSalesItemID, category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice, sales.qty , sales.discountPer, sales.discountAmount, menuMaster.menuMasterID, sales.isOrderPending,sales.isOrderInProgress, sales.isOrderCompleted, sales.kotID, KOTLocation.description  as KOT_description, sales.kitchenNote, KOTLocation.description as kitchenName, sales.kotID as kotID");
        $this->db->from("srp_erp_pos_menusalesitems sales");
        $this->db->join("srp_erp_pos_warehousemenumaster menu", "menu.warehouseMenuID = sales.warehouseMenuID");
        $this->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $this->db->join("srp_erp_pos_warehousemenumaster wMenu", "wMenu.menuMasterID = menu.menuMasterID", "inner");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $this->db->join("srp_erp_pos_kitchenlocation KOTLocation", "KOTLocation.kitchenLocationID = sales.kotID", "inner");
        $this->db->where('menu.isActive', 1);
        $this->db->where('menu.isDeleted', 0);
        $this->db->where('menuMaster.isDeleted', 0);
        $this->db->where('sales.menuSalesID', $invoiceID);
        $this->db->where('sales.id_store', $outletID);
        $this->db->where('sales.KOTFrontPrint', 0);
        $this->db->where('sales.isOrderInProgress', 0);
        $this->db->group_by('sales.menuSalesItemID');
        $this->db->order_by('sales.kotID');
        $result = $this->db->get()->result_array();
        return $result;
    }

}