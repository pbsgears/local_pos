<div class="modal fade pddLess" data-backdrop="static" id="kot_Modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="min-width: 60%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo $this->lang->line('pos_config_kot_kot'); ?><!--KOT--></h4>
            </div>
            <div class="modal-body" style="min-height: 200px; ">
                <button type="button" id="showaddKOT" onclick="showaddKOT()" style="margin-bottom: 2px;"
                        class="btn btn-primary btn-xs pull-right"><i class="fa fa-plus" aria-hidden="true"></i>
                    <?php echo $this->lang->line('common_add'); ?><!--Add-->
                </button>


                <form role="form" id="kotLocation_form" class="form-group">
                    <div id="addKOT" style="background-color: #f4f4f4; display: none;">
                        <div class="row" style="padding-left: 2%;padding-right: 2%;">
                            <input type="hidden" class="form-control" id="kitchenLocationID_kot"
                                   name="kitchenLocationID" value="0">
                            <input type="hidden" class="form-control" id="wareHouseAutoID_kot" name="segmentConfigID"
                                   value="0"/>

                            <div class="form-group col-sm-4">
                                <label class="lbl">
                                    <?php echo $this->lang->line('common_description'); ?><!--Description -->
                                </label>
                                <input type="text" class="form-control" id="description_kot" name="description">
                            </div>

                        </div>

                        <div class="row" style="padding-left:84%;">
                            <button onclick="closeAddKOT()" class="btn btn-default btn-xs" type="button">
                                <?php echo $this->lang->line('common_Close'); ?><!--Close-->
                            </button>
                            <button type="submit" id="crewSaveKOT" class="btn btn-primary btn-xs">
                                <i class="fa fa-floppy-o"
                                   aria-hidden="true"></i>
                                <?php echo $this->lang->line('common_save_change'); ?><!--Save Changes-->
                            </button>
                        </div>
                    </div>
                </form>
                <table class="<?php echo table_class_pos(1) ?>" id="tbl_kotLocation" style="font-size:12px;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_description'); ?><!--Description --></th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                </table>


            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<script>

    $(document).ready(function (e) {
        $('#kotLocation_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            fields: {
                description: {validators: {notEmpty: {message: 'Description is required.'}}}
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
                url: "<?php echo site_url('Pos_config/save_kotLocation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#crewSaveBtn").prop('disabled', false);
                    if (data['error'] == 0) {
                        load_kot_location($("#wareHouseAutoID_kot").val())
                        $('#kotLocation_form').bootstrapValidator('resetForm', true);
                        $('#kotLocation_form')[0].reset();
                        myAlert('s', data['message']);
                    } else if (data['error'] == 1) {
                        myAlert('e', data['message']);
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });
    });

    function closeAddKOT() {
        $("#addKOT").hide('slow');
        $("#showaddKOT").show();
    }

    function showaddKOT() {
        $("#crew_edit_hn").val('');
        $('#kotLocation_form')[0].reset();
        $('#kotLocation_form').bootstrapValidator('resetForm', true);
        $("#addKOT").show('slow');
        $("#showaddKOT").hide();
    }


    function load_pos_kot(id) {
        $('#kot_Modal').modal('show');
        $('#wareHouseAutoID_kot').val(id);
        load_kot_location(id);
    }

    function load_kot_location(id) {
        $('#tbl_kotLocation').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/loadKitchenLocation_table'); ?>",
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
                {"mData": "kitchenLocationID"},
                {"mData": "description"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'outletID', 'value': id});
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

    function delete_pos_kotLocation(outletID) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("Pos_config/delete_pos_kotLocation"); ?>',
                    dataType: 'json',
                    data: {'outletID': outletID},
                    success: function (data) {
                        myAlert('s', data['message']);
                        load_kot_location($("#wareHouseAutoID_kot").val());
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            }
        });
    }


</script>