<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{
    Private $main;


    public function index($employee_code = NULL, $message = NULL, $type = 'e')
    {
        $Session_data = null;
        $this->load->model('session_model');
        if ($employee_code) {
            if (md5($this->session->userdata('e_empID')) == $employee_code) {
                redirect('dashboard');
            } else {
                $Session_data = $this->session_model->createSession($employee_code);
                if ($Session_data['stats']) {
                    $this->session->set_flashdata('s', $this->session->userdata('e_empname') . ' Successfully logged into System');
                    redirect('dashboard');
                } else {

                    $this->no_permission();

                }
            }
        } else {
            $data['title'] = 'Login';
            $data['extra'] = $message;
            $data['type'] = $type;
            $this->load->view('login_page', $data);
            //$this->load->view('site_under_construction', $data);
            //$this->logout();
        }
    }

    function forget_password()
    {
        $data['title'] = 'Forget Password';
        $data['extra'] = NULL;
        $data['type'] = 'e';
        $this->load->view('forget_password', $data);
    }

    public function login_pin($id)
    {
        $Session_data = null;
        $this->load->model('session_model');
        if ($this->session->userdata('e_empID')) {
            redirect('dashboard');
        } else {
            $this->no_permission_pin($id);
        }

    }

    function no_permission()
    {
        $this->session->sess_destroy();
        //header('Location:/../../srp');
        $data['title'] = 'Login';
        $data['extra'] = NULL;
        $data['type'] = 'e';
        $this->load->view('login_page', $data);
    }

    function no_permission_pin($id)
    {
        $this->session->sess_destroy();
        //header('Location:/../../srp');
        $data['title'] = 'Login';
        $data['extra'] = NULL;
        $data['adminMasterID'] = $id;
        $this->load->view('login_pin_page', $data);
    }

    function no_permission_forgot_password()
    {
        $this->session->sess_destroy();
        //header('Location:/../../srp');
        $data['title'] = 'Reset Password';
        $data['extra'] = NULL;
        $this->load->view('reset_password', $data);
    }

    function session_expaide()
    {
        echo "session_expaide";
    }

    public function logout()
    {

        $companyID = $this->session->userdata('companyID');
        $this->setDb();
        $companyInfo = get_companyInformation($companyID);
        $data['title'] = 'Login';
        $data['extra'] = NULL;
        $this->session->sess_destroy();
        if ($companyInfo['productID'] == 2) {
            header('Location:' . site_url('gears'));
            exit;
        } else {
            $this->load->view('login_page', $data);
            //header('Location:'.site_url('Login/logout'));
            //exit;
        }
    }

    protected function setDb()
    {
        $companyID = $this->session->userdata('companyID');
        $companyInfo = get_companyInformation($companyID);
        if (!empty($companyInfo)) {
            $config['hostname'] = trim($this->encryption->decrypt($companyInfo["host"]));
            $config['username'] = trim($this->encryption->decrypt($companyInfo["db_username"]));
            $config['password'] = trim($this->encryption->decrypt($companyInfo["db_password"]));
            $config['database'] = trim($this->encryption->decrypt($companyInfo["db_name"]));
            $config['dbdriver'] = 'mysqli';
            $config['db_debug'] = FALSE;
            $config['char_set'] = 'utf8';
            $config['dbcollat'] = 'utf8_general_ci';
            $config['cachedir'] = '';
            $config['swap_pre'] = '';
            $config['encrypt'] = FALSE;
            $config['compress'] = FALSE;
            $config['stricton'] = FALSE;
            $config['failover'] = array();
            $config['save_queries'] = TRUE;
            $this->load->database($config, FALSE, TRUE);
        }
    }

    public function gears()
    {
        $this->session->sess_destroy();
        $data['title'] = 'Login';
        $data['extra'] = NULL;
        $this->load->view('login_page_2', $data);
    }


    function session_status()
    {
        $output = ($this->session->userdata("empID")) ? 1 : 0;
        echo json_encode(array('status' => $output, 'csrf' => $this->security->get_csrf_hash()));
    }

    function company_configuration()
    {
        $data['title'] = 'Welcome Dashboard';
        $data['main_content'] = 'system/configuration/company_configuration';
        $data['extra'] = NULL;
        $this->load->view('include/template', $data);
    }

    function loginSubmit()
    {
        /*$encryption_key = 'CKXH2U9RPY3EFD70TLS1ZG4N8WQBOVI6AMJ5';
        $this->load->library("Cryptor",$encryption_key);*/
        $this->encryption->initialize(array('driver' => 'mcrypt'));
        $this->load->model('session_model');
        $this->form_validation->set_rules('Username', 'Username', 'trim|required');
        $this->form_validation->set_rules('Password', 'Password', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->index(FALSE, $result['message'] = validation_errors(), $result['type'] = 'e');
        } else {
            $login_data['userN'] = $this->input->post('Username');
            $login_data['passW'] = md5($this->input->post('Password'));
            $this->db->select('*');
            $this->db->where("UserName", $login_data['userN']);
            $this->db->where("Password", $login_data['passW']);
            $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
            $resultDb2 = $this->db->get("user")->row_array();
            $result = "";
            if ($resultDb2) {
                $config['hostname'] = trim($this->encryption->decrypt($resultDb2["host"]));
                $config['username'] = trim($this->encryption->decrypt($resultDb2["db_username"]));
                $config['password'] = trim($this->encryption->decrypt($resultDb2["db_password"]));
                $config['database'] = trim($this->encryption->decrypt($resultDb2["db_name"]));
                $config['dbdriver'] = 'mysqli';
                $config['db_debug'] = FALSE;
                $config['char_set'] = 'utf8';
                $config['dbcollat'] = 'utf8_general_ci';
                $config['cachedir'] = '';
                $config['swap_pre'] = '';
                $config['encrypt'] = FALSE;
                $config['compress'] = FALSE;
                $config['stricton'] = FALSE;
                $config['failover'] = array();
                $config['save_queries'] = TRUE;
                $this->load->database($config, FALSE, TRUE);

                $result = $this->session_model->authenticateLogin($login_data);

                if ($result['stats']) {
                    $this->index($result['data'], NULL);
                } else {
                    $this->index(FALSE, $result['message']);
                }
            } else {
                $this->db->select('*');
                $this->db->where("UserName", $login_data['userN']);
                //$this->db->where("Password", $login_data['passW']);
                $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
                $usernameDb2 = $this->db->get("user")->row_array();
                if ($usernameDb2) {
                    $config['hostname'] = trim($this->encryption->decrypt($usernameDb2["host"]));
                    $config['username'] = trim($this->encryption->decrypt($usernameDb2["db_username"]));
                    $config['password'] = trim($this->encryption->decrypt($usernameDb2["db_password"]));
                    $config['database'] = trim($this->encryption->decrypt($usernameDb2["db_name"]));
                    $config['dbdriver'] = 'mysqli';
                    $config['db_debug'] = FALSE;
                    $config['char_set'] = 'utf8';
                    $config['dbcollat'] = 'utf8_general_ci';
                    $config['cachedir'] = '';
                    $config['swap_pre'] = '';
                    $config['encrypt'] = FALSE;
                    $config['compress'] = FALSE;
                    $config['stricton'] = FALSE;
                    $config['failover'] = array();
                    $config['save_queries'] = TRUE;
                    $this->load->database($config, FALSE, TRUE);
                    $result = $this->session_model->authenticateLoginUserName($login_data);
                    if ($result['stats']) {
                        $this->index($result['data'], NULL);
                    } else {
                        $this->index(FALSE, $result['message']);
                    }
                } else {
                    $data['title'] = 'Login';
                    $data['type'] = 'e';
                    $data['extra'] = 'Wrong user name or password. Please  try again.';
                    $this->load->view('login_page', $data);
                }
                /*$data['title'] = 'Login';
                $data['type'] = 'e';
                $data['extra'] = 'Invalid username or password. Please  try again.';
                $this->load->view('login_page', $data);*/
            }
        }
    }



    function loginSubmit_gears()
    {
        $this->encryption->initialize(array('driver' => 'mcrypt'));
        $this->load->model('session_model');
        $this->form_validation->set_rules('Username', 'Username', 'trim|required');
        $this->form_validation->set_rules('Password', 'Password', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $data['title'] = 'Login';
            $data['type'] = 'e';
            $data['extra'] = 'Please enter the username & password.';
            $this->load->view('login_page_2', $data);
        } else {
            $login_data['userN'] = $this->input->post('Username');
            $login_data['passW'] = md5($this->input->post('Password'));
            $this->db->select('*');
            $this->db->where("UserName", $login_data['userN']);
            $this->db->where("Password", $login_data['passW']);
            $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
            $resultDb2 = $this->db->get("user")->row_array();
            $result = "";
            if ($resultDb2) {
                $config['hostname'] = trim($this->encryption->decrypt($resultDb2["host"]));
                $config['username'] = trim($this->encryption->decrypt($resultDb2["db_username"]));
                $config['password'] = trim($this->encryption->decrypt($resultDb2["db_password"]));
                $config['database'] = trim($this->encryption->decrypt($resultDb2["db_name"]));
                $config['dbdriver'] = 'mysqli';
                $config['db_debug'] = FALSE;
                $config['char_set'] = 'utf8';
                $config['dbcollat'] = 'utf8_general_ci';
                $config['cachedir'] = '';
                $config['swap_pre'] = '';
                $config['encrypt'] = FALSE;
                $config['compress'] = FALSE;
                $config['stricton'] = FALSE;
                $config['failover'] = array();
                $config['save_queries'] = TRUE;
                $this->load->database($config, FALSE, TRUE);
                $result = $this->session_model->authenticateLogin($login_data);

                $companyID = $resultDb2['company_id'];
                $this->db->select('productID');
                $this->db->from('srp_erp_company');
                $this->db->where('company_id', $companyID);
                $productID = $this->db->get()->row('productID');
                    if ($result['stats']) {
                        if ($productID == 2) {
                            $this->index($result['data'], NULL);
                        } else {
                            $data['title'] = 'Login';
                            $data['type'] = 'e';
                            $data['extra'] = 'You are not authorize to use this product.';
                            $this->load->view('login_page_2', $data);
                        }

                    } else {
                        $data['title'] = 'Login';
                        $data['type'] = 'e';
                        $data['extra'] = $result['message'];
                        $this->load->view('login_page_2', $data);
                    }


            } else {
                $this->db->select('*');
                $this->db->where("UserName", $login_data['userN']);
                //$this->db->where("Password", $login_data['passW']);
                $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
                $usernameDb2 = $this->db->get("user")->row_array();


                if ($usernameDb2) {
                    $config['hostname'] = trim($this->encryption->decrypt($usernameDb2["host"]));
                    $config['username'] = trim($this->encryption->decrypt($usernameDb2["db_username"]));
                    $config['password'] = trim($this->encryption->decrypt($usernameDb2["db_password"]));
                    $config['database'] = trim($this->encryption->decrypt($usernameDb2["db_name"]));
                    $config['dbdriver'] = 'mysqli';
                    $config['db_debug'] = FALSE;
                    $config['char_set'] = 'utf8';
                    $config['dbcollat'] = 'utf8_general_ci';
                    $config['cachedir'] = '';
                    $config['swap_pre'] = '';
                    $config['encrypt'] = FALSE;
                    $config['compress'] = FALSE;
                    $config['stricton'] = FALSE;
                    $config['failover'] = array();
                    $config['save_queries'] = TRUE;
                    $this->load->database($config, FALSE, TRUE);
                    $result = $this->session_model->authenticateLoginUserName($login_data);

                    $companyID = $resultDb2['company_id'];
                    $this->db->select('productID');
                    $this->db->from('srp_erp_company');
                    $this->db->where('company_id', $companyID);
                    $productID = $this->db->get()->row('productID');
                    if ($result['stats']) {
                        if ($productID == 2) {
                            $this->index($result['data'], NULL);
                        } else {
                            $data['title'] = 'Login';
                            $data['type'] = 'e';
                            $data['extra'] = 'You are not authorize to use this product.';
                            $this->load->view('login_page_2', $data);
                        }

                    } else {
                        $data['title'] = 'Login';
                        $data['type'] = 'e';
                        $data['extra'] = $result['message'];
                        $this->load->view('login_page_2', $data);
                    }


                } else {
                    $data['title'] = 'Login';
                    $data['type'] = 'e';
                    $data['extra'] = 'Wrong user name or password. Please  try again.';
                    $this->load->view('login_page_2', $data);
                }

            }
        }
    }


    function forgetPasswordSubmit()
    {
        $this->form_validation->set_rules('email', 'email', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $data['title'] = 'Login';
            $data['extra'] = validation_errors();
            $this->load->view('forget_password', $data);
        } else {
            $this->db->select('*');
            $this->db->where("email", $this->input->post('email'));
            $result = $this->db->get("user")->row_array();
            if ($result) {
                $PIN = rand(10000, 99999);
                $encryptValue = trim(sha1($PIN));
                $param['randNum'] = trim($encryptValue);
                $param['id'] = trim($result["empID"]);
                $param['autoID'] = trim($result["EidNo"]);
                $update = $this->db->where("email", $this->input->post('email'))->update('user', array('randNum' => trim($encryptValue)));
                if ($update) {
                    $config['charset'] = "utf - 8";
                    $config['mailtype'] = "html";
                    $config['wordwrap'] = TRUE;
                    $config['protocol'] = 'smtp';
                    $config['smtp_host'] = 'smtp.sendgrid.net';
                    $config['smtp_user'] = 'azure_61fdfd424467a8cecb84bf014f8b5e26@azure.com';
                    $config['smtp_pass'] = 'P@ssw0rd240!';
                    $config['smtp_crypto'] = 'tls';
                    $config['smtp_port'] = '587';
                    $condig['crlf'] = "\r\n";
                    $config['newline'] = "\r\n";

                    $this->load->library('email', $config);
                    $this->email->from('noreply@spur-int.com', SYS_NAME);
                    if (!empty($param)) {
                        $this->email->to($this->input->post('email'));
                        $this->email->subject('Forgot Password');
                        $this->email->message($this->load->view('system/email_template/email_template', $param, TRUE));
                    }
                    $result = $this->email->send();
                    if ($result) {
                        $data['title'] = 'Login';
                        $data['extra'] = 'An email has been sent to your mail inbox, Use the password reset link in the mail to reset your password';
                        $data['type'] = 's';
                        $this->load->view('forget_password', $data);
                    } else {
                        $data['title'] = 'Login';
                        $data['type'] = 'e';
                        $data['extra'] = 'Error occurred in email sending';
                        $this->load->view('forget_password', $data);
                    }
                } else {
                    $data['title'] = 'Login';
                    $data['type'] = 'e';
                    $data['extra'] = 'Error occurred';
                    $this->load->view('forget_password', $data);
                }
            } else {
                $data['title'] = 'Login';
                $data['extra'] = 'Your email is not registered with the system';
                $data['type'] = 'e';
                $this->load->view('forget_password', $data);
            }
        }
    }

    function loginPinSubmit()
    {
        /* $encryption_key = 'CKXH2U9RPY3EFD70TLS1ZG4N8WQBOVI6AMJ5';
         $this->load->library("Cryptor",$encryption_key);*/
        $this->encryption->initialize(array('driver' => 'mcrypt'));
        $this->load->model('session_model');
        $this->form_validation->set_rules('pinNumber', 'Pin Number', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('msg', validation_errors());
            redirect('pin_login/' . $this->input->post('adminMasterID'));
        } else {

            $this->db->select('*');
            $this->db->where("adminMasterID", $this->input->post('adminMasterID'));
            $this->db->where("pinNumber", $this->input->post('pinNumber'));
            $pinRec = $this->db->get("srp_erp_companyadminmaster")->row_array();
            if ($pinRec) {
                $this->db->select('*');
                $this->db->where("isSystemAdmin", 1);
                $this->db->where("user . companyID", $pinRec["companyID"]);
                $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
                $resultDb2 = $this->db->get("user")->row_array();
                $result = "";
                if ($resultDb2) {
                    $login_data['userN'] = $resultDb2['Username'];
                    $login_data['passW'] = $resultDb2['Password'];
                    $config['hostname'] = trim($this->encryption->decrypt($resultDb2["host"]));
                    $config['username'] = trim($this->encryption->decrypt($resultDb2["db_username"]));
                    $config['password'] = trim($this->encryption->decrypt($resultDb2["db_password"]));
                    $config['database'] = trim($this->encryption->decrypt($resultDb2["db_name"]));
                    $config['dbdriver'] = 'mysqli';
                    $config['db_debug'] = FALSE;
                    $config['char_set'] = 'utf8';
                    $config['dbcollat'] = 'utf8_general_ci';
                    $config['cachedir'] = '';
                    $config['swap_pre'] = '';
                    $config['encrypt'] = FALSE;
                    $config['compress'] = FALSE;
                    $config['stricton'] = FALSE;
                    $config['failover'] = array();
                    $config['save_queries'] = TRUE;
                    $this->load->database($config, FALSE, TRUE);
                    $result = $this->session_model->authenticateLogin($login_data);
                }
                if ($result['stats']) {
                    $this->main = $this->load->database('db2', TRUE);
                    $this->main->set('pinNumber', null);
                    $this->main->where("adminMasterID", $this->input->post('adminMasterID'));
                    $this->main->update("srp_erp_companyadminmaster");
                    $this->index($result['data'], NULL);
                } else {
                    $this->session->set_flashdata('msg', 'Error Occurred');
                    redirect('pin_login/' . $this->input->post('adminMasterID'));
                }
            } else {
                $this->session->set_flashdata('msg', 'Invalid PIN Number');
                redirect('pin_login/' . $this->input->post('adminMasterID'));
            }
        }
    }

    function reset_password($randNum, $empID, $autoID)
    {
        $password = $this->input->post('Password');
        if (isset($password)) {
            $this->form_validation->set_rules('Password', 'Password', 'trim|required');
            $this->form_validation->set_rules('ConfirmPassword', 'Confirm Password', 'trim|required|matches[Password]');
            if ($this->form_validation->run() == FALSE) {
                $data['title'] = 'Login';
                $data['extra'] = validation_errors();
                $this->load->view('reset_password', $data);
            } else {
                $this->db->select('*');
                $this->db->where("randNum", $randNum);
                $this->db->where("empID", $empID);
                $this->db->where("EidNo", $autoID);
                $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
                $result = $this->db->get("user")->row_array();
                if ($result) {
                    $update = $this->db->where("EidNo", $autoID)->update('user', array('randNum' => null, 'Password' => md5($this->input->post('Password'))));
                    if ($update) {
                        $login_data['userN'] = $result['Username'];
                        $login_data['passW'] = $result['Password'];
                        $config['hostname'] = trim($this->encryption->decrypt($result["host"]));
                        $config['username'] = trim($this->encryption->decrypt($result["db_username"]));
                        $config['password'] = trim($this->encryption->decrypt($result["db_password"]));
                        $config['database'] = trim($this->encryption->decrypt($result["db_name"]));
                        $config['dbdriver'] = 'mysqli';
                        $config['db_debug'] = FALSE;
                        $config['char_set'] = 'utf8';
                        $config['dbcollat'] = 'utf8_general_ci';
                        $config['cachedir'] = '';
                        $config['swap_pre'] = '';
                        $config['encrypt'] = FALSE;
                        $config['compress'] = FALSE;
                        $config['stricton'] = FALSE;
                        $config['failover'] = array();
                        $config['save_queries'] = TRUE;
                        $this->load->database($config, FALSE, TRUE);
                        $updateEmp = $this->db->where("EidNo", $empID)->update('srp_employeesdetails', array('Password' => md5($this->input->post('Password'))));
                        if ($updateEmp) {
                            $this->session->set_flashdata('msg', 'Successfully Password Changed');
                            redirect('LoginPage');
                        } else {
                            $data['title'] = 'Login';
                            $data['extra'] = 'Error Occurred';
                            $data['type'] = 'e';
                            $this->load->view('reset_password', $data);
                        }
                    }
                } else {
                    $data['title'] = 'Login';
                    $data['extra'] = 'Invalid Token';
                    $data['type'] = 'e';
                    $this->load->view('reset_password', $data);
                }
            }
        } else {
            $Session_data = null;
            $this->load->model('session_model');
            if ($this->session->userdata('e_empID')) {
                redirect('dashboard');
            } else {
                $this->no_permission_forgot_password();
            }
        }
    }

    public function under_construction()
    {
        //unset($_COOKIE);
        //var_dump($_COOKIE);
        //exit;
        $this->session->sess_destroy();
        $data['title'] = 'Login';
        $data['extra'] = NULL;
        $this->load->view('site_under_construction', $data);

    }
}
