<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$outletInfo = get_outletInfo();

?>
<style>
    .timeContainer {
        padding: 0px 5px;
        color: #15d80b;
        text-align: center;
        font-weight: 800;
    }

    .bgBlack {
        background-color: #000000;
        color: #fdff05;
    }
</style>
<script>
    function changeKOTLocation(id) {
        window.location.replace("<?php echo site_url('kot_manual') ?>/" + id.value + '/1');
    }
</script>
<div class="row">
    <div class="col-md-2">
        <h4 class="text-purple text-left">
            &nbsp;&nbsp;<i class="fa fa-building-o"></i>
            <?php echo $outletInfo['wareHouseDescription'] ?>
        </h4>
    </div>
    <div class="col-md-4">
        <h4 class="text-yellow text-center">
            <i class="fa fa-cutlery"></i> <?php echo $this->lang->line('posr_pending_order'); ?><!--Pending Order-->
        </h4>
    </div>
    <div class="col-md-3">
        <h4 class="text-red text-center"><i class="fa fa-cutlery"></i>
            <?php echo $this->lang->line('posr_progressing'); ?><!--Progressing-->
        </h4>
    </div>
    <div class="col-md-2">
        <?php $kotID = $this->input->post('kotID'); ?>
        <select name="" id="" class="form-control"
                style="margin:4px 0px; margin: 4px 0px; font-size: 19px; padding: 0px; font-weight: 600; color: #1d549a;"
                onchange="changeKOTLocation(this)">
            <option value="0">Please select</option>
            <?php
            $kotLocation = get_kitchenLocation();
            if (!empty($kotLocation)) {
                foreach ($kotLocation as $item) {
                    if (isset($kotID) && $kotID == $item['kitchenLocationID']) {
                        $selected = ' selected ';
                    } else {
                        $selected = ' ';
                    }

                    $url = site_url('kot_manual');
                    echo '<option ' . $selected . ' value="' . $item['kitchenLocationID'] . '">' . $item['description'] . '</option>';
                }
            }

            ?>
        </select>
    </div>

    <div class="col-md-1">
        <a style="border-radius: 0px;" href="<?php echo site_url('dashboard') ?>" class="btn btn-danger pull-right"><i
                class="fa fa-remove fa-2x"></i> </a>
    </div>

</div>


<div class="row">

    <div class="col-md-6" style="border:1px dashed #b30300; min-height: 500px;">

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
                                        <div class="row bgBlack">
                                            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                                                <h4 class="title">
                                                    <i class="fa fa-tag"></i>
                                                    <span
                                                        style="font-weight: 800"> <?php echo $pendingOrder['invoiceCode']; ?> </span>
                                                </h4>
                                            </div>

                                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                                <h4 class="timeContainer" style="margin: 0px; padding:5px;">
                                                    <?php
                                                    $curTime = date('Y-m-d H:i:s');
                                                    $datetime1 = date_create($curTime);
                                                    $datetime2 = date_create($pendingOrder['createdDateTime']);
                                                    $interval = date_diff($datetime1, $datetime2);
                                                    echo $interval->format('%h : %i : %s');
                                                    ?>
                                                </h4>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php
                                $pendingOrder['kotID'] = isset($pendingOrder['kotID']) ? $pendingOrder['kotID'] : 0;
                                ?>
                                <input class="mySwitch" type="checkbox" value="1"
                                       id="isPax_<?php echo $pendingOrder['menuSalesID'] ?>" name="pending"
                                       onchange="updateToCurrent(<?php echo $pendingOrder['menuSalesID'] ?>,this,<?php echo $pendingOrder['kotID'] ?>)"
                                       data-size="small"
                                       data-on-text="<i class='fa fa-cutlery text-red'></i> Cook"
                                       data-on-color="default"
                                       data-handle-width="50"
                                       data-off-color="default"
                                       data-off-text="<i class='fa fa-warning text-yellow'></i> &nbsp;"
                                       data-label-width="0"></td>
                            <td class="title" style="vertical-align: top;">
                                <span class="pull-right"
                                      style="font-weight: 800"><?php echo $this->lang->line('posr_qyt'); ?><!--QTY--></span>
                            </td>

                        </tr>
                        <?php
                    }
                    $titleID = $pendingOrder['menuSalesID'];
                    ?>
                    <tr>
                        <td colspan="2">
                            <div class="detailList">
                                <img src="<?php echo base_url($pendingOrder['menuImage']) ?>"
                                     style="height: 40px; width: 40px;" alt="">
                                <?php echo $pendingOrder['menuMasterDescription']; //print_r($pendingOrder);?>
                                &nbsp;&nbsp;
                                <span style="font-weight: bold; color:red"><?php echo $pendingOrder['menuDescription']; ?></span>
                                <?php
                                echo !empty($pendingOrder['kitchenNote']) ? '<br/>&nbsp;&nbsp;&nbsp;<i class="fa fa-star" style="color:#d54136"></i> ' . $pendingOrder['kitchenNote'] : '';
                                $menuSalesItemID = $pendingOrder['menuSalesItemID'];
                                $output = get_add_on_byItem($menuSalesItemID);
                                if (!empty($output)) {
                                    foreach ($output as $val) {
                                        echo '<br/>&nbsp;&nbsp;&nbsp;<i class="fa fa-star" style="color:#d54136"></i> ' . $val['menuMasterDescription'];
                                    }
                                }

                                ?>


                            </div>
                        </td>
                        <td style="font-weight: 800; vertical-align: top; font-size:16px !important; "><span
                                class="pull-right"> <?php echo $pendingOrder['qty']; ?></span></td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-6" style="border:1px dashed #b30300; min-height: 500px; margin-left:-1px;">

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
                                        <div class="row bgBlack">
                                            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                                                <h4 class="title">

                                                    <i class="fa fa-tag"></i>
                                                    <span
                                                        style="font-weight: 800"> <?php echo $currentOrder['invoiceCode']; ?> </span>

                                                </h4>
                                            </div>
                                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                                <h4 class="timeContainer" style="margin: 0px; padding:5px;">
                                                    <?php
                                                    $curTime = date('Y-m-d H:i:s');
                                                    $datetime1 = date_create($curTime);
                                                    $datetime2 = date_create($currentOrder['createdDateTime']);
                                                    $interval = date_diff($datetime1, $datetime2);
                                                    echo $interval->format('%h : %i : %s');
                                                    ?>
                                                </h4>
                                            </div>

                                        </div>
                                    </div>
                                </div>


                            </td>
                            <td>
                                <input class="mySwitch" type="checkbox" value="1"
                                       id="isPax_<?php echo $currentOrder['menuSalesID'] ?>" name="pending"
                                       onchange="updateToCompleted(<?php echo $currentOrder['menuSalesID'] ?>,this,<?php echo $currentOrder['kotID'] ?>)"
                                       data-size="small"
                                       data-on-text="<i class='fa fa-check text-green'></i> &nbsp;"
                                       data-on-color="default"
                                       data-handle-width="50"
                                       data-off-color="default"
                                       data-off-text="<i class='fa fa-cutlery text-green'></i> Done"
                                       data-label-width="0">
                            </td>
                            <td class="title">
                                <span class="pull-right"
                                      style="font-weight: 800; vertical-align: top;"><?php echo $this->lang->line('posr_qyt'); ?><!--QTY--></span>
                            </td>
                        </tr>
                        <?php
                    }
                    $titleID = $currentOrder['menuSalesID'];
                    ?>
                    <tr>
                        <td colspan="2">
                            <div class="detailList">
                                <img src="<?php echo base_url($currentOrder['menuImage']) ?>"
                                     style="height: 40px; width: 40px;" alt="">
                                <?php echo $currentOrder['menuMasterDescription']; ?>
                                <span style="font-weight: bold; color:red"><?php echo $currentOrder['menuDescription']; ?></span>
                                <?php
                                echo !empty($currentOrder['kitchenNote']) ? '<br/>&nbsp;&nbsp;&nbsp;<i class="fa fa-star" style="color:#d54136"></i> ' . $currentOrder['kitchenNote'] : '';

                                $menuSalesItemID = $currentOrder['menuSalesItemID'];
                                $output = get_add_on_byItem($menuSalesItemID);
                                if (!empty($output)) {
                                    foreach ($output as $val) {
                                        echo '<br/>&nbsp;&nbsp;&nbsp;<i class="fa fa-star" style="color:#d54136"></i> ' . $val['menuMasterDescription'];
                                    }
                                }

                                ?>

                            </div>
                        </td>
                        <td style="vertical-align: top; font-weight: 800; font-size: 16px !important;"><span
                                class="pull-right"> <?php echo $currentOrder['qty']; ?></span></td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>

    </div>

</div>


<script type="text/javascript">
    $(document).ready(function () {
        $(".mySwitch").bootstrapSwitch();
    });
</script>