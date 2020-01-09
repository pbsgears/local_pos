<div style="border: 1px dashed gray; padding: 5px 10px;">
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
        </table>


        <div style="clear:both;"></div>

        <div class="vLine">&nbsp;</div>
        <table class="<?php echo table_class_pos(5) ?>">
            <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Status</th>
                <th>Kitchen</th>
            </tr>
            </thead>
            <tbody>

            <?php
            $templateID = get_pos_templateID();
            $i = 1;

            if (!empty($invoiceList)) {
                $i = 1;
                foreach ($invoiceList as $item) {
                    ?>
                    <tr>
                        <td><?php echo $i;
                            $i++; ?></td>
                        <td align="left">
                            <?php echo $item['menuMasterDescription'] ?>
                        </td>
                        <td class="text-center">
                            <?php
                            $isOrderPending = $item['isOrderPending'];
                            $isOrderInProgress = $item['isOrderInProgress'];
                            $isOrderCompleted = $item['isOrderCompleted'];
                            $kotID = $item['kotID'];
                            get_kitchenStatus($kotID, $isOrderPending, $isOrderInProgress, $isOrderCompleted)
                            ?>
                        </td>
                        <td><?php echo $item['KOT_description'] ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>

    <!--<div class="vLine">&nbsp;</div>

    <div id="bkpos_wrp">
        <button type="button" onclick=" $.print('#print_content<?php /*echo $uniqueID */ ?>')"
                style="width:101%; cursor:pointer; font-size:12px; background-color:#FFA93C; color:#000; text-align: center; border:1px solid #FFA93C; padding: 10px 0px; font-weight:bold;">
            <i class="fa fa-print"></i> <?php /*echo $this->lang->line('common_print'); */ ?>
        </button>
    </div>--><!--Print-->

</div>