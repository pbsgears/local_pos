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
$outlets = get_active_outletInfo();
?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="rpos_ProductMix" class="modal fade" data-keyboard="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"> <?php echo $this->lang->line('posr_product_mix');?><!--Product Mix-->  </h4>
            </div>
            <div class="modal-body" style="min-height: 400px;">
                <form id="frm_ProductMix" method="post" class="form-inline" role="form">
                    <input type="hidden" id="productMix_outletID" name="outletID" value="0"/>
                    <label for="" class="col-sm-2"><?php echo $this->lang->line('Filters');?><!--Filters--> </label>

                    <div class="form-group">

                        <label class="" for="">Outlets</label>
                        <select class="form-control input-sm" name="outlet[]" id="outletproductmix" onchange="loadCashierproductmix()" multiple>
                            <?php
                            foreach ($outlets as $outlet) {

                                echo '<option value="' . $outlet['wareHouseAutoID'] . '">' . $outlet['wareHouseCode'] . '-' . $outlet['wareHouseDescription'] . '-'. $outlet['wareHouseLocation']. '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">

                        <label class="" for=""><?php echo $this->lang->line('posr_cashier'); ?><!--Cashier--> </label>
                        <span id="cashier_option_productmix">
                            <?php echo form_dropdown('cashier[]', get_cashiers(), '', 'multiple required id="cashier"  class="form-control input-sm"'); ?>
                        </span>

                    </div>
                    <div class="form-group">
                        <label class="" for=""><?php echo $this->lang->line('common_from');?><!--From--></label>


                        <input type="text" required class="form-control input-sm" data-date-end-date="0d"
                               name="filterFrom" id="filterFrom2" value="<?php echo date('d/m/Y') ?>"
                               style="width: 79px;">
                        <?php echo $this->lang->line('common_to');?><!--to-->
                        <input type="text" required class="form-control input-sm" data-date-end-date="0d" value="<?php echo date('d/m/Y') ?>"
                               style="width: 79px;" placeholder="To"  name="filterTo" id="filterTo2" >
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm"><?php echo $this->lang->line('posr_generate_report');?><!--Generate Report--></button>
                </form>
                <hr>
                <div id="pos_modalBody_posProductMix">

                </div>

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function (e) {
        $("#filterFrom2,#filterTo2").datepicker({
            format: 'dd/mm/yyyy'
        });

        $("#frm_ProductMix").submit(function (e) {
            productMix_ajax();
            return false;
        });

        $("#outletproductmix").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Search outlet',
            includeSelectAllOption: true
        });
        $("#outletproductmix").multiselect2('selectAll', false);
        $("#outletproductmix").multiselect2('updateButtonText');
        loadCashierproductmix();
    });

    function load_productMix() {
        $("#productMix_outletID").val($("#wareHouseAutoID").val());
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/loadProductMix'); ?>",
            data: {id: null},
            cache: false,
            beforeSend: function () {
                $("#rpos_ProductMix").modal('show');
                $("#title_productMix").html(' - <span style="color:#de0303"><strong>' + $("#filterTxt").text() + '</strong></span>');
                $("#productMix_outletID").val($("#wareHouseAutoID").val());
                startLoadPos();
                $("#pos_modalBody_posProductMix").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');<!--Loading Print view-->
            },
            success: function (data) {
                stopLoad();
                $("#pos_modalBody_posProductMix").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function productMix_ajax() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/loadProductMix'); ?>",
            data: $("#frm_ProductMix").serialize(),
            cache: false,
            beforeSend: function () {
                $("#pos_modalBody_posProductMix").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');<!--Loading Print view-->
            },
            success: function (data) {
                $("#pos_modalBody_posProductMix").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function loadCashierproductmix() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/get_outlet_cashier_productmix'); ?>",
            data: {warehouseAutoID:$('#outlets').val()},
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                if(!$.isEmptyObject(data))
                {
                    $('#cashier_option_productmix').html(data);
                    $("#cashierproductmix").multiselect2({
                        enableFiltering: true,
                        filterPlaceholder: 'Search Cashier',
                        includeSelectAllOption: true
                    });
                    $("#cashierproductmix").multiselect2('selectAll', false);
                    $("#cashierproductmix").multiselect2('updateButtonText');
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


</script>