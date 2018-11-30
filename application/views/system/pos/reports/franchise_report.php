<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);


$title = '<i class="fa fa-bar-chart"></i> Franchise Report';
$locations = load_pos_location_drop();
$outlets = get_active_outletInfo();

echo head_page($title, false);
?>
<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>
<form id="frm_franchise" method="post" class="form-inline" role="form">
    <input type="hidden" id="franchise_outletID" name="outletID" value="0"/>
    <label for="" class="col-sm-2">
        <?php //echo $this->lang->line('common_filters'); ?><!--Filters--> </label>
    <div class="form-group">

        <label class="" for="">Outlets</label>
        <select class="form-control input-sm" name="outlet[]" id="outletFranchise" onchange="loadCashierFranchise()"
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
        <span id="cashier_option_Franchise">
                            <?php echo form_dropdown('cashier[]', get_cashiers(), '', 'multiple required id="cashier"  class="form-control input-sm"'); ?>
                        </span>

    </div>
    <div class="form-group">
        <label class="" for=""><?php echo $this->lang->line('common_from'); ?><!--From--></label>


        <input type="text" required class="form-control input-sm" data-date-end-date="0d"
               name="filterFrom" id="filterFrom_fr" value="<?php echo date('d/m/Y') ?>"
               style="width: 79px;">
        <?php echo $this->lang->line('common_to'); ?><!--to-->
        <input type="text" required class="form-control input-sm" data-date-end-date="0d"
               value="<?php echo date('d/m/Y') ?>"
               style="width: 79px;" placeholder="To" name="filterTo" id="filterTo2_fr">
    </div>

    <button type="button" onclick="load_franchiseReport()" class="btn btn-primary btn-sm">
        <?php echo $this->lang->line('posr_generate_report'); ?><!--Generate Report--></button>
</form>
<hr>
<div id="pos_modalBody_posFranchise">

</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>
    $(document).ready(function (e) {
        $("#filterFrom_fr,#filterTo2_fr").datepicker({
            format: 'dd/mm/yyyy'
        });



        $("#outletFranchise").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Search outlet',
            includeSelectAllOption: true
        });
        $("#outletFranchise").multiselect2('selectAll', false);
        $("#outletFranchise").multiselect2('updateButtonText');
        loadCashierFranchise();
    });

    function load_franchiseReport() {
        $("#franchise_outletID").val($("#wareHouseAutoID").val());
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/loadFranchiseReport'); ?>",
            data: $("#frm_franchise").serialize(),
            cache: false,
            beforeSend: function () {
                $("#rpos_franchise").modal('show');
                $("#title_franchise").html(' - <span style="color:#de0303"><strong>' + $("#filterTxt").text() + '</strong></span>');
                $("#franchise_outletID").val($("#wareHouseAutoID").val());
                startLoadPos();
                $("#pos_modalBody_posFranchise").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                <!--Loading Print view-->
            },
            success: function (data) {
                stopLoad();
                $("#pos_modalBody_posFranchise").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }



    function loadCashierFranchise() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/get_outlet_cashier_franchise'); ?>",
            data: {warehouseAutoID: $('#outletFranchise').val()},
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                if (!$.isEmptyObject(data)) {
                    $('#cashier_option_Franchise').html(data);
                    $("#cashierfranchise").multiselect2({
                        enableFiltering: true,
                        filterPlaceholder: 'Search Cashier',
                        includeSelectAllOption: true
                    });
                    $("#cashierfranchise").multiselect2('selectAll', false);
                    $("#cashierfranchise").multiselect2('updateButtonText');
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


</script>