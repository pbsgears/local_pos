<?php

class ExcelExport extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function export_excel()
    {
        $this->load->library('excel');
        //set cell A1 content with some text
       // $this->excel->getActiveSheet()->setCellValueByColumnAndRow('A1',1,'test');
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Users list');
        // load database
        $this->load->database();
        // load model
        // get all users in array formate
        $users = all_employees();
        // Header
        $this->excel->getActiveSheet()->fromArray(array_keys(current($users)), null, 'A1');
        // Data
        $this->excel->getActiveSheet()->fromArray($users, null, 'A2');
        //set aligment to center for that merged cell (A1 to D1)
        $filename = 'test.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }


}