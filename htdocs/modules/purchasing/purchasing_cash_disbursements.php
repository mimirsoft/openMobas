<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("purchasing_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);



$ACTION =      '';
$WARNING['show'] = false;
$results = Array();
$recorddate_year = date('Y');
$recorddate_month = date('m');
$recorddate_day = date('d');

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$search_string = $_POST['search_string'];
    @$cd_no = $_POST['cd_no'];
    @$remittance_deposit = $_POST['remittance_deposit'];
}


switch($ACTION)
{
    case "Search":
        $results = CV_Main::search_customers_by_name($search_string);
    break;
    case "Verify Disbursement Advice":
        Cash_Disbursements::verify_remittance_advice();
        Purchase_Order::verify_remittance_advice();
    break;
    case "Update GL":
        Cash_Disbursements::update_disbursement_account($cd_no, $remittance_deposit);
        $cd_info = Cash_Disbursements::get_disbursement($cd_no);
        $customer_info = CV_Main::get_cv_from_id($cd_info['vendor_id']);
        Cash_Disbursements::update_gl_entry($cd_no, $customer_info['gl_account_payable']);
    break;
    case "Delete Disbursement":
        Cash_Disbursements::delete_disbursement($cd_no);
    break;
}

$remittance_accounts=Purchasing_System::getall_disbursement_accounts();

$unpaid_purchase = Purchase_Order::getall_unpaidpurchaseorders();

$disbursements = Cash_Disbursements::getall_unapplied_disbursements();
$disbursements_no_ledger = Cash_Disbursements::getall_disbursements_without_ledger_entry();

include("purchasing_cash_disbursements.phtml");

?>
