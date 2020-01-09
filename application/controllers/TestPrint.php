<?php
/**
 *
 * -- =============================================
 * -- File Name : TestPrint.php
 * -- Project Name : POS
 * -- Module Name : POS Config
 * -- Author : Mohamed Mubashir
 * -- Create date : 25 October 2017
 * -- Description : Yield preparation
 *
 * --REVISION HISTORY
 * --Date:
 * -- =============================================
 **/
defined('BASEPATH') OR exit('No direct script access allowed');

class TestPrint extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        //$this->load->library('ReceiptPrint');
        $this->load->library('CloudPrints');
    }

    function printReceipt(){
        try {
            //$this->receiptprint->connect('192.168.1.51', 9100);
            //$this->receiptprint->connect('smb://MUBASHIR/');
            $this->receiptprint->connect('BIXOLONS');
            $this->receiptprint->print_test_receipt('Hello World!');
        } catch (Exception $e) {
            echo "Error: Could not print. Message ".$e->getMessage();
            $this->receiptprint->close_after_exception();
        }
    }

    function printReceiptCloud(){
        try {
            $this->load->library('PrintCloud');
            //$this->printcloud->printReceipt("1a3147b8-c30f-e8bb-0612-abd3bb2cd3aa","Reyaascloudprint-ce0d52395b57.p12","reyaasprintnew@reyaascloudprint.iam.gserviceaccount.com");
            $this->printcloud->printReceipt("e2d576d7f2999e28a3c0f19fd7139dd262b4a77b");
        } catch (Exception $e) {
            echo "Error: Could not print. Message ".$e->getMessage();
        }
    }

    function printReceiptCloud2($hrml='<b>'){
        try {
            $printer = $this->cloudprints->getPrinters();
            $printer = $this->cloudprints->printCloudReceipt("284c3a86-9d59-2939-3520-e0e393c9bd46",$hrml);
        } catch (Exception $e) {
            echo "Error: Could not print. Message ".$e->getMessage();
        }
    }


    function printReceiptDll(){
        try {
            /* Basic test */
            $handle = printer_open();
            printer_write($handle, "Text to print");
            printer_close($handle);
            exit;
        } catch (Exception $e) {
            echo "Error: Could not print. Message ".$e->getMessage();
        }
    }

}