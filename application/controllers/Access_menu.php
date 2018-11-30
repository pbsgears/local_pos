<?php

class Access_menu extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_model');
        $this->load->model('Access_menu_model');
        $this->load->helpers('procurement');
        $this->load->helpers('grv');
        $this->load->helpers('loan_helper');
        $this->load->helper('template_paySheet');
        $this->load->helper('employee');
        $this->load->helper('pos');
        $this->load->helper('cookie');
        $this->load->helper('asset_management');

    }

    function saveNavigationgroupSetup()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $userGroupID = $this->input->post('userGroupID');

        /*  $data = array();*/
        $navigationID = $this->input->post('navigationID');;
        /*setup array*/
        /*     if (trim($navigationID) == '' || !empty(trim($navigationID))) {
                 $x = 0;
                 $navigation = explode(',', $navigationID);
                 foreach ($navigation as $navData) {
                     $x++;
                     $data[$x]['navigationMenuID'] = $navData;
                     $data[$x]['userGroupID'] = $userGroupID;
                 }
             }*/
        $this->db->trans_start();
        /*delete*/
        $this->db->delete('srp_erp_navigationusergroupsetup', array('userGroupID' => $userGroupID));
        if (!empty($navigationID) && $navigationID != "") {

            $this->db->query("INSERT srp_erp_navigationusergroupsetup (companyID,userGroupID ,navigationMenuID,description,masterID,url,pageID,pageTitle,pageIcon,levelNo,sortOrder,isSubExist) SELECT $companyID,$userGroupID,navigationMenuID,description,masterID,url,pageID,pageTitle,pageIcon,levelNo,sortOrder,isSubExist FROM srp_erp_navigationmenus WHERE navigationMenuID IN ({$navigationID})");

        }


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('e', 'Failed. please try again');
            echo json_encode(true);
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'Access rights for this group has been updated successfully');
            echo json_encode(true);
        }

    }

    /*load navigation usergroup setup */

    function load_navigation_usergroup_setup()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $userGroupID = $this->input->post('userGroupID');
        $data['data'] = false;
        if (!empty($userGroupID)) {
            $navigationMenuID = $this->db->query("SELECT navigationMenuID FROM `srp_erp_moduleassign` WHERE `companyID` = {$companyID}")->result_array();
            /*    $data['data'] = $this->db->query("SELECT srp_erp_navigationmenus.*, IFNULL(srp_erp_navigationusergroupsetup.navigationMenuID, 0) AS navID FROM srp_erp_navigationmenus LEFT JOIN srp_erp_navigationusergroupsetup ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID AND userGroupID={$userGroupID} ORDER BY levelNo , sortOrder")->result_array(); */
            if (!empty($navigationMenuID)) {
                $data['data'] = $this->db->query("SELECT srp_erp_navigationmenus.*, IFNULL(srp_erp_navigationusergroupsetup.navigationMenuID, 0) AS navID FROM srp_erp_navigationmenus LEFT JOIN srp_erp_navigationusergroupsetup ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID AND userGroupID = {$userGroupID} WHERE srp_erp_navigationmenus.navigationMenuID NOT IN (SELECT srp_erp_navigationmenus.navigationMenuID FROM srp_erp_navigationmenus LEFT JOIN `srp_erp_moduleassign` ON srp_erp_navigationmenus.navigationMenuID = srp_erp_moduleassign.navigationMenuID AND companyID = '{$companyID}' WHERE masterID IS NULL AND moduleID IS NULL) ORDER BY levelNo , sortOrder")->result_array();
            }
        }

        $html = $this->load->view('system/navigation/ajax-erp_navigation_group_setup', $data, true);
        echo $html;
    }

    function fetch_group_access_employee()
    {
        $userGroup = $this->input->post('userGroup');
        $companyID = current_companyID();

        $filteruserGroup = $this->input->post('userGroup');
        $filtercompanyID = $this->input->post('companyID');


        /* $this->datatables->select("srp_employeesdetails.ECode as empID,employeeNavigationID,srp_erp_usergroups.description, concat(IFNULL(Ename1,''),' ',IFNULL(Ename2,''),' ',IFNULL(Ename3,''),' ',IFNULL(Ename4,'')) as emloyeeName,srp_erp_employeenavigation.userGroupID",false);
         $this->datatables->from('srp_erp_employeenavigation');
         $this->datatables->join('srp_employeesdetails', 'empID=EIdNo', 'LEFT');
         $this->datatables->join('srp_erp_usergroups', 'srp_erp_employeenavigation.userGroupID=srp_erp_usergroups.userGroupID', 'LEFT');
         $this->datatables->where('srp_erp_usergroups.companyID', current_companyID());
         $this->datatables->where('srp_employeesdetails.Erp_companyID', current_companyID());
         $this->datatables->where('srp_erp_employeenavigation.userGroupID', $userGroup);
         $this->datatables->add_column('edit', ' $1 ', 'edit_employee_nav_access(employeeNavigationID)');
         echo $this->datatables->generate();*/

        $companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();

        if (!empty($companyGroup)) {

            $this->datatables->select("srp_employeesdetails.ECode AS empID, employeeNavigationID, srp_erp_usergroups.description as description,  Ename2 as emloyeeName, srp_erp_employeenavigation.userGroupID, CONCAT(IFNULL(company_code, ''), ' - ', IFNULL( company_name, '')) AS company, company_id", false);
            $this->datatables->from('srp_erp_companygroupdetails');
            $this->datatables->join('srp_erp_employeenavigation', 'srp_erp_companygroupdetails.companyID = srp_erp_employeenavigation.companyID', 'INNER');
            $this->datatables->join('srp_employeesdetails', 'empID = EIdNo', 'INNER');
            $this->datatables->join('srp_erp_usergroups', 'srp_erp_employeenavigation.userGroupID = srp_erp_usergroups.userGroupID', 'INNER');
            $this->datatables->join('srp_erp_company', 'company_id = srp_erp_employeenavigation.companyID', 'INNER');
            $this->datatables->where('companyGroupID', $companyGroup['companyGroupID']);
            if (isset($filtercompanyID) && $filtercompanyID != '') {
                $this->datatables->where('srp_erp_employeenavigation.companyID', $filtercompanyID);
            }
            if (isset($filteruserGroup) && $filteruserGroup != '') {
                $this->datatables->where('srp_erp_employeenavigation.userGroupID ', $filteruserGroup);
            }
            $this->datatables->add_column('edit', ' $1 ', 'edit_employee_nav_access(employeeNavigationID)');
            echo $this->datatables->generate();
        } else {

            $this->datatables->select("srp_employeesdetails.ECode AS empID, employeeNavigationID, srp_erp_usergroups.description as description, Ename2 as emloyeeName, srp_erp_employeenavigation.userGroupID, CONCAT(IFNULL(company_code, ''), ' - ', IFNULL( company_name, '')) AS company, company_id", false);
            $this->datatables->from('srp_erp_employeenavigation');
            $this->datatables->join('srp_employeesdetails', 'empID = EIdNo', 'INNER');
            $this->datatables->join('srp_erp_usergroups', 'srp_erp_employeenavigation.userGroupID = srp_erp_usergroups.userGroupID', 'INNER');
            $this->datatables->join('srp_erp_company', 'company_id = srp_erp_employeenavigation.companyID', 'INNER');
            $this->datatables->where('srp_erp_employeenavigation.companyID', $companyID);

            if (isset($filteruserGroup) && $filteruserGroup != '') {
                $this->datatables->where('srp_erp_employeenavigation.userGroupID ', $filteruserGroup);
            }
            $this->datatables->add_column('edit', ' $1 ', 'edit_employee_nav_access(employeeNavigationID)');
            echo $this->datatables->generate();

        }

    }

    function load_dropdown_unassigned_employees()
    {
        $data['emp'] = true;
        $data['companyID'] = $this->input->post('companyID');
        $html = $this->load->view('system/navigation/ajax-erp_navigation_load_employees', $data, true);
        echo $html;
    }

    function load_userGroupdropDown()
    {
        $data['group'] = true;
        $data['companyID'] = $this->input->post('companyID');
        $html = $this->load->view('system/navigation/ajax-erp_navigation_load_employees', $data, true);
        echo $html;
    }

    function loaduserGroupdropdown()
    {
        $data['groupID'] = true;
        $data['companyID'] = $this->input->post('companyID');
        $html = $this->load->view('system/navigation/ajax-erp_navigation_load_employees', $data, true);
        echo $html;
    }

    function save_assigned_navigation_employees()
    {
        $this->form_validation->set_rules('empID[]', 'Employee', 'trim|required');
        $this->form_validation->set_rules('userGroup', 'User group', 'trim|required');
        $this->form_validation->set_rules('companyID', 'companyID', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {

            $employee = $this->input->post('empID');

            $userGroup = $this->input->post('userGroup');
            $companyID = $this->input->post('companyID');
            $x = 0;
            if ($employee) {
                foreach ($employee as $empID) {

                    $data[$x]['empID'] = $empID;
                    $data[$x]['userGroupID'] = $userGroup;
                    $data[$x]['companyID'] = $companyID;

                    $x++;
                    $detail = $this->db->query("SELECT approvalUserID FROM srp_erp_approvalusers WHERE employeeID={$empID} AND companyID={$companyID} ")->row_array();
                    if (!empty($detail)) {
                        $this->db->update('srp_erp_approvalusers', array('groupID' => $userGroup), array('approvalUserID' => $detail['approvalUserID']));
                    }

                }
            }

            $insert = $this->db->insert_batch('srp_erp_employeenavigation', $data);
            if ($insert) {
                $this->session->set_flashdata('s', 'Records Inserted Successfully.');
                echo json_encode(array('status' => true));
            } else {
                $this->session->set_flashdata('e', 'Failed. Please contact support team');
                echo json_encode(array('status' => false));

            }


        }
    }

    function delete_employee_navigation_access()
    {
        $this->db->where('employeeNavigationID', $this->input->post('employeeNavigationID'));
        $this->db->delete('srp_erp_employeenavigation');
        $this->session->set_flashdata('s', 'Employee navigation : deleted Successfully.');
        echo json_encode(true);
    }

    function load_navigation($companyIDTmp = null)
    {
        if (isset($companyIDTmp) && $companyIDTmp != null) {
            $companyID = $companyIDTmp;
        } else {
            $companyID = $this->input->post('companyID');
        }

        $empID = current_userID();
        //$companyCode = $this->input->post('companyCode');

        $wareHouseID = $this->db->select('wareHouseID,')->from('srp_erp_warehouse_users')->where(array('userID' => $empID, 'companyID' => $companyID))->get()->row('wareHouseID');
        $imagePath_arr = $this->db->select('imagePath,isLocalPath')->from('srp_erp_pay_imagepath')->get()->row_array();
        if ($imagePath_arr['isLocalPath'] == 1) {
            $imagePath = base_url() . 'images/users/';
        } else { // FOR SRP ERP USERS
            $imagePath = $imagePath_arr['imagePath'];
        }
        if ($this->input->post('companyType') == 1) {
            $company = $this->db->query("select * from srp_erp_company WHERE company_id={$companyID}")->row_array();
            $this->session->set_userdata("ware_houseID", trim($wareHouseID));
            $this->session->set_userdata("company_code", trim($company['company_code']));
            $this->session->set_userdata("company_name", trim($company['company_name']));
            $this->session->set_userdata("company_logo", trim($company['company_logo']));
        } else {
            $company = $this->db->query("select * from srp_erp_companygroupmaster LEFT JOIN srp_erp_groupfinanceyear ON groupID = companyGroupID  WHERE companyGroupID={$companyID}")->row_array();
            $group = $this->db->query("SELECT * FROM srp_erp_groupfinanceyear WHERE isActive = 1 AND isCurrent = 1 AND groupID={$companyID}")->row_array();
            $this->session->set_userdata("company_name", trim($company['description']));
            $this->session->set_userdata("FYBeginingDate", trim($company['beginingDate']));
            $this->session->set_userdata("FYEndingDate", trim($company['endingDate']));
        }
        $this->session->set_userdata("imagePath", trim($imagePath));
        $this->session->set_userdata("companyID", trim($companyID));
        $this->session->set_userdata("companyType", trim($this->input->post('companyType')));

        $detail = "";
        if ($this->input->post('companyType') == 1) {
            $detail = $this->db->query("SELECT srp_erp_navigationusergroupsetup.* FROM srp_erp_employeenavigation INNER JOIN srp_erp_navigationusergroupsetup ON srp_erp_employeenavigation.userGroupID = srp_erp_navigationusergroupsetup.userGroupID WHERE empID={$empID} AND srp_erp_employeenavigation.companyID={$companyID} Order by levelNo,sortOrder ASC ")->result_array();
        }
        else {
             $sql = "SELECT  srp_erp_companysubgroupnavigationsetup.* FROM srp_erp_companysubgroupnavigationsetup LEFT JOIN srp_erp_navigationmenus ON srp_erp_navigationmenus.navigationMenuID = srp_erp_companysubgroupnavigationsetup.navigationMenuID
LEFT JOIN srp_erp_companysubgroupmaster ON srp_erp_companysubgroupnavigationsetup.compaySubGroupID = srp_erp_companysubgroupmaster.companySubGroupID
LEFT JOIN srp_erp_companysubgroupemployees ON srp_erp_companysubgroupemployees.companySubGroupID = srp_erp_companysubgroupmaster.companySubGroupID  WHERE srp_erp_companysubgroupemployees.EmpID={$empID} AND companyGroupID={$companyID} AND isGroup = 1 Order by levelNo,sortOrder ASC ";

            $detail = $this->db->query($sql)->result_array();
        }
        //print_r($detail);
        $data['data'] = $detail;
        $data['companyID'] = $companyID;
        $data['companyType'] = $this->input->post('companyType');
        $status = false;
        $html = $this->load->view('system/navigation/ajax-srp_erp_navigation.php', $data, true);

        if (empty($detail)) {
            $status = true;
        } else {
            $keys = array_keys(array_column($detail, 'navigationMenuID'), 29);
            $new_array = array_map(function ($k) use ($detail) {
                return $detail[$k];
            }, $keys);

            if (!$new_array) {
                $status = true;
            }
            /*   echo  $revenue = array_search('navigationMenuID', array_column($detail, 29));
                 if(!$revenue){
                     $status = true;
                 }*/
        }
        // echo $html;

        echo json_encode(array('html' => $html, 'status' => $status));


    }

    function load_navigation_html($companyIDTmp = null)
    {
        if (isset($companyIDTmp) && $companyIDTmp != null) {
            $companyID = $companyIDTmp;
        } else {
            $companyID = $this->input->post('companyID');
        }


        $empID = current_userID();
        $companyCode = $this->input->post('companyCode');

        $wareHouseID = $this->db->select('wareHouseID,')->from('srp_erp_warehouse_users')->where(array('userID' => $empID, 'companyID' => $companyID))->get()->row('wareHouseID');
        $imagePath_arr = $this->db->select('imagePath,isLocalPath')->from('srp_erp_pay_imagepath')->get()->row_array();
        if ($imagePath_arr['isLocalPath'] == 1) {
            $imagePath = base_url() . 'images/users/';
        } else { // FOR SRP ERP USERS
            $imagePath = $imagePath_arr['imagePath'];
        }
        $company = $this->db->query("select * from srp_erp_company WHERE company_id={$companyID}")->row_array();

        $this->session->set_userdata("companyID", trim($companyID));
        $this->session->set_userdata("ware_houseID", trim($wareHouseID));
        $this->session->set_userdata("imagePath", trim($imagePath));
        $this->session->set_userdata("company_code", trim($company['company_code']));
        $this->session->set_userdata("company_name", trim($company['company_name']));
        $this->session->set_userdata("company_logo", trim($company['company_logo']));


        $detail = $this->db->query("SELECT srp_erp_navigationusergroupsetup.* FROM srp_erp_employeenavigation INNER JOIN srp_erp_navigationusergroupsetup ON srp_erp_employeenavigation.userGroupID = srp_erp_navigationusergroupsetup.userGroupID WHERE empID={$empID} AND srp_erp_employeenavigation.companyID={$companyID} Order by levelNo,sortOrder ASC ")->result_array();
        //print_r($detail);
        $data['data'] = $detail;
        $data['companyID'] = $companyID;
        $status = false;
        $html = $this->load->view('system/navigation/ajax-srp_erp_navigation.php', $data, true);
        if (empty($detail)) {
            $status = true;
        } else {
            $keys = array_keys(array_column($detail, 'navigationMenuID'), 29);
            $new_array = array_map(function ($k) use ($detail) {
                return $detail[$k];
            }, $keys);

            if (!$new_array) {
                $status = true;
            }
            /*   echo  $revenue = array_search('navigationMenuID', array_column($detail, 29));
                 if(!$revenue){
                     $status = true;
                 }*/
        }
        // echo $html;
        echo $html;


    }

    function load_companyusergroup()
    {
        $companyID = current_companyID();
        $this->datatables->select("userGroupID,
companyID,
description,
isActive
", false);
        $this->datatables->from('srp_erp_usergroups');
        $this->datatables->where('companyID', $companyID);
        $this->datatables->add_column('edit', ' $1 ', 'company_groupstatus(userGroupID,isActive,description)');
        echo $this->datatables->generate();
    }

    function save_company_usergroup()
    {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $companyID = current_companyID();
            $description = trim($this->input->post('description'));
            $userGroupID = trim($this->input->post('userGroupID'));
            if ($userGroupID) {
                $data = array(
                    'description' => $description
                );
                $this->db->where('userGroupID', $userGroupID);
                $result = $this->db->update('srp_erp_usergroups', $data);
                if($result){
                    $this->session->set_flashdata('s', 'Records Updated Successfully.');
                    echo json_encode(array('status' => true));
                }else{
                    $this->session->set_flashdata('e', 'Failed. Please contact support team');
                    echo json_encode(array('status' => false));
                }
            } else {

                $valid = $this->db->query("SELECT * FROM srp_erp_usergroups WHERE companyID = {$companyID} AND description = \"".$description."\"")->row_array();

                if ($valid) {
                    $this->session->set_flashdata('e', 'Failed. Entered description already exist.');
                    echo exit(json_encode(array('status' => false)));
                }

                $data['companyID'] = $companyID;
                $data['description'] = $description;
                $data['isActive'] = 1;
                $insert = $this->db->insert('srp_erp_usergroups', $data);
                $userGroupID = $this->db->insert_id();
                if ($insert) {
                    $defaultWidgets = $this->db->query("select widgetID from srp_erp_widgetmaster where isDefault = -1")->result_array();
                    foreach ($defaultWidgets as $val) {
                        $widgetdata['companyID'] = current_companyID();
                        $widgetdata['userGroupID'] = $userGroupID;
                        $widgetdata['widgetID'] = $val['widgetID'];
                        $insertDefaultWidget = $this->db->insert('srp_erp_usergroupwidget', $widgetdata);
                    }
                    if ($insertDefaultWidget) {
                        $this->session->set_flashdata('s', 'Records Inserted Successfully.');
                        echo json_encode(array('status' => true));
                    }
                } else {
                    $this->session->set_flashdata('e', 'Failed. Please contact support team');
                    echo json_encode(array('status' => false));
                }
            }
        }
    }

    function update_companyUsergroup()
    {
        $userGroupID = $this->input->post('userGroupID');
        $status = $this->input->post('status');
        $update = $this->db->update('srp_erp_usergroups', array('isActive' => $status), array('userGroupID' => $userGroupID));
        $this->session->set_flashdata('s', 'Successfully records updated.');
        echo json_encode(array('status' => true));
        exit;

    }

    function loadWidet()
    {
        $data = array();
        $usergroupID = $this->input->post('usergroupID');
        //$data["widgets"] = $this->Access_menu_model->loadWidet();
        $data["widgets"] = $this->Access_menu_model->loadWidet($usergroupID);
        $path = 'system/widget/erp_company_user_group_widget';
        $this->load->view($path, $data);
    }

    function save_widget()
    {
        echo json_encode($this->Access_menu_model->save_widget());
        //return  $this->Access_menu_model->save_widget();
    }

    function deleteUserGroupID()
    {
        echo json_encode($this->Access_menu_model->deleteUserGroupID());
    }

    function load_user_group()
    {
        echo json_encode($this->Access_menu_model->load_user_group());
    }
    function update_emp_language()
    {

            echo json_encode($this->Access_menu_model->update_emp_language());

    }

}
