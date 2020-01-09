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
<input type="button" value="Start" class="" id="start" step="0"/>
<div class="row">
    <div class="col-md-5">
        <h4 class="text-purple text-left">
            &nbsp;&nbsp;<i class="fa fa-building-o"></i>
            <?php echo $outletInfo['wareHouseDescription'] ?>
        </h4>

    </div>


    <div class="col-md-5">
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
        <a style="border-radius: 0px;" href="<?php echo site_url('dashboard') ?>" class="btn btn-danger pull-right">
            <i class="fa fa-remove fa-2x"></i> </a>
    </div>
</div>


<div>


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
        }
        ?>




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
        $(".mySwitch").bootstrapSwitch();
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




