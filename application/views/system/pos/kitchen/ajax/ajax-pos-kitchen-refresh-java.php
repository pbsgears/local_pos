<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$outletInfo = get_outletInfo();
//print_r($outletInfo);
$alarm = false;
$menuID = array();
?>

    <audio id="buzzer" src="<?php echo base_url('uploads/music/nokia_message2.m4r') ?>" type="audio/m4r"></audio>
    <input type="button" value="Start" class="hide" id="start"/>
    <div class="dynamicSizeCategory">
        <div class="row">
            <div class="col-md-2">
                <h4 class="text-purple text-left">
                    &nbsp;&nbsp;<i class="fa fa-building-o"></i>
                    <?php echo $outletInfo['wareHouseDescription'] ?>
                </h4>

            </div>
            <div class="col-md-7">
                <h4 class="text-red text-center">


                    <!--(-->
                    <strong><?php //echo isset($pendingOrders) && !empty($pendingOrders) ? count($pendingOrders) : 0; ?></strong>
                    <!--)--></h4>
                <!--Pending Orders -->

            </div>

            <div class="col-md-2">
                <select name="" id="" class="form-control"
                        style="margin:4px 0px; margin: 4px 0px; font-size: 19px; padding: 0px; font-weight: 600; color: #1d549a;"
                        onchange="changeKOTLocation(this)">
                    <option value="0">Please select</option>
                    <?php
                    $kotLocation = get_kitchenLocation();
                    //print_r($kotLocation);
                    if (!empty($kotLocation)) {
                        foreach ($kotLocation as $item) {
                            if (isset($kotID) && $kotID == $item['kitchenLocationID']) {
                                $selected = ' selected ';
                            } else {
                                $selected = ' ';
                            }

                            echo '<option ' . $selected . ' value="' . $item['kitchenLocationID'] . '">' . $item['description'] . '</option>';
                        }
                    }

                    ?>
                </select>
            </div>

            <div class="col-md-1">
                <a style="border-radius: 0px;" href="<?php echo site_url('dashboard') ?>"
                   class="btn btn-danger pull-right"><i
                        class="fa fa-remove fa-2x"></i> </a>
            </div>

        </div>


        <div class="row">

            <div class="col-md-12" style="min-height: 500px; border:1px dashed;"> <!--border:1px dashed #b30300; -->

                <?php
                $lastMenuID = 0;
                $pendingOrders = $Set2;
                if (!empty($pendingOrders)) {
                    $titleID = 0;
                    $i = 0;

                    foreach ($pendingOrders as $pendingOrder) {
                        if ($pendingOrder['KOTAlarm'] == 0) {
                            $alarm = true;
                            $menuID[] = $pendingOrder['menuSalesID'];
                        }
                        if ($titleID != $pendingOrder['menuSalesID']) {
                            $i++;
                            ?>
                            <div class="media">
                                <div class="media-body">
                                    <h4 class="title">
                                        <?php
                                        //echo $i . '&nbsp;&nbsp;&nbsp;';
                                        echo '&nbsp;&nbsp;&nbsp;';
                                        ?>

                                        <span style="font-weight: 800">
                                        <?php
                                        echo $pendingOrder['invoiceCode'];
                                        ?>
                                    </span>
                                        <span class="pull-right">
                                        <strong>Qty</strong>

                                    </span>
                                    </h4>
                                </div>
                            </div>

                            <?php
                        }

                        //continue;
                        $titleID = $pendingOrder['menuSalesID'];
                        ?>
                        <div style=" padding:0px 20px 2px 2px;">
                            <div class="detailList">
                                <img src="<?php echo base_url($pendingOrder['menuImage']) ?>"
                                     style="height: 40px; width: 40px;" alt="">
                                <?php //echo $pendingOrder['invoiceCode'] ?>
                                <?php echo $pendingOrder['menuMasterDescription']; //print_r($pendingOrder);?>
                                &nbsp;&nbsp;
                                <?php echo !empty($pendingOrder['kitchenNote']) ? '<i class="fa fa-star" style="color:#d54136"></i> ' . $pendingOrder['kitchenNote'] : ''; ?>
                                <span class="pull-right"> <strong><?php echo $pendingOrder['qty']; ?></strong> </span>
                            </div>
                        </div>
                        <?php
                    }

                }
                ?>
            </div>

            <div class="col-md-6 hide" style="min-height: 500px; border:1px solid">
                <!--border:1px dashed #b30300; -->
                <?php
                $lastMenuID = 0;
                //$pendingOrders = $Set2;
                if (!empty($pendingOrders)) {
                    $titleID = 0;
                    $i = 5;

                    foreach ($pendingOrders as $pendingOrder) {
                        if ($titleID != $pendingOrder['menuSalesID']) {
                            $i++;
                            ?>
                            <div class="media">
                                <div class="media-body">
                                    <h4 class="title">
                                        <?php
                                        // echo $i . '&nbsp;&nbsp;&nbsp;';
                                        echo '&nbsp;&nbsp;&nbsp;';
                                        ?>

                                        <span style="font-weight: 800">
                                        <?php
                                        echo $pendingOrder['invoiceCode'];
                                        ?>
                                    </span>
                                        <span class="pull-right"><strong>Qty</strong></span>
                                    </h4>
                                </div>
                            </div>

                            <?php
                        }

                        //continue;
                        $titleID = $pendingOrder['menuSalesID'];
                        ?>

                        <div class="detailList">
                            <img src="<?php echo base_url($pendingOrder['menuImage']) ?>"
                                 style="height: 40px; width: 40px;" alt="">
                            <?php echo $pendingOrder['invoiceCode'] ?>
                            <?php echo $pendingOrder['menuMasterDescription']; //print_r($pendingOrder);?>
                            &nbsp;&nbsp;
                            <?php echo !empty($pendingOrder['kitchenNote']) ? '<i class="fa fa-star" style="color:#d54136"></i> ' . $pendingOrder['kitchenNote'] : ''; ?>


                            <span class="pull-right"> <strong><?php echo $pendingOrder['qty']; ?></strong> </span>
                        </div>


                        <?php

                    }

                }
                ?>

            </div>
        </div>


    </div>


    </div>
    <div class="row">
        <div class="col-md-6">
            <div style="padding: 5px;"><strong> Copyright &copy; 2015-2020</strong> All rights reserved.</div>
        </div>
        <div class="col-md-6">
            <div class="pull-right" style="padding: 5px;"><?php echo current_companyName() ?></div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function () {
                $(".dynamicSizeCategory").css("height", $(window).height() - 250 + 'px');
                //$(".mySwitch").bootstrapSwitch();
            }, 100);

            var buzzer = $('#buzzer')[0];

            $(document).on('submit', '#sample', function () {
                buzzer.play();
                return false;
            });


            $('#start').on('click', function () {
                $('#buzzer').get(0).play();
            });


            <?php
            if($alarm){
                ?>$("#start").click();<?php
            }
            ?>




        });
    </script>
<?php
if ($alarm) {
    if (!empty($menuID)) {
        updateKOT_alarm($menuID);
    }
}
?>