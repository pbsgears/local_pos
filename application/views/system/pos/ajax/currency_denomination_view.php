<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
/** ================================
 * -- File Name : currency_denomination_view
 * -- Project Name : SME
 * -- Module Name : POS
 * -- Author : Nasik Ahamed
 * -- Create date : unknown
 * -- Description : currency denomination
 *
 * --REVISION HISTORY
 * Date: 13 - Dec 2016 By: Mohamed Shafri: worked on the double entry work in restaurant pos.
 * Date: 20 - Oct 2017 By: Mohamed Shafri: cash payment moved to srp_erp_pos_menusalespayment table and load the current cash from said table.
 *
 */
?>
<style type="text/css">
    #denominationTB td {
        padding: 3px;
    }

    #denominationTB td .form-control {
        height: 20px;
        width: 70px;
        font-size: 11px;
        padding: 3px 5px;
    }

    .tillBoxInputs {
        padding: 3px 5px;
        height: 25px;
        font-size: 12px;
    }
</style>
<link rel="stylesheet" href="<?php echo base_url('plugins/numPadmaster/jquery.numpad.css') ?>">
<script src="<?php echo base_url('plugins/numPadmaster/jquery.numpad.js') ?>" type="text/javascript"></script>
<?php
if (!empty($session_data)) {
    $trillCounter = $session_data['counterID'];
} else {
    $trillCounter = null;
}

$notesArray = array();
$coinArray = array();
$i = 0;
$j = 0;
foreach ($denomination as $dem) {
    if ($dem['isNote'] == 1) {
        $notesArray[$i]['amount'] = $dem['amount'];
        $notesArray[$i]['caption'] = $dem['caption'];
        $notesArray[$i]['value'] = $dem['value'];
        $i++;
    } else {
        $coinArray[$j]['amount'] = $dem['amount'];
        $coinArray[$j]['caption'] = $dem['caption'];
        $coinArray[$j]['value'] = $dem['value'];
        $j++;
    }
}

$notesCount = count($notesArray);
$coinCount = count($coinArray);
$m = ($notesCount > $coinCount) ? $notesCount : $coinCount;
$x = 0;



if (!empty($counters)) {
    ?>
    <span class="hide">
        <?php echo $this->lang->line('posr_opening_balance'); ?><!--Opening Balance--> : <?php
        $counterData = get_counterData();
        $d = $counterData['transactionCurrencyDecimalPlaces'];
        $c = $counterData['transactionCurrency'];
        $b = $counterData['startingBalance_transaction'];
        $openingBalance = number_format($b, $d);
        $netSales = 0;
        $discount = 0;
        $ExpectedCounterBalance = 0;
        echo $openingBalance . ' ' . $c;


        if (isset($session_data) && !empty($session_data)) {

            if (isset($isRestaurant) && $isRestaurant) {

                $cashSales_tmp = get_totalCashSales($session_data);
                $netCashSales = !empty($cashSales_tmp) ? $cashSales_tmp['NetCashSales'] : 0;

                $cashSales = number_format($netCashSales, $d);
                $tot_cash_sales = $this->lang->line('posr_total_cash_sales');
                $tot_discount = $this->lang->line('posr_total_discount');
                $exp_coun_balance = $this->lang->line('posr_expected_counter_balance');

                echo '<br/>' . $tot_cash_sales . '<!--Total Cash Sales--> : ' . number_format($netCashSales, $d) . " " . $c;
                //echo '<br/>' . $tot_discount . '<!--Total Discount -->: ' . number_format($discount, $d) . " " . $c;
                $ExpectedCounterBalance = ($netCashSales + $b + $cardCollection) - $discount;
                echo '<br/>' . $exp_coun_balance . '<!--Expected Counter Balance--> : <strong>' . number_format($ExpectedCounterBalance, $d) . "</strong> " . $c;
                echo '<br/> Card Collection : <strong>' . number_format($cardCollection, $d) . "</strong> " . $c;
            }
        }
        ?>
    </span>
    <!--<hr style="margin: 7px 0px;">-->

    <table style="width: 95% !important;" id="denominationTB">
        <tbody>
        <tr>
            <td colspan="3" align="center" style="font-weight:bolder"><span
                    style="bolder;border-bottom: 1px solid; font-size: 16px !important"><?php echo $this->lang->line('common_notes'); ?><!--Notes--></span>
            </td>
            <td style="width:30px;">&nbsp;</td>
            <td colspan="3" align="center" style="font-weight:bolder"><span
                    style="bolder;border-bottom: 1px solid; font-size: 16px !important"><?php echo $this->lang->line('posr_coins'); ?><!--Coins--></span>
            </td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
            <td style="/*width:30px; border-right:1px solid*/">&nbsp;</td>
            <td colspan="3">&nbsp;</td>
        </tr>
        <?php
        while ($m > $x) {
            echo '<tr>';
            if (array_key_exists($x, $notesArray)) {
                echo '<td align="right"><label for="tillTxtN_' . $x . '">' . $notesArray[$x]['amount'] . ' ' . $notesArray[$x]['caption'] . '</label></td>
                          <td align="center" width="30px"><label> X </label></td>
                          <td style="width: 80px;"><input type="text" name="tillTxtN_' . $x . '" id="tillTxtN_' . $x . '" data-id="totN_' . $x . '"
                              class="form-control number tillCalculate"  data-value="' . $notesArray[$x]['value'] . '" /></td>
                          <td style="">= <span class="pull-right inlineTot" id="totN_' . $x . '">' . number_format(0, $dPlace) . '</span></td>
                          <td style="/*border-right:1px solid*/">&nbsp;</td>';
            } else {
                echo '<td colspan="5" style="/*border-right:1px solid*/">&nbsp;</td>';
            }

            if (array_key_exists($x, $coinArray)) {
                echo '<td align="right"><label for="tillTxtC_' . $x . '">' . $coinArray[$x]['amount'] . ' ' . $coinArray[$x]['caption'] . '</label></td>
                          <td align="center" width="30px"><label> X </label>
                          <td style="width: 80px;"><input type="text" name="tillTxtC_' . $x . '" id="tillTxtC_' . $x . '" data-id="totC_' . $x . '"
                            class="form-control number tillCalculate" data-value="' . $coinArray[$x]['value'] . '"/></td>
                          <td>= <span class="pull-right inlineTot" id="totC_' . $x . '">' . number_format(0, $dPlace) . '</span></td> ';
            } else {
                echo '<td colspan="4">&nbsp;</td>';
            }

            echo '</tr>';
            $x++;
        }
        ?>

        <tr style="/*border-bottom: 1px solid; border-top: 1px solid;*/">
            <td colspan="2" align="right" style="/*padding: 15px*/">
                <?php echo $this->lang->line('posr_other'); ?><!--Other--></td>
            <td colspan="7"><input type="text" id="otherAmount" class="form-control number"
                                   onkeyup="till_totalCalculate()"/></td>
        </tr>
        <!--<tr><td colspan="9" style="border-top: 1px solid">&nbsp;</td></tr>-->
        </tbody>Notes
    </table>
    <hr style="margin:10px !important;">
    <div calss="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="till_total" class="control-label col-xs-3">
                    <?php echo $this->lang->line('common_total'); ?><!--Total--></label>
                <div class="col-xs-9">
                    <input type="text" class="form-control number tillBoxInputs" id="till_total" readonly>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group" id="counterDiv" style="/*display: none*/">
                <label for="user_counter" class="control-label col-xs-3">
                    <?php echo $this->lang->line('posr_counter'); ?><!--Counter--></label>
                <div class="col-xs-9">
                    <select name="counterID" id="counterID" class="form-control tillBoxInputs">
                        <option value=""></option>
                        <?php
                        foreach ($counters as $counter) {
                            $sel = '';
                            if ($trillCounter == $counter['counterID']) {
                                $sel = "selected";
                            }

                            echo '<option value="' . $counter['counterID'] . '" ' . $sel . '>
                                ' . $counter['counterCode'] . ' - ' . $counter['counterName'] . '
                             </option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <?php
} else {
    $error = '';
    $myOutletID = get_outletID();
    //$q = $this->db->last_query();
    if (empty(trim($myOutletID))) {
        $error .= '<strong>This user is not assigned for any outlet.</strong><br/> help:  You Can setup this under Master  <i class="fa fa-arrow-right"></i> Outlet Users  ';
    } else {

        $error .= '<strong>' . $this->lang->line('posr_counter_not_configured') . '</strong>';
        $error .= '<br>' . $this->lang->line('posr_please_create_counter_and_come_back');
        $Logout = site_url('Login/logout');
        $error .= '<br><br><a href="'.$Logout.'" class="btn btn-danger"> <i class="fa fa-power-off"></i> Logout </a>';
    }
    ?>
    <script>
        $("#tillSave_Btn").hide();
        $(".tillModal_close").show();
    </script>
    <div class="alert alert-warning">
        <?php echo $error; ?>
    </div>
    <?php
}
?>

<script type="text/javascript">
    $(".tillModal_close").show();
    $('.tillCalculate').on('keyup', function () {
        var thisID = $(this).attr('id');
        var sum = 0;

        $('.tillCalculate').each(function () {
            var count = getNumberAndValidate($(this).val());
            var thisValue = getNumberAndValidate($(this).attr('data-value'));

            if ($(this).attr('id') == thisID) {
                var inlineID = $(this).attr('data-id');
                var inlineTot = ( count * thisValue );
                $('#' + inlineID).text(commaSeparateNumber(inlineTot, dPlaces));
            }

            sum += ( count * thisValue );
        });
        var otherAmount = getNumberAndValidate($('#otherAmount').val());
        sum += otherAmount;
        $('#till_total').val(commaSeparateNumber(sum, dPlaces));
    });

    function shift_create() {
        var till_total = $('#till_total').val();
        var counterID = $('#counterID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'startingBalance': till_total, 'counterID': counterID},
            url: "<?php echo site_url('Pos/shift_create'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 'e') {
                    myAlert('e', data[1]);
                }
                else if (data[0] == 's') {
                    <?php
                    if (isset($isRestaurant) && $isRestaurant) {
                        if(isset($isRestaurant_mobile) && $isRestaurant_mobile){
                            echo 'window.location = "' . site_url('m-pos') . '"';
                        }else {
                            echo 'window.location = "' . site_url('restaurant/') . '"';
                        }
                    } else {
                        echo 'window.location = "' . site_url('Pos/') . '"';
                    }
                    ?>
                }
                else {
                    myAlert('e', 'Something went wrong, Please contact support team');
                }
            }, error: function () {
                stopLoad();
                myAlert('e', 'Error In Day Starting Process.')
            }
        });
    }

    function shift_close() {
        var till_total = $('#till_total').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                startingBalance: till_total,
                code: '<?php echo isset($code) && !empty($code) ? $code : 0; ?>',
                cashSales: '<?php echo isset($netCashSales) && $netCashSales > 0 ? $netCashSales : 0?>',
                closingCashBalance: '<?php echo isset($ExpectedCounterBalance) && $ExpectedCounterBalance > 0 ? $ExpectedCounterBalance : 0?>',
                cardCollection: '<?php echo isset($cardCollection) && $cardCollection > 0 ? $cardCollection : 0?>'
            },
            url: "<?php echo site_url('Pos/shift_close'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if (data[0] == 's') {
                    $('#till_modal').modal('hide');
                    if (data['code'] == 0) {
                        $('#testModal').modal({backdrop: 'static'});
                        var sec = 3;
                        var counter = document.getElementById('countDown');
                        counter.innerText = sec;
                        i = setInterval(function () {
                            --sec;
                            if (sec === 1) {
                                clearInterval(i);
                                window.location = "<?php echo site_url(''); ?>";
                            }
                            counter.innerText = sec;
                        }, 1000);
                    } else if (data['code'] == 1) {

                        /** Restaurant POS */
                        $('#testModal').modal({backdrop: 'static'});
                        var sec = 3;
                        var counter = document.getElementById('countDown');
                        counter.innerText = sec;
                        i = setInterval(function () {
                            --sec;
                            if (sec === 1) {
                                clearInterval(i);
                                window.location = "<?php echo site_url(''); ?>";
                            }
                            counter.innerText = sec;
                        }, 1000);

                        console.log(data['counterData']);
                    }

                }
                else {
                    myAlert('e', data[1]);
                }

            }, error: function () {
                stopLoad();
                myAlert('e', 'Error in shift close.')
            }
        });
    }

    function till_totalCalculate() {
        $('.tillCalculate').keyup();
    }
</script>
