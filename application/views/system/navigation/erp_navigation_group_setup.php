<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('config_navigation_group_setup');
echo head_page($title, false);

/*echo head_page('Navigation Group Setup', false); */?>
<style>
    .header {
        color: #000080;
        font-weight: bolder;
        font-size: 13px;

    }

    .subheader {
        color: black;
        font-weight: bolder;
        font-size: 13px;

    }

    .subdetails {
        /* color: #4e4e4e;*/

        font-size: 12px;
        padding-left: 10px;
    }

    .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
        padding: 4px;
    }

    .highlight {
        background-color: #FFF59D;

    }

    ul {
        list-style-type: none;
    }

    .select2-container {
        box-sizing: border-box;
        display: inline-block;
        margin: 0;
        position: relative;
        vertical-align: middle;
        width: 10% !important;
    }

</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <label class="" style="" for="employeeID"><?php echo $this->lang->line('config_user_group');?><!--User Group--> :</label>
            <?php
            echo form_dropdown('userGroupID', erp_navigation_usergroups(), '', 'onchange="loadform(this.value)"  class="form-control" style="width:150px" id="userGroupID" required'); ?>
        </div>
        <hr>
        <div class="form-group" id="div_reload">

        </div>
    </div>
</div>

<?php

echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/navigation/erp_navigation_group_setup','','Navigation Group Setup');
        });
        $('#userGroupID').select2();

        loadform($('#userGroupID').val());

        $('input[type=checkbox]').click(function () {
            if (this.checked) {
                $(this).parents('li').children('input[type=checkbox]').prop('checked', true);
            }
            $(this).parent().find('input[type=checkbox]').prop('checked', this.checked);
        });

    });

    function loadform(userGroupID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {userGroupID: userGroupID},
            url: "<?php echo site_url('Access_menu/load_navigation_usergroup_setup'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_reload').html(data);
                stopLoad();

            }, error: function () {

            }
        });

    }

    function saveNavigationgroupSetup() {
        var navigationID = [];
        $('.nVal:checked').each(function (i, e) {
            navigationID.push(e.value);
        });
        navigationID = navigationID.join(',');

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {navigationID: navigationID, userGroupID: $('#userGroupID').val()},
            url: "<?php echo site_url('Access_menu/saveNavigationgroupSetup'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);

                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function bank_transaction_edit(bankTransferAutoID) {
        loadform(bankTransferAutoID);
        $('#bankTransactionModal').modal({backdrop: "static"});
    }


    function gettransferedAmount(amount) {
        var conversion = $('#conversion').val();
        var decimal = $('#decimal').val();
        if (conversion != '') {
            exchangeamount = amount * conversion;
            $('#toAmount').val(exchangeamount.toFixed(decimal));
        }
    }

    function bankchange() {
        var bankTo = $('#bankTo').val();
        var bankFrom = $('#bankFrom').val();
        if (bankTo !== '') {
            getcurrencyID(bankTo, '');
        }
        if (bankFrom !== '') {
            getcurrencyID('', bankFrom);
        }

        if (bankTo != '' && bankFrom != '') {
            getexchangerate(bankTo, bankFrom);

        }


    }

    function getcurrencyID(bankTo, bankFrom) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {bankTo: bankTo, bankFrom: bankFrom},
            url: "<?php echo site_url('Bank_rec/getcurrencyID'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                if (bankFrom != '') {
                    $('#fromcurrency').text(data['fromBankCurrencyCode']);
                }
                if (bankTo != '') {
                    $('#tocurrency').text(data['toBankCurrencyCode']);
                }


            }, error: function () {

            }
        });
    }

    function getexchangerate(bankTo, bankFrom) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {bankTo: bankTo, bankFrom: bankFrom},
            url: "<?php echo site_url('Bank_rec/getexchangerate'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#conversion').prop('readonly', false);
                $('#fromcurrency').text(data[0]['subCurrencyCode']);
                $('#tocurrency').text(data[0]['masterCurrencyCode']);

                //$('#fromcurrency').text(data[0]['masterCurrencyCode']);
                // $('#tocurrency').text(data[0]['subCurrencyCode']);

                $('#conversion').val(data[0]['conversion']);
                $('#decimal').val(data['decimal']);
                $('#fromBankCurrencyID').val(data['fromBankCurrencyID']);
                $('#toBankCurrencyCode').val(data[0]['masterCurrencyCode']);
                $('#fromBankCurrencyCode').val(data[0]['subCurrencyCode']);
                $('#toBankCurrencyID').val(data['toBankCurrencyID']);
                $('#fromBankCurrentBalance').val(data['fromBankCurrentBalance']);
                gettransferedAmount($('#fromAmount').val());
                $('#bank_transaction_form').bootstrapValidator('revalidateField', 'toAmount');
                if (data['fromBankCurrencyID'] == data['toBankCurrencyID']) {
                    $('#conversion').prop('readonly', true);
                }

            }, error: function () {

            }
        });

    }

    function open_bank_transaction() {
        loadform('');
        $('#bankTransactionModal').modal({backdrop: "static"});
    }

    function bank_rec() {
        var Otable = $('#transactionTable').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Bank_rec/fetch_bank_transaction'); ?>",
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
            "columnDefs": [
                {"width": "2%", "targets": 0},
                {"width": "6%", "targets": 1},
                {"width": "6%", "targets": 2},
                {"width": "7%", "targets": 3},
                {"width": "18%", "targets": 5},
                {"width": "18%", "targets": 6},
                {"width": "6%", "targets": 8},
                {"width": "6%", "targets": 9},
                {"width": "8%", "targets": 10}
            ],
            "aoColumns": [
                {"mData": "bankTransferCode"},
                {"mData": "bankTransferCode"},
                {"mData": "transferedDate"},
                {"mData": "referenceNo"},
                {"mData": "narration"},
                {"mData": "frombank"},
                {"mData": "toBank"},

                {"mData": "transferedAmount"},
                {"mData": "confirm"},
                {"mData": "approvedYN"},
                {"mData": "edit"},

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
    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyFinanceYearID': companyFinanceYearID},
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function (data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('Select  Financial Period'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['dateFrom'] + '|' + text['dateTo']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                    });
                    if (select_value) {
                        $("#financeyear_period").val(select_value);
                    }
                    ;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function referbackgrv(bankTransferAutoID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'bankTransferAutoID': bankTransferAutoID},
                    url: "<?php echo site_url('Bank_Rec/refer_bank_transaction'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        bank_rec();
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


</script>