</div><!-- /.content-wrapper -->
</div>
<footer class="main-footer <?php echo isset($notFixed) ? 'hide' : ''; /*navbar-fixed-bottom*/ ?>"
        style="padding:4px; opacity: 0.5">
    <div class="pull-right hidden-xs">
        <!--<b><?php /*echo "Time "; */ ?>{elapsed_time} </b><?php /*echo " Memory "; */ ?>{memory_usage}-->
        <b><?php echo "Timezone :  " . current_timezoneDescription(); ?></b>
    </div>
    <strong> Copyright &copy; 2015-2020</strong> All rights reserved.
</footer>

<?php if (strtolower(SETTINGS_BAR) == 'on') { ?>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Create the tabs -->
        <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
            <!--<li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
            <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>-->
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
            <!-- Home tab content -->
            <div class="tab-pane" id="control-sidebar-home-tab">
                <!-- /.control-sidebar-menu -->
            </div><!-- /.tab-pane -->

            <!-- Settings tab content -->
            <div class="tab-pane" id="control-sidebar-settings-tab">

            </div><!-- /.tab-pane -->
        </div>
    </aside><!-- /.control-sidebar -->
<?php } ?>

<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>
<!-- <div  data-keyboard="false" data-width="95%" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade in" style="display: block;" aria-hidden="false"> -->
<!-- <div class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" id="common_modal" >
  <div class="modal-dialog modal-lg">
  <div class="modal-content">
      <div class="modal-header">
          <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
          <h4 id="modal_title" class="modal-title">Modal Title</h4>
      </div>
      <div class="modal-body" id="common_data">

      </div>
      <div class="modal-footer">
          <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
      </div>
      </div>
  </div>
</div> -->

</div><!-- ./wrapper -->
<div class="modal fade" id="ap_user_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="ap_user_label">Modal title</h4>
            </div>
            <div class="modal-body">
                <dl class="dl-horizontal">
                    <dt><?php echo $this->lang->line('footer_document_code'); ?><!--Document code--></dt>
                    <dd id="c_document_code">...</dd>
                    <dt><?php echo $this->lang->line('footer_document_date'); ?><!--Document Date--></dt>
                    <dd id="c_document_date">...</dd>
                    <dt><?php echo $this->lang->line('footer_confirmed_date'); ?><!--Confirmed Date--></dt>
                    <dd id="c_confirmed_date">...</dd>
                    <dt><?php echo $this->lang->line('common_confirmed_by'); ?><!--Confirmed By-->&nbsp;&nbsp;</dt>
                    <dd id="c_conformed_by">...</dd>
                </dl>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <th><?php echo $this->lang->line('common_level'); ?><!--Level--></th>
                        <th><?php echo $this->lang->line('footer_approved_date'); ?><!--Approved Date--></th>
                        <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                        <th><?php echo $this->lang->line('common_comment'); ?><!--Comments--></th>
                    </tr>
                    </thead>
                    <tbody id="ap_user_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">
                            <?php echo $this->lang->line('footer_document_not_approved_yet'); ?><!--Document not approved yet--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<!--model for reject approval -->
<div class="modal fade" id="reject_drill_user_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><span aria-hidden="true" class="glyphicon glyphicon-hand-right"></span>
                    <?php echo $this->lang->line('footer_approval_rejected_history'); ?><!--Approval Rejected History-->
                </h4>
            </div>
            <div class="modal-body">
                <dl class="dl-horizontal">
                    <dt><?php echo $this->lang->line('common_document_code'); ?><!--Document code--></dt>
                    <dd id="c_document_code_rejected">...</dd>
                    <!--<dt>Referback Date</dt><dd id="c_document_date_referback">...</dd>-->
                </dl>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <th><?php echo $this->lang->line('common_level'); ?><!--Level--></th>
                        <th><?php echo $this->lang->line('footer_rejected_date'); ?><!--Rejected Date--></th>
                        <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                        <th><?php echo $this->lang->line('common_comments'); ?><!--Comments--></th>
                    </tr>
                    </thead>
                    <tbody id="reject_ap_user_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">
                            <?php echo $this->lang->line('common_document_not_approved_yet'); ?><!--Document not approved yet--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<!--model for referback comments view -->
<div class="modal fade" id="referback_drill_user_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="ap_user_label_referback">Modal title</h4>
            </div>
            <div class="modal-body">
                <dl class="dl-horizontal">
                    <dt><?php echo $this->lang->line('common_document_code'); ?><!--Document code--></dt>
                    <dd id="c_document_code_reject">...</dd>
                    <!--<dt>Referback Date</dt><dd id="c_document_date_referback">...</dd>-->
                </dl>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <th><?php echo $this->lang->line('common_level'); ?><!--Level--></th>
                        <th><?php echo $this->lang->line('footer_referred_back_date'); ?><!--Referred-back Date--></th>
                        <th><?php echo $this->lang->line('common_comments'); ?><!--Comments--></th>
                    </tr>
                    </thead>
                    <tbody id="referback_ap_user_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">
                            <?php echo $this->lang->line('common_document_not_approved_yet'); ?><!--Document not approved yet--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attachment_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="attachment_modal_label">Modal title</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-10"><span class="pull-right">
                      <?php echo form_open_multipart('', 'id="attachment_uplode_form" class="form-inline"'); ?>
                            <div class="form-group">
                                <!-- <label for="attachmentDescription">Description</label> -->
                                <input type="text" class="form-control" id="attachmentDescription"
                                       name="attachmentDescription"
                                       placeholder="<?php echo $this->lang->line('common_description'); ?>...">
                                <!--Description-->
                                <input type="hidden" class="form-control" id="documentSystemCode"
                                       name="documentSystemCode">
                                <input type="hidden" class="form-control" id="documentID" name="documentID">
                                <input type="hidden" class="form-control" id="document_name" name="document_name">
                                <input type="hidden" class="form-control" id="confirmYNadd" name="confirmYNadd">
                            </div>
                          <div class="form-group">
                              <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                   style="margin-top: 8px;">
                                  <div class="form-control" data-trigger="fileinput"><i
                                              class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                              class="fileinput-filename"></span></div>
                                  <span class="input-group-addon btn btn-default btn-file"><span
                                              class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                          aria-hidden="true"></span></span><span
                                              class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                             aria-hidden="true"></span></span><input
                                              type="file" name="document_file" id="document_file"></span>
                                  <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                     data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                    aria-hidden="true"></span></a>
                              </div>
                          </div>
                          <button type="button" class="btn btn-default" onclick="document_uplode()"><span
                                      class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                            </form></span>
                    </div>
                </div>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                        <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                        <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                        <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                    </tr>
                    </thead>
                    <tbody id="attachment_modal_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">
                            <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="documentPageView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     style="z-index: 1000000000;">
    <div class="modal-dialog" role="document" style="width:90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle">Modal title</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-1">
                            <!-- Nav tabs -->
                            <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                                <li id="TabViewActivation_view" class="active"><a href="#home-v"
                                                                                  data-toggle="tab">
                                        <?php echo $this->lang->line('common_view'); ?><!--View--></a></li>
                                <li id="TabViewActivation_attachment">
                                    <a href="#profile-v" data-toggle="tab">
                                        <?php echo $this->lang->line('common_attachment'); ?><!--Attachment--></a>
                                </li>
                                <li class="itemMasterSubTab_footer" id="tab_itemMasterTabF">
                                    <a href="#subItemMaster-v" data-toggle="tab">
                                        <?php echo $this->lang->line('footer_item_master_sub'); ?><!--Item&nbsp;Master&nbsp;Sub--></a>
                                </li>

                            </ul>
                        </div>
                        <div class="col-sm-11" style="padding-left: 0px;margin-left: -2%;">
                            <!-- Tab panes -->
                            <div class="zx-tab-content">
                                <div class="zx-tab-pane active" id="home-v">
                                    <div id="loaddocumentPageView" class="col-md-12"></div>
                                </div>
                                <div class="zx-tab-pane" id="profile-v">
                                    <div id="loadPageViewAttachment" class="col-md-8">
                                        <div class="table-responsive">
                                            <span aria-hidden="true"
                                                  class="glyphicon glyphicon-hand-right color"></span>
                                            &nbsp <strong>
                                                <?php echo $this->lang->line('common_attachments'); ?><!--Attachments--></strong>
                                            <br><br>
                                            <table class="table table-striped table-condensed table-hover">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>
                                                        <?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                                                    <th>
                                                        <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                                    <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                                                    <th>
                                                        <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                                                </tr>
                                                </thead>
                                                <tbody id="View_attachment_modal_body" class="no-padding">
                                                <tr class="danger">
                                                    <td colspan="5" class="text-center">
                                                        <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="zx-tab-pane" id="subItemMaster-v">
                                    <div class="itemMasterSubTab_footer">
                                        <h4>
                                            <?php echo $this->lang->line('footer_item_master_sub'); ?><!--Item Master Sub--></h4>
                                        <div id="itemMasterSubTab_footer_div"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade pddLess" data-backdrop="static" id="passwordresetModal" data-width="80%"
     role="dialog">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true"
                          style="color:red;">x</span>
                </button>
                <h5> <?php echo $this->lang->line('footer_change_password'); ?><!--Change Password--></h5>
            </div>
            <div class="modal-body" id="modal_contact">
                <form class="form-horizontal" method="post" id="passwordFormLogin" autocomplete="off">
                    <div class="form-group">
                        <label for="currentPassword" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('footer_current_password'); ?><!--Current Password--></label>
                        <div class="col-sm-6">
                            <input type="password"
                                   placeholder="<?php echo $this->lang->line('footer_current_password'); ?>"
                                   class="form-control"
                                   name="currentPassword" id="currentPassword"/>

                        </div>

                    </div>
                    <div class="form-group">
                        <label for="newPassword" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('profile_new_password'); ?><?php echo $this->lang->line('footer_new_password'); ?><!--New Password--></label>
                        <div class="col-sm-6">
                            <input type="password" onkeyup="validatepwsStrengthfotr()" class="form-control"
                                   id="newPassword"
                                   name="newPassword"
                                   placeholder="<?php echo $this->lang->line('footer_new_password'); ?>">
                            <div class="progressbr" id="progrssbarlogin">

                            </div>
                        </div>
                        <div class="col-sm-3" id="messagelogin"></div>

                    </div>
                    <div class="form-group">
                        <label for="confirmPassword" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('footer_confirm_password'); ?><!--Confirm Password--></label>
                        <div class="col-sm-6">
                            <input type="password" class="form-control" id="confirmPassword"
                                   name="confirmPassword"
                                   placeholder="<?php echo $this->lang->line('footer_confirm_password'); ?>">
                        </div>
                    </div>

            </div>
            <div class="modal-footer" style="background-color: #ffffff">
                <button type="submit" id="passwordsavebtn" class="btn btn-primary" onclick="">
                    <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_cancel'); ?><!--Cancel--></button>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="insufficient_item_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Insufficient Items</h4>
            </div>

            <form class="form-horizontal" id="insufficient_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right"><a href="" class="btn btn-excel btn-xs" id="btn-excel"
                                                       download="Insufficient Items List.xls"
                                                       onclick="var file = tableToExcel('insufficient_item', 'Insufficient Items List'); $(this).attr('href', file);">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="insufficient_item">
                        <table class="table table-condensed table-bordered">
                            <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Description</th>
                                <th>Current Stock</th>
                            </tr>
                            </thead>
                            <tbody id="insufficient_item_body">

                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="document_status_more_details-model" class="modal fade" role="dialog" data-backdrop="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="document_status_more_details-title"></h4>
            </div>

            <div class="modal-body">
                <div id="document_status_view" style="min-height: 70px;"></div>

                <div class="modal-footer" style="padding: 10px 5px 2px;">
                    <button type="button" class="btn btn-default btn-sm"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$passwordComplexityExist = get_password_complexity_exist();
$passwordComplexity = get_password_complexity();
?>
<?php $this->load->view('include/inc-footer-modals'); ?>

<!-- <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>-->
<!--<link rel="stylesheet" type="text/css"
      href="<?php /*echo base_url('plugins/multiselect/dist/css/bootstrap-multiselect.css'); */ ?>">-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/select2/css/select2.min.css'); ?>"/>
<link rel="stylesheet" type="text/css"
      href="<?php echo base_url('plugins/validation/css/bootstrapValidator.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/sweetalert/sweet-alert.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datepicker/datepicker3.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/offline/offline.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/offline/offline-language-english.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/holdon/HoldOn.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datatables/dataTables.bootstrap.css'); ?>"/>

<!--Nasik 2017-06-13-->
<link rel="stylesheet" type="text/css"
      href="<?php echo base_url('plugins/datatables/fixedColumns.dataTables.min.css'); ?>"/>
<link rel="stylesheet" type="text/css"
      href="<?php echo base_url('plugins/datatables/keyTable.dataTables.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datatables/select.dataTables.min.css'); ?>"/>
<!--End-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/jasny-bootstrap.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/toastr/toastr.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/style.css'); ?>">
<link rel="stylesheet" type="text/css"
      href="<?php echo base_url('plugins/multiSelectCheckbox/dist/css/bootstrap-multiselect.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/xeditable/css/bootstrap-editable.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/timepicker2/bootstrap-timepicker.css'); ?>">

<script type="text/javascript" src="<?php echo base_url('plugins/select2/js/select2.full.min.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/validation/js/bootstrapValidator.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/timepicker2/bootstrap-timepicker2.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datepicker/bootstrap-datepicker.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/dataTables.bootstrap.min.js'); ?>"></script>

<!--Nasik 2017-06-13-->
<script type="text/javascript"
        src="<?php echo base_url('plugins/datatables/dataTables.fixedColumns.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/dataTables.select.js'); ?>"></script>
<!--End-->

<script type="text/javascript" src="<?php echo base_url('plugins/toastr/toastr.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/fastclick/fastclick.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/app.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/sparkline/jquery.sparkline.min.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/jvectormap/jquery-jvectormap-world-mill-en.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/slimScroll/jquery.slimscroll.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/chartjs/Chart.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/demo.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/sweetalert/sweet-alert.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/offline/offline.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/holdon/HoldOn.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/typeahead.bundle.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/bootstrap/js/jasny-bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/handlebars/handlebars-v4.0.5.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/tableHeadFixer/tableHeadFixer.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/multiselect/dist/js/multiselect.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/iCheck/icheck.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/multiSelectCheckbox/dist/js/bootstrap-multiselect.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/daterangepicker/moment.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/xeditable/js/bootstrap-editable.min.js'); ?>"></script>
<!--<script type="text/javascript" src="<?php /*echo base_url('plugins/Dragtable/jquery.dragtable.js'); */ ?>"></script>-->
<script type="text/javascript" src="<?php echo base_url('plugins/bootbox-alert/bootbox.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/jQuery/jquery.maskedinput.js'); ?>"></script>
<!--<script type="text/javascript"
        src="<?php /*echo base_url('plugins/multiselect/dist/js/bootstrap-multiselect.js'); */ ?>"></script>-->
<script type="text/javascript" src="<?php echo base_url('plugins/highchart/highcharts.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/highchart/modules/exporting.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/highchart/modules/no-data-to-display.js'); ?>"></script>

<script type="text/javascript"
        src="<?php echo base_url('plugins/input-mask/dist/jquery.inputmask.bundle.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/input-mask/dist/inputmask/inputmask.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/input-mask/dist/inputmask/inputmask.date.extensions.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/input-mask/dist/inputmask/jquery.inputmask.js'); ?>"></script>
<!-- Added by Nazir to allow - value as well in textbox-->
<script type="text/javascript"
        src="<?php echo base_url('plugins/numeric/jquery.numeric.min.js'); ?>"></script>

<script type="text/javascript"
        src="<?php echo base_url('plugins/tapmodo-Jcrop-1902fbc/js/jquery.Jcrop.min.js'); ?>"></script>

<script type="text/javascript" src="<?php echo base_url('plugins/combodate/combodate.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/combodate/moment.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/datetimepicker/src/js/bootstrap-datetimepicker.js'); ?>"></script>

<!--jquery auto complete-->
<script type="text/javascript"
        src="<?php echo base_url('plugins/jQuery-Autocomplete-master/dist/jquery.autocomplete.js'); ?>"></script>


<script type="text/javascript">

    //fetchcompany(<?php //echo current_companyID() ?>, false);
    var popup = 0;
    var CSRFHash = '<?php echo $this->security->get_csrf_hash() ?>';
    var numberOfAttempt = 0;

    /*Remove employee master filter values */
    window.localStorage.removeItem("isDischarged");
    window.localStorage.removeItem("employeeCode");
    window.localStorage.removeItem("segment");
    window.localStorage.removeItem('emp-master-alpha-search');
    window.localStorage.removeItem('emp-master-searchKeyword');
    window.localStorage.removeItem('emp-master-designation-list');
    window.localStorage.removeItem('emp-master-segment-list');
    window.localStorage.removeItem('emp-master-status-list');
    window.localStorage.removeItem('emp-master-pagination');


    function check_session_status() {
        $.ajax({
            async: true,
            type: 'get',
            dataType: 'json',
            data: {'': ''},
            url: '<?php echo site_url("login/session_status"); ?>',
            success: function (data) {
                if (data['status'] == 0) {
                    session_logout_page();
                } else {
                    CSRFHash = data.csrf;
                }
                stopLoad();
            },
            error: function () {
                stopLoad();
            }
        });
    }

    function refresh_session_status() {
        $.ajax({
            async: true,
            type: 'get',
            dataType: 'json',
            data: {'': ''},
            url: '<?php echo site_url("login/session_status"); ?>',
            success: function (data) {
                if (data['status'] == 0) {
                    session_logout_page();
                } else {
                    CSRFHash = data.csrf;
                }
                stopLoad();
            },
            error: function () {
                stopLoad();
            }
        });
    }

    function session_logout_page() {
        swal({
            title: "Session Destroyed!",
            text: "You will be redirect to login page in 2 seconds.",
            timer: 2000,
            showConfirmButton: false
        });
        setTimeout(function () {
            window.location = '<?php echo site_url('/Login/logout'); ?>';
        }, 2000);
    }

    function fetchPage(page_url, page_id, page_name, policy_id, data_arr, master_page_url=null) {

        /***************************************************************
         * By : Nasik
         * date : 2017-09-15
         * Load the employee master filters
         ***************************************************************/
        var s_AlphaSearch = window.localStorage.getItem('emp-master-alpha-search');
        var s_SearchKeyword = window.localStorage.getItem('emp-master-searchKeyword');
        var s_Designation = window.localStorage.getItem('emp-master-designation-list');
        var s_Segment = window.localStorage.getItem('emp-master-segment-list');
        var s_Pagination = window.localStorage.getItem('emp-master-pagination');
        var s_Status = window.localStorage.getItem('emp-master-status-list');

        var filterPost = {
            alphaSearch: s_AlphaSearch,
            searchKeyword: s_SearchKeyword,
            designation: s_Designation,
            segment: s_Segment,
            empStatus: s_Status,
            pagination: s_Pagination
        };

        $.ajax({
            async: true,
            type: 'POST',
            url: '<?php echo site_url("dashboard/fetchPage"); ?>',
            dataType: 'html',
            data: {
                'page_id': page_id,
                'page_url': page_url,
                'page_name': page_name,
                'policy_id': policy_id,
                'data_arr': data_arr,
                'master_page_url': master_page_url,
                'filterPost': filterPost
            },
            beforeSend: function () {
                startLoad();
                check_session_status();
            },
            success: function (page_html) {
                stopLoad();
                /***************************************************************
                 * By : Nasik
                 * date : 2017-09-06
                 * to avoid Jquery UI library functions and other styles on
                 * \views\system\hrm\employee_master_new.php
                 ***************************************************************/

                $('.employee_master_styles').attr("disabled", "disabled");

                $('#ajax_body_container').html(page_html);
                $("html, body").animate({scrollTop: "0px"}, 10);
                numberOfAttempt = 0;

            },
            error: function (jqXHR, status, errorThrown) {
                stopLoad();
                $("html, body").animate({scrollTop: "0px"}, 10);
                $('#ajax_body_container').html(jqXHR.responseText + '<br/>Error Message: ' + errorThrown);
                check_session_status();
            }
        });
    }

    function refreshNotifications() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Dashboard/fetch_notifications"); ?>',
            dataType: 'json',
            async: true,
            success: function (data) {
                check_session_status();
                if (!jQuery.isEmptyObject(data)) {
                    toastr.options = {
                        "closeButton": true,
                        "debug": true,
                        "newestOnTop": true,
                        "progressBar": true,
                        "positionClass": "toast-bottom-right animated-panel fadeInRight",
                        "preventDuplicates": true,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    }
                    $.each(data, function (i, v) {
                        toastr[v.t](v.m, v.h);
                    });
                }
                stopLoad();
            },
            error: function () {
                stopLoad();
            }
        });
    }

    function notification(message, status) {
        toastr.options = {
            "positionClass": "toast-bottom-right",
        }

        if (status == undefined) {
            toastr.error(message)
        } else if (status == 's') {
            toastr.success(message);
        } else if (status == 'w') {
            toastr.warning(message);
        } else if (status == 'i') {
            toastr.info(message);
        } else {
            toastr.error(message);
        }
    }

    Number.prototype.formatMoney = function (c, d, t) {
        var n = this,
            c = isNaN(c = Math.abs(c)) ? 2 : c,
            d = d == undefined ? "." : d,
            t = t == undefined ? "," : t,
            s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    };

    function number_validation() {
        $(".number").attr('autocomplete', 'off');
        $(".number").on("onkeyup keyup blur", function (event) {
            $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                if (event.keyCode != 8) {
                    event.preventDefault();
                }
            }
        });
        $(".m_number").attr('autocomplete', 'off');
        $(".m_number").on("onkeyup keyup blur", function (event) {
            $(this).val($(this).val().replace(/[^-0-9\.]/g, ''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                if (event.keyCode != 8) {
                    event.preventDefault();
                }
                ;
            }
        });
    }

    function commaSeparateNumber(val, dPlace = 2) {
        var toFloat = parseFloat(val);
        var a = toFloat.toFixed(dPlace);
        while (/(\d+)(\d{3})/.test(a.toString())) {
            a = a.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
        }
        return a;
    }

    function date_format_change(userdate, policydate) {
        var date_string = moment(userdate, "YYYY-MM-DD").format(policydate);
        return date_string;
    }

    function removeCommaSeparateNumber(val) {
        return parseFloat(val.replace(/,/g, ""));
    }

    function document_uplode() {
        var formData = new FormData($("#attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/do_upload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    attachment_modal($('#documentSystemCode').val(), $('#document_name').val(), $('#documentID').val(), $('#confirmYNadd').val());
                    $('#remove_id').click();
                    $('#attachmentDescription').val('');
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function fetch_approval_user_modal(documentID, documentSystemCode) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'documentID': documentID, 'documentSystemCode': documentSystemCode},
            url: '<?php echo site_url('Approvel_user/fetch_approval_user_modal'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#ap_user_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right"></span> &nbsp; <?php echo $this->lang->line('common_approval_user');?>');
                <!--Approval user-->
                $('#ap_user_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['approved'])) {
                    $('#ap_user_body').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                    <!--No Records Found-->
                } else {
                    $.each(data['approved'], function (key, value) {
                        comment = ' - ';
                        if (value['approvedComments'] !== null) {
                            comment = value['approvedComments'];
                        }
                        bePlanVar = (value['approvedYN'] == true) ? '<span class="label label-success">&nbsp;</span>' : '<span class="label label-danger">&nbsp;</span>';
                        $('#ap_user_body').append('<tr><td>' + x + '</td><td>' + value['Ename2'] + '</td><td class="text-center"> Level ' + value['approvalLevelID'] + '</td><td class="text-center">  ' + value['approveDate'] + '</td><td class="text-center">' + bePlanVar + '</td><td>' + comment + '</td></tr>');
                        x++;
                    });
                }
                $("#ap_user_modal").modal({backdrop: "static", keyboard: true});
                $("#c_document_code").html(data['document_code']);
                $("#c_document_date").html(data['document_date']);
                $("#c_confirmed_date").html(data['confirmed_date']);
                $("#c_conformed_by").html(data['conformed_by']);
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function fetch_all_approval_users_modal(documentID, documentSystemCode) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'documentID': documentID, 'documentSystemCode': documentSystemCode},
            url: '<?php echo site_url('Approvel_user/fetch_all_approval_users_modal'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#ap_user_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right"></span> &nbsp; <?php echo $this->lang->line('footer_approval_user'); ?>');
                <!--Approval user-->
                $('#ap_user_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['approved'])) {
                    $('#ap_user_body').append('<tr class="danger"><td colspan="3" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?></b></td></tr>');
                    <!--No Records Found-->
                } else {
                    $.each(data['approved'], function (key, value) {
                        comment = ' - ';
                        if (value['approvedComments'] !== null) {
                            comment = value['approvedComments'];
                        }
                        approvalDate = ' - ';
                        if (value['approveDate'] !== null) {
                            approvalDate = value['approveDate'];
                        }
                        bePlanVar = (value['approvedYN'] == true) ? '<span class="label label-success">&nbsp;</span>' : '<span class="label label-danger">&nbsp;</span>';
                        $('#ap_user_body').append('<tr><td>' + x + '</td><td>' + value['Ename2'] + '</td><td class="text-center"> Level ' + value['approvalLevelID'] + '</td><td class="text-center">' + approvalDate + '</td><td class="text-center">' + bePlanVar + '</td><td>' + comment + '</td></tr>');
                        x++;
                    });
                }
                $("#ap_user_modal").modal({backdrop: "static", keyboard: true});
                $("#c_document_code").html(data['document_code']);
                $("#c_document_date").html(data['document_date']);
                $("#c_confirmed_date").html(data['confirmed_date']);
                $("#c_conformed_by").html(data['conformed_by']);

                if (documentID == 'LA' && (data.requestForCancelYN !== undefined)) {
                    $('#ap_user_label').append(' - Cancellation');
                }
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function fetch_approval_reject_user_modal(documentID, documentSystemCode) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'documentID': documentID, 'documentSystemCode': documentSystemCode},
            url: '<?php echo site_url('Approvel_user/fetch_reject_user_modal'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                /*                $('#ap_user_label_referback').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right"></span> &nbsp; Referred-back History');*/
                $('#reject_ap_user_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['rejected'])) {
                    $('#reject_ap_user_body').append('<tr class="danger"><td colspan="6" class="text-center"><b>No Records Found</b></td></tr>');
                } else {
                    $.each(data['rejected'], function (key, value) {
                        comment = ' - ';
                        if (value['comment'] !== null) {
                            comment = value['comment'];
                        }
                        bePlanVar = '<span class="label label-danger">&nbsp;</span>';
                        $('#reject_ap_user_body').append('<tr><td>' + x + '</td><td>' + value['ECode'] + ' - ' + value['Ename2'] + '</td><td class="text-center"> Level ' + value['rejectedLevel'] + '</td><td style="text-align: center">' + value['referbackDate'] + '</td><td style="text-align: center">' + bePlanVar + '</td><td>' + comment + '</td></tr>');
                        x++;
                    });
                    $("#c_document_code_rejected").html(data['document_code']);
                }
                $("#reject_drill_user_modal").modal({backdrop: "static", keyboard: true});
                //$("#c_document_date_referback").html(data['referback_date']);
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function approval_refer_back_user_modal(documentID, documentSystemCode) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'documentID': documentID, 'documentSystemCode': documentSystemCode},
            url: '<?php echo site_url('Approvel_user/fetch_approval_referbackuser_user_modal'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#ap_user_label_referback').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right"></span> &nbsp; <?php echo $this->lang->line('footer_referred_back_history');?>');
                <!--Referred-back History-->
                $('#referback_ap_user_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['rejected'])) {
                    $('#referback_ap_user_body').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                    <!--No Records Found-->
                } else {
                    $.each(data['rejected'], function (key, value) {
                        comment = ' - ';
                        if (value['comment'] !== null) {
                            comment = value['comment'];
                        }
                        $('#referback_ap_user_body').append('<tr><td>' + x + '</td><td>' + value['ECode'] + ' - ' + value['Ename2'] + '</td><td class="text-center"> Level ' + value['rejectedLevel'] + '</td><td style="text-align: center">' + value['referbackDate'] + '</td><td>' + comment + '</td></tr>');
                        x++;
                    });
                }
                $("#referback_drill_user_modal").modal({backdrop: "static", keyboard: true});
                $("#c_document_code_referback").html(data['document_code']);
                //$("#c_document_date_referback").html(data['referback_date']);
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function currency_validation_modal(CurrencyID, documentID, partyAutoID, partyType) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'CurrencyID': CurrencyID, 'partyAutoID': partyAutoID, 'partyType': partyType},
            url: '<?php echo site_url('Company/currency_validation'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status'] == true) {
                    var message = 'local currency ( ' + data['data']['default']['masterCurrencyCode'] + ' - ' + data['data']['default']['subCurrencyCode'] + ' ) ' + data['data']['default']['conversion'];
                    message += '<br><?php echo $this->lang->line('footer_reporting_currency');?> ( ' + data['data']['reporting']['masterCurrencyCode'] + ' - ' + data['data']['reporting']['subCurrencyCode'] + ' ) ' + data['data']['reporting']['conversion'];
                    <!--Reporting currency-->
                    if (partyAutoID) {
                        message += '<br><?php echo $this->lang->line('footer_party_currency');?> ( ' + data['data']['party']['masterCurrencyCode'] + ' - ' + data['data']['party']['subCurrencyCode'] + ' ) ' + data['data']['party']['conversion'];
                        <!--Party currency-->
                    }
                    myAlert('i', message, 1000);
                } else {
                    var message = 'local currency ( ' + data['data']['currency'] + ' - ' + data['data']['def'] + ' ) ';
                    message += 'Reporting currency ( ' + data['data']['currency'] + ' - ' + data['data']['rpt'] + ' ) ';
                    if (partyAutoID) {
                        message += 'Party currency ( ' + data['data']['currency'] + ' - ' + data['data']['par'] + ' ) ';
                    }
                    swal({
                            title: "Exchange rates !",
                            text: "<?php echo $this->lang->line('footer_exchange_rates_are_not_set_for_the_selected_currency');?>." + message, /*Exchange rates are not set for the selected currency*/
                            showCancelButton: false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "<?php echo $this->lang->line('common_ok');?>", /*Ok*/
                            closeOnConfirm: true
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                fetchPage('system/erp_dashboard', '', 'Dashboard');
                            }
                        });
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function attachment_modal(documentSystemCode, document_name, documentID, confirmedYN) {
        $('#attachmentDescription').val('');
        $('#documentSystemCode').val(documentSystemCode);
        $('#document_name').val(document_name);
        $('#documentID').val(documentID);
        $('#confirmYNadd').val(confirmedYN);
        $('#remove_id').click();
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID, 'confirmedYN': confirmedYN},
                // beforeSend: function () {
                //     check_session_status();
                //     //startLoad();
                // },
                success: function (data) {
                    $('#attachment_modal_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + "");
                    $('#attachment_modal_body').empty();
                    $('#attachment_modal_body').append('' + data + '');
                    $("#attachment_modal").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function myAlert(type, message, duration=null) {
        toastr.clear();
        initAlertSetup(duration);
        if (type == 'e' || type == 'd') {
            toastr.error(message, '<?php echo $this->lang->line('common_error');?>'/*'Error!'*/);
            check_session_status();
        } else if (type == 's') {
            toastr.success(message, '<?php echo $this->lang->line('common_success');?>'/*'Success!'*/);
        } else if (type == 'w') {
            toastr.warning(message, '<?php echo $this->lang->line('common_warning');?>'/*'Warning!'*/);
        } else if (type == 'i') {
            toastr.info(message, '<?php echo $this->lang->line('common_information');?>'/*'Information'*/);
        } else {
            check_session_status();
            toastr.error('wrong input type! type only allowed for "e","s","w","i"', 'Error!');
        }
    }

    function initAlertSetup(duration=null) {
        duration = ( duration == null ) ? '1000' : duration;
        toastr.options = {
            "closeButton": true,
            "debug": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right animated-panel fadeInTop",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": duration,
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }

    function alerMessage(type, message) {
        // message+='<br /><br /><button type="button" class="btn clear">Yes</button>';

        toastr.options = {
            "closeButton": true,
            "debug": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-center",
            "preventDuplicates": true,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": 0,
            "extendedTimeOut": 0,
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut",
            "tapToDismiss": false
        };
        toastr.clear();
        if (type == 'e') {
            toastr.error(message, '<?php echo $this->lang->line('common_error');?>!');
            /*Error*/
        } else if (type == 's') {
            toastr.success(message, '<?php echo $this->lang->line('common_success');?>');
            /*Success!*/
        } else if (type == 'w') {
            toastr.warning(message, '<?php echo $this->lang->line('common_warning');?>');
            /*Warning!*/
        } else if (type == 'i') {
            toastr.info(message, '<?php echo $this->lang->line('common_information');?>');
            /*Information*/
        } else {
            toastr.error('wrong input type! type only allowed for "e","s","w","i"', 'Error!');
        }
    }

    function myAlert_topPosition(type, message) {
        toastr.options = {
            "closeButton": true,
            "debug": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-center animated-panel fadeInTop",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        toastr.clear();
        if (type == 'e') {
            toastr.error(message, '<?php echo $this->lang->line('common_error');?>!');
            /*Error*/
        } else if (type == 's') {
            toastr.success(message, '<?php echo $this->lang->line('common_success');?>');
            /*Success!*/
        } else if (type == 'w') {
            toastr.warning(message, '<?php echo $this->lang->line('common_warning');?>');
            /*Warning!*/
        } else if (type == 'i') {
            toastr.info(message, '<?php echo $this->lang->line('common_warning');?>');
            /*Information*/
        } else {
            toastr.error('wrong input type! type only allowed for "e","s","w","i"', 'Error!');
        }
    }

    function startLoad() {
        HoldOn.open({
            theme: "sk-rect",//If not given or inexistent theme throws default theme , sk-bounce , sk-cube-grid
            message: "<div style='font-size: 16px; color:#ffffff; margin-top:20px;     text-shadow: 0px 0px 4px black, 0 0 7px #000000, 0 0 3px #000000;'> Loading, Please wait </div><div id='loaderDivContent'></div>",
            content: 'test', // If theme is set to "custom", this property is available
            textColor: "#000000" // Change the font color of the message
        });
    }

    function startLoadPos() {
        $("#posPreLoader").show();
    }

    function stopLoad() {
        HoldOn.close();
        $("#posPreLoader").hide();
    }

    function modalFix() {
        setTimeout(function () {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        }, 500);
    }

    function csrf_init() {
        $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
            if (originalOptions.type == 'POST' || originalOptions.type == 'post' || options.type == 'POST' || options.type == 'post') {
                if (options.processData) { /*options.contentType === 'application/x-www-form-urlencoded; charset=UTF-8'*/
                    options.data = (options.data ? options.data + '&' : '') + $.param({'<?php echo $this->security->get_csrf_token_name(); ?>': CSRFHash});
                } else {
                    options.data.append('<?php echo $this->security->get_csrf_token_name(); ?>', CSRFHash);
                }
            } else {
                if (options.processData) {
                } else {
                }
            }
        });

    }

    $(document).ready(function () {
        $('.modal').on('hidden.bs.modal', function () {
            modalFix()
        });

        $(".select2").select2();

        csrf_init();

        setInterval(function () {
            refresh_session_status();
        }, 3601000);


        var company_id = '<?php echo json_encode($this->common_data['company_data']['company_id']); ?>';
        var company_code = '<?php echo json_encode($this->common_data['company_data']['company_code']); ?>';
        if (company_code == 'null') {
            fetchPage('system/company/erp_company_configuration_new', company_id, 'Add Company', 'COM');
        }

        $('#passwordFormLogin').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                currentPassword: {validators: {notEmpty: {message: '<?php echo $this->lang->line('footer_current_password_is_required');?>.'}}}, /*Current Password is required*/
                newPassword: {validators: {notEmpty: {message: '<?php echo $this->lang->line('footer_new_password_is_required');?>.'}}}, /*New Password is required*/
                confirmPassword: {
                    validators: {
                        identical: {
                            field: 'newPassword',
                            message: '<?php echo $this->lang->line('footer_new_password_and_confirm_password_are_not_matching');?>'/*New Password and Confirm Password are not matching*/
                        },
                        notEmpty: {message: '<?php echo $this->lang->line('footer_confirm_password_are_required');?>.'}/*Confirm Password are required*/
                    }
                }
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Profile/change_password'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    $('#passwordFormLogin')[0].reset();
                    $("#passwordFormLogin").data('bootstrapValidator').resetForm();
                    if (data[0] == 's') {
                        $('#passwordresetModal').modal('hide');
                    }
                    $('#messagelogin').html('');
                    $('#progrssbarlogin').html('<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 0" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>');
                }, error: function (data) {
                    stopLoad();
                    var msg = JSON.parse(data.responseText);
                    myAlert('w', msg[1])
                }
            });
        });
    });

    function getMonthName(monthNumber) {
        var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        return months[monthNumber - 1];
    }

    function set_navbar_cookie() {
        var classVal = $('body').attr('class');
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Dashboard/set_navbar_cookie'); ?>",
            data: {'className': classVal},
            dataType: "json",
            cache: true
        });
    }


    function makeTdAlign(name, side, rowNo) {
        $('#' + name + ' tbody tr').each(function () {
            var thisRow = this;
            $.each(rowNo, function (i, v) {
                $(thisRow).find('td:eq(' + v + ')').css('text-align', side);
            });
        });
    }


    function documentPageView_modal(documentID, para1, para2, approval=0) {
        // added for show attachemnt in same view page - Nazir
        $("#profile-v").removeClass("active");
        $("#home-v").addClass("active");
        $("#TabViewActivation_attachment").removeClass("active");
        $("#tab_itemMasterTabF").removeClass("active");
        $("#TabViewActivation_view").addClass("active");
        attachment_View_modal(documentID, para1);
        // handle with care else i will smack your face - Shahmy
        $('#loaddocumentPageView').html('');
        var siteUrl;
        var paramData = new Array();
        var title = '';
        var a_link;
        var de_link;

        /** switch 2 */
        switch (documentID) {

            case "GRV":
            case "PV":
                $("#itemMasterSubTab_footer_div").html('');
                $(".itemMasterSubTab_footer").show();
                break;

            default:
                $("#itemMasterSubTab_footer_div").html('');
                $(".itemMasterSubTab_footer").hide();
        }

        switch (documentID) {
            case "BT":  // Bank Transfer - Shahmy
                siteUrl = "<?php echo site_url('Bank_rec/bank_transfer_view'); ?>";
                paramData.push({name: 'bankTransferAutoID', value: para1});
                title = "<?php echo $this->lang->line('footer_bank_transfer');?>";
                /*Bank Transfer*/
                break;
            case "PO": // Purchase Order - Shahmy
                siteUrl = "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>";
                paramData.push({name: 'purchaseOrderID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('common_purchase_order');?>";
                /*Purchase Order*/
                break;
            case "EC": // Expense Claim - mushtaq
                siteUrl = "<?php echo site_url('ExpenseClaim/load_expense_claim_conformation'); ?>";
                paramData.push({name: 'expenseClaimMasterAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('common_expense_claim');?>";
                /*Expense Claim*/
                break;
            case "GRV": // Good Receipt Voucher - Shahmy
                if(para2 == 'buy')
                {
                    siteUrl = "<?php echo site_url('Grv/load_grv_conformation_buyback'); ?>";
                    paramData.push({name: 'grvAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_goods_received_voucher');?>";
                    /*Goods Received Voucher*/
                    a_link = "<?php echo site_url('Grv/load_grv_conformation_buyback'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + para1 + '/GRV';
                    load_itemMasterSub('GRV', para1); // item master sub - Sahfri
                    break;
                }else
                {
                    siteUrl = "<?php echo site_url('Grv/load_grv_conformation'); ?>";
                    paramData.push({name: 'grvAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_goods_received_voucher');?>";
                    /*Goods Received Voucher*/
                    a_link = "<?php echo site_url('Grv/load_grv_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + para1 + '/GRV';
                    load_itemMasterSub('GRV', para1); // item master sub - Sahfri
                    break;
                }


            case "SR": // Purchase Return - Shahmy
                siteUrl = "<?php echo site_url('Inventory/load_stock_return_conformation'); ?>";
                paramData.push({name: 'stockReturnAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_purchase_return');?>";
                /*Purchase Return*/
                a_link = "<?php echo site_url('Inventory/load_stock_return_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_stock_return'); ?>/" + para1 + '/SR';
                break;
            case "MI": // Material Issue - Shahmy
                if (para2 == 'mc') {
                    siteUrl = "<?php echo site_url('Inventory/load_material_issue_conformation_mc'); ?>";
                    paramData.push({name: 'itemIssueAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_material_issue');?>";
                    /*Material Issue*/
                    a_link = "<?php echo site_url('Inventory/load_material_issue_conformation_mc'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_material_issue'); ?>/" + para1 + '/MI';
                    break;
                } else {
                    siteUrl = "<?php echo site_url('Inventory/load_material_issue_conformation'); ?>";
                    paramData.push({name: 'itemIssueAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_material_issue');?>";
                    /*Material Issue*/
                    a_link = "<?php echo site_url('Inventory/load_material_issue_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_material_issue'); ?>/" + para1 + '/MI';
                    break;
                }

            case "ST": // Stock Transfer - Shahmy

                if(para2 == 'buy')
                {
                    siteUrl = "<?php echo site_url('Inventory/load_stock_transfer_conformation_buyback'); ?>";
                    paramData.push({name: 'stockTransferAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_stock_transfer');?>";
                    /*Stock Transfer*/
                    a_link = "<?php echo site_url('Inventory/load_stock_transfer_conformation_buyback'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_stock_transfer'); ?>/" + para1 + '/ST';
                    break;
                }else
                {
                    siteUrl = "<?php echo site_url('Inventory/load_stock_transfer_conformation'); ?>";
                    paramData.push({name: 'stockTransferAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_stock_transfer');?>";
                    /*Stock Transfer*/
                    a_link = "<?php echo site_url('Inventory/load_stock_transfer_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_stock_transfer'); ?>/" + para1 + '/ST';
                    break;
                }

            case "SA": // Stock Adjustment - Shahmy

                if(para2 == 'buy')
                {
                    siteUrl = "<?php echo site_url('Inventory/load_stock_adjustment_conformation_buyback'); ?>";
                    paramData.push({name: 'stockAdjustmentAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_stock_adjustment');?>";
                    /*Stock Adjustment*/
                    a_link = "<?php echo site_url('Inventory/load_stock_adjustment_conformation_buyback'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sa'); ?>/" + para1 + '/SA';
                    break;
                }else
                {
                    siteUrl = "<?php echo site_url('Inventory/load_stock_adjustment_conformation'); ?>";
                    paramData.push({name: 'stockAdjustmentAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_stock_adjustment');?>";
                    /*Stock Adjustment*/
                    a_link = "<?php echo site_url('Inventory/load_stock_adjustment_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sa'); ?>/" + para1 + '/SA';
                    break;
                }

            case "BSI": // Supplier Invoices - Shahmy
                siteUrl = "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>";
                paramData.push({name: 'InvoiceAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_supplier_invoices');?>";
                /*Supplier Invoices*/
                a_link = "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_supplier_invoices'); ?>/" + para1 + '/BSI';
                break;
            case "DN": // Debit Note - Shahmy
                siteUrl = "<?php echo site_url('Payable/load_dn_conformation'); ?>";
                paramData.push({name: 'debitNoteMasterAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_debit_note');?>";
                /*Debit Note*/
                a_link = "<?php echo site_url('Payable/load_dn_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_debit_note'); ?>/" + para1 + '/DN';
                break;
            case "PV": // Payment Voucher - Shahmy
                siteUrl = "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>";
                paramData.push({name: 'payVoucherAutoId', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_payment_voucher');?>";
                /*Payment Voucher*/
                a_link = "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + para1 + '/PV';
                load_itemMasterSub('PV', para1); // item master sub - Sahfri
                break;
            case "PVM": // Payment Match - Shahmy
                siteUrl = "<?php echo site_url('Payment_voucher/load_pv_match_conformation'); ?>";
                paramData.push({name: 'matchID', value: para1});
                title = "<?php echo $this->lang->line('footer_payment_voucher');?>";
                /*Payment Voucher*/
                a_link = "<?php echo site_url('Payment_voucher/load_pv_match_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + para1 + '/PV';
                break;
            case "CINV": // Invoice - Shahmy
                siteUrl = "<?php echo site_url('invoices/load_invoices_conformation'); ?>";
                paramData.push({name: 'invoiceAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_invoice');?>";
                /*Invoice*/
                a_link = "<?php echo site_url('invoices/load_invoices_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                break;
            case "HCINV": // Invoice - Shahmy
                siteUrl = "<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback'); ?>";
                paramData.push({name: 'invoiceAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_invoice');?>";
                /*Invoice*/
                a_link = "<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice_buyback'); ?>/" + para1 + '/HCINV';
                break;
            case "CN": // Credit Note - Shahmy
                siteUrl = "<?php echo site_url('Receivable/load_cn_conformation'); ?>";
                paramData.push({name: 'creditNoteMasterAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_credit_note');?>";
                /*Credit Note*/
                a_link = "<?php echo site_url('Receivable/load_cn_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_credit_note'); ?>/" + para1 + '/CN';
                break;
            case "RV": // Receipt Voucher - Shahmy
                siteUrl = "<?php echo site_url('Receipt_voucher/load_rv_conformation'); ?>";
                paramData.push({name: 'receiptVoucherAutoId', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_receipt_voucher');?>";
                /*Receipt Voucher*/
                a_link = "<?php echo site_url('Receipt_voucher/load_rv_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_receipt_voucher'); ?>/" + para1 + '/RV';
                break;
            case "RVM": // Receipt Matching
                siteUrl = "<?php echo site_url('Receipt_voucher/load_rv_match_conformation'); ?>";
                paramData.push({name: 'matchID', value: para1});
                title = "<?php echo $this->lang->line('footer_receipt_matching');?>";
                /*Receipt Matching*/
                break;
            case "JV": // Journal Voucher - Shahmy
                siteUrl = "<?php echo site_url('Journal_entry/journal_entry_conformation'); ?>";
                paramData.push({name: 'JVMasterAutoId', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_journal_entry');?>";
                /*Journal Entry*/
                a_link = "<?php echo site_url('Journal_entry/journal_entry_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_journal_entry'); ?>/" + para1 + '/JV';
                break;
            case "BR": // Bank Rec - Shahmy
                siteUrl = "<?php echo site_url('Bank_rec/bank_rec_book_balance'); ?>";
                paramData.push({name: 'bankRecAutoID', value: para1});
                paramData.push({name: 'GLAutoID', value: para2});
                title = "<?php echo $this->lang->line('footer_bank_reconciliation');?>";
                /*Bank Reconciliation*/
                break;
            case "FA": // Fixed Asset - Shahmy
                siteUrl = "<?php echo site_url('AssetManagement/load_asset_conformation'); ?>";
                paramData.push({name: 'faID', value: para1});
                title = "<?php echo $this->lang->line('footer_fixed_asset');?>";
                /*Fixed Asset*/
                a_link = "<?php echo site_url('AssetManagement/load_asset_conformation'); ?>/" + para1;
                //de_link="<?php echo site_url('Double_entry/fetch_double_entry_credit_note'); ?>/" + para1 + '/FA';
                break;
            case "FAD": // Fixed Asset Depriciation- Nazir
                if (para2 == 'month') {
                    siteUrl = "<?php echo site_url('AssetManagement/load_asset_dipriciation_view'); ?>";
                    paramData.push({name: 'depMasterAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_asset_monthly_depreciation');?>";
                    /*Asset Monthly Depreciation*/
                } else {
                    siteUrl = "<?php echo site_url('AssetManagement/load_asset_dipriciation_adhoc_view'); ?>";
                    paramData.push({name: 'depMasterAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_asset_ad_hoc_depreciation');?>";
                    /*Asset Ad hoc Depreciation*/
                }
                break;
            case "ADSP": // Fixed Asset Disposal- Nazir
                siteUrl = "<?php echo site_url('AssetManagement/load_asset_disposal_view'); ?>";
                paramData.push({name: 'assetdisposalMasterAutoID', value: para1});
                title = "<?php echo $this->lang->line('footer_asset_disposal');?>";
                /*Asset Disposal*/
                break;
            case "SD": // Salary Declaration - Nazir ( Only in salary declaration approval)
                siteUrl = "<?php echo site_url('Employee/load_salary_approval_confirmation'); ?>";
                paramData.push({name: 'declarationMasterID', value: para1}, {name: 'isFromApproval', value: 'Y'});
                title = "<?php echo $this->lang->line('footer_salary_declaration');?>";
                /*Salary Declaration*/
                break;
            case "SD-C": // Salary Declaration - Nasik (in Salary declaration master)
                siteUrl = "<?php echo site_url('Employee/load_salary_approval_confirmation'); ?>";
                paramData.push({name: 'declarationMasterID', value: para1});
                title = "<?php echo $this->lang->line('footer_salary_declaration');?>";
                /*Salary Declaration*/
                break;
            case "CNT": // Contract
                siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                paramData.push({name: 'contractAutoID', value: para1});
                title = "<?php echo $this->lang->line('footer_contract');?>";
                /*Contract*/
                break;
            case "QUT": // Quotation
                siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                paramData.push({name: 'contractAutoID', value: para1});
                title = "<?php echo $this->lang->line('footer_quotation');?>";
                /*Quotation*/
                break;
            case "SO": // Quotation
                siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                paramData.push({name: 'contractAutoID', value: para1});
                title = "<?php echo $this->lang->line('footer_sales_order');?>";
                /*Sales Order*/
                break;
            case "SC": // Sales Commission
                siteUrl = "<?php echo site_url('Sales/load_sc_conformation'); ?>";
                paramData.push({name: 'salesCommisionID', value: para1});
                title = "<?php echo $this->lang->line('footer_sales_commission');?>";
                /*Sales Commission*/
                a_link = "<?php echo site_url('Sales/load_sc_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_SC'); ?>/" + para1 + '/SC';
                break;
            case "FED": // Fixed Element Declaration - Nazir
                siteUrl = "<?php echo site_url('Employee/load_fixed_elementDeclaration_approval_confirmation'); ?>";
                paramData.push({name: 'feDeclarationMasterID', value: para1});
                title = "<?php echo $this->lang->line('footer_fixed_element_declaration');?>";
                /*Fixed Element Declaration*/
                break;
            case "SLR": // Sales Return - Safry
                siteUrl = "<?php echo site_url('Inventory/load_sales_return_conformation'); ?>";
                paramData.push({name: 'salesReturnAutoID', value: para1});
                title = "<?php echo $this->lang->line('footer_sales_return');?>";
                a_link = "<?php echo site_url('Inventory/load_sales_return_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sales_return'); ?>/" + para1 + '/SLR';
                /*Sales Return*/
                break;
            case "PRQ": // Purchase Order - Shahmy
                siteUrl = "<?php echo site_url('PurchaseRequest/load_purchase_request_conformation'); ?>";
                paramData.push({name: 'purchaseRequestID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_purchase_request');?>";
                /*Purchase Request*/
                break;
            case "SPN": // Salary Processing (Non-payroll)- NASIK
                siteUrl = "<?php echo site_url('template_paySheet/templateDetails_view'); ?>";
                paramData.push({'name': 'hidden_payrollID', 'value': para1});
                paramData.push({'name': 'isNonPayroll', 'value': 'Y'});
                paramData.push({'name': 'from_approval', 'value': 'Y'});
                paramData.push({'name': 'isForReverse', 'value': 'Y'});
                title = "<?php echo $this->lang->line('footer_monthly_allowance');?>";
                /*Monthly Allowance*/
                break;
            case "SP": // Salary Processing - NASIK
                siteUrl = "<?php echo site_url('template_paySheet/templateDetails_view'); ?>";
                paramData.push({'name': 'hidden_payrollID', 'value': para1});
                paramData.push({'name': 'isNonPayroll', 'value': 'N'});
                paramData.push({'name': 'from_approval', 'value': 'Y'});
                paramData.push({'name': 'isForReverse', 'value': 'Y'});
                title = "<?php echo $this->lang->line('footer_salary_processing');?>";
                /*Salary Processing*/
                break;
            case "MRN": // Purchase Order - Shahmy
                siteUrl = "<?php echo site_url('MaterialReceiptNote/load_material_receipt_conformation'); ?>";
                paramData.push({name: 'mrnAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_material_receipt_note');?>";
                /*Material Receipt Note*/
                break;
            case "CMT": // Commitment - Shahmy
                siteUrl = "<?php echo site_url('OperationNgo/load_donor_commitment_confirmation'); ?>";
                paramData.push({name: 'commitmentAutoId', value: para1});
                a_link = "<?php echo site_url('OperationNgo/load_donor_commitment_confirmation'); ?>/" + para1;
                title = "<?php echo $this->lang->line('footer_donor_commitment');?>";
                /*Donor Commitment*/
                break;
            case "DC": // collection - Shahmy
                siteUrl = "<?php echo site_url('OperationNgo/load_donor_collection_confirmation'); ?>";
                paramData.push({name: 'collectionAutoId', value: para1});
                a_link = "<?php echo site_url('OperationNgo/load_donor_collection_confirmation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_donor_collection'); ?>/" + para1 + '/DC';
                title = "<?php echo $this->lang->line('footer_donor_collection');?>";
                /*Donor Collection*/
                break;
            case "BBM": // Buy Back Mortality - Nazir
                siteUrl = "<?php echo site_url('buyback/load_mortality_confirmation'); ?>";
                paramData.push({name: 'mortalityAutoID', value: para1});
                title = "<?php echo $this->lang->line('footer_mortality');?>";
                /*Mortality*/
                break;
            case "BBDPN": // Buy Back Dispatch Note - Nazir
                siteUrl = "<?php echo site_url('buyback/load_dispatchNote_confirmation'); ?>";
                paramData.push({name: 'dispatchAutoID', value: para1});
                paramData.push({'name': 'batchid', value: para2});
                title = "<?php echo $this->lang->line('footer_dispatch_note');?>";
                /*Dispatch Note*/
                break;
            case "BBCR": // Buy Back Collection  - Aflal
                siteUrl = "<?php echo site_url('Buyback/load_buyback_collection_confirmation'); ?>";
                paramData.push({name: 'collectionautoid', value: para1});
                title = "Buyback Collection";
                break;
            case "BBGRN": // Buy Back Good Receipt Note - Nazir
                siteUrl = "<?php echo site_url('buyback/load_goodReceiptNote_confirmation'); ?>";
                paramData.push({name: 'grnAutoID', value: para1});
                title = "<?php echo $this->lang->line('footer_good_receipt_note');?>";
                /*Good Receipt Note*/
                break;
            case "EST": // Estimate - Mubashir
                siteUrl = "<?php echo site_url('MFQ_Estimate/fetch_estimate_print'); ?>";
                paramData.push({name: 'estimateMasterID', value: para1});
                title = "<?php echo $this->lang->line('footer_estimate');?>";
                /*Estimate*/
                break;
            case "BBPV": // Buy Back Payment Voucher - Nazir
                siteUrl = "<?php echo site_url('buyback/load_paymentVoucher_confirmation'); ?>";
                paramData.push({name: 'pvMasterAutoID', value: para1});
                title = "<?php echo $this->lang->line('footer_payment_voucher');?>";
                /*Payment Voucher*/
                break;
            case "PRVR": // Payment Reversal - Mushtaq
                siteUrl = "<?php echo site_url('PaymentReversal/load_payment_reversal_conformation'); ?>";
                paramData.push({name: 'paymentReversalAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_payment_reversal');?>";
                /*Payment Reversal*/
                break;
            case "BBBC": // Buy Back Batch Closing - Nazir
                siteUrl = "<?php echo site_url('buyback/load_production_report_confirmation'); ?>";
                paramData.push({name: 'batchMasterID', value: para1});
                title = "<?php echo $this->lang->line('footer_production_statement');?>";
                /*Production Statement*/
                break;
            case "RJV": //Recurring Journal Voucher - Mushtaq
                siteUrl = "<?php echo site_url('Recurring_je/recurring_journal_entry_conformation'); ?>";
                paramData.push({name: 'RJVMasterAutoId', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "Recurring Journal Entry";
                /*Journal Entry*/
                a_link = "<?php echo site_url('Recurring_je/recurring_journal_entry_conformation'); ?>/" + para1;
                break;
            case "BBFVR": // Buy Back Farm Visit Report - Nazir
                siteUrl = "<?php echo site_url('buyback/load_farmVisitReport_confirmation'); ?>";
                paramData.push({name: 'farmerVisitID', value: para1});
                title = "Farm Visit Report";
                break;
            case "MR": // Material Request - Mushtaq
                siteUrl = "<?php echo site_url('Inventory/load_material_request_conformation'); ?>";
                paramData.push({name: 'mrAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "Material Request";
                /**/
                a_link = "<?php echo site_url('Inventory/load_material_request_conformation'); ?>/" + para1;
                break;
            case "CI": // Customer inquiry - Mubashir
                siteUrl = "<?php echo site_url('MFQ_CustomerInquiry/fetch_customer_inquiry_print'); ?>";
                paramData.push({name: 'ciMasterID', value: para1});
                title = "Customer Inquiry";
                break;
            case "YPRP": // Yield Preparation - Aflal
                siteUrl = "<?php echo site_url('POS_yield_preparation/yield_preparation_print'); ?>";
                paramData.push({name: 'yieldPreparationID', value: para1});
                title = "Yield Preparation";
                /****/
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_yield_preparation'); ?>/" + para1 + '/YPRP';
                break;
            case "PRP": // collection - Aflal
                siteUrl = "<?php echo site_url('OperationNgo/load_project_proposal_confirmation'); ?>";
                paramData.push({name: 'proposalID', value: para1});
                title = "Donor Collection";
                /*Prpoposal Approval*/
                a_link = "<?php echo site_url('OperationNgo/load_project_proposal_print_pdf_approval'); ?>/" + para1;
                break;
            case "SCNT": // Stock Counting - Mushtaq
                siteUrl = "<?php echo site_url('StockCounting/load_stock_counting_conformation'); ?>";
                paramData.push({name: 'stockCountingAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "Stock Counting";
                /**/
                a_link = "<?php echo site_url('StockCounting/load_stock_counting_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_scnt'); ?>/" + para1 + '/SCNT';
                break;
            case "BBDR": // Aflal
                siteUrl = "<?php echo site_url('Buyback/load_buyback_return_conformation'); ?>";
                paramData.push({name: 'returnAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "Return";
                a_link = "<?php echo site_url('Buyback/load_buyback_return_conformation'); ?>/" + para1;

                break;
            case "IOU": // Aflal
                siteUrl = "<?php echo site_url('iou/load_iou_voucher_confirmation'); ?>";
                paramData.push({name: 'voucherAutoID', value: para1});
                title = "IOU Voucher";

                break;
            default:
                notification('<?php echo $this->lang->line('footer_document_id_is_not_set');?> .', 'w');
                /*Document ID is not set*/
                return false;
        }
        paramData.push({name: 'html', value: true});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: paramData,
            url: siteUrl,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                $('#documentPageViewTitle').html(title);
                $('#loaddocumentPageView').html(data);
                $('#documentPageView').modal('show');
                $("#a_link").attr("href", a_link);
                $("#de_link").attr("href", de_link);
                $('.review').removeClass('hide');
                stopLoad();

                if (documentID = 'SP') {
                    $('#paysheet-tb').tableHeadFixer({
                        head: true,
                        foot: true,
                        left: 0,
                        right: 0,
                        'z-index': 0
                    });
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function load_itemMasterSub(receivedDocumentID, grvAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                receivedDocumentID: receivedDocumentID,
                grvAutoID: grvAutoID
            },
            url: "<?php echo site_url('Grv/load_itemMasterSub_approval'); ?>",
            beforeSend: function () {
                $("#itemMasterSubTab_footer_div").html('<div  class="text-center" style="margin: 10px 0px;"><i class="fa fa-refresh fa-spin"></i></div>');
            },
            success: function (data) {
                $("#itemMasterSubTab_footer_div").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                $("#itemMasterSubTab_footer_div").html('<br>Message:<br/> ' + errorThrown);
            }
        });
    }

    function change_fetchcompany(companyID, reload) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('footer_you_want_to_load_this_company');?> ! ", /*You want to load this company*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>!", /*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>",
                closeOnCancel: false

            },
            function (isConfirm) {
                if (isConfirm) {
                    var result = companyID.split('-');
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {companyType: result[1], companyID: result[0]},
                        url: "<?php echo site_url('Access_menu/load_navigation'); ?>",
                        success: function (data) {
                            location.reload();

                            stopLoad();

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                } else {
                    swal("Canceled", "", "error");
                    e.preventDefault();
                }

                /**else {

                    /!*Cancelled*!/
                    $('#parentCompanyID').val(<?php //echo json_encode($this->common_data['company_data']['company_id']); ?>);

                }*/
            });
    }

    function fetchcompany(companyID, reload) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {companyID: companyID},
            url: "<?php echo site_url('Access_menu/load_navigation_html'); ?>",
            beforeSend: function () {
                /*startLoad();*/
                $('#ajax_body_container').html('');
            },
            success: function (data) {
                $('.main-sidebar').html(data);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function attachment_View_modal(documentID, documentSystemCode) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentID': documentID, 'documentSystemCode': documentSystemCode},
                success: function (data) {
                    $('#attachment_modal_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + "'s Attachments");
                    $('#View_attachment_modal_body').empty();
                    $('#View_attachment_modal_body').append('' + data + '');
                    //$("#View_attachment_modal_body").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><style>thead th \{font-size: 12px !important;text-align: center;\}thead tr \{ background: #dedede; /* Old browsers */ \} </style></head><body>{headerDiv}<table>{table}</table></body></html>',
            base64 = function (s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            }, format = function (s, c) {
                return s.replace(/{(\w+)}/g, function (m, p) {
                    return c[p];
                })
            }
        return function (table, name, headerDiv) {
            if (!table.nodeType) table = document.getElementById(table)
            headerDiv = (headerDiv != undefined) ? headerDiv : ''; //By Nasik
            var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML, headerDiv: headerDiv}
            var blob = new Blob([format(template, ctx)]);
            var blobURL = window.URL.createObjectURL(blob);
            return blobURL;
        }
    })();

    function delete_attachments(id, fileName) {

        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>?", /*Are you sure*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>!", /*You want to Delete*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>!"/*Yes*/
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'attachmentID': id, 'myFileName': fileName},
                    url: "<?php echo site_url('Attachment/delete_attachments'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', '<?php echo $this->lang->line('common_deleted_successfully');?>');
                            /*Deleted Successfully*/
                            $('#' + id).hide();
                        } else {
                            myAlert('e', '<?php echo $this->lang->line('footer_deletion_failed');?>');
                            /*Deletion Failed*/
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

    function openChangePassowrdModel() {
        $('#passwordresetModal').modal('show');
    }

    function validatepwsStrengthfotr() {
        var passwordComplexityExist = '<?php echo $passwordComplexityExist; ?>';

        if (passwordComplexityExist == 1) {

            var word = $('#newPassword').val();
            var Score = 0;
            var conditions = 0;
            var iscapital = 0;
            var isspecial = 0;
            var lengt = word.length;
            var Capital = word.match(/[A-Z]/);
            var format = /[ !@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;
            if (format.test(word) == true) {
                isspecial = 1
            } else {
                isspecial = 0
            }
            if (jQuery.isEmptyObject(Capital)) {
                iscapital = 0
            } else {
                iscapital = 1
            }
            $('#messagelogin').html('<label class="label label-danger">Weak</label>');
            $('#progrssbarlogin').html('<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>');
            $('#passwordsavebtn').attr('disabled', true);
            var minimumLength = '<?php echo $passwordComplexity['minimumLength'] ?>';
            if (minimumLength <= lengt) {
                conditions = conditions + 1;
                Score = Score + 1;
                $('#messagelogin').html(' ');
                var isCapitalLettersMandatory = '<?php echo $passwordComplexity['isCapitalLettersMandatory'] ?>';
                var isSpecialCharactersMandatory = '<?php echo $passwordComplexity['isSpecialCharactersMandatory'] ?>';

                if (isCapitalLettersMandatory == 1 && isSpecialCharactersMandatory == 1) {
                    conditions = conditions + 2;
                    if (iscapital == 1) {

                        Score = Score + 1;
                    }
                    if (isspecial == 1) {
                        Score = Score + 1;
                    }

                } else if (isCapitalLettersMandatory == 1 && isSpecialCharactersMandatory == 0) {
                    conditions = conditions + 1;
                    if (iscapital == 1) {
                        Score = Score + 1;
                    }

                } else if (isCapitalLettersMandatory == 0 && isSpecialCharactersMandatory == 1) {
                    conditions = conditions + 1;
                    if (isspecial == 1) {
                        Score = Score + 1;
                    }

                } else if (isCapitalLettersMandatory == 0 && isSpecialCharactersMandatory == 0) {

                }
                if (conditions == Score) {
                    $('#passwordsavebtn').attr('disabled', false);
                    $('#progrssbarlogin').html('<div class="progress-bar progress-bar-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>');
                    $('#messagelogin').html('<label class="label label-success">Strong</label>');
                } else if ((conditions % Score) > 0) {
                    $('#passwordsavebtn').attr('disabled', true);
                    $('#progrssbarlogin').html('<div class="progress-bar progress-bar-warning" role="progressbar" style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>');
                    $('#messagelogin').html('<label class="label label-warning">Medium</label>');
                } else {
                    $('#passwordsavebtn').attr('disabled', true);
                    $('#progrssbarlogin').html('<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>');
                    $('#messagelogin').html('<label class="label label-danger">Weak</label>');
                }
            }
        }
        else {
            var word = $('#newPassword').val();
            var lengt = word.length;

            if (lengt < 6) {
                $('#passwordsavebtn').attr('disabled', true);
                $('#progrssbarlogin').html('<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>');
                $('#messagelogin').html('<label class="label label-danger">Weak</label>');
            } else {
                $('#passwordsavebtn').attr('disabled', false);
                $('#progrssbarlogin').html('<div class="progress-bar progress-bar-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>');
                $('#messagelogin').html('<label class="label label-success">Strong</label>');
            }

        }
    }

    function msg_popup(btnClass=null) {
        setTimeout(function () {
            swal({
                html: true,
                title: '',
                text: 'This document contains some employees, That you do not have permission to view their informaiton'
            });
            if (btnClass != null) {
                $('.' + btnClass).hide();
            }
        }, 300);
    }

    function isDateInputMaskNotComplete(dateStr) {
        return (/[dmy]/.test(dateStr))
    }

    function openlanguagemodel() {
        $('#language_select_modal').modal('show');
    }


    function change_emp_language(languageid, reload) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {languageid: languageid},
            url: "<?php echo site_url('Access_menu/update_emp_language'); ?>",
            success: function (data) {
                location.reload();

                stopLoad();

            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });


    }


</script>

<?php if (!isset($noChart)) { ?>
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">

        /*Commented by shafry in local host only */
        var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
        (function () {
            var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/5885c3e02438f53b0a30ed13/default';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();

        Tawk_API.onStatusChange = function (status) {
            if (status === 'online') {
                document.getElementById('chat').innerHTML = '<img src="<?php echo base_url('images/chat2.ico')?>" width="25px" height="25px" class="user-image" alt="User Image"> Online';
                $('#chatUrl').attr('href', 'javascript:void(Tawk_API.toggle())');
            }
            else if (status === 'away') {
                document.getElementById('chat').innerHTML = '<img src="<?php echo base_url('images/chat2.ico')?>" width="25px" height="25px" class="user-image" alt="User Image"> Away';
                $('#chatUrl').attr('href', 'javascript:void(Tawk_API.toggle())');
            }
            else if (status === 'offline') {
                document.getElementById('chat').innerHTML = '<img src="<?php echo base_url('images/chat2.ico')?>" width="25px" height="25px" class="user-image" alt="User Image"> Offline';
                $('#chatUrl').attr('href', 'javascript:void(Tawk_API.toggle())');
            }
        };

        /**window.onbeforeunload = closingCode;
         function closingCode(){
            window.open("<?php echo site_url('Login/logout') ?>");
        }*/

    </script>
<?php } ?>
<!--End of Tawk.to Script-->
</body>
</html>