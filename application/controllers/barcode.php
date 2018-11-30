<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Barcode extends ERP_Controller
{

    function generateBarcode($code)
    {

        $code = str_replace('-', '/', $code);
        $this->set_barcode($code);
    }

    private function set_barcode($code)
    {
        //load library
        $this->load->library('zend');
        //load in folder Zend
        $this->zend->load('Zend/Barcode');
        //generate barcode
        Zend_Barcode::render('code128', 'image', array('text' => $code), array());
    }
}

