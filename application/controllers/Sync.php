<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Sync extends ERP_Controller
{

    private $tables;

    function __construct()
    {

        parent::__construct();
        $this->load->database();
        $this->tables = array(

            "srp_erp_pos_menusalesmaster" => array('menuSalesID'),
            "srp_erp_pos_shiftdetails" => array('shiftID'),
            "srp_erp_pos_menusalespayments" => array('menuSalesPaymentID'),
            "srp_erp_pos_menusalesservicecharge" => array('menusalesServiceChargeID'),
            "srp_erp_pos_menusalestaxes" => array('menuSalesTaxID'),
            "srp_erp_generalledger" => array('generalLedgerAutoID'),
            "srp_erp_itemledger" => array('itemLedgerAutoID'),
            "srp_erp_pos_deliveryorders" => array('deliveryOrderID'),
            "srp_erp_pos_customermaster" => array('posCustomerAutoID'),
            "srp_erp_bankledger" => array('bankLedgerAutoID'),
            "srp_erp_pos_javaappredeemhistory" => array('javaAppHistoryID'),
            "srp_erp_pos_menusalesitems" => array('menuSalesItemID'),
            "srp_erp_pos_menusalesitemdetails" => array('menuSalesItemDetailID'),
            "srp_erp_pos_cardtopup" => array('cardTopUpID'),
            "srp_erp_customerinvoicemaster_sync" => array('invoiceAutoID'),
            "srp_erp_customerinvoicedetails_sync" => array('invoiceDetailsAutoID'),
            "srp_erp_generalledger_sync" => array('generalLedgerAutoID'),
            "srp_erp_itemledger_sync" => array('itemLedgerAutoID'),
            "srp_erp_documentapproved_sync" => array('documentApprovedID'),
            "srp_erp_pos_wifipasswordsetup" => array('id'),
            "srp_erp_pos_cardissue" => array('cardIssueID'),
            "srp_erp_pos_menusalesoutlettaxes" => array('menuSalesOutletTaxID')
        );


        $this->id_store = $this->config->item("outletID");
    }

    function index()
    {
        $message = 'Started on ' . date("H:i:s A") . " \n ";

        $newLine = "\r\n";
        $output = $update = "";

        $output .= current_companyID() . "___";
        foreach ($this->tables as $table_name => $columns) {


            $col_check = "SHOW COLUMNS FROM `{$table_name}` LIKE 'is_sync'";
            $col_exists = $this->db->query($col_check);
            $numOfRows = count($col_exists);

            if ($numOfRows > 0) { // is_sync column exists for this table
                $sql = "SELECT * FROM {$table_name} WHERE is_sync = 0 LIMIT 50";
                $rows = $this->db->query($sql);
                $rowCount2 = count($rows);
                if ($rowCount2 > 0) { // where is_sync = 0
                    foreach ($rows->result_array() as $row) {
                        $col_val = $update_col_val = $already_exists = array();

                        foreach ($row as $name => $val) {
                            if (is_null($val)) {
                                continue;
                            }
                            $search_string = "'";
                            $pos = strpos($val, $search_string);
                            $val = str_replace(array("'", "\"", "`"), array('&#8217;', '&#8221;', '&#96;'), $val);
                            if ($pos === false) {
                                $update_col_val[] = " `{$name}` = '{$val}' ";
                            }
                            if ("is_sync" === $name) {
                                $val = 1;
                            }
                            if ("id_store" === $name) {
                                $val = $this->id_store;
                            }
                            $col_val[] = " `{$name}` = '{$val}' ";
                            if (isset($columns[0]) && $name === $columns[0]) {
                                $already_exists[] = " `$columns[0]` = '{$val}'";
                            }
                            if (isset($columns[1]) && $name === $columns[1]) {
                                $already_exists[] = " `$columns[1]` = '{$val}'";
                            }
                        }


                        if (is_array($col_val) && count($col_val) > 0) {
                            $output .= "SELECT * FROM `{$table_name}` WHERE `id_store` = {$this->id_store} AND " . implode(" AND ", $already_exists) . " ||";
                            $output .= "INSERT INTO `{$table_name}` SET " . implode(",", $col_val) . " ||";
                            $output .= "UPDATE `{$table_name}` SET " . implode(",", $col_val) . " WHERE `id_store` = {$this->id_store} AND " . implode(" AND ", $already_exists);
                            $output .= " ;; ";
                            $update .= "UPDATE {$table_name} SET is_sync = 1 WHERE " . implode(" AND ", $update_col_val) . " ;; ";
                        }
                    }
                } // if is_sync = 0

            } // if is_sync column exists
        }


        if (!empty($output) && $this->post_data($output)) {
            $update = explode(" ;; ", $update);
            foreach ($update as $upq) {
                if (!empty($upq)) {
                    $this->db->query($upq);
                }
            }
            $message .= "data updated! \n";
            $s_msg = 'updated!';
            $state = 1;
        } else {
            $state = 0;
            $message .= "empty \n";
            $s_msg = 'empty';
        }

        $message .= " \n End Time" . date("H:i:s A");

        $sql = 'select count(menuSalesID) as syncCount from srp_erp_pos_menusalesmaster where is_sync = 0';
        $count = $this->db->query($sql)->row('syncCount');

        echo json_encode(array('status' => $state, 'message' => $message, 'shortMessage' => $s_msg, 'sync_pending_bill_count' => $count));

    }

    function post_data($qry)
    {

        $qry = htmlspecialchars(urlencode($qry));
        //echo $qry;
        $data = "qry=" . $qry;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config->item("sync_server_url"));
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-length: ' . strlen($qry)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_CRLF, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $result = curl_exec($ch);

        if (curl_error($ch)) {
            $message = curl_error($ch);
            echo json_encode(array('status' => 3, 'message' => 'No internet connection!.<br/>' . $message, 'shortMessage' => 'Can not reach host'));
            exit;
        }


        //var_dump($result);

        /*echo $this->config->item("sync_server_url");
        echo $data;
        echo '<br/><br/>'; 
        */
        //echo '<h3> Curl Result </h2>';
        //echo $result; //testing remote it later


        curl_close($ch);
        if (1 === intval($result)) {
            return TRUE;
        } else {
            //echo $result;
            return $result;
        }
    }

    function pull_data()
    {
        $companyID = current_companyID();
        $companyInfo = get_companyInformation($companyID);
        $values = array(
            'companyID' => $companyID,
            'token' => $companyInfo['localposaccesstoken']
        );

        $ch = curl_init(); //http post to another server

        curl_setopt($ch, CURLOPT_URL, $this->config->item("sync_server_pull_url"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);


        $params = http_build_query($values);
        // receive server response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $server_output = curl_exec($ch);


        /*echo 'Curl output<br/>';
        echo $server_output;
        exit;*/
        $result = json_decode($server_output, true);


        if (curl_error($ch)) {
            echo json_encode(array('error' => 1, 'message' => curl_error($ch)));
            exit;
        }

        //echo $server_output; // comment it after finishing the testing


        if (isset($result['error']) && $result['error'] == 0) {

            $records = 0;
            $tables = 1;
            $outputMsg = '';

            /*echo '<pre>';
            print_r( $result['c_db_data']['table']);
            exit;*/
            /** Local central db */
            /*$central_db = $this->config->item("local_central_db");*/
            $db3 = $this->load->database('db3', TRUE);
            $db3->query('DROP TABLE IF EXISTS ' . $result['c_db_data']['table']);
            $db3->query($result['c_db_data']['query']);
            $db3->insert_batch($result['c_db_data']['table'], $result['c_db_data']['data']);
            $outputMsg .= $db3->database . '.' . $result['c_db_data']['table'] . ' table updated with ' . $result['c_db_data']['count'] . ' records.<br/>';
            $records += $result['c_db_data']['count'];
            /** End Local central db */

            if (isset($result['data_output']) && !empty($result['data_output'])) {
                foreach ($result['data_output'] as $tmpData) {

                    /*echo '<pre>';
                    print_r($tmpData);
                    exit;*/
                    if (!empty($tmpData)) {
                        $tables++;
                        $table_name = $tmpData['table'];
                        $new_data = $tmpData['data'];
                        $records += $tmpData['count'];


                        if ($table_name != 'srp_erp_pos_kitchennotesamples' && $table_name != 'srp_erp_pos_outletprinters') {
                            /*$this->db->query('TRUNCATE TABLE ' . $table_name);*/
                            $this->db->query('DROP TABLE IF EXISTS ' . $table_name);
                            $this->db->query($tmpData['query']);
                            /*echo '<pre>';
                            print_r($table_name);
                            echo '<hr>';*/

                            if (!empty($new_data)) {
                                $r = $this->db->insert_batch($table_name, $new_data);
                            }
                        }


                        $outputMsg .= $table_name . ' updated with ' . $tmpData['count'] . ' records.<br/>';
                    }


                }
            }
            $msg = number_format($tables) . ' table updated with ' . number_format($records) . ' records';
            echo json_encode(array('from_DB' => $result['db'], 'to_DB' => $this->db->database, 'error' => 0, 'message' => $msg));
        } else {
            echo json_encode($result);
        }


        curl_close($ch);
    }

    function pull_data_giftCard()
    {
        $companyID = current_companyID();
        $companyInfo = get_companyInformation($companyID);
        $values = array(
            'companyID' => $companyID,
            'token' => $companyInfo['localposaccesstoken']
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config->item("sync_server_pull_url_gift_card"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $params = http_build_query($values);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $server_output = curl_exec($ch);
        $result = json_decode($server_output, true);

        if (curl_error($ch)) {
            echo json_encode(array('error' => 2, 'message' => 'No Internet, try again!.<br/>' . curl_error($ch)));
            exit;
        }

        if (isset($result['error']) && $result['error'] == 0) {

            $records = 0;
            $tables = 1;
            $outputMsg = '';

            /** Local central db */
            $db3 = $this->load->database('db3', TRUE);
            $db3->query('DROP TABLE IF EXISTS ' . $result['c_db_data']['table']);
            $db3->query($result['c_db_data']['query']);
            $db3->insert_batch($result['c_db_data']['table'], $result['c_db_data']['data']);
            $outputMsg .= $db3->database . '.' . $result['c_db_data']['table'] . ' table updated with ' . $result['c_db_data']['count'] . ' records.<br/>';
            $records += $result['c_db_data']['count'];
            /** End Local central db */

            if (isset($result['data_output']) && !empty($result['data_output'])) {
                foreach ($result['data_output'] as $tmpData) {
                    if (!empty($tmpData)) {
                        $tables++;
                        $table_name = $tmpData['table'];
                        $new_data = $tmpData['data'];
                        $records += $tmpData['count'];


                        if ($table_name != 'srp_erp_pos_kitchennotesamples' && $table_name != 'srp_erp_pos_outletprinters') {
                            $this->db->query('DROP TABLE IF EXISTS ' . $table_name);
                            $this->db->query($tmpData['query']);
                            if (!empty($new_data)) {
                                $r = $this->db->insert_batch($table_name, $new_data);
                            }
                        }
                        $outputMsg .= $table_name . ' updated with ' . $tmpData['count'] . ' records.<br/>';
                    }
                }
            }
            $msg = number_format($tables) . ' table updated with ' . number_format($records) . ' records';
            echo json_encode(array('from_DB' => $result['db'], 'to_DB' => $this->db->database, 'error' => 0, 'message' => $msg));
        } else {
            echo json_encode(array('error' => 3, 'message' => 'Something went wrong, Please refresh your browser and check it again'));
        }
        curl_close($ch);
    }

}