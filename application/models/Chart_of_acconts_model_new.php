<?php
class Chart_of_acconts_model_new extends ERP_Model{

    function save_chart_of_accont(){
        $this->db->trans_start();
        $this->db->select('levelNo');
        $this->db->where('GLAutoID', $this->input->post('masterAccount'));
        $level= $this->db->get('srp_erp_chartofaccounts')->row_array();

        $controlAccountUpdate= $this->input->post('controlAccountUpdate');
        if($controlAccountUpdate==0){  //if not control account update
        $isActive = 0;
        if(!empty($this->input->post('isActive'))){
            $isActive = 1;
        }
        $account_type             = explode('|', trim($this->input->post('account_type')));
        $data['accountCategoryTypeID']              = trim($this->input->post('accountCategoryTypeID'));
        $data['masterCategory']                     = trim($account_type[0]);
        $data['subCategory']                        = trim($account_type[1]);
        $data['CategoryTypeDescription']            = trim($account_type[2]);

        $data['masterAutoID']           	        = trim($this->input->post('masterAccount'));
        $data['isBank']                             = trim($this->input->post('isBank'));
        $data['isCard']                             = trim($this->input->post('isCard'));
        $data['isCash']                             = trim($this->input->post('isCash'));
        /*$data['authourizedSignatureLevel']          = trim($this->input->post('authourizedSignatureLevel'));*/
        if($data['isCash'] ==1){
            $data['bankAccountNumber']                  = 'N/A';
            $data['bankName']                           = trim($this->input->post('GLDescription'));
            $data['bankBranch']                         ='-';
        }
        else{
            $data['bankAccountNumber']                  = trim($this->input->post('bankAccountNumber'));
            $data['bankName']                           = trim($this->input->post('bankName'));
            $data['bankBranch']                         = trim($this->input->post('bank_branch'));
        }


        $data['bankSwiftCode']                      = trim($this->input->post('bank_swift_code'));
        $data['bankCheckNumber']                    = trim($this->input->post('bankCheckNumber'));
        $data['masterAccountYN']                    = trim($this->input->post('masterAccountYN'));
        $data['bankCurrencyCode']                   = trim($this->input->post('bankCurrencyCode'));
        $data['levelNo']                            = $level['levelNo']+1;
       /*if currencyCode set get currencyID*/
        if($data['bankCurrencyCode']!=''){
           $data['bankCurrencyID']=fetch_currency_ID($data['bankCurrencyCode']);
        }else{
            $data['bankCurrencyID']='';
        }

        if ($data['isCash']) {
            $data['bankCurrencyID']=$this->common_data['company_data']['company_default_currencyID'];
        }

        if ($data['masterAccountYN']==1) {
            $data['masterAccount']                  = '';
            $data['masterAccountDescription']       = '';
            $data['levelNo']                        = 0;
        }else{
            $master_account                         = explode('|', trim($this->input->post('masterAccount_dec')));
            $data['masterAccount']                  = trim($master_account[0]);
            $data['masterAccountDescription']       = trim($master_account[2]);
        }
        $data['approvedYN']=1;
        $data['isActive'] = $isActive;
        }
        $data['GLSecondaryCode']                    = trim($this->input->post('GLSecondaryCode'));
        $data['GLDescription']                      = trim($this->input->post('GLDescription'));

        $data['modifiedPCID']                       = $this->common_data['current_pc'];
        $data['modifiedUserID']       	            = $this->common_data['current_userID'];
        $data['modifiedUserName']                   = $this->common_data['current_user'];
        $data['modifiedDateTime']                   = $this->common_data['current_date'];
        if (trim($this->input->post('GLAutoID'))) {
            $this->db->where('GLAutoID', trim($this->input->post('GLAutoID')));
            $this->db->update('srp_erp_chartofaccounts', $data);
            if($controlAccountUpdate==1) { /*conreol account = 1 update srp_erp_companycontrolaccounts */
                $this->db->update('srp_erp_companycontrolaccounts', array(
                    'GLSecondaryCode' => $data['GLSecondaryCode'],
                    'GLDescription' => $data['GLDescription']
                ), array('GLAutoID' => trim($this->input->post('GLAutoID'))));
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Ledger : ' . $data['GLDescription'] . ' Update Failed ');
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Ledger : ' . $data['GLDescription']. ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('GLAutoID'));
            }
        } else {


            $this->load->library('sequence');
            $this->load->library('approvals');
            $data['isActive']                       = 1;
            $data['companyID']                      = $this->common_data['company_data']['company_id'];
            $data['companyCode']                    = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup']               = $this->common_data['user_group'];
            $data['createdPCID']                    = $this->common_data['current_pc'];
            $data['createdUserID']                  = $this->common_data['current_userID'];
            $data['createdUserName']                = $this->common_data['current_user'];
            $data['createdDateTime']                = $this->common_data['current_date'];
            $data['systemAccountCode']              = $this->sequence->sequence_generator($data['subCategory']);
            $data['approvedYN']                     = 1;
            $data['approvedbyEmpID']                = $this->common_data['current_userID'];
            $data['approvedbyEmpName']              = $this->common_data['current_user'];
            $data['approvedDate']                   = $this->common_data['current_date'];
            $data['approvedComment']                = 'Auto approved';
            $data['confirmedYN']                    = 1;
            $data['confirmedDate']                  = $this->common_data['current_date'];
            $data['confirmedbyEmpID']               = $this->common_data['current_userID'];
            $data['confirmedbyName']                = $this->common_data['current_user'];
            $this->db->insert('srp_erp_chartofaccounts', $data);
            $last_id = $this->db->insert_id();
            //$status = $this->approvals->CreateApproval('GL',$last_id,$data['systemAccountCode'],'Chart Of Accont','srp_erp_chartofaccounts','GLAutoID',1);
            // if ($status==1) {
            //     $data['approvedYN']             = 1;
            //     $data['approvedbyEmpID']        = $this->common_data['current_userID'];
            //     $data['approvedbyEmpName']      = $this->common_data['current_user'];
            //     $data['approvedDate']           = $this->common_data['current_date'];
            //     $data['approvedComment']        = 'Auto approved';
            //     $this->db->where('GLAutoID', $last_id);
            //     $this->db->update('srp_erp_chartofaccounts', $data);
            // }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Ledger  : ' . $data['GLDescription'] . ' Save Failed ');
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('w', '');
                $this->session->set_flashdata('s', 'Ledger : ' . $data['GLDescription'] . ' Added Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function load_chart_of_accont_header()
    {
        $this->db->select('*');
        $this->db->where('GLAutoID', $this->input->post('GLAutoID'));
        return $this->db->get('srp_erp_chartofaccounts')->row_array();
    }

    function fetch_master_account(){
        if(!empty($this->input->post('isCash'))){
            $cash = trim($this->input->post('isCash'));
            if($cash == 1){
                $iscash=" AND (isCash = {$cash} OR masterAccountYN = 1 )";
            }else{
                $iscash=" AND (isCash = {$cash} OR masterAccountYN =  0 )";
            }
        }else{
            $iscash='';
        }
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "companyId = " . $companyid . $iscash . "";
        $this->db->select('GLSecondaryCode,GLDescription,systemAccountCode,GLSecondaryCode,GLAutoID');
        $this->db->from('srp_erp_chartofaccounts');
        $this->db->where('accountCategoryTypeID', trim($this->input->post('accountCategoryTypeID')));
        $this->db->where('subCategory', trim($this->input->post('subCategory')));
        //$this->db->where('masterAccountYN', 1);
        $this->db->where('GLAutoID<>', trim($this->input->post('GLAutoID')));
        //$this->db->where('(masterAccountYN = 1 or  controllAccountYN = 1)');
        //$this->db->where('companyId', $this->common_data['company_data']['company_id']);
        $this->db->where($where);
        $this->db->order_by('GLAutoID', 'desc');
        return $this->db->get()->result_array();
    }

    function delete_chart_of_accont()
    {
        $this->db->where('GLAutoID', $this->input->post('GLAutoID'));
        $result= $this->db->delete('srp_erp_chartofaccounts');
        $this->session->set_flashdata('s', 'Record Deleted Successfully');
        return true;
    }

    function fetch_cheque_number(){
        $this->db->select('bankCheckNumber,isCash');
        $this->db->where('GLAutoID', $this->input->post('GLAutoID'));
        return $this->db->get('srp_erp_chartofaccounts')->row_array();
    }

}