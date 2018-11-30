<style type="text/css">
    .headerTxt {
        font-size: 11px !important;
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
        font-family: 'Raleway', Arial, sans-serif !important;
    }

    .pad-top {
        padding-top: 1px;
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
        padding: 0px 1px !important;

    }

    .vLine {
        border-top: 1px dashed #000;
        margin: 4px 0px;
        height: 2px;
    }

    .printAdvance {
        margin-bottom: 7px !important;
        height: 38px !important;
        border-radius: 0px !important;
    }

</style>
<div id="wrapper">

    <?php
    $paymentTypes = get_bill_payment_types($masters['menuSalesID']);
    $tmpPayTypes = '';
    if (!empty($paymentTypes)) {

        foreach ($paymentTypes as $paymentType) {
            $tmpPayTypes .= $paymentType['description'] . ', ';
        }
        //$tmpResult = join(',', $paymentTypes);
        $tmpPayTypes = '(' . rtrim($tmpPayTypes, ', ') . ')';
        //$tmpPayTypes = '(' . rtrim($tmpPayTypes, ', ') . ')';
        //echo $tmpPayTypes;
        //exit;
    }

    $data['paymentTypes'] = '';

    $companyInfo = get_companyInfo();
    $outletInfo = get_outletInfo();
    /*echo '<pre>';
    print_r($outletInfo);
    echo '</pre>';*/
    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('pos_restaurent', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('calendar', $primaryLanguage);
    $uniqueID = time();


    ?>
    <div id="print_content<?php echo $uniqueID; ?>">
        <script>
            function print_paymentReceipt() {
                $.print('#print_content<?php echo $uniqueID ?>');
                setTimeout(function () {
                    $("#rpos_print_template").modal('hide');
                }, 3000);
            }
        </script>
        <table border="0" style="width:100%" class="f fSize fWidth">
            <tbody>
            <tr>
                <td width="100%" class="ac">
                    <?php
                    if (!empty($outletInfo['warehouseImage'])) {
                        $LogImage = 'uploads/warehouses/' . $outletInfo['warehouseImage'];
                        ?>
                        <div>
                            <img
                                src="<?php echo base_url($LogImage) ?>"
                                alt="Restaurant Logo" style="max-height: 80px;">
                        </div>
                        <?php
                    }
                    ?>

                    <div style=" padding: 0px; font-size:11px;">WELCOME TO</div>
                    <div class="headerTxt" style="font-size:17px !important; text-align: center;">
                        <?php echo $masters['wareHouseDescription']; ?>
                    </div>
                    <div class="headerTxt" style="text-align: center;">
                        <?php echo $masters['warehouseAddress']; ?>
                    </div>
                    <div class="headerTxt" style="text-align: center;">
                        <?php echo 'TEL : ' . $masters['warehouseTel']; ?>
                    </div>
                    <div class="headerTxt" style="text-align: center;">
                        <?php echo $companyInfo['companyPrintOther'] ?>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="headerTxt" style="margin-top:5px;">
                        <table style="width: 100%" class="f">
                            <tr>
                                <td style="width:25%; text-align: left;">
                                    <?php echo $this->lang->line('posr_ord_type'); ?><!--Ord.Type-->
                                    :
                                </td>
                                <td style="width:30%"> <?php echo $masters['customerDescription'] ?>   </td>
                                <td style="width:20%; "><?php echo $this->lang->line('posr_inv_no'); ?><!--Inv. No-->:
                                </td>
                                <td style="width:25%;"
                                    class="ar"><?php echo get_pos_invoice_code($masters['menuSalesID']) ?> </td>
                            </tr>
                            <tr>
                                <td style="text-align: left;"><?php echo $this->lang->line('common_date'); ?><!--Date-->
                                    :
                                </td>
                                <td> <?php echo date('d/m/Y', strtotime($masters['createdDateTime'])) ?></td>
                                <td><?php echo $this->lang->line('common_time'); ?><!--Time-->:</td>
                                <td class="ar"><?php echo date('g:i A', strtotime($masters['createdDateTime'])) ?></td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>

            </tbody>
        </table>


        <div style="clear:both;" class="f"></div>
        <table cellspacing="0" border="0" style="width:100%" class="f fWidth" id="tblListItems">
            <tr>
                <!--<td style="width:5%; text-align: left;"><em>#</em></td>-->
                <td style="width:20%; text-align: left;">
                    <?php echo $this->lang->line('common_description'); ?><!--Description--></td>
                <td style="width:5%; text-align: left;"><?php echo $this->lang->line('posr_qyt'); ?><!--Qty--></td>
                <td style="width:15%; text-align: right;">
                    <?php echo $this->lang->line('common_price'); ?><!--Price--></td>
            </tr>
        </table>
        <div class="vLine">&nbsp;</div>
        <table cellspacing="0" border="0" style="width:100%" class="f fWidth" id="tblListItems">
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
                        <!--<td width="5%">
                            <?php /*echo $i;
                            $i++ */ ?>
                        </td>-->
                        <td width="20%" align="left">
                            <?php echo $item['menuMasterDescription'] ?>
                        </td>
                        <td width="5%">
                            <?php
                            echo $item['qty'];
                            $qty = $qty + $item['qty'];
                            ?>
                        </td>
                        <td width="15%" align="right">
                            <?php
                            //$total = $total + ($item['sellingPrice'] * $item['qty']);
                            //echo number_format(($item['sellingPrice'] * $item['qty']), 2)
                            $total = $total + $sellingPrice;
                            echo number_format($sellingPrice, 2)
                            ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
        <div class="vLine">&nbsp;</div>

        <table class="totals f" style="width:100%" cellspacing="0" border="0">
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
                    <?php echo number_format($total, 2) ?>
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
                            <?php echo number_format($totalTax, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:left; font-weight:bold;">
                            Total Service Charge
                        </td>
                        <td colspan="2" style="text-align:right; font-weight:bold;">
                            <?php echo number_format($totalServiceCharge, 2) ?>
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
                            <?php echo number_format($totalTax, 2) ?>
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
                            <?php echo number_format($totalServiceCharge, 2) ?>
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
                    <td colspan="2" style="text-align:left; font-weight:bold; padding-top:1px;">
                        <?php echo $this->lang->line('posr_discount'); ?><!--Discount--> <?php echo number_format($masters['discountPer'], 2) ?>
                        %
                    </td>
                    <td colspan="2" style="text-align:right; font-weight:bold;">
                        <?php
                        $discount = $total * ($masters['discountPer'] / 100);
                        echo '(' . number_format($discount, 2) . ')';
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
                    <td colspan="2" style="text-align:left; font-weight:bold; padding-top:1px;">
                        <?php
                        $description = get_promotionDescription($masters['promotionID']);
                        //echo $this->lang->line('posr_promotional_discount');
                        echo $description;

                        ?><!--Promotional Discount-->
                        <?php echo $masters['promotionDiscount'] . '%' ?>

                    </td>
                    <td colspan="2" style="text-align:right; font-weight:bold;">
                        <?php
                        $discount = $total * ($masters['promotionDiscount'] / 100);
                        echo '(' . number_format($discount, 2) . ')';
                        $promoDiscountAmount = $discount;
                        $totalDiscount += $discount;
                        ?>
                    </td>
                </tr>

                <?php
            }
            ?>


            <tr>
                <td colspan="2" style="text-align:left; font-weight:bold; padding-top:1px;">
                    <?php echo $this->lang->line('posr_net_total'); ?><!-- Net Total-->
                </td>
                <td colspan="2" style="text-align:right; font-weight:bold;">
                    <?php
                    //echo number_format($total - $totalDiscount, 2);
                    echo number_format($total - $promoDiscountAmount, 2);
                    $netTotal = $total - $promoDiscountAmount;
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div style="margin-top:4px;"></div>
                </td>
            </tr>
            <?php
            $payments = get_pos_payments_by_menuSalesID($masters['menuSalesID']);

            if (!empty($payments)) {
                foreach ($payments as $payment) {
                    ?>
                    <tr>
                        <td colspan="2">
                            <strong>
                                <?php
                                if ($payment['paymentConfigMasterID'] == 7) {
                                    echo !empty($payment['CustomerName']) ? $payment['CustomerName'] : $payment['description'];
                                } else if ($payment['paymentConfigMasterID'] == 25) {
                                    echo $payment['description'] . ' (' . $payment['reference'] . ')';
                                } else {
                                    echo $payment['description'];
                                }
                                ?>
                            </strong>
                        </td>
                        <td class="text-right">
                            <strong>
                                <?php
                                if ($payment['autoID'] == 1) {
                                    /*actual cash amount paid by customer */
                                    $payment['amount'] = $masters['cashAmount'];
                                }
                                echo number_format($payment['amount'], 2)
                                ?>
                            </strong>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            <tr>
                <td colspan="3">
                    <div style="margin-top:4px;"></div>
                </td>
            </tr>
            <tr class="hide">
                <td colspan="2" style="text-align:left; font-weight:bold; padding-top:1px;">
                    <!--Paid By-->
                    <?php
                    if ($delivery) {
                        $paidByAmount = number_format($total - $totalDiscount, 2);
                        $paidAmount = $total - $totalDiscount;
                        $balance = $paidAmount - $netTotal;

                    } else {
                        $paidByAmount = number_format($masters['cashReceivedAmount'], 2);
                        $paidAmount = $masters['cashReceivedAmount'];
                        $balance = $paidAmount - $netTotal;
                    }

                    $totalPaidAmount = get_paidAmount($masters['menuSalesID']);
                    $advancePayment = $totalPaidAmount - $paidAmount;

                    echo $this->lang->line('posr_paid_by');
                    if ($paidAmount != 0) {
                        echo $tmpPayTypes;
                    }
                    echo ':';

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
                    style="padding-top:1px; text-align:right; font-weight:bold;">
                    <?php echo $paidByAmount; ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:left; font-weight:bold; padding-top:1px;">
                    <?php
                    if ($balance < 0) {
                        echo 'Balance Payable:';
                    } else {
                        echo $this->lang->line('common_change');
                    }
                    ?>
                </td>
                <td colspan="2"
                    style="padding-top:1px; text-align:right; font-weight:bold;">
                    <?php
                    echo number_format($balance, 2);
                    /*if ($delivery){
                        echo number_format(0, 2);
                    } else {
                        if ($promotion) {
                            echo number_format(($masters['balanceAmount'] + $totalDiscount), 2);
                        } else {
                            echo number_format(($masters['balanceAmount']), 2);

                        }
                    }*/
                    ?>
                </td>
            </tr>

            </tbody>
        </table>

        <div class="vLine">&nbsp;</div>

        <div class="pad-top">
            Cashier : <?php echo get_employeeShortName() ?>
        </div>
        <div class="f pad-top ac">
            <?php //echo $this->lang->line('posr_fresh_natural_care_puff');?><!--fresh & natural care puff--> <?php echo $outletInfo['pos_footNote'] ?>
        </div>

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
    if (isset($email) && $email) {
    } else {
        ?>
        <div class="vLine">&nbsp;</div>

        <div id="bkpos_wrp">
            <?php if ($auth) { ?>
                <button type="button" onclick="checkPosAuthentication(4,<?php echo $uniqueID ?>)"
                        style="width:101%; cursor:pointer; font-size:12px; background-color:#FFA93C; color:#000; text-align: center; border:1px solid #FFA93C; padding: 10px 0px; font-weight:bold;">
                    <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
                </button>
            <?php } else {

                $invoiceID = $masters['menuSalesID'];
                if ($invoiceID > 0) {
                    $isDelivery = isDeliveryConfirmedOrder($invoiceID);
                    if ($isDelivery) {
                        ?>
                        <button type="button" class="btn btn-default btn-block printAdvance"
                                onclick="print_delivery_order_payments()">
                            <i class="fa fa-print"></i> Print Advance Payment
                        </button>
                        <?php
                    }
                }
                //echo $invoiceID;
                ?>


                <?php $result = isPos_invoiceSessionExist(); ?>

                <button type="button" onclick="print_paymentReceipt()"
                        style="width:101%; cursor:pointer; font-size:12px; background-color:#FFA93C; color:#000; text-align: center; border:1px solid #FFA93C; padding: 10px 0px; font-weight:bold;">
                    <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
                </button>
                <?php
            } ?>
        </div>

        <div id="bkpos_wrp" style="margin-top: 8px;">
        <span class="left">
            <button type="button" onclick="openemailPrintmodule()"
                    style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center; color:#000; background-color:#4FA950; border:2px solid #4FA950; padding: 10px 0px; font-weight:bold;"
                    id="email"><i class="fa fa-envelope-o" aria-hidden="true"></i>
                <?php echo $this->lang->line('common_email'); ?><!--Email--></button></span>
        </div>
    <?php } ?>

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

