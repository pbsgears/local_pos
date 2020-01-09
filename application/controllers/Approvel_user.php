<?php
class Approvel_user extends ERP_Controller{

    function __construct(){
        parent::__construct();
        $this->load->model('Approvel_user_model');
        $this->load->helper('Employee');
    }

    function load_approvel_user(){
        $companyid = $this->common_data['company_data']['company_id'];
        $documentid = $this->input->post('documentID');
        $employeeID = $this->input->post('employeeID');
        $documentid_filter = '';
        $employeeID_filter = '';
        if (!empty($documentid)) {
            $documentid_arr = explode(',', $documentid);
            $whereIN = "( ".('"' . join('","', $documentid_arr) . '"'). " )";
            $documentid_filter = " AND srp_erp_approvalusers.documentID IN " . $whereIN;
        }
        if (!empty($employeeID)) {
            $employeeID = array($this->input->post('employeeID'));
            $whereIN = "( " . join("' , '", $employeeID) . " )";
            $employeeID_filter = " AND employeeID IN " . $whereIN;
        }
        $where = "isApprovalDocument = 1 AND companyID = " . $companyid . $documentid_filter . $employeeID_filter . "";
        $this->datatables->select('approvalUserID,levelNo,companyCode,srp_erp_approvalusers.documentID as documentID,srp_erp_approvalusers.document as document,employeeID,employeeName')
            ->where($where)
            ->from('srp_erp_approvalusers')
            ->join('srp_erp_documentcodes', 'srp_erp_approvalusers.documentID=srp_erp_documentcodes.documentID')
      /*  ->edit_column('documentID', '$1 - $2', 'documentID,document')*/
        ->edit_column('levelNo', '<center> Level No &nbsp;-&nbsp; $1 </center>', 'levelNo')
        ->edit_column('action', '<span class="pull-right"><a onclick="openapprovelusermodel($1); "><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>  &nbsp;&nbsp;|&nbsp;&nbsp;  <a onclick="deleteapproveluser($1,\'Approvel User\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'approvalUserID');
        echo $this->datatables->generate();
    }
    //<span class="pull-right" onclick="openapprovelusermodel($1)"><a  ><span class="glyphicon glyphicon-pencil" ></span></a></span>
    //->edit_column('delete', '<span class="pull-right" onclick="deleteapproveluser($1)"><a href="#" ><span class="glyphicon glyphicon-trash" style="color:red;"  rel="tooltip"></span></a></span>', 'approvalUserID');
    function save_approveluser(){
        $this->form_validation->set_rules('levelno', 'Level No', 'trim|required');
        $this->form_validation->set_rules('documentid', 'Document ID', 'trim|required');
        $this->form_validation->set_rules('employeeid', 'Employee ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {


            echo json_encode($this->Approvel_user_model->save_approveluser());
        }
    }

    function edit_approveluser(){
        if($this->input->post('id') !=""){
            echo json_encode($this->Approvel_user_model->edit_approveluser());
        }else{
            echo json_encode(FALSE);
        }
    }

    function delete_approveluser(){
        echo json_encode($this->Approvel_user_model->delete_approveluser());
    }

    function fetch_approval_user_modal(){
        echo json_encode($this->Approvel_user_model->fetch_approval_user_modal());
    }

    function fetch_reject_user_modal(){
        echo json_encode($this->Approvel_user_model->fetch_reject_user_modal());
    }

    function fetch_approval_referbackuser_user_modal(){
        echo json_encode($this->Approvel_user_model->fetch_approval_referbackuser_user_modal());
    }

    function fetch_approval_reject_user_modal(){
        echo json_encode($this->Approvel_user_model->fetch_approval_reject_user_modal());
    }

    function fetch_emploee_using_group(){
        if($this->input->post('id')!=0){
            echo json_encode($this->Approvel_user_model->fetch_emploee_using_group());
        }else{
            echo json_encode(all_employee_drop(FALSE,1));
        }
    }

    function fetch_approval_level(){
        echo json_encode($this->Approvel_user_model->fetch_approval_level());
    }

    function fetch_all_approval_users_modal(){
        echo json_encode($this->Approvel_user_model->fetch_all_approval_users_modal());
    }
}