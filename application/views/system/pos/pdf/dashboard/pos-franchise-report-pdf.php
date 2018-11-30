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
    <table style="width: 100%">
        <tr>
            <td style="width:10%;"><strong> Date :</strong></td>
            <td style=""><?php
                $filterFrom = $this->input->post('filterFrom');
                $filterTo = $this->input->post('filterTo');
                if (!empty($filterFrom) && !empty($filterTo)) {
                    echo '  From : ' . $filterFrom . ' -  To: ' . $filterTo.'';
                } else {
                    $curDate = date('d-m-Y');
                    echo $curDate . ' (Today)';
                }
                ?></td>
        </tr>
    </table>



    <br>
    <table class="theadtr" style="width: 100%; " border="1">
        <thead>
        <tr class="theadtr">
            <th colspan="3"> &nbsp;</th>
            <th colspan="2"> Eat In</th>
            <th colspan="2"> Take Away</th>
            <th colspan="2"> Delivery</th>
            <th colspan="2"> Total</th>
        </tr>
        <tr class="theadtr">
            <th class=""> #</th>
            <th class=""> Day</th>
            <th class=""> Order Date</th>
            <th class=""> QTY</th>
            <th class=""> Net Sales</th>
            <th class=""> QTY</th>
            <th class=""> Net Sales</th>
            <th class=""> QTY</th>
            <th class=""> Net Sales</th>
            <th class=""> QTY</th>
            <th class=""> Net Sales</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        $EatInTotal = 0;
        $EatInQty = 0;
        $TakeAwayTotal = 0;
        $TakeAwayQty = 0;
        $DeliveryOrdersTotal = 0;
        $DeliveryOrdersQty = 0;
        $NetTotal = 0;
        $netQty = 0;
        $tax = 0;
        if (!empty($franchiseReport)) {
            foreach ($franchiseReport as $val) {
                $EatInTotal += $val['EatInTotal'];
                $EatInQty += $val['EatInQty'];
                $TakeAwayTotal += $val['TakeAwayTotal'];
                $TakeAwayQty+= $val['TakeAwayQty'];
                $DeliveryOrdersTotal += $val['DeliveryOrdersTotal'];
                $DeliveryOrdersQty+= $val['DeliveryOrdersQty'];
                $NetTotal += $val['NetTotal'];
                $netQty+= $val['netQty'];
                ?>
                <tr>
                    <td class="" style="text-align: center;"><?php echo $i ?></td>
                    <td class="" style="text-align: center;"><?php echo $val['salesDay'] ?></td>
                    <td class="" style="text-align: right;padding-right: 3px;"><?php echo $val['menuSalesDate'] ?></td>
                    <td class="" style="text-align: right;padding-right: 3px;"><?php echo $val['EatInQty'] ?></td>
                    <td class="" style="text-align: right;padding-right: 3px;"><?php echo number_format($val['EatInTotal'], $d); ?></td>
                    <td class="" style="text-align: right;padding-right: 3px;"><?php echo $val['TakeAwayQty'] ?></td>
                    <td class="" style="text-align: right;padding-right: 3px;"><?php echo number_format($val['TakeAwayTotal'], $d); ?></td>
                    <td class="" style="text-align: right;padding-right: 3px;"><?php echo $val['DeliveryOrdersQty'] ?></td>
                    <td class="" style="text-align: right;padding-right: 3px;"><?php echo number_format($val['DeliveryOrdersTotal'], $d); ?></td>
                    <td class="" style="text-align: right;padding-right: 3px;"><?php echo $val['netQty'] ?></td>
                    <td class="" style="text-align: right;padding-right: 3px;"><?php echo number_format($val['NetTotal'], $d); ?></td>
                </tr>
                <?php

                $i=$i+1;
            }
        }else{
            ?>
            <tr>
                <td class="" style="text-align: center;" colspan="11">Records not Found</td>
            </tr>
            <?php
        }
        ?>
        </tbody>
        <tfoot>
        <tr style="font-size:12px !important;" class="t-foot">
            <td colspan="3" style="padding-right:2px;font-weight: bold; text-align: right"><strong>  Total</strong></td>
            <td class="" style="text-align: right;padding-right: 3px;font-weight: bold;"><strong><?php echo $EatInQty ?></strong></td>
            <td class="" style="text-align: right;padding-right: 3px;font-weight: bold;"><strong><?php echo number_format($EatInTotal, $d); ?></strong></td>
            <td class="" style="text-align: right;padding-right: 3px;font-weight: bold;"><strong><?php echo $TakeAwayQty ?></strong></td>
            <td class="" style="text-align: right;padding-right: 3px;font-weight: bold;"><strong><?php echo number_format($TakeAwayTotal, $d); ?></strong></td>
            <td class="" style="text-align: right;padding-right: 3px;font-weight: bold;"><strong><?php echo $DeliveryOrdersQty ?></strong></td>
            <td class="" style="text-align: right;padding-right: 3px;font-weight: bold;"><strong><?php echo number_format($DeliveryOrdersTotal, $d); ?></strong></td>
            <td class="" style="text-align: right;padding-right: 3px;font-weight: bold;"><strong><?php echo $netQty ?></strong></td>
            <td class="" style="text-align: right;padding-right: 3px;font-weight: bold;"><strong><?php echo number_format($NetTotal, $d); ?></strong></td>
        </tr>
        <tr style="font-size:12px !important;" class="t-foot">
            <?php
            $EatInQtyd=$EatInQty/$i;
            $EatInTotald=$EatInTotal/$i;
            $TakeAwayQtyd=$TakeAwayQty/$i;
            $TakeAwayTotald=$TakeAwayTotal/$i;
            $DeliveryOrdersQtyd=$DeliveryOrdersQty/$i;
            $DeliveryOrdersTotald=$DeliveryOrdersTotal/$i;
            $netQtyd=$netQty/$i;
            $NetTotald=$NetTotal/$i;
            ?>
            <td colspan="3" style="padding-right:2px;font-weight: bold; text-align: right"><strong>  Daily Avg</strong></td>
            <td class="alin" style="text-align: right;padding-right: 3px;font-weight: bold;"><strong><?php echo number_format($EatInQtyd, 2); ?></strong></td>
            <td class="alin" style="text-align: right;padding-right: 3px;font-weight: bold;"><strong><?php echo number_format($EatInTotald, $d); ?></strong></td>
            <td class="alin" style="text-align: right;padding-right: 3px;font-weight: bold;"><strong><?php echo number_format($TakeAwayQtyd, 2); ?></strong></td>
            <td class="alin" style="text-align: right;padding-right: 3px;font-weight: bold;"><strong><?php echo number_format($TakeAwayTotald, $d); ?></strong></td>
            <td class="alin" style="text-align: right;padding-right: 3px;font-weight: bold;"><strong><?php echo number_format($DeliveryOrdersQtyd, 2); ?></strong></td>
            <td class="alin" style="text-align: right;padding-right: 3px;font-weight: bold;"><strong><?php echo number_format($DeliveryOrdersTotald, $d); ?></strong></td>
            <td class="alin" style="text-align: right;padding-right: 3px;font-weight: bold;"><strong><?php echo number_format($netQtyd, 2); ?></strong></td>
            <td class="alin" style="text-align: right;padding-right: 3px;font-weight: bold;"><strong><?php echo number_format($NetTotald, $d); ?></strong></td>
        </tr>
        </tfoot>
    </table>
    <br>

    <?php
    $tax=($NetTotal*16)/100;
    $salesWouttax=$NetTotal-$tax;
    $royalty=($salesWouttax*5)/100
    ?>
    <div style="margin:4px 0px">
        <b> Sales :</b> <?php echo number_format($NetTotal, $d); ?>
    </div>
    <div style="margin:4px 0px">
        <b> TAX(16%) :</b> <?php echo number_format($tax, $d); ?>
    </div>
    <div style="margin:4px 0px">
        <b> Sales w/out TAX :</b> <?php echo number_format($salesWouttax, $d); ?>
    </div>
    <div style="margin:4px 0px">
        <b> ROYALTY :</b> <?php echo number_format($royalty, $d); ?>
    </div>
    <hr>
    <div style="margin:4px 0px">
        <h6><strong>Printed by :</strong> <?php echo current_user() ?></h6>
    </div>
    <div style="margin:4px 0px">
        <h6><strong>Printed Date :</strong> <?php echo date('d/m/Y'); ?> <?php echo date("h:i A"); ?></h6>
    </div>
</div>
