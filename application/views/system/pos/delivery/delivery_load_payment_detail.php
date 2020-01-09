<div style="padding:2px;">
    <table class="<?php echo table_class_pos() ?>">
        <tbody>
        <tr>
            <td><strong>Date</strong></td>
            <td><strong>Payment Type</strong></td>
            <td class="text-right"><strong>Amount</strong></td>
        </tr>

        <?php
        if (isset($payments) && !empty($payments)) {
        $total = 0;
        foreach ($payments as $payment) {
            ?>
            <tr>
                <td><?php echo date('d-m-Y', strtotime($payment['createdDateTime'])); ?></td>
                <td><?php echo $payment['paymentDescription']; ?></td>
                <td class="text-right"><?php echo number_format($payment['amount'], 2);
                    $total += $payment['amount']; ?></td>
            </tr>
            <?php
        }
        ?>
        <tfoot>
        <tr>
            <td colspan="2">Total</td>
            <td class="text-right"><?php echo number_format($total, 2); ?></td>
        </tr>
        </tbody>
        <?php
        } else {
            ?>

            <tr>
                <td colspan="2">Not Payment made yet</td>
            </tr>
            </tfoot>
            <?php
        }
        ?>
    </table>
</div>
<?php
///echo isset($invoiceID) ? $invoiceID : 0;

