<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
//print_r($companyInfo);
?>
<?php
if (!isset($pdf)) {
    ?>
    <style>
        .customPad {
            padding: 3px 0px;
        }
    </style>
    <span class="pull-right">
    <button type="button" id="btn_print_sales" class="btn btn-default btn-xs"> <i
            class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print--> </button>
    <button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generatePaymentSalesReportPdf()"> <i
            class="fa fa-file-pdf-o"
            aria-hidden="true"></i> PDF </button>
</span>
<?php } ?>
<div id="container_sales_report">

    </span>
    <div class="text-center">
        <h3 style="margin-top:2px;"><?php echo $companyInfo['company_name'] ?></h3>
        <?php if (!empty($companyInfo['company_address1'])) {
            ?>
            <h4 style="margin:0px;">
                <?php
                echo $companyInfo['company_address1'];
                if (!empty($companyInfo['company_city'])) {
                    echo ', ' . $companyInfo['company_city'];
                }
                ?>
            </h4>
            <?php
        }
        ?>
    </div>

    <div style="margin:4px 0px; text-align: center;">
        <?php
        $cash = $this->lang->line('posr_cashier');

        if (isset($cashier) && !empty($cashier)) {
            echo '' . $cash . ': ';/*Cashier*/
            /*<strong> ' . $cashier.'</strong>';*/
            //print_r($cashier);
            //print_r($cashierTmp);
            $tmpArray = array();
            foreach ($cashier as $c) {
                $tmpArray[] = $cashierTmp[$c];
                //$key = array_search($c, array_column($cashierTmp, 'uid'));
            }
            echo join(', ', $tmpArray);
        }
        ?>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        	<span>
        <?php echo $this->lang->line('posr_filtered_date'); ?><!--Filtered Date--> : <strong>
            <?php
            $filterFrom = $this->input->post('filterFrom');
            $filterTo = $this->input->post('filterTo');
            $today = $this->lang->line('posr_today');
            if (!empty($filterFrom) && !empty($filterTo)) {
                echo '  <i>from : </i>' . $filterFrom . ' - <i> To: </i>' . $filterTo;
            } else {
                $curDate = date('d-m-Y');
                echo $curDate . ' (' . $today . ')';/*Today*/
            }
            ?>
        </strong>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <?php echo $this->lang->line('common_date'); ?><!--Date-->: <strong><?php echo date('d/m/Y'); ?></strong>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
            <?php echo $this->lang->line('posr_time'); ?><!--Time-->: <strong><span id="pcCurrentTime"></span></strong>
        </div>
    </div>
    <hr style="margin:2px 0px;">

    <h4><?php echo $this->lang->line('posr_sales_report'); ?><!--Sales Report--> </h4>

    <hr>

    <?php
    /*echo '<pre>';
    print_r($paymentMethod);
    print_r($lessAmounts);
    echo '</pre>';*/
    $lessTotal = 0;
    if (!empty($paymentMethod)) {

        foreach ($paymentMethod as $report2) {
            if ($report2['NetTotal'] == 0) {
                continue;
            }
            ?>
            <div class="row customPad" style="padding: 3px 0px;">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5"><?php echo $report2['paymentDescription'] ?></div>
                <div class="col-xs-2 col-ce-2 col-ce-2 col-ce-2 text-center"> :</div>
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right"> <?php
                    echo number_format($report2['NetTotal'], $d);
                    $netTotal += $report2['NetTotal'];
                    ?></div>
            </div>
            <?php


            if (strtolower($report2['paymentDescription']) == 'cash') {


                if (!empty($lessAmounts)) {
                    ?>
                    <hr>
                    <div class="row customPad" style="padding: 3px 0px;">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><strong>
                                <?php echo $this->lang->line('posr_less'); ?><!--Less--></strong></div>
                    </div>
                    <?php
                    foreach ($lessAmounts as $less) {
                        $lessTotal += $less['lessAmount'];
                        ?>
                        <div class="row customPad" style="padding: 3px 0px;">
                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $less['customerName'] ?>
                            </div>
                            <div class="col-xs-2 col-ce-2 col-ce-2 col-ce-2 text-center"> :</div>
                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right">
                                <?php
                                echo number_format($less['lessAmount'], $d);
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="row customPad" style="padding: 3px 0px;">
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                            <strong>
                                <?php echo $this->lang->line('posr_total_deductions'); ?><!--Total Deductions --></strong>
                        </div>
                        <div class="col-xs-2 col-ce-2 col-ce-2 col-ce-2 text-center"> :</div>
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right">
                            <strong> (<?php echo number_format($lessTotal, $d); ?>) </strong>
                        </div>
                    </div>
                    <hr>

                    <div class="row customPad" style="padding: 3px 0px;">
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                            <strong>
                                <?php echo $this->lang->line('posr_total_net_cash'); ?><!--Total Net Cash--> </strong>
                        </div>
                        <div class="col-xs-2 col-ce-2 col-ce-2 col-ce-2 text-center"> :</div>
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right">
                            <strong> <?php echo number_format($report2['NetTotal'] - $lessTotal, $d); ?> </strong>
                        </div>
                    </div>

                    <hr>
                    <?php
                }
                ?>

                <?php
            }
            ?>
            <?php
        }
    }
    ?>

    <hr style="margin:2px 0px">
    <div class="row" style="font-size:14px;">
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5"><strong>
                <?php echo $this->lang->line('posr_grand_total'); ?><!--Grand Total--> </strong></div>
        <div class="col-xs-2 col-ce-2 col-ce-2 col-ce-2 text-center"> :</div>
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right">
            <strong><?php echo number_format($netTotal, $d) ?></strong></div>
    </div>


    <hr>

    <h4><?php echo $this->lang->line('posr_bill_count'); ?><!--Bill Count--></h4>
    <table class="<?php echo table_class_pos() ?>">
        <thead>
        <tr>
            <th> <?php echo $this->lang->line('posr_oder_type'); ?><!--Order Type--></th>
            <th>
                <?php echo $this->lang->line('posr_oder_type'); ?><!--No of Bills--> <?php //echo $this->common_data['company_data']['company_default_currency']; ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $grandTotalCount = 0;
        if (!empty($customerTypeCount)) {
            foreach ($customerTypeCount as $report1) {
                ?>
                <tr>
                    <td><strong> <?php echo str_replace("Orders", "", $report1['customerDescription']) ?>
                            <?php echo $this->lang->line('posr_oders'); ?><!--Orders--></strong></td>
                    <td class="text-right">
                        <strong>
                            <?php
                            echo $report1['countTotal'];
                            $grandTotalCount += $report1['countTotal'];
                            ?>
                        </strong>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td><strong>
                    <?php echo $this->lang->line('posr_total_number_of_bills'); ?><!--Total Number of Bills--></strong>
            </td>

            <td class="text-right"><strong><?php echo $grandTotalCount; ?></strong></td>
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
        $("#btn_print_sales").click(function (e) {
            $.print("#container_sales_report");
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
        $("#pcCurrentTime").html(date);
    })

    function generatePaymentSalesReportPdf() {
        var form = document.getElementById('frm_salesReport');
        form.target = '_blank';
        form.action = '<?php echo site_url('Pos_restaurant/loadPaymentSalesReportPdf'); ?>';
        form.submit();
    }


</script>