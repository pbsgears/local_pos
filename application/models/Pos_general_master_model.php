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

class Pos_general_master_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function save_outlet()
    {
        $id = $this->input->post('wareHouseAutoID');

        if (!$id) {
            /*Add */

            $this->db->set('wareHouseCode', (($this->input->post('warehousecode') != "")) ? $this->input->post('warehousecode') : NULL);
            $this->db->set('wareHouseDescription', (($this->input->post('warehousedescription') != "")) ? $this->input->post('warehousedescription') : NULL);
            $this->db->set('wareHouseLocation', (($this->input->post('warehouselocation') != "")) ? $this->input->post('warehouselocation') : NULL);
            $this->db->set('warehouseAddress', (($this->input->post('warehouseAddress') != "")) ? $this->input->post('warehouseAddress') : NULL);
            $this->db->set('pos_footNote', (($this->input->post('pos_footNote') != "")) ? $this->input->post('pos_footNote') : NULL);
            $this->db->set('warehouseTel', (($this->input->post('warehouseTel') != "")) ? $this->input->post('warehouseTel') : NULL);
            $this->db->set('createdUserGroup', ($this->common_data['user_group']));
            $this->db->set('createdPCID', ($this->common_data['current_pc']));
            $this->db->set('createdUserID', ($this->common_data['current_userID']));
            $this->db->set('createdDateTime', ($this->common_data['current_date']));
            $this->db->set('createdUserName', ($this->common_data['current_user']));
            $this->db->set('companyID', ($this->common_data['company_data']['company_id']));
            $this->db->set('companyCode', ($this->common_data['company_data']['company_code']));
            $this->db->set('isPosLocation', 1);
            $this->db->set('isActive', 0);
            $result = $this->db->insert('srp_erp_warehousemaster');

            if ($result) {
                $warehouseAutoID = $this->db->insert_id();
                $this->add_segment_config($warehouseAutoID);

                /*$this->session->set_flashdata('s', 'Warehouse Added Successfully');
                return true;*/
                $error = array('error' => 0, 'message' => 'Warehouse Added Successfully');
                return $error;
            }
        } else {

            $id = $this->input->post('warehouseredit');
            $code = $this->input->post('warehousecode');
            $Q = 'SELECT * FROM srp_erp_warehousemaster WHERE wareHouseCode= "' . $code . '" AND wareHouseAutoID !=' . $id;
            $output = $this->db->query($Q)->row_array();
            if (!empty($output)) {
                $error = array('error' => 1, 'message' => 'Warehouse code <strong>' . $code . '</strong> already used!, Please try different warehouse code');
                return $error;
            }

            $data['wareHouseCode'] = ((($this->input->post('warehousecode') != "")) ? $this->input->post('warehousecode') : NULL);
            $data['wareHouseDescription'] = ((($this->input->post('warehousedescription') != "")) ? $this->input->post('warehousedescription') : NULL);
            $data['wareHouseLocation'] = ((($this->input->post('warehouselocation') != "")) ? $this->input->post('warehouselocation') : NULL);
            $data['warehouseAddress'] = ((($this->input->post('warehouseAddress') != "")) ? $this->input->post('warehouseAddress') : NULL);
            $data['pos_footNote'] = ((($this->input->post('pos_footNote') != "")) ? $this->input->post('pos_footNote') : NULL);
            $data['warehouseTel'] = ((($this->input->post('warehouseTel') != "")) ? $this->input->post('warehouseTel') : NULL);
            $data['modifiedPCID'] = ($this->common_data['current_pc']);
            $data['modifiedUserID'] = ($this->common_data['current_userID']);
            $data['modifiedDateTime'] = ($this->common_data['current_date']);
            $data['modifiedUserName'] = ($this->common_data['current_user']);

            $this->db->where('wareHouseAutoID', $this->input->post('warehouseredit'));
            $result = $this->db->update('srp_erp_warehousemaster', $data);

            $printTemplateMasterID = $this->input->post('printTemplateMasterID');
            if ($printTemplateMasterID) {
                $companyID = current_companyID();
                $this->db->select('*');
                $this->db->from('srp_erp_pos_segmentconfig');
                $this->db->where('wareHouseAutoID', $id);
                $this->db->where('companyID', $companyID);
                $this->db->where('isGeneralPOS', 1);
                $segmentConfig = $this->db->get()->row_array();
                if (!empty($segmentConfig)) {
                    $sc_data['generalPrintTemplateID'] = $printTemplateMasterID;
                    $this->db->where('segmentConfigID', $segmentConfig['segmentConfigID']);
                    $this->db->update('srp_erp_pos_segmentconfig', $sc_data);
                }

            }


            if ($result) {
                /*$this->session->set_flashdata('s', 'Records Updated Successfully');
                return true;*/
                $this->session->set_flashdata('s', 'Records Updated Successfully');
                return array('error' => 0, 'message' => 'Records Updated Successfully');
            }
        }
    }

    protected function add_segment_config($id)
    {
        $segmentID = $this->input->post('segmentID');

        $this->db->select("segmentCode");
        $this->db->from('srp_erp_segment');
        $this->db->where('segmentID', $segmentID);
        $segmentCode = $this->db->get()->row('segmentCode');

        $data['wareHouseAutoID'] = $id;
        $data['companyID'] = current_companyID();
        $data['companyCode'] = current_companyCode();
        $data['segmentID'] = $segmentID;
        $data['segmentCode'] = $segmentCode;
        $data['isGeneralPOS'] = 1;
        $data['createdPCID'] = current_pc();
        $data['createdUserID'] = current_userID();
        $data['createdDateTime'] = format_date_mysql_datetime();
        $data['createdUserName'] = current_user();
        $data['timeStamp'] = format_date_mysql_datetime();
        $segmentConfig = $this->db->insert('srp_erp_pos_segmentconfig', $data);
        return $segmentConfig;
    }

    function get_warehouse()
    {
        $this->db->select('*');
        $this->db->where('wareHouseAutoID', $this->input->post('id'));
        $result1 = $this->db->get('srp_erp_warehousemaster')->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_pos_outlettemplatedetail');
        $this->db->where('companyID', $result1['companyID']);
        $this->db->where('outletID', $result1['wareHouseAutoID']);
        $tmpResult = $this->db->get()->row_array();

        $this->db->select('segmentID,generalPrintTemplateID');
        $this->db->from('srp_erp_pos_segmentconfig');
        $this->db->where('isGeneralPOS', 1);
        $this->db->where('wareHouseAutoID', $this->input->post('id'));
        $segmentOutput = $this->db->get()->row_array();
        if (isset($segmentOutput['generalPrintTemplateID']) && !empty($segmentOutput['generalPrintTemplateID'])) {
            $segmentOutput['generalPrintTemplateID'];
        } else {
            $segmentOutput['generalPrintTemplateID'] = get_general_pos_print_defaultID();
        }


        $tmpArray['outletTemplateMasterID'] = !empty($tmpResult['outletTemplateMasterID']) ? $tmpResult['outletTemplateMasterID'] : 1;

        return array_merge($result1, $tmpArray, $segmentOutput);
    }

}