<?php echo head_page('Menu Details',false);
$item=load_item_drop();
?>
<div id="filter-panel" class="collapse filter-panel"></div>

<div class="col-md-3 pull-right">
    <button type="button" onclick="openMenuDetailModel()" class="btn btn-primary btn-sm pull-right" ><i class="fa fa-plus"></i> Create New </button>
</div>
</div>
<hr>
<input type="hidden" class="form-control" id="pageid" name="pageid" value="<?php if (isset($_POST["page_id"])) echo $_POST["page_id"]; ?>">
<div class="table-responsive">
    <table id="pos_menu_detail" class="<?php echo table_class_pos();?>" >
        <thead>
        <tr>
            <th style="width:39px; ">#</th>
            <th style="width:39px;">Menu Detail Description</th>
            <th style="width:39px; ">Menu Master</th>
            <th style="width:39px;">Qty</th>
            <th style="width:39px; ">UOM</th>
            <th style="width:39px; ">Cost</th>
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
        <div class="modal-dialog" >
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Add Menu Detail</h3>
                </div>
                <form role="form" id="menuDetail_form" class="form-group">
                    <input type="hidden" class="form-control" id="menuDetailID_hn" name="menuDetailID_hn">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="lbl">Menu Detail Description</label>
                                <input type="text" class="form-control" id="menuDetailDescription" name="menuDetailDescription">
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="lbl">Item</label>
                                <select name="itemAutoID" id="itemAutoID" class="form-control" onchange="load_default_uom_and_cost()" >
                                    <option value="">Select Item</option>
                                    <?php foreach ($item as $ite) { ?>
                                        <option value="<?php echo $ite['itemAutoID']; ?>"><?php echo $ite['itemSystemCode'] ?> | <?php echo $ite['itemDescription'] ?></option>
                                    <?php }; ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>UOM</label>
                                <select name="UOM" id="UOM" class="form-control">
                                    <option value="">Select UOM</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="lbl">QTY</label>
                                <input type="number" step="any" class="form-control" id="qty" name="qty">
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="lbl">Cost</label>
                                <input type="number" step="any"  class="form-control" id="cost" name="cost">
                            </div>
                        </div>


                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                        </div>
                </form>
            </div>
        </div>
    </div>


<script type="text/javascript">
    $( document ).ready(function() {
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
                    $('#pos_menu_detail_table_body').append('<tr class="danger"><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>');
                } else {
                    var i = 1;
                    var cost=0;
                    $.each(data['detail'], function (key, value) {
                        $('#pos_menu_detail_table_body').append('<tr><td>' + i + '</td><td>' + value['menuDetailDescription'] + '</td><td>' + value['menuMasterID'] + '</td><td>' + value['qty'] + '</td><td>' + value['UOM'] + '</td><td>' + value['cost'] + '</td><td class="text-right"><a onclick="editMenuDetail(' + value['menuDetailID'] + ')"><span class="glyphicon glyphicon-pencil" style="color:blue;"  rel="tooltip"></span></a></td></tr>');
                        i++;
                        cost += parseFloat(value['cost'] );
                    });
                    $('#pos_menu_detail_table_foot').append('<tr><td colspan="5">Total:</td><td>' + cost + '</td><td>&nbsp;</td></tr>');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }



    function openMenuDetailModel(){
        $("#menuDetail_model").modal({backdrop: "static"});
        $('#menuDetail_form')[0].reset();
        $("#itemAutoID").val("").change();
        $('#menuDetail_form').bootstrapValidator('resetForm', true);
    }

    function load_default_uom_and_cost(){
        var itemAutoID = $('#itemAutoID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Pos_config/load_default_uom"); ?>',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID},
            async: false,
            success: function (data) {
                $('#cost').val(data['companyLocalWacAmount'])
                load_default_uom(data['defaultUnitOfMeasure']);
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
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
                    if(short_code == text['UnitShortCode']){
                        mySelect.append($('<option selected></option>').val(text['UnitShortCode']).html(text['UnitDes']));
                    }
                    else{
                        mySelect.append($('<option></option>').val(text['UnitShortCode']).html(text['UnitDes']));
                    }

                });

            }, error: function () {
                myAlert('e', 'Error in UMO fetching.')
            }
        });
    }



</script>





<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-10-04
 * Time: 2:31 PM
 */