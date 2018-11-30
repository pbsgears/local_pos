<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div aria-hidden="true" role="dialog" tabindex="2" id="rpos_print_template" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-responsive-bill"  >
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_print'); ?><!--Print--> </h4>
            </div>
            <div class="modal-body modal-responsive-bill" id="pos_modalBody_posPrint_template" >

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                        style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center; color:#FFF; background-color:#005b8a; border:0px solid #007FFF; padding: 10px 1px; margin: 5px auto 10px auto; font-weight:bold;" onclick="focus_barcode()">
                    <i class="fa fa-angle-double-left" aria-hidden="true"></i>
                    <?php echo $this->lang->line('posr_back_to_pos_create_new'); ?><!--Back to POS & Create New-->
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    function loadPrintTemplate(invoiceID) {
        if (invoiceID > 0) {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: "<?php echo site_url('Pos_restaurant/loadPrintTemplate'); ?>",
                data: {invoiceID: invoiceID},
                cache: false,
                beforeSend: function () {
                    $("#rpos_print_template").modal('show');
                    startLoadPos();
                    $("#pos_modalBody_posPrint_template").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_print_view');?></div>');
                    <!--Loading Print view-->
                },
                success: function (data) {

                    stopLoad();
                    $("#pos_modalBody_posPrint_template").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        } else {
            myAlert('e', 'Please select an invoice to print!');
        }

    }


</script>