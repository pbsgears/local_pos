<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$grossTotal = 0;
$generalDiscountTotal = 0;
$promotionDiscountTotal = 0;
$deliveryCommissionTotal = 0;
$lessTotal = 0;
$netAllRecordTotal = 0;
$totalpaid = 0;
$tot = 0;
$total_paid_amt = 0;
$DeliveryCommission = 0;
$decimalPlaces = 2;
$totdisamount = 0;
$tot_balance_amt = 0;
$nettot = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

//print_r($companyInfo);
//echo $netTotal;
?>
<style>
    .outletInfo {

    }

    .subHeadingTitle {
        margin-top: 10px;
        color: #cd3b43;
        font-size: 15px;
        font-weight: bold;
        text-decoration: underline;
    }

    .amountAlign {
        text-align: right;
    }
</style>
<?php
if (!isset($pdf)) {
    ?>
    <style>
        .customPad {
            padding: 3px 0px;
        }
    </style>
    <span class="pull-right">
    <button type="button" id="btn_print_sales2" class="btn btn-default btn-xs"><i
            class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print--> </button>
        <!--        <button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generatePaymentSalesReportPdf()">
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                </button>-->
        <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Sales_Report.xls"
           onclick="var file = tableToExcel('container_sales_report3', 'Sales Report'); $(this).attr('href', file);">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
        </a>
</span>
<?php } ?>
<div id="container_sales_report3">

    </span>
    <div class="text-center">
        <h4 style="margin-top:2px;"><strong><?php echo $companyInfo['company_name'] ?></strong></h4>
    </div>
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="outletInfo">
                <?php
                $get_outletInfo = get_outletInfo();
                //var_dump($get_outletInfo);
                if (!empty($get_outletInfo['warehouseImage'])) {
                    echo '<img src="' . base_url('uploads/warehouses/' . $get_outletInfo['warehouseImage']) . '" style="max-height:60px;" /><br/>';
                }
                echo '<strong>' . $get_outletInfo['wareHouseDescription'] . ' - ' . $get_outletInfo['wareHouseCode'] . '</strong><br/>';
                echo $get_outletInfo['warehouseAddress'] . '<br/>';
                echo $get_outletInfo['wareHouseLocation'] . '<br/>';
                echo $get_outletInfo['warehouseTel'] . '<br/>';

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

    <h3 class="text-center"><?php echo $this->lang->line('posr_sales_detail_report'); ?><!--Sales Report--> </h3>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <strong>Filters <i class="fa fa-filter"></i></strong><br/>

            <?php //echo $this->lang->line('posr_filtered_date'); ?><!--Filtered Date--> <strong>
                <?php
                $filterFrom = $this->input->post('filterFrom');
                $filterTo = $this->input->post('filterTo');
                $today = $this->lang->line('posr_today');
                if (!empty($filterFrom) && !empty($filterTo)) {
                    echo '  <i>Date from : </i>' . $filterFrom . ' - <i> To: </i>' . $filterTo;
                } else {
                    $curDate = date('d-m-Y');
                    echo $curDate . ' (' . $today . ')';/*Today*/
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
                $tmpArray[] = $cashierTmp[$c];
            }
            echo join(', ', $tmpArray);
        }
        ?>
    </div>
    <!--*********** -->
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"
         style="margin-top:20px; padding:5px;">

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <!--<div class="subHeadingTitle"><i class="fa fa-money"></i> Payments</strong></div>-->
                <table class="<?php echo table_class_pos(5) ?>">
                    <thead>
                    <tr>
                        <th rowspan="3" width="50px">#</th>
                        <th rowspan="3" style="table-layout: fixed; width: 100px;">Date&nbsp;&&nbsp;Time</th>
                        <th rowspan="3">Bill No</th>
                        <th rowspan="3">Outlet</th>
                        <th rowspan="3">Cashier</th>
                        <th rowspan="3">Gross Amount</th>
                        <th colspan="2">Total Discount</th>

                        <th colspan="13"></th>

                    </tr>
                    <tr>
                        <th rowspan="2">(%)</th>
                        <th rowspan="2">Amount</th>
                        <th rowspan="2">Net Total</th>
                        <?php
                        if (!empty($paymentglConfigMaster)) {
                            foreach ($paymentglConfigMaster as $config) {
                                if ($config['autoID'] != '26' && $config['autoID'] != '25') {
                                    if ($config['description'] == 'Credit Sales') {
                                        echo "<th colspan='2'>" . $config['description'] . "</th>";
                                    } else {
                                        echo "<th rowspan='2'>" . $config['description'] . "</th>";
                                    }
                                }
                            }
                        }

                        ?>
                        <th colspan="3"></th>
                    </tr>
                    <tr>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Total Paid</th>
                        <th>Balance</th>
                        <th>Dispatched</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $zx = 1;
                    $discountAmount = 0;
                    $promotionDiscountAmount = 0;
                    $discountpercentage = 0;
                    $totaldiscountAmount = 0;

                    $array_total = array();
                    if (!empty($recordDetail)) {
                        foreach ($recordDetail as $detail) {
                            $totaldiscountAmount = (($detail['discountAmount'] - ($detail['promotionDiscount'])));
                            if (!empty($totaldiscountAmount)) {
                                $discountpercentage = ((($totaldiscountAmount) / $detail['grossAmount']) * 100);
                            } else {
                                $discountpercentage = 0;
                            }
                            $totalpaid = (($detail['Cash'] + $detail['MasterCard'] + $detail['VisaCard'] + $detail['AMEX'] + $detail['GiftCard'] + $detail['CreditNote']));
                            $nettotal = (($detail['grossAmount'] - $totaldiscountAmount));
                            $tot = $nettotal - $totalpaid;
                            ?>
                            <tr>
                                <td><?php echo $zx ?></td>
                                <td><?php echo $detail['rptDate']  ?></td>
                                <td><a href="#"
                                       onclick="viewDrillDown_report(<?php echo $detail['salesMasterMenuSalesID']; ?>)"><?php echo $detail['invoiceCode'] ?></a>
                                </td>
                                <td><?php echo $detail['wareHouseCode'] ?></td>
                                <td><?php echo $detail['menuCreatedUser'] ?></td>
                                <td class="amountAlign"><?php echo number_format($detail['grossAmount'], $detail['companyLocalDecimal']); ?></td>
                                <td><?php echo round($discountpercentage, 1) . " %" ?></td>
                                <td class="amountAlign">
                                    <?php
                                    echo number_format($totaldiscountAmount, $detail['companyLocalDecimal'])
                                    ?>
                                </td>
                                <td>  <?php
                                    echo number_format($nettotal, $detail['companyLocalDecimal'])
                                    ?></td>
                                <?php
                                if (!empty($paymentglConfigMaster)) {
                                    foreach ($paymentglConfigMaster as $config) {
                                        if ($config['autoID'] != '26' && $config['autoID'] != '25') {
                                            $newName = str_replace(' ', '', $config['description']);
                                            if ($newName == 'CreditSales') { ?>
                                                <td style="width: 10px;">
                                                    <?php
                                                    if (!empty($detail['customerName'])) {
                                                        echo $detail['customerName'];
                                                    } else {
                                                        echo $detail['DeliveryCustomerName'];
                                                    }
                                                    ?>
                                                </td>
                                                <td class="amountAlign">
                                                    <?php
                                                    echo number_format($detail[$newName], $detail['companyLocalDecimal']);
                                                    $array_total[$newName][] = $detail[$newName];
                                                    ?>
                                                </td>
                                                <?php
                                            } else {
                                                ?>
                                                <td class="amountAlign">
                                                    <?php
                                                    echo number_format($detail[$newName], $detail['companyLocalDecimal']);
                                                    $array_total[$newName][] = $detail[$newName];

                                                    ?>
                                                </td>
                                                <?php
                                            }
                                        }
                                    }
                                }
                                ?>
                                <td class="amountAlign">
                                    <?php echo number_format($totalpaid, $detail['companyLocalDecimal']) ?>
                                </td>
                                <td class="amountAlign"><?php echo number_format($tot, $detail['companyLocalDecimal']) ?></td>
                                <?php
                                $isdispatched = trim($detail['deliveryordersDispatched']);
                                if($isdispatched == 'Yes'){
                                    echo "<td>Yes</td>";
                                }else if($isdispatched == 'No'){
                                    echo "<td>No</td>";
                                }else{
                                    echo "<td>Yes</td>";
                                }
                                //echo "<td>$isdispatched</td>";
                                ?>
                            </tr>
                            <?php
                            $decimalPlaces = $detail['companyLocalDecimal'];
                            $grossTotal += $detail['grossAmount'];
                            $generalDiscountTotal += $detail['discountAmount'];
                            $totdisamount += $totaldiscountAmount;
                            $total_paid_amt += $totalpaid;
                            $tot_balance_amt += $tot;
                            $nettot += $nettotal;
                            $zx++;
                        }
                    } else { ?>
                        <tr>
                            <td colspan="22" style="text-align: center">No Records Found</td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <?php
                    if (!empty($recordDetail)) { ?>
                        <tr>
                            <th colspan="5">Total</th>
                            <th class="text-right"><?php echo number_format($grossTotal, $decimalPlaces); ?></th>
                            <th>&nbsp;</th>
                            <th class="text-right"><?php echo number_format($totdisamount, $decimalPlaces); ?></th>
                            <th class="text-right"><?php echo number_format($nettot, $decimalPlaces); ?></th>
                            <?php
                            if (!empty($paymentglConfigMaster)) {
                                foreach ($paymentglConfigMaster as $config) {
                                    if ($config['autoID'] != '26' && $config['autoID'] != '25') {
                                        $newName = str_replace(' ', '', $config['description']);
                                        if ($newName == 'CreditSales') {
                                            echo "<th>&nbsp;</th>";
                                            echo "<th class='text-right'>" . number_format(array_sum($array_total[$newName]), $decimalPlaces) . "</th>";
                                        } else {
                                            echo "<th class='text-right'>" . number_format(array_sum($array_total[$newName]), $decimalPlaces) . "</th>";
                                        }

                                    }
                                }
                            }
                            ?>
                            <th class="text-right"><?php echo number_format($total_paid_amt, $decimalPlaces); ?></th>
                            <th class="text-right"><?php echo number_format($tot_balance_amt, $decimalPlaces); ?></th>
                            <th class="text-right"></th>
                        </tr>
                    <?php } ?>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>

<hr>
<div style="margin:4px 0px; ">
    <?php echo $this->lang->line('posr_report_print'); ?><!--Report print by--> : <?php echo current_user() ?>
</div>
</div>
<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>
<script>

    $(document).ready(function (e) {
        $("#btn_print_sales2").click(function (e) {
            $.print("#container_sales_report3");
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

    function generatePaymentSalesReportPdf() {
        var form = document.getElementById('frm_salesReport');
        form.target = '_blank';
        form.action = '<?php echo site_url('Pos_restaurant/loadPaymentSalesReportPdf'); ?>';
        form.submit();
    }


</script>