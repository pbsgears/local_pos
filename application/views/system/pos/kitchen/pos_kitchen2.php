<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos.css') ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/slick/slick/slick.css') ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/slick/slick/slick-theme.css') ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<style>
    .completedList li {
        font-size: 15px;
        /*list-style: square;*/
        margin: 3px;
        min-height: 10px;
    }

    .title {
        background-color: #050500;
        color: #e5de14;
        padding: 5px;
        border-radius: 2px;
        margin: 0px;

    }

    table > tr {
        border: 1px dashed black !important;
    }

    .detailList {
        margin-left: 10px;
        font-size: 18px;
        /*font-weight: 700;*/
        font-family: Calibri;
    }

    .detailList img {
        height: 25px !important;
        width: 25px !important;

    }

    .btnSwitchCustom {
        /*padding: 2px;
        background-color: #ffffff;
        margin: -5px -4px 0px 0px;*/
    }
</style>
<?php
$this->load->view('include/header', $title);
//$this->load->view('include/top');

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div class="row">
    <div class="col-md-12">
        <div id="orderListContainer">
            <!--Via Ajax -->
        </div>
    </div>
</div>
<?php
$data['notFixed'] = true;
$this->load->view('include/footer', $data);
?>
<script type="text/javascript" src="<?php echo base_url('plugins/keyboard-short-cut/shortcut.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/pos/pos.js'); ?>"></script>
<script src="<?php echo base_url('plugins/slick/slick/slick.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('plugins/printJS/jQuery.print.js') ?>" type="text/javascript"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        kitchenQue();
        $(".mySwitch").bootstrapSwitch();


    });

    function changeKOTLocation(id) {
        window.location.replace("<?php echo site_url('kitchen') ?>/" + id.value);
    }

    /*NOT USING THIS*/
    function updateToCurrent(menuSalesID, tmpVal) {
        var valueTmp = $(tmpVal).is(":checked");
        if (valueTmp) {
            var cook = 1;
        } else {
            var cook = 0;
        }

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_kitchen/updateToCurrent'); ?>",
            data: {menuSalesID: menuSalesID, value: cook, kotID: '<?php echo $locationID ?>'},
            cache: false,
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (data) {
                $("#loader").hide();
                if (data['error'] == 0) {
                    refreshOrderListContainer()
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function updateToCompleted(menuSalesID, tmpVal) {
        var valueTmp = $(tmpVal).is(":checked");
        if (valueTmp) {
            var cook = 1;
        } else {
            var cook = 0;
        }

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Pos_kitchen/updateToCompleted'); ?>",
            data: {menuSalesID: menuSalesID, value: cook, kotID: '<?php echo $locationID ?>'},
            cache: false,
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (data) {
                $("#loader").hide();
                if (data['error'] == 0) {
                    refreshOrderListContainer()
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function kitchenQue() {
        /*refreshOrderListContainer2 | refreshOrderListContainerJava*/
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_kitchen/refreshOrderListContainer2'); ?>",
            data: {kotID: '<?php echo $locationID ?>'},
            cache: false,
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (data) {
                $("#loader").hide();
                $("#orderListContainer").html(data);
                setTimeout(function () {
                    kitchenQue();
                }, 10000);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function refreshOrderListContainer() {
        /*refreshOrderListContainer2 | refreshOrderListContainerJava*/
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Pos_kitchen/refreshOrderListContainer2'); ?>",
            data: {kotID: '<?php echo $locationID ?>'},
            cache: false,
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (data) {
                $("#loader").hide();
                $("#orderListContainer").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

</script>