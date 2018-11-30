<style>
    .modal_customWidth {
        width: 70%;
    }
</style>
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

?>


<div aria-hidden="true" role="dialog" tabindex="-1" id="rpos_packInvoice" class="modal fade" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog"
         style="width: <?php echo !isset($modal_width) && !empty($modal_width) ? $modal_width . '%' : '70%'; ?>;">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('posr_pack_detail');?><!--Pack Detail--> </h4>
            </div>

            <div class="modal-body" id="pos_modalBody_packInvoice" style="min-height: 400px;"></div>

            <div class="modal-footer" style="margin-top: 0px;">
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                                style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center; padding: 10px 1px; margin: 5px auto 10px auto; font-weight:bold;">
                            <?php echo $this->lang->line('common_Close');?><!--Close-->
                        </button>
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <button type="button" class="btn btn-default btn-sm" onclick="savePackDetail()"
                                style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center; color:#FFF; background-color:#005b8a; border:0px solid #007FFF; padding: 10px 1px; margin: 5px auto 10px auto; font-weight:bold;">
                            <?php echo $this->lang->line('common_add');?><!--Add-->
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function load_packItemList(id) {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/load_packItemList'); ?>",
            data: {id: id},
            cache: false,
            beforeSend: function () {
                $("#rpos_packInvoice").modal('show');
                startLoadPos();
                $("#pos_modalBody_packInvoice").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  <?php echo $this->lang->line('posr_loading_pack_detail');?></div>');<!--Loading Pack detail-->
            },
            success: function (data) {
                stopLoad();
                $("#pos_modalBody_packInvoice").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
</script>