<?php echo head_page('<i class="fa fa-bar-chart"></i> Item wise Sales Report', false);
$locations = load_pos_location_drop();
$currentUserWarehouseID = current_warehouseID();

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
<?php
if($currentUserWarehouseID == ''){ ?>
    <div class="alert alert-warning">
        <strong>Warning<!--Warning-->!</strong><br>
        Outlet not configured for this user !
    </div>
    <?php
    exit();
}
?>
<form id="frm_itemizedSalesReport" method="post" class="form-inline" role="form">
    <input type="hidden" id="iws_outletID" name="outletID" value="0"/>
   <!-- <label for="" class="col-sm-2">
        <?php /*//echo $this->lang->line('common_filters'); */?> </label>-->

    <!--<div class="form-group">
        <label class="" for="">Outlets</label>
        <select class="form-control input-sm" name="outlet[]" id="outlets" onchange="loadCashierItemized()" multiple>
            <?php
    /*            foreach ($outlets as $outlet) {
                    echo '<option value="' . $outlet['wareHouseAutoID'] . '">' . trim($outlet['wareHouseDescription']) . '</option>';
                }
                */ ?>
        </select>
    </div>-->

    <div class="form-group col-md-3">

        <label class="" for=""><?php echo $this->lang->line('posr_cashier'); ?><!--Cashier--> </label>
        <span id="cashier_option_Itemized">
            <select name="cashier[]" multiple required id="cashier3" class="form-control input-sm">
            <?php
            $get_cashiers_drp = get_cashiers_drp();
            if (!empty($get_cashiers_drp)) {
                foreach ($get_cashiers_drp as $key => $cashier) {
                    echo '<option selected value="' . $key . '"> ' . $cashier . '</option>';
                }
            }
            ?>
            </select>
            <?php //echo form_dropdown('cashier[]', get_cashiers(), '', 'multiple required id="cashier3"  class="form-control input-sm"'); ?>
            
        </span>
    </div>


    <div class="form-group  col-md-6">
        <label class="" for=""><?php echo $this->lang->line('common_from'); ?><!--From--> &nbsp;&nbsp;</label>


        <input type="text" required class="form-control input-sm startdateDatepic"  name="filterFrom" id="filterFrom2"
               value="<?php echo date('d-m-Y 00:00:00') ?>">

        <label class="" for=""><?php echo $this->lang->line('common_to'); ?>&nbsp;&nbsp;<!--to-->&nbsp;&nbsp;</label>
        <input type="text" required class="form-control input-sm startdateDatepic"
               value="<?php echo date('d-m-Y 23:59:59') ?>" placeholder="To" name="filterTo" id="filterTo2">
    </div>




    <button type="submit" class="btn btn-primary btn-sm">
        <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report--></button>
</form>
<hr>
<div id="pos_modalBody_posItemized_sales_report" class="reportContainer">
    <div class="text-center" style="color:#9c9c9c; font-size:20px; margin-top:75px; "> Click on the Generate
        Report
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>
    $(document).ready(function (e) {
        $("#cashier").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true
        });

        $("#cashier3").multiselect2('selectAll', true);
        $("#cashier3").multiselect2('updateButtonText');


        /*$("#filterFrom2,#filterTo2").datepicker({
         format: 'dd/mm/yyyy'
         });*/
        $('.startdateDatepic').datetimepicker({
            showTodayButton: true,
            format: "DD/MM/YYYY hh:mm A",
            sideBySide: false,
            widgetPositioning: {
                /*horizontal: 'left',*/
                /*vertical: 'bottom'*/
            }
        }).on('dp.change', function (ev) {
            // $('#task_header_form').bootstrapValidator('revalidateField', 'startdate');
            //$(this).datetimepicker('hide');
        });

        $("#frm_itemizedSalesReport").submit(function (e) {
            loadPaymentItemized_salesReport_ajax();
            return false;
        })
    });


    function loadPaymentItemized_salesReport_ajax() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/loadItemizedSalesReport'); ?>",
            data: $("#frm_itemizedSalesReport").serialize(),
            cache: false,
            beforeSend: function () {
                $("#pos_modalBody_posItemized_sales_report").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i> <?php echo $this->lang->line('posr_loading_print_view');?> </div>');

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
            data: {warehouseAutoID: $('#outlets').val()},
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                if (!$.isEmptyObject(data)) {
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