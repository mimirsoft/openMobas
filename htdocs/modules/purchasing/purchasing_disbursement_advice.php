<?
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("../../../framework/framework_masterinclude.php");
Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication

require_once("purchasing_include.php");
require_once("Cash_Disbursement.php");
require_once("../invoices/Cash_Receipt.php");
require_once("Purchase_Order.php");

$ACTION =      '';
$WARNING['show'] = false;
$results = Array();
$recorddate_year = date('Y');
$recorddate_month = date('m');
$recorddate_day = "01";

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$cd_no = $_POST['cd_no'];
    @$po_id = $_POST['po_id'];
    @$remit_no = $_POST['remit_no'];
    @$apply_amount = $_POST['apply_amount'];
}


switch($ACTION)
{
    //if this page is posted to, usually from the cash_receipts page, with a check amount to be recorded along with a cv_id
    case "Record Check":
        $recorddate_year = @$_POST['recorddate_year'];
        $recorddate_month = @$_POST['recorddate_month'];
        $recorddate_day = @$_POST['recorddate_day'];
        $date = $recorddate_year."-".$recorddate_month."-".$recorddate_day;
        //$remit_no = Cash_Receipts::create_remittance($check_amount, $date, $cv_id, $remittance_deposit, $comment, $check_no);
    break;
    case "Apply Amount":
            Cash_Disbursements::apply_disbursement($cd_no, $po_id, $apply_amount);
    break;
    case "Apply Refund Amount":
            Cash_Disbursements::apply_refund($cd_no, $remit_no, $apply_amount);
    break;
}

$disbursement = Cash_Disbursements::get_disbursement($cd_no);
if($disbursement['is_refund'] == 1)
{
    $remittances = Cash_Receipts::getAllUnappliedRemittancesOfCustomer($disbursement['vendor_id']);
    
}
else
{
    $purchases = Purchase_Order::getall_unpaidpurchaseorders_of_vendor($disbursement['vendor_id']);
}
//if this is a refund, find unapplied cash receipts to apply against

include("purchasing_disbursement_advice.phtml");

?>
