<?php
$tr_currency = $this->common_data['company_data']['company_default_currency'];
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>

<div aria-hidden="true" role="dialog" tabindex="-1" id="pos_menuList_modal" class="modal fade" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog" style="width:96%">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><i class="fa fa-list"></i> <?php echo $this->lang->line('common_menu');?><!--Menu-->  </h4>
            </div>

            <div class="modal-body">


                <div class="panel panel-default" style="border: 1px solid #ddd;">
                    <div class="panel-body tabs" style="padding:3px;">

                        <div style="margin-left: 0px; margin-right: 0px;">
                            <!--<div class="col-md-12" style="padding-left: 15px; padding-right: 15px; padding-top: 10px;">-->
                                <input type="text" class="form-control" placeholder="Press 'F2' or 'Ctrl+F' to Search"
                                       id="searchProd">
                            <!--</div>-->
                        </div>

                        <!--<div class="row" style="margin-left: 0px; margin-right: 0px;">
                            <div class="col-md-12" style="border-bottom: 1px solid #ddd; padding-top: 11px;">-->
                                <div class="regular" style="width:100%">
                                    <div class="mainCategories">
                                        <div data-toggle="tab" class="categoryItemList" href="#pilltabAll">
                                            <?php echo $this->lang->line('common_all');?><!-- All-->
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
                          <!--  </div>
                        </div>-->

                        <div class="tab-content" style="overflow: scroll; height: 300px;" id="allProd">
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


            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-default btn-lg" data-dismiss="modal"
                        style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center; padding: 10px 1px; margin: 5px auto 10px auto; font-weight:bold;">
                    <?php echo $this->lang->line('common_Close');?><!--Close-->
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    function load_pos_menuList_modal() {
        $("#pos_menuList_modal").modal('show');
    }
    $(document).ready(function (e) {
        $('.mainCategories').slick({
            dots: false,
            infinite: false,
            speed: 300,
            slidesToShow: 6,
            adaptiveHeight: false
        });

        setTimeout(function () {
            $(".slick-track").css('width', '100%');
        }, 1000);


        $("#pos_menuList_modal").on('show', function(event){
            setTimeout(function () {
                $(".slick-track").css('width', '100%');
            }, 1000);
        });
    });


</script>