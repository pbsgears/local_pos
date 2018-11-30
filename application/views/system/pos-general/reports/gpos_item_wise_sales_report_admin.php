<div class="box">
    <div class="box-header with-border" id="box-header-with-border">
        <h3 class="box-title" id="box-header-title"><i class="fa fa-bar-chart"></i> Item Wise Sales Report</h3>
        <div class="box-tools pull-right">
            <button id="" class="btn btn-box-tool page-minus" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button id="" class="btn btn-box-tool headerclose navdisabl"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">

        <form id="frm_itemizedSalesReport" method="post" class="form-inline" role="form">
            <label for="" class="col-sm-2">Filters </label>

            <div class="form-group">
                <label class="sr-only" for="">From</label>
                From

                <input type="text" required class="form-control input-sm" data-date-end-date="0d"
                       name="filterFrom" id="filterFrom2" value="<?php echo date('d/m/Y') ?>"
                       style="width: 79px;">
                to
                <input type="text" required class="form-control input-sm" data-date-end-date="0d"
                       value="<?php echo date('d/m/Y') ?>"
                       style="width: 79px;" placeholder="To" name="filterTo" id="filterTo2">
            </div>

            <button type="button" onclick="loadPaymentItemized_salesReport()" class="btn btn-primary btn-sm">Generate Report
            </button>
        </form>
        <hr>
        <div id="pos_modalBody_posItemized_sales_report" >
            <div class="text-center" style="color:#9c9c9c; font-size:20px; margin-top:75px; min-height: 200px;    border: 2px solid gray !important; padding: 10px !important; " > Click on the Generate
                Report
            </div>
        </div>
        
    </div>
</div>
<script>
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
    $(document).ready(function (e) {
        $("#filterFrom2,#filterTo2").datepicker({
            format: 'dd/mm/yyyy'
        });


    });
</script>