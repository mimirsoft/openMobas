<?
require_once("../../../framework/framework_masterinclude.php");
Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication

require_once("../cv_master/CV_Main.class.php");

require_once("purchasing_include.php");
require_once("Purchase_Requisition.php");
require_once("Cash_Disbursement.php");
require_once("Purchasing_System.php");

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

//Get all clients who have unpaid purchase orders

$remittance_accounts=Purchasing_System::getall_disbursement_accounts();

$unpaid_purchase = Purchase_Order::getall_unpaidpurchaseorders_by_vendor();

include("purchasing_batch_electronic_disbursements.phtml");

?>
