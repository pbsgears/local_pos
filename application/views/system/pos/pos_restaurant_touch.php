<?php
$companyInfo = get_companyInfo();
$templateInfo = get_pos_templateInfo();
$templateID = get_pos_templateID();
$discountPolicy = show_item_level_discount();

$bank_card = load_bank_with_card();
$tr_currency = $this->common_data['company_data']['company_default_currency'];

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$header['extra'] = '';
$header['title'] = $title;
$this->load->view('include/header', $header);
?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/slick/slick/slick.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/slick/slick/slick-theme.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/numPadmaster/jquery.numpad.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/virtual-keyboard-mlkeyboard/jquery.ml-keyboard.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/buttons/button.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos-tab.css') ?>">
    <header class="main-header bg-transparent">

        <a href="<?php echo site_url('dashboard'); ?>" class="logo hidden-xs hidden-sm">
            <span class="logo-mini"><b>ERP</b></span>
            <span class="logo-lg">
                <?php echo '<img style="max-height:30px;"  src="' . base_url() . 'images/' . LOGO . '"/>' ?>
            </span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top margin-zero bg-transparent navbar-fixed-top" role="navigation">
            <div id=""></div>
            <!-- Sidebar toggle button-->


            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">


                    <li class="dropdown">
                        <button type="button" class=" btn btn-lg btn-default " onclick="cancelCurrentOrder()"
                                rel="tooltip" title="">
                            <i class="fa fa-trash-o text-red fa-2x"></i>
                        </button>

                    </li>

                    <li class="dropdown">
                        <button type="button" onclick="BOT()" class="btn btn-lg btn-default"> BOT
                        </button>
                    </li>

                    <li class="dropdown">
                        <button id="backToCategoryBtn" onclick="goToMenu()" style="padding: 11px 11px 9px 7px;"
                                data-toggle="tab"
                                class="btn btn-lg btn-default btnCategoryTab"
                                tabindex="-1"
                                href="#pilltabCategory">
                            <i class="fa fa-backward fa-2x"></i>
                        </button>

                    </li>

                    <li class="dropdown">
                        <button id="backToCategoryBtn" onclick="go_one_step_back_category()"
                                style="padding: 11px 12px 9px 7px;" data-toggle="tab"
                                class="btn btn-lg btn-default btnCategoryTab">
                            <i class="fa fa-chevron-left fa-2x"></i>
                        </button>

                    </li>


                    <li class="dropdown">
                        <button type="button" onclick="POS_SendToKitchen()" class="btn btn-lg btn-default"
                                id="btn_pos_sendtokitchen"> KOT
                        </button>
                    </li>


                    <li class="dropdown">
                        <button type="button" class="btn btn-lg btn-default" id="table_order_btn">
                            <i class="fa fa-life-ring fa-2x" aria-hidden="true"></i>
                        </button>
                    </li>
                    <li class="dropdown">
                        <button type="button" class="btn btn-lg btn-default " onclick="holdReceipt()" rel="tooltip"
                                title="">
                            <i class="fa fa-pause text-yellow fa-2x"></i> <!--<br/> Hold-->
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
                        <button type="button" class=" btn btn-lg btn-default " onclick="deleteItem_tab()"
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
                        <button type="button" class="btn btn-lg btn-default" onclick="logout()"
                                rel="tooltip">
                            <i class="fa fa-power-off text-red fa-2x"
                               title="<?php echo $name = ucwords($this->session->loginusername); ?>" rel="tooltip"
                               data-placement="bottom"></i>
                        </button>
                    </li>

                    <li class="dropdown s-c-768">
                        <button type="button" onclick="goToMenu()" class="btn btn-lg btn-default">
                            <i class="fa fa-bars fa-2x" title="Go to Menu"></i>
                        </button>
                    </li>
                    <li class="dropdown s-c-768">
                        <button type="button" onclick="goToMenuList()" class="btn btn-lg btn-default">
                            <i class="fa fa-list fa-2x" title="Go to Added List"></i>
                        </button>
                    </li>


                    <li class="dropdown s-c-768">
                        <button type="button" onclick="goToFooter()" class="btn btn-lg btn-default">
                            <i class="fa fa-dollar fa-2x" title="Go to Price"></i>
                        </button>
                    </li>


                </ul>
            </div>

        </nav>
        <div style="height: 50px" class="h-m-50">&nbsp;</div>
    </header>


    <div id="posHeader_2" class="hide" style="display: none;">

    </div>
    <div id="form_div">

        <div class="row" style="margin-top: 0px;">
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-7">


                <div class="panel panel-default" id="pos_tab_menu_container">
                    <div class="panel-body tabs" style="padding:3px;">


                        <input type="hidden" id="categoryParentID" value="0">
                        <input type="hidden" id="categoryCurrentID" value="0">

                        <div class="tab-menu-content">
                            <div class="tab-content" id="allProd">
                                <div class="tab-pane fade in active" id="pilltabCategory">
                                    <?php
                                    /** ------ Shortcuts ------  */
                                    $shortcuts = get_warehouseMenuShortcuts();
                                    if ((!empty($shortcuts) && false)) {
                                        ?>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <?php
                                                foreach ($shortcuts as $menu) {
                                                    $isPack = $menu['isPack'];
                                                    $menu['kotID'] = !empty($menu['kotID']) ? $menu['kotID'] : 0;
                                                    ?>
                                                    <div class="btnStyleCustom">
                                                        <button data-code="<?php echo $menu['warehouseMenuID'] ?>"
                                                                data-pack="<?php echo $isPack ?>"
                                                                value="item<?php echo $menu['warehouseMenuID'] ?>"
                                                                style="<?php echo 'background-color: ' . $menu['bgColor'] . ';'; ?>"
                                                                class=" itemButton glass"

                                                                onclick="<?php if ($isPack == 1) {
                                                                    ?>checkisKOT(<?php echo $menu['warehouseMenuID'] ?>, 1, <?php  echo  $menu['kotID'] ?>, '<?php
                                                                echo strtoupper(addslashes($menu['menuMasterDescription'])); ?>')<?php
                                                                } else {
                                                                    ?>checkisKOT(<?php echo $menu['warehouseMenuID'] ?>, 0, <?php  echo  $menu['kotID'] ?>,'<?php echo (strtoupper(addslashes($menu['menuMasterDescription']))); ?>')<?php
                                                                } ?>">

                                                            <div id="proname">
                                                                <?php
                                                                echo strtoupper($menu['menuMasterDescription']);
                                                                $showPrice = isset($isPriceRequired) && $isPriceRequired ? true : false;
                                                                if ($showPrice) {
                                                                    echo '<br>' . $menu['sellingPrice'];
                                                                }

                                                                if (!empty($menu['sizeDescription'])) {
                                                                    if (!empty($menu['colourCode'])) {
                                                                        //echo 'background-color: ' . $menu['colourCode'] . ';';
                                                                        echo '<br/><div><span style="font-weight: 600; font-size: 12px;" >' . $menu['sizeDescription'] . '</span></div>';
                                                                    } else {
                                                                        //echo 'background-color: ' . $menu['bgColor'] . ';';
                                                                        echo '<br/><h6>';
                                                                        echo '<span style="background-color:' . $menu['bgColor'] . '; " class="label label-default">' . $menu['sizeDescription'] . '</span>';

                                                                        echo '</h6>';
                                                                    }
                                                                }
                                                                if ($isPack) {
                                                                    echo '<br/>';
                                                                    echo '<span rel="tooltip" title="pack" >';
                                                                    echo '<i class="fa fa-star " style="color:darkgoldenrod"></i>';
                                                                    echo '<i class="fa fa-star " style="color:darkgoldenrod"></i>';
                                                                    echo '<i class="fa fa-star " style="color:darkgoldenrod"></i>';
                                                                    echo '</span>';
                                                                }
                                                                ?>


                                                            </div>
                                                        </button>
                                                    </div>

                                                    <?php
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
                                                        ?>
                                                        <div class="btnStyleCustom">
                                                            <button type="button" data-toggle="tab" tabindex="-1"
                                                                    data-parent="0"
                                                                    onclick="set_categoryInfo(0, <?php echo $Category['autoID'] ?>)"
                                                                    style="background-color: <?php if (!empty($Category['bgColor'])) {
                                                                        echo $Category['bgColor'];
                                                                    } ?>;"
                                                                    id="categoryBtnID_<?php echo $Category['autoID'] ?>"
                                                                    href="#pilltab<?php echo $Category['autoID'] ?>"
                                                                    class="itemButton btnCategoryTab glass">
                                                    <span id="proname">
                                                    <?php //echo '0,'.$Category['autoID'].'-' ?>
                                                    <?php echo str_replace("'", "&#39;", strtoupper($Category['description'])); ?>
                                                    </span>
                                                            </button>
                                                        </div>
                                                        <?php
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
                                            /*echo '<pre>';
                                            print_r($subCategoryList);
                                            echo '</pre>';*/
                                            if (!empty($subCategoryList)) {
                                                foreach ($subCategoryList as $catList) {
                                                    ?>
                                                    <div class="btnStyleCustom">
                                                        <button type="button" data-toggle="tab" tabindex="-1"
                                                                data-parent="<?php echo $autoID ?>"
                                                                onclick="set_categoryInfo(<?php echo $autoID ?>,<?php echo $catList['autoID'] ?>)"
                                                                style="background-color: <?php if (!empty($catList['bgColor'])) {
                                                                    echo $catList['bgColor'];
                                                                } ?>"
                                                                id="categoryBtnID_<?php echo $catList['autoID'] ?>"
                                                                href="#pilltab<?php echo $catList['autoID'] ?>"
                                                                class="itemButton btnCategoryTab glass">
                                                    <span id="proname">
                                                      <?php //echo $autoID . ',' . $catList['autoID'] . '-' ?>
                                                      <?php echo str_replace("'", "&#39;", strtoupper($catList['description'])); ?>
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
                                                // var_dump($menu);
                                                foreach ($menuList as $menu) {
                                                    $isPack = $menu['isPack'];
                                                    $menu['kotID'] = !empty($menu['kotID']) ? $menu['kotID'] : 0;
                                                    ?>
                                                    <div class="btnStyleCustom">
                                                        <button data-code="<?php echo $menu['warehouseMenuID'] ?>"
                                                                data-pack="<?php echo $isPack ?>"
                                                                value="item<?php echo $menu['warehouseMenuID'] ?>"
                                                                style="<?php echo 'background-color: ' . $menu['bgColor'] . ';'; ?>"
                                                                class=" itemButton glass"

                                                                onclick="<?php if ($isPack == 1) {
                                                                    ?>checkisKOT(<?php echo $menu['warehouseMenuID'] ?>, 1, <?php  echo  $menu['kotID'] ?>, '<?php
                                                                echo strtoupper(addslashes($menu['menuMasterDescription'])); ?>')<?php
                                                                } else {
                                                                    ?>checkisKOT(<?php echo $menu['warehouseMenuID'] ?>, 0, <?php  echo  $menu['kotID'] ?>,'<?php echo (strtoupper(addslashes($menu['menuMasterDescription']))); ?>')<?php
                                                                } ?>">

                                                            <div id="proname">
                                                                <?php
                                                                echo strtoupper($menu['menuMasterDescription']);
                                                                $showPrice = isset($isPriceRequired) && $isPriceRequired ? true : false;
                                                                if ($showPrice) {
                                                                    echo '<br>' . $menu['sellingPrice'];
                                                                }

                                                                if (!empty($menu['sizeDescription'])) {
                                                                    if (!empty($menu['colourCode'])) {
                                                                        //echo 'background-color: ' . $menu['colourCode'] . ';';
                                                                        echo '<br/><div><span style="font-weight: 600; font-size: 12px;" >' . $menu['sizeDescription'] . '</span></div>';
                                                                    } else {
                                                                        //echo 'background-color: ' . $menu['bgColor'] . ';';
                                                                        echo '<br/><h6>';
                                                                        echo '<span style="background-color:' . $menu['bgColor'] . '; " class="label label-default">' . $menu['sizeDescription'] . '</span>';

                                                                        echo '</h6>';
                                                                    }
                                                                }
                                                                if ($isPack) {
                                                                    echo '<br/>';
                                                                    echo '<span rel="tooltip" title="pack" >';
                                                                    echo '<i class="fa fa-star " style="color:darkgoldenrod"></i>';
                                                                    echo '<i class="fa fa-star " style="color:darkgoldenrod"></i>';
                                                                    echo '<i class="fa fa-star " style="color:darkgoldenrod"></i>';
                                                                    echo '</span>';
                                                                }
                                                                ?>


                                                            </div>
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


                <script>
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

                    /*update the status to sent to kitchen of the order*/
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
                                        //confirm_createNewBill();
                                        load_KOT_print_view(data['code'], data['auth']);
                                        $(".isSamplePrintedFlag").val(1);
                                    } else {
                                        load_KOT_print_view($("#holdInvoiceID").val());
                                        myAlert('e', data['message'])
                                    }

                                }, error: function () {
                                    stopLoad();
                                    myAlert('e', 'Error in loading currency denominations.')
                                }
                            });

                        } else {
                            myAlert('e', 'Please place an order and click.')
                        }
                    }

                    function resetKotButton() { // reset the kot button as not send to kitchen color red
                        $('#btn_pos_sendtokitchen').removeClass('btn-success');
                        $('#btn_pos_sendtokitchen').addClass('btn-danger');
                    }
                </script>

            </div> <!-- / col-md-6 -->


            <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5">
                <div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-5 col-lg-5">

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


                    <div class="productCls">
                        <div class="row">

                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                &nbsp;
                            </div>
                            <div class="col-md-8 col-sm-8 col-xs-8">
                                <div class="receiptPaddingHead">
                                    <?php echo $this->lang->line('posr_qyt'); ?><!--Qty--></div>
                                <div class="receiptPaddingHead">Rate</div>
                                <div class="receiptPaddingHead">
                                    <?php echo $this->lang->line('common_total'); ?><!--Total--></div>
                            </div>
                        </div>
                    </div>
                </div>


                <div style="overflow: scroll; height: 250px; width: 100%;" class="dynamicSizeItemList">
                    <form id="posInvoiceForm" class="form_pos_receipt" method="post">
                        <div id="log">

                        </div>
                    </form>
                </div>
                <div id="pos_tab_menu_list_container">&nbsp;</div>
                <div id="pos_tab_footer_price_container">
                    <form class="form_pos_receipt" method="post">
                        <div class="itemListContainer posFooterBgColor">
                            <table class="table table-condensed pos-tab-footer-table">
                                <tr>
                                    <td><?php echo $this->lang->line('posr_total_item'); ?><!--Total Items--> :</td>
                                    <td><span id="total_item_qty" class="posFooterTxtLg">0</span></td>
                                </tr>

                                <!--Total Amount-->
                                <tr>
                                    <td>
                                        <input type="hidden" id="total_item_qty_input" name="total_item_qty_input"/>
                                        <?php echo $this->lang->line('posr_total_amount'); ?><!--Gross Total-->
                                        :
                                    </td>
                                    <td class="ar">
                                        <div id="gross_total">0</div>
                                        <input type="hidden" id="gross_total_amount_input"
                                               name="gross_total_amount_input"
                                               value="0"/>
                                        <input type="hidden" id="gross_total_input" name="gross_total_input"/>
                                    </td>
                                </tr>

                                <tr class="hide">
                                    <td> <?php echo $this->lang->line('common_total'); ?>:<!--Total--></td>
                                    <td class="ar">
                                        <div id="totalWithoutTax">0</div>
                                    </td>
                                </tr>

                                <tr class="<?php if ($templateID == 2 || $templateID == 3) {
                                } else {
                                    echo 'hide';
                                }
                                ?>">
                                    <td><?php echo $this->lang->line('posr_total_tax'); ?><!--Total Tax -->:</td>
                                    <td class="ar">
                                        <div id="display_totalTaxAmount">0.00</div>
                                    </td>
                                </tr>

                                <tr class="<?php if ($templateID == 2 || $templateID == 4) {
                                } else {
                                    echo 'hide';
                                }
                                ?>">
                                    <td><?php echo $this->lang->line('posr_service_charge'); ?> <!--Service Charge-->
                                        :
                                    </td>
                                    <td class="ar">
                                        <div id="display_totalServiceCharge">0.00</div>
                                    </td>
                                </tr>

                                <tr class="hide">
                                    <td><?php echo $this->lang->line('posr_discount'); ?><!--Discount--> % :
                                        <input maxlength="6" class="numpad" onchange="updateDiscountPers()"
                                               type="text"
                                               style="color: black; width: 45px; font-weight: 800; text-align: right;"
                                               name="discount_percentage" id="dis_amt" value="0" readonly>
                                    </td>
                                    <td class="ar">
                                        <div class="hide" id="total_discount">0.00</div>
                                        <input type="text" class="numpad" id="discountAmountFooter"
                                               onchange="backCalculateDiscount()"
                                               style="width: 100%; text-align: right; font-weight: 700; color:#2E2E2E;"
                                               value=""
                                               placeholder="0.00">
                                        <input type="hidden" id="total_discount_amount" name="total_discount_amount"/>
                                    </td>
                                </tr>

                            </table>


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
                            <div class="hide">
                                <div class="btn-group btn-group-lg">
                                    <?php
                                    $defaultID = 0;
                                    foreach ($customerType as $val) {
                                        $isDelivery = 0;
                                        if (trim(strtolower($val['customerDescription'])) == 'delivery orders' || trim(strtolower($val['customerDescription'])) == 'delivery' || trim(strtolower($val['customerDescription'])) == 'delivery order') {
                                            $isDelivery = 1;
                                        }
                                        ?>
                                        <button type="button"
                                                onclick="updateCustomerTypeBtn(<?php echo $val['customerTypeID']; ?>,<?php echo $isDelivery ?>)"
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


            </div>
        </div> <!--/ row -->
    </div>

    <div id="posHeader_1" class="hide"></div>

    <div aria-hidden="true" role="dialog" tabindex="-1" id="pos_menu_list" class="modal fade" data-keyboard="true"
         data-backdrop="static">
        <div class="modal-dialog" style="width: 90%">
            <div class="modal-content">

                <div class="modal-header posModalHeader">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-close text-red"></i></button>
                    <h4 class="modal-title"> Menus </h4>
                </div>


                <div class="modal-footer" style="margin-top: 0px;">
                    <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>

<?php
$this->load->view('include/csrf_footer', '');
$data['notFixed'] = true;
$data['control_sidebar'] = false;
$data['tablet'] = true;

//$this->load->view('system/pos/modals/rpos-barcode', $data);
$this->load->view('system/pos/modals/pos-modal-payments', $data);
$this->load->view('system/pos/modals/rpos-modal-hold-receipt', $data);
$this->load->view('system/pos/modals/rpos-modal-kitchen-status', $data);
$this->load->view('system/pos/modals/rpos-modal-print-template', $data);
$this->load->view('system/pos/modals/rpos-modal-pack-invoice', $data);
$this->load->view('system/pos/modals/rpos-modal-void-invoice', $data);
//$this->load->view('system/pos/modals/rpos-modal-till', $data);
$this->load->view('system/pos/modals/rpos-modal-kitchen-note', $data);
$this->load->view('system/pos/modals/pos-modal-gift-card');
$this->load->view('system/pos/modals/pos-modal-credit-sales');
$this->load->view('system/pos/modals/pos-modal-java-app');
$this->load->view('system/pos/modals/pos-modal-delivery', $data);
$this->load->view('system/pos/modals/pos-modal-table-order', $data);
$this->load->view('system/pos/modals/rpos-modal-auth-process', $data);
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


        $(document).on('ready', function () {

            checkPosSession();

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


            initNumPad();


            /*$('.numpad').keyboard();*/

            setBtnSizeCookie();
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
                    //console.log(code);

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


            $('#rpos_print_template').on('hidden.bs.modal', function () {
                $("#dis_amt").val(0);
                $("#cardTotalAmount").val(0);
                $("#netTotalAmount").val(0);
                $("#serviceCharge").val('<?php echo get_defaultServiceCharge() ?>');
                $("#pos_payments_modal").modal('hide');
                clearSalesInvoice();
                resetPayTypeBtn();
            });

            $('.numberFloat').keypress(function (event) {
                if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });


        });


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
                        myAlert('e', '<br>Message: ' + errorThrown);
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
                    myAlert('e', '<br>Message: ' + errorThrown);
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
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
            return false;
        }


        var parentID_addOn = 0;
        function LoadToInvoice(id, parentID=0, source=0) {
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
                    isFromTablet: 1
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

                        var divTmp = generate_template(data);

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
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
            return false;
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

                    } else if (data['error'] == 0) {
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


                        if (parseInt(data['master']['isOrderPending'])) { /*check order is pending and change the send to kitchen button color*/
                            $('#btn_pos_sendtokitchen').removeClass('btn-danger');
                            $('#btn_pos_sendtokitchen').addClass('btn-success');

                        }
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
                url: "<?php echo site_url('Pos_restaurant/Load_pos_holdInvoiceData_tab'); //Load_pos_holdInvoiceData_withDiscount ?>",
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
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
            return false;
        }


        function deleteDiv(id) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo site_url('Pos_restaurant/delete_menuSalesItem'); ?>",
                data: {id: id},
                cache: false,
                beforeSend: function () {
                    disable_POS_btnSet();
                    startLoadPos();
                },
                success: function (data) {
                    enable_POS_btnSet();
                    stopLoad();
                    if (data['error'] == 0) {
                        $("#item_row_" + id).remove();
                        calculateFooter();
                    } else if (data['error'] == 1) {
                        myAlert('e', data['message']);
                    }
                    //focus_barcode();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    enable_POS_btnSet();
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
                        resetKotButton();
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
                            myAlert('e', '<br>Message: ' + errorThrown);
                        }
                    });
                }
            });
        }

        function logout() {
            bootbox.confirm('<div class="alert alert-danger"> <i class="fa fa-power-off fa-2x" aria-hidden="true"></i> Are you sure want to Close this window?</div>', function (result) {
                if (result) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: "<?php echo site_url('Pos_restaurant/close_shift_touchWindow'); ?>",
                        data: {id: null},
                        cache: false,
                        beforeSend: function () {
                            startLoadPos();
                        },
                        success: function (data) {
                            stopLoad();
                            var logoutURL = '<?php echo site_url('dashboard') ?>';
                            window.location.replace(logoutURL);


                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            myAlert('e', '<br>Message: ' + errorThrown);
                        }
                    });
                }
            });
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
                    $("#submit_btn_pos_receipt").html('Submit');
                    $("#submit_btn").prop('disabled', false); // Please comment it later
                    $("#backToCategoryBtn").click();
                    if (data['error'] == 0) {
                        //myAlert('s', data['message']);
                        loadPrintTemplate(data['invoiceID']);
                        $("#email_invoiceID").val(data['invoiceID']);
                        resetKotButton();
                        clearCreditSales();
                        resetGiftCardForm();
                        clearPromotion();

                    } else {
                        myAlert('d', data['message']);
                    }
                    resetPaymentForm();
                    reset_delivery_order();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#submit_btn_pos_receipt").html('Submit');
                    $("#submit_btn").prop('disabled', false);
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
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

        function submitBOT() {
            var invoiceID = $("#holdInvoiceID").val();
            if (invoiceID > 0) {
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    url: "<?php echo site_url('Pos_restaurant/submitBOT'); ?>",
                    data: {id: invoiceID},
                    cache: false,
                    beforeSend: function () {
                        startLoadPos();
                    },
                    success: function (data) {
                        myAlert(data['e_type'], data['message']);
                        if (data['error'] == 0) {
                            clearSalesInvoice();
                            reset_delivery_order();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            } else {
                swal("Bill Empty", "Please place an order and submit again", "warning");
            }
        }
    </script>
    <script type="text/javascript" src="<?php echo base_url('plugins/pos/pos-tab.js'); ?>"></script>
<?php
$this->load->view('include/footer-pos-tab', $data);
?>