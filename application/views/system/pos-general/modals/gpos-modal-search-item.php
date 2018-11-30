<div aria-hidden="true" role="dialog" tabindex="-1" id="modal_search_item" data-keyboard="true" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-close text-red"></i>
                </button>
                <h4 class="modal-title"><i class="fa fa-search"></i> Search Item </h4>
                keyword (Ctrl+F) &nbsp;&nbsp;<input type="text" id="searchKeyword" onkeyup="searchByKeyword()"/> <span
                    id="loader_itemSearch"
                    style="display: none;"><i
                        class="fa fa-refresh fa-spin"></i></span>
            </div>
            <div class="modal-body" style="min-height: 300px;">
                <div id="result_output_searchItem"></div>
                <div>
                    <table class="table table-bordered table-hover" id="posg_search_item_modal">
                        <thead>
                        <tr>
                            <th class="hidden">Image</th>
                            <th>Item Code</th>
                            <th>Barcode <i class="fa fa-barcode" aria-hidden="true"></i></th>
                            <th>Description</th>
                            <th>current stock</th>
                            <th>UOM</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="itemSearchResultTblBody">
                        <?php
                        $imgPath = base_url() . 'images/item/';
                        foreach ($items as $key => $rowItem) {
                            echo '<tr>
                                     <td class="hidden"><img src="' . $imgPath . '' . $rowItem['itemImage'] . '" style="max-width: 30px; max-height: 30px;" /></td>
                                     <td>' . $rowItem['itemSystemCode'] . '</td>
                                     <td>' . $rowItem['itemDescription'] . '</td>
                                     <td>' . round($rowItem['currentStock'], 2) . '</td>
                                     <td>' . $rowItem['defaultUnitOfMeasure'] . '</td>
                                     <td>
                                        <button type="button" onclick="loadToInvoiceList(\'' . $rowItem['barcode'] . '\')" class="btn btn-xs btn-default">
                                        <i class="fa fa - plus"></i> Add </button>
                                     </td>
                                  </tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" data-dismiss="modal" class="btn btn-block btn-danger btn-flat">
                    <i class="fa fa-angle-double-left" aria-hidden="true"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-2" id="modal_qty_box" data-keyboard="true" class="modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-close text-red"></i>
                </button>
                <h4 class="modal-title"> Qty </h4>
            </div>
            <div class="modal-body">
                <input type="number" id="tmp_qty_modal" class="form-control">
            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" data-dismiss="modal" class="btn btn-block btn-danger btn-flat">
                    <i class="fa fa-angle-double-left" aria-hidden="true"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    var systemCode = '';
    var tmpQty_modal  = 0;
    function searchItem_modal() {
        pos_form[0].reset();
        pos_form.bootstrapValidator('resetForm', true);
        $('#is-edit').val('');
        $('#searchKeyword').val('');
        searchByKeyword()
        $("#modal_search_item").modal('show');
        setTimeout(function () {
            $("#searchKeyword").focus();
        }, 500);
    }

    function searchByKeyword(initialSearch=null, e=null) {
        e = e || window.event;
        if (e.keyCode != '38' && e.keyCode != '40' && e.keyCode != '13') {
            // up arrow
            $('#is-edit').val('');
            var imgPath = '<?php echo base_url(); ?>images/item/';

            pos_form.bootstrapValidator('resetForm', true);

            var keyword = (initialSearch == null) ? $("#searchKeyword").val() : '-';
            var urlReq = (initialSearch == null) ? "<?php echo site_url('Pos/item_search?q='); ?>" + keyword : "<?php echo site_url('Pos/item_initialSearch'); ?>";
            if (keyword.trim() != '') {
                $.ajax({
                    async: true,
                    type: 'get',
                    dataType: 'json',
                    url: urlReq,
                    beforeSend: function () {
                        $("#itemSearchResultTblBody").html('');
                        $("#loader_itemSearch").show();
                        //startLoad();
                    },
                    success: function (data) {
                        $("#loader_itemSearch").hide();
                        $("#itemSearchResultTblBody").html('');
                        if (data == null || data == '') {

                        } else {

                            $.each(data, function (i, v) {
                                var num = parseInt(v.currentStock);
                                var tr_data = '<tr><td class="hidden"><img src="' + imgPath + v.itemImage + '" style="max-width: 30px; max-height: 30px;" /></td> <td>' + v.itemSystemCode + '</td><td>' + v.barcode + '</td> <td>' + v.itemDescription + '</td> <td>' + num.toFixed(2) + '</td> <td>' + v.defaultUnitOfMeasure + '</td><td><button type="button" onclick="loadToInvoiceList(\'' + v.barcode + '\')" class="btn btn-xs btn-default"><i class="fa fa-plus"></i> Add </button></td></tr>';
                                $("#itemSearchResultTblBody").append(tr_data);
                            });
                        }

                    }, error: function () {
                        $("#loader_itemSearch").hide();
                        myAlert('e', 'Error while loading')
                    }
                });

            } else {
                //$("#itemSearchResultTblBody").html('');
                $.ajax({
                    async: true,
                    type: 'get',
                    dataType: 'json',
                    url: '<?php echo site_url("Pos/itemLoadDefault"); ?>',
                    beforeSend: function () {
                        $("#itemSearchResultTblBody").html('');
                        $("#loader_itemSearch").show();
                        //startLoad();
                    },
                    success: function (data) {
                        $("#loader_itemSearch").hide();
                        $("#itemSearchResultTblBody").html('');
                        if (data == null || data == '') {

                        } else {

                            $.each(data, function (i, v) {

                                var tr_data = '<tr><td class="hidden"><img src="' + imgPath + v.itemImage + '" style="max-width: 30px; max-height: 30px;" /></td> <td>' + v.itemSystemCode + '</td><td>' + v.barcode + '</td> <td>' + v.itemDescription + '</td> <td>' + v.currentStock + '</td> <td>' + v.defaultUnitOfMeasure + '</td><td><button type="button" onclick="loadToInvoiceList(\'' + v.barcode + '\')" class="btn btn-xs btn-default"><i class="fa fa-plus"></i> Add </button></td></tr>';
                                $("#itemSearchResultTblBody").append(tr_data);
                            });
                        }

                    }, error: function () {
                        $("#loader_itemSearch").hide();
                        myAlert('e', 'Error while loading')
                    }
                });
            }
        }


    }

    function loadToInvoiceList(tmpSystemCode) {
        systemCode = tmpSystemCode;
        $("#modal_qty_box").modal('show');
        $("#tmp_qty_modal").val(1);
        $("#tmp_qty_modal").select();
    }

    function processInvoiceList(){
        $("#modal_qty_box").modal('hide');
        $("#pos-add-btn").prop('disabled', false);
        item_search_loadToInvoice(systemCode);
    }
</script>