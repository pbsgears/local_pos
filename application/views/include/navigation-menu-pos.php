<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('navigation_menu', $primaryLanguage);

$companyID = current_companyID();
$companyType = $this->session->userdata("companyType");
$empID = current_userID();


$wareHouseID = $this->db->select('wareHouseID,')->from('srp_erp_warehouse_users')->where(array('userID' => $empID, 'companyID' => $companyID, 'isActive' => 1))
    ->get()->row('wareHouseID');
$imagePath_arr = $this->db->select('imagePath,isLocalPath')->from('srp_erp_pay_imagepath')->get()->row_array();
if ($imagePath_arr['isLocalPath'] == 1) {
    $imagePath = base_url() . 'images/users/';
} else { // FOR SRP ERP USERS
    $imagePath = $imagePath_arr['imagePath'];
}
$company = $this->db->query("select * from srp_erp_company WHERE company_id={$companyID}")->row_array();

$this->session->set_userdata("companyID", trim($companyID));
$this->session->set_userdata("ware_houseID", trim($wareHouseID));
$this->session->set_userdata("imagePath", trim($imagePath));
$this->session->set_userdata("company_code", trim($company['company_code']));
$this->session->set_userdata("company_name", trim($company['company_name']));
$this->session->set_userdata("company_logo", trim($company['company_logo']));


$wareHouseID = $this->db->select('wareHouseID,')->from('srp_erp_warehouse_users')->where(array('userID' => $empID, 'companyID' => $companyID, 'isActive' => 1))
    ->get()->row('wareHouseID');
$imagePath_arr = $this->db->select('imagePath,isLocalPath')->from('srp_erp_pay_imagepath')->get()->row_array();
if ($imagePath_arr['isLocalPath'] == 1) {
    $imagePath = base_url() . 'images/users/';
} else { // FOR SRP ERP USERS
    $imagePath = $imagePath_arr['imagePath'];
}



function handleIfLanguageNotExist($languageRow, $translation)
{
    if (!empty(trim($translation))) {
        return $translation;
    } else {
        return $languageRow;
    }
}

?>
<script>
    $(document).ready(function (e) {
        $('.navMenu').click(function (e) {
            $('li').removeClass('act');
            //$(this).closest('li').addClass('active');
            $(this).parent('li').addClass('act');
            //console.log($(this).parent('li').html());
        })
    })
</script>
<aside class="main-sidebar">
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <?php
                $filePath = imagePath() . $this->session->EmpImage;
                $currentEmp_img = checkIsFileExists($filePath);
                ?>
                <img src="<?php echo $currentEmp_img; ?>" class="" alt="User Image"> <!--img-circle-->
            </div>
            <div class="pull-left info">
                <p><?php echo $name = ucwords($this->session->username); ?></p>
                <!-- <a href="#"><i class="fa fa-circle text-success"></i> Online</a>-->
            </div>

        </div>

        <div class="menu-list-pos">


            <div class="panel panel-default" style="border: 1px solid #ddd;">
                <div class="panel-body tabs" style="padding:3px;">
                    <div style="padding: 0px 0px 0px 15px;">
                        <button id="backToCategoryBtn" style="padding: 11px 11px 9px 7px;" data-toggle="tab"
                                class="btn btn-lg btn-default btnCategoryTab"
                                tabindex="-1"
                                href="#pilltabCategory">
                            <i class="fa-21 fa fa-backward fa-2x"></i>
                        </button>


                        <button id="backToCategoryBtn" onclick="go_one_step_back_category()"
                                style="padding: 11px 12px 9px 7px;" data-toggle="tab"
                                class="btn btn-lg btn-default btnCategoryTab">
                            <i class="fa-21 fa fa-chevron-left fa-2x"></i>
                        </button>


                        <?php
                        $kotBtn = is_show_KOT_button();
                        if ($kotBtn) {
                            ?>
                            <button type="button" onclick="POS_SendToKitchen()" class="btn btn-lg btn-danger buttonDefaultSize"
                                    id="btn_pos_sendtokitchen"><i class="fa fa-cutlery" aria-hidden="true"></i> KOT
                            </button>
                        <?php } ?>

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
                        <div class="tab-content dynamicSizeCategory"   id="allProd">
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


            <div class="pos-footer-summary"></div>



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
                    /*$(".btnStyleCustom").css('height', tmpHeight);
                     $(".btnStyleCustom").css('width', tmpHeight, 'important');*/
                    $(".itemButton").css('min-height', tmpHeight - 10, 'important');
                    $(".itemButton").css('width', tmpHeight - 10, 'important');
                    setCookie('btnSize', tmpHeight - 10);


                }
                function POS_SizeMin() {
                    var containerSize = $(".btnStyleCustom").height();
                    $("#currentSize").val(containerSize);

                    var tmpHeight = parseInt($("#currentSize").val()) - 5;
                    /*$(".btnStyleCustom").css('height', tmpHeight);
                     $(".btnStyleCustom").css('width', tmpHeight, 'important');*/
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



        </div>



    </section>

</aside>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content" id="ajax_body_container">