/* F1: Pay Bill */
shortcut.add("F1", function () {
    open_pos_payments_modal();
});

/* F2 : Search */
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

/* F3: Focus to bar code */
shortcut.add("F3", function () {
    $("#barcodeInput").focus();
});


/*
shortcut.add("F4", function () { });
shortcut.add("F5", function () { });
shortcut.add("F6", function () { });
shortcut.add("F7", function () { });
shortcut.add("F8", function () { });
*/
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
