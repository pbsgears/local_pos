<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class OperationNgo_model extends ERP_Model
{
    function __contruct()
    {
        parent::__contruct();
        $this->load->helper('operationNgo_helper');
    }

    function save_donor_header()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $contactMasterID = trim($this->input->post('contactID'));


        $this->db->select('phoneCountryCodePrimary,phonePrimary');
        $this->db->where('phoneCountryCodePrimary', trim($this->input->post('countryCodePrimary')));
        $this->db->where('phonePrimary', trim($this->input->post('phonePrimary')));
        $this->db->where('companyID', $companyID);
        if ($contactMasterID) {
            $this->db->where('contactID !=', $contactMasterID);
        }
        $this->db->from('srp_erp_ngo_donors');
        $recordExist = $this->db->get()->row_array();
        if (!empty($recordExist)) {
            return array('w', 'Primary Phone Number is already Exist.');
            exit();
        }

        $this->db->trans_start();
        $data['name'] = trim($this->input->post('name'));
        $data['email'] = trim($this->input->post('email'));
        $data['currencyID'] = trim($this->input->post('transactionCurrencyID'));
        $data['currencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['currencyID']);
        $data['phoneCountryCodePrimary'] = trim($this->input->post('countryCodePrimary'));
        $data['phoneAreaCodePrimary'] = trim($this->input->post('phoneAreaCodePrimary'));
        $data['phonePrimary'] = trim($this->input->post('phonePrimary'));
        $data['phoneCountryCodeSecondary'] = trim($this->input->post('countryCodeSecondary'));
        $data['phoneAreaCodeSecondary'] = trim($this->input->post('phoneAreaCodeSecondary'));
        $data['phoneSecondary'] = trim($this->input->post('phoneSecondary'));
        $data['fax'] = trim($this->input->post('fax'));
        $data['postalCode'] = trim($this->input->post('postalcode'));
        $data['city'] = trim($this->input->post('city'));
        $data['state'] = trim($this->input->post('state'));
        $data['website'] = trim($this->input->post('website'));
        $data['countryID'] = trim($this->input->post('countryID'));
        $data['address'] = trim($this->input->post('address'));

        if ($contactMasterID) {

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('contactID', trim($this->input->post('contactID')));
            $update = $this->db->update('srp_erp_ngo_donors', $data);
            if ($update) {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();

                    return array('e', 'Donor Update Failed ' /*. $this->db->_error_message()*/
                    );

                } else {
                    $this->db->trans_commit();

                    return array('s', 'Donor Updated Successfully.', $contactMasterID);
                }
            }
        } else {


            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_ngo_donors', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();

                return array('e', 'Donor Save Failed ' . $this->db->_error_message(), $last_id);
            } else {
                $this->db->trans_commit();

                return array('s', 'Donor Saved Successfully.');

            }
        }
    }

    function load_donor_header()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('*');
        $this->db->where('contactID', $this->input->post('contactID'));
        $this->db->from('srp_erp_ngo_donors');
        $data = $this->db->get()->row_array();

        return $data;

    }

    function fetch_stateMaster_name($stateID)
    {
        $this->db->select('shortCode');
        $this->db->where('stateID', $stateID);
        $this->db->from('srp_erp_statemaster');
        return $this->db->get()->row_array();

    }

    function delete_donor_master()
    {
        $contacID = trim($this->input->post('contactID'));
        $this->db->where('documentID', 1);
        $this->db->where('documentAutoID', $contacID);
        $this->db->delete('srp_erp_ngo_notes');

        $this->db->where('documentID', 1);
        $this->db->where('documentAutoID', $contacID);
        $this->db->delete('srp_erp_ngo_attachments');

        $this->db->delete('srp_erp_ngo_donors', array('contactID' => trim($this->input->post('contactID'))));

        return TRUE;
    }

    function add_ngo_donor_notes()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];

        $contactID = trim($this->input->post('contactID'));

        $data['documentID'] = 1;
        $data['documentAutoID'] = $contactID;
        $data['description'] = trim($this->input->post('description'));
        $data['companyID'] = $companyID;
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $this->db->insert('srp_erp_ngo_notes', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', 'Donor Note Save Failed ' . $this->db->_error_message(), $last_id);
        } else {
            $this->db->trans_commit();

            return array('s', 'Donor Note Added Successfully.');

        }
    }

    function donor_image_upload()
    {
        $this->db->trans_start();
        $output_dir = "uploads/NGO/donorsImage/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/NGO", 007);
            mkdir("uploads/NGO/donorsImage", 007);
        }
        $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'Donor_' . trim($this->input->post('contactID')) . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['contactImage'] = $fileName;

        $this->db->where('contactID', trim($this->input->post('contactID')));
        $this->db->update('srp_erp_ngo_donors', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', "Image Upload Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Image uploaded  Successfully.');
        }
    }

    function save_donor_project()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];
        $ngoProjectID = trim($this->input->post('ngoProjectID'));
        $company_code = $this->common_data['company_data']['company_code'];

        $data['projectName'] = trim($this->input->post('projectName'));
        $data['companyID'] = $companyID;
        $data['segmentID'] = $this->input->post('segmentID');
        $data['description'] = $this->input->post('description');
        $data['revenueGLAutoID'] = $this->input->post('revenueGLAutoID');

        if ($ngoProjectID != '') {
            $data['ngoProjectID'] = $ngoProjectID;
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $this->db->update('srp_erp_ngo_projects', $data, array('ngoProjectID' => $ngoProjectID));
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Project Update Failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Project Updated Successfully', $ngoProjectID);
            }

        } else {
            $serial = $this->db->query("select IF ( isnull(MAX(serialNo)), 1, (MAX(serialNo) + 1) ) AS serialNo FROM `srp_erp_ngo_projects` WHERE companyID={$companyID}")->row_array();
            $data['serialNo'] = $serial['serialNo'];
            $data['documentCode'] = 'PROJ';;
            $data['documentSystemCode'] = ($company_code . '/' . 'PROJ' . str_pad($data['serialNo'], 6, '0', STR_PAD_LEFT));
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_ngo_projects', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Project Saved Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Project Added Successfully.', $last_id);
            }
        }
    }

    function save_ngo_project_subcategory()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $companyID = $this->common_data['company_data']['company_id'];
        $ngoProjectID = trim($this->input->post('ngoProjectID'));
        $company_code = $this->common_data['company_data']['company_code'];

        $startDate = $this->input->post('startDate');
        $endDate = $this->input->post('endDate');
        $format_startDate = null;
        if (isset($startDate) && !empty($startDate)) {
            $format_startDate = input_format_date($startDate, $date_format_policy);
        }
        $format_endDate = null;
        if (isset($endDate) && !empty($endDate)) {
            $format_endDate = input_format_date($endDate, $date_format_policy);
        }

        $data['projectName'] = trim($this->input->post('projectName'));
        $data['description'] = trim($this->input->post('description'));
        $data['masterID'] = trim($this->input->post('masterID'));
        $data['levelNo'] = 1;
        $data['startDate'] = $format_startDate;
        $data['endDate'] = $format_endDate;
        $data['totalNumberofHouses'] = trim($this->input->post('totalNumberofHouses'));
        $data['floorArea'] = trim($this->input->post('floorArea'));
        $data['costofhouse'] = trim($this->input->post('costofhouse'));
        $data['additionalCost'] = trim($this->input->post('additionalCost'));
        $data['EstimatedDays'] = trim($this->input->post('EstimatedDays'));
        $data['contractorID'] = trim($this->input->post('contractorID'));

        if ($ngoProjectID != '') {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['modifiedUserName'] = $this->common_data['current_user'];

            $datas['proposalName'] = trim($this->input->post('description'));
            $datas['proposalTitle'] = trim($this->input->post('projectName'));
            $datas['projectID'] = trim($this->input->post('masterID'));
            $datas['startDate'] = $format_startDate;
            $datas['endDate'] = $format_endDate;
            $datas['totalNumberofHouses'] = trim($this->input->post('totalNumberofHouses'));
            $datas['floorArea'] = trim($this->input->post('floorArea'));
            $datas['costofhouse'] = trim($this->input->post('costofhouse'));
            $datas['additionalCost'] = trim($this->input->post('additionalCost'));
            $datas['EstimatedDays'] = trim($this->input->post('EstimatedDays'));
            $datas['contractorID'] = trim($this->input->post('contractorID'));
            $this->db->update('srp_erp_ngo_projectproposals', $datas, array('projectSubID' => $ngoProjectID));
            $this->db->update('srp_erp_ngo_projects', $data, array('ngoProjectID' => $ngoProjectID));
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Project Update Failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Project Updated Successfully', $ngoProjectID);
            }

        } else {
            $serial = $this->db->query("select IF ( isnull(MAX(serialNo)), 1, (MAX(serialNo) + 1) ) AS serialNo FROM `srp_erp_ngo_projects` WHERE companyID={$companyID}")->row_array();
            $data['companyID'] = $companyID;
            $data['serialNo'] = $serial['serialNo'];
            $data['documentCode'] = 'PROJ';;
            $data['documentSystemCode'] = ($company_code . '/' . 'PROJ' . str_pad($data['serialNo'], 6, '0', STR_PAD_LEFT));
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_ngo_projects', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Project Saved Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Project Added Successfully.', $last_id);
            }
        }
    }

    function delete_ngo_project()
    {
        $ngoProjectID = trim($this->input->post('ngoProjectID'));

        $this->db->where('ngoProjectID', $ngoProjectID);
        $this->db->delete('srp_erp_ngo_projects');


        return true;
    }

    function load_donor_project_data()
    {
        $ngoProjectID = trim($this->input->post('ngoProjectID'));
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(startDate,\'' . $convertFormat . '\') AS startDate,DATE_FORMAT(endDate,\'' . $convertFormat . '\') AS endDate');
        $this->db->from('srp_erp_ngo_projects');
        $this->db->where('ngoProjectID', $ngoProjectID);
        return $this->db->get()->row_array();
    }

    function save_commitments()
    {
        $this->db->trans_start();
        $companyID = $this->common_data['company_data']['company_id'];
        $company_code = $this->common_data['company_data']['company_code'];
        $commitmentAutoId = $this->input->post('commitmentAutoId');
        $date_format_policy = date_format_policy();
        $documentDate = $this->input->post('documentDate');
        $commitmentExpiryDate = $this->input->post('commitmentExpiryDate');
        $data['documentDate'] = input_format_date($documentDate, $date_format_policy);
        $data['commitmentExpiryDate'] = input_format_date($commitmentExpiryDate, $date_format_policy);
        $data['referenceNo'] = $this->input->post('referenceNo');
        $data['narration'] = $this->input->post('narration');

        if ($commitmentAutoId == '') {
            $serial = $this->db->query("select IF ( isnull(MAX(serialNo)), 1, (MAX(serialNo) + 1) ) AS serialNo FROM `srp_erp_ngo_commitmentmasters` WHERE companyID={$companyID}")->row_array();


            $curr = explode(' | ', $this->input->post('currency_code'));
            $transactionCurrency = $curr[0];
            $data['serialNo'] = $serial['serialNo'];
            $data['documentCode'] = 'CMT';;
            $data['documentSystemCode'] = ($company_code . '/' . 'CMT' . str_pad($data['serialNo'], 6,
                    '0', STR_PAD_LEFT));
            $data['donorsID'] = $this->input->post('donorsID');
            $data['transactionCurrencyID'] = $this->input->post('transactionCurrencyID');
            $data['transactionCurrency'] = $transactionCurrency;
            $data['transactionExchangeRate'] = 1;
            $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $default_currency = currency_conversionID($data['transactionCurrencyID'],
                $data['companyLocalCurrencyID']);
            $data['companyLocalExchangeRate'] = $default_currency['conversion'];
            $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];

            $reporting_currency = currency_conversionID($data['transactionCurrencyID'],
                $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

            $donor = $this->db->query("select * from srp_erp_ngo_donors WHERE contactID={$data['donorsID']}")->row_array();
            $donorcurrency = currency_conversionID($data['transactionCurrencyID'],
                $donor['currencyID']);
            $data['donorCurrencyID'] = $donor['currencyID'];
            $data['donorCurrencyDecimalPlaces'] = $donor['currencyDecimalPlaces'];
            $data['donorExchangeRate'] = $donorcurrency['conversion'];

            $data['companyID'] = $companyID;
            $data['companyCode'] = $company_code;


            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];

            $this->db->insert('srp_erp_ngo_commitmentmasters', $data);
            $last_id = $this->db->insert_id();

        } else {

            $donorsID = $this->input->post('donorsID');
            if (isset($donorsID)) {
                $curr = explode(' | ', $this->input->post('currency_code'));
                $transactionCurrency = $curr[0];
                $data['donorsID'] = $this->input->post('donorsID');
                $data['transactionCurrencyID'] = $this->input->post('transactionCurrencyID');
                $data['transactionCurrency'] = $transactionCurrency;
                $data['transactionExchangeRate'] = 1;
                $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
                $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                $default_currency = currency_conversionID($data['transactionCurrencyID'],
                    $data['companyLocalCurrencyID']);
                $data['companyLocalExchangeRate'] = $default_currency['conversion'];
                $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];

                $reporting_currency = currency_conversionID($data['transactionCurrencyID'],
                    $data['companyReportingCurrencyID']);
                $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

                $donor = $this->db->query("select * from srp_erp_ngo_donors WHERE contactID={$data['donorsID']}")->row_array();
                $donorcurrency = currency_conversionID($data['transactionCurrencyID'],
                    $donor['currencyID']);
                $data['donorCurrencyID'] = $donor['currencyID'];
                $data['donorCurrencyDecimalPlaces'] = $donor['currencyDecimalPlaces'];
                $data['donorExchangeRate'] = $donorcurrency['conversion'];
            }
            $last_id = $commitmentAutoId;
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['modifiedUserName'] = $this->common_data['current_user'];

            $this->db->update('srp_erp_ngo_commitmentmasters', $data, array('commitmentAutoId' => $commitmentAutoId));

        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', 'Donor Commitments Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Donor Commitments Added Successfully.', $last_id);

        }
    }

    function save_donor_item_detail()
    {

        $commitmentAutoId = $this->input->post('commitmentAutoId');
        $projectID = $this->input->post('projectID');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        /*   $wareHouse          = $this->input->post('wareHouse');*/
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $quantityRequested = $this->input->post('quantityRequested');
        $description = $this->input->post('description');
        $expiryDate = $this->input->post('expiryDate');
        $date_format_policy = date_format_policy();


        $master = $this->db->query("select * from srp_erp_ngo_commitmentmasters WHERE commitmentAutoId=$commitmentAutoId")->row_array();
        $this->db->trans_start();
        $ACA_ID = $this->common_data['controlaccounts']['ACA'];
        $ACA = fetch_gl_account_desc($ACA_ID);


        foreach ($itemAutoIDs as $key => $itemAutoID) {
            /* if (!$commitmentAutoId) {
               $this->db->select('dispatchAutoID,itemDescription,itemSystemCode');
               $this->db->from('srp_erp_buyback_dispatchnotedetails');
               $this->db->where('dispatchAutoID', $commitmentAutoId);
               $this->db->where('itemAutoID', $itemAutoID);
               $order_detail = $this->db->get()->row_array();
               if (!empty($order_detail)) {
                 return array('e', 'Dispatch Note Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                 exit;
               }
             }*/
            $item_data = fetch_item_data($itemAutoID);
            $uomEx = explode('|', $uom[$key]);


            $data['commitmentAutoId'] = $commitmentAutoId;
            $data['projectID'] = $projectID[$key];
            $data['type'] = 2;
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            $data['itemCategory'] = $item_data['mainCategory'];
            $data['expenseGLAutoID'] = $item_data['costGLAutoID'];
            $data['expenseGLCode'] = $item_data['costGLCode'];
            $data['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['expenseGLDescription'] = $item_data['costDescription'];
            $data['expenseGLType'] = $item_data['costType'];
            $data['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
            $data['revenueGLCode'] = $item_data['revanueGLCode'];
            $data['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
            $data['revenueGLDescription'] = $item_data['revanueDescription'];
            $data['revenueGLType'] = $item_data['revanueType'];
            $data['assetGLAutoID'] = $item_data['assteGLAutoID'];
            $data['assetGLCode'] = $item_data['assteGLCode'];
            $data['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
            $data['assetGLDescription'] = $item_data['assteDescription'];
            $data['assetGLType'] = $item_data['assteType'];
            $data['wareHouseAutoID'] = $wareHouseAutoID[$key];
            /*        $data['wareHouseCode']                = trim($wareHouse_location[0]);
                    $data['wareHouseLocation']            = trim($wareHouse_location[1]);
                    $data['wareHouseDescription']         = trim($wareHouse_location[2]);*/
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['unitOfMeasure'] = trim($uomEx[0]);
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['itemQty'] = $quantityRequested[$key];
            $data['description'] = $description[$key];
            $data['GLAutoID'] = NULL;
            $data['SystemGLCode'] = NULL;
            $data['GLCode'] = NULL;
            $data['GLDescription'] = NULL;
            $data['GLType'] = NULL;
            $data['commitmentExpiryDate'] = input_format_date($expiryDate[$key], $date_format_policy);
            $data['unittransactionAmount'] = round($estimatedAmount[$key],
                $master['transactionCurrencyDecimalPlaces']);
            $transactionAmount = ($data['unittransactionAmount']) * $quantityRequested[$key];
            $data['transactionAmount'] = $transactionAmount;
            $data['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $master['companyLocalExchangeRate']);
            $data['companyLocalAmount'] = ($data['transactionAmount'] / $master['companyLocalExchangeRate']);
            $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $master['companyReportingExchangeRate']);
            $data['companyReportingAmount'] = ($data['transactionAmount'] / $master['companyReportingExchangeRate']);
            $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            //  $emp_currency = currency_conversionID($transactionCurrencyID[$key], $empcurr['payCurrencyID']);
            $unitDonourAmount = $estimatedAmount[$key] / $master['donorExchangeRate'];
            $data['unitDonoursAmount'] = round($unitDonourAmount, $master['donorCurrencyDecimalPlaces']);
            $data['donorsAmount'] = $unitDonourAmount * $quantityRequested[$key];
            $data['donorsExchangeRate'] = $master['donorExchangeRate'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];

            $this->db->insert('srp_erp_ngo_commitmentdetails', $data);
            $last_id = $this->db->insert_id();


        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', 'Donor Commitment :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Donor Commitment :  Records Inserted Successfully.');
        }

    }

    function load_commitmentHeader()
    {
        $convertFormat = convert_date_format_sql();


        $commitmentAutoId = $this->input->post('commitmentAutoId');
        $data = $this->db->query("select narration,commitmentAutoId,DATE_FORMAT(documentDate,'{$convertFormat}') AS documentDate,  DATE_FORMAT(commitmentExpiryDate,'{$convertFormat}') AS commitmentExpiryDate,donorsID,transactionCurrencyID,referenceNo from srp_erp_ngo_commitmentmasters WHERE commitmentAutoId={$commitmentAutoId} ")->row_array();

        return $data;
    }

    function save_donor_cash_detail()
    {


        $this->db->trans_start();

        // $segment_gl         = $this->input->post('segment_gl');
        $commitmentAutoId = $this->input->post('commitmentAutoId');
        // $gl_code_des        = $this->input->post('gl_code_des');
        // $gl_auto_ids        = $this->input->post('gl_code');
        $amount = $this->input->post('amount');
        $description = $this->input->post('description');
        $projectID = $this->input->post('projectID');
        $expiryDate = $this->input->post('expiryDate');
        $date_format_policy = date_format_policy();
        $master = $this->db->query("select * from srp_erp_ngo_commitmentmasters WHERE commitmentAutoId=$commitmentAutoId")->row_array();
        foreach ($projectID as $key => $project) {
            $GL = $this->db->query("SELECT * FROM `srp_erp_ngo_projects` LEFT JOIN `srp_erp_chartofaccounts` on revenueGLAutoID=GLAutoID WHERE ngoProjectID = $projectID[$key]")->row_array();
            $data[$key]['type'] = 1;
            $data[$key]['commitmentAutoId'] = $commitmentAutoId;
            $data[$key]['GLAutoID'] = trim($GL['GLAutoID']);
            $data[$key]['SystemGLCode'] = trim($GL['systemAccountCode']);
            $data[$key]['GLCode'] = trim($GL['GLSecondaryCode']);
            $data[$key]['GLDescription'] = trim($GL['GLDescription']);
            $data[$key]['GLType'] = trim($GL['subCategory']);
            $data[$key]['projectID'] = $projectID[$key];
            $donorsAmount = $amount[$key] / $master['donorExchangeRate'];
            $data[$key]['donorsAmount'] = $donorsAmount;
            $data[$key]['donorsExchangeRate'] = $master['donorExchangeRate'];

            $data[$key]['commitmentExpiryDate'] = input_format_date($expiryDate[$key], $date_format_policy);
            $data[$key]['transactionAmount'] = trim($amount[$key]);
            $data[$key]['companyLocalAmount'] = ($data[$key]['transactionAmount'] / $master['companyLocalExchangeRate']);
            $data[$key]['companyReportingAmount'] = ($data[$key]['transactionAmount'] / $master['companyReportingExchangeRate']);
            $data[$key]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data[$key]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data[$key]['description'] = trim($description[$key]);
            $data[$key]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$key]['modifiedUserID'] = $this->common_data['current_userID'];
            $data[$key]['modifiedUserName'] = $this->common_data['current_user'];
            $data[$key]['modifiedDateTime'] = $this->common_data['current_date'];


            $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdUserName'] = $this->common_data['current_user'];
            $data[$key]['createdDateTime'] = $this->common_data['current_date'];


        }

        $this->db->insert_batch('srp_erp_ngo_commitmentdetails', $data);
        $last_id = 0;//$this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {

            $this->db->trans_rollback();

            return array('e', 'save failed');
        } else {

            $this->db->trans_commit();

            return array('s', 'Records inserted successfully');
        }

    }


    function update_commitment_itemDetail()
    {

        $commitmentAutoId = $this->input->post('commitmentAutoId');
        $commitmentDetailAutoID = $this->input->post('commitmentDetailAutoID');
        $projectID = $this->input->post('projectID');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        /* $wareHouse              = $this->input->post('wareHouse');*/
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $quantityRequested = $this->input->post('quantityRequested');
        $description = $this->input->post('description');
        $expiryDate = $this->input->post('expiryDate');
        $date_format_policy = date_format_policy();


        $master = $this->db->query("select * from srp_erp_ngo_commitmentmasters WHERE commitmentAutoId=$commitmentAutoId")->row_array();
        $this->db->trans_start();


        $item_data = fetch_item_data($itemAutoIDs);
        $uomEx = explode('|', $uom);
        /*   $wareHouse_location = explode(' | ', $wareHouse);*/


        $data['projectID'] = $projectID;
        $data['type'] = 2;
        $data['itemAutoID'] = $itemAutoIDs;
        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        $data['itemCategory'] = $item_data['mainCategory'];
        $data['expenseGLAutoID'] = $item_data['costGLAutoID'];
        $data['expenseGLCode'] = $item_data['costGLCode'];
        $data['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
        $data['expenseGLDescription'] = $item_data['costDescription'];
        $data['expenseGLType'] = $item_data['costType'];
        $data['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
        $data['revenueGLCode'] = $item_data['revanueGLCode'];
        $data['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
        $data['revenueGLDescription'] = $item_data['revanueDescription'];
        $data['revenueGLType'] = $item_data['revanueType'];
        $data['assetGLAutoID'] = $item_data['assteGLAutoID'];
        $data['assetGLCode'] = $item_data['assteGLCode'];
        $data['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
        $data['assetGLDescription'] = $item_data['assteDescription'];
        $data['assetGLType'] = $item_data['assteType'];
        /*      $data['wareHouseAutoID']              = $wareHouseAutoID;
              $data['wareHouseCode']                = trim($wareHouse_location[0]);
              $data['wareHouseLocation']            = trim($wareHouse_location[1]);
              $data['wareHouseDescription']         = trim($wareHouse_location[2]);*/
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['unitOfMeasure'] = trim($uomEx[0]);
        $data['unitOfMeasureID'] = $UnitOfMeasureID;
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['itemQty'] = $quantityRequested;
        $data['description'] = $description;
        $data['GLAutoID'] = NULL;
        $data['SystemGLCode'] = NULL;
        $data['GLCode'] = NULL;
        $data['GLDescription'] = NULL;
        $data['GLType'] = NULL;
        $data['commitmentExpiryDate'] = input_format_date($expiryDate, $date_format_policy);
        $data['unittransactionAmount'] = round($estimatedAmount, $master['transactionCurrencyDecimalPlaces']);
        $transactionAmount = ($data['unittransactionAmount']) * $quantityRequested;
        $data['transactionAmount'] = $transactionAmount;
        $data['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
        $data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $master['companyLocalExchangeRate']);
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $master['companyLocalExchangeRate']);
        $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $master['companyReportingExchangeRate']);
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $master['companyReportingExchangeRate']);
        $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
        $unitDonourAmount = $estimatedAmount / $master['donorExchangeRate'];
        $data['unitDonoursAmount'] = round($unitDonourAmount, $master['donorCurrencyDecimalPlaces']);
        $data['donorsAmount'] = $unitDonourAmount * $quantityRequested;
        $data['donorsExchangeRate'] = $master['donorExchangeRate'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['createdUserName'] = $this->common_data['current_user'];

        $this->db->update('srp_erp_ngo_commitmentdetails', $data,
            array('commitmentDetailAutoID' => $commitmentDetailAutoID));


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', 'Donor Collection :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Donor Collection :  Records Inserted Successfully.');
        }

    }

    function update_commitment_cash_details()
    {


        $this->db->trans_start();

        $segment_gl = $this->input->post('segment_gl');
        $commitmentAutoId = $this->input->post('commitmentAutoId');
        $commitmentDetailAutoID = $this->input->post('commitmentDetailAutoID');
        $gl_code_des = $this->input->post('gl_code_des');
        $gl_auto_ids = $this->input->post('gl_code');
        $amount = $this->input->post('amount');
        $description = $this->input->post('description');
        $projectID = $this->input->post('projectID');
        $expiryDate = $this->input->post('expiryDate');
        $date_format_policy = date_format_policy();
        $master = $this->db->query("select * from srp_erp_ngo_commitmentmasters WHERE commitmentAutoId=$commitmentAutoId")->row_array();

        $GL = $this->db->query("SELECT * FROM `srp_erp_ngo_projects` LEFT JOIN `srp_erp_chartofaccounts` on revenueGLAutoID=GLAutoID WHERE ngoProjectID = $projectID")->row_array();


        $data['type'] = 1;
        $data['GLAutoID'] = trim($GL['GLAutoID']);
        $data['SystemGLCode'] = trim($GL['systemAccountCode']);
        $data['GLCode'] = trim($GL['GLSecondaryCode']);
        $data['GLDescription'] = trim($GL['GLDescription']);
        $data['GLType'] = trim($GL['subCategory']);
        $data['projectID'] = $projectID;
        $donorsAmount = $amount / $master['donorExchangeRate'];
        $data['donorsAmount'] = $donorsAmount;
        $data['donorsExchangeRate'] = $master['donorExchangeRate'];

        $data['commitmentExpiryDate'] = input_format_date($expiryDate, $date_format_policy);
        $data['transactionAmount'] = trim($amount);
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $master['companyLocalExchangeRate']);
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $master['companyReportingExchangeRate']);
        $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
        $data['description'] = trim($description);
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];


        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];


        $this->db->update('srp_erp_ngo_commitmentdetails', $data,
            array('commitmentDetailAutoID' => $commitmentDetailAutoID));
        $last_id = 0;//$this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Cash Detail : update Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();

            return array('status' => FALSE);
        } else {
            $this->session->set_flashdata('s', 'Cash Detail : Records inserted successfully.');
            $this->db->trans_commit();

            return array('s', 'Cash Detail : Records inserted successfully. ');
        }

    }

    function delete_commitment_project()
    {
        $data = array('isDeleted' => 1, 'deletedDate' => $this->common_data['current_date'],
            'deletedEmpID' => $this->common_data['current_userID']);

        $commitmentAutoId = trim($this->input->post('commitmentAutoId'));

        $this->db->where('commitmentAutoId', $commitmentAutoId);
        $this->db->update('srp_erp_ngo_commitmentmasters', $data);

    }

    function donor_commitment_confirmation()
    {
        $commitmentAutoId = trim($this->input->post('commitmentAutoId'));

        $data = array('confirmedYN' => 1, 'confirmedDate' => $this->common_data['current_date'],
            'confirmedByEmpID' => $this->common_data['current_userID'],
            'confirmedByName' => $this->common_data['current_user']);

        $this->db->where('commitmentAutoId', $commitmentAutoId);
        $this->db->update('srp_erp_ngo_commitmentmasters', $data);

        return array('s', 'Confirmed Successfully. ');

    }

    function fetch_donor_commitment_confirmation($commitmentAutoId)
    {
        $convertFormat = convert_date_format_sql();
        $data['master'] = $this->db->query("select commitmentAutoId,confirmedByEmpID ,confirmedByName ,DATE_FORMAT(confirmedDate,'.$convertFormat. %h:%i:%s') AS confirmedDate, confirmedYN,transactionCurrencyDecimalPlaces,commitmentAutoId ,documentCode ,documentSystemCode ,serialNo , DATE_FORMAT(documentDate,'{$convertFormat}') AS documentDate ,DATE_FORMAT(commitmentExpiryDate,'{$convertFormat}') AS commitmentExpiryDate ,referenceNo,transactionCurrency ,name from srp_erp_ngo_commitmentmasters LEFT JOIN srp_erp_ngo_donors on srp_erp_ngo_commitmentmasters.donorsID=srp_erp_ngo_donors.contactID WHERE commitmentAutoId=$commitmentAutoId ")->row_array();
        $data['detail_item'] = $this->db->query("select commitmentDetailAutoID ,commitmentAutoId ,projectID ,type ,itemAutoID ,itemSystemCode ,itemDescription ,itemCategory ,wareHouseAutoID ,wareHouseCode ,wareHouseLocation ,wareHouseDescription ,defaultUOMID ,defaultUOM ,unitOfMeasureID ,unitOfMeasure ,conversionRateUOM ,itemQty ,srp_erp_ngo_commitmentdetails.description ,GLAutoID ,SystemGLCode ,GLCode ,GLDescription ,GLType ,DATE_FORMAT(commitmentExpiryDate,'{$convertFormat}') AS commitmentExpiryDate ,unittransactionAmount ,transactionAmount ,companyLocalWacAmount ,unitcompanyLocalAmount ,companyLocalAmount ,companyLocalExchangeRate ,unitcompanyReportingAmount ,companyReportingAmount ,companyReportingExchangeRate ,unitDonoursAmount ,donorsAmount ,donorsExchangeRate,projectName from `srp_erp_ngo_commitmentdetails` LEFT JOIN srp_erp_ngo_projects on ngoProjectID=projectID  WHERE commitmentAutoId=$commitmentAutoId AND type=2")->result_array();
        $data['detail_cash'] = $this->db->query("select commitmentDetailAutoID ,commitmentAutoId ,projectID ,type ,itemAutoID ,itemSystemCode ,itemDescription ,itemCategory ,expenseGLAutoID ,expenseGLCode ,expenseSystemGLCode ,expenseGLDescription ,expenseGLType  ,wareHouseAutoID ,wareHouseCode ,wareHouseLocation ,wareHouseDescription ,defaultUOMID ,defaultUOM ,unitOfMeasureID ,unitOfMeasure ,conversionRateUOM ,itemQty ,srp_erp_ngo_commitmentdetails.description ,GLAutoID ,SystemGLCode ,GLCode ,GLDescription ,GLType ,DATE_FORMAT(commitmentExpiryDate,'{$convertFormat}') AS commitmentExpiryDate ,unittransactionAmount ,transactionAmount ,companyLocalWacAmount ,unitcompanyLocalAmount ,companyLocalAmount ,companyLocalExchangeRate ,unitcompanyReportingAmount ,companyReportingAmount ,companyReportingExchangeRate ,unitDonoursAmount ,donorsAmount ,donorsExchangeRate,projectName from `srp_erp_ngo_commitmentdetails` LEFT JOIN srp_erp_ngo_projects on ngoProjectID=projectID WHERE commitmentAutoId=$commitmentAutoId AND type=1")->result_array();

        return $data;

    }

    function save_donorCollections()
    {
        $this->db->trans_start();
        $companyID = $this->common_data['company_data']['company_id'];
        $company_code = $this->common_data['company_data']['company_code'];
        $collectionAutoId = $this->input->post('collectionAutoId');
        $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear')));
        $date_format_policy = date_format_policy();
        $FYBegin = input_format_date($financeyr[0], $date_format_policy);
        $FYEnd = input_format_date($financeyr[1], $date_format_policy);
        $bank = explode('|', trim($this->input->post('bank')));

        $modeOfPayment = $this->input->post('modeOfPayment');
        $documentDate = $this->input->post('documentDate');
        $isIncre = 0;

        $data['documentDate'] = input_format_date($documentDate, $date_format_policy);
        $data['referenceNo'] = $this->input->post('referenceNo');
        $data['modeOfPayment'] = $modeOfPayment;
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear'));
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear'));
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period'));
        $data['narration'] = trim_desc($this->input->post('narration'));
        $bank_detail = fetch_gl_account_desc(trim($this->input->post('DCbankCode')));
        $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
        $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
        $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
        $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
        $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
        $data['DCbank'] = $bank_detail['bankName'];
        $data['DCbankBranch'] = $bank_detail['bankBranch'];
        $data['DCbankSwiftCode'] = $bank_detail['bankSwiftCode'];
        $data['DCbankAccount'] = $bank_detail['bankAccountNumber'];
        $data['DCbankType'] = $bank_detail['subCategory'];
        $data['DCbankCode'] = trim($this->input->post('DCbankCode'));
        $data['DCchequeDate'] = NULL;
        $data['DCchequeNo'] = NULL;
        if ($modeOfPayment == 2) {

            $DCchequeDate = $this->input->post('DCchequeDate');
            $data['DCchequeDate'] = input_format_date($DCchequeDate, $date_format_policy);
            $data['DCchequeNo'] = $this->input->post('DCchequeNo');
        }
        if ($collectionAutoId == '') {
            $serial = $this->db->query("select IF ( isnull(MAX(serialNo)), 1, (MAX(serialNo) + 1) ) AS serialNo FROM srp_erp_ngo_donorcollectionmaster WHERE companyID={$companyID}")->row_array();
            $curr = explode(' | ', $this->input->post('currency_code'));
            $transactionCurrency = $curr[0];
            $data['serialNo'] = $serial['serialNo'];
            $data['documentCode'] = 'DC';
            $data['documentSystemCode'] = ($company_code . '/' . 'DC' . str_pad($data['serialNo'], 6,
                    '0', STR_PAD_LEFT));
            $data['donorsID'] = $this->input->post('donorsID');
            $data['transactionCurrencyID'] = $this->input->post('transactionCurrencyID');
            $data['transactionCurrency'] = $transactionCurrency;
            $data['transactionExchangeRate'] = 1;
            $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $default_currency = currency_conversionID($data['transactionCurrencyID'],
                $data['companyLocalCurrencyID']);
            $data['companyLocalExchangeRate'] = $default_currency['conversion'];
            $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $reporting_currency = currency_conversionID($data['transactionCurrencyID'],
                $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $donor = $this->db->query("select * from srp_erp_ngo_donors WHERE contactID={$data['donorsID']}")->row_array();
            $donorcurrency = currency_conversionID($data['transactionCurrencyID'],
                $donor['currencyID']);
            $data['donorCurrencyID'] = $donor['currencyID'];
            $data['donorCurrencyDecimalPlaces'] = $donor['currencyDecimalPlaces'];
            $data['donorExchangeRate'] = $donorcurrency['conversion'];
            $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
            $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
            $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
            $data['companyID'] = $companyID;
            $data['companyCode'] = $company_code;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_ngo_donorcollectionmaster', $data);
            $last_id = $this->db->insert_id();
        } else {
            $donorsID = $this->input->post('donorsID');
            $transactioncurrencyid = $this->input->post('transactionCurrencyID');
            $details = $this->db->query("select * from srp_erp_ngo_donorcollectiondetails WHERE collectionAutoId=$collectionAutoId")->result_array();
            $masters = $this->db->query("select * from srp_erp_ngo_donorcollectionmaster WHERE collectionAutoId=$collectionAutoId")->row_array();
            if (!empty($details)) {
                if ($transactioncurrencyid != $masters['transactionCurrencyID']) {
                    $isIncre++;
                }
            }
            if (isset($donorsID)) {
                $curr = explode(' | ', $this->input->post('currency_code'));
                $transactionCurrency = $curr[0];
                $data['donorsID'] = $this->input->post('donorsID');
                $data['transactionCurrencyID'] = $this->input->post('transactionCurrencyID');
                $data['transactionCurrency'] = $transactionCurrency;
                $data['transactionExchangeRate'] = 1;
                $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
                $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                $default_currency = currency_conversionID($data['transactionCurrencyID'],
                    $data['companyLocalCurrencyID']);
                $data['companyLocalExchangeRate'] = $default_currency['conversion'];
                $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                $reporting_currency = currency_conversionID($data['transactionCurrencyID'],
                    $data['companyReportingCurrencyID']);
                $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                $donor = $this->db->query("select * from srp_erp_ngo_donors WHERE contactID={$data['donorsID']}")->row_array();
                $donorcurrency = currency_conversionID($data['transactionCurrencyID'],
                    $donor['currencyID']);
                $data['donorCurrencyID'] = $donor['currencyID'];
                $data['donorCurrencyDecimalPlaces'] = $donor['currencyDecimalPlaces'];
                $data['donorExchangeRate'] = $donorcurrency['conversion'];

                $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
                $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
                $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
            }
            $last_id = $collectionAutoId;
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['modifiedUserName'] = $this->common_data['current_user'];

            $this->db->update('srp_erp_ngo_donorcollectionmaster', $data, array('collectionAutoId' => $collectionAutoId));

            foreach ($details as $val) {
                {
                    $donorsAmount = $val['transactionAmount'] / $masters['donorExchangeRate'];
                    $data_cash_detail_up['donorsAmount'] = $donorsAmount;
                    $data_cash_detail_up['donorsExchangeRate'] = $masters['donorExchangeRate'];
                    //$data_cash_detail_up['transactionAmount'] = ($val['transactionAmount'] * $masters['donorExchangeRate']);
                    $data_cash_detail_up['companyLocalAmount'] = ($val['transactionAmount'] / $masters['companyLocalExchangeRate']);
                    $data_cash_detail_up['companyReportingAmount'] = ($val['transactionAmount'] / $masters['companyReportingExchangeRate']);
                    $data_cash_detail_up['companyLocalExchangeRate'] = $masters['companyLocalExchangeRate'];
                    $data_cash_detail_up['companyReportingExchangeRate'] = $masters['companyReportingExchangeRate'];
                    $data_cash_detail_up['modifiedPCID'] = $this->common_data['current_pc'];
                    $data_cash_detail_up['modifiedUserID'] = $this->common_data['current_userID'];
                    $data_cash_detail_up['modifiedUserName'] = $this->common_data['current_user'];
                    $data_cash_detail_up['modifiedDateTime'] = $this->common_data['current_date'];
                    $this->db->update('srp_erp_ngo_donorcollectiondetails', $data_cash_detail_up,
                        array('collectionDetailAutoID' => $val['collectionDetailAutoID']));
                }

            }


        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Donor Collection Save Failed ' . $this->db->_error_message());
        } else {
            if ($isIncre > 0) {
                $this->db->trans_rollback();
                return array('e', 'The transaction Currency is different,please delete the existing records');
            } else {
                $this->db->trans_commit();
                return array('s', 'Donor Collection :  Records inserted successfully.', $last_id);
            }
        }

    }

    function fetch_donor_collections_confirmation($collectionAutoId)
    {
        $data['master'] = $this->db->query("select srp_erp_ngo_commitmentmasters.*,name from srp_erp_ngo_donorcollectionmaster LEFT JOIN srp_erp_ngo_donors on srp_erp_ngo_commitmentmasters.donorsID=srp_erp_ngo_donors.contactID WHERE collectionAutoId=$collectionAutoId ")->row_array();
        $data['detail_item'] = $this->db->query("select * from `srp_erp_ngo_commitmentdetails`  WHERE collectionAutoId=$collectionAutoId AND type=2")->result_array();
        $data['detail_cash'] = $this->db->query("select * from `srp_erp_ngo_commitmentdetails`  WHERE collectionAutoId=$collectionAutoId AND type=1")->result_array();

        return $data;

    }

    function load_collectionHeader()
    {
        $convertFormat = convert_date_format_sql();
        $collectionAutoId = $this->input->post('collectionAutoId');
        $data = $this->db->query("select        companyFinanceYearID,companyFinancePeriodID,narration,DCbankCode,modeOfPayment,DATE_FORMAT(DCchequeDate,'{$convertFormat}') AS DCchequeDate,DCchequeNo,collectionAutoId,DATE_FORMAT(documentDate,'{$convertFormat}') AS documentDate,donorsID,transactionCurrencyID,referenceNo from srp_erp_ngo_donorcollectionmaster WHERE collectionAutoId={$collectionAutoId} ")->row_array();

        return $data;
    }

    function fetch_donor_collection_confirmation($collectionAutoId)
    {
        $convertFormat = convert_date_format_sql();
        $data['master'] = $this->db->query("select DCbank,DCbankBranch,DCchequeNo,approvedbyEmpName,approvedYN,confirmedByEmpID ,confirmedByName ,DATE_FORMAT(confirmedDate,'{$convertFormat}') AS confirmedDate, confirmedYN,transactionCurrencyDecimalPlaces,collectionAutoId ,documentCode ,documentSystemCode ,serialNo , DATE_FORMAT(documentDate,'{$convertFormat}') AS documentDate , DATE_FORMAT(approvedDate,'.$convertFormat. %h:%i:%s') AS approvedDate,referenceNo,transactionCurrency ,name from srp_erp_ngo_donorcollectionmaster LEFT JOIN srp_erp_ngo_donors on srp_erp_ngo_donorcollectionmaster.donorsID=srp_erp_ngo_donors.contactID WHERE collectionAutoId=$collectionAutoId ")->row_array();
        $data['detail_item'] = $this->db->query("SELECT cm.documentSystemCode as documentSystemCode ,projectName,collectionDetailAutoID,collectionAutoId,projectID,type,itemAutoID,itemSystemCode,itemDescription,itemCategory,expenseGLAutoID,expenseGLCode,expenseSystemGLCode,expenseGLDescription,expenseGLType,wareHouseAutoID,wareHouseCode,wareHouseLocation,wareHouseDescription,defaultUOMID,defaultUOM,unitOfMeasureID,unitOfMeasure,conversionRateUOM,itemQty,srp_erp_ngo_donorcollectiondetails.description,GLAutoID,SystemGLCode,GLCode,GLDescription,GLType,unittransactionAmount,transactionAmount,companyLocalWacAmount,unitcompanyLocalAmount,companyLocalAmount,srp_erp_ngo_donorcollectiondetails.companyLocalExchangeRate,unitcompanyReportingAmount,companyReportingAmount,srp_erp_ngo_donorcollectiondetails.companyReportingExchangeRate,unitDonoursAmount,donorsAmount,donorsExchangeRate FROM srp_erp_ngo_donorcollectiondetails LEFT JOIN srp_erp_ngo_projects ON ngoProjectID = projectID LEFT JOIN srp_erp_ngo_commitmentmasters cm ON cm.commitmentAutoId = srp_erp_ngo_donorcollectiondetails.commitmentAutoID  WHERE collectionAutoId = $collectionAutoId  AND type =2")->result_array();
        $data['detail_cash'] = $this->db->query("select projectName,collectionDetailAutoID ,collectionAutoId ,projectID ,type ,itemAutoID ,itemSystemCode ,itemDescription ,itemCategory ,expenseGLAutoID ,expenseGLCode ,expenseSystemGLCode ,expenseGLDescription ,expenseGLType,wareHouseAutoID ,wareHouseCode ,wareHouseLocation ,wareHouseDescription ,defaultUOMID ,defaultUOM ,unitOfMeasureID ,unitOfMeasure ,conversionRateUOM ,itemQty ,srp_erp_ngo_donorcollectiondetails.description ,GLAutoID ,SystemGLCode ,GLCode ,GLDescription ,GLType,unittransactionAmount ,transactionAmount ,companyLocalWacAmount ,unitcompanyLocalAmount ,companyLocalAmount ,companyLocalExchangeRate ,unitcompanyReportingAmount ,companyReportingAmount ,companyReportingExchangeRate ,unitDonoursAmount ,donorsAmount ,donorsExchangeRate from srp_erp_ngo_donorcollectiondetails LEFT JOIN srp_erp_ngo_projects ON ngoProjectID = projectID  WHERE collectionAutoId=$collectionAutoId AND type=1")->result_array();
        return $data;

    }

    function save_donor_collection_item_detail()
    {

        $collectionAutoId = $this->input->post('collectionAutoId');
        $projectID = $this->input->post('projectID');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $wareHouse = $this->input->post('wareHouse');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $quantityRequested = $this->input->post('quantityRequested');
        $description = $this->input->post('description');
        $commitmentAutoId = $this->input->post('commitmentAutoId');
        $date_format_policy = date_format_policy();


        $master = $this->db->query("select * from srp_erp_ngo_donorcollectionmaster WHERE collectionAutoId=$collectionAutoId")->row_array();
        $this->db->trans_start();
        $ACA_ID = $this->common_data['controlaccounts']['ACA'];
        $ACA = fetch_gl_account_desc($ACA_ID);


        foreach ($itemAutoIDs as $key => $itemAutoID) {
            /* if (!$commitmentAutoId) {
               $this->db->select('dispatchAutoID,itemDescription,itemSystemCode');
               $this->db->from('srp_erp_buyback_dispatchnotedetails');
               $this->db->where('dispatchAutoID', $commitmentAutoId);
               $this->db->where('itemAutoID', $itemAutoID);
               $order_detail = $this->db->get()->row_array();
               if (!empty($order_detail)) {
                 return array('e', 'Dispatch Note Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                 exit;
               }
             }*/
            $item_data = fetch_item_data($itemAutoID);
            $uomEx = explode('|', $uom[$key]);
            $wareHouse_location = explode(' | ', $wareHouse[$key]);

            $GL = $this->db->query("SELECT * FROM `srp_erp_ngo_projects` LEFT JOIN `srp_erp_chartofaccounts` on revenueGLAutoID=GLAutoID WHERE ngoProjectID = $projectID[$key]")->row_array();
            $data['GLAutoID'] = trim($GL['GLAutoID']);
            $data['SystemGLCode'] = trim($GL['systemAccountCode']);
            $data['GLCode'] = trim($GL['GLSecondaryCode']);
            $data['GLDescription'] = trim($GL['GLDescription']);
            $data['GLType'] = trim($GL['subCategory']);
            $data['commitmentAutoId'] = $commitmentAutoId[$key];

            $data['collectionAutoId'] = $collectionAutoId;
            $data['projectID'] = $projectID[$key];
            $data['type'] = 2;
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            $data['itemCategory'] = $item_data['mainCategory'];
            $data['expenseGLAutoID'] = $item_data['costGLAutoID'];
            $data['expenseGLCode'] = $item_data['costGLCode'];
            $data['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['expenseGLDescription'] = $item_data['costDescription'];
            $data['expenseGLType'] = $item_data['costType'];
            $data['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
            $data['revenueGLCode'] = $item_data['revanueGLCode'];
            $data['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
            $data['revenueGLDescription'] = $item_data['revanueDescription'];
            $data['revenueGLType'] = $item_data['revanueType'];
            $data['assetGLAutoID'] = $item_data['assteGLAutoID'];
            $data['assetGLCode'] = $item_data['assteGLCode'];
            $data['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
            $data['assetGLDescription'] = $item_data['assteDescription'];
            $data['assetGLType'] = $item_data['assteType'];
            $data['wareHouseAutoID'] = $wareHouseAutoID[$key];
            $data['wareHouseCode'] = trim($wareHouse_location[0]);
            $data['wareHouseLocation'] = trim($wareHouse_location[1]);
            $data['wareHouseDescription'] = trim($wareHouse_location[2]);
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['unitOfMeasure'] = trim($uomEx[0]);
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['itemQty'] = $quantityRequested[$key];
            $data['description'] = $description[$key];

            $data['unittransactionAmount'] = round($estimatedAmount[$key],
                $master['transactionCurrencyDecimalPlaces']);
            $transactionAmount = ($data['unittransactionAmount']) * $quantityRequested[$key];
            $data['transactionAmount'] = $transactionAmount;
            $data['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
            $data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $master['companyLocalExchangeRate']);
            $data['companyLocalAmount'] = ($data['transactionAmount'] / $master['companyLocalExchangeRate']);
            $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $master['companyReportingExchangeRate']);
            $data['companyReportingAmount'] = ($data['transactionAmount'] / $master['companyReportingExchangeRate']);
            $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            //  $emp_currency = currency_conversionID($transactionCurrencyID[$key], $empcurr['payCurrencyID']);
            $unitDonourAmount = $estimatedAmount[$key] / $master['donorExchangeRate'];
            $data['unitDonoursAmount'] = round($unitDonourAmount, $master['donorCurrencyDecimalPlaces']);
            $data['donorsAmount'] = $unitDonourAmount * $quantityRequested[$key];
            $data['donorsExchangeRate'] = $master['donorExchangeRate'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];

            $this->db->insert('srp_erp_ngo_donorcollectiondetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();
            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $data['wareHouseAutoID'],
                    'wareHouseLocation' => $data['wareHouseLocation'],
                    'wareHouseDescription' => $data['wareHouseDescription'],
                    'itemAutoID' => $data['itemAutoID'],
                    'itemSystemCode' => $data['itemSystemCode'],
                    'itemDescription' => $data['itemDescription'],
                    'unitOfMeasureID' => $data['defaultUOMID'],
                    'unitOfMeasure' => $data['defaultUOM'],
                    'currentStock' => 0,
                    'companyID' => $this->common_data['company_data']['company_id'],
                    'companyCode' => $this->common_data['company_data']['company_code'],
                );
                $this->db->insert('srp_erp_warehouseitems', $data_arr);
            }

        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', 'Donor Collection :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Donor Collection :  Records inserted successfully.');
        }

    }

    function save_donor_collection_cash_detail()
    {


        $this->db->trans_start();

        $segment_gl = $this->input->post('segment_gl');
        $collectionAutoId = $this->input->post('collectionAutoId');
        $commitmentDetailAutoID = $this->input->post('commitmentDetailAutoID');


        $amount = $this->input->post('amount');
        $description = $this->input->post('description');
        $projectID = $this->input->post('projectID');

        $date_format_policy = date_format_policy();
        $master = $this->db->query("select * from srp_erp_ngo_donorcollectionmaster WHERE collectionAutoId=$collectionAutoId")->row_array();
        foreach ($projectID as $key => $id) {
            $GL = $this->db->query("SELECT * FROM `srp_erp_ngo_projects` LEFT JOIN `srp_erp_chartofaccounts` on revenueGLAutoID=GLAutoID WHERE ngoProjectID = $projectID[$key]")->row_array();

            $commitmentID = NULL;
            $commitmentDetailID = NULL;
            if ($commitmentDetailAutoID[$key] != '') {
                $commit = explode(' | ', $commitmentDetailAutoID[$key]);
                $commitmentID = $commit[0];
                $commitmentDetailID = $commit[1];
            }


            $data[$key]['type'] = 1;
            $data[$key]['collectionAutoId'] = $collectionAutoId;
            $data[$key]['commitmentAutoId'] = $commitmentID;
            $data[$key]['commitmentDetailID'] = $commitmentDetailID;
            $data[$key]['GLAutoID'] = trim($GL['GLAutoID']);
            $data[$key]['SystemGLCode'] = trim($GL['systemAccountCode']);
            $data[$key]['GLCode'] = trim($GL['GLSecondaryCode']);
            $data[$key]['GLDescription'] = trim($GL['GLDescription']);
            $data[$key]['GLType'] = trim($GL['subCategory']);
            $data[$key]['projectID'] = $projectID[$key];
            $donorsAmount = $amount[$key] / $master['donorExchangeRate'];
            $data[$key]['donorsAmount'] = $donorsAmount;
            $data[$key]['donorsExchangeRate'] = $master['donorExchangeRate'];

            $data[$key]['transactionAmount'] = trim($amount[$key]);
            $data[$key]['companyLocalAmount'] = ($data[$key]['transactionAmount'] / $master['companyLocalExchangeRate']);
            $data[$key]['companyReportingAmount'] = ($data[$key]['transactionAmount'] / $master['companyReportingExchangeRate']);
            $data[$key]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data[$key]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data[$key]['description'] = trim($description[$key]);
            $data[$key]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$key]['modifiedUserID'] = $this->common_data['current_userID'];
            $data[$key]['modifiedUserName'] = $this->common_data['current_user'];
            $data[$key]['modifiedDateTime'] = $this->common_data['current_date'];


            $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdUserName'] = $this->common_data['current_user'];
            $data[$key]['createdDateTime'] = $this->common_data['current_date'];


        }

        $this->db->insert_batch('srp_erp_ngo_donorcollectiondetails', $data);
        $last_id = 0;//$this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {

            $this->db->trans_rollback();

            return array('e', 'Save Failed');
        } else {

            $this->db->trans_commit();

            return array('s', 'Records inserted successfully');
        }

    }

    function update_collection_itemDetail()
    {

        $collectionAutoId = $this->input->post('collectionAutoId');
        $collectionDetailAutoID = $this->input->post('collectionDetailAutoID');
        $projectID = $this->input->post('projectID');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $wareHouse = $this->input->post('wareHouse');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $quantityRequested = $this->input->post('quantityRequested');
        $description = $this->input->post('description');
        $date_format_policy = date_format_policy();


        $master = $this->db->query("select * from srp_erp_ngo_donorcollectionmaster WHERE collectionAutoId=$collectionAutoId")->row_array();
        $this->db->trans_start();


        $item_data = fetch_item_data($itemAutoIDs);
        $uomEx = explode('|', $uom);
        $wareHouse_location = explode(' | ', $wareHouse);

        $GL = $this->db->query("SELECT * FROM `srp_erp_ngo_projects` LEFT JOIN `srp_erp_chartofaccounts` on revenueGLAutoID=GLAutoID WHERE ngoProjectID = $projectID")->row_array();
        $data['commitmentAutoId'] = trim($this->input->post('commitmentAutoId'));
        $data['GLAutoID'] = trim($GL['GLAutoID']);
        $data['SystemGLCode'] = trim($GL['systemAccountCode']);
        $data['GLCode'] = trim($GL['GLSecondaryCode']);
        $data['GLDescription'] = trim($GL['GLDescription']);
        $data['GLType'] = trim($GL['subCategory']);
        $data['projectID'] = $projectID;
        $data['type'] = 2;
        $data['itemAutoID'] = $itemAutoIDs;
        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        $data['itemCategory'] = $item_data['mainCategory'];
        $data['expenseGLAutoID'] = $item_data['costGLAutoID'];
        $data['expenseGLCode'] = $item_data['costGLCode'];
        $data['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
        $data['expenseGLDescription'] = $item_data['costDescription'];
        $data['expenseGLType'] = $item_data['costType'];
        $data['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
        $data['revenueGLCode'] = $item_data['revanueGLCode'];
        $data['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
        $data['revenueGLDescription'] = $item_data['revanueDescription'];
        $data['revenueGLType'] = $item_data['revanueType'];
        $data['assetGLAutoID'] = $item_data['assteGLAutoID'];
        $data['assetGLCode'] = $item_data['assteGLCode'];
        $data['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
        $data['assetGLDescription'] = $item_data['assteDescription'];
        $data['assetGLType'] = $item_data['assteType'];
        $data['wareHouseAutoID'] = $wareHouseAutoID;
        $data['wareHouseCode'] = trim($wareHouse_location[0]);
        $data['wareHouseLocation'] = trim($wareHouse_location[1]);
        $data['wareHouseDescription'] = trim($wareHouse_location[2]);
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['unitOfMeasure'] = trim($uomEx[0]);
        $data['unitOfMeasureID'] = $UnitOfMeasureID;
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['itemQty'] = $quantityRequested;
        $data['description'] = $description;


        $data['unittransactionAmount'] = round($estimatedAmount, $master['transactionCurrencyDecimalPlaces']);
        $transactionAmount = ($data['unittransactionAmount']) * $quantityRequested;
        $data['transactionAmount'] = $transactionAmount;
        $data['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
        $data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $master['companyLocalExchangeRate']);
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $master['companyLocalExchangeRate']);
        $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $master['companyReportingExchangeRate']);
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $master['companyReportingExchangeRate']);
        $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
        $unitDonourAmount = $estimatedAmount / $master['donorExchangeRate'];
        $data['unitDonoursAmount'] = round($unitDonourAmount, $master['donorCurrencyDecimalPlaces']);
        $data['donorsAmount'] = $unitDonourAmount * $quantityRequested;
        $data['donorsExchangeRate'] = $master['donorExchangeRate'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['createdUserName'] = $this->common_data['current_user'];

        $this->db->update('srp_erp_ngo_donorcollectiondetails', $data,
            array('collectionDetailAutoID' => $collectionDetailAutoID));


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', 'Donor Collection :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Donor Collection  :  Records inserted successfully.');
        }

    }

    function update_collection_cash_details()
    {


        $this->db->trans_start();


        $collectionAutoId = $this->input->post('collectionAutoId');
        $collectionDetailAutoID = $this->input->post('collectionDetailAutoID');
        $commitmentDetailAutoID = $this->input->post('commitmentDetailAutoID');

        $amount = $this->input->post('amount');
        $description = $this->input->post('description');
        $projectID = $this->input->post('projectID');
        $date_format_policy = date_format_policy();
        $master = $this->db->query("select * from srp_erp_ngo_donorcollectionmaster WHERE collectionAutoId=$collectionAutoId")->row_array();
        $GL = $this->db->query("SELECT * FROM `srp_erp_ngo_projects` LEFT JOIN `srp_erp_chartofaccounts` on revenueGLAutoID=GLAutoID WHERE ngoProjectID = $projectID")->row_array();
        $commitmentID = NULL;
        $commitmentDetail = NULL;
        if ($commitmentDetailAutoID != '') {
            $commit = explode(' | ', $commitmentDetailAutoID);
            $commitmentID = $commit[0];
            $commitmentDetail = $commit[1];
        }
        $data['commitmentAutoId'] = $commitmentID;
        $data['commitmentDetailID'] = $commitmentDetail;
        $data['type'] = 1;
        $data['GLAutoID'] = trim($GL['GLAutoID']);
        $data['SystemGLCode'] = trim($GL['systemAccountCode']);
        $data['GLCode'] = trim($GL['GLSecondaryCode']);
        $data['GLDescription'] = trim($GL['GLDescription']);
        $data['GLType'] = trim($GL['subCategory']);
        $data['projectID'] = $projectID;
        $donorsAmount = $amount / $master['donorExchangeRate'];
        $data['donorsAmount'] = $donorsAmount;
        $data['donorsExchangeRate'] = $master['donorExchangeRate'];

        $data['transactionAmount'] = trim($amount);
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $master['companyLocalExchangeRate']);
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $master['companyReportingExchangeRate']);
        $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
        $data['description'] = trim($description);
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];


        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];


        $this->db->update('srp_erp_ngo_donorcollectiondetails', $data,
            array('collectionDetailAutoID' => $collectionDetailAutoID));

        $last_id = 0;//$this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {

            $this->db->trans_rollback();

            return array('e', 'Something went wrong');
        } else {

            $this->db->trans_commit();

            return array('s', 'Cash Detail : Records inserted successfully. ');
        }

    }

    function delete_collection_project()
    {
        $data = array('isDeleted' => 1, 'deletedDate' => $this->common_data['current_date'],
            'deletedEmpID' => $this->common_data['current_userID']);

        $collectionAutoId = trim($this->input->post('collectionAutoId'));

        $this->db->where('collectionAutoId', $collectionAutoId);
        $this->db->update('srp_erp_ngo_donorcollectionmaster', $data);

    }


    function donor_collection_confirmation()
    {
        $this->db->select('collectionDetailAutoID');
        $this->db->where('collectionAutoId', trim($this->input->post('collectionAutoId')));
        $this->db->from('srp_erp_ngo_donorcollectiondetails');
        $record = $this->db->get()->result_array();
        if (empty($record)) {
            return array('w', 'There are no records to confirm this document!');
        } else {
            $this->load->library('approvals');
            $this->db->select('*');
            $this->db->where('collectionAutoId', trim($this->input->post('collectionAutoId')));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_ngo_donorcollectionmaster');
            $row = $this->db->get()->row_array();
            if (!empty($row)) {
                return array('w', 'Document already confirmed');
            } else {
                $this->db->select('*');
                $this->db->where('collectionAutoId', trim($this->input->post('collectionAutoId')));
                $this->db->from('srp_erp_ngo_donorcollectionmaster');
                $row = $this->db->get()->row_array();
                $approvals_status = $this->approvals->CreateApproval('DC', $row['collectionAutoId'], $row['documentSystemCode'], 'Donor Collection', 'srp_erp_ngo_donorcollectionmaster', 'collectionAutoId');
                if ($approvals_status == 1) {
                    $collectionAutoId = trim($this->input->post('collectionAutoId'));

                    $data = array('confirmedYN' => 1, 'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user']);

                    $this->db->where('collectionAutoId', $collectionAutoId);
                    $this->db->update('srp_erp_ngo_donorcollectionmaster', $data);

                    return array('s', 'Donor Collection : Confirmed Successfully. ');
                } else if ($approvals_status == 3) {
                    return array('w', 'There are no users exist to perform approval for this document.');
                } else {
                    return array('e', 'something went wrong');
                }
            }
        }
    }


    function save_donor_collection_approval()
    {
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_id = trim($this->input->post('collectionAutoId'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('po_status'));
        $comments = trim($this->input->post('comments'));
        $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'DC');

        if ($approvals_status == 1) {
            $master = $this->db->query("select * from srp_erp_ngo_donorcollectionmaster WHERE collectionAutoId={$system_id} ")->row_array();

            $receipt_detail = $this->db->query("select 	srp_erp_ngo_donorcollectiondetails.type,itemAutoID,conversionRateUOM,wareHouseAutoID,wareHouseCode,wareHouseLocation,wareHouseDescription,itemSystemCode,itemDescription, defaultUOMID,defaultUOM,unitOfMeasureID,unitOfMeasure,sum(itemQty) as qty,sum(transactionAmount) as transactionAmount,sum(companyLocalAmount)as companyLocalAmount,sum(companyReportingAmount) as companyReportingAmount, srp_erp_ngo_projects.segmentID, segmentCode from srp_erp_ngo_donorcollectiondetails LEFT JOIN srp_erp_ngo_projects ON ngoProjectID = projectID LEFT JOIN srp_erp_segment ON srp_erp_segment.segmentID = srp_erp_ngo_projects.segmentID WHERE collectionAutoId={$system_id} GROUP BY itemAutoID,wareHouseAutoID")->result_array();

            for ($a = 0; $a < count($receipt_detail); $a++) {
                if ($receipt_detail[$a]['type'] == 2) {
                    $item = fetch_item_data($receipt_detail[$a]['itemAutoID']);
                    $itemAutoID = $receipt_detail[$a]['itemAutoID'];
                    $qty = $receipt_detail[$a]['qty'] / $receipt_detail[$a]['conversionRateUOM'];
                    $wareHouseAutoID = $receipt_detail[$a]['wareHouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                    $item_arr[$a]['itemAutoID'] = $receipt_detail[$a]['itemAutoID'];
                    $item_arr[$a]['currentStock'] = ($item['currentStock'] + $qty);
                    $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) + ($receipt_detail[$a]['companyLocalAmount'])) / $item_arr[$a]['currentStock']), $master['companyLocalCurrencyDecimalPlaces']);
                    $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) + ($receipt_detail[$a]['companyReportingAmount'] / $master['companyReportingExchangeRate'])) / $item_arr[$a]['currentStock']), $master['companyReportingCurrencyDecimalPlaces']);
                    $itemledger_arr[$a]['documentID'] = $master['documentCode'];
                    $itemledger_arr[$a]['documentCode'] = $master['documentCode'];
                    $itemledger_arr[$a]['documentAutoID'] = $master['collectionAutoId'];
                    $itemledger_arr[$a]['documentSystemCode'] = $master['documentSystemCode'];
                    $itemledger_arr[$a]['documentDate'] = $master['documentDate'];
                    $itemledger_arr[$a]['referenceNumber'] = $master['referenceNo'];
                    $itemledger_arr[$a]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                    $itemledger_arr[$a]['companyFinanceYear'] = $master['companyFinanceYear'];
                    $itemledger_arr[$a]['FYBegin'] = $master['FYBegin'];
                    $itemledger_arr[$a]['FYEnd'] = $master['FYEnd'];
                    $itemledger_arr[$a]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                    $itemledger_arr[$a]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                    $itemledger_arr[$a]['wareHouseAutoID'] = $receipt_detail[$a]['wareHouseAutoID'];
                    $itemledger_arr[$a]['wareHouseCode'] = $receipt_detail[$a]['wareHouseCode'];
                    $itemledger_arr[$a]['wareHouseLocation'] = $receipt_detail[$a]['wareHouseLocation'];
                    $itemledger_arr[$a]['wareHouseDescription'] = $receipt_detail[$a]['wareHouseDescription'];
                    $itemledger_arr[$a]['itemAutoID'] = $receipt_detail[$a]['itemAutoID'];
                    $itemledger_arr[$a]['itemSystemCode'] = $receipt_detail[$a]['itemSystemCode'];
                    $itemledger_arr[$a]['itemDescription'] = $receipt_detail[$a]['itemDescription'];
                    $itemledger_arr[$a]['defaultUOMID'] = $receipt_detail[$a]['defaultUOMID'];
                    $itemledger_arr[$a]['defaultUOM'] = $receipt_detail[$a]['defaultUOM'];
                    $itemledger_arr[$a]['transactionUOMID'] = $receipt_detail[$a]['unitOfMeasureID'];
                    $itemledger_arr[$a]['transactionUOM'] = $receipt_detail[$a]['unitOfMeasure'];
                    $itemledger_arr[$a]['transactionQTY'] = ($receipt_detail[$a]['qty']);
                    $itemledger_arr[$a]['convertionRate'] = $receipt_detail[$a]['conversionRateUOM'];
                    $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                    $itemledger_arr[$a]['PLGLAutoID'] = $item['revanueGLAutoID'];
                    $itemledger_arr[$a]['PLSystemGLCode'] = $item['revanueSystemGLCode'];
                    $itemledger_arr[$a]['PLGLCode'] = $item['revanueGLCode'];
                    $itemledger_arr[$a]['PLDescription'] = $item['revanueDescription'];
                    $itemledger_arr[$a]['PLType'] = $item['revanueType'];
                    $itemledger_arr[$a]['BLGLAutoID'] = $item['assteGLAutoID'];
                    $itemledger_arr[$a]['BLSystemGLCode'] = $item['assteSystemGLCode'];
                    $itemledger_arr[$a]['BLGLCode'] = $item['assteGLCode'];
                    $itemledger_arr[$a]['BLDescription'] = $item['assteDescription'];
                    $itemledger_arr[$a]['BLType'] = $item['assteType'];
                    $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];

                    $itemledger_arr[$a]['transactionAmount'] = round($receipt_detail[$a]['transactionAmount'], $itemledger_arr[$a]['transactionCurrencyDecimalPlaces']);
                    $itemledger_arr[$a]['salesPrice'] = NULL;
                    $itemledger_arr[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                    $itemledger_arr[$a]['transactionCurrency'] = $master['transactionCurrency'];
                    $itemledger_arr[$a]['transactionExchangeRate'] = $master['transactionExchangeRate'];

                    $itemledger_arr[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                    $itemledger_arr[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                    $itemledger_arr[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                    $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                    $itemledger_arr[$a]['companyLocalWacAmount'] = $item_arr[$a]['companyLocalWacAmount'];
                    $itemledger_arr[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                    $itemledger_arr[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                    $itemledger_arr[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                    $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                    $itemledger_arr[$a]['companyReportingWacAmount'] = $item_arr[$a]['companyReportingWacAmount'];
                    /*donor*/
                    $itemledger_arr[$a]['partyCurrencyID'] = $master['donorCurrencyID'];
                    $itemledger_arr[$a]['partyCurrency'] = fetch_currency_code($master['donorCurrencyID']);
                    $itemledger_arr[$a]['partyCurrencyExchangeRate'] = $master['donorExchangeRate'];
                    $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $master['donorCurrencyDecimalPlaces'];
                    $itemledger_arr[$a]['partyCurrencyAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['partyCurrencyExchangeRate']), $itemledger_arr[$a]['partyCurrencyDecimalPlaces']);
                    $itemledger_arr[$a]['confirmedYN'] = $master['confirmedYN'];
                    $itemledger_arr[$a]['confirmedByEmpID'] = $master['confirmedByEmpID'];
                    $itemledger_arr[$a]['confirmedByName'] = $master['confirmedByName'];
                    $itemledger_arr[$a]['confirmedDate'] = $master['confirmedDate'];
                    $itemledger_arr[$a]['approvedYN'] = $master['approvedYN'];
                    $itemledger_arr[$a]['approvedDate'] = $master['approvedDate'];
                    $itemledger_arr[$a]['approvedbyEmpID'] = $master['approvedbyEmpID'];
                    $itemledger_arr[$a]['approvedbyEmpName'] = $master['approvedbyEmpName'];

                    $itemledger_arr[$a]['segmentID'] = $receipt_detail[$a]['segmentID'];
                    $itemledger_arr[$a]['segmentCode'] = $receipt_detail[$a]['segmentCode'];
                    $itemledger_arr[$a]['companyID'] = $master['companyID'];
                    $itemledger_arr[$a]['companyCode'] = $master['companyCode'];
                    $itemledger_arr[$a]['createdUserGroup'] = $master['createdUserGroup'];
                    $itemledger_arr[$a]['createdPCID'] = $master['createdPCID'];
                    $itemledger_arr[$a]['createdUserID'] = $master['createdUserID'];
                    $itemledger_arr[$a]['createdDateTime'] = $master['createdDateTime'];
                    $itemledger_arr[$a]['createdUserName'] = $master['createdUserName'];
                    $itemledger_arr[$a]['modifiedPCID'] = $master['modifiedPCID'];
                    $itemledger_arr[$a]['modifiedUserID'] = $master['modifiedUserID'];
                    $itemledger_arr[$a]['modifiedDateTime'] = $master['modifiedDateTime'];
                    $itemledger_arr[$a]['modifiedUserName'] = $master['modifiedUserName'];

                }
            }

            if (!empty($item_arr)) {
                $item_arr = array_values($item_arr);
                $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
            }

            if (!empty($itemledger_arr)) {
                $itemledger_arr = array_values($itemledger_arr);
                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
            }

            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_donor_collection($system_id, 'DC');

            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['collectionAutoId'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['documentSystemCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['documentDate'];
                $generalledger_arr[$i]['documentType'] = NULL;
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['documentDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['documentDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['referenceNo'];
                $generalledger_arr[$i]['chequeNumber'] = $double_entry['master_data']['DCchequeNo'];
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['gl_detail'][$i]['transactionExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['gl_detail'][$i]['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['gl_detail'][$i]['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                    $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                }
                $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
            }
            $total = $this->db->query("SELECT sum(transactionAmount) as amount FROM `srp_erp_ngo_donorcollectiondetails` WHERE `type` = '1' AND collectionAutoId={$double_entry['master_data']['collectionAutoId']}   ")->row_array();
            $amount = $total['amount']; //receipt_voucher_total_value($double_entry['master_data']['receiptVoucherAutoId'], $double_entry['master_data']['transactionCurrencyDecimalPlaces'], 0);
            if ($amount != '') {


                $bankledger_arr['documentMasterAutoID'] = $double_entry['master_data']['collectionAutoId'];
                $bankledger_arr['documentDate'] = $double_entry['master_data']['documentDate'];
                $bankledger_arr['transactionType'] = 1;
                $bankledger_arr['bankName'] = $double_entry['master_data']['DCbank'];
                $bankledger_arr['bankGLAutoID'] = $double_entry['master_data']['bankGLAutoID'];
                $bankledger_arr['bankSystemAccountCode'] = $double_entry['master_data']['bankSystemAccountCode'];
                $bankledger_arr['bankGLSecondaryCode'] = $double_entry['master_data']['bankGLSecondaryCode'];
                $bankledger_arr['documentType'] = 'DC';
                $bankledger_arr['documentSystemCode'] = $double_entry['master_data']['documentSystemCode'];
                $bankledger_arr['modeofpayment'] = $double_entry['master_data']['modeOfPayment'];
                $bankledger_arr['chequeNo'] = $double_entry['master_data']['DCchequeNo'];
                $bankledger_arr['chequeDate'] = $double_entry['master_data']['DCchequeDate'];
                $bankledger_arr['memo'] = $double_entry['master_data']['narration'];
                $bankledger_arr['partyType'] = 'DON';
                $bankledger_arr['partyAutoID'] = $double_entry['master_data']['donorsID'];
                $bankledger_arr['partyCode'] = NULL; //$double_entry['master_data']['customerSystemCode'];
                $bankledger_arr['partyName'] = $double_entry['donor']['name'];
                $bankledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $bankledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $bankledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                $bankledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                $bankledger_arr['transactionAmount'] = $amount;
                $bankledger_arr['partyCurrencyID'] = $double_entry['master_data']['donorCurrencyID'];
                $bankledger_arr['partyCurrency'] = fetch_currency_code($double_entry['master_data']['donorCurrencyID']);
                $bankledger_arr['partyCurrencyExchangeRate'] = $double_entry['master_data']['donorExchangeRate'];
                $bankledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['donorCurrencyDecimalPlaces'];
                $bankledger_arr['partyCurrencyAmount'] = ($bankledger_arr['transactionAmount'] / $bankledger_arr['partyCurrencyExchangeRate']);
                $bankledger_arr['bankCurrencyID'] = $double_entry['master_data']['bankCurrencyID'];
                $bankledger_arr['bankCurrency'] = $double_entry['master_data']['bankCurrency'];
                $bankledger_arr['bankCurrencyExchangeRate'] = $double_entry['master_data']['bankCurrencyExchangeRate'];
                $bankledger_arr['bankCurrencyAmount'] = ($bankledger_arr['transactionAmount'] / $bankledger_arr['bankCurrencyExchangeRate']);
                $bankledger_arr['bankCurrencyDecimalPlaces'] = $double_entry['master_data']['bankCurrencyDecimalPlaces'];
                $bankledger_arr['companyID'] = $double_entry['master_data']['companyID'];
                $bankledger_arr['companyCode'] = $double_entry['master_data']['companyCode'];
                $bankledger_arr['segmentID'] = NULL;
                $bankledger_arr['segmentCode'] = NULL;
                $bankledger_arr['createdPCID'] = $this->common_data['current_pc'];
                $bankledger_arr['createdUserID'] = $this->common_data['current_userID'];
                $bankledger_arr['createdDateTime'] = $this->common_data['current_date'];
                $bankledger_arr['createdUserName'] = $this->common_data['current_user'];
                $bankledger_arr['modifiedPCID'] = $this->common_data['current_pc'];
                $bankledger_arr['modifiedUserID'] = $this->common_data['current_userID'];
                $bankledger_arr['modifiedDateTime'] = $this->common_data['current_date'];
                $bankledger_arr['modifiedUserName'] = $this->common_data['current_user'];

                $this->db->insert('srp_erp_bankledger', $bankledger_arr);
            }
            $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            /*        if (!empty($generalledger_arr)) {
                      $generalledger_arr = array_values($generalledger_arr);
                      $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                      $this->db->select('sum(transactionAmount) as transaction_total, sum(companyLocalAmount) as companyLocal_total, sum(companyReportingAmount) as companyReporting_total, sum(partyCurrencyAmount) as party_total');
                      $this->db->where('documentCode', 'DC');
                      $this->db->where('documentMasterAutoID', $system_id);
                      $totals = $this->db->get('srp_erp_generalledger')->row_array();
                      if ($totals['transaction_total'] != 0 or $totals['companyLocal_total'] != 0 or $totals['companyReporting_total'] != 0 or $totals['party_total'] != 0) {
                        $generalledger_arr = array();
                        $ERGL_ID = $this->common_data['controlaccounts']['ERGL'];
                        $ERGL = fetch_gl_account_desc($ERGL_ID);
                        $generalledger_arr['documentMasterAutoID'] = $double_entry['master_data']['collectionAutoId'];
                        $generalledger_arr['documentCode'] = $double_entry['code'];
                        $generalledger_arr['documentSystemCode'] = $double_entry['master_data']['documentSystemCode'];
                        $generalledger_arr['documentDate'] = $double_entry['master_data']['documentDate'];
                        $generalledger_arr['documentType'] = NULL;
                        $generalledger_arr['documentYear'] = $double_entry['master_data']['documentDate'];
                        $generalledger_arr['documentMonth'] = date("m", strtotime($double_entry['master_data']['documentDate']));
                        $generalledger_arr['documentNarration'] = $double_entry['master_data']['referenceNo'];
                        $generalledger_arr['chequeNumber'] = $double_entry['master_data']['DCchequeNo'];
                        $generalledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                        $generalledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                        $generalledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                        $generalledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                        $generalledger_arr['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                        $generalledger_arr['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                        $generalledger_arr['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                        $generalledger_arr['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                        $generalledger_arr['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                        $generalledger_arr['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                        $generalledger_arr['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                        $generalledger_arr['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                        $generalledger_arr['partyContractID'] = '';
                        $generalledger_arr['partyType'] = 'CUS';
                        $generalledger_arr['partyAutoID'] = $double_entry['master_data']['donorsID'];
                        $generalledger_arr['partySystemCode'] = NULL;
                        $generalledger_arr['partyName'] = $double_entry['donor']['name'];
                        $generalledger_arr['partyCurrencyID'] = $double_entry['master_data']['donorCurrencyID'];
                        $generalledger_arr['partyCurrency'] = fetch_currency_code($double_entry['master_data']['donorCurrencyID']);
                        $generalledger_arr['partyExchangeRate'] = $double_entry['master_data']['donorExchangeRate'];
                        $generalledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['donorCurrencyDecimalPlaces'];
                        $generalledger_arr['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                        $generalledger_arr['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                        $generalledger_arr['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                        $generalledger_arr['approvedDate'] = $double_entry['master_data']['approvedDate'];
                        $generalledger_arr['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                        $generalledger_arr['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                        $generalledger_arr['companyID'] = $double_entry['master_data']['companyID'];
                        $generalledger_arr['companyCode'] = $double_entry['master_data']['companyCode'];
                        $generalledger_arr['transactionAmount'] = round(($totals['transaction_total'] * -1), $generalledger_arr['transactionCurrencyDecimalPlaces']);
                        $generalledger_arr['companyLocalAmount'] = round(($totals['companyLocal_total'] * -1), $generalledger_arr['companyLocalCurrencyDecimalPlaces']);
                        $generalledger_arr['companyReportingAmount'] = round(($totals['companyReporting_total'] * -1), $generalledger_arr['companyReportingCurrencyDecimalPlaces']);
                        $generalledger_arr['partyCurrencyAmount'] = round(($totals['party_total'] * -1), $generalledger_arr['partyCurrencyDecimalPlaces']);
                        $generalledger_arr['amount_type'] = null;
                        $generalledger_arr['documentDetailAutoID'] = 0;
                        $generalledger_arr['GLAutoID'] = $ERGL_ID;
                        $generalledger_arr['systemGLCode'] = $ERGL['systemAccountCode'];
                        $generalledger_arr['GLCode'] = $ERGL['GLSecondaryCode'];
                        $generalledger_arr['GLDescription'] = $ERGL['GLDescription'];
                        $generalledger_arr['GLType'] = $ERGL['subCategory'];
                        $generalledger_arr['segmentID'] = $double_entry['master_data']['segmentID'];
                        $generalledger_arr['segmentCode'] = $double_entry['master_data']['segmentCode'];
                        $generalledger_arr['subLedgerType'] = 0;
                        $generalledger_arr['subLedgerDesc'] = null;
                        $generalledger_arr['isAddon'] = 0;
                        $generalledger_arr['createdUserGroup'] = $this->common_data['user_group'];
                        $generalledger_arr['createdPCID'] = $this->common_data['current_pc'];
                        $generalledger_arr['createdUserID'] = $this->common_data['current_userID'];
                        $generalledger_arr['createdDateTime'] = $this->common_data['current_date'];
                        $generalledger_arr['createdUserName'] = $this->common_data['current_user'];
                        $generalledger_arr['modifiedPCID'] = $this->common_data['current_pc'];
                        $generalledger_arr['modifiedUserID'] = $this->common_data['current_userID'];
                        $generalledger_arr['modifiedDateTime'] = $this->common_data['current_date'];
                        $generalledger_arr['modifiedUserName'] = $this->common_data['current_user'];
                        $this->db->insert('srp_erp_generalledger', $generalledger_arr);
                      }
                    }*/
            /*$this->db->select_sum('transactionAmount');
            $this->db->where('receiptVoucherAutoId', $system_id);
            $total = $this->db->get('srp_erp_customerreceiptdetail')->row('transactionAmount');*/

            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];
            /* $data['transactionAmount'] = $total;*/
            $this->db->where('collectionAutoId', $system_id);
            $this->db->update('srp_erp_ngo_donorcollectionmaster', $data);
            $this->session->set_flashdata('s', 'Donor Collection Approved Successfully.');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function fetch_itemrecode_donor()
    {

        $dataArr = array();
        $dataArr2 = array();
        $dataArr2['query'] = 'test';
        $companyID = $this->common_data['company_data']['company_id'];
        $search_string = "%" . $_GET['query'] . "%";

        $commitmentID = isset($_GET['commitmentID']) && $_GET['commitmentID'] != '' ? $_GET['commitmentID'] : '';
        if ($commitmentID == '') {
            $itemcat = $this->db->query("SELECT itemCategoryID FROM `srp_erp_itemcategory` WHERE `companyID` = '{$companyID}' AND `description` = 'Inventory' ")->row_array();
            $data = $this->db->query('SELECT mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT(itemDescription, " (" ,itemSystemCode,")") AS "Match" , isSubitemExist FROM srp_erp_itemmaster WHERE mainCategoryID=' . $itemcat['itemCategoryID'] . ' AND  (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR seconeryItemCode LIKE "' . $search_string . '") AND financeCategory != 3 AND companyID = ' . $companyID . ' AND isActive=1 ')->result_array();
        } else {
            $collectionAutoId = $this->input->get('collectionAutoId');
            $master = $this->db->query("select transactionCurrencyID,donorsID from `srp_erp_ngo_donorcollectionmaster` WHERE collectionAutoId={$collectionAutoId} ")->row_array();
            $transactionCurrencyID = $master['transactionCurrencyID'];
            $donorsID = $master['donorsID'];
            $projectID = $this->input->get('projectID');

            $data = $this->db->query('SELECT mainCategoryID, subcategoryID, seconeryItemCode, subSubCategoryID, revanueGLCode, t.itemSystemCode, costGLCode, assteGLCode, defaultUnitOfMeasure, defaultUnitOfMeasureID, t.itemDescription, t.itemAutoID, currentStock, t.companyLocalWacAmount, companyLocalSellingPrice, CONCAT( t.itemDescription, " (", t.itemSystemCode, ")" ) AS "Match", isSubitemExist FROM srp_erp_ngo_commitmentdetails LEFT JOIN srp_erp_ngo_commitmentmasters on srp_erp_ngo_commitmentdetails.commitmentAutoId=srp_erp_ngo_commitmentmasters.commitmentAutoId LEFT JOIN srp_erp_itemmaster t ON srp_erp_ngo_commitmentdetails.itemAutoID = t.itemAutoID AND t.companyID = ' . $companyID . ' AND isActive = 1 WHERE  transactionCurrencyID=' . $transactionCurrencyID . ' AND donorsID=' . $donorsID . ' AND projectID=' . $projectID . ' AND  `type` = 2 AND srp_erp_ngo_commitmentdetails.commitmentAutoId = ' . $commitmentID . ' AND ( t.itemSystemCode LIKE "' . $search_string . '" OR t.itemDescription LIKE "' . $search_string . '" OR t.seconeryItemCode LIKE "' . $search_string . '" ) ')->result_array();

        }

        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['itemSystemCode'], 'itemAutoID' => $val['itemAutoID'], 'currentStock' => $val['currentStock'], 'defaultUnitOfMeasure' => $val['defaultUnitOfMeasure'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'companyLocalSellingPrice' => $val['companyLocalSellingPrice'], 'companyLocalWacAmount' => $val['companyLocalWacAmount'], 'isSubitemExist' => $val['isSubitemExist']);
            }

        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function save_beneficiary_header()
    {
        $this->load->library('sequence');
        $companyID = $this->common_data['company_data']['company_id'];
        $benificiaryID = trim($this->input->post('benificiaryID'));
        $projectID = trim($this->input->post('projectID'));
        $contactID = trim($this->input->post('contactID'));
        $templateType = trim($this->input->post('templateType'));
        $monthlyExpenditure = $this->input->post('monthlyExpenditure');

        //monthly expenditure value
        $me_assitanceName = $this->input->post('me_assitanceName');
        $me_Organization = $this->input->post('me_Organization');
        $me_year = $this->input->post('me_year');

        $date_format_policy = date_format_policy();
        //$beneficiarySystemCode = $this->sequence->sequence_generator('BEN');

        $this->db->select('phoneCountryCodePrimary,phonePrimary');
        $this->db->where('phoneCountryCodePrimary', trim($this->input->post('countryCodePrimary')));
        $this->db->where('phonePrimary', trim($this->input->post('phonePrimary')));
        $this->db->where('companyID', $companyID);
        if ($benificiaryID) {
            $this->db->where('benificiaryID !=', $benificiaryID);
        }
        $this->db->from('srp_erp_ngo_beneficiarymaster');
        $recordExist = $this->db->get()->row_array();
        if (!empty($recordExist)) {
            return array('w', 'Primary Phone Number is already Exist.');
            exit();
        }

        $registeredDate = trim($this->input->post('registeredDate'));
        $dateOfBirth = trim($this->input->post('dateOfBirth'));
        $format_registeredDate = null;
        if (isset($registeredDate) && !empty($registeredDate)) {
            $format_registeredDate = input_format_date($registeredDate, $date_format_policy);
        }
        $format_dateOfBirth = null;
        if (isset($dateOfBirth) && !empty($dateOfBirth)) {
            $format_dateOfBirth = input_format_date($dateOfBirth, $date_format_policy);
        }
        $this->db->trans_start();
        $data['secondaryCode'] = trim($this->input->post('secondaryCode'));
        $data['benificiaryType'] = trim($this->input->post('benificiaryType'));
        $data['projectID'] = $projectID;
        //$data['subProjectID'] = trim($this->input->post('subProjectID'));;
        $data['titleID'] = trim($this->input->post('emp_title'));
        $data['fullName'] = trim($this->input->post('fullName'));
        $data['nameWithInitials'] = trim($this->input->post('nameWithInitials'));
        $data['registeredDate'] = $format_registeredDate;
        $data['dateOfBirth'] = $format_dateOfBirth;
        $data['nameWithInitials'] = trim($this->input->post('nameWithInitials'));
        $data['email'] = trim($this->input->post('email'));
        $data['phoneCountryCodePrimary'] = trim($this->input->post('countryCodePrimary'));
        $data['phoneAreaCodePrimary'] = trim($this->input->post('phoneAreaCodePrimary'));
        $data['phonePrimary'] = trim($this->input->post('phonePrimary'));
        $data['phoneCountryCodeSecondary'] = trim($this->input->post('countryCodeSecondary'));
        $data['phoneAreaCodeSecondary'] = trim($this->input->post('phoneAreaCodeSecondary'));
        $data['phoneSecondary'] = trim($this->input->post('phoneSecondary'));
        $data['countryID'] = trim($this->input->post('countryID'));
        $data['province'] = trim($this->input->post('province'));
        $data['district'] = trim($this->input->post('district'));
        $data['division'] = trim($this->input->post('division'));
        $data['subDivision'] = trim($this->input->post('subDivision'));
        $data['postalCode'] = trim($this->input->post('postalcode'));
        $data['address'] = trim($this->input->post('address'));
        $data['familyDescription'] = trim($this->input->post('familyDescription'));
        $data['NIC'] = trim($this->input->post('nationalIdentityCardNo'));
        $data['familyMembersDetail'] = trim($this->input->post('familyDetail'));
        if ($templateType == 'helpAndNest') {
            $data['ownLandAvailable'] = trim($this->input->post('ownLandAvailable'));
            $data['ownLandAvailableComments'] = trim($this->input->post('ownLandAvailableComments'));
            $data['totalSqFt'] = trim($this->input->post('totalSqFt'));
            $data['totalCost'] = trim($this->input->post('totalCost'));
            $data['reasoninBrief'] = trim($this->input->post('reasoninBrief'));
            $data['totalEstimatedValue'] = trim($this->input->post('totalcostforahouse'));

        }

        //damage Assesment project additional fields
        if ($templateType == 'DamageAssessment') {
            $data['ethnicityID'] = trim($this->input->post('ethnicityID'));
            $data['da_GnDivision'] = trim($this->input->post('da_GnDivision'));
            $data['da_jammiyahDivision'] = trim($this->input->post('da_jammiyahDivision'));
            $data['da_mosque'] = trim($this->input->post('da_mosque'));
            $data['da_meNotes'] = trim($this->input->post('da_meNotes'));
            $data['da_meGovAssistantYN'] = trim($this->input->post('da_meGovAssistantYN'));
            $data['da_meSupportReceivedYN'] = trim($this->input->post('da_meSupportReceivedYN'));
            $data['reasoninBrief'] = trim($this->input->post('reasoninBrief'));
            $data['da_occupationID'] = trim($this->input->post('da_occupationID'));
            $data['da_economicStatus'] = trim($this->input->post('da_economicStatus'));
        }

        if ($benificiaryID) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('benificiaryID', $benificiaryID);
            $update = $this->db->update('srp_erp_ngo_beneficiarymaster', $data);
            if ($update) {
                $projectTable = $this->db->query("select projectID,beneficiaryID from srp_erp_ngo_beneficiaryprojects  WHERE projectID={$projectID} AND beneficiaryID = {$benificiaryID}")->row_array();

                if (empty($projectTable)) {
                    $data_project['projectID'] = $projectID;
                    $data_project['beneficiaryID'] = $benificiaryID;
                    $data_project['companyID'] = $companyID;
                    $data_project['createdUserGroup'] = $this->common_data['user_group'];
                    $data_project['createdPCID'] = $this->common_data['current_pc'];
                    $data_project['createdUserID'] = $this->common_data['current_userID'];
                    $data_project['createdUserName'] = $this->common_data['current_user'];
                    $data_project['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_ngo_beneficiaryprojects', $data_project);
                }

                if ($contactID != '') {
                    $projectWiseDocumentTable = $this->db->query("select * FROM srp_erp_ngo_documentdescriptionforms WHERE beneficiaryID = {$benificiaryID}")->result_array();

                    if (!empty($projectWiseDocumentTable)) {
                        foreach ($projectWiseDocumentTable as $doc) {
                            $dataDocument['DocDesSetID'] = $doc['DocDesSetID'];
                            $dataDocument['DocDesID'] = $doc['DocDesID'];
                            $dataDocument['beneficiaryID'] = $benificiaryID;
                            $dataDocument['projectID'] = $projectID;
                            $dataDocument['FileName'] = $doc['FileName'];
                            $dataDocument['companyID'] = current_companyID();
                            $dataDocument['CreatedPC'] = current_pc();
                            $dataDocument['CreatedUserName'] = current_employee();
                            $dataDocument['CreatedDate'] = current_date();
                            $this->db->insert('srp_erp_ngo_documentdescriptionforms', $dataDocument);
                        }
                    }
                }

                if (!empty($monthlyExpenditure)) {
                    $this->db->delete('srp_erp_ngo_beneficiarymonthlyexpenditure', array('beneficiaryID' => $benificiaryID));
                    foreach ($monthlyExpenditure as $key => $expenditure) {
                        if ($expenditure > 0) {
                            $dataExpenditure['monthlyExpenditureID'] = $key;
                            $dataExpenditure['beneficiaryID'] = $benificiaryID;
                            $dataExpenditure['amount'] = $expenditure;
                            $dataExpenditure['companyID'] = current_companyID();
                            $dataExpenditure['createdPCID'] = current_pc();
                            $dataExpenditure['CreatedUserName'] = current_employee();
                            $dataExpenditure['createdDateTime'] = current_date();
                            $this->db->insert('srp_erp_ngo_beneficiarymonthlyexpenditure', $dataExpenditure);
                        }

                    }
                }

                if (!empty($me_assitanceName)) {
                    $this->db->delete('srp_erp_ngo_beneficiary_othersupportassistance', array('beneficiaryID' => $benificiaryID));
                    foreach ($me_assitanceName as $key => $monthlyExpenditureTable) {
                        if ($me_year[$key] != '') {
                            $data_othersupportassistance['beneficiaryID'] = $benificiaryID;
                            $data_othersupportassistance['assitanceName'] = $monthlyExpenditureTable;
                            $data_othersupportassistance['Organization'] = $me_Organization[$key];
                            $data_othersupportassistance['amount'] = $me_year[$key];
                            $data_othersupportassistance['companyID'] = current_companyID();
                            $data_othersupportassistance['createdPCID'] = current_pc();
                            $data_othersupportassistance['CreatedUserName'] = current_employee();
                            $data_othersupportassistance['createdDateTime'] = current_date();
                            $this->db->insert('srp_erp_ngo_beneficiary_othersupportassistance', $data_othersupportassistance);
                        }


                    }
                }

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();

                    return array('e', 'Beneficiary Update Failed ' /*. $this->db->_error_message()*/);

                } else {
                    $this->db->trans_commit();

                    return array('s', 'Beneficiary Updated Successfully.', $benificiaryID);
                }
            }
        } else {
            //$data['systemCode'] = $beneficiarySystemCode;
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_ngo_beneficiarymaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($last_id) {
                $projectTable = $this->db->query("select projectID,beneficiaryID from srp_erp_ngo_beneficiaryprojects  WHERE projectID={$projectID} AND beneficiaryID = {$last_id}")->row_array();

                if (empty($projectTable)) {
                    $data_project['projectID'] = $projectID;
                    $data_project['beneficiaryID'] = $last_id;
                    $data_project['companyID'] = $companyID;
                    $data_project['createdUserGroup'] = $this->common_data['user_group'];
                    $data_project['createdPCID'] = $this->common_data['current_pc'];
                    $data_project['createdUserID'] = $this->common_data['current_userID'];
                    $data_project['createdUserName'] = $this->common_data['current_user'];
                    $data_project['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_ngo_beneficiaryprojects', $data_project);
                }

                if (!empty($monthlyExpenditure)) {
                    foreach ($monthlyExpenditure as $key => $expenditure) {
                        if ($expenditure > 0) {
                            $dataExpenditure['monthlyExpenditureID'] = $key;
                            $dataExpenditure['beneficiaryID'] = $last_id;
                            $dataExpenditure['amount'] = $expenditure;
                            $dataExpenditure['companyID'] = current_companyID();
                            $dataExpenditure['createdPCID'] = current_pc();
                            $dataExpenditure['CreatedUserName'] = current_employee();
                            $dataExpenditure['createdDateTime'] = current_date();
                            $this->db->insert('srp_erp_ngo_beneficiarymonthlyexpenditure', $dataExpenditure);
                        }

                    }
                }

                if (!empty($me_assitanceName)) {
                    foreach ($me_assitanceName as $key => $monthlyExpenditureTable) {
                        if ($me_year[$key] != '') {
                            $data_othersupportassistance['beneficiaryID'] = $last_id;
                            $data_othersupportassistance['assitanceName'] = $monthlyExpenditureTable;
                            $data_othersupportassistance['Organization'] = $me_Organization[$key];
                            $data_othersupportassistance['amount'] = $me_year[$key];
                            $data_othersupportassistance['companyID'] = current_companyID();
                            $data_othersupportassistance['createdPCID'] = current_pc();
                            $data_othersupportassistance['CreatedUserName'] = current_employee();
                            $data_othersupportassistance['createdDateTime'] = current_date();
                            $this->db->insert('srp_erp_ngo_beneficiary_othersupportassistance', $data_othersupportassistance);
                        }


                    }
                }

                $data_familyDetail['beneficiaryID'] = $last_id;
                $data_familyDetail['name'] = trim($this->input->post('fullName'));
                $data_familyDetail['relationship'] = 0;
                $data_familyDetail['nationality'] = trim($this->input->post('countryID'));
                $data_familyDetail['DOB'] = $format_dateOfBirth;
                $data_familyDetail['idNO'] = trim($this->input->post('nationalIdentityCardNo'));
                //$data_familyDetail['gender'] =  trim($this->input->post('nationalIdentityCardNo'));
                //$data_familyDetail['companyID'] = current_companyID();
                $data_familyDetail['createdPCid'] = current_pc();
                $data_familyDetail['createdUserID'] = current_userID();
                $data_familyDetail['timestamp'] = current_date();
                $this->db->insert('srp_erp_ngo_beneficiaryfamilydetails', $data_familyDetail);

            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Beneficiary Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Beneficiary Saved Successfully.', $last_id);
            }
        }
    }

    function new_beneficiary_type()
    {
        $type = trim($this->input->post('type'));
        $companyID = current_companyID();
        $isExist = $this->db->query("SELECT beneficiaryTypeID FROM srp_erp_ngo_benificiarytypes WHERE companyID={$companyID} AND description='$type' ")->row('beneficiaryTypeID');

        if (isset($isExist)) {
            return array('e', 'This Beneficiary Type is already Exists');
        } else {

            $data = array(
                'description' => $type,
                'companyID' => current_companyID(),
                'createdUserGroup' => $this->common_data['user_group'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdDateTime' => $this->common_data['current_date']
            );
            $this->db->insert('srp_erp_ngo_benificiarytypes', $data);
            if ($this->db->affected_rows() > 0) {
                $titleID = $this->db->insert_id();
                return array('s', 'Beneficiary Type is created successfully.', $titleID);
            } else {
                return array('e', 'Error in Beneficiary Type Creating');
            }
        }

    }

    function load_beneficiary_header()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(registeredDate,\'' . $convertFormat . '\') AS registeredDate,DATE_FORMAT(dateOfBirth,\'' . $convertFormat . '\') AS dateOfBirth');
        $this->db->where('benificiaryID', $this->input->post('benificiaryID'));
        $this->db->from('srp_erp_ngo_beneficiarymaster');
        return $this->db->get()->row_array();
    }

    function load_beneficiary_header_helpNest($benificiaryID)
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(registeredDate,\'' . $convertFormat . '\') AS registeredDate,DATE_FORMAT(dateOfBirth,\'' . $convertFormat . '\') AS dateOfBirth');
        $this->db->where('benificiaryID', $benificiaryID);
        $this->db->from('srp_erp_ngo_beneficiarymaster');
        return $this->db->get()->row_array();
    }

    function save_beneficiary_familyDetails()
    {
        $date_format_policy = date_format_policy();
        $dateOfBirth = trim($this->input->post('DOB'));
        $format_dateOfBirth = null;
        if (isset($dateOfBirth) && !empty($dateOfBirth)) {
            $format_dateOfBirth = input_format_date($dateOfBirth, $date_format_policy);
        }
        $data['beneficiaryID'] = trim($this->input->post('benificiaryID'));
        $data['name'] = trim($this->input->post('name'));
        $data['relationship'] = trim($this->input->post('relationshipType'));
        $data['nationality'] = trim($this->input->post('nationality'));
        $data['DOB'] = $format_dateOfBirth;
        $data['gender'] = trim($this->input->post('gender'));
        $data['idNO'] = trim($this->input->post('idNO'));
        $data['createdUserID'] = trim($this->input->post('createdUserID'));
        $data['createdPCid'] = trim($this->input->post('createdPCid'));
        $data['timestamp'] = trim($this->input->post('timestamp'));

        //Damage Assessment Family Detail
        $data['type'] = trim($this->input->post('familyType'));
        $data['RelatedHHHead'] = trim($this->input->post('RelatedHHHead'));
        $data['schoolID'] = trim($this->input->post('schoolID'));
        $data['schoolGrade'] = trim($this->input->post('schoolGrade'));
        $data['classRank'] = trim($this->input->post('schoolRank'));
        $data['makthabID'] = trim($this->input->post('makthabID'));
        $data['makthabGrade'] = trim($this->input->post('makthabGrade'));
        $data['remarks'] = trim($this->input->post('familyremarks'));
        $data['occupationID'] = trim($this->input->post('occupationID'));
        $data['Disability'] = trim($this->input->post('Disability'));

        $result = $this->db->insert('srp_erp_ngo_beneficiaryfamilydetails', $data);
        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'Family detail added successfully'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Error, Insert Error, Please contact your system support team'));
        }
    }

    function fetch_beneficiary_family_details($benificiaryID)
    {
        $this->db->select("*,srp_erp_ngo_beneficiaryfamilydetails.name as name,r.relationship as relationshipDesc,c.Nationality as countryName,g.name as genderDesc");
        $this->db->from("srp_erp_ngo_beneficiaryfamilydetails");
        $this->db->join("srp_erp_family_relationship r", "r.relationshipID=srp_erp_ngo_beneficiaryfamilydetails.relationship", "left");
        $this->db->join("srp_nationality c", "c.NId = srp_erp_ngo_beneficiaryfamilydetails.nationality", "left");
        $this->db->join("srp_erp_gender g", "g.genderID = srp_erp_ngo_beneficiaryfamilydetails.gender", "left");
        $this->db->where("beneficiaryID", $benificiaryID);
        $this->db->where("srp_erp_ngo_beneficiaryfamilydetails.relationship !=", 0);
        $output = $this->db->get()->result_array();

        return $output;
    }

    function xeditable_update($tableName, $pkColumn)
    {
        $column = $this->input->post('name');
        $value = $this->input->post('value');
        $pk = $this->input->post('pk');
        switch ($column) {
            case 'DOB_O':
            case 'dateAssumed_O':
            case 'endOfContract_O':
            case 'SLBSeniority_O':
            case 'WSISeniority_O':
            case 'passportExpireDate_O':
            case 'VisaexpireDate_O':
            case 'coverFrom_O':
                $value = format_date_mysql_datetime($value);
                break;
        }

        $table = $tableName;
        $data = array($column => $value);
        $this->db->where($pkColumn, $pk);
        $result = $this->db->update($table, $data);
        //echo $this->db->last_query();
        return $result;
    }

    function delete_beneficiary_familydetail()
    {
        $this->db->delete('srp_erp_ngo_beneficiaryfamilydetails', array('empfamilydetailsID' => trim($this->input->post('empfamilydetailsID'))));
        return array('s', 'Deleted Successfully');
    }

    function delete_master_notes_allDocuments()
    {
        $this->db->where('notesID', $this->input->post('notesID'));
        $results = $this->db->delete('srp_erp_ngo_notes');
        return true;
    }

    function add_beneficiary_notes()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];

        $benificiaryID = trim($this->input->post('benificiaryID'));

        $data['documentID'] = 5;
        $data['documentAutoID'] = $benificiaryID;
        $data['description'] = trim($this->input->post('description'));
        $data['companyID'] = $companyID;
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $this->db->insert('srp_erp_ngo_notes', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', 'Beneficiary Note Save Failed ' . $this->db->_error_message(), $last_id);
        } else {
            $this->db->trans_commit();

            return array('s', 'Beneficiary Note Added Successfully.');

        }
    }

    function beneficiary_image_upload()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->trans_start();
        $output_dir = "uploads/NGO/beneficiaryImage/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/NGO", 007);
            mkdir("uploads/NGO/beneficiaryImage", 007);
        }
        $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'Beneficiary_' . $companyID . '_' . trim($this->input->post('benificiaryID')) . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['benificiaryImage'] = $fileName;

        $this->db->where('benificiaryID', trim($this->input->post('benificiaryID')));
        $this->db->update('srp_erp_ngo_beneficiarymaster', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', "Image Upload Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Image uploaded  Successfully.');
        }
    }

    function beneficiary_image_upload_helpNest()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->trans_start();
        $output_dir = "uploads/NGO/beneficiaryProjectImage/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/NGO", 007);
            mkdir("uploads/NGO/beneficiaryProjectImage", 007);
        }
        $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'HelpAndNest_' . $companyID . '_' . trim($this->input->post('benificiaryID')) . '_' . time() . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['helpAndNestImage'] = $fileName;

        $this->db->where('benificiaryID', trim($this->input->post('benificiaryID')));
        $this->db->update('srp_erp_ngo_beneficiarymaster', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', "Image Upload Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Image uploaded Successfully.');
        }
    }

    function new_beneficiary_province()
    {
        $stateID = trim($this->input->post('hd_province_stateID'));
        $countyID = trim($this->input->post('hd_province_countryID'));
        $description = trim($this->input->post('province_description'));
        $shortCode = trim($this->input->post('province_shortCode'));

        // check description already exist
        $this->db->select('stateID,Description');
        $this->db->where('countyID', $countyID);
        $this->db->where('Description', $description);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $isExist = $this->db->get()->row_array();
        if (!empty($isExist)) {
            return array('e', 'Province is already Exists.');
            exit();
        }

        // check short Code already exist
        $this->db->select('stateID,Description,shortCode');
        $this->db->where('countyID', $countyID);
        $this->db->where('shortCode', $shortCode);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $isExist = $this->db->get()->row_array();
        if (!empty($isExist)) {
            return array('e', 'Short Code is already Exists.');
            exit();
        }

        $data['Description'] = $description;
        $data['countyID'] = $countyID;
        $data['shortCode'] = $shortCode;
        $data['type'] = 1;

        if ($stateID) {

            $this->db->where('stateID', $stateID);
            $this->db->update('srp_erp_statemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Province Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Province Updated Successfully.');
            }
        } else {
            $this->db->insert('srp_erp_statemaster', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Province is created successfully.');
            } else {
                return array('e', 'Error in Province Creating');
            }
        }


    }

    function new_beneficiary_district()
    {
        $stateID = trim($this->input->post('hd_district_stateID'));
        $countyID = trim($this->input->post('hd_district_countryID'));
        $description = trim($this->input->post('district_description'));
        $shortCode = trim($this->input->post('district_shortCode'));
        $province = trim($this->input->post('hd_district_provinceID'));

        // check description already exist
        $this->db->select('stateID,Description');
        $this->db->where('countyID', $countyID);
        $this->db->where('masterID', $province);
        $this->db->where('Description', $description);
        $this->db->where('type', 2);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $isExist = $this->db->get()->row_array();

        if (isset($isExist)) {
            return array('e', 'District is already Exists');
            exit();
        }

        // check short Code already exist
        $this->db->select('stateID,Description,shortCode');
        $this->db->where('countyID', $countyID);
        $this->db->where('masterID', $province);
        $this->db->where('shortCode', $shortCode);
        $this->db->where('type', 2);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $isExist = $this->db->get()->row_array();

        if (isset($isExist)) {
            return array('e', 'Short Code is already Exists.');
            exit();
        }

        $data['Description'] = $description;
        $data['countyID'] = $countyID;
        $data['shortCode'] = $shortCode;
        $data['type'] = 2;
        $data['masterID'] = $province;
        if ($stateID) {
            $this->db->where('stateID', $stateID);
            $this->db->update('srp_erp_statemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'District Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'District Updated Successfully.');
            }
        } else {
            $this->db->insert('srp_erp_statemaster', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'District is created successfully.');
            } else {
                return array('e', 'Error in District Creating');
            }
        }

    }

    function new_beneficiary_division()
    {
        $stateID = trim($this->input->post('hd_division_stateID'));
        $countyID = trim($this->input->post('hd_division_countryID'));
        $description = trim($this->input->post('division_description'));
        $district = trim($this->input->post('hd_division_districtID'));
        $shortCode = trim($this->input->post('division_shortCode'));
        $divisionTypeCode = trim($this->input->post('divisionTypeCode'));

        // check description already exist
        $this->db->select('stateID,Description');
        $this->db->where('countyID', $countyID);
        $this->db->where('masterID', $district);
        $this->db->where('Description', $description);
        $this->db->where('type', 3);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $divisionisExist = $this->db->get()->row_array();
        if (isset($divisionisExist)) {
            return array('e', 'Division is already Exists');
        }

        // check short Code already exist
        $this->db->select('stateID,Description,shortCode');
        $this->db->where('countyID', $countyID);
        $this->db->where('masterID', $district);
        $this->db->where('shortCode', $shortCode);
        $this->db->where('type', 3);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $division_shortCodeisExist = $this->db->get()->row_array();
        if (isset($division_shortCodeisExist)) {
            return array('e', 'Short Code is already Exists.');
        }

        $data['Description'] = $description;
        $data['countyID'] = $countyID;
        $data['shortCode'] = $shortCode;
        $data['type'] = 3;
        $data['masterID'] = $district;
        $data['divisionTypeCode'] = $divisionTypeCode;

        if ($stateID) {
            $this->db->where('stateID', $stateID);
            $this->db->update('srp_erp_statemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Division Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Division Updated Successfully.');
            }
        } else {
            $this->db->insert('srp_erp_statemaster', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Division is created successfully.');
            } else {
                return array('e', 'Error in Division Creating');
            }
        }

    }

    function new_beneficiary_sub_division()
    {
        $divisionTypeCode = trim($this->input->post('divisionTypeCode'));
        $stateID = trim($this->input->post('hd_sub_division_stateID'));
        $countyID = trim($this->input->post('hd_sub_division_countryID'));
        $description = trim($this->input->post('sub_division_description'));
        $district = trim($this->input->post('hd_sub_division_districtID'));
        $shortCode = trim($this->input->post('sub_division_shortCode'));

        // check description already exist
        $this->db->select('stateID,Description');
        $this->db->where('countyID', $countyID);
        $this->db->where('masterID', $district);
        $this->db->where('Description', $description);
        $this->db->where('type', 4);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $isExist = $this->db->get()->row_array();
        if (isset($isExist)) {
            return array('e', 'Sub Division is already Exists');
        }

        // check short Code already exist
        $this->db->select('stateID,shortCode');
        $this->db->where('countyID', $countyID);
        $this->db->where('masterID', $district);
        $this->db->where('shortCode', $shortCode);
        $this->db->where('type', 4);
        if ($stateID) {
            $this->db->where('stateID !=', $stateID);
        }
        $this->db->from('srp_erp_statemaster');
        $isExist = $this->db->get()->row_array();
        if (isset($isExist)) {
            return array('e', 'Short Code is already Exists.');
        }

        $data['Description'] = $description;
        $data['countyID'] = $countyID;
        $data['shortCode'] = $shortCode;
        $data['type'] = 4;
        $data['masterID'] = $district;
        $data['divisionTypeCode'] = $divisionTypeCode;

        if ($stateID) {
            $this->db->where('stateID', $stateID);
            $this->db->update('srp_erp_statemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Sub Division Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Sub Division Updated Successfully.');
            }
        } else {
            $this->db->insert('srp_erp_statemaster', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Sub Division is created successfully.');
            } else {
                return array('e', 'Error in Sub Division Creating');
            }
        }

    }

    function delete_beneficiary_master()
    {
        $benificiaryID = trim($this->input->post('benificiaryID'));
        $this->db->where('documentID', 5);
        $this->db->where('documentAutoID', $benificiaryID);
        $this->db->delete('srp_erp_ngo_notes');

        $this->db->delete('srp_erp_ngo_beneficiarymaster', array('benificiaryID' => trim($this->input->post('benificiaryID'))));

        return TRUE;
    }

    function save_ngo_document_Master()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $description = $this->input->post('description');
        $sortOrder = $this->input->post('sortOrder');

        $whereIN = "( '" . join(" , ", $description) . "' )";

        $isExist = $this->db->query("SELECT DocDescription FROM srp_erp_ngo_documentdescriptionmaster WHERE DocDescription IN " . $whereIN . " AND companyID = $companyID ")->result_array();

        if (empty($isExist)) {

            $this->db->trans_start();

            foreach ($description as $key => $des) {

                $data_master = array(
                    'DocDescription' => $des,
                    'SortOrder' => $sortOrder[$key],
                    'companyID' => $companyID,
                    'CreatedPC' => current_pc(),
                    'CreatedUserName' => current_employee(),
                    'CreatedDate' => current_date()
                );

                $this->db->insert('srp_erp_ngo_documentdescriptionmaster', $data_master);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Documents Created Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in Document Create process');
            }
        } else {
            return array('e', 'Already Document Exist');
        }
    }

    function save_ngo_document_setupMaster()
    {
        $ngoDocumentID = $this->input->post('documentID');
        $ngoProjectID = $this->input->post('ngoProjectID');
        $description = $this->input->post('descriptionID');
        $isRequired = $this->input->post('isRequired');
        $sortOrder = $this->input->post('sortOrder');

        $whereIN = "( '" . join(" , ", $description) . "' )";

        //$isExist = $this->db->query("SELECT DocDesID FROM srp_erp_ngo_documentdescriptionsetup WHERE DocDesID IN " . $whereIN . " AND companyID = $companyID ")->result_array();

        $this->db->trans_start();

        foreach ($description as $key => $des) {

            if (!empty($isRequired)) {
                $thisRequired = (array_key_exists($key, $isRequired)) ? $isRequired[$key] : 0;
            } else {
                $thisRequired = 0;
            }

            $data_setup = array(
                'DocDesID' => $des,
                'FormType' => '',
                'isMandatory' => $thisRequired,
                'projectID' => $ngoProjectID[$key],
                'ngoDocumentID' => $ngoDocumentID[$key],
                'SortOrder' => $sortOrder[$key],
                'companyID' => current_companyID(),
                'CreatedPC' => current_pc(),
                'CreatedUserName' => current_employee(),
                'CreatedDate' => current_date()
            );

            $this->db->insert('srp_erp_ngo_documentdescriptionsetup', $data_setup);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Created Successfully.');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in process');
        }
    }

    function delete_ngo_document_master()
    {
        $DocDesID = trim($this->input->post('DocDesID'));
        $companyID = $this->common_data['company_data']['company_id'];

        // Check is there any document used
        $isInUse = $this->db->query("SELECT DocDesID FROM srp_erp_ngo_documentdescriptionforms WHERE DocDesID = {$DocDesID} AND companyID = $companyID ")->result_array();

        if (empty($isInUse)) {
            $this->db->trans_start();

            $this->db->where('DocDesID', $DocDesID)->delete('srp_erp_ngo_documentdescriptionmaster');

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Records deleted successfully');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in deleting process');
            }
        } else {
            return array('e', 'This Document is in use you cannot Delete !');
        }
    }

    function update_ngo_document_master()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $description = $this->input->post('edit_description');
        $sortOrder = $this->input->post('edit_sortOrder');
        $docID = $this->input->post('hidden-id');

        $isExist = $this->db->query("SELECT DocDescription FROM srp_erp_ngo_documentdescriptionmaster WHERE DocDescription='$description' AND DocDesID!={$docID} AND companyID=$companyID")->row_array();

        if (empty($isExist)) {

            $this->db->trans_start();

            $data = array(
                'DocDescription' => $description,
                'SortOrder' => $sortOrder,
                'ModifiedPC' => current_pc(),
                'ModifiedUserName' => current_employee(),
            );

            $this->db->where('DocDesID', $docID)->where('companyID', $companyID)
                ->update('srp_erp_ngo_documentdescriptionmaster', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Updated Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in update process');
            }
        } else {
            return array('e', $description . ' is already Exists');
        }
    }

    function update_ngo_document_setup()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $ngoDocumentID = $this->input->post('edit_documentID');
        $ngoProjectID = $this->input->post('edit_ngoProjectID');
        $documentID = $this->input->post('edit_descriptionID');
        $sortOrder = $this->input->post('edit_sortOrder');
        $DocDesSetupID = $this->input->post('hidden-id');
        $isRequired = $this->input->post('edit_isRequired');

        $isExist = $this->db->query("SELECT DocDesID FROM srp_erp_ngo_documentdescriptionsetup WHERE DocDesID = {$documentID} AND ngoDocumentID = {$ngoDocumentID} AND projectID = {$ngoProjectID} AND DocDesSetupID != {$DocDesSetupID} AND companyID = $companyID")->row_array();

        if (empty($isExist)) {

            $this->db->trans_start();

            $data = array(
                'DocDesID' => $documentID,
                'projectID' => $ngoProjectID,
                'ngoDocumentID' => $ngoDocumentID,
                'SortOrder' => $sortOrder,
                'isMandatory' => ($isRequired == 1) ? $isRequired : 0,
                'ModifiedPC' => current_pc(),
                'ModifiedUserName' => current_employee(),
            );

            $this->db->where('DocDesSetupID', $DocDesSetupID)->where('companyID', $companyID)
                ->update('srp_erp_ngo_documentdescriptionsetup', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Updated Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in update process');
            }
        } else {
            return array('e', 'Document is already Exists');
        }
    }

    function load_beneficiary_documents()
    {
        $companyID = current_companyID();
        $benificiaryID = $this->input->post('benificiaryID');
        $projectID = $this->input->post('projectID');
        return $this->db->query("SELECT MASTER.DocDesID AS DcoumentAutoID,DocDesFormID, DocDescription, isMandatory, FileName
                                 FROM srp_erp_ngo_documentdescriptionmaster master
                                 JOIN srp_erp_ngo_documentdescriptionsetup setup ON setup.DocDesID = master.DocDesID
                                 INNER JOIN srp_erp_ngo_documentdescriptionforms forms ON forms.DocDesID = master.DocDesID
                                 AND beneficiaryID = {$benificiaryID} AND forms.projectID = {$projectID}
                                 WHERE master.companyID={$companyID} AND isDeleted=0 GROUP BY MASTER.DocDesID")->result_array();
    }

    function save_beneficiary_document()
    {
        $companyID = current_companyID();
        $benificiaryID = $this->input->post('benificiaryID');
        $projectID = $this->input->post('projectID');
        $documentID = $this->input->post('document');

        //Check is there is a document with this document ID for this employee
        /*        $where = array('DocDesID' => $documentID, 'PersonID' => $empID, 'PersonType' => 'E');
                $isExisting = $this->db->where($where)->select('DocDesID')->from('srp_documentdescriptionforms')->get()->row('DocDesID');*/

        $isExisting = $this->db->query("SELECT DocDesFormID FROM srp_erp_ngo_documentdescriptionforms WHERE beneficiaryID = {$benificiaryID} AND DocDesID = {$documentID} AND companyID = $companyID")->row_array();

        if (!empty($isExisting)) {
            return ['e', 'This document has been updated already.<br/>Please delete the document and try again.'];
        }


        $path = UPLOAD_PATH_POS . 'documents/ngo/'; // imagePath();
        if (!file_exists($path)) {
            mkdir("documents/ngo", 007);
        }
        $fileName = str_replace(' ', '', strtolower($benificiaryID)) . '_' . time();
        $config['upload_path'] = $path;
        $config['allowed_types'] = '*';/*doc|docx|pdf|xls|xlsx|xlsxm|txt*/
        $config['max_size'] = '5120'; // 5 MB
        $config['file_name'] = $fileName;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload("doc_file")) {
            return array('e', 'Upload failed ' . $this->upload->display_errors());
        } else {

            //Get document Setup ID
            $setUpID = $this->db->query("SELECT DocDesSetupID FROM srp_erp_ngo_documentdescriptionsetup WHERE DocDesID={$documentID}
                                       AND companyID={$companyID} ")->row('DocDesSetupID');

            $data = array(
                'DocDesSetID' => $setUpID,
                'DocDesID' => $documentID,
                'beneficiaryID' => $benificiaryID,
                'projectID' => $projectID,
                'FileName' => $this->upload->data('file_name'),
                'companyID' => current_companyID(),
                'CreatedPC' => current_pc(),
                'CreatedUserName' => current_employee(),
                'CreatedDate' => current_date()
            );

            $this->db->insert('srp_erp_ngo_documentdescriptionforms', $data);

            if ($this->db->affected_rows() > 0) {
                return array('s', 'Document successfully uploaded');
            } else {
                return array('e', 'Error in document upload');
            }

        }
    }

    function delete_beneficiary_document()
    {
        $DocDesFormID = trim($this->input->post('DocDesFormID'));
        $this->db->where('DocDesFormID', $DocDesFormID)->delete('srp_erp_ngo_documentdescriptionforms');

        if ($this->db->affected_rows() > 0) {
            return array('s', 'Document deleted successfully');
        } else {
            return array('e', 'Error in document delete function');
        }

    }

    function delete_ngo_document_setup()
    {
        $DocDesSetupID = trim($this->input->post('DocDesSetupID'));
        $companyID = $this->common_data['company_data']['company_id'];

        // Check is there any document used
        $isInUse = $this->db->query("SELECT DocDesFormID FROM srp_erp_ngo_documentdescriptionforms WHERE DocDesSetID = {$DocDesSetupID} AND companyID = $companyID ")->result_array();

        if (empty($isInUse)) {
            $this->db->trans_start();

            $this->db->where('DocDesSetupID', $DocDesSetupID)->delete('srp_erp_ngo_documentdescriptionsetup');

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Records deleted successfully');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in deleting process');
            }
        } else {
            return array('e', 'This Document is in use you cannot Delete !');
        }
    }

    function load_ngo_all_countries()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_countrymaster');
        $result = $this->db->get()->result_array();
        return $result;
    }

    function beneficiary_confirmation()
    {
        $benificiaryID = trim($this->input->post('benificiaryID'));
        $projectID = trim($this->input->post('projectID'));
        $this->db->select('beneficiaryProjectID');
        $this->db->where('beneficiaryID', $benificiaryID);
        $this->db->where('projectID', $benificiaryID);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_ngo_beneficiaryprojects');
        $checkProjectConfirmed = $this->db->get()->row_array();

        $isInUse = $this->db->query("SELECT
	ddm.DocDescription as documentName
FROM
	srp_erp_ngo_documentdescriptionsetup dds
	INNER JOIN srp_erp_ngo_documentdescriptionmaster ddm ON dds.DocDesID = ddm.DocDesID
WHERE dds.DocDesID
	NOT IN (
		SELECT
			DocDesID
		FROM
			srp_erp_ngo_documentdescriptionforms  ddf
		WHERE
			ddf.projectID = {$projectID} AND ddf.beneficiaryID = {$benificiaryID}
	) AND dds.projectID = {$projectID} AND ngoDocumentID = 5 AND isMandatory = 1")->result_array();

        if (!empty($checkProjectConfirmed)) {
            return array('e', 'Beneficiary already confirmed');
        } else if (!empty($isInUse)) {
            return array('e', 'Mandatory Documents are need to upload');
        } else {

            $checkisConfirmedProjectExist = $this->db->query("select beneficiaryProjectID from srp_erp_ngo_beneficiaryprojects WHERE beneficiaryID={$benificiaryID} AND confirmedYN = 1 ")->row_array();

            if (empty($checkisConfirmedProjectExist)) {
                $benGeneratedID = $this->beneficiary_system_code_generator($benificiaryID);
                $data = array(
                    'systemCode' => $benGeneratedID,
                    'confirmedYN' => 1,
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                    'confirmedByName' => $this->common_data['current_user']);
                $this->db->where('benificiaryID', $benificiaryID);
                $this->db->update('srp_erp_ngo_beneficiarymaster', $data);
            }

            $data_project['confirmedYN'] = 1;
            $data_project['confirmedDate'] = date('Y-m-d H:i:s');
            $data_project['confirmedByEmpID'] = $this->common_data['current_user'];
            $data_project['confirmedByName'] = $this->common_data['current_user'];
            $this->db->where('projectID', $projectID);
            $this->db->where('beneficiaryID', $benificiaryID);
            $this->db->update('srp_erp_ngo_beneficiaryprojects', $data_project);
            return array('s', 'Beneficiary : Confirmed Successfully. ');
        }
    }

    function delete_beneficiaryTypes()
    {
        $beneficiaryTypeID = trim($this->input->post('beneficiaryTypeID'));
        $this->db->delete('srp_erp_ngo_benificiarytypes', array('beneficiaryTypeID' => $beneficiaryTypeID));
        return true;
    }

    function save_beneficiaryTypes_header()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];

        $beneficiaryTypeID = trim($this->input->post('beneficiaryTypeID'));
        $description = trim($this->input->post('description'));

        $this->db->select('beneficiaryTypeID,description');
        $this->db->where('companyID', $companyID);
        $this->db->where('description', $description);
        if ($beneficiaryTypeID) {
            $this->db->where('beneficiaryTypeID !=', $beneficiaryTypeID);
        }
        $this->db->from('srp_erp_ngo_benificiarytypes');
        $recordExist = $this->db->get()->row_array();
        if (!empty($recordExist)) {
            return array('e', 'Types is already Exist.');
            exit();
        } else {

            $data['description'] = $description;
            if ($beneficiaryTypeID) {
                $this->db->where('beneficiaryTypeID', $beneficiaryTypeID);
                $this->db->update('srp_erp_ngo_benificiarytypes', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Error in Beneficiary Types Update ' . $this->db->_error_message());

                } else {
                    $this->db->trans_commit();
                    return array('s', 'Beneficiary Types Updated Successfully.');
                }
            } else {
                $data['companyID'] = $companyID;
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_ngo_benificiarytypes', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Error in Beneficiary Types Creating' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Beneficiary Types created successfully.');

                }
            }
        }
    }

    function load_beneficiaryTypes_header()
    {
        $beneficiaryTypeID = trim($this->input->post('beneficiaryTypeID'));
        $data = $this->db->query("select beneficiaryTypeID,description FROM srp_erp_ngo_benificiarytypes  WHERE beneficiaryTypeID = {$beneficiaryTypeID} ")->row_array();
        return $data;
    }

    function load_ngo_area_setupDetail()
    {
        $stateID = trim($this->input->post('stateID'));
        $data = $this->db->query("select * FROM srp_erp_statemaster WHERE stateID = {$stateID} ")->row_array();
        return $data;
    }

    function beneficiary_system_code_generator($benificiaryID)
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $benificiaryDetail = $this->db->query("SELECT benificiaryID,district,countryID,province,division,subDivision FROM srp_erp_ngo_beneficiarymaster WHERE benificiaryID = " . $benificiaryID . "")->row_array();

        if (!empty($benificiaryDetail['countryID'] && $benificiaryDetail['province'] && $benificiaryDetail['district'] && $benificiaryDetail['division'] && $benificiaryDetail['subDivision'])) {
            $benificiarySubDivision = $this->db->query("SELECT count(subDivision) as divisionTotal FROM srp_erp_ngo_beneficiarymaster WHERE companyID = " . $companyID . " AND subDivision = {$benificiaryDetail['subDivision']} AND confirmedYN = 1")->row_array();

            if ($benificiarySubDivision) {
                $subdivisionAreaCount = $benificiarySubDivision['divisionTotal'] + 1;
            } else {
                $subdivisionAreaCount = 1;
            }
            $countryCode = fetch_ngo_countryMaster_code($benificiaryDetail['countryID']);
            $provinceCode = fetch_ngo_stateMaster_name($benificiaryDetail['province']);
            $districtCode = fetch_ngo_stateMaster_name($benificiaryDetail['district']);
            $divisionCode = fetch_ngo_stateMaster_name($benificiaryDetail['division']);
            $subDivisionCode = fetch_ngo_stateMaster_name($benificiaryDetail['subDivision']);

            $benificiaryGeneratedCode = $countryCode . "/" . $provinceCode . "/" . $districtCode . "/" . $divisionCode . "/" . $subDivisionCode . "/" . $subdivisionAreaCount;

            return $benificiaryGeneratedCode;

        } else {
        }

    }

    function fetch_beneficiary_relate_search()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $search_string = "%" . $_GET['query'] . "%";
        $projectID = $_GET['projectID'];
        $dataArr = array();
        $dataArr2 = array();

        if (!empty($search_string)) {
            $data = $this->db->query('SELECT benificiaryID,secondaryCode,benificiaryType,titleID,nameWithInitials,fullName,email,countryID,province,division,subDivision,district,postalCode,address,phoneCountryCodePrimary,phoneAreaCodePrimary,phonePrimary,DATE_FORMAT(registeredDate,\'' . $convertFormat . '\') AS registeredDate,DATE_FORMAT(dateOfBirth,\'' . $convertFormat . '\') AS dateOfBirth,CONCAT(fullName," ", systemCode) AS "Match" FROM srp_erp_ngo_beneficiarymaster bm LEFT JOIN srp_erp_ngo_beneficiaryprojects bp ON bm.benificiaryID = bp.beneficiaryID WHERE bm.companyID = "' . $companyID . '" AND bm.confirmedYN = 1 AND bp.projectID != ' . $projectID . ' AND (fullName LIKE "' . $search_string . '" OR systemCode LIKE "' . $search_string . '") ')->result_array();
            //echo $this->db->last_query();
            if (!empty($data)) {
                foreach ($data as $val) {
                    $dataArr[] = array(
                        'benificiaryID' => $val["benificiaryID"],
                        'value' => $val["Match"],
                        'secondaryCode' => $val['secondaryCode'],
                        'benificiaryType' => $val['benificiaryType'],
                        'titleID' => $val['titleID'],
                        'dateOfBirth' => $val['dateOfBirth'],
                        'registeredDate' => $val['registeredDate'],
                        'nameWithInitials' => $val['nameWithInitials'],
                        'fullName' => $val['fullName'],
                        'email' => $val['email'],
                        'countryID' => $val['countryID'],
                        'province' => $val['province'],
                        'division' => $val['division'],
                        'subDivision' => $val['subDivision'],
                        'district' => $val['district'],
                        'postalCode' => $val['postalCode'],
                        'address' => $val['address'],
                        'phoneCountryCodePrimary' => $val['phoneCountryCodePrimary'],
                        'phoneAreaCodePrimary' => $val['phoneAreaCodePrimary'],
                        'phonePrimary' => $val['phonePrimary']
                    );
                }
                $dataArr2['suggestions'] = $dataArr;
            }
            return $dataArr2;
        }
    }

    function fetch_ngo_sub_projects()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('ngoProjectID,description');
        $this->db->from('srp_erp_ngo_projects');
        $this->db->where('masterID', $this->input->post("ngoProjectID"));
        $this->db->where('srp_erp_ngo_projects.companyID', $companyID);
        $master = $this->db->get()->result_array();
        return $master;

    }


    function save_project_proposal_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $companyID = $this->common_data['company_data']['company_id'];
        $proposalID = trim($this->input->post('proposalID'));
        $company_code = $this->common_data['company_data']['company_code'];
        $documentDate = $this->input->post('documentDate');
        $startDate = $this->input->post('startDate');
        $endDate = $this->input->post('endDate');
        $typeofpro = trim($this->input->post('typepro'));

        $format_documentDate = null;
        if (isset($documentDate) && !empty($documentDate)) {
            $format_documentDate = input_format_date($documentDate, $date_format_policy);
        }
        $format_startDate = null;
        if (isset($startDate) && !empty($startDate)) {
            $format_startDate = input_format_date($startDate, $date_format_policy);
        }
        $format_endDate = null;
        if (isset($endDate) && !empty($endDate)) {
            $format_endDate = input_format_date($endDate, $date_format_policy);
        }
        if ($proposalID) {
            $data['proposalName'] = trim($this->input->post('proposalName'));
            $data['proposalTitle'] = trim($this->input->post('proposalTitle'));
            $data['projectID'] = trim($this->input->post('projectID'));
            //$data['projectSubID'] = trim($this->input->post('subProjectID'));
            // $data['status'] = trim($this->input->post('status'));
            $data['projectSummary'] = trim($this->input->post('projectSummary'));
            $data['detailDescription'] = $this->input->post('detailDescription');
            $data['processDescription'] = $this->input->post('processDescription');
            $data['bankGLAutoID'] = $this->input->post('bankGLAutoID');
            $data['DocumentDate'] = $format_documentDate;
            $data['startDate'] = $format_startDate;
            $data['endDate'] = $format_endDate;
            $data['totalNumberofHouses'] = trim($this->input->post('totalNumberofHouses'));
            $data['floorArea'] = trim($this->input->post('floorArea'));
            $data['costofhouse'] = trim($this->input->post('costofhouse'));
            $data['additionalCost'] = trim($this->input->post('additionalCost'));
            $data['EstimatedDays'] = trim($this->input->post('EstimatedDays'));
            $data['contractorID'] = trim($this->input->post('contractorID'));
            $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID'));
            $data['transactionExchangeRate'] = 1;
            $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
            $data['companyLocalExchangeRate'] = $default_currency['conversion'];
            $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $data['countryID'] = trim($this->input->post('countryID'));
            $data['provinceID'] = trim($this->input->post('province'));
            $data['areaID'] = trim($this->input->post('district'));
            $data['divisionID'] = trim($this->input->post('division'));
            $data['type'] = $typeofpro;
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['proposalStageID'] = 1;
            $data['status'] = 12;
            $this->db->where('proposalID', $proposalID);
            $this->db->update('srp_erp_ngo_projectproposals', $data);


            if ($typeofpro == 2) {
                $datass['description'] = $this->input->post('detailDescription');
                $datass['projectName'] =  trim($this->input->post('proposalName'));
                $datass['contractorID'] =  trim($this->input->post('contractorIDproject'));
                $datass['totalProjectValue'] =  trim($this->input->post('totalprojectcost'));
                $datass['startDate'] = $format_startDate;
                $datass['endDate'] = $format_endDate;
                $datass['modifiedPCID'] = $this->common_data['current_pc'];
                $datass['modifiedUserID'] = $this->common_data['current_userID'];
                $datass['modifiedUserName'] = $this->common_data['current_user'];
                $datass['modifiedDateTime'] = $this->common_data['current_date'];
                $this->db->where('proposalID', $proposalID);
                $this->db->update('srp_erp_ngo_projects', $datass);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Project Updated Failed ' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Project Updated Successfully.', $proposalID);

                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Project Proposal Update Failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Project Proposal Updated Successfully.', $proposalID);

            }
        } else {
            $data['proposalName'] = trim($this->input->post('proposalName'));
            $data['proposalTitle'] = trim($this->input->post('proposalTitle'));
            $data['projectID'] = trim($this->input->post('projectID'));
            //$data['projectSubID'] = trim($this->input->post('subProjectID'));
            //$data['status'] = trim($this->input->post('status'));
            $data['projectSummary'] = trim($this->input->post('projectSummary'));
            $data['detailDescription'] = $this->input->post('detailDescription');
            $data['processDescription'] = $this->input->post('processDescription');
            $data['bankGLAutoID'] = $this->input->post('bankGLAutoID');
            $data['DocumentDate'] = $format_documentDate;
            $data['startDate'] = $format_startDate;
            $data['endDate'] = $format_endDate;
            $data['totalNumberofHouses'] = trim($this->input->post('totalNumberofHouses'));
            $data['floorArea'] = trim($this->input->post('floorArea'));
            $data['costofhouse'] = trim($this->input->post('costofhouse'));
            $data['additionalCost'] = trim($this->input->post('additionalCost'));
            $data['EstimatedDays'] = trim($this->input->post('EstimatedDays'));
            $data['contractorID'] = trim($this->input->post('contractorID'));
            $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID'));
            $data['transactionExchangeRate'] = 1;
            $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
            $data['companyLocalExchangeRate'] = $default_currency['conversion'];
            $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $data['countryID'] = trim($this->input->post('countryID'));
            $data['provinceID'] = trim($this->input->post('province'));
            $data['areaID'] = trim($this->input->post('district'));
            $data['divisionID'] = trim($this->input->post('division'));
            $data['type'] = $typeofpro;
            $data['documentID'] = 'PRP';
            $data['documentSystemCode'] = $this->sequence->sequence_generator($data['documentID']);
            $data['companyID'] = $companyID;
            $data['companyCode'] = $company_code;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['proposalStageID'] = 1;
            $data['status'] = 12;
            $this->db->insert('srp_erp_ngo_projectproposals', $data);
            $last_id = $this->db->insert_id();
            if ($typeofpro == 2) {
                $datas['description'] = $this->input->post('detailDescription');
                $datas['levelNo'] = 1;
                $datas['companyID'] = $companyID;

                $this->load->library('approvals');
                $this->db->select('*');
                $this->db->where('proposalID', $last_id);
                $this->db->from('srp_erp_ngo_projectproposals');
                $master = $this->db->get()->row_array();
                $approvals_status = $this->approvals->AutoApprovalProject('PRP', $master['proposalID'], $master['documentSystemCode'], ' Project Proposal', 'srp_erp_ngo_projectproposals', 'proposalID', 1);
                $datas['proposalID'] = $last_id;
                $datas['description'] = $this->input->post('detailDescription');
                $datas['projectName'] =  trim($this->input->post('proposalName'));
                $datas['contractorID'] =  trim($this->input->post('contractorIDproject'));
                $datas['totalProjectValue'] =  trim($this->input->post('totalprojectcost'));
                $datas['startDate'] = $format_startDate;
                $datas['endDate'] = $format_endDate;
                $datas['totalNumberofHouses'] = trim($this->input->post('totalNumberofHouses'));
                $datas['floorArea'] = trim($this->input->post('floorArea'));
                $datas['costofhouse'] = trim($this->input->post('costofhouse'));
                $datas['additionalCost'] = trim($this->input->post('additionalCost'));
                $datas['EstimatedDays'] = trim($this->input->post('EstimatedDays'));
                $serial = $this->db->query("select IF ( isnull(MAX(serialNo)), 1, (MAX(serialNo) + 1) ) AS serialNo FROM `srp_erp_ngo_projects` WHERE companyID={$companyID}")->row_array();
                $datas['serialNo'] = $serial['serialNo'];
                $datas['masterID'] = trim($this->input->post('projectID'));
                $datas['documentCode'] = 'PROJ';
                $datas['documentSystemCode'] = ($company_code . '/' . 'PROJ' . str_pad($datas['serialNo'], 6, '0', STR_PAD_LEFT));
                $datas['createdUserGroup'] = $this->common_data['user_group'];
                $datas['createdPCID'] = $this->common_data['current_pc'];
                $datas['createdUserID'] = $this->common_data['current_userID'];
                $datas['createdDateTime'] = $this->common_data['current_date'];
                $datas['createdUserName'] = $this->common_data['current_user'];
                $this->db->insert('srp_erp_ngo_projects', $datas);
                $last_id_project = $this->db->insert_id();
                $data['projectSubID'] = $last_id_project;
                $this->db->where('proposalID', $last_id);
                $this->db->update('srp_erp_ngo_projectproposals', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Project Save Failed ' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Project Saved Successfully.', $last_id);

                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Project Proposal Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Project Proposal Saved Successfully.', $last_id);

            }


        }


    }

    function load_project_proposal_header()
    {
        $convertFormat = convert_date_format_sql();
        $proposalID = trim($this->input->post('proposalID'));
        $data = $this->db->query("select *,DATE_FORMAT(DocumentDate,'{$convertFormat}') AS DocumentDate,DATE_FORMAT(startDate,'{$convertFormat}') AS startDate,DATE_FORMAT(endDate,'{$convertFormat}') AS endDate FROM srp_erp_ngo_projectproposals WHERE proposalID = {$proposalID} ")->row_array();
        return $data;
    }

    function assign_beneficiary_for_project_proposal()
    {
        $proposalID = $this->input->post('proposalid');
        $ischeacked = $this->input->post('is_cheacked[]');
        $totalestimatedvalue = $this->input->post('totalestimatedvalue[]');
        $selectedItem = $this->input->post('is_cheacked_benid[]');
        $totalsqft = $this->input->post('totalsqft[]');
        $totalcost = $this->input->post('totalcost[]');
        $data = [];

        foreach ($selectedItem as $key => $vals) {
                if ($ischeacked[$key] == 1) {
                    $thisamount = (!empty($totalestimatedvalue[$key])) ? $totalestimatedvalue[$key] : 0;
                    $totalsqftben = (!empty($totalsqft[$key])) ? $totalsqft[$key] : 0;
                    $totalcostben = (!empty($totalcost[$key])) ? $totalcost[$key] : 0;
                    $data[$key]['beneficiaryID'] = $vals;
                    $data[$key]['proposalID'] = $proposalID;
                    $data[$key]['companyID'] = current_companyID();
                    $data[$key]['createdUserGroup'] = current_user_group();
                    $data[$key]['createdPCID'] = current_pc();
                    $data[$key]['createdUserID'] = current_userID();
                    $data[$key]['createdDateTime'] = current_date(true);
                    $data[$key]['timestamp'] = current_date(true);
                    $data[$key]['totalEstimatedValue'] = $thisamount;
                    $data[$key]['totalSqFt'] = $totalsqftben;
                    $data[$key]['totalCost'] = $totalcostben;
                }
            }
        $result = $this->db->insert_batch('srp_erp_ngo_projectproposalbeneficiaries', $data);
        if ($result) {
            return array('s', 'Beneficiary Added successfully !');
        } else {
            return array('s', 'Beneficiary Insertion Failed');
        }
    }

    function assign_donors_for_project_proposal()
    {
        $selectedItem = $this->input->post('selectedDonorsSync[]');
        $proposalID = $this->input->post('proposalID');
        $data = [];

        foreach ($selectedItem as $key => $vals) {
            $data[$key]['donorID'] = $vals;
            $data[$key]['proposalID'] = $proposalID;
            $data[$key]['companyID'] = current_companyID();
            $data[$key]['createdUserGroup'] = current_user_group();
            $data[$key]['createdPCID'] = current_pc();
            $data[$key]['createdUserID'] = current_userID();
            $data[$key]['createdDateTime'] = current_date(true);
            $data[$key]['timestamp'] = current_date(true);
        }
        $result = $this->db->insert_batch('srp_erp_ngo_projectproposaldonors', $data);
        if ($result) {
            return array('s', 'Donors Added successfully !');
        } else {
            return array('s', 'Donors Insertion Failed');
        }
    }

    function delete_project_proposal()
    {
        $proposalID = trim($this->input->post('proposalID'));
        $this->db->delete('srp_erp_ngo_projectproposals', array('proposalID' => $proposalID));
        //$this->db->delete('srp_erp_ngo_projectproposaldetails', array('proposalID' => $proposalID));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error while deleting!');
        } else {
            $this->db->trans_commit();
            return array('s', 'Project Proposel deleted successfully');
        }

    }

    function beneficiary_image_upload_helpNest_two()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->trans_start();
        $output_dir = "uploads/NGO/beneficiaryProjectImage/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/NGO", 007);
            mkdir("uploads/NGO/beneficiaryProjectImage", 007);
        }
        $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'HelpAndNest1_' . $companyID . '_' . trim($this->input->post('benificiaryID')) . '_' . time() . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['helpAndNestImage1'] = $fileName;

        $this->db->where('benificiaryID', trim($this->input->post('benificiaryID')));
        $this->db->update('srp_erp_ngo_beneficiarymaster', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', "Image Upload Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Image uploaded Successfully.');
        }
    }

    function load_project_proposal_beneficiary_details()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $proposalID = trim($this->input->post('proposalID'));
        $this->db->select("ppd.proposalID,pp.isConvertedToProject as isConvertedToProject,ppd.totalSqFt as totalSqFtben,ppd.totalCost as totalCostben,ppd.totalEstimatedValue as proposaltotalEstimatedValue,ppd.proposalBeneficiaryID,ppd.isQualified as isQualified,bm.systemCode as benCode,bm.nameWithInitials as name,bm.ownLandAvailable AS ownLandAvailable,IFNULL(bm.ownLandAvailableComments,' - ') AS ownLandAvailableComments,IFNULL(bm.totalSqFt,' - ') AS totalSqFt,bm.totalCost AS totalCost,ppd.isQualified AS isQualified,pp.approvedYN as approvedYN,pp.confirmedYN as confirmedYN,bm.totalEstimatedValue as totalEstimatedValue,	currency.CurrencyCode");
        $this->db->from('srp_erp_ngo_projectproposalbeneficiaries ppd');
        $this->db->join('srp_erp_ngo_beneficiarymaster bm', 'bm.benificiaryID = ppd.beneficiaryID', 'left');
        $this->db->join('srp_erp_ngo_projectproposals pp', 'pp.proposalID = ppd.proposalID', 'left');
        $this->db->join('srp_erp_currencymaster currency', 'currency.currencyID = pp.transactionCurrencyID', 'left');
        $this->db->where('ppd.companyID', $companyID);
        $this->db->where('ppd.proposalID', $proposalID);
        $this->db->order_by('proposalBeneficiaryID', 'desc');
        return $this->db->get()->result_array();
    }

    function load_project_proposal_donor_details()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertdateformat = convert_date_format_sql();

        $proposalID = trim($this->input->post('proposalID'));
        $this->db->select("pro.confirmedYN,pro.approvedYN,pro.isConvertedToProject as isConvertedToProject,ppd.proposalDonourID,ppd.isSubmitted,do.contactID,do.name as name,ppd.proposalID,ppd.donorID,DATE_FORMAT(ppd.submittedDate,'{$convertdateformat}') AS submittedDate,ppd.isApproved,DATE_FORMAT(ppd.approvedDate,'{$convertdateformat}') AS approvedDate,ppd.commitedAmount,pro.transactionCurrencyID,curm.CurrencyCode as CurrencyCode");
        $this->db->from('srp_erp_ngo_projectproposaldonors ppd');
        $this->db->join('srp_erp_ngo_donors do', 'ppd.donorID = do.contactID', 'left');
        $this->db->join('srp_erp_ngo_projectproposals pro', 'ppd.proposalID = pro.proposalID', 'left');
        $this->db->join('srp_erp_currencymaster curm', 'pro.transactionCurrencyID = curm.currencyID', 'left');
        $this->db->where('ppd.companyID', $companyID);
        $this->db->where('ppd.proposalID', $proposalID);
        $this->db->order_by('proposalDonourID', 'desc');
        return $this->db->get()->result_array();
    }

    function delete_project_proposal_detail()
    {
        $proposalBeneficiaryID = trim($this->input->post('proposalBeneficiaryID'));
        $this->db->delete('srp_erp_ngo_projectproposalbeneficiaries', array('proposalBeneficiaryID' => $proposalBeneficiaryID));
        return true;
    }


    function delete_project_proposal_donors_detail()
    {

        $proposalDonourID = trim($this->input->post('proposalDonourID'));
        $proposalID = trim($this->input->post('proposalID'));
        $donorID = trim($this->input->post('donorID'));
        $this->db->select('*');
        $this->db->from('srp_erp_ngo_projectproposaldonorbeneficiaries');
        $this->db->where('proposalID', $proposalID);
        $this->db->where('donorID', $donorID);
        $results = $this->db->get()->row_array();
        if ($results) {
            return array('status' => 1, 'message' => 'Please delete all the assign beneficiaries before deleting this donor!');
        } else {
            $this->db->delete('srp_erp_ngo_projectproposaldonors', array('proposalDonourID' => $proposalDonourID));
            return array('status' => 0, 'message' => 'Deleted Successfully!');
        }

    }

    function project_proposal_confirmation()
    {
        $this->db->trans_start();
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $proposalID = trim($this->input->post('proposalID'));
        $this->load->library('approvals');

        $this->db->select('*');
        $this->db->where('proposalID', $proposalID);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_ngo_projectproposals');
        $row = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('proposalID', $proposalID);
        $this->db->from('srp_erp_ngo_projectproposals');
        $master = $this->db->get()->row_array();

        if (!empty($row)) {
            return array('w', 'Document already confirmed');
        } else {
            $approvals_status = $this->approvals->CreateApproval('PRP', $master['proposalID'], $master['documentSystemCode'], ' Project Proposal', 'srp_erp_ngo_projectproposals', 'proposalID');
            if ($approvals_status) {
                $data = array(
                    'confirmedYN' => 1,
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                    'confirmedByName' => $this->common_data['current_user'],
                    'proposalStageID' => 2,
                    'status' => 12
                );
                $this->db->where('proposalID', trim($this->input->post('proposalID')));
                $this->db->update('srp_erp_ngo_projectproposals', $data);

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Project Proposal Confirmed Failed ' . $this->db->_error_message());

                } else {

                    $this->db->trans_commit();
                    $data['master'] = $this->db->query("SELECT pro.projectImage,pp.proposalName as ppProposalName,pro.projectName as proProjectName,DATE_FORMAT(pp.DocumentDate,'{$convertFormat}') AS DocumentDate,DATE_FORMAT(pp.startDate,'{$convertFormat}') AS ppStartDate,DATE_FORMAT(pp.endDate,'{$convertFormat}') AS ppEndDate,DATE_FORMAT(pp.DocumentDate, '%M %Y') as subprojectName,pp.detailDescription as ppDetailDescription,pp.projectSummary as ppProjectSummary,pp.totalNumberofHouses as ppTotalNumberofHouses,pp.floorArea as ppFloorArea,pp.costofhouse as ppCostofhouse,pp.additionalCost as ppAdditionalCost,pp.EstimatedDays as ppEstimatedDays,pp.proposalTitle as ppProposalTitle,pp.processDescription as ppProcessDescription,con.name as contractorName,ca.GLDescription as caBankAccName,ca.bankName as caBankName,ca.bankAccountNumber as caBankAccountNumber FROM srp_erp_ngo_projectproposals pp JOIN srp_erp_ngo_projects pro ON pp.projectID = pro.ngoProjectID LEFT JOIN srp_erp_ngo_contractors con ON pp.contractorID = con.contractorID LEFT JOIN srp_erp_chartofaccounts ca ON ca.GLAutoID = pp.bankGLAutoID WHERE pp.proposalID = $proposalID  ")->row_array();

                    $data['detail'] = $this->db->query("SELECT ppb.beneficiaryID as ppbBeneficiaryID,DATE_FORMAT(bm.registeredDate,'{$convertFormat}') AS bmRegisteredDate,DATE_FORMAT(bm.dateOfBirth,'{$convertFormat}') AS bmDateOfBirth,bm.nameWithInitials as bmNameWithInitials,bm.systemCode as bmSystemCode, CASE bm.ownLandAvailable WHEN 1 THEN 'Yes' WHEN 2 THEN 'No' END as bmOwnLandAvailable,bm.NIC as bmNIC,bm.familyMembersDetail as bmFamilyMembersDetail,bm.reasoninBrief as bmReasoninBrief,bm.totalSqFt as bmTotalSqFt,bm.totalCost as bmTotalCost,bm.helpAndNestImage as bmHelpAndNestImage,bm.helpAndNestImage1 as bmHelpAndNestImage1,bm.ownLandAvailableComments as bmOwnLandAvailableComments FROM srp_erp_ngo_projectproposalbeneficiaries ppb LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON ppb.beneficiaryID = bm.benificiaryID WHERE proposalID = $proposalID ")->result_array();

                    $data['images'] = $this->db->query("SELECT imageType,imageName FROM srp_erp_ngo_projectproposalimages WHERE ngoProposalID = $proposalID ")->result_array();

                    $data['moto'] = $this->db->query("SELECT companyPrintTagline FROM srp_erp_company WHERE company_id = {$companyID}")->row_array();

                    $data['proposalID'] = $proposalID;

                    $data['output'] = 'save';

                    $this->load->view('system/operationNgo/ngo_beneficiary_helpnest_print_all', $data, true);

                    return array('s', 'Project Proposal Confirmed Successfully');
                }
            }


        }

    }

    function referback_project_proposal()
    {
        $proposalID = trim($this->input->post('proposalID'));
        $this->load->library('approvals');
        $status = $this->approvals->approve_delete($proposalID, 'PRP');

        if ($status == 1) {

            $data = array('proposalStageID' => 1, 'status' => 12);
            $this->db->where('proposalID', trim($this->input->post('proposalID')));
            $this->db->update('srp_erp_ngo_projectproposals', $data);
            $this->db->trans_complete();

            return array('s', ' Referred Back Successfully.', $status);
        } else {
            return array('e', ' Error in refer back.', $status);
        }

    }

    function project_image_upload()
    {
        $this->db->trans_start();
        $output_dir = "uploads/NGO/projectImage/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/NGO", 007);
            mkdir("uploads/NGO/projectImage", 007);
        }
        $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'Project_' . trim($this->input->post('ngoProjectID')) . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['projectImage'] = $fileName;

        $this->db->where('ngoProjectID', trim($this->input->post('ngoProjectID')));
        $this->db->update('srp_erp_ngo_projects', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', "Image Upload Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Image uploaded  Successfully.');
        }
    }

    function save_ngo_contractor()
    {
        $contractorName = trim($this->input->post('contractorName'));
        $companyID = current_companyID();
        $isExist = $this->db->query("SELECT name FROM srp_erp_ngo_contractors WHERE companyID={$companyID} AND name ='$contractorName' ")->row('name');

        if (isset($isExist)) {
            return array('e', 'Contractor is already Exists');
        } else {

            $data = array(
                'name' => $contractorName,
                'companyID' => $this->common_data['company_data']['company_id'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdDateTime' => $this->common_data['current_date'],
            );

            $this->db->insert('srp_erp_ngo_contractors', $data);
            if ($this->db->affected_rows() > 0) {
                $titleID = $this->db->insert_id();
                return array('s', 'Contractor is created successfully.', $titleID);
            } else {
                return array('e', 'Error in Contractor Creating');
            }
        }

    }

    function load_project_images()
    {
        $companyID = current_companyID();
        $ngoProjectID = trim($this->input->post('proposalID'));
        return $this->db->query("SELECT *,case master.imageType when 1 then 'Cover Imange' when 2 then 'Front Page Image' when 3 then 'House Plan' END as imageType FROM srp_erp_ngo_projectproposalimages master WHERE master.companyID={$companyID} AND master.ngoProposalID = {$ngoProjectID}")->result_array();
    }

    function save_project_proposal_image()
    {
        $companyID = current_companyID();
        $ngoProposalID = $this->input->post('ngoProposalID');
        $documentID = $this->input->post('document');

        //Check is there is a document with this document ID for this employee
        /*        $where = array('DocDesID' => $documentID, 'PersonID' => $empID, 'PersonType' => 'E');
                $isExisting = $this->db->where($where)->select('DocDesID')->from('srp_documentdescriptionforms')->get()->row('DocDesID');*/

        if ($documentID == 1) {
            $isExisting = $this->db->query("SELECT ngoProposalImageID FROM srp_erp_ngo_projectproposalimages WHERE ngoProposalID = {$ngoProposalID} AND companyID = $companyID AND imageType = 1")->row_array();

            if (!empty($isExisting)) {
                return ['e', 'Cover Image has been updated already.<br/>Please delete the Cover Image and try again.'];
            }
        }

        $path = "uploads/ngo/projectProposalImage/";
        //$path = NGOImage . 'projectProposalImage/';
        if (!file_exists($path)) {
            mkdir("uploads/ngo", 777);
            mkdir("uploads/ngo/projectProposalImage", 777);
        }
        $fileName = "ProjectProposal_" . $documentID . '_' . time();
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
        $config['max_size'] = '200000';
        $config['file_name'] = $fileName;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload("doc_file")) {
            return array('e', 'Upload failed ' . $this->upload->display_errors());
        } else {

            $data = array(
                'ngoProposalID' => $ngoProposalID,
                'imageType' => $documentID,
                'imageName' => $this->upload->data('file_name'),
                'companyID' => $this->common_data['company_data']['company_id'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdDateTime' => $this->common_data['current_date'],
            );
            $this->db->insert('srp_erp_ngo_projectproposalimages', $data);

            if ($this->db->affected_rows() > 0) {
                return array('s', 'Image Successfully uploaded');
            } else {
                return array('e', 'Error in Image upload');
            }

        }
    }

    function get_total_commitments_drilldown()
    {
        $convertFormat = convert_date_format_sql();
        $donorID = trim($this->input->post('donorID'));
        $currencyID = trim($this->input->post('currencyID'));
        $from = trim($this->input->post('from'));
        $companyID = current_companyID();
        $date_format_policy = date_format_policy();
        $format_date = null;
        if (isset($from) && !empty($from)) {
            $format_date = input_format_date($from, $date_format_policy);
        }
        $where = "";
        if (!empty($from)) {
            $where = "AND cm.documentDate <= '" . $format_date . "' ";
        }
        $data = $this->db->query("SELECT cm.commitmentAutoId AS autoID,cmd.commitmentTotal AS transactionAmount,cm.transactionCurrencyID,cm.transactionCurrency,cm.donorsID,cm.transactionCurrencyDecimalPlaces,DATE_FORMAT(cm.documentDate,'{$convertFormat}') AS documentDate,cm.documentSystemCode,don.name AS donorName FROM srp_erp_ngo_commitmentmasters cm JOIN srp_erp_ngo_donors don ON cm.donorsID = don.contactID LEFT JOIN (SELECT SUM(transactionAmount) AS commitmentTotal, srp_erp_ngo_commitmentdetails.commitmentAutoId FROM srp_erp_ngo_commitmentdetails GROUP BY commitmentAutoId) cmd ON cmd.commitmentAutoId = cm.commitmentAutoId WHERE cm.companyID = {$companyID} AND cm.confirmedYN = 1 AND cm.transactionCurrencyID = {$currencyID} AND cm.donorsID = {$donorID} $where")->result_array();
        return $data;
    }

    function get_total_collection_drilldown()
    {
        $convertFormat = convert_date_format_sql();
        $donorID = trim($this->input->post('donorID'));
        $currencyID = trim($this->input->post('currencyID'));
        $from = trim($this->input->post('from'));
        $companyID = current_companyID();
        $date_format_policy = date_format_policy();
        $format_date = null;
        if (isset($from) && !empty($from)) {
            $format_date = input_format_date($from, $date_format_policy);
        }
        $where = "";
        if (!empty($from)) {
            $where = "AND dcm.documentDate <= '" . $format_date . "' ";
        }
        $data = $this->db->query("SELECT dcm.collectionAutoId AS autoID,cmd.collectionTotal AS transactionAmount,dcm.transactionCurrencyID,dcm.transactionCurrency,dcm.donorsID,dcm.transactionCurrencyDecimalPlaces,DATE_FORMAT(dcm.documentDate,'{$convertFormat}') AS documentDate,documentSystemCode,don.name AS donorName FROM srp_erp_ngo_donorcollectionmaster dcm JOIN srp_erp_ngo_donors don ON dcm.donorsID = don.contactID LEFT JOIN (SELECT SUM(transactionAmount) AS collectionTotal,srp_erp_ngo_donorcollectiondetails.collectionAutoId FROM srp_erp_ngo_donorcollectiondetails GROUP BY collectionAutoId) cmd ON cmd.collectionAutoId = dcm.collectionAutoId WHERE dcm.companyID = {$companyID} AND dcm.transactionCurrencyID = {$currencyID} AND dcm.donorsID = {$donorID} AND dcm.approvedYN = 1 $where ")->result_array();
        return $data;
    }

    function delete_project_proposal_image()
    {
        $ngoProposalImageID = trim($this->input->post('ngoProposalImageID'));
        $myFileName = $this->input->post('myFileName');
        $this->db->delete('srp_erp_ngo_projectproposalimages', array('ngoProposalImageID' => trim($ngoProposalImageID)));
        return array('s', 'Document deleted successfully');

    }

    function send_project_proposal_email()
    {
        $proposalID = $this->input->post('proposalID');
        $Donors = $this->input->post('selectedDonorsEmailSync');
        $path = NGOImage . "ProjectProposal_" . $proposalID . ".pdf";
        if (!empty($Donors)) {
            foreach ($Donors as $data) {

                $donorData = $this->db->query("SELECT name,email FROM srp_erp_ngo_donors WHERE contactID = {$data}")->row_array();

                if (!empty($donorData['email'])) {
                    $param = array();
                    $param["empName"] = $donorData["name"];
                    $param["body"] = 'We are pleased to submit our proposal as follow. <br/>
                                          <table border="0px">
                                          </table>';
                    $mailData = [
                        'approvalEmpID' => '',
                        'documentCode' => '',
                        'toEmail' => $donorData["email"],
                        'subject' => 'Project Proposal',
                        'param' => $param
                    ];
                    send_approvalEmail($mailData, 1, $path);

                    $this->db->set('sendEmail', 1);
                    $this->db->where('proposalID', $proposalID);
                    $this->db->where('donorID', $data);
                    $this->db->update('srp_erp_ngo_projectproposaldonors');
                }

            }
        }
        return array('s', 'Email Send Successfully.');

    }

    function beneficiary_province()
    {
        $masterid = trim($this->input->post('masterid'));
        $this->db->select('stateID,Description');
        $this->db->where('countyID', $masterid);
        $this->db->where(' type', 1);
        return $this->db->get('srp_erp_statemaster')->result_array();
    }

    function beneficiary_area()
    {
        $masterid = trim($this->input->post('masterid'));
        $this->db->select('stateID,Description');
        $this->db->where('masterID', $masterid);
        $this->db->where(' type', 2);
        return $this->db->get('srp_erp_statemaster')->result_array();
    }

    function beneficiary_division()
    {
        $masterid = trim($this->input->post('masterid'));
        $this->db->select('stateID,Description');
        $this->db->where('masterID', $masterid);
        $this->db->where(' type', 3);
        return $this->db->get('srp_erp_statemaster')->result_array();
    }

    function beneficiary_sub_division()
    {
        $masterid = trim($this->input->post('masterid'));
        $this->db->select('stateID,Description');
        $this->db->where('masterID', $masterid);
        $this->db->where(' type', 4);
        return $this->db->get('srp_erp_statemaster')->result_array();
    }


    function load_beneficiary_multiple_images()
    {
        $benificiaryID = trim($this->input->post('benificiaryID'));
        $this->db->select('beneficiaryImageID,isSelectedforPP,beneficiaryImage');
        $this->db->where('beneficiaryID', $benificiaryID);
        return $this->db->get('srp_erp_ngo_beneficiaryimages')->result_array();
    }


    function upload_beneficiary_multiple_image()
    {
        $companyID = current_companyID();
        $benificiaryID = $this->input->post('benificiaryID');

        $path = UPLOAD_PATH_POS . 'uploads/ngo/beneficiaryImage/'; // imagePath();
        if (!file_exists($path)) {
            mkdir("documents/ngo", 007);
            mkdir("documents/ngo/beneficiaryImage", 007);
        }
        $fileName = str_replace(' ', '', strtolower($benificiaryID)) . '_' . time();
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size'] = '5120'; // 5 MB
        $config['file_name'] = $fileName;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload("doc_file")) {
            return array('e', 'Upload failed ' . $this->upload->display_errors());
        } else {

            $data = array(
                'beneficiaryID' => $benificiaryID,
                'beneficiaryImage' => $this->upload->data('file_name'),
                'companyID' => current_companyID(),
                'createdPCID' => current_pc(),
                'CreatedUserName' => current_employee(),
                'createdUserID' => current_userID(),
                'createdDateTime' => current_date()
            );

            $this->db->insert('srp_erp_ngo_beneficiaryimages', $data);

            if ($this->db->affected_rows() > 0) {
                return array('s', 'Image successfully uploaded');
            } else {
                return array('e', 'Error in Image upload');
            }

        }
    }

    function delete_beneficiary_multiple_image()
    {
        $beneficiaryImageID = trim($this->input->post('beneficiaryImageID'));
        $this->db->where('beneficiaryImageID', $beneficiaryImageID)->delete('srp_erp_ngo_beneficiaryimages');

        if ($this->db->affected_rows() > 0) {
            return array('s', 'Image deleted successfully');
        } else {
            return array('e', 'Error in Image delete !');
        }

    }

    function update_beneficiary_multiple_image()
    {

        $this->db->trans_start();
        $beneficiaryImageID = trim($this->input->post('beneficiaryImageID'));
        $benificiaryID = trim($this->input->post('benificiaryID'));
        $status = trim($this->input->post('status'));

        if ($status == 1) {
            $totlaCount = $this->db->query("select beneficiaryImageID FROM srp_erp_ngo_beneficiaryimages WHERE beneficiaryID ={$benificiaryID} AND isSelectedforPP = 1")->result_array();

            if (count($totlaCount) >= 2) {
                return array('e', 'Only Two Images Can be Default Image.');
                exit();
            }
        }

        $data['isSelectedforPP'] = $status;
        $this->db->where('beneficiaryImageID', $beneficiaryImageID);
        $this->db->update('srp_erp_ngo_beneficiaryimages', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Default Status Update Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Default Status Updated Successfully.');
        }
    }

    function fetch_beneficiary_multiple_images($benificiaryID)
    {
        $this->db->select("beneficiaryImageID,beneficiaryImage,isSelectedforPP");
        $this->db->from("srp_erp_ngo_beneficiaryimages");
        $this->db->where("beneficiaryID", $benificiaryID);
        $this->db->where("isSelectedforPP", 1);
        $output = $this->db->get()->result_array();
        echo $this->db->last_query();
        return $output;
    }

    function save_project_proposal_attachments()
    {
        $ngoProposalID = $this->input->post('ngoProposalID');
        $document = $this->input->post('document');

        $path = "uploads/ngo/attachments/";
        //$path = NGOImage . 'projectProposalImage/';
        if (!file_exists($path)) {
            mkdir("uploads/ngo", 777);
            mkdir("uploads/ngo/attachments", 777);
        }
        $fileName = "Project_Proposal_Attachment_" . $ngoProposalID . '_' . time();
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
        $config['max_size'] = '200000';
        $config['file_name'] = $fileName;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload("doc_file")) {
            return array('e', 'Upload failed ' . $this->upload->display_errors());
        } else {
            $upload_data = $this->upload->data();

            $data = array(
                'documentAutoID' => $ngoProposalID,
                'documentID' => 6,
                'attachmentDescription' => $document,
                'myFileName' => $fileName . $upload_data["file_ext"],
                'timestamp' => date('Y-m-d H:i:s'),
                'fileType' => trim($upload_data["file_ext"]),
                'fileSize' => trim($upload_data["file_size"]),
                'companyID' => $this->common_data['company_data']['company_id'],
                'createdUserGroup' => $this->common_data['user_group'],
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $this->common_data['current_userID'],
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => $this->common_data['current_date'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdUserName' => $this->common_data['current_user'],
                'createdDateTime' => $this->common_data['current_date'],
            );
            $this->db->insert('srp_erp_ngo_attachments', $data);

            if ($this->db->affected_rows() > 0) {
                return array('s', 'Attachemt successfully uploaded!.');
            } else {
                return array('e', 'Error in attachment upload');
            }

        }
    }

    function delete_project_proposal_attachment()
    {
        $ngoattachmentid = trim($this->input->post('attachmentID'));
        $this->db->delete('srp_erp_ngo_attachments', array('attachmentID' => trim($ngoattachmentid)));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error while deleting!');
        } else {
            $this->db->trans_commit();
            return array('s', 'Attachment deleted successfully');
        }


    }

    function update_donors_is_submited_status()
    {
        $this->db->trans_start();
        $donorIDcheacked = $this->input->post('issubmitcheack');
        $date_format_policy = date_format_policy();
        $proposal = trim($this->input->post('proposaldonor'));
        $alldonors = trim($this->input->post('alldonors'));
        $donorIDcheackedapproved = trim($this->input->post('isapprovedchk'));
        $amount = $this->input->post('addcommitments');

        $datesubmited = $this->input->post('submiteddate');
        $dateapproved = $this->input->post('approveddate');

        $dateapproved = [];

        $upData = [];
        $cheackedapprovedid = [];
        $ids = explode(',', $alldonors);
        $ckeckeddonorid = explode(',', $donorIDcheacked);
        $ckeckedapproveddonor = explode(',', $donorIDcheackedapproved);


        foreach ($ckeckeddonorid as $val) {
            array_push($upData, $val);
        }
        foreach ($ckeckedapproveddonor as $val) {
            array_push($cheackedapprovedid, $val);
        }
        foreach ($ids as $key => $val) {
            if (in_array($val, $upData)) {
                $data['isSubmitted'] = 1;
            } else {
                $data['isSubmitted'] = 0;
            }

            if (in_array($val, $cheackedapprovedid)) {
                $data['isApproved'] = 1;
            } else {
                $data['isApproved'] = 0;

            }

            $data['submittedDate'] = $this->common_data['current_date'];
            $data['commitedAmount'] = $amount[$key];
            $this->db->where('donorID', $val);
            $this->db->where('proposalID', $proposal);
            $this->db->update('srp_erp_ngo_projectproposaldonors', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Donor Details Update Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Donor Details updated successfully!.');
        }
    }

    function update_donors_is_approved_status()
    {
        $donorIDcheackedapproved = trim($this->input->post('isapprovedchk'));
        $donorIDuncheackedapproved = trim($this->input->post('isapprovedunchk'));
        $proposal = trim($this->input->post('proposalID'));
        if ($donorIDcheackedapproved != '') {
            $unckeckeddonoridapproved = explode(',', $donorIDcheackedapproved);
            $data['isApproved'] = 1;
            $data['approvedDate'] = $this->common_data['current_date'];
            $this->db->where_in('donorID', $unckeckeddonoridapproved);
            $this->db->where('proposalID', $proposal);
            $this->db->update('srp_erp_ngo_projectproposaldonors', $data);
        }
        if ($donorIDuncheackedapproved != '') {
            $unckeckeddonoridapproved = explode(',', $donorIDuncheackedapproved);
            $data['isApproved'] = 0;
            $data['approvedDate'] = '';
            $this->db->where_in('donorID', $unckeckeddonoridapproved);
            $this->db->where('proposalID', $proposal);
            $this->db->update('srp_erp_ngo_projectproposaldonors', $data);

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            // return array('e', "Is Approved Status Update Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            // return array('s', 'Is approved status updated successfully!.');
        }


        /* $this->db->trans_start();
         $donorID = trim($this->input->post('donorID'));
         $proposal = trim($this->input->post('proposalID'));
         $statusapproved = trim($this->input->post('statusapproved'));

         $data['isApproved'] = $statusapproved;
         $data['approvedDate'] = $this->common_data['current_date'];
         $this->db->where('donorID', $donorID);
         $this->db->where('proposalID', $proposal);
         $this->db->update('srp_erp_ngo_projectproposaldonors', $data);

         $this->db->trans_complete();
         if ($this->db->trans_status() === FALSE) {
             $this->db->trans_rollback();
             return array('e', "Is Approved Status Update Failed." . $this->db->_error_message());
         } else {
             $this->db->trans_commit();
             return array('s', 'Is approved status updated successfully!.');
         }*/
    }

    function update_donors_commited_amt()
    {
//die();
        $this->db->trans_start();
        $donorID = trim($this->input->post('donorID'));
        $proposal = trim($this->input->post('proposalID'));
        $amount = trim($this->input->post('amount'));

        if ($amount != '') {
            $amount_arr = explode(',', $amount);
            $donor_arr = explode(',', $donorID);

            $upData = [];
            foreach ($amount_arr as $key => $val) {

                $data['commitedAmount'] = $val;
                $this->db->where_in('donorID', $donor_arr[$key]);
                $this->db->where('proposalID', $proposal);
                $this->db->update('srp_erp_ngo_projectproposaldonors', $data);
            }

            /* $data['commitedAmount'] = $amount;
             $this->db->where_in('donorID', $donorID);
             $this->db->where('proposalID', $proposal);
             $this->db->update('srp_erp_ngo_projectproposaldonors', $data);*/
        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Amount Update Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Amount has been updated.');
        }
    }

    function add_donor_beneficiary()
    {
        $selectedItem = $this->input->post('selectedItemsSync[]');
        $project = $this->input->post('project');
        $donorid = $this->input->post('donorid');
        $compID = current_companyID();
        $data = [];

        foreach ($selectedItem as $key => $vals) {
            $data[$key]['beneficiaryID'] = $vals;
            $data[$key]['proposalID'] = $project;
            $data[$key]['donorID'] = $donorid;
            $data[$key]['companyID'] = $compID;
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdDateTime'] = $this->common_data['current_date'];
            $data[$key]['createdUserName'] = $this->common_data['current_user'];
        }
        $result = $this->db->insert_batch('srp_erp_ngo_projectproposaldonorbeneficiaries', $data);
        if ($result) {
            $this->session->set_flashdata('s', 'Beneficiary added successfully !');
            return array('status' => true);
        } else {
            $this->session->set_flashdata('e', 'Beneficiary Insertion Failed!');
            return array('status' => false);
        }
    }

    function delete_assign_beneficiaries()
    {
        $beneficiarydonorid = $this->input->post('beneficiaryDonorID');
        $this->db->delete('srp_erp_ngo_projectproposaldonorbeneficiaries', array('beneficiaryDonorID' => $beneficiarydonorid));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error while deleting!');
        } else {
            $this->db->trans_commit();
            return array('s', 'Beneficiary deleted successfully');
        }
    }

    function update_date()
    {
        $this->db->trans_start();
        $donorID = $this->input->post('donor');
        $proposal = trim($this->input->post('proposaldonor'));
        $date = $this->input->post('submiteddate');
        $date_format_policy = date_format_policy();
        foreach ($date as $key => $val) {
            $submited_date = $val;
            $submited_date_donor = input_format_date($submited_date, $date_format_policy);
            $data['submittedDate'] = $submited_date_donor;
            $this->db->where('donorID', $donorID[$key]);
            $this->db->where('proposalID', $proposal);
            $this->db->update('srp_erp_ngo_projectproposaldonors', $data);
            $this->db->trans_complete();
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Is subbmited date update failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Is subbmited date updated successfully!.');
        }
    }

    function update_date_approved()
    {
        $this->db->trans_start();
        $donorID = $this->input->post('donor');
        $proposal = trim($this->input->post('proposaldonor'));
        $date_format_policy = date_format_policy();
        $date = $this->input->post('approveddate');


        foreach ($date as $key => $val) {
            $approved_date = $val;
            $approved_date_donor = input_format_date($approved_date, $date_format_policy);
            $data['approvedDate'] = $approved_date_donor;
            $this->db->where('donorID', $donorID[$key]);
            $this->db->where('proposalID', $proposal);
            $this->db->update('srp_erp_ngo_projectproposaldonors', $data);
            $this->db->trans_complete();
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Is Approved date update failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Is approved date updated successfully!.');
        }
    }

    function save_project_proposal_approval()
    {
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_id = trim($this->input->post('proposalid'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('project_status'));
        $comments = trim($this->input->post('comments'));
        $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'PRP');

        if ($approvals_status == 1) {
            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];
            $data['proposalStageID'] = 3;
            $data['status'] = 12;

            $this->db->where('proposalID', $system_id);
            $this->db->update('srp_erp_ngo_projectproposals', $data);
            $this->session->set_flashdata('s', 'Project Proposal Approved Successfully.');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function save_beneficiary_header_house_damageAssesment()
    {
        $this->db->trans_start();
        $benificiaryID = trim($this->input->post('benificiaryID'));
        $data['da_typeOfhouseDamage'] = trim($this->input->post('da_typeOfhouseDamage'));
        $data['da_houseCategory'] = trim($this->input->post('da_houseCategory'));
        $data['da_housingCondition'] = trim($this->input->post('da_housingCondition'));
        $data['da_buildingDamages'] = trim($this->input->post('da_buildingDamages'));
        $data['da_estimatedRepairingCost'] = trim($this->input->post('da_estimatedRepairingCost'));
        $data['da_needAssistancetoRepairYN'] = trim($this->input->post('da_needAssistancetoRepairYN'));
        $data['da_paidAmount'] = trim($this->input->post('da_totalpaidamt'));

        if ($benificiaryID) {
            $this->db->where('benificiaryID', $benificiaryID);
            $this->db->update('srp_erp_ngo_beneficiarymaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Beneficiary Update Failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Beneficiary Updated Successfully.');
            }
        }
    }

    function save_beneficiary_header_human_damageAssesment()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];
        $humanInjuryID = trim($this->input->post('humanInjuryID'));
        $benificiaryID = trim($this->input->post('benificiaryID'));
        $FamilyDetailsID = trim($this->input->post('familyMembers'));
        $damagetypeid = trim($this->input->post('damageTypeID'));
        $totalpaidamt = trim($this->input->post('totalpaidamt'));

        $data['FamilyDetailsID'] = trim($this->input->post('familyMembers'));
        $data['beneficiaryID'] = $benificiaryID;
        $data['paidAmount'] = $totalpaidamt;
        $data['damageTypeID'] = trim($this->input->post('damageTypeID'));
        $data['estimatedAmount'] = trim($this->input->post('estimatedAmount'));
        $data['estimatedAmount'] = trim($this->input->post('estimatedAmount'));
        $data['remarks'] = trim($this->input->post('remarks'));

        if ($humanInjuryID) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('humanInjuryID', $humanInjuryID);
            $this->db->update('srp_erp_ngo_humaninjuryassesment', $data);
            $q = "SELECT
                    FamilyDetailsID
                FROM
                    srp_erp_ngo_humaninjuryassesment
                WHERE 
                 humanInjuryID!='" . $humanInjuryID . "' AND beneficiaryID = '" . $benificiaryID . "' AND FamilyDetailsID = '" . $FamilyDetailsID . "' AND damageTypeID ='" . $damagetypeid . "'";
            $result = $this->db->query($q)->row_array();
            if ($result) {
                return array('e', 'The selected family memeber already exist.');
            } else {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Human Injury Assessment Update Failed' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Human Injury Assessment Updated Successfully.');
                }
            }

        } else {
            $q = "SELECT
                    FamilyDetailsID
                FROM
                    srp_erp_ngo_humaninjuryassesment
                WHERE 
                 beneficiaryID = '" . $benificiaryID . "' AND FamilyDetailsID = '" . $FamilyDetailsID . "' AND damageTypeID ='" . $damagetypeid . "'  ";
            $result = $this->db->query($q)->row_array();
            if ($result) {
                return array('e', 'The selected family memeber already exist.');
            } else {
                $data['companyID'] = $companyID;
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_ngo_humaninjuryassesment', $data);
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Human Injury Assessment Save Failed ' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Human Injury Assessment Saved Successfully.', $last_id);

                }
            }

        }

    }

    function save_beneficiary_header_itemdamage_assesment()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];
        $itemDamagedID = trim($this->input->post('itemDamagedID'));
        $benificiaryID = trim($this->input->post('benificiaryID'));
        $damagetotpaid = trim($this->input->post('totalpaidamtitem'));

        $data['damageItemCategoryID'] = trim($this->input->post('damageItemCategoryID'));
        $data['beneficiaryID'] = $benificiaryID;
        $data['damageTypeID'] = trim($this->input->post('damageTypeID'));
        $data['damageConditionID'] = trim($this->input->post('damageConditionID'));
        $data['damagedAmountClient'] = trim($this->input->post('damagedAmountClient'));
        $data['assessedValue'] = trim($this->input->post('assessedValue'));
        $data['Brand'] = trim($this->input->post('Brand'));
        $data['itemDescription'] = trim($this->input->post('itemDescription'));
        // $data['existingItemCondition'] = trim($this->input->post('existingItemCondition'));

        $data['isInsuranceYN'] = trim($this->input->post('isInsuranceYN'));
        $data['insuranceTypeID'] = trim($this->input->post('insuranceTypeID'));
        $data['insuranceRemarks'] = trim($this->input->post('insuranceRemarks'));
        $data['paidAmount'] = $damagetotpaid;

        if ($itemDamagedID) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('itemDamagedID', $itemDamagedID);
            $this->db->update('srp_erp_ngo_itemdamagedasssesment', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Damaged House Items Assessment Update Failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Damaged House Items Assessment Updated Successfully.');
            }
        } else {
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_ngo_itemdamagedasssesment', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Damaged House Items Assessment Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Damaged House Items Assessment Saved Successfully.', $last_id);

            }
        }
    }

    function save_beneficiary_header_businessProperties_assesment()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];
        $businessDamagedID = trim($this->input->post('businessDamagedID'));
        $benificiaryID = trim($this->input->post('benificiaryID'));
        $businesspropertyid = trim($this->input->post('totalpaidamtbsp'));

        $data['busineesActivityID'] = trim($this->input->post('busineesActivityID'));
        $data['beneficiaryID'] = $benificiaryID;
        $data['damageTypeID'] = trim($this->input->post('damageTypeID'));
        $data['damageConditionID'] = trim($this->input->post('damageConditionID'));
        $data['incomeSourceType'] = trim($this->input->post('incomeSourceType'));
        $data['propertyValue'] = trim($this->input->post('propertyValue'));
        $data['existingItemCondition'] = trim($this->input->post('existingItemCondition'));
        $data['expectations'] = trim($this->input->post('expectations'));
        $data['buildingTypeID'] = trim($this->input->post('buildingTypeID'));
        $data['paidAmount'] = $businesspropertyid;


        $data['isInsuranceYN'] = trim($this->input->post('isInsuranceYN'));
        $data['insuranceTypeID'] = trim($this->input->post('insuranceTypeID'));
        $data['insuranceRemarks'] = trim($this->input->post('insuranceRemarks'));

        if ($businessDamagedID) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('businessDamagedID', $businessDamagedID);
            $this->db->update('srp_erp_ngo_businessdamagedassesment', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Damaged Business Properties Assessment Update Failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Damaged Business Properties Assessment Updated Successfully.', $benificiaryID);
            }
        } else {
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_ngo_businessdamagedassesment', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Damaged Business Properties Assessment Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Damaged Business Properties Assessment Saved Successfully.', $benificiaryID);

            }
        }
    }

    function delete_human_injury_assessment()
    {
        $humanInjuryID = trim($this->input->post('humanInjuryID'));

        $this->db->where('humanInjuryID', $humanInjuryID);
        $resault = $this->db->delete('srp_erp_ngo_humaninjuryassesment');
        if ($resault) {
            return array('s', 'Record Deleted');
        } else {
            return array('e', 'Error in record delete');
        }
    }

    function delete_house_items_assessment()
    {
        $itemDamagedID = trim($this->input->post('itemDamagedID'));

        $this->db->where('itemDamagedID', $itemDamagedID);
        $resault = $this->db->delete('srp_erp_ngo_itemdamagedasssesment');
        if ($resault) {
            return array('s', 'Record Deleted');
        } else {
            return array('e', 'Error in record delete');
        }
    }

    function delete_business_properties_assessment()
    {
        $businessDamagedID = trim($this->input->post('businessDamagedID'));

        $this->db->where('businessDamagedID', $businessDamagedID);
        $resault = $this->db->delete('srp_erp_ngo_businessdamagedassesment');
        if ($resault) {
            return array('s', 'Record Deleted');
        } else {
            return array('e', 'Error in record delete');
        }
    }

    function check_project_shortCode()
    {
        $ngoProjectID = trim($this->input->post('ngoProjectID'));
        $this->db->select('projectShortCode');
        $this->db->where('ngoProjectID', $ngoProjectID);
        $this->db->from('srp_erp_ngo_projects');
        return $this->db->get()->row_array();
    }

    function edit_beneficiary_detials()
    {
        $empbenficiaryid = $this->input->post('empfamilyid');
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(DOB,\'' . $convertFormat . '\') AS DOB');
        $this->db->where('empfamilydetailsID', $empbenficiaryid);
        return $this->db->get('srp_erp_ngo_beneficiaryfamilydetails')->row_array();

    }

    function load_human_injury_assestment()
    {
        $humanInjuryID = $this->input->post('humanInjuryID');
        $this->db->select('humanInjuryID,FamilyDetailsID,damageTypeID,estimatedAmount,paidAmount,remarks');
        $this->db->where('humanInjuryID', $humanInjuryID);
        return $this->db->get('srp_erp_ngo_humaninjuryassesment')->row_array();
    }

    function load_item_damage_assetment()
    {
        $itemDamagedID = $this->input->post('itemDamagedID');
        $this->db->select('*');
        $this->db->where('itemDamagedID', $itemDamagedID);
        return $this->db->get('srp_erp_ngo_itemdamagedasssesment')->row_array();
    }

    function save_beneficiary_familyDetails_damageAssessment()
    {
        $date_format_policy = date_format_policy();
        $empfamilydetailsID = trim($this->input->post('empfamilydetailsID'));
        $dateOfBirth = trim($this->input->post('DOB'));
        $format_dateOfBirth = null;
        if (isset($dateOfBirth) && !empty($dateOfBirth)) {
            $format_dateOfBirth = input_format_date($dateOfBirth, $date_format_policy);
        }
        $data['beneficiaryID'] = trim($this->input->post('benificiaryID'));
        $data['name'] = trim($this->input->post('name'));
        $data['relationship'] = trim($this->input->post('relationshipType'));
        $data['nationality'] = trim($this->input->post('nationality'));
        $data['DOB'] = $format_dateOfBirth;
        $data['gender'] = trim($this->input->post('gender'));
        $data['idNO'] = trim($this->input->post('idNO'));
        $data['createdUserID'] = trim($this->input->post('createdUserID'));
        $data['createdPCid'] = trim($this->input->post('createdPCid'));
        $data['timestamp'] = trim($this->input->post('timestamp'));

        //Damage Assessment Family Detail
        $data['type'] = trim($this->input->post('familyType'));
        $data['RelatedHHHead'] = trim($this->input->post('RelatedHHHead'));
        $data['schoolID'] = trim($this->input->post('schoolID'));
        $data['schoolGrade'] = trim($this->input->post('schoolGrade'));
        $data['classRank'] = trim($this->input->post('schoolRank'));
        $data['makthabID'] = trim($this->input->post('makthabID'));
        $data['makthabGrade'] = trim($this->input->post('makthabGrade'));
        $data['remarks'] = trim($this->input->post('familyremarks'));
        $data['occupationID'] = trim($this->input->post('occupationID'));
        $data['Disability'] = trim($this->input->post('Disability'));

        if ($empfamilydetailsID) {
            $data['modifiedPc'] = $this->common_data['current_pc'];
            $data['modifiedUser'] = $this->common_data['current_user'];
            $this->db->where('empfamilydetailsID', $empfamilydetailsID);
            $this->db->update('srp_erp_ngo_beneficiaryfamilydetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Family Detail Update Failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Family Detail Updated Successfully.');
            }
        } else {
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCid'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['timestamp'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_ngo_beneficiaryfamilydetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Family Detail Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Family Detail Saved Successfully.');

            }
        }
    }

    function load_item_damage_bsp()
    {
        $bsdamageid = $this->input->post('businessDamagedID');
        $this->db->select('*');
        $this->db->where('businessDamagedID', $bsdamageid);
        return $this->db->get('srp_erp_ngo_businessdamagedassesment')->row_array();
    }

    function update_quaified_status_pp_beneficiaries()
    {

        $this->db->trans_start();
        $proposal = trim($this->input->post('proposalID'));
        $cheackedben = trim($this->input->post('checkedItem'));
        $uncheakedben = trim($this->input->post('uncheckedItem'));

        if ($cheackedben != '') {
            $beneficiarychk = explode(',', $cheackedben);
            $data['isQualified'] = 1;
            $this->db->where_in('proposalBeneficiaryID', $beneficiarychk);
            $this->db->where('proposalID', $proposal);
            $this->db->update('srp_erp_ngo_projectproposalbeneficiaries', $data);
        }
        if ($uncheakedben != '') {
            $unbenficiarychk = explode(',', $uncheakedben);
            $data['isQualified'] = 0;
            $this->db->where_in('proposalBeneficiaryID', $unbenficiarychk);
            $this->db->where('proposalID', $proposal);
            $this->db->update('srp_erp_ngo_projectproposalbeneficiaries', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Is Qualified Status Update Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Is Qualified status updated successfully!.');
        }

    }

    function save_employees_details()
    {
        $emp = trim($this->input->post('employee'));
        $project = trim($this->input->post('project'));
        $this->db->select('employeeID,projectID');
        $this->db->from('srp_erp_ngo_projectowners');
        $this->db->where('employeeID', $emp);
        $this->db->where('projectID', $project);
        $employeeforselectedproject = $this->db->get()->row_array();
        if (!empty($employeeforselectedproject)) {
            return array('w', 'User Already added To this project');
        }
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['employeeID'] = $emp;
        $data['projectID'] = $project;
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $this->db->insert('srp_erp_ngo_projectowners', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Employee Created Failed.');
        } else {
            $this->db->trans_commit();
            return array('s', 'Employee Created Successfully.');
        }
    }

    function update_is_add_status()
    {

        $this->db->trans_start();
        $projectid = trim($this->input->post('projectid'));
        $projectownerid = trim($this->input->post('projectOwnerID'));
        $statusisadd = trim($this->input->post('status'));

        $data['isAdd'] = $statusisadd;
        $this->db->where('projectOwnerID', $projectownerid);
        $this->db->where('projectID', $projectid);
        $this->db->update('srp_erp_ngo_projectowners', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Is Add Status Update Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Is Add status updated successfully!.');
        }
    }

    function update_is_edit_status()
    {

        $this->db->trans_start();
        $projectid = trim($this->input->post('projectid'));
        $projectownerid = trim($this->input->post('projectOwnerID'));
        $statusisedit = trim($this->input->post('statusisedit'));

        $data['isEdit'] = $statusisedit;
        $this->db->where('projectOwnerID', $projectownerid);
        $this->db->where('projectID', $projectid);
        $this->db->update('srp_erp_ngo_projectowners', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Is Edit Status Update Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Is Edit status updated successfully!.');
        }
    }

    function update_is_confirm_status()
    {

        $this->db->trans_start();
        $projectid = trim($this->input->post('projectid'));
        $projectownerid = trim($this->input->post('projectOwnerID'));
        $statusisconfirm = trim($this->input->post('statusisconfirm'));

        $data['isConfirm'] = $statusisconfirm;
        $this->db->where('projectOwnerID', $projectownerid);
        $this->db->where('projectID', $projectid);
        $this->db->update('srp_erp_ngo_projectowners', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Is Confirm Status Update Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Is Confirm status updated successfully!.');
        }
    }

    function update_is_approval_status()
    {

        $this->db->trans_start();
        $projectid = trim($this->input->post('projectid'));
        $projectownerid = trim($this->input->post('projectOwnerID'));
        $stausisapproval = trim($this->input->post('statusisapproval'));

        $data['isApproval'] = $stausisapproval;
        $this->db->where('projectOwnerID', $projectownerid);
        $this->db->where('projectID', $projectid);
        $this->db->update('srp_erp_ngo_projectowners', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Is Approval Status Update Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Is Approval status updated successfully!.');
        }
    }

    function update_is_view_status()
    {

        $this->db->trans_start();
        $projectid = trim($this->input->post('projectid'));
        $projectownerid = trim($this->input->post('projectOwnerID'));
        $stausisview = trim($this->input->post('statusisview'));

        $data['isView'] = $stausisview;
        $this->db->where('projectOwnerID', $projectownerid);
        $this->db->where('projectID', $projectid);
        $this->db->update('srp_erp_ngo_projectowners', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Is View Status Update Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Is View status updated successfully!.');
        }
    }

    function cheack_is_add_status()
    {
        $emp = $this->common_data['current_userID'];
        $project = trim($this->input->post('projectid'));
        $this->db->select('employeeID');
        $this->db->from('srp_erp_ngo_projectowners');
        $this->db->where('employeeID', $emp);
        $this->db->where('isAdd', 1);
        $this->db->where('projectID', $project);
        $employeeforselectedproject = $this->db->get()->row_array();
        if (empty($employeeforselectedproject)) {
            return array('status' => 1, 'message' => 'User didnot Have Access To This Project');
        } else {
            return array('status' => 2, 'message' => 'The Beneficiary ');
        }
    }

    function delete_assign_usersfor_project()
    {

        $projectOwnerID = trim($this->input->post('projectOwnerID'));

        $this->db->delete('srp_erp_ngo_projectowners', array('projectOwnerID' => $projectOwnerID));
        return array('status' => 0, 'message' => 'User Deleted Successfully!');

    }

    function update_is_delete_status()
    {
        $this->db->trans_start();
        $projectid = trim($this->input->post('projectid'));
        $projectownerid = trim($this->input->post('projectOwnerID'));
        $stausisdelete = trim($this->input->post('statusisdelete'));

        $data['isDelete'] = $stausisdelete;
        $this->db->where('projectOwnerID', $projectownerid);
        $this->db->where('projectID', $projectid);
        $this->db->update('srp_erp_ngo_projectowners', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Is Delete Status Update Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Is Delete status updated successfully!.');
        }
    }

    function convert_project_proposal_to_project()
    {
        $proposalid = trim($this->input->post('proposalid'));
        $description = trim($this->input->post('description'));
        $companyID = $this->common_data['company_data']['company_id'];


        $this->db->select('proposalID,beneficiaryID,isQualified');
        $this->db->from('srp_erp_ngo_projectproposalbeneficiaries');
        $this->db->where('proposalID', $proposalid);
        $this->db->where('isQualified', 1);
        $master = $this->db->get()->row_array();
        if (empty($master)) {
            return array('w', 'Please qualify at least one beneficiary to close this proposal');
        } else {
            $data['closedYN'] = trim($this->input->post('IsActive'));
            $data['closedByEmpID'] = $this->common_data['current_userID'];
            $data['proposalConvertingComment'] = $description;
            $data['closedDate'] = $this->common_data['current_date'];

            $data['reOpenedDate'] = '';
            $data['reOpenedByEmpID'] = '';
            $data['reOpenedYN'] = 0;


            $data['status'] = 12;
            $data['proposalStageID'] = 4;

            $this->db->where('proposalID', $proposalid);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_ngo_projectproposals', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', "Proposal Closed Failed." . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Proposal Closed Successfully !');
            }
        }

    }

    function load_project_proposal_to_project()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $proposalID = trim($this->input->post('proposalid'));
        $convertFormat = convert_date_format_sql();

        return $this->db->query("select case isConvertedToProject when 0 then 'No' when 1 then 'Yes' end as isConvertedToProject,case closedYN when 0 then 'No' when 1 then 'Yes' end as closedYN,proposalConvertingComment,DATE_FORMAT(closedDate,'{$convertFormat}') AS closedDate,closedByEmpID,srp_employeesdetails.Ename2 as username from srp_erp_ngo_projectproposals LEFT JOIN srp_employeesdetails on srp_employeesdetails.EIdNo = srp_erp_ngo_projectproposals.closedByEmpID  where proposalID = {$proposalID}")->row_array();

    }

    function converted_proposal_project()
    {
        $proposalid = trim($this->input->post('proposalID'));
        $companyID = $this->common_data['company_data']['company_id'];
        $company_code = $this->common_data['company_data']['company_code'];

        $master = $this->db->query("SELECT
	pp.*,
	SUM(proposalbene.totalEstimatedValue) as totalestimated,
	proposalbene.isQualified
FROM
srp_erp_ngo_projectproposals pp
LEFT JOIN (SELECT beneficiaryID,totalEstimatedValue,isQualified,proposalID from srp_erp_ngo_projectproposalbeneficiaries propben where propben.proposalID = $proposalid) proposalbene on proposalbene.proposalID = pp.proposalID

WHERE
pp.proposalID = $proposalid
AND isQualified=1

GROUP BY
pp.proposalID")->row_array();


        $project['description'] = $master['detailDescription'];
        $project['proposalID'] = $proposalid;
        $project['projectName'] = $master['proposalName'];
        $project['startDate'] = $master['startDate'];
        $project['endDate'] = $master['endDate'];
        $serial = $this->db->query("select IF ( isnull(MAX(serialNo)), 1, (MAX(serialNo) + 1) ) AS serialNo FROM `srp_erp_ngo_projects` WHERE companyID={$companyID}")->row_array();
        $project['companyID'] = $companyID;
        $project['serialNo'] = $serial['serialNo'];
        $project['documentCode'] = 'PROJ';
        $project['masterID'] = $master['projectID'];
        $project['levelNo'] = 1;
        $project['totalNumberofHouses'] = $master['totalNumberofHouses'];
        $project['floorArea'] = $master['floorArea'];
        $project['costofhouse'] = $master['costofhouse'];
        $project['additionalCost'] = $master['additionalCost'];
        $project['EstimatedDays'] = $master['EstimatedDays'];
        $project['contractorID'] = $master['contractorID'];
        $project['companyID'] = $companyID;
        $project['totalProjectValue'] = $master['totalestimated'];

        $project['documentSystemCode'] = ($company_code . '/' . 'PROJ' . str_pad($project['serialNo'], 6, '0', STR_PAD_LEFT));
        $project['createdUserGroup'] = $this->common_data['user_group'];
        $project['createdPCID'] = $this->common_data['current_pc'];
        $project['createdUserID'] = $this->common_data['current_userID'];
        $project['createdDateTime'] = $this->common_data['current_date'];
        $project['createdUserName'] = $this->common_data['current_user'];
        $this->db->insert('srp_erp_ngo_projects', $project);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();

        $data['isConvertedToProject'] = 1;
        $data['projectSubID'] = $last_id;
        $data['convertedDate'] = $this->common_data['current_date'];
        $data['convertedByEmpID'] = $this->common_data['current_userID'];
        $this->db->where('proposalID', $proposalid);
        $this->db->where('companyID', $companyID);
        $this->db->update('srp_erp_ngo_projectproposals', $data);
        $this->db->trans_complete();

        $this->db->select('*');
        $this->db->from('srp_erp_ngo_projectproposaldonors');
        $this->db->where('proposalID', $proposalid);
        $this->db->group_by('proposalDonourID');
        $proposaldonors = $this->db->get()->result_array();


        foreach ($proposaldonors as $key => $val) {

            $serials = $this->db->query("select IF ( isnull(MAX(serialNo)), 1, (MAX(serialNo) + 1) ) AS serialNo FROM `srp_erp_ngo_commitmentmasters` WHERE companyID={$companyID}")->row_array();
            $doonordetails = $this->db->query("select * FROM `srp_erp_ngo_donors` WHERE contactID={$val['donorID']} group by contactID")->row_array();
            $proposaldetails = $this->db->query("select *,pr.projectName as projectname FROM srp_erp_ngo_projectproposals pp LEFT JOIN srp_erp_ngo_projects pr on pr.ngoProjectID = pp.projectID WHERE pp.proposalID={$proposalid} ")->row_array();
            $dataproposalcommitment = [];
            $dataproposalcommitment['serialNo'] = $serials['serialNo'];
            $dataproposalcommitment['documentCode'] = 'CMT';
            $dataproposalcommitment['donorsID'] = $val['donorID'];
            $dataproposalcommitment['documentSystemCode'] = ($company_code . '/' . 'CMT' . str_pad($dataproposalcommitment['serialNo'], 6, '0', STR_PAD_LEFT));
            $dataproposalcommitment['companyCode'] = $company_code;
            $dataproposalcommitment['companyID'] = $companyID;
            $dataproposalcommitment['documentDate'] = $master['startDate'];
            $dataproposalcommitment['commitmentExpiryDate'] = $master['endDate'];
            $dataproposalcommitment['confirmedYN'] = 1;
            $dataproposalcommitment['confirmedByEmpID'] = $this->common_data['current_userID'];
            $dataproposalcommitment['confirmedByName'] = $this->common_data['current_user'];
            $dataproposalcommitment['confirmedDate'] = $this->common_data['current_date'];
            $dataproposalcommitment['transactionExchangeRate'] = 1;
            $dataproposalcommitment['donorCurrencyID'] = $doonordetails['currencyID'];
            $dataproposalcommitment['donorCurrencyDecimalPlaces'] = $doonordetails['currencyDecimalPlaces'];
            $dataproposalcommitment['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $dataproposalcommitment['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $dataproposalcommitment['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($dataproposalcommitment['transactionCurrencyID']);
            $dataproposalcommitment['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $dataproposalcommitment['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $default_currency = currency_conversionID($dataproposalcommitment['transactionCurrencyID'], $dataproposalcommitment['companyLocalCurrencyID']);
            $dataproposalcommitment['companyLocalExchangeRate'] = $default_currency['conversion'];
            $dataproposalcommitment['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $dataproposalcommitment['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $dataproposalcommitment['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $reporting_currency = currency_conversionID($dataproposalcommitment['transactionCurrencyID'], $dataproposalcommitment['companyReportingCurrencyID']);
            $dataproposalcommitment['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $dataproposalcommitment['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $donorcurrency = currency_conversionID($dataproposalcommitment['transactionCurrencyID'], $doonordetails['currencyID']);
            $dataproposalcommitment['donorExchangeRate'] = $donorcurrency['conversion'];
            $dataproposalcommitment['createdUserGroup'] = $this->common_data['user_group'];
            $dataproposalcommitment['createdPCID'] = $this->common_data['current_pc'];
            $dataproposalcommitment['createdUserID'] = $this->common_data['current_userID'];
            $dataproposalcommitment['createdDateTime'] = $this->common_data['current_date'];
            $dataproposalcommitment['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_ngo_commitmentmasters', $dataproposalcommitment);
            $commitmentAutoId = $this->db->insert_id();

            $dataautocommitment['donorCommitmentAutoID'] = $commitmentAutoId;
            $this->db->where('proposalID', $proposalid);
            $this->db->update('srp_erp_ngo_projectproposaldonors', $dataautocommitment);


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', "Is Approval Status Update Failed." . $this->db->_error_message());
            }


            $commitmentdetails = $this->db->query("select * FROM srp_erp_ngo_commitmentmasters  WHERE commitmentAutoId={$commitmentAutoId} ")->row_array();
            $dataproposalcommitmentdetails = [];
            $dataproposalcommitmentdetails['commitmentAutoId'] = $commitmentAutoId;
            $dataproposalcommitmentdetails['commitmentExpiryDate'] = $master['endDate'];
            $dataproposalcommitmentdetails['transactionAmount'] = $val['commitedAmount'];

            $dataproposalcommitmentdetails['donorsAmount'] = $val['commitedAmount'];;
            $dataproposalcommitmentdetails['donorsExchangeRate'] = $commitmentdetails['donorExchangeRate'];
            $donorsAmount = $val['commitedAmount'] / $commitmentdetails['donorExchangeRate'];
            $dataproposalcommitmentdetails['donorsAmount'] = $donorsAmount;
            $dataproposalcommitmentdetails['donorsExchangeRate'] = $commitmentdetails['donorExchangeRate'];

            $dataproposalcommitmentdetails['companyLocalAmount'] = ($dataproposalcommitmentdetails['transactionAmount'] / $commitmentdetails['companyLocalExchangeRate']);
            $dataproposalcommitmentdetails['companyReportingAmount'] = ($dataproposalcommitmentdetails['transactionAmount'] / $commitmentdetails['companyReportingExchangeRate']);
            $dataproposalcommitmentdetails['companyLocalExchangeRate'] = $commitmentdetails['companyLocalExchangeRate'];
            $dataproposalcommitmentdetails['companyReportingExchangeRate'] = $commitmentdetails['companyReportingExchangeRate'];
            $dataproposalcommitmentdetails['modifiedPCID'] = $this->common_data['current_pc'];
            $dataproposalcommitmentdetails['modifiedUserID'] = $this->common_data['current_userID'];
            $dataproposalcommitmentdetails['modifiedUserName'] = $this->common_data['current_user'];
            $dataproposalcommitmentdetails['modifiedDateTime'] = $this->common_data['current_date'];
            $dataproposalcommitmentdetails['companyID'] = $this->common_data['company_data']['company_id'];
            $dataproposalcommitmentdetails['createdUserGroup'] = $this->common_data['user_group'];
            $dataproposalcommitmentdetails['createdPCID'] = $this->common_data['current_pc'];
            $dataproposalcommitmentdetails['createdUserID'] = $this->common_data['current_userID'];
            $dataproposalcommitmentdetails['createdUserName'] = $this->common_data['current_user'];
            $dataproposalcommitmentdetails['createdDateTime'] = $this->common_data['current_date'];
            $dataproposalcommitmentdetails['projectID'] = $proposaldetails['projectID'];
            $dataproposalcommitmentdetails['description'] = $proposaldetails['projectname'] . ' - Donor Commitments - ' . $doonordetails['name'] . ' (A/C) ';//Autocommitment description
            $this->db->insert('srp_erp_ngo_commitmentdetails', $dataproposalcommitmentdetails);

        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Proposal Convertion Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Proposal Convert To Project Successfully!',$proposalid,$last_id);

        }
    }

    function load_converted_project_proposal_details()
    {
        $proposalid = trim($this->input->post('proposalid'));
        $convertFormat = convert_date_format_sql();

        return $this->db->query("SELECT
	pp.startDate AS proposalstartdate,
	pp.endDate AS proposalendadate,
	conpp.supplierName AS proposalcontractor,
SUM((pp.totalNumberofHouses * pp.costofhouse)) as Proposaltotalcost,
pro.projectName as projectName,
pp.convertedDate as proposalconverteddate,
    DATE_FORMAT(pro.startDate,\"" . $convertFormat . "\") AS projectstartdate,
    DATE_FORMAT(pro.endDate,\"" . $convertFormat . "\") AS projectenddate,
pro.contractorID as contractor,
pro.documentSystemCode as projectdocumentsyscode,
SUM((pro.totalNumberofHouses * pro.costofhouse)) as projectcost,
pro.description as  prodescription,
		proposalbene.beneficiaryID,
SUM(proposalbene.totalEstimatedValue) as totalestimated,
pro.totalProjectValue as totalProjectValue,
pp.type

FROM
	srp_erp_ngo_projectproposals pp
	LEFT JOIN srp_erp_suppliermaster conpp on conpp.supplierAutoID = pp.contractorID
	LEFT JOIN (SELECT 
	*
	from 
	srp_erp_ngo_projects projects WHERE projects.proposalID = {$proposalid} ) pro ON pro.proposalID = pp.proposalID
	
		LEFT JOIN (SELECT beneficiaryID,totalEstimatedValue,proposalID from srp_erp_ngo_projectproposalbeneficiaries propben where propben.proposalID = {$proposalid} AND isQualified = 1 ) proposalbene on proposalbene.proposalID = pp.proposalID
	LEFT JOIN srp_erp_ngo_beneficiarymaster benmaster on benmaster.benificiaryID = proposalbene.beneficiaryID
WHERE
	pp.proposalID = {$proposalid}")->row_array();


    }

    function save_converted_project_details()
    {
        $proposalid = trim($this->input->post('proposalid'));
        $contractor = trim($this->input->post('contractorID'));
        $companyID = $this->common_data['company_data']['company_id'];
        $projecttotalcost = trim($this->input->post('totalprojectcost'));
        $date_format_policy = date_format_policy();
        $projectfrom = trim($this->input->post('projectfrom'));
        $projectfromdate = input_format_date($projectfrom, $date_format_policy);
        $date_format_policy = date_format_policy();
        $projectto = trim($this->input->post('projectto'));
        $projectodate = input_format_date($projectto, $date_format_policy);
        $type =$this->input->post('type');
        $projecttotalcostdirect =  trim($this->input->post('totalprojectcostproject'));
        $projectdescription =  trim($this->input->post('detailDescription'));


        $data['startDate'] = $projectfromdate;
        $data['endDate'] = $projectodate;
        $data['contractorID'] = $contractor;
         if($type == 1 || $type==" ")
         {
             $data['totalProjectValue'] = $projecttotalcost;
         }else
         {
             $data['totalProjectValue'] = $projecttotalcostdirect;
             $data['description'] = $projectdescription;
         }


        $this->db->where('proposalID', $proposalid);
        $this->db->where('companyID', $companyID);
        $this->db->update('srp_erp_ngo_projects', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Project Details Updated Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Project Details Updated Successfully!');
        }

    }

    function load_proposal_details_view()
    {
        $proposalid = trim($this->input->post('proposalid'));
        $convertFormat = convert_date_format_sql();

        return $this->db->query("SELECT
	documentSystemCode,
	proposalName,
	proposalTitle,
	COUNT(proposalben.beneficiaryID) as beneficiarycount,
	SUM(proposalben.totalEstimatedValue) as totalEstimatedValue
	
FROM
	srp_erp_ngo_projectproposals projectproposal 
	

LEFT JOIN (SELECT beneficiaryID,totalEstimatedValue,proposalID from srp_erp_ngo_projectproposalbeneficiaries proben where proben.proposalID = $proposalid AND proben.isQualified = 1

) proposalben on proposalben.proposalID = projectproposal.proposalID
	LEFT JOIN srp_erp_ngo_beneficiarymaster benmaster on benmaster.benificiaryID = proposalben.beneficiaryID

WHERE
	projectproposal.proposalID = $proposalid")->row_array();


    }

    function send_proposal_donors()
    {
        {
            $Donors = $this->input->post('donorID');
            $donorData = $this->db->query("SELECT name,email FROM srp_erp_ngo_donors WHERE contactID = {$Donors}")->row_array();

            $proposalID = $this->input->post('proposalDonourID');
            $companyID = current_companyID();
            $convertFormat = convert_date_format_sql();

            $data['master'] = $this->db->query("SELECT pro.projectImage,pp.proposalName as ppProposalName,pro.projectName as proProjectName,DATE_FORMAT(pp.DocumentDate,'{$convertFormat}') AS DocumentDate,DATE_FORMAT(pp.startDate,'{$convertFormat}') AS ppStartDate,DATE_FORMAT(pp.endDate,'{$convertFormat}') AS ppEndDate,DATE_FORMAT(pp.DocumentDate, '%M %Y') as subprojectName,pp.detailDescription as ppDetailDescription,pp.projectSummary as ppProjectSummary,pp.totalNumberofHouses as ppTotalNumberofHouses,pp.floorArea as ppFloorArea,pp.costofhouse as ppCostofhouse,pp.additionalCost as ppAdditionalCost,pp.EstimatedDays as ppEstimatedDays,pp.proposalTitle as ppProposalTitle,pp.processDescription as ppProcessDescription,con.supplierName as contractorName,ca.GLDescription as caBankAccName,ca.bankName as caBankName,ca.bankAccountNumber as caBankAccountNumber FROM srp_erp_ngo_projectproposals pp JOIN srp_erp_ngo_projects pro ON pp.projectID = pro.ngoProjectID LEFT JOIN srp_erp_suppliermaster con ON pp.contractorID = con.supplierAutoID LEFT JOIN srp_erp_chartofaccounts ca ON ca.GLAutoID = pp.bankGLAutoID WHERE pp.proposalID = $proposalID  ")->row_array();

            $data['detail'] = $this->db->query("SELECT ppb.beneficiaryID as ppbBeneficiaryID,ppb.totalSqFt as proposalbentotalSqFt,ppb.totalEstimatedValue as proposaltotalEstimatedValue,DATE_FORMAT(bm.registeredDate,'{$convertFormat}') AS bmRegisteredDate,DATE_FORMAT(bm.dateOfBirth,'{$convertFormat}') AS bmDateOfBirth,bm.nameWithInitials as bmNameWithInitials,bm.systemCode as bmSystemCode, CASE bm.ownLandAvailable WHEN 1 THEN 'Yes' WHEN 2 THEN 'No' END as bmOwnLandAvailable,bm.NIC as bmNIC,bm.totalEstimatedValue as totalEstimatedValue,bm.familyMembersDetail as bmFamilyMembersDetail,bm.reasoninBrief as bmReasoninBrief,bm.totalSqFt as bmTotalSqFt,bm.totalCost as bmTotalCost,bm.helpAndNestImage as bmHelpAndNestImage,bm.helpAndNestImage1 as bmHelpAndNestImage1,bm.ownLandAvailableComments as bmOwnLandAvailableComments FROM srp_erp_ngo_projectproposalbeneficiaries ppb LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON ppb.beneficiaryID = bm.benificiaryID WHERE proposalID = $proposalID ")->result_array();

            $data['images'] = $this->db->query("SELECT imageType,imageName FROM srp_erp_ngo_projectproposalimages WHERE ngoProposalID = $proposalID ")->result_array();

            $data['moto'] = $this->db->query("SELECT companyPrintTagline FROM srp_erp_company WHERE company_id = {$companyID}")->row_array();

            $data['proposalID'] = $proposalID;

            $this->load->view('system/operationNgo/ngo_beneficiary_helpnest_print_all', $data);
            $path = UPLOAD_PATH . "/gs_sme/uploads/NGO/ProjectProposal_" . $proposalID . ".pdf";

            if (!empty($donorData['email'])) {
                $param = array();
                $param["empName"] = $donorData["name"];
                $param["body"] = 'We are pleased to submit our project proposal as follow. <br/>
                                          <table border="0px">
                                          </table>';
                $mailData = [
                    'approvalEmpID' => '',
                    'documentCode' => '',
                    'toEmail' => $donorData["email"],
                    'subject' => 'Project Proposal',
                    'param' => $param
                ];
                send_approvalEmail($mailData, 1, $path);
            }
            return array('s', 'Email Send Successfully.');

        }
    }

    function update_donors_status()
    {
        $this->db->trans_start();
        $donorID = trim($this->input->post('issubmitted'));
        $proposal = trim($this->input->post('proposaldonor'));
        $status = trim($this->input->post('issubmit'));

        $data['isSubmitted'] = $status;
        $data['submittedDate'] = $this->common_data['current_date'];
        $this->db->where('donorID', $donorID);
        $this->db->where('proposalID', $proposal);
        $this->db->update('srp_erp_ngo_projectproposaldonors', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Is Submited Status Update Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Is submited status updated successfully!.');
        }
    }

    function load_project_proposal_details()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $proposalID = trim($this->input->post('proposalid'));

        return $this->db->query("SELECT
	ppm.proposalID,
	ppben.netTotal,
	donorcom.commitedamt,
	bencount.Beneficiarycount,
	bencountqualified.Beneficiarycountqualified,
	CONCAT( IFNULL(Beneficiarycountqualified,'0'), \" / \", \" ( \", IFNULL(Beneficiarycount,'0'), \" ) \" ) AS Bentotalqual, 
	CONCAT( IFNULL(donorcom.commitedamt,'0'), \" / \", \" ( \", IFNULL(ppben.netTotal,'0'), \" ) \" ) AS total
FROM
	srp_erp_ngo_projectproposals ppm
	LEFT JOIN ( SELECT COUNT( proposalBeneficiaryID ) AS Beneficiarycount, proposalID FROM srp_erp_ngo_projectproposalbeneficiaries WHERE proposalID = $proposalID ) bencount ON bencount.proposalID = ppm.proposalID
	
	LEFT JOIN ( SELECT COUNT( proposalBeneficiaryID ) AS Beneficiarycountqualified, proposalID FROM srp_erp_ngo_projectproposalbeneficiaries WHERE proposalID = $proposalID AND isQualified = 1 ) bencountqualified ON bencountqualified.proposalID = ppm.proposalID 
	
LEFT JOIN (
SELECT
ppben.*,
SUM(IFNULL(ppben.totalEstimatedValue,0)) AS netTotal

FROM 
srp_erp_ngo_projectproposalbeneficiaries ppben

LEFT JOIN 

srp_erp_ngo_beneficiarymaster benmaster on benmaster.benificiaryID = ppben.beneficiaryID

WHERE
proposalID = $proposalID
AND ppben.isQualified = 1
) ppben on ppben.proposalID = ppm.proposalID


LEFT JOIN (SELECT SUM(IFNULL(propdonors.commitedAmount,0)) AS commitedamt,proposalID FROM srp_erp_ngo_projectproposaldonors propdonors where proposalID = $proposalID) donorcom on donorcom.proposalID = ppm.proposalID

WHERE
	ppm.companyID = $companyID 
	AND ppm.proposalID = $proposalID 
GROUP BY
	ppm.proposalID 
ORDER BY
	ppm.proposalID DESC")->row_array();

    }

    function closed_proposal_reopen()
    {
        $proposalid = trim($this->input->post('proposalid'));

        $this->db->select('isConvertedToProject');
        $this->db->from('srp_erp_ngo_projectproposals');
        $this->db->where('companyID', current_companyID());
        $this->db->where('proposalID', $proposalid);
        $this->db->where('isConvertedToProject', 1);
        $result = $this->db->get()->row_array();
        if (!empty($result)) {
            return array('e', 'Project is created cannot reopen this propsal');
        } else {
            $companyID = $this->common_data['company_data']['company_id'];
            $data['reOpenedDate'] = $this->common_data['current_date'];
            $data['reOpenedByEmpID'] = $this->common_data['current_userID'];
            $data['reOpenedYN'] = 1;
            $data['closedYN'] = 0;
            $data['closedByEmpID'] = '';
            $data['closedDate'] = '';
            $data['status'] = 12;
            $data['proposalStageID'] = 3;
            $this->db->where('proposalID', $proposalid);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_ngo_projectproposals', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', "Proposal Convertion Failed." . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Proposal reopen successfully!.');
            }
        }


    }

    function load_project_header()
    {
        $convertFormat = convert_date_format_sql();
        $proposalID = trim($this->input->post('proposalID'));
        $data = $this->db->query("select proposalID,type,projectSubID,DATE_FORMAT(DocumentDate,'{$convertFormat}') AS DocumentDate,DATE_FORMAT(startDate,'{$convertFormat}') AS startDate,DATE_FORMAT(endDate,'{$convertFormat}') AS endDate FROM srp_erp_ngo_projectproposals WHERE proposalID = {$proposalID} ")->row_array();
        return $data;
    }

    function load_project_details()
    {
        $convertFormat = convert_date_format_sql();
        $projectid = trim($this->input->post('projectid'));
        $data = $this->db->query("select *,CONCAT(documentSystemCode,' | ',projectName ,' | ','Project Cost : ',totalProjectValue) as description,DATE_FORMAT(startDate,'{$convertFormat}') AS startDate,DATE_FORMAT(endDate,'{$convertFormat}') AS endDate FROM srp_erp_ngo_projects WHERE ngoProjectID = {$projectid} ")->row_array();
        return $data;
    }

    function fetch_stages_project()
    {
        $stageid = $this->input->post('defaultStageID');
        $data = $this->db->query("select * FROM srp_erp_ngo_defaultprojectstages WHERE defaultStageID = {$stageid} ")->row_array();
        return $data;
    }

    function save_project_stages()
    {
        $project_id = $this->input->post('project_id');
        $stage_default_id = $this->input->post('projectstages');
        $percentage = $this->input->post('percentage');
        $stagedescription = $this->input->post('stagedescription');
        $amount = $this->input->post('Amount');
        $percentage_tot = 0;
        $remainignpercentage = 0;
        $results = $this->db->query("SELECT COALESCE(SUM(percentage),0) as percentage FROM srp_erp_ngo_projectstages where  ngoProjectID = $project_id")->row_array();
        $this->db->trans_begin();
        $isIncre = 0;
        if (!empty($stage_default_id)) {
            foreach ($stage_default_id as $key => $val) {
                $percentage_tot += $percentage[$key];
                $remainignpercentage = 100 - $results['percentage'];

                if ($percentage_tot > 100 || $remainignpercentage < $percentage_tot) {
                    $isIncre++;

                } else {
                    $data['ngoProjectID'] = $project_id;
                    $data['defaultStageID'] = $val;
                    $data['percentage'] = $percentage[$key];
                    $data['description'] = $stagedescription[$key];
                    $data['stageAmount'] = $amount[$key];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['companyID'] = current_companyID();
                    $data['createdPCID'] = current_pc();
                    $data['CreatedUserName'] = current_employee();
                    $data['createdDateTime'] = current_date();
                    $this->db->insert('srp_erp_ngo_projectstages', $data);
                }
            }


            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Stage insertion failed ' . $this->db->_error_message());
            } else {
                if ($isIncre > 0) {
                    $this->db->trans_rollback();
                    return array('e', 'Percentage Total Should less than or equal to 100');
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Project Stage Successfully Added.');
                }
            }

        }


    }

    function delete_project_stages()
    {
        $stageid = $this->input->post('projectStageID');

        $this->db->select('*');
        $this->db->from('srp_erp_ngo_projectstagedetails');
        $this->db->where('projectStageID', $stageid);
        $results = $this->db->get()->row_array();
        if ($results) {
            return array('e', 'Please delete all the claims before deleting this project step.');
        } else {
            $this->db->delete('srp_erp_ngo_projectstages', array('projectStageID' => $stageid));
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Error while deleting!');
            } else {
                $this->db->trans_commit();
                return array('s', 'Project stage deleted successfully');
            }
        }


    }

    function fetch_project_stages()
    {
        $stageid = $this->input->post('projectStageID');
        $comapnyid = $this->common_data['company_data']['company_id'];
        $data = $this->db->query("select prostages.*,
        defpro.description as projectstages
FROM
	srp_erp_ngo_projectstages prostages
	LEFT JOIN srp_erp_ngo_defaultprojectstages defpro on defpro.defaultStageID = prostages.defaultStageID
WHERE
	projectStageID = $stageid
    AND defpro.companyID = $comapnyid")->row_array();
        return $data;
    }

    function update_project_stages()
    {
        $projectStageID = trim($this->input->post('project_stage_id'));
        $companyID = $this->common_data['company_data']['company_id'];
        $descriptionupdate = trim($this->input->post('stagedescriptionupdate'));
        $percentageupdate = trim($this->input->post('percentageupdate'));
        $amountupdate = trim($this->input->post('amountupdate'));
        $project_id = trim($this->input->post('projectidstages'));


        $percentage_tot = 0;
        $remainignpercentage = 0;


        $results = $this->db->query("SELECT 
	( SUM( proper.percentage ) - prostageper.percentage ) AS percentage 
FROM
	srp_erp_ngo_projectstages proper
	LEFT JOIN (SELECT percentage,projectStageID FROM srp_erp_ngo_projectstages where projectStageID = $projectStageID) prostageper on prostageper.projectStageID = proper.projectStageID
	
WHERE
proper.ngoProjectID = $project_id")->row_array();

        $remainignpercentage = 100 - $results['percentage'];

        if ($percentageupdate > $remainignpercentage) {
            return array('e', "Percentage should be less than equal to 100.");

        } else {
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['projectStageID'] = $projectStageID;
            $data['percentage'] = $percentageupdate;
            $data['stageAmount'] = $amountupdate;
            $data['description'] = $descriptionupdate;
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->where('projectStageID', $projectStageID);
            $this->db->update('srp_erp_ngo_projectstages', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', "Project stage Insertion Failed." . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Project stage Updated successfully.');
            }
        }


    }

    function project_claims()
    {
        $projectStageID = trim($this->input->post('stage_id'));
        $companyID = $this->common_data['company_data']['company_id'];
        $description = $this->input->post('description');
        $amount = $this->input->post('amount');
        $glcode = $this->input->post('glcode');
        $projectStageDetailID = trim($this->input->post('projectStageDetailID'));
        $reamining_stage_amt = 0;
        $totalamt = 0;
        $isIncre = 0;
        $stageamount = $this->db->query("SELECT stageAmount from srp_erp_ngo_projectstages where projectStageID = $projectStageID")->row_array();
        $stagedetailamt = $this->db->query("SELECT coalesce(sum(amount),0) as amount from srp_erp_ngo_projectstagedetails where projectStageID = $projectStageID")->row_array();
        $reamining_stage_amt = $stageamount['stageAmount'] - $stagedetailamt['amount'];
        foreach ($amount as $val) {
            $totalamt += $val;
        }
        if (!$projectStageDetailID) {
            if ($totalamt > $reamining_stage_amt) {
                $isIncre++;
            } else {
                foreach ($description as $key => $projectstagedes) {
                    $data_project_steps['projectStageID'] = $projectStageID;
                    $data_project_steps['description'] = $projectstagedes;
                    $data_project_steps['glcode'] = $glcode[$key];
                    $data_project_steps['amount'] = $amount[$key];
                    $data_project_steps['companyID'] = current_companyID();
                    $data_project_steps['createdUserID'] = $this->common_data['current_userID'];
                    $data_project_steps['createdPCID'] = current_pc();
                    $data_project_steps['CreatedUserName'] = current_employee();
                    $data_project_steps['createdDateTime'] = current_date();
                    $this->db->insert('srp_erp_ngo_projectstagedetails', $data_project_steps);

                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Claim Added Faild ' . $this->db->_error_message());
            } else {
                if ($isIncre > 0) {
                    $this->db->trans_rollback();
                    return array('e', 'Total should be less than or equla to project stage amount');
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Claim Added Successfully.');
                }
            }
        }
    }

    function fetch_project_detail()
    {
        $projectstageid = $this->input->post('projectStageDetailID');
        $comapnyid = $this->common_data['company_data']['company_id'];
        $data = $this->db->query("SELECT *
FROM
	srp_erp_ngo_projectstagedetails 
WHERE
	projectStageDetailID = $projectstageid
    AND companyID = $comapnyid")->row_array();
        return $data;
    }

    function delete_project_stage_steps()
    {
        $projectstageid = $this->input->post('projectStageDetailID');
        $this->db->delete('srp_erp_ngo_projectstagedetails', array('projectStageDetailID' => $projectstageid));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error while deleting!');
        } else {
            $this->db->trans_commit();
            return array('s', 'Stage claim deleted successfully');
        }
    }

    function update_project_details()
    {
        $projectStageID = $this->input->post('projectstageid');
        $projectstagedetailsid = $this->input->post('projectStageDetailID');
        $amount = $this->input->post('amountedit');
        $reamining_stage_amt = 0;

        $stageamount = $this->db->query("SELECT stageAmount from srp_erp_ngo_projectstages where projectStageID = $projectStageID")->row_array();
        $stagedetailamt = $this->db->query("SELECT coalesce(sum(amount),0) as amount from srp_erp_ngo_projectstagedetails where projectStageID = $projectStageID AND projectStageDetailID != $projectstagedetailsid")->row_array();

        $reamining_stage_amt = $stageamount['stageAmount'] - $stagedetailamt['amount'];

        if ($amount > $reamining_stage_amt) {
            return array('e', 'Project stage detail amount should be less than or equal to stage amount');

        } else {
            $data['description'] = $this->input->post('descriptionedit');
            $data['amount'] = $this->input->post('amountedit');
            $data['glcode'] = $this->input->post('glcodeedit');

            $this->db->where('projectStageDetailID', $projectstagedetailsid);
            $result = $this->db->update('srp_erp_ngo_projectstagedetails', $data);
            if ($result) {
                return array('s', 'Project stage detail updated successfully');
            } else {
                return array('e', 'Project stage detail updated  Failed');
            }
        }


    }

    function fetch_project_description()
    {
        $this->db->select('prodetails.projectStageDetailID,description,amount,CONCAT(systemAccountCode, \'|\',GLSecondaryCode, \'|\',GLDescription, \'|\',subCategory) AS glcode,prodetails.glcode as glid');
        $this->db->from('srp_erp_ngo_projectstagedetails prodetails');
        $this->db->join('srp_erp_chartofaccounts chartofacc', 'chartofacc.GLAutoID = prodetails.glcode ');
        $this->db->where('prodetails.projectStageID', trim($this->input->post('projectStageID')));
        $this->db->where('prodetails.companyID', current_companyID());
        $this->db->where('prodetails.isClaimedYN', 0);
        $results = $this->db->get()->result_array();
        return $results;

    }

    function save_project_claim_docdate_narration()
    {
        $date_format_policy = date_format_policy();
        $documentDate = $this->input->post('documentDate');
        $documetdateconverted = input_format_date($documentDate, $date_format_policy);
        $comapnyid = $this->common_data['company_data']['company_id'];
        $narration = $this->input->post('narration');
        $projectid = $this->input->post('projectid');
        $segment = $this->input->post('segment');
        $convertdateformat = convert_date_format_sql();


        $finacialYearid = $this->db->query("SELECT companyFinanceYearID,companyFinancePeriodID,dateFrom,dateTo,concat(DATE_FORMAT(dateFrom,'{$convertdateformat}'),\" - \", DATE_FORMAT(dateTo,'{$convertdateformat}'))as companyFinanceYear FROM srp_erp_companyfinanceperiod WHERE '$documetdateconverted' BETWEEN dateFrom AND dateTo AND isActive = 1 AND companyID = $comapnyid AND isCurrent = 1")->row_array();
        $supplierid = $this->db->query("select contractorID from  srp_erp_ngo_projects where ngoProjectID = $projectid AND companyID = $comapnyid")->row_array();
        $supplierdetails = $this->db->query("select *,concat(supplierAddress1,\" , \", supplierAddress2)as supplieraddress from  srp_erp_suppliermaster where supplierAutoID =       {$supplierid['contractorID']} AND companyID = $comapnyid")->row_array();


        if (empty($finacialYearid)) {
            return array('e', 'Date not between financial year');
        } else {
            $segmentcode = explode('|', $segment);
            $data['documentID'] = 'BSI';
            $data['invoiceType'] = 'Standard';
            $data['companyFinanceYearID'] = $finacialYearid['companyFinanceYearID'];
            $data['companyFinanceYear'] = $finacialYearid['companyFinanceYear'];
            $data['FYBegin'] = $finacialYearid['dateFrom'];
            $data['FYEnd'] = $finacialYearid['dateTo'];
            $data['companyFinancePeriodID'] = $finacialYearid['companyFinancePeriodID'];
            $data['comments'] = $narration;
            $data['supplierID'] = $supplierdetails['supplierAutoID'];
            $data['supplierCode'] = $supplierdetails['supplierSystemCode'];
            $data['supplierName'] = $supplierdetails['supplierName'];
            $data['supplierAddress'] = $supplierdetails['supplierAddress1'];
            $data['supplierTelephone'] = $supplierdetails['supplierTelephone'];
            $data['supplierFax'] = $supplierdetails['supplierFax'];
            $data['supplierliabilityAutoID'] = $supplierdetails['liabilityAutoID'];
            $data['supplierliabilitySystemGLCode'] = $supplierdetails['liabilitySystemGLCode'];
            $data['supplierliabilityGLAccount'] = $supplierdetails['liabilityGLAccount'];
            $data['supplierliabilityDescription'] = $supplierdetails['liabilityDescription'];
            $data['supplierliabilityType'] = $supplierdetails['liabilityType'];
            $data['bookingInvCode'] = 0;
            $data['bookingDate'] = $documetdateconverted;
            $data['invoiceDate'] = $documetdateconverted;
            $data['invoiceDueDate'] = $documetdateconverted;
            $data['invoiceDueDate'] = $documetdateconverted;
            $data['segmentID'] = $segmentcode[0];
            $data['segmentCode'] = $segmentcode[1];
            $data['transactionCurrencyID'] = 14;
            $data['transactionCurrency'] = 'LKR';
            $data['transactionExchangeRate'] = 1;
            $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
            $data['companyLocalExchangeRate'] = $default_currency['conversion'];
            $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $data['supplierCurrencyID'] = $supplierdetails['supplierCurrencyID'];
            $data['supplierCurrency'] = $supplierdetails['supplierCurrency'];
            $supplierCurrency = currency_conversionID($data['transactionCurrencyID'], $data['supplierCurrencyID']);
            $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
            $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
            $data['companyID'] = $comapnyid;
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['timestamp'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_paysupplierinvoicemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Supplier Invoice Saved faile ' . $this->db->_error_message(), $last_id);
            } else {
                $this->db->trans_commit();
                return array('s', 'Supplier Invoice Saved Successfully.', $last_id);
            }
        }

    }

    function save_project_step_details()
    {
        $projectIDngo = $this->input->post('projectid');
        $comapnyid = $this->common_data['company_data']['company_id'];
        $inoiveauto = $this->input->post('invoiceAutoID');
        $projectstageid = $this->input->post('projectStageDetailID');
        $this->db->trans_start();

        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate,transactionExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces,transactionCurrencyID,segmentID,segmentCode');
        $this->db->where('InvoiceAutoID', $this->input->post('invoiceAutoID'));
        $master = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

        $projectdetails = $this->db->query("select concat(documentSystemCode,\" - \", projectName)as projectdescription from  srp_erp_ngo_projects where ngoProjectID =       $projectIDngo AND companyID = $comapnyid")->row_array();
        $gl_code_des = $this->input->post('glcode');
        $gl_codes = $this->input->post('glid');
        $amount = $this->input->post('amt');
        $projectStageDetailID = $this->input->post('projectStageDetailID');
        foreach ($gl_codes as $key => $gl_code) {
            $gl_code = explode('|', $gl_code_des[$key]);

            $data[$key]['invoiceAutoID'] = trim($this->input->post('invoiceAutoID'));
            $data[$key]['GLAutoID'] = $gl_codes[$key];
            $data[$key]['systemGLCode'] = trim($gl_code[0]);
            $data[$key]['GLCode'] = trim($gl_code[1]);
            $data[$key]['GLDescription'] = trim($gl_code[2]);
            $data[$key]['GLType'] = trim($gl_code[3]);
            $data[$key]['segmentID'] = $master['segmentID'];
            $data[$key]['segmentCode'] = $master['segmentCode'];
            $data[$key]['description'] = ' Project Payment for ' . $projectdetails['projectdescription'];
            $data[$key]['transactionAmount'] = round($amount[$key], $master['transactionCurrencyDecimalPlaces']);
            $data[$key]['transactionExchangeRate'] = $master['transactionExchangeRate'];
            $companyLocalAmount = 0;
            if ($master['companyLocalExchangeRate']) {
                $companyLocalAmount = $data[$key]['transactionAmount'] / $master['companyLocalExchangeRate'];
            }
            $data[$key]['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $data[$key]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $companyReportingAmount = 0;
            if ($master['companyReportingExchangeRate']) {
                $companyReportingAmount = $data[$key]['transactionAmount'] / $master['companyReportingExchangeRate'];
            }
            $data[$key]['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $data[$key]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $supplierAmount = 0;
            if ($master['supplierCurrencyExchangeRate']) {
                $supplierAmount = $data[$key]['transactionAmount'] / $master['supplierCurrencyExchangeRate'];
            }
            $data[$key]['supplierAmount'] = round($supplierAmount, $master['supplierCurrencyDecimalPlaces']);
            $data[$key]['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $data[$key]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdUserName'] = $this->common_data['current_user'];
            $data[$key]['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_paysupplierinvoicedetail', $data[$key]);

            $invoicedetailautoid = $this->db->insert_id();

            $invoiceidproject['claimedInvoiceAutoID'] = $inoiveauto;
            $invoiceidproject['claimInvoiceDetailAutoID'] = $invoicedetailautoid;
            $invoiceidproject['isClaimedYN'] = 1;
            $this->db->update('srp_erp_ngo_projectstagedetails', $invoiceidproject, array('projectStageDetailID' => $projectstageid[$key]));
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Supplier Invoice Detail : Saved Failed ');
        } else {
            $this->db->trans_commit();
            return array('s', 'Supplier Invoice Detail : Saved Successfully.');
        }
    }

    function supplier_invoice_confirmation()
    {
        $this->db->select('InvoiceAutoID');
        $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID')));
        $this->db->from('srp_erp_paysupplierinvoicedetail');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            $this->session->set_flashdata('w', 'There are no records to confirm this document!');
            return false;
        } else {
            $this->db->select('InvoiceAutoID');
            $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID')));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_paysupplierinvoicemaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return array('status' => true);
            } else {
                $this->db->trans_start();
                $system_id = trim($this->input->post('InvoiceAutoID'));
                $this->db->select('bookingInvCode,companyFinanceYearID,DATE_FORMAT(bookingDate, "%Y") as invYear,DATE_FORMAT(bookingDate, "%m") as invMonth');
                $this->db->where('InvoiceAutoID', $system_id);
                $this->db->from('srp_erp_paysupplierinvoicemaster');
                $master_dt = $this->db->get()->row_array();
                $this->load->library('sequence');
                $lenth = strlen($master_dt['bookingInvCode']);
                if ($lenth == 1) {
                    $invcod = array(
                        'bookingInvCode' => $this->sequence->sequence_generator_fin('BSI', $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']),
                    );
                    $this->db->where('InvoiceAutoID', $system_id);
                    $this->db->update('srp_erp_paysupplierinvoicemaster', $invcod);
                }

                $this->load->library('approvals');
                $this->db->select('InvoiceAutoID, bookingInvCode,transactionCurrency,transactionExchangeRate,companyFinanceYearID,DATE_FORMAT(bookingDate, "%Y") as invYear,DATE_FORMAT(bookingDate, "%m") as invMonth');
                $this->db->where('InvoiceAutoID', $system_id);
                $this->db->from('srp_erp_paysupplierinvoicemaster');
                $master_data = $this->db->get()->row_array();

                $doc_approved['departmentID'] = "BSI";
                $doc_approved['documentID'] = "BSI";
                $doc_approved['documentCode'] = $master_data['bookingInvCode'];
                $doc_approved['documentSystemCode'] = $master_data['InvoiceAutoID'];
                $doc_approved['documentDate'] = current_date();
                $doc_approved['approvalLevelID'] = 1;
                $doc_approved['docConfirmedDate'] = current_date();
                $doc_approved['docConfirmedByEmpID'] = current_userID();
                $doc_approved['table_name'] = 'srp_erp_paysupplierinvoicemaster';
                $doc_approved['table_unique_field_name'] = 'InvoiceAutoID';
                $doc_approved['approvedEmpID'] = current_userID();
                $doc_approved['approvedYN'] = 1;
                $doc_approved['approvedComments'] = 'Approved for project claim - Operation NGO';
                $doc_approved['approvedPC'] = current_pc();
                $doc_approved['approvedDate'] = current_date();
                $doc_approved['companyID'] = current_companyID();
                $doc_approved['companyCode'] = current_company_code();
                $this->db->insert('srp_erp_documentapproved', $doc_approved);

                $transa_total_amount = 0;
                $loca_total_amount = 0;
                $rpt_total_amount = 0;
                $supplier_total_amount = 0;
                $t_arr = array();
                $tra_tax_total = 0;
                $loca_tax_total = 0;
                $rpt_tax_total = 0;
                $sup_tax_total = 0;
                $this->db->select('sum(transactionAmount) as transactionAmount,sum(companyLocalAmount) as companyLocalAmount,sum(companyReportingAmount) as companyReportingAmount,sum(supplierAmount) as supplierAmount');
                $this->db->where('InvoiceAutoID', $system_id);
                $data_arr = $this->db->get('srp_erp_paysupplierinvoicedetail')->row_array();

                $transa_total_amount += $data_arr['transactionAmount'];
                $loca_total_amount += $data_arr['companyLocalAmount'];
                $rpt_total_amount += $data_arr['companyReportingAmount'];
                $supplier_total_amount += $data_arr['supplierAmount'];

                $this->db->select('taxDetailAutoID,supplierCurrencyExchangeRate,companyReportingExchangeRate,companyLocalExchangeRate ,taxPercentage');
                $this->db->where('InvoiceAutoID', $system_id);
                $tax_arr = $this->db->get('srp_erp_paysupplierinvoicetaxdetails')->result_array();
                for ($x = 0; $x < count($tax_arr); $x++) {
                    $tax_total_amount = (($tax_arr[$x]['taxPercentage'] / 100) * $transa_total_amount);
                    $t_arr[$x]['taxDetailAutoID'] = $tax_arr[$x]['taxDetailAutoID'];
                    $t_arr[$x]['transactionAmount'] = $tax_total_amount;
                    $t_arr[$x]['supplierCurrencyAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['supplierCurrencyExchangeRate']);
                    $t_arr[$x]['companyLocalAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyLocalExchangeRate']);
                    $t_arr[$x]['companyReportingAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyReportingExchangeRate']);
                    $tra_tax_total = $t_arr[$x]['transactionAmount'];
                    $sup_tax_total = $t_arr[$x]['supplierCurrencyAmount'];
                    $loca_tax_total = $t_arr[$x]['companyLocalAmount'];
                    $rpt_tax_total = $t_arr[$x]['companyReportingAmount'];
                }
                /*updating transaction amount using the query used in the master data table  done by mushtaq*/
                $companyID = current_companyID();
                $r1 = "SELECT
srp_erp_paysupplierinvoicemaster.InvoiceAutoID,
	`srp_erp_paysupplierinvoicemaster`.`companyLocalExchangeRate` AS `companyLocalExchangeRate`,
	`srp_erp_paysupplierinvoicemaster`.`companyLocalCurrencyDecimalPlaces` AS `companyLocalCurrencyDecimalPlaces`,
	`srp_erp_paysupplierinvoicemaster`.`companyReportingExchangeRate` AS `companyReportingExchangeRate`,
	`srp_erp_paysupplierinvoicemaster`.`companyReportingCurrencyDecimalPlaces` AS `companyReportingCurrencyDecimalPlaces`,
	`srp_erp_paysupplierinvoicemaster`.`supplierCurrencyExchangeRate` AS `supplierCurrencyExchangeRate`,
	`srp_erp_paysupplierinvoicemaster`.`supplierCurrencyDecimalPlaces` AS `supplierCurrencyDecimalPlaces`,
	`srp_erp_paysupplierinvoicemaster`.`transactionCurrencyDecimalPlaces` AS `transactionCurrencyDecimalPlaces`,
	(
		(
			(
				IFNULL(addondet.taxPercentage, 0) / 100
			) * IFNULL(det.transactionAmount, 0)
		) + IFNULL(det.transactionAmount, 0)
	) AS total_value
FROM
	`srp_erp_paysupplierinvoicemaster`
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		InvoiceAutoID
	FROM
		srp_erp_paysupplierinvoicedetail
	GROUP BY
		InvoiceAutoID
) det ON (
	`det`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		InvoiceAutoID
	FROM
		srp_erp_paysupplierinvoicetaxdetails
	GROUP BY
		InvoiceAutoID
) addondet ON (
	`addondet`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
)
WHERE
	`companyID` = $companyID
	AND srp_erp_paysupplierinvoicemaster.InvoiceAutoID = $system_id ";
                $totalValue = $this->db->query($r1)->row_array();
                $data = array(
                    'confirmedYN' => 1,
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                    'confirmedByName' => $this->common_data['current_user'],
                    'approvedYN' => 1,
                    'approvedDate' => $this->common_data['current_date'],
                    'approvedbyEmpID' => $this->common_data['current_userID'],
                    'approvedbyEmpName' => $this->common_data['current_user'],
                    'companyLocalAmount' => (round($totalValue['total_value'] / $totalValue['companyLocalExchangeRate'], $totalValue['companyLocalCurrencyDecimalPlaces'])),
                    'companyReportingAmount' => (round($totalValue['total_value'] / $totalValue['companyReportingExchangeRate'], $totalValue['companyReportingCurrencyDecimalPlaces'])),
                    'supplierCurrencyAmount' => (round($totalValue['total_value'] / $totalValue['supplierCurrencyExchangeRate'], $totalValue['supplierCurrencyDecimalPlaces'])),
                    'transactionAmount' => (round($totalValue['total_value'], $totalValue['transactionCurrencyDecimalPlaces'])),
                );
                $this->db->where('InvoiceAutoID', $system_id);
                $this->db->update('srp_erp_paysupplierinvoicemaster', $data);
                if (!empty($t_arr)) {
                    $this->db->update_batch('srp_erp_paysupplierinvoicetaxdetails', $t_arr, 'taxDetailAutoID');
                }
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Supplier Invoice Confirmed  failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    /*$this->session->set_flashdata('s', 'Supplier Invoice Generated Successfully.');
                    $this->db->trans_commit();*/
                    return array('status' => true);
                }
            }
        }
    }
    function assign_beneficiary_for_project_direct()
    {
        $proposalID = $this->input->post('proposalid');
        $ischeacked = $this->input->post('is_cheacked[]');
        $totalestimatedvalue = $this->input->post('totalestimatedvalue[]');
        $selectedItem = $this->input->post('is_cheacked_benid[]');
        $totalsqft = $this->input->post('totalsqft[]');
        $totalcost = $this->input->post('totalcost[]');
        $data = [];

        foreach ($selectedItem as $key => $vals) {
            if ($ischeacked[$key] == 1) {
                $thisamount = (!empty($totalestimatedvalue[$key])) ? $totalestimatedvalue[$key] : 0;
                $totalsqftben = (!empty($totalsqft[$key])) ? $totalsqft[$key] : 0;
                $totalcostben = (!empty($totalcost[$key])) ? $totalcost[$key] : 0;
                $data[$key]['beneficiaryID'] = $vals;
                $data[$key]['proposalID'] = $proposalID;
                $data[$key]['companyID'] = current_companyID();
                $data[$key]['createdUserGroup'] = current_user_group();
                $data[$key]['createdPCID'] = current_pc();
                $data[$key]['createdUserID'] = current_userID();
                $data[$key]['createdDateTime'] = current_date(true);
                $data[$key]['timestamp'] = current_date(true);
                $data[$key]['totalEstimatedValue'] = $thisamount;
                $data[$key]['totalSqFt'] = $totalsqftben;
                $data[$key]['totalCost'] = $totalcostben;
                $data[$key]['isQualified'] = 1;
            }
        }
        $result = $this->db->insert_batch('srp_erp_ngo_projectproposalbeneficiaries', $data);
        if ($result) {
            return array('s', 'Beneficiary Added successfully !');
        } else {
            return array('s', 'Beneficiary Insertion Failed');
        }
    }
    function load_project_proposal_donor_details_project()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertdateformat = convert_date_format_sql();

        $proposalID = trim($this->input->post('proposalid'));
        $this->db->select("pro.confirmedYN,pro.approvedYN,pro.isConvertedToProject as isConvertedToProject,ppd.proposalDonourID,ppd.isSubmitted,do.contactID,do.name as name,ppd.proposalID,ppd.donorID,DATE_FORMAT(ppd.submittedDate,'{$convertdateformat}') AS submittedDate,ppd.isApproved,DATE_FORMAT(ppd.approvedDate,'{$convertdateformat}') AS approvedDate,ppd.commitedAmount,pro.transactionCurrencyID,curm.CurrencyCode as CurrencyCode");
        $this->db->from('srp_erp_ngo_projectproposaldonors ppd');
        $this->db->join('srp_erp_ngo_donors do', 'ppd.donorID = do.contactID', 'left');
        $this->db->join('srp_erp_ngo_projectproposals pro', 'ppd.proposalID = pro.proposalID', 'left');
        $this->db->join('srp_erp_currencymaster curm', 'pro.transactionCurrencyID = curm.currencyID', 'left');
        $this->db->where('ppd.companyID', $companyID);
        $this->db->where('ppd.proposalID', $proposalID);
        $this->db->order_by('proposalDonourID', 'desc');
        return $this->db->get()->result_array();
    }
    function update_donors_is_submited_status_project()
    {
        $this->db->trans_start();

        $amount = $this->input->post('addcommitments');
        $donor = $this->input->post('donor');
        $proposal = $this->input->post('proposaldonor');
        foreach ($donor as $key => $val) {
            $data['submittedDate'] = $this->common_data['current_date'];
            $data['commitedAmount'] = $amount[$key];
            $data['isSubmitted'] = 1;
            $data['isApproved'] = 1;
            $data['approvedDate'] = $this->common_data['current_date'];
            $this->db->where('donorID', $val);
            $this->db->where('proposalID', $proposal);
            $this->db->where('companyID', current_companyID());
            $this->db->update('srp_erp_ngo_projectproposaldonors', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Donor Details Update Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Donor Details updated successfully!.');
        }
    }
}