<?php

class Report_template_model extends ERP_Model
{

    function saveRepoetTempaleMaster()
    {
        $this->db->trans_start();
        $companyid = $this->common_data['company_data']['company_id'];

        $data['description'] = trim($this->input->post('description'));
        $data['reportID'] = trim($this->input->post('reportID'));
        $data['companyID'] = trim($companyid);
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];


        $this->db->insert('srp_erp_companyreporttemplate', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return array('e', 'Template Save Failed');
        } else {
            return array('s', 'Template Saved Successfully');
        }
    }

    function saveRepoetTempaleDetail()
    {
        $this->db->trans_start();
        $companyid = $this->common_data['company_data']['company_id'];

        $data['companyReportTemplateID'] = trim($this->input->post('companyReportTemplateID'));
        $data['description'] = trim($this->input->post('detaildescription'));
        $data['sortOrder'] = trim($this->input->post('sortOrder'));
        $data['companyID'] = trim($companyid);
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];


        $this->db->insert('srp_erp_companyreporttemplatedetails', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return array('e', 'Template Save Failed');
        } else {
            return array('s', 'Template Saved Successfully');
        }
    }

    function saveRepoetTempaleLink()
    {
        $this->db->trans_start();
        $companyid = $this->common_data['company_data']['company_id'];

        $data['companyReportTemplateDetailID'] = trim($this->input->post('companyReportTemplateDetailID'));
        $data['glAutoID'] = trim($this->input->post('glAutoID'));
        $data['sortOrder'] = trim($this->input->post('sortOrderLink'));
        $data['companyID'] = trim($companyid);
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];


        $this->db->insert('srp_erp_companyreporttemplatelinks', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return array('e', 'Template Save Failed');
        } else {
            return array('s', 'Template Saved Successfully');
        }
    }


}