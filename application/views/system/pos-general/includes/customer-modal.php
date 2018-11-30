<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="customer_master_add">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Customer</h4>
            </div>
            <?php echo form_open('', 'role="form" id="customer_master_form"'); ?>
            <div class="modal-body">
                <div class="form-horizontal">

                    <div class="form-group">
                        <label class="col-md-4 control-label">Customer Name <?php required_mark() ?></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control input-md" id="cus_customerName" name="customerName"
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Telephone</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                                <input type="text" class="form-control input-md" id="cus_customerTelephone"
                                       name="customerTelephone">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Email</label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                                <input type="text" class="form-control input-md" id="customerEmail"
                                       name="customerEmail">
                            </div>
                        </div>
                    </div>

                </div>


                <div class="row">
                    <div class="form-group col-sm-4 hide">
                        <label for="">Customer Secondary Code <?php required_mark() ?></label>
                        <input type="text" class="form-control" id="cus_customercode" name="customercode">
                    </div>
                    <div class="form-group col-sm-4 hide"><label for="financeyear">Category</label>
                        <?php echo form_dropdown('partyCategoryID', $customerCategory, '', 'class="form-control select2"  id="partyCategoryID"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4 hide">
                        <label for="">Receivable Account <?php required_mark() ?></label>
                        <?php echo form_dropdown('receivableAccount', $gl_code_arr, $this->common_data['controlaccounts']['ARA'], 'class="form-control select2" id="receivableAccount" required'); ?>
                    </div>
                    <div class="form-group col-sm-4 hide">
                        <label for="financeyear">Customer Currency <?php required_mark() ?></label>
                        <?php echo form_dropdown('customerCurrency', $currncy_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" onchange="changecreditlimitcurr()" id="customerCurrency" required'); ?>
                    </div>
                    <div class="form-group col-sm-4 hide"><label for="financeyear">Customer
                            Country <?php required_mark() ?></label>

                        <?php echo form_dropdown('customercountry', $country_arr, $this->common_data['company_data']['countryID'], 'class="form-control select2"  id="customercountry" required'); ?>

                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4 hide">
                        <label for="">Tax Group</label>
                        <?php echo form_dropdown('customertaxgroup', $taxGroup_arr, '', 'class="form-control"  id="customertaxgroup"'); ?>
                    </div>

                </div>

                <div class="row hide">
                    <div class="form-group col-sm-4 hide">
                        <label for="">Fax</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-fax" aria-hidden="true"></i></div>
                            <input type="text" class="form-control" id="customerFax" name="customerFax">
                        </div>
                    </div>
                    <div class="form-group col-sm-4 hide">
                        <label for="financeyear">Credit Period</label>
                        <div class="input-group">
                            <div class="input-group-addon">Month</div>
                            <input type="text" class="form-control number" id="customerCreditPeriod"
                                   name="customerCreditPeriod">
                        </div>
                    </div>
                    <div class="form-group col-sm-4 hide"><label for="financeyear">Credit Limit</label>
                        <div class="input-group">
                            <div class="input-group-addon">LKR</div>
                            <input type="text" class="form-control number" id="customerCreditLimit"
                                   name="customerCreditLimit">
                        </div>
                    </div>
                </div>
                <div class="row hide">
                    <div class="form-group col-sm-4 hide">
                        <label for="">URL</label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-link" aria-hidden="true"></i></div>
                            <input type="text" class="form-control" id="customerUrl" name="customerUrl">
                        </div>
                    </div>
                    <div class="form-group col-sm-4 hide">
                        <label for="financeyear">Primary Address</label>
                        <textarea class="form-control" rows="2" id="customerAddress1"
                                  name="customerAddress1"></textarea>
                    </div>
                    <div class="form-group col-sm-4 hide"><label for="financeyear">Secondary Address</label>
                        <textarea class="form-control" rows="2" id="customerAddress2"
                                  name="customerAddress2"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk"
                                                                               aria-hidden="true"></span> Save
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function (e) {
        $('#customer_master_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                /*customercode: {validators: {notEmpty: {message: 'customer Code is required.'}}},*/
                customerName: {validators: {notEmpty: {message: 'customer Name is required.'}}}
                /*customercountry: {validators: {notEmpty: {message: 'customer Country is required.'}}},*/
                /*receivableAccount: {validators: {notEmpty: {message: 'Receivabl Account is required.'}}},
                 customerCurrency: {validators: {notEmpty: {message: 'customer Currency  is required.'}}},
                 customerName: {validators: {notEmpty: {message: 'customer Name is required.'}}}*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'currency_code', 'value': $('#customerCurrency option:selected').text()});
            data.push({'name': 'country', 'value': $('#customercountry option:selected').text()});
            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Pos/savecustomer'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        refreshNotifications(true);
                        $('#customer_master_add').modal('hide');
                        LoadCustomers();
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });
        });
    })
</script>