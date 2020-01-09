<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Lib_log{

    private $_ci;
    private $_log_table_name;

    public $levels = array(
        E_ERROR             => 'Error',
        E_WARNING           => 'Warning',
        E_PARSE             => 'Parsing Error',
        E_NOTICE            => 'Notice',
        E_CORE_ERROR        => 'Core Error',
        E_CORE_WARNING      => 'Core Warning',
        E_COMPILE_ERROR     => 'Compile Error',
        E_COMPILE_WARNING   => 'Compile Warning',
        E_USER_ERROR        => 'User Error',
        E_USER_WARNING      => 'User Warning',
        E_USER_NOTICE       => 'User Notice',
        E_STRICT            => 'Runtime Notice',
        E_RECOVERABLE_ERROR => 'Catchable error',
        E_DEPRECATED        => 'Runtime Notice',
        E_USER_DEPRECATED   => 'User Warning'
    );

    public function __construct()
    {
        $this->_ci =& get_instance();
        $this->_ci->load->library('session');
        $this->_ci->load->library('user_agent');
        set_error_handler(array($this, 'error_handler'));
        set_exception_handler(array($this, 'exception_handler'));// Load database driver
        $this->_ci->load->database();// Load config file
        $this->_log_table_name = 'srp_erp_developer_log';
    }

    /**
     * PHP Error Handler
     *
     * @param   int
     * @param   string
     * @param   string
     * @param   int
     * @return  void
     */
    public function error_handler($severity, $message, $filepath, $line)
    {
        $data = array(
            'errno'         => $severity,
            'errtype'       => isset($this->levels[$severity]) ? $this->levels[$severity] : $severity,
            'errstr'        => $message,
            'errfile'       => $filepath,
            'errline'       => $line,
            'user_agent'    => $this->_ci->input->user_agent(),
            'ip_address'    => $this->_ci->input->ip_address(),
            'username'      => $this->_ci->session->userdata('e_empname'),
            'time'          => date('Y-m-d H:i:s')
        );

        $this->_ci->db->insert($this->_log_table_name, $data);
    }

    /**
     * PHP Error Handler
     *
     * @param  object
     * @return void
     */
    public function exception_handler($exception)
    {
        $data = array(
            'errno'         => $exception->getCode(),
            'errtype'       => isset($this->levels[$exception->getCode()]) ? $this->levels[$exception->getCode()] : $exception->getCode(),
            'errstr'        => $exception->getMessage(),
            'errfile'       => $exception->getFile(),
            'errline'       => $exception->getLine(),
            'user_agent'    => $this->_ci->input->user_agent(),
            'ip_address'    => $this->_ci->input->ip_address(),
            'username'      => $this->_ci->session->userdata('e_empname'),
            'time'          => date('Y-m-d H:i:s')
        );

        $this->_ci->db->insert($this->_log_table_name, $data);
    }


    public function log_event($event = "Undefined", $type = 'Undefined', $desc = "Undefined", $cate = "Undefined")
    {   
        $data = array(
            'sys_log_event'         => $event,
            'sys_log_user_id'       => $this->_ci->session->userdata('e_empID'),
            'sys_log_user'          => $this->_ci->session->userdata('e_empname'),
            'sys_log_category'      => $cate,
            'sys_log_type'          => $type,
            'sys_log_description'   => $desc,
            'sys_log_ip'            => $this->_ci->input->ip_address(),
            'sys_log_branch'        => null,
            'sys_log_company'       => $this->_ci->session->userdata('e_companyID'),
            'sys_log_useragent'     => $this->_ci->agent->browser() . " on " . $this->_ci->agent->platform(), 
            'sys_log_user_group'    => $this->_ci->session->userdata('e_usergroupID') 
            );

        $this->_ci->db->insert('sys_log',$data); 
    }
}
/* End of file Lib_log.php */