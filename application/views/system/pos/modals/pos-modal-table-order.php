<?php
/**
 * --- Created on 18-JAN-2018 by Mohamed Shafri
 * --- Table Selection Modal
 */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div aria-hidden="true" role="dialog" id="pos_TableOrderModal" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header"
                 style="background-image: url('<?php echo base_url('images/pos/bg-restaurant.jpg') ?>');">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-close text-red"></i>
                </button>
                <h4 class="modal-title">
                    <img src="<?php echo base_url('images/pos/Plates-icon.png') ?>" alt=""> Table
                    <span id="deliveryLoader" style="display: none;" class="pull-right">
                        <i class="fa fa-refresh fa-2x fa-spin"></i>
                    </span>
                </h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <form id="delivery_order_table" method="post">
                        <input type="hidden" id="diningTableAutoID" name="diningTableAutoID" value="0"/>
                        <div class="container-fluid">
                            <div class="row">
                                <?php
                                if (isset($tables_list) && !empty($tables_list)) {
                                    foreach ($tables_list as $item) {
                                        ?>
                                        <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                                            <button type="button" class="btn btn-lg btn-default btn-block btn_tblOrder"
                                                    id="tableOrderID_<?php echo $item['diningTableAutoID'] ?>"
                                                    style="margin-top:10px; min-height: 130px;"
                                                    onclick="update_tableOrder(<?php echo $item['diningTableAutoID'] ?>,'<?php echo $item['noOfSeats'] ?>')">
                                                <?php
                                                echo '<i class="fa fa-life-ring fa-2x"></i> <br/>';
                                                echo $item['diningTableDescription'] . '<br/>';
                                                echo $item['noOfSeats'] . ' Seats';
                                                ?>

                                                <div class="clsOrderID"
                                                     id="orderID_<?php echo $item['diningTableAutoID'] ?>"></div>
                                            </button>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <div class="alert alert-info">Please setup the table for this outlet!</div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?> </button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="pos_WaiterSelectionModal" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header"
                 style="background-image: url('<?php echo base_url('images/pos/bg-restaurant.jpg') ?>');">
                <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                        <h4 class="modal-title">
                            <img src="<?php echo base_url('images/pos/waiter-icon.png') ?>" alt=""> Waiters
                            <span id="deliveryLoader" style="display: none;" class="pull-right">
                                <i class="fa fa-refresh fa-2x fa-spin"></i>
                            </span>
                        </h4>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                        <!--<button class="btn btn-lg btn-danger" type="button" onclick="openBillByTable();">
                            Open Bill <span id="tmp_invoiceID_waiterModal"></span>
                        </button>-->
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                        <button type="button" class="close btn btn-lg btn-default" data-dismiss="modal"
                                aria-hidden="true">
                            <i class="fa fa-close text-red"></i>
                        </button>
                    </div>
                </div>


            </div>
            <div class="modal-body">

                <div class="row">
                    <form id="form_waiterList" method="post">
                        <div class="container-fluid">
                            <input type="hidden" id="tmp_tableID" name="tmp_tableID" value="0">
                            <input type="hidden" id="tmp_selectedCrewID" name="tmp_selectedCrewID" value="0">
                            <input type="hidden" id="tmp_packID" name="tmp_packID" value="0">

                            <div class="title-waiter"> PACKS</div>
                            <div class="row" id="numberOfPacks_div">
                            </div>
                            <div class="title-waiter"> WAITERS</div>
                            <div class="row">
                                <?php
                                $waiters = get_waiter_list();
                                if (!empty($waiters)) {

                                    foreach ($waiters as $waiter) {
                                        ?>
                                        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <button type="button" class="btn btn-lg btn-default btn-block btn_waiter"
                                                    id="waiterID_<?php echo $waiter['crewMemberID'] ?>"
                                                    style="margin-top:10px;"
                                                    onclick="update_waiter_info(<?php echo $waiter['crewMemberID'] ?>)">
                                                <img src="<?php echo base_url('images/pos/waiter-icon.png') ?>"/>
                                                <br>
                                                <?php
                                                echo $waiter['crewLastName'] . '<br/>';
                                                ?>
                                            </button>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?> </button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        $("#table_order_btn").click(function () {
            table_order_init();
        })
    });

    function table_order_init() {
        $("#pos_TableOrderModal").modal('show');
        refreshDiningTables();
    }

    function update_waiter_info(crewID) {
        var tableID = $("#tmp_tableID").val();
        var numberOfPack = $("#tmp_packID").val();
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_restaurant/update_waiter_info'); ?>",
            data: {crewID: crewID, tableID: tableID, numberOfPack: numberOfPack},
            cache: false,
            beforeSend: function () {
                $(".btn_waiter").removeClass('btn-primary');
                $(".btn_waiter").addClass('btn-default');
                $("#waiterID_" + crewID).addClass('btn-primary');
                startLoadPos();
                $("#tmp_invoiceID_waiterModal").html();
            },
            success: function (data) {
                stopLoad();
                refreshDiningTables();
                /*setTimeout(function () {
                 $("#pos_WaiterSelectionModal").modal('hide')
                 }, 500);*/

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                if (jqXHR.status == false) {
                    myAlert('w', 'Local Server is Offline, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });
    }

    function change_number_of_pack(id) {
        $(".btn-number-of-packs").removeClass('btn-primary');
        $(".btn-number-of-packs").removeClass('btn-default');
        $("#pack_id_" + id).addClass('btn-primary');
        $("#tmp_packID").val(id);
        var tmpCrewID = $("#tmp_selectedCrewID").val();
        update_waiter_info(tmpCrewID);
    }

    function update_tableOrder(id, totalNumOfPack) {
        var gross_total = parseFloat($("#gross_total").html());
        if (gross_total > 0) {
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Pos_restaurant/update_tableOrder'); ?>",
                data: {id: id, menuSalesID: $("#holdInvoiceID").val()},
                cache: false,
                beforeSend: function () {
                    $("#tableOrderID_" + id).removeClass('btn-default');
                    $("#tableOrderID_" + id).addClass('btn-primary');
                    startLoadPos();
                },
                success: function (data) {
                    stopLoad();
                    if (data['error'] == 1) {
                        myAlert('e', data['message']);
                    }

                    if (data['show_waiter'] == 1) {

                        $("#tmp_tableID").val(id);
                        $("#pos_WaiterSelectionModal").modal('show');
                        $(".btn_waiter").removeClass('btn-primary');
                        $(".btn_waiter").addClass('btn-default');
                        if (data['crewID'] > 0) {
                            $("#waiterID_" + data['crewID']).addClass('btn-primary');
                            $("#tmp_selectedCrewID").val(data['crewID']);
                        }

                        var packsList = '';
                        var Packs;
                        var ico;
                        var btnCls;
                        if (totalNumOfPack > 0) {
                            totalNumOfPack = parseInt(totalNumOfPack);
                            var i;
                            for (i = 1; i <= totalNumOfPack; i++) {
                                if (i == 1) {
                                    Packs = ' Pack ';
                                    ico = '<i class="fa fa-user fa-2x link-black" aria-hidden="true"></i> ';
                                } else {
                                    Packs = ' Packs ';
                                    ico = '<i class="fa fa-users fa-2x link-black" aria-hidden="true"></i> ';
                                }

                                if (data['packs'] == i) {
                                    btnCls = 'btn-primary';
                                } else {
                                    btnCls = 'btn-default';
                                }

                                packsList = packsList + '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3"> <button type="button" id="pack_id_' + i + '" onclick="change_number_of_pack(' + i + ')"  class="btn btn-lg ' + btnCls + ' btn-block btn-number-of-packs" style="margin-top:10px;" >  ' + ico + i + Packs + '  </button> </div>';
                            }
                            $("#numberOfPacks_div").html(packsList);

                        }


                    } else {
                        if (data['error'] != 3) {
                            refreshDiningTables();
                        } else {
                            $("#tableOrderID_" + id).addClass('btn-default');
                            $("#tableOrderID_" + id).removeClass('btn-primary');
                        }
                    }

                    if (data['error'] == 3) {
                        swal({
                                title: "Are you sure?",
                                text: "You want to switch the table?",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#F39C12",
                                confirmButtonText: "Switch Table"
                            },
                            function () {
                                switchTable(data['fromKey'], data['id'], data['menuSalesID'], data['crewID'], data['packs'])
                            });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    if (jqXHR.status == false) {
                        myAlert('w', 'Local Server is Offline, Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }
                }
            });
        } else {
            bootbox.alert('<div class="alert alert-info"><strong><?php echo $this->lang->line('posr_no_menus_added_to_invoice_please_add_at_least_one_item');?>.</strong></div>');
        }

        return false;

    }

    function switchTable(fromKey, id, menuSalesID, crewID, packs) {
        var gross_total = parseFloat($("#gross_total").html());
        if (gross_total > 0) {
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Pos_restaurant/switchTable'); ?>",
                data: {fromKey: fromKey, id: id, menuSalesID: menuSalesID, crewID: crewID, packs: packs},
                cache: false,
                beforeSend: function () {
                    $("#tableOrderID_" + id).removeClass('btn-default');
                    $("#tableOrderID_" + id).addClass('btn-primary');
                    startLoadPos();
                },
                success: function (data) {
                    stopLoad();
                    if (data['error'] == 1) {
                        myAlert('e', data['message']);
                    }
                    refreshDiningTables();
                    if (data['show_waiter'] == 1) {
                        $("#pos_WaiterSelectionModal").modal('show');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    if (jqXHR.status == false) {
                        myAlert('w', 'Local Server is Offline, Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }
                }
            });
        }
        else {
            bootbox.alert('<div class="alert alert-info"><strong><?php echo $this->lang->line('posr_no_menus_added_to_invoice_please_add_at_least_one_item');?>.</strong></div>');
        }

        return false;
    }

    function refreshDiningTables() {
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_restaurant/refreshDiningTables'); ?>",
            data: {id: null},
            cache: false,
            beforeSend: function () {

                startLoadPos();
            },
            success: function (data) {
                stopLoad();
                $(".btn_tblOrder").removeClass('btn-primary');
                $(".btn_tblOrder").removeClass('btn-danger');
                $(".btn_tblOrder").addClass('btn-default');
                $(".clsOrderID").html('');
                var tmp_holdInvoiceID = $("#holdInvoiceID").val();
                $.each(data.result, function (key, value) {
                    if (value.menuSalesID == tmp_holdInvoiceID) {
                        $("#tableOrderID_" + value['diningTableAutoID']).removeClass('btn-default');
                        $("#tableOrderID_" + value['diningTableAutoID']).addClass('btn-primary');
                        $("#current_table_description").text(value.tableName);
                    } else {
                        $("#tableOrderID_" + value['diningTableAutoID']).addClass('btn-danger');
                        $("#tableOrderID_" + value['diningTableAutoID']).removeClass('btn-default');
                    }

                    if (value['status'] == 1) {
                        $("#orderID_" + value['diningTableAutoID']).html(value['invoiceCode']);
                    }
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                if (jqXHR.status == false) {
                    myAlert('w', 'Local Server is Offline, Please try again');
                } else {
                    myAlert('e', 'Message: ' + errorThrown);
                }
            }
        });
        return false;

    }


</script>