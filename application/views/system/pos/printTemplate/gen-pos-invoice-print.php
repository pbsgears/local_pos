<html>
<head>
    <title>Invoice Print</title>
    <!--<link rel="stylesheet" href="<?php /*echo base_url('plugins/bootstrap/css/print_style.css'); */ ?>" />-->
    <style type="text/css">
        #itemTable th {
            text-align: right !important;
            font-size: 13px;
        }

        #itemTable td {
            font-size: 18px;
        }

        #itemBreak {
            border-top: 1px dashed #000;
        }

        #headerTB td {
            font-size: 18px
        }

        #thanking-div {
            border: 1px dashed #000;
            border-left: none;
            border-right: none;
            padding: 10px;
            font-size: 12px;
            font-weight: bolder;
        }

        .borderlefttopbottomnot {
            border: 1px solid black;
            border-left: none;
            border-top: none;
            border-bottom: none;
            padding-left: 2px;
        }
        .printtagline {
            border-left: none;
            border-top: none;
            border-bottom: none;
            padding-left: 2px;
        }


        .borderlefttopnot {
            border: 1px solid black;
            border-left: none;
            border-top: none;
            padding-left: 2px;
        }

        .borderleftnot {
            border: 1px solid black;
            border-left: none;
            padding-left: 2px;
        }
    </style>
</head>

<body onload="/*window.print()*/">

<?php
$invMaster = $invData[1];
$invItems = $invData[2];
$outletInfo = get_outletInfo();
$dPlace = $invMaster['transactionCurrencyDecimalPlaces'];
$companylogo = base_url() . 'images/logo/' . $this->common_data['company_data']['company_logo'];
echo '<table width="100%" style="">
            <tr>
                <td align="center"> <img alt="Logo" style="height: 70px" src="' . $companylogo . '"></td>
            </tr>
            <tr><td align="center" style="font-size: 14px !important; font-weight: 600;padding-bottom:3px;;">INVOICE</td></tr>
           </table>';
$trype = 'CASH';
if ($invMaster['cashAmount'] != 0) {
    $trype = 'Cash';
}
if ($invMaster['chequeAmount'] != 0) {
    $trype = 'Cheque';
}
if ($invMaster['cardAmount'] != 0) {
    $trype = 'Card';
}
if ($invMaster['creditNoteAmount'] != 0) {
    $trype = 'Credit Note';
}
$cus = '';
if ($invMaster['customerID'] > 0) {
    $cusid = $invMaster['customerID'];
    $custdetails = $this->db->query("SELECT customerTelephone FROM srp_erp_customermaster WHERE  customerAutoID=$cusid")->row_array();
    $cus = $invMaster['cusName'] . '<br>' . $custdetails['customerTelephone'];

} else {
    $cus = 'Cash';
}
echo '<table width="100%" style="">
            <tr>
                <td style=" font-size: 11px !important"><b>Customer </b></td>
                <td style=" font-size: 11px !important"><b>:</b></td>
                <td style=" font-size: 11px !important">' . $cus . '</td>

                <td style=" font-size: 11px !important"><b>Date </b></td>
                <td style=" font-size: 11px !important"><b>:</b></td>
                <td style=" font-size: 11px !important">' . date("Y-m-d h:i:sa", strtotime($invMaster['createdDateTime'])) . '</td>
            </tr>

            <tr>
                <td style=" font-size: 11px !important">&nbsp;</td>
                <td style=" font-size: 11px !important">&nbsp;</td>
                <td style=" font-size: 11px !important">&nbsp;</td>

                <td style=" font-size: 11px !important"><b>Invoice No </b></td>
                <td style=" font-size: 11px !important"><b>:</b></td>
                <td style=" font-size: 11px !important">' . $invData[1]['invoiceCode']. '</td>
            </tr>



            <tr>
                <td style="  font-size: 11px !important">&nbsp;</td>
                <td style="  font-size: 11px !important">&nbsp;</td>
                <td style="  font-size: 11px !important">&nbsp;</td>
            </tr>


           </table>';
?>

<table id="itemTable" width="100%" style="border: 1px solid black; margin-top: 5px;">
    <tr>
        <th class="borderlefttopnot" style="text-align: left !important; border-left: none;">#</th>
        <th class="borderlefttopnot" style="width: 160px; text-align: center !important;">Description</th>
        <th class="borderlefttopnot" style="text-align: center !important; border-left: none;">Qty</th>
        <th class="borderlefttopnot" style="text-align: center !important;">Price</th>
        <th class="borderlefttopnot" style="text-align: center !important;">Discount</th>
        <th class="borderlefttopnot" style="text-align: center !important; border-left: none;">Amount</th>
    </tr>
    <tbody>
    <?php
    $items = 0;
    $total_transactionAmountBeforeDiscount = 0;
    if (!empty($invItems)) {
        foreach ($invItems as $key => $item) {
            $total_transactionAmountBeforeDiscount += $item['transactionAmountBeforeDiscount'];

            $umo = $item['unitOfMeasure'];
            $per = number_format(0, $dPlace);
            if ($item['discountPer'] > 0) {
                $per = number_format(($item['discountAmount'] ) * $item['qty'], $dPlace);
            }

            echo '
      <tr>
        <td style="border-left: none;" class="borderlefttopbottomnot">' . ($key + 1) . '</td>
        <td class="borderlefttopbottomnot">' . $item['itemDescription'] . '</td>
        <td class="borderlefttopbottomnot" align="right">' . $item['qty'] . '</td>
        <td class="borderlefttopbottomnot" align="right">' . number_format($item['price'], $dPlace) . '</td>
        <td class="borderlefttopbottomnot" align="right">' . $per . '</td>
        <td style="border-left: none;" class="borderlefttopbottomnot" align="right">' . number_format($item['transactionAmountBeforeDiscount'], $dPlace) . '</td>
      </tr>';
            $items = $items + 1;
        }
    }

    ?>


    <tr>
        <td class="borderleftnot" colspan="2" rowspan="3" style="border-bottom: none;">Total Items
            - <?php echo $items ?> <br> Created by : <?php echo $invMaster['repName']; ?></td>
        <td class="borderleftnot" colspan="2"><b>Sub Total</b></td>
        <td class="borderleftnot" colspan="2"
            style="text-align: right;"><?php echo number_format($total_transactionAmountBeforeDiscount, $dPlace) ?></td>
    </tr>
    <tr>
        <td class="borderlefttopnot" colspan="2"><b>Discount</b></td>
        <td class="borderlefttopnot" colspan="2"
            style="text-align: right;"><?php echo number_format($invMaster['generalDiscountAmount'], $dPlace); ?></td>
    </tr>
    <tr>
        <td class="borderlefttopnot" colspan="2"><b>Grand Total</b></td>
        <td class="borderlefttopnot" colspan="2"
            style="text-align: right;"><?php echo number_format($invMaster['netTotal'], $dPlace) ?></td>
    </tr>
    <?php
    if ($invMaster['cashAmount'] != 0) {
        echo '
    <tr>
        <td class="" colspan="2">&nbsp;</td>
        <td class="borderlefttopnot" colspan="2" style="border-left: 1px solid black;"><b>Cash</b></td>
        <td class="borderlefttopnot" colspan="2" style="text-align: right;">' . number_format($invMaster['cashAmount'], $dPlace) . '</td>
    </tr>';
    }
    if ($invMaster['chequeAmount'] != 0) {
        echo '
    <tr>
    <td class="" colspan="2">&nbsp;</td>
        <td class="borderlefttopnot" colspan="2" style="border-left: 1px solid black;"><b>Cheque</b></td>
        <td class="borderlefttopnot" colspan="2" style="text-align: right;">' . number_format($invMaster['chequeAmount'], $dPlace) . '</td>
    </tr>';
    }
    if ($invMaster['cardAmount'] != 0) {
        echo '
    <tr>
    <td class="" colspan="2">&nbsp;</td>
        <td class="borderlefttopnot" colspan="2" style="border-left: 1px solid black;"><b>Card</b></td>
        <td class="borderlefttopnot" colspan="2" style="text-align: right;">' . number_format($invMaster['cardAmount'], $dPlace) . '</td>
    </tr>';
    }
    if ($invMaster['creditNoteAmount'] != 0) {
        echo '
    <tr>
    <td class="" colspan="2">&nbsp;</td>
        <td class="borderlefttopnot" colspan="2" style="border-left: 1px solid black;"><b>Credit Note : ' . $returnDet . '</b></td>
        <td class="borderlefttopnot" colspan="2" style="text-align: right;">' . number_format($invMaster['creditNoteAmount'], $dPlace) . '</td>
    </tr>';
    }
    if ($invMaster['creditSalesAmount'] != 0) {
        echo '
    <tr>
    <td class="" colspan="2">&nbsp;</td>
        <td class="borderlefttopnot" colspan="2" style="border-left: 1px solid black;"><b>Credit Sales </b></td>
        <td class="borderlefttopnot" colspan="2" style="text-align: right;">' . number_format($invMaster['creditSalesAmount'], $dPlace) . '</td>
    </tr>';
    }
    ?>

    <tr>
        <td class="borderlefttopnot" colspan="2">&nbsp;</td>
        <?php
        $Balancetxt = 'Change';
        /*if ($invMaster['isCreditSales'] == 1) {
            $Balancetxt='Balance';
        }*/
        ?>
        <td colspan="2" style="padding-left: 2px;"><b><?php echo $Balancetxt ?></b></td>
        <?php
        $amn = abs($invMaster['netTotal'] - $invMaster['paidAmount']);
        ?>
        <td colspan="2" style="text-align: right;"><?php echo number_format($amn, $dPlace) ?></td>

    </tr>
    <tr>
        <td class="borderlefttopnot" colspan="6" style="height: 50px;"></td>
    </tr>
    <tr>
        <td class="borderlefttopnot" colspan="6">
            <div style="text-align: center">
                <?php echo $this->common_data['company_data']['company_address1'] ?>
                <?php echo $this->common_data['company_data']['company_address2'] ?>
                Tel : <?php echo $this->common_data['company_data']['company_phone'] ?> <br>
                Email : <?php echo $this->common_data['company_data']['company_email'] ?><br>
                <b><?php echo $this->common_data['company_data']['companyPrintTagline'] ?></b>
            </div>

        </td>
    </tr>
    </tbody>
    <table width="100%" style="">
        <br>
        <tr><td align="center" style="font-size: 14px !important; font-weight: 600;">  <?php echo $outletInfo['pos_footNote'] ?></td></tr>
    </table>


</body>
</html>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-11-14
 * Time: 3:32 PM
 */