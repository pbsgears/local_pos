<?php
class srp_warehouse_item_model extends ERP_Model
{
    function save_warehouseitem(){
        $out  = $this->input->post('itm');
        foreach ($out as $val) {
            $suppliercurrency = explode('_', $this->input->post('warehouselocation'));
            $query = $this->db->query("SELECT itemCodeSystem,itemCode,itemDescriptionshort,uom,mainCategoryID,subcategoryID FROM srp_mu_itemmaster where itemCodeSystem = ($val)");
            $output = $query->row_array();

            $this->db->set('itemSystemCode',$output['itemCodeSystem']);
            $this->db->set('itemPrimaryCode',$output['itemCode']);
            $this->db->set('itemDescription',$output['itemDescriptionshort']);
            $this->db->set('unitOfMeasure',$output['uom']);
            $this->db->set('financeCategoryMaster',$output['mainCategoryID']);
            $this->db->set('financeCategorySub',$output['subcategoryID']);
            $this->db->set('warehouseSystemCode', $suppliercurrency[0]);
            $this->db->set('wareHouseLocation', $suppliercurrency[1]);
            $result = $this->db->insert('srp_erp_warehouseitems');

         };
        if ($result) {
            $this->session->set_flashdata('s', 'Warehouse Item Added Successfully');
            return true;
        }

    }




}
