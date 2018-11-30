<style>
    .datepicker-days table thead th, td {
        border-radius: 0px !important;

    }
</style>
<?php
$outlets = get_active_outletInfo();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="rpos_Payment_sales_report" class="modal fade"
     data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"> <?php echo $this->lang->line('posr_sales_report'); ?><!--Sales Report -->
                    <!--<span id="title_paymentSales"></span>-->
                </h4>
            </div>
            <div class="modal-body" style="min-height: 400px;">
                <form id="frm_salesReport" method="post" class="form-inline text-center" role="form">
                    <input type="hidden" id="ps_outletID" name="outletID" value="0">
                    <label for="" class="col-sm-2">
                        <?php echo $this->lang->line('common_filters'); ?><!--Filters--> </label>
                    <div class="form-group">

                        <label class="" for="">Outlets</label>
                        <select class="form-control input-sm" name="outlet[]" id="outlet" onchange="loadCashier()" multiple>
                            <?php
                            foreach ($outlets as $outlet) {
                                echo '<option value="' . $outlet['wareHouseAutoID'] . '">' . $outlet['wareHouseCode'] . '-' . $outlet['wareHouseDescription'] . '-'. $outlet['wareHouseLocation']. '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">

                        <label class="" for=""><?php echo $this->lang->line('posr_cashier'); ?><!--Cashier--> </label>
                        <span id="cashier_option">
                            <?php echo form_dropdown('cashier[]', get_cashiers(), '', 'multiple required id="cashier"  class="form-control input-sm"'); ?>
                        </span>

                    </div>

                    <div class="form-group">
                        <label class="" for=""><?php echo $this->lang->line('common_from'); ?><!--From--> </label>

                        <input type="text" required class="form-control input-sm inputDate" data-date-end-date="0d"
                               name="filterFrom" id="filterFrom" value="<?php echo date('d-m-Y') ?>"
                               style="width: 79px;">

                        <label class="" for=""><?php echo $this->lang->line('common_to'); ?><!--to--></label>
                        <input type="text" class="form-control input-sm inputDate" data-date-end-date="0d"
                               value="<?php echo date('d-m-Y') ?>"
                               style="width: 79px;" name="filterTo" placeholder="To">
                    </div>

                    <button type="button" class="btn btn-primary btn-sm" onclick="loadPaymentSalesReport_ajax()">
                        <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report--></button>
                </form>
                <hr>
                <div id="pos_modalBody_posPayment_sales_report">

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
        $(".inputDate").datepicker({
            format: 'dd-mm-yyyy'
        });
        /*$("#cashier").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true
        });
        $("#cashier").multiselect2('selectAll', false);
        $("#cashier").multiselect2('updateButtonText');*/
        $("#outlet").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Search outlet',
            includeSelectAllOption: true
        });
        $("#outlet").multiselect2('selectAll', false);
        $("#outlet").multiselect2('updateButtonText');
        $("#frm_salesReport").submit(function (e) {
            loadPaymentSalesReport_ajax();
            return false;
        });
    });
    function loadPaymentSalesReport() {
        $("#ps_outletID").val($("#wareHouseAutoID").val());
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/loadPaymentSalesReport'); ?>",
            data: {id: null},
            cache: false,
            beforeSend: function () {
                $("#title_paymentSales").html(' - <span style="color:#de0303"><strong>' + $("#filterTxt").text()+'</strong></span>');
                $("#ps_outletID").val($("#wareHouseAutoID").val());
                $("#rpos_Payment_sales_report").modal('show');
                startLoadPos();
                $("#pos_modalBody_posPayment_sales_report").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
            },
            success: function (data) {
                stopLoad();
                $("#pos_modalBody_posPayment_sales_report").html(data);
                loadCashier();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function loadPaymentSalesReport_ajax() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/loadPaymentSalesReport'); ?>",
            data: $("#frm_salesReport").serialize(),
            cache: false,
            beforeSend: function () {
                $("#pos_modalBody_posPayment_sales_report").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
            },
            success: function (data) {
                $("#pos_modalBody_posPayment_sales_report").html(data);
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
            url: "<?php echo site_url('Pos_restaurant/get_outlet_cashier'); ?>",
            data: {warehouseAutoID:$('#outlet').val()},
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                if(!$.isEmptyObject(data))
                {
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