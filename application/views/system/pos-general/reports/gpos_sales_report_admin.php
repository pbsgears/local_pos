<?php echo head_page('<i class="fa fa-bar-chart"></i> Sales Report', false);
$locations = get_gpos_location();


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);


?>
<style>
    .reportContainer {
        border: 2px solid gray !important;
        padding: 10px !important;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>


<div>
    <div class="row">
        <form id="frm_salesReport2" method="post" class="form-inline text-center" role="form">
            <input type="hidden" id="ps_outletID2" name="outletID" value="0">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-3">
                        <label class="" for="">Outlet</label>
                        <select class=" filters" multiple required name="outletID_f[]" id="outletID_f"
                                onchange="loadCashier()">
                            <?php
                            foreach ($locations as $loc) {
                                echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . ' - ' . $loc['wareHouseDescription'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">

                            <label class="" for="">
                                <?php echo $this->lang->line('posr_cashier'); ?><!--Cashier--> </label>
                            <span id="cashier_option">
                                <?php echo form_dropdown('cashier[]', get_cashiers_gpos(), '', 'multiple required id="cashier2"  class="form-control input-sm"'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="" for=""><?php echo $this->lang->line('common_from'); ?><!--From--> </label>
                            <input type="hidden" id="tmpFromDate" value="">
                            <input type="hidden" id="tmpToDate" value="">
                            <input type="text" required class="form-control input-sm startdateDatepic" id="sr_fromDate"
                                   name="filterFrom" value="<?php echo date('d-m-Y 00:00:00') ?>"
                                   style="width: 130px;">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="" for=""><?php echo $this->lang->line('common_to'); ?><!--to--></label>
                            <input type="text" class="form-control input-sm startdateDatepic" id="sr_toDate"
                                   value="<?php echo date('d-m-Y 23:59:59') ?>"
                                   style="width: 130px;" name="filterTo" placeholder="To"> <!--id="filterTo2"-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="row col-sm-12" style="margin-top: 5px;">
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary btn-sm pull-right">
                        <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report--></button>
                </div>
            </div>
        </form>
    </div>

    <hr>
    <div id="pos_modalBody_posPayment_sales_report2" class="reportContainer" style="min-height: 200px;">
        <div class="text-center" style="color:#9c9c9c; font-size:20px; margin-top:75px; "> Click on the Generate
            Report
        </div>
    </div>


</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    $(document).ready(function (e) {

        $("#cashier2").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true
        });
        $("#cashier2").multiselect2('selectAll', false);
        $("#cashier2").multiselect2('updateButtonText');


        $("#outletID_f").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true
        });
        $("#outletID_f").multiselect2('selectAll', false);
        $("#outletID_f").multiselect2('updateButtonText');
        $("#frm_salesReport2").submit(function (e) {
            loadPaymentSalesReport_ajax2();
            return false;
        })


        $('.startdateDatepic').datetimepicker({
            showTodayButton: true,
            format: "DD/MM/YYYY hh:mm A",
            sideBySide: false,
            widgetPositioning: {
                /*horizontal: 'left',*/
                /*vertical: 'bottom'*/
            }
        }).on('dp.change', function (ev) {
            $('#task_header_form').bootstrapValidator('revalidateField', 'startdate');
        });

        loadCashier();


    })
    function loadPaymentSalesReport2() {
        var curDate = '<?php echo date('d-m-Y') ?>';
        var date = new Date,
            hour = date.getHours(),
            minute = date.getMinutes(),
            seconds = date.getSeconds(),
            ampm = hour > 12 ? "PM" : "AM";

        hour = hour % 12;
        hour = hour ? hour : 12; // zero = 12

        minute = minute > 9 ? minute : "0" + minute;
        seconds = seconds > 9 ? seconds : "0" + seconds;
        hour = hour > 9 ? hour : "0" + hour;


        date = curDate + ' ' + hour + ":" + minute + " " + ampm;

        $("#filterTo2").val(date);


        $("#ps_outletID2").val($("#wareHouseAutoID").val());
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/loadPaymentSalesReportAdmin'); ?>",
            data: {id: null},
            cache: false,
            beforeSend: function () {
                $("#title_paymentSales2").html(' - <span style="color:#de0303"><strong>' + $("#filterTxt").text() + '</strong></span>');
                $("#ps_outletID2").val($("#wareHouseAutoID").val());
                $("#rpos_Payment_sales_report2").modal('show');
                startLoadPos();
                $("#pos_modalBody_posPayment_sales_report2").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
            },
            success: function (data) {
                stopLoad();
                $("#pos_modalBody_posPayment_sales_report2").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function loadPaymentSalesReport_ajax2() {
        var data = $("#frm_salesReport2").serialize();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_general_report/load_gpos_PaymentSalesReportAdmin'); ?>",
            data: data,
            cache: false,
            beforeSend: function () {
                $("#pos_modalBody_posPayment_sales_report2").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
            },
            success: function (data) {
                $("#pos_modalBody_posPayment_sales_report2").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function loadCashier() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_general_report/get_gpos_outlet_cashier'); ?>",
            data: {warehouseAutoID: $('#outletID_f').val()},
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                if (!$.isEmptyObject(data)) {
                    $('#cashier_option').html(data);
                    $("#cashier2").multiselect2({
                        enableFiltering: true,
                        filterPlaceholder: 'Search Cashier',
                        includeSelectAllOption: true
                    });
                    $("#cashier2").multiselect2('selectAll', false);
                    $("#cashier2").multiselect2('updateButtonText');
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


</script>
