<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos-dashboard.css') ?>">
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">-->
<form action="">
    <input type="hidden" id="template2LoadStatus" value="0">
</form>

<div class="row hide">
    <div class="m-b-md col-md-6" id="wizardControl">
        <a class="btn btn-primary prm" href="#step1" data-toggle="tab">Template1</a>
        <a class="btn btn-default btn-wizard hidden" href="#step2" data-toggle="tab" onclick="loadTab2()">Template2</a>
    </div>
</div>

<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <div class="col-md-8" style="padding-top: 7px;">
            <div class="box box-primary" style="background-color: rgba(244, 244, 244, 0.47);">
                <div class="box-header boxHeaderCustom">
                    Last 7 Days Sales Analysis
                    <i class="fa fa-refresh pull-right" onclick="LoadSalesYTDAverage();"
                       style="color:#CCC; padding-left:10px;" aria-hidden="true"></i>
                </div>
                <div id="LoadSalesYTDAverage_div">
                    &nbsp;
                </div>
            </div>


            <div class="box box-primary" style="background-color: rgba(244, 244, 244, 0.47);">
                <div class="box-header boxHeaderCustom">
                    Fast Moving item
                    <i class="fa fa-refresh pull-right" onclick="LoadSalesYTDAverage();"
                       style="color:#CCC; padding-left:10px;" aria-hidden="true"></i>
                </div>
                <div id="fastMovingItemDiv"></div>
            </div>



        </div>
        <div class="col-md-4" style="padding-top: 7px;">
            <div class="box box-danger" style="background-color: rgba(244, 244, 244, 0.47);">
                <div class="box-header boxHeaderCustom">
                    <?php echo $this->lang->line('posr_profit_vs_sales'); ?><!-- Profit Vs Sales-->
                    <i class="fa fa-refresh pull-right" onclick="load_profitVsSales();"
                       style="color:#CCC; padding-left:10px;" aria-hidden="true"></i>
                </div>
                <div class="box-footer">
                    <div id="profitVsSalesDiv">&nbsp;</div>
                </div>
            </div>

        </div>
    </div>


    <div id="step2" class="tab-pane">
        <div id="dashboard_pax">&nbsp;</div>
        <div id="fastMovingItemDiv"></div>
    </div>
</div>
<?php
$this->load->view('system/pos/modals/general/gpos-modal-payment-sales');
$this->load->view('system/pos/modals/general/gpos-modal-itemized-sales');

?>
<!--<script type="text/javascript" src="<?php /*echo base_url('plugins/highchart/highcharts.js'); */ ?>"></script>
<script type="text/javascript" src="<?php /*echo base_url('plugins/highchart/modules/exporting.js'); */ ?>"></script>-->
<script type="text/javascript"
        src="<?php echo base_url('plugins/highchart/modules/no-data-to-display.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/knob/jquery.knob.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/chartjs/Chart.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/countup/countUp.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>

<script>

    function loadTab2() {
        load_invoicePax();
        setTimeout(function () {
            load_fastMovingItemByValue();
        }, 500);
    }

    function LoadSalesYTDAverage() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_dashboard/loadGeneralDashboard_sales_YTD'); ?>",
            data: {id: null},
            cache: false,
            beforeSend: function () {
                startLoadPos();
            },
            success: function (data) {
                stopLoad();
                $("#LoadSalesYTDAverage_div").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function load_profitVsSales() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_dashboard/load_profitVsSales_generalPOS'); ?>",
            data: $("#frm_filters_posr").serialize(),
            cache: false,
            beforeSend: function () {
                startLoadPos();
            },
            success: function (data) {
                stopLoad();
                $("#profitVsSalesDiv").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }


    function load_fastMovingItemByValue() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_dashboard/load_fastMovingItemByValueMix_generalPOS'); ?>",
            data: $("#frm_filters_posr").serialize(),
            cache: false,
            beforeSend: function () {
                startLoadPos();
            },
            success: function (data) {
                stopLoad();
                $("#fastMovingItemDiv").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

</script>

<script>
    $(document).ready(function () {
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        LoadSalesYTDAverage();
        load_profitVsSales();
        load_fastMovingItemByValue();
    });

</script>