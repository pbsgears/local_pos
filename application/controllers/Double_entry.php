<?php

class Double_entry extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Double_entry_model');
    }

    function fetch_double_entry_grv()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_grv_data($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 0);
        }
    }

    function fetch_double_entry_sa()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_sa_data($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 0);
        }
    }

    function fetch_double_entry_stock_return()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_stock_return_data($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 0);
        }
    }

    function fetch_double_material_issue()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_material_issue_data($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 0);
        }
    }

    function fetch_double_stock_transfer()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_stock_transfer_data($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 0);
        }
    }

    function fetch_double_entry_supplier_invoices()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_supplier_invoices_data($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['approved_YN']);
        }
    }

    function fetch_double_entry_debit_note()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_debit_note_data($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4',$data['extra']['approved_YN']);
        }
    }

    function fetch_double_entry_payment_voucher()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_payment_voucher_data($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4',$data['extra']['approved_YN']);
        }
    }

    function fetch_double_entry_journal_entry()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_journal_entry_data($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 0);
        }
    }

    function fetch_double_entry_credit_note()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_credit_note_data($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4',$data['extra']['approved_YN']);
        }
    }

    function fetch_double_entry_receipt_voucher()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_receipt_voucher_data($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['approved_YN']);
        }
    }

    function fetch_double_entry_customer_invoice()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));

        $this->db->select('tempInvoiceID,invoiceType');
        $this->db->where('invoiceAutoID', $masterID);
        $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();
        if(!empty($master['tempInvoiceID'])){
            $data['extra'] = $this->Double_entry_model->fetch_double_entry_customer_invoice_temp_data($masterID, $code);
        }else{
            if($master['invoiceType'] == 'Manufacturing') {
                $data['extra'] = $this->Double_entry_model->fetch_double_entry_mfq_customer_invoice_data($masterID, $code);
            }else{
                $data['extra'] = $this->Double_entry_model->fetch_double_entry_customer_invoice_data($masterID, $code);
            }
        }
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['approved_yn']);
        }
    }

    function fetch_double_entry_asset_master()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));

        $data['extra'] = $this->Double_entry_model->fetch_double_entry_asset_master($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_asset_print', $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['approved']);
        }
    }

    function fetch_double_entry_asset_depreciation_master()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));

        $data['extra'] = $this->Double_entry_model->fetch_double_entry_asset_depreciation_master($masterID, $code);

        $html = $this->load->view('system/double_entry/erp_double_entry_asset_print', $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['approved']);
        }
    }

    function fetch_double_entry_asset_disposal()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_asset_disposal($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_asset_print', $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['approved']);
        }
    }

    function fetch_double_entry_SC()
    {
        $salesCommisionID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('salesCommisionID'));
        $salesCommisionCode = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('salesCommisionCode'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_SC($salesCommisionID, $salesCommisionCode);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['approved_yn']);
        }
    }

    function fetch_double_entry_sales_return()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_sales_return_data($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print2', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4',$data['extra']['approved_yn']);
        }
    }

    function fetch_double_material_receipt()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_material_receipt_data($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 0);
        }
    }
    function fetch_double_entry_donor_collection()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_donor_collection($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 0);
        }
    }

    function fetch_double_entry_payment_voucher_prvr()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_payment_voucher_prvr($masterID, $code);
       /*print_r($data);
        exit;*/
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 0);
        }
    }

    function fetch_double_entry_payment_voucher_rrvr()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_payment_voucher_rrvr($masterID, $code);
        /*print_r($data);
         exit;*/
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 0);
        }
    }

    function fetch_double_entry_yield_preparation()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_yield_preparation($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_yield_preparation', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 1);
        }
    }


    function fetch_double_entry_scnt()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_scnt_data($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 0);
        }
    }

    function fetch_double_entry_customer_invoice_buyback()
    {
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID'));
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code'));
        $data['extra'] = $this->Double_entry_model->fetch_double_entry_customer_invoice_buyback_data($masterID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 0);
        }
    }
}