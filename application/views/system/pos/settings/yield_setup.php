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
                        <h4 style="font-size:16px; font-weight: 800;">
                            <?php echo $this->lang->line('pos_config_yield_master'); ?><!--Yield Master-->
                            - <?php echo current_companyCode() ?>
                            <span class="btn btn-primary btn-xs pull-right" onclick="openAddYieldModal()"><i
                                        class="fa fa-plus"></i>
                                <?php echo $this->lang->line('common_update_add_new'); ?><!--Add New--></span>
                        </h4>

                        <table class="<?php echo table_class_pos(1) ?>" id="tbl_yieldMaster"
                               style="font-size:12px;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('common_description'); ?><!--Description--> </th>
                                <th><?php echo $this->lang->line('pos_config_uom'); ?><!--UOM--></th>
                                <th><?php echo $this->lang->line('pos_config_qty'); ?><!--QTY--></th>
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
    $(document).ready(function (e) {
        fetchYieldMaster();
        initializeitemTypeahead();
    });

    function openAddYieldModal() {
        $('#yieldIDhn').val('');
        $("#itemAutoID").val("");
        $('#fromyield')[0].reset();
        $('#fromyield').bootstrapValidator('resetForm', true);
        $("#yield_Modal").modal('show');
    }

    function addyield() {
        $("#yieldUOM").prop("disabled",false);
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/saveYield') ?>",
            data: $("#fromyield").serialize(),
            dataType: "json",
            cache: false,
            beforeSend: function () {
                $("#yieldUOM").prop("disabled",true);
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    $("#yield_Modal").modal('hide');
                    fetchYieldMaster();
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

    /** function delete_menuSize(id) {
     bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
     if (result) {
     $.ajax({
     type: "POST",
     url: "<?php echo site_url('Pos_config/delete_menuSize') ?>",
     data: {id: id},
     dataType: "json",
     cache: false,
     beforeSend: function () {
     startLoad();
     },
     success: function (data) {
     stopLoad();
     if (data['error'] == 0) {
     fetchMenuSize();
     myAlert('s', data['message']);
     } else {
     myAlert('e', '<div>' + data['message'] + '</div>');
     }
     },
     error: function (jqXHR, textStatus, errorThrown) {
     stopLoad();
     myAlert('e', 'Status: ' + textStatus + '<br>Message: ' + errorThrown);
     }
     });
     }
     });
     }*/

    function fetchYieldMaster() {
        $('#tbl_yieldMaster').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/fetch_yield_master'); ?>",
            "aaSorting": [[0, 'desc']],
            "aoColumnDefs": [{"bSortable": false, "aTargets": [4]}],
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


                    if (parseInt(oSettings.aoData[x]._aData['yieldID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "yieldID"},
                {"mData": "Description"},
                {"mData": "yieldUOM"},
                {"mData": "qty"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
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

    function edit_yieldMaster(id) {
        $('#fromyield').bootstrapValidator('resetForm', true);
        $("#yield_Modal").modal("show");
        //$('#menuSizeHead').html('Edit Menu Size');
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {yieldID: id},
            url: "<?php echo site_url('Pos_config/edit_yieldMaster'); ?>",
            success: function (data) {
                $('#yieldIDhn').val(id);
                $('#searchItem').val(data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                $('#itemAutoID').val(data['itemAutoID']);
                $('#yieldUOM').val(data['yielduomID']);
                $('#qty').val(data['qty']);
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
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
            serviceUrl: '<?php echo site_url();?>/PurchaseRequest/fetch_itemrecode_pqr/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#itemAutoID').val(suggestion.itemAutoID);
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
                $('#yieldUOM').empty();
                var mySelect = $('#yieldUOM');
                mySelect.append($('<option></option>').val('').html('Select UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $('#yieldUOM').val(select_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

</script>


<div class="modal fade pddLess" data-backdrop="static" id="yield_Modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="min-width: 60%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px; ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">
                    <?php echo $this->lang->line('pos_config_add_yield'); ?><!--Add Yield-->
                </h4>
            </div>
            <form role="form" id="fromyield" class="form-group">
                <div class="modal-body" style="min-height: 100px; ">
                    <div class="row" style="padding-left: 2%;">
                        <input type="hidden" class="form-control" id="yieldIDhn" name="yieldIDhn">
                        <div class="form-group col-sm-4">
                            <label class="lbl">
                                <?php echo $this->lang->line('common_item'); ?><!--Item--> <?php required_mark(); ?></label>
                            <input type="text" onkeyup="clearitemAutoIDEdit(event,this)" id="searchItem"
                                   class="form-control" name="search"
                                   placeholder="<?php echo $this->lang->line('common_item_id');?>, <?php echo $this->lang->line('common_item_description');?>..."><!--Item ID--><!--Item Description-->
                            <input type="hidden" id="itemAutoID" class="form-control" name="itemAutoID">
                        </div>
                        <div class="form-group col-sm-4">
                            <label for=""><span
                                        id=""> <?php echo $this->lang->line('pos_config_uom'); ?><!--UOM--> </span> <?php required_mark(); ?>
                            </label>
                            <?php echo form_dropdown('yieldUOM', $unitType, '', 'class="form-control select2" id="yieldUOM" required disabled'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label class="lbl">
                                <?php echo $this->lang->line('pos_config_qty'); ?><!--QTY--> <?php required_mark(); ?></label>
                            <input type="number" step="any" class="form-control" id="qty"
                                   name="qty" value="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 5px 10px;">
                    <button type="button" onclick="addyield()" class="btn btn-primary btn-xs"><i class="fa fa-check"
                                                                                                 aria-hidden="true"></i>
                        <?php echo $this->lang->line('common_add'); ?><!--Add-->
                    </button>
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

