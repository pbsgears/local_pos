<section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
        <div class="pull-left image">
            <?php
            $filePath = imagePath() . $this->session->EmpImage;
            $currentEmp_img = checkIsFileExists($filePath);
            ?>
            <img src="<?php echo $currentEmp_img; ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
            <p><?php echo $name = ucwords($this->session->username); ?></p>
            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            <a href="<?php echo site_url('modules') ?>">Modules </a>
        </div>

    </div>

    <!-- search form -->
    <form action="#" method="get" class="sidebar-form">

        <?php

        echo form_dropdown('company', drill_down_navigation_dropdown(), $companyID.'-'.$companyType, 'id="parentCompanyID", onchange="change_fetchcompany($(\'#parentCompanyID option:selected\').val(),$(\'#parentCompanyID option:selected\').text())" class="form-control select2", required'); ?>
    </form>
    <!-- /.search form -->
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
        <!--<li class="header">MAIN NAVIGATION</li>-->


        <?php



        if ($data) {
            $x = 0;
            //echo '<pre>';print_r($data); echo '</pre>'; die();
            foreach ($data as $parent) {
                $x++;
                $active = '';
                if ($x == 1) {
                    $active = 'active';
                }
                if ($parent['levelNo'] == 0) {
                    if ($parent['isSubExist'] == 0) {
                        ?>
                        <li class="<?php echo $active; ?>"><a href="#"
                                                              onclick="fetchPage('<?php echo $parent['url'] ?>','<?php echo $parent['pageID'] ?>','<?php echo $parent['pageTitle'] ?>')"><i
                                class="<?php echo $parent['pageIcon'] ?>"></i>
                            <span><?php echo $parent['description'] ?></span></a>
                        <?php
                    } else {
                        ?>
                        <li class="treeview">
                            <a href="#">
                                <i class="<?php echo $parent['pageIcon'] ?>" aria-hidden="true"></i>
                                <span>
                                    <?php echo $parent['description'] ?>
                                </span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu" style="display: none;">
                                <?php foreach ($data as $child) {
                                    if ($child['levelNo'] == 1 && $parent['navigationMenuID'] == $child['masterID']) {
                                        if ($child['isSubExist'] == 0) {
                                            if (($child['navigationMenuID'] == 171 || $child['navigationMenuID'] == 172) || ($child['navigationMenuID'] == 306 || $child['navigationMenuID'] == 306 ||$child['navigationMenuID'] == 320)) {
                                                ?>
                                                <li>
                                                    <a href="<?php echo site_url($child['url']); ?>"><i
                                                            class="<?php echo $child['pageIcon'] ?>"></i><?php echo $child['description'] ?>
                                                    </a></li>
                                                <?php

                                            } else {
                                                ?>
                                                <li>
                                                    <a onclick="fetchPage('<?php echo $child['url'] ?>','<?php echo $child['pageID'] ?>','<?php echo $child['pageTitle'] ?>')"><i
                                                            class="<?php echo $child['pageIcon'] ?>"></i><?php echo $child['description'] ?>
                                                    </a></li>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <li class="">
                                                <a href="#"><i
                                                        class="<?php echo $child['pageIcon'] ?> "></i> <?php echo $child['description'].' --sf' ?>
                                                    <i
                                                        class="fa fa-angle-left pull-right"></i></a>
                                                <ul class="treeview-menu" style="display: none;">
                                                    <?php foreach ($data as $child2) {
                                                        if ($child2['levelNo'] == 2 && $child['navigationMenuID'] == $child2['masterID']) {
                                                           // echo '<pre>';print_r( $child2['url']); echo '</pre>'; die();
                                                            ?>
                                                            <li>
                                                                <a onclick="fetchPage('sgggfgfsgf gchg','<?php echo $child2['pageID'] ?>','<?php echo $child2['pageTitle'] ?>')"><i
                                                                        class="<?php echo $child2['pageIcon'] ?>"></i> <?php echo $child2['description'] ?>
                                                                </a>
                                                            </li>
                                                            <?php


                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                            </li>

                                            <?php


                                        }
                                    }

                                } ?>


                            </ul>
                        </li>
                        <?php

                    }

                }
            }
        }

        ?>
    </ul>
</section>
<!-- /.sidebar -->
