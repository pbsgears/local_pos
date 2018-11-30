<?php
require(APPPATH.'libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

class Rest_server extends REST_Controller {

    function __construct()
    {

        // Construct the parent class
        parent::__construct();
        $this->load->model('Auth_mobileUsers_Model');
        $this->load->library('jwt');


        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }


    public function index_options() {
        return $this->response(NULL, 200);
    }

    public function profile_get()
    {
        $devID                  = $this->get('device');
        $tokenKey               = $_SERVER['HTTP_TOKEN_KEY'];
        $output['id_token']     = $this->jwt->decode( $tokenKey, "id_token");
        $userID                 = $output['id_token']->id;
        $companyID              = $output['id_token']->Erp_companyID;

        $profile['designation'] =  $this->Auth_mobileUsers_Model->get_emp_designation($userID);
        $profile['name']        =  $output['id_token']->name;
        $profile['empDetails']  =  $this->Auth_mobileUsers_Model->get_emp_details($userID);
        $profile['approvals']      =  $this->Auth_mobileUsers_Model->count_approvals($userID,$companyID);
                                   $this->Auth_mobileUsers_Model->save_deviceInfo($userID,$devID);
            if ($profile)
            {
                $this->response($profile, REST_Controller::HTTP_OK);
            }
            else
            {
                $this->response([
                    'status' => FALSE,
                    'message' => 'No users were found'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
    }

    public function approvals_get()
    {
        //$devID = $this->get('device');
        $tokenKey                = $_SERVER['HTTP_TOKEN_KEY'];
        $output['id_token']      = $this->jwt->decode( $tokenKey, "id_token");
        $companyID               = $output['id_token']->Erp_companyID;
        $userID                  = $output['id_token']->id;
        $limit                   = $this->get('limitN')+8;

        $approval['appr'] =  $this->Auth_mobileUsers_Model->get_approvals($userID,$companyID,$limit);
        $approval['count'] =  $this->Auth_mobileUsers_Model->count_approvals($userID,$companyID);


        if ($approval)
        {
            $this->response($approval, REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'message' => 'No users were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }
    public function  Approvalcontent_get(){

        $documentCode   = $this->get('docid');
        $table          = $this->get('table');
        $feild          = $this->get('feild');
        $fvalue         = $this->get('fvalue');

        $approvalDoc['contents']= $this->Auth_mobileUsers_Model->get_approvalDoc_content($documentCode,$table,$feild,$fvalue);
        $approvalDoc['getID']=$this->Auth_mobileUsers_Model->  getApproval_docID($table,$feild,$fvalue);

        if ($approvalDoc)
        {
            $this->response($approvalDoc, REST_Controller::HTTP_OK);

        }
        else
        {
            $this->response([
                'status' => FALSE,
                'message' => 'No users were found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }


    }

    public function dashboard_get(){
        $tokenKey                = $_SERVER['HTTP_TOKEN_KEY'];
        $output['id_token']      = $this->jwt->decode( $tokenKey, "id_token");
        $companyID               = $output['id_token']->Erp_companyID;
        $userID                  = $output['id_token']->id;

        $data['widgets'] =  $this->Auth_mobileUsers_Model->getAssignedDashboard($userID,$companyID );
        $this->set_response($data,REST_Controller::HTTP_OK);
    }

    public function getWidDashboard_get(){
        $tokenKey                = $_SERVER['HTTP_TOKEN_KEY'];
        $output['id_token']      = $this->jwt->decode( $tokenKey, "id_token");
        $companyID               = $output['id_token']->Erp_companyID;
        $userID                  = $output['id_token']->id;
        $dbid                    = $this->get('dbid');

        $data['dashboard'] =  $this->Auth_mobileUsers_Model->getAssignedDashboardWidget($userID,$companyID,$dbid);
        $this->set_response($data,REST_Controller::HTTP_OK);
    }

    //************************** widgets****************************
    public function load_overall_performance_get(){
        $tokenKey                = $_SERVER['HTTP_TOKEN_KEY'];
        $output['id_token']      = $this->jwt->decode( $tokenKey, "id_token");
        $companyID               = $output['id_token']->Erp_companyID;

        $beginingDate = "";
        $endDate = "";
        $lastTwoYears = $this->Auth_mobileUsers_Model->get_last_two_financial_year($companyID);

        if (!empty($lastTwoYears)) {
            $beginingDate = $lastTwoYears[0]["beginingDate"];
            $endDate = $lastTwoYears[0]["endingDate"];

        }

        $month = get_month_list_from_date($beginingDate, $endDate, "Y-m", "1 month", 'M');
        $month_arr = array();
        $OP_array = array();
        $series = array();
        foreach ($month as $row){
            array_push($month_arr, $row);
        }

        $data["months"]= $month_arr;
        $data["months2"] = get_month_list_from_date($beginingDate, $endDate, "Y-m", "1 month", 'My');
        $data["totalRevenue"] = $this->Auth_mobileUsers_Model->getTotalRevenue($beginingDate, $endDate,$companyID);

        $data["netProfit"] = $this->Auth_mobileUsers_Model->getNetProfit($beginingDate, $endDate,$companyID);
        $op = $this->Auth_mobileUsers_Model->getOverallPerformance($beginingDate, $endDate, $month,$companyID);


        foreach($op as $key=>$row){

            $new_arr = array();
            foreach($row as $keyValue=>$val){
                array_push($new_arr , $val );
            }
            array_push($OP_array, $new_arr);

        }

        foreach($op as $rows){
            array_push($series,$rows["description"]);
        }

        $data["overallPerformance"] =$OP_array;
        $data["series"] = $series;

        $this->set_response($data,REST_Controller::HTTP_OK);

    }

  public function load_revenue_detail_analysis_get(){

      $tokenKey                = $_SERVER['HTTP_TOKEN_KEY'];
      $output['id_token']      = $this->jwt->decode( $tokenKey, "id_token");
      $companyID               = $output['id_token']->Erp_companyID;

      $beginingDate = "";
      $endDate = "";
      $lastTwoYears = $this->Auth_mobileUsers_Model->get_last_two_financial_year($companyID);

      if (!empty($lastTwoYears)) {
          $beginingDate = $lastTwoYears[0]["beginingDate"];
          $endDate = $lastTwoYears[0]["endingDate"];
      }
      $data["totalRevenue"] = $this->Auth_mobileUsers_Model->getTotalRevenue($beginingDate, $endDate,$companyID);
      $data["revenueDetailAnalysis"] = $this->Auth_mobileUsers_Model->getRevenueDetailAnalysis($beginingDate, $endDate,$companyID);
      $this->set_response($data,REST_Controller::HTTP_OK);
  }

    public function load_performance_summary_get(){

        $performance = array();
        $performance_amount =array();
        $tokenKey                = $_SERVER['HTTP_TOKEN_KEY'];
        $output['id_token']      = $this->jwt->decode( $tokenKey, "id_token");
        $companyID               = $output['id_token']->Erp_companyID;

        $beginingDate = "";
        $endDate = "";
        $lastTwoYears = $this->Auth_mobileUsers_Model->get_last_two_financial_year($companyID);

        if (!empty($lastTwoYears)) {
            $beginingDate = $lastTwoYears[0]["beginingDate"];
            $endDate = $lastTwoYears[0]["endingDate"];
        }
        $data = array();

        $per = $this->Auth_mobileUsers_Model->getPerformanceSummary($beginingDate, $endDate,$companyID);
        foreach($per as $row){
            array_push($performance,$row["description"]);
            array_push($performance_amount,$row["amount"]);
        }
        $data["per_labels"] =$performance;
        $data["performanceSummary"] = $performance_amount;
        $this->set_response($data,REST_Controller::HTTP_OK);
    }

    //************************** widgets****************************

   public function load_fast_moving_item_get(){
       $tokenKey                = $_SERVER['HTTP_TOKEN_KEY'];
       $output['id_token']      = $this->jwt->decode( $tokenKey, "id_token");
       $companyID               = $output['id_token']->Erp_companyID;

       $beginingDate = "";
       $endDate = "";
       $lastTwoYears = $this->Auth_mobileUsers_Model->get_last_two_financial_year($companyID);

       if (!empty($lastTwoYears)) {
           $beginingDate = $lastTwoYears[0]["beginingDate"];
           $endDate = $lastTwoYears[0]["endingDate"];
       }
       $data = array();
       $data['FMI'] =$this->Auth_mobileUsers_Model->get_fastMovingItem($beginingDate,$endDate,$companyID);
       $this->set_response($data,REST_Controller::HTTP_OK);
   }

   public function load_financial_position_get(){
       $tokenKey                = $_SERVER['HTTP_TOKEN_KEY'];
       $output['id_token']      = $this->jwt->decode( $tokenKey, "id_token");
       $companyID               = $output['id_token']->Erp_companyID;

       $lastTwoYears = $this->Auth_mobileUsers_Model->get_last_two_financial_year($companyID);

       if (!empty($lastTwoYears)) {
           $beginingDate = $lastTwoYears[0]["beginingDate"];
           $endDate = $lastTwoYears[0]["endingDate"];
       }
       $data = array();
       $data['BP'] =$this->Auth_mobileUsers_Model->get_bankPosition($companyID);
       $this->set_response($data,REST_Controller::HTTP_OK);

   }

   public function load_overdue_payable_receivable_get(){
       $tokenKey                = $_SERVER['HTTP_TOKEN_KEY'];
       $output['id_token']      = $this->jwt->decode( $tokenKey, "id_token");
       $companyID               = $output['id_token']->Erp_companyID;
       $data = array();
       $data['OD_payable'] =$this->Auth_mobileUsers_Model->get_overdue_payable($companyID);
       $data['OD_receivable'] =$this->Auth_mobileUsers_Model->fetch_overdue_receivable($companyID);
       $this->set_response($data,REST_Controller::HTTP_OK);
   }

   public function load_postdated_cheque_get(){
       $tokenKey                = $_SERVER['HTTP_TOKEN_KEY'];
       $output['id_token']      = $this->jwt->decode( $tokenKey, "id_token");
       $companyID               = $output['id_token']->Erp_companyID;
       $data = array();
       $data['cheque_given'] =$this->Auth_mobileUsers_Model->fetch_postdated_cheque_given($companyID);
       $data['cheque_rcd'] =$this->Auth_mobileUsers_Model->fetch_postdated_cheque_received($companyID);
       $this->set_response($data,REST_Controller::HTTP_OK);

   }

   public function load_Designation_head_count_get(){
       $tokenKey                = $_SERVER['HTTP_TOKEN_KEY'];
       $output['id_token']      = $this->jwt->decode( $tokenKey, "id_token");
       $companyID               = $output['id_token']->Erp_companyID;
       $data =array();
       $des_array = array();
       $desCount = array();

       $des_headCount =$this->Auth_mobileUsers_Model-> fetch_Designation_head_count($companyID);

       foreach($des_headCount as $drow){
           array_push($des_array,$drow['DesDescription']);
           array_push($desCount,$drow['designationCount']);
       }

       $data['DesDescription']= $des_array;
       $data['designationCount']= $desCount;
       $this->set_response($data,REST_Controller::HTTP_OK);
   }

    public function load_to_do_list_get(){
        $tokenKey                = $_SERVER['HTTP_TOKEN_KEY'];
        $output['id_token']      = $this->jwt->decode( $tokenKey, "id_token");
        $companyID               = $output['id_token']->Erp_companyID;
        $userID                  = $output['id_token']->id;

       $data['todolist']= $this->Auth_mobileUsers_Model-> getToDoList($companyID,$userID);
        $this->set_response($data,REST_Controller::HTTP_OK);
    }




    public function login_post()
    {
        $request_body = file_get_contents('php://input');
        $request = json_decode($request_body);

        $username = $request->PHP_AUTH_USER;
        $pwd=  MD5($request->PHP_AUTH_PW);

        $empid                  = $this->Auth_mobileUsers_Model->get_userID($username,$pwd);
        $token['id']            = $empid['EIdNo'];
        $token['Erp_companyID'] = $empid['Erp_companyID'];
        $token['ECode']         = $empid['ECode'];
        $token['company_code']  = $empid['company_code'];
        $token['name']          = $empid['Ename1'];
        $token['username']      = $username;
        $date                   = new DateTime();
        $token['iat']           = $date->getTimestamp();
        $token['exp']           = $date->getTimestamp() + 60*60*5;

        $this->load->library('jwt');
        $output['id_token']     = $this->jwt->encode($token, "id_token");
        $output['test']         = $_SERVER['HTTP_TOKEN_KEY'];
        $this->set_response($output, REST_Controller::HTTP_OK);

    }



    public function users_delete()
    {
        $id = (int) $this->get('id');// $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        // Validate the id.
        if ($id <= 0)
        {
            // Set the response and exit
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
        // $this->some_model->delete_something($id);
        $message = [
            'id' => $id,
            'message' => 'Deleted the resource'
        ];
        $this->set_response($message, REST_Controller::HTTP_NO_CONTENT); // NO_CONTENT (204) being the HTTP response code
    }

    public function complete_todo_put(){

        $request_body = file_get_contents('php://input');
        $request = json_decode($request_body);
        $id = $request->autoid;
        $data['todo_update']= $this->Auth_mobileUsers_Model-> update_todolist($id);
        $this->set_response($data,REST_Controller::HTTP_OK);
    }





}
