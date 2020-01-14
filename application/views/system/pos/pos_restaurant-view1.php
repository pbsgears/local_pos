<?php
$bank_card = load_bank_with_card();
$tr_currency = $this->common_data['company_data']['company_default_currency'];

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$this->load->view('include/header', $title);
$this->load->view('include/top-posr');

$d = get_company_currency_decimal();
$companyInfo = get_companyInfo();
$templateInfo = get_pos_templateInfo();
$templateID = get_pos_templateID();
$discountPolicy = show_item_level_discount();

?>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos-min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos-style-all-device.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/slick/slick/slick.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/slick/slick/slick-theme.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/numPadmaster/jquery.numpad.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/virtual-keyboard-mlkeyboard/jquery.ml-keyboard.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/buttons/button.css') ?>">
    <style>
        .receiptPadding {
            width: <?php echo $discountPolicy ? '16.5%' : '24.5%';  ?>;
            float: left;
            text-align: right;
            padding-right: 3px;
        }

        .receiptPaddingHead {
            width: <?php echo $discountPolicy ? '15%' : '20%';  ?>;
            float: left;
            text-align: right;
            padding-right: 3px;
        }
    </style>
    <script>
        /** Sync varibale init */
        var xhr = $.ajax();
    </script>

    <div id="posHeader_2" class="hide" style="display: none;"></div>
    <div id="form_div" style="padding: 1%; margin-top: 40px">
        <div class="row" style="margin-top: 0px;">
            <div class="col-md-6">
                <div class="panel panel-default" style="border: 1px solid #ddd;">
                    <div class="panel-body tabs" style="padding:3px;">
                        <div style="padding: 0px 0px 0px 15px;">
                            <button id="backToCategoryBtn" style="padding: 11px 11px 9px 7px;" data-toggle="tab"
                                    class="btn btn-lg btn-default btnCategoryTab"
                                    tabindex="-1"
                                    href="#pilltabCategory">
                                <i class="fa-21 fa fa-backward fa-2x"></i>
                            </button>
                            <?php
                            for ($i = 0; $i < 10; $i++) {
                                ?>
                                <button style="padding: 10px 14px;" onclick="updateQty_invoice(this)"
                                        class="btn btn-lg btn-primary fSizeBtn">
                                    <?php echo $i; ?>
                                </button>
                                <?php
                            }
                            ?>
                            <button style="padding: 10px 14px;" onclick="updateQty_invoice(this)"
                                    class="btn btn-lg btn-primary fSizeBtn">.
                            </button>
                            <button rel="tooltip" style="    padding: 11px 12px 9px 13px; font-weight: 600;"
                                    title="clear"
                                    onclick="updateQty_invoice(this)"
                                    class="btn btn-lg btn-default fSizeBtn">C
                            </button>
                            <button id="backToCategoryBtn" onclick="go_one_step_back_category()"
                                    style="padding: 11px 12px 9px 7px;" data-toggle="tab"
                                    class="btn btn-lg btn-default btnCategoryTab">
                                <i class="fa-21 fa fa-chevron-left fa-2x"></i>
                            </button>
                        </div>

                        <div class="row" style="margin-left: 0px; margin-right: 0px;">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"
                                 style="padding-left: 15px; padding-top: 10px;">
                                <input type="text" class="form-control" placeholder="Press 'F2' or 'Ctrl+F' to Search"
                                       id="searchProd">
                            </div>
                            <!-- BARCODE READER  -->
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="padding-top: 10px;">
                                <input type="text" class="form-control" placeholder="Barcode (shortcut F3)"
                                       id="barcodeInput">
                            </div>
                        </div>

                        <input type="hidden" id="categoryParentID" value="0">
                        <input type="hidden" id="categoryCurrentID" value="0">

                        <div style="margin: 0px 0px 0px 15px;">
                            <div class="tab-content dynamicSizeCategory" style="overflow: scroll; height: 350px;"
                                 id="allProd">
                                <div class="tab-pane fade in active" id="pilltabCategory">
                                    <?php
                                    /** ------ Shortcuts ------  */
                                    $shortcuts = get_warehouseMenuShortcuts();
                                    if ((!empty($shortcuts))) {
                                        ?>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <?php
                                                foreach ($shortcuts as $menu) {
                                                    echo generate_menu($menu);
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <hr class="posSeparator">
                                    <?php } ?>

                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                            <?php
                                            if (!empty($menuCategory)) {

                                                foreach ($menuCategory as $Category) {
                                                    if ($Category['levelNo'] == 0) {
                                                        echo generate_menuCategory($Category, 0);

                                                    }
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                /********************** Sub Category *********************** */
                                if (!empty($menuSubCategory)) {
                                    foreach ($menuSubCategory as $Category) {
                                        $autoID = $Category['autoID'];
                                        $menuCategoryID = $Category['menuCategoryID']; /* master ID */
                                        $subCategoryList = get_subCategory($menuCategoryID, $warehouseID);
                                        ?>
                                        <div class="tab-pane fade in" id="pilltab<?php echo $autoID ?>">
                                            <?php
                                            if (!empty($subCategoryList)) {
                                                foreach ($subCategoryList as $catList) {
                                                    echo generate_menuCategory($catList, $autoID);
                                                }
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>

                                <!--Global Search -->
                                <div class="tab-pane fade in" id="pilltabAll">
                                    <?php
                                    if (!empty($menuCategory)) {
                                        foreach ($menuCategory as $Category) {
                                            $autoID = $Category['autoID'];
                                            $menuList = get_wareHouseMenuByCategory($autoID);
                                            if (!empty($menuList)) {
                                                foreach ($menuList as $menu) {
                                                    echo generate_menu($menu);
                                                }
                                            }
                                        }
                                    }

                                    if (!empty($menuSubCategory)) {
                                        foreach ($menuSubCategory as $Category) {
                                            $autoID = $Category['autoID'];
                                            $menuCategoryID = $Category['menuCategoryID']; /* master ID */
                                            $subCategoryList = get_subCategory($menuCategoryID, $warehouseID);
                                            if (!empty($subCategoryList)) {
                                                foreach ($subCategoryList as $catList) {
                                                    echo generate_menuCategory($catList, $autoID);

                                                }
                                            }
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
                                                    echo generate_menu($menu);
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

                <div class="btn-group">
                    <?php
                    $cctv = is_cctv_feed_active();
                    if ($cctv) {
                        ?>
                        <button type="button" onclick="open_cctv_modal();"
                                class="btn btn-default btn-lg buttonDefaultSize">
                            <i class="fa fa-video-camera" aria-hidden="true"></i> CCTV
                        </button> <?php } ?>
                    <button type="button" onclick="POS_SizeMax()" style="font-weight: bold;"
                            class="btn btn-default btn-lg buttonDefaultSize">&nbsp;+&nbsp;
                    </button>
                    <input type="hidden" id="currentSize" value="">
                    <button type="button" onclick="POS_SizeMin()" style="font-weight: bold;"
                            class="btn btn-default btn-lg buttonDefaultSize">&nbsp;-&nbsp;
                    </button>
                    <button type="button" onclick="POS_SizeDefault()" class="btn btn-default btn-lg buttonDefaultSize">
                        Default Size
                    </button>
                </div>

                <?php
                $kotBtn = is_show_KOT_button();
                if ($kotBtn) {
                    ?>
                    <button type="button" onclick="POS_SendToKitchen()" class="btn btn-lg btn-danger buttonDefaultSize"
                            id="btn_pos_sendtokitchen"><i class="fa fa-cutlery" aria-hidden="true"></i> Send KOT
                    </button>
                <?php } ?>

                <?php
                $isLocalPOSEnabled = isLocalPOSEnabled();
                if ($isLocalPOSEnabled) {
                    ?>
                    <button type="button" onclick="openSyncModal()" class="btn btn-lg btn-warning buttonDefaultSize">
                        <i class="fa fa-cloud-download" aria-hidden="true"></i>
                        Pull Data
                    </button>
                <?php } ?>

                <?php
                $isSalesReportEnabled = isSalesReportEnabled();
                if ($isSalesReportEnabled) {
                    ?>
                    <button type="button" class="btn btn-lg btn-default buttonDefaultSize"
                            id="btn_pos_sales_report"><i class="fa fa-bar-chart" aria-hidden="true"></i> Sales Report
                    </button>
                <?php } ?>


                <script>
                    function POS_SizeDefault() {
                        $(".itemButton").css('min-height', 112, 'important');
                        $(".itemButton").css('width', 112, 'important');
                        setCookie('btnSize', 112);
                    }

                    function POS_SizeMax() {
                        var containerSize = $(".btnStyleCustom").height();
                        $("#currentSize").val(containerSize);
                        var tmpHeight = parseInt($("#currentSize").val()) + 5;
                        $(".itemButton").css('min-height', tmpHeight - 10, 'important');
                        $(".itemButton").css('width', tmpHeight - 10, 'important');
                        setCookie('btnSize', tmpHeight - 10);


                    }

                    function POS_SizeMin() {
                        var containerSize = $(".btnStyleCustom").height();
                        $("#currentSize").val(containerSize);

                        var tmpHeight = parseInt($("#currentSize").val()) - 5;
                        $(".itemButton").css('min-height', tmpHeight - 10, 'important');
                        $(".itemButton").css('width', tmpHeight - 10, 'important');
                        setCookie('btnSize', tmpHeight - 10);
                    }

                    function setBtnSizeCookie() {
                        var btnSize = getCookie('btnSize');
                        if (btnSize > 0) {
                            $(".itemButton").css('min-height', btnSize, 'important');
                            $(".itemButton").css('width', btnSize, 'important');
                        }
                    }

                    function setCookie(cname, cvalue, exdays) {
                        var d = new Date();
                        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                        var expires = "expires=" + d.toUTCString();
                        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
                    }

                    function getCookie(cname) {
                        var name = cname + "=";
                        var decodedCookie = decodeURIComponent(document.cookie);
                        var ca = decodedCookie.split(';');
                        for (var i = 0; i < ca.length; i++) {
                            var c = ca[i];
                            while (c.charAt(0) == ' ') {
                                c = c.substring(1);
                            }
                            if (c.indexOf(name) == 0) {
                                return c.substring(name.length, c.length);
                            }
                        }
                        return "";
                    }

                    /*update the status to sent to kitchent of the order*/
                    function POS_SendToKitchen() {
                        if ($("#holdInvoiceID").val()) {
                            $.ajax({
                                type: 'POST',
                                dataType: 'JSON',
                                url: "<?php echo site_url('Pos_kitchen/updateSendToKitchen'); ?>",
                                data: {menuSalesID: $("#holdInvoiceID").val()},
                                cache: false,
                                beforeSend: function () {
                                    startLoad();
                                },
                                success: function (data) {
                                    stopLoad();
                                    if (data['error'] == 0) {
                                        $('#btn_pos_sendtokitchen').removeClass('btn-danger');
                                        $('#btn_pos_sendtokitchen').addClass('btn-success');
                                        load_KOT_print_view(data['code'], data['auth']);
                                        $(".isSamplePrintedFlag").val(1);
                                    } else {
                                        load_KOT_print_view($("#holdInvoiceID").val());
                                        myAlert('e', data['message'])
                                    }

                                }, error: function (jqXHR) {
                                    stopLoad();
                                    if (jqXHR.status == false) {
                                        myAlert('w', '<strong>Local Server is Offline</strong> Please try again');
                                    } else {
                                        myAlert('e', 'Message: ' + errorThrown);
                                    }
                                }
                            });

                        } else {
                            if (jqXHR.status == false) {
                                myAlert('w', '<strong>Local Server is Offline</strong> Please try again');
                            } else {
                                myAlert('e', 'Message: ' + errorThrown);
                            }

                        }
                    }

                    function resetKotButton() { // reset the kot button as not send to kitchen color red
                        $('#btn_pos_sendtokitchen').removeClass('btn-success');
                        $('#btn_pos_sendtokitchen').addClass('btn-danger');
                    }
                </script>

            </div> <!-- / col-md-6 -->

            <div class="col-md-5" style="padding-right: 0px;">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-5 col-lg-5">
                        <input type="hidden" id="pos_orderNo" value="<?php
                        $new = $this->lang->line('common_new');
                        $invoiceID = isPos_invoiceSessionExist();
                        if (!empty($invoiceID)) {
                            $id = str_pad($invoiceID, 4, "0", STR_PAD_LEFT);
                            echo get_pos_invoice_code($id);
                        } else {
                            echo 'New';
                        }
                        ?>">
                        <button class="btn btn-sm btn-default btn-block" style="border-radius: 0px;">
                            Order : <strong id="pos_salesInvoiceID_btn">
                                <span>
                                <?php
                                $new = $this->lang->line('common_new');
                                $invoiceID = isPos_invoiceSessionExist();
                                if (!empty($invoiceID)) {
                                    $id = str_pad($invoiceID, 4, "0", STR_PAD_LEFT);
                                    echo get_pos_invoice_code($id);
                                } else {
                                    echo '<span class="label label-danger">' . $new . '<!--New--></span>';
                                }
                                ?>
                                    </span>
                            </strong>
                        </button>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-7 col-lg-7">
                        <button class="btn btn-sm btn-primary btn-block" style="border-radius: 0px; display: none; "
                                id="deliveryDateDiv">
                            Delivery Date : <strong id="pos_delivery_date">
                                <?php echo date('d-m-Y'); ?>
                            </strong>
                        </button>
                    </div>
                </div>

                <?php current_pc() ?>
                <div class="productCls">
                    <div class="row">
                        <div class="col-md-3 col-sm-2">
                            <!-- TABLE -->
                            <button class="btn btn-default btn-lg" type="button" id="table_order_btn"><i
                                        class="fa fa-life-ring"></i> <span id="current_table_description">Table</span>
                            </button>
                        </div>
                        <div class="col-md-9">
                            <div class="receiptPaddingHead"><?php echo $this->lang->line('posr_qyt'); ?><!--Qty--></div>
                            <div class="receiptPaddingHead">@
                                <?php echo $this->lang->line('posr_rate'); ?><!--rate--></div>
                            <div class="receiptPaddingHead">
                                <?php echo $this->lang->line('common_total'); ?><!--Total--></div>
                            <div
                                    class="receiptPaddingHead <?php echo $discountPolicy ? '' : 'hide' ?>"><?php echo $this->lang->line('posr_dist'); ?>
                                .<br/>
                                <!--Dist-->%
                            </div>
                            <div class="receiptPaddingHead <?php echo $discountPolicy ? '' : 'hide' ?>">
                                <?php echo $this->lang->line('posr_dist'); ?><!--Dis-->
                                .<br/><?php echo $this->lang->line('common_amount'); ?><!--Amt-->.
                            </div>
                            <div class="receiptPaddingHead"><?php echo $this->lang->line('posr_net'); ?><!--Net--><br/>
                                <?php echo $this->lang->line('common_total'); ?><!--Total--></div>
                        </div>

                    </div>
                </div>

                <div style="overflow: scroll; height: 240px; width: 100%;" class="dynamicSizeItemList">
                    <form id="posInvoiceForm" class="form_pos_receipt" method="post">
                        <div id="log">
                        </div>
                    </form>
                </div>

                <form class="form_pos_receipt" method="post">
                    <div class="itemListContainer posFooterBgColor">

                        <div class="row itemListFoot">
                            <div class="col-md-4"><span
                                        class="posFooterTxtLg"><?php echo $this->lang->line('posr_total_item'); ?><!--Total Items--> : <span
                                            id="total_item_qty" class="posFooterTxtLg">0</span></span>
                            </div>

                            <div class="col-md-5 ar">
                                <input type="hidden" id="total_item_qty_input" name="total_item_qty_input"/>
                                <?php echo $this->lang->line('posr_total_amount'); ?><!--Gross Total-->
                                :
                            </div>
                            <div class="col-md-3 ar">
                                <div id="gross_total">0</div>
                                <input type="hidden" id="gross_total_amount_input" name="gross_total_amount_input"
                                       value="0"/>
                                <input type="hidden" id="gross_total_input" name="gross_total_input"/>
                            </div>
                        </div>


                        <div class="row itemListFoot hide">
                            <div class="col-md-4">&nbsp;</div>

                            <div class="col-md-5 ar"> <?php echo $this->lang->line('common_total'); ?><!--Total-->
                            </div>
                            <div class="col-md-3 ar">
                                <div id="totalWithoutTax">0</div>
                            </div>
                        </div>

                        <div class="row itemListFoot <?php if ($templateID == 2 || $templateID == 3) {
                        } else {
                            echo 'hide';
                        }
                        ?>">
                            <div class="col-md-4">&nbsp;</div>
                            <div class="col-md-2">
                                <div>&nbsp;</div>
                            </div>
                            <div class="col-md-3 ar">
                                <?php echo $this->lang->line('posr_total_tax'); ?><!--Total Tax -->
                            </div>
                            <div class="col-md-3 ar" style="padding-bottom: 5px;">
                                <div id="display_totalTaxAmount">0.00</div>
                            </div>
                        </div>

                        <div class="row itemListFoot <?php if ($templateID == 2 || $templateID == 4) {
                        } else {
                            echo 'hide';
                        }
                        ?>">
                            <div class="col-md-4">&nbsp;</div>

                            <div class="col-md-5 ar">
                                <?php echo $this->lang->line('posr_service_charge'); ?> <!--Service Charge--> :
                            </div>
                            <div class="col-md-3 ar" style="padding-bottom: 5px;">
                                <div id="display_totalServiceCharge">0.00</div>
                            </div>
                        </div>

                        <div class="row itemListFoot">
                            <div class="col-md-4"></div>


                            <div class="col-md-5 ar">
                                <?php echo $this->lang->line('posr_discount'); ?><!--Discount--> % :
                                <input maxlength="6" class="numpad" onchange="updateDiscountPers()"
                                       type="text"
                                       style="color: black; width: 45px; font-weight: 800; text-align: right;"
                                       name="discount_percentage" id="dis_amt" value="0" readonly>
                            </div>
                            <div class="col-md-3 ar" style="border-bottom: 0px solid #ffffff; padding-bottom: 5px;">
                                <div class="hide" id="total_discount">0.00</div>
                                <input type="text" class="numpad" id="discountAmountFooter"
                                       onchange="backCalculateDiscount()"
                                       style="width: 100%; text-align: right; font-weight: 700; color:#2E2E2E;" value=""
                                       placeholder="0.00">
                                <input type="hidden" id="total_discount_amount" name="total_discount_amount"/>
                            </div>
                        </div>

                        <div class="row itemListFoot">
                            <div class="col-md-4 posFooterBorderBottom">&nbsp;</div>
                            <div class="col-md-2 posFooterBorderBottom">
                                <div>&nbsp;</div>
                            </div>
                            <div class="col-md-3 ar posFooterBorderBottom">
                            <span class="posFooterTxtLg">
                            <?php echo $this->lang->line('posr_net_total'); ?> <!--Net Total--> :
                                </span>
                            </div>
                            <div class="col-md-3 ar posFooterBorderBottom">
                                <div id="total_netAmount" class="posFooterTxtLg">0</div>
                            </div>
                        </div>

                    </div>
                </form>

                <input type="hidden" id="customerTypeBtnString" value="">
                <!-- 2017-03-09 -->
                <div id="pos_btnSet_dtd">
                    <?php
                    $customerType = getCustomerType();

                    if (!empty($customerType)) {
                        ?>
                        <input type="hidden" id="customerType" name="customerType" value="">
                        <input type="hidden" id="is_dine_in" name="is_dine_in" value="0">
                        <div class="order-type-btn-group">
                            <div class="btn-group btn-group-lg">
                                <?php
                                $defaultID = 0;
                                $isDelivery = 0;
                                $isDineIn = 0;
                                foreach ($customerType as $val) {
                                    if (trim(strtolower($val['customerDescription'])) == 'delivery orders' || trim(strtolower($val['customerDescription'])) == 'delivery' || trim(strtolower($val['customerDescription'])) == 'delivery order') {
                                        $isDelivery = 1;
                                    } else if (trim(strtolower($val['customerDescription'])) == 'dine-in' || trim(strtolower($val['customerDescription'])) == 'eat-in') {
                                        $isDineIn = 1;
                                    }
                                    ?>
                                    <button type="button"
                                            onclick="updateCustomerTypeBtn(<?php echo $val['customerTypeID']; ?>,<?php echo $isDelivery ?>,<?php echo $isDineIn ?>)"
                                            class="btn buttonCustomerType buttonDefaultSize <?php if ($val['isDefault'] == 1) {
                                                $defaultID = $val['customerTypeID'];
                                                //echo 'btn-primary';
                                                echo 'btn-default';
                                            } else {
                                                echo 'btn-default';
                                            }
                                            ?>  customerType"
                                            id="customerTypeID_<?php echo $val['customerTypeID']; ?>">
                                        <?php echo $val['customerDescription']; ?>
                                    </button>
                                <?php }

                                ?>
                            </div>
                            <script>
                                $(document).ready(function (e) {
                                    defaultDineinButtonID = '<?php echo $defaultID; ?>';
                                });
                            </script>
                        </div>
                    <?php } ?>

                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1">
                <div class="row">
                    <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList">
                        <?php
                        $confomion = $this->lang->line('common_confirmation');
                        $message = $this->lang->line('common_are_you_sure_you_want_to_close_the_counter');
                        $cancel = $this->lang->line('common_cancel');
                        $ok = $this->lang->line('common_ok');
                        ?>
                        <button type="button" class="btn btn-block btn-lg btn-default btn-myCustom"
                                onclick="clickPowerOff()">
                            <i class="fa fa-power-off text-red" aria-hidden="true"></i>
                            <br/><?php echo $this->lang->line('posr_power'); ?> <!--Power-->
                        </button>

                    </div>


                    <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList">
                        <button type="button" class="btn btn-block btn-lg btn-default btn-myCustom" rel="tooltip"
                                title="short cut  Ctrl+O "
                                onclick="open_holdReceipt()">
                            <i class="fa fa-external-link-square text-purple" aria-hidden="true"></i> <br/>
                            <?php echo $this->lang->line('posr_open'); ?><!--Open-->
                        </button>
                    </div>

                    <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList">
                        <button class="btn btn-block btn-lg btn-success btn-myCustom btn-disable-when-load"
                                onclick="open_pos_payments_modal()">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i><br/> &nbsp;
                            <?php echo $this->lang->line('common_pay'); ?><!--Pay--> (F1)
                        </button>
                    </div>

                    <?php
                    if (isset($sampleBillPolicy) && $sampleBillPolicy) {
                        ?>
                        <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList">
                            <button type="button" onclick="print_sample_bill()" class="btn btn-block btn-lg btn-default btn-myCustom" style="padding: 10px 0;">
                                <i class="fa fa-print"></i><br/> &nbsp;
                                <span style="font-size: 12px;">Print Sample</span>
                            </button>
                        </div>
                        <?php
                    }
                    ?>

                    <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList">
                        <a href="#holdmodel" data-toggle="modal" style="text-decoration: none;">
                            <button class="btn btn-block btn-lg btn-danger dangerCustom2 btn-myCustom" rel="tooltip"
                                    title="short cut  Ctrl+S "
                                    onclick="holdReceipt();">
                                <i class="fa fa-pause" aria-hidden="true"></i> &nbsp;<br>
                                <?php echo $this->lang->line('posr_hold'); ?><!--Hold-->
                            </button>
                        </a>
                    </div>

                    <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList">
                        <button class="btn btn-lg btn-danger btn-block dangerCustom"
                                onclick="checkPosAuthentication(9)">
                            <!--onclick="cancelCurrentOrder()"-->
                            <i class="fa fa-times" aria-hidden="true"></i> &nbsp;<br/>
                            <?php echo $this->lang->line('common_cancel'); ?><!--Cancel-->
                        </button>
                    </div>

                    <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList" id="btn_kitchenModal">
                        <button type="button" class="btn btn-block btn-lg btn-default btn-myCustom" rel="tooltip"
                                title=""
                                onclick="open_kitchen_ready()">
                            <i class="fa fa-cutlery text-purple" aria-hidden="true"></i> <br/>
                            <?php echo $this->lang->line('posr_kitchen'); ?><!--Kitchen-->
                        </button>
                    </div>


                    <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList" id="btn_kitchenModal">
                        <button type="button" class="btn btn-block btn-lg btn-default btn-myCustom" rel="tooltip"
                                title=""
                                onclick="open_giftCardModal()">
                            <i class="fa fa-credit-card text-green"></i> <br/>
                            Gift Card
                        </button>
                    </div>

                    <div class="col-xs-3 col-sm-2 col-md-12 col-lg-12 mainBtnList">
                        <button type="button" class="btn btn-block btn-lg btn-default btn-myCustom"
                                onclick="open_void_Modal()">
                            <i class="fa fa-ban text-red" aria-hidden="true"></i> <br/>
                            <?php echo $this->lang->line('common_closed'); ?><!--Closed--><br/>
                            <?php echo $this->lang->line('posr_bills'); ?><!--Bills-->
                        </button>
                    </div>
                </div>


            </div>
        </div> <!--/ row -->
    </div>

    <div id="posHeader_1" class="hide">
    </div>
<?php
$data['notFixed'] = true;
$data['control_sidebar'] = false;
$data['tablet'] = false;
$data['d'] = $d;
$this->load->view('system/pos/modals/rpos-barcode', $data);
$this->load->view('system/pos/modals/pos-modal-payments', $data);
$this->load->view('system/pos/modals/rpos-modal-hold-receipt', $data);
$this->load->view('system/pos/modals/rpos-modal-kitchen-status', $data);
$this->load->view('system/pos/modals/rpos-modal-print-template', $data);
$this->load->view('system/pos/modals/rpos-modal-pack-invoice', $data);
$this->load->view('system/pos/modals/rpos-modal-void-invoice', $data);
$this->load->view('system/pos/modals/rpos-modal-till', $data);
$this->load->view('system/pos/modals/rpos-modal-kitchen-note', $data);
$this->load->view('system/pos/modals/pos-modal-gift-card');
$this->load->view('system/pos/modals/pos-modal-credit-sales');
$this->load->view('system/pos/modals/pos-modal-java-app');
$this->load->view('system/pos/modals/pos-modal-delivery', $data);
$this->load->view('system/pos/modals/pos-modal-table-order', $data);
$this->load->view('system/pos/modals/pos-modal-sales-report.php', $data);
$this->load->view('system/pos/modals/rpos-modal-auth-process', $data);
$this->load->view('system/pos/modals/rpos-modal-cctv', $data);
$this->load->view('system/pos/modals/rpos-modal-sync', $data);
$this->load->view('system/pos/js/pos-restaurant-common-js', $data);

?>
    <script type="text/javascript" src="<?php echo base_url('plugins/keyboard-short-cut/shortcut.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('plugins/pos/r-pos.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('plugins/pos/r-pos-shortcuts.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/slick/slick/slick.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('plugins/printJS/jQuery.print.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('plugins/numPadmaster/jquery.numpad.js') ?>" type="text/javascript"></script>
    <script type="text/javascript"
            src="<?php echo base_url('plugins/virtual-keyboard-mlkeyboard/jquery.ml-keyboard.js') ?>"></script>
    <script type="text/javascript">
        var dPlaces = <?php echo $this->common_data['company_data']['company_default_decimal'];?>; // Don't delete NASIK
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
                }, error: function (jqXHR) {
                    stopLoad();
                    if (jqXHR.status == false) {
                        myAlert('w', 'Local Server is Offline,  Please try again');
                    } else {
                        myAlert('e', 'Error in loading currency denominations.')
                    }
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
        var numberOfRequest = 0;

        function initNumPad() {
            $('.numpad').unbind();
            $('.numpad').numpad();
        }

        $(document).on('ready', function () {

            /** Virtual Keyboard */
            $.fn.numpad.defaults.gridTpl = '<table class="modal-content table" style="width:200px" ></table>';
            $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in" style="z-index: 5000;"></div>';
            $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:16px; font-weight: 600;" />';
            $.fn.numpad.defaults.buttonNumberTpl = '<button type="button" class="btn btn-xl-numpad btn-numpad-default"></button>';
            $.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn btn-xl-numpad" style="width: 100%;"></button>';
            $.fn.numpad.defaults.onKeypadCreate = function () {
                $(this).find('.done').addClass('btn-primary');
            };

            $('html').click(function (e) {
                if (!$(e.target).hasClass('touchEngKeyboard')) {
                    $("#mlkeyboard").hide();
                }
            });
            /** Dynamic Size Setup */
            setTimeout(function () {
                <?php
                switch ($templateID) {
                    case 2:
                        /** Tax & Service Charge Separated*/
                        $sizeOfDynamicHeight = '380';
                        break;
                    case 3:
                    case 4:
                        /** 3 Tax & 4 Service Charge Separated*/
                        $sizeOfDynamicHeight = '355';
                        break;

                    default:
                        /** All Inclusive*/
                        $sizeOfDynamicHeight = '325';
                }
                ?>
                var template = '<?php echo $sizeOfDynamicHeight ?>';
                $(".dynamicSizeCategory").css("height", $(window).height() - 245 + 'px');
                $(".dynamicSizeItemList").css("height", $(window).height() - template + 'px');
            }, 100);

            initNumPad();
            setBtnSizeCookie();
            $(".btnCategoryTab").click(function (e) {
                $("#searchProd").val('');
                $("#searchProd").trigger('keyup');
            });
            $("[rel='tooltip']").tooltip();
            $("#searchProd").keyup(function (e) {

                // Retrieve the input field text
                var filter = $(this).val();

                $(".proname").each(function () {
                    if ($(this).text().search(new RegExp(filter, "gi")) < 0) {
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
                $("#cardTotalAmount").val(0);
                $("#netTotalAmount").val(0);
                $("#serviceCharge").val('<?php echo get_defaultServiceCharge() ?>');
                $("#pos_payments_modal").modal('hide');
                clearSalesInvoice();
                resetPayTypeBtn();
                /*setTimeout(function () {
                    sync_live();
                }, 3000);*/
            });

            $('.numberFloat').keypress(function (event) {
                if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });
            updateCurrentMenuWAC();
            <?php if ($isHadSession == 0) { ?>
            $("#isStart").val(1);
            $("#tillModal_title").text("Day Start");
            $("#tillSave_Btn").attr("onclick", "shift_create()");
            till_modal.modal({backdrop: "static"});

            <?php }else { ?>
            checkPosSession();
            <?php } ?>
        });

        function setUpSyncCount(count) {
            $("#count_of_to_be_synced").html(count);
            $("#input_count_of_to_be_synced").val(count);
            if (count == 0) {
                //$("#count_of_to_be_synced").hide();
            } else {
                //$("#count_of_to_be_synced").show();
            }
        }

        function clickPowerOff() {
            if ($("#holdInvoiceID").val() == 0) {
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    url: "<?php echo site_url('Pos_restaurant/clickPowerOff'); ?>",
                    data: {id: null},
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 0) {
                            $("#isStart").val(0);
                            $(".tillModal_close").show();
                            $("#tillModal_title").text("Day End");
                            $("#tillSave_Btn").attr("onclick", "shift_close()");
                            till_modal.modal({backdrop: "static"});
                        } else {
                            bootbox.alert('<div class="alert alert-danger">' + data['message'] + '</div>');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        if (jqXHR.status == false) {
                            myAlert('w', 'Local Server is Offline,  Please try again');
                        } else {
                            myAlert('e', 'Message: ' + errorThrown);
                        }
                    }
                });
                return false;
            } else {
                bootbox.alert('<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle fa-2x"></i> Warning! </strong><br/><br/>Please close the current bill.</div>');
            }

        }

        function updateCurrentMenuWAC() {
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Pos_restaurant/updateCurrentMenuWAC'); ?>",
                data: {id: null},
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    console.log(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    if (jqXHR.status == false) {
                        myAlert('w', 'Local Server is Offline,  Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }

                }
            });
            return false;
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
                    if (jqXHR.status == false) {
                        myAlert('w', 'Local Server is Offline,  Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }
                }
            });
            return false;
        }

        function checkisKOT(id, isPack, kotID, description) {
            if (kotID > 0) {
                open_kitchen_note(id, kotID);
                $("#kot_kotID").val(kotID);
                $("#kitchenNoteDescription").html(description);
                /*setTimeout(function () {
                 $("#kitchenNote").focus();
                 }, 500);*/
            } else {
                //$("#frm_kot")[0].reset();
                if (isPack == 1) {
                    LoadToInvoicePack(id);
                } else {
                    LoadToInvoice(id);
                }
            }
        }

        function open_kitchen_note(id, kotID) {
            $("#tmpWarehouseMenuID").val(id);
            if (kotID > 0) {
                $("#pos_kitchen_note").modal('show');
            }
        }

        function LoadToInvoicePack(id) {
            load_packItemList(id);
        }

        var parentID_addOn = 0;

        function LoadToInvoice(id, parentID = 0, source = 0) {
            abortSync();
            var discountPolicy = '<?php echo $discountPolicy ? 0 : 1;  ?>';
            var classDiscountHide = '<?php echo $discountPolicy ? '' : 'hide';  ?>';
            var dynamicWidth = '<?php echo $discountPolicy ? '16.5%' : '24.5%';  ?>';
            var date = new Date,
                hour = date.getHours(),
                minute = date.getMinutes(),
                seconds = date.getSeconds(),
                minute = minute > 9 ? minute : "0" + minute;
            seconds = seconds > 9 ? seconds : "0" + seconds;
            hour = hour > 9 ? hour : "0" + hour;

            date = hour + ":" + minute + ":" + seconds;


            var customerType = $("#customerType").val();
            var kotID = $("#kot_kotID").val();
            var kitchenNote = $("#kitchenNote").val();
            var addOnID = kotAddOnList[0];
            //console.log(customerType);
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Pos_restaurant/LoadToInvoice'); ?>",
                data: {
                    id: id,
                    customerType: customerType,
                    kotID: kotID,
                    kitchenNote: kitchenNote,
                    pos_templateID: '<?php echo get_pos_templateID(); ?>',
                    currentTime: date,
                    parentMenuSalesItemID: parentID,
                    isFromTablet: 0
                },
                cache: false,
                beforeSend: function () {
                    numberOfRequest++;
                    disable_POS_btnSet()
                    startLoadPos();
                },
                success: function (data) {
                    numberOfRequest--;
                    if (numberOfRequest == 0) {
                        enable_POS_btnSet();
                    }
                    stopLoad();
                    if (data['error'] == 1) {
                        myAlert('e', data['message']);
                    } else if (data['error'] == 0) {
                        var divTmp = '';
                        /*<img src="' + data['menuImage'] + '" style="max-height: 40px;" alt=""> */
                        divTmp = '<div onclick="selectMenuItem(this)" class="row itemList" id="item_row_' + data['code'] + '" style="margin: 0px; border-bottom: 1px solid #dddddd; padding-top: 5px; padding-bottom: 5px;">';
                        divTmp += '<div class="col-md-1 hidden-xs hidden-sm menuItem_pos_col_1 hide"></div>';
                        divTmp += '<div class="col-md-3 menuItem_pos_col_5">' + data['menuMasterDescription'] + ' </div>';
                        divTmp += '<div class="col-md-9">';
                        divTmp += '<div class="receiptPadding">';
                        divTmp += '<input type="text" onkeyup="calculateFooter()" onchange="updateQty(' + data['code'] + ')" value="1" class="display_qty menuItem_input numberFloat" id="qty_' + data['code'] + '" name="qty[' + data['code'] + ']"  />';
                        divTmp += '</div>';

                        divTmp += '<div class="receiptPadding">';
                        divTmp += '<span class="menu_itemCost menuItemTxt"> ' + data['sellingPrice'] + '</span> <!-- @rate -->';
                        divTmp += '<input type="hidden"  class="menuItemTxt_input numberFloat" name="sellingPrice[' + data['code'] + ']" value="' + data['sellingPrice'] + '"/>';
                        divTmp += '<input type="hidden"  class="menuItemTxt_input numberFloat pricewithoutTax" name="pricewithoutTax[' + data['code'] + ']" value="' + data['pricewithoutTax'] + '"/>';
                        divTmp += '<input type="hidden"  class="menuItemTxt_inputDiscount numberFloat pricewithoutTaxDiscount" name="pricewithoutTaxDiscount[' + data['code'] + ']" value="' + data['pricewithoutTax'] + '"/>';
                        divTmp += '<input type="hidden"  class="menuItemTxt_input totalMenuTaxAmount numberFloat" name="totalMenuTaxAmount[' + data['code'] + ']" value="' + data['totalTaxAmount'] + '"/>';
                        divTmp += '<input type="hidden"  class="menuItemTxt_inputDiscount totalMenuTaxAmountDiscount numberFloat" name="totalMenuTaxAmountDiscount[' + data['code'] + ']" value="' + data['totalTaxAmount'] + '"/>';
                        divTmp += '<input type="hidden" class="menuItemTxt_input numberFloat totalMenuServiceCharge" name="totalMenuServiceCharge[' + data['code'] + ']" value="' + data['totalServiceCharge'] + '"/>';
                        divTmp += '<input type="hidden"  class="menuItemTxt_inputDiscount numberFloat totalMenuServiceChargeDiscount" name="totalMenuServiceChargeDiscount[' + data['code'] + ']" value="' + data['totalServiceCharge'] + '"/>';
                        divTmp += '<input type="hidden"  name="frm_isTaxEnabled[' + data['code'] + ']" value="' + data['isTaxEnabled'] + '"/>';
                        divTmp += '<input type="hidden" class="isSamplePrintedFlag"  id="isSamplePrinted_' + data['code'] + '" value="0"/>';
                        divTmp += '</div>';

                        divTmp += '<div class="receiptPadding">';
                        divTmp += '<span class="menu_total menuItemTxt">0</span>  <!-- total -->';
                        divTmp += '</div>';
                        divTmp += '<div class="receiptPadding ' + classDiscountHide + '" style="width:' + dynamicWidth + '"> <input style="width:60%;" onchange="item_wise_discount(this,\'P\')" name="discountPercentage[' + data['code'] + ']"  maxlength="3" type="text" value="0" class="menu_discount_percentage menu_qty menuItem_input numberFloat numpad"> <!-- disc. % -->';
                        divTmp += '</div>';
                        divTmp += '<div class="receiptPadding ' + classDiscountHide + '" >';
                        divTmp += '<input style="width:90%;" onchange="item_wise_discount(this,\'A\')" name="discountAmount[' + data['code'] + ']" type="text" value="0" class="menu_discount_amount menu_qty menuItem_input numberFloat numpad"><!-- disc. amount -->';
                        divTmp += '</div>';
                        divTmp += '<div class="receiptPadding">';
                        divTmp += '<div style="width:55px; text-align: right;" class="itemCostNet menuItemTxt set-delete"> [' + data['sellingPrice'] + '</div> <!-- net total -->';
                        divTmp += '<div onclick="beforeDeleteItem(' + data['code'] + ')" data-placement="bottom" rel="tooltip" title="Delete" style="cursor:pointer; width: 12px; margin-top: -20px;     margin-right: 2px;" class="pull-right">';
                        divTmp += '<button type="button" class="btn btn-default btn-sm  itemList-delBtn c-b-20"><i class="fa fa-close closeColor"></i></button>';
                        divTmp += '</div>';
                        divTmp += '</div>';
                        divTmp += '</div>';
                        divTmp += '</div>';

                        $("#log").append(divTmp);

                        $("[rel='tooltip']").tooltip();
                        $("#pos_salesInvoiceID_btn").html(data['tmpInvoiceID_code']);
                        $("#pos_orderNo").val(data['tmpInvoiceID_code']);
                        $("#holdInvoiceID_input").val(data['tmpInvoiceID']);
                        $("#holdInvoiceID").val(data['tmpInvoiceID']);
                        $("#holdInvoiceID_codeTmp").val(data['tmpInvoiceID_code']);

                        if (data['isPack'] == 1) {
                            savePackDetailItemList(data['code']);
                        }
                        //console.log('isPack' + data['isPack']);
                        calculateFooter();
                        selectMenuItemSpefici('item_row_' + data['code']);
                        if (kotAddOnList.length > 0) {
                            if (kotAddOnList[0] > 0) {
                                if (source == 0) {
                                    parentID_addOn = data['code'];
                                }
                                LoadToInvoice(kotAddOnList[0], parentID_addOn, 1);
                                kotAddOnList = jQuery.grep(kotAddOnList, function (value) {
                                    return value != kotAddOnList[0];
                                });
                            }
                        }

                        initNumPad();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    numberOfRequest--;
                    if (numberOfRequest == 0) {
                        enable_POS_btnSet();
                    }
                    if (jqXHR.status == false) {
                        myAlert('w', 'Local Server is Offline,  Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }
                }
            });
            return false;
        }

        function calculateTotalAmount(priceWithoutTax, taxAmount, ServiceCharge, qty, discount) {
            var discount = parseFloat(discount);
            var discountAmount = 0;
            //console.log(' Price Without Tax: ' + priceWithoutTax + ' - TAX: ' + taxAmount + ' - SC: ' + ServiceCharge + ' - qty: ' + qty);
            var templateID = '<?php echo $templateID ?>';
            var totalAmount = 0;
            switch (parseInt(templateID)) {
                case 1:
                    totalAmount = (parseFloat(priceWithoutTax) + parseFloat(taxAmount) + parseFloat(ServiceCharge)) * parseFloat(qty);
                    /** All Inclusive */
                    break;
                case 2:
                    /** Tax & Service Charge Separated */
                    totalAmount = parseFloat(priceWithoutTax) * parseFloat(qty);
                    break;
                case 3:
                    /** Tax Separated */
                    totalAmount = (parseFloat(priceWithoutTax) + parseFloat(ServiceCharge)) * parseFloat(qty);
                    break;
                case 4:
                    /** Service Charge Separated */
                    totalAmount = (parseFloat(priceWithoutTax) + parseFloat(taxAmount)) * parseFloat(qty);
                    break;

                default:
                    /** All Inclusive */
                    totalAmount = (parseFloat(priceWithoutTax) + parseFloat(taxAmount) + parseFloat(ServiceCharge)) * parseFloat(qty);
            }
            if (discount > 0) {
                discountAmount = (totalAmount * discount) / 100;
            }
            totalAmount = totalAmount - discountAmount;
            return totalAmount;
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
            var totalTax = 0;
            var totalTaxDiscount = 0;
            var serviceCharge = 0;
            var serviceChargeDiscount = 0;
            var totalPriceWithoutTax = 0;
            var totalPriceWithoutTaxDiscount = 0;

            $("div .itemList").each(function (e) {

                var qty = $(this).find(".display_qty").val();
                if (qty < 0) {
                    $(this).find(".display_qty").val(1);
                    qty = $(this).find(".display_qty").val();
                }


                var tmpSC = $(this).find(".totalMenuServiceCharge").val();
                serviceCharge = parseFloat(serviceCharge) + (parseFloat(tmpSC) * qty);

                var tmpTax = $(this).find(".totalMenuTaxAmount").val();
                totalTax = parseFloat(totalTax) + (parseFloat(tmpTax) * qty);

                var pricewithoutTax = $(this).find(".pricewithoutTax").val();
                totalPriceWithoutTax = parseFloat(totalPriceWithoutTax) + (parseFloat(pricewithoutTax) * qty);

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
                        $(this).find(".menu_discount_amount").val(discountedAmount.toFixed(<?php echo $d ?>));

                        /** Tax Handling */
                        var taxAmount = $(this).find(".totalMenuTaxAmount").val();
                        var discountedTax = (percentage / 100) * taxAmount;
                        $(this).find(".totalMenuTaxAmountDiscount").val(discountedTax);
                        totalTaxDiscount = totalTaxDiscount + (discountedTax * qty);


                        /** Service Charge */
                        var tmpServiceCharge = $(this).find(".totalMenuServiceCharge").val();
                        var discountedServiceCharge = (percentage / 100) * tmpServiceCharge;
                        $(this).find(".totalMenuServiceChargeDiscount").val(discountedServiceCharge);
                        serviceChargeDiscount = serviceChargeDiscount + (discountedServiceCharge * qty);

                        /** PriceWithoutTax */
                        var tmpPriceWithoutTax = $(this).find(".pricewithoutTax").val();
                        var discountedPriceWithoutTax = (percentage / 100) * tmpPriceWithoutTax;
                        $(this).find(".pricewithoutTaxDiscount").val(discountedPriceWithoutTax);
                        totalPriceWithoutTaxDiscount = totalPriceWithoutTaxDiscount + (discountedPriceWithoutTax * qty);
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

                        /** Tax Handling */
                        var taxAmount = $(this).find(".totalMenuTaxAmount").val();
                        var discountedTax = (percentage / 100) * taxAmount;
                        $(this).find(".totalMenuTaxAmountDiscount").val(discountedTax);
                        totalTaxDiscount = totalTaxDiscount + (discountedTax * qty);


                        /** Service Charge */
                        var tmpServiceCharge = $(this).find(".totalMenuServiceCharge").val();
                        var discountedServiceCharge = (percentage / 100) * tmpServiceCharge;
                        $(this).find(".totalMenuServiceChargeDiscount").val(discountedServiceCharge);
                        serviceChargeDiscount = serviceChargeDiscount + (discountedServiceCharge * qty);

                        /** PriceWithoutTax */
                        var tmpPriceWithoutTax = $(this).find(".pricewithoutTax").val();
                        var discountedPriceWithoutTax = (percentage / 100) * tmpPriceWithoutTax;
                        $(this).find(".pricewithoutTaxDiscount").val(discountedPriceWithoutTax);
                        totalPriceWithoutTaxDiscount = totalPriceWithoutTaxDiscount + (discountedPriceWithoutTax * qty);
                    }

                } else {
                    var discountedAmount = $(this).find(".menu_discount_amount").val();
                    var percentage = $(this).find(".menu_discount_percentage").val();
                    var discountedAmount = (percentage / 100) * total
                    $(this).find(".menu_discount_amount").val(discountedAmount.toFixed(<?php echo $d ?>));

                    /** Tax Handling */
                    var taxAmount = $(this).find(".totalMenuTaxAmount").val();
                    var discountedTax = (percentage / 100) * taxAmount;
                    $(this).find(".totalMenuTaxAmountDiscount").val(discountedTax);
                    totalTaxDiscount = totalTaxDiscount + (discountedTax * qty);

                    /** Service Charge */
                    var tmpServiceCharge = $(this).find(".totalMenuServiceCharge").val();
                    var discountedServiceCharge = (percentage / 100) * tmpServiceCharge;
                    $(this).find(".totalMenuServiceChargeDiscount").val(discountedServiceCharge);
                    serviceChargeDiscount = serviceChargeDiscount + (discountedServiceCharge * qty);

                    /** PriceWithoutTax */
                    var tmpPriceWithoutTax = $(this).find(".pricewithoutTax").val();
                    var discountedPriceWithoutTax = (percentage / 100) * tmpPriceWithoutTax;
                    $(this).find(".pricewithoutTaxDiscount").val(discountedPriceWithoutTax);
                    totalPriceWithoutTaxDiscount = totalPriceWithoutTaxDiscount + (discountedPriceWithoutTax * qty);
                }

                if (discountedAmount == undefined) {
                    discountedAmount = 0;
                }

                var netTotal = total - discountedAmount;
                $(this).find(".itemCostNet").text(netTotal.toFixed(<?php echo $d ?>));
                var sellingPrice = $(this).find(".itemCostNet").text();
                /** Policy based Amount */
                var policyBasedAmount = calculateTotalAmount(pricewithoutTax, tmpTax, tmpSC, qty, percentage);
                netAmountTotal = parseFloat(netAmountTotal) + parseFloat(policyBasedAmount);
                var totalWithoutDiscount = qty * perItemCost;
                $(this).find(".menu_total").text(total.toFixed(<?php echo $d ?>));
                totalAmount = parseFloat(totalAmount) + parseFloat(total);
                totalQty = parseFloat(totalQty) + parseFloat(qty);
                grossTotal = parseFloat(grossTotal) + parseFloat(totalWithoutDiscount);
                totalDiscount = parseFloat(totalDiscount) + parseFloat(discountAmount);

            });

            var taxDiscountount = totalTax - totalTaxDiscount;
            var serviceChargeDiscountount = serviceCharge - serviceChargeDiscount;
            var priceWithoutTaxDiscountount = netAmountTotal - totalPriceWithoutTaxDiscount;

            //debugger;

            /** Total Tax */
            $("#display_totalTaxAmount").html(taxDiscountount.toFixed(<?php echo $d ?>));

            /**
             *  Service Charge only for Dine-in Customers
             *  only applied in Tax and SC separated tmpleate & SC separated template
             *
             *  Template
             *  2 - Tax & Service Charge Separated
             *  4 - Service Charge Separated
             *
             *  */

            var template = '<?php echo $templateID ?>';
            if (template == 2 || template == 4) {
                var dineIn = $("#is_dine_in").val();
                if (dineIn != 1) {
                    serviceChargeDiscountount = 0;
                    serviceCharge = 0;
                }
            }

            $("#display_totalServiceCharge").html(serviceChargeDiscountount.toFixed(<?php echo $d ?>));
            var netTotal = totalTax + serviceCharge + totalPriceWithoutTax;
            $("#total_item_qty").html(totalQty);
            $("#total_item_qty_input").val(totalQty);
            $("#final_purchased_item").html(totalQty);
            $("#gross_total").html(netAmountTotal.toFixed(<?php echo $d ?>));
            var tmpPriceWithoutTax = parseFloat($("#gross_total").text());
            var tmpTax = parseFloat($("#display_totalTaxAmount").text());
            var tmpSC = parseFloat($("#display_totalServiceCharge").text());
            var tmpGrossTotal = get_gross_amount(tmpPriceWithoutTax, tmpTax, tmpSC);
            $("#gross_total_amount_input").val(tmpGrossTotal);
            calculateFinalDiscount();
            calculateReturn();
        }

        function get_gross_amount(priceWithoutTax, totalTax, serviceCharge) {
            var templateID = ('<?php echo $templateID ?>');
            templateID = parseInt(templateID);
            var grossAmount = 0;
            switch (templateID) {
                case 1:
                    grossAmount = priceWithoutTax; //All Inclusive
                    break;
                case 2:
                    grossAmount = priceWithoutTax + totalTax + serviceCharge; //Tax & Service Charge Separated
                    break;
                case 3:
                    grossAmount = priceWithoutTax + totalTax; //Tax Separated
                    break;
                case 4:
                    grossAmount = priceWithoutTax + serviceCharge; //Service Charge Separated
                    break;
            }
            return grossAmount;
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
                    }
                    if (data['error'] == 0) {

                        $("#is_dine_in").val(data['dine_in']);
                        $("#dis_amt").val(data['discountPer']);
                        $("#serviceCharge").val(data['serviceCharge']);
                        Load_pos_holdInvoiceData(data['code']);
                        $("#holdInvoiceID_input").val(data['code']);
                        $("#holdInvoiceID").val(data['code']);
                        $("#holdInvoiceID_codeTmp").val(data['master']['invoiceCode']);
                        $("#customerType").val(data['customerTypeID']);
                        $(".customerType").removeClass('btn-primary');
                        $(".customerType").addClass('btn-default');
                        $("#customerTypeID_" + data['customerTypeID']).removeClass('btn-default');
                        $("#customerTypeID_" + data['customerTypeID']).addClass('btn-primary');

                        var deliveryType = $("#customerTypeID_" + data['customerTypeID']).html();
                        if (deliveryType !== undefined) {
                            if (deliveryType.trim() == "Delivery Orders") {
                                $(".deliveryRow").show()
                                $(".deliveryPromotionRow").show()
                            }
                        }

                        $("#current_table_description").text('Table');

                        selectCustomerButton(data['customerTypeID']);
                        if (parseInt(data['master']['isOrderPending'])) { /*check order is pending and change the send to kitchen button color*/
                            $('#btn_pos_sendtokitchen').removeClass('btn-danger');
                            $('#btn_pos_sendtokitchen').addClass('btn-success');
                        }
                    }
                    setUpSyncCount(data['sync_pending_bill_count']);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == false) {
                        myAlert('w', 'Local Server is Offline,  Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }

                }
            });
            return false;
        }

        function Load_pos_holdInvoiceData(invoiceID) {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: "<?php echo site_url('Pos_restaurant/Load_pos_holdInvoiceData_withDiscount'); ?>",
                data: {invoiceID: invoiceID, template: 2},
                cache: false,
                beforeSend: function () {
                    startLoadPos();
                },
                success: function (data) {
                    stopLoad();
                    $("#log").html(data);
                    calculateFooter();
                    initNumPad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == false) {
                        myAlert('w', 'Local Server is Offline,  Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }

                }
            });
            return false;
        }

        function clearInvoice() {
            $("#log").html('');
        }

        function beforeDeleteItem(id) {
            var tmpIsSamplePrinted = $("#isSamplePrinted_" + id).val();
            if (tmpIsSamplePrinted == 0) {
                deleteDiv(id)
            } else {
                checkPosAuthentication(13, id)
            }
        }

        function deleteDiv(id) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo site_url('Pos_restaurant/delete_menuSalesItem'); ?>",
                data: {id: id},
                cache: false,
                beforeSend: function () {
                    numberOfRequest++;
                    disable_POS_btnSet();
                    startLoadPos();
                },
                success: function (data) {
                    numberOfRequest--;
                    if (numberOfRequest == 0) {
                        enable_POS_btnSet();
                    }
                    stopLoad();
                    if (data['error'] == 0) {
                        if (data.add_on.length > 0) {
                            $.each(data.add_on, function (key, value) {
                                $("#item_row_" + value.menuSalesItemID).remove();
                            });
                        }
                        $("#item_row_" + id).remove();
                        calculateFooter();
                    } else if (data['error'] == 1) {
                        myAlert('e', data['message']);
                    }
                    focus_barcode();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    numberOfRequest--;
                    if (numberOfRequest == 0) {
                        enable_POS_btnSet();
                    }
                    if (jqXHR.status == false) {
                        myAlert('w', 'Local Server is Offline,  Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }

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
                        resetKotButton();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == false) {
                        myAlert('w', 'Local Server is Offline,  Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }

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
                    $("#pos_orderNo").val('New');
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
                $("#pos_orderNo").val('New');
                <!--New-->
                $("#holdInvoiceID_input").val('0');
                $("#holdInvoiceID").val('0');
                calculateFooter();
                $("#paid").val(0);
                $("#promotionID").val('');
                $("#dis_amt").val(0);
                $(".paymentInput ").val('');
                resetPaymentForm();
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
                                resetKotButton();

                                setTimeout(function () {
                                    reset_delivery_order();
                                }, 500);

                            } else {
                                myAlert('d', data['message']);
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            if (jqXHR.status == false) {
                                myAlert('w', 'Local Server is Offline,  Please try again');
                            } else {
                                myAlert('e', 'Message: ' + errorThrown);
                            }

                        }
                    });
                }
            });
        }

        function calculateFinalDiscount() {
            var disPercentage = $("#dis_amt").val();
            if (disPercentage <= 100 && disPercentage >= 0) {
                /** new Logic */
                var templateID = '<?php echo $templateID ?>';
                var grossTotal = 0;

                var totalAmount = parseFloat($("#gross_total").text());
                var totalTax = parseFloat($("#display_totalTaxAmount").text());
                var totalServiceCharge = parseFloat($("#display_totalServiceCharge").text());

                switch (parseInt(templateID)) {
                    case 1:
                        grossTotal = totalAmount;
                        break;
                    case 2:
                        grossTotal = totalAmount + totalTax + totalServiceCharge;
                        break;
                    case 3:
                        grossTotal = totalAmount + totalTax;
                        break;
                    case 4:
                        grossTotal = totalAmount + totalServiceCharge;
                        break;
                    default:
                        grossTotal = totalAmount;
                }


                var discountPercentage = $("#dis_amt").val();

                var discountAmount = (discountPercentage / 100) * grossTotal;

                $("#total_discount_amount").val(discountAmount);
                $("#total_discount").html('(' + discountAmount.toFixed(<?php echo $d ?>) + ')');
                $("#discountAmountFooter").val(discountAmount.toFixed(<?php echo $d ?>));

                var netTotal = grossTotal - discountAmount;

                $("#total_netAmount").html(netTotal.toFixed(<?php echo $d ?>)); //output Net Amount
                $("#final_payable_amt").html(netTotal.toFixed(<?php echo $d ?>)); //output Net Amount
                $("#gross_total_input").val(netTotal);

            } else {

                $("#dis_amt").val(0);
                $("#total_discount_amount").val(0);
                $("#discountAmountFooter").val('');
            }
        }

        app = {};
        app.submit_mode = null;

        function submit_pos_payments() {
            <?php
            if (isset($isHidePrintPreview) && $isHidePrintPreview) {
                echo "app.submit_mode = 'submit_and_send_to_printer';";
            } else {
                echo "app.submit_mode = 'submit_and_print';";
            }
            ?>
            var isDelivery = $("#isDelivery").val();
            var deliveryPersonID = $("#deliveryPersonID").val();
            if (isDelivery == 1) {
                if (deliveryPersonID > 0 || deliveryPersonID == -1) {
                    validateBalanceAmount();
                } else {
                    myAlert('e', 'Please select Delivery person before submit person.')
                    return false;
                }
            } else {
                validateBalanceAmount();
            }
        }

        function submit_and_close_pos_payments() {
            app.submit_mode = 'submit_and_close';
            $("#customerNameTmp").val('');
            $("#customerTelephoneTmp").val('');
            $("#customerAddressTmp").val('');
            $("#customerIDTmp").val('');
            $("#customerEmailTmp").val('');
            //setDeliveryInfo();
            var isDelivery = $("#isDelivery").val();
            var deliveryPersonID = $("#deliveryPersonID").val();
            if (isDelivery == 1) {
                if (deliveryPersonID > 0 || deliveryPersonID == -1) {
                    validateBalanceAmount();
                } else {
                    /*myAlert('e', 'Please select Delivery person before submit person.')
                    return false;*/
                    validateBalanceAmount();
                }
            } else {
                validateBalanceAmount();
            }
        }

        function validateBalanceAmount() {
            var isDelivery = $("#isDelivery").val();
            var isOnTimePayment = $("#deliveryPersonID option:selected").data('otp');
            var deliveryOrder = $("#deliveryOrderID").val();
            if (deliveryOrder > 0) {
                var returnChange = 0;
            } else if (isDelivery == 1 && isOnTimePayment == 1) {
                /** Delivery and on time payment */
                var returnChange = $("#returned_change_toDelivery").val();
            } else {
                var returnChange = parseFloat($("#return_change").text());
            }


            if (returnChange < 0) {
                bootbox.alert('<div class="alert alert-warning" style="color: #293225 !important; background-color: #ffe8c3 !important;;"><strong>Warning</strong><br/><br/><span style="font-size:18px;"> Under payment of <span style="color:red;font-weight:700">' + returnChange + ' <?php echo $companyInfo['company_default_currency'] ?></span></span><br/><br/>Please enter the exact bill amount and submit again.</div>');

            } else {
                saveBill();
            }
            modalFix();
        }

        function saveBill() {
            var formData = $(".form_pos_receipt").serializeArray();

            var date = new Date,
                hour = date.getHours(),
                minute = date.getMinutes(),
                seconds = date.getSeconds(),


                minute = minute > 9 ? minute : "0" + minute;
            seconds = seconds > 9 ? seconds : "0" + seconds;
            hour = hour > 9 ? hour : "0" + hour;

            date = hour + ":" + minute + ":" + seconds;
            formData.push({'name': 'currentTime', 'value': date});

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo site_url('Pos_restaurant/submit_pos_payments'); ?>",
                data: formData,
                cache: false,
                beforeSend: function () {
                    $("#submit_btn_pos_receipt").html('<i class="fa fa-refresh fa-spin"> </i> <?php echo $this->lang->line('common_submit');?>');
                    <!--Submit-->
                    $("#submit_btn").prop('disabled', true);

                },
                success: function (data) {
                    $("#submit_btn_pos_receipt").html('Submit and Print');
                    $("#submit_btn").prop('disabled', false); // Please comment it later
                    $("#backToCategoryBtn").click();
                    if (data['error'] == 0) {
                        //myAlert('s', data['message']);
                        if (app.submit_mode == 'submit_and_print') {
                            loadPrintTemplate(data['invoiceID']);
                        }

                        if (app.submit_mode == 'submit_and_send_to_printer') {
                            loadPrintTemplate(data['invoiceID']);
                            print_paymentReceipt();
							$("#pos_sampleBill").hide();
                            $("#rpos_print_template").hide();
                        }

                        $("#email_invoiceID").val(data['invoiceID']);
                        resetKotButton();
                        clearCreditSales();
                        resetGiftCardForm();
                        clearPromotion();
                        setUpSyncCount(data['sync_pending_bill_count']);

                    } else {
                        myAlert('d', data['message']);
                    }
                    $("#deliveryDateDiv").hide();
                    resetPaymentForm();
                    reset_delivery_order();

                    if (app.submit_mode == 'submit_and_close') {
                        clearSalesInvoice();
                        $("#pos_payments_modal").modal('hide');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#submit_btn_pos_receipt").html('Submit and Print');
                    $("#submit_btn").prop('disabled', false);
                    if (jqXHR.status == false) {
                        myAlert('w', 'Local Server is Offline,  Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }

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
                    $("#submit_btn_pos_receipt").html('Submit and Print');
                    $("#submit_btn").prop('disabled', false); // Please comment it later

                    if (data['error'] == 0) {
                        myAlert('s', data['message']);
                        loadPrintTemplate();
                    } else {
                        myAlert('d', data['message']);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#submit_btn_pos_receipt").html('Submit and Print');
                    $("#submit_btn").prop('disabled', false);
                    if (jqXHR.status == false) {
                        myAlert('w', 'Local Server is Offline,  Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }
                }
            })

        }

        function holdAndCreateNewBill() {
            /** when submit */
            $("#submit_btn_pos_receipt").html('Submit and Print');
            $("#submit_btn").prop('disabled', false); // Please comment it later
            $("#backToCategoryBtn").click();
            resetKotButton();
            clearCreditSales();
            resetGiftCardForm();
            resetPaymentForm();

            /** when hidden*/
            $("#dis_amt").val(0);
            $("#cardTotalAmount").val(0);
            $("#netTotalAmount").val(0);
            $("#serviceCharge").val('<?php echo get_defaultServiceCharge() ?>');
            $("#pos_payments_modal").modal('hide');
            clearSalesInvoice();
            resetPayTypeBtn();
        }

        function confirm_createNewBill() {
            bootbox.confirm('<div class="alert alert-info"><strong> <i class="fa fa-check-circle fa-2x"></i> Sent to Kitchen successfully</string><br/><br/><br/> Do you want to create a new order?</div>', function (result) {
                if (result) {
                    holdAndCreateNewBill()
                }

            });
        }

        function get_currentTime() {
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

            var output = nHour + ":" + nMin + " " + ap;
            return output;

        }

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
                    if (jqXHR.status == false) {
                        myAlert('w', 'Local Server is Offline,  Please try again');
                    } else {
                        myAlert('e', 'Message: ' + errorThrown);
                    }

                }
            });

            return false;
        }

    </script>

<?php
$sync = $this->config->item("sync");
if ($sync) {
    ?>
    <script type="text/javascript" language="javascript">

        $(document).ready(function (e) {
            setTimeout(function () {
                sync_live();
            }, 3000);
        });

        function manual_sync() {
            sync_live();
        }

        function abortSync() {
            $(".show_syncing_progress").hide();
            xhr.abort();
        }

        function sync_live() {
            xhr = $.ajax({
                type: "get",
                url: "<?php echo $this->config->item("sync_url"); ?>",
                data: null,
                async: true,
                beforeSend: function () {
                    $(".sync_progress_icon_container").html('<i class="fa fa-2x fa-cloud-upload text-info blink_me" aria-hidden="true"></i>');
                    $(".show_syncing_progress").show();
                    console.log('request started.');
                },
                success: function (msg) {
                    var obj = jQuery.parseJSON(msg);
                    working = false;
                    if (obj.status == 1) {
                        console.log('updated');
                        setTimeout(function () {
                            sync_live();
                        }, 3000);
                        $(".sync_progress_icon_container").html('<i class="fa fa-2x fa-cloud-upload text-info blink_me" aria-hidden="true"></i>');
                        $(".show_syncing_progress").show();
                    } else if (obj.status == 0) {
                        console.log('update completed');
                        $(".show_syncing_progress").hide();
                        //xhr.abort();
                    } else if (obj.status == 3) {
                        console.log('no internet');
                        /** No internet */
                        bootbox.alert('<div class="alert alert-warning"><strong> <i class="fa fa-wifi" aria-hidden="true"></i> No Internet.</strong></div>');
                        $(".sync_progress_icon_container").html('<i class="fa fa-2x fa-cloud-upload text-red" title="No Internet" aria-hidden="true"></i>');
                        $(".show_syncing_progress").show();
                        // xhr.abort();
                    }
                    setUpSyncCount(obj.sync_pending_bill_count);
                }
            });
        }


    </script>

    <?php
} else {
    ?>
    <script>
        $(document).ready(function (e) {
            setTimeout(function () {
                myAlert('w', 'Sync is not enabled in the system, You can turn it on the config file.')
            }, 5000);

        })
    </script>
    <?php
}
$this->load->view('include/footer', $data);