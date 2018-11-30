

<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>

<table class="<?php echo table_class_pos(1) ?>" id="packConfigTbl2">
    <thead>
    <tr>
        <th>#</th>
        <th><?php echo $this->lang->line('pos_config_item_name');?><!--Item Name--></th>
        <th><?php echo $this->lang->line('common_group');?><!--Group--></th>
        <th style="width:75px;"><?php echo $this->lang->line('pos_config_is_optional');?><!--is Optional--></th>
        <th style="width:75px;"><?php echo $this->lang->line('pos_config_is_active');?><!--is Active--></th>
        <th style="max-width:75px;">&nbsp;</th>
    </tr>
    </thead>

    <tbody>
    <?php
    if (isset($list) && !empty($list)) {


        $i = 1;
        foreach ($list as $value) {

            ?>
            <tr id="packGroup_<?php echo $value['menuPackItemID'] ?>" <?php if ($value['isRequired'] == 1) {
            ?>style="background-color: #f0eab1"<?php
            } ?> >
                <td>
                    <?php echo $i;
                    $i++; ?>
                </td>
                <td><?php echo $value['menuMasterDescription'] ?></td>
                <td>
                    <?php echo $value['GroupName'] ?>
                </td>
                <td><?php if ($value['isRequired'] == 0) {
                        echo 'Yes';
                    } else {
                        echo 'No';
                    } ?></td>
                <td style="text-align: center;">
                    <input id="groupItem_check_<?php echo $value['packgroupdetailID'] ?>"
                           onclick="changeStatus(<?php echo $value['packgroupdetailID'] ?>)"
                           type="checkbox" <?php if ($value['isActive'] == 1) {
                        echo ' checked ';
                    } ?> >
                </td>
                <td style="text-align: center;">
                    <button class="btn btn-danger btn-xs"
                            onclick="delete_pos_packConfigItem(<?php echo $value['menuPackItemID'] ?>,<?php echo !empty($value['packgroupdetailID']) ? $value['packgroupdetailID'] : 0; ?>)"
                            rel="tooltip">
                        <i class="fa fa-trash-o"></i></button>
                </td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>
<script>

    function changeStatus(id) {
        var status = $("#groupItem_check_" + id).is(":checked");
        if (status) {
            var checked = 1;
        } else {
            var checked = 0;
        }

        $.ajax({
            type: "GET",
            url: "<?php echo site_url('Pos_config/update_group_item_status') ?>?id=" + id + '&status=' + checked,
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
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
    $("#packConfigTbl2").dataTable();
</script>