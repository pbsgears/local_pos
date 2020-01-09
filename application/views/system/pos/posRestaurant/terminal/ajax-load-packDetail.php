<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>

<style>
    .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {

    }

    .hoverDiv {
        background: #fff;
        padding: 2px;
    }

    .hoverDiv:hover {
        background-color: #d8dfe7 !important;
        /*cursor: hand !important;*/
    }

</style>
<div style="padding:1px; margin: 2px 0px; ">
    <div style=" background-color: #ffffff; padding:3px; border:1px dashed #bfbfbf;">
        <table class="<?php table_class_pos(1); ?>">
            <tr>
                <td>
                    <!--<img class="img-thumbnail" src="<?php /*echo $warehouseMenu['menuImage'] */ ?>"
                         style="max-height: 50px; max-width: 50px;"
                         alt=""/>-->
                </td>
                <td>
                    <h4><?php echo $warehouseMenu['menuMasterDescription'] ?> &nbsp;
                        <span class="pull-right">Price : <?php echo $warehouseMenu['sellingPrice'] ?></span>
                    </h4>
                    <!--<div style="font-size:13px;">

                    </div>-->
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-md-5 col-lg-5">
        <h4><?php echo $this->lang->line('posr_pack_includes');?><!--Pack Includes--></h4>
        <?php
        $totalQtyAll = 0;
        /*echo '<pre>';
        print_r($packItemDetail);
        echo '</pre>';*/

        if (!empty($packItemDetail)) {
            $categoryID = 0;
            foreach ($packItemDetail as $item) {
                if ($item['isRequired'] == 0) {
                    continue;
                }

                if ($categoryID != $item['menuCategoryID']) {
                    ?>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <strong><?php echo $item['menuCategoryDescription'] ?></strong>
                        </div>
                    </div>
                    <?php
                }
                $categoryID = $item['menuCategoryID'];
                ?>
                <div class="row">
                    <div class="col-xs-3 col-sm-3  col-md-3 col-lg-3">
                        <img src="<?php echo $item['menuImage'] ?>" alt="<?php echo $item['menuMasterDescription'] ?>"
                             style="width: 30px; height: 30px; ">
                    </div>
                    <div class="col-xs-5 col-sm-5  col-md-7 col-lg-7">
                        <?php
                        //echo $item['menuCategoryID'] . $item['menuCategoryDescription'] . ' - ';
                        echo $item['menuMasterDescription'];
                        ?>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                        <?php
                        if ($item['isRequired'] == 0) {
                            echo '<input type="number" style="width: 100%"/>';

                        } else {
                            echo '<i class="fa fa-check text-green"></i>';
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <div class="col-md-7 col-lg-7">

        <h4><?php echo $this->lang->line('posr_optional');?><!--Optional--></h4>
        <form method="post" id="frm_pack_optionalValues">
            <input type="hidden" name="menuSalesID" value="<?php echo isPos_invoiceSessionExist(); ?>">
            <input type="hidden" value="<?php echo isset($id) && !empty($id) ? $id : 0; ?>" name="warehouseMenuID"
                   id="pack_menuID">
            <input type="hidden"
                   value="<?php echo isset($menuMasterID) && !empty($menuMasterID) ? $menuMasterID : 0; ?>"
                   name="menuMasterID" id="pack_menuMasterID">
            <?php
            //print_r($packItemDetail);

            if (!empty($packItemDetail)) {
                $categoryID = 0;
                foreach ($packItemDetail as $item) {

                    if ($item['isRequired'] == 1) {
                        continue;
                    }

                    if ($categoryID != $item['menuCategoryID']) {
                        $packCategory = get_srp_erp_pos_menupackcategory($item['PackMenuID'], $item['menuCategoryID']);
                        ?>
                        <input type="hidden" id="categoryCount_<?php echo $item['menuCategoryID'] ?>"
                               value="<?php echo !empty($packCategory['qty']) ? $packCategory['qty'] : 0; ?>">
                        <input type="hidden" id="categoryCountCurrent_<?php echo $item['menuCategoryID'] ?>"
                               value="0">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <strong>
                                    <?php
                                    echo $item['menuCategoryDescription'];
                                    ?>
                                    (<?php
                                    echo $packCategory['qty'];
                                    $totalQtyAll += $packCategory['qty'];

                                    ?>)</strong>
                            </div>
                        </div>
                        <?php
                    }
                    $categoryID = $item['menuCategoryID'];
                    ?>

                    <div class="row hoverDiv">
                        <div class="col-xs-3 col-sm-3 col-md-2 col-lg-2">
                            <img src="<?php echo $item['menuImage'] ?>"
                                 alt="<?php echo $item['menuMasterDescription'] ?>"
                                 style="width: 30px; height: 30px; ">
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-5 col-lg-5">
                            <?php
                            //echo $item['menuCategoryID'] . $item['menuCategoryDescription'] . ' - ';
                            echo $item['menuMasterDescription'];
                            ?>
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                            <div class="btn-group">
                                <button type="button"
                                        onclick="add_menuItem(<?php echo $item['id'] ?>,<?php echo $item['menuID'] ?>,<?php echo $item['menuCategoryID'] ?>)"
                                        class="btn btn-default"><i class="fa fa-plus"></i></button>
                                <button type="button"
                                        onclick="deduct_menuItem(<?php echo $item['id'] ?>,<?php echo $item['menuID'] ?>,<?php echo $item['menuCategoryID'] ?>)"
                                        class="btn btn-default"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-2 col-lg-2">
                            <span id="id_<?php echo $item['id'] . '_' . $item['menuID']; ?>"
                                  style="font-size: 20px; font-weight: 800;">0</span>
                            <input type="hidden" id="input_<?php echo $item['id'] . '_' . $item['menuID']; ?>"
                                   name="id[<?php echo $item['id']; ?>]" value="0">
                            <input type="hidden" name="pack_menuID[<?php echo $item['id']; ?>]" value="<?php echo $item['menuID']; ?>">
                            <!--<input type="hidden" class="catCount_<?php /*echo $item['PackMenuID'] */ ?>">-->
                        </div>
                    </div>

                    <?php
                }
                ?>
                <input type="hidden" id="totalQty_pack_combo" name="totalQty_pack_combo"
                       value="<?php echo $totalQtyAll ?>">
                <input type="hidden" id="totalQty_pack_combo_added" name="totalQty_pack_combo_added" value="0">
                <?php
            }
            //echo '<h1>' . $totalQtyAll . '</h1>';
            ?>

        </form>
    </div>
</div>

<?php if (empty($packItemDetail)) {
    ?>
    <div class="alert alert-warning"
         style="background-color: rgba(243, 156, 18, 0.23) !important; color: black !important;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong><?php echo $this->lang->line('posr_pack_not_configured');?><!--Pack not configured--></strong><br/> <?php echo $this->lang->line('posr_you_can_configure_the_Pack_under_menu_master');?><!--You can configure the Pack under Menu Master-->.
    </div>
    <input type="hidden" id="totalQty_pack_combo" name="totalQty_pack_combo" value="0">
    <input type="hidden" id="totalQty_pack_combo_added" name="totalQty_pack_combo" value="0">
    <?php
}
?>
<script>

    function getCategoryCount(PackMenuID, delimiter) {
        var currentTotalQty = parseInt($("#totalQty_pack_combo_added").val());
        var countTbl = $("#categoryCount_" + PackMenuID).val();
        var countCurrent = $("#categoryCountCurrent_" + PackMenuID).val();
        //console.log(PackMenuID);

        if (countTbl >= (parseInt(countCurrent) + parseInt(1)) && delimiter == 'A') {
            $("#categoryCountCurrent_" + PackMenuID).val(parseInt(countCurrent) + parseInt(1));
            $("#totalQty_pack_combo_added").val(currentTotalQty + 1);
            //console.log('Y: ' + countTbl + ' - ' + countCurrent + ' - ' + $("#totalQty_pack_combo_added").val());

            return true;
        } else if (countTbl >= (parseInt(countCurrent) - parseInt(1)) && delimiter == 'D' && (parseInt(countCurrent) - parseInt(1)) != -1) {
            $("#categoryCountCurrent_" + PackMenuID).val(parseInt(countCurrent) - parseInt(1));
            $("#totalQty_pack_combo_added").val(currentTotalQty - 1);
            // console.log('Y: ' + countTbl + ' - ' + countCurrent + ' - ' + $("#totalQty_pack_combo_added").val());
            return true;
        } else {
            //console.log('N: ' + countTbl + ' - ' + countCurrent);
            return false;
        }
    }

    function add_menuItem(id, menuID, PackMenuID) {
        console.log($("#totalQty_pack_combo_added").val());
        var inputID = id + '_' + menuID;
        var current = $("#input_" + inputID).val();
        var newCount = parseInt(current) + parseInt(1);
        var result = getCategoryCount(PackMenuID, 'A');
        if (result) {
            //console.log('X'+inputID+' NewCount'+newCount);
            $('#input_' + inputID).val(newCount);
            $('#id_' + inputID).html(newCount);
        } else {
            // console.log('Y'+inputID+' NewCount'+newCount);
        }
    }

    function deduct_menuItem(id, menuID, PackMenuID) {
        console.log($("#totalQty_pack_combo_added").val());
        var inputID = id + '_' + menuID;
        var current = $("#input_" + inputID).val();
        var newCount = parseInt(current) - parseInt(1);
        if (current == 0) {
            return false;
        }
        var result = getCategoryCount(PackMenuID, 'D');
        if (result) {
            //console.log('X'+inputID+' NewCount'+newCount);
            if (newCount > -1) {
                $('#input_' + inputID).val(newCount);
                $('#id_' + inputID).html(newCount);
            } else {
                alert('Item can not be ' + newCount);
            }
        }
    }

    function savePackDetailItemList(id) {
        var tmpData = $("#frm_pack_optionalValues").serializeArray();
        tmpData.push({name: 'menuSalesItemID', value: id});
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_restaurant/savePackDetailItemList'); ?>",
            data: tmpData,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
                if (data['error'] == 0) {


                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function savePackDetail() {
        var qtyTotal = $("#totalQty_pack_combo").val();
        var qtyTotalAdded = $("#totalQty_pack_combo_added").val();

        if (qtyTotal == 0) {
            bootbox.alert('<div class="alert alert-warning" style="background-color: rgba(243, 156, 18, 0.23) !important; color: black !important;"><strong> <?php echo $this->lang->line('posr_pack_not_configured');?> </strong> <br> <?php echo $this->lang->line('posr_you_can_configure_the_Pack_under_menu_master');?>.<?php echo $this->lang->line('posr_please_contact_your_administrator_or_manager');?> . </div>');
            <!--Pack not configured-->/*You can configure the Pack under Menu Master*//*please contact your administrator or manager*/
        } else {
            if (qtyTotal == qtyTotalAdded) {
                $("#rpos_packInvoice").modal('hide');
                var menuID = $("#pack_menuID").val();
                LoadToInvoice(menuID);
            } else {
                var dif = qtyTotal - qtyTotalAdded;
                bootbox.alert('<div class="alert alert-warning"><strong>Please Add Items </strong><br/><?php echo $this->lang->line('posr_you_have_to_add');?> ' + dif + ' <?php echo $this->lang->line('posr_more_items');?>. </div>');/*more item/s*/
                <!--You have to add-->
            }
        }
    }
</script>
