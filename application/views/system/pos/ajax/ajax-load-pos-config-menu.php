<?php
/*echo '<pre>';
print_r($menuCategoryList);
echo '</pre>';*/
?>
<style>
    .clsMenuCategory {
        cursor: pointer;
    }
</style>
<div ><!--class="container"-->
    <div class="row">
        <div class="col-md-12" style="background-color: #ffffff;">
            <div id="menuCategoryTitle">
                <span style="font-size: 16px; font-weight: 700"> <i class="fa fa-cutlery" style="color:goldenrod;"
                                                                    aria-hidden="true"></i> Menu Categories &nbsp;&nbsp;</span>
                <button class="btn btn-xs btn-default" type="button" onclick="add_menuCategoryModal();">Add Category
                </button>
                <span class="pull-right">
                    <i onclick="toggleMenuCategory();" class="fa fa-toggle-on fa-2x" style="color:#a6a6a6"
                       aria-hidden="true"></i></span>
                <hr>
            </div>
            <div id="menuCategoryList">
                <table class="<?php echo table_class_pos(2); ?>" id="table_menuCategory">
                    <thead>
                    <tr>
                        <!--<th>#</th>-->
                        <th>Image</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (isset($menuCategoryList) && !empty($menuCategoryList)) {
                        //print_r($menuCategoryList);
                        $i = 1;
                        foreach ($menuCategoryList as $menuCategory) {
                            ?>
                            <tr id="menuRow_<?php echo $menuCategory['menuCategoryID'] ?>">
                                <!--<td><?php /*echo $i;
                                    $i++; */ ?></td>-->
                                <td class="clsMenuCategory"
                                    onclick="LoadMenus('<?php echo $menuCategory['menuCategoryID'] ?>')">
                                    <img src="<?php echo base_url($menuCategory['image']) ?>"
                                         style="height: 25px; width:25px;"
                                         alt="<?php echo $menuCategory['menuCategoryDescription'] ?>">
                                </td>
                                <td class="clsMenuCategory"
                                    onclick="LoadMenus('<?php echo $menuCategory['menuCategoryID'] ?>')">
                                    <span style="font-size:14px !important;">
                                        <?php echo $menuCategory['menuCategoryDescription'] ?>
                                    </span>
                                </td>
                                <td class="clsMenuCategory"
                                    onclick="LoadMenus('<?php echo $menuCategory['menuCategoryID'] ?>')"
                                    style="text-align: center; font-size:13px !important;">
                                    <?php if ($menuCategory['isActive'] == 1) { ?>
                                        <span class="label label-success">Active</span>
                                    <?php } else { ?>
                                        <span class="label label-default">in-Active</span>
                                    <?php } ?>
                                </td>
                                <td style="text-align: center;">
                                    <button class="btn btn-xs btn-danger"
                                            onclick="deleteCategory(<?php echo $menuCategory['menuCategoryID'] ?>)"><i
                                            class="fa fa-trash"></i></button>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                    <button class="btn btn-xs btn-default"
                                            onclick="editCategory(<?php echo $menuCategory['menuCategoryID'] ?>)"><i
                                            class="fa fa-edit"></i></button>
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
    </div>


    <div id="outputListOfMenus" style="display: none; background-color: #ffffff;">
        <!--Menu Content  -->
    </div>

</div>


<script>
    $(document).ready(function (e) {
        $("#table_menuCategory").dataTable();
        $("#segmentConfigID_addMenu").val('<?php echo $id ?>');
    });

</script>