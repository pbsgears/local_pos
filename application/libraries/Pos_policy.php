<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class pos_policy
{
    private $ci;
    private $outletID;
    private $companyID;

    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->library('session');
        $this->ci->load->database();

    }

    function isOutletTaxEnabled(){
        $this->outletID = get_outletID();
        $this->companyID = current_companyID();

        $this->ci->db->select('*');
        $this->ci->db->from('srp_erp_pos_policydetail');
        $this->ci->db->where('posPolicyMasterID', 20);
        $this->ci->db->where('outletID', $this->outletID);
        $this->ci->db->where('companyID', $this->companyID);
        $this->ci->db->limit(1);
        $policyID = $this->ci->db->get()->row('posPolicyID');
        $policyID = !empty($policyID) ? true : false;
        return $policyID;
    }

    function isPriceRequired()
    {
        /**
         * Table srp_erp_pos_policymaster :
         * Policy master ID : 2
         * Policy Code : PRICE
         * Policy Description : is Price Required
         */

        $this->outletID = get_outletID();
        $this->companyID = current_companyID();
        $this->ci->db->select('*');
        $this->ci->db->from('srp_erp_pos_policydetail');
        $this->ci->db->where('posPolicyMasterID', 2);
        $this->ci->db->where('outletID', $this->outletID);
        $this->ci->db->where('companyID', $this->companyID);
        $this->ci->db->limit(1);
        $policyID = $this->ci->db->get()->row('posPolicyID');
        $policyID = !empty($policyID) ? true : false;
        return $policyID;
    }

    function isSampleBillRequired()
    {
        /**
         * Table srp_erp_pos_policymaster :
         * Policy master ID : 3
         * Policy Code : PRINT_SMPL
         * Policy Description : is Sample Bill Required
         */
        $this->outletID = get_outletID();
        $this->companyID = current_companyID();

        $this->ci->db->select('*');
        $this->ci->db->from('srp_erp_pos_policydetail');
        $this->ci->db->where('posPolicyMasterID', 3);
        $this->ci->db->where('outletID', $this->outletID);
        $this->ci->db->where('companyID', $this->companyID);
        $this->ci->db->limit(1);
        $policyID = $this->ci->db->get()->row('posPolicyID');
        $policyID = !empty($policyID) ? true : false;
        return $policyID;
    }

    function isHidePrintPreview(){
        $this->outletID = get_outletID();
        $this->companyID = current_companyID();
        $this->ci->db->select('*');
        $this->ci->db->from('srp_erp_pos_policydetail');
        $this->ci->db->where('posPolicyMasterID', 19);
        $this->ci->db->where('outletID', $this->outletID);
        $this->ci->db->where('companyID', $this->companyID);
        $this->ci->db->limit(1);
        $policyID = $this->ci->db->get()->row('posPolicyID');
        $policyID = !empty($policyID) ? true : false;
        return $policyID;
    }

    function isCompanyEmail()
    {
        /**
         * Table srp_erp_pos_policymaster :
         * Policy master ID : 3
         * Policy Code : PRINT_SMPL
         * Policy Description : is Sample Bill Required
         */
        $this->outletID = get_outletID();
        $this->companyID = current_companyID();

        $this->ci->db->select('*');
        $this->ci->db->from('srp_erp_pos_policydetail');
        $this->ci->db->where('posPolicyMasterID', 4);
        $this->ci->db->where('outletID', $this->outletID);
        $this->ci->db->where('companyID', $this->companyID);
        $this->ci->db->limit(1);
        $policyID = $this->ci->db->get()->row('posPolicyID');
        $policyID = !empty($policyID) ? true : false;
        return $policyID;
    }

    function is_show_KOT_button()
    {
        /**
         * Table srp_erp_pos_policymaster :
         * Policy master ID : 5
         * Policy Code : KOT_BTN_HIDE
         * Policy Description : Hide KOT button in POS
         */
        $this->outletID = get_outletID();
        $this->companyID = current_companyID();

        $this->ci->db->select('*');
        $this->ci->db->from('srp_erp_pos_policydetail');
        $this->ci->db->where('posPolicyMasterID', 5);
        $this->ci->db->where('outletID', $this->outletID);
        $this->ci->db->where('companyID', $this->companyID);
        $this->ci->db->limit(1);
        $policyID = $this->ci->db->get()->row('posPolicyID');
        $policyID = !empty($policyID) ? true : false;
        return $policyID;
    }

    function get_policy()
    {
        $outletID = $this->ci->input->post('outletID');
        $companyID = current_companyID();
        $q = "SELECT
                    *
                FROM
                    srp_erp_pos_policymaster pm
                    LEFT JOIN (
                SELECT pd.posPolicyID, pd.posPolicyMasterID as masterID, pd.outletID, pd.companyID, w.wareHouseDescription FROM  srp_erp_pos_policydetail pd
                    LEFT JOIN srp_erp_warehousemaster w ON w.wareHouseAutoID = pd.outletID
                WHERE
                    pd.companyID = '" . $companyID . "' 
                    AND pd.outletID = '" . $outletID . "' 
                    ) pd ON pd.masterID = pm.posPolicyMasterID";
        return $this->ci->db->query($q)->result_array();
    }


}