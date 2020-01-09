<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*

============================================================

-- File Name : Pos_general_report_model.php
-- Project Name : POS
-- Module Name : POS General Report model
-- Author : Mohamed Shafri
-- Create date : 29 - june 2018
-- Description : Report General POS.

--REVISION HISTORY
--Date: 29 - June 2018 By: Mohamed Shafri: comment started

============================================================

*/

class Pos_general_report_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function get_report_paymentMethod_admin($date, $data2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND invoice.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                    configMaster.description AS paymentDescription,
                    SUM( payments.amount ) AS NetTotal,
                    count( payments.invoiceID ) AS countTransaction 
                FROM
                    srp_erp_pos_invoicepayments AS payments

                  LEFT JOIN srp_erp_pos_invoice AS invoice ON payments.invoiceID = invoice.invoiceID
	              LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = payments.paymentConfigMasterID 
                WHERE
                invoice.companyID = '" . current_companyID() . "'
                AND invoice.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . "
                AND configMaster.autoID <> 7
                GROUP BY
                    payments.paymentConfigMasterID ";
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_report_credit_sales_admin($date, $data2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND invoice.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND wareHouseAutoID IN(" . $outlets . ")";
        }

        /*Sum( srp_erp_pos_menusalespayments.amount ) AS salesAmount,
                count(srp_erp_pos_menusalespayments.menuSalesPaymentID) as countCreditSales,
                srp_erp_pos_customermaster.CustomerName,
                srp_erp_pos_customermaster.CustomerAutoID */
        $q = "SELECT
                    configMaster.description AS paymentDescription,
                    SUM( payments.amount ) AS salesAmount,
                    count( payments.invoiceID ) AS countCreditSales,
                    customer.customerName as CustomerName
                FROM
                    srp_erp_pos_invoicepayments AS payments

                  LEFT JOIN srp_erp_pos_invoice AS invoice ON payments.invoiceID = invoice.invoiceID
	              LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = payments.paymentConfigMasterID 
	              LEFT JOIN srp_erp_customermaster AS  customer ON customer.customerAutoID = payments.customerAutoID
                WHERE
                invoice.companyID = '" . current_companyID() . "'
                AND invoice.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . "
                AND configMaster.autoID = 7
                GROUP BY
                   	payments.customerAutoID";
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        //print_r($result);
        return $result;
    }

    function get_report_lessAmount_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                    salesMaster.deliveryCommission,
                    customers.customerName,
                    SUM(netTotal) AS netTotal,
                    SUM(deliveryCommissionAmount) AS lessAmount

                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = salesMaster.deliveryPersonID
                LEFT JOIN srp_erp_pos_paymentglconfigmaster payments ON  payments.autoID = salesMaster.paymentMethod
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
                AND NOT ISNULL(
                    salesMaster.deliveryPersonID
                )
                AND salesMaster.deliveryPersonID <> 0
                AND payments.autoID = 1
                " . $qString . "
                " . $outletFilter . "
                GROUP BY
                    customers.customerName
                ORDER BY
                    salesMaster.isPromotion";

        echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_lessAmount_promotion_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND wareHouseAutoID IN(" . $outlets . ")";
        }


        $q = "SELECT
                    salesMaster.promotionDiscount AS deliveryCommission,
                    customers.customerName AS customerName,
                    SUM(grossTotal) AS netTotal,
                    SUM(IFNULL(promotionDiscountAmount,0) ) as lessAmount
              
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = salesMaster.promotionID
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
                AND NOT ISNULL(salesMaster.promotionID)
                AND salesMaster.promotionID <> 0
                " . $qString . "
                " . $outletFilter . "
                GROUP BY
                    customers.customerName
                ORDER BY
                    salesMaster.isPromotion";


        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_discount_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND invoice.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
               'General Discount' AS customerName,
                SUM( IFNULL(netTotal,0) ) AS netTotal,
                SUM( IFNULL(invoice.generalDiscountAmount,0)  ) AS lessAmount
            FROM
               srp_erp_pos_invoice AS invoice 
            WHERE
                  invoice.companyID =  '" . current_companyID() . "'
            AND invoice.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
             " . $qString . $outletFilter;


        //echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_general_discount_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND invoice.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
               'Item Wise Discount' AS customerName,
                SUM( netTotal ) AS netTotal,
                SUM( invoice.discountAmount ) AS lessAmount
            FROM
               srp_erp_pos_invoice AS invoice 
            WHERE
                  invoice.companyID =  '" . current_companyID() . "'
            AND invoice.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
             " . $qString . $outletFilter;


        // echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_discount_item_wise_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                    salesMaster.promotionDiscount AS deliveryCommission,
                    'Item Wise Discount' AS customerName,
                    SUM( grossTotal ) AS netTotal,
                    SUM( IFNULL( salesitem.discountAmount, 0 ) ) AS lessAmount 
              
                FROM
                    srp_erp_pos_menusalesitems AS salesitem
	LEFT JOIN srp_erp_pos_menusalesmaster salesMaster ON salesMaster.menuSalesID = salesitem.menuSalesID
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
                " . $qString . "
                " . $outletFilter . " ";


        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_javaAppDiscount_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                'Discounts' AS customerName,
            SUM(netTotal) AS netTotal,
            SUM(salesMaster.discountAmount) AS lessAmount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID =  '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
            AND salesMaster.discountAmount>0 " . $qString . $outletFilter;


        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_currentCompanyDetail()
    {
        $this->db->select("*");
        $this->db->from("srp_erp_company");
        $this->db->where('company_id', current_companyID());
        $result = $this->db->get()->row_array();
        return $result;
    }


    function get_item_wise_profitability_Report($dateFrom, $dateTo, $Outlets = null, $cashier = null)
    {

        $outletID = $this->input->post('outletID');

        if ($outletID > 0) {
            $where_tmp[] = " (invoice.wareHouseAutoID = '" . $outletID . "') ";
        }

        if ($Outlets != null) {
            $where_tmp[] = " (wareHouseAutoID IN(" . $Outlets . ")) ";
        }

        if ($cashier != null) {
            $where_tmp[] = " (id.createdUserID IN(" . $cashier . ")) ";
        }

        $where_tmp[] = " ( id.createdDateTime  BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "' )";

        $where = join('AND', $where_tmp);

        $q = "SELECT
                id.itemSystemCode,
                id.itemDescription,
                id.itemCategory,
                sum( IFNULL( id.qty, 0 ) ) qtySum,
                sum( IFNULL( id.transactionAmount, 0 ) ) AS transactionAmountSum,
              	ifnull(sum((id.wacAmount * id.qty)),0) AS wacAmountSum,
              (sum( IFNULL( id.transactionAmount, 0 ) ) - ( ifnull( sum( ( id.wacAmount * id.qty ) ), 0 ) ) ) AS profit
            FROM
                srp_erp_pos_invoicedetail id
	            LEFT JOIN srp_erp_pos_invoice invoice ON invoice.invoiceID = id.invoiceID
	            
                WHERE " . $where . " 
                GROUP BY id.itemSystemCode";

        /*echo $q;*/



        $result = $this->db->query($q)->result_array();
        return $result;
    }


    function get_gpos_detail_sales_report($date, $data2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND invoice.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                   invoice.*
                FROM
                    srp_erp_pos_invoice AS invoice
                WHERE
                invoice.companyID = '" . current_companyID() . "'
                AND invoice.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter."
                ORDER BY invoiceID DESC
                ";
        $result = $this->db->query($q)->result_array();
        return $result;
    }

    /** -------------------------------------------------------------------------------------------------------------------------------- */

    function get_report_customerTypeCount_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                    customertype.customerDescription,
                    count(salesMaster.netTotal) AS countTotal,
                    sum(subTotal) as subTotal
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster
                LEFT JOIN srp_erp_customertypemaster customertype ON customertype.customerTypeID = salesMaster.customerTypeID
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " .
            $qString . $outletFilter .
            "GROUP BY
                    customertype.customerDescription ORDER BY customertype.customerTypeID ";


        // echo $q;

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_customerTypeCount_2_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT						
                customertype.customerDescription,
                sum(payments.amount) AS subTotal,					
                count( salesMaster.menuSalesID ) AS countTotal
                 
            FROM						
                srp_erp_pos_menusalesmaster AS salesMaster 
                LEFT JOIN  (SELECT SUM( IFNULL(amount,0) ) as amount, menuSalesID, paymentConfigMasterID FROM srp_erp_pos_menusalespayments GROUP BY menuSalesID) as payments	ON payments.menuSalesID = salesMaster.menuSalesID					
                LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = payments.paymentConfigMasterID 
                LEFT JOIN srp_erp_customertypemaster customertype ON customertype.customerTypeID = salesMaster.customerTypeID  
                WHERE
                    salesMaster.isVoid = 0
                AND salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " .
            $qString . $outletFilter .
            "GROUP BY
                    customertype.customerDescription 
                    ORDER BY customertype.customerDescription";


        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_report_salesReport_totalSales_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                'Total Sales' AS Description,
                SUM(paidAmount) AS amount

            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' "
            . $qString . $outletFilter;


        // echo $q;

        $result = $this->db->query($q)->row_array();
        return $result;
    }


    function get_report_salesReport_totalTaxes_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                taxMaster.taxDescription AS Description,
                SUM(tax.taxAmount) AS amount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            LEFT JOIN srp_erp_pos_menusalestaxes tax ON tax.menuSalesID = salesMaster.menuSalesID
            INNER JOIN srp_erp_taxmaster taxMaster ON taxMaster.taxMasterAutoID = tax.taxmasterID
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'
            " . $qString . $outletFilter . "
            GROUP BY tax.taxmasterID";
        //echo $q;


        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_report_salesReport_ServiceCharge_admin($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND wareHouseAutoID IN(" . $outlets . ")";
        }


        $q = "SELECT
                'Service Charge' AS Description,
                SUM(sc.serviceChargeAmount) AS amount
            FROM
                srp_erp_pos_menusalesmaster AS salesMaster
            LEFT JOIN srp_erp_pos_menusalesservicecharge sc ON sc.menuSalesID = salesMaster.menuSalesID
            WHERE
                salesMaster.isVoid = 0
            AND salesMaster.isHold = 0
            AND salesMaster.companyID = '" . current_companyID() . "'
            AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "' " . $qString . $outletFilter;
        //echo $q;


        $result = $this->db->query($q)->row_array();
        return $result;
    }

    function get_report_giftCardTopUp_admin($date, $data2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND giftCard.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND giftCard.outletID IN(" . $outlets . ")";
        }

        $q = "SELECT
                    configMaster.description as paymentDescription,
                    SUM(giftCard.topUpAmount) AS topUpTotal,
                     count(giftCard.cardTopUpID) as countTopUp
                FROM
                    srp_erp_pos_cardtopup AS giftCard

                LEFT JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON configMaster.autoID = giftCard.glConfigMasterID
                WHERE
                  giftCard.companyID = '" . current_companyID() . "'
                AND giftCard.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . " AND giftCard.topUpAmount>0
                GROUP BY
                    configMaster.autoID";
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_report_creditSales($date, $data2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND srp_erp_pos_menusalesmaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND srp_erp_pos_menusalesmaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                Sum( srp_erp_pos_menusalespayments.amount ) AS salesAmount,
                count(srp_erp_pos_menusalespayments.menuSalesPaymentID) as countCreditSales,
                srp_erp_pos_customermaster.CustomerName,
                srp_erp_pos_customermaster.CustomerAutoID 
            FROM
                srp_erp_pos_menusalespayments
                INNER JOIN srp_erp_pos_paymentglconfigmaster ON srp_erp_pos_paymentglconfigmaster.autoID = srp_erp_pos_menusalespayments.paymentConfigMasterID
                INNER JOIN srp_erp_pos_menusalesmaster ON srp_erp_pos_menusalespayments.menuSalesID = srp_erp_pos_menusalesmaster.menuSalesID
                LEFT JOIN srp_erp_pos_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_pos_customermaster.CustomerAutoID 
                WHERE
                  srp_erp_pos_menusalesmaster.companyID = '" . current_companyID() . "'
                AND srp_erp_pos_menusalesmaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter . "  
                AND srp_erp_pos_paymentglconfigmaster.autoID = 7 
                AND  srp_erp_pos_menusalesmaster.isVoid = 0 
                AND srp_erp_pos_menusalesmaster.isHold = 0
                GROUP BY
                    srp_erp_pos_customermaster.CustomerAutoID";
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    function get_report_voidBills_admin($date, $data2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = " AND salesMaster.createdUserID IN(" . $cashier . ") ";
        }

        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND wareHouseAutoID IN(" . $outlets . ")";
        }

        $q = "SELECT
                   'Voided Bills'  AS paymentDescription,
                    SUM(salesMaster.subTotal) AS NetTotal,
                     count(	salesMaster.menuSalesID) as countTransaction
                FROM
                    srp_erp_pos_menusalesmaster AS salesMaster

                WHERE
                   salesMaster.isVoid = 1 AND
                salesMaster.isHold = 0
                AND salesMaster.companyID = '" . current_companyID() . "'
                AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $data2 . "'
                " . $qString . $outletFilter;
        $result = $this->db->query($q)->row_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_outlet_cashier()
    {
        if ($this->input->post("warehouseAutoID")) {
            $warehouse = join(',', $this->input->post("warehouseAutoID"));
            $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " and warehouseAutoID IN($warehouse) GROUP BY salesMaster.createdUserID ";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "cashier[]" id = "cashier2" class="form-control input-sm" multiple = "multiple"  required >';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['createdUserID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        } else {
            $html = '<select name = "cashier[]" id = "cashier2" class="form-control input-sm" multiple = "multiple"  required > ';
            $html .= '</select > ';
            return $html;
        }

    }

    function get_gpos_outlet_cashier()
    {
        if ($this->input->post("warehouseAutoID")) {
            $warehouse = join(',', $this->input->post("warehouseAutoID"));
            $q = "SELECT
                Ename2 AS empName,
                invoice.createdUserID as createdUserID 
            FROM
                srp_erp_pos_invoice invoice
                JOIN srp_employeesdetails employees ON employees.EIdNo = invoice.createdUserID 
            WHERE
                invoice.companyID = '" . current_companyID() . "' 
                AND warehouseAutoID IN($warehouse) 
            GROUP BY
                invoice.createdUserID";
            /*$q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " and warehouseAutoID IN($warehouse) GROUP BY salesMaster.createdUserID ";*/
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "cashier[]" id = "cashier2" class="form-control input-sm" multiple = "multiple"  required >';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['createdUserID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        } else {
            $html = '<select name = "cashier[]" id = "cashier2" class="form-control input-sm" multiple = "multiple"  required > ';
            $html .= '</select > ';
            return $html;
        }

    }


    function get_outlet_cashier_itemized()
    {
        if ($this->input->post("warehouseAutoID")) {
            $warehouse = join(',', $this->input->post("warehouseAutoID"));
            $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " and warehouseAutoID IN($warehouse) GROUP BY salesMaster.createdUserID ";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "cashier[]" id = "cashieritemized" class="form-control input-sm" multiple = "multiple"  required > ';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['createdUserID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        } else {
            $html = '<select name = "cashier[]" id = "cashieritemized" class="form-control input-sm" multiple = "multiple"  required > ';
            $html .= '</select > ';
            return $html;
        }

    }

    function get_outlet_cashier_Promotions()
    {
        if ($this->input->post("warehouseAutoID")) {
            $warehouse = join(',', $this->input->post("warehouseAutoID"));
            $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " and warehouseAutoID IN($warehouse) GROUP BY salesMaster.createdUserID ";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "cashier[]" id = "cashierpromotions" class="form-control input-sm" multiple = "multiple"  required > ';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['createdUserID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        } else {
            $html = '<select name = "cashier[]" id = "cashierpromotions" class="form-control input-sm" multiple = "multiple"  required > ';
            $html .= '</select > ';
            return $html;
        }

    }


    function get_outlet_cashier_productmix()
    {
        if ($this->input->post("warehouseAutoID")) {
            $warehouse = join(',', $this->input->post("warehouseAutoID"));
            $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " and warehouseAutoID IN($warehouse) GROUP BY salesMaster.createdUserID ";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "cashier[]" id = "cashierproductmix" class="form-control input-sm" multiple = "multiple"  required > ';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['createdUserID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        } else {
            $html = '<select name = "cashier[]" id = "cashierproductmix" class="form-control input-sm" multiple = "multiple"  required > ';
            $html .= '</select > ';
            return $html;
        }

    }

    function get_outlet_cashier_franchise()
    {
        if ($this->input->post("warehouseAutoID")) {
            $warehouse = join(',', $this->input->post("warehouseAutoID"));
            $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " and warehouseAutoID IN($warehouse) GROUP BY salesMaster.createdUserID ";
            $result = $this->db->query($q)->result_array();
            $html = '<select name = "cashier[]" id = "cashierfranchise" class="form-control input-sm" multiple = "multiple"  required > ';
            if ($result) {
                foreach ($result as $val) {
                    $html .= '<option value = "' . $val['createdUserID'] . '" > ' . $val['empName'] . ' </option > ';
                }
            }
            $html .= '</select > ';
            return $html;
        } else {
            $html = '<select name = "cashier[]" id = "cashierfranchise" class="form-control input-sm" multiple = "multiple"  required > ';
            $html .= '</select > ';
            return $html;
        }

    }

    function get_srp_erp_pos_paymentglconfigmaster()
    {
        $this->db->select("autoID,description,sortOrder");
        $this->db->from("srp_erp_pos_paymentglconfigmaster");
        $this->db->order_by("sortOrder ASC");
        $result = $this->db->get()->result_array();
        return $result;
    }

    function get_report_salesDetailReport($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        $querySalesDetail = "SELECT
                            salesMaster.menuSalesID AS salesMasterMenuSalesID,
                            DATE_FORMAT( salesMaster.createdDateTime, '%d-%m-%Y' ) AS salesMasterCreatedDate,
                            DATE_FORMAT( salesMaster.createdDateTime, '%h-%i %p' ) AS salesMasterCreatedTime,
                            wmaster.wareHouseDescription AS whouseName,
                            employee.EmpShortCode AS menuCreatedUser,
                            salesMaster.grossTotal,
                            salesMaster.grossAmount,
                            salesMaster.companyLocalCurrencyDecimalPlaces AS companyLocalDecimal,
                            invoiceCode,
                            salesMaster.discountPer,
                            salesMaster.discountAmount,
                            salesMaster.promotionDiscount,
                            salesMaster.deliveryCommission,
                            salesMaster.deliveryCommissionAmount,
                            salesMaster.subTotal AS billNetTotal,
                            salesMaster.promotionDiscount,
                            payment.*,
                            promotionTypeP.customerName AS PromotionalDiscountType,
                            promotionTypeD.customerName AS DeliveryCommissionType 
                        FROM
                            srp_erp_pos_menusalesmaster AS salesMaster
                            LEFT JOIN srp_erp_warehousemaster wmaster ON salesMaster.wareHouseAutoID = wmaster.wareHouseAutoID
                            LEFT JOIN srp_employeesdetails employee ON employee.EIdNo = salesMaster.createdUserID
                            LEFT JOIN srp_erp_pos_customers promotionTypeP ON promotionTypeP.customerID = salesMaster.promotionID
                            LEFT JOIN srp_erp_pos_customers promotionTypeD ON promotionTypeD.customerID = salesMaster.deliveryPersonID
                            LEFT JOIN (
                        SELECT
                            paymentConfigMasterID,
                            amount,
                            menuSalesID,
                            srp_erp_pos_menusalespayments.customerAutoID,
                            srp_erp_customermaster.customerName,
                            sum( CASE WHEN paymentConfigMasterID = '26' THEN amount ELSE 0 END ) RCGC,
                            sum( CASE WHEN paymentConfigMasterID = '1' THEN amount ELSE 0 END ) Cash,
                            sum( CASE WHEN paymentConfigMasterID = '2' THEN amount ELSE 0 END ) CreditNote,
                            sum( CASE WHEN paymentConfigMasterID = '3' THEN amount ELSE 0 END ) MasterCard,
                            sum( CASE WHEN paymentConfigMasterID = '4' THEN amount ELSE 0 END ) VisaCard,
                            sum( CASE WHEN paymentConfigMasterID = '5' THEN amount ELSE 0 END ) GiftCard,
                            sum( CASE WHEN paymentConfigMasterID = '6' THEN amount ELSE 0 END ) AMEX,
                            sum( CASE WHEN paymentConfigMasterID = '7' THEN amount ELSE 0 END ) CreditSales,
                            sum( CASE WHEN paymentConfigMasterID = '27' THEN amount ELSE 0 END ) FriMi,
                            sum( CASE WHEN paymentConfigMasterID = '25' THEN amount ELSE 0 END ) JavaApp 
                        FROM
                            srp_erp_pos_menusalespayments
                            LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID 
                        GROUP BY
                            menuSalesID 
                            ) payment ON salesMaster.menuSalesID = payment.menuSalesID 
                        WHERE
                            salesMaster.isVoid = 0 
                            AND salesMaster.isHold = 0 
                            AND salesMaster.companyID = " . current_companyID() . " 
                            AND salesMaster.createdDateTime BETWEEN '" . $date . "' 
                            AND '" . $date2 . "' " . $qString . $outletFilter . " 
                        GROUP BY
                            salesMaster.menuSalesID ";

        return $this->db->query($querySalesDetail)->result_array();
    }

    function get_srp_erp_pos_menusalesitems_invoiceID_salesDetailReport($invoiceID)
    {
        //return $this->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID);
        return $this->get_srp_erp_pos_menusalesitems_drillDown($invoiceID);
    }

    function get_srp_erp_pos_menusalesmaster_salesDetailReport($id)
    {
        $this->db->select("menuSales.*, cType.customerDescription, wmaster.wareHouseDescription,wmaster.warehouseAddress,wmaster.warehouseTel");
        $this->db->from("srp_erp_pos_menusalesmaster menuSales");
        $this->db->join('srp_erp_customertypemaster cType', 'cType.customerTypeID = menuSales.customerTypeID', 'left'); /*customerTypeID*/
        $this->db->join('srp_erp_warehousemaster wmaster', 'wmaster.wareHouseAutoID = menuSales.wareHouseAutoID', 'left'); /*customerTypeID*/
        $this->db->where('menuSales.menuSalesID', $id);
        //$this->db->where('menuSales.wareHouseAutoID', current_warehouseID());
        $result = $this->db->get()->row_array();
        return $result;
    }

    function get_tableList($status = array())
    {

        $this->db->select('diningTableAutoID, diningTableDescription, noOfSeats, diningRoomMasterID');
        $this->db->from('srp_erp_pos_diningtables');
        if (!empty($status)) {
            foreach ($status as $val) {
                $this->db->or_where('status', $val);
            }
        }
        $this->db->where('companyID', current_companyID());
        $this->db->where('segmentID', get_outletID());
        $result = $this->db->get()->result_array();
        return $result;
    }

    function validate_tableOrder()
    {
        $menuSalesID = $this->input->post('menuSalesID');
        $tableID = $this->input->post('id');
        $this->db->select("*");
        $this->db->from("srp_erp_pos_diningtables");
        $this->db->where('diningTableAutoID', $tableID);
        $this->db->where('status', 1);
        $diningTableAutoID = $this->db->get()->row('diningTableAutoID');
        if ($diningTableAutoID) {
            return false;
        } else {
            return true;
        }

        /*        } else {
                    return false;
                }*/
    }

    function update_menuSalesMasterTableID()
    {
        $menuSalesID = $this->input->post('menuSalesID');
        $tableID = $this->input->post('id');
        $this->db->where('menuSalesID', $menuSalesID);
        $result = $this->db->update('srp_erp_pos_menusalesmaster', array('tableID' => $tableID));
        return $result;

    }

    function update_diningTableStatus()
    {
        /*
        $this->db->select("tableID");
        $this->db->from("srp_erp_pos_menusalesmaster");
        $this->db->where('isHold', 1);
        $this->db->where('isVoid', 0);
        $this->db->where('menuSalesID', $menuSalesID);
        $tableID = $this->db->get()->row('tableID');


        $result = false;
        if (!$tableID) {*/
        $tableID = $this->input->post('id');
        $this->db->where('diningTableAutoID', $tableID);
        $data['status'] = 1;
        $data['tmp_menuSalesID'] = $this->input->post('menuSalesID');
        $result = $this->db->update('srp_erp_pos_diningtables', $data);

        /* }*/

        return $result;
    }

    function get_diningTableUsed()
    {
        $this->db->select('msm.menuSalesID, dt.diningTableAutoID, concat(msm.invoiceCode,"<br/>",crew.crewLastName) as invoiceCode, dt.diningTableDescription as tableName, dt.status, dt.tmp_crewID');
        $this->db->from('srp_erp_pos_diningtables dt');
        $this->db->join('srp_erp_pos_menusalesmaster msm', 'msm.menuSalesID=dt.tmp_menuSalesID', 'left');
        $this->db->join('srp_erp_pos_crewmembers crew', 'crew.crewMemberID = dt.tmp_crewID', 'left');
        $this->db->where('dt.status', 1);
        $this->db->where('dt.companyID', current_companyID());
        $this->db->where('dt.segmentID', get_outletID());
        $result = $this->db->get()->result_array();
        return $result;

    }

    function update_diningTableReset($tableID)
    {
        $this->db->where('diningTableAutoID', $tableID);
        $result = $this->db->update('srp_erp_pos_diningtables', array('status' => 0, 'tmp_menuSalesID' => null, 'tmp_crewID' => null, 'tmp_numberOfPacks' => 0));
        return $result;
    }

    function get_srp_erp_pos_paymentglconfigmaster2()
    {
        $this->db->select("autoID,description,sortOrder");
        $this->db->from("srp_erp_pos_paymentglconfigmaster");
        $this->db->order_by("sortOrder ASC");
        $result = $this->db->get()->result_array();
        return $result;
    }

    function get_report_salesDetailReport2($date, $date2, $cashier = null, $outlets = null)
    {
        $qString = '';
        if ($cashier != null) {
            $qString = "AND salesMaster.createdUserID IN(" . $cashier . ")";
        }
        $outletFilter = '';
        if ($outlets != null) {
            $outletFilter = " AND salesMaster.wareHouseAutoID IN(" . $outlets . ")";
        }

        //$querySalesDetail = "SELECT salesMaster.menuSalesID as salesMasterMenuSalesID,salesMaster.isDelivery,salesMaster.isHold,deliveryorders.isDispatched as isDispatched,DATE_FORMAT(salesMaster.createdDateTime, '%d-%m-%Y') AS salesMasterCreatedDate,DATE_FORMAT(salesMaster.createdDateTime, '%h-%i %p') AS salesMasterCreatedTime,wmaster.wareHouseDescription as whouseName,wmaster.wareHouseCode as wareHouseCode,employee.EmpShortCode as menuCreatedUser,salesMaster.grossTotal,salesMaster.grossAmount,salesMaster.companyLocalCurrencyDecimalPlaces as companyLocalDecimal,invoiceCode,salesMaster.discountPer,salesMaster.discountAmount,salesMaster.promotionDiscount,salesMaster.deliveryCommission,salesMaster.deliveryCommissionAmount,salesMaster.subTotal as billNetTotal,salesMaster.promotionDiscount,payment.*,promotionTypeP.customerName as PromotionalDiscountType,promotionTypeD.customerName as DeliveryCommissionType,salesMaster.isDelivery, salesMaster.isHold,COUNT(deliveryorders.menuSalesMasterID) AS isDelivery1,pos_cmaster.CustomerName AS DeliveryCustomerName,deliveryorders.posCustomerAutoID AS DeliveryCustomerID,CASE deliveryorders.isDispatched WHEN 0 THEN 'No' WHEN deliveryorders.isDispatched IS NULL THEN 'Yes' WHEN deliveryorders.isDispatched = '' THEN 'Yes' WHEN 1 THEN 'Yes' END AS deliveryordersDispatched FROM srp_erp_pos_menusalesmaster AS salesMaster LEFT JOIN srp_erp_warehousemaster wmaster ON salesMaster.wareHouseAutoID = wmaster.wareHouseAutoID LEFT JOIN srp_employeesdetails employee ON employee.EIdNo = salesMaster.createdUserID LEFT JOIN srp_erp_pos_deliveryorders deliveryorders ON deliveryorders.menuSalesMasterID = salesMaster.menuSalesID LEFT JOIN srp_erp_pos_customers promotionTypeP ON promotionTypeP.customerID = salesMaster.promotionID LEFT JOIN srp_erp_pos_customers promotionTypeD ON promotionTypeD.customerID = salesMaster.deliveryPersonID LEFT JOIN srp_erp_pos_customermaster pos_cmaster ON pos_cmaster.posCustomerAutoID = deliveryorders.posCustomerAutoID LEFT JOIN (Select paymentConfigMasterID,amount,menuSalesID,srp_erp_pos_menusalespayments.customerAutoID,srp_erp_customermaster.customerName, sum(CASE WHEN paymentConfigMasterID = '1' THEN amount ELSE 0 END) Cash,sum(CASE WHEN paymentConfigMasterID = '2' THEN amount ELSE 0 END) CreditNote,sum(CASE WHEN paymentConfigMasterID = '3' THEN amount ELSE 0 END) MasterCard,sum(CASE WHEN paymentConfigMasterID = '4' THEN amount ELSE 0 END) VisaCard,sum(CASE WHEN paymentConfigMasterID = '5' THEN amount ELSE 0 END) GiftCard,sum(CASE WHEN paymentConfigMasterID = '6' THEN amount ELSE 0 END) AMEX,sum(CASE WHEN paymentConfigMasterID = '7' THEN amount ELSE 0 END) CreditSales,sum(CASE WHEN paymentConfigMasterID = '25' THEN amount ELSE 0 END) JavaApp FROM srp_erp_pos_menusalespayments LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID GROUP BY menuSalesID) payment ON salesMaster.menuSalesID = payment.menuSalesID WHERE salesMaster.isVoid = 0   AND salesMaster.companyID = " . current_companyID() . " AND salesMaster.createdDateTime BETWEEN '" . $date . "' AND '" . $date2 . "'" . $qString . $outletFilter . " GROUP BY salesMaster.menuSalesID HAVING (isHold = 0 OR isDelivery1=1) ";
        $querySalesDetail = "SELECT deliveryorders.deliveryOrderID,salesMaster.menuSalesID as salesMasterMenuSalesID,salesMaster.isDelivery,salesMaster.isHold,deliveryorders.isDispatched as isDispatched,DATE_FORMAT(salesMaster.createdDateTime, '%d-%m-%Y') AS salesMasterCreatedDate,DATE_FORMAT(salesMaster.createdDateTime, '%h-%i %p') AS salesMasterCreatedTime,deliveryorders.deliveryDate as ddate,deliveryorders.deliveryTime as dtime, salesMaster.createdDateTime k, if (deliveryorders.deliveryOrderID is not null , DATE_FORMAT(concat( DATE_FORMAT(deliveryorders.deliveryDate, '%Y-%m-%d'), ' ', DATE_FORMAT(deliveryorders.deliveryTime, '%H-%i-%s')), '%Y-%m-%d %H-%i-%s') , DATE_FORMAT(salesMaster.createdDateTime,'%Y-%m-%d %H-%i-%S')) as rptDate,wmaster.wareHouseDescription as whouseName,wmaster.wareHouseCode as wareHouseCode,employee.EmpShortCode as menuCreatedUser,salesMaster.grossTotal,salesMaster.grossAmount,salesMaster.companyLocalCurrencyDecimalPlaces as companyLocalDecimal,invoiceCode,salesMaster.discountPer,salesMaster.discountAmount,salesMaster.promotionDiscount,salesMaster.deliveryCommission,salesMaster.deliveryCommissionAmount,salesMaster.subTotal as billNetTotal,salesMaster.promotionDiscount,payment.*,promotionTypeP.customerName as PromotionalDiscountType,promotionTypeD.customerName as DeliveryCommissionType,salesMaster.isDelivery, salesMaster.isHold,COUNT(deliveryorders.menuSalesMasterID) AS isDelivery1,pos_cmaster.CustomerName AS DeliveryCustomerName,deliveryorders.posCustomerAutoID AS DeliveryCustomerID,CASE deliveryorders.isDispatched WHEN 0 THEN 'No' WHEN deliveryorders.isDispatched IS NULL THEN 'Yes' WHEN deliveryorders.isDispatched = '' THEN 'Yes' WHEN 1 THEN 'Yes' END AS deliveryordersDispatched FROM srp_erp_pos_menusalesmaster AS salesMaster LEFT JOIN srp_erp_warehousemaster wmaster ON salesMaster.wareHouseAutoID = wmaster.wareHouseAutoID LEFT JOIN srp_employeesdetails employee ON employee.EIdNo = salesMaster.createdUserID LEFT JOIN srp_erp_pos_deliveryorders deliveryorders ON deliveryorders.menuSalesMasterID = salesMaster.menuSalesID LEFT JOIN srp_erp_pos_customers promotionTypeP ON promotionTypeP.customerID = salesMaster.promotionID LEFT JOIN srp_erp_pos_customers promotionTypeD ON promotionTypeD.customerID = salesMaster.deliveryPersonID LEFT JOIN srp_erp_pos_customermaster pos_cmaster ON pos_cmaster.posCustomerAutoID = deliveryorders.posCustomerAutoID LEFT JOIN (Select paymentConfigMasterID,amount,menuSalesID,srp_erp_pos_menusalespayments.customerAutoID,srp_erp_customermaster.customerName, sum(CASE WHEN paymentConfigMasterID = '1' THEN amount ELSE 0 END) Cash,sum(CASE WHEN paymentConfigMasterID = '2' THEN amount ELSE 0 END) CreditNote,sum(CASE WHEN paymentConfigMasterID = '3' THEN amount ELSE 0 END) MasterCard,sum(CASE WHEN paymentConfigMasterID = '4' THEN amount ELSE 0 END) VisaCard,sum(CASE WHEN paymentConfigMasterID = '5' THEN amount ELSE 0 END) GiftCard,sum(CASE WHEN paymentConfigMasterID = '6' THEN amount ELSE 0 END) AMEX,sum(CASE WHEN paymentConfigMasterID = '7' THEN amount ELSE 0 END) CreditSales,sum(CASE WHEN paymentConfigMasterID = '25' THEN amount ELSE 0 END) JavaApp, sum( CASE WHEN paymentConfigMasterID = '27' THEN amount ELSE 0 END ) FriMi FROM srp_erp_pos_menusalespayments LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID GROUP BY menuSalesID) payment ON salesMaster.menuSalesID = payment.menuSalesID WHERE salesMaster.isVoid = 0   AND salesMaster.companyID = " . current_companyID() . " " . $qString . $outletFilter . " GROUP BY salesMaster.menuSalesID HAVING (isHold = 0 OR deliveryOrderID is not null) AND (rptDate BETWEEN '$date' AND '$date2') ";

        //echo $querySalesDetail;

        return $this->db->query($querySalesDetail)->result_array();
    }

    function get_srp_erp_pos_menusalesitems_drillDown($invoiceID)
    {
        $path = base_url();
        $this->db->select("sales.menuSalesID, sales.menuSalesItemID, category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, sales.menuSalesPrice as sellingPrice, sales.qty , sales.discountPer, sales.discountAmount, menuMaster.menuMasterID,sales.remarkes, sales.menuSalesPrice as pricewithoutTax, sales.totalMenuTaxAmount as totalTaxAmount, sales.totalMenuServiceCharge as totalServiceCharge,menu.isTaxEnabled , size.code as sizeCode, size.description as sizeDescription");
        $this->db->from("srp_erp_pos_menusalesitems sales");
        $this->db->join("srp_erp_pos_warehousemenumaster menu", "menu.warehouseMenuID = sales.warehouseMenuID");
        $this->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $this->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $this->db->join("srp_erp_pos_menusize size", "size.menuSizeID = menuMaster.menuSizeID", "left");
        /*$this->db->where('menu.isActive', 1);
        $this->db->where('menu.isDeleted', 0);
        $this->db->where('menuMaster.isDeleted', 0);*/
        $this->db->where('sales.menuSalesID', $invoiceID);
        //$this->db->where('sales.id_store', current_warehouseID());
        $result = $this->db->get()->result_array();

        return $result;
    }

    function update_isSampleBillPrintFlag($invoiceID)
    {
        if (!empty($invoiceID)) {
            $this->db->where('menuSalesID', $invoiceID);
            return $this->db->update('srp_erp_pos_menusalesitems', array('isSamplePrinted' => 1));
        } else {
            return false;
        }

    }

    function load_hold_refno()
    {
        $menuSalesID = $this->input->post('menuSalesID');
        $this->db->select("holdRemarks");
        $this->db->from("srp_erp_pos_menusalesmaster");
        $this->db->where('menuSalesID', $menuSalesID);
        $result = $this->db->get()->row_array();

        return $result;
    }

    function submitBOT()
    {
        $invoiceID = $this->input->post('id');
        $data['BOT'] = 1;
        $data['BOTCreatedUser'] = current_userID();
        $data['BOTCreatedDatetime'] = format_date_mysql_datetime();
        $this->db->where('menuSalesID', $invoiceID);
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);
        if ($result) {
            return array('error' => 0, 'e_type' => 's', 'message' => 'Successfully submitted to BOT.');
        } else {
            return array('error' => 1, 'e_type' => 'e', 'message' => 'error while submitting to BOT.');
        }
    }

}