<?php
/*echo '<pre>';
//print_r($menuCategoryList);
print_r($posConfig_master);
echo '</pre>';*/
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<style>
    .clsMenuCategory {
        cursor: pointer;
    }

    .clsGold {
        color: goldenrod;
    }

    .head1 {
        font-size: 16px;
        font-weight: 700;
    }

    .clsGray {
        color: #a6a6a6;
    }

    .imgThumb {
        height: 25px;
        width: 25px;
    }

    .thumbnail_custom {
        position: relative;
        z-index: 0;
    }

    .thumbnail_custom:hover {
        background-color: transparent;
        z-index: 50;
    }

    .thumbnail_custom span { /*CSS for enlarged image*/
        position: absolute;
        background-color: lightyellow;
        padding: 5px;
        left: -1000px;
        border: 1px dashed gray;
        visibility: hidden;
        color: black;
        text-decoration: none;
    }

    .thumbnail_custom span img { /*CSS for enlarged image*/
        border-width: 0;
        padding: 2px;
    }

    .thumbnail_custom:hover span { /*CSS for enlarged image on hover*/
        visibility: visible;
        top: 0;
        left: 60px; /*position where enlarged image should offset horizontally */

    }

    .headStyle2 {
        color: #000000 !important;
    }

    .headStyle2:hover {
        text-decoration: underline !important;
        color: #3c8dbc !important;;
    }






    .myBreadcrumb {
        display: inline-block;
        /*box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.35);*/
        border : 1px solid #d6d6d6;
        overflow: hidden;
        border-radius: 5px;
    }

    .myBreadcrumb > * {
        text-decoration: none;
        outline: none;
        display: block;
        float: left;
        font-size: 12px;
        line-height: 36px;
        color: black;
        /*need more margin on the left of links to accomodate the numbers*/
        padding: 0 10px 0 30px;
        background: white;
        position: relative;
        transition: all 0.5s;
    }

    .myBreadcrumb > * div {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /*since the first link does not have a triangle before it we can reduce the left padding to make it look consistent with other links*/
    .myBreadcrumb > *:first-child {
        padding-left: 10px;
        border-radius: 5px 0 0 5px; /*to match with the parent's radius*/
    }

    .myBreadcrumb >*:first-child i {
        vertical-align: sub;
    }

    .myBreadcrumb > *:last-child {
        border-radius: 0 5px 5px 0; /*this was to prevent glitches on hover*/
        padding-right: 20px;
        padding-right: 8px;
    }

    /*hover/active styles*/
    .myBreadcrumb a.active, .myBreadcrumb a:hover{
        background: #0277BD;
        color:#ffffff;
    }
    .myBreadcrumb a.active:after, .myBreadcrumb a:hover:after {
        background: #0277BD;
    }

    /*adding the arrows for the myBreadcrumbs using rotated pseudo elements*/
    .myBreadcrumb > *:after {
        content: '';
        position: absolute;
        top: 0;
        right: -18px; /*half of square's length*/
        /*same dimension as the line-height of .myBreadcrumb a */
        width: 36px;
        height: 36px;
        /*as you see the rotated square takes a larger height. which makes it tough to position it properly. So we are going to scale it down so that the diagonals become equal to the line-height of the link. We scale it to 70.7% because if square's:
        length = 1; diagonal = (1^2 + 1^2)^0.5 = 1.414 (pythagoras theorem)
        if diagonal required = 1; length = 1/1.414 = 0.707*/
        transform: scale(0.707) rotate(45deg);
        -ms-transform:scale(0.707) rotate(45deg);
        -webkit-transform:scale(0.707) rotate(45deg);

        /*we need to prevent the arrows from getting buried under the next link*/
        z-index: 1;
        /*background same as links but the gradient will be rotated to compensate with the transform applied*/
        background: white;
        /*stylish arrow design using box shadow*/
        box-shadow:
            2px -2px 0 2px rgba(0, 0, 0, 0.4),
            3px -3px 0 2px rgba(255, 255, 255, 0.1);
        /*
            5px - for rounded arrows and
            50px - to prevent hover glitches on the border created using shadows*/
        border-radius: 0 5px 0 50px;
        transition: all 0.5s;
    }
    /*we dont need an arrow after the last link*/
    .myBreadcrumb > *:last-child:after {
        content: none;
    }
    /*we will use the :before element to show numbers*/
    .myBreadcrumb > *:before {
        /*some styles now*/
        border-radius: 100%;
        width: 20px;
        height: 20px;
        line-height: 20px;
        margin: 8px 0;
        position: absolute;
        top: 0;
        left: 30px;
        background: white;
        background: linear-gradient(#444, #222);
        font-weight: bold;
        box-shadow: 0 0 0 1px #ccc;
    }

    .myBreadcrumb > *:nth-child(n+2) {
        display:none;
    }


    @media (max-width: 767px) {
        .myBreadcrumb > *:nth-last-child(-n+4) {
            display:block;
        }
        .myBreadcrumb > * div {
            max-width: 100px;
        }
    }

    @media (min-width: 768px) {
        .myBreadcrumb > *:nth-last-child(-n+6) {
            display:block;
        }
        .myBreadcrumb > * div {
            max-width: 175px;
        }
    }

</style>


<div class="" style="background-color: #ffffff; padding:8px;" id="menuCategoryList">
    <div id="menuCategoryTitle">

            <span class="head1" onclick="refreshCategory()">
            <i class="fa fa-cutlery clsGold" aria-hidden="true"></i>
                <a href="#" class="headStyle2">
                    <?php echo $this->lang->line('pos_config_menu_categories'); ?>
                </a>
                <!--Menu Categories--> &nbsp;&nbsp;
        </span>
        <!--<button class="btn btn-xs btn-default" type="button"
                onclick="refreshCategory();">
            <i class="fa fa-backward"></i> <?php /*echo $this->lang->line('pos_config_go_back'); */ ?>
        </button>-->
        <button style="display: none;" id="goBackButton" class="btn btn-link"
                onclick="prev_gotoCategory(<?php echo $parentID ? $parentID : 0; ?>,<?php echo $parentLevel && $parentLevel > 0 ? $parentLevel : 0; ?>);"
                rel="tooltip"
                title="" data-original-title="Go Back"><i class="fa fa-arrow-left fa-2x" aria-hidden="true"></i>
            <!--go Back--></button>

        <button class="btn btn-xs btn-default pull-right" type="button" onclick="add_menuCategoryModal();">
            <?php echo $this->lang->line('pos_config_add_category'); ?><!-- Add Category-->
        </button>
        <!--<span class="pull-right"> <i onclick="toggleMenuCategory();" class="fa fa-toggle-on fa-2x clsGray" aria-hidden="true"></i></span>-->
        <hr>
    </div>

    <div>
        <?php echo isset($breadcrumbs) ? $breadcrumbs : '' ?>
    </div>
    <div>

        <table class="<?php echo table_class_pos(2); ?>" id="table_menuCategory_company">
            <thead>
            <tr>
                <!--<th>#</th>-->

                <th><?php echo $this->lang->line('common_image'); ?><!--Image--></th>
                <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                <th><?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--></th>
                <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                <th><?php echo $this->lang->line('pos_config_is_pack'); ?><!--is Pack--></th>
                <th><?php echo $this->lang->line('pos_config_category_colour'); ?><!--Category Colour--></th>
                <th><?php echo $this->lang->line('pos_config_sortoder'); ?><!--sortOder--></th>
                <th>Show Image </th>

                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php
            //var_dump($menuCategoryList);
            if (isset($menuCategoryList) && !empty($menuCategoryList)) {
                //print_r($menuCategoryList);
                $i = 1;
                foreach ($menuCategoryList as $menuCategory) {
                    ?>
                    <tr id="menuRow_<?php echo $menuCategory['menuCategoryID'] ?>">
                        <!--<td><?php /*echo $i;
                                    $i++; */ ?></td>-->

                        <!--onclick="LoadMenus('<?php /*echo $menuCategory['menuCategoryID'] */ ?>')"-->

                        <td class="clsMenuCategory"
                            onclick="checkSubExist('<?php echo $menuCategory['menuCategoryID'] ?>','<?php echo $menuCategory['masterLevelID']; ?>','<?php echo $menuCategory['levelNo']; ?>')">
                            <!--<img src="<?php /*echo base_url($menuCategory['image']) */ ?>" class="imgThumb img-rounded"
                                 alt="<?php /*echo $menuCategory['menuCategoryDescription'] */ ?>">-->
                            <!-- LoadMenus --><a class="thumbnail_custom" href="#thumb">
                                <img src="<?php echo base_url($menuCategory['image']) ?>" class="imgThumb img-rounded"/>
                                <span><img style="max-width: 300px !important;"
                                           src="<?php echo base_url($menuCategory['image']) ?>"/></span>
                            </a>

                        </td>

                        <td class="clsMenuCategory"
                            onclick="checkSubExist('<?php echo $menuCategory['menuCategoryID'] ?>','<?php echo $menuCategory['masterLevelID']; ?>','<?php echo $menuCategory['levelNo']; ?>')">
                                    <span style="font-size:14px !important;">
                                        <?php echo $menuCategory['menuCategoryDescription'] ?>
                                    </span>
                        </td>
                        <td class="clsMenuCategory"
                            onclick="checkSubExist('<?php echo $menuCategory['menuCategoryID'] ?>','<?php echo $menuCategory['masterLevelID']; ?>','<?php echo $menuCategory['levelNo']; ?>')">
                            <span
                                style="font-size:12px !important;"><?php
                                //echo $menuCategory['revenueGLAutoID'];
                                echo $menuCategory['GLDesc'];
                                ?></span>
                        </td>
                        <td class="clsMenuCategory"
                            onclick="checkSubExist('<?php echo $menuCategory['menuCategoryID'] ?>','<?php echo $menuCategory['masterLevelID']; ?>','<?php echo $menuCategory['levelNo']; ?>')"
                            style="text-align: center; font-size:13px !important;">
                            <?php if ($menuCategory['isActive'] == 1) { ?>
                                <span
                                    class="label label-success"><?php echo $this->lang->line('common_active'); ?><!--Active--></span>
                            <?php } else { ?>
                                <span
                                    class="label label-default"><?php echo $this->lang->line('pos_config_in'); ?><!--in-->-
                                    <?php echo $this->lang->line('common_active'); ?><!--Active--></span>
                            <?php } ?>
                        </td>

                        <!--is Pax -->
                        <td style="text-align: center;">

                            <input class="mySwitch" type="checkbox"
                                   id="isPax_<?php echo $menuCategory['menuCategoryID'] ?>" name="isPax"
                                   onchange="updateIsPaxValue(<?php echo $menuCategory['menuCategoryID'] ?>,'mc')"
                                   data-size="mini"
                                   data-on-text="<i class='fa fa-coffee text-purple'></i> <?php echo $this->lang->line('pos_config_pax'); ?>"
                                   data-handle-width="50" data-off-color="default" data-on-color="default"
                                   data-off-text="<?php echo $this->lang->line('common_no'); ?>"
                                   data-label-width="0" <?php if ($menuCategory['isPack'] == 1) {
                                echo 'checked';
                            } ?> /><!--Pax-->


                        </td>

                        <td style="text-align: center">
                            <?php
                            if (!empty($menuCategory['bgColor'])) {
                                echo '<i class="fa fa-square fa-2x" style="color:' . $menuCategory['bgColor'] . '"></i>';
                            }
                            ?>
                        </td>

                        <td><?php echo col_sortOrderMenu($menuCategory['menuCategoryID'], $menuCategory['sortOrder'], 'mc') ?></td>

                        <!--Show Image -->
                        <td style="text-align: center;">
                            <input class="mySwitch" type="checkbox"
                                   id="showImageYN_<?php echo $menuCategory['menuCategoryID'] ?>" name="showImageYN"
                                   onchange="update_showImageYN(<?php echo $menuCategory['menuCategoryID'] ?>,'mc')"
                                   data-size="mini"
                                   data-on-text="<i class='fa fa-image text-green'></i> On"
                                   data-handle-width="50" data-off-color="default" data-on-color="default"
                                   data-off-text="Off"
                                   data-label-width="0" <?php if ($menuCategory['showImageYN'] == 1) {
                                echo 'checked';
                            } ?> /><!-- /show image-->
                        </td>

                        <td style="text-align: left;">
                            <button class="btn btn-xs btn-danger"
                                    onclick="deleteCategory(<?php echo $menuCategory['menuCategoryID'] ?>)"><i
                                    class="fa fa-trash"></i></button>
                            &nbsp;&nbsp;

                            <button class="btn btn-xs btn-default"
                                    onclick="editCategory(<?php echo $menuCategory['menuCategoryID'] ?>)"><i
                                    class="fa fa-edit"></i></button>
                            &nbsp;&nbsp;

                            <?php
                            $t = check_category_subItemExist($menuCategory['menuCategoryID']);
                            echo $t ? '<button rel="tooltip" title="Add Sub Category" class="btn btn-xs btn-default" onclick="add_subSubCategory(' . $menuCategory['menuCategoryID'] . ',' . ($menuCategory['levelNo'] + 1) . ')"> <i class="fa fa-plus"></i> </button>' : '';
                            ?>

                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<div id="outputListOfMenusDetail">
    <!--Menu Detail Content-->
</div>
<div id="outputListOfMenus" style="display: none; background-color: #ffffff;">
    <!--Menu Content  -->
</div>


<script>
    $(document).ready(function (e) {
        //var table = $("#table_menuCategory_company").dataTable();
        $('#table_menuCategory_company').dataTable({
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "fnDrawCallback": function (oSettings) {
                $(".mySwitch").bootstrapSwitch();
            }
        });

        $("#segmentConfigID_addMenu").val('<?php echo $id ?>');
        /*setTimeout(function () {
         $(".mySwitch").bootstrapSwitch();
         }, 200);*/
        /*$('#table_menuCategory_company').on('page.dt', function () {
         setTimeout(function () {
         $(".mySwitch").bootstrapSwitch();
         }, 200);

         });*/


    });

</script>