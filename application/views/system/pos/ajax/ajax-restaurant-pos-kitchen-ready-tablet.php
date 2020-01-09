<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>

<div>

    <table class="table table-hover table-condensed">
        <tr>
            <td><i class="fa fa-stop text-green"></i> Sent To KOT</td>
            <td><i class="fa fa-stop text-red"></i> Not Sent To KOT</td>
            <td> &nbsp; </td>
            <td class="text-center">
                <h5 style="margin: 2px;">
                    <span class="label label-warning"><?php echo $this->lang->line('common_pending'); ?><!--Pending--></span>
                </h5>
            </td>
            <td class="text-center">
                <h5 style="margin: 2px;">
                    <span class="label label-info"><?php echo $this->lang->line('common_processing'); ?><!--Processing--> </span>
                </h5>
            </td>
            <td class="text-center">
                <h5 style="margin: 2px;">
                    <span class="label label-success"><?php echo $this->lang->line('posr_completed'); ?><!--Completed--></span>
                </h5>
            </td>

        </tr>
    </table>
</div>
<div class="table-responsive" style="background-color: #ffffff; padding:10px;">
    <table class="<?php echo table_class_pos(5) ?>" id="table_kitchenStatus">
        <thead>
        <tr>
            <th> #</th>
            <th>Bill No</th>
            <th> Date</th>
            <th> <?php echo $this->lang->line('posr_hold_remarks'); ?><!--Hold Remarks--></th>
            <th> KOT <i class="fa fa-cutlery"></i></th>
            <th><span class="text-yellow">Pending </span></th>
            <th><span class="text-blue">Processing </span></th>
            <th><span class="text-green">Completed </span></th>
            <th> &nbsp; </th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<script>
    var DT_kitchenStatus = $('#table_kitchenStatus').DataTable({
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "sAjaxSource": "<?php echo site_url('Pos_kitchen/loadKitchenReady_tablet'); ?>",
        "aaSorting": [[0, 'asc']],
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
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({"name": "BOT", "value": 1});
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