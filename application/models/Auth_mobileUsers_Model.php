<?php
class Auth_mobileUsers_Model extends ERP_Model{

    function get_users()
    {
        $q = "SELECT UserName,`Password` FROM `srp_employeesdetails`";
        $results = $this->db->query($q)->result_array();
        return $results;
    }

    function get_userID($username,$pwd){

        $this->db->select('EIdNo,Erp_companyID,ECode,t2.company_code,Ename1');
        $this->db->from('srp_employeesdetails AS t1');
        $this->db->join('srp_erp_company AS t2', 't2.company_id = t1.Erp_companyID');
        $this->db->where('UserName',$username);
        $this->db->where('Password',$pwd);
        return $this->db->get()->row_array();
    }

    function get_emp_designation($id){

        $this->db->select('DesDescription');
        $this->db->from('srp_employeedesignation AS t1');
        $this->db->join('srp_designation AS t2', 't2.DesignationID = t1.DesignationID');
        $this->db->where('EmpID',$id);
        return $this->db->get()->row_array();

    }

    function get_emp_details($id){

        $this->db->select('EDOJ,t2.company_name');
        $this->db->from('srp_employeesdetails AS t1');
        $this->db->join('srp_erp_company AS t2', 't2.company_id = t1.Erp_companyID');
        $this->db->where('EIdNo',$id);
        return $this->db->get()->row_array();

    }

    function save_deviceInfo($id,$devID){
        $q = "SELECT count(*) as `rec` from `srp_devices` WHERE emp_id ='".$id."' AND player_id='".$devID."' ";
        $results = $this->db->query($q)->row_array();
        if($results['rec'] =='0' )
            {
                $data = array(
                    'emp_id' => $id,
                    'player_id' => $devID
                );
                $this->db->insert('srp_devices', $data);
           }
    }

    function get_approvals($eid,$comp_id,$limit){
        $this->db->select('t1.documentApprovedID,t1.documentCode,t1.documentDate,approvalLevelID,t1.documentID,table_name,table_unique_field_name,t1.documentSystemCode,t2.document,t2.icon,t3.Ename1');
        $this->db->from('srp_erp_documentapproved  AS t1');
        $this->db->join('srp_erp_documentcodes AS t2', 't1.documentID = t2.documentID','LEFT');
        $this->db->join('srp_employeesdetails AS t3', 't1.docConfirmedByEmpID = t3.EIdNo','LEFT');
        $this->db->where('companyID',$comp_id);
        $this->db->where('approvedEmpID',$eid);
        $this->db->where('approvedYN','0');
        $this->db->where('isApprovalDocument','1');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    function count_approvals($eid,$comp_id){

        $q = "SELECT count(documentApprovedID) as cnt FROM srp_erp_documentapproved t1 left join srp_erp_documentcodes t2 ON
		t1.documentID = t2.documentID
		WHERE companyID =$comp_id AND approvedYN = '0' AND approvedEmpID = $eid AND t2.isApprovalDocument='1' ";
        $results = $this->db->query($q)->row_array();
        return $results;
    }

    function get_approvalDoc_content($documentCode,$table,$feild,$fvalue){
        $columns = "*";
        $joins = "";
        //$where = "";
        switch ($documentCode) {
            case 'LA':
                $columns = "startDate AS `Start Date`,endDate AS `End Date`,days AS `Days`,confirmedByName as `Confirmed By`,confirmedDate AS  `Confirmed Date`";
                break;

            case 'PO':
                $columns = "supplierName AS `Supplier Name`,CONCAT(transactionAmount,transactionCurrency) AS `TR Amount`,segmentCode,confirmedByName as `Confirmed By`,confirmedDate AS  `Confirmed Date`";
                break;

            case 'DN':

                $columns = "debitNoteDate AS `DN Date`,debitNoteCode as `DN Code`,supplierName AS `Supplier Name`,
                                    CONCAT(SUM(srp_erp_debitnotedetail.transactionAmount),' ',transactionCurrency )AS `TR Amount`,confirmedByName AS `Confirmed By`,confirmedDate AS `Confirmed Date`";
                $joins = "LEFT JOIN srp_erp_debitnotedetail ON srp_erp_debitnotedetail.debitNoteMasterAutoID=srp_erp_debitnotemaster.debitNoteMasterAutoID";
                break;

            case 'PV':
                $columns = "PVcode,PVdate,PVNarration, CONCAT(SUM(srp_erp_paymentvoucherdetail.transactionAmount),' ',srp_erp_paymentvoucherdetail.transactionCurrency )AS `TR Amount`,confirmedByName AS `Confirmed By`,confirmedDate as `Confirmed Date`";
                $joins = "LEFT JOIN srp_erp_paymentvoucherdetail ON srp_erp_paymentvoucherdetail.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId";
                break;

            case 'CN':

                $columns = "creditNoteDate AS `CN Date`,creditNoteCode as `CN Code`,
                                    CONCAT(SUM(srp_erp_creditnotedetail.transactionAmount),' ',transactionCurrency )AS `TR Amount`,confirmedByName AS `Confirmed By`,confirmedDate AS `Confirmed Date`";
                $joins = "LEFT JOIN srp_erp_creditnotedetail ON srp_erp_creditnotedetail.creditNoteMasterAutoID=srp_erp_creditnotemaster.creditNoteMasterAutoID";
                break;

            case 'SA':
                $columns = "stockAdjustmentDate as `SA Date`,stockAdjustmentCode AS  `SA Code`,wareHouseLocation as `WH Location`,comments AS `Comments`,SUM(srp_erp_stockadjustmentdetails.totalValue) as `Total value` ,
                                   confirmedByName as `Confirmed By`,confirmedDate AS  `Confirmed Date`";
                $joins = "LEFT JOIN srp_erp_stockadjustmentdetails ON  srp_erp_stockadjustmentdetails.stockAdjustmentAutoID = srp_erp_stockadjustmentmaster.stockAdjustmentAutoID";
                break;

            case 'RV':
                $columns = "RVcode as `Code`,RVbank as `Bank`,RVbankAccount as `Account No`,RVbankBranch as `Branch`,customerName as `Customer Name`,
                                  SUM(srp_erp_customerreceiptdetail.transactionAmount) AS `TR Amount`,confirmedByName as `Confirmed By`,confirmedDate AS  `Confirmed Date`";
                $joins = "LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId";
                break;
            case 'JV':
                $columns = "JVdate AS  `Date`,JVType AS `Type`,JVNarration AS `Narration`";
                break;

            case 'CINV':
                $columns = "invoiceCode AS `Invoice Code`,invoiceDate AS `Date`,invoicebank AS `Bank`,customerName `Customer name`,
                           CONCAT( SUM(t2.transactionAmount)+SUM(t3.transactionAmount),' ',srp_erp_customerinvoicemaster.transactionCurrency) as `Amount`, confirmedByName as `Confirmed By`,confirmedDate AS  `Confirmed Date`";
                $joins = "LEFT JOIN srp_erp_customerinvoicedetails t2 ON t2.invoiceAutoID =srp_erp_customerinvoicemaster.invoiceAutoID
                          LEFT JOIN  srp_erp_customerinvoicetaxdetails t3 ON t3.invoiceAutoID =t2.invoiceAutoID ";
                break;

            case 'BSI':
                $columns = "invoiceType AS `Invoice Type`,supplierName AS `Supplier Name`,invoicebank AS `Bank`,customerName `Customer name`,
                         CONCAT( SUM(t2.transactionAmount)+SUM(t3.transactionAmount),' ',srp_erp_customerinvoicemaster.transactionCurrency) as `Amount`";
                $joins = "LEFT JOIN srp_erp_customerinvoicedetails t2 ON t2.invoiceAutoID =srp_erp_customerinvoicemaster.invoiceAutoID
                          LEFT JOIN  srp_erp_customerinvoicetaxdetails t3 ON t3.invoiceAutoID =t2.invoiceAutoID ";
                break;

            case 'QUT':
                $columns = "contractType AS `Contract Type`,contractDate AS `Contract Date`,contractCode AS `Code`,contractNarration AS `Narration`,customerName AS `Customer`,
                        CONCAT(SUM(srp_erp_contractdetails.transactionAmount),' ',srp_erp_contractmaster.transactionCurrency) AS `Amount`,confirmedByName as `Confirmed By`,confirmedDate AS  `Confirmed Date`,
                        confirmedByName AS `Confirmed By`,confirmedDate AS `Confirmed Date`";
                $joins = "LEFT JOIN srp_erp_contractdetails ON srp_erp_contractdetails.contractAutoID = srp_erp_contractmaster.contractAutoID ";
                break;
            case 'GRV':
                $columns="grvDate AS `Date`,grvType AS `Type`,grvNarration AS `Narration`,wareHouseDescription AS `Warehouse`,supplierName AS `Supplier`
                      ,CONCAT(SUM(t2.fullTotalAmount),' ',transactionCurrency) AS Amount,confirmedByName as `Confirmed By`,confirmedDate AS  `Confirmed Date` ";
                $joins="LEFT JOIN srp_erp_grvdetails t2 ON t2.grvAutoID=srp_erp_grvmaster.grvAutoID ";
                break;
            case 'CNT':
                $columns="contractType AS `Type`,contractCode AS `Contract code`,contractDate AS `Date`,contractExpDate AS  `Exp date`,contractNarration AS `Narration`,
                     CONCAT(SUM(t2.transactionAmount),' ',transactionCurrency) AS Amount,confirmedByName as `Confirmed By`,confirmedDate AS  `Confirmed Date`";
                $joins="LEFT JOIN srp_erp_contractdetails t2 ON t2.contractAutoID=srp_erp_contractmaster.contractAutoID ";
                break;
            case 'SR':
                $columns ="stockReturnCode AS `ST code`,returnDate AS `Date`,wareHouseDescription AS `Warehouse`,supplierName AS `Supplier`,confirmedByName as `Confirmed By`,confirmedDate AS  `Confirmed Date`";

                break;

            case 'FA':
                $columns ="faCode,assetDescription,comments,usedBy,costGLCode AS `Cost GLCode`,CONCAT(transactionAmount,' ',transactionCurrency)AS `Amount`,confirmedByName AS `Confirmed by`,confirmedDate AS `Confirmed Date`";
                break;

            case 'BT':
                $columns ="bankTransferCode AS 'BT Code',transferedAmount AS `Amount`,exchangeRate AS `Exchange Rate`,transferedDate AS `date`,narration AS `Narration`,confirmedByName AS `Confirmed by`,confirmedDate AS `Confirmed Date`";
                break;
            case 'FAD';
                $columns ="depCode AS `Code`,depDate AS `Date`,depType AS `Type`,CONCAT(transactionAmount,' ',transactionCurrency) AS `Amount`,confirmedByName AS `Confirmed by`,confirmedDate AS `Confirmed Date`";
                break;
            case 'BRC':
                $columns="bankRecAsOf AS `Date`,month AS `Month`,openingBalance AS `Opening Balance`, closingBalance AS `Closing Balance`,description AS `Description`";
                break;
            case 'MI' :
                $columns="itemIssueCode AS `II Code`,itemType AS `Issue Type`,itemType AS `Type`,issueDate AS `Date`,wareHouseDescription AS `Warehouse`
                 ,comment AS `Comment`,confirmedByName AS `Confirmed By`,confirmedDate AS `Confirmed Date`";
                break;
            case 'SO':
                $columns="contractType AS `Contract Type`,contractDate AS `Contract Date`,contractCode AS `C Code`,contractNarration AS `Narration`,
                  contactPersonName AS `Contract Person`,customerName AS `Customer`,CONCAT(SUM(t2.transactionAmount),' ',srp_erp_contractmaster.transactionCurrency) AS 'Amount'";
                $joins="LEFT JOIN srp_erp_contractdetails t2 ON  t2.contractAutoID = srp_erp_contractmaster.contractAutoID";
                break;
            case 'SP':
                $columns="payrollMonth AS `Month`,documentNo AS `Doc No`,processDate AS `Date`,narration AS `Narration`,confirmedByName AS `Confirmed By`,confirmedDate AS `Confirmed Date`";
                break;
            case 'ST':
                $columns="stockTransferCode AS `ST Code`,itemType AS `Item Type` ,tranferDate AS `Date`,comment AS Comment,form_wareHouseDescription AS Warehouse,confirmedByName AS `Confirmed By`,confirmedDate AS `Confirmed Date`";
                break;
            case 'ADSP':
                $columns="segmentCode AS `Segment Code`,narration AS  `Narration`,confirmedByName AS `Confirmed By`,confirmedDate AS `Confirmed Date`";
                break;
            case 'SD':
                $columns="documentDate AS `Date`,Description,CONCAT(SUM(t2.transactionAmount),' ',srp_erp_salarydeclarationmaster.transactionCurrency) AS Amount, confirmedByName AS `Confirmed By`,confirmedDate AS `Confirmed Date`";
                $joins="LEFT JOIN srp_erp_salarydeclarationdetails t2 ON t2.declarationMasterID =srp_erp_salarydeclarationmaster.salarydeclarationMasterID ";
                break;
            case 'LO':
                $columns="loanCode AS `Loan Code`,loanDate AS Date,loanDescription AS Description,amount AS Amount,numberOfInstallment AS Installments,
                 CONCAT(transactionAmount,' ',transactionCurrency) AS Amount,confirmedByName AS `Confirmed By`,confirmedDate AS `Confirmed Date` ";
                break;
        }
        $q = "SELECT " . $columns . " FROM " . $table . " " . $joins . "  WHERE " . $table . "." . $feild . "='" . $fvalue . "' ";
        $results = $this->db->query($q)->row_array();
        return $results;
    }

    function getApproval_docID($table,$feild,$fvalue)
    {
        $q = "SELECT * FROM " . $table . "  WHERE " . $feild . "='" . $fvalue . "' ";
        $res = $this->db->query($q)->row_array();
        return $res;
    }

    function getAssignedDashboard($empID,$companyID)
    {
        $this->db->select('userGroupID');
        $this->db->where('empID', $empID);
        $this->db->where('companyID', $companyID);

        $usergroup = $this->db->get('srp_erp_employeenavigation')->row_array();

        $this->db->select('srp_erp_userdashboardmaster.userDashboardID,srp_erp_userdashboardmaster.dashboardDescription,srp_erp_dashboardtemplate.pageName');
        $this->db->where('employeeID', $empID);
        $this->db->join('srp_erp_dashboardtemplate', 'srp_erp_dashboardtemplate.templateID = srp_erp_userdashboardmaster.templateID');
        $result = $this->db->get('srp_erp_userdashboardmaster')->result_array();
        //echo $this->db->last_query();
        return $result;

    }

    function get_last_two_financial_year($comid)
    {

        $this->db->SELECT("beginingDate,endingDate");
        $this->db->FROM('srp_erp_companyfinanceyear');
        $this->db->WHERE('companyID', $comid);
        $this->db->WHERE('isActive', '1');
        $this->db->WHERE('isCurrent', '1');
        return $this->db->get()->result_array();
    }

    function getAssignedDashboardWidget($empID,$companyID,$dbID)
    {
        $this->db->select('userGroupID');
        $this->db->where('empID', $empID);
        $this->db->where('companyID', $companyID);
        $usergroup = $this->db->get('srp_erp_employeenavigation')->row_array();

        $this->db->select('*');
        $this->db->where('employeeID', $empID);
        $this->db->where('srp_erp_userdashboardwidget.userDashboardID',$dbID);
        $this->db->where('srp_erp_widgetmaster.MobileYN','1');
        $this->db->join('srp_erp_widgetmaster', 'srp_erp_widgetmaster.widgetID = srp_erp_userdashboardwidget.widgetID');
        $this->db->join('srp_erp_widgetposition', 'srp_erp_widgetposition.widgetPositionID = srp_erp_userdashboardwidget.positionID');
        $this->db->order_by('srp_erp_userdashboardwidget.userDashboardID asc,srp_erp_userdashboardwidget.sortOrder asc');
        $result = $this->db->get('srp_erp_userdashboardwidget')->result_array();
        return $result;
    }

    function getTotalRevenue($beginingDate, $endDate,$comid)
    {
                $result = $this->db->query("SELECT SUM(srp_erp_generalledger.companyLocalAmount)*-1 as totalRevenueLoc, SUM(srp_erp_generalledger.companyReportingAmount)*-1 as totalRevenue
        FROM
            srp_erp_generalledger
        INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " .$comid. "
        WHERE
            srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 11 AND srp_erp_generalledger.companyID = " .$comid)->row_array();
        //echo $this->db->last_query();
        return $result;
    }

    function getNetProfit($beginingDate, $endDate,$comid)
    {
                $result = $this->db->query("SELECT SUM(srp_erp_generalledger.companyLocalAmount)*-1 as netProfitLoc,SUM(srp_erp_generalledger.companyReportingAmount)*-1 as netProfit
        FROM
            srp_erp_generalledger
        INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $comid . "
        INNER JOIN srp_erp_accountCategoryTypes ON srp_erp_accountCategoryTypes.accountCategoryTypeID = srp_erp_chartofaccounts.accountCategoryTypeID
        WHERE
            srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_generalledger.companyID = " . $comid)->row_array();
                //echo $this->db->last_query();
                return $result;
    }

    function getOverallPerformance($beginingDate, $endDate, $months,$comid)
    {
        $feilds = "";
        if (!empty($months)) {
            foreach ($months as $key => $val2) {
                $feilds .= "SUM(if(DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') = '$key',srp_erp_generalledger.companyReportingAmount * -1,0) ) as `" . $val2 . "`,";
            }
        }
                        $sql = "SELECT $feilds 'Revenue' as description
                FROM
                    srp_erp_generalledger
                INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $comid . "
                WHERE
                    srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 11 AND srp_erp_generalledger.companyID = " . $comid . " 
                    UNION 
                    SELECT $feilds 'COGS' as description 
                FROM
                    srp_erp_generalledger
                INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $comid . "
                WHERE
                    srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 12 AND srp_erp_generalledger.companyID = " . $comid . "
                    UNION 
                    SELECT $feilds 'Other Cost' as description 
                FROM
                    srp_erp_generalledger
                INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $comid . "
                WHERE
                    srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 13 AND srp_erp_generalledger.companyID = " . $comid . " 
                    UNION 
                    SELECT $feilds 'GP' as description 
                FROM
                    srp_erp_generalledger
                INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $comid . "
                WHERE
                    srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND (srp_erp_chartofaccounts.accountCategoryTypeID = 11 OR srp_erp_chartofaccounts.accountCategoryTypeID = 12) AND srp_erp_generalledger.companyID = " . $comid;
                        $result = $this->db->query($sql)->result_array();
                        return $result;
    }

    function getRevenueDetailAnalysis($beginingDate, $endDate,$comid)
    {
                $sql = "SELECT SUM(((transactionQTY * -1) * salesPrice)/srp_erp_itemledger.companyLocalExchangeRate) as companyLocalAmount,SUM(((transactionQTY * -1) * salesPrice)/srp_erp_itemledger.companyReportingExchangeRate) as companyReportingAmount,srp_erp_itemcategory.description as subCategory,srp_erp_itemcategory.itemCategoryID FROM srp_erp_itemledger
        INNER JOIN srp_erp_itemmaster ON srp_erp_itemledger.itemAutoID = srp_erp_itemmaster.itemAutoID  AND srp_erp_itemmaster.mainCategory = 'Inventory'
        INNER JOIN srp_erp_itemcategory ON srp_erp_itemcategory.itemCategoryID = srp_erp_itemmaster.subcategoryID  
        WHERE srp_erp_itemledger.companyID = " . $comid . "  AND srp_erp_itemledger.documentCode IN('CINV','DN','RV') AND srp_erp_itemledger.documentDate BETWEEN '$beginingDate' AND '$endDate'
        GROUP BY srp_erp_itemmaster.subcategoryID";
                $result = $this->db->query($sql)->result_array();
                return $result;
    }

    function getPerformanceSummary($beginingDate, $endDate,$comid)
    {
        $sql = "SELECT 'Revenue' as description,SUM(companyLocalAmount)*-1 as amountLoc,SUM(companyReportingAmount)*-1 as amount
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $comid . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 11 AND srp_erp_generalledger.companyID = " . $comid . " 
	UNION 
	SELECT 'COGS' as description,SUM(companyLocalAmount)*-1 as amountLoc,SUM(companyReportingAmount)*-1 as amount 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $comid . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 12 AND srp_erp_generalledger.companyID = " . $comid . "
	UNION 
	SELECT 'Other Cost' as description,SUM(companyLocalAmount)*-1 as amountLoc,SUM(companyReportingAmount)*-1 as amount 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $comid . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 13 AND srp_erp_generalledger.companyID = " . $comid . "
	UNION 
	SELECT 'Gross Profit' as description,SUM(companyLocalAmount)*-1 as amountLoc,SUM(companyReportingAmount)*-1 as amount 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $comid . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND (srp_erp_chartofaccounts.accountCategoryTypeID = 11 OR srp_erp_chartofaccounts.accountCategoryTypeID = 12) AND srp_erp_generalledger.companyID = " . $comid;
        $result = $this->db->query($sql)->result_array();
        return $result;
    }

    function get_fastMovingItem($beginingDate,$endDate,$compID){
        $this->db->select('SUM(((il.transactionQTY/convertionRate)*-1) * il.salesPrice/il.companyReportingExchangeRate) as companyReportingAmount,il.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces,im.defaultUnitOfMeasure as UOM,im.itemDescription,SUM(il.transactionQTY/convertionRate)*-1 as transactionQTY,im.itemSystemCode,il.companyReportingCurrencyDecimalPlaces,im.currentStock as currentStock', false)
        ->from('srp_erp_itemledger il')
        ->join('srp_erp_itemmaster im','il.itemAutoID = im.itemAutoID','inner')
        ->where('il.documentDate BETWEEN "'.$beginingDate.'"
            AND "'.$endDate.'" AND il.companyID = "' . $compID . '" AND il.documentCode IN ("CINV","RV") AND im.mainCategory = "Inventory"')
        ->group_by('il.itemAutoID');
        return $this->db->get()->result_array();
    }

    function get_bankPosition($comid){
        $this->db->select('bankCurrencyCode,srp_erp_chartofaccounts.GLDescription,(SUM(if(srp_erp_bankledger.transactionType = 1,srp_erp_bankledger.bankcurrencyAmount,0)) - SUM(if(srp_erp_bankledger.transactionType = 2,srp_erp_bankledger.bankcurrencyAmount,0))) as bookBalance,(SUM(if(srp_erp_bankledger.transactionType = 1 AND srp_erp_bankrecmaster.approvedYN = 1,srp_erp_bankledger.bankcurrencyAmount,0)) - SUM(if(srp_erp_bankledger.transactionType = 2 AND srp_erp_bankrecmaster.approvedYN = 1,srp_erp_bankledger.bankcurrencyAmount,0))) as bankBalance', false)
            ->from('srp_erp_chartofaccounts')
            ->join('srp_erp_bankledger', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_bankledger.bankGLAutoID AND srp_erp_bankledger.companyID = ' . $comid, 'INNER')
            ->join('srp_erp_bankrecmaster', 'srp_erp_bankrecmaster.bankrecAutoID = srp_erp_bankledger.bankRecMonthID', 'LEFT')
            ->where('srp_erp_chartofaccounts.companyID', $comid)
            ->group_by('srp_erp_chartofaccounts.GLAutoID');
        return $this->db->get()->result_array();
    }

    function get_overdue_payable($comid){
        $this->db->select('srp_erp_paysupplierinvoicemaster.companyReportingCurrencyDecimalPlaces as decimalPlace,srp_erp_paysupplierinvoicemaster.companyReportingCurrency as currency, srp_erp_suppliermaster.supplierName as supplierName,supplierAutoID,SUM(srp_erp_paysupplierinvoicemaster.transactionAmount) - (IFNULL(pvd.transactionAmount,0) + IFNULL(dnd.transactionAmount,0) + IFNULL(pva.transactionAmount,0)) as amount', false)
            ->from('srp_erp_paysupplierinvoicemaster')
            ->join('srp_erp_suppliermaster', 'srp_erp_paysupplierinvoicemaster.supplierID = srp_erp_suppliermaster.supplierAutoID', 'LEFT')
            ->join("(SELECT
                         IFNULL(SUM(srp_erp_paymentvoucherdetail.companyReportingAmount),0) as companyReportingAmount,IFNULL(SUM(srp_erp_paymentvoucherdetail.transactionAmount),0) as transactionAmount,srp_erp_paymentvoucherdetail.InvoiceAutoID,srp_erp_paymentvoucherdetail.payVoucherAutoID,partyID,
                srp_erp_paymentvouchermaster.transactionCurrency
                    FROM
                        srp_erp_paymentvoucherdetail
                        INNER JOIN `srp_erp_paymentvouchermaster` ON `srp_erp_paymentvouchermaster`.`payVoucherAutoID` = `srp_erp_paymentvoucherdetail`.`payVoucherAutoID` AND `srp_erp_paymentvouchermaster`.`approvedYN` = 1
                    WHERE
                        `srp_erp_paymentvoucherdetail`.`companyID` = " . $comid . " AND srp_erp_paymentvouchermaster.PVDate <= '" . current_date() . "' AND srp_erp_paymentvoucherdetail.InvoiceAutoID IS NOT NULL  GROUP BY srp_erp_paymentvouchermaster.partyID,
                srp_erp_paymentvouchermaster.transactionCurrency) pvd", 'pvd.partyID = srp_erp_paysupplierinvoicemaster.supplierID AND `pvd`.`transactionCurrency` = `srp_erp_paysupplierinvoicemaster`.`transactionCurrency`', 'LEFT')
                            ->join("(SELECT IFNULL(SUM(srp_erp_debitnotedetail.transactionAmount),0) as transactionAmount,IFNULL(SUM(srp_erp_debitnotedetail.companyReportingAmount),0) as companyReportingAmount,
                         srp_erp_debitnotedetail.InvoiceAutoID,srp_erp_debitnotedetail.debitNoteMasterAutoID,supplierID,
                srp_erp_debitnotemaster.transactionCurrency
                    FROM
                        srp_erp_debitnotedetail 
                        INNER JOIN `srp_erp_debitnotemaster` ON `srp_erp_debitnotemaster`.`debitNoteMasterAutoID` = `srp_erp_debitnotedetail`.`debitNoteMasterAutoID` AND `srp_erp_debitnotemaster`.`approvedYN` = 1
                    WHERE
                        `srp_erp_debitnotedetail`.`companyID` = " . $comid . " AND srp_erp_debitnotemaster.debitNoteDate <= '" . current_date() . "' AND srp_erp_debitnotedetail.InvoiceAutoID IS NOT NULL GROUP BY srp_erp_debitnotemaster.supplierID,
                srp_erp_debitnotemaster.transactionCurrency) dnd", 'dnd.supplierID = srp_erp_paysupplierinvoicemaster.supplierID AND `dnd`.`transactionCurrency` = `srp_erp_paysupplierinvoicemaster`.`transactionCurrency`', 'LEFT')
                            ->join("(SELECT
                            IFNULL(SUM(srp_erp_pvadvancematchdetails.companyReportingAmount),0) as companyReportingAmount,IFNULL(SUM(srp_erp_pvadvancematchdetails.transactionAmount),0) as transactionAmount,
                         srp_erp_pvadvancematchdetails.InvoiceAutoID,supplierID,
                srp_erp_pvadvancematch.transactionCurrency
                    FROM
                    srp_erp_pvadvancematchdetails
                        INNER JOIN `srp_erp_pvadvancematch` ON `srp_erp_pvadvancematch`.`matchID` = `srp_erp_pvadvancematchdetails`.`matchID` AND `srp_erp_pvadvancematch`.`confirmedYN` = 1
                        WHERE `srp_erp_pvadvancematch`.`matchDate` <= '" . current_date() . "' AND srp_erp_pvadvancematchdetails.InvoiceAutoID IS NOT NULL GROUP BY srp_erp_pvadvancematch.supplierID,
                srp_erp_pvadvancematch.transactionCurrency) pva",'pva.supplierID = srp_erp_paysupplierinvoicemaster.supplierID AND `pva`.`transactionCurrency` = `srp_erp_paysupplierinvoicemaster`.`transactionCurrency`','LEFT')
                            ->where('srp_erp_paysupplierinvoicemaster.companyID', $comid)
                            ->where('invoiceDueDate <=', current_date())
                            ->where('srp_erp_paysupplierinvoicemaster.approvedYN', 1)
                            ->group_by('srp_erp_paysupplierinvoicemaster.supplierID');
        return $this->db->get()->result_array();
    }

    function fetch_overdue_receivable($comid)
    {
//        $fields = "";
//            $fields .= 'srp_erp_customerinvoicemaster.companyReportingCurrency as currency,';
//            $fields .= 'srp_erp_customerinvoicemaster.companyReportingCurrencyDecimalPlaces as decimalPlace,';
//            $fields .= 'SUM(srp_erp_customerinvoicemaster.companyReportingAmount) - (IFNULL(pvd.companyReportingAmount,0)+IFNULL(cnd.companyReportingAmount,0)+IFNULL(ca.transactionAmount,0)) as amount,';

        $this->db->select('srp_erp_customerinvoicemaster.companyReportingCurrencyDecimalPlaces as decimalPlace,srp_erp_customerinvoicemaster.companyReportingCurrency as currency,srp_erp_customermaster.customerName as customerName,customerAutoID,SUM(srp_erp_customerinvoicemaster.companyReportingAmount) - (IFNULL(pvd.companyReportingAmount,0)+IFNULL(cnd.companyReportingAmount,0)+IFNULL(ca.transactionAmount,0)) as amount', false)
            ->from('srp_erp_customerinvoicemaster')
            ->join('srp_erp_customermaster', 'srp_erp_customerinvoicemaster.customerID = srp_erp_customermaster.customerAutoID', 'LEFT')
            ->join("(SELECT SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount,SUM(srp_erp_customerreceiptdetail.companyReportingAmount) as companyReportingAmount,
		 srp_erp_customerreceiptdetail.invoiceAutoID,srp_erp_customerreceiptdetail.receiptVoucherAutoID,
		 srp_erp_customerreceiptmaster.customerID,
srp_erp_customerreceiptmaster.transactionCurrency
	FROM
		srp_erp_customerreceiptdetail
		INNER JOIN `srp_erp_customerreceiptmaster` ON `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId` = `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` AND `srp_erp_customerreceiptmaster`.`approvedYN` = 1
	WHERE
		`srp_erp_customerreceiptdetail`.`companyID` = " . $comid . " AND srp_erp_customerreceiptmaster.RVDate <= '" . current_date(false) . "' AND srp_erp_customerreceiptdetail.invoiceAutoID IS NOT NULL  GROUP BY srp_erp_customerreceiptmaster.customerID,srp_erp_customerreceiptmaster.transactionCurrency) pvd", 'pvd.customerID = srp_erp_customerinvoicemaster.customerID AND `pvd`.`transactionCurrency` = `srp_erp_customerinvoicemaster`.`transactionCurrency`', 'LEFT')
            ->join("(SELECT SUM(srp_erp_creditnotedetail.transactionAmount) as transactionAmount,SUM(srp_erp_creditnotedetail.companyReportingAmount) as companyReportingAmount,
		 invoiceAutoID,srp_erp_creditnotedetail.creditNoteMasterAutoID,srp_erp_creditnotemaster.customerID,srp_erp_creditnotemaster.transactionCurrency
	FROM
		srp_erp_creditnotedetail
		INNER JOIN `srp_erp_creditnotemaster` ON `srp_erp_creditnotemaster`.`creditNoteMasterAutoID` = `srp_erp_creditnotedetail`.`creditNoteMasterAutoID` AND `srp_erp_creditnotemaster`.`approvedYN` = 1
	WHERE
		`srp_erp_creditnotedetail`.`companyID` = " . $comid . " AND srp_erp_creditnotemaster.creditNoteDate <= '" . current_date(false) . "' AND srp_erp_creditnotedetail.invoiceAutoID IS NOT NULL GROUP BY srp_erp_creditnotemaster.customerID,srp_erp_creditnotemaster.transactionCurrency) cnd", 'cnd.customerID = srp_erp_customerinvoicemaster.customerID AND `cnd`.`transactionCurrency` = `srp_erp_customerinvoicemaster`.`transactionCurrency`', 'LEFT')
            ->join("(SELECT SUM(srp_erp_rvadvancematchdetails.transactionAmount) as transactionAmount,SUM(srp_erp_rvadvancematchdetails.companyReportingAmount) as companyReportingAmount,
 srp_erp_rvadvancematchdetails.InvoiceAutoID,srp_erp_rvadvancematchdetails.receiptVoucherAutoID,srp_erp_rvadvancematch.customerID,srp_erp_rvadvancematch.transactionCurrency
	FROM srp_erp_rvadvancematchdetails 
	INNER JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematchdetails`.`matchID` = `srp_erp_rvadvancematch`.`matchID` AND `srp_erp_rvadvancematch`.`confirmedYN` = 1
	WHERE `srp_erp_rvadvancematchdetails`.`companyID` = " . $comid . " AND srp_erp_rvadvancematchdetails.invoiceAutoID IS NOT NULL GROUP BY srp_erp_rvadvancematch.customerID,srp_erp_rvadvancematch.transactionCurrency) ca", 'ca.customerID = srp_erp_customerinvoicemaster.customerID AND `ca`.`transactionCurrency` = `srp_erp_customerinvoicemaster`.`transactionCurrency`', 'LEFT')
            ->where('srp_erp_customerinvoicemaster.companyID', $comid)
            ->where('invoiceDueDate <=', current_date())
            ->where('srp_erp_customerinvoicemaster.approvedYN', 1)
            ->group_by('srp_erp_customerinvoicemaster.customerID');
        return $this->db->get()->result_array();
    }

    function fetch_postdated_cheque_given($comid)
    {
        $this->db->select('srp_erp_bankledger.bankCurrencyAmount as bankCurrencyAmount,chequeDate as dueDate,bankCurrency,DATEDIFF(chequeDate,CURDATE()) as dueDays,CONCAT(IFNULL(srp_erp_customermaster.customerName, ""),IFNULL(srp_erp_suppliermaster.supplierName, "")) as vendor,remainIn', false)
            ->from('srp_erp_bankledger')
            ->join('srp_erp_bankrecmaster', 'srp_erp_bankrecmaster.bankrecAutoID = srp_erp_bankledger.bankRecMonthID', 'LEFT')
            ->join('srp_erp_customermaster', 'srp_erp_bankledger.partyCode = srp_erp_customermaster.customerSystemCode', 'LEFT')
            ->join('srp_erp_suppliermaster', 'srp_erp_bankledger.partyCode = srp_erp_suppliermaster.supplierSystemCode', 'LEFT')
            ->where('srp_erp_bankledger.transactionType', 2)
            ->where('srp_erp_bankledger.companyID', $comid)
            ->where('(srp_erp_bankrecmaster.approvedYN = 0 OR srp_erp_bankledger.bankRecMonthID IS NULL)')
            ->where('srp_erp_bankledger.documentDate < srp_erp_bankledger.chequeDate')
            ->limit(10);
        return $this->db->get()->result_array();
    }

    function fetch_postdated_cheque_received($comid)
    {
        $this->db->select('srp_erp_bankledger.bankCurrencyAmount as bankCurrencyAmount,chequeDate as dueDate,bankCurrency,DATEDIFF(chequeDate,CURDATE()) as dueDays,CONCAT(IFNULL(srp_erp_customermaster.customerName, ""),IFNULL(srp_erp_suppliermaster.supplierName, "")) as vendor,remainIn', false)
            ->from('srp_erp_bankledger')
            ->join('srp_erp_bankrecmaster', 'srp_erp_bankrecmaster.bankrecAutoID = srp_erp_bankledger.bankRecMonthID', 'LEFT')
            ->join('srp_erp_customermaster', 'srp_erp_bankledger.partyCode = srp_erp_customermaster.customerSystemCode', 'LEFT')
            ->join('srp_erp_suppliermaster', 'srp_erp_bankledger.partyCode = srp_erp_suppliermaster.supplierSystemCode', 'LEFT')
            ->where('srp_erp_bankledger.transactionType', 1)
            ->where('srp_erp_bankledger.companyID', $comid)
            ->where('(srp_erp_bankrecmaster.approvedYN = 0 OR srp_erp_bankledger.bankRecMonthID IS NULL)')
            ->where('srp_erp_bankledger.documentDate < srp_erp_bankledger.chequeDate')
            ->limit(10);
        return $this->db->get()->result_array();
    }



}