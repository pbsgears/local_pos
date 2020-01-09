<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div class="box box-success">
    <div class="box-header with-border">
        <h4 class="box-title"><?php echo $this->lang->line('dashboard_cashbank_position');?><!--Cash/Bank Position--></h4>
        <div class="box-tools pull-right">
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
        <table id="financialposition_table<?php echo $userDashboardID ?>" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 60%"><?php echo $this->lang->line('dashboard_bank_name');?><!--Bank Name--></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('dashboard_book_balance');?><!--Book Balance--></th>
                <!--<th style="min-width: 20%">Bank Balance</th>-->
            </tr>
            </thead>
        </table>
    </div>
    <!-- /.box-body -->
</div>

<script>
    financialPosition<?php echo $userDashboardID ?>();
    function financialPosition<?php echo $userDashboardID ?>() {
        var Otable = $('#financialposition_table'+<?php echo $userDashboardID ?>).DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "bFilter": false,
            "bLengthChange": false,
            "bInfo": false,
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },
            "aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
            "pageLength": 5,
            "sAjaxSource": "<?php echo site_url('Finance_dashboard/fetch_financialPosition'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
            },
            "aoColumns": [
                {"mData": "GLDescription"},
                {"mData": "bankCurrencyCode"},
                {"mData": "bookBalance"}
                /*{"mData": "bankBalance"}*/
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

</script>