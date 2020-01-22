<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('plugins/numPadmaster/jquery.numpad.css') ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/pos/pos.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/pos/pos-general.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/buttons/button.css'); ?>">
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$bank_card = posPaymentConfig_data('Y'); //load_bank_with_card();
$tr_currency = $this->common_data['company_data']['company_default_currency'];
$this->load->view('include/header', $title);
$this->load->view('include/top-gpos');

$currncy_arr = all_currency_new_drop();
$country = load_country_drop();
$customerCategory = party_category(1);
$gl_code_arr = supplier_gl_drop();
$country_arr = array('' => 'Select Country');
$taxGroup_arr = customer_tax_groupMaster();
$dPlace = get_company_currency_decimal();
if (isset($country)) {
    foreach ($country as $row) {
        $country_arr[trim($row['countryID'])] = trim($row['CountryDes']);
    }
}
?>

<div class="row"></div>

<div id="posHeader_1" style="position: fixed; width: 100%; z-index: 10">
    <div class="row" id="displayDet" style="background-color: #0581B8; margin-top: 50px">
        <div class="col-12" style="margin-top: 3px;color: #eff7ff !important; padding-left: 1%">

            <div class="col-md-2 ">
                <div class="form-group" style="margin-bottom: 2px">
                    <label for="" class="cols-sm-2"><i class="fa fa-user iconSize" aria-hidden="true"></i> Cashier :
                    </label>
                    <span class=""><?php echo ucwords($this->session->EmpShortCode); ?></span>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group" style="margin-bottom: 2px">
                    <label for="" class="cols-sm-2"><i class="fa fa-user-secret iconSize" aria-hidden="true"></i>
                        Customer : </label>
                    <span class="customerSpan">Cash</span>
                </div>
            </div>

            <div class="col-md-2 ">
                <div class="form-group" style="margin-bottom: 2px">
                    <label for="" class="cols-sm-2 "><i class="fa fa-bars iconSize" aria-hidden="true"></i> No of Items
                        : </label>
                    <span class="itemCount">0</span>
                </div>
            </div>

            <div class="col-md-3 pull-right" id="refNo_masterDiv">
                <div class="form-group pull-right" id="refNo_Div" style="margin-bottom: 2px; padding-right: 10%">
                    <label for="" class="cols-sm-3"><i class="fa fa-slack iconSize" aria-hidden="true"></i> Invoice No :
                    </label>
                    <span class="" id="doSysCode_refNo" style=""><?php echo $refNo; ?></span>
                </div>
            </div>

            <div class="col-md-2 pull-right" id="currency_masterDiv">
                <div class="form-group pull-right" id="currency_Div" style="margin-bottom: 2px; padding-right: 10%">
                    <label for="" class="cols-sm-2 "><i class="fa fa-money iconSize" aria-hidden="true"></i> Currency :
                    </label>
                    <span class="trCurrencySpan"><?php echo $tr_currency; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="posHeader_2" style="display: none;">
    <table id="posHeader_2_TB">
        <tr>
            <td width="90px">Cashier</td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td style="padding-left: 0px !important;"><?php echo ucwords($this->session->loginusername); ?></td>
            <td width="90px">Customer</td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td style="padding-left: 0px !important;"><span class="customerSpan">Cash</span></td>
        </tr>
        <tr>
            <td>No of Items</td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td class="itemCount" style="padding-left: 0px !important;">0</td>
            <td>Sales Mode</td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td style="padding-left: 0px !important;">Retail</td>
        </tr>
        <tr>
            <td>Ref No</td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td style="padding-left: 0px !important;"><?php echo $refNo; ?></td>
            <td>Currency</td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td style="padding-left: 0px !important;"><span
                    class="trCurrencySpan"><?php echo $tr_currency; ?></span></td>
        </tr>
    </table>
</div>
<div id="form_div" style="padding: 1%;/*height: 70%*/; margin-top: 80px">
    <div class="hide" style="margin-bottom: -10px">
        <label class="checkbox-inline no_indent">
            <input type="checkbox" id="enable_BC" value="option1" checked="checked"> <strong>Enable BC</strong>
        </label>
    </div>
    <div class="row">
        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
            <div class="cols-12">
                <div class="m-b-lg" style="padding-left: 15px">
                    <form class="form-horizontal" role="form" id="pos_form" autocomplete="off">
                        <div class="row" style="margin-top: 1%">
                            <div class="col-md-2">
                                <div class="form-group cols-sm-3 item-search-container">
                                    <label for="itemSearch" class="cols-sm-4"> Item </label>

                                    <div class="input-group">
                                        <input type="text" name="itemSearch" id="itemSearch" placeholder="shortcut [F9]"
                                               class="form-control">
                                        <span class="input-group-addon" onclick="searchItem_modal();"
                                              style="cursor: pointer;"><i class="fa fa-search"></i> [F4]</span>
                                    </div>

                                    <input type="hidden" id="itemAutoID" name="itemAutoID">
                                    <input type="hidden" id="itemDescription" name="itemDescription">
                                    <input type="hidden" id="currentStock" name="currentStock">
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="itemDescription2" class="cols-sm-2">Description</label>
                                    <input type="text" name="" id="itemDescription2" disabled="disabled"
                                           class="form-control  formInput" style="width: 85%;">
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="currentStockDsp" class="cols-sm-2">Stock</label>
                                    <input type="text" name="" id="currentStockDsp" disabled="disabled"
                                           class="form-control number formInput" style="width: 85%;">
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="itemUOM" class="cols-sm-1"> UOM </label>
                                    <select name="itemUOM" disabled="disabled" id="itemUOM"
                                            class="form-control  formInput"
                                            style="width: 85%;">
                                        <option></option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group cols-sm-3">
                                    <label for="itemQty" class="cols-sm-4"> Qty </label>
                                    <input type="text" name="itemQty" id="itemQty"
                                           class="form-control  formInput number"
                                           style="width: 85%;">
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group cols-sm-3">
                                    <label for="salesPrice" class="cols-sm-4"> Sales Price </label>
                                    <input type="text" name="salesPrice" id="salesPrice" placeholder="Ctrl+E"
                                           class="form-control  formInput number"
                                           style="width: 85%">
                                </div>
                            </div>


                            <div class="col-md-1">
                                <div class="form-group cols-sm-3">
                                    <label for="disPer" class="cols-sm-4"> Disc% </label>
                                    <input type="text" name="disPer" id="disPer" placeholder="Ctrl+Q"
                                           class="form-control  formInput number"
                                           style="width: 85%;">
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group cols-sm-3">
                                    <label for="disAmount" class="cols-sm-4"> Discount </label>
                                    <input type="text" name="disAmount" placeholder="Ctrl+D" id="disAmount"
                                           class="form-control  formInput number"
                                           style="width: 85%;">
                                </div>
                            </div>


                            <div class="col-md-1" style="margin-top:25px">
                                <div class="form-group cols-sm-2">
                                    <label class="cols-sm-4"> &nbsp;</label>
                                    <button type="submit"
                                            class="button button-primary button-box button-raised button-longshadow button-pill"
                                            id="pos-add-btn"
                                            style="height:28px; width:28px;"><i
                                            class="fa fa-plus"></i></button>
                                    <input type="hidden" id="item-image-hidden"/>
                                    <input type="hidden" id="is-edit" value=""/>
                                </div>
                            </div>


                        </div>
                    </form>
                </div>
            </div>
            <form id="my_form" class="form_pos_receipt" onsubmit="return false">
                <div class="cols-12">
                    <div class="m-b-lg fixHeader_Div" id="itemDisplayTB_div" style="height:290px;">
                        <table class="table table-bordered table-condensed table-row-select" id="itemDisplayTB"
                               style="">
                            <thead>
                            <tr class="header_tr" style="background-color: #75BDD8 !important;;">
                                <th></th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>UOM</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Amount</th>
                                <th>Disc%</th>
                                <th>Discount</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody id="tbody_itemList">
                            </tbody>
                        </table>
                        <input type="hidden" name="_cashAmount" id="h_cashAmount" value="0">
                        <input type="hidden" name="_chequeAmount" id="h_chequeAmount" value="0">
                        <input type="hidden" name="_cardAmount" id="h_cardAmount" value="0">
                        <input type="hidden" name="_giftCardAmount" id="h_giftCardAmount" value="0">
                        <input type="hidden" name="_creditNoteAmount" id="h_creditNoteAmount" value="0">
                        <input type="hidden" name="_creditSalesAmount" id="h_creditSalesAmount" value="0">
                        <input type="hidden" name="creditNote-invID" id="creditNote-invID">
                        <input type="hidden" name="customerCode" id="customerCode" value="CASH">
                        <input type="hidden" name="_trCurrency" id="_trCurrency" value="<?php echo $tr_currency; ?>">
                        <input type="hidden" name="_referenceNO" id="_referenceNO" value="">
                        <input type="hidden" name="_cardNumber" id="_cardNumber" value="">
                        <input type="hidden" name="_bank" id="_bank" value="">
                        <input type="hidden" name="_chequeNO" id="_chequeNO" value="">
                        <input type="hidden" name="_chequeCashDate" id="_chequeCashDate" value="">
                        <input type="hidden" id="isInvoiced" name="isInvoiced">
                    </div>
                </div>

                <div class="col-12" style="margin-top: 1%; padding-bottom: 5%; text-align: center !important;">
                    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                        <div class="row row-centered" style="" id="actionBtn-div">
                            <div class="col-12 ac">
                                <span class="button-wrap">
                                    <button type="button" class="button f-13 button-rounded button-primary button-pill "
                                            onclick="open_pos_payments_modal()"> Pay [F1]
                                    </button>
                                </span>

                                <span class="button-wrap">
                                        <button type="button"
                                                class="button f-13  button-rounded   button-primary button-pill"
                                                onclick="newInvoice()"> New [F5]
                                        </button>
                                 </span>

                                <span class="button-wrap">
                                    <button type="button"
                                            class="button f-13   button-rounded button-primary button-pill"
                                            onclick="hold_invoice()">
                                        Hold [F2]
                                    </button>
                                </span>

                                <span class="button-wrap">
                                    <button type="button" class="button f-13 button-rounded button-primary button-pill"
                                            title="shortcut - F6"
                                            onclick="open_customer_modal()">
                                        Customer [F6]
                                    </button>
                                </span>


                                <span class="button-wrap">
                                   <button type="button"
                                           class="button f-13 button-rounded button-royal button-border button-pill"
                                           onclick="adjust_qty()"
                                           title="shortcut - F8">
                                       Edit Qty [F8]
                                   </button>
                                </span>


                                <span class="button-wrap">
                                   <button type="button"
                                           class="button f-13 button-rounded button-royal button-border button-pill"
                                           title="shortcut - F7"
                                           onclick="checkifItemExsist()">
                                       Recall [F7]
                                   </button>
                                </span>


                                <span class="button-wrap">
                                   <button type="button"
                                           class="button f-13 button-rounded button-royal button-border button-pill"
                                           onclick="checkifItemExsistReturn()"
                                           title="shortcut - F3">
                                       Return [F3]
                                   </button>
                                </span>


                                <span class="button-wrap">
                                 <button type="button"
                                         class="button f-13 button-caution button-box button-raised button-pill"
                                         onclick="deleteItem()">
                                     <i class="fa fa-trash" style="font-size:23px"></i>
                                 </button>
                                </span>


                                <span class="button-wrap">
                                 <button type="button"
                                         class="button f-13 button-caution button-box button-raised button-longshadow button-pill"
                                         onclick="checkifItemExsistpower()">
                                     <i class="fa fa-power-off " style="font-size:23px"></i>
                                 </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 pad0">
                        <div class="well well-sm">
                            <table class="table table-striped table-condensed">
                                <tr>
                                    <td><span class="f-font">Total :</span></td> <!--class="f-1-2em"-->
                                    <td>
                                        <div class="f-font ar"
                                             id="netTotSpan"><?php echo $dPlace == 3 ? '0.000' : '0.00'; ?>
                                        </div>
                                        <input type="hidden" name="netTotVal" id="netTotVal" value="0"/>

                                        <div class="hide"> <!--before Discount-->
                                            <span class="f-font"
                                                  id="totSpan"><?php echo $dPlace == 3 ? '0.000' : '0.00'; ?></span>
                                            <input type="hidden" name="totVal" id="totVal" value="0"/>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="f-font"> Discount [F11] :</span></td> <!--class="f-1-2em"-->
                                    <td>
                                        <input type="text"
                                               class="form-control allownumericwithdecimal w60 f-l f-16 fw600 ar"
                                               name="gen_disc_percentage"
                                               id="gen_disc_percentage" value="0" placeholder="%"/>
                                        <input type="text"
                                               class="allownumericwithdecimal form-control w100 f-r f-16 fw600 ar"
                                               placeholder="<?php echo $dPlace == 3 ? '0.000' : '0.00'; ?>"
                                               name="gen_disc_amount" id="gen_disc_amount"/>
                                        <input type="hidden" name="gen_disc_amount_hide" id="gen_disc_amount_hide"
                                               value="0"/>
                                        <div class="hide"> <!--Item Discount Amount -->
                                            <span class="f-font" id="discSpan">
                                                <?php echo $dPlace == 3 ? '0.000' : '0.00'; ?>
                                            </span>
                                            <input type="hidden" name="discVal" id="discVal" value="0"/>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="f-font">Net Total </span></td>
                                    <td class="ar">
                                        <span class="f-font ar"
                                              id="netTot_after_g_disc_div"><?php echo $dPlace == 3 ? '0.000' : '0.00'; ?></span>
                                        <input type="hidden" name="netTot_after_g_disc" id="netTot_after_g_disc"
                                               value="0"/>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </form>

        </div> <!-- span 7 -->
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <?php $this->load->view('system/pos-general/includes/help-modal'); ?>
            <?php $this->load->view('system/pos-general/includes/number-plate'); ?>
        </div>
    </div>
</div>


<?php
$this->load->view('include/footer');
?>

<div aria-hidden="true" role="dialog" tabindex="-1" id="tender_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 327px">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Payment Tender</h5>
            </div>
            <div class="modal-header row" style="padding: 0px 15px 0px 15px; border-bottom: none">
                <div class="col-md-12" style="background: #3c3939 !important; padding: 4px">
                    <table style="color: #ffffff">
                        <tr>
                            <td style="width: 65px"> Cashier</td>
                            <td>:</td>
                            <td>Admin</td>
                        </tr>
                        <tr>
                            <td style="height: 20px" valign="bottom">Customer</td>
                            <td valign="bottom">:</td>
                            <td valign="bottom"><span class="customerSpan">Cash</span></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-12" style="background: #3c3939 !important; height: 4px">&nbsp;</div>
            </div>
            <form role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body" style="padding: 0px; height: 45%">
                    <table class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Payment Type</th>
                            <th>Tender Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td></td>
                            <td>Cash Amount</td>
                            <td style="padding: 0px"><input type="text" name="" id="cashAmount"
                                                            class="tenderTBTxt tenderPay number inputCustom1"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Cheque Amount</td>
                            <td style="padding: 0px">
                                <div class="input-group">
                                      <span class="input-group-btn">
                                        <button class="btn btn-secondary" id="clear-cheque-amount" type="button"
                                                style="padding: 1px 6px; border-radius: 0px;">X
                                        </button>
                                      </span>
                                    <input type="text" id="chequeAmount"
                                           class="tenderTBTxt tenderPay searchData number inputCustom1" readonly>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Card Amount</td>
                            <td style="padding: 0px">
                                <div class="input-group">
                                      <span class="input-group-btn">
                                        <button class="btn btn-secondary" id="clear-card-amount" type="button"
                                                style="padding: 1px 6px; border-radius: 0px;">X
                                        </button>
                                      </span>
                                    <input type="text" id="cardAmount"
                                           class="tenderTBTxt tenderPay searchData number inputCustom1" readonly>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Gift Card Amount</td>
                            <td style="padding: 0px">
                                <input type="text" name="" id="giftCard"
                                       class="tenderTBTxt tenderPay searchData inputCustom1"
                                       readonly>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Credit Note Amount</td>
                            <td style="padding: 0px">

                                <div class="input-group">
                                      <span class="input-group-btn">
                                        <button class="btn btn-secondary" id="clear-credit-amount" type="button"
                                                style="padding: 1px 6px; border-radius: 0px;">X
                                        </button>
                                      </span>
                                    <input type="text" id="creditNote"
                                           class="tenderTBTxt tenderPay searchData number inputCustom1" readonly>
                                </div>
                            </td>
                        </tr>
                        <tr id="creditsalesfield" class="hidden">
                            <td></td>
                            <td>Credit Sales Amount</td>
                            <td style="padding: 0px"><input type="text" onchange="validateCreditSales()"
                                                            name="creditSalesAmount" id="creditSalesAmount"
                                                            class="tenderTBTxt tenderPay number inputCustom1"></td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="col-md-12">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <table class="table table-condensed" id="paymentTB">
                                <tbody>
                                <tr>
                                    <td>
                                        <div style="width:80px">Net Total</div>
                                    </td>
                                    <td>
                                        <input type="text" name="" id="tenderNetTotal" class="paymentTBTxt number"
                                               disabled="disabled">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:150px">Tender Amount</td>
                                    <td>
                                        <input type="text" name="" id="tenderAmountTotal"
                                               class="paymentTBTxt number"
                                               disabled="disabled">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:150px">Amount Due</td>
                                    <td>
                                        <input type="text" name="" id="tenderDueAmount" class="paymentTBTxt number"
                                               disabled="disabled">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:150px">Change Due</td>
                                    <td>
                                        <input type="text" name="" id="tenderChangeAmount"
                                               class="paymentTBTxt number"
                                               disabled="disabled">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="padding: 10px">
                    <button class="btn btn-primary btn-xs" type="button" id="tenderBtn">Tender</button>
                    <button data-dismiss="modal" class="btn btn-default btn-xs" type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="cardDet_modal" data-keyboard="false" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-sm" style="width: 25%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">Card Details</h5>
            </div>
            <div class="modal-header row" style="padding: 0px 15px 0px 15px; border-bottom: none">
                <div class="col-md-12" style="background: #000 !important; padding: 4px">
                    <table style="color: #ffffff">
                        <tr>
                            <td style="width: 65px">Cashier</td>
                            <td>:</td>
                            <td>Admin</td>
                        </tr>
                        <tr>
                            <td style="height: 20px" valign="bottom">Customer</td>
                            <td valign="bottom">:</td>
                            <td valign="bottom"><span class="customerSpan">Cash</span></td>
                        </tr>
                        <!--<tr>
                        <td style="height: 20px" valign="bottom">Invoice No</td>
                        <td valign="bottom">:</td>
                        <td valign="bottom"><span id="invoiceNo"></span> </td>
                    </tr>-->
                    </table>
                </div>
                <div class="col-md-12" style="background: #000 !important; height: 4px">&nbsp;</div>
            </div>
            <form role="form" id="cardPayment_form" class="form-horizontal">
                <div class="modal-body" style="padding: 0px;">
                    <table class="<?php echo table_class(); ?>">
                        <tbody>
                        <tr>
                            <td></td>
                            <td>Card Amount</td>
                            <td style="padding: 0px">
                                <input type="text" name="cardAmount_cardDet" id="cardAmount_cardDet"
                                       class="tenderTBTxt number inputCustom1">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Card Number</td>
                            <td style="padding: 0px">
                                <input type="text" name="cardNumber" id="cardNumber"
                                       class="tenderTBTxt number inputCustom1">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Reference No</td>
                            <td style="padding: 0px">
                                <input type="text" name="referenceNO" id="referenceNO"
                                       class="tenderTBTxt number inputCustom1">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Card <!--Bank--></td>
                            <td style="padding: 0px">
                                <select name="bank" id="bank" class="tenderTBTxt" style="height: 24px">
                                    <?php
                                    foreach ($bank_card as $card) {
                                        echo '<option value="' . $card['GLCode'] . '"> ' . $card['description'] . ' </option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer" style="padding: 10px">
                    <input type="hidden" name="invID" id="invID" value="">
                    <button class="btn btn-primary btn-xs" type="button" onclick="save_moreCardDetails()">Save
                    </button>
                    <button data-dismiss="modal" class="btn btn-default btn-xs" type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="cheque_modal" data-keyboard="false" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-sm" style="width: 25%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">Cheque Details</h5>
            </div>
            <div class="modal-header row" style="padding: 0px 15px 0px 15px; border-bottom: none">
                <div class="col-md-12" style="background: #000 !important; padding: 4px">
                    <table style="color: #ffffff">
                        <tr>
                            <td style="width: 65px">Cashier</td>
                            <td>:</td>
                            <td>Admin</td>
                        </tr>
                        <tr>
                            <td style="height: 20px" valign="bottom">Customer</td>
                            <td valign="bottom">:</td>
                            <td valign="bottom"><span class="customerSpan">Cash</span></td>
                        </tr>

                    </table>
                </div>
                <div class="col-md-12" style="background: #000 !important; height: 4px">&nbsp;</div>
            </div>
            <form role="form" id="cardPayment_form2" class="form-horizontal">
                <div class="modal-body" style="padding: 0px;">
                    <table class="<?php echo table_class(); ?>">
                        <tbody>
                        <tr>
                            <td></td>
                            <td>Cheque Amount</td>
                            <td style="padding: 0px">
                                <input type="text" name="chequeAmount_cheqDet" id="chequeAmount_cheqDet"
                                       class="tenderTBTxt number inputCustom1">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Cheque Number</td>
                            <td style="padding: 0px">
                                <input type="text" name="chequeNumber" id="chequeNumber"
                                       class="tenderTBTxt number inputCustom1">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Cheque Date</td>
                            <td style="padding: 0px">

                                <input type="text" value="<?php echo date('Y-m-d'); ?>" id="cashDate"
                                       class="tenderTBTxt dateFields inputCustom1" style="padding-left: 10px">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer" style="padding: 10px">
                    <button class="btn btn-primary btn-xs" type="button" onclick="save_moreChequeDetails()">Save
                    </button>
                    <button data-dismiss="modal" class="btn btn-default btn-xs" type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="posg_help_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-question-circle" aria-hidden="true"></i> Help - Shortcut </h4>
            </div>
            <div class="modal-body">

            </div>

            <div class="modal-footer" style="padding: 10px">
                <button data-dismiss="modal" class="btn btn-danger btn-md" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="customer_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Customer </h4>
            </div>
            <div class="modal-body">
                <div class="form-inline" style="">
                    <div class="form-group" style="margin: 0px 4px 7px 10px; padding-top: 10px">
                        <label for="customerSearch" style="font-weight: 600">Search </label>
                        <input type="text" placeholder="search [F10]" name="customerSearch" class="form-control"
                               id="customerSearch"
                               style="height: 20px; font-size: 10px; padding: 7px 5px" autocomplete="off">
                    </div>
                    <div class="form-group" style="margin: 0px 4px 7px 10px; padding-top: 10px">
                        <button type="button" onclick="add_new_customer_modal()"
                                class="btn btn-primary btn-xs pull-right">
                            <i class="fa fa-plus"></i>Add
                        </button>
                    </div>
                </div>
                <div class="fixHeader_Div" style="height: 300px; overflow: auto;">
                    <table class="<?php echo table_class(); ?> arrow-nav" id="customerSearchTB">
                        <thead>
                        <tr class="headerTR">
                            <th></th>
                            <th>Code</th>
                            <th>Secondary Code</th>
                            <th>Name</th>
                            <th>Telephone No</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td></td>
                            <td>&nbsp;</td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <button data-dismiss="modal" class="btn btn-primary btn-md" type="button"
                        onclick="selectEmployee()">
                    Select
                </button>
                <button data-dismiss="modal" class="btn btn-danger btn-md" type="button">Close</button>
                <button data-dismiss="modal" class="btn btn-default btn-md" onclick="defaultCustomer()" type="button">
                    Clear
                </button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="recall_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-sm" style="width: 50%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Invoice Recall</h4>
            </div>
            <div class="modal-body" style="padding: 0px; height: 36%">
                <div class="form-inline" style="">
                    <div class="form-group" style="margin: 10px; margin-top: 0px; padding-top: 10px;">
                        <label for="recall_search" style="color: #eff7ff; font-weight: 600">Search </label>
                        <input type="text" onchange="open_recallHold_modal_search()" name="recall_search"
                               class="form-control" id="recall_search"
                               style="height: 20px; font-size: 10px; padding: 7px 5px" autocomplete="off">
                    </div>
                </div>
                <div class="fixHeader_Div" style="height: 150px">
                    <table class="<?php echo table_class(); ?>" id="invoiceSearchTB">
                        <thead>
                        <tr class="headerTR">
                            <th></th>
                            <th>Code</th>
                            <th>CustomerID</th>
                            <th>Customer</th>
                            <th>Date & Time</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <button class="btn btn-primary btn-md" type="button" onclick="selectInvoice()">
                    Select
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-md" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="error_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Item Qty Insufficient Details</h4>
            </div>
            <div class="modal-body">
                <table class="<?php echo table_class(); ?>" id="qtyDemandTB">
                    <thead>
                    <tr>
                        <th>Code</th>
                        <th>Item</th>
                        <th>UOM</th>
                        <th>Request QTY</th>
                        <th>Available QTY</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer" style="padding: 10px">
                <button data-dismiss="modal" class="btn btn-default btn-xs" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="creditNote_modal" class="modal" style="z-index: 1000000;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="" style="background-color: #0581B8;height: 45px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:white;"><span
                        aria-hidden="true" style="color:white;">&times;</span></button>
                <h4 class="modal-title" style="color:white;">Credit Note Search</h4>
            </div>
            <div class="modal-body" style="padding: 0px; height: 50%">
                <div class="form-inline" style="">
                    <div class="form-group" style="margin: 10px; margin-top: 0px; padding-top: 10px;">
                        <label for="creditNote_search" style=" font-weight: 600">Search </label>
                        <input type="text" name="creditNote_search" class="form-control" id="creditNote_search"
                               style="height: 20px; font-size: 10px; padding: 7px 5px" autocomplete="off">
                    </div>
                </div>
                <div class="fixHeader_Div" style="height: 280px">
                    <table class="table table-striped table-condensed" id="creditNoteTB">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Note</th>
                            <th>Date</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <button data-dismiss="modal" class="btn btn-primary btn-sm" type="button"
                        onclick="selectCreditNote()">
                    Select
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="giftCard_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Gift Card Search</h5>
            </div>
            <div class="modal-body" style="padding: 0px; height: 25%">
                <div class="form-inline" style="">
                    <div class="form-group" style="margin: 10px; margin-top: 0px; padding-top: 10px;">
                        <label for="giftCard_search" style="color: #eff7ff; font-weight: 600">Search </label>
                        <input type="text" name="giftCard_search" class="form-control" id="giftCard_search"
                               style="height: 20px; font-size: 10px; padding: 7px 5px" autocomplete="off">
                    </div>
                </div>

                <table class="table table-striped table-condensed">
                    <tr>
                        <td>
                            <lable>Issued Date</lable>
                        </td>
                        <td>:</td>
                        <td>2016-09-15</td>
                    </tr>
                    <tr>
                        <td>
                            <lable>Amount</lable>
                        </td>
                        <td>:</td>
                        <td>3000.000</td>
                    </tr>
                    <tr>
                        <td>
                            <lable>Expired Date</lable>
                        </td>
                        <td>:</td>
                        <td>2016-12-15</td>
                    </tr>
                </table>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <button data-dismiss="modal" class="btn btn-primary btn-md" type="button"
                        onclick="selectEmployee()">
                    Select
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-md" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="till_modal" class="modal fade" data-keyboard="false"
     style="display: none;">
    <div class="modal-dialog" style="/*width: 50%*/">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close tillModal_close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="tillModal_title">Start Day</h5>
            </div>
            <div class="modal-body" style="padding: 10px; height: auto">
                <div class="smallScroll" id="currencyDenomination_data" align="center"
                     style="height: auto; overflow-y: scroll"></div>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <input type="hidden" id="isStart"/>

                <button class="btn btn-primary btn-sm" type="button" id="tillSave_Btn">
                    Save
                </button>
                <button data-dismiss="modal" onclick="window.location = '<?php echo site_url('dashboard'); ?>'"
                        class="btn btn-default btn-sm tillModal_close" type="button">Close
                </button>

            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="return_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 95%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <!--<div class="modal-header " id="">-->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Invoice Return</h4>
            </div>

            <div class="modal-body" style="padding: 0px;">
                <?php echo form_open('', 'id="return_form" autocomplete="off"') ?>
                <table id="inv-return-tb">
                    <tbody>
                    <tr>
                        <td style="width: 130px">Customer Code</td>
                        <td>
                            <input type="text" class="form-control returnTxt" name="return-cusCode"
                                   id="return-cusCode" readonly>
                            <input type="hidden" class="returnTxt" name="return-customerID" id="return-customerID"
                                   value="">
                        </td>
                        <td style="width: auto">&nbsp;</td>
                        <td width="100px">Credit Note No</td>
                        <td width="">
                            <input type="text" class="form-control returnTxt" aria-invalid="credit-note-no"
                                   id="returnCreditNo" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 130px">Customer Name</td>
                        <td>
                            <input type="text" class="form-control returnTxt" name="return-cusName"
                                   id="return-cusName" readonly>
                        </td>
                        <td></td>
                        <td>Date</td>
                        <td>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-calendar"
                                                                  style="font-size: 10px;"></i></div>
                                <input type="text" name="return-date" value="<?php echo date('Y-m-d'); ?>"
                                       id="return-date"
                                       class="form-control returnTxt dateFields " style="width: 165px">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 130px">Customer Balance</td>
                        <td>
                            <input type="text" class="form-control number returnTxt" id="return-cusBalance"
                                   readonly>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>

                <table id="table-invoice-return">
                    <tr>
                        <td style="width: 100px">Invoice No</td>
                        <td style="width: 200px">
                            <input type="text" class="form-control returnTxt" id="invoiceCode" value="COM/REF0000">
                            <input type="hidden" class="returnTxt" name="return-invoiceID" id="return-invoiceID"
                                   value="">
                        </td>
                        <td style="width: 50px; padding: 0px">

                            <button type="button" class="btn btn-primary" onclick="invoice_search()">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </button>

                        </td>
                        <td style="width: 100px">&nbsp;</td>
                        <td style="width: 100px">Invoice Date</td>
                        <td style="width: 130px">
                            <input type="text" class="form-control returnTxt" id="return-inv-date" readonly>
                        </td>
                    </tr>
                </table>

                <div style="padding: 10px;">
                    <div class="fixHeader_Div" style="height: 150px; border: 1px solid #CCCCCC">
                        <table class="<?php echo table_class(); ?>" id="returnInvoiceTB">
                            <thead>
                            <tr class="header_tr">
                                <th></th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>UOM</th>
                                <th>Bal.Qty</th>
                                <th width="60px">R.Qty</th>
                                <th>Price</th>
                                <th>Amount</th>
                                <th>Disc%</th>
                                <th>Discount</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <table id="return-calculation-tb" style="margin-top: 15px" border="0">
                    <tr>
                        <td rowspan="4" style="width: 150px" class="hidden" valign="middle">
                            <img class="img-thumbnail" src="<?php echo base_url('images/item/no-image.png'); ?>"
                                 id="return-item-image" style="height: 100px; width: 150px"/>
                        </td>
                        <td style="width: 70px" rowspan="4" valign="top">Remarks</td>
                        <td rowspan="4" valign="top">
                                <textarea name="remarks" cols="3" id="remarks"
                                          style="width: 90%; height: 50px; padding: 2px 5px"></textarea>
                        </td>
                        <td style="width: 70px" valign="top">&nbsp;</td>
                        <td style="width: 120px">Invoice Total</td>
                        <td style="width: 120px">
                            <input type="text" class="form-control number returnTxt" id="return-calculate-invTot"
                                   readonly>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 70px">&nbsp;</td>
                        <td>Invoice Balance</td>
                        <td style="width: 120px">
                            <input type="text" class="form-control number returnTxt"
                                   id="return-calculate-invBalance" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 70px">&nbsp;</td>
                        <td style="width: 120px">Credit Total</td>
                        <td style="width: 120px">
                            <input type="text" class="form-control number returnTxt" name="return-credit-total"
                                   id="return-credit-total" readonly>
                            <input type="hidden" class="returnTxt" name="return-subTotalAmount"
                                   id="return-subTotalAmount">
                            <input type="hidden" class="returnTxt" name="return-discTotal" id="return-discTotal">
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 70px">&nbsp;</td>
                        <td style="width: 120px">Refundable</td>
                        <td style="width: 120px">
                            <input type="text" class="form-control number returnTxt" name="return-refund"
                                   id="return-refund">
                            <input type="hidden" name="return-refundable-hidden" id="return-refundable-hidden">
                        </td>
                    </tr>
                </table>
                <?php echo form_close(); ?>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <button class="btn btn-primary btn-md" type="button" onclick="checkifItemExsistReturn()">New</button>
                <button class="btn btn-primary btn-md" type="button" onclick="itemReturn('exchange')">Exchange
                </button>
                <button class="btn btn-primary btn-md" type="button" onclick="itemReturn('Refund')">Refund</button>

                <button data-dismiss="modal" class="btn btn-default btn-md" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="testModal" class="modal fade" data-keyboard="false"
     style="display: none;">
    <div class="modal-dialog" style="/*width: 50%*/">
        <div class="modal-content">

            <div class="modal-body" style="padding: 0px; height: 0px">
                <div class="alert alert-success fade in" style="margin-top:18px;">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close"
                       style="text-decoration: none">
                        <i class="fa fa-refresh fa-spin fa-3x fa-fw" style="font-size:15px"></i>
                    </a>
                    <strong>Session Successfully closed.</strong> Redirect in <span id="countDown"> 5 </span>
                    Seconds.
                </div>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="print_template" data-keyboard="false" class="modal fade"
     style="display: none;">
    <div class="modal-dialog" style="width: 420px">
        <div class="modal-content">
            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="newInvoice(1)">
                    <i class="fa fa-close text-red"></i></button>
                <h4 class="modal-title">Print </h4>
            </div>
            <div class="modal-body" id="pos_modalBody_posPrint_template" style="height: 400px;overflow-y: auto;">
                <div id="wrapper">
                    <div id="print_content"></div>

                    <div id="bkpos_wrp" style="margin-top: 10px;">


                    </div>
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-block btn-primary btn-flat" onclick="print_pos_report()"
                        style="">
                    <i class="fa fa-print"></i> Print
                </button>
                <button type="button" class="btn btn-block btn-default btn-flat" onclick="close_posPrint();">
                    <i class="fa fa-angle-double-left" aria-hidden="true"></i> Back to POS &amp; Create New
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal  -->
<?php
$data['customerCategory'] = $customerCategory;
$data['gl_code_arr'] = $gl_code_arr;
$data['country_arr'] = $country_arr;
$data['currncy_arr'] = $currncy_arr;
$data['taxGroup_arr'] = $taxGroup_arr;
$this->load->view('system/pos-general/includes/payment-modal', $data);
$this->load->view('system/pos-general/includes/rcgc-modal', $data);
$this->load->view('system/pos-general/includes/customer-modal', $data);
$this->load->view('system/pos-general/modals/gpos-modal-search-item', $data);
$this->load->view('system/pos-general/js/gpos-js', $data);
?>

<script type="text/javascript">


    var dPlaces = <?php echo $this->common_data['company_data']['company_default_decimal'];?>;
    var accountsreceivable = <?php echo $this->common_data['controlaccounts']['ARA'];?>;
    var currency = <?php echo $this->common_data['company_data']['company_default_currencyID'];?>;
    var country = <?php echo $this->common_data['company_data']['countryID'];?>;
    var pos_form = $('#pos_form');
    var itemDisplayTB = $('#itemDisplayTB');
    var enable_BC = $('#enable_BC');
    var formInput = $('.formInput');
    var itemCount = $('.itemCount');
    var itemSearch = $('#itemSearch');
    var itemAutoID = $('#itemAutoID');
    var itemDescription = $('#itemDescription');
    var itemDescription2 = $('#itemDescription2');
    var currentStockDsp = $('#currentStockDsp');
    var currentStock = $('#currentStock');
    var itemUOM = $('#itemUOM');
    var itemQty = $('#itemQty');
    var disPer = $('#disPer');
    var disAmount = $('#disAmount');
    var salesPrice = $('#salesPrice');
    var netTotSpan = $('#netTotSpan');
    var discSpan = $('#discSpan');
    var totSpan = $('#totSpan');
    var netTotVal = $('#netTotVal');
    var discVal = $('#discVal');
    var totVal = $('#totVal');
    var error_modal = $('#error_modal');
    var tender_modal = $('#tender_modal');
    var customer_modal = $('#customer_modal');
    var modal_qty_box = $('#modal_qty_box');
    var showSweetAlert = $('.showSweetAlert');
    var modal_search_item = $('#modal_search_item');
    var pos_payments_modal = $('#pos_payments_modal');
    var cardDet_modal = $('#cardDet_modal');
    var cheque_modal = $('#cheque_modal');
    var recall_modal = $('#recall_modal');
    var till_modal = $('#till_modal');
    var tenderPay = $('.tenderPay');
    var customerSearchTB = $('#customerSearchTB');
    var creditNoteTB = $('#creditNoteTB');
    var invoiceSearchTB = $('#invoiceSearchTB');
    var selectedCusArray = [];
    var selectedItemArray = [];
    var exceededItemArray = [];
    var freeIssueData;
    var returnInvoiceTB = $('#returnInvoiceTB');

    var _referenceNO = $('#_referenceNO');
    var _cardNumber = $('#_cardNumber');
    var _bank = $('#_bank');

    var _chequeNO = $('#_chequeNO');
    var _chequeCashDate = $('#_chequeCashDate');
    till_modal.on('shown.bs.modal', function (e) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'<?php echo $this->security->get_csrf_token_name() ?>': '<?php echo $this->security->get_csrf_hash()?>'},
            url: "<?php echo site_url('Pos/load_currencyDenominationPage'); ?>",
            beforeSend: function () {
                $('#currencyDenomination_data').html('');
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#currencyDenomination_data').html(data);
                if ($('#isStart').val() == 1) {
                    $('#counterID').prop('disabled', false);
                }
                else {
                    $('#counterID').prop('disabled', true);
                }
            }, error: function () {
                stopLoad();
                myAlert('e', 'Error in loading currency denominations.')
            }
        });
    });

    <?php
    if ($isHadSession == 0) {
        echo '$("#isStart").val(1);';
        echo '$(".tillModal_close").hide();';
        echo '$("#tillModal_title").text("Day Start");';
        echo '$("#tillSave_Btn").attr("onclick", "shift_create()");';
        echo 'till_modal.modal({backdrop:"static"});';
    }
    ?>


    netTotSpan.text(commaSeparateNumber(0, dPlaces));
    discSpan.text(commaSeparateNumber(0, dPlaces));
    totSpan.text(commaSeparateNumber(0, dPlaces));


    $(document).ready(function () {

        setTimeout(function () {
            itemSearch.focus();
        }, 500);

        itemSearch_typeHead();

        /* $("#receivableAccount").prop("disabled", true);*/
        $('.select2').select2();

        $('.dateFields').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function () {
            if ($(this).attr('id') == 'return-date') {
                var invDate = $('#return-inv-date').val();
                var thisDate = $(this).val();

                if (invDate > thisDate) {
                    $(this).datepicker('update', invDate);
                    myAlert('w', 'Return date cannot be laser than invoice date <br> [ ' + invDate + ' ]');
                }

            }
            $(this).datepicker('hide');
        });

        itemDisplayTB.tableHeadFixer({
            head: true,
            foot: true,
            left: 1,
            right: 0,
            'z-index': 0
        });

        customerSearchTB.tableHeadFixer({
            head: true,
            foot: true,
            left: 1,
            right: 0,
            'z-index': 0
        });

        creditNoteTB.tableHeadFixer({
            head: true,
            foot: true,
            left: 1,
            right: 0,
            'z-index': 0
        });

        invoiceSearchTB.tableHeadFixer({
            head: true,
            foot: true,
            left: 1,
            right: 0,
            'z-index': 0
        });

        returnInvoiceTB.tableHeadFixer({
            head: true,
            foot: true,
            left: 1,
            right: 0,
            'z-index': 0
        });

        pos_form.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                itemSearch: {validators: {notEmpty: {message: '&nbsp;'}}}, /*Item Code is required.*/
                itemUOM: {validators: {notEmpty: {message: '&nbsp;'}}}, /*UOM is required.*/
                itemQty: {validators: {notEmpty: {message: '&nbsp;'}}}, /*QTY is required.*/
                salesPrice: {validators: {notEmpty: {message: '&nbsp;'}}} /*Price is required.*/
            },
        }).on('success.form.bv',
            function (e) {
                e.preventDefault();

                var qty = $.trim(itemQty.val());

                if ($.isNumeric(qty) && qty > 0) {
                    var amount = qty * salesPrice.val();

                    var discAmount = amount * disPer.val() * 0.01;
                    var thisNetTot = (amount - discAmount);
                    var image_hidden = $('#item-image-hidden').val();

                    var itemDet = '';
                    itemDet += '<td align="right"></td>';
                    itemDet += '<td>' + itemSearch.val() + '</td>';
                    itemDet += '<td>' + itemDescription.val() + '</td>';
                    itemDet += '<td>' + itemUOM.val() + '</td>';
                    itemDet += '<td align="right">' + qty + '</td>';
                    itemDet += '<td align="right">' + commaSeparateNumber(salesPrice.val(), dPlaces) + '</td>';
                    itemDet += '<td align="right">' + commaSeparateNumber(amount, dPlaces) + '</td>';
                    itemDet += '<td align="right">' + getNumberAndValidate(disPer.val()) + '</td>';
                    itemDet += '<td align="right">' + commaSeparateNumber(discAmount, dPlaces) + '</td>';
                    itemDet += '<td align="right">' + commaSeparateNumber(thisNetTot, dPlaces) + '</td>';
                    itemDet += '<td align="right">';
                    itemDet += '<a onclcik="editRow(this)"><span class="glyphicon glyphicon-pencil editRow" style="position: static"></span></a>';
                    itemDet += '&nbsp; | &nbsp;';
                    itemDet += '<span class="glyphicon glyphicon-trash deleteRow" style="color:rgb(209, 91, 71); position: static"></span>';
                    itemDet += '<input type="hidden" name="itemID[]"  class="itemID" value="' + itemAutoID.val() + '" >';
                    itemDet += '<input type="hidden" name="itemName[]" class="itemName" value="' + itemDescription.val() + '" >';
                    itemDet += '<input type="hidden" name="itemUOM[]" class="itemUOM" value="' + itemUOM.val() + '" >';
                    itemDet += '<input type="hidden" name="itemQty[]" class="itemQty" value="' + itemQty.val() + '" >';
                    itemDet += '<input type="hidden" name="itemPrice[]" class="itemPrice" value="' + salesPrice.val() + '" >';
                    itemDet += '<input type="hidden" name="itemDis[]" class="itemDis" value="' + disPer.val() + '" >';
                    itemDet += '<input type="hidden" class="thisCurrentStk" value="' + currentStock.val() + '" >';
                    itemDet += '<input type="hidden" class="discountAmount" value="' + discAmount + '" >';
                    itemDet += '<input type="hidden" class="totalAmount" value="' + amount + '" >';
                    itemDet += '<input type="hidden" class="netAmount" value="' + thisNetTot + '" >';
                    itemDet += '<input type="hidden" class="item-image-hidden" value="' + image_hidden + '" >';
                    itemDet += '</td>';


                    if ($('#is-edit').val() != 1) {
                        //alert("was up");
                        $('#itemDisplayTB tr').removeClass('selectedTR');
                        itemDisplayTB.append('<tr class="selectedTR">' + itemDet + '</tr>');
                    }
                    else {
                        $('#is-edit').val(0);
                        $('#itemDisplayTB .editTR').html(itemDet).removeClass('editTR');
                    }

                    itemSearch.prop('readonly', false);


                    itemAdd_sub_function();
                    isThereAnyPromotion(itemAutoID.val());


                    var noofitems = 0;
                    $('#itemDisplayTB tr').each(function () {
                        var noqty = $(this).find('td:eq(4)').html();
                        if (noqty) {
                            noofitems = noofitems + parseInt(noqty);
                        }
                    });

                    itemCount.html(parseInt(noofitems));
                    //alert(qty);
                    formInput.val('');
                    itemSearch.typeahead('val', '');
                    itemUOM.empty();
                    itemUOM.css('background', '#eee');
                    formInput.prop('readonly', true);
                    itemUOM.prop('readonly', true);
                    itemSearch.focus();
                    calculateDiscount_byPercentage();

                    pos_form[0].reset();
                    pos_form.bootstrapValidator('resetForm', true);

                } else {
                    itemQty.val('');
                    pos_form.bootstrapValidator('revalidateField', 'itemQty');
                    myAlert('e', 'Qty is not valid');
                }
            });
    });


    function itemAdd_sub_function() {
        getTot();
        getDiscountTot();
        getNetTot();
        addTrNumber();
    }


    function itemSearch_typeHead() {
        return false;
        var tmpBC = $("#enable_BC").is(":checked");

        var item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace();
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "<?php echo site_url();?>/Pos/item_search/?q=%QUERY&bc=" + tmpBC
        });

        item.initialize();
        itemSearch.typeahead(null, {
            minLength: 3,
            highlight: true,
            displayKey: 'itemSystemCode',
            source: item.ttAdapter(),
            templates: {
                empty: [
                    /*'<div class="tt-suggestion"><div style="white-space: normal;">',
                     'Not found',
                     '</div></div>'*/
                ].join('\n'),
                suggestion: Handlebars.compile('<div><strong>{{itemDescription}}</strong> – {{itemSystemCode}}</div>')
            }
        }).on('typeahead:selected', function (object, datum) {
            setValues_masterForm(datum);

        });
    }

    function item_search_loadToInvoice(barCode) {
        $.ajax({
            async: true,
            type: 'get',
            dataType: 'json',
            url: "<?php echo site_url('Pos/item_search_barcode?q='); ?>" + barCode,
            beforeSend: function () {
                $("#pos-add-btn").prop('disabled', false);
                $("#itemSearchResultTblBody .selectedTR").find('button').html('<i class="fa fa-refresh fa-spin"></i> Add');
            },
            success: function (data) {
                $("#itemSearchResultTblBody .selectedTR").find('button').html('<i class="fa fa-plus"></i> Add')
                var itemVal = $("#itemSearch").val();
                if (data == null) {
                    if (itemVal.trim() != '') {
                        myAlert('e', 'Item ' + barCode + ' does not exist');
                        $("#itemSearch").val('');
                        pos_form[0].reset();
                        pos_form.bootstrapValidator('resetForm', true);
                    }
                } else {
                    setValues_masterForm(data);
                }
            }, error: function () {
                $("#itemSearchResultTblBody .selectedTR").find('button').html('<i class="fa fa-plus"></i> Add')
                myAlert('e', 'Error while loading')
            }
        });
    }

    function setValues_masterForm(datum) {

        itemSearch.val(datum.itemSystemCode);
        itemAutoID.val(datum.itemAutoID);
        itemDescription.val(datum.itemDescription);
        itemDescription2.val(datum.itemDescription);
        $('#item-image').attr('src', "<?php echo base_url('images/item/');?>/" + datum.itemImage);
        $('#item-image-hidden').val("<?php echo base_url('images/item/');?>/" + datum.itemImage);
        currentStockDsp.val(datum.currentStock);
        currentStock.val(datum.currentStock);
        currentStock.attr('data-value', datum.currentStock);
        var thatQty = $("#tmp_qty_modal").val();
        itemQty.val(thatQty);


        salesPrice.val(datum.companyLocalSellingPrice);

        fetch_related_uom_posgen(datum.defaultUnitOfMeasure, datum.defaultUnitOfMeasure);


    }

    function isThereAnyPromotion(itemAutoID) {
        /*$.each(freeIssueData, function(i, data){
         console.log(data.itemAutoID);
         if(data.itemAutoID == itemAutoID){
         //data.
         return false;
         }
         });*/
    }

    itemUOM.change(function () {
        var conversion = getNumberAndValidate($(this).find('option:selected').attr('data-value'));
        var defaultStk = currentStock.attr('data-value');
        //console.log( 'defaultStk:'+defaultStk+' /// conversion:'+conversion);
        currentStockDsp.val(defaultStk * conversion);
        currentStock.val(defaultStk * conversion);

        itemQty.val('');
        pos_form.bootstrapValidator('revalidateField', 'itemQty');


    });

    itemSearch.keyup(function (e) {

        if (e.keyCode == 13) {
            item_search_loadToInvoice(itemSearch.val())
        }

        return false;

        if (e.keyCode == 8) {
            var thisVal = $(this).val();
            pos_form.bootstrapValidator('resetForm', true);
            $(this).val(thisVal);
            itemDescription2.val('');
            itemUOM.empty();
            itemUOM.css('background', '#eee');
            formInput.prop('readonly', true);
            itemUOM.prop('readonly', true);
        }
        else if (e.keyCode != 13) {
            formInput.val('');
            itemDescription2.val('');
            itemUOM.empty();
            itemUOM.css('background', '#eee');
            formInput.prop('readonly', true);
            itemUOM.prop('readonly', true);
        }
    });

    function fetch_related_uom(short_code, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'short_code': short_code},
            url: "<?php echo site_url('dashboard/fetch_related_uom'); ?>",
            success: function (data) {
                formInput.prop('readonly', false);
                itemUOM.prop('readonly', false);
                itemUOM.css('background', '#fff');
                itemUOM.empty();
                itemUOM.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        itemUOM.append('<option value="' + text['UnitShortCode'] + '" data-value="' + text['conversion'] + '">' + text['UnitShortCode'] + ' | ' + text['UnitDes'] + '</option>');
                    });
                    if (select_value) {
                        itemUOM.val(select_value);
                    }
                }
                re_validate();

                //console.log(enable_BC.prop('checked'));
                if (enable_BC.prop('checked') == true) {
                    $('#pos_form').bootstrapValidator();
                    //itemSearch.trigger("keyup", {which: 13});
                }
                else {
                    itemUOM.focus();
                }
                calculateDiscount_byPercentage();
            }, error: function () {
                myAlert('e', 'Error in UMO fetching.')
            }
        });
    }

    function re_validate() {
        pos_form.bootstrapValidator('revalidateField', 'itemUOM');
        pos_form.bootstrapValidator('revalidateField', 'itemQty');
        pos_form.bootstrapValidator('revalidateField', 'salesPrice');
    }

    salesPrice.keyup(function (e) {
        if (e.keyCode == 13) {
            $('#pos_form').bootstrapValidator();
        } else {
            var thisVal = getNumberAndValidate($(this).val());
            var disCountPer = getNumberAndValidate(disPer.val());

            if (thisVal > 0 && disCountPer > 0) {
                var m = thisVal * disCountPer * 0.01;
                disAmount.val(commaSeparateNumber(m, dPlaces));
            }
            else {
                disAmount.val(commaSeparateNumber(0, dPlaces));
            }

        }
    });

    $(document).on('keypress', '.number', function (event) {
        var amount = $(this).val();
        if (amount.indexOf('.') > -1) {
            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        }
        else {
            if (event.which != 8 && event.which != 46 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        }
    });

    $(document).on('click', '.deleteRow', function () {
        if ($(this).closest('tr').hasClass('editTR')) {
            myAlert('e', 'You cannot delete this item while it is edit mode.');
        }
        else {
            var parentTr = $(this).closest('tr');
            parentTr.remove();
            trRemove();
        }

    });

    $(document).on('click', '.editRow', function () {
        clearData();
        $('#itemDisplayTB tr').removeClass('selectedTR editTR');
        $(this).closest('tr').addClass('selectedTR editTR');
        var parentTr = $('#itemDisplayTB').find('.selectedTR');


        $('#itemSearch').val($.trim(parentTr.find('td:eq(1)').html())).prop('readonly', true);
        itemAutoID.val(parentTr.find('td:eq(10) .itemID').val());
        itemDescription.val(parentTr.find('td:eq(10) .itemName').val());
        currentStock.val(' ' + parentTr.find('td:eq(10) .thisCurrentStk').val());
        $('#currentStockDsp').val(parentTr.find('td:eq(10) .thisCurrentStk').val());
        $('#itemDescription2').val(parentTr.find('td:eq(10) .itemName').val());
        itemQty.val(parentTr.find('td:eq(10) .itemQty').val());
        salesPrice.val(parentTr.find('td:eq(10) .itemPrice').val());
        disPer.val(parentTr.find('td:eq(10) .itemDis').val());
        var discountAmount_tmp = parentTr.find('td:eq(10) .itemDis').val();
        var salesPrice_tmp = parentTr.find('td:eq(10) .itemPrice').val();
        var discountAmount_tmp = parseFloat(discountAmount_tmp);
        if (discountAmount_tmp > 0) {
            var unitDiscount = parseFloat(salesPrice_tmp) * (parseFloat(discountAmount_tmp) / 100)
            disAmount.val(unitDiscount);
        }

        var edit_umo = parentTr.find('td:eq(10) .itemUOM').val();
        itemUOM.append('<option value="' + edit_umo + '" >' + edit_umo + '</option>');
        $('.formInput').not('#currentStockDsp').prop('readonly', false);
        $('#is-edit').val(1);
        $("#itemQty").select();
    });

    function trRemove() {
        addTrNumber();
        var noofitems = 0;
        $('#itemDisplayTB tr').each(function () {
            var noqty = $(this).find('td:eq(4)').html();
            if (noqty) {
                noofitems = noofitems + parseInt(noqty);
            }
        });
        itemCount.html(parseInt(noofitems));

        getTot();
        getDiscountTot();
        getNetTot();
        calculateDiscount_byPercentage();
    }


    disPer.keyup(function () {
        var disc = $.trim($(this).val());
        if (disc != '') {
            if (disc > 100) {
                myAlert('e', 'Discount Percentage must be lesser than 100');
                $(this).val('');
                disAmount.val('');
            }
            else if (disc > 0) {
                var price = $.trim(salesPrice.val());
                if (price != '') {
                    var m = price * disc * 0.01;
                    disAmount.val(commaSeparateNumber(m, dPlaces));
                }
            }
            else if (disc == 0) {
                disAmount.val(commaSeparateNumber(0, dPlaces));
            }
        }
    });

    disAmount.keyup(function () {
        var disAmount_val = $(this).val();
        if (disAmount_val != '') {
            disPer.val('');
            var salesPrice_val = getNumberAndValidate(salesPrice.val());
            if (salesPrice_val < disAmount_val) {
                myAlert('e', 'Discount amount could not be greater than sales price');
                $(this).val('');
                disPer.val('');
            }
            else if (disAmount_val > 0) {
                var discountInPercentage = parseFloat((disAmount_val * 100) / salesPrice_val);
                disPer.val(discountInPercentage);
            }

        } else {
            disPer.val('');
        }
    });

    function getTot() {
        var sum = 0;
        $('.totalAmount').each(function () {
            sum += parseFloat($(this).val());
        });

        totVal.val(sum);
        totSpan.text(commaSeparateNumber(sum, dPlaces));

        return sum;
    }

    function getDiscountTot() {
        var sum = 0;
        $('.discountAmount').each(function () {
            sum += parseFloat($(this).val());
        });

        discVal.val(sum);
        discSpan.text(commaSeparateNumber(sum, dPlaces));

        return sum;
    }

    function getNetTot() {
        var sum = 0;
        $('.netAmount').each(function () {
            sum += parseFloat($(this).val());
        });

        netTotSpan.text(commaSeparateNumber(sum, dPlaces));
        $('#tenderNetTotal').val(commaSeparateNumber(sum, dPlaces));
        netTotVal.val(sum);

        return sum;
    }

    function addTrNumber() {
        var i = 0;
        $('#itemDisplayTB tr').each(function () {
            $(this).find('td:eq(0)').html(i);
            i += 1;
        });
    }

    tenderPay.keyup(function () {
        var sum = 0;
        tenderPay.each(function () {
            var thisAmount = getNumberAndValidate($(this).val());
            sum += (thisAmount == '') ? 0 : parseFloat(thisAmount);
        });
        $('#tenderAmountTotal').val(commaSeparateNumber(sum, dPlaces));

        var tenderNetTotal = parseFloat(removeCommaSeparateNumber($('#tenderNetTotal').val()));
        if (sum > tenderNetTotal) {
            var change = sum - tenderNetTotal;
            $('#tenderChangeAmount').val(commaSeparateNumber(change, dPlaces));
            $('#tenderDueAmount').val(commaSeparateNumber(0, dPlaces));
        }
        else {
            var due = tenderNetTotal - sum;
            $('#tenderDueAmount').val(commaSeparateNumber(due, dPlaces));
            $('#tenderChangeAmount').val(commaSeparateNumber(0, dPlaces));
        }
    });

    tenderPay.bind('focus blur', function () {
        //$(this).css('border', 'none');
        //$(this).css('background', 'green');
    });

    $('#tenderBtn').click(function () {
        var errorCount = 0;
        var cashAmount = getNumberAndValidate($('#cashAmount').val());
        var cardAmount = getNumberAndValidate($('#cardAmount').val());
        var chequeAmount = getNumberAndValidate($('#chequeAmount').val());
        var giftCardAmount = getNumberAndValidate($('#giftCard').val());
        var creditNoteAmount = getNumberAndValidate($('#creditNote').val());
        var creditSalesAmount = getNumberAndValidate($('#creditSalesAmount').val());
        var customerID = $('#customerID').val();
        var card_chequeTot = cardAmount + chequeAmount + giftCardAmount + creditNoteAmount;
        var netTot = getNumberAndValidate(netTotVal.val());
        var totalPayment = cashAmount + cardAmount + chequeAmount + creditNoteAmount;

        if (totalPayment < netTot && customerID == 0) {
            myAlert('e', 'Payment not equal to Net total.');
            errorCount++;
        }
        if (card_chequeTot > netTot) {
            myAlert('e', 'Card and Cheque Amount sum can not be greater than net total.');
            errorCount++;
        }

        if ((creditSalesAmount < netTot || creditSalesAmount > netTot) && creditSalesAmount > 0) {
            myAlert('e', 'Payment not equal to Net total.');
            errorCount++;
        }

        if (errorCount == 0) {
            $('#h_cardAmount').val(cardAmount);
            $('#h_cashAmount').val(cashAmount);
            $('#h_chequeAmount').val(chequeAmount);
            $('#h_giftCardAmount').val(giftCardAmount);
            $('#h_creditNoteAmount').val(creditNoteAmount);
            $('#h_creditSalesAmount').val(creditSalesAmount);

            var postData = $('#my_form').serializeArray();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: postData,
                url: "<?php echo site_url('Pos/invoice_create'); ?>",
                success: function (data) {
                    myAlert(data[0], data[1]);
                    $('#isInvoiced').val('');
                    if (data[0] == 's') {
                        var doSysCode_refNo = $('#doSysCode_refNo').text();
                        invoicePrint(data[2], data[3], doSysCode_refNo);
                        searchByKeyword(1);
                    }

                    /*setTimeout(function(){
                     newInvoice(1);
                     }, 500);*/


                }, error: function () {
                    myAlert('e', 'Error in invoice create process.')
                }
            });
        }
    });

    $('#customerSearch').keyup(function (e) {
        if (e.keyCode != 9 && e.keyCode != 40 && e.keyCode != 38 && e.keyCode != 13 && e.keyCode != 27 && e.keyCode != 46) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'key': this.value},
                url: "<?php echo site_url('Pos/customer_search'); ?>",
                success: function (data) {
                    $("#customerSearchTB > tbody").empty();
                    var appData = '';

                    if (data.length > 0) {
                        $.each(data, function (i, val) {
                            appData += '<tr class="validTR" data-id="' + val['customerAutoID'] + '"  data-currency="' + val['customerCurrency'] + '">';
                            appData += '<td></td><td>' + val['customerSystemCode'] + '</td><td>' + val['secondaryCode'] + '</td><td>' + val['customerName'] + '</td><td>' + val['customerTelephone'] + '</td></tr>';
                        });
                        customerSearchTB.append(appData);
                    }
                    else {
                        customerSearchTB.append('<tr><td colspan="3">No data</td></tr>');
                    }

                }, error: function () {
                    myAlert('e', 'Error in Customer search.')
                }
            });
        }
    });

    function selectEmployee() {
        $('#customerID').val(selectedCusArray[0]);
        //$('#_trCurrency').val(selectedCusArray[1]);
        $('#customerCode').val(selectedCusArray[2]);
        $('.customerSpan').text(selectedCusArray[3]);
        var cus = $('#customerID').val();
        if (cus != 0) {
            $('#creditsalesfield').removeClass('hidden');
        } else {
            $('#creditsalesfield').addClass('hidden');
        }
        $('#customer_modal').modal('hide');
        y = 0;
        //$('.trCurrencySpan').text(selectedCusArray[1]);
    }

    $(document).on('click', '#customerSearchTB tr', function () {
        if ($(this).hasClass('validTR') == true) {
            $('#customerSearchTB tr').removeClass('selectedTR');
            $(this).addClass('selectedTR');

            var dataID = $(this).attr('data-id');
            var dataCurrency = $(this).attr('data-currency');
            var dataCode = $.trim($(this).find('td:eq(1)').text());
            var dataName = $.trim($(this).find('td:eq(3)').text());

            dataCurrency = ( dataCode == 'CASH') ? '<?php echo $tr_currency; ?>' : dataCurrency;

            selectedCusArray = [dataID, dataCurrency, dataCode, dataName];
        }
        else {
            myAlert('w', 'Please select a valid customer')
        }
    });

    $(document).on('click', '#invoiceSearchTB tr', function () {
        if ($(this).hasClass('headerTR') == false) {
            $('#invoiceSearchTB tr').removeClass('selectedTR');
            $(this).addClass('selectedTR');


        }
    });

    $(document).on('click', '#itemDisplayTB tr', function () {
        qty_adjustProcess();

        if ($(this).hasClass('header_tr') == false) {
            $(this).addClass('selectedTR');

            var dataID = $(this).attr('data-id');
            var dataCurrency = $(this).attr('data-currency');
            var dataCode = $.trim($(this).find('td:eq(1)').text());
            var dataName = $.trim($(this).find('td:eq(2)').text());
            var img = $.trim($(this).find('td:eq(10) .item-image-hidden').val());

            $('#item-image').attr('src', img);
            selectedItemArray = [dataID, dataCurrency, dataCode, dataName];
        }
    });

    $(document).on('click', '#creditNoteTB tr', function () {
        if ($(this).hasClass('validTR') == true) {
            $('#creditNoteTB tr').removeClass('selectedTR');
            $(this).addClass('selectedTR');
        }
        else {
            myAlert('w', 'Please select a valid note')
        }
    });

    function selectInvoice() {
        var invTB = $('#invoiceSearchTB tr.selectedTR');
        var selectedRowCount = 0;
        $.each(invTB, function () {
            selectedRowCount++
        });
        if (selectedRowCount == 1) {
            var selectedInv = $('#invoiceSearchTB').find('tr.selectedTR').attr('data-id');
            $("#itemDisplayTB > tbody").empty();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'holdID': selectedInv},
                url: "<?php echo site_url('Pos/load_holdInv'); ?>",
                success: function (data) {
                    var masterData = data[0];
                    var invItems = data[1];


                    $('#customerID').val(masterData['customerID']);
                    $('#_trCurrency').val(masterData['transactionCurrency']);
                    $('#customerCode').val(masterData['customerCode']);
                    $('.customerSpan').text(masterData['cusName']);
                    $('#isInvoiced').val(masterData['invoiceID']);
                    itemCount.html(0);
                    exceededItemArray = [];

                    var appendDet = '';
                    var isCurrentQtyExceed = 0;
                    $.each(invItems, function (i, elm) {
                        var qty = elm['qty'];
                        var qtys = elm['qty'];
                        var convertUOM = elm['conversionRateUOM'];
                        var currentStk = elm['currentStk'] * convertUOM;

                        if (qty > currentStk) {
                            exceededItemArray[isCurrentQtyExceed] = [];
                            exceededItemArray[isCurrentQtyExceed]['itemCode'] = elm['itemSystemCode'];
                            exceededItemArray[isCurrentQtyExceed]['requestItem'] = elm['itemDescription'];
                            exceededItemArray[isCurrentQtyExceed]['UMO'] = elm['unitOfMeasure'];
                            exceededItemArray[isCurrentQtyExceed]['requestQty'] = qty;
                            exceededItemArray[isCurrentQtyExceed]['availableQty'] = currentStk;
                            isCurrentQtyExceed++;
                        }


                        if (qty > currentStk) {
                            qty = currentStk
                        }
                        var amount = (qtys * elm['price']);
                        var disCountPer = getNumberAndValidate(elm['discountPer']);
                        var discountVal = (disCountPer > 0) ? amount * 0.01 * disCountPer : 0;
                        var total = amount - disCountPer;

                        appendDet += '<tr><td></td>';
                        appendDet += '<td>' + elm['itemSystemCode'] + '</td>';
                        appendDet += '<td>' + elm['itemDescription'] + '</td>';
                        appendDet += '<td>' + elm['unitOfMeasure'] + '</td>';
                        appendDet += '<td align="right">' + qtys + '</td>';
                        appendDet += '<td align="right">' + commaSeparateNumber(elm['price'], dPlaces) + '</td>';
                        appendDet += '<td align="right">' + commaSeparateNumber(amount, dPlaces) + '</td>';
                        appendDet += '<td align="right">' + commaSeparateNumber(elm['discountPer'], dPlaces) + '</td>';
                        appendDet += '<td align="right">' + commaSeparateNumber(discountVal, dPlaces) + '</td>';
                        appendDet += '<td align="right">' + commaSeparateNumber(total, dPlaces) + '</td>';
                        appendDet += '<td align="right">';
                        appendDet += '<span class="glyphicon glyphicon-pencil editRow" style="color: #3c8dbc; position: static"></span> | ';
                        appendDet += '<span class="glyphicon glyphicon-trash deleteRow" style="color:rgb(209, 91, 71); position: static"></span>';
                        appendDet += '<input type="hidden" name="itemID[]" class="itemID" value="' + elm['itemAutoID'] + '" >';
                        appendDet += '<input type="hidden" name="itemName[]" class="itemName" value="' + elm['itemDescription'] + '" >';
                        appendDet += '<input type="hidden" name="itemUOM[]" class="itemUOM" value="' + elm['unitOfMeasure'] + '" >';
                        appendDet += '<input type="hidden" name="itemQty[]" class="itemQty" value="' + qtys + '" >';
                        appendDet += '<input type="hidden" name="itemPrice[]" class="itemPrice" value="' + elm['price'] + '" >';
                        appendDet += '<input type="hidden" name="itemDis[]" class="itemDis" value="' + disCountPer + '" >';
                        appendDet += '<input type="hidden" class="thisCurrentStk" value="' + currentStk + '" >';
                        appendDet += '<input type="hidden" class="discountAmount" value="' + discountVal + '" >';
                        appendDet += '<input type="hidden" class="totalAmount" value="' + amount + '" >';
                        appendDet += '<input type="hidden" class="netAmount" value="' + total + '" >';
                        appendDet += '<input type="hidden" class="item-image-hidden" value="<?php echo base_url('images/item/');?>/' + elm['itemImage'] + '" >';
                        appendDet += '</td></tr>';
                        itemCount.html(parseInt($('.itemCount:first').text()) + 1);
                        itemUOM.empty();
                    });


                    itemDisplayTB.append(appendDet);
                    itemAdd_sub_function();

                    setTimeout(function () {
                        recall_modal.modal('hide');
                    }, 500);
                    calculateDiscount_byPercentage();


                }, error: function () {
                    myAlert('e', 'Error in loading hold invoice details .');
                }
            });
        }
        else {
            myAlert('e', 'Please Select a Invoice to recall.');
        }
    }

    itemQty.keyup(function () {
        var thisQty = $.trim($(this).val());
        var currentQty = parseFloat($.trim(currentStock.val()));
        thisQty = (thisQty != '') ? parseFloat(thisQty) : parseFloat(0);


    });

    $(document).on('keyup', '#qtyAdj', function (e) {
        if (e.keyCode == 13) {
            qty_adjustProcess();
        }
        else {
            var thisVal = $.trim($(this).val());
            var availableStk = parseFloat($.trim($(this).attr('data-stock')));
            thisVal = parseFloat(thisVal);

            /*if($.isNumeric(thisVal) && availableStk < thisVal ){
             $(this).val('');
             myAlert('e', 'Available Stock is only '+availableStk);
             }*/
        }
    });

    $(document).on('onChange', '#qtyAdj', function (e) {
        qty_adjustProcess();
    });

    function qty_adjustProcess() {
        var lastSelectedTR = $('#itemDisplayTB tr.selectedTR');

        //validate if a qty adjustment not finished
        if (lastSelectedTR.find('td:eq(4) #qtyAdj').length) {

            var qtyAdj = $('#qtyAdj');
            var lastAdjQty = $.trim(qtyAdj.val());
            lastAdjQty = ( lastAdjQty == '' || lastAdjQty == '.' ) ? 0 : lastAdjQty;
            lastAdjQty = parseFloat(lastAdjQty);
            //console.log('lastAdjQty:' + lastAdjQty);

            qtyAdj.closest('td').css('padding', '5px');
            qtyAdj.remove();

            if (lastAdjQty != 0) {
                lastSelectedTR.find('td:eq(4)').html(lastAdjQty);
                lastSelectedTR.find('td:eq(10) .itemQty').val(lastAdjQty);
                var itemPrice = parseFloat(lastSelectedTR.find('td:eq(10) .itemPrice').val());
                var itemDisPer = $.trim(lastSelectedTR.find('td:eq(10) .itemDis').val());
                itemDisPer = ( itemDisPer == '' ) ? 0 : parseFloat(itemDisPer);

                var amount = (itemPrice * lastAdjQty);
                var total = amount;

                lastSelectedTR.find('td:eq(6)').text(commaSeparateNumber(amount, dPlaces));


                if (itemDisPer != 0) {
                    var discountAmount = amount * itemDisPer * 0.01;
                    lastSelectedTR.find('td:eq(8)').text(commaSeparateNumber(discountAmount, dPlaces));
                    lastSelectedTR.find('td:eq(9)').text(commaSeparateNumber((total - discountAmount), dPlaces));

                    lastSelectedTR.find('td:eq(10) .discountAmount').val((discountAmount));
                    lastSelectedTR.find('td:eq(10) .totalAmount').val((total));
                    lastSelectedTR.find('td:eq(10) .netAmount').val((total - discountAmount));

                }
                else {
                    lastSelectedTR.find('td:eq(9)').text(commaSeparateNumber(total, dPlaces));
                    lastSelectedTR.find('td:eq(10) .totalAmount').val(total);
                    lastSelectedTR.find('td:eq(10) .netAmount').val(total);
                }

            }
            else {
                lastSelectedTR.find('td:eq(4)').html(qtyAdj.attr('data-value'));
                lastSelectedTR.find('td:eq(10) .itemQty').val(qtyAdj.attr('data-value'));
            }

            getTot();
            getDiscountTot();
            getNetTot();
            calculateDiscount_byPercentage();

        }

        $('#itemDisplayTB tr').removeClass('selectedTR');
    }

    function save_moreCardDetails() {
        var hCardAmount = getNumberAndValidate($('#cardAmount_cardDet').val());
        var bankID = $.trim($('#bank').val());
        if (hCardAmount != 0 && bankID != '') {
            $('#cardAmount').val(hCardAmount);
            $('#h_cardAmount').val(hCardAmount);
            _referenceNO.val($.trim($('#referenceNO').val()));
            _cardNumber.val($.trim($('#cardNumber').val()));
            _bank.val(bankID);
            tenderPay.keyup();
            cardDet_modal.modal('hide');
        }
        else if (hCardAmount == 0) {
            myAlert('e', 'Please enter a valid card amount');
        }
        else if (bankID == '') {
            myAlert('e', 'Please select a bank');
        }

        /**var postData = $('#cardPayment_form').serializeArray();

         $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php //echo site_url('Pos/invoice_cardDetail'); ?>",
            success: function (data) {
                myAlert(data[0], data[1]);

                if( data[0] == 's' ){
                    var invCode = $.trim($('#invoiceNo').text());
                    invoicePrint(data[2],invCode);
                    //newInvoice(1);
                }
                /!*setTimeout(function(){
                 newInvoice(1);
                 }, 500);*!/

            }, error: function () {
                myAlert('e', 'Error in saving card details.')
            }
        });*/
    }

    function save_moreChequeDetails() {
        var hChequeAmount = getNumberAndValidate($('#chequeAmount_cheqDet').val());
        var chequeNumber = $.trim($('#chequeNumber').val());
        if (hChequeAmount != 0 && chequeNumber != '') {
            $('#chequeAmount').val(hChequeAmount);
            $('#h_chequeAmount').val(hChequeAmount);
            _chequeCashDate.val($.trim($('#cashDate').val()));
            _chequeNO.val(chequeNumber);
            tenderPay.keyup();
            cheque_modal.modal('hide');
        }
        else if (hChequeAmount == 0) {
            myAlert('e', 'Please enter a valid cheque amount');
        }
        else if (chequeNumber == '') {
            myAlert('e', 'Please enter a valid cheque no');
        }
    }

    /*var qty = itemQty.val();
     var amount = qty * salesPrice.val();
     var tot = parseFloat(totVal.val())+ amount;
     totVal.val(tot);
     totSpan.text(commaSeparateNumber(tot, dPlaces));

     var discAmount = amount * disPer.val() * 0.01;
     var disTotal = parseFloat(discVal.val()) + discAmount;
     discSpan.text(commaSeparateNumber(disTotal, dPlaces));
     discVal.val(disTotal);

     var thisNetTot = (amount - discAmount);
     var netTot = parseFloat(netTotVal.val()) + thisNetTot;
     netTotSpan.text(commaSeparateNumber(netTot, dPlaces));
     netTotVal.val(netTot);*/

    function getNumberAndValidate(thisVal, dPlace=2) {
        thisVal = $.trim(thisVal);
        thisVal = removeCommaSeparateNumber(thisVal);
        thisVal = thisVal.toFixed(dPlace);
        if ($.isNumeric(thisVal)) {
            return parseFloat(thisVal);
        }
        else {
            return parseFloat(0);
        }
    }

    $('.searchData').click(function () {
        var thisID = $(this).attr('id');
        if (thisID == 'creditNote') {
            $('#creditNote_modal').modal({backdrop: 'static'});
            $("#creditNoteTB > tbody").empty();
            creditNoteTB.append('<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>');
        }
        else if (thisID == 'cardAmount') {
            cardDet_modal.modal({backdrop: 'static'});
            setTimeout(function () {
                $('#cardAmount_cardDet').focus();
            }, 500);
        }
        else if (thisID == 'giftCard') {
            $('#giftCard_modal').modal({backdrop: 'static'});
        }
        else if (thisID == 'chequeAmount') {
            $('#cheque_modal').modal({backdrop: 'static'});
        }

    });

    function session_close() {
        $("#isStart").val(0);
        $(".tillModal_close").show();
        $("#tillModal_title").text("Day End");
        $("#tillSave_Btn").attr("onclick", "shift_close()");
        till_modal.modal({backdrop: "static"});
    }

    function invoicePrint(invID, invCode, doSysCode_refNo) {

        //window.open("<?php echo site_url('Pos/invoice_print'); ?>/"+invID+"/"+invCode, "", "width=700,height=400");
        $.ajax({
            async: true,
            type: 'post',
            html: 'json',
            data: {'doSysCode_refNo': doSysCode_refNo},
            url: "<?php echo site_url('Pos/invoice_print'); ?>/" + invID + "/" + invCode,
            success: function (data) {
                $('#print_template').modal({backdrop: 'static'});
                $('#print_content').html(data);
            }, error: function (xhr) {
                myAlert('e', 'Error in print call. ' + xhr.status + ': ' + xhr.statusText)
            }
        });
    }

    $('#creditNote_search').keyup(function () {
        var key = $(this).val();
        var letterCount = key.length;

        if (letterCount > 1) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'key': this.value},
                url: "<?php echo site_url('Pos/creditNote_search'); ?>",

                success: function (data) {
                    $("#creditNoteTB > tbody").empty();
                    var appData = '';

                    if (data.length > 0) {
                        $.each(data, function (i, val) {
                            //, documentSystemCode, salesReturnDate, netTotal
                            appData += '<tr class="validTR" data-id="' + val['salesReturnID'] + '"  data-amount="' + val['netTotal'] + '">';
                            appData += '<td>' + (i + 1) + '</td><td>' + val['documentSystemCode'] + '</td><td>' + val['salesReturnDate'] + '</td>';
                            appData += '<td align="right">' + commaSeparateNumber(val['netTotal'], dPlaces) + '</td></tr>';
                        });
                        creditNoteTB.append(appData);
                    }
                    else {
                        creditNoteTB.append('<tr><td colspan="4">No data</td></tr>');
                    }

                }, error: function () {
                    myAlert('e', 'Error in Customer search.')
                }
            });
        }
    });

    function selectCreditNote() {
        var returnID = creditNoteTB.find('tr.selectedTR').attr('data-id');
        var returnAmount = creditNoteTB.find('tr.selectedTR').attr('data-amount');

        $('#creditNote-invID').val(returnID);
        //alert(commaSeparateNumber(returnAmount, dPlaces));
        $('#creditNote').val(commaSeparateNumber(returnAmount, dPlaces));
        $('.CreditNoteAmnt').val(commaSeparateNumber(returnAmount, dPlaces));
        calculatePaidAmount($('.CreditNoteAmnt').val());
        tenderPay.keyup();
    }

    $('#clear-credit-amount').click(function () {
        $('#creditNote-invID').val('');
        $('#creditNote').val('');
        tenderPay.keyup();
    });

    $('#clear-card-amount').click(function () {
        $('#cardAmount_cardDet').val('');
        $('#cardAmount').val('');
        $('#h_cardAmount').val('');
        $('#referenceNO').val('');
        $('#cardNumber').val('');


        _referenceNO.val('');
        _cardNumber.val('');
        _bank.val('');

        tenderPay.keyup();
    });

    $('#clear-cheque-amount').click(function () {
        $('#cardAmount_cardDet').val('');
        $('#chequeAmount').val('');
        $('#h_chequeAmount').val('');
        $('#referenceNO').val('');
        $('#cardNumber').val('');


        _chequeNO.val('');
        _chequeCashDate.val('');
        _bank.val('');

        tenderPay.keyup();
    });

    function print_pos_report() {
        $.print("#print_content");
        return false;
    }

    $('#invoiceCode').autocomplete({
        serviceUrl: '<?php echo site_url();?>/Pos/invoice_searchLiveSearch/',
        onSelect: function (suggestion) {

        }
    });
</script>
<script type="text/javascript" src="<?php echo base_url('plugins/keyboard-short-cut/shortcut.js'); ?>"></script>
<script src="<?php echo base_url('plugins/printJS/jQuery.print.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('plugins/numPadmaster/jquery.numpad.js') ?>" type="text/javascript"></script>
<script type="text/javascript">
    function initNumPad() {
        $('.numpad').unbind();
        $('.numpad').numpad();
    }
    var y = 0;
    var st = 0;
    shortcut.add("F1", function () {
        if (typeof $(pos_payments_modal).data()['bs.modal'] !== "undefined" && $(pos_payments_modal).data()['bs.modal'].isShown) {
            submit_pos_payments();
        } else {
            open_pos_payments_modal();
        }
    });

    shortcut.add("F4", function () {
        searchItem_modal();
    });

    shortcut.add("F9", function () {
        $("#itemSearch").focus();
    });

    shortcut.add("F2", function () {
        hold_invoice();
    });

    shortcut.add("F3", function () {
        checkifItemExsistReturn();
    });

    shortcut.add("F4", function () {
        var fnTr = $('#itemDisplayTB tr.selectedTR');
        console.log(fnTr.find('td:eq(1)').html());

    });

    shortcut.add("F5", function () {
        newInvoice();
    });

    shortcut.add("F6", function () {
        open_customer_modal();
    });

    shortcut.add("F7", function () {
        checkifItemExsist();
    });

    shortcut.add("F8", function () {
        adjust_qty();
    });

    shortcut.add("tab", function (e) {
        if (typeof $(customer_modal).data()['bs.modal'] !== "undefined" && $(customer_modal).data()['bs.modal'].isShown) {
            $('#customerSearchTB tbody').find('tr:first').removeClass('selectedTR');
            $('#customerSearchTB tbody').find('tr:first').addClass('selectedTR');

            var dataID = $('#customerSearchTB tbody').find('tr:first').attr('data-id');
            var dataCurrency = $('#customerSearchTB tbody').find('tr:first').attr('data-currency');
            var dataCode = $.trim($('#customerSearchTB tbody').find('tr:first').find('td:eq(1)').text());
            var dataName = $.trim($('#customerSearchTB tbody').find('tr:first').find('td:eq(3)').text());
            dataCurrency = ( dataCode == 'CASH') ? '<?php echo $tr_currency; ?>' : dataCurrency;
            selectedCusArray = [dataID, dataCurrency, dataCode, dataName];
        } else {
            e.preventDefault();
        }
    });


    shortcut.add("F10", function () {
        if (typeof $(customer_modal).data()['bs.modal'] !== "undefined" && $(customer_modal).data()['bs.modal'].isShown) {
            $("#customerSearch").focus();
        }
    });

    shortcut.add("down", function (e) {
        if (typeof $(modal_qty_box).data()['bs.modal'] !== "undefined" && $(modal_qty_box).data()['bs.modal'].isShown) {
            e.preventDefault();
        } else if (typeof $(customer_modal).data()['bs.modal'] !== "undefined" && $(customer_modal).data()['bs.modal'].isShown) {
            goDownTable('customerSearchTB');
        } else if (typeof $(modal_search_item).data()['bs.modal'] !== "undefined" && $(modal_search_item).data()['bs.modal'].isShown) {
            goDownTable('posg_search_item_modal');
        } else if (typeof $(pos_payments_modal).data()['bs.modal'] !== "undefined" && $(pos_payments_modal).data()['bs.modal'].isShown) {
            goDownTable('posg_payment_modal_table');
        } else if (typeof $(recall_modal).data()['bs.modal'] !== "undefined" && $(recall_modal).data()['bs.modal'].isShown) {
            goDownTable('invoiceSearchTB');
        } else {
            goDownTable('itemDisplayTB');
        }
    });

    shortcut.add("up", function (e) {
        if (typeof $(modal_qty_box).data()['bs.modal'] !== "undefined" && $(modal_qty_box).data()['bs.modal'].isShown) {
            e.preventDefault();
        } else if (typeof $(customer_modal).data()['bs.modal'] !== "undefined" && $(customer_modal).data()['bs.modal'].isShown) {
            goUpTable('customerSearchTB');
        } else if (typeof $(modal_search_item).data()['bs.modal'] !== "undefined" && $(modal_search_item).data()['bs.modal'].isShown) {
            goUpTable('posg_search_item_modal');
        } else if (typeof $(pos_payments_modal).data()['bs.modal'] !== "undefined" && $(pos_payments_modal).data()['bs.modal'].isShown) {
            goUpTable('posg_payment_modal_table');
        } else if (typeof $(recall_modal).data()['bs.modal'] !== "undefined" && $(recall_modal).data()['bs.modal'].isShown) {
            goUpTable('invoiceSearchTB');
        } else {
            /*Customer */
            goUpTable('itemDisplayTB');
        }
    });


    shortcut.add("ctrl+Q", function () {
        $("#disPer").focus();
    });

    shortcut.add("ctrl+D", function () {
        $("#disAmount").focus();
    });

    shortcut.add("ctrl+E", function () {
        $("#salesPrice").focus();
    });

    shortcut.add("ctrl+F", function (e) {
        if (typeof $(modal_search_item).data()['bs.modal'] !== "undefined" && $(modal_search_item).data()['bs.modal'].isShown) {
            $("#searchKeyword").focus();
        } else {
            e.preventDefault();
        }
    });


    function goUpTable(id) {
        var rowIndex = $('#' + id + ' tbody').find('tr.selectedTR').index();
        var rowcount = document.getElementById(id).rows.length;

        if (rowIndex == 0) {
            var count = rowcount - 2;
            $('#' + id + ' tbody').find('tr:eq(' + rowIndex + ')').removeClass('selectedTR');
            $('#' + id + ' tbody').find('tr:eq(' + count + ')').addClass('selectedTR');

            var dataID = $('#' + id + ' tbody').find('tr:eq(' + count + ')').attr('data-id');
            var dataCurrency = $('#' + id + ' tbody').find('tr:eq(' + count + ')').attr('data-currency');
            var dataCode = $.trim($('#' + id + ' tbody').find('tr:eq(' + count + ')').find('td:eq(1)').text());
            var dataName = $.trim($('#' + id + ' tbody').find('tr:eq(' + count + ')').find('td:eq(3)').text());
            dataCurrency = ( dataCode == 'CASH') ? '<?php echo $tr_currency; ?>' : dataCurrency;
            selectedCusArray = [dataID, dataCurrency, dataCode, dataName];
            //$(".fixHeader_Div").scrollTop(200000);
        } else {
            var x = 26;
            var index = rowIndex - 1;
            $('#' + id + ' tbody').find('tr:eq(' + rowIndex + ')').removeClass('selectedTR');
            $('#' + id + ' tbody').find('tr:eq(' + index + ')').addClass('selectedTR');

            var dataID = $('#' + id + ' tbody').find('tr:eq(' + index + ')').attr('data-id');
            var dataCurrency = $('#' + id + ' tbody').find('tr:eq(' + index + ')').attr('data-currency');
            var dataCode = $.trim($('#' + id + ' tbody').find('tr:eq(' + index + ')').find('td:eq(1)').text());
            var dataName = $.trim($('#' + id + ' tbody').find('tr:eq(' + index + ')').find('td:eq(3)').text());
            dataCurrency = ( dataCode == 'CASH') ? '<?php echo $tr_currency; ?>' : dataCurrency;
            selectedCusArray = [dataID, dataCurrency, dataCode, dataName];
            st = x + st;
            y = y - 26;
            //var scroll=index*20;
            $(".fixHeader_Div").scrollTop(-st);
        }
    }

    function goDownTable(id) {
        var rowIndex = $('#' + id + ' tbody').find('tr.selectedTR').index();
        var rowcount = document.getElementById(id).rows.length;
        if ((rowIndex + 2) == rowcount) {
            $('#' + id + ' tbody').find('tr:eq(' + rowIndex + ')').removeClass('selectedTR');
            $('#' + id + ' tbody').find('tr:first').addClass('selectedTR');

            var dataID = $('#' + id + ' tbody').find('tr:first').attr('data-id');
            var dataCurrency = $('#' + id + ' tbody').find('tr:first').attr('data-currency');
            var dataCode = $.trim($('#' + id + ' tbody').find('tr:first').find('td:eq(1)').text());
            var dataName = $.trim($('#' + id + ' tbody').find('tr:first').find('td:eq(3)').text());
            dataCurrency = ( dataCode == 'CASH') ? '<?php echo $tr_currency; ?>' : dataCurrency;
            selectedCusArray = [dataID, dataCurrency, dataCode, dataName];
            //$(".fixHeader_Div").scrollTop(0);
        } else {
            var x = 26;

            var index = rowIndex + 1;
            $('#' + id + ' tbody').find('tr:eq(' + rowIndex + ')').removeClass('selectedTR');
            $('#' + id + ' tbody').find('tr:eq(' + index + ')').addClass('selectedTR');

            var dataID = $('#' + id + ' tbody').find('tr:eq(' + index + ')').attr('data-id');
            var dataCurrency = $('#' + id + ' tbody').find('tr:eq(' + index + ')').attr('data-currency');
            var dataCode = $.trim($('#' + id + ' tbody').find('tr:eq(' + index + ')').find('td:eq(1)').text());
            var dataName = $.trim($('#' + id + ' tbody').find('tr:eq(' + index + ')').find('td:eq(3)').text());
            dataCurrency = ( dataCode == 'CASH') ? '<?php echo $tr_currency; ?>' : dataCurrency;
            selectedCusArray = [dataID, dataCurrency, dataCode, dataName];
            y = x + y;
            st = st - 26;
            //var scroll=index*20;
            $(".fixHeader_Div").scrollTop(y);
        }
    }

    function deleteRow() {
        $('#itemDisplayTB .selectedTR').remove();
        getTot();
        getDiscountTot();
        getNetTot();
        calculateDiscount_byPercentage();
        trRemove();
    }


    shortcut.add("enter", function (e) {
        if (typeof $(modal_qty_box).data()['bs.modal'] !== "undefined" && $(modal_qty_box).data()['bs.modal'].isShown) {
            processInvoiceList();
        } else if (typeof $(customer_modal).data()['bs.modal'] !== "undefined" && $(customer_modal).data()['bs.modal'].isShown) {
            selectEmployee();
        } else if (typeof $(modal_search_item).data()['bs.modal'] !== "undefined" && $(modal_search_item).data()['bs.modal'].isShown) {
            $("#itemSearchResultTblBody .selectedTR").find('button').click()
        } else if (typeof $(pos_payments_modal).data()['bs.modal'] !== "undefined" && $(pos_payments_modal).data()['bs.modal'].isShown) {
            $('#posg_payment_modal_table .selectedTR').find('button').click();
        } else if (typeof $(recall_modal).data()['bs.modal'] !== "undefined" && $(recall_modal).data()['bs.modal'].isShown) {
            selectInvoice();
        } else {
            e.preventDefault();
        }
    });

    shortcut.add("esc", function (e) {
        if (typeof $(modal_qty_box).data()['bs.modal'] !== "undefined" && $(modal_qty_box).data()['bs.modal'].isShown) {
            
            debugger;
            var test ='';
        } else {
            debugger;
            e.preventDefault();
        }
    });

    shortcut.add("delete", function () {
        if (typeof $(customer_modal).data()['bs.modal'] !== "undefined" && $(customer_modal).data()['bs.modal'].isShown) {
            defaultCustomer();
        } else {
            deleteRow()
        }
    });

    shortcut.add("F11", function () {
        if (typeof $(pos_payments_modal).data()['bs.modal'] !== "undefined" && $(pos_payments_modal).data()['bs.modal'].isShown) {
        } else {
            $('#gen_disc_percentage').select();
        }

    });

    shortcut.add("F12", function () {
        if (typeof $(customer_modal).data()['bs.modal'] !== "undefined" && $(customer_modal).data()['bs.modal'].isShown) {
        } else {
            $('#itemDisplayTB .selectedTR .editRow').click()
        }

    });

    function hold_invoice() {
        var tot = getTot();
        if (tot > 0) {
            swal({
                    title: "Are you sure ?",
                    text: "You want to hold this invoice!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                },
                function () {
                    var postData = $('#my_form').serializeArray();
                    postData.push({'name': 'customerID', 'value': $('#customerID').val()});
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: postData,
                        url: "<?php echo site_url('Pos/invoice_hold'); ?>",
                        success: function (data) {
                            myAlert(data[0], data[1]);
                            $('#isInvoiced').val('');
                            if (data[0] == 's') {
                                var zero = 0;
                                newInvoice(1);
                                $('#totSpan').html(zero.toFixed(dPlaces));
                                $('#netTotSpan').html(zero.toFixed(dPlaces));
                                $("#netTot_after_g_disc_div").html(zero.toFixed(dPlaces));
                                $("#netTot_after_g_disc").val(zero);
                                $("#gen_disc_percentage").val(zero);
                            }


                        }, error: function () {
                            myAlert('e', 'Error in hold invoice process.')
                        }
                    });
                }
            );
        }
        else {
            myAlert('e', 'There is no item to proceed');
        }
    }

    function newInvoice(isFromInvCreate=null) {
        var tot = getTot();
        if (tot > 0 && isFromInvCreate == null) {
            swal({
                    title: "Are you sure ?",
                    text: "This invoice cannot be recalled !",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                },
                function () {
                    var zero = 0
                    clearForNewInvoice();
                    clearform_pos_receipt();
                    $('#totSpan').text(zero.toFixed(dPlaces));
                    $('#netTotSpan').text(zero.toFixed(dPlaces));
                    $('.itemCount').text(zero);
                    $('#isInvoiced').val('');
                    $("#netTot_after_g_disc_div").html(zero.toFixed(dPlaces));
                    $("#netTot_after_g_disc").val(zero);
                    $("#gen_disc_percentage").val(zero);

                    pos_form[0].reset();
                    $('#itemSearch').attr('readonly', false);
                    reset_generalDiscount();
                    /**window.location = "<?php //echo site_url('Pos/')?>"; */
                }
            );
        }
        else {
            clearForNewInvoice();
            /**window.location = "<?php //echo site_url('Pos/')?>";*/
        }
    }


    function open_customer_modal() {
        customer_modal.modal({backdrop: 'static'});
        setTimeout(function () {
            $('#customerSearch').focus();
        }, 500);
        LoadCustomers();

    }

    function deleteItem() {
        var count = 0;
        var selectedTR = $('#itemDisplayTB tr.selectedTR');


        if (selectedTR.hasClass('header_tr') == false) {

            selectedTR.each(function () {
                count++;
            });

            if (count == 1) {
                selectedTR.remove();
                trRemove();
            }
            else {
                myAlert('e', 'Please select item to remove.');
            }
        }
    }

    function open_tenderModal() {
        //var tot = getTot();
        var tot = $('#netTotSpan').text();
        if (parseInt(tot) > 0) {
            itemAdd_sub_function();
            tenderPay.keyup();
            tender_modal.modal({backdrop: 'static'});

            setTimeout(function () {
                $('#cashAmount').focus();
            }, 500);
        }
        else {
            myAlert('e', 'There is no item to proceed');
        }
    }

    function adjust_qty() {
        $("#temp_number").html('');
        var count = 0;
        var selectedTR = $('#itemDisplayTB tr.selectedTR');

        selectedTR.each(function () {
            count++;
        });

        if (count == 1) {
            var qtyTD = selectedTR.find('td:eq(4)');
            var thisCurrentStk = selectedTR.find('td:eq(10) .thisCurrentStk').val();
            var qty = $.trim(qtyTD.text());
            qtyTD.html('');
            var thisWidth = '80'; //qtyTD.width();

            qtyTD.append('<input type="text" class="qtyAdjTxt number" id="qtyAdj" value="' + qty + '" data-value="' + qty + '" data-stock="' + thisCurrentStk + '" style="width: ' + thisWidth + 'px" autocomplete="off"/>');

            qtyTD.css({
                'width': thisWidth + 'px',
                'padding': '0px',
                'vertical-align': 'middle'
            });
            $('#qtyAdj').focus();
            $('#qtyAdj').select();
            focusOnLastCharacter('qtyAdj');
        }
        else {
            myAlert('e', 'Please select item to proceed.');
        }
    }

    function open_recallHold_modal() {
        $('#recall_search').val('');
        var recall_search = $('#recall_search').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'key': this.value, 'recall_search': recall_search},
            url: "<?php echo site_url('Pos/recall_hold_invoice'); ?>",
            success: function (data) {
                $("#invoiceSearchTB > tbody").empty();
                var appData = '';

                if (data.length > 0) {
                    $.each(data, function (i, val) {

                        appData += '<tr class="validTR" data-id="' + val['invoiceID'] + '"  data-currency="' + val['customerCurrency'] + '">';
                        appData += '<td></td><td>' + val['documentSystemCode'] + '</td><td>' + val['customerCode'] + '</td><td>' + val['cusName'] + '</td>';
                        appData += '<td align="center">' + val['createdDateTime'] + '</td></tr>';
                    });
                    invoiceSearchTB.append(appData);
                }
                else {
                    invoiceSearchTB.append('<tr><td colspan="5">No data</td></tr>');
                }
                calculateDiscount_byPercentage();

            }, error: function () {
                myAlert('e', 'Error in hold invoice laoding.')
            }
        });
        recall_modal.modal({backdrop: 'static'});
    }

    function recall_invoice() {
        $('#return_modal').modal({backdrop: 'static'});
        //$('#credit-to-customer-btn').hide();
        setTimeout(function () {
            $('#invoiceCode').focus();
            focusOnLastCharacter('invoiceCode');
            $('.returnTxt').val('');
            $('#invoiceCode').val('');
            $('#returnInvoiceTB tbody>tr').remove();
            $('#return-item-image').attr("src", "<?php echo base_url('images/item/no-image.png');?>");
        }, 500);
    }


    function invoice_search() {
        $('#returnInvoiceTB tbody>tr').remove();
        var invoiceCode = $('#invoiceCode').val();
        var crToCustomer_Btn = $('#credit-to-customer-btn');

        crToCustomer_Btn.hide();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceCode': invoiceCode},
            beforeSend: function () {
                startLoad();
            },
            url: "<?php echo site_url('Pos/invoice_search'); ?>",
            success: function (data, status, request) {


                console.log(request.getResponseHeader("myHeader"));
                stopLoad();
                if (data[0] != 's') {
                    myAlert(data[0], data[1]);
                }
                else {
                    var invData = data[1];
                    var invDet = data[2];
                    if (invData['customerID'] != 0) {
                        //crToCustomer_Btn.show();
                    }

                    $('#returnCreditNo').val(data[3]);
                    $('#return-invoiceID').val(invData['invoiceID']);
                    $('#return-customerID').val(invData['customerID']);
                    $('#return-cusCode').val(invData['customerCode']);
                    $('#return-cusName').val(invData['cusName']);
                    $('#return-cusBalance').val(commaSeparateNumber(invData['cusBalance'], dPlaces));
                    $('#return-inv-date').val(invData['invoiceDate']);
                    $('#return-date').datepicker('update', invData['invoiceDate']);

                    $('#return-calculate-invTot').val(commaSeparateNumber(invData['netTotal'], dPlaces));
                    $('#return-calculate-invBalance').val(commaSeparateNumber(invData['balanceAmount'], dPlaces));
                    $('#return-credit-total').val(commaSeparateNumber(invData['netTotal'], dPlaces));

                    var refundAmount = ( getNumberAndValidate(invData['netTotal']) - (invData['balanceAmount']) );
                    refundAmount = ( refundAmount > 0 ) ? refundAmount : 0;
                    $('#return-refund').val(commaSeparateNumber(refundAmount, dPlaces));
                    $('#return-refundable-hidden').val(refundAmount);

                    var appData = '';
                    $.each(invDet, function (i, itmData) {
                        var returnPrice = itmData['price'];
                        var returnAmount = returnPrice * itmData['balanceQty'];
                        var returnDicPer = itmData['discountPer'];

                        var discAmount = ( returnDicPer > 0 ) ? (returnAmount * returnDicPer * 0.01) : 0;
                        var lineNetTot = returnAmount - discAmount;
                        var returnQTY = '';
                        var otherData = '';
                        if (itmData['balanceQty'] > 0) {
                            returnQTY = '<input type="text" name="return_QTY[]" id="returnQTY' + i + '" class="returnQTY number" ';
                            returnQTY += 'data-maxqty="' + itmData['balanceQty'] + '" data-uom="' + itmData['unitOfMeasure'] + '" ';
                            returnQTY += 'value="' + itmData['balanceQty'] + '" style="width: 65px; color:#000; padding:0px 5px; height: 16px">';

                            otherData += '<input type="hidden" name="invoiceDetailsID[]" value="' + itmData['invoiceDetailsID'] + '" >';
                            otherData += '<input type="hidden" name="itemID[]" value="' + itmData['itemAutoID'] + '" >';
                            otherData += '<input type="hidden" name="itemName[]" value="' + itmData['itemDescription'] + '" >';
                            otherData += '<input type="hidden" name="itemUOM[]" value="' + itmData['unitOfMeasure'] + '" >';
                            otherData += '<input type="hidden" name="itemQty[]" class="itemQty" value="' + itmData['qty'] + '" >';
                            otherData += '<input type="hidden" name="itemMaxQty[]" class="itemMaxQty" value="' + itmData['balanceQty'] + '" >';
                            otherData += '<input type="hidden" name="itemPrice[]" class="itemPrice" value="' + returnPrice + '" >';
                            otherData += '<input type="hidden" name="itemDis[]" class="itemDis" value="' + returnDicPer + '" >';
                            otherData += '<input type="hidden" class="return-discountAmount" value="' + discAmount + '" >';
                            otherData += '<input type="hidden" class="return-totalAmount" value="' + returnAmount + '" >';
                            otherData += '<input type="hidden" class="return-netAmount" value="' + lineNetTot + '" >';

                            appData = '<tr>';
                            appData += '<td align="right"></td>';
                            appData += '<td>' + itmData['itemSystemCode'] + '</td>';
                            appData += '<td>' + itmData['itemDescription'] + '</td>';
                            appData += '<td>' + itmData['defaultUOM'] + '</td>';
                            appData += '<td align="right">' + itmData['balanceQty'] + '</td>';
                            appData += '<td align="right" style="width: 70px">' + returnQTY + '</td>';
                            appData += '<td align="right">' + commaSeparateNumber(returnPrice, dPlaces) + '</td>';
                            appData += '<td align="right">' + commaSeparateNumber(returnAmount, dPlaces) + '</td>';
                            appData += '<td align="right">' + getNumberAndValidate(returnDicPer) + '</td>';
                            appData += '<td align="right">' + commaSeparateNumber(discAmount, dPlaces) + '</td>';
                            appData += '<td align="right">' + commaSeparateNumber(lineNetTot, dPlaces) + '</td>';
                            appData += '<td align="right">';
                            appData += '<span class="glyphicon glyphicon-trash deleteRow-return" style="color:rgb(209, 91, 71); position: static"></span>';
                            appData += otherData;
                            appData += '<input type="hidden" class="return-item-image-hidden" value="<?php echo base_url('images/item/');?>/' + itmData['itemImage'] + '" >';
                            appData += '</td>';
                            appData += '</tr>';
                            returnInvoiceTB.append(appData);
                        }


                    });


                    getReturnSubTotal();
                    getReturnNetTotal();
                    getReturnDiscTotal();
                }

            }, error: function () {
                stopLoad();
                myAlert('e', 'Error in invoice calling loading.')
            }
        });
    }

    $(document).on('click', '#returnInvoiceTB tr', function () {
        if ($(this).hasClass('header_tr') == false) {
            $('#returnInvoiceTB tr').removeClass('selectedTR');
            $(this).addClass('selectedTR');
            var r_image = $.trim($(this).find('td:eq(11) .return-item-image-hidden').val());
            $('#return-item-image').attr("src", r_image);
        }
    });

    $(document).on('keyup', '.returnQTY', function () {

        var maxQty = parseFloat($(this).attr('data-maxqty'));
        var qty = getNumberAndValidate($(this).val());

        if (qty > maxQty) {
            var umo = $.trim($(this).attr('data-uom'));
            $(this).val('');
            qty = 0;
            myAlert('w', 'Quantity cannot be exceed than the balance quantity<br>[ ' + maxQty + ' ' + umo + ' ]');
        }

        var itemPrice = getNumberAndValidate($(this).closest('tr').find('td:eq(11) .itemPrice').val());
        var returnDisPer = getNumberAndValidate($(this).closest('tr').find('td:eq(11) .itemDis').val());
        var returnDisAmount = 0;
        var thisSubTot = itemPrice * qty;
        var thisNetTot = thisSubTot;

        if (returnDisPer > 0) {
            returnDisAmount = (thisSubTot * returnDisPer * 0.01 );
            thisNetTot = thisSubTot - returnDisAmount;
        }

        $(this).closest('tr').find('td:eq(7)').text(commaSeparateNumber(thisSubTot, dPlaces));
        $(this).closest('tr').find('td:eq(9)').text(commaSeparateNumber(returnDisAmount, dPlaces));
        $(this).closest('tr').find('td:eq(10)').text(commaSeparateNumber(thisNetTot, dPlaces));

        $(this).closest('tr').find('td:eq(11) .return-discountAmount').val(returnDisAmount);
        $(this).closest('tr').find('td:eq(11) .return-totalAmount').val(thisSubTot);
        $(this).closest('tr').find('td:eq(11) .return-netAmount').val(thisNetTot);

        getReturnSubTotal();
        getReturnNetTotal();
        getReturnDiscTotal();
    });

    function getReturnNetTotal() {
        var returnNetTot = 0;
        $('.return-netAmount').each(function () {
            var thisVal = getNumberAndValidate($(this).val());
            returnNetTot += thisVal;
        });

        $('#return-credit-total').val(commaSeparateNumber(returnNetTot, dPlaces));
        var return_invBalance = getNumberAndValidate($('#return-calculate-invBalance').val());
        var refundAmount = returnNetTot - return_invBalance;

        refundAmount = ( refundAmount > 0 ) ? refundAmount : 0;
        $('#return-refund').val(commaSeparateNumber(refundAmount, dPlaces));
        $('#return-refundable-hidden').val(getNumberAndValidate(refundAmount, dPlaces));

    }

    function getReturnSubTotal() {
        var returnSubTot = 0;
        $('.return-totalAmount').each(function () {
            var thisVal = getNumberAndValidate($(this).val());
            returnSubTot += thisVal;
        });

        $('#return-subTotalAmount').val(commaSeparateNumber(returnSubTot, dPlaces));
        return returnSubTot;
    }

    function getReturnDiscTotal() {
        var returnDiscTot = 0;
        $('.return-discountAmount').each(function () {
            var thisVal = getNumberAndValidate($(this).val());
            returnDiscTot += thisVal;
        });

        $('#return-discTotal').val(commaSeparateNumber(returnDiscTot, dPlaces));
        return returnDiscTot;
    }

    $(document).on('click', '.deleteRow-return', function () {
        var parentTr = $(this).closest('tr');
        parentTr.remove();

        getReturnSubTotal();
        getReturnNetTotal();
        getReturnDiscTotal();

        setTimeout(function () {
            $('#return-item-image').attr("src", "<?php echo base_url('images/item/no-image.png');?>");
        }, 100);


    });

    $(document).on('keyup', '#return-refund', function () {
        var refundAmount = getNumberAndValidate($(this).val());
        var applicable_refundAmount = getNumberAndValidate($('#return-refundable-hidden').val());

        if (applicable_refundAmount < refundAmount) {
            $(this).val('');
            myAlert('w', 'Maximum refund amount is ' + commaSeparateNumber(applicable_refundAmount, dPlaces));
        }

    });

    function focusOnLastCharacter(id) {
        var inputField = document.getElementById(id);
        if (inputField != null && inputField.value.length != 0) {
            if (inputField.createTextRange) {
                var FieldRange = inputField.createTextRange();
                FieldRange.moveStart('character', inputField.value.length);
                FieldRange.collapse();
                FieldRange.select();
            } else if (inputField.selectionStart || inputField.selectionStart == '0') {
                var elemLen = inputField.value.length;
                inputField.selectionStart = elemLen;
                inputField.selectionEnd = elemLen;
                inputField.focus();
                inputField.select();
            }
        } else {
            inputField.focus();
            inputField.select();
        }
    }

    function itemReturn(returnMode) {
        /* var errorCount = 0;

         $('.returnQTY').each(function () {
         var thisVal = getNumberAndValidate($(this).val());
         if (thisVal == 0) {
         focusOnLastCharacter($(this).attr('id'));
         errorCount++;
         return false;
         }
         });

         if (errorCount == 0) {*/
        var postData = $('#return_form').serializeArray();
        postData.push({'name': 'returnMode', 'value': returnMode});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            beforeSend: function () {
                startLoad();
            },
            url: "<?php echo site_url('Pos/invoice_return'); ?>",
            success: function (data, status, request) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#return_modal').modal('hide');
                    exchangePrint(data[2], data[3]);
                    $('#isInvoiced').val('');
                    if (returnMode == 'exchange') {

                    } else {
                    }
                    //newInvoice();

                }

            }, error: function () {
                stopLoad();
                myAlert('e', 'Error in invoice return.')
            }
        });
        /* }
         else {
         myAlert('e', 'Please enter valid data on return quantity fields');
         }*/

    }

    function exchangePrint(returnID, returnCode) {
        /**window.open("<?php // echo site_url('Pos/return_print'); ?>/"+returnID+"/"+returnCode, "", "width=700,height=400");*/

        $.ajax({
            async: true,
            type: 'post',
            html: 'json',
            url: "<?php echo site_url('Pos/return_print'); ?>/" + returnID + "/" + returnCode,
            success: function (data) {
                $('#print_template').modal({backdrop: 'static'});
                $('#print_content').html(data);
            }, error: function () {
                myAlert('e', 'Error in print call.')
            }
        });
    }

    function clearData() {
        $('.formInput').val('');
        $('.item-search-container input').each(function () {
            $(this).val('');
        });
        $('#item-image-hidden').val();
        $('#is-edit').val('');
    }

</script>
<script type="text/javascript">
    var tDay = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    var tMonth = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    function getClock() {
        var date = new Date();
        var nHour = date.getHours(), nMin = date.getMinutes(), nSec = date.getSeconds(), ap;

        if (nHour == 0) {
            ap = " AM";
            nHour = 12;
        }
        else if (nHour < 12) {
            ap = " AM";
        }
        else if (nHour == 12) {
            ap = "PM";
        }
        else if (nHour > 12) {
            ap = "PM";
            nHour -= 12;
        }

        if (nMin <= 9) {
            nMin = "0" + nMin;
        }
        if (nSec <= 9) {
            nSec = "0" + nSec;
        }

        $('#timeBox').text(nHour + " : " + nMin + " : " + nSec + " " + ap);

    }

    function getDate() {
        var toDay = new Date();
        var day = toDay.getDay(), cMonth = toDay.getMonth(), nDate = toDay.getDate(), nYear = toDay.getYear();
        if (nYear < 1000) {
            nYear += 1900;
        }
        //$('#dateBox').html(tDay[day] + " &nbsp;" + nDate + " &nbsp;" + tMonth[cMonth] + " &nbsp;" + nYear);
        $('#dateBox').html(nDate + " &nbsp;" + tMonth[cMonth] + " &nbsp;" + nYear);
    }

    window.onload = function () {
        getClock();
        getDate();
        setInterval(getClock, 1000);
        setupPromotionData();
    };

    function setupPromotionData() {
        var freeIssueItems = window.localStorage.getItem('freeIssueItems');
        freeIssueData = JSON.parse(freeIssueItems);
    }


    /** Created By Shafri */
    function clearForNewInvoice() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'key': this.value},
            url: "<?php echo site_url('Pos/loadNewInvoiceNo'); ?>",

            beforeSend: function () {
                $("#tbody_itemList").html('<tr><th colspan="11" style="text-align:center;"><i class="fa fa-refresh fa-spin"></i> Loading </th></tr>');
                $("#doSysCode_refNo").html('<i class="fa fa-refresh fa-spin"></i>');
                $('.tenderTBTxt').val('');
            },
            success: function (data) {
                $("#tbody_itemList").html('');
                $("#doSysCode_refNo").html(data['refCode']);
                defaultCustomer();
                reset_generalDiscount();
            },
            error: function (xhr) {
                $("#tbody_itemList").html('');
                $("#doSysCode_refNo").html('');
                myAlert('e', 'Error' + xhr.status + ': ' + xhr.statusText)
            }
        });

    }

    function validateCreditSales() {
        var creditSalesAmount = $('#creditSalesAmount').val();
        if (creditSalesAmount > 0) {
            $('#cashAmount').attr('disabled', true);
            $('#chequeAmount').attr('disabled', true);
            $('#cardAmount').attr('disabled', true);
            $('#giftCard').attr('disabled', true);
            $('#creditNote').attr('disabled', true);

            $('#cashAmount').val(0);
            $('#chequeAmount').val(0);
            $('#cardAmount').val(0);
            $('#giftCard').val(0);
            $('#creditNote').val(0);
        } else {
            $('#cashAmount').attr('disabled', false);
            $('#chequeAmount').attr('disabled', false);
            $('#cardAmount').attr('disabled', false);
            $('#giftCard').attr('disabled', false);
            $('#creditNote').attr('disabled', false);
            $('#creditSalesAmount').val(0);
        }
    }

    function LoadCustomers() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'key': this.value},
            url: "<?php echo site_url('Pos/customer_search'); ?>",
            success: function (data) {
                $("#customerSearchTB > tbody").empty();
                var appData = '';

                if (data.length > 0) {
                    $.each(data, function (i, val) {
                        appData += '<tr class="validTR" data-id="' + val['customerAutoID'] + '"  data-currency="' + val['customerCurrency'] + '">';
                        appData += '<td></td><td>' + val['customerSystemCode'] + '</td><td>' + val['secondaryCode'] + '</td><td>' + val['customerName'] + '</td><td>' + val['customerTelephone'] + '</td></tr>';
                    });
                    customerSearchTB.append(appData);
                }
                else {
                    customerSearchTB.append('<tr><td colspan="3">No data</td></tr>');
                }
                //$('table #customerSearchTB > tbody tr').keynavigator();
            }, error: function () {
                myAlert('e', 'Error in Customer search.')
            }
        });
    }

    function defaultCustomer() {
        $('#customerID').val(0);
        //$('#_trCurrency').val(selectedCusArray[1]);
        $('#customerCode').val('CASH');
        $('.customerSpan').text('Cash');
        $('#creditsalesfield').addClass('hidden');
        $('#cashAmount').attr('disabled', false);
        $('#chequeAmount').attr('disabled', false);
        $('#cardAmount').attr('disabled', false);
        $('#giftCard').attr('disabled', false);
        $('#creditNote').attr('disabled', false);
        //$('.trCurrencySpan').text(selectedCusArray[1]);
        $('#customer_modal').modal('hide');
        y = 0;
    }

    function open_recallHold_modal_search() {
        var recall_search = $('#recall_search').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'key': this.value, 'recall_search': recall_search},
            url: "<?php echo site_url('Pos/recall_hold_invoice'); ?>",
            success: function (data) {
                $("#invoiceSearchTB > tbody").empty();
                var appData = '';

                if (data.length > 0) {
                    $.each(data, function (i, val) {
                        appData += '<tr class="validTR" data-id="' + val['invoiceID'] + '"  data-currency="' + val['customerCurrency'] + '">';
                        appData += '<td></td><td>' + val['documentSystemCode'] + '</td><td>' + val['customerCode'] + '</td><td>' + val['cusName'] + '</td>';
                        appData += '<td align="center">' + val['timestamp'] + '</td></tr>';
                    });
                    invoiceSearchTB.append(appData);
                }
                else {
                    invoiceSearchTB.append('<tr><td colspan="5">No data</td></tr>');
                }

                calculateDiscount_byPercentage();
            }, error: function () {
                myAlert('e', 'Error in hold invoice laoding.')
            }
        });
        recall_modal.modal({backdrop: 'static'});
    }

    function checkifItemExsist() {
        var count = 0;
        var selectedTR = $('#tbody_itemList tr');

        selectedTR.each(function () {
            count++;
        });
        if (count > 0) {

            swal({
                    title: "Are you sure ?",
                    text: "Current invoice cannot be recalled!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                },
                function () {
                    open_recallHold_modal()
                }
            );
        } else {
            open_recallHold_modal()
        }
    }

    function checkifItemExsistReturn() {
        var count = 0;
        var selectedTR = $('#tbody_itemList tr');

        selectedTR.each(function () {
            count++;
        });
        if (count > 0) {

            swal({
                    title: "Are you sure ?",
                    text: "Current invoice cannot be recalled!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                },
                function () {
                    recall_invoice()
                }
            );
        } else {
            recall_invoice()
        }
    }


    function checkifItemExsistpower() {
        var count = 0;
        var selectedTR = $('#tbody_itemList tr');

        selectedTR.each(function () {
            count++;
        });
        if (count > 0) {

            swal({
                    title: "Are you sure ?",
                    text: "Current invoice cannot be recalled!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                },
                function () {
                    session_close()
                }
            );
        } else {
            session_close()
        }
    }

    function open_pos_payments_modal() {
        clearform_pos_receipt();

        //var tot = $('#netTotSpan').text();
        /*var tot = parseFloat($('#netTot_after_g_disc').val());*/
        var netTotal_tmp = parseFloat($('#netTotVal').val());
        var netTotal = netTotal_tmp.toFixed(dPlaces);
        var netTotalAfterDiscount = parseFloat($('#netTot_after_g_disc').val());

        var discountTmp = $("#gen_disc_percentage").val();
        var discountPer = parseFloat(discountTmp);
        if ((discountPer > 0 && discountPer <= 100)) {
            var discountAmount = netTotal_tmp - netTotalAfterDiscount;
        } else {
            var discountAmount = 0;
        }
        discountAmount = discountAmount.toFixed(dPlaces);

        if (parseFloat(netTotal) > 0) {
            if ($('#customerID').val() > 0) {
                $('.creditSalesPayment').attr('readonly', false);
                $('#rcgcbtn').attr('disabled', false);
            } else {
                $('.creditSalesPayment').attr('readonly', true);
                $('#rcgcbtn').attr('disabled', true);
            }
            var gross = $('#totSpan').text();
            var disc = $('#discSpan').text();
            $('#Grosstotal').text(netTotal);
            //$('#discounttxt').text(disc);
            $('#discounttxt').text(discountAmount);
            $("#pos_payments_modal").modal('show');

            //$("#paid_by").select2("val", "");

            setTimeout(function () {
                calculateReturn();
                //calculateDiscount_byPercentage()
                $("#paid").focus();
            }, 500);

        } else {
            myAlert('e', 'There is no item to proceed');
        }
    }

    function calculateReturn() {
        var zero = 0;
        var sum = 0;
        $('.netAmount').each(function () {
            sum += parseFloat($(this).val());
        });
        var fi = sum.toFixed(dPlaces);
        var total = fi;
        total = parseFloat($("#netTot_after_g_disc").val());
        total = total.toFixed(dPlaces)
        $("#final_payableNet_amt").text(total);
        var paidAmount = parseFloat(0);
        var return_amount = 0
        calculateDelivery()

        if (total < paidAmount || true) {
            return_amount = paidAmount - total;
            if (return_amount < 0) {
                $("#return_change").text(zero.toFixed(dPlaces))
                $("#returned_change").val(zero.toFixed(dPlaces))
            } else {
                if (isNaN(return_amount)) {
                    var return_amount = 0;
                }
                $("#return_change").text(return_amount.toFixed(dPlaces))
                $("#returned_change").val(return_amount.toFixed(dPlaces))
            }

        } else {
            $("#return_change").text(zero.toFixed(dPlaces))
            $("#returned_change").val(zero.toFixed(dPlaces))
        }
        if (total <= paidAmount && total != 0) {
            document.getElementById("submit_btn").style.display = "block"
            $("#total_payable_amt").val(total)
        } else {
            $("#total_payable_amt").val(total)
            document.getElementById("submit_btn").style.display = "block" //none
        }
        var totalamnt = 0;
        $(".paymentInput").each(function (e) {
            var valueThis = $.trim($(this).val());
            totalamnt += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;

        });
        $("#paid").val(totalamnt);
    }

    function calculateDelivery() {
        var zero = 0;
        var total = parseFloat($("#final_payableNet_amt").text());
        var paidAmount = parseFloat($("#paid").val());
        var return_amount = 0;
        // var elementid = $(element).attr('id')
        var delivery = $("#deliveryPersonID option:selected").data('cp');
        if (typeof(delivery) != "undefined") {

            var commission = total * (delivery / 100);
            /*console.log('commission:'+commission);*/
            var deliveryPayable = total - commission;
            /*console.log('deliveryPayable: '+deliveryPayable);*/

            $("#totalPayableAmountDelivery_id").val(deliveryPayable.toFixed(dPlaces));
            if (total < paidAmount || true) {
                return_amount = paidAmount - deliveryPayable;
                if (return_amount < 0) {
                    //return_amount = 0;
                }
                if (!isNaN(return_amount)) {
                    $("#returned_change_toDelivery").val(return_amount.toFixed(dPlaces));
                } else {
                    $("#returned_change_toDelivery").val(zero);
                }
            } else {
                $("#returned_change_toDelivery").val(zero.toFixed(dPlaces));
            }

        } else {
            $("#totalPayableAmountDelivery_id").val(zero);
            $("#returned_change_toDelivery").val(zero);
        }
    }

    function updateNoteValue(tmpValue) {
        $(".paymentInput").val('');
        var id = '<?php echo get_pos_paymentConfigID_cash() ?>';
        var noteValue = $(tmpValue).text();
        $("#paid_temp").html(noteValue);
        $("#paid").val(parseFloat(noteValue));
        $("#paymentType_" + id).val(parseFloat(noteValue));
        /*$("#paid").focus();*/
        calculateReturn();
        calculatePaidAmount();

    }

    function calculatePaidAmount(tmpThis) {
        var total = 0
        $(".paymentInput").each(function (e) {
            var valueThis = $.trim($(this).val());
            total += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;

        });
        if ($('#paymentType_7').val() > 0) {
            var paymentType = $('#paymentType_7').val()
            $('.paymentInput').val(0)
            $('#paymentType_7').val(paymentType)
        }
        $("#paid").val(total);
        var payable = $("#total_payable_amt").val();
        var returnAmount = total - payable;
        if (returnAmount > 0) {
            $("#returned_change").val(returnAmount);
            $("#return_change").html(returnAmount.toFixed(dPlaces))
        }

        setTimeout(function () {
            var discount = parseFloat(0);
            var subTotal = $("#total_payable_amt").val();
            var netTotal = subTotal - discount;
            var paidAmountTmp = parseFloat($("#paid").val());

            var advancePaymets = $("#delivery_advancePaymentAmount").val();
            netTotal = netTotal - advancePaymets;
            var returnChange = paidAmountTmp - netTotal;
            $("#final_payableNet_amt").html(netTotal.toFixed(dPlaces));
            if (returnChange > 0 || true) {
                $("#return_change").html(returnChange.toFixed(dPlaces))
            }


            /** Total card amount should not be more than the NET Total */
            if (typeof tmpThis !== "undefined") {
                var cardTotal = 0;
                $(".paymentOther").each(function (e) {
                    var valueThis = $.trim($(this).val());
                    cardTotal += ($.isNumeric(valueThis)) ? parseFloat(valueThis) : 0;
                });

                netTotal = netTotal.toFixed(dPlaces);
                if (cardTotal > netTotal) {
                    $(".paymentOther ").val(0);
                    calculateReturn();
                    $("#cardTotalAmount").val(0);
                    myAlert('e', 'You can not pay more than the net total using cards!');

                } else {
                    $("#cardTotalAmount").val(cardTotal);
                }
                $("#netTotalAmount").val(netTotal);
            }

        }, 50);
    }

    function updateExactCash() {
        $(".paymentInput").val('');
        var id = '<?php echo get_pos_paymentConfigID_cash() ?>';
        var totalAmount = $("#final_payableNet_amt").text();
        $("#paid").val(parseFloat(totalAmount));
        $("#paymentType_" + id).val(parseFloat(totalAmount));
        calculateReturn();
    }

    function updatePaidAmount(tmpValue) {
        var cPaidAmount = $("#paid").val();
        var tmpAmount = $(tmpValue).text();
        var tmpAmount_txt = $("#paid_temp").text();
        if (parseFloat(tmpAmount) >= 0 || $.trim(tmpAmount) == '.') {
            var updateVal = cPaidAmount + tmpAmount;
            var tmpAmount_output = $.trim(tmpAmount_txt) + $.trim(tmpAmount);
            if ($.trim(tmpAmount) == '.') {
                //  updateVal = cPaidAmount + tmpAmount + '0';
            }
            console.log(updateVal);

            $("#paid_temp").html(tmpAmount_output);
            //$("#paid").val(parseFloat(updateVal));
            $("#paid").val(parseFloat(tmpAmount_output));

        } else if ($.trim(tmpAmount) == 'C') {
            $("#isCreditSale").val(0);
            $("#paid").val(0);
            $("#paid_temp").html(0);
            $(".paymentInput").val(0);
            $('.cardRef').val('');
        }
        calculateReturn();
        //$("#paid").focus();
    }

    function updateExactCard(paymentTypeID) {

        $("#paid_temp").html(0);
        $(".paymentInput").val('');
        $('.cardRef').val('');
        var totalAmount = $("#final_payableNet_amt").text();
        $("#paymentType_" + paymentTypeID).val(parseFloat(totalAmount));
        calculateReturn();
    }

    function submit_pos_payments() {
        if ($('.creditSalesPayment').val() > 0) {
            $('#CreditSalesAmnt').val($('.creditSalesPayment').val());
            $('#isCreditSale ').val(1);
        } else {
            $('#CreditSalesAmnt').val(0)
        }
        var postData = $('.form_pos_receipt').serializeArray();


        $(".itemID").each(function (e) {
            //postData.push({'name': 'itemID[]', 'value': $(this).val()});
        });
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos/submit_pos_payments'); ?>",
            data: postData,
            cache: false,
            beforeSend: function () {
                //$("#submit_btn_pos_receipt").html('<i class="fa fa-refresh fa-spin"> </i> <?php echo $this->lang->line('common_submit');?>');
                <!--Submit-->
                $("#submit_btn").prop('disabled', true);

            },
            success: function (data) {
                $("#submit_btn").prop('disabled', false);
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    var zero = 0;
                    $('#isInvoiced').val('');
                    $('#totSpan').html(zero.toFixed(dPlaces));
                    $('#netTotSpan').html(zero.toFixed(dPlaces));
                    $("#pos_payments_modal").modal('hide');
                    newInvoice(1);
                    clearform_pos_receipt();
                    searchByKeyword();
                    var doSysCode_refNo = $('#doSysCode_refNo').text();
                    invoicePrint(data[2], data[3], data[4]);
                    searchByKeyword(1);
                    reset_generalDiscount();

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#submit_btn_pos_receipt").html('Submit');
                $("#submit_btn").prop('disabled', false);
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function clearform_pos_receipt() {
        var zero = 0;
        $('#final_payableNet_amt').html(zero.toFixed(dPlaces));
        $('#return_change').html(zero.toFixed(dPlaces));
        $('.paymentInput').val(zero.toFixed(dPlaces));
        /*$('.ar ').val('');*/
        $('#paid').val(zero.toFixed(dPlaces));
        $('#paymentType_1').val(zero);
        $('#paymentType_26').val(zero);
        $('#isCreditSale ').val(zero);
        $('#CreditSalesAmnt ').val('');
        $('#customerTelephone ').val('');
        $('#cardTotalAmount ').val('');
        $('#netTotalAmount ').val('');
        $('#isDelivery ').val('');
        $('#frm_isOnTimePayment ').val('');
        $('#total_payable_amt ').val('');
        $('#delivery_advancePaymentAmount ').val('');
        $('#Grosstotal').text(zero.toFixed(dPlaces));
        $('#discounttxt').text(zero.toFixed(dPlaces));
        $('#memberidhn').val('');
        $('#membernamehn').val('');
        $('#contactnumberhn').val('');
        $('#mailaddresshn').val('');
    }


    function openCreditSalesModal() {
        $('#creditNote_modal').modal('show');
        $("#creditNoteTB > tbody").empty();
        creditNoteTB.append('<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>');
        loadCreditnotes();
    }

    function loadCreditnotes() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'key': this.value},
            url: "<?php echo site_url('Pos/creditNote_load'); ?>",
            success: function (data) {
                $("#creditNoteTB > tbody").empty();
                var appData = '';

                if (data.length > 0) {
                    $.each(data, function (i, val) {
                        //, documentSystemCode, salesReturnDate, netTotal
                        appData += '<tr class="validTR" data-id="' + val['salesReturnID'] + '"  data-amount="' + val['netTotal'] + '">';
                        appData += '<td>' + (i + 1) + '</td><td>' + val['documentSystemCode'] + '</td><td>' + val['salesReturnDate'] + '</td>';
                        appData += '<td align="right">' + commaSeparateNumber(val['netTotal'], dPlaces) + '</td></tr>';
                    });
                    creditNoteTB.append(appData);
                }
                else {
                    creditNoteTB.append('<tr><td colspan="4">No data</td></tr>');
                }

            }, error: function () {
                myAlert('e', 'Error in Customer search.')
            }
        });
    }


    function fetch_related_uom_posgen(short_code, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'short_code': short_code},
            url: "<?php echo site_url('dashboard/fetch_related_uom'); ?>",
            success: function (data) {
                formInput.prop('readonly', false);
                itemUOM.prop('readonly', false);
                itemUOM.css('background', '#fff');
                itemUOM.empty();
                itemUOM.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        itemUOM.append('<option value="' + text['UnitShortCode'] + '" data-value="' + text['conversion'] + '">' + text['UnitShortCode'] + ' | ' + text['UnitDes'] + '</option>');
                    });
                    if (select_value) {
                        itemUOM.val(select_value);
                    }
                }
                re_validate();

                //console.log(enable_BC.prop('checked'));
                if (enable_BC.prop('checked') == true) {
                    $('#pos_form').bootstrapValidator();
                    //itemSearch.trigger("keyup", {which: 13});
                }
                else {
                    itemUOM.focus();
                }
                $("#pos-add-btn").prop('disabled', false);
                $("#pos-add-btn").click();
                pos_form.bootstrapValidator('resetForm', true);
                calculateDiscount_byPercentage();
            }, error: function () {
                myAlert('e', 'Error in UMO fetching.')
            }
        });
    }


    function openRCGCmodel(paymentglautoid) {
        $("#rcgc_modal").modal('show');
    }

    function setrcgccustdetails() {
        var memberid = $('#memberid').val();
        var membername = $('#membername').val();
        if (memberid == '') {
            myAlert('w', 'Member ID id required')
        } else if (membername == '') {
            myAlert('w', ' Member Name id required')
        }
        else {
            $('#memberidhn').val($('#memberid').val());
            $('#membernamehn').val($('#membername').val());
            $('#contactnumberhn').val($('#contactnumber').val());
            $('#mailaddresshn').val($('#mailaddress').val());
            $('#paymentType_26').val($('#final_payableNet_amt').text());
            $('#memberid').val('');
            $('#membername').val('');
            $('#contactnumber').val('');
            $('#mailaddress').val('');
            $("#rcgc_modal").modal('hide');
            calculatePaidAmount($('#paymentType_26').val())
        }

    }

    function add_new_customer_modal() {
        $('#customer_master_add').modal('show');
        $('#customer_master_form')[0].reset();
        $('#customer_master_form').bootstrapValidator('resetForm', true);
        $('#receivableAccount').val(accountsreceivable);
        $('#customerCurrency').val(currency);
        $('#customercountry').val(country);

    }

    function changecreditlimitcurr() {
        var currncy;
        var split;
        currncy = $('#customerCurrency option:selected').text();
        split = currncy.split("|");
        $('.currency').html(split[0]);
        CurrencyID = $('#customerCurrency').val();
        currency_validation_modal(CurrencyID, 'CUS', '', 'CUS');
    }

    $('table.arrow-nav').keydown(function (e) {
        /*alert('hi');*/
        var $table = $(this);
        var $active = $('input:focus,select:focus', $table);
        var $next = null;
        var focusableQuery = 'input:visible,select:visible,textarea:visible';
        var position = parseInt($active.closest('td').index()) + 1;
        console.log('position :', position);
        switch (e.keyCode) {
            case 37: // <Left>
                $next = $active.parent('td').prev().find(focusableQuery);
                break;
            case 38: // <Up>
                $next = $active
                    .closest('tr')
                    .prev()
                    .find('td:nth-child(' + position + ')')
                    .find(focusableQuery)
                ;

                break;
            case 39: // <Right>
                $next = $active.closest('td').next().find(focusableQuery);
                break;
            case 40: // <Down>
                $next = $active
                    .closest('tr')
                    .next()
                    .find('td:nth-child(' + position + ')')
                    .find(focusableQuery)
                ;
                break;
        }
        if ($next && $next.length) {
            $next.focus();
        }
    });


    $(document).ready(function () {

    });

    function getRowIdx() {
        return $('#data-table').DataTable().cell({
            focused: true
        }).index().row;
    }
</script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-09-18
 * Time: 2:40 PM
 */
