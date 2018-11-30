<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$grossTotal = 0;
$generalDiscountTotal = 0;
$promotionDiscountTotal = 0;
$deliveryCommissionTotal = 0;
$lessTotal = 0;
$netAllRecordTotal = 0;
$DeliveryCommission = 0;
$decimalPlaces = 2;

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
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
                if (isset($outletID) && !empty($outletID)) {
                    $tmpArrayout = array();
                    foreach ($outletID as $c) {
                        $tmpArrayout[] = get_outletInfo_byid($c);
                    }
                    echo join(', ', $tmpArrayout);
                }
                /*$get_outletInfo = get_outletInfo_byid($outletID);
                echo '<strong>' . $get_outletInfo['wareHouseDescription'] . ' - ' . $get_outletInfo['wareHouseCode'] . '</strong><br/>';*/


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

        $paymentCard = 1;
        if (!empty($paymentglConfigMaster)) {
            foreach ($paymentglConfigMaster as $config) {
                if ($config['description'] == 'Credit Sales') {
                    $paymentCard = $paymentCard + 2;
                    echo "<th colspan='2'>" . $config['description'] . "</th>";
                } else {
                    $paymentCard++;
                }
            }
        }
        echo $paymentCard;
        /*echo '<h1>XX </h1>';
        exit;*/
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
                        <th colspan="2">General Discount</th>
                        <th colspan="2">Promotional Discount</th>
                        <th colspan="2">Delivery Commission</th>
                        <th rowspan="3">Net Total</th>
                        <th colspan="<?php echo $paymentCard ?>">Paid By</th>
                    </tr>
                    <tr>
                        <th rowspan="2">(%)</th>
                        <th rowspan="2">Amount</th>
                        <th rowspan="2">Type (%)</th>
                        <th rowspan="2">Amount</th>
                        <th rowspan="2">Type (%)</th>
                        <th rowspan="2">Amount</th>
                        <?php
                        if (!empty($paymentglConfigMaster)) {
                            foreach ($paymentglConfigMaster as $config) {
                                if ($config['description'] == 'Credit Sales') {
                                    echo "<th colspan='2'>" . $config['description'] . "</th>";
                                } else {
                                    echo "<th rowspan='2'>" . $config['description'] . "</th>";
                                }
                            }
                        }
                        ?>
                    </tr>
                    <tr>
                        <th>Customer</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $zx = 1;
                    $DeliveryCommission = 0;
                    $promotionDiscountAmount = 0;
                    $array_total = array();
                    if (!empty($recordDetail)) {
                        foreach ($recordDetail as $detail) {
                            ?>
                            <tr>
                                <td><?php echo $zx ?></td>
                                <td><?php echo $detail['salesMasterCreatedDate'] . "<br>" . $detail['salesMasterCreatedTime'] ?></td>
                                <td><a href="#"
                                       onclick="viewDrillDown_report(<?php echo $detail['salesMasterMenuSalesID']; ?>)"><?php echo $detail['invoiceCode'] ?></a>
                                </td>
                                <td><?php echo $detail['whouseName'] ?></td>
                                <td><?php echo $detail['menuCreatedUser'] ?></td>
                                <td class="amountAlign"><?php echo number_format($detail['grossAmount'], $detail['companyLocalDecimal']); ?></td>
                                <td><?php echo round($detail['discountPer'], 1) . " %" ?></td>
                                <td class="amountAlign"><?php echo number_format($detail['discountAmount'], $detail['companyLocalDecimal']) ?></td>
                                <td><?php if (!empty($detail['PromotionalDiscountType'])) {
                                        echo $detail['PromotionalDiscountType'] . "<br>" . round($detail['promotionDiscount'], 1) . " %";
                                    } else {
                                        echo round($detail['promotionDiscount'], 1) . " %";
                                    } ?>
                                </td>
                                <td class="amountAlign">
                                    <?php
                                    $promotionDiscountAmount = (($detail['grossAmount'] - ($detail['discountAmount'])) / 100) * $detail['promotionDiscount'];
                                    echo number_format($promotionDiscountAmount, $detail['companyLocalDecimal'])
                                    ?>
                                </td>
                                <td><?php if (!empty($detail['DeliveryCommissionType'])) {
                                        echo $detail['DeliveryCommissionType'] . "<br>" . round($detail['deliveryCommission'], 1) . " %";
                                    } else {
                                        echo round($detail['deliveryCommission'], 1) . " %";
                                    } ?>
                                </td>
                                <td class="amountAlign"><?php echo number_format($detail['deliveryCommissionAmount'], $detail['companyLocalDecimal']) ?></td>
                                <td class="amountAlign">
                                    <?php
                                    $netRowTotal = $detail['billNetTotal'] - $detail['deliveryCommissionAmount'];
                                    echo number_format($netRowTotal, $detail['companyLocalDecimal']); ?>
                                </td>
                                <?php
                                if (!empty($paymentglConfigMaster)) {
                                    foreach ($paymentglConfigMaster as $config) {
                                        $newName = str_replace(' ', '', $config['description']);
                                        if ($newName == 'CreditSales') { ?>
                                            <td style="width: 10px;"><?php echo $detail['customerName']; ?>
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
                                ?>
                            </tr>
                            <?php
                            $decimalPlaces = $detail['companyLocalDecimal'];
                            $grossTotal += $detail['grossAmount'];
                            $generalDiscountTotal += $detail['discountAmount'];
                            $promotionDiscountTotal += $promotionDiscountAmount;
                            $deliveryCommissionTotal += $detail['deliveryCommissionAmount'];
                            $netAllRecordTotal += $netRowTotal;
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
                            <th class="text-right"><?php echo number_format($generalDiscountTotal, $decimalPlaces); ?></th>
                            <th>&nbsp;</th>
                            <th class="text-right"><?php echo number_format($promotionDiscountTotal, $decimalPlaces); ?></th>
                            <th>&nbsp;</th>
                            <th class="text-right"><?php echo number_format($deliveryCommissionTotal, $decimalPlaces); ?></th>
                            <th class="text-right"><?php echo number_format($netAllRecordTotal, $decimalPlaces); ?></th>
                            <?php
                            if (!empty($paymentglConfigMaster)) {
                                foreach ($paymentglConfigMaster as $config) {
                                    $newName = str_replace(' ', '', $config['description']);
                                    if ($newName == 'CreditSales') {
                                        echo "<th>&nbsp;</th>";
                                        echo "<th class='text-right'>" . number_format(array_sum($array_total[$newName]), $decimalPlaces) . "</th>";
                                    } else {
                                        echo "<th class='text-right'>" . number_format(array_sum($array_total[$newName]), $decimalPlaces) . "</th>";
                                    }

                                }
                            }
                            ?>
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