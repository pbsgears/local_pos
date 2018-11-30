<?php

class srp_warehousemasterModel extends ERP_Model
{

    function save_warehousemaster()
    {
        if (empty($this->input->post('warehouseredit'))) {
            $companyID = current_companyID();
            $warehousecode = $this->input->post('warehousecode');
            $Warehouse = $this->db->query("SELECT wareHouseAutoID FROM srp_erp_warehousemaster where companyID = {$companyID} AND wareHouseCode = '{$warehousecode}'")->row_array();
            if ($Warehouse) {
                $this->session->set_flashdata('e', 'Warehouse already created !');
                return false;
            } else {
                //$this->db->set('companyCode', (($this->input->post('companyid') != "")) ? $this->input->post('companyid') : NULL);
                $this->db->set('wareHouseCode', (($this->input->post('warehousecode') != "")) ? $this->input->post('warehousecode') : NULL);
                $this->db->set('wareHouseDescription', (($this->input->post('warehousedescription') != "")) ? $this->input->post('warehousedescription') : NULL);
                $this->db->set('wareHouseLocation', (($this->input->post('warehouselocation') != "")) ? $this->input->post('warehouselocation') : NULL);
                $this->db->set('warehouseAddress', (($this->input->post('warehouseAddress') != "")) ? $this->input->post('warehouseAddress') : NULL);
                $this->db->set('warehouseTel', (($this->input->post('warehouseTel') != "")) ? $this->input->post('warehouseTel') : NULL);
                $this->db->set('WIPGLAutoID', (($this->input->post('glcodeid') != "")) ? $this->input->post('glcodeid') : NULL);
                $this->db->set('warehouseType', (trim(($this->input->post('ismanufacturingHN') != ""))) ? trim($this->input->post('ismanufacturingHN')) : NULL);
  //   $this->db->set('isPosLocation', (($this->input->post('isPosLocation') != "")) ? $this->input->post('isPosLocation') : NULL);
                $this->db->set('createdUserGroup', ($this->common_data['user_group']));
                $this->db->set('createdPCID', ($this->common_data['current_pc']));
                $this->db->set('createdUserID', ($this->common_data['current_userID']));
                $this->db->set('createdDateTime', ($this->common_data['current_date']));
                $this->db->set('createdUserName', ($this->common_data['current_user']));
                $this->db->set('companyID', ($this->common_data['company_data']['company_id']));
                $this->db->set('companyCode', ($this->common_data['company_data']['company_code']));
                $result = $this->db->insert('srp_erp_warehousemaster');
                if ($result) {
                    $this->session->set_flashdata('s', 'Warehouse Added Successfully');
                    return true;
                }
            }
        } else {

            $companyID = current_companyID();
            $warehousecode = $this->input->post('warehousecode');
            $warehouseredit = $this->input->post('warehouseredit');
            $Warehouse = $this->db->query("SELECT wareHouseAutoID FROM srp_erp_warehousemaster where companyID = {$companyID} AND wareHouseCode = '{$warehousecode}' AND wareHouseAutoID !=  $warehouseredit ")->row_array();
            // echo $this->db->last_query();
            if ($Warehouse) {
                $this->session->set_flashdata('e', 'Warehouse already created !');
                return false;
            } else {
                $data['wareHouseCode'] = ((($this->input->post('warehousecode') != "")) ? $this->input->post('warehousecode') : NULL);
                $data['wareHouseDescription'] = ((($this->input->post('warehousedescription') != "")) ? $this->input->post('warehousedescription') : NULL);
                $data['wareHouseLocation'] = ((($this->input->post('warehouselocation') != "")) ? $this->input->post('warehouselocation') : NULL);
                $data['warehouseAddress'] = ((($this->input->post('warehouseAddress') != "")) ? $this->input->post('warehouseAddress') : NULL);
                $data['warehouseTel'] = ((($this->input->post('warehouseTel') != "")) ? $this->input->post('warehouseTel') : NULL);
                $this->db->set('WIPGLAutoID', (($this->input->post('glcodeid') != "")) ? $this->input->post('glcodeid') : NULL);
                $this->db->set('warehouseType', (trim(($this->input->post('ismanufacturingHN') != ""))) ? trim($this->input->post('ismanufacturingHN')) : NULL);
                // $data['isPosLocation'] = ((($this->input->post('isPosLocation') != "")) ? $this->input->post('isPosLocation') : NULL);
                $data['modifiedPCID'] = ($this->common_data['current_pc']);
                $data['modifiedUserID'] = ($this->common_data['current_userID']);
                $data['modifiedDateTime'] = ($this->common_data['current_date']);
                $data['modifiedUserName'] = ($this->common_data['current_user']);


                $this->db->where('wareHouseAutoID', $this->input->post('warehouseredit'));
                $result = $this->db->update('srp_erp_warehousemaster', $data);
                if ($result) {
                    $this->session->set_flashdata('s', 'Records Updated Successfully');
                    return true;
                }
            }
        }
    }

    function get_warehouse()
    {
        $this->db->select('*');
        $this->db->where('wareHouseAutoID', $this->input->post('id'));
        $result1 = $this->db->get('srp_erp_warehousemaster')->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_pos_outlettemplatedetail');
        $this->db->where('companyID', $result1['companyID']);
        $this->db->where('outletID', $result1['wareHouseAutoID']);
        $tmpResult = $this->db->get()->row_array();

        $tmpArray['outletTemplateMasterID'] = !empty($tmpResult['outletTemplateMasterID']) ? $tmpResult['outletTemplateMasterID'] : 1;

        return array_merge($result1, $tmpArray);
    }

    function setDefaultWarehouse()
    {
        $isDefault = $this->input->post('chkdVal');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $data['isDefault'] = 0;
        $this->db->where('companyID', current_companyID());
        $result = $this->db->update('srp_erp_warehousemaster', $data);
        if ($result) {
            $data['isDefault'] = $isDefault;
            $this->db->where('wareHouseAutoID', $wareHouseAutoID);
            $results = $this->db->update('srp_erp_warehousemaster', $data);
            if ($results) {
                return array('s', 'successfully updated');
            }
        }
    }

    function save_bin_location(){
        $description=$this->input->post('Description');
        $wareHouseAutoID=$this->input->post('wareHouseAutoID');
        $binLocationID=$this->input->post('binLocationID');
        if($binLocationID){
            $this->db->select('binLocationID');
            $this->db->from('srp_erp_warehousebinlocation');
            $this->db->where('companyID', current_companyID());
            $this->db->where('binLocationID !=', $binLocationID);
            $this->db->where('wareHouseAutoID', $wareHouseAutoID);
            $this->db->where('Description ', $description);
            $descriptionexist = $this->db->get()->row_array();
            if($descriptionexist){
                return array('e', 'Bin location already exist');
            }else{
                $data['Description'] = $description;
                $this->db->where('binLocationID', $binLocationID);
                $results = $this->db->update('srp_erp_warehousebinlocation', $data);
                if ($results) {
                    return array('s', 'successfully updated');
                }
            }
        }else{
            $this->db->select('binLocationID');
            $this->db->from('srp_erp_warehousebinlocation');
            $this->db->where('companyID', current_companyID());
            $this->db->where('wareHouseAutoID', $wareHouseAutoID);
            $this->db->where('Description ', $description);
            $descriptionexist = $this->db->get()->row_array();
            if($descriptionexist){
                return array('e', 'Bin location already exist');
            }else{
                $this->db->set('Description', ($description));
                $this->db->set('wareHouseAutoID', ($wareHouseAutoID));
                $this->db->set('companyID', (current_companyID()));
                $this->db->set('createdUserGroup', ($this->common_data['user_group']));
                $this->db->set('createdPCID', ($this->common_data['current_pc']));
                $this->db->set('createdUserID', ($this->common_data['current_userID']));
                $this->db->set('createdDateTime', ($this->common_data['current_date']));
                $this->db->set('createdUserName', ($this->common_data['current_user']));
                $result = $this->db->insert('srp_erp_warehousebinlocation');
                if ($result) {
                    return array('s', 'successfully Saved');
                }
            }
        }
    }

    function delete_bin_location(){
        $this->db->select('itemBinlocationID');
        $this->db->where('binLocationID', $this->input->post('binLocationID'));
        $this->db->where('companyID', current_companyID());
        $result = $this->db->get('srp_erp_itembinlocation')->row_array();
        if($result){
            return array('w', 'Bin location has been assigned in item master');
        }else{
            $this->db->delete('srp_erp_warehousebinlocation', array('binLocationID' => trim($this->input->post('binLocationID'))));
            return array('s','Deleted Successfully');
        }
    }

    function saveAssignedItems(){
        $wareHouseAutoID=$this->input->post('wareHouseAutoID');
        $this->db->select('wareHouseDescription,wareHouseLocation');
        $this->db->where('wareHouseAutoID', $wareHouseAutoID);
        $this->db->where('companyID', current_companyID());
        $warehus = $this->db->get('srp_erp_warehousemaster')->row_array();
        $wareHouseLocation=$warehus['wareHouseLocation'];
        $wareHouseDescription=$warehus['wareHouseDescription'];

        $result = $this->db->query('INSERT INTO srp_erp_warehouseitems (
                                    wareHouseAutoID, wareHouseLocation, wareHouseDescription, itemAutoID,
                                    itemSystemCode, itemDescription, unitOfMeasureID,unitOfMeasure,currentStock, companyID,
                                    companyCode
                                ) SELECT
                                 '.$wareHouseAutoID.',"'.$wareHouseLocation.'", "'.$wareHouseDescription.'", itemAutoID, itemSystemCode,itemDescription,
                                 defaultUnitOfMeasureID,defaultUnitOfMeasure,0,companyID,companyCode
                                FROM
                                    srp_erp_itemmaster WHERE companyID = ' . $this->common_data['company_data']['company_id'] . ' AND itemAutoID IN(' . join(",", $this->input->post('itemAutoID')) . ')');
        if ($result) {
            return array('s','Records added Successfully');
        }
    }


}
