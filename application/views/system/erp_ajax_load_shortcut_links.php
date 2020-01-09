<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div class="box box-success">
    <div class="box-header with-border">
        <h4 class="box-title"><?php echo $this->lang->line('dashboard_external_links');?><!--External Links--></h4>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                    class="fa fa-minus"></i>
            </button>
            <button type="button" onclick="openPrivateLinkModal<?php echo $userDashboardID; ?>()" title="Add Links" class="btn btn-box-tool"><i
                    class="fa fa-plus-square-o"></i>
            </button>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="display: block;width: 100%">
        <ul class="todo-list">

            <?php
            if (!empty($shortcutlinks)) {
                foreach ($shortcutlinks as $val) {
                    ?>
                    <li id="exLink_<?php echo $val['linkID'] ?>_<?php echo $userDashboardID; ?>" style="padding: 6px;">
                  <span class="">
                    <i class="fa fa-globe"></i>
                  </span>
                <span class="text"><a href="<?php echo $val['hyperlink'] ?>" title="<?php echo $val['title'] ?>"
                                      target="<?php echo $val['target'] ?>"><?php echo $val['description'] ?></a></span>
                        <div class="tools">
                            <!--<i class="fa fa-edit"></i>-->
                            <i class="fa fa-trash-o" onclick="deletePrivateLink<?php echo $userDashboardID; ?>(<?php echo $val['linkID'] ?>,<?php echo $userDashboardID; ?>)"></i>
                        </div>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
    </div>
    <div class="overlay" id="overlay8<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
    <!-- /.box-body -->
</div>

<div class="modal fade" id="addPrivateLinkModal<?php echo $userDashboardID; ?>" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('dashboard_add_link');?><!--Add Link--></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <?php echo form_open('', 'role="form" id="private_link_form'.$userDashboardID.'"'); ?>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label for="description"><?php echo $this->lang->line('dashboard_external_link_name');?><!--Link Name--></label>
                        <input type="text" class="form-control" id="description" name="description"
                               placeholder="<?php echo $this->lang->line('dashboard_external_link_name');?>"><!--Link Name-->
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label for="Link"><?php echo $this->lang->line('dashboard_external_link');?><!--Link--></label>
                        <input type="text" class="form-control" id="hyperlink" name="hyperlink"
                               placeholder="<?php echo $this->lang->line('dashboard_external_link');?>"><!--Link-->
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" onclick="save_private_link<?php echo $userDashboardID; ?>()" id="btnSave"><?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
            </div>
        </div>
    </div>
</div>

<script>

    function openPrivateLinkModal<?php echo $userDashboardID; ?>() {
        //$('#private_link_form')[0].reset();
        $('#private_link_form<?php echo $userDashboardID; ?>').bootstrapValidator('resetForm', true);
        $('#addPrivateLinkModal<?php echo $userDashboardID; ?>').modal("show");
    }

    function save_private_link<?php echo $userDashboardID; ?>() {
        var data = $('#private_link_form<?php echo $userDashboardID; ?>').serializeArray();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Finance_dashboard/save_private_link'); ?>",
            data: data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#addPrivateLinkModal<?php echo $userDashboardID; ?>').modal('hide');
                    myAlert('s', 'Message: ' + data[1]);
                    location.reload();
                } else if (data[0] == 'e') {
                    myAlert('e', 'Message: ' + data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Message: ' + "Select Widget");
            }
        });

    }

    function deletePrivateLink<?php echo $userDashboardID; ?>(id,userDashboardID) {
        if (id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this Record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: "<?php echo site_url('Finance_dashboard/deletePrivateLink'); ?>",
                        data: {linkID: id},
                        cache: false,
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data[0] == 's') {
                                $('#exLink_' + id+'_'+userDashboardID).hide();
                                myAlert('s', 'Message: ' + data[1]);
                            } else if (data[0] == 'e') {
                                myAlert('e', 'Message: ' + data[1]);
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', 'Message: ' + "Select Widget");
                        }
                    });
                });
        };
    }


</script>
