<?php

$printSession = $this->session->userdata('accessToken');
if (!empty($printSession)) {
    ?>
    <div>
        <style>
            .vLineKOT {
                border-bottom: 1px dashed;
                height: 0px;
                margin: 3px;
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
        //var_dump($invoiceList)

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
                    foreach ($invoiceList as $item) {
                        ?>
                        <tr>
                            <td><?php echo $i;
                                $i++; ?></td>
                            <td align="left">

                                <?php echo $item['menuMasterDescription'];
                                if (!empty($item['kitchenNote'])) {
                                    echo '<br/><strong>' . $item['kitchenNote'] . '</strong>';
                                }
                                ?>

                            </td>
                            <td class="text-center">
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
    <?php
} else {
    ?>
    <div class="alert alert-warning">

        <strong><i class="fa fa-info-circle"></i> Login </strong><br/>
        <p>Please Login to Google Account</p>

        <p><a href="<?php echo STATIC_LINK ?>/index.php/ReceiptPrint/oAuthRedirect?op=getauth">Click
                here</a> to Login</p>
    </div>
    <?php
}
?>
