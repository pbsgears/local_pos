<?php
/**
 *
 * -- =============================================
 * -- File Name : Pos_config.php
 * -- Project Name : POS
 * -- Module Name : POS Config
 * -- Author : Mohamed Shafri
 * -- Create date : 13 October 2016
 * -- Description : All Configuration related function are included.
 *
 * --REVISION HISTORY
 * --Date: 13-Oct 2016 By: Mohamed Shafri: file created
 * --Date: 23-NOV 2016 By: Nasik Ahamed: Created the creditNoteGL_config function
 * -- =============================================
 **/
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos_cameraSetup extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Pos_cameraSetup_model');
        $this->load->helper('pos');
    }

    function LoadCameraSetup()
    {
        $this->datatables->select('id, url_host, port, outletID, wareHouseDescription, srp_erp_pos_camera_setup.createdUserName as createdUserName, srp_erp_pos_camera_setup.createdDateTime as createdDateTime', false)
            ->from('srp_erp_pos_camera_setup')
            ->join('srp_erp_warehousemaster', 'srp_erp_warehousemaster.wareHouseAutoID = srp_erp_pos_camera_setup.outletID','inner')
            ->where('srp_erp_pos_camera_setup.companyID', current_companyID());

        $outletID = $this->input->post('outletID');
        if (!empty($outletID) && $outletID > 0) {
            $this->datatables->where('outletID', $outletID);
        }

        $this->datatables->add_column('DT_RowId', 'packItemTbl_$1', 'id')
            ->edit_column('edit', '$1', 'col_pos_cameraSetup(id)');
        echo $this->datatables->generate();
        //$this->db->last_query();
    }

    function save_camera_setup()
    {
        $this->form_validation->set_rules('url_host', 'URL', 'trim|required');
        $this->form_validation->set_rules('outletID', 'Outlet', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 'e', 'message' => validation_errors()));
        } else {
            echo json_encode($this->Pos_cameraSetup_model->save_camera_setup());
        }
    }

    function delete_camera_setup()
    {
        $id = $this->input->post('id');
        if (!empty($id)) {
            echo json_encode($this->Pos_cameraSetup_model->delete_camera_setup($id));
        } else {
            echo json_encode(array('status' => 'e', 'message' => 'An error has occurred when perform this operation. Message: id not found!'));
        }
    }

    function edit_camera_setup()
    {
        $id = $this->input->post('id');
        if (!empty($id)) {
            $result = $this->Pos_cameraSetup_model->get_camera_setup_by_id($id);
            if (!empty($result)) {
                echo json_encode(array_merge($result, array('status' => 's', 'message' => 'loaded')));
            } else {
                echo json_encode(array_merge($result, array('status' => 'e', 'message' => 'Error loading data')));
            }
        } else {
            echo json_encode(array('status' => 'e', 'message' => 'An error has occurred when perform this operation. Message: id not found!'));
        }
    }

    function get_cctv_feed()
    {
        echo json_encode($this->Pos_cameraSetup_model->get_cctv_feed());
    }


}