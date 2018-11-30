<?php echo head_page('User Counters',false);
$locations = load_pos_location_drop();
/*echo '<pre>';print_r($locations);echo '<pre>';*/

?>
<div id="filter-panel" class="collapse filter-panel"></div>

<div class="col-md-5">
    <table class="table table-bordered table-striped table-condensed ">
        <tbody><tr>
            <td>
                <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> Confirmed /Approved
            </td>
            <td>
                <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> Not Confirmed/ Not Approved
            </td>
            <td>
                <span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> Refer-back
            </td>
        </tr>
        </tbody></table>
</div>
<div class="col-md-3 pull-right">
    <button type="button" onclick="open_newUserCounterModel()" class="btn btn-primary btn-sm pull-right" ><i class="fa fa-plus"></i> Create New </button>
</div>
</div>
<hr>

<div class="table-responsive">
    <table id="userCounter_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="width: 5%">#</th>
            <th style="width: 10%">Code</th>
            <th style="width: 20%">Description</th>
            <th style="width: 20%">Warehouse</th>
            <th style="width: 20%">User</th>
            <th style="width: 10%">&nbsp;</th>
        </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="userCounter_model" role="dialog" data-keyboard="false" data-backdrop="static" >
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
                            <label class="col-sm-4 control-label">Warehouse</label>
                            <div class="col-sm-6">
                                <select class="form-control" id="wareHouseID" name="wareHouseID" onchange="load_counters(this)">
                                    <option value="">Select a Warehouse</option>
                                    <?php
                                    foreach($locations as $loc){
                                        echo '<option value="'.$loc['wareHouseAutoID'].'">'.$loc['wareHouseCode'].'-'.$loc['wareHouseLocation'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Counter</label>
                            <div class="col-sm-6">
                                <select class="form-control" id="counterID" name="counterID">
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">User</label>
                            <div class="col-sm-6">
                                <select class="form-control" id="userID" name="userID">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="requestLink" name="requestLink" >
                    <input type="hidden" id="updateID" name="updateID" >

                    <button type="submit" class="btn btn-primary btn-sm updateBtn submitBtn">Update</button>
                    <button type="submit" class="btn btn-primary btn-sm saveBtn submitBtn">Save</button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var modal_title = $("#counterCreate_modal_title");
    var userCounter_model = $("#userCounter_model");
    var counter_form = $("#counter_form");
    var userCounter_table = '';
    var counterID = $('#counterID');
    var userID = $('#userID');


    $( document ).ready(function() {
        load_counterDetails();

        counter_form.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                wareHouseID: {validators: {notEmpty: {message: 'Warehouse is required.'}}},
                counterID: {validators: {notEmpty: {message: 'Counter is required.'}}},
                userID: {validators: {notEmpty: {message: 'User is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            e.preventDefault();
            var $form      = $(e.target);
            var data       = $form.serializeArray();
            var requestUrl = $('#requestLink').val();
            save_update(data, requestUrl);

        });
    });

    function load_counterDetails() {
        userCounter_table = $('#userCounter_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Pos/fetch_user_counters'); ?>",
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
                {"mData": "wareHouse"},
                {"mData": "eName"},
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

    $('.submitBtn').click(function(){
        if( $(this).hasClass('updateBtn') ){
            $('#requestLink').val('<?php echo site_url('pos/update_counterDetails'); ?>');
        }else{
            $('#requestLink').val('<?php echo site_url('pos/new_counter'); ?>');
        }
    });

    function save_update(data, requestUrl){
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
                if(data[0] == 's'){
                    userCounter_model.modal("hide");
                    setTimeout(function(){
                        userCounter_table.ajax.reload();
                    }, 300);


                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function open_newUserCounterModel() {
        $('#isConform').val(0);
        counter_form[0].reset();
        counter_form.bootstrapValidator('resetForm', true);
        modal_title.text('User Counter');
        userCounter_model.modal({backdrop: "static"});
        $('.submitBtn').prop('disabled', false);
        btnHide('saveBtn', 'updateBtn');
    }

    function editCounterDetail(editID, coCode, coName, wareHouseID ){

        counter_form[0].reset();
        counter_form.bootstrapValidator('resetForm', true);
        $('#isConform').val(0);
        modal_title.text('Edit User Counter');
        userCounter_model.modal({backdrop: "static"});
        $('#wareHouseID').val(wareHouseID);
        $('#counterCode').val(coCode);
        $('#counterName').val(coName);
        $('#updateID').val( editID );
        btnHide('updateBtn', 'saveBtn');

    }

    function btnHide(btn1, btn2){
        $('.'+btn1).show();
        $('.'+btn2).hide();
    }

    function delete_counterDetails(id, code){
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: { 'counterID': id },
                    url: "<?php echo site_url('Pos/delete_counterDetails'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if(data[0] == 's'){
                            setTimeout(function(){
                                userCounter_table.ajax.reload();
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

    function load_counters(obj){
        if( obj.value != '' ){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'wareHouseID': obj.value},
                url: "<?php echo site_url('Pos/load_counters'); ?>",
                beforeSend: function () {
                    startLoad();
                    counterID.empty();
                    userID.empty();
                },
                success: function (data) {
                    stopLoad();
                    if(data){
                        counterID.append('<option value="">Select a Counter</option>');
                        $.each(data['counter'], function(i, val){
                            counterID.append('<option value="'+val['counterID']+'">'+val['counterCode']+' - '+val['counterName']+'</option>');

                        });

                        userID.append('<option value="">Select a User</option>');
                        $.each(data['users'], function(i, val){
                            userID.append('<option value="'+val['userID']+'">'+val['Ecode']+' - '+val['eName']+'</option>');

                        });
                    }
                    counter_form.bootstrapValidator('resetField', 'counterID');
                    counter_form.bootstrapValidator('resetField', 'userID');

                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

</script>



<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-10-05
 * Time: 9:55 AM
 */