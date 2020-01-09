<html>
<head>
    <title>Invoice Print</title>
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
        .a-right{
            text-align: right !important;
        }
        .f11{
            font-size: 11px !important;
        }

    </style>
</head>

<body onload="/*window.print()*/">

<?php

$outletInfo = get_outletInfo();
$invMaster = $invData[1];
$invItems = $invData[2];
$dPlace = $invMaster['transactionCurrencyDecimalPlaces'];
$companylogo = base_url() . 'images/logo/' . $this->common_data['company_data']['company_logo'];
echo '<table width="100%" style="">
            <tr>
                <td align="center"> <img alt="Logo" style="height: 70px" src="' . $companylogo . '"></td>
            </tr>
            <tr><td align="center" style="font-size: 14px !important; font-weight: 600;padding-bottom:3px;;">INVOICE &nbsp;&nbsp;  فاتورة</td></tr>
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
                <td class="f11"><b>Customer &nbsp;   زبون </b></td>
                <td class="f11"><b>:</b></td>
                <td class="f11">' . $cus . '</td>

                <td class="f11 a-right"><b>Date &nbsp;   تاريخ </b></td>
                <td class="f11"><b>:</b></td>
                <td class="f11 a-right">' . date("Y-m-d h:i:A", strtotime($invMaster['createdDateTime'])) . '</td>
            </tr>

            <tr>
                <td class="f11">&nbsp;</td>
                <td class="f11">&nbsp;</td>
                <td class="f11">&nbsp;</td>

                <td class="f11 a-right"><b>Invoice No &nbsp;   رقم الفاتورة </b></td>
                <td class="f11"><b>:</b></td>
                <td class="f11 a-right">' . $doSysCode_refNo . '</td>
            </tr>



            <tr>
                <td class="f11">&nbsp;</td>
                <td class="f11">&nbsp;</td>
                <td class="f11">&nbsp;</td>
            </tr>


           </table>';
?>

<table id="itemTable" width="100%" style="border: 1px solid black; margin-top: 5px;">
    <tr>
        <th class="borderlefttopnot" style="text-align: left !important; border-left: none;">#</th>
        <th class="borderlefttopnot" style="width: 160px; text-align: center !important;">Description &nbsp;   وصف</th>
        <th class="borderlefttopnot" style="text-align: center !important; border-left: none;">Qty &nbsp;   الكمية </th>
        <th class="borderlefttopnot" style="text-align: center !important;">Price &nbsp;  السعر </th>
        <th class="borderlefttopnot" style="text-align: center !important;">Discount &nbsp;  خصم </th>
        <th class="borderlefttopnot" style="text-align: center !important; border-left: none;">Amount &nbsp;   القيمة </th>
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
        <td style="border-left: none;" class="borderlefttopbottomnot" align="right">' . number_format($item['transactionAmount'], $dPlace) . '</td>
      </tr>';
            $items = $items + 1;
        }
    }

    ?>

    <tr>
        <td class="borderleftnot" colspan="2" rowspan="3" style="border-bottom: none;">Total Items &nbsp; الإجمالي
            - <?php echo $items ?> <br> Created by &nbsp;  بواسطة &nbsp;   : <?php echo $invMaster['repName']; ?></td>
        <td class="borderleftnot" colspan="2"><b>Sub Total &nbsp; الإجمالي </b></td>
        <td class="borderleftnot" colspan="2"
            style="text-align: right;"><?php echo number_format($total_transactionAmountBeforeDiscount, $dPlace) ?></td>
    </tr>
    <tr>
        <td class="borderlefttopnot" colspan="2"><b>Discount &nbsp;  خصم </b></td>
        <td class="borderlefttopnot" colspan="2"
            style="text-align: right;"><?php echo number_format($invMaster['generalDiscountAmount'], $dPlace); ?></td>
    </tr>
    <tr>
        <td class="borderlefttopnot" colspan="2"><b>Grand Total &nbsp;  الصافي </b></td>
        <td class="borderlefttopnot" colspan="2"
            style="text-align: right;"><?php echo number_format($invMaster['netTotal'], $dPlace) ?></td>
    </tr>
    <?php
    if ($invMaster['cashAmount'] != 0) {
        echo '
    <tr>
        <td class="" colspan="2">&nbsp;</td>
        <td class="borderlefttopnot" colspan="2" style="border-left: 1px solid black;"><b>Cash &nbsp;  كاش </b></td>
        <td class="borderlefttopnot" colspan="2" style="text-align: right;">' . number_format($invMaster['cashAmount'], $dPlace) . '</td>
    </tr>';
    }
    if ($invMaster['chequeAmount'] != 0) {
        echo '
    <tr>
    <td class="" colspan="2">&nbsp;</td>
        <td class="borderlefttopnot" colspan="2" style="border-left: 1px solid black;"><b>Cheque &nbsp; التحقق من </b></td>
        <td class="borderlefttopnot" colspan="2" style="text-align: right;">' . number_format($invMaster['chequeAmount'], $dPlace) . '</td>
    </tr>';
    }
    if ($invMaster['cardAmount'] != 0) {
        echo '
    <tr>
    <td class="" colspan="2">&nbsp;</td>
        <td class="borderlefttopnot" colspan="2" style="border-left: 1px solid black;"><b>Card &nbsp;  بطاقة </b></td>
        <td class="borderlefttopnot" colspan="2" style="text-align: right;">' . number_format($invMaster['cardAmount'], $dPlace) . '</td>
    </tr>';
    }
    if ($invMaster['creditNoteAmount'] != 0) {
        echo '
    <tr>
    <td class="" colspan="2">&nbsp;</td>
        <td class="borderlefttopnot" colspan="2" style="border-left: 1px solid black;"><b>Credit Note :   إشعار خصم ' . $returnDet . '</b></td>
        <td class="borderlefttopnot" colspan="2" style="text-align: right;">' . number_format($invMaster['creditNoteAmount'], $dPlace) . '</td>
    </tr>';
    }
    if ($invMaster['creditSalesAmount'] != 0) {
        echo '
    <tr>
    <td class="" colspan="2">&nbsp;</td>
        <td class="borderlefttopnot" colspan="2" style="border-left: 1px solid black;"><b>Credit Sales &nbsp; مبيعات الائتمان </b></td>
        <td class="borderlefttopnot" colspan="2" style="text-align: right;">' . number_format($invMaster['creditSalesAmount'], $dPlace) . '</td>
    </tr>';
    }
    ?>

    <tr>
        <td class="borderlefttopnot" colspan="2">&nbsp;</td>
        <?php
        $Balancetxt = 'Change &nbsp; الباقي ';

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
        <td class="borderlefttopnot" colspan="6"
            align="center"><?php echo $this->common_data['company_data']['company_address1'] ?> <?php echo $this->common_data['company_data']['company_address2'] ?>
            Tel : <?php echo $this->common_data['company_data']['company_phone'] ?> <br>Email
            : <?php echo $this->common_data['company_data']['company_email'] ?>
            <br><b><?php echo $this->common_data['company_data']['companyPrintTagline'] ?></b></td>
    </tr>
    </tbody>
    <table width="100%" style="">
        <br>
        <tr><td align="center" style="font-size: 14px !important; font-weight: 600;">  <?php echo $outletInfo['pos_footNote'] ?></td></tr>
    </table>


</body>
</html>