<?php
$data['invoiceList'] = $invoiceList;
$data['masters'] = $masters;
$data['closedBill'] = isset($closedBill) ? true : false;

$template = get_print_template();
$this->load->view($template, $data);

//$this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer', $data);
exit;
