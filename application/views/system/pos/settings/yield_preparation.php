<link rel="stylesheet" href="<?php echo base_url('plugins/iCheck/minimal/_all.css') ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<script src="<?php echo base_url('plugins/iCheck/icheck.min.js') ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<?php
$customerType = all_customer_type();
$unitType = all_umo_new_drop();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div class="box box-warning">
    <div class="box-body">
        <div class="nav-tabs-custom">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="posContainer">
                        <h4 style="font-size:16px; font-weight: 800;">
                            <?php echo $this->lang->line('pos_config_yield_preparation'); ?><!--Yield Preparation-->
                            - <?php echo current_companyCode() ?>
                            <span class="btn btn-primary btn-xs pull-right" onclick="fetchPage('system/pos/settings/add_yield_preparation',null,'Add Yield Preparation','YPRP')"><i
                                        class="fa fa-plus"></i>
                                <?php echo $this->lang->line('common_update_add_new'); ?><!--Add New--></span>
                        </h4>
                        <table class="<?php echo table_class_pos(1) ?>" id="tbl_yieldPreparation"
                               style="font-size:12px;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('pos_config_yield_number'); ?><!--Yield Number--> </th>
                                <th><?php echo $this->lang->line('common_item'); ?><!--Item--> </th>
                                <th>Document Date</th>
                                <th>outlet</th>
                                <th><?php echo $this->lang->line('common_uom'); ?><!--UOM--> </th>
                                <th><?php echo $this->lang->line('common_qty'); ?><!--Qty--> </th>
                                <th><?php echo $this->lang->line('common_narration'); ?><!--Narration--></th>
                                <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /.tab-content -->
        </div>
    </div>
</div>
<script type="text/javascript"
        src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<script>
    var otable;
    $(document).ready(function (e) {
        fetchYieldPrepartion();
    });

    function fetchYieldPrepartion() {
        otable = $('#tbl_yieldPreparation').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('POS_yield_preparation/fetch_yield_preparation'); ?>",
            "aaSorting": [[0, 'desc']],
            "aoColumnDefs": [{"bSortable": false, "aTargets": [6,7]}],
            /*"language": {
             processing: '<i class="fa fa-refresh fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
             },*/
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = parseInt('<?php echo (!empty($this->input->post('page_id'))) ? $this->input->post('page_id') : 0; ?>');
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['yieldPreparationID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "yieldPreparationID"},
                {"mData": "documentSystemCode"},
                {"mData": "item"},
                {"mData": "documentDate"},
                {"mData": "wareHouseLocation"},
                {"mData": "uom"},
                {"mData": "qty"},
                {"mData": "narration"},
                {"mData": "status"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }
    
    function delete_yield_preparation(yieldPreparationID) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'yieldPreparationID': yieldPreparationID},
                    url: "<?php echo site_url('POS_yield_preparation/delete_yield_preparation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            otable.draw();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
</script>
