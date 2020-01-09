<style type="text/css">
    .headerTxt {
        font-size: 12px !important;
        margin: 0px;
        text-align: center;
    }

    .fWidth {
        width: 100% !important;;
    }

    .fSize {
        font-size: 12px !important;
    }

    .f {
        /*font-family: monospace, sans-serif, Verdana, Geneva !important;*/
        font-family: 'Raleway', Arial, sans-serif;
    }

    .pad-top {
        padding-top: 3px;
    }

    .ac {
        text-align: center !important;
    }

    .ar {
        text-align: right !important;
    }

    .al {
        text-align: left !important;
    }

    #tblListItems tr td {
        padding: 2px 1px !important;

    }

    .vLine {
        border-top: 1px dashed #000;
        margin: 10px 0px;
        height: 2px;
    }

    .orderInformation {
        border: 0px !important;
    }

    .orderInformation tbody > tr > td {
        padding: 1px 2px 1px 0px !important;
        border: 0px !important;
    }

    .contentOfListItem {
        min-height: 150px;
    }

    .vLine2 {
        border-top: 1px solid #000;
        margin: 2px 0px;
        height: 2px;
    }

    .rptTblFont tbody > tr > td {
        font-size: 13px !important;
    }
</style>
<div id="wrapper">

    <?php
    $decimal = 3;
    $paymentTypes = get_bill_payment_types($masters['menuSalesID']);
    $tmpPayTypes = '';
    if (!empty($paymentTypes)) {

        foreach ($paymentTypes as $paymentType) {
            $tmpPayTypes .= $paymentType['description'] . ', ';
        }

        $tmpPayTypes = '(' . rtrim($tmpPayTypes, ', ') . ')';

    }

    $data['paymentTypes'] = '';

    $companyInfo = get_companyInfo();
    $outletInfo = get_outletInfo();

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('pos_restaurent', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('calendar', $primaryLanguage);
    $uniqueID = time();


    if (!isset($email)) {
        ?>
        <div style="height: 25px;">
            <div class="pull-right">
                <button class="btn btn-xs btn-default" onclick="openEmailPrint_delivery()"><i
                        class="fa fa-envelope"></i>
                    Email
                </button>
                <button class="btn btn-xs btn-default" onclick="$.print('#print_content<?php echo $uniqueID; ?>')">
                    <i class="fa fa-print"></i> Print
                </button>
            </div>
        </div>
    <?php } ?>

    <div id="print_content<?php echo $uniqueID; ?>">


        <table border="0" style="width:100%" class="f fSize fWidth">
            <tr>
                <td colspan="2" class="ac">
                    <?php if (!isset($email)) { ?>
                        <div style="text-align: center;">
                            <?php
                            if (!empty($outletInfo['warehouseImage']) && false) {
                                $LogImage = 'uploads/warehouses/' . $outletInfo['warehouseImage'];
                                ?>
                                <img
                                    src="<?php echo base_url($LogImage) ?>"
                                    alt="Restaurant Logo" style="max-height: 80px;">
                                <?php
                            }else{
                                $LogImage = 'uploads/warehouses/' . $outletInfo['warehouseImage'];
                                ?>
                                <img
                                    src="<?php echo base_url($LogImage) ?>"
                                    alt="Restaurant Logo" style="max-height: 80px;">
                            <?php
                            }
                            ?>
                        </div>
                    <?php } ?>

                    <div class="headerTxt" style="font-size:18px !important; text-align: center;">
                        <?php echo $outletInfo['wareHouseDescription']; ?>
                    </div>

                    <div class="headerTxt" style="text-align: center;">
                        <?php echo $outletInfo['warehouseAddress']; ?>
                    </div>
                    <?php if (!empty($outletInfo['warehouseTel'])) { ?>
                        <div class="headerTxt" style="text-align: center;">
                            <?php echo 'TEL : ' . $outletInfo['warehouseTel']; ?>
                        </div>
                    <?php } ?>
                    <div class="headerTxt" style="text-align: center;">
                        <?php echo $companyInfo['companyPrintOther'] ?>
                    </div>
                </td>

            </tr>
        </table>

        <div style="font-size:20px; text-align:center; font-weight: 600">
            Sales Order
        </div>

        <hr class="vLine2">

        <table border="0" style="width:100%" class="f fSize fWidth rptTblFont">
            <tbody>

            <tr>
                <td width="50%" class="al" style="vertical-align: top;">

                    <table style="width: 100%" class="table table-condensed table-bordered orderInformation">
                        <!--f-->
                        <tr>
                            <td style="width:25%"><?php echo $this->lang->line('posr_ord_type') . ':'; ?> </td>
                            <td style="width:30%"> <?php echo $masters['customerDescription'] ?>   </td>
                        </tr>
                        <tr>
                            <td style="width:20%; ">
                                Invoice ID
                            </td>
                            <td style="width:25%;"
                                class="al"><strong><?php echo get_pos_invoice_code($masters['menuSalesID']) ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td>Customer Name:</td>
                            <td><?php echo isset($orderDetail['CustomerName']) ? $orderDetail['CustomerName'] : '-'; ?></td>

                        </tr>
                        <tr>
                            <td>Customer Address:</td>
                            <td><?php echo isset($orderDetail['CustomerAddress1']) ? $orderDetail['CustomerAddress1'] : '-'; ?></td>
                        </tr>
                        <tr>
                            <td>Phone:</td>
                            <td><?php echo isset($orderDetail['phoneNo']) ? $orderDetail['phoneNo'] : '-'; ?></td>

                        </tr>

                    </table>
                </td>
                <td style="vertical-align: top;">

                    <table border="0" class="table table-condensed table-bordered orderInformation rptTblFont"
                           style="width:100%">
                        <tr>

                            <td>Order Date</td>
                            <td>:</td>
                            <td><?php echo isset($orderDetail['createdDateTime']) ? date('d-M-Y', strtotime($orderDetail['createdDateTime'])) : '-'; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Delivery Date</strong></td>
                            <td>:</td>
                            <td>
                                <strong><?php echo isset($orderDetail['deliveryDate']) ? date('d-M-Y', strtotime($orderDetail['deliveryDate'])) : '-'; ?>
                                     </strong>
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Delivery Day</strong></td>
                            <td>:</td>
                            <td>
                                <strong>
                                    <?php
                                    $day = date('l', strtotime($orderDetail['deliveryDate']));
                                    echo $day; ?>
                                </strong>
                            </td>
                        </tr>


                        <tr>

                            <td><strong>Delivery Time</strong></td>
                            <td>:</td>
                            <td>
                                <strong><?php echo isset($orderDetail['deliveryTime']) ? date('h:i A', strtotime($orderDetail['deliveryTime'])) : '-'; ?></strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>


            </tbody>
        </table>


        <hr class="vLine2">
        <table cellspacing="0" border="0" style="width:100%" class="f fWidth rptTblFont" id="tblListItems">
            <tbody>
            <tr>
                <td style="width:5%; text-align: left;">No.</td>
                <td style="width:50%; text-align: left;">Item</td>
                <td style="width:10%; text-align: left;"> Unit</td>
                <td style="width:10%; text-align: left;">Qty</td>
                <td style="width:10%; text-align: right;"> Rate</td>
                <td style="width:15%; text-align: right;">
                    <?php echo $this->lang->line('common_price'); ?><!--Price-->
                </td>
            </tr>
            </tbody>
        </table>

        <hr class="vLine2">

        <div class="contentOfListItem">
            <table cellspacing="0" border="0" style="width:100%" class="f fWidth rptTblFont" id="tblListItems">
                <tbody>

                <?php
                $templateID = get_pos_templateID();

                $qty = 0;
                $total = 0;
                $totalTax = 0;
                $totalServiceCharge = 0;
                if (!empty($invoiceList)) {
                    $i = 1;
                    foreach ($invoiceList as $item) {
                        //print_r($item);
                        $totalTax += ($item['totalTaxAmount'] * $item['qty']);
                        $totalServiceCharge += ($item['totalServiceCharge'] * $item['qty']);
                        $sellingPrice = getSellingPricePolicy($templateID, $item['pricewithoutTax'], $item['totalTaxAmount'], $item['totalServiceCharge'], $item['qty']);
                        ?>
                        <tr>
                            <td width="5%">
                                <?php echo $i;
                                $i++ ?>
                            </td>
                            <td width="50%" align="left">
                                <?php echo $item['menuMasterDescription'] ?>
                            </td>
                            <td width="10%">
                                <?php echo !empty($item['sizeCode']) ? $item['sizeCode'] : '-'; ?>
                            </td>
                            <td width="10%">
                                <?php
                                echo $item['qty'];
                                $qty = $qty + $item['qty'];
                                ?>
                            </td>
                            <td width="10%" align="right">
                                <?php
                                $unitPrice = $sellingPrice/$item['qty'];
                                echo number_format($unitPrice,$decimal);

                                ?>
                            </td>
                            <td width="15%"
                                align="right">
                                <?php
                                //$total = $total + ($item['sellingPrice'] * $item['qty']);
                                //echo number_format(($item['sellingPrice'] * $item['qty']), 2)
                                $total = $total + $sellingPrice;
                                echo number_format($sellingPrice, $decimal)
                                ?>
                            </td>

                        </tr>
                        <?php
                    }
                }
                ?>


                </tbody>
            </table>
        </div>
        <hr class="vLine2">

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 col-md-offset-8 col-lg-offset-8">
                <table class="totals f orderInformation rptTblFont" style="width:100%" cellspacing="0" border="0">
                    <tbody>

                    <?php
                    $totalDiscount = 0;
                    $delivery = $masters['isDelivery'] == 1 ? true : false;
                    $promotion = $masters['isPromotion'] == 1 ? true : false;

                    if ($promotion) {
                        if ($delivery) {

                            // Promotion to delivery person
                        } else {
                            // Normal Promotion
                        }
                    } else if ($delivery) {
                        // delivery only

                    } else {
                        // normal payment

                    }

                    ?>
                    <tr>
                        <td colspan="2" style="text-align:left; font-weight:bold;">
                            <?php echo $this->lang->line('common_total'); ?><!--Total-->
                        </td>
                        <td colspan="2" style="text-align:right; font-weight:bold;">
                            <?php echo number_format($total, $decimal) ?>
                        </td>
                    </tr>

                    <?php

                    switch ($templateID) {
                        case 2 :
                            $total += $totalTax + $totalServiceCharge;
                            ?>
                            <tr>
                                <td colspan="2" style="text-align:left; font-weight:bold;">
                                    Total Tax
                                </td>
                                <td colspan="2" style="text-align:right; font-weight:bold;">
                                    <?php echo number_format($totalTax, $decimal) ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:left; font-weight:bold;">
                                    Total Service Charge
                                </td>
                                <td colspan="2" style="text-align:right; font-weight:bold;">
                                    <?php echo number_format($totalServiceCharge, $decimal) ?>
                                </td>
                            </tr>
                            <?php
                            break;


                        case 3 :
                            $total += $totalTax;
                            ?>
                            <tr>
                                <td colspan="2" style="text-align:left; font-weight:bold;">
                                    Total Tax
                                </td>
                                <td colspan="2" style="text-align:right; font-weight:bold;">
                                    <?php echo number_format($totalTax, $decimal) ?>
                                </td>
                            </tr>
                            <?php
                            break;
                        case 4 :
                            $total += $totalServiceCharge;
                            ?>
                            <tr>
                                <td colspan="2" style="text-align:left; font-weight:bold;">
                                    Total Service Charge
                                </td>
                                <td colspan="2" style="text-align:right; font-weight:bold;">
                                    <?php echo number_format($totalServiceCharge, $decimal) ?>
                                </td>
                            </tr>
                            <?php
                            break;

                    }
                    ?>


                    <!--Discount if Exist -->
                    <?php
                    //print_r($masters);
                    if (!empty($masters['discountPer']) && $masters['discountPer'] > 0 || true) {
                        ?>
                        <tr>
                            <td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">
                                <?php echo $this->lang->line('posr_discount'); ?><!--Discount--> <?php echo number_format($masters['discountPer'], $decimal) ?>
                                %
                            </td>
                            <td colspan="2" style="text-align:right; font-weight:bold;">
                                <?php
                                $discount = $total * ($masters['discountPer'] / 100);
                                echo '(' . number_format($discount, $decimal) . ')';
                                $totalDiscount += $discount;

                                $total -= $totalDiscount;
                                ?>
                            </td>
                        </tr>


                        <?php
                    }
                    ?>

                    <?php
                    $promoDiscountAmount = 0;
                    if ($promotion) {
                        ?>
                        <tr>
                            <td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">
                                <?php echo $this->lang->line('posr_promotional_discount'); ?><!--Promotional Discount--> <?php echo $masters['promotionDiscount'] ?>
                                %
                            </td>
                            <td colspan="2" style="text-align:right; font-weight:bold;">
                                <?php
                                $discount = $total * ($masters['promotionDiscount'] / 100);
                                echo '(' . number_format($discount, $decimal) . ')';
                                $promoDiscountAmount = $discount;
                                $totalDiscount += $discount;
                                ?>
                            </td>
                        </tr>

                        <?php
                    }
                    ?>


                    <tr>
                        <td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">
                            <?php echo $this->lang->line('posr_net_total'); ?><!-- Net Total-->
                        </td>
                        <td colspan="2" style="text-align:right; font-weight:bold; border-bottom: 1px solid black;" >
                            <?php
                            //echo number_format($total - $totalDiscount, 2);
                            echo number_format($total - $promoDiscountAmount, $decimal);
                            $netTotal = $total - $promoDiscountAmount;
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">
                            Total Advance Paid <?php //echo $tmpPayTypes ?>:
                            <?php
                            $cash = '';//$this->lang->line('common_cash');
                            $visa = '';// $this->lang->line('common_visa');
                            $master_card = ''; // $this->lang->line('common_master_card');
                            $cheque = ''; // $this->lang->line('common_cheque');
                            $payment = $masters['paymentMethod'];
                            $img = '<img src="' . base_url() . 'images/payment_type/' . $payment . '.png"> ';
                            //echo $img;

                            switch ($payment) {
                                case 1:
                                    echo '' . $cash . '';
                                    break;
                                case 2:
                                    echo '' . $visa . '';
                                    break;
                                case 3:
                                    echo '' . $master_card . '';
                                    break;
                                case 4:
                                    echo '' . $cheque . '';
                                    break;
                                default:
                                    echo '' . $cash . '';
                            }
                            ?>
                        </td>
                        <td colspan="2"
                            style="padding-top:5px; text-align:right; font-weight:bold;">
                            <?php
                            /*$totalAdvance = 600;
                            if (isset($totalAdvance)) {
                                $advancePayment = !isset($totalAdvance) ? $totalAdvance : 0;
                            }*/
                            echo number_format($totalAdvance, $decimal);


                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">
                            Balance Amount
                        </td>
                        <td colspan="2"
                            style="padding-top:5px; text-align:right; font-weight:bold;">
                            <?php
                            echo number_format(($netTotal - $totalAdvance), $decimal);

                            ?>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>


        <hr class="vLine2">

        <div class="f pad-top">
            <div class="row">
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="font-size:11px;">
                    Cashier : <?php echo current_user() ?>
                </div>
                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">

                </div>
            </div>

        </div>



        <div class="f">
            Remarks:
        </div>

        <!--<div class="f pad-top ac">-->
            <?php //echo $this->lang->line('posr_fresh_natural_care_puff');?><!--fresh & natural care puff--> <?php // echo $outletInfo['pos_footNote'] ?>
        <!--</div>-->

        <?php
        if (isset($void) && $void) {
            ?>
            <!--Only for void bills-->

            <div class="f pad-top ac">
                ***** <?php echo $this->lang->line('posr_voided_bill'); ?><!--Voided Bill--> *****
            </div>
            <div class="f pad-top">
                <?php echo $this->lang->line('posr_remarks'); ?><!--Remarks-->:
                <hr>
            </div>
            <div class="f pad-top ac" style="min-height: 40px;">
            </div>
        <?php } ?>


    </div>


    <?php
    if (isset($voidBtn) && $voidBtn) {
        ?>
        <!--Only for void bills-->
        <div class="vLine">&nbsp;</div>

        <div id="bkpos_wrp">
            <button type="button" onclick="checkPosAuthentication(3,<?php echo $masters['menuSalesID'] ?>)"
                    style="width:101%; cursor:pointer; font-size:12px; background-color:#ff7b6c; color:#000; text-align: center; border:1px solid #db6e61; padding: 10px 0px; font-weight:bold;">
                <i class="fa fa-close"></i> <?php echo $this->lang->line('posr_void_bill'); ?><!--Void Bill-->
            </button>
        </div>
    <?php } ?>


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