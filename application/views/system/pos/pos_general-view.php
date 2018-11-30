<?php
$data['template'] = 'general';
$this->load->view('system/pos/pos_restaurant-view1', $data);
?>
<script>
    $(document).ready(function (e) {
        $("#pos_btnSet_dtd").hide();
        $("#btn_kitchenModal").hide();
        $("#btn_pos_sendtokitchen").hide()
        $("#deliveryCommissionDiv").hide()
    });
</script>
