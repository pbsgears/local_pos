<?php

$locations = get_active_outlets_drop();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>

<div class="box box-primary">
    <div class="box-body">
        <h4 style="font-size:16px; font-weight: 800;">
            <i class="fa fa-video-camera text-blue" aria-hidden="true"></i>
            Camera Setup
            <button class="btn btn-xs btn-primary pull-right" type="button" id="btn_addCamera">
                <i class="fa fa-plus" aria-hidden="true"></i> Add Camera
            </button>
        </h4>
        <div class="container-wifi-filter">
            <div class="row">
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                    <i class="fa fa-filter text-purple" aria-hidden="true"></i>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                    <label>Outlet</label>
                </div>
                <div class="col-xs-8 col-sm-6 col-md-5 col-lg-5">
                    <?php echo form_dropdown('outletID', $locations, '', ' type="button" id="outletID" class="form-control select2"') ?>
                </div>
            </div>
        </div>
        <table class="<?php echo table_class() ?>" id="tbl_camera_setup" style="width: 100%">
            <thead>
            <tr>
                <th>#</th>
                <th>URL</th>
                <th>Port Number</th>
                <th>Outlet</th>
                <th>Created By</th>
                <th>Created Date</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
    var DT_CameraTable;
    $(document).ready(function () {
        $('.select2').select2();
        $("#btn_addCamera").click(function () {
            add_camera_setup_modal();
        });

        $("#btn_save_camera_setup").click(function () {
            save_camera_setup();
        });

        DT_CameraTable = $('#tbl_camera_setup').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": false,
            "sAjaxSource": "<?php echo site_url('Pos_cameraSetup/LoadCameraSetup'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "url_host"},
                {"mData": "port"},
                {"mData": "wareHouseDescription"},
                {"mData": "createdUserName"},
                {"mData": "createdDateTime"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'outletID', 'value': $("#outletID").val()});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });

        $("#outletID").change(function () {
            DT_CameraTable.ajax.reload();
        })

        $("#outletID_from").change(function () {
            if ($("#outletID").val() != $("#outletID_from").val()) {
                $("#outletID").val($("#outletID_from").val()).change();
                DT_CameraTable.ajax.reload();
            }
        })
    });

    function add_camera_setup_modal() {
        $("#camera_setup_add_modal").modal('show');
        $("#frm_camera_setup")[0].reset();
        $("#setup_id").val(0);
        $("#outletID_from").val($("#outletID").val()).change();
    }

    function save_camera_setup() {
        var data = $("#frm_camera_setup").serialize();
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_cameraSetup/save_camera_setup'); ?>",
            data: data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['status'], data['message']);
                if (data['status'] == 's') {
                    DT_CameraTable.ajax.reload();
                    $("#camera_setup_add_modal").modal('hide');
                }
            }, error: function () {
                stopLoad();
                myAlert('e', 'Error in loading currency denominations.')
            }
        });
    }

    function delete_camera_setup(id) {
        swal({
                title: "Are you sure",
                text: "You want to Delete this?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "Delete",
                cancelButtonText: "Cancel"
            },
            function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "<?php echo site_url('Pos_cameraSetup/delete_camera_setup'); ?>",
                    data: {id: id},
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data['status'], data['message']);
                        if (data['status'] == 's') {
                            DT_CameraTable.ajax.reload();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', jqXHR + ' : ' + textStatus + ' : ' + errorThrown);
                    }
                });
            });
    }

    function edit_camera_setup(id) {
        $("#camera_setup_add_modal").modal('show');
        $("#setup_id").val(id);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_cameraSetup/edit_camera_setup'); ?>",
            data: {id: id},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status'] == 's') {
                    $("#url_host").val(data['url_host']);
                    $("#port").val(data['port']);
                    $("#outletID_from").val(data['outletID']).change();
                } else {
                    myAlert(data['status'], data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', jqXHR + ' : ' + textStatus + ' : ' + errorThrown);
            }
        });
    }
</script>

<div class="modal fade pddLess" data-backdrop="static" id="camera_setup_add_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title"> Add Camera Link </h4></div>
            <div class="modal-body">
                <form class="form-horizontal" id="frm_camera_setup">
                    <input type="hidden" id="setup_id" name="id">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="url_host">Outlet </label>
                            <div class="col-md-9">
                                <?php echo form_dropdown('outletID', $locations, '', ' type="button" id="outletID_from" class="form-control select2"') ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="url_host">URL</label>
                            <div class="col-md-6">
                                <input id="url_host" name="url_host" type="text"
                                       placeholder="URL with http:// or https://"
                                       class="form-control input-md" required="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="port">Port</label>
                            <div class="col-md-2">
                                <input id="port" name="port" type="text" placeholder="eg: 80"
                                       class="form-control input-md">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label" for=""></label>
                            <div class="col-md-4">
                                <button type="button" id="btn_save_camera_setup" class="btn btn-primary">
                                    <i class="fa fa-plus" aria-hidden="true"></i> Add
                                </button>
                            </div>
                        </div>

                    </fieldset>
                </form>

            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>