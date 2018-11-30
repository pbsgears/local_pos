<style>
    .posContainer {
        background-color: #ffffff;
        padding: 10px;
        margin-top: 10px;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-left: 0px !important;
    }

    .select2-container .select2-selection--single {
        height: 31px !important;
    }

    .select2-container--default .select2-selection--single {
        border-radius: 0px !important;
    }

</style>
<div class="posContainer">

    <div style="font-size:16px;"> Configuration for <?php ?></div>
    <form id="posConfigFrm" name="posConfigFrm">

        <table class="<?php echo table_class() ?>" style="font-size:12px;">
            <thead>
            <tr style="background-color: #fcfff2">
                <th>warehouse</th>
                <th>segmentID</th>
                <th>Industry Type</th>
                <th>POS Template</th>
                <td>&nbsp;</td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <?php echo form_dropdown('wareHouseAutoID', get_warehouse_drop(), '', 'id="wareHouseAutoID" class="form-control"'); ?>
                </td>
                <td>
                    <?php echo form_dropdown('segmentID', get_segment_drop(), '', 'id="segmentID" class="form-control"'); ?>
                </td>
                <td>
                    <?php echo form_dropdown('industrytypeID', get_industryTypes_drop(), '1', 'id="industrytypeID" class="form-control"'); ?>
                </td>
                <td>
                    <?php echo form_dropdown('posTemplateID', get_templateMaster_drop(), '', 'id="posTemplateID" class="form-control"'); ?>
                </td>

                <td>
                    <button class="btn btn-primary btn-sm" onclick="save_posConfig()" type="button">save</button>
                </td>
            </tr>
            </tbody>
        </table>


    </form>


    <hr>

    <table class="<?php echo table_class_pos(1) ?>" id="tbl_segmentIDConfig" style="font-size:12px;">
        <thead>
        <tr>
            <th>Warehouse</th>
            <th>segmentID</th>
            <th>Industry</th>
            <th>POS Template</th>
            <th>Menu</th>
            <th>Crew</th>
            <th>Tables</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>

</div>

<script>
    $(document).ready(function (e) {
        $("#wareHouseAutoID").select2();
        $("#segmentID").select2();
        $("#posTemplateID").select2();
        $("#industrytypeID").select2();
        loadConfigTable();

    });

    function toggleMenuCategory() {
        $("#menuCategoryList").toggle('slow');
        toggleMenuDiv();
    }

    function toggleMenuDiv() {
        $("#outputListOfMenus").toggle('slow');
    }

    function loadConfigTable() {
        var Otable = $('#tbl_segmentIDConfig').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/get_srp_erp_pos_segmentConfig'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                /*if (oSettings.bSorted || oSettings.bFiltered) {
                 for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                 $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                 }
                 }*/
                tableBgColorJs();
            },
            "aoColumns": [
                {"mData": "wareHouseDescription"},
                {"mData": "segmentDes"},
                {"mData": "industryTypeDescription"},
                {"mData": "posTemplateDescription"},
                {"mData": "btn_menu"},
                {"mData": "btn_crew"},
                {"mData": "btn_table"},
                {"mData": "btn_set"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });

    }

    function tableBgColorJs() {
        $('.table tbody').on('click', 'tr', function () {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            }
            else {
                $('.table tbody tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        });
        $("[rel=tooltip]").tooltip();

    }

    function save_posConfig() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/save_posConfig') ?>",
            data: $("#posConfigFrm").serialize(),
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    loadConfigTable();
                } else if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);

            }
        });
        return false;
    }

    function delete_segmentConfig(id) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('Pos_config/delete_segmentConfig') ?>",
                    data: {id: id},
                    dataType: "json",
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 0) {
                            loadConfigTable();
                            myAlert('s', data['message']);
                        } else {
                            myAlert('e', '<div>' + data['message'] + '</div>');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', 'Status: ' + textStatus + '<br>Message: ' + errorThrown);
                    }
                });
            }
        });
    }

    /** POS Menu */
    function posConfig_menu(id) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/posConfig_menu') ?>",
            data: {id: id},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                $("#modal_Menu").modal("show");
                $("#menu_edit_body").html('<?php echo loader_div() ?>');
                //startLoad();
            },
            success: function (data) {
                //stopLoad();
                $("#menu_edit_body").html(data);
                setTimeout(function () {
                    // $("#menu_edit_body").css("height", $(window).height() - 120 + 'px');
                }, 500);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                //stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function LoadMenuCategoryData(id) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/posConfig_menu') ?>",
            data: {id: id},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#menu_edit_body").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function add_menuCategoryModal(id) {
        $("#segmentConfigID").val(id);
        $("#menuCategoryID").val('0');
        $("#menuCategoryDescription").val('');
        $("#isActive").val('1');
        $("#add_menuCategoryModal").modal('show');
    }

    function addMenuCategory() {
        var formData = new FormData($("#fromAddMenuCategory")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_config/addMenuCategory'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    $('#add_menuCategoryModal').modal('hide');
                    LoadMenuCategoryData(data['code']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function deleteCategory(id) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('Pos_config/deleteCategory') ?>",
                    data: {id: id},
                    dataType: "json",
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 0) {
                            $("#menuRow_" + id).hide('slow');
                            myAlert('s', data['message']);
                        } else {
                            myAlert('e', data['message']);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                    }
                });
            }
        });
    }

    function editCategory(id) {
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_config/editCategory'); ?>",
            data: {id: id},
            cache: false,
            beforeSend: function () {
                $('#add_menuCategoryModal').modal('show');
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
                if (data['error'] == 0) {
                    $("#menuCategoryDescription").val(data['menuCategoryDescription']);
                    $("#isActive").val(data['isActive']);
                    $("#menuCategoryID").val(data['menuCategoryID']);
                    $("#image").val('');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    /** Menu - Load Data Table  | Javascript */
    function LoadMenuDataTable(categoryID) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/loadMenuItems') ?>",
            data: {categoryID: categoryID},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                $("#outputListOfMenus").html('<div style="text-align: center; padding:20px 0px;"><i class="fa fa-refresh fa-2x fa-spin" aria-hidden="true"></i> Loading... </div>');
            },
            success: function (data) {
                $("#outputListOfMenus").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function LoadMenus(categoryID) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/loadMenuItems') ?>",
            data: {categoryID: categoryID},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                toggleMenuCategory();
                $("#outputListOfMenus").html('<div style="text-align: center; padding:20px 0px;"><i class="fa fa-refresh fa-2x fa-spin" aria-hidden="true"></i> Loading... </div>');
            },
            success: function (data) {
                $("#outputListOfMenus").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function loadMenuItem_table(id) {
        $('#menuItemTable').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/loadMenuItem_table'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                tableBgColorJs();
            },
            "aoColumns": [
                {"mData": "menuImageOut"},
                {"mData": "menuMasterDescription"},
                {"mData": "selPrice"},
                {"mData": "status"},
                {"mData": "revenueGLAutoID"},
                {"mData": "btn_set"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'menuCatID', 'value': id});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function add_menuModal(id) {
        $("#m_menuMasterID").val('0');
        $("#m_menuCategoryID").val(id);
        $("#fromAddMenu")[0].reset();
        $("#add_menuModal").modal('show');
    }

    /**/
    function addMenu() {
        var formData = new FormData($("#fromAddMenu")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_config/addMenu'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    $('#add_menuModal').modal('hide');
                    LoadMenuDataTable(data['code']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function editMenu(id,categoryID) {
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_config/getEditMenuInfo'); ?>",
            data: {id: id, categoryID:categoryID},
            cache: false,
            beforeSend: function () {
                $('#add_menuModal').modal('show');
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
                if (data['error'] == 0) {
                    $("#m_menuCategoryID").val(data['menuCategoryID']);
                    $("#m_menuMasterID").val(data['menuMasterID']);
                    $("#m_menuMasterDescription").val(data['menuMasterDescription']);
                    $("#m_sellingPrice").val(data['sellingPrice']);
                    $("#m_menuStatus").val(data['menuStatus']);

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }
</script>


<div class="modal fade pddLess" data-backdrop="static" id="modal_Menu" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 95%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title"> Menu </h4></div>
            <div class="modal-body" id="menu_edit_body"
                 style="min-height: 200px; background-color: rgba(0, 0, 0, 0.04);">


            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade pddLess" data-backdrop="static" id="add_menuCategoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="min-width: 50%; margin-top: 6px;">
        <div class="modal-content">
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">Add Menu Category </h4></div>
            <div class="modal-body" id="" style="min-height: 200px; background-color: rgba(0, 0, 0, 0.04);">
                <form id="fromAddMenuCategory" name="fromAddMenuCategory" class="form-horizontal" method="post">
                    <input type="hidden" name="segmentConfigID" id="segmentConfigID_addMenu" value="0">
                    <input type="hidden" name="menuCategoryID" id="menuCategoryID" value="0">

                    <div class="form-group">
                        <label for="menuCategoryDescription" class="col-md-3 control-label">Category Description</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="menuCategoryDescription"
                                   name="menuCategoryDescription" placeholder="Description">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Image</label>
                        <div class="col-md-4">
                            <input type="file" id="image" name="image">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="isActive" class="col-md-3 control-label">Status</label>
                        <div class="col-md-4">
                            <select name="isActive" class="form-control" id="isActive">
                                <option value="1" selected>Active</option>
                                <option value="0">in-Active</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">&nbsp;</label>
                        <div class="col-md-4">
                            <button class="btn btn-sm btn-primary" type="button" onclick="addMenuCategory();">Save
                            </button>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade pddLess" data-backdrop="static" id="add_menuModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="min-width: 50%; margin-top: 6px;">
        <div class="modal-content">
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">Add Menu </h4></div>
            <div class="modal-body" id="" style="min-height: 200px; background-color: rgba(0, 0, 0, 0.04);">
                <form id="fromAddMenu" name="fromAddMenu" class="form-horizontal" method="post">
                    <input type="hidden" name="menuCategoryID" id="m_menuCategoryID" value="0">
                    <input type="hidden" name="menuMasterID" id="m_menuMasterID" value="0">

                    <div class="form-group">
                        <label for="m_menuMasterDescription" class="col-md-3 control-label">Category Description</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="m_menuMasterDescription"
                                   name="menuMasterDescription" placeholder="Description">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="m_sellingPrice" class="col-md-3 control-label">Price </label>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="m_sellingPrice"
                                   name="sellingPrice" placeholder="Price">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Menu Image</label>
                        <div class="col-md-4">
                            <input type="file" id="m_menuImage" name="menuImage">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="isActive" class="col-md-3 control-label" for="m_menuStatus">Status</label>
                        <div class="col-md-4">
                            <select name="menuStatus" class="form-control" id="m_menuStatus">
                                <option value="1" selected>Active</option>
                                <option value="0">in-Active</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">&nbsp;</label>
                        <div class="col-md-4">
                            <button class="btn btn-sm btn-primary" type="button" onclick="addMenu();">Save</button>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>