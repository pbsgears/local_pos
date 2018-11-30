<?php
echo head_page('<i class="fa fa-archive"></i> Till Management Report', false);
$locations = load_pos_location_drop();


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);


/*echo '<pre>';print_r($locations);echo '<pre>';*/

?>

<div id="filter-panel" class="collapse filter-panel"></div>
<form id="frm_till_management_rpt" method="post" class="form-inline text-center" role="form">
    <div class="col-md-2">
        <select class="select2 filters" name="outletID_f" id="outletID_f">
            <option value="" selected> All</option>
            <?php
            foreach ($locations as $loc) {
                echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . '-' . $loc['wareHouseDescription'] . '</option>';
            }
            ?>
        </select>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="" for="">From Date</label>
            <input type="text" class="form-control input-sm startdateDatepic" id="startdate"
                   name="startdate" value="<?php echo date('d-m-Y 00:00:00') ?>"
                   style="width: 130px;">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="" for="">To Date</label>
            <input type="text" class="form-control input-sm startdateDatepic" id="enddate"
                   name="enddate" value="<?php echo date('d-m-Y 23:59:59') ?>"
                   style="width: 130px;">
        </div>
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-primary btn-sm dsabl">Generate Report</button>
        <button type="button" onclick="load_till_management_report()" class="btn btn-success btn-sm">Print</button>
    </div>
</form>


<div class="col-md-2 pull-right">


</div>
</div>


<div class="table-responsive">
    <table id="TMRpt_table" class="<?php echo table_class(); ?> table-hover">
        <thead>
        <tr>
            <th style="width: 5%">#</th>
            <th>Start Date - Time <i class="fa fa-clock-o"></i> </th>
            <th>End Date - Time <i class="fa fa-clock-o"></i> </th>
            <th>Outlet</th>
            <th>Counter</th>
            <th>Closed By</th>
            <th>Opening Cash Balance</th>
            <th>Cash Sales</th>
            <th>Gift Card Top Up</th>
            <th>Closing Cash Balance <abbr title="Opening Cash Balance + Cash Sales + Gift Card Top Up"><i class="fa fa-question"></i></abbr>  </th>
            <th>Till Cash Balance</th>
            <th>Diff. <abbr title="Till Cash Balance - Closing Cash Balance "><i class="fa fa-question"></i></abbr>  </th>

        </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>


<script type="text/javascript">
    var modal_title = $("#counterCreate_modal_title");
    var counterCreate_model = $("#counterCreate_model");
    var giftCard_form = $("#giftCard_form");
    var TMRpt_table = '';


    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/pos/reports/till_management_report','','Till Management Report');
        });
        load_TMReport();
        $(".select2").select2();
        $("#barcode").keyup(function (e) {
            console.log(e.which);
            if (e.which == 13) {
                save_update();
            }
            return false;
        });
        $("#giftCard_form").submit(function (e) {
            return false;
        })

        $(".filters").change(function (e) {
            TMRpt_table.ajax.reload();
        })
        $('.table-row-select tbody').on('click', 'tr', function () {
            $('.table-row-select tr').removeClass('dataTable_selectedTr');
            $(this).toggleClass('dataTable_selectedTr');
        });

        $('.startdateDatepic').datetimepicker({
            showTodayButton: true,
            format: "DD/MM/YYYY hh:mm A",
            sideBySide: false,
            widgetPositioning: {
                /*horizontal: 'left',*/
                /*vertical: 'bottom'*/
            }
        });

        $("#frm_till_management_rpt").submit(function (e) {
            load_TMReport();
            $('#dsabl').attr('disabled',false);
            return false;
        });

    });

    function load_TMReport() {
        TMRpt_table = $('#TMRpt_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Pos_reports/fetch_till_management_report'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "shiftID"},
                {"mData": "startTime"},
                {"mData": "endTime"},
                {"mData": "wareHouseDescription"},
                {"mData": "counterName"},
                {"mData": "empName"},
                {"mData": "startingBal"},
                {"mData": "cashSalesCol"},
                {"mData": "tmp_giftCardTopUp"},
                {"mData": "closingCashBalance"},
                {"mData": "EndingBal"},
                {"mData": "different_transaction"}
            ],

            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'f_outletID', 'value': $("#outletID_f").val()});
                aoData.push({'name': 'startdate', 'value': $("#startdate").val()});
                aoData.push({'name': 'enddate', 'value': $("#enddate").val()});
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }/*,
             "data": function (d) {
             d.cardMasterID = $("#outletID_f").val()
             }*/
        });
    }

    /**$('.submitBtn').click(function () {
        if ($(this).hasClass('updateBtn')) {
            $('#requestLink').val('<?php echo site_url('pos/update_counterDetails'); ?>');
        } else {
            $('#requestLink').val('<?php echo site_url('pos/new_counter'); ?>');
        }
    });*/

    function save_update() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: $("#giftCard_form").serialize(),
            url: '<?php echo site_url('Pos_giftCard/submit_giftCards'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    //counterCreate_model.modal("hide");
                    setTimeout(function () {
                        TMRpt_table.ajax.reload();
                        $("#barcode").val('');
                        $("#cardMasterID").val(0);
                        $("#barcode").focus();
                        btnHide('saveBtn', 'updateBtn');
                        modal_title.html('<i class="fa fa-credit-card"></i> Gift Card');

                    }, 300);
                } else {
                    $("#barcode").val('');
                    $("#barcode").focus();
                    myAlert('e', data['message']);

                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    /**function open_newCounterModel() {
        $("#cardMasterID").val(0);
        setTimeout(function () {
            $("#barcode").focus();
        }, 500);
        $('#isConform').val(0);
        giftCard_form[0].reset();
        $("#outletID").val('').change();
        //giftCard_form.bootstrapValidator('resetForm', true);
        modal_title.html('<i class="fa fa-credit-card"></i> Gift Card');
        counterCreate_model.modal({backdrop: "static"});
        $('.submitBtn').prop('disabled', false);
        btnHide('saveBtn', 'updateBtn');
    }*/

    /**function editGiftCard(cardMasterID, barcode, outletID, cardExpiryInMonths) {
        setTimeout(function () {
            $("#barcode").focus();
        }, 500);
        giftCard_form[0].reset();
        $('#isConform').val(0);
        modal_title.html('<i class="fa fa-credit-card"></i> Edit Gift Card');
        counterCreate_model.modal({backdrop: "static"});
        $('#cardMasterID').val(cardMasterID);
        $('#outletID').val(outletID).change();
        $('#barcode').val(barcode);
        $('#cardExpiryInMonths').val(cardExpiryInMonths);

        btnHide('updateBtn', 'saveBtn');

    }

     function btnHide(btn1, btn2) {
        $('.' + btn1).show();
        $('.' + btn2).hide();
    }*/

    /**function delete_GiftCard(cardMasterID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'cardMasterID': cardMasterID},
                    url: "<?php echo site_url('Pos_giftCard/delete_giftCard'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();

                        if (data['error'] == 0) {
                            myAlert('s', data['message']);
                            setTimeout(function () {
                                TMRpt_table.ajax.reload();
                            }, 300);
                        } else {
                            myAlert('e', data['message']);
                        }
                    }, error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        );
    }*/


    function load_till_management_report(){
      /**  var data= $("#frm_till_management_rpt").serialize();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Pos_reports/load_till_management_report'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

            }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
            }
        });*/

        var form = document.getElementById('frm_till_management_rpt');
        form.target = '_blank';
        form.action = '<?php echo site_url('Pos_reports/load_till_management_report'); ?>';
        form.submit();
    }

</script>

