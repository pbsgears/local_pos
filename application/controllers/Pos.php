<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** ================================
 * -- File Name : Pos.php
 * -- Project Name : Gs_SME
 * -- Module Name : Point of sale
 * -- Author : Nasik Ahamed
 * -- Create date : 18 - September 2016
 * -- Description : General controller for POS general and POS Restaurant
 *
 * --REVISION HISTORY
 * Date: 13-12-2016 By: Mohamed Shafri: worked on the double entry work in restaurant pos.
 * Date: 24-05-2017 By: Mohamed Shafri: bank ledger impact when submit tender .
 *
 */
class Pos extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        if (!isset($this->common_data['status']) || empty(trim($this->common_data['status']))) {
            header('Location: ' . site_url('Login/logout'));
            exit;
        } else {

            $this->load->model('Pos_model');
            $this->load->model('Inventory_modal');
            $this->load->model('Pos_restaurant_accounts');
            $this->load->helper('cookie');
            $this->load->helper('pos');
        }
    }

    function index()
    {
        $isHaveNotClosedSession = $this->Pos_model->isHaveNotClosedSession();
        $isHadSession = (empty($isHaveNotClosedSession)) ? 0 : $isHaveNotClosedSession;
        if ($isHadSession != 0) {
            $counterDet = $this->Pos_model->get_counterData($isHaveNotClosedSession['counterID']);
            $counterDet = $counterDet['counterCode'];
        } else {
            $counterDet = '';
        }
        //Invoice No Start
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

        $invNo = $company_code . '/' . $code . str_pad($lastINVNo, 6, '0', STR_PAD_LEFT);
//Invoice No End
        $invCodeDet = $this->Pos_model->getInvoiceSequenceCode();
        $data['title'] = 'POS';
        $data['extra'] = 'sidebar-collapse fixed';
        $data['refNo'] = $invNo;
        $data['isHadSession'] = $isHadSession;
        $wareHouseData = $this->Pos_model->get_wareHouse();
        $data['posData'] = array(
            'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
            'counterDet' => $counterDet,
        );

        $companyID = current_companyID();
        $wareHouseID = $this->common_data['ware_houseID'];
        $items = $this->item_initialSearch(0);

        $data['items'] = $items;
        $this->load->view('system/pos/general-pos-terminal', $data);
    }

    public function item_initialSearch($isJson = 1)
    {
        $companyID = current_companyID();
        $wareHouseID = $this->common_data['ware_houseID'];
        $items = $this->db->query("SELECT t1.itemAutoID, t1.itemSystemCode, t1.itemDescription, t1.currentStock,
                                 t2.companyLocalSellingPrice, defaultUnitOfMeasure, itemImage, barcode
                                 FROM srp_erp_warehouseitems t1
                                 JOIN srp_erp_itemmaster t2 ON t1.itemAutoID = t2.itemAutoID
                                 WHERE  t2.companyID='{$companyID}' AND wareHouseAutoID ='{$wareHouseID}' AND isActive=1 limit 10")->result_array();

        if ($isJson == 1) {
            echo json_encode($items);
        } else {
            return $items;
        }

    }

    public function load_currencyDenominationPage()
    {
        $currencyCode = $this->common_data['company_data']['company_default_currency'];

        //$wareHouseID = $this->common_data['ware_houseID'];
        $wareHouseID = get_outletID();
        $data['session_data'] = $this->Pos_model->isHaveNotClosedSession();
        $data['denomination'] = $this->Pos_model->currencyDenominations($currencyCode);
        $data['counters'] = $this->Pos_model->load_wareHouseCounters($wareHouseID);
        $data['dPlace'] = $this->common_data['company_data']['company_default_decimal'];
        //echo '<pre>';print_r($data['counters']); echo '</pre>';die();
        $data['isRestaurant'] = false;
        $data['isRestaurant_mobile'] = false;

        $this->load->view('system/pos/ajax/currency_denomination_view', $data);
    }

    public function load_currencyDenominationPage_mobile()
    {
        $currencyCode = $this->common_data['company_data']['company_default_currency'];

        //$wareHouseID = $this->common_data['ware_houseID'];
        $wareHouseID = get_outletID();
        $data['session_data'] = $this->Pos_model->isHaveNotClosedSession();
        $data['denomination'] = $this->Pos_model->currencyDenominations($currencyCode);
        $data['counters'] = $this->Pos_model->load_wareHouseCounters($wareHouseID);
        $data['dPlace'] = $this->common_data['company_data']['company_default_decimal'];
        //echo '<pre>';print_r($data['counters']); echo '</pre>';die();
        $data['isRestaurant'] = false;
        $data['isRestaurant_mobile'] = false;

        $this->load->view('system/pos/ajax/currency_denomination_view', $data);
    }

    public function shift_create()
    {
        $this->form_validation->set_rules('startingBalance', 'Starting Balance', 'trim|required');
        $this->form_validation->set_rules('counterID', 'Counter ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_model->shift_create());
        }
    }

    public function shift_close()
    {
        $code = $this->input->post('code');
        $this->form_validation->set_rules('startingBalance', 'Ending Balance', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $counterData = get_counterData();
            $shiftID = $this->Pos_model->get_pos_shift();
            $shiftQuery = $this->db->last_query();
            $result = $this->Pos_model->shift_close($shiftID);
            if ($result) {
                $tmpResult = array('s', 'Shift Closed Successfully', 'code' => $code, 'counterData' => $counterData); /*code is to identify where it come from.*/
                if ($code == 1) {
                    /** POS restaurant */
                    $companyID = current_companyID();
                    $this->db->select("*");
                    $this->db->from("srp_erp_company");
                    $this->db->where("company_id", $companyID);
                    $company = $this->db->get()->row_array();
                    if (!empty($company) && $company['pos_isFinanceEnables'] == 1) {
                        /** Double Entry */
                        $this->restaurant_shift_doubleEntry($shiftID);
                    }
                }

            } else {
                $tmpResult = array('e', 'Error In Shift Close', $shiftQuery);
            }
            echo json_encode($tmpResult);
        }
    }


    public function item_search()
    {
        echo json_encode($this->Pos_model->item_search());
    }

    public function item_search_barcode()
    {
        $barcode = true;
        echo json_encode($this->Pos_model->item_search($barcode));
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
            $creditNote = $this->input->post('_creditNoteAmount');
            $paidAmount = ($cashAmount + $chequeAmount + $cardAmount + $creditNote);
            $netTotVal = $this->input->post('netTotVal');
            $balanceAmount = ($netTotVal - $paidAmount);

            if ($balanceAmount > 0 && $customerID == 0) {
                echo json_encode(array('e', 'Credit not allowed for Cash Customer'));
            } else {
                echo json_encode($this->Pos_model->invoice_create());
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
            echo json_encode($this->Pos_model->invoice_hold());
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
            echo json_encode($this->Pos_model->invoice_cardDetail());
        }

    }

    public function customer_search()
    {
        echo json_encode($this->Pos_model->customer_search());
    }

    public function recall_invoice()
    {
        echo json_encode($this->Pos_model->recall_invoice());
    }

    public function recall_hold_invoice()
    {
        echo json_encode($this->Pos_model->recall_hold_invoice());
    }

    public function creditNote_search()
    {
        echo json_encode($this->Pos_model->creditNote_search());
    }

    public function invoice_search()
    {
        echo json_encode($this->Pos_model->invoice_search());
    }

    public function invoice_searchLiveSearch()
    {
        $search_string = "%" . $this->input->get('query') . "%";
        $companyID = current_companyID();
        $wareHouse = $this->common_data['ware_houseID'];
        $dataArr = array();
        $dataArr2 = array();

        $data = $this->db->query("SELECT documentSystemCode,invoiceCode FROM srp_erp_pos_invoice WHERE companyID={$companyID} AND wareHouseAutoID={$wareHouse}
                                  AND invoiceCode LIKE '{$search_string}' ORDER BY documentSystemCode ASC LIMIT 20")->result_array();

        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array(
                    'value' => $val["invoiceCode"],
                    'data' => $val['invoiceCode'],
                );
            }
        }

        $dataArr2['suggestions'] = $dataArr;

        echo json_encode($dataArr2);
    }

    public function invoice_return()
    {
        $this->form_validation->set_rules('itemID[]', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemUOM[]', 'Item UOM', 'trim|required');
        $this->form_validation->set_rules('itemQty[]', 'Item QTY', 'trim|required');
        $this->form_validation->set_rules('return_QTY[]', 'Return QTY', 'trim|required');
        $this->form_validation->set_rules('itemPrice[]', 'Item Price', 'trim|required');
        $this->form_validation->set_rules('return-customerID', 'Customer ID', 'trim|required');
        $this->form_validation->set_rules('returnMode', 'Return Mode', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_model->invoice_return());
        }
    }

    public function load_holdInv()
    {
        echo json_encode($this->Pos_model->load_holdInv());
    }

    public function invoice_print()
    {
        try {

            $invoiceID = $this->uri->segment(3);
            $doSysCode_refNo = $this->input->post('doSysCode_refNo');
            $invData = $this->Pos_model->invoice_search($invoiceID);
            $data['wHouse'] = wareHouseDetails($invData[1]['wareHouseAutoID']);
            $data['invData'] = $invData;
            $data['doSysCode_refNo'] = $doSysCode_refNo;


            if ($invData[1]['creditNoteID'] != 0 && $invData[1]['creditNoteID'] != null) {
                $data['returnDet'] = $this->Pos_model->get_returnCode($invData[1]['creditNoteID']);
            } else {
                $data['returnDet'] = null;
            }

            $outletID = get_outletID();
            $view_name = get_general_pos_print_templates($outletID);

            $this->load->view($view_name, $data);

        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

    }

    public function return_print()
    {
        $returnID = $this->uri->segment(3);
        $data['invData'] = $this->Pos_model->invReturn_details($returnID);
        $data['wHouse'] = wareHouseDetails($data['invData'][1]['wareHouseAutoID']);
        $this->load->view('system/pos/printTemplate/gen-pos-inv-exchange-print', $data);
    }

    /*Start of Counter */
    public function new_counter()
    {
        $this->form_validation->set_rules('wareHouseID', 'Ware house ID', 'trim|required');
        $this->form_validation->set_rules('counterCode', 'Counter Code', 'trim|required');
        $this->form_validation->set_rules('counterName', 'Counter Name', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_model->new_counter());
        }
    }

    public function fetch_counters()
    {
        $this->datatables->select('counterID, counterCode, wareHouseDescription, counterName, wareHouseID, wareHouseCode, wareHouseLocation', false)
            ->from('srp_erp_pos_counters t1')
            ->join('srp_erp_warehousemaster t2', 't2.wareHouseAutoID=t1.wareHouseID')
            ->add_column('action', '$1', 'actionCounter_fn(counterID, counterCode, counterName, wareHouseID)')
            ->add_column('wareHouseColumn', '$1  -  $2 - $3', 'wareHouseCode, wareHouseDescription ,wareHouseLocation')
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
            echo json_encode($this->Pos_model->delete_counterDetails());
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
            echo json_encode($this->Pos_model->update_counterDetails());
        }
    }

    public function load_counters()
    {
        $wareHouse = $this->input->post('wareHouseID');
        $thisWareHouseCounters = $this->Pos_model->load_wareHouseCounters($wareHouse);
        $thisWareHouseUsers = $this->Pos_model->load_wareHouseUsers($wareHouse);

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
            ->where('t1.isActive', 1)
            ->where('t1.companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();
    }

    public function fetch_ware_house_user()
    {
        $this->datatables->select("Ename2 AS empName, userID, ECode, autoID, t1.wareHouseID AS WHID, wareHouseCode, t3.wareHouseDescription  as wareHouseDescription, t3.wareHouseLocation as  wareHouseLocation, t3.wareHouseDescription as  wareHouseDescription", false)
            ->from('srp_erp_warehouse_users t1')
            ->join('srp_employeesdetails t2', 't1.userID=t2.EIdNo')
            ->join('srp_erp_warehousemaster t3', 't1.wareHouseID=t3.wareHouseAutoID')
            ->edit_column('wareHouseDescription', '$1  -  $2 - $3', 'wareHouseCode,wareHouseDescription, wareHouseLocation')
            ->add_column('action', '$1', 'actionWarehouseUser_fn(autoID, userID, empName, WHID, wareHouseLocation)')
            ->where('t1.companyID', $this->common_data['company_data']['company_id'])
            ->where('t1.isActive', 1);
        echo $this->datatables->generate();

    }

    public function emp_search()
    {
        echo json_encode($this->Pos_model->emp_search());
    }

    public function add_ware_house_user()
    {
        $this->form_validation->set_rules('wareHouseID', 'Ware house ID', 'trim|required');
        $this->form_validation->set_rules('employeeID', 'Employee ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_model->add_ware_house_user());
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
            echo json_encode($this->Pos_model->update_ware_house_user());
        }
    }

    public function delete_ware_house_user()
    {
        $this->form_validation->set_rules('autoID', 'Master ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_model->delete_ware_house_user());
        }
    }

    /*Promotion setups*/
    public function fetch_promotions()
    {
        $this->datatables->select('proMaster.promotionID AS promotionID, proType.Description AS typeDes, proMaster.Description AS masterDes,
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
                echo json_encode($this->Pos_model->new_promotion());
            } else {
                echo json_encode(array('e', 'End date should be greater than or equal to from date'));
            }
        }
    }

    public function get_promotionMasterDet()
    {
        $promo_ID = $this->input->post('promo_ID');
        echo json_encode($this->Pos_model->get_promotionMasterDet($promo_ID));
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

        $data['detail'] = $this->Pos_model->get_promotionDet($promoID);
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
                echo json_encode($this->Pos_model->update_promotion());
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
            echo json_encode($this->Pos_model->delete_promotion());
        }
    }

    public function load_applicableItems()
    {
        $promo_ID = $this->input->post('promo_ID');
        $data['w_items'] = $this->Pos_model->load_applicableItems($promo_ID);

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
            echo json_encode($this->Pos_model->save_promotionItems());
        }
    }

    /*End of Promotion setups*/

    public function double()
    {
        $partyData = array(
            'cusID' => 01,
            'sysCode' => 'CASH',
            'cusName' => 'CASH',
            'partyCurID' => '',
            'partyCurrency' => 'OMR',
            'partyDPlaces' => 3,
            'partyER' => 1,
        );

        $do = $this->Pos_model->double_entry(110, $partyData);
    }

    function restaurant_bill_insertDoubleEntry($shiftID)
    {
        /* Get bill payments bank  */
        $data = array();
        $i = 0;
        /**  GL_Impact  **/
        /* 1st Entry : Revenue  */
        $result = $this->Pos_restaurant_accounts->get_bill_payments_revenue($shiftID);


        if (!empty($result)) {
            $companyID = current_companyID();
            $companyCode = current_companyCode();
            $userGroup = user_group();
            $currentPc = current_pc();
            $createdUserID = current_userID();
            $currentDatetime = format_date_mysql_datetime();
            $currentUser = current_user();
            $currentUserID = current_userID();

            foreach ($result as $item) {
                $data[$i]['documentCode'] = $item['documentCode'];
                $data[$i]['documentMasterAutoID'] = $item['documentMasterAutoID'];
                $data[$i]['documentDetailAutoID'] = null;
                $data[$i]['documentSystemCode'] = $item['documentSystemCode'];
                $data[$i]['documentType'] = null;
                $data[$i]['documentDate'] = $item['documentdate'];
                $data[$i]['documentYear'] = $item['documentYear'];
                $data[$i]['documentMonth'] = $item['documentMonth'];
                $data[$i]['documentNarration'] = $item['documentNarration'];
                $data[$i]['chequeNumber'] = $item['chequeNumber'];
                $data[$i]['GLAutoID'] = $item['GLAutoID'];
                $data[$i]['systemGLCode'] = $item['systemGLCode'];
                $data[$i]['GLCode'] = $item['GLCode'];
                $data[$i]['GLDescription'] = $item['GLDescription'];
                $data[$i]['GLType'] = $item['GLType'];
                $data[$i]['amount_type'] = $item['amount_type'];
                $data[$i]['transactionCurrencyID'] = $item['transactionCurrencyID'];
                $data[$i]['transactionCurrency'] = $item['transactionCurrency'];
                $data[$i]['transactionExchangeRate'] = $item['transactionExchangeRate'];
                $data[$i]['transactionAmount'] = round($item['transactionAmount'], $item['transactionCurrencyDecimalPlaces']);
                $data[$i]['transactionCurrencyDecimalPlaces'] = $item['transactionCurrencyDecimalPlaces'];
                $data[$i]['companyLocalCurrencyID'] = $item['companyLocalCurrencyID'];
                $data[$i]['companyLocalCurrency'] = $item['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $item['companyLocalExchangeRate']; // calculate
                $data[$i]['companyLocalAmount'] = round($item['companyLocalAmount'], $item['companyLocalCurrencyDecimalPlaces']);
                $data[$i]['companyLocalCurrencyDecimalPlaces'] = $item['companyLocalCurrencyDecimalPlaces']; // calculate
                $data[$i]['companyReportingCurrencyID'] = $item['companyReportingCurrencyID'];
                $data[$i]['companyReportingCurrency'] = $item['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $item['companyReportingExchangeRate']; // calculate
                $data[$i]['companyReportingAmount'] = round($item['companyReportingAmount'], $item['companyReportingCurrencyDecimalPlaces']);
                $data[$i]['companyReportingCurrencyDecimalPlaces'] = $item['companyReportingCurrencyDecimalPlaces']; // calculate
                $data[$i]['confirmedByEmpID'] = $currentUserID;
                $data[$i]['confirmedByName'] = $currentUser;
                $data[$i]['confirmedDate'] = $currentDatetime;
                $data[$i]['approvedDate'] = $currentDatetime;
                $data[$i]['approvedbyEmpID'] = $currentUserID;
                $data[$i]['approvedbyEmpName'] = $currentUser;
                $data[$i]['segmentID'] = $item['segmentID'];
                $data[$i]['segmentCode'] = $item['segmentCode'];
                $data[$i]['companyID'] = $companyID;
                $data[$i]['companyCode'] = $companyCode;
                $data[$i]['createdUserGroup'] = $userGroup;
                $data[$i]['createdPCID'] = $currentPc;
                $data[$i]['createdUserID'] = $createdUserID;
                $data[$i]['createdDateTime'] = $currentDatetime;
                $data[$i]['createdUserName'] = $currentUser;
                $data[$i]['timestamp'] = $currentDatetime;
                $i++;
            }
        }


        /* 2nd  Entry : Bank  */
        $result = $this->Pos_restaurant_accounts->get_bill_payments_bank($shiftID);

        if (!empty($result)) {
            $companyID = current_companyID();
            $companyCode = current_companyCode();
            $userGroup = user_group();
            $currentPc = current_pc();
            $createdUserID = current_userID();
            $currentDatetime = format_date_mysql_datetime();
            $currentUser = current_user();
            $currentUserID = current_userID();


            foreach ($result as $item) {
                $data[$i]['documentCode'] = $item['documentCode'];
                $data[$i]['documentMasterAutoID'] = $item['documentMasterAutoID'];
                $data[$i]['documentDetailAutoID'] = null;
                $data[$i]['documentSystemCode'] = $item['documentSystemCode'];
                $data[$i]['documentType'] = null;
                $data[$i]['documentDate'] = $item['documentdate'];
                $data[$i]['documentYear'] = $item['documentYear'];
                $data[$i]['documentMonth'] = $item['documentMonth'];
                $data[$i]['documentNarration'] = $item['documentNarration'];
                $data[$i]['chequeNumber'] = $item['chequeNumber'];
                $data[$i]['GLAutoID'] = $item['GLAutoID'];
                $data[$i]['systemGLCode'] = $item['systemGLCode'];
                $data[$i]['GLCode'] = $item['GLCode'];
                $data[$i]['GLDescription'] = $item['GLDescription'];
                $data[$i]['GLType'] = $item['GLType'];
                $data[$i]['amount_type'] = $item['amount_type'];
                $data[$i]['transactionCurrencyID'] = $item['transactionCurrencyID'];
                $data[$i]['transactionCurrency'] = $item['transactionCurrency'];
                $data[$i]['transactionExchangeRate'] = $item['transactionExchangeRate'];
                $data[$i]['transactionAmount'] = round($item['transactionAmount'], $item['transactionCurrencyDecimalPlaces']);
                $data[$i]['transactionCurrencyDecimalPlaces'] = $item['transactionCurrencyDecimalPlaces'];
                $data[$i]['companyLocalCurrencyID'] = $item['companyLocalCurrencyID'];
                $data[$i]['companyLocalCurrency'] = $item['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $item['companyLocalExchangeRate']; // calculate
                $data[$i]['companyLocalAmount'] = round($item['companyLocalAmount'], $item['companyLocalCurrencyDecimalPlaces']);
                $data[$i]['companyLocalCurrencyDecimalPlaces'] = $item['companyLocalCurrencyDecimalPlaces']; // calculate
                $data[$i]['companyReportingCurrencyID'] = $item['companyReportingCurrencyID'];
                $data[$i]['companyReportingCurrency'] = $item['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $item['companyReportingExchangeRate']; // calculate
                $data[$i]['companyReportingAmount'] = round($item['companyReportingAmount'], $item['companyReportingCurrencyDecimalPlaces']);
                $data[$i]['companyReportingCurrencyDecimalPlaces'] = $item['companyReportingCurrencyDecimalPlaces']; // calculate
                $data[$i]['confirmedByEmpID'] = $currentUserID;
                $data[$i]['confirmedByName'] = $currentUser;
                $data[$i]['confirmedDate'] = $currentDatetime;
                $data[$i]['approvedDate'] = $currentDatetime;
                $data[$i]['approvedbyEmpID'] = $currentUserID;
                $data[$i]['approvedbyEmpName'] = $currentUser;
                $data[$i]['segmentID'] = $item['segmentID'];
                $data[$i]['segmentCode'] = $item['segmentCode'];
                $data[$i]['companyID'] = $companyID;
                $data[$i]['companyCode'] = $companyCode;
                $data[$i]['createdUserGroup'] = $userGroup;
                $data[$i]['createdPCID'] = $currentPc;
                $data[$i]['createdUserID'] = $createdUserID;
                $data[$i]['createdDateTime'] = $currentDatetime;
                $data[$i]['createdUserName'] = $currentUser;
                $data[$i]['timestamp'] = $currentDatetime;

                $i++;
            }
        }


        /* 3rd  Entry : Sales Commission  */
        $result = $this->Pos_restaurant_accounts->get_bill_payments_sales_commission($shiftID);


        if (!empty($result)) {
            $companyID = current_companyID();
            $companyCode = current_companyCode();
            $userGroup = user_group();
            $currentPc = current_pc();
            $createdUserID = current_userID();
            $currentDatetime = format_date_mysql_datetime();
            $currentUser = current_user();
            $currentUserID = current_userID();


            foreach ($result as $item) {
                $data[$i]['documentCode'] = $item['documentCode'];
                $data[$i]['documentMasterAutoID'] = $item['documentMasterAutoID'];
                $data[$i]['documentDetailAutoID'] = null;
                $data[$i]['documentSystemCode'] = $item['documentSystemCode'];
                $data[$i]['documentType'] = null;
                $data[$i]['documentDate'] = $item['documentdate'];
                $data[$i]['documentYear'] = $item['documentYear'];
                $data[$i]['documentMonth'] = $item['documentMonth'];
                $data[$i]['documentNarration'] = $item['documentNarration'];
                $data[$i]['chequeNumber'] = $item['chequeNumber'];
                $data[$i]['GLAutoID'] = $item['GLAutoID'];
                $data[$i]['systemGLCode'] = $item['systemGLCode'];
                $data[$i]['GLCode'] = $item['GLCode'];
                $data[$i]['GLDescription'] = $item['GLDescription'];
                $data[$i]['GLType'] = $item['GLType'];
                $data[$i]['amount_type'] = $item['amount_type'];
                $data[$i]['transactionCurrencyID'] = $item['transactionCurrencyID'];
                $data[$i]['transactionCurrency'] = $item['transactionCurrency'];
                $data[$i]['transactionExchangeRate'] = $item['transactionExchangeRate'];
                $data[$i]['transactionAmount'] = round($item['transactionAmount'], $item['transactionCurrencyDecimalPlaces']);
                $data[$i]['transactionCurrencyDecimalPlaces'] = $item['transactionCurrencyDecimalPlaces'];
                $data[$i]['companyLocalCurrencyID'] = $item['companyLocalCurrencyID'];
                $data[$i]['companyLocalCurrency'] = $item['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $item['companyLocalExchangeRate']; // calculate
                $data[$i]['companyLocalAmount'] = round($item['companyLocalAmount'], $item['companyLocalCurrencyDecimalPlaces']);
                $data[$i]['companyLocalCurrencyDecimalPlaces'] = $item['companyLocalCurrencyDecimalPlaces']; // calculate
                $data[$i]['companyReportingCurrencyID'] = $item['companyReportingCurrencyID'];
                $data[$i]['companyReportingCurrency'] = $item['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $item['companyReportingExchangeRate']; // calculate
                $data[$i]['companyReportingAmount'] = round($item['companyReportingAmount'], $item['companyReportingCurrencyDecimalPlaces']);
                $data[$i]['companyReportingCurrencyDecimalPlaces'] = $item['companyReportingCurrencyDecimalPlaces']; // calculate
                $data[$i]['confirmedByEmpID'] = $currentUserID;
                $data[$i]['confirmedByName'] = $currentUser;
                $data[$i]['confirmedDate'] = $currentDatetime;
                $data[$i]['approvedDate'] = $currentDatetime;
                $data[$i]['approvedbyEmpID'] = $currentUserID;
                $data[$i]['approvedbyEmpName'] = $currentUser;
                $data[$i]['segmentID'] = $item['segmentID'];
                $data[$i]['segmentCode'] = $item['segmentCode'];
                $data[$i]['companyID'] = $companyID;
                $data[$i]['companyCode'] = $companyCode;
                $data[$i]['createdUserGroup'] = $userGroup;
                $data[$i]['createdPCID'] = $currentPc;
                $data[$i]['createdUserID'] = $createdUserID;
                $data[$i]['createdDateTime'] = $currentDatetime;
                $data[$i]['createdUserName'] = $currentUser;
                $data[$i]['timestamp'] = $currentDatetime;

                $i++;
            }
        }


        /* 4th  Entry : Inventory Asset Account  */
        $result = $this->Pos_restaurant_accounts->get_bill_payments_inventory($shiftID);


        if (!empty($result)) {
            $companyID = current_companyID();
            $companyCode = current_companyCode();
            $userGroup = user_group();
            $currentPc = current_pc();
            $createdUserID = current_userID();
            $currentDatetime = format_date_mysql_datetime();
            $currentUser = current_user();
            $currentUserID = current_userID();


            foreach ($result as $item) {
                $data[$i]['documentCode'] = $item['documentCode'];
                $data[$i]['documentMasterAutoID'] = $item['documentMasterAutoID'];
                $data[$i]['documentDetailAutoID'] = null;
                $data[$i]['documentSystemCode'] = $item['documentSystemCode'];
                $data[$i]['documentType'] = null;
                $data[$i]['documentDate'] = $item['documentdate'];
                $data[$i]['documentYear'] = $item['documentYear'];
                $data[$i]['documentMonth'] = $item['documentMonth'];
                $data[$i]['documentNarration'] = $item['documentNarration'];
                $data[$i]['chequeNumber'] = $item['chequeNumber'];
                $data[$i]['GLAutoID'] = $item['GLAutoID'];
                $data[$i]['systemGLCode'] = $item['systemGLCode'];
                $data[$i]['GLCode'] = $item['GLCode'];
                $data[$i]['GLDescription'] = $item['GLDescription'];
                $data[$i]['GLType'] = $item['GLType'];
                $data[$i]['amount_type'] = $item['amount_type'];
                $data[$i]['transactionCurrencyID'] = $item['transactionCurrencyID'];
                $data[$i]['transactionCurrency'] = $item['transactionCurrency'];
                $data[$i]['transactionExchangeRate'] = $item['transactionExchangeRate'];
                $data[$i]['transactionAmount'] = round($item['transactionAmount'], $item['transactionCurrencyDecimalPlaces']);
                $data[$i]['transactionCurrencyDecimalPlaces'] = $item['transactionCurrencyDecimalPlaces'];
                $data[$i]['companyLocalCurrencyID'] = $item['companyLocalCurrencyID'];
                $data[$i]['companyLocalCurrency'] = $item['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $item['companyLocalExchangeRate']; // calculate
                $data[$i]['companyLocalAmount'] = round($item['companyLocalAmount'], $item['companyLocalCurrencyDecimalPlaces']);
                $data[$i]['companyLocalCurrencyDecimalPlaces'] = $item['companyLocalCurrencyDecimalPlaces']; // calculate
                $data[$i]['companyReportingCurrencyID'] = $item['companyReportingCurrencyID'];
                $data[$i]['companyReportingCurrency'] = $item['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $item['companyReportingExchangeRate']; // calculate
                $data[$i]['companyReportingAmount'] = round($item['companyReportingAmount'], $item['companyReportingCurrencyDecimalPlaces']);
                $data[$i]['companyReportingCurrencyDecimalPlaces'] = $item['companyReportingCurrencyDecimalPlaces']; // calculate
                $data[$i]['confirmedByEmpID'] = $currentUserID;
                $data[$i]['confirmedByName'] = $currentUser;
                $data[$i]['confirmedDate'] = $currentDatetime;
                $data[$i]['approvedDate'] = $currentDatetime;
                $data[$i]['approvedbyEmpID'] = $currentUserID;
                $data[$i]['approvedbyEmpName'] = $currentUser;
                $data[$i]['segmentID'] = $item['segmentID'];
                $data[$i]['segmentCode'] = $item['segmentCode'];
                $data[$i]['companyID'] = $companyID;
                $data[$i]['companyCode'] = $companyCode;
                $data[$i]['createdUserGroup'] = $userGroup;
                $data[$i]['createdPCID'] = $currentPc;
                $data[$i]['createdUserID'] = $createdUserID;
                $data[$i]['createdDateTime'] = $currentDatetime;
                $data[$i]['createdUserName'] = $currentUser;
                $data[$i]['timestamp'] = $currentDatetime;

                $i++;
            }
        }


        /* 5th  Entry : COGS  */
        $result = $this->Pos_restaurant_accounts->get_bill_payments_cogs($shiftID);


        if (!empty($result)) {
            $companyID = current_companyID();
            $companyCode = current_companyCode();
            $userGroup = user_group();
            $currentPc = current_pc();
            $createdUserID = current_userID();
            $currentDatetime = format_date_mysql_datetime();
            $currentUser = current_user();
            $currentUserID = current_userID();


            foreach ($result as $item) {
                $data[$i]['documentCode'] = $item['documentCode'];
                $data[$i]['documentMasterAutoID'] = $item['documentMasterAutoID'];
                $data[$i]['documentDetailAutoID'] = null;
                $data[$i]['documentSystemCode'] = $item['documentSystemCode'];
                $data[$i]['documentType'] = null;
                $data[$i]['documentDate'] = $item['documentdate'];
                $data[$i]['documentYear'] = $item['documentYear'];
                $data[$i]['documentMonth'] = $item['documentMonth'];
                $data[$i]['documentNarration'] = $item['documentNarration'];
                $data[$i]['chequeNumber'] = $item['chequeNumber'];
                $data[$i]['GLAutoID'] = $item['GLAutoID'];
                $data[$i]['systemGLCode'] = $item['systemGLCode'];
                $data[$i]['GLCode'] = $item['GLCode'];
                $data[$i]['GLDescription'] = $item['GLDescription'];
                $data[$i]['GLType'] = $item['GLType'];
                $data[$i]['amount_type'] = $item['amount_type'];
                $data[$i]['transactionCurrencyID'] = $item['transactionCurrencyID'];
                $data[$i]['transactionCurrency'] = $item['transactionCurrency'];
                $data[$i]['transactionExchangeRate'] = $item['transactionExchangeRate'];
                $data[$i]['transactionAmount'] = round($item['transactionAmount'], $item['transactionCurrencyDecimalPlaces']);
                $data[$i]['transactionCurrencyDecimalPlaces'] = $item['transactionCurrencyDecimalPlaces'];
                $data[$i]['companyLocalCurrencyID'] = $item['companyLocalCurrencyID'];
                $data[$i]['companyLocalCurrency'] = $item['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $item['companyLocalExchangeRate']; // calculate
                $data[$i]['companyLocalAmount'] = round($item['companyLocalAmount'], $item['companyLocalCurrencyDecimalPlaces']);
                $data[$i]['companyLocalCurrencyDecimalPlaces'] = $item['companyLocalCurrencyDecimalPlaces']; // calculate
                $data[$i]['companyReportingCurrencyID'] = $item['companyReportingCurrencyID'];
                $data[$i]['companyReportingCurrency'] = $item['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $item['companyReportingExchangeRate']; // calculate
                $data[$i]['companyReportingAmount'] = round($item['companyReportingAmount'], $item['companyReportingCurrencyDecimalPlaces']);
                $data[$i]['companyReportingCurrencyDecimalPlaces'] = $item['companyReportingCurrencyDecimalPlaces']; // calculate
                $data[$i]['confirmedByEmpID'] = $currentUserID;
                $data[$i]['confirmedByName'] = $currentUser;
                $data[$i]['confirmedDate'] = $currentDatetime;
                $data[$i]['approvedDate'] = $currentDatetime;
                $data[$i]['approvedbyEmpID'] = $currentUserID;
                $data[$i]['approvedbyEmpName'] = $currentUser;
                $data[$i]['segmentID'] = $item['segmentID'];
                $data[$i]['segmentCode'] = $item['segmentCode'];
                $data[$i]['companyID'] = $companyID;
                $data[$i]['companyCode'] = $companyCode;
                $data[$i]['createdUserGroup'] = $userGroup;
                $data[$i]['createdPCID'] = $currentPc;
                $data[$i]['createdUserID'] = $createdUserID;
                $data[$i]['createdDateTime'] = $currentDatetime;
                $data[$i]['createdUserName'] = $currentUser;
                $data[$i]['timestamp'] = $currentDatetime;

                $i++;
            }
        }


        if (!empty($data)) {
            $result = $this->Pos_model->insert_batch_srp_erp_generalledger($data);
        }
        return array('error' => 0, 'message' => 'GL entries saved with ' . count($data) . ' records', 'result_batch1' => $result);


        /** /  GL_Impact  **/

    }


    /** created by shafri */
    function loadNewInvoiceNo()
    {
        $invCodeDet = $this->Pos_model->getInvoiceSequenceCode();

        /*get next bill no*/
        if (!empty($invCodeDet)) {
            echo json_encode(array('error' => 0, 'message' => 'done', 'refCode' => $invCodeDet['sequenceCode']));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'empty', 'refCode' => null));
        }

    }


    /*** By Nasik ***/
    function itemLoadDefault()
    {
        return $items = $this->item_initialSearch(1);
    }


    function restaurant_shift_doubleEntry($shiftID)
    {
        $this->db->trans_start();
        $outletID = get_outletID();
        $exceededItem = false;

        if ($exceededItem) {
            /** 0. ITEM EXCEEDED */
            $this->Pos_restaurant_accounts->update_itemExceededRecord($shiftID, false);
        }


        /** 1. REVENUE */
        $this->Pos_restaurant_accounts->update_revenue_generalLedger($shiftID); // outlet ID added - where done

        /** 2. BANK OR CASH */
        $this->Pos_restaurant_accounts->update_bank_cash_generalLedger($shiftID); // outlet ID added - where done

        /** 3. COGS */
        $this->Pos_restaurant_accounts->update_cogs_generalLedger($shiftID); // outlet ID added - where done

        /** 4. INVENTORY */
        $this->Pos_restaurant_accounts->update_inventory_generalLedger($shiftID); // outlet ID added - where done

        if ($exceededItem) {
            /** Deduct Item Exceeded - COGS */
            $this->Pos_restaurant_accounts->itemExceeded_adjustment_generalLedger_cogs($shiftID, false);

            /** 4. INVENTORY */
            $this->Pos_restaurant_accounts->itemExceeded_adjustment_generalLedger_inventory($shiftID, false);
        }


        /** 5. TAX */
        $this->Pos_restaurant_accounts->update_tax_generalLedger($shiftID); // outlet ID added

        /** 6. COMMISSION EXPENSE  */
        $this->Pos_restaurant_accounts->update_commissionExpense_generalLedger($shiftID);  // outlet ID added - where done

        /** 7. COMMISSION PAYABLE */
        $this->Pos_restaurant_accounts->update_commissionPayable_generalLedger($shiftID); // outlet ID added - where done

        /** 8. ROYALTY PAYABLE */
        $this->Pos_restaurant_accounts->update_royaltyPayable_generalLedger($shiftID); // outlet ID added - where done

        /** 9. ROYALTY EXPENSES */
        $this->Pos_restaurant_accounts->update_royaltyExpenses_generalLedger($shiftID); // outlet ID added - where done

        /** 10. SERVICE CHARGE */
        $this->Pos_restaurant_accounts->update_serviceCharge_generalLedger($shiftID); // outlet ID added - where done

        /** 11. CREDIT CUSTOMER PAYMENTS - CREDIT SALES HANDLED SEPARATELY  */
        //$this->Pos_restaurant_accounts->update_creditSales_generalLedger($shiftID);

        /** OUTLET TAX */
        $this->Pos_restaurant_accounts->update_outlet_tax_generalLedger($shiftID);

        /** BANK LEDGER UPDATE  */
        $this->Pos_restaurant_accounts->update_bankLedger($shiftID); // outlet ID added - where done

        /** Stocks are not available in the outlet -> insert it from item master */
        $this->Pos_restaurant_accounts->insert_items_notExist_inWarehouseItem($shiftID); // where outlet ID


        if ($exceededItem) {
            /** STOCK UPDATE ITEM MASTER */
            $this->Pos_restaurant_accounts->update_itemMasterNewStock_itemExceeded($shiftID);

            /** STOCK UPDATE WAREHOUSE ITEM MASTER */
            $this->Pos_restaurant_accounts->update_warehouseItemMasterNewStock_itemExceeded($shiftID);
        } else {
            /** STOCK UPDATE ITEM MASTER */
            $this->Pos_restaurant_accounts->update_itemMasterNewStock($shiftID);

            /** STOCK UPDATE WAREHOUSE ITEM MASTER */
            $this->Pos_restaurant_accounts->update_warehouseItemMasterNewStock($shiftID);
        }

        $this->Pos_restaurant_accounts->update_itemLedger($shiftID); // outlet ID already added - where done

        if ($exceededItem) {
            /** ITEM EXCEEDED - ITEM LEDGER */
            $this->Pos_restaurant_accounts->itemExceeded_adjustment_itemLedger($shiftID); // is_sync =>0
        }


        /** ----------------- CREDIT SALES ENTRIES ------------------  */

        $CS = " SELECT *  FROM srp_erp_pos_menusalesmaster  WHERE isCreditSales = 1 AND wareHouseAutoID = '" . $outletID . "'  AND shiftID = '" . $shiftID . "'";
        $resultCS = $this->db->query($CS)->result_array();
        if (!empty($resultCS)) {
            foreach ($resultCS as $CS) {
                $menuSalesID = $CS['menuSalesID'];
                /** 0. CUSTOMER INVOICE - Credit Sales Entries  */
                $this->Pos_restaurant_accounts->pos_generate_invoices($shiftID, $menuSalesID);  // outlet ID added - where done - _sync

                if ($exceededItem) {
                    /** 0. ITEM EXCEEDED */
                    $this->Pos_restaurant_accounts->update_itemExceededRecord_creditSales_menuSalesID($shiftID, $menuSalesID);
                }

            }
        }

        /** 1. CREDIT SALES  - REVENUE */
        $this->Pos_restaurant_accounts->update_revenue_generalLedger_credit_sales($shiftID); // outlet ID added - where done

        /** 2. CREDIT SALES  - COGS */
        $this->Pos_restaurant_accounts->update_cogs_generalLedger_credit_sales($shiftID); // outlet ID added - where done
        /** 3. CREDIT SALES  - INVENTORY */
        $this->Pos_restaurant_accounts->update_inventory_generalLedger_credit_sales($shiftID); // outlet ID added - where done


        if ($exceededItem) {
            /** Adjust General Ledger for Credit Sales  */
            $this->Pos_restaurant_accounts->creditSales_adjust_inventory($shiftID); // is_sync => 0
            $this->Pos_restaurant_accounts->creditSales_adjust_cogs($shiftID); // is_sync => 0
        }


        /** 4.  CREDIT SALES - TAX */
        $this->Pos_restaurant_accounts->update_tax_generalLedger_credit_sales($shiftID);  //5 outlet ID added - where done

        /** CREDIT SALES - OUTLET TAX */
        $this->Pos_restaurant_accounts->update_outlet_tax_generalLedger_credit_sales($shiftID);

        /** 5.  CREDIT SALES - COMMISSION EXPENSE  */
        $this->Pos_restaurant_accounts->update_commissionExpense_generalLedger_credit_sales($shiftID);  // outlet ID added - where done
        /** 6.  CREDIT SALES - COMMISSION PAYABLE */
        $this->Pos_restaurant_accounts->update_commissionPayable_generalLedger_credit_sales($shiftID); // outlet ID added - where done
        /** 7.  CREDIT SALES - ROYALTY PAYABLE */
        $this->Pos_restaurant_accounts->update_royaltyPayable_generalLedger_credit_sales($shiftID); // outlet ID added - where done
        /** 8.  CREDIT SALES - ROYALTY EXPENSES */
        $this->Pos_restaurant_accounts->update_royaltyExpenses_generalLedger_credit_sales($shiftID); // outlet ID added - where done
        /** 9. CREDIT SALES -  SERVICE CHARGE */
        $this->Pos_restaurant_accounts->update_serviceCharge_generalLedger_credit_sales($shiftID); // outlet ID added - where done
        /** 10. CREDIT SALES -  CREDIT CUSTOMER PAYMENTS */
        $this->Pos_restaurant_accounts->update_creditSales_generalLedger_credit_sales($shiftID); // outlet ID added - where done


        if ($exceededItem) {
            /** CREDIT SALES - ITEM MASTER STOCK UPDATE - item exceeded */
            $this->Pos_restaurant_accounts->update_itemMasterNewStock_credit_sales_Item_exceeded($shiftID);
            /** CREDIT SALES - WAREHOUSE ITEM MASTER STOCK UPDATE */
            $this->Pos_restaurant_accounts->update_warehouseItemMasterNewStock_credit_sales_Item_exceeded($shiftID);
        } else {
            /** CREDIT SALES - ITEM MASTER STOCK UPDATE */
            $this->Pos_restaurant_accounts->update_itemMasterNewStock_credit_sales($shiftID); // outlet ID - where done
            /** CREDIT SALES - WAREHOUSE ITEM MASTER STOCK UPDATE */
            $this->Pos_restaurant_accounts->update_warehouseItemMasterNewStock_credit_sales($shiftID);// outlet ID - where done
        }

        /** CREDIT SALES - ITEM LEDGER  */
        $this->Pos_restaurant_accounts->update_itemLedger_credit_sales($shiftID); // outlet ID already added - where done

        if ($exceededItem) {
            // item ledger entry
            $this->Pos_restaurant_accounts->creditSales_adjust_item_master($shiftID);  // is_sync => 0
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('status' => 'Error while updating:  <br/><br/>' . $this->db->_error_message()));
            exit;
        } else {
            $this->db->trans_commit();
            return array('status' => 'Double Entries Updated');
        }
    }


    function restaurant_shift_doubleEntry_old($shiftID)
    {
        $data = array();
        $i = 0;
        /**  GL_Impact  **/
        /** 1st Entry : Revenue - done */
        $this->Pos_restaurant_accounts->update_revenue_generalLedger($shiftID);

        /** 2nd  Entry : Bank & Cash - done */
        $this->Pos_restaurant_accounts->update_bank_cash_generalLedger($shiftID);

        /** 3rd  Entry : COGS  - done */
        $this->Pos_restaurant_accounts->update_cogs_generalLedger($shiftID);

        /** 4th  Entry : Inventory Asset Account  */
        $this->Pos_restaurant_accounts->update_inventory_generalLedger($shiftID);

        /** 5th  Entry : Tax  */
        $this->Pos_restaurant_accounts->update_tax_generalLedger($shiftID);


        /* 6th  Entry : Commission  GL */
        $result = $this->Pos_restaurant_accounts->get_commission_GL($shiftID);

        if (!empty($result)) {
            $companyID = current_companyID();
            $companyCode = current_companyCode();
            $userGroup = user_group();
            $currentPc = current_pc();
            $createdUserID = current_userID();
            $currentDatetime = format_date_mysql_datetime();
            $currentUser = current_user();
            $currentUserID = current_userID();


            foreach ($result as $item) {
                $data[$i]['documentCode'] = $item['documentCode'];
                $data[$i]['documentMasterAutoID'] = $item['documentMasterAutoID'];
                $data[$i]['documentDetailAutoID'] = null;
                $data[$i]['documentSystemCode'] = $item['documentSystemCode'];
                $data[$i]['documentType'] = null;
                $data[$i]['documentDate'] = $item['documentdate'];
                $data[$i]['documentYear'] = $item['documentYear'];
                $data[$i]['documentMonth'] = $item['documentMonth'];
                $data[$i]['documentNarration'] = $item['documentNarration'];
                $data[$i]['chequeNumber'] = $item['chequeNumber'];
                $data[$i]['GLAutoID'] = $item['GLAutoID'];
                $data[$i]['systemGLCode'] = $item['systemGLCode'];
                $data[$i]['GLCode'] = $item['GLCode'];
                $data[$i]['GLDescription'] = $item['GLDescription'];
                $data[$i]['GLType'] = $item['GLType'];
                $data[$i]['amount_type'] = $item['amount_type'];
                $data[$i]['transactionCurrencyID'] = $item['transactionCurrencyID'];
                $data[$i]['transactionCurrency'] = $item['transactionCurrency'];
                $data[$i]['transactionExchangeRate'] = $item['transactionExchangeRate'];
                $data[$i]['transactionAmount'] = round($item['transactionAmount'], $item['transactionCurrencyDecimalPlaces']);
                $data[$i]['transactionCurrencyDecimalPlaces'] = $item['transactionCurrencyDecimalPlaces'];
                $data[$i]['companyLocalCurrencyID'] = $item['companyLocalCurrencyID'];
                $data[$i]['companyLocalCurrency'] = $item['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $item['companyLocalExchangeRate']; // calculate
                $data[$i]['companyLocalAmount'] = round($item['companyLocalAmount'], $item['companyLocalCurrencyDecimalPlaces']);
                $data[$i]['companyLocalCurrencyDecimalPlaces'] = $item['companyLocalCurrencyDecimalPlaces']; // calculate
                $data[$i]['companyReportingCurrencyID'] = $item['companyReportingCurrencyID'];
                $data[$i]['companyReportingCurrency'] = $item['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $item['companyReportingExchangeRate']; // calculate
                $data[$i]['companyReportingAmount'] = round($item['companyReportingAmount'], $item['companyReportingCurrencyDecimalPlaces']);
                $data[$i]['companyReportingCurrencyDecimalPlaces'] = $item['companyReportingCurrencyDecimalPlaces']; // calculate
                $data[$i]['confirmedByEmpID'] = $currentUserID;
                $data[$i]['confirmedByName'] = $currentUser;
                $data[$i]['confirmedDate'] = $currentDatetime;
                $data[$i]['approvedDate'] = $currentDatetime;
                $data[$i]['approvedbyEmpID'] = $currentUserID;
                $data[$i]['approvedbyEmpName'] = $currentUser;
                $data[$i]['segmentID'] = $item['segmentID'];
                $data[$i]['segmentCode'] = $item['segmentCode'];
                $data[$i]['companyID'] = $companyID;
                $data[$i]['companyCode'] = $companyCode;
                $data[$i]['createdUserGroup'] = $userGroup;
                $data[$i]['createdPCID'] = $currentPc;
                $data[$i]['createdUserID'] = $createdUserID;
                $data[$i]['createdDateTime'] = $currentDatetime;
                $data[$i]['createdUserName'] = $currentUser;
                $data[$i]['timestamp'] = $currentDatetime;

                $i++;
            }
        }

        /* 7th  Entry : Commission Payable GL */
        $result = $this->Pos_restaurant_accounts->get_commissionPayable_GL($shiftID);

        if (!empty($result)) {
            $companyID = current_companyID();
            $companyCode = current_companyCode();
            $userGroup = user_group();
            $currentPc = current_pc();
            $createdUserID = current_userID();
            $currentDatetime = format_date_mysql_datetime();
            $currentUser = current_user();
            $currentUserID = current_userID();


            foreach ($result as $item) {
                $data[$i]['documentCode'] = $item['documentCode'];
                $data[$i]['documentMasterAutoID'] = $item['documentMasterAutoID'];
                $data[$i]['documentDetailAutoID'] = null;
                $data[$i]['documentSystemCode'] = $item['documentSystemCode'];
                $data[$i]['documentType'] = null;
                $data[$i]['documentDate'] = $item['documentdate'];
                $data[$i]['documentYear'] = $item['documentYear'];
                $data[$i]['documentMonth'] = $item['documentMonth'];
                $data[$i]['documentNarration'] = $item['documentNarration'];
                $data[$i]['chequeNumber'] = $item['chequeNumber'];
                $data[$i]['GLAutoID'] = $item['GLAutoID'];
                $data[$i]['systemGLCode'] = $item['systemGLCode'];
                $data[$i]['GLCode'] = $item['GLCode'];
                $data[$i]['GLDescription'] = $item['GLDescription'];
                $data[$i]['GLType'] = $item['GLType'];
                $data[$i]['amount_type'] = $item['amount_type'];
                $data[$i]['transactionCurrencyID'] = $item['transactionCurrencyID'];
                $data[$i]['transactionCurrency'] = $item['transactionCurrency'];
                $data[$i]['transactionExchangeRate'] = $item['transactionExchangeRate'];
                $data[$i]['transactionAmount'] = round($item['transactionAmount'], $item['transactionCurrencyDecimalPlaces']);
                $data[$i]['transactionCurrencyDecimalPlaces'] = $item['transactionCurrencyDecimalPlaces'];
                $data[$i]['companyLocalCurrencyID'] = $item['companyLocalCurrencyID'];
                $data[$i]['companyLocalCurrency'] = $item['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $item['companyLocalExchangeRate']; // calculate
                $data[$i]['companyLocalAmount'] = round($item['companyLocalAmount'], $item['companyLocalCurrencyDecimalPlaces']);
                $data[$i]['companyLocalCurrencyDecimalPlaces'] = $item['companyLocalCurrencyDecimalPlaces']; // calculate
                $data[$i]['companyReportingCurrencyID'] = $item['companyReportingCurrencyID'];
                $data[$i]['companyReportingCurrency'] = $item['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $item['companyReportingExchangeRate']; // calculate
                $data[$i]['companyReportingAmount'] = round($item['companyReportingAmount'], $item['companyReportingCurrencyDecimalPlaces']);
                $data[$i]['companyReportingCurrencyDecimalPlaces'] = $item['companyReportingCurrencyDecimalPlaces']; // calculate
                $data[$i]['confirmedByEmpID'] = $currentUserID;
                $data[$i]['confirmedByName'] = $currentUser;
                $data[$i]['confirmedDate'] = $currentDatetime;
                $data[$i]['approvedDate'] = $currentDatetime;
                $data[$i]['approvedbyEmpID'] = $currentUserID;
                $data[$i]['approvedbyEmpName'] = $currentUser;
                $data[$i]['segmentID'] = $item['segmentID'];
                $data[$i]['segmentCode'] = $item['segmentCode'];
                $data[$i]['companyID'] = $companyID;
                $data[$i]['companyCode'] = $companyCode;
                $data[$i]['createdUserGroup'] = $userGroup;
                $data[$i]['createdPCID'] = $currentPc;
                $data[$i]['createdUserID'] = $createdUserID;
                $data[$i]['createdDateTime'] = $currentDatetime;
                $data[$i]['createdUserName'] = $currentUser;
                $data[$i]['timestamp'] = $currentDatetime;

                $i++;
            }
        }


        if (!empty($data)) {
            //  $result = $this->Pos_model->insert_batch_srp_erp_generalledger($data);
        }
        return array('error' => 0, 'message' => 'GL entries saved with ' . count($data) . ' records', 'result_batch1' => $result, 'data' => $data);


        /** /  GL_Impact  **/

    }

    public function fetch_usergroup()
    {

        $this->datatables->select('userGroupMasterID, description, companyID, isActive')
            ->from('srp_erp_pos_auth_usergroupmaster')
            ->add_column('action', '$1', 'usergroup_action(userGroupMasterID, description, isActive)')
            ->edit_column('Active', '$1', 'load_active_usergroups(userGroupMasterID,isActive)')
            //->where('srp_erp_pos_auth_usergroupmaster.isActive', 1)
            ->where('srp_erp_pos_auth_usergroupmaster.companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();

    }

    function update_usergroup_isactive()
    {
        echo json_encode($this->Pos_model->update_usergroup_isactive());
    }

    public function save_userGroup()
    {
        $this->form_validation->set_rules('description', 'User Group', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_model->save_userGroup());
        }

    }

    public function update_userGroup()
    {
        $this->form_validation->set_rules('description', 'User Group', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_model->update_userGroup());
        }

    }

    public function fetch_user_for_group()
    {
        $search = $_REQUEST["sSearch"];
        $userGroupMasterID = $this->input->post('userGroupMasterID');
        $likesearch = '';
        if ($search) {
            $likesearch = "AND (`EIdNo` LIKE '%$search%' OR `ECode` LIKE '%$search%' OR `Ename2` LIKE '%$search%' OR `srp_designation`.`DesDescription` LIKE '%$search%')";
        }
        $where = "(srp_employeesdetails.pos_userGroupMasterID is null OR srp_employeesdetails.pos_userGroupMasterID = " . $userGroupMasterID . ") $likesearch";

        $this->datatables->select('EIdNo, ECode, Ename2, srp_designation.DesDescription as DesDescription')->from('srp_employeesdetails')->join('srp_designation', 'srp_employeesdetails.EmpDesignationId=srp_designation.DesignationID', 'left');
        $this->datatables->add_column('action', '$1', 'usergroupuser_action(EIdNo,' . $userGroupMasterID . ')');
        $this->datatables->where('srp_employeesdetails.isDischarged', 0);
        /*$this->datatables->where('srp_employeesdetails.pos_userGroupMasterID', null)
        $this->datatables->or_where('srp_employeesdetails.pos_userGroupMasterID', $userGroupMasterID)*/
        $this->datatables->where($where);
        /*if ($search) {
            $this->datatables->like('Ename2', $search);
            $this->datatables->or_like('ECode', $search);
            $this->datatables->or_like('srp_designation.DesDescription', $search);
        }*/
        $this->datatables->where('srp_employeesdetails.Erp_companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();

    }

    public function fetch_assigned_users()
    {
        echo json_encode($this->Pos_model->fetch_assigned_users());
    }

    function save_usergroup_users()
    {
        /*$this->form_validation->set_rules('empID[]', 'Employee ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', 'Select Employee'));
        } else {*/
        echo json_encode($this->Pos_model->save_usergroup_users());
        //}
    }

    function getInvoiceCode()
    {
        echo json_encode($this->Pos_model->getInvoiceCode());
    }

    function submit_pos_payments()
    {
        /*$post = $this->input->post();
        print_r($post);*/
        $totalPayment = $this->input->post('paid');
        $netTotalAmount = $this->input->post('total_payable_amt');
        $customerID = $this->input->post('customerID');
        $cardTotalAmount = $this->input->post('cardTotalAmount');
        $CreditSalesAmnt = $this->input->post('CreditSalesAmnt');

        if ($totalPayment < $netTotalAmount) { /*&& $customerID == 0*/
            echo json_encode(array('e', 'Please enter payment amount greater than net total'));
            exit;
        }

        if ($cardTotalAmount > $netTotalAmount) {
            echo json_encode(array('e', 'Card and Cheque Amount sum can not be greater than net total.'));
            exit;
        }

        if (($CreditSalesAmnt < $netTotalAmount || $CreditSalesAmnt > $netTotalAmount) && $CreditSalesAmnt > 0) {
            echo json_encode(array('e', 'Payment not equal to Net total.'));
            exit;
        }


        echo json_encode($this->Pos_model->submit_pos_payments());
    }

    function creditNote_load()
    {
        echo json_encode($this->Pos_model->creditNote_load());
    }

    function savecustomer()
    {
        $this->form_validation->set_rules('customerName', 'customer Name', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Pos_model->save_customer());
        }


    }

    /** remove this later */
    function batch_insert_credit_sales_invoice($shiftID)
    {
        $this->Pos_restaurant_accounts->pos_generate_invoices($shiftID);
    }

}