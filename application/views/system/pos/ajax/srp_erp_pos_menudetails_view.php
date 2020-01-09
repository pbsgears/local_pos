<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('pos_config_menu_details');
echo head_page($title, false);
$item = load_item_drop();
?>
    <hr style="margin:2px 0px;">
    <div>
        <button class="btn btn-link" onclick="backToMenu();" rel="tooltip" title="Go Back">
            <i class="fa fa-arrow-left fa-3x" aria-hidden="true"></i> <!--go Back--></button>
        |
        <img src="<?php echo $category['menuImage']; ?>" class="img-rounded" alt="no Image" height="100">
        <?php //print_r($category); ?>
        <span style="font-size: 20px"><?php echo $category['menuMasterDescription']; ?></span>
        |
        <button onclick="openMenuDetailModel()" class="btn btn-xs btn-default" type="button"><i class="fa fa-plus"
                                                                                                aria-hidden="true"></i>
            <?php echo $this->lang->line('pos_config_add_menu_detail'); ?><!--Add Menu Detail-->
        </button>
    </div>
    <hr style="margin:2px 0px;">
    <!--<div id="filter-panel" class="collapse filter-panel"></div>
    <div class="col-md-3 pull-right">
        <button type="button"  class="btn btn-primary btn-sm pull-right" ><i class="fa fa-plus"></i> Create New </button>
    </div>
    </div>-->
<?php
$defcurrency = $this->common_data['company_data']['company_default_currency'];

?>
    <div class="table-responsive">
        <table id="pos_menu_detail" class="<?php echo table_class_pos(); ?>">
            <thead>
            <tr>
                <th style="width:10px; ">#</th>
                <th style="width:30%;">
                    <?php echo $this->lang->line('pos_config_menu_detail_description'); ?><!--Menu Detail Description--></th>
                <th style="width:30%;"> Item</th>
                <th style="width:10%;"> Item Code</th>
                <th style="width:39px;"><?php echo $this->lang->line('pos_config_qty'); ?><!--Qty--></th>
                <th style="width:39px; "><?php echo $this->lang->line('pos_config_uom'); ?><!--UOM--></th>
                <th style="width:39px; ">
                    <?php echo $this->lang->line('pos_config_Cost'); ?><!--Cost-->
                    <span style="font-size: 11px;">(<?php echo $defcurrency ?>)</span>
                </th>

                <th style="width:39px; ">&nbsp;</th>
            </tr>
            </thead>
            <tbody id="pos_menu_detail_table_body">
            </tbody>
            <tfoot id="pos_menu_detail_table_foot">

            </tfoot>
        </table>
    </div>


    <div aria-hidden="true" role="dialog" tabindex="-1" id="menuDetail_model" class="modal fade" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header" style="padding: 6px 20px;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <?php echo $this->lang->line('pos_config_add_menu_detail'); ?><!--Add Menu Detail--> </h4>
                </div>
                <form role="form" id="menuDetail_form" class="form-group">
                    <input type="hidden" id="menuDetailID_hn" name="menuDetailID_hn">
                    <input type="hidden" id="is_cost_changed" name="is_cost_changed">
                    <input type="hidden" id="default_cost" name="default_cost">
                    <input type="hidden" id="tmp_cost" name="tmp_cost">
                    <input type="hidden" id="edit_cost_hn" name="edit_cost_hn">
                    <input type="hidden" id="pageid" name="pageid"
                           value="<?php if (isset($_POST["categoryID"])) echo $_POST["categoryID"]; ?>">
                    <input type="hidden" id="frm_uomID" name="uomID"/>

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="lbl">
                                    <?php echo $this->lang->line('pos_config_menu_detail_description'); ?><!--Menu Detail Description--></label>
                                <input type="text" class="form-control" id="menuDetailDescription"
                                       name="menuDetailDescription">
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="lbl"><?php echo $this->lang->line('common_item'); ?><!--Item--></label>
                                <select name="itemAutoID" id="itemAutoID" class="form-control"
                                        onchange="load_default_uom_and_cost(this)">
                                    <option value="">
                                        <?php echo $this->lang->line('pos_config_select_item'); ?><!--Select Item--></option>
                                    <?php foreach ($item as $ite) { ?>
                                        <option
                                            value="<?php echo $ite['itemAutoID']; ?>"><?php echo $ite['itemSystemCode'] ?>
                                            | <?php echo $ite['itemDescription'] ?></option>
                                    <?php }; ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>
                                    <?php echo $this->lang->line('pos_config_select_uom'); ?><!--<?php echo $this->lang->line('pos_config_uom'); ?>--></label>
                                <select name="UOM" id="UOM" class="form-control">
                                    <option value="">
                                        <?php echo $this->lang->line('pos_config_select_uom'); ?><!--Select UOM--></option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="lbl"><?php echo $this->lang->line('pos_config_qty'); ?><!--QTY--></label>
                                <input type="number" step="any" onkeyup="updatetotalCost()" class="form-control"
                                       id="qty_addMenuDetail" name="qty"
                                       value="1">
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="lbl">
                                    <?php echo $this->lang->line('pos_config_unit_cost'); ?><!--Unit Cost--></label>
                                <input type="number" readonly step="any" onchange="update_flaag()" class="form-control"
                                       id="cost" name="cost">
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="lbl">
                                    <?php echo $this->lang->line('pos_config_total_cost'); ?><!--Total Cost--></label>
                                <input type="number" step="any" class="form-control" id="totalCost"
                                       onkeyup="unitCost()">
                            </div>
                        </div>


                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">
                                <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                            <button type="button" class="btn btn-primary btn-sm" onclick="save_add_menuDetail();">
                                <?php echo $this->lang->line('common_save'); ?><!--Save-->
                            </button>
                        </div>
                </form>
            </div>
        </div>
    </div>


    <script type="text/javascript">

        function calculateTotal() {
            var qty = $("#qty_addMenuDetail").val();
            var cost = $("#cost").val();
            if (cost > 0 && qty > 0) {
                var totalCost = qty * cost;
                $("#totalCost").val(totalCost);
            } else if (cost == 0 || qty == 0) {
                $("#totalCost").val(0);
            }
        }

        function unitCost() {
            var total = $("#totalCost").val();
            var qty = $("#qty_addMenuDetail").val();
            if (total > 0 && qty > 0) {
                var unitCost = total / qty;
                $("#cost").val(unitCost);
            } else if (total == 0 || qty == 0) {
                $("#cost").val(0);
            }
        }

        function save_add_menuDetail() {
            var data = $("#menuDetail_form").serializeArray();
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Pos_config/save_menu_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data.error == 1) {
                        myAlert('e', data['message']);
                    } else if (data.error == 0) {
                        myAlert('s', data['message']);
                        $("#container_config_status").val(1);
                        $("#menuDetail_model").modal("hide");
                        loadMenuDetail_table();
                    }
                    console.log(data);


                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

        $(document).ready(function () {
            $('#menuDetail_model').on('show.bs.modal', function (e) {
                $(this).removeAttr('tabindex');
            })
            $("#itemAutoID").select2();
            calculateTotal();
            $("#UOM,#itemAutoID").change(function (e) {
                setTimeout(function (e) {
                    calculateTotal();
                    var cost = $("#cost").val();
                    $("#tmp_cost").val(cost);


                }, 1000)
            });
            $("#UOM").change(function (e) {
                var unitID = $(this).find(':selected').data('unitid');
                $("#frm_uomID").val(unitID);
            });
            $("#qty,#cost").keyup(function (e) {
                calculateTotal();
            });

            loadMenuDetail_table();



        });

        function loadMenuDetail_table() {
            $menuMasterID = $('#pageid').val();
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Pos_config/loadMenuDetail_table"); ?>',
                dataType: 'json',
                data: {'menuMasterID': $menuMasterID},
                async: false,
                success: function (data) {
                    $('#pos_menu_detail_table_body').empty();
                    $('#pos_menu_detail_table_foot').empty();
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $('#pos_menu_detail_table_body').append('<tr class="danger"><td colspan="7" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                        <!--No Records Found-->
                    } else {
                        var i = 1;
                        var cost = 0;
                        var actualInventoryCost = 0;
                        var loccurrency = '';
                        var costs = 0;
                        $.each(data['detail'], function (key, value) {
                            costs = parseFloat(value['cost']);
                            tmpActualInventoryCost = parseFloat(value['actualInventoryCost']);
                            actualInventoryCost = parseFloat(value['actualInventoryCost']);
                            /*<td>' + value['menuMasterID'] + '</td>*/
                            /*<td style="text-align: right;">' + tmpActualInventoryCost.toFixed(2) + '</td>*/
                            $('#pos_menu_detail_table_body').append('<tr><td>' + i + '</td><td>' + value['menuDetailDescription'] + '</td><td>' + value['itemDescription'] + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['qty'] + '</td><td>' + value['UOM'] + '</td><td style="text-align: right;">' + costs.toFixed(2) + '</td><td class="text-right"><a class="btn btn-default btn-xs" onclick="editMenuDetail(' + value['menuDetailID'] + ')"><i class="fa fa-edit"></i></a> | <a class="btn btn-default btn-danger btn-xs" onclick="deleteMenuDetail(' + value['menuDetailID'] + ',' + value['cost'] + ',' + value['menuMasterID'] + ')"><i style="color: white;" class="fa fa-trash"></i></a></td></tr>');
                            i++;
                            cost += parseFloat(value['cost']);
                            loccurrency = (value['company_default_currency'])
                        });
                        $('#pos_menu_detail_table_foot').append('<tr><td colspan="6" style="font-weight: bold; text-align: right;"><?php echo $this->lang->line('common_total');?>:</td><td style="text-align: right;">' + cost.toFixed(2) + '</td><td>&nbsp;</td></tr>');
                        <!--Total-->
                        /*<td style="text-align: right;">' + actualInventoryCost.toFixed(2) + '</td>*/
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        }

        function openMenuDetailModel() {
            $("#menuDetail_model").modal({backdrop: "static"});
            $('#menuDetail_form')[0].reset();
            $('#menuDetailID_hn').val('');
            $('#edit_cost_hn').val('');
            $('#is_cost_changed').val('');
            $('#menuDetail_form').bootstrapValidator('resetForm', true);
            $("#itemAutoID").val("").change();
            $("#UOM").html('<option value="">Select UOM</option>');
        }


        function load_default_uom_and_cost(tmpThis) {

            var selectedDesc = $(tmpThis).find(':selected').text();
            var textTmpValue = selectedDesc.split('|')[1].trim()

            var itemAutoID = $('#itemAutoID').val();
            if (itemAutoID > 0) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("Pos_config/load_default_uom"); ?>',
                    dataType: 'json',
                    data: {'itemAutoID': itemAutoID},
                    async: false,
                    success: function (data) {
                        $('#cost').val(data['companyLocalWacAmount']);
                        $('#default_cost').val(data['companyLocalWacAmount']);
                        $('#tmp_cost').val(data['companyLocalWacAmount']);
                        $('#qty').val(1);
                        load_default_uom(data['defaultUnitOfMeasure']);
                        $("#menuDetailDescription").val(textTmpValue);
                    },
                    error: function (xhr, ajaxOptions, thrownError) {

                    }
                });
            }

        }


        function load_default_uom(short_code) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'short_code': short_code},
                url: "<?php echo site_url('dashboard/fetch_related_uom'); ?>",
                success: function (data) {
                    $('#UOM').empty();
                    var mySelect = $('#UOM');
                    mySelect.append($('<option></option>').val('').html('Select UOM'));
                    $.each(data, function (val, text) {
                        if (short_code == text['UnitShortCode']) {
                            $("#frm_uomID").val(text['UnitID']);
                            mySelect.append($('<option data-unitid="' + text['UnitID'] + '" data-conversition="' + text['conversion'] + '" selected></option>').val(text['UnitShortCode']).html(text['UnitDes']));
                        }
                        else {
                            mySelect.append($('<option data-unitid="' + text['UnitID'] + '" data-conversition="' + text['conversion'] + '"></option>').val(text['UnitShortCode']).html(text['UnitDes']));
                        }

                    });

                }, error: function () {
                    myAlert('e', 'Error in UMO fetching.')
                }
            });
        }


        function editMenuDetail(id) {
            var menuDetailID = $('#menuDetailID_hn').val(id);
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Pos_config/load_menu_detail_edit"); ?>',
                dataType: 'json',
                data: {'menuDetailID': id},
                success: function (data) {

                    $('#edit_cost_hn').val(data['cost']);
                    //$('#UOM').val(data['UOM']);
                    $("#totalCost").val(data['cost']);
                    $('#qty_addMenuDetail').val(data['qty']);
                    $('#itemAutoID').val(data['itemAutoID']).change();
                    $('#menuDetailDescription').val(data['menuDetailDescription']);
                    $('#pageid').val(data['menuMasterID']);
                    $("#menuDetail_model").modal("show");
                    load_default_uom(data['UOM'])
                    $('#cost').val(parseFloat(data['cost']) / parseFloat(data['qty']));
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        }

        function update_flaag() {
            $('#is_cost_changed').val('1');
        }

        $('#UOM').change(function () {
            if ($('#is_cost_changed').val() == "") {
                var conversion = getNumberAndValidate($(this).find('option:selected').attr('data-conversition'));
                var defaultStk = $('#default_cost').val();
                var newconversion = (1 / conversion);
                currentStock.val(defaultStk * newconversion);
            }
        });

        function getNumberAndValidate(thisVal, dPlace = 2) {
            thisVal = $.trim(thisVal);
            thisVal = removeCommaSeparateNumber(thisVal);
            thisVal = thisVal.toFixed(dPlace);
            if ($.isNumeric(thisVal)) {
                return parseFloat(thisVal);
            }
            else {
                return parseFloat(0);
            }
        }

        var currentStock = $('#cost');

        function deleteMenuDetail(id, cost, menuMaster) {
            bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
                if (result) {
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo site_url("Pos_config/delete_pos_menu_detail"); ?>',
                        dataType: 'json',
                        data: {'menuDetailID': id, cost: cost, menuMasterID: menuMaster},
                        success: function (data) {
                            myAlert('s', data['message']);
                            loadMenuDetail_table();
                        },
                        error: function (xhr, ajaxOptions, thrownError) {

                        }
                    });
                }
            });
        }

        function updatetotalCost() {
            var total = $("#cost").val();
            var qty = $("#qty_addMenuDetail").val();
            if (total > 0 && qty > 0) {
                var unitCost = total * qty;
                $("#totalCost").val(unitCost);
            } else if (total == 0 || qty == 0) {
                $("#totalCost").val(0);
            }
        }


    </script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-10-04
 * Time: 2:31 PM
 */