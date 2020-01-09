<?php

echo fetch_account_review(false, true, 1);

?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td>
                            <h4>Till Management Report</h4>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<br>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th style="min-width: 4%" class='theadtr'>#</th>
            <th style="min-width: 10%" class='theadtr'>Start Date - Time <i class="fa fa-clock-o"></i></th>
            <th style="min-width: 30%" class="text-left theadtr">End Date - Time <i class="fa fa-clock-o"></i></th>
            <th style="min-width: 5%" class='theadtr'>Outlet</th>
            <th style="min-width: 5%" class='theadtr'>Counter</th>
            <th style="min-width: 5%" class='theadtr'>Closed By</th>
            <th style="min-width: 10%" class='theadtr'>Opening Cash Balance</th>
            <th style="min-width: 10%" class='theadtr'>Cash Sales</th>
            <th style="min-width: 10%" class='theadtr'>Closing Cash Balance</th>
            <th style="min-width: 10%" class='theadtr'>Till Cash Balance</th>
            <th style="min-width: 10%" class='theadtr'>Diff</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        if (!empty($extra)) {
            foreach ($extra as $value) {
               ?>
                <tr>
                    <td><?php echo $num; ?></td>
                    <td><?php echo $value['startTime']; ?></td>
                    <td><?php echo $value['endTime']; ?></td>
                    <td><?php echo $value['wareHouseDescription']; ?></td>
                    <td><?php echo $value['counterName']; ?></td>
                    <td><?php echo $value['empName']; ?></td>
                    <td><?php echo till_report_numberFormat($value['startingBalance_transaction']); ?></td>
                    <td><?php echo till_report_numberFormat($value['cashSales']); ?></td>
                    <td><?php echo till_report_numberFormat($value['closingCashBalance_transaction']); ?></td>
                    <td><?php echo till_report_numberFormat($value['endingBalance_transaction']); ?></td>
                    <td><?php echo till_report_numberFormat($value['different_transaction']); ?></td>
                </tr>
        <?php
                $num++;
            }
        } else {
            echo '<tr class="danger"><td colspan="11" class="text-center">No Records Found</td></tr>';
        } ?>
        </tbody>
    </table>
</div>



