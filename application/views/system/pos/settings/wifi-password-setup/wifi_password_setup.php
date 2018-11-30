<?php
$locations = get_active_outlets_drop();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

?>
<link rel="stylesheet" href="<?php echo base_url('plugins/pos/pos-wifi-password.css'); ?>"/>
<div class="box box-warning">
    <div class="box-body">
        <div class="nav-tabs-custom">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="posContainer">
                        <h4 style="font-size:16px; font-weight: 800;">
                            <i class="fa fa-wifi text-orange" aria-hidden="true"></i>
                            Wifi Password Setup
                            <span class="btn btn-primary btn-xs pull-right" onclick="add_wifi_password_modal()"><i
                                    class="fa fa-plus"></i>
                                <?php echo $this->lang->line('common_update_add_new'); ?><!--Add New--></span>
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
                                    <?php echo form_dropdown('outletID', $locations, '', ' onchange="filter_by_outelet()" id="outletID" class="form-control select2"') ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                                <table id="wifi_password_table" class="<?php echo table_class(); ?>"
                                       style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th>wifi Password</th>
                                        <th>Assigned Outlet</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">

                                <div class="wifi-report-container">
                                    <h5>Available Password Count in each outlet </h5>
                                    <hr class="wifi-hr">
                                    <?php
                                    $activeOutlet = get_active_outletInfo();

                                    if (!empty($activeOutlet)) {
                                        foreach ($activeOutlet as $item) {
                                            $count = get_count_unused_wifi_password($item['wareHouseAutoID']);
                                            //$item['wareHouseAutoID']
                                            if($count>0){
                                                $cls = 'wifi-cart';
                                            }else {
                                                $cls = 'wifi-cart wifi-cart-red';
                                            }
                                            echo '<div class="'.$cls.'">' . $item['wareHouseDescription'] . $item['wareHouseCode'] . ' - <strong>' . $count . '</strong></div>';
                                        }
                                    }
                                    ?>

                                </div>

                            </div>
                        </div>


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
                <h4 class="modal-title" id="dynamic_modal_title"></h4>
            </div>
            <form role="form" method="post" id="wifi_password_form" name="wifi_password_form"
                  enctype="multipart/form-data" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">outlet</label>
                        <div class="col-sm-6">
                            <?php echo form_dropdown('outlet', $locations, '', 'id="outletID_modal" class="form-control select2"') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Files </label>
                        <div class="col-sm-6">
                            <input type="file" name="password_list" class="form-control" id="password_list">
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm updateBtn submitBtn">Update</button>
                    <button type="submit" class="btn btn-primary btn-sm saveBtn submitBtn">Save</button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="Exsisting_Password_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header  modal-header-mini" style="padding: 5px 10px; ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">Existing Passwords</h4>
            </div>
            <div class="modal-body" id="exsistingpwd">

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var modal_title = $("#dynamic_modal_title");
    var userGroupCreate_model = $("#userGroupCreate_model");
    var wifi_password_form = $("#wifi_password_form");
    var wifi_password_table = '';
    var selectedItemsSync = [];
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/pos/usergroups', 'Test', 'POS');
        });
        load_wifi_password_table();
        $('.select2').select2();
        $('#wifi_password_form').submit(function () {
            var formData = new FormData($("#wifi_password_form")[0]);

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: formData,
                url: "<?php echo site_url('Pos_config/save_wifi_password'); ?>",
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $("#userGroupCreate_model").modal('hide');
                        wifi_password_table.ajax.reload();
                    } else if (data[0] == 'e') {
                        debugger;
                        $('#exsistingpwd').empty();
                        if (jQuery.isEmptyObject(data[2])) {


                        } else {
                            $('#exsistingpwd').append('<div>Following passwords already exist, please change these password and try again.</div>');
                            $.each(data[2], function (key, value) {
                                $('#exsistingpwd').append('<ul><li>' + value + '</li></ul>');
                            });
                            $('#Exsisting_Password_modal').modal({backdrop: "static"});
                        }
                    }
                }, error: function (jqXHR, exception) {
                    var msg = '';
                    if (jqXHR.status === 0) {
                        msg = 'Not connect.\n Verify Network.';
                    } else if (jqXHR.status == 404) {
                        msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status == 500) {
                        msg = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') {
                        msg = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        msg = 'Time out error.';
                    } else if (exception === 'abort') {
                        msg = 'Ajax request aborted.';
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    myAlert('e', msg);
                    stopLoad();
                }
            });
            return false;
        });
    });

    function filter_by_outelet() {
        wifi_password_table.ajax.reload();
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

    function add_wifi_password_modal() {
        wifi_password_form[0].reset();
        $("#outletID_modal").val('').change();
        modal_title.text('Add wifi password');

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
                        wifi_password_table.ajax.reload();
                    }, 300);


                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }


    function editUserGroupDetail(userGroupMasterID, description, coName, wareHouseID) {
        wifi_password_form[0].reset();
        modal_title.text('Add wifi password');
        userGroupCreate_model.modal({backdrop: "static"});
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
        load_wifi_password_table()
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

    function load_wifi_password_table() {
        wifi_password_table = $('#wifi_password_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/load_wifi_password'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "wifiPassword"},
                {"mData": "wareHouseDescription"}
            ],

            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "outletID", "value": $("#outletID").val()});
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

    function save_wifi_password() {

    }

</script>