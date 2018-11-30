<style>
    .margin-bottom {
        margin-bottom: 2px;
    }
</style>

<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>

    <!-- Text input-->
    <div class="form-group margin-bottom">
        <label class="col-md-4 control-label" for="groupDescription"><?php echo $this->lang->line('pos_config_group_name');?><!--Group Name--></label>
        <div class="col-md-8">
            <input id="groupDescription" name="groupDescription" type="text" placeholder="<?php echo $this->lang->line('pos_config_group_name');?>"><!--Group name-->
            <span style="font-weight: 600"><?php echo $this->lang->line('pos_config_qty');?><!--Qty--> </span><input id="group_qty" style="width: 50px;" name="qty"
                                                             type="number" maxlength="2" value="1" placeholder="">
        </div>
    </div>


    <!-- Multiple Checkboxes -->
    <div class="form-group margin-bottom">
        <label class="col-md-4 control-label" for="checkboxes"><?php echo $this->lang->line('pos_config_menu_items');?><!--Menu Items--></label>
        <div class="col-md-4">

            <?php //print_r($menuItemList); ?>
            <?php if (isset($menuItemList) && !empty($menuItemList)) {
                foreach ($menuItemList as $item) {

                    ?>
                    <div class="checkbox">
                        <label for="menuItem_<?php echo $item['id'] ?>">
                            <input type="checkbox" class="margin-bottom" name="menuItems[]"
                                   id="menuItem_<?php echo $item['id'] ?>" value="<?php echo $item['menuID'] ?>_<?php echo $item['id'] ?>">
                            <?php echo $item['menuMasterDescription'] ?>
                        </label>
                    </div>
                    <?php
                }
            }
            ?>


        </div>
    </div>

    <!-- Button -->
    <div class="form-group margin-bottom">
        <label class="col-md-4 control-label" for="singlebutton">&nbsp;</label>
        <div class="col-md-4">
            <button type="button" onclick="save_menuGroup()" class="btn btn-primary btn-xs"><?php echo $this->lang->line('pos_config_add_group');?><!--Add Group--></button>
        </div>
    </div>
