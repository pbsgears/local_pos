
<?php if(isset($emp)){ ?>
<label style="width: 100px"
       for="">Employee </label> <?php echo form_dropdown('empID[]', all_not_accessed_employee($companyID), '', 'class="form-control" multiple="multiple"  id="empID" required"'); ?>
<?php } ?>
<?php if(isset($group)){ ?>
    <label style="width: 100px" for="">User Group </label> <?php echo form_dropdown('userGroup', dropdown_erp_usergroups($companyID), '', 'class="" onchange="" id="userGroup" style="width:150px" required"'); ?>
<?php } ?>
<?php if(isset($groupID)){ ?>
    <label style="" for="">User Group </label>  <?php echo form_dropdown('userGroupID', dropdown_erp_usergroups($companyID), '', 'onchange="loadform();" class="" id="userGroupID" style="width:100px" required'); ?>
<?php } ?>


<script>
    $('#userGroup').select2();
    $('#userGroupID').select2();
    $('#empID').multiselect2({
        enableFiltering: true,
       /* filterBehavior: 'value',*/
        includeSelectAllOption: true,
        enableCaseInsensitiveFiltering: true,
        maxHeight: 200
    });
    </script>
