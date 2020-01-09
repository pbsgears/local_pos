<?php
/**
 * --- Created on 18-JAN-2018 by Mohamed Shafri
 * --- Table Selection Modal
 */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$this->lang->load('pos_config_restaurant', $primaryLanguage);

?>
<div aria-hidden="true" role="dialog" id="pos_salesReportModal" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-close text-red"></i>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-bar-chart"></i> Sales Report
                </h4>
            </div>
            <div class="modal-body" id="terminal_sales_report_body">
                <?php
                $locations = load_pos_location_drop();
                $currentUserWarehouseID = current_warehouseID();
                $primaryLanguage = getPrimaryLanguage();
                $currentUserWarehouseID = current_warehouseID();


                ?>
                <div class="row">
                    <form id="frm_salesReport2" method="post" class="form-inline text-center" role="form">
                        <input type="hidden" id="ps_outletID2" name="outletID" value="0">
                        <div class="col-md-3">
                            <div class="form-group">

                                <label class="" for="">
                                    <?php echo $this->lang->line('posr_cashier'); ?><!--Cashier--> </label>
                                <?php echo form_dropdown('cashier[]', get_cashiers_drp(), '', 'multiple required id="cashier2"  class="form-control input-sm"'); ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="" for="">
                                    <?php echo $this->lang->line('common_from'); ?><!--From--> </label>
                                <input type="hidden" id="tmpFromDate" value="">
                                <input type="hidden" id="tmpToDate" value="">
                                <input type="text" required class="form-control input-sm startdateDatepic"
                                       id="sr_fromDate"
                                       name="filterFrom" value="<?php echo date('d-m-Y 00:00:00') ?>"
                                       style="width: 130px;"> <!-- id="filterFrom2" inputDate-->
                                <!--data-date-end-date="0d"-->
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="" for=""><?php echo $this->lang->line('common_to'); ?><!--to--></label>
                                <input type="text" class="form-control input-sm startdateDatepic" id="sr_toDate"
                                       value="<?php echo date('d-m-Y 23:59:59') ?>"
                                       style="width: 130px;" name="filterTo" placeholder="To"> <!--id="filterTo2"-->
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report--></button>
                        </div>
                    </form>
                </div>

                <hr>
                <div id="pos_modalBody_posPayment_sales_report2" class="reportContainer" style="min-height: 200px;">
                    <div class="text-center" style="color:#9c9c9c; font-size:20px; margin-top:75px; "> Click on the
                        Generate
                        Report
                    </div>
                </div>


            </div>
            <div class="modal-footer" style="margin-top: 0px;">

                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?> </button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        $("#btn_pos_sales_report").click(function () {
            sales_report_init();
        })

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
            widgetPositioning: {}
        }).on('dp.change', function (ev) {
            $('#task_header_form').bootstrapValidator('revalidateField', 'startdate');
        });
    });


    function sales_report_init() {
        $("#pos_salesReportModal").modal('show');
        $("#pos_modalBody_posPayment_sales_report2").html('<div class="text-center" style="color:#9c9c9c; font-size:20px; margin-top:75px; "> Click on the Generate Report </div>');
    }

    function loadPaymentSalesReport_ajax2() {
        var data = $("#frm_salesReport2").serializeArray();
        data.push({'name': 'html', 'value': 'html'});
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/loadPaymentSalesReport2'); ?>",
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


</script>