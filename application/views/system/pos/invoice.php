<?php
$bank_card = load_bank_with_card();
$tr_currency = $this->common_data['company_data']['company_default_currency'];
?>

<style type="text/css">
    .wrapper {
        background: #fff !important;
        /*min-height: 80% !important;*/
    }

    #itemDisplayTB_div {
        height: 270px;
        border: 1px solid #c0c0c0;
        margin-top: -15px;
    }

    div.fixHeader_Div::-webkit-scrollbar, div.smallScroll::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    div.fixHeader_Div::-webkit-scrollbar-track, div.smallScroll::-webkit-scrollbar-track  {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
    }

    div.fixHeader_Div::-webkit-scrollbar-thumb, div.smallScroll::-webkit-scrollbar-thumb  {
        margin-left: 30px;
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.5);
        width: 3px;
        position: absolute;
        top: 0px;
        opacity: 0.4;
        border-radius: 7px;
        z-index: 99;
        right: 1px;
        height: 10px;
    }

    .maxFont { font-size: 20px; }

    #itemDisplayTB thead th { background: #dedede; }

    #itemDisplayTB tbody tr:hover {
        background-color: #9cc2cb;
        color: #eff7ff;
        cursor: pointer;
    }

    #customerSearchTB tbody tr:hover {
        background-color: #23b79d;
        color: #eff7ff;
        cursor: pointer;
    }

    #invoiceSearchTB tbody tr:hover {
        background-color: #9cc2cb;
        color: #eff7ff;
        cursor: pointer;
    }

    #itemDisplayTB tr:nth-child(even) {
        //background: #c9e7e4;
        color: #000000;
    }

    #itemDisplayTB tr:nth-child(odd) {
        color: #000000;
        background: #ebf8f8;
    }

    .pos_Header {
        background: rgba(201, 231, 228, 1);
        background: -moz-linear-gradient(top, rgba(201, 231, 228, 1) 0%, rgba(24, 145, 121, 1) 92%, rgba(24, 145, 121, 1) 100%);
        background: -webkit-gradient(left top, left bottom, color-stop(0%, rgba(201, 231, 228, 1)), color-stop(92%, rgba(24, 145, 121, 1)), color-stop(100%, rgba(24, 145, 121, 1)));
        background: -webkit-linear-gradient(top, rgba(201, 231, 228, 1) 0%, rgba(24, 145, 121, 1) 92%, rgba(24, 145, 121, 1) 100%);
        background: -o-linear-gradient(top, rgba(201, 231, 228, 1) 0%, rgba(24, 145, 121, 1) 92%, rgba(24, 145, 121, 1) 100%);
        background: -ms-linear-gradient(top, rgba(201, 231, 228, 1) 0%, rgba(24, 145, 121, 1) 92%, rgba(24, 145, 121, 1) 100%);
        background: linear-gradient(to bottom, rgba(201, 231, 228, 1) 0%, rgba(24, 145, 121, 1) 92%, rgba(24, 145, 121, 1) 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#c9e7e4', endColorstr='#189179', GradientType=0);
        color: #ffffff;
        border-bottom: none !important;

    }

    #paymentTB td {
        border: none;
        padding-bottom: 0px;
    }

    .paymentTBTxt {
        width: 120px;
        padding-right: 3px;
    }

    .tenderTBTxt {
        padding-right: 8px;
        width: 100%;
        height: 100%;
        border: none;
        background: inherit;
    }

    .selectedTR {
        //background-color: #23b79d !important;
        //color: #eff7ff;
        background-color: #2c3b41 !important;
        color : #f3f3f3 !important;

    }

    .checkbox.inline.no_indent + .checkbox.inline.no_indent {
        margin-left: 0;
        margin-right: 10px;
    }

    .checkbox.inline.no_indent:last-child { margin-right: 0; }

    #pos-add-btn{ margin-top: 25px; }

    #pos-add-btn-div{text-align: center !important;}

    #item-image{ width: 120px; height: 80px; }

    #netTotal-div{ margin-left: -35px;}

    @media (max-width: 767px) {
        #displayDet { margin-top: 100px !important; }

        #form_div { margin-top: 250px !important; }

        #refNo_masterDiv { float: left !important; }

        #refNo_Div { padding-right: 0px !important; }

        #salesPrice{ width: 85% !important;}

        #currency_masterDiv { float: left !important; }

        #currency_Div { padding-right: 0px !important; }

        /*#currencyDenomination_data { height: 65% !important; }*/

        .wrapper { /*height: 1200px*/ }

        #pos-add-btn{ margin: 0px !important; }

        #pos-add-btn-div{ text-align: left !important; }

        #netTotal-div{ margin-left: 0px;}

        #actionBtn-div{ margin-top: 60px; }
    }

    .actionBtn {
        margin: 1% 1%;
        width: auto !important;
        padding: 6px 15px !important;
    }

    #qtyAdj {
        color: #000;
        padding-right: 8px;
        width: inherit;
        height: 100%;
        border: none;
    / / background: inherit;
        border: 1px solid #01ff70;
        margin-bottom: 0px;
    }

    .form-inline{
        background: #3c3939 !important;
         margin-bottom: 3px;
        border-bottom: 2px solid #3c3838;
        box-shadow: 0px 2px 5px 0px #807979;
    }

</style>
<?php
$this->load->view('include/header', $title);
$this->load->view('include/top');
?>

<div  style="position: fixed; width: 100%; z-index: 10">
    <div class="row" id="displayDet" style="background-color: #5b5b63; margin-top: 50px">
        <div class="col-12" style="margin-top: 3px;color: #eff7ff !important; padding-left: 1%">

            <div class="col-md-2 ">
                <div class="form-group" style="margin-bottom: 2px">
                    <label for="" class="cols-sm-2">Cashier : </label>
                    <span class=""><?php echo ucwords($this->session->loginusername); ?></span>
                </div>
            </div>

            <div class="col-md-2 ">
                <div class="form-group" style="margin-bottom: 2px">
                    <label for="" class="cols-sm-2">Customer : </label>
                    <span class="customerSpan">Cash</span>
                </div>
            </div>

            <div class="col-md-2 ">
                <div class="form-group" style="margin-bottom: 2px">
                    <label for="" class="cols-sm-2 ">Sales Mode : </label>
                    <span class="">Retail</span>
                </div>
            </div>

            <div class="col-md-2 ">
                <div class="form-group" style="margin-bottom: 2px">
                    <label for="" class="cols-sm-2 ">No of Items : </label>
                    <span class="" id="itemCount">0</span>
                </div>
            </div>

            <div class="col-md-2 pull-right" id="refNo_masterDiv">
                <div class="form-group pull-right" id="refNo_Div" style="margin-bottom: 2px; padding-right: 10%">
                    <label for="" class="cols-sm-2 ">Ref No : </label>
                    <span class="" style=""><?php echo $refNo; ?></span>
                </div>
            </div>

            <div class="col-md-2 pull-right" id="currency_masterDiv">
                <div class="form-group pull-right" id="currency_Div" style="margin-bottom: 2px; padding-right: 10%">
                    <label for="" class="cols-sm-2 ">Currency : </label>
                    <span class="trCurrencySpan"><?php echo $tr_currency; ?></span>
                </div>
            </div>
        </div>
    </div>

</div>
<div id="form_div" style="padding: 1%;/*height: 70%*/; margin-top: 80px">
    <div class="" style="margin-bottom: -10px">
        <label class="checkbox-inline no_indent">
            <input type="checkbox" id="enable_BC" value="option1" checked="checked"> <strong>Enable BC</strong>
        </label>
    </div>
    <div class="cols-12">
        <div class="m-b-lg" style="padding-left: 15px">
            <form class="form-horizontal" role="form" id="pos_form" autocomplete="off">
                <div class="row" style="margin-top: 1%">
                    <div class="col-md-2">
                        <div class="form-group cols-sm-3">
                            <label for="itemSearch" class="cols-sm-4"> Item </label>
                            <input type="text" name="itemSearch" id="itemSearch" class="form-control "
                                   style="width: 85%;">
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
                            <select name="itemUOM" id="itemUOM" class="form-control  formInput" style="width: 85%;">
                                <option></option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="form-group cols-sm-3">
                            <label for="itemQty" class="cols-sm-4"> Qty </label>
                            <input type="text" name="itemQty" id="itemQty" class="form-control  formInput number"
                                   style="width: 85%;">
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="form-group cols-sm-3">
                            <label for="disPer" class="cols-sm-4"> Disc% </label>
                            <input type="text" name="disPer" id="disPer" class="form-control  formInput number"
                                   style="width: 85%;">
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="form-group cols-sm-3">
                            <label for="disAmount" class="cols-sm-4"> Discount </label>
                            <input type="text" name="disAmount" id="disAmount" class="form-control  formInput number"
                                   style="width: 85%;" disabled="disabled">
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="form-group cols-sm-3">
                            <label for="salesPrice" class="cols-sm-4"> Sales Price </label>
                            <input type="text" name="salesPrice" id="salesPrice" class="form-control  formInput number"
                                   style="/*width: 85%;*/">
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="form-group" id="pos-add-btn-div">
                            <label for="" ></label>
                            <button type="submit" class="btn btn-success btn-sm" id="pos-add-btn"><i class="fa fa-plus"></i></button>
                            <input type="hidden" id="item-image-hidden" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <form id="my_form" onsubmit="return false">
        <div class="cols-12">
            <div class="m-b-lg fixHeader_Div" id="itemDisplayTB_div">
                <table class="<?php echo table_class(); ?>" id="itemDisplayTB" style="">
                    <thead>
                    <tr class="header_tr">
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

                    <tbody>
                    </tbody>
                </table>
                <input type="hidden" name="_cashAmount" id="h_cashAmount" value="0">
                <input type="hidden" name="_chequeAmount" id="h_chequeAmount" value="0">
                <input type="hidden" name="_cardAmount" id="h_cardAmount" value="0">
                <input type="hidden" name="_cardAmount" id="h_giftCardAmount" value="0">
                <input type="hidden" name="_cardAmount" id="h_creditAmount" value="0">
                <input type="hidden" name="customerID" id="customerID" value="0">
                <input type="hidden" name="customerCode" id="customerCode" value="CASH">
                <input type="hidden" name="_trCurrency" id="_trCurrency" value="<?php echo $tr_currency; ?>">
            </div>
        </div>

        <div class="col-12" style="margin-top: 1%; text-align: center !important;">
            <div class="col-md-1">
                <img class="img-thumbnail" src="<?php echo base_url('images/item/no-image.png');?>" id="item-image" />
            </div>
            <div class="col-md-11"> <!--<div class="col-md-9">-->
                <div class="col-md-2 pull-left" id="netTotal-div">
                    <div class="form-group">
                        <label for="" class="cols-sm-2 maxFont">Net Total : </label>
                        <span class="maxFont" id="netTotSpan">0.00</span>
                        <input type="hidden" name="netTotVal" id="netTotVal" value="0"/>
                    </div>
                </div>
                <div class="col-md-3 pull-left">
                    <div class="form-group">
                        <label for="" class="cols-sm-2 maxFont">Discount : </label>
                        <span class="maxFont" id="discSpan">0.00</span>
                        <input type="hidden" name="discVal" id="discVal" value="0"/>
                    </div>
                </div>
                <div class="col-md-3 pull-left">
                    <div class="form-group">
                        <label for="" class="cols-sm-2 maxFont">Total : </label>
                        <span class="maxFont" id="totSpan">0.00</span>
                        <input type="hidden" name="totVal" id="totVal" value="0"/>
                    </div>
                </div>

            </div>
        </div>
    </form>
    <div class="row row-centered" style="padding: 2% !important;" id="actionBtn-div">
        <div class="col-12" style="margin-top: 1%; text-align: center !important;">
            <button type="button" class="btn btn-success btn-flat col-md-1 actionBtn" onclick="open_tenderModal()">
                [F1] Tender
            </button>
            <button type="button" class="btn btn-success btn-flat col-md-1 actionBtn" onclick="newInvoice()"> [F5]
                New
            </button>
            <button type="button" class="btn btn-success btn-flat col-md-1 actionBtn" onclick="hold_invoice()"> [F2]
                Hold
            </button>
            <button type="button" class="btn btn-success btn-flat col-md-1 actionBtn"onclick="open_customer_modal()">
                [F6] Customer
            </button>
            <button type="button" class="btn btn-success btn-flat col-md-1 actionBtn" onclick="deleteItem()">
                Delete
            </button>
            <button type="button" class="btn btn-success btn-flat col-md-1 actionBtn" onclick="adjust_qty()"> [F8]
                Adj.Qty
            </button>
            <button type="button" class="btn btn-success btn-flat col-md-1 actionBtn" onclick="open_recall_modal()">
                [F7] Recall
            </button>
            <button type="button" class="btn btn-danger btn-flat col-md-1 actionBtn" onclick="session_close()">
                Session Close
            </button>
        </div>
    </div>
</div>

<?php
$this->load->view('include/footer');
?>

<div aria-hidden="true" role="dialog" tabindex="-1" id="tender_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-sm">
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
                            <td style="padding: 0px"><input type="text" name="" id="cashAmount" class="tenderTBTxt tenderPay number"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Cheque Amount</td>
                            <td style="padding: 0px"><input type="text" name="" id="chequeAmount" class="tenderTBTxt tenderPay number"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Card Amount</td>
                            <td style="padding: 0px"><input type="text" name="" id="cardAmount"
                                                            class="tenderTBTxt tenderPay number"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Gift Card Amount</td>
                            <td style="padding: 0px">
                                <input type="text" name="" id="giftCard" class="tenderTBTxt tenderPay searchData" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Credit Note Amount</td>
                            <td style="padding: 0px">
                                <input type="text" name="" id="creditNote" class="tenderTBTxt tenderPay searchData" readonly>
                            </td>
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
                                        <input type="text" name="" id="tenderAmountTotal" class="paymentTBTxt number"
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
                                        <input type="text" name="" id="tenderChangeAmount" class="paymentTBTxt number"
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

<div aria-hidden="true" role="dialog" tabindex="-1" id="cardDet_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-sm" style="width: 25%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="newInvoice(1)">
                    <span aria-hidden="true">&times;</span>
                </button>-->
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
                            <td valign="bottom"><span class="customerSpan">Cash</span> </td>
                        </tr>
                        <tr>
                            <td style="height: 20px" valign="bottom">Invoice No</td>
                            <td valign="bottom">:</td>
                            <td valign="bottom"><span id="invoiceNo"></span> </td>
                        </tr>
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
                            <td>Card Number</td>
                            <td style="padding: 0px">
                                <input type="text" name="cardNumber" id="cardNumber" class="tenderTBTxt number">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Reference No</td>
                            <td style="padding: 0px">
                                <input type="text" name="referenceNO" id="referenceNO" class="tenderTBTxt number">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Bank</td>
                            <td style="padding: 0px">
                                <select name="bank" id="bank" class="tenderTBTxt">
                                    <?php
                                    foreach($bank_card as $card){
                                        echo '<option value="'.$card['GLAutoID'].'"> '.$card['bankName'].' - '.$card['bankBranch'].' </option>';
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
                    <button class="btn btn-primary btn-xs" type="button" onclick="save_moreCardDetails()">Save</button>
                    <!--<button data-dismiss="modal" class="btn btn-default btn-xs" type="button" onclick="newInvoice(1)">Close</button>-->
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="customer_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-sm" style="width: 50%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Customer</h5>
            </div>
            <div class="modal-body" style="padding: 0px; height: 40%">
                <div class="form-inline" style="">
                    <div class="form-group" style="margin: 10px; margin-top: 0px; padding-top: 10px">
                        <label for="customerSearch" style="color: #eff7ff; font-weight: 600">Search </label>
                        <input type="text" name="customerSearch" class="form-control" id="customerSearch"
                               style="height: 20px; font-size: 10px; padding: 7px 5px" autocomplete="off">
                    </div>
                </div>
                <div class="fixHeader_Div" style="height: 185px">
                    <table class="<?php echo table_class(); ?>" id="customerSearchTB">
                        <thead>
                        <tr class="headerTR">
                            <th></th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Address</th>
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
                <button data-dismiss="modal" class="btn btn-primary btn-xs" type="button" onclick="selectEmployee()">
                    Select
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-xs" type="button">Close</button>
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
                <h5 class="modal-title">Invoice Recall</h5>
            </div>
            <div class="modal-body" style="padding: 0px; height: 36%">
                <div class="form-inline" style="">
                    <div class="form-group" style="margin: 10px; margin-top: 0px; padding-top: 10px;">
                        <label for="recall_search" style="color: #eff7ff; font-weight: 600">Search </label>
                        <input type="text" name="recall_search" class="form-control" id="recall_search"
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
                <button class="btn btn-primary btn-xs" type="button" onclick="selectInvoice()">
                    Select
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-xs" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="error_modal" class="modal fade" style="display: none;">
        <div class="modal-dialog" >
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Item Qty Insufficient Details</h4>
                </div>
                <div class="modal-body">
                    <table class="<?php echo table_class(); ?>" id="qtyDemandTB">
                        <thead>
                            <tr><th>Code</th><th>Item</th><th>UOM</th><th>Request QTY</th><th>Available QTY</th></tr>
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

<div aria-hidden="true" role="dialog" tabindex="-1" id="creditNote_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Credit Note Search</h5>
            </div>
            <div class="modal-body" style="padding: 0px; height: 25%">
                <div class="form-inline" style="">
                    <div class="form-group" style="margin: 10px; margin-top: 0px; padding-top: 10px;">
                        <label for="creditNote_search" style="color: #eff7ff; font-weight: 600">Search </label>
                        <input type="text" name="creditNote_search" class="form-control" id="creditNote_search"
                               style="height: 20px; font-size: 10px; padding: 7px 5px" autocomplete="off">
                    </div>
                </div>

                <table class="table table-striped table-condensed">
                    <tr>
                        <td><lable>Issued Date</lable></td>
                        <td>:</td>
                        <td>2016-09-15</td>
                    </tr>
                    <tr>
                        <td><lable>Amount</lable></td>
                        <td>:</td>
                        <td>3000.000</td>
                    </tr>
                    <tr>
                        <td><lable>Customer name</lable></td>
                        <td>:</td>
                        <td>Mubashir Mubarak</td>
                    </tr>
                </table>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <button data-dismiss="modal" class="btn btn-primary btn-xs" type="button" onclick="selectEmployee()">
                    Select
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-xs" type="button">Close</button>
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
                        <td><lable>Issued Date</lable></td>
                        <td>:</td>
                        <td>2016-09-15</td>
                    </tr>
                    <tr>
                        <td><lable>Amount</lable></td>
                        <td>:</td>
                        <td>3000.000</td>
                    </tr>
                    <tr>
                        <td><lable>Expired Date</lable></td>
                        <td>:</td>
                        <td>2016-12-15</td>
                    </tr>
                </table>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <button data-dismiss="modal" class="btn btn-primary btn-xs" type="button" onclick="selectEmployee()">
                    Select
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-xs" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="till_modal" class="modal fade" data-keyboard="false" style="display: none;">
    <div class="modal-dialog" style="/*width: 50%*/">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close tillModal_close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="tillModal_title">Start Day</h5>
            </div>
            <div class="modal-body" style="padding: 10px; height: auto">
                <div class="smallScroll" id="currencyDenomination_data" align="center" style="height: auto; overflow-y: scroll"></div>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <input type="hidden" id="isStart" />
                <button class="btn btn-primary btn-xs" type="button" id="tillSave_Btn">
                    Save
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-xs tillModal_close" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="testModal" class="modal fade" data-keyboard="false" style="display: none;">
    <div class="modal-dialog" style="/*width: 50%*/">
        <div class="modal-content">

            <div class="modal-body" style="padding: 0px; height: 0px">
                <div class="alert alert-success fade in" style="margin-top:18px;">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close" style="text-decoration: none">
                        <i class="fa fa-refresh fa-spin fa-3x fa-fw" style="font-size:15px"></i>
                    </a>
                    <strong>Session Successfully closed.</strong> Redirect in <span id="countDown"> 5 </span> Seconds.
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var dPlaces = <?php echo $this->common_data['company_data']['company_default_decimal'];?>;
    var pos_form = $('#pos_form');
    var itemDisplayTB = $('#itemDisplayTB');
    var enable_BC = $('#enable_BC');
    var formInput = $('.formInput');
    var itemCount = $('#itemCount');
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
    var cardDet_modal = $('#cardDet_modal');
    var recall_modal = $('#recall_modal');
    var till_modal = $('#till_modal');
    var tenderPay = $('.tenderPay');
    var customerSearchTB = $('#customerSearchTB');
    var invoiceSearchTB = $('#invoiceSearchTB');
    var selectedCusArray = [];
    var selectedItemArray = [];
    var exceededItemArray = [];
    var freeIssueData;


    till_modal.on('shown.bs.modal', function (e) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            url: "<?php echo site_url('Pos/load_currencyDenominationPage'); ?>",
            beforeSend: function () {
                $('#currencyDenomination_data').html('');
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#currencyDenomination_data').html(data);
                if($('#isStart').val() == 1){ $('#counterID').prop('disabled', false); }
                else{ $('#counterID').prop('disabled', true); }
            }, error: function () {
                stopLoad();
                myAlert('e', 'Error in loading currency denominations.')
            }
        });
    });

    <?php

    if( $isHadSession == 0 ){
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

        setTimeout(function(){ itemSearch.focus(); }, 500);

        itemSearch_typeHead();

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

        invoiceSearchTB.tableHeadFixer({
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
                itemSearch: {validators: {notEmpty: {message: 'Item Code is required.'}}},
                itemUOM: {validators: {notEmpty: {message: 'UOM is required.'}}},
                itemQty: {validators: {notEmpty: {message: 'QTY is required.'}}},
                salesPrice: {validators: {notEmpty: {message: 'Price is required.'}}}
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

                var itemDet = '<tr>';
                itemDet += '<td align="right"></td>';
                itemDet += '<td>' + itemSearch.val() + '</td>';
                itemDet += '<td>' + itemDescription.val() + '</td>';
                itemDet += '<td>' + itemUOM.val() + '</td>';
                itemDet += '<td align="right">' + qty + '</td>';
                itemDet += '<td align="right">' + commaSeparateNumber(salesPrice.val(), dPlaces) + '</td>';
                itemDet += '<td align="right">' + commaSeparateNumber(amount, dPlaces) + '</td>';
                itemDet += '<td align="right">' + disPer.val() + '</td>';
                itemDet += '<td align="right">' + commaSeparateNumber(discAmount, dPlaces) + '</td>';
                itemDet += '<td align="right">' + commaSeparateNumber(thisNetTot, dPlaces) + '</td>';
                itemDet += '<td align="right">';
                itemDet += '<span class="glyphicon glyphicon-trash deleteRow" style="color:rgb(209, 91, 71); position: static"></span>';
                itemDet += '<input type="hidden" name="itemID[]" value="' + itemAutoID.val() + '" >';
                itemDet += '<input type="hidden" name="itemName[]" value="' + itemDescription.val() + '" >';
                itemDet += '<input type="hidden" name="itemUOM[]" value="' + itemUOM.val() + '" >';
                itemDet += '<input type="hidden" name="itemQty[]" class="itemQty" value="' + itemQty.val() + '" >';
                itemDet += '<input type="hidden" name="itemPrice[]" class="itemPrice" value="' + salesPrice.val() + '" >';
                itemDet += '<input type="hidden" name="itemDis[]" class="itemDis" value="' + disPer.val() + '" >';
                itemDet += '<input type="hidden" class="thisCurrentStk" value="' + currentStock.val() + '" >';
                itemDet += '<input type="hidden" class="discountAmount" value="' + discAmount + '" >';
                itemDet += '<input type="hidden" class="totalAmount" value="' + amount + '" >';
                itemDet += '<input type="hidden" class="netAmount" value="' + thisNetTot + '" >';
                itemDet += '<input type="hidden" class="item-image-hidden" value="' + image_hidden + '" >';
                itemDet += '</td>';
                itemDet += '</tr>';
                itemDisplayTB.append(itemDet);

                itemAdd_sub_function();
                isThereAnyPromotion( itemAutoID.val() );



                itemCount.html(parseInt(itemCount.text()) + 1);
                formInput.val('');
                itemSearch.typeahead('val','');
                itemUOM.empty();
                itemUOM.css('background', '#eee');
                formInput.prop('readonly', true);
                itemUOM.prop('readonly', true);
                itemSearch.focus();

                /*pos_form.bootstrapValidator('revalidateField', 'itemUOM');
                 pos_form.bootstrapValidator('revalidateField', 'itemQty');
                 pos_form.bootstrapValidator('revalidateField', 'salesPrice');*/

                pos_form[0].reset();
                pos_form.bootstrapValidator('resetForm', true);
            } else {
                itemQty.val('');
                pos_form.bootstrapValidator('revalidateField', 'itemQty');
                myAlert('e', 'Qty is not valid');
            }
        });
    });

    function itemAdd_sub_function(){
        getTot();
        getDiscountTot();
        getNetTot();
        addTrNumber();
    }

    function itemSearch_typeHead() {
        var item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace();
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "<?php echo site_url();?>/Pos/item_search/?q=%QUERY"
        });

        item.initialize();
        itemSearch.typeahead(null, {
            minLength: 3,
            highlight: true,
            displayKey: 'itemSystemCode',
            source: item.ttAdapter(),
            templates: {
                empty: [
                    '<div class="tt-suggestion"><div style="white-space: normal;">',
                    'unable to find any item that match the current query',
                    '</div></div>'
                ].join('\n'),
                suggestion: Handlebars.compile('<div><strong>{{itemDescription}}</strong> – {{itemSystemCode}}</div>')
            }
        }).on('typeahead:selected', function (object, datum) {
            itemSearch.val(datum.itemSystemCode);
            itemAutoID.val(datum.itemAutoID);
            itemDescription.val(datum.itemDescription);
            itemDescription2.val(datum.itemDescription);
            $('#item-image').attr('src', "<?php echo base_url('images/item/');?>/"+datum.itemImage);
            $('#item-image-hidden').val("<?php echo base_url('images/item/');?>/"+datum.itemImage);
            currentStockDsp.val(datum.currentStock);
            currentStock.val(datum.currentStock);
            currentStock.attr('data-value', datum.currentStock);
            itemQty.val(1);
            /*if (datum.currentStock >= 1) {
                itemQty.val(1);
            } else {
                itemQty.val('');
            }*/

            salesPrice.val(datum.companyLocalSellingPrice);

            fetch_related_uom(datum.defaultUnitOfMeasure, datum.defaultUnitOfMeasure);
            /*$('#defaultUOM').val(datum.defaultUnitOfMeasure);
             $('#mainCategoryID').val(datum.mainCategoryID);
             $('#subcategoryID').val(datum.subcategoryID);
             $('#subSubCategoryID').val(datum.subSubCategoryID);
             $('#revanueGLCode').val(datum.revanueGLCode);
             $('#costGLCode').val(datum.costGLCode);
             $('#assteGLCode').val(datum.assteGLCode);*/
        });
    }

    function isThereAnyPromotion(itemAutoID){
        /*$.each(freeIssueData, function(i, data){
            console.log(data.itemAutoID);
            if(data.itemAutoID == itemAutoID){
                //data.
                return false;
            }
        });*/
    }

    itemUOM.change(function(){
        var conversion =  getNumberAndValidate($(this).find('option:selected').attr('data-value'));
        var defaultStk = currentStock.attr('data-value');
        //console.log( 'defaultStk:'+defaultStk+' /// conversion:'+conversion);
        currentStockDsp.val( defaultStk * conversion );
        currentStock.val( defaultStk * conversion );

        itemQty.val('');
        pos_form.bootstrapValidator('revalidateField', 'itemQty');


    });

    itemSearch.keyup(function (e) {
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
                        itemUOM.append('<option value="'+text['UnitShortCode']+'" data-value="'+text['conversion']+'">'+ text['UnitShortCode'] + ' | ' + text['UnitDes'] +'</option>');
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
        }else{
            var thisVal = getNumberAndValidate( $(this).val() );
            var disCountPer = getNumberAndValidate( disPer.val() );

            if(thisVal > 0 && disCountPer > 0 ){
                var m = thisVal * disCountPer * 0.01;
                disAmount.val(commaSeparateNumber(m, dPlaces));
            }
            else{
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
        var parentTr = $(this).closest('tr');
        parentTr.remove();
        trRemove();

    });

    function trRemove() {
        addTrNumber();
        itemCount.html(parseInt(itemCount.text()) - 1);

        getTot();
        getDiscountTot();
        getNetTot();
    }

    disPer.keyup(function () {
        var disc = $.trim($(this).val());
        if (disc != '') {
            if (disc > 100) {
                myAlert('e', 'Discount Percentage must be laser than 100');
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
            var thisAmount = $.trim($(this).val());
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
        var cashAmount = getNumberAndValidate( $('#cashAmount').val() );
        var cardAmount = getNumberAndValidate( $('#cardAmount').val() );
        var chequeAmount = getNumberAndValidate( $('#chequeAmount').val() );
        var giftCardAmount = getNumberAndValidate( $('#giftCard').val() );
        var creditNoteAmount = getNumberAndValidate( $('#creditNote').val() );
        var customerID = $('#customerID').val();

        var card_chequeTot = cardAmount + chequeAmount + giftCardAmount + creditNoteAmount;
        var netTot = getNumberAndValidate(netTotVal.val());
        var totalPayment  = cashAmount + cardAmount + chequeAmount;

        if( totalPayment < netTot && customerID == 0){
            myAlert('e', 'Payment not equal to Net total.');
            errorCount++;
        }
        if( card_chequeTot > netTot){
            myAlert('e', 'Card and Cheque Amount sum can not be greater than net total.');
            errorCount++;
        }

        if( errorCount == 0 ){
            $('#h_cardAmount').val(cardAmount);
            $('#h_cashAmount').val(cashAmount);
            $('#h_chequeAmount').val(chequeAmount);
            $('#h_giftCardAmount').val(giftCardAmount);
            $('#h_creditAmount').val(creditNoteAmount);

            var postData = $('#my_form').serializeArray();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: postData,
                url: "<?php echo site_url('Pos/invoice_create'); ?>",
                success: function (data) {
                    myAlert(data[0], data[1]);


                    if (data[0] == 's' && $.isNumeric(cardAmount) && cardAmount > 0) {
                        tender_modal.modal('hide');
                        $('#invID').val(data[2]);
                        $('#invoiceNo').text(data[3]);
                        cardDet_modal.modal({backdrop: 'static'});
                        setTimeout(function () {
                            $('#cardNumber').focus()
                        }, 500);
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

    $('#customerSearch').keyup(function () {
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
                        appData += '<td></td><td>' + val['customerSystemCode'] + '</td><td>' + val['customerName'] + '</td><td>' + val['customerAddress1'] + '</td></tr>';
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
    });

    function selectEmployee() {
        $('#customerID').val(selectedCusArray[0]);
        //$('#_trCurrency').val(selectedCusArray[1]);
        $('#customerCode').val(selectedCusArray[2]);
        $('.customerSpan').text(selectedCusArray[3]);
        //$('.trCurrencySpan').text(selectedCusArray[1]);
    }

    $(document).on('click', '#customerSearchTB tr', function () {
        if( $(this).hasClass('headerTR') == false ) {
            $('#customerSearchTB tr').removeClass('selectedTR');
            $(this).addClass('selectedTR');

            var dataID = $(this).attr('data-id');
            var dataCurrency = $(this).attr('data-currency');
            var dataCode = $.trim($(this).find('td:eq(1)').text());
            var dataName = $.trim($(this).find('td:eq(2)').text());

            dataCurrency = ( dataCode == 'CASH')? '<?php echo $tr_currency; ?>' : dataCurrency;

            selectedCusArray = [dataID, dataCurrency, dataCode, dataName];
        }
    });

    $(document).on('click', '#invoiceSearchTB tr', function () {
        if( $(this).hasClass('headerTR') == false ) {
            $('#invoiceSearchTB tr').removeClass('selectedTR');
            $(this).addClass('selectedTR');


        }
    });

    $(document).on('click', '#itemDisplayTB tr', function () {
        qty_adjustProcess();

        if( $(this).hasClass('header_tr') == false ) {
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

    function selectInvoice(){
        var invTB = $('#invoiceSearchTB tr.selectedTR');
        var selectedRowCount = 0;
        $.each(invTB, function(){ selectedRowCount++ });

        if( selectedRowCount == 1) {
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
                    $('#_trCurrency').val(masterData[1]);
                    $('#customerCode').val(masterData['customerCode']);
                    $('.customerSpan').text(masterData['cusName']);
                    itemCount.html(0);
                    exceededItemArray = [];

                    var appendDet = '';
                    var isCurrentQtyExceed = 0;
                    $.each(invItems, function (i, elm) {
                        var qty = elm['qty'];
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

                        if (currentStk > 0) {
                            if (qty > currentStk) {
                                qty = currentStk
                            }
                            var amount = (qty * elm['price']);
                            var disCountPer = getNumberAndValidate(elm['discountPer']);
                            var discountVal = (disCountPer > 0) ? amount * 0.01 * disCountPer : 0;
                            var total = amount - disCountPer;

                            appendDet += '<tr><td></td>';
                            appendDet += '<td>' + elm['itemSystemCode'] + '</td>';
                            appendDet += '<td>' + elm['itemDescription'] + '</td>';
                            appendDet += '<td>' + elm['unitOfMeasure'] + '</td>';
                            appendDet += '<td align="right">' + qty + '</td>';
                            appendDet += '<td align="right">' + commaSeparateNumber(elm['price'], dPlaces) + '</td>';
                            appendDet += '<td align="right">' + commaSeparateNumber(amount, dPlaces) + '</td>';
                            appendDet += '<td align="right">' + commaSeparateNumber(elm['discountPer'], dPlaces) + '</td>';
                            appendDet += '<td align="right">' + commaSeparateNumber(discountVal, dPlaces) + '</td>';
                            appendDet += '<td align="right">' + commaSeparateNumber(total, dPlaces) + '</td>';
                            appendDet += '<td align="right">';
                            appendDet += '<span class="glyphicon glyphicon-trash deleteRow" style="color:rgb(209, 91, 71); position: static"></span>';
                            appendDet += '<input type="hidden" name="itemID[]" value="' + elm['itemAutoID'] + '" >';
                            appendDet += '<input type="hidden" name="itemName[]" value="' + elm['itemDescription'] + '" >';
                            appendDet += '<input type="hidden" name="itemUOM[]" value="' + elm['unitOfMeasure'] + '" >';
                            appendDet += '<input type="hidden" name="itemQty[]" class="itemQty" value="' + qty + '" >';
                            appendDet += '<input type="hidden" name="itemPrice[]" class="itemPrice" value="' + elm['price'] + '" >';
                            appendDet += '<input type="hidden" name="itemDis[]" class="itemDis" value="' + disCountPer + '" >';
                            appendDet += '<input type="hidden" class="thisCurrentStk" value="' + currentStk + '" >';
                            appendDet += '<input type="hidden" class="discountAmount" value="' + discountVal + '" >';
                            appendDet += '<input type="hidden" class="totalAmount" value="' + amount + '" >';
                            appendDet += '<input type="hidden" class="netAmount" value="' + total + '" >';
                            appendDet += '</td></tr>';
                            itemCount.html(parseInt(itemCount.text()) + 1);
                        }
                    });


                    itemDisplayTB.append(appendDet);
                    itemAdd_sub_function();

                    setTimeout(function () {
                        recall_modal.modal('hide');
                    }, 500);

                    if (isCurrentQtyExceed > 0) {
                        $('#qtyDemandTB > tbody').empty();
                        var appendData = '';
                        $.each(exceededItemArray, function (i, val) {
                            appendData += '<tr><td>' + val['itemCode'] + '</td><td>' + val['requestItem'] + '</td><td>' + val['UMO'] + '</td>';
                            appendData += '<td align="right">' + val['requestQty'] + '</td><td align="right">' + val['availableQty'] + '</td></tr>';
                        });

                        $('#qtyDemandTB').append(appendData);
                        error_modal.modal({backdrop: "static"});
                    }
                }, error: function () {
                    myAlert('e', 'Error in loading hold invoice details .');
                }
            });
        }
        else{
            myAlert('e', 'Please Select a Invoice to recall.');
        }
    }

    itemQty.keyup(function () {
        var thisQty = $.trim($(this).val());
        var currentQty = parseFloat($.trim(currentStock.val()));
        thisQty = (thisQty != '') ? parseFloat(thisQty) : parseFloat(0);
        if (thisQty > currentQty) {
            itemQty.val('');
            pos_form.bootstrapValidator('revalidateField', 'itemQty');
            myAlert('e', 'Qty can not be larger than Current qty');
        }

    });

    $(document).on('keyup', '#qtyAdj', function (e) {
        if (e.keyCode == 13) {
            qty_adjustProcess();
        }
        else{
            var thisVal = $.trim($(this).val());
            var availableStk = parseFloat( $.trim($(this).attr('data-stock')) );
            thisVal = parseFloat(thisVal);

            if($.isNumeric(thisVal) && availableStk < thisVal ){
                $(this).val('');
                myAlert('e', 'Available Stock is only '+availableStk);
            }
        }
    });

    $(document).on('onChange', '#qtyAdj', function (e) {
        qty_adjustProcess();
    });

    function qty_adjustProcess(){
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

        }

        $('#itemDisplayTB tr').removeClass('selectedTR');
    }

    function save_moreCardDetails(){
        var postData = $('#cardPayment_form').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Pos/invoice_cardDetail'); ?>",
            success: function (data) {
                myAlert(data[0], data[1]);

                if( data[0] == 's' ){ newInvoice(1); }
                /*setTimeout(function(){
                 newInvoice(1);
                 }, 500);*/

            }, error: function () {
                myAlert('e', 'Error in saving card details.')
            }
        });
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

    function getNumberAndValidate(thisVal){
        thisVal = $.trim(thisVal);

        if( $.isNumeric(thisVal) ){
            return parseFloat(thisVal);
        }
        else{
            return parseFloat(0);
        }
    }

    $('.searchData').click(function(){
        var thisID = $(this).attr('id');
        if(thisID == 'creditNote'){ $('#creditNote_modal').modal({backdrop: 'static'}); }
        else if(thisID == 'giftCard'){ $('#giftCard_modal').modal({backdrop: 'static'}); }

    });

    function session_close(){
        $("#isStart").val(0);
        $(".tillModal_close").show();
        $("#tillModal_title").text("Day End");
        $("#tillSave_Btn").attr("onclick", "shift_close()");
        till_modal.modal({backdrop:"static"});
    }
</script>
<script type="text/javascript" src="<?php echo base_url('plugins/keyboard-short-cut/shortcut.js'); ?>"></script>
<script type="text/javascript">

    shortcut.add("F1", function () {
        open_tenderModal();
    });

    shortcut.add("F2", function () {
        hold_invoice();
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
        open_recall_modal();
    });

    shortcut.add("F8", function () {
        adjust_qty();
    });

    shortcut.add("Delete", function () {
        deleteItem();
    });


    function hold_invoice() {
        var tot = getTot();
        if (tot > 0) {
            swal({
                    title: "Are you sure?",
                    text: "You want to hold this invoice!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                },
                function () {
                    var postData = $('#my_form').serializeArray();

                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: postData,
                        url: "<?php echo site_url('Pos/invoice_hold'); ?>",
                        success: function (data) {
                            myAlert(data[0], data[1]);

                            if( data[0] == 's' ){ newInvoice(1); }
                            /*setTimeout(function(){
                             newInvoice(1);
                             }, 500);*/

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
                    title: "Are you sure?",
                    text: "This invoice cannot be recalled !",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes"
                },
                function () {
                    window.location = "<?php echo site_url('Pos/')?>";
                }
            );
        }
        else{
            window.location = "<?php echo site_url('Pos/')?>";
        }
    }

    function open_customer_modal() {
        customer_modal.modal({backdrop: 'static'});
        setTimeout(function () {
            $('#customerSearch').focus();
        }, 500);

    }

    function deleteItem() {
        var count = 0;
        var selectedTR = $('#itemDisplayTB tr.selectedTR');


        if( selectedTR.hasClass('header_tr') == false ) {

            selectedTR.each(function () { count++; });

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
        var tot = 1;
        if (tot > 0) {
            itemAdd_sub_function();
            tenderPay.keyup();
            tender_modal.modal({backdrop: 'static'});

            setTimeout(function(){
                 $('#cashAmount').focus();
            }, 500);
        }
        else {
            myAlert('e', 'There is no item to proceed');
        }

    }

    function adjust_qty() {
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

            qtyTD.append('<input type="text" class="qtyAdjTxt number" id="qtyAdj" value="' + qty + '" data-value="' + qty + '" data-stock="'+thisCurrentStk+'" style="width: ' + thisWidth + 'px" autocomplete="off"/>');

            qtyTD.css({
                'width': thisWidth + 'px',
                'padding': '0px',
                'vertical-align': 'middle'
            });
            $('#qtyAdj').focus();
        }
        else {
            myAlert('e', 'Please select item to proceed.');
        }
    }

    function open_recall_modal() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'key': this.value},
            url: "<?php echo site_url('Pos/recall_invoice'); ?>",
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

            }, error: function () {
                myAlert('e', 'Error in hold invoice laoding.')
            }
        });
        recall_modal.modal({backdrop:'static'});
    }
</script>
<script type="text/javascript">
    var tDay = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    var tMonth = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

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
        $('#dateBox').html(tDay[day] + ", &nbsp;" + tMonth[cMonth] + " &nbsp;" + nDate + ", &nbsp;" + nYear);
    }

    window.onload = function () {
        getClock();
        getDate();
        setInterval(getClock, 1000);
        setupPromotionData();
    };

    function setupPromotionData(){
        var freeIssueItems = window.localStorage.getItem('freeIssueItems');
        freeIssueData = JSON.parse(freeIssueItems);
    }
</script>
<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-09-18
 * Time: 2:40 PM
 */
