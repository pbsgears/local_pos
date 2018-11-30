<style>
    .datepicker-days table thead th, td {
        border-radius: 0px !important;

    }
</style>
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div aria-hidden="true" role="dialog" id="rpos_Payment_sales_report2" class="modal fade"
     data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"> <?php echo $this->lang->line('posr_sales_report'); ?><!--Sales Report -->
                    <span id="title_paymentSales2"></span>
                </h4>
            </div>
            <div class="modal-body" style="min-height: 400px;">
                <form id="frm_salesReport2" method="post" class="form-inline text-center" role="form">
                    <input type="hidden" id="ps_outletID2" name="outletID" value="0">
                    <label for="" class="col-sm-2">
                        <?php echo $this->lang->line('common_filters'); ?><!--Filters--> </label>
                    <div class="form-group">

                        <label class="" for=""><?php echo $this->lang->line('posr_cashier'); ?><!--Cashier--> </label>
                        <?php echo form_dropdown('cashier[]', get_cashiers(), '', 'multiple required id="cashier2"  class="form-control input-sm"'); ?>
                    </div>


                    <div class="form-group">
                        <label class="" for=""><?php echo $this->lang->line('common_from'); ?><!--From--> </label>

                        <input type="text" required class="form-control input-sm startdateDatepic"
                               name="filterFrom" value="<?php echo date('d-m-Y 00:00:00') ?>"
                               style="width: 130px;"> <!-- id="filterFrom2" inputDate--> <!--data-date-end-date="0d"-->

                        <label class="" for=""><?php echo $this->lang->line('common_to'); ?><!--to--></label>
                        <!--inputDate-->
                        <input type="text" class="form-control input-sm startdateDatepic"
                               value="<?php echo date('d-m-Y H:i:s') ?>"
                               style="width: 130px;" name="filterTo"  placeholder="To"> <!--id="filterTo2"-->
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">
                        <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report--></button>
                </form>
                <hr>
                <div id="pos_modalBody_posPayment_sales_report2">

                </div>

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function (e) {


        /*$(".inputDate").datepicker({
            format: 'dd-mm-yyyy'
        });*/

        $("#cashier2").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true
        });
        $("#cashier2").multiselect2('selectAll', false);
        $("#cashier2").multiselect2('updateButtonText');
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
            //$(this).datetimepicker('hide');
        });
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
            url: "<?php echo site_url('Pos_restaurant/loadPaymentSalesReport2'); ?>",
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
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/loadPaymentSalesReport2'); ?>",
            data: $("#frm_salesReport2").serialize(),
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


</script>

