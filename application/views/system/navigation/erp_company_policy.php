<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('config_company_policy');
echo head_page($title, false);

/*echo head_page('Company Policy', false);*/
$companyID = current_companyID();
$documentCodes = $this->db->query("SELECT srp_erp_documentcodemaster.documentID,srp_erp_documentcodemaster.document FROM srp_erp_documentcodemaster INNER JOIN srp_erp_documentcodes ON srp_erp_documentcodemaster.documentID = srp_erp_documentcodes.documentID AND srp_erp_documentcodes.isApprovalDocument = 1 and srp_erp_documentcodemaster.companyID='{$companyID}'")->result_array();

$policyMasters = $this->db->query("SELECT companyPolicyDescription, companypolicymasterID,fieldType FROM srp_erp_companypolicymaster ")->result_array();
?>
<div class="table-responsive" id="policyTable">
    <!--<button style="margin-bottom: 5px;" type="button" class="btn btn-primary btn-xs pull-right "
            onclick="companyPolicyModal()">Change Company Policy
    </button>-->
    <table id="company_policy_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="width: 35px;">#</th>
            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="width: 80px;"><?php echo $this->lang->line('config_document_id');?><!--Document ID--></th>
            <th><?php echo $this->lang->line('config_default_value');?><!--Default Value--></th>
            <!--            <th style="width: 55px">Is Active</th>-->
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div aria-hidden="true" role="dialog" id="company_policy_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <!--<h5 class="modal-title">Add Item Transfer </h5>-->
            </div>
            <div class="modal-body">
                <div class="row margin-bottom">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <label for=""><?php echo $this->lang->line('config_select_a_document');?><!--Select a Document--></label>
                        <select name="documentId" id="documentId" class="form-control"
                                onchange="getDocumentPolicy(this)">
                            <?php foreach ($documentCodes as $documentCode) { ?>
                                <option
                                    value="<?php echo $documentCode['documentID'] ?>"><?php echo $documentCode['document'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <form action="#" id="changePolicyForm">
                            <table class="table table-bordered table-striped report-table-condensed">
                                <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                                    <th><?php echo $this->lang->line('common_value');?><!--Value--></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($policyMasters as $policyMaster) {
                                    ?>
                                    <tr>
                                        <td><?php echo $policyMaster['companyPolicyDescription']; ?></td>
                                        <td>
                                            <?php
                                            $filedType = $policyMaster['fieldType'];
                                            switch ($filedType) {
                                                case 'select':
                                                    $values = $this->db->query("SELECT * FROM srp_erp_companypolicymaster_value WHERE companypolicymasterID='{$policyMaster['companypolicymasterID']}'")->result_array();

                                                    echo '<select name="' . $policyMaster['companypolicymasterID'] . '" id="' . $policyMaster['companypolicymasterID'] . '" class="form-control">';
                                                    echo '<option></option>';
                                                    foreach ($values as $value) {
                                                        echo "<option value='{$value['value']}'>{$value['value']}</option>";
                                                    }
                                                    echo ' </select>';
                                                    break;
                                                case 'text':
                                                    /*$values = $this->db->query("SELECT * FROM srp_erp_companypolicymaster_value WHERE companypolicymasterID='{$policyMaster['companypolicymasterID']}'")->row_array();*/
                                                    echo "<input name='{$policyMaster['companypolicymasterID']}' value='' id='{$policyMaster['companypolicymasterID']}'>";
                                                    break;

                                                case 'checkbox':
                                                    break;
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button class="btn btn-primary" type="button" onclick="ChangePolicy()"><?php echo $this->lang->line('common_save_change');?><!--Save changes-->
                </button>
            </div>

        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="password_complexcity_modal"
     class=" modal fade bs-example-modal-lg"
     style="display: none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('config_password_complexity_configuration');?><!--Password Complexity Configuration--></h5>
            </div>
            <?php echo form_open('', 'role="form" id="password_complexity_form"'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label><?php echo $this->lang->line('config_minimum_length');?><!--Minimum Length--></label>
                        <input type="number" name="minimumLength" id="minimumLength" class="form-control">
                    </div>

                    <div class="form-group col-md-12 hidden">
                        <label><?php echo $this->lang->line('config_maximum_length');?><!--Maximum Length--></label>
                        <input type="number" name="maximumLength" id="maximumLength" class="form-control">
                    </div>

                    <div class="form-group col-md-12">
                        <label><?php echo $this->lang->line('config_capital_length');?><!--Capital Letters--></label>
                        <select name="isCapitalLettersMandatory"  id="isCapitalLettersMandatory" class="form-control">
                            <option value="0"><?php echo $this->lang->line('common_no');?><!--No--></option>
                            <option value="1"><?php echo $this->lang->line('common_yes');?><!--YES--></option>
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label><?php echo $this->lang->line('config_special_characters');?><!--Special Characters--></label>
                        <select name="isSpecialCharactersMandatory"  id="isSpecialCharactersMandatory" class="form-control">
                            <option value="0"><?php echo $this->lang->line('common_no');?><!--No--></option>
                            <option value="1"><?php echo $this->lang->line('common_yes');?><!--YES--></option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary " onclick="savepasswordcomplexity()"><i
                            class="fa fa-floppy-o"></i> <?php echo $this->lang->line('common_save');?><!--Save-->
                    </button>
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    fetch_company_policy_table();
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/navigation/erp_company_policy', 'Test', 'Company Filter')
        })
    });

    function fetch_company_policy_table() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            url: "<?php echo site_url('CompanyPolicy/fetch_company_policy'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#policyTable').html(data);
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function xfetch_company_policy_table() {
        var Otable = $('#company_policy_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('CompanyPolicy/fetch_company_policy'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                var
                    tmp_i = oSettings._iDisplayStart;
                var
                    iLen = oSettings.aiDisplay.length;
                var
                    x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
                makeTdAlign('company_policy_table', 'center', [2])
            },
            "aoColumns": [
                {"mData": "companypolicymasterID"}, {"mData": "companyPolicyDescription"}, {"mData": "documentID"}, {"mData": "change_policy"}
            ],
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

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function companyPolicyActive(autoID, element) {
        var isChecked;
        if ($(element).is(':checked')) {
            isChecked = 1;
        } else {
            isChecked = 0;
        }

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {autoID: autoID, isChecked: isChecked},
            url: "<?php echo site_url('CompanyPolicy/master_policy_update'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }


    function ChangePolicy(element) {
        var id = $(element).attr('id'),
            value = $(element).val(),
            type = $(element).data('type')

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {id: id, value: value, type: type},
            url: "<?php echo site_url('CompanyPolicy/policy_detail_update');?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                fetchPage('system/navigation/erp_company_policy', 'Test', 'Company Filter')
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    /*function companyPolicyModal() {
     $('#documentId').val('')
     $('#changePolicyForm')[0].reset()
     $('#company_policy_modal').modal('show')
     }*/

    /*function ChangePolicy() {
     if ($('#documentId').val() == '') {
     notification('Please Select a Document', 'e')
     return false;
     }

     var formData = $('#changePolicyForm').serializeArray();
     formData.push({name: 'documentId', value: $('#documentId').val()})
     $.ajax({
     async: true,
     type: 'post',
     dataType: 'json',
     data: formData,
     url: "echo site_url('CompanyPolicy/policy_detail_update');",
     beforeSend: function () {
     startLoad();
     },
     success: function (data) {
     stopLoad();
     notification(data[1], data[0])
     }, error: function () {
     alert('An Error Occurred! Please Try Again.');
     stopLoad();
     }
     });
     }*/

    /*function getDocumentPolicy(element) {
     var documentID = $(element).val()
     $.ajax({
     async: true,
     type: 'post',
     dataType: 'json',
     data: {documentID: documentID},
     url: "echo site_url('CompanyPolicy/get_document_policy');",
     beforeSend: function () {
     startLoad()
     },
     success: function (data) {
     $('#changePolicyForm select,#changePolicyForm input').val('')
     $(data).each(function (i, v) {
     $(v).each(function (index, value) {
     $('#' + value['companypolicymasterID']).val(value['value'])
     })
     })
     stopLoad()
     }, error: function () {
     alert('An Error Occurred! Please Try Again.');
     stopLoad()
     }
     });
     }*/

    function addpasswordpolicy() {
       var data=0;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('CompanyPolicy/get_password_policy'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(jQuery.isEmptyObject(data)) {
                    $('#password_complexcity_modal').modal('show');
                } else {
                    $('#password_complexcity_modal').modal('show');
                    $('#minimumLength').val(data['minimumLength']);
                    $('#maximumLength').val(data['maximumLength']);
                    $('#isCapitalLettersMandatory').val(data['isCapitalLettersMandatory']);
                    $('#isSpecialCharactersMandatory').val(data['isSpecialCharactersMandatory']);
                }


            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });
    }

    function savepasswordcomplexity(){
        var data = $("#password_complexity_form").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('CompanyPolicy/save_password_complexity'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#password_complexcity_modal').modal('hide');
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });
    }
</script>