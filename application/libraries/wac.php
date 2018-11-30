<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Wac
{

    var $CI;
    private static $authenticatedMemberId = null;

    function Wac()
    {
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->library('session');
    }


    function wac_calculation($isSales, $itemAutoID, $qty, $cost = 0, $wareHouseID = 0)
    {

        if (isset($itemAutoID)) {
            $CI =& get_instance();

            $com_currency = $CI->common_data['company_data']['company_default_currency'];
            $com_currDPlace = $CI->common_data['company_data']['company_default_decimal'];
            $rep_currency = $CI->common_data['company_data']['company_reporting_currency'];
            $rep_currDPlace = $CI->common_data['company_data']['company_reporting_decimal'];

            $itemData = $CI->db->select('itemSystemCode, currentStock, defaultUnitOfMeasure, companyLocalWacAmount cWac')
                ->from('srp_erp_itemmaster')->where('itemAutoID', $itemAutoID)->get()->row();

            if ($isSales == 1) {
                $qty *= -1;
                $docTot = $itemData->cWac * $qty;
            } else {
                $docTot = $cost * $qty;
            }

            $newQty = $itemData->currentStock + $qty;
            $currentTot = $itemData->cWac * $itemData->currentStock;

            $newTot = $currentTot + $docTot;
            $newWac = round(($newTot / $newQty), $com_currDPlace);

            //Reporting wac amount
            $reportConversion = currency_conversion($com_currency, $rep_currency, $newWac);
            $reportConversionRate = $reportConversion['conversion'];
            $repWac = round(($newWac / $reportConversionRate), $rep_currDPlace);

            $data = array(
                'currentStock' => $newQty,
                'companyLocalWacAmount' => $newWac,
                'companyReportingWacAmount' => $repWac
            );

            $where = array(
                'itemAutoID' => $itemAutoID,
                'companyID' => current_companyID()
            );

            $CI->db->where($where)->update('srp_erp_itemmaster', $data);

            if (isset($wareHouseID)) {
                $CI->db->query("UPDATE srp_erp_warehouseitems SET currentStock=(currentStock+{$qty})
                                  WHERE itemAutoID={$itemAutoID} AND wareHouseAutoID={$wareHouseID}");
            }

            return array('s', 'wac updated', $newQty);
        } else {
            return array('e', 'Item ID is empty');
        }
    }

    function wac_calculation_amounts($itemAutoID = null, $transactionUOM = null, $transactionQTY = null, $transactionCurrency = null, $cost = 0,$isReduce = 0)
    { /*return local and reporting wac calculation amount*/

        if (isset($itemAutoID)) {
            $CI =& get_instance();

            $com_currency = $CI->common_data['company_data']['company_default_currency'];
            $com_currDPlace = $CI->common_data['company_data']['company_default_decimal'];
            $rep_currency = $CI->common_data['company_data']['company_reporting_currency'];
            $rep_currDPlace = $CI->common_data['company_data']['company_reporting_decimal'];

            if ($isReduce) {
                $transactionQTY *= -1;
            }

            $itemData = $CI->db->select('itemSystemCode, currentStock, defaultUnitOfMeasure, companyLocalWacAmount,companyReportingWacAmount')
                ->from('srp_erp_itemmaster')->where('itemAutoID', $itemAutoID)->get()->row(); // get item detail by item ID

            //get current item total amount
            $currentTotalLoc = $itemData->currentStock * $itemData->companyLocalWacAmount;
            $currentTotalRpt = $itemData->currentStock * $itemData->companyReportingWacAmount;

            $defaultUOM = $itemData->defaultUnitOfMeasure;

            //unit convertion
            $conversion = conversionRateUOM($transactionUOM, $defaultUOM);
            $conversionRate = 1 / $conversion;
            $defaultQty = $transactionQTY * $conversionRate;

            $cost = $cost/$conversionRate; //calculate cost in default unit of mesure

           //$defaultQty = $transactionQTY / $docQty;

            //Local wac amount
            $localConversion = currency_conversion($transactionCurrency,$com_currency);
            $localConversionRate = $localConversion['conversion'];
            $locWac = round(($cost / $localConversionRate), $com_currDPlace);

            //Reporting wac amount
            $reportConversion = currency_conversion($transactionCurrency,$rep_currency);
            $reportConversionRate = $reportConversion['conversion'];
            $repWac = round(($cost / $reportConversionRate), $rep_currDPlace);

            //get convertion item total amount
            $docTotalLoc = $locWac * $defaultQty;
            $docTotalRpt = $repWac * $defaultQty;

            //get total current and convertion item total amount and qty
            $newDefaultQty = $defaultQty + $itemData->currentStock;
            $newTotalLoc = $currentTotalLoc + $docTotalLoc;
            $newTotalRpt = $currentTotalRpt + $docTotalRpt;

            $newWacLoc = round(($newTotalLoc / $newDefaultQty), $com_currDPlace);
            $newWacRpt = round(($newTotalRpt / $newDefaultQty), $rep_currDPlace);

            $data = array(
                'currentStock' => $newDefaultQty,
                'companyLocalWacAmount' => $newWacLoc,
                'companyReportingWacAmount' => $newWacRpt
            );
            $where = array(
                'itemAutoID' => $itemAutoID,
                'companyID' => current_companyID()
            );

            $result = $CI->db->where($where)->update('srp_erp_itemmaster', $data); // update itemmaster
            if ($result) {
                return array('companyLocalWacAmount' => $newWacLoc, 'companyReportingWacAmount' => $newWacRpt);
            }

        } else {
            return array('e', 'Item ID is empty');
        }
    }
}