<!-- POS System Restaurant JS : Developed By Shafri -->
<script>
    function selectMenuItem(tmpValue) {
        $('.itemSelected').removeClass('itemSelected');
        var divID = $(tmpValue.id)['selector'];
        $("#" + divID).addClass('itemSelected');
        $("#tmpQtyValue").val(0);
    }

    function selectMenuItemSpefici(id) {
        $('.itemSelected').removeClass('itemSelected');
        $("#" + id).addClass('itemSelected');
        $("#tmpQtyValue").val(0);
    }

    function updateDiscount(das) {
        var discount = $('#dis_amt').val();
        $.ajax({
            type: 'get',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/updateDiscount') . '?discount='; ?>" + discount,
            cache: false,
            beforeSend: function () {
            },
            success: function (data) {

            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == false) {
                    myAlert('w', 'Local Server is Offline, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });
    }


    function updateQty(id) {
        var qtyval = $('#qty_' + id).val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/updateQty'); ?>",
            data: {menuSalesItemID: id, qty: qtyval},
            cache: false,
            beforeSend: function () {
                numberOfRequest++;
                disable_POS_btnSet();
                startLoadPos();
            },
            success: function (data) {
                numberOfRequest--;
                if (numberOfRequest == 0) {
                    enable_POS_btnSet();
                    stopLoad();
                }
                if (data[0] == 's') {
                    calculateFooter()

                } else {
                    myAlert('e', data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                numberOfRequest--;
                if (numberOfRequest == 0) {
                    enable_POS_btnSet();
                    stopLoad();
                }
                if (jqXHR.status == false) {
                    myAlert('w', 'Local Server is Offline, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        })
    }

    function backCalculateDiscount(das) {
        //$("#dis_amt").val(0).change();
        var templateID = '<?php echo get_pos_templateID() ?>';
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

        var discountAmount = $("#discountAmountFooter").val();


        if (grossTotal >= discountAmount) {
            var discountPercentage = (100 * discountAmount) / grossTotal;
            $("#dis_amt").val(discountPercentage);
        } else {
            $("#dis_amt").val(0);
            $("#discountAmountFooter").val(0)
        }
        updateDiscount();
        calculateFooter();
        checkPosAuthentication(2, das);
    }

    function updateQty_afterAuth(id, qty) {
        $(".itemSelected [name='qty[" + id + "]']").val(qty);
        $("#tmpQtyValue").val(qty);
        calculateFooter();
        updateQty(id);
    }

    function auth_qty_update(id, qty) {
        checkPosAuthentication(13, id, qty)
    }

    function updateQty_invoice(tmpValue) {
        var tmpString = $(tmpValue).text().trim();
        var str = $(".itemSelected").attr("id");
        if (tmpString == 'C') {
            if (typeof str !== 'undefined') {
                if (str == 'dis_amt' || str == 'discountAmountFooter') {
                    $("#dis_amt").val(0);
                    $(".itemSelected").val(0);
                    $("#tmpQtyValue").val(0);
                    $("#total_discount").html(0.00);
                    $("#discountAmountFooter").val('');
                    updateDiscount();
                    calculateFooter();
                } else {

                    var tmpID = str.split('_');
                    var id = tmpID[2];
                    var isSamplePrinted = $("#isSamplePrinted_" + id).val();

                    if (isSamplePrinted == 1) {
                        auth_qty_update(id, 1);
                    } else {
                        $(".itemSelected [name='qty[" + id + "]']").val(1);
                        $("#tmpQtyValue").val(0);
                        calculateFooter();
                        updateQty(id);
                    }
                }
            }
        } else {

            if (typeof str !== 'undefined') {

                var tmpQty = $("#tmpQtyValue").val();
                var Qty = $(tmpValue).text().trim();
                var updateQtyVal = tmpQty + Qty;
                var updateQtyInt = parseFloat(updateQtyVal);
                $("#tmpQtyValue").val(updateQtyVal);
                if (str == 'dis_amt' || str == 'discountAmountFooter') {
                    $(".itemSelected").val(parseFloat(updateQtyVal));
                    calculateFooter();
                    updateDiscount();
                } else {
                    var tmpID = str.split('_');
                    var id = tmpID[2];
                    var isSamplePrinted = $("#isSamplePrinted_" + id).val();

                    if (isSamplePrinted == 1) {
                        auth_qty_update(id, updateQtyInt);
                    } else {
                        $(".itemSelected [name='qty[" + id + "]']").val(updateQtyInt);
                        calculateFooter();
                        updateQty(id);
                    }
                }
            }
        }

    }

    function updateDiscountPers(das) {
        var discount = $('#dis_amt').val();
        $.ajax({
            type: 'get',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/updateDiscount') . '?discount='; ?>" + discount,
            cache: false,
            beforeSend: function () {
            },
            success: function (data) {
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == false) {
                    myAlert('w', 'Local Server is Offline, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });
        calculateFooter();
        checkPosAuthentication(2, das)
    }

    function updateLiveTables() {
        swal({
                title: "Sync Data",
                text: "This process will take approximately 2-10 minutes depending on the internet bandwidth \n \n This process will pull data from live database to local system",
                showCancelButton: true,
                confirmButtonColor: "#f39c12",
                icon: "success",
                confirmButtonText: "Sync Now",
                closeOnConfirm: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: "<?php echo site_url('sync/pull_data'); ?>",
                        cache: false,
                        beforeSend: function () {
                            startLoad();
                            $("#holdon-overlay").css("background","rgba(208, 194, 233, 0.68)").delay(3000);
                        },
                        success: function (data) {
                            stopLoad();
                            if (data['error'] == 0) {
                                bootbox.alert('<div class="alert alert-success">\n' +
                                    '    <strong>'+data.message+' </strong><br/><br/> System is refreshing in while, please wait... \n' +
                                    '</div>');
                                setTimeout(function(){ location.reload(); }, 3000);

                            } else {
                                myAlert('d', data.message)
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            stopLoad();
                            if (jqXHR.status == false) {
                                myAlert('w', 'Local Server is Offline, Please try again');
                            } else {
                                myAlert('e', 'Message: ' + errorThrown);
                            }
                        }
                    });
                }
            });
    }
</script>
