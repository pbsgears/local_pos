<?php
/**
 *
 * -- =============================================
 * -- File Name : Batch_process_model.php
 * -- Project Name : POS
 * -- Module Name : Batch Job
 * -- Author : Mohamed Shafri
 * -- Create date : 04 March 2017
 * -- Description : database transcation file for batch job
 *
 * --REVISION HISTORY
 * --
 * --
 * -- =============================================
 **/
defined('BASEPATH') OR exit('No direct script access allowed');

class Batch_process_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function updaet_batch_srp_erp_pos_menusalesitems($data)
    {
        $this->db->update_batch('srp_erp_pos_menusalesitems', $data, 'menuSalesItemID');
        $row = $this->db->affected_rows();
        return $row;
    }

}