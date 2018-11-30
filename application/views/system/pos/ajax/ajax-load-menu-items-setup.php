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
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <button class="btn btn-link" onclick="toggleMenuCategorySetup();" rel="tooltip" title="Go Back"><i
                            class="fa fa-arrow-left fa-3x" aria-hidden="true"></i> <!--go Back--></button>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <img src="<?php echo base_url($category['image']); ?>" class="img-rounded" alt="no Image"
                         height="100">
                    <?php //print_r($category); ?>
                    <span style="font-size: 20px"><?php echo $category['menuCategoryDescription']; ?></span>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <button onclick="add_menuSeupModal('<?php echo !empty($id) && isset($id) ? $id : 0; ?>')"
                            class="btn btn-xs btn-default" type="button"><i class="fa fa-plus" aria-hidden="true"></i>
                        <?php echo $this->lang->line('pos_config_add_menu'); ?><!--Add Menu-->
                    </button>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <form id="form_kot_common_location" class="form-horizontal">
                        <input type="hidden" name="warehouseMenuCategoryID" id="warehouseMenuCategoryID"
                               value="<?php echo !empty($category['autoID']) ? $category['autoID'] : 0 ?>">
                        <input type="hidden" name="warehouse_autoID" id="warehouse_autoID"
                               value="<?php echo !empty($category['warehouseID']) ? $category['warehouseID'] : 0 ?>">
                        <label>KOT Location</label>
                        <?php
                        $kot_dropDown = kot_dropDown_category($category['warehouseID']);
                        echo $kot_dropDown;
                        ?>
                        <br>

                        <button class="btn btn-xs btn-default" type="button" onclick="kot_apply_to_all()">Apply To All
                        </button>
                    </form>
                </div>


            </div>
            <hr style="margin:2px 0px;">
            <div style="padding: 5px; border:1px dashed #afafaf">
                <table class="<?php echo table_class_pos(1) ?>" id="warehouseMenuItemTable" style="width: 100%">
                    <thead>
                    <tr>
                        <th style="max-width: 60px;">&nbsp;</th>
                        <th><?php echo $this->lang->line('pos_config_item_name'); ?><!--Item Name--></th>
                        <th><?php echo $this->lang->line('common_price'); ?><!--Price--></th>
                        <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                        <th><?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--></th>
                        <th><?php echo $this->lang->line('pos_config_tax_isEnabled'); ?><!-- is Tax Enabled --> <i
                                rel="tooltip" class="fa fa-question-circle"
                                title="Double Entry: if you enable this, Tax will be handle separately in General Ledger, if its disabled the Tax Amount that is already entered in the menu will be calculated as Revenue."></i>
                        </th>

                        <th><?php echo $this->lang->line('pos_config_kot_kot'); ?><!--KOT --></th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <script>
                    $(document).ready(function (e) {
                        loadMenuItem_setup_table(<?php echo !empty($id) && isset($id) ? $id : 0; ?>);
                    });
                    $("#menuItemTable").dataTable();
                </script>
            </div>
        </div>
    </div>
    <script>
        function kot_apply_to_all() {
            var kotID_common = $("#kotID_common").val();
            if (kotID_common > 0) {
                swal({
                        title: "Are you sure?",
                        text: "You want to apply this to all Menu?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Confirm"
                    },
                    function () {
                        $.ajax({
                            type: 'post',
                            dataType: 'json',
                            data: $("#form_kot_common_location").serialize(),
                            url: "<?php echo site_url('Pos_config/kot_apply_to_all'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                if (data['error'] == 0) {
                                    loadMenuItemsSetup_withoutToggle(data['id']);
                                }
                                myAlert(data['status'], data['message']);
                            }, error: function () {
                                myAlert('e', 'An Error Occurred! Please Try Again.');
                                stopLoad();
                            }
                        })
                    });
            } else {
                swal("", "Please select KOT Location", "error");
            }
        }
    </script>
    <?php
} else { ?>

    <div class="alert alert-danger">
        <strong><?php echo $this->lang->line('pos_config_invalid_category'); ?><!--Invalid Category--></strong>
    </div>

<?php } ?>


