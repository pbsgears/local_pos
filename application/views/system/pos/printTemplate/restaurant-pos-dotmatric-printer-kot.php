<div>
    <style>
        .vLineKOT {
            border-bottom: 1px dashed;
            height: 0px;
            margin: 3px;
        }

        .kitchenHeader {
            font-weight: 600 !important;
            font-size: 14px !important;
        }
    </style>
    <?php

    $paymentTypes = get_bill_payment_types($masters['menuSalesID']);
    $tmpPayTypes = '';
    if (!empty($paymentTypes)) {
        foreach ($paymentTypes as $paymentType) {
            $tmpPayTypes .= $paymentType['description'] . ', ';
        }
        $tmpPayTypes = '(' . rtrim($tmpPayTypes, ', ') . ')';
    }

    $data['paymentTypes'] = '';

    $companyInfo = get_companyInfo();
    $outletInfo = get_outletInfo();

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('pos_restaurent', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('calendar', $primaryLanguage);
    $uniqueID = time();
    //print_r($masters)

    ?>
    <div id="print_content<?php echo $uniqueID; ?>">


        <table style="width: 100%">
            <tr>
                <td style="width:25%; text-align: left;">
                    <?php echo $this->lang->line('posr_ord_type') . ':'; ?><!--Ord.Type-->
                </td>
                <td style="width:30%"> <?php echo $masters['customerDescription'] ?>   </td>
                <td style="width:20%; "><?php echo $this->lang->line('posr_inv_no') . ':'; ?><!--Inv. No--> </td>
                <td style="width:25%;"
                    class="ar"><?php echo get_pos_invoice_code($masters['menuSalesID']) ?> </td>
            </tr>
            <tr>
                <td style="text-align: left;"><?php echo $this->lang->line('common_date') . ':'; ?><!--Date--> </td>
                <td> <?php echo date('d/m/Y', strtotime($masters['createdDateTime'])) ?></td>
                <td><?php echo $this->lang->line('common_time'); ?><!--Time-->:</td>
                <td class="ar"><?php echo date('g:i A', strtotime($masters['createdDateTime'])) ?></td>
            </tr>
            <?php if (isset($masters['deliveryOrderID']) && $masters['deliveryOrderID']) { ?>
                <tr>
                    <td style="text-align: left;">Delivery Date</td>
                    <td> <?php echo !empty($masters['deliveryDate']) ? date('d/m/Y', strtotime($masters['deliveryDate'])) : '-'; ?></td>
                    <td>Delivery Time</td>
                    <td class="ar"><?php echo !empty($masters['deliveryTime']) ? date('g:i A', strtotime($masters['deliveryTime'])) : '-'; ?></td>
                </tr>
            <?php } ?>

            <?php if (!empty($masters['diningTableDescription'])) {
                ?>
                <tr>
                    <td style="text-align: left;">Table</td>
                    <td> <?php echo $masters['diningTableDescription']; ?></td>
                    <td>Packs</td>
                    <td class="ar"><?php echo $masters['numberOfPacks'] > 0 ? $masters['numberOfPacks'] : ''; ?></td>
                </tr>

                <tr>
                    <td style="text-align: left;">waiter</td>
                    <td> <?php echo $masters['crewLastName']; ?></td>
                    <td>&nbsp;</td>
                    <td class="ar">&nbsp;</td>
                </tr>
                <?php
            } ?>
            <?php if (!empty($masters['holdRemarks'])) {
                ?>
                <tr>
                    <td>Remarks</td>
                    <td colspan="3"><?php echo $masters['holdRemarks']; ?></td>
                </tr>

                <?php
            } ?>
        </table>


        <div style="clear:both;"></div>

        <div class="vLineKOT">&nbsp;</div>
        <table style="width: 100%;" border="0">

            <tr>
                <td>#</td>
                <td>Item</td>
                <td>Qty</td>
                <!--<th>Kitchen</th>-->
            </tr>
            <tr>
                <td colspan="3">
                    <div class="vLineKOT">&nbsp;</div>
                </td>
            </tr>

            <?php
            $templateID = get_pos_templateID();
            $i = 1;

            if (!empty($invoiceList)) {
                $i = 1;
                $kotID = 0;
                foreach ($invoiceList as $item) {
                    if ($kotID == 0 || $kotID != $item['kotID']) {
                        ?>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="kitchenHeader"><?php echo $item['kitchenName'] ?></td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div class="vLineKOT">&nbsp;</div>
                            </td>
                        </tr>
                        <?php
                    }
                    $kotID = $item['kotID'];
                    ?>
                    <tr>
                        <td style="vertical-align: top;"><?php echo $i;
                            $i++; ?></td>
                        <td align="left">

                            <?php
                            echo $item['menuMasterDescription'];
                            if (!empty($item['kitchenNote'])) {
                                echo '<br/><strong>&nbsp;&nbsp;' . $item['kitchenNote'] . '</strong>';
                            }


                            $menuSalesItemID = $item['menuSalesItemID'];
                            $output = get_add_on_byItem($menuSalesItemID);
                            if (!empty($output)) {
                                foreach ($output as $val) {
                                    echo '<br/><strong>&nbsp; - ' . $val['menuMasterDescription'] . '</strong>';
                                }
                            }
                            ?>
                        </td>
                        <td class="text-center" style="vertical-align: top;">
                            <?php echo $item['qty']; ?>
                        </td>
                        <!--<td><?php /*echo $item['KOT_description'] */ ?></td>-->
                    </tr>
                    <tr>
                        <td>
                            <div style="margin: 2px; height: 0px; ">&nbsp;</div>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>

        </table>
    </div>
    <?php if ($print) { ?>
        <div class="vLineKOT">&nbsp;</div>

        <div id="bkpos_wrp">
            <button type="button" onclick="$.print('#print_content<?php echo $uniqueID ?>')"
                    style="width:101%; cursor:pointer; font-size:12px; background-color:#FFA93C; color:#000; text-align: center; border:1px solid #FFA93C; padding: 10px 0px; font-weight:bold;">
                <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
            </button>
        </div>
    <?php }
    if ($newBill) {
        ?>
        <button type="button" class="btn btn-primary btn-lg" onclick="holdAndCreateNewBill()"
                style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center;  10px 1px; margin: 5px auto 10px auto; font-weight:bold; border-radius: 0px;">
            Hold & Create New Order
        </button>
        <?php
    }
    ?>


</div>