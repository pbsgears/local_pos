<?php
$this->load->helper('cookie');
$this->load->view('include/header',$title);
$this->load->view('include/top',$title);
$this->load->view('include/navigation',$title);
$this->load->view($main_content,$extra);
$this->load->view('include/footer');
