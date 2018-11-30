<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('config_add_company');

echo head_page($title, false);
/*echo head_page('Add Company', false);*/
$countrys = load_country_drop();
$currency_arr = all_currency_drop();
$state = load_state_drop();
$industrytypes_arr = fetch_industryTypes();
$div_arr = array('' => 'Blank', '/' => '/', '-' => '-');

//echo trim($this->session->userdata("companyID"));
//var_dump($this->common_data['company_data']);
$segment_arr = fetch_segment();
?>
<style>
    #views {
        background-color: #FFF !important;
        width: 300px !important;
        height: 300px !important;
        font-size: 24px !important;
        display: block ;

    }
    .jcrop-keymgr{
        display:none !important;
    }
    .jcrop-holder{
        margin-left: 80px !important;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('config_step_one');?><!--Step 1--> - <?php echo $this->lang->line('config_company_header');?><!--Company Header--></a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_company_control_account()" data-toggle="tab"><?php echo $this->lang->line('config_step_two');?><!--Step 2--> - <?php echo $this->lang->line('config_company_control_accounts');?><!--Company Control Accounts--></a>
    <a class="btn btn-default btn-wizard" href="#step3" data-toggle="tab"><?php echo $this->lang->line('config_step_three');?><!--Step 3--> - <?php echo $this->lang->line('config_document_setup');?><!--Document Setup--></a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="company_form"'); ?>
        <div class="row">
            <div class="col-md-3">
                <center>
                    <div class="fileinput-new thumbnail">
                        <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="changeImg"
                             class="img-responsive">
                        <input type="file" name="itemImage" id="itemImage" style="display: none;"
                               onchange="loadImage(this)"/>
                        <!--<input type="file" name="itemImage" id="itemImage" style="display: none;"
                               onchange="loadCropImage(this)"/>-->
                    </div>
                </center>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label><?php echo $this->lang->line('config_company_code');?><!--Company Code--> <?php required_mark(); ?></label>
                        <input type="text" class="form-control" id="companycode" name="companycode" readonly>
                    </div>
                    <div class="form-group col-sm-8">
                        <label><?php echo $this->lang->line('config_company_name');?><!--Company Name--> <?php required_mark(); ?></label>
                        <input type="text" class="form-control" id="companyname" name="companyname">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label><?php echo $this->lang->line('config_company_url');?><!--Company URL--></label>

                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-link" aria-hidden="true"></i></div>
                            <input type="text" class="form-control" id="companyurl" name="companyurl">
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label><?php echo $this->lang->line('config_company_email');?><!--Company Email--></label>

                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                            <input type="email" class="form-control" id="companyemail" name="companyemail">
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label><?php echo $this->lang->line('config_company_phone');?><!--Company Phone--></label>

                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                            <input type="number" step="any" class="form-control" id="companyphone" name="companyphone">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label><?php echo $this->lang->line('config_company_start_date');?><!--Company Start Date--> <?php required_mark(); ?></label>

                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="companystartdate" value="<?php echo date('Y-m-d'); ?>"
                                   id="companystartdate" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label><?php echo $this->lang->line('config_company_default_currency');?><!--Company Default Currency--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('company_default_currency', $currency_arr, '', 'class="form-control select2" id="company_default_currency" required'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label><?php echo $this->lang->line('config_company_reporting_currency');?><!--Company Reporting Currency--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('company_reporting_currency', $currency_arr, '', 'class="form-control select2" id="company_reporting_currency" required'); ?>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row form-horizontal">
            <div class="col-sm-5">
                <div class="form-group">
                    <label for="legalname" class="col-sm-5 control-label"><?php echo $this->lang->line('config_company_legal_name');?><!--Legal Name--> </label>

                    <div class="col-sm-7">
                        <input type="text" class="form-control" id="legalname" name="legalname">
                    </div>
                </div>
                <div class="form-group">
                    <label for="txtidntificationno" class="col-sm-5 control-label"><?php echo $this->lang->line('config_tax_identification_no');?><!--Tax Identification No--> </label>

                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="txtidntificationno" name="txtidntificationno">
                    </div>
                </div>
                <div class="form-group">
                    <label for="textyear" class="col-sm-5 control-label"><?php echo $this->lang->line('config_tax_year');?><!--Tax Year--></label>

                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="textyear" name="textyear">
                    </div>
                </div>
                <div class="form-group">
                    <label for="industry" class="col-sm-5 control-label"><?php echo $this->lang->line('config_industry');?><!--Industry--> </label>

                    <div class="col-sm-7">
                        <?php echo form_dropdown('industry', $industrytypes_arr, '', 'class="form-control" id="industry"'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="industry" class="col-sm-5 control-label"><?php echo $this->lang->line('config_default_segment');?><!--Default Segment--> </label>

                    <div class="col-sm-7">
                        <?php echo form_dropdown('default_segment', $segment_arr, '', 'class="form-control" id="default_segment" '); ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-7">
                <div class="form-group">
                    <label for="per_address_1"
                           class="col-sm-4 control-label"><?php echo $this->lang->line('config_permenet');?><!--Permanent--> <?php required_mark(); ?></label>

                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="companyaddress1" name="companyaddress1"
                               placeholder="Company Address 1" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="per_address_2" class="col-sm-4 control-label"></label>

                    <div class="col-sm-7">
                        <input type="text" class="form-control" id="companyaddress2" name="companyaddress2"
                               placeholder="Company Address 2" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="per_city" class="col-sm-4 control-label"></label>

                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="companycity" name="companycity"
                               placeholder="Company City" required>
                    </div>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="companypostalcode" name="companypostalcode"
                               placeholder="Postal Code" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="state" class="col-sm-4 control-label"></label>

                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="companyprovince" name="companyprovince"
                               placeholder="Province">
                    </div>
                </div>
                <div class="form-group">
                    <label for="per_country" class="col-sm-4 control-label"></label>

                    <div class="col-sm-5">
                        <div class="input-group">
                            <span class="input-group-addon" id="flag-container"><i class="flagstrap-icon flagstrap-lk"></i></span>
                            <select class="form-control" name="companycountry" id="companycountry" onchange="update_logo()">
                                <option value=""><?php echo $this->lang->line('common_select_country');?><!--Select Country--></option>
                                <?php foreach ($countrys as $country) { ?>
                                    <option
                                        value="<?php echo $country['CountryDes']; ?>" data-logo="<?php echo strtolower($country['countryShortCode']); ?>"><?php echo $country['CountryDes'] . ' | ' . $country['countryShortCode']; ?></option>
                                <?php }; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save_and_next');?><!--Save & Next--></button>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <button style="margin-bottom: 10px" onclick="modal_controlAccount()" class="btn btn-primary btn-xs pull-right">
            <?php echo $this->lang->line('common_add');?>  <!--Add-->
        </button>
        <table class="table table-striped table-condensed table-bordered">
            <thead>
            <tr>
                <th colspan="3"><?php echo $this->lang->line('config_control_account');?><!--Control Account--></th>
                <th colspan="4"><?php echo $this->lang->line('config_gl_account');?><!--GL Account--></th>
            </tr>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%"><?php echo $this->lang->line('config_document_code');?><!--Document Code--></th>
                <th style="min-width: 25%"><?php echo $this->lang->line('config_control_account_description');?><!--Control Account Description--></th>
                <th style="min-width: 13%"><?php echo $this->lang->line('config_gl_system_code');?><!--GL System Code--></th>
                <th style="min-width: 12%"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
                <th style="min-width: 30%"><?php echo $this->lang->line('config_gl_description');?><!--GL Description--></th>
                <th style="min-width: 5%">&nbsp;</th>
            </tr>
            </thead>
            <tbody id="company_control_account">
            </tbody>
        </table>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev" onclick=""><?php echo $this->lang->line('common_previous');?><!--Previous--></button>
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <?php echo form_open('', 'role="form" id="companycodeform"'); ?>
        <div id="companyCode_body"></div>
        <hr>
        <div class="text-right m-t-xs">
            <button type="button" onclick="save_companycode_form()" class="btn btn-primary"><?php echo $this->lang->line('common_update');?><!--Update--></button>
        </div>
        </form>
    </div>

</div>

<div class="modal fade" id="GL_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false"
     data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('config_control_accounts');?><!--Control Accounts--></h4>
            </div>
            <form class="form-horizontal" id="chart_of_accont_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="masterCategory" class="col-sm-4 control-label"><?php echo $this->lang->line('config_account_type');?><!--Account Type--></label>

                                <div class="col-sm-8">
                                    <?php echo form_dropdown('accountCategoryTypeID', all_account_category_drop(true, true), '', 'class="form-control" onchange="fetch_master_Account(this.value)" id="accountCategoryTypeID"'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="masterAccountYN1" class="col-sm-4 control-label"><?php echo $this->lang->line('config_master_account');?><!--Master Account--></label>

                                <div class="col-sm-3">
                                    <?php echo form_dropdown('masterAccountYN', array('0' => $this->lang->line('common_no')/*'No'*/), '0', 'class="form-control " id="masterAccountYN" onchange="set_master_detail(this.value)" '); ?>
                                    <?php echo form_dropdown('isBank', array('' => $this->lang->line('config_select_status')/*'Select Status'*/, '1' => $this->lang->line('common_yes')/*'Yes'*/, '0' =>$this->lang->line('common_no') /*'No'*/), '0', 'class="form-control control_account" id="isBank" style="display: none;" required'); ?>
                                </div>
                                <div class="col-sm-5">
                                    <?php echo form_dropdown('masterAccount', array('' => $this->lang->line('config_master_account')/*'Master Account'*/), '', 'class="form-control set_master" id="masterAccount"'); ?>
                                </div>
                            </div>


                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="GLSecondaryCode" class="col-sm-4 control-label"><?php echo $this->lang->line('config_secondary_code');?><!--Secondary Code--></label>

                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="GLSecondaryCode" name="GLSecondaryCode">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="GLDescription" class="col-sm-4 control-label"><?php echo $this->lang->line('config_account_name');?><!--Account Name--></label>

                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="GLDescription" id="GLDescription">
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="Submit" class="btn btn-primary"><?php echo $this->lang->line('common_save_change');?><!--Save Changes--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="crop_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false"
     data-backdrop="static">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('config_crop_image');?><!--Crop Image--></h4>
            </div>
            <form id="image_form">
            <div class="modal-body">
                <div class="row">

                        <input type="hidden" id="imageFaId" name="imageFaId">
                        <div style="margin-left: 20px;">
                            <div id="views"></div>
                            <!--<input type="submit" value="Upload form data and image" />-->
                        </div>

                </div>
            </div>
            <div class="modal-footer">
                <button id="cropbutton" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('config_crop');?><!--Crop--></button>
               <!-- <button type="button" onclick="save_image()" class="btn btn-primary btn-sm" >Save Changes</button>-->
                <button type="submit"  class="btn btn-primary btn-sm" ><?php echo $this->lang->line('common_save_change');?><!--Save Changes--></button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var companyid;
    var crop_max_width = 300;
    var crop_max_height = 300;
    var jcrop_api;
    var canvas;
    var context;
    var image;

    var prefsize;

    $(document).ready(function () {
        companyid = null;
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            companyid = p_id;
            load_company_header();
            load_companyCode_table();
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
        }

        $('#companystartdate').datepicker({
            format: 'yyyy/mm/dd'
        }).on('changeDate', function (ev) {
            $('#company_form').bootstrapValidator('revalidateField', 'companystartdate');
            $(this).datepicker('hide');
        });

        $('#company_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                companycode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_company_code_is_required');?>.'}}},/*Company Code is required*/
                companyname: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_company_name_is_required');?>.'}}},/*Company Name is required*/
                companystartdate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_company_start_date_is_required');?>.'}}},/*Company Start Date is required*/
                //companyurl: {validators: {notEmpty: {message: 'Company URL is required.'}}},
                //companyemail: {validators: {notEmpty: {message: 'Company Email is required.'}}},
                //companyphone: {validators: {notEmpty: {message: 'Company Phone is required.'}}},
                companyaddress1: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_company_address_one_is_required');?>.'}}},/*Company Address 1 is required*/
                companyaddress2: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_company_address_two_is_required');?>.'}}},/*Company Address 2 is required*/
                companycity: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_company_city_is_required');?>.'}}},/*Company City is required*/
                //companyprovince: {validators: {notEmpty: {message: 'Company Province is required.'}}},
                companypostalcode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_company_postal_code_is_required');?>.'}}},/*Company Postal Code is required*/
                companycountry: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_company_country_is_required');?>.'}}},/*Company Country is required*/
                //default_segment: {validators: {notEmpty: {message: 'Default Segment is required.'}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'companyid', 'value': companyid});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Company/save_company'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    HoldOn.close();
                    refreshNotifications(true);
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });

        $('#company_control_accounts_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                APA: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_accounts_payable_code_is_required');?>.'}}},/*Accounts Payable Code is required*/
                ARA: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_accounts_receivable_is_required');?>.'}}},/*Accounts Receivable is required*/
                INVA: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_accounts_inventory_control_is_required');?>.'}}},/*Inventory Control is required*/
                ACA: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_accounts_asset_control_is_required');?>.'}}},/*Asset Control Account is required*/
                PCA: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_payroll_control_account_is_required');?>.'}}},/*Payroll Control Account is required*/
                UGRV: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_unbilled_grv_is_required');?>.'}}},/*Unbilled GRV is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'companyid', 'value': companyid});
            data.push({'name': 'APA_dec', 'value': $('#APA option:selected').text()});
            data.push({'name': 'ARA_dec', 'value': $('#ARA option:selected').text()});
            data.push({'name': 'INVA_dec', 'value': $('#INVA option:selected').text()});
            data.push({'name': 'ACA_dec', 'value': $('#ACA option:selected').text()});
            data.push({'name': 'PCA_dec', 'value': $('#PCA option:selected').text()});
            data.push({'name': 'UGRV_dec', 'value': $('#UGRV option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Company/save_company_control_accounts'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    HoldOn.close();
                    refreshNotifications(true);
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $('.next').click(function () {
            var nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });


        //load_company_config_details();

        $('#state_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                state: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_state_is_required');?>.'}}}/*State is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Company/save_state'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        $("#state_model").modal("hide");
                        var s = $('#state').val();
                        $('#companyprovince').append('<option value="' + data['last_id'] + '">' + s + '</option>')
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
        /*$("#companyprovince").change(function(){
         window.location.reload(true);
         });*/


    });

    $('#chart_of_accont_form').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded: [':disabled'],
        fields: {
            accountCategoryTypeID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_account_type_is_required');?>.'}}},/*Account Type is required*/
            GLSecondaryCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_account_code_is_required');?>.'}}},/*Account Code is required*/
            GLDescription: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_control_account_description_is_required');?>.'}}},/*Account Description is required*/
            masterAccountYN: {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_control_is_master_account_is_required');?>.'}}},/*Is Master Account is required*/
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        /*data.push({'name': 'GLAutoID', 'value': GLAutoID});*/
        data.push({'name': 'masterAccount_dec', 'value': $('#masterAccount option:selected').text()});
        data.push({'name': 'account_type', 'value': $('#accountCategoryTypeID option:selected').text()});
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Company/save_chartofcontrol_account'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data_arr) {
                stopLoad();
                refreshNotifications(true);
                if (data_arr) {
                    $("#GL_modal").modal("hide");
                    $('#chart_of_accont_form')[0].reset();
                    /*             $('#chart_of_accont_form').bootstrapValidator('resetForm', true);*/
                    fetch_company_control_account();
                }
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    });

    function modal_controlAccount() {
        $('#GL_modal').modal('show');
        $('#masterAccountYN').val(0);
    }
    function fetch_master_Account(val, select_value) {
        string = $('#accountCategoryTypeID option:selected').text();
        accountCategoryTypeID = $('#accountCategoryTypeID option:selected').val();
        if (string) {
            sub_cat = string.split('|');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'subCategory': sub_cat[1], 'accountCategoryTypeID': accountCategoryTypeID},
                url: "<?php echo site_url('Chart_of_acconts/fetch_master_account'); ?>",
                success: function (data) {
                    $('#masterAccount').empty();
                    var mySelect = $('#masterAccount');
                    mySelect.append($('<option></option>').val('').html('Select Master Account'));
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['GLAutoID']).html(text['systemAccountCode'] + ' | ' + text['GLSecondaryCode'] + ' | ' + text['GLDescription']));
                        });
                        if (select_value) {
                            $("#masterAccount").val(select_value);
                        }
                        ;
                    }
                }, error: function () {
                    swal("Cancelled", "Your " + value + " file is safe :)", "error");
                }
            });
        }
    }


    function load_company_header() {
        if (companyid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'companyid': companyid},
                url: "<?php echo site_url('Company/load_company_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $("#companycode").val(data['company_code']);
                        $('#companyname').val(data['company_name']);
                        $('#companystartdate').val(data['company_start_date']);
                        $('#companyurl').val(data['company_url']);
                        $('#companyemail').val(data['company_email']);
                        $('#companyphone').val(data['company_phone']);
                        $('#companyaddress1').val(data['company_address1']);
                        $('#companyaddress2').val(data['company_address2']);
                        $('#companycity').val(data['company_city']);
                        $('#legalname').val(data['legalName']);
                        $('#txtidntificationno').val(data['textIdentificationNo']);
                        $('#textyear').val(data['textYear']);
                        $('#industry').val(data['industryID'] + ' | ' + data['industry']);
                        $('#default_segment').val(data['default_segment']);
                        $('#companyprovince').val(data['company_province']);
                        $('#companypostalcode').val(data['company_postalcode']);
                        $('#companycountry').val(data['company_country']);
                        if (data['company_logo'] == 'no-logo.png') {
                            $("#changeImg").attr("src", "<?php echo base_url('images/logo/no-logo.png'); ?>");
                        } else {
                            $("#changeImg").attr("src", "<?php echo base_url('images/logo/'); ?>" + '/' + data['company_logo']);
                            $("#changeImg").error(function () {
                                $("#changeImg").attr("src", "<?php echo base_url('images/logo/no-logo.png'); ?>");
                            });
                        }
                        update_logo();
                        $('#company_default_currency').val(data['company_default_currency']);
                        $("#company_default_currency").prop("disabled", true);
                        $('#company_reporting_currency').val(data['company_reporting_currency']);
                        $("#company_reporting_currency").prop("disabled", true);


                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }


    function load_company_config_details() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            //data : {'companyid':companyid},
            url: "<?php echo site_url('Company/get_company_config_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    if (!$.isEmptyObject(data)) {
                        $.each(data, function (i, v) {
                            $("#" + v.documentID + "_prefix").val(v.prefix);
                            $("#" + v.documentID + "_serialno").val(v.serialNo);
                            $("#" + v.documentID + "_format_length").val(v.formatLength);
                            $("#" + v.documentID + "_format_1").val(v.format_1);
                            $("#" + v.documentID + "_format_2").val(v.format_2);
                            $("#" + v.documentID + "_format_3").val(v.format_3);
                            $("#" + v.documentID + "_format_4").val(v.format_4);
                            $("#" + v.documentID + "_format_5").val(v.format_5);
                            $("#" + v.documentID + "_format_6").val(v.format_6);
                        });
                    }
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function add_state() {
        $("#state_model").modal({backdrop: "static"});
        $('#state_model').bootstrapValidator('resetForm', true);
        document.getElementById('state_form').reset();
    }

    function fetch_company_control_account() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            url: "<?php echo site_url('Company/fetch_company_control_account'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#company_control_account').empty();
                x = 1;
                if (jQuery.isEmptyObject(data)) {
                    $('#company_control_account').append('<tr class="danger"><td colspan="7" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                }
                else {
                    $.each(data, function (key, value) {
                        $('#company_control_account').append('<tr><td class="text-right">' + x + '.</td><td class="text-center">' + value['controlAccountType'] + '</td><td>' + value['controlAccountDescription'] + '</td><td class="text-center">' + value['systemAccountCode'] + '</td><td><input type="text" class="form-control" id="code_' + value['controlAccountsAutoID'] + '" value="' + value['GLSecondaryCode'] + '"></td><td ><input type="text" class="form-control" id="desc_' + value['controlAccountsAutoID'] + '" value="' + value['GLDescription'] + '"></td><td ><button class="btn btn-primary btn-sm" onclick="save_control_account(' + value['controlAccountsAutoID'] + ',\'' + value['controlAccountDescription'] + '\',' + value['GLAutoID'] + ')"><?php echo $this->lang->line('common_update');?><!--Update--></button></td></tr>');
                        x++;
                    });
                }
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function save_control_account(id, value, GLAutoID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure ?*/
                text: "<?php echo $this->lang->line('config_the_changes_you_have_made');?>. "/*The changes you have made will not impact the records which are entered previously, it will only applied for future transactions*/ + value,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'controlAccountsAutoID': id,
                        'GLAutoID': GLAutoID,
                        'GLSecondaryCode': $('#code_' + id).val(),
                        'GLDescription': $('#desc_' + id).val()
                    },
                    url: "<?php echo site_url('Company/save_control_account'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    $('#changeImg').click(function () {
        $('#itemImage').click();
    });

    function loadImage(obj) {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#changeImg').attr('src', e.target.result);
            };
            reader.readAsDataURL(obj.files[0]);

            var imgageVal = new FormData();
            imgageVal.append('faID', p_id);

            var files = $("#itemImage")[0].files[0];
            imgageVal.append('files', files);
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: imgageVal,
                contentType: false,
                cache: false,
                processData: false,
                url: "<?php echo site_url('Company/company_image_upload'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0],data[1])
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function load_companyCode_table() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {},
            url: "<?php echo site_url('Company/fetch_company_codeMaster'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#companyCode_body').html(data);
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                stopLoad();
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                refreshNotifications(true);
            }
        });
    }

    function save_companycode_form() {
        var data = $("#companycodeform").serialize();
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Company/update_company_codes_prefixChange'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    load_companyCode_table();
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    $("#cropbutton").click(function(e) {
        applyCrop();
    });
    function loadCropImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            canvas = null;
            reader.onload = function(e) {
                image = new Image();
                image.onload = validateImage;
                image.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
            $("#crop_model").modal({backdrop: "static"});
        }
    }

    function dataURLtoBlob(dataURL) {
        var BASE64_MARKER = ';base64,';
        if (dataURL.indexOf(BASE64_MARKER) == -1) {
            var parts = dataURL.split(',');
            var contentType = parts[0].split(':')[1];
            var raw = decodeURIComponent(parts[1]);

            return new Blob([raw], {
                type: contentType
            });
        }
        var parts = dataURL.split(BASE64_MARKER);
        var contentType = parts[0].split(':')[1];
        var raw = window.atob(parts[1]);
        var rawLength = raw.length;
        var uInt8Array = new Uint8Array(rawLength);
        for (var i = 0; i < rawLength; ++i) {
            uInt8Array[i] = raw.charCodeAt(i);
        }

        return new Blob([uInt8Array], {
            type: contentType
        });
    }

    function validateImage() {
        if (canvas != null) {
            image = new Image();
            image.onload = restartJcrop;
            image.src = canvas.toDataURL('image/png');
        } else {
            restartJcrop();
        }
    }

    function restartJcrop() {
        if (jcrop_api != null) {
            jcrop_api.destroy();
        }
        $("#views").empty();
        $("#views").append("<canvas id=\"canvas\">");
        canvas = $("#canvas")[0];
        context = canvas.getContext("2d");
        canvas.width = image.width;
        canvas.height = image.height;
        context.drawImage(image, 0, 0);
        $("#canvas").Jcrop({
            onSelect: selectcanvas,
            onRelease: clearcanvas,
            bgOpacity:   .4,
            aspectRatio: 213 / 144,
            boxWidth: crop_max_width,
            boxHeight: crop_max_height
        }, function() {
            jcrop_api = this;
        });
        clearcanvas();

    }

    function clearcanvas() {
        prefsize = {
            x: 0,
            y: 0,
            w: canvas.width,
            h: canvas.height,
        };
    }

    function selectcanvas(coords) {
        prefsize = {
            x: Math.round(coords.x),
            y: Math.round(coords.y),
            w: Math.round(coords.w),
            h: Math.round(coords.h)
        };
    }

    function applyCrop() {
        canvas.width = prefsize.w;
        canvas.height = prefsize.h;
        context.drawImage(image, prefsize.x, prefsize.y, prefsize.w, prefsize.h, 0, 0, canvas.width, canvas.height);
        validateImage();
    }

    /*function applyScale(scale) {
        if (scale == 1) return;
        canvas.width = canvas.width * scale;
        canvas.height = canvas.height * scale;
        context.drawImage(image, 0, 0, canvas.width, canvas.height);
        validateImage();
    }*/

    /*function applyRotate() {
        canvas.width = image.height;
        canvas.height = image.width;
        context.clearRect(0, 0, canvas.width, canvas.height);
        context.translate(image.height / 2, image.width / 2);
        context.rotate(Math.PI / 2);
        context.drawImage(image, -image.width / 2, -image.height / 2);
        validateImage();
    }*/

    /*function applyHflip() {
        context.clearRect(0, 0, canvas.width, canvas.height);
        context.translate(image.width, 0);
        context.scale(-1, 1);
        context.drawImage(image, 0, 0);
        validateImage();
    }*/

    /*function applyVflip() {
        context.clearRect(0, 0, canvas.width, canvas.height);
        context.translate(0, image.height);
        context.scale(1, -1);
        context.drawImage(image, 0, 0);
        validateImage();
    }*/


    function save_image(){
        $('#imageFaId').val(p_id);
        var data = $("#image_form").serialize();
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: data,
            contentType: false,
            cache: false,
            processData: false,
            url: "<?php echo site_url('Company/company_image_upload'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    $("#image_form").submit(function(e) {
        e.preventDefault();
        formData = new FormData($(this)[0]);
        var blob = dataURLtoBlob(canvas.toDataURL('image/png'));
        //---Add file blob to the form data
        formData.append("files", blob);
        formData.append("faID", p_id);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            url: "<?php echo site_url('Company/company_image_upload'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                if(data['status']==true){
                    $("#crop_model").modal('hide');
                    /*load_company_header();
                    $("#changeImg").attr("src", "<?php //echo base_url('images/logo/'); ?>" + '/' + data['image']);*/
                    fetchPage('system/company/erp_company_configuration_new', p_id)
                }

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    });

function update_logo(){
    var logo = $('#companycountry :selected').attr('data-logo');
    $('#flag-container').html('<i class="flagstrap-icon flagstrap-'+logo+'"></i>');
}
</script>

