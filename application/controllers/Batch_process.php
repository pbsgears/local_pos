<?php
/**
 *
 * -- =============================================
 * -- File Name : Batch_process.php
 * -- Project Name : POS
 * -- Module Name : Batch Job
 * -- Author : Mohamed Shafri
 * -- Create date : 04 March 2017
 * -- Description : to run the schedule task or cron job
 *
 * --REVISION HISTORY
 * --Date: 04-MAR 2017 By: Mohamed Shafri: file created
 * --
 * -- =============================================
 **/
defined('BASEPATH') OR exit('No direct script access allowed');

class Batch_process extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('pos');
        $this->load->model('Pos_config_model');
        $this->load->model('Pos_model');
        $this->load->model('Pos_kitchen_model');
        $this->load->model('Pos_restaurant_model');
        $this->load->model('Batch_process_model');
        echo '<pre>';
    }


    /**
     * To update the menu ID & menu Category ID to srp_erp_pos_menusalesitems
     *
     * we created new column following column recently;
     *
     * srp_erp_pos_menusalesitems.menuID
     * srp_erp_pos_menusalesitems.menuCategoryID
     *
     * */
    function batch_update_menuSalesItems_menuID_menuCategoryID()
    {

        $this->db->trans_start();
        try {

            $r = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_all();

            $data = array();
            if (!empty($r)) {
                $i = 0;
                foreach ($r as $item) {
                    $key = $item['menuSalesItemID'];

                    if (empty($item['menuID'])) {
                        $data[$i]['menuSalesItemID'] = $key;
                        $data[$i]['menuID'] = $item['menuMasterID'];
                        $data[$i]['menuCategoryID'] = $item['menuCategoryID'];
                    }

                    $i++;
                }

                if (!empty($data)) {
                    $row = $this->Batch_process_model->updaet_batch_srp_erp_pos_menusalesitems($data);
                } else {
                    $row = 0;
                }

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('error' => 1, 'message' => 'error while inserting, message: ' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('error' => 0, 'message' => 'batch completed, ' . $row . ' row/s updated'));
                    if (!empty($data)) {
                        //print_r($data);
                    }
                }

            } else {
                echo json_encode(array('error' => 0, 'message' => 'batch completed , empty menuSalesItems table'));
            }

        } catch (Exception $e) {
            $this->db->trans_complete();
            $exp = $e->getMessage();
            $this->db->trans_rollback();
            echo json_encode(array('error' => 1, 'message' => $exp));
        }


    }


    function batch_update_sales_vs_profit()
    {
        //echo current_warehouseID();
        $this->db->trans_start();
        try {

            $r = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster_all();

            $data = array();
            if (!empty($r)) {

                $i = 0;

                foreach ($r as $item) {

                    $menuSalesID = $item['menuSalesID'];
                    //echo $menuSalesID .'-' . $item['menuCost'] . '<br/>';

                    if ($item['menuCost'] < 1) {
                        $this->Pos_restaurant_model->updateTotalCost($menuSalesID);
                        $data[$i]['id'] = $menuSalesID;

                    }
                    $i++;
                }


                $row = count($data);

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('error' => 1, 'message' => 'error while inserting, message: ' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('error' => 0, 'message' => 'batch completed, ' . $row . ' row/s updated'));
                    if (!empty($data)) {
                       // print_r($data);
                    }

                }

            } else {
                echo json_encode(array('error' => 0, 'message' => 'batch completed , empty menuSalesItems table'));
            }

        } catch (Exception $e) {
            $this->db->trans_complete();
            $exp = $e->getMessage();
            $this->db->trans_rollback();
            echo json_encode(array('error' => 1, 'message' => $exp));
        }
    }

}