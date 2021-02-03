<style type="text/css">
    .headerTxt{font-size:11px!important;margin:0;text-align:center}.fWidth{width:100%!important}.fSize{font-size:12px!important}.f{font-family:Raleway,Arial,sans-serif!important}.pad-top{padding-top:1px}.ac{text-align:center!important}.ar{text-align:right!important}.al{text-align:left!important}#tblListItems tr td{padding:0 1px!important}.vLine{border-top:1px dashed #000;margin:4px 0;height:2px}.printAdvance{margin-bottom:7px!important;height:38px!important;border-radius:0!important}
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
        $tmpPayTypes = '(' . rtrim($tmpPayTypes, ', ') . ')';
    }

    $data['paymentTypes'] = '';

    $companyInfo = get_companyInfo();
    $outletInfo = get_warehouseInfo($masters['menuSalesID'], $masters['wareHouseAutoID']);
    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('pos_restaurent', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('calendar', $primaryLanguage);
    $uniqueID = time();
    $isConfirmedDelivery = isDeliveryConfirmedOrder($masters['menuSalesID']);
    $deliveryInfo = get_deliveryConfirmedOrder($masters['menuSalesID']);
    ?>

    <style>

        @media print {
            body * {
                visibility: hidden;
            }

            .myCustomPrint * {
                visibility: visible;
            }

            .myCustomPrint {
                position: absolute;
                left: 0;
                top: 0;
            }

        }

        @page
        {
            size: auto;   /* auto is the initial value */
            margin-left: 10mm;  /* this affects the margin in the printer settings */
            margin-top: 0mm;  /* this affects the margin in the printer settings */
            margin-bottom: 0mm;  /* this affects the margin in the printer settings */
        }

    </style>
    <script src="<?php echo base_url('plugins/printJS/jQuery.print.js') ?>" type="text/javascript"></script>
    <script>
        function printElement(elem, append, delimiter) {
            var domClone = elem.cloneNode(true);

            var $printSection = document.getElementById("printSection");

            if (!$printSection) {
                var $printSection = document.createElement("div");
                $printSection.id = "printSection";
                document.body.appendChild($printSection);
            }

            if (append !== true) {
                $printSection.innerHTML = "";
            }

            else if (append === true) {
                if (typeof(delimiter) === "string") {
                    $printSection.innerHTML += delimiter;
                }
                else if (typeof(delimiter) === "object") {
                    $printSection.appendChlid(delimiter);
                }
            }

            $printSection.appendChild(domClone);
        }

        function print_paymentReceipt(parameter = null) {
            var screenWidth = $(window).width();

            if(screenWidth < 768){
                if(parameter !== null){
                    window.print();
                } else{
                    window.print();

                }
            }else{
                if(parameter == null){
                    parameter = <?php echo $uniqueID ?>;
                }
                $.print("#print_content" + parameter);
            }
            $("#pos_sampleBill").modal('hide');
            setTimeout(function () {
                $("#rpos_print_template").modal('hide');
            }, 5000);
        }
    </script>

    <?php if (isset($from_up_coming)) { echo '<div style="width: 570px; margin-left: 20%;">'; }?>

    <div id="print_content<?php echo $uniqueID; ?>" >
        <div class="myCustomPrint" style="margin: 0 auto;width: 80%;">
        <table border="0" style="width:100%" class="f fSize fWidth">
            <tbody>
            <tr>
                <td width="100%" class="ac">
                    <?php
                    if (!isset($from_up_coming)) {
                        if (!empty($outletInfo['warehouseImage'])) {
                            $LogImage = 'uploads/warehouses/' . str_replace("/", "", $outletInfo['warehouseImage']);
                            ?>
                            <div>
                                <img
                                src="<?php echo base_url($LogImage) ?>"
                                alt="Restaurant Logo" style="max-height: 80px;">
                            </div>
                            <?php
                        }
                    }
                    ?>

                    <?php if (!isset($from_up_coming)) { ?>
                    <div style=" padding: 0px; font-size:11px;">WELCOME TO</div>
                    <?php } ?>
                    <div class="headerTxt" style="font-size:17px !important; text-align: center;">
                        <?php echo $outletInfo['wareHouseDescription']; ?>
                    </div>
                    <?php if (isset($from_up_coming)) {
                        echo '<div class="" style="font-size:11px; text-align: left;">Customer Name : '.$masters['customerName'].'</div>';
                    } ?>
                    <?php if (!isset($from_up_coming)) { ?>
                    <div class="headerTxt" style="text-align: center;">
                        <?php echo $outletInfo['warehouseAddress']; ?>
                    </div>
                    <div class="headerTxt" style="text-align: center;">
                        <?php echo 'TEL : ' . $outletInfo['warehouseTel']; ?>
                    </div>
                    <div class="headerTxt" style="text-align: center;">
                        <?php echo $companyInfo['companyPrintOther'] ?>
                    </div>
                    <?php } ?>
                </td>
            </tr>
            </tbody>
        </table>
<br>
        <table border="0" style="width:100%" class="f fSize fWidth">
            <tbody>
                <tr>
                    <td style="width:18%; text-align: left;">
                        <?php echo $this->lang->line('posr_ord_type'); ?> <!--Ord.Type-->:    نوع الطلب
                    </td>
                    <td style="width:30%"> <?php echo $masters['customerDescription'] ?>   </td>
                    <td style="width:20%; "><?php echo $this->lang->line('posr_inv_no'); ?>   <!--Inv. No-->: رقم الفاتورة
                    </td>
                    <td style="width:25%;"
                        class="ar"><?php echo get_pos_invoice_code($masters['menuSalesID'], $masters['wareHouseAutoID']) ?> </td>
                </tr>
                <tr>
                    <td style="text-align: left;"><?php echo $this->lang->line('common_date'); ?>  : <!--Date--> التاريخ

                    </td>
                    <td> <?php echo date('d/m/Y', strtotime($masters['createdDateTime'])) ?></td>
                    <td><?php echo $this->lang->line('common_time'); ?>  <!--Time-->: الوقت </td>
                    <td class="ar"><?php echo date('g:i A', strtotime($masters['createdDateTime'])) ?></td>
                </tr>
                <tr>
                    <td style="text-align: left;">Name :  الإسم</td>
                    <?php
                    $menusalescust='';
                    if($masters['isCreditSales']==1){
                        $menusalescust=get_credit_salesCustomers($masters['menuSalesID']);
                    }

                    if(!empty($deliveryInfo)){
                        ?>
                        <td style="width:30%"><?php echo !empty($deliveryInfo) ? $deliveryInfo['CustomerName'] : '-'; ?></td>
                        <?php
                    }elseif(!empty($masters['cusname'])){
                        ?>
                        <td style="width:30%"><?php echo $masters['cusname'] ?></td>
                        <?php
                    }elseif(!empty($menusalescust)){
                        ?>
                        <td style="width:30%"><?php echo $menusalescust['CustomerName'] ?></td>
                        <?php
                    }else{
                        ?>
                        <td style="width:30%">-</td>
                        <?php
                    }
                    ?>
                    <td style="text-align: left;">Mobile : </td>
                    <?php
                    if(!empty($deliveryInfo)){
                        ?>
                        <td style="width:30%"><?php echo !empty($deliveryInfo) ? $deliveryInfo['phoneNo'] : '-'; ?></td>
                        <?php
                    }elseif(!empty($masters['custel'])){
                        ?>
                        <td style="width:30%"><?php echo $masters['custel']; ?></td>
                        <?php
                    }elseif(!empty($menusalescust)){
                        ?>
                        <td style="width:30%"><?php echo $menusalescust['customerTelephone'] ?></td>
                        <?php
                    }else{
                        ?>
                        <td style="width:30%">-</td>
                        <?php
                    }
                    ?>
                </tr>
            </tbody>
        </table>


        <div style="clear:both;" class="f"></div>
        <table cellspacing="0" border="0" style="width:100%" class="f fWidth" id="tblListItems">
            <tr>
                <td style="width:20%; text-align: left;"> Description | وصف</td>
                <td style="width:5%; text-align: left;">Qty | الكمية</td>
                <td style="width:15%; text-align: right;"> Price | السعر</td>
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
                    $totalTax += ($item['totalTaxAmount'] * $item['qty']) - (($item['totalTaxAmount'] * $item['qty']) * $item['discountPer'] / 100);
                    $totalServiceCharge += ($item['totalServiceCharge'] * $item['qty']) - (($item['totalServiceCharge'] * $item['qty']) * $item['discountPer'] / 100);
                    //$item['pricewithoutTax'] = $item['pricewithoutTax'] - round(($item['pricewithoutTax'] * $item['discountPer'] / 100),2);
                    $item['pricewithoutTax'] = $item['pricewithoutTax'] - ($item['discountAmount']/$item['qty']);
                    $item['totalTaxAmount'] = $item['totalTaxAmount'] - ($item['totalTaxAmount'] * $item['discountPer'] / 100);
                    $item['totalServiceCharge'] = $item['totalServiceCharge'] - ($item['totalServiceCharge'] * $item['discountPer'] / 100);
                    $sellingPrice = getSellingPricePolicy($templateID, $item['pricewithoutTax'], $item['totalTaxAmount'], $item['totalServiceCharge'], $item['qty']);
                    $comboSub=get_pos_combos($item['menuSalesID'],$item['menuSalesItemID'],$item['warehouseMenuID']);
                    ?>
                    <tr>
                        <!--<td width="5%">
                            <?php /*echo $i;
                            $i++ */ ?>
                        </td>-->
                        <td width="20%" align="left">
                            <?php echo $item['menuMasterDescription'] ?>
                            <?php echo isset($item['discountPer']) && $item['discountPer'] > 0 ? '(' . $item['discountPer'] . '% Dis.)' : ''; ?>
                        </td>
                        <td width="5%">
                            <?php
                            echo $item['qty'];
                            $qty = $qty + $item['qty'];
                            ?>
                        </td>
                        <td width="15%"
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
                    if(!empty($comboSub)){
                        foreach($comboSub as $cmbo){
                            ?>
                            <tr>
                                <td width="20%" align="left" style="padding-left: 10px !important;">* <?php echo $cmbo['menuMasterDescription'] ?></td>
                                <td width="5%"> <?php echo $cmbo['qty'] ?></td>
                                <td width="15%" align="right">&nbsp;</td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
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
                ?>

                <?php

            }

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
                            Municipality Tax | ضريبة البلدية
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
                            Municipality Tax | ضريبة البلدية
                        </td>
                        <td colspan="2" style="text-align:right; font-weight:bold;">
                            <?php echo number_format($totalServiceCharge, $d) ?>
                        </td>
                    </tr>
                    <?php
                    break;

            }
            ?>

            <!-- Outlet taxes print here if exist. -->
            <?php
            if (isset($isSample) && $isSample == true) {
                $promoDiscountAmount = 0;
                $discount = $total * ($masters['promotionDiscount'] / 100);
                $promoDiscountAmount = $discount;
                $totalDiscount += $discount;
                $netTotal = $total - $promoDiscountAmount;
                $outletTaxes = array();
                foreach ($outletTaxMaster as $item) {
                    $taxAmount = (number_format($netTotal, $d) / 100) * number_format($item->taxPercentage, $d);
                    echo '<tr>
                <td colspan="2" style="text-align:left; font-weight:bold;">
                  ' . $item->taxDescription . '
                </td>
                <td colspan="2" style="text-align:right; font-weight:bold;">
                  ' . number_format($taxAmount, $d) . '
                </td>
            </tr>';
                    $outletTaxes[] = array('taxAmount' => $taxAmount);
                }
            } else {
                $outletTaxes = outlet_taxes_list($masters['menuSalesID']);
                foreach ($outletTaxes as $taxItem) {
                    echo '<tr>
                <td colspan="2" style="text-align:left; font-weight:bold;">
                   ' . $taxItem['taxDescription'] . '
                </td>
                <td colspan="2" style="text-align:right; font-weight:bold;">
                   ' . number_format($taxItem['taxAmount'], $d) . '
                </td>
            </tr>';
                }
            }
            ?>

            <!--Discount if Exist -->
            <?php
            //print_r($masters);
            if (!empty($masters['discountPer']) && $masters['discountPer'] > 0 || true) {
            $discount = $total * ($masters['discountPer'] / 100);
            if($discount>0){
                ?>
                <tr>
                    <td colspan="2" style="text-align:left; font-weight:bold; padding-top:1px;">
                        Discount | (خصم)
                        <?php //echo number_format($masters['discountPer'], $d).'%' ?>

                    </td>
                    <td colspan="2" style="text-align:right; font-weight:bold;">
                        <?php
                        echo '(' . number_format($discount, $d) . ')';
                        $totalDiscount += $discount;

                        $total -= $totalDiscount;
                        ?>
                    </td>
                </tr>


                <?php
            }}
            ?>

            <?php
            $promoDiscountAmount = 0;
            if ($promotion) {
                ?>
                <tr>
                    <td colspan="2" style="text-align:left; font-weight:bold; padding-top:1px;">
                        prmt.Discount | (خصم ترويجي)(<?php echo $masters['promotn'].'%' ?>)
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
                <td colspan="2" style="text-align:right; font-weight:bold;">
                    <?php
                    $netTotal = $total - $promoDiscountAmount;
                    $taxTotal = 0;

                    foreach ($outletTaxes as $taxItem) {
                        $taxTotal = $taxTotal + $taxItem['taxAmount'];
                    }
                    $netTotal = $netTotal + $taxTotal;
                    echo number_format($netTotal, $d);
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

        <div class="vLine">&nbsp;</div>

        <div class="pad-top">
           Cashier | أمين الصندوق <!--Cashier--> : <?php echo get_employeeShortName($masters['createdUserID']) ?>
        </div>

        <?php
        if (isset($wifi) && $wifi) {
            $wifi_pw = is_wifi_password_in_bill();
            if ($wifi_pw) {
                ?>
                <div class="pad-top">
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

        <?php if(!isset($from_up_coming)){ ?>
        <div class="f pad-top ac">
            <?php //echo $this->lang->line('posr_fresh_natural_care_puff');?><!--fresh & natural care puff--> <?php echo $outletInfo['pos_footNote'] ?>
        </div>
        <?php } ?>

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

    </div>

    <?php
    if (isset($email) && $email) {
        $reprint=reprint_salesdetail_print($masters['wareHouseAutoID']);
       if($reprint==1){ ?>
            <button type="button" onclick="print_paymentReceipt()"
                    style="width:101%; cursor:pointer; font-size:12px; background-color:#FFA93C; color:#000; text-align: center; border:1px solid #FFA93C; padding: 10px 0px; font-weight:bold;">
                <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
            </button>
        <?php }
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
