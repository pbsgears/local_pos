<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sales_modal extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function save_sales_commision_header()
    {
        $sate = ' Save';
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $date = $this->input->post('asOfDate');
        $asOfDate = input_format_date($date, $date_format_policy);

        $year = explode(' - ', trim($this->input->post('companyFinanceYear')));
        $FYBegin = input_format_date($year[0], $date_format_policy);
        $FYEnd = input_format_date($year[1], $date_format_policy);
        $currency_code = explode('|', trim($this->input->post('currency_code')));

        $data['asOfDate'] = $asOfDate;
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID'));
        $data['transactionCurrency'] = trim($currency_code[0]);
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear'));
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear'));
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period'));
        $data['Description'] = trim($this->input->post('narration'));
        $data['referenceNo'] = trim($this->input->post('referenceNo'));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('salesCommisionID'))) {
            $sate = ' Update';
            $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID')));
            $this->db->update('srp_erp_salescommisionmaster', $data);
            $last_id = trim($this->input->post('salesCommisionID'));
            $this->db->trans_complete();
        } else {
            $this->load->library('sequence');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['salesCommisionCode'] = $this->sequence->sequence_generator('SC');
            $this->db->insert('srp_erp_salescommisionmaster', $data);
            $last_id = $this->db->insert_id();
        }

        $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID')));
        $this->db->select('salesPersonID');
        $existsalesperson = $this->db->get('srp_erp_salescommisionperson')->result_array();
        $existsalesperson = array_map(function ($value) {
            return $value['salesPersonID'];
        }, $existsalesperson);
        //$existsalesperson = array_values($existsalesperson[0]);

        $chkspfordelete = array_diff($existsalesperson, $this->input->post('salesPersonID')); // check sales person for delete
        if ($chkspfordelete) {
            $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID')));
            $this->db->where_in('salesPersonID', $chkspfordelete);
            $this->db->delete('srp_erp_salescommisionperson');

            $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID')));
            $this->db->where_in('salesPersonID', $chkspfordelete);
            $this->db->delete('srp_erp_salescommisiondetail');
        }

        $chkspforinsert = array_diff($this->input->post('salesPersonID'), $existsalesperson); // check sales person for insert

        if ($chkspforinsert) {
            $this->db->select('*');
            $this->db->where_in('salesPersonID', $chkspforinsert);
            $sales_person = $this->db->get('srp_erp_salespersonmaster')->result_array();
            $sales_person_arr = array();
            for ($i = 0; $i < count($sales_person); $i++) {
                $sales_person_arr[$i]['salesCommisionID'] = $last_id;
                $sales_person_arr[$i]['salesPersonID'] = $sales_person[$i]['salesPersonID'];
                $sales_person_arr[$i]['salesPersonCurrencyID'] = $sales_person[$i]['salesPersonCurrencyID'];
                $sales_person_arr[$i]['salesPersonCurrency'] = $sales_person[$i]['salesPersonCurrency'];
                $party_currency = currency_conversionID($data['transactionCurrencyID'], $sales_person[$i]['salesPersonCurrencyID']);
                $sales_person_arr[$i]['salesPersonCurrencyExchangeRate'] = $party_currency['conversion'];
                $sales_person_arr[$i]['salesPersonCurrencyDecimalPlaces'] = $sales_person[$i]['salesPersonCurrencyDecimalPlaces'];
                $sales_person_arr[$i]['liabilityAutoID'] = $sales_person[$i]['receivableAutoID'];
                $sales_person_arr[$i]['expenseAutoID'] = $sales_person[$i]['expanseAutoID'];
                $sales_person_arr[$i]['liabilitySystemGLCode'] = $sales_person[$i]['receivableSystemGLCode'];
                $sales_person_arr[$i]['liabilityGLAccount'] = $sales_person[$i]['receivableGLAccount'];
                $sales_person_arr[$i]['liabilityDescription'] = $sales_person[$i]['receivableDescription'];
                $sales_person_arr[$i]['liabilityType'] = $sales_person[$i]['receivableType'];
                $sales_person_arr[$i]['expenseSystemGLCode'] = $sales_person[$i]['expanseSystemGLCode'];
                $sales_person_arr[$i]['expenseGLAccount'] = $sales_person[$i]['expanseGLAccount'];
                $sales_person_arr[$i]['expenseDescription'] = $sales_person[$i]['expanseDescription'];
                $sales_person_arr[$i]['expenseType'] = $sales_person[$i]['expanseType'];
                $sales_person_arr[$i]['companyID'] = $this->common_data['company_data']['company_id'];
            }
            $this->db->insert_batch('srp_erp_salescommisionperson', $sales_person_arr);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('status' => 0, 'type' => 'e', 'message' => 'Sales commision : ' . $sate . ' Failed.');
        } else {
            $this->db->trans_commit();
            return array('status' => 1, 'type' => 's', 'message' => 'Sales commision : ' . $sate . ' Successfully.', 'last_id' => $last_id);
        }
    }

    function laad_sales_commision_header()
    {
        $convertFormat = convert_date_format_sql();
        $data['person'] = '';
        $this->db->select('*,DATE_FORMAT(asOfDate,\'' . $convertFormat . '\') AS asOfDate');
        $this->db->from('srp_erp_salescommisionmaster');
        $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID')));
        $data['header'] = $this->db->get()->row_array();
        $this->db->select('salesPersonID');
        $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID')));
        $person = $this->db->get('srp_erp_salescommisionperson')->result_array();
        $data['person'] = array_column($person, 'salesPersonID');
        return $data;
    }

    function fetch_template_data($salesCommisionID)
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('*,DATE_FORMAT(asOfDate,\'' . $convertFormat . '\') AS asOfDate');
        $this->db->from('srp_erp_salescommisionmaster');
        $this->db->where('salesCommisionID', $salesCommisionID);
        $this->db->where('companyID', $companyID);
        $data['master'] = $this->db->get()->row_array();

        /* $this->db->select('*');
         $this->db->where('salesCommisionID',$salesCommisionID);
         $this->db->from('srp_erp_salescommisionperson');
         $this->db->join('srp_erp_salespersonmaster','srp_erp_salespersonmaster.salesPersonID=srp_erp_salescommisionperson.salesPersonID');
         $data['sales_person'] = $this->db->get()->result_array();

         $this->db->select('srp_erp_salescommisiondetail.*,invoiceCode,DATE_FORMAT(invoiceDate,\''.$convertFormat.'\') AS invoiceDate,invoiceNarration,customerName, `companyLocalCurrencyDecimalPlaces`,companyLocalAmount,companyLocalCurrency');
         $this->db->where('salesCommisionID',$salesCommisionID);
         $this->db->from('srp_erp_salescommisiondetail');
         $this->db->join('srp_erp_customerinvoicemaster','srp_erp_customerinvoicemaster.invoiceAutoID=srp_erp_salescommisiondetail.invoiceAutoID');
         $data['sales_detail'] = $this->db->get()->result_array();*/

        $invoice = $this->db->query("SELECT invoiceAutoID,invoiceCode,DATE_FORMAT(invoiceDate,'" . $convertFormat . "') AS invoiceDate,invoiceNarration,customerName, `companyLocalCurrencyDecimalPlaces`,companyLocalAmount,companyLocalCurrency,srp_erp_salescommisionperson.*,srp_erp_salespersonmaster.*
FROM srp_erp_salescommisionperson 
INNER JOIN srp_erp_salespersonmaster ON srp_erp_salespersonmaster.salesPersonID=srp_erp_salescommisionperson.salesPersonID
LEFT JOIN (SELECT srp_erp_customerinvoicemaster.*,srp_erp_salescommisiondetail.salesCommisionID FROM srp_erp_salescommisiondetail LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_salescommisiondetail.invoiceAutoID WHERE srp_erp_salescommisiondetail.companyID = $companyID AND salesCommisionID = $salesCommisionID) as srp_erp_salescommisiondetail ON srp_erp_salescommisiondetail.salesCommisionID = srp_erp_salescommisionperson.salesCommisionID AND srp_erp_salescommisiondetail.salesPersonID = srp_erp_salescommisionperson.salesPersonID WHERE srp_erp_salescommisionperson.salesCommisionID = $salesCommisionID GROUP BY
	srp_erp_salescommisionperson.`salesPersonID`,
	srp_erp_salescommisiondetail.`invoiceAutoID`")->result_array();
        //echo $this->db->last_query();

        $this->db->select('srp_erp_salescommisionperson.salesPersonID,DATE_FORMAT(datefrom,\'' . $convertFormat . '\') AS datefrom,DATE_FORMAT(dateTo,\'' . $convertFormat . '\') AS dateTo,fromTargetAmount,toTargetAmount,srp_erp_salespersontarget.percentage');
        $this->db->where('salesCommisionID', $salesCommisionID);
        $this->db->from('srp_erp_salespersontarget');
        $this->db->join('srp_erp_salescommisionperson', 'srp_erp_salescommisionperson.salesPersonID=srp_erp_salespersontarget.salesPersonID');
        $sales_target = $this->db->get()->result_array();

        $value = array();
        $valueSales = array();
        if (!empty($invoice)) {
            foreach ($invoice as $val) {
                if ($val["invoiceAutoID"] == NULL) {
                    $value[$val["SalesPersonCode"] . '-' . $val['SalesPersonName']]['salesperson'] = array('SalesPersonCode' => $val["SalesPersonCode"], 'SalesPersonName' => $val["SalesPersonName"], 'salesPersonID' => $val["salesPersonID"], 'adjustment' => $val["adjustment"], 'percentage' => $val["percentage"], 'salesPersonImage' => $val["salesPersonImage"], 'SecondaryCode' => $val["SecondaryCode"], 'contactNumber' => $val["contactNumber"], 'SalesPersonEmail' => $val["SalesPersonEmail"], 'wareHouseDescription' => $val["wareHouseDescription"], 'SalesPersonAddress' => $val["SalesPersonAddress"], 'salesPersonCurrency' => $val["salesPersonCurrency"], 'salesPersonTargetType' => $val["salesPersonTargetType"], 'salesPersonTarget' => $val["salesPersonTarget"], 'salesPersonCurrencyDecimalPlaces' => $val["salesPersonCurrencyDecimalPlaces"], 'salesPersonCurrency' => $val["salesPersonCurrency"]);
                    $value[$val["SalesPersonCode"] . '-' . $val['SalesPersonName']]['invoice'] = array();
                } else {
                    $value[$val["SalesPersonCode"] . '-' . $val['SalesPersonName']]['salesperson'] = $val["invoiceAutoID"] == NULL ? array() : array('SalesPersonCode' => $val["SalesPersonCode"], 'SalesPersonName' => $val["SalesPersonName"], 'salesPersonID' => $val["salesPersonID"], 'adjustment' => $val["adjustment"], 'percentage' => $val["percentage"], 'salesPersonImage' => $val["salesPersonImage"], 'SecondaryCode' => $val["SecondaryCode"], 'contactNumber' => $val["contactNumber"], 'SalesPersonEmail' => $val["SalesPersonEmail"], 'wareHouseDescription' => $val["wareHouseDescription"], 'SalesPersonAddress' => $val["SalesPersonAddress"], 'salesPersonCurrency' => $val["salesPersonCurrency"], 'salesPersonTargetType' => $val["salesPersonTargetType"], 'salesPersonTarget' => $val["salesPersonTarget"], 'salesPersonCurrencyDecimalPlaces' => $val["salesPersonCurrencyDecimalPlaces"], 'salesPersonCurrency' => $val["salesPersonCurrency"]);
                    $value[$val["SalesPersonCode"] . '-' . $val['SalesPersonName']]['invoice'][] = $val["invoiceAutoID"] == NULL ? array() : $val;
                }
            }
        }

        foreach ($value as $val) {
            foreach ($sales_target as $val2) {
                if ($val['salesperson']["salesPersonID"] == $val2['salesPersonID']) {
                    $value[$val['salesperson']["SalesPersonCode"] . '-' . $val['salesperson']['SalesPersonName']]['salestarget'][] = $val2;
                }
            }
        }

        if (!empty($sales_target)) {
            foreach ($sales_target as $val2) {
                $valueSales[$val2["salesPersonID"]][] = array('percentage' => $val2["percentage"], 'fromTargetAmount' => $val2["fromTargetAmount"], 'toTargetAmount' => $val2["toTargetAmount"]);
            }
        }

        $data['invoice'] = $value;
        $data['sales_target'] = $valueSales;

        return $data;

    }

    function fetch_detail_header_lock()
    {
        $this->db->select('salesCommisionID');
        $this->db->from('srp_erp_salescommisiondetail');
        $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID')));
        return $this->db->get()->row_array();
    }

    function fetch_inv_detail($salesCommisionID)
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('*');
        $this->db->from('srp_erp_salescommisionmaster');
        $this->db->where('salesCommisionID', $salesCommisionID);
        $this->db->where('companyID', $companyID);
        $data['header'] = $this->db->get()->row_array();

        $this->db->select('srp_erp_customerinvoicemaster.salesPersonID,srp_erp_salespersontarget.*,DATE_FORMAT(datefrom,\'' . $convertFormat . '\') AS datefrom,DATE_FORMAT(dateTo,\'' . $convertFormat . '\') AS dateTo');
        $this->db->from('srp_erp_customerinvoicemaster');
        $this->db->group_by("targetID");
        $this->db->where('srp_erp_customerinvoicemaster.salesPersonID !=', null);
        $this->db->where('srp_erp_customerinvoicemaster.salesPersonID !=', '');
        $this->db->where('approvedYN', '1');
        $this->db->join('srp_erp_salespersonmaster', 'srp_erp_salespersonmaster.salesPersonID=srp_erp_customerinvoicemaster.salesPersonID');
        $this->db->join('srp_erp_salespersontarget', 'srp_erp_salespersontarget.salesPersonID=srp_erp_customerinvoicemaster.salesPersonID');
        $sales_target = $this->db->get()->result_array();

        $invoice = $this->db->query("SELECT srp_erp_salescommisionperson.adjustment,if(srp_erp_salescommisiondetail.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID,1,0) as checked,srp_erp_salescommisionperson.`salesPersonID`,srp_erp_customerinvoicemaster.`invoiceAutoID`, `srp_erp_customerinvoicemaster`.`invoiceCode`, `srp_erp_customerinvoicemaster`.`invoiceDate`, `srp_erp_customerinvoicemaster`.`invoiceNarration`, `srp_erp_customerinvoicemaster`.`customerName`, `srp_erp_customerinvoicemaster`.`companyLocalCurrencyDecimalPlaces`, `srp_erp_customerinvoicemaster`.`companyLocalAmount`, `srp_erp_customerinvoicemaster`.`companyLocalCurrency`,srp_erp_salespersonmaster.SalesPersonCode,SalesPersonName,srp_erp_salescommisionperson.percentage 
FROM srp_erp_salescommisionperson 
INNER JOIN srp_erp_salespersonmaster ON srp_erp_salespersonmaster.salesPersonID=srp_erp_salescommisionperson.salesPersonID  
LEFT JOIN (SELECT * FROM srp_erp_customerinvoicemaster WHERE NOT EXISTS (SELECT * FROM srp_erp_salescommisiondetail WHERE srp_erp_salescommisiondetail.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID AND salesCommisionID != $salesCommisionID)  AND `invoiceDate` <= '" . $data['header']['asOfDate'] . "' AND  srp_erp_customerinvoicemaster.`salesPersonID` IS NOT NULL AND srp_erp_customerinvoicemaster.`salesPersonID` != '' AND srp_erp_customerinvoicemaster.`approvedYN` = '1' AND srp_erp_customerinvoicemaster.`companyID` = '{$companyID}') as srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.salesPersonID = srp_erp_salescommisionperson.salesPersonID
LEFT JOIN (SELECT * FROM srp_erp_salescommisiondetail WHERE companyID = $companyID AND salesCommisionID = $salesCommisionID) as srp_erp_salescommisiondetail ON srp_erp_salescommisiondetail.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID WHERE srp_erp_salescommisionperson.salesCommisionID = $salesCommisionID GROUP BY
	srp_erp_salescommisionperson.`salesPersonID`,
	srp_erp_customerinvoicemaster.`invoiceAutoID`")->result_array();
        //echo $this->db->last_query();
        $value = array();
        $valueSales = array();
        if (!empty($invoice)) {
            foreach ($invoice as $val) {
                if ($val["invoiceAutoID"] == NULL) {
                    $value[$val["SalesPersonCode"] . '-' . $val['SalesPersonName']]['salesperson'] = array('SalesPersonCode' => $val["SalesPersonCode"], 'SalesPersonName' => $val["SalesPersonName"], 'salesPersonID' => $val["salesPersonID"], 'adjustment' => $val["adjustment"], 'percentage' => $val["percentage"]);
                    $value[$val["SalesPersonCode"] . '-' . $val['SalesPersonName']]['invoice'] = array();
                } else {
                    $value[$val["SalesPersonCode"] . '-' . $val['SalesPersonName']]['salesperson'] = $val["invoiceAutoID"] == NULL ? array() : array('SalesPersonCode' => $val["SalesPersonCode"], 'SalesPersonName' => $val["SalesPersonName"], 'salesPersonID' => $val["salesPersonID"], 'adjustment' => $val["adjustment"], 'percentage' => $val["percentage"]);
                    $value[$val["SalesPersonCode"] . '-' . $val['SalesPersonName']]['invoice'][] = $val["invoiceAutoID"] == NULL ? array() : $val;
                }
            }
        }

        foreach ($value as $val) {
            foreach ($sales_target as $val2) {
                if ($val['salesperson']["salesPersonID"] == $val2['salesPersonID']) {
                    $value[$val['salesperson']["SalesPersonCode"] . '-' . $val['salesperson']['SalesPersonName']]['salestarget'][] = $val2;
                }
            }
        }

        if (!empty($sales_target)) {
            foreach ($sales_target as $val2) {
                $valueSales[$val2["salesPersonID"]][] = array('percentage' => $val2["percentage"], 'fromTargetAmount' => $val2["fromTargetAmount"], 'toTargetAmount' => $val2["toTargetAmount"]);
            }
        }

        $data['invoice'] = $value;
        $data['sales_target'] = $valueSales;
        return $data;
    }

    function sales_commission_detail()
    {
        $this->db->trans_start();
        $companyID = $this->common_data['company_data']['company_id'];
        $salesCommisionID = trim($this->input->post('salesCommisionID'));
        /*$this->db->select('*');
        $this->db->from('srp_erp_salescommisionmaster');
        $this->db->where('salesCommisionID',$salesCommisionID);
        $this->db->where('companyID',$companyID);
        $header = $this->db->get()->row_array();*/

        $sales_person_arr = array();
        $invoices = $this->input->post('isActive');
        if (!empty($invoices)) {
            foreach ($invoices as $key => $invoice) {
                $invoices_arr = explode('|', $invoices[$key]);
                $data[$key]['salesCommisionID'] = $salesCommisionID;
                $data[$key]['salesPersonID'] = trim($invoices_arr[1]);
                $data[$key]['invoiceAutoID'] = trim($invoices_arr[0]);
                $data[$key]['transactionAmount'] = trim($invoices_arr[2]);
                $data[$key]['companyID'] = $companyID;
                array_push($sales_person_arr, $data[$key]['salesPersonID']);
            }

            $this->db->delete('srp_erp_salescommisiondetail', array('salesCommisionID' => $salesCommisionID));
            if (!empty($data)) {
                $this->db->insert_batch('srp_erp_salescommisiondetail', $data);
            }
        } else {
            $this->db->delete('srp_erp_salescommisiondetail', array('salesCommisionID' => $salesCommisionID));
        }

        $sales_commission_detail_arr = array();
        $this->db->select('commisionSalesPersonID,salesPersonID');
        /* $this->db->where_in('salesPersonID', $sales_person_arr);*/
        $this->db->where('salesCommisionID', $salesCommisionID);
        $this->db->from('srp_erp_salescommisionperson');
        $sales_person_arr = $this->db->get()->result_array();
        for ($i = 0; $i < count($sales_person_arr); $i++) {
            $sales_commission_detail_arr[$i]['salesPersonID'] = $sales_person_arr[$i]['salesPersonID'];
            $sales_commission_detail_arr[$i]['commisionSalesPersonID'] = $sales_person_arr[$i]['commisionSalesPersonID'];
            $sales_commission_detail_arr[$i]['adjustment'] = $this->input->post('adjustment_' . $sales_person_arr[$i]['salesPersonID']);
            $sales_commission_detail_arr[$i]['description'] = $this->input->post('description_' . $sales_person_arr[$i]['salesPersonID']);
            $sales_commission_detail_arr[$i]['invoiceTotal'] = $this->input->post('invoice_total_' . $sales_person_arr[$i]['salesPersonID']);
            $sales_commission_detail_arr[$i]['percentage'] = $this->input->post('percentage_' . $sales_person_arr[$i]['salesPersonID']);
            $sales_commission_detail_arr[$i]['netCommision'] = ((($sales_commission_detail_arr[$i]['invoiceTotal'] / 100) * $sales_commission_detail_arr[$i]['percentage']) + $sales_commission_detail_arr[$i]['adjustment']);
        }

        if (!empty($sales_commission_detail_arr)) {
            $this->db->update_batch('srp_erp_salescommisionperson', $sales_commission_detail_arr, 'commisionSalesPersonID');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('status' => 0, 'type' => 'e', 'message' => 'Sales commision Failed.');
        } else {
            $this->db->trans_commit();
            return array('status' => 1, 'type' => 's', 'message' => 'Sales commision created Successfully.', 'last_id' => $salesCommisionID);
        }
    }

    function save_sales_target()
    {
        $this->db->trans_start();
        /*$date_format_policy = date_format_policy();
        $date_f = $this->input->post('datefrom');
        $datefrom = input_format_date($date_f,$date_format_policy);
        $date_t = $this->input->post('dateTo');
        $dateto = input_format_date($date_t,$date_format_policy);*/
        $fromTargetAmount = trim($this->input->post('fromTargetAmount'));
        $toTargetAmount = trim($this->input->post('toTargetAmount'));
        $salesPersonID = trim($this->input->post('salesPersonID'));

        $data['salesPersonID'] = trim($this->input->post('salesPersonID'));
        /* $data['datefrom'] = $datefrom;
         $data['dateTo'] = $dateto;*/
        $data['currencyID'] = trim($this->input->post('currencyID'));
        $data['fromTargetAmount'] = $fromTargetAmount;
        $data['toTargetAmount'] = $toTargetAmount;
        $data['percentage'] = trim($this->input->post('percentage'));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        if (trim($this->input->post('targetID'))) {

            $sale_target2 = $this->db->query("SELECT
   max(toTargetAmount) as toTargetAmount,MAX(targetID) as targetID
FROM
   `srp_erp_salespersontarget`
WHERE
   `salesPersonID` = $salesPersonID")->row_array();
            if (!empty($sale_target2)) {
                if(($sale_target2["targetID"] != trim($this->input->post('targetID'))) && ($toTargetAmount > $sale_target2["toTargetAmount"]))
                return array('status' => 0, 'type' => 'w', 'message' => 'Invalid sales target range.');
            }

            $sale_target2 = $this->db->query("SELECT
   salesPersonID
FROM
   `srp_erp_salespersontarget`
WHERE
   `salesPersonID` = $salesPersonID
AND `targetID` != " . $this->input->post('targetID') . "
AND (($fromTargetAmount BETWEEN fromTargetAmount
AND toTargetAmount)
or ($toTargetAmount BETWEEN fromTargetAmount
AND toTargetAmount))")->row_array();
            if (!empty($sale_target2)) {
                return array('status' => 0, 'type' => 'w', 'message' => 'Sales target already exists for selected Range.');
            }

            $this->db->where('targetID', trim($this->input->post('targetID')));
            $this->db->update('srp_erp_salespersontarget', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => 0, 'type' => 'e', 'message' => 'Sales Target Record Update Failed.');
            } else {
                $this->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'message' => 'Sales Target Record Updated Successfully.', 'last_id' => trim($this->input->post('targetID')));
            }
        } else {
            $sale_target2 = $this->db->query("SELECT
   salesPersonID
FROM
   `srp_erp_salespersontarget`
WHERE
   `salesPersonID` = $salesPersonID
AND (($fromTargetAmount BETWEEN fromTargetAmount
AND toTargetAmount)
or ($toTargetAmount BETWEEN fromTargetAmount
AND toTargetAmount))")->row_array();
            if (!empty($sale_target2)) {
                return array('status' => 0, 'type' => 'w', 'message' => 'Sales target already exists for selected Range.');
            }

            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_salespersontarget', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => 0, 'type' => 'e', 'message' => 'Sales Target Record Save Failed.');

            } else {
                $this->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'message' => 'Sales Target Record Saved Successfully.', 'last_id' => $last_id);
            }
        }
    }

    function delete_sc()
    {
        /*$salesCommisionID = trim($this->input->post('salesCommisionID'));
        $this->db->delete('srp_erp_salescommisiondetail', array('salesCommisionID' => $salesCommisionID));
        $this->db->delete('srp_erp_salescommisionperson', array('salesCommisionID' => $salesCommisionID));
        $this->db->delete('srp_erp_salescommisionmaster', array('salesCommisionID' => $salesCommisionID));*/
        $this->db->select('*');
        $this->db->from('srp_erp_salescommisiondetail');
        $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID')));
        $datas= $this->db->get()->row_array();
        if($datas) {
            return array('status' => 1, 'type' => 'e', 'message' => 'please delete all detail records before delete this document.');
        }else{
            $data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID')));
            $this->db->update('srp_erp_salescommisionmaster', $data);
            return array('status' => 1, 'type' => 's', 'message' => 'Sales commision Deleted Successfully.');
        }


    }

    function save_sales_person()
    {
        if (!trim($this->input->post('salesPersonID')) and trim($this->input->post('EIdNo'))) {
            $this->db->select('salesPersonID,SalesPersonName,SalesPersonCode');
            $this->db->from('srp_erp_salespersonmaster');
            $this->db->where('EIdNo', trim($this->input->post('EIdNo')));
            //$this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('status' => 0, 'type' => 'w', 'message' => 'Sales Person : ' . $order_detail['SalesPersonCode'] . ' ' . $order_detail['SalesPersonName'] . '  already exists.', 'last_id' => 1);
            }
        }
        $this->db->trans_start();
        $isactive = 0;
        if (!empty($this->input->post('isActive'))) {
            $isactive = 1;
        }
        $sate = ' Save';
        $segment = explode('|', trim($this->input->post('segmentID')));
        $delivery_location = explode('|', trim($this->input->post('delivery_location')));
        $liability = fetch_gl_account_desc(trim($this->input->post('receivableAutoID')));
        $expanse = fetch_gl_account_desc(trim($this->input->post('expanseAutoID')));
        $currency_code = explode('|', trim($this->input->post('currency_code')));
        $data['SalesPersonName'] = trim($this->input->post('SalesPersonName'));
        $data['EIdNo'] = trim($this->input->post('EIdNo'));
        $data['segmentID'] = trim($segment[0]);
        $data['segmentCode'] = trim($segment[1]);
        $data['SecondaryCode'] = trim($this->input->post('SecondaryCode'));
        $data['SalesPersonEmail'] = trim($this->input->post('SalesPersonEmail'));
        $data['contactNumber'] = trim($this->input->post('contactNumber'));
        $data['wareHouseAutoID'] = trim($this->input->post('wareHouseAutoID'));
        $data['SalesPersonAddress'] = trim($this->input->post('SalesPersonAddress'));
        $data['wareHouseCode'] = trim($delivery_location[0]);
        $data['wareHouseLocation'] = trim($delivery_location[1]);
        $data['wareHouseDescription'] = trim($delivery_location[2]);
        $data['salesPersonTargetType'] = trim($this->input->post('salesPersonTargetType'));
        $data['salesPersonTarget'] = trim($this->input->post('salesPersonTarget'));
        $data['receivableAutoID'] = $liability['GLAutoID'];
        $data['receivableSystemGLCode'] = $liability['systemAccountCode'];
        $data['receivableGLAccount'] = $liability['GLSecondaryCode'];
        $data['receivableDescription'] = $liability['GLDescription'];
        $data['receivableType'] = $liability['subCategory'];
        $data['expanseAutoID'] = $expanse['GLAutoID'];
        $data['expanseSystemGLCode'] = $expanse['systemAccountCode'];
        $data['expanseGLAccount'] = $expanse['GLSecondaryCode'];
        $data['expanseDescription'] = $expanse['GLDescription'];
        $data['expanseType'] = $expanse['subCategory'];
        $data['isActive'] = $isactive;
        $data['salesPersonCurrencyID'] = trim($this->input->post('salesPersonCurrencyID'));
        $data['salesPersonCurrency'] = $currency_code[0];
        $data['salesPersonCurrencyDecimalPlaces'] = fetch_currency_desimal($data['salesPersonCurrency']);
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('salesPersonID'))) {
            $this->db->where('salesPersonID', trim($this->input->post('salesPersonID')));
            $this->db->update('srp_erp_salespersonmaster', $data);
            $sate = ' Update';
            $last_id = trim($this->input->post('salesPersonID'));
        } else {
            $this->load->library('sequence');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['SalesPersonCode'] = $this->sequence->sequence_generator('REP');
            $data['salesPersonImage'] = 'images/users/default.gif';
            $this->db->insert('srp_erp_salespersonmaster', $data);
            $last_id = $this->db->insert_id();
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('status' => 0, 'type' => 'e', 'message' => 'Sales person  ' . $data['SalesPersonName'] . $sate . ' Update Failed.', 'last_id' => $last_id);
        } else {
            $this->db->trans_commit();
            return array('status' => 1, 'type' => 's', 'message' => 'Sales person  ' . $data['SalesPersonName'] . $sate . ' Updated Successfully.', 'last_id' => $last_id);
        }
    }

    function sc_confirmation()
    {
        $salesCommisionID = trim($this->input->post('salesCommisionID'));
        $this->db->select('salesCommisionID');
        $this->db->from('srp_erp_salescommisiondetail');
        $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID')));
        $result = $this->db->get()->result_array();
        if ($result) {
            $this->load->library('approvals');
            $this->db->select('salesCommisionID, salesCommisionCode');
            $this->db->where('salesCommisionID', $salesCommisionID);
            $this->db->from('srp_erp_salescommisionmaster');
            $sc_data = $this->db->get()->row_array();
            $approvals_status = $this->approvals->CreateApproval('SC', $sc_data['salesCommisionID'], $sc_data['salesCommisionCode'], 'Sales commision', 'srp_erp_salescommisionmaster', 'salesCommisionID');
            if ($approvals_status == 1) {
                $data = array(
                    'confirmedYN' => 1,
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                    'confirmedByName' => $this->common_data['current_user']
                );
                $this->db->where('salesCommisionID', $salesCommisionID);
                $this->db->update('srp_erp_salescommisionmaster', $data);
                return true;
            } else {
                return false;
            }
        } else {
            return array('status' => 0, 'type' => 'e', 'message' => 'There are no records to confirm this document!');
        }
    }

    function save_sc_approval()
    {
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_code = trim($this->input->post('salesCommisionID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        $comments = trim($this->input->post('comments'));

        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'SC');
        if ($approvals_status == 1) {
            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_SC($system_code, 'SC');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['salesCommisionID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['salesCommisionCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['asOfDate'];
                $generalledger_arr[$i]['documentType'] = '';
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['asOfDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['asOfDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['Description'];
                $generalledger_arr[$i]['chequeNumber'] = $double_entry['master_data']['referenceNo'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                    $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                }
                $generalledger_arr[$i]['partyCurrencyAmount'] = round(($amount /  $double_entry['gl_detail'][$i]['partyExchangeRate']), $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                //$generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['partyExchangeRate']),$generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
            }

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'Sales Commission Approval Successfully.');
            return true;
        }
    }

    function re_open_salescommishion(){
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('salesCommisionID', trim($this->input->post('salesCommisionID')));
        $this->db->update('srp_erp_salescommisionmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function get_sales_order_report(){
        $convertFormat = convert_date_format_sql();
        $customerID = $this->input->post('customerID');
        $search = $this->input->post('search');
        if($search){
            $search = " AND contractCode LIKE '%".$search."%' OR srp_erp_customermaster.customerName LIKE '%".$search."%' OR invoiceAmount LIKE '%".$search."%' OR receiptAmount LIKE '%".$search."%' OR transactionAmount LIKE '%".$search."%'";
        }else{
            $search = "";
        }
        $qry = "SELECT DATE_FORMAT(contractDate,'" . $convertFormat . "') as documentDate, srp_erp_contractmaster.transactionAmount, a.invoiceAmount, a.nonTaxAmount, a.receiptAmount, transactionCurrency, transactionCurrencyDecimalPlaces, srp_erp_customermaster.customerName, contractCode, srp_erp_contractmaster.contractAutoID,srp_erp_contractmaster.documentID FROM srp_erp_contractmaster LEFT JOIN ( SELECT SUM(ab.invoiceAmount) AS invoiceAmount, SUM(ab.nonTaxAmount) AS nonTaxAmount, SUM(ab.receiptAmount) AS receiptAmount, ab.contractAutoID FROM ( SELECT ( IFNULL( ( (tax.taxPercentage / 100) * SUM(civ.transactionAmount) ), 0 ) + SUM(civ.transactionAmount) ) AS invoiceAmount, SUM(civ.transactionAmount) AS nonTaxAmount, SUM(crv.transactionAmount) AS receiptAmount, tax.taxPercentage, contractAutoID, civ.invoiceAutoID FROM srp_erp_customerinvoicemaster LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, contractAutoID, invoiceAutoID FROM srp_erp_customerinvoicedetails WHERE companyID = ".current_companyID()." GROUP BY contractAutoID, invoiceAutoID ) civ ON srp_erp_customerinvoicemaster.invoiceAutoID = civ.invoiceAutoID LEFT JOIN ( SELECT SUM(IFNULL(taxPercentage, 0)) AS taxPercentage, invoiceAutoID FROM srp_erp_customerinvoicetaxdetails WHERE companyID = ".current_companyID()." GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = civ.invoiceAutoID LEFT JOIN ( SELECT SUM( srp_erp_customerreceiptdetail.transactionAmount ) AS transactionAmount, invoiceAutoID FROM srp_erp_customerreceiptdetail LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId WHERE srp_erp_customerreceiptdetail.companyID = ".current_companyID()." AND approvedYN = 1 GROUP BY invoiceAutoID ) crv ON crv.invoiceAutoID = civ.invoiceAutoID WHERE srp_erp_customerinvoicemaster.approvedYN = 1 AND contractAutoID IS NOT NULL GROUP BY contractAutoID, civ.invoiceAutoID ) ab GROUP BY ab.contractAutoID ) a ON a.contractAutoID = srp_erp_contractmaster.contractAutoID LEFT JOIN srp_erp_customermaster ON customerAutoID = customerID WHERE srp_erp_contractmaster.companyID = ".current_companyID()." AND srp_erp_contractmaster.customerID IN (".join(',',$customerID).") AND srp_erp_contractmaster.documentID = 'SO' AND approvedYN = 1 $search GROUP BY srp_erp_contractmaster.contractAutoID ORDER BY transactionCurrency,contractDate";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function get_group_sales_order_report(){
        $company = $this->get_group_company();
        $convertFormat = convert_date_format_sql();
        $customerID = $this->input->post('customerID');
        $search = $this->input->post('search');
        if($search){
            $search = " AND contractCode LIKE '%".$search."%'";
        }else{
            $search = "";
        }
        $qry = "SELECT DATE_FORMAT(contractDate,'" . $convertFormat . "') as documentDate, srp_erp_contractmaster.transactionAmount, a.invoiceAmount, a.nonTaxAmount, a.receiptAmount, transactionCurrency, transactionCurrencyDecimalPlaces, cust.groupCustomerName as customerName, contractCode, srp_erp_contractmaster.contractAutoID,srp_erp_contractmaster.documentID FROM srp_erp_contractmaster LEFT JOIN ( SELECT SUM(ab.invoiceAmount) AS invoiceAmount, SUM(ab.nonTaxAmount) AS nonTaxAmount, SUM(ab.receiptAmount) AS receiptAmount, ab.contractAutoID 
        FROM ( SELECT ( IFNULL( ( (tax.taxPercentage / 100) * SUM(civ.transactionAmount) ), 0 ) + SUM(civ.transactionAmount) ) AS invoiceAmount, SUM(civ.transactionAmount) AS nonTaxAmount, SUM(crv.transactionAmount) AS receiptAmount, tax.taxPercentage, contractAutoID, civ.invoiceAutoID FROM srp_erp_customerinvoicemaster 
        LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, contractAutoID, invoiceAutoID FROM srp_erp_customerinvoicedetails WHERE companyID IN (".join(',',$company).") GROUP BY contractAutoID, invoiceAutoID ) civ ON srp_erp_customerinvoicemaster.invoiceAutoID = civ.invoiceAutoID
         LEFT JOIN ( SELECT SUM(IFNULL(taxPercentage, 0)) AS taxPercentage, invoiceAutoID FROM srp_erp_customerinvoicetaxdetails WHERE companyID IN (".join(',',$company).") GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = civ.invoiceAutoID 
         LEFT JOIN ( SELECT SUM( srp_erp_customerreceiptdetail.transactionAmount ) AS transactionAmount, invoiceAutoID FROM srp_erp_customerreceiptdetail 
         LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
         WHERE srp_erp_customerreceiptdetail.companyID IN (".join(',',$company).") AND approvedYN = 1 GROUP BY invoiceAutoID ) crv ON crv.invoiceAutoID = civ.invoiceAutoID 
         WHERE srp_erp_customerinvoicemaster.approvedYN = 1 AND contractAutoID IS NOT NULL GROUP BY contractAutoID, civ.invoiceAutoID ) ab GROUP BY ab.contractAutoID ) a ON a.contractAutoID = srp_erp_contractmaster.contractAutoID 
         INNER JOIN (SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . " AND groupCustomerAutoID IN (".join(',',$customerID).")) cust ON cust.customerMasterID = customerID 
         WHERE srp_erp_contractmaster.companyID IN (".join(',',$company).") AND srp_erp_contractmaster.documentID = 'SO' AND approvedYN = 1 $search GROUP BY srp_erp_contractmaster.contractAutoID ORDER BY transactionCurrency,contractDate";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function get_sales_order_drilldown_report(){
        $convertFormat = convert_date_format_sql();
        $customerID = $this->input->post('customerID');
        $contractAutoID = $this->input->post('autoID');
        $search = $this->input->post('search');
        $qry="";
        if($this->input->post('type') == 1){ //get invoice amount
            $qry = "SELECT ( IFNULL( ( (tax.taxPercentage / 100) * SUM(civ.transactionAmount) ), 0 ) + SUM(civ.transactionAmount) ) AS transactionAmount, DATE_FORMAT(invoiceDate,'" . $convertFormat . "') as documentDate, contractAutoID, srp_erp_customermaster.customerName, invoiceCode AS documentCode, transactionCurrency, transactionCurrencyDecimalPlaces,srp_erp_customerinvoicemaster.invoiceAutoID as autoID,documentID FROM srp_erp_customerinvoicemaster LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, contractAutoID, invoiceAutoID FROM srp_erp_customerinvoicedetails WHERE companyID = ".current_companyID()." GROUP BY contractAutoID, invoiceAutoID ) civ ON srp_erp_customerinvoicemaster.invoiceAutoID = civ.invoiceAutoID LEFT JOIN ( SELECT SUM(IFNULL(taxPercentage, 0)) AS taxPercentage, invoiceAutoID FROM srp_erp_customerinvoicetaxdetails WHERE companyID = ".current_companyID()." GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = civ.invoiceAutoID LEFT JOIN srp_erp_customermaster ON customerAutoID = customerID WHERE srp_erp_customerinvoicemaster.approvedYN = 1 AND contractAutoID = $contractAutoID AND srp_erp_customerinvoicemaster.customerID IN (".join(',',$customerID).") GROUP BY contractAutoID, civ.invoiceAutoID";
        }else{ // get receipt amount
            $invoice = "SELECT srp_erp_customerinvoicedetails.invoiceAutoID FROM srp_erp_customerinvoicedetails LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID LEFT JOIN srp_erp_customermaster ON customerAutoID = customerID WHERE approvedYN = 1 AND srp_erp_customerinvoicemaster.customerID IN (".join(',',$customerID).") AND srp_erp_customerinvoicedetails.companyID = ".current_companyID()." AND srp_erp_customerinvoicedetails.contractAutoID = $contractAutoID GROUP BY srp_erp_customerinvoicedetails.invoiceAutoID";
            $output = $this->db->query($invoice)->result_array();
            $invoiceAutoID = array_column($output,'invoiceAutoID');
            if($invoiceAutoID) {
                $qry = "SELECT DATE_FORMAT(RVdate,'" . $convertFormat . "') as documentDate,SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount,srp_erp_customermaster.customerName,RVcode as documentCode,transactionCurrency,transactionCurrencyDecimalPlaces,srp_erp_customerreceiptdetail.receiptVoucherAutoId as autoID,documentID FROM srp_erp_customerreceiptdetail LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId LEFT JOIN srp_erp_customermaster ON customerAutoID = customerID WHERE srp_erp_customerreceiptdetail.companyID = " . current_companyID() . " AND approvedYN = 1 AND srp_erp_customerreceiptmaster.customerID IN (" . join(',', $customerID) . ") AND srp_erp_customerreceiptdetail.invoiceAutoID IN (" . join(',', $invoiceAutoID) . ") GROUP BY srp_erp_customerreceiptdetail.receiptVoucherAutoId";
            }else{
                return array();
            }
        }
        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function get_group_sales_order_drilldown_report(){
        $company = $this->get_group_company();
        $convertFormat = convert_date_format_sql();
        $customerID = $this->input->post('customerID');
        $contractAutoID = $this->input->post('autoID');
        $search = $this->input->post('search');
        $qry="";
        if($this->input->post('type') == 1){ //get invoice amount
            $qry = "SELECT ( IFNULL( ( (tax.taxPercentage / 100) * SUM(civ.transactionAmount) ), 0 ) + SUM(civ.transactionAmount) ) AS transactionAmount, DATE_FORMAT(invoiceDate,'" . $convertFormat . "') as documentDate, contractAutoID, cust.groupCustomerName as customerName, invoiceCode AS documentCode, transactionCurrency, transactionCurrencyDecimalPlaces,srp_erp_customerinvoicemaster.invoiceAutoID as autoID,documentID FROM srp_erp_customerinvoicemaster 
            LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, contractAutoID, invoiceAutoID FROM srp_erp_customerinvoicedetails WHERE companyID IN (".join(',',$company).") GROUP BY contractAutoID, invoiceAutoID ) civ ON srp_erp_customerinvoicemaster.invoiceAutoID = civ.invoiceAutoID 
            LEFT JOIN ( SELECT SUM(IFNULL(taxPercentage, 0)) AS taxPercentage, invoiceAutoID FROM srp_erp_customerinvoicetaxdetails WHERE companyID IN (".join(',',$company).") GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = civ.invoiceAutoID 
            INNER JOIN (SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . " AND groupCustomerAutoID IN (".join(',',$customerID).")) cust ON cust.customerMasterID = customerID 
            WHERE srp_erp_customerinvoicemaster.approvedYN = 1 AND contractAutoID = $contractAutoID GROUP BY contractAutoID, civ.invoiceAutoID";
        }else{ // get receipt amount
            $invoice = "SELECT srp_erp_customerinvoicedetails.invoiceAutoID FROM srp_erp_customerinvoicedetails 
LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID 
INNER JOIN (SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . " AND groupCustomerAutoID IN (".join(',',$customerID).")) cust ON cust.customerMasterID = customerID 
WHERE approvedYN = 1 AND srp_erp_customerinvoicedetails.companyID IN (".join(',',$company).") AND srp_erp_customerinvoicedetails.contractAutoID = $contractAutoID GROUP BY srp_erp_customerinvoicedetails.invoiceAutoID";
            $output = $this->db->query($invoice)->result_array();
            $invoiceAutoID = array_column($output,'invoiceAutoID');
            if($invoiceAutoID) {
                $qry = "SELECT DATE_FORMAT(RVdate,'" . $convertFormat . "') as documentDate,SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount,cust.groupCustomerName as customerName,RVcode as documentCode,transactionCurrency,transactionCurrencyDecimalPlaces,srp_erp_customerreceiptdetail.receiptVoucherAutoId as autoID,documentID FROM srp_erp_customerreceiptdetail 
                LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
                INNER JOIN (SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . " AND groupCustomerAutoID IN (".join(',',$customerID).")) cust ON cust.customerMasterID = customerID 
                WHERE srp_erp_customerreceiptdetail.companyID IN (".join(',',$company).") AND approvedYN = 1 AND srp_erp_customerreceiptdetail.invoiceAutoID IN (" . join(',', $invoiceAutoID) . ") GROUP BY srp_erp_customerreceiptdetail.receiptVoucherAutoId";
            }else{
                return array();
            }
        }
        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID',current_companyID());
        $this->db->where('documentID','SC');
        $this->db->from('srp_erp_documentcodemaster ');
        return $this->db->get()->row_array();
    }

    function get_customer_invoice_report(){
        $convertFormat = convert_date_format_sql();
        $customerID = $this->input->post('customerID');
        $segmentID = $this->input->post('segmentID');
        $search = $this->input->post('search');

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 00:00:00')";
        }

        if($search){
            $search = " AND invoiceCode LIKE '%".$search."%'";
        }else{
            $search = "";
        }
        //$qry = "SELECT DATE_FORMAT(contractDate,'" . $convertFormat . "') as documentDate, srp_erp_contractmaster.transactionAmount, a.invoiceAmount, a.nonTaxAmount, a.receiptAmount, transactionCurrency, transactionCurrencyDecimalPlaces, srp_erp_customermaster.customerName, contractCode, srp_erp_contractmaster.contractAutoID,srp_erp_contractmaster.documentID FROM srp_erp_contractmaster LEFT JOIN ( SELECT SUM(ab.invoiceAmount) AS invoiceAmount, SUM(ab.nonTaxAmount) AS nonTaxAmount, SUM(ab.receiptAmount) AS receiptAmount, ab.contractAutoID FROM ( SELECT ( IFNULL( ( (tax.taxPercentage / 100) * SUM(civ.transactionAmount) ), 0 ) + SUM(civ.transactionAmount) ) AS invoiceAmount, SUM(civ.transactionAmount) AS nonTaxAmount, SUM(crv.transactionAmount) AS receiptAmount, tax.taxPercentage, contractAutoID, civ.invoiceAutoID FROM srp_erp_customerinvoicemaster LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, contractAutoID, invoiceAutoID FROM srp_erp_customerinvoicedetails WHERE companyID = ".current_companyID()." GROUP BY contractAutoID, invoiceAutoID ) civ ON srp_erp_customerinvoicemaster.invoiceAutoID = civ.invoiceAutoID LEFT JOIN ( SELECT SUM(IFNULL(taxPercentage, 0)) AS taxPercentage, invoiceAutoID FROM srp_erp_customerinvoicetaxdetails WHERE companyID = ".current_companyID()." GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = civ.invoiceAutoID LEFT JOIN ( SELECT SUM( srp_erp_customerreceiptdetail.transactionAmount ) AS transactionAmount, invoiceAutoID FROM srp_erp_customerreceiptdetail LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId WHERE srp_erp_customerreceiptdetail.companyID = ".current_companyID()." AND approvedYN = 1 GROUP BY invoiceAutoID ) crv ON crv.invoiceAutoID = civ.invoiceAutoID WHERE srp_erp_customerinvoicemaster.approvedYN = 1 AND contractAutoID IS NOT NULL GROUP BY contractAutoID, civ.invoiceAutoID ) ab GROUP BY ab.contractAutoID ) a ON a.contractAutoID = srp_erp_contractmaster.contractAutoID LEFT JOIN srp_erp_customermaster ON customerAutoID = customerID WHERE srp_erp_contractmaster.companyID = ".current_companyID()." AND srp_erp_contractmaster.customerID IN (".join(',',$customerID).") AND srp_erp_contractmaster.documentID = 'SO' AND approvedYN = 1 $search GROUP BY srp_erp_contractmaster.contractAutoID ORDER BY transactionCurrency";
        $qry = "SELECT
	`srp_erp_customerinvoicemaster`.`invoiceAutoID` AS `invoiceAutoID`,
	`srp_erp_customerinvoicemaster`.`documentID` AS `documentID`,
	`invoiceCode`,
	`invoiceNarration`,
	`srp_erp_customermaster`.`customerName` AS `customermastername`,
	`transactionCurrencyDecimalPlaces`,
	`transactionCurrency`,
	`transactionExchangeRate`,
	`companyLocalCurrency`,
	`companyLocalCurrencyDecimalPlaces`,
	companyLocalExchangeRate,
	`companyReportingCurrency`,
	`companyReportingExchangeRate`,
	`companyReportingCurrencyDecimalPlaces`,
	`confirmedYN`,
	`approvedYN`,
	srp_erp_customerinvoicemaster.segmentCode as segid,
	`srp_erp_customerinvoicemaster`.`createdUserID` AS `createdUser`,
	DATE_FORMAT(invoiceDate,'" . $convertFormat . "') AS invoiceDate,
	DATE_FORMAT(invoiceDueDate,'" . $convertFormat . "') AS invoiceDueDate,
	`invoiceType`,
	(
		(
			(
				IFNULL(addondet.taxPercentage, 0) / 100
			) * (
				(
					IFNULL(det.transactionAmount, 0) - (
						IFNULL(det.detailtaxamount, 0)
					)
				)
			)
		) + IFNULL(det.transactionAmount, 0)
	) AS total_value,
	IFNULL(detreturn.totalValue, 0) as returnAmount,
	IFNULL(creditnote.credittransactionAmount, 0) as credittransactionAmount,
	IFNULL(creditnote.creditcompanyLocalAmount, 0) as creditcompanyLocalAmount,
	IFNULL(creditnote.creditcompanyReportingAmount, 0) as creditcompanyReportingAmount,
	IFNULL(receiptdet.receipttransactionAmount, 0) as receipttransactionAmount,
	IFNULL(receiptdet.receiptcompanyLocalAmount, 0) as receiptcompanyLocalAmount,
	IFNULL(receiptdet.receiptcompanyReportingAmount, 0) as receiptcompanyReportingAmount
FROM
	`srp_erp_customerinvoicemaster`
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		sum(totalafterTax) AS detailtaxamount,
		invoiceAutoID
	FROM
		srp_erp_customerinvoicedetails
	GROUP BY
		invoiceAutoID
) det ON (
	`det`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		InvoiceAutoID
	FROM
		srp_erp_customerinvoicetaxdetails
	GROUP BY
		InvoiceAutoID
) addondet ON (
	`addondet`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(totalValue) AS totalValue,
		invoiceAutoID
	FROM
		srp_erp_salesreturndetails
		LEFT JOIN `srp_erp_salesreturnmaster` ON `srp_erp_salesreturnmaster`.`salesReturnAutoID` = `srp_erp_salesreturndetails`.`salesReturnAutoID`
	WHERE
		srp_erp_salesreturnmaster.approvedYN=1
	GROUP BY
		invoiceAutoID
) detreturn ON (
	`detreturn`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(srp_erp_creditnotedetail.transactionAmount) AS credittransactionAmount,
		SUM(srp_erp_creditnotedetail.companyLocalAmount) AS creditcompanyLocalAmount,
		SUM(srp_erp_creditnotedetail.companyReportingAmount) AS creditcompanyReportingAmount,
		invoiceAutoID
	FROM
		srp_erp_creditnotedetail
		LEFT JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID
	WHERE
	    srp_erp_creditnotemaster.approvedYN=1
		AND srp_erp_creditnotedetail.companyID=".current_companyID()."
	GROUP BY
		invoiceAutoID
) creditnote ON (
	`creditnote`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)

LEFT JOIN (
	SELECT
		SUM(srp_erp_customerreceiptdetail.transactionAmount) AS receipttransactionAmount,
		SUM(srp_erp_customerreceiptdetail.companyLocalAmount) AS receiptcompanyLocalAmount,
		SUM(srp_erp_customerreceiptdetail.companyReportingAmount) AS receiptcompanyReportingAmount,
		invoiceAutoID,
		srp_erp_customerreceiptdetail.segmentID as segment
	FROM
		srp_erp_customerreceiptdetail
		LEFT JOIN srp_erp_customerreceiptmaster  ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
	WHERE
	srp_erp_customerreceiptmaster.approvedYN = 1
	AND	type = 'Invoice'
		AND srp_erp_customerreceiptdetail.companyID=".current_companyID()."
	GROUP BY
		invoiceAutoID,
		segment
) receiptdet ON (
	`receiptdet`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_customerinvoicemaster`.`customerID`
WHERE
    srp_erp_customerinvoicemaster.companyID = ".current_companyID()." AND approvedYN=1   $date $search AND customerID IN (".join(',',$customerID).") AND srp_erp_customerinvoicemaster.segmentID IN (".join(',',$segmentID).")  ORDER BY srp_erp_customerinvoicemaster.invoiceDate ASC";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }


    function get_group_customer_invoice_report(){
        $company = $this->get_group_company();
        $convertFormat = convert_date_format_sql();
        $segment = $this->input->post('segmentID');
        $customerID = $this->input->post('customerID');
        $search = $this->input->post('search');

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 00:00:00')";
        }

        if($search){
            $search = " AND invoiceCode LIKE '%".$search."%'";
        }else{
            $search = "";
        }
        $qry = "SELECT
	`srp_erp_customerinvoicemaster`.`invoiceAutoID` AS `invoiceAutoID`,
	`srp_erp_customerinvoicemaster`.`documentID` AS `documentID`,
	`invoiceCode`,
	`invoiceNarration`,
	`cust`.`groupCustomerName` AS `customermastername`,
	`transactionCurrencyDecimalPlaces`,
	`transactionCurrency`,
	`transactionExchangeRate`,
	`companyLocalCurrency`,
	`companyLocalCurrencyDecimalPlaces`,
	companyLocalExchangeRate,
	`companyReportingCurrency`,
	`companyReportingExchangeRate`,
	`companyReportingCurrencyDecimalPlaces`,
	`confirmedYN`,
	`approvedYN`,
	seg.segmentCode as segid,
	`srp_erp_customerinvoicemaster`.`createdUserID` AS `createdUser`,
	DATE_FORMAT(invoiceDate,'" . $convertFormat . "') AS invoiceDate,
	DATE_FORMAT(invoiceDueDate,'" . $convertFormat . "') AS invoiceDueDate,
	`invoiceType`,
	(
		(
			(
				IFNULL(addondet.taxPercentage, 0) / 100
			) * (
				(
					IFNULL(det.transactionAmount, 0) - (
						IFNULL(det.detailtaxamount, 0)
					)
				)
			)
		) + IFNULL(det.transactionAmount, 0)
	) AS total_value,
	IFNULL(detreturn.totalValue, 0) as returnAmount,
	IFNULL(creditnote.credittransactionAmount, 0) as credittransactionAmount,
	IFNULL(creditnote.creditcompanyLocalAmount, 0) as creditcompanyLocalAmount,
	IFNULL(creditnote.creditcompanyReportingAmount, 0) as creditcompanyReportingAmount,
	IFNULL(receiptdet.receipttransactionAmount, 0) as receipttransactionAmount,
	IFNULL(receiptdet.receiptcompanyLocalAmount, 0) as receiptcompanyLocalAmount,
	IFNULL(receiptdet.receiptcompanyReportingAmount, 0) as receiptcompanyReportingAmount
FROM
	`srp_erp_customerinvoicemaster`
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		sum(totalafterTax) AS detailtaxamount,
		invoiceAutoID
	FROM
		srp_erp_customerinvoicedetails
	GROUP BY
		invoiceAutoID
) det ON (
	`det`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		InvoiceAutoID
	FROM
		srp_erp_customerinvoicetaxdetails
	GROUP BY
		InvoiceAutoID
) addondet ON (
	`addondet`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(totalValue) AS totalValue,
		invoiceAutoID
	FROM
		srp_erp_salesreturndetails
		LEFT JOIN `srp_erp_salesreturnmaster` ON `srp_erp_salesreturnmaster`.`salesReturnAutoID` = `srp_erp_salesreturndetails`.`salesReturnAutoID`
	WHERE
		srp_erp_salesreturnmaster.approvedYN=1
	GROUP BY
		invoiceAutoID
) detreturn ON (
	`detreturn`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(srp_erp_creditnotedetail.transactionAmount) AS credittransactionAmount,
		SUM(srp_erp_creditnotedetail.companyLocalAmount) AS creditcompanyLocalAmount,
		SUM(srp_erp_creditnotedetail.companyReportingAmount) AS creditcompanyReportingAmount,
		invoiceAutoID
	FROM
		srp_erp_creditnotedetail
		LEFT JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID
	WHERE
	    srp_erp_creditnotemaster.approvedYN=1
		AND srp_erp_creditnotedetail.companyID IN (".join(',',$company).")
	GROUP BY
		invoiceAutoID
) creditnote ON (
	`creditnote`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)

LEFT JOIN (
	SELECT
		SUM(srp_erp_customerreceiptdetail.transactionAmount) AS receipttransactionAmount,
		SUM(srp_erp_customerreceiptdetail.companyLocalAmount) AS receiptcompanyLocalAmount,
		SUM(srp_erp_customerreceiptdetail.companyReportingAmount) AS receiptcompanyReportingAmount,
		invoiceAutoID
	FROM
		srp_erp_customerreceiptdetail
		LEFT JOIN srp_erp_customerreceiptmaster  ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
	WHERE
	srp_erp_customerreceiptmaster.approvedYN = 1
	AND	type = 'Invoice'
		AND srp_erp_customerreceiptdetail.companyID IN (".join(',',$company).")
	GROUP BY
		invoiceAutoID
) receiptdet ON (
	`receiptdet`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
INNER JOIN (SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . " AND groupCustomerAutoID IN (".join(',',$customerID).")) cust ON cust.customerMasterID = `srp_erp_customerinvoicemaster`.`customerID` 
INNER JOIN ( SELECT srp_erp_groupsegmentdetails.segmentID,description,segmentCode FROM srp_erp_groupsegment INNER JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID AND groupID = " . current_companyID() . " WHERE srp_erp_groupsegment.segmentID IN(".join(',',$segment).")) seg ON srp_erp_customerinvoicemaster.segmentID = seg.segmentID
WHERE
    srp_erp_customerinvoicemaster.companyID IN (".join(',',$company).") AND approvedYN=1 $date $search  ORDER BY srp_erp_customerinvoicemaster.invoiceDate ASC";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function get_sales_order_return_drilldown_report(){
        $invoiceID=$this->input->post('invoiceAutoID');
        $qry = "SELECT
	sd.salesReturnDetailsID,
	slsm.salesReturnAutoID,
	sd.totalValue,
	slsm.transactionExchangeRate,
	slsm.companyLocalExchangeRate,
	slsm.companyReportingExchangeRate,
	slsm.transactionCurrencyDecimalPlaces,
	slsm.companyLocalCurrencyDecimalPlaces,
	slsm.companyReportingCurrencyDecimalPlaces,
	slsm.transactionCurrency,
	slsm.companyLocalCurrency,
	slsm.companyReportingCurrency,
	slsm.salesReturnCode,
	slsm.returnDate
FROM
	srp_erp_salesreturndetails sd
LEFT JOIN srp_erp_salesreturnmaster slsm ON `slsm`.`salesReturnAutoID` = `sd`.`salesReturnAutoID`
WHERE
	slsm.approvedYN = 1
AND sd.invoiceAutoID = $invoiceID
AND slsm.companyID = ".current_companyID()." ";
        $output = $this->db->query($qry)->result_array();
        return $output;

    }

    function get_group_sales_order_return_drilldown_report(){
        $company = $this->get_group_company();
        $invoiceID=$this->input->post('invoiceAutoID');
        $qry = "SELECT
	sd.salesReturnDetailsID,
	slsm.salesReturnAutoID,
	sd.totalValue,
	slsm.transactionExchangeRate,
	slsm.companyLocalExchangeRate,
	slsm.companyReportingExchangeRate,
	slsm.transactionCurrencyDecimalPlaces,
	slsm.companyLocalCurrencyDecimalPlaces,
	slsm.companyReportingCurrencyDecimalPlaces,
	slsm.transactionCurrency,
	slsm.companyLocalCurrency,
	slsm.companyReportingCurrency,
	slsm.salesReturnCode,
	slsm.returnDate
FROM
	srp_erp_salesreturndetails sd
LEFT JOIN srp_erp_salesreturnmaster slsm ON `slsm`.`salesReturnAutoID` = `sd`.`salesReturnAutoID`
WHERE
	slsm.approvedYN = 1
AND sd.invoiceAutoID = $invoiceID
AND slsm.companyID IN (".join(',',$company).")";
        $output = $this->db->query($qry)->result_array();
        return $output;

    }

    function get_sales_order_credit_drilldown_report(){
        $invoiceID=$this->input->post('invoiceAutoID');
        $qry = "SELECT
	rm.receiptVoucherAutoId as masterID,
	rm.RVcode as documentCode,
	rm.documentID as docID,
	rm.RVdate as documentDate,
	rd.transactionAmount as transactionAmount,
	rd.companyLocalAmount as companyLocalAmount,
	rd.companyReportingAmount as companyReportingAmount,
	rm.transactionCurrency as transactionCurrency,
	rm.companyLocalCurrency as companyLocalCurrency,
	rm.companyReportingCurrency as companyReportingCurrency,
	rm.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,
	rm.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces,
	rm.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces

FROM
	srp_erp_customerreceiptdetail rd
LEFT JOIN srp_erp_customerreceiptmaster rm ON rm.receiptVoucherAutoId = rd.receiptVoucherAutoId
WHERE
	rm.approvedYN = 1
AND rd.invoiceAutoID = $invoiceID
AND rd.type = 'Invoice'
AND rm.companyID = ".current_companyID()."

UNION

SELECT
	cm.creditNoteMasterAutoID as masterID,
	cm.creditNoteCode as documentCode,
	cm.documentID as docID,
	cm.creditNoteDate as documentDate,
	cd.transactionAmount as transactionAmount,
	cd.companyLocalAmount as companyLocalAmount,
	cd.companyReportingAmount as companyReportingAmount,
	cm.transactionCurrency as transactionCurrency,
	cm.companyLocalCurrency as companyLocalCurrency,
	cm.companyReportingCurrency as companyReportingCurrency,
	cm.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,
	cm.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces,
	cm.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces

FROM
	srp_erp_creditnotedetail cd
LEFT JOIN srp_erp_creditnotemaster cm ON cm.creditNoteMasterAutoID = cd.creditNoteMasterAutoID
WHERE
	cm.approvedYN = 1
AND cd.invoiceAutoID = $invoiceID
AND cm.companyID = ".current_companyID()." ";
        $output = $this->db->query($qry)->result_array();
        return $output;

    }


    function get_group_sales_order_credit_drilldown_report(){
        $company = $this->get_group_company();
        $invoiceID=$this->input->post('invoiceAutoID');
        $qry = "SELECT
	rm.receiptVoucherAutoId as masterID,
	rm.RVcode as documentCode,
	rm.documentID as docID,
	rm.RVdate as documentDate,
	rd.transactionAmount as transactionAmount,
	rd.companyLocalAmount as companyLocalAmount,
	rd.companyReportingAmount as companyReportingAmount,
	rm.transactionCurrency as transactionCurrency,
	rm.companyLocalCurrency as companyLocalCurrency,
	rm.companyReportingCurrency as companyReportingCurrency,
	rm.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,
	rm.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces,
	rm.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces

FROM
	srp_erp_customerreceiptdetail rd
LEFT JOIN srp_erp_customerreceiptmaster rm ON rm.receiptVoucherAutoId = rd.receiptVoucherAutoId
WHERE
	rm.approvedYN = 1
AND rd.invoiceAutoID = $invoiceID
AND rd.type = 'Invoice'
AND rm.companyID IN (".join(',',$company).")

UNION

SELECT
	cm.creditNoteMasterAutoID as masterID,
	cm.creditNoteCode as documentCode,
	cm.documentID as docID,
	cm.creditNoteDate as documentDate,
	cd.transactionAmount as transactionAmount,
	cd.companyLocalAmount as companyLocalAmount,
	cd.companyReportingAmount as companyReportingAmount,
	cm.transactionCurrency as transactionCurrency,
	cm.companyLocalCurrency as companyLocalCurrency,
	cm.companyReportingCurrency as companyReportingCurrency,
	cm.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,
	cm.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces,
	cm.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces

FROM
	srp_erp_creditnotedetail cd
LEFT JOIN srp_erp_creditnotemaster cm ON cm.creditNoteMasterAutoID = cd.creditNoteMasterAutoID
WHERE
	cm.approvedYN = 1
AND cd.invoiceAutoID = $invoiceID
AND cm.companyID IN (".join(',',$company).")";
        $output = $this->db->query($qry)->result_array();
        return $output;

    }


    function get_get_revenue_summery_report($datearr){
        $customerID = $this->input->post('customerID');
        $search = $this->input->post('search');
        $currency = $this->input->post('currency');
        $segment = $this->input->post('segmentID');
        $sumamount='';
        if($currency==2){
            foreach($datearr as $key => $val ){
                $sumamount .= " SUM(IF(invoiceDate='$key',total_value/companyLocalExchangeRate,0)) as '$val' ,";
            }
        }else{
            foreach($datearr as $key => $val ){
                $sumamount .= " SUM(IF(invoiceDate='$key',total_value/companyReportingExchangeRate,0)) as '$val' ,";
            }
        }

        if($search){
            $search = " AND invoiceCode LIKE '%".$search."%'";
        }else{
            $search = "";
        }

        /*$segme='';
        if($segment){
            //$segme = " AND segmentID = $segment";
            $segme = " AND srp_erp_customerinvoicemaster.segmentID IN (".join(',',$segment).")";
        }else{
            $segm = "";
        }*/
        $qry = "SELECT
    $sumamount
	customermastername,
	transactionCurrencyDecimalPlaces,
	transactionExchangeRate,
	companyLocalCurrencyDecimalPlaces,
	companyLocalExchangeRate,
	companyReportingCurrencyDecimalPlaces,
	companyReportingExchangeRate,
	customerID
FROM
	(
		SELECT
			customerID,
			`srp_erp_customermaster`.`customerName` AS `customermastername`,
			`transactionCurrencyDecimalPlaces`,
			`transactionCurrency`,
			`transactionExchangeRate`,
			`companyLocalCurrency`,
			`companyLocalCurrencyDecimalPlaces`,
			companyLocalExchangeRate,
			`companyReportingCurrency`,
			`companyReportingExchangeRate`,
			`companyReportingCurrencyDecimalPlaces`,
			DATE_FORMAT(invoiceDate, '%Y-%m') AS invoiceDate,
			(
				(
					(
						IFNULL(addondet.taxPercentage, 0) / 100
					) * (
						(
							IFNULL(det.transactionAmount, 0) - (
								IFNULL(det.detailtaxamount, 0)
							)
						)
					)
				) + IFNULL(det.transactionAmount, 0) - IFNULL(detreturn.totalValue, 0)
			) AS total_value
		FROM
			`srp_erp_customerinvoicemaster`
		LEFT JOIN (
			SELECT
				SUM(transactionAmount) AS transactionAmount,
				sum(totalafterTax) AS detailtaxamount,
				invoiceAutoID
			FROM
				srp_erp_customerinvoicedetails
			GROUP BY
				invoiceAutoID
		) det ON (
			`det`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
		)
		LEFT JOIN (
			SELECT
				SUM(taxPercentage) AS taxPercentage,
				InvoiceAutoID
			FROM
				srp_erp_customerinvoicetaxdetails
			GROUP BY
				InvoiceAutoID
		) addondet ON (
			`addondet`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
		)
		LEFT JOIN (
			SELECT
				SUM(totalValue) AS totalValue,
				invoiceAutoID
			FROM
				srp_erp_salesreturndetails
			LEFT JOIN `srp_erp_salesreturnmaster` ON `srp_erp_salesreturnmaster`.`salesReturnAutoID` = `srp_erp_salesreturndetails`.`salesReturnAutoID`
			WHERE
				srp_erp_salesreturnmaster.approvedYN = 1
			GROUP BY
				invoiceAutoID
		) detreturn ON (
			`detreturn`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
		)
		LEFT JOIN (
			SELECT
				SUM(
					srp_erp_creditnotedetail.transactionAmount
				) AS credittransactionAmount,
				SUM(
					srp_erp_creditnotedetail.companyLocalAmount
				) AS creditcompanyLocalAmount,
				SUM(
					srp_erp_creditnotedetail.companyReportingAmount
				) AS creditcompanyReportingAmount,
				invoiceAutoID
			FROM
				srp_erp_creditnotedetail
			LEFT JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID
			WHERE
				srp_erp_creditnotemaster.approvedYN = 1
			AND srp_erp_creditnotedetail.companyID = ".current_companyID()."
			GROUP BY
				invoiceAutoID
		) creditnote ON (
			`creditnote`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
		)
		LEFT JOIN (
			SELECT
				SUM(
					srp_erp_customerreceiptdetail.transactionAmount
				) AS receipttransactionAmount,
				SUM(
					srp_erp_customerreceiptdetail.companyLocalAmount
				) AS receiptcompanyLocalAmount,
				SUM(
					srp_erp_customerreceiptdetail.companyReportingAmount
				) AS receiptcompanyReportingAmount,
				invoiceAutoID
			FROM
				srp_erp_customerreceiptdetail
			LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
			WHERE
				srp_erp_customerreceiptmaster.approvedYN = 1
			AND type = 'Invoice'
			AND srp_erp_customerreceiptdetail.companyID = ".current_companyID()."
			GROUP BY
				invoiceAutoID
		) receiptdet ON (
			`receiptdet`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
		)
		LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_customerinvoicemaster`.`customerID`
		WHERE
			srp_erp_customerinvoicemaster.companyID = ".current_companyID()."
		AND approvedYN = 1
		AND customerID IN (".join(',',$customerID).")
		AND srp_erp_customerinvoicemaster.segmentID IN (".join(',',$segment).")
	) a
GROUP BY
	customerID";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function get_group_revenue_summery_report($datearr){
        $company = $this->get_group_company();
        $customerID = $this->input->post('customerID');
        $segment = $this->input->post('segmentID');
        $search = $this->input->post('search');
        $currency = $this->input->post('currency');
        $sumamount='';
        if($currency==2){
            foreach($datearr as $key => $val ){
                $sumamount .= " SUM(IF(invoiceDate='$key',total_value/companyLocalExchangeRate,0)) as '$val' ,";
            }
        }else{
            foreach($datearr as $key => $val ){
                $sumamount .= " SUM(IF(invoiceDate='$key',total_value/companyReportingExchangeRate,0)) as '$val' ,";
            }
        }

        if($search){
            $search = " AND invoiceCode LIKE '%".$search."%'";
        }else{
            $search = "";
        }
        $qry = "SELECT
    $sumamount
	customermastername,
	transactionCurrencyDecimalPlaces,
	transactionExchangeRate,
	companyLocalCurrencyDecimalPlaces,
	companyLocalExchangeRate,
	companyReportingCurrencyDecimalPlaces,
	companyReportingExchangeRate,
	segid,
	customerID
FROM
	(
		SELECT
			 cust.groupCustomerAutoID as customerID,
			`cust`.`groupCustomerName` AS `customermastername`,
			`transactionCurrencyDecimalPlaces`,
			`transactionCurrency`,
			`transactionExchangeRate`,
			`companyLocalCurrency`,
			`companyLocalCurrencyDecimalPlaces`,
			companyLocalExchangeRate,
			`companyReportingCurrency`,
			`companyReportingExchangeRate`,
			`companyReportingCurrencyDecimalPlaces`,
			seg.segmentCode as segid,
			DATE_FORMAT(invoiceDate, '%Y-%m') AS invoiceDate,
			(
				(
					(
						IFNULL(addondet.taxPercentage, 0) / 100
					) * (
						(
							IFNULL(det.transactionAmount, 0) - (
								IFNULL(det.detailtaxamount, 0)
							)
						)
					)
				) + IFNULL(det.transactionAmount, 0) - IFNULL(detreturn.totalValue, 0)
			) AS total_value
		FROM
			`srp_erp_customerinvoicemaster`
		LEFT JOIN (
			SELECT
				SUM(transactionAmount) AS transactionAmount,
				sum(totalafterTax) AS detailtaxamount,
				invoiceAutoID
			FROM
				srp_erp_customerinvoicedetails
			GROUP BY
				invoiceAutoID
		) det ON (
			`det`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
		)
		LEFT JOIN (
			SELECT
				SUM(taxPercentage) AS taxPercentage,
				InvoiceAutoID
			FROM
				srp_erp_customerinvoicetaxdetails
			GROUP BY
				InvoiceAutoID
		) addondet ON (
			`addondet`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
		)
		LEFT JOIN (
			SELECT
				SUM(totalValue) AS totalValue,
				invoiceAutoID
			FROM
				srp_erp_salesreturndetails
			LEFT JOIN `srp_erp_salesreturnmaster` ON `srp_erp_salesreturnmaster`.`salesReturnAutoID` = `srp_erp_salesreturndetails`.`salesReturnAutoID`
			WHERE
				srp_erp_salesreturnmaster.approvedYN = 1
			GROUP BY
				invoiceAutoID
		) detreturn ON (
			`detreturn`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
		)
		LEFT JOIN (
			SELECT
				SUM(
					srp_erp_creditnotedetail.transactionAmount
				) AS credittransactionAmount,
				SUM(
					srp_erp_creditnotedetail.companyLocalAmount
				) AS creditcompanyLocalAmount,
				SUM(
					srp_erp_creditnotedetail.companyReportingAmount
				) AS creditcompanyReportingAmount,
				invoiceAutoID
			FROM
				srp_erp_creditnotedetail
			LEFT JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID
			WHERE
				srp_erp_creditnotemaster.approvedYN = 1
			AND srp_erp_creditnotedetail.companyID IN (".join(',',$company).")
			GROUP BY
				invoiceAutoID
		) creditnote ON (
			`creditnote`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
		)
		LEFT JOIN (
			SELECT
				SUM(
					srp_erp_customerreceiptdetail.transactionAmount
				) AS receipttransactionAmount,
				SUM(
					srp_erp_customerreceiptdetail.companyLocalAmount
				) AS receiptcompanyLocalAmount,
				SUM(
					srp_erp_customerreceiptdetail.companyReportingAmount
				) AS receiptcompanyReportingAmount,
				invoiceAutoID
			FROM
				srp_erp_customerreceiptdetail
			LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
			WHERE
				srp_erp_customerreceiptmaster.approvedYN = 1
			AND type = 'Invoice'
			AND srp_erp_customerreceiptdetail.companyID IN (".join(',',$company).")
			GROUP BY
				invoiceAutoID
		) receiptdet ON (
			`receiptdet`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
		)
		INNER JOIN (SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . " AND groupCustomerAutoID IN (".join(',',$customerID).")) cust ON cust.customerMasterID = `srp_erp_customerinvoicemaster`.`customerID`
		INNER JOIN ( SELECT srp_erp_groupsegmentdetails.segmentID,description,segmentCode FROM srp_erp_groupsegment INNER JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID AND groupID = " . current_companyID() . " WHERE srp_erp_groupsegment.segmentID IN(".join(',',$segment).")) seg ON srp_erp_customerinvoicemaster.segmentID = seg.segmentID
		WHERE
			srp_erp_customerinvoicemaster.companyID IN (".join(',',$company).")
		AND approvedYN = 1
	) a
GROUP BY
	customerID";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }


    function get_revanue_details_drilldown_report(){
        $convertFormat = convert_date_format_sql();
        $customerID = $this->input->post('customerID');
        $datefrm=$this->input->post('date');
        $datefromconvert= $datefrm.'-01';
        $datetoconvert= $datefrm.'-31';
        $segment = $this->input->post('segmentID');

        $date = "";
        $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 00:00:00')";

        $search = "";
        //$qry = "SELECT DATE_FORMAT(contractDate,'" . $convertFormat . "') as documentDate, srp_erp_contractmaster.transactionAmount, a.invoiceAmount, a.nonTaxAmount, a.receiptAmount, transactionCurrency, transactionCurrencyDecimalPlaces, srp_erp_customermaster.customerName, contractCode, srp_erp_contractmaster.contractAutoID,srp_erp_contractmaster.documentID FROM srp_erp_contractmaster LEFT JOIN ( SELECT SUM(ab.invoiceAmount) AS invoiceAmount, SUM(ab.nonTaxAmount) AS nonTaxAmount, SUM(ab.receiptAmount) AS receiptAmount, ab.contractAutoID FROM ( SELECT ( IFNULL( ( (tax.taxPercentage / 100) * SUM(civ.transactionAmount) ), 0 ) + SUM(civ.transactionAmount) ) AS invoiceAmount, SUM(civ.transactionAmount) AS nonTaxAmount, SUM(crv.transactionAmount) AS receiptAmount, tax.taxPercentage, contractAutoID, civ.invoiceAutoID FROM srp_erp_customerinvoicemaster LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, contractAutoID, invoiceAutoID FROM srp_erp_customerinvoicedetails WHERE companyID = ".current_companyID()." GROUP BY contractAutoID, invoiceAutoID ) civ ON srp_erp_customerinvoicemaster.invoiceAutoID = civ.invoiceAutoID LEFT JOIN ( SELECT SUM(IFNULL(taxPercentage, 0)) AS taxPercentage, invoiceAutoID FROM srp_erp_customerinvoicetaxdetails WHERE companyID = ".current_companyID()." GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = civ.invoiceAutoID LEFT JOIN ( SELECT SUM( srp_erp_customerreceiptdetail.transactionAmount ) AS transactionAmount, invoiceAutoID FROM srp_erp_customerreceiptdetail LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId WHERE srp_erp_customerreceiptdetail.companyID = ".current_companyID()." AND approvedYN = 1 GROUP BY invoiceAutoID ) crv ON crv.invoiceAutoID = civ.invoiceAutoID WHERE srp_erp_customerinvoicemaster.approvedYN = 1 AND contractAutoID IS NOT NULL GROUP BY contractAutoID, civ.invoiceAutoID ) ab GROUP BY ab.contractAutoID ) a ON a.contractAutoID = srp_erp_contractmaster.contractAutoID LEFT JOIN srp_erp_customermaster ON customerAutoID = customerID WHERE srp_erp_contractmaster.companyID = ".current_companyID()." AND srp_erp_contractmaster.customerID IN (".join(',',$customerID).") AND srp_erp_contractmaster.documentID = 'SO' AND approvedYN = 1 $search GROUP BY srp_erp_contractmaster.contractAutoID ORDER BY transactionCurrency";
        $qry = "SELECT
	`srp_erp_customerinvoicemaster`.`invoiceAutoID` AS `invoiceAutoID`,
	`srp_erp_customerinvoicemaster`.`documentID` AS `documentID`,
	`invoiceCode`,
	`invoiceNarration`,
	`srp_erp_customermaster`.`customerName` AS `customermastername`,
	`transactionCurrencyDecimalPlaces`,
	`transactionCurrency`,
	`transactionExchangeRate`,
	`companyLocalCurrency`,
	`companyLocalCurrencyDecimalPlaces`,
	companyLocalExchangeRate,
	`companyReportingCurrency`,
	`companyReportingExchangeRate`,
	`companyReportingCurrencyDecimalPlaces`,
	`confirmedYN`,
	`approvedYN`,
	srp_erp_customerinvoicemaster.segmentCode as segid,
	`srp_erp_customerinvoicemaster`.`createdUserID` AS `createdUser`,
	DATE_FORMAT(invoiceDate,'" . $convertFormat . "') AS invoiceDate,
	DATE_FORMAT(invoiceDueDate,'" . $convertFormat . "') AS invoiceDueDate,
	`invoiceType`,
	(
		(
			(
				IFNULL(addondet.taxPercentage, 0) / 100
			) * (
				(
					IFNULL(det.transactionAmount, 0) - (
						IFNULL(det.detailtaxamount, 0)
					)
				)
			)
		) + IFNULL(det.transactionAmount, 0)
	) AS total_value,
	IFNULL(detreturn.totalValue, 0) as returnAmount,
	IFNULL(creditnote.credittransactionAmount, 0) as credittransactionAmount,
	IFNULL(creditnote.creditcompanyLocalAmount, 0) as creditcompanyLocalAmount,
	IFNULL(creditnote.creditcompanyReportingAmount, 0) as creditcompanyReportingAmount,
	IFNULL(receiptdet.receipttransactionAmount, 0) as receipttransactionAmount,
	IFNULL(receiptdet.receiptcompanyLocalAmount, 0) as receiptcompanyLocalAmount,
	IFNULL(receiptdet.receiptcompanyReportingAmount, 0) as receiptcompanyReportingAmount
FROM
	`srp_erp_customerinvoicemaster`
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		sum(totalafterTax) AS detailtaxamount,
		invoiceAutoID
	FROM
		srp_erp_customerinvoicedetails
	GROUP BY
		invoiceAutoID
) det ON (
	`det`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		InvoiceAutoID
	FROM
		srp_erp_customerinvoicetaxdetails
	GROUP BY
		InvoiceAutoID
) addondet ON (
	`addondet`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(totalValue) AS totalValue,
		invoiceAutoID
	FROM
		srp_erp_salesreturndetails
		LEFT JOIN `srp_erp_salesreturnmaster` ON `srp_erp_salesreturnmaster`.`salesReturnAutoID` = `srp_erp_salesreturndetails`.`salesReturnAutoID`
	WHERE
		srp_erp_salesreturnmaster.approvedYN=1
	GROUP BY
		invoiceAutoID
) detreturn ON (
	`detreturn`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(srp_erp_creditnotedetail.transactionAmount) AS credittransactionAmount,
		SUM(srp_erp_creditnotedetail.companyLocalAmount) AS creditcompanyLocalAmount,
		SUM(srp_erp_creditnotedetail.companyReportingAmount) AS creditcompanyReportingAmount,
		invoiceAutoID
	FROM
		srp_erp_creditnotedetail
		LEFT JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID
	WHERE
	    srp_erp_creditnotemaster.approvedYN=1
		AND srp_erp_creditnotedetail.companyID=".current_companyID()."
	GROUP BY
		invoiceAutoID
) creditnote ON (
	`creditnote`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)

LEFT JOIN (
	SELECT
		SUM(srp_erp_customerreceiptdetail.transactionAmount) AS receipttransactionAmount,
		SUM(srp_erp_customerreceiptdetail.companyLocalAmount) AS receiptcompanyLocalAmount,
		SUM(srp_erp_customerreceiptdetail.companyReportingAmount) AS receiptcompanyReportingAmount,
		invoiceAutoID
	FROM
		srp_erp_customerreceiptdetail
		LEFT JOIN srp_erp_customerreceiptmaster  ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
	WHERE
	srp_erp_customerreceiptmaster.approvedYN = 1
	AND	type = 'Invoice'
		AND srp_erp_customerreceiptdetail.companyID=".current_companyID()."
	GROUP BY
		invoiceAutoID
) receiptdet ON (
	`receiptdet`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_customerinvoicemaster`.`customerID`
WHERE
    srp_erp_customerinvoicemaster.companyID = ".current_companyID()." AND approvedYN=1   $date $search AND customerID = $customerID AND srp_erp_customerinvoicemaster.segmentID IN (".join(',',$segment).") ORDER BY srp_erp_customerinvoicemaster.invoiceDate ASC";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }


    function get_group_revanue_details_drilldown_report(){
        $company = $this->get_group_company();
        $convertFormat = convert_date_format_sql();
        $customerID = $this->input->post('customerID');
        $datefrm=$this->input->post('date');
        $datefromconvert= $datefrm.'-01';
        $datetoconvert= $datefrm.'-31';
        $segment = $this->input->post('segmentID');

        $date = "";
        $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 00:00:00')";

        $search = "";
        //$qry = "SELECT DATE_FORMAT(contractDate,'" . $convertFormat . "') as documentDate, srp_erp_contractmaster.transactionAmount, a.invoiceAmount, a.nonTaxAmount, a.receiptAmount, transactionCurrency, transactionCurrencyDecimalPlaces, srp_erp_customermaster.customerName, contractCode, srp_erp_contractmaster.contractAutoID,srp_erp_contractmaster.documentID FROM srp_erp_contractmaster LEFT JOIN ( SELECT SUM(ab.invoiceAmount) AS invoiceAmount, SUM(ab.nonTaxAmount) AS nonTaxAmount, SUM(ab.receiptAmount) AS receiptAmount, ab.contractAutoID FROM ( SELECT ( IFNULL( ( (tax.taxPercentage / 100) * SUM(civ.transactionAmount) ), 0 ) + SUM(civ.transactionAmount) ) AS invoiceAmount, SUM(civ.transactionAmount) AS nonTaxAmount, SUM(crv.transactionAmount) AS receiptAmount, tax.taxPercentage, contractAutoID, civ.invoiceAutoID FROM srp_erp_customerinvoicemaster LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, contractAutoID, invoiceAutoID FROM srp_erp_customerinvoicedetails WHERE companyID = ".current_companyID()." GROUP BY contractAutoID, invoiceAutoID ) civ ON srp_erp_customerinvoicemaster.invoiceAutoID = civ.invoiceAutoID LEFT JOIN ( SELECT SUM(IFNULL(taxPercentage, 0)) AS taxPercentage, invoiceAutoID FROM srp_erp_customerinvoicetaxdetails WHERE companyID = ".current_companyID()." GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = civ.invoiceAutoID LEFT JOIN ( SELECT SUM( srp_erp_customerreceiptdetail.transactionAmount ) AS transactionAmount, invoiceAutoID FROM srp_erp_customerreceiptdetail LEFT JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId WHERE srp_erp_customerreceiptdetail.companyID = ".current_companyID()." AND approvedYN = 1 GROUP BY invoiceAutoID ) crv ON crv.invoiceAutoID = civ.invoiceAutoID WHERE srp_erp_customerinvoicemaster.approvedYN = 1 AND contractAutoID IS NOT NULL GROUP BY contractAutoID, civ.invoiceAutoID ) ab GROUP BY ab.contractAutoID ) a ON a.contractAutoID = srp_erp_contractmaster.contractAutoID LEFT JOIN srp_erp_customermaster ON customerAutoID = customerID WHERE srp_erp_contractmaster.companyID = ".current_companyID()." AND srp_erp_contractmaster.customerID IN (".join(',',$customerID).") AND srp_erp_contractmaster.documentID = 'SO' AND approvedYN = 1 $search GROUP BY srp_erp_contractmaster.contractAutoID ORDER BY transactionCurrency";
        $qry = "SELECT
	`srp_erp_customerinvoicemaster`.`invoiceAutoID` AS `invoiceAutoID`,
	`srp_erp_customerinvoicemaster`.`documentID` AS `documentID`,
	`invoiceCode`,
	`invoiceNarration`,
	`cust`.`groupCustomerName` AS `customermastername`,
	`transactionCurrencyDecimalPlaces`,
	`transactionCurrency`,
	`transactionExchangeRate`,
	`companyLocalCurrency`,
	`companyLocalCurrencyDecimalPlaces`,
	companyLocalExchangeRate,
	`companyReportingCurrency`,
	`companyReportingExchangeRate`,
	`companyReportingCurrencyDecimalPlaces`,
	`confirmedYN`,
	`approvedYN`,
	seg.segmentCode as segid,
	`srp_erp_customerinvoicemaster`.`createdUserID` AS `createdUser`,
	DATE_FORMAT(invoiceDate,'" . $convertFormat . "') AS invoiceDate,
	DATE_FORMAT(invoiceDueDate,'" . $convertFormat . "') AS invoiceDueDate,
	`invoiceType`,
	(
		(
			(
				IFNULL(addondet.taxPercentage, 0) / 100
			) * (
				(
					IFNULL(det.transactionAmount, 0) - (
						IFNULL(det.detailtaxamount, 0)
					)
				)
			)
		) + IFNULL(det.transactionAmount, 0)
	) AS total_value,
	IFNULL(detreturn.totalValue, 0) as returnAmount,
	IFNULL(creditnote.credittransactionAmount, 0) as credittransactionAmount,
	IFNULL(creditnote.creditcompanyLocalAmount, 0) as creditcompanyLocalAmount,
	IFNULL(creditnote.creditcompanyReportingAmount, 0) as creditcompanyReportingAmount,
	IFNULL(receiptdet.receipttransactionAmount, 0) as receipttransactionAmount,
	IFNULL(receiptdet.receiptcompanyLocalAmount, 0) as receiptcompanyLocalAmount,
	IFNULL(receiptdet.receiptcompanyReportingAmount, 0) as receiptcompanyReportingAmount
FROM
	`srp_erp_customerinvoicemaster`
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		sum(totalafterTax) AS detailtaxamount,
		invoiceAutoID
	FROM
		srp_erp_customerinvoicedetails
	GROUP BY
		invoiceAutoID
) det ON (
	`det`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		InvoiceAutoID
	FROM
		srp_erp_customerinvoicetaxdetails
	GROUP BY
		InvoiceAutoID
) addondet ON (
	`addondet`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(totalValue) AS totalValue,
		invoiceAutoID
	FROM
		srp_erp_salesreturndetails
		LEFT JOIN `srp_erp_salesreturnmaster` ON `srp_erp_salesreturnmaster`.`salesReturnAutoID` = `srp_erp_salesreturndetails`.`salesReturnAutoID`
	WHERE
		srp_erp_salesreturnmaster.approvedYN=1
	GROUP BY
		invoiceAutoID
) detreturn ON (
	`detreturn`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(srp_erp_creditnotedetail.transactionAmount) AS credittransactionAmount,
		SUM(srp_erp_creditnotedetail.companyLocalAmount) AS creditcompanyLocalAmount,
		SUM(srp_erp_creditnotedetail.companyReportingAmount) AS creditcompanyReportingAmount,
		invoiceAutoID
	FROM
		srp_erp_creditnotedetail
		LEFT JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID
	WHERE
	    srp_erp_creditnotemaster.approvedYN=1
		AND srp_erp_creditnotedetail.companyID IN (".join(',',$company).")
	GROUP BY
		invoiceAutoID
) creditnote ON (
	`creditnote`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)

LEFT JOIN (
	SELECT
		SUM(srp_erp_customerreceiptdetail.transactionAmount) AS receipttransactionAmount,
		SUM(srp_erp_customerreceiptdetail.companyLocalAmount) AS receiptcompanyLocalAmount,
		SUM(srp_erp_customerreceiptdetail.companyReportingAmount) AS receiptcompanyReportingAmount,
		invoiceAutoID
	FROM
		srp_erp_customerreceiptdetail
		LEFT JOIN srp_erp_customerreceiptmaster  ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId
	WHERE
	srp_erp_customerreceiptmaster.approvedYN = 1
	AND	type = 'Invoice'
		AND srp_erp_customerreceiptdetail.companyID IN (".join(',',$company).")
	GROUP BY
		invoiceAutoID
) receiptdet ON (
	`receiptdet`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
INNER JOIN (SELECT groupCustomerAutoID,groupCustomerName,customerMasterID,groupcustomerSystemCode FROM srp_erp_groupcustomermaster INNER JOIN srp_erp_groupcustomerdetails ON srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID WHERE srp_erp_groupcustomerdetails.companygroupID = " . current_companyID() . " AND groupCustomerAutoID = $customerID) cust ON cust.customerMasterID = `srp_erp_customerinvoicemaster`.`customerID`
INNER JOIN ( SELECT srp_erp_groupsegmentdetails.segmentID,description,segmentCode FROM srp_erp_groupsegment INNER JOIN srp_erp_groupsegmentdetails ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID AND groupID = " . current_companyID() . " WHERE srp_erp_groupsegment.segmentID IN(".join(',',$segment).")) seg ON srp_erp_customerinvoicemaster.segmentID = seg.segmentID
WHERE
    srp_erp_customerinvoicemaster.companyID IN (".join(',',$company).") AND approvedYN=1   $date $search ORDER BY srp_erp_customerinvoicemaster.invoiceDate ASC";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }


    function get_group_company()
    {
        $this->db->select("companyID");
        $this->db->from('srp_erp_companygroupdetails');
        $this->db->where('companyGroupID', current_companyID());
        $company = $this->db->get()->result_array();
        return array_column($company, 'companyID');
    }
}