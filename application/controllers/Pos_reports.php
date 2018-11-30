<?php
/**
 *
 * -- =============================================
 * -- File Name : giftCard.php
 * -- Project Name : POS
 * -- Module Name : Reports
 * -- Author : Mohamed Shafri
 * -- Create date : 19 October 2017
 * -- Description : Gift Card masters and Gift Card Process .
 *
 * --REVISION HISTORY
 * --Date: 02-Nov 2017 By: Mohamed Shafri: file created
 * -- =============================================
 **/
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos_reports extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Pos_report_model');
        $this->load->helper('pos');
        $this->load->helper('pos_report');
    }

    public function fetch_till_management_report()
    {
        $f_outletID = $this->input->post('f_outletID');
        $startTime = trim(str_replace('/', '-', $this->input->post('startdate')));

        if (isset($startTime) && !empty($startTime)) {
            $filterDate = date('Y-m-d H:i:s', strtotime($startTime));
        }

        $endTime = trim(str_replace('/', '-', $this->input->post('enddate')));

        if (isset($endTime) && !empty($endTime)) {
            $filterEndDate = date('Y-m-d H:i:s', strtotime($endTime));
        }
        //echo $f_cardMasterID;
        $this->datatables->select('shift.shiftID as shiftID, startTime, endTime, shift.closingCashBalance_transaction as closingCashBalance_transaction,  shift.different_transaction as different_transaction, shift.cashSales as cashSales, shift.startingBalance_transaction as startingBalance_transaction, shift.endingBalance_transaction as endingBalance_transaction,wm.wareHouseDescription, wm.wareHouseLocation, c.counterCode, c.counterName, e.Ename2 as empName, shift.giftCardTopUp as  giftCardTopUp', false)
            ->from('srp_erp_pos_shiftdetails shift')
            ->join('srp_erp_warehousemaster wm', 'wm.wareHouseAutoID = shift.wareHouseID')
            ->join('srp_erp_pos_counters c', 'c.counterID = shift.counterID')
            ->join('srp_employeesdetails e', 'e.EIdNo = shift.empID')
            ->add_column('startingBal', '$1', 'till_report_numberFormat(startingBalance_transaction)')
            ->add_column('EndingBal', '$1', 'till_report_numberFormat(endingBalance_transaction)')
            ->add_column('cashSalesCol', '$1', 'till_report_numberFormat(cashSales)')
            ->add_column('different_transaction', '$1', 'till_report_numberFormat_dif(different_transaction)')
            ->add_column('closingCashBalance', '$1', 'till_report_numberFormat(closingCashBalance_transaction)')
            ->add_column('tmp_giftCardTopUp', '$1', 'till_report_numberFormat(giftCardTopUp)');
        //->add_column('wareHouseColumn', '$1  -  $2', 'wareHouseCode,wareHouseLocation')
        $this->datatables->where('shift.companyID', current_companyID());
        $this->datatables->where('shift.isClosed', 1);
        if (!empty($f_outletID)) {
            $this->datatables->where('shift.wareHouseID', $f_outletID);
        }
        if (!empty($filterDate)) {
            $this->datatables->where('shift.startTime>=', $filterDate);
        }
        if (!empty($filterEndDate)) {
            $this->datatables->where('shift.startTime<=', $filterEndDate);
        }

        $r = $this->datatables->generate();
        $this->db->last_query();

        echo $r;


    }

    function load_till_management_report()
    {
        $f_outletID = $this->input->post('outletID_f');
        $startTime = trim(str_replace('/', '-', $this->input->post('startdate')));

        if (isset($startTime) && !empty($startTime)) {
            $filterDate = ' AND shift.startTime >="'.date('Y-m-d H:i:s', strtotime($startTime)).'"';
        } else {
            $filterDate = '';
        }

        $endTime = trim(str_replace('/', '-', $this->input->post('enddate')));

        if (isset($endTime) && !empty($endTime)) {
            $filterEndDate = ' AND shift.startTime <="'.date('Y-m-d H:i:s', strtotime($endTime)).'"';
        } else {
            $filterEndDate = '';
        }
        if (!empty($f_outletID)) {
            $f_outlet='AND shift.wareHouseID ='.$f_outletID.'' ;
        }else{
            $f_outlet='';
        }

        $data['extra']=$this->db->query('SELECT
	shift.shiftID AS shiftID,
	startTime,
	endTime,
	shift.closingCashBalance_transaction AS closingCashBalance_transaction,
	shift.different_transaction AS different_transaction,
	shift.cashSales AS cashSales,
	shift.startingBalance_transaction AS startingBalance_transaction,
	shift.endingBalance_transaction AS endingBalance_transaction,
	wm.wareHouseDescription,
	wm.wareHouseLocation,
	c.counterCode,
	c.counterName,
	e.Ename2 AS empName
FROM
	`srp_erp_pos_shiftdetails` `shift`
JOIN `srp_erp_warehousemaster` `wm` ON `wm`.`wareHouseAutoID` = `shift`.`wareHouseID`
JOIN `srp_erp_pos_counters` `c` ON `c`.`counterID` = `shift`.`counterID`
JOIN `srp_employeesdetails` `e` ON `e`.`EIdNo` = `shift`.`empID`
WHERE
	`shift`.`companyID` = '.current_companyID().'
AND `shift`.`isClosed` = 1
'.$f_outlet.'
'.$filterDate.'
'.$filterEndDate.' ')->result_array();

        $html = $this->load->view('system/pos/reports/till_management_report_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 1);
        }
    }


}