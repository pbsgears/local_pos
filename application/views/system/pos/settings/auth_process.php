<link rel="stylesheet" href="<?php echo base_url('plugins/iCheck/minimal/_all.css') ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<script src="<?php echo base_url('plugins/iCheck/icheck.min.js') ?>"></script>
<?php
$process_arr = all_authentication_process();
$warehouse_arr = get_warehouse_drop();
$usergroup_arr = all_pos_usergroup();
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
                            <?php echo "Authentication Process" ?><!--Promotion & Order Setup-->
                            - <?php echo current_companyCode() ?>
                            <span class="btn btn-primary btn-xs pull-right" onclick="openAddProcessModal()"><i
                                        class="fa fa-plus"></i>
                                <?php echo $this->lang->line('common_update_add_new'); ?><!--Add New--></span>
                        </h4>
                        <table class="<?php echo table_class_pos(1) ?>" id="tbl_process"
                               style="font-size:12px;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo "Process" ?><!--Process--> </th>
                                <th><?php echo "Status" ?><!--Status--> </th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="tab_2">
                    <div id="menu_edit_container2"></div>

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
        $('.select2').select2();
        fetchAuthProcess();
        $('#process_drop').multiselect2({
            enableCaseInsensitiveFiltering: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('#userGroupMasterID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
    });

    function openAddProcessModal() {
        $('#process_drop').multiselect2('deselectAll', false);
        $('#process_drop').multiselect2('updateButtonText');
        $('#fromauthprocess')[0].reset();
        $('#fromauthprocess').bootstrapValidator('resetForm', true);
        $("#process_Modal").modal('show');
    }

    function addProcess() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/addProcess') ?>",
            data: $("#fromauthprocess").serialize(),
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    $("#process_Modal").modal('hide');
                    fetchAuthProcess();
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

    function fetchAuthProcess() {
        $('#tbl_process').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/fetch_auth_process'); ?>",
            "aaSorting": [[1, 'asc']],
            /*"language": {
             processing: '<i class="fa fa-refresh fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
             },*/
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[name='processIsActive']").bootstrapSwitch();
                $("[rel=tooltip]").tooltip();
                var selectedRowID = parseInt('<?php echo (!empty($this->input->post('page_id'))) ? $this->input->post('page_id') : 0; ?>');
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['processMasterID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "processMasterID"},
                {"mData": "description"},
                {"mData": "active"},
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

    function fetchUserGroup(processMasterID) {
        $('#tbl_usergroup').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/fetch_assigned_user_group'); ?>",
            "aaSorting": [[1, 'asc']],
            /*"language": {
             processing: '<i class="fa fa-refresh fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
             },*/
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = parseInt('<?php echo (!empty($this->input->post('page_id'))) ? $this->input->post('page_id') : 0; ?>');
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['userGroupDetailID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "userGroupDetailID"},
                {"mData": "descriptionOutlet"},
                {"mData": "usergroup"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({name: 'processMasterID', value: processMasterID});
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

    function changeProcessIsActive(processMasterID) {
        var compchecked = 0;
        if ($('#processIsActive_' + processMasterID).is(":checked")) {
            compchecked = 1;
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {processMasterID: processMasterID, chkedvalue: compchecked},
                url: "<?php echo site_url('Pos_config/update_auth_process_isactive'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert('s', data['message']);
                    if (data['message'] == 's') {

                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });

        }
        else if (!$('#processIsActive_' + processMasterID).is(":checked")) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {processMasterID: processMasterID, chkedvalue: 0},
                url: "<?php echo site_url('Pos_config/update_auth_process_isactive'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert('s', data['message']);
                    if (data['message'] == 's') {

                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    function assign_user_group(processMasterID, process) {
        $('#userGroupMasterID').multiselect2('deselectAll', false);
        $('#userGroupMasterID').multiselect2('updateButtonText');
        $('#wareHouseID').val('').change();
        $('#formassignusergroup')[0].reset();
        $('#formassignusergroup').bootstrapValidator('resetForm', true);
        $("#usergroup_Modal").modal('show');
        $("#usergroupProcessMasterID").val(processMasterID);
        $("#process-title").text(process);
        fetchUserGroup(processMasterID);
    }

    function add_user_group() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/add_user_group') ?>",
            data: $("#formassignusergroup").serialize(),
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    $('#userGroupMasterID').multiselect2('deselectAll', false);
                    $('#userGroupMasterID').multiselect2('updateButtonText');
                    $('#formassignusergroup')[0].reset();
                    $('#wareHouseID').val('').change();
                    fetchUserGroup($('#usergroupProcessMasterID').val());
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


    function delete_assigned_user_group(userGroupDetailID) {
        swal({
                title: "Are you sure?",
                text: "You want to Delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "delete",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('Pos_config/delete_assigned_user_group'); ?>",
                    type: 'post',
                    data: {userGroupDetailID: userGroupDetailID},
                    dataType: 'json',
                    cache: false,

                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 1) {
                            swal("Error!", data['message'], "error");
                        }
                        else if (data['error'] == 0) {
                            fetchUserGroup($('#usergroupProcessMasterID').val());
                            swal("Deleted!", data['message'], "success");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }


</script>

<div class="modal fade pddLess" data-backdrop="static" id="process_Modal" role="dialog">
    <div class="modal-dialog" style="min-width: 40%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px; ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">
                    Authentication Process
                </h4>
            </div>
            <form role="form" id="fromauthprocess" class="form-horizontal">
                <div class="modal-body" style="min-height: 80px; ">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <fieldset>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"
                                           for="customerName">Process</label>
                                    <div class="col-md-6">
                                        <?php echo form_dropdown('processMasterID[]', $process_arr, "", 'id="process_drop" class="form-control" multiple') ?>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 5px 10px;">
                    <button type="button" onclick="addProcess()" class="btn btn-primary btn-xs"><i class="fa fa-check"
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

<div class="modal fade pddLess" data-backdrop="static" id="usergroup_Modal" role="dialog">
    <div class="modal-dialog" style="min-width: 50%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px; ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">
                    Assign User Group - <span id="process-title"></span>
                </h4>
            </div>
            <form role="form" id="formassignusergroup" class="form-horizontal">
                <input type="hidden" name="processMasterID" id="usergroupProcessMasterID">
                <div class="modal-body" style="min-height: 80px; ">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <fieldset>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"
                                           for="customerName">Outlet</label>
                                    <div class="col-md-6">
                                        <?php echo form_dropdown('wareHouseID', $warehouse_arr, "", 'id="wareHouseID" class="form-control select2"') ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label"
                                           for="customerName">User Group</label>
                                    <div class="col-md-6">
                                        <?php echo form_dropdown('userGroupMasterID[]', $usergroup_arr, "", 'id="userGroupMasterID" class="form-control" multiple') ?>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: 10px">
                            <table class="<?php echo table_class_pos(1) ?>" id="tbl_usergroup"
                                   style="font-size:12px;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Outlet</th>
                                    <th>User Group</th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 5px 10px;">
                    <button type="button" onclick="add_user_group()" class="btn btn-primary btn-xs"><i
                                class="fa fa-check"
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
