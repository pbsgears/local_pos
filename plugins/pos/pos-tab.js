function show_menus() {
    $("#pos_menu_list").modal('show');
}

function deleteItem_tab(tmpValue) {
    var tmpString = $(tmpValue).text().trim();
    var str = $(".itemSelected").attr("id");

    console.log(typeof str);
    if (typeof str !== 'undefined') {
        str = str.replace('item_row_', '');
        beforeDeleteItem(str);
    } else {
        swal("Error", "Please select an item to delete!", "error");
    }
}

function beforeDeleteItem(id) {
    var tmpIsSamplePrinted = $("#isSamplePrinted_" + id).val();
    if (tmpIsSamplePrinted == 0) {
        deleteDiv(id)
    } else {
        checkPosAuthentication(13, id)
    }
}

function BOT() {
    var invoiceID = $("#holdInvoiceID").val();
    if (invoiceID > 0) {
        swal({
                title: "Are you sure?",
                text: "You want to send to BOT", /**/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "send to BOT", /*Save as Draft*/
                cancelButtonText: "Cancel"
            },
            function () {
                submitBOT();
            });
    }else {
        swal("Bill Empty", "Please place an order and submit again", "warning");
    }

}

function close_tab_window() {

}

function get_currentTime() {
    var date = new Date();
    var nHour = date.getHours(), nMin = date.getMinutes(), nSec = date.getSeconds(), ap;

    if (nHour == 0) {
        ap = " AM";
        nHour = 12;
    }
    else if (nHour < 12) {
        ap = " AM";
    }
    else if (nHour == 12) {
        ap = "PM";
    }
    else if (nHour > 12) {
        ap = "PM";
        nHour -= 12;
    }

    if (nMin <= 9) {
        nMin = "0" + nMin;
    }
    if (nSec <= 9) {
        nSec = "0" + nSec;
    }

    var output = nHour + ":" + nMin + " " + ap;
    return output;

}

function holdAndCreateNewBill() {
    /** when submit */
    $("#submit_btn_pos_receipt").html('Submit');
    $("#submit_btn").prop('disabled', false); // Please comment it later
    $("#backToCategoryBtn").click();
    resetKotButton();
    clearCreditSales();
    resetGiftCardForm();
    resetPaymentForm();


    /** when hidden*/
    $("#dis_amt").val(0);
    $("#cardTotalAmount").val(0);
    $("#netTotalAmount").val(0);
    $("#serviceCharge").val('<?php echo get_defaultServiceCharge() ?>');
    $("#pos_payments_modal").modal('hide');
    clearSalesInvoice();
    resetPayTypeBtn();
}


function confirm_createNewBill() {
    bootbox.confirm('<div class="alert alert-info"><strong> <i class="fa fa-check-circle fa-2x"></i> Sent to Kitchen successfully</string><br/><br/><br/> Do you want to create a new order?</div>', function (result) {
        if (result) {
            holdAndCreateNewBill()
        }

    });
}

function print_pos_report() {
    $.print("#print_content");
    return false;
}

function submit_pos_payments() {
    var isDelivery = $("#isDelivery").val();
    var deliveryPersonID = $("#deliveryPersonID").val();
    if (isDelivery == 1) {
        if (deliveryPersonID > 0 || deliveryPersonID == -1) {
            validateBalanceAmount();
        } else {
            myAlert('e', 'Please select Delivery person before submit person.')
            return false;
        }
    } else {
        validateBalanceAmount();
    }
}

function calculateFinalDiscount() {
    var disPercentage = $("#dis_amt").val();
    if (disPercentage <= 100 && disPercentage >= 0) {

        /** new Logic */
        var templateID = '<?php echo $templateID ?>';
        var grossTotal = 0;

        var totalAmount = parseFloat($("#gross_total").text());
        var totalTax = parseFloat($("#display_totalTaxAmount").text());
        var totalServiceCharge = parseFloat($("#display_totalServiceCharge").text());

        switch (parseInt(templateID)) {
            case 1:
                grossTotal = totalAmount;
                break;
            case 2:
                grossTotal = totalAmount + totalTax + totalServiceCharge;
                break;
            case 3:
                grossTotal = totalAmount + totalTax;
                break;
            case 4:
                grossTotal = totalAmount + totalServiceCharge;
                break;
            default:
                grossTotal = totalAmount;
        }


        var discountPercentage = $("#dis_amt").val();

        var discountAmount = (discountPercentage / 100) * grossTotal;

        $("#total_discount_amount").val(discountAmount);
        $("#total_discount").html('(' + discountAmount.toFixed(2) + ')');
        $("#discountAmountFooter").val(discountAmount.toFixed(2));

        var netTotal = grossTotal - discountAmount;


        $("#total_netAmount").html(netTotal.toFixed(2)); //output Net Amount
        $("#final_payable_amt").html(netTotal.toFixed(2)); //output Net Amount

        $("#gross_total_input").val(netTotal);


    } else {

        $("#dis_amt").val(0);
        $("#total_discount_amount").val(0);
        $("#discountAmountFooter").val('');
    }
}

function clearInvoice() {
    $("#log").html('');
}

function calculateFooter(discountFrom) {
    $('.numberFloat').keypress(function (event) {
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

    var totalAmount = 0;
    var grossTotal = 0;
    var totalQty = 0;
    var totalDiscount = 0;
    var netAmountTotal = 0;
    var totalTax = 0;
    var totalTaxDiscount = 0;
    var serviceCharge = 0;
    var serviceChargeDiscount = 0;
    var totalPriceWithoutTax = 0;
    var totalPriceWithoutTaxDiscount = 0;

    $("div .itemList").each(function (e) {

        var qty = $(this).find(".display_qty").val();
        if (qty < 0) {
            $(this).find(".display_qty").val(1);
            qty = $(this).find(".display_qty").val();
        }


        var tmpSC = $(this).find(".totalMenuServiceCharge").val();
        serviceCharge = parseFloat(serviceCharge) + (parseFloat(tmpSC) * qty);

        var tmpTax = $(this).find(".totalMenuTaxAmount").val();
        totalTax = parseFloat(totalTax) + (parseFloat(tmpTax) * qty);

        var pricewithoutTax = $(this).find(".pricewithoutTax").val();
        totalPriceWithoutTax = parseFloat(totalPriceWithoutTax) + (parseFloat(pricewithoutTax) * qty);

        var perItemCost = parseFloat($(this).find(".menu_itemCost").text());
        var discountAmount = $(this).find(".menu_discount").val();
        var total = qty * perItemCost;

        if (discountFrom == 'P') {
            var percentage = $(this).find(".menu_discount_percentage").val();
            if (percentage > 100 || percentage < 0) {
                $(this).find(".menu_discount_percentage").val(0);
                $(this).find(".menu_discount_amount").val(0);
            } else {
                var discountedAmount = (percentage / 100) * total
                $(this).find(".menu_discount_amount").val(discountedAmount.toFixed(2));

                /** Tax Handling */
                var taxAmount = $(this).find(".totalMenuTaxAmount").val();
                var discountedTax = (percentage / 100) * taxAmount;
                $(this).find(".totalMenuTaxAmountDiscount").val(discountedTax);
                totalTaxDiscount = totalTaxDiscount + (discountedTax * qty);


                /** Service Charge */
                var tmpServiceCharge = $(this).find(".totalMenuServiceCharge").val();
                var discountedServiceCharge = (percentage / 100) * tmpServiceCharge;
                $(this).find(".totalMenuServiceChargeDiscount").val(discountedServiceCharge);
                serviceChargeDiscount = serviceChargeDiscount + (discountedServiceCharge * qty);

                /** PriceWithoutTax */
                var tmpPriceWithoutTax = $(this).find(".pricewithoutTax").val();
                var discountedPriceWithoutTax = (percentage / 100) * tmpPriceWithoutTax;
                $(this).find(".pricewithoutTaxDiscount").val(discountedPriceWithoutTax);
                totalPriceWithoutTaxDiscount = totalPriceWithoutTaxDiscount + (discountedPriceWithoutTax * qty);
            }


        } else if (discountFrom == 'A') {
            var discountedAmount = $(this).find(".menu_discount_amount").val();
            var percentage = (discountedAmount * 100) / total;
            if (percentage == 0) {
                $(this).find(".menu_discount_percentage").val(percentage);
            } else if (percentage > 100 || percentage < 0) {
                //alert('Invalid discount amount ' + percentage);
                $(this).find(".menu_discount_percentage").val(0);
                $(this).find(".menu_discount_amount").val(0);
                var discountedAmount = $(this).find(".menu_discount_amount").val();
            } else {
                $(this).find(".menu_discount_percentage").val(percentage.toFixed(1));

                /** Tax Handling */
                var taxAmount = $(this).find(".totalMenuTaxAmount").val();
                var discountedTax = (percentage / 100) * taxAmount;
                $(this).find(".totalMenuTaxAmountDiscount").val(discountedTax);
                totalTaxDiscount = totalTaxDiscount + (discountedTax * qty);


                /** Service Charge */
                var tmpServiceCharge = $(this).find(".totalMenuServiceCharge").val();
                var discountedServiceCharge = (percentage / 100) * tmpServiceCharge;
                $(this).find(".totalMenuServiceChargeDiscount").val(discountedServiceCharge);
                serviceChargeDiscount = serviceChargeDiscount + (discountedServiceCharge * qty);

                /** PriceWithoutTax */
                var tmpPriceWithoutTax = $(this).find(".pricewithoutTax").val();
                var discountedPriceWithoutTax = (percentage / 100) * tmpPriceWithoutTax;
                $(this).find(".pricewithoutTaxDiscount").val(discountedPriceWithoutTax);
                totalPriceWithoutTaxDiscount = totalPriceWithoutTaxDiscount + (discountedPriceWithoutTax * qty);
            }

        } else {
            var discountedAmount = $(this).find(".menu_discount_amount").val();
            var percentage = $(this).find(".menu_discount_percentage").val();
            var discountedAmount = (percentage / 100) * total
            $(this).find(".menu_discount_amount").val(discountedAmount.toFixed(2));

            /** Tax Handling */
            var taxAmount = $(this).find(".totalMenuTaxAmount").val();
            var discountedTax = (percentage / 100) * taxAmount;
            $(this).find(".totalMenuTaxAmountDiscount").val(discountedTax);
            totalTaxDiscount = totalTaxDiscount + (discountedTax * qty);

            /** Service Charge */
            var tmpServiceCharge = $(this).find(".totalMenuServiceCharge").val();
            var discountedServiceCharge = (percentage / 100) * tmpServiceCharge;
            $(this).find(".totalMenuServiceChargeDiscount").val(discountedServiceCharge);
            serviceChargeDiscount = serviceChargeDiscount + (discountedServiceCharge * qty);

            /** PriceWithoutTax */
            var tmpPriceWithoutTax = $(this).find(".pricewithoutTax").val();
            var discountedPriceWithoutTax = (percentage / 100) * tmpPriceWithoutTax;
            $(this).find(".pricewithoutTaxDiscount").val(discountedPriceWithoutTax);
            totalPriceWithoutTaxDiscount = totalPriceWithoutTaxDiscount + (discountedPriceWithoutTax * qty);
        }

        if (discountedAmount == undefined) {
            discountedAmount = 0;
        }

        var netTotal = total - discountedAmount;
        $(this).find(".itemCostNet").text(netTotal.toFixed(2));
        var sellingPrice = $(this).find(".itemCostNet").text();

        /** Policy based Amount */
        var policyBasedAmount = calculateTotalAmount(pricewithoutTax, tmpTax, tmpSC, qty, percentage);
        netAmountTotal = parseFloat(netAmountTotal) + parseFloat(policyBasedAmount);

        var totalWithoutDiscount = qty * perItemCost;
        $(this).find(".menu_total").text(total.toFixed(2));
        totalAmount = parseFloat(totalAmount) + parseFloat(total);
        totalQty = parseFloat(totalQty) + parseFloat(qty);
        grossTotal = parseFloat(grossTotal) + parseFloat(totalWithoutDiscount);
        totalDiscount = parseFloat(totalDiscount) + parseFloat(discountAmount);

    });

    var taxDiscountount = totalTax - totalTaxDiscount;
    var serviceChargeDiscountount = serviceCharge - serviceChargeDiscount;
    var priceWithoutTaxDiscountount = netAmountTotal - totalPriceWithoutTaxDiscount;

    /** Total Tax */
    $("#display_totalTaxAmount").html(taxDiscountount.toFixed(2));
    $("#display_totalServiceCharge").html(serviceChargeDiscountount.toFixed(2));

    var netTotal = totalTax + serviceCharge + totalPriceWithoutTax;

    $("#total_item_qty").html(totalQty);
    $("#total_item_qty_input").val(totalQty);
    $("#final_purchased_item").html(totalQty);
    $("#gross_total").html(netAmountTotal.toFixed(2));
    calculateFinalDiscount();
    //calculateReturn();
}

function calculateTotalAmount(priceWithoutTax, taxAmount, ServiceCharge, qty, discount) {
    var discount = parseFloat(discount);
    var discountAmount = 0;
    //console.log(' Price Without Tax: ' + priceWithoutTax + ' - TAX: ' + taxAmount + ' - SC: ' + ServiceCharge + ' - qty: ' + qty);
    var templateID = '<?php echo $templateID ?>';
    var totalAmount = 0;
    switch (parseInt(templateID)) {
        case 1:
            totalAmount = (parseFloat(priceWithoutTax) + parseFloat(taxAmount) + parseFloat(ServiceCharge)) * parseFloat(qty);
            /** All Inclusive */
            break;
        case 2:
            /** Tax & Service Charge Separated */
            totalAmount = parseFloat(priceWithoutTax) * parseFloat(qty);
            break;
        case 3:
            /** Tax Separated */
            totalAmount = (parseFloat(priceWithoutTax) + parseFloat(ServiceCharge)) * parseFloat(qty);
            break;
        case 4:
            /** Service Charge Separated */
            totalAmount = (parseFloat(priceWithoutTax) + parseFloat(taxAmount) ) * parseFloat(qty);
            break;

        default:
            /** All Inclusive */
            totalAmount = (parseFloat(priceWithoutTax) + parseFloat(taxAmount) + parseFloat(ServiceCharge)) * parseFloat(qty);
    }
    if (discount > 0) {
        discountAmount = ( totalAmount * discount ) / 100;
    }
    totalAmount = totalAmount - discountAmount;
    return totalAmount;
}

function generate_template(data) {
    var divTmp = '<div onclick="selectMenuItem(this)" class="row itemList" id="item_row_' + data['code'] + '" style="margin: 0px; border-bottom: 1px solid #dddddd; padding-top: 5px; padding-bottom: 5px;"><div class="hide"></div><div class="col-md-4 col-xs-4 col-sm-4 menuItem_pos_col_5">' + data['menuMasterDescription'] + ' </div><div class="col-md-8 col-xs-8 col-sm-8"> <div class="receiptPadding"> <input type="text" onkeyup="calculateFooter()" onchange="updateQty(' + data['code'] + ')" value="1" class="display_qty menuItem_input numberFloat numPad" id="qty_' + data['code'] + '" name="qty[' + data['code'] + ']"  /> </div> <div class="receiptPadding"> <span class="menu_itemCost menuItemTxt"> ' + data['sellingPrice'] + '</span> <!-- @rate --> <input type="hidden"  class="menuItemTxt_input numberFloat" name="sellingPrice[' + data['code'] + ']" value="' + data['sellingPrice'] + '"/> <input type="hidden"  class="menuItemTxt_input numberFloat pricewithoutTax" name="pricewithoutTax[' + data['code'] + ']" value="' + data['pricewithoutTax'] + '"/> <input type="hidden"  class="menuItemTxt_inputDiscount numberFloat pricewithoutTaxDiscount" name="pricewithoutTaxDiscount[' + data['code'] + ']" value="' + data['pricewithoutTax'] + '"/> <input type="hidden"  class="menuItemTxt_input totalMenuTaxAmount numberFloat" name="totalMenuTaxAmount[' + data['code'] + ']" value="' + data['totalTaxAmount'] + '"/><input type="hidden"  class="menuItemTxt_inputDiscount totalMenuTaxAmountDiscount numberFloat" name="totalMenuTaxAmountDiscount[' + data['code'] + ']" value="' + data['totalTaxAmount'] + '"/><input type="hidden" class="menuItemTxt_input numberFloat totalMenuServiceCharge" name="totalMenuServiceCharge[' + data['code'] + ']" value="' + data['totalServiceCharge'] + '"/><input type="hidden"  class="menuItemTxt_inputDiscount numberFloat totalMenuServiceChargeDiscount" name="totalMenuServiceChargeDiscount[' + data['code'] + ']" value="' + data['totalServiceCharge'] + '"/><input type="hidden"  name="frm_isTaxEnabled[' + data['code'] + ']" value="' + data['isTaxEnabled'] + '"/><input type="hidden" class="isSamplePrintedFlag" id="isSamplePrinted_' + data['code'] + '" value="0"></div> <div class="receiptPadding hide"> <span class="menu_total menuItemTxt">0</span>  <!-- total --> </div> <div class="receiptPadding hide"> <input style="width:60%;" onchange="item_wise_discount(this,\'P\')" name="discountPercentage[' + data['code'] + ']"  maxlength="3" type="text" value="0" class="menu_discount_percentage menu_qty menuItem_input numberFloat numPad"> <!-- disc. % --> </div> <div class="receiptPadding hide" > <input style="width:90%;" onchange="item_wise_discount(this,\'A\')" name="discountAmount[' + data['code'] + ']" type="text" value="0" class="menu_discount_amount menu_qty menuItem_input numberFloat numPad"><!-- disc. amount --> </div> <div class="receiptPadding"> <div style="width:55px; text-align: right;" class="itemCostNet menuItemTxt"> [' + data['sellingPrice'] + '</div> <!-- net total --> <div onclick="deleteDiv(' + data['code'] + ')" data-placement="bottom" rel="tooltip" title="Delete" style="cursor:pointer; width: 12px; margin-top: -20px;     margin-right: 2px;" class="pull-right"><button type="button" class="btn btn-default btn-sm hide itemList-delBtn"><i class="fa fa-close closeColor"></i></button></div> </div> </div></div>';
    return divTmp;

    /** <button type="button" class="btn btn-default btn-sm  itemList-delBtn"><i class="fa fa-close closeColor"></i></button> */

}


function checkisKOT(id, isPack, kotID, description) {
    if (kotID > 0) {
        open_kitchen_note(id, kotID);
        $("#kot_kotID").val(kotID);
        $("#kitchenNoteDescription").html(description);
    } else {
        if (isPack == 1) {
            LoadToInvoicePack(id);
        } else {
            LoadToInvoice(id);
        }
    }
}

function open_kitchen_note(id, kotID) {
    $("#tmpWarehouseMenuID").val(id);
    if (kotID > 0) {
        $("#pos_kitchen_note").modal('show');
    }
}

function LoadToInvoicePack(id) {
    load_packItemList(id);
}

function initNumPad() {
    $('.numpad').unbind();
    $('.numpad').numpad();
}

function goToMenu() {
    $('html, body').animate({scrollTop: $('#pos_tab_menu_container').position().top}, 'fast');
}

function goToMenuList() {
    $('html, body').animate({scrollTop: $('#pos_tab_menu_list_container').position().top}, 'fast');
}

function goToFooter() {
    $('html, body').animate({scrollTop: $('#pos_btnSet_dtd').position().top}, 'fast');
}
