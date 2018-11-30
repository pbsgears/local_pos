<?php

/**
 * Created by PhpStorm.
 * User: Nasik
 * Date: 7/10/2018
 * Time: 9:54 AM
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fund_management_model extends ERP_Model
{

    function investment_master_details($investID, $disburseID=null){
        $convertFormat = convert_date_format_sql();

        $filter = ($disburseID != null)? ' AND detID <> '.$disburseID: '';

        $masterData = $this->db->query("SELECT t1.id AS id, t1.invTypeID AS invTypeID, description AS invDes, t1.trCurrencyID AS currencyID, 
                            invDate, DATE_FORMAT(invDate,'{$convertFormat}') AS invDateStr, trAmount, t1.trDPlace, 
                            SUM(detTB.disburseAmount) AS disbTot, FORMAT(trAmount, t1.trDPlace) AS invAmountStr,                            
                            FORMAT( (trAmount - IFNULL(SUM(detTB.disburseAmount), 0)), t1.trDPlace) AS balAmount,
                            documentCode, company_name, address, tel_no, fax_no, email_id, t1.invComCurrencyID, t1.documentCode, t1.narration
                            FROM srp_erp_fm_master t1
                            JOIN srp_erp_fm_types t2 ON t2.invTypeID=t1.invTypeID
                            JOIN srp_erp_fm_companymaster comMas ON comMas.id=t1.invCompanyID                            
                            LEFT JOIN srp_erp_fm_details AS detTB ON t1.id = detTB.invMasterID {$filter}
                            WHERE t1.id = {$investID} ")->row_array();

        $masterData = array_merge($masterData, ['CurrencyCode'=> get_currency_code($masterData['currencyID'])]);
        $masterData = array_merge($masterData, ['invDate_str2'=> format_date_dob($masterData['invDate'])]);
        return $masterData;
    }
}