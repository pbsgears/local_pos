<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Upload extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('upload_modal');
    }



    function do_upload($description = true)
    {

        if($description){
            $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'required');

        }

        if($this->form_validation->run()==FALSE){
            $this->session->set_flashdata($msgtype='e',validation_errors());
            echo json_encode(FALSE);
        }else{
            $this->db->trans_start();
            $fileName = generate_filename($this->input->post('documentID'),$this->input->post('documentSystemCode'));
            $config['upload_path']      = realpath(APPPATH . '../uploads');;
            $config['allowed_types']    = 'gif|jpg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg';
            $config['max_size']	        = '200000';
            $config['file_name']	    = $fileName;


            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload("document_file"))
            {
                $this->session->set_flashdata('e', 'Upload failed ' . $this->upload->display_errors());

            }
            else
            {
                $data1                          = $this->upload->data();
                $fileName                       = generate_filename($this->input->post('documentID'),$this->input->post('documentSystemCode'),$data1["file_ext"]);
                $data['companyCode']            = current_companyID();
                $data['documentID']             = trim($this->input->post('documentID'));
                $data['documentSystemCode']     = trim($this->input->post('documentSystemCode'));
                $data['attachmentDescription']  = trim($this->input->post('attachmentDescription'));
                $data['myFileName']             = trim($fileName);
                $data['fileType']               = $data1["file_ext"];
                $data['docExpirtyDate']         = null;
                $data['timeStamp']              = date('Y-m-d H:i:s');
                $this->db->insert('srp_erp_documentattachments', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Upload failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    echo json_encode(array('status' => false));
                } else {
                    $this->session->set_flashdata('s', 'Successfully '.$fileName.' uploaded.');
                    $this->db->trans_commit();
                    echo json_encode(array('status' => true));
                }
            }
        }
    }


    function load_attachment(){
        echo json_encode($this->upload_modal->load_attachment());
    }

    function delete_attachment(){
        echo json_encode($this->upload_modal->delete_attachment());
    }



}

?>