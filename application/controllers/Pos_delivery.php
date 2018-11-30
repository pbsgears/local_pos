<?php
/**
 *
 * -- =============================================
 * -- File Name : Pos_delivery.php
 * -- Project Name : Point of Sales Management with ERP
 * -- Module Name : POS Delivery
 * -- Author : Mohamed Shafri
 * -- Create date : 11 November 2017
 * -- Description : Delivery Management Module  .
 *
 * --REVISION HISTORY
 * --Date: 11-Nov2017 By: Mohamed Shafri: file created
 * -- =============================================
 **/
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos_delivery extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Pos_delivery_model');
        $this->load->model('Pos_restaurant_model');
        $this->load->helper('pos');
    }

    function confirm_delivery_order()
    {
        $this->form_validation->set_rules('phone', 'Phone number', 'trim|required');
        $this->form_validation->set_rules('deliveryDate', 'Delivery Date', 'trim|required');
        //$this->form_validation->set_rules('email', 'Email Address', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error' => 1, 'message' => validation_errors()));
        } else {
            echo $this->Pos_delivery_model->confirm_delivery_order();
        }
    }

    function loadDeliveryCustomerInfo()
    {
        $phoneNo = $this->input->post('phoneNo');
        $customerInfo = $this->db->select('*')->from('srp_erp_pos_customermaster')->where('customerTelephone', $phoneNo)->get()->row_array();
        if ($customerInfo) {
            $customerInfo['DOB'] = !empty($customerInfo['DOB']) ? date('d/m/Y', strtotime($customerInfo['DOB'])) : null;
            echo json_encode(array('error' => 0, 'message' => 'customer exist', 'customerData' => $customerInfo));
        } else {
            echo json_encode(array('error' => 2, 'message' => 'Customer not exist'));
        }

    }

    public function allCalenderEvents()
    {


        $event_array2 = array();
        $companyID = current_companyID();

        /*$category = trim($this->input->post('category'));
        $status = trim($this->input->post('status'));*/

        $filterCategory = '';
        /*if (isset($category) && !empty($category)) {
            $filterCategory = " AND srp_erp_crm_task.categoryID = " . $category . "";
        }*/

        $filterStatus = '';
        /*if (isset($status) && !empty($status)) {
            $filterStatus = " AND status = " . $status . "";
        }*/

        //$where = "WHERE salesMaster.companyID = " . $companyID . $filterCategory . $filterStatus;
        $where = "WHERE salesMaster.isHold = 1  AND salesMaster.wareHouseAutoID ";

        $sql2 = "select deliveryOrderID as deliveryOrderID, salesMaster.invoiceCode as invoiceCode, salesMaster.holdRemarks as holdRemarks, deliveryOrders.deliveryDate as deliveryDate,  deliveryDate as DueDate,DATE_FORMAT(deliveryTime,'%h:%i %p') AS StartTime, deliveryTime as deliveryTime, '#FFC400' as backGroundColor,deliveryOrders.menuSalesMasterID as invoiceID,salesMaster.subTotal as billTotal,customerMaster.CustomerName,customerMaster.CustomerAddress1,salesMaster.companyLocalCurrency as amountCurrency FROM srp_erp_pos_deliveryorders as deliveryOrders INNER JOIN srp_erp_pos_menusalesmaster as salesMaster ON deliveryOrders.menuSalesMasterID = salesMaster.menuSalesID LEFT JOIN srp_erp_pos_customermaster as customerMaster ON customerMaster.posCustomerAutoID = deliveryOrders.posCustomerAutoID " . $where;
        //echo $sql2;
        $result2 = $this->db->query($sql2)->result_array();

        foreach ($result2 as $record2) {
            $dateTime = $record2['deliveryDate'] . $record2['deliveryTime'];
            //echo $dateTime;
            $record2['deliveryDate'] = date('Y-m-d h:i:s', strtotime($dateTime));
            //$record2['deliveryDate'] = date('Y-m-d h:i:s', strtotime($record2['deliveryDate'].' '.$record2['deliveryTime']));
            //echo $record2['deliveryDate'].' '.$record2['deliveryTime'];

            $event_array2[] = array(
                'id' => $record2['deliveryOrderID'],
                'title' => $record2['invoiceCode'] . ' ' . $record2['amountCurrency'] . ':' . $record2['billTotal'] . '/=' . ' ' . $record2['CustomerName'] . ' ' . $record2['CustomerAddress1'],
                'start' => $record2['deliveryDate'],
                'end' => $record2['DueDate'],
                //'url' => 'fetchPage(\'system/crm/contact_management\',\'1\',\'Contact\')',
                'color' => $record2['backGroundColor'],
                'invoiceID' => $record2['invoiceID'],
            );
        }
        $arr = array_merge($event_array2);

        echo json_encode($arr);
    }

    function delivery_load_payment_detail()
    {
        $invoiceID = $this->input->post('invoiceID');
        $this->db->select('payment.*,configMaster.description as paymentDescription');
        $this->db->from('srp_erp_pos_menusalespayments payment');
        $this->db->join('srp_erp_pos_paymentglconfigmaster configMaster', 'configMaster.autoID = payment.paymentConfigMasterID');
        $this->db->where('payment.menuSalesID', $invoiceID);
        $result = $this->db->get()->result_array();

        $data['invoiceID'] = $invoiceID;
        $data['payments'] = $result;
        $this->load->view('system/pos/delivery/delivery_load_payment_detail', $data);
    }

    function load_delivery_info()
    {
        $invoiceID = $this->input->post('invoiceID');
        $this->db->select('*');
        $this->db->from('srp_erp_pos_deliveryorders');
        $this->db->where('menuSalesMasterID', $invoiceID);
        $result = $this->db->get()->row_array();
        if (!empty($result)) {
            $result['deliveryDate'] = date('d-m-Y', strtotime($result['deliveryDate']));
            $result['deliveryTime'] = date('h:i A', strtotime($result['deliveryTime']));
            echo json_encode(array('error' => 0, 'message' => 'found', 'tmpData' => $result));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'New Order'));
        }
    }

    function update_deliveryInfo()
    {
        $id = $this->input->post('deliveryOrderID');
        $data['deliveryDate'] = date('Y-m-d', strtotime($this->input->post('deliveryDate')));
        $data['deliveryTime'] = date('H:i:s', strtotime($this->input->post('deliveryTime')));
        $data['landMarkLocation'] = $this->input->post('landMarkLocation');
        $data['deliveryType'] = $this->input->post('deliveryType');

        $this->db->where('deliveryOrderID', $id);
        $result = $this->db->update('srp_erp_pos_deliveryorders', $data);
        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'Delivery detail updated successfully'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Something went wrong, Please try again.'));
        }
    }

    function loadPrintTemplate()
    {
        $invoiceID = $this->input->post('menuSalesID');
        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID);
        $orderDetail = $this->Pos_delivery_model->get_deliveryOrder($invoiceID);
        $totalAdvance = $this->Pos_delivery_model->get_totalAdvance($invoiceID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['orderDetail'] = $orderDetail;
        $data['totalAdvance'] = $totalAdvance;
        $data['auth'] = true;

        $this->load->view('system/pos/printTemplate/restaurant-pos-advance-payment', $data);
    }

    function save_send_pos_email()
    {
        $invoiceID = $this->input->post('menuSalesID');
        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID);
        $orderDetail = $this->Pos_delivery_model->get_deliveryOrder($invoiceID);
        $totalAdvance = $this->Pos_delivery_model->get_totalAdvance($invoiceID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['orderDetail'] = $orderDetail;
        $data['totalAdvance'] = $totalAdvance;
        $data['auth'] = true;
        $data['email'] = true;

        $msg = $this->load->view('system/pos/printTemplate/restaurant-pos-advance-payment', $data, true);
        $this->Pos_delivery_model->send_pos_email_advancePayment_deilvery($msg);
    }

    function delivery_dispatchOrder()
    {
        $deliveryOrderID = $this->input->post('deliveryOrderID');
        if ($deliveryOrderID) {
            $orderInfo = $this->Pos_delivery_model->get_deliveryOrder_by_deliveryOrderID($deliveryOrderID);
            if (!empty($orderInfo)) {

                /** Total Bill Amount */
                $this->db->select('*');
                $this->db->from('srp_erp_pos_menusalesmaster');
                $this->db->where('menuSalesID', $orderInfo['menuSalesMasterID']);
                $totalBillAmount = $this->db->get()->row('subTotal');

                /** Total Paid Amount*/
                $q = "SELECT SUM(amount) as totalPaid FROM srp_erp_pos_menusalespayments WHERE menuSalesID = '" . $orderInfo['menuSalesMasterID'] . "'";
                $totalPaid = $this->db->query($q)->row('totalPaid');
                if ($totalPaid) {

                    if (round($totalBillAmount, 1) == round($totalPaid, 1)) {
                        $dataMS['isHold'] = 0;
                        $dataMS['modifiedPCID'] = current_pc();
                        $dataMS['modifiedUserID'] = current_userID();
                        $dataMS['modifiedUserName'] = current_user();
                        $dataMS['modifiedDateTime'] = format_date_mysql_datetime();
                        $this->db->where('menuSalesID', $orderInfo['menuSalesMasterID']);
                        $result = $this->db->update('srp_erp_pos_menusalesmaster', $dataMS);

                        $dataDelivery['isDispatched'] = 1;
                        $dataDelivery['dispatchedDatetime'] = format_date_mysql_datetime();
                        $dataDelivery['dispatchedBy'] = current_userID();
                        $this->db->where('deliveryOrderID', $deliveryOrderID);
                        $this->db->update('srp_erp_pos_deliveryorders', $dataDelivery);
                        /*if ($result) {

                        } else {
                            echo json_encode(array('error' => 1, 'message' => 'Error has occurred.'));
                        }*/

                        if ($result) {
                            echo json_encode(array('error' => 0, 'message' => 'Order successfully Dispatched'));
                        } else {
                            echo json_encode(array('error' => 1, 'message' => 'Error2 has occurred.'));
                        }
                    } else {
                        $balance = $totalBillAmount - $totalPaid;
                        echo json_encode(array('error' => 2, 'message' => '<strong>This bill is not fully paid!</strong><br/>Please completed the remaining payment of ' . $balance . ' dispatch the order, ' . '<br/>Bill Amount:  ' . $totalBillAmount . '<br/> Total Paid : ' . $totalPaid));
                    }
                } else {
                    echo json_encode(array('error' => 2, 'message' => '<strong>Payment not submitted</strong><br/>Please complete the payment and dispatch this order.'));
                }


            } else {
                echo json_encode(array('error' => 1, 'message' => 'Error has occurred'));
            }

        } else {
            echo json_encode(array('error' => 1, 'message' => 'delivery order not confirmed!'));
        }

    }

}