<?php
// =============================================
// -  File Name : Tax_modal.php
// -  Project Name : MERP
// -  Module Name : Tax_modal
// -  Author : Nuski Mohamed
// -  Create date : 11 - September 2016
// -  Description : This file contains the add function for tax.

// - REVISION HISTORY
// - Date: 5-November 2016 By: Nuski Description: Added a new function named as save_tax_header()
// - Date: 2-November 2016 By: Nuski Description: changed the function to add multiple items with different location in fetch_supplier_data(),delete_tax(),laad_tax_header()
// -  =============================================

class Tax_modal extends ERP_Model{

    function save_tax_header(){
        $this->db->trans_start();
        $supplier_arr           = $this->fetch_authority_data(trim($this->input->post('supplierID')));
        $liability = fetch_gl_account_desc(trim($this->input->post('supplierGLAutoID')));

        $date_format_policy = date_format_policy();
        $expClaimDate = trim($this->input->post('effectiveFrom'));
        $effectiveFrom = input_format_date($expClaimDate, $date_format_policy);
        $supplierCurrency=fetch_currency_code($supplier_arr['currencyID']);
        $supplierCurrencyDecimalPlaces=fetch_currency_desimal($supplierCurrency);

        $data['taxDescription']                     = trim($this->input->post('taxDescription'));
        $data['taxShortCode']                  		= trim($this->input->post('taxShortCode'));
        $data['taxType']                			= trim($this->input->post('taxType'));
        $data['supplierAutoID']                     = trim($this->input->post('supplierID'));
        $data['isActive']                       	= trim($this->input->post('isActive'));
        $data['effectiveFrom']                      = $effectiveFrom;
        $data['taxReferenceNo']                     = $this->input->post('taxReferenceNo');
        $data['isApplicableforTotal']               = trim($this->input->post('isApplicableforTotal'));
        $data['taxPercentage']                      = trim($this->input->post('taxPercentage'));
        $data['supplierSystemCode']                 = $supplier_arr['authoritySystemCode'];
        $data['supplierName']                       = $supplier_arr['AuthorityName'];
        $data['supplierAddress']                    = $supplier_arr['address'];
        $data['supplierTelephone']                  = $supplier_arr['telephone'];
        $data['supplierFax']                        = $supplier_arr['fax'];
        $data['supplierEmail']                      = $supplier_arr['email'];
        $data['supplierGLAutoID']            		= trim($this->input->post('supplierGLAutoID'));
        $data['supplierGLSystemGLCode']      		= $liability['systemAccountCode'];
        $data['supplierGLAccount']         			= $liability['masterAccount'];
        $data['supplierGLDescription']       		= $liability['GLDescription'];
        $data['supplierGLType']              		= $liability['subCategory'];
        $data['supplierCurrencyID']                 = $supplier_arr['currencyID'];
        $data['supplierCurrency']                   = $supplierCurrency;
        $data['supplierCurrencyDecimalPlaces']      = $supplierCurrencyDecimalPlaces;
        $data['modifiedPCID']                       = $this->common_data['current_pc'];
        $data['modifiedUserID']                     = $this->common_data['current_userID'];
        $data['modifiedUserName']                   = $this->common_data['current_user'];
        $data['modifiedDateTime']                   = $this->common_data['current_date'];

        if (trim($this->input->post('taxMasterAutoID'))) {
            $this->db->where('taxMasterAutoID', trim($this->input->post('taxMasterAutoID')));
            $this->db->update('srp_erp_taxmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Tax for : ('.$data['supplierSystemCode'].' ) '. $data['supplierName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Tax for : ('.$data['supplierSystemCode'].' ) '. $data['supplierName']. ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('TaxAutoID'));
            }
        } else {
            $data['companyID']                          = $this->common_data['company_data']['company_id'];
            $data['companyCode']                        = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup']                   = $this->common_data['user_group'];
            $data['createdPCID']                        = $this->common_data['current_pc'];
            $data['createdUserID']                      = $this->common_data['current_userID'];
            $data['createdUserName']                    = $this->common_data['current_user'];
            $data['createdDateTime']                    = $this->common_data['current_date'];
            $this->db->insert('srp_erp_taxmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Tax for : ('.$data['supplierSystemCode'].' ) '. $data['supplierName'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Tax for : ('.$data['supplierSystemCode'].' ) '. $data['supplierName'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }   
    }

    function fetch_supplier_data($supplierID){
        $this->db->select('*');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID); 
        return $this->db->get()->row_array();
    }

    function laad_tax_header(){
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(effectiveFrom,\'' . $convertFormat . '\') AS effectiveFrom');
        $this->db->from('srp_erp_taxmaster');
        $this->db->where('taxMasterAutoID', trim($this->input->post('taxMasterAutoID'))); 
        return $this->db->get()->row_array();
    }

    function delete_tax(){
        $this->db->delete('srp_erp_taxmaster', array('taxMasterAutoID' => trim($this->input->post('taxMasterAutoID'))));
        $this->db->delete('srp_erp_taxapplicableitems', array('taxMasterAutoID' => trim($this->input->post('taxMasterAutoID'))));
        $this->session->set_flashdata('e', 'Tax Deleted : '. $this->input->post('value') . ' Successfully');
        return true;
    }

    function save_tax_group_header(){
        $this->db->trans_start();
        $data['taxType']                            = trim($this->input->post('taxDescription'));
        $data['taxType']                            = trim($this->input->post('taxgroup'));
        $data['Description']                        = trim($this->input->post('taxdescription'));

        if (trim($this->input->post('taxGroupID_Edit'))) {
            $data['modifiedPCID']                       = $this->common_data['current_pc'];
            $data['modifiedUserID']                     = $this->common_data['current_userID'];
            $data['modifiedUserName']                   = $this->common_data['current_user'];
            $data['modifiedDateTime']                   = $this->common_data['current_date'];
            $this->db->where('taxGroupID', trim($this->input->post('taxGroupID_Edit')));
            $this->db->update('srp_erp_taxgroup', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Tax Group Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Tax Group Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('TaxAutoID'));
            }
        } else {
            $data['companyID']                          = $this->common_data['company_data']['company_id'];
            $data['companyCode']                        = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup']                   = $this->common_data['user_group'];
            $data['createdPCID']                        = $this->common_data['current_pc'];
            $data['createdUserID']                      = $this->common_data['current_userID'];
            $data['createdUserName']                    = $this->common_data['current_user'];
            $data['createdDateTime']                    = $this->common_data['current_date'];
            $this->db->insert('srp_erp_taxgroup', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Tax Group Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Tax Group Created successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function get_tax_group_edit()
    {
        $this->db->select('*');
        $this->db->where('taxGroupID', $this->input->post('id'));
        return $this->db->get('srp_erp_taxgroup')->row_array();
    }

    function changesupplierGLAutoID(){
        $this->db->select('taxPayableGLAutoID');
        $this->db->from('srp_erp_taxauthorithymaster');
        $this->db->where('taxAuthourityMasterID', trim($this->input->post('supplierID')));
        return $this->db->get()->row_array();
    }

    function fetch_authority_data($taxAuthourityMasterID){
        $this->db->select('*');
        $this->db->from('srp_erp_taxauthorithymaster');
        $this->db->where('taxAuthourityMasterID', $taxAuthourityMasterID);
        return $this->db->get()->row_array();
    }

    function get_tax_type($taxType){
        $qry = "SELECT
  taxMasterAutoID,
	taxShortCode,
	taxType,
	IF(taxType = 1,'Sales tax','Purchase tax') as taxTyp
FROM
	srp_erp_taxmaster
WHERE
	companyID = ".current_companyID()."
AND taxMasterAutoID IN (".join(',',$taxType).") ORDER BY  taxType DESC";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function get_tax_details($taxType,$datefrom,$dateto){
        $date_format_policy = date_format_policy();
        $fromdate = input_format_date($datefrom, $date_format_policy);
        $date_format_policy = date_format_policy();
        $todate = input_format_date($dateto, $date_format_policy);
        $local = '';
        $reporting = '';
        foreach($taxType as $tex){
            $local .= 'SUM(if(ledger.taxMasterAutoID = '.$tex.',ledger.companyLocalAmount,0)) as L_'.$tex.',';
            $reporting .= 'SUM(if(ledger.taxMasterAutoID = '.$tex.',ledger.companyReportingAmount,0)) as R_'.$tex.',';
        }

        $qry = "SELECT
   gl.*, sum(ifnull(cinvD.companyLocalAmount,0)) - sum(ifnull(cinvD.totalAfterTax,0) / ifnull(cinvm.companyLocalExchangeRate,1)) AS cinvlocal,
    sum(ifnull(cinvD.companyReportingAmount,0)) - sum(ifnull(cinvD.totalAfterTax,0) / ifnull(cinvm.companyReportingExchangeRate,1)) AS cinvReporting,
    sum(ifnull(rvd.companyLocalAmount,0)) AS rvlocal,
    sum(ifnull(rvd.companyReportingAmount,0)) AS rvreporting,
    sum(ifnull(bsid.companyLocalAmount,0)) AS bsilocal,
    sum(ifnull(bsid.companyReportingAmount,0)) AS bsireporting,
    sum(ifnull(pvd.companyLocalAmount,0)) AS pvlocal,
    sum(ifnull(pvd.companyReportingAmount,0)) AS pvReporting,
  ((sum(ifnull(cinvD.companyLocalAmount,0)) - sum(ifnull(cinvD.totalAfterTax,0) / ifnull(cinvm.companyLocalExchangeRate,1)) )+
  (sum(ifnull(rvd.companyLocalAmount,0)))+
    (sum(ifnull(bsid.companyLocalAmount,0)))+
    (sum(ifnull(pvd.companyLocalAmount,0)))
    ) AS totalgrossofTaxLocal,
((sum(ifnull(cinvD.companyReportingAmount,0)) - sum(ifnull(cinvD.totalAfterTax,0) / ifnull(cinvm.companyReportingExchangeRate,1)))+
    (sum(ifnull(rvd.companyReportingAmount,0)))+
    (sum(ifnull(bsid.companyReportingAmount,0)))+
    (sum(ifnull(pvd.companyReportingAmount,0)))) as totalGrossofTaxReporting,
    srp_erp_taxmaster.taxType as taxType,
    case WHEN gl.documentCode='PV' THEN pvm.PVNarration
   WHEN gl.documentcode='CINV' THEN cinvm.invoiceNarration
   WHEN gl.documentcode='rv' THEN rv.RVNarration
else bsi.comments END as narration,
case when gl.documentCode='BSI' THEN bsiSupplier.supplierName
     when gl.documentCode='CINV' THEN cinvCustomer.customername
     when gl.documentCode='PV' AND pvmSupplier.supplierName is not null THEN pvmSupplier.supplierName
     when gl.documentcode='RV' AND rvCustomer.customerName is not null  THEN rvCustomer.customerName
     when gl.documentCode='PV' AND pvmSupplier.supplierName is null THEN pvm.partyName
     when gl.documentcode='RV' AND rvCustomer.customerName is null  THEN rv.customerName
Else null END as SupplierName
FROM
    (
        SELECT
            $local
            $reporting
            ledger.documentCode,
            ledger.documentMasterAutoID,
            ledger.documentSystemCode,
            ledger.documentDate,
            ledger.partyVatIdNo,ledger.taxMasterAutoID,
            ledger.companyLocalCurrencyDecimalPlaces,
      ledger.companyReportingCurrencyDecimalPlaces
        FROM
            srp_erp_generalledger ledger
        WHERE
            ledger.companyID = ".current_companyID()."
        AND ledger.taxMasterAutoID IS NOT NULL
        AND ledger.documentcode IN ('CINV', 'RV', 'PV', 'BSI')
        AND (
            ledger.Documentdate BETWEEN '$fromdate'
            AND '$todate'
        )
        GROUP BY
            ledger.documentMasterAutoID,ledger.documentCode
    ) gl
LEFT JOIN srp_erp_customerinvoicedetails cinvD ON cinvD.invoiceAutoID = gl.documentMasterAutoID
AND gl.documentCode = 'CINV'
LEFT JOIN srp_erp_customerinvoicemaster cinvm ON cinvm.invoiceAutoID = cinvD.invoiceAutoID
LEFT JOIN (select * from srp_erp_customermaster where companyID=".current_companyID().")cinvCustomer on cinvCustomer.customerAutoID=cinvm.customerID
LEFT JOIN srp_erp_paymentvoucherdetail pvd ON pvd.payVoucherAutoId = gl.documentMasterAutoID
AND gl.documentCode = 'PV'
LEFT JOIN srp_erp_paymentvouchermaster pvm on pvd.payVoucherAutoId=pvm.payVoucherAutoId
LEFT JOIN (select * from srp_erp_suppliermaster where companyID=".current_companyID().") pvmSupplier on pvmSupplier.supplierAutoID=pvm.partyID
LEFT JOIN srp_erp_paysupplierinvoicedetail bsid ON bsid.InvoiceAutoID = gl.documentMasterAutoID
AND gl.documentCode = 'BSI'
LEFT JOIN srp_erp_paysupplierinvoicemaster bsi on bsid.InvoiceAutoID=bsi.InvoiceAutoID
LEFT JOIN (select * from srp_erp_suppliermaster where companyID=".current_companyID().") bsiSupplier on bsiSupplier.supplierAutoID=bsi.supplierID
LEFT JOIN srp_erp_customerreceiptdetail rvd ON rvd.receiptVoucherAutoId = gl.documentMasterAutoID
AND gl.documentCode = 'RV'
LEFT JOIN srp_erp_customerreceiptmaster rv on rvd.receiptVoucherAutoId=rv.receiptVoucherAutoId
LEFT JOIN (select * from srp_erp_customermaster where companyID=".current_companyID().")rvCustomer on rvCustomer.customerAutoID=rv.customerID
LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID=gl.taxMasterAutoID
where gl.taxMasterAutoID IN (".join(',',$taxType).")
group by gl.documentCode,gl.documentMasterAutoID";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }
}