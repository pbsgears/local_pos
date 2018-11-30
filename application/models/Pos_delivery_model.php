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

class Pos_delivery_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }


    function confirm_delivery_order()
    {
        $outletID = get_outletID();
        $this->db->trans_start();
        try {
            $deliveryOrderID = $this->input->post('deliveryOrderID');
            if (!$deliveryOrderID) {

                $phone = $this->input->post('phone');
                $posCustomerAutoID = $this->db->select('posCustomerAutoID')->from('srp_erp_pos_customermaster')->where('customerTelephone', $phone)->get()->row('posCustomerAutoID');

                $companyID = current_companyID();
                $companyCode = current_companyCode();
                $curDate = format_date_mysql_datetime();
                $pc = current_pc();
                $userID = current_userID();
                $user = current_user();
                $userGroup = user_group();
                $companyInfo = get_companyInfo();
                $country = $companyInfo['company_country'];
                $tmpDob = $this->input->post('DOB');
                $dob = !empty($tmpDob) ? date('Y-m-d', strtotime(str_replace('/', '-', $tmpDob))) : null;
                $deliveryDate = date('Y-m-d', strtotime(str_replace('/', '-', trim($this->input->post('deliveryDate')))));

                $tmpDeliveryTime = $this->input->post('deliveryTime');

                if (!empty($tmpDeliveryTime)) {
                    $date = DateTime::createFromFormat('H:i A', trim($this->input->post('deliveryTime')));
                    $formattedDeliveryTime = $date->format('H:i:s');
                } else {
                    $formattedDeliveryTime = null;
                }


                $invoiceID = isPos_invoiceSessionExist();
                if (!$invoiceID) {
                    /** Create New Order */
                    $this->create_order();
                    $invoiceID = isPos_invoiceSessionExist();
                }


                /**Create New Customer  */
                if (!$posCustomerAutoID) {
                    /** New Customer */
                    $c_data['wareHouseAutoID'] = $outletID;
                    $c_data['CustomerName'] = trim($this->input->post('CustomerName'));
                    $c_data['DOB'] = $dob;
                    $c_data['CustomerAddress1'] = trim($this->input->post('CustomerAddress1'));
                    $c_data['customerAddress1Type'] = trim($this->input->post('customerAddress1Type'));
                    $c_data['customerAddress2'] = trim($this->input->post('CustomerAddress2'));
                    $c_data['customerAddress2Type'] = trim($this->input->post('customerAddress2Type'));
                    $c_data['customerCountry'] = $country;
                    $c_data['customerTelephone'] = trim($this->input->post('phone'));
                    $c_data['customerEmail'] = trim($this->input->post('email'));
                    $c_data['isActive'] = 1;
                    $c_data['companyID'] = $companyID;
                    $c_data['companyCode'] = $companyCode;
                    $c_data['createdUserGroup'] = $userGroup;
                    $c_data['createdPCID'] = $pc;
                    $c_data['createdUserID'] = $userID;
                    $c_data['createdUserName'] = $user;
                    $c_data['createdDateTime'] = $curDate;
                    $c_data['timestamp'] = $curDate;
                    $c_data['isFromDelivery'] = 1;
                    $c_data['isFromERP'] = 0;
                    $c_data['is_sync'] = 0;

                    $this->session->userdata();

                    $this->db->insert('srp_erp_pos_customermaster', $c_data);
                    $posCustomerAutoID = $this->db->insert_id();
                } else {
                    $c_data['wareHouseAutoID'] = $outletID;
                    $c_data['CustomerName'] = trim($this->input->post('CustomerName'));
                    $c_data['DOB'] = $dob;
                    $c_data['CustomerAddress1'] = trim($this->input->post('CustomerAddress1'));
                    $c_data['customerAddress1Type'] = trim($this->input->post('customerAddress1Type'));
                    $c_data['customerAddress2'] = trim($this->input->post('CustomerAddress2'));
                    $c_data['customerAddress2Type'] = trim($this->input->post('customerAddress2Type'));
                    $c_data['customerCountry'] = $country;
                    $c_data['customerTelephone'] = trim($this->input->post('phone'));
                    $c_data['customerEmail'] = trim($this->input->post('email'));
                    $c_data['modifiedPCID'] = $pc;
                    $c_data['modifiedUserID'] = $userID;
                    $c_data['modifiedUserName'] = $user;
                    $c_data['modifiedDateTime'] = $curDate;
                    $c_data['is_sync'] = 0;


                    $this->db->where('posCustomerAutoID', $posCustomerAutoID);
                    $this->db->update('srp_erp_pos_customermaster', $c_data);
                }

                /** Create Delivery */
                $data_d['wareHouseAutoID'] = $outletID;
                $data_d['deliveryDate'] = $deliveryDate;
                $data_d['deliveryTime'] = $formattedDeliveryTime;
                $data_d['menuSalesMasterID'] = $invoiceID;
                $data_d['posCustomerAutoID'] = $posCustomerAutoID;
                $data_d['phoneNo'] = trim($this->input->post('phone'));;
                $data_d['email'] = trim($this->input->post('email'));
                $data_d['deliveryCharges'] = trim($this->input->post('deliveryCharges'));
                $data_d['deliveryType'] = trim($this->input->post('deliveryType'));
                $data_d['crewMemberID'] = null;
                $data_d['landMarkLocation'] = trim($this->input->post('landMarkLocation'));
                $data_d['companyID'] = $companyID;
                $data_d['companyCode'] = $companyCode;
                $data_d['createdUserGroup'] = $userGroup;
                $data_d['createdPCID'] = $pc;
                $data_d['createdUserID'] = $userID;
                $data_d['createdUserName'] = $user;
                $data_d['createdDateTime'] = $curDate;
                $data_d['timestamp'] = $curDate;
                $data_d['is_sync'] = 1;
                $result = $this->db->insert('srp_erp_pos_deliveryorders', $data_d);
                if (!$result) {
                    $this->db->trans_rollback();
                    echo json_encode(array('error' => 1, 'message' => 'error, please contact your support team' . $this->db->_error_message()));
                    exit;
                }
                $deliveryOrderID = $this->db->insert_id();
                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('error' => 1, 'message' => 'error, please contact your support team' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('error' => 0, 'message' => 'order successfully created ', 'orderID' => $deliveryOrderID, 'customerID' => $posCustomerAutoID, 'tmpInvoiceID_code' => get_pos_invoice_code($invoiceID), 'tmpInvoiceID' => $invoiceID, 'code' => 1));
                }
            } else {

                $order = $this->db->select('*')->from('srp_erp_pos_deliveryorders')->where('deliveryOrderID', $deliveryOrderID)->get()->row_array();
                echo json_encode(array('error' => 0, 'message' => 'order already confirmed, please add items and process the order', 'orderID' => $deliveryOrderID, 'customerID' => $order['posCustomerAutoID'], 'tmpInvoiceID_code' => get_pos_invoice_code($deliveryOrderID), 'tmpInvoiceID' => $order['menuSalesMasterID'], 'code' => 2));
            }


        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(array('error' => 1, 'message' => 'Caught exception: ', $e->getMessage()));
            exit;
        }
    }

    function create_order()
    {
        $get_shift = $this->Pos_restaurant_model->get_srp_erp_pos_shiftdetails_employee();
        if (!empty($get_shift)) {
            $warehouseDetail = $this->get_wareHouse();

            $SN = generate_pos_invoice_no();
            $data['customerTypeID'] = $this->input->post('customerTypeID');
            $data['serialNo'] = $SN;
            $data['invoiceSequenceNo'] = $SN;
            $data['invoiceCode'] = generate_pos_invoice_code();
            $data['counterID'] = $get_shift['counterID'];
            $data['shiftID'] = $get_shift['shiftID'];
            $data['menuSalesDate'] = format_date_mysql_datetime();
            $data['companyID'] = current_companyID();
            $data['companyCode'] = current_companyCode();
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
            $data['timestamp'] = format_date_mysql_datetime();
            $data['is_sync'] = 1;
            $data['id_store'] = $this->config->item('id_store');

            $invoiceID = $this->Pos_restaurant_model->insert_srp_erp_pos_menusalesmaster($data);
            if ($invoiceID) {
                set_session_invoiceID($invoiceID);
            }
        }
    }

    function get_wareHouse()
    {
        $this->db->select('wHouse.wareHouseAutoID, wareHouseCode, wareHouseDescription, wareHouseLocation, segmentID, segmentCode')
            ->from('srp_erp_warehousemaster wHouse')
            ->join('srp_erp_pos_segmentconfig conf', 'conf.wareHouseAutoID=wHouse.wareHouseAutoID', 'left')
            ->where('wHouse.wareHouseAutoID', $this->common_data['ware_houseID']);
        return $this->db->get()->row_array();
    }

    function get_deliveryOrder($invoiceID)
    {
        $this->db->select('orders.*,customer.CustomerName,customer.CustomerAddress1,customer.customerCountry');
        $this->db->from('srp_erp_pos_deliveryorders orders');
        $this->db->join('srp_erp_pos_customermaster customer', 'customer.posCustomerAutoID =orders.posCustomerAutoID ', 'left');
        $this->db->where('orders.menuSalesMasterID', $invoiceID);
        $result = $this->db->get()->row_array();
        return $result;
    }

    function get_deliveryOrder_by_deliveryOrderID($deliveryOrderID)
    {
        $this->db->select('orders.*,customer.CustomerName,customer.CustomerAddress1,customer.customerCountry');
        $this->db->from('srp_erp_pos_deliveryorders orders');
        $this->db->join('srp_erp_pos_customermaster customer', 'customer.posCustomerAutoID =orders.posCustomerAutoID ', 'left');
        $this->db->where('orders.deliveryOrderID', $deliveryOrderID);
        $result = $this->db->get()->row_array();
        return $result;
    }


    function get_totalAdvance($invoiceID)
    {
        $q = "SELECT SUM(amount) as tmpAmount FROM srp_erp_pos_menusalespayments WHERE menuSalesID=  '" . $invoiceID . "'";
        $amount = $this->db->query($q)->row('tmpAmount');
        return $amount;
    }

    function send_pos_email_advancePayment_deilvery($msg)
    {
        $emal = $this->input->post('emailAddress');
        $this->load->library('MY_PHPMailer');
        $mail = new MY_PHPMailer(); // create a new object
        $mail->AddEmbeddedImage(' ../lib/dist/img/VotexsupportDesk.png', 'logo_2u');
        //$mail->AddEmbeddedImage(' ../lib/dist/img/VotexLogo.png', 'logo');

        $mail->IsSMTP(); // enable SMTP
        $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
        $mail->Host = "smtpout.secureserver.net";
        $mail->Port = 465; // or 587
        $mail->IsHTML(true);
        $mail->Username = "support_admin@xupportcloud.com";
        $mail->Password = "P@ssw0rd240!";
        $mail->SetFrom("support_admin@xupportcloud.com");
        $mail->Subject = "Receipt";
        $mail->Body = $msg;
        $mail->AddAddress("$emal");
        $sen = 0;
        if ($sen == 0) {
            $sen = $sen + 1;
            $mailsend = $mail->Send();
        }
        if (!$mail->Send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo json_encode(array("error" => 0, "message" => "Mail sent"));
        }

    }

}