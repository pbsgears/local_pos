<?php
/**
 * --- Created on 23-May-2018 by Shafri
 * --- CCTV Camera Loading
 */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<style>
    .cctv_container {
        max-height: 300px !important;
        padding: 5px !important;
    }
</style>
<div class="modal" id="pos_cctv_modal" data-backdrop="static" role="dialog" aria-labelledby="CCTV Modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-close text-red"></i>
                </button>
                <h4 class="modal-title"> Camera Feed <i class="fa fa-video-camera" aria-hidden="true"></i></h4>
            </div>
            <div class="modal-body" style="min-height: 200px;">
                <div class="row">
                    <div id="cctv_feed">
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

<script>
    $(document).ready(function (e) {
        $('#pos_cctv_modal').on('hidden.bs.modal', function () {
            $('#cctv_feed').html('');
        })
    });

    function open_cctv_modal() {
        loadCCTV_feed();
    }

    function loadCCTV_feed() {
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_cameraSetup/get_cctv_feed'); ?>",
            data: {},
            cache: false,
            beforeSend: function () {
                startLoad();
                $("#pos_cctv_modal").modal('show');
                $('#cctv_feed').html('');
            },
            success: function (data) {
                stopLoad();
                if (data.length > 0) {
                    $('#cctv_feed').append('<div class="row">');
                    $.each(data, function (key, value) {
                        $('#cctv_feed').append('<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><div class="cctv_container"><div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="' + value.url_host + '"></iframe></div></div></div>');
                    });
                    $('#cctv_feed').append('</div>');
                } else {
                    $('#cctv_feed').append('<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><div class="well text-center" style="border-radius: 0px; "> <i class="fa fa-ban text-red" aria-hidden="true"></i> Sorry Currently there is no video feed configured.<br/> <i class="fa fa-video-camera fa-5x text-gray" aria-hidden="true"></i> </div></div>');
                }


            }, error: function () {
                stopLoad();
                myAlert('e', 'Error in loading currency denominations.')
            }
        });
    }
</script>