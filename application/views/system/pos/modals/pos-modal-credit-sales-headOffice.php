<?php
/**
 * --- Created on 27-October-2017 by Mohamed Shafri
 * --- Gift Card Process
 */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>

<!-- Credit Sales  -->
<div aria-hidden="true" role="dialog" id="pos_creditSalesModal" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <img src="<?php echo base_url('images/payment_type/7.png'); ?>" alt="Ico"> Credit Sales
                    <span class="creditSalesLoaderRedeem pull-right" style="display: none;">
                        <i class="fa fa-refresh fa-2x fa-spin"></i>
                    </span>
                </h4>

            </div>
            <div class="modal-body">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <form class="form-horizontal" method="post">
                        <input type="hidden" id="cs_paymentConfigDetailID" value="0">
                        <legend>Credit Customer List</legend>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="creditPaymentAmountTmp">Top up
                                        Amount</label>
                                    <div class="col-md-4">
                                        <input id="creditPaymentAmountTmp" name="creditPaymentAmount" type="text"
                                               placeholder="Amount"
                                               class="form-control input-md numpad">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <?php $cr_customers = get_creditCustomers(); ?>
                            <?php if (!empty($cr_customers)) {
                                foreach ($cr_customers as $cr_customer) {
                                    ?>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="cs-btn-container">
                                            <button
                                                type="button"
                                                class="btn btn-block btn-default btn-lg btn-creditCustomer"
                                                id="btn_customer<?php echo $cr_customer['posCustomerAutoID'] ?>"
                                                onclick="setCreditCustomerBtn(<?php echo $cr_customer['posCustomerAutoID'] ?>, <?php echo $cr_customer['CustomerAutoID'] ?>)"><?php echo $cr_customer['CustomerName'] ?></button>
                                        </div>
                                    </div>

                                    <?php
                                }
                            } ?></div>
                        <div class="form-group hide">
                            <label class="col-md-4 control-label" for="creditCustomerID">Select Customer </label>
                            <div class="col-md-7">


                                <select id="cs_creditCustomerID" name="creditCustomerID"
                                        onchange="creditSalesLoadNetPayment()"
                                        class="form-control select2 hide">
                                    <option value="">Please select</option>
                                    <?php if (!empty($cr_customers)) {
                                        foreach ($cr_customers as $cr_customer) {
                                            ?>
                                            <option value="<?php echo $cr_customer['posCustomerAutoID'] ?>"
                                                    data-customerID="<?php echo $cr_customer['CustomerAutoID'] ?>"><?php echo $cr_customer['CustomerName'] ?></option><?php
                                        }
                                    } ?>
                                </select>
                            </div>
                        </div>


                    </form>

                </div>

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?> </button>
                <button type="button" class="btn btn-lg btn-danger" onclick="clearCreditSales()"><i
                        class="fa fa-eraser"></i> Clear
                </button>
                <button type="button" class="btn btn-lg btn-primary" onclick="creditSales_submit()"><i
                        class="fa fa-angle-down"></i> Ok
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    /** Credit Sales */
    function setCreditCustomerBtn(posCustomerId, customerAutoID, customerName) {
        $(".btn-creditCustomer").removeClass('btn-primary');
        $(".btn-creditCustomer").addClass('btn-default');
        $("#btn_customer" + posCustomerId).removeClass('btn-default');
        $("#btn_customer" + posCustomerId).addClass('btn-primary');
        $("#cs_creditCustomerID").val(posCustomerId).change();
        var glConfigID = $("#cs_paymentConfigDetailID").val();
        $("#customerAutoID" + glConfigID).val(customerAutoID);
        $("#gc_creditCustomerID").val(customerAutoID);
    }


    function creditSales_submit() {
        var customerID = $("#cs_creditCustomerID option:selected").val();
        var erp_customerID = $("#cs_creditCustomerID").find(':selected').attr('data-customerID');
        if (customerID > 0) {
            $(".cs_remarks").val('CS-' + customerID + '-' + erp_customerID);
            //$(".creditSalesPayment").val(amount);
            $("#pos_creditSalesModal").modal('hide')
            $("#isCreditSale").val(1);
            $("#isCreditSalesPayment").val(1);
            //$("#gc_creditCustomerID").val(customerID);

            $(".creditSalesAmount").val($("#creditPaymentAmountTmp").val());
        } else {
            myAlert('e', 'Please select the customer!');
        }
    }

    function clearCreditSales() {
        $(".cs_remarks").val('');
        $("#isCreditSalesPayment").val(0);
        $("#gc_creditCustomerID").val(0);
        $("#isCreditSale").val(0);
        $(".CreditSalesRefNo").val('');
        $(".creditSalesPayment").val('');
        $("#cs_creditCustomerID").val('').change();
        $(".btn-creditCustomer").removeClass('btn-primary');
        $(".btn-creditCustomer").addClass('btn-default');
        $("#pos_creditSalesModal").modal('hide');
        $(".creditSalesAmount").val('');
        $("#creditPaymentAmountTmp").val('');

    }

    function openCreditSalesModal(paymentConfigDetailID) {
        $("#cs_paymentConfigDetailID").val(paymentConfigDetailID);
        $("#pos_creditSalesModal").modal('show');
    }


    $(document).ready(function (e) {
        $('#pos_creditSalesModal').on('hidden.bs.modal', function () {

        });
        $(".select2").select2();
    });

    function creditSalesLoadNetPayment() {
        var netAmount = parseFloat($("#final_payableNet_amt").text());
        $("#cs_amount").val(netAmount);
    }


    /*function open_creditSalesModal() {
     $("#pos_giftCardModal").modal('show');
     setTimeout(function () {
     $("#gc_barcode").focus();
     }, 500);
     }*/


</script>