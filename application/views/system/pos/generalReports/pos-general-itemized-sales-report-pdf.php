<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
?>

<div id="printContainer_itemizedSalesReport">
    <table style="width: 100%">
        <tr>
            <td style="text-align: center"><h3 style="margin-top:2px;"><?php echo $companyInfo['company_name'] ?></h3></td>
        </tr>
        <tr>
            <td style="text-align: center">
                <h4 style="margin:0;"><?php echo $companyInfo['company_address1'] . ', ' . $companyInfo['company_city'] ?></h4>
            </td>
        </tr>
    </table>
    <div style="margin:4px 0; text-align: center;">
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
    <table class="<?php //echo table_class_pos() ?>" style="width: 100%; " border="1">
        <thead>
        <tr>
            <th class="al"> Description</th>
            <th style="text-align: center;"> Qty</th>
            <th > Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $grandTotal = 0;

        if (!empty($itemizedSalesReport)) {

            foreach ($itemizedSalesReport as $val) {
                ?>
                <tr>
                    <td class="al"><?php echo $val['itemDescription'] ?></td>
                    <td style="text-align: center;"><?php echo $val['qty'] ?></td>
                    <td style="text-align: right"><?php echo number_format($val['price'], $d); ?></td>
                </tr>
                <?php
                $grandTotal += $val['price'];
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
        <tr>
            <th  style="font-size:12px !important;" colspan="2"> Grand Total </th>
            <th style="font-size:12px !important;text-align: right"><?php echo number_format($grandTotal, $d); ?></th>
        </tr>
        </tfoot>
    </table>
    <br>
    <hr>
    <div style="margin:4px 0px">
        <h6><strong>Printed by :</strong> <?php echo current_user() ?></h6>
    </div>
    <div style="margin:4px 0px">
        <h6><strong>Printed Date :</strong> <?php echo date('d/m/Y'); ?> <?php echo date("h:i A"); ?></h6>
    </div>
</div>
