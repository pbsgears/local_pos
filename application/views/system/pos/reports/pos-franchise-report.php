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

    tbody td {
        font-size: 12px !important;
        padding: 1px 10px;
    }

    thead th {
        font-size: 12px !important;
        padding: 3px 10px;
    }

    tfoot th {
        font-size: 12px !important;
        padding: 3px 10px;
    }

    .alin {
        text-align: right;
        padding-right: 3px;
    }

</style>
<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$time = time();
?>
<span class="pull-right">
    <button type="button" id="btn_print_itemizedSales" class="btn btn-default btn-xs">
        <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
    </button>
    <button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generateFranchisePdf()"><i
            class="fa fa-file-pdf-o"
            aria-hidden="true"></i> PDF
    </button>
    <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Franchise_Report.xls"
       onclick="var file = tableToExcel('printContainer_<?php echo $time ?>', 'Franchise Report'); $(this).attr('href', file);">
        <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
    </a>
</span>
<div id="printContainer_<?php echo $time ?>">
    <div class="text-center">
        <h4 style="margin-top:2px;"><strong><?php echo $companyInfo['company_name'] ?></strong></h4>
    </div>
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="outletInfo">
                <?php
                $outletInput = $this->input->post('outlet');

                echo get_outletFilterInfo($outletInput);
                /*$get_outletInfo = get_outletInfo();
                if (!empty($get_outletInfo['warehouseImage'])) {
                    echo '<img src="' . base_url('uploads/warehouses/' . $get_outletInfo['warehouseImage']) . '" style="max-height:60px;" /><br/>';
                }
                echo '<strong>' . $get_outletInfo['wareHouseDescription'] . ' - ' . $get_outletInfo['wareHouseCode'] . '</strong><br/>';
                echo $get_outletInfo['warehouseAddress'] . '<br/>';
                echo $get_outletInfo['wareHouseLocation'] . '<br/>';
                echo $get_outletInfo['warehouseTel'] . '<br/>';*/

                ?>
            </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="pull-right"><?php echo $this->lang->line('common_date'); ?><!--Date-->:
                <strong><?php echo date('d/m/Y'); ?></strong>
                <br/><?php echo $this->lang->line('posr_time'); ?><!--Time-->: <strong>
                    <span class="pcCurrentTime"></span></strong>
            </div>
        </div>
    </div>
    <hr style="margin:2px 0px;">
    <h3 class="text-center">Franchise Report</h3>
    <!--<div class="text-center">
        <h3 style="margin-top:2px;"><?php /*echo $companyInfo['company_name'] */ ?></h3>
        <h4 style="margin:0px;"><?php /*echo $companyInfo['company_address1'] . ', ' . $companyInfo['company_city'] */ ?></h4>
    </div>-->
    <!--<div style="margin:4px 0px; text-align: center;">
        <?php
    /*        $cash = $this->lang->line('posr_cashier');
            if (isset($cashier) && !empty($cashier)) {
                echo ''.$cash.': ';

                $tmpArray = array();
                foreach ($cashier as $c) {
                    $tmpArray[] = $cashierTmp[$c];
                }
                echo join(', ', $tmpArray);
            }
            */ ?>
    </div>-->
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        	<span>
        <?php echo $this->lang->line('posr_filtered_date'); ?><!--Filtered Date--> : <strong>
            <?php
            $filterFrom = $this->input->post('filterFrom');
            $filterTo = $this->input->post('filterTo');
            $from = $this->lang->line('common_from');
            $to = $this->lang->line('common_to');
            $today = $this->lang->line('posr_today');
            if (!empty($filterFrom) && !empty($filterTo)) {
                echo '  <i>' . $from . ' : </i>' . $filterFrom . ' - <i> ' . $to . ': </i>' . $filterTo;
            } else {
                $curDate = date('d-m-Y');
                echo $curDate . ' (' . $today . ')';
            }
            ?>
        </strong>
        </div>
    </div>
    <div style="margin:4px 0px;">
        <?php
        $cash = $this->lang->line('posr_cashier');

        if (isset($cashier) && !empty($cashier)) {
            echo '' . $cash . ' ';
            $tmpArray = array();
            foreach ($cashier as $c) {
                $tmpArray[] = isset($cashierTmp[$c]) ? $cashierTmp[$c] : '';
            }
            echo join(', ', $tmpArray);
        }
        ?>
    </div>

    <!--    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <?php /*echo $this->lang->line('common_date'); */ ?> : <strong><?php /*echo date('d/m/Y'); */ ?></strong>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
            <?php /*echo $this->lang->line('posr_time'); */ ?> : <strong><span id="pcCurrentTime"></span></strong>
        </div>
    </div>
-->

    <br>

    <table class="" style="width: 100%; " border="1">
        <thead>
        <tr>
            <th colspan="3"> &nbsp;</th>
            <th colspan="2">Dine-in</th>
            <th colspan="2"> <?php echo $this->lang->line('posr_take_away'); ?><!--Take Away--></th>
            <th colspan="2"> <?php echo $this->lang->line('posr_delivery'); ?><!--Delivery--></th>
            <th colspan="2"> <?php echo $this->lang->line('common_total'); ?><!--Total--></th>
        </tr>
        <tr>
            <th class=""> #</th>
            <th class=""> <?php echo $this->lang->line('common_day'); ?><!--Day--></th>
            <th class=""> <?php echo $this->lang->line('posr_order_date'); ?><!--Order Date--></th>
            <th class=""> <?php echo $this->lang->line('posr_qyt'); ?><!--QTY--></th>
            <th class=""> <?php echo $this->lang->line('posr_net_sales'); ?><!--Net Sales--></th>
            <th class=""> <?php echo $this->lang->line('posr_qyt'); ?><!--QTY--></th>
            <th class=""> <?php echo $this->lang->line('posr_net_sales'); ?><!--Net Sales--></th>
            <th class=""> <?php echo $this->lang->line('posr_qyt'); ?><!--QTY--></th>
            <th class=""> <?php echo $this->lang->line('posr_net_sales'); ?><!--Net Sales--></th>
            <th class=""> <?php echo $this->lang->line('posr_qyt'); ?><!--QTY--></th>
            <th class=""> <?php echo $this->lang->line('posr_tot_net_sales'); ?><!--Tot Net Sales--></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 0;
        $EatInTotal = 0;
        $EatInQty = 0;
        $TakeAwayTotal = 0;
        $TakeAwayQty = 0;
        $DeliveryOrdersTotal = 0;
        $DeliveryOrdersQty = 0;
        $NetTotal = 0;
        $netQty = 0;
        $tax = 0;
        $totalTax = 0;
        if (!empty($franchiseReport)) {
            foreach ($franchiseReport as $val) {
                $i += 1;
                $EatInTotal += $val['EatInTotal'];
                $EatInQty += $val['EatInQty'];
                $TakeAwayTotal += $val['TakeAwayTotal'];
                $TakeAwayQty += $val['TakeAwayQty'];
                $DeliveryOrdersTotal += $val['DeliveryOrdersTotal'];
                $DeliveryOrdersQty += $val['DeliveryOrdersQty'];
                $NetTotal += $val['NetTotal'];
                $netQty += $val['netQty'];
                $totalTax += $val['totalTax'];
                ?>
                <tr>
                    <td class="" style="text-align: center;"><?php echo $i ?></td>
                    <td class="" style="text-align: center;"><?php echo $val['salesDay'] ?></td>
                    <td class="alin"><?php echo $val['menuSalesDate'] ?></td>
                    <td class="alin"><?php echo $val['EatInQty'] ?></td>
                    <td class="alin"><?php echo number_format($val['EatInTotal'], $d); ?></td>
                    <td class="alin"><?php echo $val['TakeAwayQty'] ?></td>
                    <td class="alin"><?php echo number_format($val['TakeAwayTotal'], $d); ?></td>
                    <td class="alin"><?php echo $val['DeliveryOrdersQty'] ?></td>
                    <td class="alin"><?php echo number_format($val['DeliveryOrdersTotal'], $d); ?></td>
                    <td class="alin"><?php echo $val['netQty'] ?></td>
                    <td class="alin"><?php echo number_format($val['NetTotal'], $d); ?></td>
                </tr>
                <?php


            }
        } else {
            ?>
            <tr>
                <td class="" style="text-align: center;" colspan="11">
                    <?php echo $this->lang->line('common_records_not_found'); ?><!--Records not Found--></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
        <tfoot>
        <tr style="font-size:12px !important;" class="t-foot">
            <td colspan="3" style="padding-right:2px;font-weight: bold; text-align: right"><strong>
                    <?php echo $this->lang->line('common_total'); ?><!--Total--></strong></td>
            <td class="alin" style="font-weight: bold;"><strong><?php echo $EatInQty ?></strong></td>
            <td class="alin" style="font-weight: bold;"><strong><?php echo number_format($EatInTotal, $d); ?></strong>
            </td>
            <td class="alin" style="font-weight: bold;"><strong><?php echo $TakeAwayQty ?></strong></td>
            <td class="alin" style="font-weight: bold;">
                <strong><?php echo number_format($TakeAwayTotal, $d); ?></strong></td>
            <td class="alin" style="font-weight: bold;"><strong><?php echo $DeliveryOrdersQty ?></strong></td>
            <td class="alin" style="font-weight: bold;">
                <strong><?php echo number_format($DeliveryOrdersTotal, $d); ?></strong></td>
            <td class="alin" style="font-weight: bold;"><strong><?php echo $netQty ?></strong></td>
            <td class="alin" style="font-weight: bold;"><strong><?php echo number_format($NetTotal, $d); ?></strong>
            </td>
        </tr>
        <tr style="font-size:12px !important;" class="t-foot">
            <?php
            //echo '<h1>'.$EatInQty.$i.'</h1>';
            $EatInQtyd = $EatInQty / $i;
            $EatInTotald = $EatInTotal / $i;
            $TakeAwayQtyd = $TakeAwayQty / $i;
            $TakeAwayTotald = $TakeAwayTotal / $i;
            $DeliveryOrdersQtyd = $DeliveryOrdersQty / $i;
            $DeliveryOrdersTotald = $DeliveryOrdersTotal / $i;
            $netQtyd = $netQty / $i;
            $NetTotald = $NetTotal / $i;
            ?>
            <td colspan="3" style="padding-right:2px;font-weight: bold; text-align: right"><strong>
                    <?php echo $this->lang->line('posr_daily_avg'); ?><!--Daily Avg--></strong></td>
            <td class="alin" style="font-weight: bold;"><strong><?php echo number_format($EatInQtyd, 2); ?></strong>
            </td>
            <td class="alin" style="font-weight: bold;"><strong><?php echo number_format($EatInTotald, $d); ?></strong>
            </td>
            <td class="alin" style="font-weight: bold;"><strong><?php echo number_format($TakeAwayQtyd, 2); ?></strong>
            </td>
            <td class="alin" style="font-weight: bold;">
                <strong><?php echo number_format($TakeAwayTotald, $d); ?></strong></td>
            <td class="alin" style="font-weight: bold;">
                <strong><?php echo number_format($DeliveryOrdersQtyd, 2); ?></strong></td>
            <td class="alin" style="font-weight: bold;">
                <strong><?php echo number_format($DeliveryOrdersTotald, $d); ?></strong></td>
            <td class="alin" style="font-weight: bold;"><strong><?php echo number_format($netQtyd, 2); ?></strong></td>
            <td class="alin" style="font-weight: bold;"><strong><?php echo number_format($NetTotald, $d); ?></strong>
            </td>
        </tr>
        </tfoot>

    </table>
    <hr>
    <?php
    $royaltyPercentage = get_royalty_percentage();
    if ($royaltyPercentage > 0) {
        //$tax = ($NetTotal * $royalty) / 100;
        $tax = $totalTax;
        $salesWithoutTax = $NetTotal - $tax;
        $royalty = ($salesWithoutTax * $royaltyPercentage) / 100;
    } else {
        echo '<div class="alert alert-danger">Royalty is not assigned for this outlet.</div>';
        exit;
    }
   /* $tax = ($NetTotal * 16) / 100;
    $salesWithoutTax = $NetTotal - $tax;
    $royalty = ($salesWithoutTax * 5) / 100;*/
    ?>
    <div style="margin:4px 0px">
        <b> <strong>  <?php echo $this->lang->line('posr_sales'); ?><!--Sales-->
                :</b> <?php echo number_format($NetTotal, $d); ?>
    </div>
    <div style="margin:4px 0px">
        <?php
        $tax_input = 15;
        $tax_cal= ($NetTotal*$tax_input)/100;
        ?>
        <b> <?php echo $this->lang->line('posr_tax'); ?><!--TAX-->(<?php echo $tax_input ?>%)
            :</b> <?php echo number_format($tax_cal, $d); ?>
    </div>
    <div style="margin:4px 0px">
        <b>  <?php echo $this->lang->line('posr_sales_without_tax'); ?> <!--Sales w/out TAX-->
            :</b> <?php echo number_format($NetTotal-$tax_cal, $d); ?>
    </div>
    <div style="margin:4px 0px">
        <b> <?php echo $this->lang->line('posr_royalty'); ?><!--ROYALTY-->
            (<?php echo $royaltyPercentage ?>%) :</b> <?php echo number_format((($NetTotal-$tax_cal)*$royaltyPercentage)/100, $d); ?>
    </div>
    <hr>
    <div style="margin:4px 0px">
        <?php echo $this->lang->line('posr_report_print'); ?><!--Report print by--> : <?php echo current_user() ?>
    </div>

</div>
<script>
    $(document).ready(function (e) {
        $("#btn_print_itemizedSales").click(function (e) {
            $.print("#printContainer_<?php echo $time ?>");
        });

        var date = new Date,
            hour = date.getHours(),
            minute = date.getMinutes(),
            seconds = date.getSeconds(),
            ampm = hour > 12 ? "PM" : "AM";

        hour = hour % 12;
        hour = hour ? hour : 12; // zero = 12

        minute = minute > 9 ? minute : "0" + minute;
        seconds = seconds > 9 ? seconds : "0" + seconds;
        hour = hour > 9 ? hour : "0" + hour;


        date = hour + ":" + minute + " " + ampm;
        $(".pcCurrentTime").html(date);
    })
    function generateFranchisePdf() {
        var form = document.getElementById('frm_franchise');
        form.target = '_blank';
        form.action = '<?php echo site_url('Pos_restaurant/loadFranchiseReportPdf'); ?>';
        form.submit();
    }
</script>