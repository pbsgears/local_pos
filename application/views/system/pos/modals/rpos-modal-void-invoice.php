<?php
/**
 * --- Created on 7-MAR-2017 by Mushtaq Ahamed
 * --- POS Open void Receipt Modal Window
 */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>


<div aria-hidden="true" role="dialog" tabindex="-1" id="pos_open_void_receipt" class="modal fade" data-keyboard="true"
     data-backdrop="static" >
    <div class="modal-dialog modal-lg" >
        <div class="modal-content" style="min-height: 600px;">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('posr_closed_bills');?><!--Closed Bills--> </h4>
            </div>
            <div id="voidReceipt" class="modal-body" style="overflow: visible; background-color: #FFF; min-height: 100px;" onclick="">

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script>
    function open_void_Modal() {
        var id=0;
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/load_void_receipt'); ?>",
            data: {id: id},
            cache: false,
            beforeSend: function () {
                $("#pos_open_void_receipt").modal('show');
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#voidReceipt").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

</script>