<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$d = get_company_currency_decimal();
?>

<link rel="stylesheet" href="<?php echo base_url('plugins/pos/gpos-reports.css'); ?>">
<span class="pull-right">
    <button type="button" id="btn_print_itemizedSales" class="btn btn-default btn-xs">
        <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
    </button>

    <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Sales_Report.xls"
       onclick="var file = tableToExcel('printContainer_itemizedSalesReport', 'Item Profitability Report'); $(this).attr('href', file);">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
        </a>
</span>

<div id="printContainer_itemizedSalesReport">
    <div class="text-center">
        <h4 style="margin-top:2px;"><strong><?php echo $companyInfo['company_name'] ?></strong></h4>
    </div>
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="outletInfo">
                <?php
                $outletInput = $this->input->post('outletID_f');
                echo get_outletFilterInfo($outletInput);
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


    <h3 class="text-center"> Item Wise Profitability Report</h3>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <strong>Filters <i class="fa fa-filter"></i></strong><br/> <!--Filtered Date--> <strong>
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
                $tmpArray[] = isset($cashierTmp[$c]) ? $cashierTmp[$c] : '';
            }
            echo join(', ', $tmpArray);
        }
        ?>
    </div>

    <table class="<?php echo table_class_pos(5) ?> customTbl">
        <thead>
        <tr>
            <th class="al"> #</th>
            <th class="al"> Item Description</th>
            <th class="al"> <?php echo $this->lang->line('posr_qyt'); ?><!--Qty--></th>
            <th class="ar"> Total Sales Value</th>
            <th class="ar"> Total Cost</th>
            <th class="ar"> Profit</th>
            <th class="ar"> Profit Margin</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $totalQty = 0;
        $totalAmount = 0;
        $totalWAC = 0;
        $totalProfit = 0;
        if (!empty($reportData)) {
            $i = 1;
            foreach ($reportData as $item) {
                $totalQty += $item['qtySum'];
                $totalAmount += $item['transactionAmountSum'];
                $totalWAC += $item['wacAmountSum'];
                $totalProfit += $item['profit'];
                ?>
                <tr>
                    <td> <?php echo $i;
                        $i++; ?> </td>
                    <td><?php echo $item['itemDescription'] ?></td>
                    <td class="ar"><?php echo number_format($item['qtySum'], $d); ?></td>
                    <td class="ar"><?php echo number_format($item['transactionAmountSum'], $d); ?></td>
                    <td class="ar"><?php echo number_format($item['wacAmountSum'], $d); ?></td>
                    <td class="ar"><?php echo number_format($item['profit'], $d); ?></td>
                    <td class="ar">
                        <?php
                        if ($item['transactionAmountSum'] != 0) {
                            echo number_format(($item['profit'] / $item['transactionAmountSum']) * 100, 2);
                        } else {
                            echo 0;
                        }
                        ?>%
                    </td>
                </tr>
                <?php

            }
        }
        ?>
        </tbody>

        <tfoot>
        <tr style="font-size:15px !important;">
            <td colspan="2"><strong> &nbsp; </strong>
            </td>
            <td class="text-right"><strong><?php echo $totalQty; ?></strong></td>
            <td class="text-right"><strong><?php echo number_format($totalAmount, $d); ?></strong></td>
            <td class="text-right"><strong><?php echo number_format($totalWAC, $d); ?></strong></td>
            <td class="text-right"><strong><?php echo number_format($totalProfit, $d); ?></strong></td>
            <td class="text-right"><?php if ($totalAmount != 0) {
                    echo number_format(($totalProfit / $totalAmount) * 100, 2).' %';
                } else {
                    echo '-';
                } ?>
            </td>
        </tr>
        </tfoot>
    </table>
    <hr>
    <div style="margin:4px 0px">
        <?php echo $this->lang->line('posr_report_print'); ?><!--Report print by--> : <?php echo current_user() ?>
    </div>
</div>
<script>
    $(document).ready(function (e) {
        $("#btn_print_itemizedSales").click(function (e) {
            $.print("#printContainer_itemizedSalesReport");
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

    function generateItemSalesReportPdf() {
        var form = document.getElementById('frm_itemizedSalesReport');
        form.target = '_blank';
        form.action = '<?php echo site_url('Pos_restaurant/loadItemizedSalesReportPdf'); ?>';
        form.submit();
    }

</script>