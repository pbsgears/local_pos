<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('policy_water_mark_status')) {
    function policy_water_mark_status($code='All')
    {
        $CI =& get_instance();
        return ($CI->common_data['company_policy']['WM'][$code][0]["policyvalue"] ? $CI->common_data['company_policy']['WM'][$code][0]["policyvalue"] : $CI->common_data['company_policy']['WM']['All'][0]["policyvalue"] );
    }
}

if (!function_exists('policy_allow_to_change_po_cost_in_grv')) {
    function policy_allow_to_change_po_cost_in_grv()
    {
    	$status = 0;
        $CI =& get_instance();
        if(isset($CI->common_data['company_policy']['CPG']['GRV'][0]["policyvalue"])){
            $status = $CI->common_data['company_policy']['CPG']['GRV'][0]["policyvalue"]; 
        }
        return $status;
    }
}