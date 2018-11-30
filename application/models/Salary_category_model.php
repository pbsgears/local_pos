<?php

/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 5/11/2016
 * Time: 4:31 PM
 */
class Salary_category_model extends ERP_Model{

    public function __construct(){
        parent::__construct();
        $this->load->model('Employee_model');
    }

    function isExistDescription($description, $isPayrollCategory){
        $companyID  = current_companyID();
        $where      = "companyID='$companyID' AND salaryDescription='$description' AND isPayrollCategory='$isPayrollCategory'";

        $query = $this->db->select('salaryDescription, salaryCategoryID')
                          ->from('srp_erp_pay_salarycategories')
                          ->where($where)
                          ->get();

        return $query->result_array();
    }

    function saveCategory($data){

        $this->db->trans_start();
        $this->db->insert('srp_erp_pay_salarycategories', $data);
        $insertID = $this->db->insert_id();

        $templateFieldArray = array(
            'fieldName'          => $data['salaryDescription'],
            'caption'            => $data['salaryDescription'],
            'fieldType'          => $data['salaryCategoryType'],
            'salaryCatID'        => $insertID,
            'companyID'          => $data['companyID'],
            'companyCode'        => $data['companyCode'],
            'createdPCID'        => $data['createdPCID'],
            'createdUserGroup'   => $data['createdUserGroup'],
            'createdUserID'      => $data['createdUserID'],
            'createdDateTime'    => $data['createdDateTime'],
            'createdUserName'    => $data['createdUserName']
        );

        $this->db->insert('srp_erp_pay_templatefields', $templateFieldArray);


        if( $data['salaryCategoryType'] == 'A'){
            $setupID =  $this->deductionDeclarations();
            $id1 = 'deductionPolicyID';
            $id2 = 'salaryCategoryID';
        }
        else{
            $setupID =  $this->additionDeclarations();
            $id1 = 'salaryCategoryID';
            $id2 = 'deductionPolicyID';
        }



        if( $setupID != null){
            $setupArray = array();
            $i = 0;

            /*$setupArray[$i]['segmentID']         =  $data['segmentID'];
            $setupArray[$i]['segmentCode']       =  $data['segmentCode'];*/

            foreach($setupID as $setup){
                $setupArray[$i][$id1]                =  $setup->salaryCategoryID;
                $setupArray[$i][$id2]                =  $insertID;
                $setupArray[$i]['isSelected']        =  0;
                $setupArray[$i]['companyID']         =  $data['companyID'];
                $setupArray[$i]['companyCode']       =  $data['companyCode'];
                $setupArray[$i]['createdPCID']       =  $data['createdPCID'];
                $setupArray[$i]['createdUserGroup']  =  $data['createdUserGroup'];
                $setupArray[$i]['createdUserID']     =  $data['createdUserID'];
                $setupArray[$i]['createdDateTime']   =  $data['createdDateTime'];
                $setupArray[$i]['createdUserName']   =  $data['createdUserName'];
                $i++;
            }

            //print_r( $setupArray ); die();
            $this->db->insert_batch('srp_erp_pay_deductionpolicysetup', $setupArray);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('e', 'error in salary category create process');
                return array('e', 'error in salary category create process');
            } else {
                $this->db->trans_commit();
                return array('s','New Salary Category Added Successfully');
            }
        }
        else{
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'error in salary category create process');
            } else {
                $this->db->trans_commit();
                return array('s','New Category Added Successfully');
            }
        }

    }

    function editCategory($data, $eID){

        $this->db->trans_start();
        $this->db->update('srp_erp_pay_salarycategories', $data, 'salaryCategoryID='.$eID);


        $templateData = array(
            'fieldName' => $data['salaryDescription'],
            'caption' => $data['salaryDescription'],
            'modifiedPCID' => $data['modifiedPCID'],
            'modifiedUserID' => $data['modifiedUserID'],
            'modifiedDateTime' => $data['modifiedDateTime'],
            'modifiedUserName' => $data['modifiedUserName']
        );

        $templateDetData = array(
            'columnName' => $data['salaryDescription'],
            'modifiedPCID' => $data['modifiedPCID'],
            'modifiedUserID' => $data['modifiedUserID'],
            'modifiedDateTime' => $data['modifiedDateTime'],
            'modifiedUserName' => $data['modifiedUserName']
        );

        $this->db->where('salaryCatID', $eID)->update('srp_erp_pay_templatefields', $templateData);
        $this->db->where('salaryCatID', $eID)->update('srp_erp_pay_templatedetail', $templateDetData);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in salary category update process');
        }
        else{
            return array('s', 'Salary Category '.$data['salaryDescription'].' Updated.');
        }
    }

    function salaryCategoryIsInUse($catID){
        $query = $this->db->select('declarationDetailID')
                        ->from('srp_erp_salarydeclarationdetails')
                        ->where('salaryCategoryID', $catID)
                        ->where('companyID', current_companyID())
                        ->get();

        return $query->result();
    }

    function deleteCat($catID){
        $this->db->trans_start();
        $this->db->where('salaryCategoryID', $catID)->delete('srp_erp_pay_salarycategories');
        $this->db->where('salaryCatID', $catID)->delete('srp_erp_pay_templatefields');
        $this->db->where('salaryCatID', $catID)->delete('srp_erp_pay_templatedetail');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in delete process');
        }
        else{
            return array('s', 'Successfully Deleted');
        }
    }

    function additionDeclarations(){
        $com = current_companyID();
        $where = 'companyID=' . $com . ' AND salaryCategoryType ="A"';
        $this->db->select('srp_erp_pay_salarycategories.salaryDescription, srp_erp_pay_salarycategories.salaryCategoryID')
                 ->from('srp_erp_pay_salarycategories')
                 ->where($where);
        $query = $this->db->get();

        //echo $this->db->last_query();
        return $query->result();
    }

    function deductionDeclarations(){
        $com = current_companyID();
        //$where = 'cat.companyID=' . $com . ' AND salaryCategoryType ="D" AND isSelected=1';
        $where = 'cat.companyID=' . $com . ' AND salaryCategoryType ="D"';
        $query = $this->db->select('cat.salaryDescription, cat.salaryCategoryID, deductionPercntage AS per')
                          ->from('srp_erp_pay_salarycategories AS cat')
                          ->join('srp_erp_pay_deductionpolicysetup AS setup', 'setup.deductionPolicyID = cat.salaryCategoryID', 'left')
                          ->where($where)->group_by('cat.salaryCategoryID')->get();
        //echo $this->db->last_query();
        return $query->result();
    }

    function saveMonthlyDeclarationCategory($data){
        $this->db->insert('srp_erp_pay_monthlydeclarationstypes', $data);

        if( $this->db->affected_rows() > 0 ){
            return array('s', 'Monthly declaration saved successfully.');
        }else{
            return array('e', 'Error in process');
        }
    }


    function editMonthlyDeclaration($data, $eID){
        $this->db->trans_start();
        $this->db->update('srp_erp_pay_monthlydeclarationstypes', $data, 'monthlyDeclarationID='.$eID);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in monthly declaration update process');
        }
        else{
            return array('s', 'Monthly declaration [ '.$data['monthlyDeclaration'].' ] Updated.');
        }
    }

    function isExistMonthlyDeclaration($description){

        $query = $this->db->select('monthlyDeclaration, monthlyDeclarationID')
            ->from('srp_erp_pay_monthlydeclarationstypes')
            ->where('companyID',  current_companyID() )
            ->where('monthlyDeclaration', $description)
            ->get();

        return $query->result_array();
    }

    function delete_monthlyDeclarationSalCat($declarationID){

        $this->db->trans_start();
        $this->db->where('monthlyDeclarationID', $declarationID)->delete('srp_erp_pay_monthlydeclarationstypes');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in delete process');
        }
        else{
            return array('s', 'Successfully Deleted');
        }
    }


    /** Over-time management for Salam-Air **/
    function saveOTElement() {
        $isAlreadyExistOTElement = $this->isAlreadyExistOTElement();

        if(count($isAlreadyExistOTElement) > 0){
            return ['e', 'This description already exist.'];
        }
        $this->db->trans_start();
        $companyID                       = current_companyID();
        $data['fixedElementDescription'] = $this->input->post('description');
        $data['companyID']               = $companyID;
        $data['createdUserGroup']        = current_user_group();
        $data['createdPCID']             = current_pc();
        $data['createdUserID']           = current_userID();
        $data['createdDateTime']         = $this->common_data['current_date'];
        $data['createdUserName']         = current_employee();

        $this->db->insert('srp_erp_ot_fixedelements', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['e', 'Error in save process'];
        }
        else {
            $this->db->trans_commit();
            return ['s', 'Successfully saved'];
        }
    }

    function isAlreadyExistOTElement(){
        $companyID = current_companyID();
        $description = $this->input->post('description');

        $data = $this->db->query("SELECT * FROM srp_erp_ot_fixedelements WHERE companyID={$companyID}
                                  AND fixedElementDescription='{$description}'")->row_array();

        return $data;
    }

    function editOTElement() {
        $description = $this->input->post('description');
        $fixedElementID = $this->input->post('hiddenID');

        $isAlreadyExistOTElement = $this->isAlreadyExistOTElement();

        if(count($isAlreadyExistOTElement) > 0){
            if($isAlreadyExistOTElement['fixedElementID'] != $fixedElementID){
                return ['e', 'This description is already exist.'];
            }
        }

        $this->db->trans_start();
        $companyID                       = current_companyID();
        $data['fixedElementDescription'] = $description;
        $data['modifiedPCID']            = current_pc();
        $data['modifiedUserID']          = current_userID();
        $data['modifiedDateTime']        = $this->common_data['current_date'];
        $data['modifiedUserName']        = current_employee();

        $this->db->where('fixedElementID', $fixedElementID)->where('companyID', $companyID)->update('srp_erp_ot_fixedelements', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['e', 'Error in update process'];
        }
        else {
            $this->db->trans_commit();
            return ['s', 'Successfully updated'];
        }
    }

    function delete_ot_element(){


        $fixedElementID = $this->input->post('fixedElementID');
        $companyID = current_companyID();

        $data = $this->db->query("SELECT count(employeeNo) AS usageCount FROM srp_erp_ot_fixedelementdeclarationdetails
                                  WHERE companyID={$companyID} AND fixedElementID={$fixedElementID}")->row('usageCount');
        echo $this->db->last_query();
die($data);
        if($data == 0){
            $this->db->trans_start();
            $this->db->where('fixedElementID', $fixedElementID)->delete('srp_erp_ot_fixedelements');
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Error in delete process');
            }
            else{
                $this->db->trans_commit();
                return array('s', 'Successfully Deleted');
            }
        }else{
            return ['e', 'This fixed elements is in use.<br/>You can not delete this.'];
        }

    }

    function create_overTimeGroup(){
        $this->db->trans_start();
        $companyID                       = current_companyID();

        $data = $this->db->query("SELECT otGroupDescription FROM srp_erp_ot_groups WHERE companyID={$companyID}
                                  AND otGroupDescription='{$this->input->post('description')}'")->row_array();

        if(!empty($data)){
            return ['e', 'Group already exist'];
        }else{
            $data['otGroupDescription']      = $this->input->post('description');
            $data['CurrencyID']              = $this->input->post('CurrencyID');
            $data['companyID']               = $companyID;
            $data['createdUserGroup']        = current_user_group();
            $data['createdPCID']             = current_pc();
            $data['createdUserID']           = current_userID();
            $data['createdDateTime']         = $this->common_data['current_date'];
            $data['createdUserName']         = current_employee();

            $this->db->insert('srp_erp_ot_groups', $data);
            $insertID = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return ['e', 'Error in save process'];
            }
            else {
                $this->db->trans_commit();
                return ['s', 'Successfully saved','otGroupID'=>$insertID];
            }
        }
    }

    function delete_ot_group(){
        $this->db->trans_start();
        $this->db->where('otGroupID', $this->input->post('otGroupID'))->delete('srp_erp_ot_groups');
        $this->db->where('otGroupID', $this->input->post('otGroupID'))->delete('srp_erp_ot_groupemployees');
        $this->db->where('otGroupID', $this->input->post('otGroupID'))->delete('srp_erp_ot_groupdetail');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in delete process');
        }
        else{
            $this->db->trans_commit();
            return array('s', 'Successfully Deleted');
        }
    }

    function saveInputRates(){
        $otGroupID = $this->input->post('otGroupID');
        $systemInputID = $this->input->post('systemInputID');
        $rate = $this->input->post('rate');
        $slabID = $this->input->post('slabID');
        $inputType = $this->input->post('inputType');
        $companyID = current_companyID();

        $grp = $this->db->query("SELECT systemInputID FROM srp_erp_ot_groupdetail WHERE otGroupID={$otGroupID}
                                  AND systemInputID='{$systemInputID}'")->row_array();

        if(!empty($grp)){
            return ['e', 'Input Type already exist'];
        }else{
            $this->db->trans_start();

            $data['otGroupID']          = $otGroupID;
            $data['systemInputID']      = $systemInputID;

            if($inputType == 1){
                $data['hourlyRate'] = null;
                $data['slabMasterID'] = $slabID;
            }else{
                $data['hourlyRate'] = $rate;
                $data['slabMasterID'] = null;
            }

            $data['companyID']          = $companyID;
            $data['createdUserGroup']   = current_user_group();
            $data['createdPCID']        = current_pc();
            $data['createdUserID']      = current_userID();
            $data['createdDateTime']    = $this->common_data['current_date'];
            $data['createdUserName']    = current_employee();

            $this->db->insert('srp_erp_ot_groupdetail', $data);
            $insertID = $this->db->insert_id();

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return ['e', 'Error in save process'];
            }
            else {
                $this->db->trans_commit();
                return ['s', 'Successfully saved','otGroupID'=>$insertID];
            }
        }
    }

    function delete_ot_group_emp(){
        $this->db->delete('srp_erp_ot_groupemployees', array('otGroupEmpID' => trim($this->input->post('otGroupEmpID'))));
        $this->session->set_flashdata('s', 'Deleted Successfully.');
        return true;
    }


    function editInputRates(){
        $systemInputID = $this->input->post('systemInputID');
        $rate = $this->input->post('rate');
        $slabID = $this->input->post('slabID');
        $inputType = $this->input->post('inputType');
        $otGroupDetailID = $this->input->post('hiddenID');
        $grpIDs=$this->input->post('otGroupID');

        $grp = $this->db->query("SELECT systemInputID FROM srp_erp_ot_groupdetail WHERE otGroupDetailID !={$otGroupDetailID}
                                  AND systemInputID='{$systemInputID}' AND otGroupID= $grpIDs")->row_array();
        if(!empty($grp)){
            return ['e', 'Input Type already exist'];
        }else{
            $this->db->trans_start();

            $data['systemInputID']      = $systemInputID;
            if($inputType == 1){
                $data['hourlyRate'] = null;
                $data['slabMasterID'] = $slabID;
            }else{
                $data['hourlyRate'] = $rate;
                $data['slabMasterID'] = null;
            }

            //$data['slabMasterID']         = $slabID;
            $data['modifiedPCID']        = current_pc();
            $data['modifiedUserID']      = current_userID();
            $data['modifiedDateTime']    = $this->common_data['current_date'];
            $data['modifiedUserName']    = current_employee();

            $this->db->where('otGroupDetailID', $otGroupDetailID)->update('srp_erp_ot_groupdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return ['e', 'Error in Update process'];
            }
            else {
                $this->db->trans_commit();
                return ['s', 'Successfully Updated'];
            }
        }


    }

    function save_assigned_OT_employees()
    {
        $otGroupID = $this->input->post('otGroupIDhn');
        $empID = $this->input->post('empHiddenID');


        foreach ($empID as $val) {

            $data = array(
                'otGroupID' => $otGroupID,
                'empID' => $val,
                'companyID' => current_companyID(),
                'createdPCID' => current_pc(),
                'createdUserGroup' => current_user_group(),
                'createdUserID' => current_userID(),
                'createdUserName' => current_employee(),
                'createdDateTime' => current_date()
            );

            $result = $this->db->insert('srp_erp_ot_groupemployees', $data);

        }
        if ($result) {
            return array('s', 'Employees successfully assigned to group.');
        } else {
            return array('e', 'Error in assigning employee.');
        }
    }

    function delete_ot_group_detail(){
        $this->db->delete('srp_erp_ot_groupdetail', array('otGroupDetailID' => trim($this->input->post('otGroupDetailID'))));
        $this->session->set_flashdata('s', 'Deleted Successfully.');
        return true;
    }

    function load_ot_group_description(){
        $otGroupID=$this->input->post('otGroupID');
        $data = $this->db->query("SELECT otGroupDescription FROM srp_erp_ot_groups WHERE otGroupID={$otGroupID}")->row_array();
        return $data;
    }

    function edit_group_description(){
        $otGroupID=$this->input->post('otGroupID');
        $otGroupDescription=$this->input->post('otGroupDescription');
        $companyID=current_companyID();
        $data = $this->db->query("SELECT otGroupDescription FROM srp_erp_ot_groups WHERE otGroupID != $otGroupID AND otGroupDescription = '$otGroupDescription' AND companyID = $companyID")->row_array();
       // $Desc=$data['otGroupDescription'];
        if(!empty($data)){
            return array('e', 'Group already exist.');
        }else{
            $datas['otGroupDescription']    = $otGroupDescription;
            $result=$this->db->where('otGroupID', $otGroupID)->update('srp_erp_ot_groups', $datas);
            if($result){
                return array('s', 'Group successfully updated.');
            }else{
                return array('e', 'Group update failed.');
            }
        }
    }
}