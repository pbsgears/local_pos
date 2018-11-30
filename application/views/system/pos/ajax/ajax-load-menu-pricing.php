<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
//print_r($menuData);

$total = 0;
?>
<table class="table table-bordered table-hover table-condensed">
    <thead>
    <tr>
        <th>#</th>
        <th>Description</th>
        <th style="width:10%">Percentage&nbsp;%</th>
        <th style="width:30%">Amount</th>
        <th style="width:5%">&nbsp;</th>
    </tr>
    </thead>
    <tbody id="menu_pricing_body">
    <tr>
        <td>1</td>
        <td>
            <div class="pad-price"> Price without Tax</div>
        </td>
        <td>&nbsp;</td>
        <td>
            <input type="text" class="form-control pricingInput ar" id="m_pricewithoutTax"
                   name="pricewithoutTax"
                   value="<?php if (isset($menuData['pricewithoutTax'])) {
                       echo $menuData['pricewithoutTax'];
                       $total += $menuData['pricewithoutTax'];
                   } else {
                       echo '0';
                   } ?>"
                   placeholder="<?php echo $this->lang->line('pos_config_price'); ?>">

        </td>
        <th>&nbsp;</th>
    </tr>

    <?php
    $i = 2;
    if (isset($taxes) && !empty($taxes)) {
        foreach ($taxes as $tax) {
            $total += $tax['taxAmount'];
            ?>
            <tr>
                <td>
                    <?php echo $i;
                    $i++; ?>
                </td>

                <td>
                    <div class="pad-price"> <?php echo $tax['taxShortCode'] . ' - ' . $tax['taxDescription'] ?></div>
                </td>
                <td>
                    <input type="text" class="form-control pricingInput ar" id="m_taxPercentage"
                           name="taxPercentage[<?php echo $tax['menutaxID'] ?>]"
                           value="<?php echo $tax['taxPercentage'] ?>"/>
                </td>
                <td>
                    <input type="text" class="form-control pricingInput ar" id="m_taxAmount"
                           name="taxAmount[<?php echo $tax['menutaxID'] ?>]"
                           value="<?php echo $tax['taxAmount'] ?>"/>
                </td>
                <th>
                    <!--Tax Delete Button-->
                    <button class="btn btn-xs btn-danger" type="button" onclick="delete_menuTax(<?php echo $tax['menutaxID'] ?>,<?php echo $tax['menuMasterID'] ?>)"><i class="fa fa-trash"></i></button>
                </th>
            </tr>
            <?php
        }
    }
    ?>


    <?php
    if (isset($serviceCharges) && !empty($serviceCharges)) {
        foreach ($serviceCharges as $serviceCharge) {
            $total += $serviceCharge['serviceChargeAmount'];
            ?>
            <tr>
                <td>
                    <?php echo $i;
                    $i++; ?>
                </td>

                <td>
                    <div class="pad-price"> Service Charge</div>
                </td>
                <td><input type="text" class="form-control pricingInput ar" id="m_serviceChargePercentage"
                           name="serviceChargePercentage[<?php echo $serviceCharge['menuServiceChargeID'] ?>]"
                           value="<?php echo $serviceCharge['serviceChargePercentage'] ?>"></td>
                <td><input type="text" class="form-control pricingInput ar" id="m_serviceChargeAmount"
                           name="serviceChargeAmount[<?php echo $serviceCharge['menuServiceChargeID'] ?>]"
                           value="<?php echo $serviceCharge['serviceChargeAmount'] ?>"></td>
                <th>
                    <button class="btn btn-xs btn-danger" type="button" onclick="delete_serviceCharge(<?php echo $serviceCharge['menuServiceChargeID'] ?>,<?php echo $serviceCharge['menuMasterID'] ?>)">
                        <i class="fa fa-trash"></i></button>
                </th>
            </tr>
            <?php
        }
    }
    ?>

    <tr>
        <th colspan="3" class="ar">Total</th>
        <th class="ar"><?php echo $total ?></th>
    </tr>
    </tbody>
</table>