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

class Pos_config_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function save_posConfig($data)
    {
        $result = $this->db->insert('srp_erp_pos_segmentconfig', $data);
        if ($result) {
            return array('error' => 0, 'message' => 'successfully saved', 'result' => $result);
        } else {
            return array('error' => 1, 'message' => 'Error while insert, Please contact your system support team');
        }
    }

    function validate_posConfig()
    {
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $segmentID = $this->input->post('segmentID');
        $industrytypeID = $this->input->post('industrytypeID');
        $posTemplateID = $this->input->post('posTemplateID');

        $this->db->select('*');
        $this->db->from('srp_erp_pos_segmentconfig');
        $this->db->where('wareHouseAutoID', $wareHouseAutoID);
        /*$this->db->where('segmentID', $segmentID);
        $this->db->where('industrytypeID', $industrytypeID);
        $this->db->where('posTemplateID', $posTemplateID);*/
        $this->db->where('isActive', -1);
        $result = $this->db->get()->row_array();
        if (empty($result)) {
            return true;
        } else {
            return false;
        }

    }


    function update_segmentConfig($id, $data)
    {
        $this->db->where('segmentConfigID', $id);
        $result = $this->db->update('srp_erp_pos_segmentconfig', $data);
        return $result;
    }

    function validate_delete_segmentConfig()
    {
        $id = $this->input->post('id');
        $result = false;
        $this->db->select("wareHouseAutoID,segmentID");
        $this->db->from("srp_erp_pos_segmentconfig");
        $this->db->where("segmentConfigID", $id);
        $tmp = $this->db->get()->row_array();

        if (!empty($tmp)) {
            /*print_r($tmp);
            exit;*/
            $this->db->select('menuSalesID');
            $this->db->from('srp_erp_pos_menusalesmaster');
            $this->db->where('wareHouseAutoID', $tmp['wareHouseAutoID']);
            $this->db->where('segmentID', $tmp['segmentID']);
            $menuSalesID = $this->db->get()->row('menuSalesID');

            if (!$menuSalesID) {
                $result = true;
            }
        }


        return $result;

    }

    function getMenuCategories($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_menucategory');
        $this->db->where('wareHouseAutoID', $id);
        $this->db->where('isDeleted', 0);
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function getMenuCategories_setup($id)
    {
        $this->db->select('mc.*,wc.autoID,wc.isActive as Active,wc.isDeleted');
        $this->db->from('srp_erp_pos_warehousemenucategory wc');
        $this->db->join('srp_erp_pos_menucategory mc', 'mc.menuCategoryID = wc.menuCategoryID', 'left');
        $this->db->where('warehouseID', $id);
        $this->db->where('mc.isDeleted', 0);
        $this->db->where('mc.isActive', 1);
        $this->db->where('wc.isDeleted', 0);
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function getMenuCategories_company($companyID)
    {

        $masterLevelID = $this->input->post('masterLevelID');
        $levelNo = $this->input->post('levelNo');
        $levelNo = $levelNo > 0 ? $levelNo : 0;
        $masterLevelID = $masterLevelID > 0 ? $masterLevelID : 0;

        $this->db->select('menuCategory.*,concat(chartOfAccount.systemAccountCode," - ",chartOfAccount.GLDescription ) as GLDesc');
        $this->db->from('srp_erp_pos_menucategory menuCategory');
        $this->db->join('srp_erp_chartofaccounts chartOfAccount', 'chartOfAccount.GLAutoID = menuCategory.revenueGLAutoID', 'left');
        $this->db->where('menuCategory.companyID', $companyID);
        $this->db->where('menuCategory.isDeleted', 0);

        $this->db->where('menuCategory.levelNo', $levelNo);
        if ($masterLevelID > 0 && $masterLevelID) {
            $this->db->where('menuCategory.masterLevelID', $masterLevelID);
        }
        $this->db->order_by('menuCategory.sortOrder', "asc");
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_srp_erp_pos_segmentconfig_specific($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_segmentconfig');
        $this->db->where('segmentConfigID', $id);
        $result = $this->db->get()->row_array();
        //echo $this->db->last_query();
        return $result;

    }

    function addMenuCategory($data)
    {
        $result = $this->db->insert('srp_erp_pos_menucategory', $data);
        $insert_id = $this->db->insert_id();
        if ($result) {
            return array('error' => 0, 'message' => 'successfully saved', 'result' => $result, 'insert_id' => $insert_id);
        } else {
            return array('error' => 1, 'message' => 'Error while insert, Please contact your system support team');
        }
    }


    /** MENU Category Setup */
    function add_srp_erp_pos_warehousemenucategory($data)
    {
        $result = $this->db->insert('srp_erp_pos_warehousemenucategory', $data);
        if ($result) {
            return array('error' => 0, 'message' => 'successfully saved', 'result' => $result, 'id' => $this->db->insert_id());
        } else {
            return array('error' => 1, 'message' => 'Error while insert, Please contact your system support team');
        }
    }

    function validate_menuCategory($categoryID, $warehouseID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_warehousemenucategory');
        $this->db->where('menuCategoryID', $categoryID);
        $this->db->where('warehouseID', $warehouseID);
        $this->db->where('companyID', current_companyID());
        $this->db->where('isDeleted', 0);
        $result = $this->db->get()->row_array();
        if (empty($result)) {
            return true;
        } else {
            return false;
        }
    }

    function get_srp_erp_pos_menucategory_specific($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_menucategory');
        $this->db->where('menuCategoryID', $id);
        $result = $this->db->get()->row_array();
        return $result;

    }

    function get_srp_erp_pos_warehousemenucategory_specific($id)
    {
        $this->db->select('mc.*, wmc.warehouseID, wmc.autoID, wmc.menuCategoryID');
        $this->db->from('srp_erp_pos_warehousemenucategory wmc');
        $this->db->join('srp_erp_pos_menucategory mc', 'mc.menuCategoryID = wmc.menuCategoryID', 'inner');
        $this->db->where('autoID', $id);
        $result = $this->db->get()->row_array();
        return $result;

    }

    /** Menu Category : update */
    function updateMenuCategory($data, $id)
    {
        $this->db->where('menuCategoryID', $id);
        $result = $this->db->update('srp_erp_pos_menucategory', $data);
        return $result;
    }


    /** Menu : get */
    function get_srp_erp_pos_menumaster_specific($id)
    {
        $this->db->select('menuMaster.*,menuCategory.menuCategoryDescription');
        $this->db->from('srp_erp_pos_menumaster menuMaster');
        $this->db->join('srp_erp_pos_menucategory menuCategory', 'menuCategory.menuCategoryID  = menuMaster.menuCategoryID', 'left');
        $this->db->where('menuMaster.menuMasterID', $id);
        $result = $this->db->get()->row_array();
        if (!empty($result)) {
            $result['menuImage'] = base_url($result['menuImage']);
        }
        return $result;

    }

    function get_srp_erp_pos_menumaster_all_active()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_menumaster');
        $this->db->where('isDeleted', 0);
        $this->db->where('companyID', current_companyID());
        $result = $this->db->get()->result_array();
        return $result;

    }

    /** Menu : add */
    function addMenu($data)
    {
        $result = $this->db->insert('srp_erp_pos_menumaster', $data);
        $menuID = $this->db->insert_id();
        if ($result) {
            return array('error' => 0, 'message' => 'successfully saved', 'result' => $result, 'menuID' => $menuID);
        } else {
            return array('error' => 1, 'message' => 'Error while insert, Please contact your system support team');
        }
    }

    /** Menu : update */
    function updateMenu($data, $id)
    {
        $this->db->where('menuMasterID', $id);
        $result = $this->db->update('srp_erp_pos_menumaster', $data);
        return $result;
    }

    /** Menu : bulk update by Menu Category ID*/
    function updateMenu_byCategoryID($data, $menuCategoryID)
    {
        $this->db->where('menuCategoryID', $menuCategoryID);
        $result = $this->db->update('srp_erp_pos_menumaster', $data);
        return $result;
    }


    function menuBulkUpdate($menuCategoryID, $warehouseID, $warehouseMenuCategoryID)
    {
        $sql = "INSERT INTO srp_erp_pos_warehousemenumaster  (warehouseID, menuMasterID, warehouseMenuCategoryID,companyID, createdPCID, createdUserID , createdDateTime, createdUserName ) (SELECT '" . $warehouseID . "', menuMasterID,  '" . $warehouseMenuCategoryID . "' , '" . current_companyID() . "' , '" . current_pc() . "', '" . current_userID() . "', '" . format_date_mysql_datetime() . "' , '" . current_user() . "' FROM srp_erp_pos_menumaster WHERE menuCategoryID='" . $menuCategoryID . " AND isDeleted=0 ')";
        $result = $this->db->query($sql);

        /*echo $sql;
        exit;*/
        return $result;
    }


    /** Menu Setup */
    function get_srp_erp_pos_warehousemenumaster($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_warehousemenumaster');
        $this->db->where('warehouseMenuID', $id);
        $result = $this->db->get()->row_array();
        //echo $this->db->last_query();
        return $result;

    }

    /** Menu Category : update */
    function update_srp_erp_pos_warehousemenumaster($data, $id)
    {
        $this->db->where('warehouseMenuID', $id);
        $result = $this->db->update('srp_erp_pos_warehousemenumaster', $data);
        return $result;
    }

    function update_Menue_Category_Isactive()
    {

        $data['isActive'] = ($this->input->post('chkedvalue'));

        $this->db->where('autoID', $this->input->post('autoID'));
        $result = $this->db->update('srp_erp_pos_warehousemenucategory', $data);
        if ($result) {
            return array('error' => 0, 'message' => 'successfully updated', 'result' => $result);
        }
    }

    function get_srp_erp_pos_warehousemenucategory($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_warehousemenucategory');
        $this->db->where('autoID', $id);
        $result = $this->db->get()->row_array();
        //echo $this->db->last_query();
        return $result;

    }

    function update_srp_erp_pos_warehousemenucategory($data, $id)
    {
        $this->db->where('autoID', $id);
        $result = $this->db->update('srp_erp_pos_warehousemenucategory', $data);
        return $result;
    }


    function update_srp_erp_pos_warehousemenumenu($data, $id)
    {
        $this->db->where('warehouseMenuCategoryID', $id);
        $result = $this->db->update('srp_erp_pos_warehousemenumaster', $data);
        return $result;
    }


    function update_Menue_Master_Isactive()
    {

        $data['isActive'] = ($this->input->post('chkedvalue'));

        $this->db->where('warehouseMenuID', $this->input->post('warehouseMenuID'));
        $result = $this->db->update('srp_erp_pos_warehousemenumaster', $data);
        if ($result) {
            return array('error' => 0, 'message' => 'successfully updated', 'result' => $result);
        }
    }

    function load_default_uom()
    {
        $this->db->select('defaultUnitOfMeasure,companyLocalWacAmount');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        $result = $this->db->get()->row_array();
        //echo $this->db->last_query();
        return $result;
    }

    function save_menu_details()
    {
        $menuDetailID = $this->input->post('menuDetailID_hn');
        $qty = $this->input->post('qty');
        $unitCost = $this->input->post('cost');
        $totalCost = $qty * $unitCost;
        $itemAutoID = $this->input->post('itemAutoID');
        $tmp_cost = $this->input->post('tmp_cost');
        $totalActualCost = $qty * $tmp_cost;
        $menuMasterID = $this->input->post('pageid');


        if (empty($menuDetailID)) {
            $this->db->set('menuDetailDescription', $this->input->post('menuDetailDescription'));
            $this->db->set('itemAutoID', $itemAutoID);
            $this->db->set('qty', $qty);
            $this->db->set('UOM', $this->input->post('UOM'));
            $this->db->set('uomID', $this->input->post('uomID'));
            $this->db->set('cost', $totalCost);
            $this->db->set('actualInventoryCost', $totalActualCost);
            $this->db->set('menuMasterID', $menuMasterID);

            $this->db->set('companyID', current_companyID());
            $this->db->set('createdPCID', current_pc());
            $this->db->set('createdUserID', current_companyID());
            $this->db->set('createdDateTime', format_date_mysql_datetime());
            $this->db->set('createdUserName', current_user());

            $result = $this->db->insert('srp_erp_pos_menudetails');
            $insert_id = $this->db->insert_id();
            if ($result) {
                $this->db->select('menuCost');
                $this->db->where('menuMasterID', $this->input->post('pageid'));
                $cost = $this->db->get('srp_erp_pos_menumaster')->row_array();
                $totcost = $cost['menuCost'] + $this->input->post('cost');

                $datas['menuCost'] = $totcost;
                $this->db->where('menuMasterID', $this->input->post('pageid'));
                $results = $this->db->update('srp_erp_pos_menumaster', $datas);

                if ($results) {
                    $this->updateTotalCost($menuMasterID);
                    return array('error' => 0, 'message' => 'Menu Detail Added Successfully', 'result' => $result);
                }
            }
        } else {
            $data['menuDetailDescription'] = $this->input->post('menuDetailDescription');
            $data['itemAutoID'] = $itemAutoID;
            $data['qty'] = $qty;
            $data['UOM'] = $this->input->post('UOM');
            $data['uomID'] = $this->input->post('uomID');
            $data['cost'] = $totalCost;
            $data['actualInventoryCost'] = $totalActualCost;
            $data['menuMasterID'] = $this->input->post('pageid');

            $data['modifiedPCID'] = current_pc();
            $data['modifiedUserID'] = current_companyID();
            $data['modifiedDateTime'] = format_date_mysql_datetime();
            $data['timeStamp'] = format_date_mysql_datetime();
            $data['modifiedUserName'] = current_user();


            $this->db->where('menuDetailID', $this->input->post('menuDetailID_hn'));
            $result = $this->db->update('srp_erp_pos_menudetails', $data);
            $_last_insert_id = $this->input->post('menuDetailID_hn');
            if ($result) {
                $this->db->select('menuCost');
                $this->db->where('menuMasterID', $this->input->post('pageid'));
                $cost = $this->db->get('srp_erp_pos_menumaster')->row_array();
                $existcost = $this->input->post('edit_cost_hn');

                $totcost = ($cost['menuCost'] - $existcost) + $this->input->post('cost');

                $dat['menuCost'] = $totcost;
                $this->db->where('menuMasterID', $this->input->post('pageid'));
                $output = $this->db->update('srp_erp_pos_menumaster', $dat);
                if ($output) {
                    $this->updateTotalCost($menuMasterID);
                    return array('error' => 0, 'message' => 'Records Updated Successfully', 'result' => $result);
                }
            }
        }
    }

    function updateTotalCost($menuID)
    {
        $this->db->trans_start();

        try {
            /** get menu details*/
            $this->db->select('cost');
            $this->db->where('menuMasterID', $menuID);
            $r = $this->db->get('srp_erp_pos_menudetails')->result_array();
            $totalCost = 0;
            if (!empty($r)) {
                foreach ($r as $cost) {
                    $totalCost += $cost['cost'];
                }

                /** update menu cost */
                $tmpData['menuCost'] = $totalCost;
                $result = $this->updateMenu($tmpData, $menuID);

                /** handing db errors */
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();
                }

                $q = $this->db->last_query();
                if ($result) {
                    return array('error' => 0, 'message' => 'done', 'totalCost' => $totalCost, 'result' => $result,);
                } else {
                    return array('error' => 1, 'message' => 'error', 'totalCost' => $totalCost, 'result' => $result,);
                }

            } else {
                return array('error' => 1, 'message' => 'empty items', 'totalCost' => 0, 'result' => null, 'query' => null);
            }
        } catch (customException $e) {
            $this->db->trans_rollback();
            return array('error' => 1, 'message' => $e->getMessage(), 'totalCost' => 0, 'result' => null, 'query' => null);
        }
    }

    function load_menu_detail_edit()
    {
        $this->db->select('*');
        $this->db->where('menuDetailID', $this->input->post('menuDetailID'));
        return $this->db->get('srp_erp_pos_menudetails')->row_array();
    }


    function save_rooms_info()
    {
        $diningRoomMasterID = $this->input->post('rooms_edit_hn');
        if (empty($diningRoomMasterID)) {
            $this->db->set('diningRoomDescription', $this->input->post('diningRoomDescription'));
            $this->db->set('wareHouseAutoID', $this->input->post('wareHouseAutoIDhn'));

            $this->db->set('companyID', current_companyID());
            $this->db->set('createdPCID', current_pc());
            $this->db->set('createdUserID', current_companyID());
            $this->db->set('createdDateTime', format_date_mysql_datetime());
            $this->db->set('createdUserName', current_user());

            $result = $this->db->insert('srp_erp_pos_diningroommaster');
            $insert_id = $this->input->post('wareHouseAutoIDhn');
            if ($result) {
                return array('error' => 0, 'message' => 'Room Added Successfully', 'result' => $result, 'id' => $insert_id);
            }
        } else {
            $data['diningRoomDescription'] = $this->input->post('diningRoomDescription');

            $data['modifiedPCID'] = current_pc();
            $data['modifiedUserID'] = current_companyID();
            $data['modifiedDateTime'] = format_date_mysql_datetime();
            $data['timeStamp'] = format_date_mysql_datetime();
            $data['modifiedUserName'] = current_user();

            $this->db->where('diningRoomMasterID', $this->input->post('rooms_edit_hn'));
            $result = $this->db->update('srp_erp_pos_diningroommaster', $data);
            $insert_id = $this->input->post('wareHouseAutoIDhn');
            if ($result) {
                return array('error' => 0, 'message' => 'Records Updated Successfully', 'result' => $result, 'id' => $insert_id);
            }
        }
    }

    function edit_pos_room_config()
    {
        $this->db->select('*');
        $this->db->where('diningRoomMasterID', $this->input->post('diningRoomMasterID'));
        return $this->db->get('srp_erp_pos_diningroommaster')->row_array();
    }

    function delete_pos_room_config()
    {
        $this->db->where('diningRoomMasterID', $this->input->post('diningRoomMasterID'));
        $result = $this->db->delete('srp_erp_pos_diningroommaster');
        if ($result) {
            $this->db->where('diningRoomMasterID', $this->input->post('diningRoomMasterID'));
            $results = $this->db->delete('srp_erp_pos_diningtables');
            if ($results) {
                return array('error' => 0, 'message' => 'Records Deleted Successfully', 'result' => $result);
            }
        }
    }


    function save_tables_info()
    {
        $diningTableAutoID = $this->input->post('tables_edit_hn');
        if (empty($diningTableAutoID)) {
            $this->db->set('diningTableDescription', $this->input->post('diningTableDescription'));
            $this->db->set('noOfSeats', $this->input->post('noOfSeats'));
            $this->db->set('diningRoomMasterID', $this->input->post('diningRoomMasterIDhn'));
            $this->db->set('segmentID', $this->input->post('warehouseIDTablehn'));

            $this->db->set('companyID', current_companyID());
            $this->db->set('createdPCID', current_pc());
            $this->db->set('createdUserID', current_companyID());
            $this->db->set('createdDateTime', format_date_mysql_datetime());
            $this->db->set('createdUserName', current_user());

            $result = $this->db->insert('srp_erp_pos_diningtables');
            $insert_id = $this->input->post('diningRoomMasterIDhn');
            if ($result) {
                return array('error' => 0, 'message' => 'Tables Added Successfully', 'result' => $result, 'id' => $insert_id);
            }
        } else {
            $data['diningTableDescription'] = $this->input->post('diningTableDescription');
            $data['noOfSeats'] = $this->input->post('noOfSeats');

            $data['modifiedPCID'] = current_pc();
            $data['modifiedUserID'] = current_companyID();
            $data['modifiedDateTime'] = format_date_mysql_datetime();
            $data['timeStamp'] = format_date_mysql_datetime();
            $data['modifiedUserName'] = current_user();

            $this->db->where('diningTableAutoID', $this->input->post('tables_edit_hn'));
            $result = $this->db->update('srp_erp_pos_diningtables', $data);
            $insert_id = $this->input->post('diningRoomMasterIDhn');
            if ($result) {
                return array('error' => 0, 'message' => 'Records Updated Successfully', 'result' => $result, 'id' => $insert_id);
            }
        }
    }

    function edit_pos_table_config()
    {
        $this->db->select('*');
        $this->db->where('diningTableAutoID', $this->input->post('diningTableAutoID'));
        return $this->db->get('srp_erp_pos_diningtables')->row_array();
    }

    function delete_pos_table_config()
    {
        $this->db->where('diningTableAutoID', $this->input->post('diningTableAutoID'));
        $result = $this->db->delete('srp_erp_pos_diningtables');
        if ($result) {
            return array('error' => 0, 'message' => 'Records Deleted Successfully', 'result' => $result);
        }
    }

    function loadMenuDetail_table()
    {
        $menuMasterID = $this->input->post('menuMasterID');
        $this->db->select('menuDetailID,menuDetailDescription,menuMasterID,srp_erp_pos_menudetails.itemAutoID,qty,UOM,cost,actualInventoryCost,srp_erp_pos_menudetails.companyID,srp_erp_company.company_default_currency, srp_erp_itemmaster.itemDescription,srp_erp_itemmaster.itemSystemCode');
        $this->db->from('srp_erp_pos_menudetails');
        $this->db->join('srp_erp_company', 'srp_erp_pos_menudetails.companyID = srp_erp_company.company_id', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_pos_menudetails.itemAutoID', 'left');
        $this->db->where('menuMasterID', $menuMasterID);
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function delete_pos_menu_detail()
    {
        $this->db->where('menuDetailID', $this->input->post('menuDetailID'));
        $result = $this->db->delete('srp_erp_pos_menudetails');
        if ($result) {
            $this->db->select('menuCost');
            $this->db->where('menuMasterID', $this->input->post('menuMasterID'));
            $cost = $this->db->get('srp_erp_pos_menumaster')->row_array();
            $existcost = $this->input->post('cost');

            $totcost = $cost['menuCost'] - $existcost;

            $datDel['menuCost'] = $totcost;
            $this->db->where('menuMasterID', $this->input->post('menuMasterID'));
            $outputDel = $this->db->update('srp_erp_pos_menumaster', $datDel);
            if ($outputDel) {
                return array('error' => 0, 'message' => 'Records Deleted Successfully', 'result' => $result);
            }

        }
    }

    function save_crew_role_info()
    {
        $crewRoleID = $this->input->post('crew_role_id_hn');
        $isWaiter = $this->input->post('isWaiter');
        if (empty($crewRoleID)) {
            $this->db->set('roleDescription', $this->input->post('roleDescription'));

            $this->db->set('isWaiter', $isWaiter);
            $this->db->set('companyID', current_companyID());
            $this->db->set('companyCode', current_companyCode());
            $this->db->set('createdPCID', current_pc());
            $this->db->set('createdUserID', current_companyID());
            $this->db->set('createdDateTime', format_date_mysql_datetime());
            $this->db->set('createdUserName', current_user());

            $result = $this->db->insert('srp_erp_pos_crewroles');
            if ($result) {
                return array('error' => 0, 'message' => 'Role Added Successfully', 'result' => $result);
            }
        } else {
            $data['roleDescription'] = $this->input->post('roleDescription');

            $data['modifiedPCID'] = current_pc();
            $data['modifiedUserID'] = current_companyID();
            $data['modifiedDateTime'] = format_date_mysql_datetime();
            $data['timeStamp'] = format_date_mysql_datetime();
            $data['modifiedUserName'] = current_user();
            $data['isWaiter'] = $isWaiter;

            $this->db->where('crewRoleID', $this->input->post('crew_role_id_hn'));
            $result = $this->db->update('srp_erp_pos_crewroles', $data);

            if ($result) {
                return array('error' => 0, 'message' => 'Records Updated Successfully', 'result' => $result);
            }
        }
    }

    function edit_pos_crew_roles_config()
    {
        $this->db->select('*');
        $this->db->where('crewRoleID', $this->input->post('crewRoleID'));
        return $this->db->get('srp_erp_pos_crewroles')->row_array();
    }

    function delete_pos_crew_roles_config()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_crewmembers');
        $this->db->where('crewRoleID', $this->input->post('crewRoleID'));
        $crewMemberID = $this->db->get()->row('crewMemberID');


        if (!$crewMemberID) {
            $this->db->where('crewRoleID', $this->input->post('crewRoleID'));
            $result = $this->db->delete('srp_erp_pos_crewroles');
            if ($result) {
                return array('error' => 0, 'message' => 'Records Deleted Successfully', 'result' => $result);
            }
        } else {
            return array('error' => 1, 'message' => 'This records can not be Deleted, This crew role already assigned!');
        }

    }

    function save_crew_info()
    {
        $crewMemberID = $this->input->post('crew_edit_hn');
        if (empty($crewMemberID)) {
            $segmentConfigID = $this->input->post('wareHouseAutoIDCrewhn');
            $this->db->select("*");
            $this->db->from("srp_erp_pos_segmentconfig");
            $this->db->where("segmentConfigID", $segmentConfigID);
            $wareHouseAutoID = $this->db->get()->row('wareHouseAutoID');

            $this->db->set('crewFirstName', $this->input->post('crewFirstName'));
            $this->db->set('crewLastName', $this->input->post('crewLastName'));
            $this->db->set('EIdNo', $this->input->post('EIdNo'));
            $this->db->set('crewRoleID', $this->input->post('crewRoleID'));
            $this->db->set('wareHouseAutoID', $wareHouseAutoID);
            $this->db->set('segmentConfigID', $segmentConfigID);

            $this->db->set('companyID', current_companyID());
            $this->db->set('companyCode', current_companyCode());
            $this->db->set('createdPCID', current_pc());
            $this->db->set('createdUserID', current_companyID());
            $this->db->set('createdDateTime', format_date_mysql_datetime());
            $this->db->set('createdUserName', current_user());

            $result = $this->db->insert('srp_erp_pos_crewmembers');
            $insert_id = $this->input->post('wareHouseAutoIDCrewhn');
            if ($result) {
                return array('error' => 0, 'message' => 'Crew Added Successfully', 'result' => $result, 'id' => $insert_id);
            }
        } else {
            $data['crewFirstName'] = $this->input->post('crewFirstName');
            $data['crewLastName'] = $this->input->post('crewLastName');
            $data['EIdNo'] = $this->input->post('EIdNo');
            $data['crewRoleID'] = $this->input->post('crewRoleID');

            $data['modifiedPCID'] = current_pc();
            $data['modifiedUserID'] = current_companyID();
            $data['modifiedDateTime'] = format_date_mysql_datetime();
            $data['timeStamp'] = format_date_mysql_datetime();
            $data['modifiedUserName'] = current_user();

            $this->db->where('crewMemberID', $this->input->post('crew_edit_hn'));
            $result = $this->db->update('srp_erp_pos_crewmembers', $data);
            $insert_id = $this->input->post('wareHouseAutoIDCrewhn');
            if ($result) {
                return array('error' => 0, 'message' => 'Records Updated Successfully', 'result' => $result, 'id' => $insert_id);
            }
        }
    }

    function edit_pos_crew_config()
    {
        $this->db->select('*');
        $this->db->where('crewMemberID', $this->input->post('crewMemberID'));
        return $this->db->get('srp_erp_pos_crewmembers')->row_array();
    }

    function delete_pos_crew_config()
    {
        $this->db->where('crewMemberID', $this->input->post('crewMemberID'));
        $result = $this->db->delete('srp_erp_pos_crewmembers');
        if ($result) {
            return array('error' => 0, 'message' => 'Records Deleted Successfully', 'result' => $result);
        }
    }

    function pack_categoryExist($category, $packID)
    {
        $this->db->select('*');
        $this->db->where('valuePackID', $packID);
        $this->db->where('menuCategoryID', $category);
        $result = $this->db->get('srp_erp_pos_menupackcategory')->row_array();
        return $result;
    }

    function insert_srp_erp_pos_menupackcategory($data)
    {
        $this->db->insert('srp_erp_pos_menupackcategory', $data);
        $return = $this->db->affected_rows();
        return $return;
    }

    function update_srp_erp_pos_menupackcategory($data, $id)
    {
        /*$this->db->where('menuPackCategoryID', $id);
        $result = $this->db->update('srp_erp_pos_menupackcategory', $data);*/
        $this->db->where('groupMasterID', $id);
        $result = $this->db->update('srp_erp_pos_menupackgroupmaster', $data);
        return $result;
    }

    function pack_itemExist($packMenuID, $menuID)
    {
        $this->db->select('*');
        $this->db->where('PackMenuID', $packMenuID);
        $this->db->where('menuID', $menuID);
        $result = $this->db->get('srp_erp_pos_menupackitem')->row_array();
        return $result;
    }

    function insert_srp_erp_pos_menupackitem($data)
    {
        $this->db->insert('srp_erp_pos_menupackitem', $data);
        //$this->db->affected_rows();
        $return = $this->db->insert_id();
        return $return;
    }

    /****NASIK****/
    function posGL_config()
    {
        $glAutoID = $this->input->post('glAutoID');
        $paymentTypeID = $this->input->post('paymentTypeID');
        $load_posGLData = $this->load_posGL($paymentTypeID);


        if (!isset($load_posGLData)) {
            $data = array(
                'GLCode' => $glAutoID,
                'paymentConfigMasterID' => $paymentTypeID,
                'companyID' => current_companyID(),
                'companyCode' => current_companyCode(),
                'createdPCID' => current_pc(),
                'createdUserID' => current_userID(),
                'createdUserName' => current_employee(),
                'createdUserGroup' => current_user_group(),
                'createdDateTime' => current_date()
            );

            $this->db->insert('srp_erp_pos_paymentglconfigdetail', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'GL code register successfully');
            } else {
                return array('e', 'Error in GL code registration');
            }
        } else {
            $data = array(
                'GLCode' => $glAutoID,
                'modifiedPCID' => current_pc(),
                'modifiedUserID' => current_userID(),
                'modifiedUserName' => current_employee(),
                'modifiedDateTime' => current_date()
            );

            $where = array(
                'companyID' => current_companyID(),
                'paymentConfigMasterID' => $paymentTypeID
            );

            $this->db->where($where)->update('srp_erp_pos_paymentglconfigdetail', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'GL code register successfully');
            } else {
                return array('e', 'Error in GL code registration');
            }
        }
    }

    function load_posGL($masterID)
    {
        $companyID = current_companyID();
        return $this->db->query("SELECT GLCode FROM srp_erp_pos_paymentglconfigdetail WHERE companyID={$companyID} AND paymentConfigMasterID='$masterID'")->row('GLCode');
    }

    function fetch_menuitemfor_menucategory()
    {
        $id = $this->input->post('autoID');
        $menucatid = $this->db->query("select menuCategoryID from srp_erp_pos_warehousemenucategory where autoID=$id")->row_array();
        if ($menucatid) {
            $catid = $menucatid['menuCategoryID'];
            $menumaster = $this->db->query("select menuMasterID,menuMasterDescription,menuCategoryID
                                           from srp_erp_pos_menumaster where menuCategoryID = $catid
                                           and not exists(
                                           SELECT menuMasterID FROM  srp_erp_pos_warehousemenumaster WHERE menuMasterID= srp_erp_pos_menumaster.menuMasterID AND srp_erp_pos_menumaster.isDeleted=1
                                           )")->result_array();
            return $menumaster;
        }
    }

    function delete_pos_packItem($id)
    {
        $this->db->where('menuPackItemID', $id);
        $result = $this->db->delete('srp_erp_pos_menupackitem');
        return $result;
    }

    function delete_pos_packItemCategory($id)
    {
        $this->db->where('menuPackCategoryID', $id);
        $result = $this->db->delete('srp_erp_pos_menupackcategory');
        //echo $this->db->last_query();
        return $result;
    }

    function save_menu_item($data)
    {


        $this->db->set('warehouseMenuCategoryID', $data['warehouseMenuCategoryID']);
        $this->db->set('menuMasterID', $data['menuMasterID']);
        $this->db->set('warehouseID', $data['warehouseID']);
        $this->db->set('companyID', current_companyID());
        $this->db->set('createdPCID', current_pc());
        $this->db->set('createdUserID', current_companyID());
        $this->db->set('createdDateTime', format_date_mysql_datetime());
        $this->db->set('createdUserName', current_user());
        $result = $this->db->insert('srp_erp_pos_warehousemenumaster');
        //$insert_id = $this->input->post('wareHouseAutoIDCrewhn');
        if ($result) {
            return array('error' => 0, 'message' => 'Menu Added Successfully', 'result' => $result, 'id' => $this->input->post('wcAutoId'));
        }

    }

    function save_outlet()
    {
        if (empty($this->input->post('warehouseredit'))) {

            //$this->db->set('companyCode', (($this->input->post('companyid') != "")) ? $this->input->post('companyid') : NULL);
            $this->db->set('wareHouseCode', (($this->input->post('warehousecode') != "")) ? $this->input->post('warehousecode') : NULL);
            $this->db->set('wareHouseDescription', (($this->input->post('warehousedescription') != "")) ? $this->input->post('warehousedescription') : NULL);
            $this->db->set('wareHouseLocation', (($this->input->post('warehouselocation') != "")) ? $this->input->post('warehouselocation') : NULL);
            $this->db->set('warehouseAddress', (($this->input->post('warehouseAddress') != "")) ? $this->input->post('warehouseAddress') : NULL);
            $this->db->set('pos_footNote', (($this->input->post('pos_footNote') != "")) ? $this->input->post('pos_footNote') : NULL);
            $this->db->set('warehouseTel', (($this->input->post('warehouseTel') != "")) ? $this->input->post('warehouseTel') : NULL);
            //   $this->db->set('isPosLocation', (($this->input->post('isPosLocation') != "")) ? $this->input->post('isPosLocation') : NULL);
            $this->db->set('createdUserGroup', ($this->common_data['user_group']));
            $this->db->set('createdPCID', ($this->common_data['current_pc']));
            $this->db->set('createdUserID', ($this->common_data['current_userID']));
            $this->db->set('createdDateTime', ($this->common_data['current_date']));
            $this->db->set('createdUserName', ($this->common_data['current_user']));
            $this->db->set('companyID', ($this->common_data['company_data']['company_id']));
            $this->db->set('companyCode', ($this->common_data['company_data']['company_code']));
            $this->db->set('isPosLocation', 1);
            $this->db->set('isActive', 0);
            $result = $this->db->insert('srp_erp_warehousemaster');


            if ($result) {
                //$this->session->set_flashdata('s', 'Warehouse Added Successfully');
                $error = array('error' => 0, 'message' => 'Warehouse Added Successfully');
                return $error;
            }
        } else {
            $id = $this->input->post('warehouseredit');
            $code = $this->input->post('warehousecode');
            $Q = 'SELECT * FROM srp_erp_warehousemaster WHERE wareHouseCode= "' . $code . '" AND wareHouseAutoID !=' . $id;
            $output = $this->db->query($Q)->row_array();
            if (!empty($output)) {
                $error = array('error' => 1, 'message' => 'Warehouse code <strong>' . $code . '</strong> already used!, Please try different warehouse code');
                return $error;
            }


            $data['wareHouseCode'] = ((($this->input->post('warehousecode') != "")) ? $this->input->post('warehousecode') : NULL);
            $data['wareHouseDescription'] = ((($this->input->post('warehousedescription') != "")) ? $this->input->post('warehousedescription') : NULL);
            $data['wareHouseLocation'] = ((($this->input->post('warehouselocation') != "")) ? $this->input->post('warehouselocation') : NULL);
            $data['warehouseAddress'] = ((($this->input->post('warehouseAddress') != "")) ? $this->input->post('warehouseAddress') : NULL);
            $data['pos_footNote'] = ((($this->input->post('pos_footNote') != "")) ? $this->input->post('pos_footNote') : NULL);

            $data['warehouseTel'] = ((($this->input->post('warehouseTel') != "")) ? $this->input->post('warehouseTel') : NULL);
            /*$data['isPosLocation'] = 1;
            $data['isActive'] = 0;*/
            $data['modifiedPCID'] = ($this->common_data['current_pc']);
            $data['modifiedUserID'] = ($this->common_data['current_userID']);
            $data['modifiedDateTime'] = ($this->common_data['current_date']);
            $data['modifiedUserName'] = ($this->common_data['current_user']);


            $this->db->where('wareHouseAutoID', $this->input->post('warehouseredit'));
            $result = $this->db->update('srp_erp_warehousemaster', $data);

            $outletTemplateMasterID = $this->input->post('outletTemplateMasterID');
            $outletID = $this->input->post('warehouseredit');;
            $companyID = current_companyID();
            $this->db->select('outletTemplateDetailID')->from('srp_erp_pos_outlettemplatedetail');
            $this->db->where('companyID', $companyID);
            $this->db->where('outletID', $outletID);
            $outletTemplateDetailID = $this->db->get()->row('outletTemplateDetailID');


            if ($outletTemplateDetailID) {
                /**update*/
                $outlet_data['outletTemplateMasterID'] = $outletTemplateMasterID;
                $this->db->where('outletTemplateDetailID', $outletTemplateDetailID);
                $this->db->update('srp_erp_pos_outlettemplatedetail', $outlet_data);

            } else {
                /** insert */
                $curDate = format_date_mysql_datetime();
                $outlet_data['outletTemplateMasterID'] = $outletTemplateMasterID;
                $outlet_data['companyID'] = $companyID;
                $outlet_data['outletID'] = $this->input->post('warehouseredit');
                $outlet_data['createdPCID'] = current_pc();
                $outlet_data['createdUserID'] = current_userID();
                $outlet_data['createdDateTime'] = $curDate;
                $outlet_data['createdUserName'] = current_user();
                $outlet_data['createdUserGroup'] = user_group();
                $outlet_data['timeStamp'] = $curDate;
                $this->db->insert('srp_erp_pos_outlettemplatedetail', $outlet_data);
            }
            //echo $this->db->last_query();

            if ($result) {
                $this->session->set_flashdata('s', 'Records Updated Successfully');
                return array('error' => 0, 'message' => 'Records Updated Successfully');
                //return true;
            }
        }
    }

    function validate_warehouseItem($data)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_warehousemenumaster');
        $this->db->where('warehouseMenuCategoryID', $data['warehouseMenuCategoryID']);
        $this->db->where('menuMasterID', $data['menuMasterID']);
        $this->db->where('warehouseID', $data['warehouseID']);
        $this->db->where('isDeleted', 0);
        $result = $this->db->get()->row_array();
        //echo $this->db->last_query();
        if (empty($result)) {
            return true;
        } else {
            return false;
        }
    }

    function save_srp_erp_pos_menusize($data)
    {
        $result = $this->db->insert('srp_erp_pos_menusize', $data);
        return $result;
    }

    function update_srp_erp_pos_menusize($id, $data)
    {
        $this->db->where('menuSizeID', $id);
        $result = $this->db->update('srp_erp_pos_menusize', $data);
        return $result;
    }


    function get_srp_erp_pos_menusize_specific($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_menusize');
        $this->db->where('menuSizeID', $id);
        $result = $this->db->get()->row_array();
        return $result;
    }

    function validate_srp_erp_pos_menusize($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_menumaster');
        $this->db->where('menuSizeID', $id);
        $this->db->where('isDeleted', 0);
        $result = $this->db->get()->row_array();
        if (!empty($result)) {
            return false;
        } else {
            return true;
        }
    }

    function delete_srp_erp_pos_menusize($id)
    {
        $this->db->where('menuSizeID', $id);
        $result = $this->db->delete('srp_erp_pos_menusize');
        return $result;
    }

    function edit_menu_size()
    {
        $this->db->select('*');
        $this->db->where('menuSizeID', $this->input->post('menuSizeID'));
        return $this->db->get('srp_erp_pos_menusize')->row_array();
    }

    function get_optionalMenuPackItem($id)
    {
        $this->db->select('packItem.menuPackItemID as id, menuMaster.menuMasterID as menuID, menuMaster.menuMasterDescription, menuCategory.menuCategoryDescription');
        $this->db->from('srp_erp_pos_menupackitem packItem');
        $this->db->join('srp_erp_pos_menumaster menuMaster', 'menuMaster.menuMasterID = packItem.menuID', 'left');
        $this->db->join('srp_erp_pos_menucategory menuCategory', 'menuCategory.menuCategoryID = packItem.menuCategoryID', 'left');
        $this->db->where('packItem.PackMenuID', $id);
        $this->db->where('isRequired', 0);
        $r = $this->db->get()->result_array();
        return $r;
    }

    function insert_srp_erp_pos_menupackgroupmaster($data)
    {
        $result = $this->db->insert('srp_erp_pos_menupackgroupmaster', $data);
        if ($result) {
            $groupMasterID = $this->db->insert_id();
            return $groupMasterID;
        } else {
            return false;
        }
    }

    function insert_batch_srp_erp_pos_packgroupdetail($data)
    {
        $result = $this->db->insert_batch('srp_erp_pos_packgroupdetail', $data);
        return $result;

    }

    function validate_packGroup($description, $packMenuID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_menupackgroupmaster');
        $this->db->where('description', $description);
        $this->db->where('packMenuID', $packMenuID);
        $result = $this->db->get()->row_array();
        return $result;

    }

    function delete_posPackageGroup($id)
    {
        $this->db->where('groupMasterID', $id);
        $result = $this->db->delete('srp_erp_pos_menupackgroupmaster');
        return $result;
    }

    function delete_posPackageGroupDetail($id)
    {
        $this->db->where('groupMasterID', $id);
        $result = $this->db->delete('srp_erp_pos_packgroupdetail');
        return $result;
    }

    function delete_pos_packGroup_and_detail($id)
    {
        $this->db->where('groupMasterID', $id);
        $result = $this->db->delete('srp_erp_pos_menupackgroupmaster');
        $this->delete_srp_erp_pos_packgroupdetail($id);

        return $result;
    }


    function delete_srp_erp_pos_packgroupdetail($id, $menuID = null)
    {
        $this->db->where('packMenuID', $id);
        if ($menuID != null) {
            $this->db->where('menuID', $menuID);
        }
        $result = $this->db->delete('srp_erp_pos_packgroupdetail');
        return $result;
    }

    function delete_srp_erp_pos_packgroupdetail_menuPackItemID($menuPackItemID)
    {
        $this->db->where('menuPackItemID', $menuPackItemID);
        $result = $this->db->delete('srp_erp_pos_packgroupdetail');
        return $result;
    }

    function get_srp_erp_pos_packgroupdetail_by_groupMasterID($groupMasterID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_packgroupdetail');
        $this->db->where('groupMasterID', $groupMasterID);
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_pack_itemList_group($id)
    {

        $q = "SELECT gdetail.packgroupdetailID, mpi.menuPackItemID, mpi.isRequired,gmaster.description as GroupName, gdetail.isActive, menuMaster.* FROM srp_erp_pos_menupackitem mpi LEFT JOIN srp_erp_pos_packgroupdetail gdetail ON gdetail.menuPackItemID = mpi.menuPackItemID LEFT JOIN srp_erp_pos_menupackgroupmaster gmaster ON gmaster.groupMasterID = gdetail.groupMasterID LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = mpi.menuID WHERE mpi.PackMenuID  = '" . $id . "'";
        //echo $q;
        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_srp_erp_pos_menupackgroupmaster($packMenuID, $description)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_menupackgroupmaster');
        $this->db->where('description', $description);
        $this->db->where('packMenuID', $packMenuID);
        $result = $this->db->get()->row_array();
        return $result;
    }

    function insert_srp_erp_pos_packgroupdetail($data)
    {
        $result = $this->db->insert('srp_erp_pos_packgroupdetail', $data);
        return $result;

    }

    function update_customer_type_isactive()
    {

        $data['isActive'] = ($this->input->post('chkedvalue'));

        $this->db->where('customerID', $this->input->post('customerID'));
        $result = $this->db->update('srp_erp_pos_customers', $data);
        if ($result) {
            return array('error' => 0, 'message' => 'successfully updated', 'result' => $result);
        }
    }

    function update_srp_erp_pos_customers($id, $data)
    {
        $this->db->where('customerID', $id);
        $result = $this->db->update('srp_erp_pos_customers', $data);
        return $result;
    }

    function save_srp_erp_pos_customers($data)
    {
        $result = $this->db->insert('srp_erp_pos_customers', $data);
        return $result;
    }

    function get_edit_customer()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_customers');
        $this->db->where('customerID', $this->input->post('customerID'));
        $result = $this->db->get()->row_array();
        return $result;
    }

    function update_srp_erp_pos_menuyields($id, $data)
    {
        $this->db->where('yieldID', $id);
        $result = $this->db->update('srp_erp_pos_menuyields', $data);
        return $result;
    }

    function save_srp_erp_pos_menuyields($data)
    {
        $result = $this->db->insert('srp_erp_pos_menuyields', $data);
        return $result;
    }

    function get_edit_yieldMaster()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_menuyields');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_pos_menuyields.itemAutoID = srp_erp_itemmaster.itemAutoID', 'left');
        $this->db->where('yieldID', $this->input->post('yieldID'));
        $result = $this->db->get()->row_array();
        return $result;
    }

    function loadItemDropDown()
    {
        $id = $this->input->post('typeAutoId');
        if ($id == 1) {
            $this->db->select('itemAutoID,itemName');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('companyID', current_companyID());
            $result = $this->db->get()->result_array();
        } else {
            $this->db->select('yieldID as itemAutoID,Description as itemName');
            $this->db->from('srp_erp_pos_menuyields');
            $this->db->where('companyID', current_companyID());
            $this->db->where('yieldID !=', $this->input->post('yieldID'));
            $result = $this->db->get()->result_array();
        }

        //echo $this->db->last_query();
        return $result;
    }


    function update_srp_erp_pos_menuyieldsdetails($id, $data)
    {
        $this->db->where('yieldDetailID', $id);
        $result = $this->db->update('srp_erp_pos_menuyieldsdetails', $data);
        return $result;
    }

    function save_srp_erp_pos_menuyieldsdetails($data)
    {
        $result = $this->db->insert('srp_erp_pos_menuyieldsdetails', $data);
        return $result;
    }

    function get_edit_yieldDetail()
    {
        $this->db->select('srp_erp_pos_menuyieldsdetails.*,srp_erp_itemmaster.itemDescription as itemDescription,srp_erp_itemmaster.itemSystemCode as itemSystemCode,(srp_erp_itemmaster.companyLocalWacAmount / getUoMConvertion (srp_erp_pos_menuyieldsdetails.uom,srp_erp_itemmaster.defaultUnitOfMeasureID,srp_erp_pos_menuyieldsdetails.companyID))* qty AS COST1,srp_erp_itemmaster.companyLocalWacAmount as costwac');
        $this->db->from('srp_erp_pos_menuyieldsdetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_pos_menuyieldsdetails.itemAutoID = srp_erp_itemmaster.itemAutoID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_pos_menuyieldsdetails.uom = srp_erp_unit_of_measure.UnitID', 'left');
        $this->db->where('yieldDetailID', $this->input->post('yieldDetailID'));
        $result = $this->db->get()->row_array();
        return $result;
    }

    function isExistIn_srp_erp_pos_valuepackdetail($menuPackItemID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_valuepackdetail');
        $this->db->where('menuPackItemID', $menuPackItemID);
        $result = $this->db->get()->row_array();
        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
    }


    function update_srp_erp_pos_menupackitem($id, $data)
    {
        $this->db->where('menuPackItemID', $id);
        $result = $this->db->update('srp_erp_pos_menupackitem', $data);
        //echo $this->db->last_query();
        return $result;
    }

    function update_srp_erp_pos_packgroupdetail($id, $data)
    {
        $this->db->where('packgroupdetailID', $id);
        $result = $this->db->update('srp_erp_pos_packgroupdetail', $data);
        //echo $this->db->last_query();
        return $result;
    }

    function create_warehouseMenuCategory($warehouseID, $menuCategoryID)
    {
        $companyID = current_companyID();
        $this->db->select('*');
        $this->db->from('srp_erp_pos_warehousemenucategory');
        $this->db->where('warehouseID', $warehouseID);
        $this->db->where('menuCategoryID', $menuCategoryID);
        $this->db->where('companyID', $companyID);
        $result = $this->db->get()->row_array();
        if (empty($result)) {

            $currentDatetime = format_date_mysql_datetime();

            $data['menuCategoryID'] = $menuCategoryID;
            $data['warehouseID'] = $warehouseID;
            $data['companyID'] = $companyID;
            $data['isActive'] = 1;
            $data['isDeleted'] = 0;
            $data['createdUserID'] = current_userID();
            $data['createdDateTime'] = $currentDatetime;
            $data['createdUserName'] = current_user();
            $data['timeStamp'] = $currentDatetime;

            $this->db->insert('srp_erp_pos_warehousemenucategory', $data);
            $id = $this->db->insert_id();
            return $id;
        } else {
            return $result['autoID'];
        }
    }

    function create_warehouseMenu($data = array())
    {

        $companyID = current_companyID();
        $currentDatetime = format_date_mysql_datetime();

        $data['companyID'] = $companyID;
        $data['isActive'] = $this->input->post('menuStatus');
        $data['isDeleted'] = 0;
        $data['createdPCID'] = current_pc();
        $data['createdUserID'] = current_userID();
        $data['createdDateTime'] = $currentDatetime;
        $data['createdUserName'] = current_user();
        $data['timeStamp'] = $currentDatetime;

        $this->db->insert('srp_erp_pos_warehousemenumaster', $data);

    }

    /**
     * @param null $data
     * @return mixed : menuPackItemID
     */
    function create_menuPackItem($data = null)
    {
        $currentDatetime = format_date_mysql_datetime();

        $data['isRequired'] = 0;
        $data['createdBy'] = current_userID();
        $data['createdDatetime'] = $currentDatetime;
        $data['createdPC'] = current_pc();
        $data['timestamp'] = $currentDatetime;

        $this->db->insert('srp_erp_pos_menupackitem', $data);
        return $this->db->insert_id();

    }


    function get_menuPackGroupMaster_specific($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_menupackgroupmaster');
        $this->db->where('groupMasterID', $id);
        $r = $this->db->get()->row_array();
        return $r;
    }

    function create_packGropuDetail($data = null)
    {
        $currentDatetime = format_date_mysql_datetime();

        $data['isActive'] = 1;
        $data['createdBy'] = current_userID();
        $data['createdDatetime'] = $currentDatetime;
        $data['createdPc'] = current_pc();
        $data['timestamp'] = $currentDatetime;

        $this->db->insert('srp_erp_pos_packgroupdetail', $data);
        return $this->db->insert_id();
    }

    function get_srp_erp_pos_kitchenlocation_specific($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_kitchenlocation');
        $this->db->where('kitchenLocationID', $id);
        $result = $this->db->get()->row_array();
        return $result;
    }

    function delete_srp_erp_pos_kitchenlocation($id)
    {
        $this->db->where('kitchenLocationID', $id);
        $results = $this->db->delete('srp_erp_pos_kitchenlocation');
        if ($results) {
            return array('error' => 0, 'message' => 'Records Deleted Successfully');
        }
    }

    function update_srp_erp_pos_kitchenlocation($id, $data)
    {
        $this->db->where('kitchenLocationID', $id);
        $result = $this->db->update('erp_pos_kitchenlocation', $data);
        return $result;
    }

    function save_kotLocation()
    {
        $segmentConfigID = $this->input->post('segmentConfigID');
        $outletID = get_outletID($segmentConfigID);

        $this->db->set('description', $this->input->post('description'));
        $this->db->set('companyID', current_companyID());
        $this->db->set('outletID', $outletID);

        $result = $this->db->insert('srp_erp_pos_kitchenlocation');
        if ($result) {
            return array('error' => 0, 'message' => 'Crew Added Successfully');
        } else {
            return array('error' => 1, 'message' => 'error while inserting, please contact the system support team.');
        }
    }

    function update_kotID()
    {
        $key = $this->input->post('key');
        $value = $this->input->post('value');
        $data['kotID'] = $value;
        $this->db->where('warehouseMenuID', $key);
        $result = $this->db->update('srp_erp_pos_warehousemenumaster', $data);

        if ($result) {
            return array('error' => 0, 'message' => 'Kitchen location updated successfully');
        } else {
            return array('error' => 1, 'message' => 'error while updating, please contact the system support team.');
        }

    }

    function saveGLConfigDetail()
    {
        $this->db->select('ID');
        $this->db->from('srp_erp_pos_paymentglconfigdetail');
        $this->db->where('paymentConfigMasterID', $this->input->post('paymentConfigMasterID'));
        $this->db->where('companyID', current_companyID());
        $this->db->where('warehouseID', $this->input->post('warehouseID'));
        $ID = $this->db->get()->row('ID');

        if (!$ID) {
            $data['paymentConfigMasterID'] = $this->input->post('paymentConfigMasterID');
            $data['GLCode'] = $this->input->post('GLCode');
            $data['companyID'] = current_companyID();
            $data['companyCode'] = current_companyCode();
            $data['warehouseID'] = $this->input->post('warehouseID');
            $data['createdUserGroup'] = user_group();
            $data['createdPCID'] = current_pc();
            $data['createdUserID'] = current_userID();
            $data['createdUserName'] = current_user();
            $data['createdDateTime'] = format_date_mysql_datetime();
            $data['timestamp'] = format_date_mysql_datetime();

            $r = $this->db->insert('srp_erp_pos_paymentglconfigdetail', $data);
            if ($r) {
                return array('error' => 0, 'message' => 'Payment type added successfully!');
            } else {
                return array('error' => 1, 'message' => 'Error while saving, please contact your system support team!');
            }
        } else {
            return array('error' => 1, 'message' => 'This record is already added!.');
        }


    }

    /****Shafri****/
    function POSR_posGL_config()
    {
        $glAutoID = $this->input->post('glAutoID');
        $paymentTypeID = $this->input->post('paymentTypeID');
        $load_posGLData = $this->load_posGL($paymentTypeID);


        if (!isset($load_posGLData)) {
            $data = array(
                'GLCode' => $glAutoID,
                'paymentConfigMasterID' => $paymentTypeID,
                'companyID' => current_companyID(),
                'companyCode' => current_companyCode(),
                'createdPCID' => current_pc(),
                'createdUserID' => current_userID(),
                'createdUserName' => current_employee(),
                'createdUserGroup' => current_user_group(),
                'createdDateTime' => current_date()
            );

            $this->db->insert('srp_erp_pos_paymentglconfigdetail', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'GL code register successfully');
            } else {
                return array('e', 'Error in GL code registration');
            }
        } else {
            $data = array(
                'GLCode' => $glAutoID,
                'warehouseID' => $this->input->post('warehouseID'),
                'modifiedPCID' => current_pc(),
                'modifiedUserID' => current_userID(),
                'modifiedUserName' => current_employee(),
                'modifiedDateTime' => current_date()
            );

            $where = array('ID' => $this->input->post('ID'));

            $this->db->where($where)->update('srp_erp_pos_paymentglconfigdetail', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'GL code register successfully');
            } else {
                return array('e', 'Error in GL code registration');
            }
        }
    }

    function get_menuTax($id)
    {
        $this->db->select('taxmaster.taxDescription, taxmaster.taxShortCode, menuTax.* ');
        $this->db->from('srp_erp_pos_menutaxes menuTax');
        $this->db->join('srp_erp_taxmaster taxmaster', 'taxmaster.taxMasterAutoID = menuTax.taxmasterID', 'inner');
        $this->db->where('menuTax.menuMasterID', $id);
        $this->db->where('menuTax.companyID', current_companyID());
        $r = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $r;
    }

    function get_menuServiceCharge($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_menuservicecharge');
        $this->db->where('menuMasterID', $id);
        $this->db->where('companyID', current_companyID());
        $r = $this->db->get()->result_array();
        return $r;
    }

    function save_menuTax()
    {
        $this->db->trans_start();

        $data['menuMasterID'] = $this->input->post('menuMasterID');
        $data['taxmasterID'] = $this->input->post('taxmasterID');
        $data['taxPercentage'] = $this->input->post('taxPercentage');
        $data['taxAmount'] = $this->input->post('taxAmount');
        $data['companyID'] = current_companyID();
        $data['companyCode'] = current_companyCode();
        $data['createdUserGroup'] = user_group();
        $data['createdPCID'] = current_pc();
        $data['createdUserID'] = current_userID();
        $data['createdDateTime'] = format_date_mysql_datetime();
        $data['createdUserName'] = current_user();
        $data['timestamp'] = format_date_mysql_datetime();

        $result = $this->db->insert('srp_erp_pos_menutaxes', $data);
        $insert_id = $this->db->insert_id();

        /**  Update total tax in to menuMaster */

        $q = "UPDATE srp_erp_pos_menumaster SET totalTaxAmount = IF(( SELECT sum(taxAmount) AS totalTaxAmount FROM srp_erp_pos_menutaxes WHERE menuMasterID = '" . $data['menuMasterID'] . "' )>0,( SELECT sum(taxAmount) AS totalTaxAmount FROM srp_erp_pos_menutaxes WHERE menuMasterID = '" . $data['menuMasterID'] . "' ) , 0) WHERE menuMasterID = '" . $data['menuMasterID'] . "'";
        $this->db->query($q);

        $this->update_selling_price($data['menuMasterID']);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('error' => 1, 'message' => 'Error while adding tax, please contact your system support team.');
        } else {
            $this->db->trans_commit();
            return array('error' => 0, 'message' => 'Tax Added Successfully', 'result' => $result, 'last_id' => $insert_id, 'menuID' => $data['menuMasterID']);

        }
    }

    function save_serviceCharge()
    {
        $data['menuMasterID'] = $this->input->post('menuMasterID');
        $data['serviceChargePercentage'] = $this->input->post('serviceChargePercentage');
        $data['serviceChargeAmount'] = $this->input->post('serviceChargeAmount');
        $data['GLAutoID'] = $this->input->post('GLAutoID');
        $data['companyID'] = current_companyID();
        $data['companyCode'] = current_companyCode();
        $data['createdUserGroup'] = user_group();
        $data['createdPCID'] = current_pc();
        $data['createdUserID'] = current_userID();
        $data['createdDateTime'] = format_date_mysql_datetime();
        $data['createdUserName'] = current_user();
        $data['timestamp'] = format_date_mysql_datetime();

        $result = $this->db->insert('srp_erp_pos_menuservicecharge', $data);
        $insert_id = $this->db->insert_id();

        /**  Update total tax in to menuMaster */

        $q = "UPDATE srp_erp_pos_menumaster SET totalServiceCharge = IF(( SELECT sum(serviceChargeAmount) AS totalServiceCharge FROM srp_erp_pos_menuservicecharge WHERE menuMasterID = '" . $data['menuMasterID'] . "' )>0,( SELECT sum(serviceChargeAmount) AS totalServiceCharge FROM srp_erp_pos_menuservicecharge WHERE menuMasterID = '" . $data['menuMasterID'] . "' ) , 0) WHERE menuMasterID = '" . $data['menuMasterID'] . "'";
        $this->db->query($q);

        $this->update_selling_price($data['menuMasterID']);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('error' => 1, 'message' => 'Error while adding tax, please contact your system support team.');
        } else {
            $this->db->trans_commit();
            return array('error' => 0, 'message' => 'Tax Added Successfully', 'result' => $result, 'last_id' => $insert_id, 'menuID' => $data['menuMasterID']);

        }

    }

    function update_warehouseIsTaxEnabled()
    {

        $data['isTaxEnabled'] = ($this->input->post('checkedStatus'));

        $this->db->where('warehouseMenuID', $this->input->post('warehouseMenuID'));
        $result = $this->db->update('srp_erp_pos_warehousemenumaster', $data);
        if ($result) {
            return array('error' => 0, 'message' => 'successfully updated', 'result' => $result);
        }
    }

    function update_selling_price($menuMasterID)
    {
        $q1 = "SELECT ( pricewithoutTax + totalServiceCharge + totalTaxAmount ) AS currentTotal FROM srp_erp_pos_menumaster WHERE menuMasterID = '" . $menuMasterID . "' ";
        $r1 = $this->db->query($q1)->row_array();
        if (!empty($r1) && $r1['currentTotal'] > 0) {
            $q2 = "UPDATE srp_erp_pos_menumaster SET sellingPrice = '" . $r1['currentTotal'] . "' WHERE menuMasterID = '" . $menuMasterID . "'";
            $this->db->query($q2);
        }
    }

    function warehouse_image_upload()
    {
        $this->db->trans_start();
        $output_dir = "uploads/warehouses/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/warehouses", 007);
        }
        $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = current_companyCode() . '_' . current_companyID() . '_' . trim($this->input->post('wareHouseAutoID')) . time() . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['warehouseImage'] = $fileName;

        $this->db->where('wareHouseAutoID', trim($this->input->post('wareHouseAutoID')));
        $this->db->update('srp_erp_warehousemaster', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Image Upload Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Image uploaded  Successfully.');
        }
    }

    function fetch_itemrecode_yeild()
    {
        $dataArr = array();
        $dataArr2 = array();
        $dataArr2['query'] = 'test';
        $companyCode = $this->common_data['company_data']['company_code'];
        $search_string = "%" . $_GET['query'] . "%";
        $yieldID = $_GET['param'];
        $data = $this->db->query('SELECT mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT(itemDescription, " (" ,itemSystemCode,")") AS "Match" , isSubitemExist FROM srp_erp_itemmaster WHERE NOT EXISTS (SELECT * FROM srp_erp_pos_menuyields WHERE srp_erp_pos_menuyields.itemAutoID = srp_erp_itemmaster.itemAutoID AND yieldID=' . $yieldID . ') AND (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR seconeryItemCode LIKE "' . $search_string . '") AND companyCode = "' . $companyCode . '" AND isActive="1"')->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['itemSystemCode'], 'itemAutoID' => $val['itemAutoID'], 'currentStock' => $val['currentStock'], 'defaultUnitOfMeasure' => $val['defaultUnitOfMeasure'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'companyLocalSellingPrice' => $val['companyLocalSellingPrice'], 'companyLocalWacAmount' => $val['companyLocalWacAmount'], 'isSubitemExist' => $val['isSubitemExist']);
            }

        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    /*pos authentication*/
    function update_auth_process_isactive()
    {
        $data['isActive'] = ($this->input->post('chkedvalue'));
        $this->db->where('processMasterID', $this->input->post('processMasterID'));
        $this->db->where('companyID', current_companyID());
        $result = $this->db->update('srp_erp_pos_auth_processassign', $data);
        if ($result) {
            return array('error' => 0, 'message' => 'successfully updated', 'result' => $result);
        }
    }

    function addProcess()
    {
        $this->db->select('processMasterID');
        $this->db->from('srp_erp_pos_auth_processassign');
        $this->db->where('companyID', current_companyID());
        $r = $this->db->get()->result_array();

        $existRec = array_column($r, 'processMasterID');
        $process = $this->input->post("processMasterID");
        foreach ($process as $val) {
            if (!in_array($val, $existRec)) {
                $data['processMasterID'] = $val;
                $data['companyID'] = current_companyID();
                $data['createdUserGroup'] = user_group();
                $data['createdPCID'] = current_pc();
                $data['createdUserID'] = current_userID();
                $data['createdDateTime'] = format_date_mysql_datetime();
                $data['createdUserName'] = current_user();
                $data['timestamp'] = format_date_mysql_datetime();
                $insert_query = $this->db->insert('srp_erp_pos_auth_processassign', $data);
            }
        }
        return array('error' => 0, 'message' => 'Successfully assigned');
    }

    function wifi_password_check($wifi_password, $outletID, $companyID)
    {
        $this->db->select('wifiPassword');
        $this->db->from('srp_erp_pos_wifipasswordsetup');
        $this->db->where('outletID', $outletID);
        $this->db->where('companyID', $companyID);
        $this->db->where('wifiPassword', $wifi_password);
        $this->db->where('isUsed', 0);
        $query = $this->db->get()->row_array();
        //$rowcount = $query->num_rows();
        return $query['wifiPassword'];
    }

    function add_user_group()
    {
        $this->db->select('userGroupMasterID');
        $this->db->from('srp_erp_pos_auth_usergroupdetail');
        $this->db->where('companyID', current_companyID());
        $this->db->where('processMasterID', $this->input->post('processMasterID'));
        $this->db->where('wareHouseID', $this->input->post('wareHouseID'));
        $r = $this->db->get()->result_array();

        $existRec = array_column($r, 'userGroupMasterID');
        $userGroupMasterID = $this->input->post("userGroupMasterID");
        $processMasterID = $this->input->post("processMasterID");
        foreach ($userGroupMasterID as $val) {
            if (!in_array($val, $existRec)) {
                $data['userGroupMasterID'] = $val;
                $data['processMasterID'] = $processMasterID;
                $data['wareHouseID'] = $this->input->post('wareHouseID');
                $data['companyID'] = current_companyID();
                $data['createdUserGroup'] = user_group();
                $data['createdPCID'] = current_pc();
                $data['createdUserID'] = current_userID();
                $data['createdDateTime'] = format_date_mysql_datetime();
                $data['createdUserName'] = current_user();
                $data['timestamp'] = format_date_mysql_datetime();
                $insert_query = $this->db->insert('srp_erp_pos_auth_usergroupdetail', $data);
            }
        }
        return array('error' => 0, 'message' => 'Successfully assigned');
    }

    function delete_assigned_user_group()
    {
        $this->db->where('userGroupDetailID', $this->input->post('userGroupDetailID'));
        $result = $this->db->delete('srp_erp_pos_auth_usergroupdetail');
        if ($result) {
            return array('error' => 0, 'message' => 'Records Deleted Successfully');

        }
    }
    /*end posauthentication*/
}