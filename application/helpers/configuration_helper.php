<?php
///** Translation added by Shafri */
//$primaryLanguage = getPrimaryLanguage();
//$this->lang->load('profile', $primaryLanguage);
//$this->lang->load('common', $primaryLanguage);
////$title = $this->lang->line('profile_my_profile');
//?>
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
$newurl = explode("/", $_SERVER['SCRIPT_NAME']);
$actual_link = "$protocol$_SERVER[HTTP_HOST]/$newurl[1]/images/logo/";
define('UPLOAD_PATH', $_SERVER["DOCUMENT_ROOT"]);
define('UPLOAD_PATH_POS', $_SERVER["DOCUMENT_ROOT"] . '/gs_sme/');
define('UPLOAD_PATH_MFQ', $_SERVER["DOCUMENT_ROOT"] . '/gs_sme/uploads/mfq/');
define('LOGO', 'spur-logo-200.png');
define('LOGO_GEARS', 'gears-standard-logo-w.png');
define('SYS_NAME', 'SPUR');
define('SETTINGS_BAR', 'off'); // on, off
define("mPDFImage", $actual_link);
define("favicon", 'favicon.png');
define("ssoReportColumnDetails", serialize(array(
    'EPF' => array('shortOrder', 1),
    'ETF' => array('shortOrderETF', 2),
    'ETF-H' => array('shortOrderETFH', 3),
)));
define('STATIC_LINK', "$protocol.$_SERVER[HTTP_HOST]");

if (!function_exists('head_page')) {
    function head_page($sub_heading, $status, $closeFunc = NULL)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $Filter = $CI->lang->line('common_filter');
        $filter = '';
        if ($status) {
            $filter = '<a data-toggle="collapse" data-target="#filter-panel"><i class="fa fa-filter"></i> ' . $Filter . '<!--Filter--></a>';
        }
        /* $filter = '<a data-toggle="collapse" data-target="#filter-panel" class="btn btn-small"><button type="button" class="btn btn-small"><i class="icon-filter"></i> Advanced Filters</button></a>';*/
        $closeFuncString = '';
        if (!empty($closeFunc)) {
            $closeFuncString = 'onclick="' . trim($closeFunc) . '"';
        }

        return '<div class="row">
                    <div class="col-md-12">
                      <div class="box">
                        <div class="box-header with-border" id="box-header-with-border">
                          <h3 class="box-title" id="box-header-title">' . $sub_heading . '</h3>
                          <div class="box-tools pull-right">
                            ' . $filter . '<button id="" class="btn btn-box-tool page-minus" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            <button  id="" ' . $closeFuncString . ' class="btn btn-box-tool headerclose navdisabl" ><i class="fa fa-times"></i></button>
                          </div>
                        </div>
                        <div class="box-body">';
    }
}

if (!function_exists('head_page_employee')) {
    /***By Nasik ***/
    function head_page_employee()
    {
        return '<div class="row">
                    <div class="col-md-12">
                      <div class="box">
                        <div class="box-body" style="border-top: none;margin-top: -2px;">';
    }
}

if (!function_exists('footer_page')) {
    function footer_page($right_foot, $left_foot, $status)
    {
        if ($status) {
            return '</div><div class="box-footer"></div></div></div></div>';
        } else {
            return '</div></div></div></div>';
        }
    }
}

if (!function_exists('text_type')) {
    function text_type($status)
    {
        $data = '<span class="text-center">';
        if ($status == 1) {
            $data .= '<b>Sales Tax</b>';
        } else {
            $data .= '<b>Purchase Tax</b>';
        }
        $data .= '</span>';

        return $data;
    }
}

if (!function_exists('table_class')) {
    function table_class()
    {
        return 'table table-bordered table-striped table-condensed table-row-select';
    }
}

if (!function_exists('item_more_info')) {
    function item_more_info($itemID, $currentStock, $SellingPrice, $Currency, $decimal, $revanue, $cost, $asste, $WacAmount, $mainCategory)
    {
        //<b>Revanue GL Code : </b> '.$revanue.' &nbsp;&nbsp;<b>Cost GL Code : </b> '.$cost.' &nbsp;&nbsp;<b>Asste GL Code : </b> '.$asste.'<br>
        $data = '<br><span class="pull-right">';
        if ($mainCategory == 'Inventory') {
            $data .= '<b>Wac Amount : </b>' . number_format($WacAmount, $decimal) . ' &nbsp;&nbsp;&nbsp;';
        }
        $data .= '<b>Current Stock : </b>' . $currentStock . '&nbsp;&nbsp;&nbsp;';
        $data .= '<b>Selling Price : </b>' . $Currency . ' : ' . number_format($SellingPrice, $decimal);
        $data .= '</span>';

        return $data;
    }
}


if (!function_exists('row_class')) {
    function row_class()
    {

        $CI =& get_instance();
        $CI->load->model('erp_rowclass');
        $CI->Srp_checkMail->row_class();
    }
}

if (!function_exists('set_next_db_process')) {
    function set_next_db_process($next)
    {
        $CI =& get_instance();
        $CI->session->set_tempdata('next_db_process', $next, 500);

        return true;
    }
}

if (!function_exists('get_next_db_process')) {
    function get_next_db_process()
    {
        $CI =& get_instance();
        $data = $CI->session->tempdata('next_db_process');
        return (empty($data)) ? '' : '[ ' . $data . ' ]';
    }
}

if (!function_exists('fetch_coa_type')) {
    function fetch_coa_type($coa_type)
    {
        $data = '<span class="text-center">';
        if ($coa_type == 'PLI') {
            $data .= '<b>Income</b>';
        } elseif ($coa_type == 'PLE') {
            $data .= '<b>Expense</b>';
        } elseif ($coa_type == 'BSA') {
            $data .= '<b>Asset</b>';
        } elseif ($coa_type == 'BSL') {
            $data .= '<b>Liability</b>';
        } else {
            $data .= '<b>Equity</b>';
        }
        $data .= '</span>';

        return $data;
    }
}


if (!function_exists('format_date')) {
    function format_date($date, $format = 'Y-m-d')
    {
        if (!is_null($date)) {
            return date($format, strtotime($date));
        } else {
            return '';
        }

    }
}

if (!function_exists('current_date')) {
    function current_date($time = TRUE)
    {
        if ($time) {
            return date('Y-m-d H:i:s');
        } else {
            return date('Y-m-d');
        }
    }
}

if (!function_exists('format_number')) {
    function format_number($amount = 0, $decimal_place = 2)
    {
        if (is_null($amount)) {
            $amount = 0;
        }
        if (is_null($decimal_place)) {
            $decimal_place = 2;
        }

        return number_format($amount, $decimal_place);
    }
}

if (!function_exists('required_mark')) {
    function required_mark()
    {
        $tmp = '<span title="required field" style="color:red; font-weight: 600; font-size: 12px;">*</span>';
        echo $tmp;
    }
}

if (!function_exists('string_upper')) {
    function string_upper($string)
    {
        return $string;
    }
}

if (!function_exists('trim_desc')) {
    function trim_desc($string)
    {
        $string = preg_replace("/[\n\r]/", "", str_replace(array("'", '"'), array(" ", " "), $string));

        return trim($string);
    }
}

if (!function_exists('current_companyID')) {
    function current_companyID()
    {
        $CI =& get_instance();
        $companyID = isset($CI->common_data['company_data']['company_id']) ? $CI->common_data['company_data']['company_id'] : NULL;

        return trim($companyID);
    }
}

if (!function_exists('current_companyCode')) {
    function current_companyCode()
    {
        $CI =& get_instance();

        return trim($CI->common_data['company_data']['company_code']);
    }
}

if (!function_exists('current_userID')) {
    function current_userID()
    {
        $CI =& get_instance();
        $userID = isset($CI->common_data['current_userID']) ? $CI->common_data['current_userID'] : NULL;

        return trim($userID);
    }
}

if (!function_exists('current_userCode')) {
    function current_userCode()
    {
        $CI =& get_instance();

        return trim($CI->common_data['current_userCode']);
    }
}

if (!function_exists('current_user')) {
    function current_user()
    {
        $CI =& get_instance();
        if (!empty($CI->common_data['current_user'])) {
            $user = trim($CI->common_data['current_user']);

        } else {
            $user = trim($CI->common_data['username']);
        }

        return $user;
    }
}

if (!function_exists('current_employee')) {
    function current_employee()
    {
        $CI =& get_instance();

        return trim($CI->common_data['current_user']);
    }
}

if (!function_exists('current_user_group')) {
    function current_user_group()
    {
        $CI =& get_instance();

        return trim($CI->common_data['user_group']);
    }
}

if (!function_exists('current_pc')) {
    function current_pc()
    {
        $CI =& get_instance();

        return trim($CI->common_data['current_pc']);
    }
}

if (!function_exists('companyLogo')) {
    function companyLogo()
    {
        $CI =& get_instance();
        $companyLogo = isset($CI->common_data['company_data']['company_logo']) ? $CI->common_data['company_data']['company_logo'] : 'no-logo.png';

        return trim($companyLogo);
    }
}

if (!function_exists('imagePath')) {
    function imagePath()
    {
        $CI =& get_instance();

        return trim($CI->common_data['imagePath']);
    }
}


if (!function_exists('getCompanyImagePath')) {
    function getCompanyImagePath()
    {
        $CI =& get_instance();
        $CI->db->select('imagePath,isLocalPath')
            ->from('srp_erp_pay_imagepath');
        $data = $CI->db->get()->row_array();

        return $data;
    }
}

function checkIsFileExists($image_url)
{
    $ret = FALSE;
    if (file_exists(UPLOAD_PATH . '' . $image_url)) {
        $ret = TRUE;
    }


    return ($ret == TRUE) ? $image_url : base_url('images/default.gif');

}

function checkIsFileExists_old($image_url)
{
    $ret = FALSE;
    $comImgPath_arr = getCompanyImagePath();


    if ($comImgPath_arr['isLocalPath'] == 1) {  //If file path is local
        if (file_exists(UPLOAD_PATH . $image_url)) {
            $ret = TRUE;
        }
    } else { //If file path is not local
        $curl = curl_init($image_url);

        //don't fetch the actual page, you only want to check the connection is ok
        curl_setopt($curl, CURLOPT_NOBODY, TRUE);

        //do request
        $result = curl_exec($curl);

        //if request did not fail
        if ($result !== FALSE) {
            //if request was ok, check response code
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($statusCode == 200) {
                $ret = TRUE;
            }
        }

        curl_close($curl);

    }

    return ($ret == TRUE) ? $image_url : base_url('images/default.gif');

}

if (!function_exists('trim_value')) {
    function trim_value($comments = '', $trimVal = 150)
    {
        $String = $comments;
        $truncated = (strlen($String) > $trimVal) ? substr($String, 0,
                $trimVal) . '<span class="tol" rel="tooltip" style="color:#0088cc" title="' . str_replace('"', '&quot;',
                $String) . '">... more </span>' : $String;

        return $truncated;
    }
}

//load edit for item master
if (!function_exists('opensubcat')) {
    function opensubcat($itemCategoryID, $description)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'fetchPage("system/item/sub_category_add","' . $itemCategoryID . '","' . $description . '","Sub Category",""); \'><button type="button" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-plus" style="color:green;"></span></button></a>';
        $status .= '</span>';

        return $status;
    }
}

//Generate documentcode
if (!function_exists('generate_seq_number')) {
    function generate_seq_number($code = NULL, $count = NULL, $number = NULL, $schoolID = NULL)
    {
        return ($code . str_pad($number, $count, '0', STR_PAD_LEFT));
    }
}

if (!function_exists('all_account_category_drop')) {
    function all_account_category_drop($status = TRUE, $filter = FALSE)
    {
        $CI =& get_instance();
        $CI->db->SELECT("accountCategoryTypeID,Type,CategoryTypeDescription,subType");
        $CI->db->FROM('srp_erp_accountcategorytypes');
        if ($filter) {
            $CI->db->where('CategoryTypeDescription<>', 'Bank');
        }
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Category Types');
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['accountCategoryTypeID'])] = trim($row['Type']) . ' | ' . trim($row['subType']) . ' | ' . trim($row['CategoryTypeDescription']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_tax_drop')) {
    function all_tax_drop($id = 2, $status = 1)
    {
        $CI =& get_instance();
        $CI->db->SELECT("taxMasterAutoID,taxDescription,taxShortCode,taxPercentage");
        $CI->db->FROM('srp_erp_taxmaster');
        $CI->db->where('taxType', $id);
        $CI->db->where('isActive', 1);
        $CI->db->where('isApplicableforTotal', 0);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Tax Types');
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['taxMasterAutoID'])] = trim($row['taxShortCode']) . ' | ' . trim($row['taxDescription']) . ' | ' . trim($row['taxPercentage']) . ' %';
                }
            }
        } else {
            $data_arr = $data;
        }

        return $data_arr;
    }
}

if (!function_exists('supplier_gl_drop')) {
    function supplier_gl_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->where_in('subCategory', array("BSL", "BSA"));
        $CI->db->WHERE('controllAccountYN', 1);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Supplier GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('supplier_group_gl_drop')) {
    function supplier_group_gl_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_groupchartofaccounts');
        $CI->db->where_in('subCategory', array("BSL", "BSA"));
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('groupID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Supplier GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_revenue_gl_drop')) {
    function all_revenue_gl_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('subCategory', "PLI");
        $CI->db->WHERE('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('dropdown_all_revenue_gl')) {
    function dropdown_all_revenue_gl($code = NULL)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        if ($code) {
            $code = " AND subCategory != '$code' ";
        } else {
            $code = "";
        }
        $data = $CI->db->query("SELECT
    coa.GLAutoID,
    coa.systemAccountCode,
    coa.GLSecondaryCode,
    coa.GLDescription,
    coa.systemAccountCode,
    coa.subCategory
FROM
    `srp_erp_chartofaccounts` coa
WHERE
    coa.`masterAccountYN` = 0
AND coa.`approvedYN` = 1
AND coa.`isActive` = 1
AND coa.`isBank` = 0
AND coa.accountCategoryTypeID != 4
AND coa.`companyID` = '{$companyID}'
AND coa.controllAccountYN = 0
$code
UNION
SELECT
    coa.GLAutoID,
    coa.systemAccountCode,
    coa.GLSecondaryCode,
    coa.GLDescription,
    coa.systemAccountCode,
    coa.subCategory
FROM
    `srp_erp_chartofaccounts` coa
WHERE
    coa.`masterAccountYN` = 0
AND coa.`approvedYN` = 1
AND coa.`isActive` = 1
AND coa.`isBank` = 0
AND coa.accountCategoryTypeID != 4
AND coa.`companyID` = '{$companyID}'
AND  GLAutoID in(SELECT
    GLAutoID
FROM
    srp_erp_companycontrolaccounts cmp
WHERE
    cmp.companyID = '{$companyID}'
AND (cmp.controlaccounttype = 'ADSP' or cmp.controlaccounttype='PCA' or cmp.controlaccounttype='TAX'))")->result_array();

        $data_arr = array('' => 'Select GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }

        return $data_arr;
    }
}


if (!function_exists('all_cost_gl_drop')) {
    function all_cost_gl_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('subCategory', "PLE");
        //$CI->db->WHERE('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Cost GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_asset_gl_drop')) {
    function all_asset_gl_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('subCategory', "BSA");
        //$CI->db->WHERE('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Asset Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('master_account_drop')) {
    function master_coa_account()
    {
        $CI =& get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('masterAccountYN', 1);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Master Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_gl_account_desc')) {
    function fetch_gl_account_desc($id)
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('GLAutoID', $id);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);

        return $CI->db->get()->row_array();
    }
}

if (!function_exists('company_bank_account_drop')) {
    function company_bank_account_drop($status = TRUE)
    {
        $CI =& get_instance();
        $CI->db->select("GLAutoID,bankName,bankBranch,bankSwiftCode,bankAccountNumber,subCategory,isCash");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->WHERE('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 1);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $bank = $CI->db->get()->result_array();
        $bank_arr = array('' => 'Select Bank Account');
        if (isset($bank)) {
            foreach ($bank as $row) {
                $type = ($row['isCash'] == '1') ? ' | Cash' : ' | Bank';
                $bank_arr[trim($row['GLAutoID'])] = trim($row['bankName']) . ' | ' . trim($row['bankBranch']) . ' | ' . trim($row['bankSwiftCode']) . ' | ' . trim($row['bankAccountNumber']) . ' | ' . trim($row['subCategory']) . $type;
            }
        }

        return $bank_arr;
    }
}

if (!function_exists('fetch_all_gl_codes')) {
    function fetch_all_gl_codes($code = NULL, $category = NULL)
    {
        $CI =& get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory,accountCategoryTypeID");
        $CI->db->from('srp_erp_chartofaccounts');
        if ($code) {
            $CI->db->where('subCategory', $code);
        }
        if ($category) {
            $CI->db->where('subCategory !=', $category);
        }
        $CI->db->where('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->WHERE('accountCategoryTypeID !=', 4);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('isActive', 1);
        $CI->db->where('isBank', 0);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select GL Code');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }

        return $data_arr;
    }
}

// if (!function_exists('load_expense_gl_drop')) {
//     function load_expense_gl_drop()
//     {
//         $CI =& get_instance();
//         $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
//         $CI->db->FROM('srp_erp_chartofaccounts');
//         $CI->db->WHERE('subCategory', "PLE");
//         $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
//         $data = $CI->db->get()->result_array();
//         $data_arr = array('' => 'Select Expense GL Account');
//         if (isset($data)) {
//             foreach ($data as $row) {
//                 $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' .trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']). ' | ' . trim($row['subCategory']);
//             }
//         }
//         return $data_arr;
//     }
// }

// if (!function_exists('coa_control_account')) {
//     function coa_control_account()
//     {
//         $CI =& get_instance();
//         $CI->db->SELECT("GLAutoID,GLSecondaryCode,GLDescription,systemAccountCode");
//         $CI->db->FROM('srp_erp_chartofaccounts');
//         $CI->db->WHERE('controllAccountYN', 1);
//         $CI->db->where('isBank', 0);
//         $CI->db->where('isActive', 1);
//         $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
//         $data = $CI->db->get()->result_array();
//         $data_arr = array('' => 'Select Control Account');
//         if (isset($data)) {
//             foreach ($data as $row) {
//                 $data_arr[trim($row['GLAutoID'])] = trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']);
//             }
//         }
//         return $data_arr;
//     }
// }

// if (!function_exists('companyBankAccounts_drop')) {
//     function companyBankAccounts_drop(){
//         $companyID = $CI->common_data['company_data']['company_id'];
//         $CI =& get_instance();
//         $CI->db->select("GLAutoID, bankName, bankAccountNumber");
//         $CI->db->from('srp_erp_chartofaccounts');
//         $CI->db->where('isBank', 1);
//         $CI->db->where('isActive', 1);
//         $CI->db->where('isActive', 1);
//         $CI->db->where('bankName IS NOT NULL');
//         $CI->db->where('companyID', $companyID);
//         $CI->db->order_by('bankName');

//         $comBank = $CI->db->get()->result_array();
//         $comBank_arr = array('' => 'Select Bank');
//         if (isset($comBank)) {
//             foreach ($comBank as $row) {
//                 $comBank_arr[trim($row['GLAutoID'])] = trim($row['bankName']) . ' | ' . trim($row['bankAccountNumber']);
//             }
//         }
//         return $comBank_arr;
//     }
// }

// if (!function_exists('fetch_coa_desc')) {
//     function fetch_coa_desc($code)
//     {
//         $CI =& get_instance();
//         $CI->db->SELECT("*");
//         $CI->db->FROM('srp_erp_chartofaccounts');
//         $CI->db->WHERE('GLAutoID', $code);
//         $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
//         return $CI->db->get()->row_array();
//     }
// }

if (!function_exists('category_type')) {
    function category_type()
    {
        $CI =& get_instance();
        $CI->db->SELECT("categoryTypeID,categoryType,comment");
        $CI->db->FROM('srp_erp_itemCategoryType');
        //$CI->db->WHERE('masterAccountYN', 1);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Category Type');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['categoryTypeID'])] = trim($row['categoryType']) . ' | ' . trim($row['comment']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_currency_desimal')) {
    function fetch_currency_desimal($code)
    {
        $CI =& get_instance();
        $CI->db->SELECT("DecimalPlaces");
        $CI->db->FROM('srp_erp_currencymaster');
        $CI->db->WHERE('CurrencyCode', $code);

        return $CI->db->get()->row('DecimalPlaces');
    }
}

if (!function_exists('fetch_currency_desimal_by_id')) {
    function fetch_currency_desimal_by_id($currencyID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("DecimalPlaces");
        $CI->db->FROM('srp_erp_companycurrencyassign');
        $CI->db->WHERE('currencyID', $currencyID);

        return $CI->db->get()->row('DecimalPlaces');
    }
}

if (!function_exists('fetch_item_data')) {
    function fetch_item_data($itemAutoID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_itemmaster');
        $CI->db->WHERE('itemAutoID', $itemAutoID);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);

        return $CI->db->get()->row_array();
    }
}

if (!function_exists('fetch_ware_house_item_data')) {
    function fetch_ware_house_item_data($itemAutoID)
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $wareHouseID = $CI->common_data['ware_houseID'];

        $CI->db->select("*");
        $CI->db->select("(SELECT currentStock FROM srp_erp_warehouseitems WHERE itemAutoID=t1.itemAutoID AND wareHouseAutoID={$wareHouseID}) AS wareHouseQty ");
        $CI->db->from('srp_erp_itemmaster t1');
        $CI->db->where('t1.itemAutoID', $itemAutoID);
        $CI->db->where('t1.companyID', $companyID);

        return $CI->db->get()->row_array();
    }
}

if (!function_exists('fetch_currency_dec')) {
    function fetch_currency_dec($code)
    {
        $CI =& get_instance();
        $CI->db->SELECT("CurrencyName");
        $CI->db->FROM('srp_erp_currencymaster');
        $CI->db->WHERE('CurrencyCode', $code);

        return $CI->db->get()->row('CurrencyName');
    }
}

//units drop down
if (!function_exists('load_unit_drop')) {
    function load_unit_drop()
    {

        $CI =& get_instance();
        $CI->db->SELECT("UnitID,UnitDes,UnitShortCode");
        $CI->db->FROM('srp_erp_unit_of_measure');
        $CI->db->WHERE('companyID', $CI->common_data['company_data']['company_id']);
        $units = $CI->db->get()->result_array();

        return $units;
    }
}

if (!function_exists('load_location_drop')) {
    function load_location_drop()
    {
        /*Database changes NASIK*/
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $CI->db->SELECT("wareHouseCode,wareHouseDescription,companyCode,wareHouseAutoID");
        $CI->db->FROM('srp_erp_warehousemaster');
        $CI->db->WHERE('companyID', $companyID);
        $location = $CI->db->get()->result_array();

        return $location;
    }
}

if (!function_exists('load_warehouse_items')) {
    function load_warehouse_items()
    {
        $CI =& get_instance();
        $CI->db->SELECT("itemCodeSystem,itemDescriptionshort");
        $CI->db->FROM('srp_erp_itemmaster');
        $itemwarehouse = $CI->db->get()->result_array();

        return $itemwarehouse;
    }
}

//currency drop down
if (!function_exists('load_currency_drop')) {
    function load_currency_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("CurrencyID,CurrencyName,CurrencyCode");
        $CI->db->FROM('srp_erp_currencymaster');
        $currncy = $CI->db->get()->result_array();

        return $currncy;
    }
}

//country drop down
if (!function_exists('load_country_drop')) {
    function load_country_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("countryID,CountryDes,countryShortCode");
        $CI->db->FROM('srp_erp_countrymaster');
        $cntry = $CI->db->get()->result_array();

        return $cntry;
    }
}


if (!function_exists('load_all_country_drop')) {
    function load_all_country_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("countryID,CountryDes,countryShortCode");
        $CI->db->FROM('srp_erp_countrymaster');
        $output = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Country');
        if (isset($output)) {
            foreach ($output as $row) {
                $data_arr[trim($row['countryID'])] = trim($row['countryShortCode']) . ' | ' . trim($row['CountryDes']);
            }
        }

        return $data_arr;
    }
}


if (!function_exists('load_all_countryName_drop')) {
    function load_all_countryName_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("countryID,CountryDes,countryShortCode");
        $CI->db->FROM('srp_erp_countrymaster');
        $output = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Country');
        if (isset($output)) {
            foreach ($output as $row) {
                $data_arr[trim($row['CountryDes'])] = trim($row['CountryDes']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_main_category_drop')) {
    function all_main_category_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("itemCategoryID,description,codePrefix");
        $CI->db->FROM('srp_erp_itemcategory');
        $CI->db->WHERE('masterID', NULL);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Main Category');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['itemCategoryID'])] = trim($row['codePrefix']) . ' | ' . trim($row['description']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_currency_drop')) {
    function all_currency_drop($status = TRUE, $keyType = NULL)/*Load all currency*/
    {
        $CI =& get_instance();
        $CI->db->select("currencyID, CurrencyCode,CurrencyName");
        $CI->db->from('srp_erp_currencymaster');
        //$CI->db->where('isDefault', 0);
        $currency = $CI->db->get()->result_array();
        if ($status == TRUE) {/*by Nasik*/
            $currency_arr = array('' => 'Select Currency');
        }
        if (isset($currency)) {
            $masterVal = ($keyType == 'ID') ? 'currencyID' : 'CurrencyCode';
            foreach ($currency as $row) {
                $currency_arr[trim($row[$masterVal])] = trim($row['CurrencyCode']) . ' | ' . trim($row['CurrencyName']);
            }
        }

        return $currency_arr;
    }
}

if (!function_exists('all_currency_new_drop')) {
    function all_currency_new_drop($status = TRUE)/*Load all currency*/
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("srp_erp_companycurrencyassign.currencyID,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.CurrencyName");
        $CI->db->from('srp_erp_currencymaster');
        $CI->db->join('srp_erp_companycurrencyassign', 'srp_erp_companycurrencyassign.currencyID = srp_erp_currencymaster.currencyID');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $currency = $CI->db->get()->result_array();
        if ($status) {
            $currency_arr = array('' => $CI->lang->line('common_select_currency')/*'Select Currency'*/);
        } else {
            $currency_arr = '';
        }
        if (isset($currency)) {
            foreach ($currency as $row) {
                $currency_arr[trim($row['currencyID'])] = trim($row['CurrencyCode']) . ' | ' . trim($row['CurrencyName']);
            }
        }

        return $currency_arr;
    }
}


if (!function_exists('all_currency_master_drop')) {
    function all_currency_master_drop($status = TRUE)/*Load all currency*/
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("currencyID,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.CurrencyName");
        $CI->db->from('srp_erp_currencymaster');
        $currency = $CI->db->get()->result_array();
        if ($status) {
            $currency_arr = array('' => $CI->lang->line('common_select_currency')/*'Select Currency'*/);
        } else {
            $currency_arr = '';
        }
        if (isset($currency)) {
            foreach ($currency as $row) {
                $currency_arr[trim($row['currencyID'])] = trim($row['CurrencyCode']) . ' | ' . trim($row['CurrencyName']);
            }
        }

        return $currency_arr;
    }
}


if (!function_exists('all_taxpayee_drop')) {
    function all_taxpayee_drop($status = TRUE)/*Load all Supplier*/
    {
        $CI =& get_instance();
        $CI->db->select("supplierAutoID,supplierName,supplierSystemCode,supplierCountry");
        $CI->db->from('srp_erp_suppliermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $supplier = $CI->db->get()->result_array();
        if ($status) {
            $supplier_arr = array('' => 'Select Tax Payee');
        } else {
            $supplier_arr = '';
        }
        if (isset($supplier)) {
            foreach ($supplier as $row) {
                $supplier_arr[trim($row['supplierAutoID'])] = (trim($row['supplierSystemCode']) ? trim($row['supplierSystemCode']) . ' | ' : '') . trim($row['supplierName']) . (trim($row['supplierCountry']) ? ' | ' . trim($row['supplierCountry']) : '');
            }
        }

        return $supplier_arr;
    }
}

if (!function_exists('all_supplier_drop')) {
    function all_supplier_drop($status = TRUE)/*Load all Supplier*/
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->select("supplierAutoID,supplierName,supplierSystemCode,supplierCountry");
        $CI->db->from('srp_erp_suppliermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $supplier = $CI->db->get()->result_array();
        if ($status) {
            $supplier_arr = array('' => $CI->lang->line('common_aelect_supplier')/*'Select Supplier'*/);
        } else {
            $supplier_arr = '';
        }
        if (isset($supplier)) {
            foreach ($supplier as $row) {
                $supplier_arr[trim($row['supplierAutoID'])] = (trim($row['supplierSystemCode']) ? trim($row['supplierSystemCode']) . ' | ' : '') . trim($row['supplierName']) . (trim($row['supplierCountry']) ? ' | ' . trim($row['supplierCountry']) : '');
            }
        }

        return $supplier_arr;
    }
}

if (!function_exists('fetch_supplier_data')) {
    function fetch_supplier_data($supplierID)
    {
        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_suppliermaster');
        $CI->db->where('supplierAutoID', $supplierID);

        return $CI->db->get()->row_array();
    }
}

if (!function_exists('all_customer_drop')) {
    function all_customer_drop($status = TRUE)/*Load all Customer*/
    {
        $CI =& get_instance();
        $CI->db->select("customerAutoID,customerName,customerSystemCode,customerCountry,companyCode");
        $CI->db->from('srp_erp_customermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $customer = $CI->db->get()->result_array();
        if ($status) {
            $customer_arr = array('' => 'Select Customer');
        } else {
            $customer_arr = '';
        }
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['customerAutoID'])] = (trim($row['customerSystemCode']) ? trim($row['customerSystemCode']) . ' | ' : '') . trim($row['customerName']) . (trim($row['customerCountry']) ? ' | ' . trim($row['customerCountry']) : '');
            }
        }

        return $customer_arr;
    }
}

if (!function_exists('all_srp_erp_sales_person_drop')) {
    function all_srp_erp_sales_person_drop($status = TRUE)
    {
        $CI =& get_instance();
        $CI->db->select("salesPersonID,SalesPersonName,SalesPersonCode,wareHouseLocation");
        $CI->db->from('srp_erp_salespersonmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $sales_person = $CI->db->get()->result_array();
        if ($status) {
            $sales_person_arr = array('' => 'Select Sales person');
            if (isset($sales_person)) {
                foreach ($sales_person as $row) {
                    $sales_person_arr[trim($row['salesPersonID'])] = (trim($row['SalesPersonCode']) ? trim($row['SalesPersonCode']) . ' | ' : '') . trim($row['SalesPersonName']) . (trim($row['wareHouseLocation']) ? ' | ' . trim($row['wareHouseLocation']) : '');
                }
            }
        } else {
            $sales_person_arr = '';
        }

        return $sales_person_arr;
    }
}

if (!function_exists('all_umo_drop')) {
    function all_umo_drop()
    {
        $CI =& get_instance();
        $CI->db->select('UnitShortCode,UnitDes');
        $CI->db->from('srp_erp_unit_of_measure');
        $CI->db->WHERE('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select UOM');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['UnitShortCode'])] = trim($row['UnitShortCode']) . ' - ' . trim($row['UnitDes']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_umo_new_drop')) {
    function all_umo_new_drop()
    {
        $CI =& get_instance();
        $CI->db->select('UnitID,UnitShortCode,UnitDes');
        $CI->db->from('srp_erp_unit_of_measure');
        $CI->db->WHERE('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select UOM');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['UnitID'])] = trim($row['UnitShortCode']) . ' | ' . trim($row['UnitDes']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_delivery_location_drop')) {
    function all_delivery_location_drop($status = TRUE)
    {
        $CI =& get_instance();
        $CI->db->select('wareHouseAutoID,wareHouseLocation,wareHouseDescription,wareHouseCode');
        $CI->db->from('srp_erp_warehousemaster');
        $CI->db->where('companyCode', $CI->common_data['company_data']['company_code']);
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Warehouse Location');
        } else {
            $data_arr = '';
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['wareHouseAutoID'])] = trim($row['wareHouseCode']) . ' | ' . trim($row['wareHouseLocation']) . ' | ' . trim($row['wareHouseDescription']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_group_warehouse_drop')) {
    function all_group_warehouse_drop($status = TRUE)
    {
        $CI =& get_instance();
        $CI->db->select('wareHouseAutoID,wareHouseLocation,wareHouseDescription,wareHouseCode');
        $CI->db->from('srp_erp_groupwarehousemaster');
        $CI->db->where('groupID', current_companyID());
        $CI->db->group_by('wareHouseAutoID');
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Warehouse Location');
        } else {
            $data_arr = '';
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['wareHouseAutoID'])] = trim($row['wareHouseCode']) . ' | ' . trim($row['wareHouseLocation']) . ' | ' . trim($row['wareHouseDescription']);
            }
        }

        return $data_arr;
    }
}


if (!function_exists('load_addresstype_drop')) {
    function load_addresstype_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("addressTypeID,addressTypeDescription");
        $CI->db->FROM('srp_erp_addresstype');
        $address = $CI->db->get()->result_array();

        return $address;
    }
}


if (!function_exists('load_employee_drop')) {
    function load_employee_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("EIdNo,Ename2,EmpSecondaryCode");
        $CI->db->FROM('srp_employeesdetails');
        $CI->db->WHERE('Erp_companyID', current_companyID());
        $CI->db->WHERE('empConfirmedYN', 1);
        $CI->db->WHERE('isDischarged', 0);
        $data = $CI->db->get()->result_array();

        $data_arr = []; //array('' => '');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['EIdNo'])] = trim($row['Ename2']) . ' - ' . trim($row['EmpSecondaryCode']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('calculation_addon')) {
    function calculation_addon($grv_full_total, $addon_total_amount, $item_total, $qty)
    {
        $data = array();
        $item_full_total = ($item_total * $qty);
        $data['per'] = (($item_full_total / $grv_full_total) * 100);
        $data['full'] = (($data['per'] / 100) * $addon_total_amount);
        $data['unit'] = ($data['full'] / $qty);
        $data['item_total'] = ($item_full_total + $data['full']);

        return $data;
    }
}

if (!function_exists('generate_filename')) {
    function generate_filename($documentID = NULL, $documentSystemCode = NULL, $extention = NULL)
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        return $companyID . "_" . $documentID . "_" . $documentSystemCode . "_" . time() . $extention;
    }
}

if (!function_exists('document_uploads_url')) {
    function document_uploads_url()
    {
        $url = base_url('uploads') . '/';

        return $url;
    }
}

if (!function_exists('confirm')) {
    function confirm($con)
    {

        $status = '<center>';
        if ($con == 0) {
            $status .= '<span class="label label-danger">&nbsp;</span>';
        } elseif ($con == 1) {
            $status .= '<span class="label label-success">&nbsp;</span>';
        } elseif ($con == 2) {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        } elseif ($con == 3) {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        } else {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}
//new change according to approval change - added  by Nazir
if (!function_exists('confirm_approval')) {
    function confirm_approval($con)
    {
        $status = '<center>';
        if ($con == 0) {
            $status .= '<span class="label label-danger">&nbsp;</span>';
        } elseif ($con == 1) {
            $status .= '<span class="label label-success">&nbsp;</span>';
        } elseif ($con == 2) {
            $status .= '<span class="label label-danger">&nbsp;</span>';
        } elseif ($con == 3) {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        } else {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}
// approvals drill down history - added  by Nazir
if (!function_exists('document_approval_drilldown')) {
    function document_approval_drilldown($con, $code, $autoID)
    {
        $status = '<center>';
        if ($con == 0) {
            $status .= '<a onclick="fetch_all_approval_users_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
        } elseif ($con == 1) {
            $status .= '<a onclick="fetch_approval_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
        } else {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('confirm_user_approval_drilldown')) {
    function confirm_user_approval_drilldown($con, $code, $autoID)
    {
        $status = '<center>';
        if ($con == 0) {
            $status .= '<span class="label label-danger">&nbsp;</span>';
        } elseif ($con == 1) {
            $status .= '<span class="label label-success">&nbsp;</span>';
        } elseif ($con == 2) {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        } elseif ($con == 3) {
            $status .= '<span class="label label-warning">&nbsp;</span>';
            /*            $status .= '<a onclick="approval_refer_back_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-warning"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';*/
        } else {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('confirm_ap_user')) {
    function confirm_ap_user($approved_status, $confirmed_status, $code, $autoID, $isFromLeave = null)
    {
        $status = '<center>';
        if ($approved_status == 0) {
            if ($confirmed_status == 0 || $confirmed_status == 3) {
                $status .= '<span class="label label-danger">&nbsp;</span>';
            } else if ($confirmed_status == 2) {
                $status .= '<a onclick="fetch_approval_reject_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger">';
                $status .= '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
            } else {
                $status .= '<a onclick="fetch_all_approval_users_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger">';
                $status .= '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
            }
        } elseif ($approved_status == 1) {
            if ($confirmed_status == 1) {
                $status .= '<a onclick="fetch_approval_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-success">';
                $status .= '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
            } else {
                $status .= '<span class="label label-success">&nbsp;</span>';
            }
        } elseif ($approved_status == 2) {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        } elseif ($approved_status == 5) {
            $fn = ($isFromLeave == 1) ? 'onclick="fetch_all_approval_users_modal(\'' . $code . '\',' . $autoID . ')"' : '';
            $cls = ($isFromLeave == 1) ? 'cancel-pop-up' : '';
            $string = ($isFromLeave == 1) ? '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>' : '&nbsp;';
            $status .= '<span class="label label-info ' . $cls . '" ' . $fn . '>' . $string . '</span>';
        } elseif ($approved_status == 6) {
            $fn = 'onclick="fetch_approval_reject_user_modal(\'' . $code . '\',' . $autoID . ')"';
            $status .= '<span class="label label-info cancel-pop-up" ' . $fn . '><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></span>';
        } else {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('load_segment_action')) {
    function load_segment_action($segmentID)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'edit_segmrnt("' . $segmentID . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('reversing_approval')) {
    function reversing_approval($documentID, $documentApprovedID, $id)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'reversing_approval_modal("' . $documentID . '","' . $documentApprovedID . '","' . $id . '"); \'><span title="Revise" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>';
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('load_segment_status')) {
    function load_segment_status($segmentID, $state)
    {
        if ($state == 1) {
            $status = '<span class="pull-right">';
            $status .= '<input type="checkbox" id="statusactivate_' . $segmentID . '" name="statusactivate" onchange="changesegmentsatus(' . $segmentID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked>';
            $status .= '</span>';
        } else if ($state == 0) {
            $status = '<span class="pull-right">';
            $status .= '<input type="checkbox" id="statusactivate_' . $segmentID . '" name="statusactivate" onchange="changesegmentsatus(' . $segmentID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0">';
            $status .= '</span>';
        }

        return $status;
    }
}

if (!function_exists('all_employee_drop')) {
    function all_employee_drop($status = TRUE, $isDischarged = 0)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("EIdNo,ECode,Ename1,Ename2,Ename3,Ename4");
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('Erp_companyID', current_companyID());
        $CI->db->where('isPayrollEmployee', 1);
        if ($isDischarged == 1) {
            $CI->db->where('isDischarged !=1 ');
        }
        $customer = $CI->db->get()->result_array();
        if ($status == TRUE) {
            $customer_arr = array('' => $CI->lang->line('common_select_employee'));/*'Select Employee'*/
            if (isset($customer)) {
                foreach ($customer as $row) {
                    $customer_arr[trim($row['EIdNo'])] = trim($row['ECode']) . ' | ' . trim($row['Ename2']);
                }
            }
        } else {
            $customer_arr = $customer;
        }

        return $customer_arr;
    }
}

if (!function_exists('all_group_drop')) {
    function all_group_drop($status = TRUE)
    {
        $CI =& get_instance();
        $CI->db->select("userGroupID,description");
        $CI->db->from('srp_erp_usergroups');
        $CI->db->where('isActive', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $group = $CI->db->get()->result_array();
        if ($status == TRUE) {
            $group_arr = array('' => 'Select Group');
            $group_arr['0'] = $CI->common_data['company_data']['company_name'] . ' company only';
            if (isset($group)) {
                foreach ($group as $row) {
                    $group_arr[trim($row['userGroupID'])] = trim($row['description']);
                }
            }
        } else {
            $group_arr = $group;
        }

        return $group_arr;
    }
}

if (!function_exists('all_document_code_drop')) {
    function all_document_code_drop($status = TRUE)
    {
        $CI =& get_instance();
        $CI->db->select("srp_erp_documentcodemaster.documentID,srp_erp_documentcodemaster.document");
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->join('srp_erp_documentcodes',
            'srp_erp_documentcodes.documentID = srp_erp_documentcodemaster.documentID');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isApprovalDocument', 1);
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Document Code');
        } else {
            $data_arr = '';
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['documentID'])] = trim($row['documentID']) . ' | ' . trim($row['document']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_financeyear_drop')) {
    function all_financeyear_drop($policyFormat = FALSE)
    {
        $convertFormat = convert_date_format();
        $CI =& get_instance();
        $CI->db->select('companyFinanceYearID,beginingDate,endingDate');
        $CI->db->from('srp_erp_companyfinanceyear');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('isActive', 1);
        //$CI->db->where('isCurrent', 1);
        $CI->db->where('isClosed', 0);
        $CI->db->order_by("beginingDate", "desc");
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Financial Year');
        if (isset($data)) {
            foreach ($data as $row) {
                if ($policyFormat) {
                    $data_arr[trim($row['companyFinanceYearID'])] = trim(format_date($row['beginingDate'],
                            $convertFormat)) . ' - ' . trim(format_date($row['endingDate'], $convertFormat));
                } else {
                    $data_arr[trim($row['companyFinanceYearID'])] = trim($row['beginingDate']) . ' - ' . trim($row['endingDate']);
                }
            }
        }

        return $data_arr;
    }
}

if (!function_exists('convertCatType')) {
    function convertCatType($key)
    {
        if ($key == 'A') {
            return 'Addition';
        } else if ($key == 'D') {
            return 'Deduction';
        } else if ($key == 'DC') {
            return 'Deduction';
        } else {
            return '-';
        }
    }
}

if (!function_exists('convertPercentage')) {
    function convertPercentage($per, $type)
    {
        if ($type == 'A') {
            return '-';
        } else if ($type == 'DC') {
            return '-';
        } else if ($type == 'D') {
            return $per . ' %';
        } else {
            return $per . '|' . $type;
        }
    }
}

if (!function_exists('onclickFunction')) {
    function onclickFunction($id, $des, $type, $per, $gl, $CC_Percentage = 0, $CC_GLCode = 0, $payrollCatID = 0, $isPayrollCategory = 1)
    {
        $values = $id . ', \'' . $des . '\', \'' . $type . '\', \'' . $per . '\', \'' . $gl . '\', \'' . $CC_Percentage . '\',\'' . $CC_GLCode . '\'';
        $values .= ',\'' . $payrollCatID . '\',\'' . $isPayrollCategory . '\'';

        $str = '<spsn class="pull-right"><a onclick="editCat( ' . $values . ' )"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span>';
        $str .= ' &nbsp;&nbsp; | &nbsp;&nbsp <a onclick="delete_cat(' . $id . ', \'' . $des . '\', \'' . $type . '\')">';
        $str .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';

        return $str;
    }
}

if (!function_exists('all_banks_drop')) {
    function all_banks_drop()
    {
        $CI =& get_instance();
        $CI->db->select('bankID, bankCode, bankName');
        $CI->db->from('srp_erp_pay_bankmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result();

        return $data;
    }
}

if (!function_exists('currency_conversion')) {
    function currency_conversion($trans_currency, $againce_currency, $amount = 0)
    {
        /*********************************************************************************************
         * Always transaction is going with transaction currency [ Transaction Currency => OMR ]
         * If we want to know the reporting amount [ Reporting Currency => USD ]
         * So the currency_conversion functions 1st parameter will be the USD [ what we looking for ]
         * And the 2nd parameter will be the OMR [what we already got]
         *
         * Ex :
         *    Transaction currency  =>  OMR     => $trCurrency  OR  $trans_currency
         *    Transaction Amount    =>  1000/-  => $trAmount    OR  $amount
         *    Reporting Currency    =>  USD     => $reCurrency  OR  $againce_currency
         *
         *    $conversionData  = currency_conversion($trCurrency, $reCurrency, $trAmount);
         *    $conversionRate  = $conversionData['conversion'];
         *    $decimalPlace    = $conversionData['DecimalPlaces'];
         *    $reportingAmount = round( ($trAmount / $conversionRate) , $decimalPlace );
         **********************************************************************************************/
        $data = array();
        $CI =& get_instance();
        if ($trans_currency == $againce_currency) {
            $CI->db->select('currencyID,CurrencyCode,DecimalPlaces,CurrencyName');
            $CI->db->from('srp_erp_companycurrencyassign');
            $CI->db->where('CurrencyCode', $trans_currency);
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $data_arr = $CI->db->get()->row_array();

            /** Transaction Currency  **/
            $data['trCurrencyID'] = $data_arr['currencyID'];

            /** Conversion currency detail  **/
            $data['currencyID'] = $data_arr['currencyID'];
            $data['conversion'] = 1;
            $data['CurrencyCode'] = $data_arr['CurrencyCode'];
            $data['CurrencyName'] = $data_arr['CurrencyName'];
            $data['DecimalPlaces'] = $data_arr['DecimalPlaces'];
            $data['convertedAmount'] = $amount * 1;
        } else {
            $CI->db->select('srp_erp_currencymaster.currencyID,srp_erp_companycurrencyconversion.masterCurrencyID,conversion,CurrencyCode,CurrencyName,DecimalPlaces');
            $CI->db->from('srp_erp_companycurrencyconversion');
            $CI->db->where('srp_erp_companycurrencyconversion.masterCurrencyCode', $trans_currency);
            $CI->db->where('srp_erp_companycurrencyconversion.subCurrencyCode', $againce_currency);
            $CI->db->where('srp_erp_companycurrencyconversion.companyID', $CI->common_data['company_data']['company_id']);
            $CI->db->join('srp_erp_currencymaster',
                'srp_erp_currencymaster.currencyID = srp_erp_companycurrencyconversion.subCurrencyID');
            $data_arr = $CI->db->get()->row_array();

            /** Transaction Currency  **/
            $data['trCurrencyID'] = $data_arr['masterCurrencyID'];

            /** Conversion currency detail  **/
            $data['currencyID'] = $data_arr['currencyID'];
            $data['conversion'] = round($data_arr['conversion'], 9);
            $data['CurrencyCode'] = $data_arr['CurrencyCode'];
            $data['CurrencyName'] = $data_arr['CurrencyName'];
            $data['DecimalPlaces'] = $data_arr['DecimalPlaces'];
            $data['convertedAmount'] = $amount * $data_arr['conversion'];
        }

        return $data;

        // $data = array();
        // $CI =& get_instance();
        // if ($trans_currency == $local_currency) {
        //     $CI->db->select('CurrencyCode,CurrencyName,DecimalPlaces');
        //     $CI->db->from('srp_erp_currencymaster');
        //     $CI->db->where('CurrencyCode', $trans_currency);
        //     $data_arr = $CI->db->get()->row_array();
        //     $data['conversion']     = 1;
        //     $data['CurrencyCode']   = $data_arr['CurrencyCode'];
        //     $data['CurrencyName']   = $data_arr['CurrencyName'];
        //     $data['DecimalPlaces']  = $data_arr['DecimalPlaces'];
        // } else {
        //     $CI->db->select('conversion,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.CurrencyName,srp_erp_currencymaster.DecimalPlaces');
        //     $CI->db->from('srp_erp_currencyconversion');
        //     $CI->db->where('srp_erp_currencyconversion.masterCurrencyCode', $trans_currency);
        //     $CI->db->where('srp_erp_currencyconversion.subCurrencyCode', $local_currency);
        //     $CI->db->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_currencyconversion.subCurrencyID');
        //     $data_arr = $CI->db->get()->row_array();
        //     $data['conversion']     = $data_arr['conversion'];
        //     $data['CurrencyCode']   = $data_arr['CurrencyCode'];
        //     $data['CurrencyName']   = $data_arr['CurrencyName'];
        //     $data['DecimalPlaces']  = $data_arr['DecimalPlaces'];
        // }
        // return $data;
    }
}

if (!function_exists('currency_conversionID')) {
    function currency_conversionID($trans_currencyID, $againce_currencyID, $amount = 0)
    {
        /*********************************************************************************************
         * Always transaction is going with transaction currency [ Transaction Currency => OMR ]
         * If we want to know the reporting amount [ Reporting Currency => USD ]
         * So the currency_conversion functions 1st parameter will be the USD [what we looking for ]
         * And the 2nd parameter will be the OMR [what we already got]
         *
         * Ex :
         *    Transaction currency  =>  OMR     => $trCurrency  OR  $trans_currencyID
         *    Transaction Amount    =>  1000/-  => $trAmount    OR  $amount
         *    Reporting Currency    =>  USD     => $reCurrency  OR  $againce_currencyID
         *
         *    $conversionData  = currency_conversion($trCurrency, $reCurrency, $trAmount);
         *    $conversionRate  = $conversionData['conversion'];
         *    $decimalPlace    = $conversionData['DecimalPlaces'];
         *    $reportingAmount = round( ($trAmount / $conversionRate) , $decimalPlace );
         **********************************************************************************************/
        $data = array();
        $CI =& get_instance();
        if ($trans_currencyID == $againce_currencyID) {
            $CI->db->select('currencyID,CurrencyCode,DecimalPlaces,CurrencyName');
            $CI->db->from('srp_erp_companycurrencyassign');
            $CI->db->where('currencyID', $trans_currencyID);
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $data_arr = $CI->db->get()->row_array();
            $data['currencyID'] = $data_arr['currencyID'];
            $data['conversion'] = 1;
            $data['CurrencyCode'] = $data_arr['CurrencyCode'];
            $data['CurrencyName'] = $data_arr['CurrencyName'];
            $data['DecimalPlaces'] = $data_arr['DecimalPlaces'];
            $data['convertedAmount'] = $amount * 1;
        } else {
            $CI->db->select('srp_erp_currencymaster.currencyID,conversion,CurrencyCode,CurrencyName,DecimalPlaces');
            $CI->db->from('srp_erp_companycurrencyconversion');
            $CI->db->where('srp_erp_companycurrencyconversion.masterCurrencyID', $trans_currencyID);
            $CI->db->where('srp_erp_companycurrencyconversion.subCurrencyID', $againce_currencyID);
            $CI->db->where('srp_erp_companycurrencyconversion.companyID', $CI->common_data['company_data']['company_id']);
            $CI->db->join('srp_erp_currencymaster',
                'srp_erp_currencymaster.currencyID = srp_erp_companycurrencyconversion.subCurrencyID');
            $data_arr = $CI->db->get()->row_array();
            $data['currencyID'] = $data_arr['currencyID'];
            $data['conversion'] = round($data_arr['conversion'], 9);
            $data['CurrencyCode'] = $data_arr['CurrencyCode'];
            $data['CurrencyName'] = $data_arr['CurrencyName'];
            $data['DecimalPlaces'] = $data_arr['DecimalPlaces'];
            $data['convertedAmount'] = $amount * $data_arr['conversion'];
        }

        return $data;
    }
}

if (!function_exists('getCurrencyID_byCurrencyCode')) {
    function getCurrencyID_byCurrencyCode($currencyCodee)
    {
        $CI =& get_instance();
        $CI->db->select('currencyID');
        $CI->db->from('srp_erp_currencymaster');
        $CI->db->where('CurrencyCode', $currencyCodee);
        $result = $CI->db->get()->row_array();

        return $result['currencyID'];
    }
}


if (!function_exists('load_state_drop')) {
    function load_state_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("stateID,stateDescription");
        $CI->db->FROM('srp_erp_state');
        $state = $CI->db->get()->result_array();

        return $state;
    }
}


if (!function_exists('load_Financial_year_status')) {
    function load_Financial_year_status($companyFinanceYearID, $isActive)
    {
        $status = '<center>';
        if ($isActive == 1) {

            $status .= '<input type="checkbox" id="statusactivate_' . $companyFinanceYearID . '" name="statusactivate" onchange="changeFinancial_yearsatus(' . $companyFinanceYearID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked>';
        } else if ($isActive == 0) {
            $status .= '<input type="checkbox" id="statusactivate_' . $companyFinanceYearID . '" name="statusactivate" onchange="changeFinancial_yearsatus(' . $companyFinanceYearID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0">';

        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('load_Financial_year_current')) {
    function load_Financial_year_current($companyFinanceYearID, $is_current)
    {
        $checked = "";
        $status = '<center>';
        if ($is_current) {
            $checked = "checked";
        }
        $status .= '<input type="radio" onclick="changeFinancial_yearcurrent(' . $companyFinanceYearID . ')"  name="statuscurrent" id="statuscurrent_' . $companyFinanceYearID . '" ' . $checked . '>';
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('load_Financial_year_isactive_status')) {
    function load_Financial_year_isactive_status($companyFinancePeriodID, $isActive)
    {
        $status = '<center>';
        if ($isActive) {
            $status .= '<input type="checkbox" id="isactivesatus_' . $companyFinancePeriodID . '" name="isactivesatus"  data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" onclick="changeFinancial_yearisactivesatus(' . $companyFinancePeriodID . ')" checked>';
            $status .= '</span>';
        } else {
            $status .= '<input type="checkbox" id="isactivesatus_' . $companyFinancePeriodID . '" name="isactivesatus" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" onclick="changeFinancial_yearisactivesatus(' . $companyFinancePeriodID . ')">';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('load_Financial_year_isactive_current')) {
    function load_Financial_year_isactive_current($companyFinancePeriodID, $is_current, $companyFinanceYearID)
    {
        $status = '<center>';
        if ($is_current) {
            $status .= '<input type="radio" onclick="check_financial_period_iscurrent(' . $companyFinancePeriodID . ',' . $companyFinanceYearID . ')" name="iscurrentstatus" id="iscurrentstatus_' . $companyFinancePeriodID . '" checked>';
        } else {
            $status .= '<input type="radio" onclick="check_financial_period_iscurrent(' . $companyFinancePeriodID . ',' . $companyFinanceYearID . ')" name="iscurrentstatus" id="iscurrentstatus_' . $companyFinancePeriodID . '">';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('load_financialperiod_isclosed_closed')) {
    function load_financialperiod_isclosed_closed($companyFinancePeriodID, $is_Close)
    {
        $status = '<center>';
        if ($is_Close) {
            $status .= '<input type="checkbox" id="closefinaperiod_' . $companyFinancePeriodID . '" name="closefinaperiod" onchange="changefinancialperiodclose(' . $companyFinancePeriodID . ')" data-size="mini" data-on-text="Closed" data-handle-width="45" data-on-color="danger" data-off-color="success" data-off-text="Close" data-label-width="0" checked>';
        } else {
            $status .= '<input type="checkbox" id="closefinaperiod_' . $companyFinancePeriodID . '" name="closefinaperiod" onchange="changefinancialperiodclose(' . $companyFinancePeriodID . ')" data-size="mini" data-on-text="Closed" data-handle-width="45" data-on-color="danger" data-off-color="success" data-off-text="Close" data-label-width="0">';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('load_Financial_year_close')) {
    function load_Financial_year_close($companyFinanceYearID, $is_Close)
    {
        $status = '<center>';
        if ($is_Close) {
            $status .= '<input type="checkbox" id="closeactivate_' . $companyFinanceYearID . '" name="closeactivate" onchange="changeFinancial_yearclose(' . $companyFinanceYearID . ')" data-size="mini" data-on-text="Closed" data-handle-width="45" data-on-color="danger" data-off-color="success" data-off-text="Close" data-label-width="0" checked disabled>';
        } else {
            $status .= '<input type="checkbox" id="closeactivate_' . $companyFinanceYearID . '" name="closeactivate" onchange="changeFinancial_yearclose(' . $companyFinanceYearID . ')" data-size="mini" data-on-text="Closed" data-handle-width="45" data-on-color="danger" data-off-color="success" data-off-text="Close" data-label-width="0">';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('edit')) {
    function edit($itemAutoID, $isActive = 0, $isSubItemExist = NULL)
    {
        $status = '<span class="pull-right">';

        if (isset($isSubItemExist) && $isSubItemExist == 1) {
            $status .= '<a class="text-purple" onclick="subItemConfigList_modal(' . $itemAutoID . ');"><span title="Sub Items" rel="tooltip" class="fa fa-list"></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }


        $status .= '<a class="text-yellow" onclick="attachment_modal(' . $itemAutoID . ',\'Item\',\'ITM\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';

        if ($isActive) {
            /*            <input type="checkbox" id="itemchkbox_' . $itemAutoID . '" name="itemchkbox" onchange="changeitemactive(' . $itemAutoID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked><br><br>*/
            $status .= '<a onclick="fetchPage(\'system/item/erp_item_new\',' . $itemAutoID . ',\'Edit Item\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';

            /*| &nbsp;&nbsp;<a onclick="delete_item_master(' . $itemAutoID . ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>*/
        } else {
            $status .= '<a onclick="fetchPage(\'system/item/erp_item_new\',' . $itemAutoID . ',\'Edit Item\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('fetch_payment_bank')) {
    function fetch_payment_bank()
    {
        $CI =& get_instance();
        $CI->db->select('BankID,BankCode,BankName');
        $CI->db->from('srp_bankmaster');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Bank');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['BankID'])] = trim($row['BankCode']) . ' | ' . trim($row['BankName']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('load_countryFlags')) {
    function load_countryFlags()
    {
        $CI =& get_instance();
        $CI->db->select('countryID,countryShortCode,CountryDes');
        $CI->db->from('srp_erp_countrymaster');
        $data_arr = $CI->db->get()->result_array();
        $countries = array('' => 'Select Country');

        if (isset($countries)) {
            foreach ($data_arr as $data) {
                $countries[$data['countryShortCode']] = $data['CountryDes'];
            }
        }
        echo json_encode($countries);
    }
}

if (!function_exists('fetch_segment')) {
    function fetch_segment($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('segmentCode,description,segmentID');
        $CI->db->from('srp_erp_segment');
        $CI->db->where('status', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($state == TRUE) {
            $data_arr = array('' => $CI->lang->line('common_select_segment')/*'Select Segment'*/);
        } else {
            $data_arr = '';
        }
        if (isset($data)) {
            foreach ($data as $row) {
                if ($id) {
                    $data_arr[trim($row['segmentID'])] = trim($row['segmentCode']) . ' | ' . trim($row['description']);
                } else {
                    $data_arr[trim($row['segmentID']) . '|' . trim($row['segmentCode'])] = trim($row['segmentCode']) . ' | ' . trim($row['description']);
                }

            }
        }

        return $data_arr;
    }
}

if (!function_exists('invoice_total_value')) {
    function invoice_total_value($id, $code = 2)
    {
        $tax = 0;
        $CI =& get_instance();
        $CI->db->select_sum('transactionAmount');
        $CI->db->where('invoiceAutoID', $id);
        $transaction_total_amount = $CI->db->get('srp_erp_customerinvoicedetails')->row('transactionAmount');
        $CI->db->select_sum('totalAfterTax');
        $CI->db->where('invoiceAutoID', $id);
        $item_tax = $CI->db->get('srp_erp_customerinvoicedetails')->row('totalAfterTax');
        $totalAmount = ($transaction_total_amount - $item_tax);
        $CI->db->select('taxPercentage');
        $CI->db->where('invoiceAutoID', $id);
        $data_arr = $CI->db->get('srp_erp_customerinvoicetaxdetails')->result_array();
        for ($i = 0; $i < count($data_arr); $i++) {
            $tax += (($data_arr[$i]['taxPercentage'] / 100) * $totalAmount);
        }
        $transaction_total_amount += $tax;

        return number_format($transaction_total_amount, $code);
    }
}

if (!function_exists('contract_total_value')) {
    function contract_total_value($id, $code = 2)
    {
        $tax = 0;
        $CI =& get_instance();
        $CI->db->select_sum('transactionAmount');
        $CI->db->where('contractAutoID', $id);
        $transaction_total_amount = $CI->db->get('srp_erp_contractdetails')->row('transactionAmount');
        // $CI->db->select_sum('totalAfterTax');
        // $CI->db->where('invoiceAutoID', $id);
        // $item_tax = $CI->db->get('srp_erp_customerinvoicedetails')->row('totalAfterTax');
        // $totalAmount = ($transaction_total_amount - $item_tax);
        // $CI->db->select('taxPercentage');
        // $CI->db->where('invoiceAutoID', $id);
        // $data_arr = $CI->db->get('srp_erp_customerinvoicetaxdetails')->result_array();
        // for ($i = 0; $i < count($data_arr); $i++) {
        //     $tax += (($data_arr[$i]['taxPercentage'] / 100) * $totalAmount);
        // }
        // $transaction_total_amount += $tax;
        return number_format($transaction_total_amount, $code);
    }
}

/*if (!function_exists('receipt_voucher_total_value')) {
    function receipt_voucher_total_value($id, $desmal = 2, $status = 1)
    {
        $CI =& get_instance();
        $CI->db->select_sum('transactionAmount');
        $CI->db->where('receiptVoucherAutoId', $id);
        $totalAmount = $CI->db->get('srp_erp_customerreceiptdetail')->row('transactionAmount');
        if ($status) {
            return number_format($totalAmount, $desmal);
        } else {
            return $totalAmount;
        }
    }
}*/

if (!function_exists('load_invoice_action')) {
    function load_invoice_action($poID, $POConfirmedYN, $approved, $createdUserID, $confirmedYN, $isDeleted, $tempInvoiceID)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        if (empty($tempInvoiceID)) {
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Invoice","CINV",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

            if ($isDeleted == 1) {
                $status .= '<a onclick="reOpen_contract(' . $poID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            }

            if ($POConfirmedYN != 1 && $isDeleted == 0) {
                $status .= '<a onclick=\'fetchPage("system/invoices/erp_invoices",' . $poID . ',"Edit Customer Invoice","PO"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            }

            if ($createdUserID == trim($CI->session->userdata("empID")) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0) {
                $status .= '<a onclick="referback_customer_invoice(' . $poID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            }

            $status .= '<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('invoices/load_invoices_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';
            if ($approved == 1) {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="sendemail(' . $poID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
            }
            if ($POConfirmedYN != 1 && $isDeleted == 0) {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="confirmCustomerInvoicefront(' . $poID . ') "><span title="Confirm" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
                $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Invoices\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
            }
        } else {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('invoices/load_invoices_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';
        }


        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('load_invoice_action_buyback')) {
    function load_invoice_action_buyback($poID, $POConfirmedYN, $approved, $createdUserID, $confirmedYN, $isDeleted)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Invoice","HCINV",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        if ($isDeleted == 1) {
            $status .= '<a onclick="reOpen_contract(' . $poID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<a onclick=\'fetchPage("system/invoices/erp_invoices_buyback",' . $poID . ',"Edit Customer Invoice","PO"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($createdUserID == trim($CI->session->userdata("empID")) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<a onclick="referback_customer_invoice(' . $poID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'HCINV\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

        $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('InvoicesPercentage/load_invoices_conformation_buyback/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Invoices\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('load_contract_action')) {
    function load_contract_action($poID, $POConfirmedYN, $approved, $createdUserID, $documentID, $confirmedYN, $isDeleted)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        if ($isDeleted == 1) {
            $status .= '<a onclick="reOpen_contract(' . $poID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        } else if ($documentID == "QUT") {
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Quotation","' . $documentID . '","' . $confirmedYN . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        } else if ($documentID == "CNT") {
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Contract","' . $documentID . '","' . $confirmedYN . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        } else if ($documentID == "SO") {
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Sales Order","' . $documentID . '","' . $confirmedYN . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        } else {
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"' . $documentID . '","' . $documentID . '","' . $confirmedYN . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }
        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<a onclick=\'fetchPage("system/quotation_contract/erp_quotation_contract",' . $poID . ',"Edit Quotation or Contract","PO"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }

        if ($createdUserID == trim($CI->session->userdata("empID")) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<a onclick="referback_customer_contract(' . $poID . ',\'' . $documentID . '\');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'' . $documentID . '\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        $status .= '<a onclick="document_drill_down_View_modal(\'' . $poID . '\',\'' . $documentID . '\')"><i title="Drill Down" rel="tooltip" class="fa fa-bars" aria-hidden="true"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        $status .= '<a target="_blank" href="' . site_url('Quotation_contract/load_contract_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($documentID == 'QUT' && $isDeleted == 0) {
            if ($POConfirmedYN == 1 and $approved == 1) {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="document_version_View_modal(\'' . $documentID . '\',\'' . $poID . '\')"><i title="Documents" rel="tooltip" class="fa fa-files-o" aria-hidden="true"></i></a>&nbsp;&nbsp;|';
                $status .= ' <a onclick="sendemail(' . $poID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>&nbsp;&nbsp;';
            }
        }
        if ($documentID == 'CNT' && $isDeleted == 0) {
            if ($POConfirmedYN == 1 and $approved == 1) {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="sendemail(' . $poID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
            }
        }
        if ($documentID == 'SO' && $isDeleted == 0) {
            if ($POConfirmedYN == 1 and $approved == 1) {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="sendemail(' . $poID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
            }
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'' . $documentID . '\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        /*        if ($POConfirmedYN != 0 && $POConfirmedYN != 2) {
                    $status .= '<a target="_blank" onclick="documentPageView_modal(\'' . $documentID . '\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
                    $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Quotation_contract/load_contract_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
                }*/
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('load_rv_action')) {
    function load_rv_action($poID, $POConfirmedYN, $approved, $createdUserID, $isDeleted)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Receipt Voucher","RV",' . $POConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        if ($isDeleted == 1) {
            $status .= '<a onclick="reOpen_contract(' . $poID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<a onclick=\'fetchPage("system/receipt_voucher/erp_receipt_voucher",' . $poID . ',"Edit Receipt Voucher","PO"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($createdUserID == trim($CI->session->userdata("empID")) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<a onclick="referback_receipt_voucher(' . $poID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'RV\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        $status .= '<a target="_blank" href="' . site_url('Receipt_voucher/load_rv_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Receipt Voucher\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('inv_action_approval')) {
    function inv_action_approval($poID, $Level, $approved, $ApprovedID, $documentID, $approval = 1)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Invoice","CINV");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';

        } else {
            $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '","' . $poID . '","","' . $approval . '"  ); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        //$status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';


        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('con_action_approval')) {
    function con_action_approval($poID, $Level, $approved, $ApprovedID, $document, $isRejected)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"' . $document . '","' . $document . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';

        } else {
            $status .= '<a onclick=\'documentPageView_modal("' . $document . '","' . $poID . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp';
        }
        //$status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'' . $document . '\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';


        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('RV_action_approval')) {
    function RV_action_approval($poID, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $status = '<span class="pull-right">';
        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= ' &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'RV\',\'' . $poID . '\',\' \',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';

        }

        // $status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Receipt_voucher/load_rv_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('je_total_value')) {
    function je_total_value($totalAmount, $currency)
    {
        return number_format($totalAmount, $currency);
    }
}

if (!function_exists('clear_descriprions')) {
    function clear_descriprions($descriprions)
    {
        return htmlentities(str_replace(array('"', "'"), ' ', $descriprions));
    }
}

if (!function_exists('jv_approval')) {
    function jv_approval($poID, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $status = '<span class="pull-right">';
        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '&nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'JE\', ' . $poID . ',\' \', ' . $approval . ')"> <span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a> &nbsp;&nbsp;';
        }


        //$status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Journal_entry/journal_entry_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('get_employee_currency')) {
    function get_employee_currency($empID, $returnType)
    {
        $CI =& get_instance();

        $CI->db->select("cur.CurrencyCode AS code, DecimalPlaces")
            ->from("srp_employeesdetails AS emp")
            ->join("srp_erp_currencymaster AS cur", "cur.currencyID = emp.payCurrencyID")
            ->where("EIdNo='$empID'");
        $currency = $CI->db->get()->row();


        if ($returnType == 'c_code') {
            $val = $currency->code;
        } elseif ($returnType == '') {
            $val = $currency->DecimalPlaces;
        } elseif ($returnType == 'det') {
            $val = $currency;
        } else {
            $val = $currency->DecimalPlaces;
        }

        return $val;
    }
}

if (!function_exists('conversionRateUOM')) {
    function conversionRateUOM($umo, $default_umo)
    {
        $CI =& get_instance();
        $comm_id = $CI->common_data['company_data']['company_id'];
        $CI->db->select('UnitID');
        $CI->db->where('UnitShortCode', $default_umo);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $masterUnitID = $CI->db->get('srp_erp_unit_of_measure')->row('UnitID');

        $CI->db->select('UnitID');
        $CI->db->where('UnitShortCode', $umo);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $subUnitID = $CI->db->get('srp_erp_unit_of_measure')->row('UnitID');

        $CI->db->select('conversion');
        $CI->db->from('srp_erp_unitsconversion');
        $CI->db->where('masterUnitID', $masterUnitID);
        $CI->db->where('subUnitID', $subUnitID);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);

        return $CI->db->get()->row('conversion');
    }
}

if (!function_exists('conversionRateUOM_id')) {
    function conversionRateUOM_id($subUnitID, $masterUnitID)
    {
        $CI =& get_instance();
        $comm_id = $CI->common_data['company_data']['company_id'];
        $CI->db->select('conversion');
        $CI->db->from('srp_erp_unitsconversion');
        $CI->db->where('masterUnitID', $masterUnitID);
        $CI->db->where('subUnitID', $subUnitID);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);

        return $CI->db->get()->row('conversion');
    }
}

if (!function_exists('load_bank_with_card')) {
    function load_bank_with_card()
    {
        $CI =& get_instance();
        $CI->db->select('GLAutoID, bankName, bankBranch, GLSecondaryCode, systemAccountCode, GLDescription');
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('isBank', 1);
        $CI->db->where('isCard', 1);

        return $CI->db->get()->result_array();
    }
}

/**** Added by mubashir ***/
if (!function_exists('fetch_item_data_by_company')) {
    function fetch_item_data_by_company()
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_itemmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('financeCategory', 1);

        return $CI->db->get()->result_array();
    }
}

if (!function_exists('fetch_group_item_data_by_company')) {
    function fetch_group_item_data_by_company()
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_groupitemmaster');
        $CI->db->join('srp_erp_groupitemmasterdetails', 'srp_erp_groupitemmaster.itemAutoID = srp_erp_groupitemmasterdetails.groupItemMasterID', 'INNER');
        $CI->db->where('srp_erp_groupitemmaster.groupID', current_companyID());
        $CI->db->where('financeCategory', 1);
        $CI->db->group_by('srp_erp_groupitemmaster.itemAutoID');

        return $CI->db->get()->result_array();
    }
}

/*display serverside warning message for reporting purpose*/
if (!function_exists('warning_message')) {
    function warning_message($message)
    {
        return '<div class="callout callout-warning">
               ' . $message . '
              </div>';
    }
}

/*export excel and pdf button*/
if (!function_exists('export_buttons')) {
    function export_buttons($id, $fileName, $excel = TRUE, $pdf = TRUE, $btnSize = 'btn-xs', $functionName = 'generateReportPdf()')
    {
        $export = '<div class="pull-right">';
        if ($pdf) {
            $export .= '<button class="btn btn-pdf ' . $btnSize . '" id="btn-pdf" type="button" onclick="' . $functionName . '">
                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
            </button> ';
        }
        if ($excel) {
            $export .= '<a href="" class="btn btn-excel ' . $btnSize . '" id="btn-excel" download="' . $fileName . '.xls"
               onclick="var file = tableToExcel(\'' . $id . '\', \'' . $fileName . '\'); $(this).attr(\'href\', file);">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
            </a>';
        }
        $export .= '</div>';

        return $export;
    }
}

/*display local and reporting currency for reporting purpose*/
if (!function_exists('show_report_currency')) {
    function show_report_currency()
    {
        $CI =& get_instance();

        return '<div class="col-md-12">
            <strong>Currency: </strong>' . $CI->common_data['company_data']['company_default_currency'] . '|' . $CI->common_data['company_data']['company_reporting_currency'] . '</div>';
    }
}

/*get financial year for a perticular date*/
if (!function_exists('get_financial_year')) {

    function get_financial_year($date)
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_companyfinanceyear');
        $CI->db->WHERE('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where("'{$date}' BETWEEN beginingDate AND endingDate");

        return $CI->db->get()->row_array();
    }
}

/*get group financial year for a perticular date*/
if (!function_exists('get_group_financial_year')) {

    function get_group_financial_year($date)
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_groupfinanceyear');
        $CI->db->WHERE('groupID', current_companyID());
        $CI->db->where("'{$date}' BETWEEN beginingDate AND endingDate");

        return $CI->db->get()->row_array();
    }
}

/*print debit credit*/
if (!function_exists('print_debit_credit')) {
    function print_debit_credit($amount, $decimalPlace = 2, $GLAutoID = NULL, $masterCategory = NULL, $GLDescription = NULL, $currency = NULL, $isLink = FALSE, $month = NULL)
    {
        if ($isLink) {
            if ($amount < 0) {
                return '<td class="text-right">-</td><td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $GLAutoID . '\',\'' . $masterCategory . '\',\'' . $GLDescription . '\',\'' . $currency . '\',\'' . $month . '\')">' . number_format(abs($amount),
                        $decimalPlace) . '</a></td>';
            } else {
                if ($amount > 0) {
                    return '<td  class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $GLAutoID . '\',\'' . $masterCategory . '\',\'' . $GLDescription . '\',\'' . $currency . '\',\'' . $month . '\')">' . number_format($amount,
                            $decimalPlace) . '</a></td><td class="text-right">-</td>';
                } else {
                    return "<td  class='text-right'>-</td><td class='text-right'>-</td>";
                }
            }
        } else {
            if ($amount < 0) {
                return "<td class='text-right'>-</td><td class='text-right'>" . number_format(abs($amount),
                        $decimalPlace) . "</td>";
            } else {
                if ($amount > 0) {
                    return "<td  class='text-right'>" . number_format($amount,
                            $decimalPlace) . "</td><td class='text-right'>-</td>";
                } else {
                    return "<td  class='text-right'>-</td><td class='text-right'>-</td>";
                }
            }
        }
    }
}

if (!function_exists('print_debit_credit_pdf')) {
    function print_debit_credit_pdf($amount, $decimalPlace = 2, $GLAutoID = NULL, $masterCategory = NULL, $GLDescription = NULL, $currency = NULL, $isLink = FALSE, $month = NULL)
    {
        if ($isLink) {
            if ($amount < 0) {
                return '<td class="text-right">-</td><td align="right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $GLAutoID . '\',\'' . $masterCategory . '\',\'' . $GLDescription . '\',\'' . $currency . '\',\'' . $month . '\')">' . number_format(abs($amount),
                        $decimalPlace) . '</a></td>';
            } else {
                if ($amount > 0) {
                    return '<td  align="right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $GLAutoID . '\',\'' . $masterCategory . '\',\'' . $GLDescription . '\',\'' . $currency . '\',\'' . $month . '\')">' . number_format($amount,
                            $decimalPlace) . '</a></td><td align="right">-</td>';
                } else {
                    return "<td  class='text-right'>-</td><td align='right'>-</td>";
                }
            }
        } else {
            if ($amount < 0) {
                return "<td class='text-right'>-</td><td align='right'>" . number_format(abs($amount),
                        $decimalPlace) . "</td>";
            } else {
                if ($amount > 0) {
                    return "<td  align='right'>" . number_format($amount,
                            $decimalPlace) . "</td><td align='right'>-</td>";
                } else {
                    return "<td  align='right'>-</td><td align='right'>-</td>";
                }
            }
        }
    }
}

// Get a set of date beetween the 2 period
if (!function_exists('get_month_list_from_date')) {
    function get_month_list_from_date($beginingDate, $endDate, $format, $intervalType, $caption = "MY")
    {
        $start = new DateTime($beginingDate); // beginingDate
        $end = new DateTime($endDate); // endDate
        $interval = DateInterval::createFromDateString($intervalType); // 1 month interval
        $period = new DatePeriod($start, $interval, $end); // Get a set of date beetween the 2 period
        $months = array();
        foreach ($period as $dt) {
            if ($caption == 'MY') {
                $months[$dt->format($format)] = $dt->format("M") . "-" . $dt->format("Y");
            } else if ($caption == 'M') {
                $months[$dt->format($format)] = $dt->format("M");
            } else if ($caption == 'My') {
                $months[$dt->format($format)] = $dt->format("M") . "-" . $dt->format("y");
            }
        }

        return $months;
    }
}
/*get last two financial year*/
if (!function_exists('get_last_two_financial_year')) {

    function get_last_two_financial_year()
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_companyfinanceyear');
        $CI->db->WHERE('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('isActive', 1);
        $CI->db->ORDER_BY('beginingDate DESC');

        return $CI->db->get()->result_array();
    }
}
/*get format number for datatable with extended style*/
if (!function_exists('dashboard_format_number')) {
    function dashboard_format_number($amount = 0, $decimal_place = 2)
    {
        if ($amount == 0) {
            return "<span class='text-muted'>" . number_format($amount, $decimal_place) . "</span>";
        } else if ($amount > 0) {
            return "<span class='text-green'>" . number_format($amount, $decimal_place) . "</span>";
        } else {
            return "<span class='text-red'>" . number_format($amount, $decimal_place) . "</span>";
        }
    }
}

/*get format number for report without rounding the amount with extended style*/
if (!function_exists('report_format_number')) {
    function report_format_number($amount = 0)
    {
        //$amount = explode(".",$amount);
        $commaSepAmount = preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $amount);

        return $commaSepAmount;
    }
}


/*color due days*/
if (!function_exists('dashboard_color_duedays')) {
    function dashboard_color_duedays($days)
    {
        if ($days <= 5) {
            return "<span class='badge bg-red'>" . $days . "</span>";
        } else if ($days > 5 && $days <= 10) {
            return "<span class='badge bg-green'>" . $days . "</span>";
        } else {
            return "<span class='badge bg-default'>" . $days . "</span>";
        }
    }
}

/*get all financial year for dropdown*/
if (!function_exists('all_financeyear_report_drop')) {
    function all_financeyear_report_drop($policyFormat = FALSE)
    {
        $CI =& get_instance();
        $CI->db->select('companyFinanceYearID,beginingDate,endingDate');
        $CI->db->from('srp_erp_companyfinanceyear');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Financial Year');
        if (isset($data)) {
            foreach ($data as $row) {
                if ($policyFormat) {
                    $data_arr[trim($row['companyFinanceYearID'])] = convert_date_format(trim($row['beginingDate'])) . ' - ' . convert_date_format(trim($row['endingDate']));
                } else {
                    $data_arr[trim($row['companyFinanceYearID'])] = trim($row['beginingDate']) . ' - ' . trim($row['endingDate']);
                }

            }
        }

        return $data_arr;
    }
}

/*get all group financial year for dropdown*/
if (!function_exists('all_group_financeyear_report_drop')) {
    function all_group_financeyear_report_drop($policyFormat = FALSE)
    {
        $CI =& get_instance();
        $CI->db->select('groupFinanceYearID,beginingDate,endingDate');
        $CI->db->from('srp_erp_groupfinanceyear');
        $CI->db->where('groupID', current_companyID());
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Financial Year');
        if (isset($data)) {
            foreach ($data as $row) {
                if ($policyFormat) {
                    $data_arr[trim($row['groupFinanceYearID'])] = convert_date_format(trim($row['beginingDate'])) . ' - ' . convert_date_format(trim($row['endingDate']));
                } else {
                    $data_arr[trim($row['groupFinanceYearID'])] = trim($row['beginingDate']) . ' - ' . trim($row['endingDate']);
                }

            }
        }

        return $data_arr;
    }
}

/**** End Mubashir ***/

/*bank rec - shahmy*/

if (!function_exists('load_bank_rec_action')) {
    function load_bank_rec_action($glCode)
    {
        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'fetchPage("system/bank_rec/erp_bank_reconciliation_bank_summary","' . $glCode . '","Bank Reconciliation ","Bank Reconciliation"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" ></span>';

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('load_bank_rec_summary_action')) {
    function load_bank_rec_summary_action($glCode, $bankRecAutoID, $confirmYN, $approvedYN, $createdUserID)
    {
        $data = $glCode . '|' . $bankRecAutoID;
        $dataarray = array($glCode, $bankRecAutoID);
        $status = '<span class="pull-right">';
        $CI =& get_instance();

        /**/
        $CI->db->select('bankRecMonthID');
        $CI->db->from('srp_erp_bankledger');
        $CI->db->where('bankRecMonthID', $bankRecAutoID);
        $datas = $CI->db->get()->row_array();


        $status .= '<a onclick=\'attachment_modal(' . $bankRecAutoID . ',"Bank Reconciliation","BR");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';


        if (($approvedYN == 0 && $confirmYN == 1)) {
            $status .= '<a onclick="referback_bankrec(' . $bankRecAutoID . ');"><span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }

        if ($confirmYN == 0 || $confirmYN == 3 || $confirmYN == 2) {
            $status .= '<a onclick=\'fetchPage("system/bank_rec/erp_bank_reconciliation_new","' . $data . '","Bank Reconciliation  ","Bank Reconciliation ","BR"); \'><span class="glyphicon glyphicon-pencil" ></span>';
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
        }


        $status .= '<a target="_blank" onclick="documentPageView_modal(\'BR\',\'' . $bankRecAutoID . '\',\'' . $glCode . '\')" ><span class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        $status .= '<a target="_blank" href="' . site_url('Bank_rec/bank_rec_book_balance/') . '/' . $bankRecAutoID . '/' . $glCode . '" ><span class="glyphicon glyphicon-print"></span></a>';

        if (empty($datas['bankRecMonthID'])) {
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_bankrec(' . $bankRecAutoID . ');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        //  $status .='<a target="_blank" href="/srp_new/index.php/Bank_rec/bank_rec_confirmation/'.$bankRecAutoID.'"><span class="glyphicon glyphicon-print"></span></a>';


        $status .= '</span>';

        return $status;
    }
}

/*end of bank rec*/

if (!function_exists('bankrec_approval')) {
    function bankrec_approval($bankRecAutoID, $bankGLAutoID, $approvalLevelID, $approvedYN, $documentApprovedID)
    {
        $status = '<span class="pull-right">';
        if ($approvedYN == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $bankRecAutoID . '","' . $bankGLAutoID . '","' . $documentApprovedID . '","' . $approvalLevelID . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'BR\',\'' . $bankRecAutoID . '\',\'' . $bankGLAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        //$status .= '<a target="_blank" href="' . site_url('Bank_rec/bank_rec_book_balance/') . '/' . $bankRecAutoID . '/' . $bankGLAutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '</span>';

        return $status;
    }
}

/**/
if (!function_exists('fetch_by_gl_codes')) {
    function fetch_by_gl_codes($codes = NULL)
    {
        $CI =& get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory");
        $CI->db->from('srp_erp_chartofaccounts');
        if ($codes) {
            foreach ($codes as $key => $code) {
                $CI->db->where($key, $code);
            }
        }
        $CI->db->where('controllAccountYN', 1);
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select GL Code');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_all_location')) {
    function fetch_all_location()
    {
        $CI =& get_instance();
        $CI->db->SELECT("locationID,locationName");
        $CI->db->from('srp_erp_location');
        $CI->db->where('companyID', current_companyID());
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Location');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['locationID'])] = trim($row['locationName']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_master_category')) {
    function fetch_master_category()
    {
        $CI =& get_instance();
        $CI->db->SELECT("faCatID,catDescription");
        $CI->db->from('srp_erp_fa_category');
        $CI->db->where('isActive', 1);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Main Category');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['faCatID'])] = trim($row['catDescription']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_sub_category')) {
    function fetch_sub_category($masterCat)
    {
        $CI =& get_instance();
        $CI->db->SELECT("faCatSubID,catDescription,faCatID");
        $CI->db->from('srp_erp_fa_categorysub');
        $CI->db->where('isActive', 1);
        $CI->db->where('faCatID', $masterCat);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Sub Category');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['faCatSubID'])] = trim($row['catDescription']);
            }
        }

        return $data_arr;
    }
}
/**/

if (!function_exists('get_documentCode')) {
    function get_document_code($documentID)
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $CI->db->select('prefix,startSerialNo,serialNo');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        /*    $CI->db->where('companyID', $companyID);*/
        $data = $CI->db->get()->row_array();

        return $data;
    }
}

if (!function_exists('bank_transaction_edit')) {
    function bank_transaction_edit($transactionID, $confirmYN, $approvedYN, $createduserID, $transferType, $fromBankGLAutoID)
    {
        $documentArray = 'transactionID:' . $transactionID;
        $functionCall = 'Bank_rec/bank_transfer_view';
        $status = '<span class="pull-right">';
        $CI =& get_instance();
        $CI->db->select('COUNT(`srp_erp_chartofaccountchequetemplates`.`coaChequeTemplateID`) as templateCount');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('GLAutoID', $fromBankGLAutoID);
        $CI->db->join('srp_erp_systemchequetemplates', 'srp_erp_chartofaccountchequetemplates.systemChequeTemplateID = srp_erp_systemchequetemplates.chequeTemplateID', 'left');
        $CI->db->from('srp_erp_chartofaccountchequetemplates');
        $count = $CI->db->get()->row_array();

        $CI->db->select('coaChequeTemplateID');
        $CI->db->where('GLAutoID', $fromBankGLAutoID);
        $CI->db->where('companyID', current_companyID());
        $templateexist = $CI->db->get('srp_erp_chartofaccountchequetemplates')->row_array();

        if ($transferType == 2 && $approvedYN == 1 && !empty($templateexist)) {
            $status .= '<a onclick=cheque_print_modal(' . $transactionID . ',' . $count['templateCount'] . ',' . $templateexist['coaChequeTemplateID'] . '); ><i title="Cheque Print" rel="tooltip" class="fa fa-cc" aria-hidden="true"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        if ($approvedYN != 1 && $confirmYN == 1 && ($createduserID == current_userID())) {
            $status .= '<a onclick="referbackgrv(' . $transactionID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }
        if ($confirmYN == 0 || $confirmYN == 3 || $confirmYN == 2) {
            $status .= '&nbsp;&nbsp;<a onclick=\'bank_transaction_edit("' . $transactionID . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

            $status .= '<a onclick="delete_item(' . $transactionID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;';
        }
        $status .= '<a target="_blank" onclick="documentPageView_modal(\'BT\',\'' . $transactionID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        $status .= '<a target="_blank" href="' . site_url('Bank_rec/bank_transfer_view/') . '/' . $transactionID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';


        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('bank_transfer_approval')) {
    function bank_transfer_approval($bankTransferAutoID, $approvalLevelID, $approvedYN, $documentApprovedID)
    {
        $status = '<span class="pull-right">';
        if ($approvedYN == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $bankTransferAutoID . '","' . $documentApprovedID . '","' . $approvalLevelID . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'BT\',\'' . $bankTransferAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        // $status .= '<a target="_blank" href="' . site_url('Bank_rec/bank_transfer_view/') . '/' . $bankTransferAutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('load_budget_action')) {
    function load_budget_action($budgetAutoID,$confirmedYN)
    {
        $status = '<span class="pull-right">';
        if($confirmedYN !=1)
        {
            $status .= '<a onclick=\'fetchPage("system/budget/erp_budget_detail_page","' . $budgetAutoID . '","Budget Detail ","Budget Detail"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" ></span>';
        }
        if ($confirmedYN == 1) {
            $status .= '<a onclick="referbackbudget(' . $budgetAutoID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';
        }


        $status .= '</span>';

        return $status;
    }
}
/*bank register*/
if (!function_exists('load_bank_register_action')) {
    function load_bank_register_action($glCode, $from, $to)
    {
        $data = $from . '_' . $to;
        // echo $to;
        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'fetchPage("system/bank_register/erp_bank_register_details","' . $glCode . '","Bank Register ","Bank Register","' . $data . '"); \'><span class="glyphicon glyphicon-eye-open" ></span>';


        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('company_PL_bank_account_drop')) {
    function company_PL_bank_account_drop($status = TRUE)
    {
        $CI =& get_instance();
        $CI->db->select("GLAutoID,systemAccountCode,
    GLSecondaryCode,
    GLDescription");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('masterCategory', 'PL');
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('controllAccountYN', 0);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $bank = $CI->db->get()->result_array();
        $bank_arr = array('' => 'Select GL Account');
        if (isset($bank)) {
            foreach ($bank as $row) {
                $bank_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']);
            }
        }

        return $bank_arr;
    }
}


if (!function_exists('fetch_all_gl_codes_report')) { /*fetch all gl codes except controll accounts and master accounts*/
    function fetch_all_gl_codes_report()
    {
        $CI =& get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('isActive', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array();
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_all_group_gl_codes_report')) { /*fetch all group gl codes except controll accounts and master accounts*/
    function fetch_all_group_gl_codes_report()
    {
        $CI =& get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory");
        $CI->db->from('srp_erp_groupchartofaccounts');
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('isActive', 1);
        $CI->db->where('groupID', current_companyID());
        $data = $CI->db->get()->result_array();
        $data_arr = array();
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('dropdown_currencyAssigned')) { /*fetch all gl codes except controll accounts and master accounts*/
    function dropdown_currencyAssigned()
    {

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("select concat(CurrencyCode,' | ',CurrencyName) as currencyNmae,concat(currencyID,'|',CurrencyCode,'|',DecimalPlaces) as currency from srp_erp_currencymaster
WHERE NOT EXISTS (select null from srp_erp_companycurrencyassign WHERE companyID={$companyID} AND srp_erp_companycurrencyassign.currencyID=srp_erp_currencymaster.currencyID)")->result_array();
        $data_arr = array();
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['currency'])] = trim($row['currencyNmae']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('dropdown_currencyAssignedExchangeDropdown')) { /*fetch all gl codes except controll accounts and master accounts*/
    function dropdown_currencyAssignedExchangeDropdown()
    {

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("SELECT srp_erp_currencymaster.currencyID,concat(srp_erp_currencymaster.CurrencyCode,' | ',srp_erp_currencymaster.CurrencyName) as currencyName FROM srp_erp_companycurrencyassign LEFT JOIN srp_erp_currencymaster on srp_erp_companycurrencyassign.currencyID=srp_erp_currencymaster.currencyID
WHERE companyID = {$companyID}")->result_array();
        $data_arr = array();
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['currencyID'])] = trim($row['currencyName']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_currency_ID')) {
    function fetch_currency_ID($code)
    {
        $CI =& get_instance();
        $CI->db->SELECT("currencyID");
        $CI->db->FROM('srp_erp_currencymaster');
        $CI->db->WHERE('CurrencyCode', $code);

        return $CI->db->get()->row('currencyID');
    }
}

if (!function_exists('get_currency_code')) {
    function get_currency_code($cuID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("CurrencyCode");
        $CI->db->FROM('srp_erp_currencymaster');
        $CI->db->WHERE('currencyID', $cuID);

        return $CI->db->get()->row('CurrencyCode');
    }
}

if (!function_exists('fetch_account_review')) {
    function fetch_account_review($AccountReviewState = TRUE, $printState = TRUE, $approval = 0)
    {
        if ($approval == 1) {
            if ($AccountReviewState) {
                $html = '<div id="" class="row review hide"> <div class="col-md-12"> <span class="no-print pull-right"> <a class="btn btn-default btn-sm de_link" id="de_link" target="_blank" href="#"><span class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp;&nbsp;&nbsp;Account Review Entries </a>  </span> </div> </div>';

                return $html;
            }
        } else {
            if ($AccountReviewState && $printState) {
                $html = '<div id="" class="row review hide"> <div class="col-md-12"> <span class="no-print pull-right"> <a class="btn btn-default btn-sm de_link" id="de_link" target="_blank" href="#"><span class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp;&nbsp;&nbsp;Account Review Entries </a> <a class="btn btn-default btn-sm no-print pull-right" id="a_link" target="_blank" href="#"> <span class="glyphicon glyphicon-print" aria-hidden="true"></span> </a> </span> </div> </div>';

                return $html;
            } else if ($AccountReviewState) {
                $html = '<div id="" class="row review hide"> <div class="col-md-12"> <span class="no-print pull-right"> <a class="btn btn-default btn-sm de_link" id="de_link" target="_blank" href="#"><span class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp;&nbsp;&nbsp;Account Review Entries </a>  </span> </div> </div>';

                return $html;
            } else if ($printState) {
                $html = '<div id="" class="row review hide"> <div class="col-md-12"> <span class="no-print pull-right"> <a class="btn btn-default btn-sm no-print pull-right" id="a_link" target="_blank" href="#"> <span class="glyphicon glyphicon-print" aria-hidden="true"></span> </a> </span> </div> </div>';

                return $html;
            }
        }

    }
}

if (!function_exists('dropdown_erp_usergroups')) { /*fetch all gl codes except controll accounts and master accounts*/
    function dropdown_erp_usergroups($companyID)
    {

        $CI =& get_instance();
        if (!isset($companyID) || $companyID == '') {
            $companyID = $CI->common_data['company_data']['company_id'];

            return $customer_arr = array('' => 'Select');
        }


        $data = $CI->db->query("SELECT userGroupID,description FROM `srp_erp_usergroups` where isActive=1  AND companyID = {$companyID}")->result_array();
        $data_arr = array('' => 'All');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['userGroupID'])] = trim($row['description']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('edit_employee_nav_access')) {
    function edit_employee_nav_access($itemAutoID)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick="delete_item(' . $itemAutoID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('all_not_accessed_employee')) {
    function all_not_accessed_employee($companyID)
    {
        $CI =& get_instance();
        $customer_arr = array();
        /* if(!isset($companyID) || $companyID ==''){
             return $customer_arr=array();
         }*/
        $companyIDd = current_companyID();
        //$customer=  $CI->db->query("SELECT EIdNo,ECode,Ename1,Ename2,Ename3,Ename4 FROM srp_employeesdetails LEFT join srp_erp_employeenavigation  ON EIdNo = empID AND companyID={$companyID} WHERE Erp_companyID ={$companyID} AND employeeNavigationID is null ")->result_array();

        /* $customer=$CI->db->query("SELECT * FROM srp_erp_companygroupdetails INNER JOIN srp_employeesdetails ON srp_erp_companygroupdetails.companyID = srp_employeesdetails.Erp_companyID LEFT JOIN srp_erp_employeenavigation on EIdNo=empID AND srp_erp_employeenavigation.companyID={$companyID} WHERE companyGroupID = (SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}) AND employeeNavigationID is null")->result_array();*/
        $customer = FALSE;
        if ($companyID != '') {
            $customer = $CI->db->query("select * from srp_employeesdetails
LEFT JOIN srp_erp_employeenavigation on companyID=$companyID AND empID =EidNo
WHERE Erp_companyID=$companyIDd AND employeeNavigationID is null AND isDischarged=0")->result_array();
        }


        if ($customer) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['EIdNo'])] = trim($row['Ename2']);
            }
        }

        return $customer_arr;
    }
}


if (!function_exists('drop_down_group_of_companies')) {
    function Drop_down_group_of_companies($status = TRUE)
    {
        $CI =& get_instance();
        $group_company_arr = array();
        $companyID = current_companyID();
        $companyGroup = $CI->db->query("SELECT parentID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $group_company = $CI->db->query("select company_id as companyID,CONCAT(company_code,' | ',company_name) as company from `srp_erp_company` where company_id={$companyID} ")->result_array();
        if (!empty($companyGroup)) {
            $group_company_arr = array();
            $group_company = $CI->db->query("SELECT companyID, CONCAT(company_code, ' - ', company_name) AS company FROM srp_erp_companygroupdetails INNER JOIN `srp_erp_company` ON company_id = companyID WHERE parentID = (SELECT parentID FROM srp_erp_companygroupdetails WHERE companyID = {$companyID}) ")->result_array();
        }
        if ($status) {
            $group_company_arr = array('' => 'Select a Company');
        }
        if (isset($group_company)) {
            foreach ($group_company as $row) {
                $group_company_arr[trim($row['companyID'])] = trim($row['company']);
            }
        }

        return $group_company_arr;
    }
}


if (!function_exists('erp_navigation_usergroups')) {
    function erp_navigation_usergroups()
    {

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("SELECT userGroupID,description FROM `srp_erp_usergroups` where isActive=1  AND companyID = {$companyID}")->result_array();
        $data_arr = array();
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['userGroupID'])] = trim($row['description']);
            }
        }

        return $data_arr;
    }
}

/*journal Entry Action*/

if (!function_exists('journal_entry_action')) {
    function journal_entry_action($JVMasterAutoId, $confirmedYN, $approvedYN, $createdUserID, $isDeleted, $JVType)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        if ($JVType == 'Recurring') {
            $status .= '<a onclick=\'recurring_attachment_modal(' . $JVMasterAutoId . ',"Journal Entry","JV",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        } else {
            $status .= '<a onclick=\'attachment_modal(' . $JVMasterAutoId . ',"Journal Entry","JV",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        if ($isDeleted == 1) {
            $status .= '<a onclick="reOpen_contract(' . $JVMasterAutoId . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        if ($confirmedYN != 1 && $isDeleted == 0) {
            $status .= '<a onclick=\'fetchPage("system/finance/journal_entry_new",' . $JVMasterAutoId . ',"Edit Journal Entry","Journal Entry"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        if ($createdUserID == trim($CI->session->userdata("empID")) and $approvedYN == 0 and $confirmedYN == 1 && $isDeleted == 0) {
            $status .= '<a onclick="referback_journal_entry(' . $JVMasterAutoId . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        $status .= '<a target="_blank" onclick="documentPageView_modal(\'JV\',\'' . $JVMasterAutoId . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp';
        $status .= '<a target="_blank" href="' . site_url('Journal_entry/journal_entry_conformation/') . '/' . $JVMasterAutoId . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';
        if ($confirmedYN != 1 && $isDeleted == 0) {
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_journal_entry(' . $JVMasterAutoId . ',\'Invoices\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('company_PL_account_drop')) {
    function company_PL_account_drop($status = TRUE)
    {
        $CI =& get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('controllAccountYN', 0);
        $CI->db->where('accountCategoryTypeID<>', 4);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $bank = $CI->db->get()->result_array();
        $bank_arr = array('' => 'Select GL Account');
        if (isset($bank)) {
            foreach ($bank as $row) {
                $bank_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }

        return $bank_arr;
    }
}

if (!function_exists('company_PL_account_drop')) {
    function company_PL_account_drop($status = TRUE)
    {
        $CI =& get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('controllAccountYN', 0);
        $CI->db->where('accountCategoryTypeID<>', 4);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $bank = $CI->db->get()->result_array();
        $bank_arr = array('' => 'Select GL Account');
        if (isset($bank)) {
            foreach ($bank as $row) {
                $bank_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }

        return $bank_arr;
    }
}

if (!function_exists('company_groupstatus')) {
    function company_groupstatus($autoID, $status, $description = null)
    {

        $checked = ($status == 1) ? 'checked' : '';
        $isDisable = '';
        /*return '<input type="checkbox" class="switch-chk btn-sm" id="status_' . $autoID . '" onchange="changeStatus(' . $autoID . ')"
                    data-size="mini" data-on-text="YES" data-handle-width="25" data-off-color="danger" data-on-color="success"
                    data-off-text="NO" data-label-width="0" ' . $checked . ' ' . $isDisable . '>';*/
        if ($status == 1) {
            return '<input type="checkbox" class="switch-chk btn-sm" id="status_' . $autoID . '" onchange="changeStatus(' . $autoID . ')"
                    data-size="mini" data-on-text="YES" data-handle-width="25" data-off-color="danger" data-on-color="success"
                    data-off-text="NO" data-label-width="0" ' . $checked . ' ' . $isDisable . '>&nbsp; | &nbsp;<a class="btn btn-xs btn-primary" title="Add Widget" onclick="openAddWidgetModel(' . $autoID . ')"><span class="glyphicon glyphicon-plus" ></span></a>&nbsp; | &nbsp; <a class="" title="Edit" onclick="editUserGroup(' . $autoID . ')"><span class="glyphicon glyphicon-pencil" ></span></a> &nbsp; | &nbsp; <a class="" title="Delete" onclick="deleteUserGroup(' . $autoID . ')"><span class="text-red glyphicon glyphicon-trash" ></span></a>';
        } else {
            return '<input type="checkbox" class="switch-chk btn-sm" id="status_' . $autoID . '" onchange="changeStatus(' . $autoID . ')"
                    data-size="mini" data-on-text="YES" data-handle-width="25" data-off-color="danger" data-on-color="success"
                    data-off-text="NO" data-label-width="0" ' . $checked . ' ' . $isDisable . '>&nbsp; | &nbsp;<a class="" title="Edit" onclick="editUserGroup(' . $autoID . ')"><span class="glyphicon glyphicon-pencil" ></span></a> &nbsp; | &nbsp; <a class="" title="Delete" onclick="deleteUserGroup(' . $autoID . ')"><span class="text-red glyphicon glyphicon-trash" ></span></a>';
        }


    }
}

if (!function_exists('drill_down_navigation_dropdown')) {
    function drill_down_navigation_dropdown()
    {
        $CI =& get_instance();
        $group_company_arr = array();
        $companyID = current_companyID();
        $userID = current_userID();
        /*if there is no company*/
        $group_company = $CI->db->query("SELECT company_id as companyID,CONCAT(company_code, ' - ', company_name) AS company FROM srp_erp_employeenavigation INNER JOIN srp_erp_company on company_id=companyID INNER JOIN srp_erp_usergroups on srp_erp_employeenavigation.userGroupID=srp_erp_usergroups.userGroupID WHERE empID = {$userID} AND isActive=1")->result_array();
        if (!empty($group_company)) {
            foreach ($group_company as $row) {
                $group_company_arr[trim($row['companyID']) . '-1'] = trim($row['company']);
            }
        } else {
            $group_company = $CI->db->query("select company_id as companyID,CONCAT(company_code,' | ',company_name) as company from `srp_erp_company` where company_id={$companyID} ")->row_array();
            // dropdown session company
            $group_company_arr[trim($group_company['companyID']) . '-1'] = trim($group_company['company']);
        }

        //add group to company
        $group_company = $CI->db->query(" SELECT srp_erp_companygroupmaster.companyGroupID as companyID,srp_erp_companygroupmaster.description AS company FROM `srp_erp_companysubgroupemployees` INNER JOIN srp_erp_companysubgroupmaster ON srp_erp_companysubgroupemployees.companySubGroupID=srp_erp_companysubgroupmaster.companySubGroupID INNER JOIN srp_erp_companygroupmaster ON srp_erp_companygroupmaster.companyGroupID=srp_erp_companysubgroupmaster.companyGroupID WHERE srp_erp_companysubgroupemployees.EmpID = {$userID} GROUP BY srp_erp_companygroupmaster.companyGroupID ")->result_array();


        /*$group_company = $CI->db->query("SELECT srp_erp_companygroupmaster.companyGroupID as companyID,srp_erp_companygroupmaster.description AS company FROM srp_erp_employeenavigation INNER JOIN srp_erp_company on company_id=companyID INNER JOIN srp_erp_usergroups on srp_erp_employeenavigation.userGroupID=srp_erp_usergroups.userGroupID INNER JOIN srp_erp_companygroupdetails ON srp_erp_companygroupdetails.companyID = company_id LEFT JOIN srp_erp_companygroupmaster ON srp_erp_companygroupdetails.companyGroupID = srp_erp_companygroupmaster.companyGroupID WHERE empID = {$userID} AND isActive=1 GROUP BY srp_erp_companygroupmaster.companyGroupID")->result_array();*/

        if (!empty($group_company)) {
            foreach ($group_company as $row) {
                $group_company_arr[trim($row['companyID']) . '-2'] = trim($row['company']);
            }
        }

        return $group_company_arr;
    }
}

if (!function_exists('approval_change_modal')) { /**/
    function approval_change_modal($pocode, $poID, $ApprovedID, $Level, $approved, $documentID, $isRejected = 0)
    {
        $status = '';
        if ($approved == 0) {
            if ($isRejected == 0) {
                $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'>' . $pocode . '</a>';
            } else {
                $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '", "' . $poID . '"); \'>' . $pocode . '</a>';
            }
        } else {
            $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '", "' . $poID . '"); \'>' . $pocode . '</a>';
        }

        return $status;
    }
}

if (!function_exists('approval_change_modal_buyback')) { /**/
    function approval_change_modal_buyback($pocode, $poID, $ApprovedID, $Level, $approved, $documentID, $isRejected = 0,$buy)
    {
        $status = '';
        if ($approved == 0) {
            if ($isRejected == 0) {
                $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'>' . $pocode . '</a>';
            } else {
                $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '", "' . $poID . '","' . $buy . '"); \'>' . $pocode . '</a>';
            }
        } else {
            $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '", "' . $poID . '","' . $buy . '"); \'>' . $pocode . '</a>';
        }

        return $status;
    }
}


if (!function_exists('approval_change_modal_treasury')) { /**/
    function approval_change_modal_treasury($pocode, $bankRecAutoID, $bankGLautoID, $ApprovedID, $Level, $approved, $documentID)
    {
        $status = '';
        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $bankRecAutoID . '","' . $bankGLautoID . '","' . $ApprovedID . '","' . $Level . '"); \'>' . $pocode . '</a>';
        } else {
            $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '", "' . $bankRecAutoID . '","' . $bankGLautoID . '"); \'>' . $pocode . '</a>';
        }

        return $status;
    }
}

if (!function_exists('approval_change_modal')) { /**/
    function approval_change_modal($pocode, $poID, $ApprovedID, $Level, $approved, $documentID)
    {
        $status = '';
        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'>' . $pocode . '</a>';
        } else {
            $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '", "' . $poID . '"); \'>' . $pocode . '</a>';
        }

        return $status;
    }
}

if (!function_exists('liabilityGL_drop')) {
    function liabilityGL_drop()
    {

        $CI =& get_instance();

        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('masterAccountYN', 0);
        //$CI->db->WHERE('controllAccountYN ', 0);
        $CI->db->WHERE('accountCategoryTypeID != 4');
        $CI->db->WHERE('isBank', 0);
        $CI->db->WHERE('isActive', 1);
        $CI->db->WHERE('approvedYN', 1);
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->ORDER_BY('GLSecondaryCode');
        $data = $CI->db->get()->result_array();

        //echo $CI->db->last_query();
        /*$data_arr = array('' => 'Select GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }*/

        return $data;
    }
}

if (!function_exists('fetch_widget_template')) { //get default templates
    function fetch_widget_template()
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_dashboardtemplate');
        $CI->db->WHERE('isDefault', 0);
        $data = $CI->db->get()->result_array();

        return $data;
    }
}

if (!function_exists('fetch_currency_code')) {
    function fetch_currency_code($currencyID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("CurrencyCode");
        $CI->db->FROM('srp_erp_currencymaster');
        $CI->db->WHERE('currencyID', $currencyID);

        return $CI->db->get()->row('CurrencyCode');
    }
}

if (!function_exists('edit_loantreasury')) {
    function edit_loantreasury($itemAutoID)
    {
        $status = '<span style="width:50px" class="pull-right">';
        $status .= '<a onclick=\'fetchPage("system/bank_rec/erp_loan_mgt_new","' . $itemAutoID . '","Loan Management"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp&nbsp|&nbsp;&nbsp&nbsp';
        $status .= '<a onclick="delete_loan(' . $itemAutoID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('fetch_industryTypes')) {
    function fetch_industryTypes()
    {
        $CI =& get_instance();
        $CI->db->SELECT("industrytypeID,industryTypeDescription");
        $CI->db->FROM('srp_erp_industrytypes');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Industry');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['industrytypeID']) . ' | ' . trim($row['industryTypeDescription'])] = trim($row['industryTypeDescription']);
            }
        }

        return $data_arr;
    }
}


if (!function_exists('erp_bank_facilityStatus')) {
    function erp_bank_facilityStatus()
    {
        $CI =& get_instance();
        $data = $CI->db->query("select statusID,description from srp_erp_bankfacilitystatus")->result_array();
        $data_arr = array();
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['statusID'])] = trim($row['description']);
            }
        }

        return $data_arr;
    }
}
if (!function_exists('erp_bankfacilityrateType')) {
    function erp_bankfacilityrateType()
    {
        $CI =& get_instance();
        $data = $CI->db->query("select ratetypeID,ratetypeName from `srp_erp_bankfacilityratetype` ")->result_array();
        $data_arr = array();
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['ratetypeID'])] = trim($row['ratetypeName']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('number_months')) {
    function number_months($date1, $date2)
    {
        $diff = abs(strtotime($date2) - strtotime($date1));
        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = ceil(($diff - ($years * 365 * 60 * 60 * 24)) / ((365 * 60 * 60 * 24) / 12));

        return $months;
    }
}


if (!function_exists('current_companyName')) {
    function current_companyName($nameonly = false)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        if ($nameonly) {
            $data = $CI->db->query("select company_id as companyID,company_name as company from `srp_erp_company` where company_id={$companyID} ")->row_array();
        } else {
            $data = $CI->db->query("select company_id as companyID,CONCAT(company_code,' | ',company_name) as company from `srp_erp_company` where company_id={$companyID} ")->row_array();
        }


        return $data['company'];
    }
}

if (!function_exists('leavemaster_dropdown')) {
    function leavemaster_dropdown($policyID = FALSE, $all = FALSE)
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $where = '';
        if ($policyID) {
            $where = " AND policyID={$policyID}";
        }
        if ($all) {
            $leavetype_arr = array('' => $CI->lang->line('common_all')/*'All'*/);
        } else {
            $leavetype_arr = array('' => $CI->lang->line('common_select')/*'Select'*/);
        }


        $leavetype = $CI->db->query("SELECT leaveTypeID, description FROM srp_erp_leavetype  WHERE companyID={$companyID} AND typeConfirmed=1  $where ")->result_array();
        if (!empty($leavetype)) {
            foreach ($leavetype as $row) {
                $leavetype_arr[trim($row['leaveTypeID'])] = trim($row['description']);
            }
        }

        return $leavetype_arr;
    }
}

if (!function_exists('payroll_dropdown')) {
    function payroll_dropdown($isNonPayroll = NULL)
    {

        $tableName = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("payrollMasterID,concat(MONTHNAME(STR_TO_DATE(payrollMonth, '%m')) ,' - ',payrollYear,' | ',narration) as month");
        $CI->db->from($tableName);
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('approvedYN', 1);

        $data = $CI->db->get()->result_array();
        $payroll_arr = array('' => $CI->lang->line('common_please_select')/*'Please Select'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $payroll_arr[trim($row['payrollMasterID'])] = trim($row['month']);

            }
        }

        return $payroll_arr;
    }
}


if (!function_exists('segment_employee_drop')) {
    function segment_employee_drop($segment)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $customer = $CI->db->query("SELECT * FROM srp_employeesdetails WHERE Erp_companyID ={$companyID}  AND segmentID={$segment}")->result_array();
        $customer_arr = array('' => 'Select Employee');
        if (isset($customer)) {
            foreach ($customer as $row) {
                /*$customer_arr[trim($row['EIdNo'])] = trim($row['ECode']) . ' | ' . trim($row['Ename1']) . ' ' . trim($row['Ename2']) . ' ' . trim($row['Ename3']) . ' ' . trim($row['Ename4']);*/
                $customer_arr[trim($row['EIdNo'])] = trim($row['ECode']) . ' | ' . trim($row['Ename2']);
            }
        }

        return $customer_arr;
    }
}

if (!function_exists('fetch_employeeNo')) {
    function fetch_employeeNo($employeeNo)
    {
        $CI =& get_instance();
        $CI->db->select('EIdNo,ECode,Ename2,EcMobile,Nid,EEmail');
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('EIdNo', $employeeNo);
        $data = $CI->db->get()->row_array();

        return $data;
    }
}

if (!function_exists('leaveAction')) {
    function leaveAction($id, $type)
    {
        switch ($type) {
            case 'hourly':

                if ($id == 0) {
                    $status = 'Daily';
                } else if ($id == 1) {
                    $status = 'Monthly';
                } else {
                    $status = 'Annually';
                };

                break;
            case 'default':
                $status = ($id == 0 ? '<span class="label label-danger">&nbsp;</span>' : '<span class="label label-success">&nbsp;</span>');
                break;
            case 'ID':
                $CI =& get_instance();
                $set = $CI->db->query("SELECT * FROM srp_employeesdetails WHERE leaveGroupID=$id")->row_array();
                $status = '<span class="pull-right">';
                $status .= '<a onclick="fetchPage(\'system/hrm/new_leave_group\',' . $id . ',\'Add Leave group\',\'Leave Group\');"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; ';

                if (empty($set)) {


                    $status .= '|&nbsp;&nbsp; <a onclick="deleteLeave(' . $id . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';

                    $status .= '</span>';
                }
                break;
            default:
                '';

        }

        return $status;
    }
}

if (!function_exists('monthlyleavegroup_drop')) {
    function monthlyleavegroup_drop($type = 1)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $customer = $CI->db->query("SELECT *  FROM srp_erp_leavegroup WHERE companyID={$companyID} ")->result_array();
        $customer_arr = array('' => 'Select leave group');
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['leaveGroupID'])] = trim($row['description']);
            }
        }
        return $customer_arr;
    }
}


if (!function_exists('leaveGroup_drop')) {
    function leaveGroup_drop()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $companyID = current_companyID();
        $data = $CI->db->query("SELECT leaveGroupID,description FROM `srp_erp_leavegroup` WHERE leaveGroupID=(select leaveGroupID from `srp_erp_leavegroupdetails` WHERE leaveGroupID=srp_erp_leavegroup.leaveGroupID group by leaveGroupID) AND companyID={$companyID}")->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_leave_group')/*'Select Leave Group'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['leaveGroupID'])] = trim($row['description']);
            }
        }

        return $data_arr;
    }
}
if (!function_exists('accrualAction')) {
    function accrualAction($id, $confirmYN)
    {
        $status = '<span style="max-width:70px" class="pull-right">';
        $status .= '<a onclick=\'fetchPage("system/hrm/new_leave_accrual","' . $id . '","Leave Accrual"); \'>';
        $status .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';

        if ($confirmYN != 1) {
            $status .= '&nbsp;|&nbsp;<a onclick=\'delete_accrual(' . $id . '); \'>';
            $status .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color: #d15b47"></span></a>';
        }

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('get_all_customers')) {
    function get_all_customers()/*get all Customers*/
    {
        $CI =& get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_customermaster');
        $CI->db->where('companyCode', $CI->common_data['company_data']['company_code']);
        $customer = $CI->db->get()->result_array();

        return $customer;
    }
}


if (!function_exists('format_date_dob')) {
    function format_date_dob($date = NULL)
    {
        if (isset($date)) {
            if (!empty($date)) {
                return date('dS F Y (l)', strtotime($date));
            }
        } else {
            return date('dS F Y (l)', time());
        }
    }
}


/** By Nasik **/
if (!function_exists('covertToMysqlDate')) {
    function covertToMysqlDate($date = null)
    {
        return (!empty($date)) ? input_format_date($date, date_format_policy()) : '';
    }
}

if (!function_exists('all_priority_new_drop')) {
    function all_priority_new_drop($status = TRUE)/*Load all currency*/
    {
        $CI =& get_instance();
        $CI->db->select("priorityID,priorityDescription");
        $CI->db->from('srp_erp_priority_master');
        $prio = $CI->db->get()->result_array();
        $priority_arr = array('' => 'Select Priority');
        if (isset($prio)) {
            foreach ($prio as $row) {
                $priority_arr[trim($row['priorityID'])] = trim($row['priorityDescription']);
            }
        }

        return $priority_arr;
    }
}


if (!function_exists('format_date_mysql_datetime')) {
    function format_date_mysql_datetime($date = NULL)
    {
        if (isset($date)) {
            if (!empty($date)) {
                return date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $date)));
            }
        } else {
            return date('Y-m-d H:i:s', time());
        }
    }
}

if (!function_exists('format_date_getYear')) {
    function format_date_getYear($date = NULL)
    {
        if (isset($date)) {
            return date('Y', strtotime($date));
        } else {
            return date('Y', time());
        }
    }
}

if (!function_exists('operand_arr')) {
    function operand_arr()
    {
        return array('+', '*', '/', '-', '(', ')');
    }
}

if (!function_exists('leaveAdjustmentAction')) {
    function leaveAdjustmentAction($id, $confirmYN)
    {
        $status = '<span style="width:50px" class="pull-right">';
        $status .= '<a onclick=\'fetchPage("system/hrm/new_leave_adjustment","' . $id . '","Leave Accrual"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        if ($confirmYN == 0) {
            $status .= '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a onclick="delete_master(' . $id . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('amount_percentage')) { /*calculate percentage for total amount*/
    function amount_percentage($totalamount, $amount)
    {
        $perecentage = 0;
        if ($totalamount & $amount != 0) {
            $perecentage = ($amount / $totalamount) * 100;
        }

        return round($perecentage, 2);
    }
}

if (!function_exists('tax_groupMaster')) {
    function tax_groupMaster($id)
    {
        if ($id == 1) {
            return 'Outputs (Sales etc)';
        } else if ($id == 2) {
            return 'Inputs (Purchases, imports etc)';
        }

        return '';
    }
}

if (!function_exists('customer_tax_groupMaster')) {
    function customer_tax_groupMaster()
    {
        $CI =& get_instance();
        $CI->db->select("taxGroupID,Description");
        $CI->db->from('srp_erp_taxgroup');
        $CI->db->where('taxType', 1);
        $CI->db->where('companyID', current_companyID());
        $prio = $CI->db->get()->result_array();
        $priority_arr = array('' => 'Select Tax Group');
        if (isset($prio)) {
            foreach ($prio as $row) {
                $priority_arr[trim($row['taxGroupID'])] = trim($row['Description']);
            }
        }

        return $priority_arr;
    }
}

if (!function_exists('supplier_tax_groupMaster')) {
    function supplier_tax_groupMaster()
    {
        $CI =& get_instance();
        $CI->db->select("taxGroupID,Description");
        $CI->db->from('srp_erp_taxgroup');
        $CI->db->where('taxType', 2);
        $CI->db->where('companyID', current_companyID());
        $prio = $CI->db->get()->result_array();
        $priority_arr = array('' => 'Select Tax Group');
        if (isset($prio)) {
            foreach ($prio as $row) {
                $priority_arr[trim($row['taxGroupID'])] = trim($row['Description']);
            }
        }

        return $priority_arr;
    }
}

if (!function_exists('array_group_by')) {
    /**
     * Groups an array by a given key.
     *
     * Groups an array into arrays by a given key, or set of keys, shared between all array members.
     *
     * Based on {@author Jake Zatecky}'s {@link https://github.com/jakezatecky/array_group_by array_group_by()} function.
     * This variant allows $key to be closures.
     *
     * @param array $array The array to have grouping performed on.
     * @param mixed $key,... The key to group or split by. Can be a _string_,
     *                       an _integer_, a _float_, or a _callable_.
     *
     *                       If the key is a callback, it must return
     *                       a valid key from the array.
     *
     *                       ```
     *                       string|int callback ( mixed $item )
     *                       ```
     *
     * @return array|null Returns a multidimensional array or `null` if `$key` is invalid.
     */
    function array_group_by(array $array, $key)
    {
        if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key)) {
            trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);

            return NULL;
        }
        $func = (is_callable($key) ? $key : NULL);
        $_key = $key;
        // Load the new array, splitting by the target key
        $grouped = [];
        foreach ($array as $value) {
            if (is_callable($func)) {
                $key = call_user_func($func, $value);
            } elseif (is_object($value) && isset($value->{$_key})) {
                $key = $value->{$_key};
            } elseif (isset($value[$_key])) {
                $key = $value[$_key];
            } else {
                continue;
            }
            $grouped[$key][] = $value;
        }
        // Recursively build a nested grouping if more parameters are supplied
        // Each grouped array value is grouped according to the next sequential key
        if (func_num_args() > 2) {
            $args = func_get_args();
            foreach ($grouped as $key => $value) {
                $params = array_merge([$value], array_slice($args, 2, func_num_args()));
                $grouped[$key] = call_user_func_array('array_group_by', $params);
            }
        }

        return $grouped;
    }

}

if (!function_exists('date_format_policy')) { //get company date format user defined
    function date_format_policy()
    {
        $CI =& get_instance();

        return $CI->common_data['company_policy']['DF']['All'][0]["policyvalue"];
    }
}

if (!function_exists('convert_date_format')) { //convert to php date format
    function convert_date_format($date = NULL)
    {
        if ($date) {
            $date_format_policy = date_format_policy();
            $text = str_replace('yyyy', 'Y', $date_format_policy);
            $text = str_replace('mm', 'm', $text);
            $text = str_replace('dd', 'd', $text);

            return format_date($date, $text);
        } else {
            $date_format_policy = date_format_policy();
            $text = str_replace('yyyy', 'Y', $date_format_policy);
            $text = str_replace('mm', 'm', $text);
            $text = str_replace('dd', 'd', $text);

            return $text;
        }
    }
}

if (!function_exists('convert_date_format_sql')) { //convert to php date format
    function convert_date_format_sql()
    {
        $date_format_policy = date_format_policy();
        $text = str_replace('yyyy', '%Y', $date_format_policy);
        $text = str_replace('mm', '%m', $text);
        $text = str_replace('dd', '%d', $text);

        return $text;
    }
}

if (!function_exists('input_format_date')) { //format company date policy to mysql format
    function input_format_date($date, $format, $defaultFormat = 'Y-m-d')
    {
        if (!is_null($date)) {
            switch ($format) {
                case "mm-dd-yyyy":
                    $date = str_replace('-', '/', $date);

                    return date($defaultFormat, strtotime($date));
                    break;
                case "dd-mm-yyyy":
                    return date($defaultFormat, strtotime($date));
                    break;
                case "yyyy-mm-dd":
                    return date($defaultFormat, strtotime($date));
                    break;
                case "mm/dd/yyyy":
                    return date($defaultFormat, strtotime($date));
                    break;
                case "dd/mm/yyyy":
                    $date = str_replace('/', '-', $date);

                    return date($defaultFormat, strtotime($date));
                    break;
                case "yyyy/mm/dd":
                    return date($defaultFormat, strtotime($date));
                    break;
                default:
                    return date($defaultFormat, strtotime($date));
            }
        } else {
            return '';
        }
    }
}

if (!function_exists('current_format_date')) { //convert to php date format
    function current_format_date()
    {
        $date_format_policy = date_format_policy();  //get comany policy date
        $current_date = convert_date_format(current_date(FALSE));

        return $current_date;
    }
}

if (!function_exists('getDefaultPayroll')) {
    function getDefaultPayroll($code, $isNonPayroll = NULL)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $filter = ($isNonPayroll != 'Y') ? '1' : '2';

        $result = $CI->db->query("SELECT salaryCategoryID, GLCode FROM srp_erp_pay_salarycategories AS salCat
                                  JOIN srp_erp_defaultpayrollcategories AS defaultTB ON defaultTB.id = salCat.payrollCatID
                                  WHERE companyID={$companyID} AND defaultTB.code='{$code}' AND isPayrollCategory={$filter} ")->row_array();

        return $result;
    }
}


if (!function_exists('get_financial_from_to')) { //get finance begining enddate
    function get_financial_from_to($companyFinanceYearID)
    {
        $CI =& get_instance();
        $CI->db->select('beginingDate, endingDate');
        $CI->db->where('companyFinanceYearID', $companyFinanceYearID);
        $financialYear = $CI->db->get('srp_erp_companyfinanceyear')->row_array();

        return $financialYear;
    }
}


if (!function_exists('get_current_designation')) {
    function get_current_designation()
    {
        $empID = current_userID();
        $CI =& get_instance();
        $designation = $CI->db->query("SELECT DesDescription FROM `srp_employeesdetails` LEFT JOIN `srp_designation`  on EmpDesignationId=DesignationID WHERE EidNo={$empID}")->row_array();

        return $designation['DesDescription'];

    }
}

if (!function_exists('current_warehouseID')) {
    function current_warehouseID()
    {
        $warehouseID = get_outletID();
        return $warehouseID;

    }
}

if (!function_exists('get_calender')) {
    function get_calender()
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $year = date('Y');
        $result = $CI->db->query("SELECT ( SELECT MAX(fulldate) FROM srp_erp_calender WHERE companyID = {$companyID} ) endDate, ( SELECT MIN(fulldate) FROM srp_erp_calender WHERE companyID = {$companyID} ) AS startDate FROM `srp_erp_calender` WHERE companyID = {$companyID}")->row_array();

        return $result;

    }
}

if (!function_exists('fetchFinancePeriod')) {//get finance period using companyFinancePeriodID.
    function fetchFinancePeriod($companyFinancePeriodID)
    {
        $CI =& get_instance();

        $result = $CI->db->query("SELECT dateFrom,dateTo FROM srp_erp_companyfinanceperiod WHERE companyFinancePeriodID=$companyFinancePeriodID")->row_array();

        return $result;
    }
}

if (!function_exists('getDocumentMaster')) {//get finance period using companyFinancePeriodID.
    function getDocumentMaster()
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->db->SELECT("codeID,documentID,document");
        $CI->db->FROM('srp_erp_documentcodemaster');
        $CI->db->where('companyID', $companyID);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Document');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['documentID'])] = trim($row['document']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('reportMaster_action')) {
    function reportMaster_action($id, $reportCode)
    {
        $str = '<div align="right"><a onclick="reportDetails(' . $id . ', \'' . strtolower($reportCode) . '\')">';
        $str .= '<span class="glyphicon glyphicon-pencil" title="Edit" rel="tooltip"></span></a></div>';

        return $str;
    }
}

if (!function_exists('get_ssoReportFields')) {
    function get_ssoReportFields($where, $reportType = NULL)
    {

        $CI =& get_instance();
        $companyID = current_companyID();

        if ($where == 'E') {
            $result = $CI->db->query("SELECT masterTB.id, description, masterTable, inputName, fieldName
                                  FROM srp_erp_sso_reporttemplatefields AS masterTB
                                  WHERE isEmployeeLevel=1 AND isFillable=1")->result_array();
        } else {
            $whereStr = '';
            if ($reportType != NULL) {
                $ssoReportColumnDetails = unserialize(ssoReportColumnDetails);
                $shortOrderColumn = $ssoReportColumnDetails[$reportType][0];
                $masterID = $ssoReportColumnDetails[$reportType][1];
                //$whereStr = "AND (masterTB.reportType='".$reportType."' OR masterTB.reportType IS NULL ) AND masterTB.".$shortOrderColumn." IS NOT NULL";
                $whereStr = "AND masterTB." . $shortOrderColumn . " IS NOT NULL";
            }
            $result = $CI->db->query("SELECT masterTB.id, description, masterTable, inputName, reportValue, fieldName
                                  FROM srp_erp_sso_reporttemplatefields AS masterTB
                                  LEFT JOIN srp_erp_sso_reporttemplatedetails AS det ON det.reportID=masterTB.id AND det.companyID={$companyID} AND masterID={$masterID}
                                  WHERE isCompanyLevel=1 AND isFillable=1 {$whereStr}")->result_array();
            //$q = $CI->db->last_query();
        }

        return $result;
    }
}

if (!function_exists('get_ssoReportConfigData')) {
    function get_ssoReportConfigData($reportType)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $result_arr = array();
        $ssoData = $CI->db->query("SELECT payGroupID, ssoTB.description AS titleDes
                                   FROM srp_erp_paygroupmaster AS payGroup
                                   JOIN srp_erp_socialinsurancemaster AS ssoTB
                                   ON ssoTB.socialInsuranceID=payGroup.socialInsuranceID AND ssoTB.companyID={$companyID}
                                   WHERE payGroup.companyID={$companyID} ")->result_array();

        $payGroup_arr = payGroup_drop();

        $result_arr['companyLevel'] = get_ssoReportFields('C', $reportType);
        $result_arr['SSO_arr'] = $ssoData;
        $result_arr['payGroup_arr'] = $payGroup_arr;
        $result_arr['shortOrder'] = ssoReport_shortOrder($reportType);

        return $result_arr;
    }
}


if (!function_exists('get_ssoEmpLevelConfig')) {
    function get_ssoEmpLevelConfig($reportID)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $result_arr = array();

        $result = $CI->db->query("SELECT EIdNo, ECode, Ename2, Ename3, initial, description, temFieldsTB.id AS fieldID, inputName, reportValue, ssoNo
                                   FROM srp_employeesdetails AS empTB
                                   JOIN srp_erp_sso_reporttemplatefields AS temFieldsTB
                                   LEFT JOIN (
                                      SELECT empID, reportID, reportValue FROM srp_erp_sso_reporttemplatedetails WHERE companyID={$companyID} AND masterID={$reportID}
                                   ) AS temDetailsTB ON temDetailsTB.reportID = temFieldsTB.id AND EIdNo=empID
                                   WHERE Erp_companyID={$companyID} AND isPayrollEmployee=1 AND empTB.empConfirmedYN=1
                                   AND temFieldsTB.isFillable=1 AND temFieldsTB.isEmployeeLevel=1 ORDER BY EIdNo, temFieldsTB.id")->result_array();

//AND isDischarged=0
        $lastEmpID = NULL;
        $i = 0;
        foreach ($result as $key => $row) {
            $empID = $row['EIdNo'];
            $inputName = $row['inputName'];

            if ($empID == $lastEmpID) {

                if ($inputName == 'lastName') {
                    $reportVal = ($row['reportValue'] == NULL) ? $row['Ename3'] : $row['reportValue'];
                } else if ($inputName == 'initials') {
                    $reportVal = ($row['reportValue'] == NULL) ? $row['initial'] : $row['reportValue'];
                } else if ($inputName == 'memNumber') {
                    $reportVal = ($row['reportValue'] == NULL) ? $row['ssoNo'] : $row['reportValue'];
                } else {
                    $reportVal = $row['reportValue'];
                }

                $result_arr[$i]['columnValue'][$inputName] = $reportVal;

            } else {
                $i += ($key == 0) ? 0 : 1;
                $result_arr[$i]['empID'] = $empID;
                $result_arr[$i]['eCode'] = $row['ECode'];
                $result_arr[$i]['eName'] = $row['Ename2'];


                if ($inputName == 'lastName') {
                    $reportVal = ($row['reportValue'] == NULL) ? $row['Ename3'] : $row['reportValue'];
                } else if ($inputName == 'initials') {
                    $reportVal = ($row['reportValue'] == NULL) ? $row['initial'] . ' 150 ' : '50 ' . $row['reportValue'];
                } else {

                    $reportVal = $row['reportValue'];
                }
                $result_arr[$i]['columnValue'][$inputName] = $reportVal;

            }

            $lastEmpID = $empID;
        }

        return $result_arr;
    }
}

if (!function_exists('dropdown_all_overHead_gl')) {
    function dropdown_all_overHead_gl()
    {
        $CI =& get_instance();
        $companyID = current_companyID();

        $data = $CI->db->query("SELECT
  coa.GLAutoID,
  coa.systemAccountCode,
  coa.GLSecondaryCode,
  coa.GLDescription,
  coa.systemAccountCode,
  coa.subCategory
FROM
  `srp_erp_chartofaccounts` coa
WHERE
  coa.`masterAccountYN` = 0
AND coa.`approvedYN` = 1
AND coa.`isActive` = 1
AND coa.`isBank` = 0
AND coa.accountCategoryTypeID != 4
AND coa.`companyID` = '{$companyID}'
AND coa.controllAccountYN = 0")->result_array();

        $data_arr = array('' => 'Select GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }

        return $data_arr;
    }

    if (!function_exists('get_mfq_category')) {
        function get_mfq_category()
        {
            $CI =& get_instance();
            $companyID = current_companyID();
            $result = $CI->db->query("SELECT * FROM `srp_erp_mfq_itemcategory` WHERE companyID={$companyID}")->result_array();

            return $result;

        }
    }
}

if (!function_exists('ssoReport_shortOrder')) {
    function ssoReport_shortOrder($reportType)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $ssoReportColumnDetails = unserialize(ssoReportColumnDetails);
        $shortOrderColumn = $ssoReportColumnDetails[$reportType][0];
        $masterID = $ssoReportColumnDetails[$reportType][1];

        $data = $CI->db->query("SELECT masterTB.id, fieldName, description, isLeft_strPad,
                                COALESCE(configTB.strLength, masterTB.strLength) AS strLength,
                                COALESCE(configTB.shortOrder, masterTB.{$shortOrderColumn}) AS shortOrder
                                FROM srp_erp_sso_reporttemplatefields AS masterTB
                                LEFT JOIN (
                                    SELECT reportID, strLength, shortOrder FROM srp_erp_sso_reporttemplateconfig WHERE companyID={$companyID} AND masterID={$masterID}
                                ) AS configTB ON configTB.reportID=masterTB.id
                                WHERE masterTB.{$shortOrderColumn} IS NOT NULL
                                ORDER BY shortOrder")->result_array();

        /*$m= $CI->db->last_query();
        return array($m,$data);*/

        return $data;
    }
}


if (!function_exists('dropdown_leavepolicy')) {
    function dropdown_leavepolicy()
    {
        $CI =& get_instance();
        $data = $CI->db->query("select policyMasterID,policyDescription from `srp_erp_leavepolicymaster` ")->result_array();
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['policyMasterID'])] = trim($row['policyDescription']);
            }
        }

        return $data_arr;
    }

}

if (!function_exists('AnnualaccrualAction')) {
    function AnnualaccrualAction($id, $confirmYN)
    {

        $status = '<span style="max-width:70px" class="pull-right">';
        $status .= '<a onclick=\'fetchPage("system/hrm/new_leave_annually_accrual","' . $id . '","Leave Annual Accrual"); \'>';
        $status .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';

        if ($confirmYN != 1) {
            $status .= '&nbsp;|&nbsp;<a onclick=\'delete_accrual(' . $id . '); \'>';
            $status .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color: #d15b47"></span></a>';
        }

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('action_epfReport')) {
    function action_epfReport($id, $confirmedYN)
    {
        $status = '<div class="pull-right">';

        if ($confirmedYN != 1) {
            $status .= '<a onclick="generate_newReport(\'' . $id . '\')"><span class="glyphicon glyphicon-pencil"></span></a>';
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
            $status .= '<span class="glyphicon glyphicon-trash tbTrash" style="color: rgb(209, 91, 71);" onclick="delete_epfReport(\'' . $id . '\')"></span>';
        } else {
            $status .= '<a onclick="generate_newReport(\'' . $id . '\')"><i class="fa fa-fw fa-eye"></i></a>';
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
            $status .= '<i class="fa fa-file-text-o" aria-hidden="true" onclick="get_detFile(\'' . $id . '\')"></i>';
        }
        $status .= '</div>';

        return $status;
    }
}

if (!function_exists('epfReportTextbox')) {
    function epfReportTextbox($id, $ocGrade)
    {
        $str = '<input type="text" name="ocGrade[]" class="form-control trInputs" value="' . $ocGrade . '" />';
        $str .= '<input type="hidden" name="empID[]" value="' . $id . '" />';

        return $str;
    }
}


if (!function_exists('addBtn')) {
    function addBtn()
    {
        /*$CurrencyCode = "'".trim($CurrencyCode)."'";
        $code = "'".trim($code)."'";
        $empName = "'".trim($empName)."'";
        //$addTempTB = 'onclick="addTempTB('.$EIdNo.', '.$code.', '.$empName.', '.$CurrencyCode.', '.$DecimalPlaces.' )"';*/
        $addTempTB = 'onclick="addTempTB(this)"';

        $view = '<div class="" align="center"> <button class="btn btn-primary btn-xs" ' . $addTempTB . ' style="font-size:10px"> + Add </button> </div>';

        return $view;

    }
}

if (!function_exists('editcustomer')) {
    function editcustomer($customerAutoID)
    {
        // echo $to;
        $CI =& get_instance();
        $status = '<span class="pull-right">';
        $customerinvoice = $CI->db->query("select customerID from srp_erp_customerinvoicemaster WHERE customerID=$customerAutoID ;")->row_array();
        $customerreceipt = $CI->db->query("select customerID from srp_erp_customerreceiptmaster WHERE customerID=$customerAutoID ;")->row_array();
        $creditnote = $CI->db->query("select customerID from srp_erp_creditnotemaster WHERE customerID=$customerAutoID ;")->row_array();
        $salesreturn = $CI->db->query("select customerID from srp_erp_salesreturnmaster WHERE customerID=$customerAutoID ;")->row_array();
        $receiptmatching = $CI->db->query("select customerID from srp_erp_rvadvancematch WHERE customerID=$customerAutoID ;")->row_array();
        $generalledger = $CI->db->query("select partyautoID from srp_erp_generalledger WHERE partyautoID=$customerAutoID AND partyType = 'CUS';")->row_array();
        if (!empty($customerinvoice || $customerreceipt || $creditnote || $generalledger || $salesreturn || $receiptmatching)) {
            $status .= '<spsn class="pull-right"><a onclick="attachment_modal(' . $customerAutoID . ',\'Customer\',\'CUS\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="fetchPage(\'system/customer/erp_customer_master_new\',' . $customerAutoID . ',\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;</span>';
        } else {
            $status .= '<spsn class="pull-right"><a onclick="attachment_modal(' . $customerAutoID . ',\'Customer\',\'CUS\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="fetchPage(\'system/customer/erp_customer_master_new\',' . $customerAutoID . ',\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_customer(' . $customerAutoID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('getcustomeramount')) {
    function getcustomeramount($customerAutoID, $customerCurrency, $customerCurrencyID)
    {
        // echo $to;
        $comid = current_companyID();
        $CI =& get_instance();
        $decimalplaces = $CI->db->query("SELECT decimalplaces FROM srp_erp_currencymaster WHERE currencyID=$customerCurrencyID ;")->row_array();
        $status = '<span class="">';
        $customeramount = $CI->db->query("SELECT sum( srp_erp_generalledger.transactionAmount/srp_erp_generalledger.partyExchangeRate ) as Amount FROM srp_erp_generalledger WHERE companyID = '$comid'
AND partyType = 'CUS'
AND partyAutoID = '$customerAutoID'
AND subLedgerType=3 ;")->row_array();
        $status .= '<spsn class=""><b>' . $customerCurrency . ' :</b> ' . number_format($customeramount['Amount'],
                $decimalplaces['decimalplaces']) . '</span>';

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('isPayrollCategoryStr')) {
    function isPayrollCategoryStr($isPayrollCategory)
    {
        return ($isPayrollCategory == '1') ? 'Payroll' : 'Non payroll';
    }
}
if (!function_exists('leaveApplicationEmployee')) {
    function leaveApplicationEmployee()
    {
        $CI =& get_instance();
        $com = current_companyID();
        $CI->db->select("EIdNo, ECode,DesDescription, IFNULL(Ename2, '') AS employee,isMonthly as policyMasterID,srp_employeesdetails.leaveGroupID, DepartmentDes");
        $CI->db->from('srp_employeesdetails');
        $CI->db->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID');
        $CI->db->join('srp_erp_leavegroup', 'srp_employeesdetails.leaveGroupID=srp_erp_leavegroup.leaveGroupID', 'INNER');
        $CI->db->join(' (
                         SELECT EmpID AS empID_Dep, DepartmentDes FROM srp_departmentmaster AS departTB
                         JOIN srp_empdepartments AS empDep ON empDep.DepartmentMasterID = departTB.DepartmentMasterID
                         WHERE departTB.Erp_companyID=' . $com . ' AND empDep.Erp_companyID=' . $com . ' AND empDep.isActive=1 GROUP BY EmpID
                     ) AS departTB', 'departTB.empID_Dep=srp_employeesdetails.EIdNo', 'left');
        $CI->db->where('srp_employeesdetails.Erp_companyID', $com);
        /*   $CI->db->where('isDischarged !=', 1);*/
        $result = $CI->db->get()->result_array();
        //echo $CI->db->last_query();
        return $result;
    }
}
if (!function_exists('all_employees')) {
    function all_employees($isDischarged = false)
    {
        $CI =& get_instance();

        $companyID = current_companyID();
        if($isDischarged){
            $data = $CI->db->query("SELECT * FROM `srp_employeesdetails` WHERE `Erp_companyID` = '{$companyID}' AND isDischarged !=1 ")->result_array();
        }else{
            $data = $CI->db->query("SELECT * FROM `srp_employeesdetails` WHERE `Erp_companyID` = '{$companyID}' ")->result_array();
        }


        return $data;
    }
}

if (!function_exists('all_employees_drop')) {
    function all_employees_drop($status = TRUE)
    {
        $CI =& get_instance();
        $CI->db->SELECT("EIdNo,Ename2");
        $CI->db->FROM('srp_employeesdetails');
        $CI->db->where('isDischarged !=', 1);
        $CI->db->where('isSystemAdmin !=', 1);
        $CI->db->where('Erp_companyID', current_companyID());

        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Employee');
        } else {
            $data_arr = '';
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['EIdNo'])] = trim($row['Ename2']);
            }
        }

        return $data_arr;
    }
}


if (!function_exists('editcustomercategory')) {
    function editcustomercategory($partyCategoryID)
    {
        // echo $to;
        $CI =& get_instance();
        $status = '<span class="pull-right">';
        $customercategory = $CI->db->query("select partyCategoryID from srp_erp_customermaster WHERE partyCategoryID=$partyCategoryID ;")->row_array();
        $suppliercategory = $CI->db->query("select partyCategoryID from srp_erp_suppliermaster WHERE partyCategoryID=$partyCategoryID ;")->row_array();
        if (!empty($customercategory || $suppliercategory)) {
            $status .= '<spsn class="pull-right"><a onclick="editcustomercategory(' . $partyCategoryID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;</span>';
        } else {
            $status .= '<spsn class="pull-right"><a onclick="editcustomercategory(' . $partyCategoryID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_category(' . $partyCategoryID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';
        }


        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('leavetype_bygroup')) {
    function leavetype_bygroup($leaveGroupID = FALSE)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $data = '';
        if ($leaveGroupID) {
            $data = $CI->db->query("SELECT t.leaveTypeID,t.description,isAllowminus,isCalenderDays FROM srp_erp_leavegroupdetails INNER JOIN ( SELECT * FROM `srp_erp_leavetype` ) t ON t.leaveTypeID = srp_erp_leavegroupdetails.leaveTypeID WHERE leaveGroupID = {$leaveGroupID}")->result_array();
        }

        return $data;
    }
}


if (!function_exists('party_category')) {
    function party_category($partyType, $status = TRUE)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $data = $CI->db->query("select partyCategoryID,partyType,categoryDescription from `srp_erp_partycategories` where companyID=$companyID and partyType=$partyType ")->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Category');
        } else {
            $data_arr = '';
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['partyCategoryID'])] = trim($row['categoryDescription']);
            }
        }

        return $data_arr;
    }

}

if (!function_exists('editsuppliercategory')) {
    function editsuppliercategory($partyCategoryID)
    {
        // echo $to;
        $CI =& get_instance();
        $status = '<span class="pull-right">';
        $customercategory = $CI->db->query("select partyCategoryID from srp_erp_customermaster WHERE partyCategoryID=$partyCategoryID ;")->row_array();
        $suppliercategory = $CI->db->query("select partyCategoryID from srp_erp_suppliermaster WHERE partyCategoryID=$partyCategoryID ;")->row_array();
        if (!empty($customercategory || $suppliercategory)) {
            $status .= '<spsn class="pull-right"><a onclick="editsuppliercategory(' . $partyCategoryID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;</span>';
        } else {
            $status .= '<spsn class="pull-right"><a onclick="editsuppliercategory(' . $partyCategoryID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_category(' . $partyCategoryID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';
        }


        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('isReportMasterConfigured')) {
    function isReportMasterConfigured($reportType)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $errorMsg_arr = array();

        //return 'Y'; die();

        $whereStr = '';
        if ($reportType != NULL) {
            $ssoReportColumnDetails = unserialize(ssoReportColumnDetails);
            $shortOrderColumn = $ssoReportColumnDetails[$reportType][0];
            $masterID = $ssoReportColumnDetails[$reportType][1];
            //$whereStr = "AND (masterTB.reportType='".$reportType."' OR masterTB.reportType IS NULL ) AND masterTB.".$shortOrderColumn." IS NOT NULL";
            $whereStr = "AND masterTB." . $shortOrderColumn . " IS NOT NULL";
        }
        $companyConf = $CI->db->query("SELECT masterTB.id, description, masterTable, inputName, reportValue, fieldName
                                  FROM srp_erp_sso_reporttemplatefields AS masterTB
                                  JOIN srp_erp_sso_reporttemplatedetails AS det ON det.reportID=masterTB.id AND det.companyID={$companyID} AND masterID={$masterID}
                                  WHERE isCompanyLevel=1 AND isFillable=1 {$whereStr}")->result_array();
        if (empty($companyConf)) {
            array(array_push($errorMsg_arr, 'Report configuration not done'));
        }

        $empConfig = $CI->db->query("SELECT * FROM srp_erp_sso_reporttemplatedetails AS detailTB
                                     JOIN srp_erp_sso_reporttemplatefields AS fieldsTB ON fieldsTB.id = detailTB.reportID
                                     WHERE companyID={$companyID} AND isEmployeeLevel=1")->result_array();
        //echo $CI->db->last_query();

        if (empty($empConfig)) {
            array(array_push($errorMsg_arr, 'Employee configuration not done'));
        }

        return (count($errorMsg_arr) == 0) ? 'Y' : 'N';

    }

}

if (!function_exists('isReportEmployeeConfigured')) {
    function isReportEmployeeConfigured($reportID)
    {
        $CI =& get_instance();
        $companyID = current_companyID();

        $result = $CI->db->query("SELECT EIdNo, ECode, Ename2, Ename3, description, temFieldsTB.id AS fieldID, inputName, reportValue
                                   FROM srp_employeesdetails AS empTB
                                   JOIN srp_erp_sso_reporttemplatefields AS temFieldsTB
                                   JOIN (
                                      SELECT empID, reportID, reportValue FROM srp_erp_sso_reporttemplatedetails WHERE companyID={$companyID} AND masterID={$reportID}
                                   ) AS temDetailsTB ON temDetailsTB.reportID = temFieldsTB.id AND EIdNo=empID
                                   WHERE Erp_companyID={$companyID} AND isPayrollEmployee=1 AND isDischarged=0
                                   AND temFieldsTB.isFillable=1 AND temFieldsTB.isEmployeeLevel=1 ORDER BY EIdNo, temFieldsTB.id")->result_array();

        //return $result;
        return (!empty($result)) ? 'Y' : 'N';
    }
}

if (!function_exists('getsupplieramount')) {
    function getsupplieramount($supplierAutoID, $supplierCurrency, $supplierCurrencyID)
    {
        // echo $to;
        $comid = current_companyID();
        $CI =& get_instance();
        $decimalplaces = $CI->db->query("SELECT decimalplaces FROM srp_erp_currencymaster WHERE currencyID=$supplierCurrencyID ;")->row_array();
        $status = '<span class="">';
        $supplieramount = $CI->db->query("SELECT sum( srp_erp_generalledger.transactionAmount/srp_erp_generalledger.partyExchangeRate )*-1 as Amount FROM srp_erp_generalledger WHERE companyID = '$comid'
AND partyType = 'SUP'
AND partyAutoID = '$supplierAutoID'
AND subLedgerType=2 ;")->row_array();
        $status .= '<spsn class=""><b>' . $supplierCurrency . ' :</b> ' . number_format($supplieramount['Amount'],
                $decimalplaces['decimalplaces']) . '</span>';

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('current_timezoneID')) {
    function current_timezoneID()
    {
        $CI =& get_instance();
        $userID = isset($CI->common_data['timezoneID']) ? $CI->common_data['timezoneID'] : NULL;

        return trim($userID);
    }
}

if (!function_exists('current_timezoneDescription')) {
    function current_timezoneDescription()
    {
        $CI =& get_instance();
        $userID = isset($CI->common_data['timezoneDescription']) ? $CI->common_data['timezoneDescription'] : NULL;

        return trim($userID);
    }
}

if (!function_exists('validate_date')) {
    function validate_date($date)
    {
        $CI =& get_instance();
        $DateFormate = convert_date_format();
        $date_format_policy = date_format_policy();
        $convertedDate = input_format_date($date, $date_format_policy);
        $d = DateTime::createFromFormat($DateFormate, $convertedDate);
        if ($convertedDate == "1970-01-01") {
            $CI->form_validation->set_message('validate_date', ' %s is not in correct date format');

            return FALSE;
        } else {
            return TRUE;
        }
    }
}

if (!function_exists('load_sc_action')) {
    function load_sc_action($poID, $POConfirmedYN, $approved, $createdUserID, $isDeleted)
    {
        //return $POConfirmedYN;
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Sales Commission","SC",' . $POConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;';
        if ($isDeleted == 1) {
            $status .= '<a onclick="reOpen_contract(' . $poID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>';
        }
        if ($createdUserID == trim($CI->session->userdata("empID")) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<a onclick="referbacksc(' . $poID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }
        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<a onclick=\'fetchPage("system/sales/sale_commission_generate",' . $poID . ',"Edit Sales Commission","SC"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'SC\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a target="_blank" href="' . site_url('sales/load_sc_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a onclick="delete_item(' . $poID . ',\'Sales Commission\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        if ($POConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'SC\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a target="_blank" href="' . site_url('sales/load_sc_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('getPolicyValues')) {
    function getPolicyValues($code, $documentCode)
    {
        $CI =& get_instance();
        $policyValues = null;
        $policyArr = $CI->common_data['company_policy'];

        if (array_key_exists($code, $policyArr)) {
            if (array_key_exists($documentCode, $policyArr[$code])) {
                $policyValues = $policyArr[$code][$documentCode][0]["policyvalue"];
            }
        }
        return $policyValues;
    }
}

if (!function_exists('sc_action_approval')) {
    function sc_action_approval($poID, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Sales Commission","SC");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'SC\',\'' . $poID . '\',\' \',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('payee_drop')) {
    function payee_drop()/*Load all payee masters*/
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $data = $CI->db->query("SELECT paygroupmaster.payGroupID , payeemaster.Description
                        FROM srp_erp_payeemaster AS payeemaster
                        JOIN srp_erp_paygroupmaster AS paygroupmaster ON paygroupmaster.payeeID = payeemaster.payeeMasterID AND paygroupmaster.companyID={$companyID}
                        WHERE payeemaster.companyID={$companyID}")->result_array();

        $data_arr = array('' => 'Select Payee');

        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['payGroupID'])] = trim($row['Description']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('get_defaultPayeeSetup')) {
    function get_defaultPayeeSetup()/*Load all payee masters setup*/
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $result = $CI->db->query("SELECT reportValue FROM srp_erp_sso_reporttemplatedetails
                                  WHERE companyID={$companyID} AND masterID=4 ORDER BY reportID ")->result_array();

        if (!empty($result)) {
            $data = array();
            $data['payee'] = $result[0]['reportValue'];
            $data['payGroup'] = $result[1]['reportValue'];
            $data['regNo'] = $result[2]['reportValue'];

            return $data;
        } else {
            return $result;
        }

    }
}

if (!function_exists('payGroup_drop')) {
    function payGroup_drop($dropDown = NULL)/*Load all pay groups masters*/
    {
        $CI =& get_instance();
        $companyID = current_companyID();

        $data = $CI->db->query("SELECT payGroupID, description FROM srp_erp_paygroupmaster
                                WHERE companyID={$companyID} AND isGroupTotal=1 ")->result_array();

        if ($dropDown == 'Y') {
            $data_arr = array('' => '');

            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['payGroupID'])] = trim($row['description']);
                }
            }

            return $data_arr;
        } else {
            return $data;
        }

    }
}

if (!function_exists('getPeriods')) {
    function getPeriods($yearStart, $yearEnd)
    {
        $data = array();
        $i = 0;
        while ($i < 12) {
            //$monthStart = ($i == 0)? $yearStart:  date('Y-m-d', strtotime($yearStart.' + 1 month'));
            $monthStart = $yearStart;
            $monthEnd = date('Y-m-t', strtotime($monthStart));
            $data[$i]['dateFrom'] = $monthStart;
            $data[$i]['dateTo'] = $monthEnd;

            $yearStart = date('Y-m-d', strtotime($yearStart . ' + 1 month'));
            $i++;
        }

        return $data;
    }
}

if (!function_exists('fetch_glcode_claim_category')) {
    function fetch_glcode_claim_category($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('systemAccountCode,GLAutoID,GLDescription');
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('masterCategory', 'PL');
        $CI->db->where('isActive', 1);
        $CI->db->where('masterAccountYN ', 0);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($state == TRUE) {
            $data_arr = array('' => $CI->lang->line('common_select_gl_code')/*'Select GL Code'*/);
        } else {
            $data_arr = '';
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLDescription']);
            }

            return $data_arr;
        }
    }
}

if (!function_exists('fetch_claim_category')) {
    function fetch_claim_category($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('expenseClaimCategoriesAutoID,glCode,claimcategoriesDescription');
        $CI->db->from('srp_erp_expenseclaimcategories');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($state == TRUE) {
            $data_arr = array('' => $CI->lang->line('common_select_claim_category')/*'Select Claim Category'*/);
        } else {
            $data_arr = '';
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['expenseClaimCategoriesAutoID'])] = trim($row['glCode']) . ' | ' . trim($row['claimcategoriesDescription']);
            }

            return $data_arr;
        }
    }

}

if (!function_exists('load_subItem_notSold_report')) {
    function load_subItem_notSold_report($itemAutoID, $warehouseAutoID)
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT * FROM srp_erp_itemmaster_sub iSub  WHERE iSub.itemAutoID = '" . $itemAutoID . "' AND    ( ISNULL(iSub.isSold) OR iSub.isSold = '' OR iSub.isSold = 0 ) AND iSub.wareHouseAutoID = '" . $warehouseAutoID . "' ")->result_array();

        return $data;
    }

}


/** Created by shafri on 16-05-2017 */
if (!function_exists('all_customer_drop')) {
    function all_customer_drop($status = TRUE) /*Load all Supplier*/
    {
        $CI =& get_instance();
        $CI->db->select("customerAutoID,customerName,customerSystemCode,customerCountry");
        $CI->db->from('srp_erp_customermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $customer = $CI->db->get()->result_array();
        if ($status) {
            $customer_arr = array('' => 'Select Customer');
        } else {
            $customer_arr = '';
        }
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['customerAutoID'])] = (trim($row['customerSystemCode']) ? trim($row['customerSystemCode']) . ' | ' : '') . trim($row['customerName']) . (trim($row['customerCountry']) ? ' | ' . trim($row['customerCountry']) : '');
            }
        }

        return $customer_arr;
    }
}

if (!function_exists('all_group_customer_drop')) {
    function all_group_customer_drop($status = TRUE) /*Load all Supplier*/
    {

        $companies = getallsubGroupCompanies();
        $masterGroupID=getParentgroupMasterID();
    
        $customer = false;
        if ($companies) {
            $CI =& get_instance();
            $CI->db->select("groupCustomerAutoID,groupCustomerName,groupcustomerSystemCode,customerCountry");
            $CI->db->from('srp_erp_groupcustomermaster');
            $CI->db->join('srp_erp_groupcustomerdetails', 'srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID', 'INNER');
            $CI->db->where('srp_erp_groupcustomermaster.companyGroupID', $masterGroupID);
            $CI->db->where_in('srp_erp_groupcustomerdetails.companyID', $companies);
            $CI->db->group_by('groupCustomerAutoID');
            $customer = $CI->db->get()->result_array();
        }

        if ($status) {
            $customer_arr = array('' => 'Select Customer');
        } else {
            $customer_arr = '';
        }
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['groupCustomerAutoID'])] = (trim($row['groupcustomerSystemCode']) ? trim($row['groupcustomerSystemCode']) . ' | ' : '') . trim($row['groupCustomerName']) . (trim($row['customerCountry']) ? ' | ' . trim($row['customerCountry']) : '');
            }
        }

        return $customer_arr;
    }
}


if (!function_exists('all_sales_person_drop')) {
    function all_sales_person_drop($status = TRUE)/*Load all Sales person*/
    {
        $CI =& get_instance();
        $CI->db->select("salesPersonID,SalesPersonName,SalesPersonCode");
        $CI->db->from('srp_erp_salespersonmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $supplier = $CI->db->get()->result_array();
        if ($status) {
            $supplier_arr = array('' => 'Select Sales person');
        } else {
            $supplier_arr = '';
        }
        if (isset($supplier)) {
            foreach ($supplier as $row) {
                $supplier_arr[trim($row['salesPersonID'])] = trim($row['SalesPersonCode']) . ' | ' . trim($row['SalesPersonName']);
            }
        }

        return $supplier_arr;
    }
}

/** Created by Nasik on 18-05-2017 */
if (!function_exists('lastPayrollProcessedForEmp')) {
    function lastPayrollProcessedForEmp($empID, $payrollType = NULL)
    {
        $CI =& get_instance();
        $companyID = current_companyID();

        if ($payrollType == 2) {
            $masterTB = 'srp_erp_non_payrollmaster';
            $detailTB = 'srp_erp_non_payrollheaderdetails';
        } else {
            $masterTB = 'srp_erp_payrollmaster';
            $detailTB = 'srp_erp_payrollheaderdetails';
        }
        $lastDate = $CI->db->query("SELECT MAX( STR_TO_DATE(CONCAT(payrollYear,'-',payrollMonth,'-01'), '%Y-%m-%d') ) lastDate
                                     FROM {$masterTB} AS masterTB
                                     JOIN {$detailTB} AS detailTB ON detailTB.payrollMasterID = masterTB.payrollMasterID
                                     AND EmpID = {$empID} AND detailTB.companyID={$companyID}
                                     WHERE masterTB.companyID={$companyID}")->row('lastDate');

        return $lastDate;
    }
}

if (!function_exists('get_template_drop')) {
    function get_template_drop($FormCatID)
    {
        $CI =& get_instance();
        $CI->db->select("TempMasterID,FormCatID,TempDes");
        $CI->db->from('srp_erp_templatemaster');
        $CI->db->where('FormCatID', $FormCatID);
        $group = $CI->db->get()->result_array();

        $tempalte_arr = '';
        if (isset($group)) {
            foreach ($group as $row) {
                $tempalte_arr[trim($row['TempMasterID'] . ' | ' . $row['FormCatID'])] = trim($row['TempDes']);
            }
        }

        return $tempalte_arr;
    }
}


if (!function_exists('get_salaryComparison')) {
    function get_salaryComparison($type = NULL)
    {
        $CI =& get_instance();
        $companyID = current_companyID();

        if ($type == 'left') {
            $comparison = $CI->db->query("SELECT * FROM srp_erp_salarycomparisonsystemtable AS comparisonMaster
                                           LEFT JOIN(
                                                  SELECT formulaID,masterID,formulaStr FROM srp_erp_salarycomparisonformula WHERE companyID={$companyID}
                                           )  AS formulaTB ON formulaTB.masterID = comparisonMaster.id ")->result_array();
        } else {
            $comparison = $CI->db->query("SELECT masterID, formulaStr, description FROM srp_erp_salarycomparisonformula AS formulaTB
                                  JOIN srp_erp_salarycomparisonsystemtable AS sysTB ON sysTB.id=formulaTB.masterID
                                  WHERE companyID={$companyID}")->result_array();
        }

        return $comparison;
    }
}

if (!function_exists('formulaDecode')) {
    function formulaDecode($formula)
    {
        $salary_categories_arr = salary_categories(array('A', 'D'));
        $payGroup_arr = get_payGroup(1);
        $operand_arr = operand_arr();
        $formulaText = '';

        $formula_arr = explode('|', $formula); // break the formula

        foreach ($formula_arr as $formula_row) {
            if (trim($formula_row) != '') {
                if (in_array($formula_row, $operand_arr)) { //validate is a operand

                    $formulaText .= '<li class="formula-li formula-operation" data-value="|' . $formula_row . '|" onclick="addSelectedClass(this)">';
                    $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                    $formulaText .= '<span class="formula-text-value">' . $formula_row . '</span></li>';
                } else {

                    $elementType = $formula_row[0];


                    if ($elementType == '_') {
                        /*** Number ***/
                        $numArr = explode('_', $formula_row);
                        $num = (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];

                        $formulaText .= '<li class="formula-li formula-number" data-value="_' . $num . '_" onclick="addSelectedClass(this)">';
                        $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                        $formulaText .= '<span class="formula-text-value" style="display:none">' . $num . '</span>';
                        $formulaText .= '<input type="text" class="formula-number-text" onkeyup="updateDataValue(this)" value="' . $num . '"></li>';
                    } else if ($elementType == '@') {
                        /*** SSO ***/
                        $SSO_Arr = explode('@', $formula_row);
                        $keys = array_keys(array_column($payGroup_arr, 'payGroupID'), $SSO_Arr[1]);
                        $new_array = array_map(function ($k) use ($payGroup_arr) {
                            return $payGroup_arr[$k];
                        }, $keys);

                        $ssoDescription = (!empty($new_array[0])) ? trim($new_array[0]['description']) : '';

                        $formulaText .= '<li class="formula-li" data-value="@' . $SSO_Arr[1] . '" onclick="addSelectedClass(this)">';
                        $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                        $formulaText .= '<span class="formula-text-value">' . $ssoDescription . '</span></li>';
                    } else if ($elementType == '#') {
                        /*** Salary category ***/
                        $catArr = explode('#', $formula_row);
                        $keys = array_keys(array_column($salary_categories_arr, 'salaryCategoryID'), $catArr[1]);
                        $new_array = array_map(function ($k) use ($salary_categories_arr) {
                            return $salary_categories_arr[$k];
                        }, $keys);

                        $salaryDescription = (!empty($new_array[0])) ? trim($new_array[0]['salaryDescription']) : '';

                        $formulaText .= '<li class="formula-li" data-value="#' . $catArr[1] . '" onclick="addSelectedClass(this)">';
                        $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                        $formulaText .= '<span class="formula-text-value">' . $salaryDescription . '</span></li>';
                    } else if ($elementType == '~') {
                        /*** Pay group ***/
                        $payGroup_Arr = explode('~', $formula_row);
                        $keys = array_keys(array_column($payGroup_arr, 'payGroupID'), $payGroup_Arr[1]);
                        $new_array = array_map(function ($k) use ($payGroup_arr) {
                            return $payGroup_arr[$k];
                        }, $keys);

                        $payGrpDescription = (!empty($new_array[0])) ? trim($new_array[0]['description']) : '';

                        $formulaText .= '<li class="formula-li" data-value="~' . $payGroup_Arr[1] . '" onclick="addSelectedClass(this)">';
                        $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                        $formulaText .= '<span class="formula-text-value">' . $payGrpDescription . '</span></li>';
                    } else if ($elementType == '!') {
                        $monthlyADArr = explode('!', $formula_row);

                        if (trim($monthlyADArr[1]) == '0') {
                            /*** Balance Payment ***/

                            $formulaText .= '<li class="formula-li" data-value="!0" onclick="addSelectedClass(this)">';
                            $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                            $formulaText .= '<span class="formula-text-value">Balance Payment</span></li>';

                        } else if ($monthlyADArr[1] == 'MA' || $monthlyADArr[1] == 'MD') {
                            /*** Monthly Addition or Monthly Deduction ***/

                            $description = ($monthlyADArr[1] == 'MA') ? 'Monthly Addition' : 'Monthly Deduction';

                            $formulaText .= '<li class="formula-li" data-value="!' . $monthlyADArr[1] . '" onclick="addSelectedClass(this)">';
                            $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                            $formulaText .= '<span class="formula-text-value">' . $description . '</span></li>';

                        }
                    }
                }
            }
        }
        return $formulaText;
    }
}

if (!function_exists('formulaDecode2')) {
    function formulaDecode2($formulaData = array())
    {

        $CI =& get_instance();
        $companyID = current_companyID();
        $formula = trim($formulaData['formulaString']);
        $formulaText = '';

        $salary_categories_arr = salary_categories(array('A', 'D'));
        $payGroup_arr = get_payGroup();
        $salaryCatID = array();
        $operand_arr = operand_arr();

        $formula_arr = explode('|', $formula); // break the formula

        foreach ($formula_arr as $formula_row) {

            if (trim($formula_row) != '') {
                if (in_array($formula_row, $operand_arr)) { //validate is a operand

                    $formulaText .= '<li class="formula-li formula-operation" data-value="|' . $formula_row . '|" onclick="addSelectedClass(this)">';
                    $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                    $formulaText .= '<span class="formula-text-value">' . $formula_row . '</span></li>';
                } else {
                    $isNotCat = strpos($formula_row, '_'); // check is a amount

                    /********************************************************************************************
                     * If a amount remove '_' symbol and append in the formula
                     * if a salary category  remove '#' symbol and append in the formula
                     * else if it's a balance payment '!' because there is no MA or MD in SSO formula builder
                     ********************************************************************************************/
                    if ($isNotCat !== false) {
                        $numArr = explode('_', $formula_row);
                        $num = (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];

                        $formulaText .= '<li class="formula-li formula-number" data-value="_' . $num . '_" onclick="addSelectedClass(this)">';
                        $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                        $formulaText .= '<span class="formula-text-value" style="display:none">' . $num . '</span>';
                        $formulaText .= '<input type="text" class="formula-number-text" onkeyup="updateDataValue(this)" value="' . $num . '"></li>';
                    } else {

                        $isNotSSO = strpos($formula_row, '@');
                        /**Salary Category or SSO type**/


                        if ($isNotSSO !== false) { // SSO type
                            $SSO_Arr = explode('@', $formula_row);
                            $keys = array_keys(array_column($payGroup_arr, 'payGroupID'), $SSO_Arr[1]);
                            $new_array = array_map(function ($k) use ($payGroup_arr) {
                                return $payGroup_arr[$k];
                            }, $keys);

                            $ssoDescription = (!empty($new_array[0])) ? trim($new_array[0]['description']) : '';

                            $formulaText .= '<li class="formula-li" data-value="@' . $SSO_Arr[1] . '" onclick="addSelectedClass(this)">';
                            $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                            $formulaText .= '<span class="formula-text-value">' . $ssoDescription . '</span></li>';

                        } else {
                            $isNotSalaryCat = strpos($formula_row, '#'); //Salary Category or SSO type


                            if ($isNotSalaryCat !== false) { // salary category type
                                $catArr = explode('#', $formula_row);
                                $keys = array_keys(array_column($salary_categories_arr, 'salaryCategoryID'), $catArr[1]);
                                $new_array = array_map(function ($k) use ($salary_categories_arr) {
                                    return $salary_categories_arr[$k];
                                }, $keys);

                                $salaryDescription = (!empty($new_array[0])) ? trim($new_array[0]['salaryDescription']) : '';

                                $formulaText .= '<li class="formula-li" data-value="#' . $catArr[1] . '" onclick="addSelectedClass(this)">';
                                $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                                $formulaText .= '<span class="formula-text-value">' . $salaryDescription . '</span></li>';


                            } else {
                                $monthlyADArr = explode('!', $formula_row);

                                if (trim($monthlyADArr[1]) == '0') {

                                    $formulaText .= '<li class="formula-li" data-value="!0" onclick="addSelectedClass(this)">';
                                    $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                                    $formulaText .= '<span class="formula-text-value">Balance Payment</span></li>';

                                } else if ($monthlyADArr[1] == 'MA' || $monthlyADArr[1] == 'MD') {

                                    $description = ($monthlyADArr[1] == 'MA') ? 'Monthly Addition' : 'Monthly Deduction';

                                    $formulaText .= '<li class="formula-li" data-value="!' . $monthlyADArr[1] . '" onclick="addSelectedClass(this)">';
                                    $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                                    $formulaText .= '<span class="formula-text-value">' . $description . '</span></li>';

                                }

                            }

                        }
                    }

                }
            }

        }
        return $formulaText;
    }
}

if (!function_exists('machine_type_drop')) {
    function machine_type_drop()
    {

        $CI =& get_instance();
        $CI->db->select("machineTypeID,description");
        $CI->db->from('srp_erp_machinetype');
        $group = $CI->db->get()->result_array();

        $tempalte_arr = ['' => 'Please select'];

        if (isset($group)) {
            foreach ($group as $row) {

                $tempalte_arr[trim($row['machineTypeID'])] = trim($row['description']);

            }
        }

        return $tempalte_arr;
    }
}

if (!function_exists('edit_machine_type')) {
    function edit_machine_type($sortOrder, $ID, $detailID)
    {
        $CI =& get_instance();
        $data = $CI->db->query("select * from srp_erp_machinedetail WHERE machineMasterID={$ID} order by sortOrder ASC")->result_array();
        if (!empty($data)) {
            $html = '<select id="sortOrder" name="sortOrder" onchange="edit_updateSortOrder(this.value,' . $ID . ',' . $detailID . ')">';
            foreach ($data as $row) {
                $selected = ($sortOrder == $row['sortOrder'] ? 'selected' : '');
                $html .= '<option ' . $selected . ' value="' . $row['sortOrder'] . '">' . $row['sortOrder'] . '</option>';
            }
            $html .= '</select>';
        }

        return $html;
    }
}

if (!function_exists('machine_drop')) {
    function machine_drop()
    {

        $CI =& get_instance();
        $CI->db->select("machineMasterID,description");
        $CI->db->from('srp_erp_machinemaster');
        $group = $CI->db->get()->result_array();

        $tempalte_arr = ['' => 'Please select'];

        if (isset($group)) {
            foreach ($group as $row) {

                $tempalte_arr[trim($row['machineMasterID'])] = trim($row['description']);

            }
        }

        return $tempalte_arr;
    }
}

if (!function_exists('connection_drop')) {
    function connection_drop()
    {

        $CI =& get_instance();
        $data = $CI->db->query(" SELECT dbYN,connectionTypeID,connectionType FROM srp_erp_machine_connection")->result_array();

        return $data;
    }
}

if (!function_exists('edit_machine_mapping')) {
    function edit_machine_mapping($machineTypeID, $ID, $detailID)
    {
        $CI =& get_instance();
        $data = $CI->db->query("select * from srp_erp_machinetype")->result_array();
        if (!empty($data)) {
            $html = '<select id="machineTypeID" name="machineTypeID" onchange="edit_updatemachinetype(this.value,' . $ID . ',' . $detailID . ')">';
            $html .= '<option></option>';
            foreach ($data as $row) {
                $selected = ($machineTypeID == $row['machineTypeID'] ? 'selected' : '');
                $html .= '<option ' . $selected . ' value="' . $row['machineTypeID'] . '">' . $row['description'] . '</option>';
            }
            $html .= '</select>';
        }

        return $html;
    }
}


if (!function_exists('getPrimaryLanguage')) {
    function getPrimaryLanguage()
    {
        $CI =& get_instance();
        $CI->load->library('company_language');
        $idiom = $CI->company_language->getPrimaryLanguage();

        return $idiom;
    }
}


if (!function_exists('language_string_conversion')) {
    function language_string_conversion($string, $masterID = NULL)
    {
        $outputString = strtolower(str_replace(array('-', ' ', '&', '/'), array('_', '_', '_', '_'), $masterID . '_' . trim($string)));

        return $outputString;
    }
}


if (!function_exists('getSecondaryLanguage')) {
    function getSecondaryLanguage()
    {
        $CI =& get_instance();
        $CI->load->library('company_language');
        $idiom = $CI->company_language->getSecondaryLanguage();

        return $idiom;
    }
}

if (!function_exists('fetch_main_group')) {
    function fetch_main_group($id = FALSE, $state = FALSE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();
        /*$CI->db->select('companyGroupDetailID,srp_erp_companygroupmaster.description as groupdescription');
        $CI->db->join('srp_erp_companygroupmaster',
            'srp_erp_companygroupdetails.companyGroupID = srp_erp_companygroupmaster.companyGroupID');
        $CI->db->from('srp_erp_companygroupdetails');
        $CI->db->where('srp_erp_companygroupdetails.companyGroupID', $CI->common_data['company_data']['company_id']);*/

        $CI->db->select('companyGroupID,description');
        $CI->db->from('srp_erp_companygroupmaster');


        $data = $CI->db->get()->result_array();


        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['companyGroupID'])] = trim($row['description']);
            }

            return $data_arr;
        }
    }
}

if (!function_exists('dropdown_subGroup')) {
    function dropdown_subGroup($groupID = false, $all = FALSE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();
        $CI->db->select('companySubGroupID,companyGroupID,description');
        $CI->db->from('srp_erp_companysubgroupmaster');
        if ($groupID) {
            $CI->db->where('companyGroupID', $groupID);
        }

        $data = $CI->db->get()->result_array();


        $data_arr = [];
        if ($all) {
            $data_arr = ['' => 'All'];
        }

        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['companySubGroupID'])] = trim($row['description']);
            }

            return $data_arr;
        }
    }
}


if (!function_exists('all_not_assigned_employee_to_subGroup')) {
    function all_not_assigned_employee_to_subGroup($GroupID)
    {
        $CI =& get_instance();
        $customer_arr = array();

        $master = $CI->db->query("select masterID FROM  srp_erp_companygroupmaster WHERE companyGroupID=$GroupID")->row_array();


        $employees = $CI->db->query("SELECT
	EIdNo,
	Ename2,
	EmpSecondaryCode,
	Erp_companyID ,
	subGroupEmpID
FROM
	( SELECT EIdNo, Ename2, EmpSecondaryCode, Erp_companyID FROM `srp_erp_companygroupdetails` INNER JOIN srp_employeesdetails ON srp_employeesdetails.Erp_companyID = srp_erp_companygroupdetails.companyID WHERE parentID = {$master['masterID']} ) t
	LEFT JOIN 
	(
	SELECT subGroupEmpID,EmpID FROM srp_erp_companysubgroupemployees 
	INNER JOIN 
	srp_erp_companysubgroupmaster ON srp_erp_companysubgroupemployees.companySubGroupID=srp_erp_companysubgroupmaster.companySubGroupID
	WHERE srp_erp_companysubgroupmaster.companyGroupID = $GroupID
	)x
	 ON 
	 t.EIdNo <> x.EmpID 
")->result_array();


        if ($employees) {
            foreach ($employees as $row) {
                $customer_arr[trim($row['EIdNo'])] = trim($row['EmpSecondaryCode']) . ' | ' . trim($row['Ename2']);
            }
        }

        return $customer_arr;
    }
}

if (!function_exists('payrollMonth_dropDown')) {
    function payrollMonth_dropDown($isNonPayroll = NULL)
    {
        $companyID = current_companyID();
        $tableName = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $data = $CI->db->query("SELECT DATE_FORMAT(CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y-%m-%d') AS monthID,
                                 DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y - %M') AS monthStr
                                 FROM(
                                    SELECT payrollYear, payrollMonth, DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y-%m-%d') payrollDate
                                    FROM {$tableName} WHERE companyID={$companyID} AND approvedYN=1
                                 ) AS payrollDateTB GROUP BY payrollDate ORDER BY payrollDate DESC")->result_array();


        $payroll_arr = array('' => $CI->lang->line('common_please_select'));
        //$payroll_arr = array('' =>  $CI->lang->line('common_please_select')/*'Please Select'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $payroll_arr[trim($row['monthID'])] = trim($row['monthStr']);

            }
        }

        return $payroll_arr;
    }
}

if (!function_exists('payrollMonth_dropDown_with_visible_date')) {
    function payrollMonth_dropDown_with_visible_date($isNonPayroll = NULL)
    {
        $companyID = current_companyID();
        $tableName = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $data = $CI->db->query("SELECT DATE_FORMAT(CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y-%m-%d') AS monthID,
                                 DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y - %M') AS monthStr
                                 FROM(
                                    SELECT payrollYear, payrollMonth, DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y-%m-%d') payrollDate
                                    FROM {$tableName} WHERE companyID={$companyID} AND approvedYN=1 AND visibleDate <= CURDATE()
                                 ) AS payrollDateTB GROUP BY payrollDate ORDER BY payrollDate DESC")->result_array();


        $payroll_arr = array('' => $CI->lang->line('common_please_select'));
        //$payroll_arr = array('' =>  $CI->lang->line('common_please_select')/*'Please Select'*/);
        if (isset($data)) {
            foreach ($data as $row) {
                $payroll_arr[trim($row['monthID'])] = trim($row['monthStr']);

            }
        }

        return $payroll_arr;
    }
}


if (!function_exists('user_group')) {
    function user_group()/*Group */
    {
        $CI =& get_instance();
        $CI->load->library('session');

        return trim($CI->session->userdata("usergroupID"));
    }
}

if (!function_exists('fetch_employee_ec')) {
    function fetch_employee_ec($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->select('EIdNo,Ename2,ECode');
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('Erp_companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('isSystemAdmin !=', 1);
        $CI->db->where('isDischarged', 0);
        $data = $CI->db->get()->result_array();
        if ($state == TRUE) {
            $data_arr = array('' => $CI->lang->line('common_select_employee')/*'Select Employee'*/);
        } else {
            $data_arr = '';
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['EIdNo']) . '|' . trim($row['Ename2'])] = trim($row['ECode']) . ' | ' . trim($row['Ename2']);
            }
        }

        return $data_arr;
    }
}

/** Created by Nasik on 06-06-2017 */
if (!function_exists('isPayrollProcessedForEmpGroup')) {
    function isPayrollProcessedForEmpGroup($empID, $payYear, $payMonth, $isNonPayroll)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $headerDetailTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollheaderdetails' : 'srp_erp_payrollheaderdetails';
        $payrollMaster = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';

        $lastDate = $CI->db->query("SELECT CONCAT(ECode,' - ', Ename2) AS empData
                                     FROM {$payrollMaster} AS masterTB
                                     JOIN {$headerDetailTB} AS detailTB ON detailTB.payrollMasterID = masterTB.payrollMasterID
                                     AND EmpID IN ({$empID}) AND detailTB.companyID={$companyID}
                                     WHERE masterTB.companyID={$companyID} AND payrollYear={$payYear} AND payrollMonth={$payMonth}")->result_array();

        return $lastDate;
    }
}

if (!function_exists('editcustomerGroup')) {
    function editcustomerGroup($customerAutoID)
    {
        // echo $to;
        $CI =& get_instance();
        $status = '<span class="pull-right">';
        $customerinvoice = $CI->db->query("select customerID from srp_erp_customerinvoicemaster WHERE customerID=$customerAutoID ;")->row_array();
        $customerreceipt = $CI->db->query("select customerID from srp_erp_customerreceiptmaster WHERE customerID=$customerAutoID ;")->row_array();
        $creditnote = $CI->db->query("select customerID from srp_erp_creditnotemaster WHERE customerID=$customerAutoID ;")->row_array();
        $generalledger = $CI->db->query("select partyautoID from srp_erp_generalledger WHERE partyautoID=$customerAutoID AND partyType = 'CUS';")->row_array();
        $status .= '<spsn class="pull-right"><a onclick="load_duplicate_customer(' . $customerAutoID . ')"><span title="Replicate" rel="tooltip"class="glyphicon glyphicon-duplicate"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="openLinkModal(' . $customerAutoID . ')"><span title="Link" rel="tooltip" class="glyphicon glyphicon-link"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;';
        $status .= '<a onclick="fetchPage(\'system/GroupMaster/erp_customer_group_master_new\',' . $customerAutoID . ',\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a></span>';
        /* if (!empty($customerinvoice || $customerreceipt || $creditnote || $generalledger)) {
             $status .= '<spsn class="pull-right"><a onclick="fetchPage(\'system/GroupMaster/erp_customer_group_master_new\',' . $customerAutoID . ',\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;</span>';
         } else {
             $status .= '<spsn class="pull-right"><a onclick="fetchPage(\'system/GroupMaster/erp_customer_group_master_new\',' . $customerAutoID . ',\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_customer(' . $customerAutoID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';
         }*/
        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('all_customer_grp_drop')) {
    function all_customer_grp_drop($status = TRUE)/*Load all Customer*/
    {

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $CI->db->select("groupCustomerAutoID,groupCustomerName,groupcustomerSystemCode,customerCountry");
        $CI->db->from('srp_erp_groupcustomermaster');
        $CI->db->where('companygroupID', $companyGroup['companyGroupID']);
        $customer = $CI->db->get()->result_array();
        if ($status) {
            $customer_arr = array('' => 'Select Customer');
        } else {
            $customer_arr = '';
        }
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['groupCustomerAutoID'])] = (trim($row['groupcustomerSystemCode']) ? trim($row['groupcustomerSystemCode']) . ' | ' : '') . trim($row['groupCustomerName']) . (trim($row['customerCountry']) ? ' | ' . trim($row['customerCountry']) : '');
            }
        }

        return $customer_arr;
    }
}


if (!function_exists('all_supplier_group_drop')) {
    function all_supplier_group_drop($status = TRUE)/*Load all Supplier*/
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $CI->db->select("groupSupplierAutoID,groupSupplierName,groupSupplierSystemCode,supplierCountry");
        $CI->db->from('srp_erp_groupsuppliermaster');
        $CI->db->where('companygroupID', $companyGroup['companyGroupID']);
        $supplier = $CI->db->get()->result_array();
        if ($status) {
            $supplier_arr = array('' => 'Select Supplier');
        } else {
            $supplier_arr = '';
        }
        if (isset($supplier)) {
            foreach ($supplier as $row) {
                $supplier_arr[trim($row['groupSupplierAutoID'])] = (trim($row['groupSupplierSystemCode']) ? trim($row['groupSupplierSystemCode']) . ' | ' : '') . trim($row['groupSupplierName']) . (trim($row['supplierCountry']) ? ' | ' . trim($row['supplierCountry']) : '');
            }
        }

        return $supplier_arr;
    }
}

if (!function_exists('customer_company_link')) {
    function customer_company_link($groupCustomerMasterID, $status = TRUE)/*Load all Customer*/
    {
        $companies = getallsubGroupCompanies();
        $masterGroupID=getParentgroupMasterID();
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        //$companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE companyGroupID = {$companyID}")->row_array();
        $companygroupID = $companyID;
        $customer = $CI->db->query("SELECT
	srp_erp_companygroupdetails.companyID
FROM
	srp_erp_companygroupdetails
	JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_companygroupdetails.companyID
WHERE parentID = ($masterGroupID) ")->result_array();
        return $customer;
    }
}


if (!function_exists('dropdown_companyCustomers')) {
    function dropdown_companyCustomers($companyID, $customerMasterID = null)
    {
        $CI =& get_instance();
        $employees = array();

        if ($companyID != '') {

            $employees = $CI->db->query("SELECT
	customerAutoID,customerSystemCode,customerName
FROM
	srp_erp_customermaster
WHERE companyID = ($companyID) AND NOT EXISTS
        (
        SELECT  customerMasterID
        FROM    srp_erp_groupcustomerdetails
        WHERE   srp_erp_customermaster.customerAutoID = srp_erp_groupcustomerdetails.customerMasterID
        )")->result_array();

        }
        if ($customerMasterID != '') {
            $cust = $CI->db->query("SELECT
	customerAutoID,customerSystemCode,customerName
FROM
	srp_erp_customermaster
WHERE customerAutoID = ($customerMasterID)")->row_array();
        }
        $data_arr = array('' => 'Select Customer');
        if (!empty($cust)) {
            $data_arr[trim($cust['customerAutoID'])] = trim($cust['customerSystemCode']) . ' | ' . trim($cust['customerName']);
        }
        if ($employees) {
            foreach ($employees as $row) {
                $data_arr[trim($row['customerAutoID'])] = trim($row['customerSystemCode']) . ' | ' . trim($row['customerName']);
            }
        }

        return $data_arr;
    }
}


if (!function_exists('editgroupcategory')) {
    function editgroupcategory($partyCategoryID)
    {
        // echo $to;
        $CI =& get_instance();
        $status = '<span class="pull-right">';
        $customercategory = $CI->db->query("select partyCategoryID from srp_erp_groupcustomermaster WHERE partyCategoryID=$partyCategoryID ;")->row_array();
        //$suppliercategory = $CI->db->query("select partyCategoryID from srp_erp_groupsuppliermaster WHERE partyCategoryID=$partyCategoryID ;")->row_array();
        if (!empty($customercategory)) {
            $status .= '<spsn class="pull-right"><a onclick="link_group_customer_category(' . $partyCategoryID . ')"><span title="" rel="tooltip" class="glyphicon glyphicon-link" data-original-title="Link"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="editcustomercategory(' . $partyCategoryID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;</span>';
        } else {
            $status .= '<spsn class="pull-right"><a onclick="link_group_customer_category(' . $partyCategoryID . ')"><span title="" rel="tooltip" class="glyphicon glyphicon-link" data-original-title="Link"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="editcustomercategory(' . $partyCategoryID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_category(' . $partyCategoryID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';
        }


        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('editsuppliergroupcategory')) {
    function editsuppliergroupcategory($partyCategoryID)
    {
        // echo $to;
        $CI =& get_instance();
        $status = '<span class="pull-right">';
        //$customercategory = $CI->db->query("select partyCategoryID from srp_erp_customermaster WHERE partyCategoryID=$partyCategoryID ;")->row_array();
        $suppliercategory = $CI->db->query("select partyCategoryID from srp_erp_groupsuppliermaster WHERE partyCategoryID=$partyCategoryID ;")->row_array();
        if (!empty($suppliercategory)) {
            $status .= '<spsn class="pull-right"><a onclick="link_group_supplier_category(' . $partyCategoryID . ')"><span title="" rel="tooltip" class="glyphicon glyphicon-link" data-original-title="Link"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="editsuppliercategory(' . $partyCategoryID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;</span>';
        } else {
            $status .= '<spsn class="pull-right"><a onclick="link_group_supplier_category(' . $partyCategoryID . ')"><span title="" rel="tooltip" class="glyphicon glyphicon-link" data-original-title="Link"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="editsuppliercategory(' . $partyCategoryID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_category(' . $partyCategoryID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';
        }


        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('party_group_category')) {
    function party_group_category($partyType, $status = TRUE)
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        //$companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $grpID = $companyID;
        $data = $CI->db->query("select partyCategoryID,partyType,categoryDescription from `srp_erp_grouppartycategories` where groupID=$grpID and partyType=$partyType ")->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Category');
        } else {
            $data_arr = '';
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['partyCategoryID'])] = trim($row['categoryDescription']);
            }
        }

        return $data_arr;
    }

}

if (!function_exists('master_coa_account_group')) {
    function master_coa_account_group()
    {
        $CI =& get_instance();
        $companyID = current_companyID();
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $grpID = $companyGroup{'companyGroupID'};
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory");
        $CI->db->FROM('srp_erp_groupchartofaccounts');
        $CI->db->WHERE('masterAccountYN', 1);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('groupID', $grpID);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Master Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('edit_otElement')) {
    function edit_otElement($id, $description, $usageCount)
    {
        $status = '<spsn class="pull-right"><a onclick="edit_element(' . $id . ', \'' . $description . '\')">';
        $status .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        if ($usageCount == 0) {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_ot_element(' . $id . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" ';
            $status .= 'style="color:rgb(209, 91, 71);"></span></a>';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('edit_overtimegroup')) {
    function edit_overtimegroup($id)
    {

        $status = '<spsn class="pull-right"><a onclick="edit_overtimegroup(' . $id . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_ot_group(' . $id . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';

        return $status;
    }
}
if (!function_exists('ot_systeminput')) {
    function ot_systeminput()
    {
        $CI =& get_instance();
        $data = $CI->db->query("select * from srp_erp_ot_systeminputs ")->result_array();

        return $data;
    }
}
if (!function_exists('load_OT_slab_action')) {
    function load_OT_slab_action($otSlabsMasterID)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'fetchPage("system/hrm/OverTimeManagementSalamAir/over_time_slab_new",' . $otSlabsMasterID . ',"Over Time Slab","OTSLAB"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        //$status .= '<a onclick="delete_item(' . $otSlabsMasterID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('set_deleted_class')) {
    function set_deleted_class($isDeleted)
    {
        $status = 'notdeleted';
        if ($isDeleted == 1) {
            $status = 'deleted';
        }

        return $status;
    }
}

if (!function_exists('ot_slabDrop_down')) {
    function ot_slabDrop_down($groupID)
    {
        $CI =& get_instance();
        $currency = $CI->db->query("select CurrencyID from srp_erp_ot_groups WHERE otGroupID=$groupID ;")->row_array();
        $currencyID = $currency['CurrencyID'];
        $companyID = current_companyID();
        $data = $CI->db->query("select otSlabsMasterID, Description from srp_erp_ot_slabsmaster WHERE companyID={$companyID} And transactionCurrencyID={$currencyID}")->result_array();

        return $data;
    }
}

if (!function_exists('edit_overTimeGroupDetail')) {
    function edit_overTimeGroupDetail($id, $systemInputID, $hourlyRate, $slabMasterID, $inputType)
    {

        $status = '<spsn class="pull-right"><a onclick="edit_overTimeGroupDetail(\'' . $id . '\', \'' . $systemInputID . '\', ';
        $status .= '\'' . $hourlyRate . '\', \'' . $slabMasterID . '\',\'' . $inputType . '\')">';
        $status .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        $status .= '<a onclick="delete_ot_group_detail(' . $id . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"';
        $status .= 'style="color:rgb(209, 91, 71);"></span></a></span>';

        return $status;
    }
}


if (!function_exists('load_OT_group_employee_action')) {
    function load_OT_group_employee_action($otGroupEmpID)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        //$status .= '<a onclick=\'fetchPage("system/hrm/OverTimeManagementSalamAir/over_time_slab_new",' . $otSlabsMasterID . ',"Over Time Slab","OTSLAB"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        $status .= '<a onclick="delete_item(' . $otGroupEmpID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('all_not_assigned_employee_to_OT_group')) {
    function all_not_assigned_employee_to_OT_group($otGroupID)
    {
        $CI =& get_instance();
        $customer_arr = array();

        $companyIDd = current_companyID();

        $CurrencyID = $CI->db->query("SELECT
	CurrencyID
FROM
	srp_erp_ot_groups

WHERE otGroupID = $otGroupID")->row_array();

        $Currency = $CurrencyID['CurrencyID'];

        $employees = $CI->db->query("SELECT
	EIdNo,Ename2
FROM
	srp_employeesdetails e
WHERE NOT EXISTS
        (
        SELECT  EmpID
        FROM    srp_erp_ot_groupemployees
        WHERE   srp_erp_ot_groupemployees.empID = e.EIdNo AND srp_erp_ot_groupemployees.companyID = $companyIDd

        ) AND e.isPayrollEmployee=1 AND e.empConfirmedYN=1 AND e.Erp_companyID = $companyIDd AND e.payCurrencyID=$Currency")->result_array();


        if ($employees) {
            foreach ($employees as $row) {
                $customer_arr[trim($row['EIdNo'])] = trim($row['Ename2']);
            }
        }

        return $customer_arr;
    }
}


if (!function_exists('getDesignationDrop')) {
    function getDesignationDrop($status = false)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->db->SELECT("DesignationID,DesDescription");
        $CI->db->FROM('srp_designation');
        $CI->db->WHERE('Erp_companyID', $companyID);
        $data = $CI->db->get()->result_array();
        $data_arr = [];

        if ($status == true) {
            $primaryLanguage = getPrimaryLanguage();
            $CI->lang->load('common', $primaryLanguage);
            $data_arr[''] = $CI->lang->line('common_select_a_option');
        }

        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['DesignationID'])] = trim($row['DesDescription']);
            }
        }

        return $data_arr;
    }
}

//genser drop down
if (!function_exists('load_gender_drop')) {
    function load_gender_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("genderID,name");
        $CI->db->FROM('srp_erp_gender');
        $cntry = $CI->db->get()->result_array();

        return $cntry;
    }
}

if (!function_exists('get_hrms_insuranceCategory')) {
    function get_hrms_insuranceCategory()
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $CI->db->SELECT("insurancecategoryID,description");
        $CI->db->FROM('srp_erp_family_insurancecategory');
        $CI->db->where('companyID', $companyID);
        $cntry = $CI->db->get()->result_array();

        return $cntry;
    }
}

if (!function_exists('hrms_relationship_drop')) {
    function hrms_relationship_drop()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("relationshipID,relationship");
        $CI->db->FROM('srp_erp_family_relationship');
        $cntry = $CI->db->get()->result_array();
        $location_arr = array('' => $CI->lang->line('common_select_relationship')/*'Select Relationship'*/);
        if (isset($cntry)) {
            foreach ($cntry as $row) {
                $location_arr[trim($row['relationshipID'])] = trim($row['relationship']);
            }
        }

        return $location_arr;
    }
}

if (!function_exists('format_date_other')) {
    function format_date_other($date = NULL)
    {
        if (isset($date)) {
            if (!empty($date)) {
                return date('dS M Y', strtotime($date));
            }
        } else {
            return date('dS M Y', time());
        }
    }
}

if (!function_exists('document_uploads_family_url')) {
    function document_uploads_family_url()
    {
        $url = base_url('images/family_images') . '/';

        return $url;
    }
}

if (!function_exists('get_hrms_relationship')) {
    function get_hrms_relationship()
    {
        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_family_relationship');

        return $CI->db->get()->result_array();
    }
}


if (!function_exists('supplier_company_link')) {
    function supplier_company_link($groupSupplierMasterID, $status = TRUE)/*Load all Customer*/
    {

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $companygroupID = $companyGroup['companyGroupID'];
        $customer = $CI->db->query("SELECT
	srp_erp_companygroupdetails.companyID,srp_erp_company.company_code,srp_erp_company.company_name
FROM
	srp_erp_companygroupdetails
	JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_companygroupdetails.companyID
WHERE companygroupID = ($companygroupID) AND NOT EXISTS
        (
        SELECT  companyID
        FROM    srp_erp_groupsupplierdetails
        WHERE   srp_erp_groupsupplierdetails.groupSupplierMasterID = $groupSupplierMasterID AND srp_erp_companygroupdetails.companyID = srp_erp_groupsupplierdetails.companyID
        )")->result_array();
        if ($status) {
            $customer_arr = array('' => 'Select Company');
        } else {
            $customer_arr = '';
        }
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['companyID'])] = trim($row['company_code']) . ' | ' . trim($row['company_name']);
            }
        }

        return $customer_arr;
    }
}


if (!function_exists('dropdown_companySuppliers')) {
    function dropdown_companySuppliers($companyID, $supplierMasterID = null)
    {
        $CI =& get_instance();
        $employees = array();

        if ($companyID != '') {

            $employees = $CI->db->query("SELECT
	supplierAutoID,supplierSystemCode,supplierName
FROM
	srp_erp_suppliermaster
WHERE companyID = ($companyID) AND NOT EXISTS
        (
        SELECT  SupplierMasterID
        FROM    srp_erp_groupsupplierdetails
        WHERE   srp_erp_suppliermaster.supplierAutoID = srp_erp_groupsupplierdetails.SupplierMasterID
        )")->result_array();
        }
        if ($supplierMasterID != '') {
            $sup = $CI->db->query("SELECT
	supplierAutoID,supplierSystemCode,supplierName
FROM
	srp_erp_suppliermaster
WHERE supplierAutoID = ($supplierMasterID)")->row_array();
        }
        $data_arr = array('' => 'Select Supplier');
        if (!empty($sup)) {
            $data_arr[trim($sup['supplierAutoID'])] = trim($sup['supplierSystemCode']) . ' | ' . trim($sup['supplierName']);
        }
        if ($employees) {
            foreach ($employees as $row) {
                $data_arr[trim($row['supplierAutoID'])] = trim($row['supplierSystemCode']) . ' | ' . trim($row['supplierName']);
            }
        }

        return $data_arr;
    }
}


if (!function_exists('load_all_nationality_drop')) {
    function load_all_nationality_drop()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("NId,Nationality");
        $CI->db->FROM('srp_nationality');
        $CI->db->where('Erp_companyID', current_companyID());
        $output = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_nationality')/*'Select Nationality'*/);
        if (isset($output)) {
            foreach ($output as $row) {
                $data_arr[trim($row['NId'])] = trim($row['Nationality']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('load_nationality_drop')) {
    function load_nationality_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("NId,Nationality");
        $CI->db->FROM('srp_nationality');
        $CI->db->where('Erp_companyID', current_companyID());
        $cntry = $CI->db->get()->result_array();

        return $cntry;
    }
}


if (!function_exists('getDocumentfamilyAttachment')) {
    function getDocumentfamilyAttachment($id)
    {
        $status = '';
        if ($id == 1) {
            $status = '<spsn class="">Passport</span>';
        } else if ($id == 2) {
            $status = '<spsn class="">Visa</span>';
        } else {
            $status = '<spsn class="">Insurance</span>';
        }

        return $status;
    }
}

if (!function_exists('editsupplier')) {
    function editsupplier($supplierAutoID)
    {
        // echo $to;
        $CI =& get_instance();
        $status = '<span class="pull-right">';
        $customerinvoice = $CI->db->query("select supplierID from srp_erp_paysupplierinvoicemaster WHERE supplierID=$supplierAutoID ;")->row_array();
        $customerreceipt = $CI->db->query("select partyID from srp_erp_paymentvouchermaster WHERE partyID=$supplierAutoID ;")->row_array();
        $purchaseorder = $CI->db->query("select supplierID from srp_erp_purchaseordermaster WHERE supplierID=$supplierAutoID ;")->row_array();
        $paymentmatching = $CI->db->query("select supplierID from srp_erp_pvadvancematch WHERE supplierID=$supplierAutoID ;")->row_array();
        $grv = $CI->db->query("select supplierID from srp_erp_grvmaster WHERE supplierID=$supplierAutoID ;")->row_array();
        $purchasereturn = $CI->db->query("select supplierID from srp_erp_stockreturnmaster WHERE supplierID=$supplierAutoID ;")->row_array();
        $creditnote = $CI->db->query("select supplierID from srp_erp_debitnotemaster WHERE supplierID=$supplierAutoID ;")->row_array();
        $generalledger = $CI->db->query("select partyautoID from srp_erp_generalledger WHERE partyautoID=$supplierAutoID AND partyType = 'SUP';")->row_array();
        if (!empty($customerinvoice || $customerreceipt || $creditnote || $generalledger || $purchaseorder || $paymentmatching || $grv || $purchasereturn)) {
            $status .= '<spsn class="pull-right"><a onclick="attachment_modal(' . $supplierAutoID . ',\'Supplier\',\'SUP\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="fetchPage(\'system/supplier/erp_supplier_master_new\',' . $supplierAutoID . ',\'Edit Supplier\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;</span>';
        } else {
            $status .= '<spsn class="pull-right"><a onclick="attachment_modal(' . $supplierAutoID . ',\'Supplier\',\'SUP\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="fetchPage(\'system/supplier/erp_supplier_master_new\',' . $supplierAutoID . ',\'Edit Supplier\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_supplier(' . $supplierAutoID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';
        }
        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('load_LA_approval_action')) {
    function load_LA_approval_action($leaveMasterID, $ECConfirmedYN, $approved, $createdUserID, $level, $isFromCancel = 0)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->lang->load('hrms_approvals', $primaryLanguage);
        $CI->load->library('session');
        $status = '<span class="pull-right">';

        $leavappliction = $CI->lang->line('hrms_payroll_leave_application');
        $status .= '<a onclick=\'attachment_modal(' . $leaveMasterID . ',"' . $leavappliction . '","LA");\'>';
        $status .= '<span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

        if ($approved == 0) {
            $status .= '<a target="_blank" onclick="load_emp_leaveDet(\'' . $leaveMasterID . '\',\'' . $approved . '\', ' . $level . ', ' . $isFromCancel . ')" >';
            $status .= '<span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<a target="_blank" onclick="load_emp_leaveDet(\'' . $leaveMasterID . '\',\'' . $approved . '\', ' . $level . ')" >';
            $status .= '<span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('chart_of_account_company_link')) {
    function chart_of_account_company_link($GLAutoID, $status = TRUE)/*Load all Customer*/
    {

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $companygroupID = $companyGroup['companyGroupID'];
        $customer = $CI->db->query("SELECT
	srp_erp_companygroupdetails.companyID,srp_erp_company.company_code,srp_erp_company.company_name
FROM
	srp_erp_companygroupdetails
	JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_companygroupdetails.companyID
WHERE companygroupID = ($companygroupID) AND NOT EXISTS
        (
        SELECT  companyID
        FROM    srp_erp_groupchartofaccountdetails
        WHERE   srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID = $GLAutoID AND srp_erp_companygroupdetails.companyID = srp_erp_groupchartofaccountdetails.companyID
        )")->result_array();
        if ($status) {
            $customer_arr = array('' => 'Select Company');
        } else {
            $customer_arr = '';
        }
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['companyID'])] = trim($row['company_code']) . ' | ' . trim($row['company_name']);
            }
        }

        return $customer_arr;
    }
}

if (!function_exists('dropdown_companychartofAccounts')) {
    function dropdown_companychartofAccounts($companyID, $chartofAccountID = null, $groupChartofAccountMasterID, $masterAccountYN)
    {
        $CI =& get_instance();
        $accounts = array();

        $master = $CI->db->query("SELECT
	*
FROM
	srp_erp_groupchartofaccounts
WHERE GLAutoID = ($groupChartofAccountMasterID) ")->row_array();
        $accountCategoryTypeID = $master['accountCategoryTypeID'];
        $isBank = $master['isBank'];
        $isCash = $master['isCash'];
        if ($isBank == 1) {
            $where = 'AND isCash=' . $isCash;
        } else {
            $where = '';
        }

        if ($companyID != '') {

            $accounts = $CI->db->query("SELECT
	 GLAutoID,systemAccountCode,GLDescription
FROM
	srp_erp_chartofaccounts
WHERE companyID = ($companyID) AND accountCategoryTypeID = $accountCategoryTypeID AND masterAccountYN = $masterAccountYN $where AND NOT EXISTS
        (
        SELECT  groupChartofAccountDetailID
        FROM    srp_erp_groupchartofaccountdetails
        WHERE   srp_erp_chartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.chartofAccountID
        )")->result_array();
        }

        if ($chartofAccountID != '') {
            $chart = $CI->db->query("SELECT
	GLAutoID,systemAccountCode,GLDescription
FROM
	srp_erp_chartofaccounts
WHERE GLAutoID = ($chartofAccountID)")->row_array();
        }
        $data_arr = array('' => 'Select Chart OF Accounts');

        if (!empty($chart)) {
            $data_arr[trim($chart['GLAutoID'])] = trim($chart['systemAccountCode']) . ' | ' . trim($chart['GLDescription']);
        }

        if ($accounts) {
            foreach ($accounts as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLDescription']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('segment_company_link')) {
    function segment_company_link($groupSegmentID, $status = TRUE)/*Load all Customer*/
    {

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $companygroupID = $companyGroup['companyGroupID'];
        $customer = $CI->db->query("SELECT
	srp_erp_companygroupdetails.companyID,srp_erp_company.company_code,srp_erp_company.company_name
FROM
	srp_erp_companygroupdetails
	JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_companygroupdetails.companyID
WHERE companygroupID = ($companygroupID) AND NOT EXISTS
        (
        SELECT  companyID
        FROM    srp_erp_groupsegmentdetails
        WHERE   srp_erp_groupsegmentdetails.groupSegmentID = $groupSegmentID AND srp_erp_companygroupdetails.companyID = srp_erp_groupsegmentdetails.companyID
        )")->result_array();
        if ($status) {
            $customer_arr = array('' => 'Select Company');
        } else {
            $customer_arr = '';
        }
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['companyID'])] = trim($row['company_code']) . ' | ' . trim($row['company_name']);
            }
        }

        return $customer_arr;
    }
}


if (!function_exists('dropdown_companysegments')) {
    function dropdown_companysegments($companyID, $segmentID = null)
    {
        $CI =& get_instance();
        $segment = array();


        if ($companyID != '') {

            $segment = $CI->db->query("SELECT
	 segmentID,companyID,segmentCode,description
FROM
	srp_erp_segment
WHERE companyID = ($companyID) AND NOT EXISTS
        (
        SELECT  groupSegmentDetailID
        FROM    srp_erp_groupsegmentdetails
        WHERE   srp_erp_segment.segmentID = srp_erp_groupsegmentdetails.segmentID
        )")->result_array();
        }

        if ($segmentID != '') {
            $cust = $CI->db->query("SELECT
	segmentID,companyID,segmentCode,description
FROM
	srp_erp_segment
WHERE segmentID = ($segmentID)")->row_array();
        }
        $data_arr = array('' => 'Select Segment');

        if (!empty($cust)) {
            $data_arr[trim($cust['segmentID'])] = trim($cust['segmentCode']) . ' | ' . trim($cust['description']);
        }

        if ($segment) {
            foreach ($segment as $row) {
                $data_arr[trim($row['segmentID'])] = trim($row['segmentCode']) . ' | ' . trim($row['description']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('load_boq_category_action')) { /*get po action list*/
    function load_boq_category_action($action, $CatDescrip)
    {
        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'addNewSubCategory("' . $action . '","' . $CatDescrip . '"); \'><span class="glyphicon glyphicon-pencil"></span></a>';
        $status .= '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        $status .= '<a onclick="deleteCategory(' . $action . ')" ><span style="color:#ff3f3a" class="glyphicon glyphicon-trash "></span></a>';
        return $status;
    }
}

if (!function_exists('nextSortOrder')) {
    function nextSortOrder()
    {

        $CI =& get_instance();
        $CI->db->select_max('sortOrder');
        $CI->db->from('srp_erp_boq_category');

        $data = $CI->db->get()->row_array();
        if (is_null($data['sortOrder'])) {
            return 0;
        } else {
            return $data['sortOrder'];
        }
    }
}

if (!function_exists('load_boq_sub_category_action')) { /*get po action list*/
    function load_boq_sub_category_action($action)
    {
        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'deletesubcategory("' . $action . '"); \'><span style="color:#ff3f3a" class="glyphicon glyphicon-trash "></span></a>';

        return $status;
    }
}

if (!function_exists('get_category')) {
    function get_category()/*Load all location*/
    {

        $CI =& get_instance();
        $CI->db->select("categoryID, categoryCode,categoryDescription");
        $CI->db->from('srp_erp_boq_category');

        $cateogry = $CI->db->get()->result_array();
        $cateogry_arr = array('' => 'Select a Category');
        if (isset($cateogry)) {
            foreach ($cateogry as $row) {
                $cateogry_arr[trim($row['categoryID'])] = trim($row['categoryDescription']);
            }
        }

        return $cateogry_arr;
    }
}


if (!function_exists('loadboqheaderaction')) { /*get po action list*/
    function loadboqheaderaction($action)
    {
        $status = '<span class="pull-right">';

        $status .= '<a onclick="fetchPage(\'system/pm/erp_boq_estimation_add_new\',' . $action . ',\'System Log\')" ><span class=" glyphicon glyphicon-pencil "></span></a>';

        $status .= '<span class="pull-right"> &nbsp;|&nbsp;';

        $status .= '<a onclick="deleteBoqHeader(' . $action . ')" ><span style="color:#ff3f3a" class="glyphicon glyphicon-trash "></span></a>';

        return $status;
    }
}

if (!function_exists('opensubcatgroup')) {
    function opensubcatgroup($itemCategoryID, $description)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick="link_group_itemcategory(' . $itemCategoryID . ')"><span title="Link" rel="tooltip" class="glyphicon glyphicon-link" ></span></a> &nbsp;|&nbsp; <a onclick=\'fetchPage("system/GroupItemCategory/sub_category_add_group","' . $itemCategoryID . '","' . $description . '","Sub Category",""); \'><button type="button" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-plus" style="color:green;"></span></button></a>';
        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('checkApproved')) {//get finance period using companyFinancePeriodID.
    function checkApproved($documentSystemCode, $documentID, $approvalLevelID)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->db->SELECT("documentApprovedID");
        $CI->db->FROM('srp_erp_documentapproved');
        $CI->db->where('companyID', $companyID);
        $CI->db->where('documentSystemCode', $documentSystemCode);
        $CI->db->where('documentID', $documentID);
        $CI->db->where('approvalLevelID', $approvalLevelID);
        $CI->db->where('approvedYN', 1);
        $data = $CI->db->get()->row_array();
        if (!empty($data)) {
            return true;
        } else {
            return false;
        }


    }

    if (!function_exists('loadprojectAction')) { /*get po action list*/
        function loadprojectAction($id)
        {
            $status = '<span class="pull-right"><a onclick="edit_project( ' . $id . ' )"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span>';
            $status .= '&nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="delete_project(' . $id . ')" >';
            $status .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';


            return $status;
        }
    }
    if (!function_exists('get_all_boq_project')) {
        function get_all_boq_project()
        {
            $CI =& get_instance();
            $CI->db->select("projectID, projectName");
            $CI->db->from('srp_erp_projects');
            $CI->db->where('companyID', current_companyID());

            $cateogry = $CI->db->get()->result_array();
            $cateogry_arr = array('' => 'Select a project');
            if (isset($cateogry)) {
                foreach ($cateogry as $row) {
                    $cateogry_arr[trim($row['projectID'])] = trim($row['projectName']);
                }
            }

            return $cateogry_arr;
        }

    }
}

if (!function_exists('itemcategory_company_link')) {
    function itemcategory_company_link($itemCategoryID, $status = TRUE)/*Load all item category company*/
    {

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $companygroupID = $companyGroup['companyGroupID'];

        $customer = $CI->db->query("SELECT
	srp_erp_companygroupdetails.companyID,srp_erp_company.company_code,srp_erp_company.company_name
FROM
	srp_erp_companygroupdetails
	JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_companygroupdetails.companyID
WHERE companygroupID = ($companygroupID) AND NOT EXISTS
        (
        SELECT  companyID
        FROM    srp_erp_groupitemcategorydetails
        WHERE   srp_erp_groupitemcategorydetails.groupItemCategoryID = $itemCategoryID AND srp_erp_companygroupdetails.companyID = srp_erp_groupitemcategorydetails.companyID
        )")->result_array();
        if ($status) {
            $customer_arr = array('' => 'Select Company');
        } else {
            $customer_arr = '';
        }
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['companyID'])] = trim($row['company_code']) . ' | ' . trim($row['company_name']);
            }
        }

        return $customer_arr;
    }
}


if (!function_exists('dropdown_company_group_item_categories')) {
    function dropdown_company_group_item_categories($companyID, $itemCategoryID = null)
    {
        $CI =& get_instance();
        $segment = array();


        if ($companyID != '') {
            $segment = $CI->db->query("SELECT
	 itemCategoryID,description,codePrefix
FROM
	srp_erp_itemcategory
WHERE companyID = ($companyID) AND masterID IS NULL
AND NOT EXISTS
        (
        SELECT  groupItemCategoryDetailID
        FROM    srp_erp_groupitemcategorydetails
        WHERE   srp_erp_itemcategory.itemCategoryID = srp_erp_groupitemcategorydetails.itemCategoryID
        )")->result_array();
        }

        if ($itemCategoryID != '') {
            $cust = $CI->db->query("SELECT
	itemCategoryID,description,codePrefix
FROM
	srp_erp_itemcategory
WHERE itemCategoryID = ($itemCategoryID)")->row_array();
        }
        $data_arr = array('' => 'Select Category');

        if (!empty($cust)) {
            $data_arr[trim($cust['itemCategoryID'])] = trim($cust['codePrefix']) . ' | ' . trim($cust['description']);
        }

        if ($segment) {
            foreach ($segment as $row) {
                $data_arr[trim($row['itemCategoryID'])] = trim($row['codePrefix']) . ' | ' . trim($row['description']);
            }
        }

        return $data_arr;
    }
}
if (!function_exists('get_all_mfq_industry')) {
    function get_all_mfq_industry()
    {
        $CI =& get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_mfq_industrytypes');

        $workflow = $CI->db->get()->result_array();
        $workflow_arr = array('' => 'Select an Industry');
        if (isset($workflow)) {
            foreach ($workflow as $row) {
                $workflow_arr[trim($row['industrytypeID'])] = trim($row['industryTypeDescription']);
            }
        }
        return $workflow_arr;
    }

}


if (!function_exists('get_all_system_workflow')) {
    function get_all_system_workflow()
    {
        $CI =& get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_mfq_systemworkflowcategory');

        $workflow = $CI->db->get()->result_array();
        $workflow_arr = array('' => 'Select a Work Flow');
        if (isset($workflow)) {
            foreach ($workflow as $row) {
                $workflow_arr[trim($row['workFlowID'])] = trim($row['workFlowDescription']);
            }
        }
        return $workflow_arr;
    }
}


if (!function_exists('get_all_workflow_template')) {
    function get_all_workflow_template()
    {
        $CI =& get_instance();
        $CI->db->select("*");
        $CI->db->join('srp_erp_mfq_systemworkflowcategory',
            'srp_erp_mfq_systemworkflowcategory.workflowID = srp_erp_mfq_workflowtemplate.workflowID');
        $CI->db->from('srp_erp_mfq_workflowtemplate');

        $workflow = $CI->db->get()->result_array();
        return $workflow;
    }
}


if (!function_exists('ot_tempalte_description')) { /*get po action list*/
    function ot_tempalte_description($defultDescription, $categoryDescription)
    {
        $status = '';
        if (!empty($defultDescription)) {
            $status = $defultDescription;
        } else {
            $status = $categoryDescription;
        }
        return $status;
    }
}

if (!function_exists('get_user_isChangePassword')) {
    function get_user_isChangePassword()
    {
        $CI =& get_instance();
        $CI->db->select('isChangePassword');
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('EIdNo', current_userID());
        $isChangePassword = $CI->db->get()->row('isChangePassword');
        if ($isChangePassword == 1) {
            return true;
        } else {
            return false;
        }
    }
}


if (!function_exists('all_ot_category_drop')) {
    function all_ot_category_drop($companyID)
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT
 description,
	id AS overtimeCategoryID,
	0 AS defaultcategoryID,
	1 AS inputType
FROM
	srp_erp_pay_overtimecategory
where companyid=$companyID and id not in (select overtimeCategoryID from srp_erp_generalottemplatedetails where companyID=$companyID)
UNION
select description, 0 AS overtimeCategoryID,
	defaultTypeID AS defaultcategoryID,
	2 AS inputType from srp_erp_generalotdefaulttypes where defaultTypeID not in (select defaultcategoryID from srp_erp_generalottemplatedetails where companyID=$companyID)
	")->result_array();
        $data_arr = '';
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['overtimeCategoryID']) . '|' . trim($row['defaultcategoryID'])] = trim($row['description']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('loadHeaderBoqlanning')) { /*get po action list*/
    function loadHeaderBoqlanning($action)
    {
        $status = '<span class="pull-right">';


        $status .= '<a onclick="fetchPage(\'system/pm/erp_boq_project_planning\',' . $action . ',\'System Log\')" ><span class=" glyphicon glyphicon-pencil "></span></a>';


        return $status;
    }

    if (!function_exists('all_mfq_customer_drop')) {
        function all_mfq_customer_drop($status = TRUE)/*Load all Customer*/
        {
            $CI =& get_instance();
            $CI->db->select("mfqCustomerAutoID,CustomerName,CustomerSystemCode,CustomerCountry,CompanyCode");
            $CI->db->from('srp_erp_mfq_customermaster');
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $customer = $CI->db->get()->result_array();
            if ($status) {
                $customer_arr = array('' => 'Select Customer');
            } else {
                $customer_arr = '';
            }
            if (isset($customer)) {
                foreach ($customer as $row) {
                    $customer_arr[trim($row['mfqCustomerAutoID'])] = (trim($row['CustomerSystemCode']) ? trim($row['CustomerSystemCode']) . ' | ' : '') . trim($row['CustomerName']) . (trim($row['CustomerCountry']) ? ' | ' . trim($row['CustomerCountry']) : '');
                }
            }

            return $customer_arr;
        }
    }
}


if (!function_exists('general_ot_action')) {
    function general_ot_action($generalOTMasterID, $confirmedYN, $approvedYN)
    {

        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';

        if ($confirmedYN != 1) {
            $status .= '<a onclick=\'fetchPage("system/OverTime/erp_genaral_ot_detail",' . $generalOTMasterID . ',"Over Time","ATS"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($confirmedYN == 1 && $approvedYN == 0 || $approvedYN == 2) {
            $status .= '<a onclick="referback_general_ot(' . $generalOTMasterID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="general_ot_view_model(' . $generalOTMasterID . ')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

        $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('OverTime/load_general_ot_print/') . '/' . $generalOTMasterID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';

        if ($confirmedYN == 0 || $confirmedYN == 3) {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_general_ot_template(' . $generalOTMasterID . ',\'Invoices\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('got_action_approval')) { /*get po action list*/
    function got_action_approval($generalOTMasterID, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $status = '<span class="pull-right">';
        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $generalOTMasterID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp; ';
        } else {
            $status .= '<a target="_blank" onclick="general_ot_view_model(' . $generalOTMasterID . ')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('load_group_uom_action')) { /*get po action list*/
    function load_group_uom_action($UnitID, $desc, $code)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick="link_uom(' . $UnitID . ')"><span title="Link" rel="tooltip" class="glyphicon glyphicon-link" ></span></a>&nbsp;|&nbsp;<a onclick=\'fetch_umo_detail_con(' . $UnitID . ',"' . $desc . '","' . $code . '");\'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('group_item_edit')) {
    function group_item_edit($itemAutoID, $isActive = 0, $isSubItemExist = NULL)
    {
        $status = '<span class="pull-right">';

        /*if (isset($isSubItemExist) && $isSubItemExist == 1) {
            $status .= '<a class="text-purple" onclick="subItemConfigList_modal(' . $itemAutoID . ');"><span title="Sub Items" rel="tooltip" class="fa fa-list"></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }*/


        $status .= '<a onclick="link_group_item_master(' . $itemAutoID . ')"><span title="Link" rel="tooltip" class="glyphicon glyphicon-link" ></span></a>&nbsp;|&nbsp;';

        if ($isActive) {
            $status .= '<a onclick="load_duplicate_item(' . $itemAutoID . ')"><span title="Replicate" rel="tooltip"class="glyphicon glyphicon-duplicate"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="fetchPage(\'system/GroupItemMaster/erp_group_item_new\',' . $itemAutoID . ',\'Edit Item\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<a onclick="load_duplicate_item(' . $itemAutoID . ')"><span title="Replicate" rel="tooltip"class="glyphicon glyphicon-duplicate"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="fetchPage(\'system/GroupItemMaster/erp_group_item_new\',' . $itemAutoID . ',\'Edit Item\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('all_group_main_category_drop')) {
    function all_group_main_category_drop()
    {

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        //$companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $CI->db->SELECT("itemCategoryID,description,codePrefix");
        $CI->db->FROM('srp_erp_groupitemcategory');
        $CI->db->WHERE('masterID', NULL);
        $CI->db->where('groupID', $companyID);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Main Category');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['itemCategoryID'])] = trim($row['codePrefix']) . ' | ' . trim($row['description']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_group_umo_new_drop')) {
    function all_group_umo_new_drop()
    {
        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        //$companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $CI->db->select('UnitID,UnitShortCode,UnitDes');
        $CI->db->from('srp_erp_group_unit_of_measure');
        $CI->db->WHERE('groupID', $companyID);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select UOM');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['UnitID'])] = trim($row['UnitShortCode']) . ' | ' . trim($row['UnitDes']);
            }
        }

        return $data_arr;
    }
}


if (!function_exists('item_company_link')) {
    function item_company_link($groupItemMasterID, $status = TRUE)/*Load all Customer*/
    {

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $companygroupID = $companyGroup['companyGroupID'];
        $customer = $CI->db->query("SELECT
	srp_erp_companygroupdetails.companyID,srp_erp_company.company_code,srp_erp_company.company_name
FROM
	srp_erp_companygroupdetails
	JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_companygroupdetails.companyID
WHERE companygroupID = ($companygroupID) AND NOT EXISTS
        (
        SELECT  companyID
        FROM    srp_erp_groupitemmasterdetails
        WHERE   srp_erp_groupitemmasterdetails.groupItemMasterID = $groupItemMasterID AND srp_erp_companygroupdetails.companyID = srp_erp_groupitemmasterdetails.companyID
        )")->result_array();
        if ($status) {
            $customer_arr = array('' => 'Select Company');
        } else {
            $customer_arr = '';
        }
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['companyID'])] = trim($row['company_code']) . ' | ' . trim($row['company_name']);
            }
        }

        return $customer_arr;
    }
}


if (!function_exists('dropdown_companyitems')) {
    function dropdown_companyitems($companyID, $ItemAutoID = null)
    {
        $CI =& get_instance();
        $segment = array();
        $data_arr = array();


        if ($companyID != '') {

            $segment = $CI->db->query("SELECT
	 itemAutoID,companyID,itemSystemCode,itemDescription
FROM
	srp_erp_itemmaster
WHERE companyID = ($companyID) AND NOT EXISTS
        (
        SELECT  groupItemDetailID
        FROM    srp_erp_groupitemmasterdetails
        WHERE   srp_erp_itemmaster.itemAutoID = srp_erp_groupitemmasterdetails.ItemAutoID
        )")->result_array();
        }

        if ($ItemAutoID != '') {
            $cust = $CI->db->query("SELECT
	itemAutoID,companyID,itemSystemCode,itemDescription
FROM
	srp_erp_itemmaster
WHERE itemAutoID = ($ItemAutoID)")->row_array();
        }
        $data_arr = array('' => 'Select Item');
        if (!empty($cust)) {
            $data_arr[trim($cust['itemAutoID'])] = trim($cust['itemSystemCode']) . ' | ' . trim($cust['itemDescription']);
        }
        if ($segment) {
            foreach ($segment as $row) {
                $data_arr[trim($row['itemAutoID'])] = trim($row['itemSystemCode']) . ' | ' . trim($row['itemDescription']);
            }
        }

        return $data_arr;
    }
}


if (!function_exists('fetch_report_type')) {
    function fetch_report_type()
    {
        $CI =& get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_reporttemplate');
        $CI->db->where('isCustomizable', 1);
        $template = $CI->db->get()->result_array();
        $template_arr = array('' => 'Select Type');
        if (isset($template)) {
            foreach ($template as $row) {
                $template_arr[trim($row['reportID'])] = trim($row['reportDescription']);
            }
        }
        return $template_arr;
    }
}


if (!function_exists('gl_description_template')) {
    function gl_description_template($companyReportTemplateID, $drop=true)
    {
        $CI =& get_instance();
        $CI->db->select("reportID");
        $CI->db->from('srp_erp_companyreporttemplate');
        $CI->db->where('companyReportTemplateID', $companyReportTemplateID);
        $type = $CI->db->get()->row_array();
        $masterCategory = '';
        if ($type['reportID'] == 5) {
            $masterCategory = 'PL';
        } else if ($type['reportID'] == 6) {
            $masterCategory = 'BS';
        }

        $CI->db->select("GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('masterCategory', $masterCategory);
        $CI->db->where('companyID', current_companyID());
        $template = $CI->db->get()->result_array();

        if($drop == false){
            return $template;
        }

        $template_arr = array('' => 'Select GL Description');
        if (isset($template)) {
            foreach ($template as $row) {
                $template_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode'].' | '.$row['GLSecondaryCode'].' | '.$row['GLDescription'].' | ');
            }
        }
        return $template_arr;
    }
}

if (!function_exists('project_is_exist')) {
    function project_is_exist()
    {
        $CI =& get_instance();
        $CI->db->SELECT("value");
        $CI->db->FROM('srp_erp_companypolicy');
        $CI->db->WHERE('companypolicymasterID', 9);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $row = $CI->db->get()->row_array();
        $data = 0;
        if (!empty($row)) {
            if ($row['value'] == 1) {
                $data = 1;
            } else {
                $data = 0;
            }
        }
        return $data;
    }
}

if (!function_exists('project_currency')) {
    function project_currency($projectID)
    {
        $CI =& get_instance();
        $CI->db->SELECT("projectCurrencyID");
        $CI->db->FROM('srp_erp_projects');
        $CI->db->WHERE('projectID', $projectID);
        $row = $CI->db->get()->row_array();
        if (!empty($row)) {
            return $row['projectCurrencyID'];
        }
    }
}

if (!function_exists('get_password_complexity')) {
    function get_password_complexity()
    {
        $CI =& get_instance();
        $CI->db->select("projectComplexcityID,minimumLength,isCapitalLettersMandatory,isSpecialCharactersMandatory");
        $CI->db->from('srp_erp_passwordcomplexcity');
        $CI->db->where('companyID', current_companyID());
        $template = $CI->db->get()->row_array();

        return $template;
    }
}

if (!function_exists('get_password_complexity_exist')) {
    function get_password_complexity_exist()
    {
        $CI =& get_instance();
        $CI->db->select("companypolicymasterID");
        $CI->db->from('srp_erp_companypolicymaster');
        $CI->db->where('code', 'PC');
        $masterid = $CI->db->get()->row_array();

        $CI->db->select("companyPolicyAutoID");
        $CI->db->from('srp_erp_companypolicy');
        $CI->db->where('companypolicymasterID', $masterid['companypolicymasterID']);
        $CI->db->where('value', 1);
        $template = $CI->db->get()->row_array();
        $value = 0;
        if (!empty($template)) {
            $value = 1;
        } else {
            $value = 0;
        }

        return $value;
    }
}

if (!function_exists('default_delivery_location_drop')) {
    function default_delivery_location_drop($status = TRUE)
    {
        $CI =& get_instance();
        $CI->db->select('wareHouseAutoID');
        $CI->db->from('srp_erp_warehousemaster');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isDefault', 1);
        $data = $CI->db->get()->row_array();
        $defaultval = $data['wareHouseAutoID'];
        return $defaultval;
    }
}


if (!function_exists('loadDefaultWarehousechkbx')) {
    function loadDefaultWarehousechkbx($wareHouseAutoID, $isDefault)
    {
        $status = '<span class="pull-right">';


        if ($isDefault == 1) {
            $status .= '<input onchange="setDefaultWarehouse(this,' . $wareHouseAutoID . ')" id="isDefault" type="checkbox"  value="1" name="isDefault" checked>';
        } else {
            $status .= '<input onchange="setDefaultWarehouse(this,' . $wareHouseAutoID . ')" id="isDefault" type="checkbox"  value="1" name="isDefault">';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('current_user_segemnt')) {
    function current_user_segemnt($id = FALSE)
    {
        $CI =& get_instance();
        $CI->db->select("segmentID");
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('EIdNo', current_userID());
        $data = $CI->db->get()->row_array();

        $CI =& get_instance();
        $CI->db->select("segmentCode");
        $CI->db->from('srp_erp_segment');
        $CI->db->where('segmentID', $data['segmentID']);
        $datas = $CI->db->get()->row_array();
        if ($id) {
            $result = $data['segmentID'];
        } else {
            $result = $data['segmentID'] . '|' . $datas['segmentCode'];
        }


        return $result;
    }
}

if (!function_exists('expenseIncomeGL_drop')) {
    function expenseIncomeGL_drop()
    {
        $CI =& get_instance();

        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('masterAccountYN', 0);
        //$CI->db->WHERE('controllAccountYN ', 0);
        $CI->db->WHERE('masterCategory', 'PL');
        $CI->db->WHERE('isActive', 1);
        $CI->db->WHERE('approvedYN', 1);
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->ORDER_BY('GLSecondaryCode');
        $data = $CI->db->get()->result_array();
        //echo $CI->db->last_query();
        /*$data_arr = array('' => 'Select GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }*/

        return $data;
    }
}


if (!function_exists('all_group_supplier_drop')) {
    function all_group_supplier_drop($status = TRUE)/*Load all group Supplier*/
    {
        $CI =& get_instance();
        $CI->db->select("groupSupplierAutoID,groupSupplierName,groupSupplierSystemCode,supplierCountry");
        $CI->db->from('srp_erp_groupsuppliermaster');
        $CI->db->join('srp_erp_groupsupplierdetails', 'srp_erp_groupsuppliermaster.groupSupplierAutoID = srp_erp_groupsupplierdetails.groupSupplierMasterID', 'INNER');
        $CI->db->where('srp_erp_groupsuppliermaster.companyGroupID', current_companyID());
        $supplier = $CI->db->get()->result_array();
        if ($status) {
            $supplier_arr = array('' => 'Select Supplier');
        } else {
            $supplier_arr = '';
        }
        if (isset($supplier)) {
            foreach ($supplier as $row) {
                $supplier_arr[trim($row['groupSupplierAutoID'])] = (trim($row['groupSupplierSystemCode']) ? trim($row['groupSupplierSystemCode']) . ' | ' : '') . trim($row['groupSupplierName']) . (trim($row['supplierCountry']) ? ' | ' . trim($row['supplierCountry']) : '');
            }
        }

        return $supplier_arr;
    }
}


if (!function_exists('warehouse_company_link')) {
    function warehouse_company_link($wareHouseAutoID, $status = TRUE)/*Load all Customer*/
    {

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $companygroupID = $companyGroup['companyGroupID'];
        $customer = $CI->db->query("SELECT
	srp_erp_companygroupdetails.companyID,srp_erp_company.company_code,srp_erp_company.company_name
FROM
	srp_erp_companygroupdetails
	JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_companygroupdetails.companyID
WHERE companygroupID = ($companygroupID) AND NOT EXISTS
        (
        SELECT  companyID
        FROM    srp_erp_groupwarehousedetails
        WHERE   srp_erp_groupwarehousedetails.groupWarehouseMasterID = $wareHouseAutoID AND srp_erp_companygroupdetails.companyID = srp_erp_groupwarehousedetails.companyID
        )")->result_array();
        if ($status) {
            $customer_arr = array('' => 'Select Company');
        } else {
            $customer_arr = '';
        }
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['companyID'])] = trim($row['company_code']) . ' | ' . trim($row['company_name']);
            }
        }

        return $customer_arr;
    }
}

if (!function_exists('dropdown_companywarehouses')) {
    function dropdown_companywarehouses($companyID, $warehosueMasterID = null)
    {
        $CI =& get_instance();
        $segment = array();
        $data_arr = array();


        if ($companyID != '') {

            $segment = $CI->db->query("SELECT
	 wareHouseAutoID,companyID,wareHouseCode,wareHouseDescription
FROM
	srp_erp_warehousemaster
WHERE companyID = ($companyID) AND NOT EXISTS
        (
        SELECT  groupWarehouseDetailID
        FROM    srp_erp_groupwarehousedetails
        WHERE   srp_erp_warehousemaster.wareHouseAutoID = srp_erp_groupwarehousedetails.warehosueMasterID
        )")->result_array();
        }

        if ($warehosueMasterID != '') {
            $cust = $CI->db->query("SELECT
	wareHouseAutoID,companyID,wareHouseCode,wareHouseDescription
FROM
	srp_erp_warehousemaster
WHERE wareHouseAutoID = ($warehosueMasterID)")->row_array();
        }
        $data_arr = array('' => 'Select Warehouse');
        if (!empty($cust)) {
            $data_arr[trim($cust['wareHouseAutoID'])] = trim($cust['wareHouseCode']) . ' | ' . trim($cust['wareHouseDescription']);
        }
        if ($segment) {
            foreach ($segment as $row) {
                $data_arr[trim($row['wareHouseAutoID'])] = trim($row['wareHouseCode']) . ' | ' . trim($row['wareHouseDescription']);
            }
        }

        return $data_arr;
    }
}


if (!function_exists('uom_company_link')) {
    function uom_company_link($groupUOMMasterID, $status = TRUE)/*Load all Customer*/
    {

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $companygroupID = $companyGroup['companyGroupID'];
        $customer = $CI->db->query("SELECT
	srp_erp_companygroupdetails.companyID,srp_erp_company.company_code,srp_erp_company.company_name
FROM
	srp_erp_companygroupdetails
	JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_companygroupdetails.companyID
WHERE companygroupID = ($companygroupID) AND NOT EXISTS
        (
        SELECT  companyID
        FROM    srp_erp_groupuomdetails
        WHERE   srp_erp_groupuomdetails.groupUOMMasterID = $groupUOMMasterID AND srp_erp_companygroupdetails.companyID = srp_erp_groupuomdetails.companyID
        )")->result_array();
        if ($status) {
            $customer_arr = array('' => 'Select Company');
        } else {
            $customer_arr = '';
        }
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['companyID'])] = trim($row['company_code']) . ' | ' . trim($row['company_name']);
            }
        }

        return $customer_arr;
    }
}

if (!function_exists('dropdown_companyuom')) {
    function dropdown_companyuom($companyID, $UOMMasterID = null)
    {
        $CI =& get_instance();
        $segment = array();
        $data_arr = array();


        if ($companyID != '') {

            $segment = $CI->db->query("SELECT
	 UnitID,companyID,UnitShortCode,UnitDes
FROM
	srp_erp_unit_of_measure
WHERE companyID = ($companyID) AND NOT EXISTS
        (
        SELECT  groupUOMDetailID
        FROM    srp_erp_groupuomdetails
        WHERE   srp_erp_unit_of_measure.UnitID = srp_erp_groupuomdetails.UOMMasterID
        )")->result_array();
        }

        if ($UOMMasterID != '') {
            $cust = $CI->db->query("SELECT
	UnitID,companyID,UnitShortCode,UnitDes
FROM
	srp_erp_unit_of_measure
WHERE UnitID = ($UOMMasterID)")->row_array();
        }
        $data_arr = array('' => 'Select UOM');

        if (!empty($cust)) {
            $data_arr[trim($cust['UnitID'])] = trim($cust['UnitShortCode']) . ' | ' . trim($cust['UnitDes']);
        }

        if ($segment) {
            foreach ($segment as $row) {
                $data_arr[trim($row['UnitID'])] = trim($row['UnitShortCode']) . ' | ' . trim($row['UnitDes']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('send_approvalEmail')) {
    function send_approvalEmail($mailData, $attachment = 0, $path = 0)
    {
        $CI =& get_instance();

        $CI->load->library('email_manual');

        $approvalEmpID = $mailData['approvalEmpID'];
        $documentCode = $mailData['documentCode'];
        $toEmail = $mailData['toEmail'];
        $subject = $mailData['subject'];
        $param = $mailData['param'];


        $config['mailtype'] = "html";
        $config['wordwrap'] = TRUE;
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'smtp.sendgrid.net';
        $config['smtp_user'] = 'azure_61fdfd424467a8cecb84bf014f8b5e26@azure.com';
        $config['smtp_pass'] = 'P@ssw0rd240!';
        $config['smtp_crypto'] = 'tls';
        $config['smtp_port'] = '587';
        $config['crlf'] = "\r\n";
        $config['newline'] = "\r\n";
        $CI->load->library('email', $config);
        if (array_key_exists("from", $mailData)) {
            $CI->email->from('noreply@spur-int.com', $mailData['from']);
        } else {
            $CI->email->from('noreply@spur-int.com', SYS_NAME);
        }

        if (!empty($param)) {
            $CI->email->to($toEmail);
            $CI->email->subject($subject);
            $CI->email->message($CI->load->view('system/email_template/email_approval_template_log', $param, TRUE));
            if ($attachment == 1) {
                $CI->email->attach($path);
            }
        }

        $result = $CI->email->send();
        $CI->email->clear(TRUE);
        send_push_notification($approvalEmpID, $subject, $documentCode, 1);
    }
}


if (!function_exists('send_push_notification')) {
    function send_push_notification($managerID, $description, $documentCode, $type)
    {
        $CI =& get_instance();
        //send mobile notification
        $CI->db->select('player_id');
        $CI->db->from('srp_devices');
        $CI->db->where('emp_id', $managerID);
        $devices = $CI->db->get()->result_array();
        $player_ids = array();
        foreach ($devices as $device_id) {
            $content = array(
                "en" => $documentCode,
                //"subtitle"=>'1212212'// notification message
            );
            $headings = array("en" => $description);
            $fields = array(
                'app_id' => "2ca0ecc7-6ecf-436d-b82a-bb898b822674",
                'include_player_ids' => array($device_id["player_id"]), // add player ids here
                'data' => array("type" => "approval"), // other contents eg:- id, name
                'contents' => $content,
                'headings' => $headings
            );
            $fields = json_encode($fields);
            /*print("\nJSON sent:\n");
            print($fields);*/

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                'Authorization: Basic Y2ZmMDE2N2ItMzNlOC00ZDZjLTllNDktMDI0M2QxNTliZTU1'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $response = curl_exec($ch);
            curl_close($ch);
        }
    }
}

if (!function_exists('get_passport_visa_details')) {
    function get_passport_visa_details()
    {
        $CI =& get_instance();
        $CI->db->select("EPassportExpiryDate,EVisaExpiryDate");
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('EIdNo', current_userID());
        $result = $CI->db->get()->row_array();

        return $result;
    }
}

if (!function_exists('addRecurringBtn')) {
    function addRecurringBtn($RJVMasterAutoId)
    {
        /*$CurrencyCode = "'".trim($CurrencyCode)."'";
        $code = "'".trim($code)."'";
        $empName = "'".trim($empName)."'";
        //$addTempTB = 'onclick="addTempTB('.$EIdNo.', '.$code.', '.$empName.', '.$CurrencyCode.', '.$DecimalPlaces.' )"';*/
        //$addTempTB = 'onclick="addTempTB(this)"';

        $view = '<div class="" align="center"> <button class="btn btn-primary btn-xs" onclick="addTempTB(' . $RJVMasterAutoId . ')" style="font-size:10px"> + Add </button> </div>';

        return $view;

    }
}

if (!function_exists('dropdown_all_revenue_gl_JV')) {
    function dropdown_all_revenue_gl_JV()
    {
        $CI =& get_instance();
        $companyID = current_companyID();

        $data = $CI->db->query("SELECT
    coa.GLAutoID,
    coa.systemAccountCode,
    coa.GLSecondaryCode,
    coa.GLDescription,
    coa.systemAccountCode,
    coa.subCategory
FROM
    `srp_erp_chartofaccounts` coa
WHERE
    coa.`masterAccountYN` = 0
AND coa.`approvedYN` = 1
AND coa.`isActive` = 1
AND coa.accountCategoryTypeID != 4
AND coa.`companyID` = '{$companyID}'
AND coa.controllAccountYN = 0
UNION
SELECT
    coa.GLAutoID,
    coa.systemAccountCode,
    coa.GLSecondaryCode,
    coa.GLDescription,
    coa.systemAccountCode,
    coa.subCategory
FROM
    `srp_erp_chartofaccounts` coa
WHERE
    coa.`masterAccountYN` = 0
AND coa.`approvedYN` = 1
AND coa.`isActive` = 1
AND coa.accountCategoryTypeID != 4
AND coa.`companyID` = '{$companyID}'
AND  GLAutoID in(SELECT
    GLAutoID
FROM
    srp_erp_companycontrolaccounts cmp
WHERE
    cmp.companyID = '{$companyID}'
AND (cmp.controlaccounttype = 'ADSP' or cmp.controlaccounttype='PCA' or cmp.controlaccounttype='TAX'))")->result_array();

        $data_arr = array('' => 'Select GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_main_category_report_drop')) {
    function all_main_category_report_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("itemCategoryID,description,codePrefix");
        $CI->db->FROM('srp_erp_itemcategory');
        $CI->db->WHERE('masterID', NULL);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('categoryTypeID', 1);
        $data = $CI->db->get()->result_array();
        $data_arr = array();
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['itemCategoryID'])] = trim($row['codePrefix']) . ' | ' . trim($row['description']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_main_category_group_report_drop')) {
    function all_main_category_group_report_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("itemCategoryID,description,codePrefix");
        $CI->db->FROM('srp_erp_groupitemcategory');
        $CI->db->WHERE('masterID', NULL);
        $CI->db->where('groupID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('categoryTypeID', 1);
        $data = $CI->db->get()->result_array();
        $data_arr = array();
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['itemCategoryID'])] = trim($row['codePrefix']) . ' | ' . trim($row['description']);
            }
        }

        return $data_arr;
    }
}


if (!function_exists('all_group_supplier_drop')) {
    function all_group_supplier_drop($status = TRUE)/*Load all Supplier*/
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->select("supplierAutoID,supplierName,supplierSystemCode,supplierCountry");
        $CI->db->from('srp_erp_groupsuppliermaster');
        $CI->db->where('groupID', $CI->common_data['company_data']['company_id']);
        $supplier = $CI->db->get()->result_array();
        if ($status) {
            $supplier_arr = array('' => $CI->lang->line('common_aelect_supplier')/*'Select Supplier'*/);
        } else {
            $supplier_arr = '';
        }
        if (isset($supplier)) {
            foreach ($supplier as $row) {
                $supplier_arr[trim($row['supplierAutoID'])] = (trim($row['supplierSystemCode']) ? trim($row['supplierSystemCode']) . ' | ' : '') . trim($row['supplierName']) . (trim($row['supplierCountry']) ? ' | ' . trim($row['supplierCountry']) : '');
            }
        }

        return $supplier_arr;
    }
}

if (!function_exists('stock_adjustment_control_drop')) {
    function stock_adjustment_control_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('masterCategory', "PL");
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select GL Code');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'])] = trim($row['systemAccountCode']) . ' | ' . trim($row['GLSecondaryCode']) . ' | ' . trim($row['GLDescription']) . ' | ' . trim($row['subCategory']);
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_company_assigned_attributes')) {
    function fetch_company_assigned_attributes()
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $CI->db->SELECT("srp_erp_companyattributeassign.*,srp_erp_systemattributemaster.attributeDescription as attributeDescription,srp_erp_systemattributemaster.attributeType as attributeType,srp_erp_systemattributemaster.columnName as columnName");
        $CI->db->FROM('srp_erp_companyattributeassign');
        $CI->db->join('srp_erp_systemattributemaster', 'srp_erp_companyattributeassign.systemAttributeID = srp_erp_systemattributemaster.systemAttributeID');
        $CI->db->WHERE('companyID', $companyID);
        $data = $CI->db->get()->result_array();
        return $data;
    }
}

if (!function_exists('isMandatory_completed_document')) {
    function isMandatory_completed_document($grvID, $code)
    {
        $CI =& get_instance();
        $result = $CI->db->query("SELECT
                                    *
                                FROM
                                    srp_erp_itemmaster_subtemp
                                WHERE
                                    receivedDocumentAutoID = '" . $grvID . "'
                                AND receivedDocumentID = '$code'")->result_array();
        if (empty($result)) {
            return 0;
        } else {
            $attributes = fetch_company_assigned_attributes();
            foreach ($attributes as $val) {
                if ($val['isMandatory'] == 1) {
                    foreach ($result as $value) {
                        if (empty($value[$val['columnName']])) {
                            return 1;
                        }
                    }
                }
            }
            return 0;
        }

    }
}

if (!function_exists('companyWarehouseBinLocations')) {
    function companyWarehouseBinLocations()
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $CI->db->SELECT("srp_erp_warehousemaster.*");
        $CI->db->FROM('srp_erp_warehousemaster');
        //$CI->db->join('srp_erp_warehousebinlocation','srp_erp_warehousemaster.wareHouseAutoID = srp_erp_warehousebinlocation.warehouseAutoID','left');
        $CI->db->WHERE('srp_erp_warehousemaster.companyID', $companyID);
        $data = $CI->db->get()->result_array();
        return $data;
    }
}

if (!function_exists('companyBinLocations')) {
    function companyBinLocations()
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $CI->db->SELECT("srp_erp_warehousebinlocation.*");
        $CI->db->FROM('srp_erp_warehousebinlocation');
        //$CI->db->join('srp_erp_warehousebinlocation','srp_erp_warehousemaster.wareHouseAutoID = srp_erp_warehousebinlocation.warehouseAutoID','left');
        $CI->db->WHERE('srp_erp_warehousebinlocation.companyID', $companyID);
        $data = $CI->db->get()->result_array();
        return $data;
    }
}

if (!function_exists('fetch_tax_type')) {
    function fetch_tax_type($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('taxMasterAutoID,taxDescription,taxShortCode,IF(taxType = 1,"Sales tax","Purchase tax") as taxType', false);
        $CI->db->from('srp_erp_taxmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($state == TRUE) {
            $data_arr = array('' => 'Select Tax Type');
        } else {
            $data_arr = '';
        }
        if (isset($data)) {
            foreach ($data as $row) {
                if ($id) {
                    $data_arr[trim($row['taxMasterAutoID'])] = trim($row['taxShortCode']) . ' | ' . trim($row['taxDescription']) . ' | ' . trim($row['taxType']);
                } else {
                    $data_arr[trim($row['taxMasterAutoID']) . '|' . trim($row['taxShortCode'])] = trim($row['taxShortCode']) . ' | ' . trim($row['taxDescription']) . ' | ' . trim($row['taxType']);
                }

            }
        }

        return $data_arr;
    }
}

if (!function_exists('emp_master_authenticate')) {
    function emp_master_authenticate()
    {
        /*****************************************************************
         * - Check company policy on 'Employee Master Edit Approval'
         * and current user has the authentication for make changes
         *****************************************************************/
        $CI =& get_instance();

        $isAuthenticate = getPolicyValues('EMA', 'All');

        if ($isAuthenticate == 1) {
            $userID = current_userID();
            $companyID = current_companyID();

            $result = $CI->db->query("SELECT employeeID FROM srp_erp_approvalusers
                            JOIN srp_erp_documentcodes ON srp_erp_approvalusers.documentID=srp_erp_documentcodes.documentID
                            WHERE companyID={$companyID}  AND srp_erp_approvalusers.documentID='EMP' AND employeeID={$userID}")->row('employeeID');

            $isAuthenticate = (!empty($result)) ? 0 : 1;
        }

        return $isAuthenticate;
    }
}

if (!function_exists('isPendingDataAvailable')) {
    function isPendingDataAvailable()
    {
        $CI =& get_instance();
        $companyID = current_companyID();

        $data = $CI->db->query("SELECT EIdNo, EmpSecondaryCode AS empShtrCode, Ename2 FROM srp_employeesdetails AS t1
                        JOIN (
                            SELECT empID FROM srp_erp_employeedatachanges WHERE companyID={$companyID} AND approvedYN=0
                            UNION
                            SELECT empID  FROM srp_erp_employeefamilydatachanges WHERE companyID={$companyID} AND approvedYN=0
                            UNION
                            SELECT empID FROM srp_erp_family_details WHERE approvedYN=0
                        ) AS pendingDataTB ON pendingDataTB.empID=t1.EIdNo
                        WHERE Erp_companyID={$companyID}")->result_array();

        return $data;
    }
}

if (!function_exists('all_discount_drop')) {
    function all_discount_drop($id = 1, $status = 1)
    {
        $CI =& get_instance();
        $CI->db->SELECT("discountExtraChargeID,Description,type,isChargeToExpense,isTaxApplicable,glCode");
        $CI->db->FROM('srp_erp_discountextracharges');
        $CI->db->where('type', $id);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Types');
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['discountExtraChargeID'])] = trim($row['Description']);
                }
            }
        } else {
            $data_arr = $data;
        }

        return $data_arr;
    }
}

if (!function_exists('inv_action_approval_buyback')) {
    function inv_action_approval_buyback($poID, $Level, $approved, $ApprovedID, $documentID, $approval = 1)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Invoice","HCINV");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';

        } else {
            //$status .= '<a onclick=\'documentPageView_modal("'.$documentID.'","' . $poID . '","","' . $approval . '"  ); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
            $status .= '<a onclick="documentPageView_modal(\'HCINV\',\'' . $poID . '\',"' . $approval . '")" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
        }
        //$status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';


        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('all_tax_drop_fin')) {
    function all_tax_drop_fin($id = 2, $status = 1)
    {
        $CI =& get_instance();
        $CI->db->SELECT("taxCalculationformulaID,Description");
        $CI->db->FROM('srp_erp_taxcalculationformulamaster');
        $CI->db->where('taxType', $id);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Tax Types');
            if (isset($data)) {
                foreach ($data as $row) {
                    $data_arr[trim($row['taxCalculationformulaID'])] = trim($row['Description']);
                }
            }
        } else {
            $data_arr = $data;
        }

        return $data_arr;
    }
}
if (!function_exists('print_template_pdf')) {
    function print_template_pdf($documentid, $defaultlink)
    {

        $CI =& get_instance();
        $CI->db->SELECT("TemplateMasterID");
        $CI->db->FROM('srp_erp_printtemplates');
        $CI->db->where('documentID', $documentid);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->row_array();
        if (!empty($data)) {
            $CI->db->SELECT("TempPageNameLink");
            $CI->db->FROM('srp_erp_printtemplatemaster');
            $CI->db->where('TemplateMasterID', $data['TemplateMasterID']);
            $TemplateMasterIDlink = $CI->db->get()->row_array();
            if (!empty($TemplateMasterIDlink)) {
                return $TemplateMasterIDlink['TempPageNameLink'];
            } else {
                return $defaultlink;
            }
        } else {
            return $defaultlink;
        }


    }
}
if (!function_exists('print_template_paper_size')) {
    function print_template_paper_size($documentid, $defaultpapersize)
    {

        $CI =& get_instance();
        $CI->db->SELECT('paperSize');
        $CI->db->FROM('srp_erp_printtemplates');
        $CI->db->where('documentID', $documentid);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $pagesize = $CI->db->get()->row_array();

        if (!empty($pagesize['paperSize'])) {

            return $pagesize['paperSize'];
        } else {
            return $defaultpapersize;
        }

    }
}

if (!function_exists('generate_encrypt_link')) {
    function generate_encrypt_link($full_path, $description = null, $extra = null)
    {
        $CI =& get_instance();
        return $CI->encryption_url->generate_encrypt_link($full_path, $description, $extra);
    }
}

if (!function_exists('generate_encrypt_link_start')) {
    function generate_encrypt_link_start($full_path, $extra = null)
    {
        $CI =& get_instance();
        return $CI->encryption_url->generate_encrypt_link_start($full_path, $extra);
    }
}

if (!function_exists('generate_encrypt_link_back')) {
    function generate_encrypt_link_back()
    {
        $CI =& get_instance();
        return $CI->encryption_url->generate_encrypt_link_back();
    }
}

if (!function_exists('generate_encrypt_link_only')) {
    function generate_encrypt_link_only($full_path)
    {
        $CI =& get_instance();
        return $CI->encryption_url->generate_encrypt_link_only($full_path);
    }
}

if (!function_exists('checkPostURL')) {
    function checkPostURL($URLList)
    {
        $CI =& get_instance();
        $controllerName = $CI->uri->segment(1);
        $functionName = $CI->uri->segment(2);
        $isGet = false;
        if (!empty($URLList)) {
            foreach ($URLList as $url) {
                if ($url == $controllerName . '/' . $functionName) {
                    $isGet = true;
                    break;
                }
            }
        }

        if ($isGet) {
            if (strtoupper($CI->input->method()) == 'GET') {
                header('HTTPS/1.0 403 Forbidden');
                header('HTTP/1.0 403 Forbidden');
                exit;
            }
        }
    }
}

if (!function_exists('get_companyInfo')) {
    function get_companyInfo()
    {
        $CI =& get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_company');
        $CI->db->where('company_id', current_companyID());
        $result = $CI->db->get()->row_array();
        return $result;

    }
}

if (!function_exists('get_companyControlAccounts')) {
    function get_companyControlAccounts($accountType)
    {
        $CI =& get_instance();
        $CI->db->select("CA.*");
        $CI->db->from('srp_erp_companycontrolaccounts CCA');
        $CI->db->join('srp_erp_chartofaccounts CA', 'CA.GLAutoID = CCA.GLAutoID');
        $CI->db->where('CCA.companyID', current_companyID());
        $CI->db->where('CCA.controlAccountType', $accountType);
        $result = $CI->db->get()->row_array();
        return $result;

    }
}

if (!function_exists('get_companyInformation')) {
    function get_companyInformation($companyID)
    {
        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_company');
        $CI->db->where('company_id', $companyID);
        $companyInfo = $CI->db->get()->row_array();
        return $companyInfo;
    }
}

if (!function_exists('array_filter_reports')) {
    function array_filter_reports($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('segmentCode,description,segmentID');
        $CI->db->from('srp_erp_segment');
        //$CI->db->where('status', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($state == TRUE) {
            $data_arr = array('' => $CI->lang->line('common_select_segment')/*'Select Segment'*/);
        } else {
            $data_arr = '';
        }
        if (isset($data)) {
            foreach ($data as $row) {
                if ($id) {
                    $data_arr[trim($row['segmentID'])] = trim($row['segmentCode']) . ' | ' . trim($row['description']);
                } else {
                    $data_arr[trim($row['segmentID']) . '|' . trim($row['segmentCode'])] = trim($row['segmentCode']) . ' | ' . trim($row['description']);
                }

            }
        }

        return $data_arr;
    }
}

if (!function_exists('get_companyData')) {
    function get_companyData($id)
    {
        $CI =& get_instance();
        $CI->db->select('company_code,company_name');
        $CI->db->from('srp_erp_company');
        $CI->db->where('company_id', $id);
        $data = $CI->db->get()->row_array();
        //$data = $CI->db->get()->row('company_name');


        return $data;
    }
}

if (!function_exists('fetch_segment_reports')) {
    function fetch_segment_reports($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('segmentCode,description,segmentID');
        $CI->db->from('srp_erp_segment');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($state == TRUE) {
            $data_arr = array('' => $CI->lang->line('common_select_segment')/*'Select Segment'*/);
        } else {
            $data_arr = '';
        }
        if (isset($data)) {
            foreach ($data as $row) {
                if ($id) {
                    $data_arr[trim($row['segmentID'])] = trim($row['segmentCode']) . ' | ' . trim($row['description']);
                } else {
                    $data_arr[trim($row['segmentID']) . '|' . trim($row['segmentCode'])] = trim($row['segmentCode']) . ' | ' . trim($row['description']);
                }

            }
        }

        return $data_arr;
    }
}

if (!function_exists('group_company_drop')) {
    function group_company_drop()
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $CI->lang->load('common', getPrimaryLanguage());

        $data = $CI->db->query("SELECT company_id, CONCAT(company_code, ' - ', company_name) AS cName
                            FROM srp_erp_company compTB
                            JOIN (
                                SELECT companyID FROM srp_erp_companygroupdetails
                                WHERE companyGroupID = ( SELECT companyGroupID FROM srp_erp_companygroupdetails
                                WHERE companyID={$companyID})
                            ) AS grpDet ON grpDet.companyID = compTB.company_id ORDER BY company_name")->result_array();

        return $data;
    }
}
if (!function_exists('all_employee_drop_with_non_payroll')) {
    function all_employee_drop_with_non_payroll($status = TRUE, $isDischarged = 0)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("EIdNo,ECode,Ename1,Ename2,Ename3,Ename4");
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('Erp_companyID', current_companyID());
        if ($isDischarged == 1) {
            $CI->db->where('isDischarged !=1 ');
        }
        $customer = $CI->db->get()->result_array();
        if ($status == TRUE) {
            $customer_arr = array('' => $CI->lang->line('common_select_employee'));/*'Select Employee'*/
            if (isset($customer)) {
                foreach ($customer as $row) {
                    $customer_arr[trim($row['EIdNo'])] = trim($row['ECode']) . ' | ' . trim($row['Ename2']);
                }
            }
        } else {
            $customer_arr = $customer;
        }

        return $customer_arr;
    }
}
if (!function_exists('drill_down_emp_language')) {
    function drill_down_emp_language()
    {
        $CI =& get_instance();
        $data = $CI->db->query("SELECT *,CASE WHEN description = \"Arabic\" THEN
	\"ع\" 
	WHEN description = \"English\" THEN
	\"ENG\" 
	END `languageshortcode` 
FROM
srp_erp_lang_languages ORDER BY
languageID 
DESC")->result_array();
        return $data;


    }
}


if (!function_exists('getallsubGroupCompanies')) {
    function getallsubGroupCompanies($commaSeperated = false)
    {
        $companyID = current_companyID();
        $CI =& get_instance();
        $group = $CI->db->query("SELECT companyGroupID,description,groupCode,reportingTo FROM srp_erp_companygroupmaster ORDER BY reportingTo ASC")->result_array();
        $companies = $CI->db->query("SELECT
	companyGroupID,company_code,company_name,srp_erp_companygroupdetails.companyID,typeID,companyGroupDetailID,description
FROM
	srp_erp_companygroupdetails
	LEFT JOIN srp_erp_groupstructuretype ON typeID=groupStructureTypeID
	INNER JOIN srp_erp_company ON srp_erp_companygroupdetails.companyID = srp_erp_company.company_id")->result_array();

        $data = [];
        $tree = getsubgroupcompanyArray($group, $companyID, $companies, $data);
        if (!empty($tree)) {
            if ($commaSeperated) {
                $tree = implode(',', $tree);
            }
        }


        return $tree;


    }
}

if (!function_exists('getsubgroupcompanyArray')) {
    function getsubgroupcompanyArray(array $elements, $parentId = 0, array $companies, array $data)
    {
        $branch = array();
        $companyID = current_companyID();
        if($parentId==$companyID){
            $keys = array_keys(array_column($companies, 'companyGroupID'), $parentId);
            $company = array_map(function ($k) use ($companies) {
                return $companies[$k];
            }, $keys);

            if (!empty($company)) {
                foreach ($company as $c) {
                    if ($c['companyID'] != '') {
                        array_push($data, $c['companyID']);
                    }
                }
            }
        }
        if (!empty($elements)) {
            foreach ($elements as $element) {

                if ($element['reportingTo'] == $parentId) {

                    $keys = array_keys(array_column($companies, 'companyGroupID'), $element['companyGroupID']);
                    $company = array_map(function ($k) use ($companies) {
                        return $companies[$k];
                    }, $keys);

                    if (!empty($company)) {
                        foreach ($company as $c) {
                            if ($c['companyID'] != '') {
                                array_push($data, $c['companyID']);
                            }
                        }
                    }

                    $children = getsubgroupcompanyArray($elements, $element['companyGroupID'], $companies, $data);
                }
            }
        }
        return $data;
    }
}


if (!function_exists('getParentgroupMasterID')) {
    function getParentgroupMasterID(){
        $CI =& get_instance();
        $companyID = current_companyID();
        $CI->db->select('masterID');
        $CI->db->from('srp_erp_companygroupmaster');
        $CI->db->where('companyGroupID',$companyID);
        $data = $CI->db->get()->row_array();
        return $data['masterID'];

    }
}