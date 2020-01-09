
<style>
    .chart-col4 {
        padding-left: 20px;
    }

    .chart-icon-box {
        height: 136px;
        width: 98%;
    }

    .chart-ico-pad {
        padding-top: 51px !important;
        font-size: 30px;
    }
</style>
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div class="row" style="">
    <div class=" col-sm-4 chart-col4">
        <span class="info-box-icon bg-aqua-gradient chart-icon-box">
            <i class="fa fa-line-chart chart-ico-pad"></i>
        </span>
    </div>

    <div class=" col-sm-5 text-center"
         style="margin-left: -18px;padding-right: 0px;padding-left: 0px;border: 1px solid #a1aec7;"><label
            style="font-size: 0.9em;font-weight: bold;"><?php echo $this->lang->line('posr_yesterday');?><!--YESTERDAY--></label> <!--High chart Container -->
        <div id="ps_yesterday" style="min-width: 120px; height: 110px; max-width: 120px;margin:0 auto;"></div>
    </div>
    <div class=" col-sm-3 text-center"
         style="height: 136px;border: 1px solid #a1aec7; border-left:none;padding-left: 0px;padding-right: 0px;">
        <div class="col-sm-6"
             style="border: 1px solid #a1aec7;border-left:none;border-right:none;border-top:none;height: 50%;width: 100%;padding-left: 0px;padding-right: 0px;  ">
            <span class="" style="padding-top: 12px;text-align:center;font-size: 1em;font-weight: 600;"><?php echo $this->lang->line('posr_sales');?><!--Sales--></span>
            <span class="info-box-number" id="ps_yesterday_sales" style="text-align: center;font-size: 1.0em;">0</span>
        </div>

        <div class="col-sm-6" style="height: 50%;width: 100%;padding-left: 0px;padding-right: 0px;  ">
            <span class="" style="padding-top: 12px;text-align:center;font-size: 1em;font-weight: 600;"><abbr title="Gross Profit"><?php echo $this->lang->line('posr_gp');?><!--GP--></abbr></span>
            <span class="info-box-number" id="ps_yesterday_profit" style="text-align: center;font-size: 1.0em;">0</span>
        </div>
    </div>
</div>


<!--**************************************** WTD **************************************** -->
<div class="row" style="padding-top: 5px;">
    <div class=" col-sm-4 chart-col4">
        <span class="info-box-icon bg-green-gradient chart-icon-box">
            <i class="fa fa-pie-chart chart-ico-pad"></i>
        </span>
    </div>

    <div class="col-sm-5 text-center"
         style="margin-left: -18px;padding-right: 0px;padding-left: 0px;border: 1px solid #a1aec7;"><label
            style="color:#019853;font-size: 0.9em;font-weight: bold;"><?php echo $this->lang->line('posr_wtd_revpash');?><!--WTD--></label> <!--High chart Container -->
        <div id="ps_WTD" style="min-width: 120px; height: 110px; max-width: 120px;margin:0 auto;"></div>
    </div>
    <div class=" col-sm-3 text-center"
         style="height: 136px;border: 1px solid #a1aec7; border-left:none;padding-left: 0px;padding-right: 0px;">
        <div class="col-sm-6"
             style="border: 1px solid #a1aec7;border-left:none;border-right:none;border-top:none;height: 50%;width: 100%;padding-left: 0px;padding-right: 0px;  ">
            <span
                class=""
                style="padding-top: 12px;text-align:center;color:#019853;font-size: 1em;font-weight: 600;"><?php echo $this->lang->line('posr_sales');?><!--Sales--></span>
            <span class="info-box-number" id="ps_WTD_sales" style="text-align: center;font-size: 1.0em;">0</span>
        </div>
        <div class="col-sm-6" style="height: 50%;width: 100%;padding-left: 0px;padding-right: 0px;  "><span
                class=""
                style="padding-top: 12px;text-align:center;color:#019853;font-size: 1em;font-weight: 600;"><abbr title="Gross Profit"><?php echo $this->lang->line('posr_gp');?><!--GP--></abbr></span>
            <span class="info-box-number" id="ps_WTD_profit" style="text-align: center;font-size: 1.0em;">0</span></div>
    </div>
</div>

<!--**************************************** MTD **************************************** -->
<div class="row" style="padding-top: 5px;">
    <div class=" col-sm-4 chart-col4">
        <span class="info-box-icon bg-light-blue-gradient chart-icon-box">
            <i class="fa fa-area-chart chart-ico-pad"></i>
        </span>
    </div>

    <div class="col-sm-5 text-center"
         style="margin-left: -18px;padding-right: 0px;padding-left: 0px;border: 1px solid #a1aec7;"><label
            style="font-size: 0.9em;font-weight: bold;"><?php echo $this->lang->line('posr_mtd_revpash');?><!--MTD--></label> <!--High chart Container -->
        <div id="ps_MTD" style="min-width: 120px; height: 110px; max-width: 120px;margin:0 auto;"></div>
    </div>
    <div class=" col-sm-3 text-center"
         style="height: 136px;border: 1px solid #a1aec7; border-left:none;padding-left: 0px;padding-right: 0px;">
        <div class="col-sm-6"
             style="border: 1px solid #a1aec7;border-left:none;border-right:none;border-top:none;height: 50%;width: 100%;padding-left: 0px;padding-right: 0px;  "><span
                class=""
                style="padding-top: 12px;text-align:center;font-size: 1em;font-weight: 600;"><?php echo $this->lang->line('posr_sales');?><!--Sales--></span>
            <span class="info-box-number" id="ps_MTD_sales" style="text-align: center;font-size: 1.0em;">0</span>
        </div>
        <div class="col-sm-6" style="height: 50%;width: 100%;padding-left: 0px;padding-right: 0px;  "> <span
                class=""
                style="padding-top: 12px;text-align:center;font-size: 1em;font-weight: 600;"><abbr title="Gross Profit"><?php echo $this->lang->line('posr_gp');?><!--GP--></abbr></span>
            <span class="info-box-number" id="ps_MTD_profit" style="text-align: center;font-size: 1.0em;">0</span></div>
    </div>
</div>

<!--**************************************** YTD **************************************** -->
<div class="row" style="padding-top: 5px;">
    <div class=" col-sm-4 chart-col4">
        <span class="info-box-icon bg-teal-gradient chart-icon-box">
            <i class="fa fa-bar-chart chart-ico-pad"></i>
        </span>
    </div>
    <div class="col-sm-5 text-center"
         style="margin-left: -18px;padding-right: 0px;padding-left: 0px;border: 1px solid #a1aec7;"><label
            style="color:#39cccc;font-size: 0.9em;font-weight: bold;"><?php echo $this->lang->line('posr_ytd_revpash');?><!--YTD--></label> <!--High chart Container -->
        <div id="ps_YTD" style="min-width: 120px; height: 110px; max-width: 120px;margin:0 auto;"></div>
    </div>
    <div class=" col-sm-3 text-center"
         style="height: 136px;border: 1px solid #a1aec7; border-left:none;padding-left: 0px;padding-right: 0px;">
        <div class="col-sm-6"
             style="border: 1px solid #a1aec7;border-left:none;border-right:none;border-top:none;height: 50%;width: 100%;padding-left: 0px;padding-right: 0px;  ">
            <span
                class=""
                style="padding-top: 12px;text-align:center;color:#39cccc;font-size: 1em;font-weight: 600;"><?php echo $this->lang->line('posr_sales');?><!--Sales--></span>
            <span class="info-box-number" id="ps_YTD_sales" style="text-align: center;font-size: 1.0em;">0</span>
        </div>
        <div class="col-sm-6" style="height: 50%;width: 100%;padding-left: 0px;padding-right: 0px;  ">
            <span class="" style="padding-top: 12px;text-align:center;color:#39cccc;font-size: 1em;font-weight: 600;"><abbr title="Gross Profit"><?php echo $this->lang->line('posr_gp');?><!--GP--></abbr></span>
            <span class="info-box-number" id="ps_YTD_profit" style="text-align: center;font-size: 1.0em;">0</span></div>
    </div>
</div>
<script>
    $(document).ready(function (e) {
        genHChart('ps_yesterday', <?php echo $yesterday['profit']?>, <?php echo $yesterday['sales']?>);
        genHChart('ps_WTD', <?php echo $WTD['profit']?>, <?php echo $WTD['sales']?>);
        genHChart('ps_MTD', <?php echo $MTD['profit']?>, <?php echo $MTD['sales']?>);
        genHChart('ps_YTD', <?php echo $YTD['profit']?>, <?php echo $YTD['sales']?>);
    });

    function addCommas(nStr) {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    function genHChart(id, profit, sales) {

        Highcharts.setOptions({
            lang: {
                thousandsSep: ','
            }
        });

        $("#" + id + "_profit").html(addCommas(profit));
        $("#" + id + "_sales").html(addCommas(sales));

        Highcharts.chart(id, {

            chart: {plotBackgroundColor: null, plotBorderWidth: null, plotShadow: false, type: 'pie'},
            title: {text: ''},
            tooltip: {pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'},
            exporting: {enabled: false},
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {enabled: false},
                    showInLegend: false
                }, lang: {
                    thousandsSep: ','
                }
            },
            series: [{
                name: 'Brands',
                innerSize: '50%',
                colorByPoint: true,
                data: [{name: 'GP', y: profit}, {name: 'sales', y: sales}]
            }]
        });
    }
</script>