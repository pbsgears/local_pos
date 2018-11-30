<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 5/2/2016
 * Time: 12:54 PM
 */
class Employee_model extends ERP_Model
{

    function new_employee()
    {
        $emp_title = $this->input->post('emp_title');
        $shortName = $this->input->post('shortName');
        $fullName = $this->input->post('fullName');
        $emp_gender = $this->input->post('emp_gender');
        $date_format_policy = date_format_policy();
        $epDob = $this->input->post('empDob');
        $empDob = input_format_date($epDob, $date_format_policy);
        $religion = $this->input->post('religion');
        $emp_email = $this->input->post('emp_email');
        $EmpSecondaryCode = trim($this->input->post('EmpSecondaryCode'));
        $Nationality = trim($this->input->post('Nationality'));
        $MaritialStatus = trim($this->input->post('MaritialStatus'));
        $BloodGroup = trim($this->input->post('BloodGroup'));
        $initial = trim($this->input->post('initial'));
        $Ename4 = trim($this->input->post('Ename4'));
        $Ename3 = trim($this->input->post('Ename3'));
        $NIC = trim($this->input->post('NIC'));
        $Ename2 = $initial . ' ' . $Ename4;
        $isEmailExist = $this->isExistEmailID($emp_email);
        $companyID = current_companyID();

        if (!empty($isEmailExist)) {
            return ['e', 'Email address already exists'];
            die();
        }

        //Employee System Code Auto Generated Policy
        $isAutoGenerate = getPolicyValues('ECG', 'All');
        $ECode = $EmpSecondaryCode;
        if ($isAutoGenerate == 1) {
            $ECode = empCodeGenerate();
        }

        if (isset($_FILES['empImage']['name']) && !empty($_FILES['empImage']['name'])) {
            $imgData = $this->imageUpload($ECode);
        } else {
            $img = ($emp_gender == 2) ? 'female.png' : 'male.png';
            $imgData = array('s', $img);
        }

        if (isset($_FILES['empSignatureImage']['name']) && !empty($_FILES['empSignatureImage']['name'])) {
            $imgDataSignature = $this->imageUploadSignature($ECode . 'Signature');
        } else {
            $imgDataSignature = array('s', 'no-logo.png');
        }


        if ($imgData[0] != 's' || $imgDataSignature[0] != 's') {
            return ($imgData[0] != 's') ? $imgData : $imgDataSignature;
        } else {
            $data = array(
                'ECode' => $ECode,
                'EmpTitleId' => $emp_title,
                'Ename1' => $fullName,
                'EmpShortCode' => $shortName,
                'Gender' => $emp_gender,
                'EEmail' => $emp_email,
                'EDOB' => $empDob,
                'rid' => $religion,
                'EmpImage' => $imgData[1],
                'empSignature' => $imgDataSignature[1],
                'SchMasterId' => current_schMasterID(),
                'branchID' => current_schBranchID(),
                'Erp_companyID' => $companyID,
                'CreatedPC' => current_pc(),
                'CreatedUserName' => current_employee(),
                'CreatedDate' => current_date(),
                'EmpSecondaryCode' => $EmpSecondaryCode,
                'Nid' => $Nationality,
                'MaritialStatus' => $MaritialStatus,
                'BloodGroup' => $BloodGroup,
                'UserName' => $emp_email,
                'Password' => md5('Welcome@123'),
                'Ename2' => $Ename2,
                'Ename3' => $Ename3,
                'initial' => $initial,
                'Ename4' => $Ename4,
                'NIC' => $NIC
            );


            $this->db->trans_start();


            $this->db->insert('srp_employeesdetails', $data);
            $empID = $this->db->insert_id();

            $data_central = array(
                'empID' => $empID,
                'Username' => $emp_email,
                'Password' => md5('Welcome@123'),
                'companyID' => $companyID,
                'email' => $emp_email,
            );
            $db2 = $this->load->database('db2', TRUE);
            $db2->insert('user', $data_central);

            $this->insert_default_dashboard($empID, $companyID);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Employee Created Successfully.', $empID);
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error In Employee Creating');
            }
        }
    }

    function new_empSave()
    {
        $emp_title = $this->input->post('emp_title');
        $fullName = $this->input->post('fullName');
        $emp_gender = $this->input->post('emp_gender');
        $emp_email = $this->input->post('emp_email');
        $EmpSecondaryCode = trim($this->input->post('EmpSecondaryCode'));
        $initial = trim($this->input->post('initial'));
        $Ename4 = trim($this->input->post('Ename4'));
        $Ename2 = $initial . ' ' . $Ename4;
        $isEmailExist = $this->isExistEmailID($emp_email);
        $companyID = current_companyID();

        if (!empty($isEmailExist)) {
            return ['e', 'Email address already exists'];
            die();
        }

        //Employee System Code Auto Generated Policy
        $isAutoGenerate = getPolicyValues('ECG', 'All');
        $ECode = $EmpSecondaryCode;
        if ($isAutoGenerate == 1) {
            $ECode = empCodeGenerate();
        }

        if (isset($_FILES['empImage']['name']) && !empty($_FILES['empImage']['name'])) {
            $imgData = $this->imageUpload($ECode);
        } else {
            $img = ($emp_gender == 2) ? 'female.png' : 'male.png';
            $imgData = array('s', $img);
        }

        if (isset($_FILES['empSignatureImage']['name']) && !empty($_FILES['empSignatureImage']['name'])) {
            $imgDataSignature = $this->imageUploadSignature($ECode . 'Signature');
        } else {
            $imgDataSignature = array('s', 'no-logo.png');
        }


        if ($imgData[0] != 's' || $imgDataSignature[0] != 's') {
            return ($imgData[0] != 's') ? $imgData : $imgDataSignature;
        } else {
            $data = array(
                'ECode' => $ECode,
                'EmpTitleId' => $emp_title,
                'Ename1' => $fullName,
                'Gender' => $emp_gender,
                'EEmail' => $emp_email,
                'EmpImage' => $imgData[1],
                'empSignature' => $imgDataSignature[1],
                'SchMasterId' => current_schMasterID(),
                'branchID' => current_schBranchID(),
                'Erp_companyID' => $companyID,
                'CreatedPC' => current_pc(),
                'CreatedUserName' => current_employee(),
                'CreatedDate' => current_date(),
                'EmpSecondaryCode' => $EmpSecondaryCode,
                'UserName' => $emp_email,
                'Password' => md5('Welcome@123'),
                'Ename2' => $Ename2,
                'initial' => $initial,
                'Ename4' => $Ename4,
            );


            $this->db->trans_start();


            $this->db->insert('srp_employeesdetails', $data);
            $empID = $this->db->insert_id();

            $data_central = array(
                'empID' => $empID,
                'Username' => $emp_email,
                'Password' => md5('Welcome@123'),
                'companyID' => $companyID,
                'email' => $emp_email,
            );
            $db2 = $this->load->database('db2', TRUE);
            $db2->insert('user', $data_central);

            $this->insert_default_dashboard($empID, $companyID);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Employee Created Successfully.', $empID);
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error In Employee Creating');
            }
        }
    }

    function imageUpload($ECode)
    {

        $defaultImagePath = getCompanyImagePath();
        $path = UPLOAD_PATH . $defaultImagePath['imagePath'];
        $fileName = str_replace(' ', '', strtolower($ECode)) . '_' . time();
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|png|jpg|jpeg';
        $config['max_size'] = '200000';
        $config['file_name'] = $fileName;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        //empImage is  => $_FILES['empImage']['name'];

        if (!$this->upload->do_upload("empImage")) {
            return array('e', 'Employee image upload failed ' . $this->upload->display_errors());
        } else {
            return array('s', $this->upload->data('file_name'));
        }

        exit;
        if ($defaultImagePath['isLocalPath'] == 1) {
            $path = UPLOAD_PATH . $defaultImagePath['imagePath'];
            $fileName = str_replace(' ', '', strtolower($ECode)) . '_' . time();
            $config['upload_path'] = $path;
            $config['allowed_types'] = 'gif|png|jpg|jpeg';
            $config['max_size'] = '200000';
            $config['file_name'] = $fileName;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            //empImage is  => $_FILES['empImage']['name'];

            if (!$this->upload->do_upload("empImage")) {
                return array('e', 'Upload failed ' . $this->upload->display_errors());
            } else {
                return array('s', $this->upload->data('file_name'));
            }
        } else {
            return array('e', 'Default Image path not set');
        }

    }

    function imageUploadSignature($ECode)
    {
        $defaultImagePath = getCompanyImagePath();
        $path = UPLOAD_PATH . $defaultImagePath['imagePath'];
        $fileName = str_replace(' ', '', strtolower($ECode)) . '_' . time();
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|png|jpg|jpeg';
        $config['max_size'] = '200000';
        $config['file_name'] = $fileName;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        //empImage is  => $_FILES['empImage']['name'];

        if (!$this->upload->do_upload("empSignatureImage")) {
            return array('e', 'Signature upload failed ' . $this->upload->display_errors());
        } else {
            return array('s', $this->upload->data('file_name'));
        }

        exit;
        if ($defaultImagePath['isLocalPath'] == 1) {
            $path = UPLOAD_PATH . $defaultImagePath['imagePath'];
            $fileName = str_replace(' ', '', strtolower($ECode)) . '_' . time();
            $config['upload_path'] = $path;
            $config['allowed_types'] = 'png|jpg|jpeg';
            $config['max_size'] = '200000';
            $config['file_name'] = $fileName;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            //empImage is  => $_FILES['empImage']['name'];

            if (!$this->upload->do_upload("empImage")) {
                return array('e', 'Upload failed ' . $this->upload->display_errors());
            } else {
                return array('s', $this->upload->data('file_name'));
            }
        } else {
            return array('e', 'Default Image path not set');
        }

    }

    function insert_default_dashboard($empID, $companyID)
    {
        $getDashboard = $this->db->query("select * from srp_erp_systemuserdashboardmaster")->result_array();
        foreach ($getDashboard as $val) {
            $userDashboardWidgetID = $val['userDashboardID'];
            $dashborddata['employeeID'] = $empID;
            $dashborddata['dashboardDescription'] = $val['dashboardDescription'];
            $dashborddata['templateID'] = $val['templateID'];
            $dashborddata['companyID'] = $companyID;
            $dashborddata['isDefault'] = 1;
            $insertDashBoard = $this->db->insert('srp_erp_userdashboardmaster', $dashborddata);
            $userDashboardID = $this->db->insert_id();
            if ($insertDashBoard) {
                $this->db->query("INSERT INTO srp_erp_userdashboardwidget (userDashboardID,positionID,widgetID,sortOrder,employeeID,companyID  ) select $userDashboardID as userDashboardID, positionID,widgetID,sortOrder, $empID as empID, $companyID as comid from srp_erp_systemuserdashboardwidget where userDashboardID = $userDashboardWidgetID");
            }
        }
    }

    function update_employee()
    {
        //die($this->input->post('confirmDate'));
        $companyID = current_companyID();
        $updateID = $this->input->post('updateID');
        $emp_title = $this->input->post('emp_title');
        $shortName = $this->input->post('shortName');
        $fullName = $this->input->post('fullName');
        $emp_gender = $this->input->post('emp_gender');
        $emp_email = $this->input->post('emp_email');
        $ECode = $this->input->post('empCode');
        $EmpSecondaryCode = trim($this->input->post('EmpSecondaryCode'));
        $Nationality = trim($this->input->post('Nationality'));
        $MaritialStatus = trim($this->input->post('MaritialStatus'));
        $BloodGroup = trim($this->input->post('BloodGroup'));
        $Ename4 = trim($this->input->post('Ename4'));
        $initial = trim($this->input->post('initial'));
        $Ename3 = trim($this->input->post('Ename3'));
        $NIC = trim($this->input->post('NIC'));
        $empDob = trim($this->input->post('empDob'));
        $confirmDate = trim($this->input->post('confirmDate'));
        $religionID = trim($this->input->post('religion'));
        $isConfirmed = $this->input->post('isConfirmed');
        $Ename2 = $initial . ' ' . $Ename4;
        $date_format_policy = date_format_policy();
        $empDob = (!empty($empDob)) ? input_format_date($empDob, $date_format_policy) : null;
        $confirmDate = (!empty($confirmDate)) ? input_format_date($confirmDate, $date_format_policy) : null;

        $isEmailExist = $this->db->query("SELECT EIdNo FROM srp_employeesdetails WHERE EEmail='{$emp_email}'")->row('EIdNo');

        if (!empty($isEmailExist)) {
            if ($isEmailExist != $updateID) {
                return ['e', 'Email address already exists'];
                die();
            }
        }
        /*$isEmailExist = $this->isExistEmailID($emp_email);

        */


        $data = array(
            'EmpTitleId' => $emp_title,
            'Ename1' => $fullName,
            'EmpShortCode' => $shortName,
            'Gender' => $emp_gender,
            'EEmail' => $emp_email,
            'ModifiedPC' => current_pc(),
            'ModifiedUserName' => current_employee(),
            'Nid' => $Nationality,
            'MaritialStatus' => $MaritialStatus,
            'BloodGroup' => (!empty($BloodGroup)) ? $BloodGroup : null,
            'Ename2' => $Ename2,
            'Ename3' => $Ename3,
            'Ename4' => $Ename4,
            'initial' => $initial,
            'EDOB' => $empDob,
            'Rid' => $religionID,
            'NIC' => $NIC
        );

        //Employee System Code Auto Generated Policy
        $isAutoGenerate = getPolicyValues('ECG', 'All');
        if ($isAutoGenerate == 0) {
            $isEmpConfirmed = $this->db->query("SELECT empConfirmedYN FROM srp_employeesdetails WHERE EIdNo='{$updateID}'")->row('empConfirmedYN');
            if ($isEmpConfirmed != 1) {
                $data['ECode'] = $EmpSecondaryCode;
                $data['EmpSecondaryCode'] = $EmpSecondaryCode;
            }
        } else {
            $data['ECode'] = $ECode;
            $data['EmpSecondaryCode'] = $EmpSecondaryCode;
        }


        $msg = 'Employee Details Updated Successfully.';
        if ($isConfirmed == 1) {
            $msg = 'Employee Details confirmed';
            $data['empConfirmedYN'] = 1;
            $data['empConfirmDate'] = $confirmDate;
        }


        if (isset($_FILES['empImage']['name']) && !empty($_FILES['empImage']['name'])) {
            $imgData = $this->imageUpload($ECode);
        } else {
            $imgData = array('s', '');
            $existingImg = $this->db->query("SELECT EmpImage FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                             AND EIdNo={$updateID}")->row('EmpImage');
            if ($existingImg == 'male.png' || $existingImg == 'female.png') {
                $newSystemGeneratedImg = ($emp_gender == 1) ? 'male.png' : 'female.png';
                $imgData = array('s', $newSystemGeneratedImg);
            }


        }

        if (isset($_FILES['empSignatureImage']['name']) && !empty($_FILES['empSignatureImage']['name'])) {
            $imgDataSignature = $this->imageUploadSignature($ECode . 'Signature');
        } else {
            $imgDataSignature = array('s', '');
        }


        if ($imgData[0] != 's' || $imgDataSignature[0] != 's') {
            return ($imgData[0] != 's') ? $imgData : $imgDataSignature;
        } else {

            if ($imgData[1] != '') {
                $data['EmpImage'] = $imgData[1];
            }

            if ($imgDataSignature[1] != '') {
                $data['empSignature'] = $imgDataSignature[1];
            }

            $this->db->trans_start();

            if ($isConfirmed == 1) {
                /*** Update leave group history in employee confirm ***/
                $leaveGroupID = $this->db->get_where('srp_employeesdetails', ['EidNo' => $updateID])->row('leaveGroupID');
                $changeHistory = array(
                    'empID' => $updateID,
                    'leaveGroupID' => $leaveGroupID,
                    'adjustmentDone' => 2,
                    'companyID' => $companyID,
                    'createdPCID' => current_pc(),
                    'createdUserID' => current_userID(),
                    'createdUserName' => current_employee(),
                    'createdDateTime' => current_date()
                );

                $this->db->insert('srp_erp_leavegroupchangehistory', $changeHistory);
            }

            $this->db->where(['EidNo' => $updateID, 'Erp_companyID' => $companyID])->update('srp_employeesdetails', $data);

            $data_central = array(
                'email' => $emp_email,
            );
            $db2 = $this->load->database('db2', TRUE);
            $db2->where('empID', $updateID)->where('companyID', $companyID)->update('user', $data_central);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', $msg, $updateID);
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error In Employee Details Updating');
            }
        }
    }

    function isExistEmailID($emp_email)
    {
        $db2 = $this->load->database('db2', TRUE);
        return $db2->query("SELECT EIdNo FROM user WHERE Username='{$emp_email}'")->row('EIdNo');
    }

    function setAllDesignationsIsMajor_zero($updateID)
    {
        $upData = array(
            'isMajor' => 0,
            'ModifiedPC' => current_pc(),
            'ModifiedUserName' => current_employee(),
        );
        $this->db->where('EmpID', $updateID)->update('srp_employeedesignation', $upData);
    }

    function employee_details($empID = null)
    {
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $empID = ($empID == null) ? $this->input->post('empID') : $empID;
        $this->db->select('DATE_FORMAT(contractStartDate,\'' . $convertFormat . '\') AS contractStartDate, DATE_FORMAT(contractEndDate,\'' . $convertFormat . '\') AS contractEndDate, contractRefNo, EmployeeConType,ECode, EmpTitleId, EmpDesignationId, Ename1, EmpShortCode, EmpImage,empSignature, Gender, EcMobile, EEmail,DATE_FORMAT(EDOB,\'' . $convertFormat . '\') AS EDOB , EDOJ AS EDOJ_ORG, DATE_FORMAT(EDOJ,\'' . $convertFormat . '\') AS EDOJ , payCurrency, PayCurrencyID, EcTel, EpTelephone, rid, empMachineID, floorID, EpAddress1, EpAddress2, EpAddress3, EpAddress4, EcAddress1, EcAddress2, EcAddress3, EcAddress4, ZipCode, EpFax, EcPC, EcFax, EPassportNO, DATE_FORMAT(EPassportExpiryDate,\'' . $convertFormat . '\') AS EPassportExpiryDate, DATE_FORMAT(EVisaExpiryDate,\'' . $convertFormat . '\') AS EVisaExpiryDate, AirportDestination, segmentID,EmpSecondaryCode,BloodGroup,DATE_FORMAT(DateAssumed,\'' . $convertFormat . '\') AS DateAssumed,MaritialStatus,Nid,Ename3,Ename2,initial,Ename4,NIC,leaveGroupID,isCheckin,EcPOBox,overTimeGroup,isPayrollEmployee,IF(ISNULL(probationPeriod),0,TIMESTAMPDIFF(MONTH, DateAssumed, probationPeriod)) AS probationPeriodMonth,isDischarged, DATE_FORMAT(dischargedDate,\'' . $convertFormat . '\') AS dischargedDate,dischargedComment, CurrencyCode, empConfirmedYN, DATE_FORMAT(empConfirmDate,\'' . $convertFormat . '\') AS empConfirmDate, DATE_FORMAT(lastWorkingDate,\'' . $convertFormat . '\') AS lastWorkingDate, personalEmail, DesDescription, empTB.EIdNo AS thisEmpID, isSystemAdmin,gradeID');
        $this->db->from('srp_employeesdetails empTB');
        $this->db->join('srp_designation designation', 'empTB.EmpDesignationId = designation.DesignationID', 'left');
        $this->db->join('srp_erp_currencymaster AS cur', 'cur.currencyID = empTB.payCurrencyID', 'left');
        $this->db->where("empTB.EIdNo", $empID);
        $this->db->where("empTB.Erp_companyID", $companyID);
        $query = $this->db->get();
        $empData = $query->row();

        $RptManager = $this->db->query("SELECT srp_employeesdetails.Ename1 AS retManager, srp_employeesdetails.EIdNo AS managerId,CONCAT(ECode,'_', Ename1) AS `Match`
                                        FROM srp_erp_employeemanagers
                                        INNER JOIN srp_employeesdetails ON srp_erp_employeemanagers.managerID = srp_employeesdetails.EIdNo
                                        WHERE empID='{$empID}' AND active=1 AND companyID='{$companyID}'")->row_array();

        if (is_null($RptManager)) {
            $RptManager = array('managerId' => null);
        }

        $merge = array_merge((array)$empData, $RptManager);
        return $merge;
    }

    function contactDetails_update()
    {
        $updateID = $this->input->post('updateID');
        $personalEmail = $this->input->post('personalEmail');
        $ep_address1 = $this->input->post('ep_address1');
        $ep_address2 = $this->input->post('ep_address2');
        $ep_address3 = $this->input->post('ep_address3');
        $ep_address4 = $this->input->post('ep_address4');
        $zip_code = $this->input->post('zip_code');
        $ep_fax = $this->input->post('ep_fax');


        $ec_address1 = $this->input->post('ec_address1');
        $ec_address2 = $this->input->post('ec_address2');
        $ec_address3 = $this->input->post('ec_address3');
        $ec_address4 = $this->input->post('ec_address4');
        $ec_po_box = $this->input->post('ec_po_box');
        $ec_pc = $this->input->post('ec_pc');
        $ec_fax = $this->input->post('ec_fax');

        $telNo1 = $this->input->post('telNo1');
        $telNo2 = $this->input->post('telNo2');
        $emp_mobile = $this->input->post('emp_mobile');


        $data = array(
            'EpAddress1' => $ep_address1,
            'EpAddress2' => $ep_address2,
            'EpAddress3' => $ep_address3,
            'EpAddress4' => $ep_address4,
            'ZipCode' => $zip_code,
            'EpFax' => $ep_fax,
            'personalEmail' => $personalEmail,

            'EcAddress1' => $ec_address1,
            'EcAddress2' => $ec_address2,
            'EcAddress3' => $ec_address3,
            'EcAddress4' => $ec_address4,
            'EcPOBox' => $ec_po_box,
            'EcPC' => $ec_pc,
            'EcFax' => $ec_fax,

            'EpTelephone' => $telNo1,
            'EcTel' => $telNo2,
            'EcMobile' => $emp_mobile,

            'ModifiedPC' => current_pc(),
            'ModifiedUserName' => current_employee(),
        );

        $this->db->trans_start();
        $this->db->where('EidNo', $updateID)->update('srp_employeesdetails', $data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error In Employee Contact Details Updating');
        } else {
            $this->db->trans_commit();
            return array('s', 'Employee Contact Details Updated Successfully.');
        }

    }

    function visaDetails_update()
    {
        $updateID = $this->input->post('updateID');
        $pass_portNo = $this->input->post('pass_portNo');
        $passPort_expiryDate = $this->input->post('passPort_expiryDate');
        $visa_expiryDate = $this->input->post('visa_expiryDate');
        $airport_destination = $this->input->post('airport_destination');
        $manPowerNo = $this->input->post('manPowerNo');

        $contractStartDate = $this->input->post('contractStartDate');
        $contractEndDate = $this->input->post('contractEndDate');
        $contractRefNo = $this->input->post('contractRefNo');


        $date_format_policy = date_format_policy();
        $contractStartDate = (!empty($contractStartDate)) ? input_format_date($contractStartDate, $date_format_policy) : null;
        $contractEndDate = (!empty($contractEndDate)) ? input_format_date($contractEndDate, $date_format_policy) : null;
        $passPort_expiryDate = (!empty($passPort_expiryDate)) ? input_format_date($passPort_expiryDate, $date_format_policy) : null;
        $visa_expiryDate = (!empty($visa_expiryDate)) ? input_format_date($visa_expiryDate, $date_format_policy) : null;


        $data = array(
            'EPassportNO' => $pass_portNo,
            'EPassportExpiryDate' => $passPort_expiryDate,
            'EVisaExpiryDate' => $visa_expiryDate,
            'AirportDestination' => $airport_destination,
            'manPowerNo' => $manPowerNo,
            'contractStartDate' => $contractStartDate,
            'contractEndDate' => $contractEndDate,
            'contractRefNo' => $contractRefNo,


            'ModifiedPC' => current_pc(),
            'ModifiedUserName' => current_employee(),
        );

        $this->db->trans_start();
        $this->db->where('EidNo', $updateID)->update('srp_employeesdetails', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() > 0) {
            $this->db->trans_commit();
            return array('s', 'Employee Contract Details Updated Successfully.');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error In Employee Contract Details Updating');
        }
    }

    function visaDetails_update_envoy()
    {

        $empID = $this->input->post('updateID');
        $companyID = current_companyID();
        $data = $this->db->query("SELECT contType.Description, period , EmployeeConType, typeID, contType.Erp_companyID, contData.contractEndDate,
                                  (SELECT COUNT(contractID) FROM srp_erp_empcontracthistory WHERE companyID={$companyID} AND empID={$empID})
                                  AS contactCount
                                  FROM srp_employeesdetails AS empTB
                                  JOIN srp_empcontracttypes AS contType ON contType.EmpContractTypeID = empTB.EmployeeConType
                                  JOIN srp_erp_systememployeetype AS sysType ON sysType.employeeTypeID = contType.typeID
                                  LEFT JOIN(
                                        SELECT empID AS contEmpID, contractEndDate
                                        FROM srp_erp_empcontracthistory WHERE companyID={$companyID} AND empID={$empID} AND isCurrent=1
                                  ) AS contData ON contData.contEmpID=EIdNo
                                  AND contType.Erp_companyID={$companyID} WHERE empTB.Erp_companyID={$companyID} AND EIdNo={$empID}
                                  ")->row_array();

        if ($data['typeID'] != 2) {
            return ['e', 'Employment type is not a contract type'];
            die();
        }

        $contractID = $this->input->post('contractID');
        $isRenew = $this->input->post('isRenew');
        $contractStartDate = $this->input->post('contractStartDate');
        $contractEndDate = $this->input->post('contractEndDate');
        $contractRefNo = $this->input->post('contractRefNo');


        $date_format_policy = date_format_policy();
        $contractStartDate = (!empty($contractStartDate)) ? input_format_date($contractStartDate, $date_format_policy) : null;
        $contractEndDate = (!empty($contractEndDate)) ? input_format_date($contractEndDate, $date_format_policy) : null;

        $lastContractDate = $data['contractEndDate'];
        $period = $data['period'];
        $endDate = date('Y-m-d', strtotime($contractStartDate . ' +' . $period . ' month'));

        if ($contractStartDate > $contractEndDate) {
            return ['e', 'Contract end date should be greater than start date.'];
        }

        if ($contractEndDate > $endDate) {
            return ['e', 'Contract end date should be lesser than ' . $endDate];
        }


        $isPeriodExist = $this->db->query("SELECT contractID FROM srp_erp_empcontracthistory WHERE companyID={$companyID} AND empID={$empID}
                                           AND (
                                              ('$contractStartDate' BETWEEN contractStartDate AND contractEndDate )
                                              OR ('$contractEndDate' BETWEEN contractStartDate AND contractEndDate )
                                              OR ( (contractStartDate > '$contractStartDate') AND (contractEndDate < '$contractEndDate') )
                                           )")->row('contractID');

        //echo '<pre>'; print_r($isPeriodExist); echo '</pre>';die();
        $this->db->trans_start();

        $this->db->where(['empID' => $empID, 'companyID' => $companyID])->update('srp_erp_empcontracthistory', ['isCurrent' => 0]);

        $isRenew = ($data['contactCount'] == 0) ? 1 : $isRenew;

        if ($isRenew == 1) {

            if (!empty($isPeriodExist)) {
                return ['e', 'Contract period is falling with already existing contact dates'];
            }

            $data = array(
                'empID' => $empID,
                'contactTypeID' => $data['EmployeeConType'],
                'contractStartDate' => $contractStartDate,
                'contractEndDate' => $contractEndDate,
                'contractRefNo' => $contractRefNo,
                'companyID' => $companyID,
                'CreatedUserName' => current_employee(),
                'CreatedPC' => current_pc(),
                'CreatedDate' => current_date()
            );

            $this->db->insert('srp_erp_empcontracthistory', $data);
            $contractID = $this->db->insert_id();
        } else {

            if (!empty($isPeriodExist) && $contractID != $isPeriodExist) {
                return ['e', 'Contract period is falling with already existing contact dates'];
            }

            $data = array(
                'isCurrent' => 1,
                'contractStartDate' => $contractStartDate,
                'contractEndDate' => $contractEndDate,
                'contractRefNo' => $contractRefNo,
                'ModifiedUserName' => current_employee(),
                'ModifiedPC' => current_pc()
            );

            $this->db->where(['empID' => $empID, 'companyID' => $companyID, 'contractID' => $contractID])
                ->update('srp_erp_empcontracthistory', $data);

        }

        $this->db->trans_complete();
        if ($this->db->trans_status() > 0) {
            $this->db->trans_commit();
            return array('s', 'Employee Contract Details Updated Successfully.', $contractID);
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error In Employee Contract Details Updating');
        }
    }

    function employee_rejoin()
    {
        $companyID = current_companyID();
        $empID = $this->input->post('rejoinEmpID');

        $reJoinedEmp = $this->db->query("SELECT CONCAT(Ename2, ' - ', EmpSecondaryCode) AS reJoinedEmp  FROM srp_employeesdetails
                                          WHERE Erp_companyID={$companyID} AND previousEmpID={$empID}")->row('reJoinedEmp');

        if (!empty($reJoinedEmp)) {
            return ['e', 'This Employee is already rejoined with following Name.<br/>' . $reJoinedEmp];
        }


        $rejoinDate = $this->input->post('rejoinDate');
        $contactDetails = $this->input->post('contactDetails');
        $familyDetails = $this->input->post('familyDetails');
        $documentDetails = $this->input->post('documentDetails');
        $bankDetails = $this->input->post('bankDetails');
        $qualificationDetails = $this->input->post('qualificationDetails');
        $ssoDetails = $this->input->post('ssoDetails');


        $date_format_policy = date_format_policy();
        $rejoinDate = input_format_date($rejoinDate, $date_format_policy);

        $this->db->select('*');
        $this->db->from('srp_employeesdetails');
        $this->db->where(['Erp_companyID' => $companyID, 'EIdNo' => $empID]);
        $empData = $this->db->get()->row_array();

        $ECode = empCodeGenerate();

        $data = [
            'ECode' => $ECode,
            'EmpTitleId' => $empData['EmpTitleId'],
            'Ename1' => $empData['Ename1'],
            'EmpShortCode' => $empData['EmpShortCode'],
            'Gender' => $empData['Gender'],
            'EEmail' => $empData['EEmail'],
            'EDOB' => $empData['EDOB'],
            'Rid' => $empData['Rid'],
            'EmpImage' => $empData['EmpImage'],
            'empSignature' => $empData['empSignature'],
            'SchMasterId' => $empData['SchMasterId'],
            'branchID' => $empData['branchID'],
            'Erp_companyID' => $empData['Erp_companyID'],
            'CreatedPC' => current_pc(),
            'CreatedUserName' => current_employee(),
            'CreatedDate' => current_date(),
            'EmpSecondaryCode' => $empData['EmpSecondaryCode'],
            'Nid' => $empData['Nid'],
            'MaritialStatus' => $empData['MaritialStatus'],
            'BloodGroup' => $empData['BloodGroup'],
            'UserName' => $empData['UserName'],
            'Password' => md5('Welcome@123'),
            'Ename2' => $empData['Ename2'],
            'Ename3' => $empData['Ename3'],
            'initial' => $empData['initial'],
            'Ename4' => $empData['Ename4'],
            'NIC' => $empData['NIC'],
            'rejoinDate' => $rejoinDate,
            'previousEmpID' => $empID
        ];


        if (!empty($contactDetails)) {
            $data['EpAddress1'] = $empData['EpAddress1'];
            $data['EpAddress2'] = $empData['EpAddress2'];
            $data['EpAddress3'] = $empData['EpAddress3'];
            $data['EpAddress4'] = $empData['EpAddress4'];
            $data['ZipCode'] = $empData['ZipCode'];
            $data['EpFax'] = $empData['EpFax'];


            $data['EcAddress1'] = $empData['EcAddress1'];
            $data['EcAddress2'] = $empData['EcAddress2'];
            $data['EcAddress3'] = $empData['EcAddress3'];
            $data['EcAddress4'] = $empData['EcAddress4'];
            $data['EcPOBox'] = $empData['EcPOBox'];
            $data['EcPC'] = $empData['EcPC'];
            $data['EcFax'] = $empData['EcFax'];
        }


        $this->db->trans_start();

        $this->db->where(['Erp_companyID' => $companyID, 'EIdNo' => $empID])
            ->update('srp_employeesdetails', ['UserName' => $empID . '_' . $empData['UserName'], 'EEmail' => $empID . '' . $empData['EEmail']]);

        $this->db->insert('srp_employeesdetails', $data);
        $empIDNew = $this->db->insert_id();

        if (!empty($familyDetails)) {
            $this->db->select('*');
            $this->db->from('srp_erp_family_details');
            $this->db->where(['empID' => $empID]);
            $oldFamilyData = $this->db->get()->result_array();

            if (!empty($oldFamilyData)) {
                $newFamilyData = [];
                foreach ($oldFamilyData as $key => $familyRow) {
                    $newFamilyData[$key]['empID'] = $empIDNew;
                    $newFamilyData[$key]['name'] = $familyRow['name'];
                    $newFamilyData[$key]['firstName'] = $familyRow['firstName'];
                    $newFamilyData[$key]['surName'] = $familyRow['surName'];
                    $newFamilyData[$key]['nameOfFather'] = $familyRow['nameOfFather'];
                    $newFamilyData[$key]['relationship'] = $familyRow['relationship'];
                    $newFamilyData[$key]['nationality'] = $familyRow['nationality'];
                    $newFamilyData[$key]['DOB'] = $familyRow['DOB'];
                    $newFamilyData[$key]['idNO'] = $familyRow['idNO'];
                    $newFamilyData[$key]['nationalCode'] = $familyRow['nationalCode'];
                    $newFamilyData[$key]['insuranceCategory'] = $familyRow['insuranceCategory'];
                    $newFamilyData[$key]['gender'] = $familyRow['gender'];
                    $newFamilyData[$key]['insuranceCode'] = $familyRow['insuranceCode'];
                    $newFamilyData[$key]['coverFrom'] = $familyRow['coverFrom'];
                    $newFamilyData[$key]['passportNo'] = $familyRow['passportNo'];
                    $newFamilyData[$key]['passportExpiredate'] = $familyRow['passportExpiredate'];
                    $newFamilyData[$key]['VisaNo'] = $familyRow['VisaNo'];
                    $newFamilyData[$key]['VisaexpireDate'] = $familyRow['VisaexpireDate'];
                    $newFamilyData[$key]['image'] = $familyRow['image'];
                    $newFamilyData[$key]['createdUserGroup'] = current_user_group();
                    $newFamilyData[$key]['createdPCid'] = current_pc();
                    $newFamilyData[$key]['createdUserID'] = current_userID();
                }

                $this->db->insert_batch('srp_erp_family_details', $newFamilyData);
            }
        }

        if (!empty($documentDetails)) {
            $this->db->select('DocDesSetID, DocDesID, PersonType, FileName, UploadedDate, AcademicYearID, isSubmitted');
            $this->db->from('srp_documentdescriptionforms');
            $this->db->where(['PersonID' => $empID, 'Erp_companyID' => $companyID, 'PersonType' => 'E']);
            $oldDocumentData = $this->db->get()->result_array();

            if (!empty($oldDocumentData)) {
                $newDocumentData = [];
                foreach ($oldDocumentData as $key => $documentRow) {
                    $newDocumentData[$key]['PersonID'] = $empIDNew;
                    $newDocumentData[$key]['DocDesSetID'] = $documentRow['DocDesSetID'];
                    $newDocumentData[$key]['DocDesID'] = $documentRow['DocDesID'];
                    $newDocumentData[$key]['PersonType'] = 'E';
                    $newDocumentData[$key]['FileName'] = $documentRow['FileName'];
                    $newDocumentData[$key]['UploadedDate'] = $documentRow['UploadedDate'];
                    $newDocumentData[$key]['SchMasterID'] = current_schMasterID();
                    $newDocumentData[$key]['BranchID'] = current_schBranchID();
                    $newDocumentData[$key]['AcademicYearID'] = $documentRow['AcademicYearID'];
                    $newDocumentData[$key]['isSubmitted'] = $documentRow['isSubmitted'];
                    $newDocumentData[$key]['CreatedUserName'] = current_employee();
                    $newDocumentData[$key]['CreatedDate'] = current_date();
                }

                $this->db->insert_batch('srp_documentdescriptionforms', $newDocumentData);
            }
        }

        if (!empty($bankDetails)) {
            $this->db->select('bankID, branchID, accountNo, accountHolderName, toBankPercentage, swiftCode');
            $this->db->from('srp_erp_pay_salaryaccounts');
            $this->db->where(['employeeNo' => $empID, 'companyID' => $companyID, 'isActive' => 1]);
            $oldBankData = $this->db->get()->result_array();

            if (!empty($oldBankData)) {
                $newBankData = [];
                foreach ($oldBankData as $key => $bankRow) {
                    $newBankData[$key]['employeeNo'] = $empIDNew;
                    $newBankData[$key]['bankID'] = $bankRow['bankID'];
                    $newBankData[$key]['branchID'] = $bankRow['branchID'];
                    $newBankData[$key]['accountNo'] = $bankRow['accountNo'];
                    $newBankData[$key]['accountHolderName'] = $bankRow['accountHolderName'];
                    $newBankData[$key]['toBankPercentage'] = $bankRow['toBankPercentage'];
                    $newBankData[$key]['swiftCode'] = $bankRow['swiftCode'];
                    $newBankData[$key]['companyID'] = current_companyID();
                    $newBankData[$key]['companyCode'] = current_companycode();
                    $newBankData[$key]['createdUserGroup'] = current_user_group();
                    $newBankData[$key]['createdUserName'] = current_employee();
                    $newBankData[$key]['createdPCID'] = current_pc();
                    $newBankData[$key]['createdUserID'] = current_userID();
                    $newBankData[$key]['createdDateTime'] = current_date();
                }

                $this->db->insert_batch('srp_erp_pay_salaryaccounts', $newBankData);
            }

            $this->db->select('bankID, branchID, accountNo, accountHolderName, toBankPercentage, swiftCode');
            $this->db->from('srp_erp_non_pay_salaryaccounts');
            $this->db->where(['employeeNo' => $empID, 'companyID' => $companyID, 'isActive' => 1]);
            $oldBankData = $this->db->get()->result_array();

            if (!empty($oldBankData)) {
                $newBankData = [];
                foreach ($oldBankData as $key => $bankRow) {
                    $newBankData[$key]['employeeNo'] = $empIDNew;
                    $newBankData[$key]['bankID'] = $bankRow['bankID'];
                    $newBankData[$key]['branchID'] = $bankRow['branchID'];
                    $newBankData[$key]['accountNo'] = $bankRow['accountNo'];
                    $newBankData[$key]['accountHolderName'] = $bankRow['accountHolderName'];
                    $newBankData[$key]['toBankPercentage'] = $bankRow['toBankPercentage'];
                    $newBankData[$key]['swiftCode'] = $bankRow['swiftCode'];
                    $newBankData[$key]['companyID'] = current_companyID();
                    $newBankData[$key]['companyCode'] = current_companycode();
                    $newBankData[$key]['createdUserGroup'] = current_user_group();
                    $newBankData[$key]['createdUserName'] = current_employee();
                    $newBankData[$key]['createdPCID'] = current_pc();
                    $newBankData[$key]['createdUserID'] = current_userID();
                    $newBankData[$key]['createdDateTime'] = current_date();
                }

                $this->db->insert_batch('srp_erp_non_pay_salaryaccounts', $newBankData);
            }
        }

        if (!empty($qualificationDetails)) {
            $this->db->select('Description, Institution, GPA, AwardedDate');
            $this->db->from('srp_empcertification');
            $this->db->where(['EmpID' => $empID]);
            $oldQualificationData = $this->db->get()->result_array();

            if (!empty($oldQualificationData)) {
                $newQualificationData = [];
                foreach ($oldQualificationData as $key => $qualificationRow) {
                    $newQualificationData[$key]['EmpID'] = $empIDNew;
                    $newQualificationData[$key]['Description'] = $qualificationRow['Description'];
                    $newQualificationData[$key]['Institution'] = $qualificationRow['Institution'];
                    $newQualificationData[$key]['GPA'] = $qualificationRow['GPA'];
                    $newQualificationData[$key]['AwardedDate'] = $qualificationRow['AwardedDate'];
                    $newQualificationData[$key]['CreatedUserName'] = current_employee();
                    $newQualificationData[$key]['CreatedPC'] = current_pc();
                    $newQualificationData[$key]['CreatedDate'] = current_date();
                }

                $this->db->insert_batch('srp_empcertification', $newQualificationData);
            }
        }

        if (!empty($ssoDetails)) {
            $this->db->select('socialInsuranceMasterID, payeeID, socialInsuranceNumber');
            $this->db->from('srp_erp_socialinsurancedetails');
            $this->db->where(['empID' => $empID, 'companyID' => $companyID]);
            $oldSSOData = $this->db->get()->result_array();

            if (!empty($oldSSOData)) {
                $newSSOData = [];
                foreach ($oldSSOData as $key => $SSORow) {
                    $newSSOData[$key]['empID'] = $empIDNew;
                    $newSSOData[$key]['socialInsuranceMasterID'] = $SSORow['socialInsuranceMasterID'];
                    $newSSOData[$key]['payeeID'] = $SSORow['payeeID'];
                    $newSSOData[$key]['socialInsuranceNumber'] = $SSORow['socialInsuranceNumber'];
                    $newSSOData[$key]['companyID'] = current_companyID();
                    $newSSOData[$key]['companyCode'] = current_companycode();
                    $newSSOData[$key]['createdUserGroup'] = current_user_group();
                    $newSSOData[$key]['createdUserName'] = current_employee();
                    $newSSOData[$key]['createdPCID'] = current_pc();
                    $newSSOData[$key]['createdUserID'] = current_userID();
                    $newSSOData[$key]['createdDateTime'] = current_date();
                }

                $this->db->insert_batch('srp_erp_socialinsurancedetails', $newSSOData);
            }
        }

        $data_central = array(
            'empID' => $empIDNew,
            'Password' => md5('Welcome@123')
        );
        $db2 = $this->load->database('db2', TRUE);
        $db2->where(['companyID' => $companyID, 'empID' => $empID])->update('user', $data_central);

        $this->insert_default_dashboard($empIDNew, $companyID);

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Employee rejoining successfully processed.', $empIDNew);
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in employee rejoining process');
        }
    }

    /*Start of Employee Employment */
    function save_employmentData()
    {
        $empID = $this->input->get('empID');
        $empDoj = $this->input->post('empDoj');
        $dateAssumed = $this->input->post('dateAssumed');
        $employeeConType = $this->input->post('employeeConType');
        $payCurrencyID = $this->input->post('empCurrency');
        $empSegment = $this->input->post('empSegment');
        $isPayrollEmployee = $this->input->post('isPayrollEmployee');
        $probationPeriod = $this->input->post('probationPeriod');
        $manPowerNo = $this->input->post('manPowerNo');
        $gradeID = $this->input->post('gradeID');
        $pos_barCode = $this->input->post('pos_barCode');


        $companyID = current_companyID();
        $date_format_policy = date_format_policy();

        $empDoj = (!empty($empDoj)) ? input_format_date($empDoj, $date_format_policy) : null;
        $dateAssumed = (!empty($dateAssumed)) ? input_format_date($dateAssumed, $date_format_policy) : null;


        if (!empty($dateAssumed)) {
            $dateAssumed = input_format_date($dateAssumed, $date_format_policy);

            if (!empty($probationPeriod)) {
                $probationPeriod = date('Y-m-d', strtotime($dateAssumed . '+' . $probationPeriod . ' month'));
            }
        } else {
            $dateAssumed = null;
        }

        if (!empty($dateAssumed) && !empty($empDoj)) {
            if ($empDoj > $dateAssumed) {
                return ['e', 'Date of assumed cannot be greater than the date of join'];
            }
        }

        $minSalaryDeclaredDate = $this->db->query("SELECT MIN(effectiveDate) AS effDate FROM srp_erp_salarydeclarationdetails
                                                   WHERE companyID={$companyID} AND employeeNo={$empID}")->row('effDate');

        if (!empty($minSalaryDeclaredDate)) {
            $salaryDeclared = date('Y-m-d', strtotime($minSalaryDeclaredDate));
            if ($salaryDeclared < $empDoj) {
                $salaryDeclared = convert_date_format($salaryDeclared);
                return ['e', 'Date of join cannot be exceeded than the salary effective date [ ' . $salaryDeclared . ' ]'];
            }

            if ($salaryDeclared < $dateAssumed) {
                $salaryDeclared = convert_date_format($salaryDeclared);
                return ['e', 'Date of assumed cannot be exceeded than the salary effective date [ ' . $salaryDeclared . ' ]'];

            }
        }


        $currencyCode = $this->db->query("SELECT CurrencyCode FROM srp_erp_currencymaster WHERE currencyID= '{$payCurrencyID}'")->row('CurrencyCode');

        $data = array(
            'payCurrencyID' => $payCurrencyID,
            'payCurrency' => $currencyCode,
            'segmentID' => $empSegment,
            'probationPeriod' => $probationPeriod,
            'isPayrollEmployee' => $isPayrollEmployee,
            'EmployeeConType' => $employeeConType,
            'manPowerNo' => $manPowerNo,
            'gradeID' => $gradeID,
            'pos_barCode' => $pos_barCode,
            'EDOJ' => $empDoj,
            'DateAssumed' => $dateAssumed
        );


        $where = array('EIdNo' => $empID, 'Erp_companyID' => $companyID);

        $this->db->trans_start();

        $this->db->where($where)->update('srp_employeesdetails', $data);


        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Employment details updated successfully.');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in employment details update process.');
        }


    }

    function save_employmentData_envoy()
    {
        $empID = $this->input->get('empID');
        $empDoj = $this->input->post('empDoj');
        $dateAssumed = $this->input->post('dateAssumed');
        $employeeConType = $this->input->post('employeeConType');
        $payCurrencyID = $this->input->post('empCurrency');
        $empSegment = $this->input->post('empSegment');
        $isPayrollEmployee = $this->input->post('isPayrollEmployee');
        $probationPeriod = $this->input->post('probationPeriod');
        $manPowerNo = $this->input->post('manPowerNo');
        $gradeID = $this->input->post('gradeID');
        $pos_barCode = $this->input->post('pos_barCode');


        $pass_portNo = $this->input->post('pass_portNo');
        $passPort_expiryDate = $this->input->post('passPort_expiryDate');
        $visa_expiryDate = $this->input->post('visa_expiryDate');
        $airport_destination = $this->input->post('airport_destination');

        $companyID = current_companyID();

        $date_format_policy = date_format_policy();

        $empDoj = (!empty($empDoj)) ? input_format_date($empDoj, $date_format_policy) : null;
        $dateAssumed = (!empty($dateAssumed)) ? input_format_date($dateAssumed, $date_format_policy) : null;
        $probationPeriod = (!empty($probationPeriod)) ? input_format_date($probationPeriod, $date_format_policy) : null;
        $passPort_expiryDate = (!empty($passPort_expiryDate)) ? input_format_date($passPort_expiryDate, $date_format_policy) : null;
        $visa_expiryDate = (!empty($visa_expiryDate)) ? input_format_date($visa_expiryDate, $date_format_policy) : null;

        if (!empty($dateAssumed)) {
            $dateAssumed = input_format_date($dateAssumed, $date_format_policy);

            /*if (!empty($probationPeriod)) {
                $probationPeriod = date('Y-m-d', strtotime($dateAssumed . '+' . $probationPeriod . ' month'));
            }*/
        } else {
            $dateAssumed = null;
        }

        $minSalaryDeclaredDate = $this->db->query("SELECT MIN(effectiveDate) AS effDate FROM srp_erp_salarydeclarationdetails
                                                   WHERE companyID={$companyID} AND employeeNo={$empID}")->row('effDate');

        if (!empty($minSalaryDeclaredDate)) {
            $salaryDeclared = date('Y-m-d', strtotime($minSalaryDeclaredDate));
            if ($salaryDeclared < $empDoj) {
                $salaryDeclared = convert_date_format($salaryDeclared);
                return ['e', 'Date of join cannot be exceeded than the salary effective date [ ' . $salaryDeclared . ' ]'];
            }

            if ($salaryDeclared < $dateAssumed) {
                $salaryDeclared = convert_date_format($salaryDeclared);
                return ['e', 'Date of assumed cannot be exceeded than the salary effective date [ ' . $salaryDeclared . ' ]'];

            }
        }


        $currencyCode = $this->db->query("SELECT CurrencyCode FROM srp_erp_currencymaster WHERE currencyID= '{$payCurrencyID}'")->row('CurrencyCode');

        $data = array(
            'manPowerNo' => $manPowerNo,
            'gradeID' => $gradeID,
            'pos_barCode' => $pos_barCode,
            'payCurrencyID' => $payCurrencyID,
            'payCurrency' => $currencyCode,
            'segmentID' => $empSegment,
            'probationPeriod' => $probationPeriod,
            'isPayrollEmployee' => $isPayrollEmployee,
            'EmployeeConType' => $employeeConType,
            'EPassportNO' => $pass_portNo,
            'AirportDestination' => $airport_destination,
            'EPassportExpiryDate' => $passPort_expiryDate,
            'EVisaExpiryDate' => $visa_expiryDate,
            'EDOJ' => $empDoj,
            'DateAssumed' => $dateAssumed
        );

        $where = array('EIdNo' => $empID, 'Erp_companyID' => $companyID);

        $this->db->trans_start();

        $this->db->where($where)->update('srp_employeesdetails', $data);


        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            $data = employee_details($empID);
            return ['s', 'Employment details updated successfully.', 'record' => $data];
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in employment details update process.');
        }


    }

    function save_reportingManager()
    {
        $empID = $this->input->post('empID');
        $managerID = $this->input->post('managerID');
        $companyID = current_companyID();
        $current_date = current_date();
        $userID = current_userID();

        $data = array(
            'empID' => $empID,
            'managerID' => $managerID,
            'active' => 1,
            'companyID' => $companyID,
            'createdUserID' => $userID,
            'createdDate' => $current_date
        );

        $updateData = array(
            'active' => 0,
            'companyID' => $companyID,
            'modifiedUserID' => $userID,
            'modifiedDate' => $current_date
        );


        $this->db->trans_start();

        $this->db->where('empID', $empID)
            ->where('companyID', $companyID)
            ->where('active', 1)
            ->update('srp_erp_employeemanagers', $updateData);

        $this->db->insert('srp_erp_employeemanagers', $data);


        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            $record = employee_details($empID);
            return array('s', 'Reporting manager inserted successfully', 'record' => $record);
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in process');
        }

    }

    /*Start of Employee Designation */

    function new_empTitle()
    {
        $title = trim($this->input->post('title'));
        $companyID = current_companyID();
        $isExist = $this->db->query("SELECT TitleID FROM srp_titlemaster WHERE Erp_companyID={$companyID} AND TitleDescription='$title' ")->row('TitleID');

        if (isset($isExist)) {
            return array('e', 'This title is already Exists');
        } else {

            $data = array(
                'TitleDescription' => $title,
                'SchMasterId' => current_schMasterID(),
                'branchID' => current_schBranchID(),
                'Erp_companyID' => current_companyID(),
                'CreatedPC' => current_pc(),
                'CreatedUserName' => current_employee(),
                'CreatedDate' => current_date()
            );

            $this->db->insert('srp_titlemaster', $data);
            if ($this->db->affected_rows() > 0) {
                $titleID = $this->db->insert_id();
                return array('s', 'Title is created successfully.', $titleID);
            } else {
                return array('e', 'Error in title Creating');
            }
        }

    }

    function save_empDesignations()
    {
        $empID = $this->input->post('empID');
        $designationID = $this->input->post('designationID');
        $startDate = $this->input->post('startDate');
        $endDate = $this->input->post('endDate');
        $isMajor = $this->input->post('isMajor');
        $date_format_policy = date_format_policy();
        $startDateRow = input_format_date($startDate, $date_format_policy);
        $endDateRow = (!empty($endDate)) ? input_format_date($endDate, $date_format_policy) : null;
        $companyID = current_companyID();
        $isMajorValue = ($isMajor == 1) ? 1 : 0;

        $this->db->trans_start();
        $isFirstEntry = $this->db->query("SELECT EmpDesignationID FROM srp_employeedesignation WHERE Erp_companyID={$companyID}
                                          AND EmpID={$empID} AND isActive=1")->row('EmpDesignationID');

        if (empty($isFirstEntry)) {
            $isMajorValue = 1;
            $isMajor = 1;
        }

        if (!empty($isMajor)) {
            $this->db->query("UPDATE srp_employeedesignation SET isMajor=0 WHERE EmpID={$empID} AND Erp_companyID={$companyID}");
            $this->db->query("UPDATE srp_employeesdetails SET EmpDesignationId={$designationID} WHERE EIdNo={$empID} AND Erp_companyID={$companyID}");
        }

        $data = array();
        $data['DesignationID'] = $designationID;
        $data['EmpID'] = $empID;
        $data['startDate'] = $startDateRow;
        $data['endDate'] = $endDateRow;
        $data['isMajor'] = $isMajorValue;
        $data['SchMasterId'] = current_schMasterID();
        $data['BranchID'] = current_schBranchID();
        $data['Erp_companyID'] = current_companyID();
        $data['CreatedPC'] = current_pc();
        $data['CreatedUserName'] = current_employee();
        $data['CreatedDate'] = current_date();


        $this->db->insert('srp_employeedesignation', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Records inserted successfully');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in insert record ');
        }

    }

    function edit_empDesignations()
    {
        $empID = $this->input->post('empID');
        $designationID = $this->input->post('designationID-hidden');
        $startDate = $this->input->post('edit_startDate');
        $endDate = $this->input->post('edit_endDate');
        $date_format_policy = date_format_policy();


        $this->db->trans_start();

        $startDateRow = input_format_date($startDate, $date_format_policy);
        $endDateRow = (!empty($endDate)) ? input_format_date($endDate, $date_format_policy) : null;

        $data = array(
            'startDate' => $startDateRow,
            'endDate' => $endDateRow,
            'ModifiedUserName' => current_employee(),
            'ModifiedPC' => current_pc()
        );


        $where = array(
            'Erp_companyID' => current_companyID(),
            'EmpID' => $empID,
            'EmpDesignationID' => $designationID
        );

        $this->db->where($where)->update('srp_employeedesignation', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Records updated successfully');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in process');
        }

    }

    function delete_empDesignation()
    {
        $hidden_id = $this->input->post('hidden-id');

        $data = $this->db->query("SELECT isMajor, isActive FROM srp_employeedesignation WHERE EmpDesignationID={$hidden_id}")->row_array();
        $isMajor = $data['isMajor'];
        $isActive = $data['isActive'];

        if (!empty($isMajor)) {
            return array('e', 'This designation is the major designation of this employee</br>You can not delete this');
        } else if (!empty($isActive)) {
            return array('e', 'This designation is active.</br>You can not delete this');
        } else {
            $this->db->where('EmpDesignationID', $hidden_id)->delete('srp_employeedesignation');
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Record deleted successfully');
            } else {
                return array('e', 'Error in deleting process');
            }
        }
    }

    function changeEmpMajorDesignation()
    {
        $hidden_id = $this->input->post('hidden-id');
        $empID = $this->input->post('empID');
        $companyID = current_companyID();

        $this->db->trans_start();

        $this->db->query("UPDATE srp_employeedesignation SET isMajor=0 WHERE EmpID={$empID} AND Erp_companyID={$companyID}");
        $this->db->query("UPDATE srp_employeedesignation SET isMajor=1 WHERE EmpID={$empID} AND DesignationID={$hidden_id} AND Erp_companyID={$companyID}");
        $this->db->query("UPDATE srp_employeesdetails SET EmpDesignationId={$hidden_id} WHERE EIdNo={$empID} AND Erp_companyID={$companyID}");

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Updated successfully');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in process');
        }

    }

    function changeActiveDesignation()
    {
        $hidden_id = $this->input->post('hidden-id');
        $empID = $this->input->post('empID');
        $status = $this->input->post('status');
        $companyID = current_companyID();

        $isMajor = $this->db->query("SELECT EmpDesignationID FROM srp_employeedesignation
                                     WHERE EmpDesignationID={$hidden_id} AND Erp_companyID={$companyID} AND isMajor=1")->row('EmpDesignationID');

        if (!empty($isMajor)) {
            return ['e', 'This is major designation of the employee.<br/>You can not de active this'];
            die();
        }

        $this->db->trans_start();

        $this->db->query("UPDATE srp_employeedesignation SET isActive={$status} WHERE EmpID={$empID} AND EmpDesignationID={$hidden_id} AND Erp_companyID={$companyID}");

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Updated successfully ');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in process');
        }

    }

    /*Start of Religion */
    function saveReligion()
    {
        $description = $this->input->post('description[]');

        $data = array();
        foreach ($description as $key => $de) {
            $data[$key]['Religion'] = $de;
            $data[$key]['SchMasterId'] = current_schMasterID();
            $data[$key]['branchID'] = current_schBranchID();
            $data[$key]['Erp_companyID'] = current_companyID();
            $data[$key]['CreatedPC'] = current_pc();
            $data[$key]['CreatedUserName'] = current_employee();
            $data[$key]['CreatedDate'] = current_date();
        }

        $this->db->insert_batch('srp_religion', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records inserted successfully');
        } else {
            return array('e', 'Error in insert record');
        }
    }

    function editReligion()
    {
        $description = $this->input->post('religionDes');
        $hidden_id = $this->input->post('hidden-id');

        $data = array(
            'Religion' => $description,
            'ModifiedPC' => current_pc(),
            'ModifiedUserName' => current_employee(),
        );

        $this->db->where('RId', $hidden_id)->update('srp_religion', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }
    }
    /*End of Religion */


    /*Start of Country */

    function deleteReligion()
    {
        $hidden_id = $this->input->post('hidden-id');

        $isInUse = $this->db->query("SELECT Rid FROM srp_employeesdetails WHERE Rid={$hidden_id}")->row('Rid');

        if (isset($isInUse)) {
            return array('e', 'This Religion is in use</br>You can not delete this');
        } else {
            $this->db->where('RId', $hidden_id)->delete('srp_religion');
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Records deleted successfully');
            } else {
                return array('e', 'Error in deleting process');
            }
        }
    }

    function fetch_allCountry()
    {
        $companyID = current_companyID();
        return $this->db->query("SELECT countryID, countryShortCode, CountryDes FROM srp_erp_countrymaster t1
                                         WHERE NOT EXISTS ( SELECT countryID FROM srp_countrymaster WHERE countryMasterID = t1.countryID
                                          AND Erp_companyID ={$companyID} )
                                         ")->result_array();
    }

    function saveCountry()
    {

        $country = $this->input->post('country');
        $country = json_decode($country);

        $data = array();
        foreach ($country as $key => $arr) {
            $data[$key]['countryShortCode'] = $arr->code;
            $data[$key]['CountryDes'] = $arr->name;
            $data[$key]['countryMasterID'] = $arr->id;
            $data[$key]['SchMasterId'] = current_schMasterID();
            $data[$key]['branchID'] = current_schBranchID();
            $data[$key]['Erp_companyID'] = current_companyID();
            $data[$key]['CreatedPC'] = current_pc();
            $data[$key]['CreatedUserName'] = current_employee();
            $data[$key]['CreatedDate'] = current_date();
        }

        $this->db->insert_batch('srp_countrymaster', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records inserted successfully');
        } else {
            return array('e', 'Error in insert record');
        }
    }
    /*End of Country */


    /*Start of Designation */

    function deleteCountry()
    {
        $hidden_id = $this->input->post('hidden-id');
        $isInUse = $this->db->query("SELECT EIdNo FROM srp_employeesdetails WHERE EcAddress4={$hidden_id}  OR EpAddress4={$hidden_id} LIMIT 1")->row('EIdNo');

        if (isset($isInUse)) {
            return array('e', 'This country is in use</br>You can not delete this');
        } else {
            $this->db->where('countryID', $hidden_id)->delete('srp_countrymaster');

            if ($this->db->affected_rows() > 0) {
                return array('s', 'Records deleted successfully');
            } else {
                return array('e', 'Error in deleting process');
            }
        }
    }

    function saveDesignation()
    {
        $description = $this->input->post('description[]');
        $companyID = current_companyID();
        $whereIN = "( '" . join("' , '", $description) . "' )";

        $isExist = $this->db->query("SELECT DesDescription FROM `srp_designation` WHERE DesDescription IN " . $whereIN . " AND Erp_companyID={$companyID}")->result_array();

        if (empty($isExist)) {
            $data = array();
            foreach ($description as $key => $de) {
                $data[$key]['DesDescription'] = $de;
                $data[$key]['SchMasterId'] = current_schMasterID();
                $data[$key]['branchID'] = current_schBranchID();
                $data[$key]['Erp_companyID'] = $companyID;
                $data[$key]['isDeleted'] = '0';
                $data[$key]['CreatedPC'] = current_pc();
                $data[$key]['CreatedUserName'] = current_employee();
                $data[$key]['CreatedDate'] = current_date();
            }

            $this->db->insert_batch('srp_designation', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Records inserted successfully');
            } else {
                return array('e', 'Error in insert record');
            }
        } else {
            $existItems = '';
            foreach ($isExist as $row) {
                $existItems .= '</br>' . $row['DesDescription'];
            }
            return array('e', 'Following designations are already Exists ' . $existItems);
        }

    }

    function editDesignation()
    {
        $description = $this->input->post('designationDes');
        $hidden_id = $this->input->post('hidden-id');
        $companyID = current_companyID();

        $isExist = $this->db->query("SELECT DesDescription FROM srp_designation WHERE DesDescription='$description'
                                     AND Erp_companyID={$companyID} AND DesignationID != {$hidden_id}")->row_array();


        if (empty($isExist)) {
            $data = array(
                'DesDescription' => $description,
                'ModifiedPC' => current_pc(),
                'ModifiedUserName' => current_employee(),
            );

            $this->db->where('DesignationID', $hidden_id)->update('srp_designation', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Records updated successfully');
            } else {
                return array('e', 'Error in updating record');
            }
        } else {

            return array('e', 'Another designation is already Exists with this description.');
        }

    }
    /*End of Designation */


    /*Start of Qualification */

    function deleteDesignation()
    {
        $hidden_id = $this->input->post('hidden-id');

        $data = array(
            'isDeleted' => '1',
            'ModifiedPC' => current_pc(),
            'ModifiedUserName' => current_employee(),
        );

        $this->db->where('DesignationID', $hidden_id)->update('srp_designation', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records deleted successfully');
        } else {
            return array('e', 'Error in deleting process');
        }
    }

    function saveQualification()
    {
        $empID = $this->input->post('empID');
        $certification = $this->input->post('certification');
        $institution = $this->input->post('institution');
        $awardedDate = $this->input->post('awardedDate');
        $GPA = $this->input->post('GPA');
        $isFrom = $this->input->post('isFrom');
        $hrVerified = $this->input->post('isVerified');
        $date_format_policy = date_format_policy();
        $awardedDate = (!empty($awardedDate)) ? input_format_date($awardedDate, $date_format_policy) : null;

        $data = array(
            'EmpID' => $empID,
            'Description' => $certification,
            'Institution' => $institution,
            'GPA' => $GPA,
            'AwardedDate' => $awardedDate,
            'CreatedPC' => current_pc(),
            'CreatedUserName' => current_employee(),
            'CreatedDate' => current_date()
        );

        if ($isFrom != 'profile') {
            $data['hrVerified'] = $hrVerified;
        }

        $this->db->insert('srp_empcertification', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records inserted successfully');
        } else {
            return array('e', 'Error in insert record');
        }
    }

    function editQualification()
    {
        $hidden_id = $this->input->post('hidden-id');
        $certification = $this->input->post('certification');
        $institution = $this->input->post('institution');
        $awardedDate = $this->input->post('awardedDate');
        $GPA = $this->input->post('GPA');
        $isFrom = $this->input->post('isFrom');
        $hrVerified = $this->input->post('isVerified');

        $date_format_policy = date_format_policy();
        $awardedDate = (!empty($awardedDate)) ? input_format_date($awardedDate, $date_format_policy) : null;

        $data = array(
            'Description' => $certification,
            'Institution' => $institution,
            'GPA' => $GPA,
            'AwardedDate' => $awardedDate,
            'ModifiedPC' => current_pc(),
            'ModifiedUserName' => current_employee()
        );

        if ($isFrom != 'profile') {
            $isAlreadyVerified = $this->db->query("SELECT hrVerified FROM srp_empcertification
                                                   WHERE certificateID={$hidden_id} ")->row('hrVerified');
            if ($isAlreadyVerified != $hrVerified) {
                $data['hrVerified'] = ($hrVerified == 1) ? 1 : 0;
            }
        }


        $this->db->where('certificateID', $hidden_id)->update('srp_empcertification', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }

    }
    /*End of Qualification */


    /*Start of Document Setups */

    function deleteQualification()
    {
        $hidden_id = $this->input->post('hidden-id');


        $this->db->where('certificateID', $hidden_id)->delete('srp_empcertification');
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records deleted successfully');
        } else {
            return array('e', 'Error in deleting process');
        }
    }

    public function save_documentDescriptions()
    {
        $description = $this->input->post('description[]');
        $sortOrder = $this->input->post('sortOrder[]');
        $isRequired = $this->input->post('isRequired[]');
        $chk_issueDate = $this->input->post('is_issueDate[]');
        $chk_expireDate = $this->input->post('is_expireDate[]');
        $chk_issuedBy = $this->input->post('is_issuedBy[]');

        $whereIN = "( '" . join("' , '", $description) . "' )";


        $isExist = $this->db->query("SELECT DocDescription FROM srp_documentdescriptionmaster WHERE DocDescription IN " . $whereIN . "
                                     AND Erp_companyID=" . current_companyID())->result_array();


        if (empty($isExist)) {

            $this->db->trans_start();

            foreach ($description as $key => $des) {
                $data = array(
                    'DocDescription' => $des,
                    'SortOrder' => $sortOrder[$key],
                    'SchMasterId' => current_schMasterID(),
                    'branchID' => current_schBranchID(),
                    'Erp_companyID' => current_companyID(),
                    'CreatedPC' => current_pc(),
                    'createdUserID' => current_userID(),
                    'CreatedUserName' => current_employee(),
                    'CreatedDate' => current_date()
                );

                $this->db->insert('srp_documentdescriptionmaster', $data);
                $docID = $this->db->insert_id();

                if (!empty($isRequired)) {
                    $thisRequired = (array_key_exists($key, $isRequired)) ? $isRequired[$key] : 0;
                } else {
                    $thisRequired = 0;
                }

                $data_setup = array(
                    'DocDesID' => $docID,
                    'FormType' => 'EMP',
                    'isMandatory' => $thisRequired,
                    'issueDate_req' => $chk_issueDate[$key],
                    'expireDate_req' => $chk_expireDate[$key],
                    'issuedBy_req' => $chk_issuedBy[$key],
                    'SortOrder' => $sortOrder[$key],
                    'SchMasterId' => current_schMasterID(),
                    'BranchID' => current_schBranchID(),
                    'Erp_companyID' => current_companyID(),
                    'CreatedPC' => current_pc(),
                    'createdUserID' => current_userID(),
                    'CreatedUserName' => current_employee(),
                    'CreatedDate' => current_date()
                );


                $this->db->insert('srp_documentdescriptionsetup', $data_setup);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Created Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in process');
            }
        } else {
            $existItems = '';
            foreach ($isExist as $row) {
                $existItems .= '</br>' . $row['DocDescription'];
            }
            return array('e', 'Following designations are already Exists ' . $existItems);
        }

    }

    function edit_documentDescription()
    {
        $description = $this->input->post('edit_description');
        $sortOrder = $this->input->post('edit_sortOrder');
        $isMandatory = ($this->input->post('edit_isMandatory') == 'on') ? 1 : 0;
        $is_issueDate = ($this->input->post('edit_issueDate') == 'on') ? 1 : 0;
        $is_expireDate = ($this->input->post('edit_expireDate') == 'on') ? 1 : 0;
        $is_issuedBy = ($this->input->post('edit_issuedBy') == 'on') ? 1 : 0;
        $docID = $this->input->post('hidden-id');


        $isExist = $this->db->query("SELECT DocDescription FROM srp_documentdescriptionmaster WHERE DocDescription='$description'
                                     AND DocDesID!={$docID} AND Erp_companyID=" . current_companyID())->result_array();


        if (empty($isExist)) {

            $this->db->trans_start();

            $data = array(
                'DocDescription' => $description,
                'SortOrder' => $sortOrder,
                'CreatedPC' => current_pc(),
                'ModifiedUserName' => current_employee(),
                'createdUserID' => current_userID()
            );

            $this->db->where('DocDesID', $docID)->where('Erp_companyID', current_companyID())
                ->update('srp_documentdescriptionmaster', $data);


            //Update setup table
            unset($data['DocDescription']);
            $data['isMandatory'] = $isMandatory;
            $data['issueDate_req'] = $is_issueDate;
            $data['expireDate_req'] = $is_expireDate;
            $data['issuedBy_req'] = $is_issuedBy;

            $where = [
                'Erp_companyID' => current_companyID(),
                'FormType' => 'EMP',
                'DocDesID' => $docID,
            ];
            $this->db->where($where)->update('srp_documentdescriptionsetup', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Updated Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in update process');
            }
        } else {
            return array('e', $description . ' is already Exists');
        }
    }

    function delete_documentDescription()
    {
        $hidden_id = $this->input->post('hidden-id');

        // Check is there any employee document uploaded
        $isInUse = $this->db->query("SELECT DocDesID FROM srp_documentdescriptionforms WHERE  DocDesID={$hidden_id}
                                     AND Erp_companyID=" . current_companyID())->result_array();


        if (empty($isInUse)) {
            //check this document master used for other types (Parents/Students)
            $isInUse = $this->db->query("SELECT DocDesSetupID FROM srp_documentdescriptionsetup WHERE  DocDesID={$hidden_id}
                                         AND FormType!='EMP'  AND Erp_companyID=" . current_companyID())->result_array();
        }

        if (empty($isInUse)) {
            $this->db->trans_start();

            $this->db->where('DocDesID', $hidden_id)->delete('srp_documentdescriptionmaster');

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Records deleted successfully');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in deleting process');
            }
        } else {
            return array('e', 'This description is in use.');
        }
    }

    public function saveDoc_master()
    {

        $description = $this->input->post('descriptionID[]');
        $isRequired = $this->input->post('isRequired[]');
        $sortOrder = $this->input->post('sortOrder[]');

        $whereIN = "( " . join(" , ", $description) . " )";


        $isExist = $this->db->query("SELECT DocDescription FROM srp_documentdescriptionsetup  t1
                                     JOIN srp_documentdescriptionmaster t2 ON t1.DocDesID=t2.DocDesID
                                     WHERE t1.DocDesID IN " . $whereIN . " AND t1.Erp_companyID=" . current_companyID())->result_array();


        if (empty($isExist)) {

            $this->db->trans_start();

            foreach ($description as $key => $des) {

                if (!empty($isRequired)) {
                    $thisRequired = (array_key_exists($key, $isRequired)) ? $isRequired[$key] : 0;
                } else {
                    $thisRequired = 0;
                }

                $data_setup = array(
                    'DocDesID' => $des,
                    'FormType' => 'EMP',
                    'isMandatory' => $thisRequired,
                    'SortOrder' => $sortOrder[$key],
                    'SchMasterId' => current_schMasterID(),
                    'BranchID' => current_schBranchID(),
                    'Erp_companyID' => current_companyID(),
                    'CreatedPC' => current_pc(),
                    'CreatedUserName' => current_employee(),
                    'CreatedDate' => current_date()
                );


                $this->db->insert('srp_documentdescriptionsetup', $data_setup);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Created Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in process');
            }
        } else {
            $existItems = '';
            foreach ($isExist as $row) {
                $existItems .= '</br>' . $row['DocDescription'];
            }
            return array('e', 'Following designations are already Exists ' . $existItems);
        }

    }

    function delete_DocSetup()
    {
        $hidden_id = $this->input->post('hidden-id');

        $this->db->trans_start();
        $data = array(
            'isDeleted' => '1',
            'ModifiedPC' => current_pc(),
            'ModifiedUserName' => current_employee(),
        );

        $this->db->where('DocDesID', $hidden_id)->update('srp_documentdescriptionmaster', $data);
        $this->db->where('DocDesID', $hidden_id)->delete('srp_documentdescriptionsetup');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Records deleted successfully');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in deleting process');
        }
    }
    /*End of Document Setups*/


    /*Start of Employee Document*/

    function edit_document()
    {
        $descriptionID = $this->input->post('edit_descriptionID');
        $isRequired = $this->input->post('edit_isRequired');
        $sortOrder = $this->input->post('edit_sortOrder');
        $setupID = $this->input->post('hidden-id');


        $isExist = $this->db->query("SELECT DocDesSetupID FROM srp_documentdescriptionsetup WHERE DocDesID='$descriptionID'
                                     AND DocDesSetupID!={$setupID} AND FormType='EMP' AND Erp_companyID=" . current_companyID())->row_array();

        if (empty($isExist)) {

            $this->db->trans_start();

            $data_setup = array(
                'DocDesID' => $descriptionID,
                'isMandatory' => ($isRequired == 1) ? $isRequired : 0,
                'SortOrder' => $sortOrder,
                'ModifiedPC' => current_pc(),
                'ModifiedUserName' => current_employee()
            );


            $setupWhere = array(
                'DocDesSetupID' => $setupID,
                'FormType' => 'EMP'
            );
            $this->db->where($setupWhere)->update('srp_documentdescriptionsetup', $data_setup);


            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Updated Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in update process');
            }
        } else {
            return array('e', 'This document is already Exists');
        }
    }

    function empDocument_setup()
    {
        $companyID = current_companyID();
        $empID = $this->input->post('empID');
        $convertFormat = convert_date_format_sql();

        return $this->db->query("SELECT DocDesFormID, DocDescription, isMandatory, FileName, DATE_FORMAT(issueDate,'{$convertFormat}') AS issueDate,
                                 DATE_FORMAT(expireDate,'{$convertFormat}') AS expireDate, issuedBy, issuedByText, master.DocDesID
                                 FROM srp_documentdescriptionmaster master
                                 JOIN srp_documentdescriptionsetup setup ON setup.DocDesID = master.DocDesID
                                 LEFT JOIN srp_documentdescriptionforms forms ON forms.DocDesID = master.DocDesID
                                 AND PersonType='E' AND PersonID={$empID}
                                 WHERE master.Erp_companyID={$companyID} AND isDeleted=0 ")->result_array();
    }

    function emp_documentSave()
    {
        $companyID = current_companyID();
        $empID = $this->input->post('docEmpID');
        $documentID = $this->input->post('document');
        $issueDate = $this->input->post('issueDate');
        $expireDate = $this->input->post('expireDate');
        $issuedBy = $this->input->post('issuedBy');
        $issuedByText = $this->input->post('issuedByText');
        $issuedByText = ($issuedBy == -1) ? $issuedByText : null;


        $date_format_policy = date_format_policy();

        $issueDate = (!empty($issueDate)) ? input_format_date($issueDate, $date_format_policy) : null;
        $expireDate = (!empty($expireDate)) ? input_format_date($expireDate, $date_format_policy) : null;

        if ($expireDate != null and $issueDate != null) {
            if ($issueDate > $expireDate) {
                return ['e', 'Issue date can not be greater than expire date'];
            }
        }


        //Check is there is a document with this document ID for this employee
        $where = array('DocDesID' => $documentID, 'PersonID' => $empID, 'PersonType' => 'E');
        $isExisting = $this->db->where($where)->select('DocDesID')->from('srp_documentdescriptionforms')->get()->row('DocDesID');

        if (!empty($isExisting)) {
            return ['e', 'This document has been updated already.<br/>Please delete the document and try again.'];
        }


        $path = UPLOAD_PATH_POS . 'documents/users/'; // imagePath();
        $fileName = str_replace(' ', '', strtolower($empID)) . '_' . time();
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
        $config['max_size'] = '200000';
        $config['file_name'] = $fileName;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        //doc_file is  => $_FILES['doc_file']['name'];

        if (!$this->upload->do_upload("doc_file")) {
            return array('e', 'Upload failed ' . $this->upload->display_errors(), 'path' => $path);
        } else {


            //Get document Setup ID
            $setUpID = $this->db->query("SELECT DocDesSetupID FROM srp_documentdescriptionsetup WHERE DocDesID={$documentID}
                                         AND FormType='EMP' AND Erp_companyID={$companyID} ")->row('DocDesSetupID');


            $data = array(
                'DocDesSetID' => $setUpID,
                'DocDesID' => $documentID,
                'PersonID' => $empID,
                'PersonType' => 'E',
                'FileName' => $this->upload->data('file_name'),
                'issueDate' => $issueDate,
                'expireDate' => $expireDate,
                'issuedBy' => $issuedBy,
                'issuedByText' => $issuedByText,
                'SchMasterId' => current_schMasterID(),
                'BranchID' => current_schBranchID(),
                'Erp_companyID' => current_companyID(),
                'CreatedPC' => current_pc(),
                'CreatedUserID' => current_userID(),
                'CreatedUserName' => current_employee(),
                'CreatedDate' => current_date()
            );

            $this->db->insert('srp_documentdescriptionforms', $data);


            if ($this->db->affected_rows() > 0) {
                return array('s', 'Document successfully uploaded');
            } else {
                return array('e', 'Error in document upload');
            }

        }
    }
    /*End of Employee Document*/

    /*Start of Department */

    function delete_empDocument($hiddenID)
    {
        $this->db->where('DocDesFormID', $hiddenID)->delete('srp_documentdescriptionforms');

        if ($this->db->affected_rows() > 0) {
            return array('s', 'Document deleted successfully');
        } else {
            return array('e', 'Error in document delete function');
        }

    }

    function saveDepartment()
    {
        $description = $this->input->post('department[]');

        $data = array();
        foreach ($description as $key => $de) {
            $data[$key]['DepartmentDes'] = $de;
            $data[$key]['SchMasterId'] = current_schMasterID();
            $data[$key]['branchID'] = current_schBranchID();
            $data[$key]['Erp_companyID'] = current_companyID();
            $data[$key]['isActive'] = 1;
            $data[$key]['CreatedPC'] = current_pc();
            $data[$key]['CreatedUserName'] = current_employee();
            $data[$key]['CreatedDate'] = current_date();
        }

        $this->db->insert_batch('srp_departmentmaster', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records inserted successfully');
        } else {
            return array('e', 'Error in insert record');
        }
    }

    function editDepartment()
    {
        $description = $this->input->post('departmentDes');
        $status = $this->input->post('status');
        $hidden_id = $this->input->post('hidden-id');

        $data = array(
            'isActive' => $status,
            'DepartmentDes' => $description,
            'ModifiedPC' => current_pc(),
            'ModifiedUserName' => current_employee(),
        );

        $this->db->where('DepartmentMasterID', $hidden_id)->update('srp_departmentmaster', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }

    }
    /*End of Department */


    /*Start of Employee Floor*/

    function deleteDepartment()
    {
        $hidden_id = $this->input->post('hidden-id');

        $isInUse = $this->db->query("SELECT DepartmentMasterID FROM srp_empdepartments WHERE DepartmentMasterID={$hidden_id}")->row('DepartmentMasterID');

        if (isset($isInUse)) {
            return array('e', 'This department is in use</br>You can not delete this');
        } else {
            $this->db->where('DepartmentMasterID', $hidden_id)->delete('srp_departmentmaster');
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Records deleted successfully');
            } else {
                return array('e', 'Error in deleting process');
            }
        }
    }

    function saveFloor()
    {
        $description = $this->input->post('floor[]');
        $whereIN = "( '" . join("' , '", $description) . "' )";

        $isExist = $this->db->query("SELECT floorDescription FROM srp_erp_pay_floorMaster WHERE floorDescription IN " . $whereIN . "
                                    AND companyID=" . current_companyID())->result_array();

        if (empty($isExist)) {
            $data = array();
            foreach ($description as $key => $row) {
                $data[$key]['floorDescription'] = $row;
                $data[$key]['isActive'] = 1;
                $data[$key]['companyID'] = current_companyID();
                $data[$key]['companyCode'] = current_companyCode();
                $data[$key]['createdPCID'] = current_pc();
                $data[$key]['createdUserID'] = current_userID();
                $data[$key]['createdUserName'] = current_employee();
                $data[$key]['createdUserGroup'] = current_user_group();
                $data[$key]['createdDateTime'] = current_date();
            }


            $this->db->insert_batch('srp_erp_pay_floorMaster', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Data successfully inserted');
            } else {
                return array('e', 'Error in data insertion');
            }
        } else {
            $existItems = '';
            foreach ($isExist as $row) {
                $existItems .= '</br>' . $row['floorDescription'];
            }
            return array('e', 'Following description/s are already Exists ' . $existItems);
        }
    }

    function editFloor()
    {
        $floorDes = $this->input->post('floorDes');
        $status = $this->input->post('status');
        $floorID = $this->input->post('hidden-id');


        $isExist = $this->db->query("SELECT floorDescription FROM srp_erp_pay_floorMaster WHERE floorDescription='$floorDes'
                                     AND floorID!={$floorID} AND companyID=" . current_companyID())->result_array();


        if (empty($isExist)) {
            $data = array(
                'floorDescription' => $floorDes,
                'isActive' => $status,
                'modifiedPCID' => current_pc(),
                'modifiedUserID' => current_userID(),
                'modifiedUserName' => current_employee(),
                'modifiedDateTime' => current_date()
            );

            $this->db->where('floorID', $floorID)->update('srp_erp_pay_floorMaster', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Data successfully updated');
            } else {
                return array('e', 'Error in data updating');
            }
        } else {
            return array('e', $floorDes . ' is already Exists');
        }

    }
    /*End of Employee Floor*/


    /*Start of Employee Department */

    function deleteFloor()
    {
        $floorID = $this->input->post('hidden-id');

        $this->db->where('floorID', $floorID)->delete('srp_erp_pay_floorMaster');

        if ($this->db->affected_rows() > 0) {
            return array('s', 'Data successfully deleted');
        } else {
            return array('e', 'Error in data deleting');
        }
    }

    function save_empDepartments()
    {
        $empID = $this->input->post('empID');
        $items = $this->input->post('items[]');

        $data = array();
        foreach ($items as $key => $de) {
            $data[$key]['DepartmentMasterID'] = $de;
            $data[$key]['EmpID'] = $empID;
            $data[$key]['isActive'] = 1;
            $data[$key]['SchMasterId'] = current_schMasterID();
            $data[$key]['BranchID'] = current_schBranchID();
            $data[$key]['Erp_companyID'] = current_companyID();
            $data[$key]['CreatedPC'] = current_pc();
            $data[$key]['CreatedUserName'] = current_employee();
            $data[$key]['CreatedDate'] = current_date();
        }

        $this->db->insert_batch('srp_empdepartments', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records inserted successfully');
        } else {
            return array('e', 'Error in insert record');
        }

    }

    function delete_empDepartments()
    {
        $hidden_id = $this->input->post('hidden-id');

        $this->db->where('EmpDepartmentID', $hidden_id)->delete('srp_empdepartments');
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Record deleted successfully');
        } else {
            return array('e', 'Error in deleting process');
        }

    }
    /*End of Employee Department */


    /*Start of Employee shift*/

    function statusChangeEmpDepartments()
    {
        $hidden_id = $this->input->post('hidden-id');
        $status = $this->input->post('status');

        $this->db->where('EmpDepartmentID', $hidden_id)->update('srp_empdepartments', array('isActive' => $status));
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Status changed successfully');
        } else {
            return array('e', 'Error in status change process');
        }

    }

    function save_empShift()
    {
        $shiftID = $this->input->post('shiftID');
        $empID = $this->input->post('empID');
        $endDate = $this->input->post('endDate');
        $startDate = $this->input->post('startDate');

        $date_format_policy = date_format_policy();
        $to = date_create();
        date_modify($to, '+100 year');
        $date = date_format($to, 'Y-m-d');
        $startDate = (!empty($startDate)) ? input_format_date($startDate, $date_format_policy) : date('Y-m-d');
        $endDate = (!empty($endDate)) ? input_format_date($endDate, $date_format_policy) : $date;

        $data = array(
            'shiftID' => $shiftID,
            'empID' => $empID,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'companyID' => current_companyID(),
            'companyCode' => current_companyCode(),
            'createdPCID' => current_pc(),
            'createdUserGroup' => current_user_group(),
            'createdUserID' => current_userID(),
            'createdUserName' => current_employee(),
            'createdDateTime' => current_date()
        );

        $this->db->insert('srp_erp_pay_shiftemployees', $data);
        if ($this->db->affected_rows() > 0) {
            $autoID = $this->db->insert_id();
            return array('s', 'Employees successfully assign to the shift.', $autoID);
        } else {
            return array('e', 'Error in employees assigning to the shift.');
        }
    }

    function update_empShift()
    {
        $shiftID = $this->input->post('shiftID');
        $empID = $this->input->post('empID');
        $endDate = $this->input->post('endDate');
        $startDate = $this->input->post('startDate');
        $editID = $this->input->post('editID');
        $date_format_policy = date_format_policy();

        $to = date_create();
        date_modify($to, '+100 year');
        $date = date_format($to, 'Y-m-d');
        $startDate = (!empty($startDate)) ? input_format_date($startDate, $date_format_policy) : date('Y-m-d');
        $endDate = (!empty($endDate)) ? input_format_date($endDate, $date_format_policy) : $date;

        $data = array(
            'shiftID' => $shiftID,
            'empID' => $empID,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_employee(),
            'modifiedDateTime' => current_date()
        );


        $this->db->where('autoID', $editID)->update('srp_erp_pay_shiftemployees', $data);

        if ($this->db->affected_rows() > 0) {
            return array('s', 'Employees shift updated successfully.');
        } else {
            return array('e', 'Error in employees assigning to the shift.');
        }
    }

    function save_attendanceData()
    {
        $empID = $this->input->get('empID');
        $empMachineID = $this->input->post('empMachineID');
        $leaveGroupID = $this->input->post('leaveGroupID');
        $overTimeGroup = $this->input->post('overTimeGroup');
        $floorID = $this->input->post('floorID');
        $isCheckIn = $this->input->post('isCheckIn');
        $isLeaveGroupChangeConfirmed = $this->input->post('isLeaveGroupChangeConfirmed');
        $companyID = current_companyID();

        $data = array(
            'leaveGroupID' => $leaveGroupID,
            'isCheckin' => $isCheckIn,
            'empMachineID' => $empMachineID,
            'floorID' => $floorID,
            'overTimeGroup' => $overTimeGroup,
        );

        $where = array('EIdNo' => $empID, 'Erp_companyID' => $companyID);

        $this->db->trans_start();

        $this->db->where($where)->update('srp_employeesdetails', $data);

        $changeHistoryID = 0;
        if ($isLeaveGroupChangeConfirmed == 1) {
            $changeHistory = array(
                'empID' => $empID,
                'leaveGroupID' => $leaveGroupID,
                'companyID' => $companyID,
                'createdPCID' => current_pc(),
                'createdUserID' => current_userID(),
                'createdUserName' => current_employee(),
                'createdDateTime' => current_date()
            );

            $this->db->insert('srp_erp_leavegroupchangehistory', $changeHistory);
            $changeHistoryID = $this->db->insert_id();
        }


        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Attendance details updated successfully.', 'isLeaveGroupChangeConfirmed' => $isLeaveGroupChangeConfirmed, 'changeHistoryID' => $changeHistoryID);
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in attendance details update process.');
        }

    }

    function delete_empShift()
    {
        $hidden_id = $this->input->post('hidden-id');

        $isInUse = $this->db->query("SELECT floorID FROM srp_employeesdetails WHERE floorID={$hidden_id}")->row('floorID');

        if (empty($isInUse)) {
            $this->db->where('autoID', $hidden_id)->delete('srp_erp_pay_shiftemployees');
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Records deleted successfully');
            } else {
                return array('e', 'Error in deleting process');
            }
        } else {
            return array('e', 'This record is in use, you can not delete this.');
        }

    }
    /*End of Employee shift*/

    /*Start of Employee Bank*/
    function save_empBank()
    {

        $bankCode = $this->input->post('bankCode');
        $bankName = $this->input->post('bankName');
        $companyID = current_companyID();

        $isExist = $this->db->query("SELECT bankName FROM srp_erp_pay_bankmaster WHERE companyID={$companyID} AND bankName='$bankName' ")->row('bankName');

        if (isset($isExist)) {
            return array('e', 'This bank is already Exists');
        } else {

            $data = array(
                'bankCode' => $bankCode,
                'bankName' => $bankName,
                'companyID' => $companyID,
                'companyCode' => current_companyCode(),
                'createdPCID' => current_pc(),
                'createdUserGroup' => current_user_group(),
                'createdUserID' => current_userID(),
                'createdUserName' => current_employee(),
                'createdDateTime' => current_date()
            );

            $this->db->insert('srp_erp_pay_bankmaster', $data);
            if ($this->db->affected_rows() > 0) {
                $insertID = $this->db->insert_id();
                return array('s', 'Bank created successfully.', $insertID);
            } else {
                return array('e', 'Error in bank create');
            }
        }

    }

    function update_empBank()
    {
        $bankCode = $this->input->post('bankCode');
        $bankName = $this->input->post('bankName');
        $bankID = $this->input->post('hiddenID');
        $companyID = current_companyID();

        $isExist = $this->db->query("SELECT bankID, bankName FROM srp_erp_pay_bankmaster WHERE companyID={$companyID} AND bankName='$bankName' ")->row_array();

        if (isset($isExist)) {
            if ($isExist['bankID'] != $bankID) {
                return array('e', 'This bank is already Exists');
                exit;
            }
        }

        $data = array(
            'bankCode' => $bankCode,
            'bankName' => $bankName,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_employee(),
            'modifiedDateTime' => current_date()
        );

        $this->db->where('bankID', $bankID)->update('srp_erp_pay_bankmaster', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Bank details updated successfully', $bankID);
        } else {
            return array('e', 'Error in bank details updated');
        }
    }

    function delete_empBank()
    {
        $bankID = $this->input->post('hiddenID');

        $isInUse = $this->db->query("SELECT bankID FROM srp_erp_pay_salaryaccounts WHERE bankID={$bankID} ")->row('bankID');

        if (!empty($isInUse)) {
            return array('e', 'This bank is in use');
        } else {

            $this->db->where('bankID', $bankID)->delete('srp_erp_pay_bankmaster');
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Bank deleted successfully.');
            } else {
                return array('e', 'Error in bank delete process');
            }
        }
    }

    function save_empBranchBank()
    {

        $bankID = $this->input->post('bankID');
        $branchName = $this->input->post('branchName');
        $swiftCode = $this->input->post('swiftCode');
        $branchCode = $this->input->post('branchCode');
        $companyID = current_companyID();

        $isExist = $this->db->query("SELECT branchName FROM srp_erp_pay_bankbranches WHERE companyID={$companyID}
                                 AND bankID={$bankID} AND branchName='$branchName' ")->row('branchName');

        if (isset($isExist)) {
            return array('e', 'This branch description is already Exists');
        } else {

            $data = array(
                'bankID' => $bankID,
                'branchCode' => $branchCode,
                'branchName' => $branchName,
                'swiftCode' => $swiftCode,
                'companyID' => $companyID,
                'companyCode' => current_companyCode(),
                'createdPCID' => current_pc(),
                'createdUserGroup' => current_user_group(),
                'createdUserID' => current_userID(),
                'createdUserName' => current_employee(),
                'createdDateTime' => current_date()
            );

            $this->db->insert('srp_erp_pay_bankbranches', $data);
            if ($this->db->affected_rows() > 0) {
                $insertID = $this->db->insert_id();
                return array('s', 'Branch created successfully.', $insertID);
            } else {
                return array('e', 'Error in branch create');
            }
        }

    }

    function update_empBranchBank()
    {

        $bankID = $this->input->post('bankID');
        $branchCode = $this->input->post('branchCode');
        $branchName = $this->input->post('branchName');
        $swiftCode = $this->input->post('swiftCode');
        $branchD = $this->input->post('hiddenID');
        $companyID = current_companyID();

        $isExist = $this->db->query("SELECT branchID, branchName FROM srp_erp_pay_bankbranches WHERE companyID={$companyID}
                                 AND bankID={$bankID} AND branchName='$branchName' ")->row_array();

        if (isset($isExist)) {
            if ($isExist['branchID'] != $branchD) {
                return array('e', 'This branch description is already Exists');
                exit;
            }
        }

        $data = array(
            'swiftCode' => $swiftCode,
            'branchCode' => $branchCode,
            'branchName' => $branchName,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_employee(),
            'modifiedDateTime' => current_date()
        );

        $this->db->where('branchID', $branchD)->update('srp_erp_pay_bankbranches', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Branch details updated successfully', $branchD);
        } else {
            return array('e', 'Error in branch details updated');
        }
    }
    /*End of Employee Bank*/


    /*Start of Over time*/

    function delete_empBranchBank()
    {
        $branchID = $this->input->post('hiddenID');

        $isInUse = $this->db->query("SELECT branchID FROM srp_erp_pay_salaryaccounts WHERE branchID={$branchID} ")->row('branchID');

        if (!empty($isInUse)) {
            return array('e', 'This branch is in use');
        } else {

            $this->db->where('branchID', $branchID)->delete('srp_erp_pay_bankbranches');
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Branch deleted successfully.');
            } else {
                return array('e', 'Error in branch delete process');
            }
        }
    }

    function saveOTCat()
    {
        $description = trim($this->input->post('description'));
        $masterCat = $this->input->post('masterCat');
        $salaryCategoryID = $this->input->post('salaryCategoryID');
        $companyID = current_companyID();

        $isExist = $this->db->query("SELECT description FROM srp_erp_pay_overtimecategory WHERE companyID={$companyID} AND description='$description' ")->row('description');

        if (isset($isExist)) {
            return array('e', 'This category is already exist');
        } else {

            $data = array(
                'OTMasterID' => $masterCat,
                'description' => $description,
                'companyID' => $companyID,
                'salaryCategoryID' => $salaryCategoryID,
                'companyCode' => current_companyCode(),
                'createdPCID' => current_pc(),
                'createdUserGroup' => current_user_group(),
                'createdUserID' => current_userID(),
                'createdUserName' => current_employee(),
                'createdDateTime' => current_date()
            );

            $this->db->insert('srp_erp_pay_overtimecategory', $data);
            if ($this->db->affected_rows() > 0) {
                $insertID = $this->db->insert_id();
                return array('s', 'OT category created successfully.', $insertID);
            } else {
                return array('e', 'Error in OT category create');
            }
        }
    }

    function editOTCat()
    {
        $description = trim($this->input->post('description'));
        $masterCat = $this->input->post('masterCat');
        $catID = $this->input->post('editID');
        $salaryCategoryID = $this->input->post('salaryCategoryID');
        $companyID = current_companyID();


        $isExist = $this->db->query("SELECT ID, description FROM srp_erp_pay_overtimecategory WHERE companyID={$companyID} AND description='$description' ")->row_array();

        if (isset($isExist)) {
            if ($isExist['ID'] != $catID) {
                return array('e', 'This description is already exist');
                exit;
            }
        }

        $data = array(
            'OTMasterID' => $masterCat,
            'description' => $description,
            'salaryCategoryID' => $salaryCategoryID,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_employee(),
            'modifiedDateTime' => current_date()
        );

        $this->db->where('ID', $catID)->update('srp_erp_pay_overtimecategory', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'OT details updated successfully', $catID);
        } else {
            return array('e', 'Error in OT details updated');
        }
    }
    /*End of Over time*/


    /*Start of Over time Group Master*/

    function deleteOTCat()
    {
        $overTimeID = $this->input->post('hiddenID');

        $isInUse = $this->db->query("SELECT overTimeID FROM srp_erp_pay_overtimegroupdetails WHERE overTimeID={$overTimeID} ")->row('overTimeID');

        if (!empty($isInUse)) {
            return array('e', 'This category is in use');
        } else {

            $this->db->where('ID', $overTimeID)->delete('srp_erp_pay_overtimecategory');
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Category deleted successfully.');
            } else {
                return array('e', 'Error in category delete process');
            }
        }
    }

    function save_OTGroupMaster()
    {
        $description = trim($this->input->post('description'));
        $companyID = current_companyID();

        $isExist = $this->db->query("SELECT description FROM srp_erp_pay_overtimegroupmaster WHERE companyID={$companyID} AND description='$description' ")->row('description');

        if (isset($isExist)) {
            return array('e', 'This description is already exist');
        } else {

            $data = array(
                'description' => $description,
                'companyID' => $companyID,
                'companyCode' => current_companyCode(),
                'createdPCID' => current_pc(),
                'createdUserGroup' => current_user_group(),
                'createdUserID' => current_userID(),
                'createdUserName' => current_employee(),
                'createdDateTime' => current_date()
            );

            $this->db->insert('srp_erp_pay_overtimegroupmaster', $data);
            if ($this->db->affected_rows() > 0) {
                $insertID = $this->db->insert_id();
                $response = array(
                    'groupID' => $insertID,
                    'description' => $description
                );
                return array('s', 'OT group master created successfully.', $insertID, $response);
            } else {
                return array('e', 'Error in OT group master create');
            }
        }
    }

    function edit_OTGroupMaster()
    {
        $description = trim($this->input->post('description'));
        $catID = $this->input->post('editID');
        $companyID = current_companyID();


        $isExist = $this->db->query("SELECT groupID, description FROM srp_erp_pay_overtimegroupmaster WHERE companyID={$companyID} AND description='$description' ")->row_array();

        if (isset($isExist)) {
            if ($isExist['groupID'] != $catID) {
                return array('e', 'This description is already exist');
                exit;
            }
        }

        $data = array(
            'description' => $description,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_employee(),
            'modifiedDateTime' => current_date()
        );

        $this->db->where('groupID', $catID)->update('srp_erp_pay_overtimegroupmaster', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'OT group master description updated successfully', $catID);
        } else {
            return array('e', 'Error in OT group master description updated');
        }
    }

    function delete_OTGroupMaster()
    {
        $overTimeID = $this->input->post('catID');

        $isInUse = $this->db->query("SELECT overTimeGroup FROM srp_employeesdetails WHERE overTimeGroup={$overTimeID} ")->row('overTimeGroup');

        if (!empty($isInUse)) {
            return array('e', 'This group master assign to the employee or employees\'s, <br/>You can not delete this.');
        } else {

            $this->db->where('groupID', $overTimeID)->delete('srp_erp_pay_overtimegroupmaster');
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Category deleted successfully.');
            } else {
                return array('e', 'Error in category delete process');
            }
        }
    }

    function save_OTGroupDet()
    {
        $groupID = trim($this->input->post('groupID'));
        $description = $this->input->post('description');
        $OT_ID = $this->input->post('OT_ID');
        // $glCode_arr = $this->input->post('glCode');
        $groupDetID_arr = $this->input->post('groupDetID');
        $formula_arr = $this->input->post('formulaOriginal');


        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $pcID = current_pc();
        $userID = current_userID();
        $userName = current_employee();
        $userGroup = current_user_group();
        $currentDate = current_date();

        $this->db->trans_start();

        $masterData = array(
            'description' => $description,
            'modifiedPCID' => $userID,
            'modifiedUserID' => $pcID,
            'modifiedDateTime' => $currentDate,
            'modifiedUserName' => $userName
        );

        $this->db->where('groupID', $groupID)->update('srp_erp_pay_overtimegroupmaster', $masterData);

        if (!empty($OT_ID)) {
            foreach ($OT_ID as $key => $row) {
                $isExist = null;
                $groupDetID = $groupDetID_arr[$key];
                // $glCode = $glCode_arr[$key];

                if (!empty($groupDetID)) {
                    $isExist = $this->db->query("SELECT overTimeID FROM srp_erp_pay_overtimegroupdetails WHERE companyID={$companyID} AND
                                             groupID={$groupID} AND groupDetailID={$groupDetID}")->row('overTimeID');
                }


                $data = array(
                    /*'glCode' => $glCode,*/
                    'formula' => $formula_arr[$key],
                );

                if ($isExist != null) {
                    $data['overTimeID'] = $row;
                    $data['modifiedPCID'] = $pcID;
                    $data['modifiedUserID'] = $userID;
                    $data['modifiedUserName'] = $userName;
                    $data['modifiedDateTime'] = $currentDate;

                    $this->db->where('groupDetailID', $groupDetID)->update('srp_erp_pay_overtimegroupdetails', $data);
                } else {
                    $data['overTimeID'] = $row;
                    $data['groupID'] = $groupID;
                    $data['companyID'] = $companyID;
                    $data['companyCode'] = $companyCode;
                    $data['createdPCID'] = $pcID;
                    $data['createdUserID'] = $userID;
                    $data['createdUserName'] = $userName;
                    $data['createdUserGroup'] = $userGroup;
                    $data['createdDateTime'] = $currentDate;

                    $this->db->insert('srp_erp_pay_overtimegroupdetails', $data);
                }

            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === true) {
            $this->db->trans_commit();
            $responseData = array(
                'groupID' => $groupID,
                'description' => trim($description)
            );
            return array('s', 'Record saved successfully', $responseData);
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in process');
        }
    }

    function delete_OTGroupDetail()
    {
        $overTimeID = $this->input->post('groupDet_ID');

        $this->db->where('groupDetailID', $overTimeID)->delete('srp_erp_pay_overtimegroupdetails');
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Category deleted successfully.');
        } else {
            return array('e', 'Error in category delete process');
        }

    }

    /*End of Over time Group Master*/


    function getEmployees()
    {
        //$this->db->select('EIdNo, ECode, IFNULL(Ename1,""), IFNULL(Ename2,""), IFNULL(Ename3,""), IFNULL(Ename4,""), DesDescription, EmpImage, currencyID, CurrencyCode, DecimalPlaces');
        $this->db->select('EIdNo, ECode, IFNULL(Ename1,"") Ename1, IFNULL(Ename2,"") Ename2, IFNULL(Ename3,"") Ename3, IFNULL(Ename4,"") Ename4, DesDescription, EmpImage, currencyID, CurrencyCode, DecimalPlaces, ');
        $this->db->from('srp_employeesdetails');
        $this->db->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID', 'left');
        $this->db->join('srp_erp_currencymaster AS cur', 'cur.currencyID = srp_employeesdetails.payCurrencyID');
        $this->db->where("srp_employeesdetails.Erp_companyID", current_companyID());
        $this->db->where("srp_employeesdetails.isPayrollEmployee", 1);
        $this->db->order_by("Ename1");
        $query = $this->db->get();
        return $query->result();
    }

    function employeeData($empID = null)
    {
        $empID = ($empID == null) ? $this->input->post('empID') : $empID;
        $this->db->select('EIdNo, ECode, IFNULL(Ename1,"") Ename1, IFNULL(Ename2,"") Ename2, IFNULL(Ename3,"") Ename3, IFNULL(Ename4,"") Ename4, DesDescription, EmpImage');
        $this->db->from('srp_employeesdetails');
        $this->db->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID', 'left');
        $this->db->join('srp_erp_currencymaster AS cur', 'cur.currencyID = srp_employeesdetails.payCurrencyID', 'left');
        $this->db->where("EIdNo", $empID);
        $query = $this->db->get();
        return $query->row();
    }

    function search($keyword)
    {
        $com = $this->common_data['company_data']['company_id'];
        $where = "(Ename1 LIKE '%$keyword%' OR Ename2 LIKE '%$keyword%' OR Ename3 LIKE '%$keyword%' OR Ename4 LIKE '%$keyword%' OR ECode LIKE '%$keyword%' ";
        $where .= "OR DesDescription  LIKE '%$keyword%') AND srp_employeesdetails.Erp_companyID='$com' AND isPayrollEmployee=1";

        $this->db->select('EIdNo, ECode, IFNULL(Ename1,"") Ename1, IFNULL(Ename2,"") Ename2, IFNULL(Ename3,"") Ename3, IFNULL(Ename4,"") Ename4,, DesDescription');
        $this->db->from('srp_employeesdetails');
        $this->db->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result();
    }

    function searchInEmpLoan($keyword)
    {
        $com = $this->common_data['company_data']['company_id'];
        $where = "(Ename1 LIKE '%$keyword%' OR Ename2 LIKE '%$keyword%' OR Ename3 LIKE '%$keyword%' OR Ename4 LIKE '%$keyword%' OR ECode LIKE '%$keyword%' ";
        $where .= "OR DesDescription  LIKE '%$keyword%') AND srp_employeesdetails.Erp_companyID='$com'";
        $where .= "AND salary.confirmedYN=1 AND isDischarged=0";

        //$con = "IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '') '  |  ', DesDescription , '    |   ', ECode";
        $con = "IFNULL(Ename2, ''), '    |   ', DesDescription, '    |   ', ECode";

        $this->db->select("EIdNo, ECode, IFNULL(Ename2, '') Ename2, DesDescription, payCurrency, payCurrencyID, DecimalPlaces, CONCAT(" . $con . ") AS 'Match'");
        $this->db->from('srp_employeesdetails');
        $this->db->join('srp_erp_pay_salarydeclartion AS salaryDec', 'srp_employeesdetails.EIdNo = salaryDec.employeeNo');
        $this->db->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID');
        $this->db->join('srp_erp_pay_salarydeclartion AS salary', 'salary.employeeNo = EIdNo');
        $this->db->join('srp_erp_currencymaster AS cu_master', 'cu_master.CurrencyCode = srp_employeesdetails.payCurrency ', 'left');
        //$this->db->join('srp_erp_leavegroup', 'srp_employeesdetails.leaveGroupID=srp_erp_leavegroup.leaveGroupID', 'INNER');  //shahmy
        $this->db->where($where);
        $this->db->group_by('salaryDec.employeeNo');
        $query = $this->db->get();
        return $query->result();
    }

    function loadEmpDeclarations($empID)
    {
        $this->db->select('id, srp_erp_pay_salarycategories.salaryCategoryID, salaryDescription, amount, salaryCategoryType, effectiveDate, percentage AS per, confirmedYN');
        $this->db->from('srp_erp_pay_salarydeclartion');
        $this->db->join('srp_erp_pay_salarycategories', 'srp_erp_pay_salarycategories.salaryCategoryID = srp_erp_pay_salarydeclartion.salaryCategoryID');
        $this->db->where('employeeNo', $empID);
        $this->db->order_by('effectiveDate', 'ASC');
        $query = $this->db->get();

        return $query->result();
    }

    function loadEmpDeclarations_nonPayroll($empID)
    {
        $this->db->select('id, srp_erp_pay_salarycategories.salaryCategoryID, salaryDescription, amount, salaryCategoryType, effectiveDate,
                           percentage AS per,  transactionCurrency, confirmedYN');
        $this->db->from('srp_erp_non_pay_salarydeclartion');
        $this->db->join('srp_erp_pay_salarycategories', 'srp_erp_pay_salarycategories.salaryCategoryID = srp_erp_non_pay_salarydeclartion.salaryCategoryID');
        $this->db->where('employeeNo', $empID);
        $this->db->order_by('effectiveDate', 'ASC');
        $query = $this->db->get();

        return $query->result();
    }

    function deleteSalaryDec($deleteID)
    {
        $isConfirmed = $this->isAlreadyConformedDec($deleteID);

        if ($isConfirmed->confirmedYN == 0) {
            $this->db->delete('srp_erp_pay_salarydeclartion', array('id' => $deleteID));

            if ($this->db->affected_rows() > 0) {
                return array('s', 'Delete successfully');
            } else {
                return array('e', 'Failed to delete record');
            }
        } else {
            return array('e', 'This record all ready confirmed, you can not delete this.');
        }

    }

    function isAlreadyConformedDec($deleteID)
    {
        $query = $this->db->where('id', $deleteID)
            ->select('confirmedYN')
            ->from('srp_erp_pay_salarydeclartion')
            ->get();
        return $query->row();
    }

    function saveBankAccount($data)
    {
        $this->db->insert('srp_erp_pay_salaryaccounts', $data);

        if ($this->db->affected_rows() > 0) {
            return array('s', 'Salary Account Saved');
        } else {
            return array('e', 'Please try again');
        }
    }

    function loadEmpBankAccount($empID)
    {
        return $this->db->query("SELECT bnk.bankID, bankName, accountNo, toBankPercentage, accountHolderName, acc.id,
                                 acc.isActive, acc.swiftCode, brn.swiftCode AS brnSwiftCode,branchName, brn.branchID
                                 FROM srp_erp_pay_salaryaccounts AS acc
                                 JOIN srp_erp_pay_bankmaster AS bnk ON bnk.bankID=acc.bankID
                                 JOIN srp_erp_pay_bankbranches AS brn ON brn.branchID=acc.branchID
                                 WHERE employeeNo = {$empID}")->result_array();

    }

    function loadEmpNonBankAccount($empID)
    {
        return $this->db->query("SELECT bnk.bankID, bankName, accountNo, toBankPercentage, accountHolderName, acc.id,
                                 acc.isActive, acc.swiftCode, brn.swiftCode AS brnSwiftCode,branchName, brn.branchID
                                 FROM srp_erp_non_pay_salaryaccounts AS acc
                                 JOIN srp_erp_pay_bankmaster AS bnk ON bnk.bankID=acc.bankID
                                 JOIN srp_erp_pay_bankbranches AS brn ON brn.branchID=acc.branchID
                                 WHERE employeeNo = {$empID}")->result_array();

    }

    function updateBankAccount($data, $id)
    {
        $this->db->update('srp_erp_pay_salaryaccounts', $data, 'id=' . $id);

        if ($this->db->affected_rows() > 0) {
            return array('s', 'Bank account Updated');
        } else {
            return array('e', 'Error in Bank account Update process');
        }
    }

    function inactiveBankAccount($data, $deleteID)
    {
        $this->db->update('srp_erp_pay_salaryaccounts', $data, 'id=' . $deleteID);

        if ($this->db->affected_rows() > 0) {
            return 'Inactivated';
        } else {
            return 'Error';
        }
    }

    function save_monthAddition($monthType)
    {
        $this->form_validation->set_rules('monthDescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('dateDesc', 'Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            return array('e', validation_errors());
        } else {
            $description = $this->input->post('monthDescription');
            $payrollType = trim($this->input->post('payrollType'));
            $date_format_policy = date_format_policy();
            $datDsc = $this->input->post('dateDesc');
            $dateDesc = input_format_date($datDsc, $date_format_policy);
            $current_date = current_date();

            if ($monthType == 'MA') {
                $tableName = 'srp_erp_pay_monthlyadditionsmaster';
                $orderByID = 'monthlyAdditionsMasterID';
                $monthlyCode = 'monthlyAdditionsCode';
                $dateField = 'dateMA';
                $msg = 'Monthly Addition';
            } elseif ($monthType == 'MD') {
                $tableName = 'srp_erp_pay_monthlydeductionmaster';
                $orderByID = 'monthlyDeductionMasterID';
                $monthlyCode = 'monthlyDeductionCode';
                $dateField = 'dateMD';
                $msg = 'Monthly Deduction';
            }

            //Get last Loan no
            $query = $this->db->select('serialNo')
                ->from($tableName)
                ->where('companyID', current_companyID())
                ->order_by($orderByID, 'desc')
                ->get();
            $lastAddArray = $query->row_array();
            $additionNo = $lastAddArray['serialNo'];
            $additionNo = ($additionNo == null) ? 1 : $lastAddArray['serialNo'] + 1;


            //Generate Code
            $this->load->library('sequence');
            $additionCode = $this->sequence->sequence_generator($monthType, $additionNo);


            $data = array(
                $monthlyCode => $additionCode,
                'serialNo' => $additionNo,
                'documentID' => $monthType,
                'description' => $description,
                $dateField => $dateDesc,
                'isNonPayroll' => $payrollType,
                'confirmedYN' => 0,
                'isProcessed' => 0,
                'currency' => $this->common_data['company_data']['company_default_currency'],
                'companyID' => $this->common_data['company_data']['company_id'],
                'companyCode' => $this->common_data['company_data']['company_code'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdDateTime' => $current_date
            );


            $this->db->insert($tableName, $data);

            if ($this->db->affected_rows() > 0) {
                $insert_id = $this->db->insert_id();
                return array('s', $msg . '[ ' . $additionCode . ' ] Insert successfully', $insert_id);
            } else {
                return (array('s', 'Failed to insert record' . $msg));
            }


        }

    }

    function empMonthAddition_printData($monthType, $id)
    {
        /*echo json_encode($this->Employee_model->load_empMonthAddition('MA'));*/
        if ($monthType == 'MA') {
            $tableName = 'srp_erp_pay_monthlyadditiondetail';
            $masterID = 'monthlyAdditionsMasterID';
        } elseif ($monthType == 'MD') {
            $tableName = 'srp_erp_pay_monthlydeductiondetail';
            $masterID = 'monthlyDeductionMasterID';
        }

        /*$con = "IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '')";*/
        $con = "IFNULL(Ename2, '')";
        $this->db->select('ECode, CONCAT(' . $con . ') AS empName, transactionCurrency, transactionAmount,
                          transactionCurrencyDecimalPlaces dPlace, description, declarationTB.monthlyDeclaration AS declarationDes')
            ->from($tableName)
            ->join('srp_employeesdetails AS empTB', $tableName . '.empID=empTB.EIdNo')
            ->join('srp_erp_pay_monthlydeclarationstypes AS declarationTB', $tableName . '.declarationID=declarationTB.monthlyDeclarationID')
            ->where($masterID, $id)
            ->order_by('transactionCurrency');

        return $this->db->get()->result_array();
    }

    function delete_monthAddition($type)
    {
        $delID = $this->input->post('delID');

        if ($type == 'Addition' || $type == 'Deduction') {
            if ($type == 'Addition') {
                $tableName = 'srp_erp_pay_monthlyadditionsmaster';
                $tableDetail = 'srp_erp_pay_monthlyadditiondetail';
                $monthID = 'monthlyAdditionsMasterID';
            } elseif ($type == 'Deduction') {
                $tableName = 'srp_erp_pay_monthlydeductionmaster';
                $tableDetail = 'srp_erp_pay_monthlydeductiondetail';
                $monthID = 'monthlyDeductionMasterID';
            }

            $isConfirmed = $this->isAlreadyConformed($tableName, $monthID, $delID);
            $deleteArray = array($monthID => $delID);

            if ($isConfirmed['isProcessed'] == 1) {
                return array('e', 'This ' . $type . ' is already processed, You can not delete this.');
            } else if ($isConfirmed['confirmedYN'] == 1) {
                return array('e', 'This ' . $type . ' is already confirmed, You can not delete this.');
            } else {
                $this->db->trans_start();
                $this->db->delete($tableName, $deleteArray);
                $this->db->delete($tableDetail, $deleteArray);

                $this->db->trans_complete();
                if ($this->db->trans_status() == true) {
                    $this->db->trans_commit();
                    return array('s', 'Deleted successfully');
                } else {
                    $this->db->trans_rollback();
                    return array('e', 'Failed to delete record');
                }
            }
        } else {
            return array('e', 'Some thing went wrong');
        }
    }

    function isAlreadyConformed($tableName, $monthID, $autoID)
    {
        $query = $this->db->select('confirmedYN, isProcessed')
            ->from($tableName)
            ->where($monthID, $autoID)
            ->get();
        $result = $query->row_array();
        return $result;
    }

    function save_employeeAsTemp()
    {
        $empDet = $this->input->post('temp_empHiddenID');
        $empCurrencyID = $this->input->post('temp_empCurrencyID');
        $empCurrencyCode = $this->input->post('temp_empCurrencyCode');
        $empCurrencyDPlace = $this->input->post('temp_empCurrencyDPlace');
        $accGroupID = $this->input->post('temp_accGroupID');
        $masterID = $this->input->post('masterID');
        $type_m = $this->input->post('type_m');

        if ($type_m == 'MA') {
            $tableName = 'srp_erp_pay_monthlyadditiondetail';
            $masterColumn = 'monthlyAdditionsMasterID';
        } elseif ($type_m == 'MD') {
            $tableName = 'srp_erp_pay_monthlydeductiondetail';
            $masterColumn = 'monthlyDeductionMasterID';
        }


        $data = array();
        $current_date = current_date();
        $com_currencyID = $this->common_data['company_data']['company_default_currencyID'];
        $com_currency = $this->common_data['company_data']['company_default_currency'];
        $com_currDPlace = $this->common_data['company_data']['company_default_decimal'];


        foreach ($empDet as $key => $emp) {
            $trCurrencyID = $empCurrencyID[$key];
            $data[$key]['empID'] = $emp;
            $data[$key]['accessGroupID'] = $accGroupID[$key];
            $data[$key][$masterColumn] = $masterID;
            $data[$key]['transactionCurrencyID'] = $trCurrencyID;
            $data[$key]['transactionCurrency'] = $empCurrencyCode[$key];
            $data[$key]['transactionExchangeRate'] = 1;
            $data[$key]['transactionCurrencyDecimalPlaces'] = $empCurrencyDPlace[$key];

            if ($key > 0) {
                if ($trCurrencyID == $empCurrencyID[$key - 1]) {
                    $com_exchangeRate = $data[$key - 1]['companyLocalExchangeRate'];
                } else {
                    $com_exchangeRateData = currency_conversionID($trCurrencyID, $com_currencyID);
                    $com_exchangeRate = $com_exchangeRateData['conversion'];
                }
            } else {
                $com_exchangeRateData = currency_conversionID($trCurrencyID, $com_currencyID);
                $com_exchangeRate = $com_exchangeRateData['conversion'];
            }

            $data[$key]['companyLocalCurrencyID'] = $com_currencyID;
            $data[$key]['companyLocalCurrency'] = $com_currency;
            $data[$key]['companyLocalExchangeRate'] = $com_exchangeRate;
            $data[$key]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

            $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdUserName'] = $this->common_data['current_user'];
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdDateTime'] = $current_date;
        }


        $this->db->trans_start();
        $this->db->insert_batch($tableName, $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Failed to Update');
        } else {
            $this->db->trans_commit();
            return array('s', '');
        }
    }

    function save_empMonthlyAddition($monthType)
    {
        $editionDet = $this->edit_monthAddition($monthType);

        if ($editionDet['confirmedYN'] == 1) {
            return array('e', $this->input->post('updateCode') . ' is Already confirmed, you can not change this.');
        } else {
            if ($monthType == 'MA') {
                $tableName = 'srp_erp_pay_monthlyadditiondetail';
                $masterID = 'monthlyAdditionsMasterID';
                $msg = 'Monthly Addition';
            } elseif ($monthType == 'MD') {
                $tableName = 'srp_erp_pay_monthlydeductiondetail';
                $masterID = 'monthlyDeductionMasterID';
                $msg = 'Monthly Deduction';
            }

            if (empty($this->input->post('empHiddenID'))) {
                return $this->update_monthAddition($monthType);
            } else {

                $this->form_validation->set_rules('empHiddenID[]', 'Employee', 'trim|required');

                if ($this->input->post('isConform') == 1) {
                    $this->form_validation->set_rules('amount[]', 'Amount/s', 'trim|required');
                    $this->form_validation->set_rules('declarationID[]', 'Description/s', 'trim|required');
                }


                if ($this->form_validation->run() == FALSE) {
                    return array('e', validation_errors());
                } else {
                    $masterUpdate = $this->update_monthAddition($monthType);

                    if ($masterUpdate[0] == 's') {
                        $updateID = trim($this->input->post('updateID'));

                        $this->db->trans_start();
                        $this->db->where($masterID, $updateID)->delete($tableName);

                        $com_currency = $this->common_data['company_data']['company_default_currency'];
                        $com_currDPlace = $this->common_data['company_data']['company_default_decimal'];
                        $com_repCurrency = $this->common_data['company_data']['company_reporting_currency'];
                        $com_repCurDPlace = $this->common_data['company_data']['company_reporting_decimal'];

                        $empHiddenID = $this->input->post('empHiddenID');
                        $empCurrencyDPlace = $this->input->post('empCurrencyDPlace');
                        $empCurrencyCode = $this->input->post('empCurrencyCode');
                        $description = $this->input->post('description');
                        $declarationID = $this->input->post('declarationID');
                        $HGLCode = $this->input->post('h-glCode');
                        $amount = $this->input->post('amount');
                        $updateCode = $this->input->post('updateCode');
                        $accGroupID = $this->input->post('empAccGroupID');
                        $current_date = current_date();


                        $i = 0;
                        $data = array();
                        foreach ($empHiddenID as $empID) {
                            $tr_amount = (!empty($amount[$i])) ? str_replace(',', '', $amount[$i]) : 0;
                            $localCon = currency_conversion($empCurrencyCode[$i], $com_currency, $tr_amount);
                            $reportCon = currency_conversion($empCurrencyCode[$i], $com_repCurrency, $tr_amount);
                            $localAmount = ($localCon['conversion'] > 0) ? round(($tr_amount / $localCon['conversion']), $com_currDPlace) : round($tr_amount, $com_currDPlace);
                            $reportAmount = ($reportCon['conversion'] > 0) ? round(($tr_amount / $reportCon['conversion']), $com_repCurDPlace) : round($tr_amount, $com_repCurDPlace);

                            $data[$i]['empID'] = $empID;
                            $data[$i]['accessGroupID'] = $accGroupID[$i];
                            $data[$i][$masterID] = $updateID;
                            $data[$i]['description'] = $description[$i];
                            $data[$i]['declarationID'] = $declarationID[$i];
                            $data[$i]['GLCode'] = $HGLCode[$i];
                            $data[$i]['transactionCurrencyID'] = $localCon['trCurrencyID'];
                            $data[$i]['transactionCurrency'] = $empCurrencyCode[$i];
                            $data[$i]['transactionExchangeRate'] = 1;
                            $data[$i]['transactionAmount'] = $tr_amount;
                            $data[$i]['transactionCurrencyDecimalPlaces'] = $empCurrencyDPlace[$i];
                            $data[$i]['companyLocalCurrencyID'] = $localCon['currencyID'];
                            $data[$i]['companyLocalCurrency'] = $com_currency;
                            $data[$i]['companyLocalExchangeRate'] = $localCon['conversion'];
                            $data[$i]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;
                            $data[$i]['companyLocalAmount'] = $localAmount;
                            $data[$i]['companyReportingCurrencyID'] = $reportCon['currencyID'];
                            $data[$i]['companyReportingCurrency'] = $com_repCurrency;
                            $data[$i]['companyReportingAmount'] = $reportAmount;
                            $data[$i]['companyReportingExchangeRate'] = $reportCon['conversion'];
                            $data[$i]['companyReportingCurrencyDecimalPlaces'] = $com_repCurDPlace;
                            $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                            $data[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                            $data[$i]['createdPCID'] = $this->common_data['current_pc'];
                            $data[$i]['createdUserID'] = $this->common_data['current_userID'];
                            $data[$i]['createdUserName'] = $this->common_data['current_user'];
                            $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
                            $data[$i]['createdDateTime'] = $current_date;
                            $i++;
                        }


                        $this->db->insert_batch($tableName, $data);
                        $this->db->trans_complete();

                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            return array('s', 'Failed to Update [ ' . $updateCode . ' ] ' . $msg);
                        } else {
                            $this->db->trans_commit();
                            return array('s', $msg . '[ ' . $updateCode . ' ] Updated successfully');
                        }
                    } else {
                        return $masterUpdate;
                    }
                }
            }
        }
    }

    function edit_monthAddition($type, $id = 0)
    {
        $editID = $this->input->post('editID');
        $editID = ($editID == null) ? $id : $editID;
        $id = (isset($editID)) ? $editID : $this->input->post('updateID');
        $convertFormat = convert_date_format_sql();
        if ($type == 'MA' || $type == 'MD') {
            if ($type == 'MA') {
                $tableName = 'srp_erp_pay_monthlyadditionsmaster';
                $monthID = 'monthlyAdditionsMasterID';
                $monthlyCode = 'monthlyAdditionsCode';
                $dateField = 'DATE_FORMAT(dateMA,\'' . $convertFormat . '\') AS dateMA ';
            } elseif ($type == 'MD') {
                $tableName = 'srp_erp_pay_monthlydeductionmaster';
                $monthID = 'monthlyDeductionMasterID';
                $monthlyCode = 'monthlyDeductionCode';
                $dateField = 'DATE_FORMAT(dateMD,\'' . $convertFormat . '\') AS dateMD ';
            }

            $query = $this->db->select($monthlyCode . ' ,description, confirmedYN, approvedYN, isNonPayroll, isProcessed, ' . $dateField)
                ->from($tableName)
                ->where($monthID, $id)
                ->get();

            return $query->row_array();
        } else {
            return array('e', 'Some thing went wrong');
        }

    }

    function update_monthAddition($type)
    {
        $updateID = trim($this->input->post('updateID'));

        $this->form_validation->set_rules('monthDescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('dateDesc', 'Date', 'trim|required|date');
        $this->form_validation->set_rules('updateID', 'Update ID', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            return array('e', validation_errors());
        } else {

            $description = $this->input->post('monthDescription');
            $date_format_policy = date_format_policy();
            $datDsc = $this->input->post('dateDesc');
            $dateDesc = input_format_date($datDsc, $date_format_policy);


            $isConform = $this->input->post('isConform');
            $updateCode = $this->input->post('updateCode');
            $current_date = current_date();


            if ($type == 'MA') {
                $tableName = 'srp_erp_pay_monthlyadditionsmaster';
                $monthID = 'monthlyAdditionsMasterID';
                $dateField = 'dateMA';
                $msg = 'Monthly Addition';
            } elseif ($type == 'MD') {
                $tableName = 'srp_erp_pay_monthlydeductionmaster';
                $monthID = 'monthlyDeductionMasterID';
                $dateField = 'dateMD';
                $msg = 'Monthly Deduction';
            }


            $isConfirmed = $this->isAlreadyConformed($tableName, $monthID, $updateID);

            if ($isConfirmed != 1) {
                $data = array(
                    'description' => $description,
                    $dateField => $dateDesc,
                    'confirmedYN' => $isConform,
                    'confirmedByEmpID' => current_userID(),
                    'confirmedByName' => current_employee(),
                    'confirmedDate' => $current_date,
                    'modifiedPCID' => $this->common_data['current_pc'],
                    'modifiedUserID' => $this->common_data['current_userID'],
                    'modifiedUserName' => $this->common_data['current_user'],
                    'modifiedDateTime' => $current_date
                );


                $this->db->where($monthID, $updateID);
                $this->db->update($tableName, $data);


                return array('s', $msg . '[ ' . $updateCode . ' ] Updated successfully');

            } else {
                return array('e', '[' . $updateCode . '] is already confirmed, You can not Update this.');
            }

        }
    }

    function load_empMonthAddition($monthType)
    {
        $id = $this->input->post('editID');
        if ($monthType == 'MA') {
            $tableName = 'srp_erp_pay_monthlyadditiondetail';
            $masterID = 'monthlyAdditionsMasterID';
        } elseif ($monthType == 'MD') {
            $tableName = 'srp_erp_pay_monthlydeductiondetail';
            $masterID = 'monthlyDeductionMasterID';
        }

        $query = $this->db->select($tableName . '.*, EIdNo, ECode, Ename1, Ename2, Ename3')
            ->from($tableName)
            ->join('srp_employeesdetails AS empTB', $tableName . '.empID=empTB.EIdNo')
            ->where($masterID, $id)
            ->get();

        return $query->result_array();

    }

    function referBack_monthAddition()
    {
        $id = $this->input->post('referID');
        $referBack = $this->input->post('referBack');

        if ($referBack == 'MA') {
            $tableName = 'srp_erp_pay_monthlyadditionsmaster';
            $masterID = 'monthlyAdditionsMasterID';
            $monthlyCode = 'monthlyAdditionsCode';
        } elseif ($referBack == 'MD') {
            $tableName = 'srp_erp_pay_monthlydeductionmaster';
            $masterID = 'monthlyDeductionMasterID';
            $monthlyCode = 'monthlyDeductionCode';
        }


        $query = $this->db->select($monthlyCode . ' AS code, isProcessed')
            ->from($tableName)
            ->where($masterID, $id)
            ->get();

        $details = $query->row();

        //echo $this->db->last_query();die();

        if ($details->isProcessed == 1) {
            return array('e', $details->code . ' is already processed you can not refer back this');
        } else {
            $updateDetail = array(
                'confirmedYN' => 2,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $this->common_data['current_userID'],
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => current_date()
            );
            $this->db->where($masterID, $id)->update($tableName, $updateDetail);
            if ($this->db->affected_rows() > 0) {
                return array('s', '[' . $details->code . '] Refer backed successfully');
            } else {
                return array('e', 'Error in refer back process [' . $details->code . ']');
            }
        }
    }

    function bankBranches()
    {
        $bankID = $this->input->post('bankID');
        return $this->db->query("SELECT branchID, branchCode, branchName FROM srp_erp_pay_bankbranches WHERE bankID={$bankID} ORDER BY branchName")->result_array();
    }

    function create_bank($bankName)
    {
        $bankName = trim($bankName);

        $isExist = $this->db->query("SELECT bankID FROM srp_erp_pay_bankmaster WHERE bankName='$bankName' ")->row_array();


        if (empty($isExist)) {
            $data = array(
                'bankName' => $bankName,
                'companyID' => $this->common_data['company_data']['company_id'],
                'companyCode' => $this->common_data['company_data']['company_code'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdDateTime' => current_date()
            );

            $this->db->insert('srp_erp_pay_bankmaster', $data);

            if ($this->db->affected_rows() > 0) {
                return array('s', $this->db->insert_id());
            } else {
                return array('e', 'Error in Bank creation');
            }
        } else {
            return array('e', 'Already a bank exists in this name');
        }


    }

    function create_bankBranch($bank_id, $br_name)
    {
        $data = array(
            'bankID' => $bank_id,
            'branchName' => $br_name,
            'companyID' => $this->common_data['company_data']['company_id'],
            'companyCode' => $this->common_data['company_data']['company_code'],
            'createdPCID' => $this->common_data['current_pc'],
            'createdUserID' => $this->common_data['current_userID'],
            'createdUserName' => $this->common_data['current_user'],
            'createdUserGroup' => $this->common_data['user_group'],
            'createdDateTime' => current_date()
        );

        $this->db->insert('srp_erp_pay_bankbranches', $data);

        if ($this->db->affected_rows() > 0) {
            return array('s', $this->db->insert_id());
        } else {
            return array('e', 'Error in Bank branch creation');
        }
    }

    function removeAll_emp()
    {
        $masterID = $this->input->post('masterID');
        $monthType = $this->input->post('type_m');

        if ($monthType == 'MA') {
            $tableName = 'srp_erp_pay_monthlyadditiondetail';
            $masterColumn = 'monthlyAdditionsMasterID';
        } elseif ($monthType == 'MD') {
            $tableName = 'srp_erp_pay_monthlydeductiondetail';
            $masterColumn = 'monthlyDeductionMasterID';
        }

        $this->db->trans_start();
        $this->db->where(array($masterColumn => $masterID))->delete($tableName);
        $this->db->trans_complete();

        if ($this->db->trans_status() == false) {
            $this->db->trans_rollback();
            return array('e', 'Error');
        } else {
            $this->db->trans_commit();
            return array('s', '');
        }
    }

    function removeSingle_emp()
    {
        $detailID = $this->input->post('detailID');
        $monthType = $this->input->post('type_m');

        if ($monthType == 'MA') {
            $tableName = 'srp_erp_pay_monthlyadditiondetail';
            $masterColumn = 'monthlyAdditionDetailID';
        } elseif ($monthType == 'MD') {
            $tableName = 'srp_erp_pay_monthlydeductiondetail';
            $masterColumn = 'monthlyDeductionDetailID';
        }

        $this->db->trans_start();
        $this->db->where(array($masterColumn => $detailID))->delete($tableName);
        $this->db->trans_complete();

        if ($this->db->trans_status() == false) {
            $this->db->trans_rollback();
            return array('e', 'Error');
        } else {
            $this->db->trans_commit();
            return array('s', '');
        }
    }

    function update_leaveTypes()
    {
        $description = $this->input->post('leaveDescription');
        $attachmentRequired = $this->input->post('attachmentRequired');
        $isPaidLeave = $this->input->post('isPaidLeave');
        $leavePlanApplicable = $this->input->post('leavePlanApplicable');
        $editID = $this->input->post('editID');

        $data = array(
            'description' => $description,
            'attachmentRequired' => $attachmentRequired,
            'isPaidLeave' => $isPaidLeave,
            'isPlanApplicable' => $leavePlanApplicable,
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => current_date()

        );

        $isExistLeaveTypes = $this->isExistLeaveTypes($description);

        if (!empty($isExistLeaveTypes)) {
            if ($isExistLeaveTypes['leaveTypeID'] == $editID) {
                $this->db->where('leaveTypeID', $editID);
                $this->db->update('srp_erp_leavetype', $data);

                if ($this->db->affected_rows() > 0) {
                    return array('s', 'Leave type updated successfully.');
                } else {
                    return array('e', 'Error in leave type update process');
                }
            } else {
                return array('w', 'This description already exists.');
            }

        } else {
            $this->db->where('leaveTypeID', $editID);
            $this->db->update('srp_erp_leavetype', $data);

            if ($this->db->affected_rows() > 0) {
                return array('s', 'Leave type updated successfully.');
            } else {
                return array('e', 'Error in leave type update process');
            }
        }
    }

    function save_leaveTypes()
    {
        $description = $this->input->post('leaveDescription');
        $attachmentRequired = $this->input->post('attachmentRequired');
        $isPaidLeave = $this->input->post('isPaidLeave');
        $leavePlanApplicable = $this->input->post('leavePlanApplicable');
        $isSickLeave = $this->input->post('isSickLeave');
        $confirmed = ($isSickLeave == 1) ? 0 : 1;
        $isExistLeaveTypes = $this->isExistLeaveTypes($description);
        if (!empty($isExistLeaveTypes)) {
            return array('w', 'This description already exists.');
        } else {
            $data = array(
                'description' => $description,
                'attachmentRequired' => $attachmentRequired,
                'isPaidLeave' => $isPaidLeave,
                'isPlanApplicable' => $leavePlanApplicable,
                'isSickLeave' => $isSickLeave,
                'typeConfirmed' => $confirmed,
                'companyID' => $this->common_data['company_data']['company_id'],
                'companyCode' => $this->common_data['company_data']['company_code'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdDateTime' => current_date()
            );

            $this->db->insert('srp_erp_leavetype', $data);

            if ($this->db->affected_rows() > 0) {
                return array('s', 'Leave type created successfully.');
            } else {
                return array('e', 'Error in leave type create process');
            }
        }
    }

    function isExistLeaveTypes($description)
    {
        $companyID = $this->common_data['company_data']['company_id'];
        return $this->db->query("SELECT leaveTypeID FROM srp_erp_leavetype WHERE description='$description' AND companyID={$companyID}")->row_array();
    }

    function delete_leaveTypes()
    {
        $deleteID = $this->input->post('deleteID');
        $isInUse = $this->db->query("SELECT description FROM `srp_erp_leavegroupdetails`  AS t1
                                     JOIN srp_erp_leavetype AS t2 ON t1.leaveTypeID = t2.leaveTypeID
                                     WHERE t2.leaveTypeID={$deleteID} ")->row_array();

        if (!empty($isInUse)) {
            return array('e', 'You can\'t delete this leave type');
        } else {
            $this->db->trans_start();
            $where = ['leaveTypeID' => $deleteID, 'companyID' => current_companyID()];
            $this->db->where($where)->delete('srp_erp_leavetype');
            $this->db->where($where)->delete('srp_erp_sickleavesetup');

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Failed delete leave type');
            } else {
                $this->db->trans_commit();
                return array('s', 'Leave type delete process done.');
            }
        }
    }

    function emp_leaves($empID = null)
    {
        $empID = ($empID == null) ? $this->input->post('empID') : $empID;
        return $this->db->query("SELECT description, t3.policyDescription, days, leaveEntitledID, t1.leaveTypeID
                                 FROM srp_erp_leaveentitled AS t1
                                 JOIN srp_erp_leavetype AS t2 ON t1.leaveTypeID = t2.leaveTypeID
                                 JOIN srp_erp_leavepolicymaster AS t3 ON t3.policyMasterID = t2.policyID
                                 WHERE empID={$empID}")->result_array();
    }

    function save_empLeaveEntitle()
    {
        $empID = $this->input->post('empID');
        $leaveType = $this->input->post('leaveType');
        $leave_days = $this->input->post('leave_days');
        $companyID = $this->common_data['company_data']['company_id'];
        $companyCode = $this->common_data['company_data']['company_code'];
        $createdPCID = $this->common_data['current_pc'];
        $createdUserID = $this->common_data['current_userID'];
        $createdUserName = $this->common_data['current_user'];
        $createdUserGroup = $this->common_data['user_group'];
        $createdDateTime = current_date();

        if (isset($leaveType)) {
            $i = 0;
            $data = array();
            foreach ($leaveType as $typ) {
                $isNotExist = isAlreadyExistInThisArray($leaveType, $typ, $i, $empID);

                if ($isNotExist[0] == 'e') {
                    return array('e', $isNotExist[1]);
                }

                $data[$i]['empID'] = $empID;
                $data[$i]['leaveTypeID'] = $typ;
                $data[$i]['days'] = $leave_days[$i];
                $data[$i]['companyID'] = $companyID;
                $data[$i]['companyCode'] = $companyCode;
                $data[$i]['createdPCID'] = $createdPCID;
                $data[$i]['createdUserID'] = $createdUserID;
                $data[$i]['createdUserName'] = $createdUserName;
                $data[$i]['createdUserGroup'] = $createdUserGroup;
                $data[$i]['createdDateTime'] = $createdDateTime;
                $i++;
            }


            $this->db->trans_start();
            $this->db->insert_batch('srp_erp_leaveentitled', $data);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Failed Insert Data');
            } else {
                $this->db->trans_commit();
                return array('s', 'Leave Entitle Process Success.');
            }
        }
    }

    function update_empLeaveEntitle()
    {

        $editID = $this->input->post('editID');
        $empID = $this->input->post('empID');
        $leaveType = $this->input->post('leaveType_e');
        $leave_days = $this->input->post('leave_days_e');

        $isExist = $this->db->query("SELECT description, leaveEntitledID FROM srp_erp_leaveentitled AS t1
                                     JOIN srp_erp_leavetype AS t2 ON t1.leaveTypeID = t2.leaveTypeID
                                     WHERE empID={$empID} AND t1.leaveTypeID={$leaveType}")->row_array();

        if (!empty($isExist)) {
            if ($isExist['leaveEntitledID'] != $editID) {
                return array('e', $isExist['description'] . ' is already exists.');
            }
        }


        $data = array(
            'empID' => $empID,
            'leaveTypeID' => $leaveType,
            'days' => $leave_days,
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => current_date()
        );


        $this->db->trans_start();
        $this->db->where('leaveEntitledID', $editID)->update('srp_erp_leaveentitled', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Failed update ');
        } else {
            $this->db->trans_commit();
            return array('s', 'Leave Entitle Update Process Done.');
        }

    }

    function delete_empLeaveEntitle()
    {
        $deleteID = $this->input->post('deleteID');

        $this->db->trans_start();
        $this->db->where('leaveEntitledID', $deleteID)->delete('srp_erp_leaveentitled');
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Failed delete ');
        } else {
            $this->db->trans_commit();
            return array('s', 'Leave Entitle Delete Process Done.');
        }
    }

    function employeeLeaveSummery($empID = null, $leaveType = null, $policyMasterID = null)
    {
        $empID = ($empID == null) ? $this->input->post('empID') : $empID;
        $leaveType = ($leaveType == null) ? $this->input->post('leaveType') : $leaveType;
        $policyMasterID = ($policyMasterID == null) ? $this->input->post('policyMasterID') : $policyMasterID;
        $thisYear = date('Y-01-01');
        $nextYear = date('Y-01-01', strtotime('+1 years'));

        if ($policyMasterID == 2) {
            $qry3 = "SELECT t3.policyMasterID, IFNULL((SELECT SUM(hoursEntitled) 
                     FROM srp_erp_leaveaccrualdetail 
                     LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID
                     WHERE empID = {$empID} AND leaveType = {$leaveType} AND confirmedYN = 1), 0) AS entitled, IFNULL((SELECT SUM(hours) FROM srp_erp_leavemaster 
                     WHERE empID = {$empID} AND leaveTypeID = {$leaveType} AND approvedYN = 1), 0) AS leaveTaken, IFNULL((SELECT SUM(hoursEntitled) 
                     FROM srp_erp_leaveaccrualdetail 
                     LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID 
                     WHERE empID = {$empID} AND leaveType = {$leaveType} AND confirmedYN = 1), 0) - IFNULL((SELECT SUM(hours) FROM srp_erp_leavemaster 
                     WHERE empID = {$empID} AND leaveTypeID = {$leaveType} AND approvedYN = 1), 0) AS balance, policyDescription,
                      IFNULL((SELECT SUM(hoursEntitled) FROM srp_erp_leaveaccrualdetail 
                      LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID 
                      WHERE empID = {$empID} AND leaveType = {$leaveType} AND confirmedYN = 1), 0) AS accrued, isPaidLeave, t5.description 
                      FROM `srp_employeesdetails` t1 LEFT JOIN `srp_erp_leavegroup` t2 ON t1.leaveGroupID = t2.leaveGroupID 
                      LEFT JOIN `srp_erp_leavegroupdetails` AS t3 ON t2.leaveGroupID = t3.leaveGroupID 
                      LEFT JOIN srp_erp_leavetype AS t4 ON t4.leaveTypeID = t3.leaveTypeID JOIN srp_erp_leavepolicymaster t5 ON t5.policyMasterID = t3.policyMasterID 
                      WHERE t3.leaveTypeID = {$leaveType} AND EIdNo = {$empID}";
        } else {
            $isCarryForwardStr = $isCarryForwardStr2 = '';
            if ($policyMasterID == 1) {
                $isCarryForward = $this->db->query("SELECT isCarryForward FROM srp_erp_leavegroupdetails  t1
                JOIN srp_employeesdetails t2 ON t1.leaveGroupID=t2.leaveGroupID
                WHERE leaveTypeID={$leaveType}  AND EIdNo={$empID}")->row('isCarryForward');

                if ($isCarryForward == 0) {
                    $isCarryForwardStr = " AND `year`='" . date('Y') . "'";
                    $isCarryForwardStr2 = " AND year(startDate) = '" . date('Y') . "'";
                }
            }

            $qry3 = "SELECT *, (entitled - leaveTaken) AS balance FROM ( 
                         SELECT t3.policyMasterID,
                         IFNULL( (SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail 
                           LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID 
                           WHERE empID = {$empID} AND leaveType = {$leaveType} AND confirmedYN = 1 {$isCarryForwardStr}), 0
                         ) AS entitled, 
                         IFNULL( (SELECT SUM(days) FROM srp_erp_leavemaster WHERE empID = {$empID} AND leaveTypeID = {$leaveType} 
                           AND approvedYN = 1 {$isCarryForwardStr2}), 0
                         ) AS leaveTaken, policyDescription, isPaidLeave, t5.description 
                         FROM srp_employeesdetails t1 
                         LEFT JOIN `srp_erp_leavegroup` t2 ON t1.leaveGroupID = t2.leaveGroupID 
                         LEFT JOIN `srp_erp_leavegroupdetails` AS t3 ON t1.leaveGroupID = t3.leaveGroupID 
                         LEFT JOIN srp_erp_leavepolicymaster t4 ON t4.policyMasterID = t3.policyMasterID 
                         LEFT JOIN srp_erp_leavetype AS t5 ON t5.leaveTypeID = t3.leaveTypeID WHERE t3.leaveTypeID = {$leaveType} AND EIdNo = {$empID} 
                     ) dataTB";
        }

        $leaveDet = $this->db->query($qry3)->row_array();
        return $leaveDet;
    }


    function employeeLeave_details($masterID)
    {
        return $this->db->query("SELECT IFNULL(approvalComments,'') as approvalComments,leaveAvailable,leaveMasterID, empID, srp_erp_leavemaster.leaveTypeID,
                                 IF(policyMasterID = 2, DATE_FORMAT(startDate, '%Y-%m-%d %h:%i %p'), DATE_FORMAT(startDate, '%Y-%m-%d')) AS startDate, currentLevelNo,
                                 IF(policyMasterID = 2, DATE_FORMAT(endDate, '%Y-%m-%d %h:%i %p'), DATE_FORMAT(endDate, '%Y-%m-%d')) AS endDate, days, hours, ishalfDay,
                                 documentCode, serialNo, entryDate, comments, isCalenderDays, nonWorkingDays, workingDays, leaveGroupID, isAttendance, policyMasterID,
                                 confirmedYN, confirmedByEmpID, confirmedByName, confirmedDate, approvedYN, approvedDate, approvedbyEmpID, approvedbyEmpName, coveringEmpID,
                                 srp_erp_leavemaster.companyID, srp_erp_leavemaster.companyCode, description, applicationType, requestForCancelYN, cancelledYN
                                 FROM srp_erp_leavemaster
                                 LEFT JOIN srp_erp_leavetype on srp_erp_leavetype.leaveTypeID=srp_erp_leavemaster.leaveTypeID
                                 WHERE srp_erp_leavemaster.leaveMasterID={$masterID}")->row_array();
    }


    function delete_empLeave()
    {
        $masterID = $this->input->post('deleteID');
        $det = $this->employeeLeave_details($masterID);

        if ($det['approvedYN'] == 1) {
            return array('e', 'This leave application is Approved');
        } else {
            $this->db->trans_start();
            $this->db->where('leaveMasterID', $masterID)->delete('srp_erp_leavemaster');

            /*** Delete accrual leave ***/
            $this->db->where('companyID', current_companyID());
            $this->db->where('leaveMasterID', $masterID);
            $this->db->delete('srp_erp_leaveaccrualmaster');

            $this->db->where('leaveMasterID', $masterID);
            $this->db->delete('srp_erp_leaveaccrualdetail');

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Failed delete [' . $det['documentCode'] . ' ]');
            } else {
                $this->db->trans_commit();
                return array('s', 'Successfully Deleted [' . $det['documentCode'] . ' ]');
            }
        }
    }

    function get_emp_leaveDet_paySheetPrint($empID, $payrollData)
    {
        $y = $payrollData['payrollYear'];
        $m = $payrollData['payrollMonth'];

        $startDate = $y . '-01-01';
        $y += 1;
        $endDate = $y . '-01-01';

        $startMonth = $y . '-' . '0' . $m . '-01';

        $endMonth = date("Y-m-t", strtotime($startMonth));


        $qry = "SELECT t2.description, SUM(noOfDays) AS entitled, policyDescription, IFNULL((SELECT SUM(days)
         FROM srp_erp_leavemaster WHERE empID = {$empID} AND srp_erp_leavemaster.leaveTypeID = t1.leaveTypeID AND approvedYN = 1 AND startDate >= '{$startMonth}' AND endDate <= '{$endMonth}'), 0) AS leaveTaken,
          IFNULL((SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail 
          LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID WHERE srp_erp_leaveaccrualmaster.year <= {$y} AND srp_erp_leaveaccrualmaster.month <= {$m} AND empID = {$empID} AND confirmedYN = 1 AND srp_erp_leaveaccrualdetail.leaveType = t1.leaveTypeID), 0) AS accrued, isPaidLeave, (SUM(noOfDays) + IFNULL((SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID WHERE srp_erp_leaveaccrualmaster.year <= {$y} AND srp_erp_leaveaccrualmaster.month <= {$m} AND empID = {$empID} AND confirmedYN = 1 AND srp_erp_leaveaccrualdetail.leaveType = t1.leaveTypeID), 0)) - IFNULL((SELECT SUM(days) FROM srp_erp_leavemaster WHERE empID = {$empID} AND srp_erp_leavemaster.leaveTypeID = t1.leaveTypeID AND startDate >= '{$startMonth}' AND endDate <= '{$endMonth}'), 0) AS days FROM `srp_employeesdetails` LEFT JOIN `srp_erp_leavegroupdetails` AS t1 ON t1.leaveGroupID = srp_employeesdetails.leaveGroupID LEFT JOIN srp_erp_leavetype AS t2 ON t1.leaveTypeID = t2.leaveTypeID JOIN srp_erp_leavepolicymaster t3 ON t2.policyID = t3.policyMasterID WHERE EIdNo = {$empID} GROUP BY t2.leaveTypeID";

        // $qry="SELECT description, t3.policyDescription, days, (SELECT sum(days) FROM srp_erp_leavemaster  WHERE empID={$empID} AND approvedYN=1 AND leaveTypeID=t1.leaveTypeID AND endDate >= '$startDate' AND endDate < '$endDate' ) AS leaveTaken FROM srp_erp_leaveentitled AS t1 JOIN srp_erp_leavetype AS t2 ON t1.leaveTypeID = t2.leaveTypeID JOIN srp_erp_leavepolicymaster AS t3 ON t3.policyMasterID = t2.policyID WHERE empID={$empID} ";

        // echo $qry="SELECT t2.description, SUM(noOfDays) AS entitled, policyDescription, IFNULL((SELECT SUM(days) FROM srp_erp_leavemaster WHERE empID = {$empID} AND srp_erp_leavemaster.leaveTypeID=t1.leaveTypeID  AND approvedYN=1), 0) AS leaveTaken, IFNULL((SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID WHERE empID = {$empID} AND confirmedYN = 1 AND srp_erp_leaveaccrualdetail.leaveType=t1.leaveTypeID ), 0) AS accrued, isPaidLeave, (SUM(noOfDays) + IFNULL((SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail LEFT JOIN `srp_erp_leaveaccrualmaster` ON srp_erp_leaveaccrualdetail.leaveaccrualMasterID = srp_erp_leaveaccrualmaster.leaveaccrualMasterID WHERE empID = {$empID} AND confirmedYN = 1 AND srp_erp_leaveaccrualdetail.leaveType=t1.leaveTypeID), 0)) - IFNULL((SELECT SUM(days) FROM srp_erp_leavemaster WHERE empID = {$empID} AND  srp_erp_leavemaster.leaveTypeID=t1.leaveTypeID), 0) AS days FROM `srp_employeesdetails` LEFT JOIN `srp_erp_leavegroupdetails` AS t1 ON t1.leaveGroupID = srp_employeesdetails.leaveGroupID LEFT JOIN srp_erp_leavetype AS t2 ON t1.leaveTypeID = t2.leaveTypeID JOIN srp_erp_leavepolicymaster t3 ON t2.policyID = t3.policyMasterID WHERE EIdNo = {$empID} GROUP BY t2.leaveTypeID";

        return $this->db->query($qry)->result_array();
//        return false;

    }

    function savePresentType()
    {
        $attType = $this->input->post('attType[]');
        $data = array();

        //echo '<pre>';print_r($attType); echo '</pre>'; die();

        foreach ($attType as $key => $row) {
            $data[$key]['SysPresentTypeID'] = $row;
            $data[$key]['SchMasterId'] = current_schMasterID();
            $data[$key]['BranchID'] = current_schBranchID();
            $data[$key]['Erp_companyID'] = current_companyID();
            $data[$key]['CreatedPC'] = current_pc();
            $data[$key]['CreatedUserName'] = current_employee();
            $data[$key]['CreatedDate'] = current_date();
        }

        $this->db->insert_batch('srp_attpresenttype', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records inserted successfully');
        } else {
            return array('e', 'Error in insert record');
        }
    }

    function delete_attendanceTypes()
    {
        $deleteID = $this->input->post('hidden-id');

        $this->db->trans_start();
        $this->db->where('AttPresentTypeID', $deleteID)->delete('srp_attpresenttype');
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Delete process is failed');
        } else {
            $this->db->trans_commit();
            return array('s', ' Process Done.');
        }
    }

    function new_attendance()
    {

        $date_format_policy = date_format_policy();
        $invDueDate = $this->input->post('attendanceDate');
        $attendanceDate = input_format_date($invDueDate, $date_format_policy);
        //$attendanceDate = $this->input->post('attendanceDate');
        $attendanceTime = $this->input->post('attendanceTime');
        $convertedTime = date('H:i:s', strtotime($attendanceTime));

        $isAlreadyProcessed = $this->db->query("SELECT * FROM srp_empattendancemaster WHERE Erp_companyID=" . current_companyID() . "
                                                AND AttDate='{$attendanceDate}'")->row_array();

        if (!empty($isAlreadyProcessed)) {
            return array('e', 'Already there is a record on this date');
            exit;
        }


        $data['AttDate'] = $attendanceDate;
        $data['AttTime'] = $convertedTime;
        $data['SchMasterId'] = current_schMasterID();
        $data['branchID'] = current_schBranchID();
        $data['Erp_companyID'] = current_companyID();
        $data['isAttClosed'] = 0;
        $data['DoneBy'] = current_userID();
        $data['CreatedPC'] = current_pc();
        $data['CreatedUserName'] = current_employee();
        $data['CreatedDate'] = current_date();

        $this->db->insert('srp_empattendancemaster', $data);
        if ($this->db->affected_rows() > 0) {
            $id = $this->db->insert_id();
            return array('s', 'Records inserted successfully', $id);
        } else {
            return array('e', 'Error in insert record');
        }
    }

    function getAttMasterData($attID)
    {
        $companyID = current_companyID();
        $data = $this->db->query("SELECT * FROM srp_empattendancemaster WHERE  EmpAttMasterID={$attID} AND Erp_companyID={$companyID}")->row_array();
        return $data;
    }

    function delete_attendanceMaster()
    {
        $deleteID = $this->input->post('hidden-id');

        $this->db->trans_start();
        $this->db->where('EmpAttMasterID', $deleteID)->delete('srp_empattendancemaster');
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Delete process is failed');
        } else {
            $this->db->trans_commit();
            return array('s', ' Process Done.');
        }
    }

    function get_attendanceEmployees($attID)
    {
        //$concatenate = 'IFNULL(Ename1,"") , " ", IFNULL(Ename2,""), " ",  IFNULL(Ename3,"") , " ",  IFNULL(Ename4,"") ';
        $concatenate = 'IFNULL(Ename2,"")';
        $companyID = current_companyID();

        $data = $this->db->query("SELECT EIdNo, ECode,  CONCAT({$concatenate}) empName,  EmpImage, isAttended, AttTime, AttPresentTypeID, AttPresentRemarks
                                  FROM srp_employeesdetails AS emp LEFT JOIN srp_empattendance AS att ON att.EmpID = emp.EIdNo AND att.EmpAttMasterID={$attID}
                                  WHERE emp.Erp_companyID={$companyID}")->result();

        return $data;
    }


    /*Start of Shift Master*/

    function save_attendanceDetails()
    {

        $attendMasterID = $this->input->post('attendMasterID');
        $isComplete = $this->input->post('isComplete');
        $attEmp = $this->input->post('att-emp[]');
        $isAttended = $this->input->post('isAttended[]');
        $attType = $this->input->post('att-type[]');
        $attTime = $this->input->post('att-time[]');
        $remarks = $this->input->post('remarks[]');
        $data = array();

        //echo '<pre>';print_r( $_POST ); echo '</pre>'; die();

        $this->db->trans_start();

        $this->db->where('EmpAttMasterID', $attendMasterID)->delete('srp_empattendance');

        foreach ($attEmp as $key => $emp) {
            if (!empty($isAttended)) {
                $isThisEmpAttended = (in_array($emp, $isAttended)) ? 1 : '';
            } else {
                $isThisEmpAttended = 0;
            }


            $data[$key]['EmpAttMasterID'] = $attendMasterID;
            $data[$key]['EmpID'] = $emp;
            $data[$key]['isAttended'] = $isThisEmpAttended;
            $data[$key]['AttTime'] = ($attType[$key] == 4) ? '00:00:00' : $attTime[$key]; // Attendance type ID => 4 is Absents
            $data[$key]['AttPresentTypeID'] = $attType[$key];
            $data[$key]['AttPresentRemarks'] = $remarks[$key];
            $data[$key]['CreatedPC'] = current_pc();
            $data[$key]['CreatedUserName'] = current_employee();
            $data[$key]['CreatedDate'] = current_date();
        }

        if (!empty($isComplete)) {
            $upData = array(
                'isAttClosed' => 1,
                'ModifiedUserName' => current_employee(),
                'ModifiedPC' => current_pc()
            );
            $this->db->where('EmpAttMasterID', $attendMasterID)->update('srp_empattendancemaster', $upData);
        }

        $this->db->insert_batch('srp_empattendance', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in attendance process.');
        } else {
            $this->db->trans_commit();
            return array('s', 'Attendance Records inserted successfully');
        }
    }

    function saveShiftMaster()
    {
        $description = $this->input->post('shiftDescription');
        $isExistShiftDetails = $this->isExistShiftDetails($description);

        if (!empty($isExistShiftDetails)) {
            return array('w', 'This description already exists.');
        } else {
            $data = array(
                'description' => $description,
                'companyID' => current_companyID(),
                'companyCode' => current_companyCode(),
                'createdPCID' => current_pc(),
                'createdUserID' => current_userID(),
                'createdUserName' => current_employee(),
                'createdUserGroup' => current_user_group(),
                'createdDateTime' => current_date()
            );

            $this->db->trans_start();
            $this->db->insert('srp_erp_pay_shiftmaster', $data);
            $shiftID = $this->db->insert_id();

            $onTime_arr = $this->input->post('onTime[]');
            $offTime_arr = $this->input->post('offTime[]');
            $isWeekend_arr = $this->input->post('isWeekend[]');
            $masterDayID_arr = $this->input->post('masterDayID[]');


            $data_arr = array();
            foreach ($masterDayID_arr as $key => $row) {
                $weekDayNo = null;
                switch ($row) {
                    case 1:
                        $weekDayNo = 6;
                        break;  //Sunday
                    case 2:
                        $weekDayNo = 0;
                        break;  //Monday
                    case 3:
                        $weekDayNo = 1;
                        break;
                    case 4:
                        $weekDayNo = 2;
                        break;
                    case 5:
                        $weekDayNo = 3;
                        break;
                    case 6:
                        $weekDayNo = 4;
                        break; //Friday
                    case 7:
                        $weekDayNo = 5;
                        break; //Saturday
                }
                $onTime = ($isWeekend_arr[$key] == 1) ? null : date('H:i:s', strtotime($onTime_arr[$key]));
                $offTime = ($isWeekend_arr[$key] == 1) ? null : date('H:i:s', strtotime($offTime_arr[$key]));

                $data_arr[$key]['shiftID'] = $shiftID;
                $data_arr[$key]['dayID'] = $row;
                $data_arr[$key]['weekDayNo'] = $weekDayNo;
                $data_arr[$key]['onDutyTime'] = $onTime;
                $data_arr[$key]['offDutyTime'] = $offTime;
                $data_arr[$key]['isWeekend'] = $isWeekend_arr[$key];
                $data_arr[$key]['companyID'] = current_companyID();
                $data_arr[$key]['companyCode'] = current_companyCode();
                $data_arr[$key]['createdPCID'] = current_pc();
                $data_arr[$key]['createdUserID'] = current_userID();
                $data_arr[$key]['createdUserName'] = current_employee();
                $data_arr[$key]['createdUserGroup'] = current_user_group();
                $data_arr[$key]['createdDateTime'] = current_date();
            }

            $this->db->insert_batch('srp_erp_pay_shiftdetails', $data_arr);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Shift created successfully.', $shiftID);
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in shift create process');
            }
        }
    }

    function isExistShiftDetails($description)
    {
        $companyID = current_companyID();
        return $this->db->query("SELECT shiftID FROM srp_erp_pay_shiftmaster WHERE Description='$description' AND companyID={$companyID}")->row('shiftID');
    }

    function fetch_shiftDetails()
    {
        $shiftID = $this->input->post('shiftID');
        $companyID = current_companyID();
        $query = $this->db->query("SELECT dayID, DATE_FORMAT(onDutyTime, \"%h:%i %p\") AS onDutyTime, DATE_FORMAT(offDutyTime, \"%h:%i %p\") AS offDutyTime, shiftDetailID,isHalfDay
                                   FROM srp_erp_pay_shiftdetails WHERE shiftID={$shiftID} AND companyID={$companyID}")->result_array();

        return array('s', $query);

    }

    function updateShiftMaster()
    {
        $description = $this->input->post('shiftDescription');
        $editID = $this->input->post('editID');

        $data = array(
            'description' => $description,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_employee(),
            'modifiedDateTime' => current_date()
        );

        $isExistShiftDetails = $this->isExistShiftDetails($description);

        if (!empty($isExistShiftDetails)) {
            if ($isExistShiftDetails != $editID) {
                return array('w', 'This description already exists.');
                die();
            }
        }

        $this->db->trans_start();

        $this->db->where('shiftID', $editID);
        $this->db->update('srp_erp_pay_shiftmaster', $data);

        $onTime_arr = $this->input->post('onTime[]');
        $offTime_arr = $this->input->post('offTime[]');
        $isWeekend_arr = $this->input->post('isWeekend[]');
        $masterDayID_arr = $this->input->post('masterDayID[]');
        $shiftDetID_arr = $this->input->post('shiftDetID[]');
        $isHalfDay_arr = $this->input->post('isHalfDay[]');


        $data_arr = array();
        foreach ($masterDayID_arr as $key => $row) {
            $onTime = ($isWeekend_arr[$key] == 1) ? null : date('H:i:s', strtotime($onTime_arr[$key]));
            $offTime = ($isWeekend_arr[$key] == 1) ? null : date('H:i:s', strtotime($offTime_arr[$key]));

            $data_arr[$key]['shiftDetailID'] = $shiftDetID_arr[$key];
            $data_arr[$key]['onDutyTime'] = $onTime;
            $data_arr[$key]['offDutyTime'] = $offTime;
            $data_arr[$key]['isWeekend'] = $isWeekend_arr[$key];
            $data_arr[$key]['isHalfDay'] = $isHalfDay_arr[$key];
            $data_arr[$key]['modifiedPCID'] = current_pc();
            $data_arr[$key]['modifiedUserID'] = current_userID();
            $data_arr[$key]['modifiedUserName'] = current_employee();
            $data_arr[$key]['modifiedDateTime'] = current_date();
        }

        $this->db->update_batch('srp_erp_pay_shiftdetails', $data_arr, 'shiftDetailID');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Shift detail updated successfully.');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in shift detail update process');
        }
    }

    function deleteShiftMaster()
    {
        $deleteID = $this->input->post('deleteID');

        $this->db->trans_start();
        $this->db->where('shiftID', $deleteID)->delete('srp_erp_pay_shiftmaster');
        $this->db->where('shiftID', $deleteID)->delete('srp_erp_pay_shiftdetails');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Records deleted successfully');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in deleting process');
        }
    }

    /*End of Shift Master*/


    /*Start of Attendance review*/

    function save_ShiftEmp()
    {

        $masterID = trim($this->input->post('masterID'));
        $employees = trim($this->input->post('employees'));
        $emp_arr = json_decode($employees);


        $data = array();
        foreach ($emp_arr as $key => $arr) {
            $data[$key]['shiftID'] = $masterID;
            $data[$key]['empID'] = $arr;
            $data[$key]['companyID'] = current_companyID();
            $data[$key]['companyCode'] = current_companyCode();
            $data[$key]['createdPCID'] = current_pc();
            $data[$key]['createdUserID'] = current_userID();
            $data[$key]['createdUserName'] = current_employee();
            $data[$key]['createdUserGroup'] = current_user_group();
            $data[$key]['createdDateTime'] = current_date();
        }


        $this->db->insert_batch('srp_erp_pay_shiftemployees', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Employees successfully assign to the shift.');
        } else {
            return array('e', 'Error in employees assigning to the shift.');
        }
    }

    function getMachineID_notAssignedEmployees($begin, $start)
    {
        $companyID = current_companyID();
        /*"SELECT EIdNo, ECode, Ename1, Erp_companyID FROM srp_employeesdetails  WHERE Erp_companyID={$companyID} AND
                                  empMachineID NOT IN (
                                      SELECT empMachineID FROM srp_erp_pay_empattendancetemptable WHERE companyID={$companyID} GROUP BY empMachineID
                                  )";*/
        /*$qry = "SELECT EidNo, ECode, Ename1, srp_erp_pay_empattendancereview.* FROM `srp_erp_pay_empattendancereview` LEFT JOIN (SELECT * FROM srp_employeesdetails) t ON empID = EidNo WHERE attendanceDate BETWEEN '{$begin}' AND '{$start}' AND companyID = {$companyID} AND machineID = 0 GROUP BY empID having machineID=0";*/
        $qry = "select EidNo, ECode, Ename2 as Ename1 from srp_employeesdetails WHERE isSystemAdmin <> 1 AND isDischarged <> 1 AND Erp_companyID={$companyID} AND ( empMachineID ='' OR empMachineID=0 )";
        $data = $this->db->query($qry)->result_array();
        return $data;
    }

    function getShift_notAssignedEmployees()
    {
        $companyID = current_companyID();

        $data = $this->db->query("SELECT EIdNo, ECode, Ename1, Erp_companyID FROM srp_employeesdetails  WHERE Erp_companyID={$companyID} AND isSystemAdmin <> 1 AND  isDischarged <> 1  AND 
                                  EIdNo NOT IN (
                                      SELECT empID FROM srp_erp_pay_shiftemployees WHERE companyID={$companyID} GROUP BY empID
                                  )")->result_array();
        return $data;
    }

    function get_attendanceData($dateRange)
    {
        $companyID = current_companyID();
        $frmDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');
        $uniqueKey = current_userID() . '' . current_companyID() . '' . rand(2, 500) . '' . date('YmdHis');

        $date_arr = array();
        foreach ($dateRange as $key => $date) {
            $date_arr[$key]['actualDate'] = $date->format("Y-m-d");
            $date_arr[$key]['uniqueID'] = $uniqueKey;
            $date_arr[$key]['companyID'] = $companyID;
        }
        $this->db->insert_batch('srp_erp_pay_empattendancedaterangetemp', $date_arr);
        $sql = "SELECT EIdNo, ECode, Ename1, dateRangeTB.actualDate, attInTimeTB.empMachineID, attInTimeTB.attDate,
                                  DATE_FORMAT(attInTimeTB.attTime, '%h:%i %p') attTime, DATE_FORMAT(attOffTimeTB.attTime, '%h:%i %p') offTime,
                                  DATE_FORMAT(shiftDet.onDutyTime, '%h:%i %p') onDutyTime, DATE_FORMAT(shiftDet.offDutyTime, '%h:%i %p') offDutyTime,
                                  shiftDet.isWeekend, floorDescription, emp.floorID, IF(IFNULL(leaveMasterID,0),1,0) AS isOnLeave
                                  FROM srp_employeesdetails AS emp
                                  JOIN  srp_erp_pay_empattendancedaterangetemp AS dateRangeTB
                                  LEFT JOIN srp_erp_pay_floormaster AS floorMaster ON floorMaster.floorID = emp.floorID AND floorMaster.companyID ={$companyID}
                                  LEFT JOIN (
                                      SELECT empMachineID, attDate, attTime FROM srp_erp_pay_empattendancetemptable WHERE companyID={$companyID}
                                      AND attDate BETWEEN '{$frmDate}' AND '{$toDate}'
                                      GROUP BY empMachineID, attDate ORDER BY attDateTime ASC
                                  ) AS attInTimeTB ON emp.empMachineID = attInTimeTB.empMachineID AND attDate=dateRangeTB.actualDate
                                  LEFT JOIN (
                                       SELECT empMachineID, attDate, attTime FROM srp_erp_pay_empattendancetemptable WHERE companyID={$companyID}
                                       AND attDate BETWEEN '{$frmDate}' AND '{$toDate}'
                                  )AS attOffTimeTB ON attOffTimeTB.attDate = attInTimeTB.attDate
                                  AND emp.empMachineID = attOffTimeTB.empMachineID AND attOffTimeTB.attTime > attInTimeTB.attTime
                                  LEFT JOIN (
                                      SELECT * FROM srp_erp_pay_shiftemployees WHERE companyID={$companyID}
                                  ) AS empShift ON empShift.empID = emp.EIdNo
                                  LEFT JOIN (
                                      SELECT * FROM srp_erp_pay_shiftdetails WHERE companyID={$companyID}
                                  ) AS shiftDet ON shiftDet.shiftID = empShift.shiftID AND shiftDet.weekDayNo=WEEKDAY(dateRangeTB.actualDate)
                                  LEFT JOIN(
                                      SELECT leaveMasterID,empID, startDate, endDate FROM srp_erp_leavemaster WHERE companyID={$companyID} AND approvedYN=1
                                  ) AS leaveTB ON leaveTB.empID = emp.EIdNo AND dateRangeTB.actualDate BETWEEN leaveTB.startDate AND leaveTB.endDate
                                  WHERE Erp_companyID={$companyID} AND actualDate BETWEEN '{$frmDate}' AND '{$toDate}' AND dateRangeTB.uniqueID={$uniqueKey}
                                  ORDER BY actualDate, Ename1 ASC";


        $data = $this->db->query($sql)->result_array();
        $this->db->where('uniqueID', $uniqueKey)->delete('srp_erp_pay_empattendancedaterangetemp');

        return $data;
    }

    function save_attendanceReviewData()
    {

        $empArr = $this->input->post('empID[]');
        $machineID = $this->input->post('machineID[]');
        $floorID = $this->input->post('floorID[]');
        $attDate = $this->input->post('attDate[]');
        $clock_in = $this->input->post('clock-in[]');
        $clock_out = $this->input->post('clock-out[]');
        $onDuty = $this->input->post('onDuty[]');
        $offDuty = $this->input->post('offDuty[]');
        $att_type = $this->input->post('att-type[]');
        $h_lateHours = $this->input->post('h_lateHours[]');
        $m_lateHours = $this->input->post('m_lateHours[]');
        $h_earlyHours = $this->input->post('h_earlyHours[]');
        $m_earlyHours = $this->input->post('m_earlyHours[]');
        $h_OTHours = $this->input->post('h_OTHours[]');
        $m_OTHours = $this->input->post('m_OTHours[]');
        $weekend = $this->input->post('weekend[]');
        $holiday = $this->input->post('holiday[]');
        $nDaysOT = $this->input->post('nDaysOT[]');
        $h_weekendOT = $this->input->post('h_weekendOT[]');
        $m_weekendOT = $this->input->post('m_weekendOT[]');
        $h_holidayOT = $this->input->post('h_holidayOT[]');
        $m_holidayOT = $this->input->post('m_holidayOT[]');
        $companyID = current_companyID();
        $companyCode = current_companyCode();

        $data = array();

        foreach ($empArr as $key => $emp) {
            $data[$key]['empID'] = $emp;
            $data[$key]['machineID'] = $machineID[$key];
            $data[$key]['floorID'] = $floorID[$key];
            $data[$key]['attendanceDate'] = $attDate[$key];
            $data[$key]['onDuty'] = $onDuty[$key];
            $data[$key]['offDuty'] = $offDuty[$key];
            $data[$key]['presentTypeID'] = $att_type[$key];
            $data[$key]['checkIn'] = $clock_in[$key];
            $data[$key]['checkOut'] = $clock_out[$key];

            $total_lateHours = ($h_lateHours[$key] * 60) + $m_lateHours[$key];
            $data[$key]['lateHours'] = $total_lateHours;

            $total_earlyHours = ($h_earlyHours[$key] * 60) + $m_earlyHours[$key];
            $data[$key]['earlyHours'] = $total_earlyHours;

            $total_OTHours = ($h_OTHours[$key] * 60) + $m_OTHours[$key];
            $data[$key]['OTHours'] = $total_OTHours;

            $data[$key]['mustCheckIn'] = $clock_out[$key];
            $data[$key]['mustCheckOut'] = $clock_out[$key];

            $data[$key]['weekend'] = $weekend[$key];
            $data[$key]['holiday'] = $holiday[$key];
            $data[$key]['NDaysOT'] = $nDaysOT[$key];

            $total_weekendOTHours = ($h_weekendOT[$key] * 60) + $m_weekendOT[$key];
            $data[$key]['weekendOTHours'] = $total_weekendOTHours;

            $total_holidayOTHours = ($h_holidayOT[$key] * 60) + $m_holidayOT[$key];
            $data[$key]['holidayOTHours'] = $total_holidayOTHours;
            $data[$key]['companyID'] = $companyID;
            $data[$key]['companyCode'] = $companyCode;
        }


        //echo count($_POST).'<pre>';print_r($data); echo '</pre>'; die();

        $this->db->trans_start();
        $this->db->insert_batch('srp_erp_pay_empattendancereview', $data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Failed to Update payroll');
        } else {
            $this->db->trans_commit();

            return array('s', ' generated successfully');
        }


        /*echo '<pre>';
        print_r($data);
        echo '</pre>';
        die();*/
    }

    function get_attendanceData2()
    {

        $companyID = current_companyID();
        $frmDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');
        $floor = $this->input->post('floorID');
        $floorID = implode(", ", $floor);


        $companyID = current_companyID();
        $qry = "SELECT isWeekEndDay,isCheckin,empID,ECode,Ename1,normalTime,normalDay, Ename2,empMachineID, floorDescription, ID, machineID, srp_erp_pay_empattendancereview.floorID, attendanceDate, presentTypeID,   DATE_FORMAT(checkIn, '%h:%i %p') checkIn, DATE_FORMAT(checkOut, '%h:%i %p') checkOut, DATE_FORMAT(onDuty, '%h:%i %p') onDuty, DATE_FORMAT(offDuty, '%h:%i %p') offDuty, lateHours, earlyHours, OTHours,mustCheck, weekend, holiday, NDaysOT, weekendOTHours, holidayOTHours,realTime,approvedComment FROM srp_erp_pay_empattendancereview LEFT JOIN `srp_employeesdetails` ON srp_erp_pay_empattendancereview.empID = srp_employeesdetails.EIdNo LEFT JOIN `srp_erp_pay_floormaster` ON srp_erp_pay_floormaster.floorID = srp_erp_pay_empattendancereview.floorID WHERE attendanceDate BETWEEN '{$frmDate}' AND '{$toDate}' AND srp_erp_pay_empattendancereview.companyID = {$companyID}  AND approvedYN=0 AND srp_erp_pay_empattendancereview.floorID IN($floorID)";
        $data = $this->db->query($qry)->result_array();

        return $data;
    }

    /*End of Attendance review*/

    function get_attendanceData1()
    {
        $companyID = current_companyID();
        $data = $this->db->query("SELECT EIdNo, ECode, Ename1, tem.empMachineID, tem.attDate,
                                  DATE_FORMAT(tem.attTime, \"%h:%i %p\") AS attTime,
                                  DATE_FORMAT(tem2.attTime, \"%h:%i %p\") AS offTime,
                                  DATE_FORMAT(onDutyTime, \"%h:%i %p\") AS onDutyTime,
                                  DATE_FORMAT(offDutyTime, \"%h:%i %p\") AS offDutyTime
                                  FROM srp_erp_pay_empattendancetemptable AS tem
                                  LEFT JOIN srp_employeesdetails AS emp ON emp.empMachineID = tem.empMachineID AND emp.Erp_companyID={$companyID}
                                  LEFT JOIN srp_erp_pay_empattendancetemptable AS tem2 ON tem2.empMachineID = tem.empMachineID AND tem2.companyID={$companyID}
                                  AND tem2.attDate = tem.attDate AND tem2.attTime > tem.attTime
                                  LEFT JOIN srp_erp_pay_shiftemployees AS shiftEmp ON shiftEmp.empID = emp.EIdNo AND shiftEmp.companyID={$companyID}
                                  LEFT JOIN srp_erp_pay_shiftdetails AS shiftDet ON shiftDet.shiftID = shiftEmp.shiftID  AND shiftDet.weekDayNo=WEEKDAY(tem.attDate)
                                  INNER JOIN (
                                    SELECT DISTINCT (attDate) AS attDateDistinc FROM srp_erp_pay_empattendancetemptable WHERE companyID={$companyID}
                                  ) dt ON dt.attDateDistinc = tem.attDate
                                  WHERE tem.companyID={$companyID} AND tem.attDate BETWEEN '2015-01-01' AND '2015-01-31'
                                  GROUP BY tem.empMachineID, tem.attDate
                                  ORDER BY tem.attDateTime ASC")->result_array();

        return $data;
    }

    function fetch_employees_typeAhead()
    {

        $empID = $this->input->get('empID');
        $search_string = "%" . $this->input->get('query') . "%";
        $companyID = current_companyID();
        $dataArr = array();
        $dataArr2 = array();

        $data = $this->db->query("SELECT EIdNo, Ename1, CONCAT(ECode,' _ ', Ename1) AS nameWithCode
                                  FROM srp_employeesdetails WHERE Erp_companyID={$companyID} AND EIdNo='{$empID}'
                                  UNION
                                  SELECT EIdNo, Ename1, CONCAT(ECode,' _ ', Ename1) AS nameWithCode
                                  FROM srp_employeesdetails WHERE (Ename1 LIKE '{$search_string }' OR ECode LIKE '{$search_string }')
                                  AND empConfirmedYN=1 AND isDischarged=0 AND Erp_companyID={$companyID} LIMIT 19")->result_array();

        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array(
                    'value' => $val["nameWithCode"],
                    'data' => $val['EIdNo'],
                );
            }
        }

        $dataArr2['suggestions'] = $dataArr;


        return $dataArr2;
    }

    function save_social_insurance()
    {
        $str = $this->input->post('socialInsuranceMasterID');
        $explod = explode("_", $str);
        $socialInsuranceMasterID = $explod['0'];
        $socialInsuranceType = $explod['1'];
        //$socialInsuranceMasterID = $this->input->post('socialInsuranceMasterID');
        $socialInsuranceNumber = $this->input->post('socialInsuranceNumber');
        $empID = $this->input->post('empID');
        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $createdUserGroup = current_user_group();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $current_date = current_date();
        $current_user = current_user();

        if ($socialInsuranceType == "sso") {
            $data = array(
                'socialInsuranceMasterID' => $socialInsuranceMasterID,
                'empID' => $empID,
                'socialInsuranceNumber' => $socialInsuranceNumber,
                'companyID' => $companyID,
                'companyCode' => $companyCode,
                'createdUserGroup' => $createdUserGroup,
                'createdPCID' => $createdPCID,
                'createdUserID' => $createdUserID,
                'createdDateTime' => $current_date,
                'createdUserName' => $current_user,
                'timestamp' => $current_date,
            );
        } else if ($socialInsuranceType == "Payee") {
            $data = array(
                'payeeID' => $socialInsuranceMasterID,
                'empID' => $empID,
                'socialInsuranceNumber' => $socialInsuranceNumber,
                'companyID' => $companyID,
                'companyCode' => $companyCode,
                'createdUserGroup' => $createdUserGroup,
                'createdPCID' => $createdPCID,
                'createdUserID' => $createdUserID,
                'createdDateTime' => $current_date,
                'createdUserName' => $current_user,
                'timestamp' => $current_date,
            );
        }
        if ($socialInsuranceType == "sso") {
            $isAvailable = $this->db->query("SELECT * FROM srp_erp_socialinsurancedetails WHERE socialInsuranceMasterID='{$socialInsuranceMasterID}' AND empID='{$empID}'")->row_array();
            if ($isAvailable) {
                return array('e', 'Social Insurance Already Exist.', $empID);
            }
        } else if ($socialInsuranceType == "Payee") {
            $isAvailable = $this->db->query("SELECT * FROM srp_erp_socialinsurancedetails WHERE payeeID='{$socialInsuranceMasterID}' AND empID='{$empID}'")->row_array();
            if ($isAvailable) {
                return array('e', 'Social Insurance Already Exist.', $empID);
            }
        }


        $this->db->trans_start();
        $this->db->insert('srp_erp_socialinsurancedetails', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Social Insurance Created Successfully.', $empID);
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error In Social Insurance Creating');
        }

    }

    function delete_si()
    {
        $socialInsuranceDetailID = $this->input->post('socialInsuranceDetailID');
        $this->db->trans_start();
        $delete = $this->db->query("DELETE FROM `srp_erp_socialinsurancedetails` WHERE (`socialInsuranceDetailID`='$socialInsuranceDetailID')");
        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Social Insurance Deleted Successfully.');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error In Social Insurance Deleting');
        }
    }


    /*Natinality*/

    function update_si()
    {
        $pk = $this->input->post('pk');
        $value = $this->input->post('value');
        $empId = $this->input->post('empId');
        $companyID = current_companyID();

        $update = $this->db->query("UPDATE srp_erp_socialinsurancedetails SET socialInsuranceNumber = '{$value}' WHERE socialInsuranceDetailID = '{$pk}' AND empID = '{$empId}' AND companyID = '$companyID' ");

        if ($update) {
            return $this->output
                ->set_content_type('application/html')
                ->set_status_header(200)
                ->set_output('Successfully Updated.');
        } else {
            return $this->output
                ->set_content_type('application/html')
                ->set_status_header(400)
                ->set_output('Updated Failed.');
        }

    }

    function saveNationality()
    {
        $description = $this->input->post('description[]');

        $companyId = current_companyID();
        $availble = $this->db->select('Nationality')
            ->from('srp_nationality')
            ->where('Erp_companyID', $companyId)
            ->get()->result_array();


        $na = array();
        foreach ($availble as $item) {
            $na[] = $item['Nationality'];
        }

        $data = array();
        foreach ($description as $key => $de) {
            if (in_array($de, $na)) {
                continue;
            }

            $data[$key]['Nationality'] = $de;
            $data[$key]['SchMasterId'] = current_schMasterID();
            $data[$key]['branchID'] = current_schBranchID();
            $data[$key]['Erp_companyID'] = current_companyID();
            $data[$key]['CreatedPC'] = current_pc();
            $data[$key]['CreatedUserName'] = current_employee();
            $data[$key]['CreatedDate'] = current_date();
        }

        if (empty($data)) {

        } else {
            $this->db->insert_batch('srp_nationality', $data);
        }
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records inserted successfully');
        } else {
            return array('e', 'Error in insert record');
        }
    }

    function editNationality()
    {
        $description = $this->input->post('nationalityDes');
        $hidden_id = $this->input->post('hidden-id');

        $data = array(
            'Nationality' => $description,
            'ModifiedPC' => current_pc(),
            'ModifiedUserName' => current_employee(),
            'Timestamp' => current_date(),
        );

        $this->db->where('NId', $hidden_id)->update('srp_nationality', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }
    }


    function deleteNationality()
    {
        $hidden_id = $this->input->post('hidden-id');

        $isInUse = $this->db->query("SELECT Nid FROM srp_employeesdetails WHERE Rid={$hidden_id}")->row('Nid');

        if (isset($isInUse)) {
            return array('e', 'This Nationality is in use</br>You can not delete this');
        } else {
            $this->db->where('NId', $hidden_id)->delete('srp_nationality');
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Records deleted successfully');
            } else {
                return array('e', 'Error in deleting process');
            }
        }
    }

    /*Social Insurance*/
    function saveSocialInsurance()
    {
        $sortCode = $this->input->post('sortCode[]');
        $description = $this->input->post('description[]');
        $employee = $this->input->post('employee[]');
        $employer = $this->input->post('employer[]');
        $expenseGlAutoID = $this->input->post('expenseGlAutoID[]');
        $liabilityGlAutoID = $this->input->post('liabilityGlAutoID[]');
        $isSlab = $this->input->post('isSlabHidden[]');
        $ifSlab = $this->input->post('ifSlab[]');

        $companyId = current_companyID();
        $companyCode = current_companyCode();
        $createdUserGroup = current_user_group();
        $current_pc = current_pc();
        $createdUserID = current_userID();
        $current_date = current_date();
        $createdUserName = current_user();

        $SSO_description = join('\',\'', $sortCode);
        $SSO_description = '\'' . $SSO_description . '\'';

        $existItems = $this->db->query("SELECT sortCode FROM srp_erp_socialinsurancemaster WHERE companyID={$companyId} AND
                                      sortCode IN ({$SSO_description})")->result_array();


        if (!empty($existItems)) {
            $str = implode(', ', array_column($existItems, 'sortCode'));
            return ['e', 'Following short codes already exist <br/>' . $str];
            exit;
        }


        $data = array();
        $append = array();
        foreach ($sortCode as $key => $de) {

            $data['sortCode'] = $de;
            $data['Description'] = $description[$key];
            $data['employeeContribution'] = $employee[$key];
            $data['employerContribution'] = $employer[$key];
            $data['expenseGlAutoID'] = $expenseGlAutoID[$key];
            $data['liabilityGlAutoID'] = $liabilityGlAutoID[$key];
            $data['companyID'] = $companyId;
            $data['companyCode'] = $companyCode;
            $data['createdUserGroup'] = $createdUserGroup;
            $data['createdPCID'] = $current_pc;
            $data['createdUserID'] = $createdUserID;
            $data['createdDateTime'] = $current_date;
            $data['createdUserName'] = $createdUserName;
            $data['modifiedPCID'] = $current_pc;
            $data['modifiedUserID'] = $createdUserID;
            $data['modifiedDateTime'] = $current_date;
            $data['modifiedUserName'] = $createdUserName;
            $data['timestamp'] = $current_date;

            $data['isSlabApplicable'] = ($isSlab[$key] == 1) ? 1 : null;
            $data['SlabID'] = ($isSlab[$key] == 1) ? $ifSlab[$key] : null;


            $this->db->insert('srp_erp_socialinsurancemaster', $data);


            $append['description'] = $de;
            $append['socialInsuranceID'] = $this->db->insert_id();
            /*$append['isPayee'] = 0;*/
            $append['companyID'] = $companyId;
            $append['companyCode'] = $companyCode;
            $append['createdUserGroup'] = $createdUserGroup;
            $append['createdPCID'] = $current_pc;
            $append['createdUserID'] = $createdUserID;
            $append['createdDateTime'] = $current_date;
            $append['createdUserName'] = $createdUserName;
            $append['modifiedPCID'] = $current_pc;
            $append['modifiedUserID'] = $createdUserID;
            $append['modifiedDateTime'] = $current_date;
            $append['modifiedUserName'] = $createdUserName;
            $append['timestamp'] = $current_date;

            $this->db->insert('srp_erp_paygroupmaster', $append);
            $payGroupID = $this->db->insert_id();

            $appendFiled['fieldName'] = $de;
            $appendFiled['caption'] = $de;
            $appendFiled['fieldType'] = 'G';
            /*$appendFiled['isCalculate'] = 0;*/
            $appendFiled['payGroupID'] = $payGroupID;


            $appendFiled['companyID'] = $companyId;
            $appendFiled['companyCode'] = $companyCode;
            $appendFiled['createdUserGroup'] = $createdUserGroup;
            $appendFiled['createdPCID'] = $current_pc;
            $appendFiled['createdUserID'] = $createdUserID;
            $appendFiled['createdDateTime'] = $current_date;
            $appendFiled['createdUserName'] = $createdUserName;
            $appendFiled['modifiedPCID'] = $current_pc;
            $appendFiled['modifiedUserID'] = $createdUserID;
            $appendFiled['modifiedDateTime'] = $current_date;
            $appendFiled['modifiedUserName'] = $createdUserName;
            $appendFiled['timestamp'] = $current_date;
            $this->db->insert('srp_erp_pay_templatefields', $appendFiled);

        }

        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records inserted successfully');
        } else {
            return array('e', 'Error in insert record');
        }
    }

    function editSocialInsurance()
    {
        $sortCode = $this->input->post('siSortCode');
        $description = $this->input->post('siDes');
        $employee = $this->input->post('siEmployee');
        $employer = $this->input->post('siEmployer');
        $expenseGlAutoID = $this->input->post('si_expenseGlAutoID');
        $liabilityGlAutoID = $this->input->post('si_liabilityGlAutoID');
        $hidden_id = $this->input->post('hidden-id');
        $siIsSlab = $this->input->post('siIsSlab');//on
        $siSlab = $this->input->post('siSlab');

        $companyId = current_companyID();
        $current_pc = current_pc();
        $createdUserID = current_userID();
        $current_date = current_date();
        $createdUserName = current_user();

        $data = array(
            'sortCode' => $sortCode,
            'Description' => $description,
            'employeeContribution' => $employee,
            'employerContribution' => $employer,
            'expenseGlAutoID' => $expenseGlAutoID,
            'liabilityGlAutoID' => $liabilityGlAutoID,
            'modifiedPCID' => $current_pc,
            'modifiedUserID' => $createdUserID,
            'ModifiedUserName' => $createdUserName,
            'modifiedDateTime' => $current_date,
            'timestamp' => $current_date,
        );


        $payGroupData = array(
            'Description' => $sortCode,
            'modifiedPCID' => $current_pc,
            'modifiedUserID' => $createdUserID,
            'ModifiedUserName' => $createdUserName,
            'modifiedDateTime' => $current_date,
            'timestamp' => $current_date,
        );

        if (!is_null($siIsSlab)) {
            $data['isSlabApplicable'] = 1;
            $data['SlabID'] = $siSlab;
        } else {
            $data['isSlabApplicable'] = null;
            $data['SlabID'] = null;
        }


        $this->db->where('socialInsuranceID', $hidden_id)->where('companyID', $companyId)->update('srp_erp_socialinsurancemaster', $data);
        $this->db->where('socialInsuranceID', $hidden_id)->where('companyID', $companyId)->update('srp_erp_paygroupmaster', $payGroupData);

        $pagGroupID = $this->db->where('socialInsuranceID', $hidden_id)->select('payGroupID')->from('srp_erp_paygroupmaster')->get()->row_array();


        $filedData['fieldName'] = $sortCode;
        $filedData['caption'] = $sortCode;
        $filedData['modifiedPCID'] = $current_pc;
        $filedData['modifiedUserID'] = $createdUserID;
        $filedData['modifiedDateTime'] = $current_date;
        $filedData['modifiedUserName'] = $createdUserName;
        $filedData['timestamp'] = $current_date;

        $this->db->where('payGroupID', $pagGroupID['payGroupID'])->where('companyID', current_companyID())->update('srp_erp_pay_templatefields', $filedData);

        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }
    }

    function deleteSocialInsurance()
    {
        $hidden_id = $this->input->post('hidden-id');

        $isInUse = $this->db->query("SELECT socialInsuranceMasterID FROM srp_erp_socialinsurancedetails WHERE socialInsuranceMasterID={$hidden_id}")->row('socialInsuranceMasterID');

        if (isset($isInUse)) {
            return array('e', 'This Social Insurance is in use</br>You can not delete this');
        } else {
            $this->db->where('socialInsuranceID', $hidden_id)->delete('srp_erp_socialinsurancemaster');
            $deltesRows = $this->db->affected_rows();
            $pagGroupID = $this->db->where('socialInsuranceID', $hidden_id)->select('payGroupID')->from('srp_erp_paygroupmaster')->get()->row_array();

            $this->db->where('socialInsuranceID', $hidden_id)->delete('srp_erp_paygroupmaster');

            $this->db->where('payGroupID', $pagGroupID['payGroupID'])->where('fieldType', 'G')->delete('srp_erp_pay_templatefields');


            if ($deltesRows > 0) {
                return array('s', 'Records deleted successfully');
            } else {
                return array('e', 'Error in deleting process');
            }
        }
    }

    function save_employee_declaration_master()
    {

        $this->load->library('sequence');
        $currency_code = explode('|', trim($this->input->post('currency_code')));
        $isPayrollCategory = trim($this->input->post('isPayrollCategory'));
        $isInitialDeclaration = trim($this->input->post('isInitialDeclaration'));
        $date_format_policy = date_format_policy();
        $invDueDate = $this->input->post('documentDate');
        $documentDate = input_format_date($invDueDate, $date_format_policy);

        $data['documentID'] = 'SD';
        $data['documentSystemCode'] = $this->sequence->sequence_generator("SD");
        $data['documentDate'] = trim($documentDate);
        $data['Description'] = trim($this->input->post('salary_description'));
        $data['isPayrollCategory'] = $isPayrollCategory;
        $data['isInitialDeclaration'] = $isInitialDeclaration;
        $data['transactionCurrencyID'] = trim($this->input->post('MasterCurrency'));
        $data['transactionCurrency'] = trim($currency_code['0']);
        $data['transactionER'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalER'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingER'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = current_date();


        $this->db->insert('srp_erp_salarydeclarationmaster', $data);
        if ($this->db->affected_rows() === FALSE) {
            $errMsg = 'Salary Declaration Save Failed ' . $this->db->_error_message();
            return array('e', $errMsg);
        } else {
            $last_id = $this->db->insert_id();
            return array('s', 'Salary Declaration Saved Successfully.', $last_id, $isPayrollCategory);
        }
    }

    function save_all_salary_declaration()
    {
        $this->form_validation->set_rules('employee', 'Employee', 'trim|required');
        $this->form_validation->set_rules('amount[]', 'Amount', 'trim|required');
        $this->form_validation->set_rules('effectiveDate', 'Effective Date', 'trim|required|date');
        $this->form_validation->set_rules('cat[]', 'Category', 'trim|required');
        $this->form_validation->set_rules('salaryType', 'Salary Type', 'trim|required');
        $this->form_validation->set_rules('payDate', 'Pay Date', 'trim|required|date');
        $this->form_validation->set_rules('empJoinDate', 'Employee Join Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            return array('e', validation_errors());
        } else {
            $masterID = trim($this->input->post('declarationMasterID'));

            $masterDetail = $this->get_salaryDeclarationMaster($masterID);


            $companyID = $this->common_data['company_data']['company_id'];
            $companyCode = $this->common_data['company_data']['company_code'];
            $com_currency = $this->common_data['company_data']['company_default_currency'];
            $com_currencyDPlace = $this->common_data['company_data']['company_default_decimal'];
            $com_repCurrency = $this->common_data['company_data']['company_reporting_currency'];
            $com_repCurDPlace = $this->common_data['company_data']['company_reporting_decimal'];
            $createdPCID = $this->common_data['current_pc'];
            $createdUserID = $this->common_data['current_userID'];
            $createdUserName = $this->common_data['current_user'];
            $createdUserGroup = $this->common_data['user_group'];
            $createdDateTime = current_date();

            $empID = $this->input->post('employee');
            $amount = $this->input->post('amount');
            $salaryType = $this->input->post('salaryType');
            $cat = $this->input->post('cat');
            $empJoinDate1 = $empJoinDate = $this->input->post('empJoinDate');
            $effDate = $this->input->post('effectiveDate');
            $payDate = $this->input->post('payDate');
            $narration = $this->input->post('narration');

            $date_format_policy = date_format_policy();

            $empJoinDate = input_format_date($empJoinDate, $date_format_policy);
            $effDate = input_format_date($effDate, $date_format_policy);
            $payDate = input_format_date($payDate, $date_format_policy);

            if ($effDate < $empJoinDate) {
                return ['e', 'Effective date should be greater than employee <br/>join date [ ' . $empJoinDate1 . ' ]'];
                exit;
            }

            if ($payDate < $effDate) {
                return ['e', 'Pay date should be greater than effective date'];
                exit;
            }


            $lastPayrollProcessed = lastPayrollProcessedForEmp($empID, $masterDetail['isPayrollCategory']);

            $payDateFirst = date('Y-m-01', strtotime($payDate));
            if ($lastPayrollProcessed >= $payDateFirst) {
                return ['e', 'Pay date should be greater than [ ' . date('Y-F', strtotime($lastPayrollProcessed)) . ' ]'];
                exit;
            }

            $data = array();

            if (!empty($cat)) {
                $i = 0;
                $salaryProportionFormulaDays = getPolicyValues('SPF', 'All');
                $salaryProportionDays = (empty($salaryProportionFormulaDays)) ? 365 : $salaryProportionFormulaDays;


                $totalWorkingDays = getPolicyValues('SCD', 'All');
                $totalWorkingDays = (empty($totalWorkingDays)) ? 'totalWorkingDays' : $totalWorkingDays;
                $totalWorkingDays = (trim($totalWorkingDays) == 'LAST_DAY(effectiveDate)') ? 'totalWorkingDays' : $totalWorkingDays;

                $this->db->trans_start();
                foreach ($cat as $key => $catVal) {
                    $groupID = $this->db->query("SELECT groupID FROM srp_erp_payrollgroupemployees
                                                 WHERE companyID={$companyID} AND employeeID={$empID}")->row('groupID');

                    $tr_amount = (!empty($amount[$i])) ? str_replace(',', '', $amount[$i]) : 0;
                    $localCon = currency_conversion($masterDetail['transactionCurrency'], $com_currency, $tr_amount);
                    $reportCon = currency_conversion($masterDetail['transactionCurrency'], $com_repCurrency, $tr_amount);
                    $localAmount = ($localCon['conversion'] > 0) ? round(($tr_amount / $localCon['conversion']), $com_currencyDPlace) : round($tr_amount, $com_currencyDPlace);
                    $reportAmount = ($reportCon['conversion'] > 0) ? round(($tr_amount / $reportCon['conversion']), $com_repCurDPlace) : round($tr_amount, $com_repCurDPlace);
                    $dPlace = $masterDetail['transactionCurrencyDecimalPlaces'];

                    $data['declarationMasterID'] = $masterID;
                    $data['employeeNo'] = $empID;
                    $data['accessGroupID'] = $groupID;
                    $data['salaryCategoryType'] = $salaryType;
                    $data['salaryCategoryID'] = $catVal;
                    $data['amount'] = $amount[$key];
                    $data['effectiveDate'] = $effDate;
                    $data['payDate'] = $payDate;
                    $data['narration'] = $narration;


                    $data['transactionCurrencyID'] = $masterDetail['transactionCurrencyID'];
                    $data['transactionCurrency'] = $masterDetail['transactionCurrency'];
                    $data['transactionER'] = $masterDetail['transactionER'];
                    $data['transactionCurrencyDecimalPlaces'] = $dPlace;


                    $data['companyLocalCurrencyID'] = $localCon['currencyID'];
                    $data['companyLocalCurrency'] = $com_currency;
                    $data['companyLocalER'] = $localCon['conversion'];
                    $data['companyLocalCurrencyDecimalPlaces'] = $com_currencyDPlace;


                    $data['companyReportingCurrencyID'] = $reportCon['currencyID'];
                    $data['companyReportingCurrency'] = $com_repCurrency;
                    $data['companyReportingER'] = $reportCon['conversion'];
                    $data['companyReportingCurrencyDecimalPlaces'] = $com_repCurDPlace;
                    $data['amount'] = $tr_amount;
                    $data['transactionAmount'] = $tr_amount;
                    $data['companyLocalAmount'] = $localAmount;
                    $data['companyReportingAmount'] = $reportAmount;

                    $data['companyID'] = $companyID;
                    $data['companyCode'] = $companyCode;
                    $data['createdPCID'] = $createdPCID;
                    $data['createdUserID'] = $createdUserID;
                    $data['createdUserName'] = $createdUserName;
                    $data['createdUserGroup'] = $createdUserGroup;
                    $data['createdDateTime'] = $createdDateTime;

                    $this->db->insert('srp_erp_salarydeclarationdetails', $data);

                    $insert_id = $this->db->insert_id();

                    /*** effective date and pay date should not be same month for balance calculation ***/
                    if (date('Y-m-01', strtotime($effDate)) != date('Y-m-01', strtotime($payDate))) {
                        /*** start of calculation for effective month balance ***/

                        $effDay = date('d', strtotime($effDate));
                        if ($effDay == 1) {
                            $balanceAmount = round($tr_amount, $dPlace);
                        } else {
                            /************************************************************************
                             * salaryProportionDays == 1 means
                             * formula will be (Salary / no of day in month) * worked days
                             ***********************************************************************/
                            if ($salaryProportionDays == 1) {
                                $totalDaysInEffectiveMonth = date('t', strtotime($effDate));
                                $balanceDate = ($totalDaysInEffectiveMonth + 1) - $effDay;
                                $balanceAmount = ($tr_amount / $totalDaysInEffectiveMonth) * $balanceDate;
                                $balanceAmount = round($balanceAmount, $dPlace);
                            } else {
                                $totalDaysInEffectiveMonth = ($totalWorkingDays == 'totalWorkingDays') ? date('t', strtotime($effDate)) : $totalWorkingDays;
                                $balanceDate = ($totalDaysInEffectiveMonth + 1) - $effDay;
                                $balanceAmount = round((($tr_amount * 12) / $salaryProportionDays) * $balanceDate, $dPlace);
                            }
                        }

                        /*** end of calculation for effective month balance ***/


                        /*** start of calculation for except effective month balance ***/
                        $effDate1 = date('Y-m-01', strtotime($effDate));
                        $payDate1 = date('Y-m-01', strtotime(date('Y-m-01', strtotime($payDate)) . ' -1 month'));


                        $j = 0;
                        while ($effDate1 < $payDate1) {
                            $effDate1 = date('Y-m-d', strtotime($effDate1 . ' +1 month'));
                            $balanceAmount += $tr_amount;

                            if ($j > 150) {
                                break;
                            }
                            $j++;
                        }

                        /*** end of calculation for except effective month balance ***/

                        $detail['empID'] = $empID;
                        $detail['sdMasterID'] = $masterID;
                        $detail['declarationDetailID'] = $insert_id;
                        $detail['fromDate'] = $effDate;
                        $detail['balanceAmount'] = round($balanceAmount, $dPlace);
                        $detail['dueDate'] = $payDate;
                        $detail['salaryCatID'] = $catVal;
                        $detail['companyID'] = $companyID;
                        $detail['createdUserGroup'] = $createdUserGroup;
                        $detail['createdPCID'] = $createdPCID;
                        $detail['createdUserID'] = $createdUserID;
                        $detail['createdDateTime'] = $createdDateTime;
                        $detail['createdUserName'] = $createdUserName;

                        $this->db->insert('srp_erp_pay_balancepayment', $detail);
                    }

                    $i++;
                }

                $this->db->trans_complete();
                if ($this->db->trans_status() === true) {
                    $this->db->trans_commit();
                    return array('s', 'Insert successfully ', $masterID);
                } else {
                    $this->db->trans_rollback();
                    return array('s', 'Failed to insert record');
                }
            }
        }

    }

    function get_salaryDeclarationMaster($id)
    {
        $this->db->select("*");
        $this->db->from("srp_erp_salarydeclarationmaster");
        $this->db->where("salarydeclarationMasterID", $id);
        $this->db->where("companyID", current_companyID());
        $result = $this->db->get()->row_array();
        return $result;
    }

    function ConfirmSalaryDeclaration()
    {

        $companyID = current_companyID();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $createdUserGroup = current_user_group();
        $createdUserName = current_employee();
        $createdDateTime = current_date();
        $masterID = trim($this->input->post('masterID'));

        $masterDetail = $this->get_salaryDeclarationMaster($masterID);


        if ($masterDetail['approvedYN'] == 1) {
            return ['e', 'This document is already approved'];
            exit;
        } else if ($masterDetail['confirmedYN'] == 1) {
            return ['e', 'This document is already confirmed'];
            exit;
        }

        $declarationData = $this->db->query("SELECT declarationDetailID, declarationMasterID, employeeNo, salaryCategoryID,
                                             transactionAmount, effectiveDate, payDate
                                             FROM srp_erp_salarydeclarationdetails AS detailTB
                                             WHERE declarationMasterID ={$masterID} AND detailTB.companyID={$companyID}
                                             ORDER BY employeeNo")->result_array();

        /***Get all salary category in this salary declaration ***/ //, '' AS payGroups
        $salaryCats = $this->db->query("SELECT salaryCategoryID, '' AS payGroups FROM srp_erp_salarydeclarationdetails AS detailTB
                                        WHERE declarationMasterID={$masterID} AND detailTB.companyID={$companyID}
                                        GROUP BY salaryCategoryID")->result_array();

        /***Get all SSO in this company ***/
        $payGroups = $this->db->query("SELECT payGroup.payGroupID, formulaString, payGroupCategories
                                       FROM srp_erp_paygroupmaster AS payGroup
                                       JOIN srp_erp_paygroupformula AS payFormula ON payFormula.payGroupID=payGroup.payGroupID
                                       AND payFormula.companyID={$companyID}
                                       JOIN srp_erp_socialinsurancemaster AS ssoMaster ON ssoMaster.socialInsuranceID = payGroup.socialInsuranceID
                                       AND ssoMaster.companyID={$companyID}
                                       WHERE payGroup.companyID={$companyID} AND payGroup.socialInsuranceID IS NOT NULL")->result_array();


        foreach ($payGroups as $keyPay => $group) {

            $categories = payGroupSalaryCategories_decode($group);
            /** Get all salary categories related to the SSO formula **/
            foreach ($salaryCats as $key_cat => $salaryCats_row) {
                /** if the salary category is in this SSO formula adding the payGroupID to the $salaryCats => payGroups array  **/
                if (in_array($salaryCats_row['salaryCategoryID'], $categories)) {

                    if (is_array($salaryCats[$key_cat]['payGroups'])) {
                        array_push($salaryCats[$key_cat]['payGroups'], $group['payGroupID']);
                    } else {
                        $salaryCats[$key_cat]['payGroups'] = array($group['payGroupID']);
                    }
                }
            }

        }

        $salaryCats = array_group_by($salaryCats, 'salaryCategoryID');


        $this->db->trans_start();
        $this->db->where(array('sdMasterID' => $masterID, 'companyID' => $companyID))->delete('srp_erp_pay_balancessopayment');

        $salaryProportionFormulaDays = getPolicyValues('SPF', 'All');
        $salaryProportionDays = (empty($salaryProportionFormulaDays)) ? 365 : $salaryProportionFormulaDays;

        $totalWorkingDays = getPolicyValues('SCD', 'All');
        $totalWorkingDays = (empty($totalWorkingDays)) ? 'totalWorkingDays' : $totalWorkingDays;
        $totalWorkingDays = (trim($totalWorkingDays) == 'LAST_DAY(effectiveDate)') ? 'totalWorkingDays' : $totalWorkingDays;


        foreach ($declarationData as $key => $row) {

            $detailID = $row['declarationDetailID'];
            $empID = $row['employeeNo'];
            $categoryID = $row['salaryCategoryID'];
            $tr_amount = (!empty($row['transactionAmount'])) ? str_replace(',', '', $row['transactionAmount']) : 0;
            $dPlace = $masterDetail['transactionCurrencyDecimalPlaces'];
            $payGroups = null;

            /*** balance SSO payment ***/

            if (array_key_exists($categoryID, $salaryCats)) {
                if (array_key_exists('payGroups', $salaryCats[$categoryID][0])) {
                    $payGroups = $salaryCats[$categoryID][0]['payGroups'];
                }
            }


            if (!empty($payGroups)) {

                $effDate = $row['effectiveDate'];
                $payDate = $row['payDate'];
                $balanceSSOAmount = 0;
                $effDate1 = date('Y-m-01', strtotime($effDate));
                $payDate1 = date('Y-m-01', strtotime($payDate));
                $effDay = date('d', strtotime($effDate));


                if ($effDate1 == $payDate1 && $effDay != '01') {
                    /************************************************************************
                     * salaryProportionDays == 1 means
                     * formula will be (Salary / no of day in month) * proportion days
                     ***********************************************************************/
                    if ($salaryProportionDays == 1) {
                        $totalDaysInEffectiveMonth = date('t', strtotime($effDate));
                        $proportionDays = ($totalDaysInEffectiveMonth - $effDay) + 1;
                        $proportionVal = ($tr_amount / $totalDaysInEffectiveMonth) * $proportionDays;
                        $balanceSSOAmount = round(($tr_amount - $proportionVal), $dPlace);
                        //$balanceSSOAmount = $proportionDays;
                    } else {
                        $totalDaysInEffectiveMonth = ($totalWorkingDays == 'totalWorkingDays') ? date('t', strtotime($effDate)) + 1 : ($totalWorkingDays + 1);
                        $proportionDays = $totalDaysInEffectiveMonth - $effDay;
                        $proportionVal = ($tr_amount * 12 / $salaryProportionDays) * $proportionDays;
                        $balanceSSOAmount = round(($tr_amount - $proportionVal), $dPlace);
                    }

                } else if ($effDate1 < $payDate1) {
                    $datetime1 = new DateTime($effDate1);
                    $datetime2 = new DateTime($payDate);
                    $interval = $datetime2->diff($datetime1);
                    $totalMonth = (($interval->format('%y') * 12) + $interval->format('%m'));
                    $balanceSSOAmount += ($tr_amount * $totalMonth);
                }

                if ($balanceSSOAmount != 0) {
                    $detailSSOPayment = array();
                    foreach ($payGroups as $payGroupKey => $payGroupRow) {
                        $detailSSOPayment[$payGroupKey]['empID'] = $empID;
                        $detailSSOPayment[$payGroupKey]['sdMasterID'] = $masterID;
                        $detailSSOPayment[$payGroupKey]['declarationDetailID'] = $detailID;
                        $detailSSOPayment[$payGroupKey]['payGroupID'] = $payGroupRow;
                        $detailSSOPayment[$payGroupKey]['fromDate'] = $effDate;
                        $detailSSOPayment[$payGroupKey]['balanceAmount'] = round($balanceSSOAmount, $dPlace);
                        $detailSSOPayment[$payGroupKey]['dueDate'] = $payDate;
                        $detailSSOPayment[$payGroupKey]['companyID'] = $companyID;
                        $detailSSOPayment[$payGroupKey]['createdUserGroup'] = $createdUserGroup;
                        $detailSSOPayment[$payGroupKey]['createdPCID'] = $createdPCID;
                        $detailSSOPayment[$payGroupKey]['createdUserID'] = $createdUserID;
                        $detailSSOPayment[$payGroupKey]['createdDateTime'] = $createdDateTime;
                        $detailSSOPayment[$payGroupKey]['createdUserName'] = $createdUserName;
                    }

                    $this->db->insert_batch('srp_erp_pay_balancessopayment', $detailSSOPayment);
                }

            }

        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === true) {
            $this->db->trans_commit();

            $this->load->library('approvals');
            $systemCode = $masterDetail['documentSystemCode'];
            $table = 'srp_erp_salarydeclarationmaster';
            $primaryColumn = 'salarydeclarationMasterID';
            $documentName = 'Salary Declaration';
            $approvals_status = $this->approvals->CreateApproval('SD', $masterID, $systemCode, $documentName, $table, $primaryColumn);

            if ($approvals_status == 1) {
                return ['s', 'Approvals created successfully'];
            }
            if ($approvals_status == 3) {
                return ['w', 'There are no users exist to perform \'Salary Declaration\' approval for this company.'];
            } else {
                return ['e', 'Error in process'];
            }
        } else {
            $this->db->trans_rollback();
            return array('s', 'Failed to update balance amounts');
        }

    }

    function save_salary_declaration_approval()
    {

        $this->db->trans_start();
        $this->load->library('approvals');
        $system_code = trim($this->input->post('salaryOrderID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('approval_status'));
        $comments = trim($this->input->post('comments'));

        $createdPCID = $this->common_data['current_pc'];
        $createdUserID = $this->common_data['current_userID'];
        $createdUserName = $this->common_data['current_user'];
        $createdUserGroup = $this->common_data['user_group'];
        $createdDateTime = current_date();

        $masterDetail = $this->get_salaryDeclarationMaster($system_code);

        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'SD');

        //die('$approvals_status:'.$approvals_status);
        if ($approvals_status == 1) {


            $this->db->select('*');
            $this->db->from('srp_erp_salarydeclarationdetails');
            $this->db->where('declarationMasterID', $system_code);
            $details_arr = $this->db->get()->result_array();

            $declarationDet_arr = array();
            for ($i = 0; $i < count($details_arr); $i++) {
                $declarationDet_arr[$i]['sdMasterID'] = $system_code;
                $declarationDet_arr[$i]['sdDetailID'] = $details_arr[$i]['declarationDetailID'];
                $declarationDet_arr[$i]['employeeNo'] = $details_arr[$i]['employeeNo'];
                $declarationDet_arr[$i]['accessGroupID'] = $details_arr[$i]['accessGroupID'];
                $declarationDet_arr[$i]['salaryCategoryID'] = $details_arr[$i]['salaryCategoryID'];
                $declarationDet_arr[$i]['transactionCurrencyID'] = $details_arr[$i]['transactionCurrencyID'];
                $declarationDet_arr[$i]['transactionCurrency'] = $details_arr[$i]['transactionCurrency'];
                $declarationDet_arr[$i]['transactionER'] = $details_arr[$i]['transactionER'];
                $declarationDet_arr[$i]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['transactionCurrencyDecimalPlaces'];
                $declarationDet_arr[$i]['transactionAmount'] = $details_arr[$i]['transactionAmount'];
                $declarationDet_arr[$i]['amount'] = $details_arr[$i]['amount'];
                $declarationDet_arr[$i]['percentage'] = $details_arr[$i]['percentage'];
                $declarationDet_arr[$i]['effectiveDate'] = $details_arr[$i]['effectiveDate'];
                $declarationDet_arr[$i]['payDate'] = $details_arr[$i]['payDate'];
                $declarationDet_arr[$i]['narration'] = $details_arr[$i]['narration'];
                $declarationDet_arr[$i]['additionID'] = $details_arr[$i]['additionID'];
                $declarationDet_arr[$i]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                $declarationDet_arr[$i]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                $declarationDet_arr[$i]['companyLocalER'] = $details_arr[$i]['companyLocalER'];
                $declarationDet_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                $declarationDet_arr[$i]['companyLocalAmount'] = $details_arr[$i]['companyLocalAmount'];
                $declarationDet_arr[$i]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                $declarationDet_arr[$i]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                $declarationDet_arr[$i]['companyReportingER'] = $details_arr[$i]['companyReportingER'];
                $declarationDet_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                $declarationDet_arr[$i]['companyReportingAmount'] = $details_arr[$i]['companyReportingAmount'];
                $declarationDet_arr[$i]['confirmedYN'] = 1;
                $declarationDet_arr[$i]['confirmedByEmpID'] = $masterDetail['confirmedByEmpID'];
                $declarationDet_arr[$i]['confirmedByName'] = $masterDetail['confirmedByName'];
                $declarationDet_arr[$i]['confirmedDate'] = $masterDetail['confirmedDate'];
                $declarationDet_arr[$i]['companyID'] = $details_arr[$i]['companyID'];
                $declarationDet_arr[$i]['companyCode'] = $details_arr[$i]['companyCode'];
                $declarationDet_arr[$i]['createdUserGroup'] = $createdUserGroup;
                $declarationDet_arr[$i]['createdUserID'] = $createdUserID;
                $declarationDet_arr[$i]['createdDateTime'] = $createdDateTime;
                $declarationDet_arr[$i]['createdUserName'] = $createdUserName;
            }

            if (!empty($declarationDet_arr)) {
                $declarationDet_arr = array_values($declarationDet_arr);
                $tableName = ($masterDetail['isPayrollCategory'] == 1) ? 'srp_erp_pay_salarydeclartion' : 'srp_erp_non_pay_salarydeclartion';
                $this->db->insert_batch($tableName, $declarationDet_arr);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return ['e', 'Error In Salary Declaration Approval Process.'];
            } else {
                $this->db->trans_commit();
                return ['s', 'Salary Declaration Approved Successfully.'];
            }


        } else if ($approvals_status == 2) {
            return ['s', 'Salary Declaration Approval : Level ' . $level_id . ' Successfully.'];
        } else if ($approvals_status == 3) {
            return ['s', '[ ' . $masterDetail['documentSystemCode'] . ' ] Approvals  Reject Process Successfully done.'];
        } else if ($approvals_status == 5) {
            return ['w', '[ ' . $masterDetail['documentSystemCode'] . ' ] Previous Level Approval Not Finished.'];
        } else {
            return ['e', 'Error in approvals Of  [ ' . $masterDetail['documentSystemCode'] . ' ] ', $approvals_status];
        }

    }

    function getDeclarationmasterCurrency_edit()
    {
        $this->db->select('salarydeclarationMasterID,transactionCurrencyID,transactionCurrency');
        $this->db->where('salarydeclarationMasterID', $this->input->post('masterID'));
        return $this->db->get('srp_erp_salarydeclarationmaster')->row_array();
    }

    function delete_salary_declaration()
    {
        $id = trim($this->input->post('detailID'));
        $companyID = current_companyID();

        $this->db->trans_start();

        $this->db->delete('srp_erp_salarydeclarationdetails', ['declarationDetailID' => $id, 'companyID' => $companyID]);
        $this->db->delete('srp_erp_pay_balancepayment', ['declarationDetailID' => $id, 'companyID' => $companyID]);
        $this->db->delete('srp_erp_pay_balancessopayment', ['declarationDetailID' => $id, 'companyID' => $companyID]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['e', 'Fail to delete salary declaration'];
        } else {
            $this->db->trans_commit();
            return ['s', 'Successfully deleted'];
        }
    }

    function save_pay_slabs_master()
    {
        $this->load->library('sequence');
        $currency_code = explode('|', trim($this->input->post('currency_code')));

        $date_format_policy = date_format_policy();
        $invDueDate = $this->input->post('documentDate');
        $documentDate = input_format_date($invDueDate, $date_format_policy);

        $data['documentID'] = 'SLM';
        $data['documentSystemCode'] = $this->sequence->sequence_generator("SLM");
        //$data['documentDate'] = trim($this->input->post('documentDate'));
        $data['documentDate'] = trim($documentDate);
        $data['Description'] = trim($this->input->post('description'));
        $data['transactionCurrencyID'] = trim($this->input->post('MasterCurrency'));
        $data['transactionCurrency'] = trim($currency_code['0']);
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = current_date();
        $this->db->insert('srp_erp_slabsmaster', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Save Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }
    }

    function get_paySlabMaster($id)
    {
        $this->db->select("*");
        $this->db->from("srp_erp_slabsmaster");
        $this->db->where("slabsMasterID", $id);
        $result = $this->db->get()->row_array();
        return $result;
    }

    function save_pay_slabs_detail()
    {
        $data['slabsMasterID'] = trim($this->input->post('slabMasterID'));
        $data['rangeStartAmount'] = trim($this->input->post('start_amount'));
        $data['rangeEndAmount'] = trim($this->input->post('end_amount'));
        $data['percentage'] = trim($this->input->post('percentage'));
        $data['thresholdAmount'] = trim($this->input->post('threshold_amount'));
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = current_date();
        $this->db->insert('srp_erp_slabsdetail', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Record Created Successfully.', $this->input->post('slabMasterID'));
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error In Record Creating');
        }
    }

    function delete_payee_slab_detail()
    {
        $this->db->delete('srp_erp_slabsdetail', array('slabsDetailID' => trim($this->input->post('detailID'))));
        return true;
    }

    function delete_salary_declaration_master()
    {
        $masterID = trim($this->input->post('masterID'));
        $this->db->delete('srp_erp_salarydeclarationmaster', array('salarydeclarationMasterID' => $masterID));
        $this->db->delete('srp_erp_salarydeclarationdetails', array('declarationMasterID' => $masterID));
        $this->db->delete('srp_erp_pay_balancepayment', array('sdMasterID' => $masterID));
        $this->db->delete('srp_erp_pay_balancessopayment', array('sdMasterID' => $masterID));
        return true;
    }

    function save_leaveGroup()
    {
        $description = $this->input->post('description');
        //$isHourly = $this->input->post('isMonthly');
        $masterID = $this->input->post('masterID');

        if ($masterID == '') {
            $insert = $this->db->insert('srp_erp_leavegroup', array('description' => $description, 'companyID' => current_companyID()));
            if ($insert) {
                $last_id = $this->db->insert_id();

                $this->session->set_flashdata('s', 'Leave group inserted successfully.');

                echo json_encode(array('error' => 0, 'leaveGroupID' => $last_id));
                exit;
            } else {

            }
            $this->session->set_flashdata('e', 'Failed.');
            echo json_encode(array('error' => 1));
            exit;

        } else {
            $this->db->where('leaveGroupID', $masterID);
            $update = $this->db->update('srp_erp_leavegroup', array('description' => $description));
            if ($update) {
                $this->session->set_flashdata('s', 'Leave group updated successfully.');
                echo json_encode(array('error' => 0, 'leaveGroupID' => $masterID));
                exit;
            } else {
                $this->session->set_flashdata('e', 'Failed.');
                echo json_encode(array('error' => 1));
                exit;
            }
        }

    }


    /**
     *
     */
    public function save_leaveAccrual()
    {
        $this->db->trans_begin();
        $companyID = current_companyID();
        $description = $this->input->post('description');
        $leaveGroupID = $this->input->post('leaveGroupID');
        $masterID = $this->input->post('masterID');
        $this->load->library('sequence');
        $code = $this->sequence->sequence_generator('LAM');
        $period = $this->input->post('period');
        $d = explode('-', $this->input->post('period'));


        if ($masterID == '') {
            $data = array(
                'companyID' => current_companyID(),
                'leaveaccrualMasterCode' => $code,
                'documentID' => 'LAM',
                'description' => $description,
                'year' => $d[1],
                'month' => $d[0],
                'leaveGroupID' => $leaveGroupID,
                'createdUserGroup' => current_user_group(),
                'createDate' => date('Y-m-d H:i:s'),
                'createdpc' => current_pc(),
                /* 'confirmedYN' => 1,
                 'confirmedby' => $this->common_data['current_userID'],
                 'confirmedDate' => current_date(),*/
                'policyMasterID' => 3

            );
            $insert = $this->db->insert('srp_erp_leaveaccrualmaster', $data);
            if ($insert) {
                $last_id = $this->db->insert_id();
                $detail = array();
                $date = $d[1] . '-' . $d[0] . '-' . '01';
                $lastDate = date("Y-m-t", strtotime($date));

                //  $q="SELECT concat(EIdNo,'-',srp_erp_leavetype.leaveTypeID) as leaveTypeKey,EIdNo,srp_employeesdetails.leaveGroupID,srp_erp_leavegroupdetails.*,policyID FROM srp_employeesdetails INNER JOIN `srp_erp_leavegroupdetails` on srp_erp_leavegroupdetails.leaveGroupID=srp_employeesdetails.leaveGroupID  AND DateAssumed <='$date' INNER JOIN `srp_erp_leavetype` on srp_erp_leavegroupdetails.leaveTypeID=srp_erp_leavetype.leaveTypeID WHERE Erp_companyID = {$companyID} AND srp_employeesdetails.leaveGroupID IS NOT NULL AND srp_employeesdetails.leaveGroupID=$leaveGroupID";

                $q2 = "SELECT DateAssumed, CONCAT(EIdNo, '-', srp_erp_leavetype.leaveTypeID) AS leaveTypeKey, EIdNo, srp_employeesdetails.leaveGroupID, srp_erp_leavegroupdetails.*, policyID FROM `srp_employeesdetails` INNER JOIN `srp_erp_leavegroupdetails` ON srp_erp_leavegroupdetails.leaveGroupID = srp_employeesdetails.leaveGroupID AND policyMasterID=3 AND DateAssumed <= '{$lastDate}' INNER JOIN `srp_erp_leavetype` ON srp_erp_leavegroupdetails.leaveTypeID = srp_erp_leavetype.leaveTypeID WHERE isDischarged !=1 AND Erp_companyID = {$companyID} AND srp_employeesdetails.leaveGroupID IS NOT NULL AND srp_employeesdetails.leaveGroupID = {$leaveGroupID} AND (EIdNo , srp_erp_leavetype.leaveTypeID) NOT IN (SELECT empID, leaveType FROM `srp_erp_leaveaccrualmaster` INNER JOIN srp_erp_leaveaccrualdetail ON srp_erp_leaveaccrualmaster.leaveaccrualMasterID = srp_erp_leaveaccrualdetail.leaveaccrualMasterID WHERE year = {$d[1]} AND month = {$d[0]} AND srp_erp_leaveaccrualmaster.leaveaccrualMasterID != {$last_id} AND srp_erp_leaveaccrualmaster.manualYN=0 GROUP BY empID , leaveType)";

                $result = $this->db->query($q2)->result_array();

                //$entitled = $this->db->query("select leaveEntitledID, leaveTypeID, empID, concat(empID,'-',leaveTypeID) as entitleKey,  days, hourly, companyID, companyCode from  srp_erp_leaveentitled WHERE companyID={$companyID}")->result_array();


                $updateArr = array();
                $insert_Arr = array();
                if ($result) {
                    foreach ($result as $val) {
                        $daysEntitled = $val['noOfDays'];


                        $datas = array('leaveaccrualMasterID' => $last_id,
                            'empID' => $val['EIdNo'],
                            'leaveGroupID' => $leaveGroupID,
                            'leaveType' => $val['leaveTypeID'],
                            'daysEntitled' => $daysEntitled,

                            'description' => 'Leave Accrual ' . $this->input->post('period'),
                            'createDate' => date('Y-m-d H:i:s'),
                            'createdUserGroup' => current_user_group(),
                            'createdPCid' => current_pc()
                        );

                        /*    $keys = array_keys(array_column($entitled, 'entitleKey'), $val['leaveTypeKey']);
                            $new_array = array_map(function ($k) use ($entitled) {
                                return $entitled[$k];
                            }, $keys);*/


                        /*   if (!empty($new_array)) {
                               array_push($updateArr, array('leaveEntitledID' => $new_array[0]['leaveEntitledID'], 'days' => $new_array[0]['days'] + $daysEntitled, 'hourly' => $new_array[0]['hourly'] + $hoursEntitled));

                           } else {*/
                        array_push($insert_Arr, array(
                            'leaveTypeID' => $val['leaveTypeID'], 'empID' => $val['EIdNo'], 'days' => $daysEntitled, 'companyID' => current_companyID(), 'companyCode' => current_companyCode(), 'createdUserGroup' => '',
                            'createdPCID' => $this->common_data['current_pc'],
                            'createdUserID' => $this->common_data['current_userID'],
                            'createdDateTime' => current_date(),
                            'createdUserName' => $this->common_data['current_user'],
                        ));
                        /* }*/
                        array_push($detail, $datas);
                    }

                    /* if (!empty($updateArr)) {
                         $this->db->update_batch('srp_erp_leaveentitled', $updateArr, 'leaveEntitledID');
                     }*/
                    /* if (!empty($insert_Arr)) {
                         $this->db->insert_batch('srp_erp_leaveentitled', $insert_Arr);
                     }*/
                    $this->db->insert_batch('srp_erp_leaveaccrualdetail', $detail);
                }
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('e', 'Failed.');

                    echo json_encode(array('error' => 1));
                    exit;
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('s', 'Leave Accrual inserted successfully.');

                    echo json_encode(array('error' => 0, 'leaveGroupID' => $last_id));
                    exit;
                }


            } else {
                $this->session->set_flashdata('e', 'Failed');

                echo json_encode(array('error' => 1));
                exit;
            }

        } else {


            //   $this->session->set_flashdata('s', 'Leave group inserted successfully.');

            echo json_encode(array('error' => 0, 'leaveGroupID' => $masterID));
            exit;
        }

    }

    function updatePayGroupDetails()
    {
        $salaryCategory = $this->input->post('salaryCategory');
        $payGroupId = $this->input->post('payGroupId');
        $companyID = current_companyID();
        $createdUserName = current_employee();
        $current_date = current_date();
        $current_pc = current_pc();
        $user_id = current_userID();
        $user_group = current_user_group();

        $payGroups = $this->db->query("SELECT salaryCategoryID FROM srp_erp_paygroupdetails WHERE groupID='{$payGroupId}'")->result_array();


        $pg = array();
        foreach ($payGroups as $payGroup) {
            $pg[] = $payGroup['salaryCategoryID'];
        }

        foreach ($salaryCategory as $item) {
            if (in_array($item, $pg)) {
                continue;
            }

            $data['groupID'] = $payGroupId;
            $data['salaryCategoryID'] = $item;
            $data['companyID'] = $companyID;
            $data['companyCode'] = $companyID;
            $data['createdUserGroup'] = $user_group;
            $data['createdPCID'] = $current_pc;
            $data['createdUserID'] = $user_id;
            $data['createdDateTime'] = $current_date;
            $data['createdUserName'] = $createdUserName;
            $data['timestamp'] = $current_date;
            $this->db->insert('srp_erp_paygroupdetails', $data);
            return array('s', 'Record Created Successfully.');
        }

    }

    function saveFormula()
    {
        $formulaOriginal = $this->input->post('formulaOriginal');
        $formulaText = $this->input->post('formulaText');
        $payGroupID = $this->input->post('payGroupID');
        $salaryCategories = $this->input->post('salaryCategories');
        $companyID = current_companyID();
        $createdUserName = current_employee();
        $current_date = current_date();
        $current_pc = current_pc();
        $user_id = current_userID();
        $user_group = current_user_group();

        $data['payGroupID'] = $payGroupID;
        $data['formulaString'] = $formulaOriginal;
        $data['formula'] = $formulaText;
        $data['salaryCategories'] = $salaryCategories;

        $isHas = $this->db->query("SELECT * FROM srp_erp_paygroupformula WHERE payGroupID = '{$payGroupID}' AND companyID = '{$companyID}'")->row_array();
        if ($isHas) {
            $data['modifiedPCID'] = $current_pc;
            $data['modifiedUserID'] = $user_id;
            $data['modifiedDateTime'] = $current_date;
            $data['modifiedUserName'] = $createdUserName;
            $data['timestamp'] = $current_date;
            $this->db->where('payGroupID', $payGroupID)->where('companyID', $companyID)->update('srp_erp_paygroupformula', $data);
            return ['s', 'Record Updated Successfully.'];
        } else {
            $data['companyID'] = $companyID;
            $data['companyCode'] = $companyID;
            $data['createdUserGroup'] = $user_group;
            $data['createdPCID'] = $current_pc;
            $data['createdUserID'] = $user_id;
            $data['createdDateTime'] = $current_date;
            $data['createdUserName'] = $createdUserName;
            $data['timestamp'] = $current_date;
            $this->db->insert('srp_erp_paygroupformula', $data);
            return ['s', 'Record Created Successfully.'];
        }


    }

    function saveFormula_new()
    {
        $formulaString = $this->input->post('formulaString');
        $formula = $this->input->post('formula');
        $payGroupID = $this->input->post('payGroupID');
        $salaryCategories = $this->input->post('salaryCategoryContainer');
        $salaryCategories = (trim($salaryCategories) == '') ? null : $salaryCategories;
        $ssoCategories = $this->input->post('SSOContainer');
        $ssoCategories = (trim($ssoCategories) == '') ? null : $ssoCategories;
        $payGroupCategories = $this->input->post('payGroupContainer');
        $payGroupCategories = (trim($payGroupCategories) == '') ? null : $payGroupCategories;
        $companyID = current_companyID();
        $createdUserName = current_employee();
        $current_date = current_date();
        $current_pc = current_pc();
        $user_id = current_userID();
        $user_group = current_user_group();

        $data['payGroupID'] = $payGroupID;
        $data['formulaString'] = $formulaString;
        $data['formula'] = $formula;
        $data['salaryCategories'] = $salaryCategories;
        $data['ssoCategories'] = $ssoCategories;
        $data['payGroupCategories'] = $payGroupCategories;

        $isHas = $this->db->query("SELECT payGroupID, IF(ISNULL(socialInsuranceID), payeeID, socialInsuranceID) AS social_paye, isGroupTotal, formula_payGroupID
                                   FROM srp_erp_paygroupmaster AS groupMaster
                                   LEFT JOIN (
                                      SELECT payGroupID AS formula_payGroupID FROM srp_erp_paygroupformula WHERE payGroupID='{$payGroupID}'
                                      AND companyID = '{$companyID}'
                                   ) AS formulaTB ON formulaTB.formula_payGroupID = groupMaster.payGroupID
                                   WHERE payGroupID = '{$payGroupID}' AND companyID = '{$companyID}'")->row_array();


        if (!empty($isHas['social_paye']) && $payGroupCategories != null) {
            /*************************************************************************************************
             * Validate if SSO / PAYE item going to save the formula, is the formula contain pay group check,
             * than the pay group is only contains salary categories
             *************************************************************************************************/
            $payGroupData = $this->db->query("SELECT masterTB.description FROM srp_erp_paygroupmaster AS masterTB
                                              JOIN srp_erp_paygroupformula AS formula ON formula.payGroupID=masterTB.payGroupID
                                              WHERE masterTB.companyID = '{$companyID}' AND formula.payGroupID IN ({$payGroupCategories})
                                              AND (ssoCategories IS NOT NULL OR payGroupCategories IS NOT NULL )")->result_array();

            if (!empty($payGroupData)) {
                $description = implode('<br/>-', array_column($payGroupData, 'description'));
                return ['e', 'Following pay group/groups should only contain salary categories<br/>-' . $description];
            }
        }

        if ($isHas['isGroupTotal'] == 1 && $payGroupCategories != null) {
            /*************************************************************************************************
             * Validate if pay group item going to save the formula, is the formula pulled in SSO,PAYE formula,
             * than this pay group is only contains salary categories
             *************************************************************************************************/
            $SSOData = $this->db->query("SELECT payGroup.description  FROM srp_erp_paygroupmaster AS payGroup
                                         JOIN srp_erp_paygroupformula AS payFormula ON payFormula.payGroupID=payGroup.payGroupID
                                         AND payFormula.companyID={$companyID}
                                         JOIN srp_erp_socialinsurancemaster AS ssoMaster ON ssoMaster.socialInsuranceID = payGroup.socialInsuranceID
                                         AND ssoMaster.companyID={$companyID}
                                         WHERE payGroup.companyID={$companyID} AND payGroup.socialInsuranceID IS NOT NULL AND
                                         (
                                             payGroupCategories LIKE '%,{$payGroupID},%' OR payGroupCategories='{$payGroupID}' OR payGroupCategories
                                             LIKE '{$payGroupID},%' OR payGroupCategories LIKE '%,{$payGroupID}'
                                         )
                                         UNION
                                         SELECT payGroup.description  FROM srp_erp_paygroupmaster AS payGroup
                                         JOIN srp_erp_paygroupformula AS payFormula ON payFormula.payGroupID=payGroup.payGroupID
                                         AND payFormula.companyID={$companyID}
                                         JOIN srp_erp_payeemaster AS payeeMaster ON payeeMaster.payeeMasterID = payGroup.payeeID
                                         AND payeeMaster.companyID={$companyID}
                                         WHERE payGroup.companyID={$companyID} AND payGroup.payeeID IS NOT NULL AND
                                         (
                                             payGroupCategories LIKE '%,{$payGroupID},%' OR payGroupCategories='{$payGroupID}' OR payGroupCategories
                                             LIKE '{$payGroupID},%' OR payGroupCategories LIKE '%,{$payGroupID}'
                                         )
                                         UNION
                                         SELECT CONCAT('SSO slab | ', description ,' ( ', startRangeAmount,' - ',endRangeAmount,' )') AS description
                                         FROM srp_erp_ssoslabmaster AS slabmaster
                                         JOIN srp_erp_ssoslabdetails AS slabDetails ON slabDetails.ssoSlabMasterID=slabmaster.ssoSlabMasterID
                                         AND slabDetails.companyID={$companyID}
                                         WHERE slabmaster.companyID={$companyID} AND
                                         (
                                             payGroupCategories LIKE '%,{$payGroupID},%' OR payGroupCategories='{$payGroupID}' OR payGroupCategories
                                             LIKE '{$payGroupID},%' OR payGroupCategories LIKE '%,{$payGroupID}'
                                         ) ")->result_array();

            if (!empty($SSOData)) {
                $description = implode('<br/>-', array_column($SSOData, 'description'));
                return ['e', 'Following SSO/PAYE/SSO slab contain this pay group, so you can only select salary categories to this pay group <br/>-' . $description];
            }

            //validate added pay groups or it's sub groups contains this pay group
            $payGr_arr = explode(',', $payGroupCategories);
            foreach ($payGr_arr as $g) {
                $validation = payGroup_validation($payGroupID, $g);
                if ($validation[0] == 'e') {
                    $description = $this->db->query("SELECT description FROM srp_erp_paygroupmaster WHERE companyID={$companyID}    
                                      AND payGroupID={$g}")->row('description');

                    die(json_encode(['e', $description . ' OR it\'s sub elements formula contain this group.<br/> You can not add ' . $description . ' to this formula']));
                }
                if ($validation[0] == 'w') {
                    die(json_encode($validation));
                }
            }
        }

        if (!empty($isHas['formula_payGroupID'])) {
            $data['modifiedPCID'] = $current_pc;
            $data['modifiedUserID'] = $user_id;
            $data['modifiedDateTime'] = $current_date;
            $data['modifiedUserName'] = $createdUserName;
            $data['timestamp'] = $current_date;
            $this->db->where('payGroupID', $payGroupID)->where('companyID', $companyID)->update('srp_erp_paygroupformula', $data);
            return ['s', 'Formula updated successfully.'];
        } else {
            $data['companyID'] = $companyID;
            $data['companyCode'] = $companyID;
            $data['createdUserGroup'] = $user_group;
            $data['createdPCID'] = $current_pc;
            $data['createdUserID'] = $user_id;
            $data['createdDateTime'] = $current_date;
            $data['createdUserName'] = $createdUserName;
            $data['timestamp'] = $current_date;
            $this->db->insert('srp_erp_paygroupformula', $data);
            return ['s', 'Formula updated successfully.'];
        }


    }

    function savePayGroup()
    {
        $description = $this->input->post('description[]');
        $companyId = current_companyID();
        $companyCode = current_companyCode();
        $createdUserGroup = current_user_group();
        $current_pc = current_pc();
        $createdUserID = current_userID();
        $current_date = current_date();
        $createdUserName = current_user();
        $description = array_map('trim', $description);
        $description_list = "'" . implode("','", $description) . "'";

        $isExist = $this->db->query("SELECT description FROM srp_erp_paygroupmaster WHERE companyID={$companyId}
                     AND description IN ({$description_list})")->result_array();

        if (!empty($isExist)) {
            $description_list = implode('</br> - ', array_column($isExist, 'description'));
            return array('e', 'Following descriptions already exist.</br> - ' . $description_list);
        }

        $this->db->trans_start();
        $append = array();
        foreach ($description as $key => $de) {

            $append['isGroupTotal'] = 1;
            $append['description'] = $de;
            $append['companyID'] = $companyId;
            $append['companyCode'] = $companyCode;
            $append['createdUserGroup'] = $createdUserGroup;
            $append['createdPCID'] = $current_pc;
            $append['createdUserID'] = $createdUserID;
            $append['createdDateTime'] = $current_date;
            $append['createdUserName'] = $createdUserName;
            $append['modifiedPCID'] = $current_pc;
            $append['modifiedUserID'] = $createdUserID;
            $append['modifiedDateTime'] = $current_date;
            $append['modifiedUserName'] = $createdUserName;
            $append['timestamp'] = $current_date;

            $this->db->insert('srp_erp_paygroupmaster', $append);
            $payGroupID = $this->db->insert_id();


            $appendFiled['fieldName'] = $de;
            $appendFiled['caption'] = $de;
            $appendFiled['fieldType'] = 'G';
            $appendFiled['isCalculate'] = 1;
            $appendFiled['payGroupID'] = $payGroupID;


            $appendFiled['companyID'] = $companyId;
            $appendFiled['companyCode'] = $companyCode;
            $appendFiled['createdUserGroup'] = $createdUserGroup;
            $appendFiled['createdPCID'] = $current_pc;
            $appendFiled['createdUserID'] = $createdUserID;
            $appendFiled['createdDateTime'] = $current_date;
            $appendFiled['createdUserName'] = $createdUserName;
            $appendFiled['modifiedPCID'] = $current_pc;
            $appendFiled['modifiedUserID'] = $createdUserID;
            $appendFiled['modifiedDateTime'] = $current_date;
            $appendFiled['modifiedUserName'] = $createdUserName;
            $appendFiled['timestamp'] = $current_date;
            $this->db->insert('srp_erp_pay_templatefields', $appendFiled);

        }

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            return array('s', 'Records inserted successfully');
        } else {
            return array('e', 'Error in insert record');
        }

    }

    function updatePayGroup()
    {
        $description = trim($this->input->post('pgDes'));
        $hidden_id = $this->input->post('hidden-id');
        $companyID = current_companyID();

        $isExist = $this->db->query("SELECT payGroupID FROM srp_erp_paygroupmaster WHERE payGroupID != {$hidden_id}  AND 
                      description = '{$description}' AND companyID={$companyID}")->row('payGroupID');

        if (!empty($isExist)) {
            return array('e', 'This description already exist');
        }

        $data = array(
            'description' => $description,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_employee(),
            'modifiedDateTime' => current_date(),
            'timestamp' => current_date()
        );


        $this->db->where(['payGroupID' => $hidden_id, 'companyID' => $companyID])->update('srp_erp_paygroupmaster', $data);

        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }
    }

    function deletePayGroup()
    {
        $hidden_id = $this->input->post('hidden-id');

        $this->db->trans_start();

        $this->db->where('payGroupID', $hidden_id)->delete('srp_erp_paygroupmaster');
        $this->db->where('payGroupID', $hidden_id)->delete('srp_erp_paygroupformula');
        $this->db->where('payGroupID', $hidden_id)->where('fieldType', 'G')->delete('srp_erp_pay_templatefields');

        $this->db->trans_complete();

        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Records deleted successfully');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in deleting process');
        }

    }

    function searchleaveEmployee($keyword)
    {
        $currentEmpID = $this->input->get('currentEmpID');
        $com = $this->common_data['company_data']['company_id'];
        $where = "(Ename1 LIKE '%$keyword%' OR Ename2 LIKE '%$keyword%' OR Ename3 LIKE '%$keyword%' OR Ename4 LIKE '%$keyword%' OR ECode LIKE '%$keyword%' ";
        $where .= "OR DesDescription  LIKE '%$keyword%') AND srp_employeesdetails.Erp_companyID='$com'";
        $where .= "AND isActive=1";
        $con = "IFNULL(Ename2, ''),' | ', DesDescription , '    |   ', ECode";

        $this->db->select("EIdNo, ECode, IFNULL(Ename1, '') Ename1, IFNULL(Ename2, '') Ename2, IFNULL(Ename3, '') Ename3, IFNULL(Ename4, '') Ename4, DesDescription, CONCAT(" . $con . ") AS 'Match'");
        $this->db->from('srp_employeesdetails');

        $this->db->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID');
        $this->db->where($where);


        $query = $this->db->get();

        return $query->result();
    }

    public function save_leaveAdjustment()
    {
        $this->db->trans_begin();
        $companyID = current_companyID();
        $description = $this->input->post('description');
        $leaveGroupID = $this->input->post('leaveGroupID');
        $masterID = $this->input->post('masterID');
        $policyMasterID = $this->input->post('policyMasterID');
        $this->load->library('sequence');
        $code = $this->sequence->sequence_generator('LAM');


        if ($masterID == '') {
            $data = array(
                'companyID' => current_companyID(),
                'leaveaccrualMasterCode' => $code,
                'documentID' => 'LAM',
                'description' => $description,
                'year' => date('Y'),
                'month' => date('m'),
                'leaveGroupID' => $leaveGroupID,
                'createdUserGroup' => current_user_group(),
                'createDate' => date('Y-m-d H:i:s'),
                'createdpc' => current_pc(),
                'manualYN' => 1,
                'policyMasterID' => $policyMasterID

            );
            $insert = $this->db->insert('srp_erp_leaveaccrualmaster', $data);
            if ($insert) {
                $last_id = $this->db->insert_id();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('e', 'Failed.');

                    echo json_encode(array('error' => 1));
                    exit;
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('s', 'Leave Accrual inserted successfully.');

                    echo json_encode(array('error' => 0, 'leaveGroupID' => $last_id));
                    exit;
                }
            } else {
                $this->session->set_flashdata('e', 'Failed');

                echo json_encode(array('error' => 1));
                exit;
            }
        } else {
            echo json_encode(array('error' => 0, 'leaveGroupID' => $masterID));
            exit;
        }

    }

    function save_payeeMaster()
    {
        $sortCode = $this->input->post('sortCode[]');
        $description = $this->input->post('description[]');

        $liabilityGlAutoID = $this->input->post('liabilityGlAutoID[]');
        $payrollType = $this->input->post('payrollType[]');
        $isSlab = $this->input->post('ifSlab[]');
        $ifSlab = $this->input->post('ifSlabHidden[]');


        $companyId = current_companyID();
        $companyCode = current_companyCode();
        $createdUserGroup = current_user_group();
        $current_pc = current_pc();
        $createdUserID = current_userID();
        $current_date = current_date();
        $createdUserName = current_user();

        $availble = $this->db->select('sortCode')
            ->from('srp_erp_payeemaster')
            ->where('companyID', $companyId)
            ->get()->result_array();


        $si = array();
        foreach ($availble as $item) {
            $si[] = $item['sortCode'];
        }

        $data = array();
        $append = array();
        foreach ($sortCode as $key => $de) {

            if (in_array($de, $si)) {
                continue;
            }


            $data['sortCode'] = $de;
            $data['Description'] = $description[$key];


            $data['liabilityGlAutoID'] = $liabilityGlAutoID[$key];
            $data['isNonPayroll'] = $payrollType[$key];
            $data['companyID'] = $companyId;
            $data['companyCode'] = $companyCode;
            $data['createdUserGroup'] = $createdUserGroup;
            $data['createdPCID'] = $current_pc;
            $data['createdUserID'] = $createdUserID;
            $data['createdDateTime'] = $current_date;
            $data['createdUserName'] = $createdUserName;
            $data['modifiedPCID'] = $current_pc;
            $data['modifiedUserID'] = $createdUserID;
            $data['modifiedDateTime'] = $current_date;
            $data['modifiedUserName'] = $createdUserName;
            $data['timestamp'] = $current_date;
            if ($isSlab[$key] > 0) {

                $data['SlabID'] = $ifSlab[$key];
            }

            $this->db->insert('srp_erp_payeemaster', $data);
            $payeeID = $this->db->insert_id();


            $append['description'] = $de;
            $append['payeeID'] = $payeeID;
            $append['companyID'] = $companyId;
            $append['companyCode'] = $companyCode;
            $append['createdUserGroup'] = $createdUserGroup;
            $append['createdPCID'] = $current_pc;
            $append['createdUserID'] = $createdUserID;
            $append['createdDateTime'] = $current_date;
            $append['createdUserName'] = $createdUserName;
            $append['modifiedPCID'] = $current_pc;
            $append['modifiedUserID'] = $createdUserID;
            $append['modifiedDateTime'] = $current_date;
            $append['modifiedUserName'] = $createdUserName;
            $append['timestamp'] = $current_date;

            $this->db->insert('srp_erp_paygroupmaster', $append);
            $payGroupID = $this->db->insert_id();

            $appendFiled['fieldName'] = $de;
            $appendFiled['caption'] = $de;
            $appendFiled['fieldType'] = 'G';
            /*$appendFiled['isCalculate'] = 0;*/
            $appendFiled['payGroupID'] = $payGroupID;
            $appendFiled['companyID'] = $companyId;

            $appendFiled['companyCode'] = $companyCode;
            $appendFiled['createdUserGroup'] = $createdUserGroup;
            $appendFiled['createdPCID'] = $current_pc;
            $appendFiled['createdUserID'] = $createdUserID;
            $appendFiled['createdDateTime'] = $current_date;
            $appendFiled['createdUserName'] = $createdUserName;
            $appendFiled['modifiedPCID'] = $current_pc;
            $appendFiled['modifiedUserID'] = $createdUserID;
            $appendFiled['modifiedDateTime'] = $current_date;
            $appendFiled['modifiedUserName'] = $createdUserName;
            $appendFiled['timestamp'] = $current_date;
            $this->db->insert('srp_erp_pay_templatefields', $appendFiled);

        }


        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records inserted successfully');
        } else {
            return array('e', 'Error in insert record');
        }
    }

    function editPayeeMaster()
    {
        $sortCode = $this->input->post('siSortCode');
        $description = $this->input->post('siDes');
        $liabilityGlAutoID = $this->input->post('si_liabilityGlAutoID');
        $hidden_id = $this->input->post('hidden-id');
        $siIsSlab = $this->input->post('siIsSlab');//on
        $siSlab = $this->input->post('siSlab');

        $companyId = current_companyID();
        $companyCode = current_companyCode();
        $createdUserGroup = current_user_group();
        $current_pc = current_pc();
        $createdUserID = current_userID();
        $current_date = current_date();
        $createdUserName = current_user();

        $data = array(
            'sortCode' => $sortCode,
            'Description' => $description,
            'liabilityGlAutoID' => $liabilityGlAutoID,
            'SlabID' => $siSlab,
            'modifiedPCID' => $current_pc,
            'modifiedUserID' => $createdUserID,
            'ModifiedUserName' => $createdUserName,
            'modifiedDateTime' => $current_date,
            'timestamp' => $current_date,
        );


        $payGroupData = array(
            'Description' => $sortCode,
            'modifiedPCID' => $current_pc,
            'modifiedUserID' => $createdUserID,
            'ModifiedUserName' => $createdUserName,
            'modifiedDateTime' => $current_date,
            'timestamp' => $current_date,
        );


        $this->db->where('payeeMasterID', $hidden_id)->update('srp_erp_payeemaster', $data);
        $this->db->where('payeeID', $hidden_id)->where('companyID', current_companyID())->update('srp_erp_paygroupmaster', $payGroupData);

        $pagGroupID = $this->db->where('payeeID', $hidden_id)->select('payGroupID')->from('srp_erp_paygroupmaster')->get()->row_array();


        $filedData['fieldName'] = $sortCode;
        $filedData['caption'] = $sortCode;
        $filedData['modifiedPCID'] = $current_pc;
        $filedData['modifiedUserID'] = $createdUserID;
        $filedData['modifiedDateTime'] = $current_date;
        $filedData['modifiedUserName'] = $createdUserName;
        $filedData['timestamp'] = $current_date;

        $this->db->where('payGroupID', $pagGroupID['payGroupID'])->where('companyID', current_companyID())->update('srp_erp_pay_templatefields', $filedData);

        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }
    }

    function deletePayeeMaster()
    {
        $hidden_id = $this->input->post('hidden-id');


        $this->db->where('payeeMasterID', $hidden_id)->delete('srp_erp_payeemaster ');
        $deltesRows = $this->db->affected_rows();
        $pagGroupID = $this->db->where('payeeID', $hidden_id)->select('payGroupID')->from('srp_erp_paygroupmaster')->get()->row_array();
        $this->db->where('payGroupID', $pagGroupID['payGroupID'])->delete('srp_erp_paygroupformula');
        $this->db->where('payeeID', $hidden_id)->delete('srp_erp_paygroupmaster');

        $this->db->where('payGroupID', $pagGroupID['payGroupID'])->where('fieldType', 'G')->delete('srp_erp_pay_templatefields');


        if ($deltesRows > 0) {
            return array('s', 'Records deleted successfully');
        } else {
            return array('e', 'Error in deleting process');
        }

    }

    /*function insert_default_dashboard_for_all_employee()
    {
        $getDashboard = $this->db->query("select * from srp_erp_systemuserdashboardmaster")->result_array();
        $getemployeeDetails = $this->db->query("select EIdNo,Erp_companyID from srp_employeesdetails")->result_array();
        foreach ($getemployeeDetails as $edetail) {
            $empID=$edetail['EIdNo'];
            $companyID=$edetail['Erp_companyID'];
            foreach ($getDashboard as $val) {
                $userDashboardWidgetID = $val['userDashboardID'];
                $dashborddata['employeeID'] = $empID;
                $dashborddata['dashboardDescription'] = $val['dashboardDescription'];
                $dashborddata['templateID'] = $val['templateID'];
                $dashborddata['companyID'] = $companyID;
                $dashborddata['isDefault'] = 1;
                $insertDashBoard = $this->db->insert('srp_erp_userdashboardmaster', $dashborddata);
                $userDashboardID = $this->db->insert_id();
                if ($insertDashBoard) {
                    $this->db->query("INSERT INTO srp_erp_userdashboardwidget (userDashboardID,positionID,widgetID,sortOrder,employeeID,companyID  ) select $userDashboardID as userDashboardID, positionID,widgetID,sortOrder, $empID as empID, $companyID as comid from srp_erp_systemuserdashboardwidget where userDashboardID= $userDashboardWidgetID");
                }
            }
        }
    }*/

    function load_declaration_drilldown_table()
    {
        $companyID = current_companyID();
        $masterID = $this->input->post('masterID');
        $employeeid = $this->input->post('employeeID');
        $convertFormat = convert_date_format_sql();


        /*$this->db->select('srp_erp_salarydeclarationdetails.declarationDetailID,declarationMasterID,employeeNo,srp_erp_salarydeclarationdetails.salaryCategoryID,
                           srp_erp_salarydeclarationdetails.salaryCategoryType,effectiveDate,srp_employeesdetails.ECode,srp_employeesdetails.Ename2,
                           srp_erp_pay_salarycategories.salaryDescription,transactionAmount, DATE_FORMAT(effectiveDate, \''.$convertFormat.'\') AS effectiveDate2,
                           payDate, DATE_FORMAT(payDate,\''.$convertFormat.'\') AS payDate2, narration, balanceAmount');
        $this->db->where('declarationMasterID', $masterID);
        $this->db->where('employeeNo', $employeeid);
        $this->db->from('srp_erp_salarydeclarationdetails');
        if(!empty($employeeid )){
            $this->db->join('( SELECT balanceAmount, detailID FROM srp_erp_pay_balancepayment
                            WHERE companyID='.$companyID.' AND empID='.$employeeid.' AND declarationDetailID='.$masterID.' ) AS balanceTB',
                'balanceTB.detailID = srp_erp_salarydeclarationdetails.declarationMasterID', 'left');
        }
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_salarydeclarationdetails.employeeNo');
        $this->db->join('srp_erp_pay_salarycategories1', 'srp_erp_pay_salarycategories.salaryCategoryID = srp_erp_salarydeclarationdetails.salaryCategoryID');*/

        $this->db->select('srp_erp_salarydeclarationdetails.declarationDetailID,declarationMasterID,employeeNo,srp_erp_salarydeclarationdetails.salaryCategoryID,
                           srp_erp_salarydeclarationdetails.salaryCategoryType,effectiveDate,srp_employeesdetails.ECode,srp_employeesdetails.Ename2,
                           srp_erp_pay_salarycategories.salaryDescription,transactionAmount, DATE_FORMAT(effectiveDate, \'' . $convertFormat . '\') AS effectiveDate2,
                           payDate, DATE_FORMAT(payDate,\'' . $convertFormat . '\') AS payDate2, narration, transactionCurrencyDecimalPlaces AS trDPlace');
        $this->db->where('declarationMasterID', $masterID);
        $this->db->where('employeeNo', $employeeid);
        $this->db->from('srp_erp_salarydeclarationdetails');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_salarydeclarationdetails.employeeNo');
        $this->db->join('srp_erp_pay_salarycategories', 'srp_erp_pay_salarycategories.salaryCategoryID = srp_erp_salarydeclarationdetails.salaryCategoryID');
        return $this->db->get()->result_array();

    }

    /*Employee type*/
    function saveEmployeeType()
    {
        $description = trim($this->input->post('description'));
        $conType = $this->input->post('conType');
        $period = $this->input->post('period');
        $period = (!empty($period)) ? $period : 0;

        $exist = $this->db->select('Description')
            ->from('srp_empcontracttypes')
            ->where('Description', $description)
            ->where('Erp_companyID', current_companyID())
            ->get()->row('Description');

        if ($exist) {
            return ['e', 'This description is already available.'];
        }


        $data['Description'] = $description;
        $data['typeID'] = $conType;
        $data['period'] = $period;
        $data['SchMasterId'] = current_schMasterID();
        $data['branchID'] = current_schBranchID();
        $data['Erp_companyID'] = current_companyID();
        $data['CreatedPC'] = current_pc();
        $data['CreatedUserName'] = current_employee();
        $data['CreatedDate'] = current_date();
        $data['Timestamp'] = current_date();

        $this->db->insert('srp_empcontracttypes', $data);

        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records inserted successfully');
        } else {
            return array('e', 'Error in record insert');
        }
    }

    function deleteEmployeeDetail()
    {
        $hidden_id = $this->input->post('hidden-id');

        $isInUse = $this->db->query("SELECT EmployeeConType FROM srp_employeesdetails WHERE EmployeeConType={$hidden_id}")->row('EmployeeConType');

        if (isset($isInUse)) {
            return array('e', 'This Employee Type is in use</br>You can not delete this');
        } else {
            $this->db->where('EmpContractTypeID', $hidden_id)->delete('srp_empcontracttypes');
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Records deleted successfully');
            } else {
                return array('e', 'Error in deleting process');
            }
        }
    }


    function editEmployeeDetails()
    {
        $description = trim($this->input->post('description'));
        $period = $this->input->post('period');
        $period = (!empty($period)) ? $period : 0;
        $hidden_id = $this->input->post('hidden-id');

        $data = array(
            'Description' => $description,
            'period' => $period,
            'ModifiedPC' => current_pc(),
            'ModifiedUserName' => current_employee(),
            'Timestamp' => current_date(),
        );

        $this->db->where('EmpContractTypeID', $hidden_id)->where('Erp_companyID', current_companyID())->update('srp_empcontracttypes', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }
    }

    function discharge_update()
    {
        $isDischarged = $this->input->post('isDischarged');
        $dischargedDate = $this->input->post('dischargedDate');
        $lastWorkingDate = $this->input->post('lastWorkingDate');
        $updateID = $this->input->post('updateID');
        $dischargedComment = $this->input->post('dischargedComment');

        $date_format_policy = date_format_policy();
        $dischargedDate = input_format_date($dischargedDate, $date_format_policy);
        $lastWorkingDate = input_format_date($lastWorkingDate, $date_format_policy);

        $data = array(
            'isDischarged' => $isDischarged,
            'isLeft' => $isDischarged,
            'dischargedByEmpID' => current_userID(),
            'dischargedDate' => $dischargedDate,
            'DateLeft' => $dischargedDate,
            'lastWorkingDate' => $lastWorkingDate,
            'dischargedComment' => $dischargedComment,
            'LeftComment' => $dischargedComment,
            'ModifiedPC' => current_pc(),
            'ModifiedUserName' => current_employee(),
            'Timestamp' => current_date(),
        );

        $this->db->where('EIdNo', $updateID)->update('srp_employeesdetails', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }
    }

    public function save_leave_annualAccrual()
    {
        $this->db->trans_begin();
        $companyID = current_companyID();
        $description = $this->input->post('description');
        $leaveGroupID = $this->input->post('leaveGroupID');
        $masterID = $this->input->post('masterID');
        $this->load->library('sequence');
        $code = $this->sequence->sequence_generator('LAM');


        if ($masterID == '') {
            $data = array(
                'companyID' => current_companyID(),
                'leaveaccrualMasterCode' => $code,
                'documentID' => 'LAM',
                'description' => $description,
                'year' => date('Y'),
                'month' => date('m'),
                'leaveGroupID' => $leaveGroupID,
                'createdUserGroup' => current_user_group(),
                'createDate' => date('Y-m-d H:i:s'),
                'createdpc' => current_pc(),
                /* 'confirmedYN' => 1,
                 'confirmedby' => $this->common_data['current_userID'],
                 'confirmedDate' => current_date(),*/
                'policyMasterID' => 1

            );
            $insert = $this->db->insert('srp_erp_leaveaccrualmaster', $data);
            if ($insert) {
                $last_id = $this->db->insert_id();
                $detail = array();
                $date = date('Y');
                //   $q2 = "SELECT DateAssumed, CONCAT(EIdNo, '-', srp_erp_leavetype.leaveTypeID) AS leaveTypeKey, EIdNo, srp_employeesdetails.leaveGroupID, srp_erp_leavegroupdetails.*, policyID FROM `srp_employeesdetails` INNER JOIN `srp_erp_leavegroupdetails` ON srp_erp_leavegroupdetails.leaveGroupID = srp_employeesdetails.leaveGroupID AND DateAssumed <= '{$date}' INNER JOIN `srp_erp_leavetype` ON srp_erp_leavegroupdetails.leaveTypeID = srp_erp_leavetype.leaveTypeID WHERE Erp_companyID = {$companyID} AND srp_employeesdetails.leaveGroupID IS NOT NULL AND srp_employeesdetails.leaveGroupID = {$leaveGroupID} AND (EIdNo , srp_erp_leavetype.leaveTypeID) NOT IN (SELECT empID, leaveType FROM `srp_erp_leaveaccrualmaster` INNER JOIN srp_erp_leaveaccrualdetail ON srp_erp_leaveaccrualmaster.leaveaccrualMasterID = srp_erp_leaveaccrualdetail.leaveaccrualMasterID WHERE year = {$d[1]} AND month = {$d[0]} AND srp_erp_leaveaccrualmaster.leaveaccrualMasterID != {$last_id} AND srp_erp_leaveaccrualmaster.manualYN=0 GROUP BY empID , leaveType)";

                // $q2="SELECT * FROM srp_employeesdetails WHERE NOT EXISTS( SELECT * FROM srp_erp_leaveaccrualdetail WHERE srp_employeesdetails.EIdNo = empID AND leaveGroupID = 5 AND nextDate={$date} GROUP BY empID) AND srp_employeesdetails.leaveGroupID = {$leaveGroupID} AND isActive=1 ";

                $q2 = "SELECT * FROM srp_employeesdetails inner JOIN(select * from `srp_erp_leavegroupdetails` WHERE leaveGroupID = {$leaveGroupID} AND policyMasterID=1 ) leavegroup on leavegroup.leaveGroupID=srp_employeesdetails.leaveGroupID WHERE NOT EXISTS( SELECT * FROM srp_erp_leaveaccrualdetail WHERE srp_employeesdetails.EIdNo = empID AND leaveGroupID = {$leaveGroupID} AND nextDate!={$date} GROUP BY empID) AND srp_employeesdetails.leaveGroupID = {$leaveGroupID} AND isDischarged !=1 AND  Erp_companyID={$companyID}";


                $result = $this->db->query($q2)->result_array();

                $exist = $this->db->query("SELECT concat(det.empID,'-',det.leaveGroupID,'-',det.leaveType) as leavekey FROM `srp_erp_leaveaccrualmaster` INNER JOIN (SELECT * FROM `srp_erp_leaveaccrualdetail` WHERE nextDate IS NOT NULL) det ON srp_erp_leaveaccrualmaster.leaveaccrualMasterID=det.leaveaccrualMasterID WHERE companyID = {$companyID} AND initalDate={$date} ")->result_array();


                $updateArr = array();
                $insert_Arr = array();
                if ($result) {
                    foreach ($result as $val) {
                        $daysEntitled = 0;
                        $hoursEntitled = 0;

                        $daysEntitled = $val['noOfDays'];


                        $datas = array('leaveaccrualMasterID' => $last_id, 'empID' => $val['EIdNo'],
                            'leaveGroupID' => $leaveGroupID, 'leaveType' => $val['leaveTypeID'],
                            'daysEntitled' => $daysEntitled, 'hoursEntitled' => $hoursEntitled,
                            'description' => 'Leave Accrual ' . date('Y'),
                            'createDate' => date('Y-m-d H:i:s'),
                            'createdUserGroup' => current_user_group(), 'createdPCid' => current_pc(),
                            'initalDate' => date('Y'), 'nextDate' => date('Y') + 1,
                        );

                        $keys = array_keys(array_column($exist, 'leavekey'), $val['EIdNo'] . '-' . $val['leaveGroupID'] . '-' . $val['leaveTypeID']);
                        $new_array = array_map(function ($k) use ($exist) {
                            return $exist[$k];
                        }, $keys);


                        /* array_push($insert_Arr, array(
                             'leaveTypeID' => $val['leaveTypeID'], 'empID' => $val['EIdNo'], 'days' => $daysEntitled, 'hourly' => $hoursEntitled, 'companyID' => current_companyID(), 'companyCode' => current_companyCode(), 'createdUserGroup' => '',
                             'createdPCID' => $this->common_data['current_pc'],
                             'createdUserID' => $this->common_data['current_userID'],
                             'createdDateTime' => current_date(),
                             'createdUserName' => $this->common_data['current_user'],
                         ));*/
                        if (empty($new_array)) {
                            array_push($detail, $datas);
                        }

                    }

                    if (!empty($detail)) {
                        $this->db->insert_batch('srp_erp_leaveaccrualdetail', $detail);
                    }

                }
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('e', 'Failed.');

                    echo json_encode(array('error' => 1));
                    exit;
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('s', 'Leave Accrual inserted successfully.');

                    echo json_encode(array('error' => 0, 'leaveGroupID' => $last_id));
                    exit;
                }


            } else {
                $this->session->set_flashdata('e', 'Failed');

                echo json_encode(array('error' => 1));
                exit;
            }

        } else {


            //   $this->session->set_flashdata('s', 'Leave group inserted successfully.');

            echo json_encode(array('error' => 0, 'leaveGroupID' => $masterID));
            exit;
        }

    }

    function save_employeesLeave()
    {

        $empID = $this->input->post('empName');
        $entryDate = $this->input->post('entryDate');
        $leaveTypeID = $this->input->post('leaveTypeID');
        $startDate = $this->input->post('startDate');
        $endDate = $this->input->post('endDate');
        $halfDay = $this->input->post('halfDay');
        $comment = $this->input->post('comment');
        $isCalenderDays = $this->input->post('isCalenderDays');
        $entitleSpan = $this->input->post('entitleSpan');
        $appliedLeave = $this->input->post('appliedLeave');
        $leaveBlance = $this->input->post('leaveBlance');
        $workingDays = $this->input->post('workingDays');
        $policyMasterID = $this->input->post('policyMasterID');
        $leaveGroupID = $this->input->post('leaveGroupID');
        $applicationType = $this->input->post('applicationType');
        $coveringEmpID = $this->input->post('coveringEmpID');
        $coveringEmpID = (!empty($coveringEmpID)) ? $coveringEmpID : 0;
        $companyID = $this->common_data['company_data']['company_id'];
        $companyCode = $this->common_data['company_data']['company_code'];
        $createdPCID = $this->common_data['current_pc'];
        $createdUserID = $this->common_data['current_userID'];
        $createdUserName = $this->common_data['current_user'];
        $createdUserGroup = $this->common_data['user_group'];
        $createdDateTime = current_date();
        $confirmedYN = $this->input->post('isConfirmed');
        $hour = 0;

        //Get last leave no
        $lastCodeArray = $this->db->query("SELECT serialNo FROM srp_erp_leavemaster WHERE companyID={$companyID}
                                                ORDER BY leaveMasterID DESC LIMIT 1")->row_array();
        $lastCodeNo = $lastCodeArray['serialNo'];
        $lastCodeNo = ($lastCodeNo == NULL) ? 1 : $lastCodeArray['serialNo'] + 1;

        $this->load->library('sequence');
        $dCode = $this->sequence->sequence_generator('LA', $lastCodeNo);

        if ($isCalenderDays == 1) {
            $days = $appliedLeave;
            $workingDays = 0;
            $nonWorkingDays = $days;
            $leaveAvailable = $entitleSpan;

        } else {

            $days = $workingDays;
            $nonWorkingDays = $appliedLeave;
            $leaveAvailable = $entitleSpan;

        }

        if ($policyMasterID == 2) {
            /*if its hourly set value for hour and clear*/
            $hour = $days;
            $days = 0;
            $nonWorkingDays = 0;
            $dteStart = new DateTime($startDate);
            $dteEnd = new DateTime($endDate);


            $startDate = $dteStart->format('Y-m-d H:i:s');
            $endDate = $dteEnd->format('Y-m-d H:i:s');

        }

        $data = array(

            'empID' => $empID,
            'leaveTypeID' => $leaveTypeID,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'days' => $days,
            'ishalfDay' => $halfDay,
            'isCalenderDays' => $isCalenderDays,
            'workingDays' => $workingDays,
            'nonWorkingDays' => $nonWorkingDays,
            'leaveGroupID' => $leaveGroupID,
            'policyMasterID' => $policyMasterID,
            'applicationType' => $applicationType,
            'leaveAvailable' => $leaveAvailable,
            'documentCode' => $dCode,
            'serialNo' => $lastCodeNo,
            'hours' => $hour,
            'entryDate' => date('Y-m-d'),
            'coveringEmpID' => $coveringEmpID,
            'comments' => $comment,
            'companyID' => $companyID,
            'companyCode' => $companyCode,
            'createdPCID' => $createdPCID,
            'createdUserID' => $createdUserID,
            'createdUserGroup' => $createdUserGroup,
            'createdDateTime' => $createdDateTime,
        );

        if ($confirmedYN == 1) {
            $data['confirmedYN'] = 1;
            $data['confirmedByEmpID'] = $this->common_data['current_userID'];
            $data['confirmedByName'] = $this->common_data['current_user'];
            $data['confirmedDate'] = current_date();
        } else {
            $data['confirmedYN'] = 0;
        }


        $document_file = $this->input->post('document_file');
        if ($this->input->post('isConfirmed') == 1) {
            if (empty($_FILES['document_file']['name'])) {

                $leaveTypeID = $this->input->post('leaveTypeID');
                $isRequiredYes = $this->db->query("select * from srp_erp_leavetype WHERE  leaveTypeID=$leaveTypeID AND attachmentRequired=1 ")->row_array();
                if (!empty($isRequiredYes)) {
                    $leaveMasterID = $this->input->post('leaveMasterID');
                    $attachmentExist = $this->db->query("SELECT * FROM srp_erp_documentattachments WHERE documentID = 'LA' AND
                                                         documentSystemCode='$leaveMasterID'")->row_array();
                    if (empty($attachmentExist)) {
                        echo exit(json_encode(array('e', 'Please attach releavant document to confirm')));

                    }
                }
            }


        }
        if (!empty($_FILES['document_file']['name'])) {
            $attachmentDesc = $this->input->post('attachmentDescription');
            if ($attachmentDesc == '') {
                return array('e', 'Please enter attachment description ');
                exit;
            }
        }

        $this->db->trans_start();
        $this->db->insert('srp_erp_leavemaster', $data);
        $leaveMasterID = $this->db->insert_id();
        if (!empty($_FILES['document_file']['name'])) {
            $this->db->select('companyID');
            $this->db->where('documentID', 'LA');
            $num = $this->db->get('srp_erp_documentattachments')->result_array();
            $file_name = 'LA' . '_' . $leaveMasterID . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if ($this->upload->do_upload("document_file")) {
                $attachmentDesc = $this->input->post('attachmentDescription');

                $upload_data = $this->upload->data();
                //$fileName                       = $file_name.'_'.$upload_data["file_ext"];
                $detail['documentID'] = 'LA';
                $detail['documentSystemCode'] = $leaveMasterID;
                $detail['attachmentDescription'] = trim($this->input->post('attachmentDescription'));
                $detail['myFileName'] = $file_name . $upload_data["file_ext"];
                $detail['fileType'] = trim($upload_data["file_ext"]);
                $detail['fileSize'] = trim($upload_data["file_size"]);
                $detail['timestamp'] = date('Y-m-d H:i:s');
                $detail['companyID'] = $this->common_data['company_data']['company_id'];
                $detail['companyCode'] = $this->common_data['company_data']['company_code'];
                $detail['createdUserGroup'] = $this->common_data['user_group'];
                $detail['modifiedPCID'] = $this->common_data['current_pc'];
                $detail['modifiedUserID'] = $this->common_data['current_userID'];
                $detail['modifiedUserName'] = $this->common_data['current_user'];
                $detail['modifiedDateTime'] = current_date();
                $detail['createdPCID'] = $this->common_data['current_pc'];
                $detail['createdUserID'] = $this->common_data['current_userID'];
                $detail['createdUserName'] = $this->common_data['current_user'];
                $detail['createdDateTime'] = current_date();
                $this->db->insert('srp_erp_documentattachments', $detail);
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Failed Insert Data');
        } else {
            $this->db->trans_commit();

            if ($confirmedYN == 1) {
                /*$leaveBalanceData = $this->employeeLeaveSummery($empID, $leaveTypeID, $policyMasterID);
                $balanceLeave = $leaveBalanceData['balance'];
                $balanceLeave = ($balanceLeave > 0)?  ($balanceLeave - $days) : 0;

                $this->db->select('*');
                $this->db->from('srp_erp_employeemanagers');
                $this->db->join('srp_employeesdetails', 'managerID=EIdNo', 'left');
                $this->db->where('empID', $empID);
                $this->db->where('active', 1);
                $result = $this->db->get()->result_array();

                foreach ($result as $val) {

                    $param["empName"] = $val["Ename2"];
                    $param["body"] = 'Leave application ' . $dCode . ' is pending for your approval.<br/>
                                      <table border="0px">
                                            <tr><td><strong>Leave type </td><td> : '.$leaveBalanceData['description'].'</td></tr>
                                            <tr><td><strong>Leave balance </td><td> : '.$balanceLeave.'</td></tr>
                                      </table>';

                    $mailData = [
                        'approvalEmpID' => $val['managerID'],
                        'documentCode' => $dCode,
                        'toEmail' => $val["EEmail"],
                        'subject' => 'Leave Approval',
                        'param' => $param,
                    ];

                    send_approvalEmail($mailData);
                }

                return array('s', 'Leave Approval Created.');*/
                return $this->leave_ApprovalCreate($leaveMasterID, $level = 1);
            } else {
                return array('s', 'Leave Save Process Success.');
            }

        }


    }

    function add_employees_to_shift()
    {
        $shiftID = $this->input->post('shieftIDhn');
        $empID = $this->input->post('empHiddenID');
        /* $endDate = $this->input->post('endDate');
         $startDate = $this->input->post('startDate');*/

        $date_format_policy = date_format_policy();

        $endDat = $this->input->post('endDate');
        if (!empty($endDat)) {
            $endDate = input_format_date($endDat, $date_format_policy);
        }

        $strtdt = $this->input->post('startDate');
        $startDate = input_format_date($strtdt, $date_format_policy);
        if (empty($endDat)) {
            $endDate = '2100-01-01';
        }
        $result = '';
        if ($endDate >= $startDate) {
            foreach ($empID as $val) {

                $getShieft = $this->db->query("SELECT shiftID FROM srp_erp_pay_shiftemployees WHERE shiftID={$shiftID}
                                            AND empID='$val'")->row_array();
                if (!empty($getShieft)) {
                    continue;
                } else {
                    $data = array(
                        'shiftID' => $shiftID,
                        'empID' => $val,
                        'startDate' => $startDate,
                        'endDate' => $endDate,
                        'companyID' => current_companyID(),
                        'companyCode' => current_companyCode(),
                        'createdPCID' => current_pc(),
                        'createdUserGroup' => current_user_group(),
                        'createdUserID' => current_userID(),
                        'createdUserName' => current_employee(),
                        'createdDateTime' => current_date()
                    );

                    $result = $this->db->insert('srp_erp_pay_shiftemployees', $data);
                }
            }
            if ($result) {
                return array('s', 'Employees successfully assign to the shift.');
            } else {
                return array('e', 'This Employee has been Assign to this shift.');
            }
        } else {
            return array('e', 'End date should be grater than or equal to Start date.');
        }

    }

    function update_employeesLeave()
    {
        $leaveMasterID = $this->input->post('leaveMasterID');
        $empID = $this->input->post('empName');
        $leaveType = $this->input->post('leaveTypeID');
        if ($this->input->post('policyMasterID') == 2) {
            $startDate = $this->input->post('startDatetime');
            $endDate = $this->input->post('endDatetime');
        } else {
            $startDate = $this->input->post('startDate');
            $endDate = $this->input->post('endDate');
        }

        $isConfirmed = $this->input->post('isConfirmed');
        $entitleSpan = $this->input->post('entitleSpan');
        $entryDate = $this->input->post('entryDate');
        $halfDay = $this->input->post('halfDay');
        $comment = $this->input->post('comment');
        $isCalenderDays = $this->input->post('isCalenderDays');
        $appliedLeave = $this->input->post('appliedLeave');
        $leaveBlance = $this->input->post('leaveBlance');
        $workingDays = $this->input->post('workingDays');
        $policyMasterID = $this->input->post('policyMasterID');
        $applicationType = $this->input->post('applicationType');
        $leaveGroupID = $this->input->post('leaveGroupID');
        $coveringEmpID = $this->input->post('coveringEmpID');
        $coveringEmpID = (!empty($coveringEmpID)) ? $coveringEmpID : 0;
        $hour = 0;
        $leaveAvailable = $entitleSpan;


        $det = $this->employeeLeave_details($leaveMasterID);

        if ($det['confirmedYN'] == 1) {
            return (array('e', '[ ' . $det['documentCode'] . ' ] is already confirmed'));
        } else {

            if ($isCalenderDays == 1) {
                $days = $appliedLeave;
                $workingDays = 0;
                $nonWorkingDays = $days;

            } else {
                $days = $workingDays;
                $nonWorkingDays = $appliedLeave;
            }

            if ($policyMasterID == 2) {
                /*if its hourly set value for hour and clear*/
                $hour = $days;
                $days = 0;
                $nonWorkingDays = 0;

                $dteStart = new DateTime($startDate);
                $dteEnd = new DateTime($endDate);
                $startDate = $dteStart->format('Y-m-d H:i:s');
                $endDate = $dteEnd->format('Y-m-d H:i:s');


            }


            $modifiedPCID = $this->common_data['current_pc'];
            $modifiedUserID = $this->common_data['current_userID'];
            $modifiedUserName = $this->common_data['current_user'];
            $modifiedDateTime = current_date();

            $data = array(
                'empID' => $empID,
                'leaveTypeID' => $leaveType,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'days' => $days,
                'ishalfDay' => $halfDay,
                'isCalenderDays' => $isCalenderDays,
                'workingDays' => $workingDays,
                'nonWorkingDays' => $nonWorkingDays,
                'leaveGroupID' => $leaveGroupID,
                'policyMasterID' => $policyMasterID,
                'applicationType' => $applicationType,
                'hours' => $hour,
                'leaveAvailable' => $leaveAvailable,
                'coveringEmpID' => $coveringEmpID,
                'comments' => $comment,
                'modifiedPCID' => $modifiedPCID,
                'modifiedUserID' => $modifiedUserID,
                'modifiedUserName' => $modifiedUserName,
                'modifiedDateTime' => $modifiedDateTime,
            );

            if ($isConfirmed == 1) {
                $data['confirmedYN'] = 1;
                $data['confirmedByEmpID'] = $this->common_data['current_userID'];
                $data['confirmedByName'] = $this->common_data['current_user'];
                $data['confirmedDate'] = current_date();
            } else {
                $data['confirmedYN'] = 0;
            }

            $this->db->trans_start();

            /*attachment */

            $this->db->select('companyID');
            $this->db->where('documentID', 'LA');
            $num = $this->db->get('srp_erp_documentattachments')->result_array();
            $file_name = 'LA' . '_' . $leaveMasterID . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if ($this->upload->do_upload("document_file")) {
                $attachmentDesc = $this->input->post('attachmentDescription');
                if ($attachmentDesc == '') {
                    return array('e', 'Please enter attachment description ');
                    exit;
                }
                $upload_data = $this->upload->data();
                //$fileName                       = $file_name.'_'.$upload_data["file_ext"];
                $detail['documentID'] = 'LA';
                $detail['documentSystemCode'] = $leaveMasterID;
                $detail['attachmentDescription'] = trim($this->input->post('attachmentDescription'));
                $detail['myFileName'] = $file_name . $upload_data["file_ext"];
                $detail['fileType'] = trim($upload_data["file_ext"]);
                $detail['fileSize'] = trim($upload_data["file_size"]);
                $detail['timestamp'] = date('Y-m-d H:i:s');
                $detail['companyID'] = $this->common_data['company_data']['company_id'];
                $detail['companyCode'] = $this->common_data['company_data']['company_code'];
                $detail['createdUserGroup'] = $this->common_data['user_group'];
                $detail['modifiedPCID'] = $this->common_data['current_pc'];
                $detail['modifiedUserID'] = $this->common_data['current_userID'];
                $detail['modifiedUserName'] = $this->common_data['current_user'];
                $detail['modifiedDateTime'] = current_date();
                $detail['createdPCID'] = $this->common_data['current_pc'];
                $detail['createdUserID'] = $this->common_data['current_userID'];
                $detail['createdUserName'] = $this->common_data['current_user'];
                $detail['createdDateTime'] = current_date();
                $this->db->insert('srp_erp_documentattachments', $detail);

            }

            if ($this->input->post('isConfirmed') == 1) {
                $leaveTypeID = $this->input->post('leaveTypeID');
                $isRequiredYes = $this->db->query("select * from srp_erp_leavetype WHERE  leaveTypeID=$leaveTypeID AND attachmentRequired=1 ")->row_array();
                if (!empty($isRequiredYes)) {
                    $leaveMasterID = $this->input->post('leaveMasterID');
                    $attachmentExist = $this->db->query("SELECT * FROM srp_erp_documentattachments WHERE documentID='LA' AND documentSystemCode='$leaveMasterID'")->row_array();
                    if (empty($attachmentExist)) {
                        echo exit(json_encode(array('e', 'Please attach relevant document to confirm')));

                    }
                }
            }

            /*leave Update*/
            $this->db->where('leaveMasterID', $leaveMasterID)->update('srp_erp_leavemaster', $data);


            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();

                return array('e', 'Failed Update Data');
            } else {
                $this->db->trans_commit();

                if ($isConfirmed == 1) {

                    return $this->leave_ApprovalCreate($leaveMasterID, $level = 1);
                    /*$leaveBalanceData = $this->employeeLeaveSummery($empID, $leaveType, $policyMasterID);
                    $balanceLeave = $leaveBalanceData['balance'];
                    $balanceLeave = ($balanceLeave > 0)?  ($balanceLeave - $days) : 0;

                    $this->db->select('*');
                    $this->db->from('srp_erp_employeemanagers');
                    $this->db->join('srp_employeesdetails', 'managerID=EIdNo', 'left');
                    $this->db->where('empID', $empID);
                    $this->db->where('active', 1);
                    $result = $this->db->get()->result_array();
                    foreach ($result as $val) {

                        $param["empName"] = $val["Ename2"];
                        $param["body"] = 'Leave application ' . $det['documentCode'] . ' is pending for your approval.<br/>
                                          <table border="0px">
                                                <tr><td><strong>Leave type </td><td> : '.$leaveBalanceData['description'].'</td></tr>
                                                <tr><td><strong>Leave balance </td><td> : '.$balanceLeave.'</td></tr>
                                          </table>';

                        $mailData = [
                            'approvalEmpID' => $val['managerID'],
                            'documentCode' => $det['documentCode'],
                            'toEmail' => $val["EEmail"],
                            'subject' => 'Leave Approval',
                            'param' => $param,
                        ];

                        send_approvalEmail($mailData);
                    }

                    return ['s', 'Leave Approval created successfully.'];*/

                } else {
                    return ['s', 'Leave Update Process Success.'];
                }
            }
        }

    }

    function leave_ApprovalCreate($leaveMasterID, $level)
    {
        $companyID = current_companyID();
        $current_userID = current_userID();
        $leave = $this->db->query("SELECT leaveMaster.*, empTB.Ename2, EEmail, ECode AS empCode, coveringEmpID 
                                   FROM srp_erp_leavemaster AS leaveMaster
                                   JOIN srp_employeesdetails AS empTB ON empID=empTB.EIdNo
                                   WHERE leaveMasterID={$leaveMasterID} AND companyID={$companyID}")->row_array();
        $empID = $leave['empID'];
        $coveringEmpID = $leave['coveringEmpID'];


        $setupData = getLeaveApprovalSetup();
        $approvalEmp_arr = $setupData['approvalEmp'];
        $approvalLevel = $setupData['approvalLevel'];
        $isManagerAvailableForNxtApproval = 0;
        $nextLevel = null;
        $nextApprovalEmpID = null;
        $data_app = [];


        /**** If the number of approval level is less than current approval than only this process will run ****/
        if ($level <= $approvalLevel) {

            $managers = $this->db->query("SELECT *, {$coveringEmpID} AS coveringEmp FROM (
                                             SELECT repManager
                                             FROM srp_employeesdetails AS empTB
                                             LEFT JOIN (
                                                 SELECT empID, managerID AS repManager FROM srp_erp_employeemanagers
                                                 WHERE active = 1 AND empID={$empID} AND companyID={$companyID}
                                             ) AS repoManagerTB ON empTB.EIdNo = repoManagerTB.empID
                                             WHERE Erp_companyID = '{$companyID}' AND EIdNo={$empID}
                                         ) AS empData
                                         LEFT JOIN (
                                              SELECT managerID AS topManager, empID AS topEmpID
                                              FROM srp_erp_employeemanagers WHERE companyID={$companyID} AND active = 1
                                         ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID")->row_array();

            $approvalSetup = $setupData['approvalSetup'];
            $x = $level;


            /**** Validate is there a manager available for next approval level ****/

            $i = 0;

            while ($x <= $approvalLevel) {

                $isCurrentLevelApproval_exist = 0;
                $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $x);
                $arr = array_map(function ($k) use ($approvalSetup) {
                    return $approvalSetup[$k];
                }, $keys);

                $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';

                if ($approvalType == 3) {
                    $isCurrentLevelApproval_exist = 1;

                    if ($isManagerAvailableForNxtApproval == 0) {
                        $nextLevel = $x;
                        $nextApprovalEmpID = (array_key_exists($x, $approvalEmp_arr)) ? $approvalEmp_arr[$x] : '';
                        $isManagerAvailableForNxtApproval = 1;
                    }
                } else {
                    $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                    if (!empty($managers[$managerType])) {
                        $isCurrentLevelApproval_exist = 1;

                        if ($isManagerAvailableForNxtApproval == 0) {
                            $nextLevel = $x;
                            $nextApprovalEmpID = $managers[$managerType];
                            $isManagerAvailableForNxtApproval = 1;
                        }
                    }

                }

                if ($isCurrentLevelApproval_exist == 1) {
                    $data_app[$i]['companyID'] = $companyID;
                    $data_app[$i]['companyCode'] = current_companyCode();
                    $data_app[$i]['departmentID'] = 'LA';
                    $data_app[$i]['documentID'] = 'LA';
                    $data_app[$i]['documentSystemCode'] = $leaveMasterID;
                    $data_app[$i]['documentCode'] = $leave['documentCode'];
                    $data_app[$i]['table_name'] = 'srp_erp_leavemaster';
                    $data_app[$i]['table_unique_field_name'] = 'leaveMasterID';
                    $data_app[$i]['documentDate'] = current_date();
                    $data_app[$i]['approvalLevelID'] = $x;
                    $data_app[$i]['roleID'] = null;
                    $data_app[$i]['approvalGroupID'] = current_user_group();
                    $data_app[$i]['roleLevelOrder'] = null;
                    $data_app[$i]['docConfirmedDate'] = current_date();
                    $data_app[$i]['docConfirmedByEmpID'] = $current_userID;
                    $data_app[$i]['approvedEmpID'] = null;
                    $data_app[$i]['approvedYN'] = 0;
                    $data_app[$i]['approvedDate'] = null;
                    $i++;
                }

                $x++;
            }

        }
        //echo '<pre>'; print_r($data_app); echo '</pre>';        die();
        if (!empty($data_app)) {

            $this->db->insert_batch('srp_erp_documentapproved', $data_app);

            $upData = [
                'currentLevelNo' => $nextLevel,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $current_userID,
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => current_date()
            ];
            $this->db->where('leaveMasterID', $leaveMasterID);
            $update = $this->db->update('srp_erp_leavemaster', $upData);

            if ($update) {
                $leaveBalanceData = $this->employeeLeaveSummery($empID, $leave['leaveTypeID'], $leave['policyMasterID']);
                $balanceLeave = $leaveBalanceData['balance'];
                $balanceLeave = ($balanceLeave > 0) ? ($balanceLeave - $leave['days']) : 0;

                if (is_array($nextApprovalEmpID)) {
                    /**** If the approval type HR there may be more than one employee for next approval process ****/
                    $nextApprovalEmpID = implode(',', array_column($nextApprovalEmpID, 'empID'));
                }

                $nxtEmpData_arr = $this->db->query("SELECT EIdNo, Ename2, EEmail FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                            AND EIdNo IN ({$nextApprovalEmpID})")->result_array();

                foreach ($nxtEmpData_arr as $nxtEmpData) {

                    $bodyData = 'Leave application ' . $leave['documentCode'] . ' is pending for your approval.<br/>
                                  <table border="0px">
                                      <tr><td><strong>Name </td><td> : ' . $leave['Ename2'] . ' - ' . $leave['empCode'] . '</td></tr>
                                      <tr><td><strong>Start Date</td><td> : ' . date('Y-m-d', strtotime($leave['startDate'])) . '</td></tr>
                                      <tr><td><strong>End Date</td><td> : ' . date('Y-m-d', strtotime($leave['endDate'])) . '</td></tr> ';

                    if ($coveringEmpID != $nxtEmpData['EIdNo']) {
                        $bodyData .= '<tr><td><strong>Leave type </td><td> : ' . $leaveBalanceData['description'] . '</td></tr>
                                      <tr><td><strong>Leave balance </td><td> : ' . $balanceLeave . '</td></tr>';
                    }

                    $bodyData .= '</table>';

                    $param["empName"] = $nxtEmpData["Ename2"];
                    $param["body"] = $bodyData;

                    $mailData = [
                        'approvalEmpID' => $nxtEmpData["EIdNo"],
                        'documentCode' => $leave['documentCode'],
                        'toEmail' => $nxtEmpData["EEmail"],
                        'subject' => 'Leave Approval',
                        'param' => $param
                    ];

                    send_approvalEmail($mailData);
                }

                return ['s', 'Leave Approval created successfully.'];


            } else {
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                return array('e', $common_failed);
            }

        } else {

            $data = array(
                'currentLevelNo' => $approvalLevel,
                'approvedYN' => 1,
                'approvedDate' => current_date(),
                'approvedbyEmpID' => $current_userID,
                'approvedbyEmpName' => $this->common_data['current_user'],
                'approvalComments' => '',
            );

            $this->db->trans_start();

            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->update('srp_erp_leavemaster', $data);

            /**** Confirm leave accrual pending*/
            $accrualData = [
                'confirmedYN' => 1,
                'confirmedby' => current_userID(),
                'confirmedDate' => current_date()
            ];

            $this->db->where('companyID', $companyID);
            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('confirmedYN', 0);
            $this->db->update('srp_erp_leaveaccrualmaster', $accrualData);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                $leaveBalanceData = $this->employeeLeaveSummery($empID, $leave['leaveTypeID'], $leave['policyMasterID']);
                $balanceLeave = $leaveBalanceData['balance'];

                $param["empName"] = $leave["Ename2"];
                $param["body"] = 'Leave application ' . $leave['documentCode'] . ' is approved.<br/>
                                  <table border="0px">
                                      <tr><td><strong>Name </td><td> : ' . $leave['Ename2'] . ' - ' . $leave['empCode'] . '</td></tr>
                                      <tr><td><strong>Start Date</td><td> : ' . date('Y-m-d', strtotime($leave['startDate'])) . '</td></tr>
                                      <tr><td><strong>End Date</td><td> : ' . date('Y-m-d', strtotime($leave['endDate'])) . '</td></tr>
                                      <tr><td><strong>Leave type </td><td> : ' . $leaveBalanceData['description'] . '</td></tr>
                                      <tr><td><strong>Leave balance </td><td> : ' . $balanceLeave . '</td></tr>
                                  </table>';

                $mailData = [
                    'approvalEmpID' => $leave['empID'],
                    'documentCode' => $leave['documentCode'],
                    'toEmail' => $leave["EEmail"],
                    'subject' => 'Employee Leave Approved',
                    'param' => $param,
                ];

                send_approvalEmail($mailData);

                $success_msg = $this->lang->line('hrms_payroll_approved_successfully');/*'Approved successfully'*/
                return array('s', $success_msg);
            } else {
                $this->db->trans_rollback();
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                return array('e', $common_failed);
            }
        }
    }

    function unloackUser()
    {

        $data['NoOfLoginAttempt'] = $this->input->post('chkdVal');
        $this->db->where('EIdNo', $this->input->post('empID'));
        $result = $this->db->update('srp_employeesdetails', $data);
        if ($result) {
            return array('s', 'Updated Successfully ');
        } else {
            return array('e', 'Update Failed ');
        }
    }

    function save_machineMapping()
    {
        $data['description'] = $this->input->post('description');
        if ($this->input->post('dbYN') == 1) {
            $data['dbhost'] = $this->input->post('dbhost');
            $data['dbname'] = $this->input->post('dbname');
            $data['dbpassword'] = $this->input->post('dbpassword');
            $data['dbuser'] = $this->input->post('dbuser');
            $data['dbtableName'] = $this->input->post('dbtableName');
        }
        $insert = $this->db->insert('srp_erp_machinemaster', $data);
        if ($insert) {
            return array('s', 'Successfully Inserted', $this->db->insert_id());
        } else {
            return array('e', 'Failed');
        }
    }

    function save_machineMapping_detail()
    {
        $data['columnName'] = $this->input->post('columnName');
        $data['machineMasterID'] = $this->input->post('machineID');

        $fetch_sortOrder = $this->db->query("select * from srp_erp_machinedetail where machineMasterID = {$data['machineMasterID']} order by sortOrder desc limit 1 ")->row_array();
        if (empty($fetch_sortOrder) || $fetch_sortOrder['sortOrder'] == null) {
            $data['sortOrder'] = 1;
        } else {
            $data['sortOrder'] = $fetch_sortOrder['sortOrder'] + 1;
        }

        $insert = $this->db->insert('srp_erp_machinedetail', $data);
        if ($insert) {
            return array('s', 'Successfully Inserted', $this->db->insert_id());
        } else {
            return array('e', 'Failed');
        }
    }

    /*function update_machineMappingcolumn_detail()
    {
        $value = $this->input->post('value');
        $masterID = $this->input->post('masterID');
        $detailID = $this->input->post('detailID');
        $this->db->update('srp_erp_machinedetail', array('machineTypeID' => $value), array('machineDetailID' => $detailID));
    }*/

    function update_machineMappingcolumn_detail()
    {
        $value = $this->input->post('value');
        $masterID = $this->input->post('masterID');
        $detailID = $this->input->post('detailID');
        $this->db->update('srp_erp_machinedetail', array('machineTypeID' => $value), array('machineDetailID' => $detailID));

        return array('s', 'Successfully Updated');
    }

    /** Over-time management for Salam-Air **/
    function save_OT_monthAddition()
    {
        $companyID = current_companyID();
        $monthDescription = $this->input->post('monthDescription');
        $currencyID = $this->input->post('currencyID');
        $additionDate = $this->input->post('dateDesc');
        $date_format_policy = date_format_policy();
        $additionDate = input_format_date($additionDate, $date_format_policy);

        $serialNo = $this->db->query("SELECT IF( ISNULL(MAX(serialNo)), 1 , ( MAX(serialNo) + 1) ) AS lastNumber
                                      FROM srp_erp_ot_monthlyadditionsmaster WHERE companyID={$companyID}")->row('lastNumber');

        $this->load->library('sequence');
        $docCode = $this->sequence->sequence_generator('OTA', $serialNo);


        $data = array(
            'monthlyAdditionsCode' => $docCode,
            'serialNo' => $serialNo,
            'documentID' => 'OTA',
            'description' => $monthDescription,
            'currencyID' => $currencyID,
            'dateMA' => $additionDate,
            'companyID' => current_companyID(),
            'companyCode' => current_companyCode(),
            'createdPCID' => current_pc(),
            'createdUserGroup' => current_user_group(),
            'createdUserID' => current_userID(),
            'createdUserName' => current_employee(),
            'createdDateTime' => current_date()
        );

        $this->db->trans_start();


        $this->db->insert('srp_erp_ot_monthlyadditionsmaster', $data);
        $monthlyAdditionsMasterID = $this->db->insert_id();


        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Employee Created Successfully.', $monthlyAdditionsMasterID);
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error In Employee Creating');
        }
    }

    public function edit_OT_monthAddition($editID = null)
    {
        $id = (isset($editID)) ? $editID : $this->input->post('editID');
        $convertFormat = convert_date_format_sql();
        $dateField = 'DATE_FORMAT(dateMA,\'' . $convertFormat . '\') AS dateMA ';
        $data = $this->db->select('monthlyAdditionsCode, description, confirmedYN, currencyID, isProcessed, ' . $dateField)
            ->from('srp_erp_ot_monthlyadditionsmaster')
            ->where('monthlyAdditionsMasterID', $id)
            ->get()->row_array();

        return $data;
    }

    function save_OT_employeeAsTemp()
    {
        $empDet = $this->input->post('temp_empHiddenID');
        $empCurrencyID = $this->input->post('temp_empCurrencyID');
        $empCurrencyCode = $this->input->post('temp_empCurrencyCode');
        $empCurrencyDPlace = $this->input->post('temp_empCurrencyDPlace');
        $masterID = $this->input->post('masterID');
        $rateInt = $this->input->post('temp_rateInt');
        $rateIntLay = $this->input->post('temp_rateIntLay');
        $rateLocalLay = $this->input->post('temp_rateLocalLay');
        $temp_slabID = $this->input->post('temp_slabID');
        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $pcID = current_pc();
        $userID = current_userID();
        $userName = current_employee();
        $userGroup = current_user_group();

        $data = array();
        $current_date = current_date();
        $com_currencyID = $this->common_data['company_data']['company_default_currencyID'];
        $com_currency = $this->common_data['company_data']['company_default_currency'];
        $com_currDPlace = $this->common_data['company_data']['company_default_decimal'];


        foreach ($empDet as $key => $emp) {
            $trCurrencyID = $empCurrencyID[$key];
            $data[$key]['empID'] = $emp;
            $data[$key]['monthlyAdditionsMasterID'] = $masterID;
            $data[$key]['intHRInputID'] = 1;
            $data[$key]['intHRhourlyRate'] = $rateInt[$key];
            $data[$key]['lclLyHRInputID'] = 2;
            $data[$key]['lclLyHRhourlyRate'] = $rateLocalLay[$key];
            $data[$key]['intLyInputID'] = 3;
            $data[$key]['intLyhourlyRate'] = $rateIntLay[$key];
            $data[$key]['totalblockInputID'] = 4;
            $data[$key]['slabMasterID'] = $temp_slabID[$key];
            $data[$key]['transactionCurrencyID'] = $trCurrencyID;
            $data[$key]['transactionCurrency'] = $empCurrencyCode[$key];
            $data[$key]['transactionExchangeRate'] = 1;
            $data[$key]['transactionCurrencyDecimalPlaces'] = $empCurrencyDPlace[$key];

            if ($key > 0) {
                if ($trCurrencyID == $empCurrencyID[$key - 1]) {
                    $com_exchangeRate = $data[$key - 1]['companyLocalExchangeRate'];
                } else {
                    $com_exchangeRateData = currency_conversionID($trCurrencyID, $com_currencyID);
                    $com_exchangeRate = $com_exchangeRateData['conversion'];
                }
            } else {
                $com_exchangeRateData = currency_conversionID($trCurrencyID, $com_currencyID);
                $com_exchangeRate = $com_exchangeRateData['conversion'];
            }

            $data[$key]['companyLocalCurrencyID'] = $com_currencyID;
            $data[$key]['companyLocalCurrency'] = $com_currency;
            $data[$key]['companyLocalExchangeRate'] = $com_exchangeRate;
            $data[$key]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;

            $data[$key]['companyID'] = $companyID;
            $data[$key]['companyCode'] = $companyCode;
            $data[$key]['createdPCID'] = $pcID;
            $data[$key]['createdUserID'] = $userID;
            $data[$key]['createdUserName'] = $userName;
            $data[$key]['createdUserGroup'] = $userGroup;
            $data[$key]['createdDateTime'] = $current_date;
        }

        //echo '<pre>'; print_r($data); echo '</pre>';die();
        $this->db->trans_start();
        $this->db->insert_batch('srp_erp_ot_monthlyadditiondetail', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Failed to Update');
        } else {
            $this->db->trans_commit();
            return array('s', '');
        }
    }

    function save_fixed_element_salaryDeclaration()
    {


        $this->load->library('sequence');
        $currency_code = explode('|', trim($this->input->post('currency_code')));
        $isPayrollCategory = trim($this->input->post('isPayrollCategory'));
        $isInitialDeclaration = trim($this->input->post('isInitialDeclaration'));
        $date_format_policy = date_format_policy();
        $invDueDate = $this->input->post('documentDate');
        $documentDate = input_format_date($invDueDate, $date_format_policy);

        $data['documentID'] = 'FED';
        $data['documentSystemCode'] = $this->sequence->sequence_generator("FED");
        $data['documentDate'] = trim($documentDate);
        $data['Description'] = trim($this->input->post('salary_description'));
        //$data['isPayrollCategory'] = $isPayrollCategory;
        //$data['isInitialDeclaration'] = $isInitialDeclaration;
        $data['transactionCurrencyID'] = trim($this->input->post('MasterCurrency'));
        $data['transactionCurrency'] = trim($currency_code['0']);
        $data['transactionER'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalER'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingER'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = current_date();
        $this->db->insert('srp_erp_ot_fixedelementdeclarationmaster', $data);
        if ($this->db->affected_rows() === FALSE) {
            $errMsg = 'Fixed Element Declaration Save Failed ' . $this->db->_error_message();
            return array('e', $errMsg);
        } else {
            $last_id = $this->db->insert_id();
            return array('s', 'Fixed Element Declaration Saved Successfully.', $last_id);
        }
    }

    function fetch_FixedElementDeclarationMaster($id)
    {
        $this->db->select("*");
        $this->db->from("srp_erp_ot_fixedelementdeclarationmaster");
        $this->db->where("fedeclarationMasterID", $id);
        $result = $this->db->get()->row_array();
        return $result;
    }

    function save_fixed_element_declaration()
    {
        $this->form_validation->set_rules('employee', 'Employee', 'trim|required');
        $this->form_validation->set_rules('amount[]', 'Amount', 'trim|required');
        $this->form_validation->set_rules('effectiveDate', 'Effective Date', 'trim|required|date');
        $this->form_validation->set_rules('cat[]', 'Category', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            return array('e', validation_errors());
        } else {
            $masterID = trim($this->input->post('feDeclarationMasterID'));

            $masterDetail = $this->fetch_FixedElementDeclarationMaster($masterID);


            $companyID = $this->common_data['company_data']['company_id'];
            $companyCode = $this->common_data['company_data']['company_code'];
            $com_currency = $this->common_data['company_data']['company_default_currency'];
            $com_currencyDPlace = $this->common_data['company_data']['company_default_decimal'];
            $com_repCurrency = $this->common_data['company_data']['company_reporting_currency'];
            $com_repCurDPlace = $this->common_data['company_data']['company_reporting_decimal'];
            $createdPCID = $this->common_data['current_pc'];
            $createdUserID = $this->common_data['current_userID'];
            $createdUserName = $this->common_data['current_user'];
            $createdUserGroup = $this->common_data['user_group'];
            $createdDateTime = current_date();

            $empID = $this->input->post('employee');
            $amount = $this->input->post('amount');
            $cat = $this->input->post('cat');
            $effDate = $this->input->post('effectiveDate');
            $narration = $this->input->post('narration');

            $date_format_policy = date_format_policy();
            $effDate = input_format_date($effDate, $date_format_policy);


            $lastPayrollProcessed = lastPayrollProcessedForEmp($empID);

            $payDateFirst = date('Y-m-01', strtotime($effDate));
            if ($lastPayrollProcessed >= $payDateFirst) {
                return ['e', 'Pay date should be greater than [ ' . date('Y-F', strtotime($lastPayrollProcessed)) . ' ]'];
                exit;
            }

            $data = array();

            $i = 0;
            $this->db->trans_start();
            foreach ($cat as $key => $catVal) {

                $tr_amount = (!empty($amount[$i])) ? str_replace(',', '', $amount[$i]) : 0;
                $localCon = currency_conversion($masterDetail['transactionCurrency'], $com_currency, $tr_amount);
                $reportCon = currency_conversion($masterDetail['transactionCurrency'], $com_repCurrency, $tr_amount);
                $localAmount = ($localCon['conversion'] > 0) ? round(($tr_amount / $localCon['conversion']), $com_currencyDPlace) : round($tr_amount, $com_currencyDPlace);
                $reportAmount = ($reportCon['conversion'] > 0) ? round(($tr_amount / $reportCon['conversion']), $com_repCurDPlace) : round($tr_amount, $com_repCurDPlace);
                $dPlace = $masterDetail['transactionCurrencyDecimalPlaces'];

                $data['feDeclarationMasterID'] = $masterID;
                $data['employeeNo'] = $empID;
                $data['fixedElementID'] = $catVal;
                $data['effectiveDate'] = $effDate;
                $data['narration'] = $narration;


                $data['transactionCurrencyID'] = $masterDetail['transactionCurrencyID'];
                $data['transactionCurrency'] = $masterDetail['transactionCurrency'];
                $data['transactionER'] = $masterDetail['transactionER'];
                $data['transactionCurrencyDecimalPlaces'] = $dPlace;


                $data['companyLocalCurrencyID'] = $localCon['currencyID'];
                $data['companyLocalCurrency'] = $com_currency;
                $data['companyLocalER'] = $localCon['conversion'];
                $data['companyLocalCurrencyDecimalPlaces'] = $com_currencyDPlace;


                $data['companyReportingCurrencyID'] = $reportCon['currencyID'];
                $data['companyReportingCurrency'] = $com_repCurrency;
                $data['companyReportingER'] = $reportCon['conversion'];
                $data['companyReportingCurrencyDecimalPlaces'] = $com_repCurDPlace;


                $data['amount'] = $tr_amount;
                $data['transactionAmount'] = $tr_amount;
                $data['companyLocalAmount'] = $localAmount;
                $data['companyReportingAmount'] = $reportAmount;

                $data['companyID'] = $companyID;
                $data['companyCode'] = $companyCode;
                $data['createdPCID'] = $createdPCID;
                $data['createdUserID'] = $createdUserID;
                $data['createdUserName'] = $createdUserName;
                $data['createdUserGroup'] = $createdUserGroup;
                $data['createdDateTime'] = $createdDateTime;

                $this->db->insert('srp_erp_ot_fixedelementdeclarationdetails', $data);
                $i++;
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === true) {
                $this->db->trans_commit();
                return array('s', 'Insert successfully ', $masterID);
            } else {
                $this->db->trans_rollback();
                return array('s', 'Failed to insert record');
            }

        }

    }

    function load_fixedElement_declaration_drilldown_table()
    {
        $companyID = current_companyID();
        $masterID = $this->input->post('masterID');
        $employeeid = $this->input->post('employeeID');
        $convertFormat = convert_date_format_sql();

        $this->db->select('srp_erp_ot_fixedelementdeclarationdetails.feDeclarationDetailID,feDeclarationMasterID,employeeNo,srp_erp_ot_fixedelementdeclarationdetails.fixedElementID,
                           effectiveDate,srp_employeesdetails.ECode,srp_employeesdetails.Ename2,
                           srp_erp_ot_fixedelements.fixedElementDescription,transactionAmount, DATE_FORMAT(effectiveDate, \'' . $convertFormat . '\') AS effectiveDate2,
                           payDate, DATE_FORMAT(payDate,\'' . $convertFormat . '\') AS payDate2, narration');
        $this->db->where('feDeclarationMasterID', $masterID);
        $this->db->where('employeeNo', $employeeid);
        $this->db->from('srp_erp_ot_fixedelementdeclarationdetails');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_ot_fixedelementdeclarationdetails.employeeNo');
        $this->db->join('srp_erp_ot_fixedelements', 'srp_erp_ot_fixedelements.fixedElementID = srp_erp_ot_fixedelementdeclarationdetails.fixedElementID');
        return $this->db->get()->result_array();

    }

    function ConfirmFixedElementDeclaration()
    {

        $masterID = trim($this->input->post('masterID'));
        $masterDetail = $this->fetch_FixedElementDeclarationMaster($masterID);

        if ($masterDetail['approvedYN'] == 1) {
            return ['e', 'This document is already approved'];
            exit;
        } else if ($masterDetail['confirmedYN'] == 1) {
            return ['e', 'This document is already confirmed'];
            exit;
        }

        if ($masterDetail) {
            $this->load->library('approvals');
            $systemCode = $masterDetail['documentSystemCode'];
            $table = 'srp_erp_ot_fixedelementdeclarationmaster';
            $primaryColumn = 'fedeclarationMasterID';
            $documentName = 'Fixed Element Declaration';
            $approvals_status = $this->approvals->CreateApproval('FED', $masterID, $systemCode, $documentName, $table, $primaryColumn);

            if ($approvals_status == 1) {
                return ['s', 'Approvals created successfully'];
            }
            if ($approvals_status == 3) {
                return ['w', 'There are no users exist to perform \'Fixed Element Declaration\' approval for this company.'];
            } else {
                return ['e', 'Error in process'];
            }
        } else {
            return ['e', 'No Master Records Found'];
        }


    }


    function delete_fixed_element_declaration_master()
    {
        $masterID = trim($this->input->post('masterID'));
        $this->db->delete('srp_erp_ot_fixedelementdeclarationmaster', array('fedeclarationMasterID' => $masterID));
        $this->db->delete('srp_erp_ot_fixedelementdeclarationdetails', array('feDeclarationMasterID' => $masterID));
        return true;
    }


    function save_fixed_element_declaration_approval()
    {

        $this->db->trans_start();
        $this->load->library('approvals');
        $system_code = trim($this->input->post('salaryOrderID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('approval_status'));
        $comments = trim($this->input->post('comments'));

        $createdPCID = $this->common_data['current_pc'];
        $createdUserID = $this->common_data['current_userID'];
        $createdUserName = $this->common_data['current_user'];
        $createdUserGroup = $this->common_data['user_group'];
        $createdDateTime = current_date();

        $masterDetail = $this->fetch_FixedElementDeclarationMaster($system_code);

        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'FED');

        //die('$approvals_status:'.$approvals_status);
        if ($approvals_status == 1) {

            $this->db->select('*');
            $this->db->from('srp_erp_ot_fixedelementdeclarationdetails');
            $this->db->where('feDeclarationMasterID', $system_code);
            $details_arr = $this->db->get()->result_array();

            $declarationDet_arr = array();
            for ($i = 0; $i < count($details_arr); $i++) {
                $declarationDet_arr[$i]['fdMasterID'] = $system_code;
                $declarationDet_arr[$i]['fdDetailID'] = $details_arr[$i]['declarationDetailID'];
                $declarationDet_arr[$i]['employeeNo'] = $details_arr[$i]['employeeNo'];
                $declarationDet_arr[$i]['fixedElementID'] = $details_arr[$i]['fixedElementID'];
                $declarationDet_arr[$i]['transactionCurrencyID'] = $details_arr[$i]['transactionCurrencyID'];
                $declarationDet_arr[$i]['transactionCurrency'] = $details_arr[$i]['transactionCurrency'];
                $declarationDet_arr[$i]['transactionER'] = $details_arr[$i]['transactionER'];
                $declarationDet_arr[$i]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['transactionCurrencyDecimalPlaces'];
                $declarationDet_arr[$i]['transactionAmount'] = $details_arr[$i]['transactionAmount'];
                $declarationDet_arr[$i]['amount'] = $details_arr[$i]['amount'];
                //$declarationDet_arr[$i]['percentage'] = $details_arr[$i]['percentage'];
                $declarationDet_arr[$i]['effectiveDate'] = $details_arr[$i]['effectiveDate'];
                //$declarationDet_arr[$i]['payDate'] = $details_arr[$i]['payDate'];
                $declarationDet_arr[$i]['narration'] = $details_arr[$i]['narration'];
                //$declarationDet_arr[$i]['additionID'] = $details_arr[$i]['additionID'];
                $declarationDet_arr[$i]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                $declarationDet_arr[$i]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                $declarationDet_arr[$i]['companyLocalER'] = $details_arr[$i]['companyLocalER'];
                $declarationDet_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                $declarationDet_arr[$i]['companyLocalAmount'] = $details_arr[$i]['companyLocalAmount'];
                $declarationDet_arr[$i]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                $declarationDet_arr[$i]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                $declarationDet_arr[$i]['companyReportingER'] = $details_arr[$i]['companyReportingER'];
                $declarationDet_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                $declarationDet_arr[$i]['companyReportingAmount'] = $details_arr[$i]['companyReportingAmount'];
                $declarationDet_arr[$i]['confirmedYN'] = 1;
                $declarationDet_arr[$i]['confirmedByEmpID'] = $masterDetail['confirmedByEmpID'];
                $declarationDet_arr[$i]['confirmedByName'] = $masterDetail['confirmedByName'];
                $declarationDet_arr[$i]['confirmedDate'] = $masterDetail['confirmedDate'];
                $declarationDet_arr[$i]['companyID'] = $details_arr[$i]['companyID'];
                $declarationDet_arr[$i]['companyCode'] = $details_arr[$i]['companyCode'];
                $declarationDet_arr[$i]['createdUserGroup'] = $createdUserGroup;
                $declarationDet_arr[$i]['createdUserID'] = $createdPCID;
                $declarationDet_arr[$i]['createdDateTime'] = $createdDateTime;
                $declarationDet_arr[$i]['createdUserName'] = $createdUserName;
            }

            if (!empty($declarationDet_arr)) {
                $declarationDet_arr = array_values($declarationDet_arr);
                $this->db->insert_batch('srp_erp_ot_pay_fixedelementdeclration', $declarationDet_arr);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return ['e', 'Fixed Element Declaration Approval Process.'];
            } else {
                $this->db->trans_commit();
                return ['s', 'Fixed Element Declaration Approved Successfully.'];
            }


        } else if ($approvals_status == 2) {
            return ['s', 'Salary Declaration Approval : Level ' . $level_id . ' Successfully.'];
        } else if ($approvals_status == 3) {
            return ['s', '[ ' . $masterDetail['documentSystemCode'] . ' ] Approvals  Reject Process Successfully done.'];
        } else if ($approvals_status == 5) {
            return ['w', '[ ' . $masterDetail['documentSystemCode'] . ' ] Previous Level Approval Not Finished.'];
        } else {
            return ['e', 'Error in approvals Of  [ ' . $masterDetail['documentSystemCode'] . ' ] ', $approvals_status];
        }

    }

    function save_empMonthlyAdditionOT()
    {

        if (empty($this->input->post('empHiddenID'))) {
            return array('e', 'Please select at least one employee');
        } else {

            $this->form_validation->set_rules('empHiddenID[]', 'Employee', 'trim|required');

            if ($this->input->post('isConform') == 1) {
                //$this->form_validation->set_rules('amount[]', 'Amount/s', 'trim|required');
            }


            if ($this->form_validation->run() == FALSE) {
                return array('e', validation_errors());
            } else {
                $updateID = trim($this->input->post('updateID'));
                $isConfirmed = $this->input->post('isConfirm');

                $description = $this->input->post('monthDescription');
                $dateDesc = $this->input->post('dateDesc');
                $date_format_policy = date_format_policy();
                $dateDesc = input_format_date($dateDesc, $date_format_policy);
                $updateCode = $this->input->post('updateCode');
                $companyID = current_companyID();
                $companyCode = current_companyCode();
                $pcID = current_pc();
                $userID = current_userID();
                $userName = current_employee();
                $userGroup = current_user_group();
                $current_date = current_date();

                $editionDet = $this->edit_OT_monthAddition($updateID);
                if ($editionDet['confirmedYN'] == 1) {
                    return ['e', $this->input->post('updateCode') . ' is already confirmed, you can not change this.'];
                    exit;
                }

                $data_master = array(
                    'description' => $description,
                    'dateMA' => $dateDesc,
                    'modifiedPCID' => $pcID,
                    'modifiedUserID' => $userID,
                    'modifiedUserName' => $userName,
                    'modifiedDateTime' => $current_date
                );

                if ($isConfirmed == 1) {
                    $data_master['confirmedYN'] = 1;
                    $data_master['confirmedByEmpID'] = $userID;
                    $data_master['confirmedByName'] = $userName;
                    $data_master['confirmedDate'] = $current_date;
                }


                $this->db->trans_start();

                $this->db->where('monthlyAdditionsMasterID', $updateID)->where('companyID', $companyID)->update('srp_erp_ot_monthlyadditionsmaster', $data_master);
                $this->db->where('monthlyAdditionsMasterID', $updateID)->where('companyID', $companyID)->delete('srp_erp_ot_monthlyadditiondetail');

                $com_currency = $this->common_data['company_data']['company_default_currency'];
                $com_currDPlace = $this->common_data['company_data']['company_default_decimal'];
                $com_repCurrency = $this->common_data['company_data']['company_reporting_currency'];
                $com_repCurDPlace = $this->common_data['company_data']['company_reporting_decimal'];

                $empHiddenID = $this->input->post('empHiddenID');
                $empCurrencyDPlace = $this->input->post('empCurrencyDPlace');
                $empCurrencyCode = $this->input->post('empCurrencyCode');
                $h_intHRhourlyRate = $this->input->post('h_intHRhourlyRate');
                $m_intHRhourlyRate = $this->input->post('m_intHRhourlyRate');
                $_intHRhourlyRate = $this->input->post('_intHRhourlyRate');
                $h_lclLyHRhourlyRate = $this->input->post('h_lclLyHRhourlyRate');
                $m_lclLyHRhourlyRate = $this->input->post('m_lclLyHRhourlyRate');
                $_lclLyHRhourlyRate = $this->input->post('_lclLyHRhourlyRate');
                $h_intLyhourlyRate = $this->input->post('h_intLyhourlyRate');
                $m_intLyhourlyRate = $this->input->post('m_intLyhourlyRate');
                $_intLyhourlyRate = $this->input->post('_intLyhourlyRate');
                $h_totalblockHours = $this->input->post('h_totalblockHours');
                $m_totalblockHours = $this->input->post('m_totalblockHours');
                $amount_totalblockHours = $this->input->post('amount_totalblockHours');
                $_slabID = $this->input->post('_slabID');
                $amount = $this->input->post('amount');


                $data = array();
                foreach ($empHiddenID as $key => $empID) {
                    $tr_amount = (!empty($amount[$key])) ? str_replace(',', '', $amount[$key]) : 0;
                    $localCon = currency_conversion($empCurrencyCode[$key], $com_currency, $tr_amount);
                    $reportCon = currency_conversion($empCurrencyCode[$key], $com_repCurrency, $tr_amount);
                    $tot_InstructorMinutes = ($h_intHRhourlyRate[$key] * 60 + $m_intHRhourlyRate[$key]);
                    $tot_localLayOverMinutes = ($h_lclLyHRhourlyRate[$key] * 60 + $m_lclLyHRhourlyRate[$key]);
                    $tot_interNationalLayoverMinutes = ($h_intLyhourlyRate[$key] * 60 + $m_intLyhourlyRate[$key]);
                    $tot_blockHours = ($h_totalblockHours[$key] * 60 + $m_totalblockHours[$key]);
                    $intHRAmount = (($h_intHRhourlyRate[$key] * $_intHRhourlyRate[$key]) + ($m_intHRhourlyRate[$key] * ($_intHRhourlyRate[$key] / 60)));
                    $lclLYHRAmount = (($h_lclLyHRhourlyRate[$key] * $_lclLyHRhourlyRate[$key]) + ($m_lclLyHRhourlyRate[$key] * ($_lclLyHRhourlyRate[$key] / 60)));
                    $intLyAmount = (($h_intLyhourlyRate[$key] * $_intLyhourlyRate[$key]) + ($m_intLyhourlyRate[$key] * ($_intLyhourlyRate[$key] / 60)));
                    $dPlace = $empCurrencyDPlace[$key];

                    $data[$key]['empID'] = $empID;
                    $data[$key]['monthlyAdditionsMasterID'] = $updateID;
                    $data[$key]['intHRInputID'] = 1;
                    $data[$key]['intHRotHours'] = $tot_InstructorMinutes;
                    $data[$key]['intHRhourlyRate'] = $_intHRhourlyRate[$key];
                    $data[$key]['intHRAmount'] = round($intHRAmount, $dPlace);
                    $data[$key]['lclLyHRInputID'] = 2;
                    $data[$key]['lclLyHRotHours'] = $tot_localLayOverMinutes;
                    $data[$key]['lclLyHRhourlyRate'] = $_lclLyHRhourlyRate[$key];
                    $data[$key]['lclLYHRAmount'] = round($lclLYHRAmount, $dPlace);
                    $data[$key]['intLyInputID'] = 3;
                    $data[$key]['intLyotHours'] = $tot_interNationalLayoverMinutes;
                    $data[$key]['intLyhourlyRate'] = $_intLyhourlyRate[$key];
                    $data[$key]['intLyAmount'] = round($intLyAmount, $dPlace);
                    $data[$key]['totalblockInputID'] = 4;
                    $data[$key]['totalblockHours'] = $tot_blockHours;
                    $data[$key]['totalblockAmount'] = round(str_replace(',', '', $amount_totalblockHours[$key]), $dPlace);
                    $data[$key]['slabMasterID'] = $_slabID[$key];
                    $data[$key]['transactionCurrencyID'] = $localCon['trCurrencyID'];
                    $data[$key]['transactionCurrency'] = $empCurrencyCode[$key];
                    $data[$key]['transactionExchangeRate'] = 1;
                    $data[$key]['transactionCurrencyDecimalPlaces'] = $empCurrencyDPlace[$key];
                    $data[$key]['companyLocalCurrencyID'] = $localCon['currencyID'];
                    $data[$key]['companyLocalCurrency'] = $com_currency;
                    $data[$key]['companyLocalExchangeRate'] = $localCon['conversion'];
                    $data[$key]['companyLocalCurrencyDecimalPlaces'] = $com_currDPlace;
                    $data[$key]['companyReportingCurrencyID'] = $reportCon['currencyID'];
                    $data[$key]['companyReportingCurrency'] = $com_repCurrency;
                    $data[$key]['companyReportingExchangeRate'] = $reportCon['conversion'];
                    $data[$key]['companyReportingCurrencyDecimalPlaces'] = $com_repCurDPlace;
                    $data[$key]['companyID'] = $companyID;
                    $data[$key]['companyCode'] = $companyCode;
                    $data[$key]['createdPCID'] = $pcID;
                    $data[$key]['createdUserID'] = $userID;
                    $data[$key]['createdUserName'] = $userName;
                    $data[$key]['createdUserGroup'] = $userGroup;
                    $data[$key]['createdDateTime'] = $current_date;
                }


                $this->db->insert_batch('srp_erp_ot_monthlyadditiondetail', $data);
                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('s', 'Failed to Update [ ' . $updateCode . ' ] ');
                } else {
                    $this->db->trans_commit();
                    return array('s', '[ ' . $updateCode . ' ] Updated successfully');
                }
            }
        }

    }

    function removeAllEmp_OT()
    {
        $masterID = $this->input->post('masterID');
        $masterData = $this->Employee_model->edit_OT_monthAddition($masterID);
        if ($masterData['isProcessed'] == 1) {
            return ['e', 'This document is already processed you can not make changes on this.'];
        }

        if ($masterData['confirmedYN'] == 1) {
            return ['e', 'This document is already confirmed you can not make changes on this.'];
        }

        $this->db->trans_start();
        $this->db->where('monthlyAdditionsMasterID', $masterID)->delete('srp_erp_ot_monthlyadditiondetail');
        $this->db->trans_complete();

        if ($this->db->trans_status() == false) {
            $this->db->trans_rollback();
            return array('e', 'Error');
        } else {
            $this->db->trans_commit();
            return array('s', '');
        }
    }

    function remove_emp_OT()
    {
        $masterID = $this->input->post('masterID');
        $detailID = $this->input->post('detailID');
        $masterData = $this->Employee_model->edit_OT_monthAddition($masterID);
        if ($masterData['isProcessed'] == 1) {
            return ['e', 'This document is already processed you can not make changes on this.'];
        }

        if ($masterData['confirmedYN'] == 1) {
            return ['e', 'This document is already confirmed you can not make changes on this.'];
        }

        $this->db->trans_start();
        $this->db->where('monthlyAdditionsMasterID', $masterID)->where('monthlyAdditionDetailID', $detailID)->delete('srp_erp_ot_monthlyadditiondetail');
        $this->db->trans_complete();

        if ($this->db->trans_status() == false) {
            $this->db->trans_rollback();
            return array('e', 'Error');
        } else {
            $this->db->trans_commit();
            return array('s', '');
        }
    }

    function delete_fixedElement_declaration_detail()
    {
        $this->db->delete('srp_erp_ot_fixedelementdeclarationdetails', array('feDeclarationDetailID' => trim($this->input->post('detailID'))));
        $this->session->set_flashdata('s', 'Delete Successfully.');
        return true;
    }


    function update_userName()
    {
        $db2 = $this->load->database('db2', TRUE);
        $EIdNo = $this->input->post('EIdNo');
        $UserName = $this->input->post('UserName');
        $companyID = current_companyID();

        $result = $db2->query("SELECT Username FROM user WHERE empID != '{$EIdNo}' AND Username = '{$UserName}'")->row_array();
        if ($result) {
            return array('e', 'Employee User Name already exist');
        } else {
            $data_central = array(
                'Username' => $UserName,
            );
            $centralUpdate = $db2->where('empID', $EIdNo)->where('companyID', $companyID)->update('user', $data_central);
            if ($centralUpdate) {
                $datadb['UserName'] = $UserName;

                $this->db->where('EIdNo', trim($EIdNo));
                $update = $this->db->update('srp_employeesdetails', $datadb);
                if ($update) {
                    return array('s', 'User Name updated successfully');
                } else {
                    return array('e', 'User Name update failed');
                }
            }
        }
    }

    function fetch_family_details($empID, $isFromEmpMaster = '')
    {
        $this->db->select("*,srp_erp_family_details.name as name,r.relationship as relationshipDesc,c.Nationality as countryName,g.name as genderDesc,
                          i.description as insuranDesc");
        $this->db->from("srp_erp_family_details");
        $this->db->join("srp_erp_family_relationship r", "r.relationshipID=srp_erp_family_details.relationship", "left");
        $this->db->join("srp_nationality c", "c.NId = srp_erp_family_details.nationality", "left");
        $this->db->join("srp_erp_gender g", "g.genderID = srp_erp_family_details.gender", "left");
        $this->db->join("srp_erp_family_insurancecategory i", "i.insurancecategoryID = srp_erp_family_details.insuranceCategory", "left");
        $this->db->where("empID", $empID);
        if ($isFromEmpMaster !== '') {
            $this->db->where("approvedYN", $isFromEmpMaster);
        }
        $output = $this->db->get()->result_array();

        return $output;
    }

    function insert_familyDetails()
    {
        $isNeedApproval = getPolicyValues('EPD', 'All');
        $frmprofile = (!empty($this->input->post('frmprofile'))) ? $this->input->post('frmprofile') : 0;
        if ($isNeedApproval == 1 && $frmprofile == 1) {
            $data = array(
                "empID" => $this->input->post('employeeID'),
                "name" => $this->input->post('name'),
                "relationship" => $this->input->post('relationshipType'),
                "nationality" => $this->input->post('nationality'),
                "DOB" => format_date_mysql_datetime(trim($this->input->post('DOB'))),
                "gender" => $this->input->post('gender'),
                "createdUserID" => current_userID(),
                "createdPCid" => current_pc(),
                "timestamp" => current_date(),
                "approvedYN" => 0
            );
        } else {
            $data = array(
                "empID" => $this->input->post('employeeID'),
                "name" => $this->input->post('name'),
                "relationship" => $this->input->post('relationshipType'),
                "nationality" => $this->input->post('nationality'),
                "DOB" => format_date_mysql_datetime(trim($this->input->post('DOB'))),
                "gender" => $this->input->post('gender'),
                "createdUserID" => current_userID(),
                "createdPCid" => current_pc(),
                "timestamp" => current_date()
            );
        }

        $empID = $this->input->post('employeeID');
        $result = $this->db->insert('srp_erp_family_details', $data);
        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'Family detail added successfully', 'empID' => $empID));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Error, Insert Error, Please contact your system support team'));
        }
    }


    function xeditable_update($tableName, $pkColumn)
    {
        $column = $this->input->post('name');
        $value = $this->input->post('value');
        $pk = $this->input->post('pk');
        switch ($column) {
            case 'DOB_O':
            case 'dateAssumed_O':
            case 'endOfContract_O':
            case 'SLBSeniority_O':
            case 'WSISeniority_O':
            case 'passportExpireDate_O':
            case 'VisaexpireDate_O':
            case 'coverFrom_O':
                $value = format_date_mysql_datetime($value);
                break;
        }

        $table = $tableName;
        $data = array($column => $value);
        $this->db->where($pkColumn, $pk);
        $result = $this->db->update($table, $data);
        echo $this->db->last_query();
        return $result;
    }


    function delete_familydetail()
    {
        $this->db->delete('srp_erp_family_details', array('empfamilydetailsID' => trim($this->input->post('empfamilydetailsID'))));
        return array('s', 'Deleted Successfully');
    }

    function delete_family_attachment()
    {
        $this->db->delete('srp_erp_familydetailsattachments', array('attachmentID' => trim($this->input->post('attachmentID'))));
        return array('s', 'Deleted Successfully');
    }


    function save_leaveApproval()
    {
        $companyID = current_companyID();
        $current_userID = current_userID();

        $status = $this->input->post('status');
        $level = $this->input->post('level');
        $comments = $this->input->post('comments');
        $leaveMasterID = $this->input->post('hiddenLeaveID');

        $leave = $this->db->query("SELECT leaveMaster.*, empTB.Ename2, EEmail, ECode AS empCode, leaveMaster.leaveTypeID, isSickLeave, coveringEmpID
                                   FROM srp_erp_leavemaster AS leaveMaster
                                   JOIN srp_erp_leavetype AS leaveType ON leaveType.leaveTypeID=leaveMaster.leaveTypeID
                                   JOIN srp_employeesdetails AS empTB ON empID=empTB.EIdNo
                                   WHERE leaveMasterID={$leaveMasterID} AND leaveMaster.companyID={$companyID} AND Erp_companyID={$companyID}
                                   AND leaveType.companyID={$companyID}")->row_array();
        $empID = $leave['empID'];
        $coveringEmpID = $leave['coveringEmpID'];

        if ($status == 2) {
            /**** Document refer back process ****/

            $upData = [
                'currentLevelNo' => 0,
                'confirmedYN' => 2,
                'confirmedByEmpID' => null,
                'confirmedByName' => null,
                'confirmedDate' => null,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $current_userID,
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => current_date()
            ];

            $this->db->trans_start();

            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_leavemaster', $upData);


            $this->db->where('companyID', $companyID);
            $this->db->where('departmentID', 'LA');
            $this->db->where('documentSystemCode', $leaveMasterID);
            $this->db->delete('srp_erp_documentapproved');


            $rejectData = [
                'documentID' => 'LA',
                'systemID' => $leaveMasterID,
                'documentCode' => $leave['documentCode'],
                'comment' => $comments,
                'rejectedLevel' => $level,
                'rejectByEmpID' => current_userID(),
                'table_name' => 'srp_erp_leavemaster',
                'table_unique_field' => 'leaveMasterID',
                'companyID' => $companyID,
                'companyCode' => current_companyCode(),
                'createdPCID' => current_pc(),
                'createdUserID' => current_userID(),
                'createdUserName' => current_employee(),
                'createdDateTime' => current_date()
            ];

            $this->db->insert('srp_erp_approvalreject', $rejectData);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                $param["empName"] = $leave["Ename2"];
                $param["body"] = 'Leave application ' . $leave['documentCode'] . ' is refer backed';

                $mailData = [
                    'approvalEmpID' => $leave['empID'],
                    'documentCode' => $leave['documentCode'],
                    'toEmail' => $leave["EEmail"],
                    'subject' => 'Employee Leave Refer backed',
                    'param' => $param,
                ];

                send_approvalEmail($mailData);

                return array('s', 'Leave application refer backed successfully');

            } else {
                $this->db->trans_rollback();
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                return array('e', $common_failed);
            }
        }


        $setupData = getLeaveApprovalSetup();
        $approvalLevel = $setupData['approvalLevel'];
        $approvalEmp_arr = $setupData['approvalEmp'];
        $isManagerAvailableForNxtApproval = 0;
        $nextApprovalEmpID = null;
        $nextLevel = ($level + 1);

        /**** If the number of approval level is less than current approval than only this process will run ****/
        if ($nextLevel <= $approvalLevel) {

            $managers = $this->db->query("SELECT * FROM (
                                             SELECT repManager
                                             FROM srp_employeesdetails AS empTB
                                             LEFT JOIN (
                                                 SELECT empID, managerID AS repManager FROM srp_erp_employeemanagers
                                                 WHERE active = 1 AND empID={$empID} AND companyID={$companyID}
                                             ) AS repoManagerTB ON empTB.EIdNo = repoManagerTB.empID
                                             WHERE Erp_companyID = '{$companyID}' AND EIdNo={$empID}
                                         ) AS empData
                                         LEFT JOIN (
                                              SELECT managerID AS topManager, empID AS topEmpID
                                              FROM srp_erp_employeemanagers WHERE companyID={$companyID} AND active = 1
                                         ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID")->row_array();

            $approvalSetup = $setupData['approvalSetup'];
            $x = $nextLevel;

            /**** Validate is there a manager available for next approval level ****/
            while ($x <= $approvalLevel) {
                $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $x);
                $arr = array_map(function ($k) use ($approvalSetup) {
                    return $approvalSetup[$k];
                }, $keys);

                $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';
                if ($approvalType == 3) {
                    //$hrManagerID = (!empty($arr[0])) ? $arr[0]['empID'] : '';
                    $hrManagerID = (array_key_exists($x, $approvalEmp_arr)) ? $approvalEmp_arr[$x] : '';
                    $nextLevel = $x;
                    $nextApprovalEmpID = $hrManagerID;
                    $isManagerAvailableForNxtApproval = 1;
                    $x = $approvalLevel;

                } else {
                    $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                    if (!empty($managers[$managerType])) {
                        $nextLevel = $x;
                        $nextApprovalEmpID = $managers[$managerType];
                        $isManagerAvailableForNxtApproval = 1;
                        $x = $approvalLevel;
                    }

                }

                $x++;
            }

        }


        if ($isManagerAvailableForNxtApproval == 1) {
            $upData = [
                'currentLevelNo' => $nextLevel,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $current_userID,
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => current_date()
            ];

            $this->db->trans_start();

            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_leavemaster', $upData);

            $approvalData = [
                'approvedYN' => $status,
                'approvedEmpID' => current_userID(),
                'approvedComments' => $comments,
                'approvedDate' => current_date(),
                'approvedPC' => current_pc()
            ];

            $this->db->where('companyID', $companyID);
            $this->db->where('departmentID', 'LA');
            $this->db->where('documentSystemCode', $leaveMasterID);
            $this->db->where('approvalLevelID', $level);
            $this->db->update('srp_erp_documentapproved', $approvalData);

            $this->db->trans_complete();

            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();

                $leaveBalanceData = $this->employeeLeaveSummery($empID, $leave['leaveTypeID'], $leave['policyMasterID']);
                $balanceLeave = $leaveBalanceData['balance'];
                $balanceLeave = ($balanceLeave > 0) ? ($balanceLeave - $leave['days']) : 0;

                if (is_array($nextApprovalEmpID)) {
                    /**** If the approval type HR there may be more than one employee for next approval process ****/
                    $nextApprovalEmpID = implode(',', array_column($nextApprovalEmpID, 'empID'));
                }

                $nxtEmpData_arr = $this->db->query("SELECT EIdNo, Ename2, EEmail FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                                    AND EIdNo IN ({$nextApprovalEmpID})")->result_array();

                foreach ($nxtEmpData_arr as $nxtEmpData) {

                    $bodyData = 'Leave application ' . $leave['documentCode'] . ' is pending for your approval.<br/>
                                 <table border="0px">
                                    <tr><td><strong>Name </td><td> : ' . $leave['Ename2'] . ' - ' . $leave['empCode'] . '</td></tr>
                                    <tr><td><strong>Start Date</td><td> : ' . date('Y-m-d', strtotime($leave['startDate'])) . '</td></tr>
                                    <tr><td><strong>End Date</td><td> : ' . date('Y-m-d', strtotime($leave['endDate'])) . '</td></tr> ';

                    if ($coveringEmpID != $nxtEmpData["EIdNo"]) {
                        $bodyData .= '<tr><td><strong>Leave type </td><td> : ' . $leaveBalanceData['description'] . '</td></tr>
                                      <tr><td><strong>Leave balance </td><td> : ' . $balanceLeave . '</td></tr>';
                    }

                    $bodyData .= '</table>';

                    $param["empName"] = $nxtEmpData["Ename2"];
                    $param["body"] = $bodyData;

                    $mailData = [
                        'approvalEmpID' => $nxtEmpData["EIdNo"],
                        'documentCode' => $leave['documentCode'],
                        'toEmail' => $nxtEmpData["EEmail"],
                        'subject' => 'Leave Approval',
                        'param' => $param
                    ];


                    send_approvalEmail($mailData);
                }

                $success_msg = strtolower($this->lang->line('hrms_payroll_approved_successfully'));/*'Approved successfully'*/
                return array('s', 'Level ' . $level . ' is ' . $success_msg);

            } else {
                $this->db->trans_rollback();
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                return array('e', $common_failed);
            }

        } else {

            $data = array(
                'currentLevelNo' => $approvalLevel,
                'approvedYN' => 1,
                'approvedDate' => current_date(),
                'approvedbyEmpID' => $current_userID,
                'approvedbyEmpName' => $this->common_data['current_user'],
                'approvalComments' => $comments,
            );

            $this->db->trans_start();


            if ($leave["isSickLeave"] == 1) {
                $this->sickLeaveNoPay_calculation($leave);
            }


            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_leavemaster', $data);

            $approvalData = [
                'approvedYN' => $status,
                'approvedEmpID' => current_userID(),
                'approvedComments' => $comments,
                'approvedDate' => current_date(),
                'approvedPC' => current_pc()
            ];

            $this->db->where('companyID', $companyID);
            $this->db->where('departmentID', 'LA');
            $this->db->where('documentSystemCode', $leaveMasterID);
            $this->db->where('approvalLevelID', $level);
            $this->db->update('srp_erp_documentapproved', $approvalData);


            /**** Confirm leave accrual pending*/
            $accrualData = [
                'confirmedYN' => 1,
                'confirmedby' => current_userID(),
                'confirmedDate' => current_date()
            ];

            $this->db->where('companyID', $companyID);
            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('confirmedYN', 0);
            $this->db->update('srp_erp_leaveaccrualmaster', $accrualData);

            $this->db->trans_complete();

            if ($this->db->trans_status() == true) {

                $leaveBalanceData = $this->employeeLeaveSummery($empID, $leave['leaveTypeID'], $leave['policyMasterID']);
                $balanceLeave = $leaveBalanceData['balance'];

                $param["empName"] = $leave["Ename2"];
                $param["body"] = 'Leave application ' . $leave['documentCode'] . ' is approved.<br/>
                                  <table border="0px">
                                      <tr><td><strong>Name </td><td> : ' . $leave['Ename2'] . ' - ' . $leave['empCode'] . '</td></tr>
                                      <tr><td><strong>Start Date</td><td> : ' . date('Y-m-d', strtotime($leave['startDate'])) . '</td></tr>
                                      <tr><td><strong>End Date</td><td> : ' . date('Y-m-d', strtotime($leave['endDate'])) . '</td></tr>
                                      <tr><td><strong>Leave type </td><td> : ' . $leaveBalanceData['description'] . '</td></tr>
                                      <tr><td><strong>Leave balance </td><td> : ' . $balanceLeave . '</td></tr>
                                  </table>';

                $mailData = [
                    'approvalEmpID' => $leave['empID'],
                    'documentCode' => $leave['documentCode'],
                    'toEmail' => $leave["EEmail"],
                    'subject' => 'Employee Leave Approved',
                    'param' => $param,
                ];

                send_approvalEmail($mailData);

                $success_msg = $this->lang->line('hrms_payroll_approved_successfully');/*'Approved successfully'*/
                return array('s', $success_msg);
            } else {
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                return array('e', $common_failed);
            }
        }
    }

    function leave_cancellation_approval()
    {
        $companyID = current_companyID();
        $current_userID = current_userID();

        $status = $this->input->post('status');
        $level = $this->input->post('level');
        $comments = $this->input->post('comments');
        $leaveMasterID = $this->input->post('hiddenLeaveID');

        $leave = $this->db->query("SELECT leaveMaster.*, empTB.Ename2, EEmail, ECode AS empCode, leaveMaster.leaveTypeID, isSickLeave, coveringEmpID
                                   FROM srp_erp_leavemaster AS leaveMaster
                                   JOIN srp_erp_leavetype AS leaveType ON leaveType.leaveTypeID=leaveMaster.leaveTypeID
                                   JOIN srp_employeesdetails AS empTB ON empID=empTB.EIdNo
                                   WHERE leaveMasterID={$leaveMasterID} AND leaveMaster.companyID={$companyID} AND Erp_companyID={$companyID}
                                   AND leaveType.companyID={$companyID}")->row_array();
        $empID = $leave['empID'];
        $coveringEmpID = $leave['coveringEmpID'];

        if ($status == 2) {
            /**** Document refer back process ****/
            //die(json_encode(['e', 'Error']));
            $upData = [
                'requestForCancelYN' => 2,
                'cancelRequestedDate' => null,
                'cancelRequestComment' => null,
                'cancelRequestByEmpID' => null,
                'modifiedPCID' => current_pc(),
                'modifiedUserID' => $current_userID,
                'modifiedUserName' => current_employee(),
                'modifiedDateTime' => current_date()
            ];

            $this->db->trans_start();

            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_leavemaster', $upData);


            $this->db->where('companyID', $companyID);
            $this->db->where('departmentID', 'LA');
            $this->db->where('isCancel', 1);
            $this->db->where('documentSystemCode', $leaveMasterID);
            $this->db->delete('srp_erp_documentapproved');


            $rejectData = [
                'documentID' => 'LA',
                'systemID' => $leaveMasterID,
                'documentCode' => $leave['documentCode'],
                'comment' => $comments,
                'isFromCancel' => 1,
                'rejectedLevel' => $level,
                'rejectByEmpID' => current_userID(),
                'table_name' => 'srp_erp_leavemaster',
                'table_unique_field' => 'leaveMasterID',
                'companyID' => $companyID,
                'companyCode' => current_companyCode(),
                'createdPCID' => current_pc(),
                'createdUserID' => current_userID(),
                'createdUserName' => current_employee(),
                'createdDateTime' => current_date()
            ];

            $this->db->insert('srp_erp_approvalreject', $rejectData);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                $param["empName"] = $leave["Ename2"];
                $param["body"] = 'Leave cancellation ' . $leave['documentCode'] . ' is refer backed';

                $mailData = [
                    'approvalEmpID' => $leave['empID'],
                    'documentCode' => $leave['documentCode'],
                    'toEmail' => $leave["EEmail"],
                    'subject' => 'Employee Leave Refer backed',
                    'param' => $param,
                ];

                send_approvalEmail($mailData);

                return array('s', 'Leave cancellation refer backed successfully');

            } else {
                $this->db->trans_rollback();
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                return array('e', $common_failed);
            }
        }


        $setupData = getLeaveApprovalSetup();
        $approvalLevel = $setupData['approvalLevel'];
        $approvalEmp_arr = $setupData['approvalEmp'];
        $isManagerAvailableForNxtApproval = 0;
        $nextApprovalEmpID = null;
        $nextLevel = ($level + 1);

        /**** If the number of approval level is less than current approval than only this process will run ****/
        if ($nextLevel <= $approvalLevel) {

            $managers = $this->db->query("SELECT * FROM (
                                             SELECT repManager
                                             FROM srp_employeesdetails AS empTB
                                             LEFT JOIN (
                                                 SELECT empID, managerID AS repManager FROM srp_erp_employeemanagers
                                                 WHERE active = 1 AND empID={$empID} AND companyID={$companyID}
                                             ) AS repoManagerTB ON empTB.EIdNo = repoManagerTB.empID
                                             WHERE Erp_companyID = '{$companyID}' AND EIdNo={$empID}
                                         ) AS empData
                                         LEFT JOIN (
                                              SELECT managerID AS topManager, empID AS topEmpID
                                              FROM srp_erp_employeemanagers WHERE companyID={$companyID} AND active = 1
                                         ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID")->row_array();

            $approvalSetup = $setupData['approvalSetup'];
            $x = $nextLevel;

            /**** Validate is there a manager available for next approval level ****/
            while ($x <= $approvalLevel) {
                $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $x);
                $arr = array_map(function ($k) use ($approvalSetup) {
                    return $approvalSetup[$k];
                }, $keys);

                $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';
                if ($approvalType == 3) {
                    //$hrManagerID = (!empty($arr[0])) ? $arr[0]['empID'] : '';
                    $hrManagerID = (array_key_exists($x, $approvalEmp_arr)) ? $approvalEmp_arr[$x] : '';
                    $nextLevel = $x;
                    $nextApprovalEmpID = $hrManagerID;
                    $isManagerAvailableForNxtApproval = 1;
                    $x = $approvalLevel;

                } else {
                    $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                    if (!empty($managers[$managerType])) {
                        $nextLevel = $x;
                        $nextApprovalEmpID = $managers[$managerType];
                        $isManagerAvailableForNxtApproval = 1;
                        $x = $approvalLevel;
                    }

                }

                $x++;
            }

        }


        if ($isManagerAvailableForNxtApproval == 1) {
            $upData = [
                'currentLevelNo' => $nextLevel,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $current_userID,
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => current_date()
            ];

            $this->db->trans_start();

            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_leavemaster', $upData);

            $approvalData = [
                'approvedYN' => $status,
                'approvedEmpID' => current_userID(),
                'approvedComments' => $comments,
                'approvedDate' => current_date(),
                'approvedPC' => current_pc()
            ];

            $this->db->where('companyID', $companyID);
            $this->db->where('departmentID', 'LA');
            $this->db->where('documentSystemCode', $leaveMasterID);
            $this->db->where('approvalLevelID', $level);
            $this->db->update('srp_erp_documentapproved', $approvalData);

            $this->db->trans_complete();

            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();

                $leaveBalanceData = $this->employeeLeaveSummery($empID, $leave['leaveTypeID'], $leave['policyMasterID']);
                $balanceLeave = $leaveBalanceData['balance'];
                $balanceLeave = ($balanceLeave > 0) ? ($balanceLeave - $leave['days']) : 0;

                if (is_array($nextApprovalEmpID)) {
                    /**** If the approval type HR there may be more than one employee for next approval process ****/
                    $nextApprovalEmpID = implode(',', array_column($nextApprovalEmpID, 'empID'));
                }

                $nxtEmpData_arr = $this->db->query("SELECT EIdNo, Ename2, EEmail FROM srp_employeesdetails WHERE Erp_companyID={$companyID}
                                                    AND EIdNo IN ({$nextApprovalEmpID})")->result_array();

                foreach ($nxtEmpData_arr as $nxtEmpData) {

                    $bodyData = 'Leave cancellation ' . $leave['documentCode'] . ' is pending for your approval.<br/>
                                  <table border="0px">
                                      <tr><td><strong>Name </td><td> : ' . $leave['Ename2'] . ' - ' . $leave['empCode'] . '</td></tr>
                                      <tr><td><strong>Start Date</td><td> : ' . date('Y-m-d', strtotime($leave['startDate'])) . '</td></tr>
                                      <tr><td><strong>End Date</td><td> : ' . date('Y-m-d', strtotime($leave['endDate'])) . '</td></tr>';

                    if ($coveringEmpID != $nxtEmpData["EIdNo"]) {
                        $bodyData .= '<tr><td><strong>Leave type </td><td> : ' . $leaveBalanceData['description'] . '</td></tr>
                                      <tr><td><strong>Leave balance </td><td> : ' . $balanceLeave . '</td></tr>';
                    }

                    $bodyData .= '</table>';

                    $param["empName"] = $nxtEmpData["Ename2"];
                    $param["body"] = $bodyData;

                    $mailData = [
                        'approvalEmpID' => $nxtEmpData["EIdNo"],
                        'documentCode' => $leave['documentCode'],
                        'toEmail' => $nxtEmpData["EEmail"],
                        'subject' => 'Leave Cancellation Approval',
                        'param' => $param
                    ];


                    send_approvalEmail($mailData);
                }

                $success_msg = strtolower($this->lang->line('hrms_payroll_approved_successfully'));/*'Approved successfully'*/
                return array('s', 'Level ' . $level . ' is ' . $success_msg);

            } else {
                $this->db->trans_rollback();
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                return array('e', $common_failed);
            }

        } else {

            $data = array(
                'cancelledYN' => 1,
                'currentLevelNo' => $approvalLevel,
                'cancelledDate' => current_date(),
                'cancelledByEmpID' => $current_userID,
                'cancelledComment' => $comments,
            );

            $this->db->trans_start();


            if ($leave["isSickLeave"] == 1) {
                //$this->sickLeaveNoPay_calculation($leave);
            }


            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_leavemaster', $data);

            $approvalData = [
                'approvedYN' => $status,
                'approvedEmpID' => current_userID(),
                'approvedComments' => $comments,
                'approvedDate' => current_date(),
                'approvedPC' => current_pc()
            ];

            $this->db->where('isCancel', 1);
            $this->db->where('companyID', $companyID);
            $this->db->where('departmentID', 'LA');
            $this->db->where('documentSystemCode', $leaveMasterID);
            $this->db->where('approvalLevelID', $level);
            $this->db->update('srp_erp_documentapproved', $approvalData);


            /**** delete leave accruals that are created from calender holiday declaration*/
            $this->db->where('companyID', $companyID);
            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->delete('srp_erp_leaveaccrualmaster');

            $this->db->where('leaveMasterID', $leaveMasterID);
            $this->db->delete('srp_erp_leaveaccrualdetail');


            //if($leave['isCalenderDays'] == 0){
            /***** create leave accrual for leave cancellation  *****/
            $this->create_leave_accrual($leave);
            //}

            $this->db->trans_complete();

            if ($this->db->trans_status() == true) {

                $leaveBalanceData = $this->employeeLeaveSummery($empID, $leave['leaveTypeID'], $leave['policyMasterID']);
                $balanceLeave = $leaveBalanceData['balance'];

                $param["empName"] = $leave["Ename2"];
                $param["body"] = 'Leave application ' . $leave['documentCode'] . ' is cancelled.<br/>
                                  <table border="0px">
                                      <tr><td><strong>Name </td><td> : ' . $leave['Ename2'] . ' - ' . $leave['empCode'] . '</td></tr>
                                      <tr><td><strong>Start Date</td><td> : ' . date('Y-m-d', strtotime($leave['startDate'])) . '</td></tr>
                                      <tr><td><strong>End Date</td><td> : ' . date('Y-m-d', strtotime($leave['endDate'])) . '</td></tr>
                                      <tr><td><strong>Leave type </td><td> : ' . $leaveBalanceData['description'] . '</td></tr>
                                      <tr><td><strong>Leave balance </td><td> : ' . $balanceLeave . '</td></tr>
                                  </table>';

                $mailData = [
                    'approvalEmpID' => $leave['empID'],
                    'documentCode' => $leave['documentCode'],
                    'toEmail' => $leave["EEmail"],
                    'subject' => 'Employee Leave Cancelled',
                    'param' => $param,
                ];

                send_approvalEmail($mailData);

                $success_msg = $this->lang->line('hrms_payroll_approved_successfully');/*'Approved successfully'*/
                return array('s', $success_msg);
            } else {
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                return array('e', $common_failed);
            }
        }
    }

    function create_leave_accrual($leave)
    {
        $accDet = [];
        $leaveMasterID = $leave['leaveMasterID'];
        $daysEntitle = $leave['days'];
        $period = $leave['startDate'];
        $d = explode('-', $period);
        $description = 'Leave Accrual for leave cancellation ';
        $comment = $description . ' - ' . $leave['documentCode'];
        $leaveGroupID = $leave['leaveGroupID'];
        $policyMasterID = $leave['policyMasterID'];
        $this->load->library('sequence');
        $code = $this->sequence->sequence_generator('LAM');


        $accMaster = [
            'companyID' => current_companyID(),
            'leaveaccrualMasterCode' => $code,
            'documentID' => 'LAM',
            'cancelledLeaveMasterID' => $leaveMasterID,
            'description' => $comment,
            'year' => $d[0],
            'month' => $d[1],
            'leaveGroupID' => $leaveGroupID,
            'policyMasterID' => $policyMasterID,
            'createdUserGroup' => current_user_group(),
            'createDate' => current_date(),
            'createdpc' => current_pc(),
            'confirmedYN' => 1,
            'confirmedby' => current_userID(),
            'confirmedDate' => current_date(),
        ];


        $this->db->insert('srp_erp_leaveaccrualmaster', $accMaster);


        $accDet['leaveaccrualMasterID'] = $this->db->insert_id();
        $accDet['cancelledLeaveMasterID'] = $leaveMasterID;
        $accDet['empID'] = $leave['empID'];
        $accDet['comment'] = '';
        $accDet['leaveGroupID'] = $leaveGroupID;
        $accDet['leaveType'] = $leave['leaveTypeID'];
        $accDet['daysEntitled'] = $daysEntitle;
        $accDet['comment'] = $comment;
        $accDet['description'] = $description;
        $accDet['leaveMasterID'] = $leaveMasterID;
        $accDet['createDate'] = current_date();
        $accDet['createdUserGroup'] = current_user_group();
        $accDet['createdPCid'] = current_pc();

        $this->db->insert('srp_erp_leaveaccrualdetail', $accDet);

        return 1;
    }

    function sickLeaveNoPay_calculation($leave = [])
    {
        $companyID = current_companyID();
        $isNonSalaryProcess = getPolicyValues('NSP', 'All');
        $leaveTypeID = $leave["leaveTypeID"];
        $empID = $leave["empID"];

        $result = $this->db->query("SELECT salaryCategoryID, formulaString, isNonPayroll FROM srp_erp_sickleavesetup
                                    WHERE companyID='{$companyID}' AND leaveTypeID={$leaveTypeID}")->result_array();

        if (!empty($result)) {
            $detail = [];
            foreach ($result as $key => $row) {

                $isNonPayroll = $row['isNonPayroll'];
                $table = ($isNonPayroll != 'Y') ? 'srp_erp_pay_salarydeclartion' : 'srp_erp_non_pay_salarydeclartion';
                $formula = trim($row['formulaString']);
                $formulaBuilder = formulaBuilder_to_sql_simple_convertion($formula);
                $formulaDecodeFormula = $formulaBuilder['formulaDecode'];
                $select_str = $formulaBuilder['select_str2'];
                $whereInClause = $formulaBuilder['whereInClause'];

                $f_Data = $this->db->query("SELECT (round(({$formulaDecodeFormula }), dPlace) )AS transactionAmount, dPlace
                                             FROM (
                                                SELECT employeeNo, " . $select_str . ", transactionCurrencyDecimalPlaces AS dPlace
                                                FROM {$table} AS salDec
                                                JOIN srp_erp_pay_salarycategories AS salCat ON salCat.salaryCategoryID = salDec.salaryCategoryID
                                                WHERE salDec.companyID = {$companyID} AND employeeNo={$empID} AND salDec.salaryCategoryID
                                                IN (" . $whereInClause . ") AND salCat.companyID ={$companyID}
                                                GROUP BY employeeNo, salDec.salaryCategoryID
                                             ) calculationTB
                                             JOIN srp_employeesdetails AS emp ON emp.EIdNo = calculationTB.employeeNo
                                             WHERE EIdNo={$empID} AND Erp_companyID = {$companyID}
                                             GROUP BY employeeNo")->row_array();

                $_amount = (!empty($f_Data)) ? $f_Data['transactionAmount'] : 0;
                $dPlace = (!empty($f_Data)) ? $f_Data['dPlace'] : 0;
                $_amount = round(($_amount * $leave['workingDays']), $dPlace);
                if ($row['isNonPayroll'] == 'N') {
                    $detail['noPayAmount'] = $_amount;
                    $detail['salaryCategoryID'] = $row['salaryCategoryID'];
                } else {
                    $detail['noPaynonPayrollAmount'] = $_amount;
                    $detail['nonPayrollSalaryCategoryID'] = $row['salaryCategoryID'];
                }
            }

            if ($detail['noPayAmount'] != 0 || ($detail['noPaynonPayrollAmount'] != 0)) {
                $detail['leaveMasterID'] = $leave['leaveMasterID'];
                $detail['empID'] = $empID;
                $detail['attendanceDate'] = date('Y-m-d', strtotime($leave['endDate']));
                $detail['companyID'] = $companyID;
                $detail['companyCode'] = current_companyCode();

                $this->db->insert('srp_erp_pay_empattendancereview', $detail);
            }
        }
    }

    function save_salary_category()
    {
        $nopaySystemID = $this->input->post('nopaySystemID');
        $salaryCategoryID = $this->input->post('salaryCategoryID');
        $companyID = current_companyID();

        $this->db->select('nopaySystemID');
        $this->db->where('nopaySystemID', trim($nopaySystemID));
        $this->db->where('companyID', trim($companyID));
        $this->db->from('srp_erp_nopayformula');
        $nopayexsist = $this->db->get()->row_array();
        if (!empty($nopayexsist)) {
            return array('e', 'No pay Category already exist');
        } else {
            $data = array(
                'nopaySystemID' => $nopaySystemID,
                'salaryCategoryID' => $salaryCategoryID,
                'companyID' => $companyID,
                'companyCode' => current_companyCode(),
                'createdUserGroup' => $this->common_data['user_group'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdDateTime' => current_date(),
            );
            $result = $this->db->insert('srp_erp_nopayformula', $data);
            if ($result) {
                return array('s', 'Salary Category Successfully Added');
            }
        }
    }

    function edit_salary_category()
    {
        $noPaySystemID = $this->input->post('noPaySystemIDHidden');
        $salaryCategoryID = $this->input->post('salaryCategoryID');
        $companyID = current_companyID();

        $data = array(
            'salaryCategoryID' => $salaryCategoryID,
        );
        $this->db->where('nopaySystemID', trim($noPaySystemID));
        $this->db->where('companyID', trim($companyID));
        $result = $this->db->update('srp_erp_nopayformula', $data);
        if ($result) {
            return array('s', 'Salary Category Updated Successfully');
        }
    }

    function save_company_active()
    {
        $isActive = $this->input->post('chkdVal');
        $empID = $this->input->post('empID');
        $companyID = current_companyID();

        $noofuser = $this->db->query("select noOfUsers from srp_erp_company where company_id = $companyID ")->row_array();
        $noofactived = $this->db->query("select Count(EIdNo) AS EIdNo from srp_employeesdetails where isActive = 1 AND Erp_companyID = $companyID")->row_array();
        $noofactive = $noofactived['EIdNo'];
        $noofusers = $noofuser['noOfUsers'];
        if ($isActive == 1) {
            if ($noofactive < $noofusers || $noofusers == 0) {
                $data = array(
                    'isActive' => 1,
                );
                $this->db->where('EIdNo', trim($empID));
                $this->db->where('Erp_companyID', trim($companyID));
                $result = $this->db->update('srp_employeesdetails', $data);
                if ($result) {
                    return array('s', 'User Successfully activated', $isActive);
                }
            } else if ($noofactive >= $noofusers) {
                return array('w', 'Maximum user count exceeded ');
            }
        } else {
            $data = array(
                'isActive' => 0,
            );
            $this->db->where('EIdNo', trim($empID));
            $this->db->where('Erp_companyID', trim($companyID));
            $result = $this->db->update('srp_employeesdetails', $data);
            if ($result) {
                return array('s', 'User Successfully de activated', $isActive);
            }

        }


    }

    function save_user_change_password()
    {
        $isChangePassword = $this->input->post('chkdVal');
        $empID = $this->input->post('empID');
        $companyID = current_companyID();

        $data = array(
            'isChangePassword' => $isChangePassword,
        );
        $this->db->where('EIdNo', trim($empID));
        $this->db->where('Erp_companyID', trim($companyID));
        $result = $this->db->update('srp_employeesdetails', $data);
        if ($result) {
            return array('s', 'successfully Saved');
        }
    }

    function getemployeedetails($empID)
    {
        $companyID = current_companyID();
        $qry = "SELECT srp_employeesdetails.EIdNo, srp_employeesdetails.ECode, srp_employeesdetails.EmpSecondaryCode, DesDescription,
                IFNULL(srp_employeesdetails.Ename2, '') AS employee, srp_employeesdetails.leaveGroupID,srp_employeesdetails.DateAssumed,
                DepartmentDes as department, concat(manager.ECode,' | ',manager.Ename2) as manager
                FROM srp_employeesdetails
                INNER JOIN srp_designation on srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID
                INNER JOIN srp_erp_leavegroup on srp_employeesdetails.leaveGroupID=srp_erp_leavegroup.leaveGroupID
                LEFT JOIN srp_erp_segment  on srp_erp_segment.segmentID=srp_employeesdetails.segmentID
                LEFT JOIN  (
                     SELECT EmpID AS empID_Dep, DepartmentDes FROM srp_departmentmaster AS departTB
                     JOIN srp_empdepartments AS empDep ON empDep.DepartmentMasterID = departTB.DepartmentMasterID
                     WHERE EmpID=$empID AND departTB.Erp_companyID=$companyID AND empDep.Erp_companyID=$companyID AND empDep.isActive=1
                ) AS departTB ON departTB.empID_Dep=srp_employeesdetails.EIdNo
                LEFT JOIN `srp_erp_employeemanagers` on EIdNo=empID AND active=1
                LEFT JOIN srp_employeesdetails manager on managerID=manager.EIdNo
                WHERE srp_employeesdetails.Erp_companyID=$companyID  AND srp_employeesdetails.EIdNo =$empID ";
        $data = $this->db->query($qry)->row_array();

        return $data;
    }

    function deleteEmpAssignedShift()
    {
        $autoID = $this->input->post('autoID');

        $this->db->trans_start();
        $this->db->where('autoID', $autoID)->delete('srp_erp_pay_shiftemployees');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Records deleted successfully');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in deleting process');
        }
    }

    function saveGrade()
    {
        try {
            $gradeID = $this->input->post('gradeID');
            $datetime = date('Y-m-d');
            if (!$gradeID) {
                /** Insert */
                $data['gradeDescription'] = $this->input->post('gradeDescription');
                $data['companyID'] = current_companyID();
                $data['createdPCID'] = current_pc();
                $data['createdUserID'] = current_userID();
                $data['createdDateTime'] = $datetime;
                $data['createdUserName'] = current_user();
                $data['timestamp'] = $datetime;
                $result = $this->db->insert('srp_erp_employeegrade', $data);

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('error' => 1, 'message' => 'Error while updating');

                } else {
                    $this->db->trans_commit();
                    return array('error' => 0, 'message' => 'Successfully Employee Grade created');
                }

            } else {
                /** Update */
                $data['gradeDescription'] = $this->input->post('gradeDescription');
                $data['modifiedUserID'] = current_userID();
                $data['modifiedUserName'] = current_user();
                $data['modifiedDateTime'] = $datetime;
                $data['modifiedPCID'] = current_pc();
                $this->db->where('gradeID', $this->input->post('gradeID'));
                $result = $this->db->update('srp_erp_employeegrade', $data);

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('error' => 1, 'message' => 'Error while updating');

                } else {
                    $this->db->trans_commit();
                    return array('error' => 0, 'message' => 'Successfully Employee Grade updated');
                }
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return array('error' => 1, 'message' => 'Error while updating');
        }
    }

    function deleteGrade()
    {
        $output = $this->db->delete('srp_erp_employeegrade', array('gradeID' => trim($this->input->post('gradeID'))));
        if ($output) {
            return array('error' => 0, 'message' => 'Successfully Employee Grade deleted');
        } else {
            return array('error' => 1, 'message' => 'Error while updating');
        }
    }

    function deleteall_attendanceMaster()
    {
        $companyID=current_companyID();
        $fromDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');
        $floor = $this->input->post('floorID');
        $floorID = implode(", ", $floor);


       $delete= $this->db->where_in('floorID', $floor)->where('companyID', $companyID)->where("attendanceDate BETWEEN '{$fromDate}' AND '{$toDate}' ")->where('confirmedYN',0)->delete('srp_erp_pay_empattendancereview');


        if ($delete) {
            echo json_encode(array('s', ' Deleted Successfully'));
            exit;
        } else {
            echo json_encode(array('e',  'Failed.'));
            exit;
        }
    }
}

