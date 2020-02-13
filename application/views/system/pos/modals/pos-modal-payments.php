<style>
    .touchSizeButton {
        width: 50px;
        height: 33px;
        font-weight: 700;
    }

    .customBtnNumb {
        padding: 17px;
        margin: 3px 4px;
        font-size: 18px;
        /* background-color: rgba(255, 255, 57, 0.14); */
        height: 62px;
        width: 28%;
        font-size: 21px;
    }

    .currencyNoteBtn {
        padding: 0px;
        margin: 4px 11px;
        font-size: 18px;
        height: 38px;
        width: 100%;
    }

    .formRowPad {
        padding-top: 5px;
        padding-bottom: 8px;
    }

    .al {
        text-align: right !important;
    }

    .lbl-delivery {
        background-color: #b96868;
        padding: 4px 15px;
        color: #ffffff;
        font-weight: 600;
    }

    .payment-font {
        font-size: 14px !important;
        font-weight: 600 !important;
    }

    .tbl-payment-font {
        font-size: 15px !important;
        font-weight: 600 !important;
        font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif !important;
    }

    .btn-strong {
        font-weight: 800;
    }

    .btn-xl {
        padding: 15px 16px !important;
    }

    .payment-tblSize {
        width: 120px !important;;

    }

</style>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$get_outletID = get_outletID();
$current_companyID = current_companyID();
$isOutletTaxEnabled = json_encode(isOutletTaxEnabled($get_outletID, $current_companyID));
?>

<div aria-hidden="true" role="dialog" tabindex="-1" id="pos_submitted_payments_modal" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg"
         style="width: <?php //echo !isset($modal_width) && !empty($modal_width) ? $modal_width . '%' : '70%'; ?>">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" onclick="close_update_pos_submitted()" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h3 class="modal-title"><?php echo $this->lang->line('common_payment'); ?><!--Payment--></h3>
            </div>
            <div class="modal-body" style="overflow: visible; background-color: #FFF;">
                <form method="post" id="frm_pos_invoice_submitUpdate" class="form_pos_receipt_update">
                    <input type="hidden" name="isCreditSaleUpdate" id="isCreditSaleUpdate" value="0">
                    <input type="hidden" name="customerTelephoneUpdate" id="customerTelephoneUpdate">
                    <input type="hidden" name="customerNameUpdate" id="customerNameUpdate">
                    <input type="hidden" name="customerAddressUpdate" id="customerAddressUpdate">
                    <input type="hidden" name="customerCountry_oUpdate" id="customerCountry_oUpdate">
                    <input type="hidden" name="customerCountryCode_oUpdate" id="customerCountryCode_oUpdate">
                    <input type="hidden" name="customerCountryId_oUpdate" id="customerCountryId_oUpdate">
                    <input type="hidden" name="customerEmailUpdate" id="customerEmailUpdate">
                    <input type="hidden" name="customerIDUpdate" id="customerIDUpdate">
                    <input type="hidden" name="cardTotalAmountUpdate" id="cardTotalAmountUpdate" value="0"/>
                    <input type="hidden" name="netTotalAmountUpdate" id="netTotalAmountUpdate" value="0"/>
                    <input type="hidden" name="isDeliveryUpdate" id="isDeliveryUpdate" value="0"/>
                    <input type="hidden" name="isOnTimePaymentUpdate" id="frm_isOnTimePaymentUpdate" value=""/>
                    <input type="hidden" name="is_delivery_info_existUpdate" id="is_delivery_info_existUpdate"
                           value=""/>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">

                            <div class="row formRowPad" style="padding: 1px;">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-textLg">
                                    <?php echo $this->lang->line('posr_gross_total'); ?><!--Gross Total -->

                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                    <div id="final_payable_amtUpdate" class="ar payment-textLg"
                                         style="padding: 5px 0px;">0.00
                                    </div>
                                    <input type="hidden" name="total_payable_amtUpdate" id="total_payable_amtUpdate"
                                           value="0">
                                </div>
                            </div>


                            <!--promotion Row-->
                            <div class="row formRowPad" id="deliveryPersonContainer"> <!--promotionRow-->
                                <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3 payment-font"
                                     style="">
                                    <button class="pos2-btn-default p-disc-btn" disabled type="button"
                                            onclick="openPromotionModal()">
                                        <?php echo $this->lang->line('posr_promotion'); ?> </button>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                                    <div>
                                        <input type="text" id="tmp_promotionUpdate" readonly value=""
                                               class="form-control">
                                        <?php
                                        $deliveryPersonArray = get_specialCustomers(array(2, 3));
                                        ?>
                                        <select name="promotionIDUpdate" style="display: none;" id="promotionIDUpdate"
                                                class="form-control"
                                                onchange="calculateReturn(this)">
                                            <option value="">
                                                <?php echo $this->lang->line('common_non'); ?><!--None--></option>
                                            <?php if (!empty($deliveryPersonArray)) {
                                                foreach ($deliveryPersonArray as $val) {
                                                    ?>
                                                    <option value="<?php echo $val['customerID'] ?>"
                                                            data-cp="<?php echo $val['commissionPercentage'] ?>">
                                                        <?php echo $val['customerName'] . ' - ' . $val['commissionPercentage'] . ' %'; ?>

                                                    </option>
                                                    <?php
                                                }
                                            } ?>

                                        </select>

                                    </div>
                                </div>

                                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 payment-font">
                                    <div class="p-disc-container mrg-top5">
                                        <?php echo $this->lang->line('posr_discount'); ?><!--Promotional Discount-->
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
                                    <div class="mrg-top5">
                                        <input type="text" readonly name="promotional_discountUpdate"
                                               id="promotional_discountUpdate"
                                               class="form-control input-sm ar payment-inputTextMedium" value="0">
                                    </div>
                                </div>
                            </div>

                            <?php if ($isOutletTaxEnabled == "true") { ?>
                                <div class="row formRowPad" style="padding: 1px;">
                                    <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-textLg">
                                        Outlet Tax
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                        <div id="outlet_tax_in_invoiceUpdate" class="ar payment-textLg"
                                             style="padding: 5px 0px;">
                                            <?php echo $d == 3 ? '0.000' : '0.00'; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="row formRowPad" style="padding: 1px;" id="advancePaidDiv">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-textLg">
                                    Advance Paid
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                    <div id="delivery_advancePaymentAmountShowUpdate" class="ar payment-textLg"
                                         style="padding: 5px 0px;">
                                        <?php echo $d == 3 ? '0.000' : '0.00'; ?>
                                    </div>
                                    <input type="hidden" name="delivery_advancePaymentAmountUpdate"
                                           id="delivery_advancePaymentAmountUpdate" value="0">
                                </div>
                            </div>

                            <!-- Net Total -->
                            <div class="row formRowPad" style="padding: 1px;">
                                <div class="col-xs-6 col-sm-8 col-md-6 col-lg-6"
                                     style="font-weight: 800; font-size: 20px;"><b>
                                        <?php echo $this->lang->line('posr_net_total'); ?><!--Net Total --></b>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-6 col-lg-6">
                                    <div id="final_payableNet_amtUpdate" class="ar text-red"
                                         style="padding: 5px 0px; font-weight: 800; font-size: 20px;">0.00
                                    </div>
                                </div>
                            </div>

                            <table class="<?php echo table_class_pos() ?>">
                                <?php
                                $payments = get_paymentMethods_GLConfig();
                                foreach ($payments as $payment) {
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                    <!---->
                                                    <div style="padding:  4px;"
                                                         class="tbl-payment-font">
                                                        <input type="hidden"
                                                               name="customerAutoIDUpdate[<?php echo $payment['ID'] ?>]"
                                                               id="customerAutoIDUpdate<?php echo $payment['ID'] ?>">
                                                        <?php echo $payment['description']; ?>
                                                        <?php //echo $payment['autoID']; ?>
                                                    </div>
                                                </div>
                                                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                                                    <?php //print_r($payment)
                                                    /** GIFT CARD */
                                                    if ($payment['autoID'] == 5) {
                                                        ?>
                                                        <input type="text" value=""
                                                               class="form-control cardRef ar gitCardRefNo"
                                                               id="reference_Update<?php echo $payment['ID']; ?>"
                                                               name="referenceUpdate[<?php echo $payment['ID'] ?>]"
                                                               readonly
                                                               placeholder="Gift Card"/>
                                                        <?php
                                                        /** CREDIT SALES */
                                                    } else if ($payment['autoID'] == 7) {
                                                        ?>
                                                        <input type="text" value=""
                                                               class="form-control cardRef ar CreditSalesRefNo"
                                                               id="reference_Update<?php echo $payment['ID']; ?>"
                                                               name="referenceUpdate[<?php echo $payment['ID'] ?>]"
                                                               placeholder="Reference"/>
                                                        <?php
                                                        /** JAVA APP */
                                                    } else if ($payment['autoID'] == 25) {
                                                        ?>
                                                        <input type="text" value=""
                                                               class="form-control cardRef ar javaAppRefNo"
                                                               id="reference_Update<?php echo $payment['ID']; ?>"
                                                               name="referenceUpdate[<?php echo $payment['ID'] ?>]"
                                                               readonly
                                                               placeholder="App PIN"/>
                                                        <?php
                                                        /** OTHER */
                                                    } else {
                                                        if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                            if ($payment['autoID'] != 32) {
                                                                ?>
                                                                <input type="text" value=""
                                                                       class="form-control cardRef ar numpad"
                                                                       id="reference_Update<?php echo $payment['ID']; ?>"
                                                                       name="referenceUpdate[<?php echo $payment['ID'] ?>]"
                                                                       placeholder="Ref#"/>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td> <!--style="width:50px;"-->
                                            <?php
                                            if ($payment['autoID'] == 5) {
                                                /** Gift Card  */
                                                $onclick = ' onclick="openGiftCardRedeemModal()" ';
                                            } else if ($payment['autoID'] == 7) {
                                                /** Credit Sales */
                                                //$onclick = ' onclick="openCreditSalesModal(' . $payment['ID'] . ')" ';
                                                $onclick = ' onclick="checkPosAuthentication(11,' . $payment['ID'] . ')" ';
                                            } else if ($payment['autoID'] == 1) {
                                                /** Cash */
                                                $onclick = ' onclick="updateExactCard_update(1)" ';
                                            } else if ($payment['autoID'] == 3 || $payment['autoID'] == 4 || $payment['autoID'] == 6 || $payment['autoID'] == 1 || $payment['autoID'] == 27 || $payment['autoID'] == 28 || $payment['autoID'] == 29 || $payment['autoID'] == 30 || $payment['autoID'] == 31) {
                                                /** 3 Master Card | 4 Visa Card | 6 AMEX | 27 FriMi  | 28 Ali Pay*/
                                                $onclick = ' onclick="updateExactCard_update(' . $payment['ID'] . ')" ';
                                            } else if ($payment['autoID'] == 25) {
                                                /** java App */
                                                $onclick = ' onclick="openJavaAppModal(' . $payment['ID'] . ')" ';
                                            } else {
                                                $onclick = '';
                                            }
                                            ?>
                                            <?php
                                            if ($payment['autoID'] != 32) { ?>
                                                <button class="btn btn-default btn-block" <?php echo $onclick ?>
                                                        type="button"
                                                        style="padding: 0px;">
                                                    <img src="<?php echo base_url($payment['image']); ?>"
                                                         style="max-height: 27px;">
                                                </button>
                                            <?php } ?>

                                        </td>
                                        <td class="payment-tblSize">
                                            <?php
                                            if ($payment['autoID'] == 5) {
                                                /** GIFT CARD */
                                                ?>
                                                <input type="text" id="paymentType_Update<?php echo $payment['ID'] ?>"
                                                       style="cursor: hand"
                                                       name="paymentTypesUpdate[<?php echo $payment['ID'] ?>]"
                                                       onchange="calculatePaidAmountUpdate(this)"
                                                       onclick="openGiftCardRedeemModal()"
                                                       class="form-control al payment-inputTextMedium giftCardPayment paymentInputupdate <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOtherUpdate';
                                                       }
                                                       ?>" readonly
                                                       placeholder="0.00">
                                                <?php
                                            } else if ($payment['autoID'] == 7) {
                                                /** CREDIT SALE  */
                                                ?>
                                                <input type="text" id="paymentType_Update<?php echo $payment['ID'] ?>"
                                                       style="cursor: hand"
                                                       name="paymentTypesUpdate[<?php echo $payment['ID'] ?>]"
                                                       onchange="calculatePaidAmountUpdate(this)"
                                                       onclick="checkPosAuthentication(11,<?php echo $payment['ID'] ?>)"
                                                       class="form-control al payment-inputTextMedium creditSalesPayment paymentInputupdate <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOtherUpdate';
                                                       }
                                                       ?>" readonly
                                                       placeholder="0.00">
                                                <?php
                                            } else if ($payment['autoID'] == 25) {
                                                /** JAVA APP */
                                                ?>
                                                <input type="text" id="paymentType_Update<?php echo $tmpID; ?>"
                                                       style="cursor: hand"
                                                       name="paymentTypesUpdate[<?php echo $payment['ID'] ?>]"
                                                       onchange="calculatePaidAmountUpdate(this)"
                                                       onclick="openJavaAppModal(<?php echo $payment['ID'] ?>)"
                                                       class="form-control al payment-inputTextMedium javaAppPayment paymentInputupdate <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOtherUpdate';
                                                       }
                                                       ?>" readonly
                                                       placeholder="0.00">
                                                <?php
                                            } else if ($payment['autoID'] == 32) {
                                                $tmpID = $payment['autoID'] == 1 ? 1 : $payment['ID'];
                                                /** Round OFF */
                                                ?>
                                                <input type="text" id="paymentType_Update<?php echo $tmpID; ?>"
                                                       name="paymentTypesUpdate[<?php echo $payment['ID'] ?>]"
                                                       onchange="calculatePaidAmountUpdate(this)"
                                                       class="form-control al payment-inputTextMedium paymentInputupdate rundoff <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOtherUpdate';
                                                       }
                                                       ?> "
                                                       placeholder="0.00" readonly>
                                                <?php
                                            } else {
                                                $tmpID = $payment['autoID'] == 1 ? 1 : $payment['ID'];
                                                if ($payment["isAuthRequired"]) {
                                                    ?>
                                                    <input type="text" id="paymentType_Update<?php echo $tmpID; ?>"
                                                           name="paymentTypesUpdate[<?php echo $payment['ID'] ?>]"
                                                           onchange="checkPosAuthentication(10,this)"
                                                           class="form-control al payment-inputTextMedium paymentInputupdate numpad <?php
                                                           if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                               echo 'paymentOtherUpdate';
                                                           }
                                                           ?>"
                                                           placeholder="0.00">
                                                <?php } else {
                                                    ?>
                                                    <input type="text" id="paymentType_Update<?php echo $tmpID; ?>"
                                                           name="paymentTypesUpdate[<?php echo $payment['ID'] ?>]"
                                                           onchange="calculatePaidAmountUpdate(this)"
                                                           class="form-control al payment-inputTextMedium paymentInputupdate numpad <?php
                                                           if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                               echo 'paymentOtherUpdate';
                                                           }
                                                           ?>"
                                                           placeholder="0.00">

                                                    <?php
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>


                                    <?php
                                }
                                ?>
                                <tr>
                                    <td>
                                        <div style="padding:  8px; "
                                             class="tbl-payment-font">
                                            <?php echo $this->lang->line('posr_paid_amount'); ?><!--Paid Amount--></div>
                                    </td>
                                    <td>&nbsp;</td>
                                    <td class="payment-tblSize">
                                        <input readonly type="number"
                                               onkeyup="calculateReturnUpdate()" name="paidUpdate"
                                               id="paidUpdate"
                                               class="form-control payment-inputTextLg paymentTypeTextRed al"
                                               placeholder="0.00"
                                               autocomplete="off">
                                        <span id="paid_tempUpdate" class="hide"></span></td>
                                </tr>
                            </table>


                            <div class="row formRowPad hide">
                                <div class="col-md-4 lbl-delivery"></div>


                                <div class="col-md-8">
                                    <?php
                                    $paymentType = get_paymentMethods();
                                    if (!empty($paymentType) && false) {
                                        foreach ($paymentType as $key => $payType) {
                                            //id="paid_by"
                                            ?>
                                            <label class="radio-inline" for="<?php echo $key ?>">
                                                <input onclick="checkChequePayment(this.value)" <?php if ($key == 1) {
                                                    echo 'checked';
                                                } ?> type="radio" name="payment_methodUpdate"
                                                       id="Update_<?php echo $key ?>"
                                                       value="<?php echo $key ?>">
                                                <img title="<?php echo $payType ?>"
                                                     src="<?php echo base_url('images/payment_type') . '/' . $key . '.png' ?>"/> <?php// echo $key.$payType ?>
                                            </label>

                                            <?php
                                        }
                                    }
                                    ?>


                                    <?php
                                    $paymentType = get_paymentMethods();
                                    if (!empty($paymentType) && false) {
                                        echo '<div class="btn-group pull-right">';
                                        foreach ($paymentType as $key => $payType) {
                                            ?>
                                            <button id="payTypeBtnIDUpdate<?php echo $key ?>"
                                                    onclick="checkChequePayment(<?php echo $key ?>)" type="button"
                                                    class="btn payType <?php if ($key == 1) {
                                                        echo 'paymentTypeCustom';
                                                    } else {
                                                        echo 'btn-default';
                                                    } ?>">
                                                <img title="<?php echo $payType ?>"
                                                     src="<?php echo base_url('images/payment_type') . '/' . $key . '-32.png' ?>"/>
                                                <br/>
                                                <?php echo $payType ?>
                                            </button>

                                            <?php
                                        }
                                        echo '</div>';
                                    }
                                    ?>
                                    <input type="hidden" name="payment_methodUpdate" id="payment_methodUpdate"
                                           value="1">


                                </div>
                            </div>

                            <div class="row formRowPad" id="cheque_wrpUpdate"
                                 style="display: none;">
                                <div class="col-md-6">
                                    <b>
                                        <?php echo $this->lang->line('posr_cheque_number'); ?><!--Cheque Number-->
                                    </b>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="chequeUpdate" class="form-control" id="chequeUpdate"
                                           placeholder="<?php echo $this->lang->line('posr_cheque_number'); ?>"
                                           style="border: 1px solid #3a3a3a; color: #010101;"><!--Cheque Number-->
                                </div>
                            </div>

                            <div class="row formRowPad" id="cardRefNo_wrpUpdate"
                                 style=" display: none;">
                                <div class="col-md-6"><b>
                                        <?php echo $this->lang->line('posr_card_ref_no'); ?><!--Card Ref. No-->. </b>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="cardRefNoUpdate" class="form-control" id="cardRefNoUpdate"
                                           placeholder="<?php echo $this->lang->line('posr_card_ref_no'); ?>."
                                           style="border: 1px solid #3a3a3a; color: #010101;"><!--Card Ref. No-->
                                </div>
                            </div>


                            <div class="row formRowPad" id="card_wrpUpdate"
                                 style="display: none;">
                                <div class="col-md-6"><b>
                                        <?php echo $this->lang->line('posr_gift_card_number'); ?><!--Gift Card Number--> </b>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="gift_card_numbUpdate" class="form-control"
                                           id="card_numbUpdate"
                                           placeholder="<?php echo $this->lang->line('posr_gift_card_number'); ?>"

                                           style="border: 1px solid #3a3a3a; color: #010101;">
                                </div>
                            </div>


                            <div class="row formRowPad">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-textLg">
                                    <?php echo $this->lang->line('posr_eeturn_change'); ?><!--Return Change-->
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                    <div id="return_changeUpdate" class="ar"
                                         style=" padding:0px; font-size: 20px; font-weight: 700"></div>
                                    <input type="hidden" id="returned_changeUpdate" name="returned_changeUpdate"
                                           value="0">
                                </div>
                            </div>


                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 ">
                            <div
                                    style="padding: 10px 24px 10px 10px; border: 1px dashed grey;  border-radius: 14px;"
                                    class="hidden-xs">
                                <?php
                                $result = get_companyInfo();
                                $currencyID = $result['company_default_currencyID'];
                                $currencyCode = $result['company_default_currency'];
                                $currencies = getCurrencyNotes($currencyID);
                                ?>
                                <?php echo $this->lang->line('posr_currency_code'); ?><!--Currency Code-->:
                                <strong><?php echo $currencyCode ?></strong>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="btn-toolbar" role="toolbar">

                                            <input type="hidden" id="tmpQtyValueUpdate" value="0">
                                            <div class="row">


                                                <?php
                                                /*echo '<pre>';
                                                print_r($currencies);
                                                echo '</pre>';*/
                                                if (!empty($currencies)) {
                                                    foreach ($currencies as $currency) {
                                                        if ($currency['currencyCode'] == 'LKR') {
                                                            if ($currency['value'] > 90) {
                                                                echo ' <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"> ';
                                                                echo '<button type="button" onclick="updateNoteValueUpdate(this)" class="currencyNoteBtn pos2-btn-default">' . $currency['value'] . '</button>';
                                                                echo '</div>';
                                                            }
                                                        } else {
                                                            echo ' <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"> ';
                                                            echo '<button type="button" onclick="updateNoteValueUpdate(this)" class="currencyNoteBtn pos2-btn-default">' . $currency['value'] . '</button>';
                                                            echo '</div>';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                    <button class="currencyNoteBtn pos2-btn-default"
                                                            type="button" onclick="updateExactCashUpdate();">
                                                        <?php echo $this->lang->line('posr_exact_cash'); ?><!--Exact Cash-->
                                                    </button>
                                                </div>
                                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                                    <button type="button" onclick="updatePaidAmountUpdate(this);"
                                                            class="currencyNoteBtn pos2-btn-default">C
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="container-fluid" style="padding: 10px;">
                                <button class="btn btn-lg btn-default btn-strong btn-xl" type="button"
                                        onclick="openCustomerModal()"><i
                                            class="fa fa-users"></i> Customer
                                </button>
                            </div>
                            <div class="container-fluid" style="padding: 10px;" id="deliveryCommissionDivUpdate">
                                <div class="row formRowPad deliveryRow" style="display: none;"
                                     id="deliveryPersonContainerUpdate">
                                    <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6 lbl-delivery">
                                        <?php echo $this->lang->line('posr_delivery_person'); ?><!--Delivery Person-->
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6">
                                        <div>
                                            <?php
                                            $deliveryPersonArray = get_specialCustomers();
                                            ?>
                                            <select name="deliveryPersonIDUpdate" id="deliveryPersonIDUpdate"
                                                    class="form-control"
                                                    onchange="calculateReturn(this)">
                                                <option value="" selected>Select Delivery Person</option>
                                                <!--<option value="-1" data-cp="0" data-otp="1">Normal Delivery -
                                                    0%
                                                </option>-->

                                                <?php echo $this->lang->line('common_please_select'); ?><!--Please select--></option>
                                                <?php if (!empty($deliveryPersonArray)) {
                                                    foreach ($deliveryPersonArray as $val) {
                                                        ?>
                                                        <option value="<?php echo $val['customerID'] ?>"
                                                                data-cp="<?php echo $val['commissionPercentage'] ?>"
                                                                data-otp="<?php echo $val['isOnTimePayment'] ?>">
                                                            <?php echo $val['customerName'] ?>
                                                            - <?php echo $val['commissionPercentage'] ?>%
                                                        </option>
                                                        <?php
                                                    }
                                                } ?>

                                            </select>

                                        </div>
                                    </div>
                                </div>

                                <div class="row formRowPad deliveryPromotionRow" style="display: none;">
                                    <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 lbl-delivery">
                                        <?php echo $this->lang->line('posr_amount'); ?><!--Net Total Payable Amount-->
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                        <input type="text" class="form-control payment-inputTextRedLg"
                                               readonly="readonly"
                                               style="font-weight: 800;"
                                               id="totalPayableAmountDelivery_idUpdate"
                                               name="totalPayableAmountDelivery_idUpdate"
                                               value="0">
                                    </div>
                                </div>

                                <div class="row formRowPad deliveryPromotionRow" style="display: none;">
                                    <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 lbl-delivery">
                                        <?php echo $this->lang->line('posr_eeturn_change'); ?><!--Net Return Change-->
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                        <input type="text" class="form-control payment-inputTextRedLg"
                                               readonly="readonly"
                                               style="font-weight: 800;"
                                               id="returned_change_toDeliveryUpdate"
                                               name="returned_change_toDeliveryUpdate"
                                               value="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </form>
            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default" onclick="close_update_pos_submitted()"
                        style="height: 57px;">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>

                <button id="" type="button" onclick="update_pos_submitted_payments()" class="btn btn-lg btn-primary"
                        style="height: 57px;">
                    <span id="">Update</span>
                </button>

            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="pos_payments_modal" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg"
         style="width: <?php //echo !isset($modal_width) && !empty($modal_width) ? $modal_width . '%' : '70%'; ?>">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h3 class="modal-title"><?php echo $this->lang->line('common_payment'); ?><!--Payment--></h3>
            </div>
            <div class="modal-body" style="overflow: visible; background-color: #FFF;">
                <form method="post" id="frm_pos_invoice_submit" class="form_pos_receipt">
                    <input type="hidden" name="isCreditSale" id="isCreditSale" value="0">
                    <input type="hidden" name="customerTelephone" id="customerTelephone">
                    <input type="hidden" name="customerName" id="customerName">
                    <input type="hidden" name="cardTotalAmount" id="cardTotalAmount" value="0"/>
                    <input type="hidden" name="netTotalAmount" id="netTotalAmount" value="0"/>
                    <input type="hidden" name="isDelivery" id="isDelivery" value="0"/>
                    <input type="hidden" name="isOnTimePayment" id="frm_isOnTimePayment" value=""/>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">

                            <div class="row formRowPad" style="padding: 1px;">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-textLg">
                                    <?php echo $this->lang->line('posr_gross_total'); ?><!--Gross Total -->

                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                    <div id="final_payable_amt" class="ar payment-textLg"
                                         style="padding: 5px 0px;">0
                                    </div>
                                    <input type="hidden" name="total_payable_amt" id="total_payable_amt" value="0">
                                </div>
                            </div>


                            <!--promotion Row-->
                            <div class="row formRowPad" id="deliveryPersonContainer"> <!--promotionRow-->
                                <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3 payment-font"
                                     style="">
                                    <button class="pos2-btn-default p-disc-btn" type="button"
                                            onclick="openPromotionModal()">
                                        <?php echo $this->lang->line('posr_promotion'); ?> </button>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                                    <div>
                                        <input type="text" id="tmp_promotion" readonly value="" class="form-control">
                                        <?php
                                        $deliveryPersonArray = get_specialCustomers(array(2, 3));

                                        ?>
                                        <select name="promotionID" style="display: none;" id="promotionID"
                                                class="form-control"
                                                onchange="calculateReturn(this)">
                                            <option value="">
                                                <?php echo $this->lang->line('common_non'); ?><!--None--></option>
                                            <?php if (!empty($deliveryPersonArray)) {
                                                foreach ($deliveryPersonArray as $val) {
                                                    ?>
                                                    <option value="<?php echo $val['customerID'] ?>"
                                                            data-cp="<?php echo $val['commissionPercentage'] ?>">
                                                        <?php echo $val['customerName'] . ' - ' . $val['commissionPercentage'] . ' %'; ?>

                                                    </option>
                                                    <?php
                                                }
                                            } ?>

                                        </select>

                                    </div>
                                </div>

                                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 payment-font">
                                    <div class="p-disc-container mrg-top5">
                                        <?php echo $this->lang->line('posr_discount'); ?><!--Promotional Discount-->
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
                                    <div class="mrg-top5">
                                        <input type="text" readonly name="promotional_discount"
                                               id="promotional_discount"
                                               class="form-control input-sm ar payment-inputTextMedium" value="0">
                                    </div>
                                </div>
                            </div>

                            <?php if ($isOutletTaxEnabled == "true") { ?>
                                <div class="row formRowPad" style="padding: 1px;">
                                    <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-textLg">
                                        Outlet Tax
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                        <div id="outlet_tax_in_invoice" class="ar payment-textLg"
                                             style="padding: 5px 0px;">
                                            <?php echo $d == 3 ? '0.000' : '0.00'; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="row formRowPad" style="padding: 1px;" id="advancePaidDiv">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-textLg">
                                    Advance Paid
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                    <div id="delivery_advancePaymentAmountShow" class="ar payment-textLg"
                                         style="padding: 5px 0px;">
                                        <?php echo $d == 3 ? '0.000' : '0.00'; ?>
                                    </div>
                                    <input type="hidden" name="delivery_advancePaymentAmount"
                                           id="delivery_advancePaymentAmount" value="0">
                                </div>
                            </div>

                            <!-- Net Total -->
                            <div class="row formRowPad" style="padding: 1px;">
                                <div class="col-xs-6 col-sm-8 col-md-6 col-lg-6"
                                     style="font-weight: 800; font-size: 20px;"><b>
                                        <?php echo $this->lang->line('posr_net_total'); ?><!--Net Total --></b>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-6 col-lg-6">
                                    <div id="final_payableNet_amt" class="ar text-red"
                                         style="padding: 5px 0px; font-weight: 800; font-size: 20px;">0.00
                                    </div>
                                </div>
                            </div>

                            <table class="<?php echo table_class_pos() ?>">
                                <?php
                                $payments = get_paymentMethods_GLConfig();


                                foreach ($payments as $payment) {

                                    ?>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                    <!---->
                                                    <div style="padding:  4px;"
                                                         class="tbl-payment-font">
                                                        <input type="hidden"
                                                               name="customerAutoID[<?php echo $payment['ID'] ?>]"
                                                               id="customerAutoID<?php echo $payment['ID'] ?>">
                                                        <?php echo $payment['description']; ?>
                                                        <?php //echo $payment['autoID']; ?>
                                                    </div>
                                                </div>
                                                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                                                    <?php //print_r($payment)
                                                    /** GIFT CARD */
                                                    if ($payment['autoID'] == 5) {
                                                        ?>
                                                        <input type="text" value=""
                                                               class="form-control cardRef ar gitCardRefNo"
                                                               id="reference_<?php echo $payment['ID']; ?>"
                                                               name="reference[<?php echo $payment['ID'] ?>]" readonly
                                                               placeholder="Gift Card"/>
                                                        <?php
                                                        /** CREDIT SALES */
                                                    } else if ($payment['autoID'] == 7) {

                                                        ?>
                                                        <input type="text" value=""
                                                               class="form-control cardRef ar CreditSalesRefNo touchEngKeyboard"
                                                               id="reference_<?php echo $payment['ID']; ?>"
                                                               name="reference[<?php echo $payment['ID'] ?>]"
                                                               placeholder="Reference"/>
                                                        <?php
                                                        /** JAVA APP */
                                                    } else if ($payment['autoID'] == 25) {

                                                        ?>
                                                        <input type="text" value=""
                                                               class="form-control cardRef ar javaAppRefNo"
                                                               id="reference_<?php echo $payment['ID']; ?>"
                                                               name="reference[<?php echo $payment['ID'] ?>]" readonly
                                                               placeholder="App PIN"/>
                                                        <?php
                                                        /** OTHER */
                                                    } else {
                                                        if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                            ?>
                                                            <input type="text" value=""
                                                                   class="form-control cardRef ar numpad"
                                                                   id="reference_<?php echo $payment['ID']; ?>"
                                                                   name="reference[<?php echo $payment['ID'] ?>]"
                                                                   placeholder="Ref#"/>
                                                            <?php
                                                        }
                                                    }

                                                    ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td> <!--style="width:50px;"-->
                                            <?php

                                            if ($payment['autoID'] == 5) {
                                                /** Gift Card  */
                                                $onclick = ' onclick="openGiftCardRedeemModal()" ';
                                            } else if ($payment['autoID'] == 7) {
                                                /** Credit Sales */
                                                //$onclick = ' onclick="openCreditSalesModal(' . $payment['ID'] . ')" ';
                                                $onclick = ' onclick="checkPosAuthentication(11,' . $payment['ID'] . ')" ';
                                            } else if ($payment['autoID'] == 1) {
                                                /** Cash */
                                                $onclick = ' onclick="updateExactCard(1)" ';
                                            } else if ($payment['autoID'] == 3 || $payment['autoID'] == 4 || $payment['autoID'] == 6 || $payment['autoID'] == 1 || $payment['autoID'] == 27) {
                                                /** 3 Master Card | 4 Visa Card | 6 AMEX | 27 FriMi */
                                                $onclick = ' onclick="updateExactCard(' . $payment['ID'] . ')" ';
                                            } else if ($payment['autoID'] == 25) {
                                                /** java App */
                                                $onclick = ' onclick="openJavaAppModal(' . $payment['ID'] . ')" ';
                                            } else {
                                                $onclick = '';
                                            }
                                            ?>
                                            <button class="btn btn-default btn-block" <?php echo $onclick ?>
                                                    type="button"
                                                    style="padding: 0px;">
                                                <img src="<?php echo base_url($payment['image']); ?>"
                                                     style="max-height: 27px;">
                                            </button>

                                        </td>
                                        <td class="payment-tblSize">
                                            <?php
                                            if ($payment['autoID'] == 5) {
                                                /** GIFT CARD */
                                                ?>
                                                <input type="text" id="paymentType_<?php echo $payment['ID'] ?>"
                                                       style="cursor: hand"
                                                       name="paymentTypes[<?php echo $payment['ID'] ?>]"
                                                       onchange="calculatePaidAmount(this)"
                                                       onclick="openGiftCardRedeemModal()"
                                                       class="form-control al payment-inputTextMedium giftCardPayment paymentInput <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOther';
                                                       }
                                                       ?>" readonly
                                                       placeholder="<?php echo number_format(0, $d) ?>">
                                                <?php

                                            } else if ($payment['autoID'] == 7) {
                                                /** CREDIT SALE  */
                                                ?>
                                                <input type="text" id="paymentType_<?php echo $payment['ID'] ?>"
                                                       style="cursor: hand"
                                                       name="paymentTypes[<?php echo $payment['ID'] ?>]"
                                                       onchange="calculatePaidAmount(this)"
                                                       onclick="checkPosAuthentication(11,<?php echo $payment['ID'] ?>)"
                                                       class="form-control al payment-inputTextMedium creditSalesPayment paymentInput <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOther';
                                                       }
                                                       ?>" readonly
                                                       placeholder="<?php echo number_format(0, $d) ?>">
                                                <?php

                                            } else if ($payment['autoID'] == 25) {
                                                /** JAVA APP */
                                                ?>
                                                <input type="text" id="paymentType_<?php echo $tmpID; ?>"
                                                       style="cursor: hand"
                                                       name="paymentTypes[<?php echo $payment['ID'] ?>]"
                                                       onchange="calculatePaidAmount(this)"
                                                       onclick="openJavaAppModal(<?php echo $payment['ID'] ?>)"
                                                       class="form-control al payment-inputTextMedium javaAppPayment paymentInput <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOther';
                                                       }
                                                       ?>" readonly
                                                       placeholder="<?php echo number_format(0, $d) ?>">
                                                <?php

                                            } else {
                                                $tmpID = $payment['autoID'] == 1 ? 1 : $payment['ID'];
                                                if ($payment["isAuthRequired"]) {
                                                    ?>
                                                    <input type="text" id="paymentType_<?php echo $tmpID; ?>"
                                                           name="paymentTypes[<?php echo $payment['ID'] ?>]"
                                                           onchange="checkPosAuthentication(10,this)"
                                                           class="form-control al payment-inputTextMedium paymentInput numpad <?php
                                                           if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                               echo 'paymentOther';
                                                           }
                                                           ?>"
                                                           placeholder="<?php echo number_format(0, $d) ?>">
                                                <?php } else {
                                                    ?>
                                                    <input type="text" id="paymentType_<?php echo $tmpID; ?>"
                                                           name="paymentTypes[<?php echo $payment['ID'] ?>]"
                                                           onchange="calculatePaidAmount(this)"
                                                           class="form-control al payment-inputTextMedium paymentInput numpad <?php
                                                           if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                               echo 'paymentOther';
                                                           }
                                                           ?>"
                                                           placeholder="<?php echo number_format(0, $d) ?>">

                                                    <?php
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>


                                    <?php
                                }
                                ?>
                                <tr>
                                    <td>
                                        <div style="padding:  8px; "
                                             class="tbl-payment-font">
                                            <?php echo $this->lang->line('posr_paid_amount'); ?><!--Paid Amount--></div>
                                    </td>
                                    <td>&nbsp;</td>
                                    <td class="payment-tblSize"><input readonly type="number"
                                                                       onkeyup="calculateReturn()" name="paid"
                                                                       id="paid"
                                                                       class="form-control payment-inputTextLg paymentTypeTextRed al"
                                                                       placeholder="<?php echo number_format(0, $d) ?>"
                                                                       autocomplete="off">
                                        <span id="paid_temp" class="hide"></span></td>
                                </tr>
                            </table>

                            <!--<div class="row formRowPad">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-textLg">
                                    <?php /*echo $this->lang->line('posr_paid_amount'); */ ?>  </div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">

                                </div>
                            </div>-->


                            <div class="row formRowPad hide">
                                <div class="col-md-4 lbl-delivery">
                                    <?php //echo $this->lang->line('posr_paid_by'); ?><!--Paid By-->
                                </div>


                                <div class="col-md-8">
                                    <?php
                                    $paymentType = get_paymentMethods();
                                    if (!empty($paymentType) && false) {
                                        foreach ($paymentType as $key => $payType) {
                                            //id="paid_by"
                                            ?>
                                            <label class="radio-inline" for="<?php echo $key ?>">
                                                <input onclick="checkChequePayment(this.value)" <?php if ($key == 1) {
                                                    echo 'checked';
                                                } ?> type="radio" name="payment_method" id="<?php echo $key ?>"
                                                       value="<?php echo $key ?>">
                                                <img title="<?php echo $payType ?>"
                                                     src="<?php echo base_url('images/payment_type') . '/' . $key . '.png' ?>"/> <?php// echo $key.$payType ?>
                                            </label>

                                            <?php
                                        }
                                    }
                                    ?>

                                    <?php //echo form_dropdown('payment_method', get_paymentMethods_drop(), '1', 'id="paid_by" onchange="checkChequePayment(this.value)" class="form-control input-sm" style="margin:5px;"');
                                    ?>
                                    <?php
                                    $paymentType = get_paymentMethods();
                                    if (!empty($paymentType) && false) {
                                        echo '<div class="btn-group pull-right">';
                                        foreach ($paymentType as $key => $payType) {
                                            //id="paid_by"
                                            ?>
                                            <button id="payTypeBtnID<?php echo $key ?>"
                                                    onclick="checkChequePayment(<?php echo $key ?>)" type="button"
                                                    class="btn payType <?php if ($key == 1) {
                                                        echo 'paymentTypeCustom';
                                                    } else {
                                                        echo 'btn-default';
                                                    } ?>">
                                                <img title="<?php echo $payType ?>"
                                                     src="<?php echo base_url('images/payment_type') . '/' . $key . '-32.png' ?>"/>
                                                <br/>
                                                <?php echo $payType ?>
                                            </button>

                                            <?php
                                        }
                                        echo '</div>';
                                    }
                                    ?>
                                    <input type="hidden" name="payment_method" id="payment_method" value="1">


                                </div>
                            </div>

                            <div class="row formRowPad" id="cheque_wrp"
                                 style="display: none;">
                                <div class="col-md-6">
                                    <b>
                                        <?php echo $this->lang->line('posr_cheque_number'); ?><!--Cheque Number-->
                                    </b>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="cheque" class="form-control" id="cheque"
                                           placeholder="<?php echo $this->lang->line('posr_cheque_number'); ?>"
                                           style="border: 1px solid #3a3a3a; color: #010101;"><!--Cheque Number-->
                                </div>
                            </div>

                            <div class="row formRowPad" id="cardRefNo_wrp"
                                 style=" display: none;">
                                <div class="col-md-6"><b>
                                        <?php echo $this->lang->line('posr_card_ref_no'); ?><!--Card Ref. No-->. </b>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="cardRefNo" class="form-control" id="cardRefNo"
                                           placeholder="<?php echo $this->lang->line('posr_card_ref_no'); ?>."
                                           style="border: 1px solid #3a3a3a; color: #010101;"><!--Card Ref. No-->
                                </div>
                            </div>


                            <div class="row formRowPad" id="card_wrp"
                                 style="display: none;">
                                <div class="col-md-6"><b>
                                        <?php echo $this->lang->line('posr_gift_card_number'); ?><!--Gift Card Number--> </b>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="gift_card_numb" class="form-control" id="card_numb"
                                           placeholder="<?php echo $this->lang->line('posr_gift_card_number'); ?>"
                                    <!--Gift Card Number-->
                                    style="border: 1px solid #3a3a3a; color: #010101;">
                                </div>
                            </div>


                            <div class="row formRowPad">
                                <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 payment-textLg">
                                    <?php echo $this->lang->line('posr_eeturn_change'); ?><!--Return Change-->
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                    <div id="return_change" class="ar"
                                         style=" padding:0px; font-size: 20px; font-weight: 700"></div>
                                    <input type="hidden" id="returned_change" name="returned_change" value="0">
                                </div>
                            </div>


                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 ">
                            <div
                                    style="padding: 10px 24px 10px 10px; border: 1px dashed grey;  border-radius: 14px;"
                                    class="hidden-xs">
                                <?php
                                $result = get_companyInfo();
                                $currencyID = $result['company_default_currencyID'];
                                $currencyCode = $result['company_default_currency'];

                                $currencies = getCurrencyNotes($currencyID);
                                ?>
                                <?php echo $this->lang->line('posr_currency_code'); ?><!--Currency Code-->:
                                <strong><?php echo $currencyCode ?></strong>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="btn-toolbar" role="toolbar">

                                            <input type="hidden" id="tmpQtyValue" value="0">
                                            <div class="row">


                                                <?php
                                                /*echo '<pre>';
                                                print_r($currencies);
                                                echo '</pre>';*/

                                                if (!empty($currencies)) {
                                                    foreach ($currencies as $currency) {

                                                        if ($currency['currencyCode'] == 'LKR') {
                                                            if ($currency['value'] > 90) {
                                                                echo ' <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"> ';
                                                                echo '<button type="button" onclick="updateNoteValue(this)" class="currencyNoteBtn pos2-btn-default">' . $currency['value'] . '</button>';
                                                                echo '</div>';
                                                            }
                                                        } else {
                                                            echo ' <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"> ';
                                                            echo '<button type="button" onclick="updateNoteValue(this)" class="currencyNoteBtn pos2-btn-default">' . $currency['value'] . '</button>';
                                                            echo '</div>';
                                                        }

                                                    }
                                                }
                                                ?>
                                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                    <button class="currencyNoteBtn pos2-btn-default"
                                                            type="button" onclick="updateExactCash();">
                                                        <?php echo $this->lang->line('posr_exact_cash'); ?><!--Exact Cash-->
                                                    </button>
                                                </div>
                                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                                    <button type="button" onclick="updatePaidAmount(this);"
                                                            class="currencyNoteBtn pos2-btn-default">C
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="container-fluid" style="padding: 10px;">
                                <button class="btn btn-lg btn-default btn-strong btn-xl" type="button"
                                        onclick="openCustomerModal()"><i
                                            class="fa fa-users"></i> Customer
                                </button>
                            </div>
                            <div class="container-fluid" style="padding: 10px;" id="deliveryCommissionDiv">
                                <div class="row formRowPad deliveryRow" style="display: none;"
                                     id="deliveryPersonContainer">
                                    <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6 lbl-delivery">
                                        <?php echo $this->lang->line('posr_delivery_person'); ?><!--Delivery Person-->
                                    </div>
                                    <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6">
                                        <div>
                                            <?php
                                            $deliveryPersonArray = get_specialCustomers();
                                            ?>
                                            <select name="deliveryPersonID" id="deliveryPersonID" class="form-control"
                                                    onchange="calculateReturn(this)">
                                                <option value=""></option>
                                                <option value="-1" data-cp="0" data-otp="1" selected>Normal Delivery -
                                                    0%
                                                </option>

                                                <?php echo $this->lang->line('common_please_select'); ?><!--Please select--></option>
                                                <?php if (!empty($deliveryPersonArray)) {
                                                    foreach ($deliveryPersonArray as $val) {
                                                        ?>
                                                        <option value="<?php echo $val['customerID'] ?>"
                                                                data-cp="<?php echo $val['commissionPercentage'] ?>"
                                                                data-otp="<?php echo $val['isOnTimePayment'] ?>">
                                                            <?php echo $val['customerName'] ?>
                                                            - <?php echo $val['commissionPercentage'] ?>%
                                                        </option>
                                                        <?php
                                                    }
                                                } ?>

                                            </select>
                                            <?php //echo form_dropdown('deliveryPersonID', get_specialCustomers_drop(), '', 'class="form-control" id="deliveryPersonID"'); ?>

                                        </div>
                                    </div>
                                </div>

                                <div class="row formRowPad deliveryPromotionRow" style="display: none;">
                                    <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 lbl-delivery">
                                        <?php echo $this->lang->line('posr_amount'); ?><!--Net Total Payable Amount-->
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                        <!--<span id="totalPayableAmountDelivery"
                                              style="background-color: #FFFF99; padding: 10px 10px;"></span>-->
                                        <input type="text" class="form-control payment-inputTextRedLg"
                                               readonly="readonly"
                                               style="font-weight: 800;"
                                               id="totalPayableAmountDelivery_id" name="totalPayableAmountDelivery_id"
                                               value="0">
                                    </div>
                                </div>

                                <div class="row formRowPad deliveryPromotionRow" style="display: none;">
                                    <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 lbl-delivery">
                                        <?php echo $this->lang->line('posr_eeturn_change'); ?><!--Net Return Change-->
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6">
                                        <!--<span id="return_change_toDelivery"
                                              style="background-color: #FFFF99; padding: 10px 10px;"></span>-->
                                        <input type="text" class="form-control payment-inputTextRedLg"
                                               readonly="readonly"
                                               style="font-weight: 800;"
                                               id="returned_change_toDelivery" name="returned_change_toDelivery"
                                               value="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </form>
            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal" style="height: 57px;">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>

                <?php
                if (isset($sampleBillPolicy) && $sampleBillPolicy) {
                    ?>
                    <button type="button" onclick="print_sample_bill()" class="btn btn-lg btn-default"
                            style="height: 57px;">
                        <i class="fa fa-print"></i>Print Sample
                    </button>
                    <?php
                }
                ?>
                <button id="submit_and_close_btn" type="button" onclick="submit_and_close_pos_payments()"
                        class="btn btn-lg btn-default" style="height: 57px;">
                    <span><?php echo $this->lang->line('common_submit_and_close'); ?><!--Submit--></span>
                </button>

                <button id="submit_btn" type="submit" onclick="submit_pos_payments()" class="btn btn-lg btn-primary"
                        style="background-color: #3fb618; color: #FFF; border: 0px; float: right; display: none;height: 57px;">
                    <span id="submit_btn_pos_receipt"><?php echo $this->lang->line('common_submit_and_print'); ?><!--Submit--></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!--EMAIL MODAL -->
<div aria-hidden="true" role="dialog" tabindex="1" style="z-index: 9999;" id="email_modal" class="modal fade"
     data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-responsive-bill">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('posr_enter_email'); ?><!--Enter Email--> </h4>
            </div>
            <div class="modal-body" id="" style="">
                <form method="post" id="frm_print_email_address">
                    <input type="hidden" name="invoiceID" id="email_invoiceID" value="0">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="email" class="form-control" id="emailAddress" name="emailAddress">
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer" style="margin-top: 0px; padding: 7px;">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>

                <button type="button" onclick="send_pos_email()" class="btn btn-sm btn-primary"
                        style="background-color: #3fb618; color: #FFF; border: 0px; float: right;">
                    <span id=""><?php echo $this->lang->line('common_submit'); ?><!--Submit--></span>
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    var globalVar = '';
    var defaultDineinButtonID = 0;

    function updateNoteValueUpdate(tmpValue) {
        var noteValue = $(tmpValue).text();
        $("#paid_tempUpdate").html(noteValue);
        $("#paidUpdate").val(parseFloat(noteValue));
        $("#paymentType_Update1").val(parseFloat(noteValue));
        calculateReturnUpdate();
        calculatePaidAmountUpdate();
    }

    function updateNoteValue(tmpValue) {

        var noteValue = $(tmpValue).text();

        $("#paid_temp").html(noteValue);
        $("#paid").val(parseFloat(noteValue));
        $("#paymentType_1").val(parseFloat(noteValue));
        /*$("#paid").focus();*/
        calculateReturn();
        calculatePaidAmount();

    }

    function updatePaidAmountUpdate(tmpValue) {
        var cPaidAmount = $("#paidUpdate").val();
        var tmpAmount = $(tmpValue).text();
        var tmpAmount_txt = $("#paid_tempUpdate").text();
        if (parseFloat(tmpAmount) >= 0 || $.trim(tmpAmount) == '.') {
            var updateVal = cPaidAmount + tmpAmount;
            var tmpAmount_output = $.trim(tmpAmount_txt) + $.trim(tmpAmount);
            if ($.trim(tmpAmount) == '.') {
            }

            $("#paid_tempUpdate").html(tmpAmount_output);
            //$("#paid").val(parseFloat(updateVal));
            $("#paidUpdate").val(parseFloat(tmpAmount_output));

        } else if ($.trim(tmpAmount) == 'C') {
            $("#isCreditSaleUpdate").val(0);
            $("#paidUpdate").val(0);
            $("#paid_tempUpdate").html(0);
            $(".paymentInputupdate").val(0);
            $('.cardRef').val('');
        }
        calculateReturnUpdate();
    }

    function updatePaidAmount(tmpValue) {
        var cPaidAmount = $("#paid").val();
        var tmpAmount = $(tmpValue).text();
        var tmpAmount_txt = $("#paid_temp").text();
        if (parseFloat(tmpAmount) >= 0 || $.trim(tmpAmount) == '.') {
            var updateVal = cPaidAmount + tmpAmount;
            var tmpAmount_output = $.trim(tmpAmount_txt) + $.trim(tmpAmount);
            if ($.trim(tmpAmount) == '.') {
                //  updateVal = cPaidAmount + tmpAmount + '0';
            }
            console.log(updateVal);

            $("#paid_temp").html(tmpAmount_output);
            //$("#paid").val(parseFloat(updateVal));
            $("#paid").val(parseFloat(tmpAmount_output));

        } else if ($.trim(tmpAmount) == 'C') {
            $("#isCreditSale").val(0);
            $("#paid").val(0);
            $("#paid_temp").html(0);
            $(".paymentInput").val(0);
            $('.cardRef').val('');
        }
        calculateReturn();
        //$("#paid").focus();
    }

    function updateExactCashUpdate() {
        $(".paymentInputUpdate").val('');
        var totalAmount = $("#final_payableNet_amtUpdate").text();
        $("#paidUpdate").val(parseFloat(totalAmount));
        $("#paymentType_Update1").val(parseFloat(totalAmount).toFixed(<?php echo $d?>));
        calculateReturnUpdate();
    }

    function updateExactCash() {
        var totalAmount = $("#final_payableNet_amt").text();
        $("#paid").val(parseFloat(totalAmount));
        $("#paymentType_1").val(parseFloat(totalAmount).toFixed(<?php echo $d?>));
        calculateReturn();
    }

    function resetPaymentForm() {
        //$("#paid_by").select2("val", "1");
        $("#cardRefNo").val("");
        $("#paid").val("");
        $("#customerName").val("");
        $("#deliveryPersonID").val("");
        $("#customerTelephone").val("");
        $("#paid_temp").html('');
        $("#isDelivery").html(0);
        $("#frm_isOnTimePayment").html('');
        $("#netTotalAmount").html(0);
        $("#cardTotalAmount").html(0);
        $(".cardRef").val('');
        $("#delivery_advancePaymentAmount").val(0);
        $("#delivery_advancePaymentAmountShow").html((0).toFixed(<?php echo $d?>));
        $("#current_table_description").text('Table');
        resetKotButton();
        reset_paymentMode();
        updateCustomerTypeBtn(defaultDineinButtonID);
    }

    function open_pos_submitted_payments_modal() {
        <?php
        if (isset($template) && $template == 'general') {
            echo ' $("#customerType").val(1); ';
        }
        ?>
        /*handling exception*/
        // handleItemMisMatchException();
        var gross_total = parseFloat($("#gross_total").html());
        var customerType = $("#customerType").val();
        if (customerType > 0) {
            $("#pos_submitted_payments_modal").modal('show');
            //$("#paid_by").select2("val", "");
            setTimeout(function () {
                calculateReturn();
                $("#paid").focus();
            }, 500);
        } else {
            $("#order_mode_modal").modal("show");
        }

    }

    function open_pos_payments_modal() {

        <?php
        if (isset($template) && $template == 'general') {
            echo ' $("#customerType").val(1); ';
        }
        ?>
        var gross_total = parseFloat($("#gross_total").html());
        var customerType = $("#customerType").val();

        if (gross_total > 0) {
            if (customerType > 0) {
                $("#pos_payments_modal").modal('show');
                //$("#paid_by").select2("val", "");

                setTimeout(function () {
                    calculateReturn();
                    $("#paid").focus();
                }, 500);
            } else {
                $("#order_mode_modal").modal("show");
            }

        } else {
            bootbox.alert('<div class="alert alert-info"><strong><?php echo $this->lang->line('posr_no_menus_added_to_invoice_please_add_at_least_one_item');?>.</strong></div>');
            <!--No menus added to Invoice, please add at least one item-->
        }
    }

    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var $state = $(
            '<span><img src="<?php echo base_url()?>images/payment_type/' + state.element.value.toLowerCase() + '.png" class="img-flag" />  ' + state.text + '</span>'
        );
        return $state;
    }

    function deliveryValidation() {
        var isDelivery = $("#isDelivery").val();
        var deliveryPersonID = $("#deliveryPersonID").val();
        if ((isDelivery == 1 && (deliveryPersonID == 0 || deliveryPersonID == '' || deliveryPersonID == null))) {
            myAlert('e', 'Please select delivery person before add payments!');
            return false;
        } else {
            return true;
        }
    }

    $(document).ready(function (e) {
        $(".paymentInput").change(function (e) {
            var validation = deliveryValidation();
            if (validation) {
                setTimeout(function () {
                    calculateDelivery();
                }, 100);
            }
        });

        $('#pos_payments_modal').on('hidden.bs.modal', function (e) {
            if ($("#holdInvoiceID_input").val() == 0) {
                resetPaymentForm()
            }
        });
        $('#pos_payments_modal').on('shown.bs.modal', function (e) {
            setTimeout(function () {
            }, 100);
        });


        $("#paid").keyup(function (e) {
            if (e.keyCode == 13) {
                var tmpVisible = $("#submit_btn").is(":visible");
                if (tmpVisible) {
                    submit_pos_payments();
                }
            }
        })
    });

    function clearCustomerTypeButtons() {
        $(".customerType").removeClass('btn-primary');
        $(".customerType").addClass('btn-default');
    }

    function selectCustomerButton(id) {
        //$("#customerType").val(id);
        $(".customerType").removeClass('btn-primary');
        $(".customerType").addClass('btn-default');
        $("#customerTypeID_" + id).removeClass('btn-default');
        $("#customerTypeID_" + id).addClass('btn-primary');
    }


    function updateCustomerTypeBtn(id, isDelivery, isDineIn, ordermd = 0) {
        $("#order_mode_modal").modal("hide");
        $("#is_dine_in").val(isDineIn);
        $("#customerType").val(id);
        var customerType = id;
        var deliveryType = $("#customerTypeID_" + id).html();

        var tmpDeliveryTxt = $('#customerTypeBtnString').val(deliveryType.trim())

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_restaurant/updateCustomerType'); ?>",
            data: {customerType: customerType},
            cache: false,
            beforeSend: function () {
                startLoadPos();
            },
            success: function (data) {
                selectCustomerButton(id);
                if (isDelivery == 1) {
                    openDeliveryModal();
                    $(".deliveryRow").show()
                    $(".deliveryPromotionRow").show()
                    $(".promotionRow").hide()
                    $('select[name="deliveryPersonID"]').attr('id', 'deliveryPersonID')
                    $('select[name="promotionID"]').attr('id', 'promotionID')
                    $("#isDelivery").val(1);
                    $("#deliveryDateDiv").show()
                    $("#delivery_customerTypeID").val(id);
                    load_delivery_info();
                    if ($("#deliveryOrderID").val() > 0) {
                        $("#advancePaidDiv").show();
                    } else {
                        $("#advancePaidDiv").hide();
                    }
                    $("#deliveryPersonID").val('-1').change();
                    $('#delivery_update_btn_div').hide();
                } else if (deliveryType.trim() == "Promotion") {
                    $(".promotionRow").show()
                    $(".deliveryPromotionRow").show()
                    $(".deliveryRow").hide()
                    $('select[name="deliveryPersonID"]').attr('id', 'deliveryPersonID')
                    $('select[name="promotionID"]').attr('id', 'promotionID')
                    $("#isDelivery").val(0);
                    $("#deliveryDateDiv").hide();
                    $("#advancePaidDiv").hide();
                } else {
                    $(".promotionRow,.deliveryRow,.deliveryPromotionRow").hide();
                    $("#isDelivery").val(0);
                    $("#deliveryDateDiv").hide();
                    $("#advancePaidDiv").hide();
                }
                stopLoad();
                if (data['error'] == 0) {
                    calculateFooter();

                    if (ordermd == 1) {
                        open_pos_payments_modal();
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == false) {
                    myAlert('w', 'Local Server is Offline, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });
        return false;
    }

    function openemailPrintmodule() {
        $("#email_modal").modal('show');
    }

    function send_pos_email() {
        var data = $('#frm_print_email_address').serializeArray();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/save_send_pos_email'); ?>",
            data: data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    $("#email_modal").modal('hide');
                    myAlert('s', 'Message: ' + "Email Sent Successfully");
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
            }
        });
    }
</script>


<!-- CUSTOMER MODAL  -->
<div aria-hidden="true" role="dialog" id="pos_payments_customer_modal" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal">
                <div class="modal-header posModalHeader">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-close text-red"></i></button>
                    <h4><i class="fa fa-users"></i> Customer </h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label class="col-md-4 control-label" for="customerTelephoneTmp">
                            <?php echo $this->lang->line('posr_customer_telephone'); ?><!--Customer Telephone--></label>
                        <div class="col-md-6">
                            <input type="number" name="customerTelephoneTmp" id="customerTelephoneTmp"
                                   class="form-control input-md">

                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label" for="customerNameTmp">
                            <?php echo $this->lang->line('posr_customer_name'); ?><!--Customer Name--></label>
                        <div class="col-md-6">
                            <input type="text" name="customerNameTmp" id="customerNameTmp"
                                   class="form-control input-md">

                        </div>
                    </div>


                </div>
                <div class="modal-footer" style="margin-top: 0px;">
                    <button class="btn btn-lg btn-default" data-dismiss="modal"><i class="fa fa-times text-red"></i>
                        Cancel
                    </button>
                    <button class="btn btn-lg btn-default" onclick="clearCustomerVal()" type="reset"><i
                                class="fa fa-eraser text-purple"></i> Clear
                    </button>
                    <button class="btn btn-lg btn-primary" type="button" onclick="setCustomerInfo()"><i
                                class="fa fa-plus"></i> Add
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openCustomerModal() {
        $("#pos_payments_customer_modal").modal('show');
    }

    function setCustomerInfo() {
        var customerName = $("#customerNameTmp").val();
        var customerTel = $("#customerTelephoneTmp").val();
        if ($("#customerTelephoneTmp").val() == '') {
            myAlert('e', 'Telephone Number is not Valid');
            return false;
        }

        $("#customerName").val(customerName);
        $("#customerTelephone").val(customerTel);
        $("#pos_payments_customer_modal").modal('hide');
    }

    function clearCustomerVal() {
        $("#customerName").val('');
        $("#customerTelephone").val('');
    }
</script>


<!-- PROMOTION -->
<div aria-hidden="true" role="dialog" id="pos_payments_promotion_modal" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form class="form-horizontal">
                <div class="modal-header posModalHeader">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-close text-red"></i></button>
                    <h4> Promotion </h4>
                </div>
                <div class="modal-body">

                    <button class="btn btn-lg btn-default btn-block" onclick="clearPromotion()" type="button"><i
                                class="fa fa-eraser text-red"></i> Clear
                    </button>

                    <?php

                    $promotion = get_specialCustomers(array(2, 3));
                    if (!empty($promotion)) {
                        foreach ($promotion as $val) {
                            $val['customerID'];
                            ?>
                            <button
                                    class="btn btn-lg <?php echo $val['customerTypeMasterID'] == 3 ? 'btn-default' : 'btn-default'; ?> btn-block"
                                    onclick="checkPosAuthentication(1,<?php echo $val['customerID'] ?>)" type="button">
                                <!--addPromotion(--><?php /*echo $val['customerID'] */ ?><!--)-->
                                <?php echo $val['customerTypeMasterID'] == 3 ? '<i class="fa fa-bullhorn text-red"></i>' : '<i class="fa fa-bullhorn text-purple"></i>'; ?> <?php echo $val['customerName'] ?> <?php echo $val['commissionPercentage']; ?>
                                %
                            </button>
                            <?php
                        }
                    } ?>

                </div>
                <div class="modal-footer" style="margin-top: 0px;">
                    <button class="btn btn-lg btn-default" data-dismiss="modal"><i class="fa fa-times text-red"></i>
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog" id="pos_sampleBill" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-responsive-bill">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_print'); ?><!--Print--> </h4>
            </div>
            <div class="modal-body modal-responsive-bill" id="pos_modalBody_sampleBill">

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                        style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center; color:#FFF; background-color:#005b8a; border:0px solid #007FFF; padding: 10px 1px; margin: 5px auto 10px auto; font-weight:bold;">
                    <i class="fa fa-angle-double-left" aria-hidden="true"></i>
                    Back
                </button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="1" style="z-index: 9999;" id="order_mode_modal" class="modal fade"
     data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title">Please select order mode</h4>
            </div>
            <div class="modal-body" id="" style="">
                <div style="text-align: center;margin: 35px;">
                    <?php
                    $customerType = getCustomerType();
                    if (!empty($customerType)) {
                        ?>
                        <input type="hidden" id="customerType" name="customerType" value="">
                        <input type="hidden" id="is_dine_in" name="is_dine_in" value="0">
                        <div class="order-type-btn-group">
                            <div class="btn-group btn-group-lg">
                                <?php
                                $defaultID = 0;
                                $isDelivery = 0;
                                $isDineIn = 0;
                                foreach ($customerType as $val) {
                                    ?>
                                    <button type="button" data-val="<?php echo $val['customerDescription'] ?>"
                                            onclick="updateCustomerTypeBtn(<?php echo $val['customerTypeID']; ?>,<?php echo $val['isThirdPartyDelivery'] ?>,<?php echo $val['isDineIn'] ?>,1)"
                                            class="btn buttonCustomerType buttonDefaultSize <?php if ($val['isDefault'] == 1) {
                                                $defaultID = $val['customerTypeID'];
                                                $isDelivery = $val['isThirdPartyDelivery'];
                                                $isDineIn = $val['isDineIn'];
                                                //echo 'btn-primary';
                                                echo 'btn-default';
                                            } else {
                                                echo 'btn-default';
                                            }
                                            ?>  customerType"
                                            id="customerTypeID_<?php echo $val['customerTypeID']; ?>">
                                        <?php echo $val['displayDescription']; ?>
                                    </button>
                                <?php }
                                ?>
                            </div>
                            <script>
                                function defaultDelivaryButton() {
                                    <?php
                                    if($defaultID){
                                    ?>
                                    updateCustomerTypeBtn(<?php echo $defaultID ?>, <?php echo $isDelivery ?>,<?php echo $isDineIn ?>, 1);
                                    <?php
                                    }
                                    ?>
                                }

                                $(document).ready(function (e) {
                                    defaultDineinButtonID = '<?php echo $defaultID; ?>';
                                });
                            </script>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    var isOutletTaxEnabled = "<?php echo $isOutletTaxEnabled; ?>";
    function openPromotionModal() {
        $("#pos_payments_promotion_modal").modal('show');
    }

    function addPromotion(id) {
        $("#promotionID").val(id).change();
        setTimeout(function () {
            $("#deliveryPersonID").val('-1').change();
        }, 50);
        $("#pos_payments_promotion_modal").modal('hide');
        $("#tmp_promotion").val($("#promotionID option:selected").text().trim());
        setTimeout(function () {
            var netTotalTmp = $("#final_payableNet_amt").text();
            var netTotal = parseFloat(netTotalTmp);
            $("#gross_total_input").val(netTotal);
        }, 50);
    }

    function addPromotion_update(id) {
        $("#promotionIDUpdate").val(id).change();
        setTimeout(function () {
            $("#deliveryPersonIDUpdate").val('').change();
        }, 50);
        //$("#pos_payments_promotion_modal").modal('hide');
        $("#tmp_promotionUpdate").val($("#promotionIDUpdate option:selected").text().trim());
        setTimeout(function () {
            var netTotalTmp = $("#final_payableNet_amtUpdate").text();
            var netTotal = parseFloat(netTotalTmp);
            $("#gross_total_inputUpdate").val(netTotal);
        }, 50);
    }

    function clearPromotion() {
        $("#promotionID").val('').change();
        $("#deliveryPersonID").val('-1').change();
        $("#pos_payments_promotion_modal").modal('hide');
        $("#tmp_promotion").val('');
        setTimeout(function () {
            var netTotalTmp = $("#final_payableNet_amt").text();
            var netTotal = parseFloat(netTotalTmp);
            $("#gross_total_input").val(netTotal);
        }, 50);
    }


    function calculatePaidAmountUpdate(tmpThis) {
        if ($("#isDeliveryUpdate").val() == 1) {
            if ($("#deliveryPersonIDUpdate").val() == "") {
                //$(".paymentOther").val(0);
            } else {
                if ($("#deliveryPersonIDUpdate").val() > 0) {
                    if ($("#deliveryPersonIDUpdate option:selected").data('otp') == 1) { // on time payment
                        var cardTotal = 0;
                        $(".paymentOtherUpdate").each(function (e) {
                            var valueThis = $.trim($(this).val());
                            cardTotal += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;
                        });
                        var deliveryAmount = $("#totalPayableAmountDelivery_idUpdate").val();
                        if (cardTotal > deliveryAmount) {
                            $(".paymentOtherUpdate").val(0);
                            myAlert('e', 'You can not enter card amount more than delivery amount!')
                            return false;
                        }
                    }
                }
            }
        }
        var total = 0
        $(".paymentInputupdate").each(function (e) {
            var valueThis = $.trim($(this).val());
            total += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;
        });
        $("#paidUpdate").val(total);
        var payable = $("#total_payable_amtUpdate").val();
        var returnAmount = total - payable;
        if (returnAmount > 0) {
            $("#returned_changeUpdate").val(returnAmount);
            $("#return_changeUpdate").html(returnAmount.toFixed(<?php echo $d ?>))
        }
        setTimeout(function () {
            var discount = parseFloat($("#promotional_discountUpdate").val());
            var subTotal = $("#total_payable_amtUpdate").val();
            var netTotal = subTotal - discount;
            var paidAmountTmp = parseFloat($("#paidUpdate").val());
            var advancePaymets = $("#delivery_advancePaymentAmountUpdate").val();
            netTotal = netTotal - advancePaymets;
            var returnChange = paidAmountTmp - netTotal;
            $("#final_payableNet_amtUpdate").html(netTotal.toFixed(<?php echo $d ?>));

            var returnChange;
            //update amount with taxes. 1
            if (isOutletTaxEnabled == "true") {
                var outlet_tax = apply_outlet_tax_to_net_value(parseFloat(netTotal.toFixed(<?php echo $d ?>)));
                var netValueWithOutletTax = outlet_tax.updated_net_value;
                var outletTaxAmount = outlet_tax.calculated_tax_amount;
                $("#outlet_tax_in_invoiceUpdate").html(outletTaxAmount.toFixed(<?php echo $d ?>));
                $("#final_payableNet_amtUpdate").html(netValueWithOutletTax.toFixed(<?php echo $d ?>));
                returnChange = paidAmountTmp - netValueWithOutletTax;
            } else {
                returnChange = paidAmountTmp - netTotal;
            }

            if (returnChange > 0 || true) {
                $("#return_changeUpdate").html(returnChange.toFixed(<?php echo $d ?>))
            }

            /** Total card amount should not be more than the NET Total */

            if (typeof tmpThis !== "undefined") {
                var cardTotal = 0;
                $(".paymentOtherUpdate").each(function (e) {
                    var valueThis = $.trim($(this).val());
                    cardTotal += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;
                });
                //netTotal = netTotal.toFixed(<?php echo $d ?>);
                //update amount with taxes. 2
                if (isOutletTaxEnabled == "true") {
                    var outlet_tax = apply_outlet_tax_to_net_value(parseFloat(netTotal.toFixed(<?php echo $d ?>)));
                    var netValueWithOutletTax = outlet_tax.updated_net_value;
                    netTotal = netValueWithOutletTax;
                } else {
                    netTotal = netTotal.toFixed(<?php echo $d ?>);
                }

                if (cardTotal > netTotal) {
                    $(".paymentOtherUpdate ").val(0);
                    calculateReturnUpdate();
                    $("#cardTotalAmountUpdate").val(0);
                    myAlert('e', 'You can not pay more than the net total using cards!');

                } else {
                    $("#cardTotalAmountUpdate").val(cardTotal);

                }

            }
            $("#netTotalAmountUpdate").val(netTotal);
        }, 50);
    }

    function calculate_net_card_totalUpdate() {
        setTimeout(function () {
            var discount = parseFloat($("#promotional_discountUpdate").val());
            var subTotal = $("#total_payable_amtUpdate").val();
            var netTotal = subTotal - discount;
            var cardTotal = 0;
            var isGiftCardModal = true;
            $(".paymentOtherUpdate").each(function (e) {
                var valueThis = $.trim($(this).val());
                cardTotal += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;
                if ($(this).attr('p-type') == 'gift_card') {
                    isGiftCardModal = false;
                }
            });

            //netTotal = netTotal.toFixed(<?php echo $d ?>);
            //update amount with taxes. 6
            if (isOutletTaxEnabled == "true") {
                var outlet_tax = apply_outlet_tax_to_net_value(parseFloat(netTotal.toFixed(<?php echo $d ?>)));
                var netValueWithOutletTax = outlet_tax.updated_net_value;
                netTotal = netValueWithOutletTax;
            } else {
                netTotal = netTotal.toFixed(<?php echo $d ?>);
            }

            if (cardTotal > netTotal && isGiftCardModal) {
                $(".paymentOtherUpdate ").val(0);
                calculateReturnUpdate();
                $("#cardTotalAmountUpdate").val(0);
                myAlert('e', 'You can not pay more than the net total using cards!');
            } else {
                $("#cardTotalAmountUpdate").val(cardTotal);
            }
            $("#netTotalAmountUpdate").val(netTotal);
        }, 60);
    }

    function calculatePaidAmount(tmpThis) {

        if ($("#isDelivery").val() == 1) {
            if ($("#deliveryPersonID").val() == "") {
                $(".paymentOther").val(0);
            } else {

                if ($("#deliveryPersonID").val() > 0) {

                    if ($("#deliveryPersonID option:selected").data('otp') == 1) { // on time payment

                        var cardTotal = 0;
                        $(".paymentOther").each(function (e) {
                            var valueThis = $.trim($(this).val());
                            cardTotal += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;
                        });
                        var deliveryAmount = $("#totalPayableAmountDelivery_id").val();

                        if (cardTotal > deliveryAmount) {
                            $(".paymentOther").val(0);
                            myAlert('e', 'You can not enter card amount more than delivery amount!')
                            return false;

                        }

                    }
                }
            }
        }


        var total = 0
        $(".paymentInput").each(function (e) {
            var valueThis = $.trim($(this).val());
            total += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;

        });
        $("#paid").val((total).toFixed(<?php echo $d ?>));
        var payable = $("#total_payable_amt").val();
        var returnAmount = total - payable;
        if (returnAmount > 0) {
            $("#returned_change").val(returnAmount);
            $("#return_change").html(returnAmount.toFixed(<?php echo $d ?>))
        }

        setTimeout(function () {
            var discount = parseFloat($("#promotional_discount").val());
            var subTotal = $("#total_payable_amt").val();
            var netTotal = subTotal - discount;
            var paidAmountTmp = parseFloat($("#paid").val());

            var advancePaymets = $("#delivery_advancePaymentAmount").val();

            //update amount with taxes. 3
            var returnChange;
            if (isOutletTaxEnabled == "true") {
                var outlet_tax = apply_outlet_tax_to_net_value(parseFloat(netTotal.toFixed(<?php echo $d ?>)));
                var netValueWithOutletTax = outlet_tax.updated_net_value;
                var outletTaxAmount = outlet_tax.calculated_tax_amount;
                $("#outlet_tax_in_invoice").html(outletTaxAmount.toFixed(<?php echo $d ?>));
                netTotal = netValueWithOutletTax;
                $("#final_payableNet_amt").html(netValueWithOutletTax.toFixed(<?php echo $d ?>));
                returnChange = paidAmountTmp - netValueWithOutletTax;
            } else {
                $("#final_payableNet_amt").html(netTotal.toFixed(<?php echo $d ?>));
                netTotal = netTotal.toFixed(<?php echo $d ?>);
                returnChange = paidAmountTmp - netTotal;
            }

            netTotal = netTotal - advancePaymets;

            if (returnChange > 0 || true) {
                $("#return_change").html(returnChange.toFixed(<?php echo $d ?>))
            }

            /** Total card amount should not be more than the NET Total */
            if (typeof tmpThis !== "undefined") {
                var cardTotal = 0;
                $(".paymentOther").each(function (e) {
                    var valueThis = $.trim($(this).val());
                    cardTotal += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;
                });

                //netTotal = netTotal.toFixed(<?php echo $d ?>);
                //update amount with taxes. 4
                if (isOutletTaxEnabled == "true") {
                    var outlet_tax = apply_outlet_tax_to_net_value(parseFloat(netTotal.toFixed(<?php echo $d ?>)));
                    var netValueWithOutletTax = outlet_tax.updated_net_value;
                    netTotal = netValueWithOutletTax;
                }

                if (cardTotal > netTotal) {

                    $(".paymentOther ").val(0);
                    calculateReturn();
                    $("#cardTotalAmount").val(0);
                    myAlert('e', 'You can not pay more than the net total using cards!');


                } else {
                    $("#cardTotalAmount").val(cardTotal);
                }
                $("#netTotalAmount").val(netTotal);
            }

        }, 50);
    }


    function calculate_net_card_total() {

        setTimeout(function () {
            var discount = parseFloat($("#promotional_discount").val());
            var subTotal = $("#total_payable_amt").val();
            var netTotal = subTotal - discount;
            var cardTotal = 0;
            $(".paymentOther").each(function (e) {
                var valueThis = $.trim($(this).val());
                cardTotal += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;
            });

            //netTotal = netTotal.toFixed(<?php echo $d ?>);
            //update amount with taxes. 5
            if (isOutletTaxEnabled == "true") {
                var outlet_tax = apply_outlet_tax_to_net_value(parseFloat(netTotal.toFixed(<?php echo $d ?>)));
                var netValueWithOutletTax = outlet_tax.updated_net_value;
                netTotal = netValueWithOutletTax;
            } else {
                netTotal = netTotal.toFixed(<?php echo $d ?>);
            }

            if (cardTotal > netTotal) {
                $(".paymentOther ").val(0);
                calculateReturn();
                $("#cardTotalAmount").val(0);
                myAlert('e', 'You can not pay more than the net total using cards!');
            } else {
                $("#cardTotalAmount").val(cardTotal);
            }
            $("#netTotalAmount").val(netTotal);
        }, 60);
    }

    function reset_paymentMode() {
        $("#customerType").val('');
        $(".customerType").removeClass('btn-primary');
        $(".customerType").removeClass('btn-default');
        $(".customerType").addClass('btn-default');
    }

    function updateExactCard(paymentTypeID) {
        $("#paid").val(0);
        $("#paid_temp").html(0);
        $(".paymentInput").val('');
        $('.cardRef').val('');
        var totalAmount = $("#final_payableNet_amt").text();
        $("#paymentType_" + paymentTypeID).val(parseFloat(totalAmount).toFixed(<?php echo $d ?>));
        calculateReturn();
    }

    function print_sample_bill() {
        <?php
        if (isset($isHidePrintPreview) && $isHidePrintPreview) {
            echo "app.submit_mode = 'submit_and_send_to_printer';";
        } else {
            echo "app.submit_mode = 'submit_and_print';";
        }
        ?>
        var invoiceID = $("#holdInvoiceID").val();
        var outletID = $("#holdOutletID_input").val();
        // var tmp_promotion = $("#tmp_promotion").val();
        var promotional_discount = $("#promotional_discount").val();
        var promotionID = $("#promotionID").val();
        var promotionIDdatacp = $("#promotionID").find(':selected').attr('data-cp');

        var formData = $(".form_pos_receipt").serializeArray();
        var date = new Date,
            hour = date.getHours(),
            minute = date.getMinutes(),
            seconds = date.getSeconds(),
            minute = minute > 9 ? minute : "0" + minute;
        seconds = seconds > 9 ? seconds : "0" + seconds;
        hour = hour > 9 ? hour : "0" + hour;

        date = hour + ":" + minute + ":" + seconds;
        formData.push({'name': 'currentTime', 'value': date});
        formData.push({'name': 'invoiceID', 'value': invoiceID});
        formData.push({'name': 'promotional_discount', 'value': promotional_discount});
        formData.push({'name': 'promotionID', 'value': promotionID});
        formData.push({'name': 'promotionIDdatacp', 'value': promotionIDdatacp});
        formData.push({'name': 'outletID', 'value': outletID});


        if (invoiceID > 0) {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: "<?php echo site_url('Pos_restaurant/loadPrintTemplateSampleBill'); ?>",
                data: formData,
                cache: false,
                beforeSend: function () {
                    $("#pos_sampleBill").modal('show');
                    startLoadPos();
                    $("#pos_modalBody_sampleBill").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                    <!--Loading Print view-->
                    $(".isSamplePrintedFlag").val(1);
                },
                success: function (data) {
                    stopLoad();
                    $("#pos_modalBody_sampleBill").html(data);
                    if (app.submit_mode == 'submit_and_send_to_printer') {
                        print_paymentReceipt()
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == false) {
                        myAlert('w', 'Local Server is Offline, Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }
                }
            });
        } else {
            myAlert('e', 'Please select an invoice to print!');
        }

    }

    $('#pos_sampleBill').on('hidden.bs.modal', function (e) {
        //confirmation_to_hold_bill();
        holdReceipt();
    });

    function confirmation_to_hold_bill() {
        bootbox.confirm({
            message: "Do you want to hold this bill?",
            buttons: {
                confirm: {
                    label: 'Yes',
                    className: 'btn-lg btn-success touchSizeButton'
                },
                cancel: {
                    label: 'No',
                    className: 'btn-lg btn-danger touchSizeButton'
                }
            },
            callback: function (result) {
                if (result) {
                    holdReceipt();
                }
            }
        });
    }


    function updateExactCard_update(paymentTypeID) {
        $("#paidUpdate").val(0);
        $("#paid_tempUpdate").html(0);
        $(".paymentInputupdate").val('');
        $('.cardRef').val('');
        var totalAmount = $("#final_payableNet_amtUpdate").text();
        $("#paymentType_Update" + paymentTypeID).val(parseFloat(totalAmount));
        calculateReturnUpdate();
    }


    function open_pos_submitted_payments_modal_update() {
        <?php
        if (isset($template) && $template == 'general') {
            echo ' $("#customerType").val(1); ';
        }
        ?>
        /*handling exception*/
        // handleItemMisMatchException();

        var gross_total = parseFloat($("#gross_totalUpdate").html());
        var customerType = $("#customerType").val();
        if (customerType > 0) {
            $("#pos_submitted_payments_modal").modal('show');
            //$("#paid_by").select2("val", "");
            $("#paymentType_Update5").parent().parent().hide();
            setTimeout(function () {
                calculateReturnUpdate();
                $("#paidUpdate").focus();
            }, 500);
        } else {
            //bootbox.alert('<div class="alert alert-info"><strong>Please select order mode.</strong></div>');
            //$("#order_mode_modal").modal("show");
        }

    }

    function close_update_pos_submitted() {
        $("#pos_submitted_payments_modal").modal('hide');
        clearPosInvoiceSession();
    }
</script>