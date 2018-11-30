<?php echo head_page('Counter Master', false);
$locations = load_pos_location_drop();


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);


/*echo '<pre>';print_r($locations);echo '<pre>';*/

?>
    <div id="filter-panel" class="collapse filter-panel"></div>

    <div class="col-md-5">

    </div>
    <div class="col-md-3 pull-right">
        <button type="button" onclick="open_newCounterModel()" class="btn btn-primary btn-sm pull-right"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new'); ?><!--Create New--> </button>
    </div>
    </div>
    <hr>

    <div class="table-responsive">
        <table id="counterMaster_table" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 20%">
                    <?php echo $this->lang->line('pos_config_counter_code'); ?><!--Counter Code--></th>
                <th style="width: 30%">
                    <?php echo $this->lang->line('pos_config_counter_name'); ?><!--Counter Name--></th>
                <th style="width: 30%"><?php echo $this->lang->line('pos_config_outlets'); ?><!--Warehouse--></th>
                <th style="width: 10%">&nbsp;</th>
            </tr>
            </thead>
        </table>
    </div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
    <div class="modal fade" id="counterCreate_model" role="dialog" data-keyboard="false" data-backdrop="static">
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
                                    <?php echo $this->lang->line('pos_config_outlets'); ?><!--Warehouse--></label>
                                <div class="col-sm-6">
                                    <select class="form-control" id="wareHouseID" name="wareHouseID">
                                        <option value="">
                                            <?php echo $this->lang->line('pos_config_select_a_warehouse'); ?><!--Select a Warehouse--></option>
                                        <?php
                                        foreach ($locations as $loc) {
                                            echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . ' - ' . $loc['wareHouseDescription'] . ' - ' . $loc['wareHouseLocation'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('pos_config_counter_code'); ?><!--Counter Code--></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="counterCode" name="counterCode">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('pos_config_counter_name'); ?><!--Counter Name--></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="counterName" name="counterName">
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
        var counterCreate_model = $("#counterCreate_model");
        var counter_form = $("#counter_form");
        var counterMaster_table = '';


        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/pos/counter_master', 'Test', 'POS');
            });
            load_counterDetails();

            counter_form.bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    wareHouseID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('pos_config_warehouse_is_required');?>.'}}}, /*Warehouse is required*/
                    counterCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('pos_config_counter_code_is_required');?>.'}}}, /*Counter Code is required*/
                    counterName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('pos_config_Counter_name_is_required');?>.'}}}/*Counter Name is required*/
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

        function load_counterDetails() {
            counterMaster_table = $('#counterMaster_table').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "sAjaxSource": "<?php echo site_url('Pos/fetch_counters'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnDrawCallback": function (oSettings) {
                    if (oSettings.bSorted || oSettings.bFiltered) {
                        for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                            $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                        }
                    }
                },
                "aoColumns": [
                    {"mData": "counterID"},
                    {"mData": "counterCode"},
                    {"mData": "counterName"},
                    {"mData": "wareHouseColumn"},
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
                $('#requestLink').val('<?php echo site_url('pos/update_counterDetails'); ?>');
            } else {
                $('#requestLink').val('<?php echo site_url('pos/new_counter'); ?>');
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
                        counterCreate_model.modal("hide");
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

        function open_newCounterModel() {
            $('#isConform').val(0);
            counter_form[0].reset();
            counter_form.bootstrapValidator('resetForm', true);
            modal_title.text('New Counter');
            counterCreate_model.modal({backdrop: "static"});
            $('.submitBtn').prop('disabled', false);
            btnHide('saveBtn', 'updateBtn');
        }

        function editCounterDetail(editID, coCode, coName, wareHouseID) {

            counter_form[0].reset();
            counter_form.bootstrapValidator('resetForm', true);
            $('#isConform').val(0);
            modal_title.text('Edit Counter');
            counterCreate_model.modal({backdrop: "static"});
            $('#wareHouseID').val(wareHouseID);
            $('#counterCode').val(coCode);
            $('#counterName').val(coName);
            $('#updateID').val(editID);
            btnHide('updateBtn', 'saveBtn');

        }

        function btnHide(btn1, btn2) {
            $('.' + btn1).show();
            $('.' + btn2).hide();
        }

        function delete_counterDetails(id, code) {
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
                        data: {'counterID': id},
                        url: "<?php echo site_url('Pos/delete_counterDetails'); ?>",
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
 * Date: 2016-10-04
 * Time: 2:31 PM
 */