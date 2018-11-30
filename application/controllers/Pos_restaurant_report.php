<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* -- =============================================
-- File Name : Pos_restaurant_report.php
-- Project Name : POS
-- Module Name : POS Restaurant Report Controller
-- Author : Mohamed Shafri
-- Create date : 19 - April 2018
-- Description : SME POS Report .

--REVISION HISTORY
--Date: 19 - April 2018 By: Mohamed Shafri: comment started



-- =============================================
 */


class Pos_restaurant_report extends ERP_Controller
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

    function load_collection_detail_report()
    {
        $tmpFromDate = $this->input->post('startdate');
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
        $collection_detail = $this->Pos_restaurant_model->get_paymentCollection($dateFrom, $dateTo, $Outlets, $cashier);

        $data['companyInfo'] = $companyInfo;
        $data['collection_detail'] = $collection_detail;

        $this->load->view('system/pos/reports/pos-collection-detail-report', $data);
    }


}

