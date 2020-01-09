<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos.css') ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/slick/slick/slick.css') ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/slick/slick/slick-theme.css') ?>">
<style>
    .receiptPadding {
        width: 18%;
        float: left;
        text-align: right;
        padding-right: 3px;
    }

    .receiptPaddingHead {
        width: 19%;
        float: left;
        text-align: right;
        padding-right: 3px;
    }

    .btn-lg {
        font-size: 14px !important;
    }

    .al {
        text-align: right;
    }

    .invoiceFooter {
        background-color: #373942;
        padding: 10px;

    }

    .invoiceFooter table td {
        color: #ffffff !important;
        border: 1px solid #373942 !important;
        padding: 3px !important;
        font-size: 12px !important;

    }

    .bgTr {
        background-color: #3d3e4a;
    }
</style>

<?php
$bank_card = load_bank_with_card();
$tr_currency = $this->common_data['company_data']['company_default_currency'];

$this->load->view('include/header', $title);
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

?>
<div class="wrapper">
    <header class="main-header" style="background-color: #ffffff;">

        <!-- Logo -->


        <a href="<?php echo site_url('dashboard'); ?>" class="logo hidden-xs hidden-sm">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>ERP</b></span>
            <!-- logo for regular state and mobile devices -->
            <span
                class="logo-lg"><?php echo '<img style="max-height:30px;"  src="' . base_url() . 'images/' . LOGO . '"/>' ?>  </span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <div id=""></div>
            <!-- Sidebar toggle button-->


            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">

                    <li class="dropdown" id="posPreLoader" style="display: none;">
                        <button type="button" class=" btn btn-lg btn-default " title="Menu"
                                onclick="load_pos_menuList_modal()">
                            <i class="fa fa-refresh fa-spin fa-2x"></i>
                            <!--<br/>Open-->
                        </button>
                    </li>

                    <li class="dropdown hidden-lg hidden-md">
                        <button type="button" class=" btn btn-lg btn-default" onclick="aboutThis()" title="Tab view">
                            <i class="fa fa-tablet fa-2x" aria-hidden="true"></i>
                        </button>
                    </li>
                    <li class="dropdown">
                        <div class="form-group" style="margin-bottom: 0;">
                            <?php echo form_dropdown('restaurantTable', getresrestaurantTables_drop($warehouseID), '', 'id="restaurantTable" onchange="updaterestaurantTable(this)" class="form-control" style="height: 50px; font-weight:700;" '); ?>
                        </div>

                    </li>
                    <li class="dropdown">
                        <button type="button" class=" btn btn-lg btn-default " title="Menu"
                                onclick="load_pos_menuList_modal()">
                            <i class="fa fa-list text-green fa-2x" aria-hidden="true"></i>
                            <!--<br/>Open-->
                        </button>
                    </li>


                    <li class="dropdown">
                        <button type="button" class=" btn btn-lg btn-default " rel="tooltip" title="short cut  Ctrl+O "
                                onclick="open_holdReceipt()">
                            <i class="fa fa-folder-open-o text-blue fa-2x" aria-hidden="true"></i>
                            <!--<br/>Open-->
                        </button>
                    </li>

                    <li class="dropdown">
                        <button type="button" class=" btn btn-lg btn-default " onclick="holdReceipt()" rel="tooltip"
                                title="">
                            <i class="fa fa-pause text-yellow fa-2x"></i> <!--<br/> Hold-->
                        </button>

                    </li>
                    <li class="dropdown">
                        <button type="button" class=" btn btn-lg btn-default " onclick="cancelCurrentOrder()"
                                rel="tooltip" title="">
                            <i class="fa fa-times text-red fa-2x"></i>
                        </button>

                    </li>
                    <li class="dropdown">
                        <button type="button" class="btn btn-lg btn-default" onclick="open_kitchen_ready()"
                                rel="tooltip" title="Kitchen">
                            <i class="fa fa-cutlery text-purple fa-2x"></i>
                        </button>
                    </li>


                    <?php if (!empty($posData['wareHouseLocation'])) { ?>

                    <?php }
                    if (!empty($posData['counterDet'])) { ?>
                        <li class="dropdown user user-menu" id="">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <label style="margin-bottom: 0px">&nbsp;<?php echo $posData['counterDet']; ?>
                                    &nbsp;</label>
                            </a>
                        </li>
                    <?php } ?>

                    <li class="dropdown">
                        <button type="button" class="btn btn-lg btn-default" onclick="logoutfromsystem()"
                                rel="tooltip" >
                            <i class="fa fa-power-off text-red fa-2x" title="<?php echo $name = ucwords($this->session->loginusername); ?>" rel="tooltip" data-placement="bottom"></i>
                        </button>
                    </li>

                    <!-- Control Sidebar Toggle Button -->
                    <li class="hidden-xs hide">
                        <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                    </li>
                </ul>
            </div>

        </nav>
    </header>




    <div style="padding: 1%; margin-top: 40px">

        <div class="row" style="margin-top: 0px;">
            <div class="col-md-12" style="padding-right: 0px;">


                <?php current_pc() ?>
                <div class="productCls">
                    <div class="row">
                        <div class="col-md-4 hidden-md hidden-sm hidden-xs hide">
                            <span class="hidden-sm hidden-xs">
                            <?php echo form_dropdown('customerType', getCustomerType_drop(), $defaultCustomerType, 'id="customerType" onchange="updateCustomerType(this)" class="form-control input-sm" style="margin:5px;"'); ?></span>

                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="receiptPaddingHead"><?php echo $this->lang->line('common_description');?><!--Description--></div>
                            <div class="receiptPaddingHead"><?php echo $this->lang->line('posr_qyt');?><!--Qty--></div>
                            <div class="receiptPaddingHead">@<?php echo $this->lang->line('posr_rate');?><!--rate--></div>
                            <div class="receiptPaddingHead"><?php echo $this->lang->line('common_total');?><!--Total--></div>
                            <div class="receiptPaddingHead hide"><?php echo $this->lang->line('posr_dist');?><!--Dist-->.<br/>%</div>
                            <div class="receiptPaddingHead hide"><?php echo $this->lang->line('posr_dist');?><!--Dis-->.<br/>Amt.</div>
                            <div class="receiptPaddingHead"><?php echo $this->lang->line('posr_net_total');?><!--Net Total--></div>
                        </div>

                    </div>
                </div>

                <div style="overflow: scroll; height: 245px; width: 100%;">
                    <form id="posInvoiceForm" class="form_pos_receipt" method="post">
                        <div id="log">




                        </div>
                    </form>
                </div>

                <form class="form_pos_receipt" method="post">
                    <div class="invoiceFooter">
                        <table class="table table-condensed">

                            <tbody>
                            <tr>
                                <td><?php echo $this->lang->line('posr_total_item');?><!--Total Items--></td>
                                <td>:</td>
                                <td class="al">
                                    <div id="total_item_qty">0</div>
                                    <input type="hidden" id="total_item_qty_input" name="total_item_qty_input"/>
                                </td>
                            </tr>
                            <tr class="bgTr">
                                <td><?php echo $this->lang->line('posr_gross_total');?><!--Gross Total--></td>
                                <td>:</td>
                                <td class="al">
                                    <div id="gross_total">0</div>
                                    <input type="hidden" id="gross_total_input" name="gross_total_input"/>
                                </td>
                            </tr>
                            <tr class="hide">
                                <td>
                                    <?php echo $this->lang->line('posr_discount');?><!--Discount--> % :
                                    <input maxlength="3" onkeyup="calculateFinalDiscount()"
                                           type="number"
                                           style="color: black; width: 38px; font-weight: 800; text-align: right;"
                                           name="discount_percentage" id="dis_amt" value="0">
                                </td>
                                <td>:</td>
                                <td class="al" style=" border-bottom: 1px solid #ffffff !important;">
                                    <div id="total_discount">0.00</div>
                                    <input type="hidden" id="total_discount_amount" name="total_discount_amount"/>
                                </td>
                            </tr>

                            <tr class="bgTr">
                                <td>
                                    <?php echo $this->lang->line('common_total');?><!--Total-->
                                </td>
                                <td>:</td>
                                <td class="al">
                                    <div id="totalWithoutTax">0</div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <?php echo $this->lang->line('posr_total_tax');?><!--Total Tax--> (<?php echo get_totalTax() ?>%) :
                                    <input type="hidden" name="totalTax_input" value="<?php echo get_totalTax() ?>">
                                </td>
                                <td>:</td>
                                <td class="al">
                                    <div id="display_tax_amt">0</div>
                                    <input type="hidden" name="display_tax_amt_input" id="display_tax_amt_input"
                                           value="0">
                                </td>
                            </tr>

                            <tr class="bgTr hide">
                                <td>
                                    <?php echo $this->lang->line('posr_service_charge');?><!--Service Charge-->
                                </td>
                                <td>:</td>
                                <td class="al">
                                    <input type="text" onkeyup="calculateFinalDiscount()" id="serviceCharge"
                                           name="serviceCharge" class="ar"
                                           style="color:#000000; width:100px;" value="<?php
                                    $sc = get_defaultServiceCharge();
                                    echo !empty($sc) ? $sc : 0;
                                    ?>">
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <?php echo $this->lang->line('posr_net_total');?><!--Net Total-->
                                </td>
                                <td>:</td>
                                <td class="al"
                                    style="width:25%; border-top: 2px solid #ffffff !important; border-bottom: 2px solid #ffffff !important;">
                                    <div id="total_netAmount" style="font-weight: 800; font-size:16px;">0</div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </form>

                <div style="width: 100%; height: 40px; margin-top: 10px;">

                    <div class="row">
                        <div class="col-md-3">


                        </div>
                        <div class="col-md-3">

                        </div>
                        <div class="col-md-3">

                        </div>

                        <div class="col-md-3">

                        </div>
                    </div>
                </div>
            </div>
        </div> <!--/ row -->


        <div class="row">
            <div class="col-md-12">

            </div>
        </div>
    </div>

    <div id="posHeader_1">
        <div class="row" id="displayDet" style="background-color: #5b5b63; display: none;">
            <div class="col-12" style="color: #eff7ff !important;">

                <div class="col-md-2 ">
                    <div class="form-group" style="margin-bottom: 2px">
                        <label for="" class="cols-sm-2"><?php echo $this->lang->line('posr_cashier');?><!--Cashier--> : </label>
                        <span class=""><?php echo ucwords($this->session->loginusername); ?></span>
                    </div>
                </div>

                <div class="col-md-2 ">
                    <div class="form-group" style="margin-bottom: 2px">
                        <label for="" class="cols-sm-2"><?php echo $this->lang->line('common_customer');?><!--Customer--> : </label>
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
                        <label for="" class="cols-sm-2 "><?php echo $this->lang->line('posr_ref_no');?><!--Ref No--> : </label>
                        <span class="" style=""><?php echo $refNo; ?></span>
                    </div>
                </div>

                <div class="col-md-2 pull-right" id="currency_masterDiv">
                    <div class="form-group pull-right" id="currency_Div" style="margin-bottom: 2px; padding-right: 10%">
                        <label for="" class="cols-sm-2 "><?php echo $this->lang->line('common_currency');?><!--Currency--> : </label>
                        <span class="trCurrencySpan"><?php echo $tr_currency; ?></span>
                    </div>
                </div>
            </div>
        </div>

    </div>



    <div aria-hidden="true" role="dialog" tabindex="-1" id="pos_addonList_modal" class="modal fade"
         data-keyboard="true"
         data-backdrop="static">
        <div class="modal-dialog" style="width:90%">
            <div class="modal-content">
                <div class="modal-header posModalHeader">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                    <h4 class="modal-title"> <?php echo $this->lang->line('posr_add_on');?><!--Add On--> </h4>
                </div>
                <div class="modal-body" id="addonlist">

                </div>
                <div class="modal-footer" style="padding:5px;">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="saveAddon()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                </div>
            </div>
        </div>
    </div>


    <?php
    $data['notFixed'] = true;
    $data['modal_width'] = 96;
    $data['noChart'] = true;
    $this->load->view('include/footer', $data);
    //$this->load->view('system/pos/modals/pos-modal-payments', $data);
    $this->load->view('system/pos/modals/rpos-modal-hold-receipt', $data);
    //$this->load->view('system/pos/modals/rpos-modal-print-template', $data);
    $this->load->view('system/pos/modals/rpos-modal-pack-invoice', $data);
    $this->load->view('system/pos/modals/rpos-modal-menu-list-touch', $data);
    ?>


    <script type="text/javascript" src="<?php echo base_url('plugins/keyboard-short-cut/shortcut.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('plugins/pos/pos_touch.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/slick/slick/slick.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('plugins/printJS/jQuery.print.js') ?>" type="text/javascript"></script>
    <script type="text/javascript">

        var dPlaces = <?php echo $this->common_data['company_data']['company_default_decimal'];?>; // Don't delete NASIK

        function aboutThis() {
            bootbox.alert('<div class="alert alert-info text-center" style="background-color: #ffffff !important; color: #000000 !important; border-radius: 0px !important;"><strong><i class="fa fa-tablet fa-2x" aria-hidden="true"></i> <br/>About</strong> <br/>  Tablet / Mobile POS - version 1.0</div>');
        }
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
        function formatState(state) {
            if (!state.id) {
                return state.text;
            }
            var $state = $(
                '<span><img src="<?php echo base_url()?>images/payment_type/' + state.element.value.toLowerCase() + '.png" class="img-flag" />  ' + state.text + '</span>'
            );
            return $state;
        }


        $(document).on('ready', function () {
            checkPosSession();
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
                data: {id: id, customerType: 1},
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
                        //var divTmp = '<div class="row itemList" id="item_row_' + data['code'] + '" style="margin: 0px; border-bottom: 1px solid #dddddd; padding-top: 5px; padding-bottom: 5px;"><div class="col-md-1 hidden-xs hidden-sm menuItem_pos_col_1"><img src="' + data['menuImage'] + '" style="max-height: 40px;" alt=""> </div><div class="col-md-3 menuItem_pos_col_5">' + data['menuMasterDescription'] + ' <br>[' + data['warehouseMenuID'] + ']</div><div class="col-md-8"> <div class="receiptPadding"> <input type="text" onkeyup="calculateFooter()" value="1" class="display_qty menuItem_input numberFloat" name="qty[' + data['code'] + ']"  /> </div> <div class="receiptPadding"> <span class="menu_itemCost menuItemTxt"> ' + data['sellingPrice'] + '</span> <!-- @rate --> <input type="hidden"  class="menuItemTxt_input numberFloat" name="sellingPrice[' + data['code'] + ']" value="' + data['sellingPrice'] + '"/> </div> <div class="receiptPadding"> <span class="menu_total menuItemTxt">0</span>  <!-- total --> </div> <div class="receiptPadding"> <input style="width:60%;" onkeyup="calculateFooter(\'P\')" name="discountPercentage[' + data['code'] + ']"  maxlength="3" type="text" value="0" class="menu_discount_percentage menu_qty menuItem_input numberFloat"> <!-- disc. % --> </div> <div class="receiptPadding"> <input style="width:90%;" onkeyup="calculateFooter(\'A\')" name="discountAmount[' + data['code'] + ']" type="text" value="0" class="menu_discount_amount menu_qty menuItem_input numberFloat"><!-- disc. amount --> </div> <div class="receiptPadding"> <div style="width:55px; text-align: right;" class="itemCostNet menuItemTxt"> [' + data['sellingPrice'] + '</div> <!-- net total --> <div onclick="deleteDiv(' + data['code'] + ')" data-placement="bottom" rel="tooltip" title="Delete" style="cursor:pointer; width: 12px; margin-top: -16px;     margin-right: -13px;" class="pull-right"><i class="fa fa-close closeColor"></i></div> </div> </div></div>';
                        var divTmp = '<div class="row itemList" id="item_row_' + data['code'] + '" style="margin: 0px; border-bottom: 1px solid #dddddd; padding-top: 5px; padding-bottom: 5px;"> <div class="col-md-12" style="padding-left: 0;"> <button class="pull-left btn btn-lg btn-default" type="button" style="margin-right: 0.5%;" onclick="openaddonList(' + data['code'] + ',' + data['tmpInvoiceID_code'] + ')"><i class="fa fa-plus fa-lg text-green"   aria-hidden="true" ></i></button> <div class="receiptPadding" style="text-align: left; font-weight: 800;cursor: pointer;"> ' + data['menuMasterDescription'] + ' </div> <div class="receiptPadding"><input style="max-width: 80px;" type="text" onkeyup="calculateFooter()" value="1" class="display_qty menuItem_input numberFloat" name="qty[' + data['code'] + ']"/></div> <div class="receiptPadding"><span class="menu_itemCost menuItemTxt"> ' + data['sellingPrice'] + '</span> <!-- @rate --> <input type="hidden" class="menuItemTxt_input numberFloat" name="sellingPrice[' + data['code'] + ']" value="' + data['sellingPrice'] + '"/> <input type="hidden"  class="menuItemTxt_input numberFloat pricewithoutTax" name="pricewithoutTax[' + data['code'] + ']" value="' + data['pricewithoutTax'] + '"/> <input type="hidden"  class="menuItemTxt_input totalMenuTaxAmount numberFloat" name="totalMenuTaxAmount[' + data['code'] + ']" value="' + data['totalTaxAmount'] + '"/><input type="hidden"  class="menuItemTxt_input numberFloat totalMenuServiceCharge" name="totalMenuServiceCharge[' + data['code'] + ']" value="' + data['totalServiceCharge'] + '"/><input type="hidden"  name="frm_isTaxEnabled[' + data['code'] + ']" value="' + data['isTaxEnabled'] + '"/></div> <div class="receiptPadding"><span class="menu_total menuItemTxt">0</span> <!-- total --> </div> <div class="receiptPadding hide"><input style="width:60%;" onkeyup="calculateFooter(\'P\')" name="discountPercentage[' + data['code'] + ']" maxlength="3" type="text" value="0" class="menu_discount_percentage menu_qty menuItem_input numberFloat"> <!-- disc. % --> </div> <div class="receiptPadding hide"><input style="width:90%;" onkeyup="calculateFooter(\'A\')" name="discountAmount[' + data['code'] + ']" type="text" value="0" class="menu_discount_amount menu_qty menuItem_input numberFloat"> <!-- disc. amount --> </div> <div class="receiptPadding" style="width:19%;"> <div style="text-align: right;" class="itemCostNet menuItemTxt"> [' + data['sellingPrice'] + ' </div> <!-- net total --> <button onclick="deleteDiv(' + data['code'] + ')" data-placement="bottom" rel="tooltip" type="button" title="Delete" style="cursor:pointer; margin-top: -20px;margin-right: -47px;" class="pull-right  btn btn-default itemList-delBtn"><i class="fa fa-close closeColor"></i></button> </div> </div> </div>';


                        $("#log").append(divTmp);
                        calculateFooter();
                        $("[rel='tooltip']").tooltip();

                        $("#pos_salesInvoiceID_btn").html(data['tmpInvoiceID']);
                        $("#holdInvoiceID_input").val(data['tmpInvoiceID_code']);
                        $("#holdInvoiceID").val(data['tmpInvoiceID']);

                        if (data['isPack'] == 1) {
                            savePackDetailItemList(data['code']);
                        }
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
                url: "<?php echo site_url('Pos_restaurant/Load_pos_holdInvoiceData_touch'); ?>",
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

        function openmodelremarks(itmID, invoiceID, remark) {
            $("#pos_menuList_remarks_modal").modal('show');
            $("#menuItemRemarkes").val(remark);
            $("#invoiceIDMenusales").val(invoiceID);
            $("#itmID").val(itmID);

        }

        function saveMenuSalesItemRemarkes() {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo site_url('Pos_restaurant/saveMenuSalesItemRemarkes'); ?>",
                data: $("#menuItemRemarkesfrm").serialize(),
                cache: false,
                beforeSend: function () {
                    startLoad()
                },
                success: function (data) {
                    stopLoad();
                    if (data['error'] == 0) {
                        $("#pos_menuList_remarks_modal").modal('hide');
                        myAlert('s', data['message']);
                        $("[rel='tooltip']").tooltip();
                        Load_pos_holdInvoiceData(data['invoiceID'])
                    } else {
                        myAlert('e', data['message']);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            })

        }

        function logoutfromsystem() {
            bootbox.confirm('<div style="font-size: 12px; color: #bc9d00; line-height: 21px;"><strong><i class="fa fa-check fa-2x"></i> Confirmation </strong> <br/>Are you Sure you want to Logout ?</div>', function (result) {
                if (result) {
                    window.location.href = '<?php echo site_url('Login/logout'); ?>';
                }
            });

        }

        function openaddonList(id, invoiceID, remark) {

            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: "<?php echo site_url('Pos_restaurant/get_add_on_list'); ?>",
                data: {menuSalesItemID: id},
                cache: false,
                beforeSend: function () {
                    startLoad()
                },
                success: function (data) {
                    stopLoad();
                    $("#pos_addonList_modal").modal('show');
                    $("#addonlist").html(data);
                    $("#menuItemRemarkes").val(remark);
                    $("#invoiceIDMenusales").val(invoiceID);
                    $("#itmID").val(id);

                },
                error: function () {
                    stopLoad();
                }
            })
        }

        function saveAddon() {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo site_url('Pos_restaurant/saveAddon'); ?>",
                data: $("#addonlistfrm").serialize(),
                cache: false,
                beforeSend: function () {
                    startLoad()
                },
                success: function (data) {
                    stopLoad();
                    if (data['0'] == 's') {
                        $("#pos_addonList_modal").modal('hide');
                        myAlert('s', data[1]);
                        Load_pos_holdInvoiceData(data[2])
                    } else {
                        myAlert('e', data[1]);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            })
        }


    </script>