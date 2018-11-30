<style>
    .imgContainer-sm, #groupingContainer {
        border: 1px dashed #9b9b9b;
        border-radius: 4px;
        padding: 4px;
    }

    .lbl_cus1 {
        font-size: 11px !important;
    }
</style>
<?php // print_r($master);
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div style="display:  block; height: 20px;">
    <button class="btn btn-xs btn-default pull-right" onclick="togglePackForm()">
        <i id="ico_packFormBtn"
           class="fa fa-plus"></i> <?php echo $this->lang->line('pos_config_add_item_to_Pack_Combot');?><!--Add item to Pack / Combo-->
    </button>
</div>
<div style="padding:10px; margin: 4px 0px; background-color: #ffffff; display: none;" id="div_packForm">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-lg-6">
                <form class="form-horizontal" id="packConfigFrm">

                    <input type="hidden" name="PackMenuID" id="PackMenuID"
                           value="<?php echo $master['menuMasterID'] ?>">
                    <input type="hidden" name="menuCategoryID" id="pack_menuCategoryID" value="0">
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="pack_item"><?php echo $this->lang->line('pos_config_select_an_item');?><!--Select an Item--></label>
                        <div class="col-md-8">
                            <?php echo form_dropdown('pack_item', get_get_itemPackItem(), '', 'id="pack_item" class="form-control" style="width: 100%" onchange="packLoadItmDetail(this)"'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label" for="radios"><?php echo $this->lang->line('pos_config_optional');?><!--Optional-->?</label>
                        <div class="col-md-4">
                            <label class="radio-inline" for="req_1">
                                <input type="radio" name="isRequired" id="req_1" value="0"
                                       onchange="isOptionQtyRequired(this)" checked="checked">
                                <?php echo $this->lang->line('common_yes');?><!--Yes-->
                            </label>
                            <label class="radio-inline" for="req_0">
                                <input type="radio" name="isRequired" onchange="isOptionQtyRequired(this)" id="req_0"
                                       value="1">
                                <?php echo $this->lang->line('common_no');?><!--No-->
                            </label>

                        </div>
                    </div>

                    <!--<div class="form-group" id="maxQtyInput">
                        <label for="numberOfOptionalItem" class="col-sm-4 control-label">Max qty </label>
                        <div class="col-sm-3">
                            <input type="number" max="3" maxlength="1" class="form-control" id="numberOfOptionalItem"
                                   name="qty"
                                   placeholder="" value="1">

                        </div>
                    </div>-->


                    <div class="form-group">
                        <label class="col-md-4 control-label">&nbsp;</label>
                        <div class="col-md-8">
                            <button class="btn btn-sm btn-default" type="button" onclick="save_packItems()"><i
                                    class="fa fa-plus"></i> <?php echo $this->lang->line('pos_config_add_to_pack');?><!--Add to Pack-->
                            </button>
                            <span id="packSaveLoader"></span>
                        </div>
                    </div>

                </form>
            </div>

            <div class="col-md-6 col-lg-6">
                <div class="well well-sm" style="border-radius: 0px; margin-bottom:0px;">
                    <span style="color:#606060"><strong><?php echo $this->lang->line('pos_config_item_detail');?><!--Item Detail--> </strong></span>
                    <hr style="margin:4px 0px">
                    <div style="background-color: #ffffff; padding:5px;">
                        <div class="container-fluid">
                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                <img class="img-thumbnail" id="pack_img_thum" style="width:100%; max-width: 100px;"
                                     src="<?php echo base_url('images/no-image.png') ?>" alt="">
                            </div>
                            <div class="col-md-8 col-lg-8">
                                <p> <?php echo $this->lang->line('common_item');?><!--Item--> : <strong><span id="pax_itemName"></span></strong></p>
                                <p> <?php echo $this->lang->line('common_category');?><!--Category--> : <strong><span id="pax_itemCategory"></span> </strong></p>
                                <!-- <p> Selling Price : <span id="pax_itemSellingPrice"></span>  </p>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div style="padding:1px; margin: 4px 0px; ">
    <div style=" background-color: #ffffff; padding:9px; border:1px dashed #bfbfbf;">

        <div class="row">
            <div class="col-md-4 col-lg-4">
                <h4>
                    <?php echo $master['menuMasterDescription'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <br>
                    <small>Price : <?php echo number_format($master['sellingPrice'], 2) ?></small>
                    <br>
                    <span class="">
                        <img class="img-thumbnail" src="<?php echo $master['menuImage'] ?>"
                             style="height: 100px; max-width: 300px;"
                             alt=""/>
                    </span>

                </h4>
            </div>
            <div class="col-md-8 col-lg-8">


                <h5>
                    <?php echo $this->lang->line('pos_config_group_items');?><!--Group Items--> <span class="pull-right"><button class="btn btn-xs btn-default" type="button"
                                                                 onclick="groupingBtnClick()"><i
                                class="fa fa-plus"></i></button></span>
                </h5>
                <form class="form-horizontal" id="frm_menuGroupConfig">
                    <input type="hidden" value="0" id="frm_status" value="0">
                    <input type="hidden" value="0" name="menuID_groping" id="menuID_groping">
                    <input type="hidden" value="0" name="groupMasterID" id="groupMasterID">
                    <div id="groupingContainer" style="display: none">&nbsp;</div>
                </form>
                <hr style="margin:2px 0px;">
                <table class="<?php echo table_class_pos(1) ?>" id="packConfigCategoryTbl">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('pos_config_group_name');?><!--Group Name--></th>
                        <th style="max-width:150px;"><?php echo $this->lang->line('pos_config_number_of_optional_Item');?><!--Number of Optional Item--></th>
                        <th style="max-width:75px;">&nbsp;</th>
                    </tr>
                    </thead>
                </table>
            </div>

        </div>


    </div>
</div>

<div style="padding:1px; margin: 4px 0px; ">
    <div style=" background-color: #ffffff; padding:9px; border:1px dashed #bfbfbf;">
        <?php echo $this->lang->line('pos_config_item_list');?><!--Item List-->
        <hr style="margin:2px 0px;">
        <div id="table_container_itemList">
            <table class="<?php echo table_class_pos(1) ?>" id="packConfigTbl">
                <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo $this->lang->line('pos_config_item_name');?><!--Item Name--></th>
                    <th><?php echo $this->lang->line('common_group');?><!--Group--></th>
                    <th style="width:75px;"><?php echo $this->lang->line('pos_config_optional');?><!--is Optional--></th>
                    <th style="max-width:75px;">&nbsp;</th>
                </tr>
                </thead>

            </table>
        </div>
    </div>
</div>

<script>


    $(document).ready(function (e) {
        $("#pack_item").select2({});
        load_pack_items(<?php echo $master['menuMasterID']?>);
        //load_pack_category(<?php //echo $master['menuMasterID']?>);
        load_packGroup_table(<?php echo $master['menuMasterID']?>);
    });

    function togglePackForm() {
        $("#div_packForm").toggle();
    }

    function isOptionQtyRequired(tmpValue) {
        $("#maxQtyInput").toggle();
        if (tmpValue.value == 0) {
            $("#numberOfOptionalItem").val(1);
        } else {
            $("#numberOfOptionalItem").val(0);
        }
    }

    function loadGropingContent() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/loadGropingContent') ?>",
            data: {menuID: $("#menuID_groping").val()},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                //$("#groupingContainer").html('<div style="text-align: center; margin:20px 0px;"><i class="fa fa-refresh fa-spin fa-2x"></i> Loading</div>');
            },
            success: function (data) {
                $("#groupingContainer").html(data);
                $("#frm_status").val(1);
                setTimeout(function () {
                    $("#groupDescription").focus();
                }, 500);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function groupingBtnClick() {
        $("#groupingContainer").toggle(function () {
            if ($(this).is(":visible")) {
                if ($("#frm_status").val() == 0) {
                    loadGropingContent();
                }
            }
        });
    }

    function save_menuGroup() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/save_menuGroup') ?>",
            data: $("#frm_menuGroupConfig").serialize(),
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad()
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    load_pack_items2(data['packMenuID']);
                    load_packGroup_table(data['packMenuID']);
                    $("#frm_menuGroupConfig").trigger("reset");
                    myAlert('s', data['message']);
                } else {
                    myAlert('e', data['message']);
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_pos_packConfigItem(menuID, packGroupDetailID) {
        var id = '<?php echo $master['menuMasterID']?>';
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("Pos_config/delete_pos_packItem"); ?>',
                    dataType: 'json',
                    data: {menuID: menuID, id: id, packGroupDetailID: packGroupDetailID},
                    success: function (data) {
                        if (data['error'] == 0) {
                            myAlert('s', data['message']);
                            $("#packGroup_" + menuID).hide();
                        } else if (data['error'] == 1) {
                            myAlert('d', data['message']);
                        }
                        modalFix();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        myAlert('e', '<br>Message: ' + errorThrown);
                        modalFix();
                    }
                });
            } else {
                modalFix();
            }
        });
    }
</script>