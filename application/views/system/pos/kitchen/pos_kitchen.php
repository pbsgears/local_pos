<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos.css') ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/slick/slick/slick.css') ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/slick/slick/slick-theme.css') ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<style>
    .completedList li {
        font-size: 15px;
        /*list-style: square;*/
        margin: 3px;
        min-height: 10px;
    }

    .title {
        background-color: #050500;
        color: #e5de14;
        padding: 5px;
        border-radius: 2px;
        margin: 0px;

    }

    table > tr {
        border: 1px dashed black !important;
    }

    .detailList {
        margin-left: 10px;
        font-size: 18px;
        /*font-weight: 700;*/
        font-family: Calibri;
    }

    .detailList img {
        height: 25px !important;
        width: 25px !important;

    }

    .btnSwitchCustom {
        /*padding: 2px;
        background-color: #ffffff;
        margin: -5px -4px 0px 0px;*/
    }
</style>
<?php
$this->load->view('include/header', $title);
//$this->load->view('include/top');

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>


<div class="row">
    <div class="col-md-12">

        <div id="orderListContainer">
            <div class="row">
                <div class="col-md-6">


                    <h4 class="text-yellow text-center"><i class="fa fa-cutlery"></i>
                        <?php echo $this->lang->line('posr_pending_order'); ?><!--Pending Order-->
                        (
                        <strong><?php echo isset($pendingOrdersCount['countMenuSales']) ? $pendingOrdersCount['countMenuSales'] : 0; ?></strong>
                        ) </h4>

                    <!--Pending Orders -->
                    <table class="table table-condensed" id="pendingOrderTbl">
                        <tbody>
                        <?php
                        if (!empty($pendingOrders)) {
                            $titleID = 0;
                            $i = 0;
                            foreach ($pendingOrders as $pendingOrder) {
                                if ($titleID != $pendingOrder['menuSalesID']) {
                                    if ($i == 3) {
                                        // break;
                                    }
                                    $i++;
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="media">
                                                <div class="media-body">
                                                    <h4 class="title">
                                                        <i class="fa fa-tag"></i>
                                                        <span style="font-weight: 800">
                                                            <?php echo number_pad($pendingOrder['menuSalesID'], 5) ?>
                                                        </span>

                                                    </h4>
                                                </div>
                                            </div>
                                        </td>
                                        <td><input class="mySwitch" type="checkbox" value="1"
                                                   id="isPax_<?php echo $pendingOrder['menuSalesID'] ?>" name="pending"
                                                   onchange="updateToCurrent(<?php echo $pendingOrder['menuSalesID'] ?>,this)"
                                                   data-size="small"
                                                   data-on-text="<i class='fa fa-cutlery text-red'></i> Cook"
                                                   data-on-color="default"
                                                   data-handle-width="50"
                                                   data-off-color="default"
                                                   data-off-text="<i class='fa fa-warning text-yellow'></i> &nbsp;"
                                                   data-label-width="0"></td>
                                        <td class="title">
                                            <span class="pull-right"
                                                  style="font-weight: 800"><?php echo $this->lang->line('posr_qyt'); ?><!--QTY--></span>
                                        </td>

                                    </tr>
                                    <?php
                                }
                                $titleID = $pendingOrder['menuSalesID'];
                                ?>
                                <tr>
                                    <td colspan="3">
                                        <div class="detailList">
                                            <img src="<?php echo base_url($pendingOrder['menuImage']) ?>"
                                                 style="height: 40px; width: 40px;" alt="">
                                            <?php echo $pendingOrder['menuMasterDescription']; ?>
                                            <?php echo $pendingOrder['qty']; ?>
                                        </div>
                                        <span class="pull-right"> <?php echo $pendingOrder['qty']; ?></span>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">

                    <h4 class="text-red text-center"><i
                            class="fa fa-cutlery"></i> <?php echo $this->lang->line('posr_progressing'); ?>
                        <!--Progressing-->
                        (
                        <strong><?php echo isset($currentOrdersCount['currentCount']) ? $currentOrdersCount['currentCount'] : 0; ?> </strong>)
                    </h4>

                    <!--Current Orders -->
                    <table class="table  table-condensed" id="pendingOrderTbl">
                        <tbody>
                        <?php
                        if (!empty($currentOrders)) {
                            $titleID = 0;
                            $i = 0;
                            foreach ($currentOrders as $currentOrder) {
                                if ($titleID != $currentOrder['menuSalesID']) {
                                    if ($i == 3) {
                                        // break;
                                    }
                                    $i++;
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="media">
                                                <div class="media-body">
                                                    <h4 class="title">
                                                        <i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>
                                                        <span style="font-weight: 800">
                                                            <?php echo number_pad($currentOrder['menuSalesID'], 5) ?>
                                                        </span>
                                                    </h4>
                                                </div>
                                            </div>
                                        </td>
                                        <td>&nbsp;</td>
                                        <td>
                                            <input class="mySwitch" type="checkbox" value="1"
                                                   id="isPax_<?php echo $currentOrder['menuSalesID'] ?>" name="pending"
                                                   onchange="updateToCompleted(<?php echo $currentOrder['menuSalesID'] ?>,this)"
                                                   data-size="small"
                                                   data-on-text="<i class='fa fa-check text-green'></i> &nbsp;"
                                                   data-on-color="default"
                                                   data-handle-width="50"
                                                   data-off-color="default"
                                                   data-off-text="<i class='fa fa-cutlery text-red'></i> Cook"
                                                   data-label-width="0">
                                        </td>
                                    </tr>
                                    <?php
                                }
                                $titleID = $currentOrder['menuSalesID'];
                                ?>
                                <tr>
                                    <td colspan="3">
                                        <div class="detailList">
                                            <img src="<?php echo base_url($currentOrder['menuImage']) ?>"
                                                 style="height: 40px; width: 40px;" alt="">
                                            <?php echo $currentOrder['menuMasterDescription']; ?>
                                        </div>
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

    </div>
</div>


<?php
$data['notFixed'] = true;
$this->load->view('include/footer', $data);
?>
<script type="text/javascript" src="<?php echo base_url('plugins/keyboard-short-cut/shortcut.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/pos/pos.js'); ?>"></script>
<script src="<?php echo base_url('plugins/slick/slick/slick.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('plugins/printJS/jQuery.print.js') ?>" type="text/javascript"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        //kitchenQue();
        refreshOrderListContainer();
        $(".mySwitch").bootstrapSwitch();
    });

    function updateToCurrent(id, tmpVal, kotID) {
        var valueTmp = $(tmpVal).is(":checked");
        if (valueTmp) {
            var cook = 1;
        } else {
            var cook = 0;
        }

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_kitchen/updateToCurrent'); ?>",
            data: {id: id, value: cook, kotID: kotID},
            cache: false,
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (data) {
                $("#loader").hide();
                if (data['error'] == 0) {
                    refreshOrderListContainer()
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function updateToCompleted(id, tmpVal, kotID) {
        var valueTmp = $(tmpVal).is(":checked");
        if (valueTmp) {
            var cook = 1;
        } else {
            var cook = 0;
        }

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_kitchen/updateToCompleted'); ?>",
            data: {id: id, value: cook, kotID: kotID},
            cache: false,
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (data) {
                $("#loader").hide();
                if (data['error'] == 0) {
                    refreshOrderListContainer()
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function kitchenQue() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_kitchen/refreshOrderListContainer'); ?>",
            data: {id: null},
            cache: false,
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (data) {
                $("#loader").hide();
                $("#orderListContainer").html(data);
                setTimeout(function () {
                    kitchenQue();
                }, 10000);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function refreshOrderListContainer() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_kitchen/kitchen_manual_process_ajax'); ?>",
            data: {id: null},
            cache: false,
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (data) {
                $("#loader").hide();
                $("#orderListContainer").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

</script>