<?php

class Subcategory_model extends ERP_Model
{

    function header_update()
    {

        $this->db->select('description');
        $this->db->where('itemCategoryID', $this->input->post('editid'));
        $this->db->from('srp_erp_itemcategory');
        return $this->db->get()->row_array();
    }


    function save_sub_category()
    {
        if (!$this->input->post('subcatregoryedit')) {

            $itemCategory = $this->db->query("SELECT itemCategoryID,description,companyID FROM srp_erp_itemcategory WHERE companyID = " . $this->common_data['company_data']['company_id'] . " AND description = '" . $this->input->post('subcategory') . "'")->row_array();

            if ($itemCategory) {

                $this->session->set_flashdata('e', 'Sub Category already added ');
                return false;

            } else {

                $this->db->set('masterID', (($this->input->post('master') != "")) ? $this->input->post('master') : NULL);

                $this->db->set('description', (($this->input->post('subcategory') != "")) ? $this->input->post('subcategory') : NULL);
                $this->db->set('revenueGL', (($this->input->post('revnugl') != "")) ? $this->input->post('revnugl') : NULL);
                $this->db->set('costGL', (($this->input->post('costgl') != "")) ? $this->input->post('costgl') : NULL);
                $this->db->set('assetGL', (($this->input->post('assetgl') != "")) ? $this->input->post('assetgl') : NULL);
                $this->db->set('stockAdjustmentGL', (($this->input->post('stockadjust') != "")) ? $this->input->post('stockadjust') : NULL);
                $this->db->set('faCostGLAutoID', (($this->input->post('COSTGLCODEdes') != "")) ? $this->input->post('COSTGLCODEdes') : NULL);
                $this->db->set('faACCDEPGLAutoID', (($this->input->post('ACCDEPGLCODEdes') != "")) ? $this->input->post('ACCDEPGLCODEdes') : NULL);
                $this->db->set('faDEPGLAutoID', (($this->input->post('DEPGLCODEdes') != "")) ? $this->input->post('DEPGLCODEdes') : NULL);
                $this->db->set('faDISPOGLAutoID', (($this->input->post('DISPOGLCODEdes') != "")) ? $this->input->post('DISPOGLCODEdes') : NULL);
                $this->db->set('companyID', (($this->common_data['company_data']['company_id'] != "")) ? $this->common_data['company_data']['company_id'] : NULL);
                $this->db->set('companyCode', $this->common_data['company_data']['company_code']);
                $this->db->set('createdPCID', (($this->input->post('createdpcid') != "")) ? $this->input->post('createdpcid') : NULL);
                $this->db->set('createdUserID', (($this->input->post('createduserid') != "")) ? $this->input->post('createduserid') : NULL);
                $this->db->set('createdUserName', (($this->input->post('createdusername') != "")) ? $this->input->post('createdusername') : NULL);
                $this->db->set('createdDateTime', (($this->input->post('createddate') != "")) ? $this->input->post('createddate') : NULL);

                $result = $this->db->insert('srp_erp_itemcategory');
                if ($result) {
                    $this->session->set_flashdata('s', 'Sub Category Added Successfully');
                    return true;
                }
            }
        }
        /*else{

            $data['ClientID'] = $this->input->post('clientidedit');
            $data['Client'] = $this->input->post('clientdescription');
            $this->db->where('ClientID', $this->input->post('clientidedit'));
            $result = $this->db->update('clients', $data);
            if($result){
                $this->session->set_flashdata('s', 'Records Updated Successfully');
                return true;
            }
        }*/
    }

    function edit_itemsubcategory()
    {
        $this->db->select('*');
        $this->db->where('itemCategoryID', $this->input->post('id'));
        return $this->db->get('srp_erp_itemcategory')->row_array();
    }

    function update_itemsubcategory()
    {

        $data['description'] = ((($this->input->post('description') != "")) ? $this->input->post('description') : NULL);
        $data['revenueGL'] = ((($this->input->post('revnugledit') != "")) ? $this->input->post('revnugledit') : NULL);
        $data['costGL'] = ((($this->input->post('costgledit') != "")) ? $this->input->post('costgledit') : NULL);
        $data['assetGL'] = ((($this->input->post('assetgledit') != "")) ? $this->input->post('assetgledit') : NULL);
        $data['stockAdjustmentGL'] = ((($this->input->post('stockadjustedit') != "")) ? $this->input->post('stockadjustedit') : NULL);

        $this->db->where('itemCategoryID', $this->input->post('subcatregoryeditfrm'));
        $result = $this->db->update('srp_erp_itemcategory', $data);
        if ($result) {
            $this->session->set_flashdata('s', 'Data Updated Successfully');
            return true;
        }else{
            $this->session->set_flashdata('s', 'No changes found');
            return true;
        }
    }

    function save_sub_sub_category()
    {
        if (!$this->input->post('subsubedit')) {
            $this->db->set('masterID', (($this->input->post('subsubcategoryedit') != "")) ? $this->input->post('subsubcategoryedit') : NULL);

            $this->db->set('description', (($this->input->post('subsubcategory') != "")) ? $this->input->post('subsubcategory') : NULL);
            $this->db->set('revenueGL', (($this->input->post('rvgl') != "")) ? $this->input->post('rvgl') : NULL);
            $this->db->set('costGL', (($this->input->post('cstgl') != "")) ? $this->input->post('cstgl') : NULL);
            $this->db->set('assetGL', (($this->input->post('astgl') != "")) ? $this->input->post('astgl') : NULL);
            $this->db->set('companyID', (($this->common_data['company_data']['company_id'] != "")) ? $this->common_data['company_data']['company_id'] : NULL);
            $this->db->set('companyCode', $this->common_data['company_data']['company_code']);
            $result = $this->db->insert('srp_erp_itemcategory');
            if ($result) {
                $this->session->set_flashdata('s', 'Sub Sub Category Added Successfully');
                return true;
            }
        }
        /*else{

            $data['ClientID'] = $this->input->post('clientidedit');
            $data['Client'] = $this->input->post('clientdescription');
            $this->db->where('ClientID', $this->input->post('clientidedit'));
            $result = $this->db->update('clients', $data);
            if($result){
                $this->session->set_flashdata('s', 'Records Updated Successfully');
                return true;
            }
        }*/
    }

    function edit_itemsubsubcategory()
    {
        $this->db->select('*');
        $this->db->where('itemCategoryID', $this->input->post('id'));
        return $this->db->get('srp_erp_itemcategory')->row_array();
    }


    function update_subsubcategory()
    {

        $data['description'] = ((($this->input->post('descriptionsubsub') != "")) ? $this->input->post('descriptionsubsub') : NULL);

        $this->db->where('itemCategoryID', $this->input->post('subsubcatregoryeditfrm'));
        $result = $this->db->update('srp_erp_itemcategory', $data);
        if ($result) {
            return array('s', 'Data Updated Successfully');
        }
    }


}
