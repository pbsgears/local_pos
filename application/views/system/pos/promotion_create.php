<?php echo head_page('Add New Promotion',false);
$locations = load_pos_location_drop();
$promotions = promotion_policies_drop();

?>
<style type="text/css">
    .fixHeader_Div {
        height: 240px;
        border: 1px solid #c0c0c0;
    }

    div.fixHeader_Div::-webkit-scrollbar, div.smallScroll::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    div.fixHeader_Div::-webkit-scrollbar-track, div.smallScroll::-webkit-scrollbar-track  {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
    }

    div.fixHeader_Div::-webkit-scrollbar-thumb, div.smallScroll::-webkit-scrollbar-thumb  {
        margin-left: 30px;
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.5);
        width: 3px;
        position: absolute;
        top: 0px;
        opacity: 0.4;
        border-radius: 7px;
        z-index: 99;
        right: 1px;
        height: 10px;
    }

    #detailTB td{ vertical-align: middle; }

    #detailTB td input{
        height: 25px;
        font-size: 12px;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>

<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">Step 1 - Promotion Header</a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_itemDetails()" data-toggle="tab">Step 2 - Promotion Detail</a>
</div><hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('','role="form" id="promotion_form" autocomplete="off"'); ?>
            <div class="row" >
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="promoType">Promotion Type <?php  required_mark(); ?></label>
                        <select class="form-control" id="promoType" name="promoType" onchange="fetch_promo_details()">
                            <option value="">Select a Promotion Type</option>
                            <?php
                            foreach($promotions as $promo){
                                echo '<option value="'.$promo['promotionTypeID'].'">'.$promo['Description'].'</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">From Date <?php required_mark(); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="fromDate" value="<?php echo date('Y-m-d'); ?>" id="fromDate"
                                   class="form-control dateFields " required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="supplierID">End Date <?php  required_mark(); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="endDate" value="<?php echo date('Y-m-d'); ?>" id="endDate"
                                   class="form-control dateFields" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="isApplicableForAllItem">Is Applicable For All Items </label>
                        <div class="form-control">
                            <label class="radio-inline">
                                <input type="radio" name="isApplicableForAllItem" value="1" id="appY" >Yes
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="isApplicableForAllItem" value="0" id="appN" checked="checked">No
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="promotionDescr">Description <?php required_mark(); ?></label>
                        <input type="text" class="form-control" id="promotionDescr" name="promotionDescr">
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="warehouses">Warehouse <?php required_mark(); ?></label>
                        <div class="">
                            <div style="border: 1px solid #d2d6de; padding: 5px 10px;">
                                <label class="checkbox-inline no_indent" style="">
                                    <input type="checkbox" name="isApplicableForAllWarehouse" value="1" id="isApplicableForAllWarehouse"> For All Warehouse
                                </label>
                            </div>
                            <div style="border: 1px solid #d2d6de; height: 148px; overflow-y: scroll; padding-left: 10px;">
                                <?php
                                foreach($locations as $loc){
                                    echo '<label class="checkbox-inline no_indent">
                                              <input type="checkbox" name="warehouses[]" value="'.$loc['wareHouseAutoID'].'" class="warehouseChecks"
                                              id="wareHID_'.$loc['wareHouseAutoID'].'">
                                              '.$loc['wareHouseCode'].'-'.$loc['wareHouseLocation'].'
                                          </label><br>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <label for="">Promotion Setup <?php required_mark(); ?></label>
                    <div id="promoSetupDiv" style="height: 253px">
                        <div class="fixHeader_Div" style="max-width: 100%;">
                            <table class="<?php echo table_class(); ?>" id="detailTB" style="max-width:100%">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Start Range</th>
                                    <th>Discount %</th>
                                    <th>
                                        <button type="button" class="btn btn-primary btn-xs pull-right" onclick="addRow()">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td align="right">1</td>
                                    <td>
                                        <input type="text" name="range[]" id="" class="form-control number">
                                    </td>
                                    <td>
                                        <input type="text" name="discountPer[]" id="" class="form-control number">
                                    </td>
                                    <td align="center"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-right m-t-xs">
                <input type="hidden" id="requestLink" name="requestLink" >
                <input type="hidden" id="updateID" name="updateID" >

                <button class="btn btn-primary submitBtn" id="saveBtn" type="submit">Save</button>
                <button class="btn btn-primary submitBtn update" id="updateBtn" type="submit" style="display:none">Update</button>
            </div>
        <?php echo form_close();?>
    </div>

    <div id="step2" class="tab-pane"></div>
</div>


<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>


<script type="text/javascript">
    $( document ).ready(function() {
        var promotion_form = $("#promotion_form");
        var warehouseChecks = $('.warehouseChecks');
        var promo_ID = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        if (promo_ID) {
            fetch_promo_master(promo_ID);
            $('#saveBtn').hide();
            $('#updateBtn').show();
            $('.btn-wizard').removeClass('disabled');
        }else{
            $('.btn-wizard').addClass('disabled');
        }


        $('.dateFields').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function(){
            $(this).datepicker('hide');

            promotion_form.bootstrapValidator('revalidateField', 'fromDate');
            promotion_form.bootstrapValidator('revalidateField', 'endDate');
        });

        promotion_form.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                promoType: {validators: {notEmpty: {message: 'Promotion type is required.'}}},
                promotionDescr: {validators: {notEmpty: {message: 'Promotion description is required.'}}},
                isApplicableForAllItem: {validators: {notEmpty: {message: 'Promotion description is required.'}}},
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
                                var endDate = $('#endDate').val();
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
                                var startDate = $('#fromDate').val();
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

        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });
    });

    $('.submitBtn').click(function(){
        if( $(this).hasClass('updateBtn') ){
            $('#requestLink').val('<?php echo site_url('Pos/update_promotion'); ?>');
        }else{
            $('#requestLink').val('<?php echo site_url('Pos/new_promotion'); ?>');
        }
    });

    $('#isApplicableForAllWarehouse').change(function(){
        if( $(this).prop('checked') == true ){
            $('.warehouseChecks').prop('checked', true);
        }else{
            $('.warehouseChecks').prop('checked', false);
        }
        $('.warehouseChecks').change();
    });

    $('.warehouseChecks').change(function(){
        if( $(this).prop('checked') == true ){

            var i = 0;
            $('.warehouseChecks').each(function(){
                if( $(this).prop('checked') == true ){ i++; }
            });

            var totalChk = parseInt('<?php echo count($locations); ?>');
            if( totalChk == i){ $('#isApplicableForAllWarehouse').prop('checked', true); }

        }else{
            $('#isApplicableForAllWarehouse').prop('checked', false);
        }
    });

    function save_update(data, requestUrl){
        var error = 0;
        $('.setupColumn').each(function(){
            if( $.trim($(this).val()) == '' ){
                error++;
            }
        });

        if( error == 0 ){
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
                        //fetch_promo_details();
                        $('.btn-wizard').removeClass('disabled');
                        $('#updateID').val(data[2]);
                        /*$('[href=#step2]').tab('show');
                        fetch_itemDetails();*/
                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
        else{
            myAlert('e', 'Please fill out all promotion Setup columns');
        }

    }

    function fetch_promo_master(promo_ID){
        $('.submitBtn').addClass('updateBtn');
        $('#updateID').val(promo_ID);

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'promo_ID':promo_ID},
            url: '<?php echo site_url('pos/get_promotionMasterDet'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                var master = data['master'];
                var warehouses = data['warehouses'];

                $('.warehouseChecks').prop('checked', false);
                $('#promoType').val(master['promotionTypeID']);
                $('#promotionDescr').val(master['description']);
                $('#fromDate').datepicker('update',master['dateFrom']);
                $('#endDate').datepicker('update', master['dateTo']);


                if(master['isApplicableForAllItem'] == 1){ $('#appY').prop('checked', true); }
                else{ $('#appN').prop('checked', true); }

                var j=0; var totHouses = parseInt('<?php echo count($locations); ?>');
                $.each(warehouses, function (i, elm) {
                    $('#wareHID_'+elm['wareHID']).prop('checked', true);
                    j++;
                });

                if( totHouses == j ){ $('#isApplicableForAllWarehouse').prop('checked', true); }

                setTimeout(function(){
                    fetch_promo_details();
                }, 300);


            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }

    function fetch_promo_details(){
        var promoSetupDiv = $('#promoSetupDiv');
        var promoType = $('#promoType').val();

        if( promoType != '' ) {

            var promoID = $('#updateID').val();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'promo_ID': promoID, 'promoType': promoType},
                url: '<?php echo site_url('pos/load_promotion_template'); ?>',
                beforeSend: function () {
                    loadDiv();
                },
                success: function (data) {
                    setTimeout(function(){
                        promoSetupDiv.html(data);
                    }, 300);

                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    $('#loadingDiv').hide();
                }
            });
        }
    }

    function loadDiv(){
        var promoSetupDiv = $('#promoSetupDiv');
        var loadingDiv = '<div id="loadingDiv" style="padding: 30%;text-align: center;opacity: 0.7;background: black;height: 253px; color:#fff;">';
        loadingDiv += '<i class="fa fa-refresh fa-2x fa-spin" aria-hidden="true"></i> Loading </div>';

        promoSetupDiv.html('');
        promoSetupDiv.html(loadingDiv);
    }

    function stopLoadDiv(){
        $('#loadingDiv').hide();
    }

    function fetch_itemDetails(){
        var step2 = $('#step2');

        if ($('#appN').is(':checked')) {
            var promo_ID = $('#updateID').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'promo_ID': promo_ID},
                url: '<?php echo site_url('Pos/load_applicableItems'); ?>',
                beforeSend: function () {
                    startLoad();
                    step2.html();
                },
                success: function (data) {
                    stopLoad();
                    step2.html(data);
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
        else{
            step2.html('<div class="alert alert-danger"> This promotion is applicable for all items </div>');
        }
    }

</script>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-10-11
 * Time: 5:05 PM
 */