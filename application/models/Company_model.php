<?php

class Company_model extends ERP_Model
{
    function save_company_master()
    {
        $company_link_id = trim($this->session->userdata("company_link_id"));
        $branch_link_id = trim($this->session->userdata("branchID"));
        $industry = explode(' | ', $this->input->post('industry'));
        $this->db->trans_start();
        $this->db->select('company_link_id');
        $this->db->from('srp_erp_company');
        $this->db->where('branch_link_id', $branch_link_id);
        $this->db->where('company_link_id', $company_link_id);
        $erp_company = $this->db->get()->row_array();

        $data['company_code'] = trim($this->input->post('companycode'));
        $data['company_name'] = trim($this->input->post('companyname'));
        $data['company_start_date'] = trim($this->input->post('companystartdate'));
        $data['company_url'] = trim($this->input->post('companyurl'));
        $data['legalName'] = trim_desc($this->input->post('legalname'));
        $data['textIdentificationNo'] = trim($this->input->post('txtidntificationno'));
        $data['textYear'] = trim($this->input->post('textyear'));
        if ($this->input->post('industry')) {
            $data['industryID'] =   $industry[0];
            $data['industry'] =     $industry[1];
        }
        $data['company_email'] = trim($this->input->post('companyemail'));
        $data['company_phone'] = trim($this->input->post('companyphone'));
        $data['company_address1'] = trim($this->input->post('companyaddress1'));
        $data['company_address2'] = trim($this->input->post('companyaddress2'));
        $data['company_city'] = trim($this->input->post('companycity'));
        $data['company_province'] = trim($this->input->post('companyprovince'));
        $data['company_postalcode'] = trim($this->input->post('companypostalcode'));
        $data['company_country'] = trim($this->input->post('companycountry'));
        $data['default_segment'] = trim($this->input->post('default_segment'));
        $data['modifiedPCID'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $data['modifiedUserID'] = trim($this->session->userdata("empID"));
        $data['modifiedUserName'] = trim($this->session->userdata("username"));
        $data['modifiedDateTime'] = date('Y-m-d h:i:s');
        if (trim($this->input->post('companyid'))) {
            $this->db->where('company_id', trim($this->input->post('companyid')));
            $this->db->update('srp_erp_company', $data);
            //$this->cache->clean();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Company : ' . $data['company_name'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Company : ' . $data['company_name'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('companyid'));
            }
        } else {
            $data['company_logo'] = $data['company_code'] . '.png';
            $data['company_default_currency'] = trim($this->input->post('company_default_currency'));
            $data['company_default_decimal'] = fetch_currency_desimal($this->input->post('company_default_currency'));
            $data['company_reporting_currency'] = trim($this->input->post('company_reporting_currency'));
            $data['company_reporting_decimal'] = fetch_currency_desimal($this->input->post('company_reporting_currency'));
            $data['company_link_id'] = $company_link_id;
            $data['branch_link_id'] = $branch_link_id;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $data['createdUserID'] = trim($this->session->userdata("empID"));
            $data['createdUserName'] = trim($this->session->userdata("username"));
            $data['createdDateTime'] = date('Y-m-d h:i:s');
            $this->db->insert('srp_erp_company', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Company : ' . $data['company_name'] . '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Company : ' . $data['company_name'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_company_control_account()
    {
        $this->db->select('*');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        return $this->db->get('srp_erp_companycontrolaccounts')->result_array();
    }

    function save_company_control_accounts()
    {
        $this->db->trans_start();
        $company_id = trim($this->input->post('companyid'));
        $this->cache->delete('000002_' . $company_id);
        $this->db->delete('srp_erp_companycontrolaccounts', array('companyID' => $company_id));
        $APA_dec = explode('|', $this->input->post('APA_dec'));
        $data[0]['accountType'] = 'Accounts Payable';
        $data[0]['accountCode'] = 'APA';
        $data[0]['GLCode'] = trim($APA_dec[0]);
        $data[0]['accountDescription'] = trim($APA_dec[1]);
        $data[0]['companyID'] = $this->common_data['company_data']['company_id'];
        $data[0]['companyCode'] = $this->common_data['company_data']['company_code'];
        $ARA_dec = explode('|', $this->input->post('ARA_dec'));
        $data[1]['accountType'] = 'Accounts Receivable';
        $data[1]['accountCode'] = 'ARA';
        $data[1]['GLCode'] = trim($ARA_dec[0]);
        $data[1]['accountDescription'] = trim($ARA_dec[1]);
        $data[1]['companyID'] = $this->common_data['company_data']['company_id'];
        $data[1]['companyCode'] = $this->common_data['company_data']['company_code'];
        $INVA_dec = explode('|', $this->input->post('INVA_dec'));
        $data[2]['accountType'] = 'Inventory Control';
        $data[2]['accountCode'] = 'INVA';
        $data[2]['GLCode'] = trim($INVA_dec[0]);
        $data[2]['accountDescription'] = trim($INVA_dec[1]);
        $data[2]['companyID'] = $this->common_data['company_data']['company_id'];
        $data[2]['companyCode'] = $this->common_data['company_data']['company_code'];
        $ACA_dec = explode('|', $this->input->post('ACA_dec'));
        $data[3]['accountType'] = 'Asset Control Account';
        $data[3]['accountCode'] = 'ACA';
        $data[3]['GLCode'] = trim($ACA_dec[0]);
        $data[3]['accountDescription'] = trim($ACA_dec[1]);
        $data[3]['companyID'] = $this->common_data['company_data']['company_id'];
        $data[3]['companyCode'] = $this->common_data['company_data']['company_code'];
        $PCA_dec = explode('|', $this->input->post('PCA_dec'));
        $data[4]['accountType'] = 'Payroll Control Account';
        $data[4]['accountCode'] = 'PCA';
        $data[4]['GLCode'] = trim($PCA_dec[0]);
        $data[4]['accountDescription'] = trim($PCA_dec[1]);
        $data[4]['companyID'] = $this->common_data['company_data']['company_id'];
        $data[4]['companyCode'] = $this->common_data['company_data']['company_code'];
        $UGRV_dec = explode('|', $this->input->post('UGRV_dec'));
        $data[5]['accountType'] = 'Unbilled GRV';
        $data[5]['accountCode'] = 'UGRV';
        $data[5]['GLCode'] = trim($UGRV_dec[0]);
        $data[5]['accountDescription'] = trim($UGRV_dec[1]);
        $data[5]['companyID'] = $this->common_data['company_data']['company_id'];
        $data[5]['companyCode'] = $this->common_data['company_data']['company_code'];

        $this->db->insert_batch('srp_erp_companycontrolaccounts', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Company Control Accounts  Saved Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Company Control Accounts  Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function load_company_header()
    {
        $this->db->select('*');
        $this->db->where('company_id', $this->input->post('companyid'));
        return $this->db->get('srp_erp_company')->row_array();
    }

    function get_company_config_details()
    {
        $this->db->select('*');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        return $this->db->get('srp_erp_documentcodemaster')->result_array();
    }

    function save_state()
    {
        $this->db->trans_start();
        $data['stateDescription'] = $this->input->post('state');
        $this->db->insert('srp_erp_state', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'State : ' . $data['stateDescription'] . '  Saved Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'State : ' . $data['stateDescription'] . ' Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }
    }

    function save_control_account()
    {
        $this->db->trans_start();
        $data['GLSecondaryCode'] = $this->input->post('GLSecondaryCode');
        $data['GLDescription'] = $this->input->post('GLDescription');
        $this->db->where('controlAccountsAutoID', $this->input->post('controlAccountsAutoID'));
        $this->db->update('srp_erp_companycontrolaccounts', $data);
        $this->db->where('GLAutoID', $this->input->post('GLAutoID'));
        $this->db->update('srp_erp_chartofaccounts', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Control Account : ' . $data['GLDescription'] . '  Update Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Control Account : ' . $data['GLDescription'] . ' Updated Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function save_chartofcontrol_account()
    {
        $this->db->trans_start();
        $account_type = explode('|', trim($this->input->post('account_type')));
        $data['accountCategoryTypeID'] = trim($this->input->post('accountCategoryTypeID'));
        $data['masterCategory'] = trim($account_type[0]);
        $data['subCategory'] = trim($account_type[1]);
        $data['CategoryTypeDescription'] = trim($account_type[2]);
        $data['GLSecondaryCode'] = trim($this->input->post('GLSecondaryCode'));
        $data['GLDescription'] = trim($this->input->post('GLDescription'));
        $data['masterAutoID'] = trim($this->input->post('masterAccount'));
        $data['isBank'] = trim($this->input->post('isBank'));

            $master_account = explode('|', trim($this->input->post('masterAccount_dec')));


        $data['masterAccount'] = trim($master_account[0]);
        $data['masterAccountDescription'] = trim($master_account[2]);
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $this->load->library('sequence');
        $this->load->library('approvals');
        $data['isActive'] = 1;
        $data['controllaccountYN']=1;
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['systemAccountCode'] = $this->sequence->sequence_generator($data['subCategory']);

        $data['confirmedYN'] = 1;
        $data['confirmedbyEmpID'] = $this->common_data['current_userID'];
        $data['confirmedbyName'] = $this->common_data['current_user'];
        $data['confirmedDate'] = $this->common_data['current_date'];

        $data['approvedYN'] = 1;
        $data['approvedbyEmpID'] = $this->common_data['current_userID'];
        $data['approvedbyEmpName'] = $this->common_data['current_user'];
        $data['approvedDate'] = $this->common_data['current_date'];
        $data['approvedComment'] = 'Auto approved';
        $this->db->insert('srp_erp_chartofaccounts', $data);
        $last_id = $this->db->insert_id();
        //$status = $this->approvals->CreateApproval('GL', $last_id, $data['systemAccountCode'], 'Chart Of Accont', 'srp_erp_chartofaccounts', 'GLAutoID',1);
/*        if (!$status) {
            $data['approvedYN'] = 1;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];
            $data['approvedComment'] = 'Auto approved';
            $this->db->where('GLAutoID', $last_id);
            $this->db->update('srp_erp_chartofaccounts', $data);
            //`srp_erp_companycontrolaccounts`
        }*/
        $this->db->insert('srp_erp_companycontrolaccounts', array('controlAccountType'=>'-', 'controlAccountDescription'=>$data['GLDescription'], 'GLAutoID' => $last_id, 'systemAccountCode'=>$data['systemAccountCode'], 'GLSecondaryCode'=>$data['GLSecondaryCode'], 'GLDescription'=>$data['GLDescription'], 'companyID' => $data['companyID'], 'companyCode' =>$data['companyCode']));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Ledger  : ' . $data['GLDescription'] . ' Save Failed ');
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Ledger : ' . $data['GLDescription'] . ' Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }

    }

    function company_image_upload()
    {
        $this->db->trans_start();
        $output_dir = "images/logo/";
        if (!file_exists($output_dir)) {
            mkdir("images/logo/", 0744);
        }

        /*$attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'com_' . trim($this->input->post('faID')) . '_'.time().'.' . 'png';
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['company_logo'] = $fileName;

        $this->db->where('company_id', trim($this->input->post('faID')));
        $this->db->update('srp_erp_company', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', "Image Upload Failed." . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Image uploaded  Successfully.');
            $this->db->trans_commit();
            return array('status' => true,'image'=> $fileName);
        }*/

        $path = UPLOAD_PATH .base_url().$output_dir;
        $fileName = 'com_' . trim($this->input->post('faID')) . '_'.time().'.' . 'png';
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|png|jpg|jpeg';
        $config['max_size'] = '200000';
        $config['file_name'] = $fileName;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        //empImage is  => $_FILES['empImage']['name'];
        $data['company_logo'] = $fileName;
        $this->db->where('company_id', trim($this->input->post('faID')));
        $this->db->update('srp_erp_company', $data);

        $this->db->trans_complete();
        if (!$this->upload->do_upload("files")) {
            return array('e', 'Image upload failed ' . $this->upload->display_errors());
        } else {
            $data['company_logo'] = $fileName;
            $this->db->where('company_id', trim($this->input->post('faID')));
            $this->db->update('srp_erp_company', $data);
            return array('s', $fileName);
        }
    }

    function update_company_codes_prefixChange(){
     $get= $this->input->post('financeTable');
        $codeID = $this->input->post('codeID');
        $prefix = $this->input->post('prefix');
        $serialno = $this->input->post('serialno');
        $format_length = $this->input->post('format_length');
        $approvalLevel = $this->input->post('approvalLevel');
        $format_1 = $this->input->post('format_1');
        $format_2 = $this->input->post('format_2');
        $format_3 = $this->input->post('format_3');
        $format_4 = $this->input->post('format_4');
        $format_5 = $this->input->post('format_5');
        $format_6 = $this->input->post('format_6');
        $printHeaderFooterYN = $this->input->post('printHeaderFooterYN');

      if(isset($get)){
          $this->db->trans_start();
          $table='srp_erp_financeyeardocumentcodemaster';
          foreach ($codeID as $key => $codeAutoID) {
              $data = array(
                  'prefix'=>$prefix[$key],
                  'serialNo'=>$serialno[$key],
                  'formatLength'=>$format_length[$key],
                  'approvalLevel'=>$approvalLevel[$key],
                  'format_1'=>$format_1[$key],
                  'format_2'=>$format_2[$key],
                  'format_3'=>$format_3[$key],
                  'format_4'=>$format_4[$key],
                  'format_5'=>$format_5[$key],
                  'format_6'=>$format_6[$key],
              );
              $this->db->where('codeID', $codeAutoID);
              $this->db->update($table, $data);
          }
      }else{
          $this->db->trans_start();
          $table='srp_erp_documentcodemaster';
          foreach ($codeID as $key => $codeAutoID) {
              $data = array(
                  'prefix'=>$prefix[$key],
                  'serialNo'=>$serialno[$key],
                  'formatLength'=>$format_length[$key],
                  'approvalLevel'=>$approvalLevel[$key],
                  'format_1'=>$format_1[$key],
                  'format_2'=>$format_2[$key],
                  'format_3'=>$format_3[$key],
                  'format_4'=>$format_4[$key],
                  'format_5'=>$format_5[$key],
                  'format_6'=>$format_6[$key],
                  'printHeaderFooterYN'=>$printHeaderFooterYN[$key],

              );
              $this->db->where('codeID', $codeAutoID);
              $this->db->update($table, $data);
          }
      }




        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Company Codes :  Update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Company Codes :  Updated Successfully.');
        }
    }

    function currency_validation(){
        $status     = TRUE;
        $data_array = array();
        $this->db->select('CurrencyCode');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('currencyID', $this->input->post('CurrencyID'));
        $currency_data = $this->db->get('srp_erp_companycurrencyassign')->row_array();
        $this->db->select('masterCurrencyCode,subCurrencyCode,conversion');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('masterCurrencyID', $this->input->post('CurrencyID'));
        $this->db->where('subCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
        $currency_default = $this->db->get('srp_erp_companycurrencyconversion')->row_array();
        if (empty($currency_default)) {
            $status     = FALSE;
        }
        $this->db->select('masterCurrencyCode,subCurrencyCode,conversion');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('masterCurrencyID', $this->input->post('CurrencyID'));
        $this->db->where('subCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']);
        $currency_reporting = $this->db->get('srp_erp_companycurrencyconversion')->row_array();
        if (empty($currency_reporting)) {
            $status     = FALSE;
        }

        $party_status   = FALSE;
        $currency_party = array();
        $party_currency_code = null;
        if ($this->input->post('partyAutoID')) {
            $party_status = TRUE;
            if ($this->input->post('partyType')=='SUP') {
                $this->db->select('supplierCurrencyID,supplierCurrency');
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $this->db->where('supplierAutoID', $this->input->post('partyAutoID'));
                $party_data = $this->db->get('srp_erp_suppliermaster')->row_array();
                $party_currency_id   = $party_data['supplierCurrencyID'];
                $party_currency_code = $party_data['supplierCurrency'];
            }else{
                $this->db->select('customerCurrencyID,customerCurrency');
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $this->db->where('customerAutoID', $this->input->post('partyAutoID'));
                $party_data = $this->db->get('srp_erp_customermaster')->row_array();
                $party_currency_id   = $party_data['customerCurrencyID'];
                $party_currency_code = $party_data['customerCurrency'];
            }
            $this->db->select('masterCurrencyCode,subCurrencyCode,conversion');
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('masterCurrencyID', $this->input->post('CurrencyID'));
            $this->db->where('subCurrencyID', $party_currency_id);
            $currency_party = $this->db->get('srp_erp_companycurrencyconversion')->row_array();
            if (empty($currency_party)) {
                $status     = FALSE;
            }
        }

        $data_array['status']               = $status;
        $data_array['data']['default']      = $currency_default;
        $data_array['data']['reporting']    = $currency_reporting;
        $data_array['data']['party_status'] = $party_status;
        $data_array['data']['party']        = $currency_party;
        $data_array['data']['rpt']          = $this->common_data['company_data']['company_reporting_currency'];
        $data_array['data']['def']          = $this->common_data['company_data']['company_default_currency'];
        $data_array['data']['par']          = $party_currency_code;
        $data_array['data']['currency']     = $currency_data['CurrencyCode'];
        return $data_array;
    }

    function update_Serialization(){
        $codeID=$this->input->post('codeID');
        $isFYBasedSerialNo=$this->input->post('isFYBasedSerialNo');
        $data = array(
            'isFYBasedSerialNo'=>$isFYBasedSerialNo,
        );
        $this->db->where('codeID', $codeID);
        $result=$this->db->update('srp_erp_documentcodemaster', $data);
        if($result==1){
            return array('s','Updated Successfully.');
        }
    }

    function add_missing_document_code(){
        $companyID=current_companyID();
        $financeyearID=$this->input->post('financeyearID');
       $result= $this->db->query("SELECT
	dcm.documentID,
  dcm.document,
  dcm.prefix,
  0 as startSerialNo,
  0 as serialNo,
  6 as formatLength,
  approvalLevel as approvalLevel,
  'prefix' as format_1,
  '/' as format_2,
  'yyyy' as format_3,
  '/' as format_4,
  'mm' as format_5,
  '/' as format_6,
 companyID as companyID,
 $financeyearID as financeyearID

FROM
	srp_erp_documentcodemaster dcm
WHERE
companyID= $companyID AND isFYBasedSerialNo=1 AND
	documentID NOT IN (
		SELECT documentID from srp_erp_financeyeardocumentcodemaster where companyID=$companyID and financeyearID=$financeyearID)")->row_array();

        if(!empty($result)){
            $results=$this->db->query("INSERT INTO srp_erp_financeyeardocumentcodemaster
(
documentID,
document,
prefix,
startSerialNo,
serialNo,
formatLength,
approvalLevel,
format_1,
format_2,
format_3,
format_4,
format_5,
format_6,
companyID,
financeyearID
)
 (SELECT
	dcm.documentID,
  dcm.document,
  dcm.prefix,
  0 as startSerialNo,
  0 as serialNo,
  6 as formatLength,
  approvalLevel as approvalLevel,
  'prefix' as format_1,
  '/' as format_2,
  'yyyy' as format_3,
  '/' as format_4,
  'mm' as format_5,
  '/' as format_6,
 companyID as companyID,
 $financeyearID as financeyearID

FROM
	srp_erp_documentcodemaster dcm
WHERE
companyID=$companyID AND isFYBasedSerialNo=1 AND
	documentID NOT IN (
		SELECT documentID from srp_erp_financeyeardocumentcodemaster where companyID=$companyID and financeyearID=$financeyearID))");
            if($results){
                return array('s','Documents Codes Added Successfully');
            }else{
                return array('e','');
            }
        }else{
            return array('e','');
        }

    }

}