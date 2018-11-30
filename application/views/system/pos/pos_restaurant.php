<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos.css') ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/slick/slick/slick.css') ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/slick/slick/slick-theme.css') ?>">
<style>
    .receiptPadding {
        width: 16.5%;
        float: left;
        text-align: right;
        padding-right: 3px;
    }

    .receiptPaddingHead {
        width: 15%;
        float: left;
        text-align: right;
        padding-right: 3px;
    }

    .btn-lg {
        font-size: 14px !important;
    }
</style>

<?php
$bank_card = load_bank_with_card();
$tr_currency = $this->common_data['company_data']['company_default_currency'];

$this->load->view('include/header', $title);
$this->load->view('include/top');
?>

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
<div id="form_div" style="padding: 1%; margin-top: 40px">

    <div class="row" style="margin-top: 0px;">
        <div class="col-md-6" style="padding-right: 0px;">

            <!--<div class="row">
                <div class="col-md-12">
                    <input type="text" class="form-control inputBorder" id="pcodeBarcode"
                           placeholder="Scan your barcode">
                </div>
            </div>-->
            <?php current_pc() ?>
            <div class="productCls">
                <div class="row">
                    <div class="col-md-4">
                        <?php echo form_dropdown('customerType', getCustomerType_drop(), $defaultCustomerType, 'id="customerType" onchange="updateCustomerType(this)" class="form-control input-sm" style="margin:5px;"'); ?>

                    </div>
                    <div class="col-md-8">
                        <div class="receiptPaddingHead">Qty</div>
                        <div class="receiptPaddingHead">@rate</div>
                        <div class="receiptPaddingHead">Total</div>
                        <div class="receiptPaddingHead">Dist.<br/>%</div>
                        <div class="receiptPaddingHead">Dis.<br/>Amt.</div>
                        <div class="receiptPaddingHead">Net<br/>Total</div>
                    </div>
                    <!--<div class="col-md-1"> </div>
                    <div class="col-md-2"></div>
                    <div class="col-md-1">Dist.<br/>%</div>
                    <div class="col-md-1">Dis.<br/>Amt.</div>
                    <div class="col-md-2" style="padding-left:0px;">Net<br/>Total</div>-->
                </div>
            </div>

            <div style="overflow: scroll; height: 250px; width: 100%;">
                <form id="posInvoiceForm" class="form_pos_receipt" method="post">
                    <div id="log">

                        <!--Added Item -->
                        <!--<div class="row itemList"
                             style="margin: 0px; border-bottom: 1px solid #dddddd; padding-top: 5px; padding-bottom: 5px;">
                            <div class="col-md-1 hidden-xs hidden-sm menuItem_pos_col_1">
                                <img src="/gs_sme/uploads/pos_menu_carrotcupcake_1477915842.jpg" style="max-height: 40px;"
                                     alt="">
                            </div>
                            <div class="col-md-3 menuItem_pos_col_5">Pepsi <br>[UD0002]</div>
                            <div class="col-md-8">
                                <div class="receiptPadding">
                                    <input type="text" value="3" class="display_qty menuItem_input"/>
                                </div>
                                <div class="receiptPadding">
                                    <span class="menu_itemCost menuItemTxt">200</span>
                                </div>
                                <div class="receiptPadding">
                                    <span class="menu_total menuItemTxt">200</span>
                                </div>

                                <div class="receiptPadding">
                                    <input style="width:60%; " type="text" value="5"
                                           class="menu_discount menu_qty menuItem_input">
                                </div>
                                <div class="receiptPadding">
                                    <input style="width:90%; " type="text" value="100"
                                           class="menu_discount menu_qty menuItem_input">
                                </div>
                                <div class="receiptPadding">
                                    <div style="width:55px; text-align: right;" class="itemCostNet menuItemTxt"> 200</div>
                                    <div onclick="deleteDiv(1)" data-placement="bottom" rel="tooltip" title="Delete"
                                         style="cursor:pointer; width: 12px; margin-top: -16px;     margin-right: -13px;"
                                         class="pull-right"><i
                                            class="fa fa-close closeColor"></i></div>
                                </div>
                            </div>


                        </div>
                        <input type="hidden" name="productCode[productCode]" class="productCode" value="UD0002">
                        <input type="hidden" name="price[productCode]" class="menuPrice" value="2.70">
                        <input type="hidden" name="qty[productCode]" class="qtyItem" value="1">-->

                    </div>
                </form>
            </div>

            <form class="form_pos_receipt" method="post">
                <div class="itemListContainer">

                    <div class="row itemListFoot">
                        <div class="col-md-4">Total Items :</div>
                        <div class="col-md-2">
                            <div id="total_item_qty">0</div>
                            <input type="hidden" id="total_item_qty_input" name="total_item_qty_input"/>
                        </div>
                        <div class="col-md-3 ar">Gross Total :</div>
                        <div class="col-md-3 ar">
                            <div id="gross_total">0</div>
                            <input type="hidden" id="gross_total_input" name="gross_total_input"/>
                        </div>
                    </div>

                    <div class="row itemListFoot">
                        <div class="col-md-4">
                            <button class="btn btn-sm btn-default btn-block" style="border-radius: 0px;">
                                Invoice ID : <strong id="pos_salesInvoiceID_btn">
                                <span>
                                <?php
                                $invoiceID = isPos_invoiceSessionExist();
                                if (!empty($invoiceID)) {
                                    $id = str_pad($invoiceID, 4, "0", STR_PAD_LEFT);
                                    echo $id;
                                } else {
                                    echo '<span class="label label-danger">New</span>';
                                }
                                ?>
                                    </span>
                                </strong>
                            </button>
                        </div>
                        <div class="col-md-2">
                            &nbsp;
                            <!--<span class="dis_amt" id="dis_amt">0</span>-->
                            <!--<input type="text" style="color:black; width:50%" name="dis_amt" id="dis_amt" value="0">-->
                        </div>
                        <div class="col-md-3 ar">
                            Discount % :
                            <input maxlength="3" onkeyup="calculateFinalDiscount()"
                                   type="number"
                                   style="color: black; width: 38px; font-weight: 800; text-align: right;"
                                   name="discount_percentage" id="dis_amt" value="0">
                        </div>
                        <div class="col-md-3 ar" style="border-bottom: 1px solid #ffffff; padding-bottom: 5px;">
                            <div id="total_discount">0.00</div>
                            <input type="hidden" id="total_discount_amount" name="total_discount_amount"/>
                        </div>
                    </div>

                    <div class="row itemListFoot">
                        <div class="col-md-4">
                            <?php echo form_dropdown('restaurantTable', getresrestaurantTables_drop($warehouseID), '', 'id="restaurantTable" onchange="updaterestaurantTable(this)" class="form-control input-sm" style=""'); ?>
                        </div>
                        <div class="col-md-2">
                            <div>&nbsp;</div>
                        </div>
                        <div class="col-md-3 ar"> Total
                        </div>
                        <div class="col-md-3 ar">
                            <div id="totalWithoutTax">0</div>
                        </div>
                    </div>

                    <div class="row itemListFoot">
                        <div class="col-md-4">&nbsp;</div>
                        <div class="col-md-2">
                            <div>&nbsp;</div>
                        </div>
                        <div class="col-md-3 ar">
                            Total Tax (<?php echo get_totalTax() ?>%) :
                            <input type="hidden" name="totalTax_input" value="<?php echo get_totalTax() ?>">
                        </div>
                        <div class="col-md-3 ar" style="border-bottom: 3px solid #ffffff; padding-bottom: 5px;">
                            <div id="display_tax_amt">0</div>
                            <input type="hidden" name="display_tax_amt_input" id="display_tax_amt_input" value="0">
                        </div>
                    </div>

                    <div class="row itemListFoot">
                        <div class="col-md-4">&nbsp;</div>
                        <div class="col-md-2">
                            <div>&nbsp;</div>
                        </div>
                        <div class="col-md-3 ar"> Service Charge :
                        </div>
                        <div class="col-md-3 ar" style="border-bottom: 3px solid #ffffff; padding-bottom: 5px;">
                            <input type="text" onkeyup="calculateFinalDiscount()" id="serviceCharge"
                                   name="serviceCharge" class="ar"
                                   style="color:#000000; width:100px;" value="<?php
                            $sc = get_defaultServiceCharge();
                            echo !empty($sc) ? $sc : 0;
                            ?>">
                        </div>
                    </div>

                    <!--<div class="row itemListFoot">
                        <div class="col-md-6">Total Payable :</div>
                        <div class="col-md-6 ar">
                            <div id="total_payable" style="color:#ffffff">0</div>
                        </div>
                    </div>-->

                    <div class="row itemListFoot">
                        <div class="col-md-4">&nbsp;</div>
                        <div class="col-md-2">
                            <div>&nbsp;</div>
                        </div>
                        <div class="col-md-3 ar">Net Total :</div>
                        <div class="col-md-3 ar">
                            <div id="total_netAmount" style="font-weight: 800; font-size:16px;">0</div>
                        </div>
                    </div>

                </div>
            </form>

            <div style="width: 100%; height: 40px; margin-top: 10px;">

                <div class="row">
                    <div class="col-md-3">
                        <!--<button class="btn btn-lg btn-danger btn-block dangerCustom" onclick="cancelCurrentOrder()">
                            <i class="fa fa-times" aria-hidden="true"></i> &nbsp;&nbsp;Cancel
                        </button>-->

                    </div>
                    <div class="col-md-3">
                        <!--<a href="#holdmodel" data-toggle="modal" style="text-decoration: none;">
                            <button class="btn btn-lg btn-danger btn-block dangerCustom2" rel="tooltip" title="short cut  Ctrl+S "  onclick="holdReceipt();">
                                <i class="fa fa-pause" aria-hidden="true"></i> &nbsp;&nbsp; Hold
                            </button>

                        </a>-->
                    </div>
                    <div class="col-md-3">
                        <!--<button class="btn btn-lg btn-success btn-block" onclick="open_pos_payments_modal()">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i> &nbsp;&nbsp; Payment (F1)
                        </button>-->
                    </div>

                    <div class="col-md-3">
                        <!--<button class="btn btn-lg btn-default btn-block">
                            Invoice ID : <strong id="pos_salesInvoiceID_btn">
                                <span>
                                <?php
                        /*                                $invoiceID = isPos_invoiceSessionExist();
                                                        if (!empty($invoiceID)) {
                                                            $id = str_pad($invoiceID, 4, "0", STR_PAD_LEFT);
                                                            echo $id;
                                                        } else {
                                                            echo '<span class="label label-danger">New</span>';
                                                        }
                                                        */ ?>
                                    </span>
                            </strong>
                        </button>-->
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-5">

            <div class="panel panel-default" style="border: 1px solid #ddd;">
                <div class="panel-body tabs" style="padding:3px;">

                    <div class="row" style="margin-left: 0px; margin-right: 0px;">
                        <div class="col-md-12" style="padding-left: 15px; padding-right: 15px; padding-top: 10px;">
                            <input type="text" class="form-control" placeholder="Press 'F2' or 'Ctrl+F' to Search"
                                   id="searchProd">
                        </div>
                    </div>

                    <div class="row" style="margin-left: 0px; margin-right: 0px;">
                        <div class="col-md-12" style="border-bottom: 1px solid #ddd; padding-top: 11px;">
                            <div class="regular">
                                <div class="mainCategories">
                                    <div data-toggle="tab" class="categoryItemList" href="#pilltabAll">
                                        All
                                    </div>
                                    <?php
                                    if (!empty($menuCategory)) {
                                        foreach ($menuCategory as $Category) {
                                            ?>
                                            <div data-toggle="tab" class="categoryItemList" tabindex="-1"
                                                 href="#pilltab<?php echo $Category['autoID'] ?>">
                                                <?php echo $Category['description'] ?>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-content" style="overflow: scroll; height: 418px;" id="allProd">
                        <div class="tab-pane fade in active" id="pilltabAll">
                            <?php
                            //$menuItems = get_wareHouseMenuByCategory_All();

                            if (isset($menuItems) && !empty($menuItems)) {
                                foreach ($menuItems as $menuItem) {
                                    $isPack = $menuItem['isPack'];
                                    ?>
                                    <button type="button" data-code="<?php echo $menuItem['warehouseMenuID'] ?>"
                                            data-pack="<?php echo $isPack ?>"
                                            value="item<?php echo $menuItem['warehouseMenuID'] ?>"
                                            <?php if ($isPack){ ?>style="background-color: #E8ECAE;"<?php } ?>
                                            class="menuItemStyle"
                                            onclick="LoadToInvoice<?php if ($isPack == 1) {
                                                echo "Pack";
                                            } ?>(<?php echo $menuItem['warehouseMenuID'] ?>)">

                                        <?php //echo $isPack ?>
                                        <img src="<?php echo $menuItem['menuImage'] ?>" height="50px"
                                             style="padding-bottom: 5px;">
                                        <br>
                                        <span id="proname">
                                            <?php echo $menuItem['menuMasterDescription'] ?>
                                            <br>[<?php echo str_pad($menuItem['warehouseMenuID'], 4, "0", STR_PAD_LEFT); ?>
                                            ] - <small><?php echo $tr_currency; ?></small> <?php echo $menuItem['sellingPrice'] ?>
                                        </span>
                                    </button>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <?php
                        if (!empty($menuCategory)) {
                            foreach ($menuCategory as $Category) {
                                $autoID = $Category['autoID'];
                                ?>
                                <div class="tab-pane fade in" id="pilltab<?php echo $autoID ?>">
                                    <?php
                                    $menuList = get_wareHouseMenuByCategory($autoID);
                                    if (!empty($menuList)) {
                                        foreach ($menuList as $menu) {
                                            $isPack = $menu['isPack'];
                                            ?>
                                            <button type="button" data-code="<?php echo $menu['warehouseMenuID'] ?>"
                                                    data-pack="<?php echo $isPack ?>"
                                                    value="item<?php echo $menu['warehouseMenuID'] ?>"
                                                    <?php if ($isPack){ ?>style="background-color: #E8ECAE;"<?php } ?>
                                                    class="menuItemStyle"
                                                    onclick="LoadToInvoice<?php if ($isPack == 1) {
                                                        echo "Pack";
                                                    } ?>(<?php echo $menu['warehouseMenuID'] ?>)">
                                                <?php //echo $isPack ?>
                                                <img src="<?php echo $menu['menuImage'] ?>" height="50px"
                                                     style="padding-bottom: 5px;">
                                                <br>
                                                <span id="proname"><?php echo $menu['menuMasterDescription'] ?>
                                                    <br>[<?php echo str_pad($menu['warehouseMenuID'], 4, "0", STR_PAD_LEFT); ?>
                                                    ] - <small><?php echo $tr_currency; ?></small> <?php echo $menu['sellingPrice'] ?> </span>
                                            </button>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">


                </div>
                <div class="col-md-3">
                    <!--<button type="button" class="btn btn-lg btn-default btn-block"  onclick="clearSalesInvoice()">
                        <i class="fa fa-plus text-blue" aria-hidden="true"></i> New bill
                    </button>-->
                </div>
                <div class="col-md-3">
                    <!--<button type="button" class="btn btn-lg btn-default btn-block" onclick="session_close()">
                        <i class="fa fa-power-off text-red" aria-hidden="true"></i> Close Counter
                    </button>-->
                </div>

                <div class="col-md-3">


                </div>
            </div>

        </div> <!-- / col-md-6 -->

        <div class="col-md-1">
            <button type="button" class="btn btn-block btn-lg btn-default " onclick="session_close()">
                <i class="fa fa-power-off text-red" aria-hidden="true"></i> <br/>Power
            </button>
            <br>

            <!--<button type="button" class="btn btn-block btn-lg btn-default" onclick="NewBill()">
                <i class="fa fa-plus text-blue" aria-hidden="true"></i> <br>New Bill
            </button>
            <br>-->

            <button type="button" class="btn btn-block btn-lg btn-default" rel="tooltip" title="short cut  Ctrl+O "
                    onclick="open_holdReceipt()">
                <i class="fa fa-external-link-square text-purple" aria-hidden="true"></i> <br/>Open
            </button>
            <br>

            <button class="btn btn-block btn-lg btn-success" onclick="open_pos_payments_modal()">
                <i class="fa fa-shopping-cart" aria-hidden="true"></i><br/> &nbsp;Pay (F1)
            </button>

            <br>
            <a href="#holdmodel" data-toggle="modal" style="text-decoration: none;">
                <button class="btn btn-block btn-lg btn-danger dangerCustom2" rel="tooltip" title="short cut  Ctrl+S "
                        onclick="holdReceipt();">
                    <i class="fa fa-pause" aria-hidden="true"></i> &nbsp;<br> Hold
                </button>
            </a>

            <br>
            <button class="btn btn-lg btn-danger btn-block dangerCustom" onclick="cancelCurrentOrder()">
                <i class="fa fa-times" aria-hidden="true"></i> &nbsp;<br/>Cancel
            </button>
            <br>
            <button type="button" class="btn btn-block btn-lg btn-default" rel="tooltip" title="short cut  Ctrl+O "
                    onclick="open_kitchen_ready()">
                <i class="fa fa-cutlery text-purple" aria-hidden="true"></i> <br/>Kitchen
            </button>
        </div>

    </div> <!--/ row -->


</div>

<div id="posHeader_1">
    <div class="row" id="displayDet" style="background-color: #5b5b63; display: none;">
        <div class="col-12" style="color: #eff7ff !important;">

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
                    <label for="" class="cols-sm-2 "><!--Sales Mode : --></label>
                    <span class=""><!--Retail--></span>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group" style="margin-bottom: 2px">
                    <label for="" class="cols-sm-2 "><!--No of Items :--> </label>
                    <span class="itemCount"><!--0--></span>
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

<?php
$data['notFixed'] = true;
$this->load->view('include/footer', $data);
$this->load->view('system/pos/modals/pos-modal-payments', $data);
$this->load->view('system/pos/modals/rpos-modal-hold-receipt', $data);
$this->load->view('system/pos/modals/rpos-modal-print-template', $data);
$this->load->view('system/pos/modals/rpos-modal-pack-invoice', $data);
?>

<div aria-hidden="true" role="dialog" tabindex="-1" id="till_modal" class="modal fade" data-keyboard="false"
     style="display: none;">
    <div class="modal-dialog" style="/*width: 50%*/">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id=""> <!--style="background-color: #373942; color:#ffffff;"-->
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
                <button class="btn btn-primary btn-xs" type="button" id="tillSave_Btn">
                    Save
                </button>
                </a>
                <a href="<?php echo site_url('dashboard') ?>" class="btn btn-default btn-xs">Close</a>
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


<script type="text/javascript" src="<?php echo base_url('plugins/keyboard-short-cut/shortcut.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/pos/pos.js'); ?>"></script>
<script src="<?php echo base_url('plugins/slick/slick/slick.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('plugins/printJS/jQuery.print.js') ?>" type="text/javascript"></script>
<script type="text/javascript">

    var dPlaces = <?php echo $this->common_data['company_data']['company_default_decimal'];?>; // Don't delete NASIK
    var till_modal = $('#till_modal');

    till_modal.on('shown.bs.modal', function (e) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/load_currencyDenominationPage'); ?>",
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


</script>
<script type="text/javascript">
    $(document).on('ready', function () {
        $("[rel='tooltip']").tooltip();
        $("#searchProd").keyup(function (e) {
            // Retrieve the input field text
            var filter = $(this).val();

            $("#allProd #proname").each(function () {
                if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                    $(this).parent().hide();
                    $(this).parent().attr('data-visible', false);
                } else {
                    $(this).parent().show();
                    $(this).parent().attr('data-visible', true);
                }
            });

            if (e.keyCode == 13) {
                var tmpVar = $("#pilltabAll button:visible")[0];
                var code = $(tmpVar).data('code');
                var pack = $(tmpVar).data('pack');
                console.log(code);

                if (code > 0) {
                    if (pack > 0) {
                        LoadToInvoicePack(code);
                    } else {
                        LoadToInvoice(code);
                    }
                }

            }
        });

        $('.mainCategories').slick({
            dots: false,
            infinite: false,
            speed: 300,
            slidesToShow: 4,
            adaptiveHeight: false
        });

        $("#paid_by").select2({
            templateResult: formatState,
            minimumResultsForSearch: -1
        });

        $('#rpos_print_template').on('hidden.bs.modal', function () {
            $("#dis_amt").val(0);
            $("#serviceCharge").val('<?php echo get_defaultServiceCharge() ?>');
            $("#pos_payments_modal").modal('hide');
            clearSalesInvoice();
        });

        $('.numberFloat').keypress(function (event) {
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });

    });


    function updateCustomerType(tmp) {
        var customerType = tmp.value;
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_restaurant/updateCustomerType'); ?>",
            data: {customerType: customerType},
            cache: false,
            beforeSend: function () {
                startLoadPos();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function LoadToInvoicePack(id) {
        load_packItemList(id);
    }

    function LoadToInvoice(id) {
        var customerType = $("#customerType").val();
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_restaurant/LoadToInvoice'); ?>",
            data: {id: id, customerType: customerType},
            cache: false,
            beforeSend: function () {
                startLoadPos();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                } else if (data['error'] == 0) {

                    /* last  inserted id is 'code' */
                    alert('xx');
                    var divTmp = '<div class="row itemList" id="item_row_' + data['code'] + '" style="margin: 0px; border-bottom: 1px solid #dddddd; padding-top: 5px; padding-bottom: 5px;"><div class="col-md-1 hidden-xs hidden-sm menuItem_pos_col_1"><img src="' + data['menuImage'] + '" style="max-height: 40px;" alt=""> </div><div class="col-md-3 menuItem_pos_col_5">' + data['menuMasterDescription'] + ' <br>[' + data['warehouseMenuID'] + ']</div><div class="col-md-8"> <div class="receiptPadding"> <input type="text" onkeyup="calculateFooter()" value="1" class="display_qty menuItem_input numberFloat" name="qty[' + data['code'] + ']"  /> </div> <div class="receiptPadding"> <span class="menu_itemCost menuItemTxt"> ' + data['sellingPrice'] + '</span> <!-- @rate --> <input type="hidden"  class="menuItemTxt_input numberFloat" name="sellingPrice[' + data['code'] + ']" value="' + data['sellingPrice'] + '"/> <input type="hidden"  class="menuItemTxt_input numberFloat pricewithoutTax" name="pricewithoutTax[' + data['code'] + ']" value="' + data['pricewithoutTax'] + '"/> <input type="hidden"  class="menuItemTxt_input totalMenuTaxAmount numberFloat" name="totalMenuTaxAmount[' + data['code'] + ']" value="' + data['totalTaxAmount'] + '"/><input type="hidden"  class="menuItemTxt_input numberFloat totalMenuServiceCharge" name="totalMenuServiceCharge[' + data['code'] + ']" value="' + data['totalServiceCharge'] + '"/><input type="hidden"  name="frm_isTaxEnabled[' + data['code'] + ']" value="' + data['isTaxEnabled'] + '"/> </div> <div class="receiptPadding"> <span class="menu_total menuItemTxt">0</span>  <!-- total --> </div> <div class="receiptPadding"> <input style="width:60%;" onkeyup="calculateFooter(\'P\')" name="discountPercentage[' + data['code'] + ']"  maxlength="3" type="text" value="0" class="menu_discount_percentage menu_qty menuItem_input numberFloat"> <!-- disc. % --> </div> <div class="receiptPadding"> <input style="width:90%;" onkeyup="calculateFooter(\'A\')" name="discountAmount[' + data['code'] + ']" type="text" value="0" class="menu_discount_amount menu_qty menuItem_input numberFloat"><!-- disc. amount --> </div> <div class="receiptPadding"> <div style="width:55px; text-align: right;" class="itemCostNet menuItemTxt"> [' + data['sellingPrice'] + '</div> <!-- net total --> <div onclick="deleteDiv(' + data['code'] + ')" data-placement="bottom" rel="tooltip" title="Delete" style="cursor:pointer; width: 12px; margin-top: -16px; margin-right: -13px;" class="pull-right "><i class="fa fa-close closeColor"></i></div> </div> </div></div>';

                    $("#log").append(divTmp);
                    calculateFooter();
                    $("[rel='tooltip']").tooltip();

                    $("#pos_salesInvoiceID_btn").html(data['tmpInvoiceID']);
                    $("#holdInvoiceID_input").val(data['tmpInvoiceID_code']);
                    $("#holdInvoiceID").val(data['tmpInvoiceID']);

                    if (data['isPack'] == 1) {
                        savePackDetailItemList(data['code']);
                    }
                    //console.log('isPack' + data['isPack']);


                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }


    function calculateFooter(discountFrom) {
        $('.numberFloat').keypress(function (event) {
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });

        var totalAmount = 0;
        var grossTotal = 0;
        var totalQty = 0;
        var totalDiscount = 0;
        var netAmountTotal = 0;

        $("div .itemList").each(function (e) {
            var qty = $(this).find(".display_qty").val();
            if (qty < 1) {
                $(this).find(".display_qty").val(1);
                qty = $(this).find(".display_qty").val();
            }
            var perItemCost = parseFloat($(this).find(".menu_itemCost").text());
            var discountAmount = $(this).find(".menu_discount").val();
            var total = qty * perItemCost;

            if (discountFrom == 'P') {
                var percentage = $(this).find(".menu_discount_percentage").val();
                if (percentage > 100 || percentage < 0) {
                    $(this).find(".menu_discount_percentage").val(0);
                    $(this).find(".menu_discount_amount").val(0);
                } else {
                    var discountedAmount = (percentage / 100) * total
                    $(this).find(".menu_discount_amount").val(discountedAmount.toFixed(2));
                }


            } else if (discountFrom == 'A') {
                var discountedAmount = $(this).find(".menu_discount_amount").val();
                var percentage = (discountedAmount * 100) / total;
                if (percentage == 0) {
                    $(this).find(".menu_discount_percentage").val(percentage);
                } else if (percentage > 100 || percentage < 0) {
                    //alert('Invalid discount amount ' + percentage);
                    $(this).find(".menu_discount_percentage").val(0);
                    $(this).find(".menu_discount_amount").val(0);
                    var discountedAmount = $(this).find(".menu_discount_amount").val();
                } else {
                    $(this).find(".menu_discount_percentage").val(percentage.toFixed(1));
                }


            } else {
                var discountedAmount = $(this).find(".menu_discount_amount").val();
                var percentage = $(this).find(".menu_discount_percentage").val();
                var discountedAmount = (percentage / 100) * total
                $(this).find(".menu_discount_amount").val(discountedAmount.toFixed(2));
            }
            if (discountedAmount == undefined) {
                discountedAmount = 0;
            }

            var netTotal = total - discountedAmount;
            $(this).find(".itemCostNet").text(netTotal.toFixed(2));
            var netAmount = $(this).find(".itemCostNet").text();
            netAmountTotal = parseFloat(netAmountTotal) + parseFloat(netAmount);

            //console.log(netAmountTotal);
            var totalWithoutDiscount = qty * perItemCost;
            $(this).find(".menu_total").text(total.toFixed(2));
            totalAmount = parseFloat(totalAmount) + parseFloat(total);
            totalQty = parseFloat(totalQty) + parseFloat(qty);
            grossTotal = parseFloat(grossTotal) + parseFloat(totalWithoutDiscount);
            totalDiscount = parseFloat(totalDiscount) + parseFloat(discountAmount);

        });

        $("#total_item_qty").html(totalQty);
        $("#total_item_qty_input").val(totalQty);
        $("#final_purchased_item").html(totalQty);
        $("#gross_total").html(netAmountTotal.toFixed(2));
        $("#gross_total_input").val(netAmountTotal);
        calculateFinalDiscount();
    }

    $(document).ready(function (e) {
        <?php if ($isHadSession == 0) { ?>
        $("#isStart").val(1);
        //$(".tillModal_close").hide();
        $("#tillModal_title").text("Day Start");
        $("#tillSave_Btn").attr("onclick", "shift_create()");
        till_modal.modal({backdrop: "static"});

        <?php }else { ?>
        checkPosSession();
        <?php } ?>
    });

    function checkPosSession(id) {
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_restaurant/checkPosSession'); ?>",
            data: {id: id},
            cache: false,
            beforeSend: function () {
                startLoadPos();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 1) {
                    myAlert('i', 'Ready for new invoice');
                }
                if (data['error'] == 0) {
                    $("#dis_amt").val(data['discountPer']);
                    $("#serviceCharge").val(data['serviceCharge']);
                    myAlert('s', 'Opening Existing Invoice');
                    Load_pos_holdInvoiceData(data['code']);
                    $("#holdInvoiceID_input").val(data['code']);
                    $("#holdInvoiceID").val(data['code']);
                    $("#customerType").val(data['customerTypeID']);
                    $("#restaurantTable").val(data['tableID']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function Load_pos_holdInvoiceData(invoiceID) {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/Load_pos_holdInvoiceData'); ?>",
            data: {invoiceID: invoiceID},
            cache: false,
            beforeSend: function () {
                startLoadPos();
            },
            success: function (data) {
                stopLoad();
                $("#log").html(data);
                calculateFooter();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function clearInvoice() {
        $("#log").html('');
    }

    function deleteDiv(id) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/delete_menuSalesItem'); ?>",
            data: {id: id},
            cache: false,
            beforeSend: function () {
                startLoadPos();
            },
            success: function (data) {
                stopLoad();

                if (data['error'] == 0) {
                    $("#item_row_" + id).remove();
                    calculateFooter();
                } else if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;

    }

    function clearPosInvoiceSession() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/clearPosInvoiceSession'); ?>",
            data: {id: null},
            cache: false,
            beforeSend: function () {
                startLoadPos();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', 'Ready for new invoice');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function NewBill() {
        bootbox.confirm('<?php echo createNewInvoiceConfirmation() ?>', function (result) {
            if (result) {
                clearInvoice();
                clearPosInvoiceSession();
                $("#pos_salesInvoiceID_btn").html('<span class="label label-danger">New</span>');
                $("#holdInvoiceID_input").val('0');
                $("#holdInvoiceID").val('0');
                calculateFooter();
                $("#paid").val(0);
            }
        });

    }

    function clearSalesInvoice() {
        clearInvoice();
        clearPosInvoiceSession();
        $("#pos_salesInvoiceID_btn").html('<span class="label label-danger">New</span>');
        $("#holdInvoiceID_input").val('0');
        $("#holdInvoiceID").val('0');
        calculateFooter();
        $("#paid").val(0);
    }

    function cancelCurrentOrder() {
        bootbox.confirm('<?php echo cancelOrderConfirmation() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "<?php echo site_url('Pos_restaurant/cancelCurrentOrder'); ?>",
                    data: {id: null},
                    cache: false,
                    beforeSend: function () {
                        startLoadPos();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 0) {
                            myAlert('s', data['message']);
                            clearSalesInvoice();
                            reset_delivery_order();
                        } else {
                            myAlert('d', data['message']);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            }
        });
    }

    function calculateFinalDiscount() {
        var disPercentage = $("#dis_amt").val();
        if (disPercentage <= 100 && disPercentage >= 0) {

            /** Discount */
            var tmpTotal = $("#gross_total").text(); // Gross Total
            var discountAmount = (disPercentage / 100) * parseFloat(tmpTotal);
            $("#total_discount").html(discountAmount.toFixed(2)); //output discount

            /** Total */
            var total = parseFloat(tmpTotal) - discountAmount;
            $("#totalWithoutTax").html(total.toFixed(2)); //output Total

            /** Tax */
            var tax = (<?php echo get_totalTax() ?> / 100
        ) *
            total;
            $("#display_tax_amt").html(tax.toFixed(2));// Tax Amount
            $("#display_tax_amt_input").val(tax);// Tax Amount

            var serviceCharge = parseFloat($("#serviceCharge").val());
            if (serviceCharge >= 0) {
            } else {
                console.log(serviceCharge);
                serviceCharge = 0;
                $("#serviceCharge").val('0');
            }

            /** Net */
            var netTotal = tax + total + serviceCharge;
            $("#total_netAmount").html(netTotal.toFixed(2)); //output Net Amount
            $("#final_payable_amt").html(netTotal.toFixed(2)); //output Net Amount
            $("#total_discount_amount").val(discountAmount.toFixed(4));


        } else {
            $("#dis_amt").val(0);
            $("#total_discount_amount").val(0);
        }
    }

    function submit_pos_payments() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/submit_pos_payments'); ?>",
            data: $(".form_pos_receipt").serialize(),
            cache: false,
            beforeSend: function () {
                $("#submit_btn_pos_receipt").html('<i class="fa fa-refresh fa-spin"> </i> Submit');
                $("#submit_btn").prop('disabled', true);

            },
            success: function (data) {
                $("#submit_btn_pos_receipt").html('Submit');
                $("#submit_btn").prop('disabled', false); // Please comment it later

                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    loadPrintTemplate();
                } else {
                    myAlert('d', data['message']);
                }
                resetPaymentForm();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#submit_btn_pos_receipt").html('Submit');
                $("#submit_btn").prop('disabled', false);
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function print_pos_report() {
        $.print("#print_content");
        return false;
    }

    function prepareforDoubleEntry(warehouseID, counterID) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Pos_restaurant/prepareforDoubleEntry'); ?>",
            data: $(".form_pos_receipt").serialize(),
            cache: false,
            beforeSend: function () {
                $("#submit_btn_pos_receipt").html('<i class="fa fa-refresh fa-spin"> </i> Submit');
                $("#submit_btn").prop('disabled', true);

            },
            success: function (data) {
                $("#submit_btn_pos_receipt").html('Submit');
                $("#submit_btn").prop('disabled', false); // Please comment it later

                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    loadPrintTemplate();
                } else {
                    myAlert('d', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $("#submit_btn_pos_receipt").html('Submit');
                $("#submit_btn").prop('disabled', false);
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        })

    }




</script>
<script type="text/javascript" language="javascript">

    var validNavigation = false;

    function endSession() {
        //console.log('session is out..');
    }

    function wireUpEvents() {
        /*
         * For a list of events that triggers onbeforeunload on IE
         * check http://msdn.microsoft.com/en-us/library/ms536907(VS.85).aspx
         */
        window.onbeforeunload = function () {
            if (!validNavigation) {
                endSession();
            }
        }

// Attach the event keypress to exclude the F5 refresh
        $(document).bind('keypress', function (e) {
            if (e.keyCode == 116) {
                validNavigation = true;
            }
        });

// Attach the event click for all links in the page
        $("a").bind("click", function () {
            validNavigation = true;
        });

        // Attach the event submit for all forms in the page
        $("form").bind("submit", function () {
            validNavigation = true;
        });

        // Attach the event click for all inputs in the page
        $("input[type=submit]").bind("click", function () {
            validNavigation = true;
        });

    }

    // Wire up the events as soon as the DOM tree is ready
    $(document).ready(function () {
        wireUpEvents();
    });

    function updaterestaurantTable(tmp) {
        var tableType = tmp.value;
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_restaurant/updaterestaurantTable'); ?>",
            data: {tableType: tableType},
            cache: false,
            beforeSend: function () {
                startLoadPos();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }
</script>