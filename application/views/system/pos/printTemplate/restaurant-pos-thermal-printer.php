<div id="wrapper">

    <?php

    $companyInfo = get_companyInfo();
    $outletInfo = get_outletInfo();
    echo '<pre>';
    print_r($masters);
    echo '</pre>';

    ?>
    <div id="print_content">
        <table border="0" style="border-collapse: collapse; width: 100%; height: auto;">
            <tbody>

            <tr>
                <td width="100%" align="center">
                    <div style="padding-top: 0px; font-size: 20px;">
                        <strong>
                            <?php echo $companyInfo['company_name'] ?> </strong>
                    </div>
                    <div style="padding: 3px; font-size: 16px;">
                        <?php echo $outletInfo['wareHouseDescription'] ?> Outlet

                    </div>
                </td>
            </tr>
            <tr>
                <td width="100%">
                    <div style="margin-bottom: 3px">
                    <span class="left"
                          style="text-align: left;">Address : <?php echo $outletInfo['warehouseAddress'] ?></span>
                        <span class="left"
                              style="text-align: left;">Tel : <?php echo $outletInfo['warehouseTel'] ?></span>
                        <span class="left"
                              style="text-align: left;">Sale Id : <?php echo isPos_invoiceSessionExist() ?></span>
                        <span class="left"
                              style="text-align: left;">Date : <?php echo format_dateTime_pos_printFormat_date() ?></span>
                        <!--<span class="left" style="text-align: left;">Customer Name&nbsp; : Walk In Customer</span>
                        <span class="left" style="text-align: left;">Customer Phone : -</span>-->
                    </div>
                </td>
            </tr>
            </tbody>
        </table>


        <div style="clear:both;"></div>

        <table class="table" cellspacing="0" border="0">
            <thead>
            <tr>
                <th width="5%"><em>#</em></th>
                <th width="20%" align="left">Product</th>
                <th width="5%">Qty</th>
                <th width="15%">Per Item</th>
                <th width="15%" align="right">Total</th>
                <th width="10%" align="right">Dist.%</th>
                <th width="15%" align="right">Net Total</th>
            </tr>
            </thead>
            <tbody>

            <?php
            $qty = 0;
            $total = 0;
            if (!empty($invoiceList)) {
                $i = 1;
                foreach ($invoiceList as $item) {
                    ?>
                    <tr>
                        <td width="5%">
                            <?php echo $i;
                            $i++ ?>
                        </td>
                        <td width="20%" align="left">
                            <?php echo $item['menuMasterDescription'] ?><br/>
                            [<?php echo padZeros_saleInvoiceID($item['warehouseMenuID']) ?>]

                        </td>
                        <td width="5%"><?php echo $item['qty'];
                            $qty = $qty + $item['qty']; ?></td>
                        <td width="15%"><?php echo number_format($item['sellingPrice'], 2) ?></td>
                        <td width="15%"
                            align="right">
                            <?php
                            $total = $total + ($item['sellingPrice'] * $item['qty']);
                            echo number_format(($item['sellingPrice'] * $item['qty']), 2)
                            ?>
                        </td>
                        <td width="10%" align="right"><?php echo number_format($item['discountPer'], 1) ?>%</td>
                        <td width="15%"
                            align="right"><?php echo number_format((($item['sellingPrice'] * $item['qty']) - $item['discountAmount']), 2) ?></td>
                    </tr>
                    <?php
                }
            }
            ?>


            </tbody>
        </table>


        <table class="totals" cellspacing="0" border="0"
               style="margin-bottom:5px; border-top: 1px solid #000; border-collapse: collapse;">
            <tbody>
            <tr>
                <td style="text-align:left; padding-top: 5px;">Total Items</td>
                <td style="text-align:right; padding-right:1.5%; border-right: 1px solid #000;font-weight:bold;"><?php echo $qty ?></td>
                <td style="text-align:left; padding-left:1.5%;">Total</td>
                <td style="text-align:right;font-weight:bold;"><?php echo number_format($masters['grossTotal'], 2) ?></td>
            </tr>

            <tr>
                <td style="text-align:left;"></td>
                <td style="text-align:right; padding-right:1.5%; border-right: 1px solid #000;font-weight:bold;"></td>
                <td style="text-align:left; padding-left:1.5%; padding-bottom: 5px;">
                    Discount&nbsp;(<?php echo number_format($masters['discountPer'], 1) ?>%)
                </td>
                <td style="text-align:right;font-weight:bold;"><?php echo number_format($masters['discountAmount'], 2) ?></td>
            </tr>
            <tr>
                <td style="text-align:left; padding-top: 5px;">&nbsp;</td>
                <td style="text-align:right; padding-right:1.5%; border-right: 1px solid #000;font-weight:bold;">
                    &nbsp;</td>
                <td style="text-align:left; padding-left:1.5%;">Sub Total</td>
                <td style="text-align:right;font-weight:bold;"><?php echo number_format($masters['subTotal'], 2) ?></td>
            </tr>
            <tr>
                <td style="text-align:left; padding-top: 5px;">&nbsp;</td>
                <td style="text-align:right; padding-right:1.5%; border-right: 1px solid #000;font-weight:bold;">
                    &nbsp;</td>
                <td style="text-align:left; padding-left:1.5%;">Service Charge </td>
                <td style="text-align:right;font-weight:bold;"><?php echo number_format($masters['serviceCharge'], 2) ?></td>
            </tr>
            <tr>
                <td style="text-align:left; padding-top: 5px;">&nbsp;</td>
                <td style="text-align:right; padding-right:1.5%; border-right: 1px solid #000;font-weight:bold;">
                    &nbsp;</td>
                <td style="text-align:left; padding-left:1.5%;">Tax <?php $tax = get_totalTax();
                    echo $tax; ?>%
                </td>
                <td style="text-align:right;font-weight:bold;"><?php echo number_format($masters['totalTaxAmount'], 2) ?></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:left; font-weight:bold; border-top:1px solid #000; padding-top:5px;">
                    Grand
                    Total
                </td>
                <td colspan="2" style="border-top:1px solid #000; padding-top:5px; text-align:right; font-weight:bold;">
                    <?php echo number_format($masters['netTotal'], 2) ?>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">Paid</td>
                <td colspan="2"
                    style="padding-top:5px; text-align:right; font-weight:bold;"><?php echo number_format($masters['cashReceivedAmount'], 2) ?></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">Change</td>
                <td colspan="2"
                    style="padding-top:5px; text-align:right; font-weight:bold;"><?php echo number_format($masters['balanceAmount'], 2) ?></td>
            </tr>
            <tr>
                <td style="text-align:left; padding-top: 5px; font-weight: bold; border-top: 1px solid #000;">Paid By
                    :
                </td>
                <td style="text-align:right; padding-top: 5px; padding-right:1.5%; border-top: 1px solid #000;font-weight:bold;"
                    colspan="3">
                    <?php
                    $payment = $masters['paymentMethod'];

                    switch ($payment) {
                        case 1:
                            echo '<img src="'.base_url().'images/payment_type/'.$payment. '.png"> Cash';
                            break;
                        case 2:
                            echo '<img src="'.base_url().'images/payment_type/'.$payment. '.png"> VISA';
                            break;
                        case 3:
                            echo '<img src="'.base_url().'images/payment_type/'.$payment. '.png"> Master Card';
                            break;
                        case 4:
                            echo '<img src="'.base_url().'images/payment_type/'.$payment. '.png"> Cheque';
                            break;
                        default:
                            echo '<img src="'.base_url().'images/payment_type/'.$payment. '.png"> Cash';
                    }
                    ?>
                </td>
            </tr>
            </tbody>
        </table>

        <div style="border-top:1px solid #000; padding-top:10px;">
            <p>Thank you for Shopping with Us!</p>
        </div>

    </div>


    <div id="bkpos_wrp">
        <button type="button" onclick="print_pos_report()"
                style="width:101%; cursor:pointer; font-size:12px; background-color:#FFA93C; color:#000; text-align: center; border:1px solid #FFA93C; padding: 10px 0px; font-weight:bold;">
            <i class="fa fa-print"></i> Print
        </button>
    </div>

    <div id="bkpos_wrp" style="margin-top: 8px;">
        <span class="left"><button type="button" onclick="openemailPrintmodule()"
                              style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center; color:#000; background-color:#4FA950; border:2px solid #4FA950; padding: 10px 0px; font-weight:bold;"
                              id="email"><i class="fa fa-envelope-o" aria-hidden="true"></i> Email</button></span>
    </div>

    <!--<div id="bkpos_wrp">
    	<span class="left">
    		<a href="http://pos.prosoft-apps.com/pos/view_invoice_a4?id=216"
               style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center; color:#000; background-color:#4FA950; border:2px solid #4FA950; padding: 10px 0px; font-weight:bold; margin-top: 6px;">
	    		Print A4
	    	</a>
	    </span>
    </div>-->

    <input type="hidden" id="id" value="216">

</div>