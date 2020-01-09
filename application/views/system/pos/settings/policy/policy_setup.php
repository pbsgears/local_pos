<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$title = 'Policy Setup';
echo head_page($title, false);

?>
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
    <style>
        .container_output {
            min-height: 200px;
            text-align: center;
            font-size: 24px;
            color: gray;
            border: 1px dashed gray;
            border-radius: 4px;
            padding-top: 80px;
        }

        .checkbox-slider--b-flat {
            position: relative;
        }

        .checkbox-slider--b-flat input {
            display: block;
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            width: 0%;
            height: 0%;
            margin: 0 0;
            cursor: pointer;
            opacity: 0;
            filter: alpha(opacity=0);
        }

        .checkbox-slider--b-flat input + span {
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .checkbox-slider--b-flat input + span:before {
            position: absolute;
            left: 0px;
            display: inline-block;
        }

        .checkbox-slider--b-flat input + span > h4 {
            display: inline;
        }

        .checkbox-slider--b-flat input + span {
            padding-left: 40px;
        }

        .checkbox-slider--b-flat input + span:before {
            content: "";
            height: 20px;
            width: 40px;
            background: rgba(100, 100, 100, 0.2);
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.8);
            transition: background 0.2s ease-out;
        }

        .checkbox-slider--b-flat input + span:after {
            width: 20px;
            height: 20px;
            position: absolute;
            left: 0px;
            top: 0;
            display: block;
            background: #ffffff;
            transition: margin-left 0.1s ease-in-out;
            text-align: center;
            font-weight: bold;
            content: "";
        }

        .checkbox-slider--b-flat input:checked + span:after {
            margin-left: 20px;
            content: "";
        }

        .checkbox-slider--b-flat input:checked + span:before {
            transition: background 0.2s ease-in;
        }

        .checkbox-slider--b-flat input + span {
            padding-left: 40px;
        }

        .checkbox-slider--b-flat input + span:before {
            border-radius: 20px;
            width: 40px;
        }

        .checkbox-slider--b-flat input + span:after {
            background: #ffffff;
            content: "";
            width: 20px;
            border: solid transparent 2px;
            background-clip: padding-box;
            border-radius: 20px;
        }

        .checkbox-slider--b-flat input:not(:checked) + span:after {
            -webkit-animation: popOut ease-in 0.3s normal;
            animation: popOut ease-in 0.3s normal;
        }

        .checkbox-slider--b-flat input:checked + span:after {
            content: "";
            margin-left: 20px;
            border: solid transparent 2px;
            background-clip: padding-box;
            -webkit-animation: popIn ease-in 0.3s normal;
            animation: popIn ease-in 0.3s normal;
        }

        .checkbox-slider--b-flat input:checked + span:before {
            background: #5cb85c;
        }

        .checkbox-slider--b-flat.checkbox-slider-md input + span:before {
            border-radius: 30px;
        }

        .checkbox-slider--b-flat.checkbox-slider-md input + span:after {
            border-radius: 30px;
        }

        .checkbox-slider--b-flat.checkbox-slider-lg input + span:before {
            border-radius: 40px;
        }

        .checkbox-slider--b-flat.checkbox-slider-lg input + span:after {
            border-radius: 40px;
        }

        .checkbox-slider--b-flat input + span:before {
            box-shadow: none;
        }

        /*#####*/
        .checkbox-slider-info.checkbox-slider--b input:checked + span:before,
        .checkbox-slider-info.checkbox-slider--b-flat input:checked + span:before,
        .checkbox-slider-info.checkbox-slider--c input:checked + span:before,
        .checkbox-slider-info.checkbox-slider--c-weight input:checked + span:before {
            background: #5bc0de;
        }

        .checkbox-slider-warning.checkbox-slider--b input:checked + span:before,
        .checkbox-slider-warning.checkbox-slider--b-flat input:checked + span:before,
        .checkbox-slider-warning.checkbox-slider--c input:checked + span:before,
        .checkbox-slider-warning.checkbox-slider--c-weight input:checked + span:before {
            background: #f0ad4e;
        }

        .checkbox-slider-danger.checkbox-slider--b input:checked + span:before,
        .checkbox-slider-danger.checkbox-slider--b-flat input:checked + span:before,
        .checkbox-slider-danger.checkbox-slider--c input:checked + span:before,
        .checkbox-slider-danger.checkbox-slider--c-weight input:checked + span:before {
            background: #d9534f;
        }

        /*******************************************************
        Sizes
        *******************************************************/
        .checkbox-slider-sm {
            line-height: 10px;
        }

        .checkbox-slider-sm input + span {
            padding-left: 20px;
        }

        .checkbox-slider-sm input + span:before {
            width: 20px;
        }

        .checkbox-slider-sm input + span:after,
        .checkbox-slider-sm input + span:before {
            height: 10px;
            line-height: 10px;
        }

        .checkbox-slider-sm input + span:after {
            width: 10px;
            vertical-align: middle;
        }

        .checkbox-slider-sm input:checked + span:after {
            margin-left: 10px;
        }

        .checkbox-slider-md {
            line-height: 30px;
        }

        .checkbox-slider-md input + span {
            padding-left: 60px;
        }

        .checkbox-slider-md input + span:before {
            width: 60px;
        }

        .checkbox-slider-md input + span:after,
        .checkbox-slider-md input + span:before {
            height: 30px;
            line-height: 30px;
        }

        .checkbox-slider-md input + span:after {
            width: 30px;
            vertical-align: middle;
        }

        .checkbox-slider-md input:checked + span:after {
            margin-left: 30px;
        }

        .checkbox-slider-lg {
            line-height: 40px;
        }

        .checkbox-slider-lg input + span {
            padding-left: 80px;
        }

        .checkbox-slider-lg input + span:before {
            width: 80px;
        }

        .checkbox-slider-lg input + span:after,
        .checkbox-slider-lg input + span:before {
            height: 40px;
            line-height: 40px;
        }

        .checkbox-slider-lg input + span:after {
            width: 40px;
            vertical-align: middle;
        }

        .checkbox-slider-lg input:checked + span:after {
            margin-left: 40px;
        }

        #policyDT tbody > tr > td {
            font-size: 12px !important;
        }
    </style>
<?php
$outlets = get_active_outletInfo();
?>
    <form action="" class="form-horizontal">
        <div class="form-group">
            <label class="col-sm-2 control-label">Outlet <i class="fa fa-building" aria-hidden="true"></i> </label>
            <div class="col-sm-6">
                <select class="form-control select2" id="currentOutlet" onchange="loadPolicyValues(this)">
                    <option></option>
                    <?php

                    if (!empty($outlets)) {
                        foreach ($outlets as $outlet) {
                            ?>
                            <option
                                value="<?php echo $outlet['wareHouseAutoID'] ?>"><?php echo $outlet['wareHouseDescription'] . '  ' . $outlet['wareHouseCode'] ?></option>

                        <?php }
                    } ?>
                </select>
            </div>
        </div>

    </form>
    <hr>
    <div class="row">
    <div class="col-md-12" id="policy_output">
        <div class="container_output">
            Please select the outlet. <i class="fa fa-building" aria-hidden="true"></i>
        </div>
    </div>

    <script type="text/javascript"
            src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
    <script>
        $(document).ready(function (e) {
            $(".select2").select2({placeholder: 'please select'});
        })


        function loadPolicyValues(tmpThisVal) {
            var selected_outlet = $(tmpThisVal).val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {outletID: selected_outlet},
                url: "<?php echo site_url('Pos_policy_controller/loadPolicyValues_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#policy_output").html(data);
                    $("#policyDT").dataTable();
                }, error: function () {
                    stopLoad();
                }
            });

        }

        function changePolicy(tmpThis, posPolicyMasterID) {
            if ($(tmpThis).is(':checked')) {
                var checked = 1;
            } else {
                var checked = 0;
            }
            var currentOutlet = $("#currentOutlet").val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {outletID: currentOutlet, status: checked, posPolicyMasterID: posPolicyMasterID},
                url: "<?php echo site_url('Pos_policy_controller/change_policy'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                }, error: function () {
                    stopLoad();
                }
            });

        }
    </script>

<?php echo footer_page('Right foot', 'Left foot', false); ?>