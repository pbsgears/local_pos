<table class="table table-bordered" id="policyDT">
    <thead>
    <tr>
        <th> Code</th>
        <th> Description</th>
        <th>
            Status
        </th>

    </tr>
    </thead>
    <tbody>
    <?php
    $i = 0;

    if (isset($masters) && !empty($masters)) {
        foreach ($masters as $master) {
            $i++;
            ?>
            <tr>
                <td> <?php echo $master['policyCode'] ?></td>
                <td> <?php echo $master['policyDescription'] ?></td>
                <td>

                    <?php
                    if (!empty($master['posPolicyID'])) {
                        $check = "checked";
                    } else {
                        $check = '';
                    }
                    ?>

                    <div class="checkbox checkbox-slider--b-flat">
                        <label>
                            <input onchange="changePolicy(this,<?php echo $master['posPolicyMasterID'] ?>)"
                                   type="checkbox" <?php echo $check ?>><span>&nbsp;</span>
                        </label>
                    </div>


                </td>
            </tr>
        <?php }
    } ?>
    </tbody>
</table>
