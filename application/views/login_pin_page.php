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
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Log in | ERP</title>
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
            /* background: url("

        <?php echo base_url('images/login_bg.jpg');?>  ") no-repeat center center fixed; */
            background-color: #d6d9d0;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="lockscreen-wrapper">
        <div class="lockscreen-logo">
            <img style="max-height: 40px;" src="<?php echo base_url('images/' . LOGO) ?>" alt="Logo">
            <h3 style="margin-top: 2px">Welcome to Admin Login</h3>
        </div>
        <!-- User name -->
        <div class="lockscreen-name">&nbsp;</div>

        <!-- START LOCK SCREEN ITEM -->
        <div class="lockscreen-item">
            <!-- lockscreen image -->
            <div class="lockscreen-image">
                <img src="<?php echo base_url('images/lock.ico') ?>" alt="User Image">
            </div>
            <!-- /.lockscreen-image -->

            <!-- lockscreen credentials (contains the form) -->
            <form action="<?php echo site_url('login/loginPinSubmit') ?>" method="post" class="lockscreen-credentials">
                <div class="input-group">
                    <input type="password" class="form-control" name="pinNumber" placeholder="PIN Number">
                    <input type="hidden" class="form-control" name="adminMasterID" value="<?php echo $adminMasterID ?>">
                    <div class="input-group-btn">
                        <button type="submit" class="btn"><i class="fa fa-arrow-right text-muted"></i></button>
                    </div>
                </div>
            </form>
            <!-- /.lockscreen credentials -->

        </div>
        <?php if($this->session->flashdata('msg')) { ?>
            <div role="alert" class="text-danger text-center"><?php echo $this->session->flashdata('msg'); ?></div>
        <?php } ?>
        <div class="help-block text-center">
            Enter your pin number retrieve your session
        </div>
        <!-- /.lockscreen-item -->

        <div class="lockscreen-footer text-center">
            Copyright &copy; 2016-2017 <b><a href="http://almsaeedstudio.com" class="text-black">Xcloud</a></b><br>
            All rights reserved
        </div>
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
    });
</script>
</body>
</html>
