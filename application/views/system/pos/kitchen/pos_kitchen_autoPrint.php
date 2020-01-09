<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos.css') ?>">

<?php
$this->load->view('include/header', $title);
?>

<div id="orderListContainer">

    <div class="text-center" style="margin:120px 0px;">
        <h3 style="font-family: Arial">Please wait...</h3>
    </div>

</div>

<script type="text/javascript" src="<?php echo base_url('plugins/keyboard-short-cut/shortcut.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/pos/pos.js'); ?>"></script>
<script src="<?php echo base_url('plugins/printJS/jQuery.print.js') ?>" type="text/javascript"></script>
<?php $this->load->view('include/footer', ''); ?>
<script type="text/javascript">
    $(document).ready(function () {
        kitchenPrintQue();
    });

    function printKOTLocation(id) {
        window.location.replace("<?php echo site_url('kot') ?>/" + id.value);
    }

    function kitchenPrintQue() {
        /*refreshOrderListContainer2 | refreshOrderListContainerJava | refreshOrderListContainer_autoPrint*/
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_kitchen/refreshOrderListContainer_autoPrint'); ?>",
            data: {kotID: '<?php echo $locationID ?>'},
            cache: false,
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (data) {
                $("#loader").hide();
                $("#orderListContainer").html(data);
                setTimeout(function () {
                    kitchenPrintQue();
                }, 10000);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }


</script>