</div><!-- /.content-wrapper -->
</div>


<div style="height: 70px !important;"></div>
<footer class="main-footer navbar-fixed-bottom footerCustom">

    <div class="row">
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 footer-unwanted" style="opacity: 1 !important;" >
            <div class="text-center" >
                <?php
                $outletInfo = get_outletInfo();
                /*$outletInfo['wareHouseDescription']*/
                echo ' <span class="text-red">' . $outletInfo['wareHouseCode'] . '</span>';
                ?> | <?php echo current_user() ?>


            </div>
        </div>
        <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
            <div class="text-center">
                <input type="hidden" id="pos_orderNo" value="<?php
                $new = $this->lang->line('common_new');
                $invoiceID = isPos_invoiceSessionExist();
                if (!empty($invoiceID)) {
                    $id = str_pad($invoiceID, 4, "0", STR_PAD_LEFT);
                    echo get_pos_invoice_code($id);
                } else {
                    echo 'New';
                }
                ?>">

                <div id="pos_salesInvoiceID_btn">
                    <?php
                    $new = $this->lang->line('common_new');
                    $invoiceID = isPos_invoiceSessionExist();
                    if (!empty($invoiceID)) {
                        $id = str_pad($invoiceID, 4, "0", STR_PAD_LEFT);
                        echo get_pos_invoice_code($id);
                    } else {
                        echo '<span class="label label-danger">' . $new . '<!--New--></span>';
                    }
                    ?>

                </div>


            </div>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 footer-unwanted">
            <div id="posPreLoader" style="display: none;">
                <i class="fa fa-refresh fa-spin"></i> Loading
            </div>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <?php echo $this->lang->line('posr_net_total'); ?> :
            <span id="total_netAmount" class="posFooterTxtLg">0</span>

        </div>

    </div>
</footer>
</div><!-- ./wrapper -->


<?php
$passwordComplexityExist = get_password_complexity_exist();
$passwordComplexity = get_password_complexity();
?>
<?php $this->load->view('include/inc-footer-modals'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/select2/css/select2.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/sweetalert/sweet-alert.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datepicker/datepicker3.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/offline/offline.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/offline/offline-language-english.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/holdon/HoldOn.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datatables/dataTables.bootstrap.css'); ?>"/>

<!--Nasik 2017-06-13-->
<link rel="stylesheet" type="text/css"
      href="<?php echo base_url('plugins/datatables/fixedColumns.dataTables.min.css'); ?>"/>
<link rel="stylesheet" type="text/css"
      href="<?php echo base_url('plugins/datatables/keyTable.dataTables.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datatables/select.dataTables.min.css'); ?>"/>
<!--End-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/toastr/toastr.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/timepicker2/bootstrap-timepicker.css'); ?>">

<script type="text/javascript" src="<?php echo base_url('plugins/select2/js/select2.full.min.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/validation/js/bootstrapValidator.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/timepicker2/bootstrap-timepicker2.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datepicker/bootstrap-datepicker.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/dataTables.bootstrap.min.js'); ?>"></script>

<!--Nasik 2017-06-13-->
<script type="text/javascript"
        src="<?php echo base_url('plugins/datatables/dataTables.fixedColumns.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/dataTables.select.js'); ?>"></script>
<!--End-->

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


<script type="text/javascript">

    //fetchcompany(<?php //echo current_companyID() ?>, false);
    var popup = 0;

    /*Remove employee master filter values */
    window.localStorage.removeItem("isDischarged");
    window.localStorage.removeItem("employeeCode");
    window.localStorage.removeItem("segment");

    window.localStorage.removeItem('emp-master-alpha-search');
    window.localStorage.removeItem('emp-master-searchKeyword');
    window.localStorage.removeItem('emp-master-designation-list');
    window.localStorage.removeItem('emp-master-segment-list');
    window.localStorage.removeItem('emp-master-status-list');
    window.localStorage.removeItem('emp-master-pagination');

    /*setInterval(function () {
     check_session_status()
     }, 2000);*/

    function check_session_status() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            url: '<?php echo site_url("login/session_status"); ?>',
            success: function (data) {
                if (data['status'] == 0) {
                    session_logout_page();
                }
                stopLoad();
            },
            error: function () {
                stopLoad();
            }
        });
    }

    function session_logout_page() {
        swal({
            title: "Session Destroyed!",
            text: "You will be redirect to login page in 2 seconds.",
            timer: 2000,
            showConfirmButton: false
        });
        setTimeout(function () {
            window.location = '<?php echo site_url('/Login/logout'); ?>';
        }, 2000);
    }

    function fetchPage(page_url, page_id, page_name, policy_id, data_arr, master_page_url = null) {

        /***************************************************************
         * By : Nasik
         * date : 2017-09-15
         * Load the employee master filters
         ***************************************************************/
        var s_AlphaSearch = window.localStorage.getItem('emp-master-alpha-search');
        var s_SearchKeyword = window.localStorage.getItem('emp-master-searchKeyword');
        var s_Designation = window.localStorage.getItem('emp-master-designation-list');
        var s_Segment = window.localStorage.getItem('emp-master-segment-list');
        var s_Pagination = window.localStorage.getItem('emp-master-pagination');
        var s_Status = window.localStorage.getItem('emp-master-status-list');

        var filterPost = {
            alphaSearch: s_AlphaSearch,
            searchKeyword: s_SearchKeyword,
            designation: s_Designation,
            segment: s_Segment,
            empStatus: s_Status,
            pagination: s_Pagination
        };

        $.ajax({
            async: true,
            type: 'POST',
            url: '<?php echo site_url("dashboard/fetchPage"); ?>',
            dataType: 'html',
            data: {
                'page_id': page_id,
                'page_url': page_url,
                'page_name': page_name,
                'policy_id': policy_id,
                'data_arr': data_arr,
                'master_page_url': master_page_url,
                'filterPost': filterPost
            },
            beforeSend: function () {
                startLoad();
                check_session_status();
            },
            success: function (page_html) {
                stopLoad();
                /***************************************************************
                 * By : Nasik
                 * date : 2017-09-06
                 * to avoid Jquery UI library functions and other styles on
                 * \views\system\hrm\employee_master_new.php
                 ***************************************************************/

                $('.employee_master_styles').attr("disabled", "disabled");

                $('#ajax_body_container').html(page_html);
                $("html, body").animate({scrollTop: "0px"}, 10);

            },
            error: function (jqXHR, status, errorThrown) {

                stopLoad();
                $("html, body").animate({scrollTop: "0px"}, 10);
                $('#ajax_body_container').html(jqXHR.responseText + '<br/>Error Message: ' + errorThrown);
                check_session_status();
            }
        });
    }

    function refreshNotifications() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Dashboard/fetch_notifications"); ?>',
            dataType: 'json',
            async: true,
            success: function (data) {
                check_session_status();
                if (!jQuery.isEmptyObject(data)) {
                    toastr.options = {
                        "closeButton": true,
                        "debug": true,
                        "newestOnTop": true,
                        "progressBar": true,
                        "positionClass": "toast-bottom-right animated-panel fadeInRight",
                        "preventDuplicates": true,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    }
                    $.each(data, function (i, v) {
                        toastr[v.t](v.m, v.h);
                    });
                }
                stopLoad();
            },
            error: function () {
                stopLoad();
            }
        });
    }

    function notification(message, status) {
        toastr.options = {
            "positionClass": "toast-bottom-right",
        }

        if (status == undefined) {
            toastr.error(message)
        } else if (status == 's') {
            toastr.success(message);
        } else if (status == 'w') {
            toastr.warning(message);
        } else if (status == 'i') {
            toastr.info(message);
        } else {
            toastr.error(message);
        }
    }


    Number.prototype.formatMoney = function (c, d, t) {
        var n = this,
            c = isNaN(c = Math.abs(c)) ? 2 : c,
            d = d == undefined ? "." : d,
            t = t == undefined ? "," : t,
            s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    };

    function number_validation() {
        $(".number").attr('autocomplete', 'off');
        $(".number").on("onkeyup keyup blur", function (event) {
            $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                if (event.keyCode != 8) {
                    event.preventDefault();
                }
            }
        });
        $(".m_number").attr('autocomplete', 'off');
        $(".m_number").on("onkeyup keyup blur", function (event) {
            $(this).val($(this).val().replace(/[^-0-9\.]/g, ''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                if (event.keyCode != 8) {
                    event.preventDefault();
                }
                ;
            }
        });
    }

    function commaSeparateNumber(val, dPlace = 2) {
        var toFloat = parseFloat(val);
        var a = toFloat.toFixed(dPlace);
        while (/(\d+)(\d{3})/.test(a.toString())) {
            a = a.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
        }
        return a;
    }

    function date_format_change(userdate, policydate) {
        var date_string = moment(userdate, "YYYY-MM-DD").format(policydate);
        return date_string;
    }

    function removeCommaSeparateNumber(val) {
        return parseFloat(val.replace(/,/g, ""));
    }


    function myAlert(type, message, duration = null) {
        toastr.clear();
        initAlertSetup(duration);
        if (type == 'e' || type == 'd') {
            toastr.error(message, '<?php echo $this->lang->line('common_error');?>'/*'Error!'*/);
            check_session_status();
        } else if (type == 's') {
            toastr.success(message, '<?php echo $this->lang->line('common_success');?>'/*'Success!'*/);
        } else if (type == 'w') {
            toastr.warning(message, '<?php echo $this->lang->line('common_warning');?>'/*'Warning!'*/);
        } else if (type == 'i') {
            toastr.info(message, '<?php echo $this->lang->line('common_information');?>'/*'Information'*/);
        } else {
            check_session_status();
            toastr.error('wrong input type! type only allowed for "e","s","w","i"', 'Error!');
        }
    }

    function initAlertSetup(duration = null) {
        duration = ( duration == null ) ? '1000' : duration;
        toastr.options = {
            "closeButton": true,
            "debug": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right animated-panel fadeInTop",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": duration,
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }


    function startLoad() {
        HoldOn.open({
            theme: "sk-cube-grid",//If not given or inexistent theme throws default theme , sk-bounce
            message: "<div style='font-size: 13px;'> Loading, Please wait </div><div id='loaderDivContent'></div>",
            content: 'test', // If theme is set to "custom", this property is available
            textColor: "white" // Change the font color of the message
        });
    }

    function startLoadPos() {
        $("#posPreLoader").show();
    }

    function stopLoad() {
        HoldOn.close();
        $("#posPreLoader").hide();
    }

    function modalFix() {
        setTimeout(function () {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        }, 500);
    }

    $(document).ready(function () {
        $('.modal').on('hidden.bs.modal', function () {
            modalFix()
        });

        var company_id = '<?php echo json_encode($this->common_data['company_data']['company_id']); ?>';
        var company_code = '<?php echo json_encode($this->common_data['company_data']['company_code']); ?>';
        if (company_code == 'null') {
            fetchPage('system/company/erp_company_configuration_new', company_id, 'Add Company', 'COM');
        }


    });


    function fetchcompany(companyID, reload) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {companyID: companyID},
            url: "<?php echo site_url('Access_menu/load_navigation_html'); ?>",
            beforeSend: function () {
                /*startLoad();*/
                $('#ajax_body_container').html('');
            },
            success: function (data) {
                $('.main-sidebar').html(data);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

</script>
</body>
</html>