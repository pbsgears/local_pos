<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($this->input->post('page_name'), false); ?>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <button style="margin-right: 15px" type="button" class="btn btn-primary btn-xs pull-right "
            onclick="modal_employees_add()"> <?php echo $this->lang->line('config_create_user_group');?><!--Create UserGroup-->
    </button>
</div>
<hr style="margin-top: 10px">
<div class="row">
    <div class="col-sm-12" id="div_reload">
        <table id="table_nav_access" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="width: 10px">#</th>
                <th style=""><?php echo $this->lang->line('config_user_group');?><!--User Group--></th>
                <th style="width:100px"></th>
            </tr>
            </thead>
        </table>

    </div>
</div>

<div class="modal fade" id="bankTransactionModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_user_group');?><!--User Group--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <?php echo form_open('', 'role="form" id="save_employee_access"'); ?>
                <input type="hidden" id="userGroupIDEdit" name="userGroupID">
                <label class="" for=""><?php echo $this->lang->line('common_description');?><!--Description--></label>
                <input type="text" class="form-control" style="width: 200px" id="description" name="description">


                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" onclick="save_employees()" id="btnSave"><?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addWidgetModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_widget');?><!--Widget--></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <?php echo form_open('', 'role="form" id="save_widget"'); ?>
                <input type="hidden" class="form-control" id="userGroupIDWidget" name="userGroupIDWidget">
                <div id="widgetList" class="row"></div>


                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?> <!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" onclick="save_widget()" id="btnSave"><?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
            </div>
        </div>
    </div>
</div>


<?php

echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript"
        src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/navigation/erp_company_usergroup_setup', '', 'Company user group');
        });

        $('#userGroupID').select2();
        $('#gcompanyID').select2();
        var Otable;
        $('#userGroup').select2();
        $('#companyID').select2();
        /*   $('#empID').multiselect2({
         enableFiltering: true,
         filterBehavior: 'value',
         includeSelectAllOption: true
         });*/
        table_nav_access();


    });

    function searchfilter() {
        loadform();
    }

    function loaduserGroup() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {companyID: $('#gcompanyID').val()},
            url: "<?php echo site_url('Access_menu/loaduserGroupdropdown'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loaduserGroupdropdown').html(data);
                loadform();

            }, error: function () {

            }
        });

    }

    function save_employees() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: $('#save_employee_access').serializeArray(),
            url: "<?php echo site_url('Access_menu/save_company_usergroup'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                stopLoad();
                if (data['status']) {

                    Otable.ajax.reload();
                    $('#bankTransactionModal').modal('hide');
                }
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


    function loadform() {

        Otable.ajax.reload();
    }

    function loaddropdown() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {companyID: $('#companyID').val()},
            url: "<?php echo site_url('Access_menu/load_dropdown_unassigned_employees'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loaddropdown').html(data);
                loadform();

            }, error: function () {

            }
        });

    }
    function userGroupDropdown() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {companyID: $('#companyID').val()},
            url: "<?php echo site_url('Access_menu/load_userGroupdropDown'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#usergroup_dropdown').html(data);


            }, error: function () {

            }
        });

    }

    function table_nav_access() {
        window.Otable = $('#table_nav_access').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "recordsFiltered": 10,
            "sAjaxSource": "<?php echo site_url('Access_menu/load_companyusergroup'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            },
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
                {"mData": "userGroupID"},
                {"mData": "description"},
                {"mData": "edit"}
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

    function saveNavigationgroupSetup() {
        var navigationID = [];
        $('.nVal:checked').each(function (i, e) {
            navigationID.push(e.value);
        });
        navigationID = navigationID.join(',');

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {navigationID: navigationID, userGroupID: $('#userGroupID').val()},
            url: "<?php echo site_url('Access_menu/saveNavigationgroupSetup'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);

                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function modal_employees_add() {
        $('#userGroupIDEdit').val('');
        $('#description').val('');
        $('#bankTransactionModal').modal({backdrop: "static"});
    }

    function delete_item(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'employeeNavigationID': id},
                    url: "<?php echo site_url('Access_menu/delete_employee_navigation_access'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        Otable.ajax.reload();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

    function changeStatus(id) {
        var status = ( $('#status_' + id).is(":checked") ) ? 1 : 0;
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Access_menu/update_companyUsergroup'); ?>',
            data: {'userGroupID': id, 'status': status},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                Otable.ajax.reload();

            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        })
    }

    function openAddWidgetModel(usergroupID) {
        $('#userGroupIDWidget').val(usergroupID);
        loadWidget(usergroupID);
    }

    function loadWidget(usergroupID) {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Access_menu/loadWidet'); ?>",
            data: {"usergroupID": usergroupID},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#widgetList").html(data);
                $('#addWidgetModal').modal("show");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Message: ' + "No Widget");
            }
        });

    }

    function save_widget() {
        var data = $('#save_widget').serializeArray();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Access_menu/save_widget'); ?>",
            data: data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data[0] == 's') {
                    stopLoad();
                    $('#addWidgetModal').modal('hide');
                    myAlert('s', 'Message: ' + data[1]);
                    table_nav_access()
                } else if (data[0] == 'e') {
                    stopLoad();
                    myAlert('e', 'Message: ' + data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Message: ' + "Select Widget");
            }
        });

    }

/*    function editUserGroup(userGroupID,description){
        $('#userGroupIDEdit').val(userGroupID);
        $('#description').val('description');
        $('#bankTransactionModal').modal({backdrop: "static"});
    }*/

    function editUserGroup(userGroupID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'userGroupID': userGroupID},
            url: "<?php echo site_url('Access_menu/load_user_group'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#description').val(data['description']);
                    $('#userGroupIDEdit').val(userGroupID);
                    $('#bankTransactionModal').modal({backdrop: "static"});
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function deleteUserGroup(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {userGroupID: id},
                    url: "<?php echo site_url('Access_menu/deleteUserGroupID'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data[0] == 's') {
                            Otable.ajax.reload();
                        }
                        myAlert(data[0], data[1]);

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }


</script>