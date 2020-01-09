<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* -- =============================================
-- File Name : Pos_general_report.php
-- Project Name : POS General
-- Module Name : General POS Controller
-- Author : Mohamed Shafri
-- Create date : 29 - June 2018
-- Description : SME POS System.

--REVISION HISTORY
--Date: 29 - June 2016 By: Mohamed Shafri: comment started

-- =============================================
 */


class Pos_general_report extends ERP_Controller
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
        $this->load->model('Pos_general_report_model');
        $this->load->model('Inventory_modal');
        $this->load->model('Pos_restaurant_accounts');
        $this->load->model('Pos_restaurant_accounts_gl_fix');
        $this->load->helper('cookie');
        $this->load->helper('pos');
    }

    function generate_pdf($html = '')
    {
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }


    function sales_report_pdf()
    {
        $result = $this->load_gpos_PaymentSalesReportAdmin(true, true);
        $this->generate_pdf($result);
    }

    function load_gpos_PaymentSalesReportAdmin($html = false, $pdf = false)
    {
        $data['pdf'] = $pdf;
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


        $lessAmounts_discounts = $this->Pos_general_report_model->get_report_salesReport_discount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_general_discounts = $this->Pos_general_report_model->get_report_salesReport_general_discount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmountsAll = array_merge($lessAmounts_general_discounts, $lessAmounts_discounts);

        $data['companyInfo'] = $this->Pos_general_report_model->get_currentCompanyDetail();
        $data['paymentMethod'] = $this->Pos_general_report_model->get_report_paymentMethod_admin($filterDate, $date2, $cashier, $outlets);
        $data['creditSales'] = $this->Pos_general_report_model->get_report_credit_sales_admin($filterDate, $date2, $cashier, $outlets);
        $data['customerTypeCount'] = $this->Pos_general_report_model->get_report_customerTypeCount_2_admin($filterDate, $date2, $cashier, $outlets);
        $data['lessAmounts'] = $lessAmountsAll;
        $data['totalSales'] = $this->Pos_general_report_model->get_report_salesReport_totalSales_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalTaxes'] = $this->Pos_general_report_model->get_report_salesReport_totalTaxes_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalServiceCharge'] = $this->Pos_general_report_model->get_report_salesReport_ServiceCharge_admin($filterDate, $date2, $cashier, $outlets);
        $data['giftCardTopUp'] = $this->Pos_general_report_model->get_report_giftCardTopUp_admin($filterDate, $date2, $cashier, $outlets);
        $data['voidBills'] = $this->Pos_general_report_model->get_report_voidBills_admin($filterDate, $date2, $cashier, $outlets);
        /*$data['creditSales'] = $this->Pos_general_report_model->get_report_creditSales($filterDate, $date2, $cashier, $outlets);*/

        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();
        /*echo '<pre>';
        print_r($data['creditSales']);
        exit;*/


        return $this->load->view('system/pos-general/reports/gpos-payment-sales-report-admin', $data, $html);
    }

    function get_gpos_outlet_cashier()
    {
        echo json_encode($this->Pos_general_report_model->get_gpos_outlet_cashier());
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

            $companyInfo = $this->Pos_general_report_model->get_currentCompanyDetail();
            $itemizedSalesReport = $this->Pos_general_report_model->get_item_wise_profitability_Report($filterDate, $date2, $Outlets, $cashier);


            $data['companyInfo'] = $companyInfo;
            $data['reportData'] = $itemizedSalesReport;
            $data['cashier'] = $tmpCashierSource;
            $data['cashierTmp'] = get_cashiers();


            $this->load->view('system/pos-general/reports/posg-item-wise-profitability-report-admin', $data);
        }
    }


    function load_gpos_detail_sales_report()
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


        $data['companyInfo'] = $this->Pos_general_report_model->get_currentCompanyDetail();
        $data['reportData'] = $this->Pos_general_report_model->get_gpos_detail_sales_report($filterDate, $date2, $cashier, $outlets);
        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();


        $this->load->view('system/pos-general/reports/gpos-sales-detail-report', $data);
    }

}

