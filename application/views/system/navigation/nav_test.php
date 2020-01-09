<?php $CI =& get_instance();
$companyID = $CI->common_data['company_data']['company_id'];
$empID = current_userID();
$data = $CI->db->query("SELECT srp_erp_navigationusergroupsetup.* FROM srp_erp_employeenavigation INNER JOIN srp_erp_navigationusergroupsetup ON srp_erp_employeenavigation.userGroupID = srp_erp_navigationusergroupsetup.userGroupID WHERE empID={$empID} AND companyID={$companyID} Order by levelNo,sortOrder ASC ")->result_array();

?>
<!-- sidebar: style can be found in sidebar.less -->
<section class="sidebar" style="width: 250px">

    <ul class="sidebar-menu">
        <?php if ($data) {
            $x=0;

            foreach ($data as $parent) {
                $x++;
                $active='';
                if($x==1){
                    $active='active';
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
                                <span><?php echo $parent['description'] ?></span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu" style="display: none;">
                                <?php foreach ($data as $child) {
                                    if ($child['levelNo'] == 1 && $parent['navigationMenuID'] == $child['masterID']) {
                                        if ($child['isSubExist'] == 0) {
                                            ?>
                                            <li>
                                                <a onclick="fetchPage('<?php echo $child['url'] ?>','<?php echo $child['pageID'] ?>','<?php echo $child['pageTitle'] ?>')"><i
                                                        class="<?php echo $child['pageIcon'] ?>"></i><?php echo $child['description'] ?>
                                                </a></li>
                                            <?php
                                        } else {
                                            ?>
                                            <li class="">
                                                <a href="#"><i
                                                        class="<?php echo $child['pageIcon'] ?> "></i> <?php echo $child['description'] ?>
                                                    <i
                                                        class="fa fa-angle-left pull-right"></i></a>
                                                <ul class="treeview-menu" style="display: none;">
                                                    <?php foreach ($data as $child2) {
                                                        if ($child2['levelNo'] == 2 && $child['navigationMenuID'] == $child2['masterID']) {
                                                            ?>

                                                            <li>
                                                                <a onclick="fetchPage('<?php echo $child2['url'] ?>','<?php echo $child2['pageID'] ?>','<?php echo $child2['pageTitle'] ?>')"><i
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

