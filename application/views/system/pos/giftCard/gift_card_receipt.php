<style type="text/css">
    .headerTxt {
        font-size: 11px !important;
        margin: 0px;
        text-align: center;
    }

    .fWidth {
        width: 100% !important;;
    }

    .fSize {
        font-size: 12px !important;
    }

    .f {
        /*font-family: monospace, sans-serif, Verdana, Geneva !important;*/
        font-family: 'Raleway', Arial, sans-serif !important;
    }

    .pad-top {
        padding-top: 1px;
    }

    .ac {
        text-align: center !important;
    }

    .ar {
        text-align: right !important;
    }

    #tblListItems tr td {
        padding: 0px 1px !important;

    }

    .vLine {
        border-top: 1px dashed #000;
        margin: 4px 0px;
        height: 2px;
    }


</style>
<div id="wrapper">

    <?php
    $payments = isset($giftCardPayments) && !empty($giftCardPayments) ? $giftCardPayments : exit('sorry for inconvenient, payment id is missing!');

    $datetime = $giftCardPayments[0]['createdDateTime'];
    $giftCardReceiptID = $giftCardPayments[0]['giftCardReceiptID'];
    $total = 0;
    if (!empty($payments)) {
        foreach ($payments as $payment) {
            $total += $payment['topUpAmount'];
        }
    }

    $companyInfo = get_companyInfo();
    $outletInfo = get_outletInfo();


    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('pos_restaurent', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('calendar', $primaryLanguage);
    $uniqueID = time();


    ?>
    <div id="print_content<?php echo $uniqueID; ?>">
        <script>
            function print_paymentReceipt() {
                $.print('#print_content<?php echo $uniqueID ?>');
                setTimeout(function () {
                    $("#rpos_print_template").modal('hide');
                }, 3000);
            }
        </script>
        <table border="0" style="width:100%" class="f fSize fWidth">
            <tbody>
            <tr>
                <td width="100%" class="ac">
                    <?php
                    if (!empty($outletInfo['warehouseImage'])) {
                        $LogImage = 'uploads/warehouses/' . $outletInfo['warehouseImage'];
                        ?>
                        <div>
                            <img
                                src="<?php echo base_url($LogImage) ?>"
                                alt="Restaurant Logo" style="max-height: 80px;">
                        </div>
                        <?php
                    }
                    ?>

                    <div style=" padding: 0px; font-size:11px;">WELCOME TO</div>
                    <div class="headerTxt" style="font-size:17px !important; text-align: center;">
                        <?php echo $outletInfo['wareHouseDescription']; ?>
                    </div>
                    <div class="headerTxt" style="text-align: center;">
                        <?php echo $outletInfo['warehouseAddress']; ?>
                    </div>
                    <div class="headerTxt" style="text-align: center;">
                        <?php echo 'TEL : ' . $outletInfo['warehouseTel']; ?>
                    </div>
                    <div class="headerTxt" style="text-align: center;">
                        <?php echo $companyInfo['companyPrintOther'] ?>
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="headerTxt" style="margin-top:5px;">
                        <table style="width: 100%" class="f">
                            <tr>
                                <td style="width:20%; text-align: left;">
                                    Customer :
                                </td>
                                <td style="width:35%"> <?php echo isset($giftCardInfo) ? $giftCardInfo['CustomerName'] : ''; ?> </td>

                                <td style="text-align: left; width:20%;">Receipt No.</td>
                                <td class="ar"><?php echo $giftCardReceiptID; ?></td>


                            </tr>
                            <tr>
                                <td>Telephone:</td>
                                <td><?php echo isset($giftCardInfo) ? $giftCardInfo['customerTelephone'] : ''; ?>  </td>

                                <td><?php echo $this->lang->line('common_time') . '<!--Time-->:'; ?></td>
                                <td class="ar"><?php echo date('g:i A', strtotime($datetime)) ?></td>
                            </tr>
                            <tr>
                                <td>Card No:</td>
                                <td><?php echo $giftCardInfo['barcode']; ?>  </td>

                                <td ><?php echo $this->lang->line('common_date') . ':'; //<!--Date--> ?> </td>
                                <td class="ar"
                                    style="width:25%;"> <?php echo date('d/m/Y', strtotime($datetime)) ?></td>
                            </tr>

                        </table>
                    </div>
                </td>
            </tr>


            </tbody>
        </table>

        <div class="vLine">&nbsp;</div>
        <table cellspacing="0" border="0" style="width:100%" class="f fWidth" id="tblListItems">
            <tr>

                <td style="width:20%; text-align: left;">
                    <?php echo $this->lang->line('common_description'); ?><!--Description--></td>
                <td style="width:5%; text-align: left;">&nbsp;</td>
                <td style="width:15%; text-align: right;">
                    <?php echo $this->lang->line('common_price'); ?><!--Price--></td>
            </tr>
        </table>
        <div class="vLine">&nbsp;</div>
        <table cellspacing="0" border="0" style="width:100%" class="f fWidth" id="tblListItems">
            <tbody>

            <tr>
                <td width="20%" align="left">
                    Gift Card Top up
                </td>
                <td width="5%">
                    &nbsp;
                </td>
                <td width="15%" align="right">
                    <?php echo number_format($total, 2); ?>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="vLine">&nbsp;</div>

        <table class="totals f" style="width:100%" cellspacing="0" border="0">
            <tbody>


            <?php
            if (!empty($payments)) {
                foreach ($payments as $payment) {
                    ?>
                    <tr>
                        <td colspan="2">
                            <strong>
                                <?php
                                echo $payment['description'];
                                ?>
                            </strong>
                        </td>
                        <td class="text-right">
                            <strong>
                                <?php

                                echo number_format($payment['topUpAmount'], 2)
                                ?>
                            </strong>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>


            </tbody>
        </table>

        <div class="vLine">&nbsp;</div>

        <div class="pad-top">
            <div style="font-size:11px;">
                <div>
                    Cashier : <?php $time = format_date_mysql_datetime();
                    echo get_employeeShortName() ?>
                </div>
                <div>
                    Printed Date Time
                    : <?php echo date('d/m/Y', strtotime($time)) ?> <?php echo date('g:i A', strtotime($time)) ?>
                </div>
            </div>
        </div>

    </div>


</div>
<hr>
<button type="button" onclick="print_paymentReceipt()"
        style="width:101%; cursor:pointer; font-size:12px; background-color:#FFA93C; color:#000; text-align: center; border:1px solid #FFA93C; padding: 10px 0px; font-weight:bold;">
    <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
</button>
