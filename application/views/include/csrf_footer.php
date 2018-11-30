

<script type="text/javascript">

    var CSRFHash = '<?php echo $this->security->get_csrf_hash() ?>';
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
                    console.log('get');
                } else {
                    console.log('get form data not found');
                }
            }
        });

    }

    $(document).ready(function () {
        csrf_init();

        setInterval(function () {
            refresh_session_status();
        }, 3601000);
    });

    </script>
