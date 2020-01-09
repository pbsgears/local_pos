<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div class="box box-success">
    <div class="box-header with-border">
        <h4 class="box-title"><?php echo $this->lang->line('dashboard_sales_log');?><!--Sales Log--></h4>
        <div class="box-tools pull-right">
            <strong class="btn-box-tool"><?php echo $this->lang->line('common_currency');?><!--Currency--> :
                (<?php echo $this->common_data['company_data']['company_reporting_currency'] ?>)</strong>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                        class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                        class="fa fa-times"></i>
            </button>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="display: block;">
        <table id="sales_log<?php echo $userDashboardID ?>" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 48%"><?php echo $this->lang->line('common_customer_name');?><!--Customer Name--></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('dashboard_current_year');?><!--Current Year--></th>
                <th style="min-width: 20%">%</th>
                <th style="min-width: 15%"><?php echo $this->lang->line('dashboard_last_year');?><!--Last Year--></th>
                <th style="min-width: 15%">%</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th><?php echo $this->lang->line('common_total');?><!--Total--></th>
                <th class="text-right"><?php echo number_format($currentYear, $DecimalPlaces) ?></th>
                <th></th>
                <th class="text-right"><?php echo number_format($lastYear, $DecimalPlaces) ?></th>
            </tr>
            </tfoot>
        </table>
    </div>
    <div class="overlay" id="overlay18<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
    <!-- /.box-body -->
</div>

<script>
    salesLog<?php echo $userDashboardID ?>();
    function salesLog<?php echo $userDashboardID ?>() {
        var Otable5 = $('#sales_log' +<?php echo $userDashboardID ?>).DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "bFilter": false,
            "bInfo": false,
            "bLengthChange": false,
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },
            "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
            "pageLength": 5,
            "sAjaxSource": "<?php echo site_url('Finance_dashboard/fetch_sales_log'); ?>",
            "aaSorting": [[0, 'asc']],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')

            },
            "aoColumns": [
                {"mData": "customerName"},
                {"mData": "currentYear"},
                {"mData": "currentYearPercentage"},
                {"mData": "lastYear"},
                {"mData": "lastYearPercentage"}
            ],
            "columnDefs": [
                {"searchable": false, "targets": 2}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "period", "value": <?php echo $this->input->post("period"); ?>});
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
</script>