<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class DashboardWidget_model extends CI_Model
{
    function __contruct()
    {
        parent::__contruct();
    }

    function getWidget($position)
    {
        $result = "";
        $this->db->select('userGroupID');
        $this->db->where('empID', current_userID());
        $this->db->where('companyID', current_companyID());
        $usergroup = $this->db->get('srp_erp_employeenavigation')->row_array();
        if ($usergroup) {
            $result = $this->db->query("SELECT * FROM srp_erp_widgetmaster INNER JOIN srp_erp_usergroupwidget on srp_erp_widgetmaster.widgetID = srp_erp_usergroupwidget.widgetID  WHERE srp_erp_widgetmaster.positionID=" . $position . " AND srp_erp_usergroupwidget.userGroupID =" . $usergroup["userGroupID"] . " AND srp_erp_usergroupwidget.companyID =" . current_companyID())->result_array();
        }else{
            $result = $this->db->query("SELECT * FROM srp_erp_widgetmaster WHERE isDefault=-1")->result_array();
        }

        return $result;
    }

    function getWidgetEdit($userDashboardID)
    {
        $result = $this->db->query("SELECT * from srp_erp_userdashboardmaster INNER JOIN srp_erp_dashboardtemplate ON srp_erp_dashboardtemplate.templateID =  srp_erp_userdashboardmaster.templateID WHERE userDashboardID=" . $userDashboardID)->row();
        //$templateID= $result['templateID'];
        return $result;
    }

    function getWidgetPositionEdit($userDashboardID)
    {
        $this->db->select('userGroupID');
        $this->db->where('empID', current_userID());
        $this->db->where('companyID', current_companyID());
        $usergroup = $this->db->get('srp_erp_employeenavigation')->row_array();

        $result = $this->db->query("SELECT
	srp_erp_userdashboardwidget.positionID,
	srp_erp_userdashboardwidget.widgetID,
	srp_erp_userdashboardwidget.sortOrder,
srp_erp_widgetposition.position,
srp_erp_widgetmaster.widgetName,
srp_erp_widgetmaster.widgetImage,
srp_erp_widgetmaster.positionID as positn
FROM
	srp_erp_userdashboardwidget

JOIN srp_erp_widgetposition ON srp_erp_userdashboardwidget.positionID =  srp_erp_widgetposition.widgetPositionID
JOIN srp_erp_widgetmaster ON srp_erp_userdashboardwidget.widgetID =  srp_erp_widgetmaster.widgetID
JOIN (SELECT * FROM srp_erp_usergroupwidget WHERE userGroupID = ".$usergroup['userGroupID'].") as ugw ON ugw.widgetID = srp_erp_userdashboardwidget.widgetID

WHERE userDashboardID=" . $userDashboardID)->result_array();

        $description = $this->db->query("SELECT dashboardDescription FROM srp_erp_userdashboardmaster WHERE userDashboardID=" . $userDashboardID)->row('dashboardDescription');
        $sortOrder = $this->db->query("SELECT sortOrder FROM srp_erp_userdashboardmaster WHERE userDashboardID=" . $userDashboardID)->row('sortOrder');
        //$templateID= $result['templateID'];
        $data['detail'] = $result;
        $data['description'] = $description;
        $data['Order'] = $sortOrder;
        return $data;
    }

    function save_template_setup()
    {
        $descriptionEdit = $this->input->post('descriptionEdit');
        $sortOrder = $this->input->post('sortOrder');
        $data['dashboardDescription'] = $descriptionEdit;
        $data['sortOrder'] = $sortOrder;
        $this->db->where('userDashboardID', $this->input->post('templateID'));
        $updateDescription = $this->db->update('srp_erp_userdashboardmaster', $data);

        if ($updateDescription) {
            $results='';
            $this->db->where('userDashboardID', $this->input->post('templateID'));
            $result = $this->db->delete('srp_erp_userdashboardwidget');
            if ($result) {
                foreach ($this->input->post('userdashboardWidget') as $val) {
                    if (!empty($val)) {
                        $str = $val;
                        $expld = explode("_", $str);
                        $dashboardID = $expld[0];
                        $positionID = $expld[1];
                        $sortOrder = $expld[2];
                        $widgetID = $expld[3];
                        $empid = current_userID();
                        $compid = current_companyID();

                        $this->db->select('userDashboardWidgetID');
                        $this->db->where('userDashboardID', $dashboardID);
                        $this->db->where('widgetID', $widgetID);
                        $widgetExist = $this->db->get('srp_erp_userdashboardwidget')->row_array();

                        if (!empty($widgetExist['userDashboardWidgetID'])) {
                            continue;
                        } else {
                            $this->db->set('userDashboardID', $dashboardID);
                            $this->db->set('positionID', $positionID);
                            $this->db->set('sortOrder', $sortOrder);
                            $this->db->set('widgetID', $widgetID);
                            $this->db->set('employeeID', $empid);
                            $this->db->set('companyID', $compid);

                            $results = $this->db->insert('srp_erp_userdashboardwidget');
                            //$insert_id = $this->db->insert_id();
                        }
                    }
                }
                if ($results || $results=='') {
                    return array('s', 'Widget Added Successfully');
                }
            }
        }
    }

    function save_template_setup_add()
    {
        if (!empty($this->input->post('description'))) {
            $empid = current_userID();
            $compid = current_companyID();
            $description = $this->input->post('description');
            $sortOrder = $this->input->post('sortOrdersave');
            $templateID = $this->input->post('templateIDAdd');
            $this->db->set('employeeID', $empid);
            $this->db->set('dashboardDescription', $description);
            $this->db->set('sortOrder', $sortOrder);
            $this->db->set('templateID', $templateID);
            $this->db->set('companyID', $compid);
            $desc = $this->db->insert('srp_erp_userdashboardmaster');
            $insert_id = $this->db->insert_id();
            if ($desc) {
                foreach ($this->input->post('userdashboardWidget') as $val) {
                    if (!empty($val)) {
                        $str = $val;
                        $expld = explode("_", $str);
                        //$dashboardID=$expld[0];
                        $positionID = $expld[1];
                        $sortOrder = $expld[2];
                        $widgetID = $expld[3];
                        $empid = current_userID();
                        $compid = current_companyID();

                        $this->db->set('userDashboardID', $insert_id);
                        $this->db->set('positionID', $positionID);
                        $this->db->set('sortOrder', $sortOrder);
                        $this->db->set('widgetID', $widgetID);
                        $this->db->set('employeeID', $empid);
                        $this->db->set('companyID', $compid);

                        $results = $this->db->insert('srp_erp_userdashboardwidget');
                        //$insert_id = $this->db->insert_id();
                    }
                }
                if ($results) {
                    return array('s', 'Widget Added Successfully');
                }
            }
        } else {
            return array('e', 'Description is Required');
        }
    }

    function delete_dashboard()
    {
        $compid = current_companyID();
        $dashboardID = $this->input->post('dashboardID');

        $this->db->where('userDashboardID', $dashboardID);
        $this->db->where('companyID', $compid);
        $result = $this->db->delete('srp_erp_userdashboardmaster');
        if ($result) {
            $this->db->where('userDashboardID', $dashboardID);
            $this->db->where('companyID', $compid);
            $this->db->delete('srp_erp_userdashboardwidget');
            return array('s', 'Dashboard Deleted Successfully');
        }


    }

}
