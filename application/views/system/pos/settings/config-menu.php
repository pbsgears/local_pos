<?php
$role = load_crew_role_drop();
$employee = load_employee_for_crew_drop();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>

<style>
    .posContainer {
        background-color: #ffffff;
        padding: 10px;
        /*margin-top: 10px;*/
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

    .comboTitle {
        font-size: 14px;
        color: #057b70;
        font-weight: 700;
    }

    .checkBoxPad {
        padding-left: 20px;
    }

</style>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/pos/pos-config.css'); ?>">
<input type="hidden" id="container_menuItem_id" value="0"/>
<input type="hidden" id="container_config_status" value="0"/>
<div id="menu_edit_container2"></div>

<script type="text/javascript"
        src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<script>

    $(document).ready(function (e) {

        $('input').iCheck({
            checkboxClass: 'icheckbox_flat-red',
            radioClass: 'iradio_flat-red'
        });


        $('.select2').select2();


        $("#wareHouseAutoID").select2();
        $("#segmentID").select2();
        $("#posTemplateID").select2();
        $("#industrytypeID").select2();
        $("#mc_revenueGLAutoID").select2();
        loadConfigTable();
        posConfig_menu_inTab(0, 0);

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-orange',
            radioClass: 'iradio_square_relative-orange',
            increaseArea: '20%'
        });

        $('#rooms_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                diningRoomDescription: {validators: {notEmpty: {message: 'Description is required.'}}}
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
                diningTableDescription: {validators: {notEmpty: {message: 'Description is required.'}}}
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


                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });

        $('#crew_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                crewFirstName: {validators: {notEmpty: {message: 'First Name is required.'}}},
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
                    myAlert('s', data['message']);
                    load_crew_table(data['id']);
                    $("#crew_edit_hn").val('');
                    $("#crewFirstName").attr("readonly", false);
                    $("#crewLastName").attr("readonly", false);
                    $('#crew_form')[0].reset();
                    $('#crew_form').bootstrapValidator('resetForm', true);


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
                    myAlert('s', data['message']);
                    loadMenuItem_setup_table(data['id']);

                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });


        $('#add_menuCategoryModal').on('hidden.bs.modal', function () {
            var addCategoryFromSub = $("#addCategoryFromSub").val();
            var tmpML = $("#masterLevelID_tmp").val();
            var tmpLN = $("#levelNo_tmp").val();
            $("#masterLevelID").val(tmpML);
            $("#levelNo").val(tmpLN);
        });
    });

    function showDetailDivSet() {
        $("#outputListOfMenus").hide();
        $("#outputListOfMenusDetail").show();
        $('html,body').animate({scrollTop: 0}, 'slow');
    }

    function backToMenu() {
        var menuID = $("#container_menuItem_id").val();
        var status = $("#container_config_status").val();
        if (status == 0) {
            $("#outputListOfMenus").show();
            $("#outputListOfMenusDetail").hide();
        } else {
            $("#outputListOfMenus").show();
            $("#outputListOfMenusDetail").hide();
            $("#container_menuItem_id").val(0);
            toggleMenuCategory();
            LoadMenus(menuID);
        }


    }

    function load_pack_items(id) {
        load_pack_items2(id);
        /** $('#packConfigTbl').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php // echo site_url('Pos_config/load_packItem_table'); ?>",
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
        });*/
    }

    function load_pack_items2(id) {

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/load_packItem_table2'); ?>",
            data: {PackMenuID: id},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                $("#table_container_itemList").html('<div style="text-align: center; margin:10px 0px;"><i class="fa fa-refresh fa-spin"></i> Loading</div>')
            },
            success: function (data) {
                $("#table_container_itemList").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);

            }
        });
        return false;
    }

    function load_packGroup_table(id) {
        $('#packConfigCategoryTbl').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/load_packGroup_table'); ?>",
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
                {"mData": "description"},
                {"mData": "inputNum"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'packMenuID', 'value': id});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });

        /**
         $('#packConfigCategoryTbl').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php // echo site_url('Pos_config/load_pack_category'); ?>",
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
         * */
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
        //posConfig_menu_inTab(0,0);
        //resetAddMenuCategoryFormValues(); removed on 27 April 2018
        var parent = $("#masterLevelID_parent").val();
        $("#masterLevelID").val(parent);
        var currentLevel = $("#levelNo").val();
        var preLevel = parseInt(currentLevel) - 1;
        $("#levelNo").val(preLevel);
    }

    function resetAddMenuCategoryFormValues() {
        $("#masterLevelID_parent").val(0);
        $("#levelNo_parent").val(0);
        $("#levelNo_tmp").val(0);
        $("#masterLevelID_tmp").val(0);
        $("#levelNo").val(0);
        $("#masterLevelID").val(0);
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
            } else {
                modalFix();
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

    function posConfig_menu_inTab(masterLevelID, levelNo) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/posConfig_menu_company') ?>",
            data: {masterLevelID: masterLevelID, levelNo: levelNo},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                $("#menu_edit_container2").html('<?php echo loader_div() ?>');
            },
            success: function (data) {
                $("#menu_edit_container2").html(data);
                if (levelNo != 0) {
                    $("#goBackButton").show();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function refreshCategory() {
        posConfig_menu_inTab(0, 0);
        $("#masterLevelID").val(0);
        $("#masterLevelID_tmp").val(0);
        $("#levelNo").val(0);
        $("#levelNo_tmp").val(0);
    }

    function prev_gotoCategory(parentId, parentLevel) {
        var masterLevelID_parent = parentId;
        var levelNo_parent = parentLevel;
        posConfig_menu_inTab(masterLevelID_parent, levelNo_parent);
        $("#masterLevelID").val(masterLevelID_parent);
        $("#masterLevelID_tmp").val(masterLevelID_parent);
        $("#levelNo").val(levelNo_parent);
        $("#levelNo_tmp").val(levelNo_parent);
    }

    function add_subSubCategory(parentCategoryID, nextLevel) {
        $("#addCategoryFromSub").val(1);
        $("#masterLevelID").val(parentCategoryID);
        $("#levelNo").val(nextLevel);

        $("#menuCategoryID").val('0');
        $("#mc_revenueGLAutoID").val('').change();
        $("#menuCategoryDescription").val('');
        $("#isActive").val('1');
        $("#add_menuCategoryModal").modal('show');
        $("#bgColor").val("#ffffff");
        $('#inherit_colour').iCheck('uncheck');
        $("#inheritColorDiv").hide();

    }

    function checkSubExist(menuCategoryID, masterLevelID, levelNo) {

        $("#masterLevelID_parent").val($("#masterLevelID").val());
        $("#levelNo_parent").val($("#levelNo").val());
        $.ajax({
            type: "GET",
            url: "<?php echo site_url('Pos_config/checkSubExist_menuCategory') ?>",
            data: {menuCategoryID: menuCategoryID, masterLevelID: masterLevelID, levelNo: levelNo},
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#masterLevelID").val(menuCategoryID);
                $("#masterLevelID_tmp").val(menuCategoryID);
                $("#levelNo").val(data['levelNo']);
                $("#levelNo_tmp").val(data['levelNo']);
                if (data['masterExist'] == true) {
                    posConfig_menu_inTab(data['menuCategoryID'], data['levelNo']);
                } else if (data['masterExist'] == false) {
                    LoadMenus(data['menuCategoryID']);
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function LoadMenuCategoryData() {
        var levelNo = $("#levelNo").val();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/posConfig_menu_company') ?>",
            data: {masterLevelID: $("#masterLevelID").val(), levelNo: $("#levelNo").val()},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#menu_edit_container2").html(data);
                if (levelNo != 0) {
                    $("#goBackButton").show();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function add_menuCategoryModal() {
        $("#addCategoryFromSub").val(0);
        $("#menuCategoryID").val('0');
        $("#mc_revenueGLAutoID").val('').change();
        $("#menuCategoryDescription").val('');
        $("#isActive").val('1');
        $("#add_menuCategoryModal").modal('show');
        $("#bgColor").val("#ffffff");
        $('#inherit_colour').iCheck('uncheck');
        $("#inheritColorDiv").hide();
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
                    if (data['addCategoryFromSub'] == 1) {
                        $("#levelNo").val(data['levelNo']);
                        $("#masterLevelID").val(data['masterLevelID']);
                        $("#levelNo_tmp").val(data['levelNo']);
                        $("#masterLevelID_tmp").val(data['masterLevelID']);

                    }
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
            } else {
                modalFix();
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
                $("#addCategoryFromSub").val(0);
                $('#add_menuCategoryModal').modal('show');
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
                if (data['error'] == 0) {
                    $('#inherit_colour').iCheck('uncheck');
                    $("#inheritColorDiv").show();
                    $("#menuCategoryDescription").val(data['menuCategoryDescription']);
                    $("#isActive").val(data['isActive']);
                    $("#menuCategoryID").val(data['menuCategoryID']);
                    $("#mc_revenueGLAutoID").val(data['revenueGLAutoID']).change();
                    $("#image").val('');
                    $("#bgColor").val(data['bgColor']);
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

        /*var id = $("#container_menuItem_id").val();
         if (id != categoryID) {*/
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/loadMenuItems') ?>",
            data: {categoryID: categoryID},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                /*toggleMenuCategory();*/
                $("#menuCategoryList").hide();
                $("#outputListOfMenus").show();
                jQuery('html,body').animate({scrollTop: 0}, 0);
                //resetAddMenuCategoryFormValues();

                $("#outputListOfMenus").html('<div style="text-align: center; padding:20px 0px;"><i class="fa fa-refresh fa-2x fa-spin" aria-hidden="true"></i> Loading... </div>');
                jQuery('html,body').animate({scrollTop: 0}, 0);
            },
            success: function (data) {
                $("#container_menuItem_id").val(categoryID);
                $("#outputListOfMenus").html(data);
                $("#container_config_status").val(0);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        /*} else {
         $("#menuCategoryList").hide();
         $("#outputListOfMenus").show();
         jQuery('html,body').animate({scrollTop: 0}, 0);

         }*/


    }

    function loadMenuItemsSetup(autoID) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/loadwarehouse_MenuItemsSetup') ?>",
            data: {autoID: autoID},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                console.log('xxxdd');
                toggleMenuCategorySetup();
                /*$("#menuCategoryList_setup").show();
                 $("#outputListOfMenus_setup").hide();*/
                $("#outputListOfMenus_setup").html('<div style="text-align: center; padding:20px 0px;"><i class="fa fa-refresh fa-2x fa-spin" aria-hidden="true"></i> Loading... </div>');
                //jQuery('html,body').animate({scrollTop:0},0);
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
            "aaSorting": [[0, 'asc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                tableBgColorJs();
                $(".mySwitch").bootstrapSwitch();
                $(".mySwitch_veg").bootstrapSwitch();
                $(".mySwitch_addon").bootstrapSwitch();
            },

            "columnDefs": [
                {"targets": [0], "visible": false}
            ],
            "aoColumns": [
                {"mData": "sortOrder"},
                {"mData": "menuImageOut"},
                {"mData": "menuMasterDescription"},
                {"mData": "sizeDescription"},
                {"mData": "selPrice"},
                {"mData": "menuCostTmp"},
                {"mData": "status"},
                {"mData": "isPax_btn"},
                {"mData": "isVeg_btn"},
                {"mData": "isAddOn_btn"},
                {"mData": "sortOrder"},
                {"mData": "GLDescription"},
                {"mData": "showImageYN_btn"},
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

    function updateSortOrder(tmpValue) {
        var sortOrder = tmpValue.value;
        var id = $(tmpValue).data("id");
        var source = $(tmpValue).data("source");
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {id: id, sortOrder: sortOrder, source: source},
            url: "<?php echo site_url('Pos_config/updateSortOrder'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
            }, error: function () {
                stopLoad();
                myAlert('e', "An Error Occurred! Please Try Again.")
            }
        });

    }

    function loadMenuItem_setup_table(id) {

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
        $('.chkBox').iCheck('uncheck');
        $(".add-content-form-data").show();
        $(".edit-content-form-data").hide();
        $("#m_menuMasterID").val('0');
        $("#m_menuCategoryID").val(id);
        $("#fromAddMenu")[0].reset();
        $("#add_menuModal").modal('show');
        $(".btn-add").hide();
        $("#tax_menuMasterID").val(0);
        load_pricing(0);

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
                    $("#m_menuMasterID").val(data['menuID']);
                    $("#tax_menuMasterID").val(data['menuID'])
                    $("#sc_menuMasterID").val(data['menuID'])
                    myAlert('s', data['message']);
                    //$('#add_menuModal').modal('hide');
                    $('.btn-add').show();
                    $(".add-content-form-data").hide();
                    $(".edit-content-form-data").show();
                    LoadMenuDataTable(data['code']);
                    load_pricing(data['menuID']);
                    if (data['insert'] == 1) {
                        $("#m_revenueGLAutoID").val(data['GLCode']).change();
                    }

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
        $(".add-content-form-data").hide();
        $(".edit-content-form-data").show();
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
                    $("#sc_menuMasterID").val(data['menuMasterID']);
                    $("#m_menuMasterDescription").val(data['menuMasterDescription']);
                    $("#m_sellingPrice").val(data['sellingPrice']);
                    $("#m_menuStatus").val(data['menuStatus']);
                    $("#m_TAXpercentage").val(data['TAXpercentage']);
                    $("#m_taxMasterID").val(data['taxMasterID']);
                    $('.btn-add').show();
                    $("#tax_menuMasterID").val(data['menuMasterID']);
                    $("#m_revenueGLAutoID").val(data['revenueGLAutoID']).change();
                    $("#m_menuSizeID").val(data['menuSizeID']);
                    $("#barcode").val(data['barcode']);

                    load_pricing(id);

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
            } else {
                modalFix();
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
            } else {
                modalFix();
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
                $("#outputListOfMenusDetail").html('<div style="text-align: center; padding:20px 0px;"><i class="fa fa-refresh fa-2x fa-spin" aria-hidden="true"></i> Loading... </div>');
            },
            success: function (data) {
                showDetailDivSet();
                $("#outputListOfMenusDetail").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_pos_room_config(id) {
        $("#addRooms").hide();
        $("#showaddrooms").show();
        $('#rooms_Modal').modal('show');
        $('#wareHouseAutoIDhn').val(id);
        load_room_table(id);
    }
    function load_room_table(id) {
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
                aoData.push({'name': 'wareHouseAutoID', 'value': id});
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
            } else {
                modalFix();
            }
        });
    }

    function add_pos_tables_config(id) {
        $("#addTables").hide();
        $("#showaddtables").show();
        $('#tables_Modal').modal('show');
        $('#diningRoomMasterIDhn').val(id);
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
            } else {
                modalFix();
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
                aoData.push({'name': 'wareHouseAutoID', 'value': id});
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
    function delete_pos_crew_config(id, wareHouseAutoID) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("Pos_config/delete_pos_crew_config"); ?>',
                    dataType: 'json',
                    data: {'crewMemberID': id},
                    success: function (data) {
                        myAlert('s', data['message']);
                        load_crew_table(wareHouseAutoID);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            } else {
                modalFix();
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

    function update_showImageYN(id, where, extraID) {
        if (where == 'm') {
            var mcID = extraID;
            var flagValue = $("#showImageYN_" + id + extraID).is(':checked');
        } else {
            var flagValue = $("#showImageYN_" + id).is(':checked');
            var mcID = 0;
        }

        if (flagValue) {
            var tmpValue = 1;
        } else {
            var tmpValue = 0;
        }

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {inputValue: tmpValue, id: id, where: where, extraID: mcID},
            url: "<?php echo site_url('Pos_config/update_showImageYN'); ?>",
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
    }

    function updateIsVegValue(id, where, extraID) {
        if (where == 'm') {
            var mcID = extraID;
            var vegValue = $("#isVeg_" + id + extraID).is(':checked');
        } else {
            var vegValue = $("#isVeg_" + id).is(':checked');
            var mcID = 0;
        }

        if (vegValue) {
            var tmpVegValue = 1;
        } else {
            var tmpVegValue = 0;
        }

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {vegValue: tmpVegValue, id: id, where: where, extraID: mcID},
            url: "<?php echo site_url('Pos_config/updateIsVegValue'); ?>",
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
                $("#menuID_groping").val(id)
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
                    //load_pack_category(data['code']);
                    $("#frm_status").val(0);
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

    function delete_pos_packGroup(id) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("Pos_config/delete_pos_packGroup"); ?>', /*delete_pos_packItem*/
                    dataType: 'json',
                    data: {'id': id},
                    success: function (data) {
                        if (data['error'] == 0) {
                            myAlert('s', data['message']);
                            $("#packGroup_" + id).hide();
                            $("#frm_status").val(0);
                            load_pack_items($("#PackMenuID").val())
                        } else if (data['error'] == 1) {
                            myAlert('d', data['message']);
                        }
                        modalFix();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        myAlert('e', '<br>Message: ' + errorThrown);
                        modalFix();
                    }
                });
            } else {
                modalFix();
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
            } else {
                modalFix();
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
                if (data['error'] == 0) {
                    // myAlert('s', data['message']);
                } else if (data['error'] == 1) {
                    myAlert('d', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function updateIsAddOnValue(id, where, extraID) {
        if (where == 'm') {
            var mcID = extraID;
            var addonValue = $("#isAddOn_" + id + extraID).is(':checked');
        } else {
            var addonValue = $("#isAddOn_" + id).is(':checked');
            var mcID = 0;
        }

        if (addonValue) {
            var tmpAddOnValue = 1;
        } else {
            var tmpAddOnValue = 0;
        }

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {addonValue: tmpAddOnValue, id: id, where: where, extraID: mcID},
            url: "<?php echo site_url('Pos_config/updateIsAddOnValue'); ?>",
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


    function load_pricing(menuID) {
        $.ajax({
            type: 'post',
            dataType: 'html',
            data: {menuID: menuID},
            url: "<?php echo site_url('Pos_config/load_pricing'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#pricingDiv").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function add_taxModal() {
        $("#add_menuTaxModal").modal('show');
    }


    function save_menuTax() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/save_menuTax') ?>",
            data: $("#from_addMenuTax").serialize(),
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    $("#from_addMenuTax")[0].reset();
                    $("#add_menuTaxModal").modal('hide');
                    load_pricing(data['menuID']);
                    LoadMenuDataTable($("#m_menuCategoryID").val());
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

    function delete_menuTax(id, menuID) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('Pos_config/delete_menuTax') ?>",
                    data: {id: id},
                    dataType: "json",
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 0) {
                            load_pricing(menuID);
                            LoadMenuDataTable($("#m_menuCategoryID").val());
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
            modalFix();
        });
    }


    /** Service Charege */
    function add_serviceChargeModal() {
        $("#add_serviceCharge").modal('show');
    }


    function save_serviceCharge() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/save_serviceCharge') ?>",
            data: $("#from_addServiceCharge").serialize(),
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    $("#from_addServiceCharge")[0].reset();
                    $("#add_serviceCharge").modal('hide');
                    $("#sc_GLAutoID").val('').change();
                    load_pricing(data['menuID'])
                    LoadMenuDataTable($("#m_menuCategoryID").val());
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

    function delete_serviceCharge(id, menuID) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('Pos_config/delete_serviceCharge') ?>",
                    data: {id: id},
                    dataType: "json",
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 0) {
                            load_pricing(menuID);
                            LoadMenuDataTable($("#m_menuCategoryID").val())
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
            modalFix();
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
                <h4 class="modal-title"> Pack / Combo Setup </h4></div>
            <div class="modal-body" id="packConfig_modal_body"
                 style="min-height: 100px; background-color: rgba(0, 0, 0, 0.04);">
            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade pddLess" data-backdrop="static" id="modal_Menu" role="dialog">
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

<div class="modal fade pddLess" data-backdrop="static" id="add_menuCategoryModal" role="dialog">
    <div class="modal-dialog" style="min-width: 50%; margin-top: 6px;">
        <div class="modal-content">
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">
                    <?php echo $this->lang->line('pos_config_add_menu_category'); ?><!--Add Menu Category--> </h4></div>
            <div class="modal-body" id="" style="min-height: 200px;">
                <form id="fromAddMenuCategory" name="fromAddMenuCategory" class="form-horizontal" method="post">
                    <input type="hidden" name="segmentConfigID" id="segmentConfigID_addMenu" value="0">
                    <input type="hidden" name="menuCategoryID" id="menuCategoryID" value="0">
                    <input type="hidden" name="masterLevelID" id="masterLevelID" value="0">
                    <input type="hidden" name="levelNo" id="levelNo" value="0">
                    <input type="hidden" name="masterLevelID_tmp" id="masterLevelID_tmp" value="0">
                    <input type="hidden" name="levelNo_tmp" id="levelNo_tmp" value="0">
                    <input type="hidden" id="masterLevelID_parent" value="0">
                    <input type="hidden" id="levelNo_parent" value="0">
                    <input type="hidden" id="addCategoryFromSub" name="addCategoryFromSub" value="0">

                    <div class="form-group">
                        <label for="menuCategoryDescription" class="col-md-3 control-label">
                            <?php echo $this->lang->line('pos_config_category_description'); ?><!--Category Description--></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="menuCategoryDescription"
                                   name="menuCategoryDescription" placeholder="Description">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="mc_revenueGLAutoID" class="col-md-3 control-label">
                            <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--></label>
                        <div class="col-md-9">
                            <!--<input type="number" class="form-control" id="mc_revenueGLAutoID"
                                   name="revenueGLAutoID" placeholder="GL Code">-->
                            <?php echo form_dropdown('revenueGLAutoID', get_glCode_rpos(), '', 'id="mc_revenueGLAutoID" class="form-control" style="width:100%"'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo $this->lang->line('common_image'); ?><!--Image--></label>
                        <div class="col-md-4">
                            <input type="file" id="image" name="image">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="isActive" class="col-md-3 control-label">
                            <?php echo $this->lang->line('common_status'); ?><!--Status--></label>
                        <div class="col-md-4">
                            <select name="isActive" class="form-control" id="isActive">
                                <option value="1">
                                    <?php //echo $this->lang->line('pos_config_selected'); ?><!--selected-->
                                    <?php echo $this->lang->line('common_active'); ?><!--Active-->
                                </option>
                                <option value="0">
                                    <?php echo $this->lang->line('common_in_active'); ?><!--Inactive--></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo $this->lang->line('pos_config_category_colour'); ?><!--Category Colour-->
                        </label>
                        <div class="col-md-4">
                            <input type="color" class="form-control" id="bgColor" name="bgColor" value="#ffffff">
                        </div>
                    </div>

                    <div class="form-group" id="inheritColorDiv">
                        <label class="col-md-3 control-label">
                            Inherit Colour
                            <i class="fa fa-question"
                               title="If you click this checkbox, it will apply the background colour to all sub categories."
                               aria-hidden="true"></i>
                        </label>
                        <div class="col-md-4" style="margin-top: 5px; margin-bottom: 10px;">
                            <input type="checkbox" id="inherit_colour" name="inherit_colour" value="1">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">&nbsp;</label>
                        <div class="col-md-4">
                            <button class="btn btn-sm btn-primary" type="button" onclick="addMenuCategory();">
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

<div class="modal fade pddLess" data-backdrop="static" id="add_menuCategoryModal_setup" role="dialog">
    <div class="modal-dialog" style="min-width: 50%; margin-top: 6px;">
        <div class="modal-content">
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">Add Menu Category </h4></div>
            <div class="modal-body" id="" style="min-height: 150px; background-color: rgba(0, 0, 0, 0.04);">
                <form id="fromAddMenuCategory_addMenuSetup" name="fromAddMenuCategory" class="form-horizontal"
                      method="post">
                    <input type="hidden" name="autoID" id="autoID_addMenuSetup" value="0">
                    <input type="hidden" name="segmentConfigID" id="segmentConfigID_menuSetup" value="0">

                    <div class="form-group">
                        <label for="menuCategoryDescription_MenuSetup" class="col-md-3 control-label">Select
                            Category</label>
                        <div class="col-md-9">
                            <?php echo form_dropdown('menuCategoryID', get_warehouse_category_drop(), '', 'id="menuCategoryID_MenuSetup" class="form-control"'); ?>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-md-3 control-label">&nbsp;</label>
                        <div class="col-md-4">
                            <button class="btn btn-sm btn-primary" type="button" onclick="addMenuCategory_MenuSetup();">
                                Save
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

<div class="modal fade pddLess" data-backdrop="static" id="add_menuModal" role="dialog">
    <div class="modal-dialog" style="min-width: 50%; margin-top: 6px;">
        <div class="modal-content">
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo $this->lang->line('pos_config_add_menu'); ?><!--Add Menu--> </h4>
            </div>
            <div class="modal-body" id="" style="min-height: 200px;">
                <form id="fromAddMenu" name="fromAddMenu" class="form-horizontal" method="post">
                    <input type="hidden" name="menuCategoryID" id="m_menuCategoryID" value="0">
                    <input type="hidden" name="menuMasterID" id="m_menuMasterID" value="0">

                    <div class="form-separator">
                        <?php echo $this->lang->line('pos_config_menu_information'); ?><!-- Menu information-->
                    </div>
                    <div class="form-group">
                        <label for="m_menuMasterDescription" class="col-md-3 control-label">
                            <?php echo $this->lang->line('pos_config_menu_name'); ?><!--Menu Name--></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="m_menuMasterDescription"
                                   name="menuMasterDescription"
                                   placeholder="<?php echo $this->lang->line('pos_config_menu_name'); ?>">
                            <!--Description-->
                        </div>
                    </div>


                    <!--<div class="form-group">
                        <label for="m_sellingPrice" class="col-md-3 control-label" title="including VAT">
                            <?php /*echo $this->lang->line('pos_config_price'); */ ?>  </label>

                        <div class="col-md-3">
                            <div class="input-group">

                                <div class="input-group-addon">
                                    <?php /*echo $this->common_data['company_data']['company_default_currency'] */ ?>
                                </div>

                            </div>
                        </div>
                    </div>-->

                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo $this->lang->line('pos_config_menu_image'); ?><!--Menu Image--></label>
                        <div class="col-md-4">
                            <input type="file" id="m_menuImage" name="menuImage">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="m_menuStatus">
                            <?php echo $this->lang->line('common_status'); ?><!--Status--></label>
                        <div class="col-md-4">
                            <select name="menuStatus" class="form-control" id="m_menuStatus">
                                <option value="1">
                                    <?php //echo $this->lang->line('pos_config_selected'); ?><!--selected-->
                                    <?php echo $this->lang->line('common_active'); ?><!--Active--></option>
                                <option value="0">
                                    <?php echo $this->lang->line('pos_config_in'); ?><?php echo strtolower($this->lang->line('common_active')); ?><!--active-->
                                </option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-md-3 control-label" for="m_menuSizeID">
                            <?php echo $this->lang->line('pos_config_size'); ?><!--Size--></label>
                        <div class="col-md-4">
                            <?php echo form_dropdown('menuSizeID', get_menuSizes_drop(), '', 'id="m_menuSizeID" class="form-control"'); ?>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-md-3 control-label"> Barcode <i class="fa fa-barcode"></i> </label>
                        <div class="col-md-4">
                            <input type="text" name="barcode" id="barcode" class="form-control"/>
                        </div>
                    </div>

                    <!--GL Code XXX -->
                    <div class="form-group edit-content-form-data">
                        <label for="m_revenueGLAutoID" class="col-md-3 control-label">
                            <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--></label>
                        <div class="col-md-9">
                            <?php echo form_dropdown('revenueGLAutoID', get_glCode_rpos(), '', 'id="m_revenueGLAutoID" class="form-control select2" style="width:100%"'); ?>
                        </div>
                    </div>

                    <!-- ************************* PRICING  *************************   -->


                    <div class="form-separator">
                        <?php echo $this->lang->line('pos_config_pricing'); ?><!-- Pricing-->
                        - <?php echo $this->common_data['company_data']['company_default_currency'] ?>
                    </div>

                    <style>
                        .pricingInput {
                            height: 23px !important;
                        }

                        .ar {
                            text-align: right !important;
                        }

                        .pad-price {
                            font-size: 12px;
                            margin: 5px 0px 0px 3px;
                        }
                    </style>
                    <div class="pull-right">
                        <button class="btn btn-primary btn-xs btn-add" type="button" onclick="add_taxModal()"
                                style="display: none;">
                            Add Tax
                        </button>
                        &nbsp;&nbsp;
                        <button class="btn btn-primary btn-xs btn-add" type="button" onclick="add_serviceChargeModal()"
                                style="display: none;">
                            Add Service Charge
                        </button>
                    </div>
                    <div id="pricingDiv"></div>


                    <div class="form-group hide">
                        <label for="m_sellingPrice_XXX" class="col-md-3 control-label" title="including VAT">
                            <?php echo $this->lang->line('pos_config_price'); ?><!--Price-->
                            Price without Tax
                        </label>

                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" class="form-control" id="m_sellingPrice_XXX"
                                       name="sellingPrice_XXX"
                                       placeholder="<?php echo $this->lang->line('pos_config_price'); ?>">
                            </div>
                        </div>
                    </div>
                    <!-- Tax Type -->
                    <div class="form-group hide">
                        <label class="col-md-3 control-label" for="m_taxMasterID">
                            <?php echo $this->lang->line('pos_config_tax_type'); ?><!--Tax Type-->
                        </label>
                        <div class="col-md-4">
                            <?php echo form_dropdown('taxMasterID', get_taxType_drop(), '', 'id="m_taxMasterID" class="form-control"'); ?>
                        </div>
                    </div>

                    <!-- TAX Percentage  -->
                    <div class="form-group hide">
                        <label class="col-md-3 control-label" for="m_TAXpercentage">
                            <?php echo $this->lang->line('pos_config_tax_percentage'); ?><!--TAX Percentage-->
                        </label>
                        <div class="col-md-2">

                            <div class="input-group">
                                <input type="text" class="form-control" id="m_TAXpercentage" name="TAXpercentage">
                                <span class="input-group-addon">%</span>
                            </div>
                        </div>
                    </div>


                    <div class="add-content-form-data">
                        <div class="form-separator">
                            <?php echo $this->lang->line('pos_config_outlets'); ?><!--Outlets--></div>

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="checkboxes">
                                <?php echo $this->lang->line('pos_config_applicable_outlets'); ?><!--Applicable Outlets--> </label>
                            <div class="col-md-6">
                                <?php
                                $outlets = get_active_outletInfo();
                                if (!empty($outlets)) {
                                    foreach ($outlets as $outlet) {
                                        ?>
                                        <div class="skin skin-square checkBoxPad">
                                            <div class="skin-section extraColumns">
                                                <input id="warehouseID_<?php echo $outlet['wareHouseAutoID']; ?>"
                                                       type="checkbox"
                                                       data-caption="" class="columnSelected chkBox"
                                                       name="outlets[<?php echo $outlet['wareHouseAutoID']; ?>]"
                                                       value="<?php echo $outlet['wareHouseAutoID']; ?>">
                                                <label for="warehouseID_<?php echo $outlet['wareHouseAutoID']; ?>">
                                                    <?php echo $outlet['wareHouseCode'] . ' - ' . $outlet['wareHouseDescription'] . ' - ' . $outlet['wareHouseLocation']; ?>
                                                </label>
                                            </div>
                                        </div>

                                        <?php
                                    }
                                } else {
                                    echo 'Outlet not configured!';
                                }
                                ?>
                            </div>
                        </div>

                        <div class="form-separator">
                            <?php echo $this->lang->line('pos_config_combo_and_pack_setup'); ?><!--Combo & Pack setup-->
                            (<?php echo $this->lang->line('pos_config_optional'); ?><!--Optional-->)
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="checkboxes">
                                <?php echo $this->lang->line('pos_config_applicable_groups_in_combo_pack'); ?><!--Applicable Groups in Combo
                                /Pack--></label>
                            <div class="col-md-6">

                                <!-- apply changes-->&nbsp;
                                <?php
                                $packGroups = get_menuPack_groups();
                                if (!empty($packGroups)) {
                                    $menuID = 0;
                                    foreach ($packGroups as $packGroup) {

                                        if ($packGroup['menuMasterID'] != $menuID) {
                                            echo "<div class='comboTitle'>" . $packGroup['menuMasterDescription'] . "</div>";
                                        }
                                        $menuID = $packGroup['menuMasterID'];


                                        ?>
                                        <div class="skin skin-square checkBoxPad">
                                            <div class="skin-section extraColumns">
                                                <input id="packGroupID_<?php echo $packGroup['groupMasterID']; ?>"
                                                       type="checkbox"
                                                       data-caption="" class="columnSelected chkBox"
                                                       name="packGroups[<?php echo $packGroup['menuMasterID']; ?>]"
                                                       value="<?php echo $packGroup['groupMasterID']; ?>">
                                                <label for="packGroupID_<?php echo $packGroup['groupMasterID']; ?>">
                                                    <?php echo $packGroup['description']; ?></label>
                                            </div>
                                        </div>

                                        <?php
                                    }
                                } else {
                                    echo 'Pack or Combos not configured!';
                                }
                                ?>

                            </div>
                        </div>


                    </div>
                    <br>

                    <div class="form-group">
                        <label class="col-md-3 control-label">&nbsp;</label>
                        <div class="col-md-4">
                            <button class="btn btn-sm btn-primary" type="button" onclick="addMenu();">
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

<div class="modal fade pddLess" data-backdrop="static" id="add_menuModal_setup" role="dialog">
    <div class="modal-dialog" style="min-width: 50%; margin-top: 6px;">
        <div class="modal-content">
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">Add Menu </h4></div>
            <div class="modal-body" style="min-height: 200px; background-color: rgba(0, 0, 0, 0.04);">
                <form id="fromAddMenuitem" name="fromAddMenuitem" class="form-horizontal" method="post">
                    <input type="hidden" name="warehouseIDmenuitem" id="warehouseIDmenuitem">
                    <input type="hidden" name="wcAutoId" id="wcAutoId">

                    <div class="form-group">
                        <label for="ms_menuMasterID" class="col-md-3 control-label"> Category
                            Description</label>
                        <div class="col-md-9">
                            <!--menuMasterID-->
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="isActive" class="col-md-3 control-label" for="ms_menuStatus">Description</label>
                        <div class="col-md-4">
                            <select name="menuItem" id="menuItem" class="form-control">
                                <option value="0">Select Item</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">&nbsp;</label>
                        <div class="col-md-4">
                            <button class="btn btn-sm btn-primary" type="submit">Save</button>
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


<div class="modal fade pddLess" data-backdrop="static" id="rooms_Modal" role="dialog">
    <div class="modal-dialog" style="min-width: 60%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px; ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">
                    <img src="<?php echo base_url('images/pos/waiter-icon.png') ?>" style="height: 26px;" alt="">
                    Rooms
                </h4>
            </div>
            <div class="modal-body" style="min-height: 200px; ">
                <button type="button" id="showaddrooms" onclick="showaddRooms()" style="margin-bottom: 2px;"
                        class="btn btn-default btn-sm pull-right"><i class="fa fa-plus" aria-hidden="true"></i> Room
                </button>
                <form role="form" id="rooms_form" class="form-group">
                    <div id="addRooms"
                         style="display: none; background-image: url('<?php echo base_url("images/pos/bg-restaurant.jpg") ?>'); padding: 10px; margin: -15px -15px 0px; ">
                        <div class="row" style="padding-left: 2%;">
                            <input type="hidden" class="form-control" id="rooms_edit_hn" name="rooms_edit_hn">
                            <input type="hidden" class="form-control" id="wareHouseAutoIDhn" name="wareHouseAutoIDhn">
                            <div class="form-group col-sm-4">
                                <label class="lbl">Room Name</label>
                                <input type="text" class="form-control" id="diningRoomDescription"
                                       name="diningRoomDescription">
                            </div>
                        </div>
                        <div class="row" style="padding-left:4%;">
                            <button onclick="closeaddRooms()" class="btn btn-default btn-xs " type="button">Close
                            </button>
                            <button type="submit" class="btn btn-default btn-xs"><i class="fa fa-check"
                                                                                    aria-hidden="true"></i> Add Room
                            </button>
                        </div>
                    </div>
                </form>
                <table class="<?php echo table_class_pos(1) ?>" id="tbl_rooms" style="font-size:12px;">
                    <thead>
                    <tr class="tHeadStyle">
                        <th>#</th>
                        <th>Room Description</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                </table>


            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade pddLess" data-backdrop="static" id="tables_Modal" role="dialog">
    <div class="modal-dialog" style="min-width: 50%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title"><img src="<?php echo base_url("images/pos/Plates-icon.png") ?>" alt="">Tables
                </h4></div>
            <div class="modal-body" style="min-height: 200px; ">
                <button type="button" id="showaddtables" onclick="showaddTables()" style="margin-bottom: 2px;"
                        class="btn btn-primary btn-xs pull-right"><i class="fa fa-plus" aria-hidden="true"></i> Add
                </button>
                <form role="form" id="tables_form" class="form-group">
                    <div id="addTables" style="background-color: #f4f4f4; display: none;">
                        <div class="row" style="padding-left: 2%;">
                            <input type="hidden" class="form-control" id="tables_edit_hn" name="tables_edit_hn">
                            <input type="hidden" class="form-control" id="diningRoomMasterIDhn"
                                   name="diningRoomMasterIDhn">
                            <div class="form-group col-sm-4">
                                <label class="lbl">Description</label>
                                <input type="text" class="form-control" id="diningTableDescription"
                                       name="diningTableDescription">
                            </div>
                        </div>
                        <div class="row" style="padding-left:14%;">
                            <button onclick="closeaddTables()" class="btn btn-default btn-xs" type="button">Close
                            </button>
                            <button type="submit" class="btn btn-primary btn-xs"><i class="fa fa-floppy-o"
                                                                                    aria-hidden="true"></i> Add Tables
                            </button>
                        </div>
                    </div>
                </form>
                <table class="<?php echo table_class_pos(1) ?>" id="tbl_tables" style="font-size:12px;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Tables Description</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                </table>


            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade pddLess" data-backdrop="static" id="crew_Modal" role="dialog">
    <div class="modal-dialog" style="min-width: 60%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">Crew</h4></div>
            <div class="modal-body" style="min-height: 200px; ">
                <button type="button" id="showaddcrew" onclick="showaddCrew()" style="margin-bottom: 2px;"
                        class="btn btn-primary btn-xs pull-right"><i class="fa fa-plus" aria-hidden="true"></i> Add
                </button>
                <form role="form" id="crew_form" class="form-group">
                    <div id="addCrews" style="background-color: #f4f4f4; display: none;">
                        <div class="row" style="padding-left: 2%;padding-right: 2%;">
                            <input type="hidden" class="form-control" id="crew_edit_hn" name="crew_edit_hn">
                            <input type="hidden" class="form-control" id="wareHouseAutoIDCrewhn"
                                   name="wareHouseAutoIDCrewhn">
                            <div class="form-group col-sm-4">
                                <label class="lbl">Employee ID (Optional)</label>
                                <select onchange="loadFnameLname()" name="EIdNo" id="EIdNo" class="form-control">
                                    <option value="0">Select Employee</option>
                                    <?php foreach ($employee as $employ) { ?>
                                        <option data-fname="<?php echo $employ['Ename1'] ?>"
                                                data-lname="<?php echo $employ['Ename2'] ?>"
                                                value="<?php echo $employ['EIdNo']; ?>"><?php echo $employ['ECode'] ?>
                                            | <?php echo $employ['Ename1'] ?> <?php echo $employ['Ename2'] ?></option>
                                    <?php }; ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="lbl">First Name</label>
                                <input type="text" class="form-control" id="crewFirstName" name="crewFirstName">
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="lbl">Last Name</label>
                                <input type="text" class="form-control" id="crewLastName" name="crewLastName">
                            </div>
                        </div>
                        <div class="row" style="padding-left: 2%;">
                            <div class="form-group col-sm-4">
                                <label class="lbl">Role</label>
                                <select name="crewRoleID" id="crewRoleID" class="form-control">
                                    <option value="">Select Role</option>
                                    <?php foreach ($role as $rol) { ?>
                                        <option
                                            value="<?php echo $rol['crewRoleID']; ?>"><?php echo $rol['roleDescription'] ?></option>
                                    <?php }; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="padding-left:85%;">
                            <button onclick="closeaddCrew()" class="btn btn-default btn-xs" type="button">Close</button>
                            <button type="submit" class="btn btn-primary btn-xs"><i class="fa fa-floppy-o"
                                                                                    aria-hidden="true"></i> Save Crew
                            </button>
                        </div>
                    </div>
                </form>
                <table class="<?php echo table_class_pos(1) ?>" id="tbl_crew" style="font-size:12px;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <!--<th>Employee ID</th>-->
                        <th>Role</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                </table>


            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Tax -->
<div class="modal fade pddLess" data-backdrop="static" id="add_menuTaxModal" tabindex="-2" role="dialog">
    <div class="modal-dialog" style="min-width: 50%; margin-top: 6px;">
        <div class="modal-content">
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">Add Tax </h4></div>
            <div class="modal-body">
                <form id="from_addMenuTax" name="from_addMenuTax" class="form-horizontal" method="post">
                    <input type="hidden" name="menuMasterID" id="tax_menuMasterID" value="0">
                    <div class="form-group">
                        <label for="tax_taxmasterID" class="col-md-3 control-label">
                            Tax Type</label>
                        <div class="col-md-5">
                            <?php //echo form_dropdown('taxmasterID', get_taxType_drop(), '', ' class="form-control" id="tax_taxmasterID" onchange="loadTexPercentage()"') ?>
                            <select name="taxmasterID" class="form-control" id="tax_taxmasterID"
                                    onchange="loadTexPercentage()">
                                <?php
                                $taxes = get_taxType();
                                foreach ($taxes as $tax) {
                                    echo '<option data-p="' . $tax['taxPercentage'] . '" value="' . $tax['taxMasterAutoID'] . '">' . $tax['taxDescription'] . ' (' . $tax['taxShortCode'] . ') -  ' . $tax['taxPercentage'] . '%' . '</option>';
                                }
                                ?>

                            </select>

                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tax_taxPercentage" class="col-md-3 control-label">
                            Tax Percentage</label>
                        <div class="col-md-2">
                            <input type="number" id="tax_taxPercentage" name="taxPercentage" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tax_taxPercentage" class="col-md-3 control-label">
                            Tax Amount</label>
                        <div class="col-md-3">
                            <input type="number" id="tax_taxAmount" name="taxAmount" class="form-control"/>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-md-3 control-label">&nbsp;</label>
                        <div class="col-md-3">
                            <buton class="btn btn-xs btn-primary" type="button" onclick="save_menuTax()">
                                <i class="fa fa-plus"></i> Add Tax
                            </buton>
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


<!-- Add Service Charge  -->
<div class="modal fade pddLess" data-backdrop="static" id="add_serviceCharge" role="dialog">
    <div class="modal-dialog" style="min-width: 50%; margin-top: 6px;">
        <div class="modal-content">
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">Add Service Charge </h4></div>
            <div class="modal-body">
                <form id="from_addServiceCharge" name="from_addServiceCharge" class="form-horizontal" method="post">
                    <input type="hidden" name="menuMasterID" id="sc_menuMasterID" value="0">
                    <div class="form-group">
                        <label for="sc_GLAutoID" class="col-md-3 control-label">
                            GL Code
                        </label>
                        <div class="col-md-8">
                            <?php echo form_dropdown('GLAutoID', getChartOfAccount_serviceCharge(), '', 'class="form-control select2" id="sc_GLAutoID"') ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sc_serviceChargePercentage" class="col-md-3 control-label">
                            Percentage (%)</label>
                        <div class="col-md-2">
                            <input type="number" id="sc_serviceChargePercentage" name="serviceChargePercentage"
                                   class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sc_serviceChargeAmount" class="col-md-3 control-label">
                            Amount</label>
                        <div class="col-md-3">
                            <input type="number" id="sc_serviceChargeAmount" name="serviceChargeAmount"
                                   class="form-control"/>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-md-3 control-label">&nbsp;</label>
                        <div class="col-md-3">
                            <buton class="btn btn-xs btn-primary" type="button" onclick="save_serviceCharge()">
                                <i class="fa fa-plus"></i> Add
                            </buton>
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

<script>
    $('#add_serviceCharge, #add_menuTaxModal').on('hidden.bs.modal', function () {
        modalFix();
    });

    function loadTexPercentage() {
        var tmpText = $("#tax_taxmasterID option:selected").text();
        if ($("#tax_taxmasterID option:selected").val() !== '') {
            var p = $("#tax_taxmasterID option:selected").data('p');
            /*var percentage = tmpText.split('-');
             var tmpPercentage = percentage[1].split('%');
             var setValue = $.trim(tmpPercentage[0]);
             $("#tax_taxPercentage").val(setValue);*/
            $("#tax_taxPercentage").val(p);
        } else {
            $("#tax_taxPercentage").val('');
        }
    }


</script>