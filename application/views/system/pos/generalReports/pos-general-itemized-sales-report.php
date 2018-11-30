<style>
    .customPad {
        padding: 3px 0px;
    }

    .al {
        text-align: left !important;
        padding-left: 2px;
    }

    .ar {
        text-align: right !important;
        padding-right: 2px;
    }

</style>
<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;

?>
<span class="pull-right">
    <button type="button" id="btn_print_itemizedSales" class="btn btn-default btn-xs">
        <i class="fa fa-print"></i> Print
    </button>
    <button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generateItemSalesReportPdf()"> <i
            class="fa fa-file-pdf-o"
            aria-hidden="true"></i> PDF </button>
</span>
<div id="printContainer_itemizedSalesReport">
    <div class="text-center">
        <h3 style="margin-top:2px;"><?php echo $companyInfo['company_name'] ?></h3>
        <h4 style="margin:0px;"><?php echo $companyInfo['company_address1'] . ', ' . $companyInfo['company_city'] ?></h4>
    </div>
    <div style="margin:4px 0px; text-align: center;">
        <?php
        if (isset($cashier) && !empty($cashier)) {
            echo 'Cashier: ';

            $tmpArray = array();
            foreach ($cashier as $c) {
                $tmpArray[] = $cashierTmp[$c];
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


    <br>
    <table class="<?php //echo table_class_pos() ?>" style="width: 100%; " border="1">
        <thead>
        <tr>
            <th class="al"> Description</th>
            <th style="text-align: center;"> Qty</th>
            <th class="ar"> Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $grandTotal = 0;

        if (!empty($itemizedSalesReport)) {
            foreach ($itemizedSalesReport as $val) {

                $qty=0;
                $price=0;

                if($val['returnqty']>0){
                    $qty=$val['qty']-$val['returnqty'];
                }else{
                    $qty=$val['qty'];
                }

                if($val['returnprice']>0){
                    $price=$val['price']-$val['returnprice'];
                }else{
                    $price=$val['price'];
                }
                if($price>0){
                    ?>
                    <tr>
                        <td class="al"><?php echo $val['itemDescription'] ?></td>
                        <td style="text-align: center;"><?php echo $qty ?></td>
                        <td class="ar"><?php echo number_format($price, $d); ?></td>
                    </tr>
                    <?php
                    $grandTotal += $price;
                }
            }
        }else{
            ?>
            <tr>
                <td colspan="3" style="text-align: center;">No Records Found</td>
            </tr>

        <?php
        }

        ?>
        </tbody>
        <tfoot>
        <tr style="font-size:13px !important;">
            <th colspan="2"> Grand Total </th>
            <th class="ar"><?php echo number_format($grandTotal, $d); ?></th>
        </tr>
        </tfoot>
    </table>
    <hr>
    <div style="margin:4px 0px">
        Report print by : <?php echo current_user() ?>
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
        $("#pcCurrentTime").html(date);
    })

    function generateItemSalesReportPdf() {
        var form = document.getElementById('frm_itemizedSalesReport');
        form.target = '_blank';
        form.action = '<?php echo site_url('Pos_dashboard/loadGeneralItemizedSalesReportPdf'); ?>';
        form.submit();
    }

</script>