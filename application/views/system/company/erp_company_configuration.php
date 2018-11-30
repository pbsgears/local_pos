<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('config_company_configuration');
echo head_page($title, false);

/*echo head_page('Company Configuration',false); */?>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <?php if (empty($this->common_data['company_data']['company_code'])) { ?>
    <div class="row">
        <div class="col-md-5">
            &nbsp;
        </div>
        <div class="col-md-7 text-right">
            <button type="button" class="btn btn-primary pull-right" onclick="fetchPage('system/company/erp_company_configuration_new',null,'Add Company','COM');"><i class="fa fa-plus"></i> New Company</button>
        </div>
    </div><hr>
    <?php } ?>
    <div class="table-responsive">
        <table id="company_table" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 15%"><?php echo $this->lang->line('config_logo');?><!--Logo--></th>
                <th style="min-width: 60%"><?php echo $this->lang->line('config_company_details');?><!--Company Details--></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
            </tr>
            </thead>
        </table>
    </div>
<?php echo footer_page('Right foot','Left foot',false); ?>
    <script type="text/javascript">
        $(document).ready(function() {
            company_table();
        });

        function company_table(){
            var Otable = $('#company_table').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Company/fetch_company'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        x++;
                    }
                },
                "aoColumns": [
                    {"mData": "company_id"},
                    {"mData": "img"},
                    {"mData": "company_detail"},
                    {"mData": "edit"},
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