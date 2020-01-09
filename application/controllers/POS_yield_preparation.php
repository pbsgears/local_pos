<?php
/**
 *
 * -- =============================================
 * -- File Name : POS_Yield_preparation.php
 * -- Project Name : POS
 * -- Module Name : POS Config
 * -- Author : Mohamed Mubashir
 * -- Create date : 25 October 2017
 * -- Description : Yield preparation
 *
 * --REVISION HISTORY
 * --Date:
 * -- =============================================
 **/
defined('BASEPATH') OR exit('No direct script access allowed');

class POS_yield_preparation extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('pos_yield_preparation_model');
        $this->load->model('double_entry_model');
        $this->load->helper('pos');
    }

    function fetch_yield_preparation()
    {
        $convertFormat = convert_date_format_sql();
        $this->datatables->select('srp_erp_pos_menuyieldpreparation.*,yieldPreparationID,confirmedYN,CONCAT(itemSystemCode," - ",itemDescription) as item, DATE_FORMAT(srp_erp_pos_menuyieldpreparation.documentDate,"'.$convertFormat.'") as documentDate,srp_erp_unit_of_measure.UnitDes as uom,srp_erp_warehousemaster.wareHouseDescription as wareHouseDescription,srp_erp_warehousemaster.wareHouseLocation as wareHouseLocation,srp_erp_pos_menuyieldpreparation.documentSystemCode as documentSystemCode',false)
            ->from('srp_erp_pos_menuyieldpreparation')
            ->join('srp_erp_itemmaster','srp_erp_itemmaster.itemAutoID=srp_erp_pos_menuyieldpreparation.itemAutoID','left')
            ->join('srp_erp_unit_of_measure', 'srp_erp_pos_menuyieldpreparation.uomID = srp_erp_unit_of_measure.UnitID', 'left')
            ->join('srp_erp_warehousemaster', 'srp_erp_warehousemaster.wareHouseAutoID = srp_erp_pos_menuyieldpreparation.warehouseAutoID', 'left')
            ->where('srp_erp_pos_menuyieldpreparation.companyID', current_companyID())
            ->edit_column('action', '$1', 'load_edit_yield_preparation(yieldPreparationID,confirmedYN)')
            ->add_column('status', '$1', 'load_yield_preparation_status(confirmedYN)');
        echo $this->datatables->generate();
    }

    function load_yield_detail()
    {
        echo json_encode($this->pos_yield_preparation_model->load_yield_detail());
    }

    function load_yield_preparation_detail()
    {
        echo json_encode($this->pos_yield_preparation_model->load_yield_preparation_detail());
    }

    function save_yieldPreparation()
    {
        $this->form_validation->set_rules('yieldMasterID', 'Yield', 'trim|required');
        $this->form_validation->set_rules('documentDate', 'Document Date', 'trim|required');
        $this->form_validation->set_rules('qty', 'Qty', 'trim|required');
        $this->form_validation->set_rules('currencyID', 'Currency', 'trim|required');
        $this->form_validation->set_rules('narration', 'Narration', 'trim|required');
        $this->form_validation->set_rules('uomID', 'UOM', 'trim|required');
        $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
        $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        $this->form_validation->set_rules('warehouseAutoID', 'Warehouse', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $date_format_policy = date_format_policy();
            $documentDate = $this->input->post('documentDate');
            $documentDate = input_format_date($documentDate, $date_format_policy);
            $financearray = $this->input->post('financeyear_period');
            $financePeriod = fetchFinancePeriod($financearray);
            if ($documentDate >= $financePeriod['dateFrom'] && $documentDate <= $financePeriod['dateTo']) {
                $yieldPreparation = $this->pos_yield_preparation_model->load_yieldPreparation();
                if($yieldPreparation["confirmedYN"] == 1){
                    echo json_encode(array('w','Document is already confirmed'));
                }else{
                    echo json_encode($this->pos_yield_preparation_model->save_yieldPreparation());
                }
            } else {
                echo json_encode(array('w','Yield preparation date is not between financial period!'));
            }
        }
    }

    function load_yieldPreparation()
    {
        echo json_encode($this->pos_yield_preparation_model->load_yieldPreparation());
    }

    function pos_yield_preparation_model()
    {
        echo json_encode($this->pos_yield_preparation_model->delete_yield_preparation());
    }

    function delete_yield_preparation(){
        echo json_encode($this->pos_yield_preparation_model->delete_yield_preparation());
    }
    function yield_preparation_print()
    {
        $yieldpreparationid = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('yieldPreparationID'));
        $data['extra'] = $this->pos_yield_preparation_model->yield_preparation_print($yieldpreparationid);
        $data['approval'] = $this->input->post('approval');
        $html = $this->load->view('system/pos/reports/yield_preparation_print', $data, TRUE);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 1);
        }
    }


}