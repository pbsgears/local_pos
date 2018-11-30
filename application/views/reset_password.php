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
    <title>Reset Password | ERP</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Reset Password | ERP</title>
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





        <?php echo base_url('images/login_bg.jpg');?>      ") no-repeat center center fixed; */
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

    <!-- /.login-logo -->
    <div class="login-box-body" style="border-radius: 6px;">
        <?php if (!empty($extra) && ($type == 'e')) { ?>
            <div role="alert" class="alert alert-danger"><?php echo $extra; ?></div>
        <?php } elseif(!empty($extra) && ($type == 's')) {
            ?>
            <div role="alert" class="alert alert-success"><?php echo $extra; ?></div>
            <?php
        } ?>
        <div class="text-center m-b-md">
            <img style="max-height: 40px;" src="<?php echo base_url('images/' . LOGO) ?>" alt="Logo"> <br/>
            <h3>Welcome to <?php echo SYS_NAME ?>&trade; ERP</h3>
            <!--<small>Web Enterprise Resource Planning Solution.</small>-->
            <p>Please reset your password</p>
        </div>
        <br>
        <?php echo form_open(uri_string(), ' id="login_form" role="form"'); ?>
        <div class="form-group has-feedback">
            <input type="password" class="form-control" name="Password" placeholder="New Password">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input type="password" class="form-control" name="ConfirmPassword" placeholder="Confirm Password">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row" style="margin-top: 10px">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary btn-block btn-flat">Reset Password</button>
            </div>
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
                Password: {
                    validators: {
                        notEmpty: {message: 'Password is required.'},
                        identical: {
                            field: 'ConfirmPassword',
                            message: 'Password mismach'
                        }
                    }
                },
                ConfirmPassword: {
                    validators: {
                        notEmpty: {message: 'Confirm Password is required.'},
                        identical: {
                            field: 'Password',
                            message: 'Password mismach'
                        }
                    }
                }
            },
        }).on('success.form.bv', function (e) {
        });
    });
</script>
</body>
</html>
