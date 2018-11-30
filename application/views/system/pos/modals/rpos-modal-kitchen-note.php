<?php
/**
 * --- Created on 7-MAR-2017 by Mushtaq Ahamed
 * --- POS Open void Receipt Modal Window
 */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>

<div aria-hidden="true" role="dialog" tabindex="-1" id="pos_kitchen_note" class="modal fade" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-close text-red"></i>
                </button>-->
                <h4 class="modal-title"><?php echo $this->lang->line('posr_kitchen_note'); ?> - <span
                        id="kitchenNoteDescription"></span></h4>
            </div>
            <div id="voidReceipt" class="modal-body"
                 style="overflow: visible; background-color: #FFF; min-height: 100px;">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
                        <div>
                            <h5 class="add-on-heading">Kitchen Note </h5>
                            <?php
                            $kitchenNoteSamples = get_kitchenNoteSamples();
                            if (!empty($kitchenNoteSamples)) {
                                foreach ($kitchenNoteSamples as $kitchenNoteSample) {
                                    ?>
                                    <button onclick="loadToKitchenNote(this)"
                                            class="button button-border button-rounded button-primary button-small"
                                            style="margin: 2px;"><?php echo $kitchenNoteSample['noteDescription'] ?></button><?php
                                }
                            }
                            ?>
                        </div>

                        <form id="frm_kot" method="post">
                            <input type="hidden" name="kotID" id="kot_kotID" value="0"/>
                            <input type="hidden" name="tmpWarehouseMenuID" id="tmpWarehouseMenuID" value="0"/>
                            <textarea style="width: 100%" class="touchEngKeyboard" name="kitchenNote " id="kitchenNote"
                                      cols="30"
                                      rows="5"></textarea>
                            <button type="button" class="btn btn-xs btn-default pull-right" onclick="clearKOTNote()"><i
                                    class="fa fa-eraser"></i> Clear Note
                            </button>
                        </form>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
                        <h5 class="add-on-heading">Add-on</h5>
                        <?php
                        $add_on_list = get_add_on_list();
                        if (!empty($add_on_list)) {
                            foreach ($add_on_list as $addOn) {
                                if ($addOn['showImageYN'] == 1) {
                                    $style = 'background-image: url(\'' . $addOn['menuImage'] . '\'); background-size: cover;';
                                } else {
                                    $style = 'background-color: ' . $addOn['bgColor'] . ';';
                                }
                                ?>
                                <div class="kot-add-on">
                                    <div class="row">
                                        <div class="col-xs-3 col-sm-2 col-md-2 col-lg-2">
                                            <div class="kot-add-on-img"
                                                 style="<?php echo $style ?>"></div>
                                        </div>
                                        <div class="col-xs-7 col-sm-8 col-md-8 col-lg-8">
                                            <?php echo $addOn['menuMasterDescription'] ?>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <input class="kot-add-on-input-check" name="kotAddOn[]"
                                                   value="<?php echo $addOn['autoID'] ?>"
                                                   data-id="<?php echo $addOn['autoID'] ?>"
                                                   id="kot_add_on_id_<?php echo $addOn['autoID'] ?>" type="checkbox">
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<em>No Add-on found!</em>';
                        }
                        ?>
                    </div>
                </div>


            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <!--<button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                    <?php /*echo $this->lang->line('common_Close'); */ ?> </button>-->
                <button type="button" id="btn_kitchenNote"
                        class="btn btn-lg btn-primary btn-block"><?php echo $this->lang->line('common_ok'); ?> </button>
            </div>
        </div>
    </div>
</div>

<script>
    var kotAddOnList = [];
    function open_kitchen_note(id, kotID) {
        $("#tmpWarehouseMenuID").val(id);
        if (kotID > 0) {
            $("#pos_kitchen_note").modal('show');
        }
    }

    function selectMe(tmpThis) {
        var id = $(tmpThis).children().find('input').attr('data-id');
        $('#kot_add_on_id_' + id).iCheck('toggle');
    }

    $(document).ready(function (e) {
        $(".kot-add-on").click(function (e) {
            selectMe(this);
        });

        $("#btn_kitchenNote").click(function (e) {
            $("#pos_kitchen_note").modal('hide');
            var id = $("#tmpWarehouseMenuID").val();
            $.each($(".kot-add-on-input-check"), function (i, val) {
                if (val.checked == true) {
                    kotAddOnList.push(val.value);
                }
            });
            LoadToInvoice(id);
        });


        $('#pos_kitchen_note').on('hidden.bs.modal', function () {
            $("#kitchenNote").val('');
            $("#kot_kotID").val(0);
            $(".kot-add-on-input-check").iCheck('uncheck');
        })

        $('input').iCheck({
            checkboxClass: 'icheckbox_square-red',
            radioClass: 'iradio_square-red',
            increaseArea: '20%' // optional
        });
    });

    function loadToKitchenNote(tmpThis) {
        var tmpValue = $("#kitchenNote").val();
        var noteText = tmpValue + '- ' + $(tmpThis).text().trim() + " \n ";


        $("#kitchenNote").val(noteText);
        //$("#kitchenNote").focus();
    }

    function clearKOTNote() {
        $("#kitchenNote").val('');
        //$("#kitchenNote").focus();
    }

</script>