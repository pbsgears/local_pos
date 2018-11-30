<style>
    .customPad {
        padding: 3px 0px;
    }

    /*.al {
        text-align: left !important;
    }*/

    .ar {
        text-align: right !important;
    }

    .alin {
        text-align: center !important;
    }

</style>
<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$c_info = get_companyInfo();
$d = !empty($c_info['company_default_decimal']) ? $c_info['company_default_decimal'] : 2;

?>

<span class="pull-right">
    <button type="button" id="btn_print_itemizedSales" class="btn btn-default btn-xs">
        <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
    </button>
</span>
<div id="printContainer_itemizedSalesReport">
    <div class="text-center">
        <h3 style="margin-top:2px;"><?php echo $companyInfo['company_name'] ?></h3>
        <h4 style="margin:0px;"><?php echo $companyInfo['company_address1'] . ', ' . $companyInfo['company_city'] ?></h4>
    </div>
    <div style="margin:4px 0px; text-align: center;">
        <?php
        $cash = $this->lang->line('posr_cashier');
        if (isset($cashier) && !empty($cashier)) {
            echo '' . $cash . ': ';/*Cashier*/

            $tmpArray = array();
            foreach ($cashier as $c) {
                $tmpArray[] = $cashierTmp[$c];
            }
            echo join(', ', $tmpArray);
        }
        ?>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        	<span>
        <?php echo $this->lang->line('posr_filtered_date'); ?><!--Filtered Date -->: <strong>
                    <?php
                    $filterFrom = $this->input->post('startdate');
                    $filterTo = $this->input->post('enddate');
                    $from = $this->lang->line('common_from');
                    $to = $this->lang->line('common_to');
                    $today = $this->lang->line('posr_today');
                    if (!empty($filterFrom) && !empty($filterTo)) {
                        echo '  <i>' . $from . '<!--From--> : </i>' . $filterFrom . ' - <i> ' . $to . ': </i>' . $filterTo;/*To*/
                    } else {
                        $curDate = date('d-m-Y');
                        echo $curDate . ' (' . $today . ')';
                    }
                    ?>
                </strong>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <?php echo $this->lang->line('common_date'); ?><!--Date-->: <strong><?php echo date('d/m/Y'); ?></strong>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
            <?php echo $this->lang->line('posr_time'); ?><!--Time-->: <strong><span id="pcCurrentTime"></span></strong>
        </div>
    </div>

    <br>
    <table class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th>#</th>
            <th>Bill No</th>
            <th>Payment Date</th>
            <th>Customer - Telephone</th>
            <th>Bill Amount</th>
            <th>Paid Amount </th>
            <th>Paid By</th>
            <th>Balance</th>
            <th>Dispatched YN</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($collection_detail)) {
            $i = 1;

            $total_billAmount = 0;
            $total_paid = 0;
            $total_balance = 0;
            foreach ($collection_detail as $collection) {
                ?>
                <tr>
                    <td>
                        <?php
                        echo $i;
                        $i++;
                        ?>
                    </td>
                    <td>
                        <a href="#" onclick="viewDrillDown_report(<?php echo $collection['menuSalesID'] ?>)">
                            <?php echo $collection['invoiceCode'] ?>
                        </a>
                    </td>
                    <td><?php echo $collection['paymentDate'] ?> </td>
                    <td><?php echo $collection['customerInfo'] ?> </td>
                    <td class="ar">
                        <?php
                        echo number_format($collection['billAmount'], $d);
                        $total_billAmount += $collection['billAmount'];
                        ?>
                    </td>
                    <td class="ar">
                        <?php
                        echo number_format($collection['amountPaid'], $d);
                        $total_paid += $collection['amountPaid'];
                        ?>
                    </td>
                    <td><?php echo $collection['paidType'] ?> </td>
                    <td class="ar">
                        <?php
                        echo number_format($collection['balance'], $d);
                        $total_balance += $collection['balance'];
                        ?>
                    </td>
                    <td class="alin">
                        <?php
                        if ($collection['DispatchedYN'] == 1) {
                            echo '<i class="fa fa-check text-green" aria-hidden="true"></i> Yes';
                        } else {
                            echo '<i class="fa fa-close text-red" aria-hidden="true"></i> No';
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            <tfooter>
                <tr>
                    <th colspan="4" class="ar"> Total</th>
                    <th class="ar"><?php echo number_format($total_billAmount, $d); ?></th>
                    <th class="ar"><?php echo number_format($total_paid, $d); ?></th>
                    <th>&nbsp;</th>
                    <th class="ar"><?php echo number_format($total_balance, $d); ?></th>
                    <th>&nbsp;</th>
                </tr>
            </tfooter>
            <?php
        }
        ?>

        </tbody>

    </table>
    <hr>
    <div style="margin:4px 0px">
        <?php echo $this->lang->line('posr_report_print'); ?><!-- Report print by--> : <?php echo current_user() ?>
    </div>
</div>
<script>
    $(document).ready(function (e) {
        $("#btn_print_itemizedSales").click(function (e) {
            $.print("#printContainer_itemizedSalesReport");
        });

        var date = new Date,
            hour = date.getHours(),
            minute = date.getMinutes(),
            seconds = date.getSeconds(),
            ampm = hour > 12 ? "PM" : "AM";

        hour = hour % 12;
        hour = hour ? hour : 12; // zero = 12

        minute = minute > 9 ? minute : "0" + minute;
        seconds = seconds > 9 ? seconds : "0" + seconds;
        hour = hour > 9 ? hour : "0" + hour;


        date = hour + ":" + minute + " " + ampm;
        $("#pcCurrentTime").html(date);
    })

    function generateDeliveryPersonPdf() {
        var form = document.getElementById('frm_deliverypersonReport');
        form.target = '_blank';
        form.action = '<?php echo site_url('Pos_restaurant/loadDeliveryPersonReportPdf'); ?>';
        form.submit();
    }

    function viewDrillDown_report(invoiceID) {
        if (invoiceID > 0) {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: "<?php echo site_url('Pos_restaurant/loadPrintTemplate_salesDetailReport'); ?>",
                data: {invoiceID: invoiceID},
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#rpos_print_template').modal('show');
                    $("#pos_modalBody_posPrint_template").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                }
            });
        } else {
            myAlert('e', 'Load the invoice and click again.')
        }
    }

</script>

<div aria-hidden="true" role="dialog" tabindex="2" id="rpos_print_template" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog" style="width: 500px">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_print'); ?><!--Print--> </h4>
            </div>
            <div class="modal-body" id="pos_modalBody_posPrint_template" style="min-height: 400px;">

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-default btn-block btn-sm" data-dismiss="modal">
                    <i class="fa fa-close text-red" aria-hidden="true"></i>
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
