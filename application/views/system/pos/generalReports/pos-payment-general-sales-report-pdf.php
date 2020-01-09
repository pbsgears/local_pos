<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
?>
<!--<link rel="stylesheet" href="--><?php //echo base_url('/plugins/bootstrap/css/bootstrap.css');  ?><!--">-->
<!--<link rel="stylesheet" href="--><?php //echo base_url('/plugins/bootstrap/css/style.css');  ?><!--">-->
<div id="container_sales_report">

    </span>
    <div class="text-center">
        <h3 style="margin-top:2px;"><?php echo $companyInfo['company_name'] ?></h3>
        <?php if (!empty($companyInfo['company_address1'])) {
            ?>
            <h4 style="margin:0px;">
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
    </div>

    <div style="margin:4px 0px; text-align: center;">
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

    <hr style="margin:2px 0px;">


    <h4>Sales Report </h4>

    <div class="row customPad" style="padding: 3px 0">
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td style="width: 18%">Cash</td>
                    <td style="text-align: center">:</td>
                    <td style="width: 48%;text-align: right"><?php echo number_format($paymentMethod['cashAmount'], $d); ?></td>
                </tr>
                <tr>
                    <td style="width: 18%">Card</td>
                    <td style="text-align: center">:</td>
                    <td style="width: 48%;text-align: right"><?php echo number_format($paymentMethod['cardAmount'], $d); ?></td>
                </tr>
                <tr>
                    <td style="width: 18%">Cheque</td>
                    <td style="text-align: center">:</td>
                    <td style="width: 48%;text-align: right"><?php echo number_format($paymentMethod['chequeAmount'], $d); ?></td>
                </tr>
                <tr>
                    <td style="width: 18%">Credit Note</td>
                    <td style="text-align: center">:</td>
                    <td style="width: 48%;text-align: right"><?php echo number_format($paymentMethod['creditNoteAmount'], $d); ?></td>
                </tr>
            </tbody>
            <tfoot>
            <tr>
                <th style="width: 48%;text-align: right"><strong> Grand Total </strong></th>
                <th style="text-align: center">:</th>
                <th style="width: 48%;text-align: right"><strong><?php echo number_format($paymentMethod['netTotal'], $d); ?></strong>
                </th>
            </tr>
            <tr>
                <th style="width: 48%;text-align: right"><strong>  Bill Count </strong></th>
                <th style="text-align: center">:</th>
                <th style="width: 48%;text-align: right"><strong><?php echo $customerTypeCount['billCount']; ?></strong>
                </th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<hr>
<div style="margin:4px 0px">
    <h6><strong>Printed by :</strong> <?php echo current_user() ?></h6>
</div>
<div style="margin:4px 0px">
    <h6><strong>Printed Date :</strong> <?php echo date('d/m/Y'); ?> <?php echo date("h:i A"); ?></h6>
</div>