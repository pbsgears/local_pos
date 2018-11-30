
<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>

<script type="text/javascript" src="<?php echo base_url('plugins/toastr/toastr.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/fastclick/fastclick.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/app.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/sparkline/jquery.sparkline.min.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/jvectormap/jquery-jvectormap-world-mill-en.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/slimScroll/jquery.slimscroll.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/chartjs/Chart.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/demo.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/sweetalert/sweet-alert.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/offline/offline.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/holdon/HoldOn.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/typeahead.bundle.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/bootstrap/js/jasny-bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/handlebars/handlebars-v4.0.5.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/tableHeadFixer/tableHeadFixer.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/multiselect/dist/js/multiselect.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/iCheck/icheck.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/multiSelectCheckbox/dist/js/bootstrap-multiselect.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/daterangepicker/moment.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/xeditable/js/bootstrap-editable.min.js'); ?>"></script>
<!--<script type="text/javascript" src="<?php /*echo base_url('plugins/Dragtable/jquery.dragtable.js'); */ ?>"></script>-->
<script type="text/javascript" src="<?php echo base_url('plugins/bootbox-alert/bootbox.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/jQuery/jquery.maskedinput.js'); ?>"></script>
<!--<script type="text/javascript"
        src="<?php /*echo base_url('plugins/multiselect/dist/js/bootstrap-multiselect.js'); */ ?>"></script>-->
<script type="text/javascript" src="<?php echo base_url('plugins/highchart/highcharts.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/highchart/modules/exporting.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/highchart/modules/no-data-to-display.js'); ?>"></script>

<script type="text/javascript"
        src="<?php echo base_url('plugins/input-mask/dist/jquery.inputmask.bundle.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/input-mask/dist/inputmask/inputmask.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/input-mask/dist/inputmask/inputmask.date.extensions.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/input-mask/dist/inputmask/jquery.inputmask.js'); ?>"></script>
<!-- Added by Nazir to allow - value as well in textbox-->
<script type="text/javascript"
        src="<?php echo base_url('plugins/numeric/jquery.numeric.min.js'); ?>"></script>

<script type="text/javascript"
        src="<?php echo base_url('plugins/tapmodo-Jcrop-1902fbc/js/jquery.Jcrop.min.js'); ?>"></script>

<script type="text/javascript" src="<?php echo base_url('plugins/combodate/combodate.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/combodate/moment.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/datetimepicker/src/js/bootstrap-datetimepicker.js'); ?>"></script>

<!--jquery auto complete-->
<script type="text/javascript"
        src="<?php echo base_url('plugins/jQuery-Autocomplete-master/dist/jquery.autocomplete.js'); ?>"></script>
<script>
    function check_session_status() {
        $.ajax({
            async: true,
            type: 'get',
            dataType: 'json',
            data: {'': ''},
            url: '<?php echo site_url("login/session_status"); ?>',
            success: function (data) {
                if (data['status'] == 0) {
                    session_logout_page();
                } else {
                    CSRFHash = data.csrf;
                }
                stopLoad();
            },
            error: function () {
                stopLoad();
            }
        });
    }

    check_session_status();
</script>
<?php

exit;
?>
