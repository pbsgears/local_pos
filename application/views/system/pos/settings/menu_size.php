<link rel="stylesheet" href="<?php echo base_url('plugins/iCheck/minimal/_all.css') ?>">
<script src="<?php echo base_url('plugins/iCheck/icheck.min.js') ?>"></script>
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div class="box box-warning">
    <div class="box-body">
        <div class="nav-tabs-custom">

            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="posContainer">

                        <div style="font-size:16px; font-weight: 800; text-align: center">
                            <?php echo $this->lang->line('pos_config_menu_size_master');?><!--Menu Size Master-->
                            for <?php echo current_companyCode() ?>
                            <br>
                            <span>
                            <span class="btn btn-link" onclick="openAddMenuSizeModal()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('pos_config_menu_size');?><!--Menu Size--></span>
                        </span>
                        </div>


                        <table class="<?php echo table_class_pos(1) ?>" id="tbl_menuSize"
                               style="font-size:12px;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                                <th><?php echo $this->lang->line('pos_config_size_codee');?><!--Size Code--></th>
                                <th><?php echo $this->lang->line('pos_config_size_colour');?><!--Size Colour--></th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
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


<script>
    $(document).ready(function (e) {
        fetchMenuSize();
    });

    function openAddMenuSizeModal() {
        $('#menuSizeID').val('');
        $('#fromMenuSize')[0].reset();
        $('#fromMenuSize').bootstrapValidator('resetForm', true);
        $('#menuSizeHead').html('<?php echo $this->lang->line('pos_config_add_menu_size');?>');/*Add Menu Size*/
        $("#add_menuSizeModal").modal('show');
    }

    function refreshMenuSize() {
        fetchPage('system/pos/settings/menu_size','','Menu Size Master');
    }


    function addMenuSize() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/saveMenuSize') ?>",
            data: $("#fromMenuSize").serialize(),
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    $("#add_menuSizeModal").modal('hide');
                    fetchMenuSize();
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


    function delete_menuSize(id) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('Pos_config/delete_menuSize') ?>",
                    data: {id: id},
                    dataType: "json",
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 0) {
                            fetchMenuSize();
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

    function fetchMenuSize() {
        $('#tbl_menuSize').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/fetch_menu_size'); ?>",
            "aaSorting": [[0, 'desc']],
            "aoColumnDefs": [{"bSortable": false, "aTargets": [3, 4]}],
            /*"language": {
             processing: '<i class="fa fa-refresh fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
             },*/
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var selectedRowID = parseInt('<?php echo (!empty($this->input->post('page_id'))) ? $this->input->post('page_id') : 0; ?>');

                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if (parseInt(oSettings.aoData[x]._aData['EIdNo']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "menuSizeID"},
                {"mData": "description"},
                {"mData": "code"},
                {"mData": "menuSizeColor"},
                {"mData": "action"}
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

    function openEddMenuSizeModal(id){
        $('#fromMenuSize').bootstrapValidator('resetForm', true);
        $("#add_menuSizeModal").modal("show");
        $('#menuSizeID').val(id);
        $('#menuSizeHead').html('<?php echo $this->lang->line('pos_config_edit_menu_size');?>');/*Edit Menu Size*/
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {menuSizeID: id},
            url: "<?php echo site_url('Pos_config/edit_menu_size'); ?>",
            success: function (data) {
                $('#mSize_description').val(data['description']);
                $('#mSize_code').val(data['code']);
                $('#mSize_colourCode').val(data['colourCode']);
                if(data['isActive']==1){
                    $('#mSize_isActive').attr('checked',true);
                }else if(data['isActive']==0){
                    $('#mSize_isActive').attr('checked',false);
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
    }

</script>


<div class="modal fade pddLess" data-backdrop="static" id="add_menuSizeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="min-width: 50%; margin-top: 6px;">
        <div class="modal-content">
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title" id="menuSizeHead"></h4></div>
            <div class="modal-body" id="" style="min-height: 200px; background-color: rgba(0, 0, 0, 0.04);">
                <form id="fromMenuSize" name="fromMenuSize" class="form-horizontal" method="post">

                    <input type="hidden" value="0" name="menuSizeID" id="menuSizeID">

                    <div class="form-group">
                        <label for="mSize_description" class="col-md-3 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                        <div class="col-md-6">
                            <input type="text" maxlength="100" class="form-control" id="mSize_description"
                                   name="description" placeholder="eg: Large / Small / etc..">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="mSize_code" class="col-md-3 control-label"><?php echo $this->lang->line('pos_config_size_codee');?><!--Size Code--> </label>
                        <div class="col-md-2">
                            <input type="text" maxlength="3" class="form-control" id="mSize_code"
                                   name="code" placeholder="eg: S / L / M">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="mSize_colourCode" class="col-md-3 control-label"><?php echo $this->lang->line('pos_config_colour_code');?><!--Colour Code--> </label>
                        <div class="col-md-4">
                            <input type="color" name="colourCode" id="mSize_colourCode" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="mSize_colourCode" class="col-md-3 control-label"><?php echo $this->lang->line('pos_config_is_active');?><!--is Active--></label>
                        <div class="col-md-4">

                            <label>
                                <input type="checkbox" name="isActive" checked id="mSize_isActive" value="1" class="minimal-red" checked>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">&nbsp;</label>
                        <div class="col-md-4">
                            <button class="btn btn-sm btn-primary" type="button" onclick="addMenuSize();"><?php echo $this->lang->line('common_save');?><!--Save-->
                            </button>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer" style="padding: 5px 10px;">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade pddLess" data-backdrop="static" id="add_menuSizeModal_setup" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="min-width: 50%; margin-top: 6px;">
        <div class="modal-content">
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">Add Menu Category </h4></div>
            <div class="modal-body" id="" style="min-height: 150px; background-color: rgba(0, 0, 0, 0.04);">
                <form id="fromMenuSize_addMenuSetup" name="fromMenuSize" class="form-horizontal"
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

<div class="modal fade pddLess" data-backdrop="static" id="add_menuModal_setup" tabindex="-1" role="dialog">
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
                        <label for="ms_menuMasterID" class="col-md-3 control-label">Category
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

<div class="modal fade pddLess" data-backdrop="static" id="tables_Modal" tabindex="-1" role="dialog">
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

<div class="modal fade pddLess" data-backdrop="static" id="crew_Modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="min-width: 60%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">Crew</h4>
            </div>
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
                                <label class="lbl"> Employee ID (Optional)</label>
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
                            <button type="submit" id="crewSaveBtn" class="btn btn-primary btn-xs"><i
                                    class="fa fa-floppy-o"
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