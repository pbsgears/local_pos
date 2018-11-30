<?php

class Session_model extends CI_Model
{

    function Session_model()
    {
        parent:: __construct();
    }

    function authenticateLogin($login_data)
    {
        $this->db->select('EIdNo,NoOfLoginAttempt,isDischarged,isActive');
        $this->db->where("UserName", $login_data['userN']);
        $this->db->where("Password", $login_data['passW']);
        $result = $this->db->get("srp_employeesdetails")->row_array();
        /*echo $this->db->database.'<br/>';
        echo $this->db->last_query();
        var_dump($result);
        echo 'ccc';
        exit;*/
        if ($result['isDischarged'] == 0 && $result['isActive'] == 1) {

            if ($result['NoOfLoginAttempt'] == 4) {
                $data['stats'] = False;
                $data['type'] = "error";
                $data['message'] = "Your account has been blocked please contact support team";
                return $data;
            } else {
                if (!empty($result)) {
                    $data['NoOfLoginAttempt'] = 0;
                    $this->db->where('EIdNo', $result['EIdNo']);
                    $this->db->update('srp_employeesdetails', $data);
                    return array('stats' => True, 'data' => md5($result['EIdNo']));
                } else {
                    $this->db->select('EIdNo,NoOfLoginAttempt');
                    $this->db->where("UserName", $login_data['userN']);
                    $getusrName = $this->db->get("srp_employeesdetails")->row_array();
                    if (!empty($getusrName)) {
                        $noOfAttemps = $getusrName['NoOfLoginAttempt'] + 1;
                        if ($getusrName['NoOfLoginAttempt'] == 4) {
                            $data['stats'] = False;
                            $data['type'] = "error";
                            $data['message'] = "Your account has been blocked please contact support team";
                            return $data;
                        } else if ($getusrName['NoOfLoginAttempt'] == 2) {
                            $datas['NoOfLoginAttempt'] = $noOfAttemps;
                            $this->db->where('EIdNo', $getusrName['EIdNo']);
                            $updateAttempt = $this->db->update('srp_employeesdetails', $datas);
                            if ($updateAttempt) {
                                $data['stats'] = False;
                                $data['type'] = "error";
                                $data['message'] = "Invalid username or password. <br/><strong><i class='fa fa-exclamation-triangle'></i> You have one more attempt.<strong>";
                                return $data;
                            }
                        } else {
                            $datas['NoOfLoginAttempt'] = $noOfAttemps;
                            $this->db->where('EIdNo', $getusrName['EIdNo']);
                            $updateAttempt = $this->db->update('srp_employeesdetails', $datas);
                            if ($updateAttempt) {
                                $data['stats'] = False;
                                $data['type'] = "error";
                                $data['message'] = "Invalid username or password. Please  try again.";
                                return $data;
                            }
                        }
                    }else {
                        $data['stats'] = False;
                        $data['type'] = "error";
                        $data['message'] = " Wrong user name or password. Please  try again.";
                        return $data;
                    }
                }
            }
        }else if ($result['isActive'] == 0) {
            $data['stats'] = False;
            $data['type'] = "error";
            $data['message'] = "Your account is not activated";
            return $data;
        }  else {
            $data['stats'] = False;
            $data['type'] = "error";
            $data['message'] = "Access Denied";
            return $data;
        }

    }

    function createSession($employee_code)
    {
        if ($employee_code != 'logout') {
            $this->db->select('company_id,company_name,company_logo,UserName,Ename1,Ename2,Ename3,Ename4,serialNo,EIdNo,company_code ,branchID,SchMasterId,EmpImage, ECode,EmpShortCode,srp_employeesdetails.languageID as languageID');
            $this->db->where("md5(EIdNo)", $employee_code);
            $this->db->where("srp_erp_company.confirmedYN", 1);
            $this->db->from('srp_employeesdetails');
            $this->db->join('srp_erp_company', 'srp_erp_company.company_id = srp_employeesdetails.Erp_companyID', 'left');
            $user_master_data = $this->db->get()->row_array();
            /*echo $this->db->database.'<br/>';
            echo md5(1103).'<br/>';

            echo $this->db->last_query().'<hr/>';

            print_r($user_master_data);

            exit;*/
            if ($user_master_data) {
                $wareHouseID = $this->db->select('wareHouseID,')->from('srp_erp_warehouse_users')->where(
                    array('userID' => $user_master_data['EIdNo'], 'companyID' => $user_master_data['company_id'], 'isActive' => 1)
                )->get()->row('wareHouseID');


                $imagePath_arr = $this->db->select('imagePath,isLocalPath')->from('srp_erp_pay_imagepath')->get()->row_array();
                if ($imagePath_arr['isLocalPath'] == 1) {
                    $imagePath = base_url() . 'images/users/';
                } else { // FOR SRP ERP USERS
                    $imagePath = $imagePath_arr['imagePath'];
                }

                $session_data = array(
                    'empID' => $user_master_data['EIdNo'],
                    'empCode' => $user_master_data['ECode'],
                    'username' => $user_master_data['Ename2'],
                    'loginusername' => $user_master_data['UserName'],
                    'companyID' => $user_master_data['company_id'],
                    'EmpShortCode' => $user_master_data['EmpShortCode'],
                    'companyType' => 1,
                    'company_link_id' => $user_master_data['SchMasterId'],
                    'branchID' => $user_master_data['branchID'],
                    'usergroupID' => $user_master_data['branchID'],
                    'ware_houseID' => $wareHouseID,
                    'EmpImage' => $user_master_data['EmpImage'],
                    'imagePath' => $imagePath,
                    'company_code' => $user_master_data['company_code'],
                    'company_name' => $user_master_data['company_name'],
                    'company_logo' => $user_master_data['company_logo'],
                    'emplangid' => $user_master_data['languageID'],
                    'status' => TRUE
                );

                $this->session->set_userdata($session_data);
                $data['stats'] = TRUE;
                return $data;
            } else {
                $data['stats'] = FALSE;
                $data['type'] = "info";
                $data['message'] = "Current User From the System";
                return $data;
            }
        }
    }

    function fetch_company_detail($com, $bran)
    {
        //$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
        //if (!$company = $this->cache->get('000001_'.$com)){
        $this->db->select('*');
        $this->db->from('srp_erp_company');
        $this->db->where('company_id', $com);
        //$this->db->where('branch_link_id', $bran);
        $company = $this->db->get()->row_array();
        //$this->cache->save('000001_'.$com, $company, 300);
        //}
        return $company;
    }

    function fetch_companycontrolaccounts($companyID, $company_code)
    {

        $data = array();
        $this->load->library('sequence');
        $this->db->SELECT("controlAccountsAutoID,controlAccountType,srp_erp_chartofaccounts.GLAutoID");
        $this->db->where('srp_erp_chartofaccounts.companyID', $companyID);
        $this->db->where('srp_erp_companycontrolaccounts.companyID', $companyID);
        $this->db->where('controllAccountYN', 1);
        $this->db->FROM('srp_erp_chartofaccounts');
        $this->db->join('srp_erp_companycontrolaccounts', 'srp_erp_companycontrolaccounts.GLAutoID = srp_erp_chartofaccounts.GLAutoID');
        $control_account = $this->db->get()->result_array();

        foreach ($control_account as $row) {
            $data[$row['controlAccountType']] = $row['GLAutoID'];
        }

        return $data;
    }


    function fetch_company_policy($com)
    {//get company policy

        $Companypolicy = $this->db->query("SELECT
	srp_erp_companypolicymaster.companypolicymasterID,
	companyPolicyDescription,
	srp_erp_companypolicymaster.code,
	IFNULL(cp.documentID,'All') as documentID,

IF (
	cp.`value` IS NULL,
	srp_erp_companypolicymaster.defaultValue,
	cp.`value`
) AS policyvalue
FROM
srp_erp_companypolicymaster
LEFT JOIN
 (SELECT * FROM srp_erp_companypolicy WHERE srp_erp_companypolicy.companyID = " . $com . ") cp ON(cp.companypolicymasterID = srp_erp_companypolicymaster.companypolicymasterID);")->result_array();
        $data = array_group_by($Companypolicy, 'code', 'documentID');
        /* echo "<pre>";
         print_r($data);
         echo "</pre>";*/
        return $data;
    }

    function fetch_group_policy($com)
    {//get company policy

        $Companypolicy = $this->db->query("SELECT
	srp_erp_grouppolicymaster.groupPolicymasterID,
	groupPolicyDescription,
	srp_erp_groupPolicymaster.code,
	IFNULL(cp.documentID,'All') as documentID,

IF (
	cp.`value` IS NULL,
	srp_erp_grouppolicymaster.defaultValue,
	cp.`value`
) AS policyvalue
FROM
srp_erp_grouppolicymaster
LEFT JOIN
 (SELECT * FROM srp_erp_grouppolicy WHERE srp_erp_grouppolicy.groupID = " . $com . ") cp ON(cp.groupPolicymasterID = srp_erp_grouppolicymaster.groupPolicymasterID);")->result_array();
        $data = array_group_by($Companypolicy, 'code', 'documentID');
        /* echo "<pre>";
         print_r($data);
         echo "</pre>";*/
        return $data;
    }

    function fetch_group_detail($com, $bran)
    {
        //$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
        //if (!$company = $this->cache->get('000001_'.$com)){
        $this->db->select('*,description as company_name,companyGroupID as company_id,"" as company_code,group_address1 as company_address1,group_address2 as company_address2,group_city as company_city,group_country as company_country,group_logo as company_logo,groupCode as company_code');
        $this->db->from('srp_erp_companygroupmaster');
        $this->db->where('companyGroupID', $com);
        //$this->db->where('branch_link_id', $bran);
        $company = $this->db->get()->row_array();
        //$this->cache->save('000001_'.$com, $company, 300);
        //}
        return $company;
    }

    function authenticateLoginUserName($login_data)
    {

        $this->db->select('EIdNo,NoOfLoginAttempt,isDischarged,isActive');
        $this->db->where("UserName", $login_data['userN']);
        $result = $this->db->get("srp_employeesdetails")->row_array();
        /*echo $this->db->last_query();
        exit;*/
        if ($result['isDischarged'] == 0 && $result['isActive'] == 1) {
            if ($result['NoOfLoginAttempt'] == 4) {
                $data['stats'] = False;
                $data['type'] = "error";
                $data['message'] = "Your account has been blocked please contact support team";
                return $data;
            }elseif($result['NoOfLoginAttempt'] == 2){
                if(!empty($result)){
                    $datas['NoOfLoginAttempt'] = $result['NoOfLoginAttempt']+1;
                    $this->db->where('EIdNo', $result['EIdNo']);
                    $updateAttempt = $this->db->update('srp_employeesdetails', $datas);
                    if ($updateAttempt) {
                        $data['stats'] = False;
                        $data['type'] = "error";
                        $data['message'] = "Invalid username or password. <br/><strong><i class='fa fa-exclamation-triangle'></i> You have one more attempt.<strong>";
                        return $data;
                    }
                }else{
                    $data['stats'] = False;
                    $data['type'] = "error";
                    $data['message'] = " Wrong user name or password. Please  try again.";
                    return $data;
                }
            } else {
                if(!empty($result)){
                    $datas['NoOfLoginAttempt'] = $result['NoOfLoginAttempt']+1;
                    $this->db->where('EIdNo', $result['EIdNo']);
                    $updateAttempt = $this->db->update('srp_employeesdetails', $datas);
                    if ($updateAttempt) {
                        $data['stats'] = False;
                        $data['type'] = "error";
                        $data['message'] = "Invalid username or password. Please  try again.";
                        return $data;
                    }
                }else{
                    $data['stats'] = False;
                    $data['type'] = "error";
                    $data['message'] = " Wrong user name or password. Please  try again.";
                    return $data;
                }
            }
        }else if ($result['isActive'] == 0) {
            $data['stats'] = False;
            $data['type'] = "error";
            $data['message'] = "Your account is not activated";
            return $data;
        } else{
            $data['stats'] = False;
            $data['type'] = "error";
            $data['message'] = "Access Denied";
            return $data;
        }
    }

}