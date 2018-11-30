<?php echo head_page('Gift Card  Master',false);
$locations = load_pos_location_drop();
$promotions = promotion_policies_drop();
//echo '<pre>';print_r($promotions);echo '<pre>';

?>
<style type="text/css">
    .checkbox-inline.no_indent, .checkbox-inline.no_indent+.checkbox-inline.no_indent {
      margin-left: 0;
      margin-right: 10px;
    }
    .checkbox-inline.no_indent:last-child { margin-right: 0; }
</style>

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
        </tbody>
    </table>
</div>
<div class="col-md-3 pull-right">
    <button type="button" onclick="open_newCounterModel()" class="btn btn-primary btn-sm pull-right" ><i class="fa fa-plus"></i> Create New </button>
    <button type="button" onclick="newPromotion()" class="btn btn-primary btn-sm pull-right" ><i class="fa fa-plus"></i> Create New2 </button>
</div>
</div>
<hr>

<div class="table-responsive">
    <table id="counterMaster_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="width: 5%">#</th>
            <th style="width: 25%">Description</th>
            <th style="width: 10%">Type</th>
            <th style="width: 10%">From Date</th>
            <th style="width: 10%">End Date</th>
            <th style="width: 30%">Warehouses</th>
            <th style="width: auto">All Warehouses</th>
            <th style="width: 15%">&nbsp;</th>
        </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="counterCreate_model" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title" id="counterCreate_modal_title"></h3>
            </div>
            <form role="form" id="promotion_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="promoType">Promotion Type</label>
                            <div class="col-sm-6">
                                <select class="form-control" id="promoType" name="promoType">
                                    <option value="">Select a Promotion Type</option>
                                    <?php
                                    foreach($promotions as $promo){
                                        echo '<option value="'.$promo['promotionTypeID'].'">'.$promo['Description'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="warehouses">Warehouse</label>
                            <div class="col-sm-6">
                                <div style="border: 1px solid #d2d6de; padding-left: 10px;">
                                    <label class="checkbox-inline no_indent" style="padding-bottom: 5px">
                                        <input type="checkbox" name="isApplicableForAllItem" value="1" id="isApplicableForAllItem"> For All Warehouse
                                    </label>
                                </div>
                                <div style="border: 1px solid #d2d6de; max-height: 110px; overflow-y: scroll; padding-left: 10px;">
                                    <?php
                                     foreach($locations as $loc){
                                        echo '<label class="checkbox-inline no_indent">
                                                  <input type="checkbox" name="warehouses[]" value="'.$loc['wareHouseAutoID'].'" class="warehouseChecks">
                                                  '.$loc['wareHouseCode'].'-'.$loc['wareHouseLocation'].'
                                              </label><br>';
                                     }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="promotionDescr">Description</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="promotionDescr" name="promotionDescr">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="fromDate">From Date</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="fromDate" value="<?php echo date('Y-m-d'); ?>" id="fromDate"
                                    class="form-control dateFields datepicker" required="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="endDate">End Date</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="endDate" value="<?php echo date('Y-m-d'); ?>" id="endDate"
                                    class="form-control dateFields datepicker" required="">
                                </div>
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
    var counterCreate_model = $("#counterCreate_model");
    var promotion_form = $("#promotion_form");
    var counterMaster_table;
    var warehouseChecks = $('.warehouseChecks');
    var startDate;
    var endDate;


    $( document ).ready(function() {
        $('.dateFields').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function(ev){
            $(this).datepicker('hide');
            var thisDate = $(this).attr('id');

            if( thisDate == 'fromDate'){
                startDate =  $(this).val();
            }
            if( thisDate == 'endDate'){
                endDate =  $(this).val();
            }

            promotion_form.bootstrapValidator('revalidateField', 'fromDate');
            promotion_form.bootstrapValidator('revalidateField', 'endDate');
        });

        load_counterDetails();

        promotion_form.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                promoType: {validators: {notEmpty: {message: 'Promotion type is required.'}}},
                promotionDescr: {validators: {notEmpty: {message: 'Promotion description is required.'}}},
                'warehouses[]': {
                    validators: {
                        choice: {
                            min: 1,
                            message: 'Please choose at least one warehouse'
                        }
                    }
                },
                fromDate: {
                    validators: {
                        date: {
                            message: 'End date is required',
                            format: 'YYYY-MM-DD'
                        },
                        callback: {
                            message: 'From date must be laser than end date',
                            callback: function(value, validator) {
                                var m = new moment(value, 'YYYY-MM-DD', true);
                                if (!m.isValid()) {
                                    return false;
                                }
                                // Check if the date in our range
                                return m.isBefore(endDate) || m.isSame(endDate);
                            }
                        }
                    }
                },
                endDate: {
                    validators: {
                        date: {
                            message: 'End date is required',
                            format: 'YYYY-MM-DD'
                        },
                        callback: {
                            message: 'End date must be greter than from date',
                            callback: function(value, validator) {
                                var m = new moment(value, 'YYYY-MM-DD', true);
                                if (!m.isValid()) {
                                    return false;
                                }
                                // Check if the date in our range
                                return m.isAfter(startDate) || m.isSame(startDate);
                            }
                        }
                    }
                }
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

    $('#isApplicableForAllItem12').change(function(){
        if( $(this).prop('checked') == true ){
            $('.warehouseChecks').prop('checked', true);
        }else{
            $('.warehouseChecks').prop('checked', false);
        }
        promotion_form.bootstrapValidator('revalidateField', 'warehouses[]');
    });

    function load_counterDetails() {
        counterMaster_table = $('#counterMaster_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Pos/fetch_promotions'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "promotionID"},
                {"mData": "masterDes"},
                {"mData": "typeDes"},
                {"mData": "dateFrom"},
                {"mData": "dateTo"},
                {"mData": "wareHouse"},
                {"mData": "applicableWarehouses"},
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
            $('#requestLink').val('<?php echo site_url('pos/update_promotion'); ?>');
        }else{
            $('#requestLink').val('<?php echo site_url('pos/new_promotion'); ?>');
        }
    });

    function open_newCounterModel() {
        commonInModal_open();
        modal_title.text('New Coupons');
        counterCreate_model.modal({backdrop: "static"});
        $('.submitBtn').prop('disabled', false);
        btnHide('saveBtn', 'updateBtn');
    }

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
                    counterCreate_model.modal("hide");
                    setTimeout(function(){
                        counterMaster_table.ajax.reload();
                    }, 300);


                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function editCounterDetail(editID, coCode, coName, wareHouseID ){
        commonInModal_open();
        modal_title.text('Edit Coupons');
        counterCreate_model.modal({backdrop: "static"});
        $('#wareHouseID').val(wareHouseID);
        $('#counterCode').val(coCode);
        $('#counterName').val(coName);
        $('#updateID').val( editID );
        btnHide('updateBtn', 'saveBtn');

    }

    function commonInModal_open(){
        promotion_form[0].reset();
        promotion_form.bootstrapValidator('resetForm', true);
        $('#isConform').val(0);
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

    function newPromotion(){
        fetchPage("system/pos/promotion_create",15,"Promotions","PO");
    }

</script>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-10-10
 * Time: 12:24 PM
 */