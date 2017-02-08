<?php
require_once("../../../framework/framework_masterinclude.php");

require_once("invoices_include.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$ACTION =      '';
$WARNING['show'] = false;
$results = Array();
$recorddate_year = date('Y');
$recorddate_month = date('m');
$recorddate_day = date('d');
$remittance_advice = '';

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$search_string = $_POST['search_string'];
    @$remit_no = $_POST['remit_no'];
    @$remittance_deposit = $_POST['remittance_deposit'];
}


switch($ACTION)
{
    case "Search":
        $results = CV_Main::search_customers_by_name($search_string);
        foreach($results as $row)
        {
        	$remittance_advice[$row['cv_id']] = Cash_Receipts::getall_remittances_of_customer($row['cv_id']);
        }
        break;
    case "Verify Remittance Advice":
        Cash_Receipts::verify_remittance_advice();
        Invoice::verify_remittance_advice();
    break;
    case "Update GL":
        Cash_Receipts::update_remittance_account($remit_no, $remittance_deposit);
        $remit_info = Cash_Receipts::get_remittance($remit_no);
        $customer_info = CV_Main::get_cv_from_id($remit_info['customer_id']);
        Cash_Receipts::update_gl_entry($remit_no, $customer_info['gl_account_receivable']);
    break;
    case "Delete Remittance":
        Cash_Receipts::delete_remittance($remit_no);
    break;
    case "Unapply Remittance":
        Cash_Receipts::unapply_remittance($remit_no);
    break;
}

$remittances = Cash_Receipts::getall_unapplied_remittances();
$remittances_no_ledger = Cash_Receipts::getall_remittances_without_ledger_entry();
$remittance_accounts=Invoice_System::getall_remittance_accounts();

include("invoices_cash_receipts.phtml");

?>
