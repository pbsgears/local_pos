<div class="wrapper">
    <header class="main-header">

        <!-- Logo -->
        <a href="<?php echo site_url('dashboard'); ?>" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>ERP</b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg">
                <?php echo '<img style="max-height:30px;"  src="' . base_url() . 'images/' . LOGO . '"/>' ?>
            </span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <div id=""></div>
            <!-- Sidebar toggle button-->



            <div class="col-md-4 pull-left" id="master-time-div" style="">
                <ul class="nav navbar-nav hidden-xs">
                    <li>
                        <a style="border: none" id="timeBox_style">

                            <div class="hidden-md hidden-sm hidden-xs">
                                <span class="" id="timeBox" style="font-size: 15px; font-weight: bolder"></span> &nbsp;&nbsp;&nbsp;
                                <span class=""> <?php echo date('d/m/Y') ?></span>
                            </div>

                            <div class="hidden-lg">
                                <span
                                    class="hidden-sm hidden-xs">Date : </span><strong><?php echo date('d/m/Y') ?></strong>
                            </div>


                        </a>
                    </li>
                </ul>
            </div>

            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu" id="posPreLoader" style="display: none;">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                           style="background-color: rgba(244, 244, 244, 0.3);">
                            <i class="fa fa-refresh fa-spin" style="color:#0b0803; font-size:18px;"></i> Loading
                        </a>
                    </li>
                    <?php if (!empty($posData['wareHouseLocation'])) { ?>
                        <li class="dropdown user user-menu hidden-sm hidden-xs" id="">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <label style="margin-bottom: 0px"><?php echo $posData['wareHouseLocation']; ?></label>
                            </a>
                        </li>
                    <?php }
                    if (!empty($posData['counterDet'])) { ?>
                        <li class="dropdown user user-menu" id="">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <label style="margin-bottom: 0px">&nbsp;<?php echo $posData['counterDet']; ?>
                                    &nbsp;</label>
                            </a>
                        </li>
                    <?php } ?>

                    <li class="user user-menu">
                        <a class="dropdown-toggle" data-toggle="dropdown">
                            <span
                                class="hidden-xs"><?php echo '( ' . current_companyCode() . ' ) ' . ucwords($this->common_data['company_data']['company_name']); ?></span>
                        </a>
                    </li>
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <?php
                            $filePath = imagePath() . $this->session->EmpImage;
                            $currentEmp_img = checkIsFileExists($filePath);
                            ?>
                            <img src="<?php echo $currentEmp_img; ?>" class="user-image" alt="User Image">
                            <span
                                class="hidden-xs"><?php echo $name = ucwords($this->session->loginusername); ?> </br></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="<?php echo $currentEmp_img; ?>" class="img-circle" alt="User Image">
                                <p>
                                    <?php echo $name = ucwords($this->session->username); ?>
                                    <!-- <small><?php //$company = $this->cache->get('company_11'); var_dump($company); ?></small> -->
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="#" class="btn btn-default btn-flat" onclick="fetchPage('system/profile/profile_information','','Profile')">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?php echo site_url('Login/logout'); ?>" class="btn btn-default btn-flat">Sign
                                        out</a>
                                </div>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>

        </nav>
    </header>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-09-18
 * Time: 2:49 PM
 */