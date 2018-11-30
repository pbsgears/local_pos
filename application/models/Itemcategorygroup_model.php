<?php
class Itemcategorygroup_model extends ERP_Model{

    function save_item_category(){
        $data['codePrefix']          = $this->input->post('codeprefix');
        $data['StartSerial']         = $this->input->post('startserial');
        $data['codeLength']          = $this->input->post('codelength');
        $data['description']         = $this->input->post('description');
        $data['itemType']            = $this->input->post('itemtype');
        $data['categoryTypeID']      = $this->input->post('categoryTypeID');
        $data['modifiedPCID']        = $this->common_data['current_pc'];
        $data['modifiedUserID']      = $this->common_data['current_userID'];
        $data['modifiedUserName']    = $this->common_data['current_user'];
        $data['modifiedDateTime']    = $this->common_data['current_date'];

        if($this->input->post('itemcategoryedit')){
            $this->db->where('itemCategoryID', $this->input->post('itemcategoryedit'));
            $this->db->update('srp_erp_itemcategory', $data);
            $this->session->set_flashdata('s', 'Item Category Updated Successfully');
            return true;
        }
        else{
            $data['companyID']                    = $this->common_data['company_data']['company_id'];
            $data['companyCode']                  = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup']             = $this->common_data['user_group'];
            $data['createdPCID']                  = $this->common_data['current_pc'];
            $data['createdUserID']                = $this->common_data['current_userID'];
            $data['createdUserName']              = $this->common_data['current_user'];
            $data['createdDateTime']              = $this->common_data['current_date'];
            $this->db->insert('srp_erp_itemcategory',$data);
            $this->session->set_flashdata('s', 'Item Category Created Successfully');
            return true;
        }
    }

    function edit_itemcategory()
    {
        $this->db->select('*');
        $this->db->where('itemCategoryID', $this->input->post('id'));
        return $this->db->get('srp_erp_itemcategory')->row_array();
    }

    function save_sub_category()
    {
        $companyid = current_companyID();
        /*$this->db->select('companyGroupID');
        $this->db->where('companyID', $companyid);
        $grp = $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid = $companyid;

        if (!$this->input->post('subcatregoryedit')) {

            $itemCategory = $this->db->query("SELECT itemCategoryID,description FROM srp_erp_groupitemcategory WHERE groupID = " . $grpid . " AND description = '" . $this->input->post('subcategory') . "'")->row_array();

            if ($itemCategory) {

                $this->session->set_flashdata('e', 'Sub Category already added ');
                return false;

            } else {


                $this->db->set('masterID', (($this->input->post('master') != "")) ? $this->input->post('master') : NULL);
                $this->db->set('groupID', $grpid);

                $this->db->set('description', (($this->input->post('subcategory') != "")) ? $this->input->post('subcategory') : NULL);
                /*$this->db->set('revenueGL', (($this->input->post('revnugl') != "")) ? $this->input->post('revnugl') : NULL);
                $this->db->set('costGL', (($this->input->post('costgl') != "")) ? $this->input->post('costgl') : NULL);
                $this->db->set('assetGL', (($this->input->post('assetgl') != "")) ? $this->input->post('assetgl') : NULL);
                $this->db->set('faCostGLAutoID', (($this->input->post('COSTGLCODEdes') != "")) ? $this->input->post('COSTGLCODEdes') : NULL);
                $this->db->set('faACCDEPGLAutoID', (($this->input->post('ACCDEPGLCODEdes') != "")) ? $this->input->post('ACCDEPGLCODEdes') : NULL);
                $this->db->set('faDEPGLAutoID', (($this->input->post('DEPGLCODEdes') != "")) ? $this->input->post('DEPGLCODEdes') : NULL);
                $this->db->set('faDISPOGLAutoID', (($this->input->post('DISPOGLCODEdes') != "")) ? $this->input->post('DISPOGLCODEdes') : NULL);*/
                /*$this->db->set('companyID', (($this->common_data['company_data']['company_id'] != "")) ? $this->common_data['company_data']['company_id'] : NULL);
                $this->db->set('companyCode', $this->common_data['company_data']['company_code']);*/
                $this->db->set('createdPCID', (($this->input->post('createdpcid') != "")) ? $this->input->post('createdpcid') : NULL);
                $this->db->set('createdUserID', (($this->input->post('createduserid') != "")) ? $this->input->post('createduserid') : NULL);
                $this->db->set('createdUserName', (($this->input->post('createdusername') != "")) ? $this->input->post('createdusername') : NULL);
                $this->db->set('createdDateTime', (($this->input->post('createddate') != "")) ? $this->input->post('createddate') : NULL);

                $result = $this->db->insert('srp_erp_groupitemcategory');
                if ($result) {
                    $this->session->set_flashdata('s', 'Sub Category Added Successfully');
                    return true;
                }
            }
        }
    }

    function edit_itemsubcategory()
    {
        $this->db->select('*');
        $this->db->where('itemCategoryID', $this->input->post('id'));
        return $this->db->get('srp_erp_groupitemcategory')->row_array();
    }

    function update_subsubcategory()
    {

        $data['description'] = ((($this->input->post('descriptionsubsub') != "")) ? $this->input->post('descriptionsubsub') : NULL);

        $this->db->where('itemCategoryID', $this->input->post('subsubcatregoryeditfrm'));
        $result = $this->db->update('srp_erp_groupitemcategory', $data);
        if ($result) {
            return array('s', 'Data Updated Successfully');
        }
    }

    function update_itemsubcategory()
    {

        $data['description'] = ((($this->input->post('description') != "")) ? $this->input->post('description') : NULL);
       /* $data['revenueGL'] = ((($this->input->post('revnugledit') != "")) ? $this->input->post('revnugledit') : NULL);
        $data['costGL'] = ((($this->input->post('costgledit') != "")) ? $this->input->post('costgledit') : NULL);
        $data['assetGL'] = ((($this->input->post('assetgledit') != "")) ? $this->input->post('assetgledit') : NULL);*/

        $this->db->where('itemCategoryID', $this->input->post('subcatregoryeditfrm'));
        $result = $this->db->update('srp_erp_groupitemcategory', $data);
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
        $companyid = current_companyID();
        /*$this->db->select('companyGroupID');
        $this->db->where('companyID', $companyid);
        $grp = $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid = $companyid;
        if (!$this->input->post('subsubedit')) {
            $this->db->set('masterID', (($this->input->post('subsubcategoryedit') != "")) ? $this->input->post('subsubcategoryedit') : NULL);
            $this->db->set('groupID', $grpid);

            $this->db->set('description', (($this->input->post('subsubcategory') != "")) ? $this->input->post('subsubcategory') : NULL);
            /*$this->db->set('revenueGL', (($this->input->post('rvgl') != "")) ? $this->input->post('rvgl') : NULL);
            $this->db->set('costGL', (($this->input->post('cstgl') != "")) ? $this->input->post('cstgl') : NULL);
            $this->db->set('assetGL', (($this->input->post('astgl') != "")) ? $this->input->post('astgl') : NULL);*/
            $result = $this->db->insert('srp_erp_groupitemcategory');
            if ($result) {
                $this->session->set_flashdata('s', 'Sub Sub Category Added Successfully');
                return true;
            }
        }
    }

    function edit_itemsubsubcategory()
    {
        $this->db->select('*');
        $this->db->where('itemCategoryID', $this->input->post('id'));
        return $this->db->get('srp_erp_groupitemcategory')->row_array();
    }

    function save_item_category_link()
    {
        $companyid = $this->input->post('companyIDgrp');
        $itemCategoryID = $this->input->post('itemCategoryID');
        $com = current_companyID();
        /*$this->db->select('companyGroupID');
        $this->db->where('companyID', $com);
        $grp = $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid = $com;
        $this->db->delete('srp_erp_groupitemcategorydetails', array('companyGroupID' => $grpid, 'groupItemCategoryID' => $this->input->post('itemCategoryIDhn')));
        foreach($companyid as $key => $val){
            $data['groupItemCategoryID'] = trim($this->input->post('itemCategoryIDhn'));
            $data['itemCategoryID'] = trim($itemCategoryID[$key]);
            $data['companyID'] = trim($val);
            $data['companyGroupID'] = $grpid;

            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $results = $this->db->insert('srp_erp_groupitemcategorydetails', $data);
        }

        if ($results) {
            return array('s', 'Item Category Link Saved Successfully');
        } else {
            return array('e', 'Item Category Link Save Failed');
        }
    }

    function delete_item_category_link()
    {
        $this->db->where('groupItemCategoryDetailID', $this->input->post('groupItemCategoryDetailID'));
        $result = $this->db->delete('srp_erp_groupitemcategorydetails');
        return array('s', 'Record Deleted Successfully');
    }

    function load_category_header()
    {
        $this->db->select('*');
        //$this->db->join('srp_erp_groupcustomerdetails', 'srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID');
        $this->db->where('itemCategoryID', $this->input->post('groupItemCategoryID'));
        return $this->db->get('srp_erp_groupitemcategory')->row_array();
    }
}