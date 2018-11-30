<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos2.css') ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/slick/slick/slick.css') ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/slick/slick/slick-theme.css') ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/buttons/button.css') ?>">

<?php
$bank_card = load_bank_with_card();
$tr_currency = $this->common_data['company_data']['company_default_currency'];

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$this->load->view('include/header', $title);
$this->load->view('include/top-posr');

$gradient1 = '60%';
$gradient2 = '150%';
?>

<div id="posHeader_2" style="display: none;">
    <table id="posHeader_2_TB">
        <tr>
            <td width="90px"><?php echo $this->lang->line('posr_cashier'); ?><!--Cashier--></td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td style="padding-left: 0px !important;"><?php echo ucwords($this->session->loginusername); ?></td>
            <td width="90px"><?php echo $this->lang->line('common_customer'); ?><!--Customer--></td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td style="padding-left: 0px !important;"><span
                    class="customerSpan"><?php echo $this->lang->line('posr_cash'); ?><!--Cash--></span></td>
        </tr>
        <tr>
            <td><?php echo $this->lang->line('posr_no_of_items'); ?><!--No of Items--></td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td class="itemCount" style="padding-left: 0px !important;">0</td>
            <td><?php echo $this->lang->line('posr_sales_mode'); ?><!--Sales Mode--></td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td style="padding-left: 0px !important;"><?php echo $this->lang->line('posr_retail'); ?><!--Retail--></td>
        </tr>
        <tr>
            <td><?php echo $this->lang->line('posr_ref_no'); ?><!--Ref No--></td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td style="padding-left: 0px !important;"><?php echo $refNo; ?></td>
            <td><?php echo $this->lang->line('common_currency'); ?><!--Currency--></td>
            <td width="10px" style="padding-left: 0px !important;">:</td>
            <td style="padding-left: 0px !important;"><span
                    class="trCurrencySpan"><?php echo $tr_currency; ?></span></td>
        </tr>
    </table>
</div>
<div id="form_div" style="padding: 1%; margin-top: 40px">

    <div class="row" style="margin-top: 0px;">
        <div class="col-md-6">

            <div class="panel panel-default" style="border: 1px solid #ddd;">
                <div class="panel-body tabs" style="padding:3px;">
                    <div style="padding: 0px 0px 0px 15px;">
                        <button id="backToCategoryBtn" data-toggle="tab"
                                class="btn btn-lg btn-default btnCategoryTab pos2-btn-default"
                                tabindex="-1"
                                href="#pilltabCategory">
                            <i class="fa fa-backward fa-2x"></i>
                        </button>

                        &nbsp;&nbsp;&nbsp;
                        <?php
                        for ($i = 0; $i < 10; $i++) {
                            ?>
                            <button onclick="updateQty_invoice(this)" class="btn btn-lg btn-primary fSizeBtn "
                                    style="background: linear-gradient(to bottom, #6dc3f5 20%,#367fa9 100%) !important;">
                                <?php echo $i; ?>
                            </button>
                            <?php
                        }
                        ?>
                        <button rel="tooltip" title="clear" onclick="updateQty_invoice(this)"
                                class="btn btn-lg btn-default fSizeBtn pos2-btn-default">C
                        </button>

                    </div>

                    <div class="row" style="margin-left: 0px; margin-right: 0px;">
                        <div class="col-md-12" style="padding-left: 15px; padding-right: 15px; padding-top: 10px;">
                            <input type="text" class="form-control" placeholder="Press 'F2' or 'Ctrl+F' to Search"
                                   id="searchProd">
                        </div>
                    </div>


                    <div style="margin: 0px 0px 0px 15px;">
                        <div class="tab-content" id="allProd">
                            <div class="tab-pane fade in active" id="pilltabCategory">

                                <?php
                                if (!empty($menuCategory)) {

                                    foreach ($menuCategory as $Category) {
                                        ?>
                                        <div class="btnStyleCustom">
                                            <button type="button" data-toggle="tab" tabindex="-1"
                                                    style="<?php echo 'background: linear-gradient(to bottom, ' . $Category['bgColor'] . ' ' . $gradient1 . ', #ffffff ' . $gradient2 . ')'; // $menu['bgColor']'; ?><?php if (!empty($Category['bgColor'])) {
                                                        //echo $Category['bgColor'];
                                                    } ?>"
                                                    href="#pilltab<?php echo $Category['autoID'] ?>"
                                                    class="itemButton btnCategoryTab">
                                        <span id="proname">
                                            <?php echo strtoupper($Category['description']) ?>
                                        </span>
                                            </button>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>


                            <div class="tab-pane fade in" id="pilltabAll">
                                <?php

                                if (isset($menuItems) && !empty($menuItems)) {
                                    foreach ($menuItems as $menuItem) {
                                        break;
                                        $isPack = $menuItem['isPack'];
                                        ?>
                                        <button type="button" data-code="<?php echo $menuItem['warehouseMenuID'] ?>"
                                                data-pack="<?php echo $isPack ?>"
                                                value="item<?php echo $menuItem['warehouseMenuID'] ?>"
                                                <?php if ($isPack){ ?>style="background-color: #E8ECAE;"<?php } ?>
                                                class="btn btn-lg btn-default itemButton"
                                                onclick="LoadToInvoice<?php if ($isPack == 1) {
                                                    echo "Pack";
                                                } ?>(<?php echo $menuItem['warehouseMenuID'] ?>)">


                                            <span id="proname">
                                            <?php echo strtoupper($menuItem['menuMasterDescription']) ?>
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
                                                <div class="btnStyleCustom">
                                                    <button data-code="<?php echo $menu['warehouseMenuID'] ?>"
                                                            data-pack="<?php echo $isPack ?>"
                                                            value="item<?php echo $menu['warehouseMenuID'] ?>"
                                                            style="<?php echo 'background: linear-gradient(to bottom, ' . $menu['bgColor'] . ' ' . $gradient1 . ', #ffffff ' . $gradient2 . ')'; ?>"
                                                            class=" itemButton"
                                                            onclick="<?php if ($isPack == 1) {
                                                                ?>checkisKOT(<?php echo $menu['warehouseMenuID'] ?>, 1, <?php  echo  $menu['kotID'] ?>, '<?php  echo strtoupper($menu['menuMasterDescription']); ?>')<?php
                                                            } else {
                                                                ?>checkisKOT(<?php echo $menu['warehouseMenuID'] ?>, 0, <?php  echo  $menu['kotID'] ?>,'<?php  echo strtoupper($menu['menuMasterDescription']); ?>')<?php
                                                            } ?>">


                                                        <span id="proname">
                                                        <?php
                                                        //echo strtoupper($menu['menuMasterDescription']);
                                                        if ($isPack) {
                                                            echo '' . strtoupper($menu['menuMasterDescription']);
                                                            if (!empty($menu['sizeDescription'])) {
                                                                echo '<div style="font-size: 13px; font-weight: 600; padding:4px;">  ' . $menu['sizeDescription'] . '  </div>';
                                                            }
                                                            echo '<div style="padding:5px;"><i class="fa fa-star " style="color:darkgoldenrod"></i><i class="fa fa-star " style="color:darkgoldenrod"></i><i class="fa fa-star " style="color:darkgoldenrod"></i></div>';
                                                        } else {
                                                            echo strtoupper($menu['menuMasterDescription']);
                                                            if (!empty($menu['sizeDescription'])) {
                                                                echo '<div style="font-size: 13px; font-weight: 600; padding:4px;">  ' . $menu['sizeDescription'] . '  </div>';
                                                            }
                                                        }
                                                        if (!empty($menu['sizeDescription']) && false) {
                                                            if (!empty($menu['colourCode']) && false) {
                                                                echo '<br/><h6><span style="background-color:' . $menu['colourCode'] . '; " class="label label-default">' . $menu['sizeDescription'] . '</span></h6>';
                                                            } else {
                                                                echo '<div style="font-size: 13px; font-weight: 600; padding:4px;">  ' . $menu['sizeDescription'] . '  </div>';

                                                            }


                                                        }

                                                        ?>

                                                    </span>
                                                    </button>
                                                </div>

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
            </div>


        </div> <!-- / col-md-6 -->

        <div class="col-md-5" style="padding-right: 0px;">
            <div>
                <button class="btn btn-sm btn-default btn-block" style="border-radius: 0px;">
                    Order : <strong id="pos_salesInvoiceID_btn">
                                <span>
                                <?php
                                $new = $this->lang->line('common_new');
                                $invoiceID = isPos_invoiceSessionExist();
                                if (!empty($invoiceID)) {
                                    $id = str_pad($invoiceID, 4, "0", STR_PAD_LEFT);
                                    echo $id;
                                } else {
                                    echo '<span class="label label-danger">' . $new . '<!--New--></span>';
                                }
                                ?>
                                    </span>
                    </strong>
                </button>
            </div>

            <?php current_pc() ?>
            <div class="productCls pos2-div-bill-bg">
                <div class="row" style="padding:4px 0px;">
                    <div class="col-md-4">

                    </div>
                    <div class="col-md-8">
                        <div class="receiptPaddingHead"><?php echo $this->lang->line('posr_qyt'); ?><!--Qty--></div>
                        <div class="receiptPaddingHead">@<?php echo $this->lang->line('posr_rate'); ?><!--rate--></div>
                        <div class="receiptPaddingHead">
                            <?php echo $this->lang->line('common_total'); ?><!--Total--></div>
                        <div class="receiptPaddingHead hide"><?php echo $this->lang->line('posr_dist'); ?><br/>
                            <!--Dist-->.%
                        </div>
                        <div class="receiptPaddingHead hide"><?php echo $this->lang->line('posr_dist'); ?><!--Dis-->
                            .<br/><?php echo $this->lang->line('common_amount'); ?><!--Amt-->.
                        </div>
                        <div class="receiptPaddingHead"><?php echo $this->lang->line('posr_net'); ?><!--Net-->
                            <?php echo $this->lang->line('common_total'); ?><!--Total--></div>
                    </div>

                </div>
            </div>

            <div style="width: 100%;" id="container_orderList"> <!--overflow: scroll; height: 255px; -->
                <form id="posInvoiceForm" class="form_pos_receipt" method="post">
                    <div id="log">


                    </div>
                </form>
            </div>

            <form class="form_pos_receipt" method="post">
                <div class="itemListContainer pos2-div-bill-bg">

                    <div class="row itemListFoot">
                        <div class="col-md-4"><?php echo $this->lang->line('posr_total_item'); ?><!--Total Items-->:
                        </div>
                        <div class="col-md-2">
                            <div id="total_item_qty">0</div>
                            <input type="hidden" id="total_item_qty_input" name="total_item_qty_input"/>
                        </div>
                        <div class="col-md-3 ar"><?php echo $this->lang->line('posr_total_item'); ?><!--Gross Total-->
                            :
                        </div>
                        <div class="col-md-3 ar">
                            <div id="gross_total">0</div>
                            <input type="hidden" id="gross_total_input" name="gross_total_input"/>
                        </div>
                    </div>

                    <div class="row itemListFoot">
                        <div class="col-md-4">

                        </div>
                        <div class="col-md-1">

                        </div>
                        <div class="col-md-4 ar">
                            <?php echo $this->lang->line('posr_discount'); ?><!--Discount--> % :
                            <input maxlength="3" onchange="updateDiscount()" onkeyup="calculateFinalDiscount()"
                                   type="text" onclick="selectMenuItem(this)"
                                   style="color: black; width: 38px; font-weight: 800; text-align: right;"
                                   name="discount_percentage" id="dis_amt" value="0">
                        </div>
                        <div class="col-md-3 ar" style="border-bottom: 0px solid #ffffff; padding-bottom: 5px;">
                            <div id="total_discount">0.00</div>
                            <input type="hidden" id="total_discount_amount" name="total_discount_amount"/>
                        </div>
                    </div>

                    <div class="row itemListFoot hide">
                        <div class="col-md-4">&nbsp;</div>
                        <div class="col-md-2">
                            <div>&nbsp;</div>
                        </div>
                        <div class="col-md-3 ar"> <?php echo $this->lang->line('common_total'); ?><!--Total-->
                        </div>
                        <div class="col-md-3 ar">
                            <div id="totalWithoutTax">0</div>
                        </div>
                    </div>

                    <div class="row itemListFoot hide">
                        <div class="col-md-4">&nbsp;</div>
                        <div class="col-md-2">
                            <div>&nbsp;</div>
                        </div>
                        <div class="col-md-3 ar">
                            <?php echo $this->lang->line('posr_total_tax'); ?><!--Total Tax -->
                            (<?php echo get_totalTax() ?> %) :
                            <input type="hidden" name="totalTax_input" value="<?php echo get_totalTax() ?>">
                        </div>
                        <div class="col-md-3 ar hide" style="border-bottom: 3px solid #ffffff; padding-bottom: 5px;">
                            <div id="display_tax_amt">0</div>
                            <input type="hidden" name="display_tax_amt_input" id="display_tax_amt_input" value="0">
                        </div>
                    </div>

                    <div class="row itemListFoot hide">
                        <div class="col-md-4">&nbsp;</div>
                        <div class="col-md-2">
                            <div>&nbsp;</div>
                        </div>
                        <div class="col-md-3 ar hide">
                            <?php echo $this->lang->line('posr_service_charge'); ?><!--Service Charge--> :
                        </div>
                        <div class="col-md-3 ar hide" style="border-bottom: 3px solid #ffffff; padding-bottom: 5px;">
                            <input type="text" onkeyup="calculateFinalDiscount()" id="serviceCharge"
                                   name="serviceCharge" class="ar"
                                   style="color:#000000; width:100px;" value="<?php
                            $sc = get_defaultServiceCharge();
                            echo !empty($sc) ? $sc : 0;
                            ?>">
                        </div>
                    </div>


                    <div class="row itemListFoot">
                        <div class="col-md-4">&nbsp;</div>
                        <div class="col-md-2">
                            <div>&nbsp;</div>
                        </div>
                        <div class="col-md-3 ar"> <?php echo $this->lang->line('posr_net_total'); ?><!--Net Total-->:
                        </div>
                        <div class="col-md-3 ar" style="border-top:2px solid #ffffff">
                            <div id="total_netAmount" style="font-weight: 800; font-size:16px;">0</div>
                        </div>
                    </div>

                </div>
            </form>

            <input type="hidden" id="customerTypeBtnString" value="">
            <!-- 2017-03-09 -->
            <?php
            $customerType = getCustomerType();
            //print_r($customerType);
            if (!empty($customerType)) {
                ?>
                <input type="hidden" id="customerType" name="customerType" value="">
                <div>
                    <div class="btn-group btn-group-lg">
                        <?php foreach ($customerType as $val) { ?>
                            <button type="button" onclick="updateCustomerTypeBtn(<?php echo $val['customerTypeID']; ?>)"
                                    class="btn  <?php if ($val['isDefault'] == 1) {
                                        echo 'btn-primary';
                                    } else {
                                        echo 'btn-default';
                                    }
                                    ?>  customerType"
                                    id="customerTypeID_<?php echo $val['customerTypeID']; ?>"> <?php echo $val['customerDescription']; ?></button>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

        </div>

        <div class="col-md-1">
            <div class="mainBtnList">
                <?php
                $confomion = $this->lang->line('common_confirmation');
                $message = $this->lang->line('common_are_you_sure_you_want_to_close_the_counter');
                $cancel = $this->lang->line('common_cancel');
                $ok = $this->lang->line('common_ok');

                ?>
                <button type="button" class="btn btn-block btn-lg btn-default pos2-btn-default"
                        onclick="session_close('<?php echo $confomion ?>','<?php echo $message ?>','<?php echo $cancel ?>','<?php echo $ok ?>')">
                    <i class="fa fa-power-off text-red" aria-hidden="true"></i>
                    <br/><?php echo $this->lang->line('posr_power'); ?> <!--Power-->
                </button>

            </div>

            <!--<button type="button" class="btn btn-block btn-lg btn-default" onclick="NewBill()">
                <i class="fa fa-plus text-blue" aria-hidden="true"></i> <br>New Bill
            </button>
            <br>-->
            <div class="mainBtnList">
                <button type="button" class="btn btn-block btn-lg btn-default pos2-btn-default" rel="tooltip"
                        title="short cut  Ctrl+O "
                        onclick="open_holdReceipt()">
                    <i class="fa fa-external-link-square text-purple" aria-hidden="true"></i> <br/>
                    <?php echo $this->lang->line('posr_open'); ?><!--Open-->
                </button>
            </div>

            <div class="mainBtnList">
                <button class="btn btn-block btn-lg btn-success" onclick="open_pos_payments_modal()"
                        style="     background: linear-gradient(to bottom, #19bd72 <?php echo $gradient1 ?>, #008d4c <?php echo $gradient2 ?>) !important;">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i><br/> &nbsp;
                    <?php echo $this->lang->line('common_pay'); ?><!--Pay--> (F1)
                </button>
            </div>

            <div class="mainBtnList">
                <a href="#holdmodel" data-toggle="modal" style="text-decoration: none;">
                    <button class="btn btn-block btn-lg btn-danger dangerCustom2" rel="tooltip"
                            title="short cut  Ctrl+S "
                            onclick="holdReceipt();"
                            style="background: linear-gradient(to bottom, #d61f19 <?php echo $gradient1 ?>, #8c1a17 <?php echo $gradient2 ?>) !important;">
                        <i class="fa fa-pause" aria-hidden="true"></i> &nbsp;<br>
                        <?php echo $this->lang->line('posr_hold'); ?><!--Hold-->
                    </button>
                </a>
            </div>

            <div class="mainBtnList">
                <button class="btn btn-lg btn-danger btn-block dangerCustom" onclick="cancelCurrentOrder()"
                        style="    background: linear-gradient(to bottom, #d61f19 <?php echo $gradient1 ?>, #8c1a17 <?php echo $gradient2 ?>) !important;">
                    <i class="fa fa-times" aria-hidden="true"></i> &nbsp;<br/>
                    <?php echo $this->lang->line('common_cancel'); ?><!--Cancel-->
                </button>
            </div>

            <div class="mainBtnList">
                <button type="button" class="btn btn-block btn-lg btn-default pos2-btn-default" rel="tooltip"
                        title="short cut  Ctrl+O "
                        onclick="open_kitchen_ready()">
                    <i class="fa fa-cutlery text-purple" aria-hidden="true"></i> <br/>
                    <?php echo $this->lang->line('posr_kitchen'); ?><!--Kitchen-->
                </button>
            </div>

            <div class="mainBtnList">
                <button type="button" class="btn btn-block btn-lg btn-default pos2-btn-default"
                        onclick="open_void_Modal()">
                    <i class="fa fa-ban text-red" aria-hidden="true"></i> <br/>
                    <?php echo $this->lang->line('common_closed'); ?><!--Closed--> <br/>
                    <?php echo $this->lang->line('posr_bills'); ?><!--Bills-->
                </button>
            </div>

        </div>
    </div> <!--/ row -->
</div>

<div id="posHeader_1">
    <div class="row" id="displayDet" style="background-color: #5b5b63; display: none;">
        <div class="col-12" style="color: #eff7ff !important;">

            <div class="col-md-2 ">
                <div class="form-group" style="margin-bottom: 2px">
                    <label for="" class="cols-sm-2"><?php echo $this->lang->line('posr_cashier'); ?><!--Cashier-->
                        : </label>
                    <span class=""><?php echo ucwords($this->session->loginusername); ?></span>
                </div>
            </div>

            <div class="col-md-2 ">
                <div class="form-group" style="margin-bottom: 2px">
                    <label for="" class="cols-sm-2"><?php echo $this->lang->line('common_customer'); ?><!--Customer-->
                        : </label>
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
                    <label for="" class="cols-sm-2 "><?php echo $this->lang->line('posr_ref_no'); ?><!--Ref No-->
                        : </label>
                    <span class="" style=""><?php echo $refNo; ?></span>
                </div>
            </div>

            <div class="col-md-2 pull-right" id="currency_masterDiv">
                <div class="form-group pull-right" id="currency_Div" style="margin-bottom: 2px; padding-right: 10%">
                    <label for="" class="cols-sm-2 "><?php echo $this->lang->line('common_currency'); ?><!--Currency-->
                        : </label>
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
$this->load->view('system/pos/modals/rpos-modal-void-invoice', $data);
$this->load->view('system/pos/modals/rpos-modal-till', $data);
$this->load->view('system/pos/modals/rpos-modal-kitchen-note', $data);
?>

<?php $this->load->view('system/pos/js/pos-restaurant-common-js', $data); ?>
<script type="text/javascript" src="<?php echo base_url('plugins/keyboard-short-cut/shortcut.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/pos/pos.js'); ?>"></script>
<script src="<?php echo base_url('plugins/slick/slick/slick.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('plugins/printJS/jQuery.print.js') ?>" type="text/javascript"></script>
<!--<script src="<?php /*echo base_url('plugins/tinymce/jquery.tinymce.min.js') */ ?>" type="text/javascript"></script>-->
<!--<script src="<?php /*echo base_url('plugins/tinymce/tinymce.min.js') */ ?>" type="text/javascript"></script>-->
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
        $('#container_orderList').slimScroll({
            height: '255px'
        });
        $('#allProd').slimScroll({
            height: '400px'
        });
        $(".btnCategoryTab").click(function (e) {
            $("#searchProd").val('');
            $("#searchProd").trigger('keyup');
        });
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


    function open_kitchen_note(id, kotID) {
        $("#tmpWarehouseMenuID").val(id);
        if (kotID > 0) {
            $("#pos_kitchen_note").modal('show');
        }
    }

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

    function checkisKOT(id, isPack, kotID, description) {
        if (kotID > 0) {
            open_kitchen_note(id, kotID);
            $("#kot_kotID").val(kotID);
            $("#kitchenNoteDescription").html(description);
            setTimeout(function () {
                $("#kitchenNote").focus();
            }, 500);
        } else {
            $("#frm_kot")[0].reset();
            if (isPack == 1) {
                LoadToInvoicePack(id);
            } else {
                LoadToInvoice(id);
            }
        }
    }


    function LoadToInvoicePack(id) {
        load_packItemList(id);
    }

    function LoadToInvoice(id) {
        var customerType = $("#customerType").val();
        var kotID = $("#kot_kotID").val();
        var kitchenNote = $("#kitchenNote").val();
        console.log(customerType);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_restaurant/LoadToInvoice'); ?>",
            data: {
                id: id,
                customerType: customerType,
                kotID: kotID,
                kitchenNote: kitchenNote
            },
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
                    var divTmp = '<div onclick="selectMenuItem(this)" class="row itemList" id="item_row_' + data['code'] + '" style="margin: 0px; border-bottom: 1px solid #dddddd; padding-top: 5px; padding-bottom: 5px;"><div class="col-md-1 hidden-xs hidden-sm menuItem_pos_col_1 hide"><img src="' + data['menuImage'] + '" style="max-height: 40px;" alt=""> </div><div class="col-md-4 menuItem_pos_col_5">' + data['menuMasterDescription'] + ' </div><div class="col-md-8"> <div class="receiptPadding"> <input type="text" onkeyup="calculateFooter()" onchange="updateQty(' + data['code'] + ')" value="1" class="display_qty menuItem_input numberFloat" id="qty_' + data['code'] + '" name="qty[' + data['code'] + ']"  /> </div> <div class="receiptPadding"> <span class="menu_itemCost menuItemTxt"> ' + data['sellingPrice'] + '</span> <!-- @rate --> <input type="hidden"  class="menuItemTxt_input numberFloat" name="sellingPrice[' + data['code'] + ']" value="' + data['sellingPrice'] + '"/> <input type="hidden"  class="menuItemTxt_input numberFloat" name="pricewithoutTax[' + data['code'] + ']" value="' + data['pricewithoutTax'] + '"/> <input type="hidden"  class="menuItemTxt_input numberFloat" name="totalMenuTaxAmount[' + data['code'] + ']" value="' + data['totalTaxAmount'] + '"/><input type="hidden"  class="menuItemTxt_input numberFloat" name="totalMenuServiceCharge[' + data['code'] + ']" value="' + data['totalServiceCharge'] + '"/></div> <div class="receiptPadding"> <span class="menu_total menuItemTxt">0</span>  <!-- total --> </div> <div class="receiptPadding hide"> <input style="width:60%;" onkeyup="calculateFooter(\'P\')" name="discountPercentage[' + data['code'] + ']"  maxlength="3" type="text" value="0" class="menu_discount_percentage menu_qty menuItem_input numberFloat"> <!-- disc. % --> </div> <div class="receiptPadding hide"> <input style="width:90%;" onkeyup="calculateFooter(\'A\')" name="discountAmount[' + data['code'] + ']" type="text" value="0" class="menu_discount_amount menu_qty menuItem_input numberFloat"><!-- disc. amount --> </div> <div class="receiptPadding"> <div style="width:55px; text-align: right;" class="itemCostNet menuItemTxt"> [' + data['sellingPrice'] + '</div> <!-- net total --> <div onclick="deleteDiv(' + data['code'] + ')" data-placement="bottom" rel="tooltip" title="Delete" style="cursor:pointer; width: 12px; margin-top: -20px;     margin-right: 2px;" class="pull-right itemList-delBtn"><button type="button" class="btn btn-default btn-sm  itemList-delBtn"><i class="fa fa-close closeColor"></i></button></div> </div> </div></div>';

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
                    //myAlert('i', 'Ready for new invoice');

                    /*Set Default as Eat-in*/
                    $('.customerType').each(function () {
                        if ($(this).html().trim() == 'Eat-in') {
                            $(this).click()
                        }
                    })
                }
                if (data['error'] == 0) {
                    $("#dis_amt").val(data['discountPer']);
                    $("#serviceCharge").val(data['serviceCharge']);
                    //myAlert('s', 'Opening Existing Invoice');
                    Load_pos_holdInvoiceData(data['code']);
                    $("#holdInvoiceID_input").val(data['code']);
                    $("#holdInvoiceID").val(data['code']);
                    $("#customerType").val(data['customerTypeID']);
                    $("#customerTypeID_" + data['customerTypeID']).click()
                    selectCustomerButton(data['customerTypeID']);
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
            data: {invoiceID: invoiceID, template: 2},
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
                    calculateReturn();
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
                    //myAlert('s', 'Ready for new invoice');
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
                $("#pos_salesInvoiceID_btn").html('<span class="label label-danger"><?php echo $this->lang->line('common_new');?></span>');
                <!--New-->
                $("#holdInvoiceID_input").val('0');
                $("#holdInvoiceID").val('0');
                calculateFooter();
                $("#paid").val(0);
            }
        });

    }

    function clearSalesInvoice() {
        setTimeout(function () {
            clearInvoice();
            clearPosInvoiceSession();
            $("#pos_salesInvoiceID_btn").html('<span class="label label-danger"><?php echo $this->lang->line('common_new');?></span>');
            <!--New-->
            $("#holdInvoiceID_input").val('0');
            $("#holdInvoiceID").val('0');
            calculateFooter();
            $("#paid").val(0);
            $("#promotionID").val('');
        }, 500);
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

    function validationPayments() {
        var isDelivery = $("#isDelivery").val();
        var isOntimePayment = $("#deliveryPersonID option:selected").data('otp');
        var deliveryPersonID = $("#deliveryPersonID").val();
        if (isDelivery == 1) {
            if (deliveryPersonID > 0) {

            } else {
                myAlert('e', 'Please select Delivery person before submit person.')
                return false;
            }
        } else {
            return true;
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
                validationPayments();
                $("#submit_btn_pos_receipt").html('<i class="fa fa-refresh fa-spin"> </i> <?php echo $this->lang->line('common_submit');?>');
                <!--Submit-->
                $("#submit_btn").prop('disabled', true);

            },
            success: function (data) {
                $("#submit_btn_pos_receipt").html('Submit');
                $("#submit_btn").prop('disabled', false); // Please comment it later
                $("#backToCategoryBtn").click();
                if (data['error'] == 0) {
                    //myAlert('s', data['message']);
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
                $("#submit_btn_pos_receipt").html('<i class="fa fa-refresh fa-spin"> </i> <?php echo $this->lang->line('common_submit');?>');
                <!--Submit-->
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

    /** Table Account Sync */

    /**$(document).ready(function (e) {
        jQuery(document).ready(function ($) {
            working = false;
            var do_sync = function () {
                if (working) {
                    return;
                }
                working = true;
                jQuery.post(
                    "<?php echo $this->config->item("sync_url"); ?>",
                    {},
                    function (ret) {
                        working = false;
                    }
                );
            }
            window.setInterval(do_sync, 10000);
        });
    })*/


</script>
