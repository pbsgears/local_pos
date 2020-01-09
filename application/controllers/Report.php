<?php

/*
-- =============================================
-- File Name : Report.php
-- Project Name : SME ERP
-- Module Name : Report
-- Author : Mohamed Mubashir
-- Create date : 15 - September 2016
-- Description : This file contains all the report module generation function.

-- REVISION HISTORY
-- =============================================*/

class Report extends ERP_Controller
{
    public $format;

    function __construct()
    {
        parent::__construct();
        $this->format = convert_date_format_sql();
        $this->load->model('Report_model');
        $this->load->helper('report');
        //$this->load->driver('cache', array('adapter' => 'xcache'));
    }

    function get_item_filter()/*item ledger,valuation,counting*/
    {
        $data = array();
        $data["columns"] = $this->Report_model->getColumnsByReport($this->input->post('reportID'));
        $data["formName"] = $this->input->post('formName');
        $data["reportID"] = $this->input->post('reportID');
        $data["type"] = $this->input->post('type');
        $this->load->view('system/inventory/report/erp_item_filter', $data);
    }

    function get_finance_filter() /*Trial balance*/
    {
        $data = array();
        $data["columns"] = $this->Report_model->getColumnsByReport($this->input->post('reportID'));
        $data["formName"] = $this->input->post('formName');
        $data["reportID"] = $this->input->post('reportID');
        $data["type"] = $this->input->post('type');
        $this->load->view('system/finance/report/erp_finance_filter', $data);
    }

    function get_procurement_filter() /*PO List*/
    {
        $data = array();
        $data["columns"] = $this->Report_model->getColumnsByReport($this->input->post('reportID'));
        $data["formName"] = $this->input->post('formName');
        $data["reportID"] = $this->input->post('reportID');
        $data["type"] = $this->input->post('type');
        $this->load->view('system/procurement/report/erp_procurement_filter', $data);
    }

    function get_accounts_payable_filter() /*Vendor Ledger,Vendor Statement,Vendor Aging Summary,Vendor Aging Detail*/
    {
        $data = array();
        $data["columns"] = $this->Report_model->getColumnsByReport($this->input->post('reportID'));
        $data["formName"] = $this->input->post('formName');
        $data["reportID"] = $this->input->post('reportID');
        $data["type"] = $this->input->post('type');
        $this->load->view('system/accounts_payable/report/erp_accounts_payable_filter', $data);
    }

    function get_accounts_receivable_filter() /*Customer Ledger,Customer Statement,Customer Aging Summary,Customer Aging Detail*/
    {
        $data = array();
        $data["columns"] = $this->Report_model->getColumnsByReport($this->input->post('reportID'));
        $data["formName"] = $this->input->post('formName');
        $data["reportID"] = $this->input->post('reportID');
        $data["type"] = $this->input->post('type');
        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_filter', $data);
    }

    function get_report_by_id()
    {
        switch ($this->input->post('reportID')) {
            case "ITM_LG": /*item ledger*/
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_ledger_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["type"] = "html";
                    $data["warehouse"] = $this->Report_model->get_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_LG', $this->input->post('fieldNameChk'));
                    $this->load->view('system/inventory/report/erp_item_ledger_report', $data);
                }
                break;
            case "INV_VAL": /*item valuation*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_valuation_summary_report();
                    $data["TotalAssetValue"] = $this->Report_model->get_item_valuation_summary_total_asset();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "html";
                    $data["warehouse"] = $this->Report_model->get_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_VAL', $this->input->post('fieldNameChk'));
                    $this->load->view('system/inventory/report/erp_item_valuation_summary_report', $data);
                }
                break;

            case "ITM_CNT": /*item counting*/
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["isSubItemExist"] = $this->input->post('isSubItemRequired');
                    $data["output"] = $this->Report_model->get_item_counting_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "html";
                    $data["warehouse"] = $this->Report_model->get_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_CNT', $this->input->post('fieldNameChk'));
                    $this->load->view('system/inventory/report/erp_item_counting_report', $data);
                }
                break;
            case "ITM_FM": /*item fast moving*/
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('rptType', 'Report Type', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_fast_moving_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_FM', $this->input->post('fieldNameChk'));
                    $this->load->view('system/inventory/report/erp_item_fast_moving_report', $data);
                }
                break;
            case "FIN_TB": /*Trial Balance*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($fieldNameChk) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_tb_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_TB', $this->input->post('fieldNameChk'));
                    if ($this->input->post('rptType') == 1) {
                        $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                        $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");
                        $this->load->view('system/finance/report/erp_finance_tb_month_wise_report', $data);
                    } else if ($this->input->post('rptType') == 3) {
                        $data["retain"] = $this->Report_model->get_finance_tb_retain();
                        $this->load->view('system/finance/report/erp_finance_tb_ytd_report', $data);
                    }
                }
                break;
            case "FIN_IS": /*Income statement*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($fieldNameChk) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_income_statement_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_TB', $this->input->post('fieldNameChk'));
                    if ($this->input->post('rptType') == 1) {
                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        $this->load->view('system/finance/report/erp_finance_income_statement_month_wise_report', $data);
                    } else if ($this->input->post('rptType') == 3) {
                        $this->load->view('system/finance/report/erp_finance_income_statement_ytd_report', $data);
                    } else if ($this->input->post('rptType') == 5) {
                        $this->load->view('system/finance/report/erp_finance_income_statement_ytd_budget_report', $data);
                    } else if ($this->input->post('rptType') == 4) {
                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        $this->load->view('system/finance/report/erp_finance_income_statement_month_wise_budget_report', $data);
                    }
                }
                break;
            case "FIN_BS": /*Balance sheet*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($fieldNameChk) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_balance_sheet_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_BS', $this->input->post('fieldNameChk'));
                    if ($this->input->post('rptType') == 1) {
                        $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                        $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");
                        $this->load->view('system/finance/report/erp_finance_balance_sheet_month_wise_report', $data);
                    } else if ($this->input->post('rptType') == 3) {
                        $this->load->view('system/finance/report/erp_finance_balance_sheet_ytd_report', $data);
                    }
                }
                break;
            case "FIN_GL": /*General Ledger */
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                $this->form_validation->set_rules('glCodeTo[]', 'GL Code', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_general_ledger_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_GL', $this->input->post('fieldNameChk'));
                    //$this->load->view('system/finance/report/erp_finance_general_ledger_report', $data);
                    //$this->load->view('system/finance/report/erp_finance_general_ledger_cd_report', $data);
                    $printlink = print_template_pdf('FIN_GL','system/finance/report/erp_finance_general_ledger_report');
                    $this->load->view($printlink, $data);
                }
                break;
            case "PROC_POL": /*PO List*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                $this->form_validation->set_rules('status', 'Status', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_procurement_purchase_order_list_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["status"] = $this->input->post('status');
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('PROC_POL', $this->input->post('fieldNameChk'));
                    $this->load->view('system/procurement/report/erp_procurement_purchase_order_list_report', $data);
                }
                break;
            case "INV_UBG": /*Unbilled GRV*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_inventory_unbilled_grv_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_UBG', $this->input->post('fieldNameChk'));
                    $this->load->view('system/inventory/report/erp_inventory_unbilled_grv_report', $data);
                }
                break;
            case "AP_VL": /*Vendor Ledger*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_accounts_payable_vendor_ledger_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VL', $this->input->post('fieldNameChk'));
                    $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report', $data);
                }
                break;
            case "AP_VS": /*Vendor Statement*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_accounts_payable_vendor_statement_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VS', $this->input->post('fieldNameChk'));
                    $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_statement_report', $data);
                }
                break;
            case "AP_VAS": /*Vendor Aging Summary*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);

                    $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_summary_report($aging);
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["aging"] = $aging;
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAS', $this->input->post('fieldNameChk'));
                    $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_summary_report', $data);
                }
                break;
            case "AP_VAD": /*Vendor Aging Detail*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) { /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);

                    $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_detail_report($aging);
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["aging"] = $aging;
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAD', $this->input->post('fieldNameChk'));
                    $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_detail_report', $data);
                }
                break;
            case "AR_CL": /*Customer Ledger*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_accounts_receivable_customer_ledger_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CL', $fieldNameChk);
                    $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report', $data);
                }
                break;
            case "AR_CS": /*Customer Statement*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                    $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report', $data);
                }
                break;
            case "AR_CAS": /*Customer Aging Summary*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);

                    $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_summary_report($aging);
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["aging"] = $aging;
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAS', $this->input->post('fieldNameChk'));
                    $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_summary_report', $data);
                }
                break;
            case "AR_CAD": /*Customer Aging Summary*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);

                    $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_detail_report($aging);
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["aging"] = $aging;
                    $data["type"] = "html";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAD', $this->input->post('fieldNameChk'));
                    $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_detail_report', $data);
                }
                break;
            case "INV_IIQ": /*Item Inquiry*/
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_inquiry_report();
                    $data["warehouse"] = load_location_drop();
                    $data["type"] = "html";
                    $this->load->view('system/inventory/report/erp_item_inquiry_report', $data);
                }
                break;
        }
    }


    function get_group_report_by_id()
    {
        switch ($this->input->post('reportID')) {
            case "PROC_POL": /*PO List*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('status', 'Status', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("SUPP"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_procurement_purchase_order_list_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["status"] = $this->input->post('status');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('PROC_POL', $this->input->post('fieldNameChk'));
                        $this->load->view('system/procurement/report/erp_procurement_purchase_order_list_report', $data);
                    }
                }
                break;
            case "ITM_LG": /*item ledger*/
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("ITM","WH"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_item_ledger_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "html";
                        $data["warehouse"] = $this->Report_model->get_group_warehouse();
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_LG', $this->input->post('fieldNameChk'));
                        $this->load->view('system/inventory/report/erp_item_ledger_report', $data);
                    }
                }
                break;
            case "INV_VAL": /*item valuation*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("ITM","WH"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_item_valuation_summary_group_report();
                        $data["TotalAssetValue"] = $this->Report_model->get_item_valuation_summary_total_asset_group();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["warehouse"] = $this->Report_model->get_group_warehouse();
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_VAL', $this->input->post('fieldNameChk'));
                        $this->load->view('system/inventory/report/erp_item_valuation_summary_report', $data);
                    }
                }
                break;
            case "ITM_CNT": /*item counting*/
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("ITM","WH"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["isSubItemExist"] = $this->input->post('isSubItemRequired');
                        $data["output"] = $this->Report_model->get_item_counting_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["warehouse"] = $this->Report_model->get_group_warehouse();
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_CNT', $this->input->post('fieldNameChk'));
                        $this->load->view('system/inventory/report/erp_item_counting_report', $data);
                    }
                }
                break;
            case "INV_UBG": /*Unbilled GRV*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                if (count($fieldNameChk) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("SUPP"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_inventory_unbilled_grv_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_UBG', $this->input->post('fieldNameChk'));
                        $this->load->view('system/inventory/report/erp_inventory_unbilled_grv_report', $data);
                    }
                }
                break;
            case "AR_CL": /*Customer Ledger*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("CA","CUST"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_ledger_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CL', $fieldNameChk);
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report', $data);
                    }
                }
                break;
            case "AR_CS": /*Customer Statement*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("CA","CUST"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                        $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report', $data);
                    }
                }
                break;
            case "AP_VL": /*Vendor Ledger*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("CA","SUPP"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_ledger_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VL', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report', $data);
                    }
                }
                break;
            case "AP_VS": /*Vendor Statement*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("CA","SUPP"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_accounts_payable_vendor_statement_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VS', $this->input->post('fieldNameChk'));
                        $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_statement_report', $data);
                    }
                }
                break;
            case "FIN_TB": /*Trial Balance*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_group_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($fieldNameChk) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("CA","SUPP","CUST","SEG"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_finance_tb_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_TB', $this->input->post('fieldNameChk'));
                        if ($this->input->post('rptType') == 1) {
                            $financialBeginingDate = get_group_financial_year(format_date($this->input->post("from")));
                            $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");
                            $this->load->view('system/finance/report/erp_finance_tb_month_wise_report', $data);
                        } else if ($this->input->post('rptType') == 3) {
                            $data["retain"] = $this->Report_model->get_finance_tb_group_retain();
                            $this->load->view('system/finance/report/erp_finance_tb_ytd_report', $data);
                        }
                    }
                }
                break;
            case "FIN_GL": /*General Ledger */
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                $this->form_validation->set_rules('glCodeTo[]', 'GL Code', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("CA","SUPP","CUST","SEG"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_finance_general_ledger_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_GL', $this->input->post('fieldNameChk'));
                        $this->load->view('system/finance/report/erp_finance_general_ledger_report', $data);
                    }
                }
                break;
            case "FIN_BS": /*Balance sheet*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_group_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($fieldNameChk) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("CA","SUPP","CUST","SEG"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_finance_balance_sheet_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_BS', $this->input->post('fieldNameChk'));
                        if ($this->input->post('rptType') == 1) {
                            $financialBeginingDate = get_group_financial_year(format_date($this->input->post("from")));
                            $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");
                            $this->load->view('system/finance/report/erp_finance_balance_sheet_month_wise_report', $data);
                        } else if ($this->input->post('rptType') == 3) {
                            $this->load->view('system/finance/report/erp_finance_balance_sheet_ytd_report', $data);
                        }
                    }
                }
                break;
            case "FIN_IS": /*Income statement*/
                $fieldNameChk = $this->input->post('fieldNameChk');
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($fieldNameChk) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $errorHTML = $this->group_unlink(array("CA","SUPP","CUST","SEG"));
                    if ($errorHTML) {
                        echo warning_message($errorHTML);
                    } else {
                        $data = array();
                        $data["output"] = $this->Report_model->get_finance_income_statement_group_report();
                        $data["caption"] = $this->input->post('captionChk');
                        $data["fieldName"] = $this->input->post('fieldNameChk');
                        $data["from"] = $this->input->post('from');
                        $data["to"] = $this->input->post('to');
                        $data["type"] = "html";
                        $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_TB', $this->input->post('fieldNameChk'));
                        if ($this->input->post('rptType') == 1) {
                            $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                            $this->load->view('system/finance/report/erp_finance_income_statement_month_wise_report', $data);
                        } else if ($this->input->post('rptType') == 3) {
                            $this->load->view('system/finance/report/erp_finance_income_statement_ytd_report', $data);
                        } else if ($this->input->post('rptType') == 5) {
                            $this->load->view('system/finance/report/erp_finance_income_statement_ytd_budget_report', $data);
                        } else if ($this->input->post('rptType') == 4) {
                            $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                            $this->load->view('system/finance/report/erp_finance_income_statement_month_wise_budget_report', $data);
                        }
                    }
                }
                break;
        }
    }

    function get_financial_year()
    {
        if ($this->input->post("type") == 1) {
            echo json_encode($this->Report_model->get_financial_year());
        } else {
            echo json_encode($this->Report_model->get_group_financial_year());
        }
    }

    function get_report_drilldown()
    {
        switch ($this->input->post('reportID')) {
            case "FIN_TB";/*Trial balanacer*/
            case "FIN_IS";/*Income Statement*/
            case "FIN_BS";/*Balance sheet*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                $data["output"] = $this->Report_model->get_finance_report_drilldown($fromTo, $segment, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "html";
                $this->load->view('system/finance/report/erp_finance_drilldown_report', $data);
                break;
            case "AP_VAS";/*Vendor Aging Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                $data["output"] = $this->Report_model->get_accounts_payable_report_drilldown($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "html";
                $this->load->view('system/accounts_payable/report/erp_accounts_payable_drilldown_report', $data);
                break;
            case "AR_CAS";/*Customer Aging Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year($this->input->post("from"));
                $data["output"] = $this->Report_model->get_accounts_receivable_report_drilldown($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "html";
                $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_drilldown_report', $data);
                break;
            case "INV_VAL";/*Item Valuation Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year($this->input->post("from"));
                $data["output"] = $this->Report_model->get_item_report_drilldown($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "html";
                $this->load->view('system/inventory/report/erp_item_drilldown_report', $data);
                break;
            case "INV_IIQ";/*Item Inquiry*/
                if ($this->input->post('currency') == 'PO') {
                    $data["output"] = $this->Report_model->get_item_inquiry_po_report_drilldown();
                    $data["type"] = "html";
                    $this->load->view('system/inventory/report/erp_item_inquiry_po_drilldown_report', $data);
                } else {
                    $data["output"] = $this->Report_model->get_item_inquiry_all_doc_report_drilldown();
                    $data["type"] = "html";
                    $this->load->view('system/inventory/report/erp_item_inquiry_all_doc_drilldown_report', $data);
                }

                break;
        }
    }


    function get_report_group_drilldown()
    {
        switch ($this->input->post('reportID')) {
            case "FIN_TB";/*Trial balanacer*/
            case "FIN_IS";/*Income Statement*/
            case "FIN_BS";/*Balance sheet*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_group_financial_year(format_date($this->input->post("from")));
                $data["output"] = $this->Report_model->get_finance_report_group_drilldown($fromTo, $segment, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "html";
                $this->load->view('system/finance/report/erp_finance_drilldown_report', $data);
                break;
            case "INV_VAL";/*Item Valuation Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year($this->input->post("from"));
                $data["output"] = $this->Report_model->get_item_report_drilldown($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "html";
                $this->load->view('system/inventory/report/erp_item_drilldown_report', $data);
                break;
            case "INV_IIQ";/*Item Inquiry*/
                if ($this->input->post('currency') == 'PO') {
                    $data["output"] = $this->Report_model->get_item_inquiry_po_report_drilldown();
                    $data["type"] = "html";
                    $this->load->view('system/inventory/report/erp_item_inquiry_po_drilldown_report', $data);
                } else {
                    $data["output"] = $this->Report_model->get_item_inquiry_all_doc_report_drilldown();
                    $data["type"] = "html";
                    $this->load->view('system/inventory/report/erp_item_inquiry_all_doc_drilldown_report', $data);
                }

                break;
        }
    }

    function get_report_drilldown_pdf()
    {
        switch ($this->input->post('reportID')) {
            case "FIN_TB";/*Trial balanacer*/
            case "FIN_IS";/*Income Statement*/
            case "FIN_BS";/*Balance sheet*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                $data["output"] = $this->Report_model->get_finance_report_drilldown($fromTo, $segment, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "pdf";
                $html = $this->load->view('system/finance/report/erp_finance_drilldown_report', $data, true);
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4-L');
                break;
            case "AP_VAS";/*Vendor Aging Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year($this->input->post("from"));
                $data["output"] = $this->Report_model->get_accounts_payable_report_drilldown($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "pdf";
                $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_drilldown_report', $data, true);
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4-L');
                break;
            case "AR_CAS";/*Customer Aging Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year($this->input->post("from"));
                $data["output"] = $this->Report_model->get_accounts_receivable_report_drilldown($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "pdf";
                $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_drilldown_report', $data, true);
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4-L');
                break;
            case "INV_VAL";/*Item Valuation Summary*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_financial_year($this->input->post("from"));
                $data["output"] = $this->Report_model->get_item_report_drilldown($fromTo, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "pdf";
                $html = $this->load->view('system/inventory/report/erp_item_drilldown_report', $data, true);
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4-L');
                break;
            case "INV_IIQ";/*Item Inquiry*/
                if ($this->input->post('currency') == 'PO') {
                    $data["output"] = $this->Report_model->get_item_inquiry_po_report_drilldown();
                    $data["type"] = "pdf";
                    $html = $this->load->view('system/inventory/report/erp_item_inquiry_po_drilldown_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                } else {
                    $data["output"] = $this->Report_model->get_item_inquiry_all_doc_report_drilldown();
                    $data["type"] = "pdf";
                    $html = $this->load->view('system/inventory/report/erp_item_inquiry_all_doc_drilldown_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }

                break;
        }
    }

    function get_group_report_drilldown_pdf()
    {
        switch ($this->input->post('reportID')) {
            case "FIN_TB";/*Trial balanacer*/
            case "FIN_IS";/*Income Statement*/
            case "FIN_BS";/*Balance sheet*/
                $to = $this->input->post('to');
                $segments = $this->input->post('segment');
                $fromTo = false;
                $segment = false;
                if (isset($to)) {
                    $fromTo = true;
                    $data["to"] = $this->input->post('to');
                }
                if (isset($segments)) {
                    $segment = true;
                }
                $financialBeginingDate = get_group_financial_year(format_date($this->input->post("from")));
                $data["output"] = $this->Report_model->get_finance_report_group_drilldown($fromTo, $segment, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = convert_date_format($this->input->post('from'));
                $data["type"] = "pdf";
                $html = $this->load->view('system/finance/report/erp_finance_drilldown_report', $data, true);
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4-L');
                break;
        }
    }

    function get_report_by_id_pdf()
    {
        switch ($this->input->post('reportID')) {
            case "AR_CS": /*customer_statement*/
                $fieldNameChk = array("transactionAmount");
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                //$this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $_POST["captionChk"] = array("Transaction Currency");
                    $_POST["fieldNameChk"] = array("transactionAmount");
                    $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report();
                    $data["caption"] = array("Transaction Currency");
                    $data["fieldName"] = array("transactionAmount");
                    $data["from"] = convert_date_format($this->input->post('from'));
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                    $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report_pdf', $data);
                    /* $this->load->library('pdf');
                     $pdf = $this->pdf->printed($html, 'A4');*/
                }
                break;

            case "AR_CL": /*customer ledger*/
                $fieldNameChk = array("transactionAmount");
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                //$this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $_POST["captionChk"] = array("Transaction Currency");
                    $_POST["fieldNameChk"] = array("transactionAmount");
                    $data["output"] = $this->Report_model->get_accounts_receivable_customer_ledger_report();
                    $data["caption"] = array("Transaction Currency");
                    $data["fieldName"] = array("transactionAmount");
                    $data["from"] = convert_date_format($this->input->post('from'));
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CL', $fieldNameChk);
                    $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report_pdf', $data);
                    /*$this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4');*/
                }
                break;

            case "FIN_BS": /*Balance Sheet*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($_POST["fieldNameChk"]) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_balance_sheet_report();
                    $data["caption"] = $_POST["captionChk"];
                    $data["fieldName"] = $_POST["fieldNameChk"];
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_BS', $_POST["fieldNameChk"]);
                    if ($this->input->post('rptType') == 1) {
                        $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                        $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");
                        $html = $this->load->view('system/finance/report/erp_finance_balance_sheet_month_wise_report', $data, true);
                    } else if ($this->input->post('rptType') == 3) {
                        $html = $this->load->view('system/finance/report/erp_finance_balance_sheet_ytd_report', $data, true);
                    }
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "FIN_TB": /*Trial Balance*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($_POST["fieldNameChk"]) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_tb_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_TB', $this->input->post('fieldNameChk'));
                    if ($this->input->post('rptType') == 1) {
                        $financialBeginingDate = get_financial_year(format_date($this->input->post("from")));
                        $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");
                        $html = $this->load->view('system/finance/report/erp_finance_tb_month_wise_report', $data, true);
                    } else if ($this->input->post('rptType') == 3) {
                        $data["retain"] = $this->Report_model->get_finance_tb_retain();
                        $html = $this->load->view('system/finance/report/erp_finance_tb_ytd_report', $data, true);
                    }
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "FIN_IS": /*Income statement*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($_POST["fieldNameChk"]) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_income_statement_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_TB', $this->input->post('fieldNameChk'));
                    if ($this->input->post('rptType') == 1) {
                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        $html = $this->load->view('system/finance/report/erp_finance_income_statement_month_wise_report', $data, true);
                    } else if ($this->input->post('rptType') == 3) {
                        $html = $this->load->view('system/finance/report/erp_finance_income_statement_ytd_report', $data, true);
                    } else if ($this->input->post('rptType') == 5) {
                        $html = $this->load->view('system/finance/report/erp_finance_income_statement_ytd_budget_report', $data, true);
                    } else if ($this->input->post('rptType') == 4) {
                        $data["month"] = get_month_list_from_date(format_date($this->input->post("from")), format_date($this->input->post("to")), "Y-m", "1 month");
                        $html = $this->load->view('system/finance/report/erp_finance_income_statement_month_wise_budget_report', $data, true);
                    }
                    //echo $html;
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "FIN_GL": /*General Ledger */
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                $this->form_validation->set_rules('glCodeTo[]', 'GL Code', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_general_ledger_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_GL', $this->input->post('fieldNameChk'));
                    //$html = $this->load->view('system/finance/report/erp_finance_general_ledger_report', $data, true);
                    $printlink = print_template_pdf('FIN_GL','system/finance/report/erp_finance_general_ledger_report');
                    $html = $this->load->view($printlink, $data, true);
                    /*$this->load->library('pdftc');
                    $pdf = $this->pdftc->printed($html, 'A4-L');*/
                    /*$this->load->library('pdfdom');
                    $pdf = $this->pdfdom->printed($html, 'A4-L');*/
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "PROC_POL": /*PO List*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('status', 'Status', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_procurement_purchase_order_list_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["segmentfilter"] = $this->Report_model->get_segment();
                    $data["status"] = $this->input->post('status');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('PROC_POL', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/procurement/report/erp_procurement_purchase_order_list_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "ITM_LG": /*item ledger*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_ledger_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["type"] = "pdf";
                    $data["warehouse"] = $this->Report_model->get_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_LG', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_item_ledger_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;

            case "INV_VAL": /*item valuation*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_valuation_summary_report();
                    $data["TotalAssetValue"] = $this->Report_model->get_item_valuation_summary_total_asset();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["warehouse"] = $this->Report_model->get_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_VAL', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_item_valuation_summary_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;

            case "ITM_CNT": /*item counting*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_counting_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["warehouse"] = $this->Report_model->get_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_CNT', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_item_counting_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "ITM_FM": /*item fast moving*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('rptType', 'Report Type', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_fast_moving_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_FM', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_item_fast_moving_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "INV_UBG": /*Unbilled GRV*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_inventory_unbilled_grv_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_UBG', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_inventory_unbilled_grv_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "INV_IIQ": /*Item Inquiry*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_inquiry_report();
                    $data["warehouse"] = load_location_drop();
                    $data["type"] = "pdf";
                    $html = $this->load->view('system/inventory/report/erp_item_inquiry_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "AP_VL": /*Vendor Ledger*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_accounts_payable_vendor_ledger_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VL', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report', $data, true);
                    //echo $html;
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "AP_VS": /*Vendor Statement*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_accounts_payable_vendor_statement_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VS', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_statement_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "AP_VAS": /*Vendor Aging Summary*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);

                    $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_summary_report($aging);
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["aging"] = $aging;
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAS', $this->input->post('fieldNameChk'));
                    $data["type"] = "pdf";
                    $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_summary_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "AP_VAD": /*Vendor Aging Detail*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) { /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);

                    $data["output"] = $this->Report_model->get_accounts_payable_vendor_aging_detail_report($aging);
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["aging"] = $aging;
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VAD', $this->input->post('fieldNameChk'));
                    $data["type"] = "pdf";
                    $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_aging_detail_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "AR_CAS": /*Customer Aging Summary*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);

                    $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_summary_report($aging);
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["aging"] = $aging;
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAS', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_summary_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "AR_CAD": /*Customer Aging Detail*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                $this->form_validation->set_rules('interval', 'Interval', 'trim|required');
                $this->form_validation->set_rules('through', 'Through', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $aging = array();
                    $interval = $this->input->post("interval");
                    $through = $this->input->post("through");
                    $z = 1;
                    for ($i = $interval; $i < $through; $z++) {  /*calculate aging range*/
                        if ($z == 1) {
                            $aging[] = $z . "-" . $interval;
                        } else {
                            if (($i + $interval) > $through) {
                                $aging[] = ($i + 1) . "-" . ($through);
                                $i += $interval;
                            } else {
                                $aging[] = ($i + 1) . "-" . ($i + $interval);
                                $i += $interval;
                            }

                        }
                    }
                    $aging[] = "> " . ($through);

                    $data["output"] = $this->Report_model->get_accounts_receivable_customer_aging_detail_report($aging);
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["aging"] = $aging;
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CAD', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_aging_detail_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
        }
    }

    function get_group_report_by_id_pdf()
    {
        switch ($this->input->post('reportID')) {
            case "ITM_LG": /*item ledger*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_ledger_group_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["type"] = "pdf";
                    $data["warehouse"] = $this->Report_model->get_group_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_LG', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_item_ledger_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "INV_VAL": /*item valuation*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_valuation_summary_group_report();
                    $data["TotalAssetValue"] = $this->Report_model->get_item_valuation_summary_total_asset_group();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["warehouse"] = $this->Report_model->get_group_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_VAL', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_item_valuation_summary_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "ITM_CNT": /*item counting*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('itemTo[]', 'Items', 'trim|required');
                $this->form_validation->set_rules('location[]', 'Warehouse', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_item_counting_group_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["warehouse"] = $this->Report_model->get_group_warehouse();
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('ITM_CNT', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_item_counting_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "INV_UBG": /*Unbilled GRV*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                if (count($_POST["fieldNameChk"]) > 1) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                } else {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_inventory_unbilled_grv_group_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('INV_UBG', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/inventory/report/erp_inventory_unbilled_grv_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;

            case "AR_CL": /*Customer Ledger*/
                $fieldNameChk = array("transactionAmount");
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                //$this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $_POST["captionChk"] = array("Transaction Currency");
                    $_POST["fieldNameChk"] = array("transactionAmount");
                    $data["output"] = $this->Report_model->get_accounts_receivable_customer_ledger_group_report();
                    $data["caption"] = array("Transaction Currency");
                    $data["fieldName"] = array("transactionAmount");
                    $data["from"] = convert_date_format($this->input->post('from'));
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CL', $fieldNameChk);
                    $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_ledger_report_pdf', $data);
                    /*$this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4');*/
                }
                break;
            case "AR_CS": /*Customer Statement*/
                $fieldNameChk = array("transactionAmount");
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('customerTo[]', 'Customer', 'trim|required');
                //$this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $_POST["captionChk"] = array("Transaction Currency");
                    $_POST["fieldNameChk"] = array("transactionAmount");
                    $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_group_report();
                    $data["caption"] = array("Transaction Currency");
                    $data["fieldName"] = array("transactionAmount");
                    $data["from"] = convert_date_format($this->input->post('from'));
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $fieldNameChk);
                    $html = $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report_pdf', $data);
                    /* $this->load->library('pdf');
                     $pdf = $this->pdf->printed($html, 'A4');*/
                }
                break;
            case "AP_VL": /*Vendor Ledger*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_accounts_payable_vendor_ledger_group_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VL', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_ledger_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "AP_VS": /*Vendor Statement*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('vendorTo[]', 'Vendor', 'trim|required');
                $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_accounts_payable_vendor_statement_group_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VS', $this->input->post('fieldNameChk'));
                    $html = $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_statement_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "FIN_TB": /*Trial Balance*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_group_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($_POST["fieldNameChk"]) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_tb_group_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_TB', $this->input->post('fieldNameChk'));
                    $html = "";
                    if ($this->input->post('rptType') == 1) {
                        $financialBeginingDate = get_group_financial_year(format_date($this->input->post("from")));
                        $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");
                        $html = $this->load->view('system/finance/report/erp_finance_tb_month_wise_report', $data, true);
                    } else if ($this->input->post('rptType') == 3) {
                        $data["retain"] = $this->Report_model->get_finance_tb_group_retain();
                        $html = $this->load->view('system/finance/report/erp_finance_tb_ytd_report', $data, true);
                    }
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "FIN_GL": /*General Ledger */
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required');
                $this->form_validation->set_rules('to', 'Date To', 'trim|required|callback_check_compareDate');
                $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
                $this->form_validation->set_rules('glCodeTo[]', 'GL Code', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_general_ledger_group_report();
                    $data["caption"] = $this->input->post('captionChk');
                    $data["fieldName"] = $this->input->post('fieldNameChk');
                    $data["from"] = $this->input->post('from');
                    $data["to"] = $this->input->post('to');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_GL', $this->input->post('fieldNameChk'));
                    $printlink = print_template_pdf('FIN_GL','system/finance/report/erp_finance_general_ledger_report');
                    $html =$this->load->view($printlink, $data, true);
                    //$html = $this->load->view('system/finance/report/erp_finance_general_ledger_report', $data, true);
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
            case "FIN_BS": /*Balance Sheet*/
                $_POST["fieldNameChk"] = explode(',', $this->input->post('fieldNameChkpdf'));
                $_POST["captionChk"] = explode(',', $this->input->post('captionChkpdf'));
                $this->form_validation->set_rules('from', 'Date From', 'trim|required|callback_check_valid_group_financial_year');
                $this->form_validation->set_rules('rptType', 'Report type', 'trim|required');
                if ($this->input->post('rptType') == 1) {
                    if (count($_POST["fieldNameChk"]) > 1) {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_column_count_selected');
                    } else {
                        $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                    }
                }

                if ($this->input->post('rptType') == 3) {
                    $this->form_validation->set_rules('fieldNameChk[]', 'Column', 'callback_check_valid_extra_column');
                }
                if ($this->form_validation->run() == FALSE) {
                    $error_message = validation_errors();
                    echo warning_message($error_message);
                } else {
                    $data = array();
                    $data["output"] = $this->Report_model->get_finance_balance_sheet_group_report();
                    $data["caption"] = $_POST["captionChk"];
                    $data["fieldName"] = $_POST["fieldNameChk"];
                    $data["from"] = $this->input->post('from');
                    $data["type"] = "pdf";
                    $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_BS', $_POST["fieldNameChk"]);
                    if ($this->input->post('rptType') == 1) {
                        $financialBeginingDate = get_group_financial_year(format_date($this->input->post("from")));
                        $data["month"] = get_month_list_from_date($financialBeginingDate["beginingDate"], format_date($this->input->post("from")), "Y-m", "1 month");
                        $html = $this->load->view('system/finance/report/erp_finance_balance_sheet_month_wise_report', $data, true);
                    } else if ($this->input->post('rptType') == 3) {
                        $html = $this->load->view('system/finance/report/erp_finance_balance_sheet_ytd_report', $data, true);
                    }
                    $this->load->library('pdf');
                    $pdf = $this->pdf->printed($html, 'A4-L');
                }
                break;
        }
    }

    function check_valid_extra_column($fieldNameChk)
    {
        if (empty($fieldNameChk)) {
            $this->form_validation->set_message('check_valid_extra_column', 'Please select one currency');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function check_column_count_selected()
    {
        $this->form_validation->set_message('check_column_count_selected', 'please select one currency');
        return FALSE;
    }

    function check_valid_financial_year($date)
    {
        $output = get_financial_year(format_date($date));
        if (!$output) {
            $this->form_validation->set_message('check_valid_financial_year', 'Invalid Date Range Selected');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function check_valid_group_financial_year($date)
    {
        $output = get_group_financial_year(format_date($date));
        if (!$output) {
            $this->form_validation->set_message('check_valid_financial_year', 'Invalid Date Range Selected');
            return FALSE;
        } else {
            return TRUE;
        }
    }


    function check_compareDate()
    {
        $from = strtotime(convert_date_format($_POST['from']));
        $to = strtotime(convert_date_format($_POST['to']));

        if ($to >= $from) {
            return True;
        } else {
            $this->form_validation->set_message('check_compareDate', 'Invalid Date Range Selected');
            return False;
        }
    }

    function dashboardReportView()
    {
        $rptID = trim($this->input->post('RptID'));
        $currentDate = date("Y-m-d");
        $companyId = $this->common_data['company_data']['company_id'];
        switch ($rptID) {
            case "FIN_IS": /*Income statement*/
                $allSegments = $this->db->query("SELECT segmentID,status from srp_erp_segment where companyID = '{$companyId}' AND status = 1")->result_array();
                $new_array = array();
                if (!empty($allSegments)) {
                    foreach ($allSegments as $value) {
                        $new_array[] = $value['segmentID'];
                    }
                }
                $period = $this->input->post("year");
                $lastTwoYears = get_last_two_financial_year();
                if (!empty($lastTwoYears)) {
                    $beginingDate = $lastTwoYears[$period]["beginingDate"];
                    $endDate = $lastTwoYears[$period]["endingDate"];
                }
                $_POST['rptType'] = 3;
                $_POST['segment'] = $new_array;
                $_POST['from'] = $beginingDate;
                $_POST['to'] = $endDate;
                $data = array();
                $data["output"] = $this->Report_model->get_finance_income_statement_report();
                $data["caption"] = $this->input->post('captionChk');
                $data["fieldName"] = $this->input->post('fieldNameChk');
                $data["from"] = convert_date_format($beginingDate);
                $data["to"] = convert_date_format($endDate);
                $data["userDashboardID"] = $this->input->post('userDashboardID');
                $data["type"] = "html";
                $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('FIN_IS', $this->input->post('fieldNameChk'));
                $this->load->view('system/finance/report/erp_finance_income_statement_ytd_report', $data);
                break;
            case "AP_VS": /*Vendor Statement*/
                $_POST['from'] = $currentDate;
                $data = array();
                $data["output"] = $this->Report_model->get_accounts_payable_vendor_statement_report(true);
                $data["caption"] = $this->input->post('captionChk');
                $data["fieldName"] = $this->input->post('fieldNameChk');
                $data["from"] = current_format_date();
                $data["type"] = "html";
                $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AP_VS', $this->input->post('fieldNameChk'));
                $this->load->view('system/accounts_payable/report/erp_accounts_payable_vendor_statement_report', $data);
                break;
            case "AR_CS": /*Customer Statement*/
                $_POST['from'] = $currentDate;
                $data = array();
                $data["output"] = $this->Report_model->get_accounts_receivable_customer_statement_report(true);
                $data["caption"] = $this->input->post('captionChk');
                $data["fieldName"] = $this->input->post('fieldNameChk');
                $data["from"] = current_format_date();
                $data["type"] = "html";
                $data["fieldNameDetails"] = $this->Report_model->getColumnsDetailByReport('AR_CS', $this->input->post('fieldNameChk'));
                $this->load->view('system/accounts_receivable/report/erp_accounts_receivable_customer_statement_report', $data);
                break;
        }
    }

    function dashboardReportDrilldownView()
    {
        $rptID = trim($this->input->post('RptID'));
        $currentDate = date("Y-m-d");
        $companyId = $this->common_data['company_data']['company_id'];
        switch ($rptID) {
            case "FIN_IS";/*Income Statement*/
                $allSegments = $this->db->query("SELECT segmentID,status from srp_erp_segment where companyID = '{$companyId}' AND status = 1")->result_array();
                $new_array = array();
                if (!empty($allSegments)) {
                    foreach ($allSegments as $value) {
                        $new_array[] = $value['segmentID'];
                    }
                }
                $period = $this->input->post("year");
                $lastTwoYears = get_last_two_financial_year();
                if (!empty($lastTwoYears)) {
                    $beginingDate = $lastTwoYears[$period]["beginingDate"];
                    $endDate = $lastTwoYears[$period]["endingDate"];
                }
                $fromTo = false;
                if (isset($endDate)) {
                    $fromTo = true;
                    $data["to"] = $endDate;
                }
                $_POST['segment'] = $new_array;
                $_POST['from'] = $beginingDate;
                $_POST['to'] = $endDate;
                $financialBeginingDate = get_financial_year($beginingDate);
                $data["output"] = $this->Report_model->get_finance_report_drilldown($fromTo, $new_array, $financialBeginingDate);
                $data["fieldName"] = $this->input->post('currency');
                $data["from"] = $beginingDate;
                $data["type"] = 'html';
                $this->load->view('system/finance/report/erp_finance_drilldown_report', $data);
                break;
        }
    }

    /** NASIK **/
    function fetch_reportMaster()
    {
        $this->datatables->select('id, description, reportCode', false)
            ->from('srp_erp_sso_reportmaster')
            ->add_column('action', '$1', 'reportMaster_action(id, reportCode)');
        echo $this->datatables->generate();
    }

    function save_companyLevelReportDetails()
    {
        $reportType = $this->input->post('reportType');
        $fields_arr = get_ssoReportFields('C', $reportType);

        $this->form_validation->set_rules('masterID', 'Report master ID', 'required');
        $this->form_validation->set_rules('reportType', 'Report Type', 'required');
        foreach ($fields_arr as $field) {
            $this->form_validation->set_rules($field['inputName'], $field['description'], 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->save_companyLevelReportDetails());
        }

    }

    function employee_config_view()
    {
        $this->load->view('system/hrm/report/erp_report_employee_configuration');
    }

    function save_employeeLevelReportDetails()
    {
        $fields_arr = get_ssoReportFields('E');

        $this->form_validation->set_rules('masterID', 'Report master ID', 'required');
        foreach ($fields_arr as $field) {
            $this->form_validation->set_rules($field['inputName'] . '[]', $field['description'], 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->save_employeeLevelReportDetails($fields_arr));
        }
    }

    function epf_reportGenerate()
    {

        $epfReportID = $this->uri->segment(3);
        $companyID = current_companyID();

        $report_companyData = get_ssoReportFields('C', 'EPF');

        $payrollData = $this->db->query("SELECT DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') ,'%Y%m') AS contPeriod, masterTB.submissionID,
                                         DATE_SUB(
                                            DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') ,'%Y-%m-%d'),  INTERVAL 1 MONTH
                                         ) AS lastPayrollDate,
                                         (
                                             SELECT reportValue FROM srp_erp_sso_reporttemplatefields AS fieldTB
                                             JOIN srp_erp_sso_reporttemplatedetails AS detTB ON detTB.reportID=fieldTB.id AND companyID={$companyID}
                                             WHERE fieldName='znCode' LIMIT 1
                                         ) AS znCode
                                         ,
                                         (
                                             SELECT reportValue FROM srp_erp_sso_reporttemplatefields AS fieldTB
                                             JOIN srp_erp_sso_reporttemplatedetails AS detTB ON detTB.reportID=fieldTB.id AND companyID={$companyID}
                                             WHERE fieldName='empNo' AND masterID=1 LIMIT 1
                                         ) AS employerNumber, payrollYear, payrollMonth
                                         FROM srp_erp_sso_epfreportmaster AS masterTB
                                         WHERE masterTB.companyID={$companyID} AND masterTB.id={$epfReportID}")->row_array();

        $fileName = $payrollData['znCode'] . '' . $payrollData['employerNumber'];
        $contPeriod = $payrollData['contPeriod'];
        $payrollYear = $payrollData['payrollYear'];
        $payrollMonth = $payrollData['payrollMonth'];
        $submissionID = $payrollData['submissionID'];
        $lastPayrollDate = new DateTime($payrollData['lastPayrollDate']);
        $lastPayrollYear = $lastPayrollDate->format('Y');
        $lastPayrollMonth = $lastPayrollDate->format('m');


        $empContributionID = null;
        $comContributionID = null;
        $totalEarningID = null;
        $otherColumn = '';


        foreach ($report_companyData as $key => $reportData) {
            if ($reportData['inputName'] == 'empCont') {
                $comContributionID = $reportData['reportValue'];
            } else if ($reportData['inputName'] == 'memCont') {
                $empContributionID = $reportData['reportValue'];
            } else if ($reportData['inputName'] == 'totEarnings') {
                $totalEarningID = $reportData['reportValue'];
            } else {
                $column = '\'' . $reportData['reportValue'] . '\' AS ' . $reportData['fieldName'];
                $otherColumn .= ', ' . $column;
            }
        }

        $otherColumn .= ', \'' . $contPeriod . '\' AS contPeriod';

        $data = $this->db->query("SELECT EIdNo, nic, employerCont, memberCont, REPLACE(FORMAT(ABS(employerCont + memberCont),2), ',', '')  AS toCount,
                                REPLACE(FORMAT(totEarnings,2), ',', '') AS totEarnings, IF(isExist=1, 'E', 'N') AS memStatus, {$submissionID} AS submissionID,
                                ocGrade, CAST(empConfigDet.memNumber AS SIGNED) AS orderColumn, empConfigDet.* {$otherColumn}
                                FROM srp_employeesdetails AS empTB
                                JOIN
                                (
                                    SELECT empID, REPLACE(FORMAT(sum(ABS(employerC)), 2), ',', '') as employerCont,
                                    REPLACE(FORMAT(sum(ABS(memberC)), 2), ',', '') as memberCont
                                    FROM
                                    (
                                        SELECT empID, if(detailTBID={$comContributionID}, transactionAmount, 0) AS employerC,
                                        if(detailTBID={$empContributionID}, transactionAmount, 0) AS memberC,
                                        transactionCurrencyDecimalPlaces as dPlace, detailTBID
                                        FROM srp_erp_payrolldetail WHERE companyID={$companyID} AND fromTB='PAY_GROUP'
                                        AND detailTBID IN ({$empContributionID},{$comContributionID}) AND payrollMasterID IN (
                                            SELECT payMaster.payrollMasterID FROM srp_erp_payrollmaster AS payMaster
                                            JOIN srp_erp_payrollheaderdetails AS payHeader ON payHeader.payrollMasterID = payMaster.payrollMasterID
                                            WHERE payMaster.companyID={$companyID} AND payHeader.companyID={$companyID}
                                            AND payrollYear={$payrollYear} AND payrollMonth={$payrollMonth} AND approvedYN=1
                                        )
                                    ) AS TB1  GROUP BY empID
                                ) AS memberContTB ON memberContTB.empID = empTB.EIdNo
                                JOIN (
                                    SELECT empID, MAX(initials) AS initials, MAX(memNumber) AS memNumber, MAX(lastName) AS lastName FROM
                                    (
                                        SELECT empID, reportFields.id, inputName, reportID, reportValue,
                                        IF(inputName='lastName',reportValue, '') AS lastName,
                                        IF(inputName='initials', reportValue, '') AS initials,
                                        IF(inputName='memNumber', reportValue, '') AS memNumber
                                        FROM srp_erp_sso_reporttemplatedetails AS reportDetails
                                        JOIN srp_erp_sso_reporttemplatefields AS reportFields ON reportFields.id = reportDetails.reportID
                                        WHERE reportDetails.companyID={$companyID} AND reportFields.isEmployeeLevel=1 AND masterID=1
                                    ) AS tb1 GROUP  BY empID
                                ) AS empConfigDet ON empConfigDet.empID = empTB.EIdNo
                                JOIN (
                                    SELECT empID, ocGrade  FROM srp_erp_sso_epfreportdetails WHERE companyID={$companyID} AND epfReportID={$epfReportID}
                                )AS otherDetTB ON otherDetTB.empID=empTB.EIdNo
                                JOIN (
                                    SELECT empID, transactionAmount AS totEarnings FROM srp_erp_payrolldetailpaygroup WHERE companyID={$companyID}
                                    AND detailTBID={$totalEarningID} AND payrollMasterID IN (
                                        SELECT payMaster.payrollMasterID FROM srp_erp_payrollmaster AS payMaster
                                        JOIN srp_erp_payrollheaderdetails AS payHeader ON payHeader.payrollMasterID = payMaster.payrollMasterID
                                        WHERE payMaster.companyID={$companyID} AND payHeader.companyID={$companyID}
                                        AND payrollYear={$payrollYear} AND payrollMonth={$payrollMonth} AND approvedYN=1
                                    ) GROUP BY empID
                                )AS payGroup ON payGroup.empID=empTB.EIdNo
                                LEFT JOIN (
                                    SELECT empID, 1 AS isExist  FROM srp_erp_payrollmaster AS t1
                                    JOIN srp_erp_payrolldetail AS t2 ON t2.payrollMasterID=t1.payrollMasterID AND t2.companyID={$companyID}
                                    WHERE t1.companyID={$companyID} AND payrollYear={$lastPayrollYear} AND payrollMonth={$lastPayrollMonth}
                                    GROUP BY empID
                                ) AS isNewEmpTB ON isNewEmpTB.empID=empTB.EIdNo AND Erp_companyID={$companyID}
                                WHERE Erp_companyID={$companyID} ORDER BY orderColumn ASC")->result_array();

        $fileName = $fileName . 'C';

        $this->generateDetFile($fileName, $data, 'EPF');
    }

    function etf_reportGenerate()
    {

        $payrollMonth = $this->input->post('payrollMonth');
        $segment = $this->input->post('segment');
        $companyID = current_companyID();
        $report_companyData = get_ssoReportFields('C', 'ETF');

        $payYear = date('Y', strtotime($payrollMonth));
        $payMonth = date('m', strtotime($payrollMonth));

        $fileName = 'MEMTXT';
        $from2periodTo = date('Ym', strtotime($payrollMonth));
        $from2periodFrom = $from2periodTo;

        $etfContributionID = null;
        $otherColumn = '';

        foreach ($report_companyData as $key => $reportData) {
            if ($reportData['inputName'] == 'etfContribution') {
                $etfContributionID = $reportData['reportValue'];
            } else {
                $column = '\'' . $reportData['reportValue'] . '\' AS ' . $reportData['fieldName'];
                $otherColumn .= ', ' . $column;
            }
        }

        $otherColumn .= ', \'' . $from2periodTo . '\' AS from2periodTo, \'' . $from2periodFrom . '\' AS from2periodFrom';
        $joinSeg = "";


        if (!empty($segment)) {
            $whereIN = join(',', $segment);

            $joinSeg = "JOIN (
                            SELECT EmpID AS empIDSegTB FROM srp_erp_payrollmaster AS payMaster
                            JOIN srp_erp_payrollheaderdetails AS payHDet ON payMaster.payrollMasterID=payHDet.payrollMasterID AND payHDet.companyID={$companyID}
                            WHERE payMaster.companyID={$companyID} AND payrollYear={$payYear} AND payrollMonth={$payMonth}
                            AND payHDet.segmentID IN ({$whereIN}) AND approvedYN=1
                       ) AS segmntFillterd ON segmntFillterd.empIDSegTB = empID";
        }


        $data = $this->db->query("SELECT EIdNo, nic, etfContribution, CAST(empConfigDet.memNumber AS SIGNED) AS orderColumn,
                                 empConfigDet.* {$otherColumn}
                                FROM srp_employeesdetails AS empTB
                                JOIN
                                (
                                    SELECT empID, (REPLACE(round(sum(ABS(transactionAmount)), 2), '.', ''))AS etfContribution,
                                    transactionAmount AS employerC, transactionCurrencyDecimalPlaces as dPlace, detailTBID
                                    FROM srp_erp_payrolldetail {$joinSeg}
                                    WHERE companyID={$companyID} AND fromTB='PAY_GROUP' AND detailTBID = {$etfContributionID}
                                    AND payrollMasterID IN (
                                        SELECT payrollMasterID FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollYear={$payYear}
                                        AND payrollMonth={$payMonth} AND approvedYN=1
                                    )
                                    GROUP BY empID
                                ) AS memberContTB ON memberContTB.empID = empTB.EIdNo
                                JOIN (
                                    SELECT empID, MAX(initials) AS initials, MAX(memNumber) AS memNumber, MAX(lastName) AS lastName FROM
                                    (
                                        SELECT empID, reportFields.id, inputName, reportID, reportValue,
                                        IF(inputName='lastName', UPPER(reportValue), '') AS lastName,
                                        IF(inputName='initials', reportValue, '') AS initials,
                                        IF(inputName='memNumber', reportValue, '') AS memNumber
                                        FROM srp_erp_sso_reporttemplatedetails AS reportDetails
                                        JOIN srp_erp_sso_reporttemplatefields AS reportFields ON reportFields.id = reportDetails.reportID
                                        WHERE reportDetails.companyID={$companyID} AND reportFields.isEmployeeLevel=1 AND masterID=2
                                    ) AS tb1 GROUP BY empID
                                ) AS empConfigDet ON empConfigDet.empID = empTB.EIdNo
                                WHERE Erp_companyID={$companyID} ORDER BY orderColumn ASC")->result_array();

        if (empty($data)) {
            die('There is no data');
        }
        $this->generateDetFile($fileName, $data, 'ETF');
    }

    function etfHeaderRow($memberCount)
    {
        $payrollMonth = $this->input->post('payrollMonth');
        $segment = $this->input->post('segment');

        $payYear = date('Y', strtotime($payrollMonth));
        $payMonth = date('m', strtotime($payrollMonth));
        $from2periodTo = date('Ym', strtotime($payrollMonth));
        $from2periodFrom = $from2periodTo;

        $companyID = current_companyID();
        $report_companyData = get_ssoReportFields('C', 'ETF-H');

        $etfContributionTotalID = null;
        $otherColumn = '';
        foreach ($report_companyData as $key => $reportData) {
            if ($reportData['inputName'] == 'etfContributionTotal') {
                $etfContributionTotalID = $reportData['reportValue'];
            } else {
                $column = '\'' . $reportData['reportValue'] . '\' AS ' . $reportData['fieldName'];
                $otherColumn .= ', ' . $column;
            }
        }
        $otherColumn .= ', \'' . $from2periodTo . '\' AS from2periodTo, \'' . $from2periodFrom . '\' AS from2periodFrom';

        $headerData = ssoReport_shortOrder('ETF-H');

        $joinSeg = "";
        if (!empty($segment)) {
            $whereIN = join(',', $segment);

            $joinSeg = "JOIN (
                        SELECT EmpID AS empIDSegTB FROM srp_erp_payrollmaster AS payMaster
                        JOIN srp_erp_payrollheaderdetails AS payHDet ON payMaster.payrollMasterID=payHDet.payrollMasterID AND payHDet.companyID={$companyID}
                        WHERE payMaster.companyID={$companyID} AND payrollYear={$payYear} AND payrollMonth={$payMonth}
                        AND payHDet.segmentID IN ({$whereIN}) AND approvedYN=1
                    ) AS segmntFillterd ON segmntFillterd.empIDSegTB = empID";
        }

        $data = $this->db->query("SELECT {$memberCount} AS totalMembers,
                                  (
                                      SELECT REPLACE((round(sum(ABS(transactionAmount)),2)), '.', '')
                                      FROM srp_erp_payrolldetail
                                      {$joinSeg}
                                      WHERE companyID={$companyID} AND fromTB='PAY_GROUP' AND detailTBID = {$etfContributionTotalID}
                                      AND payrollMasterID IN (
                                          SELECT payrollMasterID FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollYear={$payYear}
                                          AND payrollMonth={$payMonth} AND approvedYN=1
                                      )
                                  )AS etfContributionTotal {$otherColumn}
                                  ")->row_array();

        $textData = '';
        foreach ($headerData as $headerRow) {
            $thisRow = $headerRow['fieldName'];
            $length = $headerRow['strLength'];
            $value = trim($data[$thisRow]);
            $escapStr = ' ';


            switch ($headerRow['fieldName']) {
                case 'totalMembers':
                    $escapStr = '0';
                    break;

                case 'etfContributionTotal':
                    $escapStr = '0';
                    break;
                case 'nic':
                    $escapStr = '0';
                    break;
                default :
                    $escapStr = ' ';
            }


            if ($headerRow['isLeft_strPad'] == 1) {
                $textData .= str_pad($value, $length, $escapStr, STR_PAD_LEFT);
            } else if ($headerRow['fieldName'] == 'nic') {
                $textData .= str_pad($value, $length, $escapStr, STR_PAD_LEFT);
            } else {
                $textData .= str_pad($value, $length, $escapStr, STR_PAD_RIGHT);
            }
        }

        return $textData;
    }

    function generateDetFile($fileName, $data, $reportType)
    {
        $headerData = ssoReport_shortOrder($reportType);
        if ($data == 0) {
            echo '<p>The File appears to have no data.</p>';
        } else {
            $memberCount = 0;
            $textData = '';
            foreach ($data as $key => $row) {
                if ($reportType == 'EPF' && $row['toCount'] == '0.00') {
                    continue;
                }
                if ($reportType == 'ETF' && $row['etfContribution'] == '0.00') {
                    continue;
                }
                $memberCount++;
                foreach ($headerData as $headerRow) {
                    $thisRow = $headerRow['fieldName'];
                    $length = $headerRow['strLength'];
                    $value = trim($row[$thisRow]);
                    $escapStr = ' ';
                    if ($reportType == 'ETF') {
                        switch ($headerRow['fieldName']) {
                            case 'nic':
                                $escapStr = '0';
                                break;
                            case 'memNumber':
                                $escapStr = '0';
                                break;
                            case 'etfContribution':
                                $escapStr = '0';
                                break;
                            default :
                                $escapStr = ' ';
                        }
                    }
                    if ($headerRow['isLeft_strPad'] == 1) {
                        $textData .= str_pad($value, $length, $escapStr, STR_PAD_LEFT);
                    } else if ($headerRow['fieldName'] == 'nic' && $reportType == 'ETF') {
                        $textData .= str_pad($value, $length, $escapStr, STR_PAD_LEFT);
                    } else if ($headerRow['fieldName'] == 'empNo' && $reportType == 'ETF') {
                        $textData .= str_pad($value, $length, $escapStr, STR_PAD_LEFT);
                    } else {
                        $textData .= str_pad($value, $length, $escapStr, STR_PAD_RIGHT);
                    }
                }
                $textData .= PHP_EOL;
            }
            if ($reportType == 'ETF') {
                $textData .= $this->etfHeaderRow($memberCount);
            }
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . $fileName . ".txt");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo trim($textData);
        }
    }

    function save_epfReportOtherConfig()
    {
        $this->form_validation->set_rules('masterID', 'Report master ID', 'required');
        $this->form_validation->set_rules('shortOrder[]', 'Short order', 'required');
        $this->form_validation->set_rules('strLength[]', 'Length', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->save_epfReportOtherConfig());
        }
    }

    function fetch_epfReport()
    {
        $this->datatables->select("id, master.documentCode, submissionID, comment, master.confirmedYN AS master_confirmedYN,
              DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') ,'%Y %M') AS payMonth", false)
            ->from('srp_erp_sso_epfreportmaster AS master')
            ->add_column('action', '$1', 'action_epfReport(id, master_confirmedYN)')
            ->where('master.companyID', current_companyID());
        echo $this->datatables->generate();
    }

    function save_epfReportMaster()
    {
        $this->form_validation->set_rules('payrollMonth', 'Payroll month ', 'required');
        $this->form_validation->set_rules('submissionID', 'Submission ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->save_epfReportMaster());
        }
    }

    function epf_reportData_view()
    {
        $epfMasterID = $this->input->post('epfMasterID');
        $data['master'] = $this->Report_model->epf_reportData($epfMasterID);
        $data['employees'] = $this->Report_model->get_epfReportEmployee();

        $this->load->view('system/hrm/report/erp_employee_epf_report_generate_view', $data);
    }

    function getEmployeesDataTable()
    {
        $companyID = current_companyID();
        $payrollYear = $this->input->post('payrollYear');
        $payrollMonth = $this->input->post('payrollMonth');
        $segmentID = $this->input->post('segmentID');

        /**Already exist employee list **/
        $whereNotIn_str = $this->employee_arr($payrollYear, $payrollMonth);


        $str_lastOCGrade = '(SELECT ocGrade FROM srp_erp_sso_epfreportdetails WHERE empID = EIdNo AND companyID=' . $companyID . ' ORDER BY id DESC LIMIT 1)';


        $this->datatables->select('EIdNo, ECode, Ename2 AS empName, DesDescription, srp_erp_segment.segmentCode AS segCode,
                                    IF(' . $str_lastOCGrade . ' IS NULL, \'\', ' . $str_lastOCGrade . ') AS last_ocGrade');
        $this->datatables->from('srp_employeesdetails');
        $this->datatables->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID');
        $this->datatables->join('srp_erp_segment', 'srp_employeesdetails.segmentID = srp_erp_segment.segmentID');
        $this->datatables->join('(SELECT EmpID AS payEmpID FROM srp_erp_payrollmaster AS payMaster
                                  JOIN srp_erp_payrollheaderdetails AS payHeader ON payHeader.payrollMasterID = payMaster.payrollMasterID
                                  WHERE payMaster.companyID=' . $companyID . ' AND payHeader.companyID=' . $companyID . ' AND
                                  payrollYear=' . $payrollYear . ' AND payrollMonth=' . $payrollMonth . ' AND approvedYN=1 ) AS payrollProcessedEmpTB',
            'srp_employeesdetails.EIdNo = payrollProcessedEmpTB.payEmpID');
        $this->datatables->add_column('addBtn', '$1', 'addBtn()');
        $this->datatables->where('srp_employeesdetails.Erp_companyID', $companyID);
        $this->datatables->where('srp_employeesdetails.isPayrollEmployee', 1);
        if ($whereNotIn_str != '') {
            $this->datatables->where('EIdNo NOT IN ' . $whereNotIn_str);
        }
        if (!empty($segmentID)) {
            $this->datatables->where('srp_employeesdetails.segmentID IN (' . $segmentID . ' )');
        }

        echo $this->datatables->generate();
    }

    function employee_arr($payrollYear, $payrollMonth)
    {
        $companyID = current_companyID();
        $empList = $this->db->query("SELECT empID FROM srp_erp_sso_epfreportmaster AS masterTB
                                     JOIN srp_erp_sso_epfreportdetails AS detailTB ON detailTB.epfReportID=masterTB.id AND detailTB.companyID=$companyID
                                     WHERE masterTB.companyID={$companyID} AND payrollYear={$payrollYear} AND payrollMonth={$payrollMonth}
                                     GROUP BY empID ")->result_array();
        $whereNotIn_str = '';
        if (!empty($empList)) {
            $whereNotIn_str = '( ';
            foreach ($empList as $key => $row) {
                $sepreter = ($key > 0) ? ',' : '';
                $whereNotIn_str .= $sepreter . '' . $row['empID'];
            }
            $whereNotIn_str .= ')';
        }

        return $whereNotIn_str;
    }

    function save_empEmployeeAsTemporary()
    {
        $this->form_validation->set_rules('masterID', 'Report ID ', 'required');
        $this->form_validation->set_rules('empHiddenID[]', 'Employee ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->save_empEmployeeAsTemporary());
        }
    }

    function epf_reportData()
    {
        $epfMasterID = $this->input->post('epfMasterID');
        echo json_encode($this->Report_model->epf_reportData($epfMasterID));
    }

    function get_epfReportEmployee()
    {
        $companyID = current_companyID();
        $con = "IFNULL(Ename2, '')";
        $epfReportID = $this->input->post('epfReportID');

        $where = array(
            'Erp_companyID' => $companyID,
            'companyID' => $companyID,
            'epfReportID' => $epfReportID
        );

        $this->datatables->select('empID, ECode, CONCAT(' . $con . ') AS empName, ocGrade');
        $this->datatables->from('srp_employeesdetails AS empTB');
        $this->datatables->join('srp_erp_sso_epfreportdetails AS reportTB', 'empTB.EIdNo = reportTB.empID');
        $this->datatables->add_column('addBtn', '$1', 'epfReportTextbox(empID,ocGrade)');
        $this->datatables->where($where);

        echo $this->datatables->generate();
    }

    function save_reportDetails()
    {
        $this->form_validation->set_rules('epfMasterID', 'Report ID', 'required');
        $this->form_validation->set_rules('ocGrade[]', 'OC Grade', 'required');
        $this->form_validation->set_rules('empID[]', 'Employee', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->save_reportDetails());
            die();
            /*$epfMasterID = $this->input->post('epfMasterID');
            $data = $this->Report_model->epf_reportData($epfMasterID);
            if($data['confirmedYN'] == 1){
                echo json_encode(array('e', 'This report is already confirmed.<br>You can not update this'));
            }
            else{
                echo json_encode($this->Report_model->save_reportDetails());
            }*/

        }
    }

    function delete_epfReportEmp()
    {
        $this->form_validation->set_rules('epfMasterID', 'Report ID', 'required');
        $this->form_validation->set_rules('id', 'Report detail ID', 'required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->delete_epfReportEmp());
        }
    }

    function delete_epfReportAllEmp()
    {
        $this->form_validation->set_rules('epfMasterID', 'Report ID', 'required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->delete_epfReportAllEmp());
        }
    }

    function delete_epfReport()
    {
        $this->form_validation->set_rules('deleteID', 'Report ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_model->delete_epfReport());
        }
    }

    function cFrom_reportGenerate()
    {
        $responseType = $this->uri->segment(3);
        $payrollMonth = $this->input->post('payrollMonth');
        $segment = $this->input->post('segment');

        $payYear = date('Y', strtotime($payrollMonth));
        $payMonth = date('m', strtotime($payrollMonth));
        $contPeriod = date('M - Y', strtotime($payrollMonth));
        $companyID = current_companyID();

        $report_companyData = get_ssoReportFields('C', 'EPF');


        $payrollData = $this->db->query("SELECT '{$contPeriod}' AS contPeriod,
                                         (
                                             SELECT reportValue FROM srp_erp_sso_reporttemplatefields AS fieldTB
                                             JOIN srp_erp_sso_reporttemplatedetails AS detTB ON detTB.reportID=fieldTB.id AND companyID={$companyID}
                                             WHERE fieldName='znCode' LIMIT 1
                                         ) AS znCode
                                         ,
                                         (
                                             SELECT reportValue FROM srp_erp_sso_reporttemplatefields AS fieldTB
                                             JOIN srp_erp_sso_reporttemplatedetails AS detTB ON detTB.reportID=fieldTB.id AND companyID={$companyID}
                                             WHERE fieldName='empNo' AND masterID=1 LIMIT 1
                                         ) AS employerNumber")->row_array();

        $empContributionID = null;
        $comContributionID = null;
        $totalEarningID = null;
        $otherColumn = '';


        foreach ($report_companyData as $key => $reportData) {
            if ($reportData['inputName'] == 'empCont') {
                $comContributionID = $reportData['reportValue'];
            } else if ($reportData['inputName'] == 'memCont') {
                $empContributionID = $reportData['reportValue'];
            } else if ($reportData['inputName'] == 'totEarnings') {
                $totalEarningID = $reportData['reportValue'];
            } else {
                $column = '\'' . $reportData['reportValue'] . '\' AS ' . $reportData['fieldName'];
                $otherColumn .= ', ' . $column;
            }
        }

        $joinSeg = "";
        if (!empty($segment)) {
            $whereIN = join(',', $segment);

            $joinSeg = "JOIN (
                            SELECT EmpID AS empIDSegTB FROM srp_erp_payrollmaster AS payMaster
                            JOIN srp_erp_payrollheaderdetails AS payHDet ON payMaster.payrollMasterID=payHDet.payrollMasterID AND payHDet.companyID={$companyID}
                            WHERE payMaster.companyID={$companyID} AND payrollYear={$payYear} AND payrollMonth={$payMonth}
                            AND payHDet.segmentID IN ({$whereIN}) AND approvedYN=1
                       ) AS segmntFillterd ON segmntFillterd.empIDSegTB = empID";
        }

        $totalContribution = $this->db->query("SELECT sum(transactionAmount) AS totalContribution  FROM srp_erp_payrolldetail {$joinSeg}
                                        WHERE companyID={$companyID} AND fromTB='PAY_GROUP'
                                        AND detailTBID IN ({$empContributionID},{$comContributionID}) AND payrollMasterID
                                        IN (
                                            SELECT payrollMasterID FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollYear={$payYear}
                                            AND payrollMonth={$payMonth} AND approvedYN=1
                                        ) ")->row('totalContribution');


        $epfData = $this->db->query("SELECT EIdNo, nic, employerCont, memberCont, REPLACE(FORMAT(ABS(employerCont + memberCont),2), ',', '')  AS toCount,
                                REPLACE(FORMAT(totEarnings,2), ',', '') AS totEarnings,
                                CAST(empConfigDet.memNumber AS SIGNED) AS orderColumn, empConfigDet.* {$otherColumn}
                                FROM srp_employeesdetails AS empTB
                                JOIN
                                (
                                    SELECT empID, REPLACE(FORMAT(sum(ABS(employerC)), 2), ',', '') as employerCont,
                                    REPLACE(FORMAT(sum(ABS(memberC)), 2), ',', '') as memberCont
                                    FROM
                                    (
                                        SELECT empID, if(detailTBID={$comContributionID}, transactionAmount, 0) AS employerC,
                                        if(detailTBID={$empContributionID}, transactionAmount, 0) AS memberC,
                                        transactionCurrencyDecimalPlaces as dPlace, detailTBID
                                        FROM srp_erp_payrolldetail {$joinSeg}
                                        WHERE companyID={$companyID} AND fromTB='PAY_GROUP'
                                        AND detailTBID IN ({$empContributionID},{$comContributionID}) AND payrollMasterID IN (
                                            SELECT payrollMasterID FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollYear={$payYear}
                                            AND payrollMonth={$payMonth} AND approvedYN=1
                                        )
                                    ) AS TB1  GROUP BY empID
                                ) AS memberContTB ON memberContTB.empID = empTB.EIdNo
                                JOIN (
                                    SELECT empID, MAX(initials) AS initials, MAX(memNumber) AS memNumber, MAX(lastName) AS lastName FROM
                                    (
                                        SELECT empID, reportFields.id, inputName, reportID, reportValue,
                                        IF(inputName='lastName',reportValue, '') AS lastName,
                                        IF(inputName='initials', reportValue, '') AS initials,
                                        IF(inputName='memNumber', reportValue, '') AS memNumber
                                        FROM srp_erp_sso_reporttemplatedetails AS reportDetails
                                        JOIN srp_erp_sso_reporttemplatefields AS reportFields ON reportFields.id = reportDetails.reportID
                                        WHERE reportDetails.companyID={$companyID} AND reportFields.isEmployeeLevel=1 AND masterID=1
                                    ) AS tb1 GROUP  BY empID
                                ) AS empConfigDet ON empConfigDet.empID = empTB.EIdNo
                                JOIN (
                                    SELECT empID, transactionAmount AS totEarnings FROM srp_erp_payrolldetailpaygroup WHERE companyID={$companyID}
                                    AND detailTBID={$totalEarningID} AND payrollMasterID IN (
                                        SELECT payrollMasterID FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollYear={$payYear}
                                        AND payrollMonth={$payMonth} AND approvedYN=1
                                    )
                                    GROUP BY empID
                                )AS payGroup ON payGroup.empID=empTB.EIdNo
                                WHERE Erp_companyID={$companyID} GROUP BY empTB.EIdNo ORDER BY orderColumn ASC")->result_array();


        $data['payrollData'] = $payrollData;
        $data['epfData'] = $epfData;
        $data['report_companyData'] = $report_companyData;
        $data['totalContribution'] = $totalContribution;
        $data['responseType'] = $responseType;

        if ($responseType == 'print') {
            $html = $this->load->view('system/hrm/report/cForm_view', $data, true);
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4', 1);
        } else {
            $this->load->view('system/hrm/report/cForm_view', $data);
        }

    }

    function rFrom_reportGenerate()
    {
        $responseType = $this->uri->segment(3);
        $payrollMonth = $this->input->post('payrollMonth');
        $segment = $this->input->post('segment');

        $payYear = date('Y', strtotime($payrollMonth));
        $payMonth = date('m', strtotime($payrollMonth));
        $contPeriod = date('M - Y', strtotime($payrollMonth));
        $companyID = current_companyID();


        $report_companyData = get_ssoReportFields('C', 'ETF');

        $payrollData = $this->db->query("SELECT '{$contPeriod}' AS contPeriod,
                                         (
                                             SELECT reportValue FROM srp_erp_sso_reporttemplatefields AS fieldTB
                                             JOIN srp_erp_sso_reporttemplatedetails AS detTB ON detTB.reportID=fieldTB.id AND companyID={$companyID}
                                             WHERE fieldName='empNo' AND masterID=2 LIMIT 1
                                         ) AS employerNumber ")->row_array();


        $etfContributionID = null;
        $otherColumn = '';

        foreach ($report_companyData as $key => $reportData) {
            if ($reportData['inputName'] == 'etfContribution') {
                $etfContributionID = $reportData['reportValue'];
            } else {
                $column = '\'' . $reportData['reportValue'] . '\' AS ' . $reportData['fieldName'];
                $otherColumn .= ', ' . $column;
            }
        }

        $joinSeg = "";
        if (!empty($segment)) {
            $whereIN = join(',', $segment);

            $joinSeg = "JOIN (
                            SELECT EmpID AS empIDSegTB FROM srp_erp_payrollmaster AS payMaster
                            JOIN srp_erp_payrollheaderdetails AS payHDet ON payMaster.payrollMasterID=payHDet.payrollMasterID AND payHDet.companyID={$companyID}
                            WHERE payMaster.companyID={$companyID} AND payrollYear={$payYear} AND payrollMonth={$payMonth}
                            AND payHDet.segmentID IN ({$whereIN}) AND approvedYN=1
                       ) AS segmntFillterd ON segmntFillterd.empIDSegTB = empID";
        }

        $totalContribution = $this->db->query("SELECT sum(transactionAmount) AS totalContribution  FROM srp_erp_payrolldetail {$joinSeg}
                                        WHERE companyID={$companyID} AND fromTB='PAY_GROUP' AND detailTBID = {$etfContributionID}
                                        AND payrollMasterID IN (
                                            SELECT payrollMasterID FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollYear={$payYear}
                                            AND payrollMonth={$payMonth} AND approvedYN=1
                                        )")->row('totalContribution');

        $etfData = $this->db->query("SELECT EIdNo, nic, etfContribution, CAST(empConfigDet.memNumber AS SIGNED) AS orderColumn,
                                 empConfigDet.* {$otherColumn}
                                FROM srp_employeesdetails AS empTB
                                JOIN
                                (
                                    SELECT empID,
                                    round(sum(ABS(transactionAmount)), 2)AS etfContribution,
                                    transactionCurrencyDecimalPlaces as dPlace, detailTBID
                                    FROM srp_erp_payrolldetail
                                    {$joinSeg}
                                    WHERE companyID={$companyID} AND fromTB='PAY_GROUP' AND detailTBID = {$etfContributionID}
                                    AND payrollMasterID IN (
                                        SELECT payrollMasterID FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollYear={$payYear}
                                        AND payrollMonth={$payMonth} AND approvedYN=1
                                    ) GROUP BY empID
                                ) AS memberContTB ON memberContTB.empID = empTB.EIdNo
                                JOIN (
                                    SELECT empID, MAX(initials) AS initials, MAX(memNumber) AS memNumber, MAX(lastName) AS lastName FROM
                                    (
                                        SELECT empID, reportFields.id, inputName, reportID, reportValue,
                                        IF(inputName='lastName', UPPER(reportValue), '') AS lastName,
                                        IF(inputName='initials', reportValue, '') AS initials,
                                        IF(inputName='memNumber', reportValue, '') AS memNumber
                                        FROM srp_erp_sso_reporttemplatedetails AS reportDetails
                                        JOIN srp_erp_sso_reporttemplatefields AS reportFields ON reportFields.id = reportDetails.reportID
                                        WHERE reportDetails.companyID={$companyID} AND reportFields.isEmployeeLevel=1 AND masterID=2
                                    ) AS tb1 GROUP  BY empID
                                ) AS empConfigDet ON empConfigDet.empID = empTB.EIdNo
                                WHERE Erp_companyID={$companyID} ORDER BY orderColumn ASC")->result_array();

        $data['payrollData'] = $payrollData;
        $data['etfData'] = $etfData;
        $data['totalContribution'] = $totalContribution;
        $data['responseType'] = $responseType;

        if ($responseType == 'print') {
            $html = $this->load->view('system/hrm/report/rForm_view', $data, true);
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4', 1);
        } else {
            $this->load->view('system/hrm/report/rForm_view', $data);
        }
    }

    function etfReturn_reportGenerate()
    {

        $responseType = $this->uri->segment(3);
        $fromDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');
        $segment = $this->input->post('segment');
        $companyID = current_companyID();
        $fromDate = $fromDate . '-01';
        $toDate = $toDate . '-01';
        $fromDate_obj = DateTime::createFromFormat('Y-m-d', $fromDate);
        $fromDate = $fromDate_obj->format('Y-m-d');

        $toDate_Obj = DateTime::createFromFormat('Y-m-d', $toDate);
        $toDate = $toDate_Obj->format('Y-m-t');


        $report_companyData = get_ssoReportFields('C', 'ETF');

        $totEarningsID = $this->db->query("SELECT reportValue FROM srp_erp_sso_reporttemplatefields AS fieldTB
                                           JOIN srp_erp_sso_reporttemplatedetails AS detTB ON detTB.reportID=fieldTB.id AND companyID={$companyID}
                                           WHERE fieldName='totEarnings' AND masterID=1 ")->row('reportValue');

        $payrollMastersData = $this->db->query("SELECT * FROM (
                                                    SELECT payrollMasterID, MONTHNAME(STR_TO_DATE(payrollMonth, '%m')) AS payrollMonth,
                                                    STR_TO_DATE( CONCAT(payrollYear,'-', payrollMonth, '-01'), '%Y-%m-%d') AS payrollDate,
                                                    CONCAT(payrollYear, payrollMonth) AS payYearMonth
                                                    FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND approvedYN=1
                                                ) AS dateTB
                                                WHERE payrollDate BETWEEN '{$fromDate}' AND '{$toDate}' GROUP BY payrollDate")->result_array();


        $ifConditionETF_str = '';
        $maxETF_str = '';
        $ifConditionTotalEarnings_str = '';
        $maxTotalEarnings_str = '';
        $separator = '';
        $plus = '';
        $totalContribution_str = '( ';
        //echo '<pre>'; print_r($payrollMastersData); echo '</pre>';
        foreach ($payrollMastersData as $keyMonth => $payrollMonthDataRow) {
            $payYearMonth = $payrollMonthDataRow['payYearMonth'];
            $rowETFData = 'etfContribution_' . $payYearMonth;
            $rowTotalEarningsData = 'totalEarnings_' . $payYearMonth;

            $ifConditionETF_str .= $separator . ' IF( payYearMonthStr=' . $payYearMonth . ', etfContribution, 0 ) AS ' . $rowETFData;
            $ifConditionTotalEarnings_str .= $separator . ' IF( payYearMonthStr=' . $payYearMonth . ', totalEarnings, 0 ) AS ' . $rowTotalEarningsData;
            $maxETF_str .= $separator . ' MAX( ' . $rowETFData . ' ) AS ' . $rowETFData;
            $maxTotalEarnings_str .= $separator . ' MAX( ' . $rowTotalEarningsData . ' ) AS ' . $rowTotalEarningsData;
            $totalContribution_str .= $plus . '' . $rowETFData;
            $separator = ',';
            $plus = '+';
        }

        $totalContribution_str .= ' ) AS totalContribution';

        $payrollID_arr = $this->db->query("SELECT * FROM (
                                              SELECT payrollMasterID, STR_TO_DATE( CONCAT(payrollYear,'-', payrollMonth, '-01'), '%Y-%m-%d') AS payrollDate
                                              FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND approvedYN=1
                                           ) AS dateTB
                                           WHERE payrollDate BETWEEN '{$fromDate}' AND '{$toDate}' ORDER BY payrollDate")->result_array();
        if (empty($payrollID_arr)) {
            echo '<div class="col-sm-12">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong>
                        </br>There is no payroll processed on selected date period
                    </div>
                  </div>';
            exit;
        }

        $whereIN = implode(',', array_column($payrollID_arr, 'payrollMasterID'));

        $etfContributionID = null;
        $employerNo = null;
        $otherColumn = '';

        foreach ($report_companyData as $key => $reportData) {
            if ($reportData['inputName'] == 'etfContribution') {
                $etfContributionID = $reportData['reportValue'];
            } else if ($reportData['inputName'] == 'empNo') {
                $employerNo = $reportData['reportValue'];
            } else {
                $column = '\'' . $reportData['reportValue'] . '\' AS ' . $reportData['fieldName'];
                $otherColumn .= ', ' . $column;
            }
        }

        $whereIN_segment = "";
        if (!empty($segment)) {
            $whereIN_segment = 'AND segmentID IN (' . join(',', $segment) . ' )';
        }


        $etfData = $this->db->query("SELECT EIdNo, nic,  CAST(empConfigDet.memNumber AS SIGNED) AS orderColumn,
                                    empConfigDet.* {$otherColumn} , memberContTB.*, {$totalContribution_str}, totalEarningsTB.*
                                    FROM srp_employeesdetails AS empTB
                                    JOIN
                                    (
                                        SELECT empID, $maxETF_str FROM
                                        (
                                            SELECT empID, {$ifConditionETF_str} FROM
                                            (
                                                SELECT empID, payrollMasterID, payYearMonthStr,
                                                round(sum(ABS(transactionAmount)), 2)AS etfContribution, transactionCurrencyDecimalPlaces as dPlace, detailTBID
                                                FROM srp_erp_payrolldetail
                                                JOIN (
                                                     SELECT payrollMasterID AS payMasterID, CONCAT(payrollYear,payrollMonth) AS payYearMonthStr
                                                     FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollMasterID IN ( {$whereIN} )
                                                ) AS payYearMonthStrTB ON payYearMonthStrTB.payMasterID = srp_erp_payrolldetail.payrollMasterID
                                                WHERE companyID={$companyID} AND fromTB='PAY_GROUP' AND detailTBID = {$etfContributionID} {$whereIN_segment}
                                                GROUP BY empID, payrollMasterID
                                            ) AS dataTB
                                        ) AS dataTB2 GROUP BY empID
                                    ) AS memberContTB ON memberContTB.empID = empTB.EIdNo
                                    JOIN
                                    (
                                        SELECT empID AS empIDTotalEarnings, $maxTotalEarnings_str FROM
                                        (
                                            SELECT empID, {$ifConditionTotalEarnings_str} FROM
                                            (
                                                SELECT empID, payrollMasterID, payYearMonthStr,
                                                round(sum(ABS(transactionAmount)), 2)AS totalEarnings, transactionCurrencyDecimalPlaces as dPlace, detailTBID
                                                FROM srp_erp_payrolldetailpaygroup
                                                JOIN (
                                                     SELECT payrollMasterID AS payMasterID, CONCAT(payrollYear,payrollMonth) AS payYearMonthStr
                                                     FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollMasterID IN ( {$whereIN} )
                                                ) AS payYearMonthStrTB ON payYearMonthStrTB.payMasterID = srp_erp_payrolldetailpaygroup.payrollMasterID
                                                WHERE companyID={$companyID} AND fromTB='PAY_GROUP' AND detailTBID = {$totEarningsID} {$whereIN_segment}
                                                GROUP BY empID, payrollMasterID
                                            ) AS dataTB
                                        ) AS dataTB2 GROUP BY empID
                                    ) AS totalEarningsTB ON totalEarningsTB.empIDTotalEarnings = empTB.EIdNo
                                    JOIN (
                                        SELECT empID, MAX(initials) AS initials, MAX(memNumber) AS memNumber, MAX(lastName) AS lastName FROM
                                        (
                                            SELECT empID, reportFields.id, inputName, reportID, reportValue,
                                            IF(inputName='lastName', UPPER(reportValue), '') AS lastName,
                                            IF(inputName='initials', reportValue, '') AS initials,
                                            IF(inputName='memNumber', reportValue, '') AS memNumber
                                            FROM srp_erp_sso_reporttemplatedetails AS reportDetails
                                            JOIN srp_erp_sso_reporttemplatefields AS reportFields ON reportFields.id = reportDetails.reportID
                                            WHERE reportDetails.companyID={$companyID} AND reportFields.isEmployeeLevel=1 AND masterID=2
                                        ) AS tb1 GROUP  BY empID
                                    ) AS empConfigDet ON empConfigDet.empID = empTB.EIdNo
                                    WHERE Erp_companyID={$companyID} ORDER BY orderColumn ASC")->result_array();

        //echo $this->db->last_query();
        //die();

        if (empty($etfData)) {
            echo '<div class="col-sm-12">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong>
                        </br>There is no payroll processed on selected date period
                    </div>
                  </div>';
            exit;
        }

        $employerNo_arr = explode(' ', $employerNo);
        if (count($employerNo_arr) > 1) {
            $employerNo = $employerNo_arr[0] . '&nbsp;' . $employerNo_arr[1];
        }

        $data['payrollMastersData'] = $payrollMastersData;
        $data['employerNo'] = $employerNo;
        $data['etfData'] = $etfData;
        $data['report_companyData'] = $report_companyData;
        $data['responseType'] = $responseType;


        if ($responseType == 'print') {
            $period = $fromDate_obj->format('F') . ' TO ' . $toDate_Obj->format('F Y');
            $data['period'] = $period;
            $html = $this->load->view('system/hrm/report/etf_return_view', $data, true);
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4-L', 1);
        } else if ($responseType == 'excel') {
            $period = $fromDate_obj->format('F') . ' - ' . $toDate_Obj->format('F Y');
            $data['period'] = $period;
            $this->etfReturn_reportGenerate_excel($data);
        } else {
            $period = $fromDate_obj->format('F') . ' TO ' . $toDate_Obj->format('F Y');
            $data['period'] = $period;
            $this->load->view('system/hrm/report/etf_return_view', $data);
        }
    }

    function etfReturn_reportGenerate_excel($data)
    {
        $payrollMastersData = $data['payrollMastersData'];

        $header = '<table>';
        $header .= '<thead>';
        $header .= '<tr>';
        $header .= '<th rowspan="2">#</th>';
        $header .= '<th rowspan="2">MEMBERS NAME</th>';
        $header .= '<th rowspan="2">NIC No</th>';
        $header .= '<th rowspan="2">MEM S No</th>';
        $header .= '<th>TOTAL</th>';

        foreach ($payrollMastersData as $row) {
            $header .= '<th colspan="2">' . $row['payrollMonth'] . '</th>';
        }

        $header .= '</tr>';
        $header .= '<tr>';
        $header .= '<th> CONTRIB.</th>';

        foreach ($payrollMastersData as $rowData) {
            $header .= '<th>Earnings</th>';
            $header .= '<th>Contrib.</th>';
        }

        $header = array('#', 'MEMBERS NAME', 'NIC No');
        $this->load->library('excel');

        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Users list');

        // Header
        $this->excel->getActiveSheet()->fromArray($header, null, 'A1');
        // Data
        //$this->excel->getActiveSheet()->fromArray($users, null, 'A2');
        //set aligment to center for that merged cell (A1 to D1)
        $filename = 'ETF Return ' . $data['period'] . '.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');

    }

    function save_payeeSetUp()
    {
        $this->form_validation->set_rules('payeeID', 'Payee', 'trim|required');
        $this->form_validation->set_rules('cashBenefits', 'Cash Benefits', 'trim|required');
        $this->form_validation->set_rules('regNo', 'PAYE Registration No', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $this->db->trans_start();
            $payeeID = $this->input->post('payeeID');
            $cashBenefits = $this->input->post('cashBenefits');
            $regNo = $this->input->post('regNo');

            $where = array(
                'masterID' => 4,
                'companyID' => current_companyID()
            );

            $this->db->delete('srp_erp_sso_reporttemplatedetails', $where);

            $data['masterID'] = '4';
            $data['reportID'] = '1'; /*Payee*/
            $data['reportValue'] = $payeeID;
            $data['companyID'] = current_companyID();
            $data['companyCode'] = current_companyCode();
            $data['createdPCID'] = current_pc();
            $data['createdUserID'] = current_userID();
            $data['createdUserGroup'] = current_user_group();
            $data['createdUserName'] = current_employee();
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_sso_reporttemplatedetails', $data);


            $data['reportID'] = '2'; /*Cash Benefits*/
            $data['reportValue'] = $cashBenefits;
            $this->db->insert('srp_erp_sso_reporttemplatedetails', $data);


            $data['reportID'] = '3'; /*PAYE Reg No*/
            $data['reportValue'] = $regNo;
            $this->db->insert('srp_erp_sso_reporttemplatedetails', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                echo json_encode(['s', 'Process successfully']);
            } else {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Error in process']);
            }
        }
    }

    function income_tax_deduction()
    {
        $responseType = $this->uri->segment(3);
        $payrollMonth = $this->input->post('payrollMonth');
        $segment = $this->input->post('segment');

        $payYear = date('Y', strtotime($payrollMonth));
        $payMonth = date('m', strtotime($payrollMonth));

        $companyID = current_companyID();
        $reportConfig = get_defaultPayeeSetup();
        $payeeID = $reportConfig['payee']; // Actually pay group id
        $cashBenefitID = $reportConfig['payGroup']; // Actually pay group id


        $payrollID_arr = $this->db->query("SELECT payrollMasterID FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND payrollYear={$payYear}
                                           AND payrollMonth={$payMonth} AND approvedYN=1")->result_array();

        if (empty($payrollID_arr)) {
            echo '<div class="" style="margin-top: 10px;">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong>
                        </br>There is no payroll processed on selected date period
                    </div>
                  </div>';
            exit;
        }

        $whereINPayroll = implode(',', array_column($payrollID_arr, 'payrollMasterID'));


        $whereIN_segment1 = "";
        $whereIN_segment2 = "";
        if (!empty($segment)) {
            $whereIN_segment1 = 'AND payrolldetail.segmentID IN (' . join(',', $segment) . ' )';
            $whereIN_segment2 = 'AND payrollGroup.segmentID IN (' . join(',', $segment) . ' )';
        }

        $payeeData = $this->db->query("SELECT NIC, ABS( FLOOR(payrolldetail.transactionAmount) ) AS payee,
                                       ABS( FLOOR(payrollGroup.transactionAmount) ) AS cashBenefit, Ename1 AS fullName
                                       FROM srp_erp_payrollmaster AS payrollmaster
                                       JOIN srp_erp_payrolldetail AS payrolldetail ON payrolldetail.payrollMasterID=payrollmaster.payrollMasterID
                                       AND payrolldetail.companyID={$companyID} AND payrolldetail.payrollMasterID IN ( {$whereINPayroll} ) {$whereIN_segment1}
                                       AND payrolldetail.detailTBID={$payeeID} AND payrolldetail.calculationTB='PAY_GROUP'
                                       JOIN srp_erp_payrolldetailpaygroup AS payrollGroup ON payrollGroup.payrollMasterID=payrollmaster.payrollMasterID
                                       AND payrollGroup.companyID={$companyID} AND payrollGroup.payrollMasterID IN ( {$whereINPayroll} ) {$whereIN_segment2}
                                       AND payrollGroup.detailTBID={$cashBenefitID} AND payrolldetail.empID=payrollGroup.empID
                                       JOIN srp_employeesdetails AS empTB ON empTB.EIdNo=payrolldetail.empID AND Erp_companyID={$companyID}
                                       WHERE payrollmaster.companyID={$companyID} AND payrollmaster.payrollMasterID IN ( {$whereINPayroll} )")->result_array();

        if (empty($payeeData)) {
            echo '<div class="" style="margin-top: 10px;">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong>
                        </br>There is no payroll processed on selected date period
                    </div>
                  </div>';
            exit;
        }

        $data['payeeData'] = $payeeData;
        $data['fromDate'] = date('Y-m-d', strtotime($payrollMonth));;
        $this->load->library('NumberToWords');

        if ($responseType == 'print') {
            echo $this->load->view('system/hrm/report/income_tax_deduction_print', $data, true);
        } else if ($responseType == 'excel') {
            //$this->etfReturn_reportGenerate_excel($data);
        } else {
            $this->load->view('system/hrm/report/income_tax_deduction_view', $data);
        }
    }

    function payee_registration()
    {
        $responseType = $this->uri->segment(3);
        $segment = $this->input->post('segment');
        $fromDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');

        $fromDate = $fromDate . '-01';
        $toDate = $toDate . '-01';
        $fromDate_obj = DateTime::createFromFormat('Y-m-d', $fromDate);
        $fromDate = $fromDate_obj->format('Y-m-d');

        $toDate_Obj = DateTime::createFromFormat('Y-m-d', $toDate);
        $toDate = $toDate_Obj->format('Y-m-t');

        $companyID = current_companyID();
        $reportConfig = get_defaultPayeeSetup();
        $payeeID = $reportConfig['payee']; // Actually pay group id
        $cashBenefitID = $reportConfig['payGroup']; // Actually pay group id
        $data['regNo'] = $reportConfig['regNo']; // PAYE registration no.
        $data['fromDate'] = date('d-m-Y', strtotime($fromDate));
        $data['toDate'] = date('d-m-Y', strtotime($toDate));


        $payrollID_arr = $this->db->query("SELECT * FROM (
                                                SELECT payrollMasterID, MONTHNAME(STR_TO_DATE(payrollMonth, '%m')) AS payrollMonth,
                                                STR_TO_DATE( CONCAT(payrollYear,'-', payrollMonth, '-01'), '%Y-%m-%d') AS payrollDate,
                                                CONCAT(payrollYear, payrollMonth) AS payYearMonth
                                                FROM srp_erp_payrollmaster WHERE companyID={$companyID} AND approvedYN=1
                                            ) AS dateTB
                                            WHERE payrollDate BETWEEN '{$fromDate}' AND '{$toDate}'")->result_array();

        if (empty($payrollID_arr)) {
            echo '<div class="" style="margin-top: 10px;">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong>
                        </br>There is no payroll processed on selected date period
                    </div>
                  </div>';
            exit;
        }

        $whereINPayroll = implode(',', array_column($payrollID_arr, 'payrollMasterID'));


        $whereIN_segment1 = "";
        $whereIN_segment2 = "";
        if (!empty($segment)) {
            $whereIN_segment1 = 'AND payrolldetail.segmentID IN (' . join(',', $segment) . ' )';
            $whereIN_segment2 = 'AND payrollGroup.segmentID IN (' . join(',', $segment) . ' )';
        }

        $payeeData = $this->db->query("SELECT NIC, ABS( FLOOR(SUM(payrolldetail.transactionAmount)) ) AS payee, Ename2 AS nameWithIn,
                                       ABS( FLOOR(SUM(payrollGroup.transactionAmount)) ) AS cashBenefit, DesDescription AS desgination
                                       FROM srp_erp_payrollmaster AS payrollmaster
                                       JOIN srp_erp_payrolldetail AS payrolldetail ON payrolldetail.payrollMasterID=payrollmaster.payrollMasterID
                                       AND payrolldetail.companyID={$companyID} AND payrolldetail.payrollMasterID IN ( {$whereINPayroll} ) {$whereIN_segment1}
                                       AND payrolldetail.detailTBID={$payeeID} AND payrolldetail.calculationTB='PAY_GROUP'
                                       JOIN srp_erp_payrolldetailpaygroup AS payrollGroup ON payrollGroup.payrollMasterID=payrollmaster.payrollMasterID
                                       AND payrollGroup.companyID={$companyID} AND payrollGroup.payrollMasterID IN ( {$whereINPayroll} ) {$whereIN_segment2}
                                       AND payrollGroup.detailTBID={$cashBenefitID} AND payrolldetail.empID=payrollGroup.empID
                                       JOIN srp_employeesdetails AS empTB ON empTB.EIdNo=payrolldetail.empID AND empTB.Erp_companyID={$companyID}
                                       JOIN srp_designation ON srp_designation.DesignationID = empTB.EmpDesignationId AND srp_designation.Erp_companyID={$companyID}
                                       WHERE payrollmaster.companyID={$companyID} AND payrollmaster.payrollMasterID IN ( {$whereINPayroll} )
                                       GROUP BY payrolldetail.empID ORDER BY empTB.EmpSecondaryCode DESC")->result_array();

        if (empty($payeeData)) {
            echo '<div class="" style="margin-top: 10px;">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong>
                        </br>There is no payroll processed on selected date period
                    </div>
                  </div>';
            exit;
        }

        $data['payeeData'] = $payeeData;

        if ($responseType == 'print') {
            $html = $this->load->view('system/hrm/report/payee_registration_view', $data, true);
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4', 1);
        } else if ($responseType == 'excel') {
            //$this->etfReturn_reportGenerate_excel($data);
        } else {
            $this->load->view('system/hrm/report/payee_registration_view', $data);
        }
    }

    function save_salaryComparisonFormula()
    {
        $companyID = current_companyID();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $createdUserName = current_employee();
        $createdDateTime = current_date();

        $masterID = $this->input->post('payGroupID');
        $formulaStr = $this->input->post('formulaString');
        $salaryCategories = $this->input->post('salaryCategoryContainer');
        $salaryCategories = (trim($salaryCategories) == '') ? null : $salaryCategories;
        $ssoCategories = $this->input->post('SSOContainer');
        $ssoCategories = (trim($ssoCategories) == '') ? null : $ssoCategories;
        $payGroupCategories = $this->input->post('payGroupContainer');
        $payGroupCategories = (trim($payGroupCategories) == '') ? null : $payGroupCategories;


        $formulaID = $this->db->query("SELECT formulaID FROM srp_erp_salarycomparisonformula
                                     WHERE masterID={$masterID} AND companyID={$companyID}")->row('formulaID');


        $this->db->trans_start();

        $data = array(
            'formulaStr' => $formulaStr,
            'salaryCategories' => $salaryCategories,
            'ssoCategories' => $ssoCategories,
            'payGroupCategories' => $payGroupCategories
        );

        if (!empty($formulaID)) {
            $data['modifiedUserID'] = $createdPCID;
            $data['modifiedUserID'] = $createdUserID;
            $data['modifiedDateTime'] = $createdDateTime;
            $data['modifiedUserName'] = $createdUserName;

            $this->db->where(array('companyID' => $companyID, 'masterID' => $masterID))->update('srp_erp_salarycomparisonformula', $data);
        } else {
            $data['masterID'] = $masterID;
            $data['companyID'] = $companyID;
            $data['companyCode'] = current_companyCode();
            $data['createdUserGroup'] = current_user_group();
            $data['createdPCID'] = $createdPCID;
            $data['createdUserID'] = $createdUserID;
            $data['createdDateTime'] = $createdDateTime;
            $data['createdUserName'] = $createdUserName;
            $this->db->insert('srp_erp_salarycomparisonformula', $data);
        }


        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            echo json_encode(['s', 'Formula successfully updated']);
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in formula update process']);
        }
    }

    function salaryComparison_reportGenerate()
    {
        $this->load->helper('template_paySheet');
        $this->load->helper('employee');

        $companyID = current_companyID();
        $responseType = $this->uri->segment(3);
        $firstMonth = $this->input->post('firstMonth');
        $secondMonth = $this->input->post('secondMonth');
        $isNonPayroll = 'N';
        $salary_categories_arr = salary_categories(array('A', 'D'));
        $payGroup_arr = get_payGroup();

        $firstMonth_arr = $this->db->query("SELECT payrollMasterID FROM(
                                                SELECT payrollMasterID, DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y-%m-%d') payrollDate
                                                FROM srp_erp_payrollmaster WHERE companyID={$companyID}
                                             ) AS payrollDateTB WHERE payrollDate='{$firstMonth}' ")->result_array();


        $secondMonth_arr = $this->db->query("SELECT payrollMasterID FROM(
                                                SELECT payrollMasterID, DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y-%m-%d') payrollDate
                                                FROM srp_erp_payrollmaster WHERE companyID={$companyID}
                                             ) AS payrollDateTB WHERE payrollDate='{$secondMonth}' ")->result_array();


        $firstMonth_whereIN = implode(',', array_column($firstMonth_arr, 'payrollMasterID'));
        $secondMonth_whereIN = implode(',', array_column($secondMonth_arr, 'payrollMasterID'));
        $allMonth_arr = array_merge($firstMonth_arr, $secondMonth_arr);
        $allMonth_whereIN = implode(',', array_column($allMonth_arr, 'payrollMasterID'));


        $formula_arr = get_salaryComparison();


        $selectStr = '';
        foreach ($formula_arr as $key => $rowFormula) {
            $masterID = $rowFormula['masterID'];
            $formulaBuilder = payGroup_formulaBuilder_to_sql('decode', $rowFormula['formulaStr'], $salary_categories_arr, $payGroup_arr);

            $formulaDecode = $formulaBuilder['formulaDecode'];
            $select_monthlyAD_str = trim($formulaBuilder['select_monthlyAD_str']);
            $select_salCat_str = trim($formulaBuilder['select_salaryCat_str']);
            $select_group_str = trim($formulaBuilder['select_group_str']);
            $whereInClause = trim($formulaBuilder['whereInClause']);
            $where_MA_MD_Clause = $formulaBuilder['where_MA_MD_Clause'];
            $whereInClause_group = trim($formulaBuilder['whereInClause_group']);


            $where_MA_MD_Clause_str = '';
            if (!empty($where_MA_MD_Clause)) {
                if (count($where_MA_MD_Clause) > 1) {
                    $where_MA_MD_Clause_str = ' calculationTB = \'' . $where_MA_MD_Clause[0] . '\' OR calculationTB = \'' . $where_MA_MD_Clause[1] . '\'';
                } else {
                    $where_MA_MD_Clause_str = ' calculationTB = \'' . $where_MA_MD_Clause[0] . '\'';
                }
            }


            if ($select_monthlyAD_str != '') {
                $select_monthlyAD_str .= ',';
            }

            if ($whereInClause != '' && $select_salCat_str != '') {
                $select_salCat_str .= ',';
                $whereInClause = 'salCatID IN (' . $whereInClause . ') AND calculationTB = \'SD\'';

            }

            if ($whereInClause_group != '' && $select_group_str != '') {
                $select_group_str .= ',';
                $whereInClause_group = 'detailTBID IN (' . $whereInClause_group . ') AND fromTB = \'PAY_GROUP\'';
            }


            if ($whereInClause != '' && $whereInClause_group != '') {
                $whereIN = $whereInClause . ' OR ' . $whereInClause_group;
            } else {
                $whereIN = $whereInClause . ' ' . $whereInClause_group;
            }

            if (trim($whereIN) == '') {
                $whereIN = (trim($where_MA_MD_Clause_str) == '') ? '' : 'AND (' . $where_MA_MD_Clause_str . ' )';
            } else {
                $MA_MD_Clause_str_join = (trim($where_MA_MD_Clause_str) == '') ? '' : ' OR ' . $where_MA_MD_Clause_str;
                $whereIN = 'AND (' . $whereIN . ' ' . $MA_MD_Clause_str_join . ')';
            }


            $detailTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrolldetail' : 'srp_erp_payrolldetail';


            $description_str = trim(implode('_', explode(' ', $rowFormula['description'])));


            $selectStr .= " LEFT JOIN (
                               SELECT calculationTB.empID AS empNo, round((" . $formulaDecode . "), transactionCurrencyDecimalPlaces) AS fr_amount_{$masterID}
                               FROM (
                                     SELECT payDet.empID, fromTB, detailType, salCatID, " . $select_salCat_str . " " . $select_group_str . " " . $select_monthlyAD_str . "
                                     transactionCurrencyDecimalPlaces
                                     FROM {$detailTB} AS payDet
                                     JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = payDet.empID AND empTB.Erp_companyID={$companyID}
                                     WHERE payDet.companyID = {$companyID} AND payrollMasterID IN ({$firstMonth_whereIN}) {$whereIN}
                                     GROUP BY payDet.empID, salCatID, payDet.fromTB, detailTBID
                               ) calculationTB GROUP BY empID
                           ) AS fr_{$description_str}TB ON fr_{$description_str}TB.empNo=payHeader.EmpID ";

            $selectStr .= " LEFT JOIN (
                               SELECT calculationTB.empID AS empNo, round((" . $formulaDecode . "), transactionCurrencyDecimalPlaces) AS sn_amount_{$masterID}
                               FROM (
                                     SELECT payDet.empID, fromTB, detailType, salCatID, " . $select_salCat_str . " " . $select_group_str . " " . $select_monthlyAD_str . "
                                     transactionCurrencyDecimalPlaces
                                     FROM {$detailTB} AS payDet
                                     JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = payDet.empID AND empTB.Erp_companyID={$companyID}
                                     WHERE payDet.companyID = {$companyID} AND payrollMasterID IN ({$secondMonth_whereIN}) {$whereIN}
                                     GROUP BY payDet.empID, salCatID, payDet.fromTB, detailTBID
                               ) calculationTB GROUP BY empID
                           ) AS sn_{$description_str}TB ON sn_{$description_str}TB.empNo=payHeader.EmpID ";


        }

        $records = $this->db->query("SELECT Ecode, Ename2, transactionCurrencyDecimalPlaces AS dPlace, fr_amount_1, sn_amount_1, fr_amount_2, sn_amount_2, fr_amount_3,
                                     sn_amount_3, fr_amount_4, sn_amount_4, fr_amount_5, sn_amount_5, fr_amount_6, sn_amount_6, fr_amount_7, sn_amount_7
                                     FROM srp_erp_payrollmaster AS payMaster
                                     JOIN srp_erp_payrollheaderdetails AS payHeader ON payHeader.payrollMasterID = payMaster.payrollMasterID
                                     AND payHeader.companyID={$companyID}
                                     {$selectStr}
                                     WHERE payMaster.companyID={$companyID} AND payMaster.payrollMasterID IN ({$allMonth_whereIN})
                                     GROUP BY payHeader.EmpID")->result_array();

        $data['reportData'] = $records;
        $html = $this->load->view('system/hrm/report/salary-comparison-table-view', $data, true);

        if ($responseType == 'print') {
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4', 1);
        } else {
            echo $html;
        }
    }

    function load_subcat()
    {
        echo json_encode($this->Report_model->load_subcat());
    }

    function load_subsubcat()
    {
        echo json_encode($this->Report_model->load_subsubcat());
    }

    function loadItems()
    {
        if ($this->input->post("type") == 1) {
            echo json_encode($this->Report_model->loadItems());
        } else {
            echo json_encode($this->Report_model->loadGroupItems());
        }
    }

    function get_collection_summery_report()
    {
        $currency = $this->input->post('currency');
        $financeyear = $this->input->post('financeyear');
        $this->db->select('beginingDate,endingDate');
        $this->db->where('companyFinanceYearID', $financeyear);
        $this->db->from('srp_erp_companyfinanceyear ');
        $financeyeardtl = $this->db->get()->row_array();
        $beginingDate = $financeyeardtl['beginingDate'];
        $endingDate = $financeyeardtl['endingDate'];

        $start = (new DateTime($beginingDate));
        $end = (new DateTime($endingDate));

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $datearr = [];
        foreach ($period as $dt) {
            $dat = $dt->format("Y-m");
            $text = $dt->format("Y-M");
            $datearr[$dat] = $text;
        }

        $this->db->select('max(beginingDate) as beginingDate,max(endingDate) as endingDate');
        $this->db->where('beginingDate < ', $beginingDate);
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_companyfinanceyear');
        $previuosyeardtl = $this->db->get()->row_array();

        $previousbegindate = $previuosyeardtl['beginingDate'];
        $previousenddate = $previuosyeardtl['endingDate'];

        //echo '<pre>';print_r($datearr); echo '</pre>'; die();

        $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
        $this->form_validation->set_rules('segment[]', 'Segment', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $data["details"] = $this->Report_model->get_collection_summery_report($datearr, $previousbegindate, $previousenddate, $beginingDate, $endingDate);
            $data["header"] = $datearr;
            $data["previousbeginingdate"] = $previousbegindate;
            $data["previousenddate"] = $previousenddate;
            $data["type"] = "html";
            $data["currency"] = $currency;
            echo $html = $this->load->view('system/accounts_receivable/report/load-collection-summary-report', $data, true);
        }
    }

    function get_collection_details_drilldown_report()
    {
        $currency = $this->input->post('currency');

        $data["customers"] = $this->Report_model->customer_name();
        $data["details"] = $this->Report_model->get_revanue_details_drilldown_report();
        $data["type"] = "html";
        $data["currency"] = $currency;
        echo $html = $this->load->view('system/accounts_receivable/report/load-collection-summary-dd-report', $data, true);

    }

    function get_collection_summery_report_pdf()
    {
        $currency = $this->input->post('currency');
        $financeyear = $this->input->post('financeyear');
        $this->db->select('beginingDate,endingDate');
        $this->db->where('companyFinanceYearID', $financeyear);
        $this->db->from('srp_erp_companyfinanceyear ');
        $financeyeardtl = $this->db->get()->row_array();
        $beginingDate = $financeyeardtl['beginingDate'];
        $endingDate = $financeyeardtl['endingDate'];

        $start = (new DateTime($beginingDate));
        $end = (new DateTime($endingDate));

        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $datearr = [];
        foreach ($period as $dt) {
            $dat = $dt->format("Y-m");
            $text = $dt->format("Y-M");
            $datearr[$dat] = $text;
        }

        $this->db->select('max(beginingDate) as beginingDate,max(endingDate) as endingDate');
        $this->db->where('beginingDate < ', $beginingDate);
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_companyfinanceyear');
        $previuosyeardtl = $this->db->get()->row_array();

        $previousbegindate = $previuosyeardtl['beginingDate'];
        $previousenddate = $previuosyeardtl['endingDate'];

        $data["details"] = $this->Report_model->get_collection_summery_report($datearr, $previousbegindate, $previousenddate, $beginingDate, $endingDate);
        $data["header"] = $datearr;
        $data["type"] = "pdf";
        $data["currency"] = $currency;
        $html = $this->load->view('system/accounts_receivable/report/load-collection-summary-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }


    function get_collection_previous_details_drilldown_report()
    {
        $currency = $this->input->post('currency');

        $data["customers"] = $this->Report_model->customer_name();
        $data["details"] = $this->Report_model->get_revanue_previous_details_drilldown_report();
        $data["type"] = "html";
        $data["currency"] = $currency;
        echo $html = $this->load->view('system/accounts_receivable/report/load-collection-summary-dd-report', $data, true);

    }

    function get_collection_detail_report()
    {
        $currency = $this->input->post('currency');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $customer = $this->input->post('customerID');
        $segment = $this->input->post('segment');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
              Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">
              Date To is  required
            </div>';
        } else {
            $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
            } else {
                $data["details"] = $this->Report_model->get_collection_detail_reports($currency, $customer, $segment);
                $data["type"] = "html";
                $data["currency"] = $currency;
                echo $html = $this->load->view('system/accounts_receivable/report/load-collection-details-report', $data, true);
            }
        }
    }

    function get_collection_detail_report_pdf()
    {
        $currency = $this->input->post('currency');
        $customer = $this->input->post('customerID');
        $segment = $this->input->post('segment');

        $data["details"] = $this->Report_model->get_collection_detail_reports($currency, $customer, $segment);
        $data["type"] = "pdf";
        $data["currency"] = $currency;

        $html = $this->load->view('system/accounts_receivable/report/load-collection-details-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');

    }

    function group_customer_linked()
    {
        return $this->Report_model->group_customer_linked();
    }

    function group_supplier_linked()
    {
        return $this->Report_model->group_supplier_linked();
    }

    function group_chartofaccount_linked()
    {
        return $this->Report_model->group_chartofaccount_linked();
    }

    function group_segment_linked()
    {
        return $this->Report_model->group_segment_linked();
    }

    function group_item_linked()
    {
        return $this->Report_model->group_item_linked();
    }

    function group_warehouse_linked()
    {
        return $this->Report_model->group_warehouse_linked();
    }


    function group_unlink($report)
    {
        $errorHTML = "";
        if (in_array('ITM', $report)) {
            if ($this->group_item_linked()) {
                $errorHTML .= "<h4>Please link the following items</h4>";
                $errorHTML .= "<ul>";
                foreach ($this->group_item_linked() as $val) {
                    $errorHTML .= "<li>" . $val . "</li>";
                }
                $errorHTML .= "</ul>";
            }
        }
        if (in_array('WH', $report)) {
            if ($this->group_warehouse_linked()) {
                $errorHTML .= "<h4>Please link the following Warehouse</h4>";
                $errorHTML .= "<ul>";
                foreach ($this->group_warehouse_linked() as $val) {
                    $errorHTML .= "<li>" . $val . "</li>";
                }
                $errorHTML .= "</ul>";
            }
        }

        if (in_array('CUST', $report)) {
            if ($this->group_customer_linked()) {
                $errorHTML .= "<h4>Please link the following customer</h4>";
                $errorHTML .= "<ul>";
                foreach ($this->group_customer_linked() as $val) {
                    $errorHTML .= "<li>" . $val . "</li>";
                }
                $errorHTML .= "</ul>";
            }
        }

        if (in_array('SUPP', $report)) {
            if ($this->group_supplier_linked()) {
                $errorHTML .= "<h4>Please link the following supplier</h4>";
                $errorHTML .= "<ul>";
                foreach ($this->group_supplier_linked() as $val) {
                    $errorHTML .= "<li>" . $val . "</li>";
                }
                $errorHTML .= "</ul>";
            }
        }

        if (in_array('SEG', $report)) {
            if ($this->group_segment_linked()) {
                $errorHTML .= "<h4>Please link the following segment</h4>";
                $errorHTML .= "<ul>";
                foreach ($this->group_segment_linked() as $val) {
                    $errorHTML .= "<li>" . $val . "</li>";
                }
                $errorHTML .= "</ul>";
            }
        }

        if (in_array('CA', $report)) {
            if ($this->group_chartofaccount_linked()) {
                $errorHTML .= "<h4>Please link the following chart of account</h4>";
                $errorHTML .= "<ul>";
                foreach ($this->group_chartofaccount_linked() as $val) {
                    $errorHTML .= "<li>" . $val . "</li>";
                }
                $errorHTML .= "</ul>";
            }
        }

        return $errorHTML;
    }

    function get_customer_balance_report()
    {
        $currency = $this->input->post('currency');
        $from = $this->input->post('from');

        $date_format_policy = date_format_policy();
        $fromdt = input_format_date($from, $date_format_policy);

        $customerID = $this->input->post('customerID');
        $companyID = current_companyID();

        //echo '<pre>';print_r($datearr); echo '</pre>'; die();

        $this->form_validation->set_rules('customerID[]', 'Customer', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $qry = "SELECT
  companyLocalCurrency,
  companyReportingCurrency
FROM
    `srp_erp_generalledger`
WHERE
srp_erp_generalledger.partyAutoID IN (" . join(',', $customerID) . ")
    AND srp_erp_generalledger.companyID = $companyID
AND `subLedgerType` = '3'
and documentDate<='$fromdt'
group by partyAutoID,GLAutoID";
            $outputcrr = $this->db->query($qry)->row_array();

            $data["details"] = $this->Report_model->get_customer_balance_report($fromdt);
            $data["type"] = "html";
            $data["loccurr"] = $outputcrr['companyLocalCurrency'];
            $data["repcurr"] = $outputcrr['companyReportingCurrency'];
            $data["loccurrDec"] = fetch_currency_desimal($outputcrr['companyLocalCurrency']);
            $data["repcurrDec"] = fetch_currency_desimal($outputcrr['companyReportingCurrency']);
            $data["currency"] = $currency;
            echo $html = $this->load->view('system/accounts_receivable/report/load-customer-balance-report', $data, true);
        }
    }

    function get_customer_balance_report_pdf()
    {
        $currency = $this->input->post('currency');
        $from = $this->input->post('from');

        $date_format_policy = date_format_policy();
        $fromdt = input_format_date($from, $date_format_policy);

        $customerID = $this->input->post('customerID');
        $companyID = current_companyID();
        $qry = "SELECT
  companyLocalCurrency,
  companyReportingCurrency
FROM
    `srp_erp_generalledger`
WHERE
srp_erp_generalledger.partyAutoID IN (" . join(',', $customerID) . ")
    AND srp_erp_generalledger.companyID = $companyID
AND `subLedgerType` = '3'
and documentDate<='$fromdt'
group by partyAutoID,GLAutoID";
        $outputcrr = $this->db->query($qry)->row_array();


        $data["details"] = $this->Report_model->get_customer_balance_report($fromdt);
        $data["type"] = "pdf";
        $data["loccurr"] = $outputcrr['companyLocalCurrency'];
        $data["repcurr"] = $outputcrr['companyReportingCurrency'];
        $data["currency"] = $currency;
        $html = $this->load->view('system/accounts_receivable/report/load-customer-balance-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }


    function get_vendor_balance_report()
    {
        $currency = $this->input->post('currency');
        $from = $this->input->post('from');

        $date_format_policy = date_format_policy();
        $fromdt = input_format_date($from, $date_format_policy);
        $supplierID = $this->input->post('supplierID');
        $companyID = current_companyID();


        //echo '<pre>';print_r($datearr); echo '</pre>'; die();

        $this->form_validation->set_rules('supplierID[]', 'Supplier', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $qry = "SELECT
  companyLocalCurrency,
  companyReportingCurrency
FROM
    `srp_erp_generalledger`
WHERE
srp_erp_generalledger.partyAutoID IN (" . join(',', $supplierID) . ")
    AND srp_erp_generalledger.companyID = $companyID
AND `subLedgerType` = '2'
and documentDate<='$fromdt'
group by partyAutoID,GLAutoID";
            $outputcrr = $this->db->query($qry)->row_array();

            $data["details"] = $this->Report_model->get_vendor_balance_report($fromdt);
            $data["type"] = "html";
            $data["loccurr"] = $outputcrr['companyLocalCurrency'];
            $data["repcurr"] = $outputcrr['companyReportingCurrency'];

            $data["loccurrDec"] = fetch_currency_desimal($outputcrr['companyLocalCurrency']);
            $data["repcurrDec"] = fetch_currency_desimal($outputcrr['companyReportingCurrency']);

            $data["currency"] = $currency;
            echo $html = $this->load->view('system/accounts_payable/report/load-vendor-balance-report', $data, true);
        }
    }

    function get_vendor_balance_report_pdf()
    {
        $currency = $this->input->post('currency');
        $from = $this->input->post('from');

        $date_format_policy = date_format_policy();
        $fromdt = input_format_date($from, $date_format_policy);

        $supplierID = $this->input->post('supplierID');
        $companyID = current_companyID();

        $qry = "SELECT
  companyLocalCurrency,
  companyReportingCurrency
FROM
    `srp_erp_generalledger`
WHERE
srp_erp_generalledger.partyAutoID IN (" . join(',', $supplierID) . ")
    AND srp_erp_generalledger.companyID = $companyID
AND `subLedgerType` = '2'
and documentDate<='$fromdt'
group by partyAutoID,GLAutoID";
        $outputcrr = $this->db->query($qry)->row_array();


        $data["details"] = $this->Report_model->get_vendor_balance_report($fromdt);
        $data["type"] = "pdf";
        $data["loccurr"] = $outputcrr['companyLocalCurrency'];
        $data["repcurr"] = $outputcrr['companyReportingCurrency'];
        $data["currency"] = $currency;
        $html = $this->load->view('system/accounts_payable/report/load-vendor-balance-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }

}
