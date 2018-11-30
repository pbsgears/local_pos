<?php
$liabilityGL = liabilityGL_drop();
$expenseGL = expenseIncomeGL_drop();
$payableGL = payableGL_drop();
$bankWithCardGL = load_bank_with_card();
$payConData = posPaymentConfig_data();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('posr_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('posr_master_gl_code_configuratio');
echo head_page($title, false);

//echo '<pre>';print_r($payConData); echo '</pre>';
?>

    <style type="text/css">
        .glInputs {
            height: 25px;
            padding: 2px 10px;
            font-size: 12px;
        }

        .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single {
            height: 25px;
            padding: 1px 5px
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 25px !important;
        }
    </style>
    <div id="filter-panel" class="collapse filter-panel"></div>

    <script type="text/javascript">
        function glDropMake(thisCombo, dropType, selectedID=null) {

            var glArray = null;
            switch (dropType) {
                case 1:
                    glArray = JSON.stringify(<?php echo json_encode($payableGL) ?>);
                    break;

                case 2:
                    glArray = JSON.stringify(<?php echo json_encode($bankWithCardGL) ?>);
                    break;

                case 3:
                    glArray = JSON.stringify(<?php echo json_encode($liabilityGL) ?>);
                    break;
                case 4:
                    console.log('as');
                    glArray = JSON.stringify(<?php echo json_encode($expenseGL) ?>);
                    break;
            }

            var row = JSON.parse(glArray);
            var drop = '<option value=""></option>';

            $.each(row, function (i, obj) {
                var selected = ( selectedID == obj.GLAutoID ) ? 'selected' : '';
                drop += '<option value="' + obj.GLAutoID + '" ' + selected + ' data-sys="' + obj.systemAccountCode + '" data-sec="' + obj.GLSecondaryCode + '">';
                drop += obj.GLSecondaryCode + ' | ' + obj.GLDescription + '</option>';
            });

            var thisDropDown = $('#gl_' + thisCombo);
            thisDropDown.append(drop);
            thisDropDown.change();
        }

        function load_glCodes(obj) {
            var systemCode = $(obj).find(':selected').attr('data-sys');
            var secondaryCode = $(obj).find(':selected').attr('data-sec');
            var systemCodeTxt = $(obj).closest('tr').find('td:eq(2) .systemCode');
            var secondaryCodeTxt = $(obj).closest('tr').find('td:eq(3) .secondaryCode');

            systemCodeTxt.val(systemCode);
            secondaryCodeTxt.val(secondaryCode);

            systemCodeTxt.hide();
            systemCodeTxt.fadeIn();

            secondaryCodeTxt.hide();
            secondaryCodeTxt.fadeIn();
        }
    </script>

    <div class="table-responsive">
        <table class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th></th>
                <th style="width: 300px">
                    <?php echo $this->lang->line('posr_master_account_name'); ?><!--Account Name--></th>
                <th><?php echo $this->lang->line('posr_master_system_code'); ?><!--System Code--></th>
                <th><?php echo $this->lang->line('posr_master_secondary_code'); ?><!--Secondary Code--></th>
                <th></th>
            </tr>
            </thead>

            <tbody>
            <?php
            /*echo '<pre>';
            print_r($payConData);
            echo '</pre>';*/
            foreach ($payConData as $row) {
                $selectedID = ($row['GLCode']) ? $row['GLCode'] : 0;
                $save = $this->lang->line('common_save');
                echo '<tr>
                <td style="vertical-align: middle">' . $row['description'] . '</td>
                <td>
                    <select name="GLDescription" id="gl_' . $row['selectBoxName'] . '" class="form-control glInputs select2" onchange="load_glCodes(this)"></select>
                    <script> glDropMake("' . $row['selectBoxName'] . '", ' . $row['glAccountType'] . ', ' . $selectedID . ') </script>
                </td>
                <td> <input type="text" class="form-control glInputs systemCode" name="systemCode" readonly> </td>
                <td> <input type="text" class="form-control glInputs secondaryCode" name="secondaryCode" readonly> </td>
                <td align="center"> <button class="btn btn-primary btn-xs" onclick="posGL_config(\'' . $row['selectBoxName'] . '\', ' . $row['autoID'] . ')">' . $save . '</button> </td>
             </tr>';/*Save*/
            }
            ?>
            </tbody>
        </table>
    </div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>


    <script type="text/javascript">

        $('.select2').select2();

        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/pos/gl_config', 'Test', 'POS');
            });
        });


        function posGL_config(dropDown, autoID) {
            var glAutoID = $('#gl_' + dropDown).val();
            data = [{'name': 'glAutoID', 'value': glAutoID}, {'name': 'paymentTypeID', 'value': autoID}];

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Pos_config/posGL_config'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

    </script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-11-22
 * Time: 5:59 PM
 */