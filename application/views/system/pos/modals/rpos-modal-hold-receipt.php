<?php
/**
 * --- Created on 16-NOV-2016 by Mohames Shafry
 * --- POS Open Hold Receipt Modal Window
 */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="pos_open_hold_receipt" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog pos_open_hold_receipt modal-lg">
        <div class="modal-content">


            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('posr_open_bills'); ?><!--Open Bills--> </h4>
            </div>
            <div id="modal_body_pos_openHoldReceipt" class="modal-body"
                 style="overflow: visible; background-color: #FFF; min-height: 100px;" onclick="">

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<?php
/**
 * --- POS Hold Receipt Modal Window
 */
?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="pos_hold_receipt_modal" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('posr_hold_bills'); ?><!--Hold Receipt--> </h4>
            </div>
            <div class="modal-body" style="overflow: visible; background-color: #FFF; min-height: 100px;"
                 id="modal_body_hold">
                <form class="form-horizontal" id="frm_POS_holdReceipt">
                    <input type="hidden" name="holdInvoiceID_input" id="holdInvoiceID_input" value="0">

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="holdInvoiceID_codeTmp">
                            <?php echo $this->lang->line('posr_invoice_id'); ?><!--Invoice ID--></label>
                        <div class="col-md-4">
                            <input id="holdInvoiceID_codeTmp" readonly type="text"
                                   placeholder="<?php echo $this->lang->line('posr_invoice_id'); ?>"
                                   class="form-control input-md"><!--Invoice ID-->
                        </div>
                    </div>

                    <div class="form-group hide">
                        <label class="col-md-3 control-label" for="holdInvoiceID">
                            <?php echo $this->lang->line('posr_invoice_id'); ?><!--Invoice ID--></label>
                        <div class="col-md-4">
                            <input id="holdInvoiceID" name="invoiceID" readonly type="text"
                                   placeholder="<?php echo $this->lang->line('posr_invoice_id'); ?>"
                                   class="form-control input-md"><!--Invoice ID-->
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="holdReference">
                            <?php echo $this->lang->line('posr_hold_reference'); ?><!--Hold Reference--> </label>
                        <div class="col-md-8">
                            <input type="text" id="holdReference" name="holdReference"
                                   placeholder="<?php echo $this->lang->line('posr_hold_reference'); ?>"
                                   class="form-control input-md touchEngKeyboard"><!--Type Hold Reference-->
                        </div>
                    </div>


                </form>
                <div class="modal-footer" style="margin-top: 10px;">
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    &nbsp; <input type="button" name="hold_bill_submit" id="hold_bill_submit"
                                  value="<?php echo $this->lang->line('common_submit'); ?>"
                                  onclick="submitHoldReceipt()"
                                  class="btn btn-primary "
                                  style="background-color: #3fb618; color: #FFF; border: 0px; padding: 5px 25px; float: right;">
                    <!--Submit-->
                </div>
            </div>

        </div>
    </div>
</div>

<script>

    function holdReceipt() {
        $("#pos_hold_receipt_modal").modal('show');
        setTimeout(function () {
            //$("#holdReference").focus();
            $("#holdReference").val('')
            load_hold_refno($("#holdInvoiceID_input").val());
        }, 800);
        $("#pos_hold_receipt_modal").keyup(function (e) {
            if (e.keyCode == 13) {
                submitHoldReceipt();
            }
        })
    }

    function submitHoldReceipt() {
        var formData = $(".form_pos_receipt,#frm_POS_holdReceipt").serializeArray();
        var date = new Date,
            hour = date.getHours(),
            minute = date.getMinutes(),
            seconds = date.getSeconds(),
            minute = minute > 9 ? minute : "0" + minute;
        seconds = seconds > 9 ? seconds : "0" + seconds;
        hour = hour > 9 ? hour : "0" + hour;

        date = hour + ":" + minute + ":" + seconds;
        formData.push({'name': 'currentTime', 'value': date});

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/submitHoldReceipt'); ?>",
            data: formData,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    $("#holdReference").val('');
                    $("#pos_hold_receipt_modal").modal('hide');
                    clearSalesInvoice();
                    reset_delivery_order();
                } else {
                    myAlert('d', data['message']);
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

    function open_holdReceipt() {
        load_pos_hold_receipt()
    }


    function load_pos_hold_receipt() {
        <?php
        if (isset($tablet) && $tablet) {
            $method = 'load_pos_hold_receipt_tablet';
        } else {
            $method = 'load_pos_hold_receipt';
        }
        ?>
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/' . $method); ?>",
            data: $("#frm_POS_holdReceipt").serialize(),
            cache: false,
            beforeSend: function () {
                $("#pos_open_hold_receipt").modal("show");
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#modal_body_pos_openHoldReceipt").html(data);
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

    function open_submitted_invoice(id, outletID) {

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/openHold_sales'); ?>",
            data: {id: id, outletID: outletID},
            cache: false,
            beforeSend: function () {
                $("#pos_open_hold_receipt").modal("hide");
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if (data['error'] == 0) {

                    $("#pos_salesInvoiceID_btn").html(data['code']);
                    $("#delivery_invoiceCode").html(data['code']);
                    if (data['advancePayment'] > 0) {
                        $("#delivery_advancePaymentAmount").val(data['advancePayment']);
                        var advancePayment = parseFloat(data['advancePayment']);
                        $("#delivery_advancePaymentAmountShow").html(advancePayment.toFixed(<?php echo $d ?>));
                    }
                    if (data['isDeliveryOrder'] == 1) {
                        $("#deliveryPersonID").val('-1').change();
                        $("#isDelivery").val(1);
                        $("#deliveryOrderID").val(data['deliveryOrderID'])
                    }
                    checkPosSessionSubmitted(id);
                    $("#pos_open_void_receipt").modal("hide");
                    $is_credit_sale = is_credit_sale(id);
                    if($is_credit_sale==false){
                        open_pos_submitted_payments_modal();
                    }else{
                        myAlert('w', 'You cannot edit payment type for a credit sale.');
                    }

                } else {
                    myAlert('e', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                if (jqXHR.status == false) {
                    myAlert('w', 'No Internet, Please try again');
                } else {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }

            }
        });
    }

    function openHold_sales(id) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/openHold_sales'); ?>",
            data: {id: id},
            cache: false,
            beforeSend: function () {
                $("#pos_open_hold_receipt").modal("hide");
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    console.log(data);
                    $("#pos_salesInvoiceID_btn").html(data['code']);
                    $("#delivery_invoiceCode").html(data['code']);
                    if (data['advancePayment'] > 0) {
                        $("#delivery_advancePaymentAmount").val(data['advancePayment']);
                        var advancePayment = parseFloat(data['advancePayment']);
                        $("#delivery_advancePaymentAmountShow").html(advancePayment.toFixed(<?php echo $d ?>));
                    }
                    if (data['isDeliveryOrder'] == 1) {
                        $("#deliveryPersonID").val('-1').change();
                        $("#isDelivery").val(1);
                        $("#deliveryOrderID").val(data['deliveryOrderID'])
                    }
                    checkPosSession();
                } else {
                    myAlert('e', data['message']);
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

    function load_hold_refno(menuSalesID) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/load_hold_refno'); ?>",
            data: {menuSalesID: menuSalesID},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#holdReference').val(data['holdRemarks'])
                } else {
                    $('#holdReference').val('');
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


</script>