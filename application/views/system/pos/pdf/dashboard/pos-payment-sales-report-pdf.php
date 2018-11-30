<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;


?>
<!--<link rel="stylesheet" href="--><?php //echo base_url('/plugins/bootstrap/css/bootstrap.css');  ?><!--">-->
<!--<link rel="stylesheet" href="--><?php //echo base_url('/plugins/bootstrap/css/style.css');  ?><!--">-->
<div id="container_sales_report">
    <table class="text-center" style="width: 100%">
        <tr>
            <td style="text-align: center">
                <h3 style="margin-top:2px;"><?php echo $companyInfo['company_name'] ?></h3>
                <?php if (!empty($companyInfo['company_address1'])) {
                    ?>
                    <h4 style="margin:0;">
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
            </td>
        </tr>
    </table>

    <div style="margin:4px 0; text-align: center;">
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

    <hr style="margin:2px 0;">
    <h4>Sales Report </h4>
    <?php
    if (!empty($paymentMethod)) {

        ?>

        <div class="row customPad" style="padding: 3px 0">
            <table style="width: 100%">
                <tbody>
                <?php foreach ($paymentMethod as $report2) { ?>
                    <tr>
                        <td style="width: 18%"><?php echo $report2['paymentDescription'] ?></td>
                        <td style="text-align: center">:</td>
                        <td style="width: 48%;text-align: right"><?php
                            echo number_format($report2['NetTotal'], $d);
                            $netTotal += $report2['NetTotal'];
                            ?></td>
                    </tr>
                <?php } ?>
                </tbody>
                <tfoot>
                <tr>
                    <th style="width: 48%;text-align: right"><strong> Grand Total </strong></th>
                    <th style="text-align: center">:</th>
                    <th style="width: 48%;text-align: right"><strong><?php echo number_format($netTotal, $d) ?></strong>
                    </th>
                </tr>
                </tfoot>
            </table>
        </div>
        <?php
    }
    ?>
    <hr style="margin:2px 0">
    <h4>Bill Count</h4>
    <table style="width: 100%" class="table-striped">
        <thead>
        <tr class="theadtr">
            <th> Order Type</th>
            <th> No of Bills <?php //echo $this->common_data['company_data']['company_default_currency']; ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $grandTotalCount = 0;
        if (!empty($customerTypeCount)) {
            foreach ($customerTypeCount as $report1) {
                ?>
                <tr style=" background-color: #f9f9f9">
                    <td><strong> <?php echo $report1['customerDescription'] ?> Orders</strong></td>
                    <td style="text-align: right">
                        <strong>
                            <?php
                            echo $report1['countTotal'];
                            $grandTotalCount += $report1['countTotal'];
                            ?>
                        </strong>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
        <tfoot>
        <tr class="t-foot">
            <td><strong> Total Number of Bills</strong></td>
            <td style="text-align: right"><strong><?php echo $grandTotalCount; ?></strong></td>
        </tr>
        </tfoot>
    </table>
</div>

<hr>
<div style="margin:4px 0px">
    <h6><strong>Printed by :</strong> <?php echo current_user() ?></h6>
</div>
<div style="margin:4px 0px">
    <h6><strong>Printed Date :</strong> <?php echo date('d/m/Y'); ?> <?php echo date("h:i A"); ?></h6>
</div>