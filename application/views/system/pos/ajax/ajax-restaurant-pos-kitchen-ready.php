<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>

<div>

    <div class="row kitchen-status-container">
        <div class="col-xs-6 col-sm-3 col-md-3 col-lg-2">
            <i class="fa fa-stop text-green"></i> Sent To KOT
        </div>
        <div class="col-xs-6 col-sm-3 col-md-3 col-lg-2">
            <i class="fa fa-stop text-red"></i> Not Sent To KOT
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
            <h5 style="margin: 2px;">
                <span
                    class="label label-warning"><?php echo $this->lang->line('common_pending'); ?><!--Pending--></span>
            </h5>
        </div>

        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
            <h5 style="margin: 2px;">
                <span
                    class="label label-info"><?php echo $this->lang->line('common_processing'); ?><!--Processing--> </span>
            </h5>
        </div>

        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
            <h5 style="margin: 2px;">
                <span
                    class="label label-success"><?php echo $this->lang->line('posr_completed'); ?><!--Completed--></span>
            </h5>
        </div>
    </div>

</div>
<div class="table-responsive" style="background-color: #ffffff; padding:10px;">
    <table class="<?php echo table_class_pos(5) ?>" id="table_kitchenStatus" style="width: 100%">
        <thead>
        <tr>
            <th> #</th>
            <th>Bill No</th>
            <th> Date</th>
            <th> <?php echo $this->lang->line('posr_hold_remarks'); ?><!--Hold Remarks--></th>
            <th> KOT <i class="fa fa-cutlery"></i></th>
            <th>
                <div class="text-yellow">
                    <span class="hidden-xs">Pending div </span>
                    <span class="hidden-sm hidden-md hidden-lg">
                        <i class="fa fa-stop text-yellow" aria-hidden="true"></i>
                    </span>
                </div>
            </th>
            <th>
                <div class="text-blue">
                    <span class="hidden-xs"> Processing </span>
                    <span
                        class="hidden-sm hidden-md hidden-lg">
                        <i class="fa fa-stop text-yellow text-blue"
                           aria-hidden="true"></i>
                    </span>
                </div>
            </th>
            <th>
                <div class="text-green">
                    <span class="hidden-xs"> Completed </span>
                    <span class="hidden-sm hidden-md hidden-lg">
                        <i class="fa fa-stop text-green" aria-hidden="true"></i>
                    </span>
                </div>
            </th>
            <th> &nbsp; </th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<script>

    $(document).ready(function (e) {

    });


    var DT_kitchenStatus = $('#table_kitchenStatus').DataTable({
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "sAjaxSource": "<?php echo site_url('Pos_kitchen/loadKitchenReady'); ?>",
        "aaSorting": [[0, 'asc']],
        /*"fnDrawCallback": function (oSettings) {
         if (oSettings.bSorted || oSettings.bFiltered) {
         for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
         $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
         }
         }
         },*/
        "aoColumns": [
            {"mData": "invoiceSequenceNo"},
            {"mData": "invoiceID"},
            {"mData": "createdDate"},
            {"mData": "remarks"},
            {"mData": "sendKOT"},
            {"mData": "isPending"},
            {"mData": "isInProgress"},
            {"mData": "isCompleted"},
            {"mData": "openHold"}
        ],
        // "columnDefs": [{
        //     "targets": [5],
        //     "orderable": false
        // }],
        "fnServerData": function (sSource, aoData, fnCallback) {
            $.ajax
            ({
                'dataType': 'json',
                'type': 'POST',
                'url': sSource,
                'data': aoData,
                'success': fnCallback
            });
        }
    });

    function load_kitchen_status() {
        DT_kitchenStatus.ajax.reload();
    }

</script>