<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($this->input->post('page_name'), false); ?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-sm-12 form-inline">
        <div class="form-group">
            <label><?php echo $this->lang->line('common_company');?><!--Company--></label>
            <?php echo form_dropdown('companyID', Drop_down_group_of_companies(), '', 'class="" onchange="loaduserGroup(this.value)" id="gcompanyID"  required"'); ?>
        </div>
        <div class="form-group" id="loaduserGroupdropdown">
            <label><?php echo $this->lang->line('config_user_group');?><!--User Group--></label>
            <?php echo form_dropdown('userGroupID', dropdown_erp_usergroups(''), '', 'onchange="" class="" style="width:100px" id="userGroupID" required'); ?>
        </div>

        <div class="form-group" id="">
            <label></label>
            <button style="    margin-top: 24px;" type="button" class="btn btn-primary btn-xs"
                    onclick="loadform()"> <?php echo $this->lang->line('common_search');?><!--Search-->
            </button>
        </div>

        <!--<div class="form-group">
            <label class=" control-label " for="employeeID">Company
                :</label>

            <?php /*echo form_dropdown('companyID', Drop_down_group_of_companies(), '', 'class="" onchange="loaduserGroup(this.value)" id="gcompanyID"  required"'); */?>
        </div>-->

        <!--<div class="form-group" id="loaduserGroupdropdown">
            <label class=" control-label " style="    margin-top: 6px;" for="employeeID">User Group
                :</label>
            <?php /*echo form_dropdown('userGroupID', dropdown_erp_usergroups(''), '', 'onchange="" class="" style="width:100px" id="userGroupID" required'); */?>
            </d>
        </div>-->
<!--        <div class="form-group">
            <button style="" type="button" class="btn btn-primary btn-sm"
                    onclick="loadform()"> Search
            </button>

        </div>
-->        <button type="button" style="    margin-top: 24px;" class="btn btn-primary btn-sm pull-right "
                onclick="modal_employees_add()"> <?php echo $this->lang->line('config_add_employees');?><!--Add Employees-->
        </button>
    </div>
</div>
<hr style="margin-top: 10px">
<div class="row">
    <div class="col-sm-12" id="div_reload">
        <table id="table_nav_access" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="width: 20px">#</th>
                <th style="min-width: 20%"><?php echo $this->lang->line('config_emp_id');?><!--EmpID--></th>
                <th style="min-width: 30%"><?php echo $this->lang->line('common_employee_name');?><!--Employee Name--></th>
                <th style="min-width: 30%"><?php echo $this->lang->line('common_company');?><!--Company--></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('common_group');?><!--Group--></th>
                <th style="min-width: 20%"></th>
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
                <h4 class="modal-title"><?php echo $this->lang->line('config_navigation_access');?><!--Navigation Access--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <?php echo form_open('', 'role="form" id="save_employee_access"'); ?>
                        <div class="form-group"><label style="width: 100px"
                                                       for=""><?php echo $this->lang->line('common_company');?><!--Company--> </label> <?php echo form_dropdown('companyID', Drop_down_group_of_companies(), '', 'class="" onchange="loaddropdown(),userGroupDropdown()" id="companyID" style="width:250px" required"'); ?>
                        </div>
                        <div class="form-group " id="loaddropdown"></div>
                        <div class="form-group " id="usergroup_dropdown"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" onclick="save_employees()" id="btnSave"><?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
            </div>
        </div>
    </div>
</div>

<?php

echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    $(document).ready(function () {

        $('.headerclose').click(function(){
            fetchPage('system/navigation/erp_employee_navigation_access','','Employee Navigation Access');
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
        loaddropdown();
        userGroupDropdown();


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
            url: "<?php echo site_url('Access_menu/save_assigned_navigation_employees'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                stopLoad();

                if (data['status']) {
                    /*       loaddropdown();
                     userGroupDropdown();*/
                    $('#companyID').val('').change();
                    $('#empID').multiselect2('deselectAll', true);
                    $('#empID').multiselect2('refresh');

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
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "recordsFiltered": 10,
            "sAjaxSource": "<?php echo site_url('Access_menu/fetch_group_access_employee'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "employeeNavigationID"},
                {"mData": "empID"},

                {"mData": "emloyeeName"},
                {"mData": "company"},
                {"mData": "description"},
                {"mData": "edit"},
                {"mData": "description"}
            ],
            "columnDefs": [{"visible":false,"searchable": true,"targets": [6] }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "userGroup", "value": $('#userGroupID').val()});
                aoData.push({"name": "companyID", "value": $('#gcompanyID').val()});
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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
        $('#bankTransactionModal').modal({backdrop: "static"});
    }

    function delete_item(id) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
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


</script>