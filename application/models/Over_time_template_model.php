<?php

class Over_time_template_model extends ERP_Model
{

    function save_over_time_template()
    {
        $overtimeCategoryIDs = $this->input->post('overtimeCategoryID');
        foreach ($overtimeCategoryIDs as $val) {
            $overtimeCategory = explode("|", $val);
            $data['defaultcategoryID'] = 0;
            $data['overtimeCategoryID'] = 0;
            $overtimeCategoryID = $overtimeCategory[0];
            $defaultcategoryID = $overtimeCategory[1];
            if ($defaultcategoryID == 3) {
                $data['defaultcategoryID'] = $defaultcategoryID;
                $data['inputType'] = 1;
            }
            else if ($overtimeCategoryID == 0) {
                $data['defaultcategoryID'] = $defaultcategoryID;
                $data['inputType'] = 2;
            } else if ($defaultcategoryID == 0) {
                $data['overtimeCategoryID'] = $overtimeCategoryID;
                $data['inputType'] = 1;
            }
            $data['companyID'] = current_companyID();
            $result = $this->db->insert('srp_erp_generalottemplatedetails', $data);

        }
        if ($result) {
            return array('s', 'Ot template details successfully saved');

        }

    }

    function get_ot_template_details()
    {
        $companyID = current_companyID();
        $detail = $this->db->query("SELECT
	`templatedetailID`,
	`sortOrder`,
	`overtimeCategoryID`,
	`defaultcategoryID`,
	`srp_erp_generalotdefaulttypes`.`description` AS `defultDescription`,
	`srp_erp_pay_overtimecategory`.`description` AS `categoryDescription`
FROM
	`srp_erp_generalottemplatedetails`
LEFT JOIN `srp_erp_generalotdefaulttypes` ON `srp_erp_generalotdefaulttypes`.`defaultTypeID` = `srp_erp_generalottemplatedetails`.`defaultcategoryID`
LEFT JOIN `srp_erp_pay_overtimecategory` ON `srp_erp_generalottemplatedetails`.`overtimeCategoryID` = `srp_erp_pay_overtimecategory`.`ID`
WHERE
	`srp_erp_generalottemplatedetails`.`companyID` = $companyID")->result_array();
        return $detail;
    }

    function get_ot_templates()
    {
        $companyID = current_companyID();
        $detail = $this->db->query("SELECT
	`templatedetailID`,
	`sortOrder`,
	`overtimeCategoryID`,
	`defaultcategoryID`,
	srp_erp_generalottemplatedetails.inputType,
	`srp_erp_generalotdefaulttypes`.`description` AS `defultDescription`,
	`srp_erp_pay_overtimecategory`.`description` AS `categoryDescription`
FROM
	`srp_erp_generalottemplatedetails`
LEFT JOIN `srp_erp_generalotdefaulttypes` ON `srp_erp_generalotdefaulttypes`.`defaultTypeID` = `srp_erp_generalottemplatedetails`.`defaultcategoryID`
LEFT JOIN `srp_erp_pay_overtimecategory` ON `srp_erp_generalottemplatedetails`.`overtimeCategoryID` = `srp_erp_pay_overtimecategory`.`ID`
WHERE
	`srp_erp_generalottemplatedetails`.`companyID` = $companyID ORDER BY sortOrder")->result_array();
        return $detail;
    }

    function get_ot_templates_emp_details($generalOTMasterID)
    {
        $companyID = current_companyID();
        $empDetail = $this->db->query("SELECT
   empID,srp_employeesdetails.Ename2 as empname,srp_employeesdetails.ECode as ECode,overtimeCategoryID,defaultCategoryID,templateDetailID,hourorDays,generalOTMasterID,inputType,transactionAmount
FROM
	srp_erp_generalotdetail
JOIN srp_employeesdetails ON srp_erp_generalotdetail.empID = srp_employeesdetails.EIdNo
WHERE
	companyID = $companyID AND
	generalOTMasterID = $generalOTMasterID
	ORDER  BY empID
")->result_array(); //GROUP BY empID
        return $empDetail;
    }

    function save_ot_master()
    {
        $companyID = current_companyID();
        $this->db->trans_start();
        //$documentDate = $this->input->post('documentDate');
        $currencyID = $this->input->post('currencyID');
        $description = $this->input->post('description');
        $serialNo = $this->db->query("SELECT
    max(serialNo) as serialNo
FROM
	srp_erp_generalotmaster
WHERE
	companyID = $companyID
")->row_array();
        if (empty($serialNo)) {
            $serialNo = 1;
        } else {
            $serialNo = $serialNo['serialNo'] + 1;
        }

        $this->load->library('sequence');
        $date_format_policy = date_format_policy();
        $otDate = trim($this->input->post('documentDate'));
        $documentDate = input_format_date($otDate, $date_format_policy);

        $data['description'] = $description;
        $data['currencyID'] = $currencyID;
        $data['documentDate'] = $documentDate;
        $data['documentID'] = "ATS";
        $data['serialNo'] = $serialNo;
        $data['companyID'] = current_companyID();
        $data['otCode'] = $this->sequence->sequence_generator($data['documentID']);

        $this->db->insert('srp_erp_generalotmaster', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return array('e', 'Over Time Master Save Failed');
        } else {
            return array('s', 'Over Time Master Saved Successfully');
        }
    }

    function add_employees_to_ot()
    {
        $companyID = current_companyID();
        $generalOTMasterID = $this->input->post('generalOTMasterID');
        $empID = $this->input->post('empHiddenID');

        $getOtCat = $this->db->query("SELECT * FROM srp_erp_generalottemplatedetails WHERE companyID={$companyID}")->result_array();
        $transactionCurrencyID = $this->db->query("SELECT currencyID FROM srp_erp_generalotmaster WHERE generalOTMasterID={$generalOTMasterID}")->row_array();
        if($getOtCat){
            foreach ($empID as $val) {
                foreach ($getOtCat as $value) {
                    $data = array(
                        'generalOTMasterID' => $generalOTMasterID,
                        'empID' => $val,
                        'overtimeCategoryID' => $value['overtimeCategoryID'],
                        'defaultCategoryID' => $value['defaultcategoryID'],
                        'templateDetailID' => $value['templatedetailID'],
                        'transactionCurrencyID' => $transactionCurrencyID['currencyID'],
                        'inputType' => $value['inputType'],

                        'companyID' => current_companyID(),
                        //'companyCode' => current_companyCode(),
                        'createdPCID' => current_pc(),
                        'createdUserGroup' => current_user_group(),
                        'createdUserID' => current_userID(),
                        'createdUserName' => current_employee(),
                        'createdDateTime' => current_date()
                    );

                    $result = $this->db->insert('srp_erp_generalotdetail', $data);
                }
            }
            if ($result) {
                return array('s', 'Employees successfully Added.');
            } else {
                return array('e', 'Employee Adding failed.');
            }
        }else{
            return array('e', 'Add template first.');
        }

    }

    function save_general_ot_template_frm()
    {

        //echo '<pre>'.print_r($_POST);
        $empID = $this->input->post('empID');
        $hourorDays = $this->input->post('hourorDays');
        $templateDetailID = $this->input->post('templateDetailID');
        $generalOTMasterID = $this->input->post('generalOTMasterID');
        $hours = $this->input->post('hours');
        $minuites = $this->input->post('minuites');
        $inputTypearr = $this->input->post('inputType');
        $companyID = current_companyID();
        //print_r($empID) ;exit;
        foreach ($empID as $key => $emp) {
            $generalOTdetailID = $this->db->query("SELECT generalOTdetailID FROM srp_erp_generalotdetail WHERE empID={$emp} AND templateDetailID={$templateDetailID[$key]} AND generalOTMasterID={$generalOTMasterID[$key]}")->row('generalOTdetailID');
            $detail = $this->db->query("SELECT overtimeCategoryID,defaultcategoryID,inputType FROM srp_erp_generalottemplatedetails WHERE  templatedetailID={$templateDetailID[$key]} ")->row_array();
            $transactionCurrencyID = $this->db->query("SELECT currencyID FROM srp_erp_generalotmaster WHERE  generalOTMasterID={$generalOTMasterID[$key]} ")->row_array();
            $overtimeCategoryID = $detail['overtimeCategoryID'];
            $defaultCategoryID = $detail['defaultcategoryID'];
            $inputType = $detail['inputType'];
            $transactionCurrencyID = $transactionCurrencyID['currencyID'];
            $salaCatIDs = $this->db->query("SELECT salaryCategoryID FROM srp_erp_pay_overtimecategory WHERE  ID = $overtimeCategoryID ")->row_array();
            $salaCatID = $salaCatIDs['salaryCategoryID'];
            if ($detail) {
                $result = $this->db->delete('srp_erp_generalotdetail', array('generalOTdetailID' => trim($generalOTdetailID)));
                if ($result) {
                    $data['transactionAmount'] = '';
                    $data['transactionCurrencyDecimalPlaces'] = '';
                    $data['companyLocalCurrencyID'] = '';
                    $data['companyLocalCurrency'] = '';
                    $data['companyLocalAmount'] = '';
                    $data['companyLocalExchangeRate'] = '';
                    $data['companyReportingCurrencyID'] = '';
                    $data['companyReportingCurrency'] = '';
                    $data['companyReportingExchangeRate'] = '';
                    $data['companyReportingAmount'] = '';
                    $data['companyReportingCurrencyDecimalPlaces'] = '';
                    $data['noPaynonPayrollAmount']='';
                    $data['nonPayrollSalaryCategoryID'] ='';
                    $declarationTB = '';
                    $declarationTBa='';
                    $data['salaryCategoryID'] = '';
                    //echo $defaultCategoryID;
                    if ($inputTypearr[$key] == 1) {
                        if($defaultCategoryID){
                            $caegoryID=$defaultCategoryID;
                            $formula = $this->db->query("SELECT masterTB.description,masterTB.isNonPayroll,masterTB.id AS masterID,fromulaTB.salaryCategoryID as salaryCategoryID,fromulaTB.formulaString as formula
                                 FROM srp_erp_nopaysystemtable AS masterTB
                                 INNER JOIN srp_erp_nopayformula AS fromulaTB ON fromulaTB.nopaySystemID =masterTB.id AND companyID={$companyID} AND nopaySystemID = 5 ")->row_array();
                        }else{
                            $caegoryID=$overtimeCategoryID;
                            $formula = $this->db->query("SELECT
	overTimeGroup,srp_erp_pay_overtimegroupdetails.formula as formula
FROM
	srp_employeesdetails
join srp_erp_pay_overtimegroupdetails on srp_employeesdetails.overTimeGroup = srp_erp_pay_overtimegroupdetails.groupID
WHERE
	EIdNo = $emp AND
	srp_erp_pay_overtimegroupdetails.overTimeID = $caegoryID ")->row_array();
                        }

                        if (!empty($formula['formula'])) {
                            $formu = $formula['formula'];
                        } else {
                            $formu = 0;
                        }

                        $formulaBuilder = $this->formulaBuilder_to_sql_OT($formu);
                        $formulaDecodeOT = trim($formulaBuilder['formulaDecode']);
                        $formulaDecodeOT = (!empty($formulaDecodeOT)) ? $formulaDecodeOT : '0';


                        $select_str2 = trim($formulaBuilder['select_str2']);
                        $select_str2 = (!empty($select_str2)) ? $select_str2 . ',' : '';

                        $whereInClause = trim($formulaBuilder['whereInClause']);
                        $whereInClause = (!empty($whereInClause)) ? ' IN (' . $whereInClause . ' )' : '';

                        $as = $this->db->query("SELECT calculationTB.employeeNo, (({$formulaDecodeOT } ) )AS transactionAmount, transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, round( ((" . $formulaDecodeOT . " ) / companyLocalER) , companyLocalCurrencyDecimalPlaces  )AS localAmount, companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, round( ((" . $formulaDecodeOT . " ) / companyReportingER)   , companyReportingCurrencyDecimalPlaces  )AS reportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces, seg.segmentID, seg.segmentCode FROM ( SELECT employeeNo, " . $select_str2 . "  transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces FROM srp_erp_pay_salarydeclartion AS salDec JOIN srp_erp_pay_salarycategories AS salCat ON salCat.salaryCategoryID = salDec.salaryCategoryID AND salCat.companyID ={$companyID} WHERE salDec.companyID = {$companyID}  AND employeeNo={$emp} AND salDec.salaryCategoryID  " . $whereInClause . " GROUP BY employeeNo, salDec.salaryCategoryID ) calculationTB JOIN srp_employeesdetails AS emp ON emp.EIdNo = calculationTB.employeeNo AND emp.Erp_companyID = {$companyID} JOIN srp_erp_segment seg ON seg.segmentID = emp.segmentID AND seg.companyID = {$companyID} GROUP BY employeeNo")->row_array();
                        $totalmin = $hours[$key] * 60 + $minuites[$key];
                        $minamount = $as['transactionAmount'] / 60;
                        $transactionAmount = $minamount * $totalmin;
                        $data['hourorDays'] = $totalmin;
                        $data['transactionAmount'] = round($transactionAmount, $as['transactionCurrencyDecimalPlaces']);;
                        $data['transactionCurrencyDecimalPlaces'] = $as['transactionCurrencyDecimalPlaces'];
                        $data['companyLocalCurrencyID'] = (!empty($as['companyLocalCurrencyID'])) ? $as['companyLocalCurrencyID'] : 1;
                        $data['companyLocalCurrency'] = $as['companyLocalCurrency'];
                        $data['companyLocalAmount'] = $as['localAmount'];
                        $data['companyLocalExchangeRate'] = $as['companyLocalER'];
                        $data['companyReportingCurrencyID'] = (!empty($as['companyReportingCurrencyID'])) ? $as['companyReportingCurrencyID'] : 1;
                        $data['companyReportingCurrency'] = $as['companyReportingCurrency'];
                        $data['companyReportingExchangeRate'] = $as['companyReportingER'];
                        $data['companyReportingAmount'] = $as['reportingAmount'];
                        $data['companyReportingCurrencyDecimalPlaces'] = $as['companyReportingCurrencyDecimalPlaces'];
                        $data['salaryCategoryID'] = $salaCatID;
                    } else {
                        if ($defaultCategoryID == 1) {
                            $detail_arr = $this->db->query("SELECT *, masterTB.id AS masterID,fromulaTB.salaryCategoryID as salaryCategoryID
                                 FROM srp_erp_nopaysystemtable AS masterTB
                                 INNER JOIN srp_erp_nopayformula AS fromulaTB ON fromulaTB.nopaySystemID =masterTB.id AND companyID={$companyID} AND nopaySystemID = 3 ")->row_array();

                            $detail_allowance = $this->db->query("SELECT *, masterTB.id AS masterID,fromulaTB.salaryCategoryID as salaryCategoryID
                                 FROM srp_erp_nopaysystemtable AS masterTB
                                 INNER JOIN srp_erp_nopayformula AS fromulaTB ON fromulaTB.nopaySystemID =masterTB.id AND companyID={$companyID} AND nopaySystemID = 4 ")->row_array();
                        } else if($defaultCategoryID == 2) {
                            $detail_arr = $this->db->query("SELECT *, masterTB.id AS masterID,fromulaTB.salaryCategoryID as salaryCategoryID
                                 FROM srp_erp_nopaysystemtable AS masterTB
                                 INNER JOIN srp_erp_nopayformula AS fromulaTB ON fromulaTB.nopaySystemID =masterTB.id AND companyID={$companyID} AND nopaySystemID = 1 ")->row_array();

                            $detail_allowance = $this->db->query("SELECT *, masterTB.id AS masterID,fromulaTB.salaryCategoryID as salaryCategoryID
                                 FROM srp_erp_nopaysystemtable AS masterTB
                                 INNER JOIN srp_erp_nopayformula AS fromulaTB ON fromulaTB.nopaySystemID =masterTB.id AND companyID={$companyID} AND nopaySystemID = 2 ")->row_array();
                        }else  {
                            $detail_arr = $this->db->query("SELECT *, masterTB.id AS masterID,fromulaTB.salaryCategoryID as salaryCategoryID
                                 FROM srp_erp_nopaysystemtable AS masterTB
                                 INNER JOIN srp_erp_nopayformula AS fromulaTB ON fromulaTB.nopaySystemID =masterTB.id AND companyID={$companyID} AND nopaySystemID = 5 ")->row_array();

                            $detail_allowance = $this->db->query("SELECT *, masterTB.id AS masterID,fromulaTB.salaryCategoryID as salaryCategoryID
                                 FROM srp_erp_nopaysystemtable AS masterTB
                                 INNER JOIN srp_erp_nopayformula AS fromulaTB ON fromulaTB.nopaySystemID =masterTB.id AND companyID={$companyID} AND nopaySystemID = 6 ")->row_array();
                        }

                       /* echo '<pre>';print_r($detail_arr);echo '</pre>';
                        echo '<pre>';print_r($detail_allowance);echo '</pre>';*/
                        $data_arr = array();
                        if (!empty($detail_arr['formulaString'])) {
                            $formula = trim($detail_arr['formulaString']);
                            $lastInputType = '';
                            $declarationTBa = 'srp_erp_pay_salarydeclartion';
                            if( $detail_arr['nopaySystemID'] == 1 || $detail_arr['nopaySystemID'] == 3 || $detail_arr['nopaySystemID'] == 5){
                                $declarationTBa = 'srp_erp_pay_salarydeclartion';
                            }
                            if( $detail_arr['nopaySystemID'] == 2 || $detail_arr['nopaySystemID'] == 4){
                                $declarationTBa = 'srp_erp_non_pay_salarydeclartion';
                            }
                            $formulaBuilder = $this->formulaBuilder_to_sql_OT($formula);
                            $formulaDecodeOT = $formulaBuilder['formulaDecode'];
                            $select_str2 = $formulaBuilder['select_str2'];
                            $whereInClause = $formulaBuilder['whereInClause'];
                            $as = $this->db->query("SELECT calculationTB.employeeNo, (({$formulaDecodeOT } ) )AS transactionAmount, transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, round( ((" . $formulaDecodeOT . " ) / companyLocalER) , companyLocalCurrencyDecimalPlaces  )AS localAmount, companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, round( ((" . $formulaDecodeOT . " ) / companyReportingER)   , companyReportingCurrencyDecimalPlaces  )AS reportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces, seg.segmentID, seg.segmentCode FROM ( SELECT employeeNo, " . $select_str2 . " , transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces FROM {$declarationTBa} AS salDec JOIN srp_erp_pay_salarycategories AS salCat ON salCat.salaryCategoryID = salDec.salaryCategoryID AND salCat.companyID ={$companyID} WHERE salDec.companyID = {$companyID}  AND employeeNo={$emp} AND salDec.salaryCategoryID  IN (" . $whereInClause . ") GROUP BY employeeNo, salDec.salaryCategoryID ) calculationTB JOIN srp_employeesdetails AS emp ON emp.EIdNo = calculationTB.employeeNo AND emp.Erp_companyID = {$companyID} JOIN srp_erp_segment seg ON seg.segmentID = emp.segmentID AND seg.companyID = {$companyID} GROUP BY employeeNo")->row_array();
                            $transactionAmount = $as['transactionAmount'] * $hourorDays[$key];

                            /*echo '<pre>';print_r($formulaBuilder);echo '</pre>';*/
                            if (!empty($detail_allowance['formulaString'])) {
                                $formula_allow = trim($detail_allowance['formulaString']);
                            } else {
                                $formula_allow = 0;
                            }

                            $lastInputType_allow = '';
                            $declarationTB = 'srp_erp_pay_salarydeclartion';
                            $formulaBuilder_allow = $this->formulaBuilder_to_sql_OT($formula_allow);
                            if( $detail_allowance['nopaySystemID'] == 1 || $detail_allowance['nopaySystemID'] == 3 || $detail_allowance['nopaySystemID'] == 5){
                                $declarationTB = 'srp_erp_pay_salarydeclartion';
                            }
                            if( $detail_allowance['nopaySystemID'] == 2 || $detail_allowance['nopaySystemID'] == 4){
                                $declarationTB = 'srp_erp_non_pay_salarydeclartion';
                            }

                            $formulaDecodeOT_allow = trim($formulaBuilder_allow['formulaDecode']);
                            $formulaDecodeOT_allow = (!empty($formulaDecodeOT_allow)) ? $formulaDecodeOT_allow : '0';
                            $select_str2_allow = trim($formulaBuilder_allow['select_str2']);
                            $select_str2_allow = (!empty($select_str2_allow)) ? $select_str2_allow . ',' : '';
                            $whereInClause_allow = trim($formulaBuilder_allow['whereInClause']);
                            $whereInClause_allow = (!empty($whereInClause_allow)) ? ' IN (' . $whereInClause_allow . ' )' : '';
                            $as_allow = $this->db->query("SELECT calculationTB.employeeNo, (({$formulaDecodeOT_allow } ) )AS transactionAmount, transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, round( ((" . $formulaDecodeOT_allow . " ) / companyLocalER) , companyLocalCurrencyDecimalPlaces  )AS localAmount, companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, round( ((" . $formulaDecodeOT_allow . " ) / companyReportingER)   , companyReportingCurrencyDecimalPlaces  )AS reportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces, seg.segmentID, seg.segmentCode FROM ( SELECT employeeNo, " . $select_str2_allow . " transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces FROM {$declarationTB} AS salDec JOIN srp_erp_pay_salarycategories AS salCat ON salCat.salaryCategoryID = salDec.salaryCategoryID AND salCat.companyID ={$companyID} WHERE salDec.companyID = {$companyID}  AND employeeNo={$emp} AND salDec.salaryCategoryID  " . $whereInClause_allow . " GROUP BY employeeNo, salDec.salaryCategoryID ) calculationTB JOIN srp_employeesdetails AS emp ON emp.EIdNo = calculationTB.employeeNo AND emp.Erp_companyID = {$companyID} JOIN srp_erp_segment seg ON seg.segmentID = emp.segmentID AND seg.companyID = {$companyID} GROUP BY employeeNo")->row_array();
                            //print_r($as_allow);
                            $transactionAmount_allow = $as_allow['transactionAmount'] * $hourorDays[$key];
                            //echo $transactionAmount_allow;
                            $data['hourorDays'] = $hourorDays[$key];
                            $data['transactionAmount'] = round($transactionAmount, (!empty($as['transactionCurrencyDecimalPlaces'])) ? $as['transactionCurrencyDecimalPlaces'] : 0);
                            $data['transactionCurrencyDecimalPlaces'] = (!empty($as['transactionCurrencyDecimalPlaces'])) ? $as['transactionCurrencyDecimalPlaces'] : 0;
                            $data['salaryCategoryID'] = $detail_arr['salaryCategoryID'];
                            $data['companyLocalCurrencyID'] = (!empty($as['companyLocalCurrencyID'])) ? $as['companyLocalCurrencyID'] : 0;
                            $data['companyLocalCurrency'] = (!empty($as['companyLocalCurrency'])) ? $as['companyLocalCurrency'] : 0;
                            $data['companyLocalAmount'] = (!empty($as['localAmount'])) ? $as['localAmount'] : 0;
                            $data['companyLocalExchangeRate'] = (!empty($as['companyLocalER'])) ? $as['companyLocalER'] : 0;
                            $data['companyReportingCurrencyID'] =(!empty($as['companyReportingCurrencyID'])) ? $as['companyReportingCurrencyID'] : 0;
                            $data['companyReportingCurrency'] = (!empty($as['companyReportingCurrency'])) ? $as['companyReportingCurrency'] : 0;
                            $data['companyReportingExchangeRate'] =(!empty($as['companyReportingER'])) ? $as['companyReportingER'] : 0;
                            $data['companyReportingAmount'] =(!empty($as['reportingAmount'])) ? $as['reportingAmount'] : 0;
                            $data['companyReportingCurrencyDecimalPlaces'] = $as_allow['companyReportingCurrencyDecimalPlaces'];

                            $data['noPaynonPayrollAmount'] = round($transactionAmount_allow, (!empty($as['transactionCurrencyDecimalPlaces'])) ? $as['transactionCurrencyDecimalPlaces'] : 0);
                            $data['nonPayrollSalaryCategoryID'] = $detail_allowance['salaryCategoryID'];
                        }

                        else {
                            $data['hourorDays'] = $hourorDays[$key];
                            $data['transactionAmount'] = 0;
                            $data['transactionCurrencyDecimalPlaces'] = 0;
                            $data['salaryCategoryID'] = $detail_arr['salaryCategoryID'];
                            $data['companyLocalCurrencyID'] = 0;
                            $data['companyLocalCurrency'] = 0;
                            $data['companyLocalAmount'] = 0;
                            $data['companyLocalExchangeRate'] = 0;
                            $data['companyReportingCurrencyID'] = 0;
                            $data['companyReportingCurrency'] = 0;
                            $data['companyReportingExchangeRate'] = 0;
                            $data['companyReportingAmount'] = 0;
                            $data['companyReportingCurrencyDecimalPlaces'] = 0;

                            $data['noPaynonPayrollAmount'] = 0;
                            $data['nonPayrollSalaryCategoryID'] = $detail_allowance['salaryCategoryID'];
                        }

                    }
                    $data['empID'] = $emp;
                    $data['templateDetailID'] = $templateDetailID[$key];
                    $data['generalOTMasterID'] = $generalOTMasterID[$key];

                    $data['overtimeCategoryID'] = $overtimeCategoryID;
                    $data['defaultCategoryID'] = $defaultCategoryID;
                    $data['inputType'] = $inputType;
                    $data['transactionCurrencyID'] = $transactionCurrencyID;
                    $data['companyID'] = current_companyID();

                    $results = $this->db->insert('srp_erp_generalotdetail', $data);
                }
            }
        }
        if ($results) {
            return array('s', 'Details successfully saved');
        }
    }

    function formulaBuilder_to_sql_OT($formula)
    {

        $salary_categories_arr = salary_categories(array('A', 'D'));
        $formulaText = '';
        $salaryCatID = array();
        $formulaDecode_arr = array();
        $operand_arr = operand_arr();

        $formula_arr = explode('|', $formula); // break the formula

        $n = 0;
        foreach ($formula_arr as $formula_row) {

            if (trim($formula_row) != '') {
                if (in_array($formula_row, $operand_arr)) { //validate is a operand
                    $formulaText .= $formula_row;
                    $formulaDecode_arr[] = $formula_row;
                } else {

                    $elementType = $formula_row[0];

                    if ($elementType == '_') {
                        /*** Number ***/
                        $numArr = explode('_', $formula_row);
                        $formulaText .= (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];
                        $formulaDecode_arr[] = (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];

                    } else if ($elementType == '#') {
                        /*** Salary category ***/
                        $catArr = explode('#', $formula_row);
                        $salaryCatID[$n]['ID'] = $catArr[1];

                        $keys = array_keys(array_column($salary_categories_arr, 'salaryCategoryID'), $catArr[1]);
                        $new_array = array_map(function ($k) use ($salary_categories_arr) {
                            return $salary_categories_arr[$k];
                        }, $keys);

                        $salaryDescription = (!empty($new_array[0])) ? trim($new_array[0]['salaryDescription']) : '';

                        $formulaText .= $salaryDescription;

                        $salaryDescription_arr = explode(' ', $salaryDescription);
                        $salaryDescription_arr = preg_replace("/[^a-zA-Z 0-9]+/", "", $salaryDescription_arr);
                        $salaryCatID[$n]['cat'] = implode('_', $salaryDescription_arr) . '_' . $n;
                        $formulaDecode_arr[] = 'SUM(' . $salaryCatID[$n]['cat'] . ')';
                        $n++;
                    }


                }
            }

        }

        $formulaDecode = implode(' ', $formulaDecode_arr);

        $select_str2 = '';
        $whereInClause = '';
        foreach ($salaryCatID as $key1 => $row) {
            $separator = ($key1 > 0) ? ',' : '';
            $select_str2 .= $separator . 'IF(salDec.salaryCategoryID=' . $row['ID'] . ', SUM(transactionAmount) , 0 ) AS ' . $row['cat'] . '';
            $whereInClause .= $separator . ' ' . $row['ID'];
        }

        //echo '<pre>'; print_r($select_str1); echo '</pre>';
        //echo '<pre>'; print_r($select_str2); echo '</pre>';

        return array(
            'formulaDecode' => $formulaDecode,
            'select_str2' => $select_str2,
            'whereInClause' => $whereInClause,
        );

    }

    function save_general_ot_template_sort_frm()
    {
        $sortOrder = $this->input->post('sortOrder');
        $templatedetailID = $this->input->post('templatedetailID');
        foreach ($templatedetailID as $key => $detailID) {
            $data['sortOrder'] = $sortOrder[$key];

            $this->db->where('templatedetailID', $detailID);
            $results = $this->db->update('srp_erp_generalottemplatedetails', $data);
        }
        if ($results) {
            return array('s', 'Details successfully saved');
        }
    }

    /*function comfirm_general_ot_template()
    {
        $empID = $this->input->post('empID');
        $hourorDays = $this->input->post('hourorDays');
        $templateDetailID = $this->input->post('templateDetailID');
        $generalOTMasterID = $this->input->post('generalOTMasterID');
        $hours = $this->input->post('hours');
        $minuites = $this->input->post('minuites');
        $inputTypearr = $this->input->post('inputType');
        $companyID = current_companyID();
        foreach ($empID as $key => $emp) {
            $generalOTdetailID = $this->db->query("SELECT generalOTdetailID FROM srp_erp_generalotdetail WHERE empID={$emp} AND templateDetailID={$templateDetailID[$key]} AND generalOTMasterID={$generalOTMasterID[$key]}")->row('generalOTdetailID');
            $detail = $this->db->query("SELECT overtimeCategoryID,defaultcategoryID,inputType FROM srp_erp_generalottemplatedetails WHERE  templatedetailID={$templateDetailID[$key]} ")->row_array();
            $transactionCurrencyID = $this->db->query("SELECT currencyID FROM srp_erp_generalotmaster WHERE  generalOTMasterID={$generalOTMasterID[$key]} ")->row_array();
            $overtimeCategoryID = $detail['overtimeCategoryID'];
            $defaultCategoryID = $detail['defaultcategoryID'];
            $inputType = $detail['inputType'];
            $transactionCurrencyID = $transactionCurrencyID['currencyID'];
            $salaCatIDs = $this->db->query("SELECT salaryCategoryID FROM srp_erp_pay_overtimecategory WHERE  ID = $overtimeCategoryID ")->row_array();
            $salaCatID=$salaCatIDs['salaryCategoryID'];
            if ($detail) {
                $result = $this->db->delete('srp_erp_generalotdetail', array('generalOTdetailID' => trim($generalOTdetailID)));
                if ($result) {
                    $data['transactionAmount'] = '';
                    $data['transactionCurrencyDecimalPlaces'] = '';
                    $data['companyLocalCurrencyID'] = '';
                    $data['companyLocalCurrency'] = '';
                    $data['companyLocalAmount'] = '';
                    $data['companyLocalExchangeRate'] = '';
                    $data['companyReportingCurrencyID'] = '';
                    $data['companyReportingCurrency'] = '';
                    $data['companyReportingExchangeRate'] = '';
                    $data['companyReportingAmount'] = '';
                    $data['companyReportingCurrencyDecimalPlaces'] = '';
                    $data['salaryCategoryID'] ='';
                    if ($inputTypearr[$key] == 1) {
                        $formula = $this->db->query("SELECT
	overTimeGroup,srp_erp_pay_overtimegroupdetails.formula as formula
FROM
	srp_employeesdetails
join srp_erp_pay_overtimegroupdetails on srp_employeesdetails.overTimeGroup = srp_erp_pay_overtimegroupdetails.groupID
WHERE
	EIdNo = $emp AND
	srp_erp_pay_overtimegroupdetails.overTimeID = $overtimeCategoryID ")->row_array();
                        if(!empty($formula)){
                            $formu = $formula['formula'];
                        }else{
                            $formu = 0;
                        }
                        $formulaBuilder = $this->formulaBuilder_to_sql_OT($formu);
                        $formulaDecodeOT = $formulaBuilder['formulaDecode'];
                        $select_str2 = $formulaBuilder['select_str2'];
                        $whereInClause = $formulaBuilder['whereInClause'];
                        $as = $this->db->query("SELECT calculationTB.employeeNo, (({$formulaDecodeOT } ) )AS transactionAmount, transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, round( ((" . $formulaDecodeOT . " ) / companyLocalER) , companyLocalCurrencyDecimalPlaces  )AS localAmount, companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, round( ((" . $formulaDecodeOT . " ) / companyReportingER)   , companyReportingCurrencyDecimalPlaces  )AS reportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces, seg.segmentID, seg.segmentCode FROM ( SELECT employeeNo, " . $select_str2 . " , transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces FROM srp_erp_pay_salarydeclartion AS salDec JOIN srp_erp_pay_salarycategories AS salCat ON salCat.salaryCategoryID = salDec.salaryCategoryID AND salCat.companyID ={$companyID} WHERE salDec.companyID = {$companyID}  AND employeeNo={$emp} AND salDec.salaryCategoryID  IN (" . $whereInClause . ") GROUP BY employeeNo, salDec.salaryCategoryID ) calculationTB JOIN srp_employeesdetails AS emp ON emp.EIdNo = calculationTB.employeeNo AND emp.Erp_companyID = {$companyID} JOIN srp_erp_segment seg ON seg.segmentID = emp.segmentID AND seg.companyID = {$companyID} GROUP BY employeeNo")->row_array();

                        $totalmin = $hours[$key] * 60 + $minuites[$key];
                        $minamount = $as['transactionAmount'] / 60;
                        $transactionAmount = $minamount * $totalmin;
                        $data['hourorDays'] = $totalmin;
                        $data['transactionAmount'] = round($transactionAmount, $as['transactionCurrencyDecimalPlaces']);;
                        $data['transactionCurrencyDecimalPlaces'] = $as['transactionCurrencyDecimalPlaces'];
                        $data['companyLocalCurrencyID'] = $as['companyLocalCurrencyID'];
                        $data['companyLocalCurrency'] = $as['companyLocalCurrency'];
                        $data['companyLocalAmount'] = $as['localAmount'];
                        $data['companyLocalExchangeRate'] = $as['companyLocalER'];
                        $data['companyReportingCurrencyID'] = $as['companyReportingCurrencyID'];
                        $data['companyReportingCurrency'] = $as['companyReportingCurrency'];
                        $data['companyReportingExchangeRate'] = $as['companyReportingER'];
                        $data['companyReportingAmount'] = $as['reportingAmount'];
                        $data['companyReportingCurrencyDecimalPlaces'] = $as['companyReportingCurrencyDecimalPlaces'];
                        $data['salaryCategoryID'] = $salaCatID;
                    } else {
                        if($defaultCategoryID==1){
                            $detail_arr = $this->db->query("SELECT *, masterTB.id AS masterID,fromulaTB.salaryCategoryID as salaryCategoryID
                                 FROM srp_erp_nopaysystemtable AS masterTB
                                 INNER JOIN srp_erp_nopayformula AS fromulaTB ON fromulaTB.nopaySystemID =masterTB.id AND companyID={$companyID} AND nopaySystemID = 3 ")->row_array();

                            $detail_allowance = $this->db->query("SELECT *, masterTB.id AS masterID,fromulaTB.salaryCategoryID as salaryCategoryID
                                 FROM srp_erp_nopaysystemtable AS masterTB
                                 INNER JOIN srp_erp_nopayformula AS fromulaTB ON fromulaTB.nopaySystemID =masterTB.id AND companyID={$companyID} AND nopaySystemID = 4 ")->row_array();
                        }else{
                            $detail_arr = $this->db->query("SELECT *, masterTB.id AS masterID,fromulaTB.salaryCategoryID as salaryCategoryID
                                 FROM srp_erp_nopaysystemtable AS masterTB
                                 INNER JOIN srp_erp_nopayformula AS fromulaTB ON fromulaTB.nopaySystemID =masterTB.id AND companyID={$companyID} AND nopaySystemID = 1 ")->row_array();

                            $detail_allowance = $this->db->query("SELECT *, masterTB.id AS masterID,fromulaTB.salaryCategoryID as salaryCategoryID
                                 FROM srp_erp_nopaysystemtable AS masterTB
                                 INNER JOIN srp_erp_nopayformula AS fromulaTB ON fromulaTB.nopaySystemID =masterTB.id AND companyID={$companyID} AND nopaySystemID = 2 ")->row_array();
                        }

                        if(!empty($detail_arr)){
                            $formula = trim($detail_arr['formulaString']);
                        }else{
                            $formula = 0;
                        }
                        $lastInputType = '';
                        $formulaBuilder = $this->formulaBuilder_to_sql_OT($formula);
                        $formulaDecodeOT = $formulaBuilder['formulaDecode'];
                        $select_str2 = $formulaBuilder['select_str2'];
                        $whereInClause = $formulaBuilder['whereInClause'];
                        $as = $this->db->query("SELECT calculationTB.employeeNo, (({$formulaDecodeOT } ) )AS transactionAmount, transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, round( ((" . $formulaDecodeOT . " ) / companyLocalER) , companyLocalCurrencyDecimalPlaces  )AS localAmount, companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, round( ((" . $formulaDecodeOT . " ) / companyReportingER)   , companyReportingCurrencyDecimalPlaces  )AS reportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces, seg.segmentID, seg.segmentCode FROM ( SELECT employeeNo, " . $select_str2 . " , transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces FROM srp_erp_pay_salarydeclartion AS salDec JOIN srp_erp_pay_salarycategories AS salCat ON salCat.salaryCategoryID = salDec.salaryCategoryID AND salCat.companyID ={$companyID} WHERE salDec.companyID = {$companyID}  AND employeeNo={$emp} AND salDec.salaryCategoryID  IN (" . $whereInClause . ") GROUP BY employeeNo, salDec.salaryCategoryID ) calculationTB JOIN srp_employeesdetails AS emp ON emp.EIdNo = calculationTB.employeeNo AND emp.Erp_companyID = {$companyID} JOIN srp_erp_segment seg ON seg.segmentID = emp.segmentID AND seg.companyID = {$companyID} GROUP BY employeeNo")->row_array();
                        $transactionAmount = $as['transactionAmount'] * $hourorDays[$key];

                        if(!empty($detail_allowance)){
                            $formula_allow = trim($detail_allowance['formulaString']);
                        }else{
                            $formula_allow = 0;
                        }
                        $lastInputType_allow = '';
                        $formulaBuilder_allow = $this->formulaBuilder_to_sql_OT($formula_allow);
                        $formulaDecodeOT_allow = $formulaBuilder_allow['formulaDecode'];
                        $select_str2_allow = $formulaBuilder_allow['select_str2'];
                        $whereInClause_allow = $formulaBuilder_allow['whereInClause'];
                        $as_allow = $this->db->query("SELECT calculationTB.employeeNo, (({$formulaDecodeOT_allow } ) )AS transactionAmount, transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, round( ((" . $formulaDecodeOT_allow . " ) / companyLocalER) , companyLocalCurrencyDecimalPlaces  )AS localAmount, companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, round( ((" . $formulaDecodeOT_allow . " ) / companyReportingER)   , companyReportingCurrencyDecimalPlaces  )AS reportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces, seg.segmentID, seg.segmentCode FROM ( SELECT employeeNo, " . $select_str2_allow . " , transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces FROM srp_erp_pay_salarydeclartion AS salDec JOIN srp_erp_pay_salarycategories AS salCat ON salCat.salaryCategoryID = salDec.salaryCategoryID AND salCat.companyID ={$companyID} WHERE salDec.companyID = {$companyID}  AND employeeNo={$emp} AND salDec.salaryCategoryID  IN (" . $whereInClause_allow . ") GROUP BY employeeNo, salDec.salaryCategoryID ) calculationTB JOIN srp_employeesdetails AS emp ON emp.EIdNo = calculationTB.employeeNo AND emp.Erp_companyID = {$companyID} JOIN srp_erp_segment seg ON seg.segmentID = emp.segmentID AND seg.companyID = {$companyID} GROUP BY employeeNo")->row_array();
                        $transactionAmount_allow = $as_allow['transactionAmount'] * $hourorDays[$key];

                        $data['hourorDays'] = $hourorDays[$key];
                        $data['transactionAmount'] = round($transactionAmount, $as['transactionCurrencyDecimalPlaces']);;
                        $data['transactionCurrencyDecimalPlaces'] = $as['transactionCurrencyDecimalPlaces'];
                        $data['salaryCategoryID'] = $detail_arr['salaryCategoryID'];
                        $data['companyLocalCurrencyID'] = $as['companyLocalCurrencyID'];
                        $data['companyLocalCurrency'] = $as['companyLocalCurrency'];
                        $data['companyLocalAmount'] = $as['localAmount'];
                        $data['companyLocalExchangeRate'] = $as['companyLocalER'];
                        $data['companyReportingCurrencyID'] = $as['companyReportingCurrencyID'];
                        $data['companyReportingCurrency'] = $as['companyReportingCurrency'];
                        $data['companyReportingExchangeRate'] = $as['companyReportingER'];
                        $data['companyReportingAmount'] = $as['reportingAmount'];
                        $data['companyReportingCurrencyDecimalPlaces'] = $as['companyReportingCurrencyDecimalPlaces'];

                        $data['noPaynonPayrollAmount'] = round($transactionAmount_allow, $as['transactionCurrencyDecimalPlaces']);;
                        $data['nonPayrollSalaryCategoryID'] = $detail_allowance['salaryCategoryID'];
                    }
                    $data['empID'] = $emp;
                    $data['templateDetailID'] = $templateDetailID[$key];
                    $data['generalOTMasterID'] = $generalOTMasterID[$key];

                    $data['overtimeCategoryID'] = $overtimeCategoryID;
                    $data['defaultCategoryID'] = $defaultCategoryID;
                    $data['inputType'] = $inputType;
                    $data['transactionCurrencyID'] = $transactionCurrencyID;
                    $data['companyID'] = current_companyID();

                    $results = $this->db->insert('srp_erp_generalotdetail', $data);
                }
            }
        }
        if ($results) {
            $MasterID = $this->input->post('MasterID');
            $this->load->library('approvals');
            $otcode = $this->db->query("SELECT otCode FROM srp_erp_generalotmaster WHERE generalOTMasterID={$MasterID} ")->row_array();

            $confirm = $this->db->query("SELECT confirmedYN FROM srp_erp_generalotmaster WHERE generalOTMasterID={$MasterID} AND confirmedYN=1")->row_array();
            $approvaluser = $this->db->query("SELECT approvalUserID FROM srp_erp_approvalusers WHERE documentID='ATS' AND companyID=$companyID")->row_array();
            if(!empty($approvaluser)){
                if (!empty($confirm)) {
                    return array('w', 'Document already confirmed');
                } else {
                    $datac['confirmedYN'] = 1;
                    $datac['confirmedByEmpID'] = current_userID();
                    $datac['approvedbyEmpName'] = current_user();

                    $this->db->where('generalOTMasterID', $MasterID);
                    $result = $this->db->update('srp_erp_generalotmaster', $datac);
                    if ($result) {
                        $approvals_status = $this->approvals->CreateApproval('ATS', $MasterID, $otcode['otCode'], 'General Over Time', 'srp_erp_generalotmaster', 'generalOTMasterID');
                        if ($approvals_status) {
                            return array('s', 'Document successfully confirmed');
                        } else {
                            return array('e', 'Approvals not created');
                        }

                    }
                }
            }else{
                return array('w', 'There are no users exist to perform general OT approval for this company');
            }


        }
    }*/

    function comfirm_general_ot_template()
    {
        $companyID = current_companyID();
        $MasterID = $this->input->post('MasterID');
        $this->load->library('approvals');
        $otcode = $this->db->query("SELECT otCode FROM srp_erp_generalotmaster WHERE generalOTMasterID={$MasterID} ")->row_array();

        $confirm = $this->db->query("SELECT confirmedYN FROM srp_erp_generalotmaster WHERE generalOTMasterID={$MasterID} AND confirmedYN=1")->row_array();
        $approvaluser = $this->db->query("SELECT approvalUserID FROM srp_erp_approvalusers WHERE documentID='ATS' AND companyID=$companyID")->row_array();
        if (!empty($approvaluser)) {
            if (!empty($confirm)) {
                return array('w', 'Document already confirmed');
            } else {
                $datac['confirmedYN'] = 1;
                $datac['confirmedByEmpID'] = current_userID();
                $datac['approvedbyEmpName'] = current_user();

                $this->db->where('generalOTMasterID', $MasterID);
                $result = $this->db->update('srp_erp_generalotmaster', $datac);
                if ($result) {
                    $approvals_status = $this->approvals->CreateApproval('ATS', $MasterID, $otcode['otCode'], 'General Over Time', 'srp_erp_generalotmaster', 'generalOTMasterID');
                    if ($approvals_status) {
                        return array('s', 'Document successfully confirmed');
                    } else {
                        return array('e', 'Approvals not created');
                    }

                }
            }
        } else {
            return array('w', 'There are no users exist to perform attendance summary approval for this company');
        }
    }

    function referback_general_ot()
    {
        $generalOTMasterID = $this->input->post('generalOTMasterID');

        $this->load->library('approvals');
        $status = $this->approvals->approve_delete($generalOTMasterID, 'ATS');

        if ($status == 1) {
            return array('s', ' Referred Back Successfully.', $status);
        } else {
            return array('e', ' Error in refer back.', $status);
        }
    }

    function fetch_template_master($generalOTMasterID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('generalOTMasterID,otCode,description, DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate ,approvedYN');
        $this->db->where('generalOTMasterID', $generalOTMasterID);
        $this->db->from('srp_erp_generalotmaster');
        $data = $this->db->get()->row_array();
        return $data;
    }


    function save_general_ot_approval()
    {
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_code = trim($this->input->post('generalOTMasterID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('got_status'));
        $comments = trim($this->input->post('comments'));
        $companyID = current_companyID();


        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'ATS');
        if ($approvals_status) {
            if ($approvals_status == 1) {
                $master = $this->db->query("SELECT * FROM srp_erp_generalotmaster WHERE generalOTMasterID={$system_code} AND companyID=$companyID")->row_array();
                $detail = $this->db->query("SELECT * FROM srp_erp_generalotdetail WHERE generalOTMasterID={$system_code} AND companyID=$companyID")->result_array();

                foreach ($detail as $val) {
                    if ($val['overtimeCategoryID'] > 0) {
                        $otcat['empID'] = $val['empID'];
                        $otcat['attendanceDate'] = $master['documentDate'];
                        $otcat['generalOTID'] = $master['generalOTMasterID'];
                        $otcat['isGeneralOT'] = 1;
                        $otcat['salaryCategoryID'] = $val['salaryCategoryID'];
                        $otcat['paymentOT'] = $val['transactionAmount'];
                        $otcat['companyID'] = current_companyID();
                        $otcat['companyCode'] = current_companyCode();
                        $this->db->insert('srp_erp_pay_empattendancereview', $otcat);
                    } else {
                        $Defotcat['empID'] = $val['empID'];
                        $Defotcat['attendanceDate'] = $master['documentDate'];
                        $Defotcat['generalOTID'] = $master['generalOTMasterID'];
                        $Defotcat['isGeneralOT'] = 1;
                        $Defotcat['salaryCategoryID'] = $val['salaryCategoryID'];
                        $Defotcat['noPayAmount'] = $val['transactionAmount'];
                        $Defotcat['nonPayrollSalaryCategoryID'] = $val['nonPayrollSalaryCategoryID'];
                        $Defotcat['noPaynonPayrollAmount'] = $val['noPaynonPayrollAmount'];
                        $Defotcat['companyID'] = current_companyID();
                        $Defotcat['companyCode'] = current_companyCode();
                        $this->db->insert('srp_erp_pay_empattendancereview', $Defotcat);
                    }
                }
            }
            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];

            $this->db->where('generalOTMasterID', trim($this->input->post('generalOTMasterID')));
            $this->db->update('srp_erp_generalotmaster', $data);

            $this->session->set_flashdata('s', 'Approval Created Successfully.');
        } else {
            $this->session->set_flashdata('s', 'Approval Rejected Successfully.');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function delete_general_ot_template()
    {
        $generalOTMasterID = $this->input->post('generalOTMasterID');
        $confirmed = $this->db->query("SELECT generalOTMasterID FROM srp_erp_generalotmaster WHERE generalOTMasterID={$generalOTMasterID} AND confirmedYN=1")->row_array();
        if (!empty($confirmed)) {
            return array('w', 'Document has been confirmed.');
        } else {
            $status = $this->db->delete('srp_erp_generalotmaster', array('generalOTMasterID' => trim($generalOTMasterID)));
            if ($status) {
                return array('s', ' Deleted Successfully.');
            } else {
                return array('e', ' Error in Deletion.');
            }
        }
    }

    function delete_general_ot_template_employees()
    {
        $generalOTMasterID = $this->input->post('generalOTMasterID');
        $empID = $this->input->post('empID');
        $companyID = current_companyID();

        $status = $this->db->delete('srp_erp_generalotdetail', array('generalOTMasterID' => trim($generalOTMasterID),'empID' => trim($empID),'companyID' => trim($companyID)));

        if ($status) {
            return array('s', ' Deleted Successfully.');
        } else {
            return array('e', ' Error in Deletion.');
        }
    }

}