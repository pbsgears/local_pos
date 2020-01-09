<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Attachment extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function fetch_attachments()
    {
        $this->db->where('documentSystemCode', $this->input->post('documentSystemCode'));
        $this->db->where('documentID', $this->input->post('documentID'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get('srp_erp_documentattachments')->result_array();
        $confirmedYN=$this->input->post('confirmedYN');
        $result='';
        $x=1;
        if(!empty($data)){
            foreach($data as $val){
                $burl= base_url("attachments").'/'.$val['myFileName'];
                $type = '<i class="color fa fa-file-pdf-o" aria-hidden="true"></i>';
                if ($val['fileType'] == '.xlsx') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.xls') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.xlsxm') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.doc') {
                    $type = '<i class="color fa fa-file-word-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.docx') {
                    $type = '<i class="color fa fa-file-word-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.ppt') {
                    $type = '<i class="color fa fa-file-powerpoint-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.pptx') {
                    $type = '<i class="color fa fa-file-powerpoint-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.jpg') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.jpeg') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.gif') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.png') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.txt') {
                    $type = '<i class="color fa fa-file-text-o" aria-hidden="true"></i>';
                }
                $link=generate_encrypt_link_only($burl);
                if ($confirmedYN == 0 || $confirmedYN == 2 || $confirmedYN == 3) {
                    $result.='<tr id="'.$val['attachmentID'].'"><td>'.$x.'</td><td>'.$val['myFileName'].'</td><td>'.$val['attachmentDescription'].'</td><td class="text-center">'.$type.'</td><td class="text-center"><a target="_blank" href="'.$link.'" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; | &nbsp; <a onclick="delete_attachments('.$val['attachmentID'].',\''.$val['myFileName'].'\')"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></td></tr>';
                } else {
                    $result.='<tr id="'.$val['attachmentID'].'"><td>'.$x.'</td><td>'.$val['myFileName'].'</td><td>'.$val['attachmentDescription'].'</td><td class="text-center">'.$type.'</td><td class="text-center"><a target="_blank" href="'.$link.'" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp;</td></tr>';
                }

            }
        }else{
            $result='<tr class="danger"><td colspan="5" class="text-center">No Attachment Found</td></tr>';
        }
        echo json_encode($result);
    }

    function do_upload($description = true)
    {
        //$this->load->model('upload_modal');
        if ($description) {
            $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
            $this->form_validation->set_rules('documentSystemCode', 'documentSystemCode', 'trim|required');
            $this->form_validation->set_rules('document_name', 'document_name', 'trim|required');
            $this->form_validation->set_rules('documentID', 'documentID', 'trim|required');
        }
        //$this->form_validation->set_rules('document_file', 'File', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status'=>0,'type'=>'e','message'=>validation_errors()));
        } else {

            $this->db->trans_start();
            $this->db->select('companyID');
            $this->db->where('documentID', trim($this->input->post('documentID')));
            $num = $this->db->get('srp_erp_documentattachments')->result_array();
            $file_name = $this->input->post('documentID') . '_' . $this->input->post('documentSystemCode') . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar|msg';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload("document_file")) {
                echo json_encode(array('status'=>0,'type'=>'w','message'=>'Upload failed ' . $this->upload->display_errors()));
            } else {
                $upload_data = $this->upload->data();
                //$fileName                       = $file_name.'_'.$upload_data["file_ext"];
                $data['documentID'] = trim($this->input->post('documentID'));
                $data['documentSystemCode'] = trim($this->input->post('documentSystemCode'));
                $data['attachmentDescription'] = trim($this->input->post('attachmentDescription'));
                $data['myFileName'] = $file_name . $upload_data["file_ext"];
                $data['fileType'] = trim($upload_data["file_ext"]);
                $data['fileSize'] = trim($upload_data["file_size"]);
                $data['timestamp'] = date('Y-m-d H:i:s');
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_documentattachments', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('status'=>0,'type'=>'e','message'=>'Upload failed ' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('status'=>1,'type'=>'s','message'=>'Successfully ' . $file_name . ' uploaded.'));
                }
            }
        }
    }

    function delete_attachment()
    {
        $attachmentID=$this->input->post('attachmentID');
        $myFileName=$this->input->post('myFileName');
        $url= base_url("attachments");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH.$link))
        {
            echo json_encode(false);
        }
        else
        {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(true);
        }
    }

    function delete_attachments(){

        $attachmentID=$this->input->post('attachmentID');
        $myFileName=$this->input->post('myFileName');
        $url= base_url("attachments");
        $link = "$url/$myFileName";
        //$link = "../../attachments/".$myFileName ;
       /* print_r(UPLOAD_PATH.$link) ;
        exit;*/
        //unlink($link);

        if (!unlink(UPLOAD_PATH.$link))
        {
            echo json_encode(false);
        }
        else
        {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(true);
        }

    }
}

?>