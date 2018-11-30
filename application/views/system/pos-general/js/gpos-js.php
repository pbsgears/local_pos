<script>
    var base_url = '<?php echo base_url();?>';
    function clearInvoice() {
        $("#totSpan").html('0.00');
        $("#totVal").val('0');

        $("#discSpan").html('0.00');
        $("#discVal").val('0');
        $("#netTotSpan").html('0.00');
        $("#netTotVal").val('0');

        $("#item-image").attr('src', base_url + "/images/item/no-image.png");

        $(".itemCount").html('0');
    }

    function close_posPrint() {
        $("#print_template").modal('hide');
        $("#tender_modal").modal('hide');
        newInvoice(1);
        clearInvoice();
    }

    var d = <?php echo get_company_currency_decimal() ?>;
    $(document).ready(function (e) {
        $.fn.numpad.defaults.gridTpl = '<table class="modal-content table" style="width:200px" ></table>';
        $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in" style="z-index: 5000;"></div>';
        $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:16px; font-weight: 600;" />';
        $.fn.numpad.defaults.buttonNumberTpl = '<button type="button" class="btn btn-xl-numpad btn-numpad-default"></button>';
        $.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn btn-xl-numpad" style="width: 100%;"></button>';
        $.fn.numpad.defaults.onKeypadCreate = function () {
            $(this).find('.done').addClass('btn-primary');
            /*$(this).find('.del').addClass('btn-numpad-default');
             $(this).find('.clear').addClass('btn-numpad-default');*/
        };
        initNumPad();
        $("#gen_disc_percentage").keyup(function (e) {
            calculate_general_discount($("#gen_disc_percentage"));
        });
        $("#gen_disc_percentage").change(function (e) {
            calculate_general_discount($("#gen_disc_percentage"));
        });
        $("#gen_disc_amount").keyup(function (e) {
            calculate_general_discount($("#gen_disc_amount"));
        });
        $("#gen_disc_amount").change(function (e) {
            calculate_general_discount($("#gen_disc_amount"));
        });
        /*$("input[type='text']").click(function () {
         $(this).select();
         });*/

        $(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
            //this.value = this.value.replace(/[^0-9\.]/g,'');
            $(this).val($(this).val().replace(/[^0-9\.]/g,''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });

        $("#itemQty, #disPer, #disAmount, #salesPrice").keyup(function (e) {
            if(e.which==13){
                $("#pos_form").submit();
            }
        });

        /*switch (e.which) {
         case 38: // up
         alert('up');

         break;

         case 40: // down
         alert('down');
         break;

         default:
         return; // exit this handler for other keys
         }
         e.preventDefault(); // prevent the default action (scroll / move caret)*/


    });



    function calculate_general_discount(that) {
        var net_amount_with_item_discount = parseFloat($("#netTotVal").val());
        var discount = parseFloat(that.val());
        var div_id = that.attr('id');
        //debugger;
        if (div_id == 'gen_disc_percentage') {
            calculateDiscount_byPercentage()
        } else if (div_id == 'gen_disc_amount') {
            /** Discount Amount */
            if (net_amount_with_item_discount < discount) {
                neutral_generalDiscount();
                myAlert('e', 'Discount Amount can not be more than net total!');
            } else if (discount < 0) {
                neutral_generalDiscount();
                myAlert('e', 'Discount Amount can not be minus value!');
            } else if (net_amount_with_item_discount >= discount) {
                /*debugger;*/
                var newNetAmount = net_amount_with_item_discount - discount;
                if (newNetAmount == 0) {
                    var zero = 0;
                    $("#gen_disc_percentage").val('');
                    $("#netTot_after_g_disc_div").html(zero.toFixed(d));
                    $("#netTot_after_g_disc").val(zero);
                } else {
                    var discountPercentage = (discount * 100) / net_amount_with_item_discount
                    $("#gen_disc_percentage").val(discountPercentage);
                    $("#netTot_after_g_disc_div").html(newNetAmount.toFixed(d));
                    $("#netTot_after_g_disc").val(newNetAmount);
                    $("#gen_disc_amount_hide").val(discount);
                }

            } else {
                neutral_generalDiscount();
            }
        } else {

        }
    }

    /** Core Function : explicitly call in another method */
    function calculateDiscount_byPercentage() {
        var net_amount_with_item_discount = parseFloat($("#netTotVal").val());
        var discount = parseFloat($("#gen_disc_percentage").val());
        /** Discount Percentage  */
        if (discount <= 100 && discount > 0) {
            var discountAmount = net_amount_with_item_discount * (discount / 100);
            var newNetTotal = net_amount_with_item_discount - discountAmount;
            $("#netTot_after_g_disc_div").html(newNetTotal.toFixed(d));
            $("#netTot_after_g_disc").val(newNetTotal);
            $("#gen_disc_amount").val(discountAmount);
            $("#gen_disc_amount_hide").val(discountAmount);

        } else if (discount < 0) {
            neutral_generalDiscount();
            myAlert('e', 'Discount can not be minus value!');
        } else {
            neutral_generalDiscount();
        }
    }

    function reset_generalDiscount() {
        var zero = 0;
        $("#netTot_after_g_disc").val(zero);
        $("#netTot_after_g_disc_div").html(zero.toFixed(d));
        $("#gen_disc_amount").val('');
        $("#gen_disc_percentage").val('');
        $("#gen_disc_amount_hide").val(zero);
    }

    function neutral_generalDiscount() {
        var zero = 0;
        var net_amount_with_item_discount = parseFloat($("#netTotVal").val());
        $("#netTot_after_g_disc").val(net_amount_with_item_discount);
        $("#netTot_after_g_disc_div").html(net_amount_with_item_discount.toFixed(d));
        $("#gen_disc_amount").val('');
        $("#gen_disc_percentage").val('');
        $("#gen_disc_amount_hide").val(zero);
    }



</script>