<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class ERP_Controller extends CI_Controller
{
    var $common_data = array();
    var $db2;
    protected $getURL = array();
    protected $controllerName;

    function __construct()
    {
        parent::__construct();
        $CI =& get_instance();
        $this->controllerName = $CI->uri->segment(1);

        /** POST URLS  LIST */


        if ($this->controllerName == 'Profile') {
            $this->getURL[] = 'Profile/empProfile';
            $this->getURL[] = 'Profile/fetch_family_details';
            $this->getURL[] = 'Profile/load_empDocumentProfileView';
            $this->getURL[] = 'Profile/fetch_bank_details';
            $this->getURL[] = 'Profile/fetch_my_employee_list';
        }


        $this->getURL[] = 'Employee/load_empQualificationView';

        switch ($this->controllerName) {
            case "Pos_restaurant":
                $this->getURL[] = 'Pos_restaurant/get_outlet_cashier_Promotions';
                $this->getURL[] = 'Pos_restaurant/get_outlet_cashier_productmix';
                $this->getURL[] = 'Pos_restaurant/get_outlet_cashier_franchise';
                $this->getURL[] = 'Pos_restaurant/checkPosSession';
                $this->getURL[] = 'Pos_restaurant/updateCurrentMenuWAC';
                $this->getURL[] = 'Pos_restaurant/Load_pos_holdInvoiceData_withDiscount';
                $this->getURL[] = 'Pos_restaurant/LoadToInvoice';
                $this->getURL[] = 'Pos_restaurant/updateQty';
                $this->getURL[] = 'Pos_restaurant/updateCustomerType';
                $this->getURL[] = 'Pos_restaurant/load_void_receipt';
                $this->getURL[] = 'Pos_restaurant/loadVoidOrders';
                $this->getURL[] = 'Pos_restaurant/loadVaoidOrderHistory';
                $this->getURL[] = 'Pos_restaurant/loadPrintTemplateVoidHistory';
                $this->getURL[] = 'Pos_restaurant/save_send_pos_email';
                $this->getURL[] = 'Pos_restaurant/loadPrintTemplateVoid';
                $this->getURL[] = 'Pos_restaurant/void_bill';
                $this->getURL[] = 'Pos_restaurant/refreshDiningTables';
                $this->getURL[] = 'Pos_restaurant/delete_menuSalesItem';
                $this->getURL[] = 'Pos_restaurant/update_tableOrder';
                $this->getURL[] = 'Pos_restaurant/update_waiter_info';
                $this->getURL[] = 'Pos_restaurant/loadPrintTemplateSampleBill';
                $this->getURL[] = 'Pos_restaurant/loadPrintTemplate';
                $this->getURL[] = 'Pos_restaurant/load_pos_hold_receipt';
                $this->getURL[] = 'Pos_restaurant/loadHoldListPOS';
                $this->getURL[] = 'Pos_restaurant/loadDeliveryOrderPending';
                $this->getURL[] = 'Pos_restaurant/openHold_sales';
                $this->getURL[] = 'Pos_restaurant/clickPowerOff';
                $this->getURL[] = 'Pos_restaurant/cancelCurrentOrder';
                $this->getURL[] = 'Pos_restaurant/clearPosInvoiceSession';
                $this->getURL[] = 'Pos_restaurant/submitHoldReceipt';
                $this->getURL[] = 'Pos_restaurant/submit_pos_payments';
                $this->getURL[] = 'Pos_restaurant/savePackDetailItemList';
                $this->getURL[] = 'Pos_restaurant/load_packItemList';
                $this->getURL[] = 'Pos_restaurant/load_currencyDenominationPage';
                $this->getURL[] = 'Pos_restaurant/Load_pos_holdInvoiceData_tab';
                $this->getURL[] = 'Pos_restaurant/load_pos_hold_receipt_tablet';
                $this->getURL[] = 'Pos_restaurant/switchTable';
                $this->getURL[] = 'Pos_restaurant/get_outlet_cashier';
                $this->getURL[] = 'Pos_restaurant/loadPaymentSalesReportAdmin';
                $this->getURL[] = 'Pos_restaurant/load_item_wise_sales_report_admin';
                $this->getURL[] = 'Pos_restaurant/load_pos_detail_sales_report';
                break;

            case "Pos_dashboard":
                $this->getURL[] = 'Pos_dashboard/loadDashboard_sales_YTD';
                $this->getURL[] = 'Pos_dashboard/load_REVPASH';
                $this->getURL[] = 'Pos_dashboard/load_profitVsSales';
                $this->getURL[] = 'Pos_dashboard/load_invoicePax';
                $this->getURL[] = 'Pos_dashboard/load_fastMovingItemByValueMix';
                break;

            case "Pos_auth_process":
                $this->getURL[] = 'Pos_auth_process/check_has_pos_auth_process';
                $this->getURL[] = 'Pos_auth_process/check_pos_auth_process';
                break;

            case "Pos_kitchen":
                $this->getURL[] = 'Pos_kitchen/updateSendToKitchen';
                $this->getURL[] = 'Pos_kitchen/load_KOT_print_view';
                $this->getURL[] = 'Pos_kitchen/load_kitchen_ready';
                $this->getURL[] = 'Pos_kitchen/loadKitchenReady';
                $this->getURL[] = 'Pos_kitchen/loadKitchenStatusPreview';
                $this->getURL[] = 'Pos_kitchen/refreshOrderListContainer_autoPrint';
                $this->getURL[] = 'Pos_kitchen/kitchen_manual_process_ajax';
                break;

            case "Pos_giftCard":
                $this->getURL[] = 'Pos_giftCard/loadHistoryGiftCard';
                $this->getURL[] = 'Pos_giftCard/loadCustomerCardData';
                $this->getURL[] = 'Pos_giftCard/issueGiftCard';
                $this->getURL[] = 'Pos_giftCard/topUpGiftCard';
                break;
            case "Pos_general_report":
                $this->getURL[] = 'Pos_general_report/load_item_wise_sales_report_admin';
                $this->getURL[] = 'Pos_general_report/load_gpos_PaymentSalesReportAdmin';
                $this->getURL[] = 'Pos_general_report/load_gpos_detail_sales_report' ;
                break;
            case "ExpenseClaim":
                $this->getURL[] = 'ExpenseClaim/fetch_expanse_claim_approval';
                $this->getURL[] = 'ExpenseClaim/save_expense_Claim_approval';
                $this->getURL[] = 'ExpenseClaim/fetch_approval_user_modal_ec';
                $this->getURL[] = 'ExpenseClaim/delete_expense_claim';
                $this->getURL[] = 'ExpenseClaim/fetch_expanse_claim';
                $this->getURL[] = 'ExpenseClaim/referback_expense_claim';
                $this->getURL[] = 'ExpenseClaim/fetch_Ec_detail_table';
                $this->getURL[] = 'ExpenseClaim/load_expense_claim_header';
                $this->getURL[] = 'ExpenseClaim/save_expense_claim_header';
                $this->getURL[] = 'ExpenseClaim/fetch_expense_claim_detail';
                $this->getURL[] = 'ExpenseClaim/update_expense_claim_detail';
                $this->getURL[] = 'ExpenseClaim/save_expense_claim_detail';
                $this->getURL[] = 'ExpenseClaim/delete_expense_claim_detail';
                $this->getURL[] = 'ExpenseClaim/expense_claim_confirmation';
                break;
            case "Employee":
                $this->getURL[] = 'Employee/fetch_leave_conformation';
                $this->getURL[] = 'Employee/employeeLeave_detailsOnApproval';
                $this->getURL[] = 'Employee/leaveApproval';
                $this->getURL[] = 'Employee/fetch_leave_cancellation_approval';
                $this->getURL[] = 'Employee/familyimage_upload';
                $this->getURL[] = 'Employee/fetch_family_attachment_details';
                $this->getURL[] = 'Employee/familyattachment_uplode';
                $this->getURL[] = 'Employee/delete_family_attachment';
                $this->getURL[] = 'Employee/delete_familydetail';
                $this->getURL[] = 'Employee/saveFamilyDetails';
                $this->getURL[] = 'Employee/load_empQualificationView';
                $this->getURL[] = 'Employee/getAcademicData';
                $this->getURL[] = 'Employee/updateAcademic';
                $this->getURL[] = 'Employee/deleteAcademic';
                $this->getURL[] = 'Employee/saveAcademic';
                $this->getURL[] = 'Employee/editQualification';
                $this->getURL[] = 'Employee/saveQualification';
                $this->getURL[] = 'Employee/deleteQualification';
                $this->getURL[] = 'Employee/bankBranches';
                $this->getURL[] = 'Employee/emp_documentSave';
                $this->getURL[] = 'Employee/new_employee_details';
                $this->getURL[] = 'Employee/fetch_employee_leave';
                $this->getURL[] = 'Employee/loadLeaveBalance';
                $this->getURL[] = 'Employee/loadLeaveBalanceHistory';
                $this->getURL[] = 'Employee/employee_leave_page';
                $this->getURL[] = 'Employee/employeeLeave_details';
                $this->getURL[] = 'Employee/get_covering_employee_list';
                $this->getURL[] = 'Employee/leaveEmployeeCalculation';
                $this->getURL[] = 'Employee/loadLeaveTypeDropDown';
                $this->getURL[] = 'Employee/employeeLeaveSummery';
                $this->getURL[] = 'Employee/update_employeesLeave';
                $this->getURL[] = 'Employee/refer_back_empLeave';
                $this->getURL[] = 'Employee/delete_empLeave';
                $this->getURL[] = 'Employee/cancel_leave';
                $this->getURL[] = 'Employee/save_employeesLeave';
                break;
            case "Approvel_user":
                $this->getURL[] = 'Approvel_user/fetch_all_approval_users_modal';
                $this->getURL[] = 'Approvel_user/fetch_approval_user_modal';
                $this->getURL[] = 'Approvel_user/fetch_reject_user_modal';
                break;
            case "Profile":
                $this->getURL[] = 'Profile/empProfile';
                $this->getURL[] = 'Profile/fetch_family_details';
                $this->getURL[] = 'Profile/load_empDocumentProfileView';
                $this->getURL[] = 'Profile/update_empDetail';
                $this->getURL[] = 'Profile/ajax_update_familydetails';
                $this->getURL[] = 'Profile/fetch_bank_details';
                $this->getURL[] = 'Profile/fetch_my_employee_list';
                break;
            case "template_paySheet":
                $this->getURL[] = 'template_paySheet/get_paySlip_profile';
                break;
            case "Attachment":
                $this->getURL[] = 'Attachment/delete_attachments';
                $this->getURL[] = 'Attachment/do_upload';
                $this->getURL[] = 'Attachment/fetch_attachments';
                break;
            case "Crmlead":
                $this->getURL[] = 'Crmlead/load_myprofile_SalesTargetAchievedManagement_view';
                $this->getURL[] = 'CrmLead/load_edit_salesTarget_achieved_profile';
                $this->getURL[] = 'Crmlead/save_sales_targetAchieved_header_profile';
                $this->getURL[] = 'Crmlead/delete_salesTarget_Acheived_profile';
                $this->getURL[] = 'CrmLead/save_salesTarget_achived_multiple';
                break;
            case "MFQ_Job":
                $this->getURL[] = 'MFQ_Job/fetch_job_approval';
                $this->getURL[] = 'MFQ_Job/fetch_job_approval_print';
                $this->getURL[] = 'MFQ_Job/save_job_approval';
                $this->getURL[] = 'MFQ_Job/fetch_job';
                $this->getURL[] = 'MFQ_Job/save_sub_job';
                $this->getURL[] = 'MFQ_Job/get_mfq_job_drilldown';
                $this->getURL[] = 'MFQ_Job/load_job_header';
                $this->getURL[] = 'MFQ_Job/get_workflow_status';
                $this->getURL[] = 'MFQ_Job/load_route_card';
                $this->getURL[] = 'MFQ_Job/save_route_card';
                $this->getURL[] = 'MFQ_Job/delete_routecard';
                $this->getURL[] = 'MFQ_Job/get_mfq_job_drilldown2';
                $this->getURL[] = 'MFQ_Job/load_material_consumption_qty';
                $this->getURL[] = 'MFQ_Job/save_usage_qty';
                $this->getURL[] = 'MFQ_Job/referback_job';
                $this->getURL[] = 'MFQ_Job/load_unit_of_measure';
                break;
            case "MFQ_CustomerInquiry":
                $this->getURL[] = 'MFQ_CustomerInquiry/fetch_customer_inquiry_approval';
                $this->getURL[] = 'MFQ_CustomerInquiry/fetch_customer_inquiry_print';
                $this->getURL[] = 'MFQ_CustomerInquiry/save_customer_inquiry_approval';
                $this->getURL[] = 'MFQ_CustomerInquiry/fetch_customerInquiry';
                $this->getURL[] = 'MFQ_CustomerInquiry/generateEstimate';
                break;
            case "MFQ_Estimate":
                $this->getURL[] = 'MFQ_Estimate/fetch_estimate_approval';
                $this->getURL[] = 'MFQ_Estimate/save_estimate_approval';
                $this->getURL[] = 'MFQ_Estimate/load_mfq_estimate_detail';
                $this->getURL[] = 'MFQ_Estimate/fetch_job_order_view';
                $this->getURL[] = 'MFQ_Estimate/load_mfq_estimate';
                $this->getURL[] = 'MFQ_Estimate/delete_estimateDetail';
                $this->getURL[] = 'MFQ_Estimate/fetch_customer_inquiry';
                $this->getURL[] = 'MFQ_Estimate/load_mfq_customerInquiryDetail';
                $this->getURL[] = 'MFQ_Estimate/save_EstimateDetail';
                $this->getURL[] = 'MFQ_Estimate/fetch_estimate';
                $this->getURL[] = 'MFQ_Estimate/load_mfq_estimate_version';
                $this->getURL[] = 'MFQ_Estimate/confirm_Estimate';
                break;
            case "MFQ_Dashboard":
                $this->getURL[] = 'MFQ_Dashboard/fetch_job_status';
                $this->getURL[] = 'MFQ_Dashboard/fetch_machine';
                $this->getURL[] = 'MFQ_Dashboard/fetch_ongoing_job';
                $this->getURL[] = 'MFQ_Dashboard/load_erp_warehouse';
                break;
            case "MFQ_Job_Card":
                $this->getURL[] = 'MFQ_Job_Card/fetch_job_detail';
                $this->getURL[] = 'MFQ_Job_Card/fetch_po_unit_cost';
                $this->getURL[] = 'MFQ_Job_Card/fetch_material_by_id';
                break;
            case "MFQ_Template":
                $this->getURL[] = 'MFQ_Template/fetch_workprocess_detail';
                $this->getURL[] = 'MFQ_Template/get_workflow_template';
                $this->getURL[] = 'MFQ_Template/load_workflow_process_design';
                $this->getURL[] = 'MFQ_Template/fetch_workflow_template';
                $this->getURL[] = 'MFQ_Template/save_work_flow_template';
                $this->getURL[] = 'MFQ_Template/edit_work_flow_template';
                $this->getURL[] = 'MFQ_Template/fetch_template';
                $this->getURL[] = 'MFQ_Template/load_template_master_header';
                $this->getURL[] = 'MFQ_Template/load_workflow_design';
                $this->getURL[] = 'MFQ_Template/save_mfq_template_header';
                $this->getURL[] = 'MFQ_Template/save_mfq_template_detail';
                $this->getURL[] = 'MFQ_Template/delete_workflow_detail';
                $this->getURL[] = 'MFQ_Template/delete_workflow_master';
                break;
            case "MFQ_BillOfMaterial":
                $this->getURL[] = 'MFQ_BillOfMaterial/checkItemInBom';
                $this->getURL[] = 'MFQ_BillOfMaterial/load_mfq_billOfMaterial';
                $this->getURL[] = 'MFQ_BillOfMaterial/load_mfq_billOfMaterial_detail';
                $this->getURL[] = 'MFQ_BillOfMaterial/delete_materialConsumption';
                $this->getURL[] = 'MFQ_BillOfMaterial/load_segment_hours';
                $this->getURL[] = 'MFQ_BillOfMaterial/delete_labour_task';
                $this->getURL[] = 'MFQ_BillOfMaterial/delete_overhead_cost';
                $this->getURL[] = 'MFQ_BillOfMaterial/add_edit_BillOfMaterial';
                $this->getURL[] = 'MFQ_BillOfMaterial/fetch_bom';
                break;
            case "MFQ_ItemMaster":
                $this->getURL[] = 'MFQ_ItemMaster/fetch_item';
                $this->getURL[] = 'MFQ_ItemMaster/fetch_link_item';
                $this->getURL[] = 'MFQ_ItemMaster/fetch_sync_item';
                $this->getURL[] = 'MFQ_ItemMaster/assign_itemCategory_children';
                $this->getURL[] = 'MFQ_ItemMaster/get_mfq_subCategory';
                $this->getURL[] = 'MFQ_ItemMaster/load_mfq_itemMaster';
                $this->getURL[] = 'MFQ_ItemMaster/add_edit_mfq_item';
                break;
            case "ItemMaster":
                $this->getURL[] = 'ItemMaster/load_subcat';
                $this->getURL[] = 'ItemMaster/load_subsubcat';
                $this->getURL[] = 'ItemMaster/fetch_sales_price';
                $this->getURL[] = 'ItemMaster/load_item_header';
                $this->getURL[] = 'ItemMaster/load_category_type_id';
                $this->getURL[] = 'ItemMaster/load_item_bin_location';
                $this->getURL[] = 'ItemMaster/load_sub_itemMaster_view';
                $this->getURL[] = 'ItemMaster/save_itemmaster';
                $this->getURL[] = 'ItemMaster/save_item_bin_location';
                break;
            case "MFQ_OverHead":
                $this->getURL[] = 'MFQ_OverHead/fetch_over_head';
                $this->getURL[] = 'MFQ_OverHead/save_over_head';
                $this->getURL[] = 'MFQ_OverHead/editOverHead';
                $this->getURL[] = 'MFQ_OverHead/fetch_labour';
                $this->getURL[] = 'MFQ_OverHead/save_labour';
                break;
            case "MFQ_AssetMaster":
                $this->getURL[] = 'MFQ_AssetMaster/fetch_asset';
                $this->getURL[] = 'MFQ_AssetMaster/fetch_sync_asset';
                $this->getURL[] = 'MFQ_AssetMaster/add_Asset';
                $this->getURL[] = 'MFQ_AssetMaster/load_mfq_Machine';
                $this->getURL[] = 'MFQ_AssetMaster/add_edit_mfq_machine';
                $this->getURL[] = 'MFQ_AssetMaster/assign_itemCategory_children';
                break;
            case "MFQ_CrewMaster":
                $this->getURL[] = 'MFQ_CrewMaster/fetch_crew';
                $this->getURL[] = 'MFQ_CrewMaster/fetch_sync_crew';
                $this->getURL[] = 'MFQ_CrewMaster/add_edit_crew';
                $this->getURL[] = 'MFQ_CrewMaster/loadCrewDetail';
                $this->getURL[] = 'MFQ_CrewMaster/add_crews';
                break;
            case "MFQ_CustomerMaster":
                $this->getURL[] = 'MFQ_CustomerMaster/fetch_customer';
                $this->getURL[] = 'MFQ_CustomerMaster/fetch_sync_customer';
                $this->getURL[] = 'MFQ_CustomerMaster/loadCustomerDetail';
                $this->getURL[] = 'MFQ_CustomerMaster/delete_mail';
                $this->getURL[] = 'MFQ_CustomerMaster/add_edit_customer';
                $this->getURL[] = 'MFQ_CustomerMaster/add_customers';
                $this->getURL[] = 'MFQ_CustomerMaster/fetch_link_customer';
                $this->getURL[] = 'MFQ_CustomerMaster/link_customer';
                break;
            case "MFQ_SegmentMaster":
                $this->getURL[] = 'MFQ_SegmentMaster/fetch_segments';
                $this->getURL[] = 'MFQ_SegmentMaster/fetch_sync_segment';
                $this->getURL[] = 'MFQ_SegmentMaster/fetch_link_segment';
                $this->getURL[] = 'MFQ_SegmentMaster/link_segment';
                $this->getURL[] = 'MFQ_SegmentMaster/add_edit_segment';
                $this->getURL[] = 'MFQ_SegmentMaster/loadSegmentDetail';
                $this->getURL[] = 'MFQ_SegmentMaster/add_segments';
                break;
            case "MFQ_SystemSettings":
                $this->getURL[] = 'MFQ_SystemSettings/settings_users';
                $this->getURL[] = 'MFQ_SystemSettings/fetch_doc_status';
                $this->getURL[] = 'MFQ_SystemSettings/create_document_status';
                $this->getURL[] = 'MFQ_SystemSettings/get_alldocumentStatus';
                $this->getURL[] = 'MFQ_SystemSettings/deleteDocumentStatus';
                break;
            case "MFQ_warehouse":
                $this->getURL[] = 'MFQ_warehouse/fetch_warehouse';
                $this->getURL[] = 'MFQ_warehouse/fetch_sync_warehouse';
                $this->getURL[] = 'MFQ_warehouse/fetch_link_warehouse';
                $this->getURL[] = 'MFQ_warehouse/edit_warehouse';
                $this->getURL[] = 'MFQ_warehouse/save_warehouse';
                $this->getURL[] = 'MFQ_warehouse/add_warehouse';
                break;
            case "MFQ_UserGroup":
                $this->getURL[] = 'MFQ_UserGroup/fetch_usergroup';
                $this->getURL[] = 'MFQ_UserGroup/fetch_employees';
                $this->getURL[] = 'MFQ_UserGroup/fetch_savedusergroup';
                $this->getURL[] = 'MFQ_UserGroup/delete_employee';
                $this->getURL[] = 'MFQ_UserGroup/link_employee';
                $this->getURL[] = 'MFQ_UserGroup/edit_mfq_user';
                $this->getURL[] = 'MFQ_UserGroup/save_mfq_user';
                $this->getURL[] = 'MFQ_UserGroup/delete_details_group_table';
                break;
            case "mfq_standard_details":
                $this->getURL[] = 'mfq_standard_details/fetch_standard_details';
                $this->getURL[] = 'mfq_standard_details/save_mfq_standard_details';
                $this->getURL[] = 'mfq_standard_details/load_mfq_standard_details';
                $this->getURL[] = 'mfq_standard_details/delete_standard_details';
                break;
            case "mfq_masters":
                $this->getURL[] = 'mfq_masters/load_mfq_category';
                $this->getURL[] = 'mfq_masters/save_itemCategory';
                $this->getURL[] = 'mfq_masters/update_itemCategory';
                break;
            case "MFQ_CustomerInvoice":
                $this->getURL[] = 'MFQ_CustomerInvoice/fetch_customer_invoice';
                $this->getURL[] = 'MFQ_CustomerInvoice/load_mfq_customerInvoice';
                $this->getURL[] = 'MFQ_CustomerInvoice/load_mfq_customerInvoiceDetail';
                $this->getURL[] = 'MFQ_CustomerInvoice/fetch_delivery_note';
                $this->getURL[] = 'MFQ_CustomerInvoice/delete_customerInvoiceDetail';
                $this->getURL[] = 'MFQ_CustomerInvoice/save_customer_invoice';
                $this->getURL[] = 'MFQ_CustomerInvoice/customer_invoice_confirmation';
                break;
            case "MFQ_DeliveryNote":
                $this->getURL[] = 'MFQ_DeliveryNote/fetch_delivery_note';
                $this->getURL[] = 'MFQ_DeliveryNote/referback_delivery_note';
                $this->getURL[] = 'MFQ_DeliveryNote/delete_delivery_note';
                $this->getURL[] = 'MFQ_DeliveryNote/load_delivery_note_header';
                $this->getURL[] = 'MFQ_DeliveryNote/fetch_customer_jobs';
                $this->getURL[] = 'MFQ_DeliveryNote/delivery_note_confirmation';
                $this->getURL[] = 'MFQ_DeliveryNote/save_delivery_note_header';
                break;
            case "Invoices":
                $this->getURL[] = 'Invoices/fetch_invoices_approval';
                $this->getURL[] = 'Invoices/save_invoice_approval';
                $this->getURL[] = 'Invoices/referback_customer_invoice';
                $this->getURL[] = 'Invoices/load_invoice_header';
                $this->getURL[] = 'Invoices/fetch_detail';
                $this->getURL[] = 'Invoices/fetch_invoice_direct_details';
                $this->getURL[] = 'Invoices/fetch_invoices';
                $this->getURL[] = 'Invoices/invoiceloademail';
                $this->getURL[] = 'Invoices/delete_invoice_master';
                $this->getURL[] = 'Invoices/re_open_invoice';
                $this->getURL[] = 'Invoices/save_direct_invoice_detail';
                $this->getURL[] = 'Invoices/delete_item_direct';
                $this->getURL[] = 'Invoices/save_invoice_item_detail';
                $this->getURL[] = 'Invoices/fetch_customer_invoice_all_detail_edit';
                $this->getURL[] = 'Invoices/updateCustomerInvoice_edit_all_Item';
                $this->getURL[] = 'Invoices/customerinvoiceGLUpdate';
                $this->getURL[] = 'Invoices/fetch_customer_invoice_detail';
                $this->getURL[] = 'Invoices/update_invoice_item_detail';
                $this->getURL[] = 'Invoices/save_invoice_header';
                $this->getURL[] = 'Invoices/fetch_con_detail_table';
                $this->getURL[] = 'Invoices/save_con_base_items';
                $this->getURL[] = 'Invoices/invoice_confirmation';
                $this->getURL[] = 'Invoices/send_invoice_email';
                $this->getURL[] = 'Invoices/load_subItemList';
                break;
            case "InvoicesPercentage":
                $this->getURL[] = 'InvoicesPercentage/fetch_invoices_approval';
                break;
            case "Inventory":
                $this->getURL[] = 'Inventory/fetch_sales_return_approval';
                $this->getURL[] = 'Inventory/save_sales_return_approval';
                $this->getURL[] = 'Inventory/stockAdjustment_load_gldropdown';
                $this->getURL[] = 'Inventory/fetch_sales_return_table';
                $this->getURL[] = 'Inventory/referback_sales_return';
                $this->getURL[] = 'Inventory/delete_sales_return';
                $this->getURL[] = 'Inventory/load_sales_return_header';
                $this->getURL[] = 'Inventory/fetch_sales_return_detail';
                $this->getURL[] = 'Inventory/fetch_return_direct_details';
                $this->getURL[] = 'Inventory/fetch_sales_return_details';
                $this->getURL[] = 'Inventory/save_sales_return_header';
                $this->getURL[] = 'Inventory/delete_sales_return_detail';
                $this->getURL[] = 'Inventory/fetch_item_for_sales_return';
                $this->getURL[] = 'Inventory/save_sales_return_detail_items';
                $this->getURL[] = 'Inventory/sales_return_confirmation';
                $this->getURL[] = 'Inventory/fetch_stock_return_approval';
                $this->getURL[] = 'Inventory/save_stock_return_approval';
                $this->getURL[] = 'Inventory/fetch_material_issue_mc';
                $this->getURL[] = 'Inventory/fetch_material_issue_approval';
                $this->getURL[] = 'Inventory/save_material_issue_approval';
                $this->getURL[] = 'Inventory/fetch_material_request_approval';
                $this->getURL[] = 'Inventory/save_material_request_approval';
                $this->getURL[] = 'Inventory/fetch_stock_transfer_approval';
                $this->getURL[] = 'Inventory/save_stock_transfer_approval';
                $this->getURL[] = 'Inventory/fetch_stock_adjustment_approval';
                $this->getURL[] = 'Inventory/save_stock_adjustment_approval';
                $this->getURL[] = 'Inventory/fetch_stock_return_table';
                $this->getURL[] = 'Inventory/laad_stock_return_header';
                $this->getURL[] = 'Inventory/fetch_stock_return_detail';
                $this->getURL[] = 'Inventory/fetch_item_for_grv';
                $this->getURL[] = 'Inventory/save_grv_base_items';
                $this->getURL[] = 'Inventory/delete_return_detail';
                $this->getURL[] = 'Inventory/stock_return_confirmation';
                $this->getURL[] = 'Inventory/re_open_stock_return';
                $this->getURL[] = 'Inventory/referback_stock_return';
                $this->getURL[] = 'Inventory/delete_purchase_return';
                $this->getURL[] = 'Inventory/fetch_material_request';
                $this->getURL[] = 'Inventory/load_material_request_header';
                $this->getURL[] = 'Inventory/delete_material_request_header';
                $this->getURL[] = 'Inventory/re_open_material_request';
                $this->getURL[] = 'Inventory/referback_materialrequest';
                $this->getURL[] = 'Inventory/fetch_material_request_detail';
                $this->getURL[] = 'Inventory/load_material_request_detail';
                $this->getURL[] = 'Inventory/save_material_request_detail';
                $this->getURL[] = 'Inventory/delete_material_request_item';
                $this->getURL[] = 'Inventory/save_material_request_detail_multiple';
                $this->getURL[] = 'Inventory/save_material_request_detail_multiple_edit';
                $this->getURL[] = 'Inventory/material_request_item_confirmation';
                $this->getURL[] = 'Inventory/fetch_material_issue';
                $this->getURL[] = 'Inventory/referback_materialissue';
                $this->getURL[] = 'Inventory/delete_material_issue_header';
                $this->getURL[] = 'Inventory/re_open_material_issue';
                $this->getURL[] = 'Inventory/load_material_issue_header';
                $this->getURL[] = 'Inventory/fetch_material_item_detail';
                $this->getURL[] = 'Inventory/materialAccountUpdate';
                $this->getURL[] = 'Inventory/load_material_item_detail';
                $this->getURL[] = 'Inventory/save_material_detail';
                $this->getURL[] = 'Inventory/delete_material_item';
                $this->getURL[] = 'Inventory/fetch_MR_code';
                $this->getURL[] = 'Inventory/fetch_mr_detail_table';
                $this->getURL[] = 'Inventory/save_mr_base_items';
                $this->getURL[] = 'Inventory/material_item_confirmation';
                $this->getURL[] = 'Inventory/save_material_issue_header';
                $this->getURL[] = 'Inventory/fetch_stock_transfer';
                $this->getURL[] = 'Inventory/re_open_stock_transfer';
                $this->getURL[] = 'Inventory/delete_stocktransfer_master';
                $this->getURL[] = 'Inventory/referback_stock_transfer';
                $this->getURL[] = 'Inventory/fetch_stockTransfer_detail_table';
                $this->getURL[] = 'Inventory/laad_stock_transfer_header';
                $this->getURL[] = 'Inventory/save_stock_transfer_header';
                $this->getURL[] = 'Inventory/fetch_st_warehouse_item';
                $this->getURL[] = 'Inventory/save_stock_transfer_detail_multiple';
                $this->getURL[] = 'Inventory/load_stock_transfer_item_detail';
                $this->getURL[] = 'Inventory/save_stock_transfer_detail';
                $this->getURL[] = 'Inventory/fetch_stockTransfer_all_detail_edit';
                $this->getURL[] = 'Inventory/save_stock_transfer_detail_edit_all_multiple';
                $this->getURL[] = 'Inventory/stock_transfer_confirmation';
                $this->getURL[] = 'Inventory/fetch_stock_adjustment_table';
                $this->getURL[] = 'Inventory/referback_stock_adjustment';
                $this->getURL[] = 'Inventory/delete_stock_adjustment';
                $this->getURL[] = 'Inventory/re_open_stock_adjestment';
                $this->getURL[] = 'Inventory/fetch_stock_adjustment_detail';
                $this->getURL[] = 'Inventory/laad_stock_adjustment_header';
                $this->getURL[] = 'Inventory/fetch_warehouse_item_adjustment';
                $this->getURL[] = 'Inventory/save_stock_adjustment_detail_multiple';
                $this->getURL[] = 'Inventory/stockadjustmentAccountUpdate';
                $this->getURL[] = 'Inventory/load_adjustment_item_detail';
                $this->getURL[] = 'Inventory/save_stock_adjustment_detail';
                $this->getURL[] = 'Inventory/delete_adjustment_item';
                $this->getURL[] = 'Inventory/stock_adjustment_confirmation';
                break;
            case "inventory":
                $this->getURL[] = 'inventory/delete_stockTransfer_details';
                break;
            case "Sales":
                $this->getURL[] = 'Sales/fetch_sales_commission';
                $this->getURL[] = 'Sales/fetch_detail_header_lock';
                $this->getURL[] = 'Sales/fetch_inv_detail';
                $this->getURL[] = 'Sales/laad_sales_commision_header';
                $this->getURL[] = 'Sales/save_sales_commision_header';
                $this->getURL[] = 'Sales/sales_commission_detail';
                $this->getURL[] = 'Sales/sc_confirmation';
                $this->getURL[] = 'Sales/referbacksc';
                $this->getURL[] = 'Sales/delete_sc';
                $this->getURL[] = 'Sales/save_sales_person';
                break;
            case "sales":
                $this->getURL[] = 'sales/fetch_sc_approval';
                $this->getURL[] = 'sales/save_sc_approval';
                break;
            case "Payment_voucher":
                $this->getURL[] = 'Payment_voucher/fetch_commission_payment_approval';
                $this->getURL[] = 'Payment_voucher/save_pv_approval';
                $this->getURL[] = 'Payment_voucher/fetch_commission_payment';
                $this->getURL[] = 'Payment_voucher/referback_payment_voucher';
                $this->getURL[] = 'Payment_voucher/load_payment_voucher_header';
                $this->getURL[] = 'Payment_voucher/fetch_detail';
                $this->getURL[] = 'Payment_voucher/fetch_pv_direct_details';
                $this->getURL[] = 'Payment_voucher/save_paymentvoucher_header';
                $this->getURL[] = 'Payment_voucher/save_commission_base_items';
                $this->getURL[] = 'Payment_voucher/delete_item_direct';
                $this->getURL[] = 'Payment_voucher/payment_confirmation';
                $this->getURL[] = 'Payment_voucher/fetch_payment_voucher_approval';
                $this->getURL[] = 'Payment_voucher/fetch_payment_voucher';
                $this->getURL[] = 'Payment_voucher/delete_payment_voucher';
                $this->getURL[] = 'Payment_voucher/load_Cheque_templates';
                $this->getURL[] = 'Payment_voucher/delete_tax_detail';
                $this->getURL[] = 'Payment_voucher/save_inv_tax_detail';
                $this->getURL[] = 'Payment_voucher/re_open_payment_voucher';
                $this->getURL[] = 'Payment_voucher/save_pv_item_detail_multiple';
                $this->getURL[] = 'Payment_voucher/fetch_payment_voucher_detail';
                $this->getURL[] = 'Payment_voucher/save_pv_item_detail';
                $this->getURL[] = 'Payment_voucher/save_direct_pv_detail_multiple';
                $this->getURL[] = 'Payment_voucher/load_html';
                $this->getURL[] = 'Payment_voucher/save_pv_po_detail';
                $this->getURL[] = 'Payment_voucher/save_debitNote_base_items';
                $this->getURL[] = 'Payment_voucher/save_inv_base_items';
                $this->getURL[] = 'Payment_voucher/fetch_payment_match';
                $this->getURL[] = 'Payment_voucher/referback_payment_match';
                $this->getURL[] = 'Payment_voucher/delete_payment_match';
                $this->getURL[] = 'Payment_voucher/re_open_payment_match';
                $this->getURL[] = 'Payment_voucher/load_payment_match_header';
                $this->getURL[] = 'Payment_voucher/fetch_match_detail';
                $this->getURL[] = 'Payment_voucher/delete_pv_match_detail';
                $this->getURL[] = 'Payment_voucher/fetch_pv_advance_detail';
                $this->getURL[] = 'Payment_voucher/save_match_amount';
                $this->getURL[] = 'Payment_voucher/payment_match_confirmation';
                break;
            case "Grv":
                $this->getURL[] = 'Grv/load_itemMasterSub_approval';
                $this->getURL[] = 'Grv/fetch_grv';
                $this->getURL[] = 'Grv/referback_grv';
                $this->getURL[] = 'Grv/delete_grv';
                $this->getURL[] = 'Grv/load_grv_header';
                $this->getURL[] = 'Grv/fetch_detail';
                $this->getURL[] = 'Grv/fetch_detail_header_lock';
                $this->getURL[] = 'Grv/fetch_po_detail_table';
                $this->getURL[] = 'Grv/save_po_base_items';
                $this->getURL[] = 'Grv/fetch_grv_detail';
                $this->getURL[] = 'Grv/save_grv_detail';
                $this->getURL[] = 'Grv/delete_grv_detail';
                $this->getURL[] = 'Grv/fetch_addons';
                $this->getURL[] = 'Grv/save_addon';
                $this->getURL[] = 'Grv/fetch_all_item';
                $this->getURL[] = 'Grv/get_addon_details_projectBase';
                $this->getURL[] = 'Grv/delete_addondetails';
                $this->getURL[] = 'Grv/save_grv_header';
                $this->getURL[] = 'Grv/save_grv_st_bulk_detail';
                $this->getURL[] = 'Grv/grv_confirmation';
                $this->getURL[] = 'Grv/fetch_grv_approval';
                $this->getURL[] = 'Grv/save_grv_approval';
                $this->getURL[] = 'Grv/fetch_addon_data';
                $this->getURL[] = 'Grv/edit_addonmaster';
                $this->getURL[] = 'Grv/save_addonmaster';
                $this->getURL[] = 'Grv/delete_addonmaster';
                break;
            case "Quotation_contract":
                $this->getURL[] = 'Quotation_contract/fetch_Quotation_contract';
                $this->getURL[] = 'Quotation_contract/document_drill_down_View_modal';
                $this->getURL[] = 'Quotation_contract/delete_con_master';
                $this->getURL[] = 'Quotation_contract/load_contract_header';
                $this->getURL[] = 'Quotation_contract/fetch_item_detail_table';
                $this->getURL[] = 'Quotation_contract/save_quotation_contract_header';
                $this->getURL[] = 'Quotation_contract/fetch_quotation_contract_approval';
                $this->getURL[] = 'Quotation_contract/save_quotation_contract_approval';
                $this->getURL[] = 'Quotation_contract/delete_item_detail';
                $this->getURL[] = 'Quotation_contract/load_unitprice_exchangerate';
                $this->getURL[] = 'Quotation_contract/save_item_order_detail';
                $this->getURL[] = 'Quotation_contract/fetch_item_detail';
                $this->getURL[] = 'Quotation_contract/fetch_documentID';
                $this->getURL[] = 'Quotation_contract/contract_confirmation';
                $this->getURL[] = 'Quotation_contract/referback_Quotation_contract';
                $this->getURL[] = 'Quotation_contract/loademail';
                $this->getURL[] = 'Quotation_contract/re_open_contract';
                break;
            case "Payable":
                $this->getURL[] = 'Payable/fetch_customer_currency_by_id';
                $this->getURL[] = 'Payable/fetch_supplier_invoice_approval';
                $this->getURL[] = 'Payable/save_supplier_invoice_approval';
                $this->getURL[] = 'Payable/fetch_debit_note_approval';
                $this->getURL[] = 'Payable/save_dn_approval';
                $this->getURL[] = 'Payable/fetch_supplier_invoices';
                $this->getURL[] = 'Payable/re_open_supplier_invoice';
                $this->getURL[] = 'Payable/referback_supplierinvoice';
                $this->getURL[] = 'Payable/delete_supplier_invoice';
                $this->getURL[] = 'Payable/supplier_invoice_confirmation';
                $this->getURL[] = 'Payable/laad_supplier_invoice_header';
                $this->getURL[] = 'Payable/fetch_supplier_invoice_detail';
                $this->getURL[] = 'Payable/fetch_detail_header_lock';
                $this->getURL[] = 'Payable/save_supplier_invoice_header';
                $this->getURL[] = 'Payable/save_bsi_detail_multiple';
                $this->getURL[] = 'Payable/fetch_bsi_detail';
                $this->getURL[] = 'Payable/save_bsi_detail';
                $this->getURL[] = 'Payable/delete_bsi_detail';
                $this->getURL[] = 'Payable/save_grv_base_items';
                $this->getURL[] = 'Payable/fetch_debit_note';
                $this->getURL[] = 'Payable/referback_dn';
                $this->getURL[] = 'Payable/dn_confirmation';
                $this->getURL[] = 'Payable/delete_dn';
                $this->getURL[] = 'Payable/load_debit_note_header';
                $this->getURL[] = 'Payable/fetch_dn_detail_table';
                $this->getURL[] = 'Payable/delete_dn_detail';
                $this->getURL[] = 'Payable/save_debitNote_detail_GLCode_multiple';
                $this->getURL[] = 'Payable/fetch_supplier_invoice';
                $this->getURL[] = 'Payable/save_debit_base_items';
                $this->getURL[] = 'Payable/re_open_dn';
                break;
            case "Company":
                $this->getURL[] = 'Company/currency_validation';
                break;
            case "Receivable":
                $this->getURL[] = 'Receivable/save_inv_tax_detail';
                $this->getURL[] = 'Receivable/delete_tax_detail';
                $this->getURL[] = 'Receivable/fetch_credit_note_approval';
                $this->getURL[] = 'Receivable/save_cn_approval';
                $this->getURL[] = 'Receivable/fetch_credit_note';
                $this->getURL[] = 'Receivable/referback_credit_note';
                $this->getURL[] = 'Receivable/delete_creditNote_master';
                $this->getURL[] = 'Receivable/re_open_credit_note';
                $this->getURL[] = 'Receivable/fetch_cn_detail_table';
                $this->getURL[] = 'Receivable/load_credit_note_header';
                $this->getURL[] = 'Receivable/save_creditnote_header';
                $this->getURL[] = 'Receivable/fetch_custemer_data_invoice';
                $this->getURL[] = 'Receivable/save_credit_base_items';
                $this->getURL[] = 'Receivable/delete_cn_detail';
                $this->getURL[] = 'Receivable/cn_confirmation';
                break;
            case "Receipt_voucher":
                $this->getURL[] = 'Receipt_voucher/fetch_rv_warehouse_item';
                $this->getURL[] = 'Receipt_voucher/fetch_Receipt_voucher_approval';
                $this->getURL[] = 'Receipt_voucher/save_rv_approval';
                $this->getURL[] = 'Receipt_voucher/fetch_receipt_voucher';
                $this->getURL[] = 'Receipt_voucher/referback_receipt_voucher';
                $this->getURL[] = 'Receipt_voucher/delete_receipt_voucher';
                $this->getURL[] = 'Receipt_voucher/re_open_receipt_voucher';
                $this->getURL[] = 'Receipt_voucher/load_receipt_voucher_header';
                $this->getURL[] = 'Receipt_voucher/fetch_detail';
                $this->getURL[] = 'Receipt_voucher/fetch_rv_details';
                $this->getURL[] = 'Receipt_voucher/save_rv_item_detail';
                $this->getURL[] = 'Receipt_voucher/fetch_income_all_detail';
                $this->getURL[] = 'Receipt_voucher/update_rv_item_detail';
                $this->getURL[] = 'Receipt_voucher/delete_item_direct';
                $this->getURL[] = 'Receipt_voucher/save_direct_rv_detail';
                $this->getURL[] = 'Receipt_voucher/update_direct_rv_detail';
                $this->getURL[] = 'Receipt_voucher/receipt_confirmation';
                $this->getURL[] = 'Receipt_voucher/save_rv_advance_detail';
                $this->getURL[] = 'Receipt_voucher/save_inv_base_items';
                $this->getURL[] = 'Receipt_voucher/fetch_receipt_match';
                $this->getURL[] = 'Receipt_voucher/delete_rv_match';
                $this->getURL[] = 'Receipt_voucher/re_open_receipt_match';
                $this->getURL[] = 'Receipt_voucher/load_receipt_match_header';
                $this->getURL[] = 'Receipt_voucher/fetch_match_detail';
                $this->getURL[] = 'Receipt_voucher/referback_receipt_match';
                $this->getURL[] = 'Receipt_voucher/fetch_rv_advance_detail';
                $this->getURL[] = 'Receipt_voucher/save_match_amount';
                $this->getURL[] = 'Receipt_voucher/delete_rv_match_detail';
                $this->getURL[] = 'Receipt_voucher/Receipt_match_confirmation';
                $this->getURL[] = 'Receipt_voucher/save_receipt_match_header';
                break;
            case "Procurement":
                $this->getURL[] = 'Procurement/load_project_segmentBase_multiple';
                $this->getURL[] = 'Procurement/load_project_segmentBase';
                $this->getURL[] = 'Procurement/fetch_purchase_order_approval';
                $this->getURL[] = 'Procurement/save_purchase_order_approval';
                $this->getURL[] = 'Procurement/fetch_purchase_order';
                $this->getURL[] = 'Procurement/delete_purchase_order';
                $this->getURL[] = 'Procurement/re_open_procurement';
                $this->getURL[] = 'Procurement/save_purchase_order_close';
                $this->getURL[] = 'Procurement/loademail';
                $this->getURL[] = 'Procurement/send_po_email';
                $this->getURL[] = 'Procurement/referback_procurement';
                $this->getURL[] = 'Procurement/load_purchase_order_header';
                $this->getURL[] = 'Procurement/fetch_po_detail_table';
                $this->getURL[] = 'Procurement/fetch_supplier_currency_by_id';
                $this->getURL[] = 'Procurement/save_purchase_order_header';
                $this->getURL[] = 'Procurement/fetch_purchase_order_detail';
                $this->getURL[] = 'Procurement/update_purchase_order_detail';
                $this->getURL[] = 'Procurement/fetch_last_grn_amount';
                $this->getURL[] = 'Procurement/save_purchase_order_detail';
                $this->getURL[] = 'Procurement/purchase_order_confirmation';
                $this->getURL[] = 'Procurement/fetch_prq_detail_table';
                $this->getURL[] = 'Procurement/save_prq_base_items';
                $this->getURL[] = 'Procurement/fetch_umo_data';
                $this->getURL[] = 'Procurement/fetch_convertion_detail_table';
                $this->getURL[] = 'Procurement/save_uom_conversion';
                $this->getURL[] = 'Procurement/save_uom';
                break;
            case "dashboard":
                $this->getURL[] = 'dashboard/fetch_related_uom_id';
                $this->getURL[] = 'dashboard/fetchPage';
                $this->getURL[] = 'dashboard/fetch_related_uom';
                break;
            case "CommunityNgo":
                $this->getURL[] = 'CommunityNgo/fetch_rentWHSetup';
                $this->getURL[] = 'CommunityNgo/defWHControl';
                $this->getURL[] = 'CommunityNgo/saveRentalWH_master';
                $this->getURL[] = 'CommunityNgo/fetchEdit_RentWh';
                $this->getURL[] = 'CommunityNgo/delete_rentalWH';
                $this->getURL[] = 'CommunityNgo/edit_rentalWH';
                $this->getURL[] = 'CommunityNgo/fetch_rentItemSetups';
                $this->getURL[] = 'CommunityNgo/get_rentTypeDrop';
                $this->getURL[] = 'CommunityNgo/get_rentDetails';
                $this->getURL[] = 'CommunityNgo/get_rentOtrDetails';
                $this->getURL[] = 'CommunityNgo/saveRentalItm_master';
                $this->getURL[] = 'CommunityNgo/fetchEdit_Item';
                $this->getURL[] = 'CommunityNgo/update_rentStockDel';
                $this->getURL[] = 'CommunityNgo/delete_rentalItm';
                $this->getURL[] = 'CommunityNgo/edit_rentalItm';
                $this->getURL[] = 'CommunityNgo/exportRentSet_excel';
                $this->getURL[] = 'CommunityNgo/exportRentItems_pdf';
                $this->getURL[] = 'CommunityNgo/load_familyMasterDetails';
                $this->getURL[] = 'CommunityNgo/referback_family_creation';
                $this->getURL[] = 'CommunityNgo/load_familyMasterView';
                $this->getURL[] = 'CommunityNgo/delete_family_master';
                $this->getURL[] = 'CommunityNgo/exportFamily_excel';
                $this->getURL[] = 'CommunityNgo/get_communityFamily_status__pdf';
                $this->getURL[] = 'CommunityNgo/fetch_famLog_del';
                $this->getURL[] = 'CommunityNgo/save_famLogDel';
                $this->getURL[] = 'CommunityNgo/delete_famLogDel';
                $this->getURL[] = 'CommunityNgo/fetch_familyLog_list';
                $this->getURL[] = 'CommunityNgo/get_comMaserHd';
                $this->getURL[] = 'CommunityNgo/comFamily_logConfig';
                $this->getURL[] = 'CommunityNgo/save_famLogEdit';
                $this->getURL[] = 'CommunityNgo/fetch_familyRelationships_list';
                $this->getURL[] = 'CommunityNgo/save_familyMaster';
                $this->getURL[] = 'CommunityNgo/nicDup_Check';
                $this->getURL[] = 'CommunityNgo/get_FamHgender';
                $this->getURL[] = 'CommunityNgo/get_FamHaddress';
                $this->getURL[] = 'CommunityNgo/get_FamArea';
                $this->getURL[] = 'CommunityNgo/get_FamHouseNo';
                $this->getURL[] = 'CommunityNgo/get_FamHouseAddState';
                $this->getURL[] = 'CommunityNgo/getAdded_members';
                $this->getURL[] = 'CommunityNgo/update_comFem_member_details';
                $this->getURL[] = 'CommunityNgo/familyMaster_exist';
                $this->getURL[] = 'CommunityNgo/fetch_item_detail';
                $this->getURL[] = 'CommunityNgo/get_memMoveState';
                $this->getURL[] = 'CommunityNgo/new_AncestryCat';
                $this->getURL[] = 'CommunityNgo/new_RelationCat';
                $this->getURL[] = 'CommunityNgo/save_communityMem_detail';
                $this->getURL[] = 'CommunityNgo/save_famMembers_detail';
                $this->getURL[] = 'CommunityNgo/load_ngoFamilyHeader';
                $this->getURL[] = 'CommunityNgo/delete_familyMemDetail';
                $this->getURL[] = 'CommunityNgo/load_famMembers_details_view';
                $this->getURL[] = 'CommunityNgo/familyCreate_confirmation';
                $this->getURL[] = 'CommunityNgo/referback_family_creation';
                $this->getURL[] = 'CommunityNgo/load_familyMasterView';
                $this->getURL[] = 'CommunityNgo/familyMaster_attachement_upload';
                $this->getURL[] = 'CommunityNgo/load_family_attachments';
                $this->getURL[] = 'CommunityNgo/delete_family_attachment';
                $this->getURL[] = 'CommunityNgo/update_comBeneficiary_familyDel';
                $this->getURL[] = 'CommunityNgo/delete_commitment_project';

                $this->getURL[] = 'CommunityNgo/load_comBeneficiaryManage_view';
                $this->getURL[] = 'CommunityNgo/delete_comBeneficiary_master';
                $this->getURL[] = 'CommunityNgo/fetch_comBeneficiary_province';
                $this->getURL[] = 'CommunityNgo/fetch_comBeneficiary_province_area';
                $this->getURL[] = 'CommunityNgo/fetch_comBeneficiary_division';
                $this->getURL[] = 'CommunityNgo/fetch_comBeneficiary_sub_division';
                $this->getURL[] = 'CommunityNgo/load_project_proposal_master_view';
                $this->getURL[] = 'CommunityNgo/load_comBeneficiaryManage_editView';
                $this->getURL[] = 'CommunityNgo/delete_project_proposal';
                $this->getURL[] = 'CommunityNgo/referback_project_proposal';
                $this->getURL[] = 'CommunityNgo/fetch_project_proposal_donors_email_send';
                $this->getURL[] = 'CommunityNgo/send_project_proposal_email';
                $this->getURL[] = 'CommunityNgo/save_project_proposal_header';
                $this->getURL[] = 'CommunityNgo/load_project_proposal_header';
                $this->getURL[] = 'CommunityNgo/load_zaqath_contribution';
                $this->getURL[] = 'CommunityNgo/load_beneficery_details_view';
                $this->getURL[] = 'CommunityNgo/load_project_proposal_donor_details_view';
                $this->getURL[] = 'CommunityNgo/check_project_proposal_details_exist';
                $this->getURL[] = 'CommunityNgo/project_proposal_confirmation';
                $this->getURL[] = 'CommunityNgo/fetch_ngo_sub_projects';
                $this->getURL[] = 'CommunityNgo/fetch_project_proposal_beneficiary';
                $this->getURL[] = 'CommunityNgo/fetch_project_proposal_donors';
                $this->getURL[] = 'CommunityNgo/assign_zaqath_for_project_proposal';
                $this->getURL[] = 'CommunityNgo/assign_beneficiary_for_project_proposal';
                $this->getURL[] = 'CommunityNgo/assign_donors_for_project_proposal';
                $this->getURL[] = 'CommunityNgo/load_proposal_family_del';
                $this->getURL[] = 'CommunityNgo/fetch_beneficiarySet_edit';
                $this->getURL[] = 'CommunityNgo/get_beneEdit_zakat';
                $this->getURL[] = 'CommunityNgo/update_beneficiary_edit';
                $this->getURL[] = 'CommunityNgo/delete_project_proposal_detail';
                $this->getURL[] = 'CommunityNgo/delete_project_zakat_detail';
                $this->getURL[] = 'CommunityNgo/fetch_zakatSet_edit';
                $this->getURL[] = 'CommunityNgo/update_zakatSet_edit';
                $this->getURL[] = 'CommunityNgo/active_project_zakat_detail';
                $this->getURL[] = 'CommunityNgo/delete_project_proposal_donors_detail';
                $this->getURL[] = 'CommunityNgo/load_project_image_view';
                $this->getURL[] = 'CommunityNgo/load_project_attachment_view';
                $this->getURL[] = 'CommunityNgo/fetch_province_based_countryDropdown_project_proposal';
                $this->getURL[] = 'CommunityNgo/fetch_province_based_districtDropdown_project_proposal';
                $this->getURL[] = 'CommunityNgo/fetch_division_based_districtDropdown_project_proposal';
                $this->getURL[] = 'CommunityNgo/fetch_sub_division_based_divisionDropdown_project';
                $this->getURL[] = 'CommunityNgo/fetch_project_proposal_zaqath';
                $this->getURL[] = 'CommunityNgo/save_comBeneficiary';
                $this->getURL[] = 'CommunityNgo/searchCommunityBeniFem';
                $this->getURL[] = 'CommunityNgo/load_comBeneficiaryTemplate_view';
                $this->getURL[] = 'CommunityNgo/get_FamMemCatch';
                $this->getURL[] = 'CommunityNgo/save_comBeneficiary_familyDel';
                $this->getURL[] = 'CommunityNgo/fetch_comBeneficiary_familyDel';
                $this->getURL[] = 'CommunityNgo/delete_comBeneficiary_familyDel';
                $this->getURL[] = 'CommunityNgo/load_comBeneficiary_documents_view';
                $this->getURL[] = 'CommunityNgo/comBeneficiary_confirmed';
                $this->getURL[] = 'CommunityNgo/load_comBeneficiary_header';
                $this->getURL[] = 'CommunityNgo/comBeneficiary_familyImg_upload';
                $this->getURL[] = 'CommunityNgo/comBeneficiary_systemCode_generator';
                $this->getURL[] = 'CommunityNgo/fetch_ngoSub_projectsForCom';
                $this->getURL[] = 'CommunityNgo/searchCommunityMem';
                $this->getURL[] = 'CommunityNgo/new_comBeneficiary_type';
                $this->getURL[] = 'CommunityNgo/new_comBeneficiary_province';
                $this->getURL[] = 'CommunityNgo/new_comBeneficiary_district';
                $this->getURL[] = 'CommunityNgo/new_comBeneficiary_division';
                $this->getURL[] = 'CommunityNgo/fetch_comBeneficiary_search';
                $this->getURL[] = 'CommunityNgo/upload_comBeneficiary_multipleImage';
                $this->getURL[] = 'CommunityNgo/delete_comBeneficiary_multipleImage';
                $this->getURL[] = 'CommunityNgo/update_comBeneficiary_multipleImage';
                $this->getURL[] = 'CommunityNgo/add_comBeneficiary_notes';
                $this->getURL[] = 'CommunityNgo/load_comBeneficiary_allNotes';
                $this->getURL[] = 'CommunityNgo/comMemBen_attachment_upload';
                $this->getURL[] = 'CommunityNgo/load_comMemBen_all_attachment';
                $this->getURL[] = 'CommunityNgo/delete_comMemBen_attachment';
                $this->getURL[] = 'CommunityNgo/comBeneficiary_imgUpload_helpNest';
                $this->getURL[] = 'CommunityNgo/comBeneficiary_imgUpload_helpNest_two';
                $this->getURL[] = 'CommunityNgo/comBeneficiary_imgUpload';
                $this->getURL[] = 'CommunityNgo/delete_comBen_masterNote_allDocument';
                $this->getURL[] = 'CommunityNgo/fetch_comBeneficiary_familyDel_view';
                $this->getURL[] = 'CommunityNgo/load_comBeneficiary_documents_view_forEdit';
                $this->getURL[] = 'CommunityNgo/comBeneficiary_familyImg_upload';
                $this->getURL[] = 'CommunityNgo/load_comBeneficiary_multipleImage_view';
                $this->getURL[] = 'CommunityNgo/save_comBeneficiary_doc';
                $this->getURL[] = 'CommunityNgo/delete_comBeneficiary_doc';

                $this->getURL[] = 'CommunityNgo/fetch_CommitteeMas';
                $this->getURL[] = 'CommunityNgo/saveCommitteeMas';
                $this->getURL[] = 'CommunityNgo/deleteCommitteeMas';
                $this->getURL[] = 'CommunityNgo/editCommitteeMas';
                $this->getURL[] = 'CommunityNgo/get_committees_report';
                $this->getURL[] = 'CommunityNgo/get_communityMem_famReport';
                $this->getURL[] = 'CommunityNgo/get_totalFamHousing';
                $this->getURL[] = 'CommunityNgo/get_communityMem_famReport_pdf';
                $this->getURL[] = 'CommunityNgo/get_committees_report_pdf';
                $this->getURL[] = 'CommunityNgo/fetch_SubCmnt_members';
                $this->getURL[] = 'CommunityNgo/save_SubCmntMem';
                $this->getURL[] = 'CommunityNgo/delete_SubCmntMem';
                $this->getURL[] = 'CommunityNgo/get_subCommitteeState_pdf';
                $this->getURL[] = 'CommunityNgo/fetch_CommitteePosition';
                $this->getURL[] = 'CommunityNgo/saveCommitteePosition';
                $this->getURL[] = 'CommunityNgo/deleteCommitteePosition';
                $this->getURL[] = 'CommunityNgo/editCommitteePosition';
                $this->getURL[] = 'CommunityNgo/save_cmteMembrEdit';
                $this->getURL[] = 'CommunityNgo/fetch_comiteMem_service';
                $this->getURL[] = 'CommunityNgo/save_comiteMemService';
                $this->getURL[] = 'CommunityNgo/delete_comiteMemService';
                $this->getURL[] = 'CommunityNgo/fetch_subCommittees_list';
                $this->getURL[] = 'CommunityNgo/saveCommittee_sub';
                $this->getURL[] = 'CommunityNgo/fetchEdit_subComt';
                $this->getURL[] = 'CommunityNgo/get_editComMaserHd';
                $this->getURL[] = 'CommunityNgo/edit_subComtSave';
                $this->getURL[] = 'CommunityNgo/comitt_members';
                $this->getURL[] = 'CommunityNgo/get_committeeStatus_pdf';
                $this->getURL[] = 'CommunityNgo/excel_committeeExport';

                $this->getURL[] = 'CommunityNgo/get_communityMem_status_report';
                $this->getURL[] = 'CommunityNgo/get_communityMem_qual_report';
                $this->getURL[] = 'CommunityNgo/get_communityMem_otrReport';
                $this->getURL[] = 'CommunityNgo/get_communityMem_diviReport';
                $this->getURL[] = 'CommunityNgo/get_totalHouseCom';
                $this->getURL[] = 'CommunityNgo/get_communityMem_status_report_pdf';
                $this->getURL[] = 'CommunityNgo/get_communityMem_qual_report_pdf';
                $this->getURL[] = 'CommunityNgo/get_communityMem_otrReport_pdf';
                $this->getURL[] = 'CommunityNgo/get_communityMem_diviReport_pdf';

                $this->getURL[] = 'CommunityNgo/load_ngo_area_setup';
                $this->getURL[] = 'CommunityNgo/new_sub_division';
                $this->getURL[] = 'CommunityNgo/load_ngo_area_setupDetail';
                $this->getURL[] = 'CommunityNgo/load_communityMemberDetails';
                $this->getURL[] = 'CommunityNgo/check_isNIC_available';
                $this->getURL[] = 'CommunityNgo/loadEmployees';
                $this->getURL[] = 'CommunityNgo/fetch_all_member_details';
                $this->getURL[] = 'CommunityNgo/load_memberDetailsView';
                $this->getURL[] = 'CommunityNgo/fetch_other_attachments';
                $this->getURL[] = 'CommunityNgo/upload_other_attachments';
                $this->getURL[] = 'CommunityNgo/load_member_all_attachments';
                $this->getURL[] = 'CommunityNgo/load_memberStatus_attachments';
                $this->getURL[] = 'CommunityNgo/status_attachment_upload';
                $this->getURL[] = 'CommunityNgo/delete_member_image';
                $this->getURL[] = 'CommunityNgo/delete_member_attachment';
                $this->getURL[] = 'CommunityNgo/ngo_Memberattachement_upload';
                $this->getURL[] = 'CommunityNgo/export_excel';
                $this->getURL[] = 'CommunityNgo/fetch_member_for_excel';
                $this->getURL[] = 'CommunityNgo/delete_community_members';
                $this->getURL[] = 'CommunityNgo/save_communityMember';
                $this->getURL[] = 'CommunityNgo/save_communityMemberStatus';
                $this->getURL[] = 'CommunityNgo/load_member';
                $this->getURL[] = 'CommunityNgo/get_gs_division_no';
                $this->getURL[] = 'CommunityNgo/load_memberOtherDetails_View';
                $this->getURL[] = 'CommunityNgo/saveQualification';
                $this->getURL[] = 'CommunityNgo/editQualification';
                $this->getURL[] = 'CommunityNgo/updateQualification';
                $this->getURL[] = 'CommunityNgo/deleteQualification';
                $this->getURL[] = 'CommunityNgo/fetch_medium_based_school';
                $this->getURL[] = 'CommunityNgo/fetch_address_based_school';
                $this->getURL[] = 'CommunityNgo/fetch_job_based_specialization';
                $this->getURL[] = 'CommunityNgo/saveOccupation';
                $this->getURL[] = 'CommunityNgo/editOccupation';
                $this->getURL[] = 'CommunityNgo/check_primaryOcc_exist';
                $this->getURL[] = 'CommunityNgo/updateOccupation';
                $this->getURL[] = 'CommunityNgo/deleteOccupation';
                $this->getURL[] = 'CommunityNgo/save_Language';
                $this->getURL[] = 'CommunityNgo/deleteLanguage';
                $this->getURL[] = 'CommunityNgo/save_healthStatus';
                $this->getURL[] = 'CommunityNgo/delete_healthStatus';
                $this->getURL[] = 'CommunityNgo/member_image_upload';
                $this->getURL[] = 'CommunityNgo/fetch_province_based_countryDropdown';
                $this->getURL[] = 'CommunityNgo/fetch_province_based_districtDropdown';
                $this->getURL[] = 'CommunityNgo/fetch_district_based_jammiyaDropdown';
                $this->getURL[] = 'CommunityNgo/fetch_district_based_districtDivisionDropdown';
                $this->getURL[] = 'CommunityNgo/fetch_division_based_GS_divisionDropdown';
                $this->getURL[] = 'CommunityNgo/fetch_division_based_division_Area_Dropdown';
                $this->getURL[] = 'CommunityNgo/fetch_collection_entry';
                $this->getURL[] = 'CommunityNgo/delete_collection_entry';
                $this->getURL[] = 'CommunityNgo/edit_collection_entry';
                $this->getURL[] = 'CommunityNgo/save_collection_setup';
                $this->getURL[] = 'CommunityNgo/fetch_collection_details';
                $this->getURL[] = 'CommunityNgo/delete_details';
                $this->getURL[] = 'CommunityNgo/edit_details';
                $this->getURL[] = 'CommunityNgo/save_collection_detail';
                $this->getURL[] = 'CommunityNgo/get_collection_member_details';
                $this->getURL[] = 'CommunityNgo/get_selected_members';
                $this->getURL[] = 'CommunityNgo/save_collection_members';
                $this->getURL[] = 'CommunityNgo/get_member_for_collection';
                $this->getURL[] = 'CommunityNgo/fetch_purchase_request';
                $this->getURL[] = 'CommunityNgo/fetch_member_details';
                $this->getURL[] = 'CommunityNgo/load_item_request_header';
                $this->getURL[] = 'CommunityNgo/save_item_request_header';
                $this->getURL[] = 'CommunityNgo/fetch_item_req_detail_table';
                $this->getURL[] = 'CommunityNgo/load_item_request_date';
                $this->getURL[] = 'CommunityNgo/get_date_format';
                $this->getURL[] = 'CommunityNgo/fetch_rent_item_details';
                $this->getURL[] = 'CommunityNgo/save_item_issue_details';
                $this->getURL[] = 'CommunityNgo/update_item_issue_detail';
                $this->getURL[] = 'CommunityNgo/fetch_item_issue_detail_edit';
                $this->getURL[] = 'CommunityNgo/delete_item_issue_detail';
                $this->getURL[] = 'CommunityNgo/load_returned_item_details';
                $this->getURL[] = 'CommunityNgo/rental_issue_confirmation';
                $this->getURL[] = 'CommunityNgo/return_item_confirmation';
                $this->getURL[] = 'CommunityNgo/save_customer_config';
                $this->getURL[] = 'CommunityNgo/fetch_customer_config';
                $this->getURL[] = 'CommunityNgo/edit_customer_config';
                $this->getURL[] = 'CommunityNgo/loademail';
                $this->getURL[] = 'CommunityNgo/send_request_email';

                break;
            case "OperationNgo":
                $this->getURL[] = 'OperationNgo/load_project_proposal_master_view';
                $this->getURL[] = 'OperationNgo/load_project_proposal_to_project';
                $this->getURL[] = 'OperationNgo/fetch_project_proposal_donors_email_send';
                $this->getURL[] = 'OperationNgo/send_project_proposal_email';
                $this->getURL[] = 'OperationNgo/fetch_province_based_countryDropdown_project_proposal';
                $this->getURL[] = 'OperationNgo/fetch_ngo_sub_projects';
                $this->getURL[] = 'OperationNgo/check_project_proposal_details_exist';
                $this->getURL[] = 'OperationNgo/fetch_division_based_districtDropdown_project_proposal';
                $this->getURL[] = 'OperationNgo/fetch_province_based_districtDropdown_project_proposal';
                $this->getURL[] = 'OperationNgo/fetch_division_based_districtDropdown_project_proposal';
                $this->getURL[] = 'OperationNgo/fetch_sub_division_based_divisionDropdown_project';
                $this->getURL[] = 'OperationNgo/save_project_proposal_header';
                $this->getURL[] = 'OperationNgo/save_ngo_contractor';
                $this->getURL[] = 'OperationNgo/load_beneficery_details_view';
                $this->getURL[] = 'OperationNgo/load_project_proposal_donor_details_view';
                $this->getURL[] = 'OperationNgo/load_project_image_view';
                $this->getURL[] = 'OperationNgo/check_project_proposal_details_exist';
                $this->getURL[] = 'OperationNgo/fetch_project_proposal_beneficiary';
                $this->getURL[] = 'OperationNgo/fetch_project_proposal_beneficiary';
                $this->getURL[] = 'OperationNgo/assign_beneficiary_for_project_proposal';
                $this->getURL[] = 'OperationNgo/load_converted_project_master_view';
                $this->getURL[] = 'OperationNgo/fetch_converted_proposal_details';
                $this->getURL[] = 'OperationNgo/fetch_project_proposal_details';
                $this->getURL[] = 'OperationNgo/proposal_cconvertion_to_project';
                $this->getURL[] = 'OperationNgo/fetch_converted_proposal_details';
                $this->getURL[] = 'OperationNgo/save_converted_project_details';
                $this->getURL[] = 'OperationNgo/fetch_beneficiarydetails';
                $this->getURL[] = 'OperationNgo/fetch_donordetails';
                $this->getURL[] = 'OperationNgo/load_project_header';
                $this->getURL[] = 'OperationNgo/fetch_proposal_details_view';
                $this->getURL[] = 'OperationNgo/load_proposal_details';
                $this->getURL[] = 'OperationNgo/closedproposal_reopen';
                $this->getURL[] = 'OperationNgo/project_steps';
                $this->getURL[] = 'OperationNgo/project_details';
                $this->getURL[] = 'OperationNgo/save_project_stages';
                $this->getURL[] = 'OperationNgo/delete_stages_project';
                $this->getURL[] = 'OperationNgo/fetch_project_stages';
                $this->getURL[] = 'OperationNgo/project_stage_details';
                $this->getURL[] = 'OperationNgo/project_stage_update';
                $this->getURL[] = 'OperationNgo/delete_project_stage_steps';
                $this->getURL[] = 'OperationNgo/save_project_claims';
                $this->getURL[] = 'OperationNgo/update_donors_issubmited_status_project';
                $this->getURL[] = 'OperationNgo/assign_donors_for_project_proposal';
                $this->getURL[] = 'OperationNgo/delete_project_proposal_donors_detail';
                $this->getURL[] = 'OperationNgo/assign_beneficiary_for_project_direct';
                $this->getURL[] = 'OperationNgo/delete_project_proposal_detail';
                $this->getURL[] = 'OperationNgo/load_invoice_claimed';
                break;
            case "Pos_config":
                $this->getURL[] = 'Pos_config/posConfig_menu_company';
                $this->getURL[] = 'Pos_config/loadMenuItems';
                $this->getURL[] = 'Pos_config/loadMenuItem_table';
                $this->getURL[] = 'Pos_config/updateIsPaxValue';
                $this->getURL[] = 'Pos_config/updateIsVegValue';
                $this->getURL[] = 'Pos_config/updateIsAddOnValue';
                $this->getURL[] = 'Pos_config/updateSortOrder';
                $this->getURL[] = 'Pos_config/load_pricing';
                $this->getURL[] = 'Pos_config/addMenu';
                $this->getURL[] = 'Pos_config/loadMenuDetail';
                $this->getURL[] = 'Pos_config/loadMenuDetail_table';
                $this->getURL[] = 'Pos_config/getEditMenuInfo';
                $this->getURL[] = 'Pos_config/save_menuTax';
                $this->getURL[] = 'Pos_config/save_serviceCharge';
                $this->getURL[] = 'Pos_config/delete_menuTax';
                $this->getURL[] = 'Pos_config/save_menu_details';
                $this->getURL[] = 'Pos_config/load_menu_detail_edit';
                $this->getURL[] = 'Pos_config/load_default_uom';
                $this->getURL[] = 'Pos_config/delete_pos_menu_detail';
                $this->getURL[] = 'Pos_config/deleteMenu';
                $this->getURL[] = 'Pos_config/addMenuCategory_company';
                $this->getURL[] = 'Pos_config/editCategory';
                $this->getURL[] = 'Pos_config/deleteCategory';
                $this->getURL[] = 'Pos_config/get_srp_erp_pos_segmentConfig';
                $this->getURL[] = 'Pos_config/save_posConfig';
                $this->getURL[] = 'Pos_config/setup_menu';
                $this->getURL[] = 'Pos_config/loadCrew_table';
                $this->getURL[] = 'Pos_config/loadRooms_table';
                $this->getURL[] = 'Pos_config/loadKitchenLocation_table';
                $this->getURL[] = 'Pos_config/delete_segmentConfig';
                $this->getURL[] = 'Pos_config/addMenuCategory_setup';
                $this->getURL[] = 'Pos_config/update_Menue_Category_Isactive';
                $this->getURL[] = 'Pos_config/delete_menue_Category';
                $this->getURL[] = 'Pos_config/loadwarehouse_MenuItemsSetup';
                $this->getURL[] = 'Pos_config/loadMenuItem_setup_table';
                $this->getURL[] = 'Pos_config/kot_apply_to_all';
                $this->getURL[] = 'Pos_config/update_warehouseIsTaxEnabled';
                $this->getURL[] = 'Pos_config/update_kotID';
                $this->getURL[] = 'Pos_config/changeShortcut';
                $this->getURL[] = 'Pos_config/update_Menue_Master_Isactive';
                $this->getURL[] = 'Pos_config/deleteMenu_setup';
                $this->getURL[] = 'Pos_config/fetch_menuitemfor_menucategory';
                $this->getURL[] = 'Pos_config/save_menu_item';
                $this->getURL[] = 'Pos_config/edit_pos_crew_config';
                $this->getURL[] = 'Pos_config/save_crew_info';
                $this->getURL[] = 'Pos_config/delete_pos_crew_config';
                $this->getURL[] = 'Pos_config/save_rooms_info';
                $this->getURL[] = 'Pos_config/edit_pos_room_config';
                $this->getURL[] = 'Pos_config/delete_pos_room_config';
                $this->getURL[] = 'Pos_config/loadTables_table';
                $this->getURL[] = 'Pos_config/save_tables_info';
                $this->getURL[] = 'Pos_config/edit_pos_table_config';
                $this->getURL[] = 'Pos_config/delete_pos_table_config';
                $this->getURL[] = 'Pos_config/save_kotLocation';
                $this->getURL[] = 'Pos_config/delete_pos_kotLocation';
                $this->getURL[] = 'Pos_config/warehouse_image_upload';
                $this->getURL[] = 'Pos_config/save_outlet';
                $this->getURL[] = 'Pos_config/loadCompanyOutlets';
                $this->getURL[] = 'Pos_config/saveMenuSize';
                $this->getURL[] = 'Pos_config/edit_menu_size';
                $this->getURL[] = 'Pos_config/fetch_menu_size';
                $this->getURL[] = 'Pos_config/delete_menuSize';
                $this->getURL[] = 'Pos_config/fetch_yield_master';
                $this->getURL[] = 'Pos_config/edit_yieldMaster';
                $this->getURL[] = 'Pos_config/saveYield';
                $this->getURL[] = 'Pos_config/fetch_yield_detail';
                $this->getURL[] = 'Pos_config/edit_yieldDetail';
                $this->getURL[] = 'Pos_config/saveYieldDetail';
                break;
            case "Pos":
                $this->getURL[] = 'Pos/fetch_counters';
                $this->getURL[] = 'Pos/update_counterDetails';
                $this->getURL[] = 'Pos/delete_counterDetails';
                $this->getURL[] = 'Pos/new_counter';
                $this->getURL[] = 'Pos/submit_pos_payments';
                $this->getURL[] = 'Pos/load_currencyDenominationPage';
                $this->getURL[] = 'Pos/invoice_search';
                $this->getURL[] = 'Pos/load_holdInv';
                $this->getURL[] = 'Pos/recall_hold_invoice';
                $this->getURL[] = 'Pos/savecustomer';

                break;
            case "POS_yield_preparation":
                $this->getURL[] = 'POS_yield_preparation/fetch_yield_preparation';
                $this->getURL[] = 'POS_yield_preparation/save_yieldPreparation';
                $this->getURL[] = 'POS_yield_preparation/load_yield_detail';
                $this->getURL[] = 'POS_yield_preparation/load_yieldPreparation';
                $this->getURL[] = 'POS_yield_preparation/load_yield_preparation_detail';
                break;
            case "Finance_dashboard":
                $this->getURL[] = 'Finance_dashboard/load_template';
                $this->getURL[] = 'Finance_dashboard/fetch_assigned_dashboard_widget';
                $this->getURL[] = 'Finance_dashboard/load_overall_performance';
                $this->getURL[] = 'Finance_dashboard/load_sales_log';
                $this->getURL[] = 'Finance_dashboard/load_financial_position';
                $this->getURL[] = 'Finance_dashboard/load_overdue_payable_receivable';
                $this->getURL[] = 'Finance_dashboard/load_revenue_detail_analysis_by_segment';
                $this->getURL[] = 'Finance_dashboard/load_Public_links';
                $this->getURL[] = 'Finance_dashboard/fetch_financialPosition';
                $this->getURL[] = 'Finance_dashboard/fetch_sales_log';
                $this->getURL[] = 'Finance_dashboard/fetch_overdue_payables';
                $this->getURL[] = 'Finance_dashboard/fetch_overdue_receivable';
                $this->getURL[] = 'Finance_dashboard/load_revenue_detail_analysis';
                $this->getURL[] = 'Finance_dashboard/load_performance_summary';
                $this->getURL[] = 'Finance_dashboard/load_revenue_detail_analysis_by_glcode';
                break;
            case "Chart_of_acconts":
                $this->getURL[] = 'Chart_of_acconts/fetch_cheque_number';
                break;
            case "Customer":
                $this->getURL[] = 'Customer/fetch_customer';
                $this->getURL[] = 'Customer/load_customer_header';
                $this->getURL[] = 'Customer/save_customer';
                $this->getURL[] = 'Customer/fetch_customer_category';
                $this->getURL[] = 'Customer/getCategory';
                $this->getURL[] = 'Customer/saveCategory';
                $this->getURL[] = 'Customer/delete_category';
                $this->getURL[] = 'Customer/fetch_sales_person';
                $this->getURL[] = 'Customer/laad_sale_person_header';
                $this->getURL[] = 'Customer/fetch_sales_person_details';
                $this->getURL[] = 'Customer/delete_sales_target';
                $this->getURL[] = 'Customer/delete_sales_person';
                $this->getURL[] = 'Customer/fetch_employee_detail';
                $this->getURL[] = 'Customer/fetch_customer_percentage';
                $this->getURL[] = 'Customer/save_customer_percentage';
                break;
            case "Srm_master":
                $this->getURL[] = 'Srm_master/load_customer_order_master';
                $this->getURL[] = 'Srm_master/delete_customer_order_master';
                $this->getURL[] = 'Srm_master/load_customer_order_inquiry_master';
                $this->getURL[] = 'Srm_master/delete_customer_inquiry_master';
                $this->getURL[] = 'Srm_master/load_OrderInquiry_editView';
                $this->getURL[] = 'Srm_master/load_supplier_editView';
                $this->getURL[] = 'Srm_master/save_customer';
                break;
            case "srm_master":
                $this->getURL[] = 'srm_master/load_customerOrder_header';
                $this->getURL[] = 'srm_master/load_customer_BaseDetail';
                $this->getURL[] = 'srm_master/load_customer_order_detail_item_view';
                $this->getURL[] = 'srm_master/save_customer_order_detail';
                $this->getURL[] = 'srm_master/load_order_multiple_attachemts';
                $this->getURL[] = 'srm_master/attachement_upload';
                $this->getURL[] = 'srm_master/save_customer_order_header';
                $this->getURL[] = 'srm_master/load_orderbase_generated_rfq_view';
                $this->getURL[] = 'srm_master/send_rfq_email_suppliers';
                $this->getURL[] = 'srm_master/load_customerInquiry_header';
                $this->getURL[] = 'srm_master/load_customerbase_ordersID';
                $this->getURL[] = 'srm_master/save_order_inquiry';
                $this->getURL[] = 'srm_master/load_customer_inquiry_detail_items_view';
                $this->getURL[] = 'srm_master/save_order_inquiry_itemDetail';
                $this->getURL[] = 'srm_master/load_customer_inquiry_detail_sellars_view';
                $this->getURL[] = 'srm_master/order_inquiry_generate_supplier_rfq';
                $this->getURL[] = 'srm_master/order_review_detail_view';
                $this->getURL[] = 'srm_master/fetch_supplier_all';
                $this->getURL[] = 'srm_master/supplier_image_upload';
                $this->getURL[] = 'srm_master/load_supplier_header';
                $this->getURL[] = 'srm_master/save_supplier';
                $this->getURL[] = 'srm_master/load_supplier_all_notes';
                $this->getURL[] = 'srm_master/add_supplier_notes';
                $this->getURL[] = 'srm_master/load_supplier_all_attachments';
                $this->getURL[] = 'srm_master/load_supplier_items_details';
                $this->getURL[] = 'srm_master/delete_supplier_item';
                $this->getURL[] = 'srm_master/load_supplier_itemsmaster';
                $this->getURL[] = 'srm_master/save_supplierItem';
                $this->getURL[] = 'srm_master/delete_supplier';
                $this->getURL[] = 'srm_master/fetch_customer_all';
                $this->getURL[] = 'srm_master/load_customer_header';
                $this->getURL[] = 'srm_master/delete_customer';
                break;
            case "StockCounting":
                $this->getURL[] = 'StockCounting/fetch_stock_counting_approval';
                $this->getURL[] = 'StockCounting/fetch_stock_counting_table';
                $this->getURL[] = 'StockCounting/re_open_stock_counting';
                $this->getURL[] = 'StockCounting/delete_stock_counting';
                $this->getURL[] = 'StockCounting/fetch_stock_counting_detail';
                $this->getURL[] = 'StockCounting/laad_stock_counting_header';
                $this->getURL[] = 'StockCounting/stock_counting_confirmation';
                $this->getURL[] = 'StockCounting/delete_all_detail';
                $this->getURL[] = 'StockCounting/referback_stock_counting';
                $this->getURL[] = 'StockCounting/load_subcat';
                $this->getURL[] = 'StockCounting/load_subsubcat';
                $this->getURL[] = 'StockCounting/save_stock_counting_header';
                $this->getURL[] = 'StockCounting/save_stock_counting_detail_multiple';
                $this->getURL[] = 'StockCounting/stockadjustmentAccountUpdate';
                $this->getURL[] = 'StockCounting/load_counting_item_detail';
                $this->getURL[] = 'StockCounting/save_stock_counting_detail';
                $this->getURL[] = 'StockCounting/delete_counting_item';
                $this->getURL[] = 'StockCounting/chk_delete_stock_counting_up_items';
                break;
            case "MaterialReceiptNote":
                $this->getURL[] = 'MaterialReceiptNote/fetch_material_issue_approval';
                $this->getURL[] = 'MaterialReceiptNote/save_material_receipt_approval';
                $this->getURL[] = 'MaterialReceiptNote/fetch_material_receipt';
                $this->getURL[] = 'MaterialReceiptNote/referback_materialissue';
                $this->getURL[] = 'MaterialReceiptNote/delete_material_receipt_header';
                $this->getURL[] = 'MaterialReceiptNote/re_open_material_receipt';
                $this->getURL[] = 'MaterialReceiptNote/fetch_material_receipt_detail';
                $this->getURL[] = 'MaterialReceiptNote/load_material_receipt_header';
                $this->getURL[] = 'MaterialReceiptNote/save_material_receipt_header';
                $this->getURL[] = 'MaterialReceiptNote/fetch_warehouse_item';
                $this->getURL[] = 'MaterialReceiptNote/save_material_detail_multiple';
                $this->getURL[] = 'MaterialReceiptNote/load_material_receipt_detail';
                $this->getURL[] = 'MaterialReceiptNote/save_material_detail';
                $this->getURL[] = 'MaterialReceiptNote/delete_material_item';
                $this->getURL[] = 'MaterialReceiptNote/material_item_confirmation';
                $this->getURL[] = 'MaterialReceiptNote/fetch_material_issue_code';
                $this->getURL[] = 'MaterialReceiptNote/fetch_material_issue_detail_table';
                $this->getURL[] = 'MaterialReceiptNote/save_material_issue_note_base_items';
                break;
            case "Address":
                $this->getURL[] = 'Address/load_address';
                $this->getURL[] = 'Address/edit_address';
                $this->getURL[] = 'Address/save_address';
                $this->getURL[] = 'Address/delete_address';
                break;
            case "Report":
                $this->getURL[] = 'Report/get_procurement_filter';
                $this->getURL[] = 'Report/get_report_by_id';
                $this->getURL[] = 'Report/get_item_filter';
                $this->getURL[] = 'Report/load_subcat';
                $this->getURL[] = 'Report/loadItems';
                $this->getURL[] = 'Report/load_subsubcat';
                $this->getURL[] = 'Report/get_accounts_payable_filter';
                $this->getURL[] = 'Report/get_accounts_receivable_filter';
                $this->getURL[] = 'Report/get_collection_summery_report';
                $this->getURL[] = 'Report/get_collection_detail_report';
                break;
            case "PurchaseRequest":
                $this->getURL[] = 'PurchaseRequest/fetch_purchase_request_approval';
                $this->getURL[] = 'PurchaseRequest/save_purchase_request_approval';
                $this->getURL[] = 'PurchaseRequest/fetch_purchase_request';
                $this->getURL[] = 'PurchaseRequest/referback_purchaserequest';
                $this->getURL[] = 'PurchaseRequest/delete_purchase_request';
                $this->getURL[] = 'PurchaseRequest/re_open_procurement';
                $this->getURL[] = 'PurchaseRequest/load_purchase_request_header';
                $this->getURL[] = 'PurchaseRequest/fetch_pqr_detail_table';
                $this->getURL[] = 'PurchaseRequest/save_purchase_request_header';
                $this->getURL[] = 'PurchaseRequest/fetch_purchase_request_detail';
                $this->getURL[] = 'PurchaseRequest/update_purchase_request_detail';
                $this->getURL[] = 'PurchaseRequest/delete_purchase_request_detail';
                $this->getURL[] = 'PurchaseRequest/fetch_last_grn_amount';
                $this->getURL[] = 'PurchaseRequest/save_purchase_request_detail';
                $this->getURL[] = 'PurchaseRequest/purchase_request_confirmation';
                break;
            case "Pos_cameraSetup":
                $this->getURL[] = 'Pos_cameraSetup/LoadCameraSetup';
                $this->getURL[] = 'Pos_cameraSetup/save_camera_setup';
                $this->getURL[] = 'Pos_cameraSetup/edit_camera_setup';
                $this->getURL[] = 'Pos_cameraSetup/delete_camera_setup';
                break;
            case "Sub_category":
                $this->getURL[] = 'Sub_category/load_subcategoryMaster';
                $this->getURL[] = 'Sub_category/save_subcategory';
                $this->getURL[] = 'Sub_category/edit_itemsubsubcategory';
                $this->getURL[] = 'Sub_category/update_subsubcategory';
                $this->getURL[] = 'Sub_category/save_subsubcategory';
                break;
            case "ItemCategory":
                $this->getURL[] = 'ItemCategory/load_category';
                break;
            case "AttributeAssign":
                $this->getURL[] = 'AttributeAssign/get_attributes';
                $this->getURL[] = 'AttributeAssign/save_assigned_attributes';
                $this->getURL[] = 'AttributeAssign/fetch_attribute_assign';
                $this->getURL[] = 'AttributeAssign/get_attributes_edit';
                $this->getURL[] = 'AttributeAssign/update_assigned_attributes';
                $this->getURL[] = 'AttributeAssign/delete_attribute';
                break;
            case "itemmaster":
                $this->getURL[] = 'itemmaster/fetch_item';
                break;
            case "srp_warehouseMaster":
                $this->getURL[] = 'srp_warehouseMaster/load_warehousemastertable';
                $this->getURL[] = 'srp_warehouseMaster/load_bin_location_table';
                $this->getURL[] = 'srp_warehouseMaster/save_bin_location';
                $this->getURL[] = 'srp_warehouseMaster/setDefaultWarehouse';
                $this->getURL[] = 'srp_warehouseMaster/save_warehousemaster';
                $this->getURL[] = 'srp_warehouseMaster/edit_warehouse';
                break;
            case "Dashboard":
                $this->getURL[] = 'Dashboard/fetch_finance_year_period';
                $this->getURL[] = 'Dashboard/fetch_notifications';
                break;
            case "PaymentReversal":
                $this->getURL[] = 'PaymentReversal/fetch_reversed_payment';
                $this->getURL[] = 'PaymentReversal/fetch_payment_reversal';
                $this->getURL[] = 'PaymentReversal/reverse_paymentVoucher';
                break;
            case "supplier":
                $this->getURL[] = 'supplier/fetch_supplier';
                $this->getURL[] = 'supplier/fetch_supplierbank';
                $this->getURL[] = 'supplier/fetch_supplierbank';
                $this->getURL[] = 'supplier/delete_supplierbank';
                $this->getURL[] = 'supplier/fetch_supplier_category';
                break;
            case "Supplier":
                $this->getURL[] = 'Supplier/load_supplier_header';
                $this->getURL[] = 'Supplier/save_suppliermaster';
                $this->getURL[] = 'Supplier/save_supplierbank';
                $this->getURL[] = 'Supplier/edit_Bank_Details';
                $this->getURL[] = 'supplier/getCategory';
                $this->getURL[] = 'supplier/saveCategory';
                $this->getURL[] = 'supplier/delete_category';
                break;
            case "ReceiptReversal":
                $this->getURL[] = 'ReceiptReversal/fetch_reversed_payment';
                $this->getURL[] = 'ReceiptReversal/fetch_receipt_reversal';
                $this->getURL[] = 'ReceiptReversal/reverse_receiptVoucher';
                break;
            case "Journal_entry":
                $this->getURL[] = 'Journal_entry/fetch_journal_entry_approval';
                $this->getURL[] = 'Journal_entry/fetch_attachmentsJV';
                $this->getURL[] = 'Journal_entry/save_jv_approval';
                $this->getURL[] = 'Journal_entry/fetch_journal_entry';
                $this->getURL[] = 'Journal_entry/referback_journal_entry';
                $this->getURL[] = 'Journal_entry/delete_Journal_entry';
                $this->getURL[] = 'Journal_entry/delete_Journal_entry_detail';
                $this->getURL[] = 'Journal_entry/fetch_journal_entry_detail';
                $this->getURL[] = 'Journal_entry/re_open_journal_entry';
                $this->getURL[] = 'Journal_entry/save_gl_detail';
                $this->getURL[] = 'Journal_entry/load_jv_detail';
                $this->getURL[] = 'Journal_entry/update_gl_detail';
                $this->getURL[] = 'Journal_entry/save_journal_entry_header';
                $this->getURL[] = 'Journal_entry/journal_entry_confirmation';
                $this->getURL[] = 'Journal_entry/getrecurringDataTable';
                $this->getURL[] = 'Journal_entry/get_recurringjv_details';
                $this->getURL[] = 'Journal_entry/add_recarring_details';
                break;
            case "Recurring_je":
                $this->getURL[] = 'Recurring_je/fetch_recurring_journal_entry_approval';
                $this->getURL[] = 'Recurring_je/save_rjv_approval';
                break;
            case "Budget":
                $this->getURL[] = 'Budget/fetch_budget_entry';
                $this->getURL[] = 'Budget/get_budget_detail_data';
                $this->getURL[] = 'Budget/get_budget_footer_total';
                $this->getURL[] = 'Budget/update_budget_row';
                $this->getURL[] = 'Budget/update_apply_all_row';
                $this->getURL[] = 'Budget/budget_confirmation';
                $this->getURL[] = 'Budget/save_budget_header';
                break;
            case "Pos_general_master":
                $this->getURL[] = 'Pos_general_master/edit_warehouse';
                $this->getURL[] = 'Pos_general_master/loadCompanyOutlets';
                break;
            default:
                $this->getURL = array();
        }



        //continue from Recurring JV


















        checkPostURL($this->getURL);

        /*$encryption_key = 'CKXH2U9RPY3EFD70TLS1ZG4N8WQBOVI6AMJ5';
        $CI->load->library("Cryptor",$encryption_key);*/
        $CI->encryption->initialize(array('driver' => 'mcrypt'));
        $this->common_data['status'] = FALSE;
        if (!$CI->session->has_userdata('status')) {
            echo '<script type="text/javascript">',
            ' check_session_status();',
            '</script>';
        } else {


            $CI->db->select('*');
            $CI->db->where("UserName", trim($CI->session->userdata("loginusername")));
            $CI->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
            $resultDb2 = $CI->db->get("user")->row_array();
            $config['hostname'] = trim($CI->encryption->decrypt($resultDb2["host"]));
            $config['username'] = trim($CI->encryption->decrypt($resultDb2["db_username"]));
            $config['password'] = trim($CI->encryption->decrypt($resultDb2["db_password"]));
            $config['database'] = trim($CI->encryption->decrypt($resultDb2["db_name"]));
            $config['dbdriver'] = 'mysqli';
            $config['db_debug'] = TRUE;
            $config['db_debug'] = True;
            $config['char_set'] = 'utf8';
            $config['dbcollat'] = 'utf8_general_ci';
            $config['cachedir'] = '';
            $config['swap_pre'] = '';
            $config['encrypt'] = FALSE;
            $config['compress'] = FALSE;
            $config['stricton'] = FALSE;
            $config['failover'] = array();
            $config['save_queries'] = TRUE;

            $CI->load->database($config, FALSE, TRUE);

            $company_id = trim($CI->session->userdata("companyID"));
            $company_type = trim($CI->session->userdata("companyType"));
            $company_detail = "";
            $company_policy = "";
            if ($company_type == 1) {
                $company_detail = $CI->Session_model->fetch_company_detail($company_id, trim($CI->session->userdata("branchID")));
                $company_policy = $CI->Session_model->fetch_company_policy($company_id);
            } else {
                $company_policy = $CI->Session_model->fetch_group_policy($company_id);
                $company_detail = $CI->Session_model->fetch_group_detail($company_id, trim($CI->session->userdata("branchID")));
            }

            /**
             * Timezone added by shafri
             *
             * Setup Time Zone
             */
            $timezoneID = "";
            $timezone = "";
            if ($company_type == 1) {
                $CI->db->select('srp_erp_company.defaultTimezoneID, srp_erp_timezonedetail.description');
                $CI->db->from('srp_erp_company');
                $CI->db->join('srp_erp_timezonedetail', 'srp_erp_timezonedetail.detailID = srp_erp_company.defaultTimezoneID', 'INNER');
                $CI->db->where('company_id', $company_id);
                $result = $CI->db->get()->row_array();
                if (!empty($result)) {
                    $timezoneID = $result['defaultTimezoneID'];
                    $timezone = $result['description'];
                    date_default_timezone_set(trim($timezone));
                } else {
                    /** DEFAULT */
                    date_default_timezone_set('Asia/Colombo');
                    $timezone = 'Asia/Colombo';
                    $timezoneID = null;
                }
            } else {
                date_default_timezone_set('Asia/Colombo');
                $timezone = 'Asia/Colombo';
                $timezoneID = null;
            }

            /** End of Timezone */
            $this->common_data['company_data'] = $company_detail;
            $this->common_data['company_policy'] = $company_policy;
            if ($this->common_data['company_data']['company_id'] != 0 && $company_type == 1) {
                $controlaccounts = $CI->Session_model->fetch_companycontrolaccounts($this->common_data['company_data']['company_id'], $this->common_data['company_data']['company_code']);
                $this->common_data['controlaccounts'] = $controlaccounts;
            }
            $this->common_data['ware_houseID'] = trim($CI->session->userdata("ware_houseID"));
            $this->common_data['imagePath'] = trim($CI->session->userdata("imagePath"));
            $this->common_data['current_pc'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $this->common_data['current_user'] = trim($CI->session->userdata("username"));
            $this->common_data['current_userID'] = trim($CI->session->userdata("empID"));
            $this->common_data['current_userCode'] = trim($CI->session->userdata("empCode"));
            $this->common_data['user_group'] = trim($CI->session->userdata("usergroupID"));
            $this->common_data['current_date'] = date('Y-m-d h:i:s');
            $this->common_data['status'] = TRUE;
            $this->common_data['timezoneID'] = $timezoneID;
            $this->common_data['timezoneDescription'] = $timezone;
            $this->common_data['emplangid'] = trim($CI->session->userdata("emplangid"));
        }
        //$this->db2 = $CI->load->database('db2', TRUE);
    }
}