<?php defined('BASEPATH') OR exit('No direct script access allowed');

class TermsAndConditions extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Terms_and_condition_modal');
        $this->load->helpers('Terms_condition');
    }

    function fetch_terms_and_condition()
    {
        $status = $this->input->post('documentID');
        $status_filter = "";
        if ($status != 'all') {
            $status_filter = " AND documentID = '$status'";
        }

        $companyid = $this->common_data['company_data']['company_id'];
        $where = "companyID = " . $companyid . " $status_filter";
        $this->datatables->select("autoID,documentID,isDefault,description");
        $this->datatables->from('srp_erp_termsandconditions');
        $this->datatables->where($where);
        $this->datatables->add_column('docdescription', '$1', 'loadDescription(documentID)');
        $this->datatables->add_column('isDefaultChk', '$1', 'defaultCheckbox(autoID,isDefault)');
        $this->datatables->add_column('edit', '<span class="pull-right"><a onclick="openNoteEdit($1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a> &nbsp;|&nbsp; <a onclick="delete_notes($1);"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></span>', 'autoID');
        echo $this->datatables->generate();
    }

    function save_notes(){
        $this->form_validation->set_rules('documentID', 'Document ID', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e',validation_errors()));
        } else {
            echo json_encode($this->Terms_and_condition_modal->save_notes());
        }
    }

    function get_notes_edit(){
        echo json_encode($this->Terms_and_condition_modal->get_notes_edit());
    }


    function delete_notes(){
        echo json_encode($this->Terms_and_condition_modal->delete_notes());
    }

    function change_isDefault(){
        echo json_encode($this->Terms_and_condition_modal->change_isDefault());
    }


}