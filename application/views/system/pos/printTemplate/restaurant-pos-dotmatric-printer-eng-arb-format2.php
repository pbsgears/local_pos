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
    $d = get_company_currency_decimal();
    $paymentTypes = get_bill_payment_types($masters['menuSalesID']);
    $tmpPayTypes = '';
    if (!empty($paymentTypes)) {

        foreach ($paymentTypes as $paymentType) {
            $tmpPayTypes .= $paymentType['description'] . ', ';
        }
        $tmpPayTypes = rtrim($tmpPayTypes, ', ');
    }

    $data['paymentTypes'] = '';

    $companyInfo = get_companyInfo();
    //$outletInfo = get_outletInfo();
    $outletInfo = get_warehouseInfo($masters['menuSalesID']);

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('pos_restaurent', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('calendar', $primaryLanguage);
    $uniqueID = time();
    $isConfirmedDelivery = isDeliveryConfirmedOrder($masters['menuSalesID']);
    $deliveryInfo = get_deliveryConfirmedOrder($masters['menuSalesID']);
    ?>
    <div id="print_content<?php echo $uniqueID; ?>">
        <table border="0" style="width:100%" class="f fSize fWidth">
            <tbody>

            <tr>
                <td>
                    <div class="headerTxt" style="font-size:17px !important; text-align: center;">
                        <?php echo $outletInfo['wareHouseDescription']; ?>
                    </div>
                </td>
            </tr>
            <?php
            if (!empty($outletInfo['warehouseImage'])) {
                $LogImage = 'uploads/warehouses/' . $outletInfo['warehouseImage'];
                ?>
                <tr>
                    <td>
                        <div class="ac">
                            <img
                                src="<?php echo base_url($LogImage) ?>"
                                alt="Restaurant Logo" style="max-height: 80px;">
                        </div>
                    </td>
                </tr>
                <?php
            }
            ?>


            <tr>
                <td>
                    <div class="headerTxt" style="margin-top:5px;">
                        <table style="width: 100%" class="f">
                            <tr>
                                <td style="width:20%;" class="al">Name</td>
                                <td style="width:30%;"
                                    class="al"><?php echo !empty($deliveryInfo) ? $deliveryInfo['CustomerName'] : '-'; ?></td>
                                <td style="width:20%;" class="al">Date</td>
                                <td style="width:30%;"
                                    class="text-right"><?php echo date('d/m/Y', strtotime($masters['createdDateTime'])) ?></td>
                            </tr>
                            <tr>
                                <td class="al">Address</td>
                                <td class="al"><?php echo !empty($deliveryInfo) ? $deliveryInfo['CustomerAddress1'] : '-'; ?></td>
                                <td class="al">Invoice No</td>
                                <td class="ar"><?php echo get_pos_invoice_code($masters['menuSalesID']); ?></td>
                            </tr>
                            <tr>
                                <td class="al">Mobile</td>
                                <td class="al"><?php echo !empty($deliveryInfo) ? $deliveryInfo['phoneNo'] : '-'; ?></td>
                                <td class="al">Payment Type</td>
                                <td class="ar"><?php echo $tmpPayTypes ?></td>
                            </tr>
                        </table>
                        <!--<table style="width: 100%" class="f">
                            <tr>
                                <td style="width:25%; text-align: left;"> <?php /*echo $this->lang->line('posr_ord_type') . ':'; */ ?> </td>
                                <td style="width:30%"> <?php /*echo $masters['customerDescription'] */ ?>   </td>
                                <td style="width:20%; "><?php /*echo $this->lang->line('posr_inv_no') . ':'; */ ?> </td>
                                <td style="width:25%;"
                                    class="ar"><?php /*echo get_pos_invoice_code($masters['menuSalesID']) */ ?> </td>
                            </tr>
                            <tr>
                                <td style="text-align: left;"><?php /*echo $this->lang->line('common_date') . ':'; */ ?>  </td>
                                <td> <?php /*echo date('d/m/Y', strtotime($masters['createdDateTime'])) */ ?></td>
                                <td><?php /*echo $this->lang->line('common_time').':'; */ ?></td>
                                <td class="ar"><?php /*echo date('g:i A', strtotime($masters['createdDateTime'])) */ ?></td>
                            </tr>
                        </table>-->
                    </div>
                </td>
            </tr>

            </tbody>
        </table>
        <div class="vLine">&nbsp;</div>

        <div style="clear:both;" class="f"></div>
        <table cellspacing="0" border="0" style="width:100%" class="f fWidth" id="tblListItems">
            <tr>
                <td style="width:40%; text-align: left;"> Description | وصف</td>
                <td style="width:20%; text-align: left;"> @rate | سعر الوحدة</td>
                <td style="width:10%; text-align: left;">Qty | الكمية</td>
                <td style="width:30%; text-align: right;"> Price | السعر</td>
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
                        <!-- <td width="5%">
                            <?php /*echo $i;
                            $i++ */ ?>
                        </td> -->
                        <td width="40%" align="left">
                            <?php echo $item['menuMasterDescription'] ?>
                        </td>
                        <td width="20%">
                            <?php echo $item['sellingPrice'] ?>
                        </td>
                        <td width="10%" class="text-center">
                            <?php
                            echo $item['qty'];
                            $qty = $qty + $item['qty'];
                            ?>
                        </td>
                        <td width="30%"
                            align="right">
                            <?php
                            //$total = $total + ($item['sellingPrice'] * $item['qty']);
                            //echo number_format(($item['sellingPrice'] * $item['qty']), $d)
                            $total = $total + $sellingPrice;
                            echo number_format($sellingPrice, $d)
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
            <tr>
                <td style="width: 40%; vertical-align: top;">
                    <div><strong>Delivery Details</strong></div>
                    <div><?php echo !empty($deliveryInfo) ? $deliveryInfo['CustomerName'] : '&nbsp;'; ?></div>
                    <div>
                        <?php echo !empty($deliveryInfo['deliveryDate']) ? date('d/m/Y', strtotime($deliveryInfo['deliveryDate'])) : '&nbsp;'; ?>
                        <?php echo !empty($deliveryInfo['deliveryTime']) ? ', ' . date('g:H A', strtotime($deliveryInfo['deliveryTime'])) : '&nbsp;'; ?>
                    </div>
                    <div><?php echo !empty($deliveryInfo) ? $deliveryInfo['CustomerAddress1'] : '&nbsp;'; ?></div>
                    <div><?php echo !empty($deliveryInfo) ? $deliveryInfo['phoneNo'] : '&nbsp;'; ?></div>

                </td>

                <td style="width: 60%; vertical-align: top;">
                    <table class="totals f" style="width:100%" cellspacing="0" border="0">
                        <tbody>
                        <?php
                        $totalDiscount = 0;
                        $delivery = $masters['isDelivery'] == 1 ? true : false;
                        $promotion = $masters['isPromotion'] == 1 ? true : false;

                        ?>
                        <tr>
                            <td colspan="2" style="text-align:left; font-weight:bold;">
                                Total | مجموع
                            </td>
                            <td colspan="2" style="text-align:right; font-weight:bold;">
                                <?php echo number_format($total, $d) ?>
                            </td>
                        </tr>

                        <?php

                        switch ($templateID) {
                            case 2 :

                                /**
                                 *  Service Charge only for Dine-in Customers
                                 *  only applied in Tax and SC separated tmpleate & SC separated template
                                 *
                                 *  Template
                                 *  2 - Tax & Service Charge Separated
                                 *  4 - Service Charge Separated
                                 *
                                 *  */

                                $is_dineIn_order = is_dineIn_order($masters['menuSalesID']);
                                if ($is_dineIn_order) {
                                    $hide = ' ';
                                    $total += $totalTax + $totalServiceCharge;
                                } else {
                                    $hide = ' hide ';
                                    $total += $totalTax;
                                    $totalServiceCharge = 0;
                                }

                                ?>
                                <tr>
                                    <td colspan="2" style="text-align:left; font-weight:bold;">
                                        Total Tax | مجموع الضريبة
                                    </td>
                                    <td colspan="2" style="text-align:right; font-weight:bold;">
                                        <?php echo number_format($totalTax, $d) ?>
                                    </td>
                                </tr>
                                <tr class="<?php echo $hide ?>">
                                    <td colspan="2" style="text-align:left; font-weight:bold;">
                                        Total Service Charge | إجمالي رسوم الخدمة
                                    </td>
                                    <td colspan="2" style="text-align:right; font-weight:bold;">
                                        <?php echo number_format($totalServiceCharge, $d) ?>
                                    </td>
                                </tr>
                                <?php
                                break;


                            case 3 :
                                $total += $totalTax;
                                ?>
                                <tr>
                                    <td colspan="2" style="text-align:left; font-weight:bold;">
                                        Total Tax | مجموع الضريبة
                                    </td>
                                    <td colspan="2" style="text-align:right; font-weight:bold;">
                                        <?php echo number_format($totalTax, $d) ?>
                                    </td>
                                </tr>
                                <?php
                                break;
                            case 4 :
                                /**
                                 *  Service Charge only for Dine-in Customers
                                 *  only applied in Tax and SC separated tmpleate & SC separated template
                                 *
                                 *  Template
                                 *  2 - Tax & Service Charge Separated
                                 *  4 - Service Charge Separated
                                 *
                                 *  */

                                $is_dineIn_order = is_dineIn_order($masters['menuSalesID']);
                                if ($is_dineIn_order) {
                                    $hide = '';
                                    $total += $totalServiceCharge;

                                } else {
                                    $total += 0;
                                    $totalServiceCharge = 0;
                                    $hide = ' hide ';
                                }
                                ?>
                                <tr class="<?php echo $hide ?>">
                                    <td colspan="2" style="text-align:left; font-weight:bold;">
                                        Total Service Charge | إجمالي رسوم الخدمة
                                    </td>
                                    <td colspan="2" style="text-align:right; font-weight:bold;">
                                        <?php echo number_format($totalServiceCharge, $d) ?>
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
                                    Discount | (خصم)
                                    <?php //echo number_format($masters['discountPer'], $d).'%' ?>

                                </td>
                                <td colspan="2" style="text-align:right; font-weight:bold;">
                                    <?php
                                    $discount = $total * ($masters['discountPer'] / 100);
                                    echo '(' . number_format($discount, $d) . ')';
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
                                    <?php echo $this->lang->line('posr_promotional_discount'); ?> | (خصم ترويجي)
                                    <?php //echo $masters['promotionDiscount'].'%' ?>
                                </td>
                                <td colspan="2" style="text-align:right; font-weight:bold;">
                                    <?php
                                    $discount = $total * ($masters['promotionDiscount'] / 100);
                                    echo '(' . number_format($discount, $d) . ')';
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
                                Net Total | الصافي الاجمالي
                            </td>
                            <td colspan="2" style="text-align:right; font-weight:bold; border-bottom: 1px solid;">
                                <?php
                                //echo number_format($total - $totalDiscount, $d);
                                echo number_format($total - $promoDiscountAmount, $d);
                                $netTotal = $total - $promoDiscountAmount;
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2" style="text-align:left; font-weight:bold; padding-top:1px;">
                                <!--Paid By-->
                                <?php
                                if ($delivery) {
                                    $paidByAmount = number_format($total - $totalDiscount, $d);
                                    $paidAmount = $total - $totalDiscount;
                                    $balance = $paidAmount - $netTotal;

                                } else {
                                    $paidByAmount = number_format($masters['cashReceivedAmount'], $d);
                                    $paidAmount = $masters['cashReceivedAmount'];
                                    $balance = $paidAmount - $netTotal;
                                }

                                $totalPaidAmount = get_paidAmount($masters['menuSalesID']);
                                $advancePayment = $totalPaidAmount - $paidAmount;

                                if ($isConfirmedDelivery) {
                                    echo 'Total Paid  ( إجمالي المبلغ المدفوع )';
                                } else {
                                    echo 'Paid Amount ( المبلغ المدفوع )';
                                }
                                if ($paidAmount != 0) {
                                    if (!$isConfirmedDelivery) {
                                        //echo $tmpPayTypes;
                                    }
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
                                <?php
                                if ($isConfirmedDelivery) {
                                    $totalPaid = get_paidAmount($masters['menuSalesID']);
                                    echo number_format(abs($totalPaid), $d);
                                    $paidByAmount = $totalPaid;
                                } else {
                                    echo $paidByAmount;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align:left; font-weight:bold; padding-top:1px;">
                                <?php

                                if ($isConfirmedDelivery) {

                                    $balanceAmount = $netTotal - $totalPaid;
                                    //echo 'Balance Payable:';
                                    if ($balanceAmount > 0) {
                                        echo 'Balance Payable | الرصيد المستحق الدفع';
                                    } else {
                                        echo 'Balance | توازن';
                                    }
                                } else {
                                    echo 'Balance | توازن';
                                }

                                ?>
                            </td>
                            <td colspan="2"
                                style="padding-top:1px; text-align:right; font-weight:bold;">
                                <?php
                                if ($isConfirmedDelivery) {

                                    if ($masters['balanceAmount'] != 0) {
                                        $balanceAmount = $masters['balanceAmount'];
                                    } else {
                                        $balanceAmount = $netTotal - $totalPaid;
                                    }

                                    echo number_format($balanceAmount, $d);

                                } else {
                                    echo number_format($balance, $d);
                                }

                                ?>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </td>

            </tr>
        </table>


        <div class="vLine">&nbsp;</div>
        <table border="0" style="width:100%" class="f fSize fWidth">
            <tbody>
            <tr>
                <td width="100%" class="ac">
                    <div class="headerTxt" style="text-align: center;">
                        <?php echo $outletInfo['warehouseAddress']; ?>
                    </div>
                    <div class="headerTxt" style="text-align: center;">
                        <?php echo 'TEL : ' . $outletInfo['warehouseTel']; ?>
                    </div>
                    <div class="headerTxt" style="text-align: center;">
                        <?php echo $companyInfo['companyPrintOther'] ?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>


        <div class="f pad-top ac">
            <?php //echo $this->lang->line('posr_fresh_natural_care_puff');?><!--fresh & natural care puff--> <?php echo $outletInfo['pos_footNote'] ?>
        </div>
        <div class="pad-top" style="font-size:11px;">
            Cashier : <?php echo get_employeeShortName() ?>
        </div>
        <?php
        if (isset($wifi) && $wifi) {
            $wifi_pw = is_wifi_password_in_bill();
            if ($wifi_pw) {
                ?>
                <div class="pad-top" style="font-size:11px;">
                    WiFi Password : <strong><?php
                        $wifi = get_random_wifi_password();
                        echo $wifi['wifiPassword'];
                        ?></strong>
                </div>
                <?php
                /** used password  */
                update_wifi_password($wifi['id'], $masters['menuSalesID']);
            }
        }

        ?>

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

                <button type="button" onclick=" $.print('#print_content<?php echo $uniqueID ?>')"
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
    if (isset   ($voidBtn) && $voidBtn) {
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