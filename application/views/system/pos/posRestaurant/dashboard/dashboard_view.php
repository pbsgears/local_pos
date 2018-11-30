<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos-dashboard.css') ?>">
<link rel="stylesheet" type="text/css"
      href="<?php echo base_url('plugins/datetimepicker/build/css/bootstrap-datetimepicker.min.css') ?>">
<form action="">
    <input type="hidden" id="template2LoadStatus" value="0">
</form>

<div>
    <div class="row">
        <div class="m-b-md col-md-6" id="wizardControl" style="padding: 0px 30px;">
            <a class="btn btn-default prm" href="#step1" data-toggle="tab">
                <i class="fa fa-area-chart"></i> <?php echo $this->lang->line('posr_dashboard'); ?><!--Template--> 1</a>
            <a class="btn btn-default btn-wizard" href="#step2" data-toggle="tab" onclick="loadTab2()">
                <i class="fa fa-bar-chart"></i> <?php echo $this->lang->line('posr_dashboard'); ?><!--Template--> 2</a>
        </div>
        <div class="col-md-6 ">
            <form id="frm_filters_posr" method="post">
                <input type="hidden" id="wareHouseAutoID" name="wareHouseAutoID" value="0">

                <?php
                $outlets = get_active_outletInfo();
                //print_r($outlets);
                ?>
                <div class="dropdown pull-right" style="padding-right: 15px;">
                    <button class="btn btn-primary dropdown-toggle " type="button" data-toggle="dropdown"><i
                                class="fa fa-filter"></i> <span id="filterTxt">All Outlets</span>
                        <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="#" onclick="filter_dashbaord(0,'All Outlets')">All Outlets</a></li>
                        <?php
                        foreach ($outlets as $outlet) {
                            $outlet['wareHouseAutoID'];
                            echo '<li><a href="#" onclick="filter_dashbaord(\'' . $outlet['wareHouseAutoID'] . '\',\'' . $outlet['wareHouseDescription'] . ' - ' . trim($outlet['wareHouseLocation']) . ' \')">' . trim($outlet['wareHouseCode']) . ' - ' . trim($outlet['wareHouseDescription']) . ' - ' . trim($outlet['wareHouseLocation']) . '</a></li>';
                        }
                        ?>

                    </ul>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <div class="col-md-8" style="padding-top: 7px;">
            <div class="box box-primary" style="background-color: rgba(244, 244, 244, 0.47);">
                <div class="box-header boxHeaderCustom">
                    <?php echo $this->lang->line('posr_last_7_days_sales_analysis'); ?><!--Last 7 Days Sales Analysis-->
                    <i class="fa fa-refresh pull-right" onclick="LoadSalesYTDAverage();"
                       style="color:#CCC; padding-left:10px;" aria-hidden="true"></i>
                </div>
                <div id="LoadSalesYTDAverage_div">
                    &nbsp;
                </div>
            </div>
            <div class="box box-warning" style="background-color: rgba(244, 244, 244, 0.47);">
                <div class="box-header boxHeaderCustom">
                    REVPASH
                    <i class="fa fa-refresh pull-right" onclick="load_REVPASH();"
                       style="color:#CCC; padding-left:10px;" aria-hidden="true"></i>
                </div>
                <div id="container_revpash"></div>
            </div>
        </div>

        <div class="col-md-4 " style="padding-top: 7px;">
            <div class="box box-warning hide" style="background-color: rgba(244, 244, 244, 0.47);">
                <div class=" box-header boxHeaderCustom">
                    <?php echo $this->lang->line('posr_reports'); ?><!--Reports-->
                    <i class="fa fa-refresh pull-right" onclick="load_profitVsSales();"
                       style="color:#CCC; padding-left:10px;" aria-hidden="true"></i>
                </div>
                <div class="box-footer">
                    <div class="list-group">
                        <a onclick="loadPaymentSalesReport()" href="#" class="list-group-item"><i
                                    class="fa fa-line-chart text-red" aria-hidden="true"></i>
                            <?php echo $this->lang->line('posr_payment_sales_report'); ?><!--Payment Sales Report-->
                        </a>

                        <!--<a onclick="loadPaymentSalesReport2()" href="#" class="list-group-item"><i
                                class="fa fa-line-chart text-red" aria-hidden="true"></i>
                            <?php /*echo $this->lang->line('posr_payment_sales_report'); */ ?>&nbsp;  &nbsp; <span class="label label-primary">New</span>
                        </a>-->

                        <a href="#" class="list-group-item" onclick="loadPaymentItemized_salesReport()">
                            <i class="fa fa-area-chart text-blue" aria-hidden="true"></i>
                            <?php echo $this->lang->line('posr_item_wise_sales_report'); ?><!--Item Wise Sales Report-->
                        </a>
                        <a href="#" class="list-group-item" onclick="loaddeliveryperson_Report()">
                            <i class="fa fa-bar-chart text-orange" aria-hidden="true"></i>
                            Delivery Commission Report
                        </a>
                        <a href="#" class="list-group-item" onclick="load_productMix()">
                            <i class="fa fa-pie-chart text-green" aria-hidden="true"></i>
                            <?php echo $this->lang->line('posr_product_mix'); ?><!--Product Mix--> </a>
                        <a href="#" class="list-group-item" onclick="load_franchiseReport()">
                            <i class="fa fa-cutlery text-purple" aria-hidden="true"></i>
                            <?php echo $this->lang->line('posr_franchise_report'); ?><!--Franchise Report--> </a>
                    </div>

                </div>
            </div>

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
$this->load->view('system/pos/modals/rpos-modal-payment-sales');
$this->load->view('system/pos/modals/rpos-modal-payment-sales2');
$this->load->view('system/pos/modals/rpos-modal-itemized-sales');
$this->load->view('system/pos/modals/rpos-modal-delivery-persons');
$this->load->view('system/pos/modals/rpos-modal-product-mix');
$this->load->view('system/pos/modals/rpos-modal-franchise-report');
?>
<script type="text/javascript"
        src="<?php echo base_url('plugins/highchart/modules/no-data-to-display.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/knob/jquery.knob.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/chartjs/Chart.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/countup/countUp.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>

<script>

    var i = 0;

    function filter_dashbaord(wareHouseAutoID, stringID) {
        $("#wareHouseAutoID").val(wareHouseAutoID);
        $("#filterTxt").html(stringID);
        LoadSalesYTDAverage();
        load_profitVsSales();
        load_REVPASH();
        load_invoicePax();
        load_fastMovingItemByValue();
    }

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
            url: "<?php echo site_url('Pos_dashboard/loadDashboard_sales_YTD'); ?>",
            data: $("#frm_filters_posr").serialize(),
            cache: false,
            beforeSend: function () {
                i++;
                startLoadPos();
            },
            success: function (data) {
                i--;
                if (i == 0) {
                    stopLoad();
                }
                $("#LoadSalesYTDAverage_div").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                i--;
                if (i == 0) {
                    stopLoad();
                }
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function load_REVPASH() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_dashboard/load_REVPASH'); ?>",
            data: $("#frm_filters_posr").serialize(),
            cache: false,
            beforeSend: function () {
                i++;
                startLoadPos();
                $("#container_revpash").html('<h3 style="text-align: center; "><i class="fa fa-refresh fa-spin"></i> Loading</h3>');
            },
            success: function (data) {
                i--;
                if (i == 0) {
                    stopLoad();
                }
                $("#container_revpash").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                i--;
                if (i == 0) {
                    stopLoad();
                }
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function load_profitVsSales() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_dashboard/load_profitVsSales'); ?>",
            data: $("#frm_filters_posr").serialize(),
            cache: false,
            beforeSend: function () {
                i++;
                startLoadPos();
            },
            success: function (data) {
                i--;
                if (i == 0) {
                    stopLoad();
                }
                $("#profitVsSalesDiv").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                i--;
                if (i == 0) {
                    stopLoad();
                }
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function load_invoicePax() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_dashboard/load_invoicePax'); ?>",
            data: $("#frm_filters_posr").serialize(),
            cache: false,
            beforeSend: function () {
                i++;
                startLoadPos();
            },
            success: function (data) {
                i--;
                if (i == 0) {
                    stopLoad();
                }
                $("#dashboard_pax").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                i--;
                if (i == 0) {
                    stopLoad();
                }
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function load_fastMovingItemByValue() {
        /*if ($("#template2LoadStatus").val() == 0) {*/
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_dashboard/load_fastMovingItemByValueMix'); ?>",//Pos_dashboard/load_fastMovingItemByValueMix
            data: $("#frm_filters_posr").serialize(),
            cache: false,
            beforeSend: function () {
                i++;
                startLoadPos();
            },
            success: function (data) {
                i--;
                if (i == 0) {
                    stopLoad();
                }
                $("#template2LoadStatus").val(1);
                $("#fastMovingItemDiv").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                i--;
                if (i == 0) {
                    stopLoad();
                }
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
        /*  }*/
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
        load_REVPASH();
        load_profitVsSales();


    });

</script>