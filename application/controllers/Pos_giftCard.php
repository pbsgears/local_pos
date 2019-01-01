<?php
/**
 *
 * -- =============================================
 * -- File Name : giftCard.php
 * -- Project Name : POS
 * -- Module Name : POS Gift Card
 * -- Author : Mohamed Shafri
 * -- Create date : 19 October 2017
 * -- Description : Gift Card masters and Gift Card Process .
 *
 * --REVISION HISTORY
 * 
 * --Date: 19-Oct 2017 By: Mohamed Shafri: file created
 * --Date: 01-JAN 2019 By: Mohamed Shafri: SME-1305  Local POS - Gift card two way syncing, Manual Pulling only for Git Card
 * --Date: 01-JAN 2019 By: Mohamed Shafri: Changes Reference https://github.com/pbsgears/local_pos/commit/7d30b122f5b9d2467220dc34c60bdd660d1ca16e
 *
 *
 * -- =============================================
 **/
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos_giftCard extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Pos_gift_card');
        $this->load->helper('pos');
    }

    public function fetch_giftCardMaster()
    {
        $f_outletID = $this->input->post('f_outletID');
        //echo $f_cardMasterID;
        $this->datatables->select('giftCard.cardMasterID as cardMasterID, barcode, outletID, outlet.wareHouseCode  as wareHouseCode ,outlet.wareHouseLocation as wareHouseLocation, outlet.wareHouseDescription as outletDescription, cardExpiryInMonths', false)
            ->from('srp_erp_pos_giftcardmaster giftCard')
            ->join('srp_erp_warehousemaster outlet', 'outlet.wareHouseAutoID = giftCard.outletID')
            ->add_column('action', '$1', 'get_giftCardCols(cardMasterID, barcode, outletID, cardExpiryInMonths)')
            ->add_column('descriptionOutlet', '$1 - $2 - $3', 'wareHouseCode,outletDescription,wareHouseLocation ');
        //->add_column('wareHouseColumn', '$1  -  $2', 'wareHouseCode,wareHouseLocation')
        $this->datatables->where('giftCard.companyID', current_companyID());
        if (!empty($f_outletID)) {
            $this->datatables->where('outletID', $f_outletID);
        }

        $r = $this->datatables->generate();
        $this->db->last_query();

        echo $r;


    }

    function submit_giftCards()
    {
        $this->form_validation->set_rules('outletID', 'Outlet', 'trim|required');
        $this->form_validation->set_rules('barcode', 'BardCode', 'trim|required');
        $this->form_validation->set_rules('cardExpiryInMonths', 'Expiry In Months', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            $result = $this->Pos_gift_card->save_giftCardMaster();
            echo json_encode($result);

        }
    }

    function delete_giftCard()
    {
        $id = $this->input->post('cardMasterID');
        $isIssued = $this->db->select('cardMasterID')->from('srp_erp_pos_cardissue')->where('cardMasterID', $id)->get()->row('cardMasterID');
        if (!$isIssued) {
            $this->db->where('cardMasterID', $id);
            $result = $this->db->delete('srp_erp_pos_giftcardmaster');
            if ($result) {
                echo json_encode(array('error' => 0, 'message' => 'Card Successfully saved.'));
            } else {
                echo json_encode(array('error' => 1, 'message' => "Error while inserting" . $this->db->_error_message()));
            }
        } else {
            echo json_encode(array('error' => 1, 'message' => "You can not delete the card!.<br/><br/> This card is already issued to a customer. "));
        }

    }

    function loadCustomerCardData()
    {
        $barCode = trim($this->input->post('barcode'));
        $customerInfo = $this->Pos_gift_card->loadCardIssueData($barCode);
        $balanceAmount = $this->Pos_gift_card->get_giftCardBalanceAmount($barCode);
        $cardIssueID = isset($customerInfo['cardIssueID']) && !empty($customerInfo['cardIssueID']) ? $customerInfo['cardIssueID'] : 0;
        $posCustomerAutoID = isset($customerInfo['posCustomerAutoID']) && !empty($customerInfo['posCustomerAutoID']) ? $customerInfo['posCustomerAutoID'] : 0;
        $customerTelephone = isset($customerInfo['customerTelephone']) && !empty($customerInfo['customerTelephone']) ? $customerInfo['customerTelephone'] : '';
        $CustomerName = isset($customerInfo['CustomerName']) && !empty($customerInfo['CustomerName']) ? $customerInfo['CustomerName'] : '';
        $expiryDate = isset($customerInfo['expiryDate']) && !empty($customerInfo['expiryDate']) ? date('d/m/Y', strtotime($customerInfo['expiryDate'])) : '';
        $curDate = date('Y-m-d');
        if (strtotime($customerInfo['expiryDate']) < strtotime(format_date($curDate))) {
            /** not expired*/
            $expired = 1;
        } else {
            /** expired*/
            $expired = 0;
        }

        echo json_encode(array('error' => 0, 'balanceAmount' => $balanceAmount, 'posCustomerAutoID' => $posCustomerAutoID, 'cardIssueID' => $cardIssueID, 'customerTelephone' => $customerTelephone, 'CustomerName' => $CustomerName, 'expiryDate' => $expiryDate, 'isCardExpired' => $expired));
    }

    function topUpGiftCard($return = false)
    {
        $this->form_validation->set_rules('barcode', 'Bar Code', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            if ($return) {
                return json_encode(array('error' => 1, 'message' => $error_message));
            } else {
                echo json_encode(array('error' => 1, 'message' => $error_message));
            }
        } else {
            $barCode = trim($this->input->post('barcode'));
            $customerInfo = $this->Pos_gift_card->loadCardIssueData($barCode);
            if (!empty($customerInfo)) {

                $paymentTypes = $this->input->post('paymentTypes');
                $references = $this->input->post('reference');


                $glConfigMasters = $this->input->post('glConfigMasters');
                $outletID = get_outletID();
                $giftCardGLAutoID = $this->Pos_gift_card->get_cardGlAutoID();
                $counterInfo = get_counterData();
                $shiftID = $counterInfo['shiftID'];
                $SN = generateGiftCardSerialNo();

                if (!empty($paymentTypes)) {
                    $i = 0;
                    foreach ($paymentTypes as $key => $paymentAmount) {
                        if ($paymentAmount > 0) {

                            //$reference = $references[$key];

                            $glConfigMasterID = $glConfigMasters[$key];
                            if ($glConfigMasterID == 7) {
                                $data_card['creditSalesCustomerID'] = $this->input->post('gc_creditCustomerID');
                                $data_card['isCreditSale'] = 1;

                            }
                            $data_card['giftCardReceiptID'] = $SN;
                            $data_card['cardMasterID'] = $customerInfo['cardMasterID'];
                            $data_card['barCode'] = $barCode;
                            $data_card['posCustomerAutoID'] = $customerInfo['posCustomerAutoID'];
                            $data_card['topUpAmount'] = $paymentAmount;
                            $data_card['points'] = 0;
                            $data_card['glConfigMasterID'] = $glConfigMasterID;
                            $data_card['glConfigDetailID'] = $key;
                            $data_card['menuSalesID'] = 0;

                            //gc_creditCustomerID

                            $data_card['giftCardGLAutoID'] = $giftCardGLAutoID;
                            $data_card['wareHouseAutoID'] = $outletID;
                            $data_card['outletID'] = $outletID;
                            $data_card['id_store'] = $outletID;
                            $data_card['reference'] = isset($references[$key]) ? $references[$key] : '';
                            $data_card['shiftID'] = $shiftID;
                            $data_card['companyID'] = current_companyID();
                            $data_card['companyCode'] = current_companyCode();
                            $data_card['createdPCID'] = current_pc();
                            $data_card['createdUserID'] = current_userID();
                            $data_card['createdDateTime'] = format_date_mysql_datetime();
                            $data_card['createdUserName'] = current_user();
                            $data_card['createdUserGroup'] = user_group();
                            $data_card['timestamp'] = format_date_mysql_datetime();

                            $this->db->insert('srp_erp_pos_cardtopup', $data_card); // Sync DONE
                            $insert_id = $this->db->insert_id();
                            if ($insert_id > 0) {
                                $this->Pos_gift_card->insert_double_entries_giftCard($insert_id);
                            }
                        }
                    }
                    if (!empty($data_card)) {

                    } else {
                        if ($return) {
                            return json_encode(array('error' => 1, 'message' => 'Please enter the payment/s and top up again!'));
                        } else {
                            echo json_encode(array('error' => 1, 'message' => 'Please enter the payment/s and top up again!'));
                        }

                        exit;
                    }
                }
                if ($return) {
                    return json_encode(array('error' => 0, 'message' => 'Top up completed!', 'receiptID' => $SN, 'barcode' => $barCode));
                } else {
                    echo json_encode(array('error' => 0, 'message' => 'Top up completed!', 'receiptID' => $SN, 'barcode' => $barCode));
                }

            } else {
                if ($return) {
                    return json_encode(array('error' => 1, 'message' => 'Gift Card not issued.'));
                } else {
                    echo json_encode(array('error' => 1, 'message' => 'Gift Card not issued.'));
                }

            }
        }
    }

    function issueGiftCard()
    {
        $this->form_validation->set_rules('barcode', 'Bar Code', 'trim|required');
        $this->form_validation->set_rules('CustomerName', 'Customer Name', 'trim|required');
        $this->form_validation->set_rules('customerTelephone', 'Customer Telephone', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            /*$post = $this->input->post();
            print_r($post);
            exit;*/


            $barCode = trim($this->input->post('barcode'));
            $cardIssueID = $this->db->select('cardIssueID')->from('srp_erp_pos_cardissue')->where('barCode', $barCode)->get()->row();

            if (!$cardIssueID) {
                $companyInfo = get_companyInfo();

                $cardMaster = $this->db->select('*')->from('srp_erp_pos_giftcardmaster')->where('barcode', $barCode)->get()->row_array();


                $cardMaster['cardExpiryInMonths'];
                $expiry = '+' . $cardMaster['cardExpiryInMonths'] . ' months';
                $effectiveDate = format_date_mysql_datetime();
                $expiryDate = date('Y-m-d H:i:s', strtotime($expiry, time()));


                if (!empty($cardMaster)) { /*cardMasterID*/

                    $this->db->trans_start();
                    /** check customer exist */
                    $telephone = $this->input->post('customerTelephone');
                    $posCustomerAutoID = $this->db->select('posCustomerAutoID')->from('srp_erp_pos_customermaster')->where('customerTelephone', $telephone)->get()->row('posCustomerAutoID');
                    if (!$posCustomerAutoID) {

                        /** register this customer */
                        $customerInfo['CustomerName'] = $this->input->post('CustomerName');
                        $customerInfo['customerCountry'] = $companyInfo['company_country'];
                        $customerInfo['customerTelephone'] = $telephone;
                        $customerInfo['isActive'] = 1;
                        $customerInfo['companyID'] = current_companyID();
                        $customerInfo['companyCode'] = current_companyCode();
                        $customerInfo['createdUserGroup'] = user_group();
                        $customerInfo['createdPCID'] = current_pc();
                        $customerInfo['createdUserID'] = current_userID();
                        $customerInfo['createdUserName'] = current_user();
                        $customerInfo['createdDateTime'] = format_date_mysql_datetime();
                        $customerInfo['isCardHolder'] = 1;
                        $customerInfo['isFromERP'] = 0;


                        $result = $this->db->insert('srp_erp_pos_customermaster', $customerInfo);
                        //echo $this->db->last_query();
                        $posCustomerAutoID = $this->db->insert_id();
                    }


                    $curDatetime = format_date_mysql_datetime();

                    $this->topUpGiftCard(true);

                    $outletID = get_outletID();

                    /** issue card */
                    $data_cardIssue['cardMasterID'] = $cardMaster['cardMasterID'];
                    $data_cardIssue['barCode'] = $barCode;
                    $data_cardIssue['posCustomerAutoID'] = isset($posCustomerAutoID) & !empty($posCustomerAutoID) ? $posCustomerAutoID : 0;
                    $data_cardIssue['issuedDatetime'] = $curDatetime;
                    $data_cardIssue['expiryDate'] = $expiryDate;
                    $data_cardIssue['issuedOutletID'] = get_outletID();
                    $data_cardIssue['companyID'] = current_companyID();
                    $data_cardIssue['createdUserGroup'] = user_group();
                    $data_cardIssue['createdPCID'] = current_userID();
                    $data_cardIssue['createdUserID'] = current_userID();
                    $data_cardIssue['createdDateTime'] = $curDatetime;
                    $data_cardIssue['createdUserName'] = current_user();
                    $data_cardIssue['timestamp'] = $curDatetime;
                    /*SME-1305*/
                    $data_cardIssue['wareHouseAutoID'] = $outletID;
                    $data_cardIssue['id_store'] = $outletID;
                    $data_cardIssue['is_sync'] = 0;


                    $this->db->insert('srp_erp_pos_cardissue', $data_cardIssue);

                    $paymentTypes = $this->input->post('paymentTypes');
                    $references = $this->input->post('references');
                    $glConfigMasters = $this->input->post('glConfigMasters');
                    $giftCardGLAutoID = $this->Pos_gift_card->get_cardGlAutoID();
                    if (!empty($paymentTypes)) {
                        $SN = generateGiftCardSerialNo();
                        foreach ($paymentTypes as $key => $paymentAmount) {
                            if ($paymentAmount > 0) {
                                $gc_receipt = true;

                                $reference = $references[$key];
                                $glConfigMasterID = $glConfigMasters[$key];
                                $data_card['giftCardReceiptID'] = $SN;
                                $data_card['cardMasterID'] = $cardMaster['cardMasterID'];
                                $data_card['barCode'] = $barCode;
                                $data_card['posCustomerAutoID'] = $posCustomerAutoID;
                                $data_card['topUpAmount'] = $paymentAmount;
                                $data_card['points'] = 0;
                                $data_card['glConfigMasterID'] = $glConfigMasterID;
                                $data_card['glConfigDetailID'] = $key;
                                $data_card['menuSalesID'] = 0;
                                $data_card['giftCardGLAutoID'] = $giftCardGLAutoID;
                                $data_card['outletID'] = get_outletID();
                                $data_card['reference'] = $reference;
                                $data_card['companyID'] = current_companyID();
                                $data_card['companyCode'] = current_company_code();
                                $data_card['createdPCID'] = current_pc();
                                $data_card['createdUserID'] = current_userID();
                                $data_card['createdDateTime'] = format_date_mysql_datetime();
                                $data_card['createdUserName'] = current_user();
                                $data_card['createdUserGroup'] = user_group();
                                $data_card['timestamp'] = format_date_mysql_datetime();


                                $this->db->insert('srp_erp_pos_cardtopup', $data_card);
                                $insert_id = $this->db->insert_id();


                                if ($insert_id > 0) {
                                    $this->Pos_gift_card->insert_double_entries_giftCard($insert_id);
                                }
                            }
                        }
                        /*if (!empty($data_card)) {
                            $this->db->insert_batch('srp_erp_pos_cardtopup', $data_card);
                        }*/
                    }

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        echo json_encode(array('error' => 1, 'message' => 'error while inserting, message: ' . $this->db->_error_message()));
                    } else {
                        $this->db->trans_commit();
                        if (!isset($gc_receipt)) {
                            $SN = 0;
                            $barCode = 0;
                        }
                        echo json_encode(array('error' => 0, 'message' => 'Card successfully issued.', 'receiptID' => $SN, 'barcode' => $barCode));
                    }

                } else {
                    echo json_encode(array('error' => 1, 'message' => 'This Gift Card ("' . $barCode . '") is not issued by card providing center.'));
                }
            } else {
                echo json_encode(array('error' => 1, 'message' => 'This Gift Card ("' . $barCode . '") is already issued.'));
            }
        }
    }

    function loadHistoryGiftCard()
    {
        $barCode = $this->input->post('barCode');
        $this->datatables->select('topUpCard.cardTopUpID as cardTopUpID,topUpAmount, menuSalesID,outlet.wareHouseDescription as outlet,  outlet.wareHouseCode as wHouseCode, topUpCard.createdDateTime as createdDateTime', false)
            ->from('srp_erp_pos_cardtopup topUpCard')
            ->join('srp_erp_warehousemaster outlet', 'outlet.wareHouseAutoID = topUpCard.outletID')
            ->add_column('gc_outlet', '$1 - $2', 'wHouseCode , outlet')
            ->add_column('gc_date', '$1', 'get_giftCardDatetime(createdDateTime)')
            ->add_column('gc_time', '$1', 'get_giftCardDatetime(createdDateTime,\'t\')')
            ->add_column('gc_amount', '$1', 'get_numberFormat(topUpAmount)')
            ->add_column('description', '$1', 'get_history_description(topUpAmount)');
        $this->datatables->where('topUpCard.barCode', $barCode);
        $this->datatables->where('topUpCard.companyID', current_companyID());

        if (!empty($f_outletID)) {
            $this->datatables->where('barCode', $f_outletID);
        }

        $r = $this->datatables->generate();
        echo $r;
    }

    function load_customer_name_for_telephone_no()
    {
        $telephone = $this->input->post('telephone');
        $customerName = $this->db->select('CustomerName')->from('srp_erp_pos_customermaster')->where('customerTelephone', $telephone)->get()->row('CustomerName');
        if ($customerName) {

            echo json_encode(array('error' => 0, 'message' => 'done', 'CustomerName' => $customerName));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'customer not registered!'));
        }

    }

    function load_giftCard_receipt()
    {
        $receiptID = $this->input->post('receiptID');
        $barcode = $this->input->post('barcode');
        $data['giftCardInfo'] = $this->Pos_gift_card->get_giftCard_cardInformation($barcode);
        $data['giftCardPayments'] = $this->Pos_gift_card->get_giftCard_paymentInformation($receiptID);
        $this->load->view('system/pos/giftCard/gift_card_receipt', $data);

    }


}