<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sync_server extends CI_Controller
{

    function __construct()
    {
        parent::__construct();

        $companyID = '';
        if (isset($_POST['qry'])) {
            $input = htmlspecialchars_decode(urldecode($_POST['qry']));
            $inputExp = explode("___", $input);
            $companyID = trim($inputExp[0]);
            $_POST['qry'] = isset($inputExp[1]) ? $inputExp[1] : '';
        }

        if (!empty($companyID) && !empty($_POST['qry'])) {
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
        } else {
            $this->load->database();
        }
    }

    function get_csrf()
    {
        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        echo json_encode($csrf);

    }


    function index()
    {
        if (isset($_POST['qry']) && trim($_POST['qry']) != '') {
            $qry = $_POST['qry'];
            $qry = explode(" ;; ", $qry);

            foreach ($qry as $q) {
                $q = explode("||", $q);

                $r = $this->db->query($q[0])->result_array();

                if (!empty($r) && count($r)) {
                    $this->db->query($q[2]);
                } else {
                    $this->db->query($q[1]);
                }

            }
            echo 1;
            exit;
        } else {
            echo 0;
        }
    }

    function pull_data()
    {

        $post = $this->input->post();
        $companyID = $this->input->post('companyID');
        $companyInfo = get_companyInformation($companyID);
        $input_token = $this->input->post('token');


        if (!empty($companyInfo)) {

            $centralDB_users = $this->db->select('*')->from('user')->where('companyID', $companyID)->get()->result_array();

            $query = $this->db->query('SHOW CREATE TABLE  user')->row_array();
            $centralDB['table'] = 'user';
            $centralDB['count'] = count($centralDB_users);
            $centralDB['data'] = $centralDB_users;
            $centralDB['query'] = $query['Create Table'];

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

            $companyInfo = get_companyInformation($companyID);
            $outputData = array();


            if (($companyInfo['localposaccesstoken'] === $input_token) && (!empty($companyInfo['localposaccesstoken'])) && ($companyInfo['localposaccesstoken'] != '') && ($companyInfo['localposaccesstoken'] != '0')) {

                $tables[] = 'srp_employeesdetails';
                $tables[] = 'srp_erp_companycontrolaccounts';

                /** SRP_ERP_POS_TABLES */
                $tables[] = 'srp_erp_pos_auth_processassign';
                $tables[] = 'srp_erp_pos_auth_processmaster';
                $tables[] = 'srp_erp_pos_auth_usergroupdetail';
                $tables[] = 'srp_erp_pos_auth_usergroupmaster';
				
				
				//--------------------------------------------------
				$tables[] = ‘srp_erp_companyfinanceyear’;
				$tables[] = ‘srp_erp_companypolicy’;
				$tables[] = ‘srp_erp_companypolicymaster’;
				$tables[] = ‘srp_erp_companypolicymaster_value’;
				$tables[] = ‘srp_erp_currencydenomination’;
				$tables[] = ‘srp_erp_currencymaster’;
				$tables[] = ‘srp_erp_customerinvoicemaster’;
				$tables[] = ‘srp_erp_customermaster’;
				$tables[] = ‘srp_erp_customertypemaster’;
				$tables[] = ‘srp_erp_itemmaster’;
				$tables[] = ‘srp_erp_lang_languages’;
				$tables[] = ‘srp_erp_passwordcomplexcity’;
				$tables[] = ‘srp_erp_pay_imagepath’;
				$tables[] = ‘srp_erp_pos_addon’;
				$tables[] = ‘srp_erp_pos_camera_setup’;
				$tables[] = ‘srp_erp_pos_cardissue’;
				$tables[] = ‘srp_erp_pos_cardtopup’;
				$tables[] = ‘srp_erp_pos_crewmembers’;
				$tables[] = ‘srp_erp_pos_customermaster’;
				$tables[] = ‘srp_erp_pos_customers’;
				$tables[] = ‘srp_erp_pos_deliveryorders’;
				$tables[] = ‘srp_erp_pos_diningroommaster’;
				$tables[] = ‘srp_erp_pos_diningtables’;
				$tables[] = ‘srp_erp_pos_franchisemaster’;
				$tables[] = ‘srp_erp_pos_giftcardmaster’;
			//	$tables[] = ‘srp_erp_pos_invoice’;
			//	$tables[] = ‘srp_erp_pos_invoicedetail’;
			//	$tables[] = ‘srp_erp_pos_invoicehold’;
			//	$tables[] = ‘srp_erp_pos_invoiceholddetail’;
			//	$tables[] = ‘srp_erp_pos_invoicepayments’;
				$tables[] = ‘srp_erp_pos_menuyieldpreparation’;
				$tables[] = ‘srp_erp_pos_menuyieldpreparationdetails’;
				$tables[] = ‘srp_erp_pos_menuyields’;
				$tables[] = ‘srp_erp_pos_menuyieldsdetails’;
				$tables[] = ‘srp_erp_pos_outletprinters’;
				$tables[] = ‘srp_erp_pos_wac_updatehistory’;
				$tables[] = ‘srp_erp_pos_wifipasswordsetup’;
				$tables[] = ‘srp_erp_taxmaster’;
				$tables[] = ‘srp_erp_timezonedetail’;
				$tables[] = ‘srp_erp_timezonemaster’;
				$tables[] = ‘srp_erp_unit_of_measure’;
				$tables[] = ‘srp_erp_unitsconversion’;
				$tables[] = ‘srp_erp_warehouseitems’;
//---------------------------------------------------------------------------------

                $tables[] = 'srp_erp_pos_counters';
                $tables[] = 'srp_erp_pos_crewroles';
                //$tables[] = 'srp_erp_pos_crewmembers'; //FOREIGN KEY CONSTRAIN

                $tables[] = 'srp_erp_pos_customertypemaster';
                $tables[] = 'srp_erp_pos_kitchenlocation';
                $tables[] = 'srp_erp_pos_kitchennotesamples';

                $tables[] = 'srp_erp_pos_menucategory';
                $tables[] = 'srp_erp_pos_menudetails';
                $tables[] = 'srp_erp_pos_menumaster';
                $tables[] = 'srp_erp_pos_menupackcategory';
                $tables[] = 'srp_erp_pos_menupackgroupmaster';
                $tables[] = 'srp_erp_pos_menupackitem';

                $tables[] = 'srp_erp_pos_menusize';
                $tables[] = 'srp_erp_pos_menutaxes';
                $tables[] = 'srp_erp_pos_menutaxmaster';

                $tables[] = 'srp_erp_pos_outletprinters';
                $tables[] = 'srp_erp_pos_outlettemplatedetail';
                $tables[] = 'srp_erp_pos_outlettemplatemaster';

                $tables[] = 'srp_erp_pos_packgroupdetail';
                $tables[] = 'srp_erp_pos_paymentglconfigdetail';
                $tables[] = 'srp_erp_pos_paymentglconfigmaster';
                $tables[] = 'srp_erp_pos_paymentmethods';
                $tables[] = 'srp_erp_pos_policydetail';
                $tables[] = 'srp_erp_pos_policymaster';
                $tables[] = 'srp_erp_pos_printtemplatedetail';
                $tables[] = 'srp_erp_pos_printtemplatemaster';
                $tables[] = 'srp_erp_pos_promotionapplicableitems';
                $tables[] = 'srp_erp_pos_promotionsetupdetail';
                $tables[] = 'srp_erp_pos_promotionsetupmaster';
                $tables[] = 'srp_erp_pos_promotiontypes';
                $tables[] = 'srp_erp_pos_promotionwarehouses';
                $tables[] = 'srp_erp_pos_segmentconfig';
                $tables[] = 'srp_erp_pos_templatemaster';

                $tables[] = 'srp_erp_pos_warehousemenucategory';
                $tables[] = 'srp_erp_pos_warehousemenumaster';
                $tables[] = 'srp_erp_pos_warehousemenucategory';

                $i = 0;

                foreach ($tables as $table) {
                    $query = $this->db->query('SHOW CREATE TABLE  '.$table)->row_array();

                    $tblData = $this->db->select('*')->from($table)->get()->result_array();
                    $outputData[$i]['table'] = $table;
                    $outputData[$i]['count'] = count($tblData);
                    $outputData[$i]['data'] = $tblData;
                    $outputData[$i]['query'] = $query['Create Table'];
                    $i++;
                }

                if (!empty($outputData)) {
                    echo json_encode(array('db' => $this->db->database, 'error' => 0, 'message' => '', 'data_output' => $outputData, 'c_db_data' => $centralDB));
                } else {
                    echo json_encode(array('error' => 1, 'message' => 'output data is empty', 'data_output' => $outputData));
                }
            } else {
                echo json_encode(array('error' => 1, 'message' => 'Request fail, token mismatch', 'data_output' => $outputData));
            }


        } else {
            echo json_encode(array('error' => 1, 'message' => 'An error has occurred...! '));
        }


    }
}