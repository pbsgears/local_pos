<?php
$data['invoiceList'] = $invoiceList;
$data['masters'] = $masters;
$data['email'] = true;
$this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer', $data);
