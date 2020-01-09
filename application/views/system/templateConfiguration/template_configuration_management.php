<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('config_template_setup');
echo head_page($title, false);

/*echo head_page('Template Setup', false);*/
$companyID = current_companyID();
?>
<div class="table-responsive" id="policyTable">
    <table id="company_template_configuration" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="width: 35px;">#</th>
            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th><?php echo $this->lang->line('config_default_value');?><!--Default Value--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">
    $(document).ready(function () {
        fetch_template_configuration_table();

        $('.headerclose').click(function () {
            fetchPage('system/templateConfiguration/template_configuration_management', 'Test', 'Template Configuration')
        })
    });

    function fetch_template_configuration_table() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            url: "<?php echo site_url('CompanyTemplate/fetch_template_configuration'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#policyTable').html(data);
                $('#company_template_configuration').DataTable()
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }



    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });


    function saveTemplate(id){
        var valu= $(id).val();
        var res = valu.split("|");
        var TempMasterID=res[0];
        var FormCatID=res[1];
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {TempMasterID: TempMasterID, FormCatID: FormCatID},
            url: "<?php echo site_url('CompanyTemplate/saveTemplate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1]);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });

    }





</script>