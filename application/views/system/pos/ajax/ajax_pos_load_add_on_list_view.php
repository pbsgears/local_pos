
<form id="addonlistfrm" name="addonlistfrm" method="post">
    <input type="hidden" id="menuSalesItemIDaddon" name="menuSalesItemIDaddon" value="<?php echo $menuSalesItemID ?>">
<?php
foreach ($adonlist as $val) {
    $id = $val['menuMasterID'];
    $addedAddOn = $this->db->query("SELECT * from srp_erp_pos_addon WHERE menuSalesItemID='$menuSalesItemID' AND menuMasterID='$id';")->row_array();
    if($addedAddOn){
        echo '
          <div class="row" style="margin-bottom: 0.5%;">
          <div class="col-sm-12">
          <label>
              <input type="checkbox" value="' . $id . '" name="addonCheck[]" class="minimal"  checked >
              ' . $val['menuMasterDescription'] . '
            </label>
        </div></div>';
    }else{
        echo '
          <div class="row" style="margin-bottom: 0.5%;">
          <div class="col-sm-12">
          <label>
              <input type="checkbox" value="' . $id . '" name="addonCheck[]" class="minimal" >
              ' . $val['menuMasterDescription'] . '
            </label>
        </div></div>';
    }

}
?>
    <hr>
    <input type="hidden" id="itmID" name="itmID">
    <input type="hidden" id="invoiceIDMenusales" name="invoiceIDMenusales">
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group ">
                <label for="deliveryTerms">Remakes</label>
                                <textarea class="form-control" id="menuItemRemarkes" name="menuItemRemarkes"
                                          rows="3" style="width: 100%;"></textarea>
            </div>
        </div>
    </div>
</form>

<script>
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });


</script>
