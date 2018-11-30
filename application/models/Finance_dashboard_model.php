<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Finance_dashboard_model extends CI_Model
{
    function __contruct()
    {
        parent::__contruct();
    }

    function getTotalRevenue($beginingDate, $endDate)
    {
        $result = $this->db->query("SELECT SUM(srp_erp_generalledger.companyLocalAmount)*-1 as totalRevenueLoc, SUM(srp_erp_generalledger.companyReportingAmount)*-1 as totalRevenue
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 11 AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'])->row_array();
        //echo $this->db->last_query();
        return $result["totalRevenue"];
    }

    function getNetProfit($beginingDate, $endDate)
    {
        $result = $this->db->query("SELECT SUM(srp_erp_generalledger.companyLocalAmount)*-1 as netProfitLoc,SUM(srp_erp_generalledger.companyReportingAmount)*-1 as netProfit
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
INNER JOIN srp_erp_accountCategoryTypes ON srp_erp_accountCategoryTypes.accountCategoryTypeID = srp_erp_chartofaccounts.accountCategoryTypeID
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'])->row_array();
        //echo $this->db->last_query();
        return $result["netProfit"];
    }

    function getOverallPerformance($beginingDate, $endDate, $months)
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
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 11 AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " 
	UNION 
	SELECT $feilds 'COGS' as description 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 12 AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . "
	UNION 
	SELECT $feilds 'Other Expense' as description 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 13 AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " 
	UNION 
	SELECT $feilds 'GP' as description 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND (srp_erp_chartofaccounts.accountCategoryTypeID = 11 OR srp_erp_chartofaccounts.accountCategoryTypeID = 12) AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'];
        $result = $this->db->query($sql)->result_array();
        return $result;
    }

    function getPerformanceSummary($beginingDate, $endDate)
    {
        $sql = "SELECT 'Revenue' as description,SUM(companyLocalAmount)*-1 as amountLoc,SUM(companyReportingAmount)*-1 as amount
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 11 AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " 
	UNION 
	SELECT 'COGS' as description,SUM(companyLocalAmount)*-1 as amountLoc,SUM(companyReportingAmount)*-1 as amount 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 12 AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . "
	UNION 
	SELECT 'Other Expense' as description,SUM(companyLocalAmount)*-1 as amountLoc,SUM(companyReportingAmount)*-1 as amount 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 13 AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . "
	UNION 
	SELECT 'Gross Profit' as description,SUM(companyLocalAmount)*-1 as amountLoc,SUM(companyReportingAmount)*-1 as amount 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND (srp_erp_chartofaccounts.accountCategoryTypeID = 11 OR srp_erp_chartofaccounts.accountCategoryTypeID = 12) AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id']."
	UNION
	SELECT 'Net Profit' as description,SUM(companyLocalAmount)*-1 as amountLoc,SUM(companyReportingAmount)*-1 as amount 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID IN(11,12,13,14,15) AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'];
        $result = $this->db->query($sql)->result_array();
        return $result;
    }


    function getRevenueDetailAnalysis($beginingDate, $endDate)
    {
        $sql = "SELECT SUM(((transactionQTY * -1) * salesPrice)/srp_erp_itemledger.companyLocalExchangeRate) as companyLocalAmount,SUM(((transactionQTY * -1) * salesPrice)/srp_erp_itemledger.companyReportingExchangeRate) as companyReportingAmount,srp_erp_itemcategory.description as subCategory,srp_erp_itemcategory.itemCategoryID FROM srp_erp_itemledger
INNER JOIN srp_erp_itemmaster ON srp_erp_itemledger.itemAutoID = srp_erp_itemmaster.itemAutoID  AND srp_erp_itemmaster.mainCategory = 'Inventory'
INNER JOIN srp_erp_itemcategory ON srp_erp_itemcategory.itemCategoryID = srp_erp_itemmaster.subcategoryID  
WHERE srp_erp_itemledger.companyID = " . $this->common_data['company_data']['company_id'] . "  AND srp_erp_itemledger.documentCode IN('CINV','DN','RV') AND srp_erp_itemledger.documentDate BETWEEN '$beginingDate' AND '$endDate'
GROUP BY srp_erp_itemmaster.subcategoryID";
        $result = $this->db->query($sql)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function getAssignedDashboard()
    {
        $this->db->select('userGroupID');
        $this->db->where('empID', current_userID());
        $this->db->where('companyID', current_companyID());
        $usergroup = $this->db->get('srp_erp_employeenavigation')->row_array();

        $this->db->select('srp_erp_userdashboardmaster.userDashboardID,srp_erp_userdashboardmaster.dashboardDescription,srp_erp_dashboardtemplate.pageName');
        $this->db->where('employeeID', current_userID());
        $this->db->join('srp_erp_dashboardtemplate', 'srp_erp_dashboardtemplate.templateID = srp_erp_userdashboardmaster.templateID');
        $this->db->order_by('srp_erp_userdashboardmaster.sortOrder');
        $result = $this->db->get('srp_erp_userdashboardmaster')->result_array();
        //echo $this->db->last_query();
        $data["dashboard"] = $result;
        return $data;
    }

    function getAssignedDashboardWidget()
    {
        $this->db->select('userGroupID');
        $this->db->where('empID', current_userID());
        $this->db->where('companyID', current_companyID());
        $usergroup = $this->db->get('srp_erp_employeenavigation')->row_array();

        $this->db->select('*');
        $this->db->where('employeeID', current_userID());
        $this->db->where('srp_erp_userdashboardwidget.userDashboardID',$this->input->post("userDashboardID"));
        $this->db->join('srp_erp_widgetmaster', 'srp_erp_widgetmaster.widgetID = srp_erp_userdashboardwidget.widgetID');
        $this->db->join('srp_erp_widgetposition', 'srp_erp_widgetposition.widgetPositionID = srp_erp_userdashboardwidget.positionID');
        $this->db->join('(SELECT * FROM srp_erp_usergroupwidget WHERE userGroupID = '.$usergroup['userGroupID'].' AND companyID = '.current_companyID().') as ugw', 'ugw.widgetID = srp_erp_userdashboardwidget.widgetID');
        $this->db->order_by('srp_erp_userdashboardwidget.userDashboardID asc,srp_erp_userdashboardwidget.sortOrder asc');
        $result = $this->db->get('srp_erp_userdashboardwidget')->result_array();

        $data["dashboardWidget"] = $result;
        return $data;

    }
    /*Started Function By Mushtaq Ahamed*/
    function getShortcutLinks()
    {
        $this->db->select('*');
        $this->db->where('EIdNo', current_userID());
        $this->db->where('isPublic', 0);
        $result = $this->db->get('srp_erp_dashboard_links')->result_array();
        return $result;

    }

    function save_private_link()
    {

        $this->db->set('EIdNo', current_userID());
        $this->db->set('isPublic', 0);
        $this->db->set('title', $this->input->post("description"));
        $this->db->set('hyperlink', $this->input->post("hyperlink"));
        $this->db->set('description', $this->input->post("description"));
        $this->db->set('createdUserID', current_userID());
        $this->db->set('createdPc', $this->common_data['current_pc']);
        $this->db->set('createdDatetime', $this->common_data['current_date']);
        $results = $this->db->insert('srp_erp_dashboard_links');
        if ($results) {
            return array('s', 'Link Added Successfully');
        }else{
            return array('e', 'Error In Adding Link');
        }
    }

    function deletePrivateLink(){
        $results = $this->db->delete('srp_erp_dashboard_links', array('linkID' => trim($this->input->post('linkID'))));
        if ($results) {
            return array('s', 'Link Deleted Successfully');
        }else{
            return array('e', 'Error In Deleting Link');
        }
    }

    function getPublicLinks()
    {
        $empid = current_userID();
        $result = $this->db->query("select dlm.*,dl.linkMasterID from  srp_erp_dashboard_links_master dlm
INNER JOIN srp_erp_dashboard_links dl on dlm.linkID=dl.linkMasterID and EIdNo='$empid'
UNION
select dlm1.*,'-' as linkMasterID from srp_erp_dashboard_links_master dlm1
where dlm1.linkID not in (select linkmasterID from srp_erp_dashboard_links where isPublic=-1 and EIdNo='$empid')")->result_array();
        return $result;
    }

    function getPublicList()
    {
        $this->db->select('*');
        $this->db->where('EIdNo', current_userID());
        $this->db->where('isPublic', -1);
        $result = $this->db->get('srp_erp_dashboard_links')->result_array();
        return $result;

    }

    function save_public_link()
    {
        $results = $this->db->delete('srp_erp_dashboard_links', array('EIdNo' => current_userID(),'isPublic' => -1));
        if($results){
            if(!empty($this->input->post('widgetCheck'))){
            foreach($this->input->post('widgetCheck') as $val){
                $this->db->select('linkID,title,hyperlink,description');
                $this->db->where('linkID', $val);
                $link = $this->db->get('srp_erp_dashboard_links_master')->row_array();
                if($link){
                    $this->db->set('EIdNo', current_userID());
                    $this->db->set('isPublic', -1);
                    $this->db->set('linkMasterID', $val);
                    $this->db->set('title', $link['title']);
                    $this->db->set('hyperlink', $link['hyperlink']);
                    $this->db->set('description', $link['description']);
                    $this->db->set('createdUserID', current_userID());
                    $this->db->set('createdPc', $this->common_data['current_pc']);
                    $this->db->set('createdDatetime', $this->common_data['current_date']);
                    $result = $this->db->insert('srp_erp_dashboard_links');
                }
            }
            }else{
                return array('s', 'Link Added Successfully');
            }
            if($result){
                return array('s', 'Link Added Successfully');
            }else{
                return array('e', 'Error In Adding Link');
            }
        }

    }
    /*End Function By Mushtaq Ahamed*/

    function getRevenueDetailAnalysisByGLcode($beginingDate, $endDate)
    {
        $result = $this->db->query("SELECT SUM(srp_erp_generalledger.companyLocalAmount)*-1 as companyLocalAmount,SUM(srp_erp_generalledger.companyReportingAmount)*-1 as companyReportingAmount,srp_erp_chartofaccounts.GLDescription,srp_erp_generalledger.GLAutoID
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 11 AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id']." GROUP BY srp_erp_generalledger.GLAutoID")->result_array();
        //echo $this->db->last_query();
        return $result;
    }
    /*Started Function By Mushtaq Ahamed*/
    function getNewMembers()
    {
        $cmpid=current_companyID();
        $result = $this->db->query("

SELECT
    EIdNo,
    Ename2,
    EmpImage,
    srp_designation.DesDescription,
    (CURDATE() - INTERVAL 1 MONTH) AS onemonth,
    srp_employeesdetails.EDOJ,
    DATE(srp_employeesdetails.EDOJ) AS datecreated
FROM
    `srp_employeesdetails`
LEFT JOIN srp_designation ON srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID
WHERE
    srp_employeesdetails.isDeleted = 0
    AND isSystemAdmin = 0
AND isActive = 1
AND srp_employeesdetails.Erp_companyID = '$cmpid'
HAVING
    EDOJ BETWEEN onemonth and CURDATE()
Limit 10
;")->result_array();
        return $result;
    }


    function save_to_do_list()
    {
        $datFormat = date('Y-m-d', strtotime($this->input->post('startDate')));
        $this->db->set('employeeId', current_userID());
        $this->db->set('companyId', current_companyID());
        $this->db->set('description', $this->input->post('description'));
        $this->db->set('startDate', $datFormat);
        $this->db->set('startTime', $this->input->post('startTime'));
        $this->db->set('priority', $this->input->post('priority'));
        $this->db->set('createdDateTime', $this->common_data['current_date']);
        $result = $this->db->insert('srp_erp_to_do_list');

        if($result){
            return array('s', 'Record added successfully');
        }else{
            return array('e', 'Error in adding record');
        }
    }

    function getToDoList()
    {
        $cmpid=current_companyID();
        $empid=current_userID();
        $result = $this->db->query("SELECT srp_erp_to_do_list.*,srp_erp_priority_master.priorityDescription FROM `srp_erp_to_do_list` LEFT JOIN srp_erp_priority_master on srp_erp_to_do_list.priority = srp_erp_priority_master.priorityID WHERE startDate >= CURDATE() AND employeeId = '$empid' AND companyId = '$cmpid' AND isCompleated = 0 ORDER BY srp_erp_to_do_list.autoId ASC;")->result_array();
        return $result;
    }

    function check_to_do_list(){
        $curdate=$this->common_data['current_date'];
        $data['isCompleated'] = $this->input->post('checked');
        if($this->input->post('checked')==-1){
        $data['endDate'] = date('Y-m-d',strtotime($curdate));
        }else{
            $data['endDate'] = NULL;
        }
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        $this->db->where('autoId', trim($this->input->post('autoId')));
        $results = $this->db->update('srp_erp_to_do_list', $data);
        if ($results) {
            return array('s', 'Record updated successfully');
        }else{
            return array('e', 'Error in updating record');
        }
    }

    function deletetodoList(){
        $results = $this->db->delete('srp_erp_to_do_list', array('autoId' => trim($this->input->post('autoId'))));
        if ($results) {
            return array('s', 'Record deleted successfully');
        }else{
            return array('e', 'Error in deleting record');
        }
    }

    function getToDoListHistory()
    {
        $cmpid=current_companyID();
        $empid=current_userID();
        $result = $this->db->query("SELECT srp_erp_to_do_list.*,srp_erp_priority_master.priorityDescription FROM `srp_erp_to_do_list` LEFT JOIN srp_erp_priority_master on srp_erp_to_do_list.priority = srp_erp_priority_master.priorityID WHERE startDate <= CURDATE() AND employeeId = '$empid' AND companyId = '$cmpid' AND isCompleated = -1 ORDER BY srp_erp_to_do_list.autoId ASC;")->result_array();
        return $result;
    }
    /*End Function By Mushtaq Ahamed*/

    function getTotalSalesLog()
    {
        $currentYear = date("Y");
        $lastYear = date("Y",strtotime("-1 year"));
        $beginingDate = "";
        $beginingDateLast = "";
        $endDate = "";
        $endDateLast = "";
        $period = $this->input->post("period");
        $lastTwoYears = get_last_two_financial_year();
        if (!empty($lastTwoYears)) {
            $beginingDate = $lastTwoYears[$period]["beginingDate"];
            $endDate = $lastTwoYears[$period]["endingDate"];
            $beginingDateLast = $lastTwoYears[$period+1]["beginingDate"];
            $endDateLast = $lastTwoYears[$period+1]["endingDate"];
        }

        $result = $this->db->query('SELECT sum(if(documentDate >= \''.$beginingDate.'\' AND documentDate <= \''.$endDate.'\',companyReportingAmount,0)) * -1 as currentYear,SUM(if(documentDate >= \''.$beginingDateLast.'\' AND documentDate <= \''.$endDateLast.'\',companyReportingAmount,0))* -1 as lastYear,DecimalPlaces FROM `srp_erp_generalledger` INNER JOIN `srp_erp_chartofaccounts` ON `srp_erp_generalledger`.`GLAutoID` = `srp_erp_chartofaccounts`.`GLAutoID` AND `srp_erp_chartofaccounts`.`masterCategory` = "PL" AND `srp_erp_chartofaccounts`.`companyID` = ' . $this->common_data['company_data']['company_id'].' LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_generalledger`.`partyAutoID` LEFT JOIN `srp_erp_currencymaster` ON `srp_erp_currencymaster`.`currencyID` = `srp_erp_generalledger`.`companyReportingCurrencyID` WHERE `srp_erp_chartofaccounts`.`accountCategoryTypeID` = 11 AND `srp_erp_generalledger`.`companyID` =  ' . $this->common_data['company_data']['company_id'].' AND `srp_erp_generalledger`.`partyType` = "CUS"')->row_array();
        return $result;
    }

    function getRestNewMembers($pageId)
    {
        $row=$pageId*10;
        $cmpid=current_companyID();
        $result = $this->db->query("
SELECT
    EIdNo,
    Ename2,
    EmpImage,
    srp_designation.DesDescription,
    (CURDATE() - INTERVAL 1 MONTH) AS onemonth,
    srp_employeesdetails.EDOJ,
    DATE(srp_employeesdetails.EDOJ) AS datecreated
FROM
    `srp_employeesdetails`
LEFT JOIN srp_designation ON srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID
WHERE
    srp_employeesdetails.isDeleted = 0
    AND isSystemAdmin = 0
AND isActive = 1
AND srp_employeesdetails.Erp_companyID = '$cmpid'
HAVING
    EDOJ BETWEEN onemonth and CURDATE()
Limit $row,$row
;")->result_array();
        return $result;
    }


    function getAllNewMembers()
    {
        $cmpid=current_companyID();
        $result = $this->db->query("SELECT
    EIdNo,
    Ename2,
    EmpImage,
    srp_designation.DesDescription,
    (CURDATE() - INTERVAL 1 MONTH) AS onemonth,
    srp_employeesdetails.EDOJ,
    DATE(srp_employeesdetails.EDOJ) AS datecreated
FROM
    `srp_employeesdetails`
LEFT JOIN srp_designation ON srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID
WHERE
    srp_employeesdetails.isDeleted = 0
    AND isSystemAdmin = 0
AND isActive = 1
AND srp_employeesdetails.Erp_companyID = '$cmpid'
HAVING
    EDOJ BETWEEN onemonth and CURDATE()
;")->result_array();
        return $result;
    }

    function getRevenueDetailAnalysisBySegment($beginingDate, $endDate)
    {
        $result = $this->db->query("SELECT SUM(srp_erp_generalledger.companyLocalAmount)*-1 as companyLocalAmount,SUM(srp_erp_generalledger.companyReportingAmount)*-1 as companyReportingAmount,srp_erp_segment.description,srp_erp_generalledger.GLAutoID
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_segment ON srp_erp_generalledger.segmentID = srp_erp_segment.segmentID AND srp_erp_segment.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id']." AND GLType='PLI' GROUP BY srp_erp_generalledger.segmentID")->result_array();
        //echo $this->db->last_query();
        return $result;
    }

}
