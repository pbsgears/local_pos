<?php
//echo '<pre>';print_r($w_items); echo '</pre>'; die();
?>

<style type="text/css">
    #item_detailTB{
        //width: 99% !important;
    }
    .hideTr{ display: none }

    @media (max-width: 767px) {
        #save_item, #openModal, #selectedCount_label{
            float: left !important;
        }
    }
</style>
<script type="text/javascript"> var selectedItems = []; </script>

<div class="row" style="margin-bottom: 1%">
    <div class="col-sm-3">
        <input type="text" class="form-control" id="searchItem" value="" placeholder="Search">
    </div>
    <div class="col-sm-9" style="">
        <!--<label id="selectedCount_label" class="pull-right">Number of Selected Items : <span id="selectedCount">0</span></label>-->
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openItem_modal()">
            <i class="fa fa-plus"></i> Add
        </button>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <?php echo form_open('','role="form" id="proItem_form" autocomplete="off"'); ?>
        <div class="fixHeader_Div" style="max-width: 100%; height: 253px">
            <table class="<?php echo table_class();?>" id="item_detailTB">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Code</th>
                    <th>Item Description</th>
                    <th><span class="glyphicon glyphicon-trash deleteAllItems" style="color:rgb(209, 91, 71);"></span></th>
                </tr>
            </thead>

            <tbody>
            <?php
            foreach($w_items as $key=>$item){
            $tr_data = $item['itemSystemCode'].''.$item['itemDescription'];
            echo '<tr data-value="'.$tr_data.'">
                    <td align="right">'.($key+1).'</td>
                    <td>'.$item['itemSystemCode'].'</td>
                    <td>'.$item['itemDescription'].'</td>
                    <td align="center">
                        <input type="hidden" class="hiddenValue" name="selectedItems[]" value="'.$item['itemAutoID'].'" />
                        <span class="glyphicon glyphicon-trash deleteItem" style="color:rgb(209, 91, 71);position: static"></span>
                    </td>
                  </tr>';
            echo '<script>selectedItems.push('.$item["itemAutoID"].');</script>';
            }
            ?>
            </tbody>
        </table>
        <?php echo form_close(); ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="" style="margin-top: 1% !important;">
        <div class="col-sm-9" style="margin-top: 1%">
            <label>
                Showing <span id="showingCount"><?php echo count($w_items);?></span> of
                <span id="totalRowCount"><?php echo count($w_items);?></span>  entries
            </label>
        </div>

        <div class="col-sm-3">
            <input type="button" class="btn btn-primary btn-sm pull-right" id="save_item" value="Save Changes">
        </div>
    </div>
</div>

<div class="modal fade" id="item_model" role="dialog" data-keyboard="false" data-backdrop="static"  style="z-index: 999999" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">Items</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" id="isItemsLoad" value="0" >
                <div class="table-responsive">
                    <table id="itemMasterTB" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="width: 5%;min-width: 30px">#</th>
                            <th style="width: 10%;min-width: 100px">Item Code</th>
                            <th style="width: 25%;min-width: 250px">Item Name</th>
                            <th style="width: 25px;min-width: 70px"><input type="checkbox" name="" id="checkAll"/></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-sm" style="" onclick="addItems()">Add</button>
            <button data-dismiss="modal" class="btn btn-default btn-sm" type="button" style="font-size:12px;">Close</button>
        </div>
    </div>
</div>


<script type="text/javascript">
    var item_detailTB = $('#item_detailTB');
    var item_model = $('#item_model');
    var itemMasterTB;

    $(document).ready(function () {
        var title = $('#promoType').find(':selected').text();
        $('#title-label').text(title);

        item_detailTB.tableHeadFixer({
            head: true,
            foot: true,
            left: 1,
            right: 0,
            'z-index': 0
        });
    });

    $('#checkAll').change(function(){
        var itemChk = $('.itemChk');
        itemChk.prop('checked', false);

        if( $(this).prop('checked') == true ){
            itemChk.prop('checked', true);
        }
    });

    function openItem_modal(){
        item_model.modal({backdrop:'static'});
        var isItemsLoad = $('#isItemsLoad');
        if( isItemsLoad.val() == 0 ){
            fetch_allItems();
            isItemsLoad.val(1);
        }
    }

    function fetch_allItems(){
        itemMasterTB = $('#itemMasterTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos/fetch_allItems'); ?>",
            "aaSorting": [[2, 'asc']],
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
                {"mData": "itemAutoID"},
                {"mData": "itemSystemCode"},
                {"mData": "itemDescription"},
                {"mData": "action"}
            ],
            "aoColumnDefs": [ { "bSortable": false, "aTargets": [ 0, 3] } ],
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

    function addItems(){
        var error = '';
        $('.itemChk:checked').each(function(){
            var autoID = parseInt($(this).val());
            var iCode = $(this).attr('data-code');
            var iDescription = $(this).attr('data-description');
            var inArray = $.inArray(autoID, selectedItems);

            if (inArray == -1) {
                var nxtRow = $('#item_detailTB tr:last').find('td:eq(0)').html();
                nxtRow = ( $.isNumeric(nxtRow) ) ? nxtRow : 0;
                nxtRow = parseInt(nxtRow)+ 1;

                var empDet = '<tr data-value="'+iCode+' '+iDescription+'">';
                empDet += '<td align="right">'+nxtRow+'</td>';
                empDet += '<td>'+iCode+'</td>';
                empDet += '<td>'+iDescription+'</td>';
                empDet += '<td align="center">';
                empDet += '<span class="glyphicon glyphicon-trash deleteItem" style="color:rgb(209, 91, 71);position: static"></span>';
                empDet += '<input type="hidden" class="hiddenValue" name="selectedItems[]" value="'+autoID+'" />';
                empDet += '</td></tr>';

                item_detailTB.append(empDet);
                selectedItems.push(autoID);
            } else{
                error += '<p>[ '+iCode+' - '+iDescription+' ] is already exist.</p>';
            }
        });

        if( error != '' ){
            myAlert('e', error);
        }

        countTotalRow();
        $('#searchItem').keyup();
    }

    $('#searchItem').keyup(function(){
        var searchKey = $.trim($(this).val()).toLowerCase();
        var tableTR = $('#item_detailTB tbody>tr');
        var row = 0;

        tableTR.removeClass('hideTr');

        tableTR.each(function(){
            var dataValue = ''+$(this).attr('data-value')+'';
            dataValue = dataValue.toLocaleLowerCase();

            if(dataValue.indexOf(''+searchKey+'') == -1){
                $(this).addClass('hideTr');
            }
            else{ row++; }

            $('#showingCount').text(row);
        });
    });

    $('body').on('click', '.deleteItem', function(){
        var parentTR = $(this).closest('tr');
        var thisVal = parseInt(parentTR.find('td:eq(3) .hiddenValue').val());
        var j = selectedItems.indexOf(thisVal);

        if (j != -1) { selectedItems.splice(j, 1); }
        parentTR.remove();

        applyRowNumbers();
        countTotalRow();
        $('#searchItem').keyup();
    });

    $('.deleteAllItems').click(function(){
        item_detailTB.find("tr:not(:first)").remove();
        selectedItems = [];

        $('#showingCount').text(0);
        $('#totalRowCount').text(0);

    });

    function applyRowNumbers(){
        $('#item_detailTB tbody>tr').each(function(i){
           $(this).find('td:eq(0)').html( i+1 );
        });
    }

    function countTotalRow(){
        $('#totalRowCount').text( $('#item_detailTB tbody>tr').length );
    }

    $('#save_item').click(function(){
        var postData = $('#proItem_form').serializeArray();
        var updateID = $('#updateID').val();

        postData.push({'name' : 'promoID', 'value' : updateID });

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: '<?php echo site_url('Pos/save_promotionItems'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    });
</script>











<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-10-17
 * Time: 2:00 PM
 */