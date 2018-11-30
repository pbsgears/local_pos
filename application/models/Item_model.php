<?php

class Item_model extends ERP_Model
{
    function save_item_master()
    {
        $this->db->trans_start();
        $company_id=current_companyID();
        if (!empty(trim($this->input->post('revanue')) && trim($this->input->post('revanue') != 'Select Revenue GL Account'))) {
            $revanue = explode('|', trim($this->input->post('revanue')));
        }
        $cost = explode('|', trim($this->input->post('cost')));
        $asste = explode('|', trim($this->input->post('asste')));
        $mainCategory = explode('|', trim($this->input->post('mainCategory')));
        $stockadjustment=explode('|', trim($this->input->post('stockadjustment')));
        $isactive = 0;
        if (!empty($this->input->post('isActive'))) {
            $isactive = 1;
        }

        $data['isActive'] = $isactive;
        $data['seconeryItemCode'] = trim($this->input->post('seconeryItemCode'));
        $data['itemName'] = clear_descriprions(trim($this->input->post('itemName')));
        $data['itemDescription'] = clear_descriprions(trim($this->input->post('itemDescription')));
        $data['subcategoryID'] = trim($this->input->post('subcategoryID'));
        $data['subSubCategoryID'] = trim($this->input->post('subSubCategoryID'));
        $data['partNo'] = trim($this->input->post('partno'));
        $data['reorderPoint'] = trim($this->input->post('reorderPoint'));
        $data['maximunQty'] = trim($this->input->post('maximunQty'));
        $data['minimumQty'] = trim($this->input->post('minimumQty'));

        $data['comments'] = trim($this->input->post('comments'));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $data['companyLocalExchangeRate'] = 1;
        $data['companyLocalSellingPrice'] = trim($this->input->post('companyLocalSellingPrice'));
        $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversion($data['companyLocalCurrency'], $data['companyReportingCurrency']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['companyReportingSellingPrice'] = ($data['companyLocalSellingPrice'] / $data['companyReportingExchangeRate']);
        $data['isSubitemExist'] = trim($this->input->post('isSubitemExist'));

        if($this->input->post('revanueGLAutoID')){
            $data['mainCategory'] = trim($mainCategory[1]);
            if ($data['mainCategory'] == 'Fixed Assets') {
                $data['assteGLAutoID'] = trim($this->input->post('assteGLAutoID'));
                $data['faCostGLAutoID'] = trim($this->input->post('COSTGLCODEdes'));
                $data['faACCDEPGLAutoID'] = trim($this->input->post('ACCDEPGLCODEdes'));
                $data['faDEPGLAutoID'] = trim($this->input->post('DEPGLCODEdes'));
                $data['faDISPOGLAutoID'] = trim($this->input->post('DISPOGLCODEdes'));

                $data['costGLAutoID'] = '';
                $data['costSystemGLCode'] = '';
                $data['costGLCode'] = '';
                $data['costDescription'] = '';
                $data['costType'] = '';

                if (!empty($revanue)) {
                    $data['revanueSystemGLCode'] = trim($revanue[0]);
                    $data['revanueGLCode'] = trim($revanue[1]);
                    $data['revanueDescription'] = trim($revanue[2]);
                    $data['revanueType'] = trim($revanue[3]);
                }
                $data['stockAdjustmentGLAutoID'] = trim($this->input->post('stockadjust'));
                $data['stockAdjustmentSystemGLCode'] = trim($stockadjustment[0]);
                $data['stockAdjustmentGLCode'] = trim($stockadjustment[1]);
                $data['stockAdjustmentDescription'] = trim($stockadjustment[2]);
                $data['stockAdjustmentType'] = trim($stockadjustment[3]);

            } elseif ($data['mainCategory'] == 'Service' or $data['mainCategory'] == 'Non Inventory') {
                $data['assteGLAutoID'] = '';
                $data['assteSystemGLCode'] = '';
                $data['assteGLCode'] = '';
                $data['assteDescription'] = '';
                $data['assteType'] = '';
                $data['revanueGLAutoID'] = trim($this->input->post('revanueGLAutoID'));
                if (!empty($revanue)) {
                    $data['revanueSystemGLCode'] = trim($revanue[0]);
                    $data['revanueGLCode'] = trim($revanue[1]);
                    $data['revanueDescription'] = trim($revanue[2]);
                    $data['revanueType'] = trim($revanue[3]);
                }
                $data['costGLAutoID'] = trim($this->input->post('costGLAutoID'));
                $data['costSystemGLCode'] = trim($cost[0]);
                $data['costGLCode'] = trim($cost[1]);
                $data['costDescription'] = trim($cost[2]);
                $data['costType'] = trim($cost[3]);

            } elseif ($data['mainCategory'] == 'Inventory') {
                $data['assteGLAutoID'] = trim($this->input->post('assteGLAutoID'));
                $data['assteSystemGLCode'] = trim($asste[0]);
                $data['assteGLCode'] = trim($asste[1]);
                $data['assteDescription'] = trim($asste[2]);
                $data['assteType'] = trim($asste[3]);
                $data['revanueGLAutoID'] = trim($this->input->post('revanueGLAutoID'));
                if (!empty($revanue)) {
                    $data['revanueSystemGLCode'] = trim($revanue[0]);
                    $data['revanueGLCode'] = trim($revanue[1]);
                    $data['revanueDescription'] = trim($revanue[2]);
                    $data['revanueType'] = trim($revanue[3]);
                }
                $data['stockAdjustmentGLAutoID'] = trim($this->input->post('stockadjust'));
                $data['stockAdjustmentSystemGLCode'] = trim($stockadjustment[0]);
                $data['stockAdjustmentGLCode'] = trim($stockadjustment[1]);
                $data['stockAdjustmentDescription'] = trim($stockadjustment[2]);
                $data['stockAdjustmentType'] = trim($stockadjustment[3]);

            } else {
                $data['assteGLAutoID'] = trim($this->input->post('assteGLAutoID'));
                $data['assteSystemGLCode'] = trim($asste[0]);
                $data['assteGLCode'] = trim($asste[1]);
                $data['assteDescription'] = trim($asste[2]);
                $data['assteType'] = trim($asste[3]);
                $data['revanueGLAutoID'] = trim($this->input->post('revanueGLAutoID'));
                if (!empty($revanue)) {
                    $data['revanueSystemGLCode'] = trim($revanue[0]);
                    $data['revanueGLCode'] = trim($revanue[1]);
                    $data['revanueDescription'] = trim($revanue[2]);
                    $data['revanueType'] = trim($revanue[3]);
                }
                $data['costGLAutoID'] = trim($this->input->post('costGLAutoID'));
                $data['costSystemGLCode'] = trim($cost[0]);
                $data['costGLCode'] = trim($cost[1]);
                $data['costDescription'] = trim($cost[2]);
                $data['costType'] = trim($cost[3]);
            }

        }

        if (trim($this->input->post('itemAutoID'))) {
            $itemauto=$this->input->post('itemAutoID');
            $barcode= $this->input->post('barcode');
            $barcodeexist=$this->db->query("SELECT barcode FROM `srp_erp_itemmaster` WHERE barcode= '$barcode' AND itemAutoID != '$itemauto' ")->row_array();
            if($barcodeexist && !empty($barcode)){
                $this->session->set_flashdata('e', 'Barcode is already exist.');
            }
            else
            {
                $itemAutoID=trim($this->input->post('itemAutoID'));
                $barcode = trim($this->input->post('barcode'));
                $bar=$this->db->query("SELECT * FROM `srp_erp_itemmaster` WHERE itemAutoID=$itemAutoID")->row_array();
                if ($barcode != '') {
                    $data['barcode'] = $barcode;
                } else {
                    $data['barcode'] = $bar['itemSystemCode'];
                }
                $this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
                $this->db->update('srp_erp_itemmaster', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Update Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    //$this->lib_log->log_event('Item','Error','Item : ' .$data['itemSystemCode'].' - '. $data['itemName'] . ' Update Failed '.$this->db->_error_message(),'Item');
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Item : ' . $data['itemName'] . ' Updated Successfully.');
                    $this->db->trans_commit();
                    //$this->lib_log->log_event('Item','Success','Item : ' . $data['companyCode'].' Update Successfully. Affected Rows - ' . $this->db->affected_rows(),'Item');
                    return array('status' => true, 'last_id' => $this->input->post('itemAutoID'),'barcode'=>$data['barcode']);
                }
            }

        } else {
            $barcode= $this->input->post('barcode');
            $barcodeexist=$this->db->query("SELECT barcode FROM `srp_erp_itemmaster` WHERE barcode= '$barcode' ")->row_array();
            if($barcodeexist && !empty($barcode)){
                $this->session->set_flashdata('e', 'Barcode is already exist.');
            }else
            {
                $uom = explode('|', trim($this->input->post('uom')));
                $this->load->library('sequence');
                // $this->db->select('codePrefix');
                // $this->db->where('itemCategoryID', $this->input->post('mainCategoryID'));
                // $code = $this->db->get('srp_erp_itemcategory')->row_array();
                $data['isActive'] = $isactive;
                $data['itemImage'] = 'no-image.png';
                $data['defaultUnitOfMeasureID'] = trim($this->input->post('defaultUnitOfMeasureID'));
                $data['defaultUnitOfMeasure'] = trim($uom[0]);
                $data['mainCategoryID'] = trim($this->input->post('mainCategoryID'));
                $data['mainCategory'] = trim($mainCategory[1]);
                $data['financeCategory'] = $this->finance_category($data['mainCategoryID']);
                $data['assteGLAutoID'] = trim($this->input->post('assteGLAutoID'));
                $data['faCostGLAutoID'] = trim($this->input->post('COSTGLCODEdes'));
                $data['faACCDEPGLAutoID'] = trim($this->input->post('ACCDEPGLCODEdes'));
                $data['faDEPGLAutoID'] = trim($this->input->post('DEPGLCODEdes'));
                $data['faDISPOGLAutoID'] = trim($this->input->post('DISPOGLCODEdes'));

                if ($data['mainCategory'] == 'Fixed Assets') {
                    $data['assteGLAutoID'] = trim($this->input->post('assteGLAutoID'));
                    $data['faCostGLAutoID'] = trim($this->input->post('COSTGLCODEdes'));
                    $data['faACCDEPGLAutoID'] = trim($this->input->post('ACCDEPGLCODEdes'));
                    $data['faDEPGLAutoID'] = trim($this->input->post('DEPGLCODEdes'));
                    $data['faDISPOGLAutoID'] = trim($this->input->post('DISPOGLCODEdes'));

                    $data['costGLAutoID'] = '';
                    $data['costSystemGLCode'] = '';
                    $data['costGLCode'] = '';
                    $data['costDescription'] = '';
                    $data['costType'] = '';
                } elseif ($data['mainCategory'] == 'Service' or $data['mainCategory'] == 'Non Inventory') {
                    $data['assteGLAutoID'] = '';
                    $data['assteSystemGLCode'] = '';
                    $data['assteGLCode'] = '';
                    $data['assteDescription'] = '';
                    $data['assteType'] = '';
                    $data['revanueGLAutoID'] = trim($this->input->post('revanueGLAutoID'));
                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanue[0]);
                        $data['revanueGLCode'] = trim($revanue[1]);
                        $data['revanueDescription'] = trim($revanue[2]);
                        $data['revanueType'] = trim($revanue[3]);
                    }
                    $data['costGLAutoID'] = trim($this->input->post('costGLAutoID'));
                    $data['costSystemGLCode'] = trim($cost[0]);
                    $data['costGLCode'] = trim($cost[1]);
                    $data['costDescription'] = trim($cost[2]);
                    $data['costType'] = trim($cost[3]);
                }

                else {
                    $data['assteGLAutoID'] = trim($this->input->post('assteGLAutoID'));
                    $data['assteSystemGLCode'] = trim($asste[0]);
                    $data['assteGLCode'] = trim($asste[1]);
                    $data['assteDescription'] = trim($asste[2]);
                    $data['assteType'] = trim($asste[3]);
                    $data['revanueGLAutoID'] = trim($this->input->post('revanueGLAutoID'));
                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanue[0]);
                        $data['revanueGLCode'] = trim($revanue[1]);
                        $data['revanueDescription'] = trim($revanue[2]);
                        $data['revanueType'] = trim($revanue[3]);
                    }
                    $data['costGLAutoID'] = trim($this->input->post('costGLAutoID'));
                    $data['costSystemGLCode'] = trim($cost[0]);
                    $data['costGLCode'] = trim($cost[1]);
                    $data['costDescription'] = trim($cost[2]);
                    $data['costType'] = trim($cost[3]);
                }
                $data['companyLocalWacAmount'] = 0.00;
                $data['companyReportingWacAmount'] = 0.00;
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['itemSystemCode'] = $this->sequence->sequence_generator(trim($mainCategory[0]));
//check if itemSystemCode already exist
                $this->db->select('itemSystemCode');
                $this->db->from('srp_erp_itemmaster');
                $this->db->where('itemSystemCode', $data['itemSystemCode']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $codeExist = $this->db->get()->row_array();
                if(!empty($codeExist)){
                    //$this->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo-1)  WHERE documentID='{$mainCategory[0]}' AND companyID = '{$company_id}'");
                    $this->session->set_flashdata('w', 'Item System Code : ' . $codeExist['itemSystemCode'] . ' Already Exist ');
                    $this->db->trans_rollback();
                    return array('status' => false);
                }

                $barcode = trim($this->input->post('barcode'));
                if ($barcode != '') {
                    $data['barcode'] = $barcode;
                } else {
                    $data['barcode'] = $data['itemSystemCode'];
                }
                $this->db->insert('srp_erp_itemmaster', $data);
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Save Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Saved Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $last_id,'barcode'=>$data['barcode']);
                }
            }


        }
    }

    function item_image_upload()
    {
        $this->db->trans_start();
        $output_dir = "uploads/itemMaster/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/itemMaster/", 007);
        }
        $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'Item_' . trim($this->input->post('faID')) . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['itemimage'] = $fileName;

        $this->db->where('itemAutoID', trim($this->input->post('faID')));
        $this->db->update('srp_erp_itemmaster', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', "Image Upload Failed." . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Image uploaded  Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => trim($this->input->post('faID')));
        }
    }

    function finance_category($id)
    {
        $this->db->select('categoryTypeID');
        $this->db->where('itemCategoryID', $id);
        return $this->db->get('srp_erp_itemcategory')->row('categoryTypeID');
    }


    function load_item_header()
    {
        $this->db->select('*');
        $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        return $this->db->get('srp_erp_itemmaster')->row_array();
    }

    function delete_item()
    {
        $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        $this->db->delete('srp_erp_itemmaster');
        $this->session->set_flashdata('s', 'Item Deleted Successfully');
        return true;
    }

    function load_subcat()
    {
        $this->db->select('itemCategoryID,description,masterID');
        $this->db->where('masterID', $this->input->post('subid'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_itemcategory');
        return $subcat = $this->db->get()->result_array();
    }

    function load_subsubcat()
    {
        $this->db->select('itemCategoryID,description,masterID');
        $this->db->where('masterID', $this->input->post('subsubid'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_itemcategory');
        return $subsubcat = $this->db->get()->result_array();
    }

    function edit_item()
    {
        $this->db->select('*');
        $this->db->where('itemAutoID', $this->input->post('id'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        return $this->db->get('srp_erp_itemmaster')->row_array();
    }


    function item_master_img_uplode()
    {
        $output_dir = "uploads/itemmaster/";
        $itemAutoID = trim($this->input->post('image_itemmaster_hn'));
        $attachment_file = $_FILES["img_file"];
        $info = new SplFileInfo($_FILES["img_file"]["name"]);
        $fileName = 'ITM' . "_" . $itemAutoID . '.' . $info->getExtension();
        move_uploaded_file($_FILES["img_file"]["tmp_name"], $output_dir . $fileName);
        $this->db->where('itemAutoID', $itemAutoID);
        $result = $this->db->update('srp_erp_itemmaster', array('itemImage' => $output_dir . $fileName));
        if ($result) {
            $this->session->set_flashdata('s', 'Image Uploaded Successfully');
            return true;
        }
    }

    function img_uplode()
    {
        $output_dir = "images/item/";
        $attachment_file = ($_FILES["img_file"] ? $_FILES["img_file"] : 'no-image.png');
        $info = new SplFileInfo($_FILES["img_file"]["name"]);
        $fileName = trim($this->input->post('item_id')) . '.' . $info->getExtension();
        move_uploaded_file($_FILES["img_file"]["tmp_name"], $output_dir . $fileName);
        $this->db->where('itemAutoID', trim($this->input->post('item_id')));
        $this->db->update('srp_erp_itemmaster', array('itemImage' => $fileName));
        return array('status' => true);
    }

    function load_gl_codes()
    {
        $this->db->select('revenueGL,costGL,assetGL,faCostGLAutoID,faACCDEPGLAutoID,faDEPGLAutoID,faDISPOGLAutoID,stockAdjustmentGL');
        $this->db->where('itemCategoryID', $this->input->post('itemCategoryID'));
        return $this->db->get('srp_erp_itemcategory')->row_array();
    }

    function changeitemactive()
    {

        $data['isActive'] = ($this->input->post('chkedvalue'));
        $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        $result = $this->db->update('srp_erp_itemmaster', $data);
        if ($result) {
            $this->session->set_flashdata('s', 'Records Updated Successfully');
            return true;
        }
    }

    function load_category_type_id()
    {
        $this->db->select('itemCategoryID,categoryTypeID');
        $this->db->where('itemCategoryID', $this->input->post('itemCategoryID'));
        return $this->db->get('srp_erp_itemcategory')->row_array();
    }

    function load_unitprice_exchangerate()
    {

        $localwacAmount = trim($this->input->post('LocalWacAmount'));
        $this->db->select('purchaseOrderID,transactionCurrencyID,transactionExchangeRate,transactionCurrency,companyLocalCurrency,transactionCurrencyDecimalPlaces');
        $this->db->where('purchaseOrderID', $this->input->post('poID'));
        $result = $this->db->get('srp_erp_purchaseordermaster')->row_array();
        $localCurrency = currency_conversion($result['companyLocalCurrency'], $result['transactionCurrency']);
        $unitprice = round(($localwacAmount / $localCurrency['conversion']), $result['transactionCurrencyDecimalPlaces']);

        return array('status' => true, 'amount' => $unitprice);
    }

    function fetch_sales_price()
    {
        $unitOfMeasureID = trim($this->input->post('unitOfMeasureID'));

        $this->db->select('*');
        $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $itemDetail = $this->db->get('srp_erp_itemmaster')->row_array();

        $defaultUOM = $itemDetail["defaultUnitOfMeasureID"];//default unit of measure

        //$conversionUOM = conversionRateUOM_id($unitOfMeasureID,$defaultUOM);

        $this->db->select('transactionCurrencyID,transactionExchangeRate,transactionCurrency,companyLocalCurrency,transactionCurrencyDecimalPlaces,companyLocalExchangeRate');
        $this->db->where($this->input->post('primaryKey'), $this->input->post('id'));
        $result = $this->db->get($this->input->post('tableName'))->row_array();

        //$localCurrency = currency_conversion($result['companyLocalCurrency'] ,$result['transactionCurrency']);
        $localCurrencyER = 1 / $result['companyLocalExchangeRate'];

        $salesprice = trim($this->input->post('salesprice'));
        /* echo $this->input->post('salesprice')."<br>";
         echo $localCurrencyER;*/

        $unitprice = round(($salesprice / $localCurrencyER), $result['transactionCurrencyDecimalPlaces']);

        return array('status' => true, 'amount' => $unitprice);
    }

    function load_sub_itemMaster_view($itemCode){
        $this->db->select('itemmaster_sub.*, wh.wareHouseDescription as warehouseDescription', false);
        $this->db->join('srp_erp_itemmaster itemmaster', 'itemmaster.itemAutoID = itemmaster_sub.itemAutoID', 'left');
        $this->db->join('srp_erp_warehousemaster wh', 'wh.wareHouseAutoID = itemmaster_sub.wareHouseAutoID', 'left');
        $this->db->where('itemmaster.itemAutoID', $itemCode);
        $this->db->where("(itemmaster_sub.isSold <> 1 OR itemmaster_sub.isSold IS NULL)");
        $r = $this->db->get('srp_erp_itemmaster_sub itemmaster_sub')->result_array();
        return $r;
    }

    function save_item_percentage(){
        $updateArray = array();
        for($x = 0; $x < sizeof($this->input->post("itemAutoID")); $x++){
            $updateArray[] = array(
                'itemAutoID'=>$this->input->post("itemAutoID")[$x],
                'finCompanyPercentage' => $this->input->post("finCompanyPercentage")[$x],
                'pvtCompanyPercentage' => $this->input->post("pvtCompanyPercentage")[$x],
            );
        }
        $this->db->trans_start();
        $this->db->update_batch('srp_erp_itemmaster', $updateArray, 'itemAutoID');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e',"Percentage Update Failed");
        } else {
            $this->db->trans_commit();
            return array('s',"Percentage Updated Successfully");
        }
    }

    function save_item_bin_location(){
        $binLocationID=$this->input->post('binLocationID');
        $itemBinlocationID=$this->input->post('itemBinlocationID');
        $itemAutoID=$this->input->post('itemAutoID');
        $wareHouseAutoID=$this->input->post('wareHouseAutoID');
        if($itemBinlocationID){
            if(!empty($binLocationID)){
                $data['binLocationID'] = $binLocationID;
                $this->db->where('itemBinlocationID', $itemBinlocationID);
                $results = $this->db->update('srp_erp_itembinlocation', $data);
                if ($results) {
                    return array('s', 'successfully updated',$itemBinlocationID);
                }
            }else{
                $this->db->where('itemBinlocationID', $itemBinlocationID);
                $results =$this->db->delete('srp_erp_itembinlocation');
                if ($results) {
                    return array('s', 'Successfully deleted');
                }
            }
        }else{
            $this->db->set('itemAutoID', ($itemAutoID));
            $this->db->set('wareHouseAutoID', ($wareHouseAutoID));
            $this->db->set('binLocationID', ($binLocationID));
            $this->db->set('companyID', (current_companyID()));
            $this->db->set('createdUserGroup', ($this->common_data['user_group']));
            $this->db->set('createdPCID', ($this->common_data['current_pc']));
            $this->db->set('createdUserID', ($this->common_data['current_userID']));
            $this->db->set('createdDateTime', ($this->common_data['current_date']));
            $this->db->set('createdUserName', ($this->common_data['current_user']));
            $result = $this->db->insert('srp_erp_itembinlocation');
            $last_id = $this->db->insert_id();
            if ($result) {
                return array('s','successfully Saved',$last_id);
            }
        }
    }

    function load_item_bin_location(){
        $this->db->select('*');
        $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        $this->db->where('companyID', current_companyID());
        return $this->db->get('srp_erp_itembinlocation')->result_array();
    }

    function saveMultipleItemMaster()
    {
        $mainCategoryID = $this->input->post('mainCategoryID');
        $mainCategoryIDselect = $this->input->post('mainCategoryIDselect');
        $mainCategory = $this->input->post('mainCategory');
        $revanue = $this->input->post('revanue');
        $costpost = $this->input->post('cost');
        $asstepost = $this->input->post('asste');
        $stockadjustment = $this->input->post('stockadjustment');
        $seconeryItemCode = $this->input->post('seconeryItemCode');
        $itemName = $this->input->post('itemName');
        $itemDescription = $this->input->post('itemDescription');
        $subcategoryID = $this->input->post('subcategoryID');
        $subSubCategoryID = $this->input->post('subSubCategoryID');
        $partno = $this->input->post('partno');
        $reorderPoint = $this->input->post('reorderPoint');
        $maximunQty = $this->input->post('maximunQty');
        $minimumQty = $this->input->post('minimumQty');
        $comments = $this->input->post('comments');
        $companyLocalSellingPrice = $this->input->post('companyLocalSellingPrice');
        $revanueGLAutoID = $this->input->post('revanueGLAutoID');
        $assteGLAutoID = $this->input->post('assteGLAutoID');
        $COSTGLCODEdes = $this->input->post('COSTGLCODEdes');
        $ACCDEPGLCODEdes = $this->input->post('ACCDEPGLCODEdes');
        $DEPGLCODEdes = $this->input->post('DEPGLCODEdes');
        $DISPOGLCODEdes = $this->input->post('DISPOGLCODEdes');
        $stockadjust = $this->input->post('stockadjust');
        $costGLAutoID = $this->input->post('costGLAutoID');
        $barcode = $this->input->post('barcode');
        $uom = $this->input->post('uom');
        $defaultUnitOfMeasureID = $this->input->post('defaultUnitOfMeasureID');
        $itemAutoIDhn = $this->input->post('itemAutoIDhn');

        foreach($itemAutoIDhn as $key => $mainCateg) {
            $data='';
            $company_id = current_companyID();
            if (!empty(trim($revanue[$key]) && trim($revanue[$key] != 'Select Revenue GL Account'))) {
                $revanueex = explode('|', trim($revanue[$key]));
            }
            $cost = explode('|', trim($costpost[$key]));
            $asste = explode('|', trim($asstepost[$key]));
            $mainCategoryex = explode('|', trim($mainCategory[$key]));
            $stockadjustmentex = explode('|', trim($stockadjustment[$key]));
            $isactive = 1;

            $data['isActive'] = $isactive;
            $data['seconeryItemCode'] = trim($seconeryItemCode[$key]);
            $data['itemName'] = clear_descriprions(trim($itemName[$key]));
            $data['itemDescription'] = clear_descriprions(trim($itemDescription[$key]));
            $data['subcategoryID'] = trim($subcategoryID[$key]);
            $data['subSubCategoryID'] = trim($subSubCategoryID[$key]);
            $data['partNo'] = trim($partno[$key]);
            $data['reorderPoint'] = trim($reorderPoint[$key]);
            $data['maximunQty'] = trim($maximunQty[$key]);
            $data['minimumQty'] = trim($minimumQty[$key]);

            $data['comments'] = trim($comments[$key]);
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data['companyLocalExchangeRate'] = 1;
            $data['companyLocalSellingPrice'] = trim($companyLocalSellingPrice[$key]);
            $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $reporting_currency = currency_conversion($data['companyLocalCurrency'], $data['companyReportingCurrency']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $data['companyReportingSellingPrice'] = ($data['companyLocalSellingPrice'] / $data['companyReportingExchangeRate']);
            //$data['isSubitemExist'] = trim($this->input->post('isSubitemExist'));

            if ($revanueGLAutoID[$key]) {
                $data['mainCategory'] = trim($mainCategoryex[1]);
                if ($data['mainCategory'] == 'Fixed Assets') {
                    $data['assteGLAutoID'] = trim($assteGLAutoID[$key]);
                    $data['faCostGLAutoID'] = trim($COSTGLCODEdes[$key]);
                    $data['faACCDEPGLAutoID'] = trim($ACCDEPGLCODEdes[$key]);
                    $data['faDEPGLAutoID'] = trim($DEPGLCODEdes[$key]);
                    $data['faDISPOGLAutoID'] = trim($DISPOGLCODEdes[$key]);

                    $data['costGLAutoID'] = '';
                    $data['costSystemGLCode'] = '';
                    $data['costGLCode'] = '';
                    $data['costDescription'] = '';
                    $data['costType'] = '';

                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanueex[0]);
                        $data['revanueGLCode'] = trim($revanueex[1]);
                        $data['revanueDescription'] = trim($revanueex[2]);
                        $data['revanueType'] = trim($revanueex[3]);
                    }
                    $data['stockAdjustmentGLAutoID'] = trim($stockadjust[$key]);
                    $data['stockAdjustmentSystemGLCode'] = trim($stockadjustmentex[0]);
                    $data['stockAdjustmentGLCode'] = trim($stockadjustmentex[1]);
                    $data['stockAdjustmentDescription'] = trim($stockadjustmentex[2]);
                    $data['stockAdjustmentType'] = trim($stockadjustmentex[3]);

                } elseif ($data['mainCategory'] == 'Service' or $data['mainCategory'] == 'Non Inventory') {
                    $data['assteGLAutoID'] = '';
                    $data['assteSystemGLCode'] = '';
                    $data['assteGLCode'] = '';
                    $data['assteDescription'] = '';
                    $data['assteType'] = '';
                    $data['revanueGLAutoID'] = trim($revanueGLAutoID[$key]);
                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanueex[0]);
                        $data['revanueGLCode'] = trim($revanueex[1]);
                        $data['revanueDescription'] = trim($revanueex[2]);
                        $data['revanueType'] = trim($revanueex[3]);
                    }
                    $data['costGLAutoID'] = trim($costGLAutoID[$key]);
                    $data['costSystemGLCode'] = trim($cost[0]);
                    $data['costGLCode'] = trim($cost[1]);
                    $data['costDescription'] = trim($cost[2]);
                    $data['costType'] = trim($cost[3]);

                } elseif ($data['mainCategory'] == 'Inventory') {
                    $data['assteGLAutoID'] = trim($assteGLAutoID[$key]);
                    $data['assteSystemGLCode'] = trim($asste[0]);
                    $data['assteGLCode'] = trim($asste[1]);
                    $data['assteDescription'] = trim($asste[2]);
                    $data['assteType'] = trim($asste[3]);
                    $data['revanueGLAutoID'] = trim($revanueGLAutoID[$key]);
                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanueex[0]);
                        $data['revanueGLCode'] = trim($revanueex[1]);
                        $data['revanueDescription'] = trim($revanueex[2]);
                        $data['revanueType'] = trim($revanueex[3]);
                    }
                    $data['stockAdjustmentGLAutoID'] = trim($stockadjust[$key]);
                    $data['stockAdjustmentSystemGLCode'] = trim($stockadjustmentex[0]);
                    $data['stockAdjustmentGLCode'] = trim($stockadjustmentex[1]);
                    $data['stockAdjustmentDescription'] = trim($stockadjustmentex[2]);
                    $data['stockAdjustmentType'] = trim($stockadjustmentex[3]);

                } else {
                    $data['assteGLAutoID'] = trim($assteGLAutoID[$key]);
                    $data['assteSystemGLCode'] = trim($asste[0]);
                    $data['assteGLCode'] = trim($asste[1]);
                    $data['assteDescription'] = trim($asste[2]);
                    $data['assteType'] = trim($asste[3]);
                    $data['revanueGLAutoID'] = trim($revanueGLAutoID[$key]);
                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanueex[0]);
                        $data['revanueGLCode'] = trim($revanueex[1]);
                        $data['revanueDescription'] = trim($revanueex[2]);
                        $data['revanueType'] = trim($revanueex[3]);
                    }
                    $data['costGLAutoID'] = trim($costGLAutoID[$key]);
                    $data['costSystemGLCode'] = trim($cost[0]);
                    $data['costGLCode'] = trim($cost[1]);
                    $data['costDescription'] = trim($cost[2]);
                    $data['costType'] = trim($cost[3]);
                }

            }
            if(!empty($barcode[$key])){
                $barcode = $barcode[$key];
            }else{
                $barcode='';
            }


            $barcodeexist = $this->db->query("SELECT barcode FROM `srp_erp_itemmaster` WHERE barcode= '$barcode' ")->row_array();
            if ($barcodeexist && !empty($barcode)) {
                return array('e', 'Barcode is already exist. ');
            } else {
                $uoms = explode('|', trim($uom[$key]));
                $this->load->library('sequence');
                $data['isActive'] = $isactive;
                $data['itemImage'] = 'no-image.png';
                $data['defaultUnitOfMeasureID'] = trim($defaultUnitOfMeasureID[$key]);
                $data['defaultUnitOfMeasure'] = trim($uoms[0]);
                $data['mainCategoryID'] = trim($mainCategoryIDselect);
                $data['mainCategory'] = trim($mainCategoryex[1]);
                $data['financeCategory'] = $this->finance_category($data['mainCategoryID']);
                $data['assteGLAutoID'] = trim($assteGLAutoID[$key]);
                $data['faCostGLAutoID'] = trim($COSTGLCODEdes[$key]);
                $data['faACCDEPGLAutoID'] = trim($ACCDEPGLCODEdes[$key]);
                $data['faDEPGLAutoID'] = trim($DEPGLCODEdes[$key]);
                $data['faDISPOGLAutoID'] = trim($DISPOGLCODEdes[$key]);

                if ($data['mainCategory'] == 'Fixed Assets') {
                    $data['assteGLAutoID'] = trim($assteGLAutoID[$key]);
                    $data['faCostGLAutoID'] = trim($COSTGLCODEdes[$key]);
                    $data['faACCDEPGLAutoID'] = trim($ACCDEPGLCODEdes[$key]);
                    $data['faDEPGLAutoID'] = trim($DEPGLCODEdes[$key]);
                    $data['faDISPOGLAutoID'] = trim($DISPOGLCODEdes[$key]);

                    $data['costGLAutoID'] = '';
                    $data['costSystemGLCode'] = '';
                    $data['costGLCode'] = '';
                    $data['costDescription'] = '';
                    $data['costType'] = '';
                } elseif ($data['mainCategory'] == 'Service' or $data['mainCategory'] == 'Non Inventory') {
                    $data['assteGLAutoID'] = '';
                    $data['assteSystemGLCode'] = '';
                    $data['assteGLCode'] = '';
                    $data['assteDescription'] = '';
                    $data['assteType'] = '';
                    $data['revanueGLAutoID'] = trim($revanueGLAutoID[$key]);
                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanueex[0]);
                        $data['revanueGLCode'] = trim($revanueex[1]);
                        $data['revanueDescription'] = trim($revanueex[2]);
                        $data['revanueType'] = trim($revanueex[3]);
                    }
                    $data['costGLAutoID'] = trim($costGLAutoID[$key]);
                    $data['costSystemGLCode'] = trim($cost[0]);
                    $data['costGLCode'] = trim($cost[1]);
                    $data['costDescription'] = trim($cost[2]);
                    $data['costType'] = trim($cost[3]);
                } else {
                    $data['assteGLAutoID'] = trim($assteGLAutoID[$key]);
                    $data['assteSystemGLCode'] = trim($asste[0]);
                    $data['assteGLCode'] = trim($asste[1]);
                    $data['assteDescription'] = trim($asste[2]);
                    $data['assteType'] = trim($asste[3]);
                    $data['revanueGLAutoID'] = trim($revanueGLAutoID[$key]);
                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanueex[0]);
                        $data['revanueGLCode'] = trim($revanueex[1]);
                        $data['revanueDescription'] = trim($revanueex[2]);
                        $data['revanueType'] = trim($revanueex[3]);
                    }
                    $data['costGLAutoID'] = trim($costGLAutoID[$key]);
                    $data['costSystemGLCode'] = trim($cost[0]);
                    $data['costGLCode'] = trim($cost[1]);
                    $data['costDescription'] = trim($cost[2]);
                    $data['costType'] = trim($cost[3]);
                }
                $data['companyLocalWacAmount'] = 0.00;
                $data['companyReportingWacAmount'] = 0.00;
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['itemSystemCode'] = $this->sequence->sequence_generator(trim($mainCategoryex[0]));
    //check if itemSystemCode already exist
                $this->db->select('itemSystemCode');
                $this->db->from('srp_erp_itemmaster');
                $this->db->where('itemSystemCode', $data['itemSystemCode']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $codeExist = $this->db->get()->row_array();
                if (!empty($codeExist)) {
                    return array('w', 'Item System Code : ' . $codeExist['itemSystemCode'] . ' Already Exist ');
                }

                if(!empty($barcode[$key])){
                    $barcode = $barcode[$key];
                }else{
                    $barcode='';
                }
                if ($barcode != '') {
                    $data['barcode'] = $barcode;
                } else {
                    $data['barcode'] = $data['itemSystemCode'];
                }
            }


            $result=$this->db->insert('srp_erp_itemmaster', $data);
            $this->db->delete('srp_erp_itemmaster_temp', array('itemAutoID' => $itemAutoIDhn[$key]));
        }


        if($result){
            return array('s', 'Records Successfully Saved');
        }else{
            return array('e', 'Upload failed');
        }




    }

}
