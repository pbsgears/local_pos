<?php
/*echo '<pre>';
print_r($menuCategoryList);
echo '</pre>';*/
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<style>
    .clsMenuCategory {
        cursor: pointer;
    }
</style>
<div><!--class="container"-->
    <div class="row">
        <div class="col-md-12" style="background-color: #ffffff;">
            <div id="menuCategoryTitle">
                <span style="font-size: 16px; font-weight: 700">
                    <i class="fa fa-cutlery" style="color:goldenrod;" aria-hidden="true"></i>
                    <?php echo $this->lang->line('pos_config_menu_categories'); ?><!--Menu Categories--> &nbsp;&nbsp;
                </span>
                <button class="btn btn-xs btn-default" type="button" onclick="add_menuCategory_setupModal();">
                    <?php echo $this->lang->line('pos_config_add_category'); ?><!--Add Category-->
                </button>
                <!--<span class="pull-right">
                    <i onclick="toggleMenuCategorySetup();" class="fa fa-toggle-on fa-2x" style="color:#a6a6a6"
                       aria-hidden="true"></i></span>-->
                <hr>
            </div>
            <div id="menuCategoryList_setup">
                <table class="<?php echo table_class_pos(2); ?>" id="table_menuCategory">
                    <thead>
                    <tr>
                        <!--<th>#</th>-->
                        <th style="width:50px;"><?php echo $this->lang->line('common_image'); ?><!--Image--></th>
                        <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <!-- <th><?php /*echo $this->lang->line('common_status');*/ ?><!--Status--><!--</th>-->
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (isset($menuCategoryList) && !empty($menuCategoryList)) {
                        //print_r($menuCategoryList);
                        $i = 1;
                        foreach ($menuCategoryList as $menuCategory) {
                            ?>
                            <tr id="menuRow_<?php echo $menuCategory['autoID'] ?>">
                                <!--<td><?php /*echo $i;
                                    $i++; */ ?></td>-->
                                <td class="clsMenuCategory"
                                    onclick="loadMenuItemsSetup('<?php echo $menuCategory['autoID'] ?>')">
                                    <img src="<?php echo base_url($menuCategory['image']) ?>"
                                         style="height: 25px; width:25px;"
                                         alt="<?php echo $menuCategory['menuCategoryDescription'] ?>">
                                </td>
                                <td class="clsMenuCategory"
                                    onclick="loadMenuItemsSetup('<?php echo $menuCategory['autoID'] ?>')">
                                    <span style="font-size:14px !important;">
                                        <?php echo $menuCategory['menuCategoryDescription'] ?>
                                    </span>
                                </td>
                                <!-- <td class="clsMenuCategory"
                                    onclick="loadMenuItemsSetup('<?php /*echo $menuCategory['autoID'] */ ?>')"
                                    style="text-align: center; font-size:13px !important;">
                                    <?php /*if ($menuCategory['Active'] == 1) { */ ?>
                                        <span class="label label-success"><?php /*echo $this->lang->line('common_active');*/ ?> </span>
                                    <?php /*} else { */ ?>
                                        <span class="label label-default"><?php /*echo $this->lang->line('common_in_active');*/ ?> </span>
                                    <?php /*} */ ?>
                                </td>-->
                                <td style="text-align: center;">

                                    <?php if ($menuCategory['Active'] == 1) { ?>
                                        <input type="checkbox"
                                               id="menueCategoryIsactive_<?php echo $menuCategory['autoID'] ?>"
                                               name="menueCategoryIsactive"
                                               onchange="changeMenueCategoryIsactive(<?php echo $menuCategory['autoID'] ?>)"
                                               data-size="mini"
                                               data-on-text="<?php echo $this->lang->line('common_active'); ?>"
                                               data-handle-width="45" data-off-color="danger" data-on-color="success"
                                               data-off-text="<?php echo $this->lang->line('pos_config_deactive'); ?>"
                                               data-label-width="0" checked><!--Active--><!--Deactive-->
                                    <?php } else if ($menuCategory['Active'] == 0) { ?>
                                        <input type="checkbox"
                                               id="menueCategoryIsactive_<?php echo $menuCategory['autoID'] ?>"
                                               name="menueCategoryIsactive"
                                               onchange="changeMenueCategoryIsactive(<?php echo $menuCategory['autoID'] ?>)"
                                               data-size="mini" data-on-text="Active" data-handle-width="45"
                                               data-off-color="danger" data-on-color="success" data-off-text="Deactive"
                                               data-label-width="0">
                                    <?php } ?>
                                    &nbsp;
                                    |
                                    &nbsp;
                                    <button class="btn btn-danger btn-xs"
                                            onclick="delete_menue_Category(<?php echo $menuCategory['autoID'] ?>)"
                                            rel="tooltip" title="" data-original-title="Delete"><i
                                            class="fa fa-trash"></i></button>

                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="outputListOfMenus_setup" style="display: none; background-color: #ffffff;">
        <!--Menu Content  -->
    </div>
</div>


<script>
    $(document).ready(function (e) {
        $("#table_menuCategory").dataTable();
        $("#segmentConfigID_menuSetup").val('<?php echo $id ?>');
        setTimeout(function () {
            $("[name='menueCategoryIsactive']").bootstrapSwitch();
        }, 100);
        $('#table_menuCategory').on('page.dt', function () {
            setTimeout(function () {
                $("[name='menueCategoryIsactive']").bootstrapSwitch();
            }, 100);
        });
        $("#outletConfigModalTitle").html('<?php echo isset($outletName) ? $outletName : 'Menu'; ?>');
    });

    $('select').on('change', function () {
        $("#segmentConfigID_menuSetup").val('<?php echo $id ?>');
        setTimeout(function () {
            $("[name='menueCategoryIsactive']").bootstrapSwitch();
        }, 100);
        $('#table_menuCategory').on('page.dt', function () {
            setTimeout(function () {
                $("[name='menueCategoryIsactive']").bootstrapSwitch();
            }, 100);
        });
    });
</script>