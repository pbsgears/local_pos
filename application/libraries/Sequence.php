<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sequence {

  function Sequence() {
    $CI = &get_instance();
  }

  function sequence_generator_fin($documentID, $companyFinanceYearID = NULL, $documentYear = NULL, $documentMonth = NULL, $count = 0) {

    $CI           = &get_instance();
    $companyID    = $CI->common_data['company_data']['company_id'];
    $company_code = $CI->common_data['company_data']['company_code'];

    //$policy    = getPolicyValues('DC', 'All');
    //$isFinance = $CI->db->query("select * from `srp_erp_documentcodes` where isFinance=1 AND documentID='{$documentID}'")->row_array();
    $isFinanceYN = $CI->db->query("select isFYBasedSerialNo from `srp_erp_documentcodemaster` where documentID='{$documentID}' AND companyID = $companyID ")->row_array();

    if ($isFinanceYN['isFYBasedSerialNo']==1) {
      if ($companyFinanceYearID == NULL) {
        var_dump('document Year  not found');
        exit;
      }
      if ($documentYear == NULL) {
        var_dump('document Year  not found');
        exit;
      }
      if ($documentMonth == NULL) {
        var_dump('document Month  not found');
        exit;
      }

      /*if ($isFinance['documentTable'] == '') {
        var_dump('document table  not found');
        exit;
      }*/



      //$table        = $isFinance['documentTable'];
      //$sqlSerialNo  = $CI->db->query("SELECT IF( isnull(MAX(serialNo)), 1 , ( MAX(serialNo) + 1) ) as serialNo FROM {$table} WHERE companyFinanceYearID={$companyFinanceYearID}  ")->row_array();
      $sqlSerialNo  = $CI->db->query("select serialNo from `srp_erp_financeyeardocumentcodemaster` where documentID='{$documentID}' AND companyID = $companyID AND financeYearID = $companyFinanceYearID  ")->row_array();
      $serialNo     = $sqlSerialNo['serialNo']+1;
      $CI           = &get_instance();
      $code         = '';
      $company_id   = $CI->common_data['company_data']['company_id'];
      $company_code = $CI->common_data['company_data']['company_code'];
      $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
      $CI->db->from('srp_erp_financeyeardocumentcodemaster');
      $CI->db->where('documentID', $documentID);
      $CI->db->where('companyID', $company_id);
      $CI->db->where('financeYearID', $companyFinanceYearID);
      $data = $CI->db->get()->row_array();
      if (empty($data)) {
        $data_arr = [
          'documentID'       => $documentID,
          'prefix'           => $documentID,
          'companyID'        => $company_id,
          'financeYearID'    => $companyFinanceYearID,
          'companyCode'      => $CI->common_data['company_data']['company_code'],
          'createdUserGroup' => $CI->common_data['user_group'],
          'createdUserID'    => $CI->common_data['current_userID'],
          'createdUserName'  => $CI->common_data['current_user'],
          'createdPCID'      => $CI->common_data['current_pc'],
          'createdDateTime'  => $CI->common_data['current_date'],
          'modifiedUserID'   => $CI->common_data['current_userID'],
          'modifiedUserName' => $CI->common_data['current_user'],
          'modifiedPCID'     => $CI->common_data['current_pc'],
          'modifiedDateTime' => $CI->common_data['current_date'],
          'startSerialNo'    => 1,
          'formatLength'     => 6,
          'format_1'         => 'prefix',
          'format_2'         => '/',
          'format_3'         => 'YYYY',
          'format_4'         => NULL,
          'format_5'         => NULL,
          'format_6'         => NULL,
          'serialNo'         => 1,
        ];

        $CI->db->insert('srp_erp_financeyeardocumentcodemaster', $data_arr);
        $data = $data_arr;
      }
      else {
        $CI->db->query("UPDATE srp_erp_financeyeardocumentcodemaster SET serialNo = {$serialNo}  WHERE documentID='{$documentID}' AND companyID = '{$company_id}' AND financeYearID = '{$companyFinanceYearID}'");
        $data['serialNo'] = $serialNo;
      }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= $documentYear;
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= $documentMonth;
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= $documentYear;
            }
            if ($data['format_3'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= $documentMonth;
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= $documentYear;
            }
            if ($data['format_5'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        }
        else {
            $number = $count;
        }
      return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));

    }
    else {
      $CI           = &get_instance();
      $code         = '';
      $company_id   = $CI->common_data['company_data']['company_id'];
      $company_code = $CI->common_data['company_data']['company_code'];
      $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
      $CI->db->from('srp_erp_documentcodemaster');
      $CI->db->where('documentID', $documentID);
      $CI->db->where('companyID', $company_id);
      $data = $CI->db->get()->row_array();
      if (empty($data)) {
        $data_arr = [
          'documentID'       => $documentID,
          'prefix'           => $documentID,
          'companyID'        => $company_id,
          'companyCode'      => $CI->common_data['company_data']['company_code'],
          'createdUserGroup' => $CI->common_data['user_group'],
          'createdUserID'    => $CI->common_data['current_userID'],
          'createdUserName'  => $CI->common_data['current_user'],
          'createdPCID'      => $CI->common_data['current_pc'],
          'createdDateTime'  => $CI->common_data['current_date'],
          'modifiedUserID'   => $CI->common_data['current_userID'],
          'modifiedUserName' => $CI->common_data['current_user'],
          'modifiedPCID'     => $CI->common_data['current_pc'],
          'modifiedDateTime' => $CI->common_data['current_date'],
          'startSerialNo'    => 1,
          'formatLength'     => 6,
          'format_1'         => 'prefix',
          'format_2'         => NULL,
          'format_3'         => NULL,
          'format_4'         => NULL,
          'format_5'         => NULL,
          'format_6'         => NULL,
          'serialNo'         => 1,
        ];

        $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
        $data = $data_arr;
      }
      else {
        $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
        $data['serialNo'] = ($data['serialNo'] + 1);
      }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= $documentYear;
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= $documentMonth;
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= $documentYear;
            }
            if ($data['format_3'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= $documentMonth;
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= $documentYear;
            }
            if ($data['format_5'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        }
        else {
            $number = $count;
        }
      return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));


    }
  }

  function sequence_generator($documentID, $count = 0) {
    $CI           = &get_instance();
    $code         = '';
    $company_id   = $CI->common_data['company_data']['company_id'];
    $company_code = $CI->common_data['company_data']['company_code'];
    $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
    $CI->db->from('srp_erp_documentcodemaster');
    $CI->db->where('documentID', $documentID);
    $CI->db->where('companyID', $company_id);
    $data = $CI->db->get()->row_array();
    if (empty($data)) {
      $data_arr = [
        'documentID'       => $documentID,
        'prefix'           => $documentID,
        'companyID'        => $company_id,
        'companyCode'      => $CI->common_data['company_data']['company_code'],
        'createdUserGroup' => $CI->common_data['user_group'],
        'createdUserID'    => $CI->common_data['current_userID'],
        'createdUserName'  => $CI->common_data['current_user'],
        'createdPCID'      => $CI->common_data['current_pc'],
        'createdDateTime'  => $CI->common_data['current_date'],
        'modifiedUserID'   => $CI->common_data['current_userID'],
        'modifiedUserName' => $CI->common_data['current_user'],
        'modifiedPCID'     => $CI->common_data['current_pc'],
        'modifiedDateTime' => $CI->common_data['current_date'],
        'startSerialNo'    => 1,
        'formatLength'     => 6,
        'format_1'         => 'prefix',
        'format_2'         => NULL,
        'format_3'         => NULL,
        'format_4'         => NULL,
        'format_5'         => NULL,
        'format_6'         => NULL,
        'serialNo'         => 1,
      ];

      $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
      $data = $data_arr;
    }
    else {
      $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
      $data['serialNo'] = ($data['serialNo'] + 1);
    }
    if ($data['format_1']) {
      if ($data['format_1'] == 'prefix') {
        $code .= $data['prefix'];
      }
      if ($data['format_1'] == 'yyyy') {
        $code .= date('Y');
      }
    }
    if ($data['format_2']) {
      $code .= $data['format_2'];
    }
    if ($data['format_3']) {
      if ($data['format_3'] == 'mm') {
        $code .= date('m');
      }
      if ($data['format_3'] == 'yyyy') {
        $code .= date('Y');
      }
        if ($data['format_3'] == 'prefix') {
            $code .= $data['prefix'];
        }
    }
    if ($data['format_4']) {
      $code .= $data['format_4'];
    }
    if ($data['format_5']) {
      if ($data['format_5'] == 'mm') {
        $code .= date('m');
      }
      if ($data['format_5'] == 'yyyy') {
        $code .= date('Y');
      }
        if ($data['format_5'] == 'prefix') {
            $code .= $data['prefix'];
        }
    }
    if ($data['format_6']) {
      $code .= $data['format_6'];
    }
    if ($count == 0) {
      $number = $data['serialNo'];
    }
    else {
      $number = $count;
    }
    return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
  }

    function mfq_sequence_generator($documentID, $count = 0,$segmentID=null) {
        $CI           = &get_instance();
        $code         = '';
        $company_id   = $CI->common_data['company_data']['company_id'];
        $company_code = $CI->common_data['company_data']['company_code'];
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $company_id);
        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = [
                'documentID'       => $documentID,
                'prefix'           => $documentID,
                'companyID'        => $company_id,
                'companyCode'      => $CI->common_data['company_data']['company_code'],
                'createdUserGroup' => $CI->common_data['user_group'],
                'createdUserID'    => $CI->common_data['current_userID'],
                'createdUserName'  => $CI->common_data['current_user'],
                'createdPCID'      => $CI->common_data['current_pc'],
                'createdDateTime'  => $CI->common_data['current_date'],
                'modifiedUserID'   => $CI->common_data['current_userID'],
                'modifiedUserName' => $CI->common_data['current_user'],
                'modifiedPCID'     => $CI->common_data['current_pc'],
                'modifiedDateTime' => $CI->common_data['current_date'],
                'startSerialNo'    => 1,
                'formatLength'     => 6,
                'format_1'         => 'prefix',
                'format_2'         => NULL,
                'format_3'         => NULL,
                'format_4'         => NULL,
                'format_5'         => NULL,
                'format_6'         => NULL,
                'serialNo'         => 1,
            ];

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        }
        else {
            $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
            $data['serialNo'] = ($data['serialNo'] + 1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        }
        else {
            $number = $count;
        }

        if($segmentID){
            $segmentID = $segmentID.'/';
        }
        return ($company_code . '/' .$segmentID. $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }

    function sequence_generator_byback($documentID, $count = 0,$compid=0,$companyDe=0) {
        $CI           = &get_instance();
        $code         = '';
        if($compid==0){
            $company_id   = $CI->common_data['company_data']['company_id'];
            $company_code = $CI->common_data['company_data']['company_code'];
        }else{
            $company_id   = $compid;
            $company_code = $companyDe;
        }
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $company_id);
        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = [
                'documentID'       => $documentID,
                'prefix'           => $documentID,
                'companyID'        => $company_id,
                'companyCode'      => $CI->common_data['company_data']['company_code'],
                'createdUserGroup' => $CI->common_data['user_group'],
                'createdUserID'    => $CI->common_data['current_userID'],
                'createdUserName'  => $CI->common_data['current_user'],
                'createdPCID'      => $CI->common_data['current_pc'],
                'createdDateTime'  => $CI->common_data['current_date'],
                'modifiedUserID'   => $CI->common_data['current_userID'],
                'modifiedUserName' => $CI->common_data['current_user'],
                'modifiedPCID'     => $CI->common_data['current_pc'],
                'modifiedDateTime' => $CI->common_data['current_date'],
                'startSerialNo'    => 1,
                'formatLength'     => 6,
                'format_1'         => 'prefix',
                'format_2'         => NULL,
                'format_3'         => NULL,
                'format_4'         => NULL,
                'format_5'         => NULL,
                'format_6'         => NULL,
                'serialNo'         => 1,
            ];

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        }
        else {
            $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
            $data['serialNo'] = ($data['serialNo'] + 1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_3'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_5'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        }
        else {
            $number = $count;
        }
        return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }


    function sequence_generator_group($documentID, $count = 0,$companyid,$compcode) {
        $CI           = &get_instance();
        $code         = '';
        $company_id   = $companyid;
        $company_code = $compcode;
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $company_id);
        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = [
                'documentID'       => $documentID,
                'prefix'           => $documentID,
                'companyID'        => $company_id,
                'companyCode'      => $CI->common_data['company_data']['company_code'],
                'createdUserGroup' => $CI->common_data['user_group'],
                'createdUserID'    => $CI->common_data['current_userID'],
                'createdUserName'  => $CI->common_data['current_user'],
                'createdPCID'      => $CI->common_data['current_pc'],
                'createdDateTime'  => $CI->common_data['current_date'],
                'modifiedUserID'   => $CI->common_data['current_userID'],
                'modifiedUserName' => $CI->common_data['current_user'],
                'modifiedPCID'     => $CI->common_data['current_pc'],
                'modifiedDateTime' => $CI->common_data['current_date'],
                'startSerialNo'    => 1,
                'formatLength'     => 6,
                'format_1'         => 'prefix',
                'format_2'         => NULL,
                'format_3'         => NULL,
                'format_4'         => NULL,
                'format_5'         => NULL,
                'format_6'         => NULL,
                'serialNo'         => 1,
            ];

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        }
        else {
            $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
            $data['serialNo'] = ($data['serialNo'] + 1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_3'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_5'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        }
        else {
            $number = $count;
        }
        return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }

}