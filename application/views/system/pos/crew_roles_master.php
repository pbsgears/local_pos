<?php
$locations = load_pos_location_drop();


$locations = load_pos_location_drop();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('posr_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('posr_master_crew_roles');
echo head_page($title, false);
/*echo '<pre>';print_r($locations);echo '<pre>';*/

?>
<div id="filter-panel" class="collapse filter-panel"></div>

<div class="col-md-3 pull-right">
    <button type="button" onclick="open_newRoleModel()" class="btn btn-primary btn-sm pull-right"><i
            class="fa fa-plus"></i><?php echo $this->lang->line('common_create_new'); ?><!-- Create New--> </button>
</div>
</div>
<hr>

<div class="table-responsive">
    <table id="crew_role_Master_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="width: 5%">#</th>
            <th><?php echo $this->lang->line('posr_master_role_description'); ?><!--Role Description--></th>
            <th>&nbsp;is Waiter <i class="fa fa-cutlery" aria-hidden="true"></i></th>
            <th style="width: 10%">&nbsp;</th>
        </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade pddLess" data-backdrop="static" id="crew_role_Modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="min-width: 30%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">
                    <?php echo $this->lang->line('posr_master_add_crew_roles'); ?><!--Add Crew Roles--></h4></div>
            <form role="form" id="crew_role_form" class="form-group">
                <input type="hidden" class="form-control" id="crew_role_id_hn" name="crew_role_id_hn">
                <div class="modal-body" style="min-height: 120px; ">
                    <div class="form-horizontal">
                        <fieldset>

                            <!-- Text input-->
                            <div class="form-group">
                                <label class="col-md-4 control-label">
                                    <?php echo $this->lang->line('common_description'); ?><!--Description--></label>
                                <div class="col-md-6">

                                    <input type="text" class="form-control" id="roleDescription"
                                           name="roleDescription">
                                </div>
                            </div>

                            <!-- Multiple Checkboxes -->
                            <div class="form-group">
                                <label class="col-md-4 control-label"> Is Waiter <i class="fa fa-cutlery"
                                                                                    aria-hidden="true"></i></label>
                                <div class="col-md-4">
                                    <div class="checkbox" style="margin-top: 7px;">
                                        <label for="checkboxes-0">
                                            <input type="checkbox" class="form-control" id="isWaiter" value="1"
                                                   name="isWaiter">
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </fieldset>
                    </div>


                    <div class="modal-footer" style="padding: 5px 10px;">
                        <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button type="button" onclick="save_cewRole();" class="btn btn-primary btn-xs"><i
                                class="fa fa-floppy-o"
                                aria-hidden="true"></i>
                            <?php echo $this->lang->line('posr_master_save_crew'); ?><!--Save Crew--></button>
                    </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">


    $(document).ready(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
            increaseArea: '20%' // optional
        });
        load_crew_roles_table();
        $('.headerclose').click(function () {
            fetchPage('system/pos/crew_roles_master', 'Test', 'POS');
        });

        /**$('#crew_role_form').bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    roleDescription: {validators: {notEmpty: {message: 'Description is required.'}}}
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Pos_config/save_crew_role_info'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert('s', data['message']);
                        $('#crew_role_Modal').modal('hide');
                        load_crew_roles_table();
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            });*/

    });

    function save_cewRole() {
        var data = $("#crew_role_form").serialize();
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Pos_config/save_crew_role_info'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    $('#crew_role_Modal').modal('hide');
                    load_crew_roles_table();
                } else if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
    function load_crew_roles_table() {
        $('#crew_role_Master_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/loadCrewRoles_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "roleDescription"},
                {"mData": "isWaiter_tmp"},
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

    function open_newRoleModel() {
        $("#crew_role_id_hn").val('');
        $("#roleDescription").val('');
        $('#crew_role_Modal').modal('show');
        $('#isWaiter').prop('checked', false).iCheck('update');
    }

    function edit_pos_crew_roles_config(id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Pos_config/edit_pos_crew_roles_config"); ?>',
            dataType: 'json',
            data: {'crewRoleID': id},
            success: function (data) {
                $('#roleDescription').val(data['roleDescription']);
                if (data['isWaiter'] == 1) {
                    $('#isWaiter').prop('checked', true).iCheck('update');
                } else {
                    $('#isWaiter').prop('checked', false).iCheck('update');
                }

                $('#crew_role_id_hn').val(id);
                $('#crew_role_Modal').modal('show');
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function delete_pos_crew_roles_config(id) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("Pos_config/delete_pos_crew_roles_config"); ?>',
                    dataType: 'json',
                    data: {'crewRoleID': id},
                    success: function (data) {
                        if (data['error'] == 0) {
                            myAlert('s', data['message']);
                            load_crew_roles_table();
                        } else {
                            myAlert('e', data['message']);
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        myAlert('e', ajaxOptions + thrownError);
                    }
                });
            }
        });
    }


</script>


