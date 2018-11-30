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

class Pos_cameraSetup_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function save_camera_setup()
    {
        $id = $this->input->post('id');
        $post_port = $this->input->post('port');
        $port = $post_port > 0 ? $post_port : 80;
        if ($id) {
            /** update*/
            $data['outletID'] = $this->input->post('outletID');
            $data['url_host'] = $this->input->post('url_host');
            $data['port'] = $port;
            $data['modifiedPCID'] = current_pc();
            $data['modifiedUserID'] = current_userID();
            $data['modifiedDateTime'] = format_date_mysql_datetime();
            $data['modifiedUserName'] = current_user();


            $this->db->where('id', $id);
            $result = $this->db->update('srp_erp_pos_camera_setup', $data);
            if ($result) {
                return array('status' => 's', 'message' => 'successfully updated');
            } else {
                return array('status' => 'e', 'message' => 'Error while updating, Please contact your system support team');
            }
        } else {
            $data['outletID'] = $this->input->post('outletID');
            $data['url_host'] = $this->input->post('url_host');
            $data['port'] = $port;
            $data['companyID'] = current_companyID();
            $data['companyCode'] = current_company_code();
            $data['createdPCID'] = current_pc();
            $data['createdUserID'] = current_userID();
            $data['createdDateTime'] = format_date_mysql_datetime();
            $data['createdUserName'] = current_user();

            $result = $this->db->insert('srp_erp_pos_camera_setup', $data);
            if ($result) {
                return array('status' => 's', 'message' => 'successfully saved');
            } else {
                return array('status' => 'e', 'message' => 'Error while insert, Please contact your system support team');
            }
        }
    }

    function delete_camera_setup($id)
    {
        $this->db->where('id', $id);
        $result = $this->db->delete('srp_erp_pos_camera_setup');
        if ($result) {
            return array('status' => 's', 'message' => 'Record deleted successfully');
        } else {
            return array('status' => 'e', 'message' => 'Error while deleting, Please contact your system support team');
        }
    }

    function get_camera_setup_by_id($id)
    {
        $this->db->select("*");
        $this->db->from("srp_erp_pos_camera_setup");
        $this->db->where("id", $id);
        return $this->db->get()->row_array();
    }

    function get_cctv_feed()
    {
        $this->db->select('id, url_host, port');
        $this->db->from('srp_erp_pos_camera_setup');
        $this->db->where('outletID', get_outletID());
        $this->db->where('companyID', current_companyID());
        $result = $this->db->get()->result_array();
        return $result;
    }

}