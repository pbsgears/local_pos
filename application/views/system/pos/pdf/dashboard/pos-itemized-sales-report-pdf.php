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




    <br>
    <table class="theadtr" style="width: 100%; " border="0">
        <thead>
        <tr class="theadtr">
            <th style="text-align: left;"> Description</th>
            <th style="text-align: center;"> Qty</th>
            <th style="text-align: right;"> Price</th>
        </tr>
        </thead>
        <tbody>
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
                    <th colspan="3" style="text-align: left"><?php echo $key ?></th>
                </tr>
                <?php
                $total = 0;
                foreach ($menus as $item) {
                    ?>
                    <tr>
                        <td><?php echo $item['menuMasterDescription'] ?></td>
                        <td style="text-align: center"> <?php echo $item['qty'] ?></td>
                        <td style="text-align: right">

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
                <tr class="t-foot">
                    <th colspan="2" style="padding:10px 0;text-align: left">Total</th>
                    <th style="text-align: right">  <?php echo number_format($total, $d) ?></th>
                </tr>
                <?php
            }


        }
        ?>
        </tbody>
        <tfoot>
        <tr style="font-size:15px !important;" class="t-foot">
            <th colspan="2" style="text-align: left"><strong> Grand Total </strong></th>
            <th style="text-align: right"><strong><?php echo number_format($grandTotal, $d); ?></strong></th>
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
