<style>
    .customPad {
        padding: 3px 0px;
    }
</style>
<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
//print_r($companyInfo);
?>
<span class="pull-right">
    <button type="button" id="btn_print_sales" class="btn btn-default btn-xs"><i
            class="fa fa-print"></i> Print
    </button>
    <button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generatePaymentSalesReportPdf()"><i
            class="fa fa-file-pdf-o"
            aria-hidden="true"></i> PDF
    </button>
</span>
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
        if (isset($cashier) && !empty($cashier)) {
            echo 'Cashier: ';
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
        Filtered Date : <strong>
                    <?php
                    $filterFrom = $this->input->post('filterFrom');
                    $filterTo = $this->input->post('filterTo');
                    if (!empty($filterFrom) && !empty($filterTo)) {
                        echo '  <i>from : </i>' . $filterFrom . ' - <i> To: </i>' . $filterTo;
                    } else {
                        $curDate = date('d-m-Y');
                        echo $curDate . ' (Today)';
                    }
                    ?>
                </strong>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            Date: <strong><?php echo date('d/m/Y'); ?></strong>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
            Time: <strong><span id="pcCurrentTime"></span></strong>
        </div>
    </div>
    <hr style="margin:2px 0px;">


    <h4>Sales Report </h4>


    <div class="row customPad">
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Cash</div>
        <div class="col-xs-2 col-ce-2 col-ce-2 col-ce-2 text-center"> :</div>
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right"> <?php echo number_format($paymentMethod['cashAmount'], $d); ?></div>
    </div>
    <div class="row customPad">
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Card</div>
        <div class="col-xs-2 col-ce-2 col-ce-2 col-ce-2 text-center"> :</div>
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right"> <?php echo number_format($paymentMethod['cardAmount'], $d); ?></div>
    </div>
    <div class="row customPad">
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Cheque</div>
        <div class="col-xs-2 col-ce-2 col-ce-2 col-ce-2 text-center"> :</div>
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right"> <?php echo number_format($paymentMethod['chequeAmount'], $d); ?></div>
    </div>
    <div class="row customPad">
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Credit Note</div>
        <div class="col-xs-2 col-ce-2 col-ce-2 col-ce-2 text-center"> :</div>
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right"> <?php echo number_format($paymentMethod['creditNoteAmount'], $d); ?></div>
    </div>
    <div class="row customPad">
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">Refund</div>
        <div class="col-xs-2 col-ce-2 col-ce-2 col-ce-2 text-center"> :</div>
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right"> - <?php echo number_format($isRefund['refundAmount'], $d); ?></div>
    </div>


    <hr style="margin:2px 0px">
    <?php
    $tot=$paymentMethod['netTotal']-$isRefund['refundAmount']
    ?>
    <div class="row" style="font-size:14px;">
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5"><strong> Grand Total </strong></div>
        <div class="col-xs-2 col-ce-2 col-ce-2 col-ce-2 text-center"> :</div>
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right">
            <strong><?php echo number_format($tot, $d); ?></strong></div>
    </div>
    <hr style="margin:2px 0px">
    <div class="row" style="font-size:14px;">
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5"><strong> Bill Count </strong></div>
        <div class="col-xs-2 col-ce-2 col-ce-2 col-ce-2 text-center"> :</div>
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 text-right">
            <strong> <?php echo $customerTypeCount['billCount']; ?></strong></div>
    </div>
    <hr>
    <div style="margin:4px 0px">
        Report print by : <?php echo current_user() ?>
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
        form.action = '<?php echo site_url('Pos_dashboard/loadGeneralPaymentSalesReportPdf'); ?>';
        form.submit();
    }


</script>