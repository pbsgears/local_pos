<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Log in | ERP</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

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
    <style
        type="text/css">table.dataTable.dtr-inline.collapsed > tbody > tr > td.child, table.dataTable.dtr-inline.collapsed > tbody > tr > th.child, table.dataTable.dtr-inline.collapsed > tbody > tr > td.dataTables_empty {
            cursor: default !important
        }

        table.dataTable.dtr-inline.collapsed > tbody > tr > td.child:before, table.dataTable.dtr-inline.collapsed > tbody > tr > th.child:before, table.dataTable.dtr-inline.collapsed > tbody > tr > td.dataTables_empty:before {
            display: none !important
        }

        table.dataTable.dtr-inline.collapsed > tbody > tr[role="row"] > td:first-child, table.dataTable.dtr-inline.collapsed > tbody > tr[role="row"] > th:first-child {
            position: relative;
            padding-left: 30px;
            cursor: pointer
        }

        table.dataTable.dtr-inline.collapsed > tbody > tr[role="row"] > td:first-child:before, table.dataTable.dtr-inline.collapsed > tbody > tr[role="row"] > th:first-child:before {
            top: 12px;
            left: 4px;
            height: 14px;
            width: 14px;
            display: block;
            position: absolute;
            color: white;
            border: 2px solid white;
            border-radius: 14px;
            -webkit-box-shadow: 0 0 3px #444;
            box-shadow: 0 0 3px #444;
            -webkit-box-sizing: content-box;
            box-sizing: content-box;
            text-align: center;
            text-indent: 0 !important;
            font-family: 'Courier New', Courier, monospace;
            line-height: 14px;
            content: '+';
            background-color: #0275d8
        }

        table.dataTable.dtr-inline.collapsed > tbody > tr.parent > td:first-child:before, table.dataTable.dtr-inline.collapsed > tbody > tr.parent > th:first-child:before {
            content: '-';
            background-color: #d33333
        }

        table.dataTable.dtr-inline.collapsed.compact > tbody > tr > td:first-child, table.dataTable.dtr-inline.collapsed.compact > tbody > tr > th:first-child {
            padding-left: 27px
        }

        table.dataTable.dtr-inline.collapsed.compact > tbody > tr > td:first-child:before, table.dataTable.dtr-inline.collapsed.compact > tbody > tr > th:first-child:before {
            top: 5px;
            left: 4px;
            height: 14px;
            width: 14px;
            border-radius: 14px;
            line-height: 14px;
            text-indent: 3px
        }

        table.dataTable.dtr-column > tbody > tr > td.control, table.dataTable.dtr-column > tbody > tr > th.control {
            position: relative;
            cursor: pointer
        }

        table.dataTable.dtr-column > tbody > tr > td.control:before, table.dataTable.dtr-column > tbody > tr > th.control:before {
            top: 50%;
            left: 50%;
            height: 16px;
            width: 16px;
            margin-top: -10px;
            margin-left: -10px;
            display: block;
            position: absolute;
            color: white;
            border: 2px solid white;
            border-radius: 14px;
            -webkit-box-shadow: 0 0 3px #444;
            box-shadow: 0 0 3px #444;
            -webkit-box-sizing: content-box;
            box-sizing: content-box;
            text-align: center;
            text-indent: 0 !important;
            font-family: 'Courier New', Courier, monospace;
            line-height: 14px;
            content: '+';
            background-color: #0275d8
        }

        table.dataTable.dtr-column > tbody > tr.parent td.control:before, table.dataTable.dtr-column > tbody > tr.parent th.control:before {
            content: '-';
            background-color: #d33333
        }

        table.dataTable > tbody > tr.child {
            padding: 0.5em 1em
        }

        table.dataTable > tbody > tr.child:hover {
            background: transparent !important
        }

        table.dataTable > tbody > tr.child ul.dtr-details {
            display: inline-block;
            list-style-type: none;
            margin: 0;
            padding: 0
        }

        table.dataTable > tbody > tr.child ul.dtr-details > li {
            border-bottom: 1px solid #efefef;
            padding: 0.5em 0
        }

        table.dataTable > tbody > tr.child ul.dtr-details > li:first-child {
            padding-top: 0
        }

        table.dataTable > tbody > tr.child ul.dtr-details > li:last-child {
            border-bottom: none
        }

        table.dataTable > tbody > tr.child span.dtr-title {
            display: inline-block;
            min-width: 75px;
            font-weight: bold
        }

        div.dtr-modal {
            position: fixed;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: 100;
            padding: 10em 1em
        }

        div.dtr-modal div.dtr-modal-display {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            width: 50%;
            height: 50%;
            overflow: auto;
            margin: auto;
            z-index: 102;
            overflow: auto;
            background-color: #f5f5f7;
            border: 1px solid black;
            border-radius: 0.5em;
            -webkit-box-shadow: 0 12px 30px rgba(0, 0, 0, 0.6);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.6)
        }

        div.dtr-modal div.dtr-modal-content {
            position: relative;
            padding: 1em
        }

        div.dtr-modal div.dtr-modal-close {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 22px;
            height: 22px;
            border: 1px solid #eaeaea;
            background-color: #f9f9f9;
            text-align: center;
            border-radius: 3px;
            cursor: pointer;
            z-index: 12
        }

        div.dtr-modal div.dtr-modal-close:hover {
            background-color: #eaeaea
        }

        div.dtr-modal div.dtr-modal-background {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 101;
            background: rgba(0, 0, 0, 0.6)
        }

        @media screen and (max-width: 767px) {
            div.dtr-modal div.dtr-modal-display {
                width: 95%
            }
        }

        div.dtr-bs-modal table.table tr:first-child td {
            border-top: none
        }
    </style>

    <link rel="stylesheet" href="<?php echo base_url('plugins/css/login2.css'); ?>"/>
    <style>
        .login-f-color{
            color: #a8a8a8;
        }
    </style>
</head>


<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
<app-dashboard class="ng-star-inserted">

    <app-login class="ng-star-inserted">
        <div class="app flex-row align-items-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-5">
                        <div class="card-group">
                            <div class="card p-4" style="padding: 6px !important;">
                                <div class="card-body">

                                    <?php echo form_open('login/loginSubmit_gears', ' id="login_form" role="form"'); ?>
                                    <h3 class="text-center login-f-color">
                                        <img style="width:170px" src="<?php echo base_url('images/gears-s-normal-ico.png');  ?>" class="img-responsive" alt="">
                                    </h3>

                                    <h3 class="text-center login-f-color">Welcome to GEARS </h3>
                                    <p class="text-center login-f-color">Please Confirm Your User Credential</p>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                        <input class="form-control" name="Username"
                                               placeholder="Username" required="" type="text">
                                    </div>

                                    <div class="input-group mb-4">
                                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                        <input class="form-control" name="Password"
                                               placeholder="Password" required="" type="password">
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <button class="btn-primary px-4"
                                                    style="border: 1px solid; padding: 6px;">
                                                Login
                                            </button>
                                        </div>
                                        <div class="col-6 text-right">
                                            <button class="btn btn-link px-0" type="button">
                                                Forgot password?
                                            </button>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php if ($this->session->flashdata('msg')) { ?>
                                                <div role="alert"
                                                     class="alert alert-success"><?php echo $this->session->flashdata('msg'); ?></div>
                                            <?php } ?>
                                            <?php if (!empty($extra) && ($type == 'e')) { ?>
                                                <div role="alert" class="alert alert-danger"><?php echo $extra; ?></div>
                                            <?php } elseif (!empty($extra) && ($type == 's')) {
                                                ?>
                                                <div role="alert"
                                                     class="alert alert-success"><?php echo $extra; ?></div>
                                                <?php
                                            } ?>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-login>
</app-dashboard>


</body>
</html>