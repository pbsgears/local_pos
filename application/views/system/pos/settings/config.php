<?php
$role = load_crew_role_drop();
$employee = load_employee_for_crew_drop();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/pos/pos-config.css'); ?>">

<div class="box box-warning">
    <div class="box-body">
        <div class="nav-tabs-custom">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="posContainer">

                        <div style="font-size:16px; font-weight: 800;">
                            <?php echo $this->lang->line('pos_config_point_of_sales_settings'); ?><!--Point of Sales Settings-->
                            for <?php echo current_companyCode() ?></div>
                        <form id="posConfigFrm" name="posConfigFrm">

                            <table class="<?php echo table_class() ?>" style="font-size:12px;">
                                <thead>
                                <tr style="background-color: #fcfff2">
                                    <th><i class="fa fa-building-o" aria-hidden="true"></i>
                                        <?php echo $this->lang->line('pos_config_outlets'); ?><!--Outlets--></th>
                                    <th><?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>
                                    <th>
                                        <?php echo $this->lang->line('pos_config_industry_type'); ?><!--Industry Type--></th>
                                    <th>
                                        <?php echo $this->lang->line('pos_config_pos_template'); ?><!--POS Template--></th>
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
                                        <button class="btn btn-primary btn-sm" onclick="save_posConfig()"
                                                type="button">
                                            <?php echo $this->lang->line('common_save'); ?><!--save-->
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>


                        </form>


                        <hr>

                        <table class="<?php echo table_class_pos(4) ?>" id="tbl_segmentIDConfig"
                               style="font-size:12px width:100%">
                            <thead>
                            <tr>
                                <th><i class="fa fa-building-o" aria-hidden="true"></i>
                                    <?php echo $this->lang->line('pos_config_outlets'); ?><!--Outlets--></th>
                                <th><?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>
                                <th><?php echo $this->lang->line('pos_config_industry'); ?><!--Industry--></th>
                                <th>
                                    <?php echo $this->lang->line('pos_config_pos_template'); ?><!--POS Template--></th>
                                <th><?php echo $this->lang->line('common_menu'); ?><!--Menu--></th>
                                <th><?php echo $this->lang->line('common_crew'); ?><!--Crew--></th>
                                <th><?php echo $this->lang->line('common_tables'); ?><!--Tables--></th>
                                <th><?php echo $this->lang->line('pos_config_kot'); ?><!--KOT--></th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="tab_2">
                    <div id="menu_edit_container2"></div>

                </div>

            </div>
            <!-- /.tab-content -->
        </div>
    </div>
</div>
<script type="text/javascript"
        src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<script>
    $(document).ready(function (e) {
        $("#wareHouseAutoID").select2();
        $("#segmentID").select2();
        $("#posTemplateID").select2();
        $("#industrytypeID").select2();
        $("#mc_revenueGLAutoID").select2();
        loadConfigTable();
        $('.modal').on('hidden.bs.modal', function () {
            modalFix();
        });
        //posConfig_menu_inTab();

        $('#rooms_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                diningRoomDescription: {validators: {notEmpty: {message: 'Room Name is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Pos_config/save_rooms_info'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert('s', data['message']);
                    load_room_table(data['id']);
                    $("#rooms_edit_hn").val('');
                    $("#diningRoomDescription").val('');


                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });

        $('#tables_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                diningTableDescription: {validators: {notEmpty: {message: 'Table Description is required.'}}},
                noOfSeats: {validators: {notEmpty: {message: 'No Of Seats is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Pos_config/save_tables_info'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert('s', data['message']);
                    load_tables_table(data['id']);
                    $("#tables_edit_hn").val('');
                    $("#diningTableDescription").val('');
                    $("#noOfSeats").val('');


                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });

        $('#crew_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            fields: {
                //crewFirstName: {validators: {notEmpty: {message: 'First Name is required.'}}},
                //crewLastName: {validators: {notEmpty: {message: 'Last Name is required.'}}},
                //EIdNo: {validators: {notEmpty: {message: 'Employee ID is required.'}}},
                crewRoleID: {validators: {notEmpty: {message: 'Role is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Pos_config/save_crew_info'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#crewSaveBtn").prop('disabled', false);
                    if (data['error'] == 0) {
                        load_crew_table(data['id']);
                        $("#crew_edit_hn").val('');
                        $('#crew_form').bootstrapValidator('resetForm', true);
                        $('#crew_form')[0].reset();
                        myAlert('s', data['message']);
                    } else if (data['error'] == 1) {
                        myAlert('e', data['message']);
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });


        $('#fromAddMenuitem').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                menuItem: {validators: {notEmpty: {message: 'Description'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Pos_config/save_menu_item'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data['error'] == 0) {
                        myAlert('s', data['message']);
                        loadMenuItem_setup_table(data['id']);
                        $("#add_menuModal_setup").modal('hide');
                    } else {
                        myAlert('e', data['message']);
                    }

                }, error: function () {
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                }
            });
        });


    });

    function load_pack_items(id) {
        $('#packConfigTbl').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/load_packItem_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "menuMasterDescription"},
                {"mData": "menuCategoryDescription"},
                {"mData": "isRequired"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'PackMenuID', 'value': id});
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

    function load_pack_category(id) {
        $('#packConfigCategoryTbl').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/load_pack_category'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "menuCategoryDescription"},
                {"mData": "noOfItems"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'valuePackID', 'value': id});
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

    function toggleMenuDetail() {
        $("#outputListOfMenus").toggle();
        toggleMenuDetailDiv();
    }

    function toggleMenuDetailDiv() {
        $("#outputMenuDetail").toggle();
    }

    function toggleMenuCategory() {
        $("#menuCategoryList").toggle();
        toggleMenuDiv();
    }

    function toggleMenuDiv() {
        $("#outputListOfMenus").toggle();
    }

    /** Menu Setup */
    function toggleMenuCategorySetup() {
        $("#menuCategoryList_setup").toggle();
        toggleMenuDivSetup();
    }

    function toggleMenuDivSetup() {
        $("#outputListOfMenus_setup").toggle();
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
                {"mData": "outletDesc"},
                {"mData": "segmentDes"},
                {"mData": "industryTypeDescription"},
                {"mData": "posTemplateDescription"},
                {"mData": "btn_menu"},
                {"mData": "btn_crew"},
                {"mData": "btn_table"},
                {"mData": "btn_kot"},
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
            },
            success: function (data) {
                $("#menu_edit_body").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    /** POS Menu */
    function setup_menu(id) {

        $("#warehouseIDmenuitem").val(id);
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/setup_menu') ?>",
            data: {id: id},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                $("#modal_Menu").modal("show");
                $("#menu_edit_body").html('<?php echo loader_div() ?>');
            },
            success: function (data) {
                //$("[name='menueCategoryIsactive']").bootstrapSwitch();
                $("#menu_edit_body").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function posConfig_menu_inTab() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/posConfig_menu_company') ?>",
            data: {id: null},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                $("#menu_edit_container2").html('<?php echo loader_div() ?>');
            },
            success: function (data) {
                $("#menu_edit_container2").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function LoadMenuCategoryData() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/posConfig_menu_company') ?>",
            data: {id: null},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#menu_edit_container2").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function add_menuCategoryModal() {
        $("#menuCategoryID").val('0');
        $("#mc_revenueGLAutoID").val('');
        $("#menuCategoryDescription").val('');
        $("#isActive").val('1');
        $("#add_menuCategoryModal").modal('show');
    }

    function add_menuCategory_setupModal() {
        $("#menuCategoryID").val('0');
        $("#add_menuCategoryModal_setup").modal('show');
    }

    function addMenuCategory() {
        var formData = new FormData($("#fromAddMenuCategory")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_config/addMenuCategory_company'); ?>",
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
                    LoadMenuCategoryData();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function addMenuCategory_MenuSetup() {
        var formData = new FormData($("#fromAddMenuCategory_addMenuSetup")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_config/addMenuCategory_setup'); ?>",
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
                    $('#add_menuCategoryModal_setup').modal('hide');
                    setup_menu(data['code']);
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
                    $("#mc_revenueGLAutoID").val(data['revenueGLAutoID']);
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

    function loadMenuItemsSetup(autoID) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/loadwarehouse_MenuItemsSetup') ?>",
            data: {autoID: autoID},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                toggleMenuCategorySetup();
                $("#outputListOfMenus_setup").html('<div style="text-align: center; padding:20px 0px;"><i class="fa fa-refresh fa-2x fa-spin" aria-hidden="true"></i> Loading... </div>');
            },
            success: function (data) {
                $("#outputListOfMenus_setup").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function loadMenuItemsSetup_withoutToggle(autoID) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/loadwarehouse_MenuItemsSetup') ?>",
            data: {autoID: autoID},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                $("#outputListOfMenus_setup").html('<div style="text-align: center; padding:20px 0px;"><i class="fa fa-refresh fa-2x fa-spin" aria-hidden="true"></i> Loading... </div>');
            },
            success: function (data) {
                $("#outputListOfMenus_setup").html(data);
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
                $(".mySwitch").bootstrapSwitch();
            },
            "aoColumns": [
                {"mData": "menuImageOut"},
                {"mData": "menuMasterDescription"},
                {"mData": "selPrice"},
                {"mData": "menuCostTmp"},
                {"mData": "status"},
                {"mData": "isPax_btn"},
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

    function changeShortcut(id) {
        if ($("#shortcutActivate_" + id).is(':checked')) {
            var checkedValue = 1;
        }
        else {
            var checkedValue = 0;
        }

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {autoID: id, checkedValue: checkedValue},
            url: "<?php echo site_url('Pos_config/changeShortcut'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    // nothing
                } else {
                    myAlert('d', data['message']);
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }

    function loadMenuItem_setup_table(id) {

        /** warehouse menu setup table */

        $('#warehouseMenuItemTable').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/loadMenuItem_setup_table'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                tableBgColorJs();
                $("[name='warehousemenumasterisactive']").bootstrapSwitch();
                $("[name='isTaxEnabled']").bootstrapSwitch();
                $("[rel='tooltip']").tooltip();
                $("[name='shortcutActivate']").bootstrapSwitch();
            },
            "aoColumns": [
                {"mData": "menuImageOut"},
                {"mData": "menuMasterDescription"},
                {"mData": "selPrice"},
                {"mData": "status"},
                {"mData": "GLDescription"},
                {"mData": "menu_isTaxEnabled"},
                {"mData": "kotID_tmp"},
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

    function add_menuSeupModal(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'autoID': id},
            url: "<?php echo site_url('Pos_config/fetch_menuitemfor_menucategory'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#menuItem').empty();
                if (jQuery.isEmptyObject(data)) {

                } else {
                    var i = 1;
                    $.each(data, function (key, value) {
                        $('#menuItem').append('<option value="' + value['menuMasterID'] + '">' + value['menuMasterDescription'] + '</option>');
                        i++;
                    });
                }
                $("#wcAutoId").val(id);
                $("#add_menuModal_setup").modal('show');
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();

            }
        });


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

    function editMenu(id, categoryID) {
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_config/getEditMenuInfo'); ?>",
            data: {id: id, categoryID: categoryID},
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

    function deleteMenu(id) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('Pos_config/deleteMenu') ?>",
                    data: {id: id},
                    dataType: "json",
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 0) {
                            $("#menu_row_" + id).hide('slow');
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

    function deleteMenu_setup(id) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('Pos_config/deleteMenu_setup') ?>",
                    data: {warehouseMenuID: id},
                    dataType: "json",
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 0) {
                            $("#menuRow_setup_" + id).hide('slow');
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
    function changeMenueCategoryIsactive(id) {
        var compchecked = 0;
        if ($('#menueCategoryIsactive_' + id).is(":checked")) {
            compchecked = 1;
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {autoID: id, chkedvalue: compchecked},
                url: "<?php echo site_url('Pos_config/update_Menue_Category_Isactive'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert('s', data['message']);
                    if (data['message'] == 's') {

                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });

        }
        else if (!$('#menueCategoryIsactive_' + id).is(":checked")) {

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {autoID: id, chkedvalue: 0},
                url: "<?php echo site_url('Pos_config/update_Menue_Category_Isactive'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert('s', data['message']);
                    if (data['message'] == 's') {

                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    function delete_menue_Category(id) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('Pos_config/delete_menue_Category') ?>",
                    data: {autoID: id},
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
            } else {
                modalFix();
            }
        });
    }
    function changewarehousemenumasterisactive(id) {
        var compchecked = 0;
        if ($('#warehousemenumasterisactive_' + id).is(":checked")) {
            compchecked = 1;
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {warehouseMenuID: id, chkedvalue: compchecked},
                url: "<?php echo site_url('Pos_config/update_Menue_Master_Isactive'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert('s', data['message']);
                    if (data['message'] == 's') {

                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });

        }
        else if (!$('#warehousemenumasterisactive_' + id).is(":checked")) {

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {warehouseMenuID: id, chkedvalue: 0},
                url: "<?php echo site_url('Pos_config/update_Menue_Master_Isactive'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert('s', data['message']);
                    if (data['message'] == 's') {

                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }
    function loadMenuDetail(id) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/loadMenuDetail') ?>",
            data: {categoryID: id},
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

    function load_pos_room_config(id, warehouseid) {
        $("#addRooms").hide();
        $("#showaddrooms").show();
        $('#rooms_Modal').modal('show');
        $('#wareHouseAutoIDhn').val(warehouseid);
        load_room_table(warehouseid);
    }
    function load_room_table(warehouseid) {
        $('#tbl_rooms').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/loadRooms_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "diningRoomDescription"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'wareHouseAutoID', 'value': warehouseid});
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
    function showaddRooms() {
        //$('#rooms_form')[0].reset();
        $("#rooms_edit_hn").val('');
        $("#diningRoomDescription").val('');
        $("#addRooms").show();
        $("#showaddrooms").hide();
    }
    function closeaddRooms() {
        $("#addRooms").hide();
        $("#showaddrooms").show();
    }
    function edit_pos_room_config(id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Pos_config/edit_pos_room_config"); ?>',
            dataType: 'json',
            data: {'diningRoomMasterID': id},
            success: function (data) {
                $('#diningRoomDescription').val(data['diningRoomDescription']);
                $('#rooms_edit_hn').val(id);
                $("#addRooms").show('slow');
                $("#showaddrooms").hide();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function delete_pos_room_config(id, wareHouseAutoID) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("Pos_config/delete_pos_room_config"); ?>',
                    dataType: 'json',
                    data: {'diningRoomMasterID': id},
                    success: function (data) {
                        myAlert('s', data['message']);
                        load_room_table(wareHouseAutoID);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            }
        });


    }
    function add_pos_tables_config(id, warehouseID) {
        $("#addTables").hide();
        $("#showaddtables").show();
        $('#tables_Modal').modal('show');
        $('#diningRoomMasterIDhn').val(id);
        $('#warehouseIDTablehn').val(warehouseID);
        load_tables_table(id);
    }
    function load_tables_table(id) {
        $('#tbl_tables').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/loadTables_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "diningTableDescription"},
                {"mData": "noOfSeats"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'diningRoomMasterID', 'value': id});
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
    function showaddTables() {
        $("#tables_edit_hn").val('');
        $("#diningTableDescription").val('');
        $("#noOfSeats").val('');
        $("#addTables").show('slow');
        $("#showaddtables").hide();
    }
    function closeaddTables() {
        $("#addTables").hide('slow');
        $("#showaddtables").show();
    }
    function edit_pos_table_config(id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Pos_config/edit_pos_table_config"); ?>',
            dataType: 'json',
            data: {'diningTableAutoID': id},
            success: function (data) {
                $('#diningTableDescription').val(data['diningTableDescription']);
                $('#noOfSeats').val(data['noOfSeats']);
                $('#tables_edit_hn').val(id);
                $("#addTables").show('slow');
                $("#showaddtables").hide();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function delete_pos_table_config(id, diningRoomMasterID) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("Pos_config/delete_pos_table_config"); ?>',
                    dataType: 'json',
                    data: {'diningTableAutoID': id},
                    success: function (data) {
                        myAlert('s', data['message']);
                        load_tables_table(diningRoomMasterID);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            }
        });
    }
    function load_pos_crew_config(id) {
        $("#addCrews").hide();
        $("#showaddcrew").show();
        $('#crew_Modal').modal('show');
        $('#wareHouseAutoIDCrewhn').val(id);
        load_crew_table(id);
    }
    function load_crew_table(id) {
        $('#tbl_crew').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/loadCrew_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "crewFirstName"},
                {"mData": "crewLastName"},
                /*{"mData": "EIdNo"},*/
                {"mData": "roleDescription"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'segmentConfigID', 'value': id});
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
    function showaddCrew() {
        $("#crew_edit_hn").val('');
        $('#crew_form')[0].reset();
        $('#crew_form').bootstrapValidator('resetForm', true);
        $("#addCrews").show('slow');
        $("#showaddcrew").hide();
    }
    function closeaddCrew() {
        $("#addCrews").hide('slow');
        $("#showaddcrew").show();
    }
    function loadFnameLname() {
        $fname = $("#EIdNo").find(':selected').attr('data-fname');
        $lname = $("#EIdNo").find(':selected').attr('data-lname');
        /*$("#crewFirstName").attr("readonly", true);
         $("#crewLastName").attr("readonly", true);*/
        $("#crewFirstName").val($fname);
        $("#crewLastName").val($lname);
        if ($("#EIdNo").val() == 0) {
            /*$("#crewFirstName").attr("readonly", false);
             $("#crewLastName").attr("readonly", false);*/
        }
    }
    function edit_pos_crew_config(id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Pos_config/edit_pos_crew_config"); ?>',
            dataType: 'json',
            data: {'crewMemberID': id},
            success: function (data) {
                $('#crewFirstName').val(data['crewFirstName']);
                $('#crewLastName').val(data['crewLastName']);
                $('#EIdNo').val(data['EIdNo']);
                $('#crewRoleID').val(data['crewRoleID']);

                $('#crew_edit_hn').val(id);
                $("#addCrews").show('slow');
                $("#showaddcrew").hide();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }
    function delete_pos_crew_config(id, segmentConfigID) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("Pos_config/delete_pos_crew_config"); ?>',
                    dataType: 'json',
                    data: {'crewMemberID': id},
                    success: function (data) {
                        myAlert('s', data['message']);
                        load_crew_table(segmentConfigID);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            }
        });
    }

    /**
     *
     * PAX config
     *
     * */
    function updateIsPaxValue(id, where, extraID) {
        togglePackBtn(id);
        if (where == 'm') {
            var mcID = extraID;
            var paxValue = $("#isPax_" + id + extraID).is(':checked');
        } else {
            var paxValue = $("#isPax_" + id).is(':checked');
            var mcID = 0;
        }

        if (paxValue) {
            var tmpPaxValue = 1;
        } else {
            var tmpPaxValue = 0;
        }

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {paxValue: tmpPaxValue, id: id, where: where, extraID: mcID},
            url: "<?php echo site_url('Pos_config/updateIsPaxValue'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                } else {
                    myAlert('e', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        /*
         $('#isPax_'+id+extraID).bootstrapSwitch('toggleState', true, true);
         */

    }

    function packConfig_modal(id) {

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/packConfig_modal_data') ?>",
            data: {id: id},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                $("#pack_configModal").modal('show');
                $("#packConfig_modal_body").html('<div style="text-align: center; margin:50px 0px;"><i class="fa fa-refresh fa-spin fa-2x"></i> Loading</div>');
            },
            success: function (data) {
                $("#packConfig_modal_body").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function togglePackBtn(id) {
        $("#packBtnID_" + id).toggle();
    }

    function packLoadItmDetail(thisVal) {
        var tmpValue = thisVal.value;
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/packLoadItmDetail') ?>",
            data: {id: tmpValue},
            dataType: "json",
            cache: false,
            beforeSend: function () {
                var icoSpin = '<i class="fa fa-refresh fa-spin"></i>';
                $("#pax_itemName").html(icoSpin);
                $("#pax_itemCategory").html(icoSpin);
            },
            success: function (data) {
                if (data['error'] == 0) {
                    $("#pax_itemName").html(data['menuMasterDescription']);
                    $("#pax_itemCategory").html(data['menuCategoryDescription']);
                    $("#pack_img_thum").attr('src', data['menuImage']);
                    $("#pack_menuCategoryID").val(data['menuCategoryID']);

                    // $("#pax_itemSellingPrice").html();

                } else if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function save_packItems() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/save_packItems') ?>",
            data: $("#packConfigFrm").serialize(),
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    load_pack_items(data['code']);
                    load_pack_category(data['code']);
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

    function delete_pos_packConfigItem(id) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("Pos_config/delete_pos_packItem"); ?>',
                    dataType: 'json',
                    data: {'id': id},
                    success: function (data) {
                        if (data['error'] == 0) {
                            myAlert('s', data['message']);
                            $("#packItemTbl_" + id).hide();
                        } else if (data['error'] == 1) {
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

    function delete_pos_packItemCategory(id) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("Pos_config/delete_pos_packItemCategory"); ?>',
                    dataType: 'json',
                    data: {'id': id},
                    success: function (data) {
                        if (data['error'] == 0) {
                            myAlert('s', data['message']);
                            $("#packItemCategoryTbl_" + id).hide();
                        } else if (data['error'] == 1) {
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

    function update_pack_noOfItem(thisVal, id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Pos_config/update_pack_noOfItems"); ?>',
            dataType: 'json',
            data: {'noOfItem': thisVal.value, 'id': id},
            success: function (data) {
                console.log(data['message']);
                /*if (data['error'] == 0) {
                 myAlert('s', data['message']);
                 } else if (data['error'] == 1) {
                 myAlert('d', data['message']);
                 }*/
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function update_kotID(tmpVal, KeyVal) {
        var tmpValue = tmpVal.value;
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Pos_config/update_kotID"); ?>',
            dataType: 'json',
            data: {'key': KeyVal, 'value': tmpValue},
            success: function (data) {
                console.log(data['message']);
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                } else if (data['error'] == 1) {
                    myAlert('d', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function change_isTaxEnabled(id) {
        if ($('#isTaxEnabled_' + id).is(":checked")) {
            var checkedStatus = 1;
        }
        else if (!$('#isTaxEnabled_' + id).is(":checked")) {
            var checkedStatus = 0;
        }

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {warehouseMenuID: id, checkedStatus: checkedStatus},
            url: "<?php echo site_url('Pos_config/update_warehouseIsTaxEnabled'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert('s', data['message']);
                if (data['message'] == 's') {

                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
</script>

<div class="modal fade pddLess" data-backdrop="static" id="pack_configModal" role="dialog">
    <div class="modal-dialog" style="min-width: 70%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title"> Pack </h4></div>
            <div class="modal-body" id="packConfig_modal_body"
                 style="min-height: 100px; background-color: rgba(0, 0, 0, 0.04);">
            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade pddLess" data-backdrop="static" id="modal_Menu" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" style="min-width: 95%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title"> <!--Menu--> <strong><span
                            id="outletConfigModalTitle"><?php echo $this->lang->line('common_menu'); ?> </span></strong>
                </h4></div>
            <div class="modal-body" id="menu_edit_body"
                 style="min-height: 200px; background-color: rgba(0, 0, 0, 0.04);">


            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
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
                        <label for="menuCategoryDescription" class="col-md-3 control-label">Category
                            Description</label>

                        <div class="col-md-9">
                            <input type="text" class="form-control" id="menuCategoryDescription"
                                   name="menuCategoryDescription" placeholder="Description">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="mc_revenueGLAutoID" class="col-md-3 control-label">GL Code</label>

                        <div class="col-md-9">
                            <!--<input type="number" class="form-control" id="mc_revenueGLAutoID"
                                   name="revenueGLAutoID" placeholder="GL Code">-->
                            <?php echo form_dropdown('revenueGLAutoID', get_glCode_rpos(), '', 'id="mc_revenueGLAutoID" class="form-control" style="width:100%"'); ?>
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

<div class="modal fade pddLess" data-backdrop="static" id="add_menuCategoryModal_setup" role="dialog">
    <div class="modal-dialog" style="min-width: 50%; margin-top: 6px;">
        <div class="modal-content">
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">
                    <?php echo $this->lang->line('pos_config_add_menu_category'); ?><!--Add Menu Category-->  </h4>
            </div>
            <div class="modal-body" id="" style="min-height: 150px; background-color: rgba(0, 0, 0, 0.04);">
                <form id="fromAddMenuCategory_addMenuSetup" name="fromAddMenuCategory" class="form-horizontal"
                      method="post">
                    <input type="hidden" name="autoID" id="autoID_addMenuSetup" value="0">
                    <input type="hidden" name="segmentConfigID" id="segmentConfigID_menuSetup" value="0">

                    <div class="form-group">
                        <label for="menuCategoryDescription_MenuSetup" class="col-md-3 control-label">
                            <?php echo $this->lang->line('pos_config_select_category'); ?><!--Select Category--></label>

                        <div class="col-md-9">
                            <?php echo form_dropdown('menuCategoryID', get_warehouse_category_drop(), '', 'id="menuCategoryID_MenuSetup" class="form-control select2"'); ?>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-md-3 control-label">&nbsp;</label>

                        <div class="col-md-4">
                            <button class="btn btn-sm btn-primary" type="button"
                                    onclick="addMenuCategory_MenuSetup();">
                                <?php echo $this->lang->line('common_save'); ?><!--Save-->
                            </button>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
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
                        <label for="m_menuMasterDescription" class="col-md-3 control-label">Category
                            Description</label>

                        <div class="col-md-9">
                            <input type="text" class="form-control" id="m_menuMasterDescription"
                                   name="menuMasterDescription" placeholder="Description">
                        </div>
                    </div>

                    <!--<div class="form-group">
                        <label for="m_sellingPrice" class="col-md-3 control-label">Price </label>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="m_sellingPrice"
                                   name="sellingPrice" placeholder="Price">

                        </div>
                    </div>-->

                    <div class="form-group">
                        <label for="m_sellingPrice" class="col-md-3 control-label">Price </label>

                        <div class="col-md-3">
                            <div class="input-group ">
                                <input type="text" class="form-control" id="m_sellingPrice"
                                       name="sellingPrice" placeholder="Price">

                                <div class="input-group-addon">
                                    <?php echo $this->common_data['company_data']['company_default_currency'] ?>
                                </div>
                            </div>
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

<div class="modal fade pddLess" data-backdrop="static" id="add_menuModal_setup" role="dialog">
    <div class="modal-dialog" style="min-width: 50%; margin-top: 6px;">
        <div class="modal-content">
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo $this->lang->line('pos_config_add_menu'); ?><!--Add Menu--> </h4>
            </div>
            <div class="modal-body" style="min-height: 200px; background-color: rgba(0, 0, 0, 0.04);">
                <form id="fromAddMenuitem" name="fromAddMenuitem" class="form-horizontal" method="post">
                    <input type="hidden" name="warehouseIDmenuitem" id="warehouseIDmenuitem">
                    <input type="hidden" name="wcAutoId" id="wcAutoId">

                    <div class="form-group">
                        <label for="ms_menuMasterID" class="col-md-3 control-label">
                            <?php echo $this->lang->line('pos_config_category_description'); ?><!--Category Description--></label>

                        <div class="col-md-9">
                            <!--menuMasterID-->
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="isActive" class="col-md-3 control-label" for="ms_menuStatus">
                            <?php echo $this->lang->line('common_description'); ?><!--Description--></label>

                        <div class="col-md-4">
                            <select name="menuItem" id="menuItem" class="form-control select2new">
                                <option value="0">
                                    <?php echo $this->lang->line('pos_config_select_item'); ?><!--Select Item--></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">&nbsp;</label>

                        <div class="col-md-4">
                            <button class="btn btn-sm btn-primary" type="submit">
                                <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade pddLess" data-backdrop="static" id="rooms_Modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="min-width: 60%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px; ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">
                    <img src="<?php echo base_url('images/pos/waiter-icon.png') ?>" style="height: 26px;" alt="">
                    <?php echo $this->lang->line('pos_config_rooms'); ?><!--Rooms-->
                </h4>
            </div>
            <div class="modal-body" style="min-height: 200px; ">
                <button type="button" id="showaddrooms" onclick="showaddRooms()" style="margin-bottom: 2px;"
                        class="btn btn-default btn-sm pull-right"><i class="fa fa-plus" aria-hidden="true"></i>
                    <?php echo $this->lang->line('pos_config_rooms'); ?><!--Room-->
                </button>
                <form role="form" id="rooms_form" class="form-group">
                    <div id="addRooms"
                         style="display: none; background-image: url('<?php echo base_url("images/pos/bg-restaurant.jpg") ?>'); padding: 10px; margin: -15px -15px 0px; ">
                        <div class="row" style="padding-left: 2%;">
                            <input type="hidden" class="form-control" id="rooms_edit_hn" name="rooms_edit_hn">
                            <input type="hidden" class="form-control" id="wareHouseAutoIDhn"
                                   name="wareHouseAutoIDhn">

                            <div class="form-group col-sm-4">
                                <label class="lbl">
                                    <?php echo $this->lang->line('pos_config_room_name'); ?><!--Room Name--></label>
                                <input type="text" class="form-control" placeholder="Room Name"
                                       id="diningRoomDescription"
                                       name="diningRoomDescription">
                            </div>
                        </div>
                        <div class="row" style="padding-left:4%;">
                            <button onclick="closeaddRooms()" class="btn btn-default btn-xs " type="button">
                                <?php echo $this->lang->line('common_Close'); ?><!--Close-->
                            </button>
                            <button type="submit" class="btn btn-default btn-xs"><i class="fa fa-check"
                                                                                    aria-hidden="true"></i>
                                <?php echo $this->lang->line('pos_config_add_room'); ?><!--Add Room-->
                            </button>
                        </div>
                    </div>
                </form>
                <table class="<?php echo table_class_pos(1) ?>" id="tbl_rooms" style="font-size:12px;">
                    <thead>
                    <tr class="tHeadStyle">
                        <th>#</th>
                        <th>
                            <?php echo $this->lang->line('pos_config_room_description'); ?><!--Room Description--></th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                </table>


            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade pddLess" data-backdrop="static" id="tables_Modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="min-width: 50%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title"><img src="<?php echo base_url("images/pos/Plates-icon.png") ?>" alt="">
                    <?php echo $this->lang->line('common_tables'); ?><!--Tables-->
                </h4></div>
            <div class="modal-body" style="min-height: 200px; ">
                <button type="button" id="showaddtables" onclick="showaddTables()" style="margin-bottom: 2px;"
                        class="btn btn-primary btn-xs pull-right"><i class="fa fa-plus" aria-hidden="true"></i>
                    <?php echo $this->lang->line('common_add'); ?><!--Add-->
                </button>
                <form role="form" id="tables_form" class="form-group">
                    <div id="addTables" style="background-color: #f4f4f4; display: none;">
                        <div class="row" style="padding-left: 2%;">
                            <input type="hidden" class="form-control" id="tables_edit_hn" name="tables_edit_hn">
                            <input type="hidden" class="form-control" id="diningRoomMasterIDhn"
                                   name="diningRoomMasterIDhn">
                            <input type="hidden" class="form-control" id="warehouseIDTablehn"
                                   name="warehouseIDTablehn">

                            <div class="form-group col-sm-4">
                                <label class="lbl">
                                    <?php echo $this->lang->line('pos_config_table_name'); ?><!--Table Name--></label>
                                <input type="text"
                                       placeholder="<?php echo $this->lang->line('pos_config_table_name'); ?>"
                                       class="form-control" id="diningTableDescription"
                                       name="diningTableDescription"><!--Table Name-->
                            </div>

                            <div class="form-group col-sm-4">
                                <label class="lbl">
                                    <?php echo $this->lang->line('pos_config_number_of_seats'); ?><!--NO Of Seats--></label>
                                <input type="number"
                                       placeholder="<?php echo $this->lang->line('pos_config_number_of_seats'); ?>"
                                       class="form-control" id="noOfSeats"
                                       name="noOfSeats"><!--Number Of Seats-->
                            </div>
                        </div>
                        <div class="row" style="padding-left:4%;">
                            <button onclick="closeaddTables()" class="btn btn-default btn-xs" type="button">
                                <?php echo $this->lang->line('common_Close'); ?><!--Close-->
                            </button>
                            <button type="submit" class="btn btn-primary btn-xs"><i class="fa fa-floppy-o"
                                                                                    aria-hidden="true"></i>
                                <?php echo $this->lang->line('pos_config_add_tables'); ?><!--Add Tables-->
                            </button>
                        </div>
                    </div>
                </form>
                <table class="<?php echo table_class_pos(1) ?>" id="tbl_tables" style="font-size:12px;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>
                            <?php echo $this->lang->line('pos_config_tables_description'); ?><!--Tables Description--></th>
                        <th><?php echo $this->lang->line('pos_config_number_of_seats'); ?><!--No Of Seats--></th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                </table>


            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade pddLess" data-backdrop="static" id="crew_Modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="min-width: 60%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_crew'); ?><!--Crew-->
                    <span class="crew_outlet_title"></span></h4>
            </div>
            <div class="modal-body" style="min-height: 200px; ">
                <button type="button" id="showaddcrew" onclick="showaddCrew()" style="margin-bottom: 2px;"
                        class="btn btn-primary btn-xs pull-right"><i class="fa fa-plus" aria-hidden="true"></i>
                    <?php echo $this->lang->line('common_add'); ?><!--Add-->
                </button>
                <form role="form" id="crew_form" class="form-group">
                    <div id="addCrews" style="background-color: #f4f4f4; display: none;">
                        <div class="row" style="padding-left: 2%;padding-right: 2%;">
                            <input type="hidden" class="form-control" id="crew_edit_hn" name="crew_edit_hn">
                            <input type="hidden" class="form-control" id="wareHouseAutoIDCrewhn"
                                   name="wareHouseAutoIDCrewhn">

                            <div class="form-group col-sm-4">
                                <label class="lbl">
                                    <?php echo $this->lang->line('pos_config_employee_id'); ?><!--Employee ID--> (
                                    <?php echo $this->lang->line('pos_config_optional'); ?><!--Optional-->)</label>
                                <select onchange="loadFnameLname()" name="EIdNo" id="EIdNo" class="form-control">
                                    <option value="0">
                                        <?php echo $this->lang->line('common_select_employee'); ?><!--Select Employee--></option>
                                    <?php foreach ($employee as $employ) { ?>
                                        <option data-fname="<?php echo $employ['Ename1'] ?>"
                                                data-lname="<?php echo $employ['Ename2'] ?>"
                                                value="<?php echo $employ['EIdNo']; ?>"><?php echo $employ['ECode'] ?>
                                            | <?php echo $employ['Ename1'] ?> <?php echo $employ['Ename2'] ?></option>
                                    <?php }; ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="lbl">
                                    <?php echo $this->lang->line('pos_config_first_name'); ?><!--First Name--></label>
                                <input type="text" class="form-control" id="crewFirstName" name="crewFirstName">
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="lbl">
                                    <?php echo $this->lang->line('pos_config_last_name'); ?><!--Last Name--></label>
                                <input type="text" class="form-control" id="crewLastName" name="crewLastName">
                            </div>
                        </div>
                        <div class="row" style="padding-left: 2%;">
                            <div class="form-group col-sm-4">
                                <label class="lbl">
                                    <?php echo $this->lang->line('pos_config_role'); ?><!--Role--></label>
                                <select name="crewRoleID" id="crewRoleID" class="form-control">
                                    <option value="">
                                        <?php echo $this->lang->line('pos_config_select_role'); ?><!--Select Role--></option>
                                    <?php foreach ($role as $rol) { ?>
                                        <option
                                            value="<?php echo $rol['crewRoleID']; ?>"><?php echo $rol['roleDescription'] ?></option>
                                    <?php }; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="padding-left:85%;">
                            <button onclick="closeaddCrew()" class="btn btn-default btn-xs" type="button">
                                <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                            <button type="submit" id="crewSaveBtn" class="btn btn-primary btn-xs"><i
                                    class="fa fa-floppy-o"
                                    aria-hidden="true"></i>
                                <?php echo $this->lang->line('pos_config_save_crew'); ?><!--Save Crew-->
                            </button>
                        </div>
                    </div>
                </form>
                <table class="<?php echo table_class_pos(1) ?>" id="tbl_crew" style="font-size:12px;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('pos_config_first_name'); ?><!--First Name--></th>
                        <th><?php echo $this->lang->line('pos_config_last_name'); ?><!--Last Name--></th>
                        <!--<th>Employee ID</th>-->
                        <th><?php echo $this->lang->line('pos_config_role'); ?><!--Role--></th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                </table>


            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<?php
$this->load->view('system/pos/modals/rpos-modal-kot.php');
?>

<script>
    $(document).ready(function () {
        $('.select2new').select2();
        $('#menuCategoryID_MenuSetup').select2();
    });
</script>
