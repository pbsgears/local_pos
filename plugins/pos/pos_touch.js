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
};


function getNumberAndValidate(thisVal) {
    thisVal = $.trim(thisVal);

    if ($.isNumeric(thisVal)) {
        return parseFloat(thisVal);
    }
    else {
        return parseFloat(0);
    }
}


function checkChequePayment(ele) {
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

function calculateReturn() {
    /*var totalTmp = $("#totalPayableAmountDelivery_id").val();
     var customerType = $('#customerTypeBtnString').val();*/
    var total = parseFloat($("#final_payable_amt").text())
    /*if (customerType == 'Delivery Orders' || customerType == 'Promotion') {
     total = totalTmp;
     }*/
    var paidAmount = parseFloat($("#paid").val())
    var return_amount = 0
    calculateDelivery()

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
            $("#returned_change").val(return_amount.toFixed(2))
        }

    } else {
        $("#return_change").text('0.00')
        $("#returned_change").val('0.00')
    }
    console.log(total + ' _ ' + (paidAmount - return_amount))
    if (total <= paidAmount && total != 0) {
        document.getElementById("submit_btn").style.display = "block"
        $("#total_payable_amt").val(total)
    } else {
        document.getElementById("submit_btn").style.display = "block" //none
    }
}