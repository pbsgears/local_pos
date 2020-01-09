<?php
/**
 * --- Created on 08-Nov-2017 by Mohamed Shafri
 * --- Delivery
 */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<style>
    .delivery-form-title {
        border-bottom: 1px solid #8a4616;
        font-weight: 600;
        font-size: 15px;
        padding: 4px;
    }

</style>
<div aria-hidden="true" role="dialog" id="pos_deliveryModal" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-close text-red"></i>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-truck"></i> Delivery (<span id="delivery_invoiceCode"></span>)
                    <span id="delivery_order_id_div"></span>
                    <span id="deliveryLoader" style="display: none;" class="pull-right">
                        <i class="fa fa-refresh fa-2x fa-spin"></i>
                    </span>
                </h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <form class="form-horizontal" id="delivery_order">
                        <input type="hidden" name="deliveryOrderID" id="deliveryOrderID" value="0">
                        <input type="hidden" name="customerTypeID" id="delivery_customerTypeID" value="0">
                        <fieldset>
                            <div class="col-xs-12 col-sm-12  col-md-12 col-lg-12 ">
                                <h5 class="delivery-form-title"><i class="fa fa-user"></i> Customer information </h5>
                            </div>

                            <div class="col-xs-12 col-sm-12  col-md-6 col-lg-6 ">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="phone">Phone</label>
                                    <div class="col-md-8">
                                        <input id="delivery_phone" name="phone" type="text" required
                                               placeholder="Type number and press enter to search"
                                               class="form-control input-md">
                                        <span class="input-req-inner"></span>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="email">Email Address</label>
                                    <div class="col-md-8">
                                        <input id="delivery_email" name="email" type="text" placeholder="email Address"
                                               class="form-control input-md">
                                        <!-- <span class="input-req-inner"></span>-->
                                    </div>
                                </div>

                                <input id="delivery_posCustomerAutoID" name="posCustomerAutoID" type="hidden" value="0">

                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="delivery_CustomerName">
                                        Customer Name
                                    </label>
                                    <div class="col-md-8">
                                        <input id="delivery_CustomerName" name="CustomerName" type="text"
                                               placeholder="Customer Name" class="form-control input-md">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="delivery_DOB">Date of Birth</label>
                                    <div class="col-md-4">
                                        <input id="delivery_DOB" name="DOB" type="text" placeholder="dd-mm-yyyy"
                                               class="form-control input-md pickDate_dob">
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12  col-md-6 col-lg-6 ">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="delivery_customerAddress1Type">Address
                                        Type</label>
                                    <div class="col-md-6">
                                        <select id="delivery_customerAddress1Type" name="customerAddress1Type"
                                                class="form-control">
                                            <option value="Home">Home</option>
                                            <option value="Office">Office</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="delivery_CustomerAddress1">
                                        Address </label>
                                    <div class="col-md-8">
                                        <input id="delivery_CustomerAddress1" name="CustomerAddress1" type="text"
                                               placeholder="CustomerAddress1" class="form-control input-md">
                                    </div>
                                </div>


                                <div class="form-group hide">
                                    <label class="col-md-4 control-label" for="delivery_customerAddress2Type">Address
                                        Type</label>
                                    <div class="col-md-5">
                                        <select id="delivery_customerAddress2Type" name="customerAddress2Type"
                                                class="form-control">
                                            <option value="Home">Home</option>
                                            <option value="Office">Office</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group hide">
                                    <label class="col-md-4 control-label" for="delivery_CustomerAddress2"> Address
                                        2</label>
                                    <div class="col-md-8">
                                        <input id="delivery_CustomerAddress2" name="CustomerAddress2" type="text"
                                               placeholder="CustomerAddress2" class="form-control input-md">
                                    </div>
                                </div>


                            </div>


                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                                    <div class="col-xs-12 col-sm-12  col-md-12 col-lg-12 ">
                                        <h5 class="delivery-form-title"><i class="fa fa-truck"></i> Delivery Information
                                        </h5>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="pull-right">
                                        <button id="delivery_edit_btn" type="button" class="btn btn-primary btn-xs"
                                                onclick="edit_delivery()" style="margin-right: 12px; display: none;">
                                            <i class="fa fa-pencil-square-o"></i> Edit
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="delivery_deliveryDate">Delivery
                                            Date</label>
                                        <div class="col-md-4">
                                            <input id="delivery_deliveryDate" name="deliveryDate" type="text"
                                                   placeholder="dd-mm-yyyy" value="<?php echo date('d-m-Y') ?>"
                                                   class="form-control input-md pickDate deliveryInputCls">
                                            <span class="input-req-inner"></span>

                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="delivery_deliveryTime">Delivery
                                            Time</label>
                                        <div class="col-md-4">
                                            <input id="delivery_deliveryTime" name="deliveryTime" type="text"
                                                   placeholder="Delivery Time"
                                                   class="form-control input-md pickTime deliveryInputCls">

                                        </div>
                                    </div>


                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <input type="hidden" name="deliveryType" id="delivery_deliveryType"
                                           value="Delivery">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="deliveryType">Dispatch Type</label>
                                        <div class="col-md-6">
                                            <div class="btn-group">
                                                <button type="button" id="deliveryType1"
                                                        class="btn btn-default deliveryType deliveryInputCls"
                                                        onclick="click_deliveryType(1)">Pick up
                                                </button>
                                                <button type="button" id="deliveryType2"
                                                        class="btn btn-primary deliveryType deliveryInputCls"
                                                        onclick="click_deliveryType(2)">Delivery
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="delivery_landMarkLocation"> <abbr
                                                title="Nearest Landmark Location">LandMark</abbr> <i
                                                class="fa fa-map-marker text-red"></i> </label>
                                        <div class="col-md-6">
                                            <input id="delivery_landMarkLocation" name="landMarkLocation" type="text"
                                                   placeholder="Land mark location"
                                                   class="form-control input-md deliveryInputCls">
                                        </div>
                                    </div>


                                </div>
                                <div class="row" id="delivery_update_btn_div" style="display: none;">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="pull-right">
                                            <button onclick="update_deliveryInfo()" type="button"
                                                    id="editBtn_deliveryUpdate"
                                                    class="btn btn-primary btn-xs" style="margin: 10px 30px 0px 0px;">
                                                <i class="fa fa-check"></i> Update
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            </div>


                        </fieldset>
                    </form>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="col-xs-12 col-sm-12  col-md-12 col-lg-12 ">
                                <h5 class="delivery-form-title">Payments </h5>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="col-xs-12 col-sm-12  col-md-12 col-lg-12 ">
                                <div id="delivery_payment_detail_div"></div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button class="btn  btn-lg btn-default" style="display: none;" id="delivery_print_salesOrder"
                        type="button" onclick="print_delivery_order_payments()">
                    <i class="fa fa-print"></i> Print Sales Order
                </button>
                <button class="btn btn-lg btn-primary" id="delivery_confirm_btn" type="button"
                        onclick="confirm_delivery_order()">
                    <i class="fa fa-check"></i> Confirm
                </button>

                <button class="btn btn-lg btn-default" id="delivery_dispatch_btn" style="display: none;" type="button"
                        onclick="delivery_dispatchOrder()">
                    <i class="fa fa-truck"></i> Dispatch
                </button>

                <button class="btn btn-lg btn-danger" id="delivery_cancel_btn" style="display: none;" type="button"
                        onclick="checkPosAuthentication(9)">
                    <i class="fa fa-times"></i> Cancel Order
                </button>

                <button id="delivery_addItems_btn" style="display: none;" type="button"
                        type="button" class="btn btn-lg btn-success" data-dismiss="modal">
                    <i class="fa fa-plus"></i> Add Items
                </button>

                <!--<button class="btn btn-lg btn-danger" type="button"
                        onclick="reset_delivery_order()">
                    <i class="fa fa-eraser"></i> Clear
                </button>-->

                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?> </button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="delivery_advancePayment_modal" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" aria-hidden="true" data-dismiss="modal">
                    <i class="fa fa-close text-red"></i>
                </button>
                <h4 class="modal-title">Advance Payment Receipt </h4>
            </div>
            <div class="modal-body" id="delivery_advancePayment_modalBody" style="min-height: 400px;">

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                        style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center; color:black; 10px 1px; margin: 5px auto 10px auto; font-weight:bold;">
                    <i class="fa fa-angle-double-left"
                       aria-hidden="true"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>


<!--EMAIL MODAL -->
<div aria-hidden="true" role="dialog" tabindex="1" style="z-index: 9999;" id="delivery_email_modal" class="modal"
     data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog" style="width: 400px">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('posr_enter_email'); ?><!--Enter Email--> </h4>
            </div>
            <div class="modal-body" id="" style="">
                <form method="post" id="frm_print_email_address_delivery">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="email" class="form-control" id="delivery_emailAddress" name="emailAddress">
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer" style="margin-top: 0px; padding: 7px;">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>

                <button type="button" onclick="send_pos_email_delivery()" class="btn btn-sm btn-primary"
                        style="background-color: #3fb618; color: #FFF; border: 0px; float: right;">
                    <span id=""><?php echo $this->lang->line('common_submit'); ?></span><!--Submit-->
                </button>
            </div>
        </div>
    </div>
</div>
<script>

    function update_deliveryInfo() {

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_delivery/update_deliveryInfo'); ?>",
            data: $("#delivery_order").serialize(),
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    $("#delivery_update_btn_div").hide();
                    $(".deliveryInputCls").attr("disabled", true);

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
    function edit_delivery() {
        $(".deliveryInputCls").prop('disabled', false);
        $("#delivery_update_btn_div").show();
    }
    var dispatchMsg = 'Are you sure want to dispatch this order?';
    function delivery_dispatchOrder() {
        $("#delivery_order :input").attr("disabled", false);
        bootbox.confirm(dispatchMsg, function (confirmed) {
            if (confirmed) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('Pos_delivery/delivery_dispatchOrder'); ?>",
                    data: $("#delivery_order").serialize(),
                    dataType: "json",
                    cache: false,
                    beforeSend: function () {
                        $("#deliveryLoader").show();

                    },
                    success: function (data) {
                        $("#deliveryLoader").hide();
                        if (data['error'] == 0) {
                            myAlert('s', data['message']);
                            $("#backToCategoryBtn").click();
                            $("#pos_deliveryModal").modal('hide');
                            $("#dis_amt").val(0);
                            $("#cardTotalAmount").val(0);
                            $("#netTotalAmount").val(0);
                            resetKotButton();
                            clearCreditSales();
                            resetGiftCardForm();
                            clearPromotion();
                            resetPaymentForm();
                            reset_delivery_order();
                            clearSalesInvoice();
                            resetPayTypeBtn();
                            $("#delivery_order :input").attr("disabled", false);
                            $("#deliveryDateDiv").hide();

                        } else if (data['error'] == 1) {
                            disable_input_delivery()
                            myAlert('e', data['message']);
                        } else if (data['error'] == 2) {
                            disable_input_delivery()
                            bootbox.alert('<div class="alert alert-danger">' + data['message'] + '<div>');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        disable_input_delivery()
                        $("#deliveryLoader").hide();
                        if (jqXHR.status == false) {
                            myAlert('w', 'Local Server is Offline, Please try again');
                        } else {
                            myAlert('e', 'Message: ' + errorThrown);
                        }
                    }
                });

            } else {
                modalFix();
            }
        });
    }

    function openEmailPrint_delivery() {
        $("#delivery_email_modal").modal('show');
        $("#delivery_emailAddress").val($("#delivery_email").val());
    }

    function send_pos_email_delivery() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_delivery/save_send_pos_email'); ?>",
            data: {menuSalesID: $("#holdInvoiceID_input").val(), emailAddress: $("#delivery_emailAddress").val()},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    $("#delivery_email_modal").modal('hide');
                    myAlert('s', 'Message: ' + "Email Sent Successfully");
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                if (jqXHR.status == false) {
                    myAlert('w', 'Local Server is Offline, Please try again');
                }
            }
        });
    }

    function print_delivery_order_payments() {
        var invoiceID = $("#holdInvoiceID_input").val();
        if (invoiceID > 0) {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: "<?php echo site_url('Pos_delivery/loadPrintTemplate'); ?>",
                data: {menuSalesID: invoiceID},
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#delivery_advancePayment_modal').modal('show');
                    $("#delivery_advancePayment_modalBody").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    if (jqXHR.status == false) {
                        myAlert('w', 'Local Server is Offline, Please try again');
                    }
                }
            });
        } else {
            myAlert('e', 'Load the invoice and click again.')
        }
    }

    function load_delivery_info() {
        $("#delivery_payment_detail_div").html('');
        var invoiceID = $("#holdInvoiceID_input").val();
        if (invoiceID > 0) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {invoiceID: invoiceID},
                url: "<?php echo site_url('Pos_delivery/load_delivery_info'); ?>",
                beforeSend: function () {
                    $("#deliveryLoader").show();
                },
                success: function (data) {
                    $("#deliveryLoader").hide();
                    if (data['error'] == 0) {
                        $("#delivery_confirm_btn").hide();
                        $("#delivery_cancel_btn").show();
                        $("#delivery_dispatch_btn").show();
                        $("#delivery_print_salesOrder").show();
                        $("#delivery_addItems_btn").show();

                        $("#delivery_phone").val(data['tmpData']['phoneNo']);
                        loadDeliveryCustomerInfo();
                        $("#delivery_customerTypeID").val(data['tmpData']['posCustomerAutoID']);
                        $("#deliveryOrderID").val(data['tmpData']['deliveryOrderID']);
                        $("#delivery_landMarkLocation").val(data['tmpData']['landMarkLocation']);
                        $("#delivery_deliveryTime").val(data['tmpData']['deliveryTime']);
                        var deliveryTime = ' - Time: ' + $("#delivery_deliveryTime").val();
                        $("#pos_delivery_date").html(data['tmpData']['deliveryDate'] + deliveryTime);
                        if (data['tmpData']['deliveryType'] == 'Pick up') {
                            click_deliveryType(1);
                        } else if (data['tmpData']['deliveryType'] == 'Delivery') {
                            click_deliveryType(2);
                        }
                        delivery_load_payment_detail();
                        disable_input_delivery();
                        $("#advancePaidDiv").show();
                    }
                }, error: function (jqXHR, textStatus, errorThrown) {
                    $("#deliveryLoader").hide();
                    if (jqXHR.status == false) {
                        myAlert('w', 'Local Server is Offline, Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }
                }
            });
        }
    }


    function delivery_load_payment_detail() {
        var invoiceID = $("#holdInvoiceID_input").val();
        var orderID = $("#deliveryOrderID").val();
        if (invoiceID > 0 && orderID > 0) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {invoiceID: invoiceID},
                url: "<?php echo site_url('Pos_delivery/delivery_load_payment_detail'); ?>",
                beforeSend: function () {
                    $("#deliveryLoader").show();
                },
                success: function (data) {
                    $("#deliveryLoader").hide();
                    $("#delivery_payment_detail_div").html(data);

                }, error: function (jqXHR, textStatus, errorThrown) {
                    $("#deliveryLoader").hide();
                    if (jqXHR.status == false) {
                        myAlert('w', 'Local Server is Offline, Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }
                }
            });
        } else {
            $("#delivery_payment_detail_div").html('');
        }
    }

    $(document).ready(function (e) {
        $('.pickDate').datepicker({
            format: 'dd-mm-yyyy',
            startDate: '-1d'
        });


        $('.pickDate_dob').datepicker({
            format: 'dd-mm-yyyy'
        });

        resetTime();

        $("#delivery_phone").keyup(function (e) {
            var keyValue = e.which;
            if (keyValue == 13) {
                loadDeliveryCustomerInfo()
            }
        })

        $("#delivery_deliveryDate, #delivery_deliveryTime").change(function (e) {
            var deliveryTime = ' - Time: ' + $("#delivery_deliveryTime").val();
            $("#pos_delivery_date").html($("#delivery_deliveryDate").val() + deliveryTime);
        })

    });

    function resetTime() {
        var date = new Date,
            hour = date.getHours(),
            minute = date.getMinutes(),
            seconds = date.getSeconds(),
            ampm = hour > 12 ? "PM" : "AM";

        hour = hour % 12;
        hour = hour ? hour : 12; // zero = 12

        minute = minute > 9 ? minute : "0" + minute;
        seconds = seconds > 9 ? seconds : "0" + seconds;
        hour = hour > 9 ? hour : "0" + hour;

        date = hour + ":" + minute + " " + ampm;
        $('.pickTime').timepicker();
        $('.pickTime').val(date);
        return date;
    }

    function loadDeliveryCustomerInfo() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {phoneNo: $("#delivery_phone").val()},
            url: "<?php echo site_url('Pos_delivery/loadDeliveryCustomerInfo'); ?>",
            beforeSend: function () {
                $("#deliveryLoader").show();
            },
            success: function (data) {
                $("#deliveryLoader").hide();
                if (data['error'] == 0) {
                    $("#delivery_email").val(data['customerData']['customerEmail']);
                    $("#delivery_CustomerName").val(data['customerData']['CustomerName']);
                    $("#delivery_DOB").val(data['customerData']['DOB']);
                    //$("#delivery_landMarkLocation").val(data['customerData']['landMarkLocation']);
                    $("#delivery_CustomerAddress1").val(data['customerData']['CustomerAddress1']);
                    $("#delivery_CustomerAddress2").val(data['customerData']['customerAddress2']);
                    $("#delivery_customerAddress1Type").val(data['customerData']['customerAddress1Type']);
                    $("#delivery_customerAddress2Type").val(data['customerData']['customerAddress2Type']);
                    $("#delivery_edit_btn").show();

                } else if (data.error == 2) {
                    myAlert('i', data['message']);
                    $("#delivery_email").val('');
                    $("#delivery_CustomerName").val('');
                    $("#delivery_DOB").val('');
                    $("#delivery_CustomerAddress1").val('');
                    $("#delivery_CustomerAddress2").val('');
                    $("#delivery_customerAddress1Type").val('Home');
                    $("#delivery_customerAddress2Type").val('Home');
                } else {
                    myAlert('e', data['message']);
                }

            }, error: function (jqXHR, textStatus, errorThrown) {
                $("#deliveryLoader").hide();
                if (jqXHR.status == false) {
                    myAlert('w', 'Local Server is Offline, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });
    }

    function reset_delivery_order() {
        var curTime = resetTime();
        curTime = ' - Time: ' + curTime;
        $("#delivery_order")[0].reset();
        $("#delivery_customerTypeID").val(0)
        $("#deliveryOrderID").val(0)
        $("#delivery_confirm_btn").show();
        $("#delivery_cancel_btn").hide();
        $("#delivery_dispatch_btn").hide();
        $("#delivery_print_salesOrder").hide();
        $("#delivery_addItems_btn").hide();
        $("#pos_delivery_date").html('<?php echo date('d-m-Y') ?>' + curTime);
        $("#delivery_advancePaymentAmount").val(0);
        $("#delivery_advancePaymentAmountShow").html('0.00');
        resetTime();
        $("#delivery_order :input").attr("disabled", false);
    }

    function confirm_delivery_order() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: $("#delivery_order").serialize(),
            url: "<?php echo site_url('Pos_delivery/confirm_delivery_order'); ?>",
            beforeSend: function () {
                $("#deliveryLoader").show();
            },
            success: function (data) {
                $("#deliveryLoader").hide();
                if (data['error'] == 0) {
                    $("#deliveryOrderID").val(data['orderID']);
                    $("#holdInvoiceID_input").val(data['tmpInvoiceID']);
                    $("#holdInvoiceID").val(data['tmpInvoiceID']);
                    $("#pos_salesInvoiceID_btn").html(data['tmpInvoiceID_code']);
                    $("#pos_orderNo").val(data['tmpInvoiceID_code']);
                    $("#holdInvoiceID_codeTmp").val(data['tmpInvoiceID_code']);
                    $("#delivery_invoiceCode").html(data['tmpInvoiceID_code']);
                    if (data['code'] == 2) {
                        $("#pos_deliveryModal").modal('hide');
                    }
                    myAlert('s', data.message);
                    $("#delivery_confirm_btn").hide();
                    $("#delivery_cancel_btn").show();
                    $("#delivery_dispatch_btn").show();
                    $("#delivery_print_salesOrder").show();
                    $("#delivery_addItems_btn").show();
                    disable_input_delivery();
                    $("#advancePaidDiv").show();
                    $("#delivery_edit_btn").show();

                } else if (data['error'] == 1) {
                    myAlert('e', data.message);
                } else {
                    myAlert('e', 'Error');
                }

            }, error: function (jqXHR, textStatus, errorThrown) {
                $("#deliveryLoader").hide();
                if (jqXHR.status == false) {
                    myAlert('w', 'Local Server is Offline, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });
    }

    function click_deliveryType(id) {
        if (id == 1) {
            $(".deliveryType").removeClass();
            $("#deliveryType1").addClass('btn btn-primary  deliveryType deliveryInputCls');
            $("#deliveryType2").addClass('btn btn-default deliveryType deliveryInputCls');
            $("#delivery_deliveryType").val('Pick up');

        } else if (id == 2) {
            $(".deliveryType").removeClass();
            $("#deliveryType1").addClass('btn btn-default deliveryType deliveryInputCls');
            $("#deliveryType2").addClass('btn btn-primary deliveryType deliveryInputCls');
            $("#delivery_deliveryType").val('Delivery');
        }
    }

    function openDeliveryModal() {
        $("#pos_deliveryModal").modal('show');
        $("#delivery_invoiceCode").html($("#pos_orderNo").val())
    }


    function loadCustomerCardData() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {barcode: $("#gc_barcode").val()},
            url: "<?php echo site_url('Pos_giftCard/loadCustomerCardData'); ?>",
            beforeSend: function () {
                $("#deliveryLoader").show();
            },
            success: function (data) {
                $("#deliveryLoader").hide();
                if (data['error'] == 0) {

                    $("#gc_customerTelephone").val(data['customerTelephone']);
                    $("#gc_CustomerName").val(data['CustomerName']);
                    $("#topUpAmount").val(parseFloat(data['balanceAmount']).toFixed(<?php echo $d ?>));
                    $("#posCustomerAutoID").val(data['posCustomerAutoID']);
                    $("#gc_cardIssueID").val(data['cardIssueID']);
                    if (data['posCustomerAutoID'] == 0) {
                        $("#newCustomer").show();
                    } else {
                        $("#newCustomer").hide();
                    }
                } else {
                    $("#newCustomer").show();
                }

            }, error: function (jqXHR, textStatus, errorThrown) {
                $("#deliveryLoader").hide();
                if (jqXHR.status == false) {
                    myAlert('w', 'Local Server is Offline, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });
    }


    function resetDeliveryForm() {


    }

    function disable_input_delivery() {
        $("#delivery_order :input").attr("disabled", true);
        $("#delivery_edit_btn, #editBtn_deliveryUpdate,#delivery_deliveryType").attr("disabled", false);
        $("#delivery_customerTypeID, #deliveryOrderID,#delivery_posCustomerAutoID").attr("disabled", false);
    }

</script>