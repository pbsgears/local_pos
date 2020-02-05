<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* -- =============================================
-- File Name : Pos_restaurant.php
-- Project Name : POS
-- Module Name : POS Restaurant Controller
-- Author : Mohamed Shafri
-- Create date : 25 - October 2016
-- Description : SME POS System.

--REVISION HISTORY
--Date: 25 - Oct 2016 By: Mohamed Shafri: comment started
--Date: 14 - NOV 2016 By: Mohamed Shafri: POS Footer functions
--Date: 15 - NOV 2016 By: Mohamed Shafri: POS Payment Receipt
--Date: 16 - NOV 2016 By: Mohamed Shafri: Hold invoice modal
--Date: 31 - DEC 2018 By: Mohamed Shafri: SME-1300 Local POS : Block Login if user is not assigned for current outlet.

-- =============================================
 */


class Pos_restaurant extends ERP_Controller
{


    function __construct()
    {
        parent::__construct();
        $status = $this->session->has_userdata('status');
        if (!$status) {
            header('Location: ' . site_url('Login'));
            exit;
        }

        $this->load->library('pos_policy');
        $this->load->model('Pos_restaurant_model');
        $this->load->model('Inventory_modal');
        $this->load->model('Pos_restaurant_accounts');
        $this->load->model('Pos_restaurant_accounts_gl_fix');
        $this->load->helper('cookie');
        $this->load->helper('pos');
    }

    function index()
    {

        $tmpWarehouseID = $this->Pos_restaurant_model->get_srp_erp_warehouse_users_WarehouseID();
        $warehouseID = isset($tmpWarehouseID) && !empty($tmpWarehouseID) ? $tmpWarehouseID : 0;

        $isHaveNotClosedSession = $this->Pos_restaurant_model->isHaveNotClosedSession();
        $isHadSession = (empty($isHaveNotClosedSession)) ? 0 : $isHaveNotClosedSession;
        if ($isHadSession != 0) {
            $counterDet = $this->Pos_restaurant_model->get_counterData($isHaveNotClosedSession['counterID']);
            $counterDet = $counterDet['counterCode'];
        } else {
            $counterDet = '';
        }

        /** Get Warehouse Menu Items */
        $output = $this->Pos_restaurant_model->get_warehouseMenues($warehouseID);

        /** Get warehouse Category */
        $output2 = $this->Pos_restaurant_model->get_warehouseCategory($warehouseID);

        $invCodeDet = $this->Pos_restaurant_model->getInvoiceCode();
        $data['title'] = 'POS';
        $data['extra'] = 'sidebar-collapse fixed';
        $data['refNo'] = $invCodeDet['refCode'];
        $data['isHadSession'] = $isHadSession;
        $data['menuItems'] = $output;
        $data['menuCategory'] = $output2;
        $wareHouseData = $this->Pos_restaurant_model->get_wareHouse();
        $data['posData'] = array(
            'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
            'counterDet' => $counterDet,
        );
        $data['common_data'] = $this->common_data;
        $defaultCustomerType = defaultCustomerType();

        $data['defaultCustomerType'] = !empty($defaultCustomerType) ? $defaultCustomerType : null;
        $data['warehouseID'] = $warehouseID;
        $data['tables_list'] = $this->Pos_restaurant_model->get_tableList();


        $this->load->view('system/pos/pos_restaurant', $data);

    }

    public function pos_terminal_1()
    {

        $tmpWarehouseID = $this->Pos_restaurant_model->get_srp_erp_warehouse_users_WarehouseID();
        $warehouseID = isset($tmpWarehouseID) && !empty($tmpWarehouseID) ? $tmpWarehouseID : 0;

        $isHaveNotClosedSession = $this->Pos_restaurant_model->isHaveNotClosedSession();
        $isHadSession = (empty($isHaveNotClosedSession)) ? 0 : $isHaveNotClosedSession;
        if ($isHadSession != 0) {
            $counterDet = $this->Pos_restaurant_model->get_counterData($isHaveNotClosedSession['counterID']);
            $counterDet = $counterDet['counterCode'];
        } else {
            $counterDet = '';
        }

        /** Get Warehouse Menu Items */
        $output = $this->Pos_restaurant_model->get_warehouseMenues($warehouseID);

        /** Get warehouse Category */
        $output2 = $this->Pos_restaurant_model->get_warehouseCategory($warehouseID);

        /** Get warehouse Sub Category */
        $output3 = $this->Pos_restaurant_model->get_warehouseSubCategory($warehouseID);

        $invCodeDet = $this->Pos_restaurant_model->getInvoiceCode();


        $data['title'] = 'POS';
        $data['extra'] = 'sidebar-collapse fixed';
        $data['refNo'] = $invCodeDet['refCode'];
        $data['isHadSession'] = $isHadSession;
        $data['menuItems'] = $output;
        $data['menuCategory'] = $output2;
        $data['menuSubCategory'] = $output3;
        $wareHouseData = $this->Pos_restaurant_model->get_wareHouse();
        $data['posData'] = array(
            'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
            'counterDet' => $counterDet,
        );
        $data['common_data'] = $this->common_data;
        $defaultCustomerType = defaultCustomerType();

        $data['defaultCustomerType'] = !empty($defaultCustomerType) ? $defaultCustomerType : null;
        $data['warehouseID'] = $warehouseID;
        $isPriceRequired = $this->pos_policy->isPriceRequired();
        $data['isPriceRequired'] = $isPriceRequired;
        $data['tables_list'] = $this->Pos_restaurant_model->get_tableList();
        $data['sampleBillPolicy'] = $this->pos_policy->isSampleBillRequired();
        $data['isHidePrintPreview'] = $this->pos_policy->isHidePrintPreview();


        /** load template */
        $templateLink = get_pos_templateView();
        /*echo $templateLink;
        exit;*/
        //$this->load->view('system/pos/pos_restaurant-view1', $data);
        //$this->load->view('system/pos/pos_restaurant-view3', $data); /* Template with item level discount */
        $this->load->view($templateLink, $data);
        //$this->load->view('system/pos/pos_restaurant', $data);
    }

    public function pos_terminal_mobile()
    {

        $tmpWarehouseID = $this->Pos_restaurant_model->get_srp_erp_warehouse_users_WarehouseID();
        $warehouseID = isset($tmpWarehouseID) && !empty($tmpWarehouseID) ? $tmpWarehouseID : 0;

        $isHaveNotClosedSession = $this->Pos_restaurant_model->isHaveNotClosedSession();
        $isHadSession = (empty($isHaveNotClosedSession)) ? 0 : $isHaveNotClosedSession;
        if ($isHadSession != 0) {
            $counterDet = $this->Pos_restaurant_model->get_counterData($isHaveNotClosedSession['counterID']);
            $counterDet = $counterDet['counterCode'];
        } else {
            $counterDet = '';
        }

        /** Get Warehouse Menu Items */
        $output = $this->Pos_restaurant_model->get_warehouseMenues($warehouseID);

        /** Get warehouse Category */
        $output2 = $this->Pos_restaurant_model->get_warehouseCategory($warehouseID);

        /** Get warehouse Sub Category */
        $output3 = $this->Pos_restaurant_model->get_warehouseSubCategory($warehouseID);

        $invCodeDet = $this->Pos_restaurant_model->getInvoiceCode();
        $data['title'] = 'POS';
        $data['extra'] = 'sidebar-collapse fixed';
        $data['refNo'] = $invCodeDet['refCode'];
        $data['isHadSession'] = $isHadSession;
        $data['menuItems'] = $output;
        $data['menuCategory'] = $output2;
        $data['menuSubCategory'] = $output3;
        $wareHouseData = $this->Pos_restaurant_model->get_wareHouse();
        $data['posData'] = array(
            'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
            'counterDet' => $counterDet,
        );
        $data['common_data'] = $this->common_data;
        $defaultCustomerType = defaultCustomerType();

        $data['defaultCustomerType'] = !empty($defaultCustomerType) ? $defaultCustomerType : null;
        $data['warehouseID'] = $warehouseID;
        $isPriceRequired = $this->pos_policy->isPriceRequired();
        $data['isPriceRequired'] = $isPriceRequired;
        $data['tables_list'] = $this->Pos_restaurant_model->get_tableList();
        $data['sampleBillPolicy'] = $this->pos_policy->isSampleBillRequired();


        $this->load->view('system/pos/pos-restaurant-mobile', $data);
    }


    public function pos_terminal_2()
    {
        $tmpWarehouseID = $this->Pos_restaurant_model->get_srp_erp_warehouse_users_WarehouseID();

        $warehouseID = isset($tmpWarehouseID) && !empty($tmpWarehouseID) ? $tmpWarehouseID : 0;

        $isHaveNotClosedSession = $this->Pos_restaurant_model->isHaveNotClosedSession();
        $isHadSession = (empty($isHaveNotClosedSession)) ? 0 : $isHaveNotClosedSession;
        if ($isHadSession != 0) {
            $counterDet = $this->Pos_restaurant_model->get_counterData($isHaveNotClosedSession['counterID']);
            $counterDet = $counterDet['counterCode'];
        } else {
            $counterDet = '';
        }

        /** Get Warehouse Menu Items */
        $output = $this->Pos_restaurant_model->get_warehouseMenues($warehouseID);

        /** Get warehouse Category */
        $output2 = $this->Pos_restaurant_model->get_warehouseCategory($warehouseID);

        $invCodeDet = $this->Pos_restaurant_model->getInvoiceCode();
        $data['title'] = 'POS';
        $data['extra'] = 'sidebar-collapse fixed';
        $data['refNo'] = $invCodeDet['refCode'];
        $data['isHadSession'] = $isHadSession;
        $data['menuItems'] = $output;
        $data['menuCategory'] = $output2;
        $wareHouseData = $this->Pos_restaurant_model->get_wareHouse();
        $data['posData'] = array(
            'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
            'counterDet' => $counterDet,
        );
        $data['common_data'] = $this->common_data;
        $defaultCustomerType = defaultCustomerType();

        $data['defaultCustomerType'] = !empty($defaultCustomerType) ? $defaultCustomerType : null;
        $data['warehouseID'] = $warehouseID;
        $isPriceRequired = $this->pos_policy->isPriceRequired();
        $data['isPriceRequired'] = $isPriceRequired;

        $this->load->view('system/pos/pos_restaurant-view2', $data);
        //$this->load->view('system/pos/pos_restaurant', $data);
    }

    public function load_currencyDenominationPage()
    {
        $currencyCode = $this->common_data['company_data']['company_default_currency'];
        //$wareHouseID = $this->common_data['ware_houseID'];
        $wareHouseID = get_outletID();
        $data['session_data'] = $this->Pos_restaurant_model->isHaveNotClosedSession();
        $data['denomination'] = $this->Pos_restaurant_model->currencyDenominations($currencyCode);
        $data['counters'] = $this->Pos_restaurant_model->load_wareHouseCounters($wareHouseID);
        $data['cardCollection'] = $this->Pos_restaurant_model->get_giftCardTopUpCashCollection();
        $data['dPlace'] = $this->common_data['company_data']['company_default_decimal'];
        $data['isRestaurant'] = true;
        $data['code'] = 1;

        /*echo 'wareHouseID: '.$wareHouseID;
        print_r($data['counters']);
        exit;*/

        $this->load->view('system/pos/ajax/currency_denomination_view', $data);
    }

    public function load_currencyDenominationPage_mobile()
    {
        $currencyCode = $this->common_data['company_data']['company_default_currency'];
        //$wareHouseID = $this->common_data['ware_houseID'];
        $wareHouseID = get_outletID();
        $data['session_data'] = $this->Pos_restaurant_model->isHaveNotClosedSession();
        $data['denomination'] = $this->Pos_restaurant_model->currencyDenominations($currencyCode);
        $data['counters'] = $this->Pos_restaurant_model->load_wareHouseCounters($wareHouseID);
        $data['cardCollection'] = $this->Pos_restaurant_model->get_giftCardTopUpCashCollection();
        $data['dPlace'] = $this->common_data['company_data']['company_default_decimal'];
        $data['isRestaurant'] = true;
        $data['mobile'] = true;
        $data['code'] = 1;
        $data['isRestaurant_mobile'] = true;


        /*echo 'wareHouseID: '.$wareHouseID;
        print_r($data['counters']);
        exit;*/

        $this->load->view('system/pos/ajax/currency_denomination_view', $data);
    }

    public function shift_create()
    {
        $this->form_validation->set_rules('startingBalance', 'Starting Balance', 'trim|required');
        $this->form_validation->set_rules('counterID', 'Counter ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->shift_create());
        }

    }

    public function shift_close()
        /** Didn't use it here */
    {
        /*$this->form_validation->set_rules('startingBalance', 'Ending Balance', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->shift_close());
        }*/

        $code = $this->input->post('code');
        $this->form_validation->set_rules('startingBalance', 'Ending Balance', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $counterData = get_counterData();
            $result = $this->Pos_model->shift_close();
            if ($result) {
                $tmpResult = array('s', 'Shift Closed Successfully', 'code' => $code, 'counterData' => $counterData); /*code is to identify where it come from.*/
            } else {
                $tmpResult = array('e', 'Error In Shift Close');
            }
            echo json_encode($tmpResult);
        }

    }


    public function item_search()
    {
        echo json_encode($this->Pos_restaurant_model->item_search());
    }

    public function invoice_create()
    {

        $this->form_validation->set_rules('itemID[]', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemUOM[]', 'Item UOM', 'trim|required');
        $this->form_validation->set_rules('itemQty[]', 'Item QTY', 'trim|required');
        $this->form_validation->set_rules('itemPrice[]', 'Item Price', 'trim|required');
        $this->form_validation->set_rules('customerID', 'Customer ID', 'trim|required');
        $this->form_validation->set_rules('_trCurrency', 'Transaction Currency', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $customerID = $this->input->post('customerID');
            $cashAmount = $this->input->post('_cashAmount');
            $chequeAmount = $this->input->post('_chequeAmount');
            $cardAmount = $this->input->post('_cardAmount');
            $paidAmount = ($cashAmount + $chequeAmount + $cardAmount);
            $netTotVal = $this->input->post('netTotVal');
            $balanceAmount = ($netTotVal - $paidAmount);

            if ($balanceAmount > 0 && $customerID == 0) {
                echo json_encode(array('e', 'Credit not allowed for Cash Customer'));
            } else {
                echo json_encode($this->Pos_restaurant_model->invoice_create());
            }
        }

    }

    public function invoice_hold()
    {
        $this->form_validation->set_rules('itemID[]', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemUOM[]', 'Item UOM', 'trim|required');
        $this->form_validation->set_rules('itemQty[]', 'Item QTY', 'trim|required');
        $this->form_validation->set_rules('itemPrice[]', 'Item Price', 'trim|required');
        $this->form_validation->set_rules('customerID', 'Customer ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->invoice_hold());
        }
    }

    public function invoice_cardDetail()
    {
        $this->form_validation->set_rules('invID', 'Invoice ID', 'trim|required|numeric');
        $this->form_validation->set_rules('referenceNO', 'Reference NO', 'trim|required');
        $this->form_validation->set_rules('cardNumber', 'cardNumber', 'trim|numeric');
        $this->form_validation->set_rules('bank', 'bank', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->invoice_cardDetail());
        }

    }

    public function customer_search()
    {
        echo json_encode($this->Pos_restaurant_model->customer_search());
    }

    public function recall_invoice()
    {
        echo json_encode($this->Pos_restaurant_model->recall_invoice());
    }

    public function recall_hold_invoice()
    {
        echo json_encode($this->Pos_restaurant_model->recall_hold_invoice());
    }

    public function invoice_search()
    {
        echo json_encode($this->Pos_restaurant_model->invoice_search());
    }

    public function load_holdInv()
    {
        echo json_encode($this->Pos_restaurant_model->load_holdInv());
    }

    public function new_counter()
    {
        $this->form_validation->set_rules('wareHouseID', 'Ware house ID', 'trim|required');
        $this->form_validation->set_rules('counterCode', 'Counter Code', 'trim|required');
        $this->form_validation->set_rules('counterName', 'Counter Name', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->new_counter());
        }

    }

    public function fetch_counters()
    {

        $this->datatables->select('counterID, counterCode, counterName, wareHouseID, wareHouseCode, wareHouseLocation', false)
            ->from('srp_erp_pos_counters t1')
            ->join('srp_erp_warehousemaster t2', 't2.wareHouseAutoID=t1.wareHouseID')
            ->add_column('action', '$1', 'actionCounter_fn(counterID, counterCode, counterName, wareHouseID)')
            ->add_column('wareHouse', '$1  -  $2', 'wareHouseCode,wareHouseLocation')
            ->where('t1.isActive', 1)
            ->where('t1.companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();

    }

    public function delete_counterDetails()
    {
        $this->form_validation->set_rules('counterID', 'Counter ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->delete_counterDetails());
        }
    }

    public function update_counterDetails()
    {
        $this->form_validation->set_rules('wareHouseID', 'Ware house ID', 'trim|required');
        $this->form_validation->set_rules('counterCode', 'Counter Code', 'trim|required');
        $this->form_validation->set_rules('counterName', 'Counter Name', 'trim|required');
        $this->form_validation->set_rules('updateID', 'Counter ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->update_counterDetails());
        }
    }

    public function load_counters()
    {
        $wareHouse = $this->input->post('wareHouseID');
        $thisWareHouseCounters = $this->Pos_restaurant_model->load_wareHouseCounters($wareHouse);
        $thisWareHouseUsers = $this->Pos_restaurant_model->load_wareHouseUsers($wareHouse);

        echo json_encode(
            array('counter' => $thisWareHouseCounters, 'users' => $thisWareHouseUsers)
        );
    }

    public function fetch_user_counters()
    {
        $this->datatables->select("t3.counterID, counterCode, counterName, t1.wareHouseID, wareHouseCode, wareHouseLocation,
             (SELECT CONCAT(IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '')) FROM srp_employeesdetails WHERE EIdNo=t1.userID) eName", false)
            ->from('srp_erp_warehouse_users t1')
            ->join('srp_erp_warehousemaster t2', 't1.wareHouseID=t2.wareHouseAutoID')
            ->join('srp_erp_pos_counters t3', 't1.counterID=t3.counterID')
            ->add_column('action', '$1', 'actionCounter_fn(counterID, counterCode, counterName, wareHouseID)')
            ->add_column('wareHouse', '$1  -  $2', 'wareHouseCode,wareHouseLocation')
            ->where('t3.isActive', 1)
            ->where('t1.companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();
    }

    public function fetch_ware_house_user()
    {

        $this->datatables->select("CONCAT(IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, ''))
              AS empName, userID, ECode, autoID, t1.wareHouseID, wareHouseCode, wareHouseLocation", false)
            ->from('srp_erp_warehouse_users t1')
            ->join('srp_employeesdetails t2', 't1.userID=t2.EIdNo')
            ->join('srp_erp_warehousemaster t3', 't1.wareHouseID=t3.wareHouseAutoID')
            ->add_column('wareHouse', '$1  -  $2', 'wareHouseCode,wareHouseLocation')
            ->add_column('action', '$1', 'actionWarehouseUser_fn(autoID, userID, empName, wareHouseID, wareHouseLocation)')
            ->where('t1.companyID', $this->common_data['company_data']['company_id'])
            ->where('t1.isActive', 1);
        echo $this->datatables->generate();

    }

    public function emp_search()
    {
        echo json_encode($this->Pos_restaurant_model->emp_search());
    }

    public function add_ware_house_user()
    {
        $this->form_validation->set_rules('wareHouseID', 'Ware house ID', 'trim|required');
        $this->form_validation->set_rules('employeeID', 'Employee ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->add_ware_house_user());
        }
    }

    public function update_ware_house_user()
    {
        $this->form_validation->set_rules('updateID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('wareHouseID', 'Ware house ID', 'trim|required');
        $this->form_validation->set_rules('employeeID', 'Employee ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->update_ware_house_user());
        }
    }

    public function delete_ware_house_user()
    {
        $this->form_validation->set_rules('autoID', 'Master ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->delete_ware_house_user());
        }
    }

    /*Promotion setups*/
    public function fetch_promotions()
    {
        $this->datatables->select('proMaster.promotionID, proType.Description AS typeDes, proMaster.Description AS masterDes,
               dateFrom, dateTo, isApplicableForAllItem, isActive,', false)
            ->from('srp_erp_pos_promotionsetupmaster proMaster')
            ->join('srp_erp_pos_promotiontypes proType', 'proMaster.promotionTypeID=proType.promotionTypeID')
            ->add_column('wareHouse', '$1', 'promoWarehouses(promotionID)')
            ->add_column('applicableItems', '$1', 'applicableItems(isApplicableForAllItem)')
            ->add_column('action', '$1', 'actionPromotion_fn(promotionID, masterDes)')
            //->where('proMaster.isActive', 1)
            ->where('proMaster.companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();
    }

    public function new_promotion()
    {
        $this->form_validation->set_rules('promoType', 'Promotion Type', 'trim|required');
        $this->form_validation->set_rules('warehouses[]', 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('promotionDescr', 'Promotion Description', 'trim|required');
        $this->form_validation->set_rules('fromDate', 'From Date', 'trim|required|date');
        $this->form_validation->set_rules('endDate', 'End Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $fromDate = $this->input->post('fromDate');
            $endDate = $this->input->post('endDate');

            if ($fromDate <= $endDate) {
                echo json_encode($this->Pos_restaurant_model->new_promotion());
            } else {
                echo json_encode(array('e', 'End date should be greater than or equal to from date'));
            }
        }
    }

    public function get_promotionMasterDet()
    {
        $promo_ID = $this->input->post('promo_ID');
        echo json_encode($this->Pos_restaurant_model->get_promotionMasterDet($promo_ID));
    }

    public function load_promotion_template()
    {
        /*
         *
         * 1 => On Sale Disc
         * 2 => On Sale Coupon
         * 3 => Item Free Issue
         *
         * */

        $promoID = $this->input->post('promo_ID');
        $promoType = $this->input->post('promoType');

        switch ($promoType) {
            case  1:
                $template = 'on_sale_discount_template';
                break;

            case  2:
                $template = 'on_sale_coupon_template';
                break;

            case  3:
                $template = 'item_free_issue_template';
                break;
        }

        $data['detail'] = $this->Pos_restaurant_model->get_promotionDet($promoID);
        $this->load->view('system/pos/ajax/' . $template, $data);

    }

    public function update_promotion()
    {
        $this->form_validation->set_rules('updateID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('promoType', 'Promotion Type', 'trim|required');
        $this->form_validation->set_rules('warehouses[]', 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('promotionDescr', 'Promotion Description', 'trim|required');
        $this->form_validation->set_rules('fromDate', 'From Date', 'trim|required|date');
        $this->form_validation->set_rules('endDate', 'End Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $fromDate = $this->input->post('fromDate');
            $endDate = $this->input->post('endDate');

            if ($fromDate <= $endDate) {
                echo json_encode($this->Pos_restaurant_model->update_promotion());
            } else {
                echo json_encode(array('e', 'End date should be greater than or equal to from date'));
            }
        }
    }

    public function delete_promotion()
    {
        $this->form_validation->set_rules('promoID', 'Promotion ID', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->delete_promotion());
        }
    }

    public function load_applicableItems()
    {
        $promo_ID = $this->input->post('promo_ID');
        $data['w_items'] = $this->Pos_restaurant_model->load_applicableItems($promo_ID);

        $this->load->view('system/pos/ajax/promotion_items_load', $data);
    }

    public function fetch_allItems()
    {
        /*$wareHouseID = $this->common_data['ware_houseID'];

        $this->datatables->select('t1.itemAutoID, t1.itemSystemCode, t1.itemDescription')
            ->from('srp_erp_warehouseitems t1')
            ->join('srp_erp_itemmaster t2', 't1.itemAutoID = t2.itemAutoID')
            ->add_column('action', '$1' , 'item_tb_checkbox(itemAutoID, itemSystemCode, itemDescription)')
            ->where('t2.companyID', current_companyID())
            ->where('wareHouseAutoID', $wareHouseID);*/

        $this->datatables->select('itemAutoID, itemSystemCode, itemDescription')
            ->from('srp_erp_itemmaster')
            ->add_column('action', '$1', 'item_tb_checkbox(itemAutoID, itemSystemCode, itemDescription)')
            ->where('companyID', current_companyID());

        echo $this->datatables->generate();
    }

    function selectedItemCheck()
    {
        $selectedItems = $this->input->post('selectedItems[]');

        if (count($selectedItems) == 0) {
            $this->form_validation->set_message('selectedItemCheck', 'Please add at least one item');
            return false;
        } else {
            return true;
        }
    }

    public function save_promotionItems()
    {
        $this->form_validation->set_rules('promoID', 'Promotion ID', 'trim|required');
        $this->form_validation->set_rules('selectedItems[]', 'Items', 'callback_selectedItemCheck');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->save_promotionItems());
        }
    }

    /*End of Promotion setups*/

    function get_wareHouse()
    {
        $outletID = get_outletID();
        $this->db->select('wHouse.wareHouseAutoID, wareHouseCode, wareHouseDescription, wareHouseLocation, segmentID, segmentCode')
            ->from('srp_erp_warehousemaster wHouse')
            ->join('srp_erp_pos_segmentconfig conf', 'conf.wareHouseAutoID=wHouse.wareHouseAutoID', 'left')
            ->where('wHouse.wareHouseAutoID', $outletID);
        return $this->db->get()->row_array();
    }


    function LoadToInvoice()
    {
        $d = get_company_currency_decimal();
        $time = $this->input->post('currentTime');
        $curDateTmp = date('Y-m-d') . $time;
        $curDate = format_date_mysql_datetime($curDateTmp);

        $id = $this->input->post('id');
        $customerType = $this->input->post('customerType');
        if (!empty($id)) {
            $output = $this->Pos_restaurant_model->get_warehouseMenu_specific($id);

            $isPack = $output['isPack'];
            if (!empty($output)) {
                $code = 0;

                $output['warehouseMenuID'] = str_pad($output['warehouseMenuID'], 4, "0", STR_PAD_LEFT);
                $output['key'] = $output['warehouseMenuID'];

                $templateID = $this->input->post('pos_templateID');
                $sellingPrice = getSellingPricePolicy($templateID, $output['pricewithoutTax'], $output['totalTaxAmount'], $output['totalServiceCharge']);
                $output['sellingPrice'] = number_format($sellingPrice, $d, '.', '');

                $get_shift = $this->Pos_restaurant_model->get_srp_erp_pos_shiftdetails_employee();

                $invoiceID_tmp = isPos_invoiceSessionExist();

                if ($invoiceID_tmp) {
                    /** -------------------------------  INVOICE EXIST ------------------------------- */

                    /* Insert Menu */
                    $data_item['menuSalesID'] = $invoiceID_tmp;
                    $data_item['warehouseAutoID'] = get_outletID();
                    $data_item['menuID'] = $output['menuMasterID'];
                    $data_item['menuCategoryID'] = $output['menuCategoryID'];

                    $data_item['warehouseMenuID'] = $output['warehouseMenuID'];
                    $data_item['warehouseMenuCategoryID'] = $output['warehouseMenuCategoryID'];
                    $data_item['defaultUOM'] = 'each';
                    $data_item['unitOfMeasure'] = 'each';
                    $data_item['conversionRateUOM'] = 1;

                    $data_item['menuCost'] = $output['menuCost'];
                    $data_item['menuSalesPrice'] = $output['pricewithoutTax'];
                    $data_item['qty'] = 1;
                    $data_item['discountPer'] = 0;
                    $data_item['discountAmount'] = 0;

                    /** KOT Kitchen order ticket detail */
                    $parentMenuSalesItemID = $this->input->post('parentMenuSalesItemID');
                    $data_item['kotID'] = $parentMenuSalesItemID > 0 ? 0 : $this->input->post('kotID');
                    $data_item['kitchenNote'] = trim($this->input->post('kitchenNote'));
                    $data_item['isOrderPending'] = -1;

                    /** Add-on */
                    $data_item['parentMenuSalesItemID'] = $parentMenuSalesItemID;

                    /** get kitchen current status */
                    $isOrderPending = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster_specific($invoiceID_tmp, 'isOrderPending', true);
                    if ($isOrderPending == 1) {
                        /** already pressed the send KOT button */
                        /** new item will not be taken in the KOT until user click on the kot button*/
                        $data_item['KOTAlarm'] = -1;
                    }


                    /** Tax Calculation */
                    $data_item['TAXpercentage'] = $output['TAXpercentage'];
                    $data_item['TAXAmount'] = NULL;//$output['TAXpercentage'] > 0 ? $output['sellingPrice'] * ($output['TAXpercentage'] / 100) : null;
                    $data_item['taxMasterID'] = NULL;//$output['taxMasterID'];

                    $transCurrencyID = getCurrencyID_byCurrencyCode($get_shift['transactionCurrency']);
                    $data_item['transactionCurrencyID'] = $transCurrencyID;
                    $data_item['transactionCurrency'] = $get_shift['transactionCurrency'];
                    $data_item['transactionAmount'] = $output['sellingPrice'];
                    $data_item['transactionCurrencyDecimalPlaces'] = $get_shift['transactionCurrencyDecimalPlaces'];
                    $data_item['transactionExchangeRate'] = $get_shift['transactionExchangeRate'];

                    $reportingCurrencyID = $this->common_data['company_data']['company_reporting_currencyID'];
                    $conversion = currency_conversionID($transCurrencyID, $reportingCurrencyID, $output['sellingPrice']);

                    $data_item['companyReportingCurrency'] = $reportingCurrencyID;
                    $data_item['companyReportingAmount'] = $conversion['convertedAmount'];
                    $data_item['companyReportingCurrencyDecimalPlaces'] = $conversion['DecimalPlaces'];
                    $data_item['companyReportingExchangeRate'] = $conversion['conversion'];

                    $defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
                    $conversion = currency_conversionID($transCurrencyID, $defaultCurrencyID, $output['sellingPrice']);


                    $data_item['companyLocalCurrencyID'] = $defaultCurrencyID;
                    $data_item['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $data_item['companyLocalAmount'] = $conversion['convertedAmount'];
                    $data_item['companyLocalExchangeRate'] = $conversion['conversion'];
                    $data_item['companyLocalCurrencyDecimalPlaces'] = $conversion['DecimalPlaces'];


                    $data_item['companyID'] = current_companyID();
                    $data_item['companyCode'] = current_companyCode();

                    $data_item['revenueGLAutoID'] = $output['revenueGLAutoID'];

                    $data_item['createdUserGroup'] = user_group();
                    $data_item['createdPCID'] = current_pc();
                    $data_item['createdUserID'] = current_userID();
                    $data_item['createdDateTime'] = $curDate;
                    $data_item['createdUserName'] = current_user();
                    $data_item['modifiedPCID'] = null;
                    $data_item['modifiedUserID'] = null;
                    $data_item['modifiedDateTime'] = null;
                    $data_item['modifiedUserName'] = null;
                    $data_item['timestamp'] = format_date_mysql_datetime();
                    $data_item['id_store'] = current_warehouseID();
					$data_item['is_sync'] = 1;




                    /*Insert Menu */
                    $code = $this->Pos_restaurant_model->insert_srp_erp_pos_menusalesitems($data_item);

                    $this->updateNetTotalForInvoice($invoiceID_tmp); // Sync DONE



                    /** KOT order is still in progress */
                    $dataKOT['isOrderCompleted'] = 0;

                    $this->db->select('menuSalesID');
                    $this->db->from('srp_erp_pos_menusalesitems');
                    $this->db->where('isOrderInProgress', 1);
                    $this->db->where('menuSalesID', $invoiceID_tmp);
                    $result = $this->db->get()->row_array();

                    if ($result) {
                        $dataKOT['isOrderInProgress'] = 1;
                    } else {
                        $dataKOT['isOrderInProgress'] = 0;
                    }


                    $this->db->where('menuSalesID', $invoiceID_tmp);
                    $this->db->update('srp_erp_pos_menusalesmaster', $dataKOT);
                    /** enf of KOT */


                } else {
                    /** -------------------------------  NEW INVOICE  ------------------------------- */

                    if (!empty($get_shift)) {
                        $warehouseDetail = $this->get_wareHouse();


                        /** -------------------------------  Create New Invoice ------------------------------- */
                        $tmpCustomerType = $this->input->post('customerType');

                        if (!empty($tmpCustomerType)) {
                            $CustomerType = $tmpCustomerType;
                        } else {
                            /***** setup default order type *** */
                            if ($this->input->post('tabOrder') == 1) {
                                $CustomerType = get_defaultOderType();
                            } else {
                                $CustomerType = null;
                            }
                        }

                        $SN = generate_pos_invoice_no();
                        $data['customerTypeID'] = $CustomerType;
                        $data['documentSystemCode'] = '';
                        $data['documentCode'] = '';
                        $data['serialNo'] = $SN;
                        $data['invoiceSequenceNo'] = $SN;
                        $data['invoiceCode'] = generate_pos_invoice_code();
                        $data['customerID'] = '';
                        $data['customerCode'] = '';
                        $data['shiftID'] = $get_shift['shiftID'];

                        if ($this->input->post('tabOrder') == 1) {
                            $data['counterID'] = null;
                            $data['isHold'] = -1;
                            $data['tabUserID'] = current_userID();
                        } else {
                            $data['counterID'] = $get_shift['counterID'];
                        }

                        $data['menuSalesDate'] = format_date_mysql_datetime();
                        $data['holdDatetime'] = format_date_mysql_datetime();
                        $data['companyID'] = current_companyID();
                        $data['companyCode'] = current_companyCode();

                        $data['subTotal'] = '';
                        $data['discountPer'] = '';
                        $data['discountAmount'] = '';
                        $data['netTotal'] = '';

                        $data['wareHouseAutoID'] = $get_shift['wareHouseID'];

                        $data['segmentID'] = $warehouseDetail['segmentID'];
                        $data['segmentCode'] = $warehouseDetail['segmentCode'];

                        $data['salesDay'] = date('l');
                        $data['salesDayNum'] = date('w');


                        $tr_currency = $this->common_data['company_data']['company_default_currency'];
                        $transConversion = currency_conversion($tr_currency, $tr_currency);

                        $data['transactionCurrencyID'] = $transConversion['currencyID'];
                        $data['transactionCurrency'] = $transConversion['CurrencyCode'];
                        $data['transactionExchangeRate'] = $transConversion['conversion'];
                        $data['transactionCurrencyDecimalPlaces '] = $transConversion['DecimalPlaces'];

                        $defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
                        $defaultConversion = currency_conversionID($transConversion['currencyID'], $defaultCurrencyID);

                        $data['companyLocalCurrencyID'] = $defaultCurrencyID;
                        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                        $data['companyLocalExchangeRate'] = $defaultConversion['conversion'];
                        $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];


                        $repCurrencyID = $this->common_data['company_data']['company_reporting_currencyID'];
                        $transConversion = currency_conversionID($transConversion['currencyID'], $repCurrencyID);

                        $data['companyReportingCurrencyID'] = $repCurrencyID;
                        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                        $data['companyReportingExchangeRate'] = $transConversion['conversion'];
                        $data['companyReportingCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_reporting_decimal'];


                        /*update the transaction currency detail for later use */
                        $tr_currency = $this->common_data['company_data']['company_default_currency'];
                        $customerCurrencyConversion = currency_conversion($tr_currency, $tr_currency);

                        $data['customerCurrencyID'] = $customerCurrencyConversion['currencyID'];
                        $data['customerCurrency'] = $customerCurrencyConversion['CurrencyCode'];
                        $data['customerCurrencyExchangeRate'] = $customerCurrencyConversion['conversion'];
                        $data['customerCurrencyDecimalPlaces'] = $customerCurrencyConversion['DecimalPlaces'];


                        /*Audit Data */
                        $data['createdUserGroup'] = current_user_group();
                        $data['createdPCID'] = current_pc();
                        $data['createdUserID'] = current_userID();
                        $data['createdUserName'] = current_user();
                        $data['createdDateTime'] = format_date_mysql_datetime();
                        $data['modifiedPCID'] = '';
                        $data['modifiedUserID'] = '';
                        $data['modifiedUserName'] = '';
                        $data['modifiedDateTime'] = '';
                        $data['timestamp'] = format_date_mysql_datetime();
                        $data['id_store'] = $this->config->item('id_store');
                        $data['isFromTablet'] = $this->input->post('isFromTablet');
                        $data['is_sync'] = 1;
                        $data['paymentMethod'] = 1;


                        $invoiceID = $this->Pos_restaurant_model->insert_srp_erp_pos_menusalesmaster($data);
                        if ($invoiceID) {
                            set_session_invoiceID($invoiceID);

                            /* Insert Menu */
                            $data_item['menuSalesID'] = $invoiceID;
                            $data_item['warehouseAutoID'] = get_outletID();
                            $data_item['menuID'] = $output['menuMasterID'];
                            $data_item['menuCategoryID'] = $output['menuCategoryID'];
                            $data_item['warehouseMenuID'] = $output['warehouseMenuID'];
                            $data_item['warehouseMenuCategoryID'] = $output['warehouseMenuCategoryID'];
                            $data_item['defaultUOM'] = 'each';
                            $data_item['unitOfMeasure'] = 'each';
                            $data_item['conversionRateUOM'] = 1;

                            $data_item['menuCost'] = $output['menuCost'];
                            $data_item['menuSalesPrice'] = $output['pricewithoutTax'];
                            $data_item['qty'] = 1;
                            $data_item['discountPer'] = 0;
                            $data_item['discountAmount'] = 0;

                            /** KOT Kitchen order ticket detail */
                            $parentMenuSalesItemID = $this->input->post('parentMenuSalesItemID');
                            $data_item['kotID'] = $parentMenuSalesItemID > 0 ? 0 : $this->input->post('kotID');
                            $data_item['kitchenNote'] = trim($this->input->post('kitchenNote'));
                            $data_item['isOrderPending'] = -1;

                            /** Add-on */
                            $data_item['parentMenuSalesItemID'] = $parentMenuSalesItemID;


                            /** Tax Calculation */
                            $data_item['TAXpercentage'] = $output['TAXpercentage'];
                            $data_item['TAXAmount'] = NULL; //$output['TAXpercentage'] > 0 ? $output['sellingPrice'] * ($output['TAXpercentage'] / 100) : null;
                            $data_item['taxMasterID'] = NULL; // $output['taxMasterID'];


                            $transCurrencyID = getCurrencyID_byCurrencyCode($get_shift['transactionCurrency']);
                            $data_item['transactionCurrencyID'] = $transCurrencyID;
                            $data_item['transactionCurrency'] = $get_shift['transactionCurrency'];
                            $data_item['transactionAmount'] = $output['sellingPrice'];
                            $data_item['transactionCurrencyDecimalPlaces'] = $get_shift['transactionCurrencyDecimalPlaces'];
                            $data_item['transactionExchangeRate'] = $get_shift['transactionExchangeRate'];

                            $reportingCurrencyID = $this->common_data['company_data']['company_reporting_currencyID'];
                            $conversion = currency_conversionID($transCurrencyID, $reportingCurrencyID, $output['sellingPrice']);

                            $data_item['companyReportingCurrency'] = $reportingCurrencyID;
                            $data_item['companyReportingAmount'] = $conversion['convertedAmount'];
                            $data_item['companyReportingCurrencyDecimalPlaces'] = $conversion['DecimalPlaces'];
                            $data_item['companyReportingExchangeRate'] = $conversion['conversion'];

                            $defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
                            $conversion = currency_conversionID($transCurrencyID, $defaultCurrencyID, $output['sellingPrice']);

                            $data_item['companyLocalCurrencyID'] = $defaultCurrencyID;
                            $data_item['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                            $data_item['companyLocalAmount'] = $conversion['convertedAmount'];
                            $data_item['companyLocalExchangeRate'] = $conversion['conversion'];
                            $data_item['companyLocalCurrencyDecimalPlaces'] = $conversion['DecimalPlaces'];
                            $data_item['companyID'] = current_companyID();
                            $data_item['companyCode'] = current_companyCode();
                            $data_item['revenueGLAutoID'] = $output['revenueGLAutoID'];
                            $data_item['createdUserGroup'] = user_group();
                            $data_item['createdPCID'] = current_pc();
                            $data_item['createdUserID'] = current_userID();
                            $data_item['createdDateTime'] = format_date_mysql_datetime();
                            $data_item['createdUserName'] = current_user();
                            $data_item['modifiedPCID'] = null;
                            $data_item['modifiedUserID'] = null;
                            $data_item['modifiedDateTime'] = null;
                            $data_item['modifiedUserName'] = null;
                            $data_item['timestamp'] = format_date_mysql_datetime();
                            $data_item['id_store'] = current_warehouseID();
                            $data_item['is_sync'] = 1;


                            /*Insert Menu */
                            $code = $this->Pos_restaurant_model->insert_srp_erp_pos_menusalesitems($data_item);
                            $this->updateNetTotalForInvoice($invoiceID);

                        } else {
                            echo json_encode(array('error' => 1, 'message' => 'An error has occurred please contact your support team'));
                            exit;
                        }
                    } else {
                        echo json_encode(array('error' => 1, 'message' => 'shift not created'));
                        exit;
                    }
                }

                $tmpInvoiceID = isset($invoiceID) && !empty($invoiceID) ? padZeros_saleInvoiceID($invoiceID) : padZeros_saleInvoiceID(isPos_invoiceSessionExist());
                $tmpInvoiceID_code_tmp = isset($invoiceID) && !empty($invoiceID) ? $invoiceID : isPos_invoiceSessionExist();
                $outletID = get_outletID();
                $tmpInvoiceID_code = get_pos_invoice_code($tmpInvoiceID_code_tmp, $outletID);


                $result = array_merge(array('error' => 0, 'message' => 'done'), $output, array('tmpInvoiceID' => $tmpInvoiceID, 'tmpInvoiceID_code' => $tmpInvoiceID_code, 'code' => $code, 'isPack' => $isPack));
                echo json_encode($result);


            } else {
                echo json_encode(array('error' => 0, 'message' => 'Menu not found'));
            }
        } else {
            echo json_encode(array('error' => 0, 'message' => 'ID not found'));
        }
    }

    function SaveKitchenNote(){
        $current_menusalesitem_id = $this->input->post('current_menusales_id');
        $kitchen_note = $this->input->post('kitchen_note');

        $menusales_item = array("kitchenNote"=>$kitchen_note);
        $this->db->where("menuSalesItemID",$current_menusalesitem_id);
        $res = $this->db->update('srp_erp_pos_menusalesitems',$menusales_item);
        if($res==true){
            $data['status']="success";
        }else{
            $data['status']="failed";
        }
        echo json_encode($data);
    }

    function checkPosSession()
    {
        $sql = 'select count(menuSalesID) as syncCount from srp_erp_pos_menusalesmaster where is_sync = 0';
        $count = $this->db->query($sql)->row('syncCount');

        $result = isPos_invoiceSessionExist();
        if ($result) {
            $get_invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($result);
            if (!empty($get_invoice)) {

                $isDineIn = 0;
                if (trim(strtolower($get_invoice['customerDescription'])) == 'dine-in' || trim(strtolower($get_invoice['customerDescription'])) == 'eat-in') {
                    $isDineIn = 1;
                }

                $result = array_merge($get_invoice, array('error' => 0, 'message' => 'Invoice Exist', 'code' => $result, 'master' => $get_invoice, 'dine_in' => $isDineIn, 'sync_pending_bill_count' => $count));
                echo json_encode($result);
            } else {
                echo json_encode(array('error' => 1, 'message' => 'This invoice is already closed', 'code' => 0, 'sync_pending_bill_count' => $count));
            }
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Invoice not exist!', 'code' => 0, 'sync_pending_bill_count' => $count));
        }
    }

    function Load_pos_holdInvoiceData()
    {
        $d = get_company_currency_decimal();
        $template = $this->input->post('template');
        $invoiceID = $this->input->post('invoiceID');
        $getMenuInvoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID);
        $output = '';
        if (!empty($getMenuInvoice)) {


            foreach ($getMenuInvoice as $data) {
                $data['warehouseMenuID'] = str_pad($data['warehouseMenuID'], 4, "0", STR_PAD_LEFT);

                $output .= '<div class="row itemList" id="item_row_' . $data['menuSalesItemID'] . '" style="margin: 0px; border-bottom: 1px solid #dddddd; padding-top: 5px; padding-bottom: 5px;" onclick="selectMenuItem(this)">';

                if ($template == 2) {
                    $hide = ' hide ';
                    $col = '4';
                } else {
                    $hide = '';
                    $col = 3;
                }

                $output .= '<div class="col-md-1 hidden-xs hidden-sm menuItem_pos_col_1 ' . $hide . '"><img src="' . $data['menuImage'] . '" style="max-height: 40px;" alt=""></div>';

                if ($template == 2) {
                    $output .= '<div class="col-md-' . $col . ' menuItem_pos_col_5">' . $data['menuMasterDescription'] . '</div> ';
                } else {
                    $output .= '<div class="col-md-' . $col . ' menuItem_pos_col_5">' . $data['menuMasterDescription'] . ' <br>[' . $data['warehouseMenuID'] . ']</div> ';
                }

                $templateID = get_pos_templateID();
                $sellingPrice = getSellingPricePolicy($templateID, $data['pricewithoutTax'], $data['totalTaxAmount'], $data['totalServiceCharge']);
                $data['sellingPrice'] = number_format($sellingPrice, $d, '.', '');


                $output .= '<div class="col-md-8">
                            <div class="receiptPadding">
                                <input type="text" onkeyup="calculateFooter()" onchange="updateQty(' . $data['menuSalesItemID'] . ')" value="' . $data['qty'] . '" class="display_qty menuItem_input numberFloat" id="qty_' . $data['menuSalesItemID'] . '" name="qty[' . $data['menuSalesItemID'] . ']"  />
                            </div>
                            <div class="receiptPadding">
                                <span class="menu_itemCost menuItemTxt">' . $data['sellingPrice'] . '</span> <!-- @rate -->
                                <input type="hidden"  class="menuItemTxt_input numberFloat" name="sellingPrice[' . $data['menuSalesItemID'] . ']" value="' . $data['sellingPrice'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input numberFloat pricewithoutTax" name="pricewithoutTax[' . $data['menuSalesItemID'] . ']" value="' . $data['pricewithoutTax'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input totalMenuTaxAmount numberFloat" name="totalMenuTaxAmount[' . $data['menuSalesItemID'] . ']" value="' . $data['totalTaxAmount'] . '"/>
<input type="hidden"  class="menuItemTxt_input numberFloat totalMenuServiceCharge" name="totalMenuServiceCharge[' . $data['menuSalesItemID'] . ']" value="' . $data['totalServiceCharge'] . '"/>
                            <input type="hidden"  name="frm_isTaxEnabled[' . $data['menuSalesItemID'] . ']" value="' . $data['isTaxEnabled'] . '"/>
                            </div>
                            <div class="receiptPadding">
                                <span class="menu_total menuItemTxt">0</span>  <!-- total -->
                            </div>

                            <div class="receiptPadding ' . $hide . '">
                                <input style="width:60%;" onchange="calculateFooter(\'P\')" name="discountPercentage[' . $data['menuSalesItemID'] . ']"  maxlength="3" type="text" value="' . $data['discountPer'] . '"
                                       class="menu_discount_percentage menu_qty menuItem_input numberFloat"> <!-- disc. % -->
                            </div>
                            <div class="receiptPadding ' . $hide . '">
                                <input style="width:90%;" onchange="calculateFooter(\'A\')" name="discountAmount[' . $data['menuSalesItemID'] . ']" type="text" value="' . $data['discountAmount'] . '"
                                       class="menu_discount_amount menu_qty menuItem_input numberFloat"><!-- disc. amount -->
                            </div>
                            <div class="receiptPadding">
                                <div style="width:55px; text-align: right;" class="itemCostNet menuItemTxt"> ' . $data['sellingPrice'] . '</div> <!-- net total -->
                                <div onclick="deleteDiv(' . $data['menuSalesItemID'] . ')" data-placement="bottom" rel="tooltip" title="Delete"
                                     style="cursor:pointer; width: 12px; margin-top: -20px;     margin-right: 0px;" class="pull-right">';
                if ($template == 2) {
                    $output .= '<button type="button" class="btn btn-default btn-sm itemList-delBtn"><i class="fa fa-close closeColor"></i></button> </div>';
                } else {
                    $output .= '<i class="fa fa-close closeColor"></i></button>';
                }

                $output .= '</div>
                        </div>';
                $output .= '</div>';


            }
        }
        echo $output;
    }

    function Load_pos_holdInvoiceData_tab()
    {

        $d = get_company_currency_decimal();
        $template = $this->input->post('template');
        $invoiceID = $this->input->post('invoiceID');
        $getMenuInvoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID);
        $output = '';
        if (!empty($getMenuInvoice)) {

            foreach ($getMenuInvoice as $data) {
                $data['warehouseMenuID'] = str_pad($data['warehouseMenuID'], 4, "0", STR_PAD_LEFT);

                $output .= '<div class="row itemList" id="item_row_' . $data['menuSalesItemID'] . '" style="margin: 0px; border-bottom: 1px solid #dddddd; padding-top: 5px; padding-bottom: 5px;" onclick="selectMenuItem(this)">';

                if ($template == 2) {
                    $hide = ' ';
                    $col = '4';
                } else {
                    $hide = '';
                    $col = 4;
                }

                $output .= '<div class=" hide"><img src="' . $data['menuImage'] . '" style="max-height: 40px;" alt=""></div>';

                if ($template == 2) {
                    $output .= '<div class="col-xs-4 col-sm-4 col-md-' . $col . ' menuItem_pos_col_5">' . $data['menuMasterDescription'] . '</div> ';
                } else {
                    $output .= '<div class="col-xs-4 col-sm-4 col-md-' . $col . ' menuItem_pos_col_5">' . $data['menuMasterDescription'] . ' <br>[' . $data['warehouseMenuID'] . ']</div> ';
                }

                $templateID = get_pos_templateID();
                $sellingPrice = getSellingPricePolicy($templateID, $data['pricewithoutTax'], $data['totalTaxAmount'], $data['totalServiceCharge']);
                $data['sellingPrice'] = number_format($sellingPrice, $d, '.', '');


                $output .= '<div class="col-md-8 col-sm-8 col-xs-8">
                            <div class="receiptPadding">
                                <input type="text" onkeyup="calculateFooter()" onchange="updateQty(' . $data['menuSalesItemID'] . ')" value="' . $data['qty'] . '" class="display_qty menuItem_input numberFloat" id="qty_' . $data['menuSalesItemID'] . '" name="qty[' . $data['menuSalesItemID'] . ']"  />
                            </div>
                            <div class="receiptPadding">
                                <span class="menu_itemCost menuItemTxt">' . $data['sellingPrice'] . '</span> <!-- @rate -->
                                <input type="hidden"  class="menuItemTxt_input numberFloat" name="sellingPrice[' . $data['menuSalesItemID'] . ']" value="' . $data['sellingPrice'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input numberFloat pricewithoutTax" name="pricewithoutTax[' . $data['menuSalesItemID'] . ']" value="' . $data['pricewithoutTax'] . '"/>
                                <input type="hidden"  class="menuItemTxt_inputDiscount numberFloat pricewithoutTaxDiscount" name="pricewithoutTaxDiscount[' . $data['menuSalesItemID'] . ']" value="' . $data['pricewithoutTax'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input totalMenuTaxAmount numberFloat" name="totalMenuTaxAmount[' . $data['menuSalesItemID'] . ']" value="' . $data['totalTaxAmount'] . '"/>
                                <input type="hidden"  class="menuItemTxt_inputDiscount totalMenuTaxAmountDiscount numberFloat" name="totalMenuTaxAmountDiscount[' . $data['menuSalesItemID'] . ']" value="' . $data['totalTaxAmount'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input numberFloat totalMenuServiceCharge" name="totalMenuServiceCharge[' . $data['menuSalesItemID'] . ']" value="' . $data['totalServiceCharge'] . '"/>
                                <input type="hidden"  class="menuItemTxt_inputDiscount numberFloat totalMenuServiceChargeDiscount" name="totalMenuServiceChargeDiscount[' . $data['menuSalesItemID'] . ']" value="' . $data['totalServiceCharge'] . '"/>
                                <input type="hidden"  name="frm_isTaxEnabled[' . $data['menuSalesItemID'] . ']" value="' . $data['isTaxEnabled'] . '"/>
                                <input type="hidden"  class="isSamplePrintedFlag" id="isSamplePrinted_' . $data['menuSalesItemID'] . '" value="' . $data['isSamplePrinted'] . '"/>
                            </div>
                            <div class="receiptPadding hide">
                                <span class="menu_total menuItemTxt">0</span>  <!-- total -->
                            </div>

                            <div class="receiptPadding hide">
                                <input style="width:60%;" onchange="calculateFooter(\'P\')" name="discountPercentage[' . $data['menuSalesItemID'] . ']"  maxlength="3" type="text" value="' . $data['discountPer'] . '"
                                       class="menu_discount_percentage menu_qty menuItem_input numberFloat"> <!-- disc. % -->
                            </div>
                            <div class="receiptPadding hide">
                                <input style="width:90%;" onchange="calculateFooter(\'A\')" name="discountAmount[' . $data['menuSalesItemID'] . ']" type="text" value="' . $data['discountAmount'] . '"
                                       class="menu_discount_amount menu_qty menuItem_input numberFloat"><!-- disc. amount -->
                            </div>
                            <div class="receiptPadding">
                                <div style="width:55px; text-align: right;" class="itemCostNet menuItemTxt"> ' . $data['sellingPrice'] . '</div> <!-- net total -->
                                <div onclick="deleteDiv(' . $data['menuSalesItemID'] . ')" data-placement="bottom" rel="tooltip" title="Delete"
                                     style="cursor:pointer; width: 12px; margin-top: -20px;     margin-right: 0px;" class="pull-right hide">';
                if ($template == 2) {
                    $output .= '<button type="button" class="btn btn-default btn-sm itemList-delBtn "><i class="fa fa-close closeColor"></i></button> </div>';
                } else {
                    $output .= '<i class="fa fa-close closeColor"></i></button>';
                }

                $output .= '</div>
                        </div>';
                $output .= '</div>';


            }
        }
        echo $output;
    }

    function Load_pos_holdInvoiceData_withDiscount()
    {

        $d = get_company_currency_decimal();
        $outletID = $this->input->post('outletID');
        $template = $this->input->post('template');
        $invoiceID = $this->input->post('invoiceID');
        $getMenuInvoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID_forHoldBill($invoiceID, $outletID);
        $output = '';
        if (!empty($getMenuInvoice)) {


            foreach ($getMenuInvoice as $data) {

                $data['warehouseMenuID'] = str_pad($data['warehouseMenuID'], 4, "0", STR_PAD_LEFT);

                $output .= '<div class="row itemList" id="item_row_' . $data['menuSalesItemID'] . '" style="margin: 0px; border-bottom: 1px solid #dddddd; padding-top: 5px; padding-bottom: 5px;" onclick="selectMenuItem(this)">';

                if ($template == 2) {
                    $hide = '  ';
                    $col = '3';
                } else {
                    $hide = '';
                    $col = 3;
                }

                $warehouseMenuForKitchenNote=get_warehouseMenuForKitchenNote($data['warehouseMenuID']);
                //var_dump($warehouseMenuForKitchenNote);exit;
                $output .= '<div class="col-md-1 hidden-xs hidden-sm menuItem_pos_col_1 hide"><img src="' . $data['menuImage'] . '" style="max-height: 40px;" alt=""></div>';

                if((int)$warehouseMenuForKitchenNote['kotID']>0){
                    $output.='<div class="col-md-1" style="padding-left: 0px;"><button type="button" value="KN" type="button" class="btn btn-primary" onclick="open_kitchen_note('.$warehouseMenuForKitchenNote['warehouseMenuID'].','.$warehouseMenuForKitchenNote['kotID'].','.$data['menuSalesItemID'].')"><i class="fa fa-file-text"></i></button></div>';
                }else {
                    $output.= '<div class="col-md-1" style="padding-left: 0px;"></div>';
                }

                if ($template == 2) {
                    $output .= '<div class="col-md-' . $col . ' menuItem_pos_col_5">' . $data['menuMasterDescription'] . '</div> ';
                } else {
                    $output .= '<div class="col-md-' . $col . ' menuItem_pos_col_5">' . $data['menuMasterDescription'] . ' <br>[' . $data['warehouseMenuID'] . ']</div> ';
                }

                $templateID = get_pos_templateID();
                $sellingPrice = getSellingPricePolicy($templateID, $data['pricewithoutTax'], $data['totalTaxAmount'], $data['totalServiceCharge']);
                $data['sellingPrice'] = number_format($sellingPrice, $d, '.', '');
                $discountPolicy = show_item_level_discount();
                $discountPolicyClass = $discountPolicy ? '' : 'hide';

                $output .= '<div class="col-md-8">
                            <div class="receiptPadding">
                                <input type="text" onkeyup="calculateFooter()" onchange="updateQty(' . $data['menuSalesItemID'] . ')" value="' . $data['qty'] . '" class="display_qty menuItem_input numberFloat" id="qty_' . $data['menuSalesItemID'] . '" name="qty[' . $data['menuSalesItemID'] . ']"  />
                            </div>
                            <div class="receiptPadding">
                                <span class="menu_itemCost menuItemTxt">' . $data['sellingPrice'] . '</span> <!-- @rate -->
                                <input type="hidden"  class="menuItemTxt_input numberFloat" name="sellingPrice[' . $data['menuSalesItemID'] . ']" value="' . $data['sellingPrice'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input numberFloat pricewithoutTax" name="pricewithoutTax[' . $data['menuSalesItemID'] . ']" value="' . $data['pricewithoutTax'] . '"/>
                                <input type="hidden"  class="menuItemTxt_inputDiscount numberFloat pricewithoutTaxDiscount" name="pricewithoutTaxDiscount[' . $data['menuSalesItemID'] . ']" value="' . $data['pricewithoutTax'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input totalMenuTaxAmount numberFloat" name="totalMenuTaxAmount[' . $data['menuSalesItemID'] . ']" value="' . $data['totalTaxAmount'] . '"/>
                                <input type="hidden"  class="menuItemTxt_inputDiscount totalMenuTaxAmountDiscount numberFloat" name="totalMenuTaxAmountDiscount[' . $data['menuSalesItemID'] . ']" value="' . $data['totalTaxAmount'] . '"/>
<input type="hidden"  class="menuItemTxt_input numberFloat totalMenuServiceCharge" name="totalMenuServiceCharge[' . $data['menuSalesItemID'] . ']" value="' . $data['totalServiceCharge'] . '"/>
<input type="hidden"  class="menuItemTxt_inputDiscount numberFloat totalMenuServiceChargeDiscount" name="totalMenuServiceChargeDiscount[' . $data['menuSalesItemID'] . ']" value="' . $data['totalServiceCharge'] . '"/>
                            <input type="hidden"  name="frm_isTaxEnabled[' . $data['menuSalesItemID'] . ']" value="' . $data['isTaxEnabled'] . '"/>
                            <input type="hidden"  class="isSamplePrintedFlag" id="isSamplePrinted_' . $data['menuSalesItemID'] . '" value="' . $data['isSamplePrinted'] . '"/>
                            </div>
                            <div class="receiptPadding">
                                <span class="menu_total menuItemTxt">0</span>  <!-- total -->
                            </div>

                            <div class="receiptPadding ' . $discountPolicyClass . '">
                                <input style="width:60%;" onchange="item_wise_discount(this,\'P\')" name="discountPercentage[' . $data['menuSalesItemID'] . ']"  maxlength="3" type="text" value="' . $data['discountPer'] . '"
                                       class="menu_discount_percentage menu_qty menuItem_input numberFloat numpad"> <!-- disc. % -->
                            </div>
                            <div class="receiptPadding ' . $discountPolicyClass . '">
                                <input style="width:90%;" onchange="item_wise_discount(this,\'A\')" name="discountAmount[' . $data['menuSalesItemID'] . ']" type="text" value="' . $data['discountAmount'] . '"
                                       class="menu_discount_amount menu_qty menuItem_input numberFloat numpad"><!-- disc. amount -->
                            </div>
                            <div class="receiptPadding">
                                <div style="width:55px; text-align: right;" class="itemCostNet menuItemTxt set-delete"> ' . $data['sellingPrice'] . '</div> <!-- net total -->
                                <div onclick="beforeDeleteItem(' . $data['menuSalesItemID'] . ')" data-placement="bottom" rel="tooltip" title="Delete"
                                     style="cursor:pointer; width: 12px; margin-top: -20px;     margin-right: 0px;" class="pull-right">';
                if ($template == 2) {
                    $output .= '<button type="button" class="btn btn-default btn-sm itemList-delBtn c-b-20"><i class="fa fa-close closeColor"></i></button> </div>';
                } else {
                    $output .= '<i class="fa fa-close closeColor"></i></button>';
                }

                $output .= '</div>
                        </div>';
                $output .= '</div>';


            }
        }
        echo $output;
    }


    function Load_pos_holdInvoiceData_touch()
    {

        $invoiceID = $this->input->post('invoiceID');
        $getMenuInvoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID);
        $output = '';
        if (!empty($getMenuInvoice)) {
            /*echo '<pre>';
            print_r($getMenuInvoice );
            echo '</pre>';*/

            foreach ($getMenuInvoice as $data) {
                $data['warehouseMenuID'] = str_pad($data['warehouseMenuID'], 4, "0", STR_PAD_LEFT);

                $output .= '<div class="row itemList" id="item_row_' . $data['menuSalesItemID'] . '" style="margin: 0px; border-bottom: 1px solid #dddddd; padding-top: 5px; padding-bottom: 5px;">';
                if (!empty($data['remarkes'])) {
                    $output .= '<div class="col-md-12" style="padding-left: 0;"><button class="pull-left btn btn-lg btn-default" type="button" style="margin-right: 0.5%;" onclick="openaddonList(' . $data['menuSalesItemID'] . ',' . $invoiceID . ',\'' . $data['remarkes'] . '\')"><i class="fa fa-plus fa-lg text-green"   aria-hidden="true" ></i></button><div class="receiptPadding" style="text-align: left; font-weight: 800; cursor: pointer;"   title="' . $data['remarkes'] . '" rel="tooltip" > ' . $data['menuMasterDescription'] . '</div>';
                } else {
                    $output .= '<div class="col-md-12"style="padding-left: 0;"><button class="pull-left btn btn-lg btn-default"  type="button" style="margin-right: 0.5%;" onclick="openaddonList(' . $data['menuSalesItemID'] . ',' . $invoiceID . ')"><i class="fa fa-plus fa-lg text-green"   aria-hidden="true" ></i></button><div class="receiptPadding" style="text-align: left; font-weight: 800; cursor: pointer;"> ' . $data['menuMasterDescription'] . '</div>';
                }
                $output .= '<div class="receiptPadding">
                                <input type="text" style="max-width: 80px;" onkeyup="calculateFooter()" value="' . $data['qty'] . '" class="display_qty menuItem_input numberFloat" name="qty[' . $data['menuSalesItemID'] . ']"  />
                            </div>
                            <div class="receiptPadding">
                                <span class="menu_itemCost menuItemTxt">' . $data['sellingPrice'] . '</span> <!-- @rate -->
                                <input type="hidden"  class="menuItemTxt_input numberFloat" name="sellingPrice[' . $data['menuSalesItemID'] . ']" value="' . $data['sellingPrice'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input numberFloat pricewithoutTax" name="pricewithoutTax[' . $data['menuSalesItemID'] . ']" value="' . $data['pricewithoutTax'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input totalMenuTaxAmount numberFloat" name="totalMenuTaxAmount[' . $data['menuSalesItemID'] . ']" value="' . $data['totalTaxAmount'] . '"/>
<input type="hidden"  class="menuItemTxt_input numberFloat totalMenuServiceCharge" name="totalMenuServiceCharge[' . $data['menuSalesItemID'] . ']" value="' . $data['totalServiceCharge'] . '"/>
<input type="hidden"  name="frm_isTaxEnabled[' . $data['menuSalesItemID'] . ']" value="' . $data['isTaxEnabled'] . '"/>
                            </div>
                            <div class="receiptPadding">
                                <span class="menu_total menuItemTxt">0</span>  <!-- total -->
                            </div>

                            <div class="receiptPadding hide">
                                <input style="width:60%;" onkeyup="calculateFooter(\'P\')" name="discountPercentage[' . $data['menuSalesItemID'] . ']"  maxlength="3" type="text" value="' . $data['discountPer'] . '"
                                       class="menu_discount_percentage menu_qty menuItem_input numberFloat"> <!-- disc. % -->
                            </div>
                            <div class="receiptPadding hide">
                                <input style="width:90%;" onkeyup="calculateFooter(\'A\')" name="discountAmount[' . $data['menuSalesItemID'] . ']" type="text" value="' . $data['discountAmount'] . '"
                                       class="menu_discount_amount menu_qty menuItem_input numberFloat"><!-- disc. amount -->s
                            </div>
                            <div class="receiptPadding" style="width:19%;">
                                <div style="text-align: right;" class="itemCostNet menuItemTxt"> ' . $data['sellingPrice'] . '</div> <!-- net total -->
                                <button onclick="deleteDiv(' . $data['menuSalesItemID'] . ')" data-placement="bottom" rel="tooltip" title="Delete"
                                     style="cursor:pointer; margin-top: -20px;     margin-right: -47px;" type="button" class="pull-right btn btn-default itemList-delBtn">
                                     <i class="fa fa-close closeColor"></i></button>
                             
                        </div></div>';
                $output .= '</div>';


            }
        }
        echo $output;
    }

    function delete_menuSalesItem()
    {
        $id = $this->input->post('id');
        $outletID = $this->input->post('outletID');
        $output = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_specific($id,$outletID);
        if (!empty($output)) {
            $isPack = $output['isPack'];
            if ($isPack == 1) {
                $this->Pos_restaurant_model->delete_srp_erp_pos_valuepackdetail_by_ItemID($id);
            }
            $result = $this->Pos_restaurant_model->delete_menuSalesItem($id,$outletID);
            if ($result) {
                /** Delete Ad-on */
                $this->db->select("menuSalesItemID");
                $this->db->from("srp_erp_pos_menusalesitems");
                $this->db->where("parentMenuSalesItemID", $id);
                $output = $this->db->get()->result_array();
                if (!empty($output)) {
                    $this->db->where("parentMenuSalesItemID", $id);
                    $this->db->delete('srp_erp_pos_menusalesitems');
                }
                echo json_encode(array('error' => 0, 'message' => 'done', 'add_on' => $output));
            } else {
                echo json_encode(array('error' => 1, 'message' => 'Error deleting!, Please contact system support team'));
            }
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Error deleting, Record not found!'));
        }
    }

    function clearPosInvoiceSession()
    {
        $this->session->unset_userdata('pos_invoice_no');
        echo json_encode(array('error' => 0, 'message' => 'session unset'));
    }

    function clearPosInvoiceSession_return()
    {
        $this->session->unset_userdata('pos_invoice_no');
        return json_encode(array('error' => 0, 'message' => 'session unset'));
    }

    function cancelCurrentOrder()
    {
        $result = isPos_invoiceSessionExist();
        if ($result) {

            $this->db->trans_begin();

            $menuSalesID = $result;
            /** Delete related tables */
            $this->Pos_restaurant_model->delete_srp_erp_pos_menusalesmaster($menuSalesID);
            $this->Pos_restaurant_model->delete_srp_erp_pos_menusalesitems_byMenuSalesID($menuSalesID);
            $this->Pos_restaurant_model->delete_srp_erp_pos_menusalesitemdetails_byMenuSalesID($menuSalesID);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('error' => 1, 'message' => 'Error, while cancelling the invoice , Please refresh and check or contact your system support team'));

            } else {
                $this->db->trans_commit();
                echo json_encode(array('error' => 0, 'message' => 'Invoice successfully canceled.'));
            }

        } else {
            echo json_encode(array('error' => 1, 'message' => 'There is no current invoice to cancel, You can create new Invoice'));
        }

    }

    function update_posListItems($isHold=false)
    {
        $invoiceID = isPos_invoiceSessionExist();
        if ($invoiceID) {
            $post = $this->input->post();
            $this->db->trans_rollback();

            $billData = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID);

            $discountPer = $this->input->post('discount_percentage'); /*Bill Discount*/
            $promotionID = $this->input->post('promotionID');
            $promotionDiscount = $this->db->select('commissionPercentage')->from('srp_erp_pos_customers')->where('customerID', $promotionID)->get()->row('commissionPercentage');


            $modifiedUserName = current_user();
            $modifiedDateTime = format_date_mysql_datetime();
            $modifiedUserID = current_userID();
            $modifiedPCID = current_pc();

            $qty = $this->input->post('qty');
            $discountPercentage_post = $this->input->post('discountPercentage');
            $discountAmount_post = $this->input->post('discountAmount');
            $sellingPrice_post = $this->input->post('sellingPrice');
            $priceWithoutTax_post = $this->input->post('pricewithoutTax');
            $pricewithoutTaxDiscount_post = $this->input->post('pricewithoutTaxDiscount');
            $isTaxesEnabled = $this->input->post('frm_isTaxEnabled');

            $taxAmount_post = $this->input->post('totalMenuTaxAmount');
            $taxAmountDiscount_post = $this->input->post('totalMenuTaxAmountDiscount');
            $serviceCharge_post = $this->input->post('totalMenuServiceCharge');
            $serviceChargeDiscount_post = $this->input->post('totalMenuServiceChargeDiscount');


            $data = array();
            if (!empty($qty)) {
                $i = 0;
                foreach ($qty as $key => $value) {
                    $itemQty = $value;
                    $discountPercentage = $discountPercentage_post[$key];
                    $discountAmount = $discountAmount_post[$key];
                    $sellingPrice = $sellingPrice_post[$key];
                    $totalSellingPrice = $sellingPrice * $itemQty;
                    $priceWithoutTax = $priceWithoutTax_post[$key];
                    $isTaxEnabled = $isTaxesEnabled[$key];

                    $net_item_wise_discount = ($pricewithoutTaxDiscount_post[$key] + $taxAmountDiscount_post[$key] + $serviceChargeDiscount_post[$key]) * $itemQty;
                    $data[$i]['discountAmount'] = $net_item_wise_discount; // total discount only - item wise

                    $tmp_net_sales = (($priceWithoutTax_post[$key] + $taxAmount_post[$key] + $serviceCharge_post[$key]) * $itemQty) - $net_item_wise_discount;


                    if ($discountPer > 0 && $promotionDiscount > 0) {
                        $generalDiscount_amount = $tmp_net_sales * ($discountPer / 100);
                        $promotionDiscount_amount = ($tmp_net_sales - $generalDiscount_amount) * ($promotionDiscount / 100);
                        $salesPriceAfterDiscount = $tmp_net_sales - ($generalDiscount_amount + $promotionDiscount_amount);
                        /** output */

                    } else if ($discountPer > 0) {
                        $generalDiscount_amount = $tmp_net_sales * ($discountPer / 100);
                        $salesPriceAfterDiscount = $tmp_net_sales - ($generalDiscount_amount);
                        /** output */

                    } else if ($promotionDiscount > 0) {
                        $promotionDiscount_amount = $tmp_net_sales * ($promotionDiscount / 100);
                        $salesPriceAfterDiscount = $tmp_net_sales - $promotionDiscount_amount;
                        /** output */

                    } else {
                        $salesPriceAfterDiscount = $tmp_net_sales;
                        /** output */
                    }

                    $data[$i]['salesPriceAfterDiscount'] = $salesPriceAfterDiscount;


                    $data[$i]['menuSalesItemID'] = $key;
                    $data[$i]['qty'] = $itemQty;
                    $data[$i]['discountPer'] = $discountPercentage;
                    //$data[$i]['discountAmount'] = $discountAmount; wrong
                    $data[$i]['salesPriceSubTotal'] = $itemQty * $priceWithoutTax;

                    $netTotal = ($itemQty * $priceWithoutTax) - ($pricewithoutTaxDiscount_post[$key] * $itemQty);

                    $discountedTax = $taxAmount_post[$key] * $itemQty;

                    /** normal Discount */
                    if ($discountPer > 0) {
                        $netTotal = $netTotal * ((100 - $discountPer) / 100);
                        $discountedTax = ($discountedTax * ((100 - $discountPer) / 100));

                        $totalSellingPrice = ($totalSellingPrice * ((100 - $discountPer) / 100));

                    }

                    /** Promotional Discount */
                    if ($promotionDiscount > 0) {
                        $netTotal = $netTotal * ((100 - $promotionDiscount) / 100);
                        //$discountedTax = ($promotionDiscount * ((100 - $promotionDiscount) / 100)) * $itemQty;
                        $discountedTax = ($discountedTax * ((100 - $promotionDiscount) / 100));
                        /*echo $netTotal;*/
                        $totalSellingPrice = ($totalSellingPrice * ((100 - $promotionDiscount) / 100));
                    }

                    if ($isTaxEnabled == 0) {
                        $netTotal += ($discountedTax - ($taxAmountDiscount_post[$key] * $itemQty));
                    }


                    //$data[$i]['salesPriceAfterDiscount'] = $totalSellingPrice;
                    $data[$i]['salesPriceNetTotal'] = $netTotal;
                    $data[$i]['netRevenueTotal'] = $netTotal;
                    $data[$i]['isOrderPending'] = 1;
                    $data[$i]['isOrderPending'] = 1;

                    $data[$i]['totalMenuTaxAmount'] = $taxAmount_post[$key];
                    $data[$i]['totalMenuServiceCharge'] = $serviceCharge_post[$key];

                    $data[$i]['modifiedPCID'] = $modifiedPCID;
                    $data[$i]['modifiedUserID'] = $modifiedUserID;
                    $data[$i]['modifiedDateTime'] = $modifiedDateTime;
                    $data[$i]['modifiedUserName'] = $modifiedUserName;
                    if(!$isHold){
                        $data[$i]['is_sync'] = 0;
                    }

                    $i++;
                }
            }
            if (!empty($data)) {
                $this->db->update_batch('srp_erp_pos_menusalesitems', $data, 'menuSalesItemID');
            }
            return array('error' => 0, 'message' => 'done');

        } else {
            return array('error' => 1, 'message' => 'Receipt not created yet');
        }

    }

    function isFinalPayment()
    {
        $return = true;
        $invoiceID = isPos_invoiceSessionExist();
        if ($invoiceID) {
            $isDeliveryConfirmed = isDeliveryConfirmedOrder($invoiceID);
            if ($isDeliveryConfirmed) {
                /** Total Bill Amount */
                $this->db->select('*');
                $this->db->from('srp_erp_pos_menusalesmaster');
                $this->db->where('menuSalesID', $invoiceID);
                $totalBillAmount = $this->db->get()->row('subTotal');

                /** Total Paid Amount*/
                $q = "SELECT SUM(amount) as totalPaid FROM srp_erp_pos_menusalespayments WHERE menuSalesID = '" . $invoiceID . "'";
                $totalPaid = $this->db->query($q)->row('totalPaid');

                if ($totalBillAmount != $totalPaid) {
                    $return = false;
                }

            }
        } else {
            $return = false;
        }
        return $return;
    }

    function submit_pos_payments()
    {
        $this->db->trans_start();
        $time = $this->input->post('currentTime');
        $curDateTmp = date('Y-m-d') . $time;
        $curDate = format_date_mysql_datetime($curDateTmp);

        $invoiceID = isPos_invoiceSessionExist();

        if ($invoiceID) {
            $commissionGL = get_glInfo_for_MenuSalesMaster_update(4);
            $deliveryPersonID = $this->input->post('deliveryPersonID');

            $deliveryCommission = 0;
            if ($deliveryPersonID > 0) {
                $r = $this->Pos_restaurant_model->get_customerInfo($deliveryPersonID);
                if (!empty($r)) {
                    $deliveryCommission = $r['commissionPercentage'];
                }
                $data['isDelivery'] = 1;
                $data['deliveryPersonID'] = $deliveryPersonID;
                $data['deliveryCommission'] = $deliveryCommission;
                $data['isOnTimeCommision'] = $r['isOnTimePayment'];
            }

            $grossSales = $this->input->post('gross_total_input');
            $disPercentage = $this->input->post('discount_percentage');

            $subTotal = $grossSales;
            if ($disPercentage > 0) {
                $subTotal = $grossSales - ($disPercentage / 100) * $grossSales;
            }


            $wastage = false;
            $wastage_glID = '';
            $promotionID = $this->input->post('promotionID');
            if ($promotionID) {
                $r = $this->Pos_restaurant_model->get_customerInfo($promotionID);
                if (!empty($r)) {
                    $promotionDiscount = $r['commissionPercentage'];
                    if ($r['customerTypeMasterID'] == 3) {
                        $wastage = true;
                        $wastage_glID = $r['expenseGLAutoID'];
                    }
                }
                $data['isPromotion'] = 1;
                $data['promotionID'] = $promotionID;
                $data['promotionDiscount'] = $promotionDiscount;
                if ($data['promotionDiscount'] > 0) {
                    $subTotal = $subTotal - ($data['promotionDiscount'] / 100) * $subTotal;
                    $data['promotionDiscountAmount'] = ($data['promotionDiscount'] / 100) * $this->input->post('total_payable_amt');
                }
            } else {
                $data['isPromotion'] = 0;
                $data['promotionID'] = 0;
                $data['promotionDiscount'] = 0;
                $data['promotionDiscountAmount'] = 0;
            }


            $this->update_posListItems();
            $this->Pos_restaurant_model->updateTotalCost($invoiceID);
            $this->updateMenuSalesItemDetail($invoiceID, $wastage, $wastage_glID);

            $paid = $this->input->post('paid');
            $cardTotalAmount = $this->input->post('cardTotalAmount');

            $data['menuSalesID'] = $invoiceID;
            $data['customerName'] = $this->input->post('customerName');
            $data['customerTelephone'] = $this->input->post('customerTelephone');


            $data['isCreditSales'] = $this->input->post('isCreditSale');
            $data['cardRefNo'] = $this->input->post('card_numb');
            //$data['subTotal'] = $this->input->post('gross_total_input') - $this->input->post('total_discount_amount');
            //$data['subTotal'] = $subTotal;
            $data['subTotal'] = $grossSales;
            $data['discountPer'] = $this->input->post('discount_percentage');
            $data['discountAmount'] = $this->input->post('total_discount_amount');
            $data['netTotal'] = $this->input->post('total_payable_amt'); // amount has to be paid : net Amount
            $data['paidAmount'] = $this->input->post('netTotalAmount');

            $data['balanceAmount'] = $this->input->post('returned_change'); // remain amount
            $data['cashReceivedAmount'] = $paid;  // cash paid by user may be there will be return
            $data['cashAmount'] = $paid - $cardTotalAmount;  // cash paid by user may be there will be return
            $data['chequeAmount'] = 0;
            $data['chequeNo'] = $this->input->post('cheque');
            $data['serviceCharge'] = $this->input->post('serviceCharge');
            $isDelivery = isDeliveryConfirmedOrder($invoiceID);
            if ($isDelivery) {
                $deliveryInfo = get_confirmedDeliveryOrder($invoiceID);
                $data['isHold'] = 1;
                $data['holdRemarks'] = 'Delivery Date:' . $deliveryInfo['deliveryDate'] . '  Time: ' . $deliveryInfo['deliveryTime'];
            } else {
                $data['isHold'] = 0;
            }

            $data['grossAmount'] = $this->input->post('gross_total_amount_input');
            $data['grossTotal'] = $this->input->post('gross_total_input');
            $data['totalQty'] = $this->input->post('total_item_qty_input');
            $data['totalTaxPercentage'] = $this->input->post('totalTax_input');
            $data['totalTaxAmount'] = $this->input->post('display_tax_amt_input');
            $data['paymentMethod'] = $this->input->post('payment_method');
            $data['isOrderPending'] = 1;

            /** GL Updates **/
            $netTotal = $this->input->post('total_payable_amt');
            $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID);
            $bankCurrencyID = null; //$glInfo['bankCurrencyID'];
            $conversion = currency_conversionID($invoice['transactionCurrencyID'], $bankCurrencyID, $netTotal);

            if ($conversion['conversion'] != 0) {
                $bankCurrencyAmount = $paid / $conversion['conversion'];
            } else {
                $bankCurrencyAmount = $paid;
            }

            $data['bankGLAutoID'] = null;
            $data['bankCurrencyID'] = null;
            $data['bankCurrency'] = null;
            $data['bankCurrencyDecimalPlaces'] = null; //$glInfo['bankCurrencyDecimalPlaces'];
            $data['bankCurrencyExchangeRate'] = $conversion['conversion'];
            $data['bankCurrencyAmount'] = $bankCurrencyAmount;
            $data['commissionGLAutoID'] = $commissionGL["GLAutoID"];

            /**  End of GL Updates **/

            $data['modifiedPCID'] = current_pc();
            $data['modifiedUserID'] = current_userID();
            $data['modifiedUserName'] = current_user();
            $data['modifiedDateTime'] = $curDate;
            $data['is_sync'] = 0;

            //print_r($data);

            $this->Pos_restaurant_model->update_srp_erp_pos_menusalesmaster($data, $invoiceID); // Sync DONE

            $this->updateNetTotalForInvoice($invoiceID); // Sync DONE

            /** update payments */
            $this->Pos_restaurant_model->update_pos_payments(); // Sync DONE

            /*$this->db->trans_complete(); // tmp line delete this later
            $this->db->trans_rollback();// tmp line delete this later
            exit;*/

            $isFinalPayment = $this->isFinalPayment();
            if ($isFinalPayment) {
                /*UPDATE TAXES */
                $this->Pos_restaurant_model->update_menuSalesTax($invoiceID); // Sync DONE

                $is_dineIn_order = is_dineIn_order($invoiceID);
                $get_pos_templateID = get_pos_templateID();
                if ($get_pos_templateID == 2 || $get_pos_templateID == 4) {
                    if ($is_dineIn_order) {
                        /*UPDATE SERVICE CHARGES */
                        $this->Pos_restaurant_model->update_menuSalesServiceCharge($invoiceID); // Sync DONE
                    }
                } else {
                    /*UPDATE SERVICE CHARGES */
                    $this->Pos_restaurant_model->update_menuSalesServiceCharge($invoiceID); // Sync DONE
                }


                /*UPDATE DELIVERY COMMISSION AMOUNT - ONLY FOR DELIVERY ORDERS */
                $this->Pos_restaurant_model->update_deliveryCommission($invoiceID); // Sync DONE
            }

            $this->Pos_restaurant_model->update_diningTableReset($invoice['tableID']); // DO NOT NEED SYNC - because table status are doesn't need to be updated in the server.

            $sql = 'select count(menuSalesID) as syncCount from srp_erp_pos_menusalesmaster where is_sync = 0';
            $count = $this->db->query($sql)->row('syncCount');

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('error' => 1, 'message' => 'error, please contact your support team' . $this->db->_error_message(), 'sync_pending_bill_count'=>$count));
            } else {
                $this->db->trans_commit();
                $this->session->unset_userdata('pos_invoice_no');
                $outletID = get_outletID();
                echo json_encode(array('error' => 0, 'message' => 'payment submitted', 'invoiceID' => $invoiceID, 'outletID' => $outletID, 'sync_pending_bill_count'=>$count));
            }


        } else {
            echo json_encode(array('error' => 1, 'message' => 'Receipt not created yet'));
        }
    }

    /**
     * UPDATE NET TOTAL
     * @param $menuSalesID
     * @return mixed
     */
    function updateNetTotalForInvoice($menuSalesID)
    {
        $outletID = get_outletID();

        $q = "UPDATE srp_erp_pos_menusalesmaster
                SET netTotal = (
                    SELECT sum(IFNULL(salesPriceNetTotal,0)) AS totalNet FROM srp_erp_pos_menusalesitems WHERE menuSalesID = '" . $menuSalesID . "' AND warehouseMenuID = '" . $outletID . "'
                ),
                 netRevenueTotal = (
                    SELECT sum(IFNULL(salesPriceNetTotal,0)) AS totalNet FROM srp_erp_pos_menusalesitems WHERE menuSalesID = '" . $menuSalesID . "' AND warehouseMenuID = '" . $outletID . "'
                )
                WHERE
                    menuSalesID = '" . $menuSalesID . "' AND wareHouseAutoID = '" . $outletID . "'";

        $result = $this->db->query($q);
        return $result;


    }

    function updateMenuSalesItemDetail($invoiceID, $wastage = false, $wastage_glID = null)
    {

        /* Setup Default values */
        $warehouseInfo = $this->Pos_restaurant_model->get_wareHouse();

        $segmentID = $warehouseInfo['segmentID'];
        $segmentCode = $warehouseInfo['segmentCode'];
        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $createdUserName = current_user();
        $createdUserGroup = current_user_group();
        $timeStamp = format_date_mysql_datetime();
        $warehouseID = get_outletID();


        $batchData = array();
        /*get items for the invoice */
        $result = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID);


        /*loop items */
        $i = 0;
        foreach ($result as $item) {

            $menuMaster = $item['menuMasterID'];

            /*Get items */
            $itemDetailList = $this->Pos_restaurant_model->get_srp_erp_pos_menudetails_by_menuMasterID($menuMaster);

            /*setup array */
            foreach ($itemDetailList as $itemDetail) {
                $batchData[$i]['menuSalesItemID'] = $item['menuSalesItemID'];
                $batchData[$i]['warehouseAutoID'] = $warehouseID;
                $batchData[$i]['menuSalesID'] = $item['menuSalesID'];
                $batchData[$i]['itemAutoID'] = $itemDetail['itemAutoID'];
                $batchData[$i]['qty'] = $itemDetail['qty'];
                $batchData[$i]['UOM'] = $itemDetail['UOM'];
                $batchData[$i]['UOMID'] = $itemDetail['uomID'];
                $batchData[$i]['cost'] = $itemDetail['cost'];
                $batchData[$i]['actualInventoryCost'] = $itemDetail['actualInventoryCost'];
                $batchData[$i]['menuSalesQty'] = $item['qty'];
                $batchData[$i]['menuID'] = $menuMaster;
                $batchData[$i]['costGLAutoID'] = $wastage ? $wastage_glID : $itemDetail['costGLAutoID'];
                $batchData[$i]['assetGLAutoID'] = $itemDetail['assetGLAutoID'];
                $batchData[$i]['isWastage'] = $wastage ? 1 : 0;

                $batchData[$i]['companyID'] = $companyID;
                $batchData[$i]['companyCode'] = $companyCode;
                $batchData[$i]['segmentID'] = $segmentID;
                $batchData[$i]['segmentCode'] = $segmentCode;
                $batchData[$i]['createdPCID'] = $createdPCID;
                $batchData[$i]['createdUserID'] = $createdUserID;
                $batchData[$i]['createdDateTime'] = $timeStamp;
                $batchData[$i]['createdUserName'] = $createdUserName;
                $batchData[$i]['createdUserGroup'] = $createdUserGroup;
                $batchData[$i]['timeStamp'] = $timeStamp;
                $batchData[$i]['id_store'] = $warehouseID;
                $i++;
            }
        }


        /*End of loop*/

        /*batch update the array*/
        if (!empty($batchData)) {
            $this->Pos_restaurant_model->batch_insert_srp_erp_pos_menusalesitemdetails($batchData);
        }

    }

    function loadPrintTemplate()
    {
        $invoiceID = isPos_invoiceSessionExist();
        $invoiceID = ($invoiceID) ? $invoiceID : $this->input->post('invoiceID');
        $outletID = $this->input->post('outletID');
        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID,$outletID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID,$outletID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['auth'] = false;
        $data['wifi'] = true;
        $template = get_print_template();
        //$this->load->view('system/pos/printTemplate/restaurant-pos-thermal-printer', $data);
        //$this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer', $data);
        $this->load->view($template, $data);
    }

    function loadPrintTemplateSampleBill()
    {
        $outletID = get_outletID();
        $invoiceID = trim($this->input->post('invoiceID'));
        $promotionID = trim($this->input->post('promotionID'));
        $promotional_discount = trim($this->input->post('promotional_discount'));
        $promotionIDdatacp = trim($this->input->post('promotionIDdatacp'));
        $this->update_posListItems();

        $this->Pos_restaurant_model->update_isSampleBillPrintFlag($invoiceID, $outletID);

        if ($promotionID) {
            $data['isPromotion'] = 1;
            $data['promotionID'] = $promotionID;
            $data['promotionDiscount'] = $promotionIDdatacp;
            $data['promotionDiscountAmount'] = $promotional_discount;
            $this->db->where('menuSalesID', $invoiceID)->update('srp_erp_pos_menusalesmaster', $data);
        } else {
            $data['isPromotion'] = 0;
            $data['promotionID'] = null;
            $data['promotionDiscount'] = 0;
            $data['promotionDiscountAmount'] = 0;
            $this->db->where('menuSalesID', $invoiceID)->update('srp_erp_pos_menusalesmaster', $data);
        }

        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID_forHoldBill($invoiceID, $outletID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID, $outletID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['sampleBill'] = true;
        $data['auth'] = false;
        //$this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer-reprint', $data);
        //$this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer', $data);
        $template = get_print_template();
        $data['template'] = $template;
        $this->load->view($template, $data);

    }

    function submitHoldReceipt()
    {
        $id = isPos_invoiceSessionExist();

        if ($id) {
            //$this->form_validation->set_rules('holdReference', 'Hold Reference', 'trim|required');
            $data = array();
            if ($this->form_validation->run() == FALSE && false) {
                echo json_encode(array('error' => 1, 'message' => validation_errors()));
            } else {
                $data['isHold'] = 1;
                $data['holdByUserID'] = current_pc();
                $data['holdByUsername'] = current_user();
                $data['holdPC'] = current_pc();
                $data['holdDatetime'] = format_date_mysql_datetime();
                $data['holdRemarks'] = $this->input->post('holdReference');

                $this->update_posListItems(true);
                $result = $this->Pos_restaurant_model->update_srp_erp_pos_menusalesmaster($data, $id);
                if ($result) {
                    echo json_encode(array('error' => 0, 'message' => 'Receipt hold successfully'));
                } else {
                    echo json_encode(array('error' => 1, 'message' => 'error, while updating'));
                }
            }
        } else {
            echo json_encode(array('error' => 1, 'message' => '<strong>Invoice Not created.</strong> <br>Please create the invoice and hold the receipt.'));
        }
    }

    function load_pos_hold_receipt()
    {
        $data['holdReceipt'] = null;
        $this->load->view('system/pos/ajax/ajax-restaurant-pos-openHold', $data);
    }

    function load_pos_hold_receipt_tablet()
    {
        $data['holdReceipt'] = null;
        $this->load->view('system/pos/ajax/ajax-restaurant-pos-openHold-tablet', $data);
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

    function loadHoldListPOS()
    {
        $this->datatables->select('master.menuSalesID as menuSalesID, master.wareHouseAutoID as wareHouseAutoID, master.invoiceCode as invoiceCode, netTotal as netTotal, holdByUsername as createdUser, holdDatetime  as holdDate, if(ISNULL(holdRemarks), "auto_remarks", holdRemarks) as remarks,  master.createdDateTime as createdDate, dt.diningTableDescription, master.BOT as BOT, master.isFromTablet as isFromTablet', false)
            ->from('srp_erp_pos_menusalesmaster master')
            ->join('srp_erp_pos_deliveryorders d', 'd.menuSalesMasterID = master.menuSalesID', 'left')
            ->join('srp_erp_pos_diningtables dt', 'dt.diningTableAutoID = master.tableID', 'left')
            ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(menuSalesID)')
            ->add_column('openHold', '$1', 'btn_openHold(menuSalesID,\'open\',wareHouseAutoID)')
            ->add_column('status', '$1', 'status_BOT(BOT,isFromTablet)')
            ->where('master.isHold', 1)
            ->where('master.companyID', current_companyID())
            ->where('master.wareHouseAutoID', get_outletID())
            ->where('d.deliveryOrderID IS NULL');
        echo $this->datatables->generate();
    }

    function loadHoldListPOS_tablet()
    {
        $this->datatables->select('master.menuSalesID as menuSalesID, master.wareHouseAutoID as wareHouseAutoID , master.invoiceCode as invoiceCode, netTotal as netTotal, holdByUsername as createdUser, holdDatetime  as holdDate, if(ISNULL(holdRemarks), "auto_remarks", holdRemarks) as remarks,  master.createdDateTime as createdDate, dt.diningTableDescription', false)
            ->from('srp_erp_pos_menusalesmaster master')
            ->join('srp_erp_pos_deliveryorders d', 'd.menuSalesMasterID = master.menuSalesID', 'left')
            ->join('srp_erp_pos_diningtables dt', 'dt.diningTableAutoID = master.tableID', 'left')
            ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(menuSalesID)')
            ->add_column('openHold', '$1', 'btn_openHold(menuSalesID,\'open\',wareHouseAutoID)')
            ->where('master.isHold', 1)
            ->where('master.companyID', current_companyID())
            ->where('master.wareHouseAutoID', get_outletID())
            ->where('master.BOT', 0)
            ->where('master.createdUserID', current_userID())
            ->where('d.deliveryOrderID IS NULL');
        echo $this->datatables->generate();
    }

    function loadDeliveryOrderPending()
    {
        $this->datatables->select('master.menuSalesID as menuSalesID,  master.wareHouseAutoID as wareHouseAutoID, master.invoiceCode as invoiceCode, netTotal as netTotal, holdByUsername as createdUser, holdDatetime  as holdDate, if(ISNULL(holdRemarks), "auto_remarks", holdRemarks) as remarks,  master.createdDateTime as createdDate, c.customerTelephone as customerTelephone, c.CustomerName as CustomerName', false)
            ->from('srp_erp_pos_menusalesmaster master')
            ->join('srp_erp_pos_deliveryorders d', 'd.menuSalesMasterID = master.menuSalesID', 'left')
            ->join('srp_erp_pos_customermaster c', 'd.posCustomerAutoID = c.posCustomerAutoID ', 'left')
            ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(menuSalesID)')
            ->add_column('openHold', '$1', 'btn_openHold(menuSalesID,\'open\',wareHouseAutoID)')
            ->where('master.isHold', 1)
            ->where('master.companyID', current_companyID())
            ->where('master.wareHouseAutoID', get_outletID())
            ->where('d.deliveryOrderID IS NOT NULL');
        echo $this->datatables->generate();
    }

    function loadKitchenReady()
    {
        $counterData = get_counterData();
        $shiftID = $counterData['shiftID'];

        $this->datatables->select('menuSalesID as menuSalesID, invoiceCode as invoiceID, holdByUsername as createdUser, if(ISNULL(holdDatetime), "-",DATE_FORMAT(holdDatetime,\'%d-%b-%Y\'))  as holdDate, if(ISNULL(holdRemarks), "-", holdRemarks) as remarks, DATE_FORMAT(createdDateTime,\'%d-%b-%Y\') as createdDate, isHold as isHold , isOrderPending as isOrderPending, isOrderInProgress as isOrderInProgress, isOrderCompleted as isOrderCompleted', false)
            ->from('srp_erp_pos_menusalesmaster master')
            //->add_column('invoiceID', '$1', 'get_pos_invoice_code(menuSalesID)')
            ->add_column('openHold', '$1', 'btn_viewKitchenStatus(menuSalesID,\'Open\')')
            ->add_column('status', '$1', 'kitchen_status(isHold,isOrderPending,isOrderInProgress,isOrderCompleted,PN)')
            // ->where('master.isHold', 1)
            ->where('master.isOrderPending', 1);
        //->where('master.isOrderCompleted', 1)
        //->where('master.isOrderCompleted', format_date_mysql_datetime())
        //$this->datatables->like('createdDateTime', date('Y-m-d'));

        $this->datatables->where('master.shiftID', $shiftID);
        $this->datatables->where('master.companyID', current_companyID());
        echo $this->datatables->generate();
    }

    function openHold_sales()
    {
        $id = $this->input->post('id');
        $outletID = $this->input->post('outletID');
        //$outletID = get_outletID();
        if (!empty($id)) {
            set_session_invoiceID($id);
            $this->updateShift($id);

            $this->db->select('*');
            $this->db->from('srp_erp_pos_deliveryorders');
            $this->db->where('menuSalesMasterID', $id);
            $deliveryOrderID = $this->db->get()->row('deliveryOrderID');
            if ($deliveryOrderID) {
                $delivery = 1;
            } else {
                $delivery = 0;
            }

            $q = "SELECT SUM(amount) as tmpAmount FROM srp_erp_pos_menusalespayments WHERE menuSalesID = '" . $id . "'";
            $advancePayment = $this->db->query($q)->row('tmpAmount');

            echo json_encode(array('error' => 0, 'message' => 'set session', 'code' => get_pos_invoice_code($id, $outletID), 'advancePayment' => $advancePayment, 'isDeliveryOrder' => $delivery, 'deliveryOrderID' => $deliveryOrderID));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'ID Not found'));
        }
    }

    function updateShift($menuSalesID)
    {
        $outletID = get_outletID();
        $get_shift = $this->Pos_restaurant_model->get_srp_erp_pos_shiftdetails_employee();
        $currentShiftID = $get_shift['shiftID'];
        $data['shiftID'] = $currentShiftID;
        $this->db->where('menuSalesID', $menuSalesID);
        $this->db->where('id_store', $outletID);
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);
        return $result;
    }

    function load_packItemList()
    {
        $warehouseMenuID = $this->input->post('id');

        $warehouseMenu = $this->Pos_restaurant_model->get_warehouseMenuItem($warehouseMenuID);
        $packItemDetail = $this->Pos_restaurant_model->get_packGroup_menuItem($warehouseMenu['menuMasterID']);
        $data['id'] = $warehouseMenuID;
        $data['menuMasterID'] = $warehouseMenu['menuMasterID'];
        $data['warehouseMenu'] = $warehouseMenu;
        $data['packItemDetail'] = $packItemDetail;

        $this->load->view('system/pos/posRestaurant/terminal/ajax-load-packDetail', $data);
    }

    function savePackDetailItemList()
    {

        $id = $this->input->post('id');
        $pack_menuID = $this->input->post('pack_menuID');


        $menuSalesID = isPos_invoiceSessionExist();
        $warehouseMenuID = $this->input->post('warehouseMenuID');
        $menuMasterID = $this->input->post('menuMasterID');
        $menuSalesItemID = $this->input->post('menuSalesItemID');

        $createdBy = current_userID();
        $createdPc = current_pc();
        $createdDatetime = format_date_mysql_datetime();


        $data = array();

        $i = 0;

        /* Required Items */
        $requiredItem = $this->Pos_restaurant_model->get_srp_erp_pos_menupackitem_requiredItems($menuMasterID);
        if (!empty($requiredItem)) {


            foreach ($requiredItem as $item) {
                $data[$i]['menuSalesID'] = $menuSalesID;
                $data[$i]['menuMasterID'] = $menuMasterID;
                $data[$i]['warehouseMenuID'] = $warehouseMenuID;
                $data[$i]['menuPackItemID'] = $item['packgroupdetailID'];
                $data[$i]['menuID'] = $item['menuID'];
                $data[$i]['menuSalesItemID'] = $menuSalesItemID;
                $data[$i]['isRequired'] = 1;
                $data[$i]['qty'] = 1;
                $data[$i]['createdBy'] = $createdBy;
                $data[$i]['createdPc'] = $createdPc;
                $data[$i]['createdDatetime'] = $createdDatetime;
                $data[$i]['timestamp'] = $createdDatetime;
                $i++;
            }
        }

        /* Optional Item */
        if (!empty($id)) {

            foreach ($id as $key => $value) {


                if ($value > 0) {
                    $data[$i]['menuSalesID'] = $menuSalesID;
                    $data[$i]['menuMasterID'] = $menuMasterID;
                    $data[$i]['warehouseMenuID'] = $warehouseMenuID;
                    $data[$i]['menuPackItemID'] = $key;
                    $data[$i]['menuID'] = $pack_menuID[$key];
                    $data[$i]['menuSalesItemID'] = $menuSalesItemID;
                    $data[$i]['isRequired'] = 0;
                    $data[$i]['qty'] = $value;
                    $data[$i]['createdBy'] = $createdBy;
                    $data[$i]['createdPc'] = $createdPc;
                    $data[$i]['createdDatetime'] = $createdDatetime;
                    $data[$i]['timestamp'] = $createdDatetime;
                    $i++;
                }
            }
        }
        //$data = array_values($data);


        if (!empty($data)) {

            $this->Pos_restaurant_model->bulk_insert_srp_erp_pos_valuepackdetail($data);
            echo json_encode(array('error' => 0, 'message' => 'added', $data));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'data not received'));
        }

    }

    function updateCustomerType()
    {
        $customerType = $this->input->post('customerType');

        $invoiceID = isPos_invoiceSessionExist();
        if ($invoiceID) {
            $data['customerTypeID'] = $customerType;
            $result = $this->Pos_restaurant_model->update_srp_erp_pos_menusalesmaster($data, $invoiceID);
            if ($result) {
                echo json_encode(array('error' => '0', 'message' => 'customer type updated'));
            } else {
                echo json_encode(array('error' => '1', 'message' => 'not updated'));
            }
        } else {
            echo json_encode(array('error' => '1', 'message' => 'not updated'));
        }
    }

    function loadPaymentSalesReport()
    {
        $tmpFilterDate = $this->input->post('filterFrom');
        $tmpFilterDateTo = $this->input->post('filterTo');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');

        if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFilterDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $filterDate = $date;
        } else {
            $filterDate = date('Y-m-d');
        }


        if (!empty($tmpFilterDateTo)) {
            $date2 = date('Y-m-d', strtotime($tmpFilterDateTo));
        } else {
            $date2 = date('Y-m-d');
        }

        if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
            $tmpCashier = join(",", $tmpCashierSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }

        if (isset($outlet) && !empty($outlet)) {
            $tmpOutlet = join(",", $outlet);
            $Outlets = $tmpOutlet;
        } else {
            $Outlets = null;
        }

        $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();


        $customerTypeCount = $this->Pos_restaurant_model->get_report_customerTypeCount($filterDate, $date2, $cashier, $Outlets);
        $lessAmounts = $this->Pos_restaurant_model->get_report_lessAmount($filterDate, $date2, $cashier, $Outlets);
        $lessAmounts_promotion = $this->Pos_restaurant_model->get_report_lessAmount_promotion($filterDate, $date2, $cashier, $Outlets);

        $lessAmounts = array_merge($lessAmounts, $lessAmounts_promotion);
        $paymentMethod = $this->Pos_restaurant_model->get_report_paymentMethod($filterDate, $date2, $cashier, $Outlets);

        $data['companyInfo'] = $companyInfo;
        $data['customerTypeCount'] = $customerTypeCount;
        $data['lessAmounts'] = $lessAmounts;
        $data['paymentMethod'] = $paymentMethod;
        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();

        $this->load->view('system/pos/reports/pos-payment-sales-report', $data);
    }

    function loadPaymentSalesReport2()
    {
        $_POST['outletID'] = array(get_outletID());
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpCashierSource = $this->input->post('cashier');
        $outletIDs = $this->input->post('outletID_f');


        if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
            $filterDate = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
        } else {
            $filterDate = date('Y-m-d 00:00:00');
        }


        if (!empty($tmpFilterDateTo)) {
            $date2 = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
        } else {
            $date2 = date('Y-m-d 23:59:59');
        }

        if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
            $tmpCashier = join(",", $tmpCashierSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }

        if (isset($outletIDs) && !empty($outletIDs)) {
            $outlet = join(",", $outletIDs);
            $outlets = $outlet;
        } else {
            $outlets = null;
        }


        $lessAmounts = $this->Pos_restaurant_model->get_report_lessAmount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_promotion = $this->Pos_restaurant_model->get_report_lessAmount_promotion_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_discounts = $this->Pos_restaurant_model->get_report_salesReport_discount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_discounts_item_wise = $this->Pos_restaurant_model->get_report_salesReport_discount_item_wise_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_discountsJavaApp = $this->Pos_restaurant_model->get_report_salesReport_javaAppDiscount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmountsAll = array_merge($lessAmounts_discounts, $lessAmounts, $lessAmounts_promotion, $lessAmounts_discountsJavaApp, $lessAmounts_discounts_item_wise);

        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $data['paymentMethod'] = $this->Pos_restaurant_model->get_report_paymentMethod_admin($filterDate, $date2, $cashier, $outlets);

        $data['customerTypeCount'] = $this->Pos_restaurant_model->get_report_customerTypeCount_2_admin($filterDate, $date2, $cashier, $outlets);
        $data['lessAmounts'] = $lessAmountsAll;


        // var_dump($lessAmountsAll);
        $data['totalSales'] = $this->Pos_restaurant_model->get_report_salesReport_totalSales_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalTaxes'] = $this->Pos_restaurant_model->get_report_salesReport_totalTaxes_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalServiceCharge'] = $this->Pos_restaurant_model->get_report_salesReport_ServiceCharge_admin($filterDate, $date2, $cashier, $outlets);
        $data['giftCardTopUp'] = $this->Pos_restaurant_model->get_report_giftCardTopUp_admin($filterDate, $date2, $cashier, $outlets);
        $data['voidBills'] = $this->Pos_restaurant_model->get_report_voidBills_admin($filterDate, $date2, $cashier, $outlets);
        $data['creditSales'] = $this->Pos_restaurant_model->get_report_creditSales($filterDate, $date2, $cashier, $outlets);
        $data['fullyDiscountBill'] = $this->Pos_restaurant_model->get_report_fullyDiscountBills_admin($filterDate, $date2, $cashier, $outlets);

        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();


        $html = $this->load->view('system/pos/reports/pos-payment-sales-report2', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $data['pdf'] = 'pdf';
            $html = $this->load->view('system/pos/reports/pos-payment-sales-report2', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }

    }


    function loadPaymentSalesReport3()
    {
        $_POST['outletID'] = get_outletID();
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpCashierSource = $this->input->post('cashier');


        if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
            $filterDate = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
        } else {
            $filterDate = date('Y-m-d 00:00:00');
        }


        if (!empty($tmpFilterDateTo)) {
            $date2 = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
        } else {
            $date2 = date('Y-m-d 23:59:59');
        }

        if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
            $tmpCashier = join(",", $tmpCashierSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }


        $lessAmounts = $this->Pos_restaurant_model->get_report_lessAmount2($filterDate, $date2, $cashier);
        $lessAmounts_promotion = $this->Pos_restaurant_model->get_report_lessAmount_promotion2($filterDate, $date2, $cashier);
        $lessAmounts_discounts = $this->Pos_restaurant_model->get_report_salesReport_discount($filterDate, $date2, $cashier);
        $lessAmounts_discountsJavaApp = $this->Pos_restaurant_model->get_report_salesReport_javaAppDiscount($filterDate, $date2, $cashier);
        $outlets = $_POST['outletID'];
        $lessAmounts_discounts_item_wise = $this->Pos_restaurant_model->get_report_salesReport_discount_item_wise_admin($filterDate, $date2, $cashier, $outlets);

        $lessAmountsAll = array_merge($lessAmounts_discounts, $lessAmounts, $lessAmounts_promotion, $lessAmounts_discountsJavaApp, $lessAmounts_discounts_item_wise);


        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $data['paymentMethod'] = $this->Pos_restaurant_model->get_report_paymentMethod2($filterDate, $date2, $cashier);;
        $data['customerTypeCount'] = $this->Pos_restaurant_model->get_report_customerTypeCount2($filterDate, $date2, $cashier);
        $data['lessAmounts'] = $lessAmountsAll;


        // var_dump($lessAmountsAll);
        $data['totalSales'] = $this->Pos_restaurant_model->get_report_salesReport_totalSales($filterDate, $date2, $cashier);
        $data['totalTaxes'] = $this->Pos_restaurant_model->get_report_salesReport_totalTaxes($filterDate, $date2, $cashier);
        $data['totalServiceCharge'] = $this->Pos_restaurant_model->get_report_salesReport_ServiceCharge($filterDate, $date2, $cashier);
        $data['giftCardTopUp'] = $this->Pos_restaurant_model->get_report_giftCardTopUp($filterDate, $date2, $cashier);
        $data['voidBills'] = $this->Pos_restaurant_model->get_report_voidBills($filterDate, $date2, $cashier);

        //var_dump($data['voidBills']);
        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();
        $html = $this->load->view('system/pos/reports/pos-payment-sales-report3', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $data['pdf'] = 'pdf';
            $html = $this->load->view('system/pos/reports/pos-payment-sales-report3', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }

    }

    function loadItemizedSalesReport()
    {
        $_POST['outletID'] = get_outletID();
        $this->form_validation->set_rules('cashier[]', 'cashier', 'trim|required');
        $this->form_validation->set_rules('filterFrom', 'Date From', 'trim|required');
        $this->form_validation->set_rules('filterTo', 'Date To', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo '<div class="alert alert-danger">' . $errors . '</div>';
        } else {
            $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
            $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
            $outlet = $this->input->post('outlet');
            $filterTo = $this->input->post('filterTo');
            $tmpCashierSource = $this->input->post('cashier');

            if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
                $filterDate = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
            } else {
                $filterDate = date('Y-m-d 00:00:00');
            }


            if (!empty($tmpFilterDateTo)) {
                $date2 = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
            } else {
                $date2 = date('Y-m-d 23:59:59');
            }

            if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
                $tmpCashier = join(",", $tmpCashierSource);
                $cashier = $tmpCashier;
            } else {
                $cashier = null;
            }


            if (isset($outlet) && !empty($outlet)) {
                $tmpOutlet = join(",", $outlet);
                $Outlets = $tmpOutlet;
            } else {
                $Outlets = null;
            }

            $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
            $itemizedSalesReport = $this->Pos_restaurant_model->get_itemizedSalesReport($filterDate, $date2, $Outlets, $cashier);

            $data['companyInfo'] = $companyInfo;
            $data['itemizedSalesReport'] = $itemizedSalesReport;
            $data['cashier'] = $tmpCashierSource;
            $data['cashierTmp'] = get_cashiers();


            $this->load->view('system/pos/reports/pos-itemized-sales-report', $data);
        }


    }

    function load_item_wise_sales_report_admin()
    {
        $this->form_validation->set_rules('cashier[]', 'cashier', 'trim|required');
        $this->form_validation->set_rules('filterFrom', 'Date From', 'trim|required');
        $this->form_validation->set_rules('filterTo', 'Date To', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo '<div class="alert alert-danger">' . $errors . '</div>';
        } else {
            $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
            $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
            $outlet = $this->input->post('outletID_f');
            $filterTo = $this->input->post('filterTo');
            $tmpCashierSource = $this->input->post('cashier');

            if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
                $filterDate = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
            } else {
                $filterDate = date('Y-m-d 00:00:00');
            }


            if (!empty($tmpFilterDateTo)) {
                $date2 = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
            } else {
                $date2 = date('Y-m-d 23:59:59');
            }

            if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
                $tmpCashier = join(",", $tmpCashierSource);
                $cashier = $tmpCashier;
            } else {
                $cashier = null;
            }


            if (isset($outlet) && !empty($outlet)) {
                $tmpOutlet = join(",", $outlet);
                $Outlets = $tmpOutlet;
            } else {
                $Outlets = null;
            }

            $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
            $itemizedSalesReport = $this->Pos_restaurant_model->get_itemizedSalesReport($filterDate, $date2, $Outlets, $cashier);


            $data['companyInfo'] = $companyInfo;
            $data['itemizedSalesReport'] = $itemizedSalesReport;
            $data['cashier'] = $tmpCashierSource;
            $data['cashierTmp'] = get_cashiers();
            //$data['cashier'] = $this;
            /*echo '<pre>';
            print_r($itemizedSalesReport);
            echo '</pre>';*/

            $this->load->view('system/pos/reports/pos-itemized-sales-report-admin', $data);
        }
    }

    function close_shift_touchWindow()
    {
        $mySession = $this->Pos_restaurant_model->isHaveNotClosedSession();
        $data['isClosed'] = 1;
        $data['endTime'] = format_date_mysql_datetime();
        $data['modifiedPCID'] = current_pc();
        $data['modifiedUserID'] = current_userID();
        $data['modifiedDateTime'] = format_date_mysql_datetime();
        $data['modifiedUserName'] = current_user();
        $this->db->where('shiftID', $mySession['shiftID']);
        $this->db->where('counterID', null);
        $result = $this->db->update('srp_erp_pos_shiftdetails', $data);
        echo json_encode(array('status' => $result));

    }


    function touchWindow()
    {
        $outletID = get_outletID();
        if ($outletID) {

            $isHaveSession = $this->Pos_restaurant_model->isHaveNotClosedSession_tabUsers();

            if (!empty($isHaveSession)) {

                $mySession = $this->Pos_restaurant_model->isHaveNotClosedSession();
                if (empty($mySession)) {
                    $this->Pos_restaurant_model->create_tmp_session($isHaveSession['shiftID']);
                }


                $tmpWarehouseID = $this->Pos_restaurant_model->get_srp_erp_warehouse_users_WarehouseID();
                $warehouseID = isset($tmpWarehouseID) && !empty($tmpWarehouseID) ? $tmpWarehouseID : 0;

                /** Get Warehouse Menu Items */
                $output = $this->Pos_restaurant_model->get_warehouseMenues($warehouseID);

                /** Get warehouse Category */
                $output2 = $this->Pos_restaurant_model->get_warehouseCategory($warehouseID);

                /** Get warehouse Sub Category */
                $output3 = $this->Pos_restaurant_model->get_warehouseSubCategory($warehouseID);

                $invCodeDet = $this->Pos_restaurant_model->getInvoiceCode();
                $data['title'] = 'POS';
                $data['extra'] = 'sidebar-collapse fixed';
                $data['refNo'] = $invCodeDet['refCode'];
                $data['menuItems'] = $output;
                $data['menuCategory'] = $output2;
                $data['menuSubCategory'] = $output3;
                $wareHouseData = $this->Pos_restaurant_model->get_wareHouse();
                $data['posData'] = array(
                    'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
                    'counterDet' => '',
                );
                $data['common_data'] = $this->common_data;
                $defaultCustomerType = defaultCustomerType();
                $data['defaultCustomerType'] = !empty($defaultCustomerType) ? $defaultCustomerType : null;
                $data['warehouseID'] = $warehouseID;
                $data['isPriceRequired'] = $this->pos_policy->isPriceRequired();
                $data['tables_list'] = $this->Pos_restaurant_model->get_tableList();
                $data['sampleBillPolicy'] = $this->pos_policy->isSampleBillRequired();

                $this->load->view('system/pos/pos_restaurant_touch', $data);
            } else {
                $this->close_shift_touchWindow();
                $data['error_message'] = 'All counters are closed!';
                $this->load->view('system/pos/pos_restaurant_touch_errors', $data);
            }
        } else {
            $data['error_message'] = 'Outlet is not configured to this user.';
            $this->load->view('system/pos/pos_restaurant_touch_errors', $data);
        }
    }


    function updaterestaurantTable()
    {
        $tableType = $this->input->post('tableType');

        $invoiceID = isPos_invoiceSessionExist();
        if ($invoiceID) {
            $data['tableID'] = $tableType;
            $result = $this->Pos_restaurant_model->update_srp_erp_pos_updaterestaurantTable($data, $invoiceID);
            if ($result) {
                echo json_encode(array('error' => '0', 'message' => 'Table updated'));
            } else {
                echo json_encode(array('error' => '1', 'message' => 'not updated'));
            }
        } else {
            echo json_encode(array('error' => '1', 'message' => 'not updated'));
        }
    }

    function saveMenuSalesItemRemarkes()
    {
        $menuSalesItemID = $this->input->post('itmID');
        $menuSalesID = $this->input->post('invoiceIDMenusales');
        $menuItemRemarkes = $this->input->post('menuItemRemarkes');

        if (!empty($menuItemRemarkes)) {
            $data['remarkes'] = $menuItemRemarkes;

            $result = $this->Pos_restaurant_model->saveMenuSalesItemRemarkes($data, $menuSalesItemID, $menuSalesID);
            if ($result) {
                echo json_encode(array('error' => '0', 'message' => 'Remakes updated', 'invoiceID' => $menuSalesID));
            } else {
                echo json_encode(array('error' => '1', 'message' => 'not updated'));
            }
        } else {
            echo json_encode(array('error' => '1', 'message' => 'Please Enter Remakes'));
        }

    }

    Function get_add_on_list()
    {
        $menuSalesItemID = $this->input->post('menuSalesItemID');
        $data["adonlist"] = $this->Pos_restaurant_model->get_add_on_list($menuSalesItemID);
        $data["menuSalesItemID"] = $this->input->post('menuSalesItemID');
        $this->load->view('system/pos/ajax/ajax_pos_load_add_on_list_view', $data);
    }


    function saveAddon()
    {
        echo json_encode($this->Pos_restaurant_model->saveAddon());

    }

    function updateQty()
    {
        $result = $this->Pos_restaurant_model->updateQty();
        $billNo = isPos_invoiceSessionExist();
        if ($billNo) {
            $this->updateNetTotalForInvoice($billNo);
        }

        echo json_encode($result);
    }

    function save_send_pos_email()
    {
        $this->Pos_restaurant_model->save_send_pos_email();
    }

    function load_void_receipt()
    {
        //$output = $this->Pos_restaurant_model->load_posHoldReceipt();
        $data['holdReceipt'] = null;
        $this->load->view('system/pos/ajax/ajax-restaurant-pos-void', $data);
    }

    function loadVoidOrders()
    {
        $counterData = get_counterData();
        $shiftID = $counterData['shiftID'];
        $companyID = $counterData['companyID'];
        $outletID = $counterData['wareHouseID'];

        $from = $this->input->post('datefrom');
        $fromDate = date('Y-m-d', strtotime($from));
        $to = $this->input->post('dateto');
        $toDate = date('Y-m-d', strtotime($to));


        $this->datatables->select('menuSalesID as menuSalesID, wareHouseAutoID as wareHouseAutoID, subTotal as subTotal, invoiceCode as invoiceCode, DATE_FORMAT(createdDateTime,\'%d-%b-%Y\') as createdDate, createdUserName', false)
            ->from('srp_erp_pos_menusalesmaster')
            ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(menuSalesID)')
            ->add_column('voidBill', '$1', 'btn_voidBill(menuSalesID,\'View\',wareHouseAutoID)')
            //->add_column('subTotal', '$1', 'column_numberFormat(subTotal)')
            ->edit_column('subTotal', '<div>$1</div>', 'column_numberFormat(subTotal)')
            ->where('isHold', 0)
            ->where('isVoid', 0)
            // ->where('DATE_FORMAT( createdDateTime ,\'%Y-%m-%d\')  BETWEEN "' . $fromDate . '" AND "' . $toDate . '"')
            ->where('shiftID', $shiftID)
            ->where('companyID', $companyID)
            ->where('wareHouseAutoID', $outletID);
        echo $this->datatables->generate();
        //echo $this->db->last_query();
    }

    function void_bill()
    {
        echo json_encode($this->Pos_restaurant_model->void_bill());
    }

    function un_void_bill()
    {
        echo json_encode($this->Pos_restaurant_model->un_void_bill());
    }

    function loadVaoidOrderHistory()
    {
        $counterData = get_counterData();
        $shiftID = $counterData['shiftID'];
        $companyID = $counterData['companyID'];
        $outletID = $counterData['wareHouseID'];

        $fromDate = date('Y-m-d', time());
        $toDate = date('Y-m-d', time());

        $this->datatables->select('menuSalesID as menuSalesID, wareHouseAutoID as wareHouseAutoID, invoiceCode as invoiceCode, subTotal as netTotal, DATE_FORMAT(createdDateTime,\'%d-%b-%Y\') as createdDate, createdUserName', false)
            ->from('srp_erp_pos_menusalesmaster')
            ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(menuSalesID)')
            ->add_column('voidBill', '$1', 'btn_voidBillHistory(menuSalesID, wareHouseAutoID)')
            ->add_column('netTotalDisplay', '$1', 'column_numberFormat(netTotal)')
            ->where('isHold', 0)
            ->where('isVoid', 1)
            // ->where('DATE_FORMAT( voidDatetime ,\'%Y-%m-%d\')  BETWEEN "' . $fromDate . '" AND "' . $toDate . '"')
            ->where('shiftID', $shiftID)
            ->where('companyID', $companyID)
            ->where('wareHouseAutoID', $outletID);
        echo $this->datatables->generate();
    }

    function loadPaymentSalesReportPdf()
    {
        $tmpFilterDate = $this->input->post('filterFrom');
        $tmpFilterDateTo = $this->input->post('filterTo');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');
        if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFilterDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $filterDate = $date;
        } else {
            $filterDate = date('Y-m-d');
        }


        if (!empty($tmpFilterDateTo)) {
            $date2 = date('Y-m-d', strtotime($tmpFilterDateTo));
        } else {
            $date2 = date('Y-m-d');
        }

        if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
            $tmpCashier = join(",", $tmpCashierSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }

        if (isset($outlet) && !empty($outlet)) {
            $tmpOutlet = join(",", $outlet);
            $Outlets = $tmpOutlet;
        } else {
            $Outlets = null;
        }

        $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $customerTypeCount = $this->Pos_restaurant_model->get_report_customerTypeCount($filterDate, $date2, $cashier, $Outlets);
        $lessAmounts = $this->Pos_restaurant_model->get_report_lessAmount($filterDate, $date2, $cashier, $Outlets);
        $lessAmounts_promotion = $this->Pos_restaurant_model->get_report_lessAmount_promotion($filterDate, $date2, $cashier, $Outlets);

        /*echo '<pre>';
        print_r($lessAmounts);
        print_r($lessAmounts_promotion);
        echo '</pre>';*/
        $lessAmounts = array_merge($lessAmounts, $lessAmounts_promotion);
        $paymentMethod = $this->Pos_restaurant_model->get_report_paymentMethod($filterDate, $date2, $cashier, $Outlets);

        $data['companyInfo'] = $companyInfo;
        $data['customerTypeCount'] = $customerTypeCount;
        $data['lessAmounts'] = $lessAmounts;
        $data['paymentMethod'] = $paymentMethod;
        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();
        $data['pdf'] = true;

        $html = $this->load->view('system/pos/reports/pos-payment-sales-report', $data, true);
        //$html = $this->load->view('system/pos/pdf/dashboard/pos-payment-sales-report-pdf', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }

    function loadItemizedSalesReportPdf()
    {
        $tmpFromDate = $this->input->post('filterFrom');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d 00:00:00', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d 00:00:00');
        }

        $filterTo = $this->input->post('filterTo');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d 00:00:00', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d 00:00:00');
        }

        if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
            $tmpCashier = join(",", $tmpCashierSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }

        if (isset($outlet) && !empty($outlet)) {
            $tmpOutlet = join(",", $outlet);
            $Outlets = $tmpOutlet;
        } else {
            $Outlets = null;
        }


        $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $itemizedSalesReport = $this->Pos_restaurant_model->get_itemizedSalesReport($dateFrom, $dateTo, $Outlets, $cashier);

        $data['companyInfo'] = $companyInfo;
        $data['itemizedSalesReport'] = $itemizedSalesReport;

        $html = $this->load->view('system/pos/pdf/dashboard/pos-itemized-sales-report-pdf', $data, true);
        //echo $html;
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }

    function LoadDeliveryPersonReport()
    {
        $tmpFromDate = $this->input->post('filterFrom');
        $customerID = $this->input->post('customerID');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        $filterTo = $this->input->post('filterTo');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d');
        }

        if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
            $tmpCashier = join(",", $tmpCashierSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }

        if (isset($outlet) && !empty($outlet)) {
            $tmpOutlet = join(",", $outlet);
            $Outlets = $tmpOutlet;
        } else {
            $Outlets = null;
        }


        $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
        //$persn = $this->Pos_restaurant_model->get_deliveryPerson($customerID);
        $deliveryPersonReport = $this->Pos_restaurant_model->get_deliveryPersonReport($dateFrom, $dateTo, $customerID, $Outlets, $cashier);

        $data['companyInfo'] = $companyInfo;
        //$data['person'] = $persn;
        $data['deliveryPersonReport'] = $deliveryPersonReport;
        /*echo '<pre>';
        print_r($itemizedSalesReport);
        echo '</pre>';*/

        $this->load->view('system/pos/reports/pos-delivery-person-report', $data);
    }

    function LoadDiscountReport()
    {
        $tmpFromDate = $this->input->post('startdate');
        $customerID = $this->input->post('customerID');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d 00:00:00');
        }

        $filterTo = $this->input->post('enddate');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d 23:59:59');
        }

        if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
            $tmpCashier = join(",", $tmpCashierSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }

        if (isset($outlet) && !empty($outlet)) {
            $tmpOutlet = join(",", $outlet);
            $Outlets = $tmpOutlet;
        } else {
            $Outlets = null;
        }

        $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $deliveryPersonReport = $this->Pos_restaurant_model->get_discountReport($dateFrom, $dateTo, $customerID, $Outlets, $cashier);

        $data['cashierTmp'] = get_cashiers();
        $data['cashier'] = $tmpCashierSource;
        $data['companyInfo'] = $companyInfo;
        $data['deliveryPersonReport'] = $deliveryPersonReport;

        $this->load->view('system/pos/reports/pos-discount-report', $data);
    }

    function loadPrintTemplateVoid()
    {
        $invoiceID = $this->input->post('menuSalesID');
        $outletID = $this->input->post('outletID');
        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID_salesDetailReport($invoiceID, $outletID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID, $outletID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['voidBtn'] = true;
        $data['closedBill'] = true;
        $data['auth'] = true;

        //$this->load->view('system/pos/printTemplate/restaurant-pos-thermal-printer', $data);
        $this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer-reprint', $data);
    }

    function loadPrintTemplateVoidHistory()
    {
        $invoiceID = $this->input->post('menuSalesID');
        $outletID = $this->input->post('outletID');
        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID_salesDetailReport($invoiceID, $outletID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID, $outletID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['closedBill'] = true;
        $data['auth'] = true;
        $data['void'] = true;
        $data['voidBtn'] = false;


        $this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer-reprint-void', $data);
    }


    function loadDeliveryPersonReportPdf()
    {
        $tmpFromDate = $this->input->post('filterFrom');
        $customerID = $this->input->post('customerID');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');


        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        $filterTo = $this->input->post('filterTo');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d');
        }

        if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
            $tmpCashier = join(",", $tmpCashierSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }

        if (isset($outlet) && !empty($outlet)) {
            $tmpOutlet = join(",", $outlet);
            $Outlets = $tmpOutlet;
        } else {
            $Outlets = null;
        }


        $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
        //$persn = $this->Pos_restaurant_model->get_deliveryPerson($customerID);
        $deliveryPersonReport = $this->Pos_restaurant_model->get_deliveryPersonReport($dateFrom, $dateTo, $customerID, $Outlets, $cashier);

        $data['companyInfo'] = $companyInfo;
        //$data['person'] = $persn;
        $data['deliveryPersonReport'] = $deliveryPersonReport;

        $html = $this->load->view('system/pos/pdf/dashboard/pos-delivery-person-report-pdf', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }


    function batchJob_checkInvoices($limit = 10)
    {
        $tmpBillID = 0;
        $allInvoices = $this->Pos_restaurant_model->batch_get_srp_erp_pos_menusalesmaster_all($limit);
        $i = 0;
        $outputTxt = '';
        if (!empty($allInvoices)) {
            foreach ($allInvoices as $invoice) {

                $menuSalesID = $invoice['menuSalesID'];
                $netTotal = $invoice['netTotal'];
                $items = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_byMenusalesID($menuSalesID);
                $totalTmp = 0;
                if (!empty($items)) {
                    foreach ($items as $item) {
                        $totalTmp += ($item['menuSalesPrice'] * $item['qty']);
                    }
                }

                if ($netTotal != $totalTmp) {
                    $outputTxt .= 'Bill ID: ' . $menuSalesID . ' Bill Total: ' . $netTotal . ' - Item Total :  ' . $totalTmp . "\n";
                    $updateData[$i]['menuSalesID'] = $menuSalesID;
                    $updateData[$i]['netTotal'] = $totalTmp;

                    if (empty($invoice['paidAmount']) || $invoice['paidAmount'] == null || $invoice['paidAmount'] == 0) {
                        $outputTxt .= "Bill ID: " . $menuSalesID . " Paid Amount: " . $invoice['paidAmount'] . "\n";
                        $updateData[$i]['paidAmount'] = $totalTmp;
                    }
                    if (empty($invoice['cashReceivedAmount']) || $invoice['cashReceivedAmount'] == null || $invoice['cashReceivedAmount'] == 0) {
                        $outputTxt .= "Bill ID: " . $menuSalesID . " cashReceived Amount : " . $invoice['cashReceivedAmount'] . "\n";
                        $updateData[$i]['cashReceivedAmount'] = $totalTmp;
                    }
                }


                $i++;


            }

            if (isset($updateData) && !empty($updateData)) {
                /*** update batch Net Total ** */

                $rows = $this->Pos_restaurant_model->batch_update_srp_erp_pos_menusalesmaster($updateData);
                $outputTxt .= "\n\n\n Updated Affected : " . $rows;
                /*** write log ** */

                $logName = "batch_POS_NetTotal_PaidAmount_" . date("Y-m-d_H-i-s", time()) . ".txt";
                $logPath = UPLOAD_PATH_POS . '/batch_logs/' . $logName;

                $myfile = fopen($logPath, "w") or die("Unable to open file!");
                fwrite($myfile, $outputTxt);
                fclose($myfile);

                echo '<a target="_blank" href="' . base_url() . 'batch_logs/' . $logName . '"> Log File </a>';

            }

        } else {
            echo 'empty';
        }

    }

    function batchJob_checkInvoices_paidZero($limit = 10)
    {
        $allInvoices = $this->Pos_restaurant_model->batch_get_srp_erp_pos_menusalesmaster_all($limit);
        $i = 0;
        $outputTxt = '';
        if (!empty($allInvoices)) {
            foreach ($allInvoices as $invoice) {

                $menuSalesID = $invoice['menuSalesID'];
                $items = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_byMenusalesID($menuSalesID);
                $totalTmp = 0;
                if (!empty($items)) {
                    foreach ($items as $item) {
                        $totalTmp += ($item['menuSalesPrice'] * $item['qty']);
                    }
                }


                if (empty($invoice['cashReceivedAmount']) || $invoice['cashReceivedAmount'] == null || $invoice['cashReceivedAmount'] == 0) {
                    $updateData[$i]['menuSalesID'] = $menuSalesID;
                    $updateData[$i]['cashReceivedAmount'] = $totalTmp;

                    $outputTxt .= "Bill ID: " . $menuSalesID . " Paid Amount: " . $invoice['paidAmount'] . "\n";
                }

                $i++;


            }

            if (isset($updateData) && !empty($updateData)) {
                /*** update batch Net Total ** */

                $rows = $this->Pos_restaurant_model->batch_update_srp_erp_pos_menusalesmaster($updateData);
                $outputTxt .= "\n\n\n Updated Affected : " . $rows;
                /*** write log ** */

                $logName = "batch_POS_PaidAmount_null_" . date("Y-m-d_H-i-s", time()) . ".txt";
                $logPath = UPLOAD_PATH_POS . '/batch_logs/' . $logName;

                $myfile = fopen($logPath, "w") or die("Unable to open file!");
                fwrite($myfile, $outputTxt);
                fclose($myfile);

                echo '<a target="_blank" href="' . base_url() . 'batch_logs/' . $logName . '"> Log File </a>';

            }

        } else {
            echo 'empty';
        }

    }

    function batchJob_checkInvoices_view($limit = 10)
    {

        $tmpBillID = 0;
        $allInvoices = $this->Pos_restaurant_model->batch_get_srp_erp_pos_menusalesmaster_all($limit);
        $i = 0;
        $outputTxt = '';
        if (!empty($allInvoices)) {
            $updateData = array();
            foreach ($allInvoices as $invoice) {

                $menuSalesID = $invoice['menuSalesID'];
                $netTotal = $invoice['netTotal'];
                $items = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_byMenusalesID($menuSalesID);
                $totalTmp = 0;
                if (!empty($items)) {
                    foreach ($items as $item) {
                        $totalTmp += ($item['menuSalesPrice'] * $item['qty']);
                    }
                }

                if ($netTotal != $totalTmp) {
                    $outputTxt .= 'Bill ID: ' . $menuSalesID . ' Bill Total: ' . $netTotal . ' - Item Total :  ' . $totalTmp . "\n";
                    $updateData[$i]['menuSalesID'] = $menuSalesID;
                    $updateData[$i]['netTotal'] = $totalTmp;

                    if (empty($invoice['paidAmount']) || $invoice['paidAmount'] == null || $invoice['paidAmount'] == 0) {
                        $outputTxt .= "Bill ID: " . $menuSalesID . " Paid Amount: " . $invoice['paidAmount'] . "\n";
                        $updateData[$i]['paidAmount'] = $totalTmp;
                    }
                    if (empty($invoice['cashReceivedAmount']) || $invoice['cashReceivedAmount'] == null || $invoice['cashReceivedAmount'] == 0) {
                        $outputTxt .= "Bill ID: " . $menuSalesID . " cashReceived Amount : " . $invoice['cashReceivedAmount'] . "\n";
                        $updateData[$i]['cashReceivedAmount'] = $totalTmp;
                    }
                }


                $i++;


            }

            echo '<pre>';
            print_r($outputTxt);


        } else {
            echo 'empty';
        }

    }

    /**
     *
     * To update the menu ID in pack table this impact the product mix report.
     *
     */

    function batchJob_updatePackMenuID()
    {

        echo '<pre>';
        echo "Batch started <br/><br/>";
        /** get all packs */
        $q = 'SELECT vp.valuePackDetailID, vp.menuSalesID, vp.menuID vpMenuID, vp.menuPackItemID, pg.menuID FROM srp_erp_pos_valuepackdetail vp LEFT JOIN srp_erp_pos_packgroupdetail pg ON vp.menuPackItemID = pg.packgroupdetailID  WHERE ISNULL(vp.menuID)';
        $r = $this->db->query($q)->result_array();

        $i = 0;
        $updateData = array();
        $outputTxt = '';
        if (!empty($r)) {
            foreach ($r as $val) {

                if (!empty($val['menuID'])) {
                    $updateData[$i]['valuePackDetailID'] = $val['valuePackDetailID'];
                    $updateData[$i]['menuID'] = $val['menuID'];

                    $outputTxt .= "Bill ID: " . $val['menuSalesID'] . " updated menuID: " . $val['menuPackItemID'] . "\n";
                } else {
                    $outputTxt .= "Bill ID: " . $val['menuSalesID'] . " menuID NULL \n";
                }


                $i++;


            }


            if (isset($updateData) && !empty($updateData)) {
                /*** update batch Net Total ** */

                $this->db->update_batch('srp_erp_pos_valuepackdetail', $updateData, 'valuePackDetailID');
                $row = $this->db->affected_rows();

                $outputTxt .= "\n\n\n\n\n Number of row affected: " . $row . " \n\n";
                $outputTxt .= "\n\n query: " . $this->db->last_query() . " \n End.";

                /*** write log ** */

                $logName = "batch_POS_packMenu_" . date("Y-m-d_H-i-s", time()) . ".txt";
                $logPath = UPLOAD_PATH_POS . '/batch_logs/' . $logName;

                $myfile = fopen($logPath, "w") or die("Unable to open file!");
                fwrite($myfile, $outputTxt);
                fclose($myfile);

                echo '<a target="_blank" href="' . base_url() . 'batch_logs/' . $logName . '"> Log File </a><br/><br/>';

            }

            print_r($outputTxt);

        } else {
            echo 'empty';
        }

        echo "<br/><br/>Process Completed";
    }


    function loadProductMix()
    {
        $tmpFromDate = $this->input->post('filterFrom');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        $filterTo = $this->input->post('filterTo');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d');
        }

        if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
            $tmpCashier = join(",", $tmpCashierSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }

        if (isset($outlet) && !empty($outlet)) {
            $tmpOutlet = join(",", $outlet);
            $Outlets = $tmpOutlet;
        } else {
            $Outlets = null;
        }


        $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $productMix_menuItem = $this->Pos_restaurant_model->productMix_menuItem($dateFrom, $dateTo, $Outlets, $cashier);
        $get_packs_sales = $this->Pos_restaurant_model->get_productMixPacks_sales($dateFrom, $dateTo, $Outlets, $cashier);

        if (!empty($productMix_menuItem)) {
            $tmpArray2 = array();
            foreach ($productMix_menuItem as $get_packs_sale) {
                $tmpArray2[$get_packs_sale['menuMasterID']]['menuID'] = $get_packs_sale['menuMasterID'];
                $tmpArray2[$get_packs_sale['menuMasterID']]['menuMasterDescription'] = $get_packs_sale['menuMasterDescription'] . ' ' . $get_packs_sale['menuSize'];
                $tmpArray2[$get_packs_sale['menuMasterID']]['qty'] = $get_packs_sale['qty'];
            }
            $productMix_menuItem = $tmpArray2;
        }

        if (!empty($get_packs_sales)) {
            $tmpArray = array();
            foreach ($get_packs_sales as $get_packs_sale) {
                $tmpArray[$get_packs_sale['menuID']]['menuID'] = $get_packs_sale['menuID'];
                $tmpArray[$get_packs_sale['menuID']]['menuMasterDescription'] = $get_packs_sale['menuMasterDescription'] . ' ' . $get_packs_sale['menuSize'];
                $tmpArray[$get_packs_sale['menuID']]['qty'] = $get_packs_sale['qty'];
            }
            $get_packs_sales = $tmpArray;
        }

        // $data['companyInfo'] = $companyInfo;

        $m = array_merge($productMix_menuItem, $get_packs_sales);

        /*Group by script from http://stackoverflow.com/questions/12706359/php-array-group*/
        $result = array();
        foreach ($m as $data) {
            $id = $data['menuID'];
            if (isset($result[$id])) {
                $result[$id][] = $data;
            } else {
                $result[$id] = array($data);
            }
        }
        $data['companyInfo'] = $companyInfo;
        $data['productMix'] = $result;
        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();


        $this->load->view('system/pos/reports/pos-product-mix.php', $data);
    }


    function updateDiscount()
    {
        $menuSalesID = isPos_invoiceSessionExist();
        $data['discountPer'] = !empty($this->input->get('discount')) ? $this->input->get('discount') : 0;
        if ($menuSalesID) {
            if ($data['discountPer'] > -1) {
                $this->Pos_restaurant_model->update_srp_erp_pos_menusalesmaster($data, $menuSalesID);
            }
        }
        echo json_encode(array('billNo' => $menuSalesID, 'discount' => $data['discountPer']));
    }


    function loadFranchiseReport()
    {
        $tmpFromDate = $this->input->post('filterFrom');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        $filterTo = $this->input->post('filterTo');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d');
        }

        if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
            $tmpCashier = join(",", $tmpCashierSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }

        if (isset($outlet) && !empty($outlet)) {
            $tmpOutlet = join(",", $outlet);
            $Outlets = $tmpOutlet;
        } else {
            $Outlets = null;
        }


        $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
        //$persn = $this->Pos_restaurant_model->get_deliveryPerson($customerID);
        $franchiseReport = $this->Pos_restaurant_model->get_franchiseReport($dateFrom, $dateTo, $Outlets, $cashier);

        $data['companyInfo'] = $companyInfo;
        //$data['person'] = $persn;
        $data['franchiseReport'] = $franchiseReport;

        // $data['companyInfo'] = $companyInfo;
        $this->load->view('system/pos/reports/pos-franchise-report', $data);
    }

    function loadFranchiseReportPdf()
    {
        $tmpFromDate = $this->input->post('filterFrom');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        $filterTo = $this->input->post('filterTo');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d');
        }

        if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
            $tmpCashier = join(",", $tmpCashierSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }

        if (isset($outlet) && !empty($outlet)) {
            $tmpOutlet = join(",", $outlet);
            $Outlets = $tmpOutlet;
        } else {
            $Outlets = null;
        }


        $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
        //$persn = $this->Pos_restaurant_model->get_deliveryPerson($customerID);
        $franchiseReport = $this->Pos_restaurant_model->get_franchiseReport($dateFrom, $dateTo, $Outlets, $cashier);

        $data['companyInfo'] = $companyInfo;
        //$data['person'] = $persn;
        $data['franchiseReport'] = $franchiseReport;

        $html = $this->load->view('system/pos/pdf/dashboard/pos-franchise-report-pdf', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }


    function loadProductMixReportPdf()
    {
        $tmpFromDate = $this->input->post('filterFrom');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        $filterTo = $this->input->post('filterTo');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d');
        }

        if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
            $tmpCashier = join(",", $tmpCashierSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }

        if (isset($outlet) && !empty($outlet)) {
            $tmpOutlet = join(",", $outlet);
            $Outlets = $tmpOutlet;
        } else {
            $Outlets = null;
        }

        $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $productMix_menuItem = $this->Pos_restaurant_model->productMix_menuItem($dateFrom, $dateTo, $Outlets, $cashier);
        $get_packs_sales = $this->Pos_restaurant_model->get_productMixPacks_sales($dateFrom, $dateTo, $Outlets, $cashier);

        if (!empty($productMix_menuItem)) {
            $tmpArray2 = array();
            foreach ($productMix_menuItem as $get_packs_sale) {
                $tmpArray2[$get_packs_sale['menuMasterID']]['menuID'] = $get_packs_sale['menuMasterID'];
                $tmpArray2[$get_packs_sale['menuMasterID']]['menuMasterDescription'] = $get_packs_sale['menuMasterDescription'] . ' ' . $get_packs_sale['menuSize'];
                $tmpArray2[$get_packs_sale['menuMasterID']]['qty'] = $get_packs_sale['qty'];
            }
            $productMix_menuItem = $tmpArray2;
        }

        if (!empty($get_packs_sales)) {
            $tmpArray = array();
            foreach ($get_packs_sales as $get_packs_sale) {
                $tmpArray[$get_packs_sale['menuID']]['menuID'] = $get_packs_sale['menuID'];
                $tmpArray[$get_packs_sale['menuID']]['menuMasterDescription'] = $get_packs_sale['menuMasterDescription'] . ' ' . $get_packs_sale['menuSize'];
                $tmpArray[$get_packs_sale['menuID']]['qty'] = $get_packs_sale['qty'];
            }
            $get_packs_sales = $tmpArray;
        }

        // $data['companyInfo'] = $companyInfo;

        $m = array_merge($productMix_menuItem, $get_packs_sales);

        /*Group by script from http://stackoverflow.com/questions/12706359/php-array-group*/
        $result = array();
        foreach ($m as $data) {
            $id = $data['menuID'];
            if (isset($result[$id])) {
                $result[$id][] = $data;
            } else {
                $result[$id] = array($data);
            }
        }
        $data['companyInfo'] = $companyInfo;
        $data['productMix'] = $result;

        $html = $this->load->view('system/pos/pdf/dashboard/pos-product-mix-report-pdf', $data, true);
        // echo $html;exit;
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }

    function updateCurrentMenuWAC()
    {
        echo json_encode($this->Pos_restaurant_model->updateCurrentMenuWAC());
    }

    function updateSendToKitchen()
    {
        echo json_encode($this->Pos_restaurant_model->updateSendToKitchen());
    }


    function loadPaymentSalesReportAdmin()
    {
        $_POST['outletID'] = $this->input->post('outletID_f');
        $data['outletID'] = $this->input->post('outletID_f');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpCashierSource = $this->input->post('cashier');
        $outletIDs = $this->input->post('outletID_f');


        if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
            $filterDate = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
        } else {
            $filterDate = date('Y-m-d 00:00:00');
        }


        if (!empty($tmpFilterDateTo)) {
            $date2 = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
        } else {
            $date2 = date('Y-m-d 23:59:59');
        }

        if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
            $tmpCashier = join(",", $tmpCashierSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }

        if (isset($outletIDs) && !empty($outletIDs)) {
            $outlet = join(",", $outletIDs);
            $outlets = $outlet;
        } else {
            $outlets = null;
        }


        $lessAmounts = $this->Pos_restaurant_model->get_report_lessAmount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_promotion = $this->Pos_restaurant_model->get_report_lessAmount_promotion_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_discounts = $this->Pos_restaurant_model->get_report_salesReport_discount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_discounts_item_wise = $this->Pos_restaurant_model->get_report_salesReport_discount_item_wise_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_discountsJavaApp = $this->Pos_restaurant_model->get_report_salesReport_javaAppDiscount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmountsAll = array_merge($lessAmounts_discounts, $lessAmounts, $lessAmounts_promotion, $lessAmounts_discountsJavaApp, $lessAmounts_discounts_item_wise);

        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $data['paymentMethod'] = $this->Pos_restaurant_model->get_report_paymentMethod_admin($filterDate, $date2, $cashier, $outlets);
        //$data['customerTypeCount'] = $this->Pos_restaurant_model->get_report_customerTypeCount_admin($filterDate, $date2, $cashier, $outlets);
        $data['customerTypeCount'] = $this->Pos_restaurant_model->get_report_customerTypeCount_2_admin($filterDate, $date2, $cashier, $outlets);
        $data['lessAmounts'] = $lessAmountsAll;


        // var_dump($lessAmountsAll);
        $data['totalSales'] = $this->Pos_restaurant_model->get_report_salesReport_totalSales_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalTaxes'] = $this->Pos_restaurant_model->get_report_salesReport_totalTaxes_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalServiceCharge'] = $this->Pos_restaurant_model->get_report_salesReport_ServiceCharge_admin($filterDate, $date2, $cashier, $outlets);
        $data['giftCardTopUp'] = $this->Pos_restaurant_model->get_report_giftCardTopUp_admin($filterDate, $date2, $cashier, $outlets);
        $data['voidBills'] = $this->Pos_restaurant_model->get_report_voidBills_admin($filterDate, $date2, $cashier, $outlets);
        $data['creditSales'] = $this->Pos_restaurant_model->get_report_creditSales($filterDate, $date2, $cashier, $outlets);

        //var_dump($data['voidBills']);

        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();


        $this->load->view('system/pos/reports/pos-payment-sales-report-admin', $data);
    }

    function loadPaymentSalesReport_terminal()
    {

        $_POST['outletID'] = $this->input->post('outletID_f');
        $data['outletID'] = $this->input->post('outletID_f');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpCashierSource = $this->input->post('cashier');
        $outletIDs = $this->input->post('outletID_f');


        if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
            $filterDate = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
        } else {
            $filterDate = date('Y-m-d 00:00:00');
        }


        if (!empty($tmpFilterDateTo)) {
            $date2 = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
        } else {
            $date2 = date('Y-m-d 23:59:59');
        }

        if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
            $tmpCashier = join(",", $tmpCashierSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }

        if (isset($outletIDs) && !empty($outletIDs)) {
            $outlet = join(",", $outletIDs);
            $outlets = $outlet;
        } else {
            $outlets = null;
        }


        $lessAmounts = $this->Pos_restaurant_model->get_report_lessAmount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_promotion = $this->Pos_restaurant_model->get_report_lessAmount_promotion_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_discounts = $this->Pos_restaurant_model->get_report_salesReport_discount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_discounts_item_wise = $this->Pos_restaurant_model->get_report_salesReport_discount_item_wise_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_discountsJavaApp = $this->Pos_restaurant_model->get_report_salesReport_javaAppDiscount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmountsAll = array_merge($lessAmounts_discounts, $lessAmounts, $lessAmounts_promotion, $lessAmounts_discountsJavaApp, $lessAmounts_discounts_item_wise);

        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $data['paymentMethod'] = $this->Pos_restaurant_model->get_report_paymentMethod_admin($filterDate, $date2, $cashier, $outlets);
        //$data['customerTypeCount'] = $this->Pos_restaurant_model->get_report_customerTypeCount_admin($filterDate, $date2, $cashier, $outlets);
        $data['customerTypeCount'] = $this->Pos_restaurant_model->get_report_customerTypeCount_2_admin($filterDate, $date2, $cashier, $outlets);
        $data['lessAmounts'] = $lessAmountsAll;


        // var_dump($lessAmountsAll);
        $data['totalSales'] = $this->Pos_restaurant_model->get_report_salesReport_totalSales_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalTaxes'] = $this->Pos_restaurant_model->get_report_salesReport_totalTaxes_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalServiceCharge'] = $this->Pos_restaurant_model->get_report_salesReport_ServiceCharge_admin($filterDate, $date2, $cashier, $outlets);
        $data['giftCardTopUp'] = $this->Pos_restaurant_model->get_report_giftCardTopUp_admin($filterDate, $date2, $cashier, $outlets);
        $data['voidBills'] = $this->Pos_restaurant_model->get_report_voidBills_admin($filterDate, $date2, $cashier, $outlets);
        $data['creditSales'] = $this->Pos_restaurant_model->get_report_creditSales($filterDate, $date2, $cashier, $outlets);

        //var_dump($data['voidBills']);

        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();


        $this->load->view('system/pos/reports/pos-payment-sales-report-admin', $data);
    }

    function clickPowerOff()
    {
        $holdBillCount = get_pos_holdBillCount();
        if ($holdBillCount) {
            $holdBillCount = $holdBillCount == 1 ? $holdBillCount . ' bill' : $holdBillCount . ' bills';
            echo json_encode(array('error' => 1, 'message' => "Please close all the pending hold bills. <br/><br/> $holdBillCount  found!"));
        } else {
            echo json_encode(array('error' => 0, 'message' => "clear"));
        }
    }

    function get_outlet_cashier()
    {
        echo json_encode($this->Pos_restaurant_model->get_outlet_cashier());
    }

    function get_gpos_outlet_cashier()
    {
        echo json_encode($this->Pos_restaurant_model->get_gpos_outlet_cashier());
    }

    function get_outlet_cashier_itemized()
    {
        echo json_encode($this->Pos_restaurant_model->get_outlet_cashier_itemized());
    }

    function get_outlet_cashier_Promotions()
    {
        echo json_encode($this->Pos_restaurant_model->get_outlet_cashier_Promotions());
    }

    function get_outlet_cashier_productmix()
    {
        echo json_encode($this->Pos_restaurant_model->get_outlet_cashier_productmix());
    }

    function get_outlet_cashier_franchise()
    {
        echo json_encode($this->Pos_restaurant_model->get_outlet_cashier_franchise());
    }


    function load_pos_detail_sales_report()
    {
        $_POST['outletID'] = $this->input->post('outletID_f');
        $data['outletID'] = $this->input->post('outletID_f');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpCashierSource = $this->input->post('cashier');
        $outletIDs = $this->input->post('outletID_f');

        if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
            $filterDate = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
        } else {
            $filterDate = date('Y-m-d 00:00:00');
        }

        if (!empty($tmpFilterDateTo)) {
            $date2 = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
        } else {
            $date2 = date('Y-m-d 23:59:59');
        }

        if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
            $tmpCashier = join(",", $tmpCashierSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }

        if (isset($outletIDs) && !empty($outletIDs)) {
            $outlet = join(",", $outletIDs);
            $outlets = $outlet;
        } else {
            $outlets = null;
        }

        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();

        $data['paymentglConfigMaster'] = $this->Pos_restaurant_model->get_srp_erp_pos_paymentglconfigmaster();
        $data['recordDetail'] = $this->Pos_restaurant_model->get_report_salesDetailReport($filterDate, $date2, $cashier, $outlets);

        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();

        $this->load->view('system/pos/reports/pos-payment-sales-detail-report', $data);
    }


    function loadPrintTemplate_salesDetailReport()
    {
        $invoiceID = isPos_invoiceSessionExist();
        $invoiceID = ($invoiceID) ? $invoiceID : $this->input->post('invoiceID');
        $outletID = $this->input->post('outletID');
        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID_salesDetailReport($invoiceID, $outletID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster_salesDetailReport($invoiceID, $outletID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['email'] = true;
        $data['wifi'] = false;
        $data['auth'] = false;
        $template = get_print_template();
        //$this->load->view('system/pos/printTemplate/restaurant-pos-thermal-printer', $data);
        //$this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer', $data);
        //$this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer-detail-reportView', $data);
        $this->load->view($template, $data);
    }


    function createShiftDoubleEntries($shiftID)
    {
        $this->Pos_restaurant_accounts->update_revenue_generalLedger($shiftID);
        /** 2. BANK OR CASH */
        $this->Pos_restaurant_accounts->update_bank_cash_generalLedger($shiftID);
        /** 3. COGS */
        $this->Pos_restaurant_accounts->update_cogs_generalLedger($shiftID);
        /** 4. INVENTORY */
        $this->Pos_restaurant_accounts->update_inventory_generalLedger($shiftID);
        /** 5. TAX */
        $this->Pos_restaurant_accounts->update_tax_generalLedger($shiftID);
        /** 6. COMMISSION EXPENSE  */
        $this->Pos_restaurant_accounts->update_commissionExpense_generalLedger($shiftID);
        /** 7. COMMISSION PAYABLE */
        $this->Pos_restaurant_accounts->update_commissionPayable_generalLedger($shiftID);
        /** 8. ROYALTY PAYABLE */
        $this->Pos_restaurant_accounts->update_royaltyPayable_generalLedger($shiftID);
        /** 9. ROYALTY EXPENSES */
        $this->Pos_restaurant_accounts->update_royaltyExpenses_generalLedger($shiftID);
        /** 10. SERVICE CHARGE */
        $this->Pos_restaurant_accounts->update_serviceCharge_generalLedger($shiftID);
        /** 11. CREDIT CUSTOMER PAYMENTS - CREDIT SALES HANDLED SEPARATELY  */
        //$this->Pos_restaurant_accounts->update_creditSales_generalLedger($shiftID);
        /** BANK LEDGER UPDATE  */
        $this->Pos_restaurant_accounts->update_bankLedger($shiftID);
        /** ITEM  LEDGER UPDATE  */
        $this->Pos_restaurant_accounts->update_itemLedger($shiftID);
        /** CREDIT SALES ENTRIES  */
        $this->Pos_restaurant_accounts->pos_credit_sales_entries($shiftID);
        $this->Pos_restaurant_accounts->pos_credit_sales_entries_manual($shiftID);
    }


    /**
     * =========================================== FIX ===========================================
     * NON CREDIT SALES - GENERAL LEDGER
     * Developed on 03-Jan-2018 Requested by Hisham to fix the issue
     */
    function batch_create_shift_general_ledger_entries($shiftID)
    {
        /** 1. REVENUE */
        $this->Pos_restaurant_accounts_gl_fix->update_revenue_generalLedger($shiftID);
        /** 2. BANK OR CASH */
        $this->Pos_restaurant_accounts_gl_fix->update_bank_cash_generalLedger($shiftID);
        /** 3. COGS */
        $this->Pos_restaurant_accounts_gl_fix->update_cogs_generalLedger($shiftID);
        /** 4. INVENTORY */
        $this->Pos_restaurant_accounts_gl_fix->update_inventory_generalLedger($shiftID);
        /** 5. TAX */
        $this->Pos_restaurant_accounts_gl_fix->update_tax_generalLedger($shiftID);
        /** 6. COMMISSION EXPENSE  */
        $this->Pos_restaurant_accounts_gl_fix->update_commissionExpense_generalLedger($shiftID);
        /** 7. COMMISSION PAYABLE */
        $this->Pos_restaurant_accounts_gl_fix->update_commissionPayable_generalLedger($shiftID);
        /** 8. ROYALTY PAYABLE */
        $this->Pos_restaurant_accounts_gl_fix->update_royaltyPayable_generalLedger($shiftID);
        /** 9. ROYALTY EXPENSES */
        $this->Pos_restaurant_accounts_gl_fix->update_royaltyExpenses_generalLedger($shiftID);
        /** 10. SERVICE CHARGE */
        $this->Pos_restaurant_accounts_gl_fix->update_serviceCharge_generalLedger($shiftID);

        /** BANK LEDGER UPDATE  */
        $this->Pos_restaurant_accounts_gl_fix->update_bankLedger($shiftID);

    }


    /**
     *  =========================================== FIX ===========================================
     * CREDIT SALES - GENERAL LEDGER
     * Developed on 03-Jan-2018 Requested by Hisham to fix the issue
     */
    function batch_create_shift_general_ledger_entries_creditSales($shiftID, $billNo)
    {

        /** 0. CUSTOMER INVOICE - Credit Sales Entries  */
        $this->Pos_restaurant_accounts_gl_fix->pos_generate_invoices_bill($shiftID, $billNo);
        /** 1. CREDIT SALES  - REVENUE */
        $this->Pos_restaurant_accounts_gl_fix->update_revenue_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 2. CREDIT SALES  - COGS */
        $this->Pos_restaurant_accounts_gl_fix->update_cogs_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 3. CREDIT SALES  - INVENTORY */
        $this->Pos_restaurant_accounts_gl_fix->update_inventory_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 4.  CREDIT SALES - TAX */
        $this->Pos_restaurant_accounts_gl_fix->update_tax_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 5.  CREDIT SALES - COMMISSION EXPENSE  */
        $this->Pos_restaurant_accounts_gl_fix->update_commissionExpense_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 6.  CREDIT SALES - COMMISSION PAYABLE */
        $this->Pos_restaurant_accounts_gl_fix->update_commissionPayable_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 7.  CREDIT SALES - ROYALTY PAYABLE */
        $this->Pos_restaurant_accounts_gl_fix->update_royaltyPayable_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 8.  CREDIT SALES - ROYALTY EXPENSES */
        $this->Pos_restaurant_accounts_gl_fix->update_royaltyExpenses_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 9. CREDIT SALES -  SERVICE CHARGE */
        $this->Pos_restaurant_accounts_gl_fix->update_serviceCharge_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 10. CREDIT SALES -  CREDIT CUSTOMER PAYMENTS */
        $this->Pos_restaurant_accounts_gl_fix->update_creditSales_generalLedger_credit_sales_bill($shiftID, $billNo);

    }

    /**
     *  =========================================== FIX ===========================================
     * NON CREDIT SALES  - ITEM LEDGER
     * Developed on 03-Jan-2018 Requested by Hisham to fix the issue
     */

    function batch_create_shift_item_ledger_entries($shiftID)
    {
        $this->Pos_restaurant_accounts_gl_fix->batch_update_itemLedger($shiftID);
    }

    /**
     *  =========================================== FIX ===========================================
     *  CREDIT SALES  - ITEM LEDGER
     * Developed on 03-Jan-2018 Requested by Hisham to fix the issue
     */
    function batch_create_shift_item_ledger_entries_creditSales($shiftID, $billNo)
    {
        $this->Pos_restaurant_accounts_gl_fix->batch_update_itemLedger_creditSales($shiftID, $billNo);
    }

    function update_tableOrder()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_diningtables');
        $this->db->where('tmp_menuSalesID', $this->input->post('menuSalesID'));
        $diningTable = $this->db->get()->row_array();
        $menuSalesID = $diningTable['tmp_menuSalesID'];
        if (empty($diningTable)) {
            $validation = $this->Pos_restaurant_model->validate_tableOrder();
            if ($validation) {
                /** update table status */
                $result = $this->Pos_restaurant_model->update_diningTableStatus();

                if ($result) {
                    /** update it to menu sales master table  */
                    $this->Pos_restaurant_model->update_menuSalesMasterTableID();
                }

                /** get active ongoing tables */
                $result = $this->Pos_restaurant_model->get_diningTableUsed();
                $output = array('error' => 0, 'result' => $result, 'show_waiter' => 1, 'packs' => 0, 'menuSalesID' => $menuSalesID);

            } else {
                $output = array('error' => 1, 'message' => 'This table is already assigned to a bill, please select the different table. ', 'result' => null, 'show_waiter' => 1, 'packs' => 0, 'menuSalesID' => $menuSalesID);
            }
        } else {
            $this->db->select('*');
            $this->db->from('srp_erp_pos_diningtables');
            $this->db->where('diningTableAutoID', $this->input->post('id'));
            $this->db->where('status', 1);
            $diningTable_tmp = $this->db->get()->row_array();
            $show_waiter = !empty($diningTable_tmp) ? 1 : 0;


            if ($show_waiter) {
                $crewID = !empty($diningTable_tmp['tmp_crewID']) ? $diningTable_tmp['tmp_crewID'] : 0;
                $numOfPacks = !empty($diningTable_tmp['tmp_numberOfPacks']) ? $diningTable_tmp['tmp_numberOfPacks'] : 0;
                $output = array('error' => 2, 'message' => 'This bill already assigned to a ' . $diningTable_tmp['diningTableDescription'], 'show_waiter' => $show_waiter, 'crewID' => $crewID, 'packs' => $numOfPacks, 'menuSalesID' => $menuSalesID);
            } else {
                $crewID = !empty($diningTable['tmp_crewID']) ? $diningTable['tmp_crewID'] : 0;
                $numOfPacks = !empty($diningTable['tmp_numberOfPacks']) ? $diningTable['tmp_numberOfPacks'] : 0;
                $output = array('error' => 3, 'message' => 'switch Table : ' . $diningTable['diningTableDescription'], 'show_waiter' => $show_waiter, 'id' => $this->input->post('id'), 'menuSalesID' => $this->input->post('menuSalesID'), 'fromKey' => $diningTable['diningTableAutoID'], 'crewID' => $crewID, 'packs' => $numOfPacks, 'menuSalesID' => $menuSalesID);
            }
        }
        echo json_encode($output);
    }

    function switchTable()
    {
        $this->db->where('diningTableAutoID', $this->input->post('id'));
        $this->db->update('srp_erp_pos_diningtables', array('status' => 1, 'tmp_menuSalesID' => $this->input->post('menuSalesID'), 'tmp_crewID' => $this->input->post('crewID'), 'tmp_numberOfPacks' => $this->input->post('packs')));

        $this->db->where('diningTableAutoID', $this->input->post('fromKey'));
        $this->db->update('srp_erp_pos_diningtables', array('status' => 0, 'tmp_menuSalesID' => null, 'tmp_crewID' => null, 'tmp_numberOfPacks' => 0));

        $this->db->where('menuSalesID', $this->input->post('menuSalesID'));
        $this->db->update('srp_erp_pos_menusalesmaster', array('tableID' => $this->input->post('id'), 'waiterID' => $this->input->post('crewID'), 'numberOfPacks' => $this->input->post('packs')));

        echo json_encode(array('error' => 0, 'message' => 'table switched'));
    }

    function update_waiter_info()
    {
        $crewID = $this->input->post('crewID');
        $tableID = $this->input->post('tableID');
        $numberOfPacks = $this->input->post('numberOfPack');

        $this->db->select('*');
        $this->db->from('srp_erp_pos_diningtables');
        $this->db->where('diningTableAutoID', $tableID);
        $menuSalesID = $this->db->get()->row('tmp_menuSalesID');

        $this->db->where('diningTableAutoID', $tableID);
        $this->db->update('srp_erp_pos_diningtables', array('tmp_crewID' => $crewID, 'tmp_numberOfPacks' => $numberOfPacks));

        $this->db->where('menuSalesID', $menuSalesID);
        $this->db->update('srp_erp_pos_menusalesmaster', array('waiterID' => $crewID, 'numberOfPacks' => $numberOfPacks));
        echo json_encode(array('error' => 0, 'message' => 'crew updated'));
    }

    function refreshDiningTables()
    {
        /** get active ongoing tables */
        $result = $this->Pos_restaurant_model->get_diningTableUsed();
        $output = array('error' => 0, 'result' => $result);

        echo json_encode($output);
    }


    /**
     *  Script requested by Hisham on 2018-02-18
     *  Sesatha Cost entry fixes
     *  Developed by Shafri
     *
     *  To Fix INVENTORY | COGS | ITEM LEDGER
     *  Logic
     *
     *  Menu sales item detail cost updated manually by hisham.
     *
     *
     */

    function batch_doubleEntry_manualUpdate_after_credit_sales($shiftID)
    {
        echo '<pre>';
        /**  GENERAL LEDGER
         * /pos_restaurant/batch_create_shift_general_ledger_entries/shiftID
         */

        /** 3. COGS |  4. INVENTORY  */
        $this->Pos_restaurant_accounts_gl_fix->update_cogs_generalLedger($shiftID, true);
        $this->Pos_restaurant_accounts_gl_fix->update_inventory_generalLedger($shiftID, true);


        /** GENERAL LEDGER : CREDIT SALES
         * /pos_restaurant/batch_create_shift_general_ledger_entries_creditSales/shiftID/billNo
         */
        $log = true;
        $this->Pos_restaurant_accounts->update_inventory_generalLedger_credit_sales($shiftID, true, $log);
        $this->Pos_restaurant_accounts->update_cogs_generalLedger_credit_sales($shiftID, true, $log);


        /**  ITEM LEDGER : CREDIT SALES
         * /pos_restaurant/batch_create_shift_item_ledger_entries_creditSales/shiftID/billNo /*
         */
        $this->Pos_restaurant_accounts->update_itemLedger_credit_sales($shiftID, true);


        /**--  ITEM LEDGER
         * /pos_restaurant/batch_create_shift_item_ledger_entries/shiftID
         */
        $this->Pos_restaurant_accounts_gl_fix->batch_update_itemLedger($shiftID, true);


        echo '</pre>';
    }

    function batch_doubleEntry_manualUpdate_before_credit_sales($shiftID)
    {
        echo '<pre>';
        /**  GENERAL LEDGER
         * /pos_restaurant/batch_create_shift_general_ledger_entries/shiftID
         */

        /** 3. COGS |  4. INVENTORY  */
        $this->Pos_restaurant_accounts_gl_fix->update_cogs_generalLedger($shiftID, false);
        $this->Pos_restaurant_accounts_gl_fix->update_inventory_generalLedger($shiftID, false);


        /** GENERAL LEDGER : CREDIT SALES
         * /pos_restaurant/batch_create_shift_general_ledger_entries_creditSales/shiftID/billNo
         */
        $log = true;
        $this->Pos_restaurant_accounts->update_inventory_generalLedger_credit_sales($shiftID, false, $log);
        $this->Pos_restaurant_accounts->update_cogs_generalLedger_credit_sales($shiftID, false, $log);


        /**  ITEM LEDGER : CREDIT SALES
         * /pos_restaurant/batch_create_shift_item_ledger_entries_creditSales/shiftID/billNo /*
         */
        $this->Pos_restaurant_accounts->update_itemLedger_credit_sales($shiftID, false);


        /**--  ITEM LEDGER
         * /pos_restaurant/batch_create_shift_item_ledger_entries/shiftID
         */
        $this->Pos_restaurant_accounts_gl_fix->batch_update_itemLedger($shiftID, false);

        echo '</pre>';

    }

    /** Created on 2018-02-09 */
    function batch_doubleEntry_manualUpdate_all_in_one($shiftID)
    {
        $this->db->trans_start();
        /** 1. REVENUE */
        $this->Pos_restaurant_accounts->update_revenue_generalLedger($shiftID);

        /** 2. BANK OR CASH */
        $this->Pos_restaurant_accounts->update_bank_cash_generalLedger($shiftID);

        /** 3. COGS */
        $this->Pos_restaurant_accounts->update_cogs_generalLedger($shiftID);

        /** 4. INVENTORY */
        $this->Pos_restaurant_accounts->update_inventory_generalLedger($shiftID);

        /** 5. TAX */
        $this->Pos_restaurant_accounts->update_tax_generalLedger($shiftID);

        /** 6. COMMISSION EXPENSE  */
        $this->Pos_restaurant_accounts->update_commissionExpense_generalLedger($shiftID);

        /** 7. COMMISSION PAYABLE */
        $this->Pos_restaurant_accounts->update_commissionPayable_generalLedger($shiftID);

        /** 8. ROYALTY PAYABLE */
        $this->Pos_restaurant_accounts->update_royaltyPayable_generalLedger($shiftID);

        /** 9. ROYALTY EXPENSES */
        $this->Pos_restaurant_accounts->update_royaltyExpenses_generalLedger($shiftID);

        /** 10. SERVICE CHARGE */
        $this->Pos_restaurant_accounts->update_serviceCharge_generalLedger($shiftID);

        /** 11. CREDIT CUSTOMER PAYMENTS - CREDIT SALES HANDLED SEPARATELY  */
        //$this->Pos_restaurant_accounts->update_creditSales_generalLedger($shiftID);


        /** BANK LEDGER UPDATE  */
        $this->Pos_restaurant_accounts->update_bankLedger($shiftID);

        /** STOCK UPDATE ITEM MASTER */
        $this->Pos_restaurant_accounts->update_itemMasterNewStock($shiftID);

        /** STOCK UPDATE WAREHOUSE ITEM MASTER */
        $this->Pos_restaurant_accounts->update_warehouseItemMasterNewStock($shiftID);

        /** ITEM  LEDGER UPDATE  */
        $this->Pos_restaurant_accounts->update_itemLedger($shiftID);

        /** CREDIT SALES ENTRIES  */
        $this->Pos_restaurant_accounts->pos_credit_sales_entries($shiftID);


        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo 'Error while updating:  <br/><br/>' . $this->db->_error_message();
            exit;
        } else {
            $this->db->trans_commit();
            echo 'Double Entries Updated on ' . date('Y-m-d H:i:s');
        }

    }

    function load_pos_detail_sales_report2()
    {
        $_POST['outletID'] = $this->input->post('outletID_f');
        $data['outletID'] = $this->input->post('outletID_f');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpCashierSource = $this->input->post('cashier');
        $outletIDs = $this->input->post('outletID_f');

        if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
            $filterDate = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
        } else {
            $filterDate = date('Y-m-d 00:00:00');
        }

        if (!empty($tmpFilterDateTo)) {
            $date2 = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
        } else {
            $date2 = date('Y-m-d 23:59:59');
        }

        if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
            $tmpCashier = join(",", $tmpCashierSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }

        if (isset($outletIDs) && !empty($outletIDs)) {
            $outlet = join(",", $outletIDs);
            $outlets = $outlet;
        } else {
            $outlets = null;
        }

        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();

        $data['paymentglConfigMaster'] = $this->Pos_restaurant_model->get_srp_erp_pos_paymentglconfigmaster2();
        $data['recordDetail'] = $this->Pos_restaurant_model->get_report_salesDetailReport2($filterDate, $date2, $cashier, $outlets);

        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();

        $this->load->view('system/pos/reports/pos-payment-sales-detail-report2', $data);
    }

    function load_hold_refno()
    {
        echo json_encode($this->Pos_restaurant_model->load_hold_refno());
    }

    function submitBOT()
    {
        $this->form_validation->set_rules('id', 'Invoice ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error' => 1, 'e_type' => 'e', 'message' => validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->submitBOT());
        }
    }

    function update_pos_submitted_payments(){
        /** update payments */
        $res=$this->Pos_restaurant_model->update_pos_submitted_payments(); // Sync DONE

        if($res['status']==true){
            echo json_encode(array('error' => 0, 'message' => 'Payment Updated Successfully', 'invoiceID' => $res['invoice_id'], 'outletID' => ''));
        }else{
            echo json_encode(array('error' => 1, 'message' => 'Error', 'invoiceID' => '', 'outletID' => ''));
        }

    }

    public function is_credit_sale(){
        $menusalesID=$this->input->post('menusalesID',true);
        $query=$this->db->query("SELECT * from srp_erp_pos_menusalespayments WHERE menuSalesID='$menusalesID' AND paymentConfigMasterID=7");
        if($query->num_rows()>0){
            $data['status']=true;
        }else{
            $data['status']=false;
        }
        echo json_encode($data);
    }

    function restaurant_doubleEntry_for_billUpdate()
    {
        $invoiceID = $_POST['invoiceID'];
        $menusalesID =  $_POST['invoiceID'];//menu sales id is similar to invoice id pass by frontend.
        //deleting previous record before insert new records
        $this->db->where('pos_menusalesID', $menusalesID);
        $this->db->delete('srp_erp_generalledger_review');
        $this->db->where('pos_menusalesID', $menusalesID);
        $this->db->delete('srp_erp_bankledger_review');
        /**
         * New GL Entries Review
         */
        /** 1. REVENUE */
        $this->Pos_restaurant_accounts->update_revenue_generalLedger_review($invoiceID);
        /** 2. BANK OR CASH */
        $this->Pos_restaurant_accounts->update_bank_cash_generalLedger_review($invoiceID);
        /** 3. COGS */
        $this->Pos_restaurant_accounts->update_cogs_generalLedger_review($invoiceID);
        /** 4. INVENTORY */
        $this->Pos_restaurant_accounts->update_inventory_generalLedger_review($invoiceID);
        /** 5. TAX */
        $this->Pos_restaurant_accounts->update_tax_generalLedger_review($invoiceID);
        /** 6. COMMISSION EXPENSE  */
        $this->Pos_restaurant_accounts->update_commissionExpense_generalLedger_review($invoiceID);
        /** 7. COMMISSION PAYABLE */
        $this->Pos_restaurant_accounts->update_commissionPayable_generalLedger_review($invoiceID);
        /** 8. ROYALTY PAYABLE */
        $this->Pos_restaurant_accounts->update_royaltyPayable_generalLedger_review($invoiceID);
        /** 9. ROYALTY EXPENSES */
        $this->Pos_restaurant_accounts->update_royaltyExpenses_generalLedger_review($invoiceID);
        /** 10. SERVICE CHARGE */
        $this->Pos_restaurant_accounts->update_serviceCharge_generalLedger_review($invoiceID);
        /** BANK LEDGER UPDATE  */
        $this->Pos_restaurant_accounts->update_bankLedger_review($invoiceID);
    }

    function load_menusalesmaster_data(){
        $id = $this->input->post('id');
        //srp_erp_pos_menusalesmaster
        $query=$this->db->query("SELECT * FROM `srp_erp_pos_menusalesmaster` WHERE menuSalesID='$id'");
        echo json_encode($query->row());
    }

    public function load_payments_list(){
        $menusalesID=$this->input->post('menusalesID',true);
        $query=$this->db->query("SELECT * FROM `srp_erp_pos_menusalespayments` WHERE menuSalesID=$menusalesID");
        echo json_encode($query->result());
    }


}

