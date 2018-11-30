<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
?>

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
         Date : <strong>
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
    <br>
    <table class="<?php //echo table_class_pos() ?>" style="width: 100%; font-size: 12px !important; " border="0">
        <thead>
        <tr >
            <th class="al" style="font-size:13px !important;"> Description</th>
            <th style="text-align: right; font-size:13px !important;"> Qty</th>

        </tr>
        </thead>
        <tbody>

        <?php
        $grandTotal = 0;

        if (!empty($productMix)) {


            foreach ($productMix as $key => $val) {
                $qty = 0;

                foreach ($val as $item) {

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
            <th ><strong>  Total </strong></th>
            <th style="text-align: right"><strong><?php echo number_format($grandTotal, 0); ?></strong></th>
        </tr>
        </tfoot>
    </table>
    <hr>
    <div style="margin:4px 0px">
        <h6><strong>Printed by :</strong> <?php echo current_user() ?></h6>
    </div>
    <div style="margin:4px 0px">
        <h6><strong>Printed Date :</strong> <?php echo date('d/m/Y'); ?> <?php echo date("h:i A"); ?></h6>
    </div>
</div>
