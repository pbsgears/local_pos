<?php echo head_page('<i class="fa fa-bar-chart"></i> Collection Detail Report', false);
$locations = load_pos_location_drop();
$currentUserWarehouseID = current_warehouseID();
$get_discountType = get_specialCustomers_drop(array(2, 3));


$outlets = get_active_outletInfo();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

?>
<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
    .customPad {
        padding: 3px 0px;
    }

    .al {
        text-align: left !important;
    }

    .ar {
        text-align: right !important;
    }

    .reportContainer {
        border: 2px solid gray !important;
        padding: 10px !important;
        min-height: 200px;
    }

</style>
<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);


?>
<form id="frm_report" method="post" class="form-inline" role="form">
    <input type="hidden" id="promoOrder_outletID" name="outletID" value="0"/>


    <div class="form-group">

        <label class="" for="">Outlets</label>
        <select class="form-control input-sm" name="outlet[]" id="outletPromotions" onchange="loadCashierPromotions()"
                multiple>
            <?php
            foreach ($outlets as $outlet) {
                echo '<option value="' . $outlet['wareHouseAutoID'] . '">' . $outlet['wareHouseCode'] . '-' . $outlet['wareHouseDescription'] . '-' . $outlet['wareHouseLocation'] . '</option>';
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


    Date From : <input type="text" class="form-control input-sm startdateDatepic" id="startdate"
                       name="startdate" value="<?php echo date('d-m-Y 00:00:00') ?>" style="width: 130px;"/>

    To : <input type="text" class="form-control input-sm startdateDatepic" id="enddate" name="enddate"
                value="<?php echo date('d-m-Y 23:59:59') ?>" style="width: 130px;"/>


    <button type="button" onclick="load_collection_detail_report()" class="btn btn-primary btn-sm">
        <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report-->
    </button>
</form>
<hr>
<div id="pos_modalBody_delivery_person_report">

</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    $(document).ready(function (e) {
        $('.startdateDatepic').datetimepicker({
            showTodayButton: true,
            format: "DD/MM/YYYY hh:mm A",
            sideBySide: false,
            widgetPositioning: {
                /*horizontal: 'left',*/
                /*vertical: 'bottom'*/
            }
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

    function load_collection_detail_report() {
        $("#promoOrder_outletID").val($("#wareHouseAutoID").val());
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant_report/load_collection_detail_report'); ?>",
            data: $("#frm_report").serialize(),
            cache: false,
            beforeSend: function () {
                $("#rpos_delivery_person_report").modal('show');
                $("#title_promotion_or_order").html(' - <span style="color:#de0303"><strong>' + $("#filterTxt").text() + '</strong></span>');
                $("#promoOrder_outletID").val($("#wareHouseAutoID").val());
                startLoadPos();
                $("#pos_modalBody_delivery_person_report").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');

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
            data: $("#frm_report").serialize(),
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
            data: {warehouseAutoID: $('#outletPromotions').val()},
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                if (!$.isEmptyObject(data)) {
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