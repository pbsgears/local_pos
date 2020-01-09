<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class company_language
{

    private $lang;
    private $defaultLanguage = 'english';

    public function __construct()
    {
        $this->lang =& get_instance();
        $this->lang->load->library('session');
        $this->lang->load->database();
    }

    function getPrimaryLanguage()
    {
        try {

            $this->lang->db->select('primarylanguageemp`.`systemDescription` AS `language');
            $this->lang->db->from('srp_employeesdetails');
            $this->lang->db->join('srp_erp_lang_languages as primarylanguageemp', 'primarylanguageemp.languageID = srp_employeesdetails.languageID', 'INNER');
            $this->lang->db->where('EIdNo', current_userID());
            $result_employee = $this->lang->db->get()->row_array();
            if (!empty($result_employee)) {
                return $result_employee['language'];
            } else {
                $this->lang->db->select('primary.systemDescription as language');
                $this->lang->db->from('srp_erp_lang_companylanguages');
                $this->lang->db->join('srp_erp_lang_languages as primary', 'primary.languageID = srp_erp_lang_companylanguages.primaryLanguageID', 'INNER');
                $this->lang->db->where('companyID', current_companyID());
                $result = $this->lang->db->get()->row_array();
                if (!empty($result)) {
                    return $result['language'];
                } else {
                    return $this->defaultLanguage;
                }
            }


        } catch (Exception $e) {

            echo "Caught Exception: " . $e->getMessage() . "\n  Line No." . __LINE__ . ' in ' . basename(__FILE__);
            exit;
        }


    }

    function getSecondaryLanguage()
    {
        try {

            $this->lang->db->select('Secondary.systemDescription as language');
            $this->lang->db->from('srp_erp_lang_companylanguages');
            $this->lang->db->join('srp_erp_lang_languages as Secondary', 'primary.languageID = srp_erp_lang_companylanguages.secondaryLanguageID', 'INNER');
            $this->lang->db->where('companyID', current_companyID());
            $result = $this->lang->db->get()->row_array();
            if (!empty($result)) {
                if (!empty($result['language'])) {
                    return $result['language'];
                } else {
                    return null;
                }

            } else {
                return null;
            }


        } catch (Exception $e) {

            echo "Caught Exception: " . $e->getMessage() . "\n  Line No." . __LINE__ . ' in ' . basename(__FILE__);
            exit;
        }


    }

}