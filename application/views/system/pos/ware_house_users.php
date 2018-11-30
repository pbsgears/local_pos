<?php
$locations = load_pos_location_drop();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('posr_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('posr_master_outlet');
echo head_page($title, false);
/*echo '<pre>';print_r($locations);echo '<pre>';*/
?>

    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="col-md-5">

    </div>
    <div class="col-md-3 pull-right">
        <button type="button" onclick="open_addEmpModel()" class="btn btn-primary btn-sm pull-right"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('posr_master_add_employee'); ?><!--Add Employee-->
        </button>
    </div>
    </div>


    <div class="table-responsive">
        <table id="counterMaster_table" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 20%"><?php echo $this->lang->line('posr_master_emp_code'); ?><!--Emp Code--></th>
                <th style="width: 30%">
                    <?php echo $this->lang->line('posr_master_employee_name'); ?><!--Employee Name--></th>
                <th style="width: 30%">Outlet<!--Warehouse-->
                    <!-- Outlet --></th>
                <th style="width: 10%">&nbsp;</th>
            </tr>
            </thead>
        </table>
    </div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

    <div class="modal fade" id="wareHouseUser_model" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title" id="counterCreate_modal_title"></h3>
                </div>
                <form role="form" id="counter_form" class="form-horizontal">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">

                                    <?php echo $this->lang->line('common_warehouse');?><!--Warehouse--></label>
                                <div class="col-sm-6">
                                    <select class="form-control" id="wareHouseID" name="wareHouseID">
                                        <option value="">
                                            <?php echo $this->lang->line('posr_master_select_a_warehouse'); ?><!--Select a Warehouse--></option>
                                        <?php
                                        foreach ($locations as $loc) {
                                            echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . ' - ' . $loc['wareHouseDescription'] .' - ' . $loc['wareHouseLocation'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('posr_master_employee'); ?><!--Employee--></label>
                                <div class="col-sm-6">
                                    <?php echo form_dropdown('employeeID', employees_pos_outlet_drop(), '', 'class="form-control" id="employeeID"'); ?>
                                    <!--<input type="text" class="form-control" id="employeeName" name="employeeName">
                                    <input type="hidden" id="employeeID" name="employeeID" value="">
                                    <input type="hidden" id="employeeCode" name="employeeCode" value="">-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="requestLink" name="requestLink">
                        <input type="hidden" id="updateID" name="updateID">

                        <button type="submit" class="btn btn-primary btn-sm updateBtn submitBtn">
                            <?php echo $this->lang->line('common_update'); ?><!--Update--></button>
                        <button type="submit" class="btn btn-primary btn-sm saveBtn submitBtn">
                            <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var modal_title = $("#counterCreate_modal_title");
        var wareHouseUser_model = $("#wareHouseUser_model");
        var counter_form = $("#counter_form");
        var counterMaster_table = '';
        var employeeName = $("#employeeName");


        $(document).ready(function () {
            $("#wareHouseID,#employeeID").select2();
            $('.headerclose').click(function () {
                fetchPage('system/pos/ware_house_users', 'Test', 'POS');
            });
            load_counterDetails();
            employeeSearch_typeHead();
            counter_form.bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    wareHouseID: {validators: {notEmpty: {message: 'Warehouse is required.'}}},
                    employeeID: {validators: {notEmpty: {message: 'Employee ID is required.'}}}
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

        function employeeSearch_typeHead() {
            var item = new Bloodhound({
                datumTokenizer: function (d) {
                    return Bloodhound.tokenizers.whitespace();
                },
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: "<?php echo site_url();?>/Pos/emp_search/?q=%QUERY"
            });

            item.initialize();
            employeeName.typeahead(null, {
                minLength: 3,
                highlight: true,
                displayKey: 'empName',
                source: item.ttAdapter(),
                templates: {
                    empty: [
                        '<div class="tt-suggestion"><div style="white-space: normal;">',
                        'unable to find any item that match the current query',
                        '</div></div>'
                    ].join('\n'),
                    suggestion: Handlebars.compile('<div><strong>{{ECode}}</strong> – {{empName}}</div>')
                }
            }).on('typeahead:selected', function (object, datum) {
                $('#employeeID').val(datum.EIdNo);
                $('#employeeCode').val(datum.ECode);
                counter_form.bootstrapValidator('revalidateField', 'employeeID');


            });
        }

        employeeName.keyup(function (e) {
            if (e.keyCode != 13) {
                $('#employeeID').val('');
                counter_form.bootstrapValidator('revalidateField', 'employeeID');
            }
        });

        function load_counterDetails() {
            counterMaster_table = $('#counterMaster_table').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "sAjaxSource": "<?php echo site_url('Pos/fetch_ware_house_user'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnDrawCallback": function (oSettings) {
                    if (oSettings.bSorted || oSettings.bFiltered) {
                        for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                            $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                        }
                    }
                },
                "aoColumns": [
                    {"mData": "userID"},
                    {"mData": "ECode"},
                    {"mData": "empName"},
                    {"mData": "wareHouseDescription"},
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

        $('.submitBtn').click(function () {
            if ($(this).hasClass('updateBtn')) {
                $('#requestLink').val('<?php echo site_url('pos/update_ware_house_user'); ?>');
            } else {
                $('#requestLink').val('<?php echo site_url('pos/add_ware_house_user'); ?>');
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
                        wareHouseUser_model.modal("hide");
                        setTimeout(function () {
                            counterMaster_table.ajax.reload();
                        }, 300);


                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

        function open_addEmpModel() {
            counter_form[0].reset();
            $('#wareHouseID').val('').change();
            $('#employeeID').val('').change();
            counter_form.bootstrapValidator('resetForm', true);
            modal_title.text('<?php echo $this->lang->line('posr_master_assign_employees_to_warehouse');?>');
            /*Assign Employees to warehouse*/
            wareHouseUser_model.modal({backdrop: "static"});
            $('.submitBtn').prop('disabled', false);
            btnHide('saveBtn', 'updateBtn');
        }

        function edit_wareHouseUsers(editID, userID, empName, wareHouseID) {

            counter_form[0].reset();
            counter_form.bootstrapValidator('resetForm', true);
            modal_title.text(' <?php echo $this->lang->line('posr_master_edit_warehouse_users');?>');
            /*Edit Warehouse Users*/
            wareHouseUser_model.modal({backdrop: "static"});
            $('#wareHouseID').val(wareHouseID).change();
            $('#employeeID').val(userID).change();
            //$("#wareHouseID,#employeeID").select2();
            $('#employeeName').val(empName);
            $('#updateID').val(editID);
            btnHide('updateBtn', 'saveBtn');
            //$('#wareHouseID').val(data['wareHouseID']).change();
            //$('#employeeID').val(data['employeeID']).change();

        }

        function btnHide(btn1, btn2) {
            $('.' + btn1).show();
            $('.' + btn2).hide();
        }

        function delete_wareHouseUsers(id, eCode, wLocation) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55 ",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'autoID': id},
                        url: "<?php echo site_url('Pos/delete_ware_house_user'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);

                            if (data[0] == 's') {
                                setTimeout(function () {
                                    counterMaster_table.ajax.reload();
                                }, 300);
                            }
                        }, error: function () {
                            myAlert('e', 'An Error Occurred! Please Try Again.');
                            stopLoad();
                        }
                    });
                }
            );
        }

    </script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-10-05
 * Time: 11:20 AM
 */