<div class="numPlateContainer ">
    <?php
    $result = get_companyInfo();
    $currencyID = $result['company_default_currencyID'];
    $currencyCode = $result['company_default_currency'];

    $currencies = getCurrencyNotes($currencyID);
    ?>
    <!--Currency Code: <strong><?php /*echo $currencyCode */ ?></strong>-->
    <div class="row">
        <!--<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <div class="btn-toolbar" role="toolbar">

                <input type="hidden" id="tmpQtyValue" value="0">
                <?php
        /*

                       if (!empty($currencies)) {
                            foreach ($currencies as $currency) {
                                if ($currency['currencyCode'] == 'LKR') {
                                    if ($currency['value'] > 90) {
                                        echo '<button type="button" onclick="updateNoteValue(this)" class="currencyNoteBtn">' . $currency['value'] . '</button>';
                                    }
                                } else {
                                    echo '<button type="button" onclick="updateNoteValue(this)" class="currencyNoteBtn">' . $currency['value'] . '</button>';
                                }
                            }
                        }*/
        ?>


            </div>
        </div>-->
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

            <div>
                <span class="button-wrap-style2">
                    <button type="button" onclick="updateAdQty(this);"
                            class="numberPlateBtn button button-raised button-box button-jumbo">7
                    </button>
                </span>

                <span class="button-wrap-style2">
                <button type="button" onclick="updateAdQty(this);"
                        class="numberPlateBtn button button-raised button-box button-jumbo">8
                </button></span>

                <span class="button-wrap-style2">
                <button type="button" onclick="updateAdQty(this);"
                        class="numberPlateBtn button button-raised button-box button-jumbo">9
                </button></span>

            </div>
            <div>

                <span class="button-wrap-style2">
                    <button type="button" onclick="updateAdQty(this);"
                            class="numberPlateBtn button button-raised button-box button-jumbo">4
                    </button>
                </span>
                <span class="button-wrap-style2">
                    <button type="button" onclick="updateAdQty(this);"
                            class="numberPlateBtn button button-raised button-box button-jumbo">5
                    </button>
                </span>
                <span class="button-wrap-style2">
                    <button type="button" onclick="updateAdQty(this);"
                            class="numberPlateBtn button button-raised button-box button-jumbo">6
                    </button>
                </span>


            </div>
            <div>
                 <span class="button-wrap-style2">
                    <button type="button" onclick="updateAdQty(this);"
                            class="numberPlateBtn button button-raised button-box button-jumbo">1
                    </button>
                </span>
                <span class="button-wrap-style2">
                    <button type="button" onclick="updateAdQty(this);"
                            class="numberPlateBtn button button-raised button-box button-jumbo">2
                    </button>
                </span>
                <span class="button-wrap-style2">
                    <button type="button" onclick="updateAdQty(this);"
                            class="numberPlateBtn button button-raised button-box button-jumbo">3
                    </button>
                </span>


            </div>
            <div>
                 <span class="button-wrap-style2">
                    <button type="button" onclick="updateAdQty(this);"
                            class="numberPlateBtn button button-raised button-box button-jumbo">0
                    </button>
                </span>
                <span class="button-wrap-style2">
                    <button type="button" onclick="updateAdQty(this);"
                            class="numberPlateBtn button button-raised button-box button-jumbo">.
                    </button>
                </span>
                <span class="button-wrap-style2">
                    <button type="button" onclick="updateAdQty(this);"
                            class="numberPlateBtn button button-raised button-box button-jumbo">C
                    </button>
                </span>
            </div>

        </div>
    </div>

</div>
<span class="hide" id="temp_number"></span>
<script>
    var lastFocus = '';
    var tmpTotalAmount = '';
    $(document).ready(function (e) {
        $(document).click(function (e) {
            var inputFocus = $('input').is(':focus');
            if (inputFocus) {
                lastFocus = document.activeElement.id;
                tmpTotalAmount = $("#" + lastFocus).val()
            } else {
                lastFocus = '';
                tmpTotalAmount = '';
            }
        })
    })
    function updateAdQty(tmpValue) {

        var cPaidAmount = $("#qtyAdj").val();
        var tmpAmount = $(tmpValue).text();
        /**input value*/
        var tmpAmount_txt = $("#temp_number").html();
        if (parseFloat(tmpAmount) >= 0 || $.trim(tmpAmount) == '.') {
            var updateVal = cPaidAmount + parseFloat(tmpAmount);
            var tmpAmount_output = $.trim(tmpAmount_txt) + $.trim(tmpAmount);
            if ($.trim(tmpAmount) == '.') {

            }


            if (lastFocus != '') {
                tmpTotalAmount = $.trim(tmpTotalAmount) + $.trim(tmpAmount);
                $('#' + lastFocus).val(tmpTotalAmount);
                $('#' + lastFocus).focus();
            } else {
                $("#qtyAdj").val(parseFloat(tmpAmount_output));
                $("#temp_number").html(tmpAmount_output)
                //$("#qtyAdj").val(parseFloat(updateVal));
            }


        } else if ($.trim(tmpAmount) == 'C') {
            if (lastFocus != '') {
                $('#' + lastFocus).val('');
                $('#' + lastFocus).focus();
            } else {
                $("#qtyAdj").val('');
                $("#temp_number").html('');
            }
        }
    }
</script>