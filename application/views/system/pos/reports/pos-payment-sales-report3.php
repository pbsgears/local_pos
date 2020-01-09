<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$valuePercentage = 0;
$transPercentage = 0;
$lessTotal = 0;
$paymentTypeTransaction = 0;
$totalCashSales = 0;
$voidedTotal = !empty($voidBills['NetTotal']) ? $voidBills['NetTotal'] : 0;
if (!empty($paymentMethod)) {
    foreach ($paymentMethod as $report2) {
        $netTotal += $report2['NetTotal'];
        $paymentTypeTransaction += $report2['countTransaction'];
    }
}
if (!empty($lessAmounts)) {
    foreach ($lessAmounts as $less) {
        $lessTotal += $less['lessAmount'];
    }
}

$grandTotalCount = 0;
$billCountTotal = 0;
if (!empty($customerTypeCount)) {
    foreach ($customerTypeCount as $report1) {
        $grandTotalCount += $report1['countTotal'];
        $billCountTotal += $report1['subTotal'];

    }
}

$grossTotal = $netTotal + $lessTotal;
$totalBill = $grossTotal + $voidedTotal;

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
//print_r($companyInfo);
//echo $netTotal;
if (!isset($pdf)) {
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

        .customPad {
            padding: 3px 0px;
        }
    </style>
    <span class="pull-right">
    <button type="button" id="btn_print_sales2" class="btn btn-default btn-xs"><i
            class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print--> </button>
        <button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generatePaymentSalesReportPdf()">
                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
        </button>
        <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Sales_Report.xls"
           onclick="var file = tableToExcel('container_sales_report2', 'Outlet Sales Report'); $(this).attr('href', file);">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
        </a>
</span>
<?php } ?>

<div id="container_sales_report2">

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

    <h3 class="text-center"><?php echo $this->lang->line('posr_sales_report'); ?><!--Sales Report--> </h3>

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
    <div style="margin-top:20px; border:1px solid #a3a3a3; padding:5px;">

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <!--<div class="subHeadingTitle"><i class="fa fa-money"></i> Payments</strong></div>-->
                <table class="<?php echo table_class_pos(5) ?>">
                    <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>No. of Trans.</th>
                        <th>&nbsp;</th>
                        <th>Amount</th>
                        <th>%</th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <td> Total Bill</td>

                        <td class="text-right"><?php
                            $voidBillCount = isset($voidBills['countTransaction']) ? $voidBills['countTransaction'] : 0;
                            echo $grandTotalCount + $voidBillCount
                            ?></td>
                        <td class="text-right"></td>
                        <td class="text-right"><?php echo number_format($totalBill, $d) ?></td>
                        <td class="text-right"><?php
                            echo $totalBill > 0 ? number_format(($totalBill / ($totalBill)) * 100, 2) . '%' : 0;
                            ?>  </td>
                    </tr>

                    <tr>
                        <td> Voided Bills</td>
                        <td class="text-right"><?php echo isset($voidBills['countTransaction']) ? $voidBills['countTransaction'] : 0; ?></td>
                        <td></td>
                        <td class="text-right">(<?php echo number_format($voidBills['NetTotal'], $d) ?>)</td>
                        <td class="text-right">
                            <?php
                            echo $totalBill > 0 ? number_format(($voidBills['NetTotal'] / ($totalBill)) * 100, 2) . '%' : 0;
                            ?> </td>
                    </tr>
                    </tbody>


                    <tr>
                        <td><strong>Gross Sales</strong></td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><strong><?php echo number_format($grossTotal, $d) ?></strong></td>
                        <td></td>
                    </tr>

                    <!--<tr>
                        <td colspan="5"></td>
                    </tr>-->

                    <tr>
                        <td>
                            <strong><u> Discount details</u></strong>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php


                    if (!empty($lessAmounts)) {
                        foreach ($lessAmounts as $less) {
                            if ($less['lessAmount'] > 0) {
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $less['customerName'] ?>
                                    </td>
                                    <td></td>
                                    <td class="text-right">
                                        <?php
                                        echo number_format($less['lessAmount'], $d);
                                        ?>
                                    </td>
                                    <td></td>
                                    <td class="text-right"><?php echo number_format(($less['lessAmount'] / ($totalBill)) * 100, 2) . '%' ?> </td>
                                </tr>

                                <?php
                            }
                        }
                        ?>

                        <tr>
                            <td>
                                <strong> Total Discount</strong>
                            </td>
                            <td></td>
                            <td></td>
                            <td class="text-right">
                                <strong>(<?php echo number_format($lessTotal, $d); ?>)</strong>

                            </td>
                            <td class="text-right">
                                <strong><?php echo $totalBill > 0 ? number_format(($lessTotal / ($totalBill)) * 100, 2) . '%' : 0; ?></strong>
                            </td>
                        </tr>

                        <?php
                    }
                    ?>

                    <tr>
                        <td colspan="5"></td>
                    </tr>
                    <tfoot>
                    <tr>
                        <td><strong>Net Sales</strong></td>
                        <td class="text-right"><?php echo $grandTotalCount ?></td>
                        <td></td>
                        <td class="text-right"><strong><?php echo number_format($netTotal, $d) ?></strong></td>
                        <td class="text-right"><?php echo $totalBill > 0 ? number_format(($netTotal / ($totalBill)) * 100, 2) . '%' : 0; ?></td>
                    </tr>


                    <tr>
                        <td colspan="5"></td>
                    </tr>
                    <tr>
                        <td><strong>Average Sales </strong></td>
                        <td class="text-right"></td>
                        <td></td>
                        <td class="text-right">
                            <strong><?php echo $grandTotalCount > 0 ? number_format(($netTotal / $grandTotalCount), $d) : 0; ?></strong>
                        </td>
                        <td></td>
                    </tr>

                    </tfoot>


                </table>


            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                <table class="<?php echo table_class_pos(5) ?>">
                    <thead>
                    <tr>
                        <th> <?php echo $this->lang->line('posr_oder_type'); ?><!--Order Type--></th>
                        <th> No. of Trans.</th>
                        <th> Net Sales</th>
                        <th> %</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    if (!empty($customerTypeCount)) {
                        foreach ($customerTypeCount as $report1) {
                            ?>
                            <tr>
                                <td>
                                    <?php echo $report1['customerDescription'] ?><!--Orders-->
                                </td>

                                <td class="text-right">
                                    <?php echo $report1['countTotal']; ?>
                                </td>
                                <td class="text-right">
                                    <?php echo number_format($report1['subTotal'], $d); ?>
                                </td>
                                <td class="text-right">
                                    <?php echo $billCountTotal > 0 ? number_format((($report1['subTotal'] / $billCountTotal) * 100), 2) . '%' : 0; ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>
                            Total
                        </th>

                        <td class="text-right"><strong><?php echo $grandTotalCount; ?></strong></td>
                        <th class="text-right"><?php echo number_format($billCountTotal, $d) ?></th>
                        <th class="text-right"><?php echo $billCountTotal > 0 ? number_format((($billCountTotal / $billCountTotal) * 100), 2) . '%' : 0; ?></th>
                    </tr>
                    </tfoot>
                </table>

                <div class="hide">
                    <hr>
                    <h4>Sales Summary </h4>

                    <!--Sales -->
                    <div class="row customPad" style="padding: 3px 0px;">
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                            <?php echo $totalSales['Description'] ?>
                        </div>
                        <div class="col-xs-2 col-ce-2 col-ce-2 col-ce-2 text-center"> :</div>
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right">
                            <?php
                            echo number_format($totalSales['amount'], $d);
                            ?>
                        </div>
                    </div>

                    <!--Taxes -->
                    <?php
                    $tmpLess = 0;
                    if (!empty($totalTaxes)) {
                        foreach ($totalTaxes as $totalTax) {
                            ?>
                            <div class="row customPad" style="padding: 3px 0px;">
                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                    <?php echo $totalTax['Description'] ?>
                                </div>
                                <div class="col-xs-2 col-ce-2 col-ce-2 col-ce-2 text-center"> :</div>
                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right">
                                    (<?php
                                    $tmpLess += $totalTax['amount'];
                                    echo number_format($totalTax['amount'], $d);
                                    ?>)
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>

                    <!--Service Charge  -->
                    <?php if (isset($totalServiceCharge['amount']) && !empty($totalServiceCharge['amount'])) {
                        $tmpLess += $totalServiceCharge['amount'];
                    } ?>
                    <div class="row customPad" style="padding: 3px 0px;">
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                            <?php echo $totalServiceCharge['Description'] ?>
                        </div>
                        <div class="col-xs-2 col-ce-2 col-ce-2 col-ce-2 text-center"> :</div>
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right">
                            (<?php
                            echo number_format($totalServiceCharge['amount'], $d);
                            ?>)
                        </div>
                    </div>

                    <!--Net Sales -->
                    <?php
                    if (isset($totalSales['amount']) && $totalSales['amount'] > 0) {
                        ?>
                        <div class="row customPad" style="padding: 3px 0px;">
                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                <h4> Net Sales</h4>
                            </div>
                            <div class="col-xs-2 col-ce-2 col-ce-2 col-ce-2 text-center"> :</div>
                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right">
                                <h4><strong><?php
                                        echo number_format($totalSales['amount'] - $tmpLess, $d);
                                        ?></strong></h4>
                            </div>
                        </div>
                    <?php } ?>
                </div>

            </div>
        </div>
    </div>


    <div style="margin-top:20px; border:1px solid #a3a3a3; padding:5px;">


        <div class="subHeadingTitle"> Sales Collection</strong></div>
        <table class="<?php echo table_class_pos(5) ?>">
            <thead>
            <tr>
                <th>Payment Type</th>
                <th>Transaction</th>
                <th>Trans %</th>
                <th>Value</th>
                <th>Value %</th>
            </tr>
            </thead>
            <tbody>
            <?php

            if (!empty($paymentMethod)) {


                foreach ($paymentMethod as $report2) {
                    if ($report2['NetTotal'] == 0) {
                        continue;
                    }
                    if (strtolower($report2['paymentDescription']) == 'cash') {
                        $totalCashSales += $report2['NetTotal'];
                    }

                    ?>
                    <tr>
                        <td> <?php echo $report2['paymentDescription'] ?></td>
                        <td class="text-right">
                            <?php
                            echo $report2['countTransaction'];
                            ?>
                        </td>
                        <td class="text-right">

                            <?php
                            //echo $netTotal . '<br/>';
                            $paymentTypeTransactionTmp = ($report2['countTransaction'] / $paymentTypeTransaction) * 100;
                            echo number_format($paymentTypeTransactionTmp, 2) . '%';
                            $transPercentage += $paymentTypeTransactionTmp;
                            ?>
                        </td>
                        <td>
                            <div class="text-right">
                                <?php
                                echo number_format($report2['NetTotal'], $d);
                                //$netTotal += $report2['NetTotal'];
                                ?>
                            </div>
                        </td>
                        <td>
                            <div class="text-right">
                                <?php
                                //echo $netTotal . '<br/>';
                                $tmpPer = ($report2['NetTotal'] / $netTotal) * 100;
                                echo number_format($tmpPer, 2) . '%';
                                $valuePercentage += $tmpPer;
                                ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <th> Total Sales</th>
                <th class="text-right">
                    <?php echo number_format($paymentTypeTransaction) ?>
                </th>
                <th class="text-right">
                    <?php echo $transPercentage ?>%
                </th>
                <th class="text-right">
                    <?php echo number_format($netTotal, $d) ?>
                </th>
                <th class="text-right">
                    <?php echo $valuePercentage ?>%
                </th>
            </tr>
            </tfoot>
        </table>



    </div>

    <hr>
    <div style="margin:4px 0px; ">
        <?php echo $this->lang->line('posr_report_print'); ?><!--Report print by--> : <?php echo current_user() ?>
    </div>
</div>
<script>
    function get_currentTime() {
        var date = new Date();
        var nHour = date.getHours(), nMin = date.getMinutes(), nSec = date.getSeconds(), ap;

        if (nHour == 0) {
            ap = " AM";
            nHour = 12;
        }
        else if (nHour < 12) {
            ap = " AM";
        }
        else if (nHour == 12) {
            ap = "PM";
        }
        else if (nHour > 12) {
            ap = "PM";
            nHour -= 12;
        }

        if (nMin <= 9) {
            nMin = "0" + nMin;
        }
        if (nSec <= 9) {
            nSec = "0" + nSec;
        }

        var output = nHour + ":" + nMin + " " + ap;
        return output;

    }

    $(document).ready(function (e) {
        $("#btn_print_sales2").click(function (e) {
            $.print("#container_sales_report2");
        });
        var currentTime = get_currentTime();

        $(".pcCurrentTime").html(currentTime);
    });

    function generatePaymentSalesReportPdf() {
        var form = document.getElementById('frm_salesReport2');
        form.target = '_blank';
        form.action = '<?php echo site_url('Pos_restaurant/loadPaymentSalesReport2'); ?>';
        form.submit();
    }

</script>