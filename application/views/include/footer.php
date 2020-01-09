</div><!-- /.content-wrapper -->
</div>
</div><!-- ./wrapper -->


<?php
$passwordComplexityExist = get_password_complexity_exist();
$passwordComplexity = get_password_complexity();
$this->load->view('include/inc-footer-modals');
?>

<!-- CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/select2/css/select2.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/validation/css/bootstrapValidator.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/sweetalert/sweet-alert.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datepicker/datepicker3.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/holdon/HoldOn.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datatables/dataTables.bootstrap.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/jasny-bootstrap.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/toastr/toastr.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/multiSelectCheckbox/dist/css/bootstrap-multiselect.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/timepicker2/bootstrap-timepicker.css'); ?>">
<!-- / CSS -->

<!-- JS -->
<script type="text/javascript" src="<?php echo base_url('plugins/select2/js/select2.full.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/validation/js/bootstrapValidator.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/timepicker2/bootstrap-timepicker2.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datepicker/bootstrap-datepicker.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/dataTables.bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/toastr/toastr.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/fastclick/fastclick.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/app.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/sparkline/jquery.sparkline.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/jvectormap/jquery-jvectormap-world-mill-en.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/slimScroll/jquery.slimscroll.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/demo.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/sweetalert/sweet-alert.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/holdon/HoldOn.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/typeahead.bundle.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/bootstrap/js/jasny-bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/handlebars/handlebars-v4.0.5.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/tableHeadFixer/tableHeadFixer.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/multiselect/dist/js/multiselect.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/iCheck/icheck.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/multiSelectCheckbox/dist/js/bootstrap-multiselect.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/daterangepicker/moment.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/xeditable/js/bootstrap-editable.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/bootbox-alert/bootbox.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/jQuery/jquery.maskedinput.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/input-mask/dist/jquery.inputmask.bundle.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/input-mask/dist/inputmask/inputmask.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/input-mask/dist/inputmask/inputmask.date.extensions.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/input-mask/dist/inputmask/jquery.inputmask.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/numeric/jquery.numeric.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/tapmodo-Jcrop-1902fbc/js/jquery.Jcrop.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/combodate/combodate.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/combodate/moment.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datetimepicker/src/js/bootstrap-datetimepicker.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/jQuery-Autocomplete-master/dist/jquery.autocomplete.js'); ?>"></script>
<!-- / JS -->

<script type="text/javascript">

    var popup = 0;
    var CSRFHash = '<?php echo $this->security->get_csrf_hash() ?>';
    var numberOfAttempt = 0;

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

    function refresh_session_status() {
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
        duration = (duration == null) ? '1000' : duration;
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

    function alerMessage(type, message) {
        // message+='<br /><br /><button type="button" class="btn clear">Yes</button>';

        toastr.options = {
            "closeButton": true,
            "debug": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-center",
            "preventDuplicates": true,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": 0,
            "extendedTimeOut": 0,
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut",
            "tapToDismiss": false
        };
        toastr.clear();
        if (type == 'e') {
            toastr.error(message, '<?php echo $this->lang->line('common_error');?>!');
            /*Error*/
        } else if (type == 's') {
            toastr.success(message, '<?php echo $this->lang->line('common_success');?>');
            /*Success!*/
        } else if (type == 'w') {
            toastr.warning(message, '<?php echo $this->lang->line('common_warning');?>');
            /*Warning!*/
        } else if (type == 'i') {
            toastr.info(message, '<?php echo $this->lang->line('common_information');?>');
            /*Information*/
        } else {
            toastr.error('wrong input type! type only allowed for "e","s","w","i"', 'Error!');
        }
    }

    function myAlert_topPosition(type, message) {
        toastr.options = {
            "closeButton": true,
            "debug": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-center animated-panel fadeInTop",
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
        };

        toastr.clear();
        if (type == 'e') {
            toastr.error(message, '<?php echo $this->lang->line('common_error');?>!');
            /*Error*/
        } else if (type == 's') {
            toastr.success(message, '<?php echo $this->lang->line('common_success');?>');
            /*Success!*/
        } else if (type == 'w') {
            toastr.warning(message, '<?php echo $this->lang->line('common_warning');?>');
            /*Warning!*/
        } else if (type == 'i') {
            toastr.info(message, '<?php echo $this->lang->line('common_warning');?>');
            /*Information*/
        } else {
            toastr.error('wrong input type! type only allowed for "e","s","w","i"', 'Error!');
        }
    }

    function startLoad() {
        HoldOn.open({
            theme: "sk-rect",//If not given or inexistent theme throws default theme , sk-bounce , sk-cube-grid
            message: "<div style='font-size: 16px; color:#ffffff; margin-top:20px;     text-shadow: 0px 0px 4px black, 0 0 7px #000000, 0 0 3px #000000;'> Loading, Please wait </div><div id='loaderDivContent'></div>",
            content: 'test', // If theme is set to "custom", this property is available
            textColor: "#000000" // Change the font color of the message
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

    function csrf_init() {
        $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
            if (originalOptions.type == 'POST' || originalOptions.type == 'post' || options.type == 'POST' || options.type == 'post') {
                if (options.processData) { /*options.contentType === 'application/x-www-form-urlencoded; charset=UTF-8'*/
                    options.data = (options.data ? options.data + '&' : '') + $.param({'<?php echo $this->security->get_csrf_token_name(); ?>': CSRFHash});
                } else {
                    options.data.append('<?php echo $this->security->get_csrf_token_name(); ?>', CSRFHash);
                }
            } else {
                if (options.processData) {
                } else {
                }
            }
        });

    }

    $(document).ready(function () {
        $('.modal').on('hidden.bs.modal', function () {
            modalFix()
        });

        $(".select2").select2();

        csrf_init();

        setInterval(function () {
            refresh_session_status();
        }, 3601000);


        var company_id = '<?php echo json_encode($this->common_data['company_data']['company_id']); ?>';
        var company_code = '<?php echo json_encode($this->common_data['company_data']['company_code']); ?>';
        if (company_code == 'null') {
            fetchPage('system/company/erp_company_configuration_new', company_id, 'Add Company', 'COM');
        }


    });

    function getMonthName(monthNumber) {
        var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        return months[monthNumber - 1];
    }

    function set_navbar_cookie() {
        var classVal = $('body').attr('class');
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Dashboard/set_navbar_cookie'); ?>",
            data: {'className': classVal},
            dataType: "json",
            cache: true
        });
    }

    function makeTdAlign(name, side, rowNo) {
        $('#' + name + ' tbody tr').each(function () {
            var thisRow = this;
            $.each(rowNo, function (i, v) {
                $(thisRow).find('td:eq(' + v + ')').css('text-align', side);
            });
        });
    }

    function msg_popup(btnClass = null) {
        setTimeout(function () {
            swal({
                html: true,
                title: '',
                text: 'This document contains some employees, That you do not have permission to view their informaiton'
            });
            if (btnClass != null) {
                $('.' + btnClass).hide();
            }
        }, 300);
    }

    function isDateInputMaskNotComplete(dateStr) {
        return (/[dmy]/.test(dateStr))
    }

    function openlanguagemodel() {
        $('#language_select_modal').modal('show');
    }


</script>

<?php if (!isset($noChart)) { ?>
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">

        /*Commented by shafry in local host only */
        var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
        (function () {
            var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/5885c3e02438f53b0a30ed13/default';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();

        Tawk_API.onStatusChange = function (status) {
            if (status === 'online') {
                document.getElementById('chat').innerHTML = '<img src="<?php echo base_url('images/chat2.ico')?>" width="25px" height="25px" class="user-image" alt="User Image"> Online';
                $('#chatUrl').attr('href', 'javascript:void(Tawk_API.toggle())');
            }
            else if (status === 'away') {
                document.getElementById('chat').innerHTML = '<img src="<?php echo base_url('images/chat2.ico')?>" width="25px" height="25px" class="user-image" alt="User Image"> Away';
                $('#chatUrl').attr('href', 'javascript:void(Tawk_API.toggle())');
            }
            else if (status === 'offline') {
                document.getElementById('chat').innerHTML = '<img src="<?php echo base_url('images/chat2.ico')?>" width="25px" height="25px" class="user-image" alt="User Image"> Offline';
                $('#chatUrl').attr('href', 'javascript:void(Tawk_API.toggle())');
            }
        };

        /**window.onbeforeunload = closingCode;
         function closingCode(){
            window.open("<?php echo site_url('Login/logout') ?>");
        }*/

    </script>
<?php } ?>
<!--End of Tawk.to Script-->
</body>
</html>