<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Profile_model');
        $this->load->helpers('profile_helper');
        $this->load->helper('Employee');
        $this->load->model('Employee_model');
    }

    public function empProfile()
    {
        $isNeedApproval = getPolicyValues('EPD', 'All');
        $data['pendingData'] = [];
        $data['empID'] = current_userID();
        $data['empArray'] = $this->Profile_model->get_employees_detail($data['empID']);

        if( $isNeedApproval == 1){
            $data['pendingData'] = get_pendingEmpApprovalData($data['empID']);
        }

        $data['yearMonth']['payrollYear'] = date('Y');
        $data['yearMonth']['payrollMonth'] = date('m');
        $data['leaveDetails'] = $this->Employee_model->get_emp_leaveDet_paySheetPrint($data['empID'], $data['yearMonth']);


        $this->load->view('system/profile/ajax/load_profile', $data);
    }

    function fetch_pendingEmpDataApproval(){
        $empID = $this->input->post('empID');
        $companyID = current_companyID();


        $familyDataChanges = $this->db->query("SELECT changeTB.*, familyDet.`name` changeName FROM srp_erp_employeefamilydatachanges AS changeTB
                                               JOIN srp_erp_family_details AS familyDet ON familyDet.empfamilydetailsID = changeTB.empfamilydetailsID
                                               WHERE companyID={$companyID} AND changeTB.empID={$empID} AND familyDet.empID={$empID}
                                               AND changeTB.approvedYN!=1 GROUP BY id")->result_array();

        $familyDataChanges = array_group_by($familyDataChanges, 'empfamilydetailsID');

        $familyDataNew = $this->Employee_model->fetch_family_details($empID, 0);

        $data['empID'] = $empID;
        $data['empArray'] = $this->Profile_model->get_employees_detail($empID);
        $data['pendingData'] = get_pendingEmpApprovalData($empID);
        $data['familyData_changes'] = $familyDataChanges;
        $data['familyData_new'] = $familyDataNew;

        $this->load->view('system/profile/ajax/pending_data', $data);
    }

    function approve_pendingEmpData(){
        $upDateNameWithInitial = $this->input->post('upDateNameWithInitial');
        $familyData = $this->input->post('familyData');
        $addFamilyData = $this->input->post('addFamilyData');
        $upDateColumn = $this->input->post('upDateColumn');
        $columnVal = $this->input->post('columnVal');
        $empID = $this->input->post('empID');
        $companyID = current_companyID();


        if(empty($upDateColumn) && empty($upDateNameWithInitial) && empty($familyData) && empty($addFamilyData)){
            die( json_encode(['e', 'There is no data to update.']) );
        }

        $updateData = [
            'approvedYN' => 1,
            'approvedDate' => current_date(),
            'approvedbyEmpID' => current_userID()
        ];


        $this->db->trans_start();

        /**** Personal Data changes ***/
        if( !empty($upDateColumn)){

            $data = [];
            foreach($upDateColumn as $key=>$row){
                $data[$row] = $columnVal[$row];
                $updateData['columnVal'] = $columnVal[$row];

                $this->db->where( ['columnName'=>$row, 'empID' =>$empID, 'companyID'=>$companyID])->update('srp_erp_employeedatachanges', $updateData);
            }

            $this->db->where(['EIdNo' => $empID]) ->update('srp_employeesdetails', $data);

        }

        if( !empty($upDateNameWithInitial)){
            $initial = $this->input->post('initial');
            $initial_changed = $this->input->post('initial_changed');
            $eName4 = $this->input->post('Ename4');
            $eName4_changed = $this->input->post('Ename4_changed');

            $data['initial'] = $initial;
            $data['Ename4'] = $eName4;
            $data['Ename2'] = $initial . ' ' . $eName4;

            $this->db->where(['EIdNo' => $empID]) ->update('srp_employeesdetails', $data);

            if($initial_changed == 1){
                $this->db->where( ['columnName'=>'initial', 'empID' =>$empID, 'companyID'=>$companyID])->update('srp_erp_employeedatachanges', $updateData);
            }

            if($eName4_changed == 1){
                $this->db->where( ['columnName'=>'Ename4', 'empID' =>$empID, 'companyID'=>$companyID])->update('srp_erp_employeedatachanges', $updateData);
            }
        }



        /**** Family Data changes ***/
        if(!empty($familyData)) {
            foreach ($familyData as $familyID => $familyRow) {

                $familyDataUpdate = [];
                $familyDataUpdate['modifiedUser'] = current_employee();
                $familyDataUpdate['modifiedPc'] = current_pc();

                foreach ($familyRow as $rowName => $rowVal) {
                    $familyDataUpdate[$rowName] = $rowVal;

                    $this->db->where(['columnName' => $rowName, 'empID' => $empID, 'companyID' => $companyID])->update('srp_erp_employeefamilydatachanges', $updateData);
                }

                $this->db->where(['empfamilydetailsID' => $familyID, 'empID' => $empID])->update('srp_erp_family_details', $familyDataUpdate);

            }
        }


        /**** Family Data add ***/
        if(!empty($addFamilyData)){
            foreach($addFamilyData as $appEmpKey => $appEmp){
                $familyDataUpdate = [];
                $familyDataUpdate['approvedYN'] = 1;
                $familyDataUpdate['modifiedUser'] = current_employee();
                $familyDataUpdate['modifiedPc'] = current_pc();

                $this->db->where(['empID'=>$empID, 'empfamilydetailsID'=>$appEmp])->update('srp_erp_family_details', $familyDataUpdate);
            }
        }



        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            echo json_encode( ['s', 'Updated successfully.']);
        } else {
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error In update process']);
        }

    }

    function update_empDetail()
    {
        echo json_encode($this->Profile_model->update_empDetail());
    }

    function change_password()
    {
        $this->form_validation->set_rules('currentPassword', 'Current Password', 'trim|required');
        $this->form_validation->set_rules('newPassword', 'New Password', 'trim|required');
        $this->form_validation->set_rules('confirmPassword', 'Confirm Password', 'trim|required|matches[newPassword]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            return $this->Profile_model->change_password();
        }
    }

    function fetch_family_details()
    {
        $empID = current_userID();
        $data['empArray'] = $this->Employee_model->fetch_family_details($empID);
        //$this->load->view('system/hrm/ajax/ajax-employee_profile_load_family_info', $data);
        $this->load->view('system/hrm/ajax/ajax-employee_profile_load_family_info_profile', $data);
    }

    public function load_empDocumentProfileView()
    {
        $docDet = $this->Profile_model->empDocument_setup();
        //echo '<pre>';print_r($docDet); echo '</pre>'; die();

        $data['docDet'] = $docDet;
        $data['isFromProfile'] = 'Y';
        //$this->load->view('system/hrm/ajax/load_empDocumentProfileView', $data);
        $this->load->view('system/hrm/ajax/load_empDocumentView', $data);
    }

    function fetch_my_employee_list()
    {
        /*$empID = current_userID();
        $data['empArray'] = $this->Employee_model->fetch_my_employee_list($empID);*/
        $this->load->view('system/hrm/ajax/ajax-my_employees_list', '');
    }

    function fetch_bank_details()
    {
        $id = current_userID();
        $data['empID'] = current_userID();
        $data['empDetail'] = $this->db->query("select ECode,Ename2 from srp_employeesdetails where EIdNo={$id} ")->row_array();
        $data['accountDetails'] = $this->Employee_model->loadEmpBankAccount($id);
        $data['accountDetails_nonPayroll'] = $this->Employee_model->loadEmpNonBankAccount($id);
        $this->load->view('system/hrm/ajax/load_empBankView', $data);
    }

    function ajax_update_familydetails()
    {
        /*$result = $this->Employee_model->xeditable_update('srp_erp_family_details', 'empfamilydetailsID');
        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'updated'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'updated Fail'));
        }*/
        echo json_encode($this->Profile_model->update_familydetails());
    }
}
