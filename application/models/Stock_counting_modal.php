<?php

class Stock_counting_modal extends ERP_Model
{
    function save_stock_counting_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $stkAdjmntDate = $this->input->post('stockCountingDate');
        $stockCountingDate = input_format_date($stkAdjmntDate, $date_format_policy);

        $segment = explode('|', trim($this->input->post('segment')));
        $location = explode('|', trim($this->input->post('location_dec')));
        //$period = explode('|', trim($this->input->post('financeyear_period')));
        $year = explode(' - ', trim($this->input->post('companyFinanceYear')));

        $FYBegin = input_format_date($year[0], $date_format_policy);
        $FYEnd = input_format_date($year[1], $date_format_policy);

        $data['documentID'] = 'SCNT';
        $data['stockCountingDate'] = trim($stockCountingDate);
        $data['referenceNo'] = trim($this->input->post('referenceNo'));
        $data['comment'] = trim($this->input->post('narration'));
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear'));
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear'));
        $data['stockCountingType'] = trim($this->input->post('stockCountingType'));
        $data['adjustmentType'] = trim($this->input->post('adjustmentType'));
        $data['wareHouseAutoID'] = trim($this->input->post('location'));
        $data['wareHouseCode'] = trim($location[0]);
        $data['wareHouseLocation'] = trim($location[1]);
        $data['wareHouseDescription'] = trim($location[2]);
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period'));
        /*$data['FYPeriodDateFrom'] = trim($period[0]);
        $data['FYPeriodDateTo'] = trim($period[1]);*/
        $data['segmentID'] = trim($segment[0]);
        $data['segmentCode'] = trim($segment[1]);
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('stockCountingAutoID'))) {
            $this->db->where('stockCountingAutoID', trim($this->input->post('stockCountingAutoID')));
            $this->db->update('srp_erp_stockcountingmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Stock Counting : ' . $data['wareHouseDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Stock Counting : ' . $data['wareHouseDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('stockCountingAutoID'));
            }
        } else {
            $this->load->library('sequence');
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data['companyLocalExchangeRate'] = 1;
            $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $reporting_currency = currency_conversionID($data['companyLocalCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['stockCountingCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_stockcountingmaster', $data);
            $last_id = $this->db->insert_id();

            $this->db->select('*');
            $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
            $this->db->where('companyID', current_companyID());
            $items = $this->db->get('srp_erp_warehouseitems')->result_array();
            foreach ($items as $detail) {

                $item_data = fetch_item_data($detail['itemAutoID']);
                if ($item_data['mainCategory'] == $data['stockCountingType']) {
                    $dtl['stockCountingAutoID'] = $last_id;
                    $dtl['itemAutoID'] = $detail['itemAutoID'];
                    $dtl['itemSystemCode'] = $detail['itemSystemCode'];
                    $dtl['itemDescription'] = $detail['itemDescription'];
                    $dtl['itemFinanceCategory'] = $item_data['subcategoryID'];
                    $dtl['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
                    $dtl['financeCategory'] = $item_data['financeCategory'];
                    $dtl['itemCategory'] = $item_data['mainCategory'];
                    $dtl['unitOfMeasure'] = $item_data['defaultUnitOfMeasure'];
                    $dtl['unitOfMeasureID'] = $item_data['defaultUnitOfMeasureID'];
                    $dtl['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
                    $dtl['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
                    $dtl['conversionRateUOM'] = conversionRateUOM_id($dtl['unitOfMeasureID'], $dtl['defaultUOMID']);
                    $dtl['segmentID'] = trim($segment[0]);
                    $dtl['segmentCode'] = trim($segment[1]);
                    if($data['adjustmentType']==0){
                        $dtl['previousStock'] = isset($item_data['currentStock']) && !empty($item_data['currentStock']) ? $item_data['currentStock'] : 0;
                        $dtl['previousWac'] = isset($item_data['companyLocalWacAmount']) && !empty($item_data['companyLocalWacAmount']) ? $item_data['companyLocalWacAmount'] : 0;
                        $dtl['previousWareHouseStock'] = isset($detail['currentStock']) && !empty($detail['currentStock']) ? $detail['currentStock'] : 0;
                        $dtl['currentWac'] = isset($item_data['companyLocalWacAmount']) && !empty($item_data['companyLocalWacAmount']) ? $item_data['companyLocalWacAmount'] : 0;
                        $dtl['currentWareHouseStock'] = 0;
                        $dtl['currentStock'] = 0;
                        $dtl['adjustmentWac'] = 0;
                        $dtl['adjustmentWareHouseStock'] = 0;
                        $dtl['adjustmentStock'] = 0;
                    }else{
                        $dtl['previousStock'] = isset($item_data['currentStock']) && !empty($item_data['currentStock']) ? $item_data['currentStock'] : 0;
                        $dtl['previousWac'] = isset($item_data['companyLocalWacAmount']) && !empty($item_data['companyLocalWacAmount']) ? $item_data['companyLocalWacAmount'] : 0;
                        $dtl['previousWareHouseStock'] = isset($detail['currentStock']) && !empty($detail['currentStock']) ? $detail['currentStock'] : 0;
                        $dtl['currentWac'] =  0;
                        $dtl['currentWareHouseStock'] = isset($detail['currentStock']) && !empty($detail['currentStock']) ? $detail['currentStock'] : 0;
                        $dtl['currentStock'] = isset($item_data['currentStock']) && !empty($item_data['currentStock']) ? $item_data['currentStock'] : 0;
                        $dtl['adjustmentWac'] = 0;
                        $dtl['adjustmentWareHouseStock'] = 0;
                        $dtl['adjustmentStock'] = 0;
                    }

                    if ($dtl['financeCategory'] == 1 or $dtl['financeCategory'] == 3) {
                        $dtl['PLGLAutoID'] = $item_data['stockAdjustmentGLAutoID'];
                        $dtl['PLSystemGLCode'] = $item_data['stockAdjustmentSystemGLCode'];
                        $dtl['PLGLCode'] = $item_data['stockAdjustmentGLCode'];
                        $dtl['PLDescription'] = $item_data['stockAdjustmentDescription'];
                        $dtl['PLType'] = $item_data['stockAdjustmentType'];
                        $dtl['BLGLAutoID'] = $item_data['assteGLAutoID'];
                        $dtl['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                        $dtl['BLGLCode'] = $item_data['assteGLCode'];
                        $dtl['BLDescription'] = $item_data['assteDescription'];
                        $dtl['BLType'] = $item_data['assteType'];
                    } elseif ($dtl['financeCategory'] == 2) {
                        $dtl['PLGLAutoID'] = $item_data['stockAdjustmentGLAutoID'];
                        $dtl['PLSystemGLCode'] = $item_data['stockAdjustmentSystemGLCode'];
                        $dtl['PLGLCode'] = $item_data['stockAdjustmentGLCode'];
                        $dtl['PLDescription'] = $item_data['stockAdjustmentDescription'];
                        $dtl['PLType'] = $item_data['stockAdjustmentType'];
                        $dtl['BLGLAutoID'] = '';
                        $dtl['BLSystemGLCode'] = '';
                        $dtl['BLGLCode'] = '';
                        $dtl['BLDescription'] = '';
                        $dtl['BLType'] = '';
                    }
                    $this->db->insert('srp_erp_stockcountingdetails', $dtl);
                }

            }


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Stock Counting : ' . $data['wareHouseDescription'] . '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Stock Counting : ' . $data['wareHouseDescription'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function laad_stock_counting_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(stockCountingDate,\'' . $convertFormat . '\') AS stockCountingDate');
        $this->db->where('stockCountingAutoID', $this->input->post('stockCountingAutoID'));
        return $this->db->get('srp_erp_stockcountingmaster')->row_array();
    }

    function fetch_stock_counting_detail()
    {
        $subcategoryID = $this->input->post('subcategoryID');
        $subcategory_filter = '';
        $subsubcategory_filter = '';
        if (!empty($subcategoryID)) {
            ;
            $whereIN = "( " . join(",", $subcategoryID) . " )";
            $subcategory_filter = " AND itemFinanceCategory IN " . $whereIN;
        }

        $subsubcategoryID = $this->input->post('subsubcategoryID');
        if (!empty($subsubcategoryID)) {
            ;
            $where = "( " . join(",", $subsubcategoryID) . " )";
            $subsubcategory_filter = " AND itemFinanceCategorySub IN " . $where;
        }
        $where = "srp_erp_stockcountingdetails.stockCountingAutoID = " . $this->input->post('stockCountingAutoID') . $subcategory_filter . $subsubcategory_filter . "";
        $this->db->select('PLGLAutoID,BLGLAutoID,srp_erp_stockcountingdetails.itemSystemCode, srp_erp_stockcountingdetails.itemDescription,srp_erp_stockcountingdetails.unitOfMeasure,srp_erp_stockcountingdetails.previousWareHouseStock,srp_erp_stockcountingdetails.previousWac,srp_erp_stockcountingmaster.companyLocalCurrencyDecimalPlaces,srp_erp_stockcountingdetails.currentWareHouseStock,srp_erp_stockcountingdetails.currentWac,srp_erp_stockcountingdetails.adjustmentWareHouseStock,srp_erp_stockcountingdetails.adjustmentWac,srp_erp_stockcountingdetails.totalValue,srp_erp_stockcountingdetails.stockCountingDetailsAutoID,srp_erp_stockcountingdetails.previousStock, srp_erp_stockcountingdetails.currentStock, srp_erp_stockcountingmaster.wareHouseAutoID, srp_erp_itemmaster.isSubitemExist,srp_erp_stockcountingdetails.isUpdated,srp_erp_stockcountingdetails.itemAutoID');
        $this->db->where($where);
        $this->db->join('srp_erp_stockcountingmaster', 'srp_erp_stockcountingdetails.stockCountingAutoID = srp_erp_stockcountingmaster.stockCountingAutoID');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID= srp_erp_stockcountingdetails.itemAutoID', 'left');
        return $this->db->get('srp_erp_stockcountingdetails')->result_array();
    }

    function fetch_warehouse_item_adjustment()
    {
        $this->db->select('wareHouseAutoID,wareHouseCode, wareHouseLocation, wareHouseDescription');
        $this->db->from('srp_erp_stockcountingmaster');
        $this->db->where('stockCountingAutoID', trim($this->input->post('stockCountingAutoID')));
        $query = $this->db->get()->row_array();

        $this->db->select('currentStock');
        $this->db->from('srp_erp_warehouseitems');
        $this->db->where('wareHouseAutoID', $query['wareHouseAutoID']);
        $this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $currentStock = $this->db->get()->row('currentStock');

        $this->db->select('companyLocalWacAmount');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $currentWac = $this->db->get()->row('companyLocalWacAmount');

        if (!empty($currentStock)) {
            return array('status' => true, 'currentStock' => $currentStock, 'currentWac' => $currentWac);
        } else {
            return array('status' => true, 'currentStock' => 0, 'currentWac' => $currentWac);
            //$this->session->set_flashdata('w', 'The item you entered is not exists in this warehouse ' . $query['wareHouseDescription'] . ' ( ' . $query['wareHouseLocation'] . ' ) . you can not issue this item from this warehouse.');
            return array('status' => false);
        }
    }


    function save_stock_counting_detail_multiple()
    {
        $stockCountingDetailsAutoID = $this->input->post('stockCountingDetailsAutoID');
        $stockCountingAutoID = $this->input->post('stockCountingAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $currentWareHouseStock = $this->input->post('currentWareHouseStock');
        $currentWac = $this->input->post('currentWac');
        $projectID = $this->input->post('projectID');
        $adjustment_Stock = $this->input->post('adjustment_Stock');
        $adjustment_wac = $this->input->post('adjustment_wac');
        $a_segment = $this->input->post('a_segment');

        $projectExist = project_is_exist();
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];

        $this->db->select('wareHouseAutoID, wareHouseCode, wareHouseLocation, wareHouseDescription');
        $this->db->where('stockCountingAutoID', $this->input->post('stockCountingAutoID'));
        $stockadjustmentMaster = $this->db->get('srp_erp_stockcountingmaster')->row_array();


        $this->db->trans_start();
        foreach ($itemAutoIDs as $key => $itemAutoID) {

            if (!$stockCountingDetailsAutoID) {
                $this->db->select('stockCountingDetailsAutoID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_stockcountingdetails');
                $this->db->where('stockCountingAutoID', $stockCountingAutoID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('error' => 1, 'w', 'Stock Counting Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $segment = explode('|', $a_segment[$key]);
            $uomEx = explode('|', $uom[$key]);
            $item_data = fetch_item_data($itemAutoID);

            $previousWareHouseStock = $this->db->query("SELECT currentStock FROM srp_erp_warehouseitems WHERE wareHouseAutoID='{$stockadjustmentMaster['wareHouseAutoID']}' and itemAutoID='{$itemAutoID}'")->row_array(); //get warehouse stock of the item by location

            $data['stockCountingAutoID'] = $stockCountingAutoID;
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasure'] = trim($uomEx[0]);
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['segmentID'] = trim($segment[0]);
            $data['isUpdated'] = 1;
            $data['segmentCode'] = trim($segment[1]);
            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
            $data['financeCategory'] = $item_data['financeCategory'];
            $data['itemCategory'] = $item_data['mainCategory'];

            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                $data['PLGLAutoID'] = $item_data['stockAdjustmentGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['stockAdjustmentSystemGLCode'];
                $data['PLGLCode'] = $item_data['stockAdjustmentGLCode'];
                $data['PLDescription'] = $item_data['stockAdjustmentDescription'];
                $data['PLType'] = $item_data['stockAdjustmentType'];
                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data['BLGLCode'] = $item_data['assteGLCode'];
                $data['BLDescription'] = $item_data['assteDescription'];
                $data['BLType'] = $item_data['assteType'];
            } elseif ($data['financeCategory'] == 2) {
                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                $data['PLGLCode'] = $item_data['costGLCode'];
                $data['PLDescription'] = $item_data['costDescription'];
                $data['PLType'] = $item_data['costType'];
                $data['BLGLAutoID'] = '';
                $data['BLSystemGLCode'] = '';
                $data['BLGLCode'] = '';
                $data['BLDescription'] = '';
                $data['BLType'] = '';
            }
            $data['previousStock'] = isset($currentWareHouseStock[$key]) && !empty($currentWareHouseStock[$key]) ? $currentWareHouseStock[$key] : 0;
            $data['previousWac'] = $currentWac[$key];
            $data['previousWareHouseStock'] = isset($previousWareHouseStock["currentStock"]) && !empty($previousWareHouseStock["currentStock"]) ? $previousWareHouseStock["currentStock"] : 0;
            $data['currentWac'] = $adjustment_wac[$key];
            $data['currentWareHouseStock'] = $adjustment_Stock[$key];
            $data['currentStock'] = $adjustment_Stock[$key];
            $data['adjustmentWac'] = ($adjustment_wac[$key] - $currentWac[$key]);
            $data['adjustmentWareHouseStock'] = ($adjustment_Stock[$key] - $currentWareHouseStock[$key]);
            $data['adjustmentStock'] = ($adjustment_Stock[$key] - $currentWareHouseStock[$key]);
            //print_r($data);

            $previousTotal = ($data['previousStock'] * $data['previousWac']);
            $newTotal = ($data['currentStock'] * $data['currentWac']);

            $this->db->select('currentStock,companyLocalWacAmount,(currentStock * companyLocalWacAmount) as prevItemMasterTotal');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $prevItemMasterTotal = $this->db->get()->row_array();

            /*$prevItemMasterStock = $prevItemMasterTotal["currentStock"];
            $prevItemMasterWac = $prevItemMasterTotal["companyLocalWacAmount"];
            $prevItemMasterTotal = $prevItemMasterTotal["prevItemMasterTotal"];
            $total = (($data['adjustmentStock'] + $prevItemMasterStock) * $data['currentWac']) - $prevItemMasterTotal;*/

            $prevItemMasterStock = $currentWareHouseStock[$key];
            $prevItemMasterWac = $currentWac[$key];
            $adjustmentStock = $adjustment_Stock[$key];
            $adjustmentWac = $adjustment_wac[$key];
            $total = (($adjustmentStock*$adjustmentWac)-($prevItemMasterStock*$prevItemMasterWac));

            //$data['totalValue'] = ($newTotal - $previousTotal);
            $data['totalValue'] = $total;
            $data['comments'] = '';

            $this->db->insert('srp_erp_stockcountingdetails', $data);
            $last_id = $this->db->insert_id();

            if ($item_data['mainCategory'] == 'Inventory' || $item_data['mainCategory'] == 'Non Inventory') {
                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', $data['itemAutoID']);
                $this->db->where('wareHouseAutoID', $stockadjustmentMaster['wareHouseAutoID']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

                if (empty($warehouseitems)) {
                    if ($data['previousWareHouseStock'] == 0) {
                        $data_arr = array(
                            'wareHouseAutoID' => $stockadjustmentMaster['wareHouseAutoID'],
                            'wareHouseLocation' => $stockadjustmentMaster['wareHouseLocation'],
                            'wareHouseDescription' => $stockadjustmentMaster['wareHouseDescription'],
                            'itemAutoID' => $data['itemAutoID'],
                            'itemSystemCode' => $data['itemSystemCode'],
                            'itemDescription' => $data['itemDescription'],
                            'unitOfMeasureID' => $data['unitOfMeasureID'],
                            'unitOfMeasure' => $data['unitOfMeasure'],
                            'currentStock' => 0,
                            'companyID' => $this->common_data['company_data']['company_id'],
                            'companyCode' => $this->common_data['company_data']['company_code'],
                        );

                        $this->db->insert('srp_erp_warehouseitems', $data_arr);
                    }

                }
            }


            /*sub item master config : multiple add scanario */
            $adjustedStock = $data['adjustmentStock'];


            if ($item_data['isSubitemExist'] == 1) {

                if ($data['previousStock'] < $data['currentStock']) {
                    /* Add Stock */
                    $qty = $adjustedStock;

                    $subData['uom'] = $data['unitOfMeasure'];
                    $subData['uomID'] = $data['unitOfMeasureID'];
                    $subData['grv_detailID'] = $last_id;
                    $subData['warehouseAutoID'] = $stockadjustmentMaster['wareHouseAutoID'];
                    $this->add_sub_itemMaster_tmpTbl($qty, $itemAutoID, $stockCountingAutoID, $last_id, 'SCNT', $item_data['itemSystemCode'], $subData);
                }

            }
            /*end of sub item master config */

        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('error' => 1, 'e', 'Stock Counting Details : Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('error' => 0, 's', 'Stock Counting Details : Saved Successfully.');
        }
    }

    function add_sub_itemMaster_tmpTbl($qty = 0, $itemAutoID, $masterID, $detailID, $code = 'GRV', $itemCode = null, $data = array())
    {

        $uom = isset($data['uom']) && !empty($data['uom']) ? $data['uom'] : null;
        $uomID = isset($data['uomID']) && !empty($data['uomID']) ? $data['uomID'] : null;
        $grv_detailID = isset($data['grv_detailID']) && !empty($data['grv_detailID']) ? $data['grv_detailID'] : null;
        $warehouseAutoID = isset($data['warehouseAutoID']) && !empty($data['warehouseAutoID']) ? $data['warehouseAutoID'] : null;
        $data_subItemMaster = array();
        if ($qty > 0) {
            $x = 0;
            for ($i = 1; $i <= $qty; $i++) {
                $data_subItemMaster[$x]['itemAutoID'] = $itemAutoID;
                $data_subItemMaster[$x]['subItemSerialNo'] = $i;
                $data_subItemMaster[$x]['subItemCode'] = $itemCode . '/SCNT/' . $grv_detailID . '/' . $i;
                $data_subItemMaster[$x]['uom'] = $uom;
                $data_subItemMaster[$x]['wareHouseAutoID'] = $warehouseAutoID;
                $data_subItemMaster[$x]['uomID'] = $uomID;
                $data_subItemMaster[$x]['receivedDocumentID'] = $code;
                $data_subItemMaster[$x]['receivedDocumentAutoID'] = $masterID;
                $data_subItemMaster[$x]['receivedDocumentDetailID'] = $detailID;
                $data_subItemMaster[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                $data_subItemMaster[$x]['createdUserGroup'] = $this->common_data['user_group'];
                $data_subItemMaster[$x]['createdPCID'] = $this->common_data['current_pc'];
                $data_subItemMaster[$x]['createdUserID'] = $this->common_data['current_userID'];
                $data_subItemMaster[$x]['createdDateTime'] = $this->common_data['current_date'];
                $x++;
            }
        }

        if (!empty($data_subItemMaster)) {
            /** bulk insert to item master sub */
            $this->batch_insert_srp_erp_itemmaster_subtemp($data_subItemMaster);
        }
    }

    function batch_insert_srp_erp_itemmaster_subtemp($data)
    {
        $this->db->insert_batch('srp_erp_itemmaster_subtemp', $data);
    }

    function delete_counting_item()
    {
        $id = $this->input->post('stockCountingDetailsAutoID');
        $this->db->where('stockCountingDetailsAutoID', $id);
        $result = $this->db->delete('srp_erp_stockcountingdetails');
        $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentDetailID' => $id, 'receivedDocumentID' => 'SCNT'));

        if ($result) {
            $this->session->set_flashdata('s', 'Records Deleted Successfully');
            return true;
        }
    }

    function stockadjustmentAccountUpdate()
    {

        $gl = fetch_gl_account_desc($this->input->post('PLGLAutoID'));

        $BLGLAutoID = $this->input->post('BLGLAutoID');

        $data = array(
            'PLGLAutoID' => $this->input->post('PLGLAutoID'),
            'PLSystemGLCode' => $gl['systemAccountCode'],
            'PLGLCode' => $gl['GLSecondaryCode'],
            'PLDescription' => $gl['GLDescription'],
            'PLType' => $gl['subCategory'],
        );
        if (isset($BLGLAutoID)) {
            $bl = fetch_gl_account_desc($this->input->post('BLGLAutoID'));
            $data = array_merge($data, array('BLGLAutoID' => $this->input->post('BLGLAutoID'),
                'BLSystemGLCode' => $bl['systemAccountCode'],
                'BLGLCode' => $bl['GLSecondaryCode'],
                'BLDescription' => $bl['GLDescription']));
        }
        if ($this->input->post('applyAll') == 1) {
            $this->db->where('stockCountingAutoID', trim($this->input->post('masterID')));
        } else {
            $this->db->where('stockCountingDetailsAutoID', trim($this->input->post('detailID')));
        }

        $this->db->update('srp_erp_stockcountingdetails', $data);
        return array('s', 'GL Account Successfully Changed');
    }

    function load_counting_item_detail()
    {
        $this->db->select('sa.stockCountingDetailsAutoID,sa.itemDescription,sa.itemSystemCode,w.currentStock as wareHouseStock,im.companyLocalWacAmount as LocalWacAmount,sa.defaultUOMID,sa.segmentID,sa.segmentCode,sa.currentStock,sa.currentWac,sa.itemAutoID,sa.projectID');
        $this->db->from('srp_erp_stockcountingdetails sa');
        $this->db->join('srp_erp_warehouseitems w', 'w.itemAutoID = sa.itemAutoID');
        $this->db->join('srp_erp_itemmaster im', 'im.itemAutoID = sa.itemAutoID');
        $this->db->where('stockCountingDetailsAutoID', trim($this->input->post('stockCountingDetailsAutoID')));
        $this->db->where('wareHouseAutoID', trim($this->input->post('location')));
        return $this->db->get()->row_array();
    }

    function save_stock_counting_detail()
    {
        if (!trim($this->input->post('stockCountingDetailsAutoID'))) {
            $this->db->select('stockCountingDetailsAutoID,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_stockcountingdetails');
            $this->db->where('stockCountingDetailsAutoID', trim($this->input->post('stockCountingDetailsAutoID')));
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID')));
            $order_detail = $this->db->get()->result_array();
            if (!empty($order_detail)) {
                return array('w', 'Stock Counting Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }
        $this->db->trans_start();
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $projectExist = project_is_exist();
        $projectID = trim($this->input->post('projectID'));

        $this->db->select('wareHouseAutoID');
        $this->db->where('stockCountingAutoID', $this->input->post('stockCountingAutoID'));
        $stockadjustmentMaster = $this->db->get('srp_erp_stockcountingmaster')->row_array();
        $segment = explode('|', trim($this->input->post('a_segment')));
        $uom = explode('|', $this->input->post('uom'));
        $item_data = fetch_item_data(trim($this->input->post('itemAutoID')));
        $previousWareHouseStock = $this->db->query("SELECT currentStock FROM srp_erp_warehouseitems WHERE wareHouseAutoID='{$stockadjustmentMaster['wareHouseAutoID']}' and itemAutoID='{$this->input->post('itemAutoID')}'")->row_array(); //get warehouse stock of the item by location
        $data['stockCountingAutoID'] = trim($this->input->post('stockCountingAutoID'));
        $data['itemSystemCode'] = trim($this->input->post('itemSystemCode'));
        $data['itemAutoID'] = trim($this->input->post('itemAutoID'));
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($companyLocalCurrencyID, $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        $data['unitOfMeasure'] = trim($uom[0]);
        $data['unitOfMeasureID'] = trim($this->input->post('unitOfMeasureID'));
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['segmentID'] = trim($segment[0]);
        $data['segmentCode'] = trim($segment[1]);
        $data['itemFinanceCategory'] = $item_data['subcategoryID'];
        $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
        $data['financeCategory'] = $item_data['financeCategory'];
        $data['itemCategory'] = $item_data['mainCategory'];
        $data['isUpdated'] = 1;
        if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
            $data['PLGLAutoID'] = $item_data['costGLAutoID'];
            $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['PLGLCode'] = $item_data['costGLCode'];
            $data['PLDescription'] = $item_data['costDescription'];
            $data['PLType'] = $item_data['costType'];
            $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
            $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
            $data['BLGLCode'] = $item_data['assteGLCode'];
            $data['BLDescription'] = $item_data['assteDescription'];
            $data['BLType'] = $item_data['assteType'];
        } elseif ($data['financeCategory'] == 2) {
            $data['PLGLAutoID'] = $item_data['costGLAutoID'];
            $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
            $data['PLGLCode'] = $item_data['costGLCode'];
            $data['PLDescription'] = $item_data['costDescription'];
            $data['PLType'] = $item_data['costType'];
            $data['BLGLAutoID'] = '';
            $data['BLSystemGLCode'] = '';
            $data['BLGLCode'] = '';
            $data['BLDescription'] = '';
            $data['BLType'] = '';
        }
        $data['previousStock'] = trim($this->input->post('currentWareHouseStock'));
        $data['previousWac'] = trim($this->input->post('currentWac'));
        $data['previousWareHouseStock'] = $previousWareHouseStock["currentStock"];
        $data['currentWac'] = trim($this->input->post('adjustment_wac'));
        $data['currentWareHouseStock'] = trim($this->input->post('adjustment_Stock'));
        $data['currentStock'] = trim($this->input->post('adjustment_Stock'));
        $data['adjustmentWac'] = (trim($this->input->post('adjustment_wac')) - trim($this->input->post('currentWac')));
        $data['adjustmentWareHouseStock'] = (trim($this->input->post('adjustment_Stock')) - trim($this->input->post('currentWareHouseStock')));
        $data['adjustmentStock'] = (trim($this->input->post('adjustment_Stock')) - trim($this->input->post('currentWareHouseStock')));
        $previousTotal = ($data['previousStock'] * $data['previousWac']);
        $newTotal = ($data['currentStock'] * $data['currentWac']);
        $data['totalValue'] = ($newTotal - $previousTotal);
        $data['comments'] = trim($this->input->post('comments'));

        if (trim($this->input->post('stockCountingDetailsAutoID'))) {

            $this->db->where('stockCountingDetailsAutoID', trim($this->input->post('stockCountingDetailsAutoID')));
            $this->db->update('srp_erp_stockcountingdetails', $data);

            /** item master Sub codes*/
            $detailsAutoID = $this->input->post('stockCountingDetailsAutoID');

            /* 1---- delete all entries in the update process - item master sub temp */
            $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentDetailID' => $detailsAutoID, 'receivedDocumentID' => 'SCNT'));
            /* 2----  update all selected sub item list */
            if ($item_data['isSubitemExist'] == 1) {

                if ($data['previousStock'] < $data['currentStock']) {
                    /* Add Stock */
                    $qty = $data['adjustmentStock'];
                    $last_id = $detailsAutoID;
                    $documentAutoID = $data['stockCountingAutoID'];

                    $subData['uom'] = $data['unitOfMeasure'];
                    $subData['uomID'] = $data['unitOfMeasureID'];
                    $subData['grv_detailID'] = $last_id;
                    $subData['warehouseAutoID'] = $stockadjustmentMaster['wareHouseAutoID'];
                    $this->add_sub_itemMaster_tmpTbl($qty, $data['itemAutoID'], $documentAutoID, $last_id, 'SCNT', $item_data['itemSystemCode'], $subData);
                }


            }

            /* 3---- update all selected values */

            $setData['isSold'] = null;
            $setData['soldDocumentID'] = null;
            $setData['soldDocumentAutoID'] = null;
            $setData['soldDocumentDetailID'] = null;

            $ware['soldDocumentID'] = 'SCNT';
            $ware['soldDocumentDetailID'] = $detailsAutoID;

            $this->db->update('srp_erp_itemmaster_sub', $setData, $ware);


            /** end item master Sub codes*/


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Stock Counting Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Stock Counting Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Updated Successfully.');
            }
        } else {
            /* We are not using this method : there is a bulk insert method used to add the item..  */
            $this->db->insert('srp_erp_stockcountingdetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Stock Counting Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'Stock Counting Detail : ' . $data['itemSystemCode'] . ' - ' . $data['itemDescription'] . ' Saved Successfully.');
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_template_stock_counting($stockCountingAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(stockCountingDate,\'' . $convertFormat . '\') AS stockCountingDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate');
        $this->db->where('stockCountingAutoID', $stockCountingAutoID);
        $this->db->from('srp_erp_stockcountingmaster');
        $data['master'] = $this->db->get()->row_array();
        $this->db->select('*');
        $this->db->where('stockCountingAutoID', $stockCountingAutoID);
        $this->db->from('srp_erp_stockcountingdetails');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function stock_counting_confirmation()
    {
        $this->db->select('stockCountingAutoID');
        $this->db->where('stockCountingAutoID', trim($this->input->post('stockCountingAutoID')));
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_stockcountingmaster');
        $Confirmed = $this->db->get()->row_array();

        $this->db->select('isUpdated,itemAutoID,stockCountingDetailsAutoID,itemSystemCode,itemDescription');
        $this->db->where('stockCountingAutoID', trim($this->input->post('stockCountingAutoID')));
        $this->db->where('isUpdated', 0);
        $this->db->from('srp_erp_stockcountingdetails');
        $details = $this->db->get()->result_array();
        if (!empty($details)) {
            return array('error' => 1, 'message' => 'Current stock is not updated for some items');
        } else {
            if (!empty($Confirmed)) {
                return array('error' => 1, 'message' => 'Document already confirmed');
            } else {
                $id = trim($this->input->post('stockCountingAutoID'));
                $isProductReference_completed = $this->isProductReference_completed_document_SA($id);

                if ($isProductReference_completed == 0) {


                    /** item Master Sub check : sub item already added items check box are ch */

                    $validate = $this->validate_itemMasterSub($id, 'SCNT');

                    /** validation skipped until they found this. we have to do the both side of check in the validate_itemMasterSub method and have to change the query */

                    if ($validate) {

                        $this->load->library('approvals');
                        $this->db->select('stockCountingAutoID, stockCountingCode');
                        $this->db->where('stockCountingAutoID', $id);
                        $this->db->from('srp_erp_stockcountingmaster');
                        $app_data = $this->db->get()->row_array();
                        $approvals_status = $this->approvals->CreateApproval('SCNT', $app_data['stockCountingAutoID'], $app_data['stockCountingCode'], 'Stock Counting', 'srp_erp_stockcountingmaster', 'stockCountingAutoID');
                        if ($approvals_status == 1) {
                            $data = array(
                                'confirmedYN' => 1,
                                'confirmedDate' => $this->common_data['current_date'],
                                'confirmedByEmpID' => $this->common_data['current_userID'],
                                'confirmedByName' => $this->common_data['current_user']
                            );

                            $this->db->where('stockCountingAutoID', $id);
                            $this->db->update('srp_erp_stockcountingmaster', $data);
                        }else if($approvals_status==3)
                        {
                            return array('error' => 2, 'message' => 'There are no users exist to perform approval for this document');
                        }else
                        {
                            return array('error' => 1, 'message' => 'Document confirmation failed');
                        }
                        //return array('status' => true);
                        return array('error' => 0, 'message' => 'Document confirmed successfully');
                    } else {
                        return array('error' => 1, 'message' => 'Please complete sub item configurations<br/> Please add sub item/s before confirm this document.');
                    }
                } else {
                    return array('error' => 1, 'message' => 'Please complete sub item configuration, fill all the product reference numbers!.');
                }
            }
        }


    }


    function isProductReference_completed_document_SA($id)
    {
        $result = $this->db->query("SELECT
                        count(itemMaster.subItemAutoID) AS countTotal
                    FROM
                        srp_erp_stockcountingmaster stockMaster
                    LEFT JOIN srp_erp_stockcountingdetails stockAdjustment ON stockAdjustment.stockCountingAutoID = stockMaster.stockCountingAutoID
                    LEFT JOIN srp_erp_itemmaster_subtemp itemMaster ON itemMaster.receivedDocumentDetailID = stockAdjustment.stockCountingDetailsAutoID
                    LEFT JOIN srp_erp_itemmaster im ON im.itemAutoID = itemMaster.itemAutoID
                    WHERE
                        stockMaster.stockCountingAutoID = '" . $id . "'
                    AND ( ISNULL( itemMaster.productReferenceNo )
                        OR itemMaster.productReferenceNo = ''
                    )
                    AND im.isSubitemExist = 1")->row_array();

        return $result['countTotal'];

    }

    function validate_itemMasterSub($itemAutoID, $documentID)
    {

        switch ($documentID) {
            case "SR":
                $query1 = "SELECT
                        count(*) AS countAll
                    FROM
                        srp_erp_stockreturnmaster masterTbl
                    LEFT JOIN srp_erp_stockreturndetails detailTbl ON masterTbl.stockReturnAutoID = detailTbl.stockReturnAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = detailTbl.stockReturnDetailsID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockReturnAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 ";
                $query2 = "SELECT
                        SUM(detailTbl.return_Qty) AS totalQty
                    FROM
                        srp_erp_stockreturnmaster masterTbl
                    LEFT JOIN srp_erp_stockreturndetails detailTbl ON masterTbl.stockReturnAutoID = detailTbl.stockReturnAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockReturnAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1";
                break;

            case "MI":
                $query1 = "SELECT
                        count(*) AS countAll
                    FROM
                        srp_erp_itemissuemaster masterTbl
                    LEFT JOIN srp_erp_itemissuedetails detailTbl ON masterTbl.itemIssueAutoID = detailTbl.itemIssueAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = detailTbl.itemIssueDetailID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.itemIssueAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 ";
                $query2 = "SELECT
                        SUM(detailTbl.qtyIssued) AS totalQty
                    FROM
                        srp_erp_itemissuemaster masterTbl
                    LEFT JOIN srp_erp_itemissuedetails detailTbl ON masterTbl.itemIssueAutoID = detailTbl.itemIssueAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.itemIssueAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1";
                break;

            case "ST":
                $query1 = "SELECT
                        count(*) AS countAll
                    FROM
                        srp_erp_stocktransfermaster masterTbl
                    LEFT JOIN srp_erp_stocktransferdetails detailTbl ON masterTbl.stockTransferAutoID = detailTbl.stockTransferAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = detailTbl.stockTransferDetailsID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockTransferAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 ";
                $query2 = "SELECT
                        SUM(detailTbl.transfer_QTY) AS totalQty
                    FROM
                        srp_erp_stocktransfermaster masterTbl
                    LEFT JOIN srp_erp_stocktransferdetails detailTbl ON masterTbl.stockTransferAutoID = detailTbl.stockTransferAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockTransferAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1";
                break;

            case "SA":
                $query1 = "SELECT
                        count(*) AS countAll
                    FROM
                        srp_erp_stockadjustmentmaster masterTbl
                    LEFT JOIN srp_erp_stockadjustmentdetails detailTbl ON masterTbl.stockAdjustmentAutoID = detailTbl.stockAdjustmentAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = detailTbl.stockAdjustmentDetailsAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockAdjustmentAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 AND detailTbl.previousStock > detailTbl.currentStock ";
                $query2 = "SELECT
                        SUM(abs(detailTbl.adjustmentStock)) AS totalQty
                    FROM
                        srp_erp_stockadjustmentmaster masterTbl
                    LEFT JOIN srp_erp_stockadjustmentdetails detailTbl ON masterTbl.stockAdjustmentAutoID = detailTbl.stockAdjustmentAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockAdjustmentAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 AND detailTbl.previousStock > detailTbl.currentStock";
                break;

            case "SCNT":
                $query1 = "SELECT
                        count(*) AS countAll
                    FROM
                        srp_erp_stockcountingmaster masterTbl
                    LEFT JOIN srp_erp_stockcountingdetails detailTbl ON masterTbl.stockCountingAutoID = detailTbl.stockCountingAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = detailTbl.stockCountingDetailsAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockCountingAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 AND detailTbl.previousStock > detailTbl.currentStock ";
                $query2 = "SELECT
                        SUM(abs(detailTbl.adjustmentStock)) AS totalQty
                    FROM
                        srp_erp_stockcountingmaster masterTbl
                    LEFT JOIN srp_erp_stockcountingdetails detailTbl ON masterTbl.stockCountingAutoID = detailTbl.stockCountingAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.stockCountingAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 AND detailTbl.previousStock > detailTbl.currentStock";
                break;

            default:
                echo $documentID . ' Error: Code not configured!<br/>';
                echo 'File: ' . __FILE__ . '<br/>';
                echo 'Line No: ' . __LINE__ . '<br><br>';
                exit;
        }

        $r1 = $this->db->query($query1)->row_array();
        //echo $this->db->last_query();

        $r2 = $this->db->query($query2)->row_array();
        //echo $this->db->last_query();

        //exit;

        if (empty($r1) && empty($r2)) {
            $validate = true;
        } else if (empty($r1) || $r1['countAll'] == 0) {
            $validate = true;
        } else {
            if ($r1['countAll'] == $r2['totalQty']) {
                $validate = true;
            } else {
                $validate = false;
            }
        }
        return $validate;

    }

    function delete_stock_counting()
    {
        $id = trim($this->input->post('stockCountingAutoID'));

        /** Delete sub item list */
        /* 1---- delete all entries in the update process - item master sub temp */
        $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentAutoID' => $id, 'receivedDocumentID' => 'SCNT'));

        /*2-- reset all marked values */
        $setData['isSold'] = null;
        $setData['soldDocumentID'] = null;
        $setData['soldDocumentAutoID'] = null;
        $setData['soldDocumentDetailID'] = null;
        $ware['soldDocumentID'] = 'SA';
        $ware['soldDocumentAutoID'] = $id;
        $this->db->update('srp_erp_itemmaster_sub', $setData, $ware);
        /** End Delete sub item list */
        $this->db->delete('srp_erp_stockcountingdetails', array('stockCountingAutoID' => $id));


        $data = array(
            'isDeleted' => 1,
            'deletedEmpID' => current_userID(),
            'deletedDate' => current_date(),
        );
        $this->db->where('stockCountingAutoID', trim($id));
        $this->db->update('srp_erp_stockcountingmaster', $data);
        $this->session->set_flashdata('s', 'Deleted Successfully.');
        return true;
    }

    function re_open_stock_counting()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('stockCountingAutoID', trim($this->input->post('stockCountingAutoID')));
        $this->db->update('srp_erp_stockcountingmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }


    function save_stock_counting_approval()
    {
        $this->load->library('approvals');
        $system_code = trim($this->input->post('stockCountingAutoID'));
        $level_id = trim($this->input->post('Level'));
        $status = trim($this->input->post('status'));
        $comments = trim($this->input->post('comments'));

        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'SCNT');
        if ($approvals_status == 1) {
            $this->db->select('*');
            $this->db->from('srp_erp_stockcountingdetails');
            $this->db->where('srp_erp_stockcountingdetails.stockCountingAutoID', $system_code);
            $this->db->join('srp_erp_stockcountingmaster',
                'srp_erp_stockcountingmaster.stockCountingAutoID = srp_erp_stockcountingdetails.stockCountingAutoID');
            $details_arr = $this->db->get()->result_array();

            $item_arr = array();
            $itemledger_arr = array();
            $transaction_loc_tot = 0;
            $company_rpt_tot = 0;
            $supplier_cr_tot = 0;
            $company_loc_tot = 0;
            for ($i = 0; $i < count($details_arr); $i++) {
                $item = fetch_item_data($details_arr[$i]['itemAutoID']);
                $qty = $details_arr[$i]['adjustmentStock'];
                $item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                $item_arr[$i]['currentStock'] = ($item['currentStock'] + $qty);
                $item_arr[$i]['companyLocalWacAmount'] = round(($details_arr[$i]['currentWac'] / $details_arr[$i]['companyLocalExchangeRate']),
                    $details_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $item_arr[$i]['companyReportingWacAmount'] = round(($details_arr[$i]['currentWac'] / $details_arr[$i]['companyReportingExchangeRate']),
                    $details_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                $itemSystemCode = $details_arr[$i]['itemAutoID'];
                $location = $details_arr[$i]['wareHouseLocation'];
                $wareHouseAutoID = $details_arr[$i]['wareHouseAutoID'];
                $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = currentStock + {$details_arr[$i]['adjustmentWareHouseStock']}  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemSystemCode}'");

                $itemledger_arr[$i]['documentID'] = $details_arr[$i]['documentID'];
                $itemledger_arr[$i]['documentAutoID'] = $details_arr[$i]['stockCountingAutoID'];
                $itemledger_arr[$i]['documentCode'] = $details_arr[$i]['documentID'];
                $itemledger_arr[$i]['documentSystemCode'] = $details_arr[$i]['stockCountingCode'];
                $itemledger_arr[$i]['documentDate'] = $details_arr[$i]['stockCountingDate'];
                $itemledger_arr[$i]['referenceNumber'] = $details_arr[$i]['referenceNo'];
                $itemledger_arr[$i]['companyFinanceYearID'] = $details_arr[$i]['companyFinanceYearID'];
                $itemledger_arr[$i]['companyFinanceYear'] = $details_arr[$i]['companyFinanceYear'];
                $itemledger_arr[$i]['FYBegin'] = $details_arr[$i]['FYBegin'];
                $itemledger_arr[$i]['FYEnd'] = $details_arr[$i]['FYEnd'];
                $itemledger_arr[$i]['FYPeriodDateFrom'] = $details_arr[$i]['FYPeriodDateFrom'];
                $itemledger_arr[$i]['FYPeriodDateTo'] = $details_arr[$i]['FYPeriodDateTo'];
                $itemledger_arr[$i]['wareHouseAutoID'] = $details_arr[$i]['wareHouseAutoID'];
                $itemledger_arr[$i]['wareHouseCode'] = $details_arr[$i]['wareHouseCode'];
                $itemledger_arr[$i]['wareHouseLocation'] = $details_arr[$i]['wareHouseLocation'];
                $itemledger_arr[$i]['wareHouseDescription'] = $details_arr[$i]['wareHouseDescription'];
                $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                $itemledger_arr[$i]['transactionQTY'] = $qty;
                $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                $itemledger_arr[$i]['currentStock'] = $item_arr[$i]['currentStock'];
                $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                $itemledger_arr[$i]['defaultUOM'] = $details_arr[$i]['defaultUOM'];
                $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['unitOfMeasure'];
                $itemledger_arr[$i]['defaultUOMID'] = $details_arr[$i]['defaultUOMID'];
                $itemledger_arr[$i]['transactionUOMID'] = $details_arr[$i]['unitOfMeasureID'];
                $itemledger_arr[$i]['convertionRate'] = $details_arr[$i]['conversionRateUOM'];
                $itemledger_arr[$i]['PLGLAutoID'] = $details_arr[$i]['PLGLAutoID'];
                $itemledger_arr[$i]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                $itemledger_arr[$i]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                $itemledger_arr[$i]['PLDescription'] = $details_arr[$i]['PLDescription'];
                $itemledger_arr[$i]['PLType'] = $details_arr[$i]['PLType'];
                $itemledger_arr[$i]['BLGLAutoID'] = $details_arr[$i]['BLGLAutoID'];
                $itemledger_arr[$i]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                $itemledger_arr[$i]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                $itemledger_arr[$i]['BLDescription'] = $details_arr[$i]['BLDescription'];
                $itemledger_arr[$i]['BLType'] = $details_arr[$i]['BLType'];
                $itemledger_arr[$i]['transactionCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                $itemledger_arr[$i]['transactionCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                $itemledger_arr[$i]['transactionExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                $itemledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                $itemledger_arr[$i]['transactionAmount'] = (round($details_arr[$i]['totalValue'],
                    $itemledger_arr[$i]['transactionCurrencyDecimalPlaces']));
                $itemledger_arr[$i]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                $itemledger_arr[$i]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                $itemledger_arr[$i]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                $itemledger_arr[$i]['companyLocalAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['companyLocalExchangeRate']),
                    $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']));
                $itemledger_arr[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                $itemledger_arr[$i]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                $itemledger_arr[$i]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                $itemledger_arr[$i]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                $itemledger_arr[$i]['companyReportingAmount'] = (round(($details_arr[$i]['totalValue'] / $details_arr[$i]['companyReportingExchangeRate']),
                    $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']));
                $itemledger_arr[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];

                $itemledger_arr[$i]['confirmedYN'] = $details_arr[$i]['confirmedYN'];
                $itemledger_arr[$i]['confirmedByEmpID'] = $details_arr[$i]['confirmedByEmpID'];
                $itemledger_arr[$i]['confirmedByName'] = $details_arr[$i]['confirmedByName'];
                $itemledger_arr[$i]['confirmedDate'] = $details_arr[$i]['confirmedDate'];
                $itemledger_arr[$i]['approvedYN'] = $details_arr[$i]['approvedYN'];
                $itemledger_arr[$i]['approvedDate'] = $details_arr[$i]['approvedDate'];
                $itemledger_arr[$i]['approvedbyEmpID'] = $details_arr[$i]['approvedbyEmpID'];
                $itemledger_arr[$i]['approvedbyEmpName'] = $details_arr[$i]['approvedbyEmpName'];
                $itemledger_arr[$i]['segmentID'] = $details_arr[$i]['segmentID'];
                $itemledger_arr[$i]['segmentCode'] = $details_arr[$i]['segmentCode'];
                $itemledger_arr[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                $itemledger_arr[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                $itemledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $itemledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                $itemledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                $itemledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                $itemledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                $itemledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $itemledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $itemledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $itemledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
            }

            if (!empty($item_arr)) {
                $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
            }
            if (!empty($itemledger_arr)) {
                $itemledger_arr = array_values($itemledger_arr);
                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
            }

            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_scnt_data($system_code, 'SCNT');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['stockCountingAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['stockCountingCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['stockCountingDate'];
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['stockCountingDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m",
                    strtotime($double_entry['master_data']['stockCountingDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comment'];
                $generalledger_arr[$i]['chequeNumber'] = '';
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
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
                $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']),
                    $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : NULL;
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

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            }

            $maxLevel = $this->approvals->maxlevel('SCNT');

            $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? TRUE : FALSE;
            /** update sub item master : shafry */
            if ($isFinalLevel) {
                $masterID = $this->input->post('stockCountingAutoID');
                $result = $this->db->query("SELECT  * FROM srp_erp_itemmaster_subtemp WHERE receivedDocumentAutoID = '" . $masterID . "'")->result_array();
                if (!empty($result)) {
                    $i = 0;
                    foreach ($result as $item) {
                        unset($result[$i]['subItemAutoID']);
                        $i++;
                    }
                    $this->db->insert_batch('srp_erp_itemmaster_sub', $result);
                    $this->db->delete('srp_erp_itemmaster_subtemp',
                        array('receivedDocumentAutoID' => $masterID, 'receivedDocumentID' => 'SA'));

                }
            }
            $this->session->set_flashdata('s', 'Stock Counting Approval Successfully.');
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return TRUE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }

    function updateCountingStockSingle()
    {
        $itemAutoID = trim($this->input->post('itemAutoID'));
        $stockCountingDetailsAutoID = trim($this->input->post('stockCountingDetailsAutoID'));
        $isUpdated = trim($this->input->post('isUpdated'));
        $stock = trim($this->input->post('stock'));
        $previousStock = trim($this->input->post('previousStock'));
        $previousWareHouseStock = trim($this->input->post('previousWareHouseStock'));
        $stockCountingAutoID = trim($this->input->post('stockCountingAutoID'));

        $item_data = fetch_item_data($itemAutoID);
        $this->db->select('wareHouseAutoID, wareHouseCode, wareHouseLocation, wareHouseDescription');
        $this->db->where('stockCountingAutoID', $stockCountingAutoID);
        $stockadjustmentMaster = $this->db->get('srp_erp_stockcountingmaster')->row_array();

        $item_data = fetch_item_data($itemAutoID);
        $this->db->select('previousStock,previousWac,currentWac');
        $this->db->where('stockCountingDetailsAutoID', $stockCountingDetailsAutoID);
        $detail = $this->db->get('srp_erp_stockcountingdetails')->row_array();

        $data['currentWareHouseStock'] = $stock;
        $data['adjustmentWareHouseStock'] = ($stock - $previousWareHouseStock);
        $data['adjustmentStock'] = ($stock - $previousWareHouseStock);
        $data['currentStock'] = $previousStock+$data['adjustmentWareHouseStock'];
        $data['isUpdated'] = 1;

        $previousTotal = ($detail['previousStock'] * $detail['previousWac']);
        $newTotal = ($data['currentStock'] * $detail['currentWac']);
        $data['totalValue'] = ($newTotal - $previousTotal);

        $this->db->where('stockCountingDetailsAutoID', trim($stockCountingDetailsAutoID));
        $results = $this->db->update('srp_erp_stockcountingdetails', $data);
        if ($results) {
            if ($item_data['isSubitemExist'] == 1) {

                if ($data['previousStock'] < $data['currentStock']) {
                    /* Add Stock */
                    $qty = $stock;
                    $subData['uom'] = $data['unitOfMeasure'];
                    $subData['uomID'] = $data['unitOfMeasureID'];
                    $subData['grv_detailID'] = $stockCountingDetailsAutoID;
                    $subData['warehouseAutoID'] = $stockadjustmentMaster['wareHouseAutoID'];
                    $this->add_sub_itemMaster_tmpTbl($qty, $itemAutoID, $stockCountingAutoID, $stockCountingDetailsAutoID, 'SCNT', $item_data['itemSystemCode'], $subData);
                }

            }
            return array('s', 'Successfully updated');
        } else {
            return array('e', 'Stock Updating failed');
        }
    }

    function load_subcat()
    {
        $companyID = current_companyID();
        if ($this->input->post('mainCategory') == 'Inventory') {
            return $result = $this->db->query("SELECT
	*
FROM
	srp_erp_itemcategory
WHERE
	companyID = $companyID and masterID= (SELECT
	itemCategoryID
FROM
	srp_erp_itemcategory
WHERE
	companyID = $companyID and codePrefix='INV')")->result_array();
        } else {
            return $result = $this->db->query("SELECT
	*
FROM
	srp_erp_itemcategory
WHERE
	companyID = $companyID and masterID= (SELECT
	itemCategoryID
FROM
	srp_erp_itemcategory
WHERE
	companyID = $companyID and codePrefix='NINV')")->result_array();
        }
    }

    function load_subsubcat()
    {
        $companyID = current_companyID();
        $subCategoryID = $this->input->post('subCategoryID');
        if ($subCategoryID) {
            $subCategory = join(",", $subCategoryID);
        }
        if (empty($subCategory)) {
            $subCategory = 0;
        }
        return $result = $this->db->query("SELECT
	*
FROM
	srp_erp_itemcategory
WHERE companyID = $companyID and masterID IN ($subCategory)    ")->result_array();
    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'SCNT');
        $this->db->from('srp_erp_documentcodemaster ');
        return $this->db->get()->row_array();


    }

    function delete_all_detail()
    {
        $id = $this->input->post('deletechk');
        if (empty($id)) {
            return array('w', 'Select check box');
        } else {
            foreach ($id as $val) {
                $this->db->where('stockCountingDetailsAutoID', $val);
                $result = $this->db->delete('srp_erp_stockcountingdetails');
                $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentDetailID' => $val, 'receivedDocumentID' => 'SCNT'));
            }
            if ($result) {
                return array('s', 'Successfully Deleted');
            }
        }
    }

    function fetch_template_stock_counting_print($stockCountingAutoID, $subcategoryID, $subsubcategoryID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(stockCountingDate,\'' . $convertFormat . '\') AS stockCountingDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate');
        $this->db->where('stockCountingAutoID', $stockCountingAutoID);
        $this->db->from('srp_erp_stockcountingmaster');
        $data['master'] = $this->db->get()->row_array();

        $subcategory_filter = '';
        $subsubcategory_filter = '';
        if (!empty($subcategoryID)) {
            ;
            $whereIN = "( " . join(",", $subcategoryID) . " )";
            $subcategory_filter = " AND itemFinanceCategory IN " . $whereIN;
        }
        if (!empty($subsubcategoryID)) {
            ;
            $wheres = "( " . join(",", $subsubcategoryID) . " )";
            $subsubcategory_filter = " AND itemFinanceCategorySub IN " . $wheres;
        }

        $where = "stockCountingAutoID = " . $stockCountingAutoID . $subcategory_filter . $subsubcategory_filter . "";
        $this->db->select('*');
        $this->db->where($where);
        $this->db->from('srp_erp_stockcountingdetails');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function chk_delete_stock_counting_up_items()
    {
        $stockcountingautoid = $this->input->post('stockCountingAutoID');
        $this->db->select('*');
        $this->db->where('stockCountingAutoID', $stockcountingautoid);
        $this->db->where('isUpdated', 0);
        $this->db->from('srp_erp_stockcountingdetails');
        $update = $this->db->get()->result_array();
        if (!empty($update)) {
            return array('value' => 1);
        } else {
            return array('value' => 0);
        }
    }

    function delete_stock_counting_up_items()
    {
        $stockcountingautoid = $this->input->post('stockCountingAutoID');
        $result = $this->db->delete('srp_erp_stockcountingdetails', array('stockcountingAutoID' => $stockcountingautoid, 'isUpdated' => 0));
        if ($result) {
            return array('s', 'Successfully Deleted');
        }
    }

    function updateCountingWacSingle()
    {
        $itemAutoID = trim($this->input->post('itemAutoID'));
        $stockCountingDetailsAutoID = trim($this->input->post('stockCountingDetailsAutoID'));
        $isUpdated = trim($this->input->post('isUpdated'));
        $wac = trim($this->input->post('wac'));
        $previousWac = trim($this->input->post('previousWac'));
        $previousWareHouseStock = trim($this->input->post('previousWareHouseStock'));
        $stockCountingAutoID = trim($this->input->post('stockCountingAutoID'));

        $item_data = fetch_item_data($itemAutoID);
        $this->db->select('wareHouseAutoID, wareHouseCode, wareHouseLocation, wareHouseDescription');
        $this->db->where('stockCountingAutoID', $stockCountingAutoID);
        $stockadjustmentMaster = $this->db->get('srp_erp_stockcountingmaster')->row_array();

        $item_data = fetch_item_data($itemAutoID);
        $this->db->select('previousStock,previousWac,currentWac,currentStock');
        $this->db->where('stockCountingDetailsAutoID', $stockCountingDetailsAutoID);
        $detail = $this->db->get('srp_erp_stockcountingdetails')->row_array();


        $data['currentWac'] = $wac;
        $data['isUpdated'] = 1;
        $data['adjustmentWac'] = ($wac - $previousWac);
        $previousTotal = ($detail['previousStock'] * $detail['previousWac']);
        $newTotal = ($detail['currentStock'] * $wac);
        $tot=($newTotal-$previousTotal);
        $data['totalValue'] = $tot;
        $this->db->where('stockCountingDetailsAutoID', trim($stockCountingDetailsAutoID));
        $results = $this->db->update('srp_erp_stockcountingdetails', $data);
        if ($results) {
            return array('s', 'Successfully updated');
        } else {
            return array('e', 'Wac Updating failed');
        }
    }

}