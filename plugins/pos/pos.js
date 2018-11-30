shortcut.add("F1", function () {
    open_pos_payments_modal();
});


shortcut.add("Ctrl+F", function () {
    $("#searchProd").val('');
    var filter = $("#searchProd").val();

    $("#allProd #proname").each(function () {
        if ($(this).text().search(new RegExp(filter, "i")) < 0) {
            $(this).parent().hide();
        } else {
            $(this).parent().show();
        }
    });
    $("#searchProd").focus();
});

shortcut.add("Ctrl+S", function () {
    holdReceipt();
});
shortcut.add("Ctrl+O", function () {
    open_holdReceipt();
});


shortcut.add("F2", function () {
    $("#searchProd").val('');
    var filter = $("#searchProd").val();

    $("#allProd #proname").each(function () {
        if ($(this).text().search(new RegExp(filter, "i")) < 0) {
            $(this).parent().hide();
        } else {
            $(this).parent().show();
        }
    });
    $("#searchProd").focus();
});

shortcut.add("F3", function () {
    recall_invoice();
});

shortcut.add("F4", function () {
    var fnTr = $('#itemDisplayTB tr.selectedTR');
    console.log(fnTr.find('td:eq(1)').html());

});

/*shortcut.add("F5", function () {
 newInvoice();
 });*/

shortcut.add("F6", function () {
    open_customer_modal();
});

shortcut.add("F7", function () {
    open_recallHold_modal();
});

shortcut.add("F8", function () {
    adjust_qty();
});

shortcut.add("Delete", function () {
    deleteItem();
});


function focusOnLastCharacter(id) {
    var inputField = document.getElementById(id);
    if (inputField != null && inputField.value.length != 0) {
        if (inputField.createTextRange) {
            var FieldRange = inputField.createTextRange();
            FieldRange.moveStart('character', inputField.value.length);
            FieldRange.collapse();
            FieldRange.select();
        } else if (inputField.selectionStart || inputField.selectionStart == '0') {
            var elemLen = inputField.value.length;
            inputField.selectionStart = elemLen;
            inputField.selectionEnd = elemLen;
            inputField.focus();
        }
    } else {
        inputField.focus();
    }
}


var tDay = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
var tMonth = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

function getClock() {
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

    $('#timeBox').text(nHour + " : " + nMin + " : " + nSec + " " + ap);

}

function getDate() {
    var toDay = new Date();
    var day = toDay.getDay(), cMonth = toDay.getMonth(), nDate = toDay.getDate(), nYear = toDay.getYear();
    if (nYear < 1000) {
        nYear += 1900;
    }
    $('#dateBox').html(tDay[day] + " &nbsp;" + nDate + " &nbsp;" + tMonth[cMonth] + " &nbsp;" + nYear);
}

window.onload = function () {
    getClock();
    getDate();
    setInterval(getClock, 1000);
    setupPromotionData();
};

function setupPromotionData() {
    var freeIssueItems = window.localStorage.getItem('freeIssueItems');
    freeIssueData = JSON.parse(freeIssueItems);
}


function getNumberAndValidate(thisVal) {
    thisVal = $.trim(thisVal);

    if ($.isNumeric(thisVal)) {
        return parseFloat(thisVal);
    }
    else {
        return parseFloat(0);
    }
}


function session_close(parameter1, parameter2, parameter3, parameter4) {
    if ($("#holdInvoiceID").val() == 0) {
        bootbox.confirm({
            message: '<div class="alert alert-danger" style="text-align: center;"><strong><i class="fa fa-power-off fa-2x"></i> ' + parameter1 + '</strong><br/>' + parameter2 + '</div>',
            buttons: {
                'cancel': {
                    label: '' + parameter3 + '', /*cancel*/
                },
                'confirm': {
                    label: '' + parameter4 + '', /*Ok*/
                }
            },
            callback: function (result) {
                if (result) {
                    $("#isStart").val(0);
                    $(".tillModal_close").show();
                    $("#tillModal_title").text("Day End");
                    $("#tillSave_Btn").attr("onclick", "shift_close()");
                    till_modal.modal({backdrop: "static"});
                    modalFix();
                }
            }
        });
    } else {
        bootbox.alert('<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle fa-2x"></i> Warning! </strong><br/><br/>Please close the current bill.</div>');
    }


}

function changeButtonPayType(id) {
    $("#payment_method").val(id);
    $(".payType").removeClass('paymentTypeCustom')
    $(".payType").addClass('btn-default')
    $("#payTypeBtnID" + id).removeClass('btn-default')
    $("#payTypeBtnID" + id).addClass('paymentTypeCustom')
}

function resetPayTypeBtn() {
    changeButtonPayType(1);
}


function checkChequePayment(ele) {
    changeButtonPayType(ele)
    if (ele == "4") {
        document.getElementById("paid").readOnly = false;
        document.getElementById("paid").value = 0;
        document.getElementById("return_change").innerHTML = 0;
        document.getElementById("card_numb").value = "";
        document.getElementById("cheque").value = "";


        document.getElementById("submit_btn").style.display = "none";

        document.getElementById("card_wrp").style.display = "none";
        document.getElementById("card_numb").required = false;

        document.getElementById("cheque_wrp").style.display = "block";
        document.getElementById("cheque").required = true;
        document.getElementById("cheque").focus();

        document.getElementById("cardRefNo_wrp").style.display = "none";
        document.getElementById("cardRefNo").required = false;

    } else if (ele == 2 || ele == 3) {
        var totalAmount = parseFloat($("#final_payable_amt").text());
        document.getElementById("paid").readOnly = false;
        document.getElementById("paid").value = totalAmount.toFixed(2);
        document.getElementById("return_change").innerHTML = 0;
        document.getElementById("card_numb").value = "";
        document.getElementById("cheque").value = "";

        document.getElementById("submit_btn").style.display = "block";

        document.getElementById("cheque_wrp").style.display = "none";
        document.getElementById("cheque").required = false;

        document.getElementById("card_wrp").style.display = "none";
        document.getElementById("card_numb").required = false;


        document.getElementById("cardRefNo_wrp").style.display = "block";
        document.getElementById("cardRefNo").required = true;
        document.getElementById("cardRefNo").focus();

    } else {

        document.getElementById("paid").readOnly = false;
        document.getElementById("paid").value = 0;
        document.getElementById("return_change").innerHTML = 0;
        document.getElementById("card_numb").value = "";
        document.getElementById("cheque").value = "";

        document.getElementById("submit_btn").style.display = "none";

        document.getElementById("cheque_wrp").style.display = "none";
        document.getElementById("cheque").required = false;

        document.getElementById("card_wrp").style.display = "none";
        document.getElementById("card_numb").required = false;

        document.getElementById("cardRefNo_wrp").style.display = "none";
        document.getElementById("cardRefNo").required = false;
        document.getElementById("paid").focus();
    }
    calculateReturn();
}

function calculateDelivery() {
    var total = parseFloat($("#final_payableNet_amt").text());
    var paidAmount = parseFloat($("#paid").val());
    var return_amount = 0;
    // var elementid = $(element).attr('id')
    var delivery = $("#deliveryPersonID option:selected").data('cp');
    if (typeof(delivery) != "undefined") {

        var commission = total * (delivery / 100);
        /*console.log('commission:'+commission);*/
        var deliveryPayable = total - commission;
        /*console.log('deliveryPayable: '+deliveryPayable);*/

        $("#totalPayableAmountDelivery_id").val(deliveryPayable.toFixed(2));
        if (total < paidAmount || true) {
            return_amount = paidAmount - deliveryPayable;
            if (return_amount < 0) {
                //return_amount = 0;
            }
            if (!isNaN(return_amount)) {
                $("#returned_change_toDelivery").val(return_amount.toFixed(2));
            } else {
                $("#returned_change_toDelivery").val(0);
            }
        } else {
            $("#returned_change_toDelivery").val('0.00');
        }

    } else {
        $("#totalPayableAmountDelivery_id").val(0);
        $("#returned_change_toDelivery").val(0);
    }
}


function calculateReturn() {

    if ($("#isDelivery").val() == 1) {
        if ($("#deliveryPersonID").val() == "") {
            $(".paymentOther").val(0);
            $("#frm_isOnTimePayment").val('');
        } else {

            if ($("#deliveryPersonID").val() > 0) {

                if ($("#deliveryPersonID option:selected").data('otp') == 1) { // on time payment

                    $("#frm_isOnTimePayment").val(1);
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

                } else if ($("#deliveryPersonID option:selected").data('otp') == 0) {
                    $("#frm_isOnTimePayment").val(0);
                } else {
                    $("#frm_isOnTimePayment").val('');

                }
            }
        }
    }

    var total = parseFloat($("#final_payable_amt").text())
    var paidAmount = parseFloat($("#paid").val());
    var return_amount = 0;


    if (total < paidAmount || true) {
        return_amount = paidAmount - total;
        if (return_amount < 0) {
            $("#return_change").text('0.00')
            $("#returned_change").val('0.00')
        } else {
            if (isNaN(return_amount)) {
                var return_amount = 0;
            }

            $("#return_change").text(return_amount.toFixed(2))
            /**/
            $("#returned_change").val(return_amount.toFixed(2))
        }

    } else {

        $("#return_change").text('0.00')
        $("#returned_change").val('0.00')
    }

    if ((total <= paidAmount || true) && total != 0 && !isNaN(total)) {
        document.getElementById("submit_btn").style.display = "block"
        $("#total_payable_amt").val(total)
    } else {
        document.getElementById("submit_btn").style.display = "block" //none
    }

    calculatePaidAmount();
    calculatePromo();
    calculateDelivery()
    calculate_net_card_total()

}

function calculatePromo() {
    var payableAmount = $("#total_payable_amt").val();
    var promotion = $("#promotionID option:selected").data('cp');
    if (!isNaN(promotion) && promotion > 0) {
        var promotionAmount = (promotion / 100) * payableAmount;
        $("#promotional_discount").val((promotionAmount).toFixed(2))
    } else {
        $("#promotional_discount").val(0)

    }
}

function openCategory(categoryID) {
    if (categoryID == 0) {
        $("#backToCategoryBtn").click();
    } else {
        $("#categoryBtnID_" + categoryID).click();
        console.log('clicked: categoryBtnID_' + categoryID);
        return false;
    }
}

function go_one_step_back_category() {
    var current_id = $("#categoryCurrentID").val();
    var parent_id = $("#categoryParentID").val();

    console.log('parent_id:' + parent_id + " - current_id: " + current_id);
    openCategory(parent_id);


}

function set_categoryInfo(parentID, currentID) {
    $("#categoryParentID").val(parentID)
    $("#categoryCurrentID").val(currentID);
    console.log('set parent:' + parentID + " - current: " + currentID);
    return false;
}


function set_categoryInfoDefault() {
    $("#categoryParentID").val(0)
    $("#categoryCurrentID").val(0);
}

function disable_POS_btnSet() {
    $(".btn-disable-when-load").prop('disabled', true);
    $(".itemList-delBtn").prop('disabled', true);
}

function enable_POS_btnSet() {
    $(".btn-disable-when-load").prop('disabled', false);
    $(".itemList-delBtn").prop('disabled', false);
}

