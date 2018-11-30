<?php
class ReportTemplate extends ERP_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Report_template_model');
    }

    function fetch_company_report_template_table()
    {
        $companyID=$this->common_data['company_data']['company_id'];
        $this->datatables->select('companyReportTemplateID,description,srp_erp_companyreporttemplate.reportID,srp_erp_reporttemplate.reportDescription as reportDescription')
            ->where('srp_erp_companyreporttemplate.companyID', $companyID)
            ->from('srp_erp_companyreporttemplate')
            ->join('srp_erp_reporttemplate', 'srp_erp_companyreporttemplate.reportID = srp_erp_reporttemplate.reportID');
        //$this->datatables->add_column('edit', '$1', 'editgroupcategory(partyCategoryID)');
        $this->datatables->add_column('edit', '<a onclick="company_report_template_details_modal($1)"><span title="Config" rel="tooltip" class="glyphicon glyphicon-cog" ></span></a>&nbsp;|&nbsp;<a onclick="delete_report_tempalte_master($1)"><span title="Delete" style="color:rgb(209, 91, 71);" rel="tooltip" class="glyphicon glyphicon-trash" ></span></a>', 'companyReportTemplateID');
        echo $this->datatables->generate();
    }

    function saveRepoetTempaleMaster()
    {

        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('reportID', 'Type', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_template_model->saveRepoetTempaleMaster());
        }
    }

    function delete_report_tempalte_master()
    {
        $this->db->select('companyReportTemplateDetailID');
        $this->db->where('companyReportTemplateID', trim($this->input->post('companyReportTemplateID')));
        $this->db->from('srp_erp_companyreporttemplatedetails');
        $detail = $this->db->get()->row_array();
        if(!empty($detail)){
            $this->db->delete('srp_erp_companyreporttemplatelinks', array('companyReportTemplateDetailID' => trim($detail['companyReportTemplateDetailID'])));
        }
        $this->db->delete('srp_erp_companyreporttemplatedetails', array('companyReportTemplateID' => trim($this->input->post('companyReportTemplateID'))));
        $status=$this->db->delete('srp_erp_companyreporttemplate', array('companyReportTemplateID' => trim($this->input->post('companyReportTemplateID'))));
        if($status){
            echo json_encode(array('s', ' Deleted Successfully.', $status));
        }else {
            echo json_encode(array('e', ' Error in Deletion.', $status));
        }
    }

    function fetch_company_report_template_details_table(){
        $companyID=$this->common_data['company_data']['company_id'];
        $companyReportTemplateID=$this->input->post('companyReportTemplateID');
        $this->datatables->select('companyReportTemplateDetailID,companyReportTemplateID,description,sortOrder')
            ->where('companyID', $companyID)
            ->where('companyReportTemplateID', $companyReportTemplateID)
            ->from('srp_erp_companyreporttemplatedetails');
        $this->datatables->add_column('edit', '<a onclick="company_report_template_links_modal($1)"><span title="Config" rel="tooltip" class="glyphicon glyphicon-cog" ></span></a>&nbsp;|&nbsp;<a onclick="delete_report_tempalte_detail($1)"><span title="Delete" style="color:rgb(209, 91, 71);" rel="tooltip" class="glyphicon glyphicon-trash" ></span></a>', 'companyReportTemplateDetailID');
        echo $this->datatables->generate();
    }

    function saveRepoetTempaleDetail()
    {

        $this->form_validation->set_rules('detaildescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('sortOrder', 'Sort Order', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_template_model->saveRepoetTempaleDetail());
        }
    }

    function delete_report_tempalte_detail(){
        $this->db->delete('srp_erp_companyreporttemplatelinks', array('companyReportTemplateDetailID' => trim($this->input->post('companyReportTemplateDetailID'))));
        $status=$this->db->delete('srp_erp_companyreporttemplatedetails', array('companyReportTemplateDetailID' => trim($this->input->post('companyReportTemplateDetailID'))));
        if($status){
            echo json_encode(array('s', ' Deleted Successfully.', $status));
        }else {
            echo json_encode(array('e', ' Error in Deletion.', $status));
        }
    }

    function saveRepoetTempaleLink()
    {

        $this->form_validation->set_rules('glAutoID', 'GL Description', 'trim|required');
        $this->form_validation->set_rules('sortOrderLink', 'Sort Order', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_template_model->saveRepoetTempaleLink());
        }
    }

    function fetch_company_report_template_links_table(){
        $companyID=$this->common_data['company_data']['company_id'];
        $companyReportTemplateDetailID=$this->input->post('companyReportTemplateDetailID');
        $this->datatables->select('linkID,companyReportTemplateDetailID,sortOrder,srp_erp_companyreporttemplatelinks.glAutoID,srp_erp_chartofaccounts.GLDescription as GLDescription')
            ->where('srp_erp_companyreporttemplatelinks.companyID', $companyID)
            ->where('companyReportTemplateDetailID', $companyReportTemplateDetailID)
            ->from('srp_erp_companyreporttemplatelinks')
            ->join('srp_erp_chartofaccounts', 'srp_erp_companyreporttemplatelinks.glAutoID = srp_erp_chartofaccounts.GLAutoID');
        $this->datatables->add_column('edit', '<a onclick="delete_report_tempalte_Link($1)"><span title="Delete" style="color:rgb(209, 91, 71);" rel="tooltip" class="glyphicon glyphicon-trash" ></span></a>', 'linkID');
        echo $this->datatables->generate();
    }

    function load_gl_drop(){
        $data['companyReportTemplateID'] = $this->input->post('companyReportTemplateID');
        $html = $this->load->view('system/ReportTemplate/ajax/ajax-erp_load_gldescription', $data, true);
        echo $html;
    }


    function delete_report_tempalte_link(){
        $status=$this->db->delete('srp_erp_companyreporttemplatelinks', array('linkID' => trim($this->input->post('linkID'))));
        if($status){
            echo json_encode(array('s', ' Deleted Successfully.', $status));
        }else {
            echo json_encode(array('e', ' Error in Deletion.', $status));
        }
    }






}
