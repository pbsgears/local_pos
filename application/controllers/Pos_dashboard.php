<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* -- =============================================
-- File Name : Pos_dashboard.php
-- Project Name : SME
-- Module Name : POS restaurant Dashboard
-- Author : Mohamed Shafri
-- Create date : 21 - November 2016
-- Description : SME POS System for restaurant.

--REVISION HISTORY
--Date: 21 - NOV 2016 By: Mohamed Shafri: comment started



-- =============================================
 */


class Pos_dashboard extends ERP_Controller
{


    function __construct()
    {
        parent::__construct();
        $this->load->model('Pos_dashboard_model');
        $this->load->helper('cookie');
        $this->load->helper('pos');
    }

    function sales_lastSevenDays($filter = null)
    {

        $begin = new DateTime("6 days ago");
        $end = new DateTime(date('Y-m-d') - "1 day");
        $interval = new DateInterval('P1D'); // 1 Day interval
        $period = new DatePeriod($begin, $interval, $end); // 7 Days

        //var_dump($begin->format('Y-m-d') );

        $days = array();

        $i = 1;
        foreach ($period as $day) {
            $days[$i]['day'] = $day->format('l');
            $days[$i]['amount'] = 0;
            $i++;
        }


        $result = $this->Pos_dashboard_model->getLastSevenDaySales($filter);


        foreach ($days as $tmpkey => $day) {
            if (empty($result)) {
                break;
            }
            $key = array_search($day['day'], array_column($result, 'salesDay'));

            if ($key || $key === 0) {
                $TmpTotalSales = $result[$key]['totalSales'];
                $tmpSalesDay = $result[$key]['salesDay'];

                if ($tmpSalesDay == $day['day']) {
                    $days[$tmpkey]['amount'] += $TmpTotalSales;
                }
            }



        }


        return $days;
    }

    function sales_YTD($filter = null)
    {
        $begin = new DateTime("6 days ago");
        $end = new DateTime(date('Y-m-d') - "1 day");
        $interval = new DateInterval('P1D'); // 1 Day interval
        $period = new DatePeriod($begin, $interval, $end); // 7 Days

        $days = array();
        foreach ($period as $day) {
            $key = $day->format('l');
            $days[$key] = 0;
        }


        $result = $this->Pos_dashboard_model->getYTDSales($filter);


        foreach ($days as $tmpkey => $day) {
            if (isset($result) && !empty($result)) {
                $key = array_search($tmpkey, array_column($result, 'salesDay'));
                //echo 'key: ' . $key . '<br/>';

                if ($key >= 0) {
                    $tmpAmount = $result[$key]['totalSales'];
                    //echo '-- ' . $tmpkey . ' - ' . $key . '<br/>';
                    $days[$tmpkey] = number_format($tmpAmount, 2, '.', '');
                }
            }
        }


        /*echo '<pre>';
        print_r($days);
        echo '</pre>';
        exit;*/

        return $days;
    }

    function sales_YTD_dayCount()
    {
        $begin = new DateTime("6 days ago");
        $end = new DateTime(date('Y-m-d') - "1 day");
        $interval = new DateInterval('P1D'); // 1 Day interval
        $period = new DatePeriod($begin, $interval, $end); // 7 Days

        $days = array();
        foreach ($period as $day) {
            $key = $day->format('l');
            $days[$key] = 0;
        }

        $result = $this->Pos_dashboard_model->getYTDSales_dayCount();

        if (!empty($result)) {
            foreach ($result as $val) {
                if (isset($days[$val['salesDay']])) {
                    $days[$val['salesDay']]++;
                }
            }
        }

        return $days;
    }

    function loadDashboard_sales_YTD()
    {
        $warehouseID = $this->input->post('warehouseID');
        $filter['warehouseID'] = $warehouseID;
        $data['sales_SevenDays'] = $this->sales_lastSevenDays($filter);
        $data['sales_YTD'] = $this->sales_YTD($filter);
        $data['sales_YTD_dayCount'] = $this->sales_YTD_dayCount();

        /*echo '<pre>';
        print_r($data);
        echo '</pre>';*/
        $this->load->view('system/pos/posRestaurant/dashboard/ajax-sales-ytd-average', $data);
    }

    function load_REVPASH()
    {
        /* get net based on interval */
        $data['yesterday'] = $this->Pos_dashboard_model->get_REVPASH('yesterday');
        $data['WTD'] = $this->Pos_dashboard_model->get_REVPASH('WTD');
        $data['MTD'] = $this->Pos_dashboard_model->get_REVPASH('MTD');
        $data['YTD'] = $this->Pos_dashboard_model->get_REVPASH('YTD');
        $this->load->view('system/pos/posRestaurant/dashboard/ajax-load-revpash', $data);
    }

    function load_profitVsSales()
    {
        $data['yesterday'] = $this->Pos_dashboard_model->get_sales_profit('yesterday');
        $data['WTD'] = $this->Pos_dashboard_model->get_sales_profit('WTD');
        $data['MTD'] = $this->Pos_dashboard_model->get_sales_profit('MTD');
        $data['YTD'] = $this->Pos_dashboard_model->get_sales_profit('YTD');

        $this->load->view('system/pos/posRestaurant/dashboard/profitVsSales', $data);
    }

    function load_profitVsSales_generalPOS()
    {
        $data['yesterday'] = $this->Pos_dashboard_model->get_sales_profit_generalPOS('yesterday');
        $data['WTD'] = $this->Pos_dashboard_model->get_sales_profit_generalPOS('WTD');
        $data['MTD'] = $this->Pos_dashboard_model->get_sales_profit_generalPOS('MTD');
        $data['YTD'] = $this->Pos_dashboard_model->get_sales_profit_generalPOS('YTD');

        $this->load->view('system/pos/posRestaurant/dashboard/profitVsSales', $data);
    }

    function load_invoicePax()
    {

        $data['yesterday'] = $this->Pos_dashboard_model->get_paxCount('yesterday');
        $data['WTD'] = $this->Pos_dashboard_model->get_paxCount('WTD');
        $data['MTD'] = $this->Pos_dashboard_model->get_paxCount('MTD');
        $data['YTD'] = $this->Pos_dashboard_model->get_paxCount('YTD');
        $this->load->view('system/pos/posRestaurant/dashboard/load_invoicePax', $data);
    }

    function load_fastMovingItemByValue()
    {

        $data['yesterday'] = 0; // $this->Pos_dashboard_model->getFastMovingItems('yesterday');
        $data['WTD'] = 0; //$this->Pos_dashboard_model->getFastMovingItems('WTD');
        $data['MTD'] = 0; //$this->Pos_dashboard_model->getFastMovingItems('MTD');
        $data['result'] = $this->Pos_dashboard_model->getFastMovingItems('YTD');
        $this->load->view('system/pos/posRestaurant/dashboard/fastMovingItem_byValue', $data);
    }

    function loadGeneralDashboard_sales_YTD()
    {
        $data['sales_SevenDays'] = $this->sales_lastSevenDaysGeneral();
        $this->load->view('system/pos/posGeneral/dashboard/ajax-general-sales-ytd-average', $data);
    }

    function sales_lastSevenDaysGeneral()
    {

        $begin = new DateTime("6 days ago");
        $end = new DateTime(date('Y-m-d') - "1 day");
        $interval = new DateInterval('P1D'); // 1 Day interval
        $period = new DatePeriod($begin, $interval, $end); // 7 Days
        $days = array();
        $i = 1;
        foreach ($period as $day) {
            $days[$i]['day'] = $day->format('l');
            $days[$i]['amount'] = 0;
            $i++;
        }
        $result = $this->Pos_dashboard_model->getLastSevenDaySalesGeneral();
        foreach ($days as $tmpkey => $day) {
            if (empty($result)) {
                break;
            }
            $key = array_search($day['day'], array_column($result, 'invoiceDate'));
            if ($key || $key === 0) {
                $TmpTotalSales = $result[$key]['totalSales'];
                $tmpSalesDay = $result[$key]['invoiceDate'];

                if ($tmpSalesDay == $day['day']) {
                    $days[$tmpkey]['amount'] += $TmpTotalSales;
                }
            }

        }
        return $result;
    }

    function loadGeneralPaymentSalesReport()
    {
        $tmpFilterDate = $this->input->post('filterFrom');
        $tmpFilterDateTo = $this->input->post('filterTo');
        $tmpCashierSource = $this->input->post('cashier');
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

        $companyInfo = $this->Pos_dashboard_model->get_currentCompanyDetail();
        $customerTypeCount = $this->Pos_dashboard_model->get_report_generalCustomerTypeCount($filterDate, $date2);
        $paymentMethod = $this->Pos_dashboard_model->get_report_generalPaymentMethod($filterDate, $date2, $cashier);
        $isRefund = $this->Pos_dashboard_model->get_report_generalRefundMethod($filterDate, $date2, $cashier);
        $data['companyInfo'] = $companyInfo;
        $data['customerTypeCount'] = $customerTypeCount;
        $data['paymentMethod'] = $paymentMethod;
        $data['isRefund'] = $isRefund;
        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();
        $this->load->view('system/pos/generalReports/pos-general-payment-sales-report', $data);
    }

    function loadGeneralPaymentSalesReportPdf()
    {
        $tmpFilterDate = $this->input->post('filterFrom');
        $tmpFilterDateTo = $this->input->post('filterTo');
        $tmpCashierSource = $this->input->post('cashier');


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

        $companyInfo = $this->Pos_dashboard_model->get_currentCompanyDetail();
        $customerTypeCount = $this->Pos_dashboard_model->get_report_generalCustomerTypeCount($filterDate, $date2);
        $paymentMethod = $this->Pos_dashboard_model->get_report_generalPaymentMethod($filterDate, $date2, $cashier);
        $data['companyInfo'] = $companyInfo;
        $data['customerTypeCount'] = $customerTypeCount;
        $data['paymentMethod'] = $paymentMethod;
        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();


        $html = $this->load->view('system/pos/generalReports/pos-payment-general-sales-report-pdf', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }

    function loadGeneralItemizedSalesReport()
    {
        $tmpFromDate = $this->input->post('filterFrom');

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


        $companyInfo = $this->Pos_dashboard_model->get_currentCompanyDetail();
        $itemizedSalesReport = $this->Pos_dashboard_model->get_itemizedSalesReport($dateFrom, $dateTo);

        $data['companyInfo'] = $companyInfo;
        $data['itemizedSalesReport'] = $itemizedSalesReport;


        $this->load->view('system/pos/generalReports/pos-general-itemized-sales-report', $data);
    }

    function loadGeneralItemizedSalesReportPdf()
    {
        $tmpFromDate = $this->input->post('filterFrom');

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


        $companyInfo = $this->Pos_dashboard_model->get_currentCompanyDetail();
        $itemizedSalesReport = $this->Pos_dashboard_model->get_itemizedSalesReport($dateFrom, $dateTo);

        $data['companyInfo'] = $companyInfo;
        $data['itemizedSalesReport'] = $itemizedSalesReport;

        $html = $this->load->view('system/pos/generalReports/pos-general-itemized-sales-report-pdf', $data, true);
        //echo $html;
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }

    /* function load_general_profitVsSales()
     {
         $data['yesterday'] = $this->Pos_dashboard_model->get_sales_profit('yesterday');
         $data['WTD'] = $this->Pos_dashboard_model->get_general_sales_profit('WTD');
         $data['MTD'] = $this->Pos_dashboard_model->get_general_sales_profit('MTD');
         $data['YTD'] = $this->Pos_dashboard_model->get_general_sales_profit('YTD');

         $this->load->view('system/pos/posRestaurant/dashboard/profitVsSales', $data);
     }*/

    function load_fastMovingItemByValueMix()
    {

        $tmpFromDate = date('Y-01-01');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        $filterTo = current_date();
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d');
        }


        //$companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $productMix_menuItem = $this->Pos_dashboard_model->productMix_menuItem($dateFrom, $dateTo);
        $get_packs_sales = $this->Pos_dashboard_model->get_productMixPacks_sales($dateFrom, $dateTo);

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
        $result2 = array();
        foreach ($m as $data) {
            $id = $data['menuID'];
            if (isset($result[$id])) {
                $result[$id][] = $data;
            } else {
                $result[$id] = array($data);
            }
        }

        foreach ($result as $data) {
            foreach ($data as $val) {
                $result2[] = $val;
            }
        }

        $newarray = array();
        foreach ($result2 as $ar) {
            foreach ($ar as $k => $v) {
                if (array_key_exists($v, $newarray))
                    $newarray[$v]['qty'] = $newarray[$v]['qty'] + $ar['qty'];
                else if ($k == 'menuID')
                    $newarray[$v] = $ar;
            }
        }

        $result = $this->array_msort($newarray, array('qty' => SORT_DESC));

        $data['productMix'] = $result;

        $this->load->view('system/pos/posRestaurant/dashboard/fastMovingItem_byValue', $data);
    }


    function load_fastMovingItemByValueMix_generalPOS()
    {

        $tmpFromDate = date('Y-01-01');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        $filterTo = current_date();
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d');
        }

        $data['productMix'] = $this->Pos_dashboard_model->productMix_menuItem_generalPOS($dateFrom, $dateTo);

        $this->load->view('system/pos-general/reports/fast-moving-Items-top15', $data);
    }

    function array_msort($array, $cols)
    {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) {
                $colarr[$col]['_' . $k] = strtolower($row[$col]);
            }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\'' . $col . '\'],' . $order . ',';
        }
        $eval = substr($eval, 0, -1) . ');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k, 1);
                if (!isset($ret[$k])) $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;
    }
}