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
<div aria-hidden="true" role="dialog" tabindex="-1" id="rpos_Itemized_sales_report" class="modal fade"
     data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title">
                    <?php echo $this->lang->line('posr_item_wise_sales_report'); ?><!--Item Wise Sales Report-->
                    <!--<span id="title_itemWiseSales"></span>-->
                </h4>
            </div>
            <div class="modal-body" style="min-height: 400px;">
                <form id="frm_itemizedSalesReport" method="post" class="form-inline" role="form">
                    <input type="hidden" id="iws_outletID" name="outletID" value="0"/>
                    <label for="" class="col-sm-2">
                        <?php echo $this->lang->line('common_filters'); ?><!--Filters--> </label>

                    <div class="form-group">

                        <label class="" for="">Outlets</label>
                        <select class="form-control input-sm" name="outlet[]" id="outlets" onchange="loadCashierItemized()" multiple>
                            <?php
                            foreach ($outlets as $outlet) {
                                echo '<option value="' . $outlet['wareHouseAutoID'] . '">' . $outlet['wareHouseCode'] . '-' . $outlet['wareHouseDescription'] . '-'. $outlet['wareHouseLocation']. '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">

                        <label class="" for=""><?php echo $this->lang->line('posr_cashier'); ?><!--Cashier--> </label>
                        <span id="cashier_option_Itemized">
                            <?php echo form_dropdown('cashier[]', get_cashiers(), '', 'multiple required id="cashier"  class="form-control input-sm"'); ?>
                        </span>

                    </div>

                    <div class="form-group">
                        <label class="" for=""><?php echo $this->lang->line('common_from'); ?><!--From--></label>


                        <input type="text" required class="form-control input-sm" data-date-end-date="0d"
                               name="filterFrom" id="filterFrom2" value="<?php echo date('d/m/Y') ?>"
                               style="width: 79px;">

                        <label class="" for=""><?php echo $this->lang->line('common_to'); ?><!--to--></label>
                        <input type="text" required class="form-control input-sm" data-date-end-date="0d"
                               value="<?php echo date('d/m/Y') ?>"
                               style="width: 79px;" placeholder="To" name="filterTo" id="filterTo2">
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">
                        <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report--></button>
                </form>
                <hr>
                <div id="pos_modalBody_posItemized_sales_report">

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
        $("#filterFrom2,#filterTo2").datepicker({
            format: 'dd/mm/yyyy'
        });

        $("#outlets").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Search outlet',
            includeSelectAllOption: true
        });
        $("#outlets").multiselect2('selectAll', false);
        $("#outlets").multiselect2('updateButtonText');
        $("#frm_salesReport").submit(function (e) {
            loadPaymentItemized_salesReport_ajax();
            return false;
        });

        $("#frm_itemizedSalesReport").submit(function (e) {
            loadPaymentItemized_salesReport_ajax();
            return false;
        })
    });

    function loadPaymentItemized_salesReport() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/loadItemizedSalesReport'); ?>",
            data: {id: null},
            cache: false,
            beforeSend: function () {
                $("#title_itemWiseSales").html(' - <span style="color:#de0303"><strong>' + $("#filterTxt").text() + '</strong></span>');
                $("#iws_outletID").val($("#wareHouseAutoID").val());
                $("#rpos_Itemized_sales_report").modal('show');
                startLoadPos();
                $("#pos_modalBody_posItemized_sales_report").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
            },
            success: function (data) {
                stopLoad();
                $("#pos_modalBody_posItemized_sales_report").html(data);
                loadCashierItemized();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function loadPaymentItemized_salesReport_ajax() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/loadItemizedSalesReport'); ?>",
            data: $("#frm_itemizedSalesReport").serialize(),
            cache: false,
            beforeSend: function () {
                $("#pos_modalBody_posItemized_sales_report").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i> <?php echo $this->lang->line('posr_loading_print_view');?> </div>');
                <!--Loading Print view-->
            },
            success: function (data) {
                $("#pos_modalBody_posItemized_sales_report").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function loadCashierItemized() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/get_outlet_cashier_itemized'); ?>",
            data: {warehouseAutoID:$('#outlets').val()},
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                if(!$.isEmptyObject(data))
                {
                    $('#cashier_option_Itemized').html(data);
                    $("#cashieritemized").multiselect2({
                        enableFiltering: true,
                        filterPlaceholder: 'Search Cashier',
                        includeSelectAllOption: true
                    });
                    $("#cashieritemized").multiselect2('selectAll', false);
                    $("#cashieritemized").multiselect2('updateButtonText');
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


</script>