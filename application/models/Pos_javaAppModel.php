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

class Pos_javaAppModel extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function check_pinExist()
    {
        $pin = $this->input->post('appPIN');
        $this->db->select('JApp.*,wm.wareHouseDescription, wm.wareHouseLocation ');
        $this->db->from('srp_erp_pos_javaappredeemhistory JApp');
        $this->db->join('srp_erp_warehousemaster wm', 'wm.wareHouseAutoID = JApp.outletID');
        $this->db->where('appPIN', $pin);
        $result = $this->db->get()->row_array();
        if (!empty($result)) {
            //print_r($result);
            return array('error' => 2, 'message' => "The entered PIN code is already used. \n \n Outlet:" . $result['wareHouseDescription'] . " \n Location: " . $result['wareHouseLocation'] . " \n Bill No." . $result['menuSalesID']);
        } else {

            $netAmount = $this->input->post('netAmount');
            $paid = $this->input->post('paid');
            $balanceAmount = $netAmount - $paid;

            $outletID = get_outletID();

            switch ($outletID) {
                case 5:
                    $amount_maxRedeemAmount = 200;
                    break;
                default:
                    $amount_maxRedeemAmount = 500;
            }

            if ($balanceAmount > $amount_maxRedeemAmount) {
                $redeemableAmount = $amount_maxRedeemAmount;
            } else {
                $redeemableAmount = $balanceAmount;
            }
            return array('error' => 0, 'redeemAmount' => $redeemableAmount, 'outletID' => get_outletID());
        }
    }

    function insert_srp_erp_pos_javaAppRedeemHistory($data)
    {
        $result = $this->db->insert('srp_erp_pos_javaappredeemhistory', $data);
        return $result;
    }


}