<?php
$date_format_policy = date_format_policy();
$from = convert_date_format($this->common_data['company_data']['FYPeriodDateFrom']);
$currency_arr = all_currency_new_drop();
$uom_arr = all_umo_new_drop();
$yield_arr = get_pos_yieldmaster();
$current_date = current_format_date();
$segment = fetch_mfq_segment(true);
$page_id = isset($page_id) && $page_id ? $page_id : 0;
$financeyear_arr = all_financeyear_drop(true);
$warehouse_arr = get_warehouse_yield_drop();
?>
<?php echo head_page($_POST["page_name"], false); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/typehead.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .entity-detail .ralign, .property-table .ralign {
        text-align: right;
        color: gray;
        padding: 3px 10px 4px 0;
        width: 150px;
        max-width: 200px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .title {
        color: #aaa;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .tddata {
        color: #333;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .nav-tabs > li > a {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
    }

    .nav-tabs > li > a:hover {
        background: rgb(230, 231, 234);
        font-size: 12px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        border-radius: 3px 3px 0 0;
        border-color: transparent;
    }

    .nav-tabs > li.active > a,
    .nav-tabs > li.active > a:hover,
    .nav-tabs > li.active > a:focus {
        color: #c0392b;
        cursor: default;
        background-color: #fff;
        font-weight: bold;
        border-bottom: 3px solid #f15727;
    }

    .arrow-steps .step.current {
        color: #fff !important;
        background-color: #657e5f !important;
    }

    .table-responsive {
        overflow: visible !important
    }

    .disabledbutton {
        pointer-events: none;
    }

</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-12">
        <div class="tab-content">
            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <?php echo fetch_account_review(true, true, 1); ?>
                </div>
                <div class="col-md-12 animated zoomIn" id="maincontent">
                    <form id="frm_yieldpreparation" class="frm_yieldpreparation" method="post">
                        <input type="hidden" id="yieldPreparationID" name="yieldPreparationID"
                               value="<?php echo $page_id ?>">
                        <header class="head-title">
                            <h2>Yield Preparation Header</h2>
                        </header>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-6 col-md-offset-0">
                                <div class="row" style="margin-top: 10px;">
                                    <div class="form-group col-sm-4">
                                        <label class="title">Yield </label>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <div class="input-req" title="Required Field">
                                            <?php echo form_dropdown('yieldMasterID', $yield_arr, '', 'onchange="getYieldUOM(this)" class="form-control select2" id="yieldMasterID"');
                                            ?>
                                            <span class="input-req-inner"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 10px;">
                                    <div class="form-group col-sm-4">
                                        <label class="title">Document Date </label>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <div class="input-req" title="Required Field">
                                            <!--<div class="input-group-addon"><i class="fa fa-calendar"></i></div>-->
                                            <div class='input-group date filterDate' id="">
                                                <input type='text' class="form-control"
                                                       name="documentDate"
                                                       id="documentDate"
                                                       value="<?php echo $current_date; ?>"
                                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                                                <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                                            </div>
                                            <span class="input-req-inner"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 10px;">
                                    <div class="form-group col-sm-4">
                                        <label class="title">Currency </label>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <div class="input-req" title="Required Field">
                                            <?php echo form_dropdown('currencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="currencyID" onchange="currency_validation(this.value,\'BOM\')" required disabled'); ?>
                                            <span class="input-req-inner"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 10px;">
                                    <div class="form-group col-sm-4">
                                        <label class="title">
                                            Financial Year</label>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <div class="input-req" title="Required Field">
                                            <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
                                            <span class="input-req-inner"></span></div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 10px;">
                                    <div class="form-group col-sm-4">
                                        <label class="title">
                                            Financial Period</label>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <div class="input-req" title="Required Field">
                                            <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
                                            <span class="input-req-inner"></span></div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="row" style="margin-top: 10px;">
                                    <div class="form-group col-sm-4">
                                        <label class="title">UOM </label>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <div class="input-req" title="Required Field">
                                            <?php echo form_dropdown('uomID', $uom_arr, "", 'class="form-control" id="uomID" required disabled'); ?>
                                            <span class="input-req-inner"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 10px;">
                                    <div class="form-group col-sm-4">
                                        <label class="title">Qty </label>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <div class="input-req" title="Required Field">
                                            <input type="text" name="qty" id="qty" class="form-control number"
                                                   data-qty=""
                                                   onkeypress="return validateFloatKeyPress(this,event)"
                                                   onfocus="this.select();" value="1">
                                            <span class="input-req-inner"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 10px;">
                                    <div class="form-group col-sm-4">
                                        <label class="title">Warehouse </label>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <div class="input-req" title="Required Field">
                                            <?php echo form_dropdown('warehouseAutoID', $warehouse_arr, "", 'class="form-control select2" id="warehouseAutoID" required'); ?>
                                            <span class="input-req-inner"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 10px;">
                                    <div class="form-group col-sm-4">
                                        <label class="title">Narration </label>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <div class="input-req" title="Required Field">
                                                            <textarea class="form-control" id="narration"
                                                                      name="narration" rows="3"></textarea>
                                            <span class="input-req-inner"></span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12 animated zoomIn">
                                <header class="head-title">
                                    <h2>Yield Item Detail</h2>
                                </header>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="mfq_customer_inquiry"
                                                   class="table table-condensed">
                                                <thead>
                                                <tr>
                                                    <th style="min-width: 12%">Item</th>
                                                    <th style="min-width: 12%">UOM</th>
                                                    <th style="min-width: 12%">Qty</th>
                                                    <th style="min-width: 12%">Unit Cost</th>
                                                    <th style="min-width: 12%">Total Cost</th>
                                                    <th style="min-width: 5%"></th>
                                                </tr>
                                                </thead>
                                                <tbody id="yield_item_body">
                                                <tr>
                                                    <td colspan="5">
                                                        <div class="search-no-results">PLEASE SELECT A YIELD.
                                                        </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <td colspan="4">
                                                        <div class="text-right">Total</div>
                                                    </td>
                                                    <td>
                                                        <div id="grand_total" style="text-align: right">0.00</div>
                                                    </td>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-sm-12 ">
                                <div class="pull-right">
                                    <button class="btn btn-primary" onclick="saveYieldPreparation(1)"
                                            type="button"
                                            id="submitBtn">
                                        Save
                                    </button>
                                    <button class="btn btn-primary" onclick="confirmYieldPreparation(2)"
                                            type="button"
                                            id="confirmBtn">
                                        Confirm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    var search_id = 1;
    var currency_decimal;
    $(document).ready(function () {
        $('.select2').select2();
        $('.filterDate').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        });
        $('.headerclose').click(function () {
            fetchPage('system/pos/settings/yield_preparation', '', 'Yield Preparation');
        });
        Inputmask().mask(document.querySelectorAll("input"));
        <?php
        if ($page_id) {
        ?>
        loadYieldPreparation('<?php echo $page_id  ?>');
        $('#yieldMasterID').prop('disabled', true);
        <?php
        }else{
        ?>
        $("#financeyear").trigger('onchange');
        <?php
        }
        ?>
        $('#qty').change(function () {
            calculateCostByQty($(this).val());
        });

        $('.review').removeClass('hide');
        var de_link = "<?php echo site_url('Double_entry/fetch_double_entry_yield_preparation'); ?>/" + <?php echo $page_id ?> +'/YPRP';
        $("#de_link").attr("href", de_link);
    });

    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyFinanceYearID': companyFinanceYearID},
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function (data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('Select  Financial Period'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                    });
                    if (select_value) {
                        $("#financeyear_period").val(select_value);
                    }
                    ;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function calculateCostByQty(gqty) {
        if ($('#yield_item_body tr').length > 0) {
            $('#yield_item_body tr').each(function () {
                var qty = parseFloat($('td', this).eq(2).find('.yQty').val());
                var masterQty = $('#qty').data('qty');
                var newQty = ((qty / masterQty) * gqty).toFixed(2);
                var unitCost = parseFloat($('td', this).eq(3).find('input').val());
                var totalCost = newQty * unitCost;
                $('td', this).eq(2).find('.totalQty').val(newQty);
                $('td', this).eq(4).find('.totalCost').text(commaSeparateNumber(totalCost, 2));
                $('td', this).eq(4).find('input').val(totalCost);
                calculateTotalCost();
            });
        }
    }

    function calculateLineTotalCost(element) {
        var qty = parseFloat($(element).closest('tr').find('.totalQty').val());
        var unitCost = parseFloat($(element).closest('tr').find('.localWacAmount').val());
        $(element).closest('tr').find('.totalCost').text(commaSeparateNumber((qty * unitCost), 2));
        $(element).closest('tr').find('.localWacAmountTotal').val(qty * unitCost);
        calculateTotalCost();
    }

    function calculateTotalCost() {
        var tot_gr = 0;
        if ($('#yield_item_body tr').length > 0) {
            $('#yield_item_body tr').each(function () {
                var tot_grand_value = parseFloat($('td', this).eq(4).find('.localWacAmountTotal').val());
                if (!isNaN(tot_grand_value)) {
                    tot_gr += tot_grand_value;
                }
            });
            $('#grand_total').text(commaSeparateNumber(tot_gr, 2));
        }
    }

    function load_yield_detail(yieldID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {yieldID: yieldID},
            url: "<?php echo site_url('POS_yield_preparation/load_yield_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#yield_item_body').html('');
                var i = 0;
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        var yieldDetailID = v.yieldDetailID;
                        var totalCost = parseFloat(v.unitCost) * v.qty;
                        var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="y_\'+yieldDetailID+\'"'), form_dropdown('yuomID[]', $uom_arr, '1', 'class="form-control yuomID"  disabled')) ?>';
                        $('#yield_item_body').append('<tr><td>' + v.itemDescription + ' <input type="hidden" class="itemAutoID" name="itemAutoID[]" value="' + v.itemAutoID + '"> <input type="hidden" class="form-control yieldDetailID" name="yieldDetailID[]" value="' + v.yieldDetailID + '"> <input type="hidden" class="form-control yieldPreperationDetailID" name="yieldPreperationDetailID[]" value=""> </td><td>' + uom + '</td> <td><input type="text" name="totalQty[]" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number totalQty" onchange="calculateLineTotalCost(this)" onfocus="this.select();" value="' + v.qty + '"><input type="hidden" name="yQty[]" class="yQty" value="' + v.qty + '"> </td> <td><input type="text" name="localWacAmount[]" onkeypress="return validateFloatKeyPress(this,event)"  class="form-control number localWacAmount" value="' + v.unitCost + '" readonly> </td><td style="text-align: right"><span class="totalCost">' + commaSeparateNumber(totalCost, 2) + '</span><input type="hidden" name="localWacAmountTotal[]" class=" localWacAmountTotal" value="' + totalCost + '"> </td> </tr>');
                        $('#y_' + v.yieldDetailID).val(v.uom);
                        i++;
                    });
                    calculateTotalCost();
                } else {
                    $('#yield_item_body').html('<tr><td colspan="5"><div class="search-no-results">PLEASE SELECT A YIELD.</div></td> </tr>');
                }
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_yield_preparation_detail(yieldPreparationID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {yieldPreparationID: yieldPreparationID},
            url: "<?php echo site_url('POS_yield_preparation/load_yield_preparation_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#yield_item_body').html('');
                var i = 0;
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        var yieldPreparationDetailID = v.yieldPreparationDetailID;
                        //var qty = parseFloat(v.qty) * parseFloat($('#qty').val());
                        var totalCost = parseFloat(v.localWacAmount) * parseFloat(v.totalQty);
                        var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="y_\'+yieldPreparationDetailID+\'"'), form_dropdown('yuomID[]', $uom_arr, '1', 'class="form-control yuomID"  disabled')) ?>';
                        $('#yield_item_body').append('<tr><td>' + v.itemDescription + ' <input type="hidden" class="itemAutoID" name="itemAutoID[]" value="' + v.itemAutoID + '"> <input type="hidden" class="form-control yieldDetailID" name="yieldDetailID[]" value="' + v.yieldDetailID + '"> <input type="hidden" class="form-control yieldPreparationDetailID" name="yieldPreparationDetailID[]" value="' + v.yieldPreparationDetailID + '"> </td><td>' + uom + '</td> <td><input type="text" name="totalQty[]" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number totalQty" onchange="calculateLineTotalCost(this)" onfocus="this.select();" value="' + v.totalQty + '"><input type="hidden" name="yQty[]" class="yQty" value="' + v.qty + '"> </td> <td><input type="text" name="localWacAmount[]" id="localWacAmount" onkeypress="return validateFloatKeyPress(this,event)"  class="form-control number localWacAmount" value="' + v.localWacAmount + '" readonly> </td><td style="text-align: right"><span class="totalCost">' + commaSeparateNumber(totalCost, 2) + '</span><input type="hidden" name="localWacAmountTotal[]" id="localWacAmountTotal" class=" localWacAmountTotal" value="' + totalCost + '"> </td> </tr>');
                        $('#y_' + v.yieldPreparationDetailID).val(v.uomID);
                        i++;
                    });
                    calculateTotalCost();
                } else {
                    $('#yield_item_body').html('<tr><td colspan="5"><div class="search-no-results">PLEASE SELECT A YIELD.</div></td> </tr>');
                }
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function loadYieldPreparation() {
        var yieldPreparationID = '<?php echo $page_id ?>';
        if (yieldPreparationID > 0) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("POS_yield_preparation/load_yieldPreparation"); ?>',
                dataType: 'json',
                data: {yieldPreparationID: yieldPreparationID},
                async: false,
                success: function (data) {
                    $("#yieldPreparationID").val(data['yieldPreparationID']);
                    $("#documentDate").val(data['documentDate']).change();
                    $("#narration").val(data['narration']);
                    $("#yieldMasterID").removeAttr('onchange');
                    $("#yieldMasterID").val(data['yieldMasterID']).change();
                    $("#yieldMasterID").attr('onchange', "getYieldUOM(this)");
                    $("#uomID").val(data['uomID']);
                    $("#qty").val(data['qty']);
                    $("#qty").data('qty', data['qty']);
                    $("#currencyID").val(data['transactionCurrencyID']);
                    $("#financeyear").val(data['companyFinanceYearID']).change();
                    $("#warehouseAutoID").val(data['warehouseAutoID']).change();
                    if (data['confirmedYN'] == 1) {
                        $("#confirmBtn").hide();
                        $("#submitBtn").hide();
                        $('#maincontent').addClass('disabledbutton')
                    } else {
                        $("#confirmBtn").show();
                        $("#submitBtn").show();
                        $('#maincontent').removeClass('disabledbutton')
                    }
                    setTimeout(function () {
                        $("#financeyear_period").val(data['companyFinancePeriodID']).change();
                    }, 500);

                    load_yield_preparation_detail(yieldPreparationID);
                    /*if (data['confirmedYN'] == 2) {
                     $('#confirmBtn').show();
                     } else {
                     $('#confirmBtn').hide();
                     }*/
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', xhr.responseText);
                }
            });
        }
    }

    function saveYieldPreparation(type) {
        $("#currencyID").prop('disabled', false);
        $("#uomID").prop('disabled', false);
        $(".yuomID").prop('disabled', false);
        var yieldPreparationID = $("#yieldPreparationID").val();
        if (parseInt(yieldPreparationID)) {
            $('#yieldMasterID').prop('disabled', false)
        }
        var data = $("#frm_yieldpreparation").serializeArray();
        data.push({'name': 'status', 'value': type});
        $.ajax({
            url: "<?php echo site_url('POS_yield_preparation/save_yieldPreparation'); ?>",
            type: 'post',
            data: data,
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                $("#currencyID").prop('disabled', true);
                $("#uomID").prop('disabled', true);
                $(".yuomID").prop('disabled', true);
                if (parseInt(yieldPreparationID)) {
                    $('#yieldMasterID').prop('disabled', true)
                }
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    if (type == 2) {
                        $('.headerclose').trigger('click');
                    } else {
                        $("#yieldPreparationID").val(data[2]);
                        load_yield_preparation_detail(data[2]);
                        var link = '<?php echo site_url('Double_entry/fetch_double_entry_yield_preparation'); ?>/'+data[2]+'/YPRP';
                        $('#de_link').attr('href',link);
                    }
                    $('#yieldMasterID').prop('disabled', true)
                }else{
                    if (parseInt(yieldPreparationID)) {
                        $('#yieldMasterID').prop('disabled', true)
                    }else{
                        $('#yieldMasterID').prop('disabled', false)
                    }
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
                myAlert('e', xhr.responseText);
            }
        });
    }

    function confirmYieldPreparation(type) {
        swal({
                title: "Are you sure?",
                text: "You want to confirm!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {
                $("#currencyID").prop('disabled', false);
                $("#uomID").prop('disabled', false);
                $(".yuomID").prop('disabled', false);
                var yieldPreparationID = $("#yieldPreparationID").val();
                if (parseInt(yieldPreparationID)) {
                    $('#yieldMasterID').prop('disabled', false)
                }
                var data = $("#frm_yieldpreparation").serializeArray();
                data.push({'name': 'status', 'value': type});
                $.ajax({
                    url: "<?php echo site_url('POS_yield_preparation/save_yieldPreparation'); ?>",
                    type: 'post',
                    data: data,
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        $("#currencyID").prop('disabled', true);
                        $("#uomID").prop('disabled', true);
                        $(".yuomID").prop('disabled', true);
                        if (parseInt(yieldPreparationID)) {
                            $('#yieldMasterID').prop('disabled', true)
                        }
                        $('.itemAutoID').closest('tr').css("background-color", 'white');
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            if (type == 2) {
                                $('.headerclose').trigger('click');
                            } else {
                                $("#yieldPreparationID").val(data[2]);
                                load_yield_preparation_detail(data[2]);
                            }
                            $('#yieldMasterID').prop('disabled', true)
                        }else if(data[0] == 'w'){
                            if (type == 2) {
                                $.each(data[2], function (index, value) {
                                    $('.itemAutoID[value=' + value + ']').closest('tr').css("background-color", '#ffb2b2');
                                });
                            }
                        }else{
                            if (parseInt(yieldPreparationID)) {
                                $('#yieldMasterID').prop('disabled', true)
                            }else{
                                $('#yieldMasterID').prop('disabled', false)
                            }
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });

    }

    function getNumberAndValidate(thisVal, dPlace=2) {
        thisVal = $.trim(thisVal);
        thisVal = removeCommaSeparateNumber(thisVal);
        thisVal = thisVal.toFixed(dPlace);
        if ($.isNumeric(thisVal)) {
            return parseFloat(thisVal);
        }
        else {
            return parseFloat(0);
        }
    }

    function validateFloatKeyPress(el, evt) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1].length > (currency_decimal - 1))) {
            return false;
        }
        return true;
    }

    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }

    function clearitemAutoID(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('.itemAutoID').val('');
        }
    }

    function getYieldUOM(element) {
        var uom = element.value.split("-");
        $('#uomID').val(uom[1]).change();
        $('#qty').val(uom[2]);
        $('#qty').data('qty', uom[2]);
        load_yield_detail(uom[0]);
    }

</script>
