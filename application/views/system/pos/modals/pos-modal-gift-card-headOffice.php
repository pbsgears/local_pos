<?php
/**
 * --- Created on 22-October-2017 by Mohamed Shafri
 * --- Gift Card Process
 */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>

<div aria-hidden="true" role="dialog" id="pos_giftCardModal" class="modal fade" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-close text-red"></i>
                </button>
                <h4 class="modal-title"><i class="fa fa-credit-card"></i>
                    Gift Card
                    <span id="giftCardLoader" style="display: none;" class="pull-right">
                        <i class="fa fa-refresh fa-2x fa-spin"></i>
                    </span>
                </h4>

            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <button class="btn btn-lg btn-primary giftCardBtnContainer" type="button"
                                onclick="topUpGiftCard()">
                            <i class="fa fa-chevron-up"></i> Top up
                        </button>
                        <button class="btn btn-lg btn-success giftCardBtnContainer" type="button"
                                onclick="issueGiftCard()">
                            <i class="fa fa-credit-card"></i> Issue Card
                        </button>
                        <button class="btn btn-lg btn-default giftCardBtnContainer" type="button"
                                onclick="loadCustomerCardData()">
                            <i class="fa fa-money"></i> Check Balance
                        </button>
                        <button class="btn btn-lg btn-danger giftCardBtnContainer" type="button"
                                onclick="resetGiftCardForm()">
                            <i class="fa fa-eraser"></i> Clear
                        </button>
                        <button class="btn btn-lg btn-default giftCardBtnContainer" type="button"
                                onclick="loadHistoryModal()">
                            <i class="fa fa-history"></i> History
                        </button>
                    </div>

                </div>

                <div class="row">
                    <div class="col-xs-12 col-sm-12  col-md-6 col-lg-6 ">
                        <div class="gc_container">
                            <h5><strong>Payments </strong></h5>
                            <form method="post" class="giftCardForm">
                                <input type="hidden" name="isCreditSalesPayment" id="isCreditSalesPayment" value="0">
                                <input type="hidden" name="gc_creditCustomerID" id="gc_creditCustomerID" value="0">
                                <table class="<?php echo table_class_pos() ?>">
                                    <?php
                                    $payments = get_paymentMethods_GLConfig();
                                    //var_dump($payments);
                                    foreach ($payments as $payment) {
                                        if ($payment['glAccountType'] == 3) {
                                            if ($payment['autoID'] != 7) {
                                                continue;
                                            }
                                        }
                                        //openCreditSalesModal
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="row">
                                                    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                        <div style="padding:  7px 2px;"
                                                             class="tbl-payment-font-card">
                                                            <?php echo $payment['description']; ?>

                                                        </div>
                                                    </div>
                                                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                                                        <?php
                                                        if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                            ?>
                                                            <input type="text" value=""
                                                                   class="form-control ar numpad <?php if ($payment['autoID'] == 7) {
                                                                       echo ' cs_remarks';
                                                                   } ?>"
                                                                   name="reference[<?php echo $payment['ID'] ?>]"
                                                                   placeholder="Ref#"/>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>

                                            </td>
                                            <td> <!--style="width:50px;"-->
                                                <button class="btn btn-default btn-block" type="button"
                                                        style="padding: 0px;" <?php
                                                if ($payment['autoID'] == 7) {
                                                    echo 'onclick="openCreditSalesModal(' . $payment['ID'] . ')"';
                                                }
                                                ?>>
                                                    <img src="<?php echo base_url($payment['image']); ?>">
                                                </button>
                                            </td>
                                            <td class="payment-tblSize">
                                                <?php $tmpID = $payment['autoID'] == 1 ? 1 : $payment['ID']; ?>
                                                <input type="text"
                                                       name="paymentTypes[<?php echo $payment['ID'] ?>]"
                                                    <?php
                                                    if ($payment['autoID'] == 7) {
                                                        echo 'onclick="openCreditSalesModal(' . $payment['ID'] . ')" readonly';
                                                    } else {
                                                        echo 'onchange="calculatePaidAmount_Card()"';
                                                    }
                                                    ?>

                                                       class="form-control al payment-inputTextMediumCard paymentInputCard  <?php
                                                       if ($payment['glAccountType'] == 2 || $payment['glAccountType'] == 3) {
                                                           echo 'paymentOther';
                                                       }

                                                       if ($payment['autoID'] == 7) {
                                                           echo ' creditSalesAmount ';
                                                       } else {
                                                           echo ' numpad ';
                                                       }
                                                       ?>"
                                                       placeholder="0.00"
                                                       style="width: 100px;  text-align: right !important;">
                                                <input type="hidden"
                                                       name="glConfigMasters[<?php echo $payment['ID'] ?>]"
                                                       value="<?php echo $payment['autoID'] ?>">
                                            </td>
                                        </tr>


                                        <?php
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <div style="padding:  7px 2px; "
                                                 class="tbl-payment-font-card">
                                                <?php echo $this->lang->line('posr_paid_amount'); ?><!--Paid Amount--></div>
                                        </td>
                                        <td>&nbsp;</td>
                                        <td class="payment-tblSize">
                                            <input readonly type="number" name="paid" id="paidCard"
                                                   class="form-control payment-inputTextLg paymentTypeTextRed al"
                                                   placeholder="0.00"
                                                   autocomplete="off"
                                                   style="width: 100px; text-align: right !important;">
                                            <span id="paid_temp" class="hide"></span></td>
                                    </tr>
                                </table>
                            </form>
                        </div>


                    </div>

                    <div class="col-xs-12 col-sm-12  col-md-6 col-lg-6 ">

                        <div class="gc_container">
                            <h5><strong> Issue Gift Card</strong></h5>
                            <form class="form-horizontal giftCardForm">
                                <input type="hidden" name="posCustomerAutoID" id="posCustomerAutoID" value="0">
                                <input type="hidden" name="cardIssueID" id="gc_cardIssueID" value="0">
                                <fieldset>

                                    <!--<header class="head-title">
                                        <h2>Card Detail</h2>
                                    </header>-->
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="gc_barcode">Bar Code</label>
                                        <div class="col-md-8">
                                            <input id="gc_barcode" name="barcode" type="text"
                                                   placeholder="swipe the card or type SN"
                                                   class="form-control input-md">

                                        </div>
                                    </div>
                                    <br>
                                    <header class="head-title">
                                        <h2>Customer Detail </h2>
                                        <span id="newCustomer"><i
                                                class="fa fa-star text-red"></i> New </span>
                                    </header>


                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="">
                                            Telephone</label>
                                        <div class="col-md-8">
                                            <input id="gc_customerTelephone" name="customerTelephone" type="text"
                                                   placeholder="Telephone"
                                                   class="form-control input-md touchEngKeyboard">
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for=""> Name</label>
                                        <div class="col-md-8">
                                            <input id="gc_CustomerName" name="CustomerName" type="text"
                                                   placeholder="Name"
                                                   class="form-control input-md touchEngKeyboard">
                                        </div>
                                    </div>

                                    <br>
                                    <header class="head-title">
                                        <h2>Balance</h2>
                                    </header>

                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="topUpAmount"> Amount</label>
                                        <div class="col-md-8">
                                            <input id="topUpAmount"
                                                   style="text-align: right; font-size: 17px; font-weight: 700; color: #424242;"
                                                   name="topUpAmount"
                                                   readonly type="text"
                                                   placeholder="Available Amount" class="form-control input-md">

                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?> </button>
            </div>
        </div>
    </div>
</div>


<!-- Gift Card Redeem -->
<div aria-hidden="true" role="dialog" id="pos_giftCardRedeem" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-credit-card" aria-hidden="true"></i> Redeem Payments
                    <span class="giftCardLoaderRedeem pull-right" style="display: none;">
                        <i class="fa fa-refresh fa-2x fa-spin"></i>
                    </span>
                </h4>

            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <button class="btn btn-lg btn-default giftCardBtnContainer" type="button"
                                onclick="loadCustomerCardDataRedeem()">
                            <i class="fa fa-money"></i> Check Balance
                        </button>
                        <button class="btn btn-lg btn-danger giftCardBtnContainer" type="button"
                                onclick="resetGiftCardFormRedeem()">
                            <i class="fa fa-eraser"></i> Clear
                        </button>
                        <button class="btn btn-lg btn-default giftCardBtnContainer" type="button"
                                onclick="loadHistoryModalRedeem()">
                            <i class="fa fa-history"></i> History
                        </button>

                        <button class="btn btn-lg btn-primary giftCardBtnContainer" type="button"
                                onclick="update_redeemAmount()">
                            <i class="fa fa-level-down" aria-hidden="true"></i> Redeem
                        </button>

                    </div>
                    <hr>
                </div>

                <div class="gc_container">
                    <h5><strong> Gift Card Detail </strong></h5>
                    <form class="form-horizontal">
                        <fieldset>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="gc_barcode">Bar Code</label>
                                <div class="col-md-8">
                                    <input id="rd_barcode" name="barcode" type="text"
                                           placeholder="swipe the card or type SN"
                                           class="form-control input-md">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label" for="gc_barcode">Expiry Date </label>
                                <div class="col-md-8">
                                    <input id="rd_expiryDate" name="expiryDate" type="text" readonly
                                           placeholder="Card Expiry"
                                           class="form-control input-md">
                                </div>
                            </div>
                            <br>
                            <header class="head-title">
                                <h2>Customer Detail </h2>
                            </header>


                            <div class="form-group">
                                <label class="col-md-4 control-label" for="gc_customerTelephone">
                                    Telephone</label>
                                <div class="col-md-8">
                                    <input id="rd_customerTelephone" readonly name="customerTelephone" type="text"
                                           placeholder="placeholder" class="form-control input-md">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label" for="gc_CustomerName"> Name</label>
                                <div class="col-md-8">
                                    <input id="rd_CustomerName" readonly name="CustomerName" type="text"
                                           placeholder="Name"
                                           class="form-control input-md">
                                </div>
                            </div>

                            <br>
                            <header class="head-title">
                                <h2>Amount</h2>
                            </header>

                            <div class="form-group">
                                <label class="col-md-4 control-label" for="topUpAmount">Available </label>
                                <div class="col-md-8">
                                    <input id="rd_topUpAmount"
                                           style="text-align: right; font-size: 17px; font-weight: 700; color: #424242;"
                                           name="topUpAmount"
                                           readonly type="text"
                                           placeholder="Available Amount" class="form-control input-md">
                                </div>
                            </div>
                            <div class="form-group" id="div_redeemAmount" style="display: none;">
                                <label class="col-md-4 control-label">Redeem </label>
                                <div class="col-md-8">
                                    <input id="rd_redeemAmount"
                                           style="text-align: right; font-size: 17px; font-weight: 700; color: #424242;"
                                           name="topUpAmount"
                                           type="text"
                                           placeholder="Redeem" class="form-control input-md numpad">
                                </div>
                            </div>

                        </fieldset>
                    </form>

                </div>

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?> </button>
            </div>
        </div>
    </div>
</div>

<!--History -->
<div aria-hidden="true" role="dialog" id="pos_giftCardHistory" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-history"></i> History of Gift Card Transaction
                    <span id="giftCardLoader" style="display: none;"
                          class="pull-right">
                        <i class="fa fa-refresh fa-2x fa-spin"></i>
                    </span>
                </h4>

            </div>
            <div class="modal-body">
                <table class="<?php echo table_class_pos(); ?>" id="giftCardPaymentHistory">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Time <i class="fa fa-clock-o"></i></th>
                        <th>Description</th>
                        <th>Outlet</th>
                        <th>Amount <i class="fa fa-money"></i></th>
                    </tr>
                    </thead>
                </table>


            </div>

            <div class="modal-footer" style="margin-top: 0px;">


                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?> </button>
            </div>
        </div>
    </div>
</div>


<!--GC Receipt  -->
<div aria-hidden="true" role="dialog" id="pos_giftCardBill" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog" style="width: 400px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    Receipt
                    <span style="display: none;"
                          class="pull-right giftCardLoader">
                        <i class="fa fa-refresh fa-2x fa-spin"></i>
                    </span>
                </h4>

            </div>
            <div class="modal-body">
                <div id="gc_receiptContainer">
                    &nbsp;
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default btn-block" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?> </button>
            </div>
        </div>
    </div>
</div>
<script>

    $(document).ready(function (e) {

        $('.touchEngKeyboard').mlKeyboard({
            layout: 'en_US'
        });

        $('#pos_giftCardModal').on('hidden.bs.modal', function () {
            resetGiftCardForm();
            clearAmounts();
        });

        $('#pos_giftCardRedeem').on('hidden.bs.modal', function () {
            resetGiftCardFormRedeem();
        });

        $("#gc_barcode").keyup(function (e) {
            var keyValue = e.which;
            console.log(keyValue);
            if (keyValue == 13) {
                loadCustomerCardData()
            }
        })

        $("#gc_customerTelephone").keyup(function (e) {
            var keyValue = e.which;
            console.log(keyValue);
            if (keyValue == 13) {
                load_customer_name_for_telephone_no($("#gc_customerTelephone").val());
            }
        })


        $("#rd_barcode").keyup(function (e) {
            var keyValue = e.which;
            if (keyValue == 13) {
                loadCustomerCardDataRedeem()
            }
        })

        $("#rd_redeemAmount").change(function (e) {
            var maxTopUp = parseFloat($("#rd_topUpAmount").val());
            var redeemAmount = parseFloat($("#rd_redeemAmount").val());
            if (maxTopUp < redeemAmount) {
                myAlert('e', "You can't redeem more than the available amount.");
                $("#rd_redeemAmount").val('');
            } else {
                //  $(".giftCardPayment").val(redeemAmount);
            }
        });


        $(document).ready(function () {
            $('#gc_barcode').bind("cut copy paste", function (e) {
                e.preventDefault();
            });
        });
    });

    /** Card Redeem */
    function openGiftCardRedeemModal() {
        $("#pos_giftCardRedeem").modal('show');
        setTimeout(function () {
            $("#rd_barcode").focus();
        }, 200);
    }

    function load_customer_name_for_telephone_no(telephone) {
        //
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {telephone: telephone},
            url: "<?php echo site_url('Pos_giftCard/load_customer_name_for_telephone_no'); ?>",
            beforeSend: function () {
                $("#giftCardLoader").show();
            },
            success: function (data) {
                $("#giftCardLoader").hide();
                if (data['error'] == 0) {
                    $("#gc_CustomerName").val(data.CustomerName)
                } else {
                    $("#gc_CustomerName").val('');
                    $("#gc_CustomerName").focus();
                    myAlert('i', 'Not Registered yet.')
                }
            }, error: function () {
                $("#giftCardLoader").hide();
                myAlert('e', 'An Error has occurred.')
            }
        });
    }

    function update_redeemAmount() {
        var tmp_redeemAmount = $("#rd_redeemAmount").val();
        if (tmp_redeemAmount > 0) {
            $(".giftCardPayment").val(tmp_redeemAmount);
            $(".gitCardRefNo").val($("#rd_barcode").val());
            $("#pos_giftCardRedeem").modal("hide");
            calculateReturn();
        }
    }

    function loadHistoryModal() {
        var barcode = $("#gc_barcode").val();
        $("#pos_giftCardHistory").modal('show');
        loadCardTransactionHistory(barcode);
    }

    function loadHistoryModalRedeem() {
        var barcode = $("#rd_barcode").val();
        $("#pos_giftCardHistory").modal('show');
        loadCardTransactionHistory(barcode);
    }

    function loadCardTransactionHistory(barCode) {
        $('#giftCardPaymentHistory').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_giftCard/loadHistoryGiftCard'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "cardTopUpID"},
                {"mData": "gc_date"},
                {"mData": "gc_time"},
                {"mData": "description"},
                {"mData": "gc_outlet"},
                {"mData": "gc_amount"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'barCode', 'value': barCode});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function topUpGiftCard() {
        var data = $(".giftCardForm").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Pos_giftCard/topUpGiftCard'); ?>",
            beforeSend: function () {
                $("#giftCardLoader").show();
            },
            success: function (data) {
                $("#giftCardLoader").hide();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    loadCustomerCardData();
                    clearAmounts();
                    if (data['receiptID'] > 0) {
                        load_giftCard_receipt(data['receiptID'], data['barcode']);
                    }
                    clearCreditSales();
                } else {
                    myAlert('e', data['message']);
                    //$("#newCustomer").show();
                }
            }, error: function () {
                $("#giftCardLoader").hide();
                myAlert('e', 'An Error has occurred.')
            }
        });
    }

    function issueGiftCard() {
        var data = $(".giftCardForm").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Pos_giftCard/issueGiftCard'); ?>",
            beforeSend: function () {
                $("#giftCardLoader").show();
            },
            success: function (data) {
                $("#giftCardLoader").hide();
                if (data['error'] == 0) {
                    $("#gc_customerTelephone").val(data['customerTelephone']);
                    $("#gc_CustomerName").val(data['CustomerName']);
                    $("#topUpAmount").val(parseFloat(data['balanceAmount']).toFixed(2));
                    $("#posCustomerAutoID").val(data['posCustomerAutoID']);
                    $("#gc_cardIssueID").val(data['cardIssueID']);
                    if (data['posCustomerAutoID'] == 0) {
                        $("#newCustomer").show();
                    } else {
                        $("#newCustomer").hide();
                    }
                    loadCustomerCardData();
                    clearAmounts();
                    if (data['receiptID'] > 0) {
                        load_giftCard_receipt(data['receiptID'], data['barcode']);
                    }
                } else {
                    $("#newCustomer").show();
                    myAlert('e', data['message'])
                }
            }, error: function () {
                $("#giftCardLoader").hide();
                myAlert('e', 'An Error has occurred.')
            }
        });
    }

    function loadCustomerCardData() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {barcode: $("#gc_barcode").val()},
            url: "<?php echo site_url('Pos_giftCard/loadCustomerCardData'); ?>",
            beforeSend: function () {
                $("#giftCardLoader").show();
            },
            success: function (data) {
                $("#giftCardLoader").hide();
                if (data['error'] == 0) {

                    $("#gc_customerTelephone").val(data['customerTelephone']);
                    $("#gc_CustomerName").val(data['CustomerName']);
                    $("#topUpAmount").val(parseFloat(data['balanceAmount']).toFixed(2));
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

            }, error: function () {
                $("#giftCardLoader").hide();
                myAlert('e', 'An Error has occurred.')
            }
        });
    }

    function loadCustomerCardDataRedeem() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {barcode: $("#rd_barcode").val()},
            url: "<?php echo site_url('Pos_giftCard/loadCustomerCardData'); ?>",
            beforeSend: function () {
                $(".giftCardLoaderRedeem").show();
            },
            success: function (data) {
                $(".giftCardLoaderRedeem").hide();
                if (data['error'] == 0) {
                    $("#rd_customerTelephone").val(data['customerTelephone']);
                    $("#rd_CustomerName").val(data['CustomerName']);
                    $("#rd_topUpAmount").val(parseFloat(data['balanceAmount']).toFixed(2));
                    var availableBalance = parseFloat(data['balanceAmount']);
                    $("#rd_posCustomerAutoID").val(data['posCustomerAutoID']);
                    $("#rd_expiryDate").val(data['expiryDate']);
                    if (data['isCardExpired'] == 1) {
                        $("#rd_expiryDate").css("font-weight", "700");
                        $("#rd_expiryDate").css("color", "#EC5447");
                        $("#div_redeemAmount").hide();
                    } else {
                        $("#rd_expiryDate").css("font-weight", "");
                        $("#rd_expiryDate").css("color", "");
                        $("#div_redeemAmount").show();
                        var netTotalAmount = parseFloat($("#final_payableNet_amt").text());
                        if (availableBalance > netTotalAmount) {
                            $("#rd_redeemAmount").val(netTotalAmount);
                        } else {
                            $("#rd_redeemAmount").val(availableBalance);
                        }

                    }
                } else {
                    myAlert('e', data['message']);
                }

            }, error: function () {
                $(".giftCardLoaderRedeem").hide();
                myAlert('e', 'An Error has occurred.')
            }
        });
    }

    function open_giftCardModal() {
        $("#pos_giftCardModal").modal('show');
        setTimeout(function () {
            $("#gc_barcode").focus();
        }, 500);
    }

    function resetGiftCardForm() {

        $(".giftCardForm")[0].reset()
        $("#gc_barcode").val('');
        $("#gc_telephone").val('');
        $("#gc_posCustomerAutoID").val('');
        $("#topUpAmount").val(0.00);
        $("#newCustomer").show();
        $("#posCustomerAutoID").val(0);
        $("#gc_cardIssueID").val(0);
        $("#gc_customerTelephone").val('');
        $("#gc_CustomerName").val('');
        $(".giftCardPayment").val('');
        $(".gitCardRefNo").val('');
        clearCreditSales();
    }

    function resetGiftCardFormRedeem() {
        $("#rd_barcode").val('');
        $("#rd_telephone").val('');
        $("#rd_customerTelephone").val('');
        $("#rd_CustomerName").val('');
        $("#rd_expiryDate").val('');
        $("#rd_topUpAmount").val('');
        $("#rd_redeemAmount").val('');
        $("#rd_barcode").focus();
        $("#div_redeemAmount").hide();
        calculateReturn();
    }

    function calculatePaidAmount_Card() {
        var total = 0;
        $(".paymentInputCard").each(function (e) {
            var valueThis = $.trim($(this).val());
            total += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;
        });
        $("#paidCard").val(total);
    }

    function clearAmounts() {
        $(".paymentInputCard").val('');
        $("#paidCard").val('');
    }

    function load_giftCard_receipt(receiptID, barcode) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {receiptID: receiptID, barcode: barcode},
            url: "<?php echo site_url('Pos_giftCard/load_giftCard_receipt'); ?>",
            beforeSend: function () {
                $(".giftCardLoader").show();
                $("#gc_receiptContainer").html('');
                $("#pos_giftCardBill").modal('show');

            },
            success: function (data) {
                $(".giftCardLoader").hide();
                $("#gc_receiptContainer").html(data);

            }, error: function () {
                $(".giftCardLoader").hide();
                myAlert('e', 'An Error has occurred.')
            }
        });
    }


</script>