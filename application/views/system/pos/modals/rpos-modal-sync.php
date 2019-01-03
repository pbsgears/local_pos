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
<div class="modal" id="pos_sync_modal" data-backdrop="static" role="dialog" aria-labelledby="Sync Modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-close text-red"></i>
                </button>
                <h4 class="modal-title"> Syncing <i class="fa fa-cloud-download" aria-hidden="true"></i></h4>
            </div>
            <div class="modal-body">


                <?php
                $isLocalPOSEnabled = isLocalPOSEnabled();
                if ($isLocalPOSEnabled) {
                    ?>
                    <button class="btn btn-block btn-lg btn-default" type="button"
                            onclick="pullFromLive_giftCard()">
                        <i class="fa fa-cloud-download"></i> Pull Gift Card Data
                    </button>
                <?php } ?>

                <hr>

                <?php
                $isLocalPOSEnabled = isLocalPOSEnabled();
                if ($isLocalPOSEnabled) {
                    ?>
                    <button type="button" onclick="updateLiveTables()"
                            class="btn btn-block btn-lg btn-default">
                        <i class="fa fa-cloud-download" aria-hidden="true"></i>
                        Pull Full Data
                    </button>
                <?php } ?>
            </div>
            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?> </button>
            </div>
        </div>
    </div>
</div>
<script>
    function openSyncModal() {
        $("#pos_sync_modal").modal('show');
    }

    function pullFromLive_giftCard() {
        swal({
                title: "Sync Gift Card Data",
                text: "This process will take approximately 1-3 minutes depending on the internet bandwidth \n \n This process will pull data from live database to local system",
                showCancelButton: true,
                confirmButtonColor: "#f39c12",
                icon: "success",
                confirmButtonText: "Sync Now",
                closeOnConfirm: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: "<?php echo site_url('sync/pull_data_giftCard'); ?>",
                        cache: false,
                        beforeSend: function () {
                            startLoad();
                            $("#holdon-overlay").css("background", "rgba(208, 194, 233, 0.68)").delay(3000);
                        },
                        success: function (data) {
                            stopLoad();
                            if (data['error'] == 0) {
                                bootbox.alert('<div class="alert alert-success">\n' +
                                    '    <strong>' + data.message + ' </strong><br/><br/> System is refreshing in while, please wait... \n' +
                                    '</div>');
                                setTimeout(function () {
                                    location.reload();
                                }, 3000);

                            } else {
                                myAlert('d', data.message)
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
            });
    }
</script>