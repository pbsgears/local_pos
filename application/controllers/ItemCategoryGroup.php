<?php
class ItemCategoryGroup extends ERP_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Itemcategorygroup_model');
        $this->load->helpers('GroupManagement');
    }



    function load_category(){
        $companyID=$this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $Grpid=$companyID;

        $this->datatables->select('itemCategoryID,description,masterID')
            ->from('srp_erp_groupitemcategory')
            ->where('masterID',NULL )
            ->where('groupID', $Grpid)
            ->edit_column('addsub', '$1', 'opensubcatgroup(itemCategoryID,description)');
            echo $this->datatables->generate();
    }

    function save_itemcategory()
    {
        $this->form_validation->set_rules('codeprefix', 'Code Prefix', 'trim|required');
        $this->form_validation->set_rules('startserial', 'Start Serial', 'trim|required');
        $this->form_validation->set_rules('codelength', 'Code Length', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('itemtype', 'Item Type', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Itemcategorygroup_model->save_item_category());
        }
    }

    function edit_itemcategory()
    {
        if($this->input->post('id') !=""){
            echo json_encode($this->Itemcategorygroup_model->edit_itemcategory());
        }
        else{
            echo json_encode(FALSE);
        }
    }

    function save_subcategory()
    {
        $CategoryID = $this->input->post('master');

        $Category = $this->db->query("SELECT itemCategoryID,categoryTypeID FROM srp_erp_groupitemcategory WHERE itemCategoryID = '{$CategoryID}'")->row_array();
        if($Category['categoryTypeID'] == 2){
            //$this->form_validation->set_rules('costgl', 'Cost GL', 'trim|required');
            // $this->form_validation->set_rules('revnugl', 'Revenue GL', 'trim|required');
        } else if($Category['categoryTypeID'] == 3){
            /*$this->form_validation->set_rules('COSTGLCODEdes', 'Cost Account', 'trim|required');
            $this->form_validation->set_rules('ACCDEPGLCODEdes', 'Acc Dep GL Code', 'trim|required');
            $this->form_validation->set_rules('DEPGLCODEdes', 'Dep GL Code', 'trim|required');
            $this->form_validation->set_rules('DISPOGLCODEdes', 'Disposal GL Code', 'trim|required');*/
        }
        else{
            // $this->form_validation->set_rules('revnugl', 'Revenue GL', 'trim|required');
            //$this->form_validation->set_rules('costgl', 'Cost GL', 'trim|required');
            //$this->form_validation->set_rules('assetgl', 'Asset GL', 'trim|required');
        }
        $this->form_validation->set_rules('subcategory', 'Sub Category', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Itemcategorygroup_model->save_sub_category());
        }
    }

    function load_subcategoryMaster(){
        $companyid = current_companyID();
       /* $this->db->select('companyGroupID');
        $this->db->where('companyID', $companyid);
        $grp = $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid = $companyid;

        $data['pageID']= $this->input->post('idedit');
        $companyID=$this->common_data['company_data']['company_id'];
        $data['table']=$this->db->query("select itemCategoryID,description,masterID,revenueGL,costGL,assetGL,faCostGLAutoID,faACCDEPGLAutoID,faDEPGLAutoID,faDISPOGLAutoID from srp_erp_groupitemcategory where groupID = '{$grpid}' AND masterID IS NOT NULL  order by itemCategoryID desc ")->result_array();
        $data['depMaster'] = $this->db->query("SELECT itemCategoryID,categoryTypeID,masterID FROM srp_erp_groupitemcategory WHERE itemCategoryID = '{$data['pageID']}'")->row_array();
        $this->load->view('system/GroupItemCategory/erp_item_category_group', $data);


    }

    function edit_itemsubcategory()
    {
        if($this->input->post('id') !=""){
            echo json_encode($this->Itemcategorygroup_model->edit_itemsubcategory());
        }
        else{
            echo json_encode(FALSE);
        }
    }

    function update_subsubcategory(){

        $this->form_validation->set_rules('descriptionsubsub', 'Sub Sub Category', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            //$this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(array('e',validation_errors()));
        } else {
            echo json_encode($this->Itemcategorygroup_model->update_subsubcategory());
        }
    }

    function update_subcategory(){
        echo json_encode($this->Itemcategorygroup_model->update_itemsubcategory());
    }

    function save_subsubcategory()
    {
        $this->form_validation->set_rules('subsubcategory', 'Sub Sub Category', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Itemcategorygroup_model->save_sub_sub_category());
        }
    }

    function edit_itemsubsubcategory()
    {
        if($this->input->post('id') !=""){
            echo json_encode($this->Itemcategorygroup_model->edit_itemsubsubcategory());
        }
        else{
            echo json_encode(FALSE);
        }
    }

    function load_company()
    {
        $data['itemCategoryID'] = $this->input->post('itemCategoryID');
        $html = $this->load->view('system/GroupItemCategory/ajax/ajax-erp_load_company', $data, true);
        echo $html;
    }

    function load_company_itemcategory()
    {
        $data['companyID'] = $this->input->post('companyID');
        $data['itemCategoryID'] = $this->input->post('itemCategoryID');
        $html = $this->load->view('system/GroupItemCategory/ajax/erp_load_company_itemcategory', $data, true);
        echo $html;
    }

    function fetch_category_Details(){
        $itemCategoryID=$this->input->post('itemCategoryID');

        $this->datatables->select('groupItemCategoryDetailID,groupItemCategoryID,srp_erp_groupitemcategorydetails.itemCategoryID,srp_erp_groupitemcategorydetails.companyID,companyGroupID,srp_erp_company.company_name as company_name,srp_erp_itemcategory.description as description');
        $this->datatables->from('srp_erp_groupitemcategorydetails');
        $this->datatables->join('srp_erp_itemcategory', 'srp_erp_groupitemcategorydetails.itemCategoryID = srp_erp_itemcategory.itemCategoryID');
        $this->datatables->join('srp_erp_company', 'srp_erp_groupitemcategorydetails.companyID = srp_erp_company.company_id');
        $this->datatables->where('srp_erp_groupitemcategorydetails.groupItemCategoryID', $itemCategoryID);
        //$this->datatables->where('srp_erp_groupchartofaccountdetails.companyGroupID', $grpid);
        $this->datatables->add_column('edit', '<a onclick="delete_Item_group_category_link($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'groupItemCategoryDetailID');
        echo $this->datatables->generate();
    }

    function save_item_category_link()
    {

        $this->form_validation->set_rules('companyIDgrp[]', 'Company', 'trim|required');
        $this->form_validation->set_rules('itemCategoryID[]', 'Item Category', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Itemcategorygroup_model->save_item_category_link());
        }
    }

    function delete_item_category_link(){
        echo json_encode($this->Itemcategorygroup_model->delete_item_category_link());
    }

    function load_all_companies_item_categories(){
        $company=array();
        $groupItemCategoryID=$this->input->post('groupItemCategoryID');
        $comp = customer_company_link($groupItemCategoryID);
        foreach($comp as $val){
            $company[]=$val['companyID'];
        }
        $data['companyID']=$company;
        $data['groupItemCategoryID']=$groupItemCategoryID;
        $html = $this->load->view('system/GroupItemCategory/ajax/erp_load_company_itemcategory', $data, true);
        echo $html;
    }

    function load_category_header()
    {
        echo json_encode($this->Itemcategorygroup_model->load_category_header());
    }

}
