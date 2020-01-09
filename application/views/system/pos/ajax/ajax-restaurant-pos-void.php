<style>
    .form-inline {
        background: rgb(255, 255, 255) !important;
        margin-bottom: 0px !important;
        border-bottom: 2px solid rgb(241, 241, 241) !important;
        box-shadow: 0px 2px 5px 0px #d4d4d4 !important;
        padding: 10px !important;
    }
</style>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">
        <?php echo $this->lang->line('posr_all_bills'); ?><!--All Bills--> </a>
    <a class="btn btn-default btn-wizard" href="#step2" data-toggle="tab">
        <?php echo $this->lang->line('posr_voided_bills'); ?><!--Voided Bills--> (
        <?php echo $this->lang->line('posr_today'); ?><!--Today-->)</a>
</div>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <div class="row hide" style="margin-top: 4%;">
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('posr_date_from'); ?><!--Date From--></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="from" value="<?php echo date('Y-m-d') ?>"
                           id="from"
                           class="form-control" required>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('posr_date_to'); ?><!--Date To--></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="to" value="<?php echo date('Y-m-d') ?>"
                           id="to"
                           class="form-control" required>
                </div>
            </div>
        </div>
        <div style="background-color: #ffffff; padding:10px;" class="table-responsive">
            <table class="<?php echo table_class_pos() ?> table-row-select" id="voidListDT">
                <thead>
                <tr>
                    <th> #</th>
                    <th> Bill No.</th>
                    <th>Bill Amount</th>
                    <th> <?php echo $this->lang->line('posr_created_by'); ?><!--Created By--></th>
                    <th> <?php echo $this->lang->line('common_date'); ?><!--Date--></th>
                    <th> &nbsp; </th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
    <div id="step2" class="tab-pane">
        <div style="background-color: #ffffff; padding:10px; margin-top: 4%;" class="table-responsive">
            <table class="<?php echo table_class_pos() ?> table-row-select" id="voidListHist">
                <thead>
                <tr>
                    <th> #</th>
                    <th>Bill No.</th>
                    <th>Bill Amount</th>
                    <th> <?php echo $this->lang->line('posr_created_by'); ?><!--Created By--></th>
                    <th> <?php echo $this->lang->line('common_date'); ?><!--Date--></th>
                    <th> &nbsp; </th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" tabindex="2" id="rpos_print_template_void" class="modal fade" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-responsive-bill">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" onclick="closeVoidRecipt()" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_print'); ?><!--Print--> </h4>
            </div>
            <div class="modal-body" id="pos_modalBody_posPrint_template_void">

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-default btn-sm" onclick="closeVoidRecipt()"
                        style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center; color:black; 10px 1px; margin: 5px auto 10px auto; font-weight:bold;">
                    <i class="fa fa-angle-double-left"
                       aria-hidden="true"></i> <?php echo $this->lang->line('posr_back'); ?> <!--Back-->
                </button>
            </div>
        </div>
    </div>
</div>
<script>

    $(document).ready(function (e) {
        loadVoidOrder();
        loadVoidOrderHistory()

        $('#from').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            $(this).datepicker('hide');
            loadVoidOrder()
        });

        $('#to').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            $(this).datepicker('hide');
            loadVoidOrder()
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });
        $('.table-row-select tbody').on('click', 'tr', function () {
            $('.table-row-select tr').removeClass('dataTable_selectedTr');
            $(this).toggleClass('dataTable_selectedTr');
        });
    });


    function loadVoidOrder() {
        $('#voidListDT').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Pos_restaurant/loadVoidOrders'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "menuSalesID"},
                {"mData": "invoiceCode"},
                {"mData": "subTotal"},
                {"mData": "createdUserName"},
                {"mData": "createdDate"},
                {"mData": "voidBill"}
            ],
            // "columnDefs": [{
            //     "targets": [5],
            //     "orderable": false
            // }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefrom", "value": $("#from").val()});
                aoData.push({"name": "dateto", "value": $("#to").val()});
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

    function voidBill(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('posr_you_want_to_void_this_bill');?>", /*You want to Void this bill!!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>", /*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "<?php echo site_url('Pos_restaurant/void_bill'); ?>",
                    data: {menuSalesID: id},
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            $('#rpos_print_template_void').modal('hide');
                            loadVoidOrder();
                            loadVoidOrderHistory();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


    function loadVoidOrderHistory() {
        $('#voidListHist').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Pos_restaurant/loadVaoidOrderHistory'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "menuSalesID"},
                {"mData": "invoiceCode"},
                {"mData": "netTotalDisplay"},
                {"mData": "createdUserName"},
                {"mData": "createdDate"},
                {"mData": "voidBill"}
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


    function unVoidBill(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('posr_you_want_to_un_void_this_bill');?>", /*You want to Un Void this bill!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>", /*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "<?php echo site_url('Pos_restaurant/un_void_bill'); ?>",
                    data: {menuSalesID: id},
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            loadVoidOrder();
                            loadVoidOrderHistory();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function loadPrintTemplateVoid(id) {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/loadPrintTemplateVoid'); ?>",
            data: {menuSalesID: id},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#email_invoiceID").val(id);
                $('#rpos_print_template_void').modal('show');
                $("#pos_modalBody_posPrint_template_void").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
            }
        });
    }

    function loadPrintTemplateVoidHistory(id) {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_restaurant/loadPrintTemplateVoidHistory'); ?>",
            data: {menuSalesID: id},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#email_invoiceID").val(id);
                $('#rpos_print_template_void').modal('show');
                $("#pos_modalBody_posPrint_template_void").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
            }
        });
    }

    function closeVoidRecipt() {
        $('#rpos_print_template_void').modal('hide');
        setTimeout(function () {
            modalFix()
        }, 450);
    }

</script>