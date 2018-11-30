<?php
echo fetch_account_review(true, false, $approval); ?>
    <br>
    <table class="table table-bordered table-condensed" style="background-color: #f0f3f5;">
        <tbody>
        <tr>
            <td style="width: 110px;">Yield</td>
            <td class="bgWhite" style="width:35%"><?php echo $extra['yield']['itemDescription']; ?></td>
            <td style="width: 110px;">Document Code</td>
            <td colspan="2" class="bgWhite"><?php echo $extra['yield']['documentSystemCode']; ?> </td>
        </tr>
        <tr>
            <td>Document Date</td>
            <td class="bgWhite"><?php echo $extra['yield']['documentDate']; ?></td>
            <td style="width: 110px;">UOM</td>
            <td colspan="2" class="bgWhite"><?php echo $extra['yield']['uom']; ?> </td>
        </tr>
        <tr>
            <td>Currency</td>
            <td class="bgWhite"><?php echo $extra['yield']['currency'] ?></td>
            <td>QTY</td>
            <td class="bgWhite"><?php echo $extra['yield']['qty']; ?> </td>
        </tr>
        <tr>
            <td>Financial Year</td>
            <td class="bgWhite" width="110px;">From: <span
                        class=""><?php echo date('d/m/Y', strtotime($extra['yield']['FYBegin'])) ?></span> To: <span
                        class=""><?php echo date('d/m/Y', strtotime($extra['yield']['FYEnd'])) ?></span></td>
            <td>Warehouse</td>
            <td class="bgWhite" colspan="2"><?php echo $extra['yield']['warehouse'] ?></td>
        </tr>
        <tr>
            <td>Financial Period</td>
            <td class="bgWhite" width="110px;">From: <span
                        class=""><?php echo date('d/m/Y', strtotime($extra['yield']['datefrom'])) ?></span> To: <span
                        class=""><?php echo date('d/m/Y', strtotime($extra['yield']['dateTo'])) ?></span></td>
            <td>Narration</td>
            <td class="bgWhite" colspan="2"><?php echo $extra['yield']['narration'] ?></td>
        </tr>

        </tbody>
    </table>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class='thead'>
            <tr>
                <th style="min-width: 4%" class='theadtr'>Item</th>
                <th style="min-width: 10%" class='theadtr'>UOM</th>
                <th style="min-width: 30%" class="theadtr">QTY</th>
                <th style="min-width: 5%" class='theadtr'>Unit Cost</th>
                <th style="min-width: 5%" class='theadtr'>Total</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total = 0;
            if (!empty($extra['yield_detail'])) {
                foreach ($extra['yield_detail'] as $val) { ?>
                    <tr>
                        <td class="text-left"><?php echo $val['itemDescription']; ?></td>
                        <td class="text-left"><?php echo $val['unitofmeasure']; ?> </td>
                        <td class="text-right"> <?php echo $val['totalQty']; ?></td>
                        <td class="text-right"> <?php echo $val['localWacAmount']; ?></td>
                        <td class="text-right"> <?php echo number_format( $val['localWacAmountTotal'],2); ?></td>
                    </tr>
                    <?php
                    $total += $val['localWacAmountTotal'];
                }
            } else {
                echo '<tr class="danger"><td colspan="5" class="text-center">No Records Found</td></tr>';
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <td style="min-width: 85%  !important" class="text-right sub_total" colspan="4">
                    Total
                </td>
                <td style="min-width: 15% !important"
                    class="text-right total"><?php echo number_format($total, 2); ?></td>
            </tr>
            </tfoot>
        </table>
    </div>
    <br>
<?php if ($extra['yield']['confirmedYN']) { ?>
    <div class="table-responsive" style="margin-top: 5px">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:10%;" valign="top"><strong> Created By </strong></td>
                <td style="width:2%;" valign="top"><strong>:</strong></td>
                <td style="text-align: justify"><?php echo $extra['yield']['createdUserName']; ?>&nbsp; <strong>on</strong>&nbsp;  <?php echo date('d-m-Y', strtotime($extra['yield']['createdDateTime'])) ?></td>
            </tr>
            <tr>
                <td style="width:10%;" valign="top"><strong> Confirmed By </strong></td>
                <td style="width:2%;" valign="top"><strong>:</strong></td>
                <td style="text-align: justify"><?php echo $extra['yield']['confirmedUserName']; ?>&nbsp; <strong>on</strong>&nbsp; <?php echo $extra['yield']['confirmedDateTime'];?></td>
            </tr>
            </tbody>
        </table>
    </div>
<?php } ?>