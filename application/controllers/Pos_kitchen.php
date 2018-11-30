<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* -- =============================================
-- File Name : Pos_kitchen.php
-- Project Name : POS
-- Module Name : POS Kitchen Controller
-- Author : Mohamed Shafri
-- Create date : 24 - January 2017
-- Description : SME POS System.

--REVISION HISTORY
--Date: 24 - JAN 2017 By: Mohamed Shafri: comment started

-- =============================================
 */


class Pos_kitchen extends ERP_Controller
{


    function __construct()
    {
        parent::__construct();
        if (empty(trim($this->common_data['status']))) {
            header('Location: ' . site_url('Login/logout'));
            exit;
        } else {
            $this->load->model('Pos_kitchen_model');
            $this->load->model('Pos_restaurant_model');
            $this->load->helper('cookie');
            $this->load->helper('pos');
            $this->load->library('CloudPrints');
        }


    }

    function index()
    {
        $data['title'] = 'POS Kitchen';
        $data['extra'] = 'sidebar-collapse fixed';
        $data['refNo'] = '';
        $data['common_data'] = $this->common_data;
        $data['pendingOrders'] = $this->Pos_kitchen_model->get_pendingOrders();
        $data['pendingOrdersCount'] = $this->Pos_kitchen_model->get_pendingOrdersCount();
        $data['currentOrders'] = $this->Pos_kitchen_model->get_currentOrders();
        $data['currentOrdersCount'] = $this->Pos_kitchen_model->get_currentOrdersCount();

        $this->load->view('system/pos/kitchen/pos_kitchen', $data);
    }

    function kitchen2()
    {

        $locationID = $this->uri->segment('2');
        if ($locationID == 0) {
            $defaultLocation = get_kitchenLocation_default();
            if (!empty($defaultLocation)) {
                $locationID = $defaultLocation['kitchenLocationID'];
            }
        }

        $data['title'] = 'POS Kitchen';
        $data['extra'] = 'sidebar-collapse fixed';
        $data['refNo'] = '';
        $data['locationID'] = $locationID;
        $data['common_data'] = $this->common_data;
        $data['pendingOrders'] = $this->Pos_kitchen_model->get_pendingOrders_kitchen2($locationID);
        $data['currentOrders'] = $this->Pos_kitchen_model->get_currentOrders($locationID);


        $this->load->view('system/pos/kitchen/pos_kitchen2', $data);
    }

    function kitchen_autoPrint()
    {

        $locationID = $this->uri->segment('2');
        if ($locationID == 0) {
            $defaultLocation = get_kitchenLocation_default();
            if (!empty($defaultLocation)) {
                $locationID = $defaultLocation['kitchenLocationID'];
            }
        }

        $outletInfo = get_outletInfo();
        $data['title'] = 'KOT : ' . $outletInfo['wareHouseCode'] . ' - ' . $outletInfo['wareHouseDescription'];
        $data['extra'] = 'sidebar-collapse fixed';
        $data['locationID'] = $locationID;

        /*ajax loading content : refreshOrderListContainer_autoPrint */
        $this->load->view('system/pos/kitchen/pos_kitchen_autoPrint', $data);
    }

    function refreshOrderListContainer_autoPrint()
    {

        $kotID = $this->input->post('kotID');
        $pendingOrders = $this->Pos_kitchen_model->get_invoiceIDs_pendingOrders_autoPrint($kotID);
        $data['pendingOrders'] = $pendingOrders;

        /*print_r($data);
        exit;*/


        $data['currentOrders'] = array();
        $data['kotID'] = $kotID;

        $html = '';
        if (!empty($pendingOrders)) {
            foreach ($pendingOrders as $pendingOrders) {
                $data['masters'] = $this->Pos_kitchen_model->get_srp_erp_pos_menusalesmaster_kitchenStatus($pendingOrders['menuSalesID']);
                $data['invoiceList'] = $this->Pos_kitchen_model->get_srp_erp_pos_menusalesitems_invoiceID_kotAlarm($pendingOrders['menuSalesID'], $kotID);
                $data['print'] = false;
                $data['newBill'] = false;
                $html .= $this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer-pdf', $data, true);
                $html .= "<br/>";


            }
        }
        $html .= " <br/>.<br/><br/>";
        $data['html'] = $html;


        $this->load->view('system/pos/kitchen/ajax/ajax-pos-kitchen-refresh-auto-print', $data);
    }

    function kitchen2_backup()
    {
        $locationID = $this->uri->segment('2');
        if ($locationID == 0) {
            $defaultLocation = get_kitchenLocation_default();
            if (!empty($defaultLocation)) {
                $locationID = $defaultLocation['kitchenLocationID'];
            }
        }

        $data['title'] = 'POS Kitchen';
        $data['extra'] = 'sidebar-collapse fixed';
        $data['refNo'] = '';
        $data['locationID'] = $locationID;
        $data['common_data'] = $this->common_data;
        $data['pendingOrders'] = $this->Pos_kitchen_model->get_pendingOrders_kitchen2($locationID);
        $data['currentOrders'] = $this->Pos_kitchen_model->get_currentOrders($locationID);


        $this->load->view('system/pos/kitchen/pos_kitchen2', $data);
    }

    function kitchen_manual_process()
    {
        $locationID = $this->uri->segment('2');
        if ($locationID == 0) {
            $defaultLocation = get_kitchenLocation_default();
            if (!empty($defaultLocation)) {
                $locationID = $defaultLocation['kitchenLocationID'];
            }
        }

        $data['title'] = 'POS Kitchen';
        $data['extra'] = 'sidebar-collapse fixed';
        $data['refNo'] = '';
        $data['common_data'] = $this->common_data;
        $data['pendingOrders'] = $this->Pos_kitchen_model->get_pendingOrders_kitchen2($locationID);
        $data['currentOrders'] = $this->Pos_kitchen_model->get_currentOrders_kitchen2($locationID);

        $this->load->view('system/pos/kitchen/pos_kitchen_manual', $data);
    }

    function kitchen_manual_process_ajax()
    {
        $kotID = $this->input->post('kotID');

        $data['pendingOrders'] = $this->Pos_kitchen_model->get_pendingOrders_kitchen2($kotID);
        $data['currentOrders'] = $this->Pos_kitchen_model->get_currentOrders_kitchen2($kotID);
        $this->load->view('system/pos/kitchen/ajax/ajax-pos-kitchen-refresh-manual', $data);
    }


    function refreshOrderListContainerJava()
    {

        $kotID = $this->input->post('kotID');
        $data['pendingOrders'] = $this->Pos_kitchen_model->get_pendingOrders_kitchenJava($kotID);
        $data['Set1'] = $this->Pos_kitchen_model->get_nTh_set($kotID, 0);
        $data['Set2'] = $this->Pos_kitchen_model->get_nTh_set($kotID, 1);


        $data['kotID'] = $kotID;
        //$this->load->view('system/pos/kitchen/ajax/ajax-pos-kitchen-refresh', $data);
        $this->load->view('system/pos/kitchen/ajax/ajax-pos-kitchen-refresh-java', $data);
    }

    function refreshOrderListContainer2()
    {

        $kotID = $this->input->post('kotID');

        $data['pendingOrders'] = $this->Pos_kitchen_model->get_pendingOrders_kitchen2($kotID);
        //$data['currentOrders'] = $this->Pos_kitchen_model->get_currentOrders_kitchen2($kotID);
        //echo $this->db->last_query();
        $data['currentOrders'] = array(); //$this->Pos_kitchen_model->get_currentOrders_kitchen2($kotID);
        $data['kotID'] = $kotID;
        $this->load->view('system/pos/kitchen/ajax/ajax-pos-kitchen-refresh', $data);
    }


    public function load_currencyDenominationPage()
    {
        $currencyCode = $this->common_data['company_data']['company_default_currency'];
        $wareHouseID = $this->common_data['ware_houseID'];
        $data['session_data'] = $this->Pos_kitchen_model->isHaveNotClosedSession();
        $data['denomination'] = $this->Pos_kitchen_model->currencyDenominations($currencyCode);
        $data['counters'] = $this->Pos_kitchen_model->load_wareHouseCounters($wareHouseID);
        $data['dPlace'] = $this->common_data['company_data']['company_default_decimal'];
        $data['isRestaurant'] = true;
        $data['code'] = 1;

        $this->load->view('system/pos/ajax/currency_denomination_view', $data);
    }

    function refreshPosKitchenWindow()
    {
        echo json_encode(array('error' => 0));
    }

    function updateToCurrent()
    {

        $menuSalesID = $this->input->post('menuSalesID');
        $value = $this->input->post('value');
        $kotID = $this->input->post('kotID');


        /** POST VALUES
         * [menuSalesID] => 1018
         * [value] => 1 always 1
         * [kotID] => 1  kitchen location id
         */

        $q = "SELECT
                    salesItem.menuSalesID,
                    warehouseMenu.menuMasterID,
                    warehouseMenu.kotID
                FROM
                    srp_erp_pos_menusalesitems salesItem
                JOIN srp_erp_pos_warehousemenumaster warehouseMenu ON salesItem.menuID = warehouseMenu.menuMasterID 
                WHERE
                        salesItem.kotID IS NOT NULL
                    AND salesItem.kotID <> '$kotID'
                    AND salesItem.kotID > 0 
                    AND salesItem.isOrderPending = 1
                    AND salesItem.menuSalesID = '$menuSalesID'
                GROUP BY salesItem.menuSalesItemID ";


        $isMenuExistForAnotherKitchen = $this->db->query($q)->row('menuMasterID');
        /*echo $this->db->last_query();
        exit;*/

        if (!empty($isMenuExistForAnotherKitchen)) {
            /** There is item in the kitchen has to be processed */
            //$result = $this->updateMenuSalesItemKitchenStatus();
            $result = $this->updateToCurrent_status();
            if ($result) {
                echo json_encode(array('error' => 0, 'message' => 'updated', 'q' => $this->db->last_query()));
            } else {
                echo json_encode(array('error' => 1, 'message' => 'update fail'));
            }
        } else {
            /** Final Level Menu Item in the Kitchen */
            //$this->updateMenuSalesItemKitchenStatus();
            $this->updateToCurrent_status();

            /*$data_tmp['isOrderInProgress'] = 1;
            $data_tmp['is_sync'] = 0;*/
            //$result = $this->Pos_kitchen_model->update_srp_erp_pos_menusalesmaster($data_tmp, $menuSalesID, $kotID);

            $result = $this->Pos_kitchen_model->progressOrder_master($menuSalesID);

            if ($result) {
                echo json_encode(array('error' => 0, 'message' => 'updated..', 'q' => $this->db->last_query()));
            } else {
                echo json_encode(array('error' => 1, 'message' => 'final level update fail'));
            }
        }
    }

    function updateToCurrent_status()
    {
        $KOT_ID = $this->input->post('kotID');
        $menuSalesID = $this->input->post('menuSalesID');
        $q = "UPDATE `srp_erp_pos_menusalesitems` SET `isOrderInProgress` = 1, isOrderCompleted =0 WHERE `menuSalesID` = '" . $menuSalesID . "' AND `kotID` = '" . $KOT_ID . "' AND isOrderPending = 1 AND isOrderInProgress=0";
        return $this->db->query($q);
    }

    function updateMenuSalesItemKitchenStatus()
    {
        /** POST VALUES
         * [menuSalesID] => 1018
         * [value] => 1 always 1
         * [kotID] => 1  kitchen location id
         */
        //$data['isOrderPending'] = 0;
        $data['isOrderInProgress'] = 1;
        $this->db->where('menuSalesID', $this->input->post('menuSalesID'));
        $this->db->where('kotID', $this->input->post('kotID'));
        $result = $this->db->update('srp_erp_pos_menusalesitems', $data);

        return $result;

    }

    function updateToCompleted()
    {
        $menuSalesID = $this->input->post('menuSalesID'); // $id changed to $menuSalesID
        $value = $this->input->post('value');
        $kotID = $this->input->post('kotID');

        /** POST VALUES
         * [menuSalesID] => 1018
         * [value] => 1 always 1
         * [kotID] => 1  kitchen location id
         */

        $q = "SELECT
                    salesItem.menuSalesID,
                    warehouseMenu.menuMasterID,
                    warehouseMenu.kotID
                FROM
                    srp_erp_pos_menusalesitems salesItem
                JOIN srp_erp_pos_warehousemenumaster warehouseMenu ON salesItem.menuID = warehouseMenu.menuMasterID
                WHERE
                    salesItem.kotID IS NOT NULL
                AND salesItem.kotID <> '$kotID'
                AND salesItem.kotID > 0 
                -- AND salesItem.isOrderPending = 1
                -- AND salesItem.isOrderInProgress = 1
                -- AND (salesItem.isOrderPending = 1 OR salesItem.isOrderInProgress = 1 OR salesItem.isOrderCompleted = 0)
                AND ( (salesItem.isOrderPending = 1 OR salesItem.isOrderInProgress = 1) AND  salesItem.isOrderCompleted = 0)
                AND salesItem.menuSalesID = '" . $menuSalesID . "'
                GROUP BY salesItem.menuSalesItemID";


        $isMenuExistForAnotherKitchen = $this->db->query($q)->row('menuMasterID');
        if ($isMenuExistForAnotherKitchen) {


            $result = $this->updateMenuSalesItemKitchenStatus_final();
            if ($result) {
                echo json_encode(array('error' => 0, 'message' => 'updated'));
            } else {
                echo json_encode(array('error' => 1, 'message' => 'update fail'));
            }
        } else {
            /** Final Level */
            $this->updateMenuSalesItemKitchenStatus_final();

            /*$data['isOrderCompleted'] = 1;
            $data['is_sync'] = 0;
            $result = $this->Pos_kitchen_model->update_srp_erp_pos_menusalesmaster($data, $menuSalesID);*/
            $result = $this->Pos_kitchen_model->completeOrder_master($menuSalesID);

            if ($result) {
                echo json_encode(array('error' => 0, 'message' => 'updated..'));
            } else {
                echo json_encode(array('error' => 1, 'message' => 'update fail'));
            }
        }


    }

    function updateMenuSalesItemKitchenStatus_final()
    {
        $KOT_ID = $this->input->post('kotID');
        $menuSalesID = $this->input->post('menuSalesID');
        $q = "UPDATE `srp_erp_pos_menusalesitems` SET `isOrderInProgress` = 1, isOrderCompleted =1 WHERE `menuSalesID` = '" . $menuSalesID . "' AND `kotID` = '" . $KOT_ID . "' AND isOrderPending = 1 AND isOrderInProgress=1";
        return $this->db->query($q);
    }

    function refreshOrderListContainer()
    {
        $data['pendingOrders'] = $this->Pos_kitchen_model->get_pendingOrders();
        $data['pendingOrdersCount'] = $this->Pos_kitchen_model->get_pendingOrdersCount();
        $data['currentOrders'] = $this->Pos_kitchen_model->get_currentOrders();
        $data['currentOrdersCount'] = $this->Pos_kitchen_model->get_currentOrdersCount();
        $this->load->view('system/pos/kitchen/ajax/ajax-pos-kitchen-refresh', $data);
    }

    function loadKitchenStatusPreview()
    {
        $invoiceID = $this->input->post('invoiceID');
        $masters = $this->Pos_kitchen_model->get_srp_erp_pos_menusalesmaster_kitchenStatus($invoiceID);
        $invoiceList = $this->Pos_kitchen_model->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID);
        $data['masters'] = $masters;
        $data['invoiceList'] = $invoiceList;

        $this->load->view('system/pos/printTemplate/restaurant-pos-kitchen-status', $data);
    }

    function loadKitchenReady()
    {
        $counterData = get_counterData();
        $shiftID = $counterData['shiftID'];
        $companyID = $counterData['companyID'];

        $this->datatables->select('menuSalesID as menuSalesID, invoiceCode as invoiceID, holdByUsername as createdUser, if(ISNULL(holdDatetime), "-",DATE_FORMAT(holdDatetime,\'%d-%b-%Y\'))  as holdDate, if(ISNULL(holdRemarks), "-", holdRemarks) as remarks, DATE_FORMAT(createdDateTime,\'%d-%b-%Y\') as createdDate, isHold as isHold , isOrderPending as isOrderPending, isOrderInProgress as isOrderInProgress, isOrderCompleted as isOrderCompleted, master.invoiceSequenceNo as invoiceSequenceNo', false)
            ->from('srp_erp_pos_menusalesmaster master')
            //->add_column('invoiceID', '$1', 'get_pos_invoice_code(menuSalesID)')
            ->add_column('openHold', '$1', 'btn_viewKitchenStatus(menuSalesID,\'Open\')')
            ->add_column('status', '$1', 'kitchen_status(isHold,isOrderPending,isOrderInProgress,isOrderCompleted,PN)')
            ->add_column('isPending', '$1', 'kitchen_status(isHold,isOrderPending,isOrderInProgress,isOrderCompleted,PEN)')
            ->add_column('isInProgress', '$1', 'kitchen_status(isHold,isOrderPending,isOrderInProgress,isOrderCompleted,PRO)')
            ->add_column('isCompleted', '$1', 'kitchen_status(isHold,isOrderPending,isOrderInProgress,isOrderCompleted,COM)')
            ->add_column('sendKOT', '$1', 'kitchen_status(isHold,isOrderPending,isOrderInProgress,isOrderCompleted,KOT)');
        //->where('master.isHold', 1);
        // ->where('master.isOrderPending', 1);
        //->where('master.isOrderCompleted', 1)
        //->where('master.isOrderCompleted', format_date_mysql_datetime())
        //$this->datatables->like('createdDateTime', date('Y-m-d'));

        $this->datatables->where('master.shiftID', $shiftID);
        $this->datatables->where('master.companyID', $companyID);
        echo $this->datatables->generate();
        //echo $this->db->last_query();
    }

    function loadKitchenReady_tablet()
    {
        $counterData = get_counterData();
        $shiftID = $counterData['shiftID'];
        $companyID = $counterData['companyID'];

        $this->datatables->select('menuSalesID as menuSalesID, invoiceCode as invoiceID, holdByUsername as createdUser, if(ISNULL(holdDatetime), "-",DATE_FORMAT(holdDatetime,\'%d-%b-%Y\'))  as holdDate, if(ISNULL(holdRemarks), "-", holdRemarks) as remarks, DATE_FORMAT(createdDateTime,\'%d-%b-%Y\') as createdDate, isHold as isHold , isOrderPending as isOrderPending, isOrderInProgress as isOrderInProgress, isOrderCompleted as isOrderCompleted, master.invoiceSequenceNo as invoiceSequenceNo', false)
            ->from('srp_erp_pos_menusalesmaster master')
            //->add_column('invoiceID', '$1', 'get_pos_invoice_code(menuSalesID)')
            ->add_column('openHold', '$1', 'btn_viewKitchenStatus(menuSalesID,\'Open\')')
            ->add_column('status', '$1', 'kitchen_status(isHold,isOrderPending,isOrderInProgress,isOrderCompleted,PN)')
            ->add_column('isPending', '$1', 'kitchen_status(isHold,isOrderPending,isOrderInProgress,isOrderCompleted,PEN)')
            ->add_column('isInProgress', '$1', 'kitchen_status(isHold,isOrderPending,isOrderInProgress,isOrderCompleted,PRO)')
            ->add_column('isCompleted', '$1', 'kitchen_status(isHold,isOrderPending,isOrderInProgress,isOrderCompleted,COM)')
            ->add_column('sendKOT', '$1', 'kitchen_status(isHold,isOrderPending,isOrderInProgress,isOrderCompleted,KOT)');

        $this->datatables->where('master.shiftID', $shiftID);
        $this->datatables->where('master.companyID', $companyID);
        $this->datatables->where('master.BOT', 0);
        $this->datatables->where('createdUserID', current_userID());
        echo $this->datatables->generate();
    }

    function load_kitchen_ready()
    {
        $data['holdReceipt'] = null;
        $this->load->view('system/pos/ajax/ajax-restaurant-pos-kitchen-ready', $data);
    }

    function load_kitchen_ready_tablet()
    {
        $data['holdReceipt'] = null;
        $this->load->view('system/pos/ajax/ajax-restaurant-pos-kitchen-ready-tablet', $data);
    }

    function load_KOT_print_view()
    {
        $outletID = get_outletID();
        $companyID = current_companyID();
        $this->db->select('pd.*');
        $this->db->from('srp_erp_pos_policydetail pd');
        $this->db->join('srp_erp_pos_policymaster pm', 'pd.posPolicyMasterID = pm.posPolicyMasterID', 'inner');
        $this->db->where('outletID', $outletID);
        $this->db->where('pd.posPolicyMasterID', 1);
        $this->db->where('companyID', $companyID);
        $result = $this->db->get()->row_array();

        if (!empty($result)) {
            $invoiceID = $this->input->post('menuSalesID');
            $data['masters'] = $this->Pos_kitchen_model->get_srp_erp_pos_menusalesmaster_kitchenStatus($invoiceID);
            $data['invoiceList'] = $this->Pos_kitchen_model->get_srp_erp_pos_menusalesitems_invoiceID_kotPrint($invoiceID);

            $data['print'] = true;
            $data['newBill'] = false;

            $q2 = "UPDATE `srp_erp_pos_menusalesitems` SET `KOTFrontPrint` = 1 WHERE `menuSalesID` = '" . $invoiceID . "'";
            $this->db->query($q2);

            $html = $this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer-kot', $data, true);
            echo json_encode(array('error' => 0, 'auth' => 0, 'message' => "", 'html' => str_replace("\n", ' ', $html)));
        } else {
            echo json_encode(array('error' => 2, 'auth' => 1, 'message' => "Policy not configured"));
        }
    }

    function load_KOT_print_view_cloudPrint()
    {
        try {
            $outletID = get_outletID();
            $companyID = current_companyID();
            $this->db->select('pd.*');
            $this->db->from('srp_erp_pos_policydetail pd');
            $this->db->join('srp_erp_pos_policymaster pm', 'pd.posPolicyMasterID = pm.posPolicyMasterID', 'inner');
            $this->db->where('outletID', $outletID);
            $this->db->where('pd.posPolicyMasterID', 1);
            $this->db->where('companyID', $companyID);
            $result = $this->db->get()->row_array();

            if (!empty($result)) {
                $invoiceID = $this->input->post('menuSalesID');
                $data['masters'] = $this->Pos_kitchen_model->get_srp_erp_pos_menusalesmaster_kitchenStatus($invoiceID);
                $data['invoiceList'] = $this->Pos_kitchen_model->get_srp_erp_pos_menusalesitems_invoiceID_kotAlarm($invoiceID);
                $data['print'] = false;
                $data['newBill'] = false;
                $html = $this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer-pdf', $data, true);


                if (!empty($data['invoiceList'])) {
                    $printerID = get_outlet_printer();
                    $printer = $this->cloudprints->printCloudReceipt($printerID, $html);
                    if ($printer) {
                        echo json_encode(array('error' => 0, 'auth' => 1, 'message' => "Print sent"));
                    }
                }
            } else {
                echo json_encode(array('error' => 2, 'auth' => 1, 'message' => "Policy not configured"));
            }


        } catch (Exception $e) {

            $outletID = get_outletID();
            $companyID = current_companyID();
            $this->db->select('pd.*');
            $this->db->from('srp_erp_pos_policydetail pd');
            $this->db->join('srp_erp_pos_policymaster pm', 'pd.posPolicyMasterID = pm.posPolicyMasterID', 'inner');
            $this->db->where('outletID', $outletID);
            $this->db->where('pd.posPolicyMasterID', 1);
            $this->db->where('companyID', $companyID);
            $result = $this->db->get()->row_array();

            if (!empty($result)) {
                $invoiceID = $this->input->post('menuSalesID');
                $data['masters'] = $this->Pos_kitchen_model->get_srp_erp_pos_menusalesmaster_kitchenStatus($invoiceID);
                $data['invoiceList'] = $this->Pos_kitchen_model->get_srp_erp_pos_menusalesitems_invoiceID_kotAlarm($invoiceID);
                $data['print'] = false;
                $data['newBill'] = false;
                $html = $this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer-kot', $data, true);

                echo json_encode(array('error' => 1, 'auth' => 0, 'message' => "Error: Could not print. Message " . $e->getMessage(), 'html' => str_replace("\n", '&nbsp;', $html)));
            } else {
                echo json_encode(array('error' => 2, 'auth' => 1, 'message' => "Policy not configured"));
            }


        }

    }

    function load_KOT_print_view_pdf()
    {


        $outletID = get_outletID();
        $companyID = current_companyID();
        $this->db->select('pd.*');
        $this->db->from('srp_erp_pos_policydetail pd');
        $this->db->join('srp_erp_pos_policymaster pm', 'pd.posPolicyMasterID = pm.posPolicyMasterID', 'inner');
        $this->db->where('outletID', $outletID);
        $this->db->where('pd.posPolicyMasterID', 1);
        $this->db->where('companyID', $companyID);
        $result = $this->db->get()->row_array();


        if (!empty($result)) {
            $printSession = $this->session->userdata('accessToken');
            if (!empty($printSession) || true) {
                $invoiceID = $this->input->post('menuSalesID');
                $this->load_KOT_print_pdf($invoiceID);
                //$url = site_url('uploads/kot/kot.pdf');

                $url = str_replace('application\controllers', 'uploads', dirname(__FILE__) . '/kot/kot.pdf');

                $html = file_get_contents($url);
                $printerID = get_outlet_printer();

                $printer = $this->cloudprints->printCloudReceipt_pdf($printerID, $html);
                if ($printer) {
                    echo json_encode(array('error' => 0, 'auth' => 1, 'message' => "Print sent", 'id' => $printerID));
                } else {
                    echo json_encode(array('error' => 0, 'auth' => 1, 'message' => $printerID));

                }
            } else {
                $string = '<div class="alert alert-warning">
                <strong><i class="fa fa-info-circle"></i> Login </strong><br/>
                <p>Please Login to Google Account</p>

                <p><a href="' . STATIC_LINK . '/index.php/ReceiptPrint/oAuthRedirect?op=getauth">Click
                        here</a> to Login</p>
            </div>';
                $html = str_replace("\n", '', $string);
                echo json_encode(array('error' => 1, 'auth' => 0, 'message' => "Auth Fail", 'html' => $html));
            }


        } else {
            echo json_encode(array('error' => 2, 'auth' => 1, 'message' => "Policy not configured"));
        }
    }

    function load_KOT_print_pdf($invoiceID = null)
    {

        $data['masters'] = $this->Pos_kitchen_model->get_srp_erp_pos_menusalesmaster_kitchenStatus($invoiceID);
        $data['invoiceList'] = $this->Pos_kitchen_model->get_srp_erp_pos_menusalesitems_invoiceID_kotAlarm($invoiceID);
        $data['print'] = false;
        $data['newBill'] = false;
        $html = $this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer-pdf', $data, true);

        $this->load->library('pdf');
        $this->pdf->printed_pos($html);

    }

    function updateSendToKitchen()
    {
        echo json_encode($this->Pos_kitchen_model->updateSendToKitchen());
    }
}