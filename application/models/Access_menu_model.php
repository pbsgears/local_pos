<?php

class Access_menu_model extends ERP_Model
{

    /*function loadWidet()
    {
        $result = $this->db->query("SELECT widgetID,widgetName from srp_erp_widgetmaster")->result_array();
        return $result;
    }*/

    function loadWidet($usergroupID)
    {
        $compid = current_companyID();

        $result = $this->db->query("SELECT
    srp_erp_widgetmaster.widgetID,
    srp_erp_widgetmaster.widgetName,
    srp_erp_usergroupwidget.widgetID AS widget
FROM
    srp_erp_widgetmaster
LEFT JOIN srp_erp_usergroupwidget ON srp_erp_widgetmaster.widgetID = srp_erp_usergroupwidget.widgetID
WHERE
    srp_erp_usergroupwidget.companyID = $compid
AND srp_erp_usergroupwidget.userGroupID = $usergroupID
UNION
SELECT
        srp_erp_widgetmaster.widgetID,
        srp_erp_widgetmaster.widgetName,
        NULL AS widget
    FROM
        srp_erp_widgetmaster
    WHERE
        srp_erp_widgetmaster.widgetID NOT IN(
            SELECT
                widgetID
            FROM
                srp_erp_usergroupwidget
            WHERE
                usergroupID = $usergroupID
            AND companyID = $compid
        )
")->result_array();
        return $result;
    }

    function save_widget()
    {
        //print_r($_POST); exit;
        $results='';
        $userGroupID = $this->input->post('userGroupIDWidget');
        $widgetcheck = $this->input->post('widgetCheck');
        $isAlreadySelected = $this->input->post('isAlreadySelected');
        $compID = current_companyID();
        if (!empty($widgetcheck)) {
            foreach ($isAlreadySelected as $key=>$vals) {
                $data = explode('|', $isAlreadySelected[$key]);
                $wedgetID = trim($data[1]);

                if($data[0] == 'yes' && !in_array( $wedgetID , $widgetcheck)){
                    $del_arr = array(
                        'userGroupID' => $userGroupID,
                        'companyID' => current_companyID(),
                        'widgetID' => $wedgetID,
                    );
                    $this->db->where($del_arr)->delete('srp_erp_usergroupwidget');
                    //echo '<p>'.$this->db->last_query();
                }
            }
            foreach ($widgetcheck as $key=>$vals) {
                $result = $this->db->query("SELECT widgetID FROM srp_erp_usergroupwidget where userGroupID = $userGroupID and companyID= $compID and widgetID= $vals")->result();
                if ($result) {
                    continue;
                } else {
                    $this->db->set('companyID', $compID);
                    $this->db->set('userGroupID', $userGroupID);
                    $this->db->set('widgetID', $vals);
                    $results = $this->db->insert('srp_erp_usergroupwidget');
                }
            }
        }else{
            $delAll = array(
                'userGroupID' => $userGroupID,
                'companyID' => current_companyID(),
            );
            $this->db->where($delAll)->delete('srp_erp_usergroupwidget');
            return array('e', 'Select Widget');
        }
        if ($results) {
            return array('s', 'Widget Added Successfully');
        }else{
            return array('s', 'Widget Added Successfully');
        }
    }

    function deleteUserGroupID(){
        $assigned  = $this->db->select('*')->from('srp_erp_employeenavigation')->where(array('userGroupID' => $this->input->post('userGroupID'), 'companyID' => current_companyID()))->get()->result_array();
        if($assigned){
            return array('w', 'You cannot delete this usergroup because it is already assigned to users');
        }else{
            $this->db->where('userGroupID', $this->input->post('userGroupID'));
            $this->db->where('companyID', current_companyID());
            $result = $this->db->delete('srp_erp_usergroupwidget');

            $this->db->where('userGroupID', $this->input->post('userGroupID'));
            $this->db->where('companyID', current_companyID());
            $result = $this->db->delete('srp_erp_usergroups');
            if($result){
                return array('s', 'User group successfully deleted.');
            }else{
                return array('e', 'Error Occurred');
            }
        }
    }

    function load_user_group()
    {
        $userGroupID = trim($this->input->post('userGroupID'));
        $data = $this->db->query("select description FROM srp_erp_usergroups WHERE userGroupID = {$userGroupID} ")->row_array();
        return $data;
    }

    function update_emp_language()
    {
        $companyID = current_companyID();
        $EIdNo = current_userID();
        $languageid =  trim($this->input->post('languageid'));
        $emplang = $this->db->query("select languageID from srp_employeesdetails WHERE Erp_companyID={$companyID} AND EIdNo={$EIdNo}")->row_array();

        $this->session->set_userdata("emplangid", $languageid);
        $data['languageID'] = $languageid;
        $this->db->where('Erp_companyID', current_companyID());
        $this->db->where('EIdNo', current_userID());
        $this->db->update('srp_employeesdetails', $data);

    }

    /*$ext = ( !empty(array_search( $wedgetID , $widgetcheck)) )? 'true' : 'false'  ;
                //$ext =  $data[1] ;
                echo '<p>'.$ext.' |' .$key .'';*/
}