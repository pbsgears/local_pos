<?php if (isset($category) && !empty($category)) {

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('pos_config_restaurant', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('calendar', $primaryLanguage);
    ?>
    <div class="row">
        <div class="col-md-12">

            <hr style="margin:2px 0px;">
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <button class="btn btn-link" onclick="toggleMenuCategory();" rel="tooltip" title="Go Back"><i class="fa fa-arrow-left fa-3x" aria-hidden="true"></i> <!--go Back--></button>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <img src="<?php echo base_url($category['image']); ?>" class="img-rounded" alt="no Image"
                         height="100">
                    <?php //print_r($category); ?>
                    <span style="font-size: 20px"><?php echo $category['menuCategoryDescription']; ?></span>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <button onclick="add_menuModal('<?php echo !empty($id) && isset($id) ? $id : 0; ?>')"
                            class="btn btn-xs btn-default" type="button"><i class="fa fa-plus" aria-hidden="true"></i> <?php echo $this->lang->line('pos_config_add_menu');?><!--Add Menu-->
                    </button>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">

                </div>

            </div>
            <hr style="margin:2px 0px;">
            <div style="padding: 5px; border:1px dashed #afafaf">
                <table class="<?php echo table_class_pos(1) ?>" id="menuItemTable" style="width: 100%;">
                    <thead>
                    <tr>
                        <th></th>
                        <th style="max-width: 60px;">&nbsp;</th>
                        <th><?php echo $this->lang->line('pos_config_item_name');?><!--Item Name--></th>
                        <th><?php echo $this->lang->line('pos_config_size');?><!--Size--></th>
                        <th><?php echo $this->lang->line('pos_config_price');?><!--Price--></th>
                        <th><?php echo $this->lang->line('pos_config_Cost');?><!--Cost--></th>
                        <th><?php echo $this->lang->line('common_status');?><!--Status--></th>
                        <th><?php echo $this->lang->line('pos_config_is_pack');?><!--is Pack--></th>
                        <th><?php echo $this->lang->line('pos_config_is_veg');?><!--is Veg--></th>
                        <th><?php echo $this->lang->line('pos_config_is_add_on');?><!--is Add On--></th>
                        <th style="width: 70px;"><?php echo $this->lang->line('pos_config_sort_order');?><!--sort Order--></th>
                        <th><?php echo $this->lang->line('pos_config_gl_code');?><!-- GL Code --></th>
                        <th>Show Image</th>
                        <th style="width: 180px;">&nbsp;</th>
                    </tr>
                    </thead>
                </table>
                <script>
                    $(document).ready(function (e) {
                        loadMenuItem_table(<?php echo !empty($id) && isset($id) ? $id : 0; ?>);
                    });
                </script>
            </div>
        </div>
    </div>
    <?php
} else { ?>
    <div class="alert alert-danger">
        <strong><?php echo $this->lang->line('pos_config_invalid_category');?><!--Invalid Category--></strong>
    </div>
<?php } ?>






