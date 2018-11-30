<?php echo head_page('Manage POS Customer', false);
/*$main_category_arr = all_main_category_drop();
$key = array_filter($main_category_arr, function ($a) {
    return $a == 'FA | Fixed Assets';
});
unset($main_category_arr[key($key)]);*/
$posCustomerAutoID = isset($page_id) && !empty($page_id) ? $page_id : 0;


?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>


<form method="post" id="from_add_edit_customer">
    <input type="hidden" value="" id="posCustomerAutoID" name="posCustomerAutoID"/>

    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2>Customer Detail </h2>
            </header>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Name </label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <input type="text" name="CustomerName" id="CustomerName" class="form-control" placeholder="Name"
                           required>
                    <span class="input-req-inner"></span>
                </span>
                </div>

            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Country </label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('customerCountry', load_all_countryName_drop(), '', 'id="customerCountry" class="form-control" required') ?>
                    <!--<select name="Gender" id="Gender" class="form-control" required>
                        <option value="">Select</option>
                        <option value="1">Male</option>
                        <option value="2">Female</option>
                    </select>-->
                    <span class="input-req-inner"></span>
                </span>
                </div>
            </div>
        </div>


        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2>Contact Detail </h2>
            </header>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Address </label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <input type="text" name="CustomerAddress1" id="CustomerAddress1" class="form-control"
                           placeholder="Address"
                           required>
                    <span class="input-req-inner"></span>
                </span>
                </div>
            </div>


            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Email </label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <input type="email" name="customerEmail" id="customerEmail" class="form-control"
                           placeholder="@email"
                           required>
                    <span class="input-req-inner"></span>
                </span>

                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Telephone </label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <input type="text" name="customerTelephone" id="customerTelephone" class="form-control"
                           placeholder="Telephone">
                    <!--<span class="input-req-inner"></span>-->
                </span>

                </div>

            </div>


        </div>
    </div>

    <div class="col-md-12 animated zoomIn">
        <div class="row" style="margin-top: 10px;">
            <div class="col-sm-7">
                <div class="pull-right">
                    <button class="btn btn-primary" type="submit" id="submitCustomerBtn"><i class="fa fa-plus"></i> Add
                        Customer
                    </button>
                </div>
            </div>
        </div>
    </div>

</form>


<?php echo footer_page('Right foot', 'Left foot', false); ?>


<script type="text/javascript">

    $(document).ready(function () {
        setTimeout(function () {
            $("#CustomerName").focus();
        }, 1000);

        //$("#customerCountry").select2();
        $('.headerclose').click(function () {
            fetchPage('system/pos/masters/pos_customermaster', '', 'Customers');
        });

        $("#from_add_edit_customer").submit(function (e) {
            addEditCustomer();
            return false;
        });
        loadCustomerDetail();
        $("#customerCountry").select2();
    });


    function addEditCustomer() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Pos_customerMaster/add_edit_customer"); ?>',
            dataType: 'json',
            data: $("#from_add_edit_customer").serialize(),
            async: false,
            success: function (data) {
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
                else if (data['error'] == 0) {
                    if (data['code'] == 1) {
                        $("#from_add_edit_customer")[0].reset();
                        $("#customerCountry").val('').change();
                    }
                    myAlert('s', data['message']);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                myAlert('e', xhr.responseText);
            }
        });
    }

    function loadCustomerDetail() {
        var posCustomerAutoID = '<?php echo $posCustomerAutoID ?>';
        if (posCustomerAutoID > 0) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Pos_customerMaster/loadCustomerDetail"); ?>',
                dataType: 'json',
                data: {posCustomerAutoID: posCustomerAutoID},
                async: false,
                success: function (data) {
                    if (data['error'] == 0) {
                        myAlert('s', data['message']);
                        $("#submitCustomerBtn").html('<i class="fa fa-pencil"></i> Edit Customer');
                        $("#posCustomerAutoID").val(data['posCustomerAutoID']);
                        $("#CustomerName").val(data['CustomerName']);
                        $("#customerCountry").val(data['customerCountry']).change();
                        $("#CustomerAddress1").val(data['CustomerAddress1']);
                        $("#customerEmail").val(data['customerEmail']);
                        $("#customerTelephone").val(data['customerTelephone']);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', xhr.responseText);
                }
            });
        }
    }


</script>