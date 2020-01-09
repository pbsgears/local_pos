<?php defined('BASEPATH') OR exit('No direct script access allowed');
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('footer', $primaryLanguage);
$companyInfo = get_companyInfo();
$productID = $companyInfo['productID'];


if ($productID == 2) {
    $theme = 'skin-blue-dark skin-blue';
} else {
    $theme = 'skin-blue-dark skin-black-light';
}

?>
<?php //header('Content-type: text/html; charset=utf-8');?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $title; ?></title>
    <link rel="icon" href="<?php echo base_url() . '/favicon.ico'; ?>" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?php echo base_url() . '/favicon.ico'; ?>" type="image/x-icon"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/font-awesome/css/font-awesome.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/jvectormap/jquery-jvectormap-1.2.2.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/dist/css/AdminLTE.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/dist/css/skins/_all-skins.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/animate/animate.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/iCheck/all.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/themify-icons/themify-icons.css'); ?>"/>
    <link rel="stylesheet"
          href="<?php echo base_url('plugins/datetimepicker/build/css/bootstrap-datetimepicker.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('plugins/tapmodo-Jcrop-1902fbc/css/jquery.Jcrop.min.css'); ?>"/>
    <!--<link rel="stylesheet" href="<?php /*echo base_url('plugins/Dragtable/dragtable.css'); */ ?>" />-->

    <!--Bootstrap Country flag-->
    <link rel="stylesheet" href="<?php echo base_url('plugins/country_flag/flags.css'); ?>"/>


    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="<?php echo base_url('plugins/jQuery/jQuery-2.1.4.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/bootstrap/js/bootstrap.min.js'); ?>"></script>
</head>
<?php
$bar_top = '';
$side_bar = get_cookie('SIDE_BAR');
if (isset($side_bar)) {
    $bar_top = $side_bar;
}
?>
<style type="text/css">
    .dataTable_selectedTr {
        background-color: #B0BED9 !important;
    }

    .progressbr {
        height: 5px !important;
        margin-bottom: 0 !important;;
    }
</style>

<body class="fixed  skin-blue-dark skin-black-light">
<div class="wrapper">
    <header class="main-header">

        <a href="<?php echo site_url('dashboard'); ?>" class="logo lg-device-only">
            <span class="logo-mini"><b>POS</b></span>
            <span class="logo-lg">
                <?php
                echo '<img style="max-height:30px;"  src="' . base_url() . 'images/' . LOGO . '"/>';
                ?>
            </span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle r-nav-menu" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <a href="#" class="logo log-sm-device-only">
                <?php
                echo '<img style="max-height:30px;"  src="' . base_url() . 'images/spur-cirl-100.png"/>';
                ?>
            </a>
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">




                    <li class="dropdown user user-menu">
                        <button class="btn btn-lg btn-success btn-myCustom btn-disable-when-load"
                                onclick="open_pos_payments_modal()">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i><br/> &nbsp;
                            <?php echo $this->lang->line('common_pay'); ?>
                        </button>
                    </li>

                    <li class="dropdown user user-menu">
                        <button type="button" class="btn btn-lg btn-default btn-myCustom" onclick="open_holdReceipt()">
                            <i class="fa fa-external-link-square text-purple" aria-hidden="true"></i> <br/>
                            <?php echo $this->lang->line('posr_open'); ?><!--Open-->
                        </button>
                    </li>

                    <li class="dropdown user user-menu">
                        <button class="btn  btn-lg btn-warning btn-myCustom" onclick="holdReceipt();">
                            <i class="fa fa-pause" aria-hidden="true"></i> &nbsp;<br>
                            <?php echo $this->lang->line('posr_hold'); ?><!--Hold-->
                        </button>
                    </li>


                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-cogs" aria-hidden="true"></i>

                        </a>
                        <ul class="dropdown-menu">


                            <!-- Menu Footer-->

                            <li class="user-footer">
                                <div>

                                    <button class="btn btn-lg btn-danger btn-block dangerCustom"
                                            onclick="checkPosAuthentication(9)">
                                        <!--onclick="cancelCurrentOrder()"-->
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                        Cancel Bill
                                    </button>

                                    <button type="button" class="btn btn-block btn-lg btn-default btn-myCustom"
                                            rel="tooltip"
                                            title=""
                                            onclick="open_kitchen_ready()">
                                        <i class="fa fa-cutlery text-purple" aria-hidden="true"></i>
                                        <?php echo $this->lang->line('posr_kitchen'); ?><!--Kitchen-->
                                    </button>

                                    <button type="button" class="btn btn-block btn-lg btn-default btn-myCustom"
                                            onclick="open_void_Modal()">
                                        <i class="fa fa-ban text-red" aria-hidden="true"></i>
                                        <?php echo $this->lang->line('common_closed'); ?><!--Closed-->
                                        <?php echo $this->lang->line('posr_bills'); ?><!--Bills-->
                                    </button>

                                </div>
                                <hr class="m-4">
                                <div class="pull-left">
                                    <a href="#" class="btn btn-default btn-flat"
                                       onclick="clickPowerOff()">
                                        <i class="fa fa-power-off text-red" aria-hidden="true"></i>
                                        <?php echo $this->lang->line('posr_power'); ?> <!--Power-->
                                    </a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?php echo site_url('Login/logout'); ?>" class="btn btn-default btn-flat">Sign
                                        out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->
                    <!--<li class="hidden-xs">
                        <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                    </li>-->
                </ul>
            </div>

        </nav>
    </header>

