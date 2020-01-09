<?php
/**
 * Author : Shahmy
 * Module : Navigation Menu
 * Modified by  : Shafri
 * Created on: 28-June-2017
 * Description : Language File Added by Shafri, isExternalLink added
 *
 */

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('navigation_menu', $primaryLanguage);

$companyID = current_companyID();
$companyType = $this->session->userdata("companyType");
$empID = current_userID();
$detail = "";

$wareHouseID = $this->db->select('wareHouseID,')->from('srp_erp_warehouse_users')->where(array('userID' => $empID, 'companyID' => $companyID, 'isActive' => 1))
    ->get()->row('wareHouseID');
$imagePath_arr = $this->db->select('imagePath,isLocalPath')->from('srp_erp_pay_imagepath')->get()->row_array();
if ($imagePath_arr['isLocalPath'] == 1) {
    $imagePath = base_url() . 'images/users/';
} else { // FOR SRP ERP USERS
    $imagePath = $imagePath_arr['imagePath'];
}
$company = $this->db->query("select * from srp_erp_company WHERE company_id={$companyID}")->row_array();

$this->session->set_userdata("companyID", trim($companyID));
$this->session->set_userdata("ware_houseID", trim($wareHouseID));
$this->session->set_userdata("imagePath", trim($imagePath));
$this->session->set_userdata("company_code", trim($company['company_code']));
$this->session->set_userdata("company_name", trim($company['company_name']));
$this->session->set_userdata("company_logo", trim($company['company_logo']));
$detail = "";
if ($companyType == 1) {

    $detail = $this->db->query("SELECT srp_erp_navigationmenus.languageID, srp_erp_navigationusergroupsetup.*,template.TempPageNameLink, srp_erp_navigationmenus.isExternalLink FROM srp_erp_employeenavigation INNER JOIN srp_erp_navigationusergroupsetup ON srp_erp_employeenavigation.userGroupID = srp_erp_navigationusergroupsetup.userGroupID INNER JOIN srp_erp_navigationmenus ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID   LEFT JOIN (SELECT srp_erp_templates.TempMasterID,srp_erp_templates.navigationMenuID,srp_erp_templatemaster.TempPageNameLink FROM srp_erp_templates LEFT JOIN srp_erp_templatemaster ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID WHERE srp_erp_templates.companyID={$companyID}  ) AS template ON (template.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID) WHERE empID={$empID} AND srp_erp_employeenavigation.companyID={$companyID} Order by levelNo,sortOrder ASC ")->result_array();
} else {

    $sql = "SELECT srp_erp_navigationmenus.languageID,srp_erp_companysubgroupnavigationsetup.*,template.TempPageNameLink FROM srp_erp_companysubgroupnavigationsetup LEFT JOIN srp_erp_navigationmenus ON srp_erp_navigationmenus.navigationMenuID = srp_erp_companysubgroupnavigationsetup.navigationMenuID
LEFT JOIN srp_erp_companysubgroupmaster ON srp_erp_companysubgroupnavigationsetup.compaySubGroupID = srp_erp_companysubgroupmaster.companySubGroupID
LEFT JOIN srp_erp_companysubgroupemployees ON srp_erp_companysubgroupemployees.companySubGroupID = srp_erp_companysubgroupmaster.companySubGroupID 
LEFT JOIN (SELECT srp_erp_companysubgrouptemplates.TempMasterID,srp_erp_companysubgrouptemplates.navigationMenuID,srp_erp_templatemaster.TempPageNameLink,companySubGroupID  FROM srp_erp_companysubgrouptemplates LEFT JOIN srp_erp_templatemaster ON srp_erp_templatemaster.TempMasterID = srp_erp_companysubgrouptemplates.TempMasterID ) AS template ON (template.navigationMenuID = srp_erp_companysubgroupnavigationsetup.navigationMenuID /*AND template.companySubGroupID = srp_erp_companysubgroupmaster.companySubGroupID*/)
 WHERE srp_erp_companysubgroupemployees.EmpID={$empID} AND companyGroupID={$companyID} AND isGroup = 1 Order by levelNo,sortOrder ASC ";
    $detail = $this->db->query($sql)->result_array();
}
$wareHouseID = $this->db->select('wareHouseID,')->from('srp_erp_warehouse_users')->where(array('userID' => $empID, 'companyID' => $companyID, 'isActive' => 1))
    ->get()->row('wareHouseID');
$imagePath_arr = $this->db->select('imagePath,isLocalPath')->from('srp_erp_pay_imagepath')->get()->row_array();
if ($imagePath_arr['isLocalPath'] == 1) {
    $imagePath = base_url() . 'images/users/';
} else { // FOR SRP ERP USERS
    $imagePath = $imagePath_arr['imagePath'];
}

$data = $detail;

function handleIfLanguageNotExist($languageRow, $translation)
{
    if (!empty(trim($translation))) {
        return $translation;
    } else {
        return $languageRow;
    }
}

?>
<script>
    $(document).ready(function (e) {
        $('.navMenu').click(function (e) {
            $('li').removeClass('act');
            //$(this).closest('li').addClass('active');
            $(this).parent('li').addClass('act');
            //console.log($(this).parent('li').html());
        })
    })
</script>
<aside class="main-sidebar">
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <?php
                $filePath = imagePath() . $this->session->EmpImage;
                $currentEmp_img = checkIsFileExists($filePath);
                ?>
                <img src="<?php echo $currentEmp_img; ?>" class="" alt="User Image"> <!--img-circle-->
            </div>
            <div class="pull-left info">
                <p><?php echo $name = ucwords($this->session->username); ?></p>
                <!-- <a href="#"><i class="fa fa-circle text-success"></i> Online</a>-->
            </div>

        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <?php
            echo form_dropdown('company', drill_down_navigation_dropdown(), $companyID . '-' . $companyType, 'id="parentCompanyID" onchange="change_fetchcompany($(\'#parentCompanyID option:selected\').val(),$(\'#parentCompanyID option:selected\').text())" class="form-control select2", required'); ?>
        </form>
        <!-- /.search form -->

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <?php if ($data) {
                $x = 0;

                foreach ($data as $parent) {
                    $x++;
                    $active = '';
                    if ($x == 1) {
                        $active = 'active';
                    }
                    if ($parent['levelNo'] == 0) {
                        if ($parent['isSubExist'] == 0) {
                            ?>
                            <li class="<?php //echo $active; ?>">
                            <a href="#" class="navMenu"
                               onclick="fetchPage('<?php echo $parent['TempPageNameLink'] ?>','<?php echo $parent['pageID'] ?>','<?php echo $parent['pageTitle'] ?>')">
                                <i class="<?php echo $parent['pageIcon'] ?>"></i>
                                <span>
                                    <?php
                                    //echo $parent['description'];
                                    $inputMenuName = language_string_conversion($parent['description'], $parent['languageID']);
                                    $translation = $this->lang->line('navigation_menu_' . $inputMenuName);
                                    echo handleIfLanguageNotExist($parent['description'], $translation);
                                    //echo $inputMenuName;
                                    ?>
                                </span>
                            </a>
                            <?php
                        } else {
                            ?>
                            <li class="treeview">
                                <a href="#" class="">
                                    <i class="<?php echo $parent['pageIcon'] ?>" aria-hidden="true"></i>
                                    <span>
                                        <?php
                                        $inputMenuName = language_string_conversion($parent['description'], $parent['languageID']);
                                        $translation = $this->lang->line('navigation_menu_' . $inputMenuName);
                                        echo handleIfLanguageNotExist($parent['description'], $translation);
                                        //echo $inputMenuName;
                                        ?>
                                    </span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu" style="display: none;">
                                    <?php foreach ($data as $child) {
                                        if ($child['levelNo'] == 1 && $parent['navigationMenuID'] == $child['masterID']) {
                                            if ($child['isSubExist'] == 0) {
                                                if ($child['isExternalLink'] == 1) {
                                                    ?>
                                                    <li>
                                                        <a class=""
                                                           href="<?php echo site_url($child['TempPageNameLink']); ?>"><i
                                                                class="<?php echo $child['pageIcon'] ?>"></i>
                                                            <?php
                                                            $inputMenuName = language_string_conversion($child['description'], $child['languageID']);
                                                            $translation = $this->lang->line('navigation_menu_' . $inputMenuName);
                                                            echo handleIfLanguageNotExist($child['description'], $translation);


                                                            ?>
                                                        </a></li>
                                                    <?php

                                                } else {
                                                    ?>
                                                    <li>
                                                        <a class="navMenu"
                                                           onclick="fetchPage('<?php echo $child['TempPageNameLink'] ?>','<?php echo $child['pageID'] ?>','<?php echo $child['pageTitle'] ?>')">
                                                            <i class="<?php echo $child['pageIcon'] ?>"></i>
                                                            <?php
                                                            $inputMenuName = language_string_conversion($child['description'], $child['languageID']);
                                                            $translation = $this->lang->line('navigation_menu_' . $inputMenuName);
                                                            echo handleIfLanguageNotExist($child['description'], $translation);

                                                            ?>
                                                        </a>
                                                    </li>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <li>
                                                    <a href="#" class="">
                                                        <i class="<?php echo $child['pageIcon'] ?> "></i>
                                                        <?php
                                                        $inputMenuName = language_string_conversion($child['description'], $child['languageID']);
                                                        $translation = $this->lang->line('navigation_menu_' . $inputMenuName);
                                                        echo handleIfLanguageNotExist($child['description'], $translation);
                                                        ?>
                                                        <i class="fa fa-angle-left pull-right"></i></a>
                                                    <ul class="treeview-menu" style="display: none;">
                                                        <?php foreach ($data as $child2) {
                                                            if ($child2['levelNo'] == 2 && $child['navigationMenuID'] == $child2['masterID']) {
                                                                ?>
                                                                <li class="">
                                                                    <a class="navMenu"
                                                                       onclick="fetchPage('<?php echo $child2['TempPageNameLink'] ?>','<?php echo $child2['pageID'] ?>','<?php echo $child2['pageTitle'] ?>')"><i
                                                                            class="<?php echo $child2['pageIcon'] ?>"></i>
                                                                        <?php
                                                                        $inputMenuName = language_string_conversion($child2['description'], $child2['languageID']);
                                                                        $translation = $this->lang->line('navigation_menu_' . $inputMenuName);
                                                                        echo handleIfLanguageNotExist($child2['description'], $translation);
                                                                        ?>
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

</aside>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content" id="ajax_body_container">