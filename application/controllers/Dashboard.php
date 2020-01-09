<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends ERP_Controller
{

    function __construct()
    {

        parent::__construct();
        if(empty(trim($this->common_data['status']))){
            header('Location: '.site_url('Login/logout'));
            exit;
        }else {
            $this->load->model('dashboard_model');
            $this->load->helpers('procurement');
            $this->load->helpers('grv');
            $this->load->helpers('loan_helper');
            $this->load->helper('template_paySheet');
            $this->load->helper('employee');
            $this->load->helper('pos');
            $this->load->helper('cookie');
            $this->load->helper('asset_management');
            //$this->load->library('approvals');

            /*$this->lang->load('form_validation', 'korean');
            $oops = $this->lang->line('form_validation_required');
            echo $oops;
            exit;*/
        }

    }

    public function index()
    {
        /* Dashboard */
        $this->load->model('Finance_dashboard_model');
        $result = $this->Finance_dashboard_model->getAssignedDashboard();
        $data["dashboardTab"] = $result["dashboard"];
        /* End Dashboard */

        $data['title'] = 'Welcome Dashboard';
        $data['main_content'] = 'system/system_dashboard';
        $data['extra'] = 'sidebar-mini';
        $this->load->view('include/template', $data);
    }

    function sample_page()
    {
        $data['title'] = 'Sample Page';
        $data['main_content'] = 'system/sample_page';
        $data['extra'] = NULL;
        $this->load->view('include/template', $data);
    }

    function validation_page()
    {
        $data['title'] = 'Validation Page';
        $data['main_content'] = 'system/validation_page';
        $data['extra'] = NULL;
        $this->load->view('include/template', $data);
    }

    public function fetchPage()
    {
        $page_url = trim($this->input->post("page_url"));
        /*if (!file_exists(APPPATH . '/views/' . $page_url . '.php')) {
            $this->load->view('404.php');
        } else {*/
        /*$array = $this->session->userdata('links');
        if (empty($array)) {
            $array = array();
        }*/
        $array_data['page_id'] = trim($this->input->post('page_id'));
        $array_data['page_url'] = $page_url;
        $array_data['page_name'] = trim($this->input->post('page_name'));
        $array_data['policy_id'] = trim($this->input->post('policy_id'));
        $array_data['data_arr'] = $this->input->post('data_arr');
        $array_data['master_page_url'] = $this->input->post('master_page_url');
        /*array_unshift($array, $array_data);
        unset($array[4]);
        array_values($array);
        $this->session->set_userdata('links', $array);*/
        $this->load->view($page_url, $array_data);
        /*}*/
    }

    function fetch_last_page()
    {
        $array = $this->session->userdata('links');
        if (empty($array[1])) {
            echo json_encode(array('page_url' => 'system/welcome_dashboard', 'page_id' => NULL, 'page_name' => 'Dashboard', 'policy_id' => NULL));
        } else {
            $data = array('page_url' => $array[1]['page_url'], 'page_id' => $array[1]['page_id'], 'page_name' => $array[1]['page_name'], 'policy_id' => $array[1]['policy_id']);
            unset($array[1]);
            array_values($array);
            $this->session->set_userdata('links', $array);
            echo json_encode($data);
        }
    }

    function fetch_notifications()
    {
        echo json_encode($this->dashboard_model->fetch_notifications());
    }

    function fetch_related_uom()
    {
        echo json_encode($this->dashboard_model->fetch_related_uom());
    }

    function fetch_related_uom_id()
    {
        echo json_encode($this->dashboard_model->fetch_related_uom_id());
    }

    function set_navbar_cookie()
    {
        $className = trim($this->input->post('className'));
        $tmp = mb_strpos($className, 'sidebar-collapse');
        if ($tmp != false) {
            delete_cookie("SIDE_BAR");
        } else {
            $cookie_arr['name'] = 'SIDE_BAR';
            $cookie_arr['value'] = 'sidebar-collapse';
            $cookie_arr['expire'] = (3600 * 12);
            $cookie_arr['domain'] = '';
            $cookie_arr['path'] = '/';
            $cookie_arr['secure'] = false;
            $this->input->set_cookie($cookie_arr);
        }
    }

    function fetch_finance_year_period()
    {
        echo json_encode($this->dashboard_model->fetch_finance_year_period());
    }

    function test_redis()
    {
        $this->load->library("redis");
        $redis = $this->redis->config();
        if($exist = $redis->EXISTS("test")){
            $get = $redis->HVALS("test");
            print_r($get);
        }else{
            $set = $redis->HMSET("test", array('asd', 'asdasd'));
        }
    }

    function convert_acoding_to_uom(){
        echo json_encode($this->dashboard_model->convert_acoding_to_uom());
    }

}
