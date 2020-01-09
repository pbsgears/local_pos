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

</style>
<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);


/*echo '<pre>';
print_r($productMix);
echo '</pre>';*/
?>
<span class="pull-right">
    <button type="button" id="btn_print_itemizedSales" class="btn btn-default btn-xs">
        <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
    </button>
    <button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onchange="loadCashier()"><i
            class="fa fa-file-pdf-o"
            aria-hidden="true"></i> PDF
    </button>
</span>
<div id="printContainer_itemizedSalesReport">

    <div class="text-center">
        <h4 style="margin-top:2px;"><strong><?php echo $companyInfo['company_name'] ?></strong></h4>
    </div>
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="outletInfo">
                <?php
                //$outlets = get_active_outletInfo();
                $outletInput = $this->input->post('outlet');

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

    <h3 class="text-center" style="margin:3px 0px;">Product Mix Report </h3>
    <!--<div class="text-center"><?php /*echo $companyInfo['company_address1'] . ', ' . $companyInfo['company_city'] */ ?></div>-->


    <div style="margin:4px 0px; text-align: center;">
        <?php
        $cash = $this->lang->line('posr_cashier');
        if (isset($cashier) && !empty($cashier)) {
            echo '' . $cash . ': ';

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
            <strong>Filters <i class="fa fa-filter"></i></strong><br/>
            <span>
         <i>Date From:</i>
      <strong>
            <?php
            $filterFrom = $this->input->post('filterFrom');
            $filterTo = $this->input->post('filterTo');
            $from = $this->lang->line('common_from');
            $to = $this->lang->line('common_to');
            $today = $this->lang->line('posr_today');
            if (!empty($filterFrom) && !empty($filterTo)) {
                echo $filterFrom . '</strong><i> -  ' . $to . ': </i> <strong>' . $filterTo;
            } else {
                $curDate = date('d-m-Y');
                //echo $curDate . ' (' . $today . ')';
            }
            ?>
        </strong>
        </div>
    </div>

    <!--<div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <?php /*echo $this->lang->line('common_date'); */ ?> : <strong><?php /*echo date('d/m/Y'); */ ?></strong>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
            <?php /*echo $this->lang->line('posr_time'); */ ?> : <strong><span
                    id="pcCurrentTime_productMix"></span></strong>
        </div>
    </div>-->

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
    <br>
    <table class="<?php echo table_class_pos(5) ?>">
        <thead>
        <tr>
            <th class="al" style="font-size:13px !important;">
                <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
            <th style="text-align: right; font-size:13px !important;">
                <?php echo $this->lang->line('posr_qyt'); ?><!--Qty--></th>

        </tr>
        </thead>
        <tbody>

        <?php
        $grandTotal = 0;

        if (!empty($productMix)) {


            foreach ($productMix as $key => $val) {
                $qty = 0;
                /*echo '<hr>';
                print_r($val);*/
                foreach ($val as $item) {
                    /*echo '<pre>';
                    echo $key;
                    print_r($item);
                    echo '</pre>';*/
                    $qty += $item['qty'];
                    $grandTotal += $item['qty'];
                }

                ?>
                <?php //echo $val[0]['menuMasterDescription'] . $qty; ?>
                <tr>
                    <td><?php echo $val[0]['menuMasterDescription']; ?></td>
                    <td style="text-align: right"><?php echo $qty; ?></td>

                </tr>
                <?php


            }


        }
        ?>
        </tbody>
        <tfoot>
        <tr style="font-size:13px !important;">
            <th><strong>  <?php echo $this->lang->line('common_total'); ?><!--Total--> </strong></th>
            <th style="text-align: right"><strong><?php echo number_format($grandTotal, 0); ?></strong></th>
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

    function generateProductMixPdf() {
        var form = document.getElementById('frm_ProductMix');
        form.target = '_blank';
        form.action = '<?php echo site_url('Pos_restaurant/loadProductMixReportPdf'); ?>';
        form.submit();
    }

</script>