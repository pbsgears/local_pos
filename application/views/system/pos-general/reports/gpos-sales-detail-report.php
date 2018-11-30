<?php
$d = $this->common_data['company_data']['company_default_decimal'];
/*$netTotal = 0;
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
$totalBill = $grossTotal + $voidedTotal;*/

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);


if (!isset($pdf)) {
    ?>
    <style>
        .customPad {
            padding: 3px 0px;
        }
    </style>
    <span class="pull-right">
    <button type="button" id="btn_print_sales2" class="btn btn-default btn-xs"> <i
            class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print--> </button>

        <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Sales_Report.xls"
           onclick="var file = tableToExcel('container_sales_report3', 'Sales Report'); $(this).attr('href', file);">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
        </a>
</span>
<?php } ?>
<link rel="stylesheet" href="<?php echo base_url('plugins/pos/gpos-reports.css'); ?>"/>
<div id="container_sales_report3">

    <div class="text-center">
        <h4 style="margin-top:2px;"><strong><?php echo $companyInfo['company_name'] ?></strong></h4>
    </div>
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="outletInfo">
                <?php
                $outletInput = $this->input->post('outletID_f');
                echo get_outletFilterInfo($outletInput);

                if (isset($outletID) && !empty($outletID)) {
                    $tmpArrayout = array();
                    foreach ($outletID as $c) {
                        $tmpArrayout[] = get_outletInfo_byid($c);
                    }
                }
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

    <h3 class="text-center">Sales Detail Report </h3>

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


    <!-- Report Datat -->
    <div style="margin-top:20px; border:1px solid #a3a3a3; padding:5px;">
        <table class="table table-bordered table-condensed table-striped customTbl">
            <thead>
            <tr>
                <th> #</th>
                <th> Date &amp; Time</th>
                <th> Bill ID</th>
                <th> Gross Total</th>
                <th> Total Discount</th>
                <th> Net Total</th>
                <th> Paid Amount</th>
                <th> Balance </th>
                <th> &nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total_sub = 0;
            $total_discount = 0;
            $total_net = 0;
            if (isset($reportData) && !empty($reportData)) {
                $i = 1;
                foreach ($reportData as $data_item) {
                    $totalDiscount = $data_item['discountAmount'] + $data_item['generalDiscountAmount'];

                    $total_sub += $data_item['subTotal'];
                    $total_discount += $totalDiscount;
                    $total_net += $data_item['netTotal'];    ?>
                    <tr>
                        <td> <?php echo $i;
                            $i++; ?></td>
                        <td> <?php echo $data_item['createdDateTime'] ?></td>
                        <td> <?php echo $data_item['invoiceCode'] ?></td>
                        <td class="ar"> <?php echo number_format($data_item['subTotal'], $d) ?></td>
                        <td class="ar">
                            <?php
                            echo number_format($totalDiscount, $d)
                            ?>
                        </td>
                        <td class="ar"> <?php echo number_format($data_item['netTotal'], $d) ?></td>
                        <td class="ar"> <?php echo number_format($data_item['paidAmount'], $d) ?></td>
                        <td class="ar"> <?php echo number_format($data_item['balanceAmount'], $d) ?></td>
                        <td class="ac">
                            <button class="btn btn-xs btn-link" onclick="invoicePrint('<?php echo $data_item['invoiceID'] ?>', '<?php echo $data_item['documentSystemCode']?>', 'PNT')">  View bill
                            </button>
                        </td>

                    </tr>

                    <?php
                }
            }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td class="ar"> <?php echo number_format($total_sub, $d) ?></td>
                    <td class="ar"> <?php echo number_format($total_discount, $d) ?> </td>
                    <td class="ar"> <?php echo number_format($total_net, $d) ?></td>
                    <td colspan="3"></td>

                </tr>
            </tfoot>
        </table>

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