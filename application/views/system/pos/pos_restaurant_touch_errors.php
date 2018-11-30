<?php
$title = 'Error';
$header['extra'] = '';
$header['title'] = $title;
$this->load->view('include/header', $header);

?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/buttons/button.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos-tab.css') ?>">

<?php
$this->load->view('include/top', $title);
$this->load->view('include/navigation', $title);
?>
    <div class="alert">
        <div class="alert alert-danger">
            <strong>Error!</strong> <br/><?php echo isset($error_message) ? $error_message : '' ?>
        </div>
    </div>

    <script type="text/javascript" src="<?php echo base_url('plugins/pos/r-pos.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('plugins/pos/pos-tab.js'); ?>"></script>
<?php
$this->load->view('include/footer');
?>