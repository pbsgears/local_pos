<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div class="box box-danger">
    <div class="box-body">

        <div id="filter-panel" class="collapse filter-panel"></div>
        <div class="row">
            <div class="col-md-9 text-center"> &nbsp; </div>
            <div class="col-md-3 text-right">
                <button type="button" onclick="open_warehouse_model()" class="btn btn-primary btn-sm pull-right"><i
                        class="fa fa-plus"></i>
                    <?php echo $this->lang->line('pos_config_create_new_outlet'); ?><!--Create new outlet-->
                </button>
            </div>
        </div>
        <hr style="margin: 5px 0px;">
        <div class="table-responsive">
            <table id="warehousemaster_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th><?php echo $this->lang->line('pos_config_warehouse_code'); ?><!--Warehouse Code--></th>
                    <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                    <th><?php echo $this->lang->line('common_Location'); ?><!--Location--></th>
                    <th><?php echo $this->lang->line('common_address'); ?><!--Address--></th>
                    <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                    <th style="width: 50px;">&nbsp;</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="warehousemaster_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-building-o"></i>
                    <?php echo $this->lang->line('pos_config_outlets'); ?><!-- Outlets--></h4>
            </div>
            <form role="form" id="warehousemaster_form" class="form-horizontal">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="warehouseredit" name="warehouseredit">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">
                                <?php echo $this->lang->line('common_code'); ?><!--Code--></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="warehousecode" name="warehousecode">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">
                                <?php echo $this->lang->line('common_description'); ?><!--Description--></label>
                            <div class="col-sm-6">
                                    <textarea class="form-control" rows="2" id="warehousedescription"
                                              name="warehousedescription"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">
                                <?php echo $this->lang->line('common_Location'); ?><!--Location--></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="warehouselocation"
                                       name="warehouselocation">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">
                                <?php echo $this->lang->line('common_address'); ?><!--Address--></label>
                            <div class="col-sm-6">
                                    <textarea rows="2" class="form-control" id="warehouseAddress"
                                              name="warehouseAddress"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">
                                <?php echo $this->lang->line('common_telephone'); ?><!--Telephone--></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="warehouseTel" name="warehouseTel">
                            </div>
                        </div>
                    </div>
                    <div class="row hide">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Pos
                                <?php echo $this->lang->line('common_Location'); ?><!--  Location--></label>
                            <div class="col-sm-6" style="">
                                <input type="checkbox" value="1" id="isPosLocation" name="isPosLocation">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Foot Note</label>
                            <div class="col-sm-6" style="">
                                <input type="text" class="form-control" id="pos_footNote" name="pos_footNote">
                            </div>
                        </div>
                    </div>
                    <div class="row onlyForEdit">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Outlet Template</label>
                            <div class="col-sm-6" style="">

                                <?php echo form_dropdown('outletTemplateMasterID', get_pos_outletTemplateMaster(), '1', 'class="form-control" id="outletTemplateMasterID" ') ?>
                            </div>
                        </div>
                    </div>
                    <div class="row onlyForEdit" id="imageUploadDiv">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"></label>
                            <div class="col-sm-6" style="">

                                <div class="fileinput-new thumbnail">
                                    <?php if (isset($header['contactImage']) != '') { ?>
                                        <img
                                            src="<?php echo base_url('uploads/crm/profileimage/' . isset($header['contactImage'])); ?>"
                                            id="changeImg" style="width: 200px; height: 145px;">
                                        <?php
                                    } else { ?>
                                        <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="changeImg"
                                             style="width: 200px; height: 145px;">
                                    <?php } ?>
                                    <input type="file" name="contactImage" id="itemImage" style="display: none;"
                                           onchange="loadImage(this)"/>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#changeImg').click(function () {
            $('#itemImage').click();
        });
        $('.skin-square input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%'
        });
        warehousemasterview();
        $('#warehousemaster_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                warehousecode: {validators: {notEmpty: {message: ' Code is required.'}}},
                warehousedescription: {validators: {notEmpty: {message: ' Description is required.'}}}
                /*warehouselocation: {validators: {notEmpty: {message: ' Location is required.'}}},
                 warehouseAddress: {validators: {notEmpty: {message: ' Address is required.'}}},
                 warehouseTel: {validators: {notEmpty: {message: ' Telephone is required.'}}}*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Pos_config/save_outlet'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data['error'] == 0) {
                        if (data) {
                            $("#warehousemaster_model").modal("hide");
                            warehousemasterview();
                            //fetchPage('system/srp_mu_suppliermaster_view','Test','Supplier Master');
                        }
                    } else {
                        myAlert('e', data['message']);
                    }
                }, error: function () {
                    stopLoad();
                    myAlert('e', 'something went wrong, Please contact your support team');
                }
            });
        });

    });

    function warehousemasterview() {
        $('#warehousemaster_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/loadCompanyOutlets'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "wareHouseAutoID"},
                {"mData": "wareHouseCode"},
                {"mData": "wareHouseDescription"},
                {"mData": "wareHouseLocation"},
                {"mData": "address"},
                {"mData": "outletStatus"},
                {"mData": "action"}
            ],
            // "columnDefs": [{
            //     "targets": [5],
            //     "orderable": false
            // }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function open_warehouse_model() {
        $(".onlyForEdit").hide();
        // alert('xx')
        $('#warehousemaster_form')[0].reset();
        $("#warehouseredit").val('')
        $('#warehousemaster_form').bootstrapValidator('resetForm', true);
        $("#warehousemaster_model").modal({backdrop: "static"});
        //$('#warehouseredit').val("");
        //document.getElementById('warehousemaster_form').reset();
    }

    function openwarehousemastermodel(id) {


        //$("#warehousemaster_model").modal("show");
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {id: id},
            url: "<?php echo site_url('srp_warehouseMaster/edit_warehouse'); ?>",
            beforeSend: function () {

            },
            success: function (data) {

                open_warehouse_model();
                $('#warehouseredit').val(id);
                $('#warehousecode').val(data['wareHouseCode']);
                $('#warehousedescription').val(data['wareHouseDescription']);
                $('#warehouselocation').val(data['wareHouseLocation']);
                $('#warehouseAddress').val(data['warehouseAddress']);
                $('#warehouseTel').val(data['warehouseTel']);
                $('#pos_footNote').val(data['pos_footNote']);
                $('#outletTemplateMasterID').val(data['outletTemplateMasterID']);
                if (data['warehouseImage'] != '' && data['warehouseImage'] != null) {
                    var src = '<?php echo base_url('uploads'); ?>/warehouses/' + data['warehouseImage'];
                } else {
                    var src = '<?php echo base_url('images/item/no-image.png'); ?>';
                }
                $($('#changeImg')).attr("src", src);
                if (data['isPosLocation'] == 1) {
                    $('#isPosLocation').prop('checked', true);
                }
                $(".onlyForEdit").show();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');

            }
        });
    }

    function loadImage(obj) {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#changeImg').attr('src', e.target.result);
            };
            reader.readAsDataURL(obj.files[0]);
            profileImageUploadContact();
        }
    }

    function profileImageUploadContact() {
        var imgageVal = new FormData();
        imgageVal.append('wareHouseAutoID', $("#warehouseredit").val());

        var files = $("#itemImage")[0].files[0];
        imgageVal.append('files', files);
        // var formData = new FormData($("#contact_profile_image_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: imgageVal,
            contentType: false,
            cache: false,
            processData: false,
            url: "<?php echo site_url('Pos_config/warehouse_image_upload'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {

                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

</script>