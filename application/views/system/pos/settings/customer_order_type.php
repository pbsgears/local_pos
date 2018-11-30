<link rel="stylesheet" href="<?php echo base_url('plugins/iCheck/minimal/_all.css') ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<script src="<?php echo base_url('plugins/iCheck/icheck.min.js') ?>"></script>
<?php
$customerType = all_customer_type();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div class="box box-warning">
    <div class="box-body">
        <div class="nav-tabs-custom">

            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="posContainer">

                        <h4 style="font-size:16px; font-weight: 800;">
                            <?php echo $this->lang->line('pos_config_promotion_and_order_setup'); ?><!--Promotion & Order Setup-->
                            - <?php echo current_companyCode() ?>


                            <span class="btn btn-primary btn-xs pull-right" onclick="openAddCustomerModal()"><i
                                    class="fa fa-plus"></i>
                                <?php echo $this->lang->line('common_update_add_new'); ?><!--Add New--></span>


                        </h4>


                        <table class="<?php echo table_class_pos(1) ?>" id="tbl_customerType"
                               style="font-size:12px;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('common_description'); ?><!--Description--> </th>
                                <th><?php echo $this->lang->line('pos_config_promotion'); ?><!--Promotion-->/
                                    <?php echo $this->lang->line('pos_config_order_type'); ?><!--Order Type--></th>
                                <th><?php echo $this->lang->line('common_percentage'); ?><!--Percentage--> (%)</th>
                                <th>Payment Terms</th>
                                <th>Expense GL</th>
                                <th>Liability GL</th>
                                <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                        </table>

                    </div>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="tab_2">
                    <div id="menu_edit_container2"></div>

                </div>

            </div>
            <!-- /.tab-content -->
        </div>
    </div>
</div>
<script type="text/javascript"
        src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<script>
    $(document).ready(function (e) {
        fetchCustomerType();
    });

    function openAddCustomerModal() {
        $('#customerIDhn').val('');
        $('#fromcustomer')[0].reset();
        $("#customerTypeMasterID").val("").change();
        $("#expenseGLAutoID").val("").change();
        $("#liabilityGLAutoID").val("").change();
        $('#fromcustomer').bootstrapValidator('resetForm', true);
        $(".clsDeliveryType").hide();
        $("#customer_Modal").modal('show');
    }

    function refreshMenuSize() {
        fetchPage('system/pos/settings/menu_size', '', 'Menu Size Master');
    }


    function addcustomer() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/saveCustomer') ?>",
            data: $("#fromcustomer").serialize(),
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    $("#customer_Modal").modal('hide');
                    fetchCustomerType();
                } else if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);

            }
        });
        return false;
    }


    function delete_menuSize(id) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('Pos_config/delete_menuSize') ?>",
                    data: {id: id},
                    dataType: "json",
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 0) {
                            fetchMenuSize();
                            myAlert('s', data['message']);
                        } else {
                            myAlert('e', '<div>' + data['message'] + '</div>');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', 'Status: ' + textStatus + '<br>Message: ' + errorThrown);
                    }
                });
            }
        });
    }

    function fetchCustomerType() {
        $('#tbl_customerType').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Pos_config/fetch_customer_type'); ?>",
            "aaSorting": [[0, 'asc']],
            "aoColumnDefs": [{"bSortable": false, "aTargets": [2, 4, 5, 6, 7]}],
            /*"language": {
             processing: '<i class="fa fa-refresh fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
             },*/
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[name='menueCustomerTypeIsactive']").bootstrapSwitch();
                $("[rel=tooltip]").tooltip();
                var selectedRowID = parseInt('<?php echo (!empty($this->input->post('page_id'))) ? $this->input->post('page_id') : 0; ?>');

                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if (parseInt(oSettings.aoData[x]._aData['EIdNo']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "customerID"},
                {"mData": "customerName"},
                {"mData": "PromotionOrderTypeDeliveryType"},
                {"mData": "commissionPercentage"},
                {"mData": "tmpIsOnTimePayment"},
                {"mData": "expenseGL"},
                {"mData": "liabilityGL"},
                {"mData": "Active"},
                {"mData": "action"}
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

    function edit_customerType(id) {
        $('#fromcustomer').bootstrapValidator('resetForm', true);
        $("#customer_Modal").modal("show");
        $('#customerIDhn').val(id);
        //$('#menuSizeHead').html('Edit Menu Size');
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {customerID: id},
            url: "<?php echo site_url('Pos_config/edit_customer'); ?>",
            success: function (data) {
                $('#customerName').val(data['customerName']);
                $('#customerTypeMasterID').val(data['customerTypeMasterID']);
                $('#commissionPercentage').val(data['commissionPercentage']);
                $('#isOnTimePayment').val(data['isOnTimePayment']).change();
                $('#expenseGLAutoID').val(data['expenseGLAutoID']).change();
                $('#liabilityGLAutoID').val(data['liabilityGLAutoID']).change();
                if (data['customerTypeMasterID'] == 1) {
                    $(".clsDeliveryType").show();

                } else if (data['customerTypeMasterID'] == 3) {
                    $(".clsDeliveryType").hide();
                    $(".wastage").show();
                } else {
                    $(".clsDeliveryType").hide()
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
    }

    function changecustomertypeIsactive(customerID) {
        var compchecked = 0;
        if ($('#menueCustomerTypeIsactive_' + customerID).is(":checked")) {
            compchecked = 1;
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {customerID: customerID, chkedvalue: compchecked},
                url: "<?php echo site_url('Pos_config/update_customer_type_isactive'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert('s', data['message']);
                    if (data['message'] == 's') {

                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });

        }
        else if (!$('#menueCustomerTypeIsactive_' + customerID).is(":checked")) {

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {customerID: customerID, chkedvalue: 0},
                url: "<?php echo site_url('Pos_config/update_customer_type_isactive'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert('s', data['message']);
                    if (data['message'] == 's') {

                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

</script>


<div class="modal fade pddLess" data-backdrop="static" id="customer_Modal" role="dialog">
    <div class="modal-dialog" style="min-width: 60%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px; ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">
                    <?php echo $this->lang->line('pos_config_add_promotion_or_order'); ?><!--Add Promotion or Order-->
                </h4>
            </div>
            <form role="form" id="fromcustomer" class="form-horizontal">
                <input type="hidden" class="form-control" id="customerIDhn" name="customerIDhn">
                <div class="modal-body" style="min-height: 100px; ">

                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <fieldset>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"
                                           for="customerName"><?php echo $this->lang->line('common_description'); ?></label>
                                    <div class="col-md-4">

                                        <input type="text" class="form-control input-md" id="customerName"
                                               name="customerName">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="customerTypeMasterID">
                                        <span
                                            id="party_text"> <?php echo $this->lang->line('common_type'); ?><!--Type--> </span> <?php required_mark(); ?>
                                    </label>
                                    <div class="col-md-4">
                                        <?php echo form_dropdown('customerTypeMasterID', $customerType, '', 'class="form-control input-md select2" onchange="checkOnTimePayment(this)" id="customerTypeMasterID" required'); ?>
                                    </div>
                                </div>

                                <div class="form-group clsDeliveryType" id="onlyForDelivery" style="display: none;">
                                    <label class="col-md-4 control-label" for="isOnTimePayment">
                                        Payment Mode
                                    </label>
                                    <div class="col-md-4">
                                        <select name="isOnTimePayment" id="isOnTimePayment"
                                                class="form-control input-md select2">
                                            <option value="">Please select</option>
                                            <option value="1">on-time Payment</option>
                                            <option value="0">Late Payment</option>
                                        </select>
                                        <div id='infoicon'><i class="fa fa-info-circle" aria-hidden="true" onclick="showinfo()"></i></div>
                                        <span class="help-block" id="infodetails">If the Delivery customer collect the commission then and their, select 'on-time payment' else 'Late Payment.</span>
                                    </div>
                                </div>

                                <?php
                                $chartOfAccount = get_chartOfAccountDop_pos();
                                ?>
                                <div class="form-group clsDeliveryType wastage">
                                    <label class="col-md-4 control-label" for="expenseGLAutoID">
                                        Expense GL
                                    </label>
                                    <div class="col-md-7">
                                        <?php echo form_dropdown('expenseGLAutoID', $chartOfAccount, '', ' id="expenseGLAutoID"  class="form-control input-md select2"'); ?>
                                    </div>
                                </div>


                                <div class="form-group clsDeliveryType">
                                    <label class="col-md-4 control-label" for="liabilityGLAutoID">
                                        Liability GL
                                    </label>
                                    <div class="col-md-7">
                                        <?php echo form_dropdown('liabilityGLAutoID', $chartOfAccount, '', ' id="liabilityGLAutoID"  class="form-control input-md select2"'); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="customerTypeMasterID">
                                        <?php echo $this->lang->line('common_percentage'); ?><!--Percentage-->
                                        (%) <?php required_mark(); ?></label>
                                    <div class="col-md-4">
                                        <input type="number" step="any" class="form-control input-md"
                                               id="commissionPercentage"
                                               name="commissionPercentage">
                                    </div>
                                </div>


                            </fieldset>

                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="padding: 5px 10px;">
                    <button type="button" onclick="addcustomer()" class="btn btn-primary btn-xs"><i class="fa fa-check"
                                                                                                    aria-hidden="true"></i>
                        <?php echo $this->lang->line('common_add'); ?><!--Add-->
                    </button>
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function checkOnTimePayment(tmpThisVal) {
        var tmpValue = tmpThisVal.value;
        console.log(tmpValue);
        if (tmpValue == 1) {
            //$("#onlyForDelivery").show();
            $(".clsDeliveryType").show();
            $("#commissionPercentage").val('');
            $("#infodetails").hide();

        } else if (tmpValue == 3) {
            $(".clsDeliveryType").hide();
            $("#isOnTimePayment").val('');
            $(".wastage").show();
            $("#commissionPercentage").val(100);
        } else {
            $("#commissionPercentage").val('');
            //$("#onlyForDelivery").hide();
            $(".clsDeliveryType").hide();
            $("#isOnTimePayment").val('');

        }

    }
    $(document).ready(function (e) {
        $(".select2").select2()
    })

   function showinfo(){
        $("#infodetails").show();
       document.getElementById("infoicon").innerHTML = "<i class='fa fa-info-circle'  aria-hidden='true' onclick='hideinfo()'></i>";
   }

   function hideinfo(){
        $("#infodetails").hide();
       document.getElementById("infoicon").innerHTML = "<i class='fa fa-info-circle'  aria-hidden='true' onclick='showinfo()'></i>";
   }


</script>
