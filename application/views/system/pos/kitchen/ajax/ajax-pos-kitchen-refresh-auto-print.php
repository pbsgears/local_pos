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

<div class="row">


    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <h4 class="text-purple text-left">
            &nbsp;&nbsp;<i class="fa fa-building-o"></i>
            <?php echo $outletInfo['wareHouseDescription'] ?>
        </h4>

    </div>


    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <select name="" id="" class="form-control"
                style="margin:4px 0px; margin: 4px 0px; font-size: 19px; padding: 0px; font-weight: 600; color: #1d549a;"
                onchange="printKOTLocation(this)">
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

    <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2" style="padding:6px;">
        <button class="btn btn-primary" type="button" onclick="autoPrintKOT()">Print <i
                class="fa fa-print"></i></button>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-1 col-lg-1" style="padding:4px;">
        <input type="button" value="Test Sound" class="btn btn-default" id="start" step="0">
    </div>
    <div class="col-xs-4 col-sm-4 col-md-1 col-lg-1">
        <a style="border-radius: 0px;" href="<?php echo site_url('dashboard') ?>" class="btn btn-danger pull-right"><i
                class="fa fa-remove fa-2x"></i> </a>
    </div>
</div>


<div id="printContent">
    <?php

    if (!empty($pendingOrders)) {
        $titleID = 0;
        $i = 0;
        foreach ($pendingOrders as $pendingOrder) {
            // <!--Pending Orders -->
            /** sent to print*/
            if (isset($pendingOrder['KOTAlarm']) && $pendingOrder['KOTAlarm'] == 0) {
                $alarm = true;
                $menuID[] = $pendingOrder['menuSalesID'];

            }


        }
        echo $html;
    } else {
        echo '<div class="text-center"  style="margin:30px 0px;"> No Pending orders</div>';
    }
    ?>
</div>


<script type="text/javascript">
    function autoPrintKOT() {
        $.print('#printContent');
    }
    $(document).ready(function () {

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
        ?>
        $("#start").click();
        $.print('#printContent');
        <?php
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




