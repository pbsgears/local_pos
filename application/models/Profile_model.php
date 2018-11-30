<?php

class Profile_model extends ERP_Model
{
    function __contruct()
    {
        parent::__contruct();
    }

    function get_employees_detail($empID)
    {
        $output = array();
        $companyID = current_companyID();

        $output['employees'] = $this->db->query("SELECT qry_employeesdetails.*, srp_titlemaster.TitleDescription,srp_erp_bloodgrouptype.BloodDescription,srp_nationality.Nationality,srp_erp_maritialstatus.description AS maritialDescription,srp_religion.Religion,srp_erp_gender.`name` AS GenderDesc
 FROM (SELECT * FROM srp_employeesdetails WHERE EIdNo='{$empID}') qry_employeesdetails LEFT JOIN srp_titlemaster ON qry_employeesdetails.EmpTitleId = srp_titlemaster.TitleID LEFT JOIN srp_erp_bloodgrouptype ON qry_employeesdetails.BloodGroup= srp_erp_bloodgrouptype.BloodTypeID LEFT JOIN srp_nationality ON qry_employeesdetails.Nid= srp_nationality.NId LEFT JOIN srp_erp_maritialstatus ON qry_employeesdetails.MaritialStatus= srp_erp_maritialstatus.maritialstatusID LEFT JOIN srp_religion ON qry_employeesdetails.RId = srp_religion.RId LEFT JOIN srp_erp_gender ON qry_employeesdetails.Gender = srp_erp_gender.genderID")->row_array();


        $this->db->select("company_id,company_name,company_address1,company_address2,company_country,company_phone,company_logo");
        $this->db->from("srp_erp_company");
        $this->db->where("company_id", $output['employees']['Erp_companyID']);
        $output['companymaster'] = $this->db->get()->row_array();

        $this->db->select("DesDescription");
        $this->db->from("srp_designation");
        $this->db->where("DesignationID", $output['employees']['EmpDesignationId']);
        $output['designation'] = $this->db->get()->row_array();

        $output['nationality'] = $this->db->select('*')->from('srp_nationality')->where('Erp_companyID', $companyID)->order_by('Nationality', 'ASC')->get()->result_array();

        $output['religion'] = $this->db->select('*')->from('srp_religion')->where('Erp_companyID', $companyID)->order_by('Religion', 'ASC')->get()->result_array();

        $output['social_insurance'] = $this->db->query("SELECT srp_erp_socialinsurancedetails.socialInsuranceDetailID, srp_erp_socialinsurancemaster.Description,srp_erp_socialinsurancemaster.sortCode, srp_erp_socialinsurancedetails.socialInsuranceNumber FROM srp_erp_socialinsurancemaster INNER JOIN srp_erp_socialinsurancedetails ON srp_erp_socialinsurancemaster.socialInsuranceID = srp_erp_socialinsurancedetails.socialInsuranceMasterID WHERE srp_erp_socialinsurancedetails.empID = '{$empID}' AND srp_erp_socialinsurancedetails.companyID = '{$companyID}'")->result_array();

        $output['manager'] = $this->db->query("SELECT
	managerID,
	srp_employeesdetails.Ename2 as Ename2
FROM
	srp_erp_employeemanagers
JOIN srp_employeesdetails ON srp_erp_employeemanagers.managerID = srp_employeesdetails.EIdNo
WHERE
	empID = $empID
AND active = 1
AND companyID = $companyID")->row_array();

        $output['department'] = $this->db->query("SELECT

GROUP_CONCAT(DepartmentDes) AS DepartmentDes
FROM
	srp_empdepartments
JOIN srp_departmentmaster ON srp_empdepartments.DepartmentMasterID = srp_departmentmaster.DepartmentMasterID
WHERE
	EmpID = $empID
AND srp_empdepartments.Erp_companyID = $companyID")->row_array();

        return $output;
    }

    function update_empDetail()
    {
        $column = $this->input->post('name');
        $value = $this->input->post('value');
        $empID = $this->input->post('pk');
        $companyID = current_companyID();
        $returnData = '';

        switch ($column) {
            case 'EDOB':
            case 'EVisaExpiryDate':
                $value = covertToMysqlDate($value);
                $returnData = format_date_dob($value);
                break;
        }

        $this->db->trans_start();

        $isNeedApproval = getPolicyValues('EPD', 'All');


        if( $isNeedApproval == 1 ){
            $pendingID = $this->db->query("SELECT id FROM srp_erp_employeedatachanges WHERE companyID={$companyID} AND
                                       empID={$empID} AND columnName='{$column}' AND approvedYN!=1")->row('id');

            if(!empty($pendingID)){
                $this->db->where('id', $pendingID)->update('srp_erp_employeedatachanges', ['columnVal'=>$value]);
            }
            else{
                $data = [
                    'empID' => $empID,
                    'columnName' => $column,
                    'columnVal' => $value,
                    'companyID' => $companyID,
                ];

                $this->db->insert('srp_erp_employeedatachanges', $data);
            }
        }
        else{
            $this->db->where(['Erp_companyID'=> $companyID, 'EIdNo'=>$empID]);
            $this->db->update('srp_employeesdetails', [$column=>$value]);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Updated successfully.', $returnData);
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error In update process');
        }

    }

    function change_password()
    {
        $currentPassword = md5($this->input->post('currentPassword'));
        $newPassword = md5($this->input->post('newPassword'));
        $isChangePassword = $this->input->post('isChangePassword');
        $empId = current_userID();

        $isValid = $this->db->query("SELECT * FROM `srp_employeesdetails` WHERE `EIdNo` = '{$empId}'")->row_array();

//        echo("SELECT * FROM `srp_employeesdetails` WHERE `EIdNo` = '{$empId}'");
//        exit;
//        echo $isValid . '<br>' . $currentPassword;

        if ($isValid['Password'] == $currentPassword) {
            if ($isChangePassword == 1) {
                $update = $this->db->query("UPDATE srp_employeesdetails SET Password='{$newPassword}' , isChangePassword =0 WHERE `EIdNo`='{$empId}'");
            } else {
                $update = $this->db->query("UPDATE srp_employeesdetails SET Password='{$newPassword}' WHERE `EIdNo`='{$empId}'");
            }
            if ($update) {
                $db = $this->load->database('db2', true);
                $updateDB2 = $db->query("UPDATE user SET Password='{$newPassword}' WHERE `Username`='{$isValid['UserName']}'");


                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(200)
                    ->set_output(json_encode(array('s', 'Password Successfully Updated')));
            }
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(array('e', 'Current Password is Incorrect')));
        }
    }

    function get_employees_myprof_departments($empID){
        //$output = array();
        $companyID = current_companyID();
        $output = $this->db->query("")->row_array();
        return $output;
    }

    function empDocument_setup()
    {
        $companyID = current_companyID();
        $empID = current_userID();
        $convertFormat = convert_date_format_sql();

        return $this->db->query("SELECT DocDesFormID, DocDescription, isMandatory, FileName,  DATE_FORMAT(issueDate,'{$convertFormat}') AS issueDate,
                                 DATE_FORMAT(expireDate,'{$convertFormat}') AS expireDate, issuedBy, issuedByText, master.DocDesID
                                 FROM srp_documentdescriptionmaster master
                                 JOIN srp_documentdescriptionsetup setup ON setup.DocDesID = master.DocDesID
                                 LEFT JOIN srp_documentdescriptionforms forms ON forms.DocDesID = master.DocDesID
                                 AND PersonType='E' AND PersonID={$empID}
                                 WHERE master.Erp_companyID={$companyID} AND isDeleted=0 ")->result_array();
    }

    function update_familydetails()
    {

        $column = $this->input->post('name');
        $value = $this->input->post('value');
        $empfamilydetailsID = $this->input->post('pk');
        $companyID = current_companyID();
        $returnData = '';

        /*switch ($column) {
            case 'EDOB':
            case 'EVisaExpiryDate':
                $value = covertToMysqlDate($value);
                $returnData = format_date_dob($value);
                break;
        }

        $this->db->trans_start();*/

        $isNeedApproval = getPolicyValues('EPD', 'All');
        $approved = $this->db->query("SELECT empID,approvedYN FROM srp_erp_family_details WHERE
                                       empfamilydetailsID={$empfamilydetailsID}")->row_array();

        if( $isNeedApproval == 1 && $approved['approvedYN']!=0){
            $pendingID = $this->db->query("SELECT id FROM srp_erp_employeefamilydatachanges WHERE companyID={$companyID} AND
                                       empfamilydetailsID={$empfamilydetailsID} AND columnName='{$column}' AND approvedYN!=1")->row('id');

            if(!empty($pendingID)){
                $this->db->where('id', $pendingID)->update('srp_erp_employeefamilydatachanges', ['columnVal'=>$value]);
            }
            else{
                $data = [
                    'empfamilydetailsID' => $empfamilydetailsID,
                    'columnName' => $column,
                    'columnVal' => $value,
                    'companyID' => $companyID,
                    'empID' => $approved['empID'],
                ];

               $results= $this->db->insert('srp_erp_employeefamilydatachanges', $data);
            }
        }
        else{
            $this->db->where(['empfamilydetailsID'=>$empfamilydetailsID]);
            $results= $this->db->update('srp_erp_family_details', [$column=>$value]);
        }

        if ($results) {
            return array('error' => 0, 'message' => 'updated');
        } else {
            return array('error' => 1, 'message' => 'updated Fail');
        }

    }


}