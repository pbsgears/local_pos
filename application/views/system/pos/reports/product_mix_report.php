<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);


$title = '<i class="fa fa-bar-chart"></i> ' . $this->lang->line('posr_product_mix');
$locations = load_pos_location_drop();
$outlets = get_active_outletInfo();

echo head_page($title, false);
?>
<style>
    .reportContainer {
        border: 2px solid gray !important;
        padding: 10px !important;
        min-height: 200px;
        margin-top: 10px;
    }
</style>
<form id="frm_ProductMix" method="post" class="form-inline" role="form">
    <input type="hidden" id="productMix_outletID" name="outletID" value="0"/>
    <label for="" class="col-sm-2"><?php echo $this->lang->line('Filters'); ?><!--Filters--> </label>

    <div class="form-group">
        <label class="" for="">Outlets</label>
        <select class="form-control input-sm" name="outlet[]" id="outletproductmix" onchange="loadCashierproductmix()"
                multiple>
            <?php
            foreach ($outlets as $outlet) {
                echo '<option value="' . $outlet['wareHouseAutoID'] . '">' . trim($outlet['wareHouseDescription']) . '</option>';
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label class="" for=""><?php echo $this->lang->line('posr_cashier'); ?><!--Cashier--> </label>
        <span id="cashier_option_productmix">
          <?php
          $cashiers = get_cashiers();
          //var_dump($cashiers);
          ?>
            <select name="cashier[]" id="cashierproductmix" class="form-control input-sm" multiple="multiple" required>
              <?php if (!empty($cashiers)) {
                  foreach ($cashiers as $key => $cashier) {
                      ?>
                      <option value="<?php echo $key ?>" selected> <?php echo $cashier ?></option>
                      <?php
                  }
              }
              ?>

          </select>
            <?php //echo form_dropdown('cashier[]', get_cashiers(), '', 'multiple required id="cashier"  class="form-control input-sm"'); ?>
        </span>

    </div>
    <div class="form-group">
        <label class="" for=""><?php echo $this->lang->line('common_from'); ?><!--From--></label>


        <input type="text" required class="form-control input-sm" data-date-end-date="0d"
               name="filterFrom" id="filterFrom2" value="<?php echo date('d/m/Y') ?>"
               style="width: 79px;">
        <?php echo $this->lang->line('common_to'); ?><!--to-->
        <input type="text" required class="form-control input-sm" data-date-end-date="0d"
               value="<?php echo date('d/m/Y') ?>"
               style="width: 79px;" placeholder="To" name="filterTo" id="filterTo2">
    </div>

    <button type="submit" class="btn btn-primary btn-sm">
        <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report--></button>
</form>

<div id="pos_modalBody_posProductMix" class="reportContainer">
    <div class="text-center" style="color:#9c9c9c; font-size:20px; margin-top:75px; "> Click on the Generate
        Report
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>


<script>
    $(document).ready(function (e) {
        $("#cashierproductmix").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Search Cashier',
            includeSelectAllOption: true
        });
        $("#outletID_f").multiselect2('selectAll', false);
        $("#cashier").multiselect2('updateButtonText');

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
        //loadCashierproductmix();
    });

    function loadCashier() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/get_outlet_cashier'); ?>",
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
                $("#pos_modalBody_posProductMix").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
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
                $("#pos_modalBody_posProductMix").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
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
            data: {warehouseAutoID: $('#outletproductmix').val()},
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                if (!$.isEmptyObject(data)) {
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