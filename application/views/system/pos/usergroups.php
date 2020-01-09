<?php
$locations = load_pos_location_drop();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

/*echo '<pre>';print_r($locations);echo '<pre>';*/
?>
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
    <div class="box box-warning">
        <div class="box-body">
            <div class="nav-tabs-custom">
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <div class="posContainer">
                            <h4 style="font-size:16px; font-weight: 800;">
                                <?php echo "User Group" ?><!--User Group-->
                                - <?php echo current_companyCode() ?>
                                <span class="btn btn-primary btn-xs pull-right" onclick="open_newUserGroupModel()"><i
                                            class="fa fa-plus"></i>
                                    <?php echo $this->lang->line('common_update_add_new'); ?><!--Add New--></span>
                            </h4>

                            <table id="usergroup_table" class="<?php echo table_class(); ?>">
                                <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 40%">User Group</th>
                                    <th style="width: 5%">Active</th>
                                    <th style="width: 5%">&nbsp;</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
    <div class="modal fade" id="userGroupCreate_model" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header  modal-header-mini" style="padding: 5px 10px; ">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                    </button>
                    <h4 class="modal-title" id="counterCreate_modal_title"></h4>
                </div>
                <form role="form" id="userGroup_form" class="form-horizontal">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">User Group</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="description" name="description">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">

                        <input type="hidden" id="requestLink" name="requestLink">
                        <input type="hidden" id="userGroupMasterID" name="userGroupMasterID">

                        <button type="submit" class="btn btn-primary btn-sm updateBtn submitBtn">Update</button>
                        <button type="submit" class="btn btn-primary btn-sm saveBtn submitBtn">Save</button>
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addUsertoUserGroup_model" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="">Add User</h4>
                </div>
                <form role="form" id="addUserToGroup_form" class="form-horizontal">
                    <div class="modal-body">
                        <div class="row">
                            <div class="table-responsive">
                                <table id="user_table" class="<?php echo table_class(); ?>">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 10%">Emp Code</th>
                                        <th style="width: 10%">Emp Name</th>
                                        <th style="width: 10%">Emp Designation</th>
                                        <th style="width: 5%">
                                            <button type="button" data-text="Add" onclick="add_users()"
                                                    class="btn btn-xs btn-primary">
                                                <i class="fa fa-plus" aria-hidden="true"></i> Add User
                                            </button>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="table_employee">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript"
            src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
    <script type="text/javascript">
        var modal_title = $("#counterCreate_modal_title");
        var userGroupCreate_model = $("#userGroupCreate_model");
        var userGroup_form = $("#userGroup_form");
        var usergroup_table = '';
        var selectedItemsSync = [];
        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/pos/usergroups', 'Test', 'POS');
            });
            load_user_group();

            userGroup_form.bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    description: {validators: {notEmpty: {message: 'Description is required.'}}}
                },
            }).on('success.form.bv', function (e) {
                $('.submitBtn').prop('disabled', false);
                e.preventDefault();
                var $form = $(e.target);
                var data = $form.serializeArray();
                var requestUrl = $('#requestLink').val();
                save_update(data, requestUrl);

            });
        });

        function load_user_group() {
            usergroup_table = $('#usergroup_table').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "sAjaxSource": "<?php echo site_url('Pos/fetch_usergroup'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnDrawCallback": function (oSettings) {
                    if (oSettings.bSorted || oSettings.bFiltered) {
                        for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                            $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                        }
                    }
                    $("[name='userGroupIsactive']").bootstrapSwitch();
                    $("[rel=tooltip]").tooltip();
                },
                "aoColumns": [
                    {"mData": "userGroupMasterID"},
                    {"mData": "description"},
                    {"mData": "Active"},
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

        function userGroupIsactive(userGroupMasterID) {
            var compchecked = 0;
            if ($('#userGroupIsactive_' + userGroupMasterID).is(":checked")) {
                compchecked = 1;
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {userGroupMasterID: userGroupMasterID, chkedvalue: compchecked},
                    url: "<?php echo site_url('Pos/update_usergroup_isactive'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert('s', data['message']);
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });

            }
            else if (!$('#userGroupIsactive_' + userGroupMasterID).is(":checked")) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {userGroupMasterID: userGroupMasterID, chkedvalue: 0},
                    url: "<?php echo site_url('Pos/update_usergroup_isactive'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert('s', data['message']);
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        }

        function open_newUserGroupModel() {
            userGroup_form[0].reset();
            userGroup_form.bootstrapValidator('resetForm', true);
            modal_title.text('New User Group');
            userGroupCreate_model.modal({backdrop: "static"});
            $('.submitBtn').prop('disabled', false);
            btnHide('saveBtn', 'updateBtn');
        }

        $('.submitBtn').click(function () {
            if ($(this).hasClass('updateBtn')) {
                $('#requestLink').val('<?php echo site_url('pos/update_userGroup'); ?>');
            } else {
                $('#requestLink').val('<?php echo site_url('pos/save_userGroup'); ?>');
            }
        });

        function save_update(data, requestUrl) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: requestUrl,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        userGroupCreate_model.modal("hide");
                        setTimeout(function () {
                            usergroup_table.ajax.reload();
                        }, 300);
                        load_user_group();
                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }


        function editUserGroupDetail(userGroupMasterID, description, coName, wareHouseID) {
            userGroup_form[0].reset();
            userGroup_form.bootstrapValidator('resetForm', true);
            modal_title.text('Edit User Group');
            userGroupCreate_model.modal({backdrop: "static"});
            $('#description').val(description);
            $('#userGroupMasterID').val(userGroupMasterID);
            btnHide('updateBtn', 'saveBtn');
        }

        function btnHide(btn1, btn2) {
            $('.' + btn1).show();
            $('.' + btn2).hide();
        }

        function addUserToGroup(userGroupMasterID) {
            $("#addUsertoUserGroup_model").modal({backdrop: "static"});
            $('#userGroupMasterID').val(userGroupMasterID);
            selectedItemsSync = [];
            load_assigned_pos_user(userGroupMasterID);
            load_user_table()
        }

        function load_assigned_pos_user(userGroupMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {userGroupMasterID: userGroupMasterID},
                url: '<?php echo site_url('Pos/fetch_assigned_users'); ?>',
                beforeSend: function () {
                },
                success: function (data) {
                    if (!$.isEmptyObject(data)) {
                        $.each(data, function (index, value) {
                            selectedItemsSync.push(value["EIdNo"]);
                        });
                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

        function load_user_table() {
            usergroup_table = $('#user_table').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "sAjaxSource": "<?php echo site_url('Pos/fetch_user_for_group'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnDrawCallback": function (oSettings) {
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        x++;
                    }
                    if (selectedItemsSync.length > 0) {
                        $.each(selectedItemsSync, function (index, value) {
                            $("#empID_" + value).iCheck('check');

                            // $("#selectItem_" + value).prop("checked", true);
                        });
                    }
                    $('.extraColumns input').iCheck({
                        checkboxClass: 'icheckbox_square_relative-purple',
                        radioClass: 'iradio_square_relative-purple',
                        increaseArea: '20%'
                    });
                    $('input').on('ifChecked', function (event) {
                        ItemsSelectedSync(this);
                    });
                    $('input').on('ifUnchecked', function (event) {
                        ItemsSelectedSync(this);
                    });
                    $("[rel=tooltip]").tooltip();
                },
                "aoColumns": [
                    {"mData": "EIdNo"},
                    {"mData": "ECode"},
                    {"mData": "Ename2"},
                    {"mData": "DesDescription"},
                    {"mData": "action"}
                ],
                // "columnDefs": [{
                //     "targets": [5],
                //     "orderable": false
                // }],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "userGroupMasterID", "value": $("#userGroupMasterID").val()});
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

        function ItemsSelectedSync(item) {
            var value = $(item).val();
            if ($(item).is(':checked')) {
                var inArray = $.inArray(value, selectedItemsSync);
                if (inArray == -1) {
                    selectedItemsSync.push(value);
                }
            }
            else {
                var i = selectedItemsSync.indexOf(value);
                if (i != -1) {
                    selectedItemsSync.splice(i, 1);
                }
            }
        }

        function add_users() {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {userGroupMasterID: $("#userGroupMasterID").val(), empID: selectedItemsSync},
                url: "<?php echo site_url('Pos/save_usergroup_users'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $("#addUsertoUserGroup_model").modal("hide");
                        usergroup_table.ajax.reload();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

    </script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-10-04
 * Time: 2:31 PM
 */