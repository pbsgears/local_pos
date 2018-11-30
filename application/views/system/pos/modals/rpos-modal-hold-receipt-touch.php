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
<div aria-hidden="true" role="dialog" tabindex="-1" id="pos_open_hold_receipt" class="modal fade" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-responsive-bill"  >
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('posr_open_bills');?><!--Open Bills--> </h4>
            </div>
            <div id="modal_body_pos_openHoldReceipt" class="modal-body"
                 style="overflow: visible; background-color: #FFF; min-height: 100px;" onclick="">

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                &nbsp;
            </div>
        </div>
    </div>
</div>

<?php
/**
 * --- POS Hold Receipt Modal Window
 */
?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="pos_hold_receipt_modal" class="modal fade" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('posr_hold_bills');?><!--Hold Receipt--> </h4>
            </div>
            <div class="modal-body" style="overflow: visible; background-color: #FFF; min-height: 100px;"
                 id="modal_body_hold">
                <form class="form-horizontal" id="frm_POS_holdReceipt">
                    <input type="hidden" name="holdInvoiceID_input" id="holdInvoiceID_input" value="0">
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="holdInvoiceID"><?php echo $this->lang->line('posr_invoice_id');?><!--Invoice ID--></label>
                        <div class="col-md-4">
                            <input id="holdInvoiceID" name="invoiceID" readonly type="text" placeholder="Invoice ID"
                                   class="form-control input-md">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="holdReference"><?php echo $this->lang->line('posr_hold_reference');?><!--Hold Reference--> </label>
                        <div class="col-md-8">
                            <input type="text" id="holdReference" name="holdReference" placeholder="<?php echo $this->lang->line('posr_hold_reference');?>"
                                   class="form-control input-md"><!--Type Hold Reference-->
                        </div>
                    </div>

                    <div class="modal-footer" style="margin-top: 10px;">
                        <input type="button" name="hold_bill_submit" id="hold_bill_submit" value="Submit"
                               onclick="submitHoldReceipt()"
                               class="btn btn-primary"
                               style="background-color: #3fb618; color: #FFF; border: 0px; padding: 5px 25px; float: right;">
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    function holdReceipt() {
        $("#pos_hold_receipt_modal").modal('show');
        setTimeout(function () {
            $("#holdReference").focus();
            $("#holdReference").val('')
        }, 800);
        $("#pos_hold_receipt_modal").keyup(function (e) {
            if (e.keyCode == 13) {
                submitHoldReceipt();
            }
        })
    }
    function submitHoldReceipt() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/submitHoldReceipt'); ?>",
            data: $("#frm_POS_holdReceipt").serialize(),
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
                } else {
                    myAlert('d', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function open_holdReceipt() {
        load_pos_hold_receipt()
    }



    function load_pos_hold_receipt() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/load_pos_hold_receipt'); ?>",
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
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_kitchen_ready() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/load_kitchen_ready'); ?>",
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
                myAlert('e', '<br>Message: ' + errorThrown);
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
                    bootbox.alert('<div class="alert alert-info"><?php echo $this->lang->line('posr_opening_invoice_code');?>: ' + data['code'] + '</div>');<!--Opening Invoice Code-->
                    $("#pos_salesInvoiceID_btn").html(data['code']);
                    checkPosSession();
                } else {
                    myAlert('e', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }
</script>