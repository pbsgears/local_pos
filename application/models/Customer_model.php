<?php
class Customer_model extends ERP_Model{

    function save_customer()
    {
        $this->db->trans_start();
        $isactive = 0;
        if (!empty($this->input->post('isActive'))) {
            $isactive = 1;
        }
        $liability = fetch_gl_account_desc(trim($this->input->post('receivableAccount')));
        $currency_code = explode('|', trim($this->input->post('currency_code')));
        $data['isActive'] = $isactive;
        $data['secondaryCode'] = trim($this->input->post('customercode'));
        $data['customerName'] = trim($this->input->post('customerName'));
        $data['customerCountry'] = trim($this->input->post('customercountry'));
        $data['customerTelephone'] = trim($this->input->post('customerTelephone'));
        $data['customerEmail'] = trim($this->input->post('customerEmail'));
        $data['customerUrl'] = trim($this->input->post('customerUrl'));
        $data['customerFax'] = trim($this->input->post('customerFax'));
        $data['customerAddress1'] = trim($this->input->post('customerAddress1'));
        $data['customerAddress2'] = trim($this->input->post('customerAddress2'));
        $data['taxGroupID'] = trim($this->input->post('customertaxgroup'));
        $data['vatIdNo'] = trim($this->input->post('vatIdNo'));
        $data['partyCategoryID'] = trim($this->input->post('partyCategoryID'));
        $data['receivableAutoID'] = $liability['GLAutoID'];
        $data['receivableSystemGLCode'] = $liability['systemAccountCode'];
        $data['receivableGLAccount'] = $liability['GLSecondaryCode'];
        $data['receivableDescription'] = $liability['GLDescription'];
        $data['receivableType'] = $liability['subCategory'];
        $data['customerCreditPeriod'] = trim($this->input->post('customerCreditPeriod'));
        $data['customerCreditLimit'] = trim($this->input->post('customerCreditLimit'));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('customerAutoID'))) {
            $this->db->where('customerAutoID', trim($this->input->post('customerAutoID')));
            $this->db->update('srp_erp_customermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Customer : ' . $data['customerName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Customer : ' . $data['customerName'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('customerAutoID'));
            }
        } else {
            $this->load->library('sequence');
            $data['customerCurrencyID'] = trim($this->input->post('customerCurrency'));
            $data['customerCurrency'] = $currency_code[0];
            $data['customerCurrencyDecimalPlaces'] = fetch_currency_desimal($data['customerCurrency']);
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['customerSystemCode'] = $this->sequence->sequence_generator('CUS');
            $this->db->insert('srp_erp_customermaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Customer : ' . $data['customerName'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Customer : ' . $data['customerName'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_sales_person_details(){
        $data = array();
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(datefrom,\''.$convertFormat.'\') AS datefrom,DATE_FORMAT(dateTo,\''.$convertFormat.'\') AS dateTo');
        $this->db->where('salesPersonID', $this->input->post('salesPersonID'));
        $data['detail'] = $this->db->get('srp_erp_salespersontarget')->result_array();
        return $data;
    }

    function load_customer_header()
    {
        $this->db->select('*');
        $this->db->where('customerAutoID', $this->input->post('customerAutoID'));
        return $this->db->get('srp_erp_customermaster')->row_array();
    }

    function laad_sale_person_header()
    {
        $this->db->select('*');
        $this->db->where('salesPersonID', $this->input->post('salesPersonID'));
        return $this->db->get('srp_erp_salespersonmaster')->row_array();
    }

    function delete_customer()
    {
        $this->db->where('customerAutoID', $this->input->post('customerAutoID'));
        $result = $this->db->delete('srp_erp_customermaster');
        $this->session->set_flashdata('s', 'Record Deleted Successfully');
        return true;
    }

    function delete_sales_person()
    {
        $this->db->where('salesPersonID', $this->input->post('salesPersonID'));
        $result = $this->db->delete('srp_erp_salespersonmaster');
        return array('status'=>1,'type'=>'s', 'message'=>'Record Deleted successfully');
    }

    function saveCategory()
    {
        if (empty($this->input->post('partyCategoryID'))) {
            $this->db->select('partyCategoryID');
            $this->db->where('categoryDescription', $this->input->post('categoryDescription'));
            $this->db->where('partyType', 1);
            $this->db->where('companyID', current_companyID());
            $category = $this->db->get('srp_erp_partycategories')->row_array();
            if (empty($category)) {
                $this->db->set('categoryDescription', $this->input->post('categoryDescription'));
                $this->db->set('partyType', 1);
                $this->db->set('companyID', current_companyID());
                $this->db->set('companyCode', current_companyCode());
                $this->db->set('createdUserGroup', current_user_group());
                $this->db->set('createdPCID', current_pc());
                $this->db->set('createdUserID', current_userID());
                $this->db->set('createdUserID', current_userID());
                $this->db->set('createdUserName', current_user());
                $this->db->set('createdDateTime', $this->common_data['current_date']);
                $result = $this->db->insert('srp_erp_partycategories');

                if ($result) {
                    return array('s', 'Record added successfully');
                } else {
                    return array('e', 'Error in adding Record');
                }
            } else {
                return array('e', 'Category Already Exist');
            }
        } else {
            $this->db->select('partyCategoryID');
            $this->db->where('categoryDescription', $this->input->post('categoryDescription'));
            $this->db->where('partyType', 1);
            $category = $this->db->get('srp_erp_partycategories')->row_array();
            if (empty($category)) {
                $data['categoryDescription'] = $this->input->post('categoryDescription');
                $data['modifiedPCID'] = current_pc();
                $data['modifiedUserID'] = current_userID();
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['modifiedUserName'] = current_user();

                $this->db->where('partyCategoryID', $this->input->post('partyCategoryID'));
                $result = $this->db->update('srp_erp_partycategories', $data);


                if ($result) {
                    return array('s', 'Record Updated successfully');
                } else {
                    return array('e', 'Error in Updating Record');
                }
            } else {
                return array('e', 'Category Already Exist');
            }
        }
    }

    function laad_sale_target()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(datefrom,\''.$convertFormat.'\') AS datefrom,DATE_FORMAT(dateTo,\''.$convertFormat.'\') AS dateTo');
        $this->db->where('targetID', $this->input->post('targetID'));
        return $this->db->get('srp_erp_salespersontarget')->row_array();
    }

    function fetch_employee_detail()
    {
        $this->db->select('*');
        $this->db->where('EIdNo', $this->input->post('employee_id'));
        return $this->db->get('srp_employeesdetails')->row_array();
    }

    function getCategory()
    {
        $this->db->select('*');
        $this->db->where('partyCategoryID', $this->input->post('partyCategoryID'));
        return $this->db->get('srp_erp_partycategories')->row_array();
    }

    function delete_category()
    {
        $this->db->where('partyCategoryID', $this->input->post('partyCategoryID'));
        $result = $this->db->delete('srp_erp_partycategories');
        if ($result) {
            return array('s', 'Record Deleted successfully');
        }
    }

    function delete_sales_target()
    {
        $this->db->where('targetID', $this->input->post('targetID'));
        $result = $this->db->delete('srp_erp_salespersontarget');
        return array('status'=>1,'type'=>'s', 'message'=>'Record Deleted successfully');
    }

    function fetch_template_data(){
        $data = array();
        $this->db->select('*');
        $this->db->where('salesPersonID', $this->input->post('salesPersonID'));
        $data['head'] = $this->db->get('srp_erp_salespersonmaster')->row_array();
        $this->db->select('*');
        $this->db->where('salesPersonID', $this->input->post('salesPersonID'));
        $data['detail'] = $this->db->get('srp_erp_salespersontarget')->result_array();
        return $data;
    }

    function img_uplode(){
        $attachment_file                = $_FILES["img_file"];
        $info                           = new SplFileInfo($_FILES["img_file"]["name"]);
        $fileName = 'rep_'.trim($this->input->post('salesPersonID')).'.'.$info->getExtension();
        $output_dir = "images/sales_person/";
        if (!file_exists($output_dir)) {
            mkdir("images/sales_person/", 007);
        } 
        move_uploaded_file($_FILES["img_file"]["tmp_name"],$output_dir.$fileName);  
        $this->db->where('salesPersonID', trim($this->input->post('salesPersonID')));
        $this->db->update('srp_erp_salespersonmaster', array('salesPersonImage'=>$output_dir.$fileName)); 
        return array('status' => 1,'type' => 's','message' => 'image upload successfully');
    }

    //get sales target end amount
    function load_sales_target_endamount()
    {
        $this->db->select_max('toTargetAmount');
        $this->db->where('salesPersonID', $this->input->get('salesPersonID'));
        return $this->db->get('srp_erp_salespersontarget')->row_array();
    }

    function save_customer_percentage(){
        $updateArray = array();
        for($x = 0; $x < sizeof($this->input->post("customerAutoID")); $x++){
            $updateArray[] = array(
                'customerAutoID'=>$this->input->post("customerAutoID")[$x],
                'capAmount' => $this->input->post("capAmount")[$x],
                'finCompanyPercentage' => $this->input->post("finCompanyPercentage")[$x],
                'pvtCompanyPercentage' => $this->input->post("pvtCompanyPercentage")[$x],
            );
        }
        $this->db->trans_start();
        $this->db->update_batch('srp_erp_customermaster', $updateArray, 'customerAutoID');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e',"Percentage Update Failed");
        } else {
            $this->db->trans_commit();
            return array('s',"Percentage Updated Successfully");
        }
    }
}