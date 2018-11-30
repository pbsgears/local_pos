<?php echo head_page('Promotion Master',false);
$locations = load_pos_location_drop();
$promotions = promotion_policies_drop();
//echo '<pre>';print_r($promotions);echo '<pre>';

?>
<style type="text/css">
    .checkbox-inline.no_indent, .checkbox-inline.no_indent+.checkbox-inline.no_indent {
      margin-left: 0;
      margin-right: 10px;
    }
    .checkbox-inline.no_indent:last-child { margin-right: 0; }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="col-md-5">
    <!--<table class="table table-bordered table-striped table-condensed ">
        <tbody><tr>
            <td>
                <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> Confirmed /Approved
            </td>
            <td>
                <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> Not Confirmed/ Not Approved
            </td>
            <td>
                <span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> Refer-back
            </td>
        </tr>
        </tbody>
    </table>-->
</div>
<div class="col-md-3 pull-right">
    <!--<button type="button" onclick="open_newCounterModel()" class="btn btn-primary btn-sm pull-right" ><i class="fa fa-plus"></i> Create New </button>-->
    <button type="button" onclick="newPromotion()" class="btn btn-primary btn-sm pull-right" ><i class="fa fa-plus"></i> Create New </button>
</div>
</div>
<hr>

<div class="table-responsive">
    <table id="promotionMaster_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="width: 5%">#</th>
            <th style="width: 25%">Description</th>
            <th style="width: 10%">Type</th>
            <th style="width: 10%">From Date</th>
            <th style="width: 10%">End Date</th>
            <!--<th style="width: 20%">Warehouses</th>-->
            <th style="width: 8%">All Items</th>
            <th style="width: 5%">&nbsp;</th>
        </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>


<script type="text/javascript">

    var promotionMaster_table;

    $( document ).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/pos/promotion_master','Test','POS');
        });
        load_promotions();
    });

    function grv_table() {
        var Otable = $('#grv_table').DataTable({
            //"scrollY"           :"500px",
            //"scrollX"           : true,
            //"scrollCollapse"    : true,
            //"paging"            : false,
            //"fixedColumns"      : true,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Grv/fetch_grv'); ?>",
            "aaSorting": [[1, 'desc']],
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
                {"mData": "grvAutoID"},
                {"mData": "grvPrimaryCode"},
                {"mData": "grv_detail"},
                {"mData": "total_value"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
                //{"mData": "edit"},
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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

    function load_promotions() {
        promotionMaster_table = $('#promotionMaster_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos/fetch_promotions'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "promotionID"},
                {"mData": "masterDes"},
                {"mData": "typeDes"},
                {"mData": "dateFrom"},
                {"mData": "dateTo"},
                /*{"mData": "wareHouse"},*/
                {"mData": "applicableItems"},
                {"mData": "action"}
            ],
            // "columnDefs": [{
            //     "targets": [5],
            //     "orderable": false
            // }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function delete_promotions(id, code){
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: { 'promoID': id },
                    url: "<?php echo site_url('Pos/delete_promotion'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if(data[0] == 's'){
                            setTimeout(function(){
                                promotionMaster_table.ajax.reload();
                            }, 300);
                        }
                    }, error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        );
    }

    function newPromotion($pageID=null){
        fetchPage("system/pos/promotion_create", $pageID,"Promotions","Promotions");
    }

</script>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-10-10
 * Time: 12:24 PM
 */