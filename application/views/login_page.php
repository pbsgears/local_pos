<?php
$session = $this->session->userdata('status');
if ($session == 1) {
    header('Location:' . site_url() . '/dashboard');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Log in | ERP</title>
    <link rel="icon" href="<?php echo base_url().'/favicon.ico'; ?>" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?php echo base_url().'/favicon.ico'; ?>" type="image/x-icon"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/font-awesome/css/font-awesome.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/dist/css/AdminLTE.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/validation/css/bootstrapValidator.min.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/animate/animate.css'); ?>"/>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">

        body {
            background-color: #d6d9d0;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }

        .fa-spin {
            -webkit-animation: fa-spin 20s infinite linear !important;
            animation: fa-spin 20s infinite linear !important;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">

    <!-- /.login-logo -->
    <div class="login-box-body" style="border-radius: 6px;">
        <?php if ($this->session->flashdata('msg')) { ?>
            <div role="alert" class="alert alert-success"><?php echo $this->session->flashdata('msg'); ?></div>
        <?php } ?>
        <?php if (!empty($extra) && ($type == 'e')) { ?>
            <div role="alert" class="alert alert-danger"><?php echo $extra; ?></div>
        <?php } elseif (!empty($extra) && ($type == 's')) {
            ?>
            <div role="alert" class="alert alert-success"><?php echo $extra; ?></div>
            <?php
        } ?>
        <div class="text-center m-b-md">
            <span class="fa fa-spin"><img style="max-height: 35px;" src="<?php echo base_url('images/spur-cirl-100.png') ?>" alt="Logo"> <br></span>
            <img style="max-height: 35px;" src="<?php echo base_url('images/spur-box-101.png') ?>" alt="Logo">

            <h3>Welcome to <?php echo SYS_NAME ?>&trade; ERP</h3>
            <!--<small>Web Enterprise Resource Planning Solution.</small>-->
            <p>Please Confirm Your User Credential</p>
        </div>
        <br>
        <?php echo form_open('login/loginSubmit', ' id="login_form" role="form"'); ?>
        <div class="form-group has-feedback">
            <input type="text" class="form-control" name="Username" id="from_username"
                   placeholder="Please enter you username" readonly
                   onfocus="this.removeAttribute('readonly');">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input type="password" class="form-control" name="Password" id="from_password" placeholder="******"
                   autocomplete="off" readonly
                   onfocus="this.removeAttribute('readonly');">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
            <div class="col-xs-8">
                <a href="<?php echo site_url('Login/forget_password') ?>">Forget password?</a>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
                <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
            </div>
            <!-- /.col -->
        </div>
        </form>
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<script src="<?php echo base_url('plugins/jQuery/jQuery-2.1.4.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/validation/js/bootstrapValidator.min.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#login_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                Username: {validators: {notEmpty: {message: 'Username is required.'}}},
                Password: {validators: {notEmpty: {message: 'Password is required.'}}}
            },
        }).on('success.form.bv', function (e) {
        });

        setTimeout(function () {
            $("#from_password").val('');
            $("#from_username").val('');
        }, 500);
    });
</script>
</body>
</html>
