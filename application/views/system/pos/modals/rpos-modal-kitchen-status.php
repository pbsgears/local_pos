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
<div aria-hidden="true" role="dialog" id="pos_open_kitchenStatus" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><i class="fa fa-cutlery"></i> Kitchen </h4>
            </div>
            <div id="kitchenStatus_modalBody" class="modal-body"
                 style="overflow: visible; background-color: #FFF; min-height: 100px;" onclick="">

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<!--PREVIEW KITCHEN STATUS -->
<div aria-hidden="true" role="dialog" id="pos_kitchenOrderStatus" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                <h5 class="modal-title"><i class="fa fa-cutlery"></i> Kitchen Status </h5>
            </div>
            <div id="kitchen_order_modalBody" class="modal-body">

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="kot_print_box_layout_modal" class="modal fade" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-responsive-bill">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <h4 class="modal-title"><i class="fa fa-print"></i> KOT </h4>
            </div>
            <div class="modal-body" id="kot_print_box_layout_modal_body">

            </div>

            <div class="modal-footer" style="margin-top: 0px;">


                <button type="button" class="btn btn-default btn-lg" data-dismiss="modal"
                        style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center; color:black; 10px 1px; margin: 5px auto 10px auto; font-weight:bold;  border-radius: 0px;">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<script>

    function open_kitchen_ready() {
        <?php
        if (isset($tablet) && $tablet) {
            $method = 'load_kitchen_ready_tablet';
        } else {
            $method = 'load_kitchen_ready';
        }
        ?>
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_kitchen/' . $method); ?>",
            data: $("#frm_POS_holdReceipt").serialize(),
            cache: false,
            beforeSend: function () {
                startLoad();
                $("#pos_open_kitchenStatus").modal("show");
                $("#kitchenStatus_modalBody").html('');
            },
            success: function (data) {
                stopLoad();
                $("#kitchenStatus_modalBody").html(data);
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

    function loadKitchenStatusPreview(invoiceID) {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_kitchen/loadKitchenStatusPreview'); ?>",
            data: {invoiceID: invoiceID},
            cache: false,
            beforeSend: function () {
                $("#pos_kitchenOrderStatus").modal('show');
                $("#kitchen_order_modalBody").html('<div class="text-center"> <i class="fa fa-refresh fa-spin"></i> Loading</div>');
            },
            success: function (data) {
                stopLoad();
                $("#kitchen_order_modalBody").html(data)
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == false) {
                    myAlert('w', 'Local Server is Offline, Please try again');
                } else {
                    $("#kitchen_order_modalBody").html('<div class="text-red"> Error: ' + errorThrown + '</div>')
                }

            }
        });
    }

    function load_KOT_print_view(invoiceID) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_kitchen/load_KOT_print_view'); //load_KOT_print_view_pdf | load_KOT_print_view ?>",
            data: {menuSalesID: invoiceID},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    //myAlert('s', data['message']);
                    if (data['auth'] == 0) {
                        $('#kot_print_box_layout_modal').modal('show');
                        $("#kot_print_box_layout_modal_body").html(data['html']);
                    }
                } else if (data['error'] == 1) {
                    myAlert('e', data['message']);
                    if (data['auth'] == 0) {
                        $('#kot_print_box_layout_modal').modal('show');
                        $("#kot_print_box_layout_modal_body").html(data['html']);
                    }
                } else if (data['error'] == 2) {
                    //myAlert('i', data['message']);
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
            }
        });
    }


</script>