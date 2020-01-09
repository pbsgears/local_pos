<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pdf
{

    function Pdf()
    {
        $CI = &get_instance();
        log_message('Debug', 'mPDF class is loaded.');
    }

    function load($param = NULL)
    {
        include_once(APPPATH . '/third_party/mpdf/mpdf.php');
        if ($param == NULL) {
            $param = '"en-GB-x","A4","","",10,10,10,10,6,3';
        }
        return new mPDF($param);
    }

    function printed($html, $format = 'A4', $Approved = 1, $footer = null, $printHeaderFooterYN = 1)
    {
        $CI = &get_instance();
        include_once(APPPATH . '/third_party/mpdf/mpdf.php');
        $mpdf = new mPDF(
            'utf-8',    // mode - default ''
            $format,    // format - A4, for example, default ''
            '9',       // font size - default 0
            'arial',    // default font family
            5,          // margin_left
            5,          // margin right
            5,          // margin top
            10,          // margin bottom
            0,          // margin header
            3,          // margin footer
            'P'         // L - landscape, P - portrait
        );
        //$mpdf->showImageErrors = true;
        $water_mark_status = policy_water_mark_status('All');
        if ($Approved != 1 and $water_mark_status == 1) {
            $waterMark = '';
            switch ($Approved) {
                case 0;
                    $waterMark = 'Not Approved';
                    break;
                case 2;
                    $waterMark = 'Referred Back';
                    break;
                case 3;
                    $waterMark = 'Rejected';
                    break;
            }
            $mpdf->SetWatermarkText($waterMark);
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.07;
        }
        $user = ucwords($CI->session->userdata('username'));
        //$date = date('l jS \of F Y h:i:s A');
        $date = date('j F Y');
        $stylesheet = file_get_contents('plugins/bootstrap/css/bootstrap.min.css');
        $stylesheet2 = file_get_contents('plugins/bootstrap/css/style.css');
        $stylesheet3 = file_get_contents('plugins/bootstrap/css/print_style.css');
        if ($printHeaderFooterYN==0) {
            $mpdf->SetFooter();
        }
        else if ($footer) {
            $mpdf->SetFooter('Pg : {PAGENO} - '.$footer);
        } else {
            $mpdf->SetFooter('Pg : {PAGENO} - Printed By : ' . $user . '|This is a computer generated document and does not require signature.|' . $date);
        }

        //$mpdf->debug = true;
        //$mpdf->packTableData = true;
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($stylesheet2, 1);
        $mpdf->WriteHTML($stylesheet3, 1);
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output();
        exit;
    }

    function printed_bank_letter($html, $format = 'A4', $Approved = 1)
    {
        $CI = &get_instance();
        include_once(APPPATH . '/third_party/mpdf/mpdf.php');
        $mpdf = new mPDF(
            'utf-8',    // mode - default ''
            $format,    // format - A4, for example, default ''
            '9',       // font size - default 0
            'arial',    // default font family
            25.4,          // margin_left
            25.4,          // margin right
            5,          // margin top
            10,          // margin bottom
            0,          // margin header
            3,          // margin footer
            'P'         // L - landscape, P - portrait
        );
        //$mpdf->showImageErrors = true;
        $water_mark_status = policy_water_mark_status('All');
        if ($Approved != 1 and $water_mark_status == 1) {
            $waterMark = '';
            switch ($Approved) {
                case 0;
                    $waterMark = 'Not Approved';
                    break;
                case 2;
                    $waterMark = 'Referred Back';
                    break;
                case 3;
                    $waterMark = 'Rejected';
                    break;
            }
            $mpdf->SetWatermarkText($waterMark);
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.07;
        }
        $user = ucwords($CI->session->userdata('username'));
        //$date = date('l jS \of F Y h:i:s A');
        $date = date('j F Y');
        $stylesheet = file_get_contents('plugins/bootstrap/css/bootstrap.min.css');
        $stylesheet2 = file_get_contents('plugins/bootstrap/css/style.css');
        $stylesheet3 = file_get_contents('plugins/bootstrap/css/print_style.css');

        $mpdf->SetFooter('');

        //$mpdf->debug = true;
        //$mpdf->packTableData = true;
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($stylesheet2, 1);
        $mpdf->WriteHTML($stylesheet3, 1);
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output();
        exit;
    }

    function printed_pos($html)
    {

        include_once(APPPATH . '/third_party/mpdf/mpdf.php');

        $mpdf = new mPDF(
            'utf-8',    // mode - default ''
            'A8',    // format - A4, for example, default ''
            '11',       // font size - default 0
            'arial',    // default font family
            2,          // margin_left
            2,          // margin right
            0,          // margin top
            2,          // margin bottom
            0,          // margin header
            0,          // margin footer
            'P'         // L - landscape, P - portrait
        );


        $mpdf->WriteHTML($html, 2);

        $url = str_replace('application\libraries', 'uploads', dirname(__FILE__) . '/kot/kot.pdf');
        $mpdf->Output($url, 'F');
        //$mpdf->Output();

    }


    function save_pdf($html, $format = 'A4', $Approved = 1, $path,$footer = null)
    {
        $CI = &get_instance();
        include_once(APPPATH . '/third_party/mpdf/mpdf.php');
        $mpdf = new mPDF();
        $mpdf = new mPDF(
            'utf-8',    // mode - default ''
            $format,    // format - A4, for example, default ''
            '9',       // font size - default 0
            'arial',    // default font family
            5,          // margin_left
            5,          // margin right
            5,          // margin top
            10,          // margin bottom
            0,          // margin header
            3,          // margin footer
            'P'         // L - landscape, P - portrait
        );
        //$mpdf->showImageErrors = true;
        $water_mark_status = policy_water_mark_status('All');
        if ($Approved != 1 and $water_mark_status == 1) {
            $waterMark = '';
            switch ($Approved) {
                case 0;
                    $waterMark = 'Not Approved';
                    break;
                case 2;
                    $waterMark = 'Referred Back';
                    break;
                case 3;
                    $waterMark = 'Rejected';
                    break;
            }
            $mpdf->SetWatermarkText($waterMark);
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.07;
        }
        $user = ucwords($CI->session->userdata('username'));
        //$date = date('l jS \of F Y h:i:s A');
        $date = date('j F Y');
        $stylesheet = file_get_contents('plugins/bootstrap/css/bootstrap.min.css');
        $stylesheet2 = file_get_contents('plugins/bootstrap/css/style.css');
        $stylesheet3 = file_get_contents('plugins/bootstrap/css/print_style.css');

        if ($footer) {
            $mpdf->SetFooter($footer.' |This is a computer generated document and does not require signature.|Pg : {PAGENO} - Printed By : ' . $user);
        } else {
            $mpdf->SetFooter('Pg : {PAGENO} - Printed By : ' . $user . '|This is a computer generated document and does not require signature.|' . $date);
        }
        //$mpdf->debug = true;
        //$mpdf->packTableData = true;
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($stylesheet2, 1);
        $mpdf->WriteHTML($stylesheet3, 1);
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output($path, 'F');
    }

    function printed_mc($html, $format = 'A4', $Approved = 1, $footer = null)
    {
        $CI = &get_instance();
        include_once(APPPATH . '/third_party/mpdf/mpdf.php');

        $mpdf = new mPDF(
            'utf-8',    // mode - default ''
            $format,    // format - A4, for example, default ''
            '9',       // font size - default 0
            'arial',    // default font family
            5,          // margin_left
            5,          // margin right
            5,          // margin top
            10,          // margin bottom
            0,          // margin header
            3,          // margin footer
            'P'         // L - landscape, P - portrait
        );
        //$mpdf->showImageErrors = true;
        $water_mark_status = policy_water_mark_status('All');
        if ($Approved != 1 and $water_mark_status == 1) {
            $waterMark = '';
            switch ($Approved) {
                case 0;
                    $waterMark = 'Not Approved';
                    break;
                case 2;
                    $waterMark = 'Referred Back';
                    break;
                case 3;
                    $waterMark = 'Rejected';
                    break;
            }
            $mpdf->SetWatermarkText($waterMark);
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.07;
        }
        $user = ucwords($CI->session->userdata('username'));
        //$date = date('l jS \of F Y h:i:s A');
        $date = date('j F Y');
        $stylesheet = file_get_contents('plugins/bootstrap/css/bootstrap.min.css');
        $stylesheet2 = file_get_contents('plugins/bootstrap/css/style.css');
        $stylesheet3 = file_get_contents('plugins/bootstrap/css/print_style.css');
        if ($footer) {
            $mpdf->SetFooter();
        } else {
            $mpdf->SetFooter('Pg : {PAGENO} - Printed By : ' . $user . '|This is a computer generated document and does not require signature.|' . $date);
        }

        //$mpdf->debug = true;
        //$mpdf->packTableData = true;
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($stylesheet2, 1);
        $mpdf->WriteHTML($stylesheet3, 1);
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output();
        exit;
    }
}

