<?php
include_once(APPPATH . '/third_party/cloudprint2/GoogleCloudPrint.php');

class CloudPrints
{
    private $googleCloudPrint;
    private $ci;

    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->library('session');
        $this->googleCloudPrint = new GoogleCloudPrint();
    }

    function getPrinters()
    {
        $this->googleCloudPrint->setAuthToken($this->ci->session->userdata('accessToken'));
        $printers = $this->googleCloudPrint->getPrinters();
        if (count($printers) == 0) {
            echo json_encode(array('error' => 1, 'message' => "Could not get printer."));
        } else {
            echo json_encode(array('error' => 0, 'printer' => $printers));
        }
    }

    function getToken($access_token_url, $authConfig)
    {
        $responseObj = $this->googleCloudPrint->getAccessToken($access_token_url, $authConfig);
        return $responseObj;
    }

    function printCloudReceipt($printerID, $content)
    {
        $this->googleCloudPrint->setAuthToken($this->ci->session->userdata('accessToken'));
        $printer_id = $printerID; // Pass id of any printer to be used for print
        // Send document to the printer
        $res_array = $this->googleCloudPrint->sendPrintToPrinter($printer_id, "Kot Print", "", "text/html", $content);
        if ($res_array['status'] == true) {
            echo json_encode(array('error' => 0, 'message' => "Document has been sent to printer and should print shortly."));
        } else {
            echo json_encode(array('error' => 1, 'message' => "An error occurred while printing the doc. Error code:" . $res_array['errorcode'] . " Message:" . $res_array['errormessage']));
        }
    }

    function printCloudReceipt_pdf($printerID, $content)
    {
        $this->googleCloudPrint->setAuthToken($this->ci->session->userdata('accessToken'));
        $printer_id = $printerID; // Pass id of any printer to be used for print
        // Send document to the printer
        $res_array = $this->googleCloudPrint->sendPrintToPrinter($printer_id, "Kot Print", "", "application/pdf", $content);
        if ($res_array['status'] == true) {
            echo json_encode(array('error' => 0, 'message' => "Document has been sent to printer and should print shortly.", ''));
        } else {
            echo json_encode(array('error' => 1, 'message' => "An error occurred while printing the doc. Error code:" . $res_array['errorcode'] . " Message:" . $res_array['errormessage']));
        }
    }
}