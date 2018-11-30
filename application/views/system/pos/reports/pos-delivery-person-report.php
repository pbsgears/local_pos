<style>
    .customPad {
        padding: 3px 0px;
    }

    /*.al {
        text-align: left !important;
    }*/

    .ar {
        text-align: right !important;
    }

    .alin{
        text-align: center !important;
    }

</style>
<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
/*echo '<pre>';
print_r($itemizedSalesReport);
echo '</pre>';*/
?>
<span class="pull-right">
    <button type="button" id="btn_print_itemizedSales" class="btn btn-default btn-xs">
        <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print');?><!--Print-->
    </button>
    <button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generateDeliveryPersonPdf()"><i
            class="fa fa-file-pdf-o"
            aria-hidden="true"></i> PDF
    </button>
</span>
<div id="printContainer_itemizedSalesReport">
    <div class="text-center">
        <h3 style="margin-top:2px;"><?php echo $companyInfo['company_name'] ?></h3>
        <h4 style="margin:0px;"><?php echo $companyInfo['company_address1'] . ', ' . $companyInfo['company_city'] ?></h4>
    </div>
    <div style="margin:4px 0px; text-align: center;">
        <?php
        $cash = $this->lang->line('posr_cashier');
        if (isset($cashier) && !empty($cashier)) {
            echo ''.$cash.': ';/*Cashier*/

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
        <?php echo $this->lang->line('posr_filtered_date');?><!--Filtered Date -->: <strong>
                    <?php
                    $filterFrom = $this->input->post('filterFrom');
                    $filterTo = $this->input->post('filterTo');
                    $from = $this->lang->line('common_from');
                    $to = $this->lang->line('common_to');
                    $today = $this->lang->line('posr_today');
                    if (!empty($filterFrom) && !empty($filterTo)) {
                        echo '  <i>'.$from.'<!--From--> : </i>' . $filterFrom . ' - <i> '.$to.': </i>' . $filterTo;/*To*/
                    } else {
                        $curDate = date('d-m-Y');
                        echo $curDate . ' ('.$today.')';
                    }
                    ?>
                </strong>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <?php echo $this->lang->line('common_date');?><!--Date-->: <strong><?php echo date('d/m/Y'); ?></strong>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
            <?php echo $this->lang->line('posr_time');?><!--Time-->: <strong><span id="pcCurrentTime"></span></strong>
        </div>
    </div>
    <?php
/*    if($this->input->post('customerID')>0){
        */?><!--
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            Promotions Or Order: <strong><?php /*echo $person['customerName']; */?></strong>
        </div>

    </div>
        --><?php
/*    }
    */?>


    <br>
    <table class="" style="width: 100%; " border="1">
        <thead>
        <tr>
            <th class=""> #</th>
            <th class=""> <?php echo $this->lang->line('posr_invoice_id');?><!--Invoice ID--></th>
            <th class=""> <?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th class=""><?php echo $this->lang->line('posr_bill_date');?><!-- Bill Date--></th>
            <th class=""> <?php echo $this->lang->line('posr_bill_time');?><!--Bill Time--></th>
            <th class=""> <?php echo $this->lang->line('posr_bill_amount');?><!--Bill Amount--></th>
            <th class=""> <?php echo $this->lang->line('posr_commission');?><!--Commission--></th>
            <th class=""> <?php echo $this->lang->line('posr_commission_amount');?><!--Commission Amount--></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $TotalBill = 0;
        $TotalCommission = 0;
        $i = 1;
        if (!empty($deliveryPersonReport)) {
            foreach ($deliveryPersonReport as $val) {
                $TotalBill += $val['subTotal'];
                $TotalCommission += $val['deliveryCommissionAmount'];
                ?>
                <tr>
                    <td class="alin"><?php echo $i ?></td>
                    <td class="alin"><?php echo $val['menuSalesID'] ?></td>
                    <td class="alin"><?php echo $val['customerName'] ?></td>
                    <td class="alin"><?php echo $val['billdate'] ?></td>
                    <td class="alin"><?php echo $val['billtime'] ?></td>
                    <td class="ar"><?php echo number_format($val['subTotal'], $d); ?></td>
                    <td class="alin"><?php echo $val['deliveryCommission']  ?> %</td>
                    <td class="ar"><?php echo number_format($val['deliveryCommissionAmount'], $d);  ?></td>
                </tr>



                <?php

                $i=$i+1;
            }
        }else{
            ?>
        <tr>
            <td class="alin" colspan="8"><?php echo $this->lang->line('common_records_not_found');?><!--Records not Found--></td>
        </tr>
            <?php
        }
        ?>
        </tbody>
        <tfoot>
        <tr style="font-size:12px !important;" class="t-foot">
            <td colspan="5" style="padding-right:2px;font-weight: bold; text-align: right"><strong>  <?php echo $this->lang->line('posr_total_bill_amount');?><!--Total Bill Amount--></strong></td>
            <td class="" style="font-weight: bold;text-align: right"><strong><?php echo number_format($TotalBill, $d); ?></strong></td>

            <td colspan="" style="padding-right:2px;font-weight: bold;text-align: right"><strong>  <?php echo $this->lang->line('posr_total_commission_amount');?><!--Total Commission Amount--></strong></td>
            <td style="font-weight: bold;text-align: right"><strong><?php echo number_format($TotalCommission, $d); ?></strong></td>

        </tr>
        </tfoot>

    </table>
    <hr>
    <div style="margin:4px 0px">
        <?php echo $this->lang->line('posr_report_print');?><!-- Report print by--> : <?php echo current_user() ?>
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

    function generateDeliveryPersonPdf() {
        var form = document.getElementById('frm_deliverypersonReport');
        form.target = '_blank';
        form.action = '<?php echo site_url('Pos_restaurant/loadDeliveryPersonReportPdf'); ?>';
        form.submit();
    }

</script>