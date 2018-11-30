<?php
class customer extends ERP_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Customer_model');
    }

    function fetch_customer()
    {
        $customer_filter = '';
        $category_filter = '';
        $currency_filter = '';
        $customer = $this->input->post('customerCode');
        $category = $this->input->post('category');
        $currency = $this->input->post('currency');
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerAutoID IN " . $whereIN;
        }
        if (!empty($category)) {
            $category = array($this->input->post('category'));
            $whereIN = "( " . join("' , '", $category) . " )";
            $category_filter = " AND srp_erp_customermaster.partyCategoryID IN " . $whereIN;
        }
        if (!empty($currency)) {
            $currency = array($this->input->post('currency'));
            $whereIN = "( " . join("' , '", $currency) . " )";
            $currency_filter = " AND customerCurrencyID IN " . $whereIN;
        }
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_customermaster.companyID = " . $companyid . $customer_filter .$category_filter .$currency_filter. "";
        $this->datatables->select('srp_erp_partycategories.categoryDescription as categoryDescription,customerAutoID,customerSystemCode,secondaryCode,customerName,customerAddress1,customerAddress2,customerCountry,customerTelephone,customerEmail,customerUrl,customerFax,isActive,customerCurrency,customerEmail,customerTelephone,customerCurrencyID,cust.Amount as Amount,cust.partyCurrencyDecimalPlaces as partyCurrencyDecimalPlaces')
            ->where($where)
            ->from('srp_erp_customermaster')
            ->join('srp_erp_partycategories', 'srp_erp_customermaster.partyCategoryID = srp_erp_partycategories.partyCategoryID','left')
            ->join('(SELECT sum(srp_erp_generalledger.transactionAmount/srp_erp_generalledger.partyExchangeRate) as Amount,partyAutoID,partyCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE partyType = "CUS" AND subLedgerType=3 GROUP BY partyAutoID) cust', 'cust.partyAutoID = srp_erp_customermaster.customerAutoID','left');
        $this->datatables->add_column('customer_detail', '<b>Name : </b> $1 &nbsp;&nbsp;&nbsp;<b>Secondary Code : </b>$5<br><b>Address : </b> $2 &nbsp;&nbsp;$3 &nbsp;&nbsp;$4.<br><b> Email </b> $7  &nbsp;&nbsp;&nbsp;<b>Telephone</b> $8','customerName,customerAddress1, customerAddress2, customerCountry, secondaryCode, customerCurrency, customerEmail,customerTelephone');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('edit', '$1', 'editcustomer(customerAutoID)');
        $this->datatables->edit_column('amt', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(Amount,partyCurrencyDecimalPlaces),customerCurrency');
        //$this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="attachment_modal($1,\'Customer\',\'CUS\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="fetchPage(\'system/customer/erp_customer_master_new\',$1,\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_customer($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'customerAutoID');
        echo $this->datatables->generate();
    }

    function fetch_sales_person()
    {
        $this->datatables->select('salesPersonID,wareHouseCode,SalesPersonName,wareHouseCode,wareHouseLocation,wareHouseDescription, isActive,salesPersonCurrency,SalesPersonCode,SecondaryCode,SalesPersonEmail,contactNumber')
           ->where('companyID', $this->common_data['company_data']['company_id'])
            ->from('srp_erp_salespersonmaster');
        $this->datatables->add_column('SalesPerson_detail', '<b>Name : </b>$1 <b>Location : </b> $2 <b> Contact Number : </b> $3 <b> Email : </b> $4 ','SalesPersonName,wareHouseLocation, contactNumber,SalesPersonEmail');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="attachment_modal($1,\'Sales person\',\'REP\');"><span class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="fetchPage(\'system/sales/erp_sales_person_new\',\'$1\',\'Sales person\')"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_sales_person($1)"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'salesPersonID');
        echo $this->datatables->generate();
    }

    function save_customer()
    {
        if (!$this->input->post('customerAutoID')) {
            $this->form_validation->set_rules('customerCurrency', 'customer Currency', 'trim|required');
        }
        $this->form_validation->set_rules('customercode', 'customer Code', 'trim|required');
        $this->form_validation->set_rules('customerName', 'customer Name', 'trim|required');
        $this->form_validation->set_rules('customercountry', 'customer country', 'trim|required');
/*        $this->form_validation->set_rules('customerTelephone', 'customer Telephone', 'trim|required');
        $this->form_validation->set_rules('customerEmail', 'customer Email', 'trim|required');
        $this->form_validation->set_rules('customerAddress1', 'Address 1', 'trim|required');
        $this->form_validation->set_rules('customerAddress2', 'Address 2', 'trim|required');
        $this->form_validation->set_rules('customerCreditLimit', 'Credit Limit', 'trim|required');
        $this->form_validation->set_rules('customerCreditPeriod', 'Credit Period', 'trim|required|max_length[3]');*/
        $this->form_validation->set_rules('receivableAccount', 'Receivable Account', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Customer_model->save_customer());
        }
    }

    function fetch_sales_person_details()
    {
        echo json_encode($this->Customer_model->fetch_sales_person_details());
    }

    function delete_sales_target()
    {
        echo json_encode($this->Customer_model->delete_sales_target());
    }

    function laad_sale_target()
    {
        echo json_encode($this->Customer_model->laad_sale_target());
    }

    // function edit_customer()
    // {
    //     if($this->input->post('id') !=""){
    //         echo json_encode($this->Customer_model->get_customer());
    //     }
    //     else{
    //         echo json_encode(FALSE);
    //     }
    // }

    function load_customer_header()
    {
        echo json_encode($this->Customer_model->load_customer_header());
    }

    function laad_sale_person_header()
    {
        echo json_encode($this->Customer_model->laad_sale_person_header());
    }

    function delete_customer()
    {
        echo json_encode($this->Customer_model->delete_customer());
    }

    function delete_sales_person()
    {
        echo json_encode($this->Customer_model->delete_sales_person());
    }

    function fetch_customer_category()
    {
        $this->datatables->select('partyCategoryID,partyType,categoryDescription')
            ->where('companyID', $this->common_data['company_data']['company_id'])
            ->where('partyType', 1)
            ->from('srp_erp_partycategories');
        $this->datatables->add_column('edit', '$1', 'editcustomercategory(partyCategoryID)');
        echo $this->datatables->generate();
    }

    function saveCategory()
    {


        if (empty($this->input->post('categoryDescription'))) {
            echo json_encode( ['e', 'Enter Category'] );
        } else {
            echo json_encode($this->Customer_model->saveCategory());
        }
    }

    function getCategory(){
        echo json_encode($this->Customer_model->getCategory());
    }

    function fetch_employee_detail(){
        echo json_encode($this->Customer_model->fetch_employee_detail());
    }

    function delete_category()
    {
        echo json_encode($this->Customer_model->delete_category());
    }

    function load_sale_conformation()
    {
        $salesPersonID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('salesPersonID'));
        $data['extra'] = $this->Customer_model->fetch_template_data($salesPersonID);
        $html = $this->load->view('system/sales/erp_sales_person_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['approvedYN']);
        }
    }

    function img_uplode(){
        $this->form_validation->set_rules('salesPersonID', 'Sales Person ID', 'trim|required');
        if($this->form_validation->run()==FALSE){
            $this->session->set_flashdata($msgtype='e',validation_errors());
            echo json_encode(FALSE);
        }else{ 
            echo json_encode($this->Customer_model->img_uplode());
        }   
    }

    //get sales target end amount
    function load_sales_target_endamount()
    {
        echo json_encode($this->Customer_model->load_sales_target_endamount());
    }

    function fetch_customer_percentage()
    {
        $customer_filter = '';
        $category_filter = '';
        $currency_filter = '';
        $customer = $this->input->post('customerCode');
        $category = $this->input->post('category');
        $currency = $this->input->post('currency');
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerAutoID IN " . $whereIN;
        }
        if (!empty($category)) {
            $category = array($this->input->post('category'));
            $whereIN = "( " . join("' , '", $category) . " )";
            $category_filter = " AND srp_erp_customermaster.partyCategoryID IN " . $whereIN;
        }
        if (!empty($currency)) {
            $currency = array($this->input->post('currency'));
            $whereIN = "( " . join("' , '", $currency) . " )";
            $currency_filter = " AND customerCurrencyID IN " . $whereIN;
        }
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_customermaster.companyID = " . $companyid . $customer_filter .$category_filter .$currency_filter. "";
        $this->datatables->select('srp_erp_partycategories.categoryDescription as categoryDescription,customerAutoID,customerSystemCode,secondaryCode,customerName,customerAddress1,customerAddress2,customerCountry,customerTelephone,customerEmail,customerUrl,customerFax,isActive,customerCurrency,customerEmail,customerTelephone,customerCurrencyID,cust.Amount as Amount,cust.partyCurrencyDecimalPlaces as partyCurrencyDecimalPlaces,finCompanyPercentage,pvtCompanyPercentage,capAmount')
            ->where($where)
            ->from('srp_erp_customermaster')
            ->join('srp_erp_partycategories', 'srp_erp_customermaster.partyCategoryID = srp_erp_partycategories.partyCategoryID','left')
            ->join('(SELECT sum(srp_erp_generalledger.transactionAmount/srp_erp_generalledger.partyExchangeRate) as Amount,partyAutoID,partyCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE partyType = "CUS" AND subLedgerType=3 GROUP BY partyAutoID) cust', 'cust.partyAutoID = srp_erp_customermaster.customerAutoID','left');
        $this->datatables->add_column('customer_detail', '<b>Name : </b> $1 &nbsp;&nbsp;&nbsp;<b>Secondary Code : </b>$5<br><b>Address : </b> $2 &nbsp;&nbsp;$3 &nbsp;&nbsp;$4.<br><b> Email </b> $7  &nbsp;&nbsp;&nbsp;<b>Telephone</b> $8','customerName,customerAddress1, customerAddress2, customerCountry, secondaryCode, customerCurrency, customerEmail,customerTelephone');
        $this->datatables->add_column('DT_RowId', 'common_$1', 'customerAutoID');

        $this->datatables->add_column('capAmount', '<input style="width: 50%" type="text" class="form-control cap number"
                                   value="$1"
                                   name="capAmount[]" onkeypress="return validateFloatKeyPress(this,event)">
                    <span class="applytoAll">
                        <button class="btn btn-xs btn-default" type="button" onclick="applyToAllCols(this)"><i
                                class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'capAmount');

        $this->datatables->add_column('fc', '<input style="width: 50%" type="text" class="form-control fc number"
                                   value="$1"
                                   name="finCompanyPercentage[]" onkeyup="validatePercentage(this,\'fc\')" onkeypress="return validateFloatKeyPress(this,event)">
                    <span class="applytoAll">
                        <button class="btn btn-xs btn-default" type="button" onclick="applyToAllCols(this)"><i
                                class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'finCompanyPercentage');

        $this->datatables->add_column('pc', '<input style="width: 50%" type="text" class="form-control pc number"
                                   value="$2"
                                   name="pvtCompanyPercentage[]" onkeyup="validatePercentage(this,\'pc\')" onkeypress="return validateFloatKeyPress(this,event,5)">
                                   <input type="hidden" name="customerAutoID[]" value="$1">
                    <span class="applytoAll">
                        <button class="btn btn-xs btn-default" type="button" onclick="applyToAllCols(this)"><i
                                class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'customerAutoID,pvtCompanyPercentage');

        $this->datatables->edit_column('amt', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(Amount,partyCurrencyDecimalPlaces),customerCurrency');
        //$this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="attachment_modal($1,\'Customer\',\'CUS\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="fetchPage(\'system/customer/erp_customer_master_new\',$1,\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_customer($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'customerAutoID');
        echo $this->datatables->generate();
    }

    function save_customer_percentage(){
        echo json_encode($this->Customer_model->save_customer_percentage());
    }
}
