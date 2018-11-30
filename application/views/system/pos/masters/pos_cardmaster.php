<?php echo head_page('<i class="fa fa-credit-card"></i> Gift Card Master', false);
$locations = load_pos_location_drop();


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

?>
<style>
    .btn-lg {
        font-size: 14px !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos.css') ?>">
<script type="text/javascript"
        src="<?php echo base_url('plugins/virtual-keyboard-mlkeyboard/jquery.ml-keyboard.js') ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('plugins/numPadmaster/jquery.numpad.css') ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="col-md-4">
    &nbsp;
</div>
<div class="col-md-1">Filter <i class="fa fa-filter"></i></div>
<div class="col-md-4">
    <select class="select2 filters" id="outletID_f">
        <option value="" selected> All</option>
        <?php
        foreach ($locations as $loc) {
            echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . ' - ' . $loc['wareHouseDescription'] . ' - ' . $loc['wareHouseLocation'] . '</option>';
        }
        ?>
    </select>
</div>
<div class="col-md-3">

    <div class="pull-right">
        <button type="button" onclick="open_giftCardModal()" class="btn btn-primary btn-sm"><i
                class="fa fa-plus"></i>
            Issue Card
        </button>

        <button type="button" onclick="open_newCounterModel()" class="btn btn-primary btn-sm"><i
                class="fa fa-plus"></i>
            Add Card
        </button>
    </div>
</div>
</div>


<div class="table-responsive">
    <table id="giftCardMaster_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="width: 5%">#</th>
            <th>Card Code</th>
            <th>Outlet</th>
            <th style="width:100px;">Expiry in Months</th>
            <th style="width: 10%">&nbsp;</th>
        </tr>
        </thead>
    </table>
</div>

<?php
echo footer_page('Right foot', 'Left foot', false);
$this->load->view('system/pos/modals/pos-modal-gift-card-headOffice');
$this->load->view('system/pos/modals/pos-modal-credit-sales-headOffice');
?>

<div class="modal" id="counterCreate_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="counterCreate_modal_title"></h4>
            </div>
            <form role="form" id="giftCard_form" method="post" class="form-horizontal">
                <input type="hidden" name="cardMasterID" id="cardMasterID">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">
                                Outlet
                            </label>
                            <div class="col-sm-6">

                                <select class="form-control select2" id="outletID" name="outletID">
                                    <option value=""> Please Select</option>
                                    <?php
                                    foreach ($locations as $loc) {
                                        echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . ' - ' . $loc['wareHouseDescription'] . ' - ' . $loc['wareHouseLocation'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">
                                Expiry in Months <i class="fa fa-question-circle"
                                                    title="This card will expire within the said period of months, it will commence from the issue date."
                                                    rel="tooltip"> </i>
                            </label>
                            <div class="col-sm-2">
                                <input type="number" id="cardExpiryInMonths" class="form-control"
                                       name="cardExpiryInMonths" value="12"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label"> Barcode</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="barcode" name="barcode"
                                       placeholder="Swipe the Card or Enter the serial No.">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                    <input type="hidden" id="requestLink" name="requestLink">
                    <input type="hidden" id="updateID" name="updateID">

                    <button type="button" onclick="save_update()" class="btn btn-primary btn-sm updateBtn submitBtn">
                        <?php echo $this->lang->line('common_update'); ?><!--Update--></button>
                    <button type="button" onclick="save_update()" class="btn btn-primary btn-sm saveBtn submitBtn">
                        <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/numPadmaster/jquery.numpad.js') ?>" type="text/javascript"></script>
<script type="text/javascript">
    var modal_title = $("#counterCreate_modal_title");
    var counterCreate_model = $("#counterCreate_model");
    var giftCard_form = $("#giftCard_form");
    var giftCardMaster_table = '';


    $(document).ready(function () {
        /** Virtual Keyboard */
        $.fn.numpad.defaults.gridTpl = '<table class="modal-content table" style="width:200px" ></table>';
        $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in" style="z-index: 5000;"></div>';
        $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:16px; font-weight: 600;" />';
        $.fn.numpad.defaults.buttonNumberTpl = '<button type="button" class="btn btn-xl-numpad btn-numpad-default"></button>';
        $.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn btn-xl-numpad" style="width: 100%;"></button>';
        $.fn.numpad.defaults.onKeypadCreate = function () {
            $(this).find('.done').addClass('btn-primary');
            /*$(this).find('.del').addClass('btn-numpad-default');
             $(this).find('.clear').addClass('btn-numpad-default');*/
        };
        $('.numpad').numpad();

        $('html').click(function (e) {
            if (!$(e.target).hasClass('touchEngKeyboard')) {
                $("#mlkeyboard").hide();
            }
        });
        $('.headerclose').click(function () {
            fetchPage('system/pos/masters/pos_cardmaster', '', 'POS');
        });
        load_giftCards();
        $(".select2").select2();
        $("#barcode").keyup(function (e) {
            console.log(e.which);
            if (e.which == 13) {
                save_update();
            }
            return false;
        });
        $("#giftCard_form").submit(function (e) {
            return false;
        })

        $(".filters").change(function (e) {
            giftCardMaster_table.ajax.reload();
        })


    });

    function load_giftCards() {
        giftCardMaster_table = $('#giftCardMaster_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Pos_giftCard/fetch_giftCardMaster'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "cardMasterID"},
                {"mData": "barcode"},
                {"mData": "descriptionOutlet"},
                {"mData": "cardExpiryInMonths"},
                {"mData": "action"}
            ],

            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'f_outletID', 'value': $("#outletID_f").val()});
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }/*,
             "data": function (d) {
             d.cardMasterID = $("#outletID_f").val()
             }*/
        });
    }

    /**$('.submitBtn').click(function () {
        if ($(this).hasClass('updateBtn')) {
            $('#requestLink').val('<?php echo site_url('pos/update_counterDetails'); ?>');
        } else {
            $('#requestLink').val('<?php echo site_url('pos/new_counter'); ?>');
        }
    });*/

    function save_update() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: $("#giftCard_form").serialize(),
            url: '<?php echo site_url('Pos_giftCard/submit_giftCards'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    //counterCreate_model.modal("hide");
                    setTimeout(function () {
                        giftCardMaster_table.ajax.reload();
                        $("#barcode").val('');
                        $("#cardMasterID").val(0);
                        $("#barcode").focus();
                        btnHide('saveBtn', 'updateBtn');
                        modal_title.html('<i class="fa fa-credit-card"></i> Gift Card');

                    }, 300);
                } else {
                    $("#barcode").val('');
                    $("#barcode").focus();
                    myAlert('e', data['message']);

                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function open_newCounterModel() {
        $("#cardMasterID").val(0);
        setTimeout(function () {
            $("#barcode").focus();
        }, 500);
        $('#isConform').val(0);
        giftCard_form[0].reset();
        $("#outletID").val('').change();
        //giftCard_form.bootstrapValidator('resetForm', true);
        modal_title.html('<i class="fa fa-credit-card"></i> Gift Card');
        counterCreate_model.modal({backdrop: "static"});
        $('.submitBtn').prop('disabled', false);
        btnHide('saveBtn', 'updateBtn');
    }

    function editGiftCard(cardMasterID, barcode, outletID, cardExpiryInMonths) {
        setTimeout(function () {
            $("#barcode").focus();
        }, 500);
        giftCard_form[0].reset();
        //giftCard_form.bootstrapValidator('resetForm', true);
        $('#isConform').val(0);
        modal_title.html('<i class="fa fa-credit-card"></i> Edit Gift Card');
        counterCreate_model.modal({backdrop: "static"});
        $('#cardMasterID').val(cardMasterID);
        $('#outletID').val(outletID).change();
        $('#barcode').val(barcode);
        $('#cardExpiryInMonths').val(cardExpiryInMonths);

        btnHide('updateBtn', 'saveBtn');

    }

    function btnHide(btn1, btn2) {
        $('.' + btn1).show();
        $('.' + btn2).hide();
    }

    function delete_GiftCard(cardMasterID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'cardMasterID': cardMasterID},
                    url: "<?php echo site_url('Pos_giftCard/delete_giftCard'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();

                        if (data['error'] == 0) {
                            myAlert('s', data['message']);
                            setTimeout(function () {
                                giftCardMaster_table.ajax.reload();
                            }, 300);
                        } else {
                            myAlert('e', data['message']);
                        }
                    }, error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        );
    }
</script>

