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
            <td style="width:60%;">Filtered Date :</td>
            <td style=""><?php
                $filterFrom = $this->input->post('filterFrom');
                $filterTo = $this->input->post('filterTo');
                if (!empty($filterFrom) && !empty($filterTo)) {
                    echo '<strong>  From : ' . $filterFrom . ' -  To: ' . $filterTo.'</strong>';
                } else {
                    $curDate = date('d-m-Y');
                    echo $curDate . ' (Today)';
                }
                ?></td>
        </tr>

       <!-- <tr>
            <?php
/*            if($this->input->post('customerID')>0){
            */?>
            <td style="width:23%;">Promotions Or Order:</td>
            <td style=""><strong><?php /*echo $person['customerName']; */?></strong></td>
                <?php
/*            }
            */?>
        </tr>-->
    </table>



    <br>
    <table class="theadtr" style="width: 100%; " border="1">
        <thead>
        <tr class="theadtr">
            <th class=""> #</th>
            <th class=""> Invoice ID</th>
            <th class=""> Description</th>
            <th class=""> Bill Date</th>
            <th class=""> Bill Time</th>
            <th class=""> Bill Amount</th>
            <th class=""> Commission</th>
            <th class=""> Commission Amount</th>
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
                    <td style="text-align: right;" class="ar"><?php echo number_format($val['subTotal'], $d); ?></td>
                    <td style="text-align: right;" class="ar"><?php echo $val['deliveryCommission']  ?> %</td>
                    <td style="text-align: right;" class="ar"><?php echo number_format($val['deliveryCommissionAmount'], $d);  ?></td>
                </tr>



                <?php

                $i=$i+1;
            }
        }else{
            ?>
            <tr>
                <td class="alin" colspan="8">Records not Found</td>
            </tr>
            <?php
        }
        ?>
        </tbody>
        <tfoot>
        <tr style="font-size:11px !important;" class="t-foot">
            <td colspan="5" style="font-size:11px;padding-right:2px;font-weight: bold; text-align: right"><strong>  Total Bill Amount</strong></td>
            <td class="" style="font-size:11px;font-weight: bold;text-align: right"><strong><?php echo number_format($TotalBill, $d); ?></strong></td>

            <td colspan="" style="font-size:11px;padding-right:2px;font-weight: bold;text-align: right"><strong>  Total Commission Amount</strong></td>
            <td style="font-size:11px;font-weight: bold;text-align: right"><strong><?php echo number_format($TotalCommission, $d); ?></strong></td>

        </tr>
        </tfoot>
    </table>
    <br>
    <hr>
    <div style="margin:4px 0px">
        <h6><strong>Printed by :</strong> <?php echo current_user() ?></h6>
    </div>
    <div style="margin:4px 0px">
        <h6><strong>Printed Date:</strong> <?php echo date('d/m/Y'); ?> <?php echo date("h:i A"); ?></h6>
    </div>
</div>
