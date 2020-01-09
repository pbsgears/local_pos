<style>
    .datepicker-days table thead th, td {
        border-radius: 0px !important;

    }
</style>
<div aria-hidden="true" role="dialog" tabindex="-1" id="gpos_Itemized_sales_report" class="modal fade"
     data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"> Item Wise Sales Report </h4>
            </div>
            <div class="modal-body" style="min-height: 400px;">
                <form id="frm_itemizedSalesReport" method="post" class="form-inline" role="form">
                    <label for="" class="col-sm-2">Filters </label>

                    <div class="form-group">
                        <label class="sr-only" for="">From</label>
                        From

                        <input type="text" required class="form-control input-sm" data-date-end-date="0d"
                               name="filterFrom" id="filterFrom2" value="<?php echo date('d/m/Y') ?>"
                               style="width: 79px;">
                        to
                        <input type="text" required class="form-control input-sm" data-date-end-date="0d" value="<?php echo date('d/m/Y') ?>"
                               style="width: 79px;" placeholder="To"  name="filterTo" id="filterTo2" >
                    </div>

                    <button type="button" onclick="loadPaymentItemized_salesReport()" class="btn btn-primary btn-sm">Generate Report</button>
                </form>
                <hr>
                <div id="pos_modalBody_posItemized_sales_report">

                </div>

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function (e) {
        $("#filterFrom2,#filterTo2").datepicker({
            format: 'dd/mm/yyyy'
        });

       /* $("#frm_itemizedSalesReport").submit(function (e) {
            loadPaymentItemized_salesReport_ajax();
            return false;
        })*/
    });

    function loadPaymentItemized_salesReport() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_dashboard/loadGeneralItemizedSalesReport'); ?>",
            data: $("#frm_itemizedSalesReport").serialize(),
            cache: false,
            beforeSend: function () {
                $("#gpos_Itemized_sales_report").modal('show');
                startLoadPos();
                $("#pos_modalBody_posItemized_sales_report").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  Loading Print view</div>');
            },
            success: function (data) {
                stopLoad();
                $("#pos_modalBody_posItemized_sales_report").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    /**function loadPaymentItemized_salesReport_ajax() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/loadItemizedSalesReport'); ?>",
            data: $("#frm_itemizedSalesReport").serialize(),
            cache: false,
            beforeSend: function () {
                $("#pos_modalBody_posItemized_sales_report").html('<div style="text-align: center;"> <i class="fa fa-refresh fa-spin fa-2x"></i>  Loading Print view</div>');
            },
            success: function (data) {
                $("#pos_modalBody_posItemized_sales_report").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }*/


</script>