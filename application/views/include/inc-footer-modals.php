<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('profile_my_profile');
$isChangePassword = get_user_isChangePassword();
$passwordComplexityExist = get_password_complexity_exist();
$passwordComplexity = get_password_complexity();
?>
<style>
    .progressbr {
        height: 5px !important;
        margin-bottom: 0 !important;;
    }
</style>
<div class="modal fade" id="changePasswordModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
     role="dialog" aria-labelledby="Chnage Password">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="documentPageViewTitle">Change Password <?php // echo ($isChangePassword) ? 'X' : 'Y' ?></h4>
            </div>
            <div class="modal-body" style="min-height: 200px; margin-top:10px;">
                <div class="row">
                    <div class="col-sm-12">
                        <form class="form-horizontal" method="post" id="passwordForm">
                            <input type="hidden" name="isChangePassword" value="1">
                            <div class="form-group">
                                <label  class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('profile_current_password'); ?><!--Current Password--></label>
                                <div class="col-sm-6">
                                    <input type="password" class="form-control" id="currentPassword_modal"
                                           name="currentPassword"
                                           placeholder="<?php echo $this->lang->line('profile_current_password'); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label  class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('profile_new_password'); ?><!--New Password--></label>
                                <div class="col-sm-6">
                                    <input type="password" onkeyup="validatepwsStrengthfirst()" class="form-control"
                                           id="newPassword_modal"
                                           name="newPassword"
                                           placeholder="<?php echo $this->lang->line('profile_new_password'); ?>">
                                    <div class="progress progressbr" id="progrssbar">

                                    </div>
                                </div>
                                <div class="col-sm-3" id="message"></div>
                            </div>
                            <div class="form-group">
                                <label  class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('profile_confirm_password'); ?><!--Confirm Password--></label>
                                <div class="col-sm-6">
                                    <input type="password" class="form-control" id="confirmPassword_modal"
                                           name="confirmPassword"
                                           placeholder="<?php echo $this->lang->line('profile_confirm_password'); ?>">
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" id="passwordsavebtn_modal" onclick="" class="btn btn-primary">
                                        <?php echo $this->lang->line('common_save_and_confirm'); ?><!--Submit-->
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    $(document).ready(function (e) {
        <?php echo ($isChangePassword) ? '$("#changePasswordModal").modal(\'show\');' : '' ?>


        $('#passwordForm').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                currentPassword_modal: {validators: {notEmpty: {message: 'Current Password is required.'}}},
                newPassword_modal: {validators: {notEmpty: {message: 'New Password is required.'}}},
                confirmPassword_modal: {
                    validators: {
                        identical: {
                            field: 'newPassword',
                            message: 'The password and its confirm are not the same'
                        },
                        notEmpty: {message: 'Confirm Password is required.'}
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
                    $('#passwordForm')[0].reset()
                    $("#passwordForm").data('bootstrapValidator').resetForm()
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if(data[0]=='s'){
                        $("#changePasswordModal").modal('hide')
                    }

                }, error: function (data) {
                    stopLoad();
                    var msg = JSON.parse(data.responseText);
                    myAlert('w', msg[1])
                }
            });
        });
    })

    function validatepwsStrengthfirst() {
        var passwordComplexityExist = '<?php echo $passwordComplexityExist; ?>';

        if (passwordComplexityExist == 1) {

            var word = $('#newPassword_modal').val();
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
            $('#message').html('<label class="label label-danger">Weak</label>');
            $('#progrssbar').html('<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>');
            $('#passwordsavebtn_modal').attr('disabled', true);

            var minimumLength = '<?php echo $passwordComplexity['minimumLength'] ?>';
            if (minimumLength <= lengt) {
                conditions = conditions + 1;
                Score = Score + 1;
                $('#message').html(' ');
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
                    $('#passwordsavebtn_modal').attr('disabled', false);
                    $('#progrssbar').html('<div class="progress-bar progress-bar-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>');
                    $('#message').html('<label class="label label-success">Strong</label>');
                } else if ((conditions % Score) > 0) {
                    $('#passwordsavebtn_modal').attr('disabled', true);
                    $('#progrssbar').html('<div class="progress-bar progress-bar-warning" role="progressbar" style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>');
                    $('#message').html('<label class="label label-warning">Medium</label>');
                } else {
                    $('#passwordsavebtn_modal').attr('disabled', true);
                    $('#progrssbar').html('<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>');
                    $('#message').html('<label class="label label-danger">Weak</label>');
                }
            }
        }else {
            var word = $('#newPassword_modal').val();
            var lengt = word.length;

            if (lengt < 6) {
                $('#passwordsavebtn_modal').attr('disabled', true);
                $('#progrssbar').html('<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>');
                $('#message').html('<label class="label label-danger">Weak</label>');
            } else {
                $('#passwordsavebtn_modal').attr('disabled', false);
                $('#progrssbar').html('<div class="progress-bar progress-bar-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>');
                $('#message').html('<label class="label label-success">Strong</label>');
            }

        }
    }
</script>