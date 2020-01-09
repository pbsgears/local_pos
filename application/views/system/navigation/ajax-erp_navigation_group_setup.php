<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);

    $checked = '';

    ?>
    <ul>
        <?php
        if ($data) {
            $i = 0;
            foreach ($data as $level1) {

                if ($level1['levelNo'] == 0) {
                    $checked = '';
                    if ($level1['navID'] != 0) {
                        $checked = 'checked';
                    }
                    $i++;
                    ?>
                    <li class="header">
                        <input class="nVal" <?php echo $checked ?> type="checkbox" name=tall-<?php echo $i; ?>"
                               id="tall-<?php echo $i; ?>"
                               value="<?php echo $level1['navigationMenuID'] ?>">
                        <lable for="tall<?php echo $i; ?>"> &nbsp;<?php echo $level1['description'] ?></lable>

                        <?php
                        $exist2 = whatever($data, 'levelNo', 1, 'masterID', $level1['navigationMenuID']);
                        if ($exist2) {
                            ?>
                            <ul>
                                <?php
                                if ($data) {
                                    $x = 0;
                                    foreach ($data as $level2) {
                                        $x++;
                                        if ($level2['levelNo'] == 1 && $level2['masterID'] == $level1['navigationMenuID']) {
                                            $checked = '';
                                            if ($level2['navID'] != 0) {
                                                $checked = 'checked';
                                            }
                                            ?>
                                            <li class="subheader">

                                                <input <?php echo $checked ?> class="nVal" type="checkbox"
                                                                              name=tall-<?php echo $i; ?>-<?php echo $x; ?>"
                                                                              id="tall-<?php echo $i; ?>-<?php echo $x; ?>"
                                                                              value="<?php echo $level2['navigationMenuID'] ?>">
                                                <lable for="tall<?php echo $i; ?>-<?php echo $x; ?>">
                                                    &nbsp;<?php echo $level2['description'] ?></lable>

                                                <?php

                                                $exist3 = whatever($data, 'levelNo', 2, 'masterID', $level2['navigationMenuID']);
                                                if ($exist3) {
                                                    ?>
                                                    <ul>
                                                        <?php
                                                        if ($data) {
                                                            $y = 0;
                                                            foreach ($data as $level3) {
                                                                if ($level3['levelNo'] == 2 && $level3['masterID'] == $level2['navigationMenuID']) {
                                                                    $checked = '';
                                                                    if ($level3['navID'] != 0) {
                                                                        $checked = 'checked';
                                                                    }
                                                                    $y++;
                                                                    ?>
                                                                    <li class="subdetails">
                                                                        <input <?php echo $checked ?> class="nVal"
                                                                                                      type="checkbox"
                                                                                                      name=tall-<?php echo $i; ?>-<?php echo $x; ?>-<?php echo $y; ?>"
                                                                                                      id="tall-<?php echo $i; ?>-<?php echo $x; ?>-<?php echo $y; ?>"
                                                                                                      value="<?php echo $level3['navigationMenuID'] ?>">
                                                                        <lable
                                                                            for="tall<?php echo $i; ?>-<?php echo $x; ?>-<?php echo $y; ?>">
                                                                            &nbsp;<?php echo $level3['description'] ?></lable>
                                                                    </li>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </ul>
                                                    <?php
                                                }

                                                ?>

                                            </li>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </ul>
                            <?php
                        }
                        ?>


                    </li>
                    <?php

                }
            }
        }
        ?>
    </ul>
    </hr>
    <button type="submit" onclick="saveNavigationgroupSetup()" class="btn btn-primary pull-right"><?php echo $this->lang->line('common_save_change');?><!--Save changes--></button>
    <!--table-->





<?php

function whatever($array, $key, $val, $key2, $val2)
{
    foreach ($array as $item)
        if ((isset($item[$key]) && $item[$key] == $val) && (isset($item[$key2]) && $item[$key2] == $val2))
            return true;
    return false;
}

?>
<script>
    $('input[type=checkbox]').click(function () {
        if (this.checked) {
            $(this).parents('li').children('input[type=checkbox]').prop('checked', true);
        }
        $(this).parent().find('input[type=checkbox]').prop('checked', this.checked);
    });
    </script>