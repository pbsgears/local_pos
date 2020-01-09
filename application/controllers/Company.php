<?php
class Company extends ERP_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Company_model');
    }

    function fetch_company()
    {
        $this->datatables->select('company_id,company_code,company_name,company_start_date,company_url,company_email,company_phone,company_address1,company_address2,company_city,company_province,company_postalcode,company_country,company_logo')
            ->from('srp_erp_company');
        $this->datatables->where('company_id', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('company_detail', '<h4> $1 ( $2 ) </h4>', 'company_name,company_code');
        $this->datatables->add_column('img', "<center><img class='img-thumbnail' src='$2/$1' style='width:90px;height: 80px;'><center>", 'company_logo,base_url("images/logo/")');
        $this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="fetchPage(\'system/company/erp_company_configuration_new\',$1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>', 'company_id');
        echo $this->datatables->generate();
    }

    function save_company(){
        $this->form_validation->set_rules('companycode', 'Company Code', 'trim|required');
        $this->form_validation->set_rules('companyname', 'Company Name', 'trim|required');
        $this->form_validation->set_rules('companystartdate', 'Company Start Date', 'trim|required');
        //$this->form_validation->set_rules('companyurl', 'Company URL', 'trim|required');
        //$this->form_validation->set_rules('companyemail', 'Company Email', 'trim|required');
        //$this->form_validation->set_rules('companyphone', 'Company Phone', 'trim|required');
        $this->form_validation->set_rules('companyaddress1', 'Company Address 1', 'trim|required');
        $this->form_validation->set_rules('companyaddress2', 'Company Address 2', 'trim|required');
        $this->form_validation->set_rules('companycity', 'Company City', 'trim|required');
        //$this->form_validation->set_rules('companyprovince', 'Company Province', 'trim|required');
        $this->form_validation->set_rules('companypostalcode', 'Company Postal Code', 'trim|required');
        $this->form_validation->set_rules('companycountry', 'Company Country', 'trim|required');
        //$this->form_validation->set_rules('default_segment', 'Default Segment', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Company_model->save_company_master());
        }
    }

    function save_company_control_accounts(){
        $this->form_validation->set_rules('APA', 'Accounts Payable', 'trim|required');
        $this->form_validation->set_rules('ARA', 'Accounts Receivable', 'trim|required');
        $this->form_validation->set_rules('INVA', 'Inventory Control', 'trim|required');
        $this->form_validation->set_rules('ACA', 'Asset Control Account', 'trim|required');
        $this->form_validation->set_rules('PCA', 'Payroll Control Account', 'trim|required');
        $this->form_validation->set_rules('UGRV', 'Unbilled GRV', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Company_model->save_company_control_accounts());
        }
    }

    function load_company_header()
    {
        echo json_encode($this->Company_model->load_company_header());
    }

    function get_company_config_details()
    {
        echo json_encode($this->Company_model->get_company_config_details());
    }

    function save_state()
    {
        echo json_encode($this->Company_model->save_state());
    }

    function fetch_company_control_account()
    {
        echo json_encode($this->Company_model->fetch_company_control_account());
    }

    function save_control_account()
    {
        $this->form_validation->set_rules('controlAccountsAutoID', 'Accounts ID', 'trim|required');
        $this->form_validation->set_rules('GLSecondaryCode', 'GL Secondary Code', 'trim|required');
        $this->form_validation->set_rules('GLDescription', 'GL Description', 'trim|required');
        if($this->form_validation->run()==FALSE)
        {
            $this->session->set_flashdata($msgtype='e',validation_errors());
            echo json_encode(FALSE);
        }
        else
        { 
            echo json_encode($this->Company_model->save_control_account());
        } 
    }

    function save_chartofcontrol_account(){
        $this->form_validation->set_rules('GLDescription', 'GL Description', 'trim|required');
        $this->form_validation->set_rules('masterAccountYN', 'Is Master Account', 'trim|required');
        $this->form_validation->set_rules('GLSecondaryCode', 'Secondary Code', 'trim|required');
        $this->form_validation->set_rules('accountCategoryTypeID', 'Account Type', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $companyID=current_companyID();
            $GLSecondaryCode=$this->input->post('GLSecondaryCode');
            if($GLSecondaryCode !=''){
           $exit= $this->db->query("SELECT * FROM `srp_erp_chartofaccounts` WHERE companyID = {$companyID}  AND GLSecondaryCode ='{$GLSecondaryCode}' ")->row_array();
                if(!empty($exit)){
                    $this->session->set_flashdata('e', 'GL secondary code is already exist');
                    echo json_encode(FALSE);
                    exit;
                }

            }

          $masterAccount_dec=  $this->input->post('masterAccount_dec');
            if($masterAccount_dec == 'Select Master Account'){
                $this->session->set_flashdata('e', 'Please select a Master Account');
                echo json_encode(FALSE);
                exit;
            }

            echo json_encode($this->Company_model->save_chartofcontrol_account());
        }
    }

    function company_image_upload()
    {
        $this->form_validation->set_rules('faID', 'Company Id is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Company_model->company_image_upload());
        }
    }

    function fetch_company_codeMaster()
    {
        $company_id = $this->common_data['company_data']['company_id'];

        $documentMaster = $this->db->query("SELECT * FROM srp_erp_documentcodemaster JOIN `srp_erp_documentcodes` ON `srp_erp_documentcodes`.`documentID` = `srp_erp_documentcodemaster`.`documentID` WHERE companyID='{$company_id}' AND `isApprovalDocument` = 1")->result_array();

        $YN_arr = array( '1' => 'YES', '0' => 'No');
        $div_arr = array('' => 'Blank', '/' => '/', '-' => '-');
        $div_prefix = array('' => 'Blank', 'prefix' => 'Prefix', 'yyyy' => 'YYYY', 'yy' => 'YY', 'mm' => 'MM');
        $div_isFYBasedSerialNo = array('0' => 'standard', '1' => 'Finance Year based');

        $data = '<table class="table table-bordered table-striped table-condesed" id="standedtbl">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 5%">Document</th>
                <th style="min-width: 10%">Serialization</th>
                <th style="min-width: 10%">Prefix</th>
                <th style="min-width: 10%">Serial No</th>
                <th style="min-width: 10%">Format Length</th>
                <th style="min-width: 10%">Format 1</th>
                <th style="min-width: 10%">Format 2</th>
                <th style="min-width: 10%">Format 3</th>
                <th style="min-width: 10%">Format 4</th>
                <th style="min-width: 10%">Format 5</th>
                <th style="min-width: 10%">Format 6</th>
                <th style="min-width: 5%">Approval Level</th>
                <th style="min-width: 5%">Print Header/footer</th>
            </tr>
            </thead>
            <tbody>';
            $i= 1;
            foreach($documentMaster as $val){
                if($val['isFinance']==0 || $val['isFinance']==null){
                    $isFYBasedSerial="disabled";
                }else{
                    $isFYBasedSerial="";
                }
                $data .= '<tr>
                    <td>' . $i .' </td>
                    <td>' . $val['documentID'] .' - ' . $val['document'] .' <input type="hidden" value="' . $val['codeID'] .'"  name="codeID[]"></td>
                    <td>'.form_dropdown('isFYBasedSerialNo[]',$div_isFYBasedSerialNo , $val['isFYBasedSerialNo'], 'class="form-control" onchange="update_Serialization('.$val['codeID'].')" '.$isFYBasedSerial.' id="isFYBasedSerialNo_'.$val['codeID'].'"  ').'</td>
                    <td style="width:120px;"><input type="text" class="form-control" name="prefix[]" value="' . $val['prefix'] .'"></td>
                    <td style="width:120px;"><input type="number" class="form-control" name="serialno[]" value="' . $val['serialNo'] .'"></td>
                    <td style="width:120px;"><input type="number" class="form-control" name="format_length[]" value="' . $val['formatLength'] .'"></td>
                    <td>'.form_dropdown('format_1[]',$div_prefix , $val['format_1'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_2[]',$div_arr , $val['format_2'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_3[]',$div_prefix , $val['format_3'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_4[]',$div_arr , $val['format_4'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_5[]',$div_prefix , $val['format_5'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_6[]',$div_arr , $val['format_6'], 'class="form-control"').'</td>
                    <td style="width:120px;"><input type="number" class="form-control" name="approvalLevel[]" value="' . $val['approvalLevel'] .'"></td>
                    <td>'.form_dropdown('printHeaderFooterYN[]',$YN_arr , $val['printHeaderFooterYN'], 'class="form-control"').'</td>
                </tr>';

            $i++;
            }

        echo $data;

    }

    function fetch_company_codeMaster_fin()
    {
        $financeyear=$this->input->post('financeyear');
        $company_id = $this->common_data['company_data']['company_id'];

        $documentMaster = $this->db->query("SELECT * FROM srp_erp_financeyeardocumentcodemaster  WHERE companyID='{$company_id}' AND financeYearID='{$financeyear}'")->result_array();

        $div_arr    = ['' => 'Blank', '/' => '/', '-' => '-'];
        $div_prefix = ['' => 'Blank', 'prefix' => 'Prefix', 'yyyy' => 'YYYY', 'yy' => 'YY', 'mm' => 'MM'];

        $data = '<table class="table table-bordered table-striped table-condesed">
            <thead>
            <tr>
              <th style="min-width: 5%">#</th>
              <th style="min-width: 5%">Document</th>
              <th style="min-width: 10%">Prefix</th>
              <th style="min-width: 10%">Serial No</th>
              <th style="min-width: 10%">Format Length</th>
              <th style="min-width: 10%">Format 1</th>
              <th style="min-width: 10%">Format 2</th>
              <th style="min-width: 10%">Format 3</th>
              <th style="min-width: 10%">Format 4</th>
              <th style="min-width: 10%">Format 5</th>
              <th style="min-width: 10%">Format 6</th>
          </tr>
            </thead>
            <tbody>';
            $i= 1;
        if($documentMaster){
            foreach($documentMaster as $val){
                $data .= '<tr>
                    <td>' . $i .' </td>
                    <td>' . $val['documentID'] .' - ' . $val['document'] .' <input type="hidden" value="' . $val['codeID'] .'"  name="codeID[]"></td>
                    <td style="width:120px;"><input type="text" class="form-control" name="prefix[]" value="' . $val['prefix'] .'"></td>
                    <td style="width:120px;"><input type="number" class="form-control" name="serialno[]" value="' . $val['serialNo'] .'"></td>
                    <td style="width:120px;"><input type="number" class="form-control" name="format_length[]" value="' . $val['formatLength'] .'"></td>
                    <td>'.form_dropdown('format_1[]',$div_prefix , $val['format_1'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_2[]',$div_arr , $val['format_2'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_3[]',$div_prefix , $val['format_3'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_4[]',$div_arr , $val['format_4'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_5[]',$div_prefix , $val['format_5'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_6[]',$div_arr , $val['format_6'], 'class="form-control"').'</td>
                </tr>';

                $i++;
            }
        }else{
            $data .= '<tr><td colspan="11" align="center">No Records Found</td></tr>';
        }


        echo $data;

    }

    function update_company_codes_prefixChange(){
        echo json_encode($this->Company_model->update_company_codes_prefixChange());
    }

    function currency_validation(){
        echo json_encode($this->Company_model->currency_validation());
    }

    function update_Serialization(){
        echo json_encode($this->Company_model->update_Serialization());
    }

    function add_missing_document_code(){
        echo json_encode($this->Company_model->add_missing_document_code());
    }
}