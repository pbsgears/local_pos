<?php
/**
 * --- Created on 30-October-2017 by Mohamed Shafri
 * --- Java App SMS process
 */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>

<!-- Credit Sales  -->
<div aria-hidden="true" role="dialog" id="pos_javaAppModal" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <img src="<?php echo base_url('images/payment_type/25.png'); ?>" alt="Ico"> Java App
                    <span class="creditSalesLoaderRedeem pull-right" style="display: none;">
                        <i class="fa fa-refresh fa-2x fa-spin"></i>
                    </span>
                </h4>

            </div>
            <div class="modal-body" style="min-height: 100px;">
                <form class="form-horizontal" method="post" id="jApp_form">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                        <input type="hidden" id="jApp_paymentConfigDetailID" value="0">
                        <!--<legend>Java App</legend>-->


                        <div class="form-group">
                            <label class="col-md-4 control-label" for="jApp_pin">Java App PIN Code </label>
                            <div class="col-md-4">
                                <input type="text" id="jApp_appPIN" name="appPIN"
                                       class="form-control ar amountStyle numPad" placeholder="PIN Number"
                                       onchange="checkJavaApp()">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="jApp_amount">Amount</label>
                            <div class="col-md-4">
                                <input type="text" id="jApp_amount" name="amount" readonly
                                       class="form-control ar amountStyle" placeholder="0.00">
                            </div>
                        </div>


                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?> </button>
                <button type="button" class="btn btn-lg btn-danger" onclick="clearJavaApp()"><i
                        class="fa fa-eraser"></i> Clear
                </button>
                <button type="button" class="btn btn-lg btn-primary" onclick="checkPosAuthentication(8)"><i
                        class="fa fa-angle-down"></i> Ok
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    /** java App */
    function checkJavaApp() {
        var data = $('#jApp_form').serializeArray();
        data.push({"name": 'netAmount', "value": parseFloat($("#final_payableNet_amt").text())});
        data.push({"name": 'paid', "value": $("#paid").val()});
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_javaApp/checkJavaApp'); ?>",
            data: data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 2) {
                    swal("PIN Used", data['message'], "error");
                } else if (data['error'] == 0) {
                    $("#jApp_amount").val(data['redeemAmount']);
                } else {
                    $("#jApp_amount").val(0);
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Error: JS ERROR');
            }
        });
    }

    /*function setCreditCustomerBtn(posCustomerId, customerAutoID) {
     $(".btn-creditCustomer").removeClass('btn-primary');
     $(".btn-creditCustomer").addClass('btn-default');
     $("#btn_customer" + posCustomerId).removeClass('btn-default');
     $("#btn_customer" + posCustomerId).addClass('btn-primary');

     var glConfigID = $("#jApp_paymentConfigDetailID").val();
     $("#customerAutoID" + glConfigID).val(customerAutoID);
     }*/


    function javaApp_submit() {
        var amount = $("#jApp_amount").val();
        if (amount > 0) {
            $(".javaAppRefNo").val($("#jApp_appPIN").val());
            $(".javaAppPayment").val(amount);
            $("#pos_javaAppModal").modal('hide');
            calculateReturn();
        } else {
            myAlert('e', 'Amount should not be zero!');
        }
    }

    function clearJavaApp() {
        $(".javaAppRefNo").val('');
        $(".javaAppPayment").val('');
        $("#jApp_appPIN").val(0);
        $("#jApp_amount").val(0);
        $("#pos_javaAppModal").modal('hide');
        calculateReturn();

    }

    function openJavaAppModal(paymentConfigDetailID) {
        $("#jApp_paymentConfigDetailID").val(paymentConfigDetailID);
        $("#pos_javaAppModal").modal('show');
    }


    $(document).ready(function (e) {
        $('#pos_javaAppModal').on('hidden.bs.modal', function () {
            $("#jApp_appPIN").val('');
            $("#jApp_amount").val('');
        });
        $(".select2").select2();
    });


</script>