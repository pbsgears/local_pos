<html>
<head>
    <title>Exchange Note</title>
    <style type="text/css">
        #itemTable th{
            border: 1px dashed #000;
            border-left: none;
            border-right: none;
            text-align: right !important;
            font-size: 13px;
        }

        #itemTable td{ font-size: 18px; }

        #itemBreak{ border-top: 1px dashed #000; }

        #headerTB td{ font-size: 18px}

        /*.lastTD{
            -webkit-transform:scale(1,2); !* Safari and Chrome *!
            -moz-transform:scale(1,2); !* Firefox *!
            -ms-transform:scale(1,2); !* IE 9 *!
            -o-transform:scale(1,2); !* Opera *!
            transform:scale(1,2); !* W3C *!
        }*/

        #thanking-div{
            border: 1px dashed #000;
            border-left: none;
            border-right: none;
            padding: 10px;
            font-size: 12px;
            font-weight: bolder;
        }
    </style>
</head>

<body onload="/*window.print()*/">

<?php
$invMaster = $invData[1];
$invItems = $invData[2];
$dPlace = $invMaster['transactionCurrencyDecimalPlaces'];
echo '<table width="370px" border="0">
            <tr><td align="center" style="font-size: 20px !important; font-weight: 600">'.$this->common_data['company_data']['company_name'].'</td></tr>
            <tr><td align="center" style="font-size: 14px !important">'.$wHouse['warehouseAddress'].'</td></tr>
            <tr><td align="center" style="font-size: 14px !important">'.$wHouse['warehouseTel'].'</td> </tr>
            <tr><td align="center" style="font-size: 20px; font-weight: 600">Exchange Note</td></tr>
            <tr><td align="center" style="font-size: 12px; font-weight: 600">Invoice No : '.$invMaster['invCode'].'</td></tr>
     </table>';

echo '<table id="headerTB" width="370px" border="0">
            <tr>
                <td width="50px">Date</td>
                <td>:</td>
                <td width="100px">'.date( "Y-m-d", strtotime($invMaster['createdDateTime']) ).'</td>
                <td width="50px"></td>
                <td width="50px">Operator</td>
                <td>:</td>
                <td width="100px">'.$invMaster['repName'].'</td>
            </tr>
            <tr>
                <td>Code</td>
                <td>:</td>
                <td>'.$invMaster['documentSystemCode'].'</td>
                <td></td>
                <td>Unit</td>
                <td>:</td>
                <td>'.count($invItems).'</td>
            </tr>
          </table>';
?>

<table id="itemTable" width="370px" border="0">
    <tr>
        <th style="text-align: left !important;">#</th>
        <th style="width: 160px; text-align: left !important;">Item</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Amount</th>
    </tr>

    <?php
    foreach($invItems as $key=>$item){
        echo '<tr>
        <td>'.($key+1).'</td>
        <td colspan="3">'.$item['itemSystemCode'].'&nbsp;&nbsp;'.$item['itemDescription'].'</td>
        <td></td>
      </tr>
      <tr>
        <td colspan="2"></td>
        <td align="right">'.number_format($item['price'], $dPlace).'</td>
        <td align="right">'.$item['qty'].'</td>
        <td align="right">'.number_format($item['transactionAmount'], $dPlace).'</td>
      </tr>';
    }
    ?>
    <tr>
        <td colspan="5" id="itemBreak">&nbsp;</td>
    </tr>
    <?php
    if($invMaster['discountAmount'] > 0){
        ?>
        <tr>
            <td colspan="4" class="lastTD">SUB TOTAL</td>
            <td class="lastTD" align="right"><?php echo number_format($invMaster['subTotal'], $dPlace) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="lastTD">DISCOUNT TOTAL</td>
            <td align="right" class="lastTD" style="/*border-bottom: 1px solid #000;*/"><?php echo number_format($invMaster['discountAmount'], $dPlace); ?></td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="4" class="lastTD">NET TOTAL</td>
        <td align="right" class="lastTD"><?php echo number_format($invMaster['netTotal'], $dPlace) ?></td>
    </tr>
</table>


</body>
</html>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-11-15
 * Time: 12:35 PM
 */