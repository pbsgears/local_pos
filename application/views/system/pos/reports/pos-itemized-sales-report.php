<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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

</style>
<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
/*
echo '<pre>';
print_r($itemizedSalesReport);
echo '</pre>';*/
?>
<span class="pull-right">
    <button type="button" id="btn_print_itemizedSales" class="btn btn-default btn-xs">
        <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
    </button>
    <!--<button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generateItemSalesReportPdf()"> <i
            class="fa fa-file-pdf-o"
            aria-hidden="true"></i> PDF </button>-->
</span>

<div id="printContainer_itemizedSalesReport">
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

    <!--<div class="text-center">
        <h3 style="margin-top:2px;"><?php /*echo $companyInfo['company_name'] */ ?></h3>
        <h4 style="margin:0px;"><?php /*echo $companyInfo['company_address1'] . ', ' . $companyInfo['company_city'] */ ?></h4>
    </div>
    <div style="margin:4px 0px; text-align: center;">
        <?php
    /*        $cash = $this->lang->line('posr_cashier');
            if (isset($cashier) && !empty($cashier)) {
                echo '' . $cash . ': ';

                $tmpArray = array();
                foreach ($cashier as $c) {
                    $tmpArray[] = $cashierTmp[$c];
                }
                echo join(', ', $tmpArray);
            }
            */ ?>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        	<span>
        <?php /*echo $this->lang->line('posr_filtered_date'); */ ?> : <strong>
            <?php
    /*            $filterFrom = $this->input->post('filterFrom');
                $filterTo = $this->input->post('filterTo');
                $from = $this->lang->line('common_from');
                $to = $this->lang->line('common_to');
                $today = $this->lang->line('posr_today');
                if (!empty($filterFrom) && !empty($filterTo)) {
                    echo '  <i>' . $from . ' : </i>' . $filterFrom . ' - <i> ' . $to . ': </i>' . $filterTo;
                } else {
                    $curDate = date('d-m-Y');
                    echo $curDate . ' (' . $today . ')';
                }
                */ ?>
        </strong>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <?php /*echo $this->lang->line('common_date'); */ ?> : <strong><?php /*echo date('d/m/Y'); */ ?></strong>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
            <?php /*echo $this->lang->line('posr_time'); */ ?> : <strong><span id="pcCurrentTime"></span></strong>
        </div>
    </div>


    <br>-->
    <h3 class="text-center">Item wise Sales Report</h3>
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
                $tmpArray[] = isset($cashierTmp[$c]) ? $cashierTmp[$c] : '';
            }
            echo join(', ', $tmpArray);
        }
        ?>
    </div>

    <table class="<?php echo table_class_pos(5) ?>">
        <thead>
        <tr>
            <th class="al"> <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
            <th class="al"> <?php echo $this->lang->line('posr_qyt'); ?><!--Qty--></th>
            <th class="ar"> <?php echo $this->lang->line('posr_amount'); ?><!--Amount--></th>
        </tr>
        </thead>

        <?php
        $grandTotal = 0;

        if (!empty($itemizedSalesReport)) {
            $catID = 0;
            $mArray = array();
            foreach ($itemizedSalesReport as $val) {
                $mArray[$val['menuCategoryDescription']][] = $val;
            }

            //var_dump($mArray);

            foreach ($mArray as $key => $menus) {
                ?>
                <tr>
                    <td colspan="3"><strong><?php echo $key ?></strong></td>
                </tr>
                <tboad>
                    <?php
                    $total = 0;
                    foreach ($menus as $item) {
                        ?>
                        <tr>
                            <td>
                                <?php
                                echo $item['menuMasterDescription'];
                                if (isset($item['menuSize']) && !empty($item['menuSize'])) {
                                    echo ' - ' . strtolower($item['menuSize']);
                                }
                                ?>
                            </td>
                            <td> <?php echo $item['qty'] ?></td>
                            <td class="text-right">

                                <?php
                                echo number_format($item['itemPriceTotal'], $d);
                                $grandTotal += $item['itemPriceTotal'];
                                $total += $item['itemPriceTotal'];
                                ?>

                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tboad>
                <tr>
                    <td colspan="2">
                        <strong><?php echo $this->lang->line('common_total'); ?><!--Total--></strong></td>
                    <th class="text-right">  <?php echo number_format($total, $d) ?></th>
                </tr>
                <?php
            }


        }
        ?>

        <tfoot>
        <tr style="font-size:15px !important;">
            <td colspan="2"><strong> <?php echo $this->lang->line('posr_grand_total'); ?><!--Grand Total--> </strong>
            </td>
            <td class="text-right"><strong><?php echo number_format($grandTotal, $d); ?></strong></td>
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
