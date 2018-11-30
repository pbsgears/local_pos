<?php
$liabilityGL = liabilityGL_drop();
$expenseGL = expenseIncomeGL_drop();
$payableGL = payableGL_drop();
$bankWithCardGL = load_bank_with_card();
$payConData = posrPaymentConfig_data();
$outlets = get_active_outlets();


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('posr_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('posr_master_gl_code_configuratio');
echo head_page($title, false);
?>
<script type="text/javascript">

    function glDropMake(thisCombo, dropType, selectedID = null, ID = null) {

        /*console.log('thisCombo:' + thisCombo + '- dropType:' + dropType + '- selectedID: ' + selectedID + '- ID: ' + ID);
         return false;*/
        if (ID != null) {
            var glArray = null;
            switch (dropType) {
                case 1:
                    glArray = JSON.stringify(<?php echo json_encode($payableGL) ?>);
                    break;

                case 2:
                    glArray = JSON.stringify(<?php echo json_encode($bankWithCardGL) ?>);
                    break;

                case 3:
                    glArray = JSON.stringify(<?php echo json_encode($liabilityGL) ?>);
                    break;

                case 4:
                    glArray = JSON.stringify(<?php echo json_encode($expenseGL) ?>);
                    break;

                case 5:
                    glArray = JSON.stringify(<?php echo json_encode($outlets) ?>);
                    break;
            }

            var row = JSON.parse(glArray);
            var drop = '<option value=""></option>';

            if (dropType == 5) {
                $.each(row, function (i, obj) {
                    var selected = ( selectedID == obj.wareHouseAutoID ) ? 'selected' : '';
                    drop += '<option value="' + obj.wareHouseAutoID + '" ' + selected + '>';
                    drop += obj.wareHouseCode + ' | ' + obj.wareHouseDescription + ' | ' + obj.wareHouseLocation + '</option>';
                });

                var thisDropDown = $('#gl_' + thisCombo + '_' + ID + '_outlet');

            } else {
                $.each(row, function (i, obj) {
                    var selected = ( selectedID == obj.GLAutoID ) ? 'selected' : '';
                    drop += '<option value="' + obj.GLAutoID + '" ' + selected + ' data-sys="' + obj.systemAccountCode + '" data-sec="' + obj.GLSecondaryCode + '" >';
                    drop += obj.GLSecondaryCode + ' | ' + obj.GLDescription + '</option>';
                });

                var thisDropDown = $('#gl_' + thisCombo + '_' + ID);
            }

            thisDropDown.append(drop);
            thisDropDown.change();
        }
        //console.log(thisCombo + '-' + dropType + '-' + selectedID + '-' + ID);

    }

    function openGLConfig_paymentsModal() {
        $("#posr_gl_config_payment_modal").modal('show');
    }

    function save_GLConfig_detail() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: $("#frm_gl_config_payment").serialize(),
            url: "<?php echo site_url('Pos_config/saveGLConfigDetail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    $("#posr_gl_config_payment_modal").modal('hide');
                    myAlert('s', data['message']);
                    fetchPage('system/pos/posr_gl_config', '', 'POS');
                } else {
                    myAlert('e', data['message']);
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function ajax_load_chartOfAccountData(data_tmp) {

        var GLConfigMasterID = data_tmp.value;
        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {GLConfigMasterID: GLConfigMasterID},
                url: "<?php echo site_url('Pos_config/loadChartOfAccountData'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (data.error == 0) {
                        $("#GLCode_frm2").html('<option value="">please select</option>');
                        $.each(data['e'], function (key, value) {
                            var optionVal = '<option value="' + value['GLAutoID'] + '">' + value['GLSecondaryCode'] + ' | ' + value['GLDescription'] + '</option>';
                            $("#GLCode_frm2").append(optionVal);
                        });
                        $("#GLCode_frm2").select2();

                    } else {
                        $("#GLCode_frm2").html('<option>please select</option>');
                        myAlert('e', data.message);
                    }
                    stopLoad();
                },
                error: function () {
                    $("#GLCode_frm2").html('<option>please select</option>');
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                }
            }
        )
    }


    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/pos/gl_config', 'Test', 'POS');
        });
    });
    function load_glCodes(obj) {

        var systemCode = $(obj).find(':selected').attr('data-sys');
        var secondaryCode = $(obj).find(':selected').attr('data-sec');
        var systemCodeTxt = $(obj).closest('tr').find('td:eq(2) .systemCode');
        var secondaryCodeTxt = $(obj).closest('tr').find('td:eq(3) .secondaryCode');
        systemCodeTxt.val(systemCode);
        secondaryCodeTxt.val(secondaryCode);


        systemCodeTxt.hide();
        systemCodeTxt.fadeIn();

        secondaryCodeTxt.hide();
        secondaryCodeTxt.fadeIn();
    }

    $(document).ready(function (e) {
        $('.select2').select2();
    })

    function posGL_config(dropDown, autoID, ID, warehouseID) {
        var glAutoID = $('#gl_' + dropDown + '_' + ID).val();
        var outletID = $('#gl_' + dropDown + '_' + ID + '_outlet').val();
        data = [
            {'name': 'glAutoID', 'value': glAutoID},
            {'name': 'paymentTypeID', 'value': autoID},
            {'name': 'ID', 'value': ID},
            {'name': 'warehouseID', 'value': outletID}
        ];

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Pos_config/POSR_posGL_config'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

</script>
<style type="text/css">
    .glInputs {
        height: 25px;
        padding: 2px 10px;
        font-size: 12px;
    }

    .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single {
        height: 25px;
        padding: 1px 5px
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 25px !important;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>


<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <button class="btn btn-sm btn-primary pull-right" onclick="openGLConfig_paymentsModal()" type="button">
                Add <i class="fa fa-plus"></i></button>

        </div>

    </div>
</div>
<div>

    <form class="form-horizontal">
        <fieldset>


            <!-- Select Basic -->
            <div class="form-group">
                <label class="col-md-4 control-label"
                       for="selectbasic"><?php echo $this->lang->line('posr_master_outlet_users'); ?></label>
                <div class="col-md-4">

                    <?php
                    echo form_dropdown('warehouseID', get_active_outlets_drop(), '', 'id="warehouseID"  class="form-control select2" onchange="filterOutlet(this)" ')
                    ?>

                </div>
            </div>

        </fieldset>
    </form>

</div>
<div class="table-responsive">
    <table class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th></th>
            <th style="width: 300px">
                <?php echo $this->lang->line('posr_master_account_name'); ?><!--Account Name--></th>
            <th><?php echo $this->lang->line('posr_master_system_code'); ?><!--System Code--></th>
            <th><?php echo $this->lang->line('posr_master_secondary_code'); ?><!--Secondary Code--></th>
            <th><?php echo $this->lang->line('posr_master_outlet_users'); ?><!--Outlets--></th>
            <th></th>
        </tr>
        </thead>

        <tbody>
        <?php
        if (!empty($payConData)) {
        foreach ($payConData as $row) {
            $selectedID = ($row['GLCode']) ? $row['GLCode'] : 0;
            $selectedID2 = ($row['warehouseID']) ? $row['warehouseID'] : 0;
            $save = $this->lang->line('common_save');
            $id = 'gl_' . $row['selectBoxName'] . '_' . $row['ID'];
            $onclick = 'onclick="posGL_config(\'' . $row['selectBoxName'] . '\',' . $row['autoID'] . ',' . $row['ID'] . ',' . $selectedID2 . ')"';
            ?>
            <tr class="allRows outletID_<?php echo $row['warehouseID'] ?>">
                <td style="vertical-align: middle"><?php echo $row['description'] ?></td>
                <td>
                    <select name="GLDescription" id="<?php echo $id ?>" class="form-control glInputs "
                            onchange="load_glCodes(this)"></select>
                </td>
                <td><input type="text" class="form-control glInputs systemCode" name="systemCode" readonly></td>
                <td><input type="text" class="form-control glInputs secondaryCode" name="secondaryCode" readonly>
                </td>
                <td>
                    <select id="<?php echo $id ?>_outlet" class="form-control"></select></td>
                <td align="center">
                    <button class="btn btn-default btn-xs" <?php echo $onclick ?>><?php echo $save ?></button>
                    <?php //echo $row['warehouseID'] ?>
                </td>
            </tr>
            <script>
                $(document).ready(function (e) {
                    glDropMake('<?php echo $row['selectBoxName']?>',<?php echo $row['glAccountType']?> ,<?php echo $selectedID ?>, <?php echo $row['ID'] ?>);
                    glDropMake('<?php echo $row['selectBoxName']?>', 5,<?php echo $selectedID2 ?>, <?php echo $row['ID']?>);
                });
            </script>
        <?php

        }
        }else {
        ?>
            <tr>
                <td colspan="5">
                    No Records Found!.
                </td>

            </tr>
            <?php
        }
        ?>

        </tbody>
    </table>
</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div aria-hidden="true" role="dialog" id="posr_gl_config_payment_modal" class="modal fade"
     data-keyboard="true" data-backdrop="static">
    <div class="modal-dialog"
         style="width: <?php echo !isset($modal_width) && !empty($modal_width) ? $modal_width . '%' : '50%'; ?>">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_payment'); ?><!--Payment--></h4>
            </div>
            <div class="modal-body" style="overflow: visible; background-color: #FFF;">
                <form class="form-horizontal" method="post" id="frm_gl_config_payment">
                    <fieldset>


                        <div class="form-group">
                            <label class="col-md-4 control-label"
                                   for="warehouseID"><?php echo $this->lang->line('posr_master_outlet_users'); ?></label>
                            <div class="col-md-5">
                                <?php
                                echo form_dropdown('warehouseID', get_active_outlets_drop(), '', 'id="warehouseID"  class="form-control select2"  ');
                                //onchange="ajax_load_chartOfAccountData(this)"
                                ?>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-4 control-label" for="paymentConfigMasterID">Payment Type </label>
                            <div class="col-md-5">
                                <?php
                                echo form_dropdown('paymentConfigMasterID', get_payment_config_master_drop(), '', 'id="paymentConfigMasterID"  class="form-control select2" onchange="ajax_load_chartOfAccountData(this)" ')
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="charOfAccountDropDown">Account Name</label>
                            <div class="col-md-7">
                                <?php
                                echo form_dropdown('GLCode', array('' => 'please select'), '', 'id="GLCode_frm2"  class="form-control select2" ')
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"></label>
                            <div class="col-md-5">
                                <button class="btn btn-primary" type="button" onclick="save_GLConfig_detail()">
                                    Add <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>

                    </fieldset>
                </form>


            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function filterOutlet(thisTmp) {
        if (thisTmp.value > 0) {
            $(".allRows").hide();
            $(".outletID_" + thisTmp.value).show();
        } else {
            $(".allRows").show();
        }
    }
</script>

