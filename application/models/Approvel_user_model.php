<?php

class Approvel_user_model extends ERP_Model
{

    function save_approveluser()
    {
        if (!trim($this->input->post('approvalUserID'))) {
            $this->db->select('levelNo');
            $this->db->from('srp_erp_approvalusers');
            $this->db->where('levelNo', trim($this->input->post('levelno')));
            $this->db->where('documentid', trim($this->input->post('documentid')));
            $this->db->where('employeeID', trim($this->input->post('employeeid')));
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $order_detail = $this->db->get()->result_array();

            if (!empty($order_detail)) {
                $this->session->set_flashdata('w',
                    'Approvel User : ' . trim($this->input->post('documentid')) . ' ' . trim($this->input->post('employee')) . '  already Exists.');

                return array('status' => FALSE);
            }

            $levelno = $this->input->post('levelno');
            if ($levelno != 1) {
                $levelno += -1;
                $documentid = $this->input->post('documentid');
                $employeeid = $this->input->post('employeeid');
                $exist = $this->db->query("SELECT * FROM srp_erp_approvalusers WHERE levelNo = {$levelno} AND documentid = '{$documentid}' ")->row_array();
                if (empty($exist)) {
                    $this->session->set_flashdata('e',
                        'Approvel User : Level ' . $levelno . ' not available for ' . trim($this->input->post('documentid')));

                    return array('status' => FALSE);

                }
            }

        }
        $employee = explode('|', $this->input->post('employee'));
        $document = explode('|', $this->input->post('document'));
        $data['levelNo'] = $this->input->post('levelno');
        $data['documentID'] = $this->input->post('documentid');
        $data['groupID'] = $this->input->post('userGroupID');
        $data['document'] = $document[1];
        $data['employeeID'] = $this->input->post('employeeid');
        $data['employeeName'] = $employee[1];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        if (!$this->input->post('approvalUserID')) {
            $data['Status'] = 1;
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $result = $this->db->insert('srp_erp_approvalusers', $data);
            if ($result) {
                $this->session->set_flashdata('s', 'Records Added Successfully');

                return TRUE;
            }
        } else {
            $this->db->where('approvalUserID', $this->input->post('approvalUserID'));
            $result = $this->db->update('srp_erp_approvalusers', $data);
            if ($result) {
                $this->session->set_flashdata('s', 'Records Updated Successfully');

                return TRUE;
            }
        }
    }

    function edit_approveluser()
    {
        $this->db->select('*');
        $this->db->where('approvalUserID', $this->input->post('id'));

        return $this->db->get('srp_erp_approvalusers')->row_array();
    }

    function delete_approveluser()
    {
        $id = $this->input->post('id');
        $companyID = $this->common_data['company_data']['company_id'];
        $row = $this->db->query("SELECT levelNo,documentID FROM srp_erp_approvalusers where approvalUserID={$id} AND companyID={$companyID}")->row_array();
        if ($row['levelNo'] != 1) {
            $row['levelNo'] += 1;
            $documentid = $row['documentID'];

            $exist = $this->db->query("SELECT * FROM srp_erp_approvalusers WHERE levelNo = {$row['levelNo']} AND documentid = '{$documentid}' ")->row_array();
            if ($exist) {
                $this->session->set_flashdata('s',
                    'Unable to delete . Please delete Level No ' . $row['levelNo'] . ' - ' . $documentid . ' and continue');

                return TRUE;

            }
        }

        $this->db->where('approvalUserID', $this->input->post('id'));
        $result = $this->db->delete('srp_erp_approvalusers');
        if ($result) {
            $this->session->set_flashdata('s', 'Records Deleted Successfully');

            return TRUE;
        }
    }

    function fetch_emploee_using_group()
    {
        $this->db->select("EIdNo,ECode,Ename1,Ename2,Ename3,Ename4");
        $this->db->from('srp_erp_employeenavigation');
        $this->db->where('userGroupID', $this->input->post('id'));
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_employeenavigation.empID');
        $this->db->where('srp_employeesdetails.isDischarged', 0);

        return $this->db->get()->result_array();
    }

    function fetch_approval_user_modal()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        /************************************************************************************************
         * No need to set a different logic to get leave approvals
         * So changed th case LA => LA1
         * by Nasik 2017-10-11
         ************************************************************************************************/
        switch ($this->input->post('documentID')) {

            case 'LA';
                /*$convertFormat = convert_date_format_sql();
                $docSystemCode = $this->input->post('documentSystemCode');
                $data = $this->db->query("SELECT approvedbyEmpName,approvedYN,DATE_FORMAT(approvedDate,\"" . $convertFormat . "\") AS approvedDate,approvedbyEmpID,
                                          approvalComments,empID,documentCode,DATE_FORMAT(entryDate,\"" . $convertFormat . "\") AS entryDate,
                                          DATE_FORMAT(confirmedDate,\"" . $convertFormat . "\") AS confirmedDate,confirmedByName,currentLevelNo
                                          FROM `srp_erp_leavemaster` WHERE `leaveMasterID` ={$docSystemCode} ")->result_array();

                $data_arr['document_code'] = $data[0]['documentCode'];
                $data_arr['document_date'] = $data[0]['entryDate'];
                $data_arr['confirmed_date'] = $data[0]['confirmedDate'];
                $data_arr['conformed_by'] = $data[0]['confirmedByName'];

                $data_arr['approved'] = $data;*/
                /*$data_arr['approved'][0]['approvalLevelID'] = $data['currentLevelNo'];
                $data_arr['approved'][0]['Ename2'] = $data['approvedbyEmpName'];
                $data_arr['approved'][0]['approvedYN'] = $data['approvedYN'];
                $data_arr['approved'][0]['approveDate'] = $data['approvedDate'];
                $data_arr['approved'][0]['approvedComments'] = $data['approvalComments'];*/

                $companyID = current_companyID();
                $convertFormat = convert_date_format_sql();
                $documentID = $this->input->post('documentID');
                $documentSystemCode = $this->input->post('documentSystemCode');
                $data_arr = array();

                $requestForCancelYN = $this->db->query("SELECT requestForCancelYN FROM srp_erp_leavemaster t1 WHERE leaveMasterID = {$documentSystemCode}")->row('requestForCancelYN');
                $requestForCancelYNStr = ($requestForCancelYN == 1)? 'AND isCancel = 1': '';
                $data = $this->db->query("SELECT app_emp.EIdNo,app_emp.ECode,app_emp.Ename2,approvalLevelID,approvedYN,approvedDate,approvedComments, documentCode,
                                  DATE_FORMAT(documentDate,\"" . $convertFormat . "\") AS documentDate,docConfirmedByEmpID,
                                  DATE_FORMAT(docConfirmedDate,\"" . $convertFormat . "\") AS docConfirmedDate,
                                  DATE_FORMAT(approvedDate,\"" . $convertFormat . "\") AS approveDate 
                                  FROM srp_erp_documentapproved app_tb
                                  JOIN srp_employeesdetails app_emp ON app_emp.EIdNo = app_tb.approvedEmpID
                                  WHERE documentID = '{$documentID}' AND documentSystemCode = '{$documentSystemCode}' AND companyID = {$companyID} 
                                  {$requestForCancelYNStr} ")->result_array();
                //echo $this->db->last_query();

                $data_arr['approved'] = $data;
                $data_arr['document_code'] = $data_arr['approved'][0]['documentCode'];
                $data_arr['document_date'] = $data_arr['approved'][0]['documentDate'];
                $data_arr['confirmed_date'] = $data_arr['approved'][0]['docConfirmedDate'];
                $emp = fetch_employeeNo($data_arr['approved'][0]['docConfirmedByEmpID']);
                $data_arr['conformed_by'] = $emp['Ename2'];

                return $data_arr;
                break;
            default:
                $convertFormat = convert_date_format_sql();
                $data_arr = array();
                $this->db->select("app_emp.EIdNo,app_emp.ECode,app_emp.Ename2,approvalLevelID,approvedYN,approvedDate,approvedComments, documentCode ,
                                  DATE_FORMAT(documentDate,\"" . $convertFormat . "\") AS documentDate,docConfirmedByEmpID,
                                  DATE_FORMAT(docConfirmedDate,\"" . $convertFormat . "\") AS docConfirmedDate,
                                  DATE_FORMAT(approvedDate,\"" . $convertFormat . "\") AS approveDate");
                $this->db->from('srp_erp_documentapproved');
                $this->db->where('documentID', $this->input->post('documentID'));
                $this->db->where('documentSystemCode', $this->input->post('documentSystemCode'));
                $this->db->join('srp_employeesdetails app_emp', 'app_emp.EIdNo = srp_erp_documentapproved.approvedEmpID');
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $data_arr['approved'] = $this->db->get()->result_array();
                $data_arr['document_code'] = $data_arr['approved'][0]['documentCode'];
                $data_arr['document_date'] = $data_arr['approved'][0]['documentDate'];
                $data_arr['confirmed_date'] = $data_arr['approved'][0]['docConfirmedDate'];
                $emp = fetch_employeeNo($data_arr['approved'][0]['docConfirmedByEmpID']);
                //$data_arr['conformed_by']   = $emp['ECode'].' - '.$emp['Ename2'];
                $data_arr['conformed_by'] = $emp['Ename2'];

                return $data_arr;
        }
    }


    function fetch_all_approval_users_modal()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $documentID = $this->input->post('documentID');
        $systemID = $this->input->post('documentSystemCode');

        switch ($documentID) {

            case 'LA';
                $convertFormat = convert_date_format_sql();


                $this->db->select('requestForCancelYN, coveringEmpID');
                $this->db->from('srp_erp_leavemaster');
                $this->db->where('leaveMasterID', $systemID);
                $this->db->where('companyID', $companyID);
                $masterData = $this->db->get()->row_array();

                $coveringEmpID = $masterData['coveringEmpID'];
                $requestForCancelYN = $masterData['requestForCancelYN'];

                $this->db->select("approvalLevelID,approvedYN,approvedDate,approvedComments,documentCode,docConfirmedByEmpID,
                                  DATE_FORMAT(documentDate,\"" . $convertFormat . "\") AS documentDate, '' AS Ename2,
                                  DATE_FORMAT(docConfirmedDate,\"" . $convertFormat . "\") AS docConfirmedDate,
                                  DATE_FORMAT(approvedDate,\"" . $convertFormat . "\") AS approveDate");
                $this->db->from('srp_erp_documentapproved');
                $this->db->where('srp_erp_documentapproved.documentID', $documentID);
                $this->db->where('documentSystemCode', $systemID);
                $this->db->where('srp_erp_documentapproved.companyID', $companyID);
                if($requestForCancelYN == 1){
                    $this->db->where('isCancel', 1);
                }
                $approved = $this->db->get()->result_array();


                $setupData = getLeaveApprovalSetup('Y');
                $approvalSetup = $setupData['approvalSetup'];
                $approvalEmp_arr = $setupData['approvalEmp'];
                $managers = $this->db->query("SELECT * FROM (
                                                 SELECT repManager, repManagerName, currentLevelNo
                                                 FROM srp_erp_leavemaster AS empTB
                                                 LEFT JOIN (
                                                     SELECT empID, managerID AS repManager, Ename2 AS repManagerName  FROM srp_erp_employeemanagers AS t1
                                                     JOIN srp_employeesdetails AS t2 ON t1.managerID=t2.EIdNo AND Erp_companyID={$companyID}
                                                     WHERE active = 1 AND companyID={$companyID}
                                                 ) AS repoManagerTB ON empTB.empID = repoManagerTB.empID
                                                 WHERE companyID = '{$companyID}' AND leaveMasterID={$systemID}
                                             ) AS empData
                                             LEFT JOIN (
                                                  SELECT managerID AS topManager, Ename2 AS topManagerName, empID AS topEmpID
                                                  FROM srp_erp_employeemanagers AS t1
                                                  JOIN srp_employeesdetails AS t2 ON t1.managerID=t2.EIdNo AND Erp_companyID={$companyID}
                                                  WHERE companyID={$companyID} AND active = 1
                                             ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID")->row_array();

                $approvalData = []; $k = 0;
                foreach($approved as $key=>$row){
                    $thisLevel = $row['approvalLevelID'];

                    $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $thisLevel);
                    $arr = array_map(function ($k) use ($approvalSetup) {
                        return $approvalSetup[$k];
                    }, $keys);

                    $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';

                    if($approvalType == 3){
                        $hrManagerID = (array_key_exists($thisLevel, $approvalEmp_arr)) ? $approvalEmp_arr[$thisLevel] : [];
                        $hrManagerID = array_column($hrManagerID, 'empID');

                        if(!empty($hrManagerID)){
                            foreach($hrManagerID as $hrManagerRow){
                                $hrEmpData = fetch_employeeNo($hrManagerRow);
                                $approved[$key]['Ename2'] = $hrEmpData['Ename2'];
                                $approvalData[] = $approved[$key];
                            }
                        }
                        else{
                            $approvalData[] = $approved[$key];
                        }
                    }
                    else if($approvalType == 4){
                        /*echo $approvalType.' <br/> cover :';
                        echo $coveringEmpID.' <br/>';*/
                        if(!empty($coveringEmpID)){
                            $coveringEmpData = fetch_employeeNo($coveringEmpID);
                            $approved[$key]['Ename2'] = $coveringEmpData['Ename2'];
                            $approvalData[] = $approved[$key];
                        }
                        else{
                            $approvalData[] = $approved[$key];
                        }
                    }
                    else{
                        $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                        if( !empty($managers[$managerType]) ){
                            $approved[$key]['Ename2'] = $managers[$managerType.'Name'];
                        }
                        $approvalData[] = $approved[$key];

                    }
                }

                $data_arr['approved'] = $approvalData;
                $data_arr['document_code'] = $data_arr['approved'][0]['documentCode'];
                $data_arr['document_date'] = $data_arr['approved'][0]['documentDate'];
                $data_arr['confirmed_date'] = $data_arr['approved'][0]['docConfirmedDate'];
                $empData = fetch_employeeNo($data_arr['approved'][0]['docConfirmedByEmpID']);
                $data_arr['conformed_by'] = $empData['Ename2'];
                $data_arr['requestForCancelYN'] = $requestForCancelYN;

                return $data_arr;
                break;
            default:


                $convertFormat = convert_date_format_sql();
                $data_arr = array();
                $this->db->select("app_emp.EIdNo,app_emp.ECode,app_emp.Ename2,approvalLevelID,approvedYN,approvedDate,approvedComments,documentCode,docConfirmedByEmpID,
                                  DATE_FORMAT(documentDate,\"" . $convertFormat . "\") AS documentDate, DATE_FORMAT(docConfirmedDate,\"" . $convertFormat . "\") AS
                                  docConfirmedDate, DATE_FORMAT(approvedDate,\"" . $convertFormat . "\")
                                  AS approveDate");
                $this->db->from('srp_erp_documentapproved');
                $this->db->join('srp_erp_approvalusers ap', 'ap.levelNo = srp_erp_documentapproved.approvalLevelID');
                $this->db->join('srp_employeesdetails app_emp', 'app_emp.EIdNo = ap.employeeID');
                $this->db->where('srp_erp_documentapproved.documentID', $documentID);
                $this->db->where('ap.documentID', $documentID);
                $this->db->where('documentSystemCode', $systemID);
                $this->db->where('srp_erp_documentapproved.companyID', $companyID);
                $this->db->where('ap.companyID', $companyID);
                $data_arr['approved'] = $this->db->get()->result_array();
                if(!empty($data_arr['approved'])){
                    $data_arr['document_code'] = $data_arr['approved'][0]['documentCode'];
                    $data_arr['document_date'] = $data_arr['approved'][0]['documentDate'];
                    $data_arr['confirmed_date'] = $data_arr['approved'][0]['docConfirmedDate'];
                    $emp = fetch_employeeNo($data_arr['approved'][0]['docConfirmedByEmpID']);
                    //$data_arr['conformed_by']   = $emp['ECode'].' - '.$emp['Ename2'];
                    $data_arr['conformed_by'] = $emp['Ename2'];
                }
                return $data_arr;
        }
    }

    function fetch_reject_user_modal()
    {
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $data_arr = array();
        $documentSystemCode = $this->input->post('documentSystemCode');
        $documentID = $this->input->post('documentID');
        $isCancelled = null;

        if($documentID == 'LA'){
            $isCancelled = $this->db->query("SELECT requestForCancelYN FROM srp_erp_leavemaster WHERE companyID={$companyID}
                              AND leaveMasterID={$documentSystemCode}")->row('requestForCancelYN');
        }

        $this->db->select("app_emp.EIdNo,app_emp.ECode,app_emp.Ename2,documentCode,comment,rejectedLevel,rejectByEmpID,systemID,
                           DATE_FORMAT(srp_erp_approvalreject.createdDateTime,\"" . $convertFormat . "\") AS referbackDate");
        $this->db->from('srp_erp_approvalreject');
        $this->db->where('srp_erp_approvalreject.documentID', $documentID);
        $this->db->where('systemID', $documentSystemCode);
        $this->db->join('srp_employeesdetails app_emp', 'app_emp.EIdNo = srp_erp_approvalreject.rejectByEmpID');
        $this->db->where('srp_erp_approvalreject.companyID', $companyID);
        if($isCancelled == 1){
            $this->db->where('srp_erp_approvalreject.isFromCancel', 1);
        }
        if($this->input->post('is')){

        }
        $data_arr['rejected'] = $this->db->get()->result_array();
        if (!empty($data_arr['rejected'])) {
            $data_arr['document_code'] = $data_arr['rejected'][0]['documentCode'];
        }

        //$data_arr['referback_date'] = $data_arr['rejected'][0]['referbackDate'];
        return $data_arr;
    }

    function fetch_approval_referbackuser_user_modal()
    {
        $convertFormat = convert_date_format_sql();
        $data_arr = array();
        $this->db->select("app_emp.EIdNo,app_emp.ECode,app_emp.Ename2,documentCode,comment,rejectedLevel,rejectByEmpID,systemID,
                          DATE_FORMAT(srp_erp_approvalreject.createdDateTime,\"" . $convertFormat . "\") AS referbackDate");
        $this->db->from('srp_erp_approvalreject');
        $this->db->where('srp_erp_approvalreject.documentID', $this->input->post('documentID'));
        $this->db->where('systemID', $this->input->post('documentSystemCode'));
        $this->db->join('srp_employeesdetails app_emp', 'app_emp.EIdNo = srp_erp_approvalreject.rejectByEmpID');
        $this->db->where('srp_erp_approvalreject.companyID', $this->common_data['company_data']['company_id']);
        $data_arr['rejected'] = $this->db->get()->result_array();
        $data_arr['document_code'] = $data_arr['rejected'][0]['documentCode'];
        $data_arr['referback_date'] = $data_arr['rejected'][0]['referbackDate'];

        return $data_arr;
    }

    function fetch_approval_level()
    {
        $this->db->select("approvalLevel");
        $this->db->from('srp_erp_documentcodemaster');
        $this->db->where('documentID', $this->input->post('documentID'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data_arr = $this->db->get()->row_array();

        return $data_arr;
    }
}