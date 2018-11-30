<style>
    .form-inline {
        background: rgb(255, 255, 255) !important;
        margin-bottom: 0px !important;
        border-bottom: 2px solid rgb(241, 241, 241) !important;
        box-shadow: none !important;
        padding: 10px !important;
    }
</style>
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div>

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#home" aria-controls="home" role="tab" data-toggle="tab">
                <i class="fa fa-list"></i> My Bills
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="home">
            <div class="table-responsive">
                <table class="<?php echo table_class_pos() ?>" id="holdListDT" style="width: 100%">
                    <thead>
                    <tr>
                        <th> #</th>
                        <th> Bill No.</th>
                        <th> <?php echo $this->lang->line('posr_hold_remarks'); ?><!--Hold Remarks--></th>
                        <th> <?php echo $this->lang->line('posr_hold_date'); ?><!--Hold Date--></th>
                        <th> <?php echo $this->lang->line('posr_created_date'); ?><!--Created Date--> </th>
                        <th><i class="fa fa-life-ring" aria-hidden="true"></i> Table</th>
                        <th> &nbsp; </th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>


    </div>

</div>

<script>

    $(document).ready(function (e) {
        loadHoldListPOS();
    });
    function loadHoldListPOS() {
        $('#holdListDT').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Pos_restaurant/loadHoldListPOS_tablet'); ?>",
            "aaSorting": [[3, 'desc']],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "invoiceID"},
                {"mData": "invoiceCode"},
                {"mData": "remarks"},
                {"mData": "holdDate"},
                {"mData": "createdDate"},
                {"mData": "diningTableDescription"},
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
    }
</script>