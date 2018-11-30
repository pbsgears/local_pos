<?php
$customer_arr = all_delivery_person_drop();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$outlets = get_active_outletInfo();
?>
<style>
    .datepicker-days table thead th, td {
        border-radius: 0px !important;

    }
</style>
<div aria-hidden="true" role="dialog" tabindex="-1" id="rpos_delivery_person_report" class="modal fade"
     data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" onclick="hideModalprsn()" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title">

                    Delivery Commission Report
                    <!--<span id="title_promotion_or_order"></span>-->
                </h4>
            </div>
            <div class="modal-body" style="min-height: 400px;">
                <form id="frm_deliverypersonReport" method="post" class="form-inline" role="form">
                    <input type="hidden" id="promoOrder_outletID" name="outletID" value="0"/>


                    <div class="form-group">

                        <label class="" for="">Outlets</label>
                        <select class="form-control input-sm" name="outlet[]" id="outletPromotions" onchange="loadCashierPromotions()" multiple>
                            <?php
                            foreach ($outlets as $outlet) {
                                echo '<option value="' . $outlet['wareHouseAutoID'] . '">' . $outlet['wareHouseCode'] . '-' . $outlet['wareHouseDescription'] . '-'. $outlet['wareHouseLocation']. '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">

                        <label class="" for=""><?php echo $this->lang->line('posr_cashier'); ?><!--Cashier--> </label>
                        <span id="cashier_option_Promotions">
                            <?php echo form_dropdown('cashier[]', get_cashiers(), '', 'multiple required id="cashier"  class="form-control input-sm"'); ?>
                        </span>

                    </div>
                    <div class="form-group">
                        <label class="" for=""><?php echo $this->lang->line('common_from'); ?><!--From--></label>


                        <input type="text" required class="form-control input-sm" data-date-end-date="0d"
                               name="filterFrom" id="filterFrom2" value="<?php echo date('d/m/Y') ?>"
                               style="width: 79px;">
                        <?php echo $this->lang->line('common_to'); ?><!-- to-->
                        <input type="text" required class="form-control input-sm" data-date-end-date="0d"
                               value="<?php echo date('d/m/Y') ?>"
                               style="width: 79px;" placeholder="To" name="filterTo" id="filterTo2">
                        <?php echo $this->lang->line('posr_promotions_or_order'); ?><!-- Promotions Or Order-->

                        <?php echo form_dropdown('customerID[]', $customer_arr, '', 'class="form-control select2" multiple id="customerID"'); ?>
                    </div>

                    <button type="button" onclick="loaddeliveryperson_Report()" class="btn btn-primary btn-sm">
                        <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report--></button>
                </form>
                <hr>
                <div id="pos_modalBody_delivery_person_report">

                </div>

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-default btn-sm" onclick="hideModalprsn()">
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
        /*$("#frm_deliverypersonReport").submit(function (e) {
         loadPaymentItemized_salesReport_ajax();
         return false;
         })*/
        $("#outletPromotions").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Search outlet',
            includeSelectAllOption: true
        });
        $("#outletPromotions").multiselect2('selectAll', false);
        $("#outletPromotions").multiselect2('updateButtonText');

        $("#customerID").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Search',
            includeSelectAllOption: true
        });
        $("#customerID").multiselect2('selectAll', false);
        $("#customerID").multiselect2('updateButtonText');

        loadCashierPromotions();

    });

    function loaddeliveryperson_Report() {
        $("#promoOrder_outletID").val($("#wareHouseAutoID").val());
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/LoadDeliveryPersonReport'); ?>",
            data: $("#frm_deliverypersonReport").serialize(),
            cache: false,
            beforeSend: function () {
                $("#rpos_delivery_person_report").modal('show');
                $("#title_promotion_or_order").html(' - <span style="color:#de0303"><strong>' + $("#filterTxt").text() + '</strong></span>');
                $("#promoOrder_outletID").val($("#wareHouseAutoID").val());
                startLoadPos();
                $("#pos_modalBody_delivery_person_report").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
            },
            success: function (data) {
                stopLoad();
                $("#pos_modalBody_delivery_person_report").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    /**function loadPaymentItemized_salesReport_ajax() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/loadItemizedSalesReport'); ?>",
            data: $("#frm_deliverypersonReport").serialize(),
            cache: false,
            beforeSend: function () {
                $("#pos_modalBody_delivery_person_report").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  Loading Print view</div>');
            },
            success: function (data) {
                $("#pos_modalBody_delivery_person_report").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }*/

    function hideModalprsn() {
        $('#filterFrom2,#filterTo2').val('<?php echo date('d/m/Y') ?>');
        $("#rpos_delivery_person_report").modal('hide');
    }

    function loadCashierPromotions() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/get_outlet_cashier_Promotions'); ?>",
            data: {warehouseAutoID:$('#outletPromotions').val()},
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                if(!$.isEmptyObject(data))
                {
                    $('#cashier_option_Promotions').html(data);
                    $("#cashierpromotions").multiselect2({
                        enableFiltering: true,
                        filterPlaceholder: 'Search Cashier',
                        includeSelectAllOption: true
                    });
                    $("#cashierpromotions").multiselect2('selectAll', false);
                    $("#cashierpromotions").multiselect2('updateButtonText');
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


</script>