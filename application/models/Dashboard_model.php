<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Dashboard_model extends CI_Model{
	function __contruct(){
		parent::__contruct();
	}

	function fetch_notifications(){
		$data = array();
		if($this->session->flashdata('s'))
			$data[] =array('t'=>'success','m'=>$this->session->flashdata('s'), 'h' => 'Success Message');

		if($this->session->flashdata('e'))
			$data[] =array('t'=>'error','m'=>$this->session->flashdata('e'), 'h' => 'Error Message');

		if($this->session->flashdata('w'))
			$data[] =array('t'=>'warning','m'=>$this->session->flashdata('w'), 'h' => 'Warning Message');

		if($this->session->flashdata('i'))
			$data[] =array('t'=>'info','m'=>$this->session->flashdata('i'), 'h' => 'Information Message');

		return $data;
	}

	// function fetch_previous_notifications(){
	// 	return $this->db->query('SELECT LOWER(CMAS_LOG_Type) AS "t", `CMAS_LOG_Description` AS "m", UNIX_TIMESTAMP(CMAS_LOG_Timestamp) AS "d" FROM (`CMAS_LOG`) WHERE `CMAS_LOG_User` LIKE ? ORDER BY `CMAS_LOG_Timestamp` DESC LIMIT 5',array($this->session->userdata('username') . "%"))->result_array();
	// }

	function fetch_related_uom(){
		$this->db->select('UnitID');
		$this->db->where('companyID',$this->common_data['company_data']['company_id']);
		$this->db->where('UnitShortCode', $this->input->post('short_code'));
		$UnitID = $this->db->get('srp_erp_unit_of_measure')->row('UnitID');

		$this->db->select('srp_erp_unit_of_measure.UnitID,UnitShortCode,UnitDes,conversion');
		$this->db->from('srp_erp_unitsconversion');
		$this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_unitsconversion.subUnitID');
		$this->db->where('masterUnitID',$UnitID);
		$this->db->where('srp_erp_unitsconversion.companyID',$this->common_data['company_data']['company_id']);
		$data = $this->db->get()->result_array();
		return $data;
	}

	function fetch_related_uom_id(){
		$this->db->select('srp_erp_unit_of_measure.UnitID,UnitShortCode,UnitDes,conversion');
		$this->db->from('srp_erp_unitsconversion');
		$this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_unitsconversion.subUnitID');
		$this->db->where('masterUnitID',$this->input->post('masterUnitID'));
		$this->db->where('srp_erp_unitsconversion.companyID',$this->common_data['company_data']['company_id']);
		return $this->db->get()->result_array();
	}

	function fetch_finance_year_period(){
		$convertFormat=convert_date_format_sql();
		$this->db->select('companyFinancePeriodID,companyFinanceYearID,DATE_FORMAT(dateFrom,\''.$convertFormat.'\') AS dateFrom,DATE_FORMAT(dateTo,\''.$convertFormat.'\') AS dateTo ');
		$this->db->from('srp_erp_companyfinanceperiod');
		$this->db->where('companyFinanceYearID',$this->input->post('companyFinanceYearID'));
		$this->db->where('isActive',1);
		//$this->db->where('isCurrent',1);
		$this->db->where('isClosed',0);
		return $this->db->get()->result_array();
    }

    function get_userInfo_password(){

        $this->db->select('isChangePassword');
        $this->db->from('srp_employeesdetails');
        $this->db->where('EIdNo', current_userID());
        $isChangePassword = $this->db->get()->row('isChangePassword');
        if($isChangePassword==1){
            return true;
        }else {
            return false;
        }
    }

	function convert_acoding_to_uom(){
		$this->db->select('conversion');
		$this->db->from('srp_erp_unitsconversion');
		$this->db->where('companyID', current_companyID());
		$this->db->where('masterUnitID', $this->input->post('defaultUOMID'));
		$this->db->where('subUnitID', $this->input->post('transactionuom'));
		$result = $this->db->get()->row_array();
		return $result;
	}
}
