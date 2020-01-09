<link rel="stylesheet" href="<?php echo base_url('plugins/iCheck/minimal/_all.css') ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<script src="<?php echo base_url('plugins/iCheck/icheck.min.js') ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<?php
$customerType = all_customer_type();
$unitType = all_umo_new_drop();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div class="box box-warning">
    <div class="box-body">
        <div class="nav-tabs-custom">

            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="posContainer">


                        <h4 style="font-size:16px; font-weight: 800;"><?php echo $this->lang->line('pos_config_yield_details');?><!--Yield Details-->
                            - <?php echo current_companyCode() ?>
                            <span class="btn btn-primary btn-xs pull-right" onclick="openAddYieldItemModal()"><i
                                    class="fa fa-plus"></i> <?php echo $this->lang->line('pos_config_add_item');?><!--Add Item--></span>
                            <span class="btn btn-default btn-xs pull-right" style="margin-right: 2px;" onclick="fetchPage('system/pos/settings/yield_setup','','Yield Setup')"><i class="fa fa-arrow-left"></i> Back</span>
                        </h4>

<!--<input type="hidden" id="yieldIDhn" name="yieldIDhn" value="<?php /*echo $this->input->post('page_id'); */?>" >-->
                        <table class="<?php echo table_class_pos(1) ?>" id="tbl_yieldDetail"
                               style="font-size:12px;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('common_item');?><!--Item--></th>
                                <th><?php echo $this->lang->line('common_type');?><!--Type--></th>
                                <th><?php echo $this->lang->line('pos_config_qty');?><!--QTY--></th>
                                <th><?php echo $this->lang->line('pos_config_uom');?><!--UOM--></th>
                                <th><?php echo $this->lang->line('pos_config_total_cost');?><!--Total Cost--></th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
            <!-- /.tab-content -->
        </div>
    </div>
</div>
<script type="text/javascript"
        src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<script>
    var defaultUOM='';
    var unitcost='';
    $(document).ready(function (e) {
        $('.select2').select2();
        fetchYieldDetail();
        initializeitemTypeahead();
    });

    function fetchYieldDetail() {
        $('#tbl_yieldDetail').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/fetch_yield_detail'); ?>",
            "aaSorting": [[0, 'desc']],
            "aoColumnDefs": [{"bSortable": false, "aTargets": [6]}],
            /*"language": {
             processing: '<i class="fa fa-refresh fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
             },*/
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[name='menueCustomerTypeIsactive']").bootstrapSwitch();
                $("[rel=tooltip]").tooltip();
                var selectedRowID = parseInt('<?php echo (!empty($this->input->post('page_id'))) ? $this->input->post('page_id') : 0; ?>');

                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    /*if (parseInt(oSettings.aoData[x]._aData['yieldDetailID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }*/

                    x++;
                }
            },
            "aoColumns": [
                {"mData": "yieldDetailID"},
                /*{"mData": "description"},*/
                {"mData": "itemDescription"},
                {"mData": "type"},
                {"mData": "qty"},
                {"mData": "UnitDes"},
                {"mData": "cost"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "yieldID", "value": $("#yieldIDhn").val()});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function openAddYieldItemModal() {
        $('#yieldDetailIDhn').val('');
        $('#fromyielddetail')[0].reset();
        $('#itemAutoID').val('');
        $('#uom').val('');
        //$('#fromyielddetail').bootstrapValidator('resetForm', true);
        $("#yield_detail_Modal").modal('show');
    }

    function loadItemDropDown(select_value){
       var id= $('#typeAutoId').val();
       var yieldID= $('#yieldIDhn').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'typeAutoId': id,'yieldID':yieldID},
            url: "<?php echo site_url('Pos_config/loadItemDropDown'); ?>",
            success: function (data) {
                $('#itemAutoID').empty();
                var mySelect = $('#itemAutoID');
                mySelect.append($('<option></option>').val('').html('Select Item'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemAutoID']).html(text['itemName']));
                    });
                    if (select_value) {
                        $("#itemAutoID").val(select_value);
                    }
                }
                $('.select2').select2();
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });

    }

    function addyieldDetail() {
        //$("#uom").prop("disabled",false);
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/saveYieldDetail') ?>",
            data: $("#fromyielddetail").serialize(),
            dataType: "json",
            cache: false,
            beforeSend: function () {
                //$("#uom").prop("disabled",true);
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    $("#yield_detail_Modal").modal('hide');
                    fetchYieldDetail();
                } else if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);

            }
        });
        return false;
    }

    function edit_yieldDetail(id){
        //$('#fromyielddetail').bootstrapValidator('resetForm', true);
        $("#yield_detail_Modal").modal("show");
        //$('#menuSizeHead').html('Edit Menu Size');
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {yieldDetailID: id},
            url: "<?php echo site_url('Pos_config/edit_yieldDetail'); ?>",
            success: function (data) {
                $('#yieldDetailIDhn').val(id);
               /* $('#description').val(data['description']);*/
                $('#typeAutoId').val(data['typeAutoId']);
                /*loadItemDropDown(data['itemAutoID']);
                setTimeout(function(){$('#itemAutoID').val(data['itemAutoID']).change(); }, 200);*/
                $('#searchItem').val(data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                $('#itemAutoID').val(data['itemAutoID']);
                $('#uom').val(data['uom']);
                $('#qty').val(data['qty']);
                $('#cost').val(data['cost']);
                $('#unitCost').val(data['unitCost']);
                setTimeout(function(){load_default_uom_and_cost(data['uom']); }, 300);
                unitcost=data['unitCost'];

            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
    }

    function changeTotalCost(){
        var qty=$('#qty').val();
        var unitCost=$('#unitCost').val();
        var cost =unitCost*qty;
        $('#cost').val(cost);
    }

    function changeUnitCost(){
        var qty=$('#qty').val();
        var cost=$('#cost').val();
        var unitCost =cost/qty;
        $('#unitCost').val(unitCost);
    }

    function load_default_uom_and_cost(edituom){
        var itemAutoID = $('#itemAutoID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Pos_config/load_default_uom"); ?>',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID},
            async: false,
            success: function (data) {
                load_default_uom(data['defaultUnitOfMeasure'],edituom);
                defaultUOM=edituom;
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function load_default_uom(short_code,edituom) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'short_code': short_code},
            url: "<?php echo site_url('dashboard/fetch_related_uom'); ?>",
            success: function (data) {

                $('#uom').empty();
                var mySelect = $('#uom');
                mySelect.append($('<option></option>').val('').html('Select UOM'));
                $.each(data, function (val, text) {
                    if(edituom == text['UnitID']){
                        mySelect.append($('<option selected></option>').val(text['UnitID']).html(text['UnitDes']));
                    }
                    else if(short_code == text['UnitID']){
                        mySelect.append($('<option selected></option>').val(text['UnitID']).html(text['UnitDes']));
                    }
                    else{
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitDes']));
                    }

                });

            }, error: function () {
                myAlert('e', 'Error in UMO fetching.')
            }
        });
    }

    function clearitemAutoIDEdit(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('#itemAutoID_edit').val('');
        }
    }

    function initializeitemTypeahead() {
        $('#searchItem').autocomplete({
            serviceUrl: '<?php echo site_url();?>/Pos_config/fetch_itemrecode_yeild/?param=<?php echo $this->input->post('page_id'); ?>',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#itemAutoID').val(suggestion.itemAutoID);
                    $('#unitCost').val(suggestion.companyLocalWacAmount);
                    unitcost=suggestion.companyLocalWacAmount;
                    changeTotalCost();
                }, 200);
                fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);
            }
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function fetch_related_uom_id(masterUnitID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $('#uom').empty();
                var mySelect = $('#uom');
                mySelect.append($('<option></option>').val('').html('Select UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $('#uom').val(select_value);
                    }
                    defaultUOM=select_value;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function convert_acoding_to_uom(){
        var transactionuom = $('#uom').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'transactionuom': transactionuom,'defaultUOMID': defaultUOM},
            url: "<?php echo site_url('dashboard/convert_acoding_to_uom'); ?>",
            success: function (data) {
                var convertion =  data['conversion'];
                var unitCost = parseFloat(unitcost) / convertion;
                $('#unitCost').val(parseFloat(unitCost));
                changeTotalCost()
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });

    }

</script>


<div class="modal fade pddLess" data-backdrop="static" id="yield_detail_Modal" role="dialog">
    <div class="modal-dialog" style="min-width: 60%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px; ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">
                    <?php echo $this->lang->line('pos_config_add_yield');?><!-- Add Yield-->
                </h4>
            </div>
            <form role="form" id="fromyielddetail" class="form-group">
                <div class="modal-body" style="min-height: 100px; ">
                    <div class="row" style="">
                        <input type="hidden" id="yieldIDhn" name="yieldIDhn" value="<?php echo $this->input->post('page_id'); ?>" >
                        <input type="hidden" class="form-control" id="yieldDetailIDhn" name="yieldDetailIDhn">
                        <div class="form-group col-sm-4">
                            <label class="lbl"><?php echo $this->lang->line('common_item'); ?><!--Item--> <?php required_mark(); ?></label>
                            <input type="text" onkeyup="clearitemAutoIDEdit(event,this)" id="searchItem"
                                   class="form-control" name="search"
                                   placeholder="<?php echo $this->lang->line('common_item_id');?>, <?php echo $this->lang->line('common_item_description');?>..."><!--Item ID--><!--Item Description-->
                            <input type="hidden" id="itemAutoID" class="form-control" name="itemAutoID">
                        </div>
                        <div class="form-group col-sm-4">
                            <label><?php echo $this->lang->line('pos_config_uom');?><!--UOM--></label>
                            <select name="uom" id="uom" class="form-control" onchange="convert_acoding_to_uom()">
                                <option value=""><?php echo $this->lang->line('pos_config_select_uom');?><!--Select UOM--></option>
                            </select>
                        </div>
                        <div class="form-group col-sm-4">
                            <label class="lbl"> <?php echo $this->lang->line('pos_config_qty');?><!--QTY--> <?php required_mark(); ?></label>
                            <input type="number" step="any" class="form-control" id="qty"
                                   name="qty" onchange="changeTotalCost()" value="1">
                        </div>
                    </div>
                    <div class="row" style="">
                        <!--<div class="form-group col-sm-4">
                            <label for=""><span id=""> UOM </span> <?php /*required_mark(); */?></label>
                            <?php /*echo form_dropdown('uom', $unitType, '', 'class="form-control" id="uom" required'); */?>
                        </div>-->
                        <div class="form-group col-sm-4">
                            <label class="lbl"> <?php echo $this->lang->line('pos_config_unit_cost');?><!--Unit Cost--> <?php required_mark(); ?></label>
                            <input type="number" step="any" class="form-control" id="unitCost"
                                   name="unitCost" onchange="changeTotalCost()">
                        </div>
                        <div class="form-group col-sm-4">
                            <label class="lbl"> <?php echo $this->lang->line('pos_config_total_cost');?><!--Total Cost--> <?php required_mark(); ?></label>
                            <input type="number" step="any" class="form-control" id="cost"
                                   name="cost" onchange="changeUnitCost()">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 5px 10px;">
                    <button type="button" onclick="addyieldDetail()" class="btn btn-primary btn-xs"><i class="fa fa-check"
                                                                                                    aria-hidden="true"></i>
                        <?php echo $this->lang->line('common_add');?><!--Add-->
                    </button>
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

