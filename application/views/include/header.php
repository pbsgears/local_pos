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
    <link rel="icon" href="<?php echo base_url().'/favicon.ico'; ?>" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?php echo base_url().'/favicon.ico'; ?>" type="image/x-icon"/>
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

<body class="sidebar-mini fixed hold-transition  <?php echo $theme.' '.$bar_top ?><?php //echo $extra.' '. ..; ?>">
