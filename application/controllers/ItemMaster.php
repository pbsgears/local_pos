<?php

class ItemMaster extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Item_model');
        $this->load->helpers('asset_management');
    }

    function fetch_item()
    {
        $this->datatables->select('itemAutoID,itemSystemCode,itemName,seconeryItemCode,itemImage,itemDescription,mainCategoryID,mainCategory,defaultUnitOfMeasure,currentStock,companyLocalSellingPrice,companyLocalCurrency,companyLocalCurrencyDecimalPlaces,revanueDescription,costDescription,assteDescription,isActive,companyLocalWacAmount,subcat.description as SubCategoryDescription,subsubcat.description as SubSubCategoryDescription,CONCAT(currentStock,\'  \',defaultUnitOfMeasure) as CurrentStock,CONCAT(companyLocalWacAmount,\'  \',companyLocalCurrency) as TotalWacAmount,CONCAT(itemSystemCode," - ",itemDescription) as description, isSubitemExist', false)
            ->from('srp_erp_itemmaster')
            ->join('srp_erp_itemcategory subcat', 'srp_erp_itemmaster.subcategoryID = subcat.itemCategoryID')
            ->join('srp_erp_itemcategory subsubcat', 'srp_erp_itemmaster.subSubCategoryID = subsubcat.itemCategoryID','left');
        if (!empty($this->input->post('mainCategory'))) {
            $this->datatables->where('mainCategoryID', $this->input->post('mainCategory'));
        }
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
        if (!empty($this->input->post('subsubcategoryID'))) {
            $this->datatables->where('subSubCategoryID', $this->input->post('subsubcategoryID'));
        }
        $this->datatables->where('srp_erp_itemmaster.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('TotalWacAmount', '$1  $2', 'number_format(companyLocalWacAmount,2),companyLocalCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        /*$this->datatables->add_column('img', "<a onclick='change_img(\"$2\",\"$3/$1\")'><img class='img-thumbnail' src='$3/$1' style='width:120px;height: 80px;' ></a>", 'itemImage,itemAutoID,base_url("images/item/")');*/
        $this->datatables->add_column('edit', '$1', 'edit(itemAutoID,isActive,isSubitemExist)');


        // $this->datatables->add_column('edit', '<spsn class="pull-right"><input type="checkbox" id="itemchkbox" name="itemchkbox" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked><br><br><a onclick="fetchPage(\'system/item/erp_item_new\',$1)"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_master($1)"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'itemAutoID');


        echo $this->datatables->generate();
    }

    function save_itemmaster()
    {
        $maincategory = $this->db->query("SELECT itemCategoryID,categoryTypeID FROM srp_erp_itemcategory WHERE itemCategoryID ={$this->input->post('mainCategoryID')}")->row_array();

        if (!$this->input->post('itemAutoID')) {
            $this->form_validation->set_rules('mainCategoryID', 'Main category', 'trim|required');
            $this->form_validation->set_rules('defaultUnitOfMeasureID', 'Unit of messure', 'trim|required');
        }
        if ($maincategory['categoryTypeID'] == 3) {
            $this->form_validation->set_rules('COSTGLCODEdes', 'Cost Account', 'trim|required');
            $this->form_validation->set_rules('ACCDEPGLCODEdes', 'Acc Dep GL Code', 'trim|required');
            $this->form_validation->set_rules('DEPGLCODEdes', 'Dep GL Code', 'trim|required');
            $this->form_validation->set_rules('DISPOGLCODEdes', 'Disposal GL Code', 'trim|required');
        }
        if ($maincategory['categoryTypeID'] == 1) {
            $this->form_validation->set_rules('assteGLAutoID', 'Asset GL Code', 'trim|required');
            $this->form_validation->set_rules('revanueGLAutoID', 'Revenue GL Code', 'trim|required');
            $this->form_validation->set_rules('costGLAutoID', 'Cost GL Code', 'trim|required');
            $this->form_validation->set_rules('stockadjust', 'Stock Adjustment GL Code', 'trim|required');
        }
        if ($maincategory['categoryTypeID'] == 2) {
            //$this->form_validation->set_rules('revanueGLAutoID', 'Revanue GL Code', 'trim|required');
            $this->form_validation->set_rules('costGLAutoID', 'Cost GL Code', 'trim|required');
        }
        $this->form_validation->set_rules('seconeryItemCode', 'Seconery Item Code', 'trim|required');
        $this->form_validation->set_rules('itemName', 'Item Name', 'trim|required');
        $this->form_validation->set_rules('itemDescription', 'Item Full Name', 'trim|required');
        $this->form_validation->set_rules('subcategoryID', 'Sub category', 'trim|required');
        /*        $this->form_validation->set_rules('maximunQty', 'Maximun Qty', 'trim|required');
                $this->form_validation->set_rules('minimumQty', 'Minimum Qty', 'trim|required');
                $this->form_validation->set_rules('reorderPoint', 'Reorder Point', 'trim|required');*/
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Item_model->save_item_master());
        }
    }

    function img_uplode()
    {
        $this->form_validation->set_rules('item_id', 'Item ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Item_model->img_uplode());
        }
    }

    function load_item_header()
    {
        echo json_encode($this->Item_model->load_item_header());
    }

    function load_subcat()
    {
        echo json_encode($this->Item_model->load_subcat());
    }

    function load_subsubcat()
    {
        echo json_encode($this->Item_model->load_subsubcat());
    }

    function edit_item()
    {
        if ($this->input->post('id') != "") {
            echo json_encode($this->Item_model->edit_item());
        } else {
            echo json_encode(FALSE);
        }
    }

    function item_master_img_uplode()
    {

        echo json_encode($this->Item_model->item_master_img_uplode());
    }

    function delete_item()
    {
        echo json_encode($this->Item_model->delete_item());
    }

    function load_gl_codes()
    {
        echo json_encode($this->Item_model->load_gl_codes());
    }

    function changeitemactive()
    {
        echo json_encode($this->Item_model->changeitemactive());

    }

    function load_category_type_id()
    {
        echo json_encode($this->Item_model->load_category_type_id());

    }

    function load_unitprice_exchangerate()
    {
        echo json_encode($this->Item_model->load_unitprice_exchangerate());

    }

    function fetch_sales_price()
    {
        echo json_encode($this->Item_model->fetch_sales_price());
    }


    function item_image_upload()
    {
        $this->form_validation->set_rules('faID', 'Document Id is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Item_model->item_image_upload());
        }
    }

    function fetch_subItem()
    {
        $itemCode = $this->input->post('itemCode');

        $this->datatables->select('itemmaster_sub.*, wh.wareHouseDescription as warehouseDescription', false)
            ->from('srp_erp_itemmaster_sub itemmaster_sub')
            ->join('srp_erp_itemmaster itemmaster', 'itemmaster.itemAutoID = itemmaster_sub.itemAutoID', 'left')
            ->join('srp_erp_warehousemaster wh', 'wh.wareHouseAutoID = itemmaster_sub.wareHouseAutoID', 'left');
        $this->datatables->where('itemmaster.itemAutoID', $itemCode);
        $this->datatables->where("(itemmaster_sub.isSold <> 1 OR itemmaster_sub.isSold IS NULL)");


        /*$this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('TotalWacAmount', '$1  $2', 'number_format(companyLocalWacAmount,2),companyLocalCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');*/
        /*$this->datatables->add_column('edit', '$1', 'edit(itemAutoID,isActive,isSubitemExist)');*/

        echo $this->datatables->generate();
        //echo $this->db->last_query();
    }

    function load_sub_itemMaster_view()
    {
        $itemCode = $this->input->post('itemCode');
        $output = $this->Item_model->load_sub_itemMaster_view($itemCode);
        $data['attributes'] = fetch_company_assigned_attributes();
        $data['itemMasterSubTemp'] = $output;

        $this->load->view('system/item/itemmastersub/ajax-item-master-list-view-modal', $data);
    }

    function fetch_item_percentage()
    {
        $this->datatables->select('itemAutoID,itemSystemCode,itemName,seconeryItemCode,itemImage,itemDescription,mainCategoryID,mainCategory,defaultUnitOfMeasure,currentStock,companyLocalSellingPrice,companyLocalCurrency,companyLocalCurrencyDecimalPlaces,revanueDescription,costDescription,assteDescription,isActive,companyLocalWacAmount,subcat.description as SubCategoryDescription,subsubcat.description as SubSubCategoryDescription,CONCAT(currentStock,\'  \',defaultUnitOfMeasure) as CurrentStock,CONCAT(companyLocalWacAmount,\'  \',companyLocalCurrency) as TotalWacAmount,CONCAT(itemSystemCode," - ",itemDescription) as description, isSubitemExist,finCompanyPercentage,pvtCompanyPercentage', false)
            ->from('srp_erp_itemmaster')
            ->join('srp_erp_itemcategory subcat', 'srp_erp_itemmaster.subcategoryID = subcat.itemCategoryID')
            ->join('srp_erp_itemcategory subsubcat', 'srp_erp_itemmaster.subSubCategoryID = subsubcat.itemCategoryID','left');
        if (!empty($this->input->post('mainCategory'))) {
            $this->datatables->where('mainCategoryID', $this->input->post('mainCategory'));
        }
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
        if (!empty($this->input->post('subsubcategoryID'))) {
            $this->datatables->where('subSubCategoryID', $this->input->post('subsubcategoryID'));
        }
        $this->datatables->where('srp_erp_itemmaster.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('DT_RowId', 'common_$1', 'itemAutoID');
        $this->datatables->add_column('fc', '<input style="width: 70%" type="text" class="form-control fc number"
                                   value="$1"
                                   name="finCompanyPercentage[]" onkeyup="validatePercentage(this,\'fc\')" onkeypress="return validateFloatKeyPress(this,event)">
                    <span class="applytoAll">
                        <button class="btn btn-xs btn-default" type="button" onclick="applyToAllCols(this)"><i
                                class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'finCompanyPercentage');
        $this->datatables->add_column('pc', '<input style="width: 70%" type="text" class="form-control pc number"
                                   value="$2"
                                   name="pvtCompanyPercentage[]" onkeyup="validatePercentage(this,\'pc\')" onkeypress="return validateFloatKeyPress(this,event,5)">
                                   <input type="hidden" name="itemAutoID[]" value="$1">
                    <span class="applytoAll">
                        <button class="btn btn-xs btn-default" type="button" onclick="applyToAllCols(this)"><i
                                class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'itemAutoID,pvtCompanyPercentage');

        echo $this->datatables->generate();
    }

    function save_item_percentage(){
        echo json_encode($this->Item_model->save_item_percentage());
    }

    function save_item_bin_location(){
        $binLocationID=$this->input->post('binLocationID');
        $itemBinlocationID=$this->input->post('itemBinlocationID');
        $this->form_validation->set_rules("itemAutoID", 'Item AutoID', 'trim|required');
        if(empty($itemBinlocationID) && $binLocationID!=''){
            $this->form_validation->set_rules("binLocationID", 'Select Bin Location', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            //$this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Item_model->save_item_bin_location());
        }
    }

    function load_item_bin_location(){
        echo json_encode($this->Item_model->load_item_bin_location());
    }

    /*function item_master_excelUpload(){
        if (isset($_FILES['excelUpload_file']['size']) && $_FILES['excelUpload_file']['size'] > 0) {
            $type = explode(".", $_FILES['excelUpload_file']['name']);
            if (strtolower(end($type)) != 'csv') {
                die(json_encode(['e', 'File type is not csv - ', $type]));
            }
            $i = 0; $n = 0;
            $filename = $_FILES["excelUpload_file"]["tmp_name"];
            $file = fopen($filename, "r");
            $dataExcel = [];
            while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                if ($i > 0) {
                    $dataExcel[$i]['ShortDescription'] = $getData[1];
                    $dataExcel[$i]['LongDescription'] = $getData[2];
                    $dataExcel[$i]['SecondaryCode'] = $getData[3];
                    $dataExcel[$i]['SellingPrice'] = $getData[4];
                    $dataExcel[$i]['Barcode'] = $getData[5];
                    $dataExcel[$i]['PartNo'] = $getData[6];
                    $dataExcel[$i]['MaximumQty'] = $getData[7];
                    $dataExcel[$i]['minimumQty'] = $getData[8];
                    $dataExcel[$i]['ReorderLevel'] = $getData[9];
                }
                $i++;
            }
            fclose($file);


            if(!empty($dataExcel)){
                echo json_encode(['m',$dataExcel]);
                //$this->load->view('system/item/itemmastersub/ajax-item-master-list-view-modal', $dataExcel);
            }else{
                echo json_encode(['e', 'No records in the uploaded file']);
            }
        }else{
            echo json_encode(['e', 'No Files Attached']);
        }
    }*/

    function item_master_excelUpload(){
        if (isset($_FILES['excelUpload_file']['size']) && $_FILES['excelUpload_file']['size'] > 0) {
            $type = explode(".", $_FILES['excelUpload_file']['name']);
            if (strtolower(end($type)) != 'csv') {
                die(json_encode(['e', 'File type is not csv - ', $type]));
            }
            $i = 0; $n = 0;
            $filename = $_FILES["excelUpload_file"]["tmp_name"];
            $file = fopen($filename, "r");
            $dataExcel = [];
            while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                if ($i > 0) {
                    $dataExcel[$i]['itemName'] = $getData[1];
                    $dataExcel[$i]['itemDescription'] = $getData[2];
                    $dataExcel[$i]['seconeryItemCode'] = $getData[3];
                    $dataExcel[$i]['companyLocalSellingPrice'] = $getData[4];
                    $dataExcel[$i]['barcode'] = $getData[5];
                    $dataExcel[$i]['partNo'] = $getData[6];
                    $dataExcel[$i]['maximunQty'] = $getData[7];
                    $dataExcel[$i]['minimumQty'] = $getData[8];
                    $dataExcel[$i]['reorderPoint'] = $getData[9];
                    $dataExcel[$i]['companyID'] = current_companyID();
                }
                $i++;
            }
            fclose($file);


            if(!empty($dataExcel)){
                $result=$this->db->insert_batch('srp_erp_itemmaster_temp', $dataExcel);
                if($result){
                    echo json_encode(['s','Successfully Updated']);
                }else{
                    echo json_encode(['e', 'Upload Failed']);
                }

                //$this->load->view('system/item/itemmastersub/ajax-item-master-list-view-modal', $dataExcel);
            }else{
                echo json_encode(['e', 'No records in the uploaded file']);
            }
        }else{
            echo json_encode(['e', 'No Files Attached']);
        }
    }

    function saveMultipleItemMaster(){
        $mainCategoryID = $this->input->post('mainCategoryID');
        $mainCategoryIDselect = $this->input->post('mainCategoryIDselect');
        $itemAutoIDhn = $this->input->post('itemAutoIDhn');
        foreach ($itemAutoIDhn as $key => $search) {
            $maincategory = $this->db->query("SELECT itemCategoryID,categoryTypeID FROM srp_erp_itemcategory WHERE itemCategoryID ={$mainCategoryIDselect}")->row_array();

            if ($maincategory['categoryTypeID'] == 3) {
                $this->form_validation->set_rules("COSTGLCODEdes[{$key}]", 'Cost Account', 'trim|required');
                $this->form_validation->set_rules("ACCDEPGLCODEdes[{$key}]", 'Acc Dep GL Code', 'trim|required');
                $this->form_validation->set_rules("DEPGLCODEdes[{$key}]", 'Dep GL Code', 'trim|required');
                $this->form_validation->set_rules("DISPOGLCODEdes[{$key}]", 'Disposal GL Code', 'trim|required');
            }
            if ($maincategory['categoryTypeID'] == 1) {
                $this->form_validation->set_rules("assteGLAutoID[{$key}]", 'Asset GL Code', 'trim|required');
                $this->form_validation->set_rules("revanueGLAutoID[{$key}]", 'Revenue GL Code', 'trim|required');
                $this->form_validation->set_rules("costGLAutoID[{$key}]", 'Cost GL Code', 'trim|required');
                $this->form_validation->set_rules("stockadjust[{$key}]", 'Stock Adjustment GL Code', 'trim|required');
            }
            if ($maincategory['categoryTypeID'] == 2) {
                //$this->form_validation->set_rules('revanueGLAutoID', 'Revanue GL Code', 'trim|required');
                $this->form_validation->set_rules("costGLAutoID[{$key}]", 'Cost GL Code', 'trim|required');
            }

            //$this->form_validation->set_rules("mainCategoryID[{$key}]", 'Main Category ID', 'trim|required');
            $this->form_validation->set_rules("defaultUnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("seconeryItemCode[{$key}]", 'Secondary Code', 'trim|required');
            $this->form_validation->set_rules("itemName[{$key}]", 'Unit of Measure', 'trim|required');
            $this->form_validation->set_rules("itemDescription[{$key}]", 'Long Description', 'trim|required');
            $this->form_validation->set_rules("subcategoryID[{$key}]", 'Sub Category', 'trim|required');
        }
            if ($this->form_validation->run() == FALSE) {
                $msg = explode('</p>', validation_errors());
                $trimmed_array = array_map('trim', $msg);
                $uniqMesg = array_unique($trimmed_array);
                $validateMsg = array_map(function ($uniqMesg) {
                    return $a = $uniqMesg . '</p>';
                }, array_filter($uniqMesg));
                echo json_encode(array('e', join('', $validateMsg)));
            }else{
                echo json_encode($this->Item_model->saveMultipleItemMaster());
            }

    }


    function fetch_item_master_server()
    {
        $main_category_arr = all_main_category_drop();
        $uom_arr = all_umo_new_drop();
        $revenue_gl_arr = all_revenue_gl_drop();
        $cost_gl_arr = all_cost_gl_drop();
        $asset_gl_arr = all_asset_gl_drop();
        $fetch_cost_account = fetch_cost_account();
        $fetch_dep_gl_code = fetch_gl_code(array('masterCategory' => 'PL', 'subCategory' => 'PLE'));
        $fetch_disposal_gl_code = fetch_gl_code(array('masterCategory' => 'PL'));
        $stock_adjustment = stock_adjustment_control_drop();
        $mainCategoryIDdrp =  form_dropdown('mainCategoryID[]', $main_category_arr, '$1', 'class="form-control mainCategoryID" onchange="load_sub_cat_bulk(this)"');
        $defaultUnitOfMeasureIDdrp =  form_dropdown('defaultUnitOfMeasureID[]', $uom_arr, '$1', 'class="form-control defaultUnitOfMeasureID" required');
        $revanueGLAutoIDdrp =  form_dropdown('revanueGLAutoID[]', $revenue_gl_arr, '$1', 'class="form-control select2 revanueGLAutoID " ');
        $costGLAutoIDdrp =  form_dropdown('costGLAutoID[]', $cost_gl_arr, '', 'class="form-control select2 costGLAutoID " ');
        $assteGLAutoIDdrp =  form_dropdown('assteGLAutoID[]', $asset_gl_arr, $this->common_data['controlaccounts']['INVA'], 'class="form-control select2 assteGLAutoID " ');
        $faCostGLAutoIDdrp =  form_dropdown('COSTGLCODEdes[]', $fetch_cost_account, '$1', 'class="form-control form1 select2 COSTGLCODEdes "');
        $faACCDEPGLAutoIDdrp =  form_dropdown('ACCDEPGLCODEdes[]', $fetch_cost_account, '$1', 'class="form-control form1 select2 ACCDEPGLCODEdes" ');
        $faDEPGLAutoIDdrp =  form_dropdown('DEPGLCODEdes[]', $fetch_dep_gl_code, '$1', 'class="form-control form1 select2 DEPGLCODEdes "  ');
        $faDISPOGLAutoIDdrp =  form_dropdown('DISPOGLCODEdes[]', $fetch_disposal_gl_code, '$1', 'class="form-control form1 select2 DISPOGLCODEdes "');
        $stockAdjustmentGLAutoIDdrp =  form_dropdown('stockadjust[]', $stock_adjustment, '$1', 'class="form-control form1 select2 stockadjust " ');


        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_itemmaster_temp.companyID = " . $companyid .  "";
        $this->datatables->select('itemAutoID,mainCategoryID,subcategoryID,subSubCategoryID,itemName,itemDescription,seconeryItemCode,defaultUnitOfMeasureID,companyLocalSellingPrice,barcode,partNo,maximunQty,minimumQty,reorderPoint,revanueGLAutoID,costGLAutoID,assteGLAutoID,faCostGLAutoID,faACCDEPGLAutoID,faDEPGLAutoID,faDISPOGLAutoID,stockAdjustmentGLAutoID')
            ->where($where)
            ->from('srp_erp_itemmaster_temp');
        $this->datatables->add_column('DT_RowId', 'common_$1', 'itemAutoID');


        $this->datatables->add_column('mainCategoryIDdrp', ''.$mainCategoryIDdrp.'
                    <span class="applytoAll">
                        <button class="btn btn-xs btn-default" type="button" onclick="applyToAllCols(this)"><i
                                class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'mainCategoryID');


        $this->datatables->add_column('subcategoryIDdrp', '<select name="subcategoryID[]" class="form-control subcategoryID searchbox"
                                            onchange="load_sub_sub_cat_bulk(this),load_gl_codes(this)">
                                        <option value="">Select Category</option>
                                    </select>
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllColsSubCat(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'subcategoryID');


        $this->datatables->add_column('subSubCategoryIDdrp', '<select name="subSubCategoryID[]" class="form-control subSubCategoryID searchbox">
                                        <option value="">Select Category</option>
                                    </select>
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllColsSubSubCat(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'subSubCategoryID');

        $this->datatables->add_column('itemNamedrp', '<input type="text" value="$1" class="form-control itemName" name="itemName[]">', 'itemName');

        $this->datatables->add_column('itemDescriptiondrp', '<input type="text" value="$1" class="form-control itemDescription" name="itemDescription[]">', 'itemDescription');

        $this->datatables->add_column('seconeryItemCodedrp', '<input type="text" value="$1" class="form-control seconeryItemCode" name="seconeryItemCode[]">', 'seconeryItemCode');

        $this->datatables->add_column('defaultUnitOfMeasureIDdrp', ''.$defaultUnitOfMeasureIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllColsUOM(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'defaultUnitOfMeasureID');

        $this->datatables->add_column('companyLocalSellingPricedrp', '<input type="text"  step="any" class="form-control companyLocalSellingPrice number"  name="companyLocalSellingPrice[]" value="$1">', 'companyLocalSellingPrice');

        $this->datatables->add_column('barcodedrp', '<input type="text" value="$1" class="form-control barcode" name="barcode[]">', 'barcode');

        $this->datatables->add_column('partNodrp', '<input type="text" value="$1" class="form-control partno" name="partno[]">', 'partNo');

        $this->datatables->add_column('maximunQtydrp', '<input type="text"  value="$1" class="form-control number maximunQty cls_maximunQty" name="maximunQty[]"><span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllmaximunQty(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'maximunQty');

        $this->datatables->add_column('minimumQtydrp', '<input type="text" value="$1" class="form-control number minimumQty cls_minimumQty" name="minimumQty[]"><span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllminimumQty(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'minimumQty');

        $this->datatables->add_column('reorderPointdrp', '<input type="text" value="$1" class="form-control number reorderPoint cls_reorderPoint" name="reorderPoint[]"><span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllreorderPoint(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'reorderPoint');

        $this->datatables->add_column('revanueGLAutoIDdrp', ''.$revanueGLAutoIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllrevanueGLAutoID(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'revanueGLAutoID');

        $this->datatables->add_column('costGLAutoIDdrp', ''.$costGLAutoIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllcostGLAutoID(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'costGLAutoID');

        $this->datatables->add_column('assteGLAutoIDdrp', ''.$assteGLAutoIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllassteGLAutoID(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'assteGLAutoID');

        $this->datatables->add_column('faCostGLAutoIDdrp', ''.$faCostGLAutoIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllCOSTGLCODEdes(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'faCostGLAutoID');

        $this->datatables->add_column('faACCDEPGLAutoIDdrp', ''.$faACCDEPGLAutoIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllACCDEPGLCODEdes(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'faACCDEPGLAutoID');

        $this->datatables->add_column('faDEPGLAutoIDdrp', ''.$faDEPGLAutoIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllDEPGLCODEdes(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'faDEPGLAutoID');

        $this->datatables->add_column('faDISPOGLAutoIDdrp', ''.$faDISPOGLAutoIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllDISPOGLCODEdes(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'faDISPOGLAutoID');

        $this->datatables->add_column('stockAdjustmentGLAutoIDdrp', ''.$stockAdjustmentGLAutoIDdrp.'
                    <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllstockadjust(this)"> <i class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'stockAdjustmentGLAutoID');

        $this->datatables->add_column('itemAutoIDhn', '<input type="hidden" value="$1" class="form-control number itemAutoIDhn" name="itemAutoIDhn[]">', 'itemAutoID');




        echo $this->datatables->generate();
    }

    function downloadExcel(){


        $csv_data = [
            [
                0 => '#',
                1 => 'Short Description',
                2 => 'Long Description',
                3 => 'Secondary Code',
                4 => 'Selling Price',
                5 => 'Barcode',
                6 => 'Part No',
                7 => 'Maximum Qty',
                8 => 'Minimum Qty',
                9 => 'Reorder Level',
            ]
        ];


        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=Item Master.csv");


        $output = fopen("php://output", "w");
        foreach ($csv_data as $row){
            fputcsv($output, $row);
        }
        fclose($output);
    }



}
