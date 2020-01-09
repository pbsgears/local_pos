<div class="wrapper">
    <header class="main-header">

        <!-- Logo -->
        <a href="<?php echo site_url('dashboard'); ?>" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>ERP</b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg" style="padding:12px;">
                <?php
                $defaultImagePath = 'images/' . LOGO;
                $logoImage = base_url($defaultImagePath);
                $outletInfo = get_outletInfo();
                $image = $outletInfo['warehouseImage'];
                if (!empty($image)) {
                    $outletImagePath = 'uploads/warehouses/' . $image;
                    $logoImage = base_url($outletImagePath);
                }
                ?>
                <?php echo '<img style="max-height:30px;"  src="' . $logoImage . '"/>' ?>
            </span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <div id=""></div>
            <!-- Sidebar toggle button-->
            <div class="col-md-5 pull-left" id="master-time-div" style="">
                <ul class="nav navbar-nav hidden-xs">
                    <li>
                        <a style="border: none" id="timeBox_style">
                            <div class="hidden-md hidden-sm hidden-xs">
                                <span class="" id="timeBox" style="font-size: 15px; font-weight: bolder"></span> &nbsp;&nbsp;&nbsp;
                                <span class="" id="dateBox"></span>
                            </div>
                            <div class="hidden-lg">
                                <span class="hidden-sm hidden-xs">Date : </span><strong><?php echo date('d/m/Y') ?></strong>
                            </div>
                        </a>
                    </li>
                    <li class="show_syncing_progress" style="display: none;">
                        <a style="border-left: 1px solid #eee; padding: 10px;" href=""
                           class="sync_progress_icon_container">
                            <i class="fa fa-cloud-upload fa-2x text-info blink_me" aria-hidden="true"></i>
                            <!--<span class="label label-warning" id="count_of_to_be_synced">0</span>-->
                        </a>
                    </li>
                    <li>
                        <a style="border-left: 1px solid #eee; padding: 10px;" href="#">
                            <button class="btn btn-default" type="button" onclick="manual_sync()">Sync</button>
                            <span class="label label-warning" id="count_of_to_be_synced">0</span>
                            <input type="hidden" id="input_count_of_to_be_synced" value="0">
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
                            <i class="fa fa-refresh fa-spin" style="color:#0b0803; font-size:18px;"></i> <!--Loading-->
                        </a>
                    </li>
                    <?php if (!empty($posData['wareHouseLocation'])) { ?>
                        <li class="dropdown user user-menu hidden-sm hidden-xs" id="">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-placement="bottom">
                                <label style="margin-bottom: 0px">
                                    <?php
                                    $outletInfo = get_outletInfo();
                                    echo ucwords(trim_value_pos($outletInfo['wareHouseDescription'], 8, 'bottom'));
                                    ?>
                                </label>
                            </a>
                        </li>
                    <?php }
                    if (!empty($posData['counterDet'])) { ?>
                        <li class="dropdown user user-menu" id="">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <label style="margin-bottom: 0px">&nbsp;
                                    <?php echo $posData['counterDet']; ?>
                                    &nbsp;
                                </label>
                            </a>
                        </li>
                    <?php } ?>
                    <li class="user user-menu hidden-xs">
                        <a class="dropdown-toggle" data-toggle="dropdown">
                            <span rel="tooltip" data-placement="bottom"
                                  title="<?php
                                  echo $this->common_data['company_data']['company_name'];
                                  ?>">
                                <?php echo current_companyCode(); ?>
                            </span>
                        </a>
                    </li>
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <?php
                            $filePath = imagePath() . $this->session->EmpImage;
                            $currentEmp_img = checkIsFileExists($filePath);
                            ?>
                            <img src="<?php echo $currentEmp_img; ?>" class="user-image" alt="User Image">
                            <span class="hidden-xs"><?php echo $name = ucwords($this->session->loginusername); ?> </br></span>
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
                                    <a href="#" onclick="fetchPage('system/profile/profile_information','','Profile')"
                                       class="btn btn-default btn-flat">Profile</a>
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

