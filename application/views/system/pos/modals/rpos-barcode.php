<?php
$menus = get_barcode_menus();
if (!empty($menus)) {
    foreach ($menus as $menu) {
        ?>
        <input type="hidden"
               id="barcode_<?php echo str_replace(array('='), array('_'), base64_encode($menu['barcode'])); ?>"
               value="<?php echo $menu['warehouseMenuID'] ?>"
               data-ispack="<?php echo $menu['isPack'] ?>" data-kotid="<?php echo $menu['kotID'] ?>"/>
        <?php
    }
}
?>
<script>

    $(document).ready(function () {
        $("#barcodeInput").keyup(function (e) {
            if (e.keyCode == 13) {
                var inputTmp = $("#barcodeInput").val();
                var barcodeInput = btoa(inputTmp);
                var tmp_convertId = barcodeInput.replace("=", "_");
                var convertId = tmp_convertId.replace("=", "_");

                var id = $("#barcode_" + convertId).val();
                if (id > 0) { checkisKOT(id, 0, 0, ''); }

                focus_barcode();
            }
        });

        focus_barcode();
    });

    function focus_barcode() {
        setTimeout(function () {
            $("#barcodeInput").val('');
            $("#barcodeInput").focus();
        }, 100);
    }
</script>
